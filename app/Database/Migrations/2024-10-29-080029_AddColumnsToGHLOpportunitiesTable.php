<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnsToGHLOpportunitiesTable extends Migration
{
    public function up()
    {
        $fields = [
            'contractLink' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ];

        $this->forge->addColumn('ghl_opportunities', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('ghl_opportunities', 'contractLink');
    }
}
