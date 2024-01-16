<?php
require_once __DIR__."/../../model/book.php";
class BookController {
    private $book;
    public function __construct()
    {
    }

    public function getBooks($queryParams) {
        // Logika untuk mengambil daftar buku
        $book = new Book();
        $dataBook = $book->read();
        $data = ['books' => $dataBook];
        $this->sendJson($data);
    }

    public function getBookById($book_id, $queryParams) {
        // Logika untuk mengambil buku berdasarkan ID
        $book = new Book();
        $data_book = $book->findId($book_id);
        if (!$data_book) {
            http_response_code(400);
            $this->sendJson([
                "message" => "Book id $book_id tidak ditemukan"
            ]);
           exit();
        }
        $data = ['data' => $data_book];
        $this->sendJson($data);
    }

    public function createBook($fileData) {
        // Logika untuk menangani upload file dan membuat buku baru
        // Proses $fileData
        $data = ['message' => 'Buku berhasil dibuat'];
        $this->sendJson($data);
    }

    public function putBook($fileData) {
        // Logika untuk menangani upload file dan membuat buku baru
        // Proses $fileData
        $data = ['message' => 'Buku berhasil diupdate'];
        $this->sendJson($data);
    }

    public function deleteBook($fileData) {
        // Logika untuk menangani upload file dan membuat buku baru
        // Proses $fileData
        $data = ['message' => 'Buku berhasil dihapus'];
        $this->sendJson($data);
    }


    private function sendJson($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}
