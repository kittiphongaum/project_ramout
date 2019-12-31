<?php
header('Content-Type: application/json');

$response = array();
if (isset($_GET["mod"])) {
    switch ($_GET["mod"]) {
        case ("201e"):
            $response = UC_HRBO_201_Edit();
            break;
        case ("202e"):
            $response = UC_HRBO_202_Edit();
            break;
        case ("203e"):
            $response = UC_HRBO_203_Edit();
            break;
        case ("204e"):
            $response = UC_HRBO_204_Edit();
            break;
        case ("205e"):
            $response = UC_HRBO_205_Edit();
            break;
        case ("206e"):
            $response = UC_HRBO_206_Edit();
            break;
        case ("207e"):
            $response = UC_HRBO_207_Edit();
            break;
        case ("208a"):
            $response = UC_HRBO_208_Add();
            break;
        case ("208e"):
            $response = UC_HRBO_208_Edit();
            break;
        default:
            $response["result"] = false;
            break;
    }
} else {
    $response["result"] = false;
}

function UC_HRBO_201_Edit()
{
    return array("result" => true);
}

function UC_HRBO_202_Edit()
{
    return array("result" => true);
}

function UC_HRBO_203_Edit()
{
    return array("result" => true);
}

function UC_HRBO_204_Edit()
{
    return array("result" => true);
}

function UC_HRBO_205_Edit()
{
    return array("result" => true);
}

function UC_HRBO_206_Edit()
{
    return array("result" => true);
}

function UC_HRBO_207_Edit()
{
    return array("result" => true);
}

function UC_HRBO_208_Add()
{
    return array("result" => true);
}

function UC_HRBO_208_Edit()
{
    return array("result" => true);
}

echo json_encode($response);
