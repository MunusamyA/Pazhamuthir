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
        $discount = ($_REQUEST["discount"] !='') ? $_REQUEST["discount"] : null;
        $stmt = null;
        $stmt = $con->prepare("INSERT INTO tbl_sales (table_name, tot_amount, order_status, order_no, discount_type, discount, grand_total, created_by, created_dt) 
              VALUES (:table_name, :tot_amount, :order_status, :order_no, :discount_type, :discount, :grand_total, :created_by, :created_dt)");
        $data = array(
            ":table_name" => trim($_REQUEST["table_name"]),
            ":tot_amount" => trim($_REQUEST["hidd_tot_amount"]),
            ":order_status" => 1,
            ":order_no" => $newOrderCode,
            ":discount_type" => $discount_type,
            ":discount" => $discount,
            ":grand_total" => trim($_REQUEST["hid_grand_total"]),
            ":created_by" => $created_by,
            ":created_dt" => $created_dt,
        );

        $stmt->execute($data);

        $sale_id = $con->lastInsertId();

        $total_item = count($_REQUEST['sell_price_amount']);
        $stmt1 = null;
        $stmt1 = $con->prepare("INSERT INTO tbl_sales_dts_temp (sale_id, p_id, sale_price, count, tot_sale_price) 
              VALUES (:sale_id, :p_id, :sale_price, :count, :tot_sale_price)");
        for($i=0;$i<$total_item;$i++){
            $data1 = array(
                ":sale_id" => $sale_id,
                ":p_id" => trim($_REQUEST["p_id"][$i]),
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
        $discount = ($_REQUEST["discount"] !='') ? $_REQUEST["discount"] :null;
        $stmt = null;
        $stmt = $con->prepare("UPDATE tbl_sales SET table_name=:table_name,tot_amount=:tot_amount,discount_type=:discount_type,discount=:discount,grand_total=:grand_total,updated_by=:updated_by,updated_dt=:updated_dt where id=:id");
        $data = array(
            ":table_name" => trim($_REQUEST["table_name"]),
            ":tot_amount" => trim($_REQUEST["hidd_tot_amount"]),
            ":discount_type" => $discount_type,
            ":discount" => $discount,
            ":grand_total" => trim($_REQUEST["hid_grand_total"]),
            ":updated_by" => $updated_by,
            ":updated_dt" => $updated_dt,
            ":id" => trim($_REQUEST["hid_id"])
        );
        $stmt->execute($data);
        $con->query("DELETE FROM tbl_sales_dts_temp WHERE sale_id =". $_REQUEST['hid_id']);
        $total_item = count($_REQUEST['sell_price_amount']);
        $stmt1 = null;
        $stmt1 = $con->prepare("INSERT INTO tbl_sales_dts_temp (sale_id, p_id, sale_price, count, tot_sale_price) 
              VALUES (:sale_id, :p_id, :sale_price, :count, :tot_sale_price)");
        for($i=0;$i<$total_item;$i++){
            $data1 = array(
                ":sale_id" => trim($_REQUEST["hid_id"]),
                ":p_id" => trim($_REQUEST["p_id"][$i]),
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
            $discount = ($_REQUEST["discount"] !='') ? $_REQUEST["discount"] :null;
            $stmt = null;
            $stmt = $con->prepare("UPDATE tbl_sales SET table_name=:table_name,tot_amount=:tot_amount,order_status=:order_status,discount_type=:discount_type,discount=:discount,grand_total=:grand_total,updated_by=:updated_by,updated_dt=:updated_dt where id=:id");
            $data = array(
                ":table_name" => trim($_REQUEST["table_name"]),
                ":tot_amount" => trim($_REQUEST["hidd_tot_amount"]),
                ":order_status" => 2,
                ":discount_type" => $discount_type,
                ":discount" => $discount,
                ":grand_total" => trim($_REQUEST["hid_grand_total"]),
                ":updated_by" => $updated_by,
                ":updated_dt" => $updated_dt,
                ":id" => trim($_REQUEST["hid_id"])
            );
            $stmt->execute($data);
            $con->query("DELETE FROM tbl_sales_dts_temp WHERE sale_id =". $_REQUEST['hid_id']);
            $total_item = count($_REQUEST['sell_price_amount']);
            $stmt1 = null;
            $stmt1 = $con->prepare("INSERT INTO tbl_sales_dts (sale_id, p_id, sale_price, count, tot_sale_price) 
                  VALUES (:sale_id, :p_id, :sale_price, :count, :tot_sale_price)");
            for($i=0;$i<$total_item;$i++){
                $data1 = array(
                    ":sale_id" => trim($_REQUEST["hid_id"]),
                    ":p_id" => trim($_REQUEST["p_id"][$i]),
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
        // echo "qd";die;
        try{
            $rs = $con->query("SELECT order_no FROM tbl_sales ORDER BY id DESC LIMIT 1");
            $obj = $rs->fetch(PDO::FETCH_OBJ);
            $newOrderCode = generateOrderCode($obj->order_no);
            $created_by = $_SESSION["user_id"];
            $created_dt = date('Y-m-d H:i:s');
            $discount_type = ($_REQUEST["discount_type"] !='') ? $_REQUEST["discount_type"] : null;
            $discount = ($_REQUEST["discount"] !='') ? $_REQUEST["discount"] :null;
            $stmt = null;
            $stmt = $con->prepare("INSERT INTO tbl_sales (table_name, tot_amount, order_status, order_no, discount_type, discount, grand_total, created_by, created_dt) 
                  VALUES (:table_name, :tot_amount, :order_status, :order_no, :discount_type, :discount, :grand_total, :created_by, :created_dt)");
            $data = array(
                ":table_name" => trim($_REQUEST["table_name"]),
                ":tot_amount" => trim($_REQUEST["hidd_tot_amount"]),
                ":order_status" => 2,
                ":order_no" => $newOrderCode,
                ":discount_type" => $discount_type,
                ":discount" => $discount,
                ":grand_total" => trim($_REQUEST["hid_grand_total"]),
                ":created_by" => $created_by,
                ":created_dt" => $created_dt,
            );
    
            $stmt->execute($data);
    
            $sale_id = $con->lastInsertId();
    
            $total_item = count($_REQUEST['sell_price_amount']);
            $stmt1 = null;
            $stmt1 = $con->prepare("INSERT INTO tbl_sales_dts (sale_id, p_id, sale_price, count, tot_sale_price) 
                  VALUES (:sale_id, :p_id, :sale_price, :count, :tot_sale_price)");
            for($i=0;$i<$total_item;$i++){
                $data1 = array(
                    ":sale_id" => $sale_id,
                    ":p_id" => trim($_REQUEST["p_id"][$i]),
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
            $discount = ($obj->discount !='') ? $obj->discount :'';
            $discount_type = ($obj->discount_type !='') ? $obj->discount_type :'';
            $grand_total = ($obj->grand_total !='') ? $obj->grand_total :'';
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
    table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    thead th, tfoot td {
        background-color: #f2f2f2;
        position: sticky;
        top: 0;
        z-index: 1;
        text-align:center;
    }

    tbody {
        display: block;
        max-height: 200px; /* Set desired max-height for scrolling */
        overflow-y: auto;  /* Allow vertical scrolling */
    }
    tbody td{
        text-align:center;
    }
    thead, tbody tr {
        display: table;
        width: 100%;
        table-layout: fixed;
    }
    tfoot{
        display: table;
        width: 100%; 
    }
    th, td {
        padding: 6px 18px;
        border: 1px solid #ddd;
        text-align: left;
        white-space: nowrap; /* Prevent text wrapping */
    }
    tbody::-webkit-scrollbar {
        display: none; /* Hide scrollbar for Chrome, Safari, and Edge */
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
                                    <input type="text" class="form-control" name="cus_name" id="cus_name" value="" title="Customer Name">
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
                                        <label class="col-form-label">Name of the Particulars <span class="err">*</span></label>
                                            <select class="form-control select2" name="particulars" id="particulars" title="Select the Particulars">
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="col-form-label">UOM <span class="err">*</span></label>
                                        <select class="form-control select2" name="uom_id" id="uom_id" title="Select the UOM">
                                            <option value=""></option>
                                            <?php
                                            echo $dbcon->fnFillComboFromTable("id", "uom", "tbl_uom", "id");
                                            ?>
                                        </select>
                                    </div>

                                    <div class="col-md-2">
                                        <label class="col-form-label">Qty<span class="err">*</span></label>
                                        <input type="text" class="form-control" name="qty" id="qty" value="" title="Enter the Qty" onkeypress="return isNumberKey(event);">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="col-form-label">Rate</label>
                                        <input type="text" class="form-control text-right" name="rate" id="rate" value="" readonly title="Enter the Rate">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="col-form-label">Amount</label>
                                        <input type="text" class="form-control text-right" name="amount" id="amount" value="" readonly title="Enter the Amount">
                                    </div>
                                    <div class="col-lg-1" style="padding-top: 24px;">
                                        <button class="btn btn-icon btn-success btn_add" style="width:35px; height:35px; padding:0px 8px; border-radius:0.12rem;" type="button" name="btn_add" id="btn_add"><i class="fa fa-plus-circle"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div id="" class="form-group">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="supp_id" name="supp_id" readonly value="<?php echo $_SESSION["uname"] ?>">
                                    <!-- <input type="text" class="form-control" id="" name="" placeholder="Enter the Sale Price" title="Enter the Sale Price" required oninvalid="this.setCustomValidity('Please Enter the Sale Price...!')" onchange="this.setCustomValidity('')" value="<?php echo $sales_price ?>"> -->
                                    <!-- <div class="input-group-append">
                                        <select class="form-control select2" placeholder="Select the Table" id="table_name" name="table_name" title="Select the Table"  required oninvalid="this.setCustomValidity('Please select the Table...!')" onchange="this.setCustomValidity('')">
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
                                    </div> -->
                                </div>
                                <input type="text" id="search_item" class="form-control search-box" placeholder="Search..." />
                                
                                <div id="Product_items">
                                </div><br>
                                <div id="pos_items">
                                    <?php 
                                        if(isset($_REQUEST["id"])){
                                            echo '<table id="pos_table" class="">
                                                <thead>
                                                    <tr>
                                                        <th style="text-align: start;">Item</th>
                                                        <th>Selling Price</th>
                                                        <th>Count</th>
                                                        <th>Amount</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>';
                                                $row_count = 0;
                                                $item_ids_str ='';
                                                while($obj1 = $stmt1->fetch(PDO::FETCH_OBJ)){
                                                    $item_ids_str .="'$obj1->p_id'".',';
                                                    $p_name = $dbcon->GetOneRecord('tbl_product','p_name','id ="'.$obj1->p_id.'" AND del_status',0); 
                                                    echo'<tr class="item_count" id="item_count_'.$row_count.'" style="text-a">
                                                        <td style="text-align: start;">'.$p_name.'<input type="hidden" id="" name="p_id[]" value="'.$obj1->p_id.'"/></td>
                                                        <td>'.$obj1->sale_price.' <input type="hidden" id="hidd_sales_price_'.$row_count.'" name="hidd_sales_price[]" value="'.$obj1->sale_price.'"/></td>
                                                        <td><div class="input-group"><lable class="col-form-label form-control inc_dec decreament" data-count="'.$row_count.'">-</lable><div class="input-group inc_dec_val"><input class="form-control input-group-append inc_dec_val inc_dec_input NUMBER_ONLY" data-count="'.$row_count.'" id="count_'.$row_count.'" name="count[]" value="'.$obj1->count.'"/></div><lable class="col-form-label form-control inc_dec increament" data-count="'.$row_count.'">+</lable></div></td>
                                                        <td ><span id="sales_price_'.$row_count.'">'.$obj1->tot_sale_price.'</span><input type="hidden" class="sell_price_amount" name="sell_price_amount[]" id="sell_price_amount_'.$row_count.'" value="'.$obj1->tot_sale_price.'"/></td>
                                                        <td><a href="javascript:;" class="remove_item" p_id="'.$obj1->p_id.'" name="total_item" data-count="'.$row_count.'" ><i class="fa fa-trash-o"></i></a></td>
                                                    </tr>';
                                                    $row_count++;
                                                }

                                                $item_ids = rtrim($item_ids_str, ',');
                                                echo'</tbody>
                                                <tfoot>
                                                    <tr>
                                                        <td style="width:60%;">Total</td>
                                                        <td id="tot_amount">'.$tot_amount.'</td><input type="hidden" id="hidd_tot_amount" name="hidd_tot_amount" value="'.$tot_amount.'"/>
                                                    </tr>
                                                </tfoot>
                                            </table>';
                                            
                                        }                                    
                                    ?>
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
                                    <input type="text" class="form-control NUMBER_ONLY" name="discount" id="discount" placeholder="Enter the discount" title="Enter the discount" autocomplete="off" maxlength="8" value="<?php echo $discount; ?>">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-form-label col-md-4 padding_top">Grand Total </label>
                                <label class="col-form-label col-md-8 padding_top" id="grand_total"><?php echo $grand_total; ?></label>
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
<script>
$('#search_item').focus();
    $(document).on('keyup','#search_item',function(event){
        var value = $(this).val();

        $.ajax({
            method: "POST",
            url: "ajax/get_pro_items.php",
            data: { 'value': value, mode: "search_item" }
        }).done(function(dat){
            $('#Product_items').html(dat);

        })
    });


var item_ids = [];
var hid_id = $('#hid_id').val();

if(hid_id > 0){
    item_ids.push(<?php echo $item_ids; ?>);
}
// console.log(item_ids);
function product_dets(p_id){
    var item_count = $('.item_count').length;
    var row_count = $('#row_count').val();
    if (jQuery.inArray(p_id, item_ids) != -1) {
        alert("Item already added");
        return false;
    } else {
        item_ids.push(p_id);
        // console.log(item_ids);
        $.ajax({
            method:"post",
            url:"ajax/sales_pro_cal.php",
            data:{
                'value':p_id,
                'row_count':row_count,
                'item_count':item_count
            }
        }).done(function(data){
            $('#search_item').val('').focus();
            var spl_data = data.split('~');
            if(item_count == 0){
                $("#pos_items").html(spl_data[0])
            }
            
            $("#pos_table tbody").append(spl_data[1])
            $("#row_count").val(spl_data[2])
            item_cal()
            discount()
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

function discount(){
    var hidd_tot_amount = parseFloat($('#hidd_tot_amount').val());
    var discount_type = $('#discount_type').val();
    var discount = parseFloat($('#discount').val());
    if((discount_type !== '' && discount_type !== null) || (!isNaN(discount) && discount !== 0)){
        if(discount_type == 'F' && !isNaN(discount) && !isNaN(hidd_tot_amount)){
            if(hidd_tot_amount < discount){
                alert("Please Enter Less then total amount");
                $('#discount').val('').focus();
                return false;
            }else{
                var grand_total = hidd_tot_amount - discount;
                $('#grand_total').html(grand_total.toFixed(2));
                $('#hid_grand_total').val(grand_total.toFixed(2));
            }
        }else if(discount_type == 'P' && !isNaN(discount) && !isNaN(hidd_tot_amount)){
        if(discount > 100){
                alert("Please Enter less then 100%");
                $('#discount').val('').focus();
                return false;
            }
            var percentage_cal = (hidd_tot_amount/100) * discount;
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


function selectOption(option) { 
    var pro_id = option.getAttribute('cal');
    product_dets(pro_id);
    document.getElementById('optionList').style.display = "none";
}



$(document).on('click','.increament',function(){
    var s_no = $(this).data('count')
    var value = parseFloat($('#count_'+s_no).val())
    $('#count_'+s_no).val(value+1)
    inc_dec(s_no);
    item_cal()
    discount()
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
    discount()
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
    discount()
});

function remove_item(data){
    $('#item_count_'+data).remove();
    inc_dec(data);
    item_cal()
    discount()
    var item_count = $('.item_count').length;
    if(item_count == 0){
        $('#pos_table').remove();
    }

}
$(document).on('click','.remove_item',function(){
    var s_no = $(this).data('count')
    var p_id = $(this).attr('p_id')
    remove_item(s_no)
    item_ids = jQuery.grep(item_ids, function(value) {
        return value != p_id;
    });
});
$(document).on('change','#discount,#discount_type',function(){
    discount();
});

function fnvalidate(){
    var discount_type = $('#discount_type').val();
    var discount = parseFloat($('#discount').val());
    if(((discount_type !='') || (discount_type != null)) && isNaN(discount)){
        if(discount_type == 'F'){
            alert("Please Enter Discount Amount ...!")
            return false;
        }if(discount_type == 'P'){
            alert("Please Enter Discount Percentage ...!")
            return false;
        }
    } 
    if(((discount_type =='') || (discount_type == null)) && !isNaN(discount)){
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
