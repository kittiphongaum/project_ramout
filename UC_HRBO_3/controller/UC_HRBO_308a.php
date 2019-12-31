<?php
ob_start();
session_start();
require '../../session.php';
require_once '../../common.php';
require_once '../../lib/template.php';
require_once '../../config/database.php';
require_once '../../UC_HRBO_3/model/HRBO_308_Model.php';
include_once '../../utils/base_function.php';
include_once '../../utils/http_response.php';

//prepare template
$template = new template();
$template->set_filenames(array(
    'body' => '../view/UC_HRBO_308a.html')
);

// prepare http_response
$http_response = new http_response();

//get database connection
$database = new Database();
$db = $database->getConnection();

$model = new HRBO_308_Model($db);

$result = isset($_GET["result"])? $_GET["result"] : "x";
$records = isset($_GET["records"])? $_GET["records"] : "x";

$data = array(
    "menu_item" => 2,
    "result" => $result,
    "records" => $records
);

$template->assign_vars($data);
$template->pparse('body');

?>