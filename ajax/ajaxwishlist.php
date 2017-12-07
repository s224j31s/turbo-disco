<?php
session_start();
include("../autoloader.php");
if($_SERVER["REQUEST_METHOD"] == "POST"){
  
  //receive post variables
  $product_id = $_POST["id"];
  $list_id = $_POST["listid"];
  $action = $_POST["action"];
  $user_id = $_POST["userid"];
  
  //create new wishlist instance
  $wishlist = new WishList($user_id);
  
  //create response array
  $response = array();
  //respond to action
  if($action == "delete"){
    //delete an item from the wishlist
    if($wishlist -> removeItem($product_id)){
      $response["success"] = true;
      echo json_encode($response);
    }
  }
  if($action == "add"){
    //add an item to the wishlist
    $result = $wishlist -> addItem($product_id);
    if($result === true){
      $response["success"] = true;
      echo json_encode($response);
    }
    else{
      $response["success"] = false;
      echo json_encode($response);
    }
  }
  if($action == "list"){
    $result = $wishlist -> getList();
    if( count($result) > 0){
      $response["success"] = true;
      $response["data"] = $result;
      echo json_encode($response);
    }
    else{
      $response["success"] = false;
      echo json_encode($response);
    }
  }
}
else{
  exit();
}
?>