<?php
class HRBO_320_Model
{
    // database connection and table name
    public $Category_code ="LOAN_PARAM";
    public $Category_type ="LOAN_PARAM";
    private $conn;
    public $Category_desc;
    public $created_date;
    public $req_status_list;
    public $key_code;
    public $key_code2;
    public $active_status;
    public $active_status2;
    public $Regidauto;
    public $ord;
    public $id;
    public $status;
    public $key;      
    public $subgroup;     
    public $claim_limit;
    public $subgroup_name;
    public $created_by;
    public $item_amount_bat;
    public $item_amount_mamy;
    public $period_amount;
    public $guarantor_amount;
    public $guarator_level;
    public $byPass;

    public function __construct($db)
    {
        $this->conn = $db;

    }
    public function getAll()
    {

        $query = "SELECT * FROM myhr_req_funhdloan_item";

        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        try {
            $stmt->execute();
            return $stmt;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }

    }

    public function get()
    {

        $query = "SELECT * FROM myhr_req_funhdloan_item WHERE item_value = :category";

        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        $stmt->bindParam(":category", $this->category);

        try {
            $stmt->execute();
            return $stmt;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }

    }

    public function check_duplicate()
    {

        $query = "SELECT * FROM myhr_req_funhdloan_item WHERE item_value = :item_desc";

        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        $stmt->bindParam(":item_desc", $this->item_desc);

        try {
            $stmt->execute();
            $num = $stmt->rowCount();
            return $num;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }

    public function create()
    {
        $query = "
            INSERT INTO myhr_req_funhdloan_item (
                id,
                category,
                actvie_flag,
                item_desc,
                item_value,
                item_value2,
                item_amount_bat,
                item_amount_mamy,
                loan_status,
                period_amount,
                guarantor_amount,
                guarator_level,
                byPass
            )
            VALUES
            (
                :id,
                :category,
                :actvie_flag,
                :item_desc,
                :item_value,
                :item_value2,
                :item_amount_bat,
                :item_amount_mamy,
                :loan_status,
                :period_amount,
                :guarantor_amount,
                :guarator_level,
                :byPass

            );";
        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":category", $this->category);
        $stmt->bindParam(":actvie_flag", $this->actvie_flag);
        $stmt->bindParam(":item_desc", $this->item_desc);
        $stmt->bindParam(":item_value", $this->item_value);
        $stmt->bindParam(":item_value2", $this->item_value2);
        $stmt->bindParam(":item_amount_bat", $this->item_amount_bat);
        $stmt->bindParam(":item_amount_mamy", $this->item_amount_mamy);
        $stmt->bindParam(":loan_status", $this->loan_status);
        $stmt->bindParam(":period_amount", $this->period_amount);
        $stmt->bindParam(":guarantor_amount", $this->guarantor_amount);
        $stmt->bindParam(":guarator_level", $this->guarator_level);
        $stmt->bindParam(":byPass", $this->byPass);

        try {
            $this->conn->beginTransaction();
            $stmt->execute();
            $this->conn->commit();
            return true;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }

    public function update()
    {
        $query = "UPDATE myhr_req_funhdloan_item
        SET
            item_amount_bat = COALESCE(NULLIF(:item_amount_bat, ''), item_amount_bat),
            item_amount_mamy = COALESCE(NULLIF(:item_amount_mamy, ''), item_amount_mamy),
            loan_status = COALESCE(NULLIF(:loan_status, ''), loan_status),
            period_amount = COALESCE(NULLIF(:period_amount, ''), period_amount),
            guarantor_amount = COALESCE(NULLIF(:guarantor_amount, ''), guarantor_amount),
            guarator_level = COALESCE(NULLIF(:guarator_level, ''), guarator_level),
            byPass = COALESCE(NULLIF(:byPass, ''), byPass)

        WHERE
         item_value = :category";

        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        $stmt->bindParam(":item_amount_bat", $this->item_amount_bat);
        $stmt->bindParam(":item_amount_mamy", $this->item_amount_mamy);
        $stmt->bindParam(":loan_status", $this->loan_status);
        $stmt->bindParam(":category", $this->category);
        $stmt->bindParam(":period_amount", $this->period_amount);
        $stmt->bindParam(":guarantor_amount", $this->guarantor_amount);
        $stmt->bindParam(":guarator_level", $this->guarator_level);
        $stmt->bindParam(":byPass", $this->byPass);
        
        try {
            $this->conn->beginTransaction();
            $stmt->execute();
            $this->conn->commit();
            return $stmt->rowCount();
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }

    public function delete()
    {
        $query = "DELETE FROM myhr_req_funhdloan_item WHERE item_value = :item_desc";
        $stmt  = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        $stmt->bindParam(":item_desc", $this->category);

        try {
            $this->conn->beginTransaction();
            $stmt->execute();
            $this->conn->commit();
            return $stmt->rowCount();

        } catch (PDOException $ex) {
            $this->conn->rollback();
            die($ex->getMessage());
        }
    }
    public function getBorrower()
    {
        $query = " SELECT * FROM db_application_variable_master WHERE  category_type = ? ORDER BY  created_date DESC , updated_date DESC";
        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        $stmt->bindParam(1, $this->Category_code);

        try {
            $stmt->execute();
            return $stmt;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }
    public function getloanPeamByid($id)
    {
        
        $query = " SELECT * FROM db_application_variable_master WHERE  category_type = ? " ;
         if ($id !="") {
            $query .=" AND id= $id";
        }
        $query .=" ORDER BY  created_date DESC , updated_date DESC";
        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        $stmt->bindParam(1, $this->Category_code);

        try {
            $stmt->execute();
            return $stmt;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }

    public function createLoan()
    {
        $query = "INSERT INTO db_application_variable_master (category_type,key_code,key_name,ord,active_status,title,created_date) VALUES(?,?,?,?,?,?,?)";

        // prepare query
        $stmt = $this->conn->prepare($query);
        // bind values

        $this->Category_type     = htmlspecialchars(strip_tags($this->Category_type));
        $this->key_code          = htmlspecialchars(strip_tags($this->key_code));
        $this->key_name          = htmlspecialchars(strip_tags($this->key_name));
        $this->ord               = htmlspecialchars(strip_tags($this->ord));
        $this->status            = htmlspecialchars(strip_tags($this->status));
        $this->title             = htmlspecialchars(strip_tags($this->title));
        $this->created_date      = htmlspecialchars(strip_tags($this->created_date));

        $stmt->bindParam(1, $this->Category_type);
        $stmt->bindParam(2, $this->key_code);
        $stmt->bindParam(3, $this->key_name);
        $stmt->bindParam(4, $this->ord);
        $stmt->bindParam(5, $this->status);
        $stmt->bindParam(6, $this->title);
        $stmt->bindParam(7, $this->created_date);

        // execute query
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    public function config_createLoan()
    {
        // query to insert record
        $query = "INSERT INTO db_application_config_master (amount,less_than,greater_than,title,key_code,config_type,key_table,created_date) VALUES(0,0,0,'Provident fund',?,'provident_fund','db_application_variable_master',?)";

        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->key_code = htmlspecialchars(strip_tags($this->key_code));
        $this->created_date = htmlspecialchars(strip_tags($this->created_date));

        // bind values
        $stmt->bindParam(1, $this->key_code);
        $stmt->bindParam(2, $this->created_date);
        // execute query
        if($stmt->execute()){
            return true;
        }
        return false;
    }
    public function searchLoan($key)
    {
        $query = " SELECT * FROM db_application_variable_master WHERE  category_type = ? AND key_code ='".$key."' ORDER BY  created_date DESC , updated_date DESC";
        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        $stmt->bindParam(1, $this->Category_code);

        try {
            $stmt->execute();
            $num = $stmt->rowCount();
            if ($num == 1) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $this->key_code2 = $row['id'];
            } else {
                return $stmt;
            }
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }
    public function updateLoan()
    {
         $query = "UPDATE db_application_variable_master SET key_name = ?, updated_date = ?, active_status = ?, ord = ?,title = ? WHERE key_code = ?";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // bind values
        $stmt->bindParam(1, $this->key_name);
        $stmt->bindParam(2, $this->updated_date);
        $stmt->bindParam(3, $this->status);
        $stmt->bindParam(4, $this->ord);
        $stmt->bindParam(5, $this->title);
        $stmt->bindParam(6, $this->key_code);
      
        // execute the query
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    public function guarantor()
    {
        $query = "SELECT * FROM guarantee_person_limit ";

        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        try {
            $stmt->execute();
            return $stmt;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }

    }

    public function guarantorById($id)
    {
        $query = "SELECT * FROM guarantee_person_limit  WHERE guarantor_id = $id ";

        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        try {
            $stmt->execute();
            return $stmt;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }

    public function insertsubgroup()
    {
        $query = "INSERT INTO guarantee_person_limit (subgroup,subgroup_name,claim_limit,created_by,active_status,title,created_date) VALUES (?,?,?,?,?,?,?)";
           // prepare query
        $stmt = $this->conn->prepare($query);

        $this->subgroup         = htmlspecialchars(strip_tags($this->subgroup));
        $this->subgroup_name    = htmlspecialchars(strip_tags($this->subgroup_name));
        $this->claim_limit      = htmlspecialchars(strip_tags($this->claim_limit));
        $this->created_by       = htmlspecialchars(strip_tags($this->created_by));
        $this->status           = htmlspecialchars(strip_tags($this->status));
        $this->title            = htmlspecialchars(strip_tags($this->title));
        $this->created_date     = htmlspecialchars(strip_tags($this->created_date));

        $stmt->bindParam(1, $this->subgroup);
        $stmt->bindParam(2, $this->subgroup_name);
        $stmt->bindParam(3, $this->claim_limit);
        $stmt->bindParam(4, $this->created_by);
        $stmt->bindParam(5, $this->status);
        $stmt->bindParam(6, $this->title);
        $stmt->bindParam(7, $this->created_date);
     
        // execute query
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }


    public function updatesubGroup($group_id)
    {
         $query = " UPDATE guarantee_person_limit SET subgroup = ?, subgroup_name =? ,claim_limit= ? ,created_by= ?, active_status = ?,title = ?, updated_date = ? WHERE guarantor_id = $group_id";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // bind values
        $stmt->bindParam(1, $this->subgroup);
        $stmt->bindParam(2, $this->subgroup_name);
        $stmt->bindParam(3, $this->claim_limit);
        $stmt->bindParam(4, $this->created_by);
        $stmt->bindParam(5, $this->status);
        $stmt->bindParam(6, $this->title);
        $stmt->bindParam(7, $this->updated_date);
      
        // execute the query
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    public function updateStatus($group_id)
    {
         $query = " UPDATE guarantee_person_limit SET  active_status = 'I'  WHERE guarantor_id != $group_id";

        // prepare query statement
        $stmt = $this->conn->prepare($query);
        // bind values
        // $stmt->bindParam(5, $this->status);
        // execute the query
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    public function getmaster()
    {
        $query = " SELECT * FROM myhr_req_funhdloan_master  ORDER BY subgroup ASC";
        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        try {
            $stmt->execute();
            return $stmt;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }
    public function getmasterByid($subgroup)
    {
        $query = " SELECT * FROM myhr_req_funhdloan_master  WHERE subgroup ='$subgroup'";
        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        try {
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }

}
