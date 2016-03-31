<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class AbstractSqliteDbCommand extends Command {

    const DB_CONFIG_OPTION_NAME = 'dbconfig';
    const DEFAULT_DB_CONFIG = 'dbtest';

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments() {
        return array(
            array(self::DB_CONFIG_OPTION_NAME, InputArgument::OPTIONAL, 'Database configuration name', self::DEFAULT_DB_CONFIG),
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions() {
        return array();
    }

}