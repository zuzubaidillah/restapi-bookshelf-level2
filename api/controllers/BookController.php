<?php
require_once __DIR__ . "/../../model/book.php";

use Model\Book;

class BookController
{
    private $book;
    private $user;

    public function __construct()
    {
        $this->user = null;
    }

    public function test()
    {
        var_dump("BookController@test");
        exit();
    }

    public function getBooks($queryParams)
    {
        // Logika untuk mengambil daftar buku
        $book = new Book();
        $dataBook = $book->read();
        $data = ['books' => $dataBook];
        $this->sendJson($data, 200);
    }

    public function getBookById($book_id)
    {
        $user_id = (auth())->id;
        // Logika untuk mengambil buku berdasarkan ID
        $book = new Book();
        $data_book = $book->findIdAndCreator($book_id, $user_id);
        if (!$data_book) {
            $this->sendJson([
                "message" => "Book id $book_id tidak ditemukan"
            ], 400);
            exit();
        }
        $data = ['data' => $data_book];
        $this->sendJson($data, 200);
    }

    private function validasiCreate()
    {
        $errors = [];
        // Validasi 'title' required
        if (empty($_POST['title'])) {
            $errors['title'] = 'Judul buku diperlukan.';
        }

        // Validasi 'year' required
        if (empty($_POST['year']) || !is_numeric($_POST['year'])) {
            $errors['year'] = 'Tahun harus berupa angka.';
        }

        // Validasi 'author' required
        if (empty($_POST['author'])) {
            $errors['author'] = 'Nama penulis diperlukan.';
        }

        // Validasi 'isComplete' required
        if (isset($_POST['isComplete'])) {
            if ($_POST['isComplete'] != '1' && $_POST['isComplete'] != '0') {
                $errors['isComplete'] = 'Status isComplete harus 1 atau 0.';
            }
        }

        // Validasi 'file' required
        if (!isset($_FILES['file'])) {
            $errors['file'] = 'File harus diisi.';
        } else if ($_FILES['file']['name'] === "") {
            $errors['file'] = 'File harus diisi.';
        }

        return $errors;
    }

    public function createBook()
    {
        // validasi request
        $validasi = $this->validasiCreate();
        if ($validasi) {
            $this->sendJson([
                "errors" => $validasi,
                "error" => "request tidak lengkap"
            ], 400);
            exit();
        }

        // cek title sama
        $book = new Book();
        $find_book = $book->findTitleRelasiUser($_POST['title']);
        if ($find_book) {
            $user_name = strtoupper($find_book['users_name']);
            $this->sendJson([
                "message" => "Judul buku sudah ada. di buat oleh pengguna $user_name",
            ], 400);
            exit();
        }


        // Mengecek ukuran file
        if (!$_FILES["file"]["size"] || $_FILES["file"]["size"] > (2 * 1024 * 1024)) {
            $this->sendJson([
                "message" => "Ukuran file terlalu besar. Maksimal 2MB."
            ], 400);
            exit();
        }

        // Direktori tujuan untuk menyimpan file
        $targetDir = "uploads/";
        $fileName = basename($_FILES["file"]["name"]);
        // Menghasilkan nama file unik
        $fileType = pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
        $fileName = uniqid() . '.' . $fileType;
        $targetFilePath = $targetDir . $fileName;

        // Daftar ekstensi file yang diperbolehkan
        $allowedTypes = ['jpg', 'jpeg', 'png', 'pdf'];

        // Mengecek tipe file
        if (!in_array(strtolower($fileType), $allowedTypes)) {
            $this->sendJson([
                "message" => "Tipe file tidak diperbolehkan. Hanya jpg, jpeg, png, dan pdf."
            ], 400);
            exit();
        }

        try {
            // Cek apakah direktori uploads ada, jika tidak buat direktori tersebut
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            // Upload file ke direktori tujuan
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
                $title = $_POST['title'];
                $year = $_POST['year'];
                $author = $_POST['author'];
                $isComplete = isset($_POST['isComplete']) ? $_POST['isComplete'] : 0;
                $file = $targetDir . $fileName;
                $form_data = [
                    "title" => $title,
                    "year" => $year,
                    "author" => $author,
                    "is_complete" => $isComplete,
                    "file" => $file,
                    "created_at" => date("Y-m-d H:i:s"),
                    "creator_id" => (auth())->id
                ];
                $book = new Book();
                $result = $book->save($form_data);
                $data = ['data' => $result];
                $this->sendJson($data, 201);
            } else {
                $data = ['message' => 'Maaf, terjadi kesalahan saat mengupload file.'];
                $this->sendJson($data, 400);
            }
        } catch (Exception $e) {
            $data = ['message' => 'Terjadi kesalahan: ' . $e->getMessage()];
            $this->sendJson($data, 200); // 500 Internal Server Error
        }
    }

    private function validasiPut()
    {
        $errors = [];
        // Validasi 'title' required
        if (empty($_POST['title'])) {
            $errors['title'] = 'Judul buku diperlukan.';
        }

        // Validasi 'year' required
        if (empty($_POST['year']) || !is_numeric($_POST['year'])) {
            $errors['year'] = 'Tahun harus berupa angka.';
        }

        // Validasi 'author' required
        if (empty($_POST['author'])) {
            $errors['author'] = 'Nama penulis diperlukan.';
        }

        // Validasi 'isComplete' required
        if (isset($_POST['isComplete'])) {
            if ($_POST['isComplete'] != '1' && $_POST['isComplete'] != '0') {
                $errors['isComplete'] = 'Status isComplete harus 1 atau 0.';
            }
        }

        // Validasi 'file' optional
        if (isset($_FILES['file']) && $_FILES['file']['name'] === "") {
            $errors['file'] = 'File harus diisi.';
        }

        var_dump($_FILES, $_POST);
        exit();

        return $errors;
    }

    public function putBook($book_id)
    {
        // validasi request
        $validasi = $this->validasiPut();
        if ($validasi) {
            $this->sendJson([
                "errors" => $validasi,
                "error" => "request tidak lengkap"
            ], 400);
            exit();
        }
        // Logika untuk menangani upload file dan membuat buku baru
        // Proses $fileData
        $data = ['message' => 'Buku berhasil diupdate book_id ' . $book_id];
        $this->sendJson($data, 200);
    }

    public function updateFile($book_id)
    {
        // validasi request
        $validasi = "";
        if ($validasi) {
            $this->sendJson([
                "errors" => $validasi,
                "error" => "request tidak lengkap"
            ], 400);
            exit();
        }
        // Logika untuk menangani upload file dan membuat buku baru
        // Proses $fileData
        $data = ['message' => 'Buku berhasil diupdate book_id ' . $book_id];
        $this->sendJson($data, 200);
    }

    public function deleteBook($book_id)
    {
        // memvalidasi parameter $book_id
        if (!$book_id) {
            $this->sendJson([
                "message" => "request tidak lengkap"
            ], 200);
            exit();
        }

        $model_book = new Book();

        // validasi $book_id dengan data di table book
        $book = $model_book->findId($book_id);
        if ($book === false) {
            $this->sendJson([
                "message" => "Book id $book_id tidak ditemukan"
            ], 200);
            exit();
        }

        // proses hapus data secara permanen
        $book = $model_book->deleteById($book_id);
        if ($book === true) {
            $data = ['message' => 'Buku berhasil dihapus '];
            $code = 200;
        } else {
            $data = ['message' => 'Buku berhasil dihapus '];
            $code = 400;
        }
        $this->sendJson($data, $code);
    }


    private function sendJson($data, $code)
    {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode($data);
    }
}
