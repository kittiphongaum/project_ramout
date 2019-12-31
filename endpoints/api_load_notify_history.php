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

//get database connection
$database = new Database();
$db = $database->getConnection();

$model = new notifyhistoryModel($db);


$response_data = array();
if (!empty($decoded["emp_id"])) {
    $model->to_emp_id = $decoded["emp_id"];
    $model->on_page = $decoded["on_page"];
    $stmt = $model->getAll();
    $num = $stmt->rowCount();
    if ($num > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $date=date_create($row["created_date"]);
            $response_item = array(
                "id" => $row["id"],
                "msg" => $row["msg"],
                "category" => $row["category"],
                "notify_type" => $row["notify_type"],
                "to_emp_id" => $row["to_emp_id"],
                "created_date" => date_format($date,"d-m-Y"),

            );

            array_push($response_data, $response_item);
        }
    }
}

printJson($response_data);
