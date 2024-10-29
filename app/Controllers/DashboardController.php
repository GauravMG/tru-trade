<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\GHLOpportunitiesModel;
use App\Models\AccountsModel;
use App\Models\PaymentsModel;

class DashboardController extends Controller
{
    public function index()
    {
        $session = session();
        if (empty($session->get('userId'))) {
            return redirect()->to('/login');
        }
        $branchId = $session->get('branchId');

        $ghlOpportunitiesModel = new GHLOpportunitiesModel();
        $paymentsModel = new PaymentsModel();

        $filters['ghl_opportunities.status'] = ["open", "won"];

        if (!empty($branchId)) {
            $filters['ghl_opportunities.ghlPipelineId'] = $branchId;
        }

        $totalLeadCount = $ghlOpportunitiesModel->getTotalCount($filters);

        $filtersPaidAccounts = [
            'payments.month' => date('F Y'),
            'payments.paymentStatus' => "paid"
        ];
        $groupByFieldsPaidAccounts = ['payments.ghlOpportunityId'];

        $totalPaidAccounts = $paymentsModel->getTotalCount($filtersPaidAccounts, $groupByFieldsPaidAccounts, $branchId);

        $totalUnpaidAccounts = $totalLeadCount - $totalPaidAccounts;

        // Fetch opportunities with pending amounts
        $dueAmountAccounts = $paymentsModel->getOpportunitiesWithPendingAmounts($branchId);
        $totalPaidAccountsWithDueAmount = count($dueAmountAccounts ?? 0);
        $totalAmountDue = 0;
        foreach ($dueAmountAccounts as $item) {
            $totalAmountDue += $item['totalAmountDue'];
        }

        $totalEarnings = 0;
        $resultTotalEarnings = $paymentsModel->getPaidAmountByMonth(null, $branchId);
        if ($resultTotalEarnings) {
            $totalEarnings = $resultTotalEarnings['amount']; // This will contain the sum of the amounts
        }

        $totalEarningsThisMonth = 0;
        $resultTotalEarningsThisMonth = $paymentsModel->getPaidAmountByMonth(date('F Y'), $branchId);
        if ($resultTotalEarningsThisMonth) {
            $totalEarningsThisMonth = $resultTotalEarningsThisMonth['amount']; // This will contain the sum of the amounts
        }

        $barChartEarnings = $paymentsModel->getMonthlyPaidAmountsLast12Months($branchId);

        $data = [
            'title' => 'Admin Dashboard',
            'page_heading' => 'Dashboard',
            'data' => [
                'stats' => [
                    'totalLeadCount' => $totalLeadCount,
                    'totalPaidAccounts' => $totalPaidAccounts,
                    'totalPaidAccountsWithDueAmount' => $totalPaidAccountsWithDueAmount,
                    'totalUnpaidAccounts' => $totalUnpaidAccounts,
                    'totalEarnings' => $totalEarnings,
                    'totalEarningsThisMonth' => $totalEarningsThisMonth,
                    'totalAmountDue' => $totalAmountDue
                ],
                'graph' => [
                    'barChartEarnings' => $barChartEarnings
                ]
            ]
        ];
        return view('dashboard', $data);
    }

    public function clients()
    {
        $session = session();
        if (empty($session->get('userId'))) {
            return redirect()->to('/login');
        }
        $branchId = $session->get('branchId');

        $ghlOpportunitiesModel = new GHLOpportunitiesModel();

        $filters['ghl_opportunities.status'] = ["open", "won"];

        if (!empty($branchId)) {
            $filters['ghl_opportunities.ghlPipelineId'] = $branchId;
        }

        $leads = $ghlOpportunitiesModel->getOpportunitiesWithLinkedEntities($filters);

        $data = [
            'title' => 'Clients',
            'page_heading' => 'Clients',
            'data' => [
                'leads' => $leads
            ]
        ];
        return view('clients', $data);
    }

    public function changeBranch()
    {
        $branchId = $this->request->getPost('branchId');

        // Validate inputs
        if (empty($branchId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid data provided.'
            ]);
        }

        $session = session();

