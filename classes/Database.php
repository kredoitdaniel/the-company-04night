<?php

class Database
{
    private $server_name = 'localhost';
    private $username    = 'root';
    private $password    = '';
    private $db_name     = 'the_company';
    protected $conn;

    public function __construct()
    {
        $this->conn = new mysqli($this->server_name, $this->username, $this->password, $this->db_name);
        // mysqli represents connection between php and a MySQL Database
        // $this->conn is now an object of the class mysqli
        // $this->conn has now a connection to our database 'the_company'

        if($this->conn->connect_error){
            die('Unable to connect to the database: ' . $this->conn->connect_error);
            // die() will help us display the error message of connect_error property
        }
    }
}