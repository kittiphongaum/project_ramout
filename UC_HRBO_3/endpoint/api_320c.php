<?php

header('Content-Type: application/json');
require_once '../../config/database.php';
include_once '../model/HRBO_320_Model.php';

$database = new Database();
$db       = $database->getConnection();

$model = new HRBO_320_Model($db);

$stmt = $model->getBorrower();
$num = $stmt->rowCount();
$resp = array();
if ($num > 0) {
    $count = 1;

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $row["no"] = $count;
        $row["created_date"] = date("Y-m-d H:i", strtotime($row["created_date"]));
        $row["status"]       = reformatStatus($row["active_status"]);
        array_push($resp, $row);
        $count++;
    }
}
function reformatStatus($status)
{
    if ($status == "A") {
        return '<span style="color: green;">เปิดใช้งาน</span>';
    } else if ($status == "I") {
        return '<span style="color: red;">ปิดการใช้งาน</span>';
    } else{
        return "";
    }
}
echo json_encode($resp, JSON_UNESCAPED_UNICODE);
