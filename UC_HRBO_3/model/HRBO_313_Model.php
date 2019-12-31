<?php
class HRBO_313_Model
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
    public $child_full_name;
    public $birth_date;
    public $id_card;
    public $child_birth_limit_amt;
    public $child_no;
    public $status;
    public $created_date;
    public $created_by;
    public $updated_date;
    public $updated_by;

    public function __construct($db)
    {
        $this->conn         = $db;
        $this->benefit_code = 'child_allowance';

    }

    public function getAll()
    {
        $query = "SELECT * FROM myhr_req_maternity_fee_approval WHERE 1=1";

        if ($this->doc_no) {
            $query .= " AND doc_no like '%" . $this->doc_no . "%'";
        }

        if ($this->sdate && $this->edate) {
            $query .= " AND req_date BETWEEN '" . $this->sdate . "' AND '" . $this->edate . "'";
        }

        if ($this->emp_org_id) {
            $query .= " AND emp_org_id = '" . $this->emp_org_id . "'";
        }

        if ($this->emp_id) {
            $query .= " AND emp_id = '" . $this->emp_id . "'";
        }

        if ($this->emp_name) {
            $query .= " AND (emp_title like '%" . $this->emp_name . "%' OR emp_first_name like '%" . $this->emp_name . "%' OR emp_last_name like '%" . $this->emp_name . "%')";
        }

        if ($this->child_full_name) {
            $query .= " AND child_full_name like '%" . $this->child_full_name . "%'";
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
        $query = "SELECT * FROM myhr_req_maternity_fee_approval WHERE transaction_id = :transaction_id";

        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        $stmt->bindParam(":transaction_id", $this->transaction_id);

        try {
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }

    public function update_status()
    {
        $query = "UPDATE myhr_req_maternity_fee_approval SET status = :status WHERE transaction_id = :transaction_id ";


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

    public function getallnoti()
    {
        $query = "SELECT * FROM NOTIFICATION_HISTRORY  WHERE benefit_code = 'maternity_fee_approval' AND req_no = :doc_no ORDER BY created_date DESC";

        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        $stmt->bindParam(":doc_no", $this->doc_no);
        try {
            $stmt->execute();
            return $stmt;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }
}
