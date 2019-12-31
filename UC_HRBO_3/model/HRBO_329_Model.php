<?php
class HRBO_329_Model
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
    public $priviledge_month;
    public $dept_code;
    public $edate;
    public $sdate;
    public $req_no;
    public $housId;
    public $Benefit;
    public $ReceiptNo;
    public $receipt_date;
    public $emp_id;
    public $name;
    public $id_card_number;
    public $relationship;
    public $ratio;
    public $type_benefits;

    public function __construct($db)
    {
        $this->conn  = $db;
       
    }
    public function search()
    {
        $query = "SELECT t.*,b.member_type As beneficiaryid,b.beneficiary_group,b.ord As Ord FROM myhr_req_beneficiary_item AS t LEFT JOIN myhr_mp_beneficiary_group AS b ON t.member_type =b.member_type WHERE 1=1 ";

        if (isset($this->req_no)) {
            $query .= " AND t.doc_no like '%" . $this->req_no . "%'";
        }
        
        if (isset($this->EmpName)) {
            $query .= " AND  (emp_first_name LIKE '%" . $this->EmpName . "%' OR emp_last_name LIKE '%" . $this->EmpName . "%') ";
        }

        if (isset($this->emp_org_id)) {
            $query .= " AND emp_org_id = '" . $this->emp_org_id."'";
        }

        if (isset($this->Benefit)) {
            $query .= " AND t.member_type ='" . $this->Benefit."'";
        }

        if (!empty($this->sdate) && !empty($this->edate)) {
            $query .= " AND t.created_date between '" . $this->sdate . "' AND '" . $this->edate . "'";
        }
        if (isset($this->req_status_list)) {      
             $query .= " AND t.status IN ('" . $this->req_status_list . "') ";
             
        }

     $query .= " ORDER BY t.created_date DESC ";
          
       $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        try {
               //  $stmt->execute($this->req_status_list);
                $stmt->execute();
            return $stmt;

        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }

    public function update()
    {
        $query = "UPDATE trans_req_scholarship_fee SET req_status = ?, updated_date = ? WHERE id = ? ";

        $stmt = $this->conn->prepare( $query );
                $this->housId=htmlspecialchars(strip_tags($this->housId));
                $this->updated_date=htmlspecialchars(strip_tags($this->updated_date));
                $this->req_status=htmlspecialchars(strip_tags($this->req_status));

        // bind values
        $stmt->bindParam(1, $this->req_status);
        $stmt->bindParam(2, $this->updated_date);
        $stmt->bindParam(3, $this->housId);

        // execute the query
        if($stmt->execute()){
            return true;
        }
        return false;
    }

    public function getbeneficiary_group()
    {
        $query = "SELECT * FROM myhr_mp_beneficiary_group";
        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        try {
            $stmt->execute();
        
        return $stmt;
        } catch (PDOException $ex) {
             die($ex->getMessage());
        }
    }
    public function getById()
    {
        $query = "SELECT t.*,b.member_type As beneficiaryid,b.beneficiary_group,b.ord As Ord FROM myhr_req_beneficiary_item AS t LEFT JOIN myhr_mp_beneficiary_group AS b ON t.member_type =b.member_type WHERE transaction_id=:id ";
         $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
         $stmt->bindParam(":id", $this->id);
        try {
            $stmt->execute();
            return $stmt;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }

    public function getallnoti()
    {
        $query = "SELECT * FROM NOTIFICATION_HISTRORY WhERE benefit_code = 'beneficiary' AND req_no = :rep AND emp_id=:empId ORDER BY created_date DESC";
        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
         $stmt->bindParam(":rep", $this->rep);
         $stmt->bindParam(":empId", $this->EmpId);
    
        try {
            $stmt->execute();
            return $stmt;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }
    public function update_status()
    {
        $query = "UPDATE myhr_req_beneficiary_item SET status = :status WHERE transaction_id = :transaction_id";

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

    public function memberty(){

        $query = "SELECT * from myhr_req_beneficiary_item_db2 WHERE doc_no =:doc_no AND employeeno =:empId";
        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
         $stmt->bindParam(":doc_no", $this->doc_no);
         $stmt->bindParam(":empId", $this->EmpId);
    
        try {
            $stmt->execute();
            return $stmt;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }
    public function membertyChild($id){

        $query = "SELECT * from myhr_req_beneficiary_item_db2 WHERE value ='$id'";
        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        //  $stmt->bindParam(":doc_no", $this->doc_no);
        //  $stmt->bindParam(":empId", $this->EmpId);
    
        try {
            $stmt->execute();
            return $stmt;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }
    
    public function update_status_DB2()
    {
        $query = "UPDATE myhr_req_beneficiary_item_db2 SET status_finish = :status WHERE value = :transaction_id";

        // prepare query
        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":transaction_id", $this->transaction_id);
        try {
            $stmt->execute();
            return $stmt->rowCount();
        } catch (PDOException $ex) {
            $this->conn->rollback();
            die($ex->getMessage());
        }
    }
}
