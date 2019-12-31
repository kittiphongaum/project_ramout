<?php
ob_start();
session_start();
require '../../session.php';
require_once '../../common.php';
require_once '../../lib/template.php';
require_once '../../config/database.php';
require_once '../../UC_HRBO_3/model/HRBO_312_Model.php';
include '../../utils/base_function.php';

$template = new template();
$template->set_filenames (array(
    'body' => '../view/UC_HRBO_312.html')
);

//get database connection
$database = new Database();
$db       = $database->getConnection();
$model    = new HRBO_312_Model($db);

$stmt = $model->getAll();

$num = $stmt->rowCount();
if ($num > 0) {
    $response_data = array();
    $count = 1;

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $row["amount"]       = number_format($row["amount"], 2, '.', ',');
        $row["less_than"]    = number_format($row["less_than"], 2, '.', ',');
        $row["greater_than"] = number_format($row["greater_than"], 2, '.', ',');
        $originalDate        = $row["created_date"];
        $newDate             = date("Y-m-d H:i", strtotime($originalDate));
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
