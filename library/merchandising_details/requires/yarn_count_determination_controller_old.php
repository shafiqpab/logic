<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$permission=$_SESSION['page_permission'];

if ($action=="search_list_view")
{
	$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count"  );
    $arr=array (0=>$item_category,2=>$composition, 4=>$composition, 7=>$color_range,8=>$lib_yarn_count,9=>$yarn_type, 10=>$lib_yarn_count,11=>$yarn_type,13=>$row_status);
    echo  create_list_view ( "list_view", "Fab Nature, Construction,Comp-1,%,Comp-1,%,GSM/Weight,Color Range,Cotton Count,Cotton Type,Denier Count,Denier Type,Stich Length,Status", "100,100,100,50,90,50,80,100,70,100,70,100,75,95","1230","350",0, "select fab_nature_id,construction,copm_one_id,percent_one,copm_two_id, percent_two,gsm_weight,color_range_id,cotton_count_id,cotton_type_id,denier_count_id,denier_type_id,stich_length,status_active,id from  lib_yarn_count_determination  where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'",1, "fab_nature_id,0,copm_one_id,0,copm_two_id,0,0,color_range_id,cotton_count_id,cotton_type_id,denier_count_id,denier_type_id,0,status_active", $arr , "fab_nature_id,construction,copm_one_id,percent_one,copm_two_id,percent_two,gsm_weight,color_range_id,cotton_count_id,cotton_type_id,denier_count_id,denier_type_id,stich_length,status_active", "../merchandising_details/requires/yarn_count_determination_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,0,0,0,0,0,0,0,0,0,0') ;
}
    
if ($action=="load_php_data_to_form")

{
	$nameArray=sql_select( "select fab_nature_id,construction,copm_one_id,percent_one,copm_two_id, percent_two,gsm_weight,color_range_id,cotton_count_id,cotton_type_id,denier_count_id,denier_type_id,stich_length,status_active,id from  lib_yarn_count_determination where id='$data'" );
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('cbofabricnature').value  = '".($inf[csf("fab_nature_id")])."';\n";
		echo "document.getElementById('txtconstruction').value = '".($inf[csf("construction")])."';\n";    
		echo "document.getElementById('cbocompone').value  = '".($inf[csf("copm_one_id")])."';\n";
		echo "document.getElementById('percentone').value  = '".($inf[csf("percent_one")])."';\n";
		echo "document.getElementById('cbocomptwo').value  = '".($inf[csf("copm_two_id")])."';\n";
		echo "document.getElementById('percenttwo').value = '".($inf[csf("percent_two")])."';\n";
		echo "document.getElementById('txtgsmweight').value = '".($inf[csf("gsm_weight")])."';\n";
		echo "document.getElementById('cbocolortype').value = '".($inf[csf("color_range_id")])."';\n";   
		echo "document.getElementById('cbocountcotton').value = '".($inf[csf("cotton_count_id")])."';\n";   
		
		echo "document.getElementById('cbotypecotton').value  = '".($inf[csf("cotton_type_id")])."';\n";
		echo "document.getElementById('cbocountdenier').value = '".($inf[csf("denier_count_id")])."';\n";    
		echo "document.getElementById('cbotypedenier').value  = '".($inf[csf("denier_type_id")])."';\n";
		echo "document.getElementById('stichlength').value  = '".($inf[csf("stich_length")])."';\n";
		echo "document.getElementById('cbostatus').value  = '".($inf[csf("status_active")])."';\n";
		echo "document.getElementById('updateid').value = '".($inf[csf("id")])."';\n";
	    echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_yarn_count_determination',1);\n";  
	}
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	if ($operation==0)  // Insert Here
	{
		/*if (is_duplicate_field( "a.id", "lib_trim_costing_temp a, lib_trim_costing_temp_dtls b", " a.id=b.lib_trim_costing_temp_id and a.trims_group=$cbo_trims_group and b. buyer_id in(".str_replace("'","",$cbo_rel_buyer).") and is_deleted=0" ) == 1)
		{
			echo "11**0"; die;
		}*/
		
		
		//else
		//{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$id=return_next_id( "id", "lib_yarn_count_determination", 1 ) ;
			$field_array= "id,fab_nature_id, construction, copm_one_id, percent_one, copm_two_id, percent_two, gsm_weight, color_range_id,	cotton_count_id,cotton_type_id,	denier_count_id,denier_type_id,	stich_length,inserted_by,insert_date,status_active,is_deleted";
			$data_array="(".$id.",".$cbofabricnature.",".$txtconstruction.",".$cbocompone.",".$percentone.",".$cbocomptwo.",".$percenttwo.",".$txtgsmweight.",".$cbocolortype.",".$cbocountcotton.",".$cbotypecotton.",".$cbocountdenier.",".$cbotypedenier.",".$stichlength.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbostatus.",'0')";
			$rID=sql_insert("lib_yarn_count_determination",$field_array,$data_array,1);
			//echo $rID; die;
			
			
			//Insert Data in  lib_trim_costing_temp_dtls Table----------------------------------------
			/*$data_array="";
			$buyer_type=explode(',',str_replace("'","",$cbo_rel_buyer));
			for($i=0; $i<count($buyer_type); $i++)
			{
				if($lib_trim_costing_temp_dtls_id=="") $lib_trim_costing_temp_dtls_id=return_next_id( "id", "lib_trim_costing_temp_dtls", 1 ); else $lib_trim_costing_temp_dtls_id=$lib_trim_costing_temp_dtls_id+1;
				if($i==0) $add_comma=""; else $add_comma=",";
				$data_array.="$add_comma(".$lib_trim_costing_temp_dtls_id.",".$id.",".$buyer_type[$i].")";
			}
			$field_array="id,lib_trim_costing_temp_id, buyer_id";
			$rID=sql_insert("lib_trim_costing_temp_dtls",$field_array,$data_array,0);*/
		
			//----------------------------------------------------------------------------------
			if($db_type==0)
			{
				if($rID ){
					mysql_query("COMMIT");  
					echo "0**".$rID;
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID;
				}
			}
			
			if($db_type==2 || $db_type==1 )
			{
				echo '0**0';
			}
			disconnect($con);
			die;
		//}
	}
	
	else if ($operation==1)   // Update Here
	{
		//if (is_duplicate_field( "group_name", "lib_group", "group_name=$txt_group_name and id!=$update_id and is_deleted=0" ) == 1)
		//{
			//echo "11**0"; die;
		//}
		//else
		//{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$field_array= "fab_nature_id*construction*copm_one_id*percent_one*copm_two_id*percent_two*gsm_weight*color_range_id*cotton_count_id*cotton_type_id*denier_count_id*denier_type_id*stich_length*updated_by*update_date*status_active*is_deleted";
			$data_array="".$cbofabricnature."*".$txtconstruction."*".$cbocompone."*".$percentone."*".$cbocomptwo."*".$percenttwo."*".$txtgsmweight."*".$cbocolortype."*".$cbocountcotton."*".$cbotypecotton."*".$cbocountdenier."*".$cbotypedenier."*".$stichlength."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbostatus."*'0'";
			$rID=sql_update("lib_yarn_count_determination",$field_array,$data_array,"id","".$updateid."",1);
			//Insert Data in  lib_trim_costing_temp_dtls Table----------------------------------------
			/*$rID=execute_query( "delete from lib_trim_costing_temp_dtls where  lib_trim_costing_temp_id = $update_id",0);

			$data_array="";
			$buyer_type=explode(',',str_replace("'","",$cbo_rel_buyer));
			for($i=0; $i<count($buyer_type); $i++)
			{
				if($lib_trim_costing_temp_dtls_id=="") $lib_trim_costing_temp_dtls_id=return_next_id( "id", "lib_trim_costing_temp_dtls", 1 ); else $lib_trim_costing_temp_dtls_id=$lib_trim_costing_temp_dtls_id+1;
				if($i==0) $add_comma=""; else $add_comma=",";
				$data_array.="$add_comma(".$lib_trim_costing_temp_dtls_id.",".$update_id.",".$buyer_type[$i].")";
			}
			$field_array="id,lib_trim_costing_temp_id, buyer_id";
			$rID=sql_insert("lib_trim_costing_temp_dtls",$field_array,$data_array,0);*/
		
			//----------------------------------------------------------------------------------
			if($db_type==0)
			{
				if($rID ){
					mysql_query("COMMIT");  
					echo "1**".$rID;
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID;
				}
			}
			if($db_type==2 || $db_type==1 )
			{
			echo "1**".$rID;
			}
			disconnect($con);
			die;
		//}
		
	}
	
	
	
	else if ($operation==2) // Delete Here
	{
		
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$field_array="updated_by*update_date*status_active*is_deleted";
			$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
			
			$rID=sql_delete("lib_yarn_count_determination",$field_array,$data_array,"id","".$updateid."",1);
			
			if($db_type==0)
			{
				if($rID ){
					mysql_query("COMMIT");  
					echo "2**".$rID;
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID;
				}
			}
			if($db_type==2 || $db_type==1 )
			{
			echo "2**".$rID;
			}
			disconnect($con);
			die;
	    }
	}
	

?>