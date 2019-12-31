<?php
header('Content-Type: application/json');
ob_start();
session_start();
require '../../session.php';
require_once '../../common.php';
require_once '../../config/database.php';
require_once '../../UC_HRBO_3/model/HRBO_311_Model.php';
require_once '../../utils/base_function.php';
require_once "../../vendor/autoload.php";
require_once '../../utils/validate_excel.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
$resp = array();

if (true) {
    if ($_FILES["file"]["error"] == 1) {
        $d["file_size_error"] = 1;
    } else {
        $file_path  =   uploadFile(
                            basename($_FILES['file']['name']),
                            $_FILES['file']['tmp_name'],
                            $_FILES['file']['size']
                        );

        $spreadsheet = IOFactory::load($file_path);
        // $spreadsheet = IOFactory::load("../../assets/upload/guarantee/5c3ee25ba15c5.xlsx");
        $data        = $spreadsheet->getActiveSheet()->toArray();
        $count       = 0;
        $count2      = 0;
        $error       = 0;
        foreach ($data as $row) {

            if ($count<2) {
                $count++;
                continue;
            }

            $col0 = validate_date(substr($row[0], 0, 4) . "-" . substr($row[0], 4, 2) . "-" . substr($row[0], 6, 2), $count, 0);
            if (!$col0["status"]) {
                $error++;
                array_push($resp, $col0);
            }

            $col1 = validate_text($row[13], $count, 13);
            if (!$col1["status"]) {
                $error++;
                array_push($resp, $col1);
            }

            $col2 = validate_text($row[14], $count, 14);
            if (!$col2["status"]) {
                $error++;
                array_push($resp, $col2);
            }

            $col3 = validate_number($row[12], $count, 12);
            if (!$col3["status"]) {
                $error++;
                array_push($resp, $col3);
            }

            $col4 = validate_number($row[15], $count, 15);
            if (!$col4["status"]) {
                $error++;
                array_push($resp, $col4);
            }

            $col5 = validate_number(str_replace(",","",$row[16]), $count, 16);
            if (!$col5["status"]) {
                $error++;
                array_push($resp, $col5);
            }

            $col6 = validate_number(str_replace(",","",$row[17]), $count, 17);
            if (!$col6["status"]) {
                $error++;
                array_push($resp, $col6);
            }

            $col7 = validate_text($row[22], $count, 22);
            if (!$col7["status"]) {
                $error++;
                array_push($resp, $col7);
            }

            $col8 = validate_text($row[23], $count, 23);
            if (!$col8["status"]) {
                $error++;
                array_push($resp, $col8);
            }

            $col9 = validate_text($row[24], $count, 24);
            if (!$col9["status"]) {
                $error++;
                array_push($resp, $col9);
            }

            $col10 = validate_number($row[25], $count, 25);
            if (!$col10["status"]) {
                $error++;
                array_push($resp, $col10);
            }

            $col11 = validate_number($row[26], $count, 26);
            if (!$col11["status"]) {
                $error++;
                array_push($resp, $col11);
            }
            $count++;
        }
        if ($error == 0) {

            $database = new Database();
            $db = $database->getConnection();
            $model = new HRBO_311_Model($db);

            $model->upload_date = date("Y-m-d");
            $model->remark = $_POST["remark"];
            $model->n_of_rows = 0;
            $model->upload_file_name = $_FILES['file']['name'];
            $model->upload_file_path = $file_path;
            $file_id = $model->createFile();

            foreach ($data as $row) {
                if ($count2 < 2) {
                    $count2++;
                    continue;
                }

                $model->file_id = $file_id;
                $model->act_emp_id = $_SESSION["user_id"];
                $model->as_of_date = substr($row[0],0,4)."-".substr($row[0],4,2)."-".substr($row[0],6,2);
                $model->loan_emp_id = $row[15];
                $model->loan_id_card = $row[12];
                $model->loan_prefix = $row[13];
                $model->loan_name = $row[14];
                $model->loan_amount = str_replace(",","",$row[16]);
                $model->loan_remain = str_replace(",","",$row[17]);
                $model->gua_prefix = $row[22];
                $model->gua_firstname = $row[23];
                $model->gua_lastname = $row[24];
                $model->gua_emp_id = $row[25];
                $model->gua_id_card = $row[26];

                $model->create();
            }

            $model->id = $file_id;
            $model->n_of_rows = ($count - 1) - $error;
            $records = ($count - 1) - $error;
            $model->updateFile();
        }

        $d["errors"] = $error;
        $d["desc"]   = $resp;
    }

    printJson($d);
}


function uploadFile($file_name, $file_tmp, $file_size)
{
    if (isset($file_name) && isset($file_tmp)) {
        $errors = array();
        $file_ext = strtolower(end(explode('.', $file_name)));

        $expensions = array("csv", "xls", "xlsx");
        if (in_array($file_ext, $expensions) === false) {
            die("extension not allowed, please choose a CSV or Xls or Xlsx file.");
        }

        if (empty($errors) == true) {
            $tmppath = "../../assets/upload/guarantee/" . uniqid() . '.' . $file_ext;
            move_uploaded_file($file_tmp, $tmppath);
            return $tmppath;
        } else {
            return $errors;
        }
    }
}
