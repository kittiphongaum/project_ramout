<?php
class positionMasterModel
{

    // database connection and table name
    private $conn;

    // object properties
    public $position_id;
    public $position_name;
    public $position_group_id;
    public $position_group_name;
    public $position_group_level;
    public $created_date;
    public $created_by;
    public $updated_date;
    public $updated_by;

    // constructor with $db as database connection
    public function __construct($db)
    {
        $this->conn       = $db;
        $this->req_status = 0;
    }

    public function getAll()
    {
        $query = "SELECT * FROM db_position_master WHERE 1=1";

        if (!empty($this->position_name)) {
            $query .= " AND position_name LIKE '%".$this->position_name."%'";
        }

        // echo $query;
        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        try {
            $stmt->execute();
            return $stmt;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }

}
