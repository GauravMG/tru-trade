<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnsToGHLOpportunities extends Migration
{
    public function up()
    {
        $fields = [
            'accountType' => [
                'type' => 'ENUM',
                'constraint' => ['funded', 'brokered'],
                'null' => true,
            ],
            'server' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'serverCost' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2', // Adjust the precision and scale as needed
                'null' => true,
            ],
        ];

        // Modify the ghlOpportunities table
        $this->forge->addColumn('ghl_opportunities', $fields);
    }

    public function down()
    {
        // Drop the columns if the migration is rolled back
        $this->forge->dropColumn('ghl_opportunities', 'accountType');
        $this->forge->dropColumn('ghl_opportunities', 'server');
        $this->forge->dropColumn('ghl_opportunities', 'serverCost');
    }
}
