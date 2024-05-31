<?php
require_once __DIR__ . "/../../model/Book.php";

use Model\Book;
use Config\TokenJwt;

class BookController
{
    private $book;
    private $user;

    public function __construct()
    {
        $this->user = null;
    }

    public function getBooks()
    {
        // Mendapatkan token dari header
        $headers = getallheaders();
        $jwt = null;

        if (isset($headers['Authorization'])) {
            $bearer = explode(' ', $headers['Authorization']);
            $index_token = (sizeof($bearer) - 1);
            $jwt = $bearer[$index_token] ?? null;
        }

        if (!$jwt) {
            echo json_encode(['message' => 'Akses ditolak. Token tidak ditemukan.']);
            http_response_code(401); // Unauthorized
            exit();
        }

        $status = "";
        $filter_title = null;

        // Pengecekan apakah status diatur dalam URL
        if (isset($_GET['status'])) {
            // Pengecekan nilai status
            $status_param = $_GET['status'];

            // Hanya set status jika nilainya adalah all, belum, atau sudah
            if ($status_param === 'all' || $status_param === 'belum' || $status_param === 'sudah') {
                $status = $status_param;
            } else {
                $this->sendJson([
                    'message' => 'parmas status tidak sesuai. harus (all,belum,sudah)'
                ], 400);
                exit();
            }
        }

        // Pengecekan apakah status diatur dalam URL
        if (isset($_GET['q'])) {
            // Pengecekan nilai status
            $filter_title = $_GET['q'];
        }

        $token_jwt = new TokenJwt();
        $verifikasi_token = $token_jwt->verify($jwt);

        $model_book = new Book();
        $result = $model_book->read($verifikasi_token['user_id'], $status, $filter_title);
        echo json_encode([
            'data' => $result
        ]);
        http_response_code(200); // OK
    }

    public function getBookById($book_id)
    {
        // verifikasi token
        $headers = getallheaders();
        $bearer = explode(' ', $headers['Authorization']);
        $jwt = $bearer[sizeof($bearer) - 1] ?? null;
        // memanggil library tokenJWT
        $token_jwt = new TokenJwt();
        $verifikasi_token = $token_jwt->verify($jwt);
        $user_id = $verifikasi_token['user_id'];

        // Logika untuk mengambil buku berdasarkan ID
        $model_book = new Book();
        $detail_book = $model_book->findIdAndCreator($book_id, $user_id);
        if (!$detail_book) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode([
                "message" => "Book id $book_id tidak ditemukan"
            ]);
            exit();
        }

        $data = [
            'data' => $detail_book
        ];
        header('Content-Type: application/json');
        http_response_code(200);
        echo json_encode($data);
        exit();
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
        // verifikasi token
        $headers = getallheaders();
        $bearer = explode(' ', $headers['Authorization']);
        $jwt = $bearer[sizeof($bearer) - 1] ?? null;
        // memanggil library tokenJWT
        $token_jwt = new TokenJwt();
        $verifikasi_token = $token_jwt->verify($jwt);
        $user_id = $verifikasi_token['user_id'];

