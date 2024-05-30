<?php
namespace Model;
require_once __DIR__ . "/../config/Database.php";
use Config\{Database};
class Users
{
    private $table_name = "users";
    private $db;

    public $id;
    public $name;
    public $email;
    public $password;
    public $file;
    public $created_at;
    public $updated_at;
    public $deleted_at;

    public function __construct()
    {
        $this->db = new Database();
    }

    // Method untuk membaca data buku
    public function read()
    {
        // Query untuk memilih semua kolom dari tabel book
        $query = "SELECT * FROM " . $this->table_name . " WHERE deleted_at IS NULL";

        // Mempersiapkan statement query
        $this->db->query($query);
        return $this->db->resultSet();
    }

    public function login($email, $password)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE email=:email and password=:password and deleted_at IS NULL";
        $this->db->query($query);
        $this->db->bind('email', $email);
        $this->db->bind('password', $password);
        return $this->db->single();
    }

    public function findId($book_id)
    {
        $query = "SELECT * FROM users WHERE id=:id";
        $this->db->query($query);
        $this->db->bind('id', $book_id);
        return $this->db->single();
    }

    public function findEmail($email)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE email=:email";
        $this->db->query($query);
        $this->db->bind('email', $email);
        return $this->db->single();
    }

    public function save($form_data)
    {
        $query = "INSERT INTO $this->table_name (name, email, password, created_at) VALUES (:name, :email, :password, :created_at)";
        $this->db->query($query);
        $this->db->bind('name', $form_data['name']);
        $this->db->bind('email', $form_data['email']);
        $this->db->bind('password', $form_data['password']);
        $this->db->bind('created_at', $form_data['created_at']);

        $res = $this->db->execute();
        return $res;
        /*if ($res) {
            // Mengambil data yang baru disimpan
            $this->db->query("SELECT * FROM $this->table_name WHERE id = :id");
            $this->db->bind('id', $id);
            return $this->db->single();
        } else {
            return false;
        }*/
    }

    /**
     * form_data [name,email,password(has)]
    */
    public function registrasi($form_data)
    {
        try {
        $query = "INSERT INTO $this->table_name (name, email, password, created_at) VALUES (:name, :email, :password, :created_at)";
        $this->db->query($query);
        $this->db->bind('name', $form_data['name']);
        $this->db->bind('email', $form_data['email']);
        $this->db->bind('password', $form_data['password']);
        $this->db->bind('created_at', date("Y-m-d H:i:s"));

        $res = $this->db->execute();
        if ($res) {
            $user_id = $this->db->lastInsertId();
            // Mengambil data yang baru disimpan
            $this->db->query("SELECT * FROM $this->table_name WHERE id = :id");
            $this->db->bind('id', $user_id);
            return $this->db->single();
        } else {
            return false;
        }
        } catch (PDOException $exception) {
            return $exception;
        }
    }
}

?>
