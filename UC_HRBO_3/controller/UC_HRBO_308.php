<?php
ob_start();
session_start();
require '../../session.php';
require_once '../../common.php';
require_once '../../lib/template.php';
require_once '../../models/importModel.php';
include '../../utils/base_function.php';

//prepare template
$template = new template();
$template->set_filenames(array(
    'body' => '../view/UC_HRBO_308.html')
);

// prepare product object
$model = new importModel("saleunit");
$stmt = $model->getAll();

    if($stmt !== NULL){
        $stmt['data']["no"] = '1';
        // $stmt['data']["lastRunningFormatDate"] = reformatDate($stmt['data']["lastRunningFormatDate"]);
        $template->assign_block_vars('request', $stmt['data']);
    }

// prepare product object
$data = array(
    "menu_item" => 2,
);

$template->assign_vars($data);
$template->pparse('body');
