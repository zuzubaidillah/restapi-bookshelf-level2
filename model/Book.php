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
}

?>
