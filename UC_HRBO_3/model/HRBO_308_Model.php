<?php
class HRBO_308_Model
{

    // database connection and table name
    private $conn;

    // object properties
    public $id;
    public $upload_file_name;
    public $upload_file_path;
    public $upload_date;
    public $n_of_rows;
    public $last_updated;

    public $file_id;
    public $Brncode;
    public $AssetMngID;
    public $AssetName;
    public $Fundtype2id;
    public $Fundtype2Name;
    public $Fundshortname;
    public $Fundname;
    public $Amount;
    public $Tradedate;
    public $Employeecode;

    // constructor with $db as database connection
    public function __construct($db)
    {
        $this->conn = $db;
        $this->req_status = 0;
    }

    public function getAll()
    {
        $query = "SELECT t.*  FROM import_FileTrading t ORDER BY t.id desc";
        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        try {
            $stmt->execute();
            return $stmt;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }

    public function getAllSub()
    {
        $query = "SELECT t.*  FROM import_TRTrading t WHERE file_id = ".$this->file_id." ORDER BY t.id desc";
        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        try {
            $stmt->execute();
            return $stmt;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }

    public function create()
    {
        $query = "INSERT INTO import_TRTrading (file_id, Brncode, AssetMngID, AssetName, Fundtype2id, Fundtype2Name, Fundshortname, Fundname, Amount, Tradedate, Employeecode)
        VALUES ({$this->file_id},'{$this->Brncode}',{$this->AssetMngID},'{$this->AssetName}',{$this->Fundtype2id}, '{$this->Fundtype2Name}','{$this->Fundshortname}','{$this->Fundname}',{$this->Amount},'{$this->Tradedate}','{$this->Employeecode}')";

        // prepare query
        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        try {
            $this->conn->beginTransaction();
            $stmt->execute();
            $this->conn->commit();
            return $this->conn->lastInsertId();

        } catch (PDOException $ex) {
            $this->conn->rollback();
            die($ex->getMessage());
        }
    }

    public function createFile()
    {
        $query = "INSERT INTO import_FileTrading (upload_file_name, upload_file_path, upload_date, n_of_rows, remark)
        VALUES ('{$this->upload_file_name}','{$this->upload_file_path}','{$this->upload_date}',{$this->n_of_rows},'{$this->remark}')";

        // prepare query
        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        try {
            $this->conn->beginTransaction();
            $stmt->execute();
            $this->conn->commit();
            return $this->conn->lastInsertId();

        } catch (PDOException $ex) {
            $this->conn->rollback();
            die($ex->getMessage());
        }
    }

    public function updateFile()
    {
        $query = "UPDATE import_FileTrading SET n_of_rows = {$this->n_of_rows} WHERE id = {$this->id}";

        // prepare query
        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        try {
            $this->conn->beginTransaction();
            $stmt->execute();
            $this->conn->commit();
            return $this->conn->lastInsertId();

        } catch (PDOException $ex) {
            $this->conn->rollback();
            die($ex->getMessage());
        }
    }
}
