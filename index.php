<?php
//require_once 'api/router.php';

// Membuat objek router
//$router = new Router();
//$router->route();
require_once __DIR__.'/api/Route.php';
$base_url = "/smkti/restApi-bookshelf-level2";

Route::get($base_url.'/api/book/{id}', function($id) {
    echo "Book API $id";
});

Route::get($base_url.'/api/book', function() {
    echo "Book API 2";
});

// Add more routes here

Route::run($base_url);