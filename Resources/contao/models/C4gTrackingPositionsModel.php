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
 * Class C4gTrackingPositionsModel
 * @package c4g
 */
class C4gTrackingPositionsModel extends Model
{

	/**
	 * Table name
	 * @var string
	 */
	protected static $strTable = 'tl_c4g_tracking_positions';

}



