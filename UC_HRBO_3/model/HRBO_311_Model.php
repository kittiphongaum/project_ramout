<?php
class HRBO_311_Model
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
    public $act_emp_id;
    public $as_of_date;
    public $loan_emp_id;
    public $loan_id_card;
    public $loan_prefix;
    public $loan_name;
    public $loan_amount;
    public $loan_remain;
    public $gua_prefix;
    public $gua_firstname;
    public $gua_lastname;
    public $gua_emp_id;
    public $gua_id_card;

    // constructor with $db as database connection
    public function __construct($db)
    {
        $this->conn = $db;
        $this->req_status = 0;
    }

    public function getAll()
    {
        $query = "SELECT t.*  FROM import_FileGuaranteePerson t ORDER BY t.id desc";
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
        $query = "SELECT * FROM import_GuaranteePerson ORDER BY id desc";
        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        try {
            $stmt->execute();
            return $stmt;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }

    public function getSub()
    {
        $query = "SELECT t.* ,
        v.emp_code AS emp_id, v.emp_name,  v.pos_code AS pos_id,
        v.pos_name, v.dept_code AS dept_id, v.dept_name, v.parent_name
        FROM import_GuaranteePerson t
        LEFT JOIN v_org_chart v ON (t.gua_emp_id = CAST(v.emp_code as int))
        WHERE file_id = ".$this->file_id." ORDER BY t.id asc";
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
         $query = "INSERT INTO import_GuaranteePerson (file_id, as_of_date, loan_emp_id, loan_id_card, loan_prefix, loan_name, loan_amount, loan_remain, gua_prefix, gua_firstname, gua_lastname, gua_emp_id, gua_id_card, act_emp_id)
        VALUES ({$this->file_id},'{$this->as_of_date}','{$this->loan_emp_id}','{$this->loan_id_card}','{$this->loan_prefix}','{$this->loan_name}',{$this->loan_amount},{$this->loan_remain},'{$this->gua_prefix}','{$this->gua_firstname}','{$this->gua_lastname}','{$this->gua_emp_id}','{$this->gua_id_card}',{$this->act_emp_id})";

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

    public function deleteByFileId()
    {
        $query = "SELECT upload_file_path FROM import_FileGuaranteePerson WHERE id = {$this->file_id}";
        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (isset($row["upload_file_path"])) {
            unlink($row["upload_file_path"]);
        }

        $query = "DELETE FROM import_FileGuaranteePerson WHERE id = {$this->file_id}";
        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        try {
            $this->conn->beginTransaction();
            $stmt->execute();
            $this->conn->commit();
            $num = $stmt->rowCount();
            if ($num > 0) {
                $query = "SELECT count(*) as num_rows FROM import_GuaranteePerson WHERE file_id = {$this->file_id}";
                $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $num_rows = $row["num_rows"];
                if ($num_rows > 0) {

                    $query = "DELETE FROM import_GuaranteePerson WHERE file_id = {$this->file_id}";
                    $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
                    $this->conn->beginTransaction();
                    $stmt->execute();
                    $this->conn->commit();
                    $deleted = $stmt->rowCount();
                    if ($deleted == $num_rows) {
                        return $deleted;
                    } else {
                        return 0;
                    }

                } else {
                    return 1;
                }
            }
            else
            {
                return 1;
            }

        } catch (PDOException $ex) {
            $this->conn->rollback();
            die($ex->getMessage());
        }
    }

    public function createFile()
    {
        $query = "INSERT INTO import_FileGuaranteePerson (upload_file_name, upload_file_path, upload_date, n_of_rows, remark)
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
        $query = "UPDATE import_FileGuaranteePerson SET n_of_rows = {$this->n_of_rows} WHERE id = {$this->id}";

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
