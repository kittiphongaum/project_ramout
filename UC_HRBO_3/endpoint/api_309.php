<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../../config/database.php';
include_once '../model/HRBO_309_Model.php';
include_once '../../models/masterDataModel.php';
require_once '../../common.php';

//Make sure that it is a POST request.
if (strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') != 0) {
    die('Request method must be POST!');
}

// get database connection
$database          = new Database();
$db                = $database->getConnection();
$model             = new HRBO_309_Model($db);
$model_master_data = new masterDataModel($db);

$model->transaction_id = $_POST['id'];
$result                = $model->get();
$model_master_data->key_code = "medical_from";
$medical_from                = $model_master_data->get()["key_name"];
$model_master_data->key_code = "medical_from_body_region";
$medical_from_body_region    = $model_master_data->get()["key_name"];
$model_master_data->key_code = "medical_from_body_center";
$medical_from_body_center    = $model_master_data->get()["key_name"];

if ($result["personal_group"] === "C") {
    $result_curl = $_POST["status"] !== "R" ? send_notification($host_notification_service, $result["emp_id"], $medical_from, $medical_from_body_region) : send_notification($host_notification_service, $result["emp_id"], $medical_from, $_POST["remark_denied"]);
    if ($result_curl) {
        $model->status = $_POST["status"];
        $check_update  = $model->update_status();
    }
} else {
    $result_curl = $_POST["status"] !== "R" ? send_notification($host_notification_service, $result["emp_id"], $medical_from, $medical_from_body_center) : send_notification($host_notification_service, $result["emp_id"], $medical_from, $_POST["remark_denied"]);
    if ($result_curl) {
        $model->status = $_POST["status"];
        $check_update  = $model->update_status();
    }
}

if ($check_update && $result_curl) {
    $response["status"] = "success";
} elseif (!$check_update && $result_curl) {
    $response["status"] = "noti_fail";
} else {
    $response["status"] = "fail";
}
$response["status_noti"] = $result_curl;
echo json_encode($response);

function send_notification($host_notification_service, $emp_id, $subject, $body)
{
    $data->group = "gsb-mobile";
    $data->uid   = $emp_id;
    $data->title = $subject;
    $data->body  = $body;

    $myJSON = json_encode($data);

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

    return curl_exec($curl);
}