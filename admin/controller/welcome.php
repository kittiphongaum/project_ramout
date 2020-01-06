<?php
ob_start();
// session_start();
// require '../../session.php';
// require_once '../../common.php';
require_once '../../lib/template.php';
// require_once '../../config/database.php';
// require_once '../../UC_HRBO_3/model/course_master_Model_import.php';
$template = new template();
$template->set_filenames(array(
    'body' => '../view/welcome.html')
);
// $database = new Database();
// $db       = $database->getConnection();

$data  = array(
    "menu_item" => 3,
);

$template->assign_vars($data);
$template->pparse('body');
