<?php

use Illuminate\Support\Facades\Artisan;

class DbIntegrationTestCase extends IntegrationTestCase {

    public static function setupDatabase() {
        Artisan::call(SqliteCreateDbCommand::COMMAND_NAME);
        Artisan::call('migrate');
    }

    public static function seedDatabase() {
        Artisan::call('db:seed');
    }

    public static function tearDownDatabase() {
        Artisan::call(SqliteDeleteDbCommand::COMMAND_NAME);
    }
}
