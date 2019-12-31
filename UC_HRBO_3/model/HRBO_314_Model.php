<?php
class HRBO_314_Model
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
    public $Id;
    public $priviledge_month;
    public $req_no;
    public $reqstatus;
    public $approvers_name;
    public $receipt_no;
    public $receipt_date;
    public $receive_time;
    public $receive_year;
    public $scholarship_limit;
    public $req_info;
    public $json_array;
    public $withdraw_amount;
    public $housId;
    public $message;
    public $statused;
    public $updated_date;
    public $benefitcode;

    public function __construct($db)
    {
        $this->conn         = $db;
        $this->benefit_code = 'scholarship';
    }

    public function getAll()
    {
        $query = "SELECT
            emp_title As TitleEmp,
            transaction_id As id,
            emp_first_name As FirstName,
            emp_id As EmpId,
            emp_position_name As EmpPos,
            emp_last_name As LastName,
            withdraw_amount As priviledge_month,
            emp_org_name As EmpDep,
            emp_org_full_name As DepPar,
            claim_date As req_datetime,
            t.status As Status,
            slip_no As slip_no,
            doc_no As req_no
            FROM myhr_req_scholarship_fee_approval AS t WHERE webhr_jobid=:benefit_code";
        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        $stmt->bindParam(":benefit_code", $this->benefit_code);
        try {
            $stmt->execute();
            return $stmt;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }
    public function getById()
    {
        $query = "SELECT * FROM myhr_req_scholarship_fee_approval AS t WHERE transaction_id=:id ";
        $stmt  = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":benefit_code", $this->benefit_code);

        try {
            $stmt->execute();
            return $stmt;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }
    public function OneAll()
    {

        $query = "SELECT
            emp_title As TitleEmp,
            transaction_id As id,
            emp_first_name As EmpName,
            emp_id As EmpId,
            emp_position_name As EmpPos,
            emp_last_name As LastName,
            withdraw_amount As priviledge_month,
            emp_org_name As EmpDep,
            emp_org_full_name As DepPar,
            created_date As CreatedDate,
            claim_date As req_datetime,
            t.status As Status,
            slip_no As slip_no,
            doc_no As req_no
                    FROM myhr_req_scholarship_fee_approval AS t
                    WHERE t.transaction_id = '1'
                    ORDER BY t.created_date";

        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        $stmt->bindParam(":id", $this->id);
        // $stmt->bindParam(":benefit_code", $this->benefit_code);
        try {
            $stmt->execute();
            return $stmt;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }
    public function All()
    {
        $query = "SELECT
            emp_title As TitleEmp,
            transaction_id As id,
            emp_first_name As FirstName,
            emp_id As EmpId,
            emp_position_name As EmpPos,
            emp_last_name As LastName,
            withdraw_amount As priviledge_month,
            emp_org_name As EmpDep,
            emp_org_full_name As DepPar,
            claim_date As req_datetime,
            t.status As Status,
            slip_no As slip_no,
            doc_no As req_no
                        FROM myhr_req_scholarship_fee_approval As t
                        WHERE t.transaction_id = :EmpId
                        ORDER BY t.claim_date";

        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        $stmt->bindParam(":EmpId", $this->EmpId);
        // $stmt->bindParam(":benefit_code", $this->benefit_code);
        try {
            $stmt->execute();
            return $stmt;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }

    public function search()
    {
        $query = "SELECT
        emp_title As TitleEmp,
        transaction_id As id,
        emp_first_name As FirstName,
        emp_id As EmpId,
        emp_position_name As EmpPos,
        emp_last_name As LastName,
        withdraw_amount As priviledge_month,
        emp_org_name As EmpDep,
        emp_org_full_name As DepPar,
        claim_date As req_datetime,
        t.status As Status,
        slip_no As slip_no,
        doc_no As req_no,
        req_date 
         FROM myhr_req_scholarship_fee_approval AS t  WHERE 1=1 ";

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
            //  $query .= " AND t.status IN (" . $this->req_status_list . ") ";
            $query .= " AND t.status IN ('" . $this->req_status_list . "') ";

        }
        $query .= " ORDER BY t.claim_date DESC ";

        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        try {
            $stmt->execute();
            return $stmt;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }

    public function searching()
    {
        $query = "SELECT * FROM trans_req_scholarship_fee  WHERE id = :id ";

        $stmt         = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        $this->housId = htmlspecialchars(strip_tags($this->housId));

        $stmt->bindParam(":id", $this->housId);

        try {
            $stmt->execute();
            $num = $stmt->rowCount();
            if ($num == 1) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                // set values to object properties
                $this->req_status = $row['req_status'];
            } else {
                return $stmt;
            }
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }
    public function getallnoti()
    {
        $query = "SELECT * FROM NOTIFICATION_HISTRORY WhERE benefit_code = 'scholarship' AND req_no = :rep AND emp_id=:empId ORDER BY created_date DESC";
        $stmt  = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        $stmt->bindParam(":rep", $this->rep);
        $stmt->bindParam(":empId", $this->EmpId);

        try {
            $stmt->execute();
            return $stmt;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }
    public function getIdApproval()
    {
        $query = "SELECT * FROM myhr_req_scholarship_fee_approval AS t WHERE t.emp_id=:empId";
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
            // var_export($stmt->fetch());
            return $stmt;
            exit;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }

    public function update_status()
    {
        $query = "UPDATE myhr_req_scholarship_fee_approval SET status = :status WHERE transaction_id = :transaction_id";

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
        $query = "UPDATE myhr_req_scholarship_fee_approval
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
