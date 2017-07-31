<?php
$host = "localhost";
$user = "user";
$password = "password";
$database ="datastores";

$connection = mysqli_connect($host,$user,$password,$database);

if(!$connection){
  echo "database error";
}
?>
