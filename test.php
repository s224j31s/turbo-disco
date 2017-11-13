<?php
session_start();
include("includes/database.php");
include("autoloader.php");

$total_query = "SELECT * from Menu";

  $total = $connection -> prepare($total_query);
  
  $total->execute();
$total_result = $total -> get_result();
$total_products = $total_result->num_rows;


echo $total_products;
?>
<html>
      <?php
  $page_title = "Menu";
  include("includes/head.php");
  ?>
  

    <body>   
    <?php
    include("includes/navigation.php");
    ?>
    
    <div class="container">
        
    </div>
    
    </body>
</html>


