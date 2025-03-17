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

//---------------------------------update--------------------------------------
if (isset($_REQUEST["update"])) {
    try{
        //echo "Update"; die();
        $updated_by = $_SESSION["user_id"]; 0;
        $updated_dt = date('Y-m-d H:i:s');

        $stmt = null;
        $stmt = $con->prepare("UPDATE tbl_shop SET shop_name=:shop_name, shop_gst_no=:shop_gst_no, owner_name=:owner_name, mobile_no1=:mobile_no1, mobile_no2=:mobile_no2, email_id=:email_id, door_no=:door_no, address_line1=:address_line1, address_line2=:address_line2, city=:city, shop_state=:shop_state, pincode=:pincode, updated_by=:updated_by, updated_dt=:updated_dt where id=:id");
        $data = array(
            ":id" => trim($_REQUEST["hid_id"]),
            ":shop_name" => trim($_REQUEST["shop_name"]),
            ":shop_gst_no" => trim($_REQUEST["shop_gst_no"]),
            ":owner_name" => trim($_REQUEST["owner_name"]),
            ":mobile_no1" => trim($_REQUEST["mobile_no1"]),
            ":mobile_no2" => trim($_REQUEST["mobile_no2"]),
            ":email_id" => trim($_REQUEST["email_id"]), 
            ":door_no" => trim($_REQUEST["door_no"]), 
            ":address_line1" => trim($_REQUEST["address_line1"]), 
            ":address_line2" => trim($_REQUEST["address_line2"]), 
            ":city" => trim($_REQUEST["city"]), 
            ":shop_state" => trim($_REQUEST["shop_state"]), 
            ":pincode" => trim($_REQUEST["pincode"]), 
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

    header("location: shop.php");
    die();
}
//---------------------------------update----------------------------------------
//---------------------------------delete----------------------------------------
//if (isset($_REQUEST["did"])) {
    // $stmt = null;
    // $stmt = $con->query("UPDATE tbl_shop SET del_status=1 WHERE id=" . $converter->decode($_REQUEST["did"]));

    // $_SESSION["msg"] = "Deleted Successfully";

    // header("location:shop.php");
    // die();
//}
//---------------------------------delete----------------------------------------

$rs = $con->query("SELECT * FROM tbl_shop where id = 1");
if ($rs->rowCount()) {
    if ($obj = $rs->fetch(PDO::FETCH_OBJ)) {
        $id = $obj->id;
        $shop_name = $obj->shop_name;
        $shop_gst_no = $obj->shop_gst_no;
        $owner_name = $obj->owner_name;
        $mobile_no1 = $obj->mobile_no1;
        $mobile_no2 = $obj->mobile_no2;
        $email_id = $obj->email_id;
        $door_no = $obj->door_no;
        $address_line1 = $obj->address_line1;
        $address_line2 = $obj->address_line2;
        $city = $obj->city;
        $shop_state = $obj->shop_state;
        $pincode = $obj->pincode;
    }
}
?>

<?php include("includes/header.php"); ?>
<?php include("includes/aside.php"); ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Shop Details
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-cog"></i> Settings</a></li>
            <li class="active">Shop Details</li>
        </ol>
    </section>
    <section class="content">
        <div class="box box-primary">
            <!-- <div class="box-header with-border">
                <?php //if(isset($_REQUEST["id"])){
                    //echo '<h3 class="box-title">Edit</h3>';
                //}else{
                    //echo '<h3 class="box-title">Add</h3>';
                //} ?>
                <div class="box-tools pull-right">
                    <a href="staffs_lst" class="btn btn-block btn-primary"><i class="fa fa-bars" aria-hidden="true"></i></a>
                </div>
            </div> -->
            <br />
            <form id="thisForm" name="thisForm" action="shop.php" method="post">
                <div class="box-body">
                    <div class="form-group col-md-4">
                        <label class="col-form-label">Shop Name (In CAPS) <span class="err">*</span></label>
                        <input type="text" class="form-control" name="shop_name" id="shop_name" placeholder="Enter the Shop Name" title="Enter the Shop Name" autocomplete="off" autofocus="autofocus" onKeyPress="return isCapitalWithSpace(event);" required oninvalid="this.setCustomValidity('Please enter the shop name...!')" oninput="this.setCustomValidity('')" maxlength="80" value="<?php echo $shop_name; ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label class="col-form-label">Shop GST No. <span class="err">*</span></label>
                        <input type="text" class="form-control" name="shop_gst_no" id="shop_gst_no" placeholder="Enter the Shop GST No." title="Enter the Shop GST No." autocomplete="off" required oninvalid="this.setCustomValidity('Please enter the shop gst no...!')" oninput="this.setCustomValidity('')" maxlength="30" value="<?php echo $shop_gst_no; ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label class="col-form-label">Owner Name <span class="err">*</span></label>
                        <input type="text" class="form-control" name="owner_name" id="owner_name" placeholder="Enter the Owner Name" title="Enter the Owner Name" autocomplete="off" onKeyPress="return isNameKey(event);" required oninvalid="this.setCustomValidity('Please enter the owner name...!')" oninput="this.setCustomValidity('')" maxlength="50" value="<?php echo $owner_name; ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label class="col-form-label">Mobile No. 1 <span class="err">*</span></label>
                        <input type="text" class="form-control" name="mobile_no1" id="mobile_no1" maxlength="10" placeholder="Enter the Mobile No. 1" title="Enter the Mobile No. 1" autocomplete="off" onKeyPress="return isNumberKey(event);" required oninvalid="this.setCustomValidity('Please enter the mobile no. 1...!')" oninput="this.setCustomValidity('')" value="<?php echo $mobile_no1; ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label class="col-form-label">Mobile No. 2 </label>
                        <input type="text" class="form-control" name="mobile_no2" id="mobile_no2" maxlength="10" placeholder="Enter the Mobile No. 2" title="Enter the Mobile No. 2" autocomplete="off" onKeyPress="return isNumberKey(event);" value="<?php echo $mobile_no2; ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label class="col-form-label">Email Id</label>
                        <input type="text" class="form-control" name="email_id" id="email_id" placeholder="Enter the Email Id" title="Enter the Email Id" autocomplete="off" onKeyPress="return isEmailKey(event);" value="<?php echo $email_id; ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label class="col-form-label">Door No. <span class="err">*</span></label>
                        <input type="text" class="form-control" name="door_no" id="door_no" placeholder="Enter the Door No." title="Enter the Door No." autocomplete="off" required oninvalid="this.setCustomValidity('Please enter the door no...!')" oninput="this.setCustomValidity('')" value="<?php echo $door_no; ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label class="col-form-label">Address Line 1 <span class="err">*</span></label>
                        <input type="text" class="form-control" name="address_line1" id="address_line1" placeholder="Enter the Address Line 1" title="Enter the Address Line 1" autocomplete="off" required oninvalid="this.setCustomValidity('Please enter the address line 1...!')" oninput="this.setCustomValidity('')" value="<?php echo $address_line1; ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label class="col-form-label">Address Line 2 <span class="err">*</span></label>
                        <input type="text" class="form-control" name="address_line2" id="address_line2" placeholder="Enter the Address Line 2" title="Enter the Address Line 2" autocomplete="off" required oninvalid="this.setCustomValidity('Please enter the address line 2...!')" oninput="this.setCustomValidity('')" value="<?php echo $address_line2; ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label class="col-form-label">City <span class="err">*</span></label>
                        <input type="text" class="form-control" name="city" id="city" placeholder="Enter the City" title="Enter the City" autocomplete="off" required oninvalid="this.setCustomValidity('Please enter the city...!')" oninput="this.setCustomValidity('')" value="<?php echo $city; ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label class="col-form-label">State <span class="err">*</span></label>
                        <input type="text" class="form-control" name="shop_state" id="shop_state" placeholder="Enter the State" title="Enter the State" autocomplete="off" required oninvalid="this.setCustomValidity('Please enter the state...!')" oninput="this.setCustomValidity('')" value="<?php echo $shop_state; ?>">
                    </div>
                    <div class="form-group col-md-4">
                        <label class="col-form-label">Pincode <span class="err">*</span></label>
                        <input type="text" class="form-control" name="pincode" id="pincode" placeholder="Enter the Pincode" title="Enter the Pincode" autocomplete="off" onKeyPress="return isNumberKey(event);" required oninvalid="this.setCustomValidity('Please enter the pincode...!')" oninput="this.setCustomValidity('')" maxlength="6" value="<?php echo $pincode; ?>">
                    </div>
                </div>
                <div class="box-footer text-right">
                    <?php //if (isset($_REQUEST["id"])) { ?>
                    <input type="hidden" value="<?php echo $id; ?>" name="hid_id">
                    <button type="submit" name="update" class="btn btn-primary"><i class="fa fa-check" aria-hidden="true"></i> Update</button>
                    <?php //} else { ?>
                    <!-- <button type="submit" name="submit" class="btn btn-primary"><i class="fa fa-check" aria-hidden="true"></i> Submit</button> -->
                    <?php //} ?>
                </div>
            </form>
        </div>
    </section>
</div>
<?php include("includes/footer.php"); ?>

<script type="text/javascript">
$(document).ready(function() {
    $('#settingsMainNav').addClass('active');
    $('#settingsShopSubNav').addClass('active');
});
</script>