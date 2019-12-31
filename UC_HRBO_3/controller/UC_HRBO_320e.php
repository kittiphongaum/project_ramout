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
    'body' => '../view/UC_HRBO_320e.html',
));

// Get connections
$database = new Database();
$db       = $database->getConnection();

$model = new HRBO_320_Model($db);

$model->category = $_GET["category"];
$stmt            = $model->get();

$result = $stmt->fetch(PDO::FETCH_ASSOC);
$resp["item_value"]       = $result["item_value"];  
$resp["category_id"]      = $result["category"];
$resp["category"]         = $result["id"]+"|"+$result["category"]+"|"+$result["actvie_flag"]+"|"+$result["item_desc"]+"|"+$result["item_value"]+"|"+$result["item_value2"];
$resp["category_title"]   = $result["item_desc"];
$resp["item_amount_bat"]  = $result["item_amount_bat"];
$resp["item_amount_mamy"] = $result["item_amount_mamy"];
$resp["loan_status"]      = $result["loan_status"];
$resp["period_amount"]      = $result["period_amount"] ;
$resp["guarantor_amount"]= $result["guarantor_amount"];
$restgutor=$model->getmasterByid($result["guarator_level"]);
$resp["subgroup_desc"] =$restgutor["subgroup_desc"];
$resp["guarator_level"]= $result["guarator_level"];
$resp["byPass"]= $result["byPass"];
$resp["checked_1"]        = $result["loan_status"] == "T" ? "checked" : "";
$resp["checked_2"]        = $result["loan_status"] == "B" ? "checked" : "";

// Set var
$data = array(
    "menu_item" => 3,
);

// Assign data to view
$template->assign_vars($resp);
$template->assign_vars($data);
$template->pparse('body');
