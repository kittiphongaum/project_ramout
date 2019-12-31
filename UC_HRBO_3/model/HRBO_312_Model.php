<?php
class HRBO_312_Model
{
     // database connection and table name
     private $conn;

     // object properties
     public $created_date;
     public $config_type;
     public $key_table;
     public $greater_than;
     public $title;
     public $less_than;
     public $amount;
     public $key_code;
     public $key_name;
     public $variable_id;
     public $config_id;
     public $title_config;

    public function __construct($db)
    {
        $this->conn  = $db;
        $this->config_type = 'rental_house';
    }

    public function getAll()
    {
        $query = "SELECT config.*,
                    variable.key_name,
                    variable.id AS variableId,
                    variable.title AS variableTitle 
                    FROM db_application_variable_master variable 
                    LEFT JOIN db_application_config_master config ON config.key_code = variable.key_code 
                    WHERE config.config_type = 'provident_fund'
                    ORDER BY config.created_date DESC, config.updated_date DESC";

        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        try {
            $stmt->execute();
            return $stmt;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }

    function insert_config_Master()
    {

        // query to insert record
          $query = "INSERT INTO db_application_config_master (config_type,title,key_code,key_table,amount,less_than,greater_than,created_date) VALUES(?,?,?,?,?,?,?,?)";
  
          // prepare query
         $stmt = $this->conn->prepare($query);
  
          // sanitize
          $this->config_type=htmlspecialchars(strip_tags($this->config_type));
          $this->title_config=htmlspecialchars(strip_tags($this->title_config));
          $this->key_code=htmlspecialchars(strip_tags($this->key_code));
          $this->key_table=htmlspecialchars(strip_tags($this->key_table));
          $this->amount=htmlspecialchars(strip_tags($this->amount));
          $this->less_than=htmlspecialchars(strip_tags($this->less_than));
          $this->greater_than=htmlspecialchars(strip_tags($this->greater_than));
          $this->created_date=htmlspecialchars(strip_tags($this->created_date));
  
          // bind values
          $stmt->bindParam(1, $this->config_type);
          $stmt->bindParam(2, $this->title_config);
          $stmt->bindParam(3, $this->key_code);
          $stmt->bindParam(4, $this->key_table);
          $stmt->bindParam(5, $this->amount);
          $stmt->bindParam(6, $this->less_than);
          $stmt->bindParam(7, $this->greater_than);
          $stmt->bindParam(8, $this->created_date);
  
          // execute query
          if($stmt->execute()){
              return true;
          }
  
          return false;
    }

    function insert_variable_Master()
    {

        // query to insert record
          $query = "INSERT INTO db_application_variable_master (category_type,key_code,key_name,active_status,created_date,title) VALUES('PROVIDENT_FUND',?,?,'A',?,?)";
  
          // prepare query
         $stmt = $this->conn->prepare($query);
  
          // sanitize
          $this->config_type=htmlspecialchars(strip_tags($this->key_name));
          $this->title=htmlspecialchars(strip_tags($this->title));
          $this->key_code=htmlspecialchars(strip_tags($this->key_code));
          $this->created_date=htmlspecialchars(strip_tags($this->created_date));
  
          // bind values
          $stmt->bindParam(1, $this->key_code);
          $stmt->bindParam(2, $this->key_name);
          $stmt->bindParam(3, $this->created_date);
          $stmt->bindParam(4, $this->title);

          // execute query
          if($stmt->execute()){
              return true;
          }
  
          return false;
  
    }

    public function select_money()
    {
        $query ="SELECT config.*,
                    variable.key_name,
                    variable.id AS variableId,
                    variable.title AS variableTitle 
                    FROM db_application_variable_master variable 
                    LEFT JOIN db_application_config_master config ON config.key_code = variable.key_code 
                    WHERE config.amount = :amount  
                    OR config.less_than = :less_than  
                    AND config.config_type ='provident_fund'";
   
        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        $this->amount=htmlspecialchars(strip_tags($this->amount));
        $this->less_than=htmlspecialchars(strip_tags($this->less_than));
        $this->greater_than=htmlspecialchars(strip_tags($this->greater_than));
        
        $stmt->bindParam(":amount", $this->amount);
        $stmt->bindParam(":less_than", $this->less_than);

        try {
            $stmt->execute();
            return $stmt;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }

    public function OneAll()
    {
        $query = "SELECT config.*,
        variable.key_name AS  variableKey_name,
        variable.id AS variableId,
        variable.title AS variableTitle 
        FROM db_application_variable_master variable 
        LEFT JOIN db_application_config_master config ON config.key_code = variable.key_code 
        WHERE config.id = :id  AND variable.id =:variable_id ";

        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":variable_id", $this->variable_id);

        try {
            $stmt->execute();
            $num = $stmt->rowCount();

            if ($num == 1) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                // set values to object properties
                $this->greater_than   = $row['greater_than'];
                $this->title          = $row['variableTitle'];
                $this->less_than      = $row['less_than'];
                $this->amount         = $row['amount'];
                $this->key_code       = $row['key_code'];
                $this->key_name       = $row['variableKey_name'];
                $this->config_id      = $row['id'];
                $this->variable_id    = $row['variableId'];

            } else {
                    return $stmt;
            }
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }

    
    function update_config_Master()
    {

        // query to update record
          $query = "UPDATE db_application_config_master SET amount = ?,less_than = ?,greater_than = ?, updated_date = ? WHERE id = ? ";
          // prepare query
         $stmt = $this->conn->prepare($query);
  
          // sanitize
          $this->amount=htmlspecialchars(strip_tags($this->amount));
          $this->less_than=htmlspecialchars(strip_tags($this->less_than));
          $this->greater_than=htmlspecialchars(strip_tags($this->greater_than));
          $this->updated_date=htmlspecialchars(strip_tags($this->updated_date));
          $this->config_id=htmlspecialchars(strip_tags($this->config_id));
  
          // bind values
          $stmt->bindParam(1, $this->amount);
          $stmt->bindParam(2, $this->less_than);
          $stmt->bindParam(3, $this->greater_than);
          $stmt->bindParam(4, $this->updated_date);
          $stmt->bindParam(5, $this->config_id);
  
          // execute query
          if($stmt->execute()){
              return true;
          }
  
          return false;
    }

    function update_variable_Master()
    {

        // query to update record
          $query = "UPDATE db_application_variable_master SET  key_name = ?, title = ?,updated_date = ? WHERE id = ? ";
          // prepare query
         $stmt = $this->conn->prepare($query);
  
          // sanitize
          $this->key_name=htmlspecialchars(strip_tags($this->key_name));
          $this->title=htmlspecialchars(strip_tags($this->title));
          $this->updated_date=htmlspecialchars(strip_tags($this->updated_date));
          $this->variable_id=htmlspecialchars(strip_tags($this->variable_id));
          // bind values
          $stmt->bindParam(1, $this->key_name);
          $stmt->bindParam(2, $this->title);
          $stmt->bindParam(3, $this->updated_date);
          $stmt->bindParam(4, $this->variable_id);
          // execute query
          if($stmt->execute()){
              return true;
          }
  
          return false;
  
    }

    public function searching()
    {
        $query = " SELECT *
                        FROM db_application_config_master 
                        WHERE id != :id 
                        AND amount = :amount
                        AND config_type = 'provident_fund' ";

        $stmt = $this->conn->prepare($query, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        $this->config_id=htmlspecialchars(strip_tags($this->config_id));
        $this->amount=htmlspecialchars(strip_tags($this->amount));

        // bind values
        $stmt->bindParam(":id", $this->config_id);
        $stmt->bindParam(":amount", $this->amount);
        
        try {
            $stmt->execute();
            return $stmt;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }
}
