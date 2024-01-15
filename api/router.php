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
            echo json_encode(["message"=>"routes tidak ditemukan"]);
            exit();
        }
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
            $controller->createBook($_FILES); // Menangani upload file
        } elseif ($uriSegments[$nomorUrutSetelahAPI] == 'book' && $_SERVER['REQUEST_METHOD'] == 'PUT') {
            include 'controllers/BookController.php';
            $controller = new BookController();
            $controller->createBook($_FILES); // Menangani upload file
        } elseif ($uriSegments[$nomorUrutSetelahAPI] == 'book' && $_SERVER['REQUEST_METHOD'] == 'DELETE') {
            include 'controllers/BookController.php';
            $controller = new BookController();
            $controller->createBook($_FILES); // Menangani upload file
        } else {
            echo json_encode(["message"=>"routes tidak ditemukan"]);
            exit();
        }
        // Implementasi PUT dan DELETE
    }
}
