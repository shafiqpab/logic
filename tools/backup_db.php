
<?
 include('../includes/common.php');

//connect();
// $file_folder = "../Database/SCOTT.dmp";
// $file_folder1 = "../Database/SCOTT.log";
$output = `uname -a`;
if($db_type==2)
{
	foreach (glob("../Database/"."*.dmp") as $filename)
	{			
    	@unlink($filename);
		//echo $filename."<br>";
	}
	
	$DB="LOGIC3RDVERSION";
	$filename = "../Database/".$DB."_" . date("Y-m-d_H-i-s");
	$file_folder = $filename.".dmp";
	//passthru("Exp userid=LOGIC3RDVERSION/LOGIC3RDVERSION@TEST file=".$filename.".dmp");
	passthru("Exp userid=LOGIC3RDVERSION/LOGIC3RDVERSION@192.168.11.72:1521/TEST file=".$filename.".dmp");
 	//passthru("expdp TEST schemas=LOGIC3RDVERSION directory=../Database/ dumpfile=$file_folder logfile=$file_folder1");
	echo "**".$file_folder;
 	die;
}
else
{
	foreach (glob("../Database/"."*.sql") as $filename){			
		@unlink($filename);
	}
	
	foreach (glob("../Database/"."*.zip") as $filename){			
		@unlink($filename);
	}
	
	$DB_SERVER		= "localhost";		// Database Server ID
	$DB_LOGIN		= "root";			// Database UserName
	$DB_PASSWORD	= "";				// Database Password
	$DB				= "logic_hrm";  // Database containing the tables  fariha_250313, 
	$HTTP_HOST		= "localhost";		// HTTP Host
	
	$filename = "../Database/".$DB. "_" . date("Y-m-d_H-i-s") . ".sql";
	$con = mysql_connect( $DB_SERVER, $DB_LOGIN, $DB_PASSWORD );
	$DB22 =  mysql_select_db($DB, $con);
	
	$result = mysql_query("SHOW TABLES "); //  like 'user%'  like 'user%'
	while($row = mysql_fetch_row($result))
	{
		
		$filename = "../Database/".$row[0] . ".sql";
		//echo $filename."<br>";
		if (preg_match("#Linux#i",$output))
		{
			if($row[0]!='activities_history') passthru("mysqldump --opt --host=$DB_SERVER --user=$DB_LOGIN --password=$DB_PASSWORD $DB $row[0] > $filename"); // Linux
			//if($row[0]!='activities_history') passthru("/usr/bin/mysqldump --opt --host=$DB_SERVER --user=$DB_LOGIN --password=$DB_PASSWORD $DB $row[0] > $filename"); // BPKW SERVER

		}
		else
		{
			//passthru("mysqldump --opt --host=$DB_SERVER --user=$DB_LOGIN --password=$DB_PASSWORD $DB $row[0] > $filename");// Windows
			if($row[0]!='activities_history') passthru("c:/mysqldump --opt --host=$DB_SERVER --user=$DB_LOGIN --password=$DB_PASSWORD $DB $row[0] > $filename");// Windows
		}
		passthru("tail -1 $filename");
	}
	// echo $row[0]; die;
	// Creates Zip File Here
	$file_folder = "../Database/total_db_".date("Y-m-d_H-i-s").".zip";
	//$file_folder = "../Database/total_db_".date("Y-m-d_H-i-s").".zip";
	if(extension_loaded('zip'))
	{
		$zip = new ZipArchive();			// Load zip library	
		$filename = str_replace(".sql",".zip", $file_folder );			// Zip name
		if($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE){		// Opening zip file to load files
			$error .=  "* Sorry ZIP creation failed at this time<br/>";
		}
		foreach (glob("../Database/"."*.sql") as $filenames){			
				   $zip->addFile( $filenames);			// Adding files into zip
				}
			$zip->close();
	}
	
	foreach (glob("../Database/"."*.sql") as $filename){			
		@unlink($filename);
	}
	echo "**".$file_folder;
	exit();
	die;

}
// push to download the zip
/*header('Content-type: application/zip');
header("Content-Disposition: attachment; filename=\"".basename($filename)."\";" );
readfile($filename);
echo "Your Database Backup has been Completed Successfully,<br> Please Downlaod and Store the File in a Secure Place.";*/
	/*header("Pragma: public"); // required
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private",false); // required for certain browsers 
header("Content-Type: application/zip");
// change, added quotes to allow spaces in filenames,  
header("Content-Disposition: attachment; filename=\"".basename($file_folder)."\";" );
header("Content-Transfer-Encoding: binary");
header("Content-Length: ".filesize($file_folder));
readfile("$file_folder");
*/	 // push to download the zip
	//header('Content-type: application/zip');
	//header("Content-Disposition: attachment; filename=\"".basename($file_folder)."\";" );
	//readfile($file_folder);

/*
// required for IE, otherwise Content-disposition is ignored
if(ini_get('zlib.output_compression'))
  ini_set('zlib.output_compression', 'on');

// addition by sumon
$file_extension = strtolower(substr(strrchr($filename,"."),1));
 
switch( $file_extension )
{
  case "pdf": $ctype="application/pdf"; break;
  case "exe": $ctype="application/octet-stream"; break;
  case "zip": $ctype="application/zip"; break;
  case "sql": $ctype="application/text"; break;
  case "doc": $ctype="application/msdoc"; break;
  case "xls": $ctype="application/vnd.ms-excel"; break;
  case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
  case "gif": $ctype="image/gif"; break;
  case "png": $ctype="image/png"; break;
  case "jpeg":
  case "jpg": $ctype="image/jpg"; break;
  default: $ctype="application/force-download";
}
header("Pragma: public"); // required
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private",false); // required for certain browsers 
header("Content-Type: $ctype");
// change, added quotes to allow spaces in filenames,  
header("Content-Disposition: attachment; filename=\"".basename($filename)."\";" );
header("Content-Transfer-Encoding: binary");
header("Content-Length: ".filesize($filename));
readfile("$filename");
 
echo "Your Database Backup has been Completed Successfully,<br> Please Downlaod and Store the File in a Secure Place.";

*/
?>
 
    