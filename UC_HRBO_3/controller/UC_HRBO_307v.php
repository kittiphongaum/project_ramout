<?php
ob_start();
session_start();
require '../../session.php';
require_once '../../common.php';
require_once '../../lib/template.php';
require_once '../../config/database.php';
require_once '../../UC_HRBO_3/model/HRBO_307_Model.php';
include '../../utils/base_function.php';

$template = new template();
$template->set_filenames(array(
    'body' => '../view/UC_HRBO_307v.html')
);
//get database connection
$database = new Database();
$db       = $database->getConnection();
$model    = new HRBO_307_Model($db);

$model->id = isset($_GET['id']) ? $_GET['id'] : $http_response->print_error(400);

$model->OneAll();

$response_data = array();
$response_data["EmpName"] = $model->EmpName;
$response_data["EmpId"] = $model->EmpId;
$response_data["EmpDep"] = $model->EmpDep;
$response_data["CreatedDate"] = $model->CreatedDate;
$response_data["EmpPos"] = $model->EmpPos;
$response_data["DepPar"] = $model->DepPar;
$response_data["Id"] = $model->Id;
$response_data["req_no"] = $model->req_no;
$response_data["receipt_no"] = $model->ReceiptNo;
$response_data["receipt_date"] = $model->receipt_date;
$response_data["personal_insurance"] = reformatStatuStake_moro($model->personal_insurance);
$response_data["date_of_treat"] = $model->date_of_treat;
$response_data["tel"] = $model->tel;
$response_data["disease"] = $model->disease;
$response_data["type_of_hospital"] = $model->type_of_hospital;
$response_data["name_of_hospital"] = $model->name_of_hospital;
$response_data["rights_user"] = $model->rights_user;
$response_data["addess"] = $model ->addess;

$template->assign_vars($response_data);

$stmt = $model->All();
$model->EmpId;
$num = $stmt->rowCount();
if ($num > 0) {
    $response_data = array();
    $count = 1;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $originalDate = $row["CreatedDate"];
        $newDate = date("Y-m-d H:i", strtotime($originalDate));
        $row["req_datetime"] =  $newDate;
        $row["no"]           = $count;

        $template->assign_block_vars('request', $row);
        unset($rows);
        $count++;
    }
}

$data = array(
    "menu_item"=>3,
);

$template->assign_vars($data);
$template->pparse('body');
