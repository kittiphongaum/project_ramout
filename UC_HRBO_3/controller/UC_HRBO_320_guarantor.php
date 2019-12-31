<?php
ob_start();
session_start();

require '../../session.php';
require_once '../../common.php';
require_once '../../lib/template.php';
require_once '../../config/database.php';
include '../../utils/base_function.php';
require_once '../model/HRBO_320_Model.php';

// Call view
$template = new template();
$template->set_filenames(array(
    'body' => '../view/UC_HRBO_320_guarantor.html',
));

// Get connections
$database = new Database();
$db       = $database->getConnection();

$model = new HRBO_320_Model($db);

$model->id = $_GET["guarantor"];
if ($_GET["guarantor"] != "" || $_GET["guarantor"] !=null) {
    $stmt = $model->guarantorById($_GET["guarantor"]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $resp =$result;
    $resp["dis"]= "disabled";
    $resp["optiongroup"] ='<option value="'.$resp["subgroup"].'" hidden>'.$resp["subgroup_name"] .'</option>';
}

// Set var
$data = array(
    "menu_item" => 3,
);
// Assign data to view
$template->assign_vars($resp);
$template->assign_vars($data);
$template->pparse('body');

