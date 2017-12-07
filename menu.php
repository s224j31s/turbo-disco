<?php
session_start();
include("includes/database.php");
include("autoloader.php");

//handle GET request for pages
if(!$_GET["page"]){
  $pagenumber = 1;
}
else{
  $pagenumber = $_GET["page"];
}

//handle get request for categories
if($_GET["category"] > 0){
  //if there is a GET variable for category, set cat_selected to the same value
  $cat_selected = $_GET["category"];
}
else{
  //if there is no GET variable set it to 0, which will show all categories
  $cat_selected = 0;
}

//get total number of products
//if no category is selected
if($cat_selected == 0){
  $total_query = "SELECT Menu.menu_id 
  FROM Menu 
  INNER JOIN Menu_image
  ON Menu_image.menu_id = Menu.menu_id
  GROUP BY Menu.menu_id";
  $total = $connection -> prepare($total_query);
}
else{
  $total_query = "SELECT Menu.menu_id 
  FROM Menu 
  INNER JOIN Menu_image
  ON Menu.menu_id = Menu.menu_id
  INNER JOIN menu_sort
  ON menu_sort.menu_id = Menu.menu_id
  WHERE menu_sort.category_id = ?
  GROUP BY Menu.menu_id";
  $total = $connection -> prepare($total_query);
  $total -> bind_param("i",$cat_selected);
}
$total->execute();
$total_result = $total -> get_result();
$total_products = $total_result->num_rows;

//number of producs per page
$products_perpage = 8;
//total number of pages
$total_pages = ceil( $total_products / $products_perpage );
//calculate offset for query
$offset = ($pagenumber-1) * $products_perpage;


//get products from database
if($cat_selected == 0){
  $product_query = "SELECT 
  Menu.menu_id,
  Menu.name,
  Menu.description,
  Menu.price,
  images.image_file
  FROM Menu 
  INNER JOIN Menu_image 
  ON Menu.menu_id=Menu_image.menu_id
  INNER JOIN images
  ON images.image_id = Menu_image.image_id
  ORDER BY Menu.menu_id ASC
  LIMIT ? OFFSET ?";
  
  $product_statement = $connection->prepare($product_query);
  $product_statement -> bind_param("ii",$products_perpage,$offset);
}
else{
  $product_query = "SELECT 
  Menu.menu_id,
  Menu.name,
  Menu.description,
  Menu.price,
  images.image_file
  FROM Menu 
  INNER JOIN Menu_image
  ON Menu.menu_id=Menu_image.menu_id
  INNER JOIN images
  ON images.image_id = Menu_image.image_id
  INNER JOIN menu_sort 
  ON Menu.menu_id = menu_sort.menu_id 
  WHERE menu_sort.category_id = ?
  GROUP BY Menu.menu_id
  ORDER BY Menu.menu_id ASC
  LIMIT ? OFFSET ?";
  
  $product_statement = $connection -> prepare($product_query);
  $product_statement -> bind_param("iii",$cat_selected,$products_perpage,$offset);
}

$product_statement->execute();
$result = $product_statement->get_result();

//GET categories from the database
$cat_query = "SELECT 
menu_cat.category_id AS id,
menu_cat.category_name AS name,
COUNT(menu_sort.category_id) AS cat_count
FROM menu_sort 
INNER JOIN menu_cat
ON menu_sort.category_id = menu_cat.category_id
GROUP BY menu_sort.category_id";
$cat_statement = $connection->prepare($cat_query);
$cat_statement->execute();
$cat_result = $cat_statement->get_result();


