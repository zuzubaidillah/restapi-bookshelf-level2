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

    public function getBookById($bookId, $queryParams) {
        // Logika untuk mengambil buku berdasarkan ID
        $data = ['book' => 'Detail Buku']; // Data contoh
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
