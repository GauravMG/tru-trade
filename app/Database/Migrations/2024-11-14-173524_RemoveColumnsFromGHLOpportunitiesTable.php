<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveColumnsFromGHLOpportunitiesTable extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('ghl_opportunities', 'contractLink');
    }

    public function down()
    {
        $fields = [
            'contractLink' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ];

        $this->forge->addColumn('ghl_opportunities', $fields);
    }
}
