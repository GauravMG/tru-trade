<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentsModel extends Model
{
    protected $table            = 'payments';
    protected $primaryKey       = 'paymentId';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'paymentId',
        'ghlOpportunityId',
        'month',
        'amount',
        'paymentStatus',
        'paymentMadeOn',
        'createdAt',
        'updatedAt',
        'deletedAt',
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

    public function getTotalCount(array $filters = [], array $groupByFields = [], $ghlPipelineId = null)
    {
        $builder = $this->table("payments");
        $builder->select("payments.*");

        // Join with ghl_opportunities table if ghlPipelineId is provided
        if ($ghlPipelineId !== null) {
            $builder->join('ghl_opportunities', 'payments.ghlOpportunityId = ghl_opportunities.ghlOpportunityId')
                    ->where('ghl_opportunities.ghlPipelineId', $ghlPipelineId);
        }

        // Apply other optional filters
        if (!empty($filters)) {
            foreach ($filters as $field => $value) {
                if (is_array($value)) {
                    $builder->whereIn($field, $value);
                } else {
                    $builder->where($field, $value);
                }
            }
        }

        // Apply optional group by fields
        if (!empty($groupByFields)) {
            foreach ($groupByFields as $field) {
                $builder->groupBy($field);
            }
        }

        // Count the total records
        return $builder->countAllResults();
    }

    public function getPaidAmountByMonth($month = null, $ghlPipelineId = null)
    {
        $builder = $this->where('paymentStatus', 'paid');

        // Apply month filter only if provided
        if ($month !== null) {
            $builder->where('month', $month);
        }

        // Apply ghlPipelineId filter if provided
        if ($ghlPipelineId !== null) {
            $builder->join('ghl_opportunities', 'payments.ghlOpportunityId = ghl_opportunities.ghlOpportunityId')
                    ->where('ghl_opportunities.ghlPipelineId', $ghlPipelineId);
        }

        // Sum the 'amount' field and retrieve the result
        $result = $builder->selectSum('amount')->first();

        // Return the amount, defaulting to 0 if null
        return ['amount' => $result['amount'] ?? 0];
    }


}
