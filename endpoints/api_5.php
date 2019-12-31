<?php
header('Content-Type: application/json');

$response = array();
if (isset($_GET["mod"])) {
    switch ($_GET["mod"]) {
        case ("501a"):
            $response = UC_HRBO_501_Add();
            break;
        case ("502a"):
            $response = UC_HRBO_502_Add();
            break;
        default:
            $response["result"] = false;
            break;
    }
} else {
    $response["result"] = false;
}

function UC_HRBO_501_Add()
{
    return array("result" => true);
}

function UC_HRBO_502_Add()
{
    return array("result" => true);
}

echo json_encode($response);
