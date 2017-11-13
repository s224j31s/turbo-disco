<?php
session_start();
include("autoloader.php");
include("includes/database.php");
//get search parameter
$keyword = $_GET["search-query"];
if(!$keyword){
  //if there is no keyword
  header("location:index.php");
}

if(!$_GET["page"]){
  $page = 1;
}
else{
  $page = $_GET["page"];
}

$items_per_page = 8;
$offset = $items_per_page * ($page-1);

//create a query to get data from database
$search_query = "SELECT 
Menu.menu_id,
Menu.name,
Menu.description,
Menu.price,
images.image_file
FROM Menu
INNER JOIN Menu_image 
ON Menu.menu_id = Menu_image.menu_id
INNER JOIN images
ON Menu_image.image_id = images.image_id
WHERE Menu.name LIKE ? 
OR Menu.description LIKE ?
LIMIT ? OFFSET ?";

//get search result to display
//send query to database
$statement = $connection -> prepare($search_query);
//create and bind parameters
$search_term = "%".$keyword."%";
$statement -> bind_param("ssii",$search_term,$search_term,$items_per_page,$offset);
//execute the query
if( $statement -> execute() ){
  $result = $statement -> get_result();
}
else{
  //error executing query
}

//get total search result
//bind different parameters to get total search result
$zero = 0;
$limit = 10000;
$statement -> bind_param("ssii",$search_term,$search_term,$limit,$zero);
if( $statement -> execute() ){
  $total_result = $statement -> get_result();
  $total = $total_result -> num_rows;
}

//calculate paging
//total number of pages
$total_pages = ceil( $total / $items_per_page );
?>

<!doctype html>
<html>
  <?php
  $page_title = "Search results for ".$keyword;
  include("includes/head.php");
  ?>
  <body class ="body2">
    <?php include("includes/navigation.php"); ?>    
    <div class="container">
      <?php
      if( $result -> num_rows > 0){
        //tell the user how many items in result
        echo "<div class=\"row\">
        <div class=\"col-md-12\">
          <h4>Your search for <strong> $keyword </strong> returned $total results</h4>
          <p>Displaying page $page of $total_pages</p>
        </div>
        </div>";
        //create pagination of search results
        if($total_pages > 1){
          echo "<div class=\"row\">
            <div class=\"col-md-12\">
              <ul class=\"pagination\">
                <li>
                  <a href=\"#\" aria-label=\"Previous\">
                    <span aria-hidden=\"true\">&laquo;</span>
                  </a>
                </li>";
          //pages links
          for($i = 0; $i < $total_pages; $i++){
            $page_num = $i + 1;
            $search_str = urlencode($keyword);
            if($page_num == $page){
              $class = "class=\"active\"";
            }
            else{
              $class = "";
            }
            echo "<li $class><a href=\"search.php?search-query=$search_str&page=$page_num\">$page_num</a></li>";
          }
          echo  "<li>
                  <a href=\"#\" aria-label=\"Next\">
                    <span aria-hidden=\"true\">&raquo;</span>
                  </a>
                </li>
              </ul>
            </div>
          </div>";
        }
        
        //render search results
        $counter = 0;
        while($row = $result -> fetch_assoc() ){
          $counter++;
          if($counter == 1){
            echo "<div class=\"row\">";
          }
          $id = $row["menu_id"];
          $name = $row["name"];
          $description = new TrimWords( $row["description"], 10 );
          $price = $row["price"];
          $image = $row["image_file"];
          
          echo "<div class=\"col-md-3\">";
          echo "<h3 class=\"menu-name cap\">$name</h3>";
          echo "<img class=\"img-responsive\" src=\"products_images/$image\">";
          echo "<p class=\"menu-description\">$description</p>";
          echo "<h4 class=\"price\">$price</h4>";
          echo "<a href=\"menu.php?id=$id\">Details</a>";
          echo "</div>";
          
          if($counter==4){
            echo "</div>";
            $counter=0;
          }
          
        }
      }
      else{
        echo "<div class=\"row\">
        <div class=\"col-md-12\"><h4>Your search for $keyword returned no result</h4></div>
        </div>";
      }
      ?>
    </div>
  </body>
</html>