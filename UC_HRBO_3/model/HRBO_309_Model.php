<?php
class HRBO_309_Model
{
    // database connection and table name
    private $conn;

    // object properties
    public $transaction_id;
    public $doc_no;
    public $req_date;
    public $emp_id;
    public $emp_title;
    public $emp_first_name;
    public $emp_last_name;
    public $emp_position_id;
    public $emp_position_name;
    public $emp_org_id;
    public $emp_org_long_id;
    public $emp_org_name;
    public $emp_org_full_name;
    public $req_full_name;
    public $req_position;
    public $req_rights_type;
    public $admit_date;
    public $clinic_type;
    public $clinic_name;
    public $province;
    public $district;
    public $disease;
    public $status;
    public $created_date;
    public $created_by;
    public $updated_date;
    public $updated_by;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAll()
    {
        $query = "SELECT medical_form.* FROM myhr_req_medical_form as medical_form
        LEFT JOIN db_org_hr_department as hr ON medical_form.emp_org_id = hr.department_id
        LEFT JOIN db_org_hr_department as hr2 ON hr2.department_id = hr.parent_department_id WHERE 1=1";

        if ($this->doc_no) {
            $query .= " AND doc_no like '%" . $this->doc_no . "%'";
        }

        if ($this->sdate && $this->edate) {
            $query .= " AND req_date BETWEEN '" . $this->sdate . "' AND '" . $this->edate . "'";
        }

        if ($this->emp_org_id) {
            $query .= " AND ( hr.department_id = '" . $this->emp_org_id . "' OR hr.parent_department_id = '" . $this->emp_org_id . "' OR hr2.parent_department_id = '" . $this->emp_org_id . "' )";
        }

        // if ($this->emp_name) {
        //     $query .= " AND (emp_title like '%" . $this->emp_name . "%' OR emp_first_name like '%" . $this->emp_name . "%' OR emp_last_name like '%" . $this->emp_name . "%')";
        // }

        if ($this->emp_id) {
            $query .= " AND (emp_id = '" . $this->emp_id . "' )";
        }

        if ($this->personal_group) {
            $query .= " AND personal_group IN ('" . $this->personal_group . "')";
        }
        
        if ($this->status) {
            $query .= " AND status IN ('" . $this->status . "')";
        }

        $query .= " ORDER BY req_date DESC";

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
        $query = "SELECT * FROM myhr_req_medical_form WHERE transaction_id = :transaction_id";

        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        $stmt->bindParam(":transaction_id", $this->transaction_id);

        try {
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }

    public function get_region($code)
    {
        if (!$code) {            
            $query = "SELECT * FROM myhr_mp_region";
        }else{
            $query = "SELECT * FROM myhr_mp_region WHERE region_code =  :region_code";
        }
        
        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        if ($code) {
            $stmt->bindParam(":region_code", $code);
        }
        try {
            $stmt->execute();
            return $stmt;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }

    public function check_emp_region($emp_id)
    {

        $query = "SELECT * FROM myhr_ms_medical_form_permission WHERE emp_id = :emp_id";
        
        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        
        $stmt->bindParam(":emp_id", $emp_id);
        
        try {
            $stmt->execute();
            return $stmt;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }
    
    public function update_status()
    {
        $query = "UPDATE myhr_req_medical_form SET status = :status WHERE transaction_id = :transaction_id ";


        // prepare query
        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":transaction_id", $this->transaction_id);

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
