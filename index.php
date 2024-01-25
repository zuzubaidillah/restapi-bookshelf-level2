<?php
// Mengaktifkan CORS untuk semua domain
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Memeriksa metode pra-flighted (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header('HTTP/1.1 200 OK');
    exit();
}
require_once __DIR__."/api/Routes.php";