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


use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\Input;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use con4gis\TrackingBundle\Classes\TrackingService;

class TrackingController extends AbstractController
{

    private LoggerInterface $logger;

    public function __construct(ContaoFramework $framework, LoggerInterface $logger)
    {
        $framework->initialize();
        $this->logger = $logger;
    }

    public function trackingAction(Request $request)
    {
        try {
            $debugData = [];
            $arrParams = array('api_key','date','imei','latitude','longitude','phoneNo','speed','mileage','driverId','temperature','status');
            foreach ($arrParams as $param) {
                $debugData[$param] = Input::post($param);
                if (!$debugData[$param]) {
                    $debugData[$param] = Input::get($param);
                }
            }
            $query = $request->query->all();
    //        return new Response($query['method']);

            $trackingService = new TrackingService();


            $response = $trackingService->generate($query['method']);
        } catch (\Throwable $exception) {
            $this->logger->error($exception->getMessage());
        }
//        $response = $trackingService->generate("");
//        $response = \GuzzleHttp\json_decode($response, true);
        return JsonResponse::create($response);
    }

    public function trackingActionNewPositionFromBox(Request $request)
    {
        try {
            $debugData = [];
            $arrParams = array('api_key','date','imei','latitude','longitude','phoneNo','speed','mileage','driverId','temperature','status');
            foreach ($arrParams as $param) {
                $debugData[$param] = Input::post($param);
                if (!$debugData[$param]) {
                    $debugData[$param] = Input::get($param);
                }
            }
            $query = $request->query->all();
            //        return new Response($query['method']);

            $trackingService = new TrackingService();


            $response = $trackingService->generate("newPositionFromBox");
        } catch (\Throwable $exception) {
            $this->logger->error($exception->getMessage());
        }
//        $response = $trackingService->generate("");
//        $response = \GuzzleHttp\json_decode($response, true);
        return JsonResponse::create($response);
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