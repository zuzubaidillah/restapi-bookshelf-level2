<?php
//require_once 'api/router.php';

// Membuat objek router
//$router = new Router();
//$router->route();
require_once __DIR__.'/api/Route.php';
require_once __DIR__ . "/api/AuthMiddleware.php";
require_once __DIR__ . "/config/helper.php";
$base_url = "/smkti/restApi-bookshelf-level2";

Route::get($base_url.'/api/book/{id}', function($id) {
    echo "Book API $id";
});

Route::get($base_url.'/api/book', function() {
    // verifikasi token
    AuthMiddleware::authenticate();

    require_once __DIR__."/api/controllers/BookController.php";
    $controller = new BookController();
    $controller->getBooks("");
});

// Add more routes here
Route::run();