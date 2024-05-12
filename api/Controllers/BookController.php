<?php

namespace Controllers;

use Config\TokenJwt;
use Model\Book;

class BookController
{
    public function getBooks()
    {
        // verifikasi token
        $headers = getallheaders();
        $bearer = explode(' ', $headers['Authorization']);
        $jwt = $bearer[sizeof($bearer) - 1] ?? null;
        // memanggil library tokenJWT
        $token_jwt = new TokenJwt();
        $verifikasi_token = $token_jwt->verify($jwt);
        $user_id = $verifikasi_token['user_id'];

        // validasi params status
        if (!isset($_GET['status'])) {
            // membuat response
            http_response_code(400);
            echo json_encode([
                'message' => 'Request Params status harus kirim'
            ]);
            exit();
        }
        $status = '';
        $filter_q = '';
        $status_param = $_GET['status'];

        if ($status_param === 'all' || $status_param === 'sudah' || $status_param === 'belum' ) {
            $status = $status_param;
        }else{
            // membuat response
            http_response_code(400);
            echo json_encode([
                'message' => 'Status harus berupa nilai (all, sudah, atau belum)'
            ]);
            exit();
        }

        if (isset($_GET['q'])) {
            $filter_q = $_GET['q'];
        }

        // membuat model untuk mengambil data buku
        $model_book = new Book();
        $result = $model_book->read($user_id, $status, $filter_q);

        // membuat response
        http_response_code(200);
        echo json_encode([
            'data' => $result
        ]);

    }

    public function createBook()
    {
        // verifikasi token
        $headers = getallheaders();
        $bearer = explode(' ', $headers['Authorization']);
        $jwt = $bearer[sizeof($bearer) - 1] ?? null;
        // memanggil library tokenJWT
        $token_jwt = new TokenJwt();
        $verifikasi_token = $token_jwt->verify($jwt);
        $user_id = $verifikasi_token['user_id'];
        
        // validasi request client
        $errors = [];
        // Validasi 'title' required
        if (empty($_POST['title'])) {
            $errors['title'] = 'Judul buku diperlukan.';
        }

        // Validasi 'year' required
        if (empty($_POST['year']) || !is_numeric($_POST['year'])) {
            $errors['year'] = 'Tahun harus berupa angka.';
        }

        // Validasi 'author' required
        if (empty($_POST['author'])) {
            $errors['author'] = 'Nama penulis diperlukan.';
        }

        // Validasi 'isComplete' optional
        if (isset($_POST['isComplete'])) {
            if ($_POST['isComplete'] != '1' && $_POST['isComplete'] != '0') {
                $errors['isComplete'] = 'Status isComplete harus 1 atau 0.';
            }
        }

        // Validasi 'file' required
        if (!isset($_FILES['file'])) {
            $errors['file'] = 'File harus diisi.';
        } else if ($_FILES['file']['name'] === "") {
            $errors['file'] = 'File harus diisi.';
        }

        if (count($errors) >= 1) {
            http_response_code(400); // Unauthorized
            echo json_encode([
                'errors' => $errors,
                'error' => 'request tidak lengkap',
                'message' => 'request tidak lengkap',
            ]);
            exit();
        }
        var_dump('request sesuai');
        exit();
        
        // cek title/judul yang sama
        // cek ukuran file maksimal 2mb
        // file harus jpg, jpeg, png, dan pdf
        // lakukan upload file kedalam folder uploads
        // cek apakah ada folder uploads, buatkan folder jika blm ada
        // simpan data
        // response data yang baru saja disimpan

    }
}