<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'libraries/vendor/autoload.php';

use Dompdf\Dompdf;

class RSSController extends CI_Controller
{

	public function __construct()
	{

		parent::__construct();

		$this->load->model("Reports");
	}


	public function rssTest()
	{

		// $details = getStudyDetails('https://clinicaltrials.gov/ct2/show/NCT00936936?term=d&amp;cond=Testis+Cancer&amp;cntry=US');

		// $details = getStudyDetails('https://clinicaltrials.gov/ct2/show/NCT02660229?cond=Cancer+Pain&draw=2&rank=3');
		$details = getStudyDetails('https://clinicaltrials.gov/ct2/show/NCT04175639?cond=Cancer+Pain&draw=2&rank=89');

		$details['link'] = 'https://clinicaltrials.gov/show/NCT04844645';
		$details['title'] = 'Using Mini Program for Selfmanagement VS Conventional Pharmaceutical Care for Cancer Pain';
		// echo $details['phase'];
		// print_r($details);die();

		// instantiate and use the dompdf class
		$dompdf = new Dompdf();
		$data = array(
			'clinics' => array(),
			'title' => "ClinicalTrials.gov: d | Testis Cancer | United States | Last update posted in the last 7 days"
		);
		$data['clinics'][] = $details;

		$clinic_html = $this->load->view('admin/template/clinic-table', $data, TRUE);
		echo $clinic_html;
		die();
		$dompdf->loadHtml($clinic_html);

		// (Optional) Setup the paper size and orientation
		$dompdf->setPaper('A3', 'landscape');

		// Render the HTML as PDF
		$dompdf->render();

		// Output the generated PDF to Browser
		$dompdf->stream("test.pdf");

		die();

		$rss_url = 'https://clinicaltrials.gov/ct2/results/rss.xml?rcv_d=&lup_d=7&sel_rss=mod7&term=d&cond=Testis+Cancer&cntry=US&count=10000';
		$rss_url = 'https://clinicaltrials.gov/ct2/results/rss.xml?rcv_d=&lup_d=1&sel_rss=mod1&term=d&cond=Testis+Cancer&cntry=US&count=10000';
		if (isset($_GET['url'])) {
			$rss_url = $_GET['url'];
		}

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

		echo count($xml->channel->item);
		die();
		foreach ($xml->channel->item as $item) {
			echo $item->title . "<br><br>";
		}
	}

	public function rssDownload()
	{
		set_time_limit(0);

		if (!isset($_GET['report_id'])) {
			echo ("Not find report id");
			die();
		}

		$reports = $this->Reports->getByID($_GET['report_id']);
		if (count($reports) == 0) {
			echo ("Not find report");
			die();
		}

		$report = $reports[0];

		// create rss url
		$rss_url = "https://clinicaltrials.gov/ct2/results/rss.xml?rcv_d=&lup_d=7&sel_rss=mod7&term=" . str_replace(" ", "+", $report['terms']) . "&type=" . $report['study'] . "&cond=" . str_replace(" ", "+", $report['conditions']) . "&cntry=" . $report['country'] . "&count=10";

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

		foreach ($xml->channel->item as $item) {
			$details = getStudyDetails($item->link);
			$details['link'] = $item->link;
			$details['title'] = $item->title;
			$details['description'] = $item->description;

			$data['clinics'][] = $details;
		}

		$dompdf = new Dompdf();

		$clinic_html = $this->load->view('admin/template/clinic-table', $data, TRUE);
		// echo $clinic_html;die();
		$dompdf->loadHtml($clinic_html);

		// (Optional) Setup the paper size and orientation
		$dompdf->setPaper('A3', 'landscape');

		// Render the HTML as PDF
		$dompdf->render();

		// Output the generated PDF to Browser
		$dompdf->stream("SearchResults.pdf");

		echo "PDF Downloading...";
		die();
	}


	public function downloadListCsv()
	{


		$response = array(
			'success' => false
		);


		$reports = $this->Reports->getByID($_GET['report_id']);

		if ($reports) {
			$response['success'] = true;

			if (count($reports)) {
				$report = $reports[0];
			} else {
				$report = null;
			}

			$week_list = $report['week_list'];
			$week_reports = $report['week_reports'];

			$filename = $_SESSION['username'] . '_' . $report['title'] . '_' . date('m-d-Y') . '.csv';

			header("Content-Description: File Transfer");
			header('Content-Type: text/csv');
			header("Content-Disposition: attachment; filename=$filename");

			$data = array();

			$week_list_arr = explode(",", $week_list);
			$week_report_arr = explode(",", $week_reports);

			$show_date = date('m-d-Y');

			$week_report_arr = array_reverse($week_report_arr);

			$week_val = "";

			$every_week = 0;
			$every_week_val = 0;
			$week_cumul_val = 0;

			$every_week = intval(count($week_list_arr) / 7);


			for ($i = 0; $i < count($week_list_arr); $i++) {

				if ($i == 0) {
					$week_list_arr[$i] = "this week";
				} else if ($i == 1) {
					$week_list_arr[$i] = "previous week";
				} else {
					$week_list_arr[$i] = $week_list_arr[$i] . " weeks ago";
				}

				//$show_date = date('m-d-Y', strtotime("-$i week"));
				//$data[] = array($week_list_arr[$i], $show_date, $week_report_arr[$i]);


				$week_cumul_val += intval($week_report_arr[$i]);
				$every_week_val += 1;

				if ($every_week_val == 8 || $every_week == 1) {

					$week_val = $i / 8;
					$show_date = date('m-d-Y', strtotime("-$i day"));
					$data[] = array($week_list_arr[$week_val], $show_date, $week_cumul_val);

					$every_week -= 1;
					$every_week_val = 1;
					$week_cumul_val = 0;
				}
			}



			$fp = fopen('php://output', 'w');

			$header = array("WEEK", "DATE", "COUNT");
			fputcsv($fp, $header);

			foreach ($data as $key => $line) {
				//$val = explode(",", $line);
				fputcsv($fp, $line);
			}
			fclose($fp);

			exit;
		}

		//exit;
	}



