<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnsToUsers extends Migration
{
    public function up()
    {
        $fields = [
            'branchId' => [
                'type' => 'INT',
            ],
        ];

        // Modify the users table
        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        // Drop the columns if the migration is rolled back
        $this->forge->dropColumn('users', 'branchId');
    }
}
