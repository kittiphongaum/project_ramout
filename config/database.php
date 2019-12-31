<?php
class Database{
 
    // specify your own database credentials
    
    // private $host = "10.22.50.146";
    // private $db_name = "memo_bo_admin";
    // private $username = "bo_admin";
    // private $password = "P@ssw0rd!";
    

    // private $host = "10.22.50.145";
    // private $db_name = "memo_bo_admin";
    // private $username = "bo_admin";
    // private $password = "P@ssw0rd!";

    
    private $host = "35.186.151.187";
    private $db_name = "memo_bo_admin";
    private $username = "dba01";
    private $password = "dba@dminOnly1";
    

    public $conn;
 
    // get the database connection
    public function getConnection(){
        $this->conn = null;
 
        try{
            $this->conn = new PDO("sqlsrv:server=" . $this->host . ";Database=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }
 
        return $this->conn;
    }
}
?>