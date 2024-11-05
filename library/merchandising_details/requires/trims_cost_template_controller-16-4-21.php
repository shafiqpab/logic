<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="set_cons_uom")
{
	$cons_uom=return_field_value("trim_uom", "lib_item_group", "id=$data");
	echo create_drop_down( "cbo_cons_uom", 70, $unit_of_measurement,"", "", "",$cons_uom, "",1,"" );
	exit();
}

if ($action=="on_change_data")
{
	$lib_buyer=return_library_array( "select buyer_name,id from lib_buyer", "id", "buyer_name"  );
	$trims_group=return_library_array( "select item_name,id from lib_item_group","id","item_name"  );
	$supplier_name=return_library_array( "select supplier_name,id from  lib_supplier","id","supplier_name"  );
	$yes_no=array(1=>"Yes",2=>"No"); //2= Deleted,3= Locked
	//echo $data;
	$arr=array (0=>$lib_buyer,2=>$trims_group,4=>$unit_of_measurement,9=>$yes_no,10=>$supplier_name);
	
	echo  create_list_view ( "list_view", "Related Buyer,User Code,Trims Group,Item Desc,Cons. UOM,Brand/Sup Ref.,Cons/Dzn Gmts,Parchase Rate,Amount,Approval Required,Supplier", "150,100,120,130,70,100,70,70,100,70,","1150","220",0, "select  b.buyer_id, a.user_code,a.trims_group,a.item_description,a.cons_uom,a.sup_ref,a.cons_dzn_gmts,a.purchase_rate,a.amount,a.apvl_req,a.supplyer,a.id from  lib_trim_costing_temp a,lib_trim_costing_temp_dtls b where a.id=b.lib_trim_costing_temp_id and b.buyer_id in ($data) and a.is_deleted=0 order by a.id", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "buyer_id,0,trims_group,0,cons_uom,0,0,0,0,apvl_req,supplyer", $arr , "buyer_id,user_code,trims_group,item_description,cons_uom,sup_ref,cons_dzn_gmts,purchase_rate,amount,apvl_req,supplyer", "../merchandising_details/requires/trims_cost_template_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,0,0,2,2,2') ;
	exit();
}
    
if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select id,related_buyer,user_code,trims_group,item_description,cons_uom,sup_ref,cons_dzn_gmts,purchase_rate,amount,apvl_req,supplyer,status_active from   lib_trim_costing_temp where id='$data'" );
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('txt_user_code').value  = '".($inf[csf("user_code")])."';\n";
		echo "document.getElementById('cbo_trims_group').value = '".($inf[csf("trims_group")])."';\n"; 
		echo "document.getElementById('txt_desc').value = '".($inf[csf("item_description")])."';\n";    
		echo "document.getElementById('cbo_cons_uom').value  = '".($inf[csf("cons_uom")])."';\n";
		echo "document.getElementById('txt_sub_ref').value  = '".($inf[csf("sup_ref")])."';\n";
		echo "document.getElementById('txt_cons_dzn_gmts').value  = '".($inf[csf("cons_dzn_gmts")])."';\n";
		echo "document.getElementById('txt_purchase_rate').value  = '".($inf[csf("purchase_rate")])."';\n";
		echo "document.getElementById('txt_amount').value = '".($inf[csf("amount")])."';\n";
		echo "document.getElementById('cbo_apvl_req').value = '".($inf[csf("apvl_req")])."';\n";
		echo "document.getElementById('cbo_supplyer').value = '".($inf[csf("supplyer")])."';\n";   
		echo "document.getElementById('cbo_status').value = '".($inf[csf("status_active")])."';\n";    
 
    	echo "document.getElementById('update_id').value = '".($inf[csf("id")])."';\n";    
	    echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_trim_cost_temp',1);\n";  
		echo "set_multiselect('cbo_rel_buyer','0','1','".($inf[csf("related_buyer")])."','0');\n";  
	}
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	if ($operation==0)  // Insert Here
	{
		if (is_duplicate_field( "a.id", "lib_trim_costing_temp a, lib_trim_costing_temp_dtls b", " a.id=b.lib_trim_costing_temp_id and a.trims_group=$cbo_trims_group and a.sup_ref=$txt_sub_ref and b. buyer_id in(".str_replace("'","",$cbo_rel_buyer).") and is_deleted=0" ) == 1)
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
			$id=return_next_id( "id", "lib_trim_costing_temp", 1 ) ;
			$field_array= "id,related_buyer,user_code,trims_group,item_description,cons_uom,sup_ref,cons_dzn_gmts,purchase_rate,amount,apvl_req,supplyer,inserted_by,inserted_date,status_active,is_deleted";
			
			$data_array="(".$id.",".$cbo_rel_buyer.",".$txt_user_code.",".$cbo_trims_group.",".$txt_desc.",".$cbo_cons_uom.",".$txt_sub_ref.",".$txt_cons_dzn_gmts.",".$txt_purchase_rate.",".$txt_amount.",".$cbo_apvl_req.",".$cbo_supplyer.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",'0')";
			$rID=sql_insert("lib_trim_costing_temp",$field_array,$data_array,0);
			//Insert Data in  lib_trim_costing_temp_dtls Table----------------------------------------
			$data_array="";
			$buyer_type=explode(',',str_replace("'","",$cbo_rel_buyer));
			$lib_trim_costing_temp_dtls_id=return_next_id( "id", "lib_trim_costing_temp_dtls", 1 );
			for($i=0; $i<count($buyer_type); $i++)
			{
				if($i==0) $add_comma=""; else $add_comma=",";
				$data_array.="$add_comma(".$lib_trim_costing_temp_dtls_id.",".$id.",".$buyer_type[$i].")";
				$lib_trim_costing_temp_dtls_id=$lib_trim_costing_temp_dtls_id+1;
			}
			$field_array="id,lib_trim_costing_temp_id, buyer_id";
			$rID2=sql_insert("lib_trim_costing_temp_dtls",$field_array,$data_array,1);
			
			//echo "shajjad".$rID;die;
		
			//----------------------------------------------------------------------------------
			if($db_type==0)
			{
				if($rID && $rID2 ){
					mysql_query("COMMIT");  
					//echo "0**".$rID;
					echo "0**".str_replace("'",'',$rID)."**".str_replace("'",'',$cbo_rel_buyer);
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$rID;
				}
			}
			
			if($db_type==2 || $db_type==1 )
			{
			 if($rID && $rID2)
			    {
					oci_commit($con);   
					//echo "0**".$rID;
					echo "0**".str_replace("'",'',$rID)."**".str_replace("'",'',$cbo_rel_buyer);
				}
				else{
					oci_rollback($con);
					echo "10**".$rID;
				}
			}
			disconnect($con);
			die;
		}
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array= "related_buyer*user_code*trims_group*item_description*cons_uom*sup_ref*cons_dzn_gmts*purchase_rate*amount*apvl_req*supplyer*updated_by*updated_date*status_active*is_deleted";
		
		$data_array="".$cbo_rel_buyer."*".$txt_user_code."*".$cbo_trims_group."*".$txt_desc."*".$cbo_cons_uom."*".$txt_sub_ref."*".$txt_cons_dzn_gmts."*".$txt_purchase_rate."*".$txt_amount."*".$cbo_apvl_req."*".$cbo_supplyer."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*'0'";
		
		//Insert Data in  lib_trim_costing_temp_dtls Table----------------------------------------
		
		$data_array1="";
		$buyer_type=explode(',',str_replace("'","",$cbo_rel_buyer));
		for($i=0; $i<count($buyer_type); $i++)
		{
			if($lib_trim_costing_temp_dtls_id=="") $lib_trim_costing_temp_dtls_id=return_next_id( "id", "lib_trim_costing_temp_dtls", 1 ); else $lib_trim_costing_temp_dtls_id=$lib_trim_costing_temp_dtls_id+1;
			if($i==0) $add_comma=""; else $add_comma=",";
			$data_array1.="$add_comma(".$lib_trim_costing_temp_dtls_id.",".$update_id.",".$buyer_type[$i].")";
		}
		$rID=sql_update("lib_trim_costing_temp",$field_array,$data_array,"id","".$update_id."",0);
		$rID1=execute_query( "delete from  lib_trim_costing_temp_dtls where  lib_trim_costing_temp_id = $update_id",0);
		$field_array1="id,lib_trim_costing_temp_id, buyer_id";
		$rID2=sql_insert("lib_trim_costing_temp_dtls",$field_array1,$data_array1,1);
	
		//----------------------------------------------------------------------------------
		if($db_type==0)
		{
			 if($rID && $rID1 && $rID2 )
			   {
				mysql_query("COMMIT");  
				//echo "1**".$rID;
				echo "1**".str_replace("'",'',$rID)."**".str_replace("'",'',$cbo_rel_buyer);
			   }
			else{
				mysql_query("ROLLBACK"); 
				echo "10**".$rID;
			}
		}
		if($db_type==2 || $db_type==1 )
		{
			 if($rID && $rID1 && $rID2 )
			{
				oci_commit($con);   
				//echo "1**".$rID;
				echo "1**".str_replace("'",'',$rID)."**".str_replace("'",'',$cbo_rel_buyer);
			}
			else{
				oci_rollback($con);
				echo "10**".$rID;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2) // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$field_array="updated_by*updated_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
		
		$rID=sql_update("lib_trim_costing_temp",$field_array,$data_array,"id","".$update_id."",1);
		
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
		 if($rID )
			{
				oci_commit($con);   
				echo "2**".$rID;
			}
			else{
				oci_rollback($con);
				echo "10**".$rID;
			}
		}
		disconnect($con);
		die;
	}
}
?>