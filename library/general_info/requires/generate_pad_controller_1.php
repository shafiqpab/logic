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
 echo  create_list_view ( "list_view", "Company Name,Header Size,Footer Size", "120,120,220,","530","220",0, "select id,company_id,header_size,footer_size from template_pad  where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "company_id,", $arr , "company_id,header_size,footer_size", "../general_info/requires/generate_pad_controller", 'setFilterGrid("list_view",-1);' );
	 
}

if($action=='save_update_delete'){
	
	$hedername = time().$_FILES['header_img']['name'];
    $header_location = "../../../file_upload/".$hedername; 

	$bodyname = time().$_FILES['body_img']['name'];
    $body_location = "../../../file_upload/".$bodyname; 
	$footername = time().$_FILES['footer_img']['name']; 
	$footer_location = "../../../file_upload/".$footername; 
	$uploadOk = 1; 
	
		if(move_uploaded_file($_FILES['header_img']['tmp_name'], $header_location))
		{ 
			echo $header_location; 
		}

        if(move_uploaded_file($_FILES['body_img']['tmp_name'], $body_location))
		{ 
			echo $body_location; 
		}
	
        if(move_uploaded_file($_FILES['footer_img']['tmp_name'], $footer_location))
		{ 
			echo $footer_location; 
		}
	
		$cbo_company_name=$_POST['cbo_company_name'];
		$txt_header_size=$_POST['txt_header_size'];
		$txt_footer_size=$_POST['txt_footer_size'];

  		$con = connect();
  		if($db_type==0)
  		{
  			mysql_query("BEGIN");
  		}

			$id=return_next_id( "id","TEMPLATE_PAD", 1 ) ;
			$field_array="ID,COMPANY_ID,HEADER_SIZE,FOOTER_SIZE,INSERTED_BY,INSERT_DATE,STATUS_ACTIVE,IS_DELETED,HEADER_LOCATION,BODY_LOCATION,FOOTER_LOCATION";

			$data_array="(".$id.",".$cbo_company_name.",".$txt_header_size.",".$txt_footer_size.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0,'file_upload/".$hedername."','file_upload/".$bodyname."','file_upload/".$footername."')"; 

			//.echo "10**insert into TEMPLATE_PAD($field_array)values".$data_array; oci_rollback($con);die;

			$rID=sql_insert("TEMPLATE_PAD",$field_array,$data_array,1);

			// $data_array .="(".$id.",".$mst_id.",'sweater_sample_requisition_1','file_upload/".$filename."','2','".$filename."')";
			check_table_status( $_SESSION['menu_id'],0);
  		if($db_type==0)
  		{
  			if($rID ){
					mysql_query("COMMIT");  
					echo "0**".$rID;
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID."**insert into TEMPLATE_PAD($field_array)values".$data_array;
				}
  		}
  		else if($db_type==2 || $db_type==1 )
  		{
			if($rID )
			{
				oci_commit($con);  
				echo "0**".$rID;
			}
		else
			{
				oci_rollback($con);
				//echo "10**".$rID."**insert into TEMPLATE_PAD($field_array)values".$data_array;
			}
  		}
  		disconnect($con);
  		die;

}

if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select id,company_id,header_size,footer_size,header_location,body_location,footer_location from template_pad where id='$data'" );

    // $sql= "select id,company_id,header_size,footer_size from template_pad where id='$data'";
    // echo $sql;die();

  
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('cbo_company_name').value = '".($inf[csf("COMPANY_ID")])."';\n";    
		echo "document.getElementById('txt_header_size').value  = '".($inf[csf("HEADER_SIZE")])."';\n";
		echo "document.getElementById('txt_footer_size').value  = '".($inf[csf("FOOTER_SIZE")])."';\n";
		echo "document.getElementById('upload_header_file').value  = '".($inf[csf("HEADER_LOCATION")])."';\n";
		echo "document.getElementById('upload_body_file').value  = '".($inf[csf("BODY_LOCATION")])."';\n";
		echo "document.getElementById('upload_footer_file').value  = '".($inf[csf("FOOTER_LOCATION")])."';\n";

		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_pad_info',1);\n";  
	}
}


// if ($action=="hello")
// {
// 	$process = array( &$_POST );
// 	extract(check_magic_quote_gpc( $process )); 
// 	$txt_yarn_color_code=str_replace("'", "", $txt_yarn_color_code);
// 	//echo "11**0 **select yarn_count from lib_yarn_count yarn_count=$txt_yarn_count and sequence_no=$txt_sequence and sequence_no=$txt_sequence and yarn_spinning_system=$cbo_yarn_spinning_system and yarn_finish=$cbo_yarn_finish and yarn_color_code='".$txt_yarn_color_code."' and yarn_type=$cbo_yarn_type and number_of_filament=$cbo_number_of_filament and count_system=$cbo_count_system and yarn_fibre=$cbo_yarn_fibre and yarn_fibre_type=$cbo_yarn_fibre_type and yarn_color=$cbo_yarn_color and is_deleted=0";die;
// 	//echo $_SESSION['menu_id'];die;
// 	if ($operation==0)  // Insert Here
// 	{
// 		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
// 		if (is_duplicate_field( "COMPANY_ID", "TEMPLATE_PAD", "COMPANY_ID=$cbo_company_name and HEADER_SIZE=$txt_header_size and FOOTER_SIZE=$txt_footer_size  and IS_DELETED=0" ) == 1)
// 		{
// 			echo "11**0"; die;
// 		}
// 		else
// 		{
// 			$con = connect();
// 			if($db_type==0)
// 			{
// 				mysql_query("BEGIN");
// 			}
// 			check_table_status( $_SESSION['menu_id'],1);
// 			$id=return_next_id( "ID", "  TEMPLATE_PAD", 0 ) ;

