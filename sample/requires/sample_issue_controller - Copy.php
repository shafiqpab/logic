<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
//...............................................
/*if ($action=="search_list_view")
{
	  echo  create_list_view ( "list_view", "Item Barcode,Issue Date,Quantity,Purpose,Gifted To,Returnable,Return Date", "100,100,100,100,100","600","220",0, "select id,item_barcode,issue_date,issue_qty,issue_purpose,gifted_to,issue_returnable,pos_re_date from sample_issue_mst", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,0,0,0,0,0,0", $arr , "item_barcode,issue_date,issue_qty,issue_purpose,gifted_to,issue_returnable,pos_re_date", "requires/sample_issue_controller", 'setFilterGrid("list_view",-1);','0,3,0,0,0,0,3') ;
}*/

if ($action=="barcode_list_view")
{
	echo load_html_head_contents("Sample List View","../../", 1, 1, $unicode);
?>	
	<script> 
	function js_set_value(data)
	{
		document.getElementById('update_id').value=data;
		parent.emailwindow.hide();
	}
	</script> 
	<input type="hidden" id="update_id"	 value="">
<?	
	echo load_html_head_contents("Sample Issue", "../../", 1, 1,$unicode,'','');
	echo  create_list_view ( "list_view", "Color,Size,Quantity,Expected Price,Amount,Barcode", "100,100,100,100,100","700","220",0,"select id,mst_id,color_id,size_id,quantity,expected_price,amount,barcode from sample_receive_dtls", "js_set_value", "barcode", "'load_php_data'", 1, "0,0,0,0,0,0", $arr , "color_id,size_id,quantity,expected_price,amount,barcode", "requires/sample_issue_controller", 'setFilterGrid("list_view",-1)','0,0,0,0') ;
}


if ($action=="sample_list_view")
{
			$purpose=array(1=>"Presentation",2=>"Buyer Selected",3=>"Unknown and  Adjustment");
			$team_leader=return_library_array( "select id,team_leader_name from lib_marketing_team", "id", "team_leader_name"  );
			$dealing_merchant=return_library_array( "select id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name"  );
			$arr=array (3=>$team_leader,4=>$dealing_merchant,5=>$purpose,7=>$yes_no);
						  
	  		echo  create_list_view ( "list_view","Item Barcode,Item Name,Quantity,Team Leader,Dealing Merchant,Purpose,Gifted To,Returnable,Posiable Return date", "100,100,50,100,100,100,150","880","220",0, "select id,item_barcode,issue_date,issue_qty,team_leader,dealing_merchant,issue_purpose,gifted_to,issue_returnable,pos_re_date from sample_issue_mst where is_deleted=0", "sample_listview_data", "id,item_barcode", "", 1, "0,0,0,team_leader,dealing_merchant,issue_purpose,0,issue_returnable", $arr , "item_barcode,issue_date,issue_qty,team_leader,dealing_merchant,issue_purpose,gifted_to,issue_returnable,pos_re_date", "requires/sample_issue_controller", 'setFilterGrid("list_view",-1);' ) ;
}


if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select id,item_barcode,issue_date,issue_qty,team_leader,dealing_merchant,issue_purpose,gifted_to,issue_returnable,pos_re_date from sample_issue_mst where id='$data'" );
	
	
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('system_id').value 			= '".($inf[csf("id")])."';\n";
		echo "document.getElementById('item_barcode').value 		= '".($inf[csf("item_barcode")])."';\n";    
		echo "document.getElementById('issue_date').value  			= '".change_date_format($inf[csf("issue_date")])."';\n";
		echo "document.getElementById('txtissued_qty').value  		= '".($inf[csf("issue_qty")])."';\n";
		echo "document.getElementById('cbo_team_leader').value  	= '".($inf[csf("team_leader")])."';\n";
		echo "load_drop_down( 'requires/sample_issue_controller', '".$inf[csf("team_leader")]."', 'cbo_dealing_merchant', 'div_marchant' );\n"; 
		echo "document.getElementById('cbo_dealing_merchant').value = '".($inf[csf("dealing_merchant")])."';\n";
		echo "document.getElementById('cbo_Purpose').value  		= '".($inf[csf("issue_purpose")])."';\n";
		echo "document.getElementById('txt_gifted_to').value  		= '".($inf[csf("gifted_to")])."';\n";
		echo "document.getElementById('returnable').value  			= '".($inf[csf("issue_returnable")])."';\n";
		echo "document.getElementById('pos_return_date').value  	= '".change_date_format($inf[csf("pos_re_date")])."';\n";
		echo "document.getElementById('update_id').value  			= '".($inf[csf("id")])."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_sample_issue',1);\n";  
	}
}


