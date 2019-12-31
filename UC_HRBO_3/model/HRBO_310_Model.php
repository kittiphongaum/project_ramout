<?php
class HRBO_310_Model
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

     public function getAll()
     {
        $query = "SELECT
                    emp.emp_name  as EmpName,
                    t.emp_id as EmpId,
                    emp.department_name  as EmpDep,
                    t.created_date As CreatedDate,
                    emp.position_name As EmpPos,
                    dept2.parent_department_name As DepPar,
                    t.req_amount as priviledge_month,
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
                        WHERE 1=1 AND t.benefit_code = 'loan'";

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
                    JSON_VALUE(req_info,'$.invoice.objective') AS objective,
                    JSON_VALUE(req_info,'$.guarantor.emp_id') AS emp_id,
                    JSON_VALUE(req_info,'$.guarantor.name') AS name,
                    JSON_VALUE(req_info,'$.guarantor.position_name') AS position_name,
                    JSON_QUERY(req_info,'$.guarantor.tel') AS tel,
                    JSON_VALUE(req_info,'$.withdraw.credit_limit') AS credit_limit,
                    JSON_VALUE(req_info,'$.withdraw.current_outstanding_debt') AS current_outstanding_debt,
                    JSON_VALUE(req_info,'$.withdraw.credit_limit_balance') AS credit_limit_balance,
                    JSON_VALUE(req_info,'$.withdraw.amount_loan_requested') AS amount_loan_requested,
                    JSON_VALUE(req_info,'$.withdraw.installment_period_time') AS installment_period_time,
                    JSON_VALUE(req_info,'$.withdraw.salary') AS salary,
                    JSON_VALUE(req_info,'$.withdraw.amount_deduction') AS amount_deduction,
                    JSON_VALUE(req_info,'$.withdraw.monthly_payments') AS monthly_payments,
                    JSON_VALUE(req_info,'$.withdraw.percentage_deduction_money') AS percentage_deduction_money,
                    JSON_VALUE(req_info,'$.withdraw.net_salary') AS net_salary
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
                   $this->EmpName                     = $row['EmpName'];
                   $this->EmpId                       = $row['EmpId'];
                   $this->EmpDep                      = $row['EmpDep'];
                   $this->CreatedDate                 = $row['CreatedDate'];
                   $this->EmpPos                      = $row['EmpPos'];
                   $this->DepPar                      = $row['DepPar'];
                   $this->Id                          = $row['Id'];
                   $this->req_no                      = $row['req_no'];
                   $this->ReceiptNo                   = $row['ReceiptNo'];
                   $this->receipt_date                = $row['receipt_date'];
                   $this->objective                   = $row['objective'];
                   $this->emp_id                      = $row['emp_id'];
                   $this->name                        = $row['name'];
                   $this->rights_user                 = $row['rights_user'];
                   $json_array                        = json_decode($row['tel'], true);
                   $this->tel                         = implode(", ", $json_array);
                   $this->credit_limit                = $row['credit_limit'];
                   $this->current_outstanding_debt    = $row['current_outstanding_debt'];
                   $this->credit_limit_balance        = $row['credit_limit_balance'];
                   $this->amount_loan_requested       = $row['amount_loan_requested'];
                   $this->installment_period_time     = $row['installment_period_time'];
                   $this->salary                      = $row['salary'];
                   $this->amount_deduction            = $row['amount_deduction'];
                   $this->monthly_payments            = $row['monthly_payments'];
                   $this->percentage_deduction_money  = $row['percentage_deduction_money'];
                   $this->net_salary                  = $row['net_salary'];
                   $this->position_name               = $row['position_name'];
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

    public function getRequestBenefitType()
    {
        $sql = "SELECT benefit_type.*, CAST(config.amount AS INT) AS amount
                FROM db_request_benefit_type AS benefit_type
	                LEFT JOIN db_application_config_master AS config
                		ON benefit_type.benefit_code = config.key_code AND config.key_table = 'db_request_benefit_type'";
        $stmt = $this->conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        try {
            $stmt->execute();
            return $stmt;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }

    }

    public function getConfig($key)
    {
        $sql = "SELECT TOP 1 * FROM db_application_config_master WHERE key_code = '$key' AND config_type = 'loan_config'";
        $stmt = $this->conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return $stmt->fetch()["amount"];
        } else {
            return "";
        }
    }

    public function setConfigMonthTimes($times)
    {
        // Set default times
        if ($this->isZero($times) <= 0)
            $times = 12;

        // Insert or Update a config
        return $this->updateOrCreateConfig('month_times', $times);
    }

    public function setConfigMaxPersonGuarantee($person)
    {
        // Set default person
        if ($this->isZero($person) <= 0)
            $person = 5;

        // Insert or Update a config
        return $this->updateOrCreateConfig('max_person_guarantee', $person);
    }

    public function setConfigLoanAmounts($amonts)
    {
        foreach ($amonts as $key => $row) {
            $value = $this->isZero($row);

            if ($value <= 0)  {
                $this->deleteConfigIfExist($key);
            } else {
                $this->updateOrCreateConfig($key, $value);
            }
        }
    }

    private function deleteConfigIfExist($key)
    {
        $sql_find = "SELECT id FROM db_application_config_master WHERE key_code = '$key'";
        $stmt = $this->conn->prepare($sql_find, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $sql_delete = "DELETE FROM db_application_config_master WHERE key_code = '$key'";
            $stmt = $this->conn->prepare($sql_delete, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
            $stmt->execute();

            return $stmt;
        }

        unset($stmt);
        return false;
    }

    private function updateOrCreateConfig($key, $data)
    {
        $sql_find = "SELECT id FROM db_application_config_master WHERE key_code = '$key'";
        $stmt = $this->conn->prepare($sql_find, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        $stmt->execute();
        $date = date("Y-m-d H:i:s");

        if ($stmt->rowCount() > 0) {
            // Update if exist
            $sql = "UPDATE db_application_config_master
                    SET
                        amount = '$data',
                        updated_date = '$date'
                    WHERE config_type = 'loan_config' AND key_code = '$key'";
        } else {
            // Create if not exist
            $sql = "INSERT INTO db_application_config_master
                        (config_type, key_table, key_code, amount, created_date, updated_date)
                    VALUES
                        ('loan_config', 'db_request_benefit_type', '$key', '$data', '$date', '$date')";
        }

        $stmt = $this->conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        $stmt->execute();

        return $stmt;
    }

    private function isZero($param)
    {
        if (empty($param)) return 0;
        else return $param;
    }
}
