<?php

/**
 * con4gis - the gis-kit
 *
 * @version   php 5
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Janosch Oltmanns in cooperation with Küstenschmiede GmbH Software & Design 2011 - 2018
 * @link      https://www.kuestenschmiede.de
 */


/**
 * Run in a custom namespace, so the class can be replaced
 */
namespace con4gis\TrackingBundle\Resources\contao\models;


/**
 * Reads and writes news archives
 *
 * @package   con4gis_tracking
 * @author    Janosch Oltmanns
 * @copyright Janosch Oltmanns in cooperation with Küstenschmiede GmbH Software & Design 2011 - 2018
 */
class C4gTrackingDevicesModel extends \Model
{

	/**
	 * Table name
	 * @var string
	 */
	protected static $strTable = 'tl_c4g_tracking_devices';

	public static function findMultipleByIds($arrIds, array $arrOptions=array())
	{
		if (!is_array($arrIds) || empty($arrIds))
		{
			return null;
		}
		$arrIds = implode(',', array_map('intval', $arrIds));
		$t = static::$strTable;
		$db = \Database::getInstance();
		return static::findBy
		(
			array("$t.id IN(" . $arrIds . ")"),
			null,
			array('order' => $db->findInSet("$t.id", $arrIds))
		);
	}

	public static function findByImeiEndpiece($strImei)
	{
		if (empty($strImei))
		{
			return null;
		}

		$t = static::$strTable;

		return static::findOneBy(
			array("$t.imei LIKE ? OR SUBSTRING(?, -6)=$t.imei"), array('%' . $strImei, $strImei));
	}

}


