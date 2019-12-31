<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../../config/database.php';
require_once '../../utils/base_function.php';
include_once '../model/HRBO_301_Model.php';
require_once '../../lib/template.php';

//Make sure that it is a POST request.
if (strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') != 0) {
    die('Request method must be POST!');
}

// get database connection
$database = new Database();
$template = new template();
$db = $database->getConnection();

// prepare product object
$model = new HRBO_301_Model($db);

// get posted data
$input = file_get_contents('php://input');

$data = json_decode($input);
// make sure data is not empty
if (!empty($data->reqid)) {
    $model->reqid = $data->reqid;

    $stmt  = $model->HistroryNoti();
    $num = $stmt->rowCount();
    $response_data = array();

    if ($num > 0) {
        $count = 1;

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $response_item = array(
                "no" => $count,
                "created_date" => date("Y-m-d H:i", strtotime($row["created_date"])),
                "nnn" => $row["msg"]

            );

            $count ++;
            array_push($response_data, $response_item);
        }
    }
}

printJson($response_data);