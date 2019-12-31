<?php

ob_start();
session_start();
require '../../session.php';
require_once '../../common.php';
require_once '../../lib/template.php';
require_once '../../config/database.php';
require_once '../../UC_HRBO_3/model/HRBO_301_Model.php';
include '../../utils/base_function.php';

$template = new template();
$template->set_filenames(array(
    'body' => '../view/UC_HRBO_301v.html')
);

//get database connection
$database = new Database();
$db       = $database->getConnection();
$model    = new HRBO_301_Model($db);

$model->id = isset($_GET['id']) ? $_GET['id'] : $http_response->print_error(400);
$model->OneAll();

$response_data = array();

$response_data["priviledge_amount"] = number_format( $model->priviledge_amount, 2, '.', ',');
$response_data["priviledge_month"]  = number_format($model->priviledge_month,2, '.', ',');
$response_data["department_name"]   = $model->department_name;
$response_data["approvers_name"]    = $model->approvers_name;
$response_data["receipt_date"]      = date("Y-m-d", strtotime($model->receipt_date));
$response_data["CreatedDate"]       = date("Y-m-d", strtotime($model->CreatedDate));
$response_data["draw_amount"]       = number_format($model->draw_amount, 2, '.', ',');
$response_data["benefitcode"]       = $model->benefitcode;
$response_data["receipt_no"]        = $model->receipt_no;
$response_data["commander"]         = $model->commander;
$response_data["EmpName"]           = $model->EmpName;
$response_data["address"]           = $model->address;
$response_data["EmpDep"]            = $model->EmpDep;
$response_data["EmpPos"]            = $model->EmpPos;
$response_data["housId"]            = $model->housId;
$response_data["req_no"]            = $model->req_no;
$response_data["DepPar"]            = $model->DepPar;
$response_data["EmpId"]             = $model->EmpId;
$response_data["Id"]                = $model->Id;


if (($model->reqstatus == "1") || ($model->reqstatus == "3")) {
    $response_data["Status"] = "";
} else {
    $response_data["Status"] = "<button id=\"approve\" type=\"button\" data-toggle=\"modal\" data-target=\"#md-approve\" class=\"btn btn-space btn-primary\">
    <i class=\"icon icon-left mdi mdi-check-all\"></i> ยืนยันผลการตรวจ</button>";
}

$template->assign_vars($response_data);

$stmt_all = $model->All();
$num = $stmt_all->rowCount();

if ($num > 0) {
    $response_data = array();
    $count = 1;
    while ($row = $stmt_all->fetch(PDO::FETCH_ASSOC)) {
        $originalDate = $row["CreatedDate"];
        $newDate      = date("Y-m-d H:i", strtotime($originalDate));

        $row["req_datetime"] = $newDate;
        $row["req_status"]   = reformatStatus($row["Status"]);
        $row["no"]           = $count;
        $row["priviledge_month"] = number_format($row["priviledge_month"], 2, '.', ',');

        $template->assign_block_vars('request', $row);

        unset($rows);
        $count++;
    }
}

$stmt_noti = $model->getallnoti();
$num_noti = $stmt_noti->rowCount();
if ($num_noti > 0) {
    $count = 1;

    while ($row = $stmt_noti->fetch(PDO::FETCH_ASSOC)) {
        $originalDate = $row["created_date"];
        $newDate = date("Y-m-d H:i", strtotime($originalDate));
        $row["req_datetime"] = $newDate;
        $row["no"]           = $count;

        $TEXT = str_replace("\n", "<br>\n", $row["description"]); 

        $row["description"]  = $TEXT;
        
        $template->assign_block_vars('request2', $row);
        unset($rows);
        $count++;
    }
}

$data = array(
    "menu_item"=>3,
);

$template->assign_vars($data);
$template->pparse('body');
