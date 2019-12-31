<?php
header('Content-Type: application/json');

$response = array();
if (isset($_GET["mod"])) {
    switch ($_GET["mod"]) {
        case ("601a"):
            $response = UC_HRBO_601_Add();
            break;
        default:
            $response["result"] = false;
            break;
    }
} else {
    $response["result"] = false;
}

function UC_HRBO_601_Add()
{
    return array("result" => true);
}

echo json_encode($response);
