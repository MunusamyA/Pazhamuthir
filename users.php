<?php
ob_start();
session_start();

require_once("includes/common/connection.php");
require_once("includes/common/dbfunctions.php");
require_once("includes/common/functions.php");

$con = new connection();
$dbcon = new dbfunctions();
$converter = new Encryption;

// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL);

isAdmin();
$menu_permission = explode('||', $_SESSION["menu_permission"]);

//---------------------------------save/submit----------------------------------
if (isset($_REQUEST["submit"])) {
    try{
        $created_by = $_SESSION["user_id"]; ;
        $created_dt = date('Y-m-d H:i:s');

        $stmt = null;
        $stmt = $con->prepare("INSERT INTO tbl_users (uname, mobile_no, email, roles_id, status, username, password, hint_password, created_by, created_dt) 
                        VALUES (:uname, :mobile_no, :email, :roles_id, :status, :username, :password, :hint_password, :created_by, :created_dt)");
        $data = array(
            ":uname" => trim(ucwords($_REQUEST["uname"])),
            ":mobile_no" => trim($_REQUEST["mobile_no"]),
            ":email" => trim($_REQUEST["email"]),
            ":roles_id" => trim($_REQUEST["roles"]),
            ":status" => trim($_REQUEST["status"]),
            ":username" => trim($_REQUEST["username"]),
            ":password" => md5(sha1($_REQUEST["password"])),
            ":hint_password" => trim($_REQUEST["password"]),
            ":created_by" => $created_by,
            ":created_dt" => $created_dt,
        );
        //print_r($data); die();
        $stmt->execute($data);

        $_SESSION["msg"] = "Saved Successfully";
    } catch (Exception $e) {
        $str = filter_var($e->getMessage(), FILTER_SANITIZE_STRING);
        echo $_SESSION['msg_err'] = $str;  
    }
    
    header("location: users_lst.php");
    die();
}
//---------------------------------save/submit----------------------------------
//---------------------------------update--------------------------------------
if (isset($_REQUEST["update"])) {
    try{
       $updated_by = $_SESSION["user_id"]; 0;
        $updated_dt = date('Y-m-d H:i:s');

        $stmt = null;
        $stmt = $con->prepare("UPDATE tbl_users SET uname=:uname, mobile_no=:mobile_no, email=:email, roles_id=:roles_id, status=:status, username=:username, password=:password, hint_password=:hint_password, updated_by=:updated_by, updated_dt=:updated_dt where id=:id");
        $data = array(
            ":id" => trim($_REQUEST["hid_id"]),
            ":uname" => trim(ucwords($_REQUEST["uname"])),
            ":mobile_no" => trim($_REQUEST["mobile_no"]),
            ":email" => trim($_REQUEST["email"]),
            ":roles_id" => trim($_REQUEST["roles"]),
            ":status" => trim($_REQUEST["status"]),
            ":username" => trim($_REQUEST["username"]),
            ":password" => md5(sha1($_REQUEST["password"])),
            ":hint_password" => trim($_REQUEST["password"]),
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

    header("location:users_lst.php");
    die();
}
//---------------------------------update----------------------------------------
//---------------------------------delete----------------------------------------
if (isset($_REQUEST["did"])) {
    $stmt = null;
    $stmt = $con->query("UPDATE tbl_users SET del_status=1 WHERE id=" . $converter->decode($_REQUEST["did"]));

    $_SESSION["msg"] = "Deleted Successfully";

    header("location:users_lst.php");
    die();
}
//---------------------------------delete----------------------------------------

if (isset($_REQUEST["id"])) {
    $rs = $con->query("SELECT * FROM tbl_users where id=" . $converter->decode($_REQUEST["id"]));
    if ($rs->rowCount()) {
        if ($obj = $rs->fetch(PDO::FETCH_OBJ)) {
            $id = $obj->id;
            $uname = $obj->uname;
            $mobile_no = $obj->mobile_no;
            $email = $obj->email;
            $roles_id = $obj->roles_id;
            $status = $obj->status;
            $username = $obj->username;
            $hint_password = $obj->hint_password;
        }
    }
}
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
                <?php if(isset($_REQUEST["id"])){
                    echo '<h3 class="box-title">Edit</h3>';
                }else{
                    echo '<h3 class="box-title">Add</h3>';
                } ?>
                <?php 
                if(in_array('viewUsers',$menu_permission)){
                ?>
                <div class="box-tools pull-right">
                    <a href="users_lst.php" class="btn btn-block btn-primary"><i class="fa fa-bars" aria-hidden="true"></i></a>
                </div>
                <?php
                }
                ?>
            </div>
            <br />
            <form id="thisForm" name="thisForm" class="form-horizontal" method="post" action="users.php">
                <div class="box-body">
                    <div class="col-md-12">
                        <div class="row">

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Name <span class="err">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="uname" id="uname" placeholder="Enter the Name" title="Enter The Name" autocomplete="off" autofocus="autofocus" onKeyPress="return isNameKey(event);" required oninvalid="this.setCustomValidity('Please enter the name...!')" oninput="this.setCustomValidity('')" value="<?php echo $uname; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Mobile No</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="mobile_no" id="mobile_no" maxlength="10" placeholder="Enter the Mobile No" title="Enter the Mobile No" autocomplete="off" onKeyPress="return isNumberKey(event);" value="<?php if(!empty($mobile_no)){ echo $mobile_no; } ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                  <label class="col-sm-4 control-label">Email <span class="err">*</span></label>
                                  <div class="col-sm-8">
                                    <input type="email" class="form-control" name="email" id="email" placeholder="Enter the Email" title="Enter the Email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" onKeyPress="return isEmailKey(event);" required oninvalid="this.setCustomValidity('Please enter the email...!')" oninput="this.setCustomValidity('')" value="<?php echo $email; ?>" autocomplete="off">
                                  </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Roles and Permissions <span class="err">*</span></label>
                                    <div class="col-sm-8">
                                        <select class="form-control select2" name="roles" id="roles" title="Select the Roles and Permissions" required oninvalid="this.setCustomValidity('Please select the roles...!')" onchange="this.setCustomValidity('')">
                                            <option value=""></option>
                                            <?php
                                                echo $dbcon->fnFillComboFromTable_Where("id", "role_name", "tbl_roles", "role_name", " WHERE del_status = 0");
                                            ?>
                                        </select>
                                        <script>document.thisForm.roles.value = "<?php echo $roles_id; ?>"</script>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Status</label>
                                    <div class="col-sm-8">
                                        <select class="form-control select2" id="status" name="status">
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
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Username <span class="err">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="username" id="username" placeholder="Enter the Username" title="Enter the Username" onKeyPress="return isSmallNoSpace(event);" required oninvalid="this.setCustomValidity('Please enter the username...!')" oninput="this.setCustomValidity('')" autocomplete="off" value="<?php echo $username; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Password <span class="err">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="password" class="form-control" name="password" id="password" placeholder="Enter the Password" title="Enter the Password in Eight or More Characters" pattern=".{8,}"  required oninvalid="this.setCustomValidity('Please enter the password in eight or more characters...!')" oninput="this.setCustomValidity('')" autocomplete="off" value="<?php echo $hint_password; ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4 control-label">Retype Password <span class="err">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Enter the Retype Password" title="Enter the Retype Password" required autocomplete="off" value="<?php echo $hint_password; ?>">
                                    </div>
                                </div>
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

<script>
//------ Retype/ Confirm Password Match Checking ------
var password = document.getElementById("password"), confirm_password = document.getElementById("confirm_password");
function validatePassword(){
    if(password.value != confirm_password.value) {
        confirm_password.setCustomValidity("Passwords Don't Match");
    } else {
        confirm_password.setCustomValidity('');
    }
}
password.onchange = validatePassword;
confirm_password.onkeyup = validatePassword;
//------ Retype/ Confirm Password Match Checking ------
</script>

<script type="text/javascript">
$(document).ready(function() {
    $('#settingsMainNav').addClass('active');
    $('#settingsUsersSubNav').addClass('active');
});
</script>