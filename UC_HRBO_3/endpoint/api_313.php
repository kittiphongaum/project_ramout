<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../../config/database.php';
include_once '../model/HRBO_313_Model.php';
require_once '../../common.php';

//Make sure that it is a POST request.
if (strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') != 0) {
    die('Request method must be POST!');
}

// get database connection
$database              = new Database();
$db                    = $database->getConnection();
$model                 = new HRBO_313_Model($db);
$model->transaction_id = $_POST["transaction_id"];
$model->status         = $_POST["status"];
if ($model->update_status()) {
    $response["status"] = "success";
} else {
    $response["status"] = "fail";
}

echo json_encode($response);
