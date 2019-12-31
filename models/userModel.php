<?php
class userModel
{

    // database connection and table name
    private $conn;
    private $table_name = "";

    // object properties
    public $id;
    public $emp_id;
    public $fullname_th;
    public $position_id;
    public $position_name;
    public $department_id;
    public $department_name;
    public $start_work;
    public $contact_no;
    public $email;
    public $username;
    public $password;
    public $active;
    public $last_updated;

    // constructor with $db as database connection
    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAll()
    {
        $query = "SELECT * FROM db_user ORDER BY id";
        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        try {
            $stmt->execute();
            return $stmt;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }

    public function get()
    {
        $query = "SELECT u.*, v.*     
        FROM db_user AS u 
        LEFT JOIN v_org_chart v ON (u.emp_id = CAST(v.emp_code as int)) 
        WHERE u.active=1 ";

        if (isset($this->id)) {
            $query = $query . " AND u.id = :id";
        }

        if (isset($this->emp_id)) {
            $query = $query . " AND u.emp_id = :emp_id";
        }

        if (isset($this->username)) {
            $query = $query . " AND u.username = :username";
        }

        if (isset($this->password)) {
            $query = $query . " AND u.password = :password";
        }

        $query = $query . " ORDER BY u.id";
        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        if (isset($this->id)) {
            $stmt->bindParam(":id", $this->id);
        }

        if (isset($this->emp_id)) {
            $stmt->bindParam(":emp_id", $this->emp_id);
        }

        if (isset($this->username)) {
            $stmt->bindParam(":username", $this->username);
        }

        if (isset($this->password)) {
            $stmt->bindParam(":password", $this->password);
        }

        try {
            $stmt->execute();
            $num = $stmt->rowCount();
            if ($num == 1) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                //print_r($row);
                // set values to object properties
                $this->id = $row['id'];
                $this->emp_id = $row['emp_id'];
                $this->emp_name = $row['emp_name'];
                $this->position_id = $row['pos_code'];
                $this->position_name = $row['pos_name'];
                $this->department_id = $row['dept_code'];
                $this->department_name = $row['dept_name'];
                // $this->start_work = $row['start_work'];
                // $this->contact_no = $row['contact_no'];
                // $this->email = $row['email'];
                $this->username = $row['username'];
                $this->password = $row['password'];
                $this->active = $row['active'];
                $this->last_updated = $row['last_updated'];
            } 
            else if ($num == 0)
            {
                
            }
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }
}

?>