<?
 include('../includes/common.php');

//connect();
// $file_folder = "../Database/SCOTT.dmp";
// $file_folder1 = "../Database/SCOTT.log";


passthru("Exp userid=LOGIC3RDVERSION/LOGIC3RDVERSION@TEST file=..\Database\LOGIC3RDVERSION27122014.dmp");
//passthru("expdp TEST schemas=LOGIC3RDVERSION directory=../Database/ dumpfile=$file_folder logfile=$file_folder1");
die;


foreach (glob("../Database/"."*.sql") as $filename){			
    @unlink($filename);
}

foreach (glob("../Database/"."*.zip") as $filename){			
	@unlink($filename);
}

$DB_SERVER		= "localhost";		// Database Server ID
$DB_LOGIN		= "root";			// Database UserName
$DB_PASSWORD	= "";				// Database Password
$DB				= "logic_erp_3rd_version";  	// Database containing the tables  fariha_250313, 
$HTTP_HOST		= "localhost";		// HTTP Host



$con = mysql_connect( $DB_SERVER, $DB_LOGIN, $DB_PASSWORD );
$DB =  mysql_select_db($DB, $con);

$result = mysql_query('SHOW TABLES');
while($row = mysql_fetch_row($result))
{
	//$tables[] = $row[0];
	$filename = "../Database/".$DB. "_".$row[0]."_" . date("Y-m-d_H-i-s") . ".sql";
	passthru("c:/mysqldump --opt --host=$DB_SERVER --user=$DB_LOGIN --password=$DB_PASSWORD $DB $row[0] > $filename"); // Windows
	//passthru("mysqldump --opt --host=$DB_SERVER --user=$DB_LOGIN --password=$DB_PASSWORD $DB $row[0] > $filename"); // Linux
  	passthru("tail -1 $filename");
}
  //passthru("mysqldump --opt --host=$DB_SERVER --user=$DB_LOGIN --password=$DB_PASSWORD $DB > $filename");
 

 // Creates Zip File Here
 $file_folder = "../Database/";
if(extension_loaded('zip'))
{
	$zip = new ZipArchive();			// Load zip library	
	$filename = str_replace(".sql",".zip", "../Database/".""."Total_DB");			// Zip name
	if($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE){		// Opening zip file to load files
		$error .=  "* Sorry ZIP creation failed at this time<br/>";
	}
	foreach (glob("../Database/"."*.sql") as $filenames){			
               $zip->addFile($file_folder.$filenames);			// Adding files into zip
			}
		$zip->close();
}

 // push to download the zip
header('Content-type: application/zip');
header("Content-Disposition: attachment; filename=\"".basename($filename)."\";" );
readfile($filename);

die;

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
 
    <!doctype html>
<html>
    <head>
        <title>Backup Your Database</title>
        <link href="../css/style_common.css" rel="stylesheet" type="text/css" />
    </head>
    
<!-- Place dumped MySQL data to a textarea box -->
<body>
 <div align="center" style="margin-top:50px">
    <fieldset style="width:300px; height:150px">
		<legend>Database Backup System</legend>
	 <form name="" method="post" action="" onSubmit="">
     <?
	 
				$result = mysql_list_dbs( $link );
				//echo "asd".mysql_num_rows($result); ?>
     	<select name="db" class="combo_boxes" >
        	<?
				while( $row = mysql_fetch_object( $result ) ):
				?>
                <option value=""><? echo $row->Database.TB.NL; ?></option> 
				<?
					//echo TB.'<li>'.$row->Database.'</li>'.NL;
				endwhile;
			?>
        	
        </select>
        <br>
       	Your Database Backup has been Created, Please click bellow Button to download file on your local Drive.
        <br />
        <br>
        <input type="submit" name="Submit" class="formbutton" value=" Download Database ">
        
	</form>
</fieldset>
</div>
</body>
</html>
