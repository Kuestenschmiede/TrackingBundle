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
 * Run in a custom namespace, so the class can be replaced
 */
namespace con4gis\TrackingBundle\Resources\contao\models;


use Contao\Model;

/**
 * Class C4gTrackingTracksModel
 * @package c4g
 */
class C4gTrackingTracksModel extends Model
{

	/**
	 * Table name
	 * @var string
	 */
	protected static $strTable = 'tl_c4g_tracking_tracks';

    public static function findWithPositions(array $arrMemberIds=array(), array $arrVisibilityStatus=array())
    {

      $arrWhere = array();
      if (sizeof($arrMemberIds) > 0)
      {
        $arrWhere[] = "tl_c4g_tracking_tracks.member IN(" . implode(",", $arrMemberIds) . ")";
      }

      if (sizeof($arrVisibilityStatus) > 0)
      {
        $arrWhere[] = "(tl_c4g_tracking_tracks.visibility='" . implode("' OR tl_c4g_tracking_tracks.visibility='", $arrVisibilityStatus) . "')";
      }

      $strWhere = "";
      if (sizeof($arrWhere) > 0)
      {
        $strWhere = " WHERE " . implode(" AND ", $arrWhere) . " AND forDelete!=1";
      }
      else
      {
        $strWhere = " WHERE forDelete!=1";
      }


   		$objDatabase = \Database::getInstance();

   		$objResult = $objDatabase->execute("SELECT tl_c4g_tracking_tracks.* FROM tl_c4g_tracking_tracks LEFT JOIN tl_c4g_tracking_positions ON tl_c4g_tracking_tracks.uuid=tl_c4g_tracking_positions.trackUuid" . $strWhere . " GROUP BY tl_c4g_tracking_tracks.id");
   		//echo $objResult->__get('query');
   		return static::createCollectionFromDbResult($objResult, 'tl_c4g_tracking_tracks');

    }
}



