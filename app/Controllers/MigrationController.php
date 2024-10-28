<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class MigrationController extends Controller
{
    public function migrate()
    {
        $migrate = \Config\Services::migrations();

        try {
            $migrate->latest();
            echo "Migrations have been run successfully.";
        } catch (\Throwable $e) {
            echo "Migration failed: " . $e->getMessage();
        }
    }

    public function seed($seederName)
    {
        $seeder = \Config\Database::seeder();

        try {
            $seeder->call($seederName);
            echo "Seeder $seederName has been run successfully.";
        } catch (\Throwable $e) {
            echo "Seeding failed: " . $e->getMessage();
        }
    }
}
