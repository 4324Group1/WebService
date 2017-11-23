<?php

/**
 * File to handle all API requests
 * Accepts GET and POST
 *
 * Each request will be identified by TAG
 * Response will be JSON data

 /**
 * check for POST request
 */
class User{
	var $uid;
	var $name;
	var $email;
	var $created_at;
	var $updated_at;
	var $mcv;
	var $gender;
	var $address;
	var $phone;

	function User($uid,$name,$email,$created_at,$updated_at,$mcv, $gender, $address, $phone){
	$this -> uid= $uid;
	$this -> name=$name;
	$this -> email=$email;
	$this -> created_at=$created_at;
	$this -> updated_at=$updated_at;
	$this -> gender =$gender;
	$this -> address = $address;
	$this -> phone = $phone;
	$this -> mcv=$mcv;
	}
}
// include db handler
	require_once 'include/DB_Functions.php';
	$db = new DB_Functions();

	

if (isset($_POST['tag']) && $_POST['tag'] != '') {
	// get tag
	$tag = $_POST['tag'];

	

	// check for tag type
	if ($tag == 'login') {
		// Request type is check Login
		$email = $_POST['email'];
		$password = $_POST['password'];
		// response Array
		$response = array("tag" => $tag, "success" => 0, "error" => 0);
		// check for user
		$user = $db->getUserByEmailAndPassword($email, $password);
		if ($user != false) {
			// user found
			// echo json with success = 1
			$response["success"] = 1;
			$response["uid"] = $user["unique_id"];
			$response["user"]["name"] = $user["name"];
			$response["user"]["email"] = $user["email"];
			$response["user"]["created_at"] = $user["created_at"];
			$response["user"]["updated_at"] = $user["updated_at"];
			$response["user"]["mcv"] = $user["mcv"];
			echo json_encode($response);
		} else {
			// user not found
			// echo json with error = 1
			$response["error"] = 1;
			$response["error_msg"] = "Incorrect email or password!";
			echo json_encode($response);
		}
	}else if($tag =='checkpass'){

		$uid = $_POST['uid'];
		$password = $_POST['password'];
		// response Array
		$response = array("tag" => $tag, "success" => 0, "error" => 0);
		$user = $db->checkpass($uid, $password);
		if ($user != false){
			$response['success'] =1;
			echo json_encode($response);
		}
		else 
		{
			// user not found
			// echo json with error = 1
			$response["error"] = 1;
			$response["error_msg"] = "Incorrect password!";
			echo json_encode($response);
		}

	}

	 else if ($tag == 'register') {
		// Request type is Register new user
		$name = $_POST['name'];
		$email = $_POST['email'];
		$password = $_POST['password'];
		$response = array("tag" => $tag, "success" => 0, "error" => 0);

		// check if user is already existed
		if ($db->isUserExisted($email)) {
			// user is already existed - error response
			$response["error"] = 2;
			$response["error_msg"] = "Email already existed";
			echo json_encode($response);
		} else {
			// store user
			$user = $db->storeUser($name, $email, $password);
			if ($user) {
				// user stored successfully
				$response["success"] = 1;
				$response["uid"] = $user["unique_id"];
				$response["user"]["name"] = $user["name"];
				$response["user"]["email"] = $user["email"];
				$response["user"]["created_at"] = $user["created_at"];
				$response["user"]["updated_at"] = $user["updated_at"];
				echo json_encode($response);
			} else {
				// user failed to store
				$response["error"] = 1;
				$response["error_msg"] = "Error occurred in Registration";
				echo json_encode($response);
			}
		}
	}
	else if($tag == 'editprofile'){
		$uid =$_POST['uid'];
		$name = $_POST['name'];
		$email = $_POST['email'];
		$password = $_POST['password'];
		$phone = $_POST['phone'];
		$address = $_POST['address'];
		$gender =$_POST['gender'];
		$mcv="";
		$response = array("tag" => $tag, "success" => 0, "error" => 0);

		if(strlen($email)>0){
			if(!$db ->isUserExisted($email))
			{
			$result = $db -> updateUser($uid,$name, $email,$gender, $password,$address,$phone,$mcv); 
			if ($result != false)
		 {
			// user found
			$row = mysql_fetch_array($result) ;
			$response = new User($row['unique_id'],$row['name'],$row['email'],$row['created_at'],$row['updated_at'],$row['mcv'],$row['gender'],$row['address'],$row['PhoneNumber']);
			echo json_encode($response ,JSON_UNESCAPED_UNICODE);
		}else {
			// user not found
			// echo json with error = 1
			$response["error"] = 2;
			$response["error_msg2"] = "Error occurred in edit user!";
			echo json_encode($response);
		}


			}else{
			
			// user is already existed - error response
			$response["error"] = 1;
			$response["error_msg1"] = "Email already existed!";
			$result =false;
			echo json_encode($response);
		
			}
		}else
		{
			$result = $db -> updateUser($uid,$name, $email,$gender, $password,$address,$phone,$mcv); 
			if ($result != false)
		 {
			// user found
			$row = mysql_fetch_array($result) ;
			$response = new User($row['unique_id'],$row['name'],$row['email'],$row['created_at'],$row['updated_at'],$row['mcv'],$row['gender'],$row['address'],$row['PhoneNumber']);
			echo json_encode($response ,JSON_UNESCAPED_UNICODE);
		}else {
			// user not found
			// echo json with error = 1
			$response["error"] = 2;
			$response["error_msg2"] = "Error edit users!";
			echo json_encode($response);
		}
		}
		
	}
	else {
		echo "Invalid Request";
	}
} 
else if(!empty($_GET['tag']))
{
	 $tag= $_GET['tag'];
	 // response Array
	$response = array("tag" => $tag, "success" => 0, "error" => 0);
	if($tag =='getAllUser')
	{
		$users = $db->getAllUser();
		if ($users != false)
		 {
			// user found
			$response =  Array(); 

			while ($row = mysql_fetch_array($users)) 
			{

				array_push($response, new User($row['unique_id'],$row['name'],$row['email']
					,$row['created_at'],$row['updated_at'],$row['mcv'],$row['gender'],$row['address'],$row['PhoneNumber']));
			}
			
			echo json_encode($response,JSON_UNESCAPED_UNICODE);

		} else {
			// user not found
			// echo json with error = 1
			$response["error"] = 1;
			$response["error_msg"] = "Error get users!";
			echo json_encode($response);
		}
		
		
	} 
	else if($tag ='getuserbyid'){
		if(!empty($_GET['uid'])){
			$users = $db->getUser($_GET['uid']);
		if ($users != false)
		 {
			// user found
		

			$row = mysql_fetch_array($users) ;
			$response = new User($row['unique_id'],$row['name'],$row['email'],$row['created_at'],$row['updated_at'],$row['mcv'],$row['gender'],$row['address'],$row['PhoneNumber']);
			echo json_encode($response ,JSON_UNESCAPED_UNICODE);
		}
		

		} else {
			// user not found
			// echo json with error = 1
			$response["error"] = 1;
			$response["error_msg"] = "Error get user!";
			echo json_encode($response);
		}
		

	}

	else {
		echo "Invalid Request";
	}

}
else {
	echo "Access Denied";
}
?>
