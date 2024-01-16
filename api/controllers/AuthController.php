<?php
require_once __DIR__ . "/../../model/user.php";

class AuthController
{
    private $book;

    public function __construct()
    {
    }
    public function login() {}
    public function getByToken() {}
    public function registrasi() {
        // Menerima data JSON dari request
        $inputData = json_decode(file_get_contents('php://input'), true);

        // Validasi input
        if (empty($inputData['name']) || empty($inputData['email']) || empty($inputData['password'])) {
            echo json_encode(['message' => 'Data lengkap harus diisi']);
            http_response_code(400); // Bad Request
            exit();
        }

        // Cek duplikasi email
        $stmt = $db->prepare("SELECT id FROM user WHERE email = :email");
        $stmt->execute(['email' => $inputData['email']]);
        if ($stmt->fetch()) {
            echo json_encode(['message' => 'Email sudah digunakan']);
            http_response_code(409); // Conflict
            exit();
        }

        // Enkripsi password
        $hashedPassword = password_hash($inputData['password'], PASSWORD_DEFAULT);

        // Menyimpan data ke database
        $stmt = $db->prepare("INSERT INTO user (name, email, password) VALUES (:name, :email, :password)");
        $result = $stmt->execute([
            'name' => $inputData['name'],
            'email' => $inputData['email'],
            'password' => $hashedPassword
        ]);

        if ($result) {
            echo json_encode(['message' => 'Registrasi berhasil']);
            http_response_code(201); // Created
        } else {
            echo json_encode(['message' => 'Registrasi gagal']);
            http_response_code(500); // Internal Server Error
        }
    }
}