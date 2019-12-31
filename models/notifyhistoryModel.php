<?php
class notifyhistoryModel
{
    // database connection and table name
    private $conn;

    // object properties
    public $id;
    public $msg;
    public $category;
    public $notify_type;
    public $to_emp_id;
    public $created_date;
    public $on_page;
    public $created_by;



    // constructor with $db as database connection
    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAll()
    {
        $query = "SELECT * FROM notify_history WHERE 1 = 1";

        if (isset($this->to_emp_id)) {
            $query = $query . " AND to_emp_id = :to_emp_id";
        }

        if (isset($this->on_page)) {
            $query = $query . " AND on_page = :on_page";
        }

        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        $stmt->bindParam(":to_emp_id", $this->to_emp_id);
        $stmt->bindParam(":on_page", $this->on_page);
        try {
            $stmt->execute();
            return $stmt;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }

    public function get()
    {
        $query = "SELECT * FROM notify_history WHERE 1=1 ";

        if (isset($this->id)) {
            $query = $query . " AND id = :id";
        }

        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        if (isset($this->id)) {
            $stmt->bindParam(":id", $this->id);
        }


        try {
            $stmt->execute();

            $num = $stmt->rowCount();
            if ($num == 1) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                // set values to object properties
                $this->id = $row['id'];
                $this->msg = $row['msg'];
                $this->category = $row['category'];
                $this->notify_type = $row['notify_type'];
                $this->to_emp_id = $row['to_emp_id'];
                $this->created_date = $row['created_date'];

            } else {
                return $stmt;
            }
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }

    public function create()
    {
        $query = "INSERT INTO notify_history (msg, category, notify_type, to_emp_id, created_date, on_page, created_by)
        VALUES (:msg, :category, :notify_type, :to_emp_id, :created_date, :on_page, :created_by)";

        // prepare query
        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        $this->msg = htmlspecialchars(strip_tags($this->msg));
        $this->category = htmlspecialchars(strip_tags($this->category));
        $this->notify_type = htmlspecialchars(strip_tags($this->notify_type));
        $this->to_emp_id = htmlspecialchars(strip_tags($this->to_emp_id));
        $this->created_date = date('Y-m-d H:i:s');
        $this->on_page = htmlspecialchars(strip_tags($this->on_page));
        $this->created_by = htmlspecialchars(strip_tags($this->created_by));

        // bind values
        $stmt->bindParam(":msg", $this->msg);
        $stmt->bindParam(":category", $this->category);
        $stmt->bindParam(":notify_type", $this->notify_type);
        $stmt->bindParam(":to_emp_id", $this->to_emp_id);
        $stmt->bindParam(":created_date", $this->created_date);
        $stmt->bindParam(":on_page", $this->on_page);
        $stmt->bindParam(":created_by", $this->created_by);

        try {
            $this->conn->beginTransaction();
            $stmt->execute();
            $this->conn->commit();
            return $stmt->rowCount();
        } catch (PDOException $ex) {
            $this->conn->rollback();
            die($ex->getMessage());
        }
    }

}