if ($action=="cbo_dealing_merchant")
{
	echo create_drop_down( "cbo_dealing_merchant", 210, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" );
}


if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$size_name_array=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
	
	if ($operation==0)  // Insert Here
	{
	
		if (is_duplicate_field( "item_barcode", "sample_issue_mst", "item_barcode=$item_barcode and is_deleted=0" ) == 1)
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
			//check_table_status( $_SESSION['menu_id'],1);
			$id=return_next_id( "id","sample_issue_mst", 1 ) ;
			//$dtls_id=return_next_id( "id","sample_receive_dtls", 1 ) ;
			
			$field_array="id,item_barcode,issue_date,issue_qty,team_leader,dealing_merchant,issue_purpose,gifted_to,issue_returnable,pos_re_date,inserted_by,insert_date,status_active,is_deleted";
			
			$data_array="(".$id.",".$item_barcode.",".$issue_date.",".$txtissued_qty.",".$cbo_team_leader.",".$cbo_dealing_merchant.",".$cbo_Purpose.",".$txt_gifted_to.",".$returnable.",".$pos_return_date.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
		
			$rID=sql_insert("sample_issue_mst",$field_array,$data_array,1);
			//echo "5**".$rID_dtls.'_'.$rID;die;
			
			if($db_type==0)
			{
				if($rID )
				{
				  mysql_query("COMMIT");  
				  echo "0**".$id;
				}
				else
				{
				  mysql_query("ROLLBACK"); 
				  echo "10**".$id;
				}
			}
			
			if($db_type==2 || $db_type==1 )
			{
				if($rID)
				{
				  oci_commit($con);  
				  echo "0**".$id;
				}
				else
				{
				  oci_rollback($con);
				  echo "10**".$id;
				}
			}
			disconnect($con);
			die;
	}
}
	
else if ($operation==1)
{   // Update Here


	  $con = connect();
	  if($db_type==0)
	  {
	  	mysql_query("BEGIN");
	  }
	  
	  $field_array="item_barcode*issue_date*issue_qty*team_leader*dealing_merchant*issue_purpose*gifted_to*issue_returnable*pos_re_date*updated_by*update_date*status_active*is_deleted";
	  $data_array="".$item_barcode."*".$issue_date."*".$txtissued_qty."*".$cbo_team_leader."*".$cbo_dealing_merchant."*".$cbo_Purpose."*".$txt_gifted_to."*".$returnable."*".$pos_return_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";
	  
	  $rID=sql_update("sample_issue_mst",$field_array,$data_array,"id","".$update_id."",1);
	  
	  if($db_type==0)
	  {
	  	if($rID )
	  	{
	  		mysql_query("COMMIT");  
	  		echo "1**".$update_id;
	  	}
	  	else
	  	{
	  		mysql_query("ROLLBACK"); 
	  		echo "10**".$update_id;
	  	}
	  }
	  if($db_type==2 || $db_type==1 )
	  {
	  if($rID )
	  	{
	  		oci_commit($con);   
	  		echo "1**".$update_id;
	  	}
	  	else{
	  		oci_rollback($con);
	  		echo "10**".$update_id;
	  	}
	  }
	  disconnect($con);
	  die;
	  
	  }
		
	else if ($operation==2)   // Delete Here
	{
		$con = connect();
		if($db_type==0)
		{
		  mysql_query("BEGIN");
		}
		
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		$rID=sql_delete("sample_issue_mst",$field_array,$data_array,"id",$update_id,1);
		
		if($db_type==0)
		{
			  if($rID)
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
		{
			  if($rID)
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
		disconnect($con);
		die;
	}
}
