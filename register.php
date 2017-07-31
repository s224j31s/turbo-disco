<?php

include("includes/database.php");
//PROCESS REGISTRATION WITH PHP
//print_r($_SERVER);
if($_SERVER["REQUEST_METHOD"]=="POST"){
 // print_r($_POST);
  
  $errors = array();
  $username = $_POST["username"];
  
  if(strlen($username)>16){
    $errors["username"] = "username too long";
  }
  
  if(strlen($username)<6){
    $errors["username"]  = $errors["username"] . " " . "username is too short";  
  }
  
  if($errors["username"]){
  $errors["username"] = trim($errors["username"]);
  }
  
  $email = $_POST["email"];
  //check and validate email
  $email_check = filter_var($email,FILTER_VALIDATE_EMAIL);
  if($email_check==false){
    $errors["email"] = "email address is not valid";
  }
  $password1 = $_POST["password1"];
  $password2 = $_POST["password2"];
  
  if($password1 !== $password2){
    $errors["password"] = "passwords do not match";
  }
  elseif(strlen($password1) < 8){
    $errors["password"] = "password should be 8 characters";
  }
  
  if(count($errors)==0){
    //hash the password
    $password = password_hash($password1,PASSWORD_DEFAULT);
    
    $query =  "INSERT INTO accounts (username, email, password, status, created) VALUES ('$username', '$email','$password', 1,NOW())";
    
    echo $query;
  
    $result = $connection->query($query);
    
    if($result==true){
      
      $message ="Account successfully created";
    }
    else{
     if($connection->errno == 1062){
       $message = $connection->error;
      
       if(strstr($message, "username")){
         $errors["username"] = "username is already taken";
       }
       
       if(strstr($message, "email")){
         $errors["email"] = "email is already taken";
       }
       
     
     } 
      
    }
    
  }
  
  
}

?>

<!doctype html>

<html>
    <?php 
    $page_title= "Register";
    include("includes/head.php");
    ?>
    
    <body>
      <?php 
        include("includes/navigation.php");
        ?>
        <div class="container">
            <div class ="row">
                <div class= "col-md-4 col-md-offset-4">
                    <form id="registration" action = "register.php" method="post">
                        <h2>Register for an account</h2>
                        
                        <?php 
                        if($errors["username"]){
                          $username_error_class = "has-error";
                        }
                        ?>
                        <div class="form-group <?php echo $username_error_class; ?>">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" name="username" id="username" placeholder="minimum 6 characters" value="<?php echo $username;?>">
                      
                          <span class="help-block"><?php echo $errors["username"];?></span>
                        </div>
                        
                           <?php 
                            if($errors["email"]){
                              $email_error_class = "has-error";
                            }
                          ?>
                         <div class="form-group <?php echo $email_error_class;?>">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" name="email" id="email" placeholder="user@domain.com" value="<?php echo $email;?>">
                            
                           <span class="help-block"><?php echo $errors["email"];?></span>
                        </div>
                        
                       <!--password-->
                       
                       <?php
                        if($errors["password"]){
                              $password_error_class = "has-error";
                            }
                       ?>
                         <div class ="form-group <?php echo $password_error_class?>">
                           <label for="password1">Password</label>
                           <input class= "form-control" type= "password" name="password1" id="password1" placeholder ="minimum 8 characters">
                           
                           <label for="password2">Password</label>
                           <input class= "form-control" type= "password" name="password2" id="password2" placeholder ="Retype password">
                         
                         <span class="help-block">
                           <?php 
                           echo $errors["password"];
                           ?>
                         </span>
                         </div>
                        
                         <p>Have an account? <a href="login.php">Sign In</a></p>
                         <div class ="text-center">
                           <button type= "submit" class="btn btn-info">Register</button>
                         </div>
                    </form>
                    
                    
                </div>
                <?php 
                
                if($message){
                  echo "<div class=\"alert alert-success\">$message</div>";
                }
                ?>
            </div>
            
        </div>
    </body>
</html>
