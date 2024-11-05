<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_store_location", 224, "select location_name,id from lib_location where company_id='$data' and is_deleted=0  and status_active=1  order by location_name",'id,location_name', 1, '--- Select Location ---', 0, ""  );
}

if ($action=="store_location_list_view")
{
		$companyarr=return_library_array( "select id, company_name from lib_company",'id','company_name');
		$arr=array (1=>$companyarr);
		echo  create_list_view ( "list_view", "Store Name,Company Name,Location Name", "120,120,220,","530","220",0, "select id,store_name,company_id,store_location from  lib_store_location where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,company_id,0", $arr , "store_name,company_id,store_location", "../general_info/requires/store_location_controller", 'setFilterGrid("list_view",-1);' ) ; 
}

else if ($action=="load_php_data_to_form")
{
		$nameArray=sql_select( "select id, store_name, company_id, store_location, location_id,status_active, item_category_id, is_textile_store from lib_store_location  where id='$data'" );
		foreach ($nameArray as $inf)
		{
			echo "document.getElementById('txt_store_name').value  = '".($inf[csf("store_name")])."';\n"; 
			echo "load_drop_down( 'requires/store_location_controller', '".($inf[csf("company_id")])."', 'load_drop_down_location', 'location' );\n";

			echo "document.getElementById('cbo_company_name').value = '".($inf[csf("company_id")])."';\n";    
			echo "document.getElementById('cbo_store_location').value  = '".($inf[csf("location_id")])."';\n";  
			echo "document.getElementById('cbo_catagory_item').value  = '".($inf[csf("item_category_id")])."';\n"; 
			echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n"; 
			echo "document.getElementById('cbo_status').value  = '".($inf[csf("status_active")])."';\n"; 
			echo "set_multiselect('cbo_catagory_item','0','1','".$inf[csf("item_category_id")]."','0');\n";
			echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_store_location',1);\n"; 
			if($inf[csf("is_textile_store")]==1){
				echo "$('#txt_is_textile').prop('checked', true);\n"; 
			}else{
				echo "$('#txt_is_textile').prop('checked', false);\n"; 
			}
		}
}