        $session->set([
            'userId' => $session->get('userId'),
            'branchId' => $branchId,
            'userName' => $session->get('userName'),
            'email' => $session->get('email'),
            'role' => $session->get('role'),
            'isLoggedIn' => TRUE,
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Branch changed successfully.'
        ]);
    }

    public function fetchDetails($leadId)
    {
        $ghlOpportunitiesModel = new GHLOpportunitiesModel();

        $opportunity = $ghlOpportunitiesModel->find($leadId);

        $response = [
            'success' => true,
            'message' => 'All servers',
            'data'    => [
                "leadDetails" => $opportunity
            ]
        ];
        return $this->response->setJSON($response);
    }

    public function leadSingle($leadId)
    {
        $session = session();
        if (empty($session->get('userId'))) {
            return redirect()->to('/login');
        }

        $ghlOpportunitiesModel = new GHLOpportunitiesModel();

        $opportunity = $ghlOpportunitiesModel->find($leadId);

        $data = [
            'title' => $opportunity['name'],
            'page_heading' => $opportunity['name'],
            'opportunity' => $opportunity
        ];
        return view('lead_single', $data);
    }

    public function updateDetails($leadId)
    {
        $ghlOpportunitiesModel = new GHLOpportunitiesModel();

        $server = json_decode($this->request->getPost('server')); // Decode JSON string back to array
        $serverCost = $this->request->getPost('serverCost');

        // Validate inputs
        if (empty($leadId) || empty($server)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid data provided.'
            ]);
        }

        // Prepare the data to update
        $dataToUpdate = [
            'server' => json_encode($server), // Store as JSON in the database
            'serverCost' => $serverCost,
            'updatedAt' => date('Y-m-d H:i:s')
        ];

        // Update record by ghlOpportunityId
        $ghlOpportunitiesModel->update($leadId, $dataToUpdate);

        $this->fetchPayments($leadId);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Lead updated successfully.'
        ]);
    }

    public function fetchAccounts($leadId)
    {
        $accountsModel = new AccountsModel();

        $filters['accounts.ghlOpportunityId'] = $leadId;

        $accounts = $accountsModel
            ->where('ghlOpportunityId', $leadId)
            ->findAll();

        $totalCount = $accountsModel->getTotalCount($filters);

        $response = [
            'success' => true,
            'message' => 'All accounts',
            'total' => $totalCount,
            'data'    => $accounts
        ];
        return $this->response->setJSON($response);
    }

    public function createAccount($leadId)
    {
        $accountsModel = new AccountsModel();

        $serverType = $this->request->getPost('serverType');
        $accountType = $this->request->getPost('accountType');
        $accountNumber = $this->request->getPost('accountNumber');
        $accountCost = $this->request->getPost('accountCost');
        $multiplier = $this->request->getPost('multiplier');

        // Validate inputs
        if (empty($leadId) || empty($serverType) || empty($accountType) || empty($accountNumber) || empty($accountCost) || empty($multiplier)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid data provided.'
            ]);
        }

        // Prepare the data to create
        $data = [
            'ghlOpportunityId' => $leadId,
            'serverType' => $serverType,
            'accountType' => $accountType,
            'accountNumber' => $accountNumber,
            'accountCost' => $accountCost,
            'multiplier' => $multiplier,
            'createdAt' => date('Y-m-d H:i:s')
        ];

        $accountsModel->insert($data);

        $this->fetchPayments($leadId);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Account created successfully.'
        ]);
    }

    public function fetchPayments($ghlOpportunityId)
    {
        $paymentsModel = new PaymentsModel();

        // 1. Fetch past payments
        $pastPayments = $paymentsModel->getPastPayments($ghlOpportunityId);

        // 2. Calculate server cost and account cost for the current month
        $serverCost = $paymentsModel->getServerCost($ghlOpportunityId);
        $accountTotal = $paymentsModel->getAccountTotal($ghlOpportunityId);
        $currentMonthCost = $serverCost + $accountTotal;

        // 3. Get the current month
        $currentMonth = date('F Y'); // e.g., 'October 2024'

        // 4. Check if there's an existing payment entry for the current month
        $currentPayment = $paymentsModel->getAllCurrentMonthPayments($ghlOpportunityId, $currentMonth);

        // Initialize paid and remaining amounts based on current payment if it exists
        $paidAmount = 0;
        foreach ($currentPayment as $item) {
            if ($item['paymentStatus'] === "paid") {
                $paidAmount += $item['amount'];
            }
        }
        $remainingAmount = max(0, $currentMonthCost - $paidAmount);

        // Determine if an additional "unpaid" entry is needed
        if ($remainingAmount > 0) {
            // Check if there’s already an unpaid entry for the remaining amount in the current month
            $unpaidEntry = $paymentsModel->getCurrentMonthPaymentByStatus($ghlOpportunityId, $currentMonth, 'unpaid');

            // Only create a new unpaid entry if it doesn’t already exist
            if (!$unpaidEntry) {
                $paymentsModel->recordPayment($ghlOpportunityId, $remainingAmount, 'unpaid', $currentMonth);
            } else if ($unpaidEntry["amount"] < $remainingAmount) {
                // Prepare the data to update
                $dataToUpdate = [
                    'amount' => $remainingAmount,
                    'updatedAt' => date('Y-m-d H:i:s')
                ];

                // Update record by paymentId
                $paymentsModel->update($unpaidEntry['paymentId'], $dataToUpdate);
            }
        }

        // Create an entry to represent the current month's payment for display
        $allCurrentPayment = $paymentsModel->getAllCurrentMonthPayments($ghlOpportunityId, $currentMonth);

        // 5. Combine past payments and the current month entry into one list
        $payments = array_merge($pastPayments, $allCurrentPayment);

        $response = [
            'success' => true,
            'message' => 'All payments',
            'data'    => $payments
        ];
        return $this->response->setJSON($response);
    }

    public function updatePayments($leadId)
    {
        $paymentsModel = new PaymentsModel();

        $paymentId = $this->request->getPost('paymentId');
        $month = $this->request->getPost('month');
        $amount = $this->request->getPost('amount');
        $paymentMadeOn = $this->request->getPost('paymentMadeOn');

        // Validate inputs
        if (empty($leadId) || empty($paymentMadeOn) || empty($month) || empty($amount)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid data provided.'
            ]);
        }

        if (!empty($paymentId) && $paymentId !== "undefined") {
            // Prepare the data to update
            $dataToUpdate = [
                'paymentStatus' => 'paid',
                'paymentMadeOn' => $paymentMadeOn,
                'updatedAt' => date('Y-m-d H:i:s')
            ];

            // Update record by paymentId
            $paymentsModel->update($paymentId, $dataToUpdate);
        } else {
            // Prepare the data to create
            $data = [
                'ghlOpportunityId' => $leadId,
                'month' => $month,
                'amount' => $amount,
                'paymentStatus' => 'paid',
                'paymentMadeOn' => $paymentMadeOn,
                'createdAt' => date('Y-m-d H:i:s')
            ];

            $paymentsModel->insert($data);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => "Payment recorded successfully"
        ]);
    }
}
