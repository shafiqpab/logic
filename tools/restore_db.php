<?
$output = `uname -a`;
if( isset( $_POST["submit"] ) )
{	
	error_reporting(E_ALL);
	ini_set('display_errors', '1');
	
	extract($_REQUEST);
	
	//$serverName="localhost";
	//$userName="root";
	//$password="";
	//$dbName="platform_hams_db";
	
	
	$host=$serverName;
	$userName=$userName;
	$password=$password;
	$dbName=$dbName;
	
	$source = $_FILES['uploadfile']['tmp_name'];
	$targetdir = '../Database/';
	
	//$targetzip ='../Database/'.$_FILES['uploadfile']['name'];//$uploaddir.
	$targetzip ='../Database/'.$_FILES['uploadfile']['name'];
	
	
	//echo $targetzip;
	
	//move_uploaded_file($_FILES['uploadfile']['tmp_name'], $filename) ; 
	foreach (glob("../Database/"."*.zip") as $filename){			
		//@unlink($filename);
	}
	//move_uploaded_file($_FILES["uploadfile"]["tmp_name"], $tmp_name);
	//$filename ='../Database/total_db.zip';
	
	/*if(move_uploaded_file($source, $targetzip)) {
		$zip = new ZipArchive();
		$x = $zip->open($targetzip); // open the zip file to extract
		if ($x === true) {
			$zip->extractTo($targetdir); // place in the directory with same name
			$zip->close();
		
			unlink($targetzip);
		}
	}
	die;*/
 
	if (move_uploaded_file($source, $targetzip)) 
	{
		$conn=mysql_connect($host,$userName,$password);
		if($conn)
		{
			$db=mysql_query("create database IF NOT EXISTS $dbName")or die(mysql_error());
			if(extension_loaded('zip'))
			{	
				$zip = new ZipArchive();
				if($zip->open($targetzip, ZIPARCHIVE::CREATE)===TRUE)
				{
					$zip->extractTo($targetdir);
					$zip->close();
					//echo 'ok';
					
					foreach (glob("../Database/Database/"."*.sql") as $filenames)
					{		
						if (preg_match("#Linux#i",$output))
						{
							passthru("mysql --host=$host --user=$userName --password=$password $dbName < $filenames");
						}
						else
						{
							passthru("d:/mysql --host=$host --user=$userName --password=$password $dbName < $filenames");
						}
						@unlink($filenames);
					}
					echo "Database Upload and Restore Completed Successfully.";
				} 
				else 
				{
					echo 'failed';
					//header('location: restore.php');
				}
			}
		}
		else
		{
			echo "Could not connect to the server";	
			//header('location: restore.php');
		}
	
	}
}
die;
	
	/*// Restore Database Script
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
	die;*/
  
 
?>
