<?php
include('../includes/common.php');

foreach (glob("../file_upload/"."*.zip") as $filename){			
	@unlink($filename);
}

	$file_folder = "../file_upload/";
 
	$zip = new ZipArchive(); // Load zip library
	$filename = $file_folder."Image". "_" .$DB. "_" . date("Y-m-d").".zip";			// Zip name
	if($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE){	// Opening zip file to load files
		$error .=  "* Sorry ZIP creation failed at this time<br/>";
	}
	
	
	foreach (glob("../file_upload/"."*.*") as $filenames){			
               $zip->addFile($filenames);	// Adding files into zip
			}
		$zip->close();
		
	
	header('Content-type: application/zip');
	header("Content-Disposition: attachment; filename=\"".basename($filename)."\";" );
	readfile($filename);
 

?>