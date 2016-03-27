<?php

use Illuminate\Support\Facades\Input;
use Symfony\Component\HttpFoundation\Response;

class ReadingController extends BaseController {

    /**
     * @var ApiKeyService $apikeyService
     */
    protected $apikeyService;

    /**
     * @var DataseriesService $dataseriesService
     */
    protected $dataseriesService;

    public function __construct(ApiKeyService $apikeyService, DataseriesService $dataseriesService) {
        $this->apikeyService = $apikeyService;
        $this->dataseriesService = $dataseriesService;
    }

    /**
     * List readings for the given dataseries as json, @see DataseriesService::findReadings
     *
     * @return Response
     */
    public function index($dataseriesId) {
        if (!$this->dataseriesService->dataseriesExists($dataseriesId)) {
            return ResponseHelper::notFoundResponse();
        }

        $readings = $this->dataseriesService->findReadings($dataseriesId);

        return $readings;
    }

    /**
     * List daily averages for the given dataseries as json, @see DataseriesService::getDataseriesAverages
     *
     * @param $dataseriesId
     * @return Response
     */
    public function getDataseriesAverages($dataseriesId) {
        if (!$this->dataseriesService->dataseriesExists($dataseriesId)) {
            return ResponseHelper::notFoundResponse();
        }

        $readings = $this->dataseriesService->getDataseriesAverages($dataseriesId);

        return $readings;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        return ResponseHelper::notImplementedResponse();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store($dataseriesId) {
        if (!$this->dataseriesService->dataseriesExists($dataseriesId)) {
            return ResponseHelper::notFoundResponse();
        }

        $validator = $this->getStoreReadingValidator(Input::all());
        if ($validator->fails()) {
            return ResponseHelper::badRequestResponse();
        }

        if (!$this->apikeyService->validate($dataseriesId, Input::get('api_key'))) {
            return ResponseHelper::unauthorizedResponse();
        }

        $readingValue = Input::get('value');

        $this->dataseriesService->addReading($dataseriesId, $readingValue);

        return ResponseHelper::okResponse();
    }

    /**
     * Display the specified resource.
     *
     * @param $dataseriesId
     * @param $readingId
     * @return Response
     * @internal param int $id
     */
    public function show($dataseriesId, $readingId) {
        ResponseHelper::notImplementedResponse();
    }

    /**
     * Return a CSV representation of the readings in the given dataseries.
     *
     * @param $dataseriesId
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function getDataseriesAsCsv($dataseriesId) {
        $dataseries = Dataseries::find($dataseriesId);
        if (is_null($dataseries)) {
            return ResponseHelper::notFoundResponse();
        }

        $headings = array(Lang::get('messages.reading.time'), Lang::get('messages.value'));
        $readings = $this->dataseriesService->getDataseriesReadings($dataseriesId);
        $downloadFilename = $this->getDownloadFilename($dataseries->name);

        return CsvResponse::asCsv($headings, $readings, $downloadFilename);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id) {
        return ResponseHelper::notImplementedResponse();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update($id) {
        return ResponseHelper::notImplementedResponse();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id) {
        return ResponseHelper::notImplementedResponse();
    }

    private function getStoreReadingValidator($values) {
        $rules = array(
            'value' => 'required',
            'api_key' => 'required'
        );
        return Validator::make($values, $rules);
    }

    private function getDownloadFilename($filename, $extension = ".csv") {
        $filename = iconv('UTF-8', 'ASCII//TRANSLIT', $filename);
        $filename = preg_replace("/[^a-zA-Z0-9\/_| -]/", '', $filename) . $extension;
        return $filename;
    }
}
