<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 362, "select location_name,id from lib_location where company_id='$data' and is_deleted=0  and status_active=1  order by location_name",'id,location_name', 1, '--- Select Location ---', 0, ""  );
} 

if ($action=="productionfloor_list_view")
{
	$client_arr= return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$arr=array(4=>$client_arr,5=>$production_process,6=>$row_status);
	echo  create_list_view ( "list_view", "Company Name,Location Name,Floor Name,Floor Group,Client,Prod. Process,Status", "150,100,100,100,100,100,50","800","220",1, "select c.company_name,l.location_name,a.client_id,a.floor_name,a.group_name,a.status_active, a.production_process, a.id from  lib_prod_floor a, lib_company c, lib_location l  where a.company_id=c.id and a.location_id=l.id and a.is_deleted=0 order by a.floor_name", "get_php_form_data", "id","'load_php_data_to_form'", 1, "0,0,0,0,client_id,production_process,status_active", $arr , "company_name,location_name,floor_name,group_name,client_id,production_process,status_active", "../production/requires/production_floor_controller", 'setFilterGrid("list_view",-1);' ) ;		
}
function get_company_config($data)
{
	$cbo_client= create_drop_down( "cbo_client", 100, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (7))  order by buyer_name","id,buyer_name", 1, "-- Select Client --", $selected, "" );
	$cbo_location= create_drop_down( "cbo_location_name", 362, "select location_name,id from lib_location where company_id='$data' and is_deleted=0  and status_active=1  order by location_name",'id,location_name', 1, '--- Select Location ---', 0, ""  );
	echo "document.getElementById('party_type_td').innerHTML = '".$cbo_client."';\n";
	echo "document.getElementById('location').innerHTML = '".$cbo_location."';\n";
}
if($action=="get_company_config"){
	$action($data);
}

