<?php
ob_start();
session_start();
require '../../session.php';
require_once '../../common.php';
require_once '../../lib/template.php';
require_once '../../config/database.php';
require_once '../../UC_HRBO_3/model/HRBO_310_Model.php';
include '../../utils/base_function.php';

$template = new template();
$template->set_filenames(array(
    'body' => '../view/UC_HRBO_310v.html')
);
//get database connection
$database = new Database();
$db       = $database->getConnection();
$model    = new HRBO_310_Model($db);

$model->id = isset($_GET['id']) ? $_GET['id'] : $http_response->print_error(400);

$model->OneAll();

$response_data = array();
$response_data["EmpName"] = $model->EmpName;
$response_data["EmpId"] = $model->EmpId;
$response_data["EmpDep"] = $model->EmpDep;
$response_data["CreatedDate"] = $model->CreatedDate;
$response_data["EmpPos"] = $model->EmpPos;
$response_data["DepPar"] = $model->DepPar;
$response_data["department_name"] = $model->department_name;
$response_data["Id"] = $model->Id;
$response_data["req_no"] = $model->req_no;
$response_data["ReceiptNo"] = $model->ReceiptNo;
$response_data["receipt_date"] = $model->receipt_date;
$response_data["objective"] =  $model->objective;
$response_data["emp_id"] = $model->emp_id;
$response_data["name"] = $model->name;
$response_data["rights_user"] = $model->rights_user;
$response_data["name_of_hospital"] = $model->name_of_hospital;
$response_data["tel"] = $model->tel;
$response_data["credit_limit"] = number_format($model->credit_limit, 2, '.', ',');
$response_data["current_outstanding_debt"] = number_format($model ->current_outstanding_debt, 2, '.', ',');
$response_data["credit_limit_balance"] =  number_format($model ->credit_limit_balance, 2, '.', ',');
$response_data["amount_loan_requested"] = number_format($model ->amount_loan_requested, 2, '.', ',');
$response_data["installment_period_time"] = $model ->installment_period_time;
$response_data["salary"] = number_format($model ->salary, 2, '.', ',');
$response_data["amount_deduction"] = number_format($model ->amount_deduction, 2, '.', ',');
$response_data["monthly_payments"] = number_format($model ->monthly_payments, 2, '.', ',');
$response_data["percentage_deduction_money"] = $model ->percentage_deduction_money;
$response_data["net_salary"] = number_format($model ->net_salary, 2, '.', ',');
$response_data["position_name"] = $model ->position_name;

$template->assign_vars($response_data);

$stmt = $model->All();
$model->EmpId;
$num = $stmt->rowCount();

if ($num > 0) {
    $response_data = array();
    $count = 1;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $originalDate = $row["CreatedDate"];
        $newDate = date("Y-m-d H:i", strtotime($originalDate));
        $row["req_datetime"] =  $newDate;
        $row["no"]           = $count;

        $template->assign_block_vars('request', $row);
        unset($rows);
        $count++;
    }
}

$data = array(
    "menu_item"=>3,
);

$template->assign_vars($data);
$template->pparse('body');
