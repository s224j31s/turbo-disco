<?php
class ShoppingCart extends Database{
  //user adds item to cart
  public $lifespan = 30; //cookie lifespan in days
  //unique id for the cart
  private $cart_uid = "";
  private $account_id;
  //class constructor
  public function __construct($life = NULL){
    //construct database instance
    parent::__construct();
    //inherit parents "getConnection()" method and store in conn
    $this -> conn = $this -> getConnection();
    //if lifespan is set
    if( isset($life) ){
      $this -> lifespan = $life;
    }
    //get account id if exists (user is logged in)
    if( isset($_SESSION["id"]) ){
      $this -> account_id = $_SESSION["id"];
      //if user is logged in, record cookie id in db
      //however this needs the cart uid, which is called in the line below
    }
    //create cart id
    $this -> cart_uid = $this -> getCartUid();
  }
  
  private function getCartUid(){
    //if user is not logged in
    if( isset($this -> account_id) == false){
      //if cookie does not already exist
      $cid = $this -> createCookie();
    }
    //if user is logged in
    else{
      //get the cart_uid from db
      $query = "SELECT cart_uid FROM shopping_cart WHERE account_id=?";
      $statement = $this -> conn -> prepare( $query );
      $statement -> bind_param("i" , $this -> account_id);
      
      if( $statement -> execute() ){
        $result = $statement -> get_result();
        //if user cart exists
        if($result -> num_rows > 0){
          $cart = $result -> fetch_assoc();
          //set cookie with the cart_uid from db
          $cid = $cart["cart_uid"];
        }
        //if user does not have a cart
        $cid = $this -> createCookie();
      }
    }
    return $cid;
  }
  private function createCookie(){
    if( isset($_COOKIE["cart"]) == false ){
      $name = "cart";
      $now = time();
      $expiry = $now + (86400 * $this -> lifespan);
      $cid = new Token(8);
      setcookie($name, $cid, $expiry , "/");
    }
    else{
      $cid = $_COOKIE["cart"];
    }
    return $cid;
  }
  //call when user logs out to clear the cookie
  public function clearCart(){
    $name = "cart";
    $now = time();
    $expiry = $now - 1;
    $cid = new Token(8);
    setcookie($name, $cid, $expiry , "/");
  }
  private function createCart(){
    //create the shopping cart record using cart_id
    $query = "INSERT INTO shopping_cart (cart_uid,account_id,updated,status,active) VALUES (?,?,NOW(),1,1)";
    $statement = $this -> conn -> prepare( $query );
    $statement -> bind_param( "si",$this -> cart_uid, $this -> account_id );
    if($statement -> execute()){
      return true;
    }
    else{
      $error_no = $this -> conn -> errno;
      if( $error_no == "1062"){
        //cart already exists
        return true;
      }
      else{
        //some other error
        return false;
      }
    }
  }

