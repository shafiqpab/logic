<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="search_list_view")
{
	$companyarr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$arr=array (0=>$companyarr);
	echo  create_list_view ( "list_view", "Company Name,Header Image,Body Image,Footer Image", "100,90,80,80,","400","220",0, "select id,company_id,header_location,body_location,footer_location from template_pad  where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "company_id,", $arr , "company_id,header_location,body_location,footer_location", "../general_info/requires/generate_pad_controller", 'setFilterGrid("list_view",-1);' );
}

if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select ID,COMPANY_ID,HEADER_LOCATION,BODY_LOCATION,FOOTER_LOCATION from template_pad where id='$data'");
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('cbo_company_id').value = '".($inf[csf("COMPANY_ID")])."';\n";    
		echo "$('.upload_header_file').attr('src','../../".($inf["HEADER_LOCATION"])."');\n";
		$url = '../../'.$inf["BODY_LOCATION"];
		echo "$('.upload_body_file').css({'background-image':'url(".$url.")','background-repeat': 'no-repeat','background-position': 'center 0%'});\n";
		echo "$('.upload_footer_file').attr('src','../../".($inf["FOOTER_LOCATION"])."');\n";
		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_pad_info',1);\n";  
	}
}

if($action=='save_update_delete'){
	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	if($_FILES['header_img']['tmp_name']){
		$hedername = "file_upload/".time().$_FILES['header_img']['name'];
		if(move_uploaded_file($_FILES['header_img']['tmp_name'],"../../../".$hedername))
		{ 
			$header_img = 1;  
		}
	}

	if($_FILES['body_img']['tmp_name']){
		$bodyname = "file_upload/".time().$_FILES['body_img']['name'];
		if(move_uploaded_file($_FILES['body_img']['tmp_name'], "../../../".$bodyname))
		{ 
			$body_img = 1; 
		}
	}

	if($_FILES['footer_img']['tmp_name']){
		$footername = "file_upload/".time().$_FILES['footer_img']['name']; 
		if(move_uploaded_file($_FILES['footer_img']['tmp_name'],"../../../".$footername))
		{ 
			$footer_img = 1; 
		}
	}
	


	if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();

		$id=return_next_id( "id","TEMPLATE_PAD", 1 ) ;
		$field_array="ID,COMPANY_ID,INSERTED_BY,INSERT_DATE,STATUS_ACTIVE,IS_DELETED,HEADER_LOCATION,BODY_LOCATION,FOOTER_LOCATION";

		$data_array="(".$id.",".$cbo_company_id.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0,'".$hedername."','".$bodyname."','".$footername."')"; 
		$rID=sql_insert("TEMPLATE_PAD",$field_array,$data_array,1);

		//echo "insert into TEMPLATE_PAD ($field_array) values $data_array";die;
		if($rID )
		{
			oci_commit($con);  
			echo "0**".$id;
		}
		else
		{
			oci_rollback($con);
			echo "10**".$id;
		}
	
		disconnect($con);
		die;
	}
	else if($operation==1)
	{
		$con = connect();
		if($header_img==1){$fieldStr.="*HEADER_LOCATION";$dataStr.="*'".$hedername."'";}
		if($body_img==1){$fieldStr.="*BODY_LOCATION";$dataStr.="*'".$bodyname."'";}
		if($footer_img==1){$fieldStr.="*FOOTER_LOCATION";$dataStr.="*'".$footername."'";}
		
		$field_array="UPDATED_BY*UPDATE_DATE".$fieldStr;
		$data_array=$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'".$dataStr;

		$rID=sql_update("TEMPLATE_PAD",$field_array,$data_array,"id",$update_id,1);

		// echo $rID;oci_rollback($con);die;

		      		
		if($rID )
		{
			oci_commit($con);   
			echo "1**".$update_id;
		}
		else{
			oci_rollback($con);
			echo "10**".$update_id;
		}

		disconnect($con);
		die;
	}else if ($operation == 2) {   // delete Here
		$con = connect();
        $field_array = "updated_by*update_date*status_active*is_deleted";
        $data_array = "" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*'0'*'1'";
        $rID = sql_delete("TEMPLATE_PAD", $field_array, $data_array, "id", "" . $update_id . "", 1);
		//echo 20** $rID;die();
		if ($rID) {
			oci_commit($con);
			echo "2**" . str_replace("'", "", $update_id);
			
		} else {
			oci_rollback($con);
			echo "10**" . str_replace("'", "", $update_id);
		}
	

        disconnect($con);
    }
}







?>