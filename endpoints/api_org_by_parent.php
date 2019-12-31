<?php
header('Content-Type: application/json');
ob_start();
session_start();
require '../session.php';
require_once '../common.php';
require_once '../config/database.php';
require_once '../models/orgChartModel.php';
require_once '../utils/base_function.php';

$content = trim(file_get_contents("php://input"));
$decoded = json_decode($content, true);

//get database connection
$database = new Database();
$db       = $database->getConnection();

$model = new orgChartModel($db);

$model->dept_name     = $decoded["search"];
$model->parent_org_id = $decoded["parent_org_id"];

$stmt = $model->getORGbyParent();

$response_data = array();

$num = $stmt->rowCount();
if ($num > 0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $response_item = array(
            "department_id"        => $row["department_id"],
            "department_name"      => $row["department_name"],
            "department_long_name" => $row["department_long_name"],
        );

        array_push($response_data, $response_item);
    }
}

printJson($response_data);
