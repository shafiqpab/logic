<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 262, "select location_name,id from lib_location where company_id='$data' and is_deleted=0  and status_active=1  order by location_name",'id,location_name', 1, '--- Select Location ---', 0, "load_drop_down( 'requires/room_setup_controller', this.value, 'load_drop_down_store_by_location', 'store_td' )"  );
}
if ($action=="load_drop_down_store_by_location")
{
	echo create_drop_down( "cbo_store_name", 262, "select store_name,id from lib_store_location where location_id='$data' and is_deleted=0  and status_active=1  order by store_name",'id,store_name', 1, '--- Select Store ---', 0, "load_drop_down( 'requires/room_setup_controller', this.value, 'load_drop_down_floor_by_store', 'floor_td' )"  );
}
if ($action=="load_drop_down_floor_by_store")
{
	echo create_drop_down( "cbo_floor_id", 262, "select a.floor_room_rack_name,a.floor_room_rack_id from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.floor_id and b.store_id='$data' and a.is_deleted=0  and a.status_active=1 group by a.floor_room_rack_name,a.floor_room_rack_id",'floor_room_rack_id,floor_room_rack_name', 1, '--- Select Floor ---', 0, ""  );
}


if ($action=="inventoryRoom_list_view")
{
	$lib_company_arr=return_library_array( "select id,company_name from lib_company where status_active=1 and is_deleted=0", "id","company_name" );
 	$lib_location_arr=return_library_array( "select id,location_name from lib_location where status_active=1 and is_deleted=0", "id","location_name" );
 	$lib_floor_arr=return_library_array( "select floor_id,floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.floor_id and a.status_active=1 and a.is_deleted=0", "floor_id","floor_room_rack_name" ); 
 	$lib_store_arr=return_library_array("select id, store_name from lib_store_location where status_active = 1 and is_deleted = 0", 'id', 'store_name');
    // $arr=array(0=>$lib_company_arr,1=>$lib_location_arr,2=>$lib_floor_arr,4=>$row_status);
    $arr=array(0=>$lib_company_arr,1=>$lib_location_arr,2=>$lib_store_arr,3=>$lib_floor_arr,5=>$row_status); // issue id:7657
     if($db_type==0)
      {
          $sql="select a.company_id,b.location_id,b.floor_id,a.floor_room_rack_name,a.status_active,b.store_id, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.room_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and b.rack_id ='' and b.shelf_id ='' and b.bin_id ='' group by a.company_id,b.location_id,b.floor_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id,b.store_id";
      }
      else
      {
          $sql="select a.company_id,b.location_id,b.floor_id,a.floor_room_rack_name,a.status_active,b.store_id, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.room_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and b.rack_id is null and b.shelf_id is null and b.bin_id is null group by a.company_id,b.location_id,b.floor_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id,b.store_id";
      }
    // echo  create_list_view ( "list_view", "Company Name,Location Name,Floor Name,Room Name,Status", "150,100,100,100,50","600","220",1, $sql, "get_php_form_data", "floor_room_rack_id,floor_room_rack_dtls_id","'load_php_data_to_form'", 1, "company_id,location_id,floor_id,0,status_active", $arr , "company_id,location_id,floor_id,floor_room_rack_name,status_active", "../inventory/requires/room_setup_controller", 'setFilterGrid("list_view",-1);' ) ;
    echo create_list_view ( "list_view", "Company Name,Location Name,Store,Floor Name,Room Name,Status", "150,80,100,80,80","600","220",1, $sql, "get_php_form_data", "floor_room_rack_id,floor_room_rack_dtls_id","'load_php_data_to_form'", 1, "company_id,location_id,store_id,floor_id,0,status_active", $arr, "company_id,location_id,store_id,floor_id,floor_room_rack_name,status_active", "../inventory/requires/room_setup_controller", 'setFilterGrid("list_view",-1);' ) ;  // issue id: 7687
}

