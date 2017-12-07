<?php
include("../autoloader.php");
if($_SERVER["REQUEST_METHOD"] == "POST"){
  //initialise shopping cart
  $cart = new ShoppingCart();
  //add array for response
  $response = array();
  //check action variable
  $action = $_POST["action"];
  switch($action){
    case "add":
      //add the item to the shopping cart
      $product_id = $_POST["menu_id"];
      $quantity = $_POST["quantity"];
      if ( $cart -> addItem($product_id,$quantity) ){
        //get total of items in cart
        $total = $cart -> getCount();
        $response["total"] = $total;
        $response["success"] = true;
      }
      else{
        $response["success"] = false;
      }
      break;
    case "list":
      //list items in the shopping cart
      $items = $cart -> getItems();
      if( count($items) > 0 ){
        $response["success"] = true;
        $response["items"] = $items;
      }
      else{
        $response["success"] = false;
      }
      break;
    case "delete" :
      $product_id = $_POST["menu_id"];
      $delete = $cart -> removeItem($product_id);
      if( $delete === true){
        $response["success"] = true;
      }
      else{
        $response["success"] = false;
      }
      break;
    default:
      break;
  }
  echo json_encode($response);
}
?>