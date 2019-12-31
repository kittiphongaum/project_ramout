<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../../common.php';
require_once '../../config/database.php';
require_once '../../utils/base_function.php';
include_once '../model/HRBO_3_Model.php';
include_once '../../models/masterDataModel.php';

//get database connection
$database = new Database();
$db       = $database->getConnection();

$model             = new HRBO_3_Model($db);
$model_master_data = new masterDataModel($db);

// get posted data
$input    = file_get_contents('php://input');
$req_data = json_decode($input);

$response = array();

if (!empty($req_data->emp_id) &&
    !empty($req_data->benefit) &&
    !empty($req_data->req_no) &&
    !empty($req_data->description)
) {

    $model_master_data->key_code = $req_data->benefit;

    $master_data = $model_master_data->get();

    $fileArray   = array();
    $data->group = "gsb-mobile";
    $data->uid   = $req_data->emp_id;
    $data->title = $master_data["key_name"];
    $data->body  = $req_data->description;

    $model->empid       = $req_data->emp_id;
    $model->reqno       = $req_data->req_no;
    $model->benefitcode = $req_data->benefit;
    $model->description = $req_data->description;
    $model->createddate = Date('Y-m-d H:i:s');
    $myJSON             = json_encode($data);

    // Setup cURL
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $myJSON);
    curl_setopt($curl, CURLOPT_URL, $host_notification_service . "/us-push-notification/push.service");
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
    ));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

    $result = curl_exec($curl);

    if (json_decode($result)->code == "404") {
        $model->description = "ไม่สามารถทำการส่ง Notification ได้";
    }
    $response["noti"] = json_decode($result);
    $response["data"] = $data;
    $model->insert();
} else {
    $response["result"] = false;
}

printJson($response);
