<?php
ini_set('memory_limit', '128M');
require_once '../config/database.php';

class availablePositionModel extends importModel
{
    private $result_data;
    private $import_data = array();
    private $table = "db_position";
    private $import_data_rows;
    private $imported_count = 0;
    private $import_id;

    public function __construct()
    {
        parent::__construct();
    }

    public function run()
    {
        $data = $this->result_data;
        $positions = $data->AvailablePosition->childs;
        $this->setPositions($positions);
        $this->import_data_rows = count($this->import_data);

        $this->updateLog([
            "import_status" => "running",
            "import_data_rows" => $this->import_data_rows
        ], $this->import_id);

        // Import
        $this->importData();
        $this->updateLog([
            "import_status" => "completed",
            "imported_rows" => $this->imported_count,
            "end_datetime" => date('Y-m-d H:i:s')
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

    public function getTableName()
    {
        return $this->table;
    }

    // Customize function to import API
    private function setPositions($positions, $parent_id = null)
    {
        foreach ($positions as $position) {
            // Skip when found person name
            // if ($position->objectType == "P") continue;

            // Recursive when childs found
            if (count($position->childs) > 0) {
                $this->setPositions($position->childs, $position->objectId);
            }

            $this->setPositionData($position, $parent_id);
        }
    }

    private function setPositionData($position, $parent_id)
    {
        // Skip when ObjectType "O"
        // if ($position->objectType == "O") return;

        // Default parentId
        if ($parent_id === null) $parent_id = 10000000;

        $value = [
            $position->objectId => [
                "objectType" => $position->objectType,
                "objectId" => $position->objectId,
                "shortText" => $position->shortText,
                "longText" => $position->longText,
                "longText_en" => $position->longText,
                "beginDate" => $position->beginDate,
                "endDate" => $position->endDate,
                "dflag" => $position->dflag,
                "parentId" => $parent_id,
                "taskId" => $position->taskId,
                "taskDesc" => $position->taskDesc,
                "positionGroupId" => $position->positionGroupId,
                "positionGroupDesc" => $position->positionGroupDesc,
                "positionAvailableDate" => $position->positionAvailableDate
            ]
        ];

        array_push($this->import_data, $value);
        if (count($this->import_data) >= 5000) {
            $this->importData();
            $this->import_data = array();
        }
    }

    private function importData()
    {
        $conn = $this->database->getConnection();

        try {
            $sql = "INSERT INTO " . $this->table;
            $sql .= " (
                        objectId,
                        objectType,
                        parentId,
                        shortText,
                        longText,
                        longText_en,
                        beginDate,
                        endDate,
                        dFlag,
                        taskId,
                        taskDesc,
                        positionGroupId,
                        positionGroupDesc,
                        positionAvailableDate
                        )";
            $sql .= " VALUES (
                        :object_id,
                        :object_type,
                        :parent_id,
                        :short_text,
                        :long_text,
                        :long_text_en,
                        :begin_date,
                        :end_date,
                        :d_flag,
                        :taskId,
                        :taskDesc,
                        :positionGroupId,
                        :positionGroupDesc,
                        :positionAvailableDate
                    )";

            $conn->beginTransaction();
            $stmt = $conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

            foreach ($this->import_data as $value) {
                $key = key($value);
                $importing_data_value = $value[$key];

                $stmt->bindParam(":object_id", $key);
                $stmt->bindParam(":object_type", $importing_data_value["objectType"]);
                $stmt->bindParam(":parent_id", $importing_data_value["parentId"]);
                $stmt->bindParam(":short_text", $importing_data_value["shortText"]);
                $stmt->bindParam(":long_text", $importing_data_value["longText"]);
                $stmt->bindParam(":long_text_en", $importing_data_value["longText_en"]);
                $stmt->bindParam(":begin_date", $importing_data_value["beginDate"]);
                $stmt->bindParam(":end_date", $importing_data_value["endDate"]);
                $stmt->bindParam(":d_flag", $importing_data_value["dFlag"]);
                $stmt->bindParam(":taskId", $importing_data_value["taskId"]);
                $stmt->bindParam(":taskDesc", $importing_data_value["taskDesc"]);
                $stmt->bindParam(":positionGroupId", $importing_data_value["positionGroupId"]);
                $stmt->bindParam(":positionGroupDesc", $importing_data_value["positionGroupDesc"]);
                $stmt->bindParam(":positionAvailableDate", $importing_data_value["positionAvailableDate"]);
                $stmt->execute();

                $this->imported_count++;
                error_log($this->imported_count);
            }

            $conn->commit();
        } catch (PDOException $ex) {
            $conn->rollback();
            die($ex->getMessage());
        } finally {
            unset($conn);
        }
    }
}
