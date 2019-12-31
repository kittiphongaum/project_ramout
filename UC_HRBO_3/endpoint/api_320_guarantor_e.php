<?php
header('Content-Type: application/json');
require_once '../../config/database.php';
include_once '../model/HRBO_320_Model.php';

$database = new Database();
$db       = $database->getConnection();

$model = new HRBO_320_Model($db);

    $model->key      = $_POST["key"];
    $model->subgroup      = $_POST["subgroup"];
    $model->claim_limit           = $_POST["claim_limit"];
    $model->status        = $_POST["statusswitch"];
    $model->title         = $_POST["title"];
    $model ->subgroup_name =gurantee($_POST["subgroup"]);
    $model->created_date  = Date('Y-m-d H:i:s');

    $resp["status"]=404;
    $stmt= $model->guarantorById($_POST["key"]);
    $num = $stmt->rowCount();

    if ($num >0) {
        $model->updated_date  = Date('Y-m-d H:i:s');
            $status = $model->updatesubGroup($_POST["key"]);
            if ($_POST["statusswitch"] =="A") {
                $model->updateStatus($_POST["key"]);
            }
            
            if ($status) {
                $resp["status"] = 200;

            } else {

                $resp["status"] = 201;
            }
    }else{
            $status = $model->insertsubgroup();
            if ($_POST["statusswitch"] =="A") {
                $model->updateStatus($_POST["key"]);
            }
            if ($status) {
                $resp["status"] = 200;
            } else {
                $resp["status"] = 201;
            }
    }

function gurantee($subgroup)
{
    switch ($subgroup) {
     case '10' : $subgroup = "รธส.อ./รธส./ผตก.อ."; break;
	 case '15' : $subgroup = "ชธส./ผตก."; break;
	 case '20' : $subgroup = "ฝ./ผอภ./ผชช.อ."; break;
	 case '25' : $subgroup = "รฝ./รอภ./ผอข./ผชช."; break;
	 case '30' : $subgroup = "ผจส.อ"; break;
	 case '35' : $subgroup = "ผจส./ผจศ./ผชน.อ."; break;
	 case '40' : $subgroup = "ชฝ/ชอภ/ชอข/ชจส/ผชน"; break;
	 case '45' : $subgroup = "พ.กลุ่มปฏิบัติการ 7"; break;
	 case '50' : $subgroup = "พ.กลุ่มปฏิบัติการ 6"; break;
	 case '55' : $subgroup = "พ.กลุ่มปฏิบัติการ 5"; break;
	 case '60' : $subgroup = "พ.กลุ่มปฏิบัติการ 4"; break;
	 case '65' : $subgroup = "พ.กลุ่มปฏิบัติการ 3"; break;
	 case '70' : $subgroup = "พ.กลุ่มปฏิบัติการ 2"; break;
	 case '75' : $subgroup = "พ.บริการ 3"; break;
	 case '80' : $subgroup = "พ.บริการ 2"; break;
	 case '85' : $subgroup = "พ.บริการ 1"; break;
	 case 'A1' : $subgroup = "1 : งานเฉพาะ"; break;
	 case 'A2' : $subgroup = "1 : แทนปฏิบัติการ"; break;
	 case 'A3' : $subgroup = "1 :ขับรถยนต์/ขับเรือ"; break;
	 case 'B1' : $subgroup = "2 : เหมาจ่าย"; break;
	 case 'C1' : $subgroup = "3 : โครงการ"; break;
	 case 'D1' : $subgroup = "4 : วิชาชีพ"; break;
	 case 'M1' : $subgroup = "อธส."; break;
	 case '10' : $subgroup = "รธส.อ./รธส./ผตก.อ."; break;
	 case '15' : $subgroup = "ชธส./ผตก."; break;
	 case '20' : $subgroup = "ฝ./ผอภ./ผชช.อ."; break;
	 case '25' : $subgroup = "รฝ./รอภ./ผอข./ผชช."; break;
	 case '30' : $subgroup = "ผจส.อ"; break;
	 case '35' : $subgroup = "ผจส./ผจศ./ผชน.อ."; break;
	 case '40' : $subgroup = "ชฝ/ชอภ/ชอข/ชจส/ผชน"; break;
	 case '45' : $subgroup = "พ.กลุ่มปฏิบัติการ 7"; break;
	 case '50' : $subgroup = "พ.กลุ่มปฏิบัติการ 6"; break;
	 case '55' : $subgroup = "พ.กลุ่มปฏิบัติการ 5"; break;
	 case '60' : $subgroup = "พ.กลุ่มปฏิบัติการ 4"; break;
	 case '65' : $subgroup = "พ.กลุ่มปฏิบัติการ 3"; break;
	 case '70' : $subgroup = "พ.กลุ่มปฏิบัติการ 2"; break;
	 case '75' : $subgroup = "พ.บริการ 3"; break;
	 case '80' : $subgroup = "พ.บริการ 2"; break;
	 case '85' : $subgroup = "พ.บริการ 1"; break;
	 case 'Z1' : $subgroup = "ทายาท"; break;
	 case 'Z2' : $subgroup = "กรรมการ"; break;
	 case 'Z3' : $subgroup = "ที่ปรึกษา"; break;
     default:
     $subgroup ="";
            break;
    }
    return $subgroup;
}
echo json_encode($resp, JSON_UNESCAPED_UNICODE);