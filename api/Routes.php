<?php
//file Routes.php digunakan untuk list sebuah endpoint
require_once __DIR__ . '/../config/Route.php';
require_once __DIR__ . '/Controllers/AuthController.php';
require_once __DIR__ . '/../config/TokenJwt.php';
require_once __DIR__ . '/../model/Users.php';

use Config\Route;
use Config\TokenJwt;
use Controllers\AuthController;
use Model\Users;

$base_url = "/smkti/restapi-bookshelf-level2-action-video";

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


// Add more routes here
Route::run();
