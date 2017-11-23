<?php

class DB_Functions {

    private $db;

    //put your code here
    // constructor
    function __construct() {
        require_once 'DB_Connect.php';
        // connecting to database
        $this->db = new DB_Connect();
        $this->db->connect();
    }

    // destructor
    function __destruct() {
        
    }

    /**
     * Storing new user
     * returns user details
     */
    public function storeUser($name, $email, $password) {
        $uuid = uniqid('', true);
        $hash = $this->hashSSHA($password);
        $encrypted_password = $hash["encrypted"]; // encrypted password
        $salt = $hash["salt"]; // salt
        $sqlcmd = "INSERT INTO users(unique_id, name, email, encrypted_password, salt, created_at,mcv) VALUES('$uuid', '$name', '$email', '$encrypted_password', '$salt', NOW(),2)";
		$result = mysql_query($sqlcmd);
        // check for successful store
        if ($result) {
            // get user details 
            $result = mysql_query("SELECT * FROM users WHERE unique_id = \"$uuid\"");
            // return user details
            return mysql_fetch_array($result);
        } else {
            return false;
        }
    }

    /**
     * Get user by email and password
     */
    public function getUserByEmailAndPassword($email, $password) {
        $result = mysql_query("SELECT * FROM users WHERE email = '$email'") or die(mysql_error());
        // check for result 
        $no_of_rows = mysql_num_rows($result);
        if ($no_of_rows > 0) {
            $result = mysql_fetch_array($result);
            $salt = $result['salt'];
            $encrypted_password = $result['encrypted_password'];
            $hash = $this->checkhashSSHA($salt, $password);
            // check for password equality
            if ($encrypted_password == $hash) {
                // user authentication details are correct
                return $result;
            }
        } else {
            // user not found
            return false;
        }
    }
    public function getAllUser(){
        $result = mysql_query("SELECT * FROM users ") or die(mysql_error());
         $no_of_rows = mysql_num_rows($result);
        if ($no_of_rows > 0) {
            
            return $result;
        }else {
            // user not found
            return false;
        }
    }
    public function getUser($uid){
        $result = mysql_query("SELECT * FROM users where unique_id = '$uid'") or die(mysql_error());
         $no_of_rows = mysql_num_rows($result);
        if ($no_of_rows > 0) {
            
            return $result;
        }else {
            // user not found
            return false;
        }
    }
   
    public function checkpass($uid,$password){
         $result = mysql_query("SELECT * FROM users WHERE unique_id = '".$uid."'") or die(mysql_error());
        // check for result 
        $no_of_rows = mysql_num_rows($result);
        if ($no_of_rows > 0) {
            $result = mysql_fetch_array($result);
            $salt = $result['salt'];
            $encrypted_password = $result['encrypted_password'];
            $hash = $this->checkhashSSHA($salt, $password);
            // check for password equality
            if ($encrypted_password == $hash) {
                // user authentication details are correct
                return $result;
            }
        } else {
            // user not found
            return false;
        }
    }
     public function updateUser($uid,$name, $email,$gender, $password,$address,$phone,$mcv) {
       $semi = false;
       $cmd='UPDATE users SET ';
       if(strlen($name)>0){
            $cmd = $cmd.' name="'.$name.'"';
            $semi = true;
        }
        if(strlen($email)>0){
            if( $semi == true) $cmd= $cmd.' , ';
            $cmd = $cmd.' email="'.$email.'"';
             $semi = true;
        }
        if(strlen($password)>0){
        $hash = $this->hashSSHA($password);
        $encrypted_password = $hash["encrypted"]; // encrypted password
        $salt = $hash["salt"]; // salt
         if( $semi == true) $cmd= $cmd.' , ';
        $cmd = $cmd.' encrypted_password="'.$encrypted_password.'", salt="'.$salt.'" ';
         $semi = true;
        }
        if(strlen($address)>0){
             if( $semi == true) $cmd= $cmd.' , ';
            $cmd = $cmd.'address="'.$address.'"';
             $semi = true;
        }
        if(strlen($phone)>0){
             if( $semi == true) $cmd= $cmd.' , ';
            $cmd = $cmd.' PhoneNumber="'.$phone.'"';
             $semi = true;
        }
        if(strlen($mcv)>0){
             if( $semi == true) $cmd= $cmd.' , ';
            $cmd = $cmd.' mcv="'.$mcv.'"';
             $semi = true;
        }
        if( $semi == true) $cmd= $cmd.' , ';
        $cmd = $cmd.'gender = '.$gender.', updated_at = NOW() where unique_id= "'.$uid.'"';
        echo $cmd;
        $result = mysql_query($cmd);
        // check for successful store
        if ($result) {
            // get user details 
            $result = mysql_query("SELECT * FROM users WHERE unique_id = \"".$uid."\"");
            // return user details
            return $result;
        } else {
            return false;
        }
    }
    /**
     * Check user is existed or not
     */
    public function isUserExisted($email) {
        $result = mysql_query("SELECT email from users WHERE email = '$email'");
        $no_of_rows = mysql_num_rows($result);
        if ($no_of_rows > 0) {
            // user existed 
            return true;
        } else {
            // user not existed
            return false;
        }
    }

    /**
     * Encrypting password
     * @param password
     * returns salt and encrypted password
     */
    public function hashSSHA($password) {

        $salt = sha1(rand());
        $salt = substr($salt, 0, 10);
        $encrypted = base64_encode(sha1($password . $salt, true) . $salt);
        $hash = array("salt" => $salt, "encrypted" => $encrypted);
        return $hash;
    }

    /**
     * Decrypting password
     * @param salt, password
     * returns hash string
     */
    public function checkhashSSHA($salt, $password) {

        $hash = base64_encode(sha1($password . $salt, true) . $salt);

        return $hash;
    }
}

?>
