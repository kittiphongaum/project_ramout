<?php
header('Content-Type: application/json');
require_once '../../config/database.php';
include_once '../model/HRBO_320_Model.php';

$database = new Database();
$db       = $database->getConnection();

$model = new HRBO_320_Model($db);
   
    if ($_POST["key_code1"] != "" || $_POST["key_code1"] != null) {
        $_POST["key_code"] =$_POST["key_code1"];
    }
    $model->key_code      = $_POST["key_code"];
    $model->key_name      = $_POST["key_name"];
    $model->ord           = $_POST["ord"];
    $model->status        = $_POST["statusswitch"];
    $model->title         = $_POST["title"];
    $model->created_date  = Date('Y-m-d H:i:s');
    $resp["status"]=404;
    $model->searchLoan($_POST["key_code"]);
    if ($_POST["key"] !="" || $_POST["key"] != null) {
        
        if ($model->key_code2 != null) {

        $model->updated_date  = Date('Y-m-d H:i:s');
            $status = $model->updateLoan();
            if ($status) {
                $resp["status"] = 200;

            } else {
                $resp["status"] = 201;
            }
        }
    }else{
        if ($_POST["key_code"] != null) {
            $status = $model->createLoan();
            if ($status) {
                $resp["status"] = 200;
            } else {
                $resp["status"] = 201;
            }
        }
    }
echo json_encode($resp, JSON_UNESCAPED_UNICODE);