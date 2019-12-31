<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../../config/database.php';
include_once '../model/HRBO_314_Model.php';
include_once '../model/HRBO_3_Model.php';
require_once '../../common.php';

//Make sure that it is a POST request.
if (strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') != 0) {
    die('Request method must be POST!');
}

// get database connection
$database       = new Database();
$db             = $database->getConnection();
$model          = new HRBO_314_Model($db);
$model_log_noti = new HRBO_3_Model($db);

$date_now = Date('Y-m-d H:i:s');

if ($_POST["status"] == "A") {

    $model->transaction_id = $_POST['transaction_id'];
    $model->status         = $_POST['status'];
    $check_update          = $model->update_status();

} elseif ($_POST["status"] == "D") {

    $model->transaction_id = $_POST['transaction_id'];
    $model->status         = $_POST['status'];
    $check_update          = $model->update_status();

    if ($_POST['message']) {
        $resp_noti = send_notification($host_notification_service, $_POST["emp_id"], "เบิกทุนการศึกษา", $_POST["message"]);

        if (json_decode($resp_noti)->code != "404") {
            $model_log_noti->empid       = $_POST["emp_id"];
            $model_log_noti->reqno       = $_POST["doc_no"];
            $model_log_noti->benefitcode = "scholarship";
            $model_log_noti->description = $_POST["message"];
            $model_log_noti->created_date = $date_now;
            $model_log_noti->insert();
        }else{
            $model_log_noti->empid       = $_POST["emp_id"];
            $model_log_noti->reqno       = $_POST["doc_no"];
            $model_log_noti->benefitcode = "scholarship";
            $model_log_noti->description = "ไม่สามารถทำการส่ง Notification ได้";
            $model_log_noti->created_date = $date_now;
            $model_log_noti->insert();
        }

    }

} elseif ($_POST["status"] == "R") {

    $model->transaction_id = $_POST['transaction_id'];
    $model->status         = $_POST['status'];
    $check_update          = $model->update_status();

    if ($_POST['money_back_amt']) {
        $model->money_back_amt  = $_POST['money_back_amt'];
        $model->money_back_msg  = $_POST['message'];
        $model->meney_back_date = $date_now;
        $status_update_money    = $model->update_money_back();
    }

    if ($_POST['message']) {
        $resp_noti = send_notification($host_notification_service, $_POST["emp_id"], "เบิกทุนการศึกษา", $_POST["message"]);

        if (json_decode($resp_noti)->code != "404") {
            $model_log_noti->empid       = $_POST["emp_id"];
            $model_log_noti->reqno       = $_POST["doc_no"];
            $model_log_noti->benefitcode = "scholarship";
            $model_log_noti->description = $_POST["message"];
            $model_log_noti->created_date = $date_now;
            $model_log_noti->insert();
        }else{
            $model_log_noti->empid       = $_POST["emp_id"];
            $model_log_noti->reqno       = $_POST["doc_no"];
            $model_log_noti->benefitcode = "scholarship";
            $model_log_noti->description = "ไม่สามารถทำการส่ง Notification ได้";
            $model_log_noti->created_date = $date_now;
            $model_log_noti->insert();
        }
    }
}elseif ($_POST["status"] == "X") {

    $model->transaction_id = $_POST['transaction_id'];
    $model->status         = $_POST['status'];
    $check_update          = $model->update_status();

    if ($_POST['money_back_amt']) {
        $model->money_back_amt  = $_POST['money_back_amt'];
        $model->money_back_msg  = $_POST['message'];
        $model->meney_back_date = $date_now;
        $status_update_money    = $model->update_money_back();
    }

    if ($_POST['message']) {
        $resp_noti = send_notification($host_notification_service, $_POST["emp_id"], "เบิกทุนการศึกษา", $_POST["message"]);

        if (json_decode($resp_noti)->code != "404") {
            $model_log_noti->empid       = $_POST["emp_id"];
            $model_log_noti->reqno       = $_POST["doc_no"];
            $model_log_noti->benefitcode = "scholarship";
            $model_log_noti->description = $_POST["message"];
            $model_log_noti->created_date = $date_now;
            $model_log_noti->insert();
        }else{
            $model_log_noti->empid       = $_POST["emp_id"];
            $model_log_noti->reqno       = $_POST["doc_no"];
            $model_log_noti->benefitcode = "scholarship";
            $model_log_noti->description = "ไม่สามารถทำการส่ง Notification ได้";
            $model_log_noti->created_date = $date_now;
            $model_log_noti->insert();
        }
    }
}elseif ($_POST["status"] == "Y") {

    $model->transaction_id = $_POST['transaction_id'];
    $model->status         = $_POST['status'];
    $check_update          = $model->update_status();

}

if ($check_update) {
    $response["status"] = "success";
} else {
    $response["status"] = "fail";
}

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
