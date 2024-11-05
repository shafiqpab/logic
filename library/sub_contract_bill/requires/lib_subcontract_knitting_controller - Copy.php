<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$pc_time= add_time(date("H:i:s",time()),360);  
$pc_date = date("Y-m-d",strtotime(add_time(date("H:i:s",time()),360)));
extract($_REQUEST);

if ($action=="list_container_subcont")
{
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
    $arr=array (0=>$company_arr,2=>$unit_of_measurement,6=>$row_status);
	echo  create_list_view ( "list_view", "Company Name,Fabric Type,Quantity Type,Customer Rate,In-house Rate,Valid Up To,Status", "150,120,60,70,70,100,60","700","220",1, "select id,company_id,knitting_type,quantity_type,customer_rate,in_house_rate,validity,status_active from lib_subcontract_knitting where is_deleted=0", "get_php_form_data", "id","'load_php_data_to_form'", 1, "company_id,0,quantity_type,0,0,0,status_active", $arr , "company_id,knitting_type,quantity_type,customer_rate,in_house_rate,validity,status_active", "../sub_contract_bill/requires/lib_subcontract_knitting_controller", 'setFilterGrid("list_view",-1);','0,0,0,2,2,3,0' ) ;	
	
}

if ($action=="load_php_data_to_form")
{
	//cbo_company_name,text_fabric_type,cbo_uom,text_customer_rate,text_in_house_rate,text_valid_up_to,cbo_status,update_id
		//id,company_id,knitting_type,quantity_type,customer_rate,in_house_rate,validity,inserted_by,insert_date,updated_by,update_date,status_active,is_deleted
	$nameArray=sql_select( "select id,company_id,knitting_type,quantity_type,customer_rate,in_house_rate,validity,status_active from lib_subcontract_knitting where id='$data'" );
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('cbo_company_name').value = '".($inf[csf("company_id")])."';\n";    
		echo "document.getElementById('text_fabric_type').value  = '".($inf[csf("knitting_type")])."';\n"; 
		echo "document.getElementById('cbo_uom').value  = '".($inf[csf("quantity_type")])."';\n";
		echo "document.getElementById('text_customer_rate').value  = '".($inf[csf("customer_rate")])."';\n"; 
		echo "document.getElementById('text_in_house_rate').value  = '".($inf[csf("in_house_rate")])."';\n"; 
		echo "document.getElementById('text_valid_up_to').value  = '".($inf[csf("validity")])."';\n"; 
		echo "document.getElementById('cbo_status').value  = '".($inf[csf("status_active")])."';\n"; 
		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_lib_subcontract_knitting',1);\n";  

	}
}

if ($action=="save_update_delete")
{
	$date= date('Y-m-d');
	if ($operation==0)  // Insert Here
	{
		if (is_duplicate_field( "id", "lib_subcontract_knitting", "company_id=$cbo_company_name and knitting_type=$text_fabric_type and  quantity_type=$cbo_uom and customer_rate=$text_customer_rate and in_house_rate=$text_in_house_rate and validity=$text_valid_up_to and is_deleted=0 and status_active=1" ) == 1)
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
			
			$id=return_next_id( "id", "lib_subcontract_knitting", 1 ) ; 
			$field_array="id,company_id,knitting_type,quantity_type,customer_rate,in_house_rate,validity,inserted_by,insert_date,status_active,is_deleted";
			$data_array="(".$id.",".trim($cbo_company_name).",".trim($text_fabric_type).",".trim($cbo_uom).",".trim($text_customer_rate).",".trim($text_in_house_rate).",".trim($text_valid_up_to).",".$_SESSION['logic_erp']['user_id'].",'".$date."',".trim($cbo_status).",'0')";
			$rID=sql_insert("lib_subcontract_knitting",$field_array,$data_array,1);
			//echo $rID; die;
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
		}
	}
	
	else if ($operation==1)   // Update Here
	{
		if (is_duplicate_field( "id", "lib_subcontract_knitting", "company_id=$cbo_company_name and knitting_type=$text_fabric_type and  quantity_type=$cbo_uom and customer_rate=$text_customer_rate and in_house_rate=$text_in_house_rate and validity=$text_valid_up_to and id!=$update_id and is_deleted=0 and status_active=1" ) == 1)
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
			
			$field_array="company_id*knitting_type*quantity_type*customer_rate*in_house_rate*validity*updated_by*update_date*status_active*is_deleted";
			$data_array="".trim($cbo_company_name)."*".trim($text_fabric_type)."*".trim($cbo_uom)."*".trim($text_customer_rate)."*".trim($text_in_house_rate)."*".trim($text_valid_up_to)."*".$_SESSION['logic_erp']['user_id']."*'".$date."'*".trim($cbo_status)."*0";
			
			$rID=sql_update("lib_subcontract_knitting",$field_array,$data_array,"id","".$update_id."",1);
			//echo $rID; die; 
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
			disconnect($con);
			if($db_type==2 || $db_type==1 )
			{
			echo "1**".$rID;
			}
		}
		
	}
	else if ($operation==2)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$date."'*'0'*'1'";
		
		$rID=sql_delete("lib_subcontract_knitting",$field_array,$data_array,"id","".$update_id."",1);
		
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
		
		disconnect($con);
		if($db_type==2 || $db_type==1 )
		{
	    echo "2**".$rID;
		}
	}
}


?>