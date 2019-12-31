<?php
ob_start();
session_start();
require_once 'common.php';
require_once 'config/database.php';
require_once 'models/userModel.php';
require_once 'lib/template.php';

$template = new template();
$template->set_filenames(array(
    'body' => 'login.html')
);

$data = array();

if (isset($_GET["e"])) {
    $data["showError"] = "$('#error').show();";
}

$template->assign_vars($data);
$template->pparse('body');

?>