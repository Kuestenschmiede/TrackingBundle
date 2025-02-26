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
 * Class C4gTrackingPoisModel
 * @package c4g
 */
class C4gTrackingPoisModel extends Model
{

	/**
	 * Table name
	 * @var string
	 */
	protected static $strTable = 'tl_c4g_tracking_pois';


    public static function findWithMagic(array $arrMemberIds=array(), array $arrVisibilityStatus=array())
    {
      $t = static::$strTable;
      $arrWhere = array();
      if (sizeof($arrMemberIds) > 0)
      {
        $arrWhere[] = "$t.member IN(" . implode(",", $arrMemberIds) . ")";
      }

      if (sizeof($arrVisibilityStatus) > 0)
      {
        $arrWhere[] = "($t.visibility='" . implode("' OR $t.visibility='", $arrVisibilityStatus) . "')";
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

   		$objResult = $objDatabase->execute("SELECT $t.* FROM $t" . $strWhere . "");
   		return static::createCollectionFromDbResult($objResult, $t);

    }

    public static function findPrivate($varMemberId, array $arrOptions=array())
   	{
   		$t = static::$strTable;

        $arrColumns = array("$t.visibility=? AND $t.member=?");
        $arrValues = array("privat", $varMemberId);

   		return static::findBy($arrColumns, $arrValues, $arrOptions);
   	}

    public static function findPublic(array $arrOptions=array())
   	{
   		$t = static::$strTable;

        $arrColumns = array("$t.visibility=?");
        $arrValues = array("public");

   		return static::findBy($arrColumns, $arrValues, $arrOptions);
   	}

}



