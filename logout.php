<?php
ob_start();
session_start();

session_destroy();

session_start();

$_SESSION['msg_login']="Successfully Sign Out. Please Sign In Again.";

header("Location:index.php");

?>