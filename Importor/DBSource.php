<?php

	class DBSource {

		protected $servername;protected $username;protected $password;protected $db;protected $con;

		public function __construct() {        
        	$this->servername = "localhost";
			$this->username = "root";
			$this->password = "";
			$this->db = "import";

			try {				   
					$conn = mysqli_connect($this->servername, $this->username, $this->password, $this->db);
					mysqli_set_charset($conn,"utf8");  
					 
				}
			catch(exception $e)
				{
					echo "Connection failed: " . $e->getMessage();
				}
			
			$this->con = $conn;
    	}

		
		public function insertRecord($data, $extra = array(), $importorInfo){

	    	$sql = "INSERT into import_temps (source, data, extra, import_processor_id, status, type,  ts) 
					    values ('".$importorInfo["source"]."', '".json_encode($data,JSON_UNESCAPED_UNICODE)."', '".json_encode($extra)."', '".$importorInfo["import_processor_id"]."', 1 , '".$importorInfo["type"]."', ".time().")";	

			try{
				$result = mysqli_query($this->con, $sql);
				return true;
			}	
			catch(exception $e)
				{
					echo "Query failed: " . $e->getMessage();
				}					
			
	    }

	    public function getFirstRow($ts){
	    	$sql = "Select data, import_processor_id from import_temps where import_processor_id='$ts' limit 0,1;";	    	
	    	$result = mysqli_query($this->con, $sql);
	    	return mysqli_fetch_row($result);
	    }

	    public function getAllRow($ts){
	    	$sql = "Select id,data from import_temps where import_processor_id='$ts';";
	    	$result = mysqli_query($this->con, $sql);
	    	return mysqli_fetch_all($result);
	    }

	    public function saveData($fastname, $lastname, $email){
	    	$sql = "Select id,email from userinfo where email='$email' limit 0,1";
	    	$result = mysqli_query($this->con, $sql);
	    	$result = mysqli_fetch_row($result);
	    	

	    	if(is_null($result)){
	    		$sql = "Insert into userinfo (firstname, lastname, email) VALUES ('$fastname', '$lastname', '$email')";
	    		if(mysqli_query($this->con, $sql)  == TRUE){
	    			$sql = "Select id from userinfo where email = '$email' limit 0,1";
	    			$result = mysqli_query($this->con, $sql);
	    			return mysqli_fetch_row($result)[0];
	    		}else{
	    			return "Unsuccessful saving data";
	    		}

	    	}else{
	    		return  $result[0];
	    	}

	    }

	    public function saveDataToJudging($userId , $roundId){

	    	$sql = "Select id from judgings where user_id='$userId' and round_id = '$roundId' limit 0,1";
	    	$result = mysqli_query($this->con, $sql);
	    	if(is_null(mysqli_fetch_row($result))){
	    		$sql = "Insert into judgings (user_id, round_id) VALUES ('$userId', '$roundId')";
	    		if(mysqli_query($this->con, $sql)  == TRUE){
	    			return true;
	    		}else{
	    			return "Failed Data Saving";
	    		}
	    	}else {
	    		return false;
	    	} 

	    }

	    public function errorChangeStatus($id, $status){
	    	$sql = "Update import_temps SET status = '$status' where id = '$id'";
	    	if(mysqli_query($this->con, $sql)  == TRUE){
	    		return "Successful Updating Status";
	    	}else{
	    		return "Failed Updating Stuatus";
	    	}

	    }

	    public function showError($id){

	    	$sql = "Select extra, status from import_temps where import_processor_id = '$id'";
	    	$result = mysqli_query($this->con, $sql);
	    	return mysqli_fetch_all($result);

	    }


	}

?>	