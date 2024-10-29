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

    public function getPastPayments($ghlOpportunityId)
    {
        // Get the current month and year
        $currentMonth = date('F Y');

        return $this->db->table('payments')
            ->select('paymentId, month, amount, paymentStatus, paymentMadeOn')
            ->where('ghlOpportunityId', $ghlOpportunityId)
            ->where('month <', $currentMonth)
            ->orderBy('month', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function getServerCost($ghlOpportunityId)
    {
        return $this->db->table('ghl_opportunities')
            ->select('serverCost')
            ->where('ghlOpportunityId', $ghlOpportunityId)
            ->get()
            ->getRow()->serverCost;
    }

    public function getAccountTotal($ghlOpportunityId)
    {
        return $this->db->table('accounts')
            ->select('SUM(accountCost + COALESCE(quickfundCost, 0)) AS accountTotal')
            ->where('ghlOpportunityId', $ghlOpportunityId)
            ->get()
            ->getRow()
            ->accountTotal;
    }

    public function getAllCurrentMonthPayments($ghlOpportunityId, $currentMonth)
    {
        return $this->db->table('payments')
            ->select('paymentId, month, amount, paymentStatus, paymentMadeOn')
            ->where('ghlOpportunityId', $ghlOpportunityId)
            ->where('month', $currentMonth)
            ->get()
            ->getResultArray();
    }

    public function getCurrentMonthPayment($ghlOpportunityId, $currentMonth)
    {
        return $this->db->table('payments')
            ->select('paymentId, month, amount, paymentStatus, paymentMadeOn')
            ->where('ghlOpportunityId', $ghlOpportunityId)
            ->where('month', $currentMonth)
            ->get()
            ->getRowArray();
    }

    public function getCurrentMonthPaymentByStatus($ghlOpportunityId, $month, $status)
    {
        return $this->db->table('payments')
            ->select('paymentId, amount')
            ->where('ghlOpportunityId', $ghlOpportunityId)
            ->where('month', $month)
            ->where('paymentStatus', $status)
            ->get()
            ->getRowArray();
    }

    public function recordPayment($ghlOpportunityId, $amount, $status, $month)
    {
        return $this->db->table('payments')->insert([
            'ghlOpportunityId' => $ghlOpportunityId,
            'amount' => $amount,
            'paymentStatus' => $status,
            'month' => $month,
            'paymentMadeOn' => date('Y-m-d H:i:s'),
            'createdAt' => date('Y-m-d H:i:s'),
            'updatedAt' => date('Y-m-d H:i:s')
        ]);
    }

    public function getOpportunitiesWithPendingAmounts($branchId = null)
    {
        $builder = $this->db->table('payments')
            ->select('payments.ghlOpportunityId, SUM(payments.amount) AS totalAmountDue')
            ->where('payments.paymentStatus', 'unpaid');

        // Apply branch filter if provided
        if (!empty($branchId)) {
            $builder->join('ghl_opportunities', 'payments.ghlOpportunityId = ghl_opportunities.ghlOpportunityId')
                ->where('ghl_opportunities.ghlPipelineId', $branchId);
        }

        // Group by GHL Opportunity ID to calculate total amount due per opportunity
        return $builder->groupBy('payments.ghlOpportunityId')
            ->get()
            ->getResultArray();
    }

    public function getMonthlyPaidAmountsLast12Months($branchId = null)
    {
        // Define the date range (last 12 months including the current month)
        $startDate = date('Y-m-01', strtotime('-11 months')); // First day of 12 months ago
        $endDate = date('Y-m-t'); // Last day of the current month

        // Initialize the query
        $builder = $this->select("
            DATE_FORMAT(payments.paymentMadeOn, '%Y-%m') AS monthYear, 
            COALESCE(SUM(payments.amount), 0) AS totalPaidAmount
        ")
            ->where('payments.paymentStatus', 'paid')
            ->where('payments.paymentMadeOn >=', $startDate)
            ->where('payments.paymentMadeOn <=', $endDate)
            ->groupBy('payments.month') // Group by month and year
            ->orderBy('payments.paymentMadeOn', 'ASC'); // Sort by ascending month and year

        // Optional filter by branchId if provided
        if ($branchId) {
            $builder->join('ghl_opportunities', 'payments.ghlOpportunityId = ghl_opportunities.ghlOpportunityId')
                ->where('ghl_opportunities.ghlPipelineId', $branchId);
        }

        // Execute the query and fetch results
        $query = $builder->get()->getResultArray();

        // Fill in missing months with 0 totals
        $results = $this->fillMissingMonths($query);

        // Format the results with label and value
        $formattedResults = [];
        foreach ($results as $row) {
            $dateObj = \DateTime::createFromFormat('Y-m', $row['monthYear']);
            $formattedResults[] = [
                'label' => $dateObj->format('F Y'), // Format as "Month Year"
                'value' => (float) $row['totalPaidAmount']
            ];
        }

        return $formattedResults;
    }

    // Helper function to fill in missing months with 0 totals
    private function fillMissingMonths($data)
    {
        $filledData = [];
        $currentMonth = new \DateTime(date('Y-m-01'));
        $interval = new \DateInterval('P1M');
        $currentMonth->sub(new \DateInterval('P11M')); // Go back 11 months to start

        for ($i = 0; $i < 12; $i++) {
            $monthYear = $currentMonth->format('Y-m');
            $found = false;

            // Check if this monthYear exists in the result data
            foreach ($data as $row) {
                if ($row['monthYear'] === $monthYear) {
                    $filledData[] = $row;
                    $found = true;
                    break;
                }
            }

            // If monthYear is missing, add it with 0 total
            if (!$found) {
                $filledData[] = [
                    'monthYear' => $monthYear,
                    'totalPaidAmount' => 0
                ];
            }

            $currentMonth->add($interval); // Move to the next month
        }

        return $filledData;
    }
}
