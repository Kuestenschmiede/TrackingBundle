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

/**
 * Table tl_page
 */

Contao\CoreBundle\DataContainer\PaletteManipulator::create()
    ->addLegend('c4gtracking_legend', 'layout_legend')
    ->addField('c4gtracking_configuration', 'c4gtracking_legend', Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('root', 'tl_page')
    ->applyToPalette('rootfallback', 'tl_page');


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
 * @copyright Janosch Oltmanns in cooperation with Küstenschmiede GmbH Software & Design 2011 - 2018
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