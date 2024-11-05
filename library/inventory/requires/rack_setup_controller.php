<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 262, "select location_name,id from lib_location where company_id='$data' and is_deleted=0  and status_active=1  order by location_name",'id,location_name', 1, '--- Select Location ---', 0, "load_drop_down( 'requires/rack_setup_controller', this.value, 'load_drop_down_store_by_location', 'store_td' )"  );
}
if ($action=="load_drop_down_store_by_location")
{
	echo create_drop_down( "cbo_store_name", 262, "select store_name,id from lib_store_location where location_id='$data' and is_deleted=0  and status_active=1  order by store_name",'id,store_name', 1, '--- Select Store ---', 0, "load_drop_down( 'requires/rack_setup_controller', this.value, 'load_drop_down_floor_by_store', 'floor_td' )"  );
}
if ($action=="load_drop_down_floor_by_store")
{
	echo create_drop_down( "cbo_floor_id", 262, "select a.floor_room_rack_name,a.floor_room_rack_id from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.floor_id and b.store_id='$data' and a.is_deleted=0  and a.status_active=1 group by a.floor_room_rack_name,a.floor_room_rack_id",'floor_room_rack_id,floor_room_rack_name', 1, '--- Select Floor ---', 0, "load_drop_down( 'requires/rack_setup_controller', this.value, 'load_drop_down_room_by_floor', 'room_td' )"  );
}
if ($action=="load_drop_down_room_by_floor")
{
	echo create_drop_down( "cbo_room_id", 262, "select a.floor_room_rack_name,a.floor_room_rack_id from lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.room_id and b.floor_id='$data' and a.is_deleted=0  and a.status_active=1 group by a.floor_room_rack_name,a.floor_room_rack_id",'floor_room_rack_id,floor_room_rack_name', 1, '--- Select Room ---', 0, ""  );
}