	public function rssPopup()
	{
		set_time_limit(0);

		if (!isset($_POST['report_id'])) {
			echo ("Not find report id");
			die();
		}

		$reports = $this->Reports->getByID($_POST['report_id']);
		if (count($reports) == 0) {
			echo ("Not find report");
			die();
		}

		$report = $reports[0];

		// create rss url
		$rss_url = "https://clinicaltrials.gov/ct2/results/rss.xml?rcv_d=&lup_d=7&sel_rss=mod7&term=" . str_replace(" ", "+", $report['terms']) . "&type=" . $report['study'] . "&cond=" . str_replace(" ", "+", $report['conditions']) . "&cntry=" . $report['country'] . "&count=10";

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

		foreach ($xml->channel->item as $item) {
			//$details = getStudyDetails($item->link);
			/*
			$contents['link'] = $item->link;
			$contents['title'] = $item->title;
			$contents['description'] = $item->description;
			$contents['guid'] = $item->guid ;
			$contents['pubDate'] = $item->pubDate;
			
			$data['clinics'][] = $contents;
			*/
			$pos = strpos($report['guids'], $item->guid->__toString());

			if ($pos === false) {


				$details['link'] = $item->link;
				$details['title'] = $item->title;
				$details['description'] = $item->description;
				$details['guid'] = $item->guid;
				$details['pubDate'] = $item->pubDate;

				$data['clinics'][] = $details;
			}

			//$guids[] = $item->guid->__toString();
		}

		echo json_encode($data);
	}

	public function downloadDatesListCsv()
	{



		$response = array(
			'success' => false
		);



		$select_last_date = $_GET['last_date_day'];
		$select_start_date = $_GET['start_date_day'];

		date_default_timezone_set('US/Eastern');

		$today = date("m/d/Y");

		$date1_ts = strtotime($select_last_date);
		$date2_ts = strtotime($today);
		$last_days = $date2_ts - $date1_ts;
		$last_days = round($last_days / 86400, PHP_ROUND_HALF_DOWN);

		$date1_ts = strtotime($select_start_date);
		$date2_ts = strtotime($today);
		$start_days = $date2_ts - $date1_ts;
		$start_days = round($start_days / 86400, PHP_ROUND_HALF_DOWN);

		$date1_ts = strtotime($select_start_date);
		$date2_ts = strtotime($select_last_date);
		$total_days = $date2_ts - $date1_ts;
		$total_days = round($total_days / 86400, PHP_ROUND_HALF_DOWN);



		$reports = $this->Reports->getByID($_GET['report_id']);

		if ($reports) {
			$response['success'] = true;

			if (count($reports)) {
				$report = $reports[0];
			} else {
				$report = null;
			}

			$week_list = $report['week_list'];
			$week_reports = $report['week_reports'];

			$filename = $_SESSION['username'] . '_' . $report['title'] . '_' . date('m-d-Y') . '.csv';

			header("Content-Description: File Transfer");
			header('Content-Type: text/csv');
			header("Content-Disposition: attachment; filename=$filename");

			$data = array();

			$week_list_arr = explode(",", $week_list);
			$week_report_arr = explode(",", $week_reports);

			$total_counts = 0;

			$show_date = date('m-d-Y');

			$week_report_arr = array_reverse($week_report_arr);


			$selected_week_report_arr = array();
			$report_count = intval($total_days) + 1;
			$selected_week_report_arr = array_slice($week_report_arr, intval($last_days),  intval($report_count));



			for ($i = 0; $i < count($selected_week_report_arr); $i++) {

				$total_counts +=  $selected_week_report_arr[$i];

				$show_date = date('m-d-Y', strtotime("-$i day", strtotime($select_last_date)));
				$data[] = array($show_date, $selected_week_report_arr[$i], $total_counts);
			}

			$total_counts = 0;

			$fp = fopen('php://output', 'w');

			$header = array("DATE", "COUNTS", "TOTAL COUNTS");
			fputcsv($fp, $header);

			foreach ($data as $key => $line) {
				//$val = explode(",", $line);
				fputcsv($fp, $line);
			}
			fclose($fp);

			exit;
		}



		//exit;
	}
}
