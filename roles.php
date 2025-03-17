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
 $menu_permission1 = explode('||', $_SESSION["menu_permission"]);
//  print_r($menu_permission1);

//---------------------------------save/submit----------------------------------
if (isset($_REQUEST["submit"])) {
    try{
        $created_by = $_SESSION["user_id"];
        $created_dt = date('Y-m-d H:i:s');

        if ($_REQUEST['permissions']) {
          $permissions = implode('||', $_REQUEST['permissions']);
        }
        else
        {
            $permissions = "";
        }

        $stmt = null;
        $stmt = $con->prepare("INSERT INTO tbl_roles (role_name, menu_permission, status, created_by, created_dt) 
                        VALUES (:role_name, :menu_permission, :status, :created_by, :created_dt)");
        $data = array(
            ":role_name" => trim(ucwords($_REQUEST["role_name"])),
            ":menu_permission" => $permissions,
            ":status" => trim($_REQUEST["status"]),
            ":created_by" => $created_by,
            ":created_dt" => $created_dt,
        );
        // print_r($data); die();
        $stmt->execute($data);

        $_SESSION["msg"] = "Saved Successfully";
    } catch (Exception $e) {
        $str = filter_var($e->getMessage(), FILTER_SANITIZE_STRING);
        echo $_SESSION['msg_err'] = $str;  
    }
    
    header("location: roles_lst.php");
    die();
}
//---------------------------------save/submit----------------------------------
//---------------------------------update--------------------------------------
if (isset($_REQUEST["update"])) {
    try{
       $updated_by = $_SESSION["user_id"]; 0;
        $updated_dt = date('Y-m-d H:i:s');

        if ($_REQUEST['permissions']) {
            $permissions = implode('||', $_REQUEST['permissions']);
        }
        else
        {
            $permissions = "";
        }

        $stmt = null;
        $stmt = $con->prepare("UPDATE tbl_roles SET role_name=:role_name, menu_permission=:menu_permission, status=:status, updated_by=:updated_by, updated_dt=:updated_dt where id=:id");
        $data = array(
            ":id" => trim($_REQUEST["hid_id"]),
            ":role_name" => trim(ucwords($_REQUEST["role_name"])),
            ":menu_permission" => $permissions,
            ":status" => trim($_REQUEST["status"]),
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

    header("location:roles_lst.php");
    die();
}
//---------------------------------update----------------------------------------
//---------------------------------delete----------------------------------------
if (isset($_REQUEST["did"])) {
    $stmt = null;
    $stmt = $con->query("UPDATE tbl_roles SET del_status=1 WHERE id=" . $converter->decode($_REQUEST["did"]));

    $_SESSION["msg"] = "Deleted Successfully";

    header("location:roles_lst.php");
    die();
}
//---------------------------------delete----------------------------------------

if (isset($_REQUEST["id"])) {
    $rs = $con->query("SELECT * FROM tbl_roles where id=" . $converter->decode($_REQUEST["id"]));
    if ($rs->rowCount()) {
        if ($obj = $rs->fetch(PDO::FETCH_OBJ)) {
            $id = $obj->id;
            $role_name = $obj->role_name;
            $menu_permissions = $obj->menu_permission;
            $status = $obj->status;
        }
    }
}
?>

<style type="text/css">
  .menu-head {
    background-color: #5dafde;
    color: #ffffff;
    padding: 10px 3px 3px 10px;
    font-size: 15px;
  }

  #menu_perimission_div{
    /*height: 540px;
    overflow-y: auto;*/
    max-height: 1000px;
    margin-bottom: 10px;
    overflow: scroll;
    overflow-x: hidden;
    -webkit-overflow-scrolling: touch;
  }
  #menu_perimission_div::-webkit-scrollbar {
    width: 4px;
  }
  #menu_perimission_div::-webkit-scrollbar-track {
    background: #f1f1f1; 
  }
  /* Handle */
  #menu_perimission_div::-webkit-scrollbar-thumb {
    background: #888; 
  }
  /* Handle on hover */
  #menu_perimission_div::-webkit-scrollbar-thumb:hover {
    background: #555; 
  }
</style>

<link href="assets/dist/css/bootstrap-switch-button.min.css" rel="stylesheet">
<script src="assets/dist/js/bootstrap-switch-button.min.js"></script>

