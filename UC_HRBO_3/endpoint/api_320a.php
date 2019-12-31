<?php
ob_start();
header('Content-Type: application/json');
require_once '../../common.php';

$url = $host . "/gsb-service-api/m4-service/internal/benefits/fundloan/master/category/LOAN_OBJECTIVE";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$json_response = curl_exec($ch);

$response = json_decode($json_response, true);


echo json_encode($response, JSON_UNESCAPED_UNICODE);