if ($action=="load_php_data_to_form")//load list view data to the form
{
	//echo "SELECT company_id, location_id,client_id, floor_name,group_name,floor_serial_no, production_process,status_active,id from lib_prod_floor where id='$data'";die;

	$nameArray=sql_select( "select company_id, location_id,client_id, floor_name,group_name,floor_serial_no, production_process,status_active,id from lib_prod_floor where id='$data'" );
	foreach ($nameArray as $inf)
	{
		//echo "load_drop_down( 'requires/production_floor_controller', '".($inf[csf("company_id")])."', 'load_drop_down_location', 'location' );\n";
		echo "get_company_config('".($inf[csf("company_id")])."');\n";
		echo "document.getElementById('cbo_company_name').value = '".($inf[csf("company_id")])."';\n";    
		echo "document.getElementById('cbo_client').value = '".($inf[csf("client_id")])."';\n";    
		echo "document.getElementById('cbo_location_name').value  = '".($inf[csf("location_id")])."';\n"; 
		echo "document.getElementById('txt_floor').value  = '".($inf[csf("floor_name")])."';\n";
		echo "document.getElementById('txt_group_name').value  = '".($inf[csf("group_name")])."';\n";
		echo "document.getElementById('txt_floor_sequence').value  = '".($inf[csf("floor_serial_no")])."';\n";
		echo "document.getElementById('cbo_production_process').value  = '".($inf[csf("production_process")])."';\n";
		echo "document.getElementById('cbo_status').value  = '".($inf[csf("status_active")])."';\n";
		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_product_floor_info',1);\n";  
	}
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$cbo_client=str_replace("'", "", $cbo_client);
	if ($operation==1 || $operation==2) //Floor Check if used it.
	{
		$prod_all=sql_select("select min(po_break_down_id) as po_id,min(production_type) as production_type from  pro_garments_production_mst where company_id=$cbo_company_name and location=$cbo_location_name and floor_id=$update_id  and status_active=1 and is_deleted=0 and production_source=1 ");
		$prod_type_form=$prod_all[0][csf('production_type')];
		$all_prod_type=$production_type[$prod_type_form];
		$dying_prod=sql_select("select min(batch_no) as batch_no,min(entry_form) as entry_form from  pro_fab_subprocess where service_company=$cbo_company_name and floor_id=$update_id  and status_active=1 and is_deleted=0 ");
		$dying_prod_entry=$dying_prod[0][csf('entry_form')];
		$batch_no=$dying_prod[0][csf('batch_no')];
		$all_dying_prod=$entry_form[$dying_prod_entry];
		
		$knit_recv=sql_select("select min(recv_number) as recv_number,min(entry_form) as entry_form from  inv_receive_master where company_id=$cbo_company_name and location_id=$cbo_location_name  and floor=$update_id and status_active=1 and is_deleted=0 and  entry_form in (2,7) and knitting_source=1 ");
		$knit_prod_entry=$knit_recv[0][csf('entry_form')];
		$knit_prod_no=$knit_recv[0][csf('recv_number')];
		$all_knit_prod_name=$entry_form[$knit_prod_entry];
		//subcon_embel_production_mst
		$trims_prod=sql_select("select min(trims_production) as trims_production,min(entry_form) as entry_form from  trims_production_mst where company_id=$cbo_company_name and location_id=$cbo_location_name  and floor=$update_id and status_active=1 and is_deleted=0 ");
		$trims_prod_entry=$trims_prod[0][csf('entry_form')];
		$trims_prod_no=$trims_prod[0][csf('trims_production')];
		$trims_prod_name=$entry_form[$trims_prod_entry];
		
		$wash_prod=sql_select("select min(sys_no) as sys_no,min(entry_form) as entry_form from  subcon_embel_production_mst where company_id=$cbo_company_name and location_id=$cbo_location_name  and floor_id=$update_id and status_active=1 and is_deleted=0 ");
		$wash_prod_entry=$wash_prod[0][csf('entry_form')];
		$wash_prod_no=$wash_prod[0][csf('sys_no')];
		$wash_prod_name=$entry_form[$wash_prod_entry];
		
		$production_msg=="";
		if($prod_type_form || $knit_prod_no || $dying_prod_entry || $trims_prod_no || $wash_prod_no)
		{
			$production_msg=" \n Production: ".$all_prod_type." ";
			if($dying_prod_entry)
			{
			$production_msg.=" \n"."Sub Process: $all_dying_prod  \n BatchNo= ".$batch_no;
			}
			if($knit_prod_no)
			{
			$production_msg.=" \n"."KnitProd: $all_knit_prod_name  \n Mrr No= ".$knit_prod_no;
			}
			if($trims_prod_no)
			{
			$production_msg.=" \n"."TrimsProd: $trims_prod_name  \n Production ID= ".$trims_prod_no;
			}
			if($wash_prod_no)
			{
			$production_msg.=" \n"."WashProd: $trims_prod_name  \n Production ID= ".$wash_prod_no;
			}
			
			echo "50**Some Entries Found For This Floor, Update/Deleting Not Allowed, \n".$production_msg;	
		die;
		}
	}
	if ($operation==0)  // Insert Here
	{
		if (is_duplicate_field( "floor_name", "lib_prod_floor", " floor_name=$txt_floor and company_id=$cbo_company_name and location_id=$cbo_location_name and production_process=$cbo_production_process and is_deleted=0" ) == 1)
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
			$id=return_next_id( "id", "lib_prod_floor", 1 ) ;
			$field_array="id,company_id,location_id,client_id,floor_name,group_name,floor_serial_no,production_process,inserted_by,insert_date,status_active,is_deleted";
			$data_array="(".$id.",".$cbo_company_name.",".$cbo_location_name.",'".$cbo_client."',".$txt_floor.",".$txt_group_name.",".$txt_floor_sequence.",".$cbo_production_process.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",0)";
			$rID=sql_insert("lib_prod_floor",$field_array,$data_array,1);
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
	}
	else if ($operation==1)   // Update Here
	{
		
		if (is_duplicate_field( "floor_name", "lib_prod_floor", " floor_name=$txt_floor and company_id=$cbo_company_name and location_id=$cbo_location_name and production_process=$cbo_production_process and id!=$update_id and is_deleted=0" ) == 1)
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
			$field_array="company_id*location_id*client_id*floor_name*group_name*floor_serial_no*production_process*updated_by*update_date*status_active*is_deleted";
			$data_array="".$cbo_company_name."*".$cbo_location_name."*'".$cbo_client."'*".$txt_floor."*".$txt_group_name."*".$txt_floor_sequence."*".$cbo_production_process."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*0";
			$rID=sql_update("lib_prod_floor",$field_array,$data_array,"id","".$update_id."",1);
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
		
		$rID=sql_delete("lib_prod_floor",$field_array,$data_array,"id","".$update_id."",1);
		
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

if($action=="open_group_name_suggession")
{
	extract($_REQUEST);
	$keywords = "'%$keyword%'";
	$sql = "SELECT group_name FROM lib_prod_floor WHERE group_name LIKE $keywords group by group_name ORDER BY group_name";
	$sql_res = sql_select($sql);
	// print_r($sql_res);
	foreach ($sql_res as $rs) 
	{		
	    ?>
	    <li onclick="set_item('<? echo $rs[csf('group_name')];?>')"><? echo $rs[csf('group_name')];?></li>
	    <?
	}
	?>
	<?
}
?>