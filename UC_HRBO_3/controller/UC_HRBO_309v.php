<?php
ob_start();
session_start();
require '../../session.php';
require_once '../../common.php';
require_once '../../lib/template.php';
require_once '../../config/database.php';
require_once '../../UC_HRBO_3/model/HRBO_309_Model.php';
include '../../utils/base_function.php';

$template = new template();
$template->set_filenames(array(
    'body' => '../view/UC_HRBO_309v.html')
);
//get database connection
$database = new Database();
$db       = $database->getConnection();
$model    = new HRBO_309_Model($db);

$model->transaction_id = isset($_GET['id']) ? $_GET['id'] : $http_response->print_error(400);

$result               = $model->get();
$result["req_date"]   = date("d/m/Y H:i:s", strtotime($result["req_date"]));
$result["admit_date"] = date("d/m/Y", strtotime($result["admit_date"]));
$template->assign_vars($result);

$approve_html = "";
if ($result["status"] === 'S') {
    $approve_html = '<button id="denied" type="button" data-toggle="modal" data-target="#md-denied" class="btn btn-space btn-secondary">
        <i class="icon icon-left mdi mdi-close"></i> ไม่สามารถจัดทำใบส่งตัวได้</button>
    <button id="approve" type="button" data-toggle="modal" data-target="#md-approve" class="btn btn-space btn-primary">
        <i class="icon icon-left mdi mdi-check-all"></i> จัดทำใบส่งตัวเรียบร้อยแล้ว</button>';
}

function status($data){
    if ($data === 'I') {
        $status_approve = 'รออนุมัติ';
    } elseif ($data === 'S') {
        $status_approve = 'แก้ไขจัดทำ และอนุมัติแล้ว';
    } elseif ($data === 'N') {
        $status_approve = 'ไม่อนุมัติ';
    } elseif ($data === 'A') {
        $status_approve = 'จัดทำใบส่งตัวเรียบร้อยแล้ว';
    } elseif ($data === 'X') {
        $status_approve = 'ไม่สามารถจัดทำใบส่งตัวได้';
    }elseif($data==="C"){
        $status_approve = 'ยกเลิกด้วยตัวเอง';
    }
    return $status_approve;
}
$template->assign_var('approve_html', $approve_html);

$data = array(
    "menu_item"      => 3,
    "data"           => json_encode($result),
    "id"             => $_GET["id"],
    "status_approve" => $status_approve,
);
$template->assign_vars($data);
$template->pparse('body');