        // validasi request
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
        if (count($errors) >= 1) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode([
                "errors" => $errors,
                "error" => "request tidak lengkap",
                "message" => "request tidak lengkap"
            ]);
            exit();
        }

        // buat object memanggil model Book
        $model_book = new Book();
        // cek title sama
        $find_book = $model_book->findByTitleRelasiUsers($_POST['title']);
        if ($find_book) {
            $user_name = strtoupper($find_book['users_name']);
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode([
                "message" => "Judul buku sudah ada. di buat oleh pengguna $user_name",
            ]);
            exit();
        }

        // Mengecek ukuran file
        if (!$_FILES["file"]["size"] || $_FILES["file"]["size"] > (2 * 1024 * 1024)) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode([
                "message" => "Ukuran file terlalu besar. Maksimal 2MB."
            ]);
            exit();
        }

        // Direktori tujuan untuk menyimpan file
        $target_folder = "uploads/";
        $nama_file = basename($_FILES["file"]["name"]);
        // Menghasilkan nama file unik
        $tipe_file = pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
        $nama_file = uniqid() . '.' . $tipe_file;
        $kombinasi_target_file_name = $target_folder . $nama_file;

        // Daftar ekstensi file yang diperbolehkan
        $tipe_file_sesui = ['jpg', 'jpeg', 'png', 'pdf'];

        // Mengecek tipe file
        if (!in_array(strtolower($tipe_file), $tipe_file_sesui)) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode([
                "message" => "Tipe file tidak diperbolehkan. Hanya jpg, jpeg, png, dan pdf."
            ]);
            exit();
        }

        try {
            // Cek apakah direktori uploads ada, jika tidak buat direktori tersebut
            if (!file_exists($target_folder)) {
                mkdir($target_folder, 0777, true);
            }

            // Upload file ke direktori tujuan
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $kombinasi_target_file_name)) {
                $title = $_POST['title'];
                $year = $_POST['year'];
                $author = $_POST['author'];
                $isComplete = isset($_POST['isComplete']) ? $_POST['isComplete'] : 0;
                $form_data = [
                    "title" => $title,
                    "year" => $year,
                    "author" => $author,
                    "is_complete" => $isComplete,
                    "file" => $kombinasi_target_file_name,
                    "created_at" => date("Y-m-d H:i:s"),
                    "creator_id" => $user_id
                ];
                $book = new Book();
                $result = $book->save($form_data);
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode([
                    'data' => $result
                ]);
                exit();
            } else {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode([
                    'message' => 'Maaf, terjadi kesalahan saat mengupload file.'
                ]);
                exit();
            }
        } catch (Exception $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
            exit();
        }
    }

    private function validasiPut($request)
    {
        /**
         * title: required,
         * year: required,
         * author: required,
         */

        $errors = [];
        // Validasi 'title' required
        if (empty($request['title'])) {
            $errors['title'] = 'Judul buku diperlukan.';
        }

        // Validasi 'year' required
        if (empty($request['year']) || !is_numeric($request['year'])) {
            $errors['year'] = 'Tahun harus berupa angka.';
        }

        // Validasi 'author' required
        if (empty($request['author'])) {
            $errors['author'] = 'Nama penulis diperlukan.';
        }

        return $errors;
    }

    public function putBook($book_id)
    {
        // verifikasi token
        $headers = getallheaders();
        $bearer = explode(' ', $headers['Authorization']);
        $jwt = $bearer[sizeof($bearer) - 1] ?? null;
        // memanggil library tokenJWT
        $token_jwt = new TokenJwt();
        $verifikasi_token = $token_jwt->verify($jwt);
        $user_id = $verifikasi_token['user_id'];

        // cara mengambil request client yang dikirim dengan type json
        $request = json_decode(file_get_contents('php://input'), true);

        // validasi request

        $errors = [];
        // Validasi 'title' required
        if (empty($request['title'])) {
            $errors['title'] = 'Judul buku diperlukan.';
        }

        // Validasi 'year' required
        if (empty($request['year']) || !is_numeric($request['year'])) {
            $errors['year'] = 'Tahun harus berupa angka.';
        }

        // Validasi 'author' required
        if (empty($request['author'])) {
            $errors['author'] = 'Nama penulis diperlukan.';
        }

        // variable errors kita lakukan pengecekan
        if (count($errors) >= 1) {
            $this->sendJson([
                "errors" => $errors,
                "error" => "request tidak lengkap",
                "message" => "request tidak lengkap"
            ], 400);
            exit();
        }

        // memanggil object Book (model dari table book)
        $model_book = new Book();

        // cek title sama dengan yang ada di table book, kecuali book_id yang sama tidak papa.
        $find_book_id = $model_book->findId($book_id);
        if (!$find_book_id) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode([
                "message" => "Buku id $book_id tidak ditemukan",
            ]);
            exit();
        }

        // cek title sama dengan yang ada di table book, kecuali book_id yang sama tidak papa.
        $find_book = $model_book->findByTitleAndIdRelasiUsers($book_id, $request['title']);
        if ($find_book) {
            $user_name = strtoupper($find_book['users_name']);
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode([
                "message" => "Judul buku sudah ada. di buat oleh pengguna $user_name",
            ]);
            exit();
        }

        $form_data = [
            "title" => $request['title'],
            "year" => $request['year'],
            "author" => $request['author'],
        ];

        // proses update book
        $book = $model_book->update($book_id, $user_id, $form_data);
        if ($book) {
            $data = [
                'message' => 'Buku berhasil diubah',
                "data" => $book
            ];
            $code = 200;
        } else {
            $data = ['message' => 'Buku Gagal diubah'];
            $code = 500;
        }

        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode($data);
        exit();
    }

    public function putIsCompleteBook($book_id)
    {
        // verifikasi token
        $headers = getallheaders();
        $bearer = explode(' ', $headers['Authorization']);
        $jwt = $bearer[sizeof($bearer) - 1] ?? null;
        // memanggil library tokenJWT
        $token_jwt = new TokenJwt();
        $verifikasi_token = $token_jwt->verify($jwt);
        $user_id = $verifikasi_token['user_id'];

        // mendapatkan request dari client (yang dikirim menggunakan JSON)
        $request = json_decode(file_get_contents('php://input'), true);

        $errors = [];
        // Validasi 'isComplete' required
        if (!isset($request['isComplete']) && empty($request['isComplete'])) {
            $errors['isComplete'] = 'komplete membaca diperlukan.';
        } else {
            if ($request['isComplete'] != '1' && $request['isComplete'] != '0') {
                $errors['isComplete'] = 'Status isComplete harus 1 atau 0.';
            }
        }

        if (count($errors) >= 1) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode([
                "errors" => $errors,
                "error" => "request tidak lengkap",
                "message" => "request tidak lengkap"
            ]);
            exit();
        }

        // memanggil object Book (model dari table book)
        $model_book = new Book();

        // cek title sama dengan yang ada di table book, kecuali book_id yang sama tidak papa.
        $find_book_id = $model_book->findId($book_id);
        if (!$find_book_id) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode([
                "message" => "Buku id $book_id tidak ditemukan",
            ]);
            exit();
        }

        $isComplete = $request['isComplete'];

        // proses update book
        $book = $model_book->updateIsComplete($book_id, $user_id, $isComplete);
        if ($book) {
            $message = $request['isComplete'] == 1 ? 'Buku telah komplete dibaca' : 'Buku diubah ke belum dibaca';
            $data = [
                'message' => $message,
                "data" => $book
            ];
            $code = 200;
        } else {
            $data = ['message' => 'Buku Gagal diubah'];
            $code = 500;
        }

        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode($data);
        exit();
    }

    public function updateFile($book_id)
    {
        // Validasi 'file' optional
        if (!isset($_FILES['file']) || $_FILES['file']['name'] === "") {
            $errors['file'] = 'File harus diisi.';
            $this->sendJson([
                "errors" => $errors,
                "error" => "request tidak lengkap",
                "message" => "request tidak lengkap"
            ], 400);
            exit();
        }

        // memanggil object Book (model dari table book)
        $model_book = new Book();

        // mengambil data by id
        $find_book_id = $model_book->findId($book_id);
        if (!$find_book_id) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode([
                "message" => "Buku id $book_id tidak ditemukan",
            ]);
            exit();
        }

        // Hapus file lama jika ada
        $oldFilePath = $find_book_id['file'];
        if (file_exists($oldFilePath)) {
            unlink($oldFilePath);
        }

        $target_folder = "uploads/";
        $nama_file = basename($_FILES["file"]["name"]);
        // Menghasilkan nama file unik
        $tipe_file = pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);
        $nama_file = uniqid() . '.' . $tipe_file;
        $kombinasi_target_file_name = $target_folder . $nama_file;

        // Daftar ekstensi file yang diperbolehkan
        $tipe_file_sesui = ['jpg', 'jpeg', 'png', 'pdf'];

        // Mengecek tipe file
        if (!in_array(strtolower($tipe_file), $tipe_file_sesui)) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode([
                "message" => "Tipe file tidak diperbolehkan. Hanya jpg, jpeg, png, dan pdf."
            ]);
            exit();
        }

        // Cek apakah direktori uploads ada, jika tidak buat direktori tersebut
        if (!file_exists($target_folder)) {
            mkdir($target_folder, 0777, true);
        }

        try {
            // Upload file ke direktori tujuan
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $kombinasi_target_file_name)) {
                // proses simpan data
                $book = new Book();
                $result = $book->updateFile($book_id, $kombinasi_target_file_name);
                $data = [
                    'data' => $result
                ];
                header('Content-Type: application/json');
                http_response_code(200);
                echo json_encode($data);
                exit();
            } else {
                $data = [
                    'message' => 'Maaf, terjadi kesalahan saat mengupload file.'
                ];
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode($data);
                exit();
            }
        } catch (Exception $e) {
            $data = [
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode($data);
            exit();
        }
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
                "message" => "Buku id $book_id tidak ditemukan"
            ], 200);
            exit();
        }

        // proses hapus data secara permanen
        $book = $model_book->deleteById($book_id);
        if ($book === true) {
            $data = ['message' => 'Buku berhasil dihapus '];
            $code = 200;
        } else {
            $data = ['message' => 'Buku Gagal dihapus '];
            $code = 500;
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
