<?php
header('Content-Type: application/json');
require_once '../../config/database.php';
include_once '../model/HRBO_320_Model.php';

$database = new Database();
$db       = $database->getConnection();

$model = new HRBO_320_Model($db);

if ($_POST["status_action"] === "I") {
    $sp_category = explode("|", $_POST["category"]);
    $model->item_desc = $sp_category[3];

    if ($model->check_duplicate()) {
        $resp["status"] = 202;
    } else {
        $model->id               = $sp_category[0];
        $model->category         = $sp_category[1];
        $model->actvie_flag      = $sp_category[2];
        $model->item_value       = $sp_category[4];
        $model->item_value2      = $sp_category[5];
        $model->item_amount_bat  = $_POST["item_amount_bat"];
        $model->item_amount_mamy = $_POST["item_amount_mamy"];
        $model->period_amount       = $_POST["period_amount"];
        $model->guarantor_amount     = $_POST["guarantor_amount"];
        $model->guarator_level       = $_POST["guarator_level"];
        $model->byPass               = $_POST["statusswitch"];
        $model->loan_status       = $_POST["status"];

        $status = $model->create();

        if ($status) {
            $resp["status"] = 200;
        } else {
            $resp["status"] = 201;
        }
    }
} else if ($_POST["status_action"] === "U") {
    $model->category = $_POST["old_category"];

    $model->item_amount_bat  = $_POST["item_amount_bat"];
    $model->item_amount_mamy = $_POST["item_amount_mamy"];
    $model->period_amount       = $_POST["period_amount"];
    $model->guarantor_amount     = $_POST["guarantor_amount"];
    $model->guarator_level       = $_POST["guarator_level"];
    $model->byPass               = $_POST["statusswitch"];
    $model->loan_status       = $_POST["status"];
    
    $status = $model->update();

    if ($status) {
        $resp["status"] = 200;
    } else {
        $resp["status"] = 201;
    }

} else {
    $model->category = $_POST["category"];
    $status          = $model->delete();

    if ($status) {
        $resp["status"] = 200;
    } else {
        $resp["status"] = 201;
    }
    
}

echo json_encode($resp);
