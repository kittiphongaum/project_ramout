<?php
class HRBO_304_Model
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
        $this->conn = $db;
        $this->benefit_code = 'scholarship';
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
                    t.req_no As req_no
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
                emp.emp_name AS EmpName,
                t.emp_id AS EmpId,
                emp.department_name AS EmpDep,
                t.created_date AS CreatedDate,
                emp.position_name AS EmpPos,
                dept2.parent_department_name AS DepPar,
                t.id AS Id,
                t.req_amount AS priviledge_month,
                t.req_no AS req_no,
                t.req_status AS reqstatus,
                JSON_VALUE(req_info ,'$.approver.name') AS approvers_name,
                JSON_VALUE(req_info ,'$.invioce.receipt_no') AS receipt_no,
                JSON_VALUE(req_info ,'$.invioce.receipt_date') AS receipt_date,
                JSON_VALUE(req_info ,'$.invioce.receive_time') AS receive_time,
                JSON_VALUE(req_info ,'$.invioce.receive_year') AS receive_year,
                JSON_VALUE(req_info ,'$.withdraw.withdraw_amount') AS withdraw_amount,
                JSON_QUERY(req_info ,'$.withdraw.lists') AS req_info,
                JSON_VALUE(req_info ,'$.withdraw.scholarship_limit') AS scholarship_limit,
                t.benefit_code AS benefitcode
                    FROM trans_req_benefits AS t
                    LEFT JOIN db_request_benefit_type AS r ON r.benefit_code = t.benefit_code
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

                // set values to object properties
                $this->EmpName             = $row['EmpName'];
                $this->EmpId               = $row['EmpId'];
                $this->EmpDep              = $row['EmpDep'];
                $this->CreatedDate         = $row['CreatedDate'];
                $this->EmpPos              = $row['EmpPos'];
                $this->DepPar              = $row['DepPar'];
                $this->Id                  = $row['Id'];
                $this->priviledge_month    = $row['priviledge_month'];
                $this->req_no              = $row['req_no'];
                $this->reqstatus           = $row['reqstatus'];
                $this->approvers_name      = $row['approvers_name'];
                $this->receipt_no          = $row['receipt_no'];
                $this->receipt_date        = $row['receipt_date'];
                $this->receive_time        = $row['receive_time'];
                $this->receive_year        = $row['receive_year'];
                $this->withdraw_amount     = $row['withdraw_amount'];
                $this->scholarship_limit   = $row['scholarship_limit'];
                $this->req_info            = $row['req_info'];
                $this->housId              = $row['Id'];
                $this->benefitcode         = $row['benefitcode'];

                // json array
                $this->json_array = json_decode($row['req_info'], true);
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

    public function search()
    {
        $query = "SELECT
                    emp.emp_name  AS EmpName,
                    t.emp_id AS EmpId,
                    emp.department_name  AS EmpDep,
                    t.created_date AS CreatedDate,
                    emp.position_name AS EmpPos,
                    dept2.parent_department_name AS DepPar,
                    t.req_status AS Status,
                    t.id AS Id,
                    t.req_amount AS priviledge_month,
                    t.req_no AS req_no
                        FROM trans_req_benefits AS t
                        LEFT JOIN db_request_benefit_type AS r ON r.benefit_code = t.benefit_code
                        LEFT JOIN v_employee_profile AS emp ON t.emp_id = emp.emp_id
                        LEFT JOIN db_org_hr_department AS dept2 ON dept2.department_id = emp.department_short_id
                        WHERE 1=1 AND  t.benefit_code = 'scholarship'";

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

    public function getallnoti()
    {
        $query = "SELECT * FROM NOTIFICATION_HISTRORY  WHERE benefit_code = 'scholarship' AND req_no = :req_no ORDER BY created_date DESC";
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
