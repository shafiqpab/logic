<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
//...............................................
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
	echo load_html_head_contents("Sample inquiry", "../../", 1, 1,$unicode,'','');
		
	$purpose=array(1=>"Presentation",2=>"Buyer Selected",3=>"Unknown and  Adjustment");
	$team_leader=return_library_array( "select id,team_leader_name from lib_marketing_team", "id", "team_leader_name" );
	$dealing_merchant=return_library_array( "select id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name"  );
	$arr=array (3=>$team_leader,4=>$dealing_merchant,5=>$purpose,7=>$yes_no);
			  
	echo  create_list_view ( "list_view","Item Barcode,Item Name,Quantity,Team Leader,Dealing Merchant,Purpose,Gifted To,Returnable,Posiable Return date", "100,80,50,100,100,100,80,80","890","220",0, "select id,item_barcode,issue_date,issue_qty,team_leader,dealing_merchant,issue_purpose,gifted_to,issue_returnable,pos_re_date from sample_issue_mst where is_deleted=0", "js_set_value", "item_barcode", "", 1, "0,0,0,team_leader,dealing_merchant,issue_purpose,0,issue_returnable", $arr , "item_barcode,issue_date,issue_qty,team_leader,dealing_merchant,issue_purpose,gifted_to,issue_returnable,pos_re_date", "requires/sample_issue_return_controller", 'setFilterGrid("list_view",-1);','0,3,1,0,0,0,0,0,3' ) ;
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
		echo "load_drop_down( 'requires/sample_inquiry_controller', '".$inf[csf("team_leader")]."', 'cbo_dealing_merchant', 'div_marchant' );\n"; 
		echo "document.getElementById('cbo_dealing_merchant').value = '".($inf[csf("dealing_merchant")])."';\n";
		echo "document.getElementById('cbo_Purpose').value  		= '".($inf[csf("issue_purpose")])."';\n";
		echo "document.getElementById('txt_gifted_to').value  		= '".($inf[csf("gifted_to")])."';\n";
		echo "document.getElementById('returnable').value  			= '".($inf[csf("issue_returnable")])."';\n";
		echo "document.getElementById('pos_return_date').value  	= '".change_date_format($inf[csf("pos_re_date")])."';\n";
		echo "document.getElementById('update_id').value  			= '".($inf[csf("id")])."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_sample_issue_return',1);\n";  
	}
}



if ($action=="cbo_dealing_merchant")
{
	echo create_drop_down( "cbo_dealing_merchant", 210, "select id,team_member_name from lib_mkt_team_member_info where team_id='$data' and status_active =1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-- Select Team Member --", $selected, "" ,1);
}

if ($action=="sample_issuereturn_action")
{
	echo load_html_head_contents("Sample Issue Return", "../", 1, 1,$unicode,'','');
			  
	echo  create_list_view ( "list_view","Product Id,Bacode,Return Date,Return Qty", "100,100,100","450","150",0, "select id,product_id,barcode,return_date,return_qty from sample_issue_return where  is_deleted=0", "sample_listview_data", "id,barcode", "", 1, "0,0,0", $arr , "product_id,barcode,return_date,return_qty", "requires/sample_issue_return_controller", 'setFilterGrid("list_view",-1);','0,0,3' ) ;
}

if ($action=="sample_return_list")
{
$data_array = sql_select("SELECT a.id,a.item_id,a.category_id,a.construction,a.composition,a.produced_by_id,a.designer,b.balance,b.barcode,b.color_id,b.size_id,b.expected_price,c.id,c.issue_date,c.gifted_to,c.team_leader,c.dealing_merchant,c.issue_purpose,c.issue_returnable,c.pos_re_date from sample_receive_mst a, sample_receive_dtls b,sample_issue_mst c where a.id=b.mst_id and b.barcode=c.item_barcode");
	$nameArray=sql_select( "select id,product_id,barcode,return_date,return_qty from sample_issue_return where  id='$data'" );
	
	
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('system_id').value 			= '".($inf[csf("id")])."';\n";
		echo "document.getElementById('item_barcode').value 		= '".($inf[csf("barcode")])."';\n";    
		echo "document.getElementById('return_date').value  		= '".change_date_format($inf[csf("return_date")])."';\n";
		echo "document.getElementById('return_qty').value  		  	= '".($inf[csf("return_qty")])."';\n";
		echo "document.getElementById('update_id').value  			= '".($inf[csf("id")])."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_sample_issue_return',1);\n";  
	}
	
}



if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{
		
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			//check_table_status( $_SESSION['menu_id'],1);
			$id=return_next_id( "id","sample_issue_return", 1 ) ;
			
			$field_array="id,product_id,barcode,return_date,return_qty,inserted_by,insert_date,status_active,is_deleted";
			$data_array="(".$id.",'".$product_id."',".$item_barcode.",".$return_date.",".$return_qty.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
			$rID=sql_insert("sample_issue_return",$field_array,$data_array,1);
			//echo "insert into lib_yarn_count($field_array)values".$data_array;die;
			if($db_type==0)
			{
				if($rID ){
					mysql_query("COMMIT");  
					echo "0**".$id;
				}
				else{
					mysql_query("ROLLBACK"); 
					echo "10**".$id;
				}
			}
			
			if($db_type==2 || $db_type==1 )
			{
				if($rID )
				{
					oci_commit($con);  
					echo "0**".$rID;
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
	
	else if ($operation==1)   // Update Here
	{
		
		//echo 'ok';die;
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			
			$field_array="return_date*return_qty*updated_by*update_date*status_active*is_deleted";
			$data_array="".$return_date."*".$return_qty."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*1*0";
			
			$rID=sql_update("sample_issue_return",$field_array,$data_array,"id","".$update_id."",1);
			
			if($db_type==0)
			{
				if($rID ){
					mysql_query("COMMIT");  
					echo "1**".$update_id;
				}
				else{
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
			
			disconnect($con);
			die;
		}
		
	}
	
	else if ($operation==2)   // Delete Here
	{
		/*$unique_check1 = is_duplicate_field( "id", "wo_po_yarn_info_details", "yarn_count_id=$update_id and status_active=1" );
		$unique_check2 = is_duplicate_field( "id", "wo_projected_order_child", "yarn_count_id=$update_id and status_active=1" );
		$unique_check3 = is_duplicate_field( "id", "wo_non_order_info_dtls", "Yarn_count_id 	=$update_id and status_active=1" );
		$unique_check4 = is_duplicate_field( "id", "inv_product_info_details", "yarn_count=$update_id and status_active=1" );*/
		
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$field_array="updated_by*update_date*status_active*is_deleted";
	    $data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";
		
		$rID=sql_delete("sample_issue_return",$field_array,$data_array,"id","".$update_id."",1);
		
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
			{
			if($rID )
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
