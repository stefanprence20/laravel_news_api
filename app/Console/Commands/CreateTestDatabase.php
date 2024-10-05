<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateTestDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-test-database {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to create a new test database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $host = env('DB_HOST', '127.0.0.1');
        $password = env('DB_PASSWORD');

        try {
            $db_name = $this->argument('name');
            $pdo = new \PDO("mysql:host={$host}", 'root', $password);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$db_name}`");
            $this->info("Database '{$db_name}' created.");
        } catch (\PDOException $e) {
            $this->error("Could not create database: " . $e->getMessage());
        }
    }
}
