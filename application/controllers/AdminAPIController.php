<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AdminAPIController extends CI_Controller
{

    public function __construct()
    {

        parent::__construct();

        $this->load->model("Users");
        $this->load->model("Reports");
    }

    public function login()
    {
        $response = array(
            'success' => false
        );

        $users = $this->Users->exist($_POST['email'], $_POST['password']);
        if (count($users) > 0) {
            $response = array(
                'success' => true
            );

            $_SESSION['user_id'] = $users[0]['id'];
            $_SESSION['email'] = $users[0]['email'];
            $_SESSION['username'] = $users[0]['username'];
        }

        echo json_encode($response);
    }

    public function register()
    {
        $response = array(
            'success' => true
        );

        $user_id = $this->Users->add(array(
            'username' => isset($_POST['username']) ? $_POST['username'] : '',
            'email' => isset($_POST['email']) ? $_POST['email'] : '',
            'password' => isset($_POST['password']) ? $_POST['password'] : '',
            'first_name' => isset($_POST['first_name']) ? $_POST['first_name'] : '',
            'last_name' => isset($_POST['last_name']) ? $_POST['last_name'] : ''
        ));

        if (!$user_id) {
            $response = array(
                'success' => false
            );
        }
        echo json_encode($response);
    }

    public function reportAdd()
    {
        $response = array(
            'success' => false
        );

        $report_id = $this->Reports->add(array(
            'title' => isset($_POST['title']) ? $_POST['title'] : '',
            'conditions' => isset($_POST['conditions']) ? $_POST['conditions'] : '',
            'study' => isset($_POST['study']) ? $_POST['study'] : '',
            'country' => isset($_POST['country']) ? $_POST['country'] : '',
            'terms' => isset($_POST['terms']) ? $_POST['terms'] : '',
            'user_id' => isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '',
        ));

        if ($report_id) {
            $response['report_id'] = $report_id;
            $response['success'] = true;

            $reports = $this->Reports->getByID($report_id);

            if (count($reports)) {
                $data = array();
                $data['report'] = $reports[0];
                $data['report']['status'] = '';
                $data['report']['status_str'] = 'No Updates';
                $data['studies'] = getAllStudies();
                $data['countries'] = getAllCountries();

                $report_html = $this->load->view('admin/template/report-template', $data, TRUE);
            }

            $response['report'] = $report_html;
        }

        echo json_encode($response);
    }

    public function reportUpdate()
    {
        $response = array(
            'success' => false
        );

        $report_update = $this->Reports->update(array(
            'id' => isset($_POST['id']) ? $_POST['id'] : '',
            'title' => isset($_POST['title']) ? $_POST['title'] : '',
            'conditions' => isset($_POST['conditions']) ? $_POST['conditions'] : '',
            'study' => isset($_POST['study']) ? $_POST['study'] : '',
            'country' => isset($_POST['country']) ? $_POST['country'] : '',
            'terms' => isset($_POST['terms']) ? $_POST['terms'] : ''
        ));

        $reports = $this->Reports->getByID(isset($_POST['id']) ? $_POST['id'] : '');

        if (count($reports)) {
            $report = $reports[0];
        } else {
            $report = null;
        }

        $found_count = 0;

        $current_week_list = $report['week_list'];
        $current_week_reports = $report['week_reports'];

        $status = 'new';

        if ($report) {
            if ($report['reporting'] == '1') {


                if ($_SESSION['user_id'] == $report['user_id']) {
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
                    $found_count = getRssCount($rss_link);




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


                    $week_update = $this->Reports->updateWeek(array(
                        'id' => isset($_POST['id']) ? $_POST['id'] : '',
                        'week_list' => '1',
                        'week_reports' => $found_count
                    ));

                    $on_reporters = $report['on_reporters'];

                    $current_user_id = $_SESSION['user_id'];

                    $report_status = '';

                    if ($on_reporters == null || $on_reporters == '') {
                        $report_status = $current_user_id;
                    } else {
                        if (strpos($on_reporters, $current_user_id) === false) {
                            $report_status = $on_reporters . ',' . $current_user_id;
                        } else {
                            $report_status = $on_reporters;
                        }
                    }

                    $on_reporters_update = $this->Reports->updateOnReporters(array(
                        'id' => isset($_POST['id']) ? $_POST['id'] : '',
                        'on_reporters' => $report_status
                    ));
                } else {
                    $on_reporters = $report['on_reporters'];

                    $current_user_id = $_SESSION['user_id'];

                    $report_status = '';

                    if ($on_reporters == null || $on_reporters == '') {
                        $report_status = $current_user_id;
                    } else {
                        if (strpos($on_reporters, $current_user_id) === false) {
                            $report_status = $on_reporters . ',' . $current_user_id;
                        } else {
                            $report_status = $on_reporters;
                        }
                    }


                    $on_reporters_update = $this->Reports->updateOnReporters(array(
                        'id' => isset($_POST['id']) ? $_POST['id'] : '',
                        'on_reporters' => $report_status
                    ));
                }
            } else {
                /*
                $status = 'no';

                $week_update = $this->Reports->updateWeek(array(
                    'id' => isset($_POST['id']) ? $_POST['id'] : '',
                    'week_list' => '',
                    'week_reports' => ''
                ));
                */
                if ($_SESSION['user_id'] == $report['user_id']) {
                    $status = 'no';
                    $cur_updated_at = $report['updated_at'];



                    $week_update = $this->Reports->updateWeek(array(
                        'id' => isset($_POST['id']) ? $_POST['id'] : '',
                        'week_list' => '',
                        'week_reports' => ''
                    ));


                    $on_reporters = $report['on_reporters'];

                    $current_user_id = $_SESSION['user_id'];

                    $report_status = '';
                    if ($on_reporters == null || $on_reporters == '') {
                        $report_status = '';
                    } else {
                        if (strpos($on_reporters, $current_user_id) === false) {
                            $report_status = $on_reporters;
                        } else if (strpos($on_reporters, $current_user_id) === 0) {
                            $delete_second_str =  $current_user_id . ',';
                            $report_status = str_replace($delete_second_str, "", $on_reporters);
                        } else {
                            $delete_second_str = ',' . $current_user_id;
                            $report_status = str_replace($delete_second_str, "", $on_reporters);
                        }
                    }

                    $on_reporters_update = $this->Reports->updateOnReporters(array(
                        'id' => isset($_POST['id']) ? $_POST['id'] : '',
                        'on_reporters' => $report_status
                    ));
                } else {

                    $status = 'no';

                    $on_reporters = $report['on_reporters'];

                    $current_user_id = $_SESSION['user_id'];

                    $report_status = '';
                    if ($on_reporters == null || $on_reporters == '') {
                        $report_status = '';
                    } else {
                        if (strpos($on_reporters, $current_user_id) === false) {
                            $report_status = $on_reporters;
                        } else if (strpos($on_reporters, $current_user_id) === 0) {
                            $report_status = str_replace($current_user_id, "", $on_reporters);
                        } else {
                            $delete_second_str = ',' . $current_user_id;
                            $report_status = str_replace($delete_second_str, "", $on_reporters);
                        }
                    }

                    $on_reporters_update = $this->Reports->updateOnReporters(array(
                        'id' => isset($_POST['id']) ? $_POST['id'] : '',
                        'on_reporters' => $report_status
                    ));
                }
            }


            $report_update = $this->Reports->updateField(array(
                'id' => isset($_POST['id']) ? $_POST['id'] : '',
                'status' => $status
            ));


            if ($report_update) {
                $response['success'] = true;
            }

            $response['status'] = $status;
            $response['status_str'] = getStatusString($status);
        } else if ($report_update) {
            $response['success'] = true;
        }

        echo json_encode($response);
    }

    public function reportDelete()
    {
        $response = array(
            'success' => false
        );

        $report_delete = $this->Reports->deleteByID(isset($_POST['id']) ? $_POST['id'] : null);

        if ($report_delete) {
            $response['success'] = true;
        }

        echo json_encode($response);
    }

    public function reportGetWeekList()
    {
        $response = array(
            'success' => false
        );


        $reports = $this->Reports->getByID(isset($_POST['id']) ? $_POST['id'] : '');

        if ($reports) {
            $response['success'] = true;

            if (count($reports)) {
                $report = $reports[0];
            } else {
                $report = null;
            }

            $response['title'] = $report['title'];
            $response['week_list'] = $report['week_list'];
            $response['week_reports'] = $report['week_reports'];
        }

        echo json_encode($response);
    }

    public function reportDuplicate()
    {
        $response = array(
            'success' => false
        );

        $report_id = $this->Reports->duplicateByID(isset($_POST['id']) ? $_POST['id'] : null);

        if ($report_id) {
            $response['success'] = true;
            $response['report_id'] = $report_id;

            $response['success'] = true;

            $reports = $this->Reports->getByID($report_id);

            if (count($reports)) {
                $data = array();
                $data['report'] = $reports[0];
                $data['studies'] = getAllStudies();
                $data['countries'] = getAllCountries();

                $report_html = $this->load->view('admin/template/report-template', $data, TRUE);
            }

            $response['report'] = $report_html;
        }

        echo json_encode($response);
    }

    public function reportReporting()
    {
        $response = array(
            'success' => false
        );

        $reports = $this->Reports->getByID($_POST['id']);

        if (count($reports)) {
            $report = $reports[0];
        } else {
            $report = null;
            echo json_encode($response);
            die();
        }

        $found_count = 0;

        $status = 'new';


        if (isset($_POST['reporting']) && $_POST['reporting'] == '1') {


            if ($_SESSION['user_id'] == $report['user_id']) {
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
                $found_count = getRssCount($rss_link);

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


                $week_update = $this->Reports->updateWeek(array(
                    'id' => isset($_POST['id']) ? $_POST['id'] : '',
                    'week_list' => '1',
                    'week_reports' => $found_count
                ));

                $on_reporters = $report['on_reporters'];

                $current_user_id = $_SESSION['user_id'];

                $report_status = '';

                if ($on_reporters == null || $on_reporters == '') {
                    $report_status = $current_user_id;
                } else {
                    if (strpos($on_reporters, $current_user_id) === false) {
                        $report_status = $on_reporters . ',' . $current_user_id;
                    } else {
                        $report_status = $on_reporters;
                    }
                }

                $on_reporters_update = $this->Reports->updateOnReporters(array(
                    'id' => isset($_POST['id']) ? $_POST['id'] : '',
                    'on_reporters' => $report_status
                ));
            } else {

                $on_reporters = $report['on_reporters'];

                $current_user_id = $_SESSION['user_id'];

                $report_status = '';

                if ($on_reporters == null || $on_reporters == '') {
                    $report_status = $current_user_id;
                } else {
                    if (strpos($on_reporters, $current_user_id) === false) {
                        $report_status = $on_reporters . ',' . $current_user_id;
                    } else {
                        $report_status = $on_reporters;
                    }
                }


                $on_reporters_update = $this->Reports->updateOnReporters(array(
                    'id' => isset($_POST['id']) ? $_POST['id'] : '',
                    'on_reporters' => $report_status
                ));
            }
        } else {
            /*
            $status = 'no';

            $week_update = $this->Reports->updateWeek(array(
                'id' => isset($_POST['id']) ? $_POST['id'] : '',
                'week_list' => '',
                'week_reports' => ''
            ));
            */
            if ($_SESSION['user_id'] == $report['user_id']) {
                $status = 'no';
                $cur_updated_at = $report['updated_at'];



                $week_update = $this->Reports->updateWeek(array(
                    'id' => isset($_POST['id']) ? $_POST['id'] : '',
                    'week_list' => '',
                    'week_reports' => ''
                ));


                $on_reporters = $report['on_reporters'];

                $current_user_id = $_SESSION['user_id'];

                $report_status = '';
                if ($on_reporters == null || $on_reporters == '') {
                    $report_status = '';
                } else {
                    if (strpos($on_reporters, $current_user_id) === false) {
                        $report_status = $on_reporters;
                    } else if (strpos($on_reporters, $current_user_id) === 0) {
                        $delete_second_str =  $current_user_id . ',';
                        $report_status = str_replace($delete_second_str, "", $on_reporters);
                    } else {
                        $delete_second_str = ',' . $current_user_id;
                        $report_status = str_replace($delete_second_str, "", $on_reporters);
                    }
                }

                $on_reporters_update = $this->Reports->updateOnReporters(array(
                    'id' => isset($_POST['id']) ? $_POST['id'] : '',
                    'on_reporters' => $report_status
                ));
            } else {

                $status = 'no';

                $on_reporters = $report['on_reporters'];

                $current_user_id = $_SESSION['user_id'];

                $report_status = '';
                if ($on_reporters == null || $on_reporters == '') {
                    $report_status = '';
                } else {
                    if (strpos($on_reporters, $current_user_id) === false) {
                        $report_status = $on_reporters;
                    } else if (strpos($on_reporters, $current_user_id) === 0) {
                        $report_status = str_replace($current_user_id, "", $on_reporters);
                    } else {
                        $delete_second_str = ',' . $current_user_id;
                        $report_status = str_replace($delete_second_str, "", $on_reporters);
                    }
                }

                $on_reporters_update = $this->Reports->updateOnReporters(array(
                    'id' => isset($_POST['id']) ? $_POST['id'] : '',
                    'on_reporters' => $report_status
                ));
            }
        }

        /*
        $report_update = $this->Reports->updateField(array(
            'id' => isset($_POST['id']) ? $_POST['id'] : '',
            'reporting' => isset($_POST['reporting']) ? $_POST['reporting'] : '',
            'status' => $status
        ));
        */

         //always showing reporting = 1
         $always_reporting = 1;
         $report_update = $this->Reports->updateField(array(
             'id' => isset($_POST['id']) ? $_POST['id'] : '',
             'reporting' => $always_reporting,
 
             'status' => $status
         ));

        if ($report_update) {
            $response['success'] = true;
        }

        $response['status'] = $status;
        $response['status_str'] = getStatusString($status);

        echo json_encode($response);
    }

    public function reportSearch()
    {
        $response = array(
            'success' => false
        );

        $reports = $this->Reports->search(isset($_POST['keyword']) ? $_POST['keyword'] : '', isset($_POST['sort']) ? $_POST['sort'] : 'az', isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '');

        if (count($reports) > 0) {
            $response['success'] = true;
            $html = '';

            foreach ($reports as $report) {

                $data = array();
                $data['report'] = $report;
                $data['studies'] = getAllStudies();
                $data['countries'] = getAllCountries();

                $html .= $this->load->view('admin/template/report-template', $data, TRUE);
            }

            $response['reports'] = $html;
        }

        echo json_encode($response);
    }

    public function userDelete()
    {
        $response = array(
            'success' => false
        );

        $report_delete = $this->Users->deleteByID(isset($_POST['user_id']) ? $_POST['user_id'] : null);

        if ($report_delete) {
            $response['success'] = true;
        }

        echo json_encode($response);
    }
}
