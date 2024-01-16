<?php
header("Content-type: application/json");
class Router {
    public function route() {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uriSegments = explode('/', trim($uri, '/'));
        // SILAHKAN CEK TERLEBIH DAHULU
        //var_dump($uriSegments);
        //exit();

        $queryParams = $_GET; // Menangkap query parameters
        $nomorUrutSetelahAPI = 3;
        if (!isset($uriSegments[$nomorUrutSetelahAPI])) {
            http_response_code(401);
            echo json_encode(["message"=>"routes tidak ditemukan"]);
            exit();
        }


        /**
         * ROUTES BOOK
         */
        if ($uriSegments[$nomorUrutSetelahAPI] == 'book' && $_SERVER['REQUEST_METHOD'] == 'GET') {
            include 'controllers/BookController.php';
            $controller = new BookController();

            if (isset($uriSegments[$nomorUrutSetelahAPI+1])) {
                // GET api/book/{{book_id}}
                $controller->getBookById($uriSegments[$nomorUrutSetelahAPI+1], $queryParams);
            } else {
                // GET api/book
                $controller->getBooks($queryParams);
            }
        } elseif ($uriSegments[$nomorUrutSetelahAPI] == 'book' && $_SERVER['REQUEST_METHOD'] == 'POST') {
            include 'controllers/BookController.php';
            $controller = new BookController();
            $controller->createBook();
        } elseif ($uriSegments[$nomorUrutSetelahAPI] == 'book' && $_SERVER['REQUEST_METHOD'] == 'PUT') {
            include 'controllers/BookController.php';
            $controller = new BookController();
            $controller->putBook($_FILES);
        } elseif ($uriSegments[$nomorUrutSetelahAPI] == 'book' && $_SERVER['REQUEST_METHOD'] == 'DELETE') {
            include 'controllers/BookController.php';
            $controller = new BookController();
            $controller->deleteBook($_FILES);
        }



        /**
         * ROUTES USER
         */
        elseif ($uriSegments[$nomorUrutSetelahAPI] == 'auth' && $_SERVER['REQUEST_METHOD'] == 'GET') {
            include 'controllers/AuthController.php';
            $controller = new AuthController();

            if (isset($uriSegments[$nomorUrutSetelahAPI+1])) {
                // GET api/auth/registrasi
                if ($uriSegments[$nomorUrutSetelahAPI+1] == 'registrasi') {
                    $controller->registrasi();
                }

                // GET api/auth/login
                elseif ($uriSegments[$nomorUrutSetelahAPI+1] == 'login') {
                    $controller->login();
                }

                // GET api/auth/current
                elseif ($uriSegments[$nomorUrutSetelahAPI+1] == 'current') {
                    $controller->getByToken();
                }
            } else {
                // GET api/auth
                http_response_code(404);
                echo json_encode(["message"=>"routes tidak ditemukan"]);
                exit();
            }
        }



        /**
         * ROUTES USER
         */
        elseif ($uriSegments[$nomorUrutSetelahAPI] == 'user' && $_SERVER['REQUEST_METHOD'] == 'GET') {
            include 'controllers/UserController.php';
            $controller = new UserController();

            if (isset($uriSegments[$nomorUrutSetelahAPI+1])) {
                // GET api/user/{{book_id}}
                $controller->getUserById($uriSegments[$nomorUrutSetelahAPI+1], $queryParams);
            } else {
                // GET api/user
                $controller->getUsers($queryParams);
            }
        } elseif ($uriSegments[$nomorUrutSetelahAPI] == 'user' && $_SERVER['REQUEST_METHOD'] == 'POST') {
            include 'controllers/UserController.php';
            $controller = new UserController();
            $controller->registrasi();
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
            echo json_encode(["message"=>"routes tidak ditemukan"]);
            exit();
        }
        // Implementasi PUT dan DELETE
    }
}
