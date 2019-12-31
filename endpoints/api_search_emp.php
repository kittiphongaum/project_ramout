<?php
header('Content-Type: application/json');
ob_start();
session_start();
//require '../session.php';
require_once '../common.php';
require_once '../config/database.php';
require_once '../models/orgChartModel.php';
require_once '../utils/base_function.php';

//get database connection
$database = new Database();
$db = $database->getConnection();

$model = new orgChartModel($db);

$response_data = array();
if (isset($_POST["query"])) {
    $model->emp_name = $_POST["query"];
    $model->emp_code = $_POST["query"];
    $stmt = $model->getEMP();
    $num = $stmt->rowCount();
    if ($num > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $response_item = array(
                "id" => $row["emp_id"],
                "name" => $row["emp_name"],
                "position" => $row["position_name"],
                "department" => $row["department_name"]
            );

            array_push($response_data, $response_item);
        }
    }
}

printJson($response_data);
