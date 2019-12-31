<?php
class HRBO_3_Model
{
    // database connection and table name
    private $conn;

    // object properties
    public $empid;
    public $createddate;
    public $benefitcode;
    public $description;
    public $benefit;
    public $benefitname;
    public $reqno;

    public function __construct($db)
    {
        $this->conn  = $db;
    }

    public function insert()
    {
        $query = "INSERT INTO NOTIFICATION_HISTRORY (emp_id,benefit_code,created_date,description,req_no) VALUES(?,?,?,?,?)";
        $stmt = $this->conn->prepare($query);
                $this->empid       = htmlspecialchars(strip_tags($this->empid));
                $this->createddate = htmlspecialchars(strip_tags($this->createddate));
                $this->benefitcode = htmlspecialchars(strip_tags($this->benefitcode));
                $this->reqno       = htmlspecialchars(strip_tags($this->reqno));
                $this->description = htmlspecialchars(strip_tags($this->description));

        // bind values
        $stmt->bindParam(1, $this->empid);
        $stmt->bindParam(2, $this->benefitcode);
        $stmt->bindParam(3, $this->createddate);
        $stmt->bindParam(4, $this->description);
        $stmt->bindParam(5, $this->reqno);

        // execute the query
        if($stmt->execute()){
            return true;
        }
        return false;
    }

    public function searchbenefit()
    {
        $query = "SELECT t.name as benefitname FROM db_request_benefit_type As t  WHERE t.benefit_code = :benefit_code";

        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        $stmt->bindParam(":benefit_code", $this->benefit);

        try {
            $stmt->execute();
            $num = $stmt->rowCount();

            if ($num == 1) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                // set values to object properties
                $this->benefitname = $row['benefitname'];
            } else {
                return $stmt;
            }
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }
}
