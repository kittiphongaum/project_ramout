<?php
ob_start();
session_start();
require './session.php';
require_once './lib/template.php';
require_once './models/role_Model.php';
require_once './config/database.php';

$template = new template();
$template->set_filenames(array(
    'body' => './index.html')
);

//get database connection
$database = new Database();
$db       = $database->getConnection();

$model = new role_Model($db);

$response_data = array();

if (isset($_SESSION["username"])) {
    $model->emp_id    = $_SESSION["username"];

    $stmt = $model->get_role_menu();

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          if($row["total_on"] > 0){
            $template->assign_block_vars('data', $row);
          }
        }
    }
}

$data = array(
    "menu_item" => 0,
);

$template->assign_vars($data);
$template->pparse('body');
