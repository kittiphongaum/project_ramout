<?php
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 10000); 
header('Content-Type: application/json');
ob_start();
session_start();
require '../../session.php';
require_once '../../common.php';
require_once '../../config/database.php';
require_once '../model/course_master_Model_import.php';
require_once "../../vendor/autoload.php";
require_once '../../utils/validate_excel.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
$database = new Database();
$db       = $database->getConnection();

$model = new course_master_Model_import($db);
$date_now = date("Y-m-d H:i:s");
if (isset($_FILES["file"])) {
    
    $file_path = uploadFile(
        basename($_FILES['file']['name']),
        $_FILES['file']['tmp_name'],
        $_FILES['file']['size']
    );
    $spreadsheet = IOFactory::load($file_path);
   
    $data       = $spreadsheet->getActiveSheet()->toArray();
    $data2      = $data;
    $first      = true;
    $count      = 0;
    $error      = 0;
    $resp       = array();
    $resp1       =[];
    $data_check = [];
    $count2     = 0;

    $data_fetch = [];
    $sumtable;

    if ($data2!=[] && $data2!=null) {
        $model->delTableSave();

    foreach ($data2 as $row) {
        if ($count2 == 0) {
            $count2++;
            continue;
        }
        if (empty($row[0]) && empty($row[1])) {
            break;
        } else {
            $resp=[];
            if ( $row[0]!="" && $row[0]!=null) {
                // var_export($row);
                    foreach($row as $key => $data) {
                     if ($data == "-" || $data == ".") {
                           $row[$key] = 0;
                        }
                    }
            $row[0] = str_replace(",","",$row[0]);
            $row[1] = str_replace(",","",$row[1]);
            $row[2] = str_replace(",","",$row[2]);
            $row[3] = str_replace(",","",$row[3]);
            $row[4] = str_replace(",","",$row[4]);
            $row[5] = str_replace(",","",$row[5]);
            $row[6] = str_replace(",","",$row[6]);
            $row[7] = str_replace(",","",$row[7]);

            $model->emp_id              = $row[0] ? $row[0] : 0;
            $model->saving_amt          = $row[1] ? $row[1] : 0;
            $model->saving_benefit      = $row[2] ? $row[2] : 0;
            $model->supporting_amt      = $row[3] ? $row[3] : 0;
            $model->supporting_benefit  = $row[4] ? $row[4] : 0;
            $model->initiate_amt        = $row[5] ? $row[5] : 0;
            $model->initiate_benefit    = $row[6] ? $row[6] : 0;
            $model->initiate_result_1   = $row[7] ? $row[7] : 0;
 
            $model->created_date        = $date_now;

                if ( $row[0]!= 0 ) {
                     $result =  $model->create();
                    $sumtable=$result;
                    $count++;
                 }
               if($sumtable == false){
                 $resp1["emp_id"]=$row[0];
                 $resp1["saving_amt"]=$row[1];
                 $resp1["saving_benefit"]=$row[2];
                 $resp1["supporting_amt"]=$row[3];
                 $resp1["supporting_benefit"]=$row[4];
                 $resp1["initiate_amt"]=$row[5];
                 $resp1["initiate_benefit"]=$row[6];
                 $resp1["initiate_result_1"]=$row[7];
           
                 $error++;
               }
           
             }else{
                 $error++;
                
                }
             }   
        }
        if ($sumtable == true && $error == 0 ){
            $model->delTable();
            $model->sumTable();
        }
    }else{
    $sumtable= false;
    $error=$error+1;
    $resp=null;
 }
 
    $d["count"] = $count;
    $d["sumTable"] = $sumtable;
    $d["errors"] = $error;
    $d["desc"]   = $resp1;
    echo json_encode($d);
}
        function uploadFile($file_name, $file_tmp, $file_size)
        {
            if (isset($file_name) && isset($file_tmp)) {
                $errors   = array();
                $file_ext = strtolower(end(explode('.', $file_name)));
                $expensions = array("csv", "xlsx", "xls","XLSX");
                if (in_array($file_ext, $expensions) === false) {
                    die("extension not allowed, please choose a CSV or TXT or Xlsx file.");
                }

                if (empty($errors) == true) {
                    $tmppath = "../../assets/upload/" . uniqid() . '.' . $file_ext;
                    move_uploaded_file($file_tmp, $tmppath);
                    return $tmppath;
                } else {
                    return $errors;
                }
            }
        }

