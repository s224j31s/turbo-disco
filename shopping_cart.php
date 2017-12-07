<?php
session_start();
include("autoloader.php");
$cart = new ShoppingCart();

if( $_SERVER["REQUEST_METHOD"] == "POST" ){
  $product_id = $_POST["menu_id"];
  $quantity = $_POST["quantity"];
  if( $_POST["submit"] == "delete" ){
    $delete = $cart -> removeItem($product_id);
  }
  if( $_POST["submit"] == "update" ){
    $update = $cart -> updateItem($product_id,$quantity);
  }
}

$cartitems = $cart -> getItems();

$total_count = $cart -> getCount();
if( $total_count > 0 ){
  $page_title = "($total_count) Shopping Cart";
}
else{
  $page_title = "Shopping Cart";
}


?>
<!doctype html>
<html>
  <?php
  include("includes/head.php");
  ?>
  <body>
    <?php include("includes/navigation.php"); ?>    
    <div class="container cart-container">
      <div class="row">
        <div class="col-md-12">
          <h2>Shopping cart</h2>
        </div>
      </div>
      <?php
      if( count($cartitems) > 0 && $cartitems){
        $grand_total = 0;
        foreach($cartitems as $item){
          $cart_uid = $item["cart_uid"];
          $product_price = $item["price"];
          $product_name = $item["name"];
          $product_description = new TrimWords($item["description"],20);
          $product_image = $item["image"];
          $product_id = $item["menu_id"];
          $product_quantity = $item["quantity"];
          $row_total = $product_quantity * $product_price;
          $row_total_display = number_format($row_total,2);
          $grand_total = $grand_total + $row_total;
          echo 
          "<div class=\"row cart-row\" data-product-id=\"$product_id\">
            <div class=\"col-md-2 col-xs-6\">
              <a href=\"product_detail.php?id=$product_id\">
                <img class=\"img-responsive\" src=\"menu_images/$product_image\">
              </a>
            </div>
            <div class=\"col-md-3 col-xs-6\">
              <h3 class=\"product-name\">$product_name</h3>
              <p>$product_description</p>
            </div>
            <div class=\"col-md-5 col-xs-12\">
              <form class=\"form-horizontal\" method=\"post\" action=\"shopping_cart.php\">
                <div class=\"form-group col-md-4 col-sm-3 col-xs-4\">
                  <label>Quantity</label>
                  <input type=\"hidden\" name=\"product_id\" value=\"$product_id\">
                  <div class=\"input-group\" data-group=\"quantity\">
                    <span class=\"input-group-btn\">
                      <button class=\"btn btn-default\" data-function=\"subtract\" data-product-id=\"$product_id\">
                        &minus;
                      </button>
                    </span>
                    <input type=\"type\" name=\"quantity\" data-id=\"$product_id\" min=\"0\" step=\"1\" class=\"form-control\" value=\"$product_quantity\">
                    <span class=\"input-group-btn\">
                      <button class=\"btn btn-default\" data-function=\"add\" data-product-id=\"$product_id\">
                        &plus;
                      </button>
                    </span>
                  </div>
                </div>
                <div class=\"form-group col-md-4 col-sm-2 col-xs-2\">
                  <label>Price</label>
                  <span class=\"price\">
                  <input type=\"text\" name=\"price\" data-type=\"price\" readonly class=\"form-control no-border\" value=\"$product_price\" data-product-id=\"$product_id\" data-cart-uid=\"$cart_uid\">
                  </span>
                </div>
                <div class=\"form-group col-md-4\">
                  <label>Update Delete</label>
                  <div class=\"btn-group\">
                    <button type=\"submit\" class=\"btn btn-default\" data-product-id=\"$product_id\" data-cart-uid=\"$cart_uid\" name=\"submit\" value=\"update\">
                      <span class=\"glyphicon glyphicon-refresh\"></span>
                    </button>
                    <button type=\"submit\" class=\"btn btn-default\" data-product-id=\"$product_id\" data-cart-uid=\"$cart_uid\" name=\"submit\" value=\"delete\">
                      <span class=\"glyphicon glyphicon-remove\"></span>
                    </button>
                  </div>
                </div>
                <div class=\"form-group col-md-2\">
                  <p class=\"form-control text-right no-border price\">$row_total_display</p>
                </div>
              </form>
            </div>
          </div><hr>
          <div class=\"clearfix\"></div>";
        }
       
        if($grand_total > 0){
          //format the total
          $total = number_format($grand_total,2);
          echo "<div class=\"row\">
            <div class=\"col-md-9\">
              <p class=\"form-control no-border\">Total</p>
            </div>
            <div class=\"col-md-3\">
              <p class=\"form-control no-border price text-right\">$total</p>
            </div>
          </div>";
        }
      }
      else{
        echo "<h2 class=\"no-wish text-center\">Your cart is empty</h2>";
      }
      ?>
      <div class="row">
        <div class="col-md-12 text-right">
          <button class="btn btn-success" data-cart-uid="<?php echo $cart_uid;?>">Checkout</button>
        </div>
      </div>
  </div>
  <script src="js/shopping.js"></script>
  </body>
</html>