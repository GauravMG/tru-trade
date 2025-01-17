<?php

namespace App\Models;

use CodeIgniter\Model;

class GHLOpportunitiesModel extends Model
{
    protected $table            = 'ghl_opportunities';
    protected $primaryKey       = 'ghlOpportunityId';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'ghlOpportunityId',
        'ghlPipelineId',
        'ghlStageId',
        'externalId',
        'name',
        'assignedTo',
        'status',
        'source',
        'lastStatusChangeAt',
        'contactIdExternal',
        'contactNameExternal',
        'contactCompanyNameExternal',
        'contactEmailExternal',
        'contactPhoneExternal',
        'contactTagsExternal',
        'completeJson',
        'createdAt',
        'updatedAt',
        'deletedAt',
        'server',
        'serverCost'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'createdAt';
    protected $updatedField  = 'updatedAt';
    protected $deletedField  = 'deletedAt';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    protected function beforeInsert(array $data)
    {
        $data['data']['createdAt'] = date('Y-m-d H:i:s');
        $data['data']['updatedAt'] = date('Y-m-d H:i:s');
        return $data;
    }

    protected function beforeUpdate(array $data)
    {
        $data['data']['updatedAt'] = date('Y-m-d H:i:s');
        return $data;
    }

    public function getAll()
    {
        // Fetch all
        return $this->findAll();
    }

    public function getOpportunitiesWithLinkedEntities(array $filters = [])
    {
        // Initialize the query with necessary joins
        $builder = $this->select('
                ghl_opportunities.*, 
                ghl_pipelines.name AS pipelineName, 
                ghl_stages.name AS stageName,
                COALESCE(SUM(payments.amount), 0) AS totalPaidAmount
            ')
            ->join('ghl_pipelines', 'ghl_opportunities.ghlPipelineId = ghl_pipelines.ghlPipelineId')
            ->join('ghl_stages', 'ghl_opportunities.ghlStageId = ghl_stages.ghlStageId')
            ->join('payments', 'payments.ghlOpportunityId = ghl_opportunities.ghlOpportunityId AND payments.paymentStatus = "paid"', 'left') // Left join to include leads without payments
            ->groupBy('ghl_opportunities.ghlOpportunityId'); // Group by each lead

        // Apply optional filters if they exist
        if (!empty($filters)) {
            foreach ($filters as $field => $value) {
                // Check if the value is an array, indicating we should use `whereIn`
                if (is_array($value)) {
                    $builder->whereIn($field, $value);
                } else {
                    $builder->where($field, $value);
                }
            }
        }

        // Execute the query and fetch the results
        $query = $builder->findAll();

        // Get field names from models
        $opportunityKeys = $this->allowedFields;
        $pipelineKeys = ['pipelineName'];
        $stageKeys = ['stageName'];

        // Separate opportunity, pipeline, and stage data
        $result = [];
        foreach ($query as $row) {
            $opportunityData = array_intersect_key($row, array_flip($opportunityKeys));
            $pipelineData = array_intersect_key($row, array_flip($pipelineKeys));
            $stageData = array_intersect_key($row, array_flip($stageKeys));
            $totalPaidAmount = $row['totalPaidAmount'];

            $result[] = array_merge(
                $opportunityData,
                ['pipeline' => $pipelineData],
                ['stage' => $stageData],
                ['totalPaidAmount' => $totalPaidAmount] // Add the total paid amount for each lead
            );
        }

        return $result;
    }

    public function getTotalCount(array $filters = [])
    {
        $builder = $this->builder(); // Use the model's builder

        // Apply optional filters
        if (!empty($filters)) {
            foreach ($filters as $field => $value) {
                if (is_array($value)) {
                    $builder->whereIn($field, $value);
                } else {
                    $builder->where($field, $value);
                }
            }
        }

        // Count the total records
        return $builder->countAllResults();
    }
}
