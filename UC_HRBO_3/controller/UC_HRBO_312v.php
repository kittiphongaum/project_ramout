<?php
ob_start();
session_start();
require '../../session.php';
require_once '../../common.php';
require_once '../../lib/template.php';
require_once '../../config/database.php';
require_once '../../UC_HRBO_3/model/HRBO_312_Model.php';
include '../../utils/base_function.php';

//prepare template
$template = new template();
$template->set_filenames(array(
    'body' => '../view/UC_HRBO_312v.html')
);

//get database connection
$database = new Database();
$db = $database->getConnection();

// prepare product object
$model = new HRBO_312_Model($db);
$model->id = isset($_GET['id']) ? $_GET['id'] : $http_response->print_error(400);
$model->variable_id = isset($_GET['variableId']) ? $_GET['variableId'] : $http_response->print_error(400);

$model->OneAll();

$response_data = array();

$response_data["greater_than"] = substr($model->greater_than,0,-2);
$response_data["title"]        = $model->title;
$response_data["less_than"]    = substr($model->less_than,0,-2);
$response_data["amount"]       = substr($model->amount,0,-2);
$response_data["key_code"]     = $model->key_code;
$response_data["key_name"]     = $model->key_name;
$response_data["config_id"]    = $model->config_id;
$response_data["variable_id"]  = $model->variable_id;

$template->assign_vars($response_data);

$data = array(
    "menu_item" => 3,
);

$template->assign_vars($data);
$template->pparse('body');
