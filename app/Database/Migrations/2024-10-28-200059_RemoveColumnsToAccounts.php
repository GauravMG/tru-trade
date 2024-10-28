<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveColumnsToAccounts extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('accounts', 'systemType');
    }

    public function down()
    {
        $fields = [
            'systemType' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
        ];

        $this->forge->addColumn('accounts', $fields);
    }
}
