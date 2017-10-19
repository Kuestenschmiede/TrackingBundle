<?php

/**
 * con4gis - the gis-kit
 *
 * @version   php 5
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Janosch Oltmanns in cooperation with Küstenschmiede GmbH Software & Design 2011 - 2017.
 * @link      https://www.kuestenschmiede.de
 */

$GLOBALS['TL_LANG']['c4gTracking']['tracking_hint'] = "Damit das Tracking funktioniert, muss in der Root-Seite die entsprechende Tracking-Konfiguration ausgewählt sein.";

$GLOBALS['TL_LANG']['c4gTracking']['no_username'] = "Kein Benutzername übermittelt.";
$GLOBALS['TL_LANG']['c4gTracking']['wrong_login'] = "Falscher Benutzername oder falsches Passwort.";
$GLOBALS['TL_LANG']['c4gTracking']['no_group_access'] = "Kein Zugriff auf das Tracking.";
$GLOBALS['TL_LANG']['c4gTracking']['no_password'] = "Kein Passwort übermittelt.";
$GLOBALS['TL_LANG']['c4gTracking']['no_user_password'] = "Kein Benutzername und Passwort übermittelt.";
$GLOBALS['TL_LANG']['c4gTracking']['no_track'] = "Kein Track übermittelt.";
$GLOBALS['TL_LANG']['c4gTracking']['no_config'] = "Keine Konfiguration übermittelt.";
$GLOBALS['TL_LANG']['c4gTracking']['no_latitude'] = "Keine Latitude übermittelt.";
$GLOBALS['TL_LANG']['c4gTracking']['no_longitude'] = "Keine Longitude übermittelt.";
$GLOBALS['TL_LANG']['c4gTracking']['no_data'] = "Keine Daten übermittelt.";
$GLOBALS['TL_LANG']['c4gTracking']['data_error'] = "Daten-Fehler";
$GLOBALS['TL_LANG']['c4gTracking']['method_error'] = "Fehler in Methode: ";



$GLOBALS['TL_LANG']['tl_c4g_tracking']['pois'] = array('POIs','POIs von Konfiguration %s anzeigen.');
$GLOBALS['TL_LANG']['tl_c4g_tracking']['tracks'] = array('Tracks','Tracks von Konfiguration %s anzeigen');
$GLOBALS['TL_LANG']['tl_c4g_tracking']['new'] = array('Neue Konfiguration','Eine neue Tracking-Konfiguration erzeugen.');
$GLOBALS['TL_LANG']['tl_c4g_tracking']['edit'] = array('Bearbeiten','Konfiguration %s bearbeiten.');
$GLOBALS['TL_LANG']['tl_c4g_tracking']['delete'] = array('Löschen','Konfiguration %s löschen.');
$GLOBALS['TL_LANG']['tl_c4g_tracking']['show'] = array('Details','Details der Konfiguration %s anzeigen.');

$GLOBALS['TL_LANG']['tl_c4g_tracking']['title_legend'] = 'Titel der Tracking-Konfiguration';
$GLOBALS['TL_LANG']['tl_c4g_tracking']['config_legend'] = 'Konfigurationseinstellungen';
$GLOBALS['TL_LANG']['tl_c4g_tracking']['sms_gateway_legend'] = 'SMS Gateway Einstellungen';
$GLOBALS['TL_LANG']['tl_c4g_tracking']['additional_data_legend'] = 'Zusätzliche Daten';
$GLOBALS['TL_LANG']['tl_c4g_tracking']['push_notifications_legend'] = 'Push-Nachrichten Einstellungen';
$GLOBALS['TL_LANG']['tl_c4g_tracking']['access_legend'] = 'Zugriff-Einstellungen';


$GLOBALS['TL_LANG']['tl_c4g_tracking']['name'] = array('Name der Konfiguration','Der Name der Konfiguration kann in der App angezeigt werden');

$GLOBALS['TL_LANG']['tl_c4g_tracking']['httpGatewayInterval'] = array('Interval des HTTP-Gateways', 'Interval des HTTP-Gateways');
$GLOBALS['TL_LANG']['tl_c4g_tracking']['useSmsGateway'] = array('SMS Gateway verwenden', 'SMS Gateway verwenden');
$GLOBALS['TL_LANG']['tl_c4g_tracking']['smsGatewayInterval'] = array('Interval des SMS-Gateways', 'Interval des SMS-Gateways');
$GLOBALS['TL_LANG']['tl_c4g_tracking']['smsGatewayNumber'] = array('SMS-Gateway Nummer', 'SMS-Gateway Nummer');
$GLOBALS['TL_LANG']['tl_c4g_tracking']['disableHttpGateway'] = array('HTTP-Gateway deaktivieren', 'HTTP-Gateway deaktivieren');
$GLOBALS['TL_LANG']['tl_c4g_tracking']['adjustAdditionalData'] = array('zusätzliche Daten senden', 'Hier können zusätzlichen Daten von den Apps aktiviert werden, die an das Backend übermittelt werden sollen');
$GLOBALS['TL_LANG']['tl_c4g_tracking']['additionalData'] = array('Zusätzliche Daten', 'Zusätzliche Daten, die ggf. mit übermittelt werden sollen.');
$GLOBALS['TL_LANG']['tl_c4g_tracking']['usePushNotifications'] = array('Push-Nachrichten aktivieren', 'Sollen die Apps Push-Nachrichten erhalten können');
$GLOBALS['TL_LANG']['tl_c4g_tracking']['pushGcmApiKey'] = array('GCM Api Key','API Key der Google Cloud Messaging-Schnittstelle');

$GLOBALS['TL_LANG']['tl_c4g_tracking']['limitAccess'] = array('Zugriff auf das Tracking beschränken', 'Zugriff auf das Tracking beschränken');
$GLOBALS['TL_LANG']['tl_c4g_tracking']['accessGroups'] = array('Erlaubte Gruppen', 'Erlaubte Gruppen. Alle anderen können sich in der App nicht einloggen.');

$GLOBALS['TL_LANG']['tl_c4g_tracking']['gatewayIntervalOptions'] = array
(
    '0' => 'kein Intervall – bei Positionsänderung',
    '1' => '1 Sekunde',
    '2' => '2 Sekunden',
    '3' => '3 Sekunden',
    '5' => '5 Sekunden',
    '10' => '10 Sekunden',
    '30' => '30 Sekunden',
    '60' => '1 Minute',
    '120' => '2 Minuten'
);

$GLOBALS['TL_LANG']['tl_c4g_tracking']['additionalDataOptions'] = array
(
    'imei' => 'IMEI-Nummer',
    'batterystatus' => 'Akku-Stand',
    'networkinfo' => 'Netzwerk Stärke',
    'positionaccuracy' => 'Genauigkeit der Position',
    'positionspeed' => 'Geschwindigkeit der Position',
    'positiontype' => 'Positions-Typ'
);
