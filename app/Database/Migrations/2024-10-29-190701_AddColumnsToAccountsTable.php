<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnsToAccountsTable extends Migration
{
    public function up()
    {
        $fields = [
            'isQuickfundAccount' => [
                'type' => 'ENUM',
                'constraint' => ['yes', 'no'],
                'null' => false,
                'default' => 'no',
            ],
            'quickfundCost' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',  // 10 total digits, 2 decimal places
                'null' => true,
            ],
        ];

        $this->forge->addColumn('accounts', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('accounts', 'isQuickfundAccount');
        $this->forge->dropColumn('accounts', 'quickfundCost');
    }
}
