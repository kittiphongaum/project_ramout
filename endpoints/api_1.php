<?php
header('Content-Type: application/json');

$response = array();
if (isset($_GET["mod"])) {
    switch ($_GET["mod"]) {
        case ("101e"):
            $response = UC_HRBO_101_Edit();
            break;
        default:
            $response["result"] = false;
            break;
    }
} else {
    $response["result"] = false;
}

function UC_HRBO_101_Edit()
{
    return array("result" => true);
}

echo json_encode($response);
