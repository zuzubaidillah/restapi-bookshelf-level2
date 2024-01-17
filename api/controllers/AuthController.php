<?php
require_once __DIR__ . "/../../model/users.php";
require_once __DIR__ . "/../../config/TokenJwt.php";


class AuthController
{
    private $book;

    public function __construct()
    {
    }
    public function login() {
        // Menerima data JSON dari request
        $input_data = json_decode(file_get_contents('php://input'), true);

        // Validasi input
        if (empty($input_data['email']) || empty($input_data['password'])) {
            echo json_encode(['message' => 'Data tidak lengkap harus diisi']);
            http_response_code(400);
            exit();
        }

        $user = new Users();
        // validasi email yang SAMA
        $validasi_email = $user->findEmail($input_data['email']);
        if (!($validasi_email)) {
            echo json_encode(['message' => 'login gagal, cek email dan password']);
            http_response_code(400); // Conflict
            exit();
        }
        // Verifikasi password
        if (password_verify($input_data['password'], $validasi_email['password'])) {
            // Password cocok, buat token auth (misalnya JWT atau token sederhana)

            $handlerToken = new TokenJwt();
            $token = $handlerToken->create($validasi_email['id']);
            // Kirim respons sukses dengan token
            unset($validasi_email['password']);
            echo json_encode([
                'data' => $validasi_email,
                'message' => 'Login berhasil',
                'token' => $token
            ]);
            http_response_code(200); // OK
        } else {
            // Password tidak cocok
            echo json_encode(['message' => 'Login gagal, cek email dan password']);
            http_response_code(400); // Unauthorized
            exit();
        }
    }
    private function unauthorize() {
        echo json_encode(['message' => 'Akses ditolak. Token tidak ditemukan.']);
        http_response_code(401); // Unauthorized
        exit();
    }

    public function getByToken() {
        // Mendapatkan token dari header
        $headers = getallheaders();
        $jwt = null;

        if (isset($headers['Authorization'])) {
            $bearer = explode(' ', $headers['Authorization']);
            $index_token = (sizeof($bearer)-1);
            $jwt = $bearer[$index_token] ?? null;
        }

        if (!$jwt) {
            $this->unauthorize();
        }

        $token_jwt = new TokenJwt();
        $verifikasi_token = $token_jwt->verify($jwt);

        $user = new Users();
        $result = $user->findId($verifikasi_token['user_id']);
        unset($result['password']);
        echo json_encode(['data' => $result]);
        http_response_code(200);
        exit();
    }

    public function registrasi() {
        // Menerima data JSON dari request
        $input_data = json_decode(file_get_contents('php://input'), true);

        // Validasi input
        if (empty($input_data['name']) || empty($input_data['email']) || empty($input_data['password'])) {
            echo json_encode(['message' => 'Data tidak lengkap harus diisi']);
            http_response_code(400);
            exit();
        }

        $user = new Users();
        // validasi email yang SAMA
        $validasi_email = $user->findEmail($input_data['email']);
        if (is_array($validasi_email)) {
            echo json_encode(['message' => 'Email sudah digunakan']);
            http_response_code(409); // Conflict
            exit();
        }

        // Enkripsi password
        $hashedPassword = password_hash($input_data['password'], PASSWORD_DEFAULT);
        $form_data = [
            'name' => $input_data['name'],
            'email' => $input_data['email'],
            'password' => $hashedPassword
        ];

        // Menyimpan data ke database
        $result = $user->registrasi($form_data);

        if ($result) {
            // key password tidak perlu di response
            unset($result['password']);
            echo json_encode([
                'message' => 'Registrasi berhasil',
                'data' => $result
            ]);
            http_response_code(201); // Created
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['message' => 'Registrasi gagal, terjadi kesalahan pada database saat proses registrasi']);
        }
    }
    private function sendJson($data, $code)
    {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode($data);
    }
}