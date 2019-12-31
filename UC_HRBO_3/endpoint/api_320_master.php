<?php

header('Content-Type: application/json');
require_once '../../config/database.php';
include_once '../model/HRBO_320_Model.php';

$database = new Database();
$db       = $database->getConnection();

$model = new HRBO_320_Model($db);

$stmt = $model->getmaster();
$num = $stmt->rowCount();
$resp = array();
if ($num > 0) {
    $count = 1;

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        array_push($resp, $row);
        $count++;
    }
}

echo json_encode($resp, JSON_UNESCAPED_UNICODE);
