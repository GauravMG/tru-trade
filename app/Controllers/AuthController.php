<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\UsersModel;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth/login');
    }

    public function attemptLogin()
    {
        $session = session();
        if (!empty($session->get('userId'))) {
            return redirect()->to('/dashboard');
        }

        $usersModel = new UsersModel();

        $branchId = $this->request->getPost('branchId');
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $usersModel->where('email', $email)->first();

        if ($user) {
            $pass = $user['password'];
            if (password_verify($password, $pass)) {
                if ($user['role'] !== "Owner" && ($user['branchId'] !== $branchId || empty($branchId))) {
                    $session->setFlashdata('error', 'Not authorized. Please select a valid branch.');
                    return redirect()->to('/login');
                } else {
                    $session->set([
                        'userId' => $user['userId'],
                        'branchId' => $user['branchId'],
                        'userName' => $user['userName'],
                        'email' => $user['email'],
                        'role' => $user['role'],
                        'isLoggedIn' => TRUE,
                    ]);
                    return redirect()->to('/dashboard');
                }
            } else {
                $session->setFlashdata('error', 'Invalid Password');
                return redirect()->to('/login');
            }
        } else {
            $session->setFlashdata('error', 'Email not found');
            return redirect()->to('/login');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
