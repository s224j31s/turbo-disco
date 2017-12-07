<?php
session_start();
include("autoloader.php");
// include("includes/database.php");

//get product id from GET request or POST request
if( $_GET["id"] ){
  $product_id = $_GET["id"];
}
elseif( $_POST["id"] ){
  $product_id = $_POST["id"];
}

//get user id
if($_SESSION["id"]){
  $user_id = $_SESSION["id"];
  $wish = new WishList($user_id);
  $list_id = $wish -> getListId();
}

//create an instance of product class
$product_instance = new Products();
//pass product id to instance to get product details
$products = $product_instance -> getProductById($product_id);
//create a single product row from the first row of data 
//(if the product has multiple images, it will return multiple rows)
$product_detail = $products[0];

?>
<!doctype html>
<html>
  <?php
  $page_title = ucfirst( $product_detail["name"] );
  include("includes/head.php");
  ?>
  <body>
    <?php include("includes/navigation.php"); ?>    
    <div class="container">
      <div class="row spaced-top">
        <div class="col-md-6">
          <div id="product-detail-images" class="carousel slide" data-ride="carousel">
          <!-- Indicators -->
          <ol class="carousel-indicators">
            <?php
            $counter = 0;
            foreach($products as $item){
              if($counter == 0){
                $active = "class=\"active\"";
              }
              else{
                $active = "";
              }
              echo "<li 
              data-target=\"#product-detail-images\"
              data-slide-to=\"$counter\" $active>
              </li>";
              $counter++;
            }
            ?>
          </ol>
        
          <!-- Wrapper for slides -->
          <div class="carousel-inner" role="listbox">
            <?php
            //output images as slides
            $counter = 0;
            foreach($products as $item){
              $image = $item["image"];
              if( $counter==0 ){
                $slideactive = "active";
              }
              else{
                $slideactive = "";
              }
              echo "<div class=\"item $slideactive\">
              <img src=\"menu_images/$image\">
              </div>";
              $counter++;
            }
            ?>
          </div>
        
          <!-- Controls -->
          <a class="left carousel-control" href="#product-detail-images" role="button" data-slide="prev">
            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
          </a>
          <a class="right carousel-control" href="#product-detail-images" role="button" data-slide="next">
            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
          </a>
        </div>
          
        </div>
        <div class="col-md-6">
          <h3 class="product-name cap"><?php echo $product_detail["name"]; ?></h3>
          <p><?php echo $product_detail["description"]; ?></p>
          <h4 class="price"><?php echo $product_detail["price"]; ?></h4>
            <form id="cart-form" class="form-inline" action="product_detail.php" method="post">
              <div class="form-group">
                <label>Quantity</label>
                <input class="form-control product-quantity" type="number" step="1" min="1" value="1" name ="quantity">
              </div>
              <div class="form-group">
                <button class="btn btn-info" type="submit" name="cart" value="cart" data-id="<?php echo $product_id; ?>">
                  <span class="glyphicon glyphicon-shopping-cart"></span>
                  Add to Cart
                </button>
              </div>
              <div class="form-group">
                <button class="btn btn-info" name="add" type="submit" name="wishlist" value="add" data-id="<?php echo $product_id; ?>" data-list-id="<?php echo $list_id; ?>" data-user-id="<?php echo $user_id; ?>">
                  <span class="glyphicon glyphicon-heart"></span>
                  Add to Wishlist
                </button>
              </div>
            </form>
        </div>
      </div>
    </div>
     <script src="js/shopping.js"></script>
    <script src="js/wishlist.js"></script>
    <template id="spinner-template">
      <span class="spinner-container">
        <img class="spinner" src="graphics/spinner.png">
      </span>
    </template>
    <template id="check-template">
      <span class="glyphicon glyphicon-ok">
      </span>
    </template>
  </body>
</html>