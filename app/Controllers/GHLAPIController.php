<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\GHLPipelinesModel;
use App\Models\GHLStagesModel;
use App\Models\GHLOpportunitiesModel;

class GHLAPIController extends Controller
{
    private function fetchOpportunities($pipelineExternalId, $stageExternalId) {
        $url = "https://rest.gohighlevel.com/v1/pipelines/$pipelineExternalId/opportunities?stageId=$stageExternalId";
    
        $allOpportunities = []; // Array to store all opportunities
    
        do {
            // Initialize cURL
            $ch = curl_init($url);
    
            // Set options
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer 1a97f81f-9480-4f8d-9422-5c26ddeca5c9' // Ensure this token is valid
            ]);
    
            // Execute the request
            $response = curl_exec($ch);
    
            // Check for errors
            if (curl_errno($ch)) {
                return $this->response->setJSON(['error' => curl_error($ch)]);
            }
    
            // Close cURL
            curl_close($ch);
    
            // Decode the response
            $dataOpportunities = json_decode($response, true);
    
            // Check if the request was successful and data is present
            if (isset($dataOpportunities['opportunities'])) {
                // Merge the current page data into all opportunities
                $allOpportunities = array_merge($allOpportunities, $dataOpportunities['opportunities']);
                
                // Update the URL for the next page using the nextPage value
                $nextPage = $dataOpportunities['meta']['nextPage'] ?? null;
                if ($nextPage) {
                    // Add the page parameter to the URL
                    $url = "https://rest.gohighlevel.com/v1/pipelines/$pipelineExternalId/opportunities?stageId=$stageExternalId&page=$nextPage";
                } else {
                    // If there's no data, break the loop
                    break;
                }
            } else {
                // If there's no data, break the loop
                break;
            }
        } while ($url); // Continue until there are no more pages
    
        // Return all opportunities as JSON
        return $allOpportunities;
    }    

    public function fetchPipelines()
    {
        $url = 'https://rest.gohighlevel.com/v1/pipelines/';

        // Initialize cURL
        $ch = curl_init($url);

        // Set options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            // Add any other headers if required
            'Authorization: Bearer 1a97f81f-9480-4f8d-9422-5c26ddeca5c9'
        ]);

        // Execute the request
        $response = curl_exec($ch);

        // Check for errors
        if (curl_errno($ch)) {
            return $this->response->setJSON(['error' => curl_error($ch)]);
        }

        // Close cURL
        curl_close($ch);

        // Decode the response
        $dataPipelines = json_decode($response, true);

        $ghlPipelinesModel = new GHLPipelinesModel();
        $ghlStagesModel = new GHLStagesModel();
        $ghlOpportunitiesModel = new GHLOpportunitiesModel();

        foreach ($dataPipelines['pipelines'] as $pipeline) {
            // Remove numbering if it exists in the pipeline name
            $pipelineName = preg_replace('/^\d+\.\s*/', '', $pipeline['name']);

            // Prepare pipeline data
            $pipelineData = [
                'externalId'         => $pipeline['id'],
                'externalLocationId' => $pipeline['locationId'],
                'name'               => $pipelineName,
                'createdAt'          => date('Y-m-d H:i:s'),
            ];

            // Insert pipeline using the model
            $ghlPipelinesModel->insert($pipelineData);
            $ghlPipelineId = $ghlPipelinesModel->insertID();  // Get the inserted pipeline's ID

            foreach ($pipeline['stages'] as $stage) {
                // Prepare pipeline data
                $stageData = [
                    'ghlPipelineId' => $ghlPipelineId,
                    'externalId'    => $stage['id'],
                    'name'          => $stage['name'],
                    'createdAt'     => date('Y-m-d H:i:s'),
                ];

                // Insert stage using the model
                $ghlStagesModel->insert($stageData);
                $ghlStageId = $ghlStagesModel->insertID();  // Get the inserted stage's ID

                $dataOpportunities = $this->fetchOpportunities($pipeline['id'], $stage['id']);

                // Prepare opportunity data
                foreach ($dataOpportunities as $opportunity) {
                    $opportunityData = [
                        'ghlPipelineId' => $ghlPipelineId, // Corresponds to ghlPipelineId
                        'ghlStageId' => $ghlStageId, // This field may need to be included as per your schema
                        'externalId' => $opportunity['id'] ?? null, // Maps to externalId
                        'name' => $opportunity['name'] ?? null, // Maps to name
                        'assignedTo' => $opportunity['assignedTo'] ?? null, // Maps to assignedTo
                        'status' => $opportunity['status'] ?? null, // Maps to status
                        'source' => $opportunity['source'] ?? null, // Maps to source
                        'lastStatusChangeAt' => date('Y-m-d H:i:s', strtotime($opportunity['lastStatusChangeAt'])), // Maps to lastStatusChangeAt
                        'contactIdExternal' => isset($opportunity['contact']) ? $opportunity['contact']['id'] ?? null : null, // Maps to contactIdExternal
                        'contactNameExternal' => isset($opportunity['contact']) ? $opportunity['contact']['name'] ?? null : null, // Maps to contactNameExternal
                        'contactCompanyNameExternal' => isset($opportunity['contact']) ? $opportunity['contact']['companyName'] ?? null : null, // Maps to contactCompanyNameExternal
                        'contactEmailExternal' => isset($opportunity['contact']) ? $opportunity['contact']['email'] ?? null : null, // Maps to contactEmailExternal
                        'contactPhoneExternal' => isset($opportunity['contact']) ? $opportunity['contact']['phone'] ?? null : null, // Maps to contactPhoneExternal
                        'contactTagsExternal' => isset($opportunity['contact']) ? (isset($opportunity['contact']['tags']) ? json_encode($opportunity['contact']['tags']) : null) : null, // Converts tags array to JSON for contactTagsExternal
                        'completeJson' => json_encode($opportunity), // Store the entire opportunity object as a JSON string
                        'createdAt' => date('Y-m-d H:i:s', strtotime($opportunity['createdAt'])), // Maps to createdAt
                        'updatedAt' => date('Y-m-d H:i:s', strtotime($opportunity['updatedAt'])), // Maps to updatedAt
                    ];

                    $ghlOpportunitiesModel->insert($opportunityData);
                }
            }
        }

        // Fetch all pipelines from the `ghl_pipelines` table
        $allPipelines = $ghlPipelinesModel->findAll();

        $results = [];

        // Loop through each pipeline and fetch its stages
        foreach ($allPipelines as $pipeline) {
            // Fetch the stages for this pipeline
            $stages = $ghlStagesModel
                ->where('ghlPipelineId', $pipeline['ghlPipelineId'])
                ->findAll();

            $resultStages = [];

            // Loop through each stage and fetch its opportunities
            foreach ($stages as $stage) {
                // Fetch the opportunities for this stage
                $opportunities = $ghlOpportunitiesModel
                    ->where('ghlStageId', $stage['ghlStageId'])
                    ->findAll();

                // Add the pipeline and its stages to the result
                $resultStages[] = array_merge($stage, ['opportunities' => $opportunities]);
            }

            // Add the pipeline and its stages to the result
            $results[] = array_merge($pipeline, ['stages' => $resultStages]);
        }

        // Prepare the response structure
        $response = [
            'success' => true,
            'message' => 'All pipelines',
            'data'    => $results
        ];

        // Return the response as a JSON object
        return $this->response->setJSON($response);
    }
}