?>
<!doctype html>
<html>
  <?php
  $page_title = "Menu";
  include("includes/head.php");
  ?>
  
       <body class="body2">
                 <?php include("includes/navigation.php"); ?>    
              
       <div class="jumbotron jumbotron1">
				<div class="container text-center">
				  
					<h1>Menu</h1>
					<?php
					include("includes/search.php");
					?> 
					
					
				</div>
				
			</div>
			
    <div class="container">
      <div class="row">
        <div class="col-md-2">
          <h3>Categories</h3>
          <nav>
            <ul class="nav nav-stacked nav-pills">
             <?php
            
            if($cat_result->num_rows > 0){
              //check value of cat_selected if 0, set active class on all categories
              if($cat_selected==0){
                $active = "class=\"active\"";
              }
              else{
                $active = "";
              }
              echo "<li $active><a href=\"menu.php?category=0\">All categories</a></li>";
              //now output other categories
              while($cat_row = $cat_result->fetch_assoc()){
                $cat_id = $cat_row["id"];
                $cat_name = $cat_row["name"];
                $cat_count = $cat_row["cat_count"];
                
                //if cat_id matches the selected (cat_selected) then set active
                if($cat_selected==$cat_id){
                  $active = "class=\"active\"";
                }
                else{
                  $active = "";
                }
                echo "<li $active data-id=\"$cat_id\">
                <a href=\"menu.php?category=$cat_id&page=1\">$cat_name
                  <span class=\"badge\">$cat_count</span>
                </a>
                </li>";
              }
            }
            // ?>
            </ul>
          </nav>
          
        </div>
        <div class="col-md-10">
          <div class="row">
            <div class="col-md-6">
              <nav aria-label="Page navigation">
                <ul class="pagination">
                  <?php
                   if($pagenumber > 1){
                     $previouspage = $pagenumber - 1;
                     $previous_disable = "";
                   }
                   else{
                     $previous_disable = "disabled";
                   }
                   $previous_link = $_SERVER["PHP_SELF"]
                   . "?page=$previouspage&category=$cat_selected";
                   
                   echo "<li>
                     <a href=\"$previous_link\" $previous_disable aria-label=\"Previous\">
                       <span aria-hidden=\"true\">&laquo;</span>
                     </a>
                   </li>
                   ";
                  ?>
                  
                  <?php
                   for($i=0; $i < $total_pages; $i++){
                     $page_label = $i+1;
                     $page_url = $_SERVER["PHP_SELF"] 
                     . "?page=$page_label&category=$cat_selected";
                     if($page_label == $pagenumber){
                       $page_active = "class=\"active\"";
                     }
                     else{
                       $page_active = "";
                     }
                     echo "<li $page_active>
                     <a href=\"$page_url\">$page_label</a>
                     </li>";
                   }
                  ?>
                  <!--Next Link-->
                   <?php
                   if($pagenumber < $total_pages){
                     $nextpage = $pagenumber + 1;
                     $next_disable = "";
                   }
                   else{
                     $nextpage = $pagenumber;
                     $next_disable = "disabled";
                   }
                  
                   $nextlink = $_SERVER["PHP_SELF"] 
                   . "?page=$nextpage&category=$cat_selected";
                   
                   echo "<li>
                     <a href=\"$nextlink\" $next_disable aria-label=\"Next\">
                       <span aria-hidden=\"true\">&raquo;</span>
                     </a>
                   </li>";
                  // ?>
                 
                </ul>
              </nav>
            </div>
          </div>
          <div class="row">
          <?php
           //output the products in rows
           if($result->num_rows > 0){
             $counter = 0;
             while($row = $result->fetch_assoc()){
               $counter++;
               if($counter==1){
                 echo "<div class=\"row\">";
               }
               $name = $row["name"];
               $id = $row["menu_id"];
               $description = trimWords($row["description"],10);
               $price = $row["price"];
               $image = $row["image_file"];
               echo "<div class=\"col-md-3\">
               <h3 class=\"product-name\">$name </h3>
               <a href=\"product_detail.php?id=$id\">
               <img class=\"img-responsive\" src=\"products_images/$image\">
               </a>
               <h4 class=\"price\">$price</h4>
               <p class=\"product-description\">$description</p>
               <a href=\"product_detail.php?id=$id\">Details</a>
               </div>";
              
               if($counter==4){
                 echo "</div>";
                 $counter=0;
               }
             }
           }
           //function to output only a short description
           function trimWords($str,$count){
             $words_array = explode(" ", $str);
             for($i=0; $i<$count; $i++){
               if($words_array[$i]!=="." && $words_array[$i]!==" " && $words_array[$i]!=="&nbsp;"){
                 $words = $words. " ".trim($words_array[$i]);
               }
             }
             return $words;
           }
          
          ?>
          </div>
        </div>
      </div>
    </div>
    <footer>
      <div class="container">
        <div class="row">
          <div class="col-md-4">
            <h3>This is a footer</h3>
          </div>
        </div>
      </div>
      </footer>
      </body>
  



			</div>

    </body>
    
</html>