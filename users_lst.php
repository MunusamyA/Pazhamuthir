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
$menu_permission = explode('||', $_SESSION["menu_permission"]);

?>
<?php include("includes/header.php"); ?>
<?php include("includes/aside.php"); ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Users
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-cog"></i> Settings</a></li>
            <li class="active">Users</li>
        </ol>
    </section>
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">List</h3>
                <?php 
                if(in_array('createUsers',$menu_permission)){
                ?>
                <div class="box-tools pull-right">
                    <a href="users.php" class="btn btn-block btn-primary"><i class="fa fa-plus-circle" aria-hidden="true"></i></a>
                </div>
                <?php
                }
                ?>
            </div>
            <div class="box-body">

                <form id="thisForm" name="thisForm" class="form-horizontal" action="students.php" method="post">
                <div class="table-responsive">
                    <div class="dt-responsive table-responsive">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="8">#</th>
                                    <th width="60">User Id</th>
                                    <th class="text-center">Name</th>
                                    <th width="70" class="text-center">Mobile No</th>
                                    <th width="180">Email Id</th>
                                    <th width="160">Roles and Permissions</th>
                                    <th width="60" class="text-center">Status</th>
                                    <th width="60" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $rs = $con->query("SELECT * FROM tbl_users where del_status=0 order by id");
                                if ($rs->rowCount()) {
                                    $sno=1;
                                    while ($obj = $rs->fetch(PDO::FETCH_OBJ)) {
                                ?>
                                <tr>
                                    <td><?php echo $sno; ?></td>
                                    <td><?php echo $obj->id; ?></td>
                                    <td><?php echo $obj->uname; ?></td>
                                    <td class="text-center"><?php echo iif($obj->mobile_no=="", "-", $obj->mobile_no); ?></td>
                                    <td><?php echo $obj->email; ?></td>
                                    <td><?php echo $dbcon->GetOneRecord("tbl_roles", "role_name", "id", $obj->roles_id); ?></td>
                                    <?php if ($obj->status == 1) { ?>
                                        <td class="text-center"><span class='btn btn-success btn-xs'>ACTIVE</span></td>
                                    <?php }else{ ?>
                                        <td align="center"><span class='btn btn-danger btn-xs'>IN ACTIVE</span></td>
                                    <?php } ?>
                                    <td class="text-center">
                                        <?php if(in_array('updateUsers',$menu_permission)){ ?>
                                        <a href="users.php?id=<?php echo $converter->encode($obj->id); ?>" title="Edit"><i class="fa fa-pencil" aria-hidden="true"></i></a>&nbsp;&nbsp;
                                        <?php }
                                        if(in_array('deleteUsers',$menu_permission)){
                                        ?>
                                        <a href="users.php?did=<?php echo $converter->encode($obj->id); ?>" title="Delete" onclick="return confirm('Are You Sure Want To Delete?');"><i class="fa fa-trash-o" aria-hidden="true"></i>
                                        </a>
                                        <?php } ?>
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
                </form>

            </div>
        </div>
    </section>
</div>
<?php include("includes/footer.php"); ?>
<script>
$(function () {
    $('#example1').DataTable({
        'responsive'    : true,
        'pageLength'    : 14,
        'searching'     : false,
        'autoWidth'     : false,
        //'dom'           : 'Bfrtip',
        'dom'           : "<'row'<'col-sm-12 d-flex col-md-6'lf><'col-sm-12 col-md-6 text-right'B>>" +
                            "<'row'<'col-sm-12'tr>>" +
                            "<'row m-2'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                            
        buttons         : ['copyHtml5','excelHtml5','csvHtml5','pdfHtml5','print'],

        rowReorder      : true,
        columnDefs      : [
            { orderable: true, className: 'reorder', targets: [0,1,2,3,4,5,6] },
            { orderable: false, targets: '_all' }
        ]
    });

});
</script>

<script type="text/javascript">
$(document).ready(function() {
    $('#settingsMainNav').addClass('active');
    $('#settingsUsersSubNav').addClass('active');
});
</script>