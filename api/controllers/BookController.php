<?php
require_once __DIR__ . "/../../model/book.php";

class BookController
{
    private $book;

    public function __construct()
    {
    }

    public function getBooks($queryParams)
    {
        // Logika untuk mengambil daftar buku
        $book = new Book();
        $dataBook = $book->read();
        $data = ['books' => $dataBook];
        $this->sendJson($data, 200);
    }

    public function getBookById($book_id, $queryParams)
    {
        // Logika untuk mengambil buku berdasarkan ID
        $book = new Book();
        $data_book = $book->findId($book_id);
        if (!$data_book) {
            $this->sendJson([
                "message" => "Book id $book_id tidak ditemukan"
            ], 400);
            exit();
        }
        $data = ['data' => $data_book];
        $this->sendJson($data, 200);
    }

    private function validasi()
    {
        $errors = [];
        // Validasi 'title'
        if (empty($_POST['title'])) {
            $errors['title'] = 'Judul buku diperlukan.';
        }

        // Validasi 'year'
        if (empty($_POST['year']) || !is_numeric($_POST['year'])) {
            $errors['year'] = 'Tahun harus berupa angka.';
        }

        // Validasi 'author'
        if (empty($_POST['author'])) {
            $errors['author'] = 'Nama penulis diperlukan.';
        }

        // Validasi 'isComplete'
        if (isset($_POST['isComplete'])) {
            if ($_POST['isComplete'] != '1' && $_POST['isComplete'] != '0') {
                $errors['isComplete'] = 'Status isComplete harus 1 atau 0.';
            }
        }

        // Validasi 'isComplete'
        if (!isset($_FILES['file'])) {
            $errors['file'] = 'File harus diisi.';
        }

        return $errors;
    }

    public function createBook()
    {
        // validasi request
        $validasi = $this->validasi();
        if ($validasi) {
            $this->sendJson([
                "errors" => $validasi,
                "error" => "request tidak lengkap"
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

        try {
            // Cek apakah direktori uploads ada, jika tidak buat direktori tersebut
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            // Upload file ke direktori tujuan
            if(move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)){
                $title = $_POST['title'];
                $year = $_POST['year'];
                $author = $_POST['author'];
                $isComplete = isset($_POST['isComplete']) ? $_POST['isComplete'] : 0;
                $file = $targetDir.$fileName;
                $form_data = [
                    "title" => $title,
                    "year" => $year,
                    "author" => $author,
                    "is_complete" => $isComplete,
                    "file" => $file,
                    "created_at" => date("Y-m-d H:i:s"),
                ];
                $book = new Book();
                $result = $book->save($form_data);
                $data = ['data' => $result];
                $this->sendJson($data, 201);
            } else{
                $data = ['message' => 'Maaf, terjadi kesalahan saat mengupload file.'];
                $this->sendJson($data, 400);
            }
        } catch (Exception $e) {
            $data = ['message' => 'Terjadi kesalahan: ' . $e->getMessage()];
            $this->sendJson($data, 200); // 500 Internal Server Error
        }
    }

    public function putBook($fileData)
    {
        // Logika untuk menangani upload file dan membuat buku baru
        // Proses $fileData
        $data = ['message' => 'Buku berhasil diupdate'];
        $this->sendJson($data, 200);
    }

    public function deleteBook($fileData)
    {
        // Logika untuk menangani upload file dan membuat buku baru
        // Proses $fileData
        $data = ['message' => 'Buku berhasil dihapus'];
        $this->sendJson($data, 200);
    }


    private function sendJson($data, $code)
    {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode($data);
    }
}
