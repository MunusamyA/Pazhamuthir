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

// $id= $_REQUEST['ord_id'];
$id = $converter->decode($_REQUEST["ord_id"]);
$mode = $_REQUEST['mode'];

if($mode == "complete_bill" && $id !=''){
    try{
        $con->query("UPDATE tbl_sales SET order_status = '2' WHERE id ='".$id."' AND del_status = '0'");
        $con->query("INSERT INTO tbl_sales_dts (sale_id, p_id, uom_id, sale_price, count, tot_sale_price) SELECT sale_id, p_id, uom_id, sale_price, count, tot_sale_price FROM tbl_sales_dts_temp WHERE sale_id='".$id."'");
        $con->query("DELETE FROM tbl_sales_dts_temp WHERE sale_id =". $id);
        echo "completed Successfully";
    } catch (Exception $e) {
        $str = filter_var($e->getMessage(), FILTER_SANITIZE_STRING);
        echo $str;  
    }
}
    




?>