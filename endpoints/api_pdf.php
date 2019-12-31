<?php
header('Content-Type: application/json');
ob_start();
session_start();
require '../session.php';
require_once '../common.php';
require_once '../config/database.php';
require_once '../UC_HRBO_2/model/HRBO_204_Model.php';
require_once '../utils/base_function.php';

//get database connection
$database = new Database();
$db = $database->getConnection();

$model = new HRBO_204_Model($db);


$response_data = array();

$model->id = $_POST["id"];

$model->getnonreq();

$response_data["th"] = $model->file_gen_name_th;
$response_data["path"] = $host  . $model->file_path;
$response_data["eng"] = $model->file_gen_name_eng;

printJson($response_data);