else if ($action=="save_update_delete")
{
	    $process = array( &$_POST );
		extract(check_magic_quote_gpc( $process )); 
	
		if ($operation==0)  // Insert Here
		{
			//echo "10**0".$txt_storeLocationName; die;
			if (is_duplicate_field( "store_name", "lib_store_location", "store_name=$txt_store_name and company_id=$cbo_company_name and is_deleted=0" ) == 1)
			{
				echo "11**0"; die;
			}
			else
			{
				$con = connect();
				if($db_type==0)
				{
					mysql_query("BEGIN");
				}
				//id,store_name,company_name,store_location,inserted_by,insert_date,updated_by,update_date,is_deleted,status_active
				//txt_store_name,cbo_company_name,cbo_store_location,cbo_status 
				$id=return_next_id( "id", "lib_store_location", 1 );
				$field_array="id,store_name,company_id,location_id,store_location,item_category_id,inserted_by,insert_date,is_deleted,status_active,is_textile_store";
				$data_array="(".$id.",".$txt_store_name.",".$cbo_company_name.",".$cbo_store_location.",'".$txt_storeLocationName."',".$cbo_catagory_item.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',0,".$cbo_status.",".$txt_is_textile.")";
				$flag=1;
				$rID=sql_insert("lib_store_location",$field_array,$data_array,0);
				if($rID) $flag=1; else $flag=0;
				//echo $rID; die;
				//==========================================================================	
				$data_array="";
				$catagory_item=explode(',',str_replace("'","",$cbo_catagory_item));
				for($i=0; $i<count($catagory_item); $i++)
				{
					if($id_lib_catagory_item=="") $id_lib_catagory_item=return_next_id( "id", "lib_store_location_category", 1 ); else $id_lib_catagory_item=$id_lib_catagory_item+1;
					if($i==0) $add_comma=""; else $add_comma=",";
					$data_array.="$add_comma(".$id_lib_catagory_item.",".$id.",".$catagory_item[$i].")";
				}
				$field_array="id, store_location_id, category_type";
				
				if($data_array!="")
				{
					$rID2=sql_insert("lib_store_location_category",$field_array,$data_array,1);
					if($flag==1) 
					{
						if($rID2) $flag=1; else $flag=0; 
					} 
				}
		//=================================================================================
				if($db_type==0)
				{
					if($flag==1)
					{
						mysql_query("COMMIT");  
						echo "0**".$rID."**".$id;
					}
					else
					{
						mysql_query("ROLLBACK"); 
						echo "10**".$rID;
					}
				}
				
				if($db_type==2 || $db_type==1 )
				{
					if($flag==1)
					{  
					     oci_commit($con);  
						echo "0**".$rID."**".$id;
					}
					else
					{ 
					    oci_rollback($con);
						echo "10**".$rID;
					}
				}
				disconnect($con);
				die;
			}
		}
		
		if ($operation==1)  // Update Here
		{
			if (is_duplicate_field( "store_name", "lib_store_location", "store_name=$txt_store_name and company_id=$cbo_company_name and id!=$update_id and  is_deleted=0" ) == 1)
			{
				echo "11**0"; die;
			}
			else
			{
				$con = connect();
				if($db_type==0)
				{
					mysql_query("BEGIN");
				}
				
				$field_array="store_name*company_id*location_id*store_location*item_category_id*inserted_by*insert_date*is_deleted*status_active*is_textile_store";
			    $data_array="".$txt_store_name."*".$cbo_company_name."*".$cbo_store_location."*'".$txt_storeLocationName."'*".$cbo_catagory_item."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*".$cbo_status."*".$txt_is_textile;
				$flag==1;
				$rID=sql_update("lib_store_location",$field_array,$data_array,"id","".$update_id."",1);
				
				if($rID) $flag=1; else $flag=0;
				//=======================================================================================================
				
				
				$data_array2="";
				$catagory_item=explode(',',str_replace("'","",$cbo_catagory_item));
				for($i=0; $i<count($catagory_item); $i++)
				{
					if($id_lib_catagory_item=="") $id_lib_catagory_item=return_next_id( "id", "lib_store_location_category", 1 ); else $id_lib_catagory_item=$id_lib_catagory_item+1;
					if($i==0) $add_comma=""; else $add_comma=",";
					$data_array2.="$add_comma(".$id_lib_catagory_item.",".$update_id.",".$catagory_item[$i].")";
				}
				$field_array="id, store_location_id, category_type";
				
					//echo "10**insert into lib_store_location_category (".$field_array.") values ".$data_array2;die;
				
				
				
				
				if($flag==1)
				{
					$rID3=execute_query( "delete from lib_store_location_category where store_location_id=$update_id",0);
				}
				if($data_array2!="")
				{
					$rID2=sql_insert("lib_store_location_category",$field_array,$data_array2,1);
					if($flag==1) 
					{
						if($rID2) $flag=1; else $flag=0; 
					} 
				}
				
				//echo "10** $rID == $rID2";disconnect($con);die;
			//==================================================================================================================
			
				if($db_type==0)
				{
					if($flag==1 )
					{
						mysql_query("COMMIT");  
						echo "1**".$rID;
					}
					else
					{
						mysql_query("ROLLBACK"); 
						echo "10**".$rID;
					}
				}
				if($db_type==2 || $db_type==1 )
				{
				  if($flag==1 )
					{
						oci_commit($con);  
						echo "1**".$rID;
					}
					else
					{
						oci_rollback($con);
						echo "10**".$rID;
					}
					
				}
				disconnect($con);
				die;
			}
		}
		
		if ($operation==2)  // Delete Here
		{
				$con = connect();
				if($db_type==0)
				{
					mysql_query("BEGIN");
				}
				// and company_id=$cbo_company_name  and location_id=$cbo_store_location
				if (is_duplicate_field( "store_id", "inv_transaction", "status_active=1 and is_deleted=0 and store_id=$update_id" ) == 1){
					echo "13**0"; disconnect($con);die;
				}else{
					$field_array="updated_by*update_date*status_active*is_deleted";
					$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
					$rID=sql_update("lib_store_location",$field_array,$data_array,"id","".$update_id."",1);
					if($db_type==0)
					{
						if($rID )
						{
							mysql_query("COMMIT");  
							echo "2**".$rID;
						}
						else
						{
							mysql_query("ROLLBACK"); 
							echo "10**".$rID;
						}
					}
				
					if($db_type==2 || $db_type==1 )
					{	if($rID )
						{
							oci_commit($con);  
							echo "2**".$rID;
						}
						else
						{
							oci_rollback($con);
							echo "10**".$rID;
						}
					}
				}
				disconnect($con);
				die;
		}
}




?>