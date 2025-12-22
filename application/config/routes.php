<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'monitor';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['admin']           = 'admin/index';
$route['admin/login']     = 'admin/login';
$route['admin/logout']    = 'admin/logout';
$route['admin/dashboard'] = 'admin/dashboard';

$route['admin/users'] = 'admin/users';
$route['admin/users/create'] = 'admin/users_create';

$route['admin/users/update/(:num)']     = 'admin/users_update/$1';
$route['admin/users/deactivate/(:num)'] = 'admin/users_deactivate/$1';

$route['admin/users/reset_device/(:num)'] = 'admin/users_reset_device/$1';
$route['admin/users/toggle/(:num)']       = 'admin/users_toggle/$1';



$route['admin/settings'] = 'admin/settings';
$route['admin/settings/save'] = 'admin/settings_save';
$route['admin/settings/regenerate_secret'] = 'admin/settings_regenerate_secret';

$route['admin/logs']        = 'admin/logs';
$route['admin/logs/export'] = 'admin/logs_export';

$route['admin/rekap']         = 'admin/rekap';
$route['admin/rekap/export']  = 'admin/rekap_export';
$route['admin/rekap/bulanan'] = 'admin/rekap_bulanan';





$route['qr']     = 'qr/index';
$route['qr/png'] = 'qr/png';

$route['absen']         = 'absen/index';
$route['absen/submit'] = 'absen/submit';

$route['monitor']      = 'monitor/index';
$route['monitor/feed'] = 'monitor/feed';
