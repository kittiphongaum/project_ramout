<?php
ob_start();
session_start();
require '../../session.php';
require_once '../../common.php';
require_once '../../lib/template.php';
require_once '../../config/database.php';
require_once '../../UC_HRBO_3/model/HRBO_313_Model.php';
include '../../utils/base_function.php';

$template = new template();
$template->set_filenames(array(
    'body' => '../view/UC_HRBO_313v.html')
);

//get database connection
$database = new Database();
$db       = $database->getConnection();
$model    = new HRBO_313_Model($db);

$model->transaction_id = isset($_GET['id']) ? $_GET['id'] : $http_response->print_error(400);

$result               = $model->get();
$result["req_date"]   = date("d/m/Y H:i:s", strtotime($result["req_date"]));
$result["status_str"] = status($result["status"]);
$result["age"]        = date_diff(date_create($result["birth_date"]), date_create('now'))->y;

$template->assign_vars($result);

$model->status           = "S','I";
$model->emp_id           = $result["emp_id"];
$stmt_maternity_history  = $model->getAll();
$count_maternity_history = 1;
while ($row = $stmt_maternity_history->fetch(PDO::FETCH_ASSOC)) {
    $row["no"]                    = $count_maternity_history++;
    $row["req_date"]              = date("Y-m-d H:i", strtotime($row["req_date"]));
    $row["child_birth_limit_amt"] = $row["child_birth_limit_amt"] ? $row["child_birth_limit_amt"] . "฿" : "-";
    $row["status"]                = status($row["status"]);
    $template->assign_block_vars('maternity_history', $row);
}

$model->doc_no = $result["doc_no"];
$stmt_noti     = $model->getallnoti();
$num_noti      = $stmt_noti->rowCount();
if ($num_noti > 0) {
    $count = 1;

    while ($row = $stmt_noti->fetch(PDO::FETCH_ASSOC)) {
        $row["created_date"] = date("Y-m-d H:i", strtotime($row["created_date"]));
        $row["no"]           = $count;

        $TEXT = str_replace("\n", "<br>\n", $row["description"]);

        $row["description"] = $TEXT;

    $template->assign_block_vars('notification_history', $row);
        unset($rows);
        $count++;
    }
}

if ($result["status"] === "S") {
    $btn_status = ' <button id="approve" type="button" data-toggle="modal" data-target="#md-approve"
                        class="btn btn-space btn-primary">
                        <i class="icon icon-left mdi mdi-bell"></i> ตรวจสอบแล้ว
                    </button>';
}
if ($result["status"] === "S" || $result["status"] === "R") {
    $btn_status .= '<button id="approve" type="button" data-toggle="modal" data-target="#md-approve-noti"
                        class="btn btn-space btn-primary">
                        <i class="icon icon-left mdi mdi-bell"></i> เรียกเงินคืน
                    </button>';
}


$data = array(
    "menu_item"  => 3,
    "btn_status" => $btn_status,
);

$template->assign_vars($data);
$template->pparse('body');

function status($status)
{
    switch ($status) {
        case "S":
            $status = "รอตรวจสอบ";
            break;
        case "A":
            $status = "ตรวจสอบแล้ว";
            break;
        case "R":
            $status = "เรียกเงินคืน";
            break;
        default:
            $status = "-";
            break;
    }
    return $status;
}
