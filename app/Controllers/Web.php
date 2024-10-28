<?php

namespace App\Controllers;

use App\Models\QuestionsModel;
use App\Models\OptionsModel;
use App\Models\CertificatesModel;
use App\Models\UserRecordsModel;
use CodeIgniter\Controller;

class Web extends BaseController
{
    public function index(): string
    {
        return view('home');
    }

    public function questions(): string
    {
        $questionsModel = new QuestionsModel();
        $optionsModel = new OptionsModel();

        // Fetch all questions
        $questions = $questionsModel->findAll();

        // Fetch options for each question
        foreach ($questions as $index => $question) {
            $questions[$index]['options'] = $optionsModel->where('questionId', $question['questionId'])->findAll();
        }

        $data = array(
            'questions' => $questions
        );

        return view('questions', $data);
    }

    public function form(): string
    {
        return view('form');
    }

    public function storeUserRecord()
    {
        try {
            // Get the POST data
            $data = $this->request->getPost();

            // Add createdAt and updatedAt fields
            $timestamp = date('Y-m-d H:i:s');
            $data['createdAt'] = $timestamp;
            $data['updatedAt'] = $timestamp;

            if (isset($_FILES['profilePic']) && $_FILES['profilePic']['error'] == UPLOAD_ERR_OK) {
                // Get file information
                $tmpName = $_FILES['profilePic']['tmp_name'];
                $originalName = $_FILES['profilePic']['name'];
                $fileExtension = pathinfo($originalName, PATHINFO_EXTENSION);
    
                // Generate a unique filename
                $imageName = md5(uniqid(rand(), true)) . '.' . $fileExtension;
                
                // Path to the uploads directory
                $path = $_SERVER['DOCUMENT_ROOT'] . '/public/uploads/profile-pic/';
            
                // Ensure the uploads directory exists and is writable
                if (!file_exists($path)) {
                    mkdir($path, 0777, true); // Create uploads directory if it doesn't exist
                }
            
                // Read the file data from the temporary location
                $fileData = file_get_contents($tmpName);
                
                // Write the file data to the target directory
                $filePath = $path . $imageName;
                if (file_put_contents($filePath, $fileData) !== false) {
                    // File uploaded successfully, echo the file URL
                    $data["profilePic"] = base_url('uploads/profile-pic/' . $imageName);
                    // exit; // Exit after successful upload
                } else {
                    // Error in writing the file
                    // echo json_encode(array("success" => false, "message" => "Error writing the file."));
                }
            }

            $certificatesModel = new CertificatesModel();

            $certificate = $certificatesModel->getCertificateByPoints($data["score"]);

            $data['certificateId'] = $certificate["certificateId"];
            $data['certificateName'] = $certificate["title"];

            $userRecordsModel = new UserRecordsModel();

            $userRecordsModel->insert($data);

            echo json_encode(array(
                "success" => true,
                "message" => "Details recorded successfully",
                "data" => array(
                    "userData" => $data,
                    "certificateDetails" => $certificate
                )
            ));
        } catch (\Exception $error) {
            echo json_encode(array(
                "success" => false,
                "message" => $error->getMessage(),
                "data" => $error
            ));
        }
    }

    public function certificate(): string
    {
        return view('certificate');
    }

    public function userCertificatePlanetHealer(): string
    {
        return view('certificates/planet-healer');
    }

    public function userCertificateGlobeMechanic(): string
    {
        return view('certificates/globe-mechanic');
    }

    public function userCertificateEcoPioneer(): string
    {
        return view('certificates/eco-pioneer');
    }

    public function userCertificateSustainabilityStarStudent(): string
    {
        return view('certificates/sustainability-star-student');
    }

    public function uploadCertificate() {
        // Check if 'image' is set in POST data
        if(isset($_POST['image']) && !empty($_POST['image'])) {
            $image = base64_decode($_POST['image']);
            
            // Generate a unique filename
            $image_name = md5(uniqid(rand(), true));
            $filename = $image_name . '.png';
            
            // Path to the uploads directory
            $path = $_SERVER['DOCUMENT_ROOT'] . '/public/uploads/';
    
            // Ensure the uploads directory exists and is writable
            if (!file_exists($path)) {
                mkdir($path, 0777, true); // Create uploads directory if it doesn't exist
            }
    
            // Write the image data to the file
            if(file_put_contents($path . $filename, $image)) {
                // File uploaded successfully, echo the file URL
                echo base_url() . 'uploads/' . $filename;
                exit; // Exit after successful upload
            } else {
                // Error in uploading file
                echo false;
            }
        } else {
            // 'image' not found in POST data or empty
            echo false;
        }
    }
}