if ($action=="load_php_data_to_form")//load list view data to the form
{
	$data = explode("_",$data);
	$nameArray=sql_select( "select a.company_id,b.location_id,a.floor_room_rack_name,a.status_active,b.serial_no, a.floor_room_rack_id,b.store_id,b.floor_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.room_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.floor_room_rack_id='$data[0]' and b.floor_room_rack_dtls_id='$data[1]'" );
	foreach ($nameArray as $inf)
	{
		echo "load_drop_down( 'requires/room_setup_controller', '".($inf[csf("company_id")])."', 'load_drop_down_location', 'location' );\n";
		echo "load_drop_down( 'requires/room_setup_controller', '".($inf[csf("location_id")])."', 'load_drop_down_store_by_location', 'store_td' );\n";
		echo "load_drop_down( 'requires/room_setup_controller', '".($inf[csf("store_id")])."', 'load_drop_down_floor_by_store', 'floor_td' );\n";

		echo "document.getElementById('cbo_store_name').value  = '".($inf[csf("store_id")])."';\n"; 
		echo "document.getElementById('cbo_company_name').value = '".($inf[csf("company_id")])."';\n";    
		echo "document.getElementById('cbo_location_name').value  = '".($inf[csf("location_id")])."';\n"; 
		echo "document.getElementById('txt_room').value  = '".($inf[csf("floor_room_rack_name")])."';\n";
		echo "document.getElementById('cbo_floor_id').value  = '".($inf[csf("floor_id")])."';\n";
		echo "document.getElementById('txt_room_sequence').value  = '".($inf[csf("serial_no")])."';\n";
		echo "document.getElementById('cbo_status').value  = '".($inf[csf("status_active")])."';\n";
		echo "document.getElementById('update_id').value  = '".($inf[csf("floor_room_rack_id")])."';\n"; 
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_inventory_room_info',1);\n";  
	}
	$companyID 		=$nameArray[0][csf('company_id')];
	$locationID 	=$nameArray[0][csf('location_id')];
	$storeID 		=$nameArray[0][csf('store_id')];
	$floorID 		=$nameArray[0][csf('floor_id')];
	$roomID 		=$nameArray[0][csf('floor_room_rack_id')];

	$checkPrevTransFloorArr=sql_select("select store_id as from_store,0 as to_store,floor_id,0 as to_floor_id,room, 0 as to_room,rack,0 as to_rack,self as shelf,0 as to_shelf,bin_box,0 as to_bin_box from inv_transaction where item_category in(2,13) and company_id=$companyID and store_id=$storeID  and floor_id=$floorID and room=$roomID group by  store_id , floor_id, room ,rack, self, bin_box
   union all 
   select b.from_store,b.to_store,b.floor_id,b.to_floor_id,b.room,b.to_room,b.rack,b.to_rack,shelf,to_shelf,b.bin_box,b.to_bin_box from inv_item_transfer_mst a, inv_item_transfer_dtls b where a.id=b.mst_id and b.item_category in(2,13) and (a.company_id=$companyID or a.to_company=$companyID) and (a.location_id=$locationID  or  a.to_location_id=$locationID) and (b.from_store=$storeID or b.to_store=$storeID) and (b.floor_id=$floorID or b.to_floor_id=$floorID) and (b.room=$roomID or b.to_room=$roomID) group by  b.from_store,b.to_store,b.floor_id,b.to_floor_id,b.room,b.to_room,b.rack,b.to_rack,shelf,to_shelf,b.bin_box,b.to_bin_box ");

	if($checkPrevTransFloorArr[0][csf('room')]>0 || $checkPrevTransFloorArr[0][csf('to_room')]>0)
	{
		echo "$('#cbo_company_name').attr('disabled','disabled')" . ";\n";
		echo "$('#cbo_location_name').attr('disabled','disabled')" . ";\n";
		echo "$('#cbo_store_name').attr('disabled','disabled')" . ";\n";
		echo "$('#cbo_floor_id').attr('disabled','disabled')" . ";\n";
		echo "$('#txt_room').attr('disabled','disabled')" . ";\n";
	}
	else
	{
		echo "$('#cbo_company_name').removeAttr('disabled')" . ";\n";
		echo "$('#cbo_location_name').removeAttr('disabled')" . ";\n";
		echo "$('#cbo_store_name').removeAttr('disabled')" . ";\n";
		echo "$('#cbo_floor_id').removeAttr('disabled')" . ";\n";
		echo "$('#txt_room').removeAttr('disabled')" . ";\n";
	}
	
	
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	if ($operation==0)  // Insert Here
	{
		if (is_duplicate_field( "a.floor_room_rack_name", "lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b", "a.company_id=$cbo_company_name and b.location_id=$cbo_location_name and b.store_id=$cbo_store_name and b.floor_id=$cbo_floor_id  and a.floor_room_rack_id=b.room_id and a.floor_room_rack_name=$txt_room and a.is_deleted=0" ) == 1)
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
			$id=return_next_id( "floor_room_rack_id", "lib_floor_room_rack_mst", 1 ) ;

			$field_array="floor_room_rack_id,company_id,floor_room_rack_name,inserted_by,insert_date,status_active,is_deleted";
			$data_array="(".$id.",".$cbo_company_name.",".$txt_room.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",0)";

			$id_dtls=return_next_id( "floor_room_rack_dtls_id", "lib_floor_room_rack_dtls", 1 ) ;
			$field_array_dtls="floor_room_rack_dtls_id,room_id,floor_id,company_id,location_id,store_id,serial_no,inserted_by,insert_date,status_active,is_deleted";
			$data_array_dtls="(".$id_dtls.",".$id.",".$cbo_floor_id.",".$cbo_company_name.",".$cbo_location_name.",".$cbo_store_name.",".$txt_room_sequence.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",0)";

		//echo "insert into lib_floor_room_rack_mst (".$field_array.") values".$data_array; die;


			$rID=sql_insert("lib_floor_room_rack_mst",$field_array,$data_array,1);
			$rID_dtls=sql_insert("lib_floor_room_rack_dtls",$field_array_dtls,$data_array_dtls,1);
			if($db_type==0)
			{
				if($rID && $rID_dtls){
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
				 if($rID && $rID_dtls)
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

		if (is_duplicate_field( "a.floor_room_rack_name", "lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b", "a.company_id=$cbo_company_name and b.location_id=$cbo_location_name and b.store_id=$cbo_store_name and b.floor_id=$cbo_floor_id  and a.floor_room_rack_id=b.room_id and a.floor_room_rack_name=$txt_room and a.floor_room_rack_id!=$update_id and a.is_deleted=0" ) == 1)
		//if (is_duplicate_field( "floor_name", "lib_prod_floor", " floor_name=$cbo_floor_id and company_id=$cbo_company_name and location_id=$cbo_location_name and id!=$update_id and is_deleted=0" ) == 1)
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

			$field_array="company_id*floor_room_rack_name*updated_by*update_date*status_active*is_deleted";
			$data_array="".$cbo_company_name."*".$txt_room."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*0";

			$field_array_dtls="room_id*floor_id*company_id*location_id*store_id*serial_no*updated_by*update_date*status_active*is_deleted";
			$data_array_dtls="".$update_id."*".$cbo_floor_id."*".$cbo_company_name."*".$cbo_location_name."*".$cbo_store_name."*".$txt_room_sequence."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*0";

			//echo "update lib_floor_room_rack_dtls set(".$field_array_dtls.")=".$data_array_dtls; die;

			$rID=sql_update("lib_floor_room_rack_mst",$field_array,$data_array,"floor_room_rack_id","".$update_id."",1);
			$rID_dtls=sql_update("lib_floor_room_rack_dtls",$field_array_dtls,$data_array_dtls,"floor_id","".$update_id."",1);

			if($db_type==0)
			{
				if($rID && $rID_dtls){
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
			 if($rID && $rID_dtls)
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
		
		$rID=sql_delete("lib_floor_room_rack_mst",$field_array,$data_array,"floor_room_rack_id","".$update_id."",1);
		$rID_dtls=sql_delete("lib_floor_room_rack_dtls",$field_array,$data_array,"floor_id","".$update_id."",1);

		
		if($db_type==0)
		{
			if($rID && $rID_dtls){
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
			 if($rID && $rID_dtls)
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