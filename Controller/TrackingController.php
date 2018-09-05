<?php
/**
 * Created by PhpStorm.
 * User: cro
 * Date: 15.09.17
 * Time: 11:38
 */

namespace con4gis\TrackingBundle\Controller;


use Contao\Input;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use con4gis\TrackingBundle\Resources\contao\classes\TrackingService;

class TrackingController extends Controller
{

    public function trackingAction(Request $request, $methodString)
    {
        /**
         * Begin Debugging
         */
        $debugData = [];
        $arrParams = array('api_key','date','imei','latitude','longitude','phoneNo','speed','mileage','driverId','temperature','status');
        foreach ($arrParams as $param) {
            $debugData[$param] = Input::post($param);
            if (!$debugData[$param]) {
                $debugData[$param] = Input::get($param);
            }
        }
        $this->get('logger')->error($request->request->all());

        /**
         * End Debugging
         */

        $trackingService = new TrackingService();
        return JsonResponse::create($trackingService->generate($methodString));
    }

    public function getLiveAction(Request $request)
    {
        $trackingService = new TrackingService();
        return JsonResponse::create($trackingService->generate("getLive"));
    }


}