if ($action=="inventoryRack_list_view")
{
		$lib_company_arr=return_library_array( "select id,company_name from lib_company where status_active=1 and is_deleted=0", "id","company_name" );
	 	$lib_location_arr=return_library_array( "select id,location_name from lib_location where status_active=1 and is_deleted=0", "id","location_name" );
	 	$lib_floor_arr=return_library_array( "select floor_id,floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.floor_id and a.status_active=1 and a.is_deleted=0", "floor_id","floor_room_rack_name" );

        $lib_room_arr=return_library_array( "select room_id,floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.room_id and a.status_active=1 and a.is_deleted=0", "room_id","floor_room_rack_name" );

        $lib_store_arr=return_library_array("select id, store_name from lib_store_location where status_active = 1 and is_deleted = 0", 'id', 'store_name');

        // $arr=array(0=>$lib_company_arr,1=>$lib_location_arr,2=>$lib_floor_arr,3=>$lib_room_arr,5=>$row_status);
        //$arr=array(0=>$lib_company_arr,1=>$lib_location_arr,2=>$lib_store_arr,3=>$lib_floor_arr,4=>$lib_room_arr,6=>$row_status); // issue id:7658
        if($db_type==0)
         {
             $sql="select a.company_id,b.location_id,b.floor_id,b.room_id,a.floor_room_rack_name,a.status_active, b.store_id a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.rack_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and b.shelf_id ='' and b.bin_id ='' group by a.company_id,b.location_id,b.floor_id,b.room_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id,b.store_id";
         }
         else
         {
            $sql="select a.company_id,b.location_id,b.floor_id,b.room_id,a.floor_room_rack_name,a.status_active,b.store_id, a.floor_room_rack_id,b.floor_room_rack_dtls_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.rack_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and b.shelf_id is null and b.bin_id is null group by a.company_id,b.location_id,b.floor_id,b.room_id,a.floor_room_rack_name,a.status_active, a.floor_room_rack_id,b.floor_room_rack_dtls_id,b.store_id";
         }

        // echo  create_list_view ( "list_view", "Company Name,Location Name,Floor Name,Room Name,Rack Name,Status", "150,100,100,100,100,50","700","220",1, $sql, "get_php_form_data", "floor_room_rack_id,floor_room_rack_dtls_id","'load_php_data_to_form'", 1, "company_id,location_id,floor_id,room_id,0,status_active", $arr , "company_id,location_id,floor_id,room_id,floor_room_rack_name,status_active", "../inventory/requires/rack_setup_controller", 'setFilterGrid("list_view",-1);' ) ;
       /*  echo create_list_view ( "list_view", "Company Name,Location Name,Store,Floor Name,Room Name,Rack Name,Status", "150,80,100,100,60,80,30","700","220",1, $sql, "get_php_form_data", "floor_room_rack_id,floor_room_rack_dtls_id","'load_php_data_to_form'", 1, "company_id,location_id,store_id,floor_id,room_id,0,status_active", $arr, "company_id,location_id,store_id,floor_id,room_id,floor_room_rack_name,status_active", "../inventory/requires/rack_setup_controller", 'setFilterGrid("list_view",-1);' ) ; */

	   $result = sql_select($sql);

	   ?>

		 <style>
			.wrd_brk{word-break: break-all;word-wrap: break-word;}
		</style>
		<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width='850'>
			<thead>
				<tr>
					<th width="50">Check All <input type="checkbox" name="check_all" id="check_all" onClick="check_all_report()"></th>
					<th width="30" class="wrd_brk">SL</th>
					<th width="150" class="wrd_brk">Company Name</th>
					<th width="100" class="wrd_brk">Location Name</th>
					<th width="100" class="wrd_brk">Store Name</th>
					<th width="100" class="wrd_brk">Floor Name</th>
					<th width="100" class="wrd_brk">Room Name</th>
					<th width="100" class="wrd_brk">Rack Name</th>
					<th class="wrd_brk">Status</th>
				</tr>
			</thead>
		</table>
		<div style="width:850px; max-height:220px; overflow-y:scroll" id="scroll_body">
			<table width="830px" cellspacing="0" border="1" class="rpt_table" rules="all" id="tbl_list_search">
				<tbody >
				<?
					$i = 1;
					foreach ($result as $row)
					{

						if ($i % 2 == 0) $bgcolor = "#E9F3FF";
						else $bgcolor = "#FFFFFF";
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer">
							<td width="50" align="center" valign="middle" title="<? echo $row[csf('floor_room_rack_dtls_id')]; ?>">
								<input id="chkRack_<? echo $i; ?>" type="checkbox" name="chkRack">

								<input type="hidden" name="txtFloorRoomRackDtlsId[]" id="txtFloorRoomRackDtlsId_<? echo $i; ?>" value="<? echo $row[csf('floor_room_rack_dtls_id')]; ?>">
							</td>
							<td width="30" class="wrd_brk" onClick='get_php_form_data("<? echo $row[csf("floor_room_rack_id")].'_'.$row[csf("floor_room_rack_dtls_id")]; ?>","load_php_data_to_form","requires/rack_setup_controller")'><?php echo $i; ?></td>
							<td width="150" class="wrd_brk" onClick='get_php_form_data("<? echo $row[csf("floor_room_rack_id")].'_'.$row[csf("floor_room_rack_dtls_id")]; ?>","load_php_data_to_form","requires/rack_setup_controller")'><?php echo $lib_company_arr[$row[csf("company_id")]]; ?></td>
							<td width="100" class="wrd_brk" onClick='get_php_form_data("<? echo $row[csf("floor_room_rack_id")].'_'.$row[csf("floor_room_rack_dtls_id")]; ?>","load_php_data_to_form","requires/rack_setup_controller")'><?php echo $lib_location_arr[$row[csf("location_id")]]; ?></td>
							<td width="100" class="wrd_brk" onClick='get_php_form_data("<? echo $row[csf("floor_room_rack_id")].'_'.$row[csf("floor_room_rack_dtls_id")]; ?>","load_php_data_to_form","requires/rack_setup_controller")'><?php echo $lib_store_arr[$row[csf("store_id")]]; ?></td>
							<td width="100" class="wrd_brk" onClick='get_php_form_data("<? echo $row[csf("floor_room_rack_id")].'_'.$row[csf("floor_room_rack_dtls_id")]; ?>","load_php_data_to_form","requires/rack_setup_controller")'><?php echo $lib_floor_arr[$row[csf("floor_id")]]; ?></td>
							<td width="100" class="wrd_brk" onClick='get_php_form_data("<? echo $row[csf("floor_room_rack_id")].'_'.$row[csf("floor_room_rack_dtls_id")]; ?>","load_php_data_to_form","requires/rack_setup_controller")'><?php echo $lib_room_arr[$row[csf("room_id")]]; ?></td>
							<td width="100" class="wrd_brk" onClick='get_php_form_data("<? echo $row[csf("floor_room_rack_id")].'_'.$row[csf("floor_room_rack_dtls_id")]; ?>","load_php_data_to_form","requires/rack_setup_controller")'><?php echo $row[csf("floor_room_rack_name")]; ?></td>
							<td class="wrd_brk" onClick='get_php_form_data("<? echo $row[csf("floor_room_rack_id")].'_'.$row[csf("floor_room_rack_dtls_id")]; ?>","load_php_data_to_form","requires/rack_setup_controller")'><?php echo $row_status[$row[csf("status_active")]]; ?></td>
						</tr>
						<? $i++;
					} ?>
				</tbody>
			</table>
		</div>
		<?
}

