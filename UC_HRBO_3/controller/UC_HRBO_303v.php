<?php
ob_start();
session_start();
require '../../session.php';
require_once '../../common.php';
require_once '../../lib/template.php';
require_once '../../config/database.php';
require_once '../../UC_HRBO_3/model/HRBO_303_Model.php';
include '../../utils/base_function.php';

$template = new template();
$template->set_filenames(array(
    'body' => '../view/UC_HRBO_303v.html')
);

//get database connection
$database = new Database();
$db       = $database->getConnection();
$model    = new HRBO_303_Model($db);

$model->id = isset($_GET['id']) ? $_GET['id'] : $http_response->print_error(400);

$model->OneAll();

$response_data = array();
$response_data["type_of_patient"]               = $model->type_of_patient;
$response_data["type_of_disease"]               = $model->type_of_disease;
$response_data["hospital_type_name"]            = $model->hospital_type_name;
$response_data["days_of_treatment"]             = $model->days_of_treatment;
$response_data["room_and_food_fee"]             = number_format($model->room_and_food_fee, 2, '.', ',');
$response_data["docter_fee_amount"]             = number_format($model->docter_fee_amount,2, '.',',');
$response_data["priviledge_month"]              = number_format($model->priviledge_month, 2, '.', ',');
$response_data["type_of_hospital"]              = $model->type_of_hospital;
$response_data["department_name"]               = $model->department_name;
$response_data["withdraw_amount"]               = number_format($model->withdraw_amount, 2, '.', ',');
$response_data["approver_name"]                 = $model->approver_name;
$response_data["equipment_fee"]                 = number_format($model->equipment_fee, 2, '.', ',');
$response_data["invoice_name"]                  = $model->invoice_name;
$response_data["receipt_date"]                  = date("Y-m-d", strtotime($model->receipt_date));
$response_data["CreatedDate"]                   = date("Y-m-d H:i", strtotime($model->CreatedDate));
$response_data["medical_fee"]                   = number_format($model->medical_fee, 2, '.', ',');
$response_data["benefitcode"]                   = $model->benefitcode;
$response_data["receipt_no"]                    = $model->receipt_no;
$response_data["reqstatus"]                     = $model->reqstatus;
$response_data["other_fee"]                     = number_format($model->other_fee, 2, '.', ',');
$response_data["take_moro"]                     = reformatStatuStake_moro($model->take_moro);
$response_data["EmpName"]                       = $model->EmpName;
$response_data["req_no"]                        = $model->req_no;
$response_data["housId"]                        = $model->housId;
$response_data["EmpDep"]                        = $model->EmpDep;
$response_data["EmpPos"]                        = $model->EmpPos;
$response_data["DepPar"]                        = $model->DepPar;
$response_data["EmpId"]                         = $model->EmpId;
$response_data["Id"]                            = $model->Id;

if (($model->reqstatus == "1")) {
    $response_data["Status"] = "";
} else {
    $response_data["Status"] = "<button id=\"approve\" type=\"button\" data-toggle=\"modal\" data-target=\"#md-approve\" class=\"btn btn-space btn-primary\">
    <i class=\"icon icon-left mdi mdi-check-all\"></i> ยืนยันผลการตรวจ</button>";
}

$template->assign_vars($response_data);

$stmt = $model->All();
$num = $stmt->rowCount();
if ($num > 0) {
    $response_data = array();
    $count = 1;

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $originalDate = $row["CreatedDate"];
        $newDate = date("Y-m-d H:i", strtotime($originalDate));
        $row["req_datetime"] =  $newDate;
        $row["req_status"]   = reformatStatus($row["Status"]);
        $row["no"]           = $count;

        $template->assign_block_vars('request', $row);
        unset($rows);
        $count++;
    }
}

$stmt_noti = $model->getallnoti();
$num_noti = $stmt_noti->rowCount();
if ($num_noti > 0) {
    $count = 1;

    while ($row = $stmt_noti->fetch(PDO::FETCH_ASSOC)) {
        $originalDate = $row["created_date"];
        $newDate = date("Y-m-d H:i", strtotime($originalDate));
        $row["req_datetime"] = $newDate;
        $row["no"]           = $count;

        $TEXT = str_replace("\n", "<br>\n", $row["description"]); 

        $row["description"]  = $TEXT;
        
        $template->assign_block_vars('request2', $row);
        unset($rows);
        $count++;
    }
}
$data = array(
    "menu_item"=>3,
);

$template->assign_vars($data);
$template->pparse('body');
