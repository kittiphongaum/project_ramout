<?php

class course_master_Model_import
{

    // database connection and table name
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }
    public function delTable(){

        $query = "TRUNCATE TABLE trans_provident_fund_imp ";
        $stmt  = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
    public function delTableSave(){

        $query = "TRUNCATE TABLE trans_provident_fund_imp_source ";
        $stmt  = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
    public function sumTable(){

        $query = "INSERT INTO trans_provident_fund_imp
        SELECT 
            emp_id,
            SUM(saving_amt) AS saving_amt, 
            SUM(saving_benefit) AS saving_benefit, 
            SUM(supporting_amt) AS supporting_amt, 
            SUM(supporting_benefit) AS supporting_benefit, 
            SUM(initiate_amt) AS initiate_amt,
            SUM(initiate_benefit) AS initiate_benefit,
            SUM(initiate_result_1) AS initiate_result_1,
            SUM(initiate_result_2) AS initiate_result_2,
            SUM(initiate_result_3) AS initiate_result_3,
            CONVERT(VARCHAR, getdate(), 121) AS created_date
        FROM 
            trans_provident_fund_imp_source 
        GROUP BY emp_id";
        $stmt  = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
    public function getAll()
    {
        $query = "SELECT *  FROM trans_provident_fund_imp ";
        $stmt  = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        try {
            $stmt->execute();
            return $stmt;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }

    public function create()   
    {
       $query = "INSERT INTO trans_provident_fund_imp_source (
                                    emp_id, 
                                    saving_amt, 
                                    saving_benefit, 
                                    supporting_amt, 
                                    supporting_benefit, 
                                    initiate_amt,
                                    initiate_benefit,
                                    initiate_result_1,
                                    initiate_result_2,
                                    initiate_result_3,
                                    created_date
                                ) VALUES (
                                    " . $this->emp_id . ",
                                    " . $this->saving_amt . ",
                                    " . $this->saving_benefit . ",
                                    " . $this->supporting_amt . ",
                                    " . $this->supporting_benefit . ",
                                    " . $this->initiate_amt . ",
                                    " . $this->initiate_benefit . ",
                                    " . $this->initiate_result_1 . ",
                                    0,
                                    0,
                                    '" . $this->created_date . "'
                                 )";

        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
}
