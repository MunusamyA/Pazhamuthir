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

ini_set('display_errors', '1'); ini_set('display_startup_errors', '1'); error_reporting(E_ALL);

isAdmin();

// echo "conform";die;
$value = $_REQUEST['value'];
$mode = $_REQUEST['mode'];

// $rs = $con->query("SELECT * FROM tbl_product where id=" . $converter->decode($_REQUEST["id"]));
// $rs = $con->query("SELECT * FROM tbl_product where id=" . $converter->decode($_REQUEST["id"]));

$val = ($value != '') ? $value : '';
$str ='';
if($val!=''){
$rs = $con->query("SELECT * FROM tbl_product WHERE p_name LIKE '%".$val."%' OR p_code LIKE '%".$val."%' AND del_status = 0");
// $obj = $rs->fetch(PDO::FETCH_OBJ);
// print_r($rs);

$str.='<ul id="optionList" class="options">';
while($obj = $rs->fetch(PDO::FETCH_OBJ)){
    
    $str.='<li onclick="selectOption(this)" cal="'.$obj->id.'">'.$obj->p_name.'</li>';

}
$str.='</ul>';

}
echo $str;
// die;


?>