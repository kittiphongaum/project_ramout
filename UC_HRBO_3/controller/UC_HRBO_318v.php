<?php
ob_start();
session_start();
require '../../session.php';
require_once '../../common.php';
require_once '../../lib/template.php';
require_once '../../config/database.php';
require_once '../../UC_HRBO_3/model/HRBO_318_Model.php';
include '../../utils/base_function.php';

$template = new template();
$template->set_filenames(array(
    'body' => '../view/UC_HRBO_318v.html')
);
//get database connection
$database = new Database();
$db       = $database->getConnection();
$model    = new HRBO_318_Model($db);

$model->id             = isset($_GET['id']) ? $_GET['id'] : $http_response->print_error(400);
$stmt_byid             = $model->getById();
$data_by_id            = $stmt_byid->fetch(PDO::FETCH_ASSOC);
$data_by_id["EmpName"] = $data_by_id["emp_title"] . $data_by_id["emp_first_name"] . " " . $data_by_id["emp_last_name"];

if ($data_by_id["status"] == "S") {
    $data_by_id["btn_status"] = "<button id=\"nonapprove\" type=\"button\" data-toggle=\"modal\" data-target=\"#md-nonapprove\" class=\"btn btn-space btn-primary\">
    <i class=\"icon icon-left mdi mdi-close\"></i> ไม่ผ่าน</button><button id=\"approve\" type=\"button\" data-toggle=\"modal\" data-target=\"#md-approve\" class=\"btn btn-space btn-primary\">
    <i class=\"icon icon-left mdi mdi-check\"></i> ผ่าน</button>";
} elseif ($data_by_id["status"] == "A") {
    $data_by_id["btn_status"] = "<button id=\"completed\" type=\"button\" data-toggle=\"modal\" data-target=\"#md-completed\" class=\"btn btn-space btn-primary\">
    <i class=\"icon icon-left mdi mdi-close\"></i> Completed</button>";
} else {
    $data_by_id["btn_status"] = "";
}

$model->EmpId = isNotEmpty($data_by_id["emp_id"]);
$stmt_noti    = $model->historyLoan();
$num          = $stmt_noti->rowCount();
if ($num > 0) {
    $response_data = array();
    $count         = 1;
    while ($row = $stmt_noti->fetch(PDO::FETCH_ASSOC)) {
        $newDate             = date("Y-m-d", strtotime($row["created_date"]));
        $row["req_datetime"] = $newDate;
        $row["no"]           = $count;
        $row["Id"]           = $row["transaction_id"];
        $row["EmpName"]      = $row["emp_title"] . $row["emp_first_name"] . " " . $row["emp_last_name"];
        $template->assign_block_vars('request', $row);
        // unset($rows);
        $count++;
    }
}

$data = array(
    "menu_item" => 3,
);
$template->assign_vars($data_by_id);
$template->assign_vars($data);
$template->pparse('body');

function status($status)
{
    switch ($status) {
        case "S":
            $status = "รอตรวจสอบ";
            break;
        case "A":
            $status = "ผ่าน";
            break;
        case "D":
            $status = "ไม่ผ่าน";
            break;
        case "C":
            $status = "Completed";
            break;
        default:
            $status = "-";
            break;
    }
    return $status;
}
