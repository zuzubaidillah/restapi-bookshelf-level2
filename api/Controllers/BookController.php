<?php

namespace Controllers;

use Config\TokenJwt;
use Model\Book;

class BookController
{
    public function getBooks()
    {
        // verifikasi token
        $headers = getallheaders();
        $bearer = explode(' ', $headers['Authorization']);
        $jwt = $bearer[sizeof($bearer) - 1] ?? null;
        // memanggil library tokenJWT
        $token_jwt = new TokenJwt();
        $verifikasi_token = $token_jwt->verify($jwt);
        $user_id = $verifikasi_token['user_id'];

        // validasi params status
        if (!isset($_GET['status'])) {
            // membuat response
            http_response_code(400);
            echo json_encode([
                'message' => 'Request Params status harus kirim'
            ]);
            exit();
        }
        $status = '';
        $filter_q = '';
        $status_param = $_GET['status'];

        if ($status_param === 'all' || $status_param === 'sudah' || $status_param === 'belum') {
            $status = $status_param;
        } else {
            // membuat response
            http_response_code(400);
            echo json_encode([
                'message' => 'Status harus berupa nilai (all, sudah, atau belum)'
            ]);
            exit();
        }

        if (isset($_GET['q'])) {
            $filter_q = $_GET['q'];
        }

        // membuat model untuk mengambil data buku
        $model_book = new Book();
        $result = $model_book->read($user_id, $status, $filter_q);

        // membuat response
        http_response_code(200);
        echo json_encode([
            'data' => $result
        ]);

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

        // validasi request client
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

        // Validasi 'isComplete' optional
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
            http_response_code(400); // Unauthorized
            echo json_encode([
                'errors' => $errors,
                'error' => 'request tidak lengkap',
                'message' => 'request tidak lengkap',
            ]);
            exit();
        }

        // cek title/judul yang sama
        $model_book = new Book();
        // cek title sama
        $find_book = $model_book->findByTitleRelasiUsers($_POST['title']);
        if ($find_book) {
            $user_name = strtoupper($find_book['users_name']);
            header('Content-Type: application/json');
            http_response_code(400); // Unauthorized
            echo json_encode([
                'message' => "Judul buku sudah ada. di buat oleh pengguna $user_name",
            ]);
            exit();
        }

        // cek ukuran file maksimal 2mb
        if (!$_FILES['file']['size'] || $_FILES['file']['size'] > (2 * 1024 * 1024)) {
            header('Content-Type: application/json');
            http_response_code(400); // Unauthorized
            echo json_encode([
                'message' => 'Ukuran file terlalu besar. Maksimal 2MB.',
            ]);
            exit();
        }

        // file harus jpg, jpeg, png, dan pdf
        $target_folder = 'uploads/';
        $nama_file = basename($_FILES['file']['name']);
        // Menghasilkan nama file unik
        $tipe_file = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $nama_file = uniqid() . '.' . $tipe_file;
        $kombinasi_target_file_name = $target_folder . $nama_file;

        // daftar type file yang diperbolehkan
        $tipe_file_sesuai = ['jpg', 'jpeg', 'png', 'pdf'];

        // cek tipe file
        if (!in_array(strtolower($tipe_file), $tipe_file_sesuai)) {
            header('Content-Type: application/json');
            http_response_code(400); // Unauthorized
            echo json_encode([
                'message' => 'Tipe file tidak diperbolehkan. Hanya jpg, jpeg, png, dan pdf.',
            ]);
            exit();
        }

        try {

            // cek apakah ada folder uploads, buatkan folder jika blm ada
            if (!file_exists($target_folder)) {
                mkdir($target_folder, 0777, true);
            }

            // lakukan upload file kedalam folder uploads
            if (move_uploaded_file($_FILES['file']['tmp_name'], $kombinasi_target_file_name)) {
                // lanjut proses simpan
                $title = $_POST['title'];
                $year = $_POST['year'];
                $author = $_POST['author'];
                $isComplete = isset($_POST['isComplete']) ? $_POST['isComplete'] : 0;

                $form_data = [
                    'title' => $title,
                    'year' => $year,
                    'author' => $author,
                    'is_complete' => $isComplete,
                    'file' => $kombinasi_target_file_name,
                    'created_at' => date('Y-m-d H:i:s'),
                    'creator_id' => $user_id
                ];

                // simpan data
                $model_book = new Book();
                $result = $model_book->save($form_data);
                header('Content-Type: application/json');
                http_response_code(200);
                // response data yang baru saja disimpan
                echo json_encode([
                    'data' => $result
                ]);
                exit();
            } else {
                header('Content-Type: application/json');
                http_response_code(400); // Unauthorized
                echo json_encode([
                    'message' => 'Maaf, terjadi kesalahan saat mengupload file',
                ]);
                exit();
            }

        } catch (\Exception $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
            exit();
        }


    }
}