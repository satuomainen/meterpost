<?php

class DataseriesSeeder extends Seeder {

    const SAMPLE_READINGS_TO_GENERATE = 200;
    const SAMPLE_READING_MIN = 0;
    const SAMPLE_READING_MAX = 100;

    /**
     * @var DataseriesService $dataseriesService
     */
    private $dataseriesService;

    /**
     * Sample data series that can be created with `php artisan db:seed`
     *
     * @var array $sampleDataSeries
     */
    private $sampleDataSeries = array(
        array(
            "name" => "Murkiness",
            "description" => "Resistance to the passage of light",
            "api_key" => "c3365fc720c57f5ef987022b716b2957"
        ),
        array(
            "name" => "Enjoyment",
            "description" => "Human joy at a task",
            "api_key" => "c3365fc720c57f5ef987022b716b2957"
        ),
        array(
            "name" => "Widthitude",
            "description" => "Disposition of girth",
            "api_key" => "c3365fc720c57f5ef987022b716b2957"
        ),
        array(
            "name" => "Amplification",
            "description" => "Change in enjoyment as a function of widthitude",
            "api_key" => "c3365fc720c57f5ef987022b716b2957"
        )
    );

    public function __construct(DataseriesService $dataseriesService) {
        $this->dataseriesService = $dataseriesService;
    }

    public function run() {
        foreach ($this->sampleDataSeries as $dataseries) {
            $this->createSeries($dataseries);
        }
    }

    private function createSeries($dataseries) {
        $dataseries = Dataseries::create(array(
            "name" => $dataseries['name'],
            "description" => $dataseries['description'],
            "api_key" => $dataseries['api_key']
        ));

        $this->createReadingsForDataseries($dataseries->id);
    }

    private function createReadingsForDataseries($dataseriesId) {
        $creationTime = new DateTime('now');
        for ($i = 0; $i < DataseriesSeeder::SAMPLE_READINGS_TO_GENERATE; $i++) {
            $createdAt = $creationTime->format('Y-m-d H:i:s');
            Reading::create(array(
                "value" => rand(DataseriesSeeder::SAMPLE_READING_MIN, DataseriesSeeder::SAMPLE_READING_MAX),
                "dataseries_id" => $dataseriesId,
                "created_at" => $createdAt,
                "updated_at" => $createdAt
            ));
            $creationTime->modify('+5 minute');
        }
    }
}