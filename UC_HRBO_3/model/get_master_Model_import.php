<?php
$root_dir = dirname(dirname(dirname(__FILE__))) . '/';
require_once $root_dir . 'config/database.php';

class get_master_Model_import
{
    // database connection and table name
    private $database;
    private $table = "trans_provident_fund_imp";
    private $page_size = 10;
    private $filter = false;

    public function __construct()
    {
        $this->database = new Database();
    }

    public function getAll($page=1,$search,$col,$dir)
    {
        return $this->get(
                    $page,
                    $search["value"],
                    $col,
                    $dir
                );
    }
    public function get( $page = false, $search, $col1, $dir1)
    {

        $conn = $this->database->getConnection();
        $sql = "SELECT *  FROM trans_provident_fund_imp WHERE 1=1 ";
        
         if ($search !="" && $search!= 0) {
            $sql .= " AND emp_id like '%" .$search . "%' ";
        }

        switch ($col1) {
            case "1":
                $sql .= " ORDER BY emp_id " . $dir1;
                break;
            case "2":
                $sql .= " ORDER BY saving_amt " . $dir1;
                break;
            case "3":
                $sql .= " ORDER BY saving_benefit " . $dir1;
                break;
            case "4":
                $sql .= " ORDER BY supporting_amt " . $dir1;
                break;
            case "5":
                $sql .= " ORDER BY supporting_benefit " . $dir1;
                break;
            case "6":
                $sql .= " ORDER BY initiate_amt " . $dir1;
                break;
            case "7":
                $sql .= " ORDER BY initiate_benefit " . $dir1;
                break;
            case "8":
                $sql .= " ORDER BY initiate_result_1 " . $dir1;
                break;
            default:
                $sql .= " ORDER BY emp_id " . $dir1;
        }
        $sql .= " OFFSET " . $this->page_size ." * (" . $page . "-1) ROWS";
        $sql .= " FETCH NEXT " . $this->page_size ." ROWS ONLY";
      
        $stmt = $conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        
        try {
            $stmt->execute();
            return $stmt;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        } finally {
            unset($conn);
        }
    }

    public function setPageSize($size)
    {
        $this->page_size = $size;
    }

    public function setFilter($filter)
    {
        if (is_array($filter)) {
            $this->filter = $filter;
        }
    }
   
    public function count()
    {
        $sql = "SELECT count(emp_id) FROM trans_provident_fund_imp";
         if (is_array($this->filter)) {
            $sql .= " WHERE 1=1";
        }

        $conn = $this->database->getConnection();
        $stmt = $conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        try {
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                return $stmt->fetchAll(PDO::FETCH_COLUMN, 0)[0];
            }
        } catch  (PDOException $ex) {
            die($ex->getMessage());
        } finally {
            unset($conn);
        }
    }

  
}
