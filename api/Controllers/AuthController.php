<?php

namespace Controllers;
require_once __DIR__ . "/../../model/Users.php";

use Model\Users;

class AuthController
{
    public function registrasi()
    {
        // menerima request dari client content-type: JSON
        $request = json_decode(file_get_contents('php://input'), true);

        // validasi request client
        if (empty($request['name']) || empty($request['email']) || empty($request['password'])) {
            http_response_code(400);
            echo json_encode(['message' => 'Data tidak lengkap harus diisi']);
            exit();
        }

        // validasi email sama
        $model_users = new Users();
        $validasi_email = $model_users->findEmail($request['email']);
        if ($validasi_email != false) {
            http_response_code(409);
            echo json_encode(['message' => 'Email sudah digunakan']);
            exit();
        }

        // enkripsi password
        $password_hased = password_hash($request['password'], PASSWORD_DEFAULT);
        $form_data = [
            "name" => $request['name'],
            "email" => $request['email'],
            "password" => $password_hased,
        ];

        // simpan data
        $result = $model_users->registrasi($form_data);

        if ($result) {
            // key password tidak perlu di response
            unset($result['password']);

            //response data
            http_response_code(201); // Created
            echo json_encode([
                'message' => 'Registrasi berhasil',
                'data' => $result
            ]);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['message' => 'Registrasi gagal, terjadi kesalahan pada database saat proses registrasi']);
        }
    }

    /*
     * method login digunakan untuk proses mencocokan email dan password yang ada di table users
     * */
    public function login() {
        // Menerima data JSON dari request
        $request = json_decode(file_get_contents('php://input'), true);

        // Validasi input
        if (empty($request['email']) || empty($request['password'])) {
            echo json_encode(['message' => 'Data tidak lengkap harus diisi']);
            http_response_code(400);
            exit();
        }

        // verifikasi request email
        $model_user = new Users();
        $check_email = $model_user->findEmail($request['email']);
        var_dump($check_email, $request['email']);
        exit();

        // Verifikasi password

        // Kirim respons sukses dengan token

        // Password tidak cocok
    }
}