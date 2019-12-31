<?php
ob_start();
session_start();
require '../../session.php';
require_once '../../common.php';
require_once '../../lib/template.php';
require_once '../../config/database.php';
require_once '../../UC_HRBO_3/model/HRBO_310_Model.php';
include '../../utils/base_function.php';

$template = new template();
$template->set_filenames(array(
    'body' => '../view/UC_HRBO_310.html')
);

//get database connection
$database = new Database();
$db       = $database->getConnection();
$model    = new HRBO_310_Model($db);

if (isset($_POST["search"])) {
    $EmpDep = isNotEmpty($_POST["dept_name"]);
    $dept_code = isNotEmpty($_POST["dept_name"])? isNotEmpty($_POST["dept_id"]) : null ;
    $dept_code = str_replace("(","",$dept_code);
    $dept_code = str_replace(")","",$dept_code);

    $model->dept_code = isNotEmpty($dept_code);
    $model->req_no = isNotEmpty($_POST["req_no"]);
    $model->EmpName = isNotEmpty($_POST["emp_name"]);
    $model->sdate = strToDate(isNotEmpty($_POST["sdate"]));
    $model->edate = strToDate(isNotEmpty($_POST["edate"]));

    $stmt = $model->search();

    $rpt_url = RPT_SERVER_ADDRESS;
    $rpt_url = str_replace("{reportUnit}", "/reports/aomsinbo/UC_HRBO_310", $rpt_url);
    $rpt_url = str_replace("{req_no}", isNotEmpty($_POST["req_no"]), $rpt_url);
    $rpt_url = str_replace("{sdate}", str_replace("-", "/", isNotEmpty($_POST["sdate"])), $rpt_url);
    $rpt_url = str_replace("{edate}", str_replace("-", "/", isNotEmpty($_POST["edate"])), $rpt_url);
    $rpt_url = str_replace("{org_id}", "", $rpt_url);
    $rpt_url = str_replace("{emp_name}", isNotEmpty($_POST["emp_name"]), $rpt_url);
    $rpt_url = str_replace("{req_status}", $filter_req_status, $rpt_url);

    $template->assign_var("dept_name", isNotEmpty($_POST["dept_name"]));
    $template->assign_var("dept_id", isNotEmpty($_POST["dept_id"]));
    $template->assign_var("req_no", isNotEmpty($_POST["req_no"]));
    $template->assign_var("emp_name", isNotEmpty($_POST["emp_name"]));
    $template->assign_var("sdate", isNotEmpty($_POST["sdate"]));
    $template->assign_var("edate", isNotEmpty($_POST["edate"]));
    $template->assign_var("rpt_url", $rpt_url);

} else {

    $rpt_url = RPT_SERVER_ADDRESS;
    $rpt_url = str_replace("{reportUnit}", "/reports/aomsinbo/UC_HRBO_310", $rpt_url);
    $rpt_url = str_replace("{req_no}", "", $rpt_url);
    $rpt_url = str_replace("{sdate}", "", $rpt_url);
    $rpt_url = str_replace("{edate}", "", $rpt_url);
    $rpt_url = str_replace("{org_id}", "", $rpt_url);
    $rpt_url = str_replace("{emp_name}", "", $rpt_url);
    $rpt_url = str_replace("{req_status}", "", $rpt_url);

    $template->assign_var("rpt_url", $rpt_url);
    $template->assign_var("checked_0", "checked=''");

    $stmt = $model->getAll();
}

$template->assign_var("xlsx_url", str_replace("output=pdf", "output=xlsxNoPag", $rpt_url));
$num = $stmt->rowCount();

if ($num > 0) {
    $response_data = array();
    $count = 1;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $originalDate = $row["CreatedDate"];
        $newDate = date("Y-m-d H:i", strtotime($originalDate));
        $row["req_datetime"] =  $newDate;
        $row["no"]           = $count;
        $row["priviledge_month"] = number_format($row["priviledge_month"],2, '.',',');
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
