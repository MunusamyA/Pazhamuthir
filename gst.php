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

//---------------------------------save/submit----------------------------------
if (isset($_REQUEST["submit"])) {
    try{
        $created_by = $_SESSION["user_id"];
        $created_dt = date('Y-m-d H:i:s');

        $stmt = null;
        $stmt = $con->prepare("INSERT INTO tbl_gst (sgst, cgst, igst, descriptions, created_by, created_dt) 
              VALUES (:sgst, :cgst, :igst, :descriptions, :created_by, :created_dt)");
        $data = array(
            ":sgst" => trim($_REQUEST["sgst"]),
            ":cgst" => trim($_REQUEST["cgst"]),
            ":igst" => trim($_REQUEST["igst"]),
            ":descriptions" => trim($_REQUEST["descriptions"]),
            ":created_by" => $created_by,
            ":created_dt" => $created_dt,
        );
        $stmt->execute($data);

        $_SESSION["msg"] = "Saved Successfully";
    } catch (Exception $e) {
        $str = filter_var($e->getMessage(), FILTER_SANITIZE_STRING);
        echo $_SESSION['msg_err'] = $str;  
    }
    
    header("location: gst.php");
    die();
}
//---------------------------------save/submit----------------------------------
//---------------------------------update--------------------------------------
if (isset($_REQUEST["update"])) {
    try{
        $updated_by = $_SESSION["user_id"];
        $updated_dt = date('Y-m-d H:i:s');

        $stmt = null;
        $stmt = $con->prepare("UPDATE tbl_gst SET sgst = :sgst, cgst = :cgst, igst = :igst, descriptions = :descriptions, updated_by = :updated_by, updated_dt = :updated_dt WHERE id = :id");

        $data = array(
            ":id" => trim($_REQUEST["hid_id"]),
            ":sgst" => trim($_REQUEST["sgst"]),
            ":cgst" => trim($_REQUEST["cgst"]),
            ":igst" => trim($_REQUEST["igst"]),
            ":descriptions" => trim($_REQUEST["descriptions"]),
            ":updated_by" => $updated_by,
            ":updated_dt" => $updated_dt
        );
        //print_r($data); die();
        $stmt->execute($data);

        $_SESSION["msg"] = "Updated Successfully";
    } catch (Exception $e) {
        $str = filter_var($e->getMessage(), FILTER_SANITIZE_STRING);
        echo $_SESSION['msg_err'] = $str;  
    }

    header("location:gst.php");
    die();
}
//---------------------------------update----------------------------------------
//---------------------------------delete----------------------------------------
if (isset($_REQUEST["did"])) {
    $stmt = null;
    $stmt = $con->query("UPDATE tbl_gst SET del_status = 1 WHERE id=" . $converter->decode($_REQUEST["did"]));

    $_SESSION["msg"] = "Deleted Successfully";

    header("location:gst.php");
    die();
}
//---------------------------------delete----------------------------------------
//---------------------------------edit----------------------------------------
if (isset($_REQUEST["id"])) {
    $rs = $con->query("SELECT * FROM tbl_gst where id=" . $converter->decode($_REQUEST["id"]));
    if ($rs->rowCount()) {
        if ($obj = $rs->fetch(PDO::FETCH_OBJ)) {
            $id = $obj->id;
            $sgst = $obj->sgst;
            $cgst = $obj->cgst;
            $igst = $obj->igst;
            $descriptions = $obj->descriptions;
        }
    }
}
//---------------------------------edit----------------------------------------
?>

<?php include("includes/header.php"); ?>
<?php include("includes/aside.php"); ?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>GST</h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-edit"></i> Masters</a></li>
            <li class="active">GST</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <!-------------------------------------------------- Form ------------------------------------------>
            <div class="col-md-4">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                        <?php if(isset($_REQUEST["id"])){
                            echo "Edit";
                        }else{
                            echo "Add";
                        }
                        ?>
                        </h3>
                    </div>
					<br />
                    <form id="thisForm" name="thisForm" action="gst.php" method="post">
                        <div class="box-body">
                            <div class="form-group">
                                <label class="control-label">SGST <span class="err">*</span></label>
                                <input type="text" class="form-control" value="<?php echo $sgst; ?>" name="sgst" id="sgst" placeholder="Enter the SGST" title="Enter the SGST" maxlength="10" autocomplete="off" autofocus="autofocus" required oninvalid="this.setCustomValidity('Please enter the sgst...!')" oninput="this.setCustomValidity('')" />
                            </div>
                            <div class="form-group">
                                <label class="control-label">CGST <span class="err">*</span></label>
                                <input type="text" class="form-control" value="<?php echo $cgst; ?>" name="cgst" id="cgst" placeholder="Enter the CGST" title="Enter the CGST" maxlength="10" autocomplete="off" required oninvalid="this.setCustomValidity('Please enter the cgst...!')" oninput="this.setCustomValidity('')" />
                            </div>
                            <div class="form-group">
                                <label class="control-label">IGST <span class="err">*</span></label>
                                <input type="text" class="form-control" value="<?php echo $igst; ?>" name="igst" id="igst" placeholder="Enter the IGST" title="Enter the IGST" maxlength="10" autocomplete="off" required oninvalid="this.setCustomValidity('Please enter the igst...!')" oninput="this.setCustomValidity('')" />
                            </div>
                            <div class="form-group">
                                <label class="control-label">Description </label>
                                <input type="text" class="form-control" value="<?php echo $descriptions; ?>" name="descriptions" id="descriptions" placeholder="Enter the Description" title="Enter the Description" autocomplete="off" />    
                            </div>
                        </div>
                        <div class="box-footer">
							<div class="pull-right">
								<?php if (isset($_REQUEST["id"])) { ?>
								<input type="hidden" value="<?php echo $id; ?>" name="hid_id">
								<button type="submit" name="update" class="btn btn-primary"><i class="fa fa-check" aria-hidden="true"></i> Update</button>
								<?php
								} else { ?>
								<button type="submit" name="submit" class="btn btn-primary"><i class="fa fa-check" aria-hidden="true"></i> Submit</button>
								<?php } ?>
							</div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- ----------------------------------------------- Form ------------------------------------------ -->
            <!-- --------------------------------------------- View -------------------------------------------- -->
            <div class="col-md-8">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">List</h3>
                    </div>
                    <div class="box-body">
                        <div class="dt-responsive table-responsive">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="8">#</th>
                                        <th width="200" class="text-center">SGST</th>
                                        <th width="200" class="text-center">CGST</th>
                                        <th class="text-center">IGST</th>
                                        <th width="60" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $rs = $con->query("SELECT * FROM tbl_gst where del_status = 0 order by id");
                                    if ($rs->rowCount()) {
                                        $sno=1;
                                        while ($obj = $rs->fetch(PDO::FETCH_OBJ)) {
                                    ?>
                                    <tr>
                                        <td class="text-center"><?php echo $sno; ?></td>
                                        <td><?php echo $obj->sgst; ?></td>
                                        <td><?php echo $obj->cgst; ?></td>
                                        <td><?php echo $obj->igst; ?></td>
                                        <td class="text-center">
                                            <a href="gst.php?id=<?php echo $converter->encode($obj->id); ?>" title="Edit"><i class="fa fa-pencil" aria-hidden="true"></i></a>&nbsp;&nbsp;
                                            <a href="gst.php?did=<?php echo $converter->encode($obj->id); ?>" title="Delete" onclick="return confirm('Are You Sure Want To Delete?');"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                        </td>
                                    </tr>
                                    <?php
                                            $sno++;
                                        }
                                    } else {
                                    ?>
                                    <tr>
                                        <td colspan="5" align="center">--No Records Found--</td>
                                    </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
            <!-- ------------------------ View ---------------------------------- -->
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
            { orderable: true, className: 'reorder', targets: [0,1,2,3,4] },
            { orderable: false, targets: '_all' }
        ]
    })
});
</script>

<script type="text/javascript">
$(document).ready(function() {
    $('#masterMainNav').addClass('active');
    $('#masterGstSubNav').addClass('active');
});
</script>
