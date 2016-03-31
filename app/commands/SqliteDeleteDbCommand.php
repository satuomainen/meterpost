<?php

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Console\Input\InputArgument;

class SqliteDeleteDbCommand extends AbstractSqliteDbCommand {

    const COMMAND_NAME = 'sqlite:delete';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = self::COMMAND_NAME;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete the sqlite database file if exists';

    /**
     * Create a new command instance.
     *
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire() {
        $dbconfig = $this->argument(self::DB_CONFIG_OPTION_NAME);
        $path = storage_path($dbconfig . '.sqlite');
        if (file_exists($path) && is_dir(dirname($path))) {
            unlink($path);
        }
    }
}
