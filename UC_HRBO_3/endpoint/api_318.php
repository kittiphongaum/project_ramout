<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../../config/database.php';
include_once '../model/HRBO_318_Model.php';
include_once '../model/HRBO_3_Model.php';
require_once '../../common.php';

//Make sure that it is a POST request.
if (strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') != 0) {
    die('Request method must be POST!');
}

// get database connection
$database       = new Database();
$db             = $database->getConnection();
$model          = new HRBO_318_Model($db);
$model_log_noti = new HRBO_3_Model($db);

$date_now = Date('Y-m-d H:i:s');
if ($_POST["status"] == "A") {

    $model->transaction_id = $_POST['transaction_id'];
    $model->status         = $_POST['status'];
    $check_update          = $model->update_status();

    $key               = get_key();
    $file_arr          = array();
    $file->fileName    = "loan_contract.pdf";
    $file->fileUrlPath = "https://10.22.51.148:8443/api/benefits/loancontract/report/empid/05100469/key/" . $key;
    array_push($file_arr, $file);
    $resp_email = send_email($host_email_service, $_POST["emp_id"], "ขอเงินกู้ส่งเสริมสวัสดิภาพพนักงาน", "เอกสารขอเงินกู้ส่งเสริมสวัสดิภาพพนักงาน", $file_arr);

} elseif ($_POST["status"] == "D") {

    $model->transaction_id = $_POST['transaction_id'];
    $model->status         = $_POST['status'];
    $check_update          = $model->update_status();

    if ($_POST['message']) {
        $resp_noti = send_notification($host_notification_service, $_POST["emp_id"], "ขอเงินกู้ส่งเสริมสวัสดิภาพพนักงาน", $_POST["message"]);
    }

} elseif ($_POST["status"] == "C") {

    $model->transaction_id = $_POST['transaction_id'];
    $model->status         = $_POST['status'];
    $check_update          = $model->update_status();

} elseif ($_POST["status"] == "R") {

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

function send_email($host_email_service, $emp_id, $subject, $body, $file)
{
    $data["toEmpCode"]       = [$emp_id];
    $data["subject"]         = $subject;
    $data["body"]            = $body;
    $data["filesAttachment"] = $file;

    $myJSON = json_encode($data);

    $curl = curl_init();

    curl_setopt($curl, CURLOPT_POST, 1);

    curl_setopt($curl, CURLOPT_POSTFIELDS, $myJSON);

    curl_setopt($curl, CURLOPT_URL, $host_email_service . "/gsb-mobile/internal-email.service");

    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
    ));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

    $result = curl_exec($curl);

    return $result;
}

function get_key()
{

    $curl = curl_init();

    curl_setopt($curl, CURLOPT_URL, "http://10.22.50.146/fundloan.json");

    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
    ));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

    $result = curl_exec($curl);
    if (count(json_decode($result)->data->groupYearFundloanDisplay)) {
        return json_decode($result)->data->groupYearFundloanDisplay[0]->values[0]->keyLoanContractReport;
    } else {
        return null;
    }
}
