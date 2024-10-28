<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterColumnsToAccounts extends Migration
{
    public function up()
    {
        $fields = [
            'serverType' => [
                'type' => 'ENUM',
                'constraint' => ['a', 'b'],
                'null' => false,
            ],
            'accountType' => [
                'type' => 'ENUM',
                'constraint' => ['funded', 'brokered'],
                'null' => false,
            ],
        ];

        $this->forge->addColumn('accounts', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('accounts', 'serverType');
        $this->forge->dropColumn('accounts', 'accountType');
    }
}
