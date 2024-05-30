<?php
require_once __DIR__ . '/../config/Route.php';
require_once __DIR__ . '/../config/TokenJwt.php';
require_once __DIR__ . "/AuthMiddleware.php";
require_once __DIR__ . "/../config/helper.php";
require_once __DIR__ . "/controllers/BookController.php";
require_once __DIR__ . "/controllers/UserController.php";
require_once __DIR__ . "/../model/Book.php";
require_once __DIR__ . "/../model/Users.php";

use Config\Route;
use Model\Users;
use Config\TokenJwt;

$base_url = "/smkti/FIX-restapi-bookshelf-level2";

Route::get($base_url . '/api/mock/{code}', function ($code) {
    http_response_code((int)$code);
    echo json_encode(['message' => 'mock-api']);
});
Route::post($base_url . '/api/mock/{code}', function ($code) {
    http_response_code(400);
    echo json_encode(['message' => 'mock-api']);
});
Route::put($base_url . '/api/mock/{code}', function ($code) {
    http_response_code((int)$code);
    echo json_encode(['message' => 'mock-api']);
});
Route::delete($base_url . '/api/mock/{code}', function ($code) {
    http_response_code((int)$code);
    echo json_encode(['message' => 'mock-api']);
});

/**
 * ROUTES AUTH
 */
Route::post($base_url . '/api/auth/registrasi', function () {
    require_once __DIR__ . '/controllers/AuthController.php';
    $controller = new AuthController();
    $controller->registrasi();
});

Route::post($base_url . '/api/auth/login', function () {
    require_once __DIR__ . '/controllers/AuthController.php';
    $controller = new AuthController();
    $controller->login();
});

Route::get($base_url . '/api/auth/current', function () {
    require_once __DIR__ . '/controllers/AuthController.php';
    AuthMiddleware::authenticate();
    $controller = new AuthController();
    $controller->getByToken();
});

/**
 * ROUTES BOOK
 */
Route::get($base_url . "/api/book", function () {
    // ambil bearer Token yang di request client
    $headers = getallheaders();
    $jwt = null;

    // jika ada array dengan key Authorization
    if (isset($headers['Authorization'])) {
        $bearer = explode(' ', $headers['Authorization']);
        $jwt = $bearer[sizeof($bearer) - 1] ?? null;
    }

    // jika tidak ditemukan key Authorization
    if (!$jwt) {
        http_response_code(401); // Unauthorized
        echo json_encode(['message' => 'Akses ditolak. Token tidak ditemukan.']);
        exit();
    }

    //proses cek token
    // mengambil user_id yang disimpan didalam token
    try {
        $token_jwt = new TokenJwt();
        $verifikasi_token = $token_jwt->verify($jwt);

        $user = new Users();
        $result = $user->findId($verifikasi_token['user_id']);
        if (!$result) {
            http_response_code(401);
            echo json_encode(['message' => 'users tidak ditemukan']);
            exit();
        }
    } catch (Exception $e) {
        http_response_code(401); // Unauthorized
        echo json_encode(['message' => 'Token tidak valid: ' . $e->getMessage()]);
        exit();
    }

    $controller = new BookController();
    $controller->getBooks();
});

Route::get($base_url . '/api/book/{book_id}', function ($id) {
    // kode dibawah ini untuk mengarahkan ke controller
    // verifikasi token
    $headers = getallheaders();
    $jwt = null;

    if (isset($headers['Authorization'])) {
        $bearer = explode(' ', $headers['Authorization']);
        $jwt = $bearer[sizeof($bearer) - 1] ?? null;
    }

    if (!$jwt) {
        header('Content-Type: application/json');
        http_response_code(401); // Unauthorized
        echo json_encode(['message' => 'Akses ditolak. Token tidak ditemukan.']);
        exit();
    }

    try {
        $token_jwt = new TokenJwt();
        $verifikasi_token = $token_jwt->verify($jwt);

        $user = new Users();
        $result = $user->findId($verifikasi_token['user_id']);
        if (!$result) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['message' => 'users tidak ditemukan']);
            exit();
        }
    } catch (Exception $e) {
        http_response_code(401); // Unauthorized
        echo json_encode(['message' => 'Token tidak valid: ' . $e->getMessage()]);
        exit();
    }

    $controller = new BookController();
    $controller->getBookById($id);
});

Route::post($base_url . '/api/book', function () {
    // verifikasi token
    $headers = getallheaders();
    $jwt = null;

    if (isset($headers['Authorization'])) {
        $bearer = explode(' ', $headers['Authorization']);
        $jwt = $bearer[sizeof($bearer) - 1] ?? null;
    }

    if (!$jwt) {
        http_response_code(401); // Unauthorized
        echo json_encode(['message' => 'Akses ditolak. Token tidak ditemukan.']);
        exit();
    }

    try {
        $token_jwt = new TokenJwt();
        $verifikasi_token = $token_jwt->verify($jwt);

        $user = new Users();
        $result = $user->findId($verifikasi_token['user_id']);
        if (!$result) {
            http_response_code(401);
            echo json_encode(['message' => 'users tidak ditemukan']);
            exit();
        }

        $controller = new BookController();
        $controller->createBook();

    } catch (Exception $e) {
        http_response_code(401); // Unauthorized
        echo json_encode(['message' => 'Token tidak valid: ' . $e->getMessage()]);
        exit();
    }
});

Route::put($base_url . '/api/book/{book_id}', function ($id) {
    // verifikasi token
    $headers = getallheaders();
    $jwt = null;

    if (isset($headers['Authorization'])) {
        $bearer = explode(' ', $headers['Authorization']);
        $jwt = $bearer[sizeof($bearer) - 1] ?? null;
    }

    if (!$jwt) {
        header('Content-Type: application/json');
        http_response_code(401); // Unauthorized
        echo json_encode(['message' => 'Akses ditolak. Token tidak ditemukan.']);
        exit();
    }

    try {
        $token_jwt = new TokenJwt();
        $verifikasi_token = $token_jwt->verify($jwt);

        $user = new Users();
        $result = $user->findId($verifikasi_token['user_id']);
        if (!$result) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['message' => 'users tidak ditemukan']);
            exit();
        }
    } catch (Exception $e) {
        http_response_code(401); // Unauthorized
        echo json_encode(['message' => 'Token tidak valid: ' . $e->getMessage()]);
        exit();
    }

    $controller = new BookController();
    $controller->putBook($id);
});

Route::put($base_url . '/api/book/{book_id}/read-book', function ($id) {
    // verifikasi token
    AuthMiddleware::authenticate();

    require_once __DIR__ . "/controllers/BookController.php";
    $controller = new BookController();
    $controller->putIsCompleteBook($id);
});

Route::post($base_url . '/api/book/{book_id}/file', function ($id) {
    // verifikasi token
    AuthMiddleware::authenticate();

    require_once __DIR__ . "/controllers/BookController.php";
    $controller = new BookController();
    $controller->updateFile($id);
});

Route::delete($base_url . '/api/book/{book_id}', function ($id) {
    // verifikasi token
    AuthMiddleware::authenticate();

    require_once __DIR__ . "/controllers/BookController.php";
    $controller = new BookController();
    $controller->deleteBook($id);
});

// Add more routes here
Route::run();