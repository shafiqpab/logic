
<?
 extract($_GET);
 extract($_POST);
 
   
 $image =$_FILES["uploadfile"]["name"];
$uploadedfile = $_FILES['uploadfile']['tmp_name'];
$uploaddir = '../Database/';
		
		$tmp_name = $uploaddir . basename($_FILES['uploadfile']['name']);
		$file=$tmp_name;
		move_uploaded_file($_FILES['uploadfile']['tmp_name'], $file) ; 
  
	// Restore Database Script
	$DB_SERVER="localhost";
	$DB_LOGIN="root";
	$DB_PASSWORD="";
	$DB="test1";
	$filename = $tmp_name;
	
	passthru("c:/mysql --host=$DB_SERVER --user=$DB_LOGIN --password=$DB_PASSWORD $DB < $filename");
	 
	//passthru("tail -1 $filename");
	foreach (glob("../Database/"."*.sql") as $filename){			
		@unlink($filename);
	}
	 	echo "Database Upload and Restore Completed Successfully.";
 		die;
  
 
?>
 
   