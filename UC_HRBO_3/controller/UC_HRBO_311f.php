<?php
ob_start();
session_start();
require '../../session.php';
require_once '../../common.php';
require_once '../../lib/template.php';
require_once '../../config/database.php';
require_once '../../UC_HRBO_3/model/HRBO_311_Model.php';
include '../../utils/base_function.php';

//prepare template
$template = new template();
$template->set_filenames(array(
    'body' => '../view/UC_HRBO_311f.html')
);

//get database connection
$database = new Database();
$db = $database->getConnection();

// prepare product object
$model = new HRBO_311_Model($db);
if (isset($_POST["search"])) {
    // $model->emp_name = isNotEmpty($_POST["emp_name"]);
    // $model->req_status_list = isset($_POST["req_status"]) ? $_POST["req_status"] : null;
    // $stmt = $model->search();

    // if (is_array($model->req_status_list)) {
    //     foreach ($model->req_status_list as $item) {
    //         if ($item == 0) {
    //             $template->assign_var("checked_0", "checked=''");
    //         } else if ($item == 1) {
    //             $template->assign_var("checked_1", "checked=''");
    //         }
    //     }
    // }

    // $template->assign_var("emp_name", isNotEmpty($_POST["emp_name"]));

} else {
    // $template->assign_var("checked_1", "checked=''");
    $stmt = $model->getAll();
}

$num = $stmt->rowCount();
if ($num > 0) {
    $response_data = array();
    $count = 1;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        //print_r($row);
        $row["no"] = $count;
        $row["upload_date"] = reformatDate($row["upload_date"]);
        $row["n_of_rows"] = number_format($row["n_of_rows"],0,".",",");
        $template->assign_block_vars('request', $row);
        unset($rows);
        $count++;
    }
}

$data = array(
    "menu_item" => 2,
);

$template->assign_vars($data);
$template->pparse('body');
