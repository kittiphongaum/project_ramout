<?php
class HRBO_302_Model
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
    public $req_status_list;
    public $edate;
    public $sdate;
    public $req_no;
    public $receipt_date;
    public $child_name;
    public $child_age;
    public $school_name;
    public $semester;
    public $receipt_no;
    public $student_level_name;
    public $id;
    public $updated_date;
    public $housId;
    public $TotalBenefit;
    public $tuition_fee;
    public $commander;
    public $message;
    public $statused;
    public $benefitcode;

    public function __construct($db)
    {
        $this->conn  = $db;
        $this->benefit_code = 'child_allowance';

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
                    t.req_no As req_no,
                    JSON_VALUE(req_info,'$.invoice.child_name') AS child_name
                    FROM trans_req_benefits As t
                    LEFT JOIN db_request_benefit_type  As r ON r.benefit_code = t.benefit_code
                    LEFT JOIN v_employee_profile AS emp ON t.emp_id = emp.emp_id
                    LEFT JOIN db_org_hr_department AS dept2 ON dept2.department_id = emp.department_short_id
                    WHERE t.req_status = '0' AND t.benefit_code = :benefit_code
                    ORDER BY t.created_date DESC , t.updated_date DESC";

        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
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
                    emp.emp_name  as EmpName,
                    t.emp_id as EmpId,
                    emp.department_name  as EmpDep,
                    t.created_date As CreatedDate,
                    emp.position_name As EmpPos,
                    dept2.parent_department_name As DepPar,
                    t.req_status As reqstatus,
                    t.id As Id,
                    t.req_amount As priviledge_month,
                    t.req_no As req_no,
                    JSON_VALUE(req_info,'$.invoice.receipt_no') AS ReceiptNo,
                    JSON_VALUE(req_info,'$.invoice.receipt_date') AS ReceiptDate,
                    JSON_VALUE(req_info,'$.invoice.address') AS Address,
                    JSON_VALUE(req_info,'$.invoice.child_name') AS child_name,
                    JSON_VALUE(req_info,'$.invoice.child_age') AS child_age,
                    JSON_VALUE(req_info,'$.invoice.school_name') AS school_name,
                    JSON_VALUE(req_info,'$.invoice.student_level_name') AS student_level_name,
                    JSON_VALUE(req_info,'$.invoice.semester') AS semester,
                    JSON_VALUE(req_info,'$.withdraw.total_benefit') AS TotalBenefit,
                    JSON_VALUE(req_info,'$.withdraw.tuition_fee') AS tuition_fee,
                    JSON_VALUE(req_info,'$.withdraw.withdraw_amount') AS WithdrawAmount,
                    JSON_VALUE(req_info,'$.invoice.commander') AS commander,
                    t.benefit_code as benefitcode
                    FROM trans_req_benefits As t
                    LEFT JOIN db_request_benefit_type  As r ON r.benefit_code = t.benefit_code
                    LEFT JOIN v_employee_profile AS emp ON t.emp_id = emp.emp_id
                    LEFT JOIN db_org_hr_department AS dept2 ON dept2.department_id = emp.department_short_id
                    WHERE t.id = :id AND t.benefit_code = :benefit_code
                    ORDER BY t.created_date DESC , t.updated_date DESC";

        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":benefit_code", $this->benefit_code);

        try {
            $stmt->execute();
            $num = $stmt->rowCount();
            if ($num == 1) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $this->EmpName               = $row['EmpName'];
                $this->EmpId                 = $row['EmpId'];
                $this->EmpDep                = $row['EmpDep'];
                $this->CreatedDate           = $row['CreatedDate'];
                $this->EmpPos                = $row['EmpPos'];
                $this->DepPar                = $row['DepPar'];
                $this->reqstatus             = $row['reqstatus'];
                $this->Id                    = $row['Id'];
                $this->priviledge_amount     = $row['WithdrawAmount'];
                $this->TotalBenefit          = $row['TotalBenefit'];
                $this->req_no                = $row['req_no'];
                $this->address               = $row['Address'];
                $this->receipt_no            = $row['ReceiptNo'];
                $this->receipt_date          = $row['ReceiptDate'];
                $this->housId                = $row['Id'];
                $this->child_name            = $row['child_name'];
                $this->child_age             = $row['child_age'];
                $this->school_name           = $row['school_name'];
                $this->semester              = $row['semester'];
                $this->student_level_name    = $row['student_level_name'];
                $this->tuition_fee           = $row['tuition_fee'];
                $this->commander             = $row['commander'];
                $this->benefitcode           = $row['benefitcode'];
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
                    WHERE t.benefit_code = :benefit_code AND t.emp_id = :EmpId
                    ORDER BY t.created_date DESC , t.updated_date DESC";

        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        $stmt->bindParam(":EmpId", $this->EmpId);
        $stmt->bindParam(":benefit_code", $this->benefit_code);

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
                    JSON_VALUE(req_info,'$.invoice.child_name') AS child_name
                    FROM trans_req_benefits As t
                    LEFT JOIN db_request_benefit_type  As r ON r.benefit_code = t.benefit_code
                    LEFT JOIN v_employee_profile AS emp ON t.emp_id = emp.emp_id
                    LEFT JOIN db_org_hr_department AS dept2 ON dept2.department_id = emp.department_short_id
                    WHERE 1=1 AND  t.benefit_code = 'child_allowance'";

        if (isset($this->req_no)) {
            $query .= " AND t.req_no like '%" . $this->req_no . "%' ";
        }

        if (isset($this->EmpName)) {
            $query .= " AND emp.emp_name like '%" . $this->EmpName . "%' ";
        }

        if (isset($this->child_name)) {
            $query .= " AND JSON_VALUE(req_info,'$.invoice.child_name') like '%" . $this->child_name . "%' ";
        }

        if (isset($this->dept_code)) {
            $query .= " AND dept2.department_id = " . $this->dept_code;
        }

        if (!empty($this->sdate) && !empty($this->edate)) {
            $query .= " AND t.created_date between '" . $this->sdate . "' AND '" . $this->edate . "'";
        }

        if (isset($this->req_status_list)) {
            $array = str_repeat('?,', count($this->req_status_list) - 1) . '?';
            $query .= " AND t.req_status IN (" . $array . ") ";
        }

        $query .= "ORDER BY t.created_date DESC , t.updated_date DESC";
        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        try {
            if (isset($this->req_status_list)) {
                $stmt->execute($this->req_status_list);
            } else {
                $stmt->execute();
            }
            return $stmt;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }

    public function update()
    {
        $query = "UPDATE trans_req_benefits SET req_status =?,updated_date = ?,inbox_message = ? WHERE id = ?";

        $stmt = $this->conn->prepare($query);
                $this->housId       = htmlspecialchars(strip_tags($this->housId));
                $this->updated_date = htmlspecialchars(strip_tags($this->updated_date));
                $this->statused     = htmlspecialchars(strip_tags($this->statused));
                $this->message      = htmlspecialchars(strip_tags($this->message));

        // bind values
        $stmt->bindParam(1, $this->statused);
        $stmt->bindParam(2, $this->updated_date);
        $stmt->bindParam(3, $this->message);
        $stmt->bindParam(4, $this->housId);

        // execute the query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function getallnoti()
    {
        $query  = "SELECT * FROM NOTIFICATION_HISTRORY  WHERE benefit_code = 'child_allowance' AND req_no = :req_no ORDER BY created_date DESC";

        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        $stmt->bindParam(":req_no", $this->req_no);
        try {
            $stmt->execute();
            return $stmt;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }
}
