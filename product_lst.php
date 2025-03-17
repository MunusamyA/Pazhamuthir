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

//---------------------------------delete----------------------------------------
if (isset($_REQUEST["did"])) {
    $stmt = null;
    $stmt = $con->query("UPDATE tbl_product SET del_status=1 WHERE id=" . $converter->decode($_REQUEST["did"]));

    $_SESSION["msg"] = "Deleted Successfully";

    header("location:product_lst.php");
    die();
}
?>

<?php include("includes/header.php"); ?>
<?php include("includes/aside.php"); ?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Product</h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-edit"></i> Masters</a></li>
            <li class="active">Product</li>
        </ol>
    </section>
    <section class="content">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">List</h3>
                    <div class="box-tools pull-right">
                        <a href="product.php" class="btn btn-block btn-primary"><i class="fa fa-plus-circle" aria-hidden="true"></i></a>
                    </div>
                </div>
                <div class="box-body">
                    <div class="dt-responsive table-responsive">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="12">#</th>
                                    <th width="" class="text-center">Product Code</th>
                                    <th class="text-center">Product Name</th>
                                    <th class="text-center">UOM </th>
                                    <th class="text-center">Sale Price</th>
                                    <th class="text-center">Purchase Price</th>
                                    <th class="text-center">Tax Rate</th>
                                    <th width="" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $rs = $con->query("SELECT * FROM tbl_product where del_status = 0 order by id");
                                if ($rs->rowCount()) {
                                    $sno=1;
                                    while ($obj = $rs->fetch(PDO::FETCH_OBJ)) {
                                ?>
                                <tr>
                                    <td class="text-center"><?php echo $sno; ?></td>
                                    <td><?php echo $obj->p_code; ?></td>
                                    <td><?php echo $obj->p_name; ?></td>
                                    <td><?php echo $dbcon->GetOneRecord('tbl_uom','uom_name','id ="'.$obj->uom.'" AND del_status',0); ?></td>
                                    <td><?php echo $obj->sales_price; ?></td>
                                    <td><?php echo $obj->purchase_price; ?></td>
                                    <td><?php echo $dbcon->GetOneRecord('tbl_gst','descriptions','id ="'.$obj->gst.'" AND del_status',0); ?></td>
                                    <td class="text-center">
                                        <a href="product.php?id=<?php echo $converter->encode($obj->id); ?>" title="Edit"><i class="fa fa-pencil" aria-hidden="true"></i></a>&nbsp;&nbsp;
                                        <a href="product_lst.php?did=<?php echo $converter->encode($obj->id); ?>" title="Delete" onclick="return confirm('Are You Sure Want To Delete?');"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                    </td>
                                </tr>
                                <?php
                                        $sno++;
                                    }
                                } else {
                                ?>
                                <tr>
                                    <td colspan="10" align="center">--No Records Found--</td>
                                </tr>
                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
    </section>
</div>
<?php include("includes/footer.php"); ?>
<script>
    
$(function () {
    $('#example1').DataTable({
        'responsive'    : true,
        'pageLength'    : 10,
        'searching'     : true,
        'autoWidth'     : false,
        //'dom'           : 'Bfrtip',
        // 'dom'           : "<'row'<'col-sm-12 d-flex col-md-5'lf><'col-sm-12 col-md-7 text-right'B>>" +
        //                     "<'row'<'col-sm-12'tr>>" +
        //                     "<'row m-2'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                            
        // buttons         : ['copyHtml5','excelHtml5','csvHtml5','pdfHtml5','print'],

        rowReorder      : true,
        columnDefs      : [
            { orderable: true, className: 'reorder', targets: [0,1,2,3,4,5,6,7] },
            { orderable: false, targets: '_all' }
        ]
    })
});

</script>

<script type="text/javascript">
$(document).ready(function() {
    $('#masterMainNav').addClass('active');
    $('#masterCustomerSubNav').addClass('active');
    // alert();
    // $('.select2').select2();
    
});
    
</script>
