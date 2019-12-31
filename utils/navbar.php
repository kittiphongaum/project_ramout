<?php
ob_start();
session_start();
require_once 'common.php';
require_once 'lib/template.php';
$template = new template();

$template->set_filenames(array(
    'body' => 'navbar.html')
);

if (isset($_GET["active"])) {
    switch ($_GET["active"]) {
        case 0:
            $template->assign_var('active_0', 'active');
            break;
        case 1:
            $template->assign_var('active_1', 'active');
            break;
        case 2:
            $template->assign_var('active_2', 'active');
            break;
        case 3:
            $template->assign_var('active_3', 'active');
            break;
        case 4:
            $template->assign_var('active_4', 'active');
            break;
        case 5:
            $template->assign_var('active_5', 'active');
            break;
        case 6:
            $template->assign_var('active_6', 'active');
            break;
        case 7:
            $template->assign_var('active_7', 'active');
            break;
        case 9:
            $template->assign_var('active_9', 'active');
            break;
        case 10:
            $template->assign_var('active_10', 'active');
            break;
    }
}

$data = array(
    "username" => $_SESSION["username"],
    "firstname" => $_SESSION["emp_name"],
);
$template->assign_vars($data);
$template->pparse('body');
