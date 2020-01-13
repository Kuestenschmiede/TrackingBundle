<?php
/*
 * This file is part of con4gis,
 * the gis-kit for Contao CMS.
 *
 * @package    con4gis
 * @version    7
 * @author     con4gis contributors (see "authors.txt")
 * @license    LGPL-3.0-or-later
 * @copyright  Küstenschmiede GmbH Software & Design
 * @link       https://www.con4gis.org
 */

$GLOBALS['TL_LANG']['c4gTracking']['tracking_hint'] = "For the tracking to work, the tracking configuration needs to be choosen on the root-site.";

$GLOBALS['TL_LANG']['c4gTracking']['no_username'] = "No username sent.";
$GLOBALS['TL_LANG']['c4gTracking']['wrong_login'] = "Wrong login.";
$GLOBALS['TL_LANG']['c4gTracking']['no_group_access'] = "No group access.";
$GLOBALS['TL_LANG']['c4gTracking']['no_password'] = "No password sent.";
$GLOBALS['TL_LANG']['c4gTracking']['no_user_password'] = "No username and password sent.";
$GLOBALS['TL_LANG']['c4gTracking']['no_track'] = "No track sent.";
$GLOBALS['TL_LANG']['c4gTracking']['no_config'] = "No configuration sent.";
$GLOBALS['TL_LANG']['c4gTracking']['no_latitude'] = "No latitude sent.";
$GLOBALS['TL_LANG']['c4gTracking']['no_longitude'] = "No longitude sent.";
$GLOBALS['TL_LANG']['c4gTracking']['no_data'] = "No data transmitted.";
$GLOBALS['TL_LANG']['c4gTracking']['data_error'] = "Data error";
$GLOBALS['TL_LANG']['c4gTracking']['method_error'] = "Error in method: ";


$GLOBALS['TL_LANG']['tl_c4g_tracking']['pois'] = array('POIs','Show POIs of configuration %s.');
$GLOBALS['TL_LANG']['tl_c4g_tracking']['tracks'] = array('Tracks','Show tracks of configuration %s.');
$GLOBALS['TL_LANG']['tl_c4g_tracking']['new'] = array('New configuration','Create a new tracking-configuration.');
$GLOBALS['TL_LANG']['tl_c4g_tracking']['edit'] = array('Edit','Edit configuration %s.');
$GLOBALS['TL_LANG']['tl_c4g_tracking']['delete'] = array('Delete','Delete configuration %s.');
$GLOBALS['TL_LANG']['tl_c4g_tracking']['show'] = array('Details','Show details of configuration %s.');

$GLOBALS['TL_LANG']['tl_c4g_tracking']['title_legend'] = 'Title of the tracking-configuration';
$GLOBALS['TL_LANG']['tl_c4g_tracking']['config_legend'] = 'Configuration-settings';
$GLOBALS['TL_LANG']['tl_c4g_tracking']['sms_gateway_legend'] = 'SMS Gateway settings';
$GLOBALS['TL_LANG']['tl_c4g_tracking']['additional_data_legend'] = 'Additional data';
$GLOBALS['TL_LANG']['tl_c4g_tracking']['push_notifications_legend'] = 'Push-Notification settings';
$GLOBALS['TL_LANG']['tl_c4g_tracking']['access_legend'] = 'Access settings';

$GLOBALS['TL_LANG']['tl_c4g_tracking']['name'] = array('Name of the configuration','The name of the configuration can be displayed inside the APP');

$GLOBALS['TL_LANG']['tl_c4g_tracking']['httpGatewayInterval'] = array('Interval of the HTTP gateway', 'Interval of the HTTP gateway');
$GLOBALS['TL_LANG']['tl_c4g_tracking']['useSmsGateway'] = array('Use SMS Gateway', 'Use SMS Gateway');
$GLOBALS['TL_LANG']['tl_c4g_tracking']['smsGatewayInterval'] = array('Interval of the SMS gateway', 'Interval of the SMS gateway');
$GLOBALS['TL_LANG']['tl_c4g_tracking']['smsGatewayNumber'] = array('SMS gateway number', 'SMS gateway number');
$GLOBALS['TL_LANG']['tl_c4g_tracking']['disableHttpGateway'] = array('Disable HTTP Gateway', 'Disable HTTP Gateway');
$GLOBALS['TL_LANG']['tl_c4g_tracking']['adjustAdditionalData'] = array('send additional data', 'Here you can activate additional data from the apps to be transmitted to the backend.');
$GLOBALS['TL_LANG']['tl_c4g_tracking']['additionalData'] = array('Additional data', 'Additional data to be transmitted if necessary.');
$GLOBALS['TL_LANG']['tl_c4g_tracking']['usePushNotifications'] = array('Activate push messages', 'Should the apps be able to receive push messages');
$GLOBALS['TL_LANG']['tl_c4g_tracking']['pushGcmApiKey'] = array('GCM Api Key','API key of the Google Cloud Messaging interface');

$GLOBALS['TL_LANG']['tl_c4g_tracking']['limitAccess'] = array('Zugriff auf das Tracking beschränken', 'Restrict access to tracking');
$GLOBALS['TL_LANG']['tl_c4g_tracking']['accessGroups'] = array('Allowed groups', 'Permitted groups. All others cannot log in to the app.');

$GLOBALS['TL_LANG']['tl_c4g_tracking']['gatewayIntervalOptions'] = array
(
    '0' => 'no interval - on change of position',
    '1' => '1 second',
    '2' => '2 seconds',
    '3' => '3 seconds',
    '5' => '5 seconds',
    '10' => '10 seconds',
    '30' => '30 seconds',
    '60' => '1 minute',
    '120' => '2 minutes'
);

$GLOBALS['TL_LANG']['tl_c4g_tracking']['additionalDataOptions'] = array
(
    'imei' => 'IMEI-Number',
    'batterystatus' => 'Battery stand',
    'networkinfo' => 'Network strength',
    'positionaccuracy' => 'Accuracy of position',
    'positionspeed' => 'Speed of position',
    'positiontype' => 'Position type'
);

