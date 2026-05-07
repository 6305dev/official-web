<?php

namespace App\Controllers;

class AuthController
{
    public function logout()
    {
        session_destroy();
        header('location: /login');
        exit();
    }

    public function authenticate()
    {
        if (!isset($_SESSION['authtoken'])) {
            header('location: /login');
            exit();
        }
    }

    public function limitAccess()
    {
        if (!isset($_SESSION['authtoken'])) {
            http_response_code(403);
            echo 'Forbidden';
            exit();
        }
    }

    public function logged()
    {
        if (isset($_SESSION['authtoken'])) {
            header('location: /dashboard');
            exit();
        }
    }

    public function login()
    {
        $body = (object) $_POST;
        $pass = $body->password ?? '';

        if ($pass === base64_decode('cGxheTYzMDU=')) {
            session_regenerate_id(true);
            $_SESSION['authtoken'] = bin2hex(random_bytes(32));
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
        } else {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['success' => false]);
        }
    }
}
