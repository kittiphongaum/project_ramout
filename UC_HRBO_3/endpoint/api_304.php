<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../../config/database.php';
include_once '../model/HRBO_304_Model.php';

//Make sure that it is a POST request.
if (strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') != 0) {
    die('Request method must be POST!');
}

// get database connection
$database = new Database();
$db       = $database->getConnection();

// prepare product object
$model = new HRBO_304_Model($db);
// get posted data

$input = file_get_contents('php://input');
$data  = json_decode($input);

// make sure data is not empty
if (!empty($data->housId) &&
    !empty($data->Message) &&
    !empty($data->Statused)
    ) {
  
    $model->housId = $data->housId;
    $model->statused = $data->Statused;
    $model->message = $data->Message;
    $model->updated_date = Date('Y-m-d H:i:s');
    $model->update();
 }


 
