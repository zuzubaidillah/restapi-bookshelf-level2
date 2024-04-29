<?php

namespace Controllers;
require_once __DIR__ . "/../../model/Book.php";
require_once __DIR__ . "/../../config/TokenJwt.php";

use Config\TokenJwt;
use Model\Book;

class BookController
{
    public function getBooks()
    {
        // Mendapatkan token dari header
        $headers = getallheaders();
        $jwt = null;

        if (isset($headers['Authorization'])) {
            $bearer = explode(' ', $headers['Authorization']);
            $index_token = (sizeof($bearer)-1);
            $jwt = $bearer[$index_token] ?? null;
        }

        if (!$jwt) {
            echo json_encode(['message' => 'Akses ditolak. Token tidak ditemukan.']);
            http_response_code(401); // Unauthorized
            exit();
        }

        $status = "";

        // Pengecekan apakah status diatur dalam URL
        if (isset($_GET['status'])) {
            // Pengecekan nilai status
            $status_param = $_GET['status'];

            // Hanya set status jika nilainya adalah all, belum, atau sudah
            if ($status_param === 'all' || $status_param === 'belum' || $status_param === 'sudah') {
                $status = $status_param;
            } else {
                $this->sendJson([
                    'message' => 'parmas status tidak sesuai. harus (all,belum,sudah)'
                ], 400);
                exit();
            }
        }

        $token_jwt = new TokenJwt();
        $verifikasi_token = $token_jwt->verify($jwt);

        $model_book = new Book();
        $result = $model_book->read($verifikasi_token['user_id'], $status);
        echo json_encode([
            'data' => $result
        ]);
        http_response_code(200); // OK
    }
}