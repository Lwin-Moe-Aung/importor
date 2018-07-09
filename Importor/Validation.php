<?php
	require_once __DIR__ . '/DBSource.php';

	class Validation {

	    protected $dbobj;
	    protected $roundId;

	    public function __construct($roundId) { 
	    	$this->roundId = $roundId;
	    	$this->dbobj = new DBSource();       
	    }

	  	public function valid($jude_email, $fastname, $lastname, $id){

    		$data = $this->dbobj->getAllRow($id);
    		$completeStatus = 2;
    		$vStatus = 3;
    		$eStatus = 4;

    		foreach ($data as $key => $value) {

    			$cols = json_decode($value[1], true);
    			if(!self::fastNameValidaton($cols[$fastname])){
    				self::errorChangeStatus($value[0], $vStatus);
    				
    			}elseif (!self::lastNameValidation($cols[$lastname])) {
    				self::errorChangeStatus($value[0], $vStatus);
    				
    			}elseif (!self::emailValidation($cols[$jude_email])) {
    				self::errorChangeStatus($value[0], $vStatus);

    			}elseif(!self::saveData($cols[$fastname], $cols[$lastname], $cols[$jude_email]) ){
    				self::errorChangeStatus($value[0], $eStatus);

    			}else{
    				self::errorChangeStatus($value[0], $completeStatus);

    			}
    		}
		}
		/*
		*  fastname validaiton
		*/
		public function fastNameValidaton($fastname){
			$email = self::test_input($fastname);
			if (empty($fastname) || filter_var($email, FILTER_VALIDATE_EMAIL)) {
			    return false;
			}
			return true;
		}
		/*
		*  lastname validaiton
		*/
		public function lastNameValidation($lastname){
			$email = self::test_input($lastname);
			if (empty($lastname) || filter_var($email, FILTER_VALIDATE_EMAIL)) {
			    return false;
			}
			return true;
		}
		/*
		*  email format validaiton
		*/
		public function emailValidation($email){
			$email = self::test_input($email);
			if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
				return false;
			}
			return true;
		} 
		/*
		*  check whether the email is already exist in userinfo table
		*/
		public function saveData($fastname, $lastname, $email){
			$id = $this->dbobj->saveData($fastname, $lastname, $email);
			$result = self::saveDataToJudging($id);
			return $result;
			//return
			// if ($email != null) {
			// 	//$cols = json_decode($email[], true);
			// 	self::checkInJudging($email[0]);
			// }
		}
		/*
		*  trim the input data
		*/
		public function saveDataToJudging($id){
			$data = $this->dbobj->saveDataToJudging($id, $this->roundId);
			return $data;

		}

		/*
		*  trim the input data
		*/
		public function test_input($data) {
		  	$data = trim($data);
		  	$data = stripslashes($data);
		  	$data = htmlspecialchars($data);
		  	return $data;
		}

		/*
		*change status in import_temp table according to validation error
		*/
		public function errorChangeStatus($id, $status){

			$id = $this->dbobj->errorChangeStatus($id, $status);

		}

		/*
		* show error massage
		*/
		public function showError($id){
			$two= $three= $four = [];
			$errorMessage = [];
			$data = $this->dbobj->showError($id);
			foreach ($data as $key => $value) {
				$data = json_decode($value[0], true);
				if ($value[1] == 3) {
					$three[] = $data['row'];
				}elseif ($value[1] == 4) {
					$four[] = $data['row'];
				}else{
					$two[] = $data['row'];
				}
			}
			
			if(!empty($two)){
				$errorMessage['two']= $two;
			}
			if(!empty($three)){
				$errorMessage['three']= $three;
			}
			if (!empty($four)) {
				$errorMessage['four']= $four;
			}
			
			return $errorMessage;			

		}
	   
	}
?>