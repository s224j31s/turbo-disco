<?php
$dbhost = "localhost";
$dbuser = "user";
$dbpassword = "password";
$dbdatabase ="datastores";

$connection = mysqli_connect($dbhost,$dbuser,$dbpassword,$dbdatabase);

if(!$connection){
  echo "database error";
}
?>
