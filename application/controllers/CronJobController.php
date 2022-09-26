<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/vendor/autoload.php';

require_once APPPATH . "libraries/PHPMailer/Exception.php";
require_once APPPATH . "libraries/PHPMailer/PHPMailer.php";
require_once APPPATH . "libraries/PHPMailer/SMTP.php";

use Dompdf\Dompdf;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class CronJobController extends CI_Controller
{

	public function __construct()
	{

		parent::__construct();

		$this->load->model("Reports");
		$this->load->model("Users");

		$this->load->library('mailer');
	}

	public function test()
	{
		writeLog('Test Log');

		$this->mailer->sendTestMail();
		die();
		$mail = new PHPMailer();

		$mail->IsSMTP();
		$mail->Host = 'mail.clinical.careequity.com';
		$mail->Port = 465;
		$mail->SMTPAuth = true;
		$mail->Username = 'clinicalrss@clinical.careequity.com';
		$mail->Password = 'M?r;=[_Wq631';
		$mail->SMTPSecure = 'ssl';
		$mail->SMTPDebug  = 1;
		$mail->SMTPAuth   = TRUE;

		$mail->From = 'clinicalrss@clinical.careequity.com';
		$mail->FromName = 'Clinic';

		$mail->Subject = "Message from contact form";
		$mail->Body    = "This is test email";
		$mail->AddAddress('jaygangkun@hotmail.com');

		$mail->addAttachment('searchresults/Test Attachment.pdf');

		if (!$mail->Send()) {
			echo $mail->ErrorInfo;
		}

		echo "success";
	}

	public function run()
	{
		set_time_limit(0);
		writeLog('Cron Job Start >>>>>>>>>>');

		// get active reports
		$reports = $this->Reports->allActiveReports();

		foreach ($reports as $report) {
			writeLog('---Start the report:' . $report['id']);
			$days = 7;
			if ($report['status'] == 'new') {
				$days = 7;
			} else if ($report['status'] == 'recent') {
				$days = 31;
			} else if ($report['status'] == 'old') {
				$days = 31 * 3;
			}

			$rss_url = getRssLink(array(
				'days' => $days,
				'terms' => $report['terms'],
				'study' => $report['study'],
				'conditions' => $report['conditions'],
				'country' => $report['country'],
				'count' => 30
			));
			// echo $rss_url; die();

			$curl = curl_init();

			curl_setopt_array($curl, array(
				CURLOPT_URL => $rss_url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'GET',
				CURLOPT_HTTPHEADER => array(
					'Cookie: CTOpts=Qihzm6CLC74Psi1HjyUgzw-R98Fz3R4gQC-w; Psid=vihzm6CLC74Psi1Hjyz3FQ7V9gCkkKC8-BC8Eg0jF64VSgzqSB78SB0gCD8V'
				),
			));

			$response = curl_exec($curl);

			curl_close($curl);

			$xml = new SimpleXMLElement($response);

			$data = array(
				'clinics' => array(),
				'title' => $xml->channel->title
			);

			$pubDate = $xml->channel->pubDate;
			$guids = array();



			$changed = false;
			$db_guids = json_decode($report['guids'], true);


			foreach ($xml->channel->item as $item) {

				$pos = strpos($report['guids'], $item->guid->__toString());

				if ($pos === false) {

					$details = getStudyDetails($item->link);
					$details['link'] = $item->link;
					$details['title'] = $item->title;
					$details['description'] = $item->description;

					$data['clinics'][] = $details;
				}

				$guids[] = $item->guid->__toString();
			}

			// check changes between emails


			$found_count = 0;
			foreach ($guids as $guid) {
				if (strpos($report['guids'], $guid) !== false) {
					$found_count++;
				}
			}

			//writeLog('---found count ' . $found_count);

			if ($found_count != count($db_guids) || $found_count != count($guids)) {
				$changed = true;
			}


			if ($changed) {
				$dompdf = new Dompdf();

				$clinic_html = $this->load->view('admin/template/clinic-table', $data, TRUE);
				// echo $clinic_html;die();
				$dompdf->loadHtml($clinic_html);

				// (Optional) Setup the paper size and orientation
				$dompdf->setPaper('A3', 'landscape');

				// Render the HTML as PDF
				$dompdf->render();

				// Output the generated PDF to Browser

				// $dompdf->stream("SearchResults.pdf");
				$output = $dompdf->output();
				$filepath = 'searchresults/Search Results_' . $report['id'] . "_" . time() . '.pdf';
				file_put_contents($filepath, $output);



				$on_reporters = $report['on_reporters'];

				if ($on_reporters == null || $on_reporters == '') {
				} else {

					$user_ids = explode(",", $on_reporters);

					//foreach loop to display the returned array
					foreach ($user_ids as $i) {
						$users = $this->Users->getByID($i);

						if (count($users) > 0) {
							$user = $users[0];

							// sending email
							$mail = new PHPMailer();

							$mail->IsSMTP();
							$mail->Host = 'mail.clinical.careequity.com';
							$mail->Port = 465;
							$mail->SMTPAuth = true;
							$mail->Username = 'clinicalrss@clinical.careequity.com';
							$mail->Password = 'M?r;=[_Wq631';
							$mail->SMTPSecure = 'ssl';
							$mail->SMTPDebug  = 1;
							$mail->SMTPAuth   = TRUE;

							$mail->From = 'careequityclinicaltool@clinical.careequity.com';
							$mail->FromName = 'Care Equity Clinical Tool';

							$mail->Subject = "Message from Care Equity Clinical Tool";
							$mail->Body    = $xml->channel->title;

							$mail->AddAddress($user['email']);

							$mail->addAttachment($filepath);

							if (!$mail->Send()) {
								//writeLog($mail->ErrorInfo);
							}

							//writeLog('---Sent Email to ' . $user['email']);
						}
					}


					/*
					$reports = $this->Reports->updateGuids($report['id'], array(
						'pubDate' => $pubDate,
						'guids' => json_encode($guids)
					));
					*/
				}
			} else {
				//writeLog('---No Changes');
			}



			//writeLog('---Complete the report:' . $report['id']);
		}

		//writeLog('Cron Job End <<<<<<<<<<');
		//echo "Successfully!";
	}

	public function check()
	{
		set_time_limit(0);
		writeLog('Cron Job Check Start >>>>>>>>>>');

		// get active reports
		$reports = $this->Reports->allActiveReports();

		foreach ($reports as $report) {
			//writeLog('---Start Check:' . $report['id']);


			$found_count = 0;

			//Getting week_list and week_reports
			$current_week_list = $report['week_list'];
			$current_week_reports = $report['week_reports'];

			if ($report['reporting'] == '1') {
				$terms = $report['terms'];
				$study = $report['study'];
				$conditions = $report['conditions'];
				$country = $report['country'];

				$rss_link = getRssLink(array(
					'days' => 7,
					'terms' => $terms,
					'study' => $study,
					'conditions' => $conditions,
					'country' => $country,
					'count' => 30
				));

				$guids = getClinicalGuids($rss_link);
				$cur_updated_at =  date("Ymd");
				//$found_count = getRssCount($rss_link);

				foreach ($guids as $guid) {
					if (strpos($report['guids'], $guid) === false) {
						$found_count++;
					}
				}

				if (getRssCount($rss_link) == 0) {
					$rss_link = getRssLink(array(
						'days' => 31,
						'terms' => $terms,
						'study' => $study,
						'conditions' => $conditions,
						'country' => $country,
						'count' => 30
					));
					if (getRssCount($rss_link) == 0) {
						$rss_link = getRssLink(array(
							'days' => 31 * 3,
							'terms' => $terms,
							'study' => $study,
							'conditions' => $conditions,
							'country' => $country,
							'count' => 30
						));

						if (getRssCount($rss_link) == 0) {
							$status = 'no';
						} else {
							$status = 'old';
							$reports = $this->Reports->updateOnlyGuids(isset($_POST['id']) ? $_POST['id'] : '', array(
								'updated_at' => $cur_updated_at,
								'guids' => json_encode($guids)
							));
						}
					} else {
						$status = 'recent';
						$reports = $this->Reports->updateOnlyGuids(isset($_POST['id']) ? $_POST['id'] : '', array(
							'updated_at' => $cur_updated_at,
							'guids' => json_encode($guids)
						));
					}
				} else {
					$status = 'new';
					$reports = $this->Reports->updateOnlyGuids(isset($_POST['id']) ? $_POST['id'] : '', array(
						'updated_at' => $cur_updated_at,
						'guids' => json_encode($guids)
					));
				}


				if ($current_week_list == "" && $current_week_reports == "") {
					$week_update = $this->Reports->updateWeek(array(
						'id' => $report['id'],
						'week_list' => '1',
						'week_reports' => $found_count
					));
				} else {

					$org_list = explode(",", $current_week_list);




					$new_week_list = "";
					$new_week_reports = "";

					$last_week = end($org_list) + 1;
					$new_week_list = $current_week_list . "," . $last_week;

					$new_week_reports = $current_week_reports . "," . $found_count;



					$week_update = $this->Reports->updateWeek(array(
						'id' => $report['id'],
						'week_list' => $new_week_list,
						'week_reports' => $new_week_reports
					));
				}
			} else {
				$status = 'no';

				$week_update = $this->Reports->updateWeek(array(
					'id' => $report['id'],
					'week_list' => '',
					'week_reports' => ''
				));
			}

			$report_update = $this->Reports->updateField(array(
				'id' => $report['id'],
				'status' => $status
			));

			if ($report_update) {
				//writeLog('---Update Status:' . $status);
			}
			//writeLog('---Complete Check:' . $report['id']);
		}

		//writeLog('Cron Job Check End <<<<<<<<<<');
		//echo "Successfully!";
	}
}
