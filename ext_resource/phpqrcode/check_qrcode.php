<?php    

 
    
    //set it to writable location, a place for temp generated PNG files
    $PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'temp'.DIRECTORY_SEPARATOR.'barcode'.DIRECTORY_SEPARATOR;
  
    //html PNG location prefix
    $PNG_WEB_DIR = 'temp/';

    include "qrlib.php";    
    
    //ofcourse we need rights to create temp dir
    if (!file_exists($PNG_TEMP_DIR))
        mkdir($PNG_TEMP_DIR);
    
    
    $filename = $PNG_TEMP_DIR.'test.png';
    
    //processing form input
    //remember to sanitize user input in real-life solution !!!
    $errorCorrectionLevel = 'L';
    $matrixPointSize = 4;

    $filename = $PNG_TEMP_DIR.'test'.md5('fds dsf ds fdsf |'.$errorCorrectionLevel.'|'.$matrixPointSize).'.png';
   // echo $filename;die;
    QRcode::png('fds dsf ds fdsf ', $filename, $errorCorrectionLevel, $matrixPointSize, 2);    
        
    
        
    //display generated file
    echo '<img src="'.$PNG_WEB_DIR.basename($filename).'" />'; 
    