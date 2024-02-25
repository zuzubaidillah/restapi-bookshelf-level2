<?php
//file Routes.php digunakan untuk list sebuah endpoint
require_once __DIR__ . '/../config/Route.php';
require_once __DIR__ . '/Controllers/AuthController.php';

use Config\Route;
use Controllers\AuthController;

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


// Add more routes here
Route::run();
