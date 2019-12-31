<?php
$host                      = "http://10.22.50.146";
$host_jasper_service       = "http://10.22.50.146/gsb-report";
$host_email_service        = "http://10.22.50.146:18080";
$host_notification_service = "http://10.22.50.146:18080";
$host_sap                  = "http://10.22.50.146/gsb-service-api/m5-service/sap";

$host_jasper_username      = "jasperadmin";
$host_jasper_password      = "P@ssw0rd";

error_reporting(0);
date_default_timezone_set('Asia/Bangkok');
ini_set('session.gc_maxlifetime', 3600);
session_set_cookie_params(3600);
session_cache_expire(360);

$pdf_report_address = $host_jasper_service . "/jasperserver/flow.html?j_username=" . $host_jasper_username . "&j_password=" .$host_jasper_password . "&_flowId=viewReportFlow&reportUnit={reportUnit}&decorate=no&output=pdf&req_no={req_no}&sdate={sdate}&edate={edate}&org_id={org_id}&emp_name={emp_name}&req_status={req_status}";
$excel_report_address = $host_jasper_service . "/jasperserver/flow.html?j_username=" . $host_jasper_username . "&j_password=" .$host_jasper_password . "&_flowId=viewReportFlow&reportUnit={reportUnit}&decorate=no&output=xlsxNoPag&req_no={req_no}&sdate={sdate}&edate={edate}&org_id={org_id}&emp_name={emp_name}&req_status={req_status}";
define("RPT_SERVER_ADDRESS", $pdf_report_address);
define("XLS_SERVER_ADDRESS", $excel_report_address);
define("UPLOAD_PATH", __DIR__ . "assets/upload/");