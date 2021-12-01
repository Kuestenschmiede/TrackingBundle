<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2021, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */



$GLOBALS['TL_DCA']['tl_c4g_map_profiles']['palettes']['default'] = str_replace("{geosearch_legend:hide},geosearch_headline,geosearch_engine,geosearchParams", "{geosearch_legend:hide},geosearch_headline,geosearch_engine,ownGeosearch,geosearchParams", $GLOBALS['TL_DCA']['tl_c4g_map_profiles']['palettes']['default']);

$GLOBALS['TL_DCA']['tl_c4g_map_profiles']['fields']['ownGeosearch'] = [
    'label'                   => &$GLOBALS['TL_LANG']['tl_c4g_map_profiles']['ownGeosearch'],
    'exclude'                 => true,
    'default'                 => false,
    'inputType'               => 'checkbox',
    'sql'                     => "char(1) NOT NULL default ''"
];