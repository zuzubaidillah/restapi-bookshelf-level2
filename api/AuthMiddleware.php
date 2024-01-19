<?php
require_once __DIR__."/../model/book.php";
require_once __DIR__."/../model/users.php";
require_once __DIR__."/../config/TokenJwt.php";
use Config\{TokenJwt};
use Model\{Users,Book};

class AuthMiddleware
{

    public static function authenticate()
    {
        $headers = getallheaders();
        $jwt = null;

        if (isset($headers['Authorization'])) {
            $bearer = explode(' ', $headers['Authorization']);
            $jwt = $bearer[sizeof($bearer) - 1] ?? null;
        }

        if (!$jwt) {
            http_response_code(401); // Unauthorized
            echo json_encode(['message' => 'Akses ditolak. Token tidak ditemukan.']);
            exit();
        }

        try {
            $token_jwt = new TokenJwt();
            $verifikasi_token = $token_jwt->verify($jwt);

            $user = new Users();
            $result = $user->findId($verifikasi_token['user_id']);
            if (!$result) {
                http_response_code(401);
                echo json_encode(['message' => 'users tidak ditemukan']);
                exit();
            }
            unset($result['password']);

            return $result;

        } catch (Exception $e) {
            http_response_code(401); // Unauthorized
            echo json_encode(['message' => 'Token tidak valid: ' . $e->getMessage()]);
            exit();
        }
    }
}
