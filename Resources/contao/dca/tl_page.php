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

/**
 * Table tl_page
 */
$GLOBALS['TL_DCA']['tl_page']['palettes']['root'] = str_replace(';{publish_legend', ';{c4gtracking_legend:hide},c4gtracking_configuration;{publish_legend',
$GLOBALS['TL_DCA']['tl_page']['palettes']['root']);

$GLOBALS['TL_DCA']['tl_page']['fields']['c4gtracking_configuration'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_page']['c4gtracking_configuration'],
 	'exclude'                 => true,
    'inputType'               => 'select',
 	'foreignKey'              => 'tl_c4g_tracking.name',
 	'options_callback'        => array('tl_c4gtracking_page', 'getTrackingConfigurations'),
 	'eval'                    => array('includeBlankOption'=>true, 'chosen'=>true, 'tl_class'=>'w50'),
 	'sql'                     => "int(10) unsigned NOT NULL default '0'",
 	'relation'                => array('type'=>'hasOne', 'load'=>'lazy')
);


/**
 * Class tl_c4gtracking_page
 *
 * Provide miscellaneous methods that are used by the data configuration array.
 * @package   con4gis_tracking
 * @author    Janosch Oltmanns
 * @copyright Janosch Oltmanns in cooperation with Küstenschmiede GmbH Software & Design 2014 - 2015
 */
class tl_c4gtracking_page extends Backend
{

    /**
   	 * Get all trcking-configurations and return them as array
   	 * @return array
   	 */
   	public function getTrackingConfigurations()
   	{
   		/*if (!$this->User->isAdmin && !is_array($this->User->forms))
   		{
   			return array();
   		}*/

   		$arrTrackingConfigurations = array();
   		$objTrackingConfigurations = $this->Database->execute("SELECT id, name FROM tl_c4g_tracking ORDER BY name");

   		while ($objTrackingConfigurations->next())
   		{
   			//if ($this->User->isAdmin || $this->User->hasAccess($objForms->id, 'forms'))
   			//{
            $arrTrackingConfigurations[$objTrackingConfigurations->id] = $objTrackingConfigurations->name . ' (ID ' . $objTrackingConfigurations->id . ')';
   			//}
   		}

   		return $arrTrackingConfigurations;
   	}
}