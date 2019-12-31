<?php
header('Content-Type: application/json');
ob_start();
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../../session.php';
require_once '../../common.php';
require_once '../../config/database.php';
require_once '../../utils/base_function.php';
require_once '../../UC_HRBO_3/model/HRBO_310_Model.php';

//get database connection
$database = new Database();
$db = $database->getConnection();

// Get Model
$model = new HRBO_310_Model($db);

// Assign loan config
$loan_config = $_POST['loan_config'];

// Defined result var
$result = [];
// Set month times
$month_times = $loan_config["month_times"] ?? "";
$times_result = $model->setConfigMonthTimes($month_times);
$result[] = $times_result->rowCount() > 0 ? "Times updated" : "Times update failed";

// Set garantee person
$max_person_guarantee = $loan_config["max_person_guarantee"] ?? "";
$person_result = $model->setConfigMaxPersonGuarantee($max_person_guarantee);
$result[] = $person_result->rowCount() > 0 ? "Guarantee person updated" : "Guarantee person failed";

// Set loan amount
$loan_amounts = $_POST["loan_amount"];
$model->setConfigLoanAmounts($loan_amounts);

$resp["result"] = true;
$resp["description"] = $result;

printJson($response);
