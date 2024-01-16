<?php
require_once __DIR__ . "/../../model/user.php";

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

        $user = new User();
        // validasi email yang SAMA
        $validasi_email = $user->findEmail($input_data['email']);
        if (!($validasi_email)) {
            echo json_encode(['message' => 'login gagal, cek email dan password']);
            http_response_code(400); // Conflict
            exit();
        }
    }
    public function getByToken() {
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

        $user = new User();
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
            echo json_encode(['message' => 'Registrasi gagal, terjadi kesalahan pada database saat proses registrasi']);
            http_response_code(500); // Internal Server Error
        }
    }
}