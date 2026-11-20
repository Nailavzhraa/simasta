<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckSimastaDatabase extends Command
{
    protected $signature = 'simasta:check-database';

    protected $description = 'Verify PostgreSQL database and schema before applying SIMASTA migrations';

    public function handle(): int
    {
        $connection = DB::connection();
        if ($connection->getDriverName() !== 'pgsql') {
            $this->error('Deployment requires PostgreSQL. No database changes were made.');
            return self::FAILURE;
        }

        $database = $connection->selectOne('SELECT current_database() AS database, current_schema() AS schema');
        if ($database->database !== 'db_magang' || $database->schema !== 'simasta') {
            $this->error('Expected db_magang.simasta. Check DB_DATABASE, DB_SCHEMA, and DB_URL before migrating.');
            return self::FAILURE;
        }

        $this->info('Connected to db_magang.simasta.');
        return self::SUCCESS;
    }
}
