<?php
ob_start();
session_start();
require '../../session.php';
require_once '../../common.php';
require_once '../../lib/template.php';
require_once '../../config/database.php';
require_once '../../UC_HRBO_3/model/HRBO_319_Model.php';
include '../../utils/base_function.php';

$template = new template();
$template->set_filenames(array(
    'body' => '../view/UC_HRBO_319M_02.html')
);

//get database connection
$database               = new Database();
$db                     = $database->getConnection();
$model                  = new HRBO_319_Model($db);
$EmpDep                 = isNotEmpty($_POST["dept_name"]);
$model->emp_org_id      = isNotEmpty($_POST["dept_id"]);
$model->req_no          = isNotEmpty($_POST["doc_no"]);
$model->EmpName         = isNotEmpty($_POST["emp_name"]);
$model->Benefit         = isNotEmpty($_POST["Benefit"]);
$model->sdate           = strToDateSdate(isNotEmpty($_POST["sdate"]));
$model->edate           = strToDateEdate(isNotEmpty($_POST["edate"]));
$model->req_status_list = implode("','", $_POST["req_status"]);
$Benefitgroup           = $model->getbeneficiary_group();
$num                    = $Benefitgroup->rowCount();
$Benefit                = "";
if ($num > 0) {
    $count = 1;
    $Benefit .= '<option value="">ทั้งหมด</option>';
    while ($rows = $Benefitgroup->fetch(PDO::FETCH_ASSOC)) {
        $Benefit .= '<option value="' . $rows["member_type"] . '">' . $rows["beneficiary_group"] . '</option>';
        $count++;
    }
}
if (isset($_POST["search"])) {
    $status = $_POST["req_status"];
} else {
    $status = ["I", "D"];
}
$model->req_status_list = implode("','", $status);
$template->assign_var("OptionBenefit", $Benefit);
$model->membertype = "9006','9007','9008";

$stmt = $model->search();
foreach ($status as $item) {
    switch ($item) {
        case 'X':
            $template->assign_var("checked_3", "checked=''");
            break;
        case 'A':
            $template->assign_var("checked_2", "checked=''");
            break;
        case 'D':
            $template->assign_var("checked_1", "checked=''");
            break;
        default:
            $template->assign_var("checked_0", "checked=''");
            break;
    }
}

$rpt_url = $host_jasper_service . "/jasperserver/flow.html?j_username=" . $host_jasper_username . "&j_password=" . $host_jasper_password . "&_flowId=viewReportFlow&reportUnit=/reports/aomsinbo/UC_HRBO_319M_02&decorate=no";
$rpt_url .= "&output=pdf";
$rpt_url .= "&req_no=" . isNotEmpty($_POST["doc_no"]);
$rpt_url .= "&sdate=" . isNotEmpty($_POST["sdate"]);
$rpt_url .= "&edate=" . isNotEmpty($_POST["edate"]);
$rpt_url .= "&org_id=" . isNotEmpty($_POST["dept_id"]);
$rpt_url .= "&emp_name=" . isNotEmpty($_POST["emp_name"]);
$rpt_url .= "&benefit=" . isNotEmpty($_POST["Benefit"]);
$rpt_url .= "&member_type="."9006,9007,9008";

$status_list = implode(",", $status);
$rpt_url .= "&req_status=" . str_replace("'","",$status_list);

$template->assign_var("rpt_url", $rpt_url);
$template->assign_var("xlsx_url", str_replace("output=pdf", "output=xlsxNoPag", $rpt_url));
$template->assign_var("xlsx_url", str_replace("output=pdf", "output=xlsxNoPag", $rpt_url));

$template->assign_var("req_no", $_POST["doc_no"]);
$template->assign_var("emp_name", $_POST["emp_name"]);
$template->assign_var("sdate", $_POST["sdate"]);
$template->assign_var("edate", $_POST["edate"]);
$template->assign_var("req_status", $status);
$template->assign_var("dept_name", $_POST["dept_name"]);
$template->assign_var("dept_id", $_POST["dept_id"]);

$template->assign_var("xlsx_url", str_replace("output=pdf", "output=xlsxNoPag", $rpt_url));

$num = $stmt->rowCount();
if ($num > 0) {
    $response_data = array();
    $count         = 1;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $newDate             = date("Y-m-d", strtotime($row["valid_begin"]));
        $row["req_datetime"] = $newDate;
        $row["EmpName"]      = $row["emp_title"] . $row["emp_first_name"] . " " . $row["emp_last_name"];
        $row["no"]           = $count;
        $row["Id"]           = $row["transaction_id"];
        $row["req_status"]   = status($row["status"]);

        $template->assign_block_vars('request', $row);
        $count++;
    }
}

$data = array(
    "menu_item" => 3,
);

$template->assign_vars($data);
$template->pparse('body');
function status($status)
{
    switch ($status) {
        case "I":
            $status = "รอพิจารณา";
            break;
        case "A":
            $status = "อนุมัติ";
            break;
        case "X":
            $status = "ไม่อนุมัติ";
            break;
        case "D":
            $status = "รอเอกสารเพิ่มเติม";
            break;
        default:
            $status = "-";
            break;
    }
    return $status;
}
