<?php
require_once '../config/database.php';

class employeeSeniorityModel extends importModel
{
    private $result_data;
    private $import_data = array();
    private $table = "db_seniority";
    private $import_data_rows;
    private $imported_count = 0;
    private $import_id;

    public function __construct()
    {
        parent::__construct();
    }

    public function run()
    {
        // Customize by type
        $data = $this->result_data;

        // Get data to import from file
        $this->import_data = $data->generateFileSeniority ?? array();

        // Already imported data checked (Always clean import for this function)
        $inserted_keys = [];

        // Clean Table
        $this->cleanImportedData($this->table);

        // Count prepare import data rows
        $this->import_data_rows = count($this->import_data);

        $this->updateLog([
            "import_status" => "running",
            "import_data_rows" => $this->import_data_rows
        ],  $this->import_id);

        // // Import
        $this->importData();

        // Update Completed status to log
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
    private function importData()
    {
        $conn = $this->database->getConnection();
        try {
            $sql = "INSERT INTO " . $this->table;
            $sql .= " (
                        empId,
                        empName,
                        posId,
                        posName,
                        orgId,
                        group_no,
                        subGroup,
                        birthDate,
                        age,
                        positionAssignDate,
                        positionAssignAge,
                        orgAssignDate,
                        orgAssignDateAge,
                        kpi,
                        kpiAverage,
                        listKpi,
                        education
                    ) ";
            $sql .= " VALUES (
                        :emp_id,
                        :emp_name,
                        :pos_id,
                        :pos_name,
                        :orgId,
                        :group_no,
                        :subGroup,
                        :birthDate,
                        :age,
                        :positionAssignDate,
                        :positionAssignAge,
                        :orgAssignDate,
                        :orgAssignDateAge,
                        :kpi,
                        :kpiAverage,
                        :listKpi,
                        :education
                    )";

            $conn->beginTransaction();
            $stmt = $conn->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));

            foreach ($this->import_data as $value) {
                $stmt->bindParam(":emp_id", $value->empId);
                $stmt->bindParam(":emp_name", $value->empName);
                $stmt->bindParam(":pos_id", $value->posId);
                $stmt->bindParam(":pos_name", $value->posName);
                $stmt->bindParam(":orgId", $value->orgId);
                $stmt->bindParam(":group_no", $value->group);
                $stmt->bindParam(":subGroup", $value->subGroup);
                $stmt->bindParam(":birthDate", $value->birthDate);
                $stmt->bindParam(":age", $value->age);
                $stmt->bindParam(":positionAssignDate", $value->positionAssignDate);
                $stmt->bindParam(":positionAssignAge", $value->positionAssignAge);
                $stmt->bindParam(":orgAssignDate", $value->orgAssignDate);
                $stmt->bindParam(":orgAssignDateAge", $value->orgAssignDateAge);
                $stmt->bindParam(":kpi", $value->kpi);
                $stmt->bindParam(":kpiAverage", $value->kpiAverage);
                $stmt->bindParam(":listKpi", json_encode($value->listKpi));
                $stmt->bindParam(":education", $value->education);
                $stmt->execute();
                $this->imported_count ++;

                error_log($this->imported_count, 0);
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
