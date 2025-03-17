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
// if (isset($_REQUEST["did"])) {
//     $stmt = null;
//     $stmt = $con->query("UPDATE tbl_product SET del_status=1 WHERE id=" . $converter->decode($_REQUEST["did"]));

//     $_SESSION["msg"] = "Deleted Successfully";

//     header("location:product_lst.php");
//     die();
// }

?>

<?php include("includes/header.php"); ?>
<?php include("includes/aside.php"); ?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Orders</h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-edit"></i> Reports</a></li>
            <li class="active">Sales</li>
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
                                </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
    </section>
</div>
<?php include("includes/footer.php"); ?>
<script>
    var data = [
    {
        "name":       "Tiger Nixon",
        "position":   "System Architect",
        "salary":     "$3,120",
        "start_date": "2011/04/25",
        "office":     "Edinburgh",
        "extn":       "5421"
    },
    {
        "name":       "Garrett Winters",
        "position":   "Director",
        "salary":     "$5,300",
        "start_date": "2011/07/25",
        "office":     "Edinburgh",
        "extn":       "8422"
    }
]
$(function () {
    $('#example1').DataTable({
        data: data,
        columns: [
        { data: 'name' },
        { data: 'position' },
        { data: 'salary' },
        { data: 'salary' },
        { data: 'salary' },
        { data: 'office' }
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
