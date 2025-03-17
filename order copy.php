<?php
ob_start();
session_start();

require_once("includes/common/connection.php");
require_once("includes/common/dbfunctions.php");
require_once("includes/common/functions.php");

$con = new connection();
$dbcon = new dbfunctions();
$converter = new Encryption;

ini_set('display_errors', '1'); ini_set('display_startup_errors', '1'); error_reporting(E_ALL);

isAdmin();

$stmt = null;
$stmt = $con->query("SELECT * FROM tbl_sales WHERE del_status = '0' AND order_status = '1' ");


?>

<?php include("includes/header.php"); ?>
<?php include("includes/aside.php"); ?>
<style>
    .row_lable_gap{padding-bottom: 14px;}
</style>
<div class="content-wrapper">
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-edit"></i> Sales</a></li>
            <li class="active">Orders</li>
        </ol>
    </section><br>
    <section class="content">
        <div class="row">
            <!-------------------------------------------------- Form ------------------------------------------>
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">

                        <h3 class="box-title" style="margin-right: 20px;">
                            Orders
                        </h3>
                        <a href="pos.php" class="btn btn-info" style="margin-right: 20px;">New Orders</a>
                    </div>
                    <form id="thisForm" name="thisForm" action="order.php" method="post">
                        <div class="box-body">
                            <div class="row">
                                <?php  
                                 if($stmt->rowCount() >0){
                                    while($obj = $stmt->fetch(PDO::FETCH_OBJ)){
                                   
                                ?>
                                <div id="complete_bill_<?php echo $obj->id?>" class="col-md-3">
                                    <div class="box box-info">
                                        <div class="box-header with-border">
                                        <div><b><?php echo $obj->order_no; ?></b></div>
                                        </div>
                                        <div class="box-body">
                                            <div class="row_lable_gap"><lable>Table Name : <?php echo $obj->table_name ?></lable></div>
                                            <div class="row_lable_gap"><lable >Total Amount : <?php echo $obj->tot_amount ?></lable></div>
                                        </div>
                                        <input name="order_id"  value="<?php echo $obj->id ?>"/>
                                        <div class="box-footer">
                                            <div class="pull-right">
                                                <a href="pos.php?id=<?php echo $converter->encode($obj->id); ?>" class="btn btn-info btn-sm" style="margin-right: 20px;">Edit</a>
                                                <a href="javascript:;" class="btn btn-warning btn-sm complete_bill" data-id="<?php echo $obj->id ?>" ><i class="fa fa-check" aria-hidden="true"></i> Complete Order</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php 
                                    }
                                    }else{?>
                                        <h3 style="padding-left: 15px;">
                                            No Orders Found
                                        </h3>
                                    <?php }
                                ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- ----------------------------------------------- Form ------------------------------------------ -->
            <!-- --------------------------------------------- View -------------------------------------------- -->
            
            <!-- ------------------------ View ---------------------------------- -->
        </div>
    </section>
</div>
<?php include("includes/footer.php"); ?>
<script>

    $(document).on('click','.complete_bill',function(){

        var ord_id = $(this).data("id");
        $.ajax({
            method:'post',
            url:"ajax/complete_order.php",
            data:{"ord_id":ord_id,"mode":"complete_bill"}
        }).done(function(data){
            // toastr.success(data);
            location.reload();
            // $('#complete_bill_'+ord_id).remove();
        })

    })
    // toastr.options = {
    //     "closeButton": true,
    //     "newestOnTop": false,
    //     "progressBar": true,
    //     "positionClass": "toast-top-right",
    //     "preventDuplicates": false,
    //     "onclick": null,
    //     "showDuration": "300",
    //     "hideDuration": "1000",
    //     "timeOut": "2000",
    //     "extendedTimeOut": "1000",
    //     "showEasing": "swing",
    //     "hideEasing": "linear",
    //     "showMethod": "fadeIn",
    //     "hideMethod": "fadeOut"
    // }
</script>

<script type="text/javascript">
$(document).ready(function() {
    $('#masterMainNav').addClass('active');
    $('#masterGstSubNav').addClass('active');
});
</script>
