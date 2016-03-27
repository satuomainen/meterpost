<?php

class DataseriesController extends BaseController {

    /**
     * @var DataseriesService $dataseriesService
     */
    private $dataseriesService;

    public function __construct(DataseriesService $dataseriesService) {
        $this->dataseriesService = $dataseriesService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        return ResponseHelper::notImplementedResponse();
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
    public function store() {
        return ResponseHelper::notImplementedResponse();
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id) {
        if (!$this->dataseriesService->dataseriesExists($id)) {
            return ResponseHelper::notFoundResponse();
        }

        $model = array(
            "dataseries" => $this->dataseriesService->getDataseries($id)
        );

        return View::make('dataseries', $model);
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
}
