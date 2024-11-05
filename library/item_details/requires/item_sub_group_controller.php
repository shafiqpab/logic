<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];


if ($action=="order_popup")																																																					{
	  echo load_html_head_contents("Item Group popup", "../../../", 1, 1,'','1','');
	  extract($_REQUEST);
	?>
	  <script>
	  function js_set_value(id)
	  {
		  document.getElementById('item_id').value=id;
		  parent.emailwindow.hide();
	  }
	  </script>
	  </head>
	  <body>
		<div align="center" style="width:930px" >
		<fieldset style="width:930px">
			<form name="order_popup_1"  id="order_popup_1">
				<?
				if ($category!=0) $item_category_list=" and item_category='$category'"; else { echo "Please Select Item Category."; die; }
				$sql="select id,item_category,item_group_code,item_name,trim_type,order_uom,trim_uom,conversion_factor,cal_parameter from lib_item_group where is_deleted=0 $item_category_list";
				$arr=array (0=>$item_category,3=>$trim_type,4=>$unit_of_measurement,5=>$unit_of_measurement,7=>$cal_parameter);
				echo  create_list_view ( "list_view", "Item Catagory,Group Code,Item Group Name,Item Type,Order UOM,Cons. UOM,Conv. Factor,Cal Parameter", "150,100,200,80,50,50,50","900","320",0, $sql, "js_set_value", "id,item_name,item_group_code", "'load_php_popup_to_form'", 1, "item_category,0,0,trim_type,order_uom,trim_uom,0,cal_parameter", $arr , "item_category,item_group_code,item_name,trim_type,order_uom,trim_uom,conversion_factor,cal_parameter", "item_creation_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,0,0,0,0' );
				?>
			<input type="hidden" id="item_id" />
			</form>
		</fieldset>
		</div>
	  </body>
	  <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	  </html>
	<?
	die; 																																																					}

if ($action=="item_group_list_view")
{
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	$arr=array (0=>$item_category,1=>$item_group_arr,4=>$row_status);
	//if ($category!=0) $item_category_list=" and item_category_id='$category'";
	if($data!=0) $item_category_list=" and item_category_id='$data'"; else { echo "Please Select Item Category."; die; }
	$sql="select id,item_category_id,item_group_id,sub_group_code,sub_group_name,status_active from lib_item_sub_group where is_deleted=0 $item_category_list";
	echo  create_list_view ( "list_view", "Item Catagory,Item Group,Sub Group Code,Sub Group Name,Status", "200,200,100,200","890","320",0, $sql, "get_php_form_data", "id", "'load_php_data_to_form'", 1, "item_category_id,item_group_id,0,0,status_active", $arr , "item_category_id,item_group_id,sub_group_code,sub_group_name,status_active", "../item_details/requires/item_sub_group_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,0' ) ;
				
	//$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
//	$arr=array (0=>$item_category,1=>$item_group_arr,4=>$row_status);
//	if($data!=0) $item_category_list=" and item_category_id='$data'"; else { echo "Please Select Item Category."; die; }
//	$sql="select id,item_category_id,item_group_id,sub_group_code,sub_group_name,status_active from lib_item_sub_group where is_deleted=0 $item_category_list";
//	echo  create_list_view ( "list_view", "Item Catagory,Item Group,Sub Group Code,Sub Group Name,Status", "200,200,100,200","890","320",0, $sql, "get_php_form_data", "id", "'load_php_data_to_form'", 1, "item_category,item_group_id,0,0,status_active", $arr , "item_category_id,item_group_id,sub_group_code,sub_group_name,status_active", "../item_details/requires/item_sub_group_controller", 'setFilterGrid("list_view",-1);','0,0,0,0,0' ) ;
	die;
}


if ($action=="save_update_delete")
{  

	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{
		if(is_duplicate_field( "sub_group_name", "lib_item_sub_group", "item_category_id=$cbo_item_category and item_group_id=$item_group_id and sub_group_name=$txt_subgroup_name and is_deleted=0" ) == 1)
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
			$id=return_next_id( "id", "lib_item_sub_group", 1 ) ;
			$field_array="id, item_category_id, item_group_id, sub_group_code, sub_group_name, status_active,inserted_by,insert_date";
			$data_array="(".$id.",".$cbo_item_category.",".$item_group_id.",".$txt_subgroup_code.",".$txt_subgroup_name.",".$cbo_status.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$rID=sql_insert("lib_item_sub_group",$field_array,$data_array,1);
		   
			if($db_type==0)
			{
				if($rID ){
					mysql_query("COMMIT");  
					echo "0**".str_replace("'","",$cbo_item_category);
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".str_replace("'","",$cbo_item_category);
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
			if($rID )
				{
					oci_commit($con);   
					echo "0**".str_replace("'","",$cbo_item_category);
				}
			else{
					oci_rollback($con);
					echo "10**".str_replace("'","",$cbo_item_category);
				}
			}
			disconnect($con);
			die;
		}
	}
		
	else if ($operation==1)   // Update Here
	{
		$item_sub_group_id=str_replace("'","",$item_sub_group_id);
		if($item_sub_group_id=="" || $item_sub_group_id==0)
		{
			echo "11**0"; die;
		}
		
		if(is_duplicate_field( "sub_group_name", "lib_item_sub_group", "item_category_id=$cbo_item_category and item_group_id=$item_group_id and sub_group_name=$txt_subgroup_name and is_deleted=0 and id<>$item_sub_group_id" ) == 1)
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
			
			$field_array="item_category_id*item_group_id*sub_group_code*sub_group_name*status_active*update_by*update_date";
			$data_array="".$cbo_item_category."*".$item_group_id."*".$txt_subgroup_code."*".$txt_subgroup_name."*".$cbo_status."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			$rID=sql_update("lib_item_sub_group",$field_array,$data_array,"id","$item_sub_group_id",1);
			//echo "10**".$rID;oci_rollback($con);disconnect($con);die;
			if($db_type==0)
			{
				if($rID ){
					mysql_query("COMMIT");  
					echo "1**".str_replace("'","",$cbo_item_category);
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".str_replace("'","",$cbo_item_category);
				}
			}
			if($db_type==2 || $db_type==1 )
			{
			  if($rID )
			    {
					oci_commit($con);   
					echo "1**".str_replace("'","",$cbo_item_category);
				}
				else{
					oci_rollback($con);
					echo "10**".str_replace("'","",$cbo_item_category);
				}
			}
			disconnect($con);
			die;
		}
	}
	else if ($operation==2)   // Delete Here
	{
		/*$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		$price_quat_check = return_field_value( "id", "wo_pri_quo_trim_cost_dtls", "trim_group=$update_id and status_active=1","id");
		$budge_check = return_field_value( "id", "wo_pre_cost_trim_cost_dtls", "trim_group=$update_id and status_active=1","id");
		$product_check = return_field_value( "id", "product_details_master", "item_group_id=$update_id and status_active=1","id");
		if($price_quat_check >0 || $budge_check >0 || $product_check >0)
		{
			echo "5555**".str_replace("'", "", $update_id);disconnect($con);die;
		}
		if(is_duplicate_field( "a.id", "inv_transaction a, product_details_master b", "b.item_group_id=$update_id and a.prod_id=b.id and b.item_category_id=$cbo_item_category and a.status_active=1 and a.is_deleted=0" ) == 1)
			{
				echo "5555**".str_replace("'", "", $update_id);disconnect($con);die;
			}
		
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		
		$rID=sql_delete("lib_item_group",$field_array,$data_array,"id","".$update_id."",1);
		
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
		die;*/
	}	
}

if ($action=="load_php_data_to_form")
{
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	$sql_item_group = "select id,item_category_id,item_group_id,sub_group_code,sub_group_name,status_active from lib_item_sub_group where is_deleted=0 and id='$data'" ;//die;
	$nameArray=sql_select($sql_item_group);
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('cbo_item_category').value = '".($inf[csf("item_category_id")])."';\n";    
		echo "document.getElementById('item_group_id').value  = '".($inf[csf("item_group_id")])."';\n";
		echo "document.getElementById('txt_item_group').value = '".($item_group_arr[$inf[csf("item_group_id")]])."';\n";    
		echo "document.getElementById('cbo_status').value  = '".($inf[csf("status_active")])."';\n";
		echo "document.getElementById('txt_subgroup_code').value = '".($inf[csf("sub_group_code")])."';\n";    
		echo "document.getElementById('txt_subgroup_name').value  = '".($inf[csf("sub_group_name")])."';\n";
		echo "document.getElementById('item_sub_group_id').value = '".($inf[csf("id")])."';\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_item_group',1);\n";
	}
	die;
}

?>