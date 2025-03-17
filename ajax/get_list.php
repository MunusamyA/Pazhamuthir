<?php
ob_start();
session_start();
define('BASEPATH', '../');

require_once(BASEPATH ."includes/common/connection.php");
require_once(BASEPATH ."includes/common/dbfunctions.php");
require_once(BASEPATH ."includes/common/functions.php");

// ini_set('display_errors', '1'); ini_set('display_startup_errors', '1'); error_reporting(E_ALL);

$con = new connection();
$dbcon = new dbfunctions();

isset($_POST["mode"]);


if($_POST["mode"]=="modal_add_gst")
{
    $total_amt = $_POST["id"];

    $html_output ="";
    $html_output ='<div class="row">
        <div class="col-lg-12">
            <div class="row txt-dets">
                <div class="col-md-12" style="line-height:2rem;">
                    <div class="card-body" style="padding: 0px 15px;">
                        <div class="form-group">
                            <label class="col-sm-7 control-label">Total Amount</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control text-right" name="total_amt" id="total_amt" value="'. number_format($total_amt, 2, '.', '') .'" readonly title="Total Amount">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">SGST <span class="err">*</span></label>
                            <div class="col-sm-3">
                                <select class="form-control select2" name="sgst_id" id="sgst_id" title="Select the SGST">
                                    <option value=""></option>'.
                                    $dbcon->fnFillComboFromTable_Where("id", "sgst", "tbl_gst", "id", " WHERE del_status = 0") .'
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <input type="text" class="form-control text-right" name="sgst_amt" id="sgst_amt" value="" readonly title="SGST Amount">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">CGST <span class="err">*</span></label>
                            <div class="col-sm-3">
                                <select class="form-control select2" name="cgst_id" id="cgst_id" title="Select the CGST">
                                    <option value=""></option>'.
                                    $dbcon->fnFillComboFromTable_Where("id", "cgst", "tbl_gst", "id", " WHERE del_status = 0") .'
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <input type="text" class="form-control text-right" name="cgst_amt" id="cgst_amt" value="" readonly title="CGST Amount">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">IGST <span class="err">*</span></label>
                            <div class="col-sm-3">
                                <select class="form-control select2" name="igst_id" id="igst_id" title="Select the IGST">
                                    <option value=""></option>'.
                                    $dbcon->fnFillComboFromTable_Where("id", "igst", "tbl_gst", "id", " WHERE del_status = 0") .'
                                </select>
                            </div>
                            <div class="col-sm-3">
                                <input type="text" class="form-control text-right" name="igst_amt" id="igst_amt" value="" readonly title="IGST Amount">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-7 control-label">Total GST</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control text-right" name="total_gst" id="total_gst" value="" readonly title="Total GST">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>';

    echo "<b>Sales - Add GST</b>" ."~". $html_output;
}

if($_POST["mode"]=="calculate_sgst")
{
    $url_data = explode("~", $_POST["id"]);
    $id = $url_data[0];
    $tot_amt = $url_data[1];

    $sgst = $dbcon->GetOneRecord("tbl_gst", "sgst", "id = ". $id ." and del_status", 0);

    echo ($tot_amt * $sgst)/100;
}

if($_POST["mode"]=="calculate_cgst")
{
    $url_data = explode("~", $_POST["id"]);
    $id = $url_data[0];
    $tot_amt = $url_data[1];

    $cgst = $dbcon->GetOneRecord("tbl_gst", "cgst", "id = ". $id ." and del_status", 0);

    echo ($tot_amt * $cgst)/100;
}

if($_POST["mode"]=="calculate_igst")
{
    $url_data = explode("~", $_POST["id"]);
    $id = $url_data[0];
    $tot_amt = $url_data[1];

    $igst = $dbcon->GetOneRecord("tbl_gst", "igst", "id = ". $id ." and del_status", 0);

    echo ($tot_amt * $igst)/100;
}
?>