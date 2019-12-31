<?php
class HRBO_307_Model
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
    public $receipt_no;
    public $receipt_date;
    public $personal_insurance ;
    public $date_of_treat ;
    public $tel ;
    public $disease ;
    public $type_of_hospital ;
    public $name_of_hospital ;
    public $rights_user;
    public $addess;

    public function __construct($db)
    {
        $this->conn  = $db;
        $this->benefit_code = 'referral_letter';
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
                    t.id As Id,
                    t.req_no As req_no
                        FROM trans_req_benefits As t
                        LEFT JOIN db_request_benefit_type  As r ON r.benefit_code = t.benefit_code
                        LEFT JOIN v_employee_profile AS emp ON t.emp_id = emp.emp_id
                        LEFT JOIN db_org_hr_department AS dept2 ON dept2.department_id = emp.department_short_id
                        WHERE  t.benefit_code = :benefit_code
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
                    t.req_no As req_no
                        FROM trans_req_benefits As t
                        LEFT JOIN db_request_benefit_type  As r ON r.benefit_code = t.benefit_code
                        LEFT JOIN v_employee_profile AS emp ON t.emp_id = emp.emp_id
                        LEFT JOIN db_org_hr_department AS dept2 ON dept2.department_id = emp.department_short_id
                        WHERE 1=1 AND t.benefit_code = 'referral_letter'";

        if (isset($this->req_no)) {
            $query .= " AND t.req_no like '%" . $this->req_no . "%' ";
        }

        if (isset($this->EmpName)) {
            $query .= " AND emp.emp_name like '%" . $this->EmpName . "%' ";
        }

        if (isset($this->dept_code)) {
            $query .= " AND dept2.department_id = " . $this->dept_code;
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
                    JSON_VALUE(req_info,'$.invoice.personal_insurance') AS personal_insurance,
                    JSON_VALUE(req_info,'$.invoice.date_of_treat') AS date_of_treat,
                    JSON_VALUE(req_info,'$.invoice.tel') AS tel,
                    JSON_VALUE(req_info,'$.invoice.rights_user') AS rights_user,
                    JSON_VALUE(req_info,'$.invoice.disease') AS disease,
                    JSON_VALUE(req_info,'$.invoice.type_of_hospital') AS type_of_hospital,
                    JSON_VALUE(req_info,'$.invoice.name_of_hospital') AS name_of_hospital,
                    JSON_VALUE(req_info,'$.invoice.address') AS addess
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
                   $this->DepPar               = $row['DepPar'];
                   $this->Id                   = $row['Id'];
                   $this->req_no               = $row['req_no'];
                   $this->receipt_no           = $row['ReceiptNo'];
                   $this->receipt_date         = $row['receipt_date'];
                   $this->personal_insurance   = $row['personal_insurance'];
                   $this->date_of_treat        = $row['date_of_treat'];
                   $this->tel                  = $row['tel'];
                   $this->disease              = $row['disease'];
                   $this->type_of_hospital     = $row['type_of_hospital'];
                   $this->name_of_hospital     = $row['name_of_hospital'];
                   $this->rights_user          = $row['rights_user'];
                   $this->addess               = $row['addess'];

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
                        WHERE t.emp_id = :EmpId AND t.benefit_code = :benefit_code
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
}
