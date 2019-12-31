<?php
ob_start();
session_start();
require '../../session.php';
require_once '../../common.php';
require_once '../../lib/template.php';
require_once '../../config/database.php';
require_once '../../UC_HRBO_3/model/HRBO_319_Model.php';
include '../../utils/base_function.php';

$template = new template();
$template->set_filenames(array(
    'body' => '../view/UC_HRBO_319v.html')
);
//get database connection
$database = new Database();
$db       = $database->getConnection();
$model    = new HRBO_319_Model($db);

$model->id = isset($_GET['id']) ? $_GET['id'] : $http_response->print_error(400);
$stmt_byid = $model->getById();
$data_by_id = $stmt_byid->fetch(PDO::FETCH_ASSOC);
// var_dump($data_by_id);exit; 

$data_by_id["EmpName"]=$data_by_id["emp_title"].$data_by_id["emp_first_name"]." ".$data_by_id["emp_last_name"];


if ($data_by_id["status"] == "I") {
    $data_by_id["Status_"] = "<button id=\"approve\" type=\"button\" data-toggle=\"modal\" data-target=\"#md-req-doc\" class=\"btn btn-space btn-primary\">
    <i class=\"icon icon-left mdi mdi-bell\"></i>ขอเอกสารเพิ่มเติม</button><button id=\"approve\" type=\"button\" data-toggle=\"modal\" data-target=\"#md-approve\" class=\"btn btn-space btn-primary\">
    <i class=\"icon icon-left mdi mdi-check-all\"></i> ยืนยันผลการตรวจ</button><button id=\"nonapprove\" type=\"button\" data-toggle=\"modal\" data-target=\"#md-nonapprove\" class=\"btn btn-space btn-primary\">
    <i class=\"icon icon-left mdi mdi-close\"></i>ไม่อนุมัติ</button>";
} else {
    $data_by_id["Status_"] = "";

}
$data_by_id["Status"]  = status($data_by_id["status"]);
$model->EmpId =isNotEmpty($data_by_id["emp_id"]);
$model ->rep =isNotEmpty($data_by_id["doc_no"]);
$stmt_noti = $model->getallnoti();
$num_noti = $stmt_noti->rowCount();

        if ($num_noti > 0) {
            $count = 1;

            while ($row = $stmt_noti->fetch(PDO::FETCH_ASSOC)) {
                $row["no"]  = $count;
             $newDate = date("Y-m-d H:i", strtotime($row["created_date"]));
             $row["created_date"]=$newDate;
             $template->assign_block_vars('request2',$row);
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
        case "I":
            $status = "รอพิจารณา";
            break;
        case "A":
            $status = "อนุมัติ";
            break;
        case "X":
            $status = "ไม่อนุมัติ";
            break;
        default:
            $status = "-";
            break;
    }
    return $status;
}