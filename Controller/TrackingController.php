<?php
/**
 * Created by PhpStorm.
 * User: cro
 * Date: 15.09.17
 * Time: 11:38
 */

namespace con4gis\TrackingBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use con4gis\TrackingBundle\Resources\contao\classes\TrackingService;

class TrackingController extends Controller
{

    public function trackingAction(Request $request) {

        $trackingService = new TrackingService();

        return JsonResponse::create($trackingService->generate());


    }


}