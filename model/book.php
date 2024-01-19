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
    public function read()
    {
        // Query untuk memilih semua kolom dari tabel book
        $query = "SELECT 
            book.id, book.title, book.year, book.author, book.isComplete, book.file, book.created_at, book.updated_at, book.deleted_at, book.creator_id, book.updator_id,
            users.name as creator_name
        FROM book
        INNER JOIN users on book.creator_id=users.id
        WHERE book.deleted_at IS NULL";

        // Mempersiapkan statement query
        $this->db->query($query);
        return $this->db->resultSet();
    }

    public function findId($book_id)
    {
        $query = "SELECT 
            id, title, year, author, isComplete, file, created_at, updated_at, deleted_at, creator_id, updator_id 
        FROM " . $this->table_name . " 
        WHERE id=:id and deleted_at IS NULL";

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

    public function findTitleRelasiUser($book_title)
    {
        $query = "SELECT book.*, users.name as users_name FROM " . $this->table_name . " 
        inner join users on book.creator_id=users.id
        WHERE book.title=:title and book.deleted_at IS NULL";
        $this->db->query($query);
        $this->db->bind('title', $book_title);
        return $this->db->single();
    }

    public function save($form_data)
    {
        $id = mt_rand(1, 9999);
        // id pada table book pastikan auto_increment
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
}

?>
