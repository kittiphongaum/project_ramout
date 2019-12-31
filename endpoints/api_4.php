<?php
header('Content-Type: application/json');

$response = array();
if (isset($_GET["mod"])) {
    switch ($_GET["mod"]) {
        case ("401a"):
            $response = UC_HRBO_401_Add();
            break;
        case ("401e"):
            $response = UC_HRBO_401_Edit();
            break;
        case ("402a"):
            $response = UC_HRBO_402_Add();
            break;
        case ("402i"):
            $response = UC_HRBO_402_Import();
            break;
        case ("403i"):
            $response = UC_HRBO_403_Import();
            break;
        case ("405a"):
            $response = UC_HRBO_405_Add();
            break;
        case ("405e"):
            $response = UC_HRBO_405_Edit();
            break;
        case ("406a"):
            $response = UC_HRBO_406_Add();
            break;
        case ("406e"):
            $response = UC_HRBO_406_Edit();
            break;
        case ("407a"):
            $response = UC_HRBO_407_Add();
            break;
        case ("407e"):
            $response = UC_HRBO_407_Edit();
            break;
        case ("404e"):
            $response = UC_HRBO_404_Edit();
            break;
        default:
            $response["result"] = false;
            break;
    }
} else {
    $response["result"] = false;
}

function UC_HRBO_401_Add()
{
    return array("result" => true);
}

function UC_HRBO_401_Edit()
{
    return array("result" => true);
}

function UC_HRBO_402_Add()
{
    return array("result" => true);
}

function UC_HRBO_402_Import()
{
    return array("result" => true);
}

function UC_HRBO_403_Import()
{
    return array("result" => true);
}

function UC_HRBO_405_Add()
{
    return array("result" => true);
}

function UC_HRBO_405_Edit()
{
    return array("result" => true);
}

function UC_HRBO_406_Add()
{
    return array("result" => true);
}

function UC_HRBO_406_Edit()
{
    return array("result" => true);
}

function UC_HRBO_407_Add()
{
    return array("result" => true);
}

function UC_HRBO_407_Edit()
{
    return array("result" => true);
}

function UC_HRBO_404_Edit()
{
    return array("result" => true);
}

echo json_encode($response);
