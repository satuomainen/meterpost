<?php


class DashboardController extends BaseController {

    /**
     * @var DataseriesService $dataseriesService
     */
    private $dataseriesService;

    public function __construct(DataseriesService $dataseriesService) {
        $this->dataseriesService = $dataseriesService;
    }

    /**
     * Show the front page
     *
     * @return mixed
     */
    public function showDashboard() {
        $model = array(
            "dashboardSummaries" => $this->dataseriesService->getAllDataseriesSummaries()
        );
        return View::make('dashboard', $model);
    }
}
