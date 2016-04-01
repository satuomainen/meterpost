<?php

use Symfony\Component\HttpFoundation\Response;

class ReadingResourceTest extends DbIntegrationTestCase {

    public function setUp() {
        parent::setUp();

        parent::setupDatabase();
        parent::seedDatabase();
    }

    public function tearDown() {
        parent::tearDown();

        parent::tearDownDatabase();
    }

    public function testReadingsAreReturnedForExistingDataseries() {
        $dataseries = $this->getRandomDataseries();

        $crawler = $this->client->request('GET', '/dataseries/' . $dataseries->id . '/reading');

        $this->assertTrue($this->client->getResponse()->isOk(), "Response code should be OK");

        $data = json_decode($this->client->getResponse()->getContent());

        $this->assertObjectHasAttribute('dataseriesId', $data);
        $this->assertAttributeEquals($dataseries->id, 'dataseriesId', $data);
        $this->assertObjectHasAttribute('readings', $data);
        $this->assertCount(DataseriesSeeder::SAMPLE_READINGS_TO_GENERATE, $data->readings);

        $reading = $data->readings[0];
        $this->assertObjectHasAttribute('x', $reading);
        $this->assertObjectHasAttribute('y', $reading);
    }

    public function testDataseriesReadingsHandleNotFound() {
        $crawler = $this->client->request('GET', '/dataseries/99999999/reading');
        $this->assertTrue($this->client->getResponse()->isNotFound());
    }

    public function testDataseriesAveragesHandleNotFound() {
        $crawler = $this->client->request('GET', '/dataseries/99999999/reading/averages');
        $this->assertTrue($this->client->getResponse()->isNotFound());
    }

    public function testCsvDownload() {
        $dataseries = $this->getRandomDataseries();

        $crawler = $this->client->request('GET', '/dataseries/' . $dataseries->id . '/reading/csv');
        $this->assertTrue($this->client->getResponse()->isOk(), "Response code should be OK");
        $this->assertTrue(str_contains($this->client->getResponse()->headers->get('content-disposition'), $dataseries->name));
    }

    public function testCsvDownloadHandleNotFound() {
        $crawler = $this->client->request('GET', '/dataseries/99999999/reading/csv');
        $this->assertTrue($this->client->getResponse()->isNotFound());
    }

    public function testPostNewReadingSuccess() {
        $dataseries = $this->getRandomDataseries();
        $dataseriesIdColumnName = DataseriesService::getForeignKeyColumnName(Dataseries::TABLE_NAME);

        $existingReadings = DB::table(Reading::TABLE_NAME)
            ->addSelect('id')
            ->where($dataseriesIdColumnName, '=', $dataseries->id)
            ->count();

        $newReading = $this->createSampleReading($dataseries->api_key);

        $crawler = $this->client->request('POST', '/dataseries/' . $dataseries->id . '/reading', $newReading);

        $this->assertTrue($this->client->getResponse()->isOk());
        
        $newReadings = DB::table(Reading::TABLE_NAME)
            ->addSelect('id')
            ->where($dataseriesIdColumnName, '=', $dataseries->id)
            ->count();

        $this->assertEquals($existingReadings + 1, $newReadings);
    }

    public function testPostNewReadingDataseriesNotFound() {
        $newReading = $this->createSampleReading('asdfgh');
        $crawler = $this->client->request('POST', '/dataseries/99999999/reading', $newReading);

        $this->assertTrue($this->client->getResponse()->isNotFound());
    }

    public function testPostNewReadingValueMissing() {
        $dataseries = $this->getRandomDataseries();
        $dataseriesIdColumnName = DataseriesService::getForeignKeyColumnName(Dataseries::TABLE_NAME);
        $newReading = array('api_key' => $dataseries->api_key);
        $crawler = $this->client->request('POST', '/dataseries/' . $dataseries->id . '/reading', $newReading);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function testPostNewReadingApikeyMissing() {
        $dataseries = $this->getRandomDataseries();
        $newReading = array('value' => '66');
        $crawler = $this->client->request('POST', '/dataseries/' . $dataseries->id . '/reading', $newReading);
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function testPostNewReadingIncorrectApikey() {
        $dataseries = $this->getRandomDataseries();
        $newReading = array('api_key' => 'asdfg', 'value' => '77');
        $crawler = $this->client->request('POST', '/dataseries/' . $dataseries->id . '/reading', $newReading);
        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
    }

    private function getRandomDataseries() {
        $allDataseries = Dataseries::all();
        if (count($allDataseries) > 0) {
            return $allDataseries[0];
        }
        throw new UnexpectedValueException("No dataseries are available. Has the database been seeded for tests?");
    }

    public function testPostNewReadingToLegacyApiSuccess() {
        $dataseries = $this->getRandomDataseries();
        $dataseriesIdColumnName = DataseriesService::getForeignKeyColumnName(Dataseries::TABLE_NAME);

        $existingReadings = DB::table(Reading::TABLE_NAME)
            ->addSelect('id')
            ->where($dataseriesIdColumnName, '=', $dataseries->id)
            ->count();

        $newReading = $this->createSampleReading($dataseries->api_key);

        $crawler = $this->client->request('POST', '/series/' . $dataseries->id . '/add', $newReading);

        $this->assertTrue($this->client->getResponse()->isOk());

        $newReadings = DB::table(Reading::TABLE_NAME)
            ->addSelect('id')
            ->where($dataseriesIdColumnName, '=', $dataseries->id)
            ->count();

        $this->assertEquals($existingReadings + 1, $newReadings);
    }

    private function createSampleReading($apikey) {
        return array(
            'api_key' => $apikey,
            'value' => '50'
        );
    }
}
