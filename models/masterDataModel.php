<?php
class masterDataModel
{
    // database connection and table name
    private $conn;

    // object properties
    public $key_code;

    // constructor with $db as database connection
    public function __construct($db)
    {
        $this->conn = $db;

    }

    public function get()
    {
        $query = "SELECT * FROM  db_application_variable_master WHERE key_code = :key_code ";
        $stmt  = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        $stmt->bindParam(":key_code", $this->key_code);

        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        try {
            // set values to object properties
            return $row;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }
}
