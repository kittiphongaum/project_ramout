<?php
ob_start();
session_start();

require '../../session.php';
require_once '../../common.php';
require_once '../../lib/template.php';
require_once '../../config/database.php';
include '../../utils/base_function.php';
require_once '../model/HRBO_320_Model.php';

// Call view
$template = new template();
$template->set_filenames(array(
    'body' => '../view/UC_HRBO_320v.html'
));

// Get connections
$database = new Database();
$db = $database->getConnection();

// Get benefit types
// $model = new HRBO_320_Model($db);

// $benefits = $model->getRequestBenefitType();


// // Assign benefit types to view
// if ($benefits->rowCount() > 0) {
//     while ($row = $benefits->fetch(PDO::FETCH_ASSOC)) {
//         $template->assign_block_vars('data', $row);
//     }
// }

// Set var
$data = array(
    "menu_item" => 3,
    // "month_times" => (int)$model->getConfig("month_times"),
    // "max_person_guarantee" => (int)$model->getConfig("max_person_guarantee")
);

// Assign data to view
$template->assign_vars($data);
$template->pparse('body');