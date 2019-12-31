<?php
$root_dir = dirname(dirname(dirname(__FILE__))) . '/';
define("ROOT_DIR", $root_dir);
require_once '../config/database.php';

class importPositionMasterModel extends importModel
{
    private $result_data;
    private $import_data = array();
    private $table = "db_position_master";
    private $import_data_rows;
    private $imported_count = 0;
    private $import_id;
    private $remark_message;

    public function __construct($url = false)
    {
        parent::__construct();
    }

    public function run()
    {
        $this->getTextToArray();
        $inserted_keys = $this->getDataKeys("import_logs", "url", "completed");
        $this->import_data_rows = count($this->import_data);

        $this->updateLog([
            "import_status" => "running",
            "import_data_rows" => $this->import_data_rows
        ],  $this->import_id);

        // Import
        $import_result = $this->importData($inserted_keys);
        $import_status = $import_result ? "completed" : "failed";

        $this->updateLog([
            "import_status" => $import_status,
            "imported_rows" => $this->imported_count,
            "end_datetime" => date('Y-m-d H:i:s'),
            "remark" => $this->remark_message
        ], $this->import_id);

        return $this->imported_count;
    }

    // Encapsulation
    public function setData($data)
    {
        $this->result_data = $data;
    }

    public function setImportLogId($id)
    {
        $this->import_id = $id;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    // Import data
    private function importData($inserted_keys)
    {
        if (!in_array($this->url, $inserted_keys)) {
            $conn = $this->database->getConnection();

            try {
                $sql = "INSERT INTO " . $this->table;
                $sql .= " (position_id, position_name, position_group_id, position_group_name, position_group_level)";
                $sql .= " VALUES (:position_id, :position_name, :position_group_id, :position_group_name, :position_group_level)";

                $conn->beginTransaction();
                $stmt = $conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

                foreach ($this->import_data as $value) {
                    $stmt->bindParam(":position_id", $value[3]);
                    $stmt->bindParam(":position_name", $value[4]);
                    $stmt->bindParam(":position_group_id", $value[0]);
                    $stmt->bindParam(":position_group_name", $value[1]);
                    $stmt->bindParam(":position_group_level", $value[2]);
                    $stmt->execute();
                    $this->imported_count ++;
                }

                $conn->commit();

            } catch (PDOException $ex) {
                $conn->rollback();
                die($ex->getMessage());
                $this->remark_message = "Import Error";
                return false;
            } finally {
                unset($conn);
            }

            $this->remark_message = "Import Success";
            return true;
        } else {
            $this->remark_message = "$this->url Already imported";
            return true;
        }
    }

    // Build Data
    private function getTextToArray()
    {
        $data               = array();
        $this->result_data  = iconv("tis-620", "utf-8", $this->result_data);
        $split_line         = explode("\n", $this->result_data);

        foreach ($split_line as $row) {
            if(trim($row) == "") {
                break;
            }

            array_push($data, explode("|", $row));
        }

        $this->import_data = $data;
    }
}
