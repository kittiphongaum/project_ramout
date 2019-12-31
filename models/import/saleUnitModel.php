<?php
$root_dir = dirname(dirname(dirname(__FILE__))) . '/';
define("ROOT_DIR", $root_dir);
require_once '../config/database.php';

class saleUnitModel extends importModel
{
    private $result_data;
    private $import_data = array();
    private $table = "import_TRTrading";
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
                $sql .= " (
                            AssetMngID,
                            AssetName,
                            Fundtype2id,
                            Fundtype2Name,
                            Fundname,
                            Fundshortname,
                            Amount,
                            Tradedate,
                            Employeecode,
                            Brncode,
                            file_id
                        ) VALUES (
                            :asset_management_id,
                            :asset_management_name,
                            :unit_type_id,
                            :unit_type,
                            :unit_name,
                            :short_unit_name,
                            :amount,
                            :buy_date,
                            :emp_id,
                            :cost_center,
                            :log_id
                        )";

                $conn->beginTransaction();
                $stmt = $conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

                foreach ($this->import_data as $value) {
                    $stmt->bindParam(":cost_center", $value[0]);
                    $stmt->bindParam(":asset_management_id", $value[1]);
                    $stmt->bindParam(":asset_management_name", $value[2]);
                    $stmt->bindParam(":unit_type_id", $value[3]);
                    $stmt->bindParam(":unit_type", $value[4]);
                    $stmt->bindParam(":short_unit_name", $value[5]);
                    $stmt->bindParam(":unit_name", $value[6]);
                    $stmt->bindParam(":amount", $value[7]);
                    $stmt->bindParam(":buy_date", $value[8]);
                    $stmt->bindParam(":emp_id", $value[9]);
                    $stmt->bindParam(":log_id", $this->import_id);
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
        $data       = array();
        $split_line = explode("\n", $this->result_data);
        $header     = explode("|", array_shift($split_line));

        foreach ($split_line as $row) {
            if ($row[0] == "T") {
                $total = explode("|", $row);
                $total = $total[1];
                break;
            }

            array_push($data, explode("|", $row));
        }

        $this->import_data = $data;
    }
}
