<?php
header('Content-Type: application/json');
ob_start();
session_start();
require '../../session.php';
require_once '../../common.php';
require_once '../../config/database.php';
require_once '../../UC_HRBO_3/model/HRBO_308_Model.php';

//get database connection
$database = new Database();
$db = $database->getConnection();

if (!empty($_POST)) {
    // print_r($form);
    // print_r($_FILES['file']);
    if (isset($_FILES["file"])) {
        print_r($_FILES["file"]);
        $model = new HRBO_308_Model($db);
        $model->upload_date = date("Y-m-d");
        $model->n_of_rows = 0;
        $model->remark = $_POST["remark"];
        $model->upload_file_name = $_FILES['file']['name'];
        $model->upload_file_path = uploadFile(
            basename($_FILES['file']['name']),
            $_FILES['file']['tmp_name'],
            $_FILES['file']['size']);
        $id = $model->createFile();

        $row = 1;        
        if (($handle = fopen($model->upload_file_path, "r")) !== false) {
            $import = new HRBO_308_Model($db);
            while (($data = fgetcsv($handle, 1000, "|")) !== false) {
                if ($row > 1) {
                    $import->file_id = $id;
                    $import->Brncode = $data[0];
                    $import->AssetMngID = $data[1];
                    $import->AssetName = $data[2];
                    $import->Fundtype2id = $data[3];
                    $import->Fundtype2Name = $data[4];
                    $import->Fundshortname = $data[5];
                    $import->Fundname = $data[6];
                    $import->Amount = $data[7];
                    $import->Tradedate = $data[8];
                    $import->Employeecode = $data[9];
                    $import->create();
                }

                $row++;
            }
            fclose($handle);
            $model = new HRBO_308_Model($db);
            $model->id = $id;
            $model->n_of_rows = $row-2;
            $records = $row-2;
            $model->updateFile();
        }
        header("location: ../controller/UC_HRBO_308a.php?result=true&records=$records");
    }
} else {
    header("location: ../controller/UC_HRBO_308a.php?result=false");
}

function uploadFile($file_name, $file_tmp, $file_size)
{
    if (isset($file_name) && isset($file_tmp)) {
        $errors = array();
        $file_ext = strtolower(end(explode('.', $file_name)));

        $expensions = array("csv", "txt");
        if (in_array($file_ext, $expensions) === false) {
            die("extension not allowed, please choose a CSV or TXT file.");
        }

        if (empty($errors) == true) {
            $tmppath = "../../assets/upload/trading/" . uniqid() . '.' . $file_ext;
            move_uploaded_file($file_tmp, $tmppath);
            return $tmppath;
        } else {
            return $errors;
        }
    }
}
