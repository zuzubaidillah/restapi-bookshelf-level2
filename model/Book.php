<?php
namespace Model;
require_once __DIR__ . "/../config/Database.php";

use Config\{Database};
use PDOException;

class Book
{
    private $table_name = "book";
    private $db;

    public $id;
    public $title;
    public $year;
    public $author;
    public $isComplete;
    public $created_at;
    public $updated_at;
    public $deleted_at;
    public $creator_id;
    public $updator_id;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function read($user_id, $status, $filter_q)
    {
        $db = $this->table_name;
        $query_params = '';
        if ($status != 'all') {
            $query_params = " AND $db.isComplete = :is_complete ";
        }
        if ($filter_q != '') {
            $query_params = " AND $db.title LIKE :filter_q ";
        }
        $query = "SELECT $db.*, users.name as creator_name 
        FROM $db
               inner join users on users.id = $db.creator_id
         WHERE $db.creator_id=:user_id 
           and $db.deleted_at IS NULL $query_params";
        $this->db->query($query);
        $this->db->bind('user_id', $user_id);
        if ($status=='sudah') {
            $this->db->bind('is_complete', 1);
        }
        if ($status=='belum') {
            $this->db->bind('is_complete', 0);
        }
        if ($filter_q != '') {
            $this->db->bind('filter_q', "%$filter_q%");
        }
        return $this->db->resultSet(); // akan mengeluarkan nilai false / array
    }

    public function findByTitleRelasiUsers($book_title)
    {
        $query = "SELECT book.*, users.name as users_name 
        FROM book 
        inner join users on users.id = book.creator_id
        WHERE book.title=:title and book.deleted_at IS NULL";
        $this->db->query($query);
        $this->db->bind('title', "$book_title");
        return $this->db->single();
    }

    public function save($form_data)
    {
        // id dibuat oleh sisi php
        $id = mt_rand(1, 9999);

        $query = "INSERT INTO book 
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
            $this->db->query("SELECT * FROM book WHERE id = :id");
            $this->db->bind('id', $id);
            return $this->db->single();
        } else {
            return false;
        }
    }

}

?>
