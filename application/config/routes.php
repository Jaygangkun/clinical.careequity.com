<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'AdminController';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// Pages
$route['login'] = 'AdminController/login';
$route['register'] = 'AdminController/register';
$route['forgot-password'] = 'AdminController/forgotPassword';
$route['reset-password/(:any)'] = 'AdminController/resetPassword/$1';
$route['dashboard'] = 'AdminController/dashboard';
$route['verify/(:any)'] = 'AdminController/verify/$1';

$route['profile'] = 'AdminController/profile';
$route['users'] = 'AdminController/users';
$route['user-edit/(:any)'] = 'AdminController/userEdit/$1';
$route['user-new'] = 'AdminController/userNew';

// Admin APIS
$route['admin_api/login'] = 'AdminAPIController/login';
$route['admin_api/register'] = 'AdminAPIController/register';

$route['admin_api/report_add'] = 'AdminAPIController/reportAdd';
$route['admin_api/report_update'] = 'AdminAPIController/reportUpdate';
$route['admin_api/report_delete'] = 'AdminAPIController/reportDelete';
$route['admin_api/report_duplicate'] = 'AdminAPIController/reportDuplicate';
$route['admin_api/report_reporting'] = 'AdminAPIController/reportReporting';
$route['admin_api/report_search'] = 'AdminAPIController/reportSearch';

$route['admin_api/user_delete'] = 'AdminAPIController/userDelete';

$route['admin_api/report_get_week_list'] = 'AdminAPIController/reportGetWeekList';
$route['admin_api/download_csv'] = 'RSSController/downloadListCsv';
$route['admin_api/popup_update'] = 'RSSController/rssPopup';

// 
$route['rss_test'] = 'RSSController/rssTest';
$route['cronjob_test'] = 'CronJobController/test';
$route['cronjob'] = 'CronJobController/run';
$route['cronjob_check'] = 'CronJobController/check';

$route['admin_api/rss_download'] = 'RSSController/rssDownload';

$route['admin_api/download_dates_csv'] = 'RSSController/downloadDatesListCsv';


// Global APIS
$route['(:any)'] = 'CustomerController/customerPage/$1';