<?php
ob_start();
require_once '../../common.php';
require_once '../../utils/base_function.php';
$data = json_decode(base64_decode(htmlspecialchars($_GET["id"])));
$url    = $host .  "/gsb-service-api/m4-service/internal/benefits/pdf/empid/" . $data->emp_id . "/docno/" . $data->doc_no . "/".$data->category;
$ch     = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Encoding: none','Content-Type: application/pdf'));

header("Content-type: application/pdf");
header('Content-disposition: filename="'.$data->name.$data->doc_no.'.pdf"');
$result = curl_exec($ch);
curl_close($ch);
echo $result;
