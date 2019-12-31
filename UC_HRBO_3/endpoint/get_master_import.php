<?php

ob_start();
session_start();
require '../../session.php';
require_once '../../common.php';
require_once '../../config/database.php';
require_once '../model/course_master_Model_import.php';
require_once "../../vendor/autoload.php";
require_once '../../utils/validate_excel.php';

$database = new Database();
$db       = $database->getConnection();

$model = new course_master_Model_import($db);

$stmt = $model->getAll();
$num = $stmt->rowCount();
$resp = array();
if ($num > 0) {
    $response_data = array();
    $count = 1;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $row["no"] = $count;
        array_push($resp, $row);
        $count++;
        
    }
}
echo json_encode($resp, JSON_UNESCAPED_UNICODE);
