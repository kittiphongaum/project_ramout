<?php
class HRBO_318_Model
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
    public $department_name;
    public $Id;
    public $req_no;
    public $ReceiptNo;
    public $receipt_date ;
    public $objective ;
    public $emp_id ;
    public $name ;
    public $rights_user ;
    public $name_of_hospital ;
    public $json_array;
    public $tel;
    public $credit_limit;
    public $current_outstanding_debt;
    public $credit_limit_balance;
    public $amount_loan_requested;
    public $installment_period_time;
    public $salary;
    public $amount_deduction;
    public $monthly_payments;
    public $percentage_deduction_money;
    public $net_salary;
    public $position_name;

     public function __construct($db)
     {
        $this->conn  = $db;
        $this->benefit_code = 'loan';
     }
     public function search()
     {
         $query = "SELECT * FROM myhr_req_loan_welfare As t WHERE 1=1 ";
 
         if (isset($this->req_no)) {
             $query .= " AND t.doc_no like '%" . $this->req_no . "%'";
         }
         if (isset($this->EmpName)) {
             $query .= " AND  (emp_first_name LIKE '%" . $this->EmpName . "%' OR emp_last_name LIKE '%" . $this->EmpName . "%') ";
         }
 
         if (isset($this->emp_org_id)) {
             $query .= " AND emp_org_id = '" . $this->emp_org_id."'";
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
     public function getById()
     {
         $query = "SELECT * FROM myhr_req_loan_welfare AS t WHERE transaction_id=:id ";
          $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
          $stmt->bindParam(":id", $this->id);
         try {
             $stmt->execute();
             return $stmt;
         } catch (PDOException $ex) {
             die($ex->getMessage());
         }
     }

    public function historyLoan()
    {
       $query = "SELECT * FROM myhr_req_loan_welfare As t WHERE t.emp_id = :EmpId ORDER BY t.created_date DESC ";

        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        $stmt->bindParam(":EmpId", $this->EmpId);
      
        try {
            $stmt->execute();
            return $stmt;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }

    public function update_status()
    {
        $query = "UPDATE myhr_req_loan_welfare SET status = :status WHERE transaction_id = :transaction_id";

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
