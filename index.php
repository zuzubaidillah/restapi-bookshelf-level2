<?php
//require_once 'api/router.php';

// Membuat objek router
//$router = new Router();
//$router->route();
require_once __DIR__ . '/api/Route.php';
require_once __DIR__ . "/api/AuthMiddleware.php";
require_once __DIR__ . "/config/helper.php";
$base_url = "/smkti/restApi-bookshelf-level2";

/**
 * ROUTES AUTH
 */
Route::post($base_url . '/api/auth/registrasi', function () {
    require_once __DIR__ . '/api/controllers/AuthController.php';
    $controller = new AuthController();
    $controller->registrasi();
});

Route::post($base_url . '/api/auth/login', function () {
    require_once __DIR__ . '/api/controllers/AuthController.php';
    $controller = new AuthController();
    $controller->login();
});

Route::get($base_url . '/api/auth/current', function () {
    require_once __DIR__ . '/api/controllers/AuthController.php';
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

    require_once __DIR__ . "/api/controllers/BookController.php";
    $controller = new BookController();
    $controller->getBooks("");
});

Route::get($base_url . '/api/book/{book_id}', function ($id) {
    // verifikasi token
    AuthMiddleware::authenticate();

    require_once __DIR__ . "/api/controllers/BookController.php";
    $controller = new BookController();
    $controller->getBookById($id);
});

Route::post($base_url . '/api/book', function () {
    // verifikasi token
    AuthMiddleware::authenticate();

    require_once __DIR__ . "/api/controllers/BookController.php";
    $controller = new BookController();
    $controller->createBook();
});

Route::put($base_url . '/api/book/{book_id}', function ($id) {
    // verifikasi token
    AuthMiddleware::authenticate();

    require_once __DIR__ . "/api/controllers/BookController.php";
    $controller = new BookController();
    $controller->putBook($id);
});

Route::post($base_url . '/api/book/{book_id}/file', function ($id) {
    // verifikasi token
    AuthMiddleware::authenticate();

    require_once __DIR__ . "/api/controllers/BookController.php";
    $controller = new BookController();
    $controller->updateFile($id);
});

Route::delete($base_url . '/api/book/{book_id}', function ($id) {
    // verifikasi token
    AuthMiddleware::authenticate();

    require_once __DIR__ . "/api/controllers/BookController.php";
    $controller = new BookController();
    $controller->deleteBook($id);
});

// Add more routes here
Route::run();