  public function updateCartAccount($account_id){
    //associate a cart with an account, use in register and login
    $query = "UPDATE shopping_cart SET account_id=? WHERE cart_uid=?";
    $statement = $this -> conn -> prepare( $query );
    $statement -> bind_param("is",$account_id,$this -> cart_uid);
    if( $statement -> execute() ){
      return true;
    }
    else{
      return false;
    }
  }
  public function addItem($product_id,$quantity = 1,$cart_uid=NULL){
    //add the item to the cart using cart uid
    if( $this -> createCart() == false ){
      return false;
    }
    else{
      //cart is already existing or has been created
      //add item to the cart_items database
      $query = "INSERT INTO cart_items 
      (cart_uid,menu_id,quantity,status,active) 
      VALUES (?,?,?,1,1) 
      ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)";
      $statement = $this -> conn -> prepare( $query );
      $statement -> bind_param("sii",$this -> getCartUid() ,$product_id,$quantity);
      if( $statement -> execute() ){
        
        if( isset($this -> account_id) == false){
          return true;
        }
        //if account exists, update the shopping cart
        else{
          $this -> updateCartAccount($this -> account_id);
          return true;
        }
        
      }
      else{
        return false;
      }
      $statement -> close();
    }
  }
  
  public function removeItem($product_id){
    $query = "DELETE FROM cart_items WHERE cart_uid=? AND menu_id=? AND active = 1";
    $statement = $this -> conn -> prepare( $query );
    $statement -> bind_param( "ii",$this -> cart_uid,$product_id );
    if( $statement -> execute() ){
      return true;
    }
    else{
      return false;
    }
    $statement -> close();
  }
  public function updateItem($product_id,$quantity){
    //if product quantity is set to 0, delete the product from cart
    if($quantity == 0){
      $this -> removeItem($product_id);
    }
    else{
      $query = "UPDATE cart_items SET quantity =? WHERE cart_uid =? AND menu_id=? AND active=1";
      $statement = $this -> conn -> prepare( $query );
      $statement -> bind_param("isi",$quantity,$this -> cart_uid,$product_id);
      if( $statement -> execute() ){
        return true;
      }
      else{
        return false;
      }  
    }
  }
  public function getItems(){
    if($this -> account_id){
      $query = "SELECT 
      cart_items.cart_uid AS cart_uid,
      cart_items.menu_id AS product_id,
      cart_items.quantity AS quantity,
      Menu.name AS name,
      Menu.description AS description,
      Menu.price AS price,
      images.image_file AS image
      FROM cart_items
      INNER JOIN shopping_cart 
      ON cart_items.cart_uid = shopping_cart.cart_uid
      INNER JOIN Menu
      ON Menu.menu_id = cart_items.menu_id
      INNER JOIN Menu_image
      ON Menu.menu_id = Menu_image.menu_id
      INNER JOIN images
      ON Menu_image.image_id = images.image_id
      WHERE cart_items.cart_uid = ? 
      AND shopping_cart.account_id = ?
      GROUP BY Menu.menu_id";
      $statement = $this -> conn -> prepare( $query );
      $cart_uid = $this -> getCartUid();
      $statement -> bind_param("si",$cart_uid,$this -> account_id);
    }
    else{
      $query = "SELECT 
      cart_items.cart_uid AS cart_uid,
      cart_items.menu_id AS product_id,
      cart_items.quantity AS quantity,
      Menu.name AS name,
      Menu.description AS description,
      Menu.price AS price,
      images.image_file AS image
      FROM cart_items
      INNER JOIN Menu
      ON Menu.menu_id = cart_items.menu_id
      INNER JOIN Menu_image
      ON Menu.menu_id = Menu_image.menu_id
      INNER JOIN images
      ON Menu_image.image_id = images.image_id
      WHERE cart_items.cart_uid = ? 
      GROUP BY Menu.menu_id";
      $statement = $this -> conn -> prepare( $query );
      $cart_uid = $this -> getCartUid();
      $statement -> bind_param("s",$cart_uid);
    }
    if( $statement -> execute() ){
      $result = $statement -> get_result();
      if($result -> num_rows > 0){
        $items = array();
        $grand_total = 0;
        while( $row = $result -> fetch_assoc() ){
          $row["total"] = $row["price"] * $row["quantity"];
          array_push( $items , $row );
        }
        return $items;
      }
      else{
        return false;
      }
    }
    else{
      return false;
    }
    $statement -> close();
  }
  public function getCount(){
    //query to count items belonging to a user
    $count_query = "SELECT COUNT(menu_id) AS total 
                    FROM cart_items
                    WHERE cart_uid = ?";
    //run the query
    $statement = $this -> conn -> prepare($count_query);
    $statement -> bind_param( "s",$this -> cart_uid );
    $statement -> execute();
    $result = $statement -> get_result();
    $row = $result -> fetch_assoc();
    $count = $row["total"];
    return $count;
  }
  public function getCartId(){
    if($this->cart_uid){
      return $this -> cart_uid;
    }
    else{
      return $this -> getCartUid();
    }
  }
  public function __toString(){
    if($this->cart_uid){
      return $this -> cart_uid;
    }
    else{
      return $this -> getCartUid();
    }
  }
  
  public function checkout(){
    
  }
}


?>