<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Reserved Controller
|--------------------------------------------------------------------------
|
| Set reserved controller for the app.
|
*/

// Reserved Controllers
$config[ 'reserved_controllers' ]                    =    array( 'dashboard' , 'sign-in', 'sign-out' , 'sign-up' , 'do-setup', 'oauth' );
$config[ 'controllers_requiring_installation' ]        =    array( 'dashboard' , 'sign-in' , 'sign-out' , 'sign-up', 'oauth' );
$config[ 'controllers_requiring_login' ]            =    array( 'dashboard' , 'sign-out', 'oauth' );
$config[ 'default_login_route' ]                    =    '/sign-in/';
$config[ 'controllers_requiring_logout' ]           =    array( 'sign-in' , 'sign-up' );
$config[ 'default_logout_route' ]                   =    '/dashboard/';
$config[ 'hide_modules' ]                           =   false;

$config[ 'core_app_name' ]                          =   'Tendoo';

// Core ID
$config[ 'version' ]                                =    $config[ 'core_version' ]    =    '3.3.0'; // core id
$config[ 'core_signature' ]                            =    'Tendoo ' . $config[ 'version' ]; // core id
$config[ 'supported_languages' ]                    =    array(
    'en_US'     =>  'English',
    'fr_FR'     =>  'Français',
    'es_ES'	    =>  'Español',
    'tr_TR'     =>  'Türkçe',
    'de_DE'     =>  'Deutch',
    'ar_AE'     =>  'Arabic (United Arab Emirates)',
    'it_IT'     =>  'Italiano'
);
$config[ 'rtl-languages' ]  = [ 'ar_AE' ];
$config[ 'site_language' ]                            =    'en_US'; // @since 4.0.5
$config[ 'database_version' ]                        =    '1.2';

// Text Domain @since 3.0.5
$config[ 'text_domain' ]                            =    array(
    'tendoo-core'    =>    APPPATH . 'language'
);

// Update
$config[ 'force_major_updates' ]                    =    true;


// For Auth Class (Email Purpose)
// Uses Username to login
$config[ 'username_login' ]                            =    true;
// default route to access email verifcaion
$config[ 'route_for_verification' ]                    =    '/sign-in/verify/';
// default route to access password reset
$config[ 'route_for_reset' ]                            =    '/sign-in/reset/';

