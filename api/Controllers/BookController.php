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
}