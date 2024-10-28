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

        $leads = $ghlOpportunitiesModel->getOpportunitiesWithLinkedEntities($filters);

        $totalLeadCount = $ghlOpportunitiesModel->getTotalCount($filters);

        $filtersPaidAccounts = [
            'payments.month' => date('F Y'),
            'payments.paymentStatus' => "paid"
        ];
        $groupByFieldsPaidAccounts = ['payments.ghlOpportunityId'];
        
        $totalPaidAccounts = $paymentsModel->getTotalCount($filtersPaidAccounts, $groupByFieldsPaidAccounts, $branchId);

        $totalUnpaidAccounts = $totalLeadCount - $totalPaidAccounts;

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

        $data = [
            'title' => 'Admin Dashboard',
            'page_heading' => 'Dashboard',
            'stats' => [
                'totalLeadCount' => $totalLeadCount,
                'totalPaidAccounts' => $totalPaidAccounts,
                'totalUnpaidAccounts' => $totalUnpaidAccounts,
                'totalEarnings' => $totalEarnings,
                'totalEarningsThisMonth' => $totalEarningsThisMonth
            ],
            'leads' => $leads
        ];
        return view('dashboard', $data);
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

        $accountType = $this->request->getPost('accountType');
        $server = json_decode($this->request->getPost('server')); // Decode JSON string back to array
        $serverCost = $this->request->getPost('serverCost');

        // Validate inputs
        if (empty($leadId) || empty($accountType)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid data provided.'
            ]);
        }

        // Prepare the data to update
        $dataToUpdate = [
            'accountType' => $accountType,
            'server' => json_encode($server), // Store as JSON in the database
            'serverCost' => $serverCost,
            'updatedAt' => date('Y-m-d H:i:s')
        ];

        // Update record by ghlOpportunityId
        $ghlOpportunitiesModel->update($leadId, $dataToUpdate);

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

        $systemType = $this->request->getPost('systemType');
        $accountNumber = $this->request->getPost('accountNumber');
        $accountSize = $this->request->getPost('accountSize');
        $accountCost = $this->request->getPost('accountCost');
        $multiplier = $this->request->getPost('multiplier');

        // Validate inputs
        if (empty($leadId) || empty($systemType) || empty($accountNumber) || empty($accountCost) || empty($multiplier)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid data provided.'
            ]);
        }

        // Prepare the data to update
        $data = [
            'ghlOpportunityId' => $leadId,
            'systemType' => $systemType,
            'accountNumber' => $accountNumber,
            'accountSize' => $accountSize,
            'accountCost' => $accountCost,
            'multiplier' => $multiplier,
            'createdAt' => date('Y-m-d H:i:s')
        ];

        $accountsModel->insert($data);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Account created successfully.'
        ]);
    }

    public function fetchPayments($leadId)
    {
        $paymentsModel = new PaymentsModel();

        $payments = $paymentsModel
            ->where('ghlOpportunityId', $leadId)
            ->findAll();

        $response = [
            'success' => true,
            'message' => 'All payments',
            'data'    => $payments
        ];
        return $this->response->setJSON($response);
    }

    public function updatePayments($leadId, $paymentId)
    {
        $paymentsModel = new PaymentsModel();

        $paymentMadeOn = $this->request->getPost('paymentMadeOn');

        // Validate inputs
        if (empty($leadId) || empty($paymentId) || empty($paymentMadeOn)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid data provided.'
            ]);
        }

        // Prepare the data to update
        $dataToUpdate = [
            'paymentStatus' => 'paid',
            'paymentMadeOn' => $paymentMadeOn,
            'updatedAt' => date('Y-m-d H:i:s')
        ];

        // Update record by paymentId
        $paymentsModel->update($paymentId, $dataToUpdate);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Payment updated successfully.'
        ]);
    }
}
