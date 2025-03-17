<?php
ob_start();
session_start();

require_once("includes/common/connection.php");
require_once("includes/common/dbfunctions.php");
require_once("includes/common/functions.php");

$con = new connection();
$dbcon = new dbfunctions();
$converter = new Encryption;

//ini_set('display_errors', '1'); ini_set('display_startup_errors', '1'); error_reporting(E_ALL);

isAdmin();

//---------------------------------save/submit----------------------------------
if (isset($_REQUEST["submit"])) {
    try{
        $created_by = $_SESSION["user_id"];
        $created_dt = date('Y-m-d H:i:s');

        $stmt = null;
        $stmt = $con->prepare("INSERT INTO tbl_uom (uom_name, uom, created_by, created_dt) 
              VALUES (:uom_name, :uom, :created_by, :created_dt)");
        $data = array(
            ":uom_name" => trim($_REQUEST["uom_name"]),
            ":uom" => trim($_REQUEST["uom"]),
            ":created_by" => $created_by,
            ":created_dt" => $created_dt,
        );
        $stmt->execute($data);

        $_SESSION["msg"] = "Saved Successfully";
    } catch (Exception $e) {
        $str = filter_var($e->getMessage(), FILTER_SANITIZE_STRING);
        echo $_SESSION['msg_err'] = $str;  
    }
    
    header("location: uom.php");
    die();
}
//---------------------------------save/submit----------------------------------
//---------------------------------update--------------------------------------
if (isset($_REQUEST["update"])) {
    try{
        $updated_by = $_SESSION["user_id"];
        $updated_dt = date('Y-m-d H:i:s');

        $stmt = null;
        $stmt = $con->prepare("UPDATE tbl_uom SET uom_name = :uom_name, uom = :uom, updated_by = :updated_by, updated_dt = :updated_dt WHERE id = :id");

        $data = array(
            ":id" => trim($_REQUEST["hid_id"]),
            ":uom_name" => trim($_REQUEST["uom_name"]),
            ":uom" => trim($_REQUEST["uom"]),
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

    header("location:uom.php");
    die();
}
//---------------------------------update----------------------------------------
//---------------------------------delete----------------------------------------
if (isset($_REQUEST["did"])) {
    $stmt = null;
    $stmt = $con->query("UPDATE tbl_uom SET del_status = 1 WHERE id=" . $converter->decode($_REQUEST["did"]));

    $_SESSION["msg"] = "Deleted Successfully";

    header("location:uom.php");
    die();
}
//---------------------------------delete----------------------------------------
//---------------------------------edit----------------------------------------
if (isset($_REQUEST["id"])) {
    $rs = $con->query("SELECT * FROM tbl_uom where id=" . $converter->decode($_REQUEST["id"]));
    if ($rs->rowCount()) {
        if ($obj = $rs->fetch(PDO::FETCH_OBJ)) {
            $id = $obj->id;
            $uom_name = $obj->uom_name;
            $uom = $obj->uom;
        }
    }
}
//---------------------------------edit----------------------------------------
?>

<?php include("includes/header.php"); ?>
<?php include("includes/aside.php"); ?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>UOM - Unit of Measurement</h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-edit"></i> Masters</a></li>
            <li class="active">UOM</li>
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
                    <form id="thisForm" name="thisForm" action="uom.php" method="post">
                        <div class="box-body">
                            <div class="form-group">
                                <label class="control-label">Name of the UOM <span class="err">*</span></label>
                                <input type="text" class="form-control" value="<?php echo $uom_name; ?>" name="uom_name" id="uom_name" placeholder="Enter the Name of UOM" title="Enter the Name of UOM" maxlength="15" autocomplete="off" autofocus="autofocus" required oninvalid="this.setCustomValidity('Please enter the name of uom...!')" oninput="this.setCustomValidity('')" />
                            </div>
                            <div class="form-group">
                                <label class="control-label">Short Name <span class="err">*</span></label>
                                <input type="text" class="form-control" value="<?php echo $uom; ?>" name="uom" id="uom" placeholder="Enter the Short Name" title="Enter the Short Name" maxlength="5" autocomplete="off" required oninvalid="this.setCustomValidity('Please enter the short name...!')" oninput="this.setCustomValidity('')" />
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
                                        <th class="text-center">Name of the UOM</th>
                                        <th width="140" class="text-center">Short Name</th>
                                        <th width="60" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $rs = $con->query("SELECT * FROM tbl_uom where del_status = 0 order by id");
                                    if ($rs->rowCount()) {
                                        $sno=1;
                                        while ($obj = $rs->fetch(PDO::FETCH_OBJ)) {
                                    ?>
                                    <tr>
                                        <td class="text-center"><?php echo $sno; ?></td>
                                        <td><?php echo $obj->uom_name; ?></td>
                                        <td><?php echo $obj->uom; ?></td>
                                        <td class="text-center">
                                            <a href="uom.php?id=<?php echo $converter->encode($obj->id); ?>" title="Edit"><i class="fa fa-pencil" aria-hidden="true"></i></a>&nbsp;&nbsp;
                                            <a href="uom.php?did=<?php echo $converter->encode($obj->id); ?>" title="Delete" onclick="return confirm('Are You Sure Want To Delete?');"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                                        </td>
                                    </tr>
                                    <?php
                                            $sno++;
                                        }
                                    } else {
                                    ?>
                                    <tr>
                                        <td colspan="4" align="center">--No Records Found--</td>
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
            { orderable: true, className: 'reorder', targets: [0,1,2,3] },
            { orderable: false, targets: '_all' }
        ]
    })
});
</script>

<script type="text/javascript">
$(document).ready(function() {
    $('#masterMainNav').addClass('active');
    $('#masterUOMSubNav').addClass('active');
});
</script>
