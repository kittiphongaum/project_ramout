<?php

function reformatDate($date)
{
    $date = str_replace("/", "-", $date);
    if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $date)) {
        return substr($date, 8, 2) . "-" . substr($date, 5, 2) . "-" . substr($date, 0, 4);
    } else {
        return $date;
    }
}

function strToDate($date)
{
    $date = str_replace("/", "-", $date);
    if (preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{4}$/", $date)) {
        return substr($date, 6, 4) . "-" . substr($date, 3, 2) . "-" . substr($date, 0, 2);
    } else {
        return $date;
    }
}

function changeFormatDatetoDB($date)
{
    $date = str_replace("/", "-", $date);
    $sp   = explode("-", $date);
    $sp2  = explode(":", $sp[2]);
    $sp3  = explode(" ", $sp2[0]);

    return $sp3[0] . "-" . $sp[1] . "-" . $sp[0] . " " . $sp3[1] . ":" . $sp2[1];
}

function changeFormatDatetoDBNonTime($date)
{
    $date = str_replace("/", "-", $date);
    $sp   = explode("-", $date);
    return $sp[2] . "-" . $sp[1] . "-" . $sp[0];

}

function changeFormatDBtoDate($date)
{
    $date = str_replace("/", "-", $date);
    $sp   = explode("-", $date);
    $sp2  = explode(":", $sp[2]);
    $sp3  = explode(" ", $sp2[0]);

    return $sp3[0] . "/" . $sp[1] . "/" . $sp[0] . " " . $sp3[1] . ":" . $sp2[1];
}

function reformatStatus($status)
{
    if ($status == 0) {
        return "รอพิจารณา";
    } else if ($status == 1) {
        return "อนุมัติ";
    } else if ($status == 2) {
        return "ไม่อนุมัติ";
    } else if ($status == 3) {
        return "ส่งออกข้อมูล";
    }
}

function reformatStatusResign($status)
{
    if ($status == 0) {
        return "รอพิจารณา";
    } else if ($status == 1) {
        return "ส่งออกข้อมูล";
    } else if ($status == 2) {
        return "ยกเลิกลาออก";
    } else if ($status == 3) {
        return "--ไม่มีค่า";
    }
}

function reformatStatusM9($status)
{
    if ($status == "A") {
        return '<span style="color: green;">เปิดใช้งาน</span>';
    } else if ($status == "I") {
        return '<span style="color: red;">ปิดการใช้งาน</span>';
    } else{
        return "";
    }
}

function reformatStatuStake_moro($status)
{
    if ($status == "Y") {
        return 'ใช่';
    } else if ($status == "N") {
        return 'ไม่';
    }
}

function reformatStatusBenefits($status)
{
    if ($status == 0) {
        return "รอตรวจสอบ";
    } else if ($status == 1) {
        return "ตรวจสอบแล้ว";
    } else if ($status == 2) {
        return "เอกสารไม่ครบ";
    } else if ($status == 3) {
        return "เรียกเงินคืน";
    }
}

function reformatStatusApproval($status)
{
    if ($status == "I") {
        return "กำลังดำเนินการ";
    } else if ($status == "W") {
        return "รอการอนุมัติ";
    } else if ($status == "S") {
        return "หัวหน้าอนุมัติแล้ว";
    } else if ($status == "X") {
        return "หัวหน้าไม่อนุมัติ";
    } else if ($status == "A") {
        return "ผู้ดูแลระบบได้รับการอนุมัติ";
    }
}

function reformatStatusMedical($status)
{
    if ($status == 0) {
        return "รอตรวจสอบเอกสาร";
    } else if ($status == 1) {
        return "ตรวจเอกสารแล้ว";
    } else if ($status == 2) {
        return "รอเอกสารเพิ่มเติม";
    }
}

function reformatCertificate($request_form)
{
    if ($request_form == 5) {
        return "ใบรับรองการเดินทางไปต่างประเทศ";
    } else if ($request_form == 4) {
        return "ใบรับรองการทำงาน";
    } else if ($request_form == 3) {
        return "ใบรับรองเงินเดือน";
    }
}

function generateImage($img, $name = false, $folder = false)
{
    $file        = "";
    $folderPath  = "../../assets/upload/card/";
    $image_parts = explode(";base64,", $img);

    if (strpos($image_parts[0], "image") !== false) {
        if ($folder !== false) {
            $folderPath .= $folder . "/";
            mkdir($folderPath, 0777);
        }

        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type     = $image_type_aux[1];
        $image_base64   = base64_decode($image_parts[1]);
        $file_type = explode("/", getImageSizeFromString($image_base64)["mime"])[1];

        if ($name !== false) {
            $file = $folderPath . $name . '.'.$file_type;
        } else {
            $file = $folderPath . uniqid() . '.'.$file_type;
        }

        file_put_contents($file, $image_base64);

        return $file;
    }
}

function convertImage()
{
    $path   = '../assets/upload/logo-portrait.jpg';
    $type   = pathinfo($path, PATHINFO_EXTENSION);
    $data   = file_get_contents($path);
    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);

    return $base64;
}

function isNotEmpty($str)
{
    if (!empty($str)) {
        return $str;
    }
}

function strToDateSdate($date)
{
    $date = str_replace("/", "-", $date);
    if (preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{4}$/", $date)) {
        return substr($date, 6, 4) . "-" . substr($date, 3, 2) . "-" . substr($date, 0, 2) . " 00:00:00";
    } else {
        return $date;
    }
}

function strToDateEdate($date)
{
    $date = str_replace("/", "-", $date);
    if (preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{4}$/", $date)) {
        return substr($date, 6, 4) . "-" . substr($date, 3, 2) . "-" . substr($date, 0, 2). " 23:59:59";
    } else {
        return $date;
    }
}

function refactorRecommended($status)
{
    if ($status == 0) {
        return "รอการตอบกลับ";
    } else if ($status == 1) {
        return "ส่ง Email";
    } else if ($status == 2) {
        return "ส่ง GSB Cafe'";
    }
}

function refactorReadStatus($status)
{
    if ($status == '1') {
        return "อ่านแล้ว";
    } else {
		return "ยังไม่อ่าน";
	}
}

function refactorEmpAdminStatus($status)
{
    if ($status == null) {
        return "-";
    } else {
		return $status;
	}
}

function reformatStatus_GSB($status)
{
    if ($status == "0") {
        return '<span style="color: green;">เปิดใช้งาน</span>';
    } else if ($status == "1") {
        return '<span style="color: red;">ปิดการใช้งาน</span>';
    } else{
        return "";
    }
}

function printJson($resp)
{
    echo json_encode($resp, JSON_UNESCAPED_UNICODE);
}
