<?
extract($_REQUEST);

if(@$action=='file_upload'){
include('../includes/common.php');
	
	$countfiles = count($_FILES['files']['name']);
	$upload_location = "upload/";
	//$file_path;
	$files_arr = array();
	
	for($index = 0;$index < $countfiles;$index++){
		$filename = $_FILES['files']['name'][$index];
		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		$path = time().'_'.$index.'.'.$ext;
		 if(move_uploaded_file($_FILES['files']['tmp_name'][$index],$upload_location.$path)){
			$files_arr[] = array(
				PATH=>$path,
				EXTENSION=>$ext
			);
		 }
	}



	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	$id=return_next_id( "id","COMMON_PHOTO_LIBRARY", 1 ) ;
		
	foreach($files_arr as $rows){
		if($rows[EXTENSION]=='docx' || $rows[EXTENSION]=='doc' || $rows[EXTENSION]=='pdf' || $rows[EXTENSION]=='txt' || $rows[EXTENSION]=='xlsx' || $rows[EXTENSION]=='html'){$type=2;}
		else{$type=1;}
		
		if ($data_array!='') $data_array .=",";
		$data_array .="(".$id.",".$mst_id.",'".$form_name."','".$upload_location.$rows[PATH]."','".$type."','".$rows[PATH]."')";
		$id++;
	}
		
	$field_array="id,master_tble_id,form_name,image_location,file_type,real_file_name";
	$rID=sql_insert("COMMON_PHOTO_LIBRARY",$field_array,$data_array,1);

	if($db_type==0)
	{
		if($rID)
		{
			mysql_query("COMMIT");
			echo "0**".$new_system_id[0]."**".$id_mst;
		}
		else
		{
			mysql_query("ROLLBACK");
			echo "10**".$id_mst;
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($rID)
		{
			oci_commit($con);
			echo "0**".$new_system_id[0]."**".$id_mst;
		}
		else
		{
			oci_rollback($con);
			echo "10**".$id_mst;
		}
	}
	disconnect($con);
	die;

}


?>
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head> 

<script type="text/javascript"> 

	/*if(document.getElementById('sample_mst_file').files.length==0 && $('#update_id').val()==''){
		alert("Please Add File in Master Part");return;	
	}*/



function uploadFiles(file_id,mst_id,form_name,file_path){
	//$(document).ready(function(){ 
		
		var totalfiles = document.getElementById(file_id).files.length;
		if(totalfiles>0){
			
			var fd = new FormData(); 
			for (var index = 0; index < totalfiles; index++) {
			  fd.append("files[]", document.getElementById(file_id).files[index]);
			}
			
			$.ajax({ 
				url: 'fileUpload.php?action=file_upload&mst_id='+ mst_id+'&form_name='+ form_name+'&file_path='+ file_path, 
				type: 'post', 
				data: fd, 
				contentType: false, 
				processData: false, 
				success: function(response){ 
					if(response != 0){ 
						document.getElementById(file_id).value='';
					} 
					else{ 
						alert('file not uploaded'); 
					} 
				}, 
			});
		}//end if;
		
		 
		
		
	//}); 
}
</script>


 

<form method="post" enctype="multipart/form-data">
    File: <input type="file" id="sample_mst_file" name="sample_mst_file[]" multiple class="image_uploader" style="width:150px">
</form>
<a href="javascript:uploadFiles('sample_mst_file',9999999999,'test_form_name','../../',1)">Upload</a>

