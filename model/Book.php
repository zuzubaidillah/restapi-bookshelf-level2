<?php
namespace Model;
require_once __DIR__ . "/../config/Database.php";

use Config\{Database};

class Book
{
    private $table_name = "book";
    private $db;

    public $id;
    public $title;
    public $year;
    public $author;
    public $isComplete;
    public $file_path;
    public $created_at;
    public $updated_at;
    public $deleted_at;
    public $creator_id;
    public $updator_id;

    public function __construct()
    {
        $this->db = new Database();
    }

    // Method untuk membaca data buku
    public function read($user_id, $status)
    {
        $whereQuery = "";
        if ($status != 'all') {
            $whereQuery = "AND book.isComplete=:isComplete";
        }
        if (isset($_GET['q'])) {
            $whereQuery .= " AND book.title LIKE :title";
        }
        // Query untuk memilih semua kolom dari tabel book
        $query = "SELECT 
            book.id, book.title, book.year, book.author, book.isComplete, book.file, book.created_at, book.updated_at, book.deleted_at, book.creator_id, book.updator_id,
            users.name as creator_name
        FROM book
        INNER JOIN users on book.creator_id=users.id
        WHERE book.deleted_at IS NULL and book.creator_id=:creator_id $whereQuery";

        // Mempersiapkan statement query
        $this->db->query($query);
        $this->db->bind('creator_id', $user_id);
        if ($status != 'all') {
            $value = $status === 'sudah' ? 1 : 0;
            $this->db->bind('isComplete', $value);
        }
        if (isset($_GET['q'])) {
            $this->db->bind('title', "%".htmlspecialchars($_GET['q'], ENT_QUOTES)."%");
        }
        return $this->db->resultSet();
    }

    public function findId($book_id)
    {
        $query = "SELECT 
            id, title, year, author, isComplete, file, created_at, updated_at, deleted_at, creator_id, updator_id 
        FROM book
        WHERE id=:id";

        $this->db->query($query);
        $this->db->bind('id', $book_id);
        return $this->db->single();
    }

    public function findIdAndCreator($book_id, $user_id)
    {
        $query = "SELECT 
            book.id, 
            book.title, 
            book.year, 
            book.author, 
            book.isComplete, 
            book.file, 
            book.created_at, 
            book.updated_at, 
            book.deleted_at, 
            book.creator_id, 
            book.updator_id,
            users.name as creator_name
        FROM 
            book
        INNER JOIN 
            users ON book.creator_id = users.id
        WHERE 
            book.id = :id 
            AND book.deleted_at IS NULL 
            AND book.creator_id = :creator_id";

        $this->db->query($query);
        $this->db->bind('id', $book_id);
        $this->db->bind('creator_id', $user_id);
        return $this->db->single();
    }

    public function findByTitleRelasiUsers($book_title)
    {
        $query = "SELECT book.*, users.name as users_name 
        FROM book
        inner join users
        WHERE book.title=:title and book.deleted_at IS NULL";
        $this->db->query($query);
        $this->db->bind('title', $book_title);
        return $this->db->single();
    }

    public function findByTitleAndIdRelasiUsers($book_id, $book_title)
    {
        $query = "SELECT book.*, users.name as users_name 
        FROM book
        inner join users
        WHERE book.title=:title
          and book.id!=:id";
        $this->db->query($query);
        $this->db->bind('id', $book_id);
        $this->db->bind('title', $book_title);
        return $this->db->single();
    }

    public function save($form_data)
    {
        // id pada table book pastikan tidak auto_increment
        // karena id dibuat oleh sisi PHP
        $id = mt_rand(1, 9999);
        $query = "INSERT INTO $this->table_name 
        (id, title, year, author, isComplete, file, created_at, creator_id) 
        VALUES (:id, :title, :year, :author, :is_complete, :file, :created_at, :creator_id)";
        $this->db->query($query);
        $this->db->bind('id', $id);
        $this->db->bind('title', $form_data['title']);
        $this->db->bind('year', $form_data['year']);
        $this->db->bind('author', $form_data['author']);
        $this->db->bind('is_complete', $form_data['is_complete']);
        $this->db->bind('file', $form_data['file']);
        $this->db->bind('created_at', $form_data['created_at']);
        $this->db->bind('creator_id', $form_data['creator_id']);

        $res = $this->db->execute();
        if ($res) {
            // Mengambil data yang baru disimpan
            $this->db->query("SELECT * FROM $this->table_name WHERE id = :id");
            $this->db->bind('id', $id);
            return $this->db->single();
        } else {
            return false;
        }
    }

    public function update($id, $user_id, $form_data)
    {
        $query = "UPDATE book SET title=:title, year=:year, author=:author, updated_at=:updated_at, updator_id=:updator_id ";
        // berhubung request isComplete adalah opsional
        // harus kita berikan logika jika ada is_complete maka akan diikut sertakan update data book
        if (isset($form_data['isComplete'])) {
            $query .= ", isComplete=:isComplete "; // Tambahkan spasi sebelum klausa is_complete
        }
        $query .= " WHERE id=:id"; // Tambahkan spasi sebelum klausa WHERE
        $this->db->query($query);
        $this->db->bind('id', (int)$id);
        $this->db->bind('title', (string)$form_data['title']);
        $this->db->bind('year', (int)$form_data['year']);
        $this->db->bind('author', (string)$form_data['author']);
        if (isset($form_data['isComplete'])) {
            $this->db->bind('isComplete', (int)$form_data['isComplete']);
        }
        $tanggal_sekarang = date("Y-m-d H:i:s");
        $this->db->bind('updated_at', $tanggal_sekarang);
        $this->db->bind('updator_id', (int)$user_id);

        $res = $this->db->execute();
        if ($res === true) {
            // Mengambil data yang baru disimpan
            return $this->findId($id);
        } else {
            return false;
        }

    }

    public function updateIsComplete($id, $user_id, $isComplete)
    {
        // berhubung request isComplete required
        // harus kita berikan logika jika ada is_complete maka akan diikut sertakan update data book
        $query = "UPDATE book SET isComplete=:isComplete, updated_at=:updated_at, updator_id=:updator_id WHERE id=:id";
        $this->db->query($query);
        $this->db->bind('id', $id);
        $this->db->bind('isComplete', $isComplete);
        $tanggal_sekarang = date("Y-m-d H:i:s");
        $this->db->bind('updated_at', $tanggal_sekarang);
        $this->db->bind('updator_id', $user_id);

        $res = $this->db->execute();
        if ($res === true) {
            // Mengambil data yang baru disimpan
            return $this->findId($id);
        } else {
            return false;
        }

    }

    public function updateFile($id, $file)
    {
        $query = "UPDATE book SET file=:file WHERE id=:id";
        $this->db->query($query);
        $this->db->bind('id', $id);
        $this->db->bind('file', $file);

        $res = $this->db->execute();
        if ($res) {
            // Mengambil data yang baru disimpan
            $this->db->query("SELECT * FROM $this->table_name WHERE id = :id");
            $this->db->bind('id', $id);
            return $this->db->single();
        } else {
            return false;
        }
    }

    public function deleteById($book_id)
    {
        $query = "DELETE 
        FROM " . $this->table_name . " 
        WHERE id=:id";

        $this->db->query($query);
        $this->db->bind('id', $book_id);
        return $this->db->execute();
    }
}

?>
