<?php
ob_start();
session_start();

require '../../session.php';
require_once '../../common.php';
require_once '../../lib/template.php';
require_once '../../config/database.php';
include '../../utils/base_function.php';
require_once '../model/HRBO_320_Model.php';

// Call view
$template = new template();
$template->set_filenames(array(
    'body' => '../view/UC_HRBO_320.html'
));

// Get connections
$database = new Database();
$db = $database->getConnection();

$model = new HRBO_320_Model($db);

$stmt = $model->getAll();

$num = $stmt->rowCount();
    if ($num > 0) {
    $response_data = array();
    $count = 1;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $row["loan_status"]   = status($row["loan_status"]);
            $template->assign_block_vars('request', $row);
            $count++;
    }
}

$data = array(
    "menu_item" => 3
);

// Assign data to view
 $template->assign_vars($data);
$template->pparse('body');

function status($status){
    switch ($status) {
        case "T":
            $status = "เปิด ใช้งานเงื่อนไขจำนวนเงินเท่า<br/>ปิด ใช้งานเงื่อนไขจำนวนเงินบาท";
            break;
        case "B":
            $status = "ปิด ใช้งานเงื่อนไขจำนวนเงินเท่า<br/>เปิด ใช้งานเงื่อนไขจำนวนเงินบาท";
            break;
        default:
            $status = "ยังไม่ได้ตั้งค่า";
            break;
    }
    return $status;
}