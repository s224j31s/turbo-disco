<?php
//handles account creation and authentication
class Account extends Database{
  private $conn;
  private $errors;
  private $username_min_length = 6;
  private $username_max_length = 16;
  private $password_min_length = 8;
  private $account_id;
  //class constructor
  public function __construct(){
    $db = parent::__construct();
    $this -> conn = $this -> getConnection();
    $this -> errors = array();
  }
  
  //Account Registration
  public function register($name,$email,$password1,$password2){
    //array to store errors
    $register_errors = array();
    //convert name to lowercase
    //check username
    if( $this -> validateUserName($name) === false ){
      $register_errors["username"] = 1;
    }
    //check passwords
    if( $this -> validatePasswords($password1,$password2) == false ){
      $register_errors["password"] = 1;
    }
    //check email address
    if( filter_var($email,FILTER_VALIDATE_EMAIL) === false ){
        $register_errors["email"] = "invalid email address";
    }
    //if there are no errors insert user to database
    if( count($register_errors) == 0 ){
      $query = "INSERT INTO accounts (username,email,password,status,created)
                VALUES (?,?,?,1,NOW())";
          
      //hash password
      $hash = password_hash($password1,PASSWORD_DEFAULT);
      //prepare query
      $statement = $this -> conn -> prepare($query);
      //bind parameters to query
      $statement -> bind_param("sss",$name,$email,$hash);
      //check if success
      if($statement -> execute() ){
        //log user in after registration
        $this -> account_id = $this -> conn -> insert_id;
        $_SESSION["id"] = $this -> account_id;
        $_SESSION["username"] = $name;
        $_SESSION["email"] = $email;
        $_SESSION["profile_image"] = 'profile.png';
        if( $this -> isUserAdmin($this -> id) ){
          $_SESSION["admin"] = 1;
        }
        return true;
      }
      else{
        //check for error type
        if($this -> conn -> errno == "1062"){
          //1062 = duplicate email or username error
          //check if error message contains "username"
          $errormsg = $this -> conn -> error;
          if( strstr($errormsg,"username") ){
            $register_errors["username"] = "username already used";
          }
          if( strstr($errormsg,"email")  ){
            $register_errors["email"] = "email address already used";
          }
        }
        $this -> errors["register"] = implode(" ",$register_errors);
        return false;
      }
    }
    else{
        $this -> errors["register"] = implode(" ",$register_errors);
        return false;
    }
  }
  
  //Account authentication
  public function authenticate($name_or_email,$password){
    //return true if successful
    $query = "SELECT id,username,email,password,profile_image FROM accounts 
            WHERE username=? OR email=? AND status=1";
    $statement = $this -> conn -> prepare($query);
    $statement -> bind_param("ss",$name_or_email,$name_or_email);
    if( $statement -> execute() ){
      $result = $statement -> get_result();
      //convert result into associative array
      $user = $result -> fetch_assoc();
      $id = $user["id"];
      $username = $user["username"];
      $profile_image = $user["profile_image"];
      $email = $user["email"];
      //hash of password stored in database
      $stored_hash = $user["password"];
        
        //check if supplied password matches the stored hash
      if( password_verify( $password, $stored_hash ) ){
        //log user in and return true
        //indicate that the user has logged in using session variables
        $_SESSION["id"] = $id;
        $_SESSION["username"] = $username;
        $_SESSION["email"] = $email;
        $_SESSION["profile_image"] = $profile_image;
        //check if user is admin
        if( $this -> isUserAdmin($id) ){
          $_SESSION["admin"] = 1;
        }
        $this -> account_id = $id;
        return true;
      }
      else{
        return false;
      }
    }
    else{
      return false;
    }
  }
  private function isUserAdmin($id){
    //return true if admin false if not
    $query = "SELECT id FROM admin WHERE userid = ?";
    $statement = $this->conn -> prepare($query);
    $statement -> bind_param("i",$id);
    $statement -> execute();
    $result = $statement -> get_result();
    //if user exists in admin table
    if($result -> num_rows > 0){
      //set last login
      return true;
    }
    //user is not admin
    else{
      return false;
    }
  }
  private function validateUserName($name){
    //remove space before and after user name
    $name = trim($name);
    //create array to store username errors
    $name_errors = array();
    //check user name if within range of min or max length
    if( 
      strlen($name) > $this -> username_max_length 
      || 
      strlen($name) < $this -> username_min_length 
      )
    {
      $name_errors["length"] = "user name should be between 6 and 16 characters";
    }
    //check if user name is only alphanumeric
    if( ctype_alnum($name) == false ){
      //if there is already a username error
      $name_errors["chars"] = "user name should contain only alphanumeric characters";
    }
    //check if user name contains spaces
    if( strpos($name," ") !== false){
      //if true, remove spaces
      //$suggest_name = str_replace(" ","",$name);
      $name_errors["spaces"] = "username cannot contain spaces";
    }
    //if there are no name errors
    if( count($name_errors) == 0 ){
      //there are no errors
      return true;
    }
    else{
      //add errors to errors array
      $this -> errors["username"] = implode(" ",$name_errors);
      return false;
    }
  }
  private function validatePasswords($password1,$password2){
    //check passwords
    $password_errors = array();
    if( $password1 !== $password2 ){
      $password_errors["password"] = "passwords are not identical";
    }
    if( strlen($password1) < 8 ){
      $password_errors["password"] = "password should be at least 8 characters";
    }
    
    if( count($password_errors) > 0 ){
      $this -> errors["password"] = implode(" ",$password_errors);
      return false;
    }
    else{
      return true;
    }
  }
  
  public function getAccountId(){
    return $this -> account_id;
  }
  public function checkIfUserExists($username){
    $query = "SELECT username FROM accounts WHERE username=?";
    $statement = $this -> conn -> prepare($query);
    $statement -> bind_param("s",$username);
    $statement -> execute();
    $result = $statement -> get_result();
    if($result -> num_rows > 0){
      //check if the username compares
      $row = $result -> fetch_assoc();
      if( strtolower($username) == strtolower($row["username"]) ){
        return true;
      }
      else{
        return false;
      }
    }
    else{
      return false;
    }
  }
  public function checkIfEmailExists($email){
    $query = "SELECT email FROM accounts WHERE email=?";
    $statement = $this -> conn -> prepare($query);
    $statement -> bind_param("s",$email);
    $statement -> execute();
    $result = $statement -> get_result();
    if($result -> num_rows > 0){
      //email exists
      return true;
    }
    else{
      //email does not exist
      return false;
    }
  }
  public function getErrors(){
    return $this -> errors;
  }
    public function updateAccountStatus($account_id,$status){
    $query = "UPDATE accounts SET status= ? WHERE id= ?";
    $statement = $this -> conn -> prepare( $query );
    $statement -> bind_param("ii",$account_id,$status);
    if( $statement -> execute() ){
      return true;
    }
    else{
      return false;
    }
  }
  
  public function getAllAccounts(){
    //get account_id from session
    $this -> account_id = $_SESSION["id"];
    //check if user is admin
    if( $this -> isUserAdmin($this -> account_id) == false ){
      exit();
    }
    else{
      $query = "SELECT id,
      username,
      email,
      profile_image,
      created,
      status
      FROM accounts";
      $statement = $this -> conn -> prepare( $query );
      $statement -> execute();
      $result = $statement -> get_result();
      $accounts = array();
      if($result -> num_rows > 0){
        while( $row = $result -> fetch_assoc() ){
          array_push($accounts,$row);
        }
        return $accounts;
      }
      else{
        return false;
      }
    }
  }
}


?>