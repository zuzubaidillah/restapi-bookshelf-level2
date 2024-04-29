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
    public $file;
    public $created_at;
    public $updated_at;
    public $deleted_at;
    public $creator_id;
    public $updator_id;
    public $creator_name;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function read($user_id)
    {
        try {
            $query = "SELECT book.*, users.name as creator_name
            FROM book
            INNER JOIN users ON users.id = book.creator_id
            WHERE book.deleted_at IS NULL AND book.creator_id = :creator_id
            ORDER BY book.created_at DESC";

            $this->db->query($query);
            $this->db->bind('creator_id', $user_id);
            return $this->db->single();
        } catch (PDOException $exception) {
            return $exception;
        }
    }
}

?>
