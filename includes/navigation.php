<header>
    <nav class="navbar navbar-inverse">
        <div class="navbar-header">
            <button class="navbar-toggle collapsed" data-toggle="collapse" data-target="#main-menu">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a href="index.php" class ="navbar-brand">Hello</a>
            <p class="navbar-text">
            <?php 
        if($_SESSION["username"]){
            echo "Hello ". $_SESSION["username"];
        }
        ?>
        </p>
        </div>
        
        <div class="collapse navbar-collapse" id ="main menu">
            <ul class="nav navbar-nav">
                <li><a href ="index.php">Home</a></li>
                <li><a href ="register.php">Register</a></li>
                <li><a href= "login.php">Login</a></li>
                  <li><a href= "account.php">Account</a></li>
            </ul>
                
        </div>
        
        
    </nav>
</header>