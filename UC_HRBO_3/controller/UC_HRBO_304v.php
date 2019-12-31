<?php
ob_start();
session_start();
require '../../session.php';
require_once '../../common.php';
require_once '../../lib/template.php';
require_once '../../config/database.php';
require_once '../../UC_HRBO_3/model/HRBO_304_Model.php';
include '../../utils/base_function.php';

$template = new template();
$template->set_filenames(array(
    'body' => '../view/UC_HRBO_304v.html')
);

//get database connection
$database = new Database();
$db       = $database->getConnection();
$model    = new HRBO_304_Model($db);

$model->id = isset($_GET['id']) ? $_GET['id'] : $http_response->print_error(400);

$model->OneAll();

$response_data = array();
$response_data["scholarship_limit"]      = number_format($model->scholarship_limit, 2, '.', ',');
$response_data["priviledge_month"]       = $model->priviledge_month;
$response_data["withdraw_amount"]        = number_format($model->withdraw_amount, 2, '.', ',');
$response_data["approvers_name"]         = $model->approvers_name;
$response_data["receipt_date"]           = $model->receipt_date;
$response_data["receive_time"]           = $model->receive_time;
$response_data["receive_year"]           = $model->receive_year;
$response_data["CreatedDate"]            = date("Y-m-d", strtotime($model->CreatedDate));
$response_data["benefitcode"]            = $model->benefitcode;
$response_data["receipt_no"]             = $model->receipt_no;
$response_data["EmpName"]                = $model->EmpName;
$response_data["EmpDep"]                 = $model->EmpDep;
$response_data["EmpPos"]                 = $model->EmpPos;
$response_data["DepPar"]                 = $model->DepPar;
$response_data["req_no"]                 = $model->req_no;
$response_data["housId"]                 = $model->housId;
$response_data["EmpId"]                  = $model->EmpId;
$response_data["Id"]                     = $model->Id;
$i=1;

foreach ($model->json_array as $key=>$value) {
    $price = number_format($value, 2, '.', ',');
    $t .=' <tr>
        <td colspan="3">&nbsp;</td>
        <td class="summary">รายการเบิกที่ ' . $i . '</td>
        <td class="amount">' . $price . '฿</td>
        </tr>';
    $i++;
}

$response_data["req_info"] = $t;
if (($model->reqstatus == "1") || ($model->reqstatus == "3") ) {
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
        $row["req_datetime"] = $newDate;
        $row["req_status"]   = reformatStatus($row["Status"]);
        $row["no"]           = $count;
        $row["priviledge_month"] = number_format($row["priviledge_month"], 2, '.', ',');

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
