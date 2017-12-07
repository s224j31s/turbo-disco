
<head>
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>
    <?php echo $page_title; ?>
  </title>
  <link href="components/bootstrap/dist/css/bootstrap.css" rel="stylesheet">
  <link href="components/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link rel="stylesheet" href="https://bootswatch.com/spacelab/bootstrap.min.css">
  <link href="style.css" rel="stylesheet">
  <script src="components/jquery/dist/jquery.js"></script>
  <script src="components/bootstrap/dist/js/bootstrap.js"></script>
  <?php
  //get the total and items from wishlist
  //get total of wishlist items
  if($_SESSION["id"]){
    $user_id = $_SESSION["id"];
    $wish = new WishList($user_id);
    //get the total of 
    $wish_count = $wish -> getCount();
    $wish_items = $wish -> getJSONList();
    //output array of wishlist items into the javascript scope
    if( $wish_count == 0 ){
      //set wish items as an empty array
      $wish_items = "[]";
    }
    echo "<script>var wishlist_items = $wish_items; </script>";
  }
  //we output the count in navigation
    $cart = new ShoppingCart();
  $cart_count = $cart -> getCount();
  //we output the count in navigation
  //get cart uid
  $uid = $cart -> getCartId();
  echo "<script>var cart_uid='$uid';</script>"
  ?>
 
</head>