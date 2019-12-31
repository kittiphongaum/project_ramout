<?php
ob_start();
session_start();
require '../../session.php';
require_once '../../common.php';
require_once '../../lib/template.php';
require_once '../../config/database.php';
require_once '../../UC_HRBO_3/model/HRBO_309_Model.php';
include '../../utils/base_function.php';

$template = new template();
$template->set_filenames(array(
    'body' => '../view/UC_HRBO_309.html')
);

//get database connection
$database = new Database();
$db       = $database->getConnection();
$model    = new HRBO_309_Model($db);

$model->doc_no = isNotEmpty($_POST["doc_no"]);
$model->sdate  = strToDateSdate(isNotEmpty($_POST["sdate"]));
$model->edate  = strToDateEdate(isNotEmpty($_POST["edate"]));

$org_lv1_disable = "true";
$org_lv2_disable = "true";
$org_lv3_disable = "true";

if (isNotEmpty($_POST["org_lv3"])) {
    $model->emp_org_id = end(explode("||", $_POST["org_lv1"]));
    $org_lv1_disable   = "false";
    $org_lv2_disable   = "false";
    $org_lv3_disable   = "false";
} 
if (isNotEmpty($_POST["org_lv2"])) {
    $model->emp_org_id = end(explode("||", $_POST["org_lv2"]));
    $org_lv1_disable   = "false";
    $org_lv2_disable   = "false";
    $org_lv3_disable   = "false";
} if (isNotEmpty($_POST["org_lv1"])) {
    $model->emp_org_id = end(explode("||", $_POST["org_lv2"]));
    $org_lv1_disable   = "false";
    $org_lv2_disable   = "false";

}

$model->emp_id = isNotEmpty($_POST["emp_id"]);
$model->status   = implode("','", $_POST["status"]);

$template->assign_var("doc_no", isNotEmpty($_POST["doc_no"]));
$template->assign_var("org_lv1_title", explode("||", $_POST["org_lv1"])[0]);
$template->assign_var("org_lv1", isNotEmpty($_POST["org_lv1"]));
$template->assign_var("org_lv1_disable", $org_lv1_disable);
$template->assign_var("org_lv2_title", explode("||", $_POST["org_lv2"])[0]);
$template->assign_var("org_lv2", isNotEmpty($_POST["org_lv2"]));
$template->assign_var("org_lv2_disable", $org_lv2_disable);
$template->assign_var("org_lv3_title", explode("||", $_POST["org_lv3"])[0]);
$template->assign_var("org_lv3", isNotEmpty($_POST["org_lv3"]));
$template->assign_var("org_lv3_disable", $org_lv3_disable);
$template->assign_var("personal_group", isNotEmpty($_POST["personal_group"]));
$template->assign_var("emp_id", isNotEmpty($_POST["emp_id"]));
$template->assign_var("sdate", isNotEmpty($_POST["sdate"]));
$template->assign_var("edate", isNotEmpty($_POST["edate"]));
$template->assign_var("dept_name", isNotEmpty($_POST["dept_name"]));

foreach ($_POST["status"] as $item) {
    switch ($item) {
        case "W":
            $template->assign_var("checked_0", "checked=''");
            break;
        case "S":
            $template->assign_var("checked_1", "checked=''");
            break;
        default:
            $template->assign_var("checked_2", "checked=''");
            break;
    }
}

$stmt_check_rg   = $model->check_emp_region($_SESSION["user_id"]);
$result_check_rg = $stmt_check_rg->fetch(PDO::FETCH_ASSOC);
$emp_region      = $result_check_rg["personal_group"];

$model->personal_group = $_POST["personal_group"] || $emp_region === "C" ? $_POST["personal_group"] : $emp_region;

if ($emp_region) {
    $stmt                  = $model->getAll();
    $num = $stmt->rowCount();
}

if ($num > 0) {
    $response_data = array();
    $count         = 1;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $row["req_date"] = date("Y-m-d H:i:s", strtotime($row["req_date"]));
        $row["no"]       = $count;
        $row["status"]   = status($row["status"]);
        $template->assign_block_vars('request', $row);
        unset($rows);
        $count++;
    }
}

if ($num_rg > 0) {
    $response_data = array();
    $count         = 1;

    if ($emp_region === "C") {
        $row["region_code"] = "";
        $row["region_name"] = "ทั้งหมด";
        $template->assign_block_vars('data_rg', $row);
        $row["region_code"] = "C";
        $row["region_name"] = "ส่วนกลาง";
        $template->assign_block_vars('data_rg', $row);
    }

    while ($row = $stmt_rg->fetch(PDO::FETCH_ASSOC)) {
        $template->assign_block_vars('data_rg', $row);
        unset($rows);
        $count++;
    }
}
$rpt_url = $host_jasper_service . "/jasperserver/flow.html?j_username=" . $host_jasper_username . "&j_password=" . $host_jasper_password . "&_flowId=viewReportFlow&reportUnit=/reports/aomsinbo/UC_HRBO_309&decorate=no";
$rpt_url .= "&output=pdf";
$rpt_url .= "&doc_no=" . isNotEmpty($_POST["doc_no"]);
$rpt_url .= "&sdate=" . isNotEmpty($_POST["sdate"]);
$rpt_url .= "&edate=" . isNotEmpty($_POST["edate"]);
$rpt_url .= "&org_id=" . $model->emp_org_id;
$rpt_url .= "&emp_name=" . isNotEmpty($_POST["emp_id"]);
$region_rpt = $_POST["personal_group"] || $emp_region === "C" ? $_POST["personal_group"] : $emp_region;
$rpt_url .= "&personal_group=" . $region_rpt;
$rpt_url .= "&status=" . implode("*,*", $_POST["status"]);
$template->assign_var("rpt_url", $rpt_url);
$template->assign_var("xlsx_url", str_replace("output=pdf", "output=xlsxNoPag", $rpt_url));

$data = array(
    "menu_item"   => 3,
    "rg_selected" => $_POST["personal_group"] || $emp_region === "C" ? $_POST["personal_group"] : $emp_region,
);
$template->assign_vars($data);
$template->pparse('body');

function status($data){
    if ($data === 'I') {
        $status_approve = 'รออนุมัติ';
    } elseif ($data === 'S') {
        $status_approve = 'แก้ไขจัดทำ และอนุมัติแล้ว';
    } elseif ($data === 'N') {
        $status_approve = 'ไม่อนุมัติ';
    } elseif ($data === 'A') {
        $status_approve = 'จัดทำใบส่งตัวเรียบร้อยแล้ว';
    } elseif ($data === 'X') {
        $status_approve = 'ไม่สามารถจัดทำใบส่งตัวได้';
    }elseif($data==="C"){
        $status_approve = 'ยกเลิกด้วยตัวเอง';
    }

    return $status_approve;
}
