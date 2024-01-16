<?php
require_once __DIR__ . "/../config/Database.php";
class Book {
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

    public function __construct() {
        $this->db = new Database();
    }

    // Method untuk membaca data buku
    public function read() {
        // Query untuk memilih semua kolom dari tabel book
        $query = "SELECT id, title, year, author, isComplete, created_at, updated_at, deleted_at, creator_id, updator_id FROM " . $this->table_name . " WHERE deleted_at IS NULL";

        // Mempersiapkan statement query
        $this->db->query($query);
        return $this->db->resultSet();
    }

    public function findId($book_id) {
        $query = "SELECT id, title, year, author, isComplete, created_at, updated_at, deleted_at, creator_id, updator_id FROM " . $this->table_name . " WHERE id=:id and deleted_at IS NULL";
        $this->db->query($query);
        $this->db->bind('id', $book_id);
        return $this->db->single();
    }


    // Method lainnya (create, read_one, update, delete)
    // ...
}
?>
