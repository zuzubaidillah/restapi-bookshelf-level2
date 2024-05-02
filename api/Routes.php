<?php
//file Routes.php digunakan untuk list sebuah endpoint
require_once __DIR__ . '/../config/Route.php';
require_once __DIR__ . '/Controllers/AuthController.php';
require_once __DIR__ . '/Controllers/BookController.php';
require_once __DIR__ . '/../config/TokenJwt.php';
require_once __DIR__ . '/../model/Users.php';
require_once __DIR__ . '/../model/Book.php';

use Config\Route;
use Config\TokenJwt;
use Controllers\AuthController;
use Controllers\BookController;
use Model\Users;

$base_url = "/smkti/restapi-bookshelf-level2";

Route::post($base_url . "/api/registrasi", function () {
    echo json_encode([
        "message" => "ini registrasi"
    ]);
});

/**
 * API AUTH / USER
 * */
Route::post($base_url . "/api/auth/registrasi", function (){
    $controller = new AuthController();
    $controller->registrasi();
});
Route::post($base_url . "/api/auth/login", function (){
    $controller = new AuthController();
    $controller->login();
});
Route::get($base_url . "/api/auth/current", function (){
    // verifikasi token
    $headers = getallheaders();
    $jwt = null;

    if (isset($headers['Authorization'])) {
        $bearer = explode(' ', $headers['Authorization']);
        $jwt = $bearer[sizeof($bearer) - 1] ?? null;
    }

    if ($jwt == null) {
        //    response token tidak ditemukan
        http_response_code(400);
        echo json_encode([
            "message" => "Akses ditolak. Token tidak ditemukan"
        ]);
        exit();
    }

    try {
        // memanggil library tokenJWT
        $token_jwt = new TokenJwt();
        $verifikasi_token = $token_jwt->verify($jwt);

        $model_user = new Users();
        $result = $model_user->findId($verifikasi_token['user_id']);

        if ($result == false) {
            http_response_code(401);
            echo json_encode(['message' => 'users tidak ditemukan']);
            exit();
        }
    } catch (Exception $e) {
        http_response_code(401); // Unauthorized
        echo json_encode([
            'message' => 'Token tidak valid: ' . $e->getMessage()
        ]);
        exit();
    }

    $controller = new AuthController();
    $controller->getByToken();
});

/**
 * API BOOK
 * */
Route::get($base_url . "/api/book", function (){
    // verifikasi token
    $headers = getallheaders();
    $jwt = null;

    if (isset($headers['Authorization'])) {
        $bearer = explode(' ', $headers['Authorization']);
        $jwt = $bearer[sizeof($bearer) - 1] ?? null;
    }

    if ($jwt == null) {
        //    response token tidak ditemukan
        http_response_code(400);
        echo json_encode([
            "message" => "Akses ditolak. Token tidak ditemukan"
        ]);
        exit();
    }

    try {
        // memanggil library tokenJWT
        $token_jwt = new TokenJwt();
        $verifikasi_token = $token_jwt->verify($jwt);

        $controller = new BookController();
        $controller->getBooks();

    } catch (Exception $e) {
        http_response_code(401); // Unauthorized
        echo json_encode([
            'message' => 'Token tidak valid: ' . $e->getMessage()
        ]);
        exit();
    }
});


// Add more routes here
Route::run();
