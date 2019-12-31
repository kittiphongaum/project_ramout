<?php
header('Content-Type: application/json');
ob_start();
session_start();
require '../../session.php';
require_once '../../common.php';
require_once '../../config/database.php';
require_once '../../utils/base_function.php';
require_once '../../UC_HRBO_3/model/HRBO_311_Model.php';

//get database connection
$database = new Database();
$db = $database->getConnection();

$model = new HRBO_311_Model($db);

$response = array();

// $_POST["file_id"] = 2;
if (isset($_POST["file_id"])) {
    $model->file_id = $_POST["file_id"];
    $num = $model->deleteByFileId();
    $response['data'] = $model;
    if ($num > 0) {
        $response["result"] = true;
    } else {
        $response["result"] = false;
    }

} else {
    $response["result"] = false;
}

printJson($response);
