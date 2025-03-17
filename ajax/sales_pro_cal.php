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
$p_id = $_REQUEST['value'];
$row_count = $_REQUEST['row_count'] + 1;
$mode = $_REQUEST['mode'];
$qty = $_REQUEST['qty'];
$uom_id = $_REQUEST['uom_id'];
// $tot_amount = $_REQUEST['tot_amount'];
$rs = $con->query("SELECT * FROM tbl_product WHERE id = '".$p_id."' AND del_status = 0");
$obj = $rs->fetch(PDO::FETCH_OBJ);

$uom = $dbcon->GetOneRecord('tbl_uom','uom_name','id ="'.$uom_id.'" AND del_status',0);

if(isset($mode) && $mode == 'pro_item_cal'){
    
    // $tot_price = $qty * $obj->sales_price;
     $tot_price = number_format((float)$qty * $obj->sales_price, 2, '.', '');
    
    $html = '';
    $html.='<tr class="item_count te_al_ce" id="item_count_'.$row_count.'" style="text-a;vertical-align: middle;">
                <td style="text-align: start;vertical-align: middle;">'.$obj->p_code.'</td>
                <td style="text-align: start;vertical-align: middle;">'.$obj->p_name.'<input type="hidden" id="" name="p_id[]" value="'.$obj->id.'"/></td>
                <td style="vertical-align: middle;">'.$uom.'<input type="hidden" id="" name="p_uom[]" value="'.$uom_id.'"/></td>
                <td style="vertical-align: middle;">'.$obj->sales_price.' <input type="hidden" id="hidd_sales_price_'.$row_count.'" name="hidd_sales_price[]" value="'.$obj->sales_price.'"/></td>
                <td style="vertical-align: middle;"><div class="input-group"><lable class="col-form-label form-control inc_dec decreament" data-count="'.$row_count.'">-</lable><div class="input-group inc_dec_val"><input class="form-control input-group-append inc_dec_val inc_dec_input" data-count="'.$row_count.'" id="count_'.$row_count.'" name="count[]" value="'.$qty.'"/></div><lable class="col-form-label form-control inc_dec increament" data-count="'.$row_count.'">+</lable></div></td>
                <td style="vertical-align: middle;"><span id="sales_price_'.$row_count.'">'.$tot_price.'</span><input type="hidden" class="sell_price_amount" name="sell_price_amount[]" id="sell_price_amount_'.$row_count.'" value="'.$tot_price.'"/></td>
                <td style="vertical-align: middle;"><a href="javascript:;" class="remove_item" p_id="'.$obj->id.'" name="total_item" data-count="'.$row_count.'" ><i class="fa fa-trash-o"></i></a></td>
            </tr>';
    echo $html.'~'.$row_count;
}

if(isset($mode) && $mode == 'pro_single_cal'){
    echo $obj->uom.'~'.$obj->sales_price;
}




?>