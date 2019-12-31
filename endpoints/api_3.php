<?php
header('Content-Type: application/json');

$response = array();
if (isset($_GET["mod"])) {
    switch ($_GET["mod"]) {
        case ("301e"):
            $response = UC_HRBO_301_Edit();
            break;
        case ("302e"):
            $response = UC_HRBO_302_Edit();
            break;
        case ("303e"):
            $response = UC_HRBO_303_Edit();
            break;
        case ("304e"):
            $response = UC_HRBO_304_Edit();
            break;
        case ("305e"):
            $response = UC_HRBO_305_Edit();
            break;
        case ("306e"):
            $response = UC_HRBO_306_Edit();
            break;
        case ("307e"):
            $response = UC_HRBO_307_Edit();
            break;
        default:
            $response["result"] = false;
            break;
    }
} else {
    $response["result"] = false;
}

function UC_HRBO_301_Edit()
{
    return array("result" => true);
}

function UC_HRBO_302_Edit()
{
    return array("result" => true);
}

function UC_HRBO_303_Edit()
{
    return array("result" => true);
}

function UC_HRBO_304_Edit()
{
    return array("result" => true);
}

function UC_HRBO_305_Edit()
{
    return array("result" => true);
}

function UC_HRBO_306_Edit()
{
    return array("result" => true);
}

function UC_HRBO_307_Edit()
{
    return array("result" => true);
}

echo json_encode($response);
