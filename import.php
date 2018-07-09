<?php

	include_once 'Importor\FileImportor.php';
	include_once 'Importor\DBSource.php';
	include_once 'Importor\Reader\FileIterator\CSVFileIterator.php';
	include_once 'Importor\Reader\FileIterator\XLSFileIterator.php';
	include_once 'Importor\Validation.php';
	
	
	// Import data to  Temp table
	if(isset($_FILES["fileToUpload"]["name"])) {	
	
		$target_dir = "D:\wamp\www\import\csv\/";
		$filename = basename($_FILES["fileToUpload"]["name"]);
		
		$target_file = $target_dir . $filename;
		$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
		
		if($imageFileType != "csv" && $imageFileType != "xls" && $imageFileType != "xlsx") {
			print_r(http_response_code(404));die();
		}

		if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {

			$processor_id = uniqid().time(); 
			$importorInfo = array(
				 "filename" => $filename,
				 "source" => strtoupper($imageFileType),
				 "type" => "judge_assignment",
				 "import_processor_id" => $processor_id,

				);

			if($imageFileType == "csv"){
				$obj = new FileImportor(new CSVFileIterator($target_file));
			}else{
				$obj = new FileImportor(new XLSFileIterator($target_file));
			}

        	$obj->setImportorInfo($importorInfo);
        	$obj->read();
       
        	$dbobj = new DBSource();
			$result = $dbobj->getFirstRow($processor_id);

			if(count($result) <=0 ){
			 	echo "No data found."; die();
			}
			$result[0] = json_decode($result[0], true);
			$result = json_encode($result);
			//$id = $result[1];	
			print_r($result);die();
        	// header("Location: http://import.me/import.php?ts=".$processor_id);
			

   		} else {
        	echo "Sorry, there was an error uploading your file.";die();
    	}
    	
	}
	/*
	Get Cols to match column
	if(isset($_GET['ts'])) {
		$dbobj = new DBSource();
		$result = $dbobj->getFirstRow($_GET['ts']);	

		if(count($result) <=0 ){
		 	echo "No data found."; die();
		}
		$result[0] = json_decode($result[0], true);
		$result = json_encode($result);
		//$id = $result[1];	
		print_r($result);die();
				
	}
	*/
	/*
	* Import to real table
	* 	if ( judge_mail in users table) =>
		   - get user_id and save data into judge_group_assignments		   
		else =>
		   - save users table & profiles only with user_id
		   - save data into judge_group_assignments
	*
	* Validations
	* 		- Column (matched with judge_email) must be email
	*/
	if(isset($_POST["judge_email"])) {	
		$oop = new Validation($_POST['judging_roundid']); 
		$oop->valid($_POST['judge_email'],$_POST['firstname'],$_POST['lastname'], $_POST['import_processor_id']);

		$sms = $oop->showError($_POST['import_processor_id']);
		$sms = json_encode($sms);
		print_r($sms);die();

	}
	
	
	
	
?>

<!-- <!DOCTYPE html>
<html>
<body>
<?php if (!isset($_GET['ts'])){ ?>

<form action="import.php" method="post" enctype="multipart/form-data">
    Select file to upload (CSV/XLS,XLSX):
    <input type="file" name="fileToUpload" id="fileToUpload">
    <input type="submit" value="Upload File" name="submit">
</form>

<?php }else{ ?>
	<h3 style="margin: 0px;">Match your columns & import your records</h3>
	<div style="border-bottom: 1px dotted #000;padding-bottom: 10px;">Your file was successfully uploaded and inspected. Now match the columns in your file to the appropriate contacts fields in Judgify.</div>
	<form style="margin-top: 20px;" action="import.php?ts=<?php echo $_GET['ts']; ?>" method="post" enctype="multipart/form-data">    
	    Judge Email :
	    <select id="judge_email" name="judge_email"> 
	    <?php foreach ( $cols as $key=>$val) { ?>		    
		    	<option value="<?php echo $key; ?>"><?php echo $key; ?></option>		   	
		<?php } ?>  
		 </select> 
		 
		<hr/> 
		 First Name :
	    <select id="firstname" name="firstname"> 
	    <?php foreach ( $cols as $key=>$val) { ?>		    
		    	<option value="<?php echo $key; ?>"><?php echo $key; ?></option>		   	
		<?php } ?>  
		 </select>  
		 <hr/> 
		 Last Name :
	    <select id="lastname" name="lastname"> 
	    <?php foreach ( $cols as $key=>$val) { ?>		    
		    	<option value="<?php echo $key; ?>"><?php echo $key; ?></option>		   	
		<?php } ?>  
		 </select>  
		 <hr/> 
		 <input type="hidden" value="<?php echo $id;?>" name="import_processor_id" id="import_processor_id">
		 <input type="hidden" value="22" name="judging_roundid" id="judging_roundid">
	    <input type="submit" value="Import" name="import">
	</form>
<?php } ?>	
</body>
</html> -->