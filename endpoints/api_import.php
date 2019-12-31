<?php
header('Content-Type: application/json');
ob_start();
session_start();

require '../session.php';
require_once '../common.php';
require_once '../controllers/importController.php';
require_once '../utils/base_function.php';

$import = new importController();

$response_data = $import->run($_GET['type']);

printJson($response_data);
