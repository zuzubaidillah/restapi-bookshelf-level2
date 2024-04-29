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

        $token_jwt = new TokenJwt();
        $verifikasi_token = $token_jwt->verify($jwt);

        $model_book = new Book();
        $result = $model_book->read($verifikasi_token['user_id']);
        var_dump($result);
        exit();
    }
}