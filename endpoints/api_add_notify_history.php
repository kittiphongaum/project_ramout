<?php
header('Content-Type: application/json');
ob_start();
session_start();
require '../session.php';
require_once '../common.php';
require_once '../config/database.php';
require_once '../models/notifyhistoryModel.php';
require_once '../utils/base_function.php';

$content = trim(file_get_contents("php://input"));
$decoded = json_decode($content, true);

$response_data = array();

// get database connection
$database = new Database();
$db = $database->getConnection();

// // prepare object
$model = new notifyhistoryModel($db);
$model->msg = $decoded["msg"];
$model->category = $decoded["category"];
$model->notify_type = $decoded["notify_type"];
$model->to_emp_id = $decoded["to_emp_id"];
$model->on_page = $decoded["on_page"];

$lastId = $model->create();

if ($lastId <= 0) {
    $response_data["error_code"] = "500";
    $response_data["error_message"] = "Fail";
} else {
    $response_data["error_code"] = "200";
    $response_data["error_message"] = "Success";
}

printJson($response_data);
