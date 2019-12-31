<?php
$root_dir = dirname(dirname(__FILE__)) . '/';
$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$actual_link = explode("endpoint", $actual_link);

define("ROOT_PATH", $root_dir);
define("ACTUAL_LINK", $actual_link[0]);

require_once $root_dir . 'models/importModel.php';

class importController extends importModel
{

    private $path = array(
        "positions" => ACTUAL_LINK . "assets/upload/import/AvailablePositions.json",
        "seniority" => ACTUAL_LINK . "assets/upload/import/EmployeeSeniority.json",
        "saleunit" => ROOT_PATH . "assets/upload/sale_unit/",
        "positions_master" => ROOT_PATH . "assets/upload/positions_master/"
    );

    public function run($type = false)
    {
        if (empty($type) || empty($this->getPath($type))) {
            return [
                "error" => "400",
                "error_message" => "Type and Url is required"
            ];
        }

        $import = new importModel($type, $this->getPath($type));

        $imported_count = $import->execute();
        return $this->resultMessage($imported_count);

    }

    private function resultMessage($imported_count)
    {
        $text = $imported_count . " rows already imported";

        if ($imported_count <= 0) {
            $text = "No new rows imported";
        }

        return [
            "result" => "success",
            "error" => "200",
            "error_message" => $text
        ];
    }

    private function getPath($type)
    {
        $path = false;

        if ($type == "saleunit") {
            $file = $this->getLastedFile($type);
            $path = ACTUAL_LINK . "assets/upload/sale_unit/" . $file["file_name"];
        } elseif ($type == "positions_master") {
            $file = $this->getFile($type);
            $path = ACTUAL_LINK . "assets/upload/positions_master/" . $file;
        } elseif (isset($this->path[$type])) {
            $path = urlencode($this->path[$type]);
        }

        return $path;
    }

    // function for get lasted file to import format like "xxxx_20181231.xxx"
    private function arrayDateSort($first, $second)
    {
        return strtotime($first["date"]) - strtotime($second["date"]);
    }

    private function getLastedFile($type = false)
    {
        $data = false;
        if ($type) {
            $file_lists = array();
            if ($handle = opendir($this->path[$type])) {
                while (false !== ($entry = readdir($handle))) {
                    if ($entry != "." && $entry != "..") {
                        $file_date = explode("_", $entry);
                        $file_date = str_replace(".txt", "", $file_date[1]);
                        $file = array(
                            "file_name" => $entry,
                            "date" => $file_date
                        );
                        array_push($file_lists, $file);
                        unset($file_date);
                    }
                }
                closedir($handle);
            }
            usort($file_lists, array($this, 'arrayDateSort'));
            $file_lists = array_reverse($file_lists);
            if (count($file_lists) > 0) {
                $data = $file_lists[0];
            }
        }

        return $data;
    }

    private function getFile($type = false)
    {
        $data = false;
        if ($type) {
            $file_lists = array();
            if ($handle = opendir($this->path[$type])) {
                while (false !== ($entry = readdir($handle))) {
                    if ($entry != "." && $entry != "..") {
                        array_push($file_lists, $entry);
                        break;
                    }
                }
                closedir($handle);
            }

            if (count($file_lists) > 0) {
                $data = $file_lists[0];
            }
        }

        return $data;
    }
}
