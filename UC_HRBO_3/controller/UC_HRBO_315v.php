<?php

ob_start();
session_start();
require '../../session.php';
require_once '../../common.php';
require_once '../../lib/template.php';
require_once '../../config/database.php';
require_once '../../UC_HRBO_3/model/HRBO_315_Model.php';
include '../../utils/base_function.php';

$template = new template();
$template->set_filenames(array(
    'body' => '../view/UC_HRBO_315v.html')
);
//get database connection
$database = new Database();
$db       = $database->getConnection();
$model    = new HRBO_315_Model($db);

$model->id = isset($_GET['id']) ? $_GET['id'] : $http_response->print_error(400);

$stmt_byid = $model->getById();

$data_by_id                     = $stmt_byid->fetch(PDO::FETCH_ASSOC);
$data_by_id["approvers_status"] = status($data_by_id["status"]);
// $data
$data_by_id["Slip_date"] = (date('Y', strtotime($data_by_id["slip_date"]))) + 543;

$data_by_id["claim_amount"] = number_format(floor(($data_by_id["claim_amount"]*100))/100, 2);
$data_by_id["rent_amount"] = number_format(floor(($data_by_id["rent_amount"]*100))/100, 2);
if ($data_by_id["posta"] === "") {
    $data_by_id["Posta"] = "";
} else {
    $data_by_id["Posta"] = "อพาร์เมนต์ " . $data_by_id["posta"];
}

if ($data_by_id["bldig"] === null) {
    $data_by_id["Bldig"] = "";
} else {
    $data_by_id["Bldig"] = "ตึก " . $data_by_id["bldig"];
}

if ($data_by_id["floor"] === null) {
    $data_by_id["Floor"] = "";
} else {
    $data_by_id["Floor"] = "ชั้น " . $data_by_id["floor"];
}
if ($data_by_id["locat"] === null) {
    $data_by_id["Locat"] = "";
} else {
    $data_by_id["Locat"] = "ซอย " . $data_by_id["locat"];
}
if ($data_by_id["stras"] === null) {
    $data_by_id["Stras"] = "";
} else {
    $data_by_id["Stras"] = "หมู่บ้าน" . $data_by_id["stras"];
}
if ($data_by_id["dar03"] === null) {
    $data_by_id["Dar03"] = "";
} else {
    $data_by_id["Dar03"] = "ถนน " . $data_by_id["dar03"];
}
$data_by_id["ort02"]    = "ตำบล/แขวง " . $data_by_id["ort02"];
$data_by_id["ort01"]    = "อำเภอ/เขต " . $data_by_id["ort01"];
$data_by_id["prorvine"] = $data_by_id["prorvine"];

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
$data_by_id["req_date_"]=date("Y-m-d H:i", strtotime($data_by_id["req_date"]));
$template->assign_vars($data_by_id);

$model->EmpId = isNotEmpty($data_by_id["emp_id"]);

$getIdApproval_show = $model->getIdApproval();
$num11              = $getIdApproval_show->rowCount();
if ($num11 > 0) {
    $count = 1;
    while ($num_rows = $getIdApproval_show->fetch(PDO::FETCH_ASSOC)) {
        $num_rows["no"]     = $count;
        $num_rows["status"] = status($num_rows["status"]);
        $template->assign_block_vars('request', $num_rows);
        $count++;
    }
}

$model->rep = isNotEmpty($data_by_id["doc_no"]);
$stmt_noti  = $model->getallnoti();
$num_noti   = $stmt_noti->rowCount();
if ($num_noti > 0) {
    $count = 1;

    while ($row = $stmt_noti->fetch(PDO::FETCH_ASSOC)) {
        $originalDate        = $row["req_date"];
        $newDate             = date("Y-m-d H:i", strtotime($originalDate));
        $row["req_datetime"] = $newDate;
        $row["no"]           = $count;

        $TEXT = str_replace("\n", "<br>\n", $row["description"]);

        $row["description"] = $TEXT;

        $template->assign_block_vars('request2', $row);
        unset($rows);
        $count++;
    }
}

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
