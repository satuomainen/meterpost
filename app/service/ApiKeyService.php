<?php

class ApiKeyService {
    
    const SERVICE_NAME = "apikeyService";

    /**
     * Validates that the provided API key matches the API key stored with the data series
     *
     * @param $dataseriesId Integer, Dataseries primary key
     * @param $apiKey String, the API key to be checked against the dataseries with id $dataseriesId
     * @return bool
     */
    public function validate($dataseriesId, $apiKey) {
        $dataseries = Dataseries::find($dataseriesId);
        if (is_null($dataseries) || is_array($dataseries)) {
            return false;
        }
        return strcmp($apiKey, $dataseries->api_key) === 0;
    }
}
