<?php
session_start();
require_once 'config/database.php';
require_once 'models/userModel.php';

if (isset($_POST["submit"]) && isset($_POST["username"]) && isset($_POST["password"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    //get database connection
    $database = new Database();
    $db = $database->getConnection();

    // // //prepare object
    $userModel = new userModel($db);
    $userModel->username = $username;
    $userModel->password = $password;
    $userModel->get();
    if (($userModel->id > 0))
    {
        // var_dump($userModel);
        $_SESSION["user_id"] = $userModel->id;
        $_SESSION["username"] = $userModel->username;
        $_SESSION["emp_name"] = $userModel->emp_name;
        header("location: /BO-HR/index.php");
    }
    else
    {
       echo "<script>alert('ชื่อผู้เข้าใช้หรือรหัสผ่านไม่ถูกต้อง');location.href='login.php?e=mismatch'</script>";
    }
}

?>