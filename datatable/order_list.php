<?php
ob_start();
session_start();
define('BASEPATH', '../');
require_once(BASEPATH ."includes/common/connection.php");
require_once(BASEPATH ."includes/common/dbfunctions.php");
require_once(BASEPATH ."includes/common/functions.php");

$con = new connection();
$dbcon = new dbfunctions();
$converter = new Encryption;

// ini_set('display_errors', '1'); ini_set('display_startup_errors', '1'); error_reporting(E_ALL);

isAdmin();

$draw = isset($_POST['draw']) ? intval($_POST['draw']) : '';
$start = isset($_POST['start']) ? intval($_POST['start']) : 0; // Starting record index
$length = isset($_POST['length']) ? intval($_POST['length']) : 10; // Number of records per page
$columnIndex = isset($_POST['order'][0]['column']) ? intval($_POST['order'][0]['column']) : 1; // Column index to sort by
$columnOrder = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc'; // Order direction
$columnName = $_POST['columns'][$columnIndex]['data']; 
$search = isset($_POST['search']['value']) ? $_POST['search']['value']: '';


$searchQuery = "";

if($search != ''){
    $searchQuery.=' AND (cus_name like "%' . $search . '%" OR order_no like "%' . $search . '%" OR tot_amount like "%' . $search . '%" OR gst_amt like "%' . $search . '%" OR discount_amount like "%' . $search . '%" OR grand_total like "%' . $search . '%")';
}

$sel = $con->query("SELECT count(*) as allcount FROM tbl_sales where order_status ='1' AND del_status='0'".$searchQuery);
$records = $sel->fetch();
$totalRecords = $records->allcount;

$sel = $con->query("SELECT  count(*) as allcount FROM tbl_sales where order_status ='1' AND del_status='0'".$searchQuery);
$records = $sel->fetch();
$totalRecordwithFilter = $records->allcount;

// $rs = null;
// $rs = $con->query("SELECT * FROM tbl_sales where order_status ='2' AND del_status='0' ORDER BY '" . $columnName . "' '" . $columnSortOrder . "' limit '" . $row . "','" . $rowperpage"'");
$rs = $con->query("SELECT * FROM tbl_sales WHERE order_status = '1' AND del_status = '0' ".$searchQuery." ORDER BY " . $columnName . " " . $columnOrder . " LIMIT " . $start . ", " . $length);


// $sno = 1;
$sno = $start + 1;
$data = array();
while($obj = $rs->fetch(PDO::FETCH_OBJ)){
$action = '<a href="pos.php?id='.$converter->encode($obj->id).'" title="Edit" style="margin-right: 20px;"> <i class="fa fa-pencil" aria-hidden="true"></i></a>
            <a href="javascript:;" title="Delete" data-id='.$converter->encode($obj->id).' style="margin-right: 20px;"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                                <a href="javascript:;" class="complete_bill" data-id='.$converter->encode($obj->id).'><i class="fa fa-check" aria-hidden="true"></i></a>';
$data[]=array(
"id" => $sno,
"order_no" => $obj->order_no,
"cus_name" => $obj->cus_name,
"tot_amount" => $obj->tot_amount,
"discount_amount" => $obj->discount_amount,
"gst_amt" => $obj->gst_amt,
"grand_total" => $obj->grand_total,
"action" => $action,
);
$sno++;
}

$response = array(
    "draw" => intval($draw),
    "iTotalRecords" => $totalRecords,
    "iTotalDisplayRecords" => $totalRecordwithFilter,
    "aaData" => $data
);

echo json_encode($response);


?>