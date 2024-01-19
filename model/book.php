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
        $query = "SELECT id, title, year, author, isComplete, file, created_at, updated_at, deleted_at, creator_id, updator_id FROM " . $this->table_name . " WHERE deleted_at IS NULL";

        // Mempersiapkan statement query
        $this->db->query($query);
        return $this->db->resultSet();
    }

    public function findId($book_id)
    {
        $query = "SELECT id, title, year, author, isComplete, file, created_at, updated_at, deleted_at, creator_id, updator_id FROM " . $this->table_name . " WHERE id=:id and deleted_at IS NULL";
        $this->db->query($query);
        $this->db->bind('id', $book_id);
        return $this->db->single();
    }

    public function save($form_data)
    {
        $id = mt_rand(1, 9999);
        // id pada table book pastikan auto_increment
        $query = "INSERT INTO $this->table_name (id, title, year, author, isComplete, file, created_at) VALUES (:id, :title, :year, :author, :is_complete, :file, :created_at)";
        $this->db->query($query);
        $this->db->bind('id', $id);
        $this->db->bind('title', $form_data['title']);
        $this->db->bind('year', $form_data['year']);
        $this->db->bind('author', $form_data['author']);
        $this->db->bind('is_complete', $form_data['is_complete']);
        $this->db->bind('file', $form_data['file']);
        $this->db->bind('created_at', $form_data['created_at']);

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
