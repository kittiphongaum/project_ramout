<?php
class orgChartModel
{

    // database connection and table name
    private $conn;
    private $table_name = "";

    // object properties
    public $id;
    public $dept_code;
    public $dept_name;
    public $parent_code;
    public $parent_name;
    public $pos_code;
    public $pos_name;
    public $emp_code;
    public $emp_name;

    // constructor with $db as database connection
    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAll()
    {

    }

    public function getORG()
    {
        $query = "SELECT TOP 30 col011, col012, col013
         FROM CBS_HRCOMPANYREL WHERE col012 LIKE '%".$this->dept_name."%' OR col013 LIKE '%".$this->dept_name."%'
         Group by col013, col011, col012
         ORDER BY col013 ";
        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        try {
            $stmt->execute();
            return $stmt;

        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }

    public function getORGbyParent()
    {
        
        $query = "SELECT TOP 30 parent_department_id, department_id, department_name, department_long_name FROM db_org_hr_department WHERE (department_long_name like '%".$this->dept_name."%' OR department_id = '%".$this->dept_name."%')";

        if ($this->parent_org_id) {
            $query .= " AND parent_department_id = '".$this->parent_org_id."'";
        }

        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        try {
            $stmt->execute();
            return $stmt;

        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }

    public function getEMP()
    {
        $query = "SELECT TOP 30 * FROM ktc_employee_view WHERE 1=1 AND emp_name like '%".$this->emp_name."%'";

        if (isset($this->emp_code)) {
            $query = $query." OR emp_id like '%".$this->emp_code."%'";
        }

        $query = $query." ORDER BY emp_name ";
        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        try {
            $stmt->execute();
            return $stmt;

        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }
}
