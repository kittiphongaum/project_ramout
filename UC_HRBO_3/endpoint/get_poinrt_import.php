<?php
require_once '../model/get_master_Model_import.php';

$request = $_GET;
$model = new get_master_Model_import();

$search = $request["search"];
$order = $request["order"];
$orderBY = $order[0];
$col = $orderBY["column"];
$dir  =$orderBY["dir"]; 

$model->setPageSize($request["length"]);

$count = $model->count();

$page = ceil((intval($request["start"]) + 1) / $request["length"]);

$records = $model->getAll($page, $search, $col, $dir);

$data = array();

if ($records->rowCount() > 0) {
    $conn=1;
    while ($row = $records->fetch(PDO::FETCH_ASSOC)) {
        $nestedData = array();
        $nestedData[] = ($row["no"]= $conn);
        $nestedData[] = $row["emp_id"];
        $nestedData[] = number_format($row["saving_amt"],2);
        $nestedData[] = number_format($row["saving_benefit"],2);
        $nestedData[] = number_format($row["supporting_amt"],2);
        $nestedData[] = number_format($row["supporting_benefit"],2);
        $nestedData[] = number_format($row["initiate_amt"],2);
        $nestedData[] = number_format($row["initiate_benefit"],2);
        $nestedData[] = number_format($row["initiate_result_1"],2);
        $data[] = $nestedData;
        $conn++;
    }
}
$json_data = array(
            "draw"            => intval($request['draw']),
            "recordsTotal"    => intval($count),
            "totalPages"      => intval(ceil($count/10)),
            "data"            => $data,
            "recordsFiltered" => intval($count)
            );

echo json_encode($json_data);
