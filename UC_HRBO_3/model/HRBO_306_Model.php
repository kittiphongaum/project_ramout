<?php
class HRBO_306_Model
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

    public function getAll()
    {

        $query = "SELECT
                    emp.emp_name  as EmpName,
                    t.emp_id as EmpId,
                    emp.department_name  as EmpDep,
                    t.created_date As CreatedDate,
                    emp.position_name As EmpPos,
                    dept2.parent_department_name As DepPar,
                    t.req_status As Status,t.id As Id,
                    t.req_amount As priviledge_month,
                    r.name as type_benefits,
                    t.req_no As req_no
                        FROM trans_req_benefits As t
                        LEFT JOIN db_request_benefit_type  As r ON r.benefit_code = t.benefit_code
                        LEFT JOIN v_employee_profile AS emp ON t.emp_id = emp.emp_id
                        LEFT JOIN db_org_hr_department AS dept2 ON dept2.department_id = emp.department_short_id
                        WHERE t.benefit_code IN ('retirement_money','funeral_fee','cremation_walfare','provident_fund','retirement_allowance_extra','retirement_allowance')
                        ORDER BY t.created_date DESC , t.updated_date DESC";

        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

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
                    emp.emp_name  as EmpName,
                    t.emp_id as EmpId,
                    emp.department_name  as EmpDep,
                    t.created_date As CreatedDate,
                    emp.position_name As EmpPos,
                    dept2.parent_department_name As DepPar,
                    t.id As Id,
                    t.req_no As req_no,
                    JSON_VALUE(req_info,'$.invoice.receipt_no') AS ReceiptNo,
                    JSON_VALUE(req_info,'$.invoice.receipt_date') AS receipt_date,
                    JSON_VALUE(req_info,'$.invoice.emp_id') AS emp_id,
                    JSON_VALUE(req_info,'$.invoice.name') AS name,
                    JSON_VALUE(req_info,'$.invoice.id_card_number') AS id_card_number,
                    JSON_VALUE(req_info,'$.invoice.relationship') AS relationship,
                    JSON_VALUE(req_info,'$.invoice.ratio') AS ratio,
                    r.name as type_benefits
                        FROM trans_req_benefits As t
                        LEFT JOIN db_request_benefit_type  As r ON r.benefit_code = t.benefit_code
                        LEFT JOIN v_employee_profile AS emp ON t.emp_id = emp.emp_id
                        LEFT JOIN db_org_hr_department AS dept2 ON dept2.department_id = emp.department_short_id
                        WHERE t.id = :id
                        ORDER BY t.created_date DESC , t.updated_date DESC";

        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        $stmt->bindParam(":id", $this->id);

        try {
            $stmt->execute();
            $num = $stmt->rowCount();

            if ($num == 1) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                // set values to object properties
                $this->EmpName              = $row['EmpName'];
                $this->EmpId                = $row['EmpId'];
                $this->EmpDep               = $row['EmpDep'];
                $this->CreatedDate          = $row['CreatedDate'];
                $this->EmpPos               = $row['EmpPos'];
                $this->Id                   = $row['Id'];
                $this->housId               = $row['Id'];
                $this->req_no               = $row['req_no'];
                $this->ReceiptNo            = $row['ReceiptNo'];
                $this->receipt_date         = $row['receipt_date'];
                $this->emp_id               = $row['emp_id'];
                $this->name                 = $row['name'];
                $this->id_card_number       = $row['id_card_number'];
                $this->relationship         = $row['relationship'];
                $this->ratio                = $row['ratio'];
                $this->type_benefits        = $row['type_benefits'];
                $this->DepPar               = $row['DepPar'];
            } else {
                return $stmt;
            }
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }

    public function All()
    {
        $query = "SELECT
                    emp.emp_name  as EmpName,
                    t.emp_id as EmpId,
                    emp.department_name  as EmpDep,
                    t.created_date As CreatedDate,
                    emp.position_name As EmpPos,
                    dept2.parent_department_name As DepPar,
                    t.req_status As Status,t.id As Id,
                    t.req_amount As priviledge_month,
                    t.req_no As req_no
                        FROM trans_req_benefits As t
                        LEFT JOIN db_request_benefit_type  As r ON r.benefit_code = t.benefit_code
                        LEFT JOIN v_employee_profile AS emp ON t.emp_id = emp.emp_id
                        LEFT JOIN db_org_hr_department AS dept2 ON dept2.department_id = emp.department_short_id
                        WHERE t.emp_id = :EmpId
                        ORDER BY t.created_date DESC , t.updated_date DESC";

        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        $stmt->bindParam(":EmpId", $this->EmpId);

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
                    emp.emp_name  as EmpName,
                    t.emp_id as EmpId,
                    emp.department_name  as EmpDep,
                    t.created_date As CreatedDate,
                    emp.position_name As EmpPos,
                    dept2.parent_department_name As DepPar,
                    t.req_status As Status,t.id As Id,
                    t.req_amount As priviledge_month,
                    t.req_no As req_no,
                    r.name as type_benefits
                        FROM trans_req_benefits As t
                        LEFT JOIN db_request_benefit_type  As r ON r.benefit_code = t.benefit_code
                        LEFT JOIN v_employee_profile AS emp ON t.emp_id = emp.emp_id
                        LEFT JOIN db_org_hr_department AS dept2 ON dept2.department_id = emp.department_short_id
                        WHERE 1=1 ";

        if (isset($this->req_no)) {
            $query .= " AND t.req_no like '%" . $this->req_no . "%' ";
        }

        if (isset($this->EmpName)) {
            $query .= " AND emp.emp_name like '%" . $this->EmpName . "%' ";
        }

        if (isset($this->dept_code)) {
            $query .= " AND dept2.department_id = " . $this->dept_code;
        }

        if (isset($this->Benefit)) {
            $query .= " AND  t.benefit_code IN ('" .$this->Benefit.str_replace(",", "','") . "')";
        }
        if(empty($_POST["Benefit"])){
            $query .= " AND  t.benefit_code IN ('retirement_money','funeral_fee','cremation_walfare','provident_fund','retirement_allowance_extra','retirement_allowance')";
        }
        if(empty($_POST["Benefit"])){

        }
        if (!empty($this->sdate) && !empty($this->edate)) {
            $query .= " AND t.created_date between '" . $this->sdate . "' AND '" . $this->edate . "'";
        }

        $query .= "ORDER BY t.created_date DESC , t.updated_date DESC";
        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        try {
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

    public function searching()
    {
       $query = "SELECT * FROM trans_req_scholarship_fee  WHERE id = :id ";

        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
                $this->housId=htmlspecialchars(strip_tags($this->housId));

        $stmt->bindParam(":id", $this->housId);

        try {
            $stmt->execute();
            $num = $stmt->rowCount();
            if ($num == 1) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                 // set values to object properties
                $this->req_status  = $row['req_status'];

            } else {
                return $stmt;
            }
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }
}