// Site Time Zone
$config[ 'site_timezone' ]                                =    [
    'Pacific/Midway' => '(UTC-11:00) Pacific &mdash; Midway',
    'Pacific/Niue' => '(UTC-11:00) Pacific &mdash; Niue',
    'Pacific/Pago_Pago' => '(UTC-11:00) Pacific &mdash; Pago Pago',
    'America/Adak' => '(UTC-10:00) America &mdash; Adak',
    'Pacific/Honolulu' => '(UTC-10:00) Pacific &mdash; Honolulu',
    'Pacific/Johnston' => '(UTC-10:00) Pacific &mdash; Johnston',
    'Pacific/Rarotonga' => '(UTC-10:00) Pacific &mdash; Rarotonga',
    'Pacific/Tahiti' => '(UTC-10:00) Pacific &mdash; Tahiti',
    'Pacific/Marquesas' => '(UTC-09:30) Pacific &mdash; Marquesas',
    'America/Anchorage' => '(UTC-09:00) America &mdash; Anchorage',
    'Pacific/Gambier' => '(UTC-09:00) Pacific &mdash; Gambier',
    'America/Juneau' => '(UTC-09:00) America &mdash; Juneau',
    'America/Nome' => '(UTC-09:00) America &mdash; Nome',
    'America/Sitka' => '(UTC-09:00) America &mdash; Sitka',
    'America/Yakutat' => '(UTC-09:00) America &mdash; Yakutat',
    'America/Dawson' => '(UTC-08:00) America &mdash; Dawson',
    'America/Los_Angeles' => '(UTC-08:00) America &mdash; Los Angeles',
    'America/Metlakatla' => '(UTC-08:00) America &mdash; Metlakatla',
    'Pacific/Pitcairn' => '(UTC-08:00) Pacific &mdash; Pitcairn',
    'America/Santa_Isabel' => '(UTC-08:00) America &mdash; Santa Isabel',
    'America/Tijuana' => '(UTC-08:00) America &mdash; Tijuana',
    'America/Vancouver' => '(UTC-08:00) America &mdash; Vancouver',
    'America/Whitehorse' => '(UTC-08:00) America &mdash; Whitehorse',
    'America/Boise' => '(UTC-07:00) America &mdash; Boise',
    'America/Cambridge_Bay' => '(UTC-07:00) America &mdash; Cambridge Bay',
    'America/Chihuahua' => '(UTC-07:00) America &mdash; Chihuahua',
    'America/Creston' => '(UTC-07:00) America &mdash; Creston',
    'America/Dawson_Creek' => '(UTC-07:00) America &mdash; Dawson Creek',
    'America/Denver' => '(UTC-07:00) America &mdash; Denver',
    'America/Edmonton' => '(UTC-07:00) America &mdash; Edmonton',
    'America/Hermosillo' => '(UTC-07:00) America &mdash; Hermosillo',
    'America/Inuvik' => '(UTC-07:00) America &mdash; Inuvik',
    'America/Mazatlan' => '(UTC-07:00) America &mdash; Mazatlan',
    'America/Ojinaga' => '(UTC-07:00) America &mdash; Ojinaga',
    'America/Phoenix' => '(UTC-07:00) America &mdash; Phoenix',
    'America/Shiprock' => '(UTC-07:00) America &mdash; Shiprock',
    'America/Yellowknife' => '(UTC-07:00) America &mdash; Yellowknife',
    'America/Bahia_Banderas' => '(UTC-06:00) America &mdash; Bahia Banderas',
    'America/Belize' => '(UTC-06:00) America &mdash; Belize',
    'America/North_Dakota/Beulah' => '(UTC-06: America00 &mdash;) Beulah',
    'America/Cancun' => '(UTC-06:00) America &mdash; Cancun',
    'America/North_Dakota/Center' => '(UTC-06: America00 &mdash;) Center',
    'America/Chicago' => '(UTC-06:00) America &mdash; Chicago',
    'America/Costa_Rica' => '(UTC-06:00) America &mdash; Costa Rica',
    'Pacific/Easter' => '(UTC-06:00) Pacific &mdash; Easter',
    'America/El_Salvador' => '(UTC-06:00) America &mdash; El Salvador',
    'Pacific/Galapagos' => '(UTC-06:00) Pacific &mdash; Galapagos',
    'America/Guatemala' => '(UTC-06:00) America &mdash; Guatemala',
    'America/Indiana/Knox' => '(UTC-06: America00 &mdash;) Knox',
    'America/Managua' => '(UTC-06:00) America &mdash; Managua',
    'America/Matamoros' => '(UTC-06:00) America &mdash; Matamoros',
    'America/Menominee' => '(UTC-06:00) America &mdash; Menominee',
    'America/Merida' => '(UTC-06:00) America &mdash; Merida',
    'America/Mexico_City' => '(UTC-06:00) America &mdash; Mexico City',
    'America/Monterrey' => '(UTC-06:00) America &mdash; Monterrey',
    'America/North_Dakota/New_Salem' => '(UTC-06: America00 &mdash;) New Salem',
    'America/Rainy_River' => '(UTC-06:00) America &mdash; Rainy River',
    'America/Rankin_Inlet' => '(UTC-06:00) America &mdash; Rankin Inlet',
    'America/Regina' => '(UTC-06:00) America &mdash; Regina',
    'America/Resolute' => '(UTC-06:00) America &mdash; Resolute',
    'America/Swift_Current' => '(UTC-06:00) America &mdash; Swift Current',
    'America/Tegucigalpa' => '(UTC-06:00) America &mdash; Tegucigalpa',
    'America/Indiana/Tell_City' => '(UTC-06: America00 &mdash;) Tell City',
    'America/Winnipeg' => '(UTC-06:00) America &mdash; Winnipeg',
    'America/Atikokan' => '(UTC-05:00) America &mdash; Atikokan',
    'America/Bogota' => '(UTC-05:00) America &mdash; Bogota',
    'America/Cayman' => '(UTC-05:00) America &mdash; Cayman',
    'America/Detroit' => '(UTC-05:00) America &mdash; Detroit',
    'America/Grand_Turk' => '(UTC-05:00) America &mdash; Grand Turk',
    'America/Guayaquil' => '(UTC-05:00) America &mdash; Guayaquil',
    'America/Havana' => '(UTC-05:00) America &mdash; Havana',
    'America/Indiana/Indianapolis' => '(UTC-05: America00 &mdash;) Indianapolis',
    'America/Iqaluit' => '(UTC-05:00) America &mdash; Iqaluit',
    'America/Jamaica' => '(UTC-05:00) America &mdash; Jamaica',
    'America/Lima' => '(UTC-05:00) America &mdash; Lima',
    'America/Kentucky/Louisville' => '(UTC-05: America00 &mdash;) Louisville',
    'America/Indiana/Marengo' => '(UTC-05: America00 &mdash;) Marengo',
    'America/Kentucky/Monticello' => '(UTC-05: America00 &mdash;) Monticello',
    'America/Montreal' => '(UTC-05:00) America &mdash; Montreal',
    'America/Nassau' => '(UTC-05:00) America &mdash; Nassau',
    'America/New_York' => '(UTC-05:00) America &mdash; New York',
    'America/Nipigon' => '(UTC-05:00) America &mdash; Nipigon',
    'America/Panama' => '(UTC-05:00) America &mdash; Panama',
    'America/Pangnirtung' => '(UTC-05:00) America &mdash; Pangnirtung',
    'America/Indiana/Petersburg' => '(UTC-05: America00 &mdash;) Petersburg',
    'America/Port-au-Prince' => '(UTC- America05 &mdash;:00) Port-au-Prince',
    'America/Thunder_Bay' => '(UTC-05:00) America &mdash; Thunder Bay',
    'America/Toronto' => '(UTC-05:00) America &mdash; Toronto',
    'America/Indiana/Vevay' => '(UTC-05: America00 &mdash;) Vevay',
    'America/Indiana/Vincennes' => '(UTC-05: America00 &mdash;) Vincennes',
    'America/Indiana/Winamac' => '(UTC-05: America00 &mdash;) Winamac',
    'America/Caracas' => '(UTC-04:30) America &mdash; Caracas',
    'America/Anguilla' => '(UTC-04:00) America &mdash; Anguilla',
    'America/Antigua' => '(UTC-04:00) America &mdash; Antigua',
    'America/Aruba' => '(UTC-04:00) America &mdash; Aruba',
    'America/Asuncion' => '(UTC-04:00) America &mdash; Asuncion',
    'America/Barbados' => '(UTC-04:00) America &mdash; Barbados',
    'Atlantic/Bermuda' => '(UTC-04:00) Atlantic &mdash; Bermuda',
    'America/Blanc-Sablon' => '(UTC-04: America00 &mdash;) Blanc-Sablon',
    'America/Boa_Vista' => '(UTC-04:00) America &mdash; Boa Vista',
    'America/Campo_Grande' => '(UTC-04:00) America &mdash; Campo Grande',
    'America/Cuiaba' => '(UTC-04:00) America &mdash; Cuiaba',
    'America/Curacao' => '(UTC-04:00) America &mdash; Curacao',
    'America/Dominica' => '(UTC-04:00) America &mdash; Dominica',
    'America/Eirunepe' => '(UTC-04:00) America &mdash; Eirunepe',
    'America/Glace_Bay' => '(UTC-04:00) America &mdash; Glace Bay',
    'America/Goose_Bay' => '(UTC-04:00) America &mdash; Goose Bay',
    'America/Grenada' => '(UTC-04:00) America &mdash; Grenada',
    'America/Guadeloupe' => '(UTC-04:00) America &mdash; Guadeloupe',
    'America/Guyana' => '(UTC-04:00) America &mdash; Guyana',
    'America/Halifax' => '(UTC-04:00) America &mdash; Halifax',
    'America/Kralendijk' => '(UTC-04:00) America &mdash; Kralendijk',
    'America/La_Paz' => '(UTC-04:00) America &mdash; La Paz',
    'America/Lower_Princes' => '(UTC-04:00) America &mdash; Lower Princes',
    'America/Manaus' => '(UTC-04:00) America &mdash; Manaus',
    'America/Marigot' => '(UTC-04:00) America &mdash; Marigot',
    'America/Martinique' => '(UTC-04:00) America &mdash; Martinique',
    'America/Moncton' => '(UTC-04:00) America &mdash; Moncton',
    'America/Montserrat' => '(UTC-04:00) America &mdash; Montserrat',
    'Antarctica/Palmer' => '(UTC-04:00) Antarctica &mdash; Palmer',
    'America/Port_of_Spain' => '(UTC-04:00) America &mdash; Port of Spain',
    'America/Porto_Velho' => '(UTC-04:00) America &mdash; Porto Velho',
    'America/Puerto_Rico' => '(UTC-04:00) America &mdash; Puerto Rico',
    'America/Rio_Branco' => '(UTC-04:00) America &mdash; Rio Branco',
    'America/Santiago' => '(UTC-04:00) America &mdash; Santiago',
    'America/Santo_Domingo' => '(UTC-04:00) America &mdash; Santo Domingo',
    'America/St_Barthelemy' => '(UTC-04:00) America &mdash; St. Barthelemy',
    'America/St_Kitts' => '(UTC-04:00) America &mdash; St. Kitts',
    'America/St_Lucia' => '(UTC-04:00) America &mdash; St. Lucia',
    'America/St_Thomas' => '(UTC-04:00) America &mdash; St. Thomas',
    'America/St_Vincent' => '(UTC-04:00) America &mdash; St. Vincent',
    'America/Thule' => '(UTC-04:00) America &mdash; Thule',
    'America/Tortola' => '(UTC-04:00) America &mdash; Tortola',
    'America/St_Johns' => '(UTC-03:30) America &mdash; St. Johns',
    'America/Araguaina' => '(UTC-03:00) America &mdash; Araguaina',
    'America/Bahia' => '(UTC-03:00) America &mdash; Bahia',
    'America/Belem' => '(UTC-03:00) America &mdash; Belem',
    'America/Argentina/Buenos_Aires' => '(UTC-03: America00 &mdash;) Buenos Aires',
    'America/Argentina/Catamarca' => '(UTC-03: America00 &mdash;) Catamarca',
    'America/Cayenne' => '(UTC-03:00) America &mdash; Cayenne',
    'America/Argentina/Cordoba' => '(UTC-03: America00 &mdash;) Cordoba',
    'America/Fortaleza' => '(UTC-03:00) America &mdash; Fortaleza',
    'America/Godthab' => '(UTC-03:00) America &mdash; Godthab',
    'America/Argentina/Jujuy' => '(UTC-03: America00 &mdash;) Jujuy',
    'America/Argentina/La_Rioja' => '(UTC-03: America00 &mdash;) La Rioja',
    'America/Maceio' => '(UTC-03:00) America &mdash; Maceio',
    'America/Argentina/Mendoza' => '(UTC-03: America00 &mdash;) Mendoza',
    'America/Miquelon' => '(UTC-03:00) America &mdash; Miquelon',
    'America/Montevideo' => '(UTC-03:00) America &mdash; Montevideo',
    'America/Paramaribo' => '(UTC-03:00) America &mdash; Paramaribo',
    'America/Recife' => '(UTC-03:00) America &mdash; Recife',
    'America/Argentina/Rio_Gallegos' => '(UTC-03: America00 &mdash;) Rio Gallegos',
    'Antarctica/Rothera' => '(UTC-03:00) Antarctica &mdash; Rothera',
    'America/Argentina/Salta' => '(UTC-03: America00 &mdash;) Salta',
    'America/Argentina/San_Juan' => '(UTC-03: America00 &mdash;) San Juan',
    'America/Argentina/San_Luis' => '(UTC-03: America00 &mdash;) San Luis',
    'America/Santarem' => '(UTC-03:00) America &mdash; Santarem',
    'America/Sao_Paulo' => '(UTC-03:00) America &mdash; Sao Paulo',
    'Atlantic/Stanley' => '(UTC-03:00) Atlantic &mdash; Stanley',
    'America/Argentina/Tucuman' => '(UTC-03: America00 &mdash;) Tucuman',
    'America/Argentina/Ushuaia' => '(UTC-03: America00 &mdash;) Ushuaia',
    'America/Noronha' => '(UTC-02:00) America &mdash; Noronha',
    'Atlantic/South_Georgia' => '(UTC-02:00) Atlantic &mdash; South Georgia',
    'Atlantic/Azores' => '(UTC-01:00) Atlantic &mdash; Azores',
    'Atlantic/Cape_Verde' => '(UTC-01:00) Atlantic &mdash; Cape Verde',
    'America/Scoresbysund' => '(UTC-01:00) America &mdash; Scoresbysund',
    'Africa/Abidjan' => '(UTC+00:00) Africa &mdash; Abidjan',
    'Africa/Accra' => '(UTC+00:00) Africa &mdash; Accra',
    'Africa/Bamako' => '(UTC+00:00) Africa &mdash; Bamako',
    'Africa/Banjul' => '(UTC+00:00) Africa &mdash; Banjul',
    'Africa/Bissau' => '(UTC+00:00) Africa &mdash; Bissau',
    'Atlantic/Canary' => '(UTC+00:00) Atlantic &mdash; Canary',
    'Africa/Casablanca' => '(UTC+00:00) Africa &mdash; Casablanca',
    'Africa/Conakry' => '(UTC+00:00) Africa &mdash; Conakry',
    'Africa/Dakar' => '(UTC+00:00) Africa &mdash; Dakar',
    'America/Danmarkshavn' => '(UTC+00:00) America &mdash; Danmarkshavn',
    'Europe/Dublin' => '(UTC+00:00) Europe &mdash; Dublin',
    'Africa/El_Aaiun' => '(UTC+00:00) Africa &mdash; El Aaiun',
    'Atlantic/Faroe' => '(UTC+00:00) Atlantic &mdash; Faroe',
    'Africa/Freetown' => '(UTC+00:00) Africa &mdash; Freetown',
    'Europe/Guernsey' => '(UTC+00:00) Europe &mdash; Guernsey',
    'Europe/Isle_of_Man' => '(UTC+00:00) Europe &mdash; Isle of Man',
    'Europe/Jersey' => '(UTC+00:00) Europe &mdash; Jersey',
    'Europe/Lisbon' => '(UTC+00:00) Europe &mdash; Lisbon',
    'Africa/Lome' => '(UTC+00:00) Africa &mdash; Lome',
    'Europe/London' => '(UTC+00:00) Europe &mdash; London',
    'Atlantic/Madeira' => '(UTC+00:00) Atlantic &mdash; Madeira',
    'Africa/Monrovia' => '(UTC+00:00) Africa &mdash; Monrovia',
    'Africa/Nouakchott' => '(UTC+00:00) Africa &mdash; Nouakchott',
    'Africa/Ouagadougou' => '(UTC+00:00) Africa &mdash; Ouagadougou',
    'Atlantic/Reykjavik' => '(UTC+00:00) Atlantic &mdash; Reykjavik',
    'Africa/Sao_Tome' => '(UTC+00:00) Africa &mdash; Sao Tome',
    'Atlantic/St_Helena' => '(UTC+00:00) Atlantic &mdash; St. Helena',
    'UTC' => '(UTC+00:00)  UTCUTC &mdash;',
    'Africa/Algiers' => '(UTC+01:00) Africa &mdash; Algiers',
    'Europe/Amsterdam' => '(UTC+01:00) Europe &mdash; Amsterdam',
    'Europe/Andorra' => '(UTC+01:00) Europe &mdash; Andorra',
    'Africa/Bangui' => '(UTC+01:00) Africa &mdash; Bangui',
    'Europe/Belgrade' => '(UTC+01:00) Europe &mdash; Belgrade',
    'Europe/Berlin' => '(UTC+01:00) Europe &mdash; Berlin',
    'Europe/Bratislava' => '(UTC+01:00) Europe &mdash; Bratislava',
    'Africa/Brazzaville' => '(UTC+01:00) Africa &mdash; Brazzaville',
    'Europe/Brussels' => '(UTC+01:00) Europe &mdash; Brussels',
    'Europe/Budapest' => '(UTC+01:00) Europe &mdash; Budapest',
    'Europe/Busingen' => '(UTC+01:00) Europe &mdash; Busingen',
    'Africa/Ceuta' => '(UTC+01:00) Africa &mdash; Ceuta',
    'Europe/Copenhagen' => '(UTC+01:00) Europe &mdash; Copenhagen',
    'Africa/Douala' => '(UTC+01:00) Africa &mdash; Douala',
    'Europe/Gibraltar' => '(UTC+01:00) Europe &mdash; Gibraltar',
    'Africa/Kinshasa' => '(UTC+01:00) Africa &mdash; Kinshasa',
    'Africa/Lagos' => '(UTC+01:00) Africa &mdash; Lagos',
    'Africa/Libreville' => '(UTC+01:00) Africa &mdash; Libreville',
    'Europe/Ljubljana' => '(UTC+01:00) Europe &mdash; Ljubljana',
    'Arctic/Longyearbyen' => '(UTC+01:00) Arctic &mdash; Longyearbyen',
    'Africa/Luanda' => '(UTC+01:00) Africa &mdash; Luanda',
    'Europe/Luxembourg' => '(UTC+01:00) Europe &mdash; Luxembourg',
    'Europe/Madrid' => '(UTC+01:00) Europe &mdash; Madrid',
    'Africa/Malabo' => '(UTC+01:00) Africa &mdash; Malabo',
    'Europe/Malta' => '(UTC+01:00) Europe &mdash; Malta',
    'Europe/Monaco' => '(UTC+01:00) Europe &mdash; Monaco',
    'Africa/Ndjamena' => '(UTC+01:00) Africa &mdash; Ndjamena',
    'Africa/Niamey' => '(UTC+01:00) Africa &mdash; Niamey',
    'Europe/Oslo' => '(UTC+01:00) Europe &mdash; Oslo',
    'Europe/Paris' => '(UTC+01:00) Europe &mdash; Paris',
    'Europe/Podgorica' => '(UTC+01:00) Europe &mdash; Podgorica',
    'Africa/Porto-Novo' => '(UTC+01:00) Africa00 &mdash; Porto-Novo',
    'Europe/Prague' => '(UTC+01:00) Europe &mdash; Prague',
    'Europe/Rome' => '(UTC+01:00) Europe &mdash; Rome',
    'Europe/San_Marino' => '(UTC+01:00) Europe &mdash; San Marino',
    'Europe/Sarajevo' => '(UTC+01:00) Europe &mdash; Sarajevo',
    'Europe/Skopje' => '(UTC+01:00) Europe &mdash; Skopje',
    'Europe/Stockholm' => '(UTC+01:00) Europe &mdash; Stockholm',
    'Europe/Tirane' => '(UTC+01:00) Europe &mdash; Tirane',
    'Africa/Tripoli' => '(UTC+01:00) Africa &mdash; Tripoli',
    'Africa/Tunis' => '(UTC+01:00) Africa &mdash; Tunis',
    'Europe/Vaduz' => '(UTC+01:00) Europe &mdash; Vaduz',
    'Europe/Vatican' => '(UTC+01:00) Europe &mdash; Vatican',
    'Europe/Vienna' => '(UTC+01:00) Europe &mdash; Vienna',
    'Europe/Warsaw' => '(UTC+01:00) Europe &mdash; Warsaw',
    'Africa/Windhoek' => '(UTC+01:00) Africa &mdash; Windhoek',
    'Europe/Zagreb' => '(UTC+01:00) Europe &mdash; Zagreb',
    'Europe/Zurich' => '(UTC+01:00) Europe &mdash; Zurich',
    'Europe/Athens' => '(UTC+02:00) Europe &mdash; Athens',
    'Asia/Beirut' => '(UTC+02:00) Asia &mdash; Beirut',
    'Africa/Blantyre' => '(UTC+02:00) Africa &mdash; Blantyre',
    'Europe/Bucharest' => '(UTC+02:00) Europe &mdash; Bucharest',
    'Africa/Bujumbura' => '(UTC+02:00) Africa &mdash; Bujumbura',
    'Africa/Cairo' => '(UTC+02:00) Africa &mdash; Cairo',
    'Europe/Chisinau' => '(UTC+02:00) Europe &mdash; Chisinau',
    'Asia/Damascus' => '(UTC+02:00) Asia &mdash; Damascus',
    'Africa/Gaborone' => '(UTC+02:00) Africa &mdash; Gaborone',
    'Asia/Gaza' => '(UTC+02:00) Asia &mdash; Gaza',
    'Africa/Harare' => '(UTC+02:00) Africa &mdash; Harare',
    'Asia/Hebron' => '(UTC+02:00) Asia &mdash; Hebron',
    'Europe/Helsinki' => '(UTC+02:00) Europe &mdash; Helsinki',
    'Europe/Istanbul' => '(UTC+02:00) Europe &mdash; Istanbul',
    'Asia/Jerusalem' => '(UTC+02:00) Asia &mdash; Jerusalem',
    'Africa/Johannesburg' => '(UTC+02:00) Africa &mdash; Johannesburg',
    'Europe/Kiev' => '(UTC+02:00) Europe &mdash; Kiev',
    'Africa/Kigali' => '(UTC+02:00) Africa &mdash; Kigali',
    'Africa/Lubumbashi' => '(UTC+02:00) Africa &mdash; Lubumbashi',
    'Africa/Lusaka' => '(UTC+02:00) Africa &mdash; Lusaka',
    'Africa/Maputo' => '(UTC+02:00) Africa &mdash; Maputo',
    'Europe/Mariehamn' => '(UTC+02:00) Europe &mdash; Mariehamn',
    'Africa/Maseru' => '(UTC+02:00) Africa &mdash; Maseru',
    'Africa/Mbabane' => '(UTC+02:00) Africa &mdash; Mbabane',
    'Asia/Nicosia' => '(UTC+02:00) Asia &mdash; Nicosia',
    'Europe/Riga' => '(UTC+02:00) Europe &mdash; Riga',
    'Europe/Simferopol' => '(UTC+02:00) Europe &mdash; Simferopol',
    'Europe/Sofia' => '(UTC+02:00) Europe &mdash; Sofia',
    'Europe/Tallinn' => '(UTC+02:00) Europe &mdash; Tallinn',
    'Europe/Uzhgorod' => '(UTC+02:00) Europe &mdash; Uzhgorod',
    'Europe/Vilnius' => '(UTC+02:00) Europe &mdash; Vilnius',
    'Europe/Zaporozhye' => '(UTC+02:00) Europe &mdash; Zaporozhye',
    'Africa/Addis_Ababa' => '(UTC+03:00) Africa &mdash; Addis Ababa',
    'Asia/Aden' => '(UTC+03:00) Asia &mdash; Aden',
    'Asia/Amman' => '(UTC+03:00) Asia &mdash; Amman',
    'Indian/Antananarivo' => '(UTC+03:00) Indian &mdash; Antananarivo',
    'Africa/Asmara' => '(UTC+03:00) Africa &mdash; Asmara',
    'Asia/Baghdad' => '(UTC+03:00) Asia &mdash; Baghdad',
    'Asia/Bahrain' => '(UTC+03:00) Asia &mdash; Bahrain',
    'Indian/Comoro' => '(UTC+03:00) Indian &mdash; Comoro',
    'Africa/Dar_es_Salaam' => '(UTC+03:00) Africa &mdash; Dar es Salaam',
    'Africa/Djibouti' => '(UTC+03:00) Africa &mdash; Djibouti',
    'Africa/Juba' => '(UTC+03:00) Africa &mdash; Juba',
    'Europe/Kaliningrad' => '(UTC+03:00) Europe &mdash; Kaliningrad',
    'Africa/Kampala' => '(UTC+03:00) Africa &mdash; Kampala',
    'Africa/Khartoum' => '(UTC+03:00) Africa &mdash; Khartoum',
    'Asia/Kuwait' => '(UTC+03:00) Asia &mdash; Kuwait',
    'Indian/Mayotte' => '(UTC+03:00) Indian &mdash; Mayotte',
    'Europe/Minsk' => '(UTC+03:00) Europe &mdash; Minsk',
    'Africa/Mogadishu' => '(UTC+03:00) Africa &mdash; Mogadishu',
    'Africa/Nairobi' => '(UTC+03:00) Africa &mdash; Nairobi',
    'Asia/Qatar' => '(UTC+03:00) Asia &mdash; Qatar',
    'Asia/Riyadh' => '(UTC+03:00) Asia &mdash; Riyadh',
    'Antarctica/Syowa' => '(UTC+03:00) Antarctica &mdash; Syowa',
    'Asia/Tehran' => '(UTC+03:30) Asia &mdash; Tehran',
    'Asia/Baku' => '(UTC+04:00) Asia &mdash; Baku',
    'Asia/Dubai' => '(UTC+04:00) Asia &mdash; Dubai',
    'Indian/Mahe' => '(UTC+04:00) Indian &mdash; Mahe',
    'Indian/Mauritius' => '(UTC+04:00) Indian &mdash; Mauritius',
    'Europe/Moscow' => '(UTC+04:00) Europe &mdash; Moscow',
    'Asia/Muscat' => '(UTC+04:00) Asia &mdash; Muscat',
    'Indian/Reunion' => '(UTC+04:00) Indian &mdash; Reunion',
    'Europe/Samara' => '(UTC+04:00) Europe &mdash; Samara',
    'Asia/Tbilisi' => '(UTC+04:00) Asia &mdash; Tbilisi',
    'Europe/Volgograd' => '(UTC+04:00) Europe &mdash; Volgograd',
    'Asia/Yerevan' => '(UTC+04:00) Asia &mdash; Yerevan',
    'Asia/Kabul' => '(UTC+04:30) Asia &mdash; Kabul',
    'Asia/Aqtau' => '(UTC+05:00) Asia &mdash; Aqtau',
    'Asia/Aqtobe' => '(UTC+05:00) Asia &mdash; Aqtobe',
    'Asia/Ashgabat' => '(UTC+05:00) Asia &mdash; Ashgabat',
    'Asia/Dushanbe' => '(UTC+05:00) Asia &mdash; Dushanbe',
    'Asia/Karachi' => '(UTC+05:00) Asia &mdash; Karachi',
    'Indian/Kerguelen' => '(UTC+05:00) Indian &mdash; Kerguelen',
    'Indian/Maldives' => '(UTC+05:00) Indian &mdash; Maldives',
    'Antarctica/Mawson' => '(UTC+05:00) Antarctica &mdash; Mawson',
    'Asia/Oral' => '(UTC+05:00) Asia &mdash; Oral',
    'Asia/Samarkand' => '(UTC+05:00) Asia &mdash; Samarkand',
    'Asia/Tashkent' => '(UTC+05:00) Asia &mdash; Tashkent',
    'Asia/Colombo' => '(UTC+05:30) Asia &mdash; Colombo',
    'Asia/Kolkata' => '(UTC+05:30) Asia &mdash; Kolkata',
    'Asia/Kathmandu' => '(UTC+05:45) Asia &mdash; Kathmandu',
    'Asia/Almaty' => '(UTC+06:00) Asia &mdash; Almaty',
    'Asia/Bishkek' => '(UTC+06:00) Asia &mdash; Bishkek',
    'Indian/Chagos' => '(UTC+06:00) Indian &mdash; Chagos',
    'Asia/Dhaka' => '(UTC+06:00) Asia &mdash; Dhaka',
    'Asia/Qyzylorda' => '(UTC+06:00) Asia &mdash; Qyzylorda',
    'Asia/Thimphu' => '(UTC+06:00) Asia &mdash; Thimphu',
    'Antarctica/Vostok' => '(UTC+06:00) Antarctica &mdash; Vostok',
    'Asia/Yekaterinburg' => '(UTC+06:00) Asia &mdash; Yekaterinburg',
    'Indian/Cocos' => '(UTC+06:30) Indian &mdash; Cocos',
    'Asia/Rangoon' => '(UTC+06:30) Asia &mdash; Rangoon',
    'Asia/Bangkok' => '(UTC+07:00) Asia &mdash; Bangkok',
    'Indian/Christmas' => '(UTC+07:00) Indian &mdash; Christmas',
    'Antarctica/Davis' => '(UTC+07:00) Antarctica &mdash; Davis',
    'Asia/Ho_Chi_Minh' => '(UTC+07:00) Asia &mdash; Ho Chi Minh',
    'Asia/Hovd' => '(UTC+07:00) Asia &mdash; Hovd',
    'Asia/Jakarta' => '(UTC+07:00) Asia &mdash; Jakarta',
    'Asia/Novokuznetsk' => '(UTC+07:00) Asia &mdash; Novokuznetsk',
    'Asia/Novosibirsk' => '(UTC+07:00) Asia &mdash; Novosibirsk',
    'Asia/Omsk' => '(UTC+07:00) Asia &mdash; Omsk',
    'Asia/Phnom_Penh' => '(UTC+07:00) Asia &mdash; Phnom Penh',
    'Asia/Pontianak' => '(UTC+07:00) Asia &mdash; Pontianak',
    'Asia/Vientiane' => '(UTC+07:00) Asia &mdash; Vientiane',
    'Asia/Brunei' => '(UTC+08:00) Asia &mdash; Brunei',
    'Antarctica/Casey' => '(UTC+08:00) Antarctica &mdash; Casey',
    'Asia/Choibalsan' => '(UTC+08:00) Asia &mdash; Choibalsan',
    'Asia/Chongqing' => '(UTC+08:00) Asia &mdash; Chongqing',
    'Asia/Harbin' => '(UTC+08:00) Asia &mdash; Harbin',
    'Asia/Hong_Kong' => '(UTC+08:00) Asia &mdash; Hong Kong',
    'Asia/Kashgar' => '(UTC+08:00) Asia &mdash; Kashgar',
    'Asia/Krasnoyarsk' => '(UTC+08:00) Asia &mdash; Krasnoyarsk',
    'Asia/Kuala_Lumpur' => '(UTC+08:00) Asia &mdash; Kuala Lumpur',
    'Asia/Kuching' => '(UTC+08:00) Asia &mdash; Kuching',
    'Asia/Macau' => '(UTC+08:00) Asia &mdash; Macau',
    'Asia/Makassar' => '(UTC+08:00) Asia &mdash; Makassar',
    'Asia/Manila' => '(UTC+08:00) Asia &mdash; Manila',
    'Australia/Perth' => '(UTC+08:00) Australia &mdash; Perth',
    'Asia/Shanghai' => '(UTC+08:00) Asia &mdash; Shanghai',
    'Asia/Singapore' => '(UTC+08:00) Asia &mdash; Singapore',
    'Asia/Taipei' => '(UTC+08:00) Asia &mdash; Taipei',
    'Asia/Ulaanbaatar' => '(UTC+08:00) Asia &mdash; Ulaanbaatar',
    'Asia/Urumqi' => '(UTC+08:00) Asia &mdash; Urumqi',
    'Australia/Eucla' => '(UTC+08:45) Australia &mdash; Eucla',
    'Asia/Dili' => '(UTC+09:00) Asia &mdash; Dili',
    'Asia/Irkutsk' => '(UTC+09:00) Asia &mdash; Irkutsk',
    'Asia/Jayapura' => '(UTC+09:00) Asia &mdash; Jayapura',
    'Pacific/Palau' => '(UTC+09:00) Pacific &mdash; Palau',
    'Asia/Pyongyang' => '(UTC+09:00) Asia &mdash; Pyongyang',
    'Asia/Seoul' => '(UTC+09:00) Asia &mdash; Seoul',
    'Asia/Tokyo' => '(UTC+09:00) Asia &mdash; Tokyo',
    'Australia/Adelaide' => '(UTC+09:30) Australia &mdash; Adelaide',
    'Australia/Broken_Hill' => '(UTC+09:30) Australia &mdash; Broken Hill',
    'Australia/Darwin' => '(UTC+09:30) Australia &mdash; Darwin',
    'Australia/Brisbane' => '(UTC+10:00) Australia &mdash; Brisbane',
    'Pacific/Chuuk' => '(UTC+10:00) Pacific &mdash; Chuuk',
    'Australia/Currie' => '(UTC+10:00) Australia &mdash; Currie',
    'Antarctica/DumontDUrville' => '(UTC+10:00) Antarctica &mdash; DumontDUrville',
    'Pacific/Guam' => '(UTC+10:00) Pacific &mdash; Guam',
    'Australia/Hobart' => '(UTC+10:00) Australia &mdash; Hobart',
    'Asia/Khandyga' => '(UTC+10:00) Asia &mdash; Khandyga',
    'Australia/Lindeman' => '(UTC+10:00) Australia &mdash; Lindeman',
    'Australia/Melbourne' => '(UTC+10:00) Australia &mdash; Melbourne',
    'Pacific/Port_Moresby' => '(UTC+10:00) Pacific &mdash; Port Moresby',
    'Pacific/Saipan' => '(UTC+10:00) Pacific &mdash; Saipan',
    'Australia/Sydney' => '(UTC+10:00) Australia &mdash; Sydney',
    'Asia/Yakutsk' => '(UTC+10:00) Asia &mdash; Yakutsk',
    'Australia/Lord_Howe' => '(UTC+10:30) Australia &mdash; Lord Howe',
    'Pacific/Efate' => '(UTC+11:00) Pacific &mdash; Efate',
    'Pacific/Guadalcanal' => '(UTC+11:00) Pacific &mdash; Guadalcanal',
    'Pacific/Kosrae' => '(UTC+11:00) Pacific &mdash; Kosrae',
    'Antarctica/Macquarie' => '(UTC+11:00) Antarctica &mdash; Macquarie',
    'Pacific/Noumea' => '(UTC+11:00) Pacific &mdash; Noumea',
    'Pacific/Pohnpei' => '(UTC+11:00) Pacific &mdash; Pohnpei',
    'Asia/Sakhalin' => '(UTC+11:00) Asia &mdash; Sakhalin',
    'Asia/Ust-Nera' => '(UTC+11: Asia00 &mdash;) Ust-Nera',
    'Asia/Vladivostok' => '(UTC+11:00) Asia &mdash; Vladivostok',
    'Pacific/Norfolk' => '(UTC+11:30) Pacific &mdash; Norfolk',
    'Asia/Anadyr' => '(UTC+12:00) Asia &mdash; Anadyr',
    'Pacific/Auckland' => '(UTC+12:00) Pacific &mdash; Auckland',
    'Pacific/Fiji' => '(UTC+12:00) Pacific &mdash; Fiji',
    'Pacific/Funafuti' => '(UTC+12:00) Pacific &mdash; Funafuti',
    'Asia/Kamchatka' => '(UTC+12:00) Asia &mdash; Kamchatka',
    'Pacific/Kwajalein' => '(UTC+12:00) Pacific &mdash; Kwajalein',
    'Asia/Magadan' => '(UTC+12:00) Asia &mdash; Magadan',
    'Pacific/Majuro' => '(UTC+12:00) Pacific &mdash; Majuro',
    'Antarctica/McMurdo' => '(UTC+12:00) Antarctica &mdash; McMurdo',
    'Pacific/Nauru' => '(UTC+12:00) Pacific &mdash; Nauru',
    'Antarctica/South_Pole' => '(UTC+12:00) Antarctica &mdash; South Pole',
    'Pacific/Tarawa' => '(UTC+12:00) Pacific &mdash; Tarawa',
    'Pacific/Wake' => '(UTC+12:00) Pacific &mdash; Wake',
    'Pacific/Wallis' => '(UTC+12:00) Pacific &mdash; Wallis',
    'Pacific/Chatham' => '(UTC+12:45) Pacific &mdash; Chatham',
    'Pacific/Apia' => '(UTC+13:00) Pacific &mdash; Apia',
    'Pacific/Enderbury' => '(UTC+13:00) Pacific &mdash; Enderbury',
    'Pacific/Fakaofo' => '(UTC+13:00) Pacific &mdash; Fakaofo',
    'Pacific/Tongatapu' => '(UTC+13:00) Pacific &mdash; Tongatapu',
    'Pacific/Kiritimati' => '(UTC+14:00) Pacific &mdash; Kiritimati',
];
//
$config[ 'default_user_names' ]                            =    'John Doe';
$config[ 'admin_menus' ]                                =    array();
$config[ 'admin_menu_position' ]                        =    array( 'before' , 'after' );
$config[ 'admin_menu_item' ]                            =    array( 'dashboard' , 'menu' , 'about' , 'controllers' , 'installer' , 'modules' , 'themes' , 'settings' , 'frontend' );