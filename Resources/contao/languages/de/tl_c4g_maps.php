<?php
/*
 * This file is part of con4gis,
 * the gis-kit for Contao CMS.
 *
 * @package    con4gis
 * @version    6
 * @author     con4gis contributors (see "authors.txt")
 * @license    LGPL-3.0-or-later
 * @copyright  Küstenschmiede GmbH Software & Design
 * @link       https://www.con4gis.org
 */

$GLOBALS['TL_LANG']['tl_c4g_maps']['tDontShowIfEmpty'] = array('Ausblenden wenn keine Einträge vorhanden', 'Blendet die Ebene im Starboard aus, wenn sie keine Einträge enthält.');
$GLOBALS['TL_LANG']['tl_c4g_maps']['memberVisibility'] = array('Angezeigte Daten', 'Legt fest wessen Daten auf dieser Ebene dargestellt werden.');
$GLOBALS['TL_LANG']['tl_c4g_maps']['specialMembers'] = array('Ausgewählte Mitglieder', 'Mitglieder dessen Trackingdaten auf dieser Ebene dargestellt werden sollen.');
$GLOBALS['TL_LANG']['tl_c4g_maps']['specialGroups'] = array('Ausgewählte Gruppen', 'Gruppen dessen Mitglieder-Trackingdaten auf dieser Ebene dargestellt werden sollen.');
$GLOBALS['TL_LANG']['tl_c4g_maps']['useDatabaseStatus'] = array('Benutzerdefinierte Sichtbarkeit überschreiben', 'Überschreibt die vom Mitglied eingestellte Sichtbarkeiten seiner Trackingdaten. (Nicht empfohlen!)');
$GLOBALS['TL_LANG']['tl_c4g_maps']['databaseStatus'] = array('Mit folgendem Wert überschreiben', 'Der Wert mit dem die benutzerdefinierten Sichtbarkeiten überschrieben werden sollen.');

$GLOBALS['TL_LANG']['tl_c4g_maps']['liveTrackingType'] = array('Live-Tracking-Typ', 'Einträge gruppieren, zusammenfassen oder nur einzelne Geräte auswählen');
$GLOBALS['TL_LANG']['tl_c4g_maps']['liveTrackingDevices'] = array('Geräte','Geräte');

$GLOBALS['TL_LANG']['tl_c4g_maps']['isFilterable'] = array('Strukturelement ist filterbar', 'Zeigt am Strukturelement den Filter-Button an und initialisiert den Filter');
$GLOBALS['TL_LANG']['tl_c4g_maps']['filterLocationStyle'] = array('Lokationstil für Filter-Tracks', 'Lokationstil für Filter-Tracks');

$GLOBALS['TL_LANG']['tl_c4g_maps']['useIgnitionStatusStyle'] = array('Extra Stil für Zündungsstatus verwenden','Extra Stil für Zündungsstatus verwenden');
$GLOBALS['TL_LANG']['tl_c4g_maps']['ignitionStatusStyleOn'] = array('Stil für Zündung an','Stil für Zündung an');
$GLOBALS['TL_LANG']['tl_c4g_maps']['ignitionStatusStyleOff'] = array('Stil für Zündung aus','Stil für Zündung aus');

$GLOBALS['TL_LANG']['tl_c4g_maps']['references']['tPois'] = 'Tracking – POIs';
$GLOBALS['TL_LANG']['tl_c4g_maps']['references']['tTracks'] = 'Tracking – Tracks';
$GLOBALS['TL_LANG']['tl_c4g_maps']['references']['tLive'] = 'Tracking – Live-Ansicht';
$GLOBALS['TL_LANG']['tl_c4g_maps']['references']['tBoxes'] = 'Tracking – Telematiksysteme';




$GLOBALS['TL_LANG']['tl_c4g_maps']['references']['liveTrackingTypes']['tLive_all'] = "Alle Geräte zusammengefasst";
$GLOBALS['TL_LANG']['tl_c4g_maps']['references']['liveTrackingTypes']['tLive_alleach'] = "Alle Geräte einzeln aufgelistet";
$GLOBALS['TL_LANG']['tl_c4g_maps']['references']['liveTrackingTypes']['tLive_group'] = "Gruppierte Geräte zusammengefasst";
$GLOBALS['TL_LANG']['tl_c4g_maps']['references']['liveTrackingTypes']['tLive_groupeach'] = "Gruppierte Geräte einzeln aufgelistet";
$GLOBALS['TL_LANG']['tl_c4g_maps']['references']['liveTrackingTypes']['tLive_device'] = "Einzelne Geräte zusammengefasst";
$GLOBALS['TL_LANG']['tl_c4g_maps']['references']['liveTrackingTypes']['tLive_deviceeach'] = "Einzelne Geräte einzeln aufgelistet";


$GLOBALS['TL_LANG']['tl_c4g_maps']['live_tracking_legend'] = "Live-Tracking Einstellungen";

$GLOBALS['TL_LANG']['tl_c4g_maps']['references']['memberVisibility'] = array
(
  'own' => 'Eigene Einträge',
  'ownGroups' => 'Mitglieder der Gruppen des aktuellen Mitglieds',
  'specialGroups' => 'Mitglieder ausgewählter Gruppen',
  'specialMember' => 'Ausgewählte Mitglieder',
  'all' => 'Aller Mitglieder'
);

$GLOBALS['TL_LANG']['tl_c4g_maps']['references']['databaseStatus'] = array
(
  'privat' => 'Privat',
  'membergroups' => 'Bestimmte Mitgliedergruppen',
  'owngroups' => 'Mitglieder der Gruppen des Mitglieds',
  'public' => 'Jeder'
);