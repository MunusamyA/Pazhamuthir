<?php
ob_start();
session_start();

require_once("includes/common/connection.php");
require_once("includes/common/dbfunctions.php");
require_once("includes/common/functions.php");

$con = new connection();
$dbcon = new dbfunctions();

// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL);

if (isset($_REQUEST["Submit"])) {
  $sql = "SELECT * FROM tbl_users WHERE status = 1 AND username = '". $_POST["user_name"] ."' AND password = '". md5(sha1($_POST["password"])) ."' AND del_status = 0";
  $result = $con->query($sql);
  $no = $result->rowCount();
  if ($no > 0) 
  {
    $obj = $result->fetch(PDO::FETCH_OBJ);
    $_SESSION["user_id"] = $obj->id;
    $_SESSION["uname"] = ucwords(strtolower($obj->uname));
    $_SESSION["username"] = $obj->username;
    $_SESSION["email"] = $obj->email;
    $_SESSION["roles_id"] = $obj->roles_id;
    $_SESSION["menu_permission"] = $dbcon->GetOneRecord("tbl_roles", "menu_permission", "id", $obj->roles_id);
    $_SESSION['loggedin_time'] = time();  

    $_SESSION['msg_login'] = "Signin Successfully";
  } else {
    $_SESSION['msg_login'] = "Invalid Username or Password!";
  }
}

if(isset($_SESSION["user_id"])) {
  if(!isLoginSessionExpired()) {
    header("Location:dashboard.php");
  } else {
    header("Location:logout.php");
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Pazhamuthir POS - ERP</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <link rel="shortcut icon" href="assets/images/emblem_ramakrishna_order.png">

  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="assets/bower_components/bootstrap/dist/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="assets/bower_components/font-awesome/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="assets/bower_components/Ionicons/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="assets/dist/css/AdminLTE.min.css">
  
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <!-- /.login-logo -->
  <div class="login-box-body">
    <div class="login-logo" style="margin-bottom: 10px;">
      <a href="#"><b>Pazhamuthir POS</b></a>
    </div>
    
    <div class="login-logo">
      <img src="assets/images/Pazhamudir_logo">
      <!-- LOGO -->
    </div>

    <p class="login-box-msg">Sign in to start your session <br><span class="text-danger"><?php echo $_SESSION['msg_login']; $_SESSION['msg_login'] = ""; ?></span></p>

    <form action="index.php" method="post">
      <!-- <div class="form-group has-feedback">
        <input type="text" name="user_name" id="user_name" value="admin" class="form-control" placeholder="Username/Email" autofocus autocomplete="off">
        <span class="glyphicon glyphicon-user form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" name="password" id="password" value="admin1234" class="form-control" placeholder="Password" autocomplete="off">
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div> -->
      <div class="input-group">
        <input type="text" name="user_name" id="user_name" value="admin" class="form-control" placeholder="Username/Email" autofocus autocomplete="off">
        <div class="input-group-addon" style="background-color: #E9ECEF;">
            <a href="#"><i class="glyphicon glyphicon-user" aria-hidden="true"></i></a>
        </div>
      </div>
      <br>
      <div class="input-group" id="show_hide_password">
        <input type="password" name="password" id="password" value="admin1234" class="form-control" placeholder="Password" autocomplete="off">
        <div class="input-group-addon" style="background-color: #E9ECEF;">
            <a href=""><i class="glyphicon glyphicon-eye-close" aria-hidden="true"></i></a>
        </div>
      </div>
      <br>
      <div class="row">
        <!-- /.col -->
        <div class="col-xs-12">
          <button type="submit" name="Submit" class="btn btn-primary btn-block btn-lg">Sign In</button>
        </div>
        <!-- /.col -->
      </div>
    </form>

    <!-- <a href="#">I forgot my password</a><br>
    <a href="register.html" class="text-center">Register a new membership</a> -->

  </div>
  <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<!-- jQuery 3 -->
<script src="assets/bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

<script>
$(document).ready(function() {
    $("#show_hide_password a").on('click', function(event) {
        event.preventDefault();
        if($('#show_hide_password input').attr("type") == "text"){
            $('#show_hide_password input').attr('type', 'password');
            $('#show_hide_password i').addClass( "glyphicon-eye-close" );
            $('#show_hide_password i').removeClass( "glyphicon-eye-open" );
        }else if($('#show_hide_password input').attr("type") == "password"){
            $('#show_hide_password input').attr('type', 'text');
            $('#show_hide_password i').removeClass( "glyphicon-eye-close" );
            $('#show_hide_password i').addClass( "glyphicon-eye-open" );
        }
    });
});  
</script>
</body>
</html>
