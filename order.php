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
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="12">S.No.</th>
                                        <th width="" class="text-center">Order No.</th>
                                        <th class="text-center">Customer Name</th>
                                        <th class="text-center">Total Amount </th>
                                        <th class="text-center">Discount Amount</th>
                                        <th class="text-center">GST Amount</th>
                                        <th class="text-center">Grand Total</th>
                                        <th width="90px" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
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
            data:{"ord_id":ord_id,"mode":"complete_bill"},
            beforeSend:function(){
                return confirm("Are you sure?");
            },
        }).done(function(data){
            toastr.success(data);
            // location.reload();
            // data
            datatable.draw();
            // $('#complete_bill_'+ord_id).remove();
        })
    })


    var datatable = $('#example1').DataTable( {
        paging: true,
        select: true,
        searching: true,
        serverSide: true,
        // "draw": 1,
        // "pageLength": 50,
        ajax:{
            method:'post',
            url: 'datatable/order_list.php',
            data:function(data){
                data.vals="sdfg"
            }
        },
        columns: [
            { data: 'id' },
            { data: 'order_no' },
            { data: 'cus_name' },
            { data: 'tot_amount' },
            { data: 'discount_amount' },
            { data: 'gst_amt' },
            { data: 'grand_total' },
            { data: 'action' }
        ],
        "aoColumnDefs": [
            { "bSortable": false, "aTargets": [0,7] },
        ]
    });
</script>

<script type="text/javascript">
$(document).ready(function() {
    $('#masterMainNav').addClass('active');
    $('#masterGstSubNav').addClass('active');
});
</script>
