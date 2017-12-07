<?php
//unset session variables
//to access session, it needs to be started
session_start();
include("autoloader.php");
//unset varibles
unset($_SESSION["username"]);
unset($_SESSION["email"]);
unset($_SESSION["profile_image"]);
unset($_SESSION["admin"]);
unset($_SESSION["id"]);

$cart = new ShoppingCart();
$cart -> clearCart();

//redirect user to home page
header("location: index.php");
?>