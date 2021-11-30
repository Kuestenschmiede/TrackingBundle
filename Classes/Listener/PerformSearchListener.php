<?php

namespace con4gis\TrackingBundle\Classes\Listener;

use con4gis\MapsBundle\Classes\Events\PerformSearchEvent;
use con4gis\MapsBundle\Resources\contao\models\C4gMapProfilesModel;
use con4gis\MapsBundle\Resources\contao\modules\api\SearchApi;
use Contao\Database;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\RequestStack;

class PerformSearchListener
{
    private $Database;

    /**
     * LayerContentService constructor.
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
        $this->Database = Database::getInstance();
    }
    public function onPerformSearchDoIt(
        PerformSearchEvent $event,
        $eventName,
        EventDispatcher $eventDispatcher
    ) {
        $profileId = $event->getProfileId();
        $arrParams = $event->getArrParams();
        $response = $event->getResponse();
        $profile = C4gMapProfilesModel::findByPk($profileId);
        if ($profile && $profile->ownGeosearch) {
            $sql = "SELECT pos.latitude AS geoy, pos.longitude AS geox, dev.name AS name, IF (dev.name LIKE ?, 42, 0) AS weight FROM tl_c4g_tracking_devices AS dev INNER JOIN tl_c4g_tracking_positions as pos ON dev.lastPositionId = pos.id WHERE (dev.name LIKE ?) ORDER BY weight DESC";
            $searchString = $arrParams['q'];
            $arrTerms = explode(' ', $searchString);
            $strSearch = '%';
            foreach ($arrTerms as $term) {
                $strSearch .= $term . '%';
            }
            $request = $this->Database->prepare($sql);
            $arrDBResult = $request->execute([$strSearch, $strSearch])->fetchAllAssoc();
            $arrResults = [];
            foreach ($arrDBResult as $dBResult) {
                $arrResults[] = [
                    'lat'           => $dBResult['geoy'],
                    'lon'           => $dBResult['geox'],
                    'display_name'  => $dBResult['name']
                ];
            }
            $arrResults = array_merge($arrResults, $response ?: []);
            $arrResults = array_slice($arrResults, 0, $arrParams['limit'] ?: 10);
            $event->setResponse($arrResults);
        }
    }
}
