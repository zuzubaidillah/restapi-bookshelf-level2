<?php
header("Content-type: application/json");
require_once __DIR__ . "/AuthMiddleware.php";
require_once __DIR__."/../config/helper.php";

class Router
{
    public function route()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uriSegments = explode('/', trim($uri, '/'));
        // SILAHKAN CEK TERLEBIH DAHULU
        //var_dump($uriSegments);
        //exit();

        $queryParams = $_GET; // Menangkap query parameters
        $nomorUrutSetelahAPI = 3;
        if (!isset($uriSegments[$nomorUrutSetelahAPI])) {
            http_response_code(400);
            echo json_encode(["message" => "routes tidak ditemukan"]);
            exit();
        }


        /**
         * ROUTES BOOK
         */
        if ($uriSegments[$nomorUrutSetelahAPI] == 'book' && $_SERVER['REQUEST_METHOD'] == 'GET') {
            // verifikasi token
            AuthMiddleware::authenticate();

            include 'controllers/BookController.php';
            $controller = new BookController();

            // GET api/book/{{book_id}}
            if (isset($uriSegments[$nomorUrutSetelahAPI + 1])) {
                $controller->getBookById($uriSegments[$nomorUrutSetelahAPI + 1], $queryParams);
            } // GET api/book
            else {
                $controller->getBooks($queryParams);
            }
        } // POST api/book
        elseif ($uriSegments[$nomorUrutSetelahAPI] == 'book' && $_SERVER['REQUEST_METHOD'] == 'POST') {
            // verifikasi token
            AuthMiddleware::authenticate();

            include 'controllers/BookController.php';
            $controller = new BookController();
            $controller->createBook();
        } // PUT api/book/{{book_id}}
        elseif ($uriSegments[$nomorUrutSetelahAPI] == 'book' && $_SERVER['REQUEST_METHOD'] == 'PUT') {
            // verifikasi token
            AuthMiddleware::authenticate();

            include 'controllers/BookController.php';
            $controller = new BookController();
            $controller->putBook($uriSegments[$nomorUrutSetelahAPI + 1]);
        } // DELETE api/book
        elseif ($uriSegments[$nomorUrutSetelahAPI] == 'book' && $_SERVER['REQUEST_METHOD'] == 'DELETE') {
            // verifikasi token
            AuthMiddleware::authenticate();

            include 'controllers/BookController.php';
            $controller = new BookController();
            $controller->deleteBook($_FILES);
        } /**
         * ROUTES AUTH
         */
        elseif ($uriSegments[$nomorUrutSetelahAPI] == 'auth') {
            include 'controllers/AuthController.php';
            $controller = new AuthController();

            if (isset($uriSegments[$nomorUrutSetelahAPI + 1])) {
                // POST api/auth/registrasi
                if ($uriSegments[$nomorUrutSetelahAPI + 1] == 'registrasi' && $_SERVER['REQUEST_METHOD'] == 'POST') {
                    $controller->registrasi();
                } // POST api/auth/login
                elseif ($uriSegments[$nomorUrutSetelahAPI + 1] == 'login' && $_SERVER['REQUEST_METHOD'] == 'POST') {
                    $controller->login();
                } // GET api/auth/current
                elseif ($uriSegments[$nomorUrutSetelahAPI + 1] == 'current' && $_SERVER['REQUEST_METHOD'] == 'GET') {
                    $controller->getByToken();
                } // GET api/auth/....
                else {
                    http_response_code(404);
                    echo json_encode(["message" => "routes tidak ditemukan"]);
                    exit();
                }
            } else {
                // GET api/auth
                http_response_code(404);
                echo json_encode(["message" => "routes tidak ditemukan"]);
                exit();
            }
        } /**
         * ROUTES USER
         */
        elseif ($uriSegments[$nomorUrutSetelahAPI] == 'user' && $_SERVER['REQUEST_METHOD'] == 'GET') {
            include 'controllers/UserController.php';
            $controller = new UserController();

            if (isset($uriSegments[$nomorUrutSetelahAPI + 1])) {
                // GET api/user/{{book_id}}
                $controller->getUserById($uriSegments[$nomorUrutSetelahAPI + 1], $queryParams);
            } else {
                // GET api/user
                $controller->getUsers($queryParams);
            }
        } elseif ($uriSegments[$nomorUrutSetelahAPI] == 'user' && $_SERVER['REQUEST_METHOD'] == 'POST') {
            include 'controllers/UserController.php';
            $controller = new UserController();
            //$controller->registrasi();
        } elseif ($uriSegments[$nomorUrutSetelahAPI] == 'user' && $_SERVER['REQUEST_METHOD'] == 'PUT') {
            include 'controllers/UserController.php';
            $controller = new UserController();
            $controller->putUser();
        } elseif ($uriSegments[$nomorUrutSetelahAPI] == 'user' && $_SERVER['REQUEST_METHOD'] == 'DELETE') {
            include 'controllers/UserController.php';
            $controller = new UserController();
            $controller->deleteUser();
        } else {
            http_response_code(404);
            echo json_encode(["message" => "routes tidak ditemukan"]);
            exit();
        }
        // Implementasi PUT dan DELETE
    }
}
