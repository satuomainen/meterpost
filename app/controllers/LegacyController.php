<?php

use Symfony\Component\HttpFoundation\Response;

class LegacyController extends BaseController {

    /**
     * Support for receiving readings to legacy end point
     * POST /series/{dataseriesId}/add
     *
     * @param $dataseriesId
     * @return mixed
     */
    public function storeReading($dataseriesId) {
        $parameters = array($dataseriesId);

        $controller = app()->make('ReadingController');
        return $controller->callAction('store', $parameters);
    }
}
