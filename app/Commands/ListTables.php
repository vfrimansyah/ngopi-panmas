<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ListTables extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:list-tables';
    protected $description = 'List all database tables';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        $tables = $db->listTables();
        CLI::write("Tables in database:");
        foreach ($tables as $table) {
            CLI::write(" - " . $table);
        }
    }
}
