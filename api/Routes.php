<?php
require_once __DIR__ . '/../config/Route.php';
require_once __DIR__ . "/AuthMiddleware.php";
require_once __DIR__ . "/../config/helper.php";

use Config\Route;

$base_url = "/smkti/restApi-bookshelf-level2";

Route::get($base_url . '/api/mock/{code}', function ($code) {
    http_response_code((int)$code);
    echo json_encode(['message'=>'mock-api']);
});
Route::post($base_url . '/api/mock/{code}', function ($code) {
    http_response_code(400);
    echo json_encode(['message'=>'mock-api']);
});
Route::put($base_url . '/api/mock/{code}', function ($code) {
    http_response_code((int)$code);
    echo json_encode(['message'=>'mock-api']);
});
Route::delete($base_url . '/api/mock/{code}', function ($code) {
    http_response_code((int)$code);
    echo json_encode(['message'=>'mock-api']);
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
Route::get($base_url . '/api/book', function () {
    // verifikasi token
    AuthMiddleware::authenticate();

    require_once __DIR__ . "/controllers/BookController.php";
    $controller = new BookController();
    $controller->getBooks();
});

Route::get($base_url . '/api/book/{book_id}', function ($id) {
    // verifikasi token
    AuthMiddleware::authenticate();

    require_once __DIR__ . "/controllers/BookController.php";
    $controller = new BookController();
    $controller->getBookById($id);
});

Route::post($base_url . '/api/book', function () {
    // verifikasi token
    AuthMiddleware::authenticate();

    require_once __DIR__ . "/controllers/BookController.php";
    $controller = new BookController();
    $controller->createBook();
});

Route::put($base_url . '/api/book/{book_id}', function ($id) {
    // verifikasi token
    AuthMiddleware::authenticate();

    require_once __DIR__ . "/controllers/BookController.php";
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