// 			//txt_yarn_count*cbo_status*txt_sequence*update_id*cbo_count_system*cbo_number_of_filament*cbo_yarn_spinning_system

			
// 			$field_array="ID,COMPANY_ID,HEADER_SIZE,FOOTER_SIZE,INSERTED_BY,INSERT_DATE,STATUS_ACTIVE,IS_DELETED";
// 			$data_array="(".$id.",".$cbo_company_name.",".$txt_header_size.",".$txt_footer_size.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)"; 
// 			//echo "insert into lib_yarn_count($field_array)values".$data_array;die;
// 			$rID=sql_insert("TEMPLATE_PAD",$field_array,$data_array,1);
// 			check_table_status( $_SESSION['menu_id'],0);
// 			if($db_type==0)
// 			{
// 				if($rID ){
// 					mysql_query("COMMIT");  
// 					echo "0**".$rID;
// 				}
// 				else{
// 					mysql_query("ROLLBACK"); 
// 					echo "10**".$rID."**insert into TEMPLATE_PAD($field_array)values".$data_array;
// 				}
// 			}
			
// 			if($db_type==2 || $db_type==1 )
// 			{
// 				if($rID )
// 				{
// 					oci_commit($con);  
// 					echo "0**".$rID;
// 				}
// 			else
// 				{
// 					oci_rollback($con);
// 					echo "10**".$rID."**insert into TEMPLATE_PAD($field_array)values".$data_array;
// 				}
// 			}
// 			disconnect($con);
// 			die;
// 		}
// 	}
	
// 	else if ($operation==1)   // Update Here
// 	{
// 		if (is_duplicate_field( "COMPANY_ID", "TEMPLATE_PAD", "COMPANY_ID=$cbo_company_name and ID!=$update_id and IS_DELETED=0" ) == 1)
// 		{
// 			echo "11**0"; die;
// 		}
// 		else
// 		{
// 			$con = connect();
// 			if($db_type==0)
// 			{
// 				mysql_query("BEGIN");
// 			}
// 			//ID,COMPANY_ID,HEADER_SIZE,FOOTER_SIZE,INSERTED_BY,INSERT_DATE,STATUS_ACTIVE,IS_DELETED"
// 			$field_array="COMPANY_ID*HEADER_SIZE*FOOTER_SIZE*UPDATED_BY*UPDATE_DATE*IS_DELETED";
          
// 			$data_array="".$cbo_company_name."*".$txt_header_size."*".$txt_footer_size."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0";
			
// 			$rID=sql_update("TEMPLATE_PAD",$field_array,$data_array,"id","".$update_id."",1);
			
// 			if($db_type==0)
// 			{
// 				if($rID ){
// 					mysql_query("COMMIT");  
// 					echo "1**".$rID;
// 				}
// 				else{
// 					mysql_query("ROLLBACK"); 
// 					echo "10**".$rID;
// 				}
// 			}
// 			if($db_type==2 || $db_type==1 )
// 			{
// 			if($rID )
// 			    {
// 					oci_commit($con);   
// 					echo "1**".$rID;
// 				}
// 				else{
// 					oci_rollback($con);
// 					echo "10**".$rID;
// 				}
// 			}
// 			disconnect($con);
// 			die;
// 		}
		
// 	}
	
// 	else if ($operation==2)   // Update Here
// 	{
// 		/*$unique_check1 = is_duplicate_field( "id", "wo_po_yarn_info_details", "yarn_count_id=$update_id and status_active=1" );
// 		$unique_check2 = is_duplicate_field( "id", "wo_projected_order_child", "yarn_count_id=$update_id and status_active=1" );
// 		$unique_check3 = is_duplicate_field( "id", "wo_non_order_info_dtls", "Yarn_count_id 	=$update_id and status_active=1" );
// 		$unique_check4 = is_duplicate_field( "id", "inv_product_info_details", "yarn_count=$update_id and status_active=1" );*/
		
// 		$con = connect();
// 		if($db_type==0)
// 		{
// 			mysql_query("BEGIN");
// 		}
		
// 		$field_array="updated_by*update_date*status_active*is_deleted";
// 	    $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		
// 		$rID=sql_delete("TEMPLATE_PAD",$field_array,$data_array,"id","".$update_id."",1);
		
// 		if($db_type==0)
// 		{
// 			if($rID ){
// 				mysql_query("COMMIT");  
// 				echo "2**".$rID;
// 			}
// 			else{
// 				mysql_query("ROLLBACK"); 
// 				echo "10**".$rID;
// 			}
// 		}
		
// 		if($db_type==2 || $db_type==1 )
// 			{
// 			if($rID )
// 			    {
// 					oci_commit($con);   
// 					echo "1**".$rID;
// 				}
// 				else{
// 					oci_rollback($con);
// 					echo "10**".$rID;
// 				}
// 			}
// 			disconnect($con);
// 			die;
// 	}
// }


?>