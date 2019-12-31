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
    'body' => '../view/UC_HRBO_313.html')
);

//get database connection
$database = new Database();
$db       = $database->getConnection();
$model    = new HRBO_313_Model($db);

$model->doc_no          = isNotEmpty($_POST["doc_no"]);
$model->sdate           = strToDateSdate(isNotEmpty($_POST["sdate"]));
$model->edate           = strToDateEdate(isNotEmpty($_POST["edate"]));
$model->emp_org_id      = isNotEmpty($_POST["dept_id"]);
$model->child_full_name = isNotEmpty($_POST["child_name"]);
$model->emp_name        = isNotEmpty($_POST["emp_name"]);
$model->status          = implode("','", $_POST["status"]);

$template->assign_var("doc_no", isNotEmpty($_POST["doc_no"]));
$template->assign_var("dept_id", isNotEmpty($_POST["dept_id"]));
$template->assign_var("child_name", isNotEmpty($_POST["child_name"]));
$template->assign_var("emp_name", isNotEmpty($_POST["emp_name"]));
$template->assign_var("sdate", isNotEmpty($_POST["sdate"]));
$template->assign_var("edate", isNotEmpty($_POST["edate"]));
$template->assign_var("dept_name", isNotEmpty($_POST["dept_name"]));

foreach ($_POST["status"] as $item) {
    switch ($item) {
        case "W":
            $template->assign_var("checked_0", "checked=''");
            break;
        case "I":
            $template->assign_var("checked_1", "checked=''");
            break;
        default:
            $template->assign_var("checked_2", "checked=''");
            break;
    }
}
$stmt = $model->getAll();

$num = $stmt->rowCount();

if ($num > 0) {
    $response_data = array();
    $count         = 1;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $originalDate                 = $row["req_date"];
        $newDate                      = date("Y-m-d H:i:s", strtotime($originalDate));
        $row["req_datetime"]          = $newDate;
        $row["req_status"]            = status($row["status"]);
        $row["no"]                    = $count;
        $row["priviledge_month"]      = number_format($row["priviledge_month"], 2, '.', ',');
        $row["child_birth_limit_amt"] = $row["child_birth_limit_amt"] ? $row["child_birth_limit_amt"] . "฿" : "";
        $template->assign_block_vars('request', $row);
        unset($rows);
        $count++;
    }
}

$rpt_url = $host_jasper_service . "/jasperserver/flow.html?j_username=" . $host_jasper_username . "&j_password=" . $host_jasper_password . "&_flowId=viewReportFlow&reportUnit=/reports/aomsinbo/UC_HRBO_313&decorate=no";
$rpt_url .= "&output=pdf";
$rpt_url .= "&doc_no=" . isNotEmpty($_POST["doc_no"]);
$rpt_url .= "&sdate=" . isNotEmpty($_POST["sdate"]);
$rpt_url .= "&edate=" . isNotEmpty($_POST["edate"]);
$rpt_url .= "&org_id=" . isNotEmpty($_POST["dept_id"]);
$rpt_url .= "&emp_name=" . isNotEmpty($_POST["emp_name"]);
$rpt_url .= "&child_name=" . isNotEmpty($_POST["child_name"]);
$rpt_url .= "&status=" . implode("*,*", $_POST["status"]);

$template->assign_var("rpt_url", $rpt_url);
$template->assign_var("xlsx_url", str_replace("output=pdf", "output=xlsxNoPag", $rpt_url));

$data = array(
    "menu_item" => 3,
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
