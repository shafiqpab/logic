<?
header('Content-type:text/html; charset=utf-8');

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_supplier")
{
	if($data==1){
		echo create_drop_down( "cbo_supplier", 167, "select id,company_name from lib_company where status_active =1 and is_deleted=0 $company_credential_cond order by company_name","id,company_name", 1, "-- Select --", 0, "","" );
	}
	else{
		echo create_drop_down( "cbo_supplier", 167, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and b.party_type=26 order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "","" );
	}
}



if ($action=="search_list_view")
{
 	 $supplier_arr=return_library_array( "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b 
					  where a.id=b.supplier_id and b.party_type=26 order by a.supplier_name", "id", "supplier_name"  );
	  $arr=array (0=>$testing_category,1=>$test_for,7=>$currency,8=>$supplier_arr);
	  echo  create_list_view ( "list_view", "Test Category,Test For, Test Item,Rate,Upcharge %,Upcharge Amount,Net Rate,Currency,Testing Company", "130,80,130,60,60,70,70,60,100","880","220",1, "
SELECT id,test_category,test_for,test_item,rate,upcharge_parcengate,upcharge_amount,net_rate,currency_id,testing_company FROM lib_lab_test_rate_chart WHERE status_active =1 AND is_deleted =0", "get_php_form_data", "id", "'load_php_data_to_form'", 1,"test_category,test_for,0,0,0,0,0,currency_id,testing_company", $arr, "test_category,test_for,test_item,rate,upcharge_parcengate,upcharge_amount,net_rate,currency_id,testing_company", "../merchandising_details/requires/lab_test_rate_chart_controller", 'setFilterGrid("list_view",-1);','0,0,0,2,2,2,2,0,0');
                    

}

if ($action=="load_php_data_to_form")
{
	
	$nameArray=sql_select( "SELECT id,test_category,uom_id,test_for,test_item,rate,upcharge_parcengate,upcharge_amount,net_rate,currency_id,testing_company,within_group FROM lib_lab_test_rate_chart WHERE status_active =1 and id='$data' AND is_deleted =0" );
	
	foreach ($nameArray as $inf)
	{
		echo "load_drop_down( 'requires/lab_test_rate_chart_controller', ".($inf[csf("within_group")]*1).", 'load_drop_down_supplier', 'supplir_td' );\n";
		echo "document.getElementById('cbo_within_group').value = ".($inf[csf("within_group")]*1).";\n";    
		
		echo "document.getElementById('cbo_test_category').value = ".($inf[csf("test_category")]).";\n";    
		echo "document.getElementById('cbo_test_for').value  = ".($inf[csf("test_for")]).";\n"; 
		echo "document.getElementById('txt_test_item').value  = '".($inf[csf("test_item")])."';\n";
		echo "document.getElementById('update_id').value  = ".($inf[csf("id")]).";\n"; 
		echo "document.getElementById('txt_rate').value = '".(number_format($inf[csf("rate")],2))."';\n";    
		echo "document.getElementById('txt_upcharge_per').value  = '".(number_format($inf[csf("upcharge_parcengate")],2))."';\n"; 
		echo "document.getElementById('txt_upcharge_amount').value  = '".(number_format($inf[csf("upcharge_amount")],2))."';\n";
		echo "document.getElementById('txt_net_rate').value  = '".(number_format($inf[csf("net_rate")],2))."';\n";
		echo "document.getElementById('cbo_currency').value = ".($inf[csf("currency_id")]).";\n";    
		echo "document.getElementById('cbo_supplier').value  = ".($inf[csf("testing_company")]).";\n";
		echo "document.getElementById('cbo_uom_id').value  = ".($inf[csf("uom_id")]).";\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_testing_chart',1);\n";  
	}
}

if ($action=="save_update_delete")
{
	
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	if ($operation==0)  // Insert Here
	{
		/*if (is_duplicate_field( "sample_name", "lib_sample", "sample_name=$txt_sample_name and is_deleted=0" ) == 1)
		{
			echo "11**0"; die;
		}*/
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$id=return_next_id( "id", " lib_lab_test_rate_chart", 1 ) ;
		$field_array="id,test_category,test_for,test_item,rate,upcharge_parcengate,upcharge_amount,net_rate,currency_id,testing_company,uom_id,within_group,inserted_by,
		insert_date,status_active,is_deleted";
		
		$data_array="(".$id.",".$cbo_test_category.",".$cbo_test_for.",".$txt_test_item.",".$txt_rate.",".$txt_upcharge_per.",".$txt_upcharge_amount.",".$txt_net_rate.",".$cbo_currency.",".$cbo_supplier.",".$cbo_uom_id.",".$cbo_within_group.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		//echo "insert into lib_lab_test_rate_chart($field_array)values".$data_array;die;
		$rID=sql_insert(" lib_lab_test_rate_chart",$field_array,$data_array,1);
		
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
		if($rID )
			{
				oci_commit($con);   
				echo "0**".$rID;
			}
			else{
				oci_rollback($con);
				echo "10**".$rID;
			}
		}
		disconnect($con);
		die;
	}
	
	else if ($operation==1)   // Update Here
	{
	
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array="test_category*test_for*test_item*rate*upcharge_parcengate*upcharge_amount*net_rate*currency_id*testing_company*uom_id*within_group*updated_by*
		update_date";
		$data_array="".$cbo_test_category."*".$cbo_test_for."*".$txt_test_item."*".$txt_rate."*".$txt_upcharge_per."*".$txt_upcharge_amount."*".$txt_net_rate."*".$cbo_currency."*".$cbo_supplier."*".$cbo_uom_id."*".$cbo_within_group."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID=sql_update("lib_lab_test_rate_chart",$field_array,$data_array,"id","".$update_id."",1);
		
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
		 if($rID )
			{
				oci_commit($con);   
				echo "1**".$rID;
			}
		else{
				oci_rollback($con);
				echo "10**".$rID;
			}
		}
		
		disconnect($con);
		die;
	}
	
	else if ($operation==2)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array="updated_by*update_date*status_active*is_deleted";
	    $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		
		$rID=sql_delete("lib_lab_test_rate_chart",$field_array,$data_array,"id","".$update_id."",1);
		
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