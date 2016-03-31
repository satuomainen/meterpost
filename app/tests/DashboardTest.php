<?php

class DashboardTest extends DbIntegrationTestCase {

    /**
     * Run every time before test starts
     */
    public function setUp() {
        parent::setUp();
        parent::setupDatabase();
        $this->seed('DatabaseSeeder');
    }

    /**
     * Run every time after test has completed
     */
    public function tearDown() {
        parent::tearDown();
        parent::tearDownDatabase();
    }

    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testBasicExample() {
        $crawler = $this->client->request('GET', '/');
        $numGauges = count($crawler->filter('.dataseries-gauge-container'));
        $this->assertGreaterThan(0, $numGauges, "Front page should have gauges");
        $this->assertTrue($this->client->getResponse()->isOk());
    }
}
