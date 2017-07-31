<?php

 include("includes/database.php");
  
if($_SERVER["REQUEST_METHOD"]=="POST"){
    
    $user_email = $_POST["user"];
    
    if(filter_var($user_email,FILTER_VALIDATE_EMAIL)){
        //if true user entered an email
        
         $query = "SELECT * FROM accounts WHERE email='$user_email'";
    }
    
    else{
         $query = "SELECT * FROM accounts WHERE username='$user_email'";
    }
    $password = $_POST["password"];

    //$query = "SELECT * FROM accounts WHERE email='$email'";
    
   //echo $query;
    //check the result
    
    $errors =array();
    $userdata = $connection->query($query);
    
    if($userdata->num_rows > 0){
        $user = $userdata->fetch_assoc();
        if(password_verify($password, $user["password"])==false){
            $errors["account"] = "email or password incorrect";
        }
        else{
            $message = "You have been logged in $email";
            echo $message;
        }
    }
    else{
        $errors["account"] = "There is no user with the supplied credentials";
    }
}

?>


<!doctype html>
<html>
<?php 
$page_title= "Login";
include("includes/head.php");
?>
    
    <body>
        <div class ="container">
            <div class ="row">
                <div class="col-md-4 col-md-offset-4">
                    <form id ="login-form" action="login.php" method ="post">
                        <h1>Login to your Account</h1>
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input class="form-control" type ="user" id="user" name ="user" placeholder="you@email.com or user">
                        </div>
                        
                        <div class ="form-group">
                            <label for="password">Password</label>
                            <input class="form-control" type ="password" id="password" name="password" placeholder="password">
                        </div>
                        
                        <div class="text-center">
                            <button type="submit" name ="submit" value="login" class="btn btn-info">Login</button>
                        </div>
                        <?php 
                        if(count($errors) > 0 || $message){
                            //see which class to be used with alert
                            if(count($errors) > 0){
                                $class = "alert-warning";
                                $feedback = implode(" ",$errors);
                            }
                        }
                        if($message){
                            $class= "alert-success";
                            $feedback = "$message";
                        }
                        
                        echo "<div class=\"alert $class\">$feedback</div>";
                        
                        ?>
                        
                    </form>
                </div>
            </div>
            
        </div>
        
    </body>
</html>