<?php include("includes/header.php"); ?>
<?php include("includes/aside.php"); ?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Roles and Permissions
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-cog"></i> Settings</a></li>
            <li class="active">Roles and Permissions</li>
        </ol>
    </section>
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <?php if(isset($_REQUEST["id"])){
                    echo '<h3 class="box-title">Edit</h3>';
                }else{
                    echo '<h3 class="box-title">Add</h3>';
                } ?>
                <div class="box-tools pull-right">
                    <a href="roles_lst.php" class="btn btn-block btn-primary"><i class="fa fa-bars" aria-hidden="true"></i></a>
                </div>
            </div>
            <br />
            <form id="thisForm" name="thisForm" action="roles.php" method="post">
                <div class="box-body">
                    <div class="col-md-12">
                        
                        <div class="row">
                            <div class="form-group col-md-9">
                              <label>Role Name</label><span class="err">*</span>
                              <input type="text" class="form-control" name="role_name" id="role_name" placeholder="Enter the Role Name" value="<?php if(!empty($role_name)){ echo $role_name;} ?>" autofocus autocomplete="off" title="Enter the Role Name" onKeyPress="return isNameKey(event);" required oninvalid="this.setCustomValidity('Please enter the role name...!')" oninput="this.setCustomValidity('')">
                            </div>

                            <div class="form-group col-md-3">
                              <label>Status</label>
                              <select class="form-control select2" name="status" id="status" >
                                <?php 
                                if(!empty($status)){
                                  if($status==1){
                                    echo "<option value='1' selected>Active</option>";
                                    echo "<option value='0'>IN Active</option>";
                                  }
                                  else{
                                    echo "<option value='1'>Active</option>";
                                    echo "<option value='0' selected>IN Active</option>";
                                  }
                                }
                                else{ 
                                ?>
                                <option value="1">Active</option>
                                <option value="0">IN Active</option>
                              <?php } ?>
                              </select>
                            </div>
                        </div>

                        <div class="row" id="menu_perimission_div">
                            <div class="form-group col-sm-12" style="padding-right: 10px;">
                              <div class="menu-head">
                                <label>Menu Permission</label>
                              </div>
                              
                              <?php 
                              if(!empty($menu_permissions)){
                              $serialize_permission = explode('||', $menu_permissions);
                              }
                              ?>

                              <div class="form-group col-sm-12">
                                
                                <div class="col-sm-12" style="padding-bottom: 10px;">
                                  <font size="+1"><b>Transaction</b></font>
                                  <table border="0" cellpadding="0" cellspacing="0" width="100%" class="table table-hover">
                                    <tr>
                                      <td width="12%">Purchase</td>
                                      <td width="12%">
                                        <input type="checkbox" name="permissions[]" id="permission" value="mnuPurchase" <?php if(!empty($serialize_permission)){
                                          if(in_array('mnuPurchase', $serialize_permission)) { echo "checked"; } 
                                        } ?> data-toggle="switchbutton" data-onlabel="Allowed" data-offlabel="Not Allowed" data-onstyle="success" data-offstyle="danger" data-size="xs" data-width="80">
                                      </td>
                                      <td width="12%">Sales</td>
                                      <td width="12%">
                                        <input type="checkbox" name="permissions[]" id="permission" value="mnuSales" <?php if(!empty($serialize_permission)){
                                          if(in_array('mnuSales', $serialize_permission)) { echo "checked"; } 
                                        } ?> data-toggle="switchbutton" data-onlabel="Allowed" data-offlabel="Not Allowed" data-onstyle="success" data-offstyle="danger" data-size="xs" data-width="80">
                                      </td>
                                      <td width="12%">&nbsp;</td>
                                      <td width="12%">&nbsp;</td>
                                      <td width="12%">&nbsp;</td>
                                      <td width="12%">&nbsp;</td>
                                    </tr>
                                  </table>

                                  <font size="+1"><b>Masters</b></font>
                                  <table border="0" cellpadding="0" cellspacing="0" width="100%" class="table table-hover">
                                    <tr>
                                      <td width="12%">Supplier</td>
                                      <td width="12%">
                                        <input type="checkbox" name="permissions[]" id="permission" value="mnuSupplier" <?php if(!empty($serialize_permission)){
                                          if(in_array('mnuSupplier', $serialize_permission)) { echo "checked"; } 
                                        } ?> data-toggle="switchbutton" data-onlabel="Allowed" data-offlabel="Not Allowed" data-onstyle="success" data-offstyle="danger" data-size="xs" data-width="80">
                                      </td>
                                      <td width="12%">Customer</td>
                                      <td width="12%">
                                        <input type="checkbox" name="permissions[]" id="permission" value="mnuCustomer" <?php if(!empty($serialize_permission)){
                                          if(in_array('mnuCustomer', $serialize_permission)) { echo "checked"; } 
                                        } ?> data-toggle="switchbutton" data-onlabel="Allowed" data-offlabel="Not Allowed" data-onstyle="success" data-offstyle="danger" data-size="xs" data-width="80">
                                      </td>
                                      <td width="12%">GST</td>
                                      <td width="12%">
                                        <input type="checkbox" name="permissions[]" id="permission" value="mnuGST" <?php if(!empty($serialize_permission)){
                                          if(in_array('mnuGST', $serialize_permission)) { echo "checked"; } 
                                        } ?> data-toggle="switchbutton" data-onlabel="Allowed" data-offlabel="Not Allowed" data-onstyle="success" data-offstyle="danger" data-size="xs" data-width="80">
                                      </td>
                                      <td width="12%">UOM</td>
                                      <td width="12%">
                                        <input type="checkbox" name="permissions[]" id="permission" value="mnuUOM" <?php if(!empty($serialize_permission)){
                                          if(in_array('mnuUOM', $serialize_permission)) { echo "checked"; } 
                                        } ?> data-toggle="switchbutton" data-onlabel="Allowed" data-offlabel="Not Allowed" data-onstyle="success" data-offstyle="danger" data-size="xs" data-width="80">
                                      </td>
                                    </tr>
                                    <!-- <tr>
                                      <td>Designation</td>
                                      <td>
                                        <input type="checkbox" name="permissions[]" id="permission" value="mnuDesignation" <?php //if(!empty($serialize_permission)){
                                          //if(in_array('mnuDesignation', $serialize_permission)) { echo "checked"; } 
                                        //} ?> data-toggle="switchbutton" data-onlabel="Allowed" data-offlabel="Not Allowed" data-onstyle="success" data-offstyle="danger" data-size="xs" data-width="80">
                                      </td>
                                      <td>Blocked Date</td>
                                      <td>
                                        <input type="checkbox" name="permissions[]" id="permission" value="mnuBlocked" <?php //if(!empty($serialize_permission)){
                                          //if(in_array('mnuBlocked', $serialize_permission)) { echo "checked"; } 
                                        //} ?> data-toggle="switchbutton" data-onlabel="Allowed" data-offlabel="Not Allowed" data-onstyle="success" data-offstyle="danger" data-size="xs" data-width="80">
                                      </td>
                                      <td>&nbsp;</td>
                                      <td>&nbsp;</td>
                                      <td>&nbsp;</td>
                                      <td>&nbsp;</td>
                                    </tr> -->
                                  </table>

                                  <font size="+1"><b>Settings</b></font>
                                  <table border="0" cellpadding="0" cellspacing="0" width="100%" class="table table-hover">
                                    <tr>
                                      <td width="12%">Roles and Permissions</td>
                                      <td width="12%">
                                        <input type="checkbox" name="permissions[]" id="permission" value="mnuRolesPermissions" <?php if(!empty($serialize_permission)){
                                          if(in_array('mnuRolesPermissions', $serialize_permission)) { echo "checked"; } 
                                        } ?> data-toggle="switchbutton" data-onlabel="Allowed" data-offlabel="Not Allowed" data-onstyle="success" data-offstyle="danger" data-size="xs" data-width="80">
                                      </td>
                                      <td width="12%">Users</td>
                                      <td width="12%">
                                        <input type="checkbox" name="permissions[]" id="permission" value="mnuUsers" <?php if(!empty($serialize_permission)){
                                          if(in_array('mnuUsers', $serialize_permission)) { echo "checked"; } 
                                        } ?> data-toggle="switchbutton" data-onlabel="Allowed" data-offlabel="Not Allowed" data-onstyle="success" data-offstyle="danger" data-size="xs" data-width="80">
                                      </td>
                                      <td width="12%">Shop Details</td>
                                      <td width="12%">
                                        <input type="checkbox" name="permissions[]" id="permission" value="mnuShop" <?php if(!empty($serialize_permission)){
                                          if(in_array('mnuShop', $serialize_permission)) { echo "checked"; }
                                        } ?> data-toggle="switchbutton" data-onlabel="Allowed" data-offlabel="Not Allowed" data-onstyle="success" data-offstyle="danger" data-size="xs" data-width="80">
                                      </td>
                                      <td width="12%">&nbsp;</td>
                                      <td width="12%">&nbsp;</td>
                                    </tr>
                                  </table>
                                </div>
                              </div>

                              <!-- <div class="menu-head">
                                <label><input type="checkbox" id="select_all">
                                &nbsp;&nbsp;&nbsp;Select All</label>
                              </div> -->
                            </div>
                          </div>

                    </div>
                </div>
                <div class="box-footer text-right">
                    <?php if (isset($_REQUEST["id"])) { ?>
                    <input type="hidden" value="<?php echo $id; ?>" name="hid_id">
                    <button type="submit" name="update" class="btn btn-primary"><i class="fa fa-check" aria-hidden="true"></i> Update</button>
                    <?php } else { ?>
                    <button type="submit" name="submit" class="btn btn-primary"><i class="fa fa-check" aria-hidden="true"></i> Submit</button>
                    <?php } ?>
                </div>
            </form>
        </div>

    </section>
</div>
<?php include("includes/footer.php"); ?>

<script type="text/javascript">
// $("#select_all").click(function(){
//   if($("#select_all").prop('checked') == true){
//     $(':checkbox').each(function() {
//       this.checked = true;                        
//     });
//   }
//   else
//   {
//     $(':checkbox').each(function() {
//       this.checked = false;                        
//     });
//   }
// });

$(document).ready(function() {
    $('#settingsMainNav').addClass('active');
    $('#settingsRolesSubNav').addClass('active');
});
</script>