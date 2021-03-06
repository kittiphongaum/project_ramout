<?php
ob_start();
session_start();

require '../../session.php';
require_once '../../common.php';
require_once '../../lib/template.php';
require_once '../../config/database.php';
include '../../utils/base_function.php';

$template = new template();
$template->set_filenames(array(
    'body' => '../view/UC_HRBO_310_menu.html'
));

$data = array(
    "menu_item" => 3,
);

$template->assign_vars($data);
$template->pparse('body');
