<?php
class HRBO_317_Model
{
    // database connection and table name
    private $conn;

    // object properties
    public $EmpName;
    public $EmpId;
    public $EmpDep;
    public $CreatedDate;
    public $EmpPos;
    public $DepPar;
    public $reqstatus;
    public $department_name;
    public $Id;
    public $priviledge_amount;
    public $draw_amount;
    public $draw_amount_net;
    public $priviledge_month;
    public $dept_code;
    public $req_status_list;
    public $edate;
    public $sdate;
    public $req_no;
    public $receipt_no;
    public $receipt_date;
    public $type_of_patient;
    public $type_of_disease;
    public $type_of_hospital;
    public $days_of_treatment;
    public $housId;
    public $approver_name;
    public $medical_fee;
    public $room_and_food_fee;
    public $equipment_fee;
    public $docter_fee_amount;
    public $other_fee;
    public $withdraw_amount;
    public $take_moro;
    public $benefitcode;
    public $invoice_name;

    public function __construct($db)
    {
        $this->conn         = $db;
        $this->benefit_code = 'medical_fee';
    }

    public function search()
    {
        $query = "SELECT * FROM myhr_req_medical_fee_approval As t WHERE 1=1 ";

        if (isset($this->req_no)) {
            $query .= " AND t.doc_no like '%" . $this->req_no . "%'";
        }
        if (isset($this->EmpName)) {
            $query .= " AND  (emp_first_name LIKE '%" . $this->EmpName . "%' OR emp_last_name LIKE '%" . $this->EmpName . "%') ";
        }

        if (isset($this->emp_org_id)) {
            $query .= " AND emp_org_id = '" . $this->emp_org_id . "'";
        }

        if (!empty($this->sdate) && !empty($this->edate)) {
            $query .= " AND t.claim_date between '" . $this->sdate . "' AND '" . $this->edate . "'";
        }

        if (isset($this->req_status_list)) {
            $query .= " AND t.status IN ('" . $this->req_status_list . "') ";

        }
        $query .= " ORDER BY t.created_date DESC ";

        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        try {
            $stmt->execute($this->req_status_list);
            $stmt->execute();
            return $stmt;

        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }
    public function getById()
    {
        $query = "SELECT * FROM myhr_req_medical_fee_approval AS t WHERE transaction_id=:id ";
        $stmt  = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        $stmt->bindParam(":id", $this->id);
        try {
            $stmt->execute();
            return $stmt;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }
    public function getIdApproval()
    {
        $query = "SELECT * FROM myhr_req_medical_fee_approval AS t WHERE t.emp_id=:empId AND status IN ('S','A','R')";
        $stmt  = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        $stmt->bindParam(":empId", $this->EmpId);

        try {
            $stmt->execute();
            return $stmt;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }
    // item
    public function getItemAppver()
    {
        $query = "SELECT * FROM myhr_req_scholarship_fee_item  WHERE doc_no =:req_no AND transaction_ref_id =:id_tr ORDER BY ord ASC";
        $stmt  = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        $stmt->bindParam(":req_no", $this->req_no);
        $stmt->bindParam(":id_tr", $this->id);
        try {
            $stmt->execute();
            return $stmt;
            exit;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }

    public function getallnoti()
    {
        $query = "SELECT * FROM NOTIFICATION_HISTRORY  WHERE benefit_code = 'medical_fee_approval' AND emp_id = :req_no ORDER BY created_date DESC";

        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        $stmt->bindParam(":req_no", $this->req_no);
        try {
            $stmt->execute();
            return $stmt;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }

    public function update_status()
    {
        $query = "UPDATE myhr_req_medical_fee_approval SET status = :status WHERE transaction_id = :transaction_id";

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

    public function update_money_back()
    {
        $query = "UPDATE myhr_req_medical_fee_approval
                        SET money_back_amt = :money_back_amt
                        , money_back_msg = :money_back_msg
                        , money_back_date = :money_back_date
                  WHERE transaction_id = :transaction_id";

        // prepare query
        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        $stmt->bindParam(":money_back_amt", $this->money_back_amt);
        $stmt->bindParam(":money_back_msg", $this->money_back_msg);
        $stmt->bindParam(":money_back_date", $this->money_back_date);
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
