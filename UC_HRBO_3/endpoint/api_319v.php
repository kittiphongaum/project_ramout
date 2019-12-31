<?php

header('Content-Type: application/json');
require_once '../../config/database.php';
include_once '../model/HRBO_319_Model.php';

$database = new Database();
$db       = $database->getConnection();

$model         = new HRBO_319_Model($db);
$model->EmpId  = $_POST["emp_id"];
$model->doc_no = $_POST["doc_no"];
$stmt          = $model->memberty();
$num           = $stmt->rowCount();
$resp          = array();
if ($num > 0) {
    $count = 1;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        array_push($resp, $row);
        $count++;
    }
}

echo json_encode($resp, JSON_UNESCAPED_UNICODE);
