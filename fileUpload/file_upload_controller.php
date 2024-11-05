<?
//flow blew link. First use in woven_gmts/quotation_inquery.php page
//file:///D|/wamp/www/platform-v3.5/order/woven_gmts/quotation_inquery.php

extract($_REQUEST);

if(@$action=='file_upload'){
include('../includes/common.php');
	
	$countfiles = count($_FILES['file']['name']);
	$upload_location = "file_upload/";
	$files_arr = array();
	
	for($index = 0;$index < $countfiles;$index++){
		$filename = $_FILES['file']['name'][$index];
		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		$path = time().'_'.$index.'.'.$ext;
		 if(move_uploaded_file($_FILES['file']['tmp_name'][$index],'../'.$upload_location.$path)){
			$files_arr[] = array(
				PATH=>$path,
				EXTENSION=>$ext,
				FORM_NAME=>$_POST['form_name'][$index]
			);
		 }
	}


	$con = connect();
	$id=return_next_id( "id","COMMON_PHOTO_LIBRARY", 1 ) ;
		
	foreach($files_arr as $rows){
		if($rows[EXTENSION]=='docx' || $rows[EXTENSION]=='doc' || $rows[EXTENSION]=='pdf' || $rows[EXTENSION]=='txt' || $rows[EXTENSION]=='xlsx' || $rows[EXTENSION]=='xls' || $rows[EXTENSION]=='html'){$type=2;}
		else{$type=1;}
		//$mst_id=99999999;
		if ($data_array!='') $data_array .=",";
		$data_array .="(".$id.",".$mst_id.",'".$rows[FORM_NAME]."','".$upload_location.$rows[PATH]."','".$type."','".$rows[PATH]."','".$pc_date_time."')";
		$id++;
	}
		
	$field_array="id,master_tble_id,form_name,image_location,file_type,real_file_name,insert_date";
	$rID=sql_insert("COMMON_PHOTO_LIBRARY",$field_array,$data_array,1);

	if($rID)
	{
		oci_commit($con);
		echo 1;
	}
	else
	{
		oci_rollback($con);
		echo 0;
	}
		
	disconnect($con);
	die;

}

