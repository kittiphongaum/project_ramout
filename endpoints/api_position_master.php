<?php
header('Content-Type: application/json');
ob_start();
session_start();

require_once '../common.php';
require_once '../config/database.php';
require_once '../models/positionMasterModel.php';
require_once '../utils/base_function.php';

//get database connection
$database = new Database();
$db = $database->getConnection();

$model = new positionMasterModel($db);

//$_POST["query"] = "บุญ";
$response_data = array();
    $model->position_name = $_POST["position_name"];
    $stmt = $model->getAll();
    $num = $stmt->rowCount();
    if ($num > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            array_push($response_data, $row);
        }
    }

printJson($response_data);
