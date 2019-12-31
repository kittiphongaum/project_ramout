<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
ob_start();
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require '../../session.php';
require_once '../../common.php';
require_once '../../config/database.php';
require_once '../../utils/base_function.php';
require_once '../../UC_HRBO_3/model/HRBO_312_Model.php';

//Make sure that it is a POST request.
if (strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') != 0) {
    die('Request method must be POST!');
}

//get database connection
$database = new Database();
$db = $database->getConnection();

// Get Model
$model = new HRBO_312_Model($db);

// get posted data
$input = file_get_contents('php://input');
$data = json_decode($input);

// make sure data is not empty
if (
    !empty($data->less_than) &&
    !empty($data->key_code) &&
    !empty($data->key_name) &&
    !empty($data->amount) &&
    !empty($data->config_id) &&
    !empty($data->variable_id)
  ) {

    // Defined result var
    $result = [];

    $model->update_date    = Date('Y-m-d H:i:s');
    $model->greater_than   = $data->greater_than;
    $model->title          = $data->key_code;
    $model->less_than      = $data->less_than;
    $model->amount         = $data->amount;
    $model->key_name       = $data->key_name;
    $model->config_id      = $data->config_id;
    $model->variable_id    = $data->variable_id;

    if ($data->amount < $data->less_than && $data->amount > $data->greater_than) {

        $stmt =  $model->searching();
        $num  = $stmt->rowCount();

        if ($num > 0) {
            $resp["result"] = "3";

        } else {
            $model->update_config_Master();
            $model->update_variable_Master();
            $resp["result"] = "0";
        }
    } else {
        $resp["result"] = "1";
    }
} else {
    $resp["result"] = "2";
}

printJson($resp);
