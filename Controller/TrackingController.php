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
namespace con4gis\TrackingBundle\Controller;


use Contao\Input;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use con4gis\TrackingBundle\Classes\TrackingService;

class TrackingController extends Controller
{

    public function trackingAction(Request $request, $methodString)
    {
        $debugData = [];
        $arrParams = array('api_key','date','imei','latitude','longitude','phoneNo','speed','mileage','driverId','temperature','status');
        foreach ($arrParams as $param) {
            $debugData[$param] = Input::post($param);
            if (!$debugData[$param]) {
                $debugData[$param] = Input::get($param);
            }
        }
        $trackingService = new TrackingService();
        return JsonResponse::create($trackingService->generate($methodString));
    }

    public function trackingLegacyAction(Request $request)
    {
        $trackingService = new TrackingService();
        return JsonResponse::create($trackingService->generate(""));
    }

    public function getLiveAction(Request $request)
    {
        $trackingService = new TrackingService();
        return JsonResponse::create($trackingService->generate("getLive"));
    }


}