<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveColumnsToGHLOpportunities extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('ghl_opportunities', 'accountType');
    }

    public function down()
    {
        $fields = [
            'accountType' => [
                'type' => 'ENUM',
                'constraint' => ['funded', 'brokered'],
                'null' => true,
            ]
        ];

        // Modify the ghlOpportunities table
        $this->forge->addColumn('ghl_opportunities', $fields);
    }
}
