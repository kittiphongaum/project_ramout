<?php
function validate_date($data, $index, $col) {
    $sp  = explode('-', $data);
    if (checkdate($sp[1], $sp[2], $sp[0])) {
        $resp["status"] = true;
        $resp["record"] = $index;
        $resp["column"] = $col+1;
        $resp["msg"] = "is correct";
        return $resp; 
    }else{
        $resp["status"] = false;
        $resp["record"] = $index;
        $resp["column"] = $col+1;
        $resp["data"] = $data;
        $resp["msg"] = "รูปแบบวันที่ไม่ถูกต้อง";
        return $resp;
    }  
}
function validate_number($data, $index, $col) {
    $data = str_replace(' ', '', $data);
    if (is_numeric($data)||empty($data)) {
        $resp["status"] = true;
        $resp["record"] = $index;
        $resp["column"] = $col+1;
        $resp["msg"] = "is correct";
        return $resp; 
    }else{
        $resp["status"] = false;
        $resp["record"] = $index;
        $resp["column"] = $col+1;
        $resp["data"] = $data;
        $resp["msg"] = "รูปแบบตัวเลขไม่ถูกต้อง";
        return $resp;
    }  
}
function validate_text($data, $index, $col) {
   if (preg_match("/^[0-9*#+]+$/", $data) == 0) {
    $resp["status"] = true;
    $resp["record"] = $index;
    $resp["column"] = $col+1;
    $resp["msg"] = "is correct";
    return $resp; 
   }else{
    $resp["status"] = false;
    $resp["record"] = $index;
    $resp["column"] = $col+1;
    $resp["data"] = $data;
    $resp["msg"] = "ตรวจพบตัวเลข หรือ ตัวอักษรพิเศษ";
    return $resp; 
   }
}