<?php
$root_dir = dirname(dirname(__FILE__)) . '/';
define("ROOT_PATH", $root_dir);
require_once $root_dir . 'config/database.php';

// set limit 300 seconds = 5 minutes
ini_set('max_execution_time', 300);

// use GuzzleHttp\Psr7\Request;
define('API_HOST_IMPORT','http://10.22.50.146:8099');
define('API_PATH_IMPORT',API_HOST_IMPORT.'/getfile');
define('API_PATH_IMPORT_P1',API_HOST_IMPORT.'/async/category');
define('API_PATH_IMPORT_P2','/period/11/flag/o/type/s');
define('API_PATH_STATUS_IMPORT',API_HOST_IMPORT.'/sync-data/category');

class importModel
{
    // database connection and table name
    protected $database;
    private $table = "import_logs";

    // Variables
    protected $url;
    protected $type;
    private $import_id;

    //Define Model name of import
    private $model_name;

    //Defind sftp var
    private $sftp;

    public function __construct($type = false, $url = false)
    {
        $this->database = new Database();
        $this->type = $type;
        $this->url = $url;
        $this->setDefineVar();
    }

    public function getAll()
    {
        return $this->get();
    }

    public function truncate($table = false)
    {
        $conn = $this->database->getConnection();
        $sql = "TRUNCATE TABLE $table";
        $stmt = $conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        try {
            $stmt->execute();
            return $stmt;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
        finally {
            unset($conn);
        }
    }

    public function get($id = false)
    {
        // $conn = $this->database->getConnection();
        // $sql = "SELECT
        //             type,
        //             url,
        //             import_status,
        //             start_datetime,
        //             end_datetime,
        //             imported_rows,
        //             remark
        //         FROM " . $this->table;

        // $sql .= " WHERE type='" . $this->type . "'";

        // if ($id) {
        //     $sql .= " AND id = '" . $id . "'";
        // }

        // $sql .= " ORDER BY id DESC";

        // $stmt = $conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

            if ($this->type == "saleunit"){
                $apiurl = API_PATH_STATUS_IMPORT . '/SALE_UNIT_TRUST';
            }
            if ($this->type == "seniority"){
                $apiurl = API_PATH_STATUS_IMPORT . '/EMPLOYEE_SENIORITY';
            }
            if ($this->type == "positions") {
                $apiurl = API_PATH_STATUS_IMPORT . '/AVALIABLE_POSITION';
            }
            // return $apiurl;
            //call api
            $curl = curl_init();
            curl_setopt($curl,CURLOPT_URL, $apiurl);
            // curl_setopt($curl,CURLOPT_HTTPHEADER, array("accept: application/json","authorization: Bearer $token"));
            curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl,CURLOPT_VERBOSE, true);

            $response = curl_exec($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $err = curl_error($curl);

            curl_close($curl);

            // $res = $response;
            // $data = $response['data'];
            // $response = json_decode($response, TRUE);
            // return $response;

            if($httpcode == 200){
                //do something
                $response = json_decode($response, TRUE);
                $stmt = $response;
            }else{
                $stmt = null;
            }

            // $result = array(
            //     "response" => $response,
            //     "httpcode" => $httpcode,
            //     "err" => $err
            // );
            // return $result;

        try {
            // $stmt->execute();
            return $stmt;
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
        finally {
            unset($conn);
        }
    }

    public function createLog()
    {
        // Check log status
        if (!in_array($this->checkLogStatus(), array("ready", "completed", "failed"))) {
            return false;
        }

        $type_text = $this->typeToText($this->type);
        if (!$this->typeToText($this->type)) {
            return false;
        }

        // Insert log status Starting
        $sql = "INSERT INTO import_logs
            (
                type,
                url,
                start_datetime,
                import_status
            )
            VALUES
            (
                '" . $this->type . "',
                '" . urldecode($this->url) . "',
                '" . date('Y-m-d H:i:s') . "',
                'starting'
            )
        ";

        $conn = $this->database->getConnection();
        $stmt = $conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        try {
            $conn->beginTransaction();
            $stmt->execute();
            $conn->commit();
            $import_id = $conn->lastInsertId();
            $this->import_id = $import_id;

            return $import_id;
        } catch (PDOException $ex) {
            $conn->rollback();
            die($ex->getMessage());
        }
        finally {
            unset($conn);
        }
    }

    public function execute()
    {
        $model_name = $this->getImportModelName();
        if ($model_name) {

            //check model name for specify url
            if ($model_name == "saleUnitModel"){
                // $apiurl = API_PATH_IMPORT . '/sale-unit-trust/period/1/flag/o';
                $apiurl = API_PATH_IMPORT_P1 . '/SALE_UNIT_TRUST' . API_PATH_IMPORT_P2;
            }
            if ($model_name == "employeeSeniorityModel") {
                // $apiurl = API_PATH_IMPORT . '/employee-seniority';
                $apiurl = API_PATH_IMPORT_P1 . '/EMPLOYEE_SENIORITY' . API_PATH_IMPORT_P2;
            }
            if ($model_name == "availablePositionModel"){
                // $apiurl = API_PATH_IMPORT . '/avaliable-position';
                $apiurl = API_PATH_IMPORT_P1 . '/AVALIABLE_POSITION' . API_PATH_IMPORT_P2;
            }

            //call api
            $curl = curl_init();
            curl_setopt($curl,CURLOPT_URL, $apiurl);
            curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl,CURLOPT_VERBOSE, true);

            $response = curl_exec($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $err = curl_error($curl);

            curl_close($curl);
            
            if($httpcode == 200){
                //do something
                $result = array(
                    "response" => $response,
                    "httpcode" => $httpcode,
                    "err" => $err
                );
            }

            return $result;

        } else {
            return [
                "error_message" => "import type" . $this->type . " not found"
            ];
        }
    }

    protected function getDataKeys($table = false, $key = "objectId", $filter = false)
    {
        if (!$table) return false;

        $sql = "SELECT " . $key . " FROM " . $table;
        if ($filter) $sql .= " WHERE import_status = '$filter'";
        $conn = $this->database->getConnection();
        $stmt = $conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        try {
            $stmt->execute();
            $row_count = $stmt->rowCount();
            if ($row_count > 0) {
                return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
            } else {
                return [];
            }
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
        finally {
            unset($conn);
        }
    }

    protected function cleanImportedData($table = false)
    {
        if (!$table) return false;

        $sql = "TRUNCATE TABLE $table";
        $conn = $this->database->getConnection();
        $stmt = $conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        try {
            $stmt->execute();
        } catch (PDOException $ex) {
            die($ex->getMessage());
        } finally {
            unset($conn);
        }
    }

    protected function updateLog($data, $id = null)
    {
        $import_id = $id ? $id : $this->import_id;

        if (!empty($import_id) && count($data) > 0) {
            $fields_values = [];
            foreach ($data as $key => $value) {
                array_push($fields_values, $key . " = '" . $value . "'");
            }

            $sql = "UPDATE import_logs SET " . implode($fields_values, ", ") . " WHERE id = " . $import_id;
            $conn = $this->database->getConnection();

            $stmt = $conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

            try {
                $conn->beginTransaction();
                $stmt->execute();
                $conn->commit();
                return $conn->lastInsertId();
            } catch (PDOException $ex) {
                $conn->rollback();
                die($ex->getMessage());
            }
            finally {
                unset($conn);
            }
        }
    }

    private function typeToText($type)
    {
        $text = false;
        $type = strtolower($type);

        switch ($type) {
            case "positions":
                $text = "import_available_positions";
                break;
            case "seniority":
                $text = "import_seniority";
                break;
            case "saleunit":
                $text = "import_sale_unit_thrust";
                break;
            case "positions_master":
                $text = "import_positions_master";
                break;
        }

        return $text;
    }

    private function checkLogStatus()
    {
        $sql = "SELECT top(1) import_status FROM import_logs WHERE type='" . $this->type . "' ORDER BY id DESC";
        $conn = $this->database->getConnection();
        $stmt = $conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

        try {
            $stmt->execute();
            $row_count = $stmt->rowCount();
            if ($row_count > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return $row['import_status'];
            } else {
                return "ready";
            }
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
        finally {
            unset($conn);
        }
    }

    private function getImportModelName()
    {
        $model_name = false;
        $type = strtolower($this->type);
        return $this->model_name[$type];
    }

    private function getFile()
    {
        $result = "";

        if (empty($this->url)) {
            $this->update_log([
                "import_status" => "failed",
                "remark" => "import url fail"
            ]);

            return ["error_message" => "import url fail"];
        }

        $model_name = $this->getImportModelName();
        $result = $this->getFileCurl($this->url);

        if ($this->isJson($result)) {
            $result = json_decode($result);
        }

        return $result;
    }

    private function getFileCurl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, urldecode($url));
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    private function setDefineVar()
    {
        // Model name
        $this->model_name = array(
            "positions" => "availablePositionModel",
            "seniority" => "employeeSeniorityModel",
            "saleunit" => "saleUnitModel",
            "positions_master" => "importPositionMasterModel"
        );

        // Sftp data
        $this->sftp = array(
            "availablePositionModel" => array(
                "host" => "10.22.51.114",
                "port " => "22",
                "usr" => "UMHR59",
                "pwd" => "P@ssw0rd",
                "dir" => array(
                    "remote" => "HRAPI/AvailablePositions.json",
                    "local" => ROOT_PATH . "assets/upload/available_positions/AvailablePositions.json"
                )
            )
        );
    }

    private function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}