if ($action=="load_php_data_to_form")//load list view data to the form
{
	$data = explode("_",$data);
	$nameArray=sql_select( "select a.company_id,b.location_id,a.floor_room_rack_name,a.status_active,b.serial_no, a.floor_room_rack_id,b.store_id,b.floor_id,b.room_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.rack_id and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and a.floor_room_rack_id='$data[0]' and b.floor_room_rack_dtls_id='$data[1]'" );
	foreach ($nameArray as $inf)
	{
		echo "load_drop_down( 'requires/rack_setup_controller', '".($inf[csf("company_id")])."', 'load_drop_down_location', 'location' );\n";
		echo "load_drop_down( 'requires/rack_setup_controller', '".($inf[csf("location_id")])."', 'load_drop_down_store_by_location', 'store_td' );\n";
		echo "load_drop_down( 'requires/rack_setup_controller', '".($inf[csf("store_id")])."', 'load_drop_down_floor_by_store', 'floor_td' );\n";
		echo "load_drop_down( 'requires/rack_setup_controller', '".($inf[csf("floor_id")])."', 'load_drop_down_room_by_floor', 'room_td' );\n";

		echo "document.getElementById('cbo_company_name').value = '".($inf[csf("company_id")])."';\n";
		echo "document.getElementById('cbo_location_name').value  = '".($inf[csf("location_id")])."';\n";
		echo "document.getElementById('cbo_store_name').value  = '".($inf[csf("store_id")])."';\n";
		echo "document.getElementById('cbo_floor_id').value  = '".($inf[csf("floor_id")])."';\n";
		echo "document.getElementById('cbo_room_id').value  = '".($inf[csf("room_id")])."';\n";
		echo "document.getElementById('txt_rack').value  = '".($inf[csf("floor_room_rack_name")])."';\n";
		echo "document.getElementById('txt_rack_sequence').value  = '".($inf[csf("serial_no")])."';\n";
		echo "document.getElementById('cbo_status').value  = '".($inf[csf("status_active")])."';\n";
		echo "document.getElementById('update_id').value  = '".($inf[csf("floor_room_rack_id")])."';\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_inventory_rack_info',1);\n";
	}
	$companyID 		=$nameArray[0][csf('company_id')];
	$locationID 	=$nameArray[0][csf('location_id')];
	$storeID 		=$nameArray[0][csf('store_id')];
	$floorID 		=$nameArray[0][csf('floor_id')];
	$roomID 		=$nameArray[0][csf('room_id')];
	$rackID 		=$nameArray[0][csf('floor_room_rack_id')];

	$checkPrevTransFloorArr=sql_select("select store_id as from_store,0 as to_store,floor_id,0 as to_floor_id,room, 0 as to_room,rack,0 as to_rack,self as shelf,0 as to_shelf,bin_box,0 as to_bin_box from inv_transaction where item_category in(2,13) and company_id=$companyID and store_id=$storeID and floor_id=$floorID and room=$roomID and rack=$rackID group by  store_id , floor_id, room ,rack, self, bin_box
   union all
   select b.from_store,b.to_store,b.floor_id,b.to_floor_id,b.room,b.to_room,b.rack,b.to_rack,shelf,to_shelf,b.bin_box,b.to_bin_box from inv_item_transfer_mst a, inv_item_transfer_dtls b where a.id=b.mst_id and b.item_category in(2,13) and (a.company_id=$companyID or a.to_company=$companyID) and (a.location_id=$locationID  or  a.to_location_id=$locationID) and (b.from_store=$storeID or b.to_store=$storeID) and (b.floor_id=$floorID or b.to_floor_id=$floorID) and (b.room=$roomID or b.to_room=$roomID) and (b.rack=$rackID or b.to_rack=$rackID) group by  b.from_store,b.to_store,b.floor_id,b.to_floor_id,b.room,b.to_room,b.rack,b.to_rack,shelf,to_shelf,b.bin_box,b.to_bin_box ");

	if($checkPrevTransFloorArr[0][csf('rack')]>0 || $checkPrevTransFloorArr[0][csf('to_rack')]>0)
	{
		echo "$('#cbo_company_name').attr('disabled','disabled')" . ";\n";
		echo "$('#cbo_location_name').attr('disabled','disabled')" . ";\n";
		echo "$('#cbo_store_name').attr('disabled','disabled')" . ";\n";
		echo "$('#cbo_floor_id').attr('disabled','disabled')" . ";\n";
		echo "$('#cbo_room_id').attr('disabled','disabled')" . ";\n";
		echo "$('#txt_rack').attr('disabled','disabled')" . ";\n";
	}
	else
	{
		echo "$('#cbo_company_name').removeAttr('disabled')" . ";\n";
		echo "$('#cbo_location_name').removeAttr('disabled')" . ";\n";
		echo "$('#cbo_store_name').removeAttr('disabled')" . ";\n";
		echo "$('#cbo_floor_id').removeAttr('disabled')" . ";\n";
		echo "$('#cbo_room_id').removeAttr('disabled')" . ";\n";
		echo "$('#txt_rack').removeAttr('disabled')" . ";\n";
	}

}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0)  // Insert Here
	{
		if (is_duplicate_field( "a.floor_room_rack_name", "lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b", "a.company_id=$cbo_company_name and b.location_id=$cbo_location_name and b.store_id=$cbo_store_name and b.floor_id=$cbo_floor_id and b.room_id=$cbo_room_id  and a.floor_room_rack_id=b.rack_id and a.floor_room_rack_name=$txt_rack and a.is_deleted=0" ) == 1)
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
			$data_array="(".$id.",".$cbo_company_name.",".$txt_rack.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",0)";

			$id_dtls=return_next_id( "floor_room_rack_dtls_id", "lib_floor_room_rack_dtls", 1 ) ;
			$field_array_dtls="floor_room_rack_dtls_id,rack_id,floor_id,room_id,company_id,location_id,store_id,serial_no,inserted_by,insert_date,status_active,is_deleted";
			$data_array_dtls="(".$id_dtls.",".$id.",".$cbo_floor_id.",".$cbo_room_id.",".$cbo_company_name.",".$cbo_location_name.",".$cbo_store_name.",".$txt_rack_sequence.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",0)";

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

		if (is_duplicate_field( "a.floor_room_rack_name", "lib_floor_room_rack_mst a,lib_floor_room_rack_dtls b", "a.company_id=$cbo_company_name and b.location_id=$cbo_location_name and b.store_id=$cbo_store_name and b.floor_id=$cbo_floor_id and b.room_id=$cbo_room_id  and a.floor_room_rack_id=b.rack_id and a.floor_room_rack_name=$txt_rack and a.floor_room_rack_id!=$update_id and a.is_deleted=0" ) == 1)
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
			$data_array="".$cbo_company_name."*".$txt_rack."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*0";

			$field_array_dtls="room_id*floor_id*company_id*location_id*store_id*serial_no*updated_by*update_date*status_active*is_deleted";
			//$data_array_dtls="".$update_id."*".$cbo_floor_id."*".$cbo_company_name."*".$cbo_location_name."*".$cbo_store_name."*".$txt_rack_sequence."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*0";
			$data_array_dtls="".$cbo_room_id."*".$cbo_floor_id."*".$cbo_company_name."*".$cbo_location_name."*".$cbo_store_name."*".$txt_rack_sequence."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*0";

			/*echo "10**update lib_floor_room_rack_dtls set(".$field_array_dtls.")=".$data_array_dtls;
			oci_rollback($con);
			disconnect($con);
			die;*/

			$rID=sql_update("lib_floor_room_rack_mst",$field_array,$data_array,"floor_room_rack_id", $update_id,1);
			//$rID_dtls=sql_update("lib_floor_room_rack_dtls",$field_array_dtls,$data_array_dtls,"floor_id","".$update_id."",1);
			$rID_dtls=sql_update("lib_floor_room_rack_dtls",$field_array_dtls,$data_array_dtls,"floor_room_rack_dtls_id", $update_id,1);

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

if($action=="print_barcode")
{
	//echo "1000".$data;die;
	$rack_dtls_arr=array_unique(array_filter(explode(",",$data)));


	$company_lib = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$store_lib = return_library_array("select id, store_name from lib_store_location", "id", "store_name");
	$floor_room_rack_library = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst", "floor_room_rack_id", "floor_room_rack_name");

	$PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'qrcode_image'.DIRECTORY_SEPARATOR;
	$PNG_WEB_DIR = 'qrcode_image/';

	foreach (glob($PNG_WEB_DIR."*.png") as $filename) {
		@unlink($filename);
	}

	if (!file_exists($PNG_TEMP_DIR)) mkdir($PNG_TEMP_DIR);

	$filename = $PNG_TEMP_DIR.'test.png';
	$errorCorrectionLevel = 'L';
	$matrixPointSize = 4;

    include "../../../ext_resource/phpqrcode/qrlib.php";
	require_once("../../../ext_resource/mpdf60/mpdf.php");

	$mpdf = new mPDF('',    // mode - default ''
					array(55,75),		// array(65,210),    // format - A4, for example, default ''
					 6,     // font size - default 0
					 '',    // default font family
					 2,    // margin_left
					 2,    // margin right
					 2,     // margin top
					 0,    // margin bottom
					 0,     // margin header
					 0,     // margin footer
					 'L');



	$con = connect();
	$r_id1 = execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and ref_from=1 and entry_form in (159)");
	if($r_id1)
	{
		oci_commit($con);
		disconnect($con);
	}
	foreach ($rack_dtls_arr as $row)
	{
		$all_rack_dtls_arr[$row]=$row;
	}


	fnc_tempengine("GBL_TEMP_ENGINE", $user_id, 159, 1,$all_rack_dtls_arr, $empty_arr);
	//die;
	$i 		= 1;
	$html 	= '';

	// $sql= "SELECT a.company_id, a.floor_room_rack_name, b.floor_room_rack_dtls_id, b.store_id, b.floor_id, b.room_id,b.rack_id, concat(concat(b.floor_room_rack_dtls_id, '***'), a.floor_room_rack_name) as rackinfo from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b, GBL_TEMP_ENGINE c where a.floor_room_rack_id=b.rack_id  and b.floor_room_rack_dtls_id=c.ref_val and c.user_id=$user_id and c.ref_from=1 and c.entry_form=159 and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 order by b.floor_room_rack_dtls_id";

	$sql= "SELECT a.company_id, a.floor_room_rack_name, b.floor_room_rack_dtls_id, b.store_id, b.floor_id, b.room_id,b.rack_id, b.shelf_id, b.bin_id from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b, GBL_TEMP_ENGINE c where a.floor_room_rack_id=b.rack_id  and b.floor_room_rack_dtls_id=c.ref_val and c.user_id=$user_id and c.ref_from=1 and c.entry_form=159 and a.status_active in(1,2,3) and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 order by b.floor_room_rack_dtls_id";

	//echo $sql;die;

	$dataArray = sql_select($sql);

	execute_query("delete from GBL_TEMP_ENGINE where user_id=$user_id and ref_from=1 and entry_form in (159)");
    oci_commit($con);
	disconnect($con);

	foreach($dataArray as $row)
	{
		if(!empty($row['BIN_ID'])){
			$rackinfo = $row['FLOOR_ROOM_RACK_DTLS_ID'].'***'.$floor_room_rack_library[$row['BIN_ID']]."/".$floor_room_rack_library[$row['SHELF_ID']]."/".$floor_room_rack_library[$row['RACK_ID']];
		}
		elseif(!empty($row['SHELF_ID'])){
			$rackinfo = $row['FLOOR_ROOM_RACK_DTLS_ID'].'***'.$floor_room_rack_library[$row['SHELF_ID']]."/".$floor_room_rack_library[$row['RACK_ID']]."/".$floor_room_rack_library[$row['ROOM_ID']];
		}
		elseif(!empty($row['RACK_ID'])){
			$rackinfo = $row['FLOOR_ROOM_RACK_DTLS_ID'].'***'.$floor_room_rack_library[$row['RACK_ID']]."/".$floor_room_rack_library[$row['ROOM_ID']]."/".$floor_room_rack_library[$row['FLOOR_ID']];
		}
		elseif(!empty($row['ROOM_ID'])){
			$rackinfo = $row['FLOOR_ROOM_RACK_DTLS_ID'].'***'.$floor_room_rack_library[$row['ROOM_ID']]."/".$floor_room_rack_library[$row['FLOOR_ID']]."/".$floor_room_rack_library[$row['STORE_ID']];
		}

		$filename = $PNG_TEMP_DIR.md5($rackinfo).'.png';
		QRcode::png($rackinfo, $filename, $errorCorrectionLevel, $matrixPointSize, 2);


		$mpdf->AddPage('',    // mode - default ''
				array(55,75),		// array(65,210),    // format - A4, for example, default ''
				6,     // font size - default 0
				'',    // default font family
				2,    // margin_left
				2,    // margin right
				2,     // margin top
				0,    // margin bottom
				0,     // margin header
				0,     // margin footer
				'L');


		//@media screen, print

		$html.='

		<style>
			@media print
			{
				table, tr, td {
						font-size: 13px !important;
						width:100%; !important;
						text-align:center; !important;
					}
				}

			}
		</style>
		<table  cellpadding="0" cellspacing="0" >

			<tr>
				<td align="center" >
				<div  id="div_'.$i.'"><img src="'.$PNG_WEB_DIR.basename($filename).'" height="100" width=""></div>
				</td>
			</tr>
			<tr>
				<td align="center"><span style="font-weight:bold">Store : </span>'. $store_lib[$row[csf("store_id")]].'</td>
			</tr>
			<tr>
				<td align="center"><span style="font-weight:bold">Floor : </span>'.$floor_room_rack_library[$row[csf('floor_id')]].'</td>
			</tr>
			<tr>
				<td><span style="font-weight:bold">Room : </span>'.$floor_room_rack_library[$row[csf('room_id')]].'</td>
			</tr>

			<tr>
				<td align="center" style="font-weight:bold"><span >Rack : </span>'.$row[csf('floor_room_rack_name')].'</td>
			</tr>
			<tr style="width:100%, text-align: center;vertical-align: middle;padding: 0;">
				<td align="center"><barcode code="'.$rackinfo.'" type="C39" size="0.5"  height="2.0" /></td>
			</tr>

		</table>';

		$mpdf->WriteHTML($html);
		$html='';
		$i++;
	}

	foreach (glob("*.pdf") as $filename)
	{
		@unlink($filename);
	}


	$name ='qr_barcode'.date('j-M-Y_h-iA').'.pdf';

	$mpdf->Output( "".$name, 'F');
	echo "requires/".$name;
	exit();

}

?>