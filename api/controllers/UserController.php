<?php
require_once __DIR__ . "/../../model/Users.php";

class UserController
{
    private $book;

    public function __construct()
    {
    }
    public function getUsers() {}
    public function loginUser() {}
    public function deleteUser() {}
    public function putUser() {}
    public function getUserById() {}
    public function postUser() {
        // Menerima data JSON dari request
        $request = json_decode(file_get_contents('php://input'), true);

        // Validasi input
        if (empty($request['name']) || empty($request['email']) || empty($request['password'])) {
            echo json_encode(['message' => 'Data lengkap harus diisi']);
            http_response_code(400); // Bad Request
            exit();
        }

        // Cek duplikasi email

        // Enkripsi password
        $hashedPassword = password_hash($request['password'], PASSWORD_DEFAULT);

        // Menyimpan data ke database

        if (true) {
            echo json_encode(['message' => 'Registrasi berhasil']);
            http_response_code(201); // Created
        } else {
            echo json_encode(['message' => 'Registrasi gagal']);
            http_response_code(500); // Internal Server Error
        }
    }
}