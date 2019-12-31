<?php
ob_start();
session_start();
require '../../session.php';
require_once '../../common.php';
require_once '../../lib/template.php';
require_once '../../config/database.php';
require_once '../../UC_HRBO_3/model/HRBO_316_Model.php';
include '../../utils/base_function.php';

$template = new template();
$template->set_filenames(array(
    'body' => '../view/UC_HRBO_316v.html')
);

//get database connection
$database = new Database();
$db       = $database->getConnection();
$model    = new HRBO_316_Model($db);

$model->id = isset($_GET['id']) ? $_GET['id'] : $http_response->print_error(400);

$stmt_byid = $model->getById();

$data_by_id = $stmt_byid->fetch(PDO::FETCH_ASSOC);
$data_by_id["approvers_status"]  = status($data_by_id["status"]);
$data_by_id["wdw_amount_String"] = number_format(floor(($data_by_id["wdw_v7_amount"]*100))/100, 2);
$data_by_id["limit_amount"] = number_format(floor(($data_by_id["limit_amount"]*100))/100, 2);

if ($data_by_id["status"] == "S" || $data_by_id["status"] == "D") {
    $data_by_id["btn_status"] = "<button id=\"approve\" type=\"button\" data-toggle=\"modal\" data-target=\"#md-approve\" class=\"btn btn-space btn-primary\">
    <i class=\"icon icon-left mdi mdi-check-all\"></i> ตรวจสอบแล้ว</button>";
    $data_by_id["btn_status_req_doc"] = "<button id=\"req_doc\" type=\"button\" data-toggle=\"modal\" data-target=\"#md-req-doc\" class=\"btn btn-space btn-primary\">
    <i class=\"icon icon-left mdi mdi-file-text\"></i> ขอเอกสารเพิ่มเติม</button>";
    $data_by_id["btn_status_refund"] = "<button id=\"refund\" type=\"button\" data-toggle=\"modal\" data-target=\"#md-refund\" class=\"btn btn-space btn-primary\">
    <i class=\"icon icon-left mdi mdi-money\"></i> เรียกเงินคืน</button>";

}elseif($data_by_id["status"] == "R" || $data_by_id["status"] == "Z"){
    $data_by_id["btn_status_refund"] = "<button id=\"approve\" type=\"button\" data-toggle=\"modal\" data-target=\"#md-refundapprove\" class=\"btn btn-space btn-primary\">
    <i class=\"icon icon-left mdi mdi-check-all\"></i> เรียกเงินคืนสำเร็จ</button>";
    $data_by_id["btn_status"] = "<button id=\"refund\" type=\"button\" data-toggle=\"modal\" data-target=\"#md-refundfales\" class=\"btn btn-space btn-primary\">
        <i class=\"icon icon-left mdi mdi-money\"></i> เรียกเงินคืนไม่สำเร็จ</button>";
}else {
    $data_by_id["btn_status"] = "";
}
$template->assign_vars($response_data);


$model->EmpId =isNotEmpty($data_by_id["emp_id"]);
$getIdApproval_show = $model->getIdApproval();
$num11 = $getIdApproval_show->rowCount();
if ($num11 > 0) {
   $count =1;
        while ($num_rows=$getIdApproval_show->fetch(PDO::FETCH_ASSOC)){
            $num_rows["no"]=$count;
            $newDate = date("Y-m-d H:i", strtotime($num_rows["withdraw_date"]));
            $num_rows["withdraw_date"]=$newDate;
            $num_rows["status"]= status($num_rows["status"]);
        $template->assign_block_vars('request', $num_rows);
        $count++;
    }
}

$model ->req_no =isNotEmpty($data_by_id["emp_id"]);
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
        $count++;
    }
}

$data = array(
    "menu_item"=>3,
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
            $status = "ตรวจสอบแล้ว";
            break;
        case "R":
            $status = "เรียกเงินคืน";
            break;
        case "D":
            $status = "รอเอกสารเพิ่มเติม";
            break;
        case "Z":
            $status = "เรียกเงินคืนไม่สำเร็จ";
            break;
        case "Y":
             $status = "เรียกเงินคืนสำเร็จ";
             break;
        case "N":
           $status = "ไม่อนุมัติ";
           break;
        case "I":
            $status = "รออนุมัติ";
            break;
        case "C":
            $status = "ยกเลิกด้วยตัวเอง";
            break;
        default:
            $status = "-";
            break;
    }
    return $status;
}