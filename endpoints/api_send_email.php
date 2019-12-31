<?php
require_once '../common.php';

$data["toEmpCode"] = [$_POST["toEmpCode"]];
$data["subject"]   = $_POST["subject"];
$data["body"]      = $_POST["body"];

$response_data = array();

$myJSON = json_encode($data);

$curl = curl_init();

curl_setopt($curl, CURLOPT_POST, 1);

curl_setopt($curl, CURLOPT_POSTFIELDS, $myJSON);

curl_setopt($curl, CURLOPT_URL, $host_email_service . "/gsb-mobile/internal-email.service");

curl_setopt($curl, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

$result = curl_exec($curl);
echo $result;
