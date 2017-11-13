<?php
class Products extends Database{
  private $dbconn;
  private $query = "SELECT 
      Menu.menu_id AS id,
      Menu.name AS name,
      Menu.description AS description,
      Menu.price AS price,
      images.image_file AS image
      FROM Menu 
      INNER JOIN Menu_image 
      ON Menu.menu_id=Menu_image.Menu_id
      INNER JOIN images
      ON images.image_id = menu_image.image_id";
  private $grouping ="GROUP BY menu.menu_id";
  private $order = "ORDER BY Menu.menu_id";
      
  //constructor
  public function __construct(){
    //initialise parent class which is "Database"
    parent::__construct();
    $this -> dbconn = $this -> connection;
  }
  
  //get all products
  public function getProducts($categories = NULL,$size = 8,$page = 1){
    if($categories || $categories > 0){
      $this -> getCategories($categories);
    }
    else{
      //no categories specified so get all products
      $query = $this -> query . " ASC
      LIMIT ? OFFSET ?";
      //prepare the statement
      $statement = $this -> dbconn -> prepare($query);
      $statement -> bind_param("ii",$size,$page);
      $statement -> execute();
      $result = $statement -> get_result();
      if($result -> num_rows > 0){
        $products = array();
        while( $row = $result -> fetch_assoc() ){
          array_push($products, $row);
        }
        return $products;
      }
      else{
        return false;
      }
    }
  }
  //get categories
  public function getCategories($categories){
    //check if categories is an array
    if(gettype($categories) !== "array"){
      return false;
    }
    else{
      $categorised_query = $this -> query . "
      INNER JOIN menu_sort
      ON menu.menu_id = menu_sort.menu_id
      WHERE ";
      //construct query using categories
      $cat_count = count($categories);
      for($i=0; $i<$cat_count; $i++){
        //build query
        $categorised_query = $categorised_query . "menu_sort.category_id=?";
        if($i !== $cat_count - 1){
          $categorised_query = $categorised_query . " " . " OR ";
        }
        //build parameter string
        $param_string = $param_string . "i";
      }
      echo $categorised_query ."<br>";
      echo $param_string;
      //prepare query
      $statement = $this -> dbconn -> prepare($categorised_query);
    }
  }
}
?>