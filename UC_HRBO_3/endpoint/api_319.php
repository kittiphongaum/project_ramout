<?php

// use ___PHPSTORM_HELPERS\object;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../../config/database.php';
include_once '../model/HRBO_319_Model.php';
include_once '../model/HRBO_3_Model.php';
require_once '../../common.php';

//Make sure that it is a POST request.
if (strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') != 0) {
    die('Request method must be POST!');
}

// get database connection
$database       = new Database();
$db             = $database->getConnection();
$model          = new HRBO_319_Model($db);
$model_log_noti = new HRBO_3_Model($db);

$date_now = Date('Y-m-d H:i:s');
if ($_POST["status"] == "A") {

    $model->EmpId  = $_POST["emp_id"];
    $model->doc_no = $_POST["doc_no"];
    $res_data      = $model->memberty();
    $stmt_byid      = $model->beneficiary_add();
    // $data                  = $res_data->fetch(PDO::FETCH_ASSOC);
    // $data1                  = array();
    $data_by_id = $stmt_byid->fetch(PDO::FETCH_ASSOC);
    $num            = $res_data->rowCount();
    $check_update   = 0;
    
    $beneficiaryfinish_arr = array();

    while ($res = $res_data->fetch(PDO::FETCH_ASSOC)) {
        $beneficiaryAdd = array(
            "addressnumbersoitrok"   => setdata($res["addressnumbersoitrok"]),
            "alivedeceased"          => setdata($res["alivedeceased"]),
            "anrlt"                  => setdata($res["anrlt"]),
            "birthplace"             => setdata($res["birthplace"]),
            "childnumber"            => setdata($res["childnumber"]),
            "city"                   => setdata($res["city"]),
            "communicationnumber01"  => setdata($res["communicationnumber01"]),
            "communicationnumber02"  => setdata($res["communicationnumber02"]),
            "communicationnumber03"  => setdata($res["communicationnumber03"]),
            "communicationnumber04"  => setdata($res["communicationnumber04"]),
            "communicationtype01"    => setdata($res["communicationtype01"]),
            "communicationtype02"    => setdata($res["communicationtype02"]),
            "communicationtype03"    => setdata($res["communicationtype03"]),
            "communicationtype04"    => setdata($res["communicationtype04"]),
            "country"                => setdata($res["country"]),
            "countryofbirth"         => setdata($res["countryofbirth"]),
            "dateofbirth"            => setdata($res["dateofbirth"]),
            "delimitDate"            => setdata($res["delimitDate"]),
            "district"               => setdata($res["district"]),
            "employeeno"             => setdata($res["employeeno"]),
            "employeroffamilymember" => setdata($res["employeroffamilymember"]),
            "firstname"              => setdata($res["firstname"]),
            "formofaddress"          => setdata($res["formofaddress"]),
            "gender"                 => setdata($res["gender"]),
            "groupid"                => setdata($res["groupid"]),
            "jobtitle"               => setdata($res["jobtitle"]),
            "lastname"               => setdata($res["lastname"]),
            "lockindic"              => setdata($res["lockindic"]),
            "membertype"             => setdata($res["membertype"]),
            "nameofsubtype"          => setdata($res["nameofsubtype"]),
            "nationality"            => setdata($res["nationality"]),
            "objectid"               => setdata($res["objectid"]),
            "phonenumber"            => setdata($res["phonenumber"]),
            "postalcode"             => setdata($res["postalcode"]),
            "recordnr"               => setdata($res["recordnr"]),
            "secondnationality"      => setdata($res["secondnationality"]),
            "streetthambol"          => setdata($res["streetthambol"]),
            "subtype"                => setdata($res["subtype"]),
            "thirdnationality"       => setdata($res["thirdnationality"]),
            "title"                  => setdata($res["title"]),
            "titleDesc"              => setdata($res["titleDesc"]),
            "validbegin"             => setdata($res["validbegin"]),
            "validend"               => setdata($res["validend"]),
            "zzchild_desc"           => setdata($res["zzchild_desc"]),
            "zzchild_type"           => setdata($res["zzchild_type"]),
            "zzid"                   => setdata($res["zzid"]),
        );
        array_push($beneficiaryfinish_arr, $beneficiaryAdd);
    }
        $data->beneficiaryAdd = $beneficiaryfinish_arr;
        // $beneficiaryfinish_arr = array();
        $data->beneficiaryfinish = json_decode($data_by_id["json_finish"]);

        $date_resurt = add_sevice_api($data, $host);
        if (json_decode($date_resurt)->status_code == 200) {
            $model->doc_no = $_POST["doc_no"];
            $model->status         = $_POST['status'];
            $check_update =$model->update_status_DB2();

                $resp_noti = send_notification($host_notification_service, $_POST["emp_id"], "พิจารณาข้อมูลผู้รับผลประโยชน์ตามสวัสดิการ","อนุมัติเรียบร้อย");
                
                if (json_decode($resp_noti)->code != "404") {
                    $model_log_noti->empid       = $_POST["emp_id"];
                    $model_log_noti->reqno       = $_POST["doc_no"];
                    $model_log_noti->benefitcode = "beneficiary";
                    $model_log_noti->description = $_POST["message"];
                    $model_log_noti->createddate = $date_now;
                }else{
                    $model_log_noti->empid       = $_POST["emp_id"];
                    $model_log_noti->reqno       = $_POST["doc_no"];
                    $model_log_noti->benefitcode = "beneficiary";
                    $model_log_noti->description = "ไม่สามารถทำการส่ง Notification ได้";
                    $model_log_noti->createddate = $date_now;
                }
                $model_log_noti->insert();
                    
        
        }
}
if ($check_update === $num) {
    $model->transaction_id = $_POST['transaction_id'];
    $model->status         = $_POST['status'];
    $check_                = $model->update_status();
    $response["status"]    = "success";
   
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
function setdata($data)
{
    if ($data == null) {
        $data = "";
    }
    return $data;
}
function add_sevice_api($dataarray, $host)
{

    $myJSON = json_encode($dataarray);

    // Setup cURL
    $curl = curl_init();

    curl_setopt($curl, CURLOPT_POST, 1);

    curl_setopt($curl, CURLOPT_POSTFIELDS, $myJSON);

    // curl_setopt($curl, CURLOPT_URL, "http://localhost:8088/benefits/beneficiary/add");
    curl_setopt($curl, CURLOPT_URL, $host . '/gsb-service-api/m4-service/internal/benefits/beneficiary/add');

    // curl_setopt($curl, CURLOPT_URL, "http://10.22.50.146/gsb-service-api/m4-service/benefits/beneficiary/add");
    
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
    ));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    return curl_exec($curl);
}
