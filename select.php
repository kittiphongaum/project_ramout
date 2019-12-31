<?php
ob_start();
session_start();
include 'session.php';
require_once 'common.php';
require_once 'lib/template.php';
require_once 'models/role_Model.php';
require_once 'config/database.php';

$template = new template();

$template->set_filenames(array(
    'body' => 'select.html')
);

$database = new Database();
$db       = $database->getConnection();

$model = new role_Model($db);

$response_data = array();

if (isset($_SESSION["username"])) {

    $model->emp_id    = $_SESSION["username"];
    $model->username  = strtolower($_SESSION["emp_username"]);
    $model->menu_code = "M000";
    $stmt             = $model->get_role_page();
    $stmt2            = $model->get_role_mypoint();
    $model->menu_code = "M999";
    $stmt_report      = $model->get_role_page();
    $hr_report        = $model->get_parent_role_page();
    $report_parent    = $hr_report->fetch(PDO::FETCH_ASSOC);

    $mypoint = $stmt2->rowCount();
    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if ($row["box_code"] == "B040" && $mypoint > 0) {
                $row["path"] = $_SESSION["url_mypoint"];
                $template->assign_block_vars('data', $row);
            } else {
                if ($row["box_code"] != "B040") {
                    $template->assign_block_vars('data', $row);
                }
            }

        }
    } else {
        if ($mypoint > 0) {
            $row["path"]     = $_SESSION["url_mypoint"];
            $row["box_name"] = "My Point";
            $row["icon"]     = "My Point";
            $template->assign_block_vars('data', $row);
        }
    }
    if ($hr_report->rowCount() > 0) {
        $row["path"]     = $report_parent["path"];
        $row["box_name"] = $report_parent["menu_name"];
        $row["icon"]     = $report_parent["menu_name"];
        $template->assign_block_vars('data', $row);
    }
}
$data = array(
    "menu_item" => 0,
);

$template->assign_vars($data);
$template->pparse('body');
