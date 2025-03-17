<?php
ob_start();
session_start();

require_once("includes/common/connection.php");
require_once("includes/common/dbfunctions.php");
require_once("includes/common/functions.php");

$con = new connection();
$dbcon = new dbfunctions();
$converter = new Encryption;

// ini_set('display_errors', '1'); ini_set('display_startup_errors', '1'); error_reporting(E_ALL);

isAdmin();
function generateOrderCode($lastOrderCode) {
    $orderNumber = (int) substr($lastOrderCode, 3);
    $orderNumber++;
    $newOrderNumber = '00' . $orderNumber;
    return 'ORD' . $newOrderNumber;
}
//---------------------------------save/submit----------------------------------
if (isset($_REQUEST["submit"])) {
    try{
        $rs = $con->query("SELECT order_no FROM tbl_sales ORDER BY id DESC LIMIT 1");
        $obj = $rs->fetch(PDO::FETCH_OBJ);
        $newOrderCode = generateOrderCode($obj->order_no);
        $created_by = $_SESSION["user_id"];
        $created_dt = date('Y-m-d H:i:s');
        $discount_type = ($_REQUEST["discount_type"] !='') ? $_REQUEST["discount_type"] : null;
        $discount_amount = ($_REQUEST["discount_amount"] !='') ? $_REQUEST["discount_amount"] : null;

        $sgst_amt = ($_REQUEST["hid_sgst_amt"] !='') ? $_REQUEST["hid_sgst_amt"] : 0.00 ;
        $cgst_amt = ($_REQUEST["hid_cgst_amt"] !='') ? $_REQUEST["hid_cgst_amt"] : 0.00 ;
        $igst_amt = ($_REQUEST["hid_igst_amt"] !='') ? $_REQUEST["hid_igst_amt"] : 0.00 ;
        $gst_amt = ($_REQUEST["total_gst_amt"] !='') ? $_REQUEST["total_gst_amt"] : 0.00 ;
        $round_amount = ($_REQUEST["round_amount"] !='') ? $_REQUEST["round_amount"] : 0.00 ;

        $stmt = null;
        $stmt = $con->prepare("INSERT INTO tbl_sales (table_name, cus_name, tot_amount, order_status, order_no, discount_type, discount_amount, sgst_amt, cgst_amt, igst_amt, gst_amt, round_amount, grand_total, created_by, created_dt) 
              VALUES ( :table_name, :cus_name, :tot_amount, :order_status, :order_no, :discount_type, :discount_amount, :sgst_amt, :cgst_amt, :igst_amt, :gst_amt, :round_amount, :grand_total, :created_by, :created_dt)");
        $data = array(
            ":table_name" => trim($_REQUEST["table_name"]),
            ":cus_name" => trim($_REQUEST["cus_name"]),
            ":tot_amount" => trim($_REQUEST["hidd_tot_amount"]),
            ":order_status" => 1,
            ":order_no" => $newOrderCode,
            ":discount_type" => $discount_type,
            ":discount_amount" => $discount_amount,
            ":sgst_amt" => $sgst_amt,
            ":cgst_amt" => $cgst_amt,
            ":igst_amt" => $igst_amt,
            ":gst_amt" => $gst_amt,
            ":round_amount" => $round_amount,
            ":grand_total" => trim($_REQUEST["hid_grand_total"]),
            ":created_by" => $created_by,
            ":created_dt" => $created_dt,
        );
// print_r($data);
        $stmt->execute($data);

        $sale_id = $con->lastInsertId();

        $total_item = count($_REQUEST['sell_price_amount']);
        $stmt1 = null;
        $stmt1 = $con->prepare("INSERT INTO tbl_sales_dts_temp (sale_id, p_id, uom_id, sale_price, count, tot_sale_price) 
              VALUES (:sale_id, :p_id, :uom_id, :sale_price, :count, :tot_sale_price)");
        for($i=0;$i<$total_item;$i++){
            $data1 = array(
                ":sale_id" => $sale_id,
                ":p_id" => trim($_REQUEST["p_id"][$i]),
                ":uom_id" => trim($_REQUEST["p_uom"][$i]),
                ":sale_price" => trim($_REQUEST["hidd_sales_price"][$i]),
                ":count" => trim($_REQUEST["count"][$i]),
                ":tot_sale_price" => trim($_REQUEST["sell_price_amount"][$i]),
            );
            $stmt1->execute($data1);
        }
        $_SESSION["msg"] = "Saved Successfully";
    } catch (Exception $e) {
        $str = filter_var($e->getMessage(), FILTER_SANITIZE_STRING);
        echo $_SESSION['msg_err'] = $str;  
    }
    
    header("location: order.php");
    die();
}
if (isset($_REQUEST["update"])) {
    try{
        $updated_by = $_SESSION["user_id"];
        $updated_dt = date('Y-m-d H:i:s');
        $discount_type = ($_REQUEST["discount_type"] !='') ? $_REQUEST["discount_type"] : null;
        $discount_amount = ($_REQUEST["discount_amount"] !='') ? $_REQUEST["discount_amount"] :null;
        $sgst_amt = ($_REQUEST["hid_sgst_amt"] !='') ? $_REQUEST["hid_sgst_amt"] : 0.00 ;
        $cgst_amt = ($_REQUEST["hid_cgst_amt"] !='') ? $_REQUEST["hid_cgst_amt"] : 0.00 ;
        $igst_amt = ($_REQUEST["hid_igst_amt"] !='') ? $_REQUEST["hid_igst_amt"] : 0.00 ;
        $gst_amt = ($_REQUEST["total_gst_amt"] !='') ? $_REQUEST["total_gst_amt"] : 0.00 ;
        $round_amount = ($_REQUEST["round_amount"] !='') ? $_REQUEST["round_amount"] : 0.00 ;
        $stmt = null;
        $stmt = $con->prepare("UPDATE tbl_sales SET cus_name=:cus_name,table_name=:table_name,tot_amount=:tot_amount,discount_type=:discount_type,discount_amount=:discount_amount, sgst_amt=:sgst_amt,cgst_amt=:cgst_amt,igst_amt=:igst_amt,gst_amt=:gst_amt,round_amount=:round_amount,grand_total=:grand_total, updated_by=:updated_by,updated_dt=:updated_dt where id=:id");
        $data = array(
            ":cus_name" => trim($_REQUEST["cus_name"]),
            ":table_name" => trim($_REQUEST["table_name"]),
            ":tot_amount" => trim($_REQUEST["hidd_tot_amount"]),
            ":discount_type" => $discount_type,
            ":discount_amount" => $discount_amount,
            ":sgst_amt" => $sgst_amt,
            ":cgst_amt" => $cgst_amt,
            ":igst_amt" => $igst_amt,
            ":gst_amt" => $gst_amt,
            ":round_amount" => $round_amount,
            ":grand_total" => trim($_REQUEST["hid_grand_total"]),
            ":updated_by" => $updated_by,
            ":updated_dt" => $updated_dt,
            ":id" => trim($_REQUEST["hid_id"])
        );
        $stmt->execute($data);
        $con->query("DELETE FROM tbl_sales_dts_temp WHERE sale_id =". $_REQUEST['hid_id']);
        $total_item = count($_REQUEST['sell_price_amount']);
        $stmt1 = null;
        $stmt1 = $con->prepare("INSERT INTO tbl_sales_dts_temp (sale_id, p_id, uom_id, sale_price, count, tot_sale_price) 
              VALUES (:sale_id, :p_id, :uom_id, :sale_price, :count, :tot_sale_price)");
        for($i=0;$i<$total_item;$i++){
            $data1 = array(
                ":sale_id" => trim($_REQUEST["hid_id"]),
                ":p_id" => trim($_REQUEST["p_id"][$i]),
                ":uom_id" => trim($_REQUEST["p_uom"][$i]),
                ":sale_price" => trim($_REQUEST["hidd_sales_price"][$i]),
                ":count" => trim($_REQUEST["count"][$i]),
                ":tot_sale_price" => trim($_REQUEST["sell_price_amount"][$i]),
            );
            $stmt1->execute($data1);
        }
        $_SESSION["msg"] = "Updated Successfully";
    } catch (Exception $e) {
        $str = filter_var($e->getMessage(), FILTER_SANITIZE_STRING);
        echo $_SESSION['msg_err'] = $str;  
    }
    
    header("location: order.php");
    die();
}

if (isset($_REQUEST["pay_bill"])) {
    
    if(isset($_REQUEST["hid_id"])){
        try{
            $updated_by = $_SESSION["user_id"];
            $updated_dt = date('Y-m-d H:i:s');
            $discount_type = ($_REQUEST["discount_type"] !='') ? $_REQUEST["discount_type"] : null;
            $discount_amount = ($_REQUEST["discount_amount"] !='') ? $_REQUEST["discount_amount"] :null;
            $sgst_amt = ($_REQUEST["hid_sgst_amt"] !='') ? $_REQUEST["hid_sgst_amt"] : 0.00 ;
            $cgst_amt = ($_REQUEST["hid_cgst_amt"] !='') ? $_REQUEST["hid_cgst_amt"] : 0.00 ;
            $igst_amt = ($_REQUEST["hid_igst_amt"] !='') ? $_REQUEST["hid_igst_amt"] : 0.00 ;
            $gst_amt = ($_REQUEST["total_gst_amt"] !='') ? $_REQUEST["total_gst_amt"] : 0.00 ;
            $round_amount = ($_REQUEST["round_amount"] !='') ? $_REQUEST["round_amount"] : 0.00 ;
            $stmt = null;
            $stmt = $con->prepare("UPDATE tbl_sales SET cus_name=:cus_name,table_name=:table_name,tot_amount=:tot_amount,order_status=:order_status,discount_type=:discount_type,discount_amount=:discount_amount, sgst_amt=:sgst_amt,cgst_amt=:cgst_amt,igst_amt=:igst_amt,gst_amt=:gst_amt,round_amount=:round_amount,grand_total=:grand_total,updated_by=:updated_by,updated_dt=:updated_dt where id=:id");
            $data = array(
                ":cus_name" => trim($_REQUEST["cus_name"]),
                ":table_name" => trim($_REQUEST["table_name"]),
                ":tot_amount" => trim($_REQUEST["hidd_tot_amount"]),
                ":order_status" => 2,
                ":discount_type" => $discount_type,
                ":discount_amount" => $discount_amount,
                ":sgst_amt" => $sgst_amt,
                ":cgst_amt" => $cgst_amt,
                ":igst_amt" => $igst_amt,
                ":gst_amt" => $gst_amt,
                ":round_amount" => $round_amount,
                ":grand_total" => trim($_REQUEST["hid_grand_total"]),
                ":updated_by" => $updated_by,
                ":updated_dt" => $updated_dt,
                ":id" => trim($_REQUEST["hid_id"])
            );
            // print_r($stmt);
            // print_r($data);die;
            $stmt->execute($data);
            $con->query("DELETE FROM tbl_sales_dts_temp WHERE sale_id =". $_REQUEST['hid_id']);
            $total_item = count($_REQUEST['sell_price_amount']);
            $stmt1 = null;
            $stmt1 = $con->prepare("INSERT INTO tbl_sales_dts (sale_id, p_id, uom_id, sale_price, count, tot_sale_price) 
                  VALUES (:sale_id, :p_id, :uom_id, :sale_price, :count, :tot_sale_price)");
            for($i=0;$i<$total_item;$i++){
                $data1 = array(
                    ":sale_id" => trim($_REQUEST["hid_id"]),
                    ":p_id" => trim($_REQUEST["p_id"][$i]),
                    ":uom_id" => trim($_REQUEST["p_uom"][$i]),
                    ":sale_price" => trim($_REQUEST["hidd_sales_price"][$i]),
                    ":count" => trim($_REQUEST["count"][$i]),
                    ":tot_sale_price" => trim($_REQUEST["sell_price_amount"][$i]),
                );
                $stmt1->execute($data1);
            }
            $_SESSION["msg"] = "completed Successfully";
        } catch (Exception $e) {
            $str = filter_var($e->getMessage(), FILTER_SANITIZE_STRING);
            echo $_SESSION['msg_err'] = $str;  
        }
        
        header("location: pos.php");
        die();
    }else{
        try{
            $rs = $con->query("SELECT order_no FROM tbl_sales ORDER BY id DESC LIMIT 1");
            $obj = $rs->fetch(PDO::FETCH_OBJ);
            $newOrderCode = generateOrderCode($obj->order_no);
            $created_by = $_SESSION["user_id"];
            $created_dt = date('Y-m-d H:i:s');
            $discount_type = ($_REQUEST["discount_type"] !='') ? $_REQUEST["discount_type"] : null;
            $discount_amount = ($_REQUEST["discount_amount"] !='') ? $_REQUEST["discount_amount"] :null;
            $sgst_amt = ($_REQUEST["hid_sgst_amt"] !='') ? $_REQUEST["hid_sgst_amt"] : 0.00 ;
            $cgst_amt = ($_REQUEST["hid_cgst_amt"] !='') ? $_REQUEST["hid_cgst_amt"] : 0.00 ;
            $igst_amt = ($_REQUEST["hid_igst_amt"] !='') ? $_REQUEST["hid_igst_amt"] : 0.00 ;
            $gst_amt = ($_REQUEST["total_gst_amt"] !='') ? $_REQUEST["total_gst_amt"] : 0.00 ;
            $round_amount = ($_REQUEST["round_amount"] !='') ? $_REQUEST["round_amount"] : 0.00 ;
            $stmt = null;
            $stmt = $con->prepare("INSERT INTO tbl_sales (cus_name,table_name, tot_amount, order_status, order_no, discount_type, discount_amount, sgst_amt, cgst_amt, igst_amt, gst_amt, round_amount, grand_total, created_by, created_dt) 
                  VALUES (:cus_name,:table_name, :tot_amount, :order_status, :order_no, :discount_type, :discount_amount, :sgst_amt, :cgst_amt, :igst_amt, :gst_amt, :round_amount, :grand_total, :created_by, :created_dt)");
            $data = array(
                ":cus_name" => trim($_REQUEST["cus_name"]),
                ":table_name" => trim($_REQUEST["table_name"]),
                ":tot_amount" => trim($_REQUEST["hidd_tot_amount"]),
                ":order_status" => 2,
                ":order_no" => $newOrderCode,
                ":discount_type" => $discount_type,
                ":discount_amount" => $discount_amount,
                ":sgst_amt" => $sgst_amt,
                ":cgst_amt" => $cgst_amt,
                ":igst_amt" => $igst_amt,
                ":gst_amt" => $gst_amt,
                ":round_amount" => $round_amount,
                ":grand_total" => trim($_REQUEST["hid_grand_total"]),
                ":created_by" => $created_by,
                ":created_dt" => $created_dt,
            );
    
            $stmt->execute($data);
    
            $sale_id = $con->lastInsertId();
    
            $total_item = count($_REQUEST['sell_price_amount']);
            $stmt1 = null;
            $stmt1 = $con->prepare("INSERT INTO tbl_sales_dts (sale_id, p_id, uom_id, sale_price, count, tot_sale_price) 
                  VALUES (:sale_id, :p_id, :uom_id, :sale_price, :count, :tot_sale_price)");
            for($i=0;$i<$total_item;$i++){
                $data1 = array(
                    ":sale_id" => $sale_id,
                    ":p_id" => trim($_REQUEST["p_id"][$i]),
                    ":uom_id" => trim($_REQUEST["p_uom"][$i]),
                    ":sale_price" => trim($_REQUEST["hidd_sales_price"][$i]),
                    ":count" => trim($_REQUEST["count"][$i]),
                    ":tot_sale_price" => trim($_REQUEST["sell_price_amount"][$i]),
                );
                $stmt1->execute($data1);
            }

            $_SESSION["msg"] = "completed Successfully";

        } catch (Exception $e) {
            $str = filter_var($e->getMessage(), FILTER_SANITIZE_STRING);
            echo $_SESSION['msg_err'] = $str;  
        }
        header("location: pos.php");
        die();
    }

    
}
//---------------------------------save/submit----------------------------------
//---------------------------------edit----------------------------------------
$id = $converter->decode($_REQUEST["id"]);
if (isset($_REQUEST["id"])) {
    $rs = $con->query("SELECT * FROM tbl_sales where id=" . $id);
    if ($rs->rowCount()) {
        if ($obj = $rs->fetch(PDO::FETCH_OBJ)) {
            $id = $obj->id;
            $table_name = $obj->table_name;
            $tot_amount = $obj->tot_amount;
            $order_status = $obj->order_status;
            $order_no = $obj->order_no;
            $discount_amount = ($obj->discount_amount !='') ? $obj->discount_amount :'';
            $discount_type = ($obj->discount_type !='') ? $obj->discount_type :'';
            $grand_total = ($obj->grand_total !='') ? $obj->grand_total :'';
            $newOrderCode = $obj->order_no;
            $cus_name = $obj->cus_name;
            $sgst_amt = ($obj->sgst_amt !='') ? $obj->sgst_amt :'';
            $cgst_amt = ($obj->cgst_amt !='') ? $obj->cgst_amt :'';
            $igst_amt = ($obj->igst_amt !='') ? $obj->igst_amt :'';
        }
    }

    $stmt1 = null;
    $stmt1 = $con->query("SELECT * FROM tbl_sales_dts_temp WHERE sale_id= '".$id."'");
    $cou = $stmt1->rowCount()-1;
    
}else{
    $cou =-1;
    $tot_amount = "0.00";
    $grand_total = "0.00";

    $rs = $con->query("SELECT order_no FROM tbl_sales ORDER BY id DESC LIMIT 1");
    $obj = $rs->fetch(PDO::FETCH_OBJ);
    $newOrderCode = generateOrderCode($obj->order_no);

}
//---------------------------------edit----------------------------------------
?>

<?php include("includes/header.php"); ?>
<?php include("includes/aside.php"); ?>
<style>
    .input-group {
display: flex;
flex-wrap: wrap;
align-items: stretch;
width: 100%;
position: relative;
}

.input-group-append {
  flex: 0 0 auto;
}

.input-group .form-control {
  flex: 1 1 auto;
  width: 1%;
  min-width: 0;
  position: relative;
  height: auto;
}
.input-group select {
  border-top-left-radius: 0;
  border-bottom-left-radius: 0;
  position: relative;
  width: 100%;
}
    .options {
        list-style-type: none;
        padding: 0;
        margin: 0;
        border: 1px solid #ccc;
        width: auto;
        max-height: 150px;
        overflow-y: auto;
    }

    .options li {
        padding: 10px;
        cursor: pointer;
    }
    .options li.selected {
        background-color: #f0f0f0;
    }
    .options li:hover {
        background-color: #f0f0f0;
    }
   
    /* thead th, tfoot td {
        background-color: #f2f2f2 !important;
        position: sticky;
        top: 0;
        z-index: 1;
        text-align:center;
    }
     */
    tfoot td {
        background-color: #f2f2f2;
        position: sticky;
        top: 0;
        z-index: 1;
        text-align:center;
    }
     .te_al_ce{
        text-align:center;
     }
    .inc_dec{
        text-align: center;
        cursor: pointer;
    }
    .inc_dec_val{
        width: 50px;
        text-align-last: center;
    }
    .padding_top{
        padding-top:5px;
    }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1>POS - Point of Sales System</h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-edit"></i> Sales</a></li>
            <li class="active">pos</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <!-------------------------------------------------- Form ------------------------------------------>
            <form id="thisForm" name="thisForm" action="pos.php" onsubmit="return fnvalidate()" method="post">
            <div class="col-md-8">
                <div class="box box-primary">
                    <div class="box-header with-border">

                        <h3 class="box-title" style="margin-right: 20px;">
                        <?php if(isset($_REQUEST["id"])){
                            echo "Edit Order  ";
                        }else{
                            echo "Place Order  ";
                        }
                        ?>
                        </h3>
                        <a href="order.php" class="btn btn-info" style="margin-right: 20px;">Orders</a>
                        <a href="product.php" class="btn btn-warning">New item</a>
                    </div>
					<br />
                    
                        <div class="box-body">
                            <div class="row" style="padding-bottom:15px;">
                                <div class="col-md-3">
                                    <label class="col-form-label">Invoice No.</label>
                                    <input type="text" class="form-control" name="invoice_no" id="invoice_no" value="<?php echo $newOrderCode; ?>" readonly title="Invoice Number">
                                </div>
                                <div class="col-md-3">
                                    <label class="col-form-label">Date</label>
                                    <input type="date" class="form-control" name="sales_date" id="sales_date" value="" title="Sales Date">
                                </div>
                                <div class="col-md-4">
                                    <label class="col-form-label">Customer Name <span class="err">*</span></label>
                                    <input type="text" class="form-control" placeholder="Enter Custumer Name" name="cus_name" id="cus_name" value="<?php echo $cus_name; ?>" title="Customer Name">
                                </div>
                                <div class="col-md-2">
                                    <label class="col-form-label">Table <span class="err">*</span></label>
                                    <select class="form-control select2" name="table_name" id="table_name" title="Select the Table" required oninvalid="this.setCustomValidity('Please Select the Table...!')" onchange="this.setCustomValidity('')">
                                    <option value="">--- Select the option ---</option>
                                            <option value="T1">Table 1</option>
                                            <option value="T2">Table 2</option>
                                            <option value="T3">Table 3</option>
                                            <option value="T4">Table 4</option>
                                            <option value="T5">Table 5</option>
                                    </select>
                                    <script>
                                        document.thisForm.table_name.value = "<?php echo $table_name; ?>"
                                    </script>
                                </div>
                            </div>
                            <div class="col-md-12" style="background-color: #ECF0F5;">
                                <div class="row" style="padding:10px 0;">
                                    <div class="col-md-3">
                                        <label class="col-form-label">Product <span class="err">*</span></label>
                                        <select class="form-control select2" name="product_id" id="product_id" title="Select the Product">
                                            <option value="">--- Select the option ---</option>
                                            <?php
                                                $rs1 = $con->query("SELECT id,p_code,p_name FROM tbl_product WHERE del_status= '0'");
                                                while($obj1 = $rs1->fetch(PDO::FETCH_OBJ)){
                                                    echo'<option value="'.$obj1->id.'">'.$obj1->p_code.' - '.$obj1->p_name.' </option>'; 
                                                }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="col-form-label">UOM <span class="err">*</span></label>
                                        <select class="form-control select2" name="uom_id" id="uom_id" title="Select the UOM">
                                            <option value="">--- Select the option ---</option>
                                            <?php
                                                echo $dbcon->fnFillComboFromTable("id", "uom", "tbl_uom", "id");
                                            ?>
                                        </select>
                                    </div>

                                    <div class="col-md-2">
                                        <label class="col-form-label">Qty<span class="err">*</span></label>
                                        <input type="text" class="form-control NUMBER_ONLY" name="qty" id="qty" value="" placeholder='Enter the Quantity' title="Enter the Qty" onkeypress="return isNumberKey(event);">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="col-form-label">Rate</label>
                                        <input type="text" class="form-control text-right" name="rate" id="rate" value="" readonly title="Enter the Rate">
                                        <input type="hidden" class="form-control text-right" name="hidd_rate" id="hidd_rate" value="">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="col-form-label">Amount</label>
                                        <input type="text" class="form-control text-right" name="amount" id="amount" value="" readonly title="Enter the Amount">
                                        <input type="hidden" class="form-control text-right" name="hidd_amunt" id="hidd_amunt" value=""  title="">
                                    </div>
                                    <input type="hidden" id="row_count" value="<?php echo $cou; ?>"/>
                                    <div class="col-lg-1" style="padding-top: 24px;">
                                        <button class="btn btn-icon btn-success btn_add" style="width:35px; height:35px; padding:0px 8px; border-radius:0.12rem;" type="button" name="btn_add" id="btn_add"><i class="fa fa-plus-circle"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div id="" class="form-group">
                                <div id="pos_items">
                                    <table id="pos_table" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th width="12%" style="text-align: start;">Product Code</th>
                                                <th width="30%" style="text-align: start;">Product Name</th>    
                                                <th width="10%" class="te_al_ce">UOM</th>
                                                <th width="12%" class="te_al_ce">Selling Price</th>
                                                <th width="17%" class="te_al_ce">Count</th>
                                                <th width="12%" class="te_al_ce">Amount</th>
                                                <th class="te_al_ce">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr id="remove_tr" ><td colspan="7" style="text-align:center;">---- No One Records Added ----</td></tr>
                                        <?php 
                                            if(isset($_REQUEST["id"])){
                                                $row_count = 0;
                                                $item_ids_str ='';
                                                while($obj1 = $stmt1->fetch(PDO::FETCH_OBJ)){

                                                    $uom = $dbcon->GetOneRecord('tbl_uom','uom_name','id ="'.$obj1->uom_id.'" AND del_status',0);

                                                    $item_ids_str .="'$obj1->p_id'".',';
                                                    $p_name = $dbcon->GetOneRecord('tbl_product','p_name','id ="'.$obj1->p_id.'" AND del_status',0); 
                                                    $p_code = $dbcon->GetOneRecord('tbl_product','p_code','id ="'.$obj1->p_id.'" AND del_status',0); 
                                                    echo'<tr class="item_count te_al_ce" id="item_count_'.$row_count.'" style="text-a">
                                                        <td style="text-align: start;vertical-align: middle;">'.$p_code.'</td>
                                                        <td style="text-align: start;vertical-align: middle;">'.$p_name.'<input type="hidden" id="" name="p_id[]" value="'.$obj1->p_id.'"/></td>
                                                        <td style="vertical-align: middle;">'.$uom.'<input type="hidden" id="" name="p_uom[]" value="'.$obj1->uom_id.'"/></td>
                                                        <td style="vertical-align: middle;">'.$obj1->sale_price.' <input type="hidden" id="hidd_sales_price_'.$row_count.'" name="hidd_sales_price[]" value="'.$obj1->sale_price.'"/></td>
                                                        <td style="vertical-align: middle;"><div class="input-group"><lable class="col-form-label form-control inc_dec decreament" data-count="'.$row_count.'">-</lable><div class="input-group inc_dec_val"><input class="form-control input-group-append inc_dec_val inc_dec_input NUMBER_ONLY" data-count="'.$row_count.'" id="count_'.$row_count.'" name="count[]" value="'.$obj1->count.'"/></div><lable class="col-form-label form-control inc_dec increament" data-count="'.$row_count.'">+</lable></div></td>
                                                        <td style="vertical-align: middle;"><span id="sales_price_'.$row_count.'">'.$obj1->tot_sale_price.'</span><input type="hidden" class="sell_price_amount" name="sell_price_amount[]" id="sell_price_amount_'.$row_count.'" value="'.$obj1->tot_sale_price.'"/></td>
                                                        <td style="vertical-align: middle;"><a href="javascript:;" class="remove_item" p_id="'.$obj1->p_id.'" name="total_item" data-count="'.$row_count.'" ><i class="fa fa-trash-o"></i></a></td>
                                                    </tr>';
                                                    $row_count++;
                                                }

                                                $item_ids = rtrim($item_ids_str, ',');
                                            }                                    
                                        ?>
                                        </tbody>
                                        <tfoot>
                                            <tr style="font-weight: bold;">
                                                <td colspan="5" style="text-align: end;vertical-align: middle;">Total</td>
                                                <td style="text-align: center;font-color:black" id="tot_amount"><?php echo $tot_amount; ?></td><input type="hidden" id="hidd_tot_amount" name="hidd_tot_amount" value="<?php echo $tot_amount; ?>"/>
                                                <td class="" style="text-align: end;vertical-align: middle; border:0px"></td>

                                            </tr>
                                        </tfoot>
                                    </table>
                                    
                                </div>
                            </div>
                        </div>
                        <div class="box-footer">
							<div class="pull-right">
								<?php if (isset($_REQUEST["id"])) { ?>
								<input type="hidden" id="hid_id" value="<?php echo $id; ?>" name="hid_id">
								<button type="submit" name="update" class="btn btn-primary"><i class="fa fa-check" aria-hidden="true"></i> Update Order</button>
								<?php
								} else { ?>
								<button type="submit" name="submit" class="btn btn-primary"><i class="fa fa-check" aria-hidden="true"></i> Place Order</button>
								<?php } ?>
							</div>  
                        </div>
                    
                </div>
            </div>
            <!-- ----------------------------------------------- Form ------------------------------------------ -->
            <!-- --------------------------------------------- View -------------------------------------------- -->
            <div class="col-md-4">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Pay Bill</h3>
                    </div>
                    <input type="hidden" name="hid_sgst_amt" id="hid_sgst_amt" value="<?php echo $sgst_amt; ?>">
                    <input type="hidden" name="hid_cgst_amt" id="hid_cgst_amt" value="<?php echo $cgst_amt; ?>">
                    <input type="hidden" name="hid_igst_amt" id="hid_igst_amt" value="<?php echo $igst_amt; ?>">

                    <div class="box-body">
                        <div class="form-group row">
                            <label class="col-form-label col-md-4 padding_top">Total Amount </label>
                            <label class="col-form-label col-md-8 padding_top" id="tot_amount_html"><?php echo $tot_amount; ?></label>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-md-4 padding_top">Discount Type </label>
                            <div class="col-md-8">
                                <select class="form-control select2" placeholder="Select the Discount Type" id="discount_type" name="discount_type" title="Select the Discount Type">
                                    <option value="">--- Select the option ---</option>
                                    <option value="F">Fixed</option>
                                    <option value="P">Percentage</option>
                                </select>
                                <script>
                                    document.thisForm.discount_type.value = "<?php echo $discount_type; ?>"
                                </script>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-form-label col-md-4 padding_top">Discount </label>
                            <div class="col-md-8">
                                <input type="text" class="form-control NUMBER_ONLY" name="discount_amount" id="discount_amount" placeholder="Enter the Discount Amount" title="Enter the Discount Amount" autocomplete="off" maxlength="8" value="<?php echo $discount_amount; ?>">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-4 padding_top">Add GST </label>
                            <div class="col-md-8 padding_top"><a href="" data-toggle="modal" data-target="#modal_add_gst" title="Add GST" class="btn btn-xs bg-purple" style="color: black;">Add GST<span class="err">*</span></a></div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-4 padding_top">Total GST </label>
                            <div class="col-md-8 padding_top" style="font-weight: bold;"> <input onkeydown="return false;" type="text" name="total_gst_amt" id="total_gst_amt" value="<?php echo iif($obj->gst_amt == "", "0.00", $obj->gst_amt); ?>" class="input_borderless" size="7"></div>
                        </div>
                        <div class="form-group row">
                            <label class="col-form-label col-md-4 padding_top" >Rounded Amount</label>
                            <div class="col-md-8 padding_top" style="font-weight: bold;"><input onkeydown="return false;" type="text" name="round_amount" id="round_amount" value="<?php echo iif($obj->round_amount == "", "0.00", $obj->round_amount); ?>" class="input_borderless" size="7"></div>
                        </div>
                        <div class="form-group row">    
                            <label class="col-form-label col-md-4 padding_top">Grand Total </label>
                            <label class="col-form-label col-md-8 padding_top" id="grand_total"style="padding-left: 19px;"><?php echo $grand_total; ?></label>
                            <input type="hidden" id="hid_grand_total" name="hid_grand_total" value="<?php echo $grand_total; ?>">
                        </div>
                    </div>
                    <div class="box-footer">
                        <div class="pull-right">
                            <a href="#" class="btn btn-warning " style="margin-right: 15px;">Print</a>
                            <button type="submit" name="pay_bill" class="btn btn-primary"></i> Pay Bill</button>
                        </div>
                    </div>
                </div>

            </div>
            </form>
            <!-- ------------------------ View ---------------------------------- -->
        </div>
    </section>
</div>
<?php include("includes/footer.php"); ?>
<div class="modal fade" id="modal_add_gst" tabindex="-1" role="dialog" aria-hidden="true">
    <!-- modal-dialog -->
    <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
        <!-- modal-content -->
        <div class="modal-content">
            <form id="thisFrm" name="thisFrm" class="form-horizontal" action="" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title" id="myModal_fs_Head"><b>Default Modal</b></h4>
                </div>
                <div class="modal-body" id="m_fs_details">
                    <div class="text-center">
                        <span class="spinner-border spinner-border-sm text-danger"></span> Loading - Add Details Properly...!
                    </div>
                </div>
                <div class="modal-footer" id="m_fs_button">
                    <button type="button" name="submit_gst" id="submit_gst" class="btn btn-primary" disabled>Submit</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<script>
 var item_count = $('.item_count').length;
if(item_count == 0){
    $("#remove_tr").show()
}else{
    $("#remove_tr").hide()
}
$('#modal_add_gst').on('show.bs.modal', function (e){
    var id = parseFloat($('#hid_grand_total').val());

    // var id = parseFloat($('#hidd_tot_amount').val());
    
    if(id != 0){

        $.ajax({
            type : 'post',
            url : 'ajax/get_list.php',
            data: {
                mode: 'modal_add_gst',
                id: id,
            },
            success : function(data){
                data_arr = data.split("~");
                $('#myModal_fs_Head').html(data_arr[0]);
                $('#m_fs_details').html(data_arr[1]);
                $('#submit_gst').prop('disabled', false);
            }
        });

    }
});

$(document).on('change', '#sgst_id', function() {
    var gst_id = $(this).val();
    var tot_amt = parseFloat($('#total_amt').val());
    if(gst_id != '')
    {
        $.ajax({
            type : 'post',
            url : 'ajax/get_list.php',
            data: {
                mode: 'calculate_sgst',
                id: gst_id +'~'+ tot_amt,
            }
        }).done(function(msg) {
            //alert(msg);
            $('#sgst_amt').val(parseFloat(msg).toFixed(2));
            $('#hid_sgst_amt').val(parseFloat(msg).toFixed(2));
            calc_total_gst();
        });
    }
});

$(document).on('change', '#cgst_id', function() {
    var gst_id = $(this).val();
    var tot_amt = parseFloat($('#total_amt').val());

    if(gst_id != '')
    {
        $.ajax({
            type : 'post',
            url : 'ajax/get_list.php',
            data: {
                mode: 'calculate_cgst',
                id: gst_id +'~'+ tot_amt,
            }
        }).done(function(msg) {
            //alert(msg);
            $('#cgst_amt').val(parseFloat(msg).toFixed(2));
            $('#hid_cgst_amt').val(parseFloat(msg).toFixed(2));
            calc_total_gst();
        });
    }
});

$(document).on('change', '#igst_id', function() {
    var gst_id = $(this).val();
    var tot_amt = parseFloat($('#total_amt').val());

    if(gst_id != '')
    {
        $.ajax({
            type : 'post',
            url : 'ajax/get_list.php',
            data: {
                mode: 'calculate_igst',
                id: gst_id +'~'+ tot_amt,
            }
        }).done(function(msg) {
            //alert(msg);
            $('#igst_amt').val(parseFloat(msg).toFixed(2));
            $('#hid_igst_amt').val(parseFloat(msg).toFixed(2));
            calc_total_gst();
        });
    }
});

function calc_total_gst(){
    var sgst_amt = parseFloat($('#sgst_amt').val());
    if (isNaN(sgst_amt)) sgst_amt = 0;
    var cgst_amt = parseFloat($('#cgst_amt').val());    
    if (isNaN(cgst_amt)) cgst_amt = 0;
    var igst_amt = parseFloat($('#igst_amt').val());
    if (isNaN(igst_amt)) igst_amt = 0;
    var total_gst = sgst_amt + cgst_amt + igst_amt;
    $('#total_gst').val(parseFloat(total_gst).toFixed(2));
}


$(document).on('click', '#submit_gst', function() {

    if($('#sgst_id').val() == "" || $('#cgst_id').val() == "" || $('#igst_id').val() == ""){
        if($('#sgst_id').val() == ""){
            alert("Please select the sgst..!");
            $('#sgst_id').select2('open');
            return false; 
        }
        if($('#cgst_id').val() == ""){
            alert("Please select the cgst..!");
            $('#cgst_id').select2('open');
            return false; 
        }
        if($('#igst_id').val() == ""){
            alert("Please select the igst..!");
            $('#igst_id').select2('open');
            return false; 
        }
    }
    // var total = $('#hidd_tot_amount').val();
    var total = parseFloat($('#hid_grand_total').val());
    var total_gst = $('#total_gst').val();
    $('#total_gst_amt').val(total_gst);
    var final_amount = parseFloat(total) + parseFloat(total_gst);
    var total_amount = Math.round(final_amount.toFixed(2));
    var round_amount = parseFloat(total_amount) - parseFloat(final_amount);
    $('#round_amount').val(round_amount.toFixed(2));
    $('#hid_grand_total').val(total_amount.toFixed(2));
    $('#grand_total').html(total_amount.toFixed(2));
    $('#modal_add_gst').modal('hide');
    
});

//------------------ This is for Select2 in modal popup --------------------
$('body').on('shown.bs.modal', '.modal', function() {
    $(this).find('select').each(function() {
        var dropdownParent = $(document.body);
        if ($(this).parents('.modal.in:first').length !== 0)
            dropdownParent = $(this).parents('.modal.in:first');
        $(this).select2({
            placeholder: 'Select the Options',
            allowClear: true,
            dropdownParent: dropdownParent
        });
    });
});

$(document).on('click','#btn_add',function(event){
    var pro_id = $('#product_id').val();
    product_dets(pro_id);
    $('#total_gst_amt,#round_amount,#hid_sgst_amt,#hid_cgst_amt,#hid_igst_amt').val('0.00');
});
$(document).on('change','#qty',function(event){
    var pro_id = $('#product_id').val();
    ind_item_cal(pro_id);
});


function ind_item_cal(p_id){
    
    if(p_id !=''){
        var qty = parseFloat($('#qty').val()) || 0;
        var rate = parseFloat($('#hidd_rate').val()) || 0;
        var cal_price = qty * rate;
        $('#amount,#hidd_amunt').val(cal_price.toFixed(2)); 
    }
}

$(document).on('change', '#product_id', function(event) {
    var pro_id = $('#product_id').val();
    if(pro_id != ''){
        $.ajax({
        method: "post",
        url: "ajax/sales_pro_cal.php",
        data: {
            'value': pro_id,
            'mode': "pro_single_cal"
        },
        success: function(data) {
            var spl_data = data.split('~');
            $('#uom_id').val($.trim(spl_data[0])).trigger('change'); 
            $('#rate, #hidd_rate').val(parseFloat($.trim(spl_data[1])).toFixed(2));
            ind_item_cal(pro_id);
        }
    });
    }
    
});


var item_ids = [];
var hid_id = $('#hid_id').val();

if(hid_id > 0){
    item_ids.push(<?php echo $item_ids; ?>);
}

function product_dets(p_id){

    if((p_id == '') || (p_id == null) ){
        alert("Please Select Product ...!");
        $('#product_id').select2('open');
        return false;
    }
    var row_count = $('#row_count').val();
    if (jQuery.inArray(p_id, item_ids) != -1) {
        alert("Item already added");
        return false;
    } else {
        var uom_id = $('#uom_id').val();
        if((uom_id == '') || (uom_id == null) ){
            alert("Please Select UOM ...!");
            $('#uom_id').select2('open');
            return false;
        }
        var qty = $('#qty').val();
        if((qty == '') || (qty == null) ){
            alert("Please Enter Quantity ...!");
            $('#qty').focus();
            return false;
        }
        if(qty == 0){
            alert("At least one Quentity must be added");
            $('#qty').val(1)
            return false;
        }
        item_ids.push(p_id);
        $.ajax({
            method:"post",
            url:"ajax/sales_pro_cal.php",
            data:{
                'value':p_id,
                'row_count':row_count,
                'qty':qty,
                'uom_id':uom_id,
                'mode': "pro_item_cal"
            }
        }).done(function(data){
            $('#search_item').val('').focus();
            var spl_data = data.split('~');
            $("#pos_table tbody").append(spl_data[0])
            $("#row_count").val(spl_data[1])
            var item_count = $('.item_count').length;
            if(item_count == 0){
                $("#remove_tr").show()
            }else{
                $("#remove_tr").hide()
            }
            item_cal()
            discount_amount()
            $('#qty,#rate,#hidd_rate,#amount,#hidd_amunt').val('');
            $('#product_id,#uom_id').val(null).trigger('change');
        })
    }
}

function item_cal(){
   var rowcount = $('#row_count').val();
    var tot_val = 0
   for(var i = 0; i<=rowcount; i++){
    var a = parseFloat($('#sell_price_amount_'+i).val()) || 0;
    tot_val += a;
   }
   $('#tot_amount,#tot_amount_html,#grand_total').html(tot_val.toFixed(2));
   $('#hidd_tot_amount,#hid_grand_total').val(tot_val.toFixed(2));
}

function discount_amount(){
    var hidd_tot_amount = parseFloat($('#hidd_tot_amount').val());
    var discount_type = $('#discount_type').val();
    var discount_amount = parseFloat($('#discount_amount').val());

    var item_count = $('.item_count').length;

    if((discount_type !== '' && discount_type !== null) || (!isNaN(discount_amount) && discount_amount !== 0)){
        if(discount_type == 'F' && !isNaN(discount_amount) && !isNaN(hidd_tot_amount) && hidd_tot_amount !== 0){
            
            if(hidd_tot_amount < discount_amount){
                alert("Please Enter Less then total amount");
                $('#discount_amount').val('').focus();
                return false;
            }else{
                var grand_total = hidd_tot_amount - discount_amount;
                $('#grand_total').html(grand_total.toFixed(2));
                $('#hid_grand_total').val(grand_total.toFixed(2));
            }
        }else if(discount_type == 'P' && !isNaN(discount_amount) && !isNaN(hidd_tot_amount) && hidd_tot_amount !== 0){
        if(discount_amount > 100){
                alert("Please Enter less then 100%");
                $('#discount_amount').val('').focus();
                return false;
            }
            var percentage_cal = (hidd_tot_amount/100) * discount_amount;
            var percentage = hidd_tot_amount - percentage_cal;
            $('#grand_total').html(percentage.toFixed(2));
            $('#hid_grand_total').val(percentage.toFixed(2));
        }else{
            item_cal()
        }

        
    }
}

function inc_dec(data){
    var sel_price = parseFloat($('#hidd_sales_price_'+data).val());
    var count = parseFloat($('#count_'+data).val());
    var cal_value =  sel_price * count;
    $('#sales_price_'+data).html(cal_value.toFixed(2));
    $('#sell_price_amount_'+data).val(cal_value.toFixed(2));
}


$(document).on('click','.increament',function(){
    var s_no = $(this).data('count')
    var value = parseFloat($('#count_'+s_no).val())
    $('#count_'+s_no).val(value+1)
    inc_dec(s_no);
    item_cal()
    discount_amount()
    $('#total_gst_amt,#round_amount,#hid_sgst_amt,#hid_cgst_amt,#hid_igst_amt').val('0.00');

});
$(document).on('click','.decreament',function(){
    var s_no = $(this).data('count')
    var value = parseFloat($('#count_'+s_no).val())
    if(value <= 1){
        alert("At least one item must be added");
        return false;
    }
    $('#count_'+s_no).val(value-1)
    inc_dec(s_no);
    item_cal()
    discount_amount()
    $('#total_gst_amt,#round_amount,#hid_sgst_amt,#hid_cgst_amt,#hid_igst_amt').val('0.00');

});
$(document).on('change','.inc_dec_input',function(){
    var s_no = $(this).data('count')
    if($(this).val() ==''){
        $(this).val(1)
    }
    if($(this).val() == 0){
        alert("At least one item must be added");
        $(this).val(1)
        return false;
    }
    inc_dec(s_no);
    item_cal()
    discount_amount()
    $('#total_gst_amt,#round_amount,#hid_sgst_amt,#hid_cgst_amt,#hid_igst_amt').val('0.00');

});

function remove_item(data){
    $('#item_count_'+data).remove();
    inc_dec(data);
    item_cal()
    discount_amount()
    var item_count = $('.item_count').length;
    if(item_count == 0){
        // $('#pos_table').remove();
        $("#remove_tr").show()
    }else{
        $("#remove_tr").hide()
    }
}
$(document).on('click','.remove_item',function(){
    var s_no = $(this).data('count')
    var p_id = $(this).attr('p_id')
    remove_item(s_no)
    item_ids = jQuery.grep(item_ids, function(value) {
        return value != p_id;
    });
    $('#total_gst_amt,#round_amount,#hid_sgst_amt,#hid_cgst_amt,#hid_igst_amt').val('0.00');

});

$(document).on('change','#discount_amount,#discount_type',function(){
    discount_amount();
    var discounts = $('#discount_amount').val();
    var discount_type = $('#discount_type').val();
    $('#total_gst_amt,#round_amount,#hid_sgst_amt,#hid_cgst_amt,#hid_igst_amt').val('0.00');
});


function fnvalidate(){
    var discount_type = $('#discount_type').val();
    var discount_amount = parseFloat($('#discount_amount').val());
    if(((discount_type !='') || (discount_type != null)) && isNaN(discount_amount)){
        if(discount_type == 'F'){
            alert("Please Enter Discount Amount ...!")
            return false;
        }if(discount_type == 'P'){
            alert("Please Enter Discount Percentage ...!")
            return false;
        }
    } 
    if(((discount_type =='') || (discount_type == null)) && !isNaN(discount_amount)){
        alert("Please Select Discount Type ...!")
        $('#discount_type').click();
        return false;
    }

    var item_count = $('.item_count').length;
    if(item_count == 0){
        alert('No items added. Please add at least one item.');
        return false;
    }
    return true;
}
</script>
<script type="text/javascript">
$(document).ready(function() {
    $('#masterMainNav').addClass('active');
    $('#masterGstSubNav').addClass('active');
});
</script>
