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

    public function read($user_id, $status, $filter_title)
    {
        $whereQuery = "";
        if ($status != 'all') {
            $whereQuery = "AND book.isComplete=:isComplete";
        }
        if ($filter_title) {
            $whereQuery .= " AND book.title LIKE :title";
        }
        try {
            $query = "SELECT book.*, users.name as creator_name
            FROM book
            INNER JOIN users ON users.id = book.creator_id
            WHERE book.deleted_at IS NULL AND book.creator_id = :creator_id
            $whereQuery
            ORDER BY book.created_at DESC";

            // kita lakukan SQL injection
            // agar proses filter aman dari nilai yang tidak diinginkan seperti tanda '
            $filter_title = htmlspecialchars($filter_title, ENT_QUOTES);

            $this->db->query($query);
            $this->db->bind('creator_id', $user_id);
            $this->db->bind('status', $status);
            $this->db->bind('filter_title', "%$filter_title%");
            if ($status != 'all') {
                if ($status == 'sudah') {
                    $value = 1;
                }else if ($status == 'belum') {
                    $value = 0;
                }
                $this->db->bind('isComplete', $value);
            }
            if ($filter_title) {
                $this->db->bind('filter_title', "%$filter_title%");
            }
            return $this->db->resultSet();
        } catch (PDOException $exception) {
            return $exception;
        }
    }
}

?>
