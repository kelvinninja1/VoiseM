<?php

	class Account {

		public $con;
		private $errorAray;

		// When create variable new Account and set our errorArray variable to a empty array and pass on the connection
		public function  __construct($con) {
			$this->con = $con;
			$this->errorArray = array();
		} 


		public function login($un, $pw) {
			$pw = md5($pw);

			$query = mysqli_query($this->con, "SELECT * FROM users WHERE username='$un' AND password='$pw'");

			if(mysqli_num_rows($query) == 1) {
				return true;
			} else {
				array_push($this->errorArray, 'The username and password is incorrect');
				return false;
			}
		}

		public function register($un, $fn, $ln, $em, $em2, $pw, $pw2) {
			$this->validateUsername($un);
			$this->validateFirstName($fn);
			$this->validateLastName($ln);
			$this->validateEmails($em, $em2);
			$this->validatePasswords($pw, $pw2);

			if(empty($this->errorArray)) {
				//Insert into db
				return $this->insertUserDetails($un, $fn, $ln, $em, $pw);
			}
			else {
				return false;
			}

		}

		//This function is to check the error that we pass in is in the array if its not in the array return empty string
		public function getError($error) {
			if(!in_array($error, $this->errorArray)) {
				$error = "";
			}
			return "<span class='errorMessage'>$error</span>";
		}

		private function insertUserDetails($un, $fn, $ln, $em, $pw) {
			$encryptedPw = md5($pw);
			$profilePic = "assets/images/profile-pics/default-profile-pic.png";
			$date = date("Y-m-d");

			// Perform a query in the database
			$result = mysqli_query($this->con, "INSERT INTO users VALUES ('', '$un', '$fn', '$ln', '$em', '$encryptedPw', '$date', '$profilePic')");

			return $result;
		}

		private function validateUsername($un) {

			if(strlen($un) > 25 || strlen($un) < 5) {
				array_push($this->errorArray, "Your username must be 5 to 25 characters");
				return; // Put in return there when we dont want to execute this function if we find any errors 
			}

			$checkUserNameQuery = mysqli_query($this->con, "SELECT username FROM users WHERE username='$un'");

			if(mysqli_num_rows($checkUserNameQuery) != 0) {
				array_push($this->errorArray, "This username is already taken");
			}
		}	

		private function validateFirstName($fn) {

			if(strlen($fn) > 25 || strlen($fn) < 2) {
				array_push($this->errorArray, "Your first name must be 2 to 25 characters");
				return;  
			}
			
		}

		private function validateLastName($ln) {

			if(strlen($ln) > 25 || strlen($ln) < 2) {
				array_push($this->errorArray, "Your last name must be 2 to 25 characters");
				return; 
			}

		}

		private function validateEmails($em, $em2) {
			
			if($em != $em2) {
				array_push($this->errorArray, "Your email don't match");
				return;
			}

			// This is use to manual validate the e.g .com/.co.uk because it doesnt check in HTML 
			if(!filter_var($em, FILTER_VALIDATE_EMAIL)) {
				array_push($this->errorArray, "Your email invalid");
				return;
			}

			$checkEmailQuery = mysqli_query($this->con, "SELECT email FROM users WHERE email='$em'");

			if(mysqli_num_rows($checkEmailQuery) != 0) {
				array_push($this->errorArray, "This email is already taken");
			}

		}

		private function validatePasswords($pw, $pw2) {

			if($pw != $pw2) {
				array_push($this->errorArray, "Your passwords don't match");
				return;
			}

			//To find out more: https://www.phpjabbers.com/php-validation-and-verification-php27.html
			//Password must be in number and word characters(uppercase/lowercase) only
			if(preg_match('/[^A-Za-z0-9]/', $pw)) {
				array_push($this->errorArray, "Your password can only be numbers and letters");
				return;
			}

			if(strlen($pw) > 25 || strlen($pw) < 5) {
				array_push($this->errorArray, "Your password must be 5 to 25 characters");
				return;
			}
		}

	}
?>
