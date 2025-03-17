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

// echo "conform";die;
$id= $_REQUEST['id'];
$p_code= $_REQUEST['p_code'];
$mode = $_REQUEST['mode'];

if($mode == "product_code" && $p_code != '' && $id ==''){
    echo $status = $dbcon->GetOneRecord('tbl_product','id','p_code ="'.$p_code.'" AND del_status',0);
    
}elseif($mode == "product_code" && $p_code != '' && $id !=''){
    echo $status = $dbcon->GetOneRecord('tbl_product','id','p_code ="'.$p_code.'" AND id <>"'.$id.'" AND del_status',0);
}else{
    echo '0';
}



?>