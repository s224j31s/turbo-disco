<?php
session_start();
include("autoloader.php");
//check if user is admin, else redirect
if( isset($_SESSION["admin"]) === false ){
  //redirect and exit
  header("location:login2.php");
  exit();
}

//create a new account instance
$account = new Account();
$users = $account -> getAllAccounts();

?>
<!doctype html>
<html>
  <?php 
  $page_title = "Admin";
  include("includes/head.php");?>
  <body>
    <?php include("includes/navigation.php"); ?>
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <ul class="nav nav-tabs" role="tablist">
            <li role="presentation" class="active">
              <a href="#accounts" aria-controls="accounts" role="tab" data-toggle="tab">Accounts</a>
            </li>
            <li role="presentation">
              <a href="#products" aria-controls="products" role="tab" data-toggle="tab">Products</a>
            </li>
            
            <li role="presentation"><a href="#messages" aria-controls="messages" role="tab" data-toggle="tab">Messages</a></li>
            <li role="presentation"><a href="#settings" aria-controls="settings" role="tab" data-toggle="tab">Settings</a></li>
          </ul>
          <div class="tab-content">
            <div role="tabpanel" class="tab-pane" id="products">...</div>
            <div role="tabpanel" class="tab-pane active" id="accounts">
              <?php
              if( count($users) > 0 ){
                foreach( $users as $useraccount){
                  $userid = $useraccount["id"];
                  $username = $useraccount["username"];
                  $useremail = $useraccount["email"];
                  $userimage = $useraccount["profile_image"];
                  $usercreated = $useraccount["created"];
                  $userlastseen = $useraccount["lastseen"];
                  $userstatus = $useraccount["status"];
                  
                  echo "<div class=\"row\">
                  <h3>$username</h3>
                  </div>";
                }
              }
              ?>
            </div>
            <div role="tabpanel" class="tab-pane" id="messages">...</div>
            <div role="tabpanel" class="tab-pane" id="settings">...</div>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
