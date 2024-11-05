<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');
include('../../../includes/class4/class.conditions.php');
include('../../../includes/class4/class.reports.php');
include('../../../includes/class4/class.fabrics.php');


$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

$field_level_data_arr =  $_SESSION['logic_erp']['data_arr'][17];

if($action=="company_wise_report_button_setting"){
	
	extract($_REQUEST);

	$print_report_format=return_field_value("format_id"," lib_report_template","template_name ='".$data."'  and module_id=6 and report_id=125 and is_deleted=0 and status_active=1");

	$print_report_format_arr=explode(",",$print_report_format);

	echo "$('#print').hide();\n";
	echo "$('#show_button').hide();\n";
	
	if($print_report_format != "")
	{
		foreach($print_report_format_arr as $id)
		{
			if($id==78){echo "$('#print').show();\n";}
			if($id==66){echo "$('#show_button').show();\n";}			
		}
	}
	else
	{		
		echo "$('#print').hide();\n";
		echo "$('#show_button').hide();\n";
	}

	exit();
}

if($action=="varible_inv_auto_batch_maintain")
{
	$sql_variable_inv_auto_batch=sql_select("select id, user_given_code_status  from variable_settings_inventory where company_name=$data and variable_list=33 and status_active=1 and is_deleted=0 and item_category_id = 3");

 	echo $sql_variable_inv_auto_batch[0][csf("user_given_code_status")];
	//if(count($sql_variable_inv_auto_batch)>0){echo 1;}else{echo 0;}
	die;
}
if($action=="varible_inventory_upto_rack")
{
	$sql_variable_inv_rack_wise=sql_select("select id, store_method  from variable_settings_inventory where company_name=$data and variable_list=21 and status_active=1 and item_category_id = 3");
	if(count($sql_variable_inv_rack_wise)>0)
	{
		echo "1**".$sql_variable_inv_rack_wise[0][csf("store_method")];
	}
	else
	{
		echo "0**".$sql_variable_inv_rack_wise[0][csf("store_method")];
	}
	die;
}
if($action=="varible_inventory")
{
	$sql_variable_inventory=sql_select("select id, independent_controll, rate_optional, is_editable, rate_edit  from variable_settings_inventory where company_name=$data and variable_list=20 and status_active=1 and menu_page_id=17");
	if(count($sql_variable_inventory)>0)
	{
		echo "1**".$sql_variable_inventory[0][csf("independent_controll")]."**".$sql_variable_inventory[0][csf("rate_optional")]."**".$sql_variable_inventory[0][csf("is_editable")]."**".$sql_variable_inventory[0][csf("rate_edit")];
	}
	else
	{
		echo "0**".$sql_variable_inventory[0][csf("independent_controll")]."**".$sql_variable_inventory[0][csf("rate_optional")]."**".$sql_variable_inventory[0][csf("is_editable")]."**".$sql_variable_inventory[0][csf("rate_edit")];
	}
	/*$variable_inventory=return_field_value("store_method","variable_settings_inventory","company_name=$data and item_category_id=8 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	echo "**".$variable_inventory;*/
	die;
}

//--------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------

//load drop down supplier
if ($action=="load_drop_down_supplier")
{
	if($db_type==0)
	{
		echo create_drop_down( "cbo_supplier", 120, "select id,supplier_name from lib_supplier where FIND_IN_SET($data,tag_company) and FIND_IN_SET(9,party_type) order by supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
		exit();
	}
	else
	{
		echo create_drop_down( "cbo_supplier", 120, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data' and b.party_type =9 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
		exit();
	}
}
if($action == "load_drop_down_buyer")
{
	if($data !=0){
		echo create_drop_down( "cbo_buyer_name", 130, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
		exit();
	}
	else{
		echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- Select Buyer --", $selected, "" );
		exit();
	}
}

if ($action=="load_drop_down_location")
{
	//$locationArr=return_library_array( "select id,location_name from lib_location where company_id=$data and is_deleted=0  and status_active=1",'id','location_name');
	$PreviligLocationArr=return_library_array( "select id,company_location_id from user_passwd where id='$user_id' and is_deleted=0  and status_active=1",'id','company_location_id');
	if($PreviligLocationArr[$user_id]!=""){$PreviligLocationCond="and id in($PreviligLocationArr[$user_id])";}
	$sql_location = "select id, location_name from lib_location where company_id='$data' $PreviligLocationCond and status_active=1 and is_deleted=0 group by id, location_name order by location_name";

	echo create_drop_down("cbo_location", 130, $sql_location, "id,location_name", 1, "--Select Location--", 0,"load_drop_down( 'requires/woven_finish_fabric_roll_receive_controller', $data+'_'+this.value, 'load_drop_down_store', 'store_td');");

}

if($action=="load_drop_down_store")
{
	$data= explode("_", $data);
	echo create_drop_down( "cbo_store_name", 130, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and b.category_type=3 and a.company_id=$data[0] and a.location_id=$data[1] and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "fn_load_floor(this.value);" );
	//fnc_details_row_blank();
}

if($action=="floor_list")
{
	$data_ref=explode("__",$data);
	$floor_arr=array();

	if($data_ref[0] && $data_ref[1])
	{
		$floor_data=sql_select("select b.floor_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.floor_id and b.store_id='$data_ref[1]' and a.company_id='$data_ref[0]' and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
		group by b.floor_id,a.floor_room_rack_name  order by a.floor_room_rack_name");
		foreach($floor_data as $row)
		{
			$floor_arr[$row[csf('floor_id')]]=$row[csf('floor_room_rack_name')];
		}
	}
	$jsFloor_arr= json_encode($floor_arr);
	echo $jsFloor_arr;
	die();
}

if($action=="room_list")
{
	$data_ref=explode("__",$data); // com_id + "__" + location_id + "__" + store_id + "__" + floor_id;
	$room_arr=array();
	$location_cond = "";
	if($data_ref[1]) $location_cond = "and b.location_id='$data_ref[1]'";
	$room_data=sql_select("select b.room_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.room_id and b.store_id='$data_ref[2]' and a.company_id='$data_ref[0]' and b.floor_id='$data_ref[3]' $location_cond and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	group by b.room_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc");
	foreach($room_data as $row)
	{
		$room_arr[$row[csf('room_id')]]=$row[csf('floor_room_rack_name')];
	}
	$jsRoom_arr= json_encode($room_arr);
	echo $jsRoom_arr;
	die();
}

if($action=="rack_list")
{
	$data_ref=explode("__",$data); // com_id + "__" + location_id + "__" + store_id + "__" + floor_id;
	$rack_arr=array();
	$location_cond = "";
	if($data_ref[1]) $location_cond = "and b.location_id='$data_ref[1]'";
	$rack_data=sql_select("select b.rack_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.rack_id and b.store_id='$data_ref[2]' and a.company_id='$data_ref[0]' and b.room_id='$data_ref[3]' $location_cond and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	group by b.rack_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc");
	foreach($rack_data as $row)
	{
		$rack_arr[$row[csf('rack_id')]]=$row[csf('floor_room_rack_name')];
	}
	$jsRack_arr= json_encode($rack_arr);
	echo $jsRack_arr;
	die();
}

if($action=="shelf_list")
{
	$data_ref=explode("__",$data); // com_id + "__" + location_id + "__" + store_id + "__" + floor_id;
	$shelf_arr=array();
	$location_cond = "";
	if($data_ref[1]) $location_cond = "and b.location_id='$data_ref[1]'";
	$shelf_data=sql_select("select b.shelf_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.shelf_id and b.store_id='$data_ref[2]' and a.company_id='$data_ref[0]' and b.rack_id='$data_ref[3]' $location_cond and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	group by b.shelf_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc");
	foreach($shelf_data as $row)
	{
		$shelf_arr[$row[csf('shelf_id')]]=$row[csf('floor_room_rack_name')];
	}
	$jsShelf_arr= json_encode($shelf_arr);
	echo $jsShelf_arr;
	die();
}

if($action=="bin_list")
{
	$data_ref=explode("__",$data);
	$bin_arr=array();
	$location_cond = "";
	if($data_ref[1]) $location_cond = "and b.location_id='$data_ref[1]'";
	$bin_data=sql_select("select b.bin_id, a.floor_room_rack_name from lib_floor_room_rack_mst a, lib_floor_room_rack_dtls b where a.floor_room_rack_id=b.bin_id and b.store_id='$data_ref[2]' and a.company_id='$data_ref[0]' and b.shelf_id='$data_ref[3]' $location_cond and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0
	group by b.bin_id,a.floor_room_rack_name  order by a.floor_room_rack_name asc");
	foreach($bin_data as $row)
	{
		$bin_arr[$row[csf('bin_id')]]=$row[csf('floor_room_rack_name')];
	}
	$jsBin_arr= json_encode($bin_arr);
	echo $jsBin_arr;
	die();
}



if ($action=="load_drop_down_bodypart")
{
	$datas=explode("_", $data);
	$data = implode(",",$datas);
	echo create_drop_down( "cbo_body_part", 167, "select id,body_part_full_name from  lib_body_part where id in($data) and status_active=1 and is_deleted=0 order by body_part_full_name","id,body_part_full_name", 1, "-- Select Body Part --", $datas[0], "","" );
	exit();
}
if ($action=="load_room_rack_self_bin")
{
	load_room_rack_self_bin("requires/woven_finish_fabric_roll_receive_controller",$data);
}

//load drop down color
if($action=="load_drop_down_color")
{
	echo create_drop_down( "cbo_color", 110, "select id,color_name from lib_color where status_active=1 order by color_name and color_name!=''","id,color_name", 1, "--Select--", 0, "",0 );
	echo '<input type="button" name="btn_color" id="btn_color" class="formbutton"  style="width:20px" onClick="fn_color_new(this.id)" value="N" />';
	exit();
}
if ($action=="load_drop_down_season")
{
	$data_arr = explode("*", $data);
	if($data_arr[1] == 1) $width=100; else $width=100;
	echo create_drop_down( "cbo_season_id", $width, "select id, season_name from lib_buyer_season where buyer_id='$data_arr[0]' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- Select Season--", "", "" );
	exit();
}

if ($action=="load_drop_down_brand")
{
	$data_arr = explode("*", $data);
	if($data_arr[1] == 1) $width=100; else $width=100;
	echo create_drop_down( "cbo_brand_id", $width, "select id, brand_name from lib_buyer_brand brand where buyer_id='$data_arr[0]' and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC","id,brand_name", 1, "--Brand--", $selected, "" );
	exit();
}

if($action=="check_conversion_rate")
{
	$data=explode("**",trim($data));
	if($db_type==0)
	{
		$conversion_date=change_date_format($data[1], "Y-m-d", "-",1);
	}
	else
	{
		$conversion_date=change_date_format($data[1], "d-M-y", "-",1);
	}

	$currency_rate=set_conversion_rate( $data[0], $conversion_date,$data[2] );
	echo "1"."_".$currency_rate;
	exit();
}

if($action=="fn_fabric_descriptin_variable_check")
{
	$data=explode("**",trim($data));
	$variable_check_value=return_field_value("allocation","variable_settings_inventory","company_name ='$data[0]' and variable_list=26 and is_deleted=0 and status_active=1");
	echo "1"."_".$variable_check_value;
	exit();
}

if($action=="addi_info_popup")
{
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);

	$user_info_arr=return_library_array("SELECT a.id, a.user_full_name, b.custom_designation from user_passwd a, lib_designation b where a.designation = b.id and a.valid = 1 order by a.user_full_name","id","user_full_name");


 	if(str_replace("'","",$pre_addi_info))
	{
		$pre_addi_info_arr = explode("_",str_replace("'","",$pre_addi_info)); 

		$txt_gate_entry_no = $pre_addi_info_arr[0];
		$txt_delv_chln_date = $pre_addi_info_arr[1];
		$txt_gate_entry_date = $pre_addi_info_arr[2];
		$txt_vehicle_no = $pre_addi_info_arr[3];
		$txt_invoice_no = $pre_addi_info_arr[4];
		$txt_transporter_name = $pre_addi_info_arr[5];
		$txt_invoice_date = $pre_addi_info_arr[6];
		$txt_short_qnty = $pre_addi_info_arr[7];
		$txt_excess_qnty = $pre_addi_info_arr[8];
	}
	
	?>
	<script>

	function fnClosed_addinfo() 
	{   var txtString = "";
		txtString = $("#txt_gate_entry_no").val() + '_' + $("#txt_delv_chln_date").val() + '_' + $("#txt_gate_entry_date").val() + '_' + $("#txt_vehicle_no").val() + '_' + $("#txt_invoice_no").val() + '_' + $("#txt_transporter_name").val() + '_' + $("#txt_invoice_date").val() + '_' + $("#txt_short_qnty").val() + '_' + $("#txt_excess_qnty").val();
		$("#txt_string").val(txtString);
		parent.emailwindow.hide();
 	}

	</script>
	</head>
	<body>
		<div align="center" style="width:100%;" >
			<br>
			<form name="searchlcfrm_1" id="searchlcfrm_1" autocomplete="off">
				<fieldset style="width:650px;">   
				<table  width="650" cellspacing="2" cellpadding="0" border="0" >
					<tr>
						<td width="100">
							<b>Gate Entry No</b>
						</td>
						<td>
							<input type="text" id="txt_gate_entry_no" name="txt_gate_entry_no" style="width:150px" class="text_boxes" value="<? echo $txt_gate_entry_no; ?>" />
						</td>
						
						<td width="120">
							<b>Delivery Challan Date</b>
						</td>
						<td>
							<input type="text" id="txt_delv_chln_date" name="txt_delv_chln_date" style="width:150px" class="datepicker" value="<? echo $txt_delv_chln_date ; ?>" readonly />
						</td>
					</tr>
					<tr>
						<td width="100">
							<b>Gate Entry Date</b>
						</td>
						<td>
							<input type="text" id="txt_gate_entry_date" name="txt_gate_entry_date" style="width:150px" class="datepicker" value="<? echo $txt_gate_entry_date ; ?>" readonly />
						</td>
						<td width="100">
							<b>Vehicle No</b>
						</td>
						<td>
							<input type="text" id="txt_vehicle_no" name="txt_vehicle_no" style="width:150px" class="text_boxes" value="<? echo $txt_vehicle_no; ?>" />
						</td>
					</tr>
					<tr>
						<td width="100">
							<b>Invoice No</b>
						</td>
						<td>
							<input type="text" id="txt_invoice_no" name="txt_invoice_no" style="width:150px" class="text_boxes" value="<? echo $txt_invoice_no; ?>" />
						</td>
						<td width="100">
							<b>Transporter Name</b>
						</td>
						<td>
							<input type="text" id="txt_transporter_name" name="txt_transporter_name" style="width:150px" class="text_boxes" value="<? echo $txt_transporter_name; ?>" />
						</td>
					</tr>
					<tr>
						<td width="100">
							<b>Invoice Date</b>
						</td>
						<td>
							<input type="text" id="txt_invoice_date" name="txt_invoice_date" style="width:150px" class="datepicker" value="<? echo $txt_invoice_date ; ?>" readonly />
						</td>
						<td width="100">
							<b>Short Qty</b>
						</td>
						<td>
							<input type="text" id="txt_short_qnty" name="txt_short_qnty" style="width:150px" class="text_boxes" value="<? echo $txt_short_qnty; ?>" />
						</td>

					</tr>
					<tr>
						<td width="100">
							
						</td>
						<td>
						
						</td>
						<td width="100">
							<b>Excess Qty</b>
						</td>
						<td>
							<input type="text" id="txt_excess_qnty" name="txt_excess_qnty" style="width:150px" class="text_boxes" value="<? echo $txt_excess_qnty; ?>" />
						</td>
					</tr>
				

				</table>
				<br>  
	            <div><input type="button" name="btn_close" class="formbutton" style="width:100px" value="Close" onClick="fnClosed_addinfo()" /></div>

				<input type="hidden" id="txt_string" value="" />
				<br>
				</fieldset>
			</form>
		</div>
	</body>           
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

// wo/pi popup here----------------------//
if ($action=="wopi_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo "var revBasis =  $receive_basis ";
?>

<script>
var revBasis =  <? echo $receive_basis;?>;
	function js_set_value(str)
	{
		//alert(str);
		var splitData = str.split("_");
		$("#hidden_tbl_id").val(splitData[0]); // wo/pi id
		$("#hidden_wopi_number").val(splitData[1]); // wo/pi number
		$("#hidden_is_non_ord_sample").val(splitData[2]); // wo/pi number
		$("#hidden_fabric_source").val(splitData[3]); // wo/pi number
		$("#hidden_basis").val(splitData[4]);
		$("#chkApproveStatus").val(splitData[5]);

		parent.emailwindow.hide();
	}

</script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="1400" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
            <thead>
                <tr>
                    <th width="120">Search By</th>
                    <th width="120" align="center" id="search_by_th_up">Enter WO/PI Number</th>
                    <th width="100">Buyer</th>
                    <th width="100">Brand</th>
                    <th width="100">Season</th>
                    <th width="50">Season Year</th>
                    <th width="70">Job No</th>
                    <th width="70">Style Ref </th>
                    <th width="70">PO Number</th>
					<th width="100">Actual PO Number</th>
					<th title="Partial means avilable qnty for recv and All means allow for Over recv qnty" width="70">Search By</th>
                    <th width="140">Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <?
                            echo create_drop_down( "cbo_search_by", 140, $receive_basis_arr,"",1, "--Select--", $receive_basis,"",1 );
                        ?>
                    </td>
                    <td width="180" align="center" id="search_by_td">
                        <input type="text" style="width:170px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                    </td>
                    <td id="buyer_td">
                        <? echo create_drop_down( "cbo_buyer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --","", "load_drop_down( 'woven_finish_fabric_roll_receive_controller', this.value+'*'+1, 'load_drop_down_season', 'season_td'); load_drop_down( 'woven_finish_fabric_roll_receive_controller', this.value+'*'+1, 'load_drop_down_brand', 'brand_td');","0" ); ?>
                    </td>
                    <td id="brand_td"><? echo create_drop_down( "cbo_brand_id", 100, "select id, brand_name from lib_buyer_brand brand where buyer_id='$buyerId' and status_active =1 and is_deleted=0 $brand_cond order by brand_name ASC","id,brand_name", 1, "--Brand--", $selected, ""  ); ?>
    				<td id="season_td"><? echo create_drop_down( "cbo_season_id", 100, "select id, season_name from lib_buyer_season where buyer_id='$buyerId' and status_active =1 and is_deleted=0 order by season_name ASC","id,season_name", 1, "-- Select Season--", "", ""); ?></td>

                    <td><?=create_drop_down( "cbo_season_year", 50, create_year_array(),"", 1,"-Year-", 1, "",0,"" ); ?></td>
                    <td><input name="txt_job" id="txt_job" class="text_boxes" style="width:60px" placeholder="Job Prefix No"/></td>
                    <td><input name="txt_style" id="txt_style" class="text_boxes" style="width:60px"></td>
                    <td><input name="txt_order_search" id="txt_order_search" class="text_boxes" style="width:60px"></td>
					<td><input name="txt_actual_order_search" id="txt_actual_order_search" class="text_boxes" style="width:100px"></td>
 					<td>
                        <?
                        $search_type_arr=array(1=>"All",2=>"Pending/Partial");
                            echo create_drop_down( "cbo_search_type", 100, $search_type_arr,"",1, "--Select--", 0,"",0 );
                        ?>
                    </td>    
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:50px" placeholder="From Date" />
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:50px" placeholder="To Date" />
                     </td>
                     <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_actual_order_search').value+'_'+document.getElementById('cbo_brand_id').value+'_'+document.getElementById('cbo_season_id').value+'_'+document.getElementById('cbo_season_year').value+'_'+document.getElementById('txt_job').value+'_'+document.getElementById('cbo_search_type').value, 'create_wopi_search_list_view', 'search_div', 'woven_finish_fabric_roll_receive_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                    </td>
            	</tr>
            </tbody>
            <tfoot>
                <tr style="background-color:#CCC;">
                    <td align="center" height="26" valign="middle" colspan="11">
                        <? echo load_month_buttons(1);  ?>
                        <!-- Hidden field here -->
                        <input type="hidden" id="hidden_tbl_id" value="" />
                        <input type="hidden" id="hidden_wopi_number" value="" />
                        <input type="hidden" id="hidden_is_non_ord_sample" value="" />
                        <input type="hidden" id="hidden_fabric_source" value="" />
                        <input type="hidden" id="hidden_basis" value="" />
                        <input type="hidden" id="chkApproveStatus" value="" />
                        <!-- END -->
                    </td>
                </tr>
            </tfoot>
         </tr>
        </table>
        <div align="center" style="margin-top:5px" id="search_div"> </div>
        </form>
   </div>
</body>
<script>
if(revBasis==1){
	$("#search_by_th_up").text("Enter PI Number");
}else if(revBasis==2 || revBasis==4){
	$("#search_by_th_up").text("Enter WO Number");
}
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}

if($action=="create_wopi_search_list_view")
{
 	$ex_data = explode("_",$data);
	$txt_search_by = $ex_data[0];
	$txt_search_common = trim($ex_data[1]);
	$txt_date_from = $ex_data[2];
	$txt_date_to = $ex_data[3];
	$company = $ex_data[4];
	$buyer = $ex_data[5];
	$style = $ex_data[6];
	$order = $ex_data[7];
	$cbo_year = $ex_data[8];
	$actual_order_no = trim($ex_data[9]);
	$brand_id=trim($ex_data[10]);
	$season_id=trim($ex_data[11]);
	$season_year=trim($ex_data[12]);
	$txt_job=trim($ex_data[13]);
	$cbo_search_type=trim($ex_data[14]);
	//echo $actual_order_no.'DD';
	$booking_cond='';
	$year_id=str_replace("'","",$cbo_year);;
	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(a.insert_date)=$year_id"; else $year_cond="";
		$concat_grp="group_concat(acc_po_no) as acc_po_no";
	}
	elseif($db_type==2)
	{
		if($year_id!=0) $year_cond=" and to_char(a.insert_date,'yyyy')=$year_id"; else $year_cond="";
		$concat_grp="listagg(cast( acc_po_no as varchar(4000)),',') within group (order by acc_po_no) as acc_po_no";
	}

 	$buyer_arr=return_library_array( "select id,buyer_name from lib_buyer where is_deleted=0  and status_active=1 order by buyer_name",'id','buyer_name');
	$act_po_no_arr=return_library_array( "select po_break_down_id,$concat_grp from wo_po_acc_po_info where is_deleted=0  and status_active=1 group by po_break_down_id order by po_break_down_id",'po_break_down_id','acc_po_no');


	//---------brand,season,season year------------------
	$brand_no_cond="";
	//echo $brand_id;
	if($brand_id>0)
	{
		$brand_no_cond=" and a.brand_id=$brand_id";
	}

	$season_no_cond="";
	if($season_id>0)
	{
		$season_no_cond=" and a.season_buyer_wise=$season_id";
	}
	$season_year_cond="";
	if($season_year>0)
	{
		$season_year_cond=" and a.season_year=$season_year";
	}
	//-------------------




	//echo $booking_cond; die;
	$sql_cond="";
	if(trim($txt_search_common)!="")
	{
		if(trim($txt_search_by)==1) // for pi
		{
			$sql_cond .= " and a.pi_number LIKE '%$txt_search_common%'";
			if( $txt_date_from!="" || $txt_date_to!="" )
			{
				if($db_type==0)
				{
					$sql_cond .= " and a.pi_date  between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
				}
				if($db_type==2 || $db_type==1)
				{
					$sql_cond .= " and a.pi_date  between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
				}
			}
			//if(trim($company)!="") $sql_cond .= " and a.importer_id='$company'";
		}
		else if(trim($txt_search_by)==2 || trim($txt_search_by)==4) // for wo
		{
			$sql_cond .= " and  a.booking_no LIKE '%$txt_search_common%'";
			if( $txt_date_from!="" || $txt_date_to!="" )
			{
				if($db_type==0)
				{
					$sql_cond .= " and  a.wo_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
				}
				if($db_type==2 || $db_type==1)
				{
					$sql_cond .= " and  a.wo_date between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
				}
			}
			if(trim($company)!="") $sql_cond .= " and  a.company_id='$company'";
		}
 	}

 	if($buyer!=0 || $style!='' || $txt_job!='' || $order!='' || $actual_order_no!='' || $brand_id!=0 || $season_id!=0 || $season_year!=0)
 	{
 		$sql_condition=''; $sql_condition_non='';
 		if(trim($txt_search_by)==2 || trim($txt_search_by)==4) // for wo
		{
			$sql_condition .= " and d.booking_no LIKE '%$txt_search_common%'";
			if( $txt_date_from!="" || $txt_date_to!="" )
			{
				if($db_type==0)
				{
					$sql_condition .= " and d.wo_date between '".change_date_format($txt_date_from,'yyyy-mm-dd')."' and '".change_date_format($txt_date_to,'yyyy-mm-dd')."'";
				}
				if($db_type==2 || $db_type==1)
				{
					$sql_condition .= " and d.wo_date between '".change_date_format($txt_date_from,'','',1)."' and '".change_date_format($txt_date_to,'','',1)."'";
				}
			}
			if(trim($company)!="") $sql_condition .= " and d.company_id='$company'";
			if($order!= '')
			{
				$po_cond= " and b.po_number LIKE '%$order%'";
			}
		}

 		if($buyer!=0)
 		{
 			$sql_condition .= " and a.buyer_name=$buyer";
 			$sql_condition_non .= " and a.buyer_id=$buyer";
 			$sql_condition_non2 .= " and a.buyer_name=$buyer";
 		}
 		if($style!='')
 		{
 			$sql_condition .= " and a.style_ref_no LIKE '%$style%'";
 			$sql_condition_non .= " and b.style_des LIKE '%$style%'";
			$po_cond .= " and b.style_des LIKE '%$style%'";
 		}
 		if($txt_job!='')
 		{
 			$sql_job_cond = " and a.job_no_prefix_num=$txt_job"; 
 		}


		if($order!= '')
 		{
 			$sql_condition .= " and b.po_number LIKE '%$order%'";
 		}
		//if(($buyer!=0 || $style!='' || $order!='') && $actual_order_no!="" )
		if($actual_order_no!="" || $order!="" || $style!='' || $brand_id!=0 || $season_id!=0 || $season_year!=0)
 		{
 			if($actual_order_no!="")
 			{
 				$act_po_condition = " and b.po_number LIKE '%$actual_order_no%'";
 			}
 			
		 	$actaul_sql_dtls ="select b.po_number,b.id from wo_po_details_master a, wo_po_break_down b  where a.id=b.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.garments_nature=3 $year_cond $act_po_condition $po_cond $brand_no_cond $season_no_cond $season_year_cond and a.company_name=$company $sql_condition_non2 ";
			$act_result_dtls = sql_select($actaul_sql_dtls);
			foreach($act_result_dtls as $val)
			{
				$act_po_ids.=$val[csf("id")].",";
			}
				$actual_po_ids=rtrim($act_po_ids,",");
				$actual_po_ids=implode(",",array_unique(explode(",",$actual_po_ids)));
				if($actual_po_ids!='') $actual_po_cond="and b.id in($actual_po_ids)";else $actual_po_cond="";
 		}
 		// and d.pay_mode!=2
 		$sql_dtls ="select d.id,a.buyer_name,a.style_ref_no,b.po_number,b.id as po_id,b.grouping from wo_po_details_master a, wo_po_break_down b , wo_booking_dtls c  , wo_booking_mst d where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.booking_no=d.booking_no and d.item_category=3 $sql_condition  and  a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $actual_po_cond  and a.garments_nature=3 $year_cond $brand_no_cond $season_no_cond $season_year_cond $sql_job_cond";
 		//echo $sql_dtls;// die;
 		$result_dtls = sql_select($sql_dtls);
 		if(count($result_dtls)<1)
 		{
 			echo "Search criteria does not matched"; die;
 		}
 		else
 		{
 			$booking_dtls_dataArr=array();
	 		foreach($result_dtls as $val)
			{
				$booking_ids.=$val[csf("id")].",";
				$booking_dtls_dataArr[$val[csf('id')]]['buyer_name'].=$buyer_arr[$val[csf('buyer_name')]].",";
				$booking_dtls_dataArr[$val[csf('id')]]['style_ref_no'].=$val[csf('style_ref_no')].",";
				$booking_dtls_dataArr[$val[csf('id')]]['grouping'].=$val[csf('grouping')].",";
				$booking_dtls_dataArr[$val[csf('id')]]['po_number'].=$val[csf('po_number')].",";
				$booking_dtls_dataArr[$val[csf('id')]]['act_po_number'].=$act_po_no_arr[$val[csf('po_id')]].",";
			}
			//echo $booking_ids.'DSD';
			//print_r($booking_dtls_dataArr);
			$booking_cond=""; $booking_cond_pi="";
			if($booking_ids!='')
			{
				$booking_ids=rtrim($booking_ids,",");
				$booking_ids=array_chunk(array_unique(explode(",",$booking_ids)),999, true);
				$ji=0;
			   	foreach($booking_ids as $key=> $value)
			   	{
				   if($ji==0)
				   {
						$booking_cond=" and id in(".implode(",",$value).")";
						$booking_cond_pi=" and work_order_id in(".implode(",",$value).")";
				   }
				   else
				   {
						$booking_cond.=" or id in(".implode(",",$value).")";
						$booking_cond_pi.=" or work_order_id in(".implode(",",$value).")";
				   }
				   $ji++;
			   	}
			}
 		}
 	}
 	if($txt_search_by==1 )
 	{
		$approval_status_cond="";
		if($db_type==0)
		{
			$approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format(date('d-m-Y'),'yyyy-mm-dd')."' and company_id='$company')) and page_id=18 and status_active=1 and is_deleted=0";
		}
		else
		{
			$approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format(date('d-m-Y'), "", "",1)."' and company_id='$company')) and page_id=18 and status_active=1 and is_deleted=0";
		}
		$approval_status=sql_select($approval_status);
		if($approval_status[0][csf('approval_need')]==1)
		{
			$approval_status_cond= "and a.approved = 1";
		}
 	}
 	
	if($txt_search_by==1 )
	{
		$pi_cond='';
		if(($buyer!=0 || $style!='' || $txt_job!='' || $order!='' || $actual_order_no!='' || $brand_id!=0 || $season_id!=0 || $season_year!=0) && $booking_cond_pi!="" )
	 	{
	 		$sql_pi ="select  pi_id from com_pi_item_details where status_active=1 and is_deleted=0 $booking_cond_pi group by pi_id  order by pi_id ";
	 		//echo $sql_pi; die;
	 		$result_pi = sql_select($sql_pi); $pi_ids='';
	 		foreach($result_pi as $val)
			{
				//echo $val[csf("pi_id")]."**";
				$pi_ids.=$val[csf("pi_id")].",";
			}
			//echo $pi_ids;
			$pi_ids=rtrim($pi_ids,",");
			$pi_ids=array_chunk(array_unique(explode(",",$pi_ids)),999, true);
			$pi_cond="";
			$ji=0;
		   	foreach($pi_ids as $key=> $value)
		   	{
			   if($ji==0)
			   {
					$pi_cond=" and a.id in(".implode(",",$value).")";
			   }
			   else
			   {
					$pi_cond.=" or a.id  in(".implode(",",$value).")";
			   }
			   $ji++;
		   	}
	 	}
		//echo $pi_cond; die;
		if($db_type==0)
		{
 		 	$sql = "select a.id as id,a.pi_number as wopi_number,b.lc_number as lc_number,a.pi_date as wopi_date,a.supplier_id as supplier_id,a.currency_id as currency_id,a.source as source,0 as is_non_ord_sample, a.pi_basis_id, 1 as is_pi
				from com_pi_master_details a left join com_btb_lc_master_details b on FIND_IN_SET(a.id,b.pi_id)
				where
				a.item_category_id = 3 and
				a.status_active=1 and a.is_deleted=0 and
				a.importer_id=$company and a.pi_basis_id=1 and a.goods_rcv_status !=1 
				$sql_cond $approval_status_cond $pi_cond $year_cond order by wopi_date desc ";//a.supplier_id in (select id from lib_supplier where FIND_IN_SET($company,tag_company) )
		}
		else
		{
			$sql = "select a.id as id, a.pi_number as wopi_number,  a.pi_date as wopi_date, a.supplier_id as supplier_id, a.currency_id as currency_id, a.source as source, c.lc_number as lc_number,0 as is_non_ord_sample, a.pi_basis_id, 1 as is_pi,x.pi_id as dtls_table_mst_pi_id,sum(x.quantity) as pi_qnty 
				from com_pi_master_details a
				left join com_btb_lc_pi b on a.id=b.pi_id
				left join com_btb_lc_master_details c on b.com_btb_lc_master_details_id=c.id  left join com_pi_item_details x on a.id=x.pi_id  
				where
				a.item_category_id = 3 and
				a.status_active=1 and a.is_deleted=0 and
				a.importer_id=$company and a.pi_basis_id=1 and a.goods_rcv_status !=1 
				$sql_cond $approval_status_cond $pi_cond $year_cond group by  a.id , a.pi_number , a.pi_date , a.supplier_id, a.currency_id , a.source, c.lc_number , a.pi_basis_id,x.pi_id order by wopi_date desc";
		}//echo $sql;

		if($cbo_search_type==2)
		{
			$resultPI = sql_select($sql);
			$pi_wise_recv_array=array();$pi_wise_required_array=array(); $piMstIDx="";
			foreach($resultPI as $row)
			{
				$piMstIDx.=$row[csf('id')].",";
				$pi_wise_required_array[$row[csf('id')]]['booking_qnty']=$row[csf('pi_qnty')];
			}
			$piMstIDx=chop($piMstIDx,",");

			$piWiseRcv="select a.booking_id,a.receive_basis ,sum(b.receive_qnty) as receive_qnty
			from inv_receive_master a, pro_finish_fabric_rcv_dtls b 
			where a.id=b.mst_id and a.booking_id in($piMstIDx) and a. entry_form=17 and a.receive_basis =1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
			group by a.booking_id,a.receive_basis";

			$piWiseRcvSql = sql_select($piWiseRcv);
			foreach($piWiseRcvSql as $row)
			{
				$pi_wise_recv_array[$row[csf('booking_id')]]['recv_qnty']=$row[csf('receive_qnty')];
			}
		}

	}
	else if($txt_search_by==2 || $txt_search_by==4)
	{

 		// ======================== new add ============
 		if($db_type==0)
 		{
 			$approval_status="select page_id, approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format(date('d-m-Y'),'yyyy-mm-dd')."' and company_id='$company')) and page_id in(5,6) and status_active=1 and is_deleted=0";
 		}
 		else
 		{
 			$approval_status="select page_id, approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format(date('d-m-Y'), "", "",1)."' and company_id='$company')) and page_id in(5,6) and status_active=1 and is_deleted=0";
 		}
 		//echo $approval_status;//die;
 		$approval_status=sql_select($approval_status);
 		$approval_status_cond_main=$approval_status_cond_short="";
 		foreach($approval_status as $row)
 		{
 			if($row[csf("page_id")]==5)
 			{
 				if($row[csf("approval_need")]==1  )
	 			{
	 				//$approval_status_cond_main=" and a.is_approved = 1";
	 				$chkapproval_status_main = 1;

	 			}else{
	 				$chkapproval_status_main = 0;
	 			}
 			}else if($row[csf("page_id")]==6){
 				if( $row[csf("approval_need")]==1)
	 			{
	 				//$approval_status_cond_short=" and a.is_approved = 1";
	 				$chkapproval_status_short = 1;
	 			}else{
	 				$chkapproval_status_short = 0;
	 			}
 			}	
 		}
 		// ======================================== new add

 		if($txt_search_by==2)
 		{
 			$sql = "select a.id, a.booking_type,a.is_short, a.booking_no as wopi_number, a.fabric_source,' ' as lc_number, a.booking_date as wopi_date, a.supplier_id as supplier_id, a.currency_id as currency_id, a.source as source,0 as is_non_ord_sample , 0 as is_pi,a.is_approved, 0 as dtls_table_mst_pi_id,sum(b.fin_fab_qnty) as fin_fab_qnty  
				from wo_booking_mst a,wo_booking_dtls b   
				where 
				a.booking_no=b.booking_no and
				 a.status_active=1 and  a.is_deleted=0 and 
				 b.status_active=1 and  b.is_deleted=0 and
				 a.item_category=3 and  a.pay_mode!=2 and
				 a.company_id=$company
				$sql_cond $booking_cond $year_cond  group by a.id, a.booking_type,a.is_short, a.booking_no, a.fabric_source, a.booking_date, a.supplier_id, a.currency_id, a.source,a.is_approved
				union all
			    SELECT a.id,a.booking_type,a.is_short,a.booking_no as wopi_number,a.fabric_source,' ' as lc_number, a.booking_date as wopi_date,a.supplier_id as supplier_id,a.currency_id as currency_id,a.source as source ,1 as is_non_ord_sample, 0 as is_pi,a.is_approved , 0 as dtls_table_mst_pi_id  ,sum(b.finish_fabric) as fin_fab_qnty 
				from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b
				where
				a.booking_no=b.booking_no and
				a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and
				a.item_category=3 and a.pay_mode!=2 and
				a.company_id=$company $sql_condition_non
				$sql_cond $year_cond  group by  a.id,a.booking_type,a.is_short,a.booking_no,a.fabric_source, a.booking_date,a.supplier_id,a.currency_id,a.source,a.is_approved  order by wopi_date desc";//supplier_id in (select id from lib_supplier where FIND_IN_SET($company,tag_company) )

				if($cbo_search_type==2)
				{
					$resultx = sql_select($sql);
					$booking_wise_recv_array=array();$booking_wise_required_array=array(); $bookingMstIDx="";
					foreach($resultx as $row)
					{
						$bookingMstIDx.=$row[csf('id')].",";
						$booking_wise_required_array[$row[csf('id')]]['booking_qnty']=$row[csf('fin_fab_qnty')];
					}
					$bookingMstIDx=chop($bookingMstIDx,",");

					$bookingWiseRcv="select a.booking_id,a.receive_basis ,sum(b.receive_qnty) as receive_qnty
					from inv_receive_master a, pro_finish_fabric_rcv_dtls b 
					where a.id=b.mst_id and a.booking_id in($bookingMstIDx) and a. entry_form=17 and a.receive_basis =2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
					group by a.booking_id,a.receive_basis";

					$bookingWiseRcvSql = sql_select($bookingWiseRcv);
					foreach($bookingWiseRcvSql as $row)
					{
						$booking_wise_recv_array[$row[csf('booking_id')]]['recv_qnty']=$row[csf('receive_qnty')];
					}
				}

 		}
 		else
 		{
 			$sql = "select a.id, a.booking_type,a.is_short, a.booking_no as wopi_number, a.fabric_source,' ' as lc_number, a.booking_date as wopi_date, a.supplier_id as supplier_id, a.currency_id as currency_id, a.source as source,0 as is_non_ord_sample , 0 as is_pi,a.is_approved, 0 as dtls_table_mst_pi_id 
				from wo_booking_mst a
				where
				 a.status_active=1 and  a.is_deleted=0 and
				 a.item_category=3 and  a.pay_mode!=2 and 
				 a.company_id=$company and a.booking_type!=4 
				$sql_cond $booking_cond $year_cond 
				order by wopi_date desc";//supplier_id in (select id from lib_supplier where FIND_IN_SET($company,tag_company) )
 		}
	}
	//echo $sql;
	// die;
	//echo $pi_cond;die;
	$result = sql_select($sql);
	$woPI_array=array(); $bookingMstID=""; $nonBookingMstID=""; $piMstID="";
	foreach($result as $row)
	{
		//echo $row[csf('is_non_ord_sample')]."**".$row[csf('is_pi')];
		if($row[csf('is_non_ord_sample')]==0 && $row[csf('is_pi')]==0)
		{
			$bookingMstID .= $row[csf('id')].",";
		}
		elseif($row[csf('is_non_ord_sample')]==1 && $row[csf('is_pi')]==0)
		{
			$nonBookingMstID .= $row[csf('id')].",";
		}
		else if($row[csf('is_non_ord_sample')]==0 && $row[csf('is_pi')]==1)
		{
			$piMstID .= $row[csf('id')].",";
			//$bookingMstID .= $row[csf('id')].",";
		}

		$woPI_array[$row[csf('is_non_ord_sample')]][$row[csf('id')]]['wopi_number'] 	= $row[csf('wopi_number')];
		$woPI_array[$row[csf('is_non_ord_sample')]][$row[csf('id')]]['booking_type'] 	= $row[csf('booking_type')];
		$woPI_array[$row[csf('is_non_ord_sample')]][$row[csf('id')]]['is_short'] 		= $row[csf('is_short')];
		$woPI_array[$row[csf('is_non_ord_sample')]][$row[csf('id')]]['is_approved'] 	= $row[csf('is_approved')];
		$woPI_array[$row[csf('is_non_ord_sample')]][$row[csf('id')]]['fabric_source'] 	= $row[csf('fabric_source')];
		$woPI_array[$row[csf('is_non_ord_sample')]][$row[csf('id')]]['lc_number'] 		= $row[csf('lc_number')];
		$woPI_array[$row[csf('is_non_ord_sample')]][$row[csf('id')]]['wopi_date'] 		= change_date_format($row[csf('wopi_date')]);
		$woPI_array[$row[csf('is_non_ord_sample')]][$row[csf('id')]]['supplier_id'] 	= $row[csf('supplier_id')];
		$woPI_array[$row[csf('is_non_ord_sample')]][$row[csf('id')]]['currency_id'] 	= $row[csf('currency_id')];
		$woPI_array[$row[csf('is_non_ord_sample')]][$row[csf('id')]]['source'] 			= $row[csf('source')];
		$woPI_array[$row[csf('is_non_ord_sample')]][$row[csf('id')]]['is_pi'] 			= $row[csf('is_pi')];
		$woPI_array[$row[csf('is_non_ord_sample')]][$row[csf('id')]]['id'] 				= $row[csf('id')];
		$woPI_array[$row[csf('is_non_ord_sample')]][$row[csf('id')]]['is_non_ord_sample']= $row[csf('is_non_ord_sample')];
		$woPI_array[$row[csf('is_non_ord_sample')]][$row[csf('id')]]['dtls_table_mst_pi_id']= $row[csf('dtls_table_mst_pi_id')];
	}

	//echo $bookingMstID; die;
	$nonBookingMstID=chop($nonBookingMstID,",");
	if($nonBookingMstID!='')
	{
		$nonByrStl_sql="select a.id,listagg(a.buyer_id,',') within group (order by a.id) as buyer_id, listagg(b.style_des,',') within group (order by b.id) as style_des,a.grouping from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.item_category=3 and a.pay_mode!=2 and a.id in ($nonBookingMstID)  group by a.id,a.grouping";
		//echo $nonByrStl_sql; die;
		$result_nonByrStl = sql_select($nonByrStl_sql); $nonByrStl_Arr=array();
		foreach($result_nonByrStl as $val)
		{
			$nonByrStl_Arr[$val[csf('id')]]['buyer_name'].=$buyer_arr[$val[csf('buyer_id')]].",";
			$nonByrStl_Arr[$val[csf('id')]]['style_ref_no'].=$val[csf('style_des')].",";
			$nonByrStl_Arr[$val[csf('id')]]['grouping'].=$val[csf('grouping')].",";
		}
	}

	$piMstID=chop($piMstID,",");
	if($piMstID!='')
	{
		$pi_wo_sql="select a.pi_id,a.work_order_id from com_pi_item_details a where a.pi_id in ($piMstID) and a.work_order_id is not null ";
		//echo $pi_wo_sql; die;
		$result_piWo = sql_select($pi_wo_sql); $piByrStl_arr=array();
		foreach($result_piWo as $val)
		{
			$piByrStl_arr[$val[csf('pi_id')]]['work_order_id'] .=$val[csf('work_order_id')].",";
			$booking_ids.=$val[csf("work_order_id")].",";
		}
		//print_r($booking_dtls_dataArr);
		$booking_cond="";
		if($booking_ids!='')
		{
			$booking_ids=rtrim($booking_ids,",");
			$booking_ids=array_chunk(array_unique(explode(",",$booking_ids)),999, true);
			//print_r($booking_ids);
			$ji=0;
		   	foreach($booking_ids as $key=> $value)
		   	{
			   if($ji==0)
			   {
					$booking_cond=" and d.id in(".implode(",",$value).")";
			   }
			   else
			   {
					$booking_cond.=" or d.id in(".implode(",",$value).")";
			   }
			   $ji++;
		   	}
		}
	}
	//echo $booking_cond; die;
	if($buyer==0 && $style=='' && $txt_job=='' && $order=='' && $actual_order_no=='')
 	{
		$bookingMstID=chop($bookingMstID,",");
		$sql_dtls ="select d.id,a.buyer_name,a.style_ref_no,b.po_number,b.id as po_id,b.grouping from wo_po_details_master a, wo_po_break_down b , wo_booking_dtls c  , wo_booking_mst d where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.booking_no=d.booking_no and d.company_id=$company $booking_cond and d.item_category=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 ";
	 		//echo $sql_dtls;
 		$result_dtls = sql_select($sql_dtls); $booking_dtls_dataArr=array();
 		foreach($result_dtls as $val)
		{
			//$booking_ids.=$val[csf("id")].",";
			$booking_dtls_dataArr[$val[csf('id')]]['buyer_name'].=$buyer_arr[$val[csf('buyer_name')]].",";
			$booking_dtls_dataArr[$val[csf('id')]]['style_ref_no'].=$val[csf('style_ref_no')].",";
			$booking_dtls_dataArr[$val[csf('id')]]['grouping'].=$val[csf('grouping')].",";
			$booking_dtls_dataArr[$val[csf('id')]]['po_number'].=$val[csf('po_number')].",";
			$booking_dtls_dataArr[$val[csf('id')]]['act_po_number'].=$act_po_no_arr[$val[csf('po_id')]].",";
			//echo $act_po_no_arr[$val[csf('po_id')]].'DD';
		}
	}
	//print_r($booking_dtls_dataArr);
 	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	if($txt_search_by==1){
		$revBasis = "PI No";
	}else if($txt_search_by==2 || $txt_search_by==4){
		$revBasis = "WO No";
	}else{
		$revBasis = "WO/PI No";
	}
	?>
	<div id="report_container" align="left">
	   	<table style="margin-top:2px" class="rpt_table" border="1" rules="all" width="1190" cellpadding="0" cellspacing="0">
	       <thead>
	           	<tr>
	                <th width="30">SL</th>
	                <th width="120"><? echo $revBasis; ?></th>
	                <th width="70">Basis</th>
	                <th width="120">LC No.</th>
	                <th width="70">Date</th>
	                <th width="150">Supplier</th>
	                <th width="50">Currency</th>
	                <th width="60">Source</th>
	                <th width="100">Buyer</th>
	                <th width="100">Master Style/Internal Ref.</th>
	                <th width="100">Style Ref</th>
	                <th width="110">PO Number</th>
					<th width="110">Actual PO Number</th>
	            </tr>
	        </thead>
	    </table>
	    <div style="width:1210px; overflow-y:scroll; max-height:205px" id="scroll_body" align="left" >
	        <table class="rpt_table" border="1" rules="all" width="1190" cellpadding="0" cellspacing="0" id="list_view">
	        	<? $i=1;
				foreach($woPI_array as $is_sample=>$dtls_arr)
				{
					foreach($dtls_arr as $id=>$row)
					{
						if($row['dtls_table_mst_pi_id']!='') // this condition for not showing PI whose are not find Details table data
						{
							if($txt_search_by==1)
							{
								$piReqr=$pi_wise_required_array[$row['id']]['booking_qnty'];
								$piRecv=$pi_wise_recv_array[$row['id']]['recv_qnty'];
								$pi_wo_balance=$piReqr-$piRecv;
							}
							else if($txt_search_by==2)
							{
								$woReqr=$booking_wise_required_array[$row['id']]['booking_qnty'];
								$woRecv=$booking_wise_recv_array[$row['id']]['recv_qnty'];
								$pi_wo_balance=$woReqr-$woRecv;
							}
							//echo $pi_wo_balance."<br/>";
							
							if($i%2==0)$bgcolor="#E9F3FF";  else $bgcolor="#FFFFFF";
							//echo $chkapproval_status_main;
							$chkApproveStatus = 0;
							if(  $chkapproval_status_main==1 && $row['booking_type']==1 && $row['is_short']==2 && $row['is_approved']!=1  )
							{
								$chkAppbgcolor = "#fcaf9f";
								$chkApproveStatus = 1;
							}else {
								$chkAppbgcolor = $bgcolor;
							}

							if ($chkapproval_status_short==1 && $row['booking_type']==1 && $row['is_short']==1 && $row['is_approved']!=1 )
							{
								$chkAppbgcolor = "#fcaf9f";
								$chkApproveStatus = 1;
							}else{
								$chkAppbgcolor = $bgcolor;
								
							}
							if($cbo_search_type==2)
							{
								if($pi_wo_balance>0)
								{
									?>
									<tbody>
										<tr bgcolor="<? echo $chkAppbgcolor; ?>" onClick="js_set_value('<? echo $row['id'].'_'.$row['wopi_number'].'_'.$row['is_non_ord_sample'].'_'.$row['fabric_source'].'_'.$row['pi_basis_id'].'_'.$chkApproveStatus; ?>')" id="tr_<? echo $i; ?>" style="cursor:pointer;">
						                    <td width="30"><? echo $i; ?></td>
							                <td width="120"><? echo $row['wopi_number']; ?></td>
							                <td width="70"><? echo $pi_basis[$row['pi_basis_id']]; ?></td>
							                <td width="120"><? echo $row['lc_number']; ?></td>
							                <td width="70" align="center"><? echo $row['wopi_date']; ?></td>
							                <td width="150"><? echo $supplier_arr[$row['supplier_id']]; ?></td>
							                <td width="50"><? echo $currency[$row['currency_id']]; ?></td>
							                <td width="60"><? echo $source[$row['source']]; ?></td>
							                <?
							                	if($is_sample==1 && $row['is_pi']==0)
							                	{
							                		?>
							                		<td width="100"><p>
							                		<?
							                			$buyer_name=implode(",",array_unique(explode(",",chop($nonByrStl_Arr[$id]['buyer_name'],","))));
							                		 	echo $buyer_name;
							                		?></p></td>
							                		<td width="100"><p>
							                		<?
							                			$internal_ref_no=implode(",",array_unique(explode(",",chop($nonByrStl_Arr[$id]['grouping'],","))));
							                		 	echo $internal_ref_no;
							                		?></p></td>
							                		<td width="100"><p>
							                		<?
							                			$style_ref_no=implode(",",array_unique(explode(",",chop($nonByrStl_Arr[$id]['style_ref_no'],","))));
							                		 	echo $style_ref_no;
							                		?></p></td>
									                <td width="110">&nbsp;</td>
													 <td width="110">&nbsp;</td>
							                		<?
							                	}
							                	else if($is_sample==0 && $row['is_pi']==0)
							                	{
							                		?>
							                		<td width="100"><p>
							                		<?
							                			$buyer_name=implode(",",array_unique(explode(",",chop($booking_dtls_dataArr[$id]['buyer_name'],","))));
							                		 	echo $buyer_name;
							                		?></p></td>
							                		<td width="100"><p>
							                		<?
							                			$internal_ref_no=implode(",",array_unique(explode(",",chop($booking_dtls_dataArr[$id]['grouping'],","))));
							                		 	echo $internal_ref_no;
							                		?></p></td>
							                		<td width="100"><p>
							                		<?
							                			$style_ref_no=implode(",",array_unique(explode(",",chop($booking_dtls_dataArr[$id]['style_ref_no'],","))));
							                		 	echo $style_ref_no;
							                		?></p></td>
							                		<td width="110"><p>
							                		<?
							                			$po_number=implode(",",array_unique(explode(",",chop($booking_dtls_dataArr[$id]['po_number'],","))));
							                		 	echo $po_number;
							                		?></p></td>
													<td width="110"><p>
							                		<?
							                			$act_po_number=implode(",",array_unique(explode(",",chop($booking_dtls_dataArr[$id]['act_po_number'],","))));
							                		 	echo $act_po_number;
							                		?></p></td>
							                		<?
							                	}
							                	else if($is_sample==0 && $row['is_pi']==1)
							                	{
							                		$wo_ids=array_unique(explode(",",chop($piByrStl_arr[$id]['work_order_id'],",")));
							                		if(count($wo_ids)>0)
							                		{
							                			?>
								                		<td width="100"><p>
								                		<?
								                			foreach($wo_ids as $value)
								                			{
								                				$buyer_name=implode(",",array_unique(explode(",",chop($booking_dtls_dataArr[$value]['buyer_name'],","))));
								                				$style_ref_no=implode(",",array_unique(explode(",",chop($booking_dtls_dataArr[$value]['style_ref_no'],","))));
								                				$internal_ref_no=implode(",",array_unique(explode(",",chop($booking_dtls_dataArr[$value]['grouping'],","))));
								                				$po_number=implode(",",array_unique(explode(",",chop($booking_dtls_dataArr[$value]['po_number'],","))));
																$act_po_number=implode(",",array_unique(explode(",",chop($booking_dtls_dataArr[$value]['act_po_number'],","))));
								                			}
								                		 	echo $buyer_name;
								                		?></p></td>
								                		<td width="100"><p>
								                		<?
								                		 	echo $internal_ref_no;
								                		?></p></td>
								                		<td width="100"><p>
								                		<?
								                		 	echo $style_ref_no;
								                		?></p></td>
								                		<td width="110"><p>
								                		<?
								                		 	echo $po_number;
								                		?></p></td>
														<td width="110"><p>
								                		<?

							                		 	echo $act_po_number;
								                		?></p></td>
							                		<?
							                		}
							                		else
							                		{
							                			?>
							                			<td width="100">&nbsp;</td>
							                			<td width="100">&nbsp;</td>
							                			<td width="100">&nbsp;</td>
							                			<td width="110">&nbsp;</td>
														<td width="110">&nbsp;</td>
							                			<?
							                		}

							                	}
							                ?>
										</tr>
									</tbody>
										<script>
										setFilterGrid('tbl_list_search',-1);
										</script>
										<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
									<?
									$i++;
								}
							}
							else
							{
								?>
								<tbody>
									<tr bgcolor="<? echo $chkAppbgcolor; ?>" onClick="js_set_value('<? echo $row['id'].'_'.$row['wopi_number'].'_'.$row['is_non_ord_sample'].'_'.$row['fabric_source'].'_'.$row['pi_basis_id'].'_'.$chkApproveStatus; ?>')" id="tr_<? echo $i; ?>" style="cursor:pointer;">
					                    <td width="30"><? echo $i; ?></td>
						                <td width="120"><? echo $row['wopi_number']; ?></td>
						                <td width="70"><? echo $pi_basis[$row['pi_basis_id']]; ?></td>
						                <td width="120"><? echo $row['lc_number']; ?></td>
						                <td width="70" align="center"><? echo $row['wopi_date']; ?></td>
						                <td width="150"><? echo $supplier_arr[$row['supplier_id']]; ?></td>
						                <td width="50"><? echo $currency[$row['currency_id']]; ?></td>
						                <td width="60"><? echo $source[$row['source']]; ?></td>
						                <?
						                	if($is_sample==1 && $row['is_pi']==0)
						                	{
						                		?>
						                		<td width="100"><p>
						                		<?
						                			$buyer_name=implode(",",array_unique(explode(",",chop($nonByrStl_Arr[$id]['buyer_name'],","))));
						                		 	echo $buyer_name;
						                		?></p></td>
						                		<td width="100"><p>
						                		<?
						                			$internal_ref_no=implode(",",array_unique(explode(",",chop($nonByrStl_Arr[$id]['grouping'],","))));
						                		 	echo $internal_ref_no;
						                		?></p></td>
						                		<td width="100"><p>
						                		<?
						                			$style_ref_no=implode(",",array_unique(explode(",",chop($nonByrStl_Arr[$id]['style_ref_no'],","))));
						                		 	echo $style_ref_no;
						                		?></p></td>
								                <td width="110">&nbsp;</td>
												 <td width="110">&nbsp;</td>
						                		<?
						                	}
						                	else if($is_sample==0 && $row['is_pi']==0)
						                	{
						                		?>
						                		<td width="100"><p>
						                		<?
						                			$buyer_name=implode(",",array_unique(explode(",",chop($booking_dtls_dataArr[$id]['buyer_name'],","))));
						                		 	echo $buyer_name;
						                		?></p></td>
						                		<td width="100"><p>
						                		<?
						                			$internal_ref_no=implode(",",array_unique(explode(",",chop($booking_dtls_dataArr[$id]['grouping'],","))));
						                		 	echo $internal_ref_no;
						                		?></p></td>
						                		<td width="100"><p>
						                		<?
						                			$style_ref_no=implode(",",array_unique(explode(",",chop($booking_dtls_dataArr[$id]['style_ref_no'],","))));
						                		 	echo $style_ref_no;
						                		?></p></td>
						                		<td width="110"><p>
						                		<?
						                			$po_number=implode(",",array_unique(explode(",",chop($booking_dtls_dataArr[$id]['po_number'],","))));
						                		 	echo $po_number;
						                		?></p></td>
												<td width="110"><p>
						                		<?
						                			$act_po_number=implode(",",array_unique(explode(",",chop($booking_dtls_dataArr[$id]['act_po_number'],","))));
						                		 	echo $act_po_number;
						                		?></p></td>
						                		<?
						                	}
						                	else if($is_sample==0 && $row['is_pi']==1)
						                	{
						                		$wo_ids=array_unique(explode(",",chop($piByrStl_arr[$id]['work_order_id'],",")));
						                		if(count($wo_ids)>0)
						                		{
						                			?>
							                		<td width="100"><p>
							                		<?
							                			foreach($wo_ids as $value)
							                			{
							                				$buyer_name=implode(",",array_unique(explode(",",chop($booking_dtls_dataArr[$value]['buyer_name'],","))));
							                				$style_ref_no=implode(",",array_unique(explode(",",chop($booking_dtls_dataArr[$value]['style_ref_no'],","))));
							                				$internal_ref_no=implode(",",array_unique(explode(",",chop($booking_dtls_dataArr[$value]['grouping'],","))));
							                				$po_number=implode(",",array_unique(explode(",",chop($booking_dtls_dataArr[$value]['po_number'],","))));
															$act_po_number=implode(",",array_unique(explode(",",chop($booking_dtls_dataArr[$value]['act_po_number'],","))));
							                			}
							                		 	echo $buyer_name;
							                		?></p></td>
							                		<td width="100"><p>
							                		<?
							                		 	echo $internal_ref_no;
							                		?></p></td>
							                		<td width="100"><p>
							                		<?
							                		 	echo $style_ref_no;
							                		?></p></td>
							                		<td width="110"><p>
							                		<?
							                		 	echo $po_number;
							                		?></p></td>
													<td width="110"><p>
							                		<?

						                		 	echo $act_po_number;
							                		?></p></td>
						                		<?
						                		}
						                		else
						                		{
						                			?>
						                			<td width="100">&nbsp;</td>
						                			<td width="100">&nbsp;</td>
						                			<td width="100">&nbsp;</td>
						                			<td width="110">&nbsp;</td>
													<td width="110">&nbsp;</td>
						                			<?
						                		}

						                	}
						                ?>
									</tr>
								</tbody>
									<script>
									setFilterGrid('tbl_list_search',-1);
									</script>
									<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
								<?
								$i++;
							}
						}
					}
				}
				?>
	        </table>
	    </div>
	</div>
	<?
	exit();

}

//after select wo/pi number get form data here---------------------------//
if($action=="populate_data_from_wopi_popup")
{
	$ex_data = explode("**",$data);
	$receive_basis = $ex_data[0];
	$wo_pi_ID = $ex_data[1];
	$hidden_is_non_ord_sample=$ex_data[2];
	$wopiNumber=$ex_data[3];
	$basis=$ex_data[4];

	//echo "shajjad_".$receive_basis.'_'.$hidden_is_non_ord_sample;

	if($receive_basis==1 )
	{
		if($db_type==0)
		{
 			$sql = "select b.id as id, b.lc_number as lc_number,a.supplier_id as supplier_id,a.currency_id as currency_id,a.source as source,e.buyer_id from com_pi_master_details a left join com_btb_lc_master_details b on FIND_IN_SET(a.id,b.pi_id) left join com_pi_item_details d on a.id=d.pi_id left join wo_booking_mst e on d.work_order_id=e.id where a.item_category_id = 3 and a.status_active=1 and a.is_deleted=0 and a.id=$wo_pi_ID";
		}
		else
		{
			$sql = "select c.id as id, c.lc_number as lc_number,a.supplier_id as supplier_id,a.currency_id as currency_id,a.source as source,e.buyer_id from com_pi_master_details a left join com_btb_lc_pi b on a.id=b.pi_id left join com_btb_lc_master_details c on b.com_btb_lc_master_details_id=c.id left join com_pi_item_details d on a.id=d.pi_id left join wo_booking_mst e on d.work_order_id=e.id where a.item_category_id = 3 and a.status_active=1 and a.is_deleted=0 and a.id=$wo_pi_ID";
		}
	}
	else if($receive_basis==2 || $receive_basis==4)
	{
		if($hidden_is_non_ord_sample==0)
		{
 			$sql = "select id,'' as lc_number,supplier_id as supplier_id,currency_id as currency_id,source as source,buyer_id
				from wo_booking_mst
				where
				status_active=1 and is_deleted=0 and
				item_category = 3 and
				id=$wo_pi_ID";
		}
		else
		{
			$sql = "select id,'' as lc_number,supplier_id as supplier_id,currency_id as currency_id,source as source,buyer_id
				from wo_non_ord_samp_booking_mst
				where
				status_active=1 and is_deleted=0 and
				item_category = 3 and
				id=$wo_pi_ID";
		}
	}

	//echo $sql;die;
	$result = sql_select($sql);
	foreach($result as $row)
	{

		//echo $row[csf("supplier_id")];
		echo "$('#cbo_supplier').val(".$row[csf("supplier_id")].");\n";
		echo "$('#cbo_currency').val(".$row[csf("currency_id")].");\n";
		echo "$('#cbo_source').val(".$row[csf("source")].");\n";
		echo "$('#txt_lc_no').val('".$row[csf("lc_number")]."');\n";
		echo "$('#cbo_buyer_name').val('".$row[csf("buyer_id")]."');\n";

		if($row[csf("lc_number")]!="")
		{
			echo "$('#hidden_lc_id').val(".$row[csf("id")].");\n";
		}
		if($row[csf("currency_id")]==1)
		{
			echo "$('#txt_exchange_rate').val(1);\n";
			//echo "$('#txt_exchange_rate').attr('disabled','disabled');\n";
		}
		if($row[csf("currency_id")]!=1)
		{
			$sql1 = sql_select("select exchange_rate,max(id) from inv_receive_master where item_category=3");
			foreach($sql1 as $row1)
			{
				echo "$('#txt_exchange_rate').val(".$row1[csf("exchange_rate")].");\n";
			}

			//echo "$('#txt_exchange_rate').removeAttr('disabled','disabled');\n";
		}
		if($basis == 2 && $row[csf("buyer_id")] == ''){
			echo "$('#cbo_buyer_name').removeAttr('disabled','disabled');\n";
		}
		else{
			echo "$('#cbo_buyer_name').attr('disabled','disabled');\n";
		}
	}

	if($hidden_is_non_ord_sample==1)
		{
			echo "$('#txt_receive_qty').removeAttr('readonly','readonly');\n";
			echo "$('#txt_receive_qty').removeAttr('onClick','onClick');\n";
			echo "$('#txt_receive_qty').removeAttr('placeholder','placeholder');\n";
		}
		else
		{
			echo "$('#txt_receive_qty').attr('readonly','readonly');\n";
			echo "$('#txt_receive_qty').attr('onClick','openmypage_po();');\n";
			echo "$('#txt_receive_qty').attr('placeholder','Single Click');\n";
		}
	exit();

}

if($action=="set_exchange_rate")
{
	//$sql1 = sql_select("select exchange_rate,max(id) from inv_receive_master where item_category=3");
	$sql1 = sql_select("select max(exchange_rate) as exchange_rate, max(id) from inv_receive_master where item_category=3");
	foreach($sql1 as $row1)
	{
		echo $row1[csf("exchange_rate")];
	}
}

//right side product list create here--------------------//
if($action=="show_product_listview")
{
	die;
	$ex_data = explode("**",$data);
	$receive_basis = $ex_data[0];
	$wo_pi_ID = $ex_data[1];
	$hidden_is_non_ord_sample=$ex_data[2];
	$wopiNumber = str_replace(' ', '', $ex_data[3]);

 	$composition_arr=array();
 	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from  lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	if(count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
			}
		}
	}

	if($receive_basis==1) // pi basis
	{
		$sql = "SELECT a.determination_id as lib_yarn_count_deter_id, a.uom,a.fab_weight as gsm, a.dia_width, a.color_id, sum(a.quantity) as qnty, sum(a.quantity*a.rate) as amount, b.pi_basis_id, a.work_order_no, a.work_order_id, a.body_part_id from com_pi_item_details a,com_pi_master_details b  where b.id=a.pi_id and a.pi_id=$wo_pi_ID and a.status_active=1 and a.is_deleted=0 and b.pi_basis_id=1 and b.goods_rcv_status !=1  group by a.determination_id, a.color_id, a.dia_width, a.uom, a.fab_weight,b.pi_basis_id,a.work_order_no, a.work_order_id, a.body_part_id";

		$ref_info_sql = sql_select("SELECT a.determination_id as lib_yarn_count_deter_id,a.uom,a.fab_weight as gsm, a.dia_width, a.color_id, a.work_order_id, d.style_ref_no 
		from com_pi_item_details a, wo_booking_dtls b, wo_po_break_down c, wo_po_details_master d 
		where a.pi_id='$wo_pi_ID' and a.work_order_no=b.booking_no and b.po_break_down_id=c.id and d.job_no=c.job_no_mst and 
		a.dia_width=b.dia_width and a.color_id=b.fabric_color_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and d.status_active=1 and d.is_deleted=0
		group by a.determination_id ,a.uom,a.fab_weight, a.dia_width, a.color_id, a.work_order_id, d.style_ref_no ");

		$style_ref_no_arr = array();
		foreach($ref_info_sql as $row)
		{
			$style_ref_no_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('work_order_id')]]['style_ref_no']=$row[csf('style_ref_no')];
		}
		unset($ref_info_sql);


		/* $determination_info_sql = sql_select("SELECT a.determination_id as lib_yarn_count_deter_id, a.uom,a.fab_weight as gsm, a.dia_width, a.color_id, a.work_order_id,e.rd_no,e.fabric_ref  from com_pi_item_details a,lib_yarn_count_determina_mst e where a.determination_id=e.id and a.pi_id=$wo_pi_ID and a.status_active=1 and a.is_deleted=0 group by a.determination_id, a.color_id, a.dia_width, a.uom, a.fab_weight,a.work_order_no, a.work_order_id ,e.rd_no,e.fabric_ref");
		foreach($determination_info_sql as $row)
		{
			$determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('work_order_id')]]['rd_no']=$row[csf('rd_no')];
			$determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('work_order_id')]]['fabric_ref']=$row[csf('fabric_ref')];
		} */



		$determination_info_sql =sql_select("SELECT a.determination_id as lib_yarn_count_deter_id, a.uom, a.fab_weight as gsm_weight, a.dia_width,a.color_id as fabric_color_id, a.work_order_id, e.body_part_id,f.fabric_ref,f.rd_no,e.gsm_weight_type as weight_type,g.item_size as cutable_width from com_pi_item_details a, wo_booking_dtls c,wo_pre_cost_fabric_cost_dtls e join  wo_pre_cos_fab_co_avg_con_dtls g on e.id=g.pre_cost_fabric_cost_dtls_id,lib_yarn_count_determina_mst f where a.work_order_no=c.booking_no and c.pre_cost_fabric_cost_dtls_id=e.id and e.lib_yarn_count_deter_id=f.id and  a.determination_id = e.lib_yarn_count_deter_id and a.color_id= c.fabric_color_id and a.pi_id=$wo_pi_ID  and a.status_active=1 and a.is_deleted=0   
			group by a.determination_id , a.uom, a.fab_weight , a.dia_width,a.color_id, a.work_order_id, e.body_part_id,f.fabric_ref,f.rd_no,e.gsm_weight_type,g.item_size");

		foreach($determination_info_sql as $row)
		{
			$determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('fabric_color_id')]][$row[csf('work_order_id')]][$row[csf('body_part_id')]]['rd_no']=$row[csf('rd_no')];
			$determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('fabric_color_id')]][$row[csf('work_order_id')]][$row[csf('body_part_id')]]['fabric_ref']=$row[csf('fabric_ref')];
			$determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('fabric_color_id')]][$row[csf('work_order_id')]][$row[csf('body_part_id')]]['weight_type']=$row[csf('weight_type')];
			$determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('fabric_color_id')]][$row[csf('work_order_id')]][$row[csf('body_part_id')]]['cutable_width']=$row[csf('cutable_width')];
		}


		$previous_recv_by_wo=sql_select("select a.id,b.body_part_id,b.fabric_description_id,b.original_gsm,b.original_width,b.gsm,b.width,b.color_id ,sum( b.receive_qnty) as receive_qnty,b.booking_id from inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form=17 and a.item_category=3 and a.receive_basis=1 and a.booking_id=$wo_pi_ID group by a.id,b.body_part_id,b.fabric_description_id,b.original_gsm,b.original_width,b.gsm,b.width,b.color_id,b.booking_id");
			foreach($previous_recv_by_wo as $row)
			{
				$prev_recv_qnty_arr[$row[csf('body_part_id')]][$row[csf('fabric_description_id')]][$row[csf('original_gsm')]][$row[csf('original_width')]][$row[csf('color_id')]][$row[csf('booking_id')]]+=$row[csf('receive_qnty')];


				$prev_recv_data_arr[$row[csf('id')]][$row[csf('body_part_id')]][$row[csf('fabric_description_id')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]]['original_gsm']=$row[csf('original_gsm')];
				$prev_recv_data_arr[$row[csf('id')]][$row[csf('body_part_id')]][$row[csf('fabric_description_id')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]]['original_width']=$row[csf('original_width')];
			}
			$recv_rtrnQnty=sql_select("select a.received_id,b.body_part_id,d.detarmination_id,b.width_editable,b.weight_editable,d.color,sum(b.cons_quantity) as cons_quantity from inv_issue_master a ,inv_transaction b,pro_batch_create_mst  c, product_details_master d where a.id=b.mst_id and  b.pi_wo_batch_no=c.id  and b.prod_id=d.id and a.entry_form=202 and a.item_category=3 and a.booking_id=$wo_pi_ID  group by a.received_id,b.body_part_id,d.detarmination_id,b.width_editable,b.weight_editable,d.color");
			foreach($recv_rtrnQnty as $row)
			{
				$getOrgGsm=$prev_recv_data_arr[$row[csf('received_id')]][$row[csf('body_part_id')]][$row[csf('detarmination_id')]][$row[csf('weight_editable')]][$row[csf('width_editable')]][$row[csf('color')]]['original_gsm'];
				$getOrgWidth=$prev_recv_data_arr[$row[csf('received_id')]][$row[csf('body_part_id')]][$row[csf('detarmination_id')]][$row[csf('weight_editable')]][$row[csf('width_editable')]][$row[csf('color')]]['original_width'];

				$recv_rtn_qnty_arr[$row[csf('body_part_id')]][$row[csf('detarmination_id')]][$getOrgGsm][$getOrgWidth][$row[csf('color')]]+=$row[csf('cons_quantity')];
			}
	}
	else if($receive_basis==2 || $receive_basis==4) // wo basis
	{
		if($hidden_is_non_ord_sample==0)
		{
			$sql="SELECT b.body_part_id,b.lib_yarn_count_deter_id,b.uom, b.gsm_weight, a.booking_no as wopi_number, a.dia_width, a.fabric_color_id as color_id, sum(a.fin_fab_qnty) as qnty,sum(a.grey_fab_qnty*a.rate) as amount from wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b  where a.pre_cost_fabric_cost_dtls_id=b.id  and a.booking_no='$wopiNumber' and a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 group by b.body_part_id,a.booking_no, b.lib_yarn_count_deter_id,b.gsm_weight, a.dia_width, a.fabric_color_id,b.uom";

			$determination_info_sql =sql_select("SELECT b.body_part_id,b.lib_yarn_count_deter_id, b.gsm_weight,b.uom, a.dia_width, a.booking_no as wopi_number, a.fabric_color_id as color_id,c.fabric_ref,c.rd_no,b.gsm_weight_type as weight_type,g.item_size as cutable_width from wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b join  wo_pre_cos_fab_co_avg_con_dtls g on b.id=g.pre_cost_fabric_cost_dtls_id ,lib_yarn_count_determina_mst c  where a.pre_cost_fabric_cost_dtls_id=b.id and b.lib_yarn_count_deter_id=c.id and a.booking_no='$wopiNumber' and a.status_active=1 and a.is_deleted=0 group by b.body_part_id,b.lib_yarn_count_deter_id, b.gsm_weight,b.uom,a.dia_width, a.booking_no, a.fabric_color_id,c.fabric_ref,c.rd_no,b.gsm_weight_type,g.item_size");
			foreach($determination_info_sql as $row)
			{
				$determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('wopi_number')]][$row[csf('body_part_id')]]['rd_no']=$row[csf('rd_no')];
				$determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('wopi_number')]][$row[csf('body_part_id')]]['fabric_ref']=$row[csf('fabric_ref')];
				$determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('wopi_number')]][$row[csf('body_part_id')]]['weight_type']=$row[csf('weight_type')];
				$determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('wopi_number')]][$row[csf('body_part_id')]]['cutable_width']=$row[csf('cutable_width')];
			}


			$previous_recv_by_wo=sql_select("select a.id,b.body_part_id,b.fabric_description_id,b.original_gsm,b.original_width,b.gsm,b.width,b.color_id ,sum( b.receive_qnty) as receive_qnty from inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form=17 and a.item_category=3 and a.receive_basis=2 and a.booking_id=$wo_pi_ID and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id,b.body_part_id,b.fabric_description_id,b.original_gsm,b.original_width,b.gsm,b.width,b.color_id");
			foreach($previous_recv_by_wo as $row)
			{
				$prev_recv_qnty_arr[$row[csf('body_part_id')]][$row[csf('fabric_description_id')]][$row[csf('original_gsm')]][$row[csf('original_width')]][$row[csf('color_id')]]+=$row[csf('receive_qnty')];


				$prev_recv_data_arr[$row[csf('id')]][$row[csf('body_part_id')]][$row[csf('fabric_description_id')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]]['original_gsm']=$row[csf('original_gsm')];
				$prev_recv_data_arr[$row[csf('id')]][$row[csf('body_part_id')]][$row[csf('fabric_description_id')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('color_id')]]['original_width']=$row[csf('original_width')];
			}

			$recv_rtrnQnty=sql_select("select a.received_id,b.body_part_id,d.detarmination_id,b.width_editable,b.weight_editable,d.color,sum(b.cons_quantity) as cons_quantity from inv_issue_master a ,inv_transaction b,pro_batch_create_mst  c, product_details_master d where a.id=b.mst_id and  b.pi_wo_batch_no=c.id  and b.prod_id=d.id and a.entry_form=202 and a.item_category=3 and c.booking_no_id=$wo_pi_ID  group by a.received_id,b.body_part_id,d.detarmination_id,b.width_editable,b.weight_editable,d.color");
			foreach($recv_rtrnQnty as $row)
			{
				$getOrgGsm=$prev_recv_data_arr[$row[csf('received_id')]][$row[csf('body_part_id')]][$row[csf('detarmination_id')]][$row[csf('weight_editable')]][$row[csf('width_editable')]][$row[csf('color')]]['original_gsm'];
				$getOrgWidth=$prev_recv_data_arr[$row[csf('received_id')]][$row[csf('body_part_id')]][$row[csf('detarmination_id')]][$row[csf('weight_editable')]][$row[csf('width_editable')]][$row[csf('color')]]['original_width'];

				$recv_rtn_qnty_arr[$row[csf('body_part_id')]][$row[csf('detarmination_id')]][$getOrgGsm][$getOrgWidth][$row[csf('color')]]+=$row[csf('cons_quantity')];
			}
		}
		else
		{
			//27-May-2021 qnty source is changed after consulting with mr. rashel and mr. jahid hasan vai
		 	$sql="SELECT body_part, booking_no as wopi_number, lib_yarn_count_deter_id, gsm_weight, dia_width, color_all_data as fabric_color_id, fabric_color, sum(finish_fabric) as qnty,uom, entry_form_id   
		 	from wo_non_ord_samp_booking_dtls
		 	where booking_no='$wopiNumber' and status_active=1 and is_deleted=0 
		 	group by booking_no,lib_yarn_count_deter_id, body_part, gsm_weight, dia_width,color_all_data,fabric_color,uom, entry_form_id"; // color_all_data
		}
	}
	//echo $sql;
	$result = sql_select($sql);
	$color_name_arr=return_library_array( "select id, color_name from lib_color where status_active=1",'id','color_name');
	$lib_body_part_arr=return_library_array("select id, body_part_full_name from lib_body_part", "id", "body_part_full_name");
	$i=1;
	if($receive_basis==1)
	{

		foreach($result as $row)
		{
			if ($i%2==0)$bgcolor="#E9F3FF";
			else $bgcolor="#FFFFFF";
			$fabric_desc=$lib_body_part_arr[$row[csf('body_part_id')]].', '.$composition_arr[$row[csf('lib_yarn_count_deter_id')]];
			$body_part_id=$row[csf('body_part_id')];
			if($receive_basis==1) // pi basis
			{
				$color_id=$row[csf("color_id")];
			}
			else if($receive_basis==2)
			{
				if($hidden_is_non_ord_sample==0)
				{
					$color_id=$row[csf("color_id")];
				}
				else
				{
					$color_id=  explode("_",$row[csf("fabric_color_id")]);
					$color_id=$color_id[2];
				}
			}

			$recvRtnQnty=$recv_rtn_qnty_arr[$body_part_id][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$color_id];
			$cumulative_recvQnty=$prev_recv_qnty_arr[$body_part_id][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$color_id][$row[csf('work_order_id')]]-$recvRtnQnty;

			$balanceQnty=($row[csf('qnty')]-$cumulative_recvQnty);


			$rd_no= $determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('work_order_id')]][$body_part_id]['rd_no'];
			$fabric_ref= $determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('work_order_id')]][$body_part_id]['fabric_ref'];
			$weight_type = $determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('work_order_id')]][$body_part_id]['weight_type'];
			$cutable_width = $determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('work_order_id')]][$body_part_id]['cutable_width'];

			$style_ref_no = $style_ref_no_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('work_order_id')]]['style_ref_no'];

			
			$avgRate=$row[csf("amount")]/$row[csf("qnty")];
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick='change_color("<?php echo $i; ?>","#E9F3FF");'>
				<td width="80"><? echo $body_part[$body_part_id]; ?></td>
				<td width="120" class="wrap_break"><? echo $fabric_desc; ?></td>
				<td width="80" align="left"><p class="wrap_break"><? echo $fabric_ref; ?></p></td>
				<td width="80" align="left"><p class="wrap_break"><? echo $rd_no; ?></p></td>
				<td width="80"><p class="wrap_break"><? echo $color_name_arr[$color_id]; ?></p></td>
				<td width="80"><? echo $row[csf("gsm")]; ?>&nbsp;</td>
				<td width="80"><? echo $fabric_weight_type[$weight_type]; ?>&nbsp;</td>
				<td width="80"><? echo $unit_of_measurement[$row[csf("uom")]]; ?>&nbsp;</td>
				<td width="80"><? echo $row[csf("dia_width")]; ?></td>
				<td width="80"><? echo $cutable_width; ?>&nbsp;</td>
				<td width="80" class="wrap_break"><? //echo 'batch lot'; ?>&nbsp;</td>
				<td width="80">
					<input type="text" id="recvQty_<? echo $i; ?>" class="text_boxes"  style="width:50px" name="recvQty[]" onDblClick="openmypage_po(<? echo $i; ?>)" readonly/>
					<input type="hidden" id="rollData_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="rollData[]"  />
				</td>
				<td width="80">
					<input type="text" id="txtRoll_<? echo $i; ?>" class="text_boxes"  style="width:50px" name="txtRoll[]" readonly disabled/>
				</td>
				<td width="80" align="right">
					<input type="text" value="<? echo number_format($avgRate,2); ?>" id="txtRate_<? echo $i; ?>" class="text_boxes_numeric"  style="width:55px" name="txtRate[]" readonly disabled/>
				</td>
				<td width="80" align="right">
					<input type="text" id="txtAmount_<? echo $i; ?>" class="text_boxes_numeric"  style="width:60px" name="txtAmount[]" readonly disabled/>
				</td>
				<td width="80" align="right">
					<input type="text" value="" id="txtBlaOrderQty_<? echo $i; ?>" class="text_boxes_numeric"  style="width:60px" name="txtBlaOrderQty[]" readonly disabled/>
				</td>
				<td width="80" align="right">
					<input type="text" value="" id="txtBookCurrency_<? echo $i; ?>" class="text_boxes_numeric"  style="width:60px" name="txtBookCurrency[]" readonly disabled/>
				</td>
				<td width="80" align="right"><input type="text" value="" id="txtIle_<? echo $i; ?>" class="text_boxes_numeric"  style="width:55px" name="txtIle[]" readonly disabled/></td>

				<td width="80" align="center" id="floorTd_<? echo $i; ?>" class="floor_td_to">
					<? 
					$argument = "'".$i.'_0'."'";					
					echo create_drop_down( "cboFloor_".$i, 60,$blank_array,"", 1, "--Select--", 0, "fn_load_room(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'cboFloor');",0,"","","","","","","cboFloor[]" ,"onchange_void"); ?>
				</td>
				<td width="80" align="center" id="roomTd_<? echo $i; ?>" >
					<? 
					$argument = "'".$i.'_1'."'";
					echo create_drop_down( "cboRoom_".$i, 60,$blank_array,"", 1, "--Select--", 0, "fn_load_rack(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'cboRoom');",0,"","","","","","","cboRoom[]","onchange_void" ); ?>		            
				</td>
				<td width="80" align="center" id="rackTd_<? echo $i; ?>" >
					<? 
					$argument = "'".$i.'_2'."'";
					echo create_drop_down( "txtRack_".$i, 60,$blank_array,"", 1, "--Select--", 0, "fn_load_shelf(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'txtRack');",0,"","","","","","","txtRack[]","onchange_void" ); ?>
				</td>
				<td width="80" align="center" id="shelfTd_<? echo $i; ?>" >
					<? 
					$argument = "'".$i.'_3'."'";
					echo create_drop_down( "txtShelf_".$i, 60,$blank_array,"", 1, "--Select--", 0, "fn_load_bin(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'txtShelf');",0,"","","","","","","txtShelf[]","onchange_void" ); ?>
				</td>
				<td width="80" align="center" id="binTd_<? echo $i; ?>" >
					<? 
					$argument = "'".$i.'_4'."'";
					echo create_drop_down( "txtBin_".$i, 60,$blank_array,"", 1, "--Select--", 0, "copy_all($argument);",0,"","","","","","","txtBin[]","onchange_void" ); ?>
				</td>
				<td width="80">
					<input type="text" id="txtRemarks_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtRemarks[]" />
					
					<input type="hidden" value="<? echo $body_part_id;?>" id="cboBodyPart_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="cboBodyPart[]" />
					
					<input type="hidden" value="<? echo $row[csf('lib_yarn_count_deter_id')];?>" id="fabricDescId_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="fabricDescId[]" />
					
					<input type="hidden" value="<? echo $fabric_ref;?>" id="txtFabricRef_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtFabricRef[]" />
					
					<input type="hidden" value="<? echo $rd_no;?>" id="txtRDNo_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtRDNo[]" />
					
					<input type="hidden" value="<? echo $color_id;?>" id="txtColor_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtColor[]" />

					<input type="hidden" value="<? echo $row[csf("gsm")];?>" id="txtGsm_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtGsm[]" />
					
					<input type="hidden" value="<? echo $weight_type;?>" id="txtWeightType_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtWeightType[]" />
					
					<input type="hidden" value="<? echo $row[csf("uom")];?>" id="cboUOM_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="cboUOM[]" />

					<input type="hidden" value="<? echo $wo_pi_ID;?>" id="hiddenPIId_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="hiddenPIId[]" />

					<input type="hidden" value="<? echo $row[csf('work_order_no')];?>" id="hdnBookingNo_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="hdnBookingNo[]" />
					<input type="hidden" value="<? echo $row[csf('work_order_id')];?>" id="hdnBookingID_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="hdnBookingID[]" />
					
					<input type="hidden" value="<? echo $row[csf("dia_width")];?>" id="txtDiaWidth_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtDiaWidth[]" />
					<input type="hidden" value="<? echo $cutable_width;?>" id="txtCutableWidth_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtCutableWidth[]" />
					<input type="hidden" value="<? //?>" id="distributionMethodId_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="distributionMethodId[]" />
					<input type="hidden" value="<? //?>" id="txtDeletedId_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtDeletedId[]" />
					<input type="hidden" value="" id="updateDtlsId_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="updateDtlsId[]" />
				</td>				
			</tr>
			<?
			$i++;
		}
	}
	else
	{
		foreach($result as $row)
		{
			if ($i%2==0)$bgcolor="#E9F3FF";
			else $bgcolor="#FFFFFF";

			$fabric_desc=$composition_arr[$row[csf('lib_yarn_count_deter_id')]];
			if($receive_basis==1) // pi basis
			{
				$color_id=$row[csf("color_id")];
			}
			else if($receive_basis==2 || $receive_basis==4)
			{
				if($hidden_is_non_ord_sample==0)
				{
					$color_id=$row[csf("color_id")];
					$body_part_id=$row[csf("body_part_id")];
				}
				else
				{
					if ($row[csf("entry_form_id")]==439)
					{
						$color_id=explode("_",$row[csf("fabric_color_id")]);
						$color_id=$color_id[2];
						$body_part_id=$row[csf("body_part")];
					}
					else
					{
						$color_id=$row[csf("fabric_color")];								
						$body_part_id=$row[csf("body_part")];
					}
				}
			}

			$recvRtnQnty=$recv_rtn_qnty_arr[$body_part_id][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]];

			$cumulative_recvQnty=$prev_recv_qnty_arr[$body_part_id][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]]-$recvRtnQnty;

			$balanceQnty=($row[csf('qnty')]-$cumulative_recvQnty);

			$rd_no=$determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('wopi_number')]][$row[csf('body_part_id')]]['rd_no'];
			$fabric_ref=$determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('wopi_number')]][$row[csf('body_part_id')]]['fabric_ref'];
			$cutable_width=$determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('wopi_number')]][$row[csf('body_part_id')]]['cutable_width'];
			$weight_type=$determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('wopi_number')]][$row[csf('body_part_id')]]['weight_type'];

			$avgRate=$row[csf("amount")]/$row[csf("qnty")];
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick='change_color("<?php echo $i; ?>","#E9F3FF");'>
				<td width="80"><? echo $body_part[$body_part_id]; ?></td>
				<td width="120" class="wrap_break"><? echo $fabric_desc; ?></td>
				<td width="80" align="left"><p class="wrap_break"><? echo $fabric_ref; ?></p></td>
				<td width="80" align="left"><p class="wrap_break"><? echo $rd_no; ?></p></td>
				<td width="80"><p class="wrap_break"><? echo $color_name_arr[$color_id]; ?></p></td>
				<td width="80"><? echo $row[csf("gsm_weight")]; ?>&nbsp;</td>
				<td width="80"><? echo $fabric_weight_type[$weight_type]; ?>&nbsp;</td>
				<td width="80"><? echo $unit_of_measurement[$row[csf("uom")]]; ?>&nbsp;</td>
				<td width="80"><? echo $row[csf("dia_width")]; ?></td>
				<td width="80"><? echo $cutable_width; ?>&nbsp;</td>
				<td width="80" class="wrap_break"><? //echo 'batch lot'; ?>&nbsp;</td>
				<td width="80">
					<input type="text" id="recvQty_<? echo $i; ?>" class="text_boxes"  style="width:50px" name="recvQty[]" onDblClick="openmypage_po(<? echo $i; ?>)" readonly/>
					<input type="hidden" id="rollData_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="rollData[]"  />
				</td>
				<td width="80">
					<input type="text" id="txtRoll_<? echo $i; ?>" class="text_boxes"  style="width:50px" name="txtRoll[]" readonly disabled/>
				</td>
				<td width="80" align="right">
					<input type="text" value="<? echo number_format($avgRate,2); ?>" id="txtRate_<? echo $i; ?>" class="text_boxes_numeric"  style="width:55px" name="txtRate[]" readonly disabled/>
				</td>
				<td width="80" align="right">
					<input type="text" id="txtAmount_<? echo $i; ?>" class="text_boxes_numeric"  style="width:60px" name="txtAmount[]" readonly disabled/>
				</td>
				<td width="80" align="right">
					<input type="text" value="" id="txtBlaOrderQty_<? echo $i; ?>" class="text_boxes_numeric"  style="width:60px" name="txtBlaOrderQty[]" readonly disabled/>
				</td>
				<td width="80" align="right">
					<input type="text" value="" id="txtBookCurrency_<? echo $i; ?>" class="text_boxes_numeric"  style="width:60px" name="txtBookCurrency[]" readonly disabled/>
				</td>
				<td width="80" align="right"><input type="text" value="" id="txtIle_<? echo $i; ?>" class="text_boxes_numeric"  style="width:55px" name="txtIle[]" readonly disabled/></td>

				<td width="80" align="center" id="floorTd_<? echo $i; ?>" class="floor_td_to">
					<? 
					$argument = "'".$i.'_0'."'";					
					echo create_drop_down( "cboFloor_".$i, 60,$blank_array,"", 1, "--Select--", 0, "fn_load_room(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'cboFloor');",0,"","","","","","","cboFloor[]" ,"onchange_void"); ?>
				</td>
				<td width="80" align="center" id="roomTd_<? echo $i; ?>" >
					<? 
					$argument = "'".$i.'_1'."'";
					echo create_drop_down( "cboRoom_".$i, 60,$blank_array,"", 1, "--Select--", 0, "fn_load_rack(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'cboRoom');",0,"","","","","","","cboRoom[]","onchange_void" ); ?>		            
				</td>
				<td width="80" align="center" id="rackTd_<? echo $i; ?>" >
					<? 
					$argument = "'".$i.'_2'."'";
					echo create_drop_down( "txtRack_".$i, 60,$blank_array,"", 1, "--Select--", 0, "fn_load_shelf(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'txtRack');",0,"","","","","","","txtRack[]","onchange_void" ); ?>
				</td>
				<td width="80" align="center" id="shelfTd_<? echo $i; ?>" >
					<? 
					$argument = "'".$i.'_3'."'";
					echo create_drop_down( "txtShelf_".$i, 60,$blank_array,"", 1, "--Select--", 0, "fn_load_bin(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'txtShelf');",0,"","","","","","","txtShelf[]","onchange_void" ); ?>
				</td>
				<td width="80" align="center" id="binTd_<? echo $i; ?>" >
					<? 
					$argument = "'".$i.'_4'."'";
					echo create_drop_down( "txtBin_".$i, 60,$blank_array,"", 1, "--Select--", 0, "copy_all($argument);",0,"","","","","","","txtBin[]","onchange_void" ); ?>
				</td>
				<td width="80">
					<input type="text" id="txtRemarks_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtRemarks[]" />
					
					<input type="hidden" value="<? echo $body_part_id;?>" id="cboBodyPart_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="cboBodyPart[]" />
					
					<input type="hidden" value="<? echo $row[csf('lib_yarn_count_deter_id')];?>" id="fabricDescId_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="fabricDescId[]" />
					
					<input type="hidden" value="<? echo $fabric_ref;?>" id="txtFabricRef_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtFabricRef[]" />
					
					<input type="hidden" value="<? echo $rd_no;?>" id="txtRDNo_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtRDNo[]" />
					
					<input type="hidden" value="<? echo $color_id;?>" id="txtColor_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtColor[]" />

					<input type="hidden" value="<? echo $row[csf("gsm_weight")];?>" id="txtGsm_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtGsm[]" />
					
					<input type="hidden" value="<? echo $weight_type;?>" id="txtWeightType_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtWeightType[]" />
					
					<input type="hidden" value="<? echo $row[csf("uom")];?>" id="cboUOM_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="cboUOM[]" />

					<input type="hidden" value="" id="hiddenPIId_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="hiddenPIId[]" />

					<input type="hidden" value="" id="hdnBookingNo_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="hdnBookingNo[]" />
					<input type="hidden" value="" id="hdnBookingID_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="hdnBookingID[]" />
					
					<input type="hidden" value="<? echo $row[csf("dia_width")];?>" id="txtDiaWidth_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtDiaWidth[]" />
					<input type="hidden" value="<? echo $cutable_width;?>" id="txtCutableWidth_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtCutableWidth[]" />
					<input type="hidden" value="<? //?>" id="distributionMethodId_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="distributionMethodId[]" />
					<input type="hidden" value="<? //?>" id="txtDeletedId_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtDeletedId[]" />
					<input type="hidden" value="" id="updateDtlsId_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="updateDtlsId[]" />
					
				</td>				
			</tr>
		<?
			$i++;
		}

	}
	exit();
}


if($action=="show_product_listview_update")
{
	$ex_data = explode("**",$data);
	$receive_basis = $ex_data[0];
	$wo_pi_ID = $ex_data[1];
	$hidden_is_non_ord_sample=$ex_data[2];
	$wopiNumber = str_replace(' ', '', $ex_data[3]);
	$update_id =  $ex_data[4];

 	$composition_arr=array();
 	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from  lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	if(count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
			}
		}
	}

	if($receive_basis==1) // pi basis
	{
		$sql = "SELECT a.determination_id as lib_yarn_count_deter_id, a.uom,a.fab_weight as gsm, a.dia_width, a.color_id, sum(a.quantity) as qnty, b.pi_basis_id, a.work_order_no, a.work_order_id, a.body_part_id from com_pi_item_details a,com_pi_master_details b  where b.id=a.pi_id and a.pi_id=$wo_pi_ID and a.status_active=1 and a.is_deleted=0 group by a.determination_id, a.color_id, a.dia_width, a.uom, a.fab_weight,b.pi_basis_id,a.work_order_no, a.work_order_id, a.body_part_id";

		$ref_info_sql = sql_select("SELECT a.determination_id as lib_yarn_count_deter_id,a.uom,a.fab_weight as gsm, a.dia_width, a.color_id, a.work_order_id, d.style_ref_no 
		from com_pi_item_details a, wo_booking_dtls b, wo_po_break_down c, wo_po_details_master d 
		where a.pi_id='$wo_pi_ID' and a.work_order_no=b.booking_no and b.po_break_down_id=c.id and d.job_no=c.job_no_mst and 
		a.dia_width=b.dia_width and a.color_id=b.fabric_color_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  and d.status_active=1 and d.is_deleted=0
		group by a.determination_id ,a.uom,a.fab_weight, a.dia_width, a.color_id, a.work_order_id, d.style_ref_no ");

		$style_ref_no_arr = array();
		foreach($ref_info_sql as $row)
		{
			$style_ref_no_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('work_order_id')]]['style_ref_no']=$row[csf('style_ref_no')];
		}
		unset($ref_info_sql);


		$determination_info_sql =sql_select("SELECT a.determination_id as lib_yarn_count_deter_id, a.uom, a.fab_weight as gsm_weight, a.dia_width,a.color_id as fabric_color_id, a.work_order_id, e.body_part_id,f.fabric_ref,f.rd_no,e.gsm_weight_type as weight_type,g.item_size as cutable_width from com_pi_item_details a, wo_booking_dtls c,wo_pre_cost_fabric_cost_dtls e join  wo_pre_cos_fab_co_avg_con_dtls g on e.id=g.pre_cost_fabric_cost_dtls_id,lib_yarn_count_determina_mst f where a.work_order_no=c.booking_no and c.pre_cost_fabric_cost_dtls_id=e.id and e.lib_yarn_count_deter_id=f.id and  a.determination_id = e.lib_yarn_count_deter_id and a.color_id= c.fabric_color_id and a.pi_id=$wo_pi_ID  and a.status_active=1 and a.is_deleted=0   
			group by a.determination_id , a.uom, a.fab_weight , a.dia_width,a.color_id, a.work_order_id, e.body_part_id,f.fabric_ref,f.rd_no,e.gsm_weight_type,g.item_size");

		foreach($determination_info_sql as $row)
		{
			$determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('fabric_color_id')]][$row[csf('work_order_id')]][$row[csf('body_part_id')]]['rd_no']=$row[csf('rd_no')];
			$determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('fabric_color_id')]][$row[csf('work_order_id')]][$row[csf('body_part_id')]]['fabric_ref']=$row[csf('fabric_ref')];
			$determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('fabric_color_id')]][$row[csf('work_order_id')]][$row[csf('body_part_id')]]['weight_type']=$row[csf('weight_type')];
			$determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('fabric_color_id')]][$row[csf('work_order_id')]][$row[csf('body_part_id')]]['cutable_width']=$row[csf('cutable_width')];
		}


		$previous_recv_by_wo=sql_select("SELECT a.id as update_id, b.id as dtls_id, b.mst_id, b.wo_pi_id, b.wo_pi_no, b.item_category_id, b.entry_form, b.body_part_id, b.fabric_description_id,b.fabric_ref,b.rd_no,b.weight_type,b.width, b.cutable_width, b.color_name, b.gsm, b.uom, b.parking_quantity, b.rate, b.amount, b.no_of_roll, b.book_currency, b.ile,b.store_id,b.floor_id,b.room,b.rack,b.self, b.bin_box, b.remarks, b.inserted_by, b.insert_date, b.status_active, b.is_deleted, c.id as roll_table_id, c.barcode_no, c.roll_no, c.qnty, c.po_breakdown_id, c.rf_id, b.job_no, a.booking_no
			from inv_receive_master a, quarantine_parking_dtls b, pro_roll_details c
			where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id and b.item_category_id=3 and a.booking_id=$wo_pi_ID and a.receive_basis=1 and a.entry_form=559 and c.entry_form=559 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");

		foreach($previous_recv_by_wo as $row)
		{
			
			if($update_id == $row[csf('update_id')])
			{
				$this_recv_qnty_arr[$row[csf('body_part_id')]][$row[csf('fabric_description_id')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('uom')]][$row[csf('color_name')]]['qnty']+=$row[csf('qnty')];
				$this_recv_qnty_arr[$row[csf('body_part_id')]][$row[csf('fabric_description_id')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('uom')]][$row[csf('color_name')]]['rate']=$row[csf('rate')];
				$this_recv_qnty_arr[$row[csf('body_part_id')]][$row[csf('fabric_description_id')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('uom')]][$row[csf('color_name')]]['amount']=$row[csf('amount')];

				$this_recv_qnty_arr[$row[csf('body_part_id')]][$row[csf('fabric_description_id')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('uom')]][$row[csf('color_name')]]['no_of_roll']=$row[csf('no_of_roll')];
				$this_recv_qnty_arr[$row[csf('body_part_id')]][$row[csf('fabric_description_id')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('uom')]][$row[csf('color_name')]]['book_currency']=$row[csf('book_currency')];
				$this_recv_qnty_arr[$row[csf('body_part_id')]][$row[csf('fabric_description_id')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('uom')]][$row[csf('color_name')]]['ile']=$row[csf('ile')];
				$this_recv_qnty_arr[$row[csf('body_part_id')]][$row[csf('fabric_description_id')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('uom')]][$row[csf('color_name')]]['remarks']=$row[csf('remarks')];
				$this_recv_qnty_arr[$row[csf('body_part_id')]][$row[csf('fabric_description_id')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('uom')]][$row[csf('color_name')]]['dtls_id']=$row[csf('dtls_id')];


				$save_string = $row[csf('po_breakdown_id')] . "**" . $row[csf('job_no')] . "**" . $row[csf('booking_no')] . "**" . $row[csf('qnty')] . "**" . $row[csf('roll_no')] . "**" . "" . "**" . $row[csf('roll_table_id')] . "**" . $row[csf('barcode_no')] . "**" . $row[csf('rf_id')];


				$this_recv_qnty_arr[$row[csf('body_part_id')]][$row[csf('fabric_description_id')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('uom')]][$row[csf('color_name')]]['remarks']=$row[csf('remarks')];

				$this_recv_qnty_arr[$row[csf('body_part_id')]][$row[csf('fabric_description_id')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('uom')]][$row[csf('color_name')]]['roll_data'] .=$save_string.",";

			}
			else
			{
				$prev_recv_qnty_arr[$row[csf('body_part_id')]][$row[csf('fabric_description_id')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('uom')]][$row[csf('color_name')]]['qnty']+=$row[csf('qnty')];
			}

		}
	}
	else if($receive_basis==2 || $receive_basis==4) // wo basis
	{
		if($hidden_is_non_ord_sample==0)
		{
			$sql="SELECT b.body_part_id,b.lib_yarn_count_deter_id,b.uom, b.gsm_weight, a.booking_no as wopi_number, a.dia_width, a.fabric_color_id as color_id, sum(a.fin_fab_qnty) as qnty,sum(a.grey_fab_qnty*a.rate) as amount from wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b  where a.pre_cost_fabric_cost_dtls_id=b.id  and a.booking_no='$wopiNumber' and a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 group by b.body_part_id,a.booking_no, b.lib_yarn_count_deter_id,b.gsm_weight, a.dia_width, a.fabric_color_id,b.uom";

			$determination_info_sql =sql_select("SELECT b.body_part_id,b.lib_yarn_count_deter_id, b.gsm_weight,b.uom, a.dia_width, a.booking_no as wopi_number, a.fabric_color_id as color_id,c.fabric_ref,c.rd_no,b.gsm_weight_type as weight_type,g.item_size as cutable_width from wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b join  wo_pre_cos_fab_co_avg_con_dtls g on b.id=g.pre_cost_fabric_cost_dtls_id ,lib_yarn_count_determina_mst c  where a.pre_cost_fabric_cost_dtls_id=b.id and b.lib_yarn_count_deter_id=c.id and a.booking_no='$wopiNumber' and a.status_active=1 and a.is_deleted=0 group by b.body_part_id,b.lib_yarn_count_deter_id, b.gsm_weight,b.uom,a.dia_width, a.booking_no, a.fabric_color_id,c.fabric_ref,c.rd_no,b.gsm_weight_type,g.item_size");
			foreach($determination_info_sql as $row)
			{
				$determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('wopi_number')]][$row[csf('body_part_id')]]['rd_no']=$row[csf('rd_no')];
				$determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('wopi_number')]][$row[csf('body_part_id')]]['fabric_ref']=$row[csf('fabric_ref')];
				$determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('wopi_number')]][$row[csf('body_part_id')]]['weight_type']=$row[csf('weight_type')];
				$determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('wopi_number')]][$row[csf('body_part_id')]]['cutable_width']=$row[csf('cutable_width')];
			}

			$previous_recv_by_wo=sql_select("SELECT a.id as update_id, b.id as dtls_id, b.mst_id, b.wo_pi_id, b.wo_pi_no, b.item_category_id, b.entry_form, b.body_part_id, b.fabric_description_id,b.fabric_ref,b.rd_no,b.weight_type,b.width, b.cutable_width, b.color_name, b.gsm, b.uom, b.parking_quantity, b.rate, b.amount, b.no_of_roll, b.book_currency, b.ile,b.store_id,b.floor_id,b.room,b.rack,b.self, b.bin_box, b.remarks, b.inserted_by, b.insert_date, b.status_active, b.is_deleted, c.id as roll_table_id, c.barcode_no, c.roll_no, c.qnty, c.po_breakdown_id, c.rf_id, b.job_no, a.booking_no
			from inv_receive_master a, quarantine_parking_dtls b, pro_roll_details c
			where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id and b.item_category_id=3 and a.receive_basis=2 and a.booking_id=$wo_pi_ID and a.entry_form=559 and c.entry_form=559 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");

			foreach($previous_recv_by_wo as $row)
			{

				if($update_id == $row[csf('update_id')])
				{
					$this_recv_qnty_arr[$row[csf('body_part_id')]][$row[csf('fabric_description_id')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('uom')]][$row[csf('color_name')]]['qnty']+=$row[csf('qnty')];
					$this_recv_qnty_arr[$row[csf('body_part_id')]][$row[csf('fabric_description_id')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('uom')]][$row[csf('color_name')]]['rate']=$row[csf('rate')];
					$this_recv_qnty_arr[$row[csf('body_part_id')]][$row[csf('fabric_description_id')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('uom')]][$row[csf('color_name')]]['amount']=$row[csf('amount')];

					$this_recv_qnty_arr[$row[csf('body_part_id')]][$row[csf('fabric_description_id')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('uom')]][$row[csf('color_name')]]['no_of_roll']=$row[csf('no_of_roll')];
					$this_recv_qnty_arr[$row[csf('body_part_id')]][$row[csf('fabric_description_id')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('uom')]][$row[csf('color_name')]]['book_currency']=$row[csf('book_currency')];
					$this_recv_qnty_arr[$row[csf('body_part_id')]][$row[csf('fabric_description_id')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('uom')]][$row[csf('color_name')]]['ile']=$row[csf('ile')];
					$this_recv_qnty_arr[$row[csf('body_part_id')]][$row[csf('fabric_description_id')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('uom')]][$row[csf('color_name')]]['remarks']=$row[csf('remarks')];
					$this_recv_qnty_arr[$row[csf('body_part_id')]][$row[csf('fabric_description_id')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('uom')]][$row[csf('color_name')]]['dtls_id']=$row[csf('dtls_id')];


					$save_string = $row[csf('po_breakdown_id')] . "**" . $row[csf('job_no')] . "**" . $row[csf('booking_no')] . "**" . $row[csf('qnty')] . "**" . $row[csf('roll_no')] . "**" . "" . "**" . $row[csf('roll_table_id')] . "**" . $row[csf('barcode_no')] . "**" . $row[csf('rf_id')];


					$this_recv_qnty_arr[$row[csf('body_part_id')]][$row[csf('fabric_description_id')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('uom')]][$row[csf('color_name')]]['remarks']=$row[csf('remarks')];

					$this_recv_qnty_arr[$row[csf('body_part_id')]][$row[csf('fabric_description_id')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('uom')]][$row[csf('color_name')]]['roll_data'] .=$save_string.",";

				}
				else
				{
					$prev_recv_qnty_arr[$row[csf('body_part_id')]][$row[csf('fabric_description_id')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('uom')]][$row[csf('color_name')]]['qnty']+=$row[csf('qnty')];
				}

			}
		}
		else
		{
		 	$sql="SELECT body_part, booking_no as wopi_number, lib_yarn_count_deter_id, gsm_weight, dia_width, color_all_data as fabric_color_id, fabric_color, sum(finish_fabric) as qnty,uom, entry_form_id   
		 	from wo_non_ord_samp_booking_dtls
		 	where booking_no='$wopiNumber' and status_active=1 and is_deleted=0 
		 	group by booking_no,lib_yarn_count_deter_id, body_part, gsm_weight, dia_width,color_all_data,fabric_color,uom, entry_form_id";
		}
	}
	//echo $sql;
	$result = sql_select($sql);
	$color_name_arr=return_library_array( "select id, color_name from lib_color where status_active=1",'id','color_name');
	$lib_body_part_arr=return_library_array("select id, body_part_full_name from lib_body_part", "id", "body_part_full_name");
	$i=1;
	if($receive_basis==1)
	{
		
		foreach($result as $row)
		{
			if ($i%2==0)$bgcolor="#E9F3FF";
			else $bgcolor="#FFFFFF";
			$fabric_desc=$lib_body_part_arr[$row[csf('body_part_id')]].', '.$composition_arr[$row[csf('lib_yarn_count_deter_id')]];
			$body_part_id=$row[csf('body_part_id')];

			$color_id=$row[csf("color_id")];

			$recvRtnQnty=$recv_rtn_qnty_arr[$body_part_id][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$color_id];
			$cumulative_recvQnty=$prev_recv_qnty_arr[$body_part_id][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$color_id][$row[csf('work_order_id')]]-$recvRtnQnty;

			$balanceQnty=($row[csf('qnty')]-$cumulative_recvQnty);


			$rd_no= $determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('work_order_id')]][$body_part_id]['rd_no'];
			$fabric_ref= $determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('work_order_id')]][$body_part_id]['fabric_ref'];
			$weight_type = $determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('work_order_id')]][$body_part_id]['weight_type'];
			$cutable_width = $determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('work_order_id')]][$body_part_id]['cutable_width'];

			$style_ref_no = $style_ref_no_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('work_order_id')]]['style_ref_no'];


			$qnty = $this_recv_qnty_arr[$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('uom')]][$row[csf('color_id')]]['qnty'];
			$rate = $this_recv_qnty_arr[$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('uom')]][$row[csf('color_id')]]['rate'];
			$amount = $this_recv_qnty_arr[$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('uom')]][$row[csf('color_id')]]['amount'];
			$no_of_roll = $this_recv_qnty_arr[$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('uom')]][$row[csf('color_id')]]['no_of_roll'];
			$roll_data = $this_recv_qnty_arr[$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('uom')]][$row[csf('color_id')]]['roll_data'];
			$dtls_id = $this_recv_qnty_arr[$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('uom')]][$row[csf('color_id')]]['dtls_id'];
			$book_currency = $this_recv_qnty_arr[$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('uom')]][$row[csf('color_id')]]['book_currency'];
			$ile = $this_recv_qnty_arr[$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('uom')]][$row[csf('color_id')]]['ile'];
			$remarks = $this_recv_qnty_arr[$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('uom')]][$row[csf('color_id')]]['remarks'];

			$roll_data = chop($roll_data,",");
			//a.determination_id as lib_yarn_count_deter_id, a.uom,a.fab_weight as gsm, a.dia_width, a.color_id, sum(a.quantity) as qnty, b.pi_basis_id, a.work_order_no, a.work_order_id, a.body_part_id
			$avgRate=$row[csf("amount")]/$row[csf("qnty")];
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick='change_color("<?php echo $i; ?>","#E9F3FF");'>
				<td width="80"><? echo $body_part[$body_part_id]; ?></td>
				<td width="120" class="wrap_break"><? echo $fabric_desc; ?></td>
				<td width="80" align="left"><p class="wrap_break"><? echo $fabric_ref; ?></p></td>
				<td width="80" align="left"><p class="wrap_break"><? echo $rd_no; ?></p></td>
				<td width="80"><p class="wrap_break"><? echo $color_name_arr[$color_id]; ?></p></td>
				<td width="80"><? echo $row[csf("gsm")]; ?>&nbsp;</td>
				<td width="80"><? echo $fabric_weight_type[$weight_type]; ?>&nbsp;</td>
				<td width="80"><? echo $unit_of_measurement[$row[csf("uom")]]; ?>&nbsp;</td>
				<td width="80"><? echo $row[csf("dia_width")]; ?></td>
				<td width="80"><? echo $cutable_width; ?>&nbsp;</td>
				<td width="80" class="wrap_break"><? //echo 'batch lot'; ?>&nbsp;</td>
				<td width="80">
					<input type="text" value="<? echo $qnty;?>" id="recvQty_<? echo $i; ?>" class="text_boxes"  style="width:50px" name="recvQty[]" onDblClick="openmypage_po(<? echo $i; ?>)" readonly/>
					<input type="hidden" value="<? echo $roll_data;?>" id="rollData_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="rollData[]"  />
				</td>
				<td width="80">
					<input type="text" value="<? echo $no_of_roll;?>" id="txtRoll_<? echo $i; ?>" class="text_boxes"  style="width:50px" name="txtRoll[]" readonly disabled/>
				</td>
				<td width="80" align="right">
					<input type="text" value="<? echo number_format($rate,2); ?>" id="txtRate_<? echo $i; ?>" class="text_boxes_numeric"  style="width:55px" name="txtRate[]" readonly disabled/>
				</td>
				<td width="80" align="right">
					<input type="text" value="<? echo number_format($amount,2); ?>" id="txtAmount_<? echo $i; ?>" class="text_boxes_numeric"  style="width:60px" name="txtAmount[]" readonly disabled/>
				</td>
				<td width="80" align="right">
					<input type="text" value="" id="txtBlaOrderQty_<? echo $i; ?>" class="text_boxes_numeric"  style="width:60px" name="txtBlaOrderQty[]" readonly disabled/>
				</td>
				<td width="80" align="right">
					<input type="text" value="<? echo number_format($book_currency,2,".","");?>" id="txtBookCurrency_<? echo $i; ?>" class="text_boxes_numeric"  style="width:60px" name="txtBookCurrency[]" readonly disabled/>
				</td>
				<td width="80" align="right">
					<input type="text" value="<? echo number_format($ile,2,".","");?>" id="txtIle_<? echo $i; ?>" class="text_boxes_numeric"  style="width:55px" name="txtIle[]" readonly disabled/>
				</td>

				<td width="80" align="center" id="floorTd_<? echo $i; ?>" class="floor_td_to">
					<? 
					$argument = "'".$i.'_0'."'";					
					echo create_drop_down( "cboFloor_".$i, 60,$blank_array,"", 1, "--Select--", 0, "fn_load_room(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'cboFloor');",0,"","","","","","","cboFloor[]" ,"onchange_void"); ?>
				</td>
				<td width="80" align="center" id="roomTd_<? echo $i; ?>" >
					<? 
					$argument = "'".$i.'_1'."'";
					echo create_drop_down( "cboRoom_".$i, 60,$blank_array,"", 1, "--Select--", 0, "fn_load_rack(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'cboRoom');",0,"","","","","","","cboRoom[]","onchange_void" ); ?>		            
				</td>
				<td width="80" align="center" id="rackTd_<? echo $i; ?>" >
					<? 
					$argument = "'".$i.'_2'."'";
					echo create_drop_down( "txtRack_".$i, 60,$blank_array,"", 1, "--Select--", 0, "fn_load_shelf(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'txtRack');",0,"","","","","","","txtRack[]","onchange_void" ); ?>
				</td>
				<td width="80" align="center" id="shelfTd_<? echo $i; ?>" >
					<? 
					$argument = "'".$i.'_3'."'";
					echo create_drop_down( "txtShelf_".$i, 60,$blank_array,"", 1, "--Select--", 0, "fn_load_bin(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'txtShelf');",0,"","","","","","","txtShelf[]","onchange_void" ); ?>
				</td>
				<td width="80" align="center" id="binTd_<? echo $i; ?>" >
					<? 
					$argument = "'".$i.'_4'."'";
					echo create_drop_down( "txtBin_".$i, 60,$blank_array,"", 1, "--Select--", 0, "copy_all($argument);",0,"","","","","","","txtBin[]","onchange_void" ); ?>
				</td>
				<td width="80">
					<input type="text" value="<? echo $remarks;?>" id="txtRemarks_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtRemarks[]" />
					
					<input type="hidden" value="<? echo $body_part_id;?>" id="cboBodyPart_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="cboBodyPart[]" />
					
					<input type="hidden" value="<? echo $row[csf('lib_yarn_count_deter_id')];?>" id="fabricDescId_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="fabricDescId[]" />
					
					<input type="hidden" value="<? echo $fabric_ref;?>" id="txtFabricRef_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtFabricRef[]" />
					
					<input type="hidden" value="<? echo $rd_no;?>" id="txtRDNo_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtRDNo[]" />
					
					<input type="hidden" value="<? echo $color_id;?>" id="txtColor_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtColor[]" />

					<input type="hidden" value="<? echo $row[csf("gsm")];?>" id="txtGsm_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtGsm[]" />
					
					<input type="hidden" value="<? echo $weight_type;?>" id="txtWeightType_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtWeightType[]" />
					
					<input type="hidden" value="<? echo $row[csf("uom")];?>" id="cboUOM_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="cboUOM[]" />

					<input type="hidden" value="<? echo $wo_pi_ID;?>" id="hiddenPIId_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="hiddenPIId[]" />

					<input type="hidden" value="<? echo $row[csf('work_order_no')];?>" id="hdnBookingNo_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="hdnBookingNo[]" />
					<input type="hidden" value="<? echo $row[csf('work_order_id')];?>" id="hdnBookingID_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="hdnBookingID[]" />
					
					<input type="hidden" value="<? echo $row[csf("dia_width")];?>" id="txtDiaWidth_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtDiaWidth[]" />
					<input type="hidden" value="<? echo $cutable_width;?>" id="txtCutableWidth_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtCutableWidth[]" />
					<input type="hidden" value="<? //?>" id="distributionMethodId_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="distributionMethodId[]" />
					<input type="hidden" value="<? //?>" id="txtDeletedId_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtDeletedId[]" />
					<input type="hidden" value="<? echo $dtls_id;?>" id="updateDtlsId_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="updateDtlsId[]" />
				</td>				
			</tr>
			<?
			$i++;
		}
		                
	}
	else
	{
		foreach($result as $row)
		{
			if ($i%2==0)$bgcolor="#E9F3FF";
			else $bgcolor="#FFFFFF";

			$fabric_desc=$composition_arr[$row[csf('lib_yarn_count_deter_id')]];
			if($receive_basis==1) // pi basis
			{
				$color_id=$row[csf("color_id")];
			}
			else if($receive_basis==2 || $receive_basis==4)
			{
				if($hidden_is_non_ord_sample==0)
				{
					$color_id=$row[csf("color_id")];
					$body_part_id=$row[csf("body_part_id")];
				}
				else
				{
					if ($row[csf("entry_form_id")]==439)
					{
						$color_id=explode("_",$row[csf("fabric_color_id")]);
						$color_id=$color_id[2];
						$body_part_id=$row[csf("body_part")];
					}
					else
					{
						$color_id=$row[csf("fabric_color")];								
						$body_part_id=$row[csf("body_part")];
					}
				}
			}

			$recvRtnQnty=$recv_rtn_qnty_arr[$body_part_id][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]];

			$cumulative_recvQnty=$prev_recv_qnty_arr[$body_part_id][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]]-$recvRtnQnty;

			$balanceQnty=($row[csf('qnty')]-$cumulative_recvQnty);

			$rd_no=$determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('wopi_number')]][$row[csf('body_part_id')]]['rd_no'];
			$fabric_ref=$determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('wopi_number')]][$row[csf('body_part_id')]]['fabric_ref'];
			$cutable_width=$determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('wopi_number')]][$row[csf('body_part_id')]]['cutable_width'];
			$weight_type=$determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('wopi_number')]][$row[csf('body_part_id')]]['weight_type'];

			$avgRate=$row[csf("amount")]/$row[csf("qnty")];


			$qnty = $this_recv_qnty_arr[$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('uom')]][$row[csf('color_id')]]['qnty'];
			$rate = $this_recv_qnty_arr[$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('uom')]][$row[csf('color_id')]]['rate'];
			$amount = $this_recv_qnty_arr[$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('uom')]][$row[csf('color_id')]]['amount'];
			$no_of_roll = $this_recv_qnty_arr[$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('uom')]][$row[csf('color_id')]]['no_of_roll'];
			$roll_data = $this_recv_qnty_arr[$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('uom')]][$row[csf('color_id')]]['roll_data'];
			$dtls_id = $this_recv_qnty_arr[$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('uom')]][$row[csf('color_id')]]['dtls_id'];
			$book_currency = $this_recv_qnty_arr[$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('uom')]][$row[csf('color_id')]]['book_currency'];
			$ile = $this_recv_qnty_arr[$row[csf('body_part_id')]][$row[csf('lib_yarn_count_deter_id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('uom')]][$row[csf('color_id')]]['ile'];

			$roll_data = chop($roll_data,",");
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick='change_color("<?php echo $i; ?>","#E9F3FF");'>
				<td width="80"><? echo $body_part[$body_part_id]; ?></td>
				<td width="120" class="wrap_break"><? echo $fabric_desc; ?></td>
				<td width="80" align="left"><p class="wrap_break"><? echo $fabric_ref; ?></p></td>
				<td width="80" align="left"><p class="wrap_break"><? echo $rd_no; ?></p></td>
				<td width="80"><p class="wrap_break"><? echo $color_name_arr[$color_id]; ?></p></td>
				<td width="80"><? echo $row[csf("gsm_weight")]; ?>&nbsp;</td>
				<td width="80"><? echo $fabric_weight_type[$weight_type]; ?>&nbsp;</td>
				<td width="80"><? echo $unit_of_measurement[$row[csf("uom")]]; ?>&nbsp;</td>
				<td width="80"><? echo $row[csf("dia_width")]; ?></td>
				<td width="80"><? echo $cutable_width; ?>&nbsp;</td>
				<td width="80" class="wrap_break"><? //echo 'batch lot'; ?>&nbsp;</td>
				<td width="80">
					<input type="text" value="<? echo $qnty;?>" id="recvQty_<? echo $i; ?>" class="text_boxes"  style="width:50px" name="recvQty[]" onDblClick="openmypage_po(<? echo $i; ?>)" readonly/>
					<input type="hidden" value="<? echo $roll_data;?>" id="rollData_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="rollData[]"  />
				</td>
				<td width="80">
					<input type="text" value="<? echo $no_of_roll;?>" id="txtRoll_<? echo $i; ?>" class="text_boxes"  style="width:50px" name="txtRoll[]" readonly disabled/>
				</td>
				<td width="80" align="right">
					<input type="text" value="<? echo number_format($rate,2); ?>" id="txtRate_<? echo $i; ?>" class="text_boxes_numeric"  style="width:55px" name="txtRate[]" readonly disabled/>
				</td>
				<td width="80" align="right">
					<input type="text" value="<? echo number_format($amount,2); ?>" id="txtAmount_<? echo $i; ?>" class="text_boxes_numeric"  style="width:60px" name="txtAmount[]" readonly disabled/>
				</td>
				<td width="80" align="right">
					<input type="text" value="" id="txtBlaOrderQty_<? echo $i; ?>" class="text_boxes_numeric"  style="width:60px" name="txtBlaOrderQty[]" readonly disabled/>
				</td>
				<td width="80" align="right">
					<input type="text" value="<? echo number_format($book_currency,2,".","");?>" id="txtBookCurrency_<? echo $i; ?>" class="text_boxes_numeric"  style="width:60px" name="txtBookCurrency[]" readonly disabled/>
				</td>
				<td width="80" align="right">
					<input type="text" value="<? echo number_format($ile,2,".","");?>" id="txtIle_<? echo $i; ?>" class="text_boxes_numeric"  style="width:55px" name="txtIle[]" readonly disabled/>
				</td>

				<td width="80" align="center" id="floorTd_<? echo $i; ?>" class="floor_td_to">
					<? 
					$argument = "'".$i.'_0'."'";					
					echo create_drop_down( "cboFloor_".$i, 60,$blank_array,"", 1, "--Select--", 0, "fn_load_room(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'cboFloor');",0,"","","","","","","cboFloor[]" ,"onchange_void"); ?>
				</td>
				<td width="80" align="center" id="roomTd_<? echo $i; ?>" >
					<? 
					$argument = "'".$i.'_1'."'";
					echo create_drop_down( "cboRoom_".$i, 60,$blank_array,"", 1, "--Select--", 0, "fn_load_rack(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'cboRoom');",0,"","","","","","","cboRoom[]","onchange_void" ); ?>		            
				</td>
				<td width="80" align="center" id="rackTd_<? echo $i; ?>" >
					<? 
					$argument = "'".$i.'_2'."'";
					echo create_drop_down( "txtRack_".$i, 60,$blank_array,"", 1, "--Select--", 0, "fn_load_shelf(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'txtRack');",0,"","","","","","","txtRack[]","onchange_void" ); ?>
				</td>
				<td width="80" align="center" id="shelfTd_<? echo $i; ?>" >
					<? 
					$argument = "'".$i.'_3'."'";
					echo create_drop_down( "txtShelf_".$i, 60,$blank_array,"", 1, "--Select--", 0, "fn_load_bin(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'txtShelf');",0,"","","","","","","txtShelf[]","onchange_void" ); ?>
				</td>
				<td width="80" align="center" id="binTd_<? echo $i; ?>" >
					<? 
					$argument = "'".$i.'_4'."'";
					echo create_drop_down( "txtBin_".$i, 60,$blank_array,"", 1, "--Select--", 0, "copy_all($argument);",0,"","","","","","","txtBin[]","onchange_void" ); ?>
				</td>
				<td width="80">
					<input type="text" id="txtRemarks_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtRemarks[]" />
					
					<input type="hidden" value="<? echo $body_part_id;?>" id="cboBodyPart_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="cboBodyPart[]" />
					
					<input type="hidden" value="<? echo $row[csf('lib_yarn_count_deter_id')];?>" id="fabricDescId_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="fabricDescId[]" />
					
					<input type="hidden" value="<? echo $fabric_ref;?>" id="txtFabricRef_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtFabricRef[]" />
					
					<input type="hidden" value="<? echo $rd_no;?>" id="txtRDNo_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtRDNo[]" />
					
					<input type="hidden" value="<? echo $color_id;?>" id="txtColor_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtColor[]" />

					<input type="hidden" value="<? echo $row[csf("gsm_weight")];?>" id="txtGsm_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtGsm[]" />
					
					<input type="hidden" value="<? echo $weight_type;?>" id="txtWeightType_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtWeightType[]" />
					
					<input type="hidden" value="<? echo $row[csf("uom")];?>" id="cboUOM_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="cboUOM[]" />

					<input type="hidden" value="<? //echo $wo_pi_ID;?>" id="hiddenPIId_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="hiddenPIId[]" />

					<input type="hidden" value="<? //echo $row[csf('work_order_no')];?>" id="hdnBookingNo_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="hdnBookingNo[]" />
					<input type="hidden" value="<? //echo $row[csf('work_order_id')];?>" id="hdnBookingID_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="hdnBookingID[]" />
					
					<input type="hidden" value="<? echo $row[csf("dia_width")];?>" id="txtDiaWidth_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtDiaWidth[]" />
					<input type="hidden" value="<? echo $cutable_width;?>" id="txtCutableWidth_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtCutableWidth[]" />
					<input type="hidden" value="<? //?>" id="distributionMethodId_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="distributionMethodId[]" />
					<input type="hidden" value="<? //?>" id="txtDeletedId_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtDeletedId[]" />
					<input type="hidden" value="<? echo $dtls_id;?>" id="updateDtlsId_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="updateDtlsId[]" />
					
				</td>				
			</tr>
		<?
			$i++;
		}

	}
	exit();
}

if($action=="check_same_item_found_inSameMRR")
{
	$ex_data = explode("**",$data);
	$update_id = $ex_data[0];
	$receive_basis = $ex_data[1];
	if($receive_basis==1)
	{
		$wo_pi_ID = $ex_data[2];
		$deter_id=$ex_data[3];
		$color_id=$ex_data[4];
		$dia_width=$ex_data[5];
		$weight=$ex_data[6];
		$pi_basis=$ex_data[7];
		$work_order_no=$ex_data[8];
		$work_order_id=$ex_data[9];
		$fabric_ref=$ex_data[10];
		$rd_no=$ex_data[11];
	}
	else
	{
		$wopiNumber=$ex_data[2];
		$hidden_is_non_ord_sample=$ex_data[3];
		$deter_id=$ex_data[4];
		$color_id=$ex_data[5];
		$dia_width=$ex_data[6];
		$weight=$ex_data[7];
		$body_part=$ex_data[8];
		$fabric_ref=$ex_data[9];
		$rd_no=$ex_data[10];
	}
	 
	if($receive_basis==1)
	{
		$sql=sql_select("select a.mst_id from inv_transaction a ,pro_finish_fabric_rcv_dtls b  where a.id=b.trans_id and a.mst_id=$update_id and b.fabric_description_id=$deter_id and b.color_id=$color_id and b.original_width='$dia_width' and b.original_gsm='$weight' and b.booking_id='$work_order_id' and a.fabric_ref='$fabric_ref' and a.rd_no='$rd_no' and a.transaction_type = 1 and a.item_category = 3  and a.is_deleted=0 and a.status_active=1");
		$mst_id=$sql[0][csf('mst_id')];
		echo "$('#hidden_chk_saved_item').val('".$mst_id."');\n";
	}
	else if($receive_basis==2)
	{
		$sql=sql_select("select a.mst_id from inv_transaction a,pro_finish_fabric_rcv_dtls b  where a.id=b.trans_id and a.mst_id=$update_id and b.fabric_description_id=$deter_id and b.color_id=$color_id and b.original_width='$dia_width' and b.original_gsm='$weight' and b.body_part_id='$body_part' and a.fabric_ref='$fabric_ref' and a.rd_no='$rd_no'  and a.transaction_type = 1 and a.item_category = 3 and a.is_deleted=0 and a.status_active=1");
		$mst_id=$sql[0][csf('mst_id')];
		echo "$('#hidden_chk_saved_item').val('".$mst_id."');\n";
	}	

	exit();
}

// get form data from product click in right side
if($action=="wo_pi_product_form_input")
{
	$color_name_arr=return_library_array( "select id, color_name from lib_color where status_active=1",'id','color_name');
	$composition_arr=array();
	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from  lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	if(count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
			}
		}
	}

	$ex_data = explode("**",$data);
	$receive_basis = $ex_data[0];
	if($receive_basis==1)
	{
		$wo_pi_ID = $ex_data[1];
		$deter_id=$ex_data[2];
		$color_id=$ex_data[3];
		$dia_width=$ex_data[4];
		$weight=$ex_data[5];
		$pi_basis=$ex_data[6];
		$work_order_no=$ex_data[7];
		$work_order_id=$ex_data[8];
		$fabric_ref=$ex_data[9];
		$rd_no=$ex_data[10];
		$body_part_id=$ex_data[11];
	}
	else
	{
		$wopiNumber=$ex_data[1];
		$hidden_is_non_ord_sample=$ex_data[2];
		$deter_id=$ex_data[3];
		$color_id=$ex_data[4];
		$dia_width=$ex_data[5];
		$weight=$ex_data[6];
		$body_part=$ex_data[7];
		$fabric_ref=$ex_data[8];
		$rd_no=$ex_data[9];
	}

	if($receive_basis==1) // pi basis
	{
		if($weight!="" || $db_type==0)
		{
			if ($pi_basis==2) 
			{
				$weight_cond="a.gsm='$weight'";
			}
			else
			{
				$weight_cond="a.fab_weight='$weight'";
			}
			
		}
		else 
		{
			if ($pi_basis==2) 
			{
				$weight_cond="a.gsm is null";
			}
			else
			{
				$weight_cond="a.fab_weight is null";
			}
		}

		if($dia_width!="" || $db_type==0)
		{
			$dia_width_cond="a.dia_width='$dia_width'";
		}
		else $dia_width_cond="a.dia_width is null";
		if($weight=="") $weight_con=0;else $weight_con=$weight;
		if($dia_width=="") $dia_con=0;else $dia_con=$dia_width;

		if($db_type==2)
		{
			//$gsm_dia_cond=" and nvl(a.gsm,0) ='$weight_con'  and  nvl(a.dia_width,0) ='$dia_con' ";
			//$gsm_dia_field=" and nvl(a.gsm,0) =nvl(e.gsm_weight,0)  and  nvl(a.dia_width,0) =nvl(c.dia_width,0) ";
			$gsm_dia_cond=" and  nvl(a.dia_width,0) ='$dia_con' ";
			$gsm_dia_field="and  nvl(a.dia_width,0) =nvl(c.dia_width,0) ";
		}
		else
		{
			//$gsm_dia_field=" and a.gsm=b.gsm_weight and  a.dia_width =b.dia_width ";
			//$gsm_dia_cond=" and a.gsm ='$weight_con'  and  a.dia_width='$dia_con' ";
			$gsm_dia_field=" and a.dia_width =c.dia_width ";
			$gsm_dia_cond=" and  a.dia_width='$dia_con' ";
		}

		if($fabric_ref){$fabric_ref_cond="and f.fabric_ref='$fabric_ref'";}
		if($rd_no){$rd_no_cond="and f.rd_no ='$rd_no' ";}

		if($pi_basis==2)
		{
			$sql = "SELECT a.determination_id as lib_yarn_count_deter_id, a.gsm as gsm_weight, a.uom, a.dia_width, a.color_id as fabric_color_id,sum(a.quantity*a.rate) as amount, sum(a.quantity) as qnty
				from com_pi_item_details a
				where a.pi_id=$wo_pi_ID and a.status_active=1 and a.is_deleted=0 and a.determination_id='$deter_id' and a.color_id='$color_id' and $dia_width_cond and $weight_cond
				group by a.determination_id, a.gsm, a.uom, a.dia_width, a.color_id";
		}
		else
		{
			$sql ="SELECT a.determination_id as lib_yarn_count_deter_id, a.uom, a.fab_weight as gsm_weight, a.dia_width,a.color_id as fabric_color_id, sum(a.quantity*a.rate) as amount, a.quantity as qnty,a.body_part_id,b.buyer_id 
			from com_pi_item_details a, wo_booking_mst b 
			where a.work_order_no=b.booking_no and a.pi_id=$wo_pi_ID  and a.status_active=1 and a.is_deleted=0 and b.status_active = 1 and b.is_deleted =0  and a.determination_id='$deter_id' and a.color_id='$color_id' $gsm_dia_cond and $weight_cond and a.body_part_id=$body_part_id and b.booking_no='$work_order_no' 
			group by a.determination_id , a.uom, a.fab_weight , a.dia_width,a.color_id,a.body_part_id,b.buyer_id, a.quantity";
			//and a.fabric_composition = e.composition 

			$determination_info_sql =sql_select("SELECT a.determination_id as lib_yarn_count_deter_id, a.uom, a.fab_weight as gsm_weight, a.dia_width,a.color_id as fabric_color_id,e.body_part_id,f.fabric_ref,f.rd_no,e.gsm_weight_type as weight_type,g.item_size as cutable_width from com_pi_item_details a, wo_booking_dtls c,wo_pre_cost_fabric_cost_dtls e join  wo_pre_cos_fab_co_avg_con_dtls g on e.id=g.pre_cost_fabric_cost_dtls_id,lib_yarn_count_determina_mst f where a.work_order_no=c.booking_no and c.pre_cost_fabric_cost_dtls_id=e.id and e.lib_yarn_count_deter_id=f.id and  a.determination_id = e.lib_yarn_count_deter_id and a.color_id= c.fabric_color_id and a.pi_id=$wo_pi_ID  and a.status_active=1 and a.is_deleted=0   and a.determination_id='$deter_id' and a.color_id='$color_id'  $gsm_dia_field $gsm_dia_cond $fabric_ref_cond $rd_no_cond  and $weight_cond
			group by a.determination_id , a.uom, a.fab_weight , a.dia_width,a.color_id,e.body_part_id,f.fabric_ref,f.rd_no,e.gsm_weight_type,g.item_size");
			//and a.fabric_composition = e.composition
			foreach($determination_info_sql as $row)
			{
				$determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('fabric_color_id')]][$row[csf('body_part_id')]]['rd_no']=$row[csf('rd_no')];
				$determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('fabric_color_id')]][$row[csf('body_part_id')]]['fabric_ref']=$row[csf('fabric_ref')];
				$determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('fabric_color_id')]][$row[csf('body_part_id')]]['weight_type']=$row[csf('weight_type')];
				$determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('fabric_color_id')]][$row[csf('body_part_id')]]['cutable_width']=$row[csf('cutable_width')];
			}
		}
		//echo $sql;
		//echo "string"; die;

	}
	else if($receive_basis==2) // wo basis
	{
		//echo $hidden_is_non_ord_sample."test";
		//$hidden_is_non_ord_sample = 1;
		if($hidden_is_non_ord_sample==0)
		{
			if($weight!="" || $db_type==0)
			{
				$weight_cond="b.gsm_weight='$weight'";
			}
			else $weight_cond="b.gsm_weight is null";

			if($dia_width!="" || $db_type==0)
			{
				$dia_width_cond="a.dia_width='$dia_width'";
			}
			else $dia_width_cond="a.dia_width is null";
			if ($color_id!=""){$color_id_cond="and a.fabric_color_id='$color_id'";}else{$color_id_cond="";}
			if ($body_part!=""){$body_part_cond="and b.body_part_id='$body_part'";}else{$body_part_cond="";}

		 	//$sql="SELECT b.body_part_id,b.lib_yarn_count_deter_id, b.gsm_weight,b.uom, a.dia_width, a.fabric_color_id, sum(a.fin_fab_qnty) as qnty, avg(a.rate) as avg_rate, a.booking_no from wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b where a.pre_cost_fabric_cost_dtls_id=b.id and a.booking_no='$wopiNumber' and b.lib_yarn_count_deter_id='$deter_id' $color_id_cond $body_part_cond and $dia_width_cond and $weight_cond and a.status_active=1 and a.is_deleted=0 $fabric_ref_cond $rd_no_cond group by b.body_part_id,b.lib_yarn_count_deter_id, b.gsm_weight,b.uom,a.dia_width, a.fabric_color_id, a.booking_no";

		 	$sql="SELECT b.body_part_id,b.lib_yarn_count_deter_id, b.gsm_weight,b.uom, a.dia_width, a.fabric_color_id, sum(a.fin_fab_qnty) as qnty,sum(a.grey_fab_qnty*a.rate) as amount, a.booking_no from wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b where a.pre_cost_fabric_cost_dtls_id=b.id and a.booking_no='$wopiNumber' and b.lib_yarn_count_deter_id='$deter_id' $color_id_cond $body_part_cond and $dia_width_cond and $weight_cond and a.status_active=1 and a.is_deleted=0 $fabric_ref_cond $rd_no_cond group by b.body_part_id,b.lib_yarn_count_deter_id, b.gsm_weight,b.uom,a.dia_width, a.fabric_color_id, a.booking_no";
			$booking_id=return_field_value("id","wo_booking_mst","booking_no='$wopiNumber'");


			$determination_info_sql =sql_select("SELECT b.body_part_id,b.lib_yarn_count_deter_id, b.gsm_weight,b.uom, a.dia_width, a.fabric_color_id,c.fabric_ref,c.rd_no,b.gsm_weight_type as weight_type,g.item_size as cutable_width from wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b join  wo_pre_cos_fab_co_avg_con_dtls g on b.id=g.pre_cost_fabric_cost_dtls_id ,lib_yarn_count_determina_mst c  where a.pre_cost_fabric_cost_dtls_id=b.id and b.lib_yarn_count_deter_id=c.id and a.booking_no='$wopiNumber' and b.lib_yarn_count_deter_id='$deter_id' $color_id_cond $body_part_cond and $dia_width_cond and $weight_cond and a.status_active=1 and a.is_deleted=0 $fabric_ref_cond $rd_no_cond group by b.body_part_id,b.lib_yarn_count_deter_id, b.gsm_weight,b.uom,a.dia_width, a.fabric_color_id,c.fabric_ref,c.rd_no,b.gsm_weight_type,g.item_size");
			foreach($determination_info_sql as $row)
			{
				$determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('fabric_color_id')]][$row[csf('body_part_id')]]['rd_no']=$row[csf('rd_no')];
				$determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('fabric_color_id')]][$row[csf('body_part_id')]]['fabric_ref']=$row[csf('fabric_ref')];
				$determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('fabric_color_id')]][$row[csf('body_part_id')]]['weight_type']=$row[csf('weight_type')];
				$determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('fabric_color_id')]][$row[csf('body_part_id')]]['cutable_width']=$row[csf('cutable_width')];
			}
		}
		else
		{
			if($weight!="" || $db_type==0)
			{
				$weight_cond="gsm_weight='$weight'";
			}
			else $weight_cond="gsm_weight is null";

			if($dia_width!="" || $db_type==0)
			{
				$dia_width_cond="dia_width='$dia_width'";
			}
			else $dia_width_cond="dia_width is null";
			if ($body_part!=""){$body_part_cond="and body_part='$body_part'";}else{$body_part_cond="";}
			if($fabric_ref){$fabric_ref_cond="and c.fabric_ref='$fabric_ref'";}
			if($rd_no){$rd_no_cond="and c.rd_no ='$rd_no' ";}

			//27-May-2021 qnty source is changed after consulting with mr. rashel and mr. jahid hasan vai
			$sql="SELECT body_part as body_part_id,lib_yarn_count_deter_id, gsm_weight, dia_width, uom, color_all_data as fabric_color_id, fabric_color, sum(finish_fabric) as qnty,sum(grey_fabric*rate) as amount, entry_form_id,booking_no  
			from wo_non_ord_samp_booking_dtls
			where booking_no='$wopiNumber' and lib_yarn_count_deter_id='$deter_id' $body_part_cond and $dia_width_cond and $weight_cond and status_active=1 and is_deleted=0 
			group by body_part,lib_yarn_count_deter_id, gsm_weight, dia_width,uom, color_all_data, fabric_color, entry_form_id,booking_no"; // color_all_data
			$booking_id=return_field_value("id","wo_non_ord_samp_booking_mst","booking_no='$wopiNumber'");
		}
		//echo $sql;
	}
	// echo $sql;
	$result = sql_select($sql);
	$bodyPardId="";
	foreach($result as $row)
	{
		$bodyPardId.=$row[csf("body_part_id")]."_";
	}
	$bodyPardId=chop($bodyPardId,"_");
	
	
	foreach($result as $row)
	{
		if($row[csf("gsm_weight")]!="" || $db_type==0)
		{
			$weight_cond="d.original_gsm='".$row[csf("gsm_weight")]."'";
		}
		else $weight_cond="d.original_gsm is null";

		if($row[csf("dia_width")]!="" || $db_type==0)
		{
			$dia_width_cond="d.original_width='".$row[csf("dia_width")]."'";
		}
		else $dia_width_cond="d.original_width is null";

		if($receive_basis==1) $woPi_id=$wo_pi_ID;
		else if($receive_basis==2) $woPi_id=$booking_id;

		$rd_no=$determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('fabric_color_id')]][$row[csf('body_part_id')]]['rd_no'];
		$fabric_ref=$determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('fabric_color_id')]][$row[csf('body_part_id')]]['fabric_ref'];
		$weight_type=$determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('fabric_color_id')]][$row[csf('body_part_id')]]['weight_type'];
		$cutable_width=$determination_info_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('fabric_color_id')]][$row[csf('body_part_id')]]['cutable_width'];

		if($receive_basis==2)
		{
			if($hidden_is_non_ord_sample==0)
			{
				$color_id=$row[csf("fabric_color_id")];
			}
			else
			{
				if ($row[csf("entry_form_id")]==439)
				{
					$color_id=explode("_",$row[csf("fabric_color_id")]);
					$color_id=$color_id[2];
				}
				else
				{
					$color_id=$row[csf("fabric_color")];
				}
			}
		}

		$recv_retrn_qnty=return_field_value("sum(c.quantity) as qnty"," inv_transaction a, product_details_master b,order_wise_pro_details c ,wo_po_break_down f,wo_po_details_master g,wo_booking_dtls h"," a.prod_id=b.id and a.id=c.trans_id and b.id=c.prod_id and c.po_breakdown_id=f.id and f.job_no_mst=g.job_no and g.job_no=h.job_no and f.id=h.po_break_down_id and c.entry_form=202 and b.detarmination_id='".$row[csf("lib_yarn_count_deter_id")]."' and b.color='".$color_id."' and a.body_part_id='".$row[csf("body_part_id")]."' and a.status_active=1 and a.is_deleted=0 and a.item_category=3 and a.transaction_type=3 and h.booking_no='".$row[csf("booking_no")]."'","qnty");
		if($receive_basis==1)
		{
			$pi_bookingCond="and a.booking_no='".$work_order_no."'";
		}
		$recv_qnty=return_field_value("sum(c.quantity) as qnty","inv_transaction a, product_details_master b,order_wise_pro_details c,pro_finish_fabric_rcv_dtls d","a.prod_id=b.id and a.id=c.trans_id and b.id=c.prod_id and c.dtls_id=d.id and d.trans_id=a.id and b.id=d.prod_id and c.entry_form=17 and a.receive_basis=$receive_basis and a.pi_wo_batch_no='$woPi_id' and b.detarmination_id='".$row[csf("lib_yarn_count_deter_id")]."' and b.color='".$color_id."' and a.body_part_id='".$row[csf("body_part_id")]."' and  $dia_width_cond and $weight_cond $pi_bookingCond and a.status_active=1 and a.is_deleted=0 and a.item_category=3 and a.transaction_type=1","qnty");

		//echo $row[csf("qnty")] .'-'.$recv_qnty.'-'.$recv_retrn_qnty;
		$balance_qnty=$row[csf("qnty")]-($recv_qnty-$recv_retrn_qnty);
		$fabric_desc=$composition_arr[$row[csf('lib_yarn_count_deter_id')]];

		echo "$('#txt_fabric_description').val('".$fabric_desc."');\n";
		echo "$('#original_fabric_description').val('".$fabric_desc."');\n";

		echo "$('#fabric_desc_id').val('".$row[csf("lib_yarn_count_deter_id")]."');\n";
		if($receive_basis==1) // pi basis
		{
			echo "$('#txt_color').val('".$color_name_arr[$row[csf("fabric_color_id")]]."');\n";
			echo "$('#hidden_color_id').val(".$row[csf("fabric_color_id")].");\n";
			echo "$('#hdn_booking_no').val('".$ex_data[7]."');\n";
			echo "$('#hdn_booking_id').val('".$ex_data[8]."');\n";
		}
		else if($receive_basis==2) // wo basis
		{
			if($hidden_is_non_ord_sample==0)
			{
				echo "$('#txt_color').val('".$color_name_arr[$color_id]."');\n";
				echo "$('#hidden_color_id').val(".$color_id.");\n";
			}
			else
			{
				//$color_id=explode("_",$row[csf("fabric_color_id")]);
				//echo "$('#txt_color').val('".$color_name_arr[$color_id[2]]."');\n";
				//echo "$('#hidden_color_id').val(".$color_id[2].");\n";
				echo "$('#txt_color').val('".$color_name_arr[$color_id]."');\n";
				echo "$('#hidden_color_id').val(".$color_id.");\n";
			}
		}
		else
		{
			echo "$('#txt_color').val('".$color_name_arr[$row[csf("fabric_color_id")]]."');\n";
			echo "$('#hidden_color_id').val(".$row[csf("fabric_color_id")].");\n";
		}
		//echo $row[csf("amount")].'/'.$row[csf("qnty")];
		$avgRate=$row[csf("amount")]/$row[csf("qnty")];
		echo "$('#txt_width').val('".$row[csf("dia_width")]."');\n";
		echo "$('#hidden_dia_width').val('".$row[csf("dia_width")]."');\n";
		echo "$('#cbouom').val('".$row[csf("uom")]."');\n";
		echo "$('#txt_weight').val(".$row[csf("gsm_weight")].");\n";
		echo "$('#hidden_gsm_weight').val(".$row[csf("gsm_weight")].");\n";

		echo "$('#txt_weight_edit').val(".$row[csf("gsm_weight")].");\n";
		echo "$('#txt_width_edit').val('".$row[csf("dia_width")]."');\n";

		echo "$('#txt_rate').val(".$avgRate.");\n";
		echo "$('#txt_bla_order_qty').val(".$balance_qnty.");\n";
		//echo "$('#cbo_body_part').val('".$row[csf("body_part_id")]."');\n";
		echo "$('#hidden_pi_id').val(".$wo_pi_ID.");\n";
		echo "$('#hdn_buyer_id').val(".$row[csf("buyer_id")].");\n";

		echo "$('#txt_fabric_ref').val('".$fabric_ref."');\n";
		echo "$('#txt_cutable_width').val('".$cutable_width."');\n";
		echo "$('#txt_rd_no').val('".$rd_no."');\n";
		echo "$('#cbo_weight_type').val(".$weight_type.");\n";

		if($receive_basis==2 || $receive_basis==1)
		{
			echo "load_drop_down( 'requires/woven_finish_fabric_roll_receive_controller', '".$bodyPardId."', 'load_drop_down_bodypart','body_part_td');\n";
			//echo "load_drop_down( 'requires/woven_finish_fabric_roll_receive_controller', '".$fabric_ref."', 'load_drop_down_bodypart','body_part_td');\n";

			echo "$('#txt_fabric_description').attr('disabled','disabled');\n";
			echo "$('#cbo_body_part').attr('disabled','disabled');\n";
			//echo "$('#txt_batch_lot').removeAttr('disabled','disabled');\n";
			echo "$('#txt_width_edit').removeAttr('disabled','disabled');\n";
			echo "$('#txt_weight_edit').removeAttr('disabled','disabled');\n";
		}
		else
		{
			echo "$('#txt_fabric_description').attr('readonly','readonly');\n";
		}

		exit();
	}
}

// LC popup here----------------------//
if ($action=="lc_popup")
{
  	echo load_html_head_contents("Popup Info","../../", 1, 1, $unicode);
	extract($_REQUEST);
?>

<script>
function js_set_value(str)
{
		var splitData = str.split("_");
		$("#hidden_tbl_id").val(splitData[0]); // wo/pi id
		$("#hidden_wopi_number").val(splitData[1]); // wo/pi number
		parent.emailwindow.hide();
}


</script>

</head>


<body>
<div align="center" style="width:100%;" >
<form name="searchlcfrm_1" id="searchlcfrm_1" autocomplete="off">
	<table width="600" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
            <thead>
                <tr>
                    <th width="150">Search By</th>
                    <th width="150" align="center" id="search_by_td_up">Enter WO/PI Number</th>
                    <th>
                    	<input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  />

                        <input type="hidden" id="hidden_tbl_id" value="" />
                        <input type="hidden" id="hidden_wopi_number" value="hidden_wopi_number" />

                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <?
                            $search_by_arr=array(0=>'LC Number',1=>'Supplier Name');
							$dd="change_search_event(this.value, '0*1', '0*select id, supplier_name from lib_supplier', '../../') ";
							echo create_drop_down( "cbo_search_by", 170, $search_by_arr,"",0, "--Select--", "",$dd,0 );
                        ?>
                    </td>
                    <td width="180" align="center" id="search_by_td">
                        <input type="text" style="width:230px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                    </td>
                     <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $company; ?>, 'create_lc_search_list_view', 'search_div', 'woven_finish_fabric_roll_receive_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                    </td>
           	 	</tr>
            </tbody>
        </table>
        <div align="center" valign="top" id="search_div"> </div>
        </form>
   </div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?

}


if($action=="create_lc_search_list_view")
{
	$ex_data = explode("_",$data);
	$cbo_search_by = $ex_data[0];
	$txt_search_common = $ex_data[1];
	$company = $ex_data[2];

	if($cbo_search_by==1 && $txt_search_common!="") // lc number
	{
		$sql= "select id,lc_number,item_category_id,lc_serial,supplier_id,importer_id,lc_value from com_btb_lc_master_details where lc_number LIKE '%$search_string%' and importer_id=$company and item_category_id=1 and is_deleted=0 and status_active=1";
	}
	else if($cbo_search_by==1 && $txt_search_common!="") //supplier
	{
		$sql= "select id,lc_number,item_category_id,lc_serial,supplier_id,importer_id,lc_value from com_btb_lc_master_details where supplier_id='$search_string' and importer_id=$company and item_category_id=1 and is_deleted=0 and status_active=1";
	}
	else
	{
		$sql= "select id,lc_number,item_category_id,lc_serial,supplier_id,importer_id,lc_value from com_btb_lc_master_details where importer_id=$company and item_category_id=1 and is_deleted=0 and status_active=1";
	}

	$company_arr = return_library_array("select id,company_name from lib_company","id","company_name");
	$supplier_arr = return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$arr=array(1=>$company_arr,2=>$supplier_arr,3=>$item_category);
	echo  create_list_view("list_view", "LC No,Importer,Supplier Name,Item Category,Value","120,150,150,120,120","750","260",0, $sql , "js_set_value", "id,lc_number", "", 1, "0,importer_id,supplier_id,item_category_id,0", $arr, "lc_number,importer_id,supplier_id,item_category_id,lc_value", "",'','0,0,0,0,0,1') ;
	exit();

}


if($action=="show_ile")
{
	$ex_data = explode("**",$data);
	$company = $ex_data[0];
	$source = $ex_data[1];
	$rate = $ex_data[2];

	$sql="select standard from variable_inv_ile_standard where source='$source' and company_name='$company' and category=3 and status_active=1 and is_deleted=0 order by id";
	//echo "saju1_".$sql;
	$result=sql_select($sql);
	foreach($result as $row)
	{
		// NOTE :- ILE=standard, ILE% = standard/100*rate
		$ile = $row[csf("standard")];
		$ile_percentage = ( $row[csf("standard")]/100 )*$rate;
		echo $ile."**".number_format($ile_percentage,$dec_place[3],".","");
		exit();
	}
	exit();
}



/*data save update delete here------------------------------*/
if($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	//echo "10**Working progress.....";die;

    if (str_replace("'",'',$update_id) != '')
    {
        $is_audited=return_field_value("is_audited","inv_receive_master","id=".str_replace("'",'',$update_id)." and status_active=1 and is_deleted=0","is_audited");
        //echo "10**$is_audited".'rakib';die;
        if($is_audited==1) {
            echo "50**This MRR is Audited. Save, Update and Delete Not Allowed..";
            die;
        }
    }
	$txt_fabric_type=3;

	for($j=1;$j<=$tot_row;$j++)
	{
		$hdnBookingNo="hdnBookingNo_".$j;
		$hdnBookingID="hdnBookingID_".$j;
		$txtColor="txtColor_".$j;
		$recvQty="recvQty_".$j;
		$all_booking_no[$$hdnBookingNo] ="'" .$$hdnBookingNo. "'";
		$booking_color_qnty_arr[$$hdnBookingNo][$$txtColor]['wgt'] += $$recvQty*1;
		$booking_color_qnty_arr[$$hdnBookingNo][$$txtColor]['book_id'] = $$hdnBookingID;

		$txtFabricDescription="txtFabricDescription_".$j;
		$fabricDescId="fabricDescId_".$j;
		$txtColor="txtColor_".$j;
		$txtDiaWidth="txtDiaWidth_".$j;
		$txtActualWgt="txtActualWgt_".$j;
		$cboUOM="cboUOM_".$j;

		$product_source_ref[$txt_fabric_type."='".$$txtFabricDescription."'=".$$fabricDescId."=".$$txtColor."=".$$txtDiaWidth."=".$$txtActualWgt."=".$cbo_company_id."=".$cbo_supplier."=".$cbo_store_name."=".$$cboUOM."=0=".$fabric_source]=$$fabricDescId;
	}

	/* echo "10**";
	print_r($product_source_ref);
	die; */

	
	$fabric_source = str_replace("'","",$fabric_source);
    if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }

		//echo "10**";
		foreach ($product_source_ref as $prodkey => $value) 
		{
			$prodRefArr = explode("=",$prodkey);

			$rtnString = return_product_id($prodRefArr[0],$prodRefArr[1],$prodRefArr[2],$prodRefArr[3],$prodRefArr[4],$prodRefArr[5],$prodRefArr[6],$prodRefArr[7],$prodRefArr[8],$prodRefArr[9],$prodRefArr[10],$prodRefArr[11]);

			$expString = explode("***",$rtnString);
			//echo '<pre>';print_r($expString);die;
			//echo $expString[0].'------------'.$expString[1].'-------'. $expString[1].'---------'.$expString[2]."<br>";
			$insertR = true; $flag=1;
			if($expString[0]==true && $expString[0]!="")
			{
				$prodMSTID = $expString[1];
			}
			else
			{
				$field_array = $expString[1];
				$data_array = $expString[2];
				//echo "10**insert into product_details_master($field_array)values".$data_array; echo "<br>";die;
				$insertR = sql_insert("product_details_master", $field_array, $data_array, 0);
				if($insertR)
				{
					$flag=1;
					if($db_type==2) oci_commit($con);
					else mysql_query("COMMIT");
					$prodMSTID = $expString[3];
				}
				else 
				{
					$flag=0;
				}
			}

			$product_id_ref[$prodRefArr[0]."=".$prodRefArr[1]."=".$prodRefArr[2]."=".$prodRefArr[3]."=".$prodRefArr[4]."=".$prodRefArr[5]."=".$prodRefArr[6]."=".$prodRefArr[7]."=".$prodRefArr[8]."=".$prodRefArr[9]."=".$prodRefArr[10]."=".$prodRefArr[11]]=$prodMSTID;

			//echo "10**".$prodMSTID."<br>";oci_rollback($con);die;

			if($prodMSTID=="")
			{
				echo "20**Product ID not found.";
				die;
			}

			$all_product_id[$prodMSTID] =$prodMSTID;
		}

		/* echo "10**";
		print_r($product_id_ref);
		die; */

		/*---------------Check Receive date with Last Transaction date-------------*/
		$max_transaction_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id in (".implode(',',$all_product_id).") and store_id=$cbo_store_name  and status_active = 1", "max_date");
		if($max_transaction_date != "")
		{
			$max_transaction_date = date("Y-m-d", strtotime($max_transaction_date));
			$receive_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_receive_date)));
			if ($receive_date < $max_transaction_date)
			{
				echo "20**Receive Date Can not Be Less Than Last Transaction Date Of These Lots";
			//	check_table_status($_SESSION['menu_id'], 0);
				disconnect($con);
				die;
			}
		}



		$sql = sql_select("select id, product_name_details,avg_rate_per_unit,last_purchased_qnty,current_stock,stock_value from product_details_master where id in (".implode(',',$all_product_id).")");
		$presentStock=$presentStockValue=$presentAvgRate=0;
		$product_name_details="";
		foreach($sql as $result)
		{
			$presentProductArr[$result[csf("id")]]["current_stock"] =$result[csf("current_stock")];
			$presentProductArr[$result[csf("id")]]["stock_value"] =$result[csf("stock_value")];
			$presentProductArr[$result[csf("id")]]["avg_rate_per_unit"] =$result[csf("avg_rate_per_unit")];
			$presentProductArr[$result[csf("id")]]["product_name_details"] =$result[csf("product_name_details")];
		}

		$woben_recv_num=''; $woben_update_id=''; //$flag=1;
		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)";
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";
			//defined Later

			$id = return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con);

            $new_woven_finish_recv_system_id = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master",$con,1,$cbo_company_id,'WFRR',564,date("Y",time())));

			$field_array1="id, recv_number_prefix, recv_number_prefix_num, recv_number, entry_form, item_category, receive_basis, company_id, receive_date, challan_no, qc_name, qc_id, booking_id, booking_no, booking_without_order, store_id, location_id,supplier_id,lc_no, currency_id,exchange_rate,dyeing_source, source,inserted_by, insert_date,fabric_source,buyer_id,challan_date";

			$data_array1="(".$id.",'".$new_woven_finish_recv_system_id[1]."',".$new_woven_finish_recv_system_id[2].",'".$new_woven_finish_recv_system_id[0]."',564,3,".$cbo_receive_basis.",".$cbo_company_id.",".$txt_receive_date.",".$txt_challan_no.",".$txt_qc_no.",".$txt_qc_no_id.",".$txt_wo_pi_id.",".$txt_wo_pi.",".$booking_without_order.",".$cbo_store_name.",".$cbo_location.",".$cbo_supplier.",".$hidden_lc_id.",".$cbo_currency.",".$txt_exchange_rate.",".$cbo_dyeing_source.",".$cbo_source.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','".$fabric_source."',".$cbo_buyer_name.",".$txt_challan_date.")";

			$woben_recv_num=$new_woven_finish_recv_system_id[0];
			$woben_update_id=$id;
		}
		else
		{
			$field_array_update="receive_basis*receive_date*challan_no*booking_id*booking_no*booking_without_order*store_id*location_id*supplier_id*lc_no*currency_id*exchange_rate*source*updated_by*update_date*fabric_source*buyer_id*challan_date";

			$data_array_update=$cbo_receive_basis."*".$txt_receive_date."*".$txt_challan_no."*".$txt_wo_pi_id."*".$txt_wo_pi."*".$booking_without_order."*".$cbo_store_name."*".$cbo_location."*".$cbo_supplier."*".$hidden_lc_id."*".$cbo_currency."*".$txt_exchange_rate."*".$cbo_source."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'".$fabric_source."'*".$cbo_buyer_name."*".$txt_challan_date;

			$woben_recv_num=str_replace("'","",$txt_mrr_no);
			$woben_update_id=str_replace("'","",$update_id);
		}

		for($j=1;$j<=$tot_row;$j++)
		{
			$txtBarcodeNo="txtBarcodeNo_".$j;
			$txtRFId="txtRFId_".$j;
			$txtRollNo="txtRollNo_".$j;
			$txtManualRollNo="txtManualRollNo_".$j;
			$txtBatchId="txtBatchId_".$j;
			$txtBatchNo="txtBatchNo_".$j;
			$cboBodyPart="cboBodyPart_".$j;
			$fabricDescId="fabricDescId_".$j;
			$txtFabricDescription="txtFabricDescription_".$j;
			$txtFabricRef="txtFabricRef_".$j;
			$txtRDNo="txtRDNo_".$j;
			$txtColor="txtColor_".$j;
			$txtGsm="txtGsm_".$j;
			$txtActualWgt="txtActualWgt_".$j;
			$txtDiaWidth="txtDiaWidth_".$j;
			$cboUOM="cboUOM_".$j;
			$txtWeightType="txtWeightType_".$j;
			$txtCutableWidth="txtCutableWidth_".$j;
			$recvQty="recvQty_".$j;
			//$txtRejQty="txtRejQty_".$j;
			$txtRate="txtRate_".$j;
			$txtAmount="txtAmount_".$j;
			$txtBookCurrency="txtBookCurrency_".$j;
			$txtBalancePI="txtBalancePI_".$j;
			$txtIle="txtIle_".$j;
			$floor="floor_".$j;
			$room="room_".$j;
			$rack="rack_".$j;
			$self="self_".$j;
			$binBox="binBox_".$j;
			$txtRemarks="txtRemarks_".$j;
			$txtShade="txtShade_".$j;
			$txtDeletedId="txtDeletedId_".$j;
			$hdnBookingID="hdnBookingID_".$j;
			$hdnBookingNo="hdnBookingNo_".$j;
			$hdnPOID="hdnPOID_".$j;
			$hdnJOBID="hdnJOBID_".$j;
			$hdnPOwiseQntyStr="hdnPOwiseQntyStr_".$j;

			$updateDtlsId="updateDtlsId_".$j;


			$rate = str_replace("'","",$$txtRate);
			$txt_ile = str_replace("'","",$$txtIle);
			$txt_receive_qty = str_replace("'","",$$recvQty);
			$ile = ($txt_ile/$rate)*100; // ile cost to ile
			if(is_nan($ile)){$ile=0;}
			$ile_cost = str_replace("'","",$txt_ile); //ile cost = (ile/100)*rate
			if(is_nan($ile_cost)){$ile_cost=0;}
			$exchange_rate = str_replace("'","",$txt_exchange_rate);
			$conversion_factor = 1; // woven Fabric always Yds
			$domestic_rate = return_domestic_rate($rate,$ile_cost,$exchange_rate,$conversion_factor);
			$cons_rate = number_format($domestic_rate,$dec_place[3],".","");//number_format($rate*$exchange_rate,$dec_place[3],".","");
			$con_amount = $cons_rate*$txt_receive_qty;
			$con_ile = $ile;//($ile/$domestic_rate)*100;
			$con_ile_cost = ($ile/100)*($rate*$exchange_rate);
			if(is_nan($con_ile_cost)){$con_ile_cost=0;}

			$product_id = $product_id_ref[$txt_fabric_type."='".$$txtFabricDescription."'=".$$fabricDescId."=".$$txtColor."=".$$txtDiaWidth."=".$$txtActualWgt."=".$cbo_company_id."=".$cbo_supplier."=".$cbo_store_name."=".$$cboUOM."=0='".$fabric_source."'"];

			//$product_id_ref[$txt_fabric_type."='".$$txtFabricDescription."'=".$$fabricDescId."=".$$txtColor."=".$$txtDiaWidth."=".$$txtActualWgt."=".$cbo_company_id."=".$cbo_supplier."=".$cbo_store_name."=".$$cboUOM."=0=".$fabric_source];

			$dtlsid = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);

			$txt_roll=1;
			$cbo_cutting_unit_no=0;

			//$txt_batch_lot = $batch_color_qnty_arr[$$hdnBookingNo][$$txtColor]['batch_no'];
			//$batch_id = $batch_color_qnty_arr[$$hdnBookingNo][$$txtColor]['id'];

			$field_array2 = "id,mst_id,receive_basis,pi_wo_batch_no,company_id,supplier_id,prod_id,body_part_id,item_category,transaction_type, transaction_date, store_id, order_uom, order_qnty, order_rate, order_ile,order_ile_cost, order_amount, cons_uom, cons_quantity, cons_rate, cons_ile, cons_ile_cost, cons_amount,balance_qnty, balance_amount,floor_id,room,roll,remarks,fabric_shade,cutting_unit_no,rack, self,bin_box, batch_lot,batch_id,fabric_ref,rd_no,weight_type,cutable_width,weight_editable,width_editable,inserted_by,insert_date";
			if($data_array2!="") $data_array2.= ",";
			$data_array2 .= "(".$dtlsid.",".$woben_update_id.",".$cbo_receive_basis.",".$txt_wo_pi_id.",".$cbo_company_id.",".$cbo_supplier.",".$product_id.",".$$cboBodyPart.",3,1,".$txt_receive_date.",".$cbo_store_name.",".$$cboUOM.",".$txt_receive_qty.",".$$txtRate.",".$ile.",'".$ile_cost."',".$$txtAmount.",".$$cboUOM.",".$txt_receive_qty.",".$cons_rate.",".$con_ile.",".$con_ile_cost.",".$con_amount.",".$txt_receive_qty.",".$con_amount.",".$$floor.",".$$room.",".$txt_roll.",'".$$txtRemarks."','".$$txtShade."',".$cbo_cutting_unit_no.",".$$rack.",".$$self.",".$$binBox.",'".$$txtBatchNo."',".$$txtBatchId.",'".$$txtFabricRef."','".$$txtRDNo."','".$$txtWeightType."','".$$txtCutableWidth."','".$$txtActualWgt."',".$$txtDiaWidth.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."')";
			
			$stock_value 	= $domestic_rate*$txt_receive_qty;
			$currentStock 	= $presentProductArr[$product_id]["current_stock"]+$txt_receive_qty;
			$StockValue	 	= $presentProductArr[$product_id]["stock_value"]+$stock_value;
			$avgRate		= $StockValue/$currentStock;

			
			$prod_up_id_array[]=$product_id;
			if($currentStock <=0)
			{
				$StockValue=0;
				$avgRate=0;
			}

			$field_array_prod_update="avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*updated_by*update_date";
			$data_array_prod_update[$product_id]=explode("*",(number_format($avgRate,$dec_place[3],".","")."*".$txt_receive_qty."*".$currentStock."*".$StockValue."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));


			$id_dtls = return_next_id_by_sequence("PRO_FIN_FAB_RCV_DTLS_PK_SEQ", "pro_finish_fabric_rcv_dtls", $con);
			$field_array_dtls="id, mst_id, trans_id, prod_id, batch_id,body_part_id, fabric_description_id, width,gsm, color_id, receive_qnty, no_of_roll, order_id, buyer_id,fabric_shade, floor,room,rack_no, shelf_no,bin,rate,amount,grey_fabric_rate,grey_used_qty, uom, booking_no, booking_id,original_gsm,original_width, inserted_by, insert_date";

			if($data_array_dtls!="" ) $data_array_dtls.=",";
			$data_array_dtls .="(".$id_dtls.",".$woben_update_id.",".$dtlsid.",".$product_id.",".$$txtBatchId.",".$$cboBodyPart.",".$$fabricDescId.",".$$txtDiaWidth.",".$$txtActualWgt.",".$$txtColor.",'".$txt_receive_qty."',".$txt_roll.",'".$$hdnPOID."',".$cbo_buyer_name.",'".$$txtShade."',".$$floor.",".$$room.",".$$rack.",".$$self.",".$$binBox.",".$cons_rate.", ".$con_amount.",'".$grey_fabric_rate."','".$txt_used_qty."',".$$cboUOM.",'".$$hdnBookingNo."',".$$hdnBookingID.",".$$txtGsm.",".$$txtDiaWidth.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";


			$field_array_roll="id, barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, roll_no, batch_no, manual_roll_no, booking_no, booking_without_order, rf_id,shrinkage_shade,job_id,is_booking, inserted_by, insert_date";
			$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
			if($data_array_roll!="") $data_array_roll.=",";
			$data_array_roll.="(".$id_roll.",".$$txtBarcodeNo.",".$woben_update_id.",".$id_dtls.",".$$hdnBookingID.",564,".$txt_receive_qty.",'".$$txtRollNo."','".$$txtBatchNo."','".$$txtManualRollNo."','".$$hdnBookingNo."',".$booking_without_order.",'".$$txtRFId."','".$$txtShade."',".$$hdnJOBID.",1,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

			/*$field_array_batch_dtls="id, mst_id, po_id, prod_id, item_description, original_item_description , roll_no, roll_id, barcode_no, batch_qnty, dtls_id, inserted_by, insert_date";
			$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
			if($data_array_batch_dtls!="" ) $data_array_batch_dtls.=",";
			$data_array_batch_dtls.="(".$id_dtls_batch.",'".$batch_id."','".$$hdnPOID."',".$product_id.",'".$$txtFabricDescription."','".$$txtFabricDescription."','".$$txtRollNo."','".$id_roll."',".$$txtBarcodeNo.",".$txt_receive_qty.",".$dtlsid.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";*/


			

				$rollDataPoWise = explode("_",$$hdnPOwiseQntyStr);
				foreach ($rollDataPoWise as  $poStr) 
				{
					$po_val = explode("##",$poStr);
					$txtPoId = $po_val[0];
					$txtPoQnty = $po_val[1];
					//$con_amount_orderWise = str_replace("'","",$txt_exchange_rate)*$txtPoQnty;
					$orderWiseQntyArr[$id_dtls][$txtPoId]['poQnty']+=$txtPoQnty;
					$orderWiseQntyArr[$id_dtls][$txtPoId]['transId']=$dtlsid;
					$orderWiseQntyArr[$id_dtls][$txtPoId]['colorId']=$$txtColor;
					$orderWiseQntyArr[$id_dtls][$txtPoId]['productId']=$product_id;
					//$id_prop = $id_prop+1;

				}

				
			
				/*if($data_array_prop!="") $data_array_prop.= ",";
				$data_array_prop.="(".$id_prop.",".$dtlsid.",".$id_dtls.",1,564,".$$hdnPOID.",'".$product_id."','".$$txtColor."','".$txt_receive_qty."','1',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";*/
		}

		foreach ($orderWiseQntyArr as $dtlsId => $dtlsData) {
			foreach ($dtlsData as $txtPoIds => $ordRow) {
				$id_prop = return_next_id_by_sequence("ORDER_WISE_PROP_PK_SEQ", "order_wise_pro_details", $con);
				if($data_array_prop!="") $data_array_prop.= ",";
				$data_array_prop.="(".$id_prop.",'".$ordRow['transId']."',".$dtlsId.",1,564,'".$txtPoIds."','".$ordRow['productId']."','".$ordRow['colorId']."','".$ordRow['poQnty']."',0,".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			}	
		}
						



		$rID=$rID2=$rID3=$rID4=$rID5=$rID6=$rID7=$rID8=$rID9=true;

		if($flag==1)
		{
			if(str_replace("'","",$update_id)=="")
			{
				//echo "10**INSERT INTO inv_receive_master (".$field_array1.") VALUES ".$data_array1.""; die;
				$rID=sql_insert("inv_receive_master",$field_array1,$data_array1,0);
				if($rID) $flag=1; else $flag=0;
			}
			else
			{
				//echo "update inv_receive_master set(".$field_array_update.")=".$data_array_update; die;
				$rID=sql_update("inv_receive_master",$field_array_update,$data_array_update,"id",$update_id,1);
				if($rID) $flag=1; else $flag=0;
			}
		}
		
		if($flag==1)
		{
			$rID2 = sql_insert("inv_transaction",$field_array2,$data_array2,1);
			//echo "10**INSERT INTO inv_transaction (".$field_array2.") VALUES ".$data_array2.""; die;
			if($rID2) $flag=1; else $flag=0;
		}

		if($flag==1)
		{
			//echo "10**insert into pro_finish_fabric_rcv_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
			$rID3=sql_insert("pro_finish_fabric_rcv_dtls",$field_array_dtls,$data_array_dtls,0);
			if($rID3) $flag=1; else $flag=0;
		}

		/*if($flag==1)
		{
			if(!empty($data_array_batch_update))
			{
				$rID4=execute_query(bulk_update_sql_statement( "pro_batch_create_mst", "id", $field_array_batch_update, $data_array_batch_update, $batch_id_up_array ));
				if($rID4) $flag=1; else $flag=0;
			}
			if($data_array_batch !="")
			{
				//echo "10**insert into pro_batch_create_mst (".$field_array_batch.") values ".$data_array_batch;die;
				$rID5=sql_insert("pro_batch_create_mst",$field_array_batch,$data_array_batch,0);
				if($rID5) $flag=1; else $flag=0;
			}
		}

		if($data_array_batch_dtls!="")
		{
			if($flag==1)
			{
				//echo "10**insert into pro_batch_create_dtls (".$field_array_batch_dtls.") values ".$data_array_batch_dtls;die;
				$rID6=sql_insert("pro_batch_create_dtls",$field_array_batch_dtls,$data_array_batch_dtls,1);
				if($rID6) $flag=1; else $flag=0;
			}
		}*/

		if($data_array_roll!="" )
		{
			//echo "10**insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll;die;
			$rID7=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
			if($flag==1)
			{
				if($rID7) $flag=1; else $flag=0;
			}
		}

		if($data_array_prop!="" && str_replace("'","",$booking_without_order)==0)
		{
			$field_array_proportionate="id, trans_id, dtls_id, trans_type,entry_form, po_breakdown_id, prod_id, color_id, quantity,no_of_roll, inserted_by, insert_date";
			//echo "10**insert into order_wise_pro_details (".$field_array_proportionate.") values ".$data_array_prop;die;

			$rID8=sql_insert("order_wise_pro_details",$field_array_proportionate,$data_array_prop,0);
			if($flag==1)
			{
				if($rID8) $flag=1; else $flag=0;
			}
		}

		if($flag==1){
			//echo  "10**".$field_array_prod_update.'='. $data_array_prod_update ;die;
			$rID9=execute_query(bulk_update_sql_statement( "product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $prod_up_id_array ));
			if($rID9) $flag=1; else $flag=0;
		}

		
		//echo "10**$rID=$rID2=$rID3=$rID7=$rID8=$rID9#".$flag;oci_rollback($con);die();
		////echo "5**insert into pro_batch_create_dtls (".$field_array_batch_dtls.") values ".$data_array_batch_dtls;die;

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "0**".$woben_update_id."**".$woben_recv_num."**0";
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "5**0**"."&nbsp;"."**0";
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".$woben_update_id."**".$woben_recv_num."**0";
			}
			else
			{
				oci_rollback($con);
				echo "5**0**"."&nbsp;"."**0";
			}
		}
		disconnect($con);
		die;

	}
	else if ($operation==1) // Update Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }


		for($j=1;$j<=$tot_row;$j++)
		{
			$txtDeletedId="txtDeletedId_".$j;
			$roll_deleted_ids .= $$txtDeletedId.",";
		}
		$roll_deleted_ids = chop($roll_deleted_ids,",");

		$field_array_update="receive_date*store_id*location_id*updated_by*update_date";
		$data_array_update=$txt_receive_date."*".$cbo_store_name."*".$cbo_location."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		for($j=1;$j<=$tot_row;$j++)
		{
			$floor="floor_".$j;
			$room="room_".$j;
			$rack="rack_".$j;
			$self="self_".$j;
			$binBox="binBox_".$j;
			$txtRemarks="txtRemarks_".$j;  
			$updateRollTableId="updateRollTableId_".$j;  
			$updateTransId="updateTransId_".$j;
			$updateDtlsId="updateDtlsId_".$j;  


			$field_array_trans_update = "transaction_date*store_id*floor_id*room*rack*self*bin_box*remarks*updated_by*update_date";
			$trans_up_id_arr[]=$$updateTransId;
			$data_array_trans_update[$$updateTransId]=explode("*",("".$txt_receive_date."*".$cbo_store_name."*".$$floor."*".$$room."*".$$rack."*".$$self."*".$$binBox."*'".$$txtRemarks."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

			$field_array_dtls_update="floor*room*rack_no*shelf_no*bin*updated_by*update_date";
			$dtls_up_id_arr[]=$$updateDtlsId;
			$data_array_dtls_update[$$updateDtlsId]=explode("*",("'".$$floor."'*'".$$room."'*'".$$rack."'*'".$$self."'*'".$$binBox."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));

		}

		
		$rID=$rID2=$rID3=1;

		$rID=sql_update("inv_receive_master",$field_array_update,$data_array_update,"id",$update_id,1);
		if($rID) $flag=1; else $flag=0;

		//$field_array_dtls="id, mst_id, wo_pi_id, wo_pi_no, item_category_id, entry_form, body_part_id, fabric_description_id,fabric_ref,rd_no,weight_type,width, cutable_width, color_name, gsm, uom, parking_quantity, rate, amount, no_of_roll, book_currency, ile,store_id,floor_id,room,rack,self, bin_box, remarks, inserted_by, insert_date, status_active, is_deleted";

		if(!empty($data_array_trans_update)>0)
	 	{
			//echo "10**".bulk_update_sql_statement("inv_transaction","id",$field_array_trans_update,$data_array_trans_update,$trans_up_id_arr);die;
			$rID2=execute_query(bulk_update_sql_statement("inv_transaction","id",$field_array_trans_update,$data_array_trans_update,$trans_up_id_arr),1);
			if($flag==1) 
			{
				if($rID2) $flag=1; else $flag=0; 
			} 
	 	}

		if(!empty($data_array_dtls_update)>0)
	 	{
			$rID3=execute_query(bulk_update_sql_statement("pro_finish_fabric_rcv_dtls","id",$field_array_dtls_update,$data_array_dtls_update,$dtls_up_id_arr),1);
			if($flag==1) 
			{
				if($rID3) $flag=1; else $flag=0; 
			} 
	 	}

		//echo "10**$flag**$rID**$rID2**$rID3"; oci_rollback($con);die();

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_mrr_no)."**0";
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "6**0**"."&nbsp;"."**0";
			}
		}

		if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'","",$update_id)."**".str_replace("'","",$txt_mrr_no)."**0";
			}
			else
			{
				oci_rollback($con);
				echo "6**0**"."&nbsp;"."**0";
			}
		}
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
 	}
	else if ($operation==2) // Delete Here----------------------------------------------------------
	{
		echo "20**Delete facility not available yet"; die;
	}
}


if($action=="mrr_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>

<script>
	function js_set_value(mrr)
	{
 		$("#hidden_recv_number").val(mrr); // mrr number
		parent.emailwindow.hide();
	}
</script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="980" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
            <thead>
                <tr>
                    <th width="130">Supplier</th>
                    <th width="130">Buyer Name</th>
                    <th width="100">Store Name</th>
                    <th width="120">Search By</th>
                    <th width="150" align="center" id="search_by_td_up">Enter WO/PI Number</th>
                    <th width="200">Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <?
							if($db_type==0)
							{
								echo create_drop_down( "cbo_supplier", 130, "select id,supplier_name from lib_supplier where FIND_IN_SET($data,tag_company) and FIND_IN_SET(9,party_type) order by supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
							}
							else
							{
								echo create_drop_down( "cbo_supplier", 130, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$company' and b.party_type =9 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
							}
                        ?>
                    </td>
                    <td>
                        <?
 							echo create_drop_down( "cbo_buyer_name", 160, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
                        ?>
                    </td>
                    <td>
                        <?
                        $userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, supplier_id FROM user_passwd where id='$user_id'");
						$store_location_id = $userCredential[0][csf('store_location_id')];
						if ($store_location_id != '') {$store_location_credential_cond = "and a.id in($store_location_id)";} else { $store_location_credential_cond = "";}
 							echo create_drop_down( "cbo_store_name_id", 130, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$company' and a.status_active=1 and a.is_deleted=0 and b.category_type in(3) $store_location_credential_cond group by a.id, a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", $selected, "" );
                        ?>
                    </td>
                    <td>
                        <?
                            $search_by = array(1=>'MRR No',2=>'Challan No',3=>'WO No',4=>'PI No');
							$dd="change_search_event(this.value, '0*0*0*0', '0*0*0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 120, $search_by,"",0, "--Select--", "",$dd,0 );
                        ?>
                    </td>
                    <td width="" align="center" id="search_by_td">
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                    </td>
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
                    </td>
                    <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_supplier').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_store_name_id').value, 'create_mrr_search_list_view', 'search_div', 'woven_finish_fabric_roll_receive_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                    </td>
            </tr>
        	<tr>
            	<td align="center" height="40" valign="middle" colspan="6">
					<? echo load_month_buttons(1);  ?>
                    <!-- Hidden field here -->
                     <input type="hidden" id="hidden_recv_number" value="hidden_recv_number" />
                    <!-- END -->
                </td>
            </tr>
            </tbody>
         </tr>
        </table>
        <div align="center" valign="top" id="search_div"> </div>
        </form>
   </div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}


if($action=="create_mrr_search_list_view")
{
	$ex_data = explode("_",$data);
	$supplier = $ex_data[0];
	$txt_search_by = $ex_data[1];
	$txt_search_common = $ex_data[2];
	$fromDate = $ex_data[3];
	$toDate = $ex_data[4];
	$company = $ex_data[5];
	$buyer = $ex_data[6];
	$store_id = $ex_data[7];


	$sql_cond="";
	if(trim($txt_search_common)!="")
	{
		if(trim($txt_search_by)==1) // for mrr
		{
			$sql_cond .= " and a.recv_number LIKE '%$txt_search_common%'";

		}
		else if(trim($txt_search_by)==2) // for chllan no
		{
			$sql_cond .= " and a.challan_no LIKE '%$txt_search_common%'";
 		}
 		else if(trim($txt_search_by)==3) // for WO no
		{
			$sql_cond .= " and a.receive_basis=2 and a.booking_no LIKE '%$txt_search_common%'";
 		}
 		else if(trim($txt_search_by)==4) // for PI no
		{
			$sql_cond .= " and a.receive_basis=1 and a.booking_no LIKE '%$txt_search_common%'";
 		}

 	}

	if( $fromDate!="" && $toDate!="" )
	{
		if($db_type==0)
		{
			$sql_cond .= " and a.receive_date  between '".change_date_format($fromDate,'yyyy-mm-dd')."' and '".change_date_format($toDate,'yyyy-mm-dd')."'";
		}
		if($db_type==2 || $db_type==1)
		{
			$sql_cond .= " and a.receive_date  between '".change_date_format($fromDate,'','','1')."' and '".change_date_format($toDate,'','','1')."'";
		}
	}

	if(trim($company)!="") $sql_cond .= " and a.company_id='$company'";
	if(trim($supplier)!=0) $sql_cond .= " and a.supplier_id='$supplier'";
	if(trim($buyer)!=0) $sql_cond .= " and a.buyer_id='$buyer'";
	if(trim($store_id)!=0) $sql_cond .= " and a.store_id='$store_id'";


	$sql = "SELECT a.recv_number, a.supplier_id, a.challan_no, c.lc_number, a.receive_date, a.receive_basis, sum(b.cons_quantity) as receive_qnty, a.buyer_id,case WHEN a.receive_basis=2 THEN a.booking_no ELSE null end as wo_number,case WHEN a.receive_basis=1 THEN a.booking_no ELSE null end as  pi_number  from inv_transaction b, inv_receive_master a left join com_btb_lc_master_details c on a.lc_no=c.id where a.id=b.mst_id and a.entry_form=564 and a.item_category=3 and b.item_category=3 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond group by b.mst_id, a.recv_number, a.supplier_id, a.challan_no, c.lc_number, a.receive_date, a.receive_basis, a.buyer_id,a.booking_no order by a.recv_number desc";
	//echo $sql;
	$supplier_arr = return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$arr=array(1=>$supplier_arr,2=>$buyer_arr,6=>$receive_basis_arr);
	echo create_list_view("list_view", "MRR No, Supplier Name,Buyer Name, Challan No, LC No, Receive Date, Receive Basis,WO Number,PI Number ,Receive Qnty","110,130,130,120,120,80,80,120,80,80","1100","260",0, $sql , "js_set_value", "recv_number", "", 1, "0,supplier_id,buyer_id,0,0,0,receive_basis,0,0,0", $arr, "recv_number,supplier_id,buyer_id,challan_no,lc_number,receive_date,receive_basis,wo_number,pi_number,receive_qnty", "",'','0,0,0,0,0,3,0,0,0,1') ;

	/* $sql = "SELECT a.recv_number, a.supplier_id, a.challan_no, a.booking_without_order, c.lc_number, a.receive_date, a.receive_basis, sum(b.parking_quantity) as receive_qnty, a.buyer_id, case WHEN a.receive_basis=2 THEN a.booking_no ELSE null end as wo_number,case WHEN a.receive_basis=1 THEN a.booking_no ELSE null end as  pi_number  from quarantine_parking_dtls b, inv_receive_master a left join com_btb_lc_master_details c on a.lc_no=c.id where a.id=b.mst_id and a.entry_form=559 and b.entry_form=559 and a.item_category=3 and b.item_category_id=3  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond group by b.mst_id, a.recv_number, a.supplier_id, a.challan_no, a.booking_without_order, c.lc_number, a.receive_date, a.receive_basis, a.buyer_id,a.booking_no order by a.recv_number desc";
	//echo $sql;
	$supplier_arr = return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$arr=array(1=>$supplier_arr,2=>$buyer_arr,6=>$receive_basis_arr);
	echo create_list_view("list_view", "MRR No, Supplier Name,Buyer Name, Challan No, LC No, Receive Date, Receive Basis,WO Number,PI Number ,Receive Qnty","110,130,130,120,120,80,80,120,80,80","1100","260",0, $sql , "js_set_value", "recv_number", "", 1, "0,supplier_id,buyer_id,0,0,0,receive_basis,0,0,0", $arr, "recv_number,supplier_id,buyer_id,challan_no,lc_number,receive_date,receive_basis,wo_number,pi_number,receive_qnty", "",'','0,0,0,0,0,3,0,0,0,1') ; */
	exit();

}

if($action=="qc_popup")
{
  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
?>

<script>
	function js_set_value(mrr)
	{
 		$("#hidden_recv_number").val(mrr); // mrr number
		parent.emailwindow.hide();
	}
</script>

</head>

<body>
<div align="center" style="width:100%;" >
<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
	<table width="980" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
            <thead>
                <tr>
                    <th width="130">Company</th>
                    <th width="130">Buyer Name</th>
                    <th width="100">Store Name</th>
                    <th width="120">Search By</th>
                    <th width="150" align="center" id="search_by_td_up">Shrinkage No</th>
                    <th width="200">Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <?
							echo create_drop_down( "cbo_company_id", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select --", $company, "",0 );
                        ?>
                    </td>
                    <td>
                        <?
 							echo create_drop_down( "cbo_buyer_name", 160, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$company' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
                        ?>
                    </td>
                    <td>
                        <?
                        $userCredential = sql_select("SELECT unit_id as company_id, store_location_id, item_cate_id, supplier_id FROM user_passwd where id='$user_id'");
						$store_location_id = $userCredential[0][csf('store_location_id')];
						if ($store_location_id != '') {$store_location_credential_cond = "and a.id in($store_location_id)";} else { $store_location_credential_cond = "";}
 							echo create_drop_down( "cbo_store_name_id", 130, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and a.company_id='$company' and a.status_active=1 and a.is_deleted=0 and b.category_type in(3) $store_location_credential_cond group by a.id, a.store_name order by a.store_name","id,store_name", 1, "-- Select Store --", $selected, "" );
                        ?>
                    </td>
                    <td>
                        <?
                            //$search_by = array(1=>'QC No',2=>'GRN No.',3=>'WO No',4=>'PI No');
                            $search_by = array(1=>'Shrinkage No',2=>'GRN No.',3=>'WO No',4=>'PI No');
							$dd="change_search_event(this.value, '0*0*0*0', '0*0*0*0', '../../') ";

							//$search_by = array(1=>'QC No',2=>'GRN No.');
							//$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 120, $search_by,"",0, "--Select--", "1",$dd,0 );
                        ?>
                    </td>
                    <td width="" align="center" id="search_by_td">
                        <input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                    </td>
                    <td align="center">
                        <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" placeholder="From Date" />
                        <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" placeholder="To Date" />
                    </td>
                    <td align="center">
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_store_name_id').value, 'create_qc_search_list_view', 'search_div', 'woven_finish_fabric_roll_receive_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                    </td>
            </tr>
        	<tr>
            	<td align="center" height="40" valign="middle" colspan="6">
					<? echo load_month_buttons(1);  ?>
                    <!-- Hidden field here -->
                     <input type="hidden" id="hidden_recv_number" value="hidden_recv_number" />
                    <!-- END -->
                </td>
            </tr>
            </tbody>
         </tr>
        </table>
        <div align="center" valign="top" id="search_div"> </div>
        </form>
   </div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
}


if($action=="create_qc_search_list_view")
{
	$ex_data = explode("_",$data);
	$company = $ex_data[0];
	$txt_search_by = $ex_data[1];
	$txt_search_common = $ex_data[2];
	$fromDate = $ex_data[3];
	$toDate = $ex_data[4];
	$buyer = $ex_data[5];
	$store_id = $ex_data[6];


	$sql_cond="";
	if(trim($txt_search_common)!="")
	{
		if(trim($txt_search_by)==1) // for QC
		{
			$sql_cond .= " and e.sys_number LIKE '%$txt_search_common%'";

		}
		else if(trim($txt_search_by)==2) // for chllan no
		{
			$sql_cond .= " and a.recv_number LIKE '%$txt_search_common%'";
 		}
 		else if(trim($txt_search_by)==3) // for WO no
		{
			$sql_cond .= " and a.receive_basis=2 and c.booking_no LIKE '%$txt_search_common%'";
 		}
 		else if(trim($txt_search_by)==4) // for PI no
		{
			$sql_cond .= " and a.receive_basis=1 and c.booking_no LIKE '%$txt_search_common%'";
 		}

 	}

	if( $fromDate!="" && $toDate!="" )
	{
		if($db_type==0)
		{
			$sql_cond .= " and a.receive_date  between '".change_date_format($fromDate,'yyyy-mm-dd')."' and '".change_date_format($toDate,'yyyy-mm-dd')."'";
		}
		if($db_type==2 || $db_type==1)
		{
			$sql_cond .= " and a.receive_date  between '".change_date_format($fromDate,'','','1')."' and '".change_date_format($toDate,'','','1')."'";
		}
	}

	if(trim($company)!="") $sql_cond .= " and a.company_id='$company'";
	if(trim($supplier)!=0) $sql_cond .= " and a.supplier_id='$supplier'";
	if(trim($buyer)!=0) $sql_cond .= " and a.buyer_id='$buyer'";
	if(trim($store_id)!=0) $sql_cond .= " and a.store_id='$store_id'";

	/*$sql ="SELECT a.id, a.qc_number, a.grn_number as recv_number, a.grn_id, a.supplier_id, c.challan_no, c.buyer_id, c.lc_no as lc_number, c.receive_date, sum(b.parking_quantity) as receive_qnty,c.receive_basis,
	case WHEN c.receive_basis=2 THEN c.booking_no ELSE null end as wo_number,case WHEN c.receive_basis=1 THEN c.booking_no ELSE null end as  pi_number  
	from  quarantine_parking_mst a, quarantine_parking_dtls b, inv_receive_master c
	where a.id = b.mst_id and a.entry_form=560 and a.GRN_ID=c.id and c.entry_form=559 $sql_cond
	and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
	group by a.id, a.qc_number, a.grn_number, a.grn_id, a.supplier_id, c.challan_no, c.buyer_id, c.lc_no, c.receive_date,c.receive_basis,c.booking_no";*/



	$sql ="select e.sys_number,e.grn_no as recv_number,a.supplier_id,e.buyer_id,a.challan_no, a.lc_no as lc_number, a.receive_date, sum(c.qnty) as receive_qnty,a.receive_basis,
	case WHEN a.receive_basis=2 THEN a.booking_no ELSE null end as wo_number,case WHEN a.receive_basis=1 THEN a.booking_no ELSE null end as  pi_number from 
	inv_receive_master a ,quarantine_parking_dtls b , pro_roll_details c, woven_shrink_shade_dtls d, woven_shrink_shade_mst e
	where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and c.barcode_no=d.barcode_no and d.mst_id=e.id and e.grn_no=a.recv_number and a.entry_form in (559) and c.entry_form in (559) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $sql_cond and e.id not in(select f.qc_id from inv_receive_master f where e.id=f.qc_id and f.entry_form=564 and f.is_deleted=0 and f.status_active=1)
	group by e.grn_no,e.sys_number,a.supplier_id,e.buyer_id,a.challan_no, a.lc_no, a.receive_date,a.receive_basis,a.booking_no order by e.sys_number desc";


	//echo $sql;
	$supplier_arr = return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$arr=array(2=>$supplier_arr,3=>$buyer_arr,7=>$receive_basis_arr);
	echo create_list_view("list_view", "Shrinkage No,GRN No., Supplier Name,Buyer Name, Challan No, LC No, Receive Date, Receive Basis,WO Number,PI Number ,Receive Qnty","110,110,130,130,120,120,80,80,120,80,80","1200","260",0, $sql , "js_set_value", "sys_number", "", 1, "0,0,supplier_id,buyer_id,0,0,0,receive_basis,0,0,0", $arr, "sys_number,recv_number,supplier_id,buyer_id,challan_no,lc_number,receive_date,receive_basis,wo_number,pi_number,receive_qnty", "",'','0,0,0,0,0,0,3,0,0,0,1') ;
	exit();

}

if($action=="populate_data_from_data")
{
	/*$sql = "SELECT a.id, a.qc_number, a.grn_number, a.grn_id, a.receive_date as qc_date,b.company_id, b.receive_basis, b.booking_id, b.is_audited, b.booking_no, b.booking_without_order, b.location_id, b.challan_date, b.challan_no, b.store_id, b.lc_no, b.supplier_id, b.exchange_rate, b.currency_id, b.lc_no, b.source, b.dyeing_source, b.fabric_source,b.buyer_id, c.pi_basis_id  
	from  quarantine_parking_mst a, inv_receive_master b left join com_pi_master_details c on b.booking_id=c.id
	where a.qc_number='$data' and a.grn_id=b.id and a.entry_form=560 and b.entry_form=559 and a.status_active=1 and a.is_deleted=0";*/



	$sql ="select e.id,e.sys_number as shrinkage_mrr,e.grn_no as grn_number,e.buyer_id, a.lc_no, a.receive_date,a.company_id,a.receive_basis, a.booking_id, a.is_audited, a.booking_no, a.booking_without_order, a.location_id, a.challan_date, a.challan_no, a.store_id, a.supplier_id, a.exchange_rate, a.currency_id, a.source, a.dyeing_source, a.fabric_source, f.pi_basis_id from 
	inv_receive_master a left join com_pi_master_details f on a.booking_id=f.id ,quarantine_parking_dtls b , pro_roll_details c, woven_shrink_shade_dtls d, woven_shrink_shade_mst e
	where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and c.barcode_no=d.barcode_no and d.mst_id=e.id and e.grn_no=a.recv_number and a.entry_form in (559) and c.entry_form in (559) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.sys_number='$data'
	group by e.id,e.sys_number,e.grn_no,e.buyer_id,a.challan_no, a.lc_no, a.receive_date,a.company_id,a.receive_basis, a.booking_id, a.is_audited, a.booking_no, a.booking_without_order, a.location_id, a.challan_date, a.store_id, a.supplier_id, a.exchange_rate, a.currency_id, a.source, a.dyeing_source, a.fabric_source, f.pi_basis_id";



	$res = sql_select($sql);
	foreach($res as $row)
	{
		echo "$('#update_id').val('');\n";
		echo "$('#txt_qc_no').val('".$row[csf("shrinkage_mrr")]."');\n";
		echo "$('#txt_qc_no_id').val(".$row[csf("id")].");\n";
		echo "company_wise_load(".$row[csf("company_id")].");\n";
		echo "$('#cbo_buyer_name').val(".$row[csf("buyer_id")].");\n";
		echo "$('#cbo_company_id').val(".$row[csf("company_id")].");\n";
		echo "$('#cbo_receive_basis').val(".$row[csf("receive_basis")].");\n";
		if($row[csf("receive_basis")]==1 || $row[csf("receive_basis")]==2)
		{
			echo "$('#txt_wo_pi').val('".$row[csf("booking_no")]."');\n";
			echo "$('#txt_wo_pi_id').val(".$row[csf("booking_id")].");\n";
		}
		echo "$('#booking_without_order').val(".$row[csf("booking_without_order")].");\n";
		echo "$('#cbo_location').val(".$row[csf("location_id")].");\n";

		echo "store_change(".$row[csf("location_id")].");\n";

		echo "$('#txt_receive_date').val('".change_date_format($row[csf("receive_date")])."');\n";
		echo "$('#txt_challan_date').val('".change_date_format($row[csf("challan_date")])."');\n";
		
		echo "$('#txt_challan_no').val('".$row[csf("challan_no")]."');\n";
		echo "$('#cbo_store_name').val(".$row[csf("store_id")].");\n";

		
		


		echo "$('#cbo_supplier').val(".$row[csf("supplier_id")].");\n";
		echo "$('#cbo_currency').val(".$row[csf("currency_id")].");\n";
		echo "$('#txt_exchange_rate').val(".$row[csf("exchange_rate")].");\n";
		echo "$('#cbo_source').val(".$row[csf("source")].");\n";
		echo "$('#cbo_dyeing_source').val(".$row[csf("dyeing_source")].");\n";
		echo "$('#hidden_lc_id').val(".$row[csf("lc_no")].");\n";
		echo "$('#fabric_source').val(".$row[csf("fabric_source")].");\n";
		if($row[csf("lc_no")]>0)
		{
			$lcNumber = return_field_value("lc_number","com_btb_lc_master_details","id=".$row[csf("lc_no")]."");
		}
		echo "$('#txt_lc_no').val('".$lcNumber."');\n";

		$addi_info_str = $row[csf("gate_entry_no")]."_".change_date_format($row[csf("addi_challan_date")])."_".change_date_format($row[csf("gate_entry_date")])."_".$row[csf("vehicle_no")]."_".$row[csf("rcvd_book_no")]."_".$row[csf("transporter_name")]."_".change_date_format($row[csf("bill_date")])."_".$row[csf("short_qnty")]."_".$row[csf("excess_qnty")];
		echo "$('#txt_addi_info').val('".$addi_info_str."');\n";

		echo "show_list_view(".$row[csf("receive_basis")]."+'**'+".$row[csf("booking_id")]."+'**'+".$row[csf("booking_without_order")]."+'**'+'".$row[csf("booking_no")]."'+'**'+".$row[csf("id")].",'show_product_listview_qc','list_product_container','requires/woven_finish_fabric_roll_receive_controller','setFilterGrid(\'table_body\',-1);');\n";

		echo "$('#cbo_buyer_name').attr('disabled','true')".";\n";
		if($row[csf("receive_basis")]==1 && $row[csf("pi_basis_id")]==2){
			echo "$('#cbo_buyer_name').removeAttr('disabled','disabled');\n";
		}

        echo "$('#audited').text('');\n";
        if($row[csf("is_audited")]==1) echo "$('#audited').text('Audited');\n";

		echo "fn_load_floor(".$row[csf("store_id")].");\n";
		echo "$('#cbo_company_id').attr('disabled','true')".";\n";
		echo "$('#cbo_receive_basis').attr('disabled','true')".";\n";
		echo "$('#cbo_supplier').attr('disabled','true')".";\n";
		echo "$('#cbo_dyeing_source').attr('disabled','true')".";\n";
		echo "$('#txt_challan_date').attr('disabled','true')".";\n";
		
 	}
	exit();
}

if($action=="show_product_listview_qc")
{
	$ex_data = explode("**",$data);
	$shrinkage_id = $ex_data[4];


 	$composition_arr=array();
 	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from  lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	if(count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
			}
		}
	}

	/*$sql="SELECT a.id as qc_mst_id, b.id as dtls_id, b.grn_dtls_id, b.mst_id, b.wo_pi_id, b.wo_pi_no, b.item_category_id, b.entry_form, b.body_part_id, b.fabric_description_id,b.fabric_ref,b.rd_no,b.weight_type,b.width, b.cutable_width, b.color_name, b.gsm, b.uom, b.parking_quantity, b.qc_qnty, b.qc_reject_qnty, b.rate, b.amount, b.no_of_roll, b.book_currency, b.ile, b.store_id,b.floor_id,b.room,b.rack,b.self, b.bin_box, b.remarks, b.booking_no as grn_booking_no, b.booking_id as grn_booking_id, b.actual_weight, c.id as roll_table_id, c.barcode_no, c.roll_no, c.qnty, c.qc_pass_qnty, c.reject_qnty, c.po_breakdown_id, b.job_no, a.grn_number, a.grn_id, c.rf_id, c.manual_roll_no, c.booking_without_order, d.source, d.company_id, d.exchange_rate
	from quarantine_parking_mst a, quarantine_parking_dtls b, pro_roll_details c, inv_receive_master d
	where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id and a.grn_id=d.id and b.item_category_id=3 and a.id=$QC_ID and a.entry_form=560 and c.entry_form=560 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";*/


	$sql ="select e.id,e.sys_number,e.grn_no as grn_number,a.id as grn_id, b.id as dtls_id, b.grn_dtls_id, b.mst_id, b.wo_pi_id, b.wo_pi_no, b.item_category_id, b.entry_form, b.body_part_id, b.fabric_description_id,b.fabric_ref,b.rd_no,b.weight_type,b.width,d.batch_id, b.cutable_width, b.color_name, b.gsm, b.uom, b.parking_quantity, b.qc_qnty, b.qc_reject_qnty, b.rate, b.amount, b.no_of_roll, b.book_currency, b.ile, b.store_id,b.floor_id,b.room,b.rack,b.self, b.bin_box, b.remarks, c.booking_no as grn_booking_no, c.po_breakdown_id as grn_booking_id,c.job_id, b.actual_weight,d.after_wash_gsm,d.shade,d.width as shrikage_cutable_width, c.id as roll_table_id, c.barcode_no, c.roll_no, c.qnty, c.qc_pass_qnty, c.reject_qnty,b.all_po_ids,b.po_wise_qnty_str, b.job_no, c.rf_id, c.manual_roll_no, c.booking_without_order,c.batch_no, a.source, a.company_id, a.exchange_rate,d.intellocut_roll_no,sum(f.quantity) as pi_qnty from 
	inv_receive_master a ,quarantine_parking_dtls b  left join com_pi_item_details f on b.wo_pi_id=f.pi_id and f.body_part_id=b.body_part_id and f.determination_id=b.fabric_description_id and f.pi_id=b.wo_pi_id and f.color_id=b.color_name and f.cutable_width=b.cutable_width and f.fab_weight=b.gsm and f.dia_width=b.width, pro_roll_details c, woven_shrink_shade_dtls d, woven_shrink_shade_mst e
	where a.id=b.mst_id and b.id=c.dtls_id and a.id=c.mst_id and c.barcode_no=d.barcode_no and d.mst_id=e.id and e.grn_no=a.recv_number and a.entry_form in (559) and c.entry_form in (559) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and e.id=$shrinkage_id 
	group by e.id,e.sys_number,e.grn_no,a.id, b.id, b.grn_dtls_id, b.mst_id, b.wo_pi_id, b.wo_pi_no, b.item_category_id, b.entry_form, b.body_part_id, b.fabric_description_id,b.fabric_ref,b.rd_no,b.weight_type,b.width, b.cutable_width, b.color_name, b.gsm, b.uom, b.parking_quantity, b.qc_qnty, b.qc_reject_qnty, b.rate, b.amount, b.no_of_roll, b.book_currency, b.ile, b.store_id,b.floor_id,b.room,b.rack,b.self, b.bin_box, b.remarks, c.booking_no, c.po_breakdown_id,c.job_id, b.actual_weight,d.after_wash_gsm,d.shade,d.width,d.batch_id, c.id, c.barcode_no, c.roll_no, c.qnty, c.qc_pass_qnty, c.reject_qnty, b.all_po_ids,b.po_wise_qnty_str, b.job_no, c.rf_id, c.manual_roll_no, c.booking_without_order,c.batch_no, a.source, a.company_id, a.exchange_rate,d.intellocut_roll_no order by b.id";




	$result = sql_select($sql);
	$all_booking_no="";
	foreach($result as $row)
	{
		$details_arr[$row[csf("dtls_id")]]["body_part_id"] = $row[csf('body_part_id')];
		$details_arr[$row[csf("dtls_id")]]["lib_yarn_count_deter_id"] = $row[csf('fabric_description_id')];
		$details_arr[$row[csf("dtls_id")]]["gsm"] = $row[csf('gsm')];
		$details_arr[$row[csf("dtls_id")]]["uom"] = $row[csf('uom')];
		$details_arr[$row[csf("dtls_id")]]["width"] = $row[csf('width')];
		$details_arr[$row[csf("dtls_id")]]["shrikage_cutable_width"] = $row[csf('shrikage_cutable_width')];
		$details_arr[$row[csf("dtls_id")]]["weight_type"] = $row[csf('weight_type')];
		$details_arr[$row[csf("dtls_id")]]["cutable_width"] = $row[csf('cutable_width')];
		$details_arr[$row[csf("dtls_id")]]["parking_quantity"] = $row[csf('parking_quantity')];
		$details_arr[$row[csf("dtls_id")]]["qc_qnty"] = $row[csf('qc_qnty')];
		$details_arr[$row[csf("dtls_id")]]["qc_reject_qnty"] = $row[csf('qc_reject_qnty')];
		$details_arr[$row[csf("dtls_id")]]["grn_dtls_id"] = $row[csf('grn_dtls_id')];
		//$details_arr[$row[csf("dtls_id")]]["actual_weight"] = $row[csf('actual_weight')];
		$details_arr[$row[csf("dtls_id")]]["actual_weight"] = $row[csf('after_wash_gsm')];
		$details_arr[$row[csf("dtls_id")]]["grn_booking_no"] = $row[csf('grn_booking_no')];
		$details_arr[$row[csf("dtls_id")]]["grn_booking_id"] = $row[csf('grn_booking_id')];
		$details_arr[$row[csf("dtls_id")]]["intellocut_roll_no"] = $row[csf('intellocut_roll_no')];
		$details_arr[$row[csf("dtls_id")]]["grn_booking_id"] = $row[csf('grn_booking_id')];

		$details_arr[$row[csf("dtls_id")]]["roll_data"] .= $row[csf('all_po_ids')]. "**" . $row[csf('wo_pi_no')]. "**" . $row[csf('roll_no')] . "**" . $row[csf('barcode_no')] . "**" . $row[csf('intellocut_roll_no')]  . "**" . $row[csf('qnty')]  . "**" . $row[csf('qc_pass_qnty')] . "**" . $row[csf('reject_qnty')] . "**" . $row[csf('rf_id')]. "**" . ""  . "**" . $row[csf('roll_table_id')] . "**" . $row[csf('booking_id')] . "**" . $row[csf('grn_booking_no')] . "**" . $row[csf('job_id')] .",";

		//order +'**'+ booking_no +'**'+ roll_no+'**'+barcode+'**'+manual_roll+'**'+grn qnty+'**'+QC qnty+'**'+reject qnty+'**'+rfid+'**'+grn_roll_table_id+'**'+ qc_roll_table_id

		if($row[csf('booking_without_order')]==0)
		{
			$poIdsArr=explode("_",$row[csf('all_po_ids')]);
			foreach ($poIdsArr as $poidStr) {
				$poidStrs=explode("#",$poidStr);
				foreach ($poidStrs as $poids) {
					$all_po_id[$poids] = $poids;
				}
				
			}
			$all_job_id[$row[csf('job_id')]] = $row[csf('job_id')];
			$all_booking_no.="'".$row[csf('grn_booking_no')]."',";
			
		}
		
		$source = $row[csf('source')];
		$company_id = $row[csf('company_id')];
		$exchange_rate = $row[csf('exchange_rate')];
		$currency_id = $row[csf('currency_id')];
	}
	$all_booking_no=chop($all_booking_no,",");
	if(!empty($all_po_id))
	{
	
		$job_sql = sql_select("select a.id, a.po_number, a.job_no_mst, a.job_id, b.style_ref_no,c.booking_no from wo_po_break_down a, wo_po_details_master b ,wo_booking_dtls c where a.job_id=b.id and a.job_no_mst=c.job_no and b.job_no=c.job_no and c.po_break_down_id=a.id and a.id in (".implode(',',$all_po_id).") and b.id in (".implode(',',$all_job_id).") and c.booking_no in ($all_booking_no) group by a.id, a.po_number, a.job_no_mst, a.job_id, b.style_ref_no,c.booking_no ");


		foreach ($job_sql as  $row) 
		{
			$po_ref_arr[$row[csf('job_id')]]['po_number']=$row[csf('po_number')];
			$po_ref_arr[$row[csf('job_id')]]['job_no']=$row[csf('job_no_mst')];
			$po_ref_arr[$row[csf('job_id')]]['job_id']=$row[csf('job_id')];
			$po_ref_arr[$row[csf('job_id')]]['style_ref_no']=$row[csf('style_ref_no')];
		}
	}
	
	$color_name_arr=return_library_array( "select id, color_name from lib_color where status_active=1",'id','color_name');
	$lib_body_part_arr=return_library_array("select id, body_part_full_name from lib_body_part", "id", "body_part_full_name");

	$sql_ile="select standard from variable_inv_ile_standard where source='$source' and company_name='$company_id' and category=3 and status_active=1 and is_deleted=0 order by id";
	//echo "saju1_".$sql;
	$ile_result=sql_select($sql_ile);
	foreach($ile_result as $row)
	{
		// NOTE :- ILE=standard, ILE% = standard/100*rate
		$ile = $row[csf("standard")];
		//$ile_percentage = ( $row[csf("standard")]/100 )*$rate;
		//echo $ile."**".number_format($ile_percentage,$dec_place[3],".","");
		//exit();
	}
	
	//a.id as update_id, b.id as dtls_id, b.grn_dtls_id, b.mst_id, b.wo_pi_id, b.wo_pi_no, b.item_category_id, b.entry_form, b.body_part_id, b.fabric_description_id,b.fabric_ref,b.rd_no,b.weight_type,b.width, b.cutable_width, b.color_name, b.gsm, b.uom, b.parking_quantity, b.qc_qnty, b.qc_reject_qnty, b.rate, b.amount, b.no_of_roll, b.book_currency, b.ile,b.store_id,b.floor_id,b.room,b.rack,b.self, b.bin_box, b.remarks, b.booking_no as grn_booking_no, b.booking_id as grn_booking_id, b.actual_weight, c.id as roll_table_id, c.barcode_no, c.roll_no, c.qnty, c.qc_pass_qnty, c.reject_qnty, c.po_breakdown_id, b.job_no, a.grn_number, a.grn_id, c.rf_id,


	$i=1;
	foreach($result as $row)
	{
		if ($i%2==0)$bgcolor="#E9F3FF";
		else $bgcolor="#FFFFFF";
		$fabric_desc=$lib_body_part_arr[$row[csf('body_part_id')]].', '.$composition_arr[$row[csf('fabric_description_id')]];
		$body_part_id=$row[csf('body_part_id')];

		//Body Part	Fabrication		UOM	Weight	Actual Weight	Weight Type	Full Width	Cut. Width	QC Qty	Reject Qty
		$job_no = $po_ref_arr[$row[csf('job_id')]]['job_no'];
		$style_ref_no = $po_ref_arr[$row[csf('job_id')]]['style_ref_no'];
		$po_number = $po_ref_arr[$row[csf('job_id')]]['po_number'];

		$ile_percentage=0;
		if($ile)
		{
			$ile_percentage = ( $ile/100 )*$row[csf('rate')];
			//echo $ile."**".number_format($ile_percentage,$dec_place[3],".","");
			$ile_percentage = number_format($ile_percentage,2,".","");
		}


		//amount and book currency calculate--------------// 
		$amount = $row[csf("qnty")]*1*($row[csf('rate')]*1+$ile_percentage*1); 
		$bookCurrency = ($row[csf('rate')]*1+$ile_percentage*1)*$exchange_rate*1*$row[csf("qnty")]*1;
		$amount = number_format($amount,2,".","");
		$bookCurrency = number_format($bookCurrency,2,".","");

		?>
		<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick='change_color("<?php echo $i; ?>","#E9F3FF");'>
			<td width="30" align="center"><? echo $i; ?></td>
			<td width="80"><? echo $row[csf("grn_booking_no")]; ?></td>
			<td width="80" align="center"><? echo $job_no; ?></td>
			<td width="80"><? echo $style_ref_no; ?></td>
			
			<td width="80" align="center"><? echo $row[csf("barcode_no")]; ?></td>
			<td width="80"><? echo $row[csf("rf_id")]; ?></td>
			<!-- <td width="80"><? //echo $row[csf("roll_no")]; ?></td> -->

			<td id="shadeTd_<? echo $i; ?>"  style="width: 80px; text-align: center;">
                <?
                    echo create_drop_down( "cboShade_$i", 80, $fabric_shade,"", 1, "-- Select Shade --", $row[csf("shade")], "",1,0,"","","","","","cboShade[]" );	
                ?>
            </td>
			<!-- <td width="80"><? //echo $row[csf("shade")]; ?></td> -->
			<td width="80" align="center"><? echo $row[csf("intellocut_roll_no")]; ?></td>
			<td width="80">
				<input type="text" value="<? echo $row[csf("batch_no")]; ?>" id="txtBatchNo_<? echo $i; ?>" class="text_boxes"  style="width:70px" name="txtBatchNo[]" readonly disabled/>
			</td>
			<td style="word-break: break-all;" width="80"><? echo $body_part[$body_part_id]; ?></td>

			<td width="120"><? echo $fabric_desc;?></td> 
			<td width="80"><? echo $row[csf("fabric_ref")];?></td>
			<td width="80" align="center"><? echo $row[csf("rd_no")];?></td>
			<td width="80" align="center"><? echo $color_name_arr[$row[csf('color_name')]];?></td>
			<td width="80" align="center"><? echo $row[csf("gsm")];?></td>
			<!-- <td width="80"><? //echo $row[csf("actual_weight")];?></td> -->
			<td width="80" align="center"><? echo $row[csf("after_wash_gsm")];?></td>
			<td width="80" align="center"><? echo $fabric_weight_type[$row[csf("weight_type")]]; ?></td>
			<td width="80" align="center"><? echo $row[csf("uom")];?></td>
			<td width="80" align="center"><? echo $row[csf("width")];?></td>
			<td width="80" align="center"><? echo $row[csf("cutable_width")];?></td>
			<td width="80" align="right">
				<? echo $row[csf("qnty")];?>
				<input type="hidden" value="<? echo $row[csf("qnty")];?>" id="recvQty_<? echo $i; ?>" class="text_boxes"  style="width:50px" name="recvQty[]" readonly disabled/>
			</td>
			<!-- <td width="80" align="right">
				<? //echo $row[csf('reject_qnty')];?>
				<input type="hidden" value="<? //echo $row[csf('reject_qnty')];?>" id="txtRejQty_<? //echo $i; ?>" class="text_boxes"  style="width:50px" name="txtRejQty[]" readonly disabled/>
			</td> -->
			<!-- <td width="80" align="right">1<? //echo No of Roll;?></td> -->
			<td width="80" align="right">
				<? echo $row[csf('rate')];?>
				<input type="hidden" value="<? echo $row[csf('rate')];?>" id="txtRate_<? echo $i; ?>" class="text_boxes"  style="width:50px" name="txtRate[]" readonly disabled/>
			</td>
			<td width="80" align="right">
				<? echo $amount;?>
				<input type="hidden" value="<? echo $amount;?>" id="txtAmount_<? echo $i; ?>" class="text_boxes"  style="width:50px" name="txtAmount[]" readonly disabled/>
			</td>
			<td width="80" align="right">
				<? echo $bookCurrency;?>
				<input type="hidden" value="<? echo $bookCurrency;?>" id="txtBookCurrency_<? echo $i; ?>" class="text_boxes_numeric"  style="width:60px" name="txtBookCurrency[]" readonly disabled/>
			</td>
			<td width="80" align="right">
				<? echo $row[csf('pi_qnty')];?>
				<input type="hidden" value="<? echo $row[csf('pi_qnty')];?>" id="txtBalancePI_<? echo $i; ?>" class="text_boxes_numeric"  style="width:60px" name="txtBalancePI[]" readonly disabled/>
			</td>
			<td width="80" align="right">
				<? echo $ile_percentage;?>
				<input type="hidden" value="<? echo number_format($ile_percentage,2,".","");?>" id="txtIle_<? echo $i; ?>" class="text_boxes_numeric"  style="width:55px" name="txtIle[]" readonly disabled/>
			</td>					


			<td width="80" align="center" id="floorTd_<? echo $i; ?>" class="floor_td_to">
				<? 
				$argument = "'".$i.'_0'."'";					
				echo create_drop_down( "cboFloor_".$i, 60,$blank_array,"", 1, "--Select--", 0, "fn_load_room(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'cboFloor');",0,"","","","","","","cboFloor[]" ,"onchange_void"); ?>
			</td>
			<td width="80" align="center" id="roomTd_<? echo $i; ?>" >
				<? 
				$argument = "'".$i.'_1'."'";
				echo create_drop_down( "cboRoom_".$i, 60,$blank_array,"", 1, "--Select--", 0, "fn_load_rack(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'cboRoom');",0,"","","","","","","cboRoom[]","onchange_void" ); ?>		            
			</td>
			<td width="80" align="center" id="rackTd_<? echo $i; ?>" >
				<? 
				$argument = "'".$i.'_2'."'";
				echo create_drop_down( "txtRack_".$i, 60,$blank_array,"", 1, "--Select--", 0, "fn_load_shelf(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'txtRack');",0,"","","","","","","txtRack[]","onchange_void" ); ?>
			</td>
			<td width="80" align="center" id="shelfTd_<? echo $i; ?>" >
				<? 
				$argument = "'".$i.'_3'."'";
				echo create_drop_down( "txtShelf_".$i, 60,$blank_array,"", 1, "--Select--", 0, "fn_load_bin(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'txtShelf');",0,"","","","","","","txtShelf[]","onchange_void" ); ?>
			</td>
			<td width="80" align="center" id="binTd_<? echo $i; ?>" >
				<? 
				$argument = "'".$i.'_4'."'";
				echo create_drop_down( "txtBin_".$i, 60,$blank_array,"", 1, "--Select--", 0, "copy_all($argument);",0,"","","","","","","txtBin[]","onchange_void" ); ?>
			</td>
			<td width="80">
				<input type="text" id="txtRemarks_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtRemarks[]" />
				
				<input type="hidden" value="<? echo $row[csf("barcode_no")];?>" id="txtBarcodeNo_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtBarcodeNo[]" />
				<input type="hidden" value="<? echo $row[csf("rf_id")];?>" id="txtRFId_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtRFId[]" />
				<input type="hidden" value="<? echo $row[csf("roll_no")];?>" id="txtRollNo_<? //echo $i; ?>" class="text_boxes"  style="width:60px" name="txtRollNo[]" />
				<input type="hidden" value="<? echo $row[csf("shade")];?>" id="txtShade_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtShade[]" />
				<input type="hidden" value="<? echo $row[csf("intellocut_roll_no")];?>" id="txtManualRollNo_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtManualRollNo[]" />
				<input type="hidden" value="<? echo $row[csf("batch_id")]; ?>" id="txtBatchId_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtBatchId[]" />
				<input type="hidden" value="<? echo $body_part_id;?>" id="cboBodyPart_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="cboBodyPart[]" />
				<input type="hidden" value="" id="txtProdId_<? echo $i; ?>" class="text_boxes" name="txtProdId[]" />
				
				<input type="hidden" value="<? echo $row[csf('fabric_description_id')];?>" id="fabricDescId_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="fabricDescId[]" />
				<input type="hidden" value="<? echo $fabric_desc;?>" id="txtFabricDescription_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtFabricDescription[]" />
				
				<input type="hidden" value="<? echo $row[csf("fabric_ref")];?>" id="txtFabricRef_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtFabricRef[]" />
				
				<input type="hidden" value="<? echo $row[csf("rd_no")];?>" id="txtRDNo_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtRDNo[]" />
				
				<input type="hidden" value="<? echo $row[csf('color_name')];?>" id="txtColor_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtColor[]" />

				<input type="hidden" value="<? echo $row[csf("gsm")];?>" id="txtGsm_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtGsm[]" />

				<!-- <input type="hidden" value="<? //echo $row[csf("actual_weight")];?>" id="txtActualWgt_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtActualWgt[]" /> -->
				<input type="hidden" value="<? echo $row[csf("after_wash_gsm")];?>" id="txtActualWgt_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtActualWgt[]" />
				
				<input type="hidden" value="<? echo $row[csf("weight_type")];?>" id="txtWeightType_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtWeightType[]" />
				
				<input type="hidden" value="<? echo $row[csf("uom")];?>" id="cboUOM_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="cboUOM[]" />
				<input type="hidden" value="<? echo $row[csf("width")];?>" id="txtDiaWidth_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtDiaWidth[]" />
				<input type="hidden" value="<? echo $row[csf("cutable_width")];?>" id="txtCutableWidth_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtCutableWidth[]" />
				


				<input type="hidden" value="" id="hiddenPIId_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="hiddenPIId[]" />

				<input type="hidden" value="<? echo $row[csf("grn_booking_no")];?>" id="hdnBookingNo_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="hdnBookingNo[]" />
				<input type="hidden" value="<? echo $row[csf("grn_booking_id")];?>" id="hdnBookingID_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="hdnBookingID[]" />
				<input type="hidden" value="<? echo $row[csf('all_po_ids')];?>" id="hdnPOID_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="hdnPOID[]" />
				<input type="hidden" value="<? echo $row[csf('job_id')];?>" id="hdnJOBID_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="hdnJOBID[]" />
				<input type="hidden" value="<? echo $row[csf('po_wise_qnty_str')];?>" id="hdnPOwiseQntyStr_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="hdnPOwiseQntyStr[]" />
				
				
				<input type="hidden" value="<? //?>" id="distributionMethodId_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="distributionMethodId[]" />
				<input type="hidden" value="<? //?>" id="txtDeletedId_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtDeletedId[]" />
				<input type="hidden" value="" id="updateDtlsId_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="updateDtlsId[]" />
				
			</td>			
		</tr>
		<?
		$i++;
	}
	exit();
}

if($action=="populate_data_from_data_update")
{
	$sql = "SELECT a.id, a.recv_number, a.company_id, a.receive_basis, a.booking_id, a.is_audited, a.booking_no,a.booking_without_order, a.location_id, a.receive_date,a.challan_date, a.challan_no, a.store_id, a.lc_no, a.supplier_id, a.exchange_rate, a.currency_id, a.lc_no,a.source,a.dyeing_source,a.fabric_source,a.buyer_id,a.gate_entry_no,a.addi_challan_date,a.gate_entry_date,a.vehicle_no,a.rcvd_book_no,a.transporter_name,a.bill_date,a.short_qnty,a.excess_qnty,b.pi_basis_id, a.qc_name, a.qc_id from inv_receive_master a left join com_pi_master_details b on a.booking_id = b.id  where a.recv_number='$data' and a.entry_form=564 and a.status_active=1 and a.is_deleted=0";

	$res = sql_select($sql);
	foreach($res as $row)
	{
		echo "$('#update_id').val(".$row[csf("id")].");\n";
		echo "$('#txt_qc_no').val('".$row[csf("qc_name")]."');\n";
		echo "$('#txt_qc_no_id').val(".$row[csf("qc_id")].");\n";
		echo "company_wise_load(".$row[csf("company_id")].");\n";
		echo "$('#cbo_buyer_name').val(".$row[csf("buyer_id")].");\n";
		echo "$('#cbo_company_id').val(".$row[csf("company_id")].");\n";
		echo "$('#cbo_receive_basis').val(".$row[csf("receive_basis")].");\n";
		if($row[csf("receive_basis")]==1 || $row[csf("receive_basis")]==2)
		{
			echo "$('#txt_wo_pi').val('".$row[csf("booking_no")]."');\n";
			echo "$('#txt_wo_pi_id').val(".$row[csf("booking_id")].");\n";
		}
		echo "$('#booking_without_order').val(".$row[csf("booking_without_order")].");\n";
		echo "$('#cbo_location').val(".$row[csf("location_id")].");\n";
		
		echo "store_change(".$row[csf("location_id")].");\n";
		echo "$('#cbo_store_name').val(".$row[csf("store_id")].");\n";

		echo "$('#txt_receive_date').val('".change_date_format($row[csf("receive_date")])."');\n";
		echo "$('#txt_challan_date').val('".change_date_format($row[csf("challan_date")])."');\n";
		
		echo "$('#txt_challan_no').val('".$row[csf("challan_no")]."');\n";


		echo "$('#cbo_supplier').val(".$row[csf("supplier_id")].");\n";
		echo "$('#cbo_currency').val(".$row[csf("currency_id")].");\n";
		echo "$('#txt_exchange_rate').val(".$row[csf("exchange_rate")].");\n";
		echo "$('#cbo_source').val(".$row[csf("source")].");\n";
		echo "$('#cbo_dyeing_source').val(".$row[csf("dyeing_source")].");\n";
		echo "$('#hidden_lc_id').val(".$row[csf("lc_no")].");\n";
		echo "$('#fabric_source').val(".$row[csf("fabric_source")].");\n";
		if($row[csf("lc_no")]>0)
		{
			$lcNumber = return_field_value("lc_number","com_btb_lc_master_details","id=".$row[csf("lc_no")]."");
		}
		echo "$('#txt_lc_no').val('".$lcNumber."');\n";

		echo "show_list_view(".$row[csf("receive_basis")]."+'**'+".$row[csf("booking_id")]."+'**'+".$row[csf("booking_without_order")]."+'**'+'".$row[csf("booking_no")]."'+'**'+".$row[csf("id")].",'show_product_listview_qc_update','list_product_container','requires/woven_finish_fabric_roll_receive_controller','setFilterGrid(\'table_body\',-1);');\n";

		echo "$('#cbo_buyer_name').attr('disabled','true')".";\n";
		if($row[csf("receive_basis")]==1 && $row[csf("pi_basis_id")]==2){
			echo "$('#cbo_buyer_name').removeAttr('disabled','disabled');\n";
		}

        echo "$('#audited').text('');\n";
        if($row[csf("is_audited")]==1) echo "$('#audited').text('Audited');\n";

		echo "$('#cbo_company_id').attr('disabled','true')".";\n";
		echo "$('#cbo_receive_basis').attr('disabled','true')".";\n";
		echo "$('#cbo_supplier').attr('disabled','true')".";\n";
		echo "$('#cbo_dyeing_source').attr('disabled','true')".";\n";
		echo "$('#txt_challan_date').attr('disabled','true')".";\n";
		
 	}
	exit();
}


if($action=="show_product_listview_qc_update")
{
	$ex_data = explode("**",$data);
	$RCV_ID = $ex_data[4];

 	$composition_arr=array();
 	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from  lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	if(count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
			}
		}
	}

	$sql= "SELECT a.company_id,a.location_id,b.store_id, a.currency_id, a.exchange_rate, a.booking_without_order, a.booking_no, b.id as trans_id,b.cutting_unit_no,b.roll,b.remarks, b.receive_basis, b.pi_wo_batch_no, b.prod_id, b.brand_id,b.batch_lot, c.detarmination_id,c.color,c.dia_width,c.weight, b.order_uom, e.qnty as recv_qnty,b.body_part_id, b.order_rate, b.order_ile_cost,b.batch_id, b.order_amount,b.cons_amount,b.no_of_bags,b.product_code,b.floor_id,b.room,b.rack, b.self,b.bin_box, d.id as details_id,d.buyer_id, d.booking_no as pi_booking_no, d.booking_id as pi_booking_id,d.original_width,d.original_gsm, d.fabric_description_id, d.width, b.fabric_ref,b.rd_no,b.weight_type,b.cutable_width,b.weight_editable,b.width_editable, e.id as roll_table_id, e.po_breakdown_id, e.barcode_no, e.roll_no,e.shrinkage_shade, e.manual_roll_no, e.rf_id, e.reject_qnty,e.job_id FROM inv_receive_master a, inv_transaction b, product_details_master c, pro_finish_fabric_rcv_dtls d,pro_roll_details e where a.id=b.mst_id and b.prod_id=c.id and a.id=d.mst_id and b.id=d.trans_id and a.id=e.mst_id and d.id=e.dtls_id and e.entry_form=564 and a.id=$RCV_ID and a.entry_form=564 and a.entry_form=564 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and b.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0  ";
	

	$result = sql_select($sql);

	foreach($result as $row)
	{
		if($row[csf('booking_without_order')]==0)
		{
			$all_job_id[$row[csf('job_id')]] = $row[csf('job_id')];
			//$all_po_id[$row[csf('job_id')]] = $row[csf('job_id')];
		}

		$source = $row[csf('source')];
		$company_id = $row[csf('company_id')];
		$exchange_rate = $row[csf('exchange_rate')];
		$currency_id = $row[csf('currency_id')];
		$store_id = $row[csf('store_id')];
	}

	if(!empty($all_job_id))
	{
		$job_sql = sql_select("select a.id, a.po_number, a.job_no_mst, a.job_id, b.style_ref_no from wo_po_break_down a, wo_po_details_master b where a.job_id=b.id and b.id in (".implode(',',$all_job_id).")");
		foreach ($job_sql as  $row) 
		{
			$job_ref_arr[$row[csf('job_id')]]['po_number']=$row[csf('po_number')];
			$job_ref_arr[$row[csf('job_id')]]['job_no']=$row[csf('job_no_mst')];
			$job_ref_arr[$row[csf('job_id')]]['job_id']=$row[csf('job_id')];
			$job_ref_arr[$row[csf('job_id')]]['style_ref_no']=$row[csf('style_ref_no')];
		}
	}
	
	$color_name_arr=return_library_array( "select id, color_name from lib_color where status_active=1",'id','color_name');
	$lib_body_part_arr=return_library_array("select id, body_part_full_name from lib_body_part", "id", "body_part_full_name");
	
	$lib_room_rack_shelf_sql = "select b.company_id,b.location_id, b.store_id,b.floor_id,b.room_id, b.rack_id,b.shelf_id,b.bin_id, a.floor_room_rack_name floor_name, c.floor_room_rack_name room_name, d.floor_room_rack_name rack_name, e.floor_room_rack_name shelf_name , f.floor_room_rack_name bin_name 
		from lib_floor_room_rack_dtls b
		left join lib_floor_room_rack_mst a on b.floor_id=a.floor_room_rack_id and a.status_active=1 and a.is_deleted=0
		left join lib_floor_room_rack_mst c on b.room_id=c.floor_room_rack_id and c.status_active=1 and c.is_deleted=0
		left join lib_floor_room_rack_mst d on b.rack_id=d.floor_room_rack_id and d.status_active=1 and d.is_deleted=0
		left join lib_floor_room_rack_mst e on b.shelf_id=e.floor_room_rack_id and e.status_active=1 and e.is_deleted=0
		left join lib_floor_room_rack_mst f on b.bin_id=f.floor_room_rack_id and f.status_active=1 and f.is_deleted=0
		where b.status_active=1 and b.is_deleted=0 and b.store_id=$store_id and b.company_id =$company_id";
	$lib_floor_room_data_arr=sql_select($lib_room_rack_shelf_sql);
	foreach ($lib_floor_room_data_arr as $room_rack_shelf_row) 
	{
		$company  = $room_rack_shelf_row[csf("company_id")];
		$store_id   = $room_rack_shelf_row[csf("store_id")];
		$floor_id = $room_rack_shelf_row[csf("floor_id")];
		$room_id  = $room_rack_shelf_row[csf("room_id")];
		$rack_id  = $room_rack_shelf_row[csf("rack_id")];
		$shelf_id = $room_rack_shelf_row[csf("shelf_id")];
		$bin_id   = $room_rack_shelf_row[csf("bin_id")];

		if($floor_id!="" && $room_id=="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
			//$lib_floor_arr[$store_id] .= $floor_id.",";
			$lib_floor_arr[$store_id][$floor_id] = $room_rack_shelf_row[csf("floor_name")];
		}

		if($floor_id!="" && $room_id!="" && $rack_id=="" && $shelf_id=="" && $bin_id==""){
			//$lib_room_arr[$store_id][$floor_id] .= $room_id.",";
			$lib_room_arr[$store_id][$floor_id][$room_id] = $room_rack_shelf_row[csf("room_name")];
		}

		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id=="" && $bin_id==""){
			//$lib_rack_arr[$store_id][$floor_id][$room_id] .= $rack_id.",";
			$lib_rack_arr[$store_id][$floor_id][$room_id][$rack_id] =  $room_rack_shelf_row[csf("rack_name")];
		}

		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id==""){
			//$lib_shelf_arr[$store_id][$floor_id][$room_id][$rack_id] .= $shelf_id.",";
			$lib_shelf_arr[$store_id][$floor_id][$room_id][$rack_id][$shelf_id] =  $room_rack_shelf_row[csf("shelf_name")];
		}

		if($floor_id!="" && $room_id!="" && $rack_id!="" && $shelf_id!="" && $bin_id!=""){
			//$lib_bin_arr[$store_id][$floor_id][$room_id][$rack_id][$shelf_id].= $bin_id.",";
			$lib_bin_arr[$store_id][$floor_id][$room_id][$rack_id][$shelf_id][$bin_id] =  $room_rack_shelf_row[csf("bin_name")];
		}
	}
	unset($lib_floor_room_data_arr);


	$i=1;
	foreach($result as $row)
	{
		if ($i%2==0)$bgcolor="#E9F3FF";
		else $bgcolor="#FFFFFF";
		$fabric_desc=$lib_body_part_arr[$row[csf('body_part_id')]].', '.$composition_arr[$row[csf('fabric_description_id')]];
		$body_part_id=$row[csf('body_part_id')];

		$job_no = $job_ref_arr[$row[csf('job_id')]]['job_no'];
		$style_ref_no = $job_ref_arr[$row[csf('job_id')]]['style_ref_no'];
		$po_number = $job_ref_arr[$row[csf('job_id')]]['po_number'];

		$bookCurrency = number_format($row[csf("cons_amount")],2,'.','');
		?>
		<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick='change_color("<?php echo $i; ?>","#E9F3FF");'>
			<td width="30" align="center"><? echo $i; ?></td>
			<td width="80"><? echo $row[csf("pi_booking_no")]; ?></td>
			<td width="80"><? echo $job_no; ?></td>
			<td width="80"><? echo $style_ref_no; ?></td>
			<!-- <td width="80"><? //echo $po_number; ?></td> -->
			<td width="80"><? echo $row[csf("barcode_no")]; ?></td>
			<td width="80"><? echo $row[csf("rf_id")]; ?></td>
			<!-- <td width="80"><? //echo $row[csf("roll_no")]; ?></td> -->

			<td id="shadeTd_<? echo $i; ?>"  style="width: 80px; text-align: center;">
                <?
                    echo create_drop_down( "cboShade_$i", 80, $fabric_shade,"", 1, "-- Select Shade --", $row[csf("shrinkage_shade")], "",1,0,"","","","","","cboShade[]" );	
                ?>
            </td>


			<!-- <td width="80" align="center"><? //echo $fabric_shade[$row[csf("shrinkage_shade")]]; ?></td> -->
			<td width="80"><? echo $row[csf("manual_roll_no")]; ?></td>
			<td width="80">
				<input type="text" value="<? echo $row[csf("batch_lot")];?>" id="txtBatchNo_<? echo $i; ?>" class="text_boxes"  style="width:50px" name="txtBatchNo[]" readonly disabled/>
			</td>
			<td width="80" style="word-break:break-all;"><? echo $body_part[$body_part_id]; ?></td>

			<td width="120"><? echo $fabric_desc;?></td> 
			<td width="80"><? echo $row[csf("fabric_ref")];?></td>
			<td width="80"><? echo $row[csf("rd_no")];?></td>
			<td width="80"><? echo $color_name_arr[$row[csf("color")]];?></td>
			<td width="80"><? echo $row[csf("original_gsm")];?></td>
			<td width="80"><? echo $row[csf("weight")];?></td>
			<td width="80"><? echo $fabric_weight_type[$row[csf("weight_type")]]; ?></td>
			<td width="80"><? echo $unit_of_measurement[$row[csf("order_uom")]];?></td>
			<td width="80"><? echo $row[csf("width")];?></td>
			<td width="80"><? echo $row[csf("cutable_width")];?></td>
			<td width="80" align="right">
				<? echo number_format($row[csf("recv_qnty")],2,".","");?>
				<input type="hidden" value="<? echo number_format($row[csf("recv_qnty")],2,".","");?>" id="recvQty_<? echo $i; ?>" class="text_boxes"  style="width:50px" name="recvQty[]" readonly disabled/>
			</td>
			<!-- <td width="80" align="right">
				<? //echo $row[csf('reject_qnty')];?>
				<input type="hidden" value="<? //echo $row[csf('reject_qnty')];?>" id="txtRejQty_<? echo $i; ?>" class="text_boxes"  style="width:50px" name="txtRejQty[]" readonly disabled/>
			</td> -->
			<!-- <td width="80" align="right">1<? //echo No of Roll;?></td> -->
			<td width="80" align="right">
				<? echo $row[csf("order_rate")];?>
				<input type="hidden" value="<? echo $row[csf("order_rate")];?>" id="txtRate_<? echo $i; ?>" class="text_boxes"  style="width:50px" name="txtRate[]" readonly disabled/>
			</td>
			<td width="80" align="right">
				<? echo $row[csf("order_amount")];?>
				<input type="hidden" value="<? echo $row[csf("order_amount")];?>" id="txtAmount_<? echo $i; ?>" class="text_boxes"  style="width:50px" name="txtAmount[]" readonly disabled/>
			</td>
			<td width="80" align="right">
				<? echo $row[csf("cons_amount")];?>
				<input type="hidden" value="<? echo $row[csf("cons_amount")];?>" id="txtBookCurrency_<? echo $i; ?>" class="text_boxes_numeric"  style="width:60px" name="txtBookCurrency[]" readonly disabled/>
			</td>
			<td width="80" align="right">
				<? //echo $bookCurrency;?>
				<input type="hidden" value="<? //echo $bookCurrency;?>" id="txtBalancePI_<? echo $i; ?>" class="text_boxes_numeric"  style="width:60px" name="txtBalancePI[]" readonly disabled/>
			</td>
			<td width="80" align="right">
				<? echo $row[csf("order_ile_cost")];?>
				<input type="hidden" value="<? echo number_format($row[csf("order_ile_cost")],2,".","");?>" id="txtIle_<? echo $i; ?>" class="text_boxes_numeric"  style="width:55px" name="txtIle[]" readonly disabled/>
			</td>					


			<td width="80" align="center" id="floorTd_<? echo $i; ?>" class="floor_td_to">
				<? 
				$argument = "'".$i.'_0'."'";					
				echo create_drop_down( "cboFloor_".$i, 60,$lib_floor_arr[$store_id],"", 1, "--Select--", $row[csf("floor_id")], "fn_load_room(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'cboFloor');",0,"","","","","","","cboFloor[]" ,"onchange_void"); 
				
				?>
			</td>
			<td width="80" align="center" id="roomTd_<? echo $i; ?>" >
				<? 
				$argument = "'".$i.'_1'."'";
				echo create_drop_down( "cboRoom_".$i, 60,$lib_room_arr[$store_id][$row[csf("floor_id")]],"", 1, "--Select--", $row[csf("room")], "fn_load_rack(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'cboRoom');",0,"","","","","","","cboRoom[]","onchange_void" ); ?>		            
			</td>
			<td width="80" align="center" id="rackTd_<? echo $i; ?>" >
				<? 
				$argument = "'".$i.'_2'."'";
				echo create_drop_down( "txtRack_".$i, 60,$lib_rack_arr[$store_id][$row[csf("floor_id")]][$row[csf("room")]],"", 1, "--Select--", $row[csf("rack")], "fn_load_shelf(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'txtRack');",0,"","","","","","","txtRack[]","onchange_void" ); ?>
			</td>
			<td width="80" align="center" id="shelfTd_<? echo $i; ?>" >
				<? 
				$argument = "'".$i.'_3'."'";
				echo create_drop_down( "txtShelf_".$i, 60,$lib_shelf_arr[$store_id][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]],"", 1, "--Select--", $row[csf("self")], "fn_load_bin(this.value, $i); copy_all($argument); reset_room_rack_shelf($i,'txtShelf');",0,"","","","","","","txtShelf[]","onchange_void" ); ?>
			</td>
			<td width="80" align="center" id="binTd_<? echo $i; ?>" >
				<? 
				$argument = "'".$i.'_4'."'";
				echo create_drop_down( "txtBin_".$i, 60,$lib_bin_arr[$store_id][$row[csf("floor_id")]][$row[csf("room")]][$row[csf("rack")]][$row[csf("self")]],"", 1, "--Select--", $row[csf("bin_box")], "copy_all($argument);",0,"","","","","","","txtBin[]","onchange_void" ); ?>
			</td>
			<td width="80">
				<input type="text" value="<? echo $row[csf("remarks")];?>" id="txtRemarks_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtRemarks[]" />
				
				<input type="hidden" value="<? echo $row[csf("barcode_no")];?>" id="txtBarcodeNo_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtBarcodeNo[]" />
				<input type="hidden" value="<? echo $row[csf("rf_id")];?>" id="txtRFId_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtRFId[]" />
				<input type="hidden" value="<? echo $row[csf("roll_no")];?>" id="txtRollNo_<? //echo $i; ?>" class="text_boxes"  style="width:60px" name="txtRollNo[]" />
				<input type="hidden" value="<? echo $row[csf("shrinkage_shade")];?>" id="txtShade_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtShade[]" />
				<input type="hidden" value="<? echo $row[csf("manual_roll_no")];?>" id="txtManualRollNo_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtManualRollNo[]" />
				<input type="hidden" value="<? echo $row[csf("batch_id")];?>" id="txtBatchId_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtBatchId[]" />
				<input type="hidden" value="<? echo $body_part_id;?>" id="cboBodyPart_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="cboBodyPart[]" />
				<input type="hidden" value="<? echo $row[csf("prod_id")];?>" id="txtProdId_<? echo $i; ?>" class="text_boxes" name="txtProdId[]" />
				
				<input type="hidden" value="<? echo $row[csf('fabric_description_id')];?>" id="fabricDescId_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="fabricDescId[]" />
				<input type="hidden" value="<? echo $fabric_desc;?>" id="txtFabricDescription_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtFabricDescription[]" />
				
				<input type="hidden" value="<? echo $row[csf("fabric_ref")];?>" id="txtFabricRef_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtFabricRef[]" />
				
				<input type="hidden" value="<? echo $row[csf("rd_no")];?>" id="txtRDNo_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtRDNo[]" />
				
				<input type="hidden" value="<? echo $row[csf("color")];?>" id="txtColor_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtColor[]" />

				<input type="hidden" value="<? echo $row[csf("original_gsm")];?>" id="txtGsm_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtGsm[]" />

				<input type="hidden" value="<? echo $row[csf("weight")];?>" id="txtActualWgt_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtActualWgt[]" />
				
				<input type="hidden" value="<? echo $row[csf("weight_type")];?>" id="txtWeightType_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtWeightType[]" />
				
				<input type="hidden" value="<? echo $row[csf("order_uom")];?>" id="cboUOM_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="cboUOM[]" />
				<input type="hidden" value="<? echo $row[csf("width")];?>" id="txtDiaWidth_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtDiaWidth[]" />
				<input type="hidden" value="<? echo $row[csf("cutable_width")];?>" id="txtCutableWidth_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtCutableWidth[]" />
				


				<input type="hidden" value="" id="hiddenPIId_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="hiddenPIId[]" />

				<input type="hidden" value="<? echo $row[csf("pi_booking_no")];?>" id="hdnBookingNo_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="hdnBookingNo[]" />
				<input type="hidden" value="<? echo $row[csf("pi_booking_id")];?>" id="hdnBookingID_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="hdnBookingID[]" />
				<input type="hidden" value="<? //echo $row[csf('po_breakdown_id')];?>" id="hdnPOID_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="hdnPOID[]" />
				<input type="hidden" value="<? echo $row[csf('job_id')];?>" id="hdnJOBID_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="hdnJOBID[]" />
				
				<input type="hidden" value="<? //echo $row[csf('po_wise_qnty_str')];?>" id="hdnPOwiseQntyStr_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="hdnPOwiseQntyStr[]" />

				<input type="hidden" value="<? //?>" id="distributionMethodId_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="distributionMethodId[]" />
				<input type="hidden" value="<? //?>" id="txtDeletedId_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="txtDeletedId[]" />
				<input type="hidden" value="<? echo $row[csf("details_id")];?>" id="updateDtlsId_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="updateDtlsId[]" />
				<input type="hidden" value="<? echo $row[csf("roll_table_id")];?>" id="updateRollTableId_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="updateRollTableId[]" />
				<input type="hidden" value="<? echo $row[csf("trans_id")];?>" id="updateTransId_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="updateTransId[]" />
			</td>			
		</tr>
		<?
		$i++;
	}
	exit();
}

if($action=="show_dtls_list_view")
{
	$ex_data = explode("**",$data);
	$recv_number = $ex_data[0];
	$rcv_mst_id = $ex_data[1];
	$cond="";
	if($recv_number!="") $cond .= " and a.recv_number='$recv_number'";
	if($rcv_mst_id!="") $cond .= " and a.id='$rcv_mst_id'";

	$sql = "SELECT a.id as rcv_mst_id, a.recv_number, b.id as transection_id, b.receive_basis, b.pi_wo_batch_no, c.product_name_details, c.lot, b.order_uom, b.order_qnty, b.order_rate, b.order_ile_cost, b.order_amount, b.cons_amount,b.batch_lot,d.id as pro_roll_dtls_id,d.no_of_roll 
	from inv_receive_master a, inv_transaction b, product_details_master c, pro_finish_fabric_rcv_dtls d
	where a.id=b.mst_id and a.id=d.mst_id and b.prod_id=c.id and b.id=d.trans_id and b.transaction_type=1 and b.item_category=3 and a.entry_form=17 $cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0";

	$result = sql_select($sql);
	$i=1;
	$totalQnty=0;
	$totalAmount=0;
	$totalbookCurr=0;
	?>
    	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="970">
        	<thead>
            	<tr>
                	<th>SL</th>
                    <th>WO/PI No</th>
                    <th>MRR No</th>
                    <th>Product Details</th>
                    <th>Batch/Lot</th>
                    <th>UOM</th>
                    <th>Receive Qty</th>
                    <th>Rate</th>
                    <th>Roll</th>
                    <th>ILE Cost</th>
                    <th>Amount</th>
                    <th>Book Currency</th>
                </tr>
            </thead>
            <tbody>
            	<?
				    foreach($result as $row)
					{
					if ($i%2==0)$bgcolor="#E9F3FF";
					else $bgcolor="#FFFFFF";
					$wopi="";
					if($row[csf("receive_basis")]==1)
						$wopi=return_field_value("pi_number","com_pi_master_details","id=".$row[csf("pi_wo_batch_no")]."");
					else if($row[csf("receive_basis")]==2)
						$wopi=return_field_value("booking_no","wo_booking_mst","id=".$row[csf("pi_wo_batch_no")]."");
					$totalQnty +=$row[csf("order_qnty")];
					$totalAmount +=$row[csf("order_amount")];
					$totalbookCurr +=$row[csf("cons_amount")];
 				?>
                	<tr bgcolor="<? echo $bgcolor; ?>" onClick="fn_fabric_descriptin_variable_check();put_data_dtls_part('<? echo $row[csf('rcv_mst_id')].'**'.$wopi."**". $row[csf('recv_number')]."**".$row[csf('pro_roll_dtls_id')]."**".$row[csf('transection_id')]."**".$row[csf('body_part_id')]."**".$row[csf('receive_basis')]."**".$row[csf('pi_wo_batch_no')];?>','child_form_input_data','requires/woven_finish_fabric_roll_receive_controller')" style="cursor:pointer" >

                        <td width="30"><?php echo $i; ?></td>
                        <td width="100"><p><?php echo $wopi; ?></p></td>
                        <td width="100"><p><?php echo $row[csf("recv_number")]; ?></p></td>
                        <td width="200"><p><?php echo $row[csf("product_name_details")]; ?></p></td>
                        <td width="80"><p><?php echo $row[csf("batch_lot")]; ?></p></td>
                        <td width="70"><p><?php echo $unit_of_measurement[$row[csf("order_uom")]]; ?></p></td>
                        <td width="80" align="right"><p><?php echo number_format($row[csf("order_qnty")], 2); ?></p></td>
                        <td width="60" align="right"><p><?php echo $row[csf("order_rate")]; ?></p></td>
                        <td width="60" align="right"><p><?php echo $row[csf("no_of_roll")]; ?></p></td>
                        <td width="70" align="right"><p><?php echo $row[csf("order_ile_cost")]; ?></p></td>
                        <td width="70" align="right"><p><?php echo number_format($row[csf("order_amount")], 2); ?></p></td>
                        <td width="80" align="right"><p><?php echo number_format($row[csf("cons_amount")], 2); ?></p></td>
                   </tr>
                   <? $i++; } ?>
                	<tfoot>
                        <th colspan="6">Total</th>
                        <th><?php echo number_format($totalQnty, 2); ?></th>
                        <th colspan="2"></th>
                        <th><?php echo number_format($totalAmount, 2); ?></th>
                        <th><?php echo number_format($totalbookCurr, 2); ?></th>
                        <th></th>
                  </tfoot>
            </tbody>
        </table>
    <?
	exit();
}


if($action=="child_form_input_data")
{
	$rcv_dtls_id = explode("**",$data);

	/*[0] => 25838
    [1] => RpC-Fb-18-00020
    [2] => RpC-WFR-18-00012
    [3] => 3117*/

	//echo $rcv_dtls_id[0].'/';
	//echo $rcv_dtls_id[1];
	// and b.body_part_id=$rcv_dtls_id[5]
	$color_library=return_library_array( "select id,color_name from lib_color where status_active=1",'id','color_name');
	$woben_fabric_type_library = return_library_array( "select id,fabric_type from lib_woben_fabric_type",'id','fabric_type');


	/*echo $sql ="SELECT a.company_id,a.location_id,a.store_id, a.currency_id, a.exchange_rate, a.booking_without_order, a.booking_no, b.id,b.cutting_unit_no,b.roll,b.remarks, b.receive_basis, b.pi_wo_batch_no, b.prod_id, b.brand_id,b.batch_lot, c.lot,c.yarn_type, c.detarmination_id,c.color,c.dia_width,c.weight, b.order_uom, b.order_qnty,b.body_part_id, b.order_rate, b.order_ile_cost,b.batch_id, b.order_amount,b.cons_amount,b.no_of_bags,b.product_code,b.floor_id,b.room,b.rack,b.self,b.bin_box,d.id as finish_fabric_id FROM inv_receive_master a, inv_transaction b, product_details_master c, pro_finish_fabric_rcv_dtls d where a.id=b.mst_id and b.prod_id=c.id and a.id=d.mst_id and b.id=d.trans_id and d.id=$rcv_dtls_id[3] and a.entry_form=17"; */

	$sql = "SELECT a.company_id,a.location_id,b.store_id, a.currency_id, a.exchange_rate, a.booking_without_order, a.booking_no, b.id,b.cutting_unit_no,b.roll,b.remarks, b.receive_basis, b.pi_wo_batch_no, b.prod_id, b.brand_id,b.batch_lot, c.lot,c.yarn_type, c.detarmination_id,c.color,c.dia_width,c.weight, b.order_uom, b.order_qnty,b.body_part_id, b.order_rate, b.order_ile_cost,b.batch_id, b.order_amount,b.cons_amount,b.no_of_bags,b.product_code,b.floor_id,b.room,b.rack,b.self,b.bin_box,d.id as finish_fabric_id,d.buyer_id, d.booking_no as pi_booking_no, d.booking_id as pi_booking_id,d.original_width,d.original_gsm,b.fabric_ref,b.rd_no,b.weight_type,b.cutable_width,b.weight_editable,b.width_editable FROM inv_receive_master a, inv_transaction b, product_details_master c, pro_finish_fabric_rcv_dtls d where a.id=b.mst_id and b.prod_id=c.id and a.id=d.mst_id and b.id=d.trans_id and d.id=$rcv_dtls_id[3] and a.entry_form=17";

	//echo $sql; die();
	$result = sql_select($sql);
	foreach($result as $row)
	{
		$comp='';
		$determination_sql=sql_select("select a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=".$row[csf('detarmination_id')]."");

		if($determination_sql[0][csf('construction')]!="")
		{
			$comp=$determination_sql[0][csf('construction')].", ";
		}

		foreach( $determination_sql as $d_row )
		{
			$comp.=$composition[$d_row[csf('copmposition_id')]]." ".$d_row[csf('percent')]."% ";
		}
		if ($row[csf("booking_without_order")]==1)  // without order 1
		{
			echo "$('#txt_roll').attr('disabled',false);\n";
		}
		else{ // with order 0
			echo "$('#txt_roll').attr('disabled','disabled');\n";
		}
		echo "$('#cbo_receive_basis').attr('disabled','disabled');\n";
		echo "$('#update_finish_fabric_id').val('".$row[csf("finish_fabric_id")]."');\n";
		echo "$('#txt_fabric_type').val('".$woben_fabric_type_library[$row[csf("yarn_type")]]."');\n";

		echo "$('#txt_fabric_description').val('".$comp."');\n";
		echo "$('#original_fabric_description').val('".$comp."');\n";

		echo "$('#fabric_desc_id').val(".$row[csf("detarmination_id")].");\n";
		echo "$('#txt_color').val('".$color_library[$row[csf("color")]]."');\n";
		
		echo "$('#txt_width_edit').val('".$row[csf("dia_width")]."');\n";
		echo "$('#txt_weight_edit').val('".$row[csf("weight")]."');\n";

		echo "$('#txt_width').val('".$row[csf("original_width")]."');\n";
		echo "$('#hidden_dia_width').val('".$row[csf("original_width")]."');\n";
        echo "$('#txt_weight').val(".$row[csf("original_gsm")].");\n";
        echo "$('#hidden_gsm_weight').val(".$row[csf("original_gsm")].");\n";
       
        echo "$('#txt_fabric_ref').val('".$row[csf("fabric_ref")]."');\n";
        echo "$('#txt_rd_no').val('".$row[csf("rd_no")]."');\n";
        echo "$('#cbo_weight_type').val(".$row[csf("weight_type")].");\n";
        echo "$('#txt_cutable_width').val('".$row[csf("cutable_width")]."');\n";

        echo "$('#hidden_color_id').val(".$row[csf("color")].");\n";
        echo "$('#txt_batch_lot').val('".$row[csf("batch_lot")]."');\n";
		echo "$('#hidden_batch_id').val('".$row[csf("batch_id")]."');\n";
		echo "$('#txt_receive_qty').val(".number_format($row[csf("order_qnty")],2,".","").");\n";
		echo "$('#txt_rate').val(".$row[csf("order_rate")].");\n";
		echo "$('#txt_ile').val(".$row[csf("order_ile_cost")].");\n";
		echo "$('#cbouom').val(".$row[csf("order_uom")].");\n";
		echo "$('#txt_amount').val(".$row[csf("order_amount")].");\n";
		echo "$('#cbouom').attr('disabled','true')".";\n";
		echo "$('#txt_book_currency').val(".$row[csf("cons_amount")].");\n";
		echo "$('#txt_order_qty').val(0);\n";
		echo "$('#txt_prod_code').val('".$row[csf("prod_id")]."');\n";
		echo "$('#cbo_floor').val(".$row[csf("floor_id")].");\n";
		echo "load_room_rack_self_bin('requires/woven_finish_fabric_roll_receive_controller', 'room','room_td', '".$row[csf('company_id')]."','".$row[csf('location_id')]."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."',this.value);\n";
		echo "$('#cbo_room').val(".$row[csf("room")].");\n";
		echo "load_room_rack_self_bin('requires/woven_finish_fabric_roll_receive_controller', 'rack','rack_td', '".$row[csf('company_id')]."','".$row[csf('location_id')]."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."',this.value);\n";
		echo "$('#txt_rack').val(".$row[csf("rack")].");\n";
		echo "load_room_rack_self_bin('requires/woven_finish_fabric_roll_receive_controller', 'shelf','shelf_td', '".$row[csf('company_id')]."','".$row[csf('location_id')]."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."',this.value);\n";
		echo "$('#txt_shelf').val(".$row[csf("self")].");\n";
		echo "load_room_rack_self_bin('requires/woven_finish_fabric_roll_receive_controller', 'bin','bin_td', '".$row[csf('company_id')]."','".$row[csf('location_id')]."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."','".$row[csf('self')]."',this.value);\n";
		echo "$('#cbo_bin').val(".$row[csf("bin_box")].");\n";
		echo "$('#update_dtls_id').val(".$row[csf("id")].");\n";
		echo "$('#cbo_body_part').val(".$row[csf("body_part_id")].");\n";

		if($row[csf("receive_basis")]==2 || $row[csf("receive_basis")]==1)
		{
			echo "load_drop_down( 'requires/woven_finish_fabric_roll_receive_controller', '".$row[csf("body_part_id")]."', 'load_drop_down_bodypart','body_part_td');\n";
			echo "$('#txt_fabric_description').attr('disabled','disabled');\n";
			echo "$('#cbo_body_part').attr('disabled','disabled');\n";
			echo "$('#txt_width_edit').attr('disabled','disabled');\n";
			echo "$('#txt_weight_edit').attr('disabled','disabled');\n";
			echo "$('#txt_batch_lot').attr('disabled','disabled');\n";
		}
		else
		{
			echo "$('#txt_fabric_description').attr('readonly','readonly');\n";
		}
		//new
		echo "$('#txt_roll').val(".$row[csf("roll")].");\n";
		echo "$('#txt_remarks').val('".$row[csf("remarks")]."');\n";
		echo "$('#cbo_cutting_unit_no').val('".$row[csf("cutting_unit_no")]."');\n";
		echo "$('#hdn_booking_no').val('".$row[csf("pi_booking_no")]."');\n";
		echo "$('#hdn_booking_id').val('".$row[csf("pi_booking_id")]."');\n";

		if($row[csf("receive_basis")]==4)
		{
			echo "$('#all_booking_no').val('".$row[csf("pi_booking_no")]."');\n";
			echo "$('#all_booking_id').val('".$row[csf("pi_booking_id")]."');\n";
		}

		$pi_id="";
		if($rcv_dtls_id[6] ==1){
			$pi_id =$rcv_dtls_id[7];
		}
		echo "$('#hidden_pi_id').val('".$pi_id."');\n";
		echo "$('#hdn_buyer_id').val('".$row[csf("buyer_id")]."');\n";

		if($row[csf("receive_basis")] ==2 && $row[csf("buyer_id")] == ''){

		}

		$balance_qnty='';
		if($row[csf("receive_basis")]==1 || $row[csf("receive_basis")] ==2)
		{
			if($row[csf("receive_basis")]==1) // PI Basis
			{
				if($row[csf("original_gsm")]!="" || $db_type==0)
				{
					$weight_cond="fab_weight='".$row[csf("original_gsm")]."'";

				}
				else $weight_cond="fab_weight is null";

				if($row[csf("original_width")]!="" || $db_type==0)
				{
					$dia_width_cond="dia_width='".$row[csf("original_width")]."'";
				}
				else $dia_width_cond="dia_width is null";

				//echo $sql = "select sum(quantity) as qnty from com_pi_item_details where pi_id='".$row[csf("pi_wo_batch_no")]."' and determination_id='".$row[csf("detarmination_id")]."' and color_id='".$row[csf("color")]."' and $dia_width_cond and $weight_cond and status_active=1 and is_deleted=0";

				$pi_wo_qnty=return_field_value("sum(quantity) as qnty","com_pi_item_details","pi_id='".$row[csf("pi_wo_batch_no")]."' and determination_id='".$row[csf("detarmination_id")]."' and color_id='".$row[csf("color")]."' and $dia_width_cond and $weight_cond and status_active=1 and is_deleted=0","qnty");
			}
			else // WO/Booking Basis
			{
				// echo ' Test'.$row[csf("booking_without_order")].' Test';die;
				if($row[csf("booking_without_order")]==1)
				{
					if($row[csf("original_gsm")]!="" || $db_type==0)
					{
						$weight_cond="gsm_weight='".$row[csf("original_gsm")]."'";
					}
					else $weight_cond="gsm_weight is null";

					if($row[csf("original_width")]!="" || $db_type==0)
					{
						$dia_width_cond="dia_width='".$row[csf("original_width")]."'";
					}
					else $dia_width_cond="dia_width is null";

					$booking_no=return_field_value("booking_no","wo_non_ord_samp_booking_mst","id='".$row[csf("pi_wo_batch_no")]."'");
					$pi_wo_qnty=return_field_value("finish_fabric as qnty","wo_non_ord_samp_booking_dtls","booking_no='".$booking_no."' and lib_yarn_count_deter_id='".$row[csf("detarmination_id")]."' and fabric_color='".$row[csf("color")]."' and body_part='".$row[csf("body_part_id")]."' and $dia_width_cond and $weight_cond and status_active=1 and is_deleted=0","qnty");
				}
				else
				{
					if($row[csf("original_gsm")]!="" || $db_type==0)
					{
						$weight_cond="b.gsm_weight='".$row[csf("original_gsm")]."'";
					}
					else $weight_cond="b.gsm_weight is null";

					if($row[csf("original_width")]!="" || $db_type==0)
					{
						$dia_width_cond="a.dia_width='".$row[csf("original_width")]."'";
					}
					else $dia_width_cond="a.dia_width is null";

					$booking_no=return_field_value("booking_no","wo_booking_mst","id='".$row[csf("pi_wo_batch_no")]."'");
					$pi_wo_qnty=return_field_value("sum(a.fin_fab_qnty) as qnty","wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b","a.job_no=b.job_no and a.booking_no='".$booking_no."' and b.lib_yarn_count_deter_id='".$row[csf("detarmination_id")]."' and a.fabric_color_id='".$row[csf("color")]."' and b.body_part_id='".$row[csf("body_part_id")]."' and $dia_width_cond and $weight_cond and a.status_active=1 and a.is_deleted=0","qnty");
				}
			}

			if($row[csf("original_gsm")]!="" || $db_type==0)
			{
				$weight_cond2="d.original_gsm='".$row[csf("original_gsm")]."'";
			}
			else $weight_cond2="d.original_gsm is null";

			if($row[csf("original_width")]!="" || $db_type==0)
			{
				$dia_width_cond2="d.original_width='".$row[csf("original_width")]."'";
			}
			else $dia_width_cond2="d.original_width is null";

			/*echo $sqls = "select sum(a.order_qnty) as qnty from inv_receive_master c, pro_finish_fabric_rcv_dtls d, inv_transaction a , product_details_master b"," c.id=d.mst_id and d.trans_id=a.id and d.mst_id=a.mst_id  and a.prod_id=b.id and c.entry_form=17 and a.receive_basis='".$row[csf("receive_basis")]."' and a.pi_wo_batch_no='".$row[csf("pi_wo_batch_no")]."' and b.detarmination_id='".$row[csf("detarmination_id")]."' and a.body_part_id='".$row[csf("body_part_id")]."' and b.color='".$row[csf("color")]."' and $dia_width_cond2 and $weight_cond2 and a.order_rate='".$row[csf("order_rate")]."' and a.status_active=1 and a.is_deleted=0 and a.item_category=3 and a.transaction_type=1" ;*/



			$recv_qnty=return_field_value("sum(a.order_qnty) as qnty","inv_receive_master c, pro_finish_fabric_rcv_dtls d, inv_transaction a , product_details_master b"," c.id=d.mst_id and d.trans_id=a.id and d.mst_id=a.mst_id  and a.prod_id=b.id and c.entry_form=17 and a.receive_basis='".$row[csf("receive_basis")]."' and a.pi_wo_batch_no='".$row[csf("pi_wo_batch_no")]."' and b.detarmination_id='".$row[csf("detarmination_id")]."' and a.body_part_id='".$row[csf("body_part_id")]."' and b.color='".$row[csf("color")]."' and $dia_width_cond2 and $weight_cond2 and a.order_rate='".$row[csf("order_rate")]."' and a.status_active=1 and a.is_deleted=0 and a.item_category=3 and a.transaction_type=1","qnty");

			 //echo $pi_wo_qnty."-".$recv_qnty;

			$balance_qnty = $pi_wo_qnty-$recv_qnty;
		}

		$roll_maintained=return_field_value("fabric_roll_level","variable_settings_production","company_name ='".$row[csf("company_id")]."' and item_category_id=3 and variable_list=3 and is_deleted=0 and status_active=1");
		$save_string=''; $order_id=''; //$roll_maintained=0;
		if($roll_maintained==1)
		{
			$data_roll_array=sql_select("select id, po_breakdown_id, qnty, roll_no, barcode_no from pro_roll_details where dtls_id='$rcv_dtls_id[3]' and entry_form=17 and status_active=1 and is_deleted=0");

			foreach($data_roll_array as $row_roll)
			{
				if($row_roll[csf('roll_used')]==1) $roll_id=$row_roll[csf('id')]; else $roll_id=0;
				$roll_id=$row_roll[csf('id')];

				if($save_string=="")
				{
					$save_string=$row_roll[csf("po_breakdown_id")]."**".$row_roll[csf("qnty")]."**".$row_roll[csf("roll_no")]."**".$row_roll[csf("id")]."**".$row_roll[csf("barcode_no")];
				}
				else
				{
					$save_string.=",".$row_roll[csf("po_breakdown_id")]."**".$row_roll[csf("qnty")]."**".$row_roll[csf("roll_no")]."**".$row_roll[csf("id")]."**".$row_roll[csf("barcode_no")];
				}
				$order_id.=$row_roll[csf("po_breakdown_id")].",";
			}
			$order_id=implode(",",array_unique(explode(",",substr($order_id,0,-1))));
		}
		else
		{
			$data_po_array=sql_select("select po_breakdown_id, quantity,no_of_roll from order_wise_pro_details where trans_id='$rcv_dtls_id[4]' and entry_form=17 and status_active=1 and is_deleted=0");
			foreach($data_po_array as $row_po)
			{
				if($save_string=="")
				{
					$save_string=$row_po[csf("po_breakdown_id")]."**".$row_po[csf("quantity")]."**"."0"."**"."0"."**"."0"."**".$row_po[csf("no_of_roll")];
				}
				else
				{
					$save_string.=",".$row_po[csf("po_breakdown_id")]."**".$row_po[csf("quantity")]."**"."0"."**"."0"."**"."0"."**".$row_po[csf("no_of_roll")];
				}

				$order_id.=$row_po[csf("po_breakdown_id")].",";
			}
			$order_id=substr($order_id,0,-1);
		}
		if($balance_qnty>0){
			echo "$('#txt_bla_order_qty').val(".$balance_qnty.");\n"; 
		}else{
			echo "$('#txt_bla_order_qty').val('');\n";
		}

		echo "$('#all_po_id').val('".$order_id."');\n";
		echo "document.getElementById('save_data').value 				= '".$save_string."';\n";
		echo "set_button_status(1, permission, 'fnc_woben_finish_fab_receive_entry',1,1);\n";
		echo "disable_enable_fields( 'cbo_receive_purpose*txt_challan_no*cbo_store_name', 0, '', '');\n";
		echo "fn_calile();\n";
	}
	exit();
}




//################################################# function Here #########################################//




//function for domestic rate find--------------//
//parameters rate,ile cost,exchange rate,conversion factor
function return_domestic_rate($rate,$ile_cost,$exchange_rate,$conversion_factor){
	$rate_ile=$rate+$ile_cost;
	$rate_ile_exchange=$rate_ile*$exchange_rate;
	$doemstic_rate=$rate_ile_exchange/$conversion_factor;
	return $doemstic_rate;
}


//return product master table id ----------------------------------------//
function return_product_id($txt_fabric_type,$txt_fabric_description,$fabric_desc_id,$txt_color,$txt_width,$txt_weight,$company,$supplier,$store,$uom,$prodCode,$fabric_source)
{
	$buyer_supp_cond = ($fabric_source==3)?" and is_buyer_supplied=1" : "";
	$fabric_desc_id_cond = ( str_replace("'","", $fabric_desc_id)!="")?" and detarmination_id=$fabric_desc_id" : "";
	$weight_cond = ( str_replace("'","", $txt_weight)!="")?" and weight=$txt_weight " : "";
	$width_cond = ( str_replace("'","", $txt_width)!="")?" and dia_width=$txt_width" : "";
	$color_cond = ( str_replace("'","", $txt_color)!="")?" and color='$txt_color'" : "";

	$whereCondition = "company_id=$company and supplier_id=$supplier and item_category_id=3 $fabric_desc_id_cond $weight_cond $width_cond $color_cond  and unit_of_measure = $uom and status_active=1 and is_deleted=0 $buyer_supp_cond";

	//echo "10**select id from product_details_master where $whereCondition";die;

  	$prodMSTID = return_field_value("id","product_details_master","$whereCondition");
	$insertResult = true;

	if($prodMSTID==false || $prodMSTID=="")
	{
		// new product create here--------------------------//
		//$fabric_type_arr=return_library_array( "select id, fabric_type from lib_woben_fabric_type",'id','fabric_type');
		$color_name_arr=return_library_array( "select id, color_name from lib_color where status_active=1",'id','color_name');
		$buyer_supplied = ($fabric_source==3)?" [BS]" : "";

		$product_name_details=trim(str_replace("'","",$txt_fabric_description)).", ".trim(str_replace("'","",$txt_weight)).", ".trim(str_replace("'","",$txt_width)).", ".$color_name_arr[$txt_color]. ", ".$buyer_supplied; 
				//$product_name_details=$fabric_type_arr[$txt_fabric_type].", ".trim(str_replace("'","",$txt_fabric_description));


 		$is_buyer_supplied = ($fabric_source==3)?1 : 0;
		$prodMSTID = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
		$field_array = "id,company_id,supplier_id,item_category_id,detarmination_id,product_name_details,item_code,unit_of_measure,yarn_type,color,dia_width,weight,is_buyer_supplied";
 		$data_array = "(".$prodMSTID.",".$company.",".$supplier.",3,".$fabric_desc_id.",'".$product_name_details."',".$prodCode.",".$uom.",".$txt_fabric_type.",".$txt_color.",".$txt_width.",".$txt_weight.",".$is_buyer_supplied.")";
		//echo $field_array."<br>".$data_array."--".$product_name_details;die;
		$insertResult = false;
		//$insertResult = sql_insert("product_details_master",$field_array,$data_array,1);

	}
	if($insertResult == true)
	{
		return $insertResult."***".$prodMSTID;
	}else{
		return $insertResult."***".$field_array."***".$data_array."***".$prodMSTID;
	}

}

if ($action=="po_popup_booking_wise")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$data=explode("_",$data);
	$po_id=$data[0]; $type=$data[1];

	if($type==1)
	{
		$dtls_id=$data[2];
		$roll_maintained=$data[3];
		$save_data=$data[4];
		$prev_distribution_method=$data[5];
		$receive_basis=$data[6];
		$txt_deleted_id=$data[7];
		$hidden_pi_id=$data[8];
		$update_hdn_transaction_id=$data[9];
	}

	if($roll_maintained==1)
	{
		$disable_drop_down=1;
		$prev_distribution_method=2;
		$disabled="disabled='disabled'";

		$width="900";
		$roll_arr=return_library_array("select po_breakdown_id, max(roll_no) as roll_no from pro_roll_details where entry_form=17 group by po_breakdown_id",'po_breakdown_id','roll_no');
	}
	else
	{
		//$prev_distribution_method=1;
		$disabled="";
		$disable_drop_down=0;
		$width="900";
	}
	
	//Field lavel Access.................start
	foreach($field_level_data_arr[$cbo_company_id] as $val=>$row){
		$is_disable= $row[is_disable];
		$defalt_value= $row[defalt_value];
	}
	if($is_disable){$disable_drop_down=$is_disable;}
	if($defalt_value){$prev_distribution_method=$defalt_value;}
	//Field lavel Access.................end
	
	

	if($receive_basis==4 || $receive_basis==6){
		$enable_des_cond = 0;
		$massageShow = "";
	}else{
		$enable_des_cond = 1;
		$massageShow = "<h2 style='color:#FF0000'>The basis of selected PI is Independent.</h2>";
	}
	$variable_set_invent = sql_select("select category,over_rcv_status,over_rcv_percent,over_rcv_payment from variable_inv_ile_standard where company_name=$cbo_company_id and variable_list=23 and category = 3 and status_active =1 and is_deleted=0 order by id");
	$over_receive_limit = !empty($variable_set_invent) ? $variable_set_invent[0][csf('over_rcv_percent')] : 0;

	?>

	<script>
		var receive_basis=<? echo $receive_basis; ?>;
		var roll_maintained=<? echo $roll_maintained; ?>;
		var balance_qnty = <? if($txt_bla_order_qty==""){  echo $txt_bla_order_qty=0;}else{ echo  $txt_bla_order_qty=$txt_bla_order_qty;}?>;
		var exists_receive_qnty = <? if($txt_receive_qnty=="") {echo $txt_receive_qnty=0;}else {echo $txt_receive_qnty=$txt_receive_qnty;} ?>;
		var dtls_id = <? if($dtls_id!="") {echo $dtls_id=$dtls_id;}else {echo $dtls_id=0;} ?>;


		function fn_show_check()
		{

			if (document.getElementById('txt_search_common').value=="") 
			{
				if( form_validation('cbo_buyer_name','Buyer Name')==false )
				{
					return;
				}
			}
			
			show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('cbo_buyer_name').value+'_'+'<? echo $all_po_id; ?>', 'create_po_search_list_view', 'search_div', 'woven_finish_fabric_roll_receive_controller', 'setFilterGrid(\'tbl_list_search\',-1);hidden_field_reset();');
			set_all();
		}

		function distribute_qnty(str)
		{
			if(str==1)
			{
				//var tot_po_qnty=$('#tot_po_qnty').val()*1;
				var tot_req_qnty=$('#tot_req_qnty').val()*1;
				var txt_prop_grey_qnty=$('#txt_prop_grey_qnty').val()*1;
				var tblRow = $("#tbl_list_search tr").length;
				var len=totalFinish=0;var totalAvailableQnty=0;
				$("#tbl_list_search").find('tr').each(function()
				{
					totalAvailableQnty+=$(this).find('input[name="availabeQntyWithOverRcv[]"]').val()*1;
				});
				if(totalAvailableQnty<txt_prop_grey_qnty)
				{
					alert("Receive quantity is more than required quantity. Availabe Quantity = "+totalAvailableQnty);
					return;
				}
					$("#tbl_list_search").find('tr').each(function()
					{
						var req_qnty=$(this).find('input[name="txtReqQnty[]"]').val()*1;
						if(req_qnty>0)
						{

							len=len+1;
							var po_qnty=$(this).find('input[name="txtPoQnty[]"]').val()*1;
							var txtbalanceqnty=$(this).find('input[name="txtBalanceQnty[]"]').val()*1;
							var hidden_cummulative_rcv_qnty=$(this).find('input[name="hidden_cummulative_rcv_qnty[]"]').val()*1;
							var availabeQntyWithOverRcv=$(this).find('input[name="availabeQntyWithOverRcv[]"]').val()*1;
							
							var perc=(req_qnty/tot_req_qnty)*100;
							
							var finish_qnty=((perc*txt_prop_grey_qnty)/100);
							//alert(finish_qnty+"= 1");
							if(availabeQntyWithOverRcv<finish_qnty)
							{
								finish_qnty=availabeQntyWithOverRcv;
							}
							//alert(finish_qnty+"= 2");

							//alert(req_qnty+"="+tot_req_qnty+"=" + txt_prop_grey_qnty+"="+ finish_qnty);
							//ug 37 rcv id

							totalFinish = (totalFinish*1+finish_qnty*1).toFixed(2);
							//totalFinish = totalFinish;
							var balance_qty= req_qnty-(hidden_cummulative_rcv_qnty + finish_qnty);
							//alert(req_qnty+"-"+hidden_cummulative_rcv_qnty +"+"+ finish_qnty +"="+balance_qty);


							if(tblRow==len)
							{
								var balance = (txt_prop_grey_qnty-totalFinish);
								if(balance > 0){
									finish_qnty = (finish_qnty*1 + balance*1);
								}else{
									
									finish_qnty = (finish_qnty*1 - balance*1);

								}

								if(balance!=0) totalFinish=totalFinish*1+(balance*1);
							}
							balance_qty= req_qnty-(hidden_cummulative_rcv_qnty + finish_qnty);

							if(balance_qty<0)
							{
								balance_qty=0;
							}

							$(this).find('input[name="txtGreyQnty[]"]').val(finish_qnty.toFixed(2));
							$(this).find('input[name="txtBalanceQnty[]"]').val(balance_qty.toFixed(2));
						}
					});
				
			}
			else
			{
				$('#txt_prop_grey_qnty').val('');
				$("#tbl_list_search").find('tr').each(function()
				{
					if($(this).find('input[name="txtGreyQnty[]"]').is(":disabled")==false)
					{
						$(this).find('input[name="txtGreyQnty[]"]').val('');
					}
				});
			}
		}

		function roll_duplication_check(row_id)
		{
			var row_num=$('#tbl_list_search tr').length;
			var po_id=$('#txtPoId_'+row_id).val();
			var roll_no=$('#txtRoll_'+row_id).val();

			if(roll_no*1>0)
			{
				for(var j=1; j<=row_num; j++)
				{
					if(j==row_id)
					{
						continue;
					}
					else
					{
						var po_id_check=$('#txtPoId_'+j).val();
						var roll_no_check=$('#txtRoll_'+j).val();

						if(po_id==po_id_check && roll_no==roll_no_check)
						{
							alert("Duplicate Roll No.");
							$('#txtRoll_'+row_id).val('');
							return;
						}
					}
				}

				var txtRollId=$('#txtRollId_'+row_id).val();
				var data=po_id+"**"+roll_no+"**"+txtRollId;
				var response=return_global_ajax_value( data, 'roll_duplication_check', '', 'woven_finish_fabric_roll_receive_controller');
				var response=response.split("_");

				if(response[0]!=0)
				{
					var po_number=$('#tr_'+row_id).find('td:first').text();
					alert("This Roll Already Used. Duplicate Not Allowed");
					$('#txtRoll_'+row_id).val('');
					return;
				}
			}

		}

		function add_break_down_tr( i )
		{
			var cbo_distribiution_method=$('#cbo_distribiution_method').val();
			var isDisbled=$('#txtGreyQnty_'+i).is(":disabled");

			if(cbo_distribiution_method==2 && isDisbled==false)
			{
				var row_num=$('#tbl_list_search tr').length;
				row_num++;

				var clone= $("#tr_"+i).clone();
				clone.attr({
					id: "tr_" + row_num,
				});

				clone.find("input,select").each(function(){

				$(this).attr({
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ row_num },
				  'name': function(_, name) { return name },
				  'value': function(_, value) { return value }
				});

				}).end();

				$("#tr_"+i).after(clone);

				$('#txtOrginal_'+row_num).removeAttr("value").attr("value","0");
				$('#txtRoll_'+row_num).removeAttr("value").attr("value","");
				$('#txtGreyQnty_'+row_num).removeAttr("value").attr("value","");
				$('#txtRoll_'+row_num).removeAttr("onBlur").attr("onBlur","roll_duplication_check("+row_num+");");
				$('#txtBarcodeNo_'+row_num).removeAttr("value").attr("value","");
				$('#txtRollTableId_'+row_num).removeAttr("value").attr("value","");
				

				$('#increase_'+row_num).removeAttr("value").attr("value","+");
				$('#decrease_'+row_num).removeAttr("value").attr("value","-");
				$('#increase_'+row_num).removeAttr("onclick").attr("onclick","add_break_down_tr("+row_num+");");
				$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fn_deleteRow("+row_num+");");
			}
		}

		function fn_deleteRow(rowNo)
		{
			var txtOrginal=$('#txtOrginal_'+rowNo).val()*1;
			var txtRollTableId=$('#txtRollTableId_'+rowNo).val();
			var txt_deleted_id=$('#hide_deleted_id').val();
			var selected_id='';
			if(txtOrginal==0)
			{
				if(txtRollTableId!='')
				{
					if(txt_deleted_id=='') selected_id=txtRollTableId; else selected_id=txt_deleted_id+','+txtRollTableId;
					$('#hide_deleted_id').val( selected_id );
				}
				$("#tr_"+rowNo).remove();
			}
		}

		var selected_id = new Array();

		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				var jobNo=$('#txt_individual_job'+i).val();
				js_set_value( i,1,jobNo );
			}
		}

		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function set_all()
		{
			var old=document.getElementById('txt_po_row_id').value;
			if(old!="")
			{
				old=old.split(",");
				for(var i=0; i<old.length; i++)
				{
					js_set_value( old[i],0 )
				}
			}
		}

		function js_set_value( str, check_or_not,jobNo )
		{
			var any_selected = $('#po_id').val();
			if(any_selected=="")
			{
				job_no_arr_chk = [];
			}
			if(check_or_not==1)
			{
				var roll_used=$('#roll_used'+str).val();
				if(roll_used==1)
				{
					var po_number=$('#search' + str).find("td:eq(3)").text();
					alert("Batch Roll Found Against PO- "+po_number);
					return;
				}
			}


			if(job_no_arr_chk.length==0)
			{
				job_no_arr_chk.push( jobNo );
			}
			else if( jQuery.inArray( jobNo, job_no_arr_chk )==-1 &&  job_no_arr_chk.length>0)
			{
				alert("Job Mixed is Not Allowed");
				return;
			}


			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );

			$('#po_id').val( id );
		}

		function show_grey_prod_recv()
		{
			var po_id=$('#po_id').val();
			show_list_view ( po_id+'_'+'1'+'_'+'<? echo $dtls_id; ?>'+'_'+'<? echo $roll_maintained; ?>'+'_'+'<? echo $save_data; ?>'+'_'+'<? echo $prev_distribution_method; ?>'+'_'+'<? echo $receive_basis; ?>'+'_'+'<? echo $txt_deleted_id; ?>'+'_'+'<? echo $hidden_pi_id; ?>'+'_'+'<? echo $update_hdn_transaction_id; ?>'+'_'+'<? echo $cbo_company_id; ?>', 'po_popup_booking_wise', 'search_div', 'woven_finish_fabric_roll_receive_controller', '');   
		}

		function hidden_field_reset()
		{
			$('#po_id').val('');
			$('#save_string').val( '' );
			$('#tot_grey_qnty').val( '' );
			$('#number_of_roll').val( '' );
			selected_id = new Array();
		}

		function fnc_close()
		{
			var save_string = ''; var tot_grey_qnty=''; var no_of_roll='';
			$("#tbl_list_search").find('tr').each(function ()
			{
				var txtPoId = $(this).find('input[name="txtPoId[]"]').val();
				var hdnJobNo = $(this).find('input[name="hdnJobNo[]"]').val();
				var hdnBookingNo = $(this).find('input[name="hdnBookingNo[]"]').val();
				var txtGreyQnty = $(this).find('input[name="txtGreyQnty[]"]').val();

				var txtRoll = $(this).find('input[name="txtRoll[]"]').val();
				var txtRollId = $(this).find('input[name="txtRollId[]"]').val();
				var txtRollTableId = $(this).find('input[name="txtRollTableId[]"]').val();
				var txtBarcodeNo = $(this).find('input[name="txtBarcodeNo[]"]').val();
				var txtRFId = $(this).find('input[name="txtRFId[]"]').val();
				
				if(txtGreyQnty*1>0)
				{
					tot_grey_qnty=tot_grey_qnty*1+txtGreyQnty*1;
					if (save_string == "") 
					{
						save_string = txtPoId + "**" + hdnJobNo + "**" + hdnBookingNo + "**" + txtGreyQnty + "**" + txtRoll + "**" + txtRollId + "**" + txtRollTableId + "**" + txtBarcodeNo + "**" + txtRFId;
					}
					else 
					{
						save_string += "," + txtPoId + "**" + hdnJobNo + "**" + hdnBookingNo + "**" + txtGreyQnty + "**" + txtRoll + "**" + txtRollId + "**" + txtRollTableId + "**" + txtBarcodeNo + "**" + txtRFId;
					}

					no_of_roll=no_of_roll*1+1;
					no_of_roll=no_of_roll*1;
				}
			});

			$('#save_string').val( save_string );
			$('#tot_grey_qnty').val( tot_grey_qnty );
			//$('#tot_rollNo').val( tot_rollNo );
			$('#number_of_roll').val( no_of_roll );
			//$('#all_po_id').val( po_id_array );
			$('#distribution_method').val( $('#cbo_distribiution_method').val() );

			//$('#all_booking_id').val( booking_id_array ); //only for independent basis
			//$('#all_booking_no').val( booking_no_array ); //only for independent basis
			//return;
			parent.emailwindow.hide();
		}

		function fnc_close_bk()
		{
			var save_string='';	 var tot_grey_qnty=''; var no_of_roll='';var tot_rollNo='';
			var po_id_array = new Array();var booking_id_array = new Array();var booking_no_array = new Array();
			var overRecvCheckCount=0;var sl=1;var tot_row=0;tot_Rollrow=0;var availabeQntyWithOverRcv_showing=0;
			$("#tbl_list_search").find('tr').each(function()
			{
				var txtPoId=$(this).find('input[name="txtPoId[]"]').val();
				var txtBookingNo=$(this).find('input[name="txtBookingNo[]"]').val();
				var txtBookingId=$(this).find('input[name="txtBookingId[]"]').val();
				var txtGreyQntyx = $('#txtGreyQnty_'+sl).val();
				var txtRollNox = $('#txtRollNo_'+sl).val();
				
				if(txtGreyQntyx>0)
				{
					var txtHdnPoRatio= $('#txtHdnPoRatio_'+sl).val();
					tot_row++;

					if(txtRollNox==0 || txtRollNox=="")
					{
						tot_Rollrow++;
					}
				}
				var txtRoll=$(this).find('input[name="txtRoll[]"]').val();
				var txtRollId=$(this).find('input[name="txtRollId[]"]').val();
				var txtBarcodeNo=$(this).find('input[name="txtBarcodeNo[]"]').val();
				
				var txtRecQntyPrev=$(this).find('input[name="txtRecQntyPrev[]"]').val()*1;
				var thisChallanRcvQnty=$(this).find('input[name="thisChallanRcvQnty[]"]').val()*1;
				var txtGreyQnty=$(this).find('input[name="txtGreyQnty[]"]').val()*1;
				var txtRollNo=$(this).find('input[name="txtRollNo[]"]').val()*1;
				var availabeQntyWithOverRcv=$(this).find('input[name="availabeQntyWithOverRcv[]"]').val()*1;
				var recvQntyFieldId=$(this).find('input[name="txtGreyQnty[]"]').attr("id");
				var hidden_job_no=$(this).find('input[name="hidden_job_no[]"]').val();

				tot_grey_qnty=tot_grey_qnty*1+txtGreyQnty*1;
				tot_rollNo=tot_rollNo*1+txtRollNo*1;
				//alert(roll_maintained);return;
				if(roll_maintained!=1)
				{
					txtRoll=0;
					txtBarcodeNo=0;
				}

				if(txtRoll*1>0)
				{
					//no_of_roll=no_of_roll*1+1;
					no_of_roll=no_of_roll*1;
					no_of_roll+=txtRoll*1; //issue id:33591
				}

				if(txtGreyQnty*1>0)
				{
					/*if(save_string=="")
					{*/
						if(txtGreyQntyx>0)
						{
							var txtHdnPoRatio= $('#txtHdnPoRatio_'+sl).val();
							var txtHdnPoRat=txtHdnPoRatio.split(",");
								var totOrdReqQnty=0;var po_req_ref=new Array();
								for(var j=0; j<txtHdnPoRat.length; j++)
								{
									var txtHdnPo=txtHdnPoRat[j].split("=");
									var txtPoIdx=txtHdnPo[0];
									totOrdReqQnty+=txtHdnPo[1]*1;
									po_req_ref[txtPoIdx]=txtHdnPo[1]*1;
									//alert(txtGreyQnt);
								}
								//----------------For adjust praction after calculation po wise qnty distribution---------------------
								var totQntyAftrPraction=0;var txtPoIdxArr=new Array();
								for(var y=0; y<txtHdnPoRat.length; y++)
								{
									var txtHdnPo=txtHdnPoRat[y].split("=");
									var txtPoIdx=txtHdnPo[0];
									var txtGreyQntx=(po_req_ref[txtPoIdx]/totOrdReqQnty)*txtGreyQntyx;
									txtGreyQntx=decimal_format(txtGreyQntx, 1)*1;
									txtPoIdxArr[txtPoIdx]= txtGreyQntx*1;
									txtGreyQntx=txtGreyQntx*1;
									totQntyAftrPraction+=txtGreyQntx;
								}
								//txtGreyQntyx=txtGreyQntyx*1;
															
								var lastPoSll=0;
								
								for(var y=0; y<txtHdnPoRat.length; y++)
								{
									var txtHdnPo=txtHdnPoRat[y].split("=");
									var txtPoIdx=txtHdnPo[0];

									lastPoSll=txtHdnPoRat.length-y;
									//alert(txtPoIdxArr[txtPoIdx]);
									if(lastPoSll==1)
									{
										if(txtGreyQntyx>totQntyAftrPraction)
										{
											var dueQntyafterMinuz = (txtGreyQntyx-totQntyAftrPraction)*1;
											dueQntyafterMinuz = decimal_format(dueQntyafterMinuz, 1)*1;
											
											txtPoIdxArr[txtPoIdx]+= dueQntyafterMinuz*1;
											txtPoIdxArr[txtPoIdx]=decimal_format(txtPoIdxArr[txtPoIdx], 1)*1;
											
										}
										else
										{
											var dueQntyafterMinuz = (totQntyAftrPraction-txtGreyQntyx);
											dueQntyafterMinuz = decimal_format(dueQntyafterMinuz, 1)*1;

											txtPoIdxArr[txtPoIdx]-=dueQntyafterMinuz*1;
											txtPoIdxArr[txtPoIdx]=decimal_format(txtPoIdxArr[txtPoIdx], 1)*1;
										}
										//alert(txtPoIdxArr[txtPoIdx]);
										//alert(txtPoIdxArr[txtPoIdx]+'+'+dueQntyafterMinuz);
									}
									
								}


								//alert(txtGreyQntx+parseFloat(dueQntyafterMinuz).toFixed(14)*1);							
								lastPoSl=0;
								//-----------------End For adjust praction after calculation po wise qnty distribution-------------
								for(var k=0; k<txtHdnPoRat.length; k++)
								{
									var txtHdnPo=txtHdnPoRat[k].split("=");
									var txtPoIdx=txtHdnPo[0];
									//var txtGreyQnt=(po_req_ref[txtPoIdx]/totOrdReqQnty)*txtGreyQntyx;
									var txtGreyQnt=txtPoIdxArr[txtPoIdx];

									lastPoSl=txtHdnPoRat.length-k; //this line for adjust po wise qnty distribution

									if(save_string=="")
									{

										save_string=txtPoIdx+"**"+txtGreyQnt+"**"+txtRoll+"**"+txtRollId+"**"+txtBarcodeNo+"**"+txtRollNo;
									}
									else
									{
										/*if(lastPoSl==1) //this IF line for adjust po wise qnty distribution
										{
											txtGreyQnt=txtPoIdxArr[txtPoIdx];
										}*/

										save_string+=","+txtPoIdx+"**"+txtGreyQnt+"**"+txtRoll+"**"+txtRollId+"**"+txtBarcodeNo+"**"+txtRollNo;
									}
								}	

						}

								/*var txtHdnPoRat=txtHdnPoRatio.split(",");
								for(var j=1; j<=tot_row; j++)
								{

									var txtHdnPoRat=txtHdnPoRat.split("=");
									var txtPoIds=txtHdnPoRat[0];
									var txtPoIdRat=txtHdnPoRat[1];
									save_string=txtPoId+"**"+txtGreyQnty+"**"+txtRoll+"**"+txtRollId+"**"+txtBarcodeNo+"**"+txtRollNo;
								}*/
								//save_string=txtPoId+"**"+txtGreyQnty+"**"+txtRoll+"**"+txtRollId+"**"+txtBarcodeNo+"**"+txtRollNo;
						
					/*}
					else
					{
						save_string+=","+txtPoId+"**"+txtGreyQnt+"**"+txtRoll+"**"+txtRollId+"**"+txtBarcodeNo+"**"+txtRollNo;
					}*/
					//alert(save_string);
					
					if( jQuery.inArray( txtPoId, po_id_array) == -1 )
					{
						po_id_array.push(txtPoId);
					}

					//only for independent basis
					if( jQuery.inArray( txtBookingId, booking_id_array) == -1 )
					{
						booking_id_array.push(txtBookingId);
					}
					if( jQuery.inArray( txtBookingNo, booking_no_array) == -1 )
					{
						booking_no_array.push(txtBookingNo);
					}
				}
				//alert(availabeQntyWithOverRcv+'<'+txtRecQntyPrev +'-'+ thisChallanRcvQnty +'+'+ txtGreyQnty);
				//var wo_pi_qty = (txtRecQntyPrev - thisChallanRcvQnty + txtGreyQnty);
				//if(availabeQntyWithOverRcv<wo_pi_qty)
				availabeQntyWithOverRcv=availabeQntyWithOverRcv.toFixed(2);
				if(availabeQntyWithOverRcv<txtGreyQnty)
				{
					//alert("Required quantity is more than receive quantity");
					overRecvCheckCount+=1;
					$('#'+recvQntyFieldId).val(availabeQntyWithOverRcv);
					availabeQntyWithOverRcv_showing=availabeQntyWithOverRcv;
					//return;
				}
				sl++;
			});
			//alert(overRecvCheckCount);
			if(overRecvCheckCount>0)
			{
				alert("Receive quantity is more than required quantity."+ " Original Required Qty = "+ availabeQntyWithOverRcv_showing);
				return;
			}
			if(tot_Rollrow>0)
			{
				alert("Please write roll No");
				return;
			}

			$('#save_string').val( save_string );
			$('#tot_grey_qnty').val( tot_grey_qnty );
			$('#tot_rollNo').val( tot_rollNo );
			$('#number_of_roll').val( no_of_roll );
			$('#all_po_id').val( po_id_array );
			$('#distribution_method').val( $('#cbo_distribiution_method').val() );

			$('#all_booking_id').val( booking_id_array ); //only for independent basis
			$('#all_booking_no').val( booking_no_array ); //only for independent basis
			//return;
			parent.emailwindow.hide();
		}
    </script>

	</head>

	<body>
		<div align="center">
		<?
		if($type!=1)
		{
			?>
			<form name="searchdescfrm"  id="searchdescfrm">
				<!--<fieldset style="width:<? //echo $width; ?>px;margin-left:10px">-->

		        	<input type="hidden" name="save_string" id="save_string" class="text_boxes" value="">
		            <input type="hidden" name="tot_grey_qnty" id="tot_grey_qnty" class="text_boxes" value="">
		            <input type="hidden" name="tot_rollNo" id="tot_rollNo" class="text_boxes" value="">
		            <input type="hidden" name="number_of_roll" id="number_of_roll" class="text_boxes" value="">
		            <input type="hidden" name="all_po_id" id="all_po_id" class="text_boxes" value="">
		            <input type="hidden" name="all_booking_id" id="all_booking_id" class="text_boxes" value="">
		            <input type="hidden" name="all_booking_no" id="all_booking_no" class="text_boxes" value="">
		            <input type="hidden" name="distribution_method" id="distribution_method" class="text_boxes" value="">
		            <input type="hidden" name="hide_deleted_id" id="hide_deleted_id" class="text_boxes" value="<? echo $txt_deleted_id; ?>">
		            <input type="hidden" name="update_hdn_transaction_id" id="update_hdn_transaction_id" class="text_boxes" value="<? echo $update_hdn_transaction_id; ?>">

			<?
		}
		//echo $receive_basis."=".$type;die;
		if(($receive_basis==4 || $receive_basis==6 || $receive_basis==1) && $type!=1)
		{
			?>
			<?
			$po_data=sql_select("SELECT d.po_number,d.job_no_mst, d.po_quantity as po_qnty_in_pcs, d.pub_shipment_date from com_pi_item_details a, wo_booking_dtls b, wo_po_break_down d where a.pi_id='$hidden_pi_id' and a.work_order_no=b.booking_no and b.po_break_down_id=d.id group by d.po_number,d.job_no_mst, d.po_quantity, d.pub_shipment_date");
			//print_r ($po_data[0]);
			//if($save_data !="" || $po_data[0]=="")
			if($po_data[0]=="")
			{
				//echo 'pi without pi id';
				?>
		  		<table cellpadding="0" cellspacing="0" width="<? echo $width-20; ?>" class="rpt_table">
		  			<thead>
		            	<tr><th colspan="4"><? echo $massageShow;?></th></tr>
		                <tr>
		                    <th>Buyer</th>
		                    <th>Search By</th>
		                    <th>Search</th>
		                    <th>
		                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
		                        <input type="hidden" name="po_id" id="po_id" value="">
		                    </th>
		                </tr>
		  			</thead>
		  			<tr class="general">
		  				<td align="center">
		  					<?
		  						echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $hdn_buyer_id, "",$enable_des_cond);
		  					?>
		  				</td>
		  				<td align="center">
		  					<?
		  						$search_by_arr=array(1=>"PO No",2=>"Job No",3=>"Style",4=>"Internal Ref.");
		  						echo create_drop_down( "cbo_search_by", 170, $search_by_arr,"",0, "--Select--", 1,$dd,0,'2,3,4' );
		  					?>
		  				</td>
		  				<td align="center">
		  					<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
		  				</td>
		  				<td align="center">
		  					<input type="button" name="button2" class="formbutton" value="Show" onClick="fn_show_check();" style="width:100px;" />
		  				</td>
		  			</tr>
		  		</table>
		        <?
			}
			//7-1-2017 start
			else if($hidden_pi_id!="" && $save_data =="")
			{
				if ($hidden_pi_id!="") 
				{
					$check_exixting_pi_sql=sql_select("select booking_id from inv_receive_master where booking_id='".$hidden_pi_id."'  and receive_basis=$receive_basis and entry_form=17 and is_deleted=0 and status_active=1 and item_category=3");
					$check_exixting_pi=$check_exixting_pi_sql[0][csf('booking_id')];
				}
				if($check_exixting_pi>0)
				{
					$prev_recv_qnty=sql_select("select a.id, c.po_breakdown_id,c.quantity as qnty,f.job_no_mst,e.booking_no ,g.style_ref_no from inv_receive_master d,pro_finish_fabric_rcv_dtls e ,inv_transaction a,order_wise_pro_details c, product_details_master b,wo_po_break_down f,wo_po_details_master g,wo_booking_dtls h where d.id=e.mst_id and e.trans_id=a.id and c.trans_id=a.id and c.prod_id=b.id and c.po_breakdown_id=f.id and f.job_id=g.id and f.id=h.po_break_down_id and a.receive_basis=$receive_basis  and d.receive_basis=$receive_basis and b.color='".$hidden_color_id."' and d.booking_id='".$hidden_pi_id."' and a.body_part_id='".$cbo_body_part_id."' and b.detarmination_id='".$fabric_desc_id."' and e.original_gsm='".$hidden_gsm_weight."' and e.original_width='".$hidden_dia_width."' and e.uom='".$cbouom."' and a.status_active=1 and c.entry_form=17 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.status_active=1 and d.is_deleted=0 and a.item_category=3 and d.item_category=3 and a.transaction_type=1 group by a.id, c.po_breakdown_id,c.quantity,f.job_no_mst,e.booking_no ,g.style_ref_no");
				}
				else
				{
					$prev_recv_qnty=sql_select("select a.id, c.po_breakdown_id,c.quantity as qnty from inv_transaction a, product_details_master b,order_wise_pro_details c where a.prod_id=b.id and b.id=c.prod_id and a.id=c.trans_id and a.receive_basis=$receive_basis and b.color='".$hidden_color_id."' and a.body_part_id='".$cbo_body_part_id."' and b.detarmination_id='".$fabric_desc_id."' and b.weight='".$hidden_gsm_weight."' and b.dia_width='".$hidden_dia_width."' and a.cons_uom='".$cbouom."'  and a.status_active=1 and c.entry_form=17 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.item_category=3 and a.transaction_type=1");
				}

				foreach ($prev_recv_qnty as $row) 
				{
					$prev_recv_qnty_arr[$row[csf('job_no_mst')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]]['qnty']+=$row[csf('qnty')];
					//$prev_recv_qnty_arr[$row[csf('po_breakdown_id')]]['qnty']+=$row[csf('qnty')];
					if($update_hdn_transaction_id != "" && $update_hdn_transaction_id == $row[csf('id')])
					{
						$thisChallanRcvQntyArray[$row[csf('job_no_mst')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]] +=$row[csf('qnty')];
					}
				}

				if($hdn_booking_no!=""){$bookingNo_cond2="and h.booking_no='$hdn_booking_no'";}
				$prev_recv_return_qnty=sql_select("select a.id, c.po_breakdown_id,c.quantity as qnty,f.job_no_mst,h.booking_no ,g.style_ref_no from inv_transaction a, product_details_master b,order_wise_pro_details c ,wo_po_break_down f,wo_po_details_master  g,wo_booking_dtls h where a.prod_id=b.id and b.id=c.prod_id and a.id=c.trans_id and c.po_breakdown_id=f.id and f.job_no_mst=g.job_no and g.job_no=h.job_no and f.id=h.po_break_down_id and a.status_active=1 and c.entry_form=202 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.item_category=3 and a.transaction_type=3 and b.color='".$hidden_color_id."' and a.cons_uom='".$cbouom."' and a.body_part_id='".$cbo_body_part_id."' and b.detarmination_id='".$fabric_desc_id."' $bookingNo_cond2 group by a.id, c.po_breakdown_id,c.quantity,f.job_no_mst,h.booking_no ,g.style_ref_no "); 

				foreach ($prev_recv_return_qnty as $row) 
				{
					$prev_recv_return_qnty_arr[$row[csf('job_no_mst')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]]['qnty']+=$row[csf('qnty')];
				}


				if ($txt_fabric_ref) {$txt_fabric_ref_cond="and f.fabric_ref='$txt_fabric_ref'";}
				if ($txt_rd_no) {$txt_rd_no_cond="and f.rd_no='$txt_rd_no'";}
				if ($cbo_weight_type) {$cbo_weight_type_cond="and c.gsm_weight_type='$cbo_weight_type'";}
				if ($txt_cutable_width) {$txt_cutable_width_cond="and f.cutable_width='$txt_cutable_width'";}
				if ($hidden_gsm_weight=="" && $db_type==2) {$hidden_gsm_weight_cond="and a.fab_weight is NULL";} else{$hidden_gsm_weight_cond="and a.fab_weight='$hidden_gsm_weight'";}
				if ($hidden_gsm_weight=="" && $db_type==2) {$hidden_gsm_weight_cond2="and b.gsm_weight is NULL";} else{$hidden_gsm_weight_cond2="and b.gsm_weight='$hidden_gsm_weight'";}

				$nameArray2=sql_select("SELECT a.id as pi_id,d.job_no_mst, e.style_ref_no,b.booking_no, d.id as po_id,a.quantity as pi_qnty   
				from com_pi_item_details a, wo_booking_dtls b, wo_po_break_down d, wo_pre_cost_fabric_cost_dtls c, wo_po_details_master e,lib_yarn_count_determina_mst f
				where a.pi_id='$hidden_pi_id' and a.work_order_no=b.booking_no and b.po_break_down_id=d.id and b.po_break_down_id=d.id and b.pre_cost_fabric_cost_dtls_id=c.id and e.job_no=d.job_no_mst and c.lib_yarn_count_deter_id=f.id and c.lib_yarn_count_deter_id=$fabric_desc_id and a.determination_id=$fabric_desc_id  and a.color_id in($hidden_color_id) and b.fabric_color_id in($hidden_color_id) and c.body_part_id=$cbo_body_part_id and a.body_part_id=$cbo_body_part_id and c.uom=$cbouom and a.dia_width=b.dia_width and a.color_id=b.fabric_color_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $hidden_gsm_weight_cond and a.dia_width='".$hidden_dia_width."' $hidden_gsm_weight_cond2 and b.dia_width='".$hidden_dia_width."' and a.work_order_id=$hdn_booking_id order by a.id");

				$poIDS="";
				foreach($nameArray2 as $row)
				{
					$dataArr[$row[csf('job_no_mst')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]]["po_id"].=$row[csf('po_id')].",";
					if ($chk_pi_id[$row[csf('pi_id')]]=="") 
					{
						$chk_pi_id[$row[csf('pi_id')]]=$row[csf('pi_id')];
						$dataArr[$row[csf('job_no_mst')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]]["pi_qnty"]+=$row[csf('pi_qnty')];
						$dataArr2[$row[csf('booking_no')]]+=$row[csf('pi_qnty')];
					}
					$poIDS.=$row[csf('po_id')].",";
				}

				$poIDS=implode(",",array_unique(explode(",", $poIDS))); 
				$poIDS=chop($poIDS,",");


				$finish_req_qnty_sql=sql_select("SELECT d.id as po_id,d.job_no_mst, d.po_number, sum(b.fin_fab_qnty) as  fin_fab_qnty,b.booking_no,e.style_ref_no from wo_booking_dtls b, wo_po_break_down d ,wo_pre_cost_fabric_cost_dtls c, wo_po_details_master e where b.po_break_down_id=d.id and b.pre_cost_fabric_cost_dtls_id=c.id  and c.job_no=e.job_no and d.job_no_mst=e.job_no and d.id in($poIDS) and b.fabric_color_id in($hidden_color_id) and c.body_part_id=$cbo_body_part_id and  c.lib_yarn_count_deter_id=$fabric_desc_id and b.gsm_weight='".$hidden_gsm_weight."' and b.dia_width='".$hidden_dia_width."' and c.uom='".$cbouom."' and b.booking_no='$hdn_booking_no' and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by d.id,d.job_no_mst, d.po_number ,b.booking_no,e.style_ref_no"); 
				$finish_req_qnty_total=0;
				foreach ($finish_req_qnty_sql as $row) {

					$finish_req_qnty_arr[$row[csf('id')]]["finish_reqt_qnty"]=$row[csf('fin_fab_qnty')];
					$finish_req_qnty_total+=$row[csf('fin_fab_qnty')];
					$finish_req_qnty_arr2[$row[csf('job_no_mst')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]][$row[csf('po_id')]]["finish_reqt_qnty"]+=$row[csf('fin_fab_qnty')];
					$finish_req_qnty_arr3[$row[csf('job_no_mst')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]]["finish_reqt_qnty"]+=$row[csf('fin_fab_qnty')];
				}


				if($nameArray2[0] !="")
				{
					?>
					<div style="width:<? echo $width; ?>px; margin-top:10px" align="center">
						<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="430" align="center">
	                    <? //echo $hidden_pi_id; ?>
							<thead>
								<th>Total Receive Qnty</th>
								<th>Distribution Method</th>
							</thead>
							<tr class="general">
								<td><input type="text" name="txt_prop_grey_qnty" id="txt_prop_grey_qnty" class="text_boxes_numeric" value="<? echo $txt_receive_qnty; ?>" style="width:120px" onBlur="distribute_qnty(document.getElementById('cbo_distribiution_method').value)" <? echo $disabled; ?>></td>
								<td>
									<?
										$distribiution_method=array(0=>"--Select--",1=>"Proportionately",2=>"Manually");
										echo create_drop_down( "cbo_distribiution_method", 160, $distribiution_method,"",0, "",$prev_distribution_method, "distribute_qnty(this.value);",$disable_drop_down );
									?>
								</td>
							</tr>
						</table>
					</div>
					<div style="margin-left:10px; margin-top:10px">
						<!----Start New Table-->
						<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="<? echo $width-20; ?>">
							<thead>
								<th width="130">Job No</th>
								<th width="130">Style Ref.</th>
								<th width="100">Booking No</th>
								<th width="100">Req. Qnty</th>
								<th width="100">GRN Qnty</th>
								<th width="40">Roll</th>
								<th width="100">Barcode No.</th>
								<th width="100">RF Id</th>
								<th width="80"></th>
							</thead>
						
							<tbody id="tbl_list_search" >
								<?
								$i=1; $tot_po_qnty=0; $tot_req_qnty=0; $po_array=array();

							foreach($dataArr as $jobNo => $job_data)
							{
								foreach($job_data as $bookingNo => $booking_data)
								{
									foreach($booking_data as $styleRef => $row)
									{
										
										if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

										$orginal_val=1;
										$roll_id=0;

										$prevRecReturnQnty=$prev_recv_return_qnty_arr[$jobNo][$bookingNo][$styleRef]['qnty'];
										$prevRecQnty=$prev_recv_qnty_arr[$jobNo][$bookingNo][$styleRef]['qnty']-$prevRecReturnQnty;

										$requiredQnty_from_pi=$dataArr[$jobNo][$bookingNo][$styleRef]["pi_qnty"];


										$requiredQnty_pi=$dataArr2[$bookingNo];
										$requiredQnty_finish2=$finish_req_qnty_arr3[$jobNo][$bookingNo][$styleRef]["finish_reqt_qnty"];

										$availabeQntyWithOverRcv=(($requiredQnty_finish2*$over_receive_limit)/100)+$requiredQnty_finish2;
										$availabeQntyWithOverRcv=$availabeQntyWithOverRcv-$prevRecQnty;

										$po_arr_uniq=array_unique(explode(",", chop($row['po_id'],",") ));
										$po_ids= implode(",", $po_arr_uniq);
										$hdn_po_ratio_ref="";
										foreach ($po_arr_uniq as $poID)
										{

											$orderRequiredQnty=$finish_req_qnty_arr2[$jobNo][$bookingNo][$styleRef][$poID]["finish_reqt_qnty"];

												
											
											$po_ratio=$orderRequiredQnty*1/$finish_req_qnty_total*1;
											//echo $po_ratio."=".$requiredQnty_pi." <br>";
											$final_po_required_qnty=$po_ratio*$requiredQnty_pi*1;
											$final_po_required_arr[$poID]['required']=$po_ratio*$requiredQnty_pi;

											//$hdn_po_ratio_ref.=$poID."=".$po_ratio.",";
											$hdn_po_ratio_ref.=$poID."=".$orderRequiredQnty.",";
										}
										$hdn_po_ratio_ref=chop($hdn_po_ratio_ref,",");

										$job_req=$finish_req_qnty_arr3[$jobNo][$bookingNo][$styleRef]["finish_reqt_qnty"];
										//echo "==".$job_req."==";
										$job_ratio=$job_req*1/$finish_req_qnty_total*1;
										$final_job_required_qnty=$job_ratio*$requiredQnty_pi*1;


										$dataArrJob[$jobNo][$bookingNo][$styleRef]["qnty"]=$final_job_required_qnty;
										$balance_req_qnty=$dataArrJob[$jobNo][$bookingNo][$styleRef]["qnty"]-$prevRecQnty;

										$booking_po_ids=implode(",",array_unique(explode(",", $row['po_id']))); 
										$booking_po_ids= chop($booking_po_ids,",");
										//echo $booking_po_ids."<br>";
										?>
										<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
											<td ><p><? echo $jobNo; ?></p></td>
											<td >
												<p><? echo $styleRef; ?></p>
											</td>
											<td title="<? echo $po_ids; ?>">
												<p><? echo $bookingNo; ?></p>

												<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? //echo $po_ids; ?>">
												<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="<? echo $orginal_val; ?>">
												<input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="<? echo $roll_id; ?>">
												<input type="hidden" name="txtRollTableId[]" id="txtRollTableId_<? echo $i; ?>" value="<? echo $roll_id; ?>">
												<input type="hidden" name="txtHdnPoRatio[]" id="txtHdnPoRatio_<? echo $i; ?>" value="<? echo $hdn_po_ratio_ref; ?>">
												<input type="hidden" name="hidden_cummulative_rcv_qnty[]" id="hidden_cummulative_rcv_qnty_<? echo $i; ?>" value="<? echo $prevRecQnty; ?>">
												<input type="hidden" name="hdnJobNo[]" id="hdnJobNo_<? echo $i; ?>" value="<? echo $jobNo; ?>">
												<input type="hidden" name="hdnBookingNo[]" id="hdnBookingNo_<? echo $i; ?>" value="<? echo $bookingNo; ?>">
											</td>
											
											<td  align="right" title="Over Receive =<? echo $over_receive_limit;?>% , Total Required qnty with over receive = <? echo $availabeQntyWithOverRcv; ?> , Previous Receive qnty = <? echo $prevRecQnty; ?> Total balance is = <? echo $availabeQntyWithOverRcv-$prevRecQnty;?>  " >
												<?
												echo number_format($final_job_required_qnty,2,".",""); ?>
												<input type="hidden" name="txtReqQnty[]" id="txtReqQnty_<? echo $i; ?>" value="<? echo $final_job_required_qnty; ?>"/>

											</td>
											<td  align="center">
												<input type="text" name="txtGreyQnty[]" id="txtGreyQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="" >

												<input type="hidden" name="thisChallanRcvQnty[]" id="thisChallanRcvQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $thisChallanRcvQntyArray[$jobNo][$bookingNo][$styleRef]; ?>" <? echo $disable; ?>>

												<input type="hidden" name="txtRecQntyPrev[]" id="txtRecQntyPrev_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $prevRecQnty; ?>" <? echo $disable; ?>>

												<input type="hidden" name="availabeQntyWithOverRcv[]" id="availabeQntyWithOverRcv_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $availabeQntyWithOverRcv; ?>" <? echo $disable; ?>>

											</td>

											<td  align="center">
													<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px" value="" placeholder="<? echo $roll_arr[$order_id]+1; ?>" onBlur="roll_duplication_check(<? echo $i; ?>);" />
												</td>

											<td  align="center"><input type="text" name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="" disabled/></td>

											<td align="center">
												<input type="text" name="txtRFId[]" id="txtRFId_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="" readonly>
											</td>

											<td >
												<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
												<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
											</td>
										</tr>
										<?
										$i++;
										$tot_req_qnty+=$final_job_required_qnty;
										
									}
								}
							}

							

							
								?>
							</tbody>
								<input type="hidden" name="tot_req_qnty" id="tot_req_qnty" class="text_boxes" value="<? echo $tot_req_qnty; ?>">
	                            <input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="<? echo $i-1; ?>">
							</table>
							<!----End New Table-->
						</div>

						<table width="<? echo $width; ?>">
							 <tr>
								<td align="center" >
									<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
								</td>
							</tr>
						</table>
					</div>
					<?
				}
			}
			// 7-1-2017 end
			?>

	   		<?
			if($save_data!="")
			{
				?>
				<div style="width:<? echo $width; ?>px; margin-top:10px" align="center">
					<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="300" align="center">
						<thead>
							<th>Total Receive Qnty</th>
							<th>Distribution Method </th>
						</thead>
						<tr class="general">
							<td><input type="text" name="txt_prop_grey_qnty" id="txt_prop_grey_qnty" class="text_boxes_numeric" value="<? echo $txt_receive_qnty; ?>" style="width:120px" onBlur="distribute_qnty(document.getElementById('cbo_distribiution_method').value)" <? echo $disabled; ?>></td>
							<td>
								<?
									$distribiution_method=array(0=>"--Select--",1=>"Proportionately",2=>"Manually");
									echo create_drop_down( "cbo_distribiution_method", 160, $distribiution_method,"",0, "",$prev_distribution_method, "distribute_qnty(this.value);",$disable_drop_down );
								?>
							</td>
						</tr>
					</table>
				</div>
				<div style="margin-left:10px; margin-top:10px">
					<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="<? echo $width-20; ?>">
						<thead>
							<th width="130">Job No</th>
							<th width="130">Style Ref.</th>
							<th width="100">Booking No</th>
							<th width="100">Req. Qnty</th>
							<th width="100">GRN Qnty</th>
							<th width="40">Roll</th>
							<th width="100">Barcode No.</th>
							<th width="100">RF Id</th>
							<th width="80"></th>
						</thead>
					
					
						<tbody id="tbl_list_search">
							<?

							$i=1; $tot_po_qnty=0; $tot_req_qnty=0; $po_array=array();

							if($hdn_booking_no!=""){$bookingNo_cond2="and h.booking_no='$hdn_booking_no'";}
							$prev_recv_return_qnty=sql_select("select a.id, c.po_breakdown_id,c.quantity as qnty,f.job_no_mst,h.booking_no ,g.style_ref_no from inv_transaction a, product_details_master b,order_wise_pro_details c ,wo_po_break_down f,wo_po_details_master  g,wo_booking_dtls h where a.prod_id=b.id and b.id=c.prod_id and a.id=c.trans_id and c.po_breakdown_id=f.id and f.job_no_mst=g.job_no and g.job_no=h.job_no and f.id=h.po_break_down_id and a.status_active=1 and c.entry_form=202 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.item_category=3 and a.transaction_type=3 and b.color='".$hidden_color_id."' and a.cons_uom='".$cbouom."' and a.body_part_id='".$cbo_body_part_id."' and b.detarmination_id='".$fabric_desc_id."' $bookingNo_cond2 group by a.id, c.po_breakdown_id,c.quantity,f.job_no_mst,h.booking_no ,g.style_ref_no "); 

							foreach ($prev_recv_return_qnty as $row) 
							{
								$prev_recv_return_qnty_arr[$row[csf('job_no_mst')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]]['qnty']+=$row[csf('qnty')];
							}

							$prev_recv_qnty=sql_select("select a.id, c.po_breakdown_id,c.quantity as qnty,f.job_no_mst,e.booking_no ,g.style_ref_no,c.no_of_roll from inv_transaction a, product_details_master b,order_wise_pro_details c,inv_receive_master d,pro_finish_fabric_rcv_dtls e,wo_po_break_down f,wo_po_details_master  g,wo_booking_dtls h where a.prod_id=b.id and b.id=c.prod_id and a.id=c.trans_id and a.mst_id=d.id and d.id=e.mst_id and c.po_breakdown_id=f.id and f.job_id=g.id and g.job_no=h.job_no and f.id=h.po_break_down_id and a.receive_basis=$receive_basis  and d.receive_basis=$receive_basis and b.color='".$hidden_color_id."' and d.booking_id='".$hidden_pi_id."' and a.body_part_id='".$cbo_body_part_id."' and b.detarmination_id='".$fabric_desc_id."' and e.original_gsm='".$hidden_gsm_weight."' and e.original_width='".$hidden_dia_width."' and e.uom='".$cbouom."' and a.status_active=1 and c.entry_form=17 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.status_active=1 and d.is_deleted=0 and a.item_category=3 and d.item_category=3 and a.transaction_type=1 group by a.id, c.po_breakdown_id,c.quantity,f.job_no_mst,e.booking_no ,g.style_ref_no,c.no_of_roll");

							foreach ($prev_recv_qnty as $row) 
							{
								if($update_hdn_transaction_id!=$row[csf('id')])
								{
									$prev_recv_qnty_arr[$row[csf('job_no_mst')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]]['qnty']+=$row[csf('qnty')];
									if($update_hdn_transaction_id != "" && $update_hdn_transaction_id == $row[csf('id')])
									{
										$thisChallanRcvQntyArray[$row[csf('job_no_mst')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]] +=$row[csf('qnty')];
									}
								}
								$prev_roll_no_arr[$row[csf('job_no_mst')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]]['no_of_roll']=$row[csf('no_of_roll')];
								
							}

							$explSaveData = explode(",",$save_data);$order_ids="";$poWiseQntyArr=array();$poWiseRollNoArr=array();
							for($kk=0;$kk<count($explSaveData);$kk++)
							{
								$po_wise_datas = explode("**",$explSaveData[$kk]);
								$order_ids.=$po_wise_datas[0].",";
								$poWiseQntyArr[$po_wise_datas[0]]=$po_wise_datas[1];
								$poWiseRollNoArr[$po_wise_datas[0]]=$po_wise_datas[5];

							}
							
							$order_ids=chop($order_ids,",");
							if ($txt_fabric_ref) {$txt_fabric_ref_cond="and f.fabric_ref='$txt_fabric_ref'";}
							if ($txt_rd_no) {$txt_rd_no_cond="and f.rd_no='$txt_rd_no'";}
							if ($cbo_weight_type) {$cbo_weight_type_cond="and c.gsm_weight_type='$cbo_weight_type'";}
							if ($txt_cutable_width) {$txt_cutable_width_cond="and f.cutable_width='$txt_cutable_width'";}
							if ($hidden_gsm_weight=="" && $db_type==2) {$hidden_gsm_weight_cond="and a.fab_weight is NULL";} else{$hidden_gsm_weight_cond="and a.fab_weight='$hidden_gsm_weight'";}
							if ($hidden_gsm_weight=="" && $db_type==2) {$hidden_gsm_weight_cond2="and b.gsm_weight is NULL";} else{$hidden_gsm_weight_cond2="and b.gsm_weight='$hidden_gsm_weight'";}

							$nameArray2=sql_select("SELECT a.id as pi_id,d.job_no_mst, e.style_ref_no,b.booking_no, d.id as po_id,a.quantity as pi_qnty, e.id as job_id   
								from com_pi_item_details a, wo_booking_dtls b, wo_po_break_down d, wo_pre_cost_fabric_cost_dtls c, wo_po_details_master e,lib_yarn_count_determina_mst f
								where a.pi_id='$hidden_pi_id' and a.work_order_no=b.booking_no and b.po_break_down_id=d.id and b.po_break_down_id=d.id and b.pre_cost_fabric_cost_dtls_id=c.id and e.id=d.job_id and c.lib_yarn_count_deter_id=f.id and c.lib_yarn_count_deter_id=$fabric_desc_id and a.color_id in($hidden_color_id) and b.fabric_color_id in($hidden_color_id) and c.body_part_id=$cbo_body_part_id and c.uom='$cbouom' and a.dia_width=b.dia_width and a.color_id=b.fabric_color_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 $hidden_gsm_weight_cond and a.dia_width='".$hidden_dia_width."' $hidden_gsm_weight_cond2 and b.dia_width='".$hidden_dia_width."' and a.work_order_id=$hdn_booking_id  order by a.id");
							//and d.id in($order_ids)

							$dataArrQnty=array();$poIDS="";
							foreach($nameArray2 as $row)
							{
								$dataArr[$row[csf('job_no_mst')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]]["po_id"].=$row[csf('po_id')].",";
								if ($chk_pi_id[$row[csf('pi_id')]]=="") 
								{
									$chk_pi_id[$row[csf('pi_id')]]=$row[csf('pi_id')];
									$dataArr[$row[csf('job_no_mst')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]]["pi_qnty"]+=$row[csf('pi_qnty')];
									$dataArr2[$row[csf('booking_no')]]+=$row[csf('pi_qnty')];
								}
								$poIDS.=$row[csf('po_id')].",";
							}
							$poIDS=implode(",",array_unique(explode(",", $poIDS))); 
							$poIDS=chop($poIDS,",");

							
							$finish_req_qnty_sql=sql_select("SELECT d.id as po_id,d.job_no_mst, d.po_number, sum(b.fin_fab_qnty) as  fin_fab_qnty,b.booking_no,e.style_ref_no from wo_booking_dtls b, wo_po_break_down d ,wo_pre_cost_fabric_cost_dtls c, wo_po_details_master e where b.po_break_down_id=d.id and b.pre_cost_fabric_cost_dtls_id=c.id  and c.job_no=e.job_no and d.job_id=e.id and d.id in($poIDS) and b.fabric_color_id in($hidden_color_id) and c.body_part_id=$cbo_body_part_id and  c.lib_yarn_count_deter_id=$fabric_desc_id and b.gsm_weight='".$hidden_gsm_weight."' and b.dia_width='".$hidden_dia_width."' and c.uom=$cbouom and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by d.id,d.job_no_mst, d.po_number ,b.booking_no,e.style_ref_no"); 


							$finish_req_qnty_total=0;
							foreach ($finish_req_qnty_sql as $row) {

								$finish_req_qnty_arr[$row[csf('id')]]["finish_reqt_qnty"]=$row[csf('fin_fab_qnty')];
								$finish_req_qnty_total+=$row[csf('fin_fab_qnty')];
								$finish_req_qnty_arr2[$row[csf('job_no_mst')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]][$row[csf('po_id')]]["finish_reqt_qnty"]+=$row[csf('fin_fab_qnty')];
								$finish_req_qnty_arr3[$row[csf('job_no_mst')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]]["finish_reqt_qnty"]+=$row[csf('fin_fab_qnty')];

								$po_data[$row[csf('po_id')]]['job_no_mst']=$row[csf('job_no_mst')];
								$po_data[$row[csf('po_id')]]['style_ref_no']=$row[csf('style_ref_no')];

								$jobRefdata[$row[csf('job_no_mst')]]['job_no_mst']=$row[csf('job_no_mst')];
								$jobRefdata[$row[csf('job_no_mst')]]['style_ref_no']=$row[csf('style_ref_no')];

								$po_data_array[$row[csf('job_no_mst')]][$row[csf('booking_no')]]['requ_qty']+=$row[csf('fin_fab_qnty')];
							}
							
							$sqlroll = sql_select("select barcode_no,roll_used from pro_roll_details where status_active=1 and is_deleted=0 and booking_no='$booking_no'");
							foreach ($sqlroll as $row) {
								$rollData[$row[csf("barcode_no")]]['roll_used'] = $row[csf("roll_used")];
							}

							$explSaveData = explode(",",$save_data);
							foreach($explSaveData as $val)
							{
								$order_data = explode("**",$val);
								$order_id=$order_data[0];
								$hdnJobNo=$order_data[1];
								$hdnBookingNo=$order_data[2];
								$fin_qty=$order_data[3];
								$roll_no=$order_data[4];
								$roll_id=$order_data[5];
								$roll_table_id=$order_data[6];
								$barcode_no=$order_data[7];
								$rf_id=$order_data[8];

								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								$roll_used = $rollData[$barcode_no]['roll_used'];

								if(!(in_array($order_id,$po_array)))
								{
									$tot_po_qnty+=$po_data_array[$order_id]['qty'];
									//$orginal_val=1;
									$po_array[]=$order_id;
								}
								if ($roll_used == 1) $orginal_val = 1; else $orginal_val = 0;

								if($hdnJobNo == ""){
									$job_number = $po_data[$order_id]['job_no_mst'];
									$style_ref_number = $po_data[$order_id]['style_ref_no'];
								}
								else
								{
									$job_number = $hdnJobNo;
									$style_ref_number = $jobRefdata[$hdnJobNo]['style_ref_no'];
								}
								//N.B before save order number is not avaibale and after save job no did not saved
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
									<td ><p><? echo $job_number; ?></p></td>
									<td>
										<p><? echo $style_ref_number; ?></p>
									</td>
									<td >
										<p><? echo $hdnBookingNo; ?></p>
										<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $order_id; ?>">
										<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="<? echo $orginal_val; ?>">
										<input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="<? echo $roll_id; ?>">
										<input type="hidden" name="txtRollTableId[]" id="txtRollTableId_<? echo $i; ?>" value="<? echo $roll_table_id; ?>">
										<input type="hidden" name="hidden_cummulative_rcv_qnty[]" id="hidden_cummulative_rcv_qnty_<? echo $i; ?>" value="<? echo $prevRecQnty; ?>">
										<input type="hidden" name="hdnJobNo[]" id="hdnJobNo_<? echo $i; ?>" value="<? echo $job_number; ?>">
										<input type="hidden" name="hdnBookingNo[]" id="hdnBookingNo_<? echo $i; ?>" value="<? echo $hdnBookingNo; ?>">
									</td>
									<td  align="right">
										<? echo $po_data_array[$job_number][$hdnBookingNo]['requ_qty']; ?>
									</td>

									<td align="center">
										<input type="text" name="txtGreyQnty[]" id="txtGreyQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $fin_qty; ?>"/>
									</td>
									<td  align="center">
										<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px" value="<? if($roll_no!=0) echo $roll_no; ?>" placeholder="<? echo $roll_arr[$order_id]+1; ?>" onBlur="roll_duplication_check(<? echo $i; ?>);" />
									</td>
									<td  align="center"><input type="text" name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $barcode_no; ?>" disabled/>
									</td>
									<td align="center">
										<input type="text" name="txtRFId[]" id="txtRFId_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $rf_id; ?>" >
									</td>
									<td >
										<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
										<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
									</td>
								
								</tr>
								<?
							}




							
							/* foreach($dataArrQnty as $jobNo => $job_data)
							{
								foreach($job_data as $bookingNo => $booking_data)
								{
									foreach($booking_data as $styleRef => $row)
									{
										$hdn_po_ratio_ref=chop($row['po_req'],",");
										if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										
										$no_of_roll=$row['rollNo'];
										
										$prevRecReturnQnty=$prev_recv_return_qnty_arr[$jobNo][$bookingNo][$styleRef]['qnty'];
										$prevRecQnty=$prev_recv_qnty_arr[$jobNo][$bookingNo][$styleRef]['qnty']-$prevRecReturnQnty;
										$requiredQnty_from_pi= $dataArr[$jobNo][$bookingNo][$styleRef]["pi_qnty"];	

										$requiredQnty_pi=$dataArr2[$bookingNo];
										$requiredQnty_finish2=$finish_req_qnty_arr3[$jobNo][$bookingNo][$styleRef]["finish_reqt_qnty"];
										//$balance_req_qnty=$requiredQnty_finish2-$prevRecQnty;

										$availabeQntyWithOverRcv=(($requiredQnty_finish2*$over_receive_limit)/100)+$requiredQnty_finish2;
										$availabeQntyWithOverRcv=$availabeQntyWithOverRcv-$prevRecQnty;

										$job_req=$finish_req_qnty_arr3[$jobNo][$bookingNo][$styleRef]["finish_reqt_qnty"];
										$job_ratio=$job_req*1/$finish_req_qnty_total*1;
										$final_job_required_qnty=$job_ratio*$requiredQnty_pi*1;


										if($update_hdn_transaction_id=="")
										{
											$prevRecReturnQnty=$prev_recv_return_qnty_arr[$jobNo][$bookingNo][$styleRef]['qnty'];
											$prevRecQnty=$prev_recv_qnty_arr[$jobNo][$bookingNo][$styleRef]['qnty']-$prevRecReturnQnty;
											//$balance_req_qnty=$requiredQnty_finish2-($prevRecQnty+$row['job_qnty']);

											$dataArrJob[$jobNo][$bookingNo][$styleRef]["qnty"]=$final_job_required_qnty;
											$requiredPiQnty=$dataArrJob[$jobNo][$bookingNo][$styleRef]["qnty"];
											//$balance_req_qnty=$requiredPiQnty-($prevRecQnty+$row['job_qnty']);
											$balance_req_qnty=$requiredPiQnty-($prevRecQnty);
										}
										else
										{
											$prevRecReturnQnty=$prev_recv_return_qnty_arr[$jobNo][$bookingNo][$styleRef]['qnty'];
											$prevRecQnty=$prev_recv_qnty_arr[$jobNo][$bookingNo][$styleRef]['qnty']-$prevRecReturnQnty;

											$dataArrJob[$jobNo][$bookingNo][$styleRef]["qnty"]=$final_job_required_qnty;
											$requiredPiQnty=$dataArrJob[$jobNo][$bookingNo][$styleRef]["qnty"];
											$balance_req_qnty=$requiredPiQnty-($prevRecQnty);
										}


										$po_arr_uniq=array_unique(explode(",", chop($row['po_id'],",") ));
										$po_ids=implode(",", $po_arr_uniq);

										?>
											<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
												<td><p><? echo $jobNo; ?></p></td>
												<td><p><? echo $styleRef; ?></p>
													<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="<? echo $orginal_val; ?>">
													<input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="<? echo $roll_id; ?>">
													<input type="hidden" name="txtHdnPoRatio[]" id="txtHdnPoRatio_<? echo $i; ?>" value="<? echo $hdn_po_ratio_ref; ?>">
													<input type="hidden" name="hidden_cummulative_rcv_qnty[]" id="hidden_cummulative_rcv_qnty_<? echo $i; ?>" value="<? echo $prevRecQnty; ?>">
												</td>
												<td title="<? echo $po_ids; ?>" >
													<p><? echo $bookingNo; ?></p>

													<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $order_id; ?>">
													<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="<? echo $orginal_val; ?>">
													<input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="<? echo $roll_id; ?>">
													<input type="hidden" name="txtRollTableId[]" id="txtRollTableId_<? echo $i; ?>" value="<? echo $roll_table_id; ?>">
													<input type="hidden" name="hidden_cummulative_rcv_qnty[]" id="hidden_cummulative_rcv_qnty_<? echo $i; ?>" value="<? echo $prevRecQnty; ?>">
													<input type="hidden" name="hdnJobNo[]" id="hdnJobNo_<? echo $i; ?>" value="<? echo $po_data[$order_id]['job_no_mst']; ?>">
													<input type="hidden" name="hdnBookingNo[]" id="hdnBookingNo_<? echo $i; ?>" value="<? echo $hdnBookingNo; ?>">


												</td>
												<td  align="right" title="Over Receive =<? echo $over_receive_limit;?>% , Total Required qnty with over receive = <? echo $availabeQntyWithOverRcv; ?> , Previous Receive qnty = <? echo $prevRecQnty; ?> Total balance is = <? echo $availabeQntyWithOverRcv-$prevRecQnty;?>  " >
												<? 

												echo number_format($final_job_required_qnty,2,".",""); ?>
												<input type="hidden" name="txtReqQnty[]" id="txtReqQnty_<? echo $i; ?>" value="<? echo $final_job_required_qnty; ?>"/>

												</td>



												<td  align="right">
													<? echo number_format($prevRecQnty,2); ?>
												</td>
												<?
												if($roll_maintained!=1)
												{
												?>
													<td>
														<input type="text" name="txtRollNo[]" id="txtRollNo_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px" value="<? echo $no_of_roll; ?>">
													</td>
												<?
												}
												?>
												<td  align="center">
													<input type="text" name="txtGreyQnty[]" id="txtGreyQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo number_format($row['job_qnty'],2,".",""); ?>">

													<input type="hidden" name="thisChallanRcvQnty[]" id="thisChallanRcvQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $thisChallanRcvQntyArray[$jobNo][$bookingNo][$styleRef]; ?>" <? echo $disable; ?>>
															
													<input type="hidden" name="txtRecQntyPrev[]" id="txtRecQntyPrev_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $prevRecQnty; ?>" <? echo $disable; ?>>

													<input type="hidden" name="availabeQntyWithOverRcv[]" id="availabeQntyWithOverRcv_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $availabeQntyWithOverRcv; ?>" <? echo $disable; ?>>

												</td>
												<td  align="center" title="<? echo $balance_req_qnty.'='.$requiredQnty_from_pi.'-'.$prevRecQnty[$order_id]['qnty']; ?>">
													<input type="text" name="txtBalanceQnty[]" id="txtBalanceQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo number_format($balance_req_qnty,2,".",""); ?>" readonly>
												</td>
												<?
												if($roll_maintained==1)
												{
													?>
													<td  align="center">
														<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px" value="<? if($roll_no!=0) echo $roll_no; ?>" placeholder="<? echo $roll_arr[$order_id]+1; ?>" onBlur="roll_duplication_check(<? echo $i; ?>);" />
													</td>
													<td  align="center"><input type="text" name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $barcode_no; ?>" disabled/></td>
													<td >
														<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
														<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
													</td>
													<?
												}
												?>
											</tr>
										<?
										$i++;
										$tot_req_qnty+=$final_job_required_qnty;
									
									}
								}
							} */
							
							?>
							<input type="hidden" name="tot_po_qnty" id="tot_po_qnty" class="text_boxes" value="<? echo $tot_po_qnty; ?>">
							<input type="hidden" name="tot_req_qnty" id="tot_req_qnty" class="text_boxes" value="<? echo $tot_req_qnty; ?>">
	                        <input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="<? echo $i-1; ?>">
	                    </tbody>
						</table>
					</div>
					<table width="<? echo $width; ?>">
						<tr>
							<td align="center" >
								<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
							</td>
						</tr>
					</table>
				</div>
				<?
			}

			?>
	    	</div>
			<?
		}
		else
		{
			//echo 'wo po booking..........................................';
			?>
			<div style="width:<? echo $width; ?>px; margin-top:10px" align="center">
				<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="350" align="center">
					<thead>
						<th>Total Receive Qnty</th>
						<th>Distribution Method</th>
					</thead>
					<tr class="general">
						<td><input type="text" name="txt_prop_grey_qnty" id="txt_prop_grey_qnty" class="text_boxes_numeric" value="<? echo $txt_receive_qnty; ?>" style="width:120px" onBlur="distribute_qnty(document.getElementById('cbo_distribiution_method').value)" <? echo $disabled; ?>></td>
						<td>
							<?
								$distribiution_method=array(0=>"--Select--",1=>"Proportionately",2=>"Manually");
								echo create_drop_down( "cbo_distribiution_method", 160, $distribiution_method,"",0, "",$prev_distribution_method, "distribute_qnty(this.value);", $disable_drop_down );
							?>
						</td>
					</tr>
				</table>
			</div>
			<div style="margin-left:5px; margin-top:5px">
				<table style="float: left;" class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="<? echo $width-20; ?>">
					<thead>
						<th width="130">Job No</th>
						<th width="130">Style Ref.</th>
						<th width="100">Booking No</th>
						<th width="100">Req. Qnty</th>
						<th width="100">GRN Qnty</th>
						<th width="40">Roll</th>
						<th width="100">Barcode No.</th>
						<th width="100">RF Id</th>
						<th width="80"></th>
					</thead>
					<tbody id="tbl_list_search">
						<?
						//echo $save_data ."&&". $receive_basis;die;
						$i=1; $tot_po_qnty=0; $tot_req_qnty=0; $po_array=array();
						if($save_data!="" && ($receive_basis==4 || $receive_basis==6 || $receive_basis==1))
						{
							$po_id = explode(",",$po_id);
							//echo $save_data;
							$explSaveData = explode(",",$save_data);
							for($z=0;$z<count($explSaveData);$z++)
							{
								if ($i%2==0)
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";

								$po_wise_data = explode("**",$explSaveData[$z]);
								$order_id=$po_wise_data[0];
								$fin_qty=$po_wise_data[1];
								$roll_no=$po_wise_data[2];
								$roll_id=$po_wise_data[3];
								$barcode_no=$po_wise_data[4];

								if(in_array($order_id,$po_id))
								{
									$po_data=sql_select("SELECT a.job_no,a.style_ref_no, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id=$order_id");
									//$sql_cuml="select po_breakdown_id, sum(quantity) as qnty from order_wise_pro_details where po_breakdown_id in ($batch_po_id) and entry_form=37 and status_active=1 and is_deleted=0 group by po_breakdown_id";
									if($roll_maintained==1)
									{
										if(!(in_array($order_id,$po_array)))
										{
											$tot_po_qnty+=$po_data[0][csf('po_qnty_in_pcs')];
											$orginal_val=1;
											$po_array[]=$order_id;
										}
										else
										{
											$orginal_val=0;
										}
									}
									else
									{
										if(!(in_array($order_id,$po_array)))
										{
											$tot_po_qnty+=$po_data[0][csf('po_qnty_in_pcs')];
											$orginal_val=1;
											$po_array[]=$order_id;
										}
										else
										{
											$orginal_val=0;
										}

										$roll_id=0;
									}
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
										<td ><p><? echo $po_data[0][csf('job_no')]; ?></p></td>
										<td ><p><? echo $po_data[0][csf('style_ref_no')]; ?></p></td>
										<td >
											<p><? echo $po_data[0][csf('po_number')]; ?></p>
											<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $order_id; ?>">
											<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="<? echo $orginal_val; ?>">
											<input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="<? echo $roll_id; ?>">
										</td>
										<td  align="right">
											<? echo $po_data[0][csf('po_qnty_in_pcs')] ?>
											<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $po_data[0][csf('po_qnty_in_pcs')]; ?>">
										</td>
										<?
										if($roll_maintained!=1)
										{
										?>
											<td>
		                            			<input type="text" name="txtRollNo[]" id="txtRollNo_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px" value="<? //echo $grey_qnty; ?>">
		                            		</td>
		                            	<?
										}
										?>
	                                    <td  align="center"><? echo change_date_format($po_data[0][csf('pub_shipment_date')]); ?></td>
	                                    <td  align="center"><? echo ""; ?></td>
										<td align="center">
											<input type="text" name="txtGreyQnty[]" id="txtGreyQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $fin_qty; ?>">
										</td>
										<?
										if($roll_maintained==1)
										{
										?>
											<td  align="center">
												<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px" value="<? if($roll_no!=0) echo $roll_no; ?>" placeholder="<? echo $roll_arr[$order_id]+1; ?>" onBlur="roll_duplication_check(<? echo $i; ?>);" />
											</td>
											<td  align="center"><input type="text" name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $barcode_no; ?>" disabled/></td>
											<td >
												<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
												<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
											</td>
										<?
										}
										?>
									</tr>
								<?
								$i++;
								}
							}

							$result=implode(",",array_diff($po_id, $po_array));
							if($result!="")
							{
								$po_sql="SELECT a.job_no,a.style_ref_no,b.id, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in ($result)";
							  //$po_data=sql_select("select b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id=$order_id");
								$nameArray=sql_select($po_sql);
								foreach($nameArray as $row)
								{
									if ($i%2==0)
										$bgcolor="#E9F3FF";
									else
										$bgcolor="#FFFFFF";

									$orginal_val=1;
									$roll_id=0;

									$tot_po_qnty+=$row[csf('po_qnty_in_pcs')];

								 ?>
									<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
										<td ><p><? echo $row[csf('job_no')]; ?></p></td>
										<td ><p><? echo $row[csf('style_ref_no')]; ?></p></td>
										<td >
											<p><? echo $row[csf('po_number')]; ?></p>
											<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
											<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="<? echo $orginal_val; ?>">
											<input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="<? echo $roll_id; ?>">
										</td>
										<td  align="right">
											<? echo $row[csf('po_qnty_in_pcs')]; ?>
											<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $row[csf('po_qnty_in_pcs')]; ?>">
										</td>
										<?
										if($roll_maintained!=1)
										{
										?>
											<td>
		                            			<input type="text" name="txtRollNo[]" id="txtRollNo_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px" value="<? //echo $grey_qnty; ?>">
		                            		</td>
	                            		<?
										}
										?>
	                                    <td  align="center"><? echo change_date_format($po_data[0][csf('pub_shipment_date')]); ?></td>
	                                    <td  align="center"><? echo ""; ?></td>
										<td align="center">
											<input type="text" name="txtGreyQnty[]" id="txtGreyQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="" >
										</td>
										<?
										if($roll_maintained==1)
										{
										?>
											<td  align="center">
												<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px" value="" placeholder="<? echo $roll_arr[$order_id]+1; ?>" onBlur="roll_duplication_check(<? echo $i; ?>);" />
											</td>
											<td  align="center"><input type="text" name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="" disabled/></td>
											<td >
												<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
												<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
											</td>
										<?
										}
										?>
									</tr>
								<?
								$i++;
								}
							}
						}
						else if($save_data!="" && $receive_basis==2)
						{

							$po_sql="SELECT b.job_no_mst,a.style_ref_no,b.id as po_id, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs,c.fabric_color_id,c.booking_no , d.body_part_id ,c.uom, d.gsm_weight ,c.dia_width,e.fabric_ref,e.rd_no,d.gsm_weight_type as weight_type,e.cutable_width,c.fin_fab_qnty from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c, wo_pre_cost_fabric_cost_dtls d,lib_yarn_count_determina_mst e  where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.pre_cost_fabric_cost_dtls_id=d.id  and d.lib_yarn_count_deter_id=e.id  and c.booking_no='$booking_no' and c.fabric_color_id=$hidden_color_id and d.body_part_id=$cbo_body_part_id and c.dia_width='$hidden_dia_width' and d.gsm_weight='$hidden_gsm_weight' and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.lib_yarn_count_deter_id=$fabric_desc_id and d.uom=$cbouom  group by b.job_no_mst,a.style_ref_no,b.id, b.po_number,a.total_set_qnty,b.po_quantity,c.fabric_color_id,c.booking_no , d.body_part_id ,c.uom, d.gsm_weight ,c.dia_width,e.fabric_ref,e.rd_no,d.gsm_weight_type,e.cutable_width,c.fin_fab_qnty order by b.job_no_mst";
							$nameArray=sql_select($po_sql);
							foreach($nameArray as $row)
							{
								//$po_data_array[$row[csf('po_id')]][$row[csf('booking_no')]]['style_ref_no']=$row[csf('style_ref_no')];
								$po_data_array[$row[csf('job_no_mst')]][$row[csf('booking_no')]]['requ_qty']+=$row[csf('fin_fab_qnty')];
								$po_data[$row[csf('po_id')]]['job_no_mst']=$row[csf('job_no_mst')];
								$po_data[$row[csf('po_id')]]['style_ref_no']=$row[csf('style_ref_no')];
							}

							//$po_id = explode(",",$po_id);

							$sqlroll = sql_select("select barcode_no,roll_used from pro_roll_details where status_active=1 and is_deleted=0 and booking_no='$booking_no'");
							foreach ($sqlroll as $row) {
								$rollData[$row[csf("barcode_no")]]['roll_used'] = $row[csf("roll_used")];
							}

							

							$explSaveData = explode(",",$save_data);
							foreach($explSaveData as $val)
							{
								$order_data = explode("**",$val);
								$order_id=$order_data[0];
								$hdnJobNo=$order_data[1];
								$hdnBookingNo=$order_data[2];
								$fin_qty=$order_data[3];
								$roll_no=$order_data[4];
								$roll_id=$order_data[5];
								$roll_table_id=$order_data[6];
								$barcode_no=$order_data[7];
								$rf_id=$order_data[8];

								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								$roll_used = $rollData[$barcode_no]['roll_used'];

								if(!(in_array($order_id,$po_array)))
								{
									$tot_po_qnty+=$po_data_array[$order_id]['qty'];
									//$orginal_val=1;
									$po_array[]=$order_id;
								}
								if ($roll_used == 1) $orginal_val = 1; else $orginal_val = 0;

								if($hdnJobNo == ""){
									$job_number = $po_data[$order_id]['job_no_mst'];
									$style_ref_number = $po_data[$order_id]['style_ref_no'];
								}
								else
								{
									$job_number = $hdnJobNo;
									$style_ref_number = $jobRefdata[$hdnJobNo]['style_ref_no'];
								}

								?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
									<td ><p><? echo $job_number; ?></p></td>
									<td>
										<p><? echo $style_ref_number; ?></p>
									</td>
									<td >
										<p><? echo $hdnBookingNo; ?></p>
										<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $order_id; ?>">
										<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="<? echo $orginal_val; ?>">
										<input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="<? echo $roll_id; ?>">
										<input type="hidden" name="txtRollTableId[]" id="txtRollTableId_<? echo $i; ?>" value="<? echo $roll_table_id; ?>">
										<input type="hidden" name="hidden_cummulative_rcv_qnty[]" id="hidden_cummulative_rcv_qnty_<? echo $i; ?>" value="<? echo $prevRecQnty; ?>">
										<input type="hidden" name="hdnJobNo[]" id="hdnJobNo_<? echo $i; ?>" value="<? echo $job_number; ?>">
										<input type="hidden" name="hdnBookingNo[]" id="hdnBookingNo_<? echo $i; ?>" value="<? echo $hdnBookingNo; ?>">
									</td>
									<td  align="right">
										<? echo $po_data_array[$job_number][$hdnBookingNo]['requ_qty']; ?>
									</td>

									<td align="center">
										<input type="text" name="txtGreyQnty[]" id="txtGreyQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $fin_qty; ?>"/>
									</td>
									<td  align="center">
										<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px" value="<? if($roll_no!=0) echo $roll_no; ?>" placeholder="<? echo $roll_arr[$order_id]+1; ?>" onBlur="roll_duplication_check(<? echo $i; ?>);" />
									</td>
									<td  align="center"><input type="text" name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $barcode_no; ?>" disabled/>
									</td>
									<td align="center">
										<input type="text" name="txtRFId[]" id="txtRFId_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $rf_id; ?>" >
									</td>
									<td >
										<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
										<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
									</td>
								</tr>
							<?
							$i++;
							}
							/* foreach($po_data_array as $order_id=>$val)
							{
								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$orginal_val=1;
								if(!(in_array($order_id,$po_array)))
								{
									$tot_po_qnty+=$val['qty'];
									$orginal_val=1;
								?>
									<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
									<td ><p><? echo $po_data_array[$order_id]['job_no']; ?></p></td>
									<td ><p><? echo $po_data_array[$order_id]['style_ref_no']; ?></p></td>
										<td >
											<p><? echo $po_data_array[$order_id]['po_no']; ?></p>
											<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $order_id; ?>">
											<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="<? echo $orginal_val; ?>">
											<input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="<? echo $roll_id; ?>">
										</td>
										<td  align="right">
											<? echo $val['qty']; ?>
											<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $val['qty']; ?>">
										</td>
										<td  align="center"><? echo change_date_format($val['date']); ?></td>
										<td  align="center"><? echo ""; ?></td>
										<td  align="right"><? echo number_format($req_qty_array[$order_id],2,'.',''); ?></td>
										<td  align="right"><? echo number_format($cumu_rec_qty[$order_id],2,'.',''); ?></td>
										<td align="center">
											<input type="text" name="txtfinishQnty[]" id="txtfinishQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value=""/>
										</td>
										<td  align="center">
											<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px" value="" placeholder="<? echo $roll_arr[$order_id]+1; ?>" onBlur="roll_duplication_check(<? echo $i; ?>);" />
										</td>
										<td ><input type="text" name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="" disabled/></td>
										<td >
											<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
											<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
										</td>
									</tr>
								<?
									$i++;
								}
							} */
							
						}
						else
						{
							if ($txt_fabric_ref) {$txt_fabric_ref_cond="and e.fabric_ref='$txt_fabric_ref'";}
							if ($txt_rd_no) {$txt_rd_no_cond="and e.rd_no='$txt_rd_no'";}
							if ($cbo_weight_type) {$cbo_weight_type_cond="and d.gsm_weight_type='$cbo_weight_type'";}
							if ($txt_cutable_width) {$txt_cutable_width_cond="and e.cutable_width='$txt_cutable_width'";}
							if ($hidden_gsm_weight=="" && $db_type==2) {$hidden_gsm_weight_cond="and a.fab_weight is NULL";} else{$hidden_gsm_weight_cond="and a.fab_weight='$hidden_gsm_weight'";}

							if($booking_no!="" && $receive_basis==2){$bookingNo_cond="and d.booking_no='$booking_no'";$bookingNo_cond2="and h.booking_no='$booking_no'";}
							
							$req_qty_array=array();
							$poIDS="";$bookingNos="";$jobNos="";$poNos="";							
							
							$prev_recv_qnty=sql_select("select a.id, c.po_breakdown_id,c.quantity as qnty,f.job_no_mst,d.booking_no ,g.style_ref_no  from inv_transaction a, product_details_master b,order_wise_pro_details c,inv_receive_master d,pro_finish_fabric_rcv_dtls e,wo_po_break_down f,wo_po_details_master  g,wo_booking_dtls h where a.prod_id=b.id and b.id=c.prod_id and a.id=c.trans_id and a.mst_id=d.id and d.id=e.mst_id and c.po_breakdown_id=f.id and f.job_no_mst=g.job_no and g.job_no=h.job_no and f.id=h.po_break_down_id and a.receive_basis=$receive_basis  and d.receive_basis=$receive_basis and b.color='".$hidden_color_id."' and a.body_part_id='".$cbo_body_part_id."' $bookingNo_cond and b.detarmination_id='".$fabric_desc_id."' and e.original_gsm='".$hidden_gsm_weight."' and e.original_width='".$hidden_dia_width."' and e.uom='".$cbouom."' and a.status_active=1 and c.entry_form=17 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.status_active=1 and d.is_deleted=0 and a.item_category=3 and d.item_category=3 and a.transaction_type=1 group by a.id, c.po_breakdown_id,c.quantity,f.job_no_mst,d.booking_no ,g.style_ref_no");
							foreach ($prev_recv_qnty as $row) 
							{
								$prev_recv_qnty_arr[$row[csf('job_no_mst')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]]['qnty']+=$row[csf('qnty')];

								if($update_hdn_transaction_id != "" && $update_hdn_transaction_id == $row[csf('id')])
								{
									$thisChallanRcvQntyArray[$row[csf('job_no_mst')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]] +=$row[csf('qnty')];
								}
							}
							
							$prev_recv_return_qnty=sql_select("select a.id, c.po_breakdown_id,c.quantity as qnty,f.job_no_mst,h.booking_no ,g.style_ref_no from inv_transaction a, product_details_master b,order_wise_pro_details c ,wo_po_break_down f,wo_po_details_master  g,wo_booking_dtls h where a.prod_id=b.id and b.id=c.prod_id and a.id=c.trans_id and c.po_breakdown_id=f.id and f.job_no_mst=g.job_no and g.job_no=h.job_no and f.id=h.po_break_down_id and a.status_active=1 and c.entry_form=202 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.item_category=3 and a.transaction_type=3 and b.color='".$hidden_color_id."' and a.cons_uom='".$cbouom."' and a.body_part_id='".$cbo_body_part_id."' and b.detarmination_id='".$fabric_desc_id."' $bookingNo_cond2 group by a.id, c.po_breakdown_id,c.quantity,f.job_no_mst,h.booking_no ,g.style_ref_no "); 

							foreach ($prev_recv_return_qnty as $row) 
							{
								//$prev_recv_return_qnty_arr[$row[csf('po_breakdown_id')]]['qnty']+=$row[csf('qnty')];
								$prev_recv_return_qnty_arr[$row[csf('job_no_mst')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]]['qnty']+=$row[csf('qnty')];

								/*if($update_hdn_transaction_id != "" && $update_hdn_transaction_id == $row[csf('id')])
								{
									$thisChallanRcvQntyArray[$row[csf('po_breakdown_id')]] +=$row[csf('qnty')];
								}*/
							}

							
							$nameArray2=sql_select("SELECT b.job_no_mst,a.style_ref_no,b.id as po_id, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs,c.fabric_color_id,c.booking_no , d.body_part_id ,c.uom, d.gsm_weight ,c.dia_width,e.fabric_ref,e.rd_no,d.gsm_weight_type as weight_type,e.cutable_width,c.fin_fab_qnty from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c, wo_pre_cost_fabric_cost_dtls d,lib_yarn_count_determina_mst e  where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.pre_cost_fabric_cost_dtls_id=d.id  and d.lib_yarn_count_deter_id=e.id  and c.booking_no='$booking_no' and c.fabric_color_id=$hidden_color_id and d.body_part_id=$cbo_body_part_id and c.dia_width='$hidden_dia_width' and d.gsm_weight='$hidden_gsm_weight' and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.lib_yarn_count_deter_id=$fabric_desc_id and d.uom=$cbouom  group by b.job_no_mst,a.style_ref_no,b.id, b.po_number,a.total_set_qnty,b.po_quantity,c.fabric_color_id,c.booking_no , d.body_part_id ,c.uom, d.gsm_weight ,c.dia_width,e.fabric_ref,e.rd_no,d.gsm_weight_type,e.cutable_width,c.fin_fab_qnty order by b.job_no_mst");


							foreach($nameArray2 as $row)
							{
								$dataArr[$row[csf('job_no_mst')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]]["po_id"].=$row[csf('po_id')].",";

								$dataArr[$row[csf('job_no_mst')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]]["wo_qnty"]+=$row[csf('fin_fab_qnty')];
								$dataArr2[$row[csf('booking_no')]]+=$row[csf('fin_fab_qnty')];
								$poIDS.=$row[csf('po_id')].",";
							}
													
							$poIDS=implode(",",array_unique(explode(",", $poIDS))); 
							$poIDS=chop($poIDS,",");
							$poNos=implode(",",array_unique(explode(",", $poNos))); 
							$poNos=chop($poNos,",");
							
							if($receive_basis==2)
							{

								$finish_req_qnty_sql=sql_select("SELECT d.id as po_id,d.job_no_mst, d.po_number, sum(b.fin_fab_qnty) as  fin_fab_qnty,b.booking_no,e.style_ref_no from wo_booking_dtls b, wo_po_break_down d ,wo_pre_cost_fabric_cost_dtls c, wo_po_details_master e where b.po_break_down_id=d.id and b.pre_cost_fabric_cost_dtls_id=c.id  and c.job_no=e.job_no and d.job_no_mst=e.job_no and d.id in($poIDS) and b.fabric_color_id in($hidden_color_id) and b.booking_no='$booking_no' and c.body_part_id=$cbo_body_part_id and  c.lib_yarn_count_deter_id=$fabric_desc_id and c.gsm_weight='".$hidden_gsm_weight."' and b.dia_width='".$hidden_dia_width."' and c.uom='".$cbouom."' and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by d.id,d.job_no_mst, d.po_number ,b.booking_no,e.style_ref_no"); 
								$finish_req_qnty_total=0;
								foreach ($finish_req_qnty_sql as $row) {

									$finish_req_qnty_arr[$row[csf('id')]]["finish_reqt_qnty"]=$row[csf('fin_fab_qnty')];
									$finish_req_qnty_total+=$row[csf('fin_fab_qnty')];
									$finish_req_qnty_arr2[$row[csf('job_no_mst')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]][$row[csf('po_id')]]["finish_reqt_qnty"]+=$row[csf('fin_fab_qnty')];
									$finish_req_qnty_arr3[$row[csf('job_no_mst')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]]["finish_reqt_qnty"]+=$row[csf('fin_fab_qnty')];
								}
							
							}
							else
							{
								$booking_mst_lib=return_library_array("select booking_no,id from wo_booking_mst where booking_no in($bookingNos)",'booking_no','id');


								$finish_req_qnty_sql=sql_select("SELECT d.id as po_id,d.job_no_mst, d.po_number, sum(b.fin_fab_qnty) as  fin_fab_qnty,b.booking_no,e.style_ref_no , (e.total_set_qnty*d.po_quantity) as po_qnty_in_pcs  from wo_booking_dtls b, wo_po_break_down d ,wo_pre_cost_fabric_cost_dtls c, wo_po_details_master e where b.po_break_down_id=d.id and b.pre_cost_fabric_cost_dtls_id=c.id  and c.job_no=e.job_no and d.job_no_mst=e.job_no and d.id in($poIDS) and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by d.id,d.job_no_mst, d.po_number ,b.booking_no,e.style_ref_no,e.total_set_qnty,d.po_quantity"); 
								$finish_req_qnty_total=0;
								foreach ($finish_req_qnty_sql as $row) {

									$finish_req_qnty_arr[$row[csf('id')]]["finish_reqt_qnty"]=$row[csf('po_qnty_in_pcs')];
									$finish_req_qnty_total+=$row[csf('po_qnty_in_pcs')];
									$finish_req_qnty_arr2[$row[csf('job_no_mst')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]][$row[csf('po_id')]]["finish_reqt_qnty"]+=$row[csf('po_qnty_in_pcs')];
									$finish_req_qnty_arr3[$row[csf('job_no_mst')]][$row[csf('booking_no')]][$row[csf('style_ref_no')]]["finish_reqt_qnty"]+=$row[csf('po_qnty_in_pcs')];
								}

							}


							
							foreach($dataArr as $jobNo => $job_data)
							{
								foreach($job_data as $bookingNo => $booking_data)
								{
									foreach($booking_data as $styleRef => $row)
									{
										if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										$orginal_val=1;
										$roll_id=0;
										$disable="";

										$prevRecReturnQnty=$prev_recv_return_qnty_arr[$jobNo][$bookingNo][$styleRef]['qnty'];

										$prevRecQnty=$prev_recv_qnty_arr[$jobNo][$bookingNo][$styleRef]['qnty']-$prevRecReturnQnty;
										$requiredQnty_finish2=$finish_req_qnty_arr3[$jobNo][$bookingNo][$styleRef]["finish_reqt_qnty"];
										$balance_req_qnty=$requiredQnty_finish2-$prevRecQnty;

										$availabeQntyWithOverRcv=(($requiredQnty_finish2*$over_receive_limit)/100)+$requiredQnty_finish2;
										$availabeQntyWithOverRcv=$availabeQntyWithOverRcv-$prevRecQnty;
										$po_arr_uniq=array_unique(explode(",", chop($row['po_id'],",") ));
										$po_ids=implode(",", $po_arr_uniq);
										$hdn_po_ratio_ref="";
										foreach ($po_arr_uniq as $poID) {
											$orderRequiredQnty=$finish_req_qnty_arr2[$jobNo][$bookingNo][$styleRef][$poID]["finish_reqt_qnty"];
											$hdn_po_ratio_ref.=$poID."=".$orderRequiredQnty.",";
										}
										$hdn_po_ratio_ref=chop($hdn_po_ratio_ref,",");

										?>
											<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
												<td ><p><? echo $jobNo; ?></p></td>
												<td ><p><? echo $styleRef; ?></p></td>
												<td title="<? echo $po_ids; ?>">
													<p><? echo $bookingNo; ?></p>
													<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? //echo $po_ids; ?>">
													<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="<? echo $orginal_val; ?>">
													<input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="<? echo $roll_id; ?>">
													<input type="hidden" name="txtRollTableId[]" id="txtRollTableId_<? echo $i; ?>" value="<? echo $roll_id; ?>">
													<input type="hidden" name="txtHdnPoRatio[]" id="txtHdnPoRatio_<? echo $i; ?>" value="<? echo $hdn_po_ratio_ref; ?>">
													<input type="hidden" name="hidden_cummulative_rcv_qnty[]" id="hidden_cummulative_rcv_qnty_<? echo $i; ?>" value="<? echo $prevRecQnty; ?>">
													<input type="hidden" name="hdnJobNo[]" id="hdnJobNo_<? echo $i; ?>" value="<? echo $jobNo; ?>">
													<input type="hidden" name="hdnBookingNo[]" id="hdnBookingNo_<? echo $i; ?>" value="<? echo $bookingNo; ?>">
												</td>
												
												<td  align="right" title="Over Receive =<? echo $over_receive_limit;?>% , Total Required qnty with over receive = <? echo $availabeQntyWithOverRcv; ?> , Previous Receive qnty = <? echo $prevRecQnty; ?> Total balance is = <? echo $availabeQntyWithOverRcv-$prevRecQnty;?>  ">
													<?
														echo number_format($requiredQnty_finish2,2,'.','');
														?>
														<input type="hidden" name="txtReqQnty[]" id="txtReqQnty_<? echo $i; ?>" value="<? echo $requiredQnty_finish2; ?>">

												</td>
												<td   align="center">
													<input type="text" name="txtGreyQnty[]" id="txtGreyQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $grey_qnty; ?>" <? echo $disable; ?>>
													<input type="hidden" name="thisChallanRcvQnty[]" id="thisChallanRcvQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $thisChallanRcvQntyArray[$jobNo][$bookingNo][$styleRef]; ?>" <? echo $disable; ?>>

													<input type="hidden" name="txtRecQntyPrev[]" id="txtRecQntyPrev_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $prevRecQnty; ?>" <? echo $disable; ?>>
													<input type="hidden" name="availabeQntyWithOverRcv[]" id="availabeQntyWithOverRcv_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $availabeQntyWithOverRcv; ?>" <? echo $disable; ?>>
												</td>

												<td  align="center">
														<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px" value="<? if($roll_no!=0) echo $roll_no; ?>" <? echo $disable; ?> placeholder="<? echo $roll_arr[$order_id]+1; ?>" onBlur="roll_duplication_check(<? echo $i; ?>);" />
													</td>
												<td  align="center">
													<input type="text" name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $barcode_no; ?>" disabled/>
												</td>
												<td align="center">
													<input type="text" name="txtRFId[]" id="txtRFId_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="" readonly>
												</td>
												<td >
													<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
													<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
												</td>
											</tr>
										<?
										$i++;
										$tot_req_qnty+=$requiredQnty_finish2;
									}
								}
							}							
						}
						?>
						<input type="hidden" name="tot_req_qnty" id="tot_req_qnty" class="text_boxes" value="<? echo $tot_req_qnty; ?>">
	                    <input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="<? echo $i-1; ?>">
	                </tbody>
					</table>
				</div>
				<table width="<? echo $width; ?>">
					 <tr>
						<td align="center" >
							<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
						</td>
					</tr>
				</table>
			</div>
			<?
		}
		?>
			<div id="search_div" style="margin-top:10px">
		<?
		if($type!=1)
		{
			?>
			</form>
		    <?
		}
		?>
	    </div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}
if ($action=="po_popup")
{
	echo load_html_head_contents("PO Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	
	$data=explode("_",$data);
	$po_id=$data[0]; $type=$data[1];

	if($type==1)
	{
		$dtls_id=$data[2];
		$roll_maintained=$data[3];
		$save_data=$data[4];
		$prev_distribution_method=$data[5];
		$receive_basis=$data[6];
		$txt_deleted_id=$data[7];
		$hidden_pi_id=$data[8];
		$update_hdn_transaction_id=$data[9];
	}

	if($roll_maintained==1)
	{
		$disable_drop_down=1;
		$prev_distribution_method=2;
		$disabled="disabled='disabled'";

		$width="900";
		$roll_arr=return_library_array("select po_breakdown_id, max(roll_no) as roll_no from pro_roll_details where entry_form=17 group by po_breakdown_id",'po_breakdown_id','roll_no');
	}
	else
	{
		$prev_distribution_method=1;
		$disabled="";
		$disable_drop_down=0;
		$width="1000";
	}
	
	//Field lavel Access.................start
	foreach($field_level_data_arr[$cbo_company_id] as $val=>$row){
		$is_disable= $row[is_disable];
		$defalt_value= $row[defalt_value];
	}
	if($is_disable){$disable_drop_down=$is_disable;}
	if($defalt_value){$prev_distribution_method=$defalt_value;}
	//Field lavel Access.................end
	
	

	if($receive_basis==4 || $receive_basis==6){
		$enable_des_cond = 0;
		$massageShow = "";
	}else{
		$enable_des_cond = 1;
		$massageShow = "<h2 style='color:#FF0000'>The basis of selected PI is Independent.</h2>";
	}
	$variable_set_invent = sql_select("select category,over_rcv_status,over_rcv_percent,over_rcv_payment from variable_inv_ile_standard where company_name=$cbo_company_id and variable_list=23 and category = 3 and status_active =1 and is_deleted=0 order by id");
	$over_receive_limit = !empty($variable_set_invent) ? $variable_set_invent[0][csf('over_rcv_percent')] : 0;

	?>

	<script>
		var receive_basis=<? echo $receive_basis; ?>;
		var roll_maintained=<? echo $roll_maintained; ?>;
		var balance_qnty = <? if($txt_bla_order_qty==""){  echo $txt_bla_order_qty=0;}else{ echo  $txt_bla_order_qty=$txt_bla_order_qty;}?>;
		var exists_receive_qnty = <? if($txt_receive_qnty=="") {echo $txt_receive_qnty=0;}else {echo $txt_receive_qnty=$txt_receive_qnty;} ?>;
		var dtls_id = <? if($dtls_id!="") {echo $dtls_id=$dtls_id;}else {echo $dtls_id=0;} ?>;


		function fn_show_check()
		{
			if( form_validation('cbo_buyer_name','Buyer Name')==false )
			{
				return;
			}
			show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('cbo_buyer_name').value+'_'+'<? echo $all_po_id; ?>', 'create_po_search_list_view', 'search_div', 'woven_finish_fabric_roll_receive_controller', 'setFilterGrid(\'tbl_list_search\',-1);hidden_field_reset();');
			set_all();
		}

		function distribute_qnty(str)
		{
			if(str==1)
			{
				var tot_po_qnty=$('#tot_po_qnty').val()*1;
				var tot_req_qnty=$('#tot_req_qnty').val()*1;
				var txt_prop_grey_qnty=$('#txt_prop_grey_qnty').val()*1;

				$("#tbl_list_search").find('tr').each(function()
				{
					var txtOrginal=$(this).find('input[name="txtOrginal[]"]').val()*1;
					var isDisbled=$(this).find('input[name="txtGreyQnty[]"]').is(":disabled");

					if(txtOrginal==0)
					{
						$(this).remove();
					}
					else if(isDisbled==false && txtOrginal==1)
					{
						if(receive_basis==2)
						{
							var req_qnty=$(this).find('input[name="txtReqQnty[]"]').val()*1;
							var perc=(req_qnty/tot_req_qnty)*100;
						}
						else
						{
							var po_qnty=$(this).find('input[name="txtPoQnty[]"]').val()*1;
							var perc=(po_qnty/tot_po_qnty)*100;
						}

						var grey_qnty=(perc*txt_prop_grey_qnty)/100;
						$(this).find('input[name="txtGreyQnty[]"]').val(grey_qnty.toFixed(3));
						//$(this).find('input[name="txtGreyQnty[]"]').val(number_format_common(grey_qnty,"","",2));
					}
				});
			}
			else
			{
				$('#txt_prop_grey_qnty').val('');
				$("#tbl_list_search").find('tr').each(function()
				{
					if($(this).find('input[name="txtGreyQnty[]"]').is(":disabled")==false)
					{
						$(this).find('input[name="txtGreyQnty[]"]').val('');
					}
				});
			}
		}

		function roll_duplication_check(row_id)
		{
			var row_num=$('#tbl_list_search tr').length;
			var po_id=$('#txtPoId_'+row_id).val();
			var roll_no=$('#txtRoll_'+row_id).val();

			if(roll_no*1>0)
			{
				for(var j=1; j<=row_num; j++)
				{
					if(j==row_id)
					{
						continue;
					}
					else
					{
						var po_id_check=$('#txtPoId_'+j).val();
						var roll_no_check=$('#txtRoll_'+j).val();

						if(po_id==po_id_check && roll_no==roll_no_check)
						{
							alert("Duplicate Roll No.");
							$('#txtRoll_'+row_id).val('');
							return;
						}
					}
				}

				var txtRollId=$('#txtRollId_'+row_id).val();
				var data=po_id+"**"+roll_no+"**"+txtRollId;
				var response=return_global_ajax_value( data, 'roll_duplication_check', '', 'woven_finish_fabric_roll_receive_controller');
				var response=response.split("_");

				if(response[0]!=0)
				{
					var po_number=$('#tr_'+row_id).find('td:first').text();
					alert("This Roll Already Used. Duplicate Not Allowed");
					$('#txtRoll_'+row_id).val('');
					return;
				}
			}

		}

		function add_break_down_tr( i )
		{
			var cbo_distribiution_method=$('#cbo_distribiution_method').val();
			var isDisbled=$('#txtGreyQnty_'+i).is(":disabled");

			if(cbo_distribiution_method==2 && isDisbled==false)
			{
				var row_num=$('#tbl_list_search tr').length;
				row_num++;

				var clone= $("#tr_"+i).clone();
				clone.attr({
					id: "tr_" + row_num,
				});

				clone.find("input,select").each(function(){

				$(this).attr({
				  'id': function(_, id) { var id=id.split("_"); return id[0] +"_"+ row_num },
				  'name': function(_, name) { return name },
				  'value': function(_, value) { return value }
				});

				}).end();

				$("#tr_"+i).after(clone);

				$('#txtOrginal_'+row_num).removeAttr("value").attr("value","0");
				$('#txtRoll_'+row_num).removeAttr("value").attr("value","");
				$('#txtGreyQnty_'+row_num).removeAttr("value").attr("value","");
				$('#txtRoll_'+row_num).removeAttr("onBlur").attr("onBlur","roll_duplication_check("+row_num+");");
				$('#txtBarcodeNo_'+row_num).removeAttr("value").attr("value","");

				$('#increase_'+row_num).removeAttr("value").attr("value","+");
				$('#decrease_'+row_num).removeAttr("value").attr("value","-");
				$('#increase_'+row_num).removeAttr("onclick").attr("onclick","add_break_down_tr("+row_num+");");
				$('#decrease_'+row_num).removeAttr("onclick").attr("onclick","fn_deleteRow("+row_num+");");
			}
		}

		function fn_deleteRow(rowNo)
		{
			var txtOrginal=$('#txtOrginal_'+rowNo).val()*1;
			var txtRollId=$('#txtRollId_'+rowNo).val();
			var txt_deleted_id=$('#hide_deleted_id').val();
			var selected_id='';
			if(txtOrginal==0)
			{
				if(txtRollId!='')
				{
					if(txt_deleted_id=='') selected_id=txtRollId; else selected_id=txt_deleted_id+','+txtRollId;
					$('#hide_deleted_id').val( selected_id );
				}
				$("#tr_"+rowNo).remove();
			}
		}

		var selected_id = new Array();

		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;

			tbl_row_count = tbl_row_count-1;
			for( var i = 1; i <= tbl_row_count; i++ ) {
				js_set_value( i,1 );
			}
		}

		function toggle( x, origColor )
		{
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function set_all()
		{
			var old=document.getElementById('txt_po_row_id').value;
			if(old!="")
			{
				old=old.split(",");
				for(var i=0; i<old.length; i++)
				{
					js_set_value( old[i],0 )
				}
			}
		}

		function js_set_value( str, check_or_not )
		{
			if(check_or_not==1)
			{
				var roll_used=$('#roll_used'+str).val();
				if(roll_used==1)
				{
					var po_number=$('#search' + str).find("td:eq(3)").text();
					alert("Batch Roll Found Against PO- "+po_number);
					return;
				}
			}

			toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

			if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
				selected_id.push( $('#txt_individual_id' + str).val() );

			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
				}
				selected_id.splice( i, 1 );
			}
			var id = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );

			$('#po_id').val( id );
		}

		function show_grey_prod_recv()
		{
			var po_id=$('#po_id').val();
			show_list_view ( po_id+'_'+'1'+'_'+'<? echo $dtls_id; ?>'+'_'+'<? echo $roll_maintained; ?>'+'_'+'<? echo $save_data; ?>'+'_'+'<? echo $prev_distribution_method; ?>'+'_'+'<? echo $receive_basis; ?>'+'_'+'<? echo $txt_deleted_id; ?>'+'_'+'<? echo $hidden_pi_id; ?>'+'_'+'<? echo $update_hdn_transaction_id; ?>', 'po_popup', 'search_div', 'woven_finish_fabric_roll_receive_controller', '');   
		}

		function hidden_field_reset()
		{
			$('#po_id').val('');
			$('#save_string').val( '' );
			$('#tot_grey_qnty').val( '' );
			$('#number_of_roll').val( '' );
			selected_id = new Array();
		}

		function fnc_close()
		{
			var save_string='';	 var tot_grey_qnty=''; var no_of_roll='';var tot_rollNo='';
			var po_id_array = new Array();
			var overRecvCheckCount=0;
			$("#tbl_list_search").find('tr').each(function()
			{
				var txtPoId=$(this).find('input[name="txtPoId[]"]').val();
				//alert(txtPoId);
				var txtGreyQnty=$(this).find('input[name="txtGreyQnty[]"]').val();
				var txtRollNo=$(this).find('input[name="txtRollNo[]"]').val();
				var txtRoll=$(this).find('input[name="txtRoll[]"]').val();
				var txtRollId=$(this).find('input[name="txtRollId[]"]').val();
				var txtBarcodeNo=$(this).find('input[name="txtBarcodeNo[]"]').val();
				
				var txtRecQntyPrev=$(this).find('input[name="txtRecQntyPrev[]"]').val()*1;
				var thisChallanRcvQnty=$(this).find('input[name="thisChallanRcvQnty[]"]').val()*1;
				var txtGreyQnty=$(this).find('input[name="txtGreyQnty[]"]').val()*1;
				var txtRollNo=$(this).find('input[name="txtRollNo[]"]').val()*1;
				var availabeQntyWithOverRcv=$(this).find('input[name="availabeQntyWithOverRcv[]"]').val()*1;

				tot_grey_qnty=tot_grey_qnty*1+txtGreyQnty*1;
				tot_rollNo=tot_rollNo*1+txtRollNo*1;
				//alert(roll_maintained);return;
				if(roll_maintained!=1)
				{
					txtRoll=0;
					txtBarcodeNo=0;
				}

				if(txtRoll*1>0)
				{
					//no_of_roll=no_of_roll*1+1;
					no_of_roll=no_of_roll*1;
					no_of_roll+=txtRoll*1; //issue id:33591
				}

				if(txtGreyQnty*1>0)
				{

					if(save_string=="")
					{
						save_string=txtPoId+"**"+txtGreyQnty+"**"+txtRoll+"**"+txtRollId+"**"+txtBarcodeNo+"**"+txtRollNo;
					}
					else
					{
						save_string+=","+txtPoId+"**"+txtGreyQnty+"**"+txtRoll+"**"+txtRollId+"**"+txtBarcodeNo+"**"+txtRollNo;
					}

					if( jQuery.inArray( txtPoId, po_id_array) == -1 )
					{
						po_id_array.push(txtPoId);
					}
				}

				var wo_pi_qty = (txtRecQntyPrev - thisChallanRcvQnty + txtGreyQnty);
				if(availabeQntyWithOverRcv<wo_pi_qty)
				{
					//alert("Required quantity is more than receive quantity");
					overRecvCheckCount+=1;
					//return;
				}
			});
			//alert(overRecvCheckCount);
			if(overRecvCheckCount>0)
			{
				alert("Receive quantity is more than required quantity");
				return;
			}
			/*if(exists_receive_qnty==""){
				exists_receive_qnty = 0;
			}

			if(dtls_id>0)
			{
				var wo_pi_qty = (exists_receive_qnty+balance_qnty);
				if(tot_grey_qnty>wo_pi_qty)
				{
					var r = confirm("Over Receive?");
					if (r==false)
					{
						return;
					}
				}

			}else{
				if(tot_grey_qnty>balance_qnty)
				{
					var r = confirm("Over Receive?");
					if (r==false)
					{
						return;
					}
				}
			}*/

			$('#save_string').val( save_string );
			$('#tot_grey_qnty').val( tot_grey_qnty );
			$('#tot_rollNo').val( tot_rollNo );
			$('#number_of_roll').val( no_of_roll );
			$('#all_po_id').val( po_id_array );
			$('#distribution_method').val( $('#cbo_distribiution_method').val() );
			//return;
			parent.emailwindow.hide();
		}
    </script>

	</head>

	<body>
		<div align="center">
		<?
		if($type!=1)
		{
			?>
			<form name="searchdescfrm"  id="searchdescfrm">
				<!--<fieldset style="width:<? //echo $width; ?>px;margin-left:10px">-->

		        	<input type="hidden" name="save_string" id="save_string" class="text_boxes" value="">
		            <input type="hidden" name="tot_grey_qnty" id="tot_grey_qnty" class="text_boxes" value="">
		            <input type="hidden" name="tot_rollNo" id="tot_rollNo" class="text_boxes" value="">
		            <input type="hidden" name="number_of_roll" id="number_of_roll" class="text_boxes" value="">
		            <input type="hidden" name="all_po_id" id="all_po_id" class="text_boxes" value="">
		            <input type="hidden" name="distribution_method" id="distribution_method" class="text_boxes" value="">
		            <input type="hidden" name="hide_deleted_id" id="hide_deleted_id" class="text_boxes" value="<? echo $txt_deleted_id; ?>">
		            <input type="hidden" name="update_hdn_transaction_id" id="update_hdn_transaction_id" class="text_boxes" value="<? echo $update_hdn_transaction_id; ?>">

			<?
		}
		//echo $receive_basis."=".$type;die;
		if(($receive_basis==4 || $receive_basis==6 || $receive_basis==1) && $type!=1)
		{
			?>
			<?
			$po_data=sql_select("SELECT d.po_number,d.job_no_mst, d.po_quantity as po_qnty_in_pcs, d.pub_shipment_date from com_pi_item_details a, wo_booking_dtls b, wo_po_break_down d where a.pi_id='$hidden_pi_id' and a.work_order_no=b.booking_no and b.po_break_down_id=d.id group by d.po_number,d.job_no_mst, d.po_quantity, d.pub_shipment_date");
			//print_r ($po_data[0]);
			//if($save_data !="" || $po_data[0]=="")
			if($po_data[0]=="")
			{
				//echo 'pi without pi id';
				?>
		  		<table cellpadding="0" cellspacing="0" width="<? echo $width-20; ?>" class="rpt_table">
		  			<thead>
		            	<tr><th colspan="4"><? echo $massageShow;?></th></tr>
		                <tr>
		                    <th>Buyer</th>
		                    <th>Search By</th>
		                    <th>Search</th>
		                    <th>
		                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
		                        <input type="hidden" name="po_id" id="po_id" value="">
		                    </th>
		                </tr>
		  			</thead>
		  			<tr class="general">
		  				<td align="center">
		  					<?
		  						echo create_drop_down( "cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $hdn_buyer_id, "",$enable_des_cond);
		  					?>
		  				</td>
		  				<td align="center">
		  					<?
		  						$search_by_arr=array(1=>"PO No",2=>"Job No");
		  						echo create_drop_down( "cbo_search_by", 170, $search_by_arr,"",0, "--Select--", "",$dd,0 );
		  					?>
		  				</td>
		  				<td align="center">
		  					<input type="text" style="width:130px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
		  				</td>
		  				<td align="center">
		  					<input type="button" name="button2" class="formbutton" value="Show" onClick="fn_show_check();" style="width:100px;" />
		  				</td>
		  			</tr>
		  		</table>
		        <?
			}
			//7-1-2017 start
			else if($hidden_pi_id!="" && $save_data =="")
			{
				if ($hidden_pi_id!="") 
				{
					$check_exixting_pi_sql=sql_select("select booking_id from inv_receive_master where booking_id='".$hidden_pi_id."'  and receive_basis=$receive_basis and entry_form=17 and is_deleted=0 and status_active=1 and item_category=3");
					$check_exixting_pi=$check_exixting_pi_sql[0][csf('booking_id')];
				}
				if($check_exixting_pi>0)
				{
					$prev_recv_qnty=sql_select("select a.id, c.po_breakdown_id,c.quantity as qnty from inv_transaction a, product_details_master b,order_wise_pro_details c,inv_receive_master d where a.prod_id=b.id and b.id=c.prod_id and a.id=c.trans_id and a.mst_id=d.id and a.receive_basis=$receive_basis  and d.receive_basis=$receive_basis and b.color='".$hidden_color_id."' and b.dia_width='".$hidden_dia_width."' and d.booking_id='".$hidden_pi_id."' and a.body_part_id='".$cbo_body_part_id."' and b.detarmination_id='".$fabric_desc_id."' and a.status_active=1 and c.entry_form=17 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.status_active=1 and d.is_deleted=0 and a.item_category=3 and d.item_category=3 and a.transaction_type=1");
				}
				else
				{
					$prev_recv_qnty=sql_select("select a.id, c.po_breakdown_id,c.quantity as qnty from inv_transaction a, product_details_master b,order_wise_pro_details c where a.prod_id=b.id and b.id=c.prod_id and a.id=c.trans_id and a.receive_basis=$receive_basis and b.color='".$hidden_color_id."' and a.body_part_id='".$cbo_body_part_id."' and b.dia_width='".$hidden_dia_width."' and b.detarmination_id='".$fabric_desc_id."' and a.status_active=1 and c.entry_form=17 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.item_category=3 and a.transaction_type=1");
				}
				/*$prev_recv_qnty=sql_select("select a.id, c.po_breakdown_id,c.quantity as qnty from inv_transaction a, product_details_master b,order_wise_pro_details c where a.prod_id=b.id and b.id=c.prod_id and a.id=c.trans_id and a.receive_basis=$receive_basis and b.color='".$hidden_color_id."' and a.body_part_id='".$cbo_body_part_id."' and a.status_active=1 and c.entry_form=17 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.item_category=3 and a.transaction_type=1");
				$prev_recv_qnty=sql_select("SELECT a.id, c.po_breakdown_id,c.quantity as qnty 
				from inv_transaction a, product_details_master b,order_wise_pro_details c, inv_receive_master d 
				where a.prod_id=b.id and b.id=c.prod_id and a.id=c.trans_id and a.mst_id=d.id and a.receive_basis=$receive_basis and d.receive_basis=$receive_basis and b.color='".$hidden_color_id."' and a.body_part_id='".$cbo_body_part_id."' and d.booking_id='$hidden_pi_id' and a.status_active=1 and c.entry_form=17 and d.entry_form=17 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.item_category=3 and a.transaction_type=1 and d.is_deleted=0 and d.status_active=1");*/
				foreach ($prev_recv_qnty as $row) 
				{
					$prev_recv_qnty_arr[$row[csf('po_breakdown_id')]]['qnty']+=$row[csf('qnty')];
					if($update_hdn_transaction_id != "" && $update_hdn_transaction_id == $row[csf('id')])
					{
						$thisChallanRcvQntyArray[$row[csf('po_breakdown_id')]] +=$row[csf('qnty')];
					}
				}

				//$nameArray=sql_select("SELECT d.id,d.job_no_mst, d.po_number, d.po_quantity as po_qnty_in_pcs, d.pub_shipment_date from com_pi_item_details a, wo_booking_dtls b, wo_po_break_down d where a.pi_id='$hidden_pi_id' and a.work_order_no=b.booking_no and b.po_break_down_id=d.id group by d.id,d.job_no_mst, d.po_number, d.po_quantity, d.pub_shipment_date ");
			

				if ($txt_fabric_ref) {$txt_fabric_ref_cond="and f.fabric_ref='$txt_fabric_ref'";}
				if ($txt_rd_no) {$txt_rd_no_cond="and f.rd_no='$txt_rd_no'";}
				if ($cbo_weight_type) {$cbo_weight_type_cond="and c.gsm_weight_type='$cbo_weight_type'";}
				if ($txt_cutable_width) {$txt_cutable_width_cond="and f.cutable_width='$txt_cutable_width'";}


				$nameArray=sql_select("SELECT d.id,d.job_no_mst, d.po_number, d.po_quantity as po_qnty_in_pcs, d.pub_shipment_date, e.style_ref_no,a.rate 
				from com_pi_item_details a, wo_booking_dtls b, wo_po_break_down d, wo_pre_cost_fabric_cost_dtls c, wo_po_details_master e,lib_yarn_count_determina_mst f
				where a.pi_id='$hidden_pi_id' and a.work_order_no=b.booking_no and b.po_break_down_id=d.id and b.po_break_down_id=d.id and b.pre_cost_fabric_cost_dtls_id=c.id and e.job_no=d.job_no_mst and c.lib_yarn_count_deter_id=f.id and c.lib_yarn_count_deter_id=$fabric_desc_id and a.color_id in($hidden_color_id) and b.fabric_color_id in($hidden_color_id) and c.body_part_id=$cbo_body_part_id and a.rate='$txt_rate' $txt_fabric_ref_cond $txt_rd_no_cond $cbo_weight_type_cond $txt_cutable_width_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0  and c.status_active=1 and c.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.work_order_id=$hdn_booking_id
				group by d.id,d.job_no_mst, d.po_number, d.po_quantity, d.pub_shipment_date, e.style_ref_no,a.rate");

				$poIDS="";
				foreach ($nameArray as $row) {
					$poIDS.=$row[csf('id')].",";
				}

				$poIDS=chop($poIDS,",");

				$finish_req_qnty_sql=sql_select("SELECT d.id,d.job_no_mst, d.po_number, sum(b.fin_fab_qnty) as  fin_fab_qnty 
				 from  wo_booking_dtls b, wo_po_break_down d ,wo_pre_cost_fabric_cost_dtls c
				 where   b.po_break_down_id=d.id and b.pre_cost_fabric_cost_dtls_id=c.id and d.id in($poIDS) and b.fabric_color_id in($hidden_color_id) and c.body_part_id=$cbo_body_part_id and  c.lib_yarn_count_deter_id=$fabric_desc_id and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0
				 group by d.id,d.job_no_mst, d.po_number"); 

				foreach ($finish_req_qnty_sql as $row) {
					$finish_req_qnty_arr[$row[csf('id')]]["finish_reqt_qnty"]=$row[csf('fin_fab_qnty')];
				}

				if($nameArray[0] !="")
				{
					?>
					<div style="width:<? echo $width; ?>px; margin-top:10px" align="center">
						<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="430" align="center">
	                    <? //echo $hidden_pi_id; ?>
							<thead>
								<th>Total Receive Qnty</th>
								<th>Distribution Method</th>
							</thead>
							<tr class="general">
								<td><input type="text" name="txt_prop_grey_qnty" id="txt_prop_grey_qnty" class="text_boxes_numeric" value="<? echo $txt_receive_qnty; ?>" style="width:120px" onBlur="distribute_qnty(document.getElementById('cbo_distribiution_method').value)" <? echo $disabled; ?>></td>
								<td>
									<?
										$distribiution_method=array(1=>"Proportionately",2=>"Manually");
										echo create_drop_down( "cbo_distribiution_method", 160, $distribiution_method,"",0, "",$prev_distribution_method, "distribute_qnty(this.value);",$disable_drop_down );
									?>
								</td>
							</tr>
						</table>
					</div>
					<div style="margin-left:10px; margin-top:10px">
						<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="<? echo $width-20; ?>">
							<thead>
								<th width="130">Job No</th>
								<th width="130">Style Ref.</th>
								<th width="130">PO No</th>
	                            <th width="100">PO Qnty</th>
	                            <th width="100">Req. Qty.</th>
	                            <th width="100">Cum. Receive Qty.</th>
	                            <?
	                            if($roll_maintained!=1)
	                            {
	                            ?>                          
	                            	<th width="40">Roll</th>
	                            <?
	                            }
	                            ?>                          
	                            <th width="100">Shipment Date</th>
	                            <th width="50">Rate</th>
	                            <th width="100">Receive Qnty</th>
	                            <th>Balance</th>
	                            <?
	                            if($roll_maintained==1)
	                            {
	                            ?>
	                                <th width="80">Roll</th>
	                                <th width="100">Barcode No.</th>

	                            <?
	                            }
	                            ?>
							</thead>
						
							<tbody id="tbl_list_search" >
								<?
								$i=1; $tot_po_qnty=0; $tot_req_qnty=0; $po_array=array();


							//$nameArray=sql_select($po_sql);
							foreach($nameArray as $row)
							{
								//echo $i;
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								$orginal_val=1;
								$roll_id=0;

								$tot_po_qnty+=$row[csf('po_qnty_in_pcs')];
								$prevRecQnty=$prev_recv_qnty_arr[$row[csf('id')]]['qnty'];
								$requiredQnty_finish=$finish_req_qnty_arr[$row[csf('id')]]["finish_reqt_qnty"];
								$balance_req_qnty=$requiredQnty_finish-$prevRecQnty;

								$availabeQntyWithOverRcv=(($requiredQnty_finish*$over_receive_limit)/100)+$requiredQnty_finish;

							 	?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
									<td ><p><? echo $row[csf('job_no_mst')]; ?></p></td>
									<td ><p><? echo $row[csf('style_ref_no')]; ?></p></td>
									<td >
										<p><? echo $row[csf('po_number')]; ?></p>
										<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
										<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="<? echo $orginal_val; ?>">
										<input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="<? echo $roll_id; ?>">
									</td>
									<td  align="right">
										<? echo $row[csf('po_qnty_in_pcs')]; ?>
										<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $row[csf('po_qnty_in_pcs')]; ?>">
									</td>
									<td  align="right" title="Over Receive =<? echo $over_receive_limit;?>% , Total Required qnty with over receive = <? echo $availabeQntyWithOverRcv; ?> , Previous Receive qnty = <? echo $prevRecQnty; ?> Total balance is = <? echo $availabeQntyWithOverRcv-$prevRecQnty;?>  ">
										<? 

										echo number_format($requiredQnty_finish,2,".",""); ?>
									</td>
									<td  align="right">
										<? echo number_format($prevRecQnty,2,".",""); ?>
									</td>
									<?
									if($roll_maintained!=1)
									{
									?>
										<td>
		                					<input type="text" name="txtRollNo[]" id="txtRollNo_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px" value="<? //echo $grey_qnty; ?>">
		                				</td>
		                			<?
									}
									?>	
	                                <td  align="center"><? echo change_date_format($po_data[0][csf('pub_shipment_date')]); ?></td>
	                                <td  align="center"><? echo $row[csf('rate')]; ?></td>
									<td  align="center">
										<input type="text" name="txtGreyQnty[]" id="txtGreyQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="" >

										<input type="hidden" name="thisChallanRcvQnty[]" id="thisChallanRcvQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $thisChallanRcvQntyArray[$row[csf('id')]]; ?>" <? echo $disable; ?>>

										<input type="hidden" name="txtRecQntyPrev[]" id="txtRecQntyPrev_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $prevRecQnty; ?>" <? echo $disable; ?>>

										<input type="hidden" name="availabeQntyWithOverRcv[]" id="availabeQntyWithOverRcv_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $availabeQntyWithOverRcv; ?>" <? echo $disable; ?>>

									</td>
									<td align="center" title="<? echo $balance_req_qnty.'='.$requiredQnty_finish.'-'.$prevRecQnty; ?>">
										<input type="text" name="txtBalanceQnty[]" id="txtBalanceQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo number_format($balance_req_qnty,2); ?>" readonly>
									</td>
									<?
									if($roll_maintained==1)
									{
										?>
										<td  align="center">
											<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px" value="" placeholder="<? echo $roll_arr[$order_id]+1; ?>" onBlur="roll_duplication_check(<? echo $i; ?>);" />
										</td>
										<td  align="center"><input type="text" name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="" disabled/></td>

										<?
									}
									?>
								</tr>
								<?
								$i++;

								}
								?>
							</tbody>
								<input type="hidden" name="tot_po_qnty" id="tot_po_qnty" class="text_boxes" value="<? echo $tot_po_qnty; ?>">
								<input type="hidden" name="tot_req_qnty" id="tot_req_qnty" class="text_boxes" value="<? echo $tot_req_qnty; ?>">
	                            <input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="<? echo $i-1; ?>">
							</table>
						</div>
						<table width="<? echo $width; ?>">
							 <tr>
								<td align="center" >
									<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
								</td>
							</tr>
						</table>
					</div>
					<?
				}
			}
			// 7-1-2017 end
			?>

	   		<?
			if($save_data!="")
			{
				?>
				<div style="width:<? echo $width; ?>px; margin-top:10px" align="center">
					<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="300" align="center">
						<thead>
							<th>Total Receive Qnty</th>
							<th>Distribution Method </th>
						</thead>
						<tr class="general">
							<td><input type="text" name="txt_prop_grey_qnty" id="txt_prop_grey_qnty" class="text_boxes_numeric" value="<? echo $txt_receive_qnty; ?>" style="width:120px" onBlur="distribute_qnty(document.getElementById('cbo_distribiution_method').value)" <? echo $disabled; ?>></td>
							<td>
								<?
									$distribiution_method=array(1=>"Proportionately",2=>"Manually");
									echo create_drop_down( "cbo_distribiution_method", 160, $distribiution_method,"",0, "",$prev_distribution_method, "distribute_qnty(this.value);",$disable_drop_down );
								?>
							</td>
						</tr>
					</table>
				</div>
				<div style="margin-left:10px; margin-top:10px">
					<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="<? echo $width+300-20; ?>">
						<thead>
							<th width="130">Job No</th>
							<th width="130">Style Ref.</th>
							<th width="130">PO No</th>
	                        <th width="100">PO Qnty</th>
	                        <th width="100">Req. Qnty</th>
	                        <th width="100">Cum. Receive Qty.</th>
	                        <?
	                        if($roll_maintained!=1)
	                        {
	                        ?>
	                        	<th width="40">Roll</th>
	                        <?
	                        }
	                        ?>
	                        <th width="100">Ship Date</th>
	                        <th width="100">Receive Qnty</th>
	                        <th <? if($roll_maintained==1){ echo "width='100'";} ?>>Balance</th>
	                        <?
	                        if($roll_maintained==1)
	                        {
	                        ?>
	                            <th width="80">Roll</th>
	                            <th width="100">Barcode No.</th>
	                            <th width="65"></th>
	                        <?
	                        }
	                        ?>
						</thead>
					
					
						<tbody id="tbl_list_search">
							<?
							$i=1; $tot_po_qnty=0; $tot_req_qnty=0; $po_array=array();

							$prev_recv_qnty=sql_select("SELECT a.id, c.po_breakdown_id,c.quantity as qnty 
							from inv_transaction a, product_details_master b,order_wise_pro_details c, inv_receive_master d  
							where a.prod_id=b.id and b.id=c.prod_id and a.id=c.trans_id and a.mst_id=d.id and a.receive_basis=$receive_basis and d.receive_basis=$receive_basis and b.color='".$hidden_color_id."' and b.detarmination_id='".$fabric_desc_id."' and d.booking_id='".$hidden_pi_id."' and a.body_part_id='".$cbo_body_part_id."' and b.dia_width='".$hidden_dia_width."' and a.status_active=1 and c.entry_form=17 and d.entry_form=17 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 and a.item_category=3 and a.transaction_type=1");
							foreach ($prev_recv_qnty as $row) 
							{
								$prev_recv_qnty_arr[$row[csf('po_breakdown_id')]]['qnty']+=$row[csf('qnty')];

								if($update_hdn_transaction_id != "" && $update_hdn_transaction_id == $row[csf('id')])
								{
									$thisChallanRcvQntyArray[$row[csf('po_breakdown_id')]] +=$row[csf('qnty')];
								}
							}

							$explSaveData = explode(",",$save_data);
							for($z=0;$z<count($explSaveData);$z++)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								$po_wise_data = explode("**",$explSaveData[$z]);
								$order_id=$po_wise_data[0];
								$fin_qty=$po_wise_data[1];
								$roll_no=$po_wise_data[2];
								$roll_id=$po_wise_data[3];
								$barcode_no=$po_wise_data[4];
								$rollNo=$po_wise_data[5];

								$po_data=sql_select("SELECT b.id,a.job_no,a.style_ref_no,b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date 
								from wo_po_details_master a, wo_po_break_down b 
								where a.job_no=b.job_no_mst and b.id=$order_id");//old
							
								$finish_req_qnty_sql=sql_select("SELECT d.id,d.job_no_mst, d.po_number, sum(b.fin_fab_qnty) as  fin_fab_qnty 
								 from  wo_booking_dtls b, wo_po_break_down d ,wo_pre_cost_fabric_cost_dtls c
								 where   b.po_break_down_id=d.id and b.pre_cost_fabric_cost_dtls_id=c.id and d.id in($order_id) and b.fabric_color_id in($hidden_color_id) and c.body_part_id=$cbo_body_part_id and  c.lib_yarn_count_deter_id=$fabric_desc_id and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by d.id,d.job_no_mst, d.po_number"); 
								foreach ($finish_req_qnty_sql as $row) 
								{
									$finish_req_qnty_arr[$row[csf('id')]]["finish_reqt_qnty"]=$row[csf('fin_fab_qnty')];
								}

								if($roll_maintained==1)
								{
									if(!(in_array($order_id,$po_array)))
									{
										$tot_po_qnty+=$po_data[0][csf('po_qnty_in_pcs')];
										$orginal_val=1;
										$po_array[]=$order_id;
									}
									else
									{
										$orginal_val=0;
									}
								}
								else
								{
									if(!(in_array($order_id,$po_array)))
									{
										$tot_po_qnty+=$po_data[0][csf('po_qnty_in_pcs')];
										$orginal_val=1;
										$po_array[]=$order_id;
									}
									else
									{
										$orginal_val=0;
									}

									$roll_id=0;
								}

								$prevRecQnty=$prev_recv_qnty_arr[$order_id]['qnty'];
								$requiredQnty_finish= $finish_req_qnty_arr[$row[csf('id')]]["finish_reqt_qnty"];
								$balance_req_qnty=$requiredQnty_finish-$prev_recv_qnty_arr[$order_id]['qnty'];
								$availabeQntyWithOverRcv=(($requiredQnty_finish*$over_receive_limit)/100)+$requiredQnty_finish;
								/*echo "<pre>";
								print_r($po_data);*/
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
									<td><p><? echo $po_data[0][csf('job_no')]; ?></p></td>
									<td><p><? echo $po_data[0]['STYLE_REF_NO']; ?></p></td>
									<td >
										<p><? echo $po_data[0][csf('po_number')]; ?></p>
										<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $order_id; ?>">
										<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="<? echo $orginal_val; ?>">
										<input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="<? echo $roll_id; ?>">
									</td>
									<td  align="right">
										<? echo $po_data[0][csf('po_qnty_in_pcs')] ?>
										<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $po_data[0][csf('po_qnty_in_pcs')]; ?>">
									</td>
									<td  align="right" title="Over Receive =<? echo $over_receive_limit;?>% , Total Required qnty with over receive = <? echo $availabeQntyWithOverRcv; ?> , Previous Receive qnty = <? echo $prevRecQnty; ?> Total balance is = <? echo $availabeQntyWithOverRcv-$prevRecQnty;?>  ">
										<? echo number_format($requiredQnty_finish,2); ?>
									</td>
									<td  align="right">
										<? echo number_format($prevRecQnty,2); ?>
									</td>
									<?
									if($roll_maintained!=1)
									{
									?>
										<td>
		                        			<input type="text" name="txtRollNo[]" id="txtRollNo_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px" value="<? echo $rollNo; ?>">
		                        		</td>
	                        		<?
			                        }
			                        ?>
	                                <td  align="center"><? echo change_date_format($po_data[0][csf('pub_shipment_date')]); ?></td>
									<td   align="center">
										<input type="text" name="txtGreyQnty[]" id="txtGreyQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo number_format($fin_qty,2); ?>">

										<input type="hidden" name="thisChallanRcvQnty[]" id="thisChallanRcvQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $thisChallanRcvQntyArray[$row[csf('id')]]; ?>" <? echo $disable; ?>>
												
										<input type="hidden" name="txtRecQntyPrev[]" id="txtRecQntyPrev_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $prevRecQnty; ?>" <? echo $disable; ?>>

										<input type="hidden" name="availabeQntyWithOverRcv[]" id="availabeQntyWithOverRcv_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $availabeQntyWithOverRcv; ?>" <? echo $disable; ?>>

									</td>
									<td  align="center" title="<? echo $balance_req_qnty.'='.$requiredQnty_finish.'-'.$prev_recv_qnty_arr[$order_id]['qnty']; ?>">
										<input type="text" name="txtBalanceQnty[]" id="txtBalanceQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo number_format($balance_req_qnty,2); ?>" readonly>
									</td>
									<?
									if($roll_maintained==1)
									{
										?>
										<td  align="center">
											<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px" value="<? if($roll_no!=0) echo $roll_no; ?>" placeholder="<? echo $roll_arr[$order_id]+1; ?>" onBlur="roll_duplication_check(<? echo $i; ?>);" />
										</td>
										<td  align="center"><input type="text" name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $barcode_no; ?>" disabled/></td>
										<td >
											<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
											<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
										</td>
										<?
									}
									?>
								</tr>
								<?
								$i++;
							}
							?>
							<input type="hidden" name="tot_po_qnty" id="tot_po_qnty" class="text_boxes" value="<? echo $tot_po_qnty; ?>">
							<input type="hidden" name="tot_req_qnty" id="tot_req_qnty" class="text_boxes" value="<? echo $tot_req_qnty; ?>">
	                        <input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="<? echo $i-1; ?>">
	                    </tbody>
						</table>
					</div>
					<table width="<? echo $width; ?>">
						<tr>
							<td align="center" >
								<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
							</td>
						</tr>
					</table>
				</div>
				<?
			}

			?>
	    	</div>
			<?
		}
		else
		{
			//echo 'wo po booking..........................................';
			?>
			<div style="width:<? echo $width; ?>px; margin-top:10px" align="center">
				<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="350" align="center">
					<thead>
						<th>Total Receive Qnty</th>
						<th>Distribution Method</th>
					</thead>
					<tr class="general">
						<td><input type="text" name="txt_prop_grey_qnty" id="txt_prop_grey_qnty" class="text_boxes_numeric" value="<? echo $txt_receive_qnty; ?>" style="width:120px" onBlur="distribute_qnty(document.getElementById('cbo_distribiution_method').value)" <? echo $disabled; ?>></td>
						<td>
							<?
								$distribiution_method=array(1=>"Proportionately",2=>"Manually");
								echo create_drop_down( "cbo_distribiution_method", 160, $distribiution_method,"",0, "",$prev_distribution_method, "distribute_qnty(this.value);", $disable_drop_down );
							?>
						</td>
					</tr>
				</table>
			</div>
			<div style="margin-left:5px; margin-top:5px">
				<table style="float: left;" class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="<? echo $width-20; ?>">
					<thead>
						<th width="130">Job No</th>
						<th width="130">Style Ref.</th>
						<th width="130">PO No</th>
						<th width="100">PO Qnty</th>
						<th width="100">Req. Qnty</th>
						<th width="100">Cum. Receive Qnty</th>
						<?
						if($roll_maintained!=1)
						{
						?>
							<th width="40">Roll</th>
						<?
	                    }
	                    ?>
	                    <th width="100">Shipment Date</th>
	                    <th width="50">Rate</th>
						<th width="100">Receive Qnty</th>
						<th>Balance Qnty</th>
	                    <?
						if($roll_maintained==1)
						{
						?>
	                        <th width="80">Roll</th>
	                        <th width="100">Barcode No.</th>
	                        <th width="65"></th>
						<?
	                    }
	                    ?>
					</thead>
				
					<tbody id="tbl_list_search">
						<?
						$i=1; $tot_po_qnty=0; $tot_req_qnty=0; $po_array=array();
						if($save_data!="" && ($receive_basis==4 || $receive_basis==6 || $receive_basis==1))
						{
							$po_id = explode(",",$po_id);
							//echo $save_data;
							$explSaveData = explode(",",$save_data);
							for($z=0;$z<count($explSaveData);$z++)
							{
								if ($i%2==0)
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";

								$po_wise_data = explode("**",$explSaveData[$z]);
								echo $order_id=$po_wise_data[0];
								$fin_qty=$po_wise_data[1];
								$roll_no=$po_wise_data[2];
								$roll_id=$po_wise_data[3];
								$barcode_no=$po_wise_data[4];

								if(in_array($order_id,$po_id))
								{
									$po_data=sql_select("SELECT a.job_no,a.style_ref_no, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id=$order_id");
									//$sql_cuml="select po_breakdown_id, sum(quantity) as qnty from order_wise_pro_details where po_breakdown_id in ($batch_po_id) and entry_form=37 and status_active=1 and is_deleted=0 group by po_breakdown_id";
									if($roll_maintained==1)
									{
										if(!(in_array($order_id,$po_array)))
										{
											$tot_po_qnty+=$po_data[0][csf('po_qnty_in_pcs')];
											$orginal_val=1;
											$po_array[]=$order_id;
										}
										else
										{
											$orginal_val=0;
										}
									}
									else
									{
										if(!(in_array($order_id,$po_array)))
										{
											$tot_po_qnty+=$po_data[0][csf('po_qnty_in_pcs')];
											$orginal_val=1;
											$po_array[]=$order_id;
										}
										else
										{
											$orginal_val=0;
										}

										$roll_id=0;
									}
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
										<td ><p><? echo $po_data[0][csf('job_no')]; ?></p></td>
										<td ><p><? echo $po_data[0][csf('style_ref_no')]; ?></p></td>
										<td >
											<p><? echo $po_data[0][csf('po_number')]; ?></p>
											<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $order_id; ?>">
											<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="<? echo $orginal_val; ?>">
											<input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="<? echo $roll_id; ?>">
										</td>
										<td  align="right">
											<? echo $po_data[0][csf('po_qnty_in_pcs')] ?>
											<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $po_data[0][csf('po_qnty_in_pcs')]; ?>">
										</td>
										<?
										if($roll_maintained!=1)
										{
										?>
											<td>
		                            			<input type="text" name="txtRollNo[]" id="txtRollNo_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px" value="<? //echo $grey_qnty; ?>">
		                            		</td>
		                            	<?
										}
										?>
	                                    <td  align="center"><? echo change_date_format($po_data[0][csf('pub_shipment_date')]); ?></td>
	                                    <td  align="center"><? echo ""; ?></td>
										<td align="center">
											<input type="text" name="txtGreyQnty[]" id="txtGreyQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $fin_qty; ?>">
										</td>
										<?
										if($roll_maintained==1)
										{
										?>
											<td  align="center">
												<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px" value="<? if($roll_no!=0) echo $roll_no; ?>" placeholder="<? echo $roll_arr[$order_id]+1; ?>" onBlur="roll_duplication_check(<? echo $i; ?>);" />
											</td>
											<td  align="center"><input type="text" name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $barcode_no; ?>" disabled/></td>
											<td >
												<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
												<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
											</td>
										<?
										}
										?>
									</tr>
								<?
								$i++;
								}
							}

							$result=implode(",",array_diff($po_id, $po_array));
							if($result!="")
							{
								$po_sql="SELECT a.job_no,a.style_ref_no,b.id, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in ($result)";
							  //$po_data=sql_select("select b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id=$order_id");
								$nameArray=sql_select($po_sql);
								foreach($nameArray as $row)
								{
									if ($i%2==0)
										$bgcolor="#E9F3FF";
									else
										$bgcolor="#FFFFFF";

									$orginal_val=1;
									$roll_id=0;

									$tot_po_qnty+=$row[csf('po_qnty_in_pcs')];

								 ?>
									<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
										<td ><p><? echo $row[csf('job_no')]; ?></p></td>
										<td ><p><? echo $row[csf('style_ref_no')]; ?></p></td>
										<td >
											<p><? echo $row[csf('po_number')]; ?></p>
											<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
											<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="<? echo $orginal_val; ?>">
											<input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="<? echo $roll_id; ?>">
										</td>
										<td  align="right">
											<? echo $row[csf('po_qnty_in_pcs')]; ?>
											<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $row[csf('po_qnty_in_pcs')]; ?>">
										</td>
										<?
										if($roll_maintained!=1)
										{
										?>
											<td>
		                            			<input type="text" name="txtRollNo[]" id="txtRollNo_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px" value="<? //echo $grey_qnty; ?>">
		                            		</td>
	                            		<?
										}
										?>
	                                    <td  align="center"><? echo change_date_format($po_data[0][csf('pub_shipment_date')]); ?></td>
	                                    <td  align="center"><? echo ""; ?></td>
										<td align="center">
											<input type="text" name="txtGreyQnty[]" id="txtGreyQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="" >
										</td>
										<?
										if($roll_maintained==1)
										{
										?>
											<td  align="center">
												<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px" value="" placeholder="<? echo $roll_arr[$order_id]+1; ?>" onBlur="roll_duplication_check(<? echo $i; ?>);" />
											</td>
											<td  align="center"><input type="text" name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="" disabled/></td>
											<td >
												<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
												<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
											</td>
										<?
										}
										?>
									</tr>
								<?
								$i++;
								}
							}
						}
						else if($save_data!="" && $receive_basis==2)
						{

							if($roll_maintained==1)
							{
								$po_sql="SELECT a.job_no,a.style_ref_no,b.id, b.po_number, a.total_set_qnty, b.po_quantity, b.pub_shipment_date,c.rate from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.booking_no='$booking_no' and c.rate='$txt_rate' group by a.job_no,a.style_ref_no,b.id, b.pub_shipment_date, b.po_number,a.total_set_qnty,b.po_quantity";
								$nameArray=sql_select($po_sql);
								foreach($nameArray as $row)
								{
									$po_data_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
									$po_data_array[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
									$po_data_array[$row[csf('id')]]['po_no']=$row[csf('po_number')];
									$po_data_array[$row[csf('id')]]['qty']=$row[csf('total_set_qnty')]*$row[csf('po_quantity')];
									$po_data_array[$row[csf('id')]]['date']=$row[csf('pub_shipment_date')];
								}

								$po_id = explode(",",$po_id);
								$explSaveData = explode(",",$save_data);
								foreach($explSaveData as $val)
								{
									$order_data = explode("**",$val);
									$order_id=$order_data[0];
									$fin_qty=$order_data[1];
									$roll_no=$order_data[2];
									$roll_id=$order_data[3];
									$barcode_no=$order_data[4];

									if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

									if(!(in_array($order_id,$po_array)))
									{
										$tot_po_qnty+=$po_data_array[$order_id]['qty'];
										$orginal_val=1;
										$po_array[]=$order_id;
									}
									else
									{
										$orginal_val=0;
									}

									?>
									<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
									<td ><p><? echo $po_data_array[$order_id]['job_no']; ?></p></td>
									<td ><p><? echo $po_data_array[$order_id]['style_ref_no']; ?></p></td>
										<td >
											<p><? echo $po_data_array[$order_id]['po_no']; ?></p>
											<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $order_id; ?>">
											<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="<? echo $orginal_val; ?>">
											<input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="<? echo $roll_id; ?>">
										</td>
	                                    <td  align="right">
											<? echo $po_data_array[$order_id]['qty']; ?>
											<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $po_data_array[$order_id]['qty']; ?>">
										</td>
										<td  align="center"><? echo change_date_format($po_data_array[$order_id]['date']); ?></td>
										<td  align="center"><? echo ""; ?></td>
										<td align="center">
											<input type="text" name="txtfinishQnty[]" id="txtfinishQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $fin_qty; ?>"/>
										</td>
										<td  align="center">
											<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px" value="<? if($roll_no!=0) echo $roll_no; ?>" placeholder="<? echo $roll_arr[$order_id]+1; ?>" onBlur="roll_duplication_check(<? echo $i; ?>);" />
										</td>
										<td  align="center"><input type="text" name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $barcode_no; ?>" disabled/></td>
										<td >
											<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
											<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
										</td>
									</tr>
								<?
								$i++;
								}
								foreach($po_data_array as $order_id=>$val)
								{
									if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									$orginal_val=1;
									if(!(in_array($order_id,$po_array)))
									{
										$tot_po_qnty+=$val['qty'];
										$orginal_val=1;
									?>
										<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
										<td ><p><? echo $po_data_array[$order_id]['job_no']; ?></p></td>
										<td ><p><? echo $po_data_array[$order_id]['style_ref_no']; ?></p></td>
											<td >
												<p><? echo $po_data_array[$order_id]['po_no']; ?></p>
												<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $order_id; ?>">
	                                            <input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="<? echo $orginal_val; ?>">
	                                            <input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="<? echo $roll_id; ?>">
											</td>
											<td  align="right">
												<? echo $val['qty']; ?>
												<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $val['qty']; ?>">
											</td>
	                                        <td  align="center"><? echo change_date_format($val['date']); ?></td>
	                                        <td  align="center"><? echo ""; ?></td>
											<td  align="right"><? echo number_format($req_qty_array[$order_id],2,'.',''); ?></td>
											<td  align="right"><? echo number_format($cumu_rec_qty[$order_id],2,'.',''); ?></td>
											<td align="center">
												<input type="text" name="txtfinishQnty[]" id="txtfinishQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value=""/>
											</td>
											<td  align="center">
												<input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px" value="" placeholder="<? echo $roll_arr[$order_id]+1; ?>" onBlur="roll_duplication_check(<? echo $i; ?>);" />
											</td>
											<td ><input type="text" name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="" disabled/></td>
											<td >
												<input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
												<input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
											</td>
										</tr>
									<?
										$i++;
									}
								}
							}
							else
							{
								$prev_po_qnty_arr=array();$prev_rollNo_arr=array();
								$explSaveData = explode(",",$save_data);
								for($z=0;$z<count($explSaveData);$z++)
								{
									$po_wise_data = explode("**",$explSaveData[$z]);
									$order_id=$po_wise_data[0];
									$grey_qnty=$po_wise_data[1];
									$rollNo=$po_wise_data[5];
									$prev_po_qnty_arr[$order_id]=$grey_qnty;
									$prev_rollNo_arr[$order_id]=$rollNo;
								}

								/*$prev_recv_qnty=sql_select("select a.id, c.po_breakdown_id,c.quantity as qnty from inv_transaction a, product_details_master b,order_wise_pro_details c where a.prod_id=b.id and b.id=c.prod_id and a.id=c.trans_id and a.receive_basis=$receive_basis and b.color='".$hidden_color_id."' and a.body_part_id='".$cbo_body_part_id."' and a.status_active=1 and c.entry_form=17 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.item_category=3 and a.transaction_type=1");*/

								$prev_recv_qnty=sql_select("SELECT c.trans_id as id, c.po_breakdown_id,c.quantity as qnty 
									from pro_finish_fabric_rcv_dtls a, product_details_master b, order_wise_pro_details c, inv_receive_master d, inv_transaction e where a.prod_id=b.id and a.id=c.dtls_id and b.id=c.prod_id and d.receive_basis=2 and d.id=a.mst_id and a.trans_id= e.id and d.id=e.mst_id and c.trans_id=e.id  and e.order_rate='".$txt_rate."' and b.color='".$hidden_color_id."' and a.body_part_id='".$cbo_body_part_id."'  and a.fabric_description_id='".$fabric_desc_id."' and d.booking_no='".$booking_no."' and a.status_active=1 and c.entry_form=17 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.item_category_id=3 and c.trans_type=1");
								foreach ($prev_recv_qnty as $row) 
								{
									$prev_recv_qnty_arr[$row[csf('po_breakdown_id')]]['qnty']+=$row[csf('qnty')];
									if($update_hdn_transaction_id != "" && $update_hdn_transaction_id == $row[csf('id')])
									{
										$thisChallanRcvQntyArray[$row[csf('po_breakdown_id')]] +=$row[csf('qnty')];
									}
								}

								$prev_recv_return_qnty=sql_select("select a.id, c.po_breakdown_id,c.quantity as qnty from inv_transaction a, product_details_master b,order_wise_pro_details c where a.prod_id=b.id and b.id=c.prod_id and a.id=c.trans_id  and b.color='".$hidden_color_id."' and a.body_part_id='".$cbo_body_part_id."' and a.status_active=1 and c.entry_form=202 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.item_category=3 and a.transaction_type=3");
								foreach ($prev_recv_return_qnty as $row) 
								{
									$prev_recv_return_qnty_arr[$row[csf('po_breakdown_id')]]['qnty']+=$row[csf('qnty')];

									/*if($update_hdn_transaction_id != "" && $update_hdn_transaction_id == $row[csf('id')])
									{
										$thisChallanRcvQntyArray[$row[csf('po_breakdown_id')]] +=$row[csf('qnty')];
									}*/
								}



								$po_sql="SELECT a.job_no,a.style_ref_no,b.id, b.po_number,c.fabric_color_id, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date,c.rate from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c, wo_pre_cost_fabric_cost_dtls d where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.pre_cost_fabric_cost_dtls_id=d.id and c.booking_no='$booking_no' and c.fabric_color_id=$hidden_color_id and c.rate='$txt_rate' and d.body_part_id=$cbo_body_part_id and d.lib_yarn_count_deter_id=$fabric_desc_id and b.id in($all_po_id)  group by a.job_no,a.style_ref_no,b.id, b.pub_shipment_date, b.po_number, a.total_set_qnty, b.po_quantity,c.fabric_color_id,c.rate";
								$nameArray=sql_select($po_sql);
								$req_qty_array=array();
								$reqQnty = "SELECT a.po_break_down_id,a.fabric_color_id as color_id, sum(a.grey_fab_qnty) as grey_fab_qnty,a.rate from wo_booking_dtls a,wo_pre_cost_fabric_cost_dtls b where  a.pre_cost_fabric_cost_dtls_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.booking_no='$booking_no' and a.booking_no='$booking_no' and b.lib_yarn_count_deter_id=$fabric_desc_id and a.rate='$txt_rate'  group by a.po_break_down_id,a.fabric_color_id,a.rate"; //and booking_no='$batch_booking'
								// echo $reqQnty;
								$reqQnty_res = sql_select($reqQnty);
								foreach($reqQnty_res as $req_val)
								{
									$req_qty_array[$req_val[csf('po_break_down_id')]][$req_val[csf('color_id')]]=$req_val[csf('grey_fab_qnty')];
								}
								foreach($nameArray as $row)
								{
									if ($i%2==0)
										$bgcolor="#E9F3FF";
									else
										$bgcolor="#FFFFFF";

									$orginal_val=1;
									$roll_id=0;
									$disable="";
									$tot_po_qnty+=$row[csf('po_qnty_in_pcs')];
									$prevRecReturnQnty=$prev_recv_return_qnty_arr[$row[csf('id')]]['qnty'];
									$tot_req_qnty+=$req_qty_array[$row[csf('id')]][$row[csf('fabric_color_id')]];
									$grey_qnty=$prev_po_qnty_arr[$row[csf('id')]];
									$rollNo=$prev_rollNo_arr[$row[csf('id')]];
									$requiredQnty=$req_qty_array[$row[csf('id')]][$row[csf('fabric_color_id')]];
									$prevRecQnty=$prev_recv_qnty_arr[$row[csf('id')]]['qnty']-$prevRecReturnQnty;
									$availabeQntyWithOverRcv=(($requiredQnty*$over_receive_limit)/100)+$requiredQnty;
									$balance_req_qnty=$req_qty_array[$row[csf('id')]][$row[csf('fabric_color_id')]]-$prevRecQnty;

								 ?>
									<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
										<td><p><? echo $row[csf('job_no')]; ?></p></td>
										<td><p><? echo $row[csf('style_ref_no')]; ?></p></td>
										<td >
											<p><? echo $row[csf('po_number')]; ?></p>
											<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
											<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="<? echo $orginal_val; ?>">
											<input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="<? echo $roll_id; ?>">
										</td>
										<td  align="right">
											<? echo $row[csf('po_qnty_in_pcs')]; ?>
											<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $row[csf('po_qnty_in_pcs')]; ?>">
										</td>
										 <td  align="right" title="Over Receive =<? echo $over_receive_limit;?>% , Total Required qnty with over receive = <? echo $availabeQntyWithOverRcv; ?> , Previous Receive qnty = <? echo $prevRecQnty; ?> Total balance is = <? echo $availabeQntyWithOverRcv-$prevRecQnty;?>  ">
											<? echo number_format($req_qty_array[$row[csf('id')]][$row[csf('fabric_color_id')]],2,'.',''); ?>
											<input type="hidden" name="txtReqQnty[]" id="txtReqQnty_<? echo $i; ?>" value="<? echo $req_qty_array[$row[csf('id')]][$row[csf('fabric_color_id')]]; ?>">
	                            		</td>
	                            		<td  align="center"><? echo number_format($prevRecQnty,2,'.',''); ?></td>
	                            		<td>
	                            			<input type="text" name="txtRollNo[]" id="txtRollNo_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px" value="<? echo $rollNo; ?>">
	                            		</td>
	                            		<td  align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
	                            		<td  align="center"><? echo $row[csf('rate')]; ?></td>
										<td  align="center">
											<input type="text" name="txtGreyQnty[]" id="txtGreyQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $grey_qnty; ?>" <? echo $disable; ?>>
											<input type="hidden" name="thisChallanRcvQnty[]" id="thisChallanRcvQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $thisChallanRcvQntyArray[$row[csf('id')]]; ?>" <? echo $disable; ?>>

											<input type="hidden" name="txtRecQntyPrev[]" id="txtRecQntyPrev_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $prevRecQnty; ?>" <? echo $disable; ?>>
											<input type="hidden" name="availabeQntyWithOverRcv[]" id="availabeQntyWithOverRcv_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $availabeQntyWithOverRcv; ?>" <? echo $disable; ?>>
										<td align="center">
											<input type="text" name="txtBalanceQnty[]" id="txtBalanceQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo number_format($balance_req_qnty,2,'.',''); ?>" readonly>
										</td>
										</td>
									</tr>
								<?
								$i++;
								}
							}
						}
						else
						{
							//echo $update_hdn_transaction_id;die;
							//and b.color='".$hidden_color_id."' and a.body_part_id='".$cbo_body_part_id."'
							/*$prev_recv_qnty=sql_select("select a.id, c.po_breakdown_id,c.quantity as qnty from inv_transaction a, product_details_master b,order_wise_pro_details c where a.prod_id=b.id and b.id=c.prod_id and a.id=c.trans_id and a.receive_basis=$receive_basis  and a.status_active=1 and c.entry_form=17 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.item_category=3 and a.transaction_type=1 and b.color='".$hidden_color_id."' and a.body_part_id='".$cbo_body_part_id."'");*/

							  
							if($txt_fabric_ref){$txt_fabric_ref_cond="and e.fabric_ref='$txt_fabric_ref'";}
							if($txt_rd_no){$txt_rd_no_cond="and e.rd_no='$txt_rd_no'";}
							if($cbo_weight_type){$cbo_weight_type_cond="and d.gsm_weight_type='$cbo_weight_type'";}
							if($txt_cutable_width){$txt_cutable_width_cond="and e.cutable_width='$txt_cutable_width'";}


							if($booking_no!="" && $receive_basis==2){$bookingNo_cond="and d.booking_no='$booking_no'";}
							
							$prev_recv_qnty=sql_select("SELECT c.trans_id as id, c.po_breakdown_id,c.quantity as qnty
							from pro_finish_fabric_rcv_dtls a, product_details_master b, order_wise_pro_details c, inv_receive_master d
							where a.prod_id=b.id and a.id=c.dtls_id and b.id=c.prod_id and d.receive_basis=2 and d.id=a.mst_id and b.color='".$hidden_color_id."' and a.body_part_id='".$cbo_body_part_id."' $bookingNo_cond  and a.fabric_description_id='".$fabric_desc_id."' and b.weight='".$hidden_gsm_weight."' and b.dia_width='".$hidden_dia_width."' and a.status_active=1 and c.entry_form=17 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.item_category_id=3 and c.trans_type=1");
							foreach ($prev_recv_qnty as $row)
							{
								$prev_recv_qnty_arr[$row[csf('po_breakdown_id')]]['qnty']+=$row[csf('qnty')];

								if($update_hdn_transaction_id != "" && $update_hdn_transaction_id == $row[csf('id')])
								{
									$thisChallanRcvQntyArray[$row[csf('po_breakdown_id')]] +=$row[csf('qnty')];
								}
							}

							$prev_recv_return_qnty=sql_select("select a.id, c.po_breakdown_id,c.quantity as qnty
							from inv_transaction a, product_details_master b,order_wise_pro_details c 
							where a.prod_id=b.id and b.id=c.prod_id and a.id=c.trans_id  and a.status_active=1 and c.entry_form=202 and a.is_deleted=0 
							and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.item_category=3 and a.transaction_type=3 and b.color='".$hidden_color_id."' and a.body_part_id='".$cbo_body_part_id."'");
							foreach ($prev_recv_return_qnty as $row) 
							{
								$prev_recv_return_qnty_arr[$row[csf('po_breakdown_id')]]['qnty']+=$row[csf('qnty')];

								/*if($update_hdn_transaction_id != "" && $update_hdn_transaction_id == $row[csf('id')])
								{
									$thisChallanRcvQntyArray[$row[csf('po_breakdown_id')]] +=$row[csf('qnty')];
								}*/
							}

							if($type==1)
							{
								if($po_id!="")
								{
									$po_sql="SELECT a.job_no,a.style_ref_no,b.id, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in ($po_id)";
								}
							}
							else 
							{
								/*
								|
								|	09-Jun-2021 gsm weight source changed to pre cost from booking dtls   in $po_sql and $reqQnty sql
								|
								*/

								$po_sql="SELECT a.job_no,a.style_ref_no,b.id, b.po_number, (a.total_set_qnty*b.po_quantity) as po_qnty_in_pcs, b.pub_shipment_date,c.fabric_color_id , d.body_part_id ,c.uom, d.gsm_weight ,c.dia_width,c.rate,e.fabric_ref,e.rd_no,d.gsm_weight_type as weight_type,e.cutable_width from wo_po_details_master a, wo_po_break_down b, wo_booking_dtls c, wo_pre_cost_fabric_cost_dtls d,lib_yarn_count_determina_mst e  where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.pre_cost_fabric_cost_dtls_id=d.id  and d.lib_yarn_count_deter_id=e.id  and c.booking_no='$booking_no' and c.fabric_color_id=$hidden_color_id and d.body_part_id=$cbo_body_part_id and c.dia_width='$hidden_dia_width' and d.gsm_weight='$hidden_gsm_weight' $txt_fabric_ref_cond $txt_rd_no_cond $cbo_weight_type_cond $txt_cutable_width_cond and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and d.lib_yarn_count_deter_id=$fabric_desc_id and c.rate='$txt_rate' group by a.job_no,a.style_ref_no,b.id, b.pub_shipment_date, b.po_number,a.total_set_qnty,b.po_quantity,c.fabric_color_id , d.body_part_id ,c.uom, d.gsm_weight ,c.dia_width,c.rate,e.fabric_ref,e.rd_no,d.gsm_weight_type,e.cutable_width ";
							}
							$req_qty_array=array();
							if($receive_basis==2)
							{
								//$reqQnty = "SELECT po_break_down_id, sum(fin_fab_qnty) as fabric_qty from wo_booking_dtls where status_active=1 and is_deleted=0 and fabric_color_id=$hidden_color_id group by po_break_down_id and booking_no='$booking_no'";

								$reqQnty = "SELECT a.po_break_down_id, sum(a.fin_fab_qnty) as fabric_qty,b.gsm_weight,a.dia_width,a.fabric_color_id,a.rate from wo_booking_dtls  a,wo_pre_cost_fabric_cost_dtls b where  a.pre_cost_fabric_cost_dtls_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.fabric_color_id=$hidden_color_id and a.booking_no='$booking_no' and a.dia_width='$hidden_dia_width' and b.gsm_weight='$hidden_gsm_weight' and a.rate='$txt_rate' and b.body_part_id=$cbo_body_part_id and b.lib_yarn_count_deter_id=$fabric_desc_id  group by a.po_break_down_id,b.gsm_weight,a.dia_width,a.fabric_color_id,a.rate";
								// echo $reqQnty;
								$reqQnty_res = sql_select($reqQnty);
								$booking_poId="";$booking_colorId="";
								foreach($reqQnty_res as $req_val)
								{
									//$booking_poId.=$req_val[csf('po_break_down_id')].",";
									//$booking_colorId.=$req_val[csf('fabric_color_id')].",";
									$req_qty_array[$req_val[csf('po_break_down_id')]][$req_val[csf('gsm_weight')]][$req_val[csf('dia_width')]][$req_val[csf('fabric_color_id')]][$req_val[csf('rate')]]=$req_val[csf('fabric_qty')];
								}
								
								//previous recev balancing
								/*$booking_poId=chop($booking_poId,",");
								$booking_colorId=chop($booking_colorId,",");
								$prev_recv=sql_select("select b.po_breakdown_id,b.color_id,sum(b.quantity) as recv_quantity from order_wise_pro_details b where b.po_breakdown_id in($booking_poId) and b.color_id in($booking_colorId) and b.entry_form=17 group by b.po_breakdown_id,b.color_id");
								foreach($prev_recv as $row)
								{
									$prev_recv_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]["recv_quantity"]=$row[csf('recv_quantity')];
								}*/
							}
							else
							{
								$reqQnty = "SELECT po_break_down_id, sum(fin_fab_qnty) as fabric_qty from wo_booking_dtls where status_active=1 and is_deleted=0  group by po_break_down_id"; //and booking_no='$batch_booking'
								$reqQnty_res = sql_select($reqQnty);
								foreach($reqQnty_res as $req_val)
								{
									$req_qty_array[$req_val[csf('po_break_down_id')]]=$req_val[csf('fabric_qty')];
								}
							}

							$nameArray=sql_select($po_sql);
							foreach($nameArray as $row)
							{
								if ($i%2==0)
									$bgcolor="#E9F3FF";
								else
									$bgcolor="#FFFFFF";

								$orginal_val=1;
								$roll_id=0;
								$disable="";
								$tot_po_qnty+=$row[csf('po_qnty_in_pcs')];
								if($receive_basis==2){$req_qty=$req_qty_array[$row[csf('id')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$row[csf('fabric_color_id')]][$row[csf('rate')]];}else{$req_qty=$req_qty_array[$row[csf('id')]];}

								$requiredQnty=$req_qty;
								$prevRecReturnQnty=$prev_recv_return_qnty_arr[$row[csf('id')]]['qnty'];
								$prevRecQnty=$prev_recv_qnty_arr[$row[csf('id')]]['qnty']-$prevRecReturnQnty;
								$availabeQntyWithOverRcv=(($requiredQnty*$over_receive_limit)/100)+$requiredQnty;
								$tot_req_qnty+=$req_qty;
								$balance_req_qnty=$req_qty-$prevRecQnty;
							 	?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
									<td ><p><? echo $row[csf('job_no')]; ?></p></td>
									<td ><p><? echo $row[csf('style_ref_no')]; ?></p></td>
									<td >
										<p><? echo $row[csf('po_number')]; ?></p>
										<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
										<input type="hidden" name="txtOrginal[]" id="txtOrginal_<? echo $i; ?>" value="<? echo $orginal_val; ?>">
										<input type="hidden" name="txtRollId[]" id="txtRollId_<? echo $i; ?>" value="<? echo $roll_id; ?>">
									</td>
									<td  align="right">
										<? echo $row[csf('po_qnty_in_pcs')]; ?>
										<input type="hidden" name="txtPoQnty[]" id="txtPoQnty_<? echo $i; ?>" value="<? echo $row[csf('po_qnty_in_pcs')]; ?>">
									</td>
									<td  align="right" title="Over Receive =<? echo $over_receive_limit;?>% , Total Required qnty with over receive = <? echo $availabeQntyWithOverRcv; ?> , Previous Receive qnty = <? echo $prevRecQnty; ?> Total balance is = <? echo $availabeQntyWithOverRcv-$prevRecQnty;?>  ">
										<?
										//echo number_format($req_qty_array[$row[csf('id')]],2,'.','');
											echo number_format($req_qty,2,'.','');
										 ?>
										 <input type="hidden" name="txtReqQnty[]" id="txtReqQnty_<? echo $i; ?>" value="<? echo $req_qty; ?>">

	                            	</td>
	                            	<td  align="right">
	                            		<?
											echo number_format($prevRecQnty,2,'.','');
										 ?>
	                            	</td>
	                            	<?
									if($roll_maintained!=1)
									{
									?>
		                            	<td>
		                            		<input type="text" name="txtRollNo[]" id="txtRollNo_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px" value="<? //echo $grey_qnty; ?>">
		                            	</td>
		                            <?
									}
									?>
	                                <td  align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
	                                <td  align="center"><? echo $row[csf('rate')]; ?></td>
									<td   align="center">
										<input type="text" name="txtGreyQnty[]" id="txtGreyQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $grey_qnty; ?>" <? echo $disable; ?>>
										<input type="hidden" name="thisChallanRcvQnty[]" id="thisChallanRcvQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $thisChallanRcvQntyArray[$row[csf('id')]]; ?>" <? echo $disable; ?>>

										<input type="hidden" name="txtRecQntyPrev[]" id="txtRecQntyPrev_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $prevRecQnty; ?>" <? echo $disable; ?>>
										<input type="hidden" name="availabeQntyWithOverRcv[]" id="availabeQntyWithOverRcv_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $availabeQntyWithOverRcv; ?>" <? echo $disable; ?>>
									</td>
									<td align="center">
	                            		<input type="text" name="txtBalanceQnty[]" id="txtBalanceQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo number_format($balance_req_qnty,2,'.',''); ?>" readonly>
	                            	</td>
									<?
									if($roll_maintained==1)
									{
										?>
										<td  align="center">
	                                        <input type="text" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px" value="<? if($roll_no!=0) echo $roll_no; ?>" <? echo $disable; ?> placeholder="<? echo $roll_arr[$order_id]+1; ?>" onBlur="roll_duplication_check(<? echo $i; ?>);" />
	                                    </td>
	                                    <td  align="center"><input type="text" name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $barcode_no; ?>" disabled/></td>
	                                    <td >
	                                        <input type="button" id="increase_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="add_break_down_tr( <? echo $i; ?> )" />
	                                        <input type="button" id="decrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $i; ?>);" />
	                                    </td>
										<?
									}
									?>
								</tr>
								<?
								$i++;
							}
						}
						?>
						<input type="hidden" name="tot_po_qnty" id="tot_po_qnty" class="text_boxes" value="<? echo $tot_po_qnty; ?>">
						<input type="hidden" name="tot_req_qnty" id="tot_req_qnty" class="text_boxes" value="<? echo $tot_req_qnty; ?>">
	                    <input type="hidden" name="txt_tot_row" id="txt_tot_row" class="text_boxes" value="<? echo $i-1; ?>">
	                </tbody>
					</table>
				</div>
				<table width="<? echo $width; ?>">
					 <tr>
						<td align="center" >
							<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
						</td>
					</tr>
				</table>
			</div>
			<?
		}
		?>
			<div id="search_div" style="margin-top:10px">
		<?
		if($type!=1)
		{
			?>
			</form>
		    <?
		}
		?>
	    </div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="create_po_search_list_view")
{
	$data = explode("_",$data);

	$search_string="%".trim($data[0])."%";
	$search_by=$data[1];

	if($search_by==1)
	{
		$search_field='b.po_number';
	}
	else if($search_by==2)
	{
		$search_field='a.job_no';
	}
	else if($search_by==3)
	{
		$search_field='a.style_ref_no';
	}
	else
	{
		$search_field='b.grouping';
	}

	$company_id =$data[2];
	$buyer_id=$data[3];
	if($data[3]>0){$buyerCond= "and a.buyer_name=$buyer_id";}
	

	$all_po_id=$data[4];

	if($all_po_id!="")
		$po_id_cond=" or b.id in($all_po_id)";
	else
		$po_id_cond="";

	$hidden_po_id=explode(",",$all_po_id);
	if (trim($data[0])=="" && $data[3]==0) 
	{
		if($buyer_id==0) { echo "Please Select Buyer First."; die; }
	}
	

	if($db_type==0)
	{
		$sql = "SELECT a.job_no, a.style_ref_no, a.order_uom, b.id, a.job_quantity, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_id $buyerCond and $search_field like '$search_string' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.id";
	}
	else
	{
		//$sql = "SELECT a.job_no, a.style_ref_no, a.order_uom, LISTAGG(b.id, ', ') WITHIN GROUP (ORDER BY b.id)  as  id, a.job_quantity, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_id $buyerCond and $search_field like '$search_string' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.job_quantity, a.job_no, a.style_ref_no, a.order_uom, b.pub_shipment_date order by a.job_no";
		$sql = "SELECT a.job_no, a.style_ref_no, a.order_uom, LISTAGG(b.id, ', ') WITHIN GROUP (ORDER BY b.id)  as  id, a.job_quantity, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$company_id $buyerCond and $search_field like '$search_string' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.job_quantity, a.job_no, a.style_ref_no, a.order_uom, b.pub_shipment_date order by a.job_no";
	}
	//echo $sql;die;
	?>
    <div align="center">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="618" class="rpt_table" >
            <thead>
                <th width="40">SL</th>
                <th width="100">Job No</th>
                <th width="250">Style No</th>
                <th width="90">Job Quantity</th>
                <th width="50">UOM</th>
                <th>Shipment Date</th>
            </thead>
        </table>
        <div style="width:618px; overflow-y:scroll; max-height:240px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" id="tbl_list_search" >
            	<?
				$i=1; $po_row_id='';
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $selectResult)
				{
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";

					$roll_used=0;

					if(in_array($selectResult[csf('id')],$hidden_po_id))
					{
						if($po_row_id=="") $po_row_id=$i; else $po_row_id.=",".$i;

						/*$roll_data_array=sql_select("select roll_no from pro_roll_details where po_breakdown_id=$selectResult[id] and roll_used=1 and entry_form=1 and status_active=1 and is_deleted=0");
						if(count($roll_data_array)>0)
						{
							$roll_used=1;
						}
						else
							$roll_used=0;*/
					}

					?>
                        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i; ?>,1,'<? echo $selectResult[csf('job_no')]; ?>')">
                            <td width="40" align="center">
								<? echo $i; ?>
                            	<input type="hidden" name="txt_individual_id" id="txt_individual_id<? echo $i ?>" value="<? echo $selectResult[csf('id')]; ?>"/>
                            	<input type="hidden" name="txt_individual_style_id" id="txt_individual_style_id<? echo $i ?>" value="<? echo $selectResult[csf('style_ref_no')]; ?>"/>
                            	<input type="hidden" name="txt_individual_job" id="txt_individual_job<? echo $i ?>" value="<? echo $selectResult[csf('job_no')]; ?>"/>
                            	<input type="hidden" name="roll_used" id="roll_used<? echo $i ?>" value="<? echo $roll_used; ?>"/>
                            </td>
                            <td width="100"><p><? echo $selectResult[csf('job_no')]; ?></p></td>
                            <td width="250"><p><? echo $selectResult[csf('style_ref_no')]; ?></p></td>
                            <td width="90" align="right"><? echo $selectResult[csf('job_quantity')]; ?></td>
                            <td width="50" align="center"><p><? echo $unit_of_measurement[$selectResult[csf('order_uom')]]; ?></p></td>
                            <td align="center"><? echo change_date_format($selectResult[csf('pub_shipment_date')]); ?></td>
                        </tr>
                    <?
                    $i++;
				}
				?>
				<input type="hidden" name="txt_po_row_id" id="txt_po_row_id" value="<? echo $po_row_id; ?>"/>
            </table>
        </div>
        <table width="620" cellspacing="0" cellpadding="0" style="border:none" align="center">
            <tr>
                <td align="center" height="30" valign="bottom">
                    <div style="width:100%">
                        <div style="width:50%; float:left" align="left">
                            <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                        </div>
                        <div style="width:50%; float:left" align="left">
                            <input type="button" name="close" onClick="show_grey_prod_recv();" class="formbutton" value="Close" style="width:100px" />
                        </div>
                    </div>
                </td>
            </tr>
        </table>
	</div>
<?

exit();
}

if($action=="roll_duplication_check")
{
	$data=explode("**",$data);
	$po_id=$data[0];
	$roll_no=trim($data[1]);
	$roll_id=$data[2];

	if($roll_id=="" || $roll_id=="0")
	{
		$sql="select a.recv_number from inv_receive_master a, pro_roll_details b where a.id=b.mst_id and b.po_breakdown_id='$po_id' and b.roll_no='$roll_no' and a.is_deleted=0 and a.status_active=1 and b.entry_form=17 and b.is_deleted=0 and b.status_active=1";
	}
	else
	{
		$sql="select a.recv_number from inv_receive_master a, pro_roll_details b where a.id=b.mst_id and b.po_breakdown_id='$po_id' and b.roll_no='$roll_no' and a.is_deleted=0 and a.status_active=1 and b.entry_form=17 and b.id<>$roll_id and b.is_deleted=0 and b.status_active=1";
	}
	//echo $sql;
	$data_array=sql_select($sql,1);
	if(count($data_array)>0)
	{
		echo "1"."_".$data_array[0][csf('recv_number')];
	}
	else
	{
		echo "0_";
	}

	exit();
}

if($action=="roll_maintained")
{
	$roll_maintained=return_field_value("fabric_roll_level","variable_settings_production","company_name ='$data' and item_category_id=3 and variable_list=3 and is_deleted=0 and status_active=1");

	$barcode_generation=return_field_value("smv_source","variable_settings_production","company_name ='$data' and variable_list=27 and is_deleted=0 and status_active=1");

	if($roll_maintained=="") $roll_maintained=0; else $roll_maintained=$roll_maintained;
	echo "document.getElementById('roll_maintained').value 	= '".$roll_maintained."';\n";
	echo "document.getElementById('barcode_generation').value 	= '".$barcode_generation."';\n";
	exit();
}

if($action=="fabricDescription_popup")
{
	echo load_html_head_contents("Fabric Description Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>

	<script>
		function js_set_value(hidden_desc_id,hidden_desc_no,hidden_gsm,hidden_weight_type,hidden_full_width,hidden_cutable_width,hidden_fabric_ref,hidden_rd_no)
		{
			//var splitData = str.split(",");
			//alert(splitData);
			$("#hidden_desc_id").val(hidden_desc_id);
			$("#hidden_desc_no").val(hidden_desc_no);
			$("#hidden_gsm").val(hidden_gsm);
			$("#hidden_weight_type").val(hidden_weight_type);
			$("#hidden_full_width").val(hidden_full_width);
			$("#hidden_cutable_width").val(hidden_cutable_width);
			$("#hidden_fabric_ref").val(hidden_fabric_ref);
			$("#hidden_rd_no").val(hidden_rd_no);
			parent.emailwindow.hide();
		}

		
	</script>
    </head>
    <body>
    <div align="center">
        <form name="styleRef_form" id="styleRef_form">
		<fieldset>
            <table cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
                <thead>
                    
                    <tr>
                    	<th>RD No</th>
                        <th>Construction</th>
                        <th>Ounce/Weight</th>
                        <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td align="center"><input type="text" style="width:130px" class="text_boxes" name="txt_rd" id="txt_rd" /></td>
                        <td align="center"><input type="text" style="width:130px" class="text_boxes" name="txt_construction" id="txt_construction" /></td>
                        <td align="center">	<input type="text" style="width:130px" class="text_boxes" name="txt_gsm_weight" id="txt_gsm_weight" /></td>
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('txt_construction').value+'**'+document.getElementById('txt_gsm_weight').value+'**'+document.getElementById('txt_rd').value, 'fabric_description_popup_search_list_view', 'search_div', 'woven_finish_fabric_roll_receive_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
                        </td>
                    </tr>
            	</tbody>
           	</table>
            <div style="margin-top:10px" id="search_div"></div>
		</fieldset>
	</form>
    </div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
	exit();
}


if ($action=="fabric_description_popup_search_list_view")
{
	//source
	extract($_REQUEST);
	list($construction,$gsm_weight,$rd_no)=explode('**',$data);
	$search_con='';
	
	if($rd_no!='') {$search_con .= " and a.rd_no='".trim($rd_no)."'";}
	if($construction!='') {$search_con .= " and a.construction like ('%".trim($construction)."%')";}
	if($gsm_weight!='') {$search_con .= " and a.gsm_weight like ('%".trim($gsm_weight)."%')";}
	
	?>
</head>

<body>
	<form name="searchdescfrm"  id="searchdescfrm">
		
            <input type="hidden" name="hidden_desc_id" id="hidden_desc_id" class="text_boxes" value="">
            <input type="hidden" name="hidden_desc_no" id="hidden_desc_no" class="text_boxes" value="">
            <input type="hidden" name="hidden_gsm" id="hidden_gsm" class="text_boxes" value="">
            <input type="hidden" name="hidden_weight_type" id="hidden_weight_type" class="text_boxes" value="">
            <input type="hidden" name="hidden_full_width" id="hidden_full_width" class="text_boxes" value="">
            <input type="hidden" name="hidden_cutable_width" id="hidden_cutable_width" class="text_boxes" value="">
            <input type="hidden" name="hidden_fabric_ref" id="hidden_fabric_ref" class="text_boxes" value="">
            <input type="hidden" name="hidden_rd_no" id="hidden_rd_no" class="text_boxes" value="">

            <!-- <div style="margin-left:10px; margin-top:10px"> -->
            <div align="center">
                <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="1350">
                    <thead>
                        <th width="50">SL</th>
                        <th width="80">Fabric Nature</th>
                        <th width="50">RD No</th>
                        <th width="150">Fabric Ref.</th>
                        <th width="50">Type</th>
                        <th width="150">Construction</th>
                        <th width="150">Design</th>
                        <th width="80">Ounce/Weight</th>
                        <th width="80">Weight Type</th>
                        <th width="80">Color Range</th>
                        <th width="60">Full Width</th>
                        <th width="60">Cuttable Width</th>
                        <th>Composition</th>
                    </thead>
                </table>
                <div style="width:1348px; max-height:300px; overflow-y:scroll" id="list_container" align="left">
                    <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="1330" id="tbl_list_search">
                        <?
						$composition_arr=array();
						$compositionData=sql_select("select mst_id, copmposition_id, percent from lib_yarn_count_determina_dtls where status_active=1 and is_deleted=0");
						foreach( $compositionData as $row )
						{
							$composition_arr[$row[csf('mst_id')]].=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
						}
                        $i=1;
						$data_array=sql_select("select a.id, a.fab_nature_id,a.rd_no,a.fabric_ref,a.type, a.construction,a.design,a.gsm_weight,a.weight_type,a.color_range_id,a.full_width,a.cutable_width 
							from lib_yarn_count_determina_mst a  
							where a.fab_nature_id=3 and a.status_active=1 and a.is_deleted=0 $search_con order by a.id desc");
                        foreach($data_array as $row)
                        {
                            if ($i%2==0)
                                $bgcolor="#E9F3FF";
                            else
                                $bgcolor="#FFFFFF";
                            $construction=$row[csf('construction')];
							$comp=$composition_arr[$row[csf('id')]];
							$cons_comp=$construction.", ".$comp;
                         ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="js_set_value('<? echo $row[csf('id')]; ?>','<? echo $cons_comp; ?>','<? echo $row[csf('gsm_weight')]; ?>','<? echo $row[csf('weight_type')]; ?>','<? echo $row[csf('full_width')]; ?>','<? echo $row[csf('cutable_width')]; ?>','<? echo $row[csf('fabric_ref')]; ?>','<? echo $row[csf('rd_no')]; ?>')" style="cursor:pointer" >
                                <td width="50"><? echo $i; ?></td>
                                <td width="80"><? echo $item_category[$row[csf('fab_nature_id')]]; ?></td>
								<td width="50"><? echo $row[csf('rd_no')]; ?></td>
								<td width="150"><? echo $row[csf('fabric_ref')]; ?></td>
								<td width="50"><? echo $row[csf('type')]; ?></td>
                                <td width="150"><p><? echo $row[csf('construction')]; ?></p></td>
								<td width="150"><? echo $row[csf('design')]; ?></td>
								<td align="center" width="80"><? echo $row[csf('gsm_weight')]; ?></td>
								<td align="center" width="80"><? echo $fabric_weight_type[$row[csf('weight_type')]]; ?></td>
								<td align="center" width="80"><? echo $color_range[$row[csf('color_range_id')]]; ?></td>
								<td align="center" width="60"><? echo $row[csf('full_width')]; ?></td>
								<td align="center" width="60"><? echo $row[csf('cutable_width')]; ?></td>
                                <td><p><? echo $comp; ?></p></td>
                            </tr>
                        <?
                        $i++;
                        }
                        ?>
                    </table>
                </div>
            </div>
		
	</form>
</body>
<script>
setFilterGrid('tbl_list_search',-1);
</script>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

</html>
<?
exit();
}

if($action=="gwoven_finish_fabric_receive_print")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	$sql="select id, recv_number, receive_basis, booking_id,booking_without_order, receive_date, challan_no, store_id, is_audited, audit_by, audit_date, supplier_id, lc_no, currency_id, exchange_rate, source from inv_receive_master where id='$data[1]' and company_id='$data[0]'";
	//echo $sql;die;
	$dataArray=sql_select($sql);


	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$supplier_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name"  );
    $user_name 		= return_library_array("select id,user_full_name from user_passwd", "id", "user_full_name");

    if ($dataArray[0][csf('booking_without_order')]==1)
	{
		$wo_arr=return_library_array( "select id, booking_no from  wo_non_ord_samp_booking_mst", "id", "booking_no"  );
	}
	else
	{
		$wo_arr=return_library_array( "select id, booking_no from  wo_booking_mst", "id", "booking_no"  );
	}
	$pi_arr=return_library_array( "select id, pi_number from  com_pi_master_details", "id", "pi_number"  );
	$lc_arr=return_library_array( "select id, lc_number from  com_btb_lc_master_details", "id", "lc_number"  );
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1", 'id', 'color_name');

	$roll_maintained=return_field_value("fabric_roll_level","variable_settings_production","company_name ='".$data[0]."' and item_category_id=3 and variable_list=3 and is_deleted=0 and status_active=1");

	/*if($roll_maintained == 1)
	{*/
	?>
	<div style="width:1430px; float: left;">
	    <table width="900" cellspacing="0" >
	        <tr>
	            <td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
	        </tr>
	        <tr class="form_caption">
	        	<td colspan="6" align="center" style="font-size:14px">
					<?
						$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
					foreach ($nameArray as $result)
					{
					?>
						 <? echo $result[csf('plot_no')]; ?>
						 <? if($result[csf('level_no')]!="") echo ",".$result[csf('level_no')]?>
						 <? if($result[csf('road_no')]!="") echo ",".$result[csf('road_no')]; ?>
						 <? if($result[csf('block_no')]!="") echo ",".$result[csf('block_no')];?>
						 <? if($result[csf('city')]!="") echo ",".$result[csf('city')];?>
						 <? if($result[csf('zip_code')]!="") echo ",".$result[csf('zip_code')]; ?>
						 <? if($result[csf('province')]!="") echo ",".$result[csf('province')];?>
						 <? if($result[csf('country_id')]!="") echo ",".$country_arr[$result[csf('country_id')]]; ?><br>
						 Email:<? if($result[csf('email')]!="") echo $result[csf('email')].",";?>
						 Website:<? if($result[csf('website')]!="") echo $result[csf('website')];


					}
	                ?>
	            </td>
	        </tr>
	        <tr>
	            <td colspan="6" align="center" style="font-size:x-large"><strong><u><? echo $data[2]; ?> Note</u></strong></td>
	        </tr>
	    </table>
	        <br>
	        <table cellspacing="0" width="1100"  border="1" rules="all" class="" style="margin-bottom: 10px;">
	        <tr>
	        	<td width="120"><strong>MRR ID :</strong></td><td width="175px"><? echo $dataArray[0][csf('recv_number')]; ?></td>
	            <td width="130"><strong>Receive Basis:</strong></td> <td width="175px"><? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
	            <td width="125"><strong>WO/PI</strong></td><td width="175px"><? if ($dataArray[0][csf('receive_basis')]==2) echo $wo_arr[$dataArray[0][csf('booking_id')]]; else if ($dataArray[0][csf('receive_basis')]==1) echo $pi_arr[$dataArray[0][csf('booking_id')]]; ?></td>
	        </tr>
	        <tr>
	            <td><strong>Receive Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
	            <td><strong>Challan No:</strong></td><td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
	            <td><strong>Store Name:</strong></td> <td width="175px"><? echo $store_library[$dataArray[0][csf('store_id')]]; ?></td>
	        </tr>
	        <tr>
	            <td><strong>Supplier:</strong></td><td width="175px"><? echo $supplier_arr[$dataArray[0][csf('supplier_id')]]; ?></td>
	            <td><strong>L/C No:</strong></td><td width="175px"><? echo $lc_arr[$dataArray[0][csf('lc_no')]]; ?></td>
	            <td><strong>Currency:</strong></td> <td width="175px"><? echo $currency[$dataArray[0][csf('currency_id')]]; ?></td>
	        </tr>
	        <tr>
	            <td><strong>Exchange Rate:</strong></td><td width="175px"><? echo $dataArray[0][csf('exchange_rate')]; ?></td>
	            <td><strong>Source:</strong></td><td width="175px"><? echo $source[$dataArray[0][csf('source')]]; ?></td>
	            <td><strong>&nbsp;</strong></td> <td width="175px"><? //echo $item_category[$dataArray[0][csf('item_category')]]; ?></td>
	        </tr>
	    </table>
	     <br>
	    
	        
	    <div style="width:100%;">
	    <table cellspacing="0" width="1300"  border="1" rules="all" class="rpt_table">
	        <thead bgcolor="#dddddd" align="center">
	            <th width="30">SL</th>
	            <th width="250" >Fabric Details</th>
	            <th width="70" >Buyer Name</th>
	            <th width="70" >Style Ref.</th>
	            <th width="120" >Order No</th>
	            <th width="70" >Color</th>
	            <th width="40" >Width</th>
	            <th width="50" >Weight</th>
	            <th width="70" >Batch/Lot</th>
	            <th width="50" >UOM</th>
	            <th width="80" >Barcode</th>
	            <th width="80" >Roll No</th>
	            <?
	            if($roll_maintained ==1 )
				{
	            	echo "<th width='80'>Roll Qty</th>";
	            }
	            else
	            {
	            	echo "<th width='80'>Receive Qty</th>";
	            }
	            ?>
	            <!-- <th width="60" >Recv. Qnty.</th> -->
	            <th width="60" >Rate</th>
	            <th width="80" >Amount</th>
	            <th width="100">Book Currency</th>
	            <th>Remarks</th>
	        </thead>
	        <tbody>
	<?

	if($roll_maintained ==1 )
	{

 		 $sql_dtls = "select b.id as recv_dtls_id,a.recv_number, a.receive_basis,c.id, c.pi_wo_batch_no,c.remarks,c.order_uom,sum(e.qnty) as order_qnty,c.order_rate, c.order_ile_cost, c.order_amount, c.cons_amount,c.batch_lot, c.roll as roll_no,d.color, d.product_name_details, d.dia_width, d.weight ,d.detarmination_id,e.qnty,e.barcode_no ,e.po_breakdown_id from  inv_receive_master a,pro_finish_fabric_rcv_dtls b,inv_transaction c,product_details_master d,pro_roll_details e   where a.id=b.mst_id and b.trans_id=c.id and c.prod_id=d.id and b.id=e.dtls_id and a.id=e.mst_id and  a.id='$data[1]'  and a.entry_form=17  and c.transaction_type=1 and c.item_category=3 group by b.id ,a.recv_number, a.receive_basis, c.id, c.pi_wo_batch_no,c.remarks,c.order_uom, c.order_rate, c.order_ile_cost, c.order_amount, c.cons_amount,c.batch_lot, c.roll ,d.color, d.product_name_details, d.dia_width, d.weight ,d.detarmination_id, e.qnty,e.barcode_no ,e.po_breakdown_id";


		 //$sql_dtls = "SELECT a.recv_number, a.receive_basis, b.id, b.pi_wo_batch_no,b.remarks,c.color, c.product_name_details, c.dia_width, c.weight, b.order_uom,sum(e.quantity) as order_qnty, b.order_rate, b.order_ile_cost, b.order_amount, b.cons_amount,b.batch_lot, c.weight, c.detarmination_id,b.roll as roll_no,d.qnty,d.barcode_no,d.po_breakdown_id from inv_receive_master a, inv_transaction b,  product_details_master c, pro_roll_details d,order_wise_pro_details e where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and b.item_category=3 and a.entry_form=17 and a.id='$data[1]' and a.id=d.mst_id and a.id=d.mst_id and e.trans_id=b.id  and e.entry_form=17 and e.prod_id=c.id and d.po_breakdown_id=e.po_breakdown_id and e.status_active=1 and e.is_deleted=0  group by  a.recv_number, a.receive_basis, b.id, b.pi_wo_batch_no,b.remarks,c.color, c.product_name_details, c.dia_width, c.weight, b.order_uom,b.order_rate, b.order_ile_cost, b.order_amount, b.cons_amount,b.batch_lot, c.weight, c.detarmination_id,b.roll ,d.qnty,d.barcode_no,d.po_breakdown_id";
	}
	else
	{
		$checkOrderwise=sql_select("select a.booking_without_order from inv_receive_master a where a.status_active=1 and a.is_deleted=0 and a.id='$data[1]'");
		if ($checkOrderwise[0][csf('booking_without_order')]==0)
		{
			$sql_dtls = "select a.recv_number, a.receive_basis,a.booking_without_order,a.exchange_rate, b.id, b.pi_wo_batch_no,b.remarks,c.color, c.product_name_details, c.dia_width, c.weight, b.order_uom, d.quantity as order_qnty, b.order_rate, b.order_ile_cost, b.order_amount, b.cons_amount,b.batch_lot,d.no_of_roll as roll_no, c.weight, c.detarmination_id,d.po_breakdown_id from inv_receive_master a, inv_transaction b,  product_details_master c,order_wise_pro_details d where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and b.id=d.trans_id and b.item_category=3 and a.entry_form=17 and a.id='$data[1]'";
		}
		else{
				$sql_dtls = " select a.recv_number,a.booking_id, a.receive_basis,a.booking_without_order, b.id, b.pi_wo_batch_no,b.remarks,c.color, c.product_name_details, c.dia_width, c.weight, b.order_uom, b.order_qnty, b.order_rate, b.order_ile_cost, b.order_amount, b.cons_amount,b.batch_lot,b.roll as roll_no, c.weight, c.detarmination_id from inv_receive_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and b.item_category=3 and a.entry_form=17 and a.id='$data[1]'";

		}
	}


	 //echo $sql_dtls;
	/*}else {
		$sql_dtls = "select a.recv_number, a.receive_basis, b.id,b.pi_wo_batch_no,b.remarks,c.color, c.product_name_details, c.dia_width, c.weight, b.order_uom, b.order_qnty, b.order_rate, b.order_ile_cost, b.order_amount, b.cons_amount,b.batch_lot, c.weight, c.detarmination_id from inv_receive_master a, inv_transaction b,  product_details_master c, pro_roll_details d where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and b.item_category=3 and a.entry_form=17 and a.id='$data[1]'";
	}*/

	$sql_result= sql_select($sql_dtls);
	$po_array=array();
	foreach($sql_result as $row)
	{
		$po_array[]=$row[csf('po_breakdown_id')];
		$nonOrderbooking_array[]=$row[csf('booking_id')];
	}
	if ($row[csf('booking_without_order')]==0)
	{
		$po_ids = implode(",",$po_array);
		if ($po_ids!="") {$poIds_cond="and b.id in ($po_ids)";}else{$poIds_cond="";}



		$po_data=sql_select("select a.buyer_name,a.style_ref_no,b.id,b.po_number from wo_po_details_master a,wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $poIds_cond");
		foreach($po_data as $row){
			$po_number_details[$row[csf('id')]]['po_number'] = $row[csf('po_number')];
			$po_number_details[$row[csf('id')]]['style_ref_no'] = $row[csf('style_ref_no')];
			$po_number_details[$row[csf('id')]]['buyer_name'] = $row[csf('buyer_name')];
		}
	}
	else
	{
		//for withou order
		$booking_ids = implode(",",$nonOrderbooking_array);
		if ($booking_ids!="") {$bookingIds_cond="and a.id in ($booking_ids)";}else{$bookingIds_cond="";}
		$booking_data=sql_select("select a.id, a.booking_no,a.buyer_id,b.style_id from wo_non_ord_samp_booking_mst a,wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bookingIds_cond");
		foreach($booking_data as $row){
			$po_number_details[$row[csf('id')]]['buyer_name'] = $row[csf('buyer_id')];
			$po_number_details[$row[csf('id')]]['style_ref_no'] = $row[csf('style_id')];
		}
	}




	$i=1;
	foreach($sql_result as $row)
	{
		if ($i%2==0)$bgcolor="#E9F3FF";
		else $bgcolor="#FFFFFF";
		$wopi="";
		if($row[csf("receive_basis")]==1)
			$wopi=return_field_value("pi_number","com_pi_master_details","id=".$row[csf("pi_wo_batch_no")]."");
		else if($row[csf("receive_basis")]==2)
			$wopi=return_field_value("booking_no","wo_booking_mst","id=".$row[csf("pi_wo_batch_no")]."");
		$totalQnty +=$row[csf("order_qnty")];
		//$totalAmount +=$row[csf("order_amount")];
		$totalAmount +=$row[csf("order_qnty")]*( $row[csf("order_rate")]+ $row[csf("order_ile_cost")]);
		//$totalbookCurr +=$row[csf("cons_amount")];
		//$totalbookCurr +=$row[csf("order_qnty")]*( $row[csf("order_rate")]+ $row[csf("order_ile_cost")])*$row[csf("exchange_rate")];

		$color = '';
		$color_id = explode(",", $row[csf('color')]);
		foreach ($color_id as $val) {
			if ($val > 0) $color .= $color_arr[$val] . ",";
		}
		$color = chop($color, ',');
		if ($row[csf('booking_without_order')]==0)
		{
			$buyer_name = $po_number_details[$row[csf('po_breakdown_id')]]['buyer_name'];
			$style_ref = $po_number_details[$row[csf('po_breakdown_id')]]['style_ref_no'];
			$po_number = $po_number_details[$row[csf('po_breakdown_id')]]['po_number'];
		}
		else
		{
			$buyer_name = $po_number_details[$row[csf('booking_id')]]['buyer_name'];
			if ($po_number_details[$row[csf('booking_id')]]['style_ref_no']!=0) {
				$style_ref = $po_number_details[$row[csf('booking_id')]]['style_ref_no'];
			}
			else
			{
				$style_ref = '';
			}

		}
		?>
            <tr bgcolor="<? echo $bgcolor; ?>">
                <td align="center"><? echo $i; ?></td>
                <td><? echo $row[csf("product_name_details")]; ?></td>
                <td><? echo $buyer_library[$buyer_name]; ?></td>
                <td><? echo $style_ref; ?></td>
                <td><? echo $po_number; ?></td>
                <td><? echo $color; ?></td>
                <td><? echo $row[csf("dia_width")]; ?></td>
                <td><? echo $row[csf("weight")]; ?></td>
                <td align="center"><? echo $row[csf("batch_lot")]; ?></td>
                <td align="center"><? echo $unit_of_measurement[$row[csf("order_uom")]]; ?></td>
                <td align="right"><? echo $row[csf("barcode_no")]; ?></td>
                <td align="right"><? echo $row[csf("roll_no")]; ?></td>
                <td align="right">
                <?
               
                if($roll_maintained ==1 )
				{
                	$totalrollQty += $row[csf("qnty")];
                	$total_roll_no += $row[csf("roll_no")];
                	echo number_format($row[csf("qnty")],2);
                }
                else
                {
                	$totalrollQty += $row[csf("order_qnty")];
                	$total_roll_no += $row[csf("roll_no")];
                	echo number_format($row[csf("order_qnty")],2);
                }

                ?>
                </td>

                <!-- <td align="right"><? //echo number_format($row[csf("order_qnty")],2); ?></td> -->
                <td align="right"><? echo $row[csf("order_rate")]; ?></td>
                <td align="right"><? $amout_cal = $row[csf("order_qnty")]*( $row[csf("order_rate")]+ $row[csf("order_ile_cost")]); echo number_format($amout_cal,2); ?></td>
                <td align="right"><? echo number_format($amout_cal*$dataArray[0][csf('exchange_rate')],2);//$row[csf("cons_amount")]; ?></td>
                <td align="right"><? echo $row[csf("remarks")]; ?></td>
            </tr>
           	<?
           	$totalbookCurr +=$amout_cal*$dataArray[0][csf('exchange_rate')];
           	$totalAmuntNew+=$amout_cal;
			$i++;
        	}
			?>
        </tbody>

        <tfoot>
            <tr>
                <td colspan="11" align="right"><strong>Total :</strong></td>
                <td align="right"><?php echo $total_roll_no; ?></td>
                <td align="right"><?php echo number_format($totalrollQty, 2); ?></td>
                <td></td>
                <!-- <td align="right"><?php //echo number_format($totalQnty, 2); ?></td> -->
                <td align="right"><? echo number_format($totalAmuntNew,2); ?></td>
                <!-- <td align="right"><?php //echo number_format($totalAmount, 2); ?></td> -->
                <td align="right"><?php echo number_format($totalbookCurr, 2); ?></td>
                <td align="right"><?php //echo $totalbookCurr; ?></td>

            </tr>
        </tfoot>

      </table>
        <br>
        <table>
            <tr>
                <?php

                if($dataArray[0][csf("is_audited")]==1){
                    ?>
                    <td><strong><? echo 'Audited By &nbsp;'.$user_name[$dataArray[0][csf("audit_by")]].'&nbsp;At&nbsp;'.$dataArray[0][csf("audit_date")]; ?></strong></td>
                    <?php
                }
                ?>


            </tr>
        </table>
        <br>
		 <?
            echo signature_table(20, $data[0], "900px");
         ?>
      </div>
   </div>
	<?
 	exit();
	//}
	/*else
	{
		?>
		<div style="width:930px;">
		    <table width="900" cellspacing="0" align="right">
		        <tr>
		            <td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
		        </tr>
		        <tr class="form_caption">
		        	<td colspan="6" align="center" style="font-size:14px">
						<?
							$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
							foreach ($nameArray as $result)
							{
							?>
								Plot No: <? echo $result['plot_no']; ?>
								Level No: <? echo $result['level_no']?>
								Road No: <? echo $result['road_no']; ?>
								Block No: <? echo $result['block_no'];?>
								City No: <? echo $result['city'];?>
								Zip Code: <? echo $result['zip_code']; ?>
								Province No: <?php echo $result['province']; ?>
								Country: <? echo $country_arr[$result['country_id']]; ?><br>
								Email Address: <? echo $result['email'];?>
								Website No: <? echo $result['website'];
							}
		                ?>
		            </td>
		        </tr>
		        <tr>
		            <td colspan="6" align="center" style="font-size:x-large"><strong><u><? echo $data[2]; ?> Challan</u></strong></td>
		        </tr>
		        <tr>
		        	<td width="120"><strong>MRR ID :</strong></td><td width="175px"><? echo $dataArray[0][csf('recv_number')]; ?></td>
		            <td width="130"><strong>Receive Basis:</strong></td> <td width="175px"><? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
		            <td width="125"><strong>WO/PI</strong></td><td width="175px"><? if ($dataArray[0][csf('receive_basis')]==2) echo $wo_arr[$dataArray[0][csf('booking_id')]]; else if ($dataArray[0][csf('receive_basis')]==1) echo $pi_arr[$dataArray[0][csf('booking_id')]]; ?></td>
		        </tr>
		        <tr>
		            <td><strong>Receive Date:</strong></td><td width="175px"><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
		            <td><strong>Challan No:</strong></td><td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
		            <td><strong>Store Name:</strong></td> <td width="175px"><? echo $store_library[$dataArray[0][csf('store_id')]]; ?></td>
		        </tr>
		        <tr>
		            <td><strong>Supplier:</strong></td><td width="175px"><? echo $supplier_arr[$dataArray[0][csf('supplier_id')]]; ?></td>
		            <td><strong>L/C No:</strong></td><td width="175px"><? echo $lc_arr[$dataArray[0][csf('lc_no')]]; ?></td>
		            <td><strong>Currency:</strong></td> <td width="175px"><? echo $currency[$dataArray[0][csf('currency_id')]]; ?></td>
		        </tr>
		        <tr>
		            <td><strong>Exchange Rate:</strong></td><td width="175px"><? echo $dataArray[0][csf('exchange_rate')]; ?></td>
		            <td><strong>Source:</strong></td><td width="175px"><? echo $source[$dataArray[0][csf('source')]]; ?></td>
		            <td><strong>&nbsp;</strong></td> <td width="175px"><? //echo $item_category[$dataArray[0][csf('item_category')]]; ?></td>
		        </tr>
		    </table>
	        <br>
	    <div style="width:100%;">
	    <table align="right" cellspacing="0" width="900"  border="1" rules="all" class="rpt_table" >
	        <thead bgcolor="#dddddd" align="center">
	            <th width="30">SL</th>
	            <th width="260" >Fabric Details</th>
	            <th width="40" >Width</th>
	            <th width="50" >Weight</th>
	            <th width="70" >Batch/Lot</th>
	            <th width="50" >UOM</th>
	            <th width="60" >Recv. Qnty.</th>
	            <th width="60" >Rate</th>
	            <th width="80" >Amount</th>
	            <th width="100" >Book Currency</th>
	        </thead>
	        <tbody>
		<?
		 $sql_dtls = "select a.recv_number, a.receive_basis, b.id, b.pi_wo_batch_no, c.product_name_details, c.dia_width, c.weight, b.order_uom, b.order_qnty, b.order_rate, b.order_ile_cost, b.order_amount, b.cons_amount,b.batch_lot, c.weight, c.detarmination_id from inv_receive_master a, inv_transaction b,  product_details_master c where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and b.item_category=3 and a.entry_form=17 and a.id='$data[1]'";

		$sql_result= sql_select($sql_dtls);
		$i=1;
		foreach($sql_result as $row)
		{
			if ($i%2==0)$bgcolor="#E9F3FF";
			else $bgcolor="#FFFFFF";
			$wopi="";
			if($row[csf("receive_basis")]==1)
				$wopi=return_field_value("pi_number","com_pi_master_details","id=".$row[csf("pi_wo_batch_no")]."");
			else if($row[csf("receive_basis")]==2)
				$wopi=return_field_value("booking_no","wo_booking_mst","id=".$row[csf("pi_wo_batch_no")]."");
			$totalQnty +=$row[csf("order_qnty")];
			$totalAmount +=$row[csf("order_amount")];
			$totalbookCurr +=$row[csf("cons_amount")];
			?>
				<tr bgcolor="<? echo $bgcolor; ?>">
	                <td align="center"><? echo $i; ?></td>
	                <td><? echo $row[csf("product_name_details")]; ?></td>
	                <td><? echo $row[csf("dia_width")]; ?></td>
	                <td><? echo $row[csf("weight")]; ?></td>
	                <td align="center"><? echo $row[csf("batch_lot")]; ?></td>
	                <td align="center"><? echo $unit_of_measurement[$row[csf("order_uom")]]; ?></td>
	                <td align="right"><? echo $row[csf("order_qnty")]; ?></td>
	                <td align="right"><? echo $row[csf("order_rate")]; ?></td>
	                <td align="right"><? echo $row[csf("order_amount")]; ?></td>
	                <td align="right"><? echo $row[csf("cons_amount")]; ?></td>
				</tr>
		<? $i++;
	    } ?>
	        </tbody>
	        <tfoot>
	            <tr>
	                <td colspan="6" align="right"><strong>Total :</strong></td>
	                <td align="right"><?php echo $totalQnty; ?></td>
	                <td align="right" colspan="2"><?php echo $totalAmount; ?></td>
	                <td align="right"><?php echo $totalbookCurr; ?></td>
	            </tr>
	        </tfoot>
	      </table>
	        <br>
			 <?
	            echo signature_table(20, $data[0], "900px");
	         ?>
	      </div>
	   </div>
		<?
	}*/
}

if($action=="show_roll_listview")
{
	$data=explode("**",str_replace("'","",$data));
	$mst_id=$data[0];
	$barcode_generation=$data[1];
	$booking_without_order=$data[2];
	$recv_dtls_id=$data[3];
	if($booking_without_order==1)
	{
		$query="select id,roll_no,barcode_no,po_breakdown_id,qnty,booking_no as po_number from pro_roll_details  where mst_id=$mst_id and entry_form=17 and roll_id=0 and status_active=1 and is_deleted=0";
		//$caption="Booking No.";
	}
	else
	{
		$query="select a.id, a.roll_no, a.barcode_no, a.po_breakdown_id, a.qnty, b.po_number, a.booking_without_order from pro_roll_details a, wo_po_break_down b where a.po_breakdown_id=b.id and a.mst_id=$mst_id and a.dtls_id=$recv_dtls_id and a.entry_form=17 and roll_id=0 and a.status_active=1 and a.is_deleted=0 order by a.id";
		//$caption="PO No.";
	}
	//echo $query;
	?>



	<div align="center">
		<?
		if($barcode_generation==2)
		{
			?>
			<input type="button" id="btn_send_to_printer" name="btn_send_to_printer" value="Send To Printer" class="formbutton" onClick="fnc_send_printer_text()"/>
			<?
		}
		else
		{
			?>
			<input type="button" id="btn_barcode_generation" name="btn_barcode_generation" value="Barcode Generation" class="formbutton" onClick="fnc_barcode_generation()"/>
			<?
		}
		?>
	</div>

	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="100%">
		<thead>
			<th width="90">PO No</th>
			<th width="45">Roll No</th>
			<th width="60">Roll Qnty</th>
			<th width="85">Barcode No.</th>
			<th>Check All <input type="checkbox" name="check_all"  id="check_all" onClick="check_all_report()"></th>
		</thead>
	</table>
	<div style="width:100%; max-height:200px; overflow-y:scroll" id="list_container" align="left">
		<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="100%" id="tbl_list_search">
			<?
			$i=1;
			//$query="select a.id, a.roll_no, a.barcode_no, a.po_breakdown_id, a.qnty, b.po_number, a.booking_without_order from pro_roll_details a, wo_po_break_down b where a.po_breakdown_id=b.id and a.dtls_id=$dtls_id and a.status_active=1 and a.is_deleted=0 order by a.id";
			$result=sql_select($query);
			foreach($result as $row)
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
					<td width="90">
						<p><? if($row[csf('booking_without_order')]!=1) echo $row[csf('po_number')]; ?></p>
						<input type="hidden" name="txtRollTableId[]" id="txtRollTableId_<? echo $i; ?>" value="<? echo $row[csf('id')]; ?>">
					</td>
					<td width="43" style="padding-left:2px"><? echo $row[csf('roll_no')]; ?></td>
					<td align="right" width="58" style="padding-right:2px"><? echo $row[csf('qnty')]; ?></td>
					<td width="85" style="padding-left:2px"><? echo $row[csf('barcode_no')]; ?></td>
					<td align="center" valign="middle">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input id="chkBundle_<? echo $i;  ?>" type="checkbox" name="chkBundle"></td>
				</tr>
				<?
				$i++;
			}
			?>
		</table>
	</div>
	<?
	exit();
}

if ($action == "report_barcode_text_file")
{
	$data = explode("***", $data);

	$booking_no=$data[2];
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1", 'id', 'color_name');
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
	$machine_no_arr = return_library_array("select id, machine_no from lib_machine_name", 'id', 'machine_no');
	$machine_brand_arr = return_library_array("select id, brand from lib_machine_name", 'id', 'brand'); // Temporary

	$sql = "select a.company_id, a.recv_number, a.location_id, a.receive_basis, a.booking_id, a.booking_no, a.booking_without_order, a.within_group, a.receive_date, a.buyer_id, a.knitting_source, a.knitting_company, b.order_id, b.prod_id, b.gsm, b.width,b.dia_width_type, b.machine_no_id, b.color_id, b.fabric_description_id, b.shift_name, b.insert_date  from inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form=17 and b.trans_id=$data[1] and b.mst_id=$data[4]";
	//echo $sql;die;
	$result = sql_select($sql);
	$party_name = '';
	$prod_date = '';
	$order_id = '';
	$buyer_name = '';
	$grey_dia = '';
	$booking_without_order = '';
	$gsm = '';
	$finish_dia = '';
	foreach ($result as $row) {
		if ($row[csf('knitting_source')] == 1) {
			$party_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('knitting_company')]);
		} else if ($row[csf('knitting_source')] == 3) {
			$party_name = return_field_value("short_name", "lib_supplier", "id=" . $row[csf('knitting_company')]);
		}


		$tube_type = $fabric_typee[$row[csf('dia_width_type')]];

		$booking_without_order= $row[csf('booking_without_order')];

		//$prod_date=date("d-m-Y",strtotime($row[csf('insert_date')]));
		//$prod_time=date("H:i",strtotime($row[csf('insert_date')]));
		$prod_date = date("d-m-Y", strtotime($row[csf('receive_date')]));
		$order_id = $row[csf('order_id')];
		$gsm = $row[csf('gsm')];
		$finish_dia = $row[csf('width')];
		$shiftName = $shift_name[$row[csf('shift_name')]];
		$colorRange = $color_range[$row[csf('color_range_id')]];

		//$color=$color_arr[$row[csf('color_id')]];
		$color = '';
		$color_id = explode(",", $row[csf('color_id')]);
		foreach ($color_id as $val) {
			if ($val > 0) $color .= $color_arr[$val] . ",";
		}
		$color = chop($color, ',');
		if (trim($color) != "") {
			//$color=", ".$color;
			//$color="".$color;
		}

		$machine_data = sql_select("select machine_no, dia_width, gauge,brand from lib_machine_name where id='" . $row[csf('machine_no_id')] . "'");
		$machine_name = $machine_data[0][csf('machine_no')];
		$machine_dia_width = $machine_data[0][csf('dia_width')];
		$machine_gauge = $machine_data[0][csf('gauge')];
		$machine_brand = $machine_data[0][csf('brand')];


		$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $row[csf('buyer_id')]);


		$comp = '';
		if ($row[csf('febric_description_id')] == 0 || $row[csf('febric_description_id')] == "") {
			$comp = return_field_value("item_description", "product_details_master", "id=" . $row[csf('prod_id')]);
		} else {
			$determination_sql = sql_select("select a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=" . $row[csf('febric_description_id')]);

			if ($determination_sql[0][csf('construction')] != "") {
				$comp = $determination_sql[0][csf('construction')] . ", ";
				$construction = $determination_sql[0][csf('construction')];
			}

			foreach ($determination_sql as $d_row) {
				$comp .= $composition[$d_row[csf('copmposition_id')]] . " " . $d_row[csf('percent')] . "% ";
				$composi .= $composition[$d_row[csf('copmposition_id')]] . " " . $d_row[csf('percent')] . "% ";
			}
		}
	}

	$po_array = array();
	$booking_no_prefix = '';
	if ($booking_without_order != 1) {
		$po_sql = sql_select("select a.job_no, a.style_ref_no, a.buyer_name, a.job_no_prefix_num, b.id, b.po_number, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($order_id)");
		foreach ($po_sql as $row) {
			$po_array[$row[csf('id')]]['no'] = $row[csf('po_number')];
			$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
			$po_array[$row[csf('id')]]['prefix'] = $row[csf('job_no_prefix_num')];
			$po_array[$row[csf('id')]]['grouping'] = $row[csf('grouping')];
			$po_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
			$po_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
			$buyer_name = $buyer_arr[$row[csf('buyer_name')]];
		}
	}
	foreach (glob("" . "*.zip") as $filename) {
		@unlink($filename);
	}
	//echo $within_group;
	//exit;
	$i = 1;
	$zip = new ZipArchive();            // Load zip library
	$filename = str_replace(".sql", ".zip", 'norsel_bundle.sql');            // Zip name
	if ($zip->open($filename, ZIPARCHIVE::CREATE) !== TRUE) {        // Opening zip file to load files
		$error .= "* Sorry ZIP creation failed at this time<br/>";
		echo $error;
	}

	$i = 1;
	$year = date("y");
	$query = "select a.id, a.roll_no, a.po_breakdown_id, a.barcode_no, a.qnty,a.reject_qnty from pro_roll_details a  where a.id in($data[0]) and a.entry_form=17 and roll_id=0 order by a.barcode_no asc";
	//echo	$booking_without_order;die;
	//echo $query;die;
	$res = sql_select($query);
	$split_data_arr = array();
	foreach ($res as $row) {
		$split_roll_id = $row[csf('id')];
		$roll_split_query = sql_select("select a.barcode_no, a.qnty, a.id, a.roll_split_from from pro_roll_details a where a.roll_id = $split_roll_id and a.roll_split_from != 0");
		$file_name = "NORSEL-IMPORT_" . $i;
		$myfile = fopen($file_name . ".txt", "w") or die("Unable to open file!");
		$txt = "Norsel_imp\r\n1\r\n";
		if ($booking_without_order == 1) {
			$txt .= $party_name . ",";
			$txt .= "Job No.".$booking_no_prefix . "\r\n";
			$txt .= $machine_name . "-" . $machine_dia_width . "X" . $machine_gauge . "\r\n";
			$full_job_no = $full_booking_no;
			//$txt .=$party_name." Booking No.".$booking_no_prefix." M/C:".$machine_name."-".$machine_dia_width."X".$machine_gauge."\r\n";
		} else {
			$txt .= $party_name;
			$txt .= ",Job No.".$po_array[$row[csf('po_breakdown_id')]]['prefix'] . "\r\n";
			$txt .= $machine_name . "-" . $machine_dia_width . "X" . $machine_gauge . "\r\n";
			$full_job_no = $po_array[$row[csf('po_breakdown_id')]]['job_no'];
		}

		if (!empty($roll_split_query)) {
			$qnty = number_format($roll_split_query[0]['qnty'], 2, '.', '');
			$barcode = $roll_split_query[0]['barcode_no'];
		} else {
			$qnty = number_format($row[csf('QNTY')], 2, '.', '');
			$barcode = $row[csf('barcode_no')];
		}
		$txt .= $barcode . "\r\n";
		//$txt .="Barcode No: ".$row[csf('barcode_no')]."\r\n";
		$txt .= "ID:".$barcode . "\r\n";
		$txt .= "Booking/PI No:".$booking_no . "\r\n";
		$txt .= "D:" . $prod_date . "\r\n";
		$txt .= "Order No: " . $po_array[$row[csf('po_breakdown_id')]]['no'] . "\r\n";//ok
		$txt .= $comp . "\r\n";//ok
		$txt .= "Buyer: ".$buyer_name . "\r\n";
		$txt.="Finish Dia:".$finish_dia."\r\n";
		$txt.="Dia type:".$tube_type."\r\n";
		$txt .= "GSM: " . $gsm . "\r\n";
		$txt .= "Yarn Count: ".$yarn_count . "\r\n";//.$brand." Lot:".$yarn_lot."\r\n";
		$txt .= "RollWt:".$qnty . "Kg\r\n";
		$txt .= "Roll No:" . $row[csf('roll_no')] . "\r\n";
		$txt .="Color:". trim($color) . "\r\n";
		$txt .= "Style Ref.: " . $po_array[$row[csf('po_breakdown_id')]]['style_ref'] . "\r\n";
		fwrite($myfile, $txt);
		fclose($myfile);

		$i++;
	}
	foreach (glob("" . "*.txt") as $filenames) {
		$zip->addFile($file_folder . $filenames);
	}
	$zip->close();

	foreach (glob("" . "*.txt") as $filename) {
		@unlink($filename);
	}
	echo "norsel_bundle";
	exit();
}


if ($action == "report_barcode_generation_present") {

	$data = explode("***", $data);

	//echo "<pre>";
	//print_r($data);

	$booking_no=$data[2];
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1", 'id', 'color_name');
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');


	$sql = "select a.company_id, a.recv_number, a.location_id, a.receive_basis, a.booking_id, a.booking_no, a.booking_without_order, a.within_group, a.receive_date, a.buyer_id, a.knitting_source, a.knitting_company,a.supplier_id, b.order_id, b.prod_id, b.gsm, b.width,b.dia_width_type, b.machine_no_id, b.color_id, b.fabric_description_id, b.shift_name, b.insert_date  from inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form=17 and b.trans_id=$data[1] and b.mst_id=$data[4]";
	//echo $sql;die;
	$result = sql_select($sql);
	$party_name = '';
	$prod_date = '';
	$order_id = '';
	$buyer_name = '';
	$grey_dia = '';
	$booking_without_order = '';
	$gsm = '';
	$finish_dia = '';

	foreach ($result as $row) {
		/*if ($row[csf('knitting_source')] == 1) {
			$party_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('knitting_company')]);
		} else if ($row[csf('knitting_source')] == 3) {
			$party_name = return_field_value("short_name", "lib_supplier", "id=" . $row[csf('knitting_company')]);
		}*/

		$tube_type = $fabric_typee[$row[csf('dia_width_type')]];

		$booking_without_order= $row[csf('booking_without_order')];

		$prod_date = date("d-m-Y", strtotime($row[csf('receive_date')]));
		$order_id = $row[csf('order_id')];
		$gsm = $row[csf('gsm')];
		$finish_dia = $row[csf('width')];
		$shiftName = $shift_name[$row[csf('shift_name')]];
		$colorRange = $color_range[$row[csf('color_range_id')]];

		$color = '';
		$color_id = explode(",", $row[csf('color_id')]);
		foreach ($color_id as $val) {
			if ($val > 0) $color .= $color_arr[$val] . ",";
		}
		$color = chop($color, ',');

		$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $row[csf('buyer_id')]);
		$suplier_name = return_field_value("supplier_name", "lib_supplier", "id=" . $row[csf('supplier_id')]);
		$product_description = return_field_value("product_name_details", "product_details_master", "id=" . $row[csf('prod_id')]);

		$comp = '';
		if ($row[csf('fabric_description_id')] == 0 || $row[csf('fabric_description_id')] == "") {
			$comp = return_field_value("item_description", "product_details_master", "id=" . $row[csf('prod_id')]);
		} else {
			$determination_sql = sql_select("select a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=" . $row[csf('fabric_description_id')]);

			if ($determination_sql[0][csf('construction')] != "") {
				$comp = $determination_sql[0][csf('construction')] . ", ";
				$construction = $determination_sql[0][csf('construction')];
			}

			foreach ($determination_sql as $d_row) {
				$comp .= $composition[$d_row[csf('copmposition_id')]] . " " . $d_row[csf('percent')] . "% ";
				$composi .= $composition[$d_row[csf('copmposition_id')]] . " " . $d_row[csf('percent')] . "% ";
			}
		}
	}

	$po_array = array();
	$booking_no_prefix = '';
	if ($booking_without_order != 1) {
		$po_sql = sql_select("select a.job_no, a.style_ref_no, a.buyer_name, a.job_no_prefix_num, b.id, b.po_number, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($order_id)");
		foreach ($po_sql as $row) {
			$po_array[$row[csf('id')]]['po_number'] = $row[csf('po_number')];
			$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
			$po_array[$row[csf('id')]]['prefix'] = $row[csf('job_no_prefix_num')];
			$po_array[$row[csf('id')]]['grouping'] = $row[csf('grouping')];
			$po_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
			$po_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
			$buyer_name = $buyer_arr[$row[csf('buyer_name')]];
		}
	}


	$i = 1;
	$barcode_array = array();
	$query = "select a.id, a.roll_no, a.po_breakdown_id, a.barcode_no, a.qnty from pro_roll_details a  where a.id in($data[0]) and a.entry_form=17 and roll_id=0 order by a.barcode_no asc";

	$res = sql_select($query);

	foreach ($res as $row) {
		$barcode_array[$i] = $row[csf('barcode_no')];
		$i++;
		?>
	<style>
		.break {page-break-after:always;}
		th{
			border: 1px solid black;
			border-collapse:collapse ;
			padding: 5px;
		}
    </style>
    <div style="width:520px; padding:10px 0 10px; 0;">
    <div style="width:100%; float:left;">
        <table width="500" border="0" cellpadding="0" cellspacing="0" style="font-size:10px;" align="left">
           	 <tr height="35">
                <td style="padding-left:5px;padding-top:10px;padding-bottom:5px"><div id="div_<?php echo $i; ?>"></div></td>
            </tr>
            <tr height="35">
                <td style="width:20px; padding-right:5px;">OUR REF. &nbsp;</td> <td>:--------------------------------------------------------------------------------------------------------------------------</td>
            </tr>
            <tr height="35">
                <td style="width:20px; padding-right:5px;">BUYER  &nbsp;</td> <td>: <?php echo $buyer_name; ?></td>
            </tr>
            <tr height="35">
                <td style="width:20px; padding-right:5px;">ART/STYLE #  &nbsp;</td> <td>: <?php echo $po_array[$row[csf('po_breakdown_id')]]['style_ref']; ?></td>
            </tr>
            <tr height="35">
                <td style="width:20px; padding-right:5px;">ORDER #  &nbsp;</td> <td>: <?php echo $po_array[$row[csf('po_breakdown_id')]]['po_number']; ?> </td>
            </tr>
            <tr height="35">
                <td style="width:20px; padding-right:5px;">DESCRIPTION  &nbsp;</td> <td>: <?php echo $product_description; ?></td>
            </tr>
            <tr height="35">
                <td style="width:20px; padding-right:5px;">FABRICS/YARN &nbsp;</td> <td>: <?php echo $construction; ?></td>
            </tr>
            <tr height="35">
                <td style="width:20px; padding-right:5px;">CONST/COUNT &nbsp;</td> <td>: <?php echo $composi; ?></td>
            </tr>
            <tr height="35">
                <td style="width:20px; padding-right:5px;">WEIGHT &nbsp;</td> <td>: <?php echo $row[csf('qnty')]; ?></td>
            </tr>
            <tr height="35">
                <td style="width:20px; padding-right:5px;">SUPPLIER &nbsp;</td>
                <td width="100%">: <?php echo $suplier_name; ?> <span style="margin-left:100px;"> COLOR:  &nbsp;  <?php echo $color; ?> </span></td>
            </tr>
        </table>
        </div>


        <div style="width:100%; float:left;">
        <span> SAMPLE OF </span>
        <table width="500" border="0" cellpadding="0" cellspacing="0" style="font-size:10px; float:left;" align="left">
             <tr>
                <th style="width:20px; border-left:none;"> Approval </th>
                <th> Pre Production </th>
                <th> Reference</th>
                <th style="border-right:none;"> Shipment</th>
             </tr>

            <tr height="35">
                <td style="width:20px; padding-right:5px;">DATE &nbsp;</td> <td colspan="3">:--------------------------------------------------------------------------------------------------------------------------</td>
            </tr>
            <tr height="35">
                <td style="width:20px; padding-right:5px;">PRICE &nbsp;</td> <td colspan="3">:--------------------------------------------------------------------------------------------------------------------------</td>
            </tr>
            <tr height="35">
                <td style="width:20px; padding-right:30px;">REMARKS &nbsp;</td> <td colspan="3">:--------------------------------------------------------------------------------------------------------------------------</td>
            </tr>
        </table>
    	</div>
        </div>

     	<?
	    if ($i && $i%1 == 0) {echo "<p class=\"break\"></p>";}
	}

	?>

	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		var barcode_array =<? echo json_encode($barcode_array); ?>;
		function generateBarcode(td_no, valuess) {
            var value = valuess;//$("#barcodeValue").val();
            //alert(value)
            var btype = 'code39';//$("input[name=btype]:checked").val();
            var renderer = 'bmp';// $("input[name=renderer]:checked").val();

            var settings = {
            	output: renderer,
            	bgColor: '#FFFFFF',
            	color: '#000000',
            	barWidth: 1,
            	barHeight: 30,
            	moduleSize: 5,
            	posX: 10,
            	posY: 20,
            	addQuietZone: 1
            };
            //$("#barcode_img_id").html('11');
            value = {code: value, rect: false};

            $("#div_" + td_no).show().barcode(value, btype, settings);
        }

        for (var i in barcode_array) {
        	generateBarcode(i, barcode_array[i]);
        }
    </script>
    <?

    exit();
}



if ($action == "report_barcode_generation") {

	$data = explode("***", $data);
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1", 'id', 'color_name');
	$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');

	$sql = "select a.company_id, a.recv_number, a.location_id, a.receive_basis, a.booking_id, a.booking_no, a.booking_without_order, a.within_group, a.receive_date, a.buyer_id, a.knitting_source, a.knitting_company,a.supplier_id,a.dyeing_source, b.order_id, b.prod_id, b.gsm, b.width,b.dia_width_type, b.machine_no_id, b.color_id, b.fabric_description_id, b.shift_name, b.insert_date  from inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.entry_form=17 and b.trans_id=$data[1] and b.mst_id=$data[4]";
	$result = sql_select($sql);
	$prod_date = '';
	$buyer_name = '';
	$grey_dia = '';
	$booking_no = '';
	$booking_without_order = '';
	$yarn_count = '';
	$brand = '';
	$gsm = '';
	$finish_dia = '';
	foreach ($result as $row) {

		if ($row[csf('dyeing_source')] == 1) {
			$buyer_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('knitting_company')]);
		} else if ($row[csf('dyeing_source')] == 3) {
			$buyer_name = return_field_value("short_name", "lib_supplier", "id=" . $row[csf('knitting_company')]);
		} else {
			$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $row[csf('buyer_id')]);
		}

		if ($row[csf("within_group")] == 1)
			$buyer_name = return_field_value("company_short_name", "lib_company", "id=" . $row[csf('buyer_id')]);
		else
			$buyer_name = return_field_value("short_name", "lib_buyer", "id=" . $row[csf('buyer_id')]);

		$suplier_name = return_field_value("supplier_name", "lib_supplier", "id=" . $row[csf('supplier_id')]);
		$product_description = return_field_value("product_name_details", "product_details_master", "id=" . $row[csf('prod_id')]);


		$booking_no = $row[csf('booking_no')];
		$booking_without_order = $row[csf('booking_without_order')];

		$prod_date = date("d-m-Y", strtotime($row[csf('insert_date')]));
		$prod_time = date("H:i", strtotime($row[csf('insert_date')]));

		$order_id = $row[csf('order_id')];
		$finish_dia = $row[csf('width')];
		$color = '';
		$color_id = explode(",", $row[csf('color_id')]);
		foreach ($color_id as $val) {
			if ($val > 0) $color .= $color_arr[$val] . ",";
		}
		$color = chop($color, ',');


		$comp = '';
		if ($row[csf('fabric_description_id')] == 0 || $row[csf('fabric_description_id')] == "") {
			$comp = return_field_value("item_description", "product_details_master", "id=" . $row[csf('prod_id')]);
		} else {
			$determination_sql = sql_select("select a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.id=" . $row[csf('fabric_description_id')]);

			if ($determination_sql[0][csf('construction')] != "") {
				$comp = $determination_sql[0][csf('construction')] . ", ";
				$construction = $determination_sql[0][csf('construction')];
			}

			foreach ($determination_sql as $d_row) {
				$comp .= $composition[$d_row[csf('copmposition_id')]] . " " . $d_row[csf('percent')] . "% ";
				$composi .= $composition[$d_row[csf('copmposition_id')]] . " " . $d_row[csf('percent')] . "% ";
			}
		}

	}

	$po_array = array();
	$booking_no_prefix = '';
	if ($booking_without_order != 1) {
		$po_sql = sql_select("select a.job_no, a.style_ref_no, a.buyer_name, a.job_no_prefix_num, b.id, b.po_number, b.grouping, b.file_no from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and b.id in($order_id)");
		foreach ($po_sql as $row) {
			$po_array[$row[csf('id')]]['po_number'] = $row[csf('po_number')];
			$po_array[$row[csf('id')]]['job_no'] = $row[csf('job_no')];
			$po_array[$row[csf('id')]]['prefix'] = $row[csf('job_no_prefix_num')];
			$po_array[$row[csf('id')]]['grouping'] = $row[csf('grouping')];
			$po_array[$row[csf('id')]]['file_no'] = $row[csf('file_no')];
			$po_array[$row[csf('id')]]['style_ref'] = $row[csf('style_ref_no')];
			$buyer_name = $buyer_arr[$row[csf('buyer_name')]];
		}
	} else{
		$booking_no_prefix = return_field_value("booking_no_prefix_num", "wo_non_ord_samp_booking_mst", "booking_no='" . $booking_no . "'");
	}

	$i = 1;
	$barcode_array = array();
	$query = "select a.id, a.roll_no, a.po_breakdown_id, a.barcode_no, a.qnty from pro_roll_details a  where a.id in($data[0]) and a.entry_form=17 and roll_id=0 order by a.barcode_no asc";
	$res = sql_select($query);
	echo '<table width="800" border="0"><tr>';
	foreach ($res as $row) {
		$barcode_array[$i] = $row[csf('barcode_no')];
		if ($booking_without_order == 1) {
			$txt = $row[csf('barcode_no')] . ", Booking No." . $booking_no_prefix . ";<br>";
		} else {
			$txt = $row[csf('barcode_no')] . ", Job No." . $po_array[$row[csf('po_breakdown_id')]]['job_no'] . ";<br>";
		}

		$txt .= "Our Ref. : " . $po_array[$row[csf('po_breakdown_id')]]['grouping'] . "; Buyer :" . $buyer_name .  ",<br>";
		$txt .= "ART/Style # : " . $po_array[$row[csf('po_breakdown_id')]]['style_ref'] . ",<br> Order :" . $po_array[$row[csf('po_breakdown_id')]]['po_number'] .  ",<br>";
		$txt .= "Description # : " . $product_description . ",<br>";
		$txt .= "Fabrics/Yarn : " . $construction . ", Const/Count :" . $composi .  ",<br>";
		$txt .= "Roll No: " . $row[csf('roll_no')] . "; Roll Weight :" . number_format($row[csf('qnty')], 2, '.', '') . " Yds;<br>";
		if (trim($color) != "") $txt .= " Color: " . trim($color) .",<br> ";
		$txt .= "Supplier : " . $suplier_name;

		echo '<td style="padding-left:7px;padding-top:10px;padding-bottom:5px"><div id="div_' . $i . '"></div>' . $txt . '</td>';//border:dotted;
		if ($i % 3 == 0) echo '</tr><tr>';
		$i++;
	}
	echo '</tr></table>';
	?>

	<script type="text/javascript" src="../../../js/jquery.js"></script>
	<script type="text/javascript" src="../../../js/jquerybarcode.js"></script>
	<script>
		var barcode_array =<? echo json_encode($barcode_array); ?>;
		function generateBarcode(td_no, valuess) {
            var value = valuess;//$("#barcodeValue").val();
            //alert(value)
            var btype = 'code39';//$("input[name=btype]:checked").val();
            var renderer = 'bmp';// $("input[name=renderer]:checked").val();

            var settings = {
            	output: renderer,
            	bgColor: '#FFFFFF',
            	color: '#000000',
            	barWidth: 1,
            	barHeight: 30,
            	moduleSize: 5,
            	posX: 10,
            	posY: 20,
            	addQuietZone: 1
            };
            //$("#barcode_img_id").html('11');
            value = {code: value, rect: false};

            $("#div_" + td_no).show().barcode(value, btype, settings);
        }

        for (var i in barcode_array) {
        	generateBarcode(i, barcode_array[i]);
        }
    </script>
    <?
    exit();
}


function sql_update_test($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues,$commit)
{

	$strQuery = "UPDATE ".$strTable." SET ";
	$arrUpdateFields=explode("*",$arrUpdateFields);
	$arrUpdateValues=explode("*",$arrUpdateValues);

	if(count($arrUpdateFields)!=count($arrUpdateValues)){
		return "0";
	}

	if(is_array($arrUpdateFields))
	{
		$arrayUpdate = array_combine($arrUpdateFields,$arrUpdateValues);
		$Arraysize = count($arrayUpdate);
		$i = 1;
		foreach($arrayUpdate as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value.", ":$key."=".$value;
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrUpdateFields."=".$arrUpdateValues;
	}
	$strQuery .=" WHERE ";

	$arrRefFields=explode("*",$arrRefFields);
	$arrRefValues=explode("*",$arrRefValues);
	if(is_array($arrRefFields))
	{
		$arrayRef = array_combine($arrRefFields,$arrRefValues);
		$Arraysize = count($arrayRef);
		$i = 1;
		foreach($arrayRef as $key=>$value):
			$strQuery .= ($i != $Arraysize)? $key."=".$value." AND ":$key."=".$value."";
			$i++;
		endforeach;
	}
	else
	{
		$strQuery .= $arrRefFields."=".$arrRefValues."";
	}

	global $con;
	if( strpos($strQuery, "WHERE")==false)  return "0";
	echo $strQuery; die;
	 //return $strQuery; die;
	$stid =  oci_parse($con, $strQuery);
	$exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
	if ($exestd)
		return "1";
	else
		return "0";

	die;
	if ( $commit==1 )
	{
		if (!oci_error($stid))
		{
			oci_commit($con);
			return "1";
		}
		else
		{
			oci_rollback($con);
			return "10";
		}
	}
	else
		return 1;
	die;
}


if($action=="company_wise_load")
{
	$company_id = $data; 

	$variable_inventory_sql=sql_select("select store_method, rack_balance from variable_settings_inventory  where company_name=$company_id and item_category_id=3 and variable_list=21 and status_active=1 and is_deleted=0");
	$store_method=$variable_inventory_sql[0][csf("store_method")];
	if($store_method=="") $store_method=0;
	echo "document.getElementById('store_update_upto').value 				= '" . $store_method . "';\n";

	$supplier_td = create_drop_down( "cbo_supplier", 120, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data' and b.party_type =9 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
	echo "document.getElementById('supplier').innerHTML = '".$supplier_td."';\n";

	/* $PreviligLocationArr=return_library_array( "select id,company_location_id from user_passwd where id='$user_id' and is_deleted=0  and status_active=1",'id','company_location_id');
	if($PreviligLocationArr[$user_id]!=""){$PreviligLocationCond="and id in($PreviligLocationArr[$user_id])";}*/

	$sql_location = "select id, location_name from lib_location where company_id='$data' $PreviligLocationCond and status_active=1 and is_deleted=0 group by id, location_name order by location_name"; 
	$location_td = create_drop_down("cbo_location", 130, $sql_location, "id,location_name", 1, "--Select Location--", 0,"store_change(this.value)");
	echo "document.getElementById('location_td').innerHTML = '".$location_td."';\n"; 

	$buyer_td =  create_drop_down( "cbo_buyer_name", 130, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	echo "document.getElementById('buyer_td').innerHTML = '".$buyer_td."';\n";


	$roll_maintained=return_field_value("fabric_roll_level","variable_settings_production","company_name ='$data' and item_category_id=3 and variable_list=3 and is_deleted=0 and status_active=1");

	$barcode_generation=return_field_value("smv_source","variable_settings_production","company_name ='$data' and variable_list=27 and is_deleted=0 and status_active=1");
	
	if($roll_maintained=="") $roll_maintained=0; else $roll_maintained=$roll_maintained;
	echo "document.getElementById('roll_maintained').value 	= '".$roll_maintained."';\n";
	echo "document.getElementById('barcode_generation').value 	= '".$barcode_generation."';\n";

	

	exit();

}


//PRINT BUTTON 1
if($action=="gwoven_finish_fabric_receive_print_1")
{
    extract($_REQUEST);
	$data=explode('*',$data);

	$sql="select id, recv_number, receive_basis, booking_id,booking_without_order, receive_date, challan_no, store_id, audit_by, audit_date, is_audited, supplier_id, lc_no, currency_id, exchange_rate, source, qc_name, location_id , challan_date from inv_receive_master where id='$data[1]' and company_id='$data[0]'";
	// echo $sql;die;
	$dataArray=sql_select($sql);
	$dtls_sql = "SELECT  d.buyer_id , d.fabric_description_id, b.fabric_ref, c.color ,b.roll , b.order_amount, b.cons_amount, b.order_rate, b.body_part_id, d.booking_no as pi_booking_no,d.booking_id as pi_booking_id, b.remarks, e.job_id , b.rd_no, e.qnty as recv_qnty,  a.booking_without_order, e.po_breakdown_id FROM inv_receive_master a, inv_transaction b, product_details_master c, pro_finish_fabric_rcv_dtls d,pro_roll_details e where a.id=b.mst_id and b.prod_id=c.id and a.id=d.mst_id and b.id=d.trans_id and a.id=e.mst_id and d.id=e.dtls_id and e.entry_form=564 and a.entry_form=564 and a.id='$data[1]' and a.company_id='$data[0]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 order by e.job_id "; 
	// echo $dtls_sql;die;
	$sql_result = sql_select($dtls_sql);


	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$supplier_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name"  );
    $user_name 		= return_library_array("select id,user_full_name from user_passwd", "id", "user_full_name");
	$color_name_arr=return_library_array( "select id, color_name from lib_color where status_active=1",'id','color_name');
	$lib_body_part_arr=return_library_array("select id, body_part_full_name from lib_body_part", "id", "body_part_full_name");
	$pi_arr=return_library_array( "select id, pi_number from  com_pi_master_details", "id", "pi_number"  );
	$lc_arr=return_library_array( "select id, lc_number from  com_btb_lc_master_details", "id", "lc_number"  );
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1", 'id', 'color_name'); 
	$location_arr=return_library_array( "select id, location_name from  lib_location", "id", "location_name"  );

    if ($dataArray[0][csf('booking_without_order')]==1)
	{
		$wo_arr=return_library_array( "select id, booking_no from  wo_non_ord_samp_booking_mst", "id", "booking_no"  );
	}
	else
	{
		$wo_arr=return_library_array( "select id, booking_no from  wo_booking_mst", "id", "booking_no"  );
	}
	
	
	$all_job_id=array();
	foreach($sql_result as $row)
	{
		if($row[csf('booking_without_order')]==0)
		{
			$all_job_id[$row[csf('job_id')]] = $row[csf('job_id')];
		}
	}

	$all_job_id = array_unique($all_job_id) ;
	if(!empty($all_job_id))
	{
		$job_sql = sql_select("select a.id, a.po_number, a.job_no_mst, a.job_id, b.style_ref_no from wo_po_break_down a, wo_po_details_master b where a.job_id=b.id and b.id in (".implode(',',$all_job_id).")");
		foreach ($job_sql as  $row) 
		{
			$job_ref_arr[$row[csf('job_id')]]['style_ref_no']=$row[csf('style_ref_no')];
		}
	}


	$composition_arr=array();
 	$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from  lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
	$data_array=sql_select($sql_deter);
	if(count($data_array)>0)
	{
		foreach( $data_array as $row )
		{
			if(array_key_exists($row[csf('id')],$composition_arr))
			{
				$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
			}
			else
			{
				$composition_arr[$row[csf('id')]]=$row[csf('construction')].", ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."% ";
			}
		}
	}

	$deta_array=array();
	$po_breakdown_id="";
	foreach($sql_result as $row)
	{
			$po_breakdown_ids = ",". $row[csf('po_breakdown_id')];
			$fabric_desc=$lib_body_part_arr[$row[csf('body_part_id')]].', '.$composition_arr[$row[csf('fabric_description_id')]];
			
			$data_array[$job_ref_arr[$row[csf("job_id")]]['style_ref_no']][$buyer_library[$row[csf("buyer_id")]]][$lib_body_part_arr[$row[csf("body_part_id")]]][$fabric_desc][$row[csf("fabric_ref")]][$row[csf("rd_no")]][$color_arr[$row[csf("color")]]]['roll'] += $row[csf("roll")];

			$data_array[$job_ref_arr[$row[csf("job_id")]]['style_ref_no']][$buyer_library[$row[csf("buyer_id")]]][$lib_body_part_arr[$row[csf("body_part_id")]]][$fabric_desc][$row[csf("fabric_ref")]][$row[csf("rd_no")]][$color_arr[$row[csf("color")]]]['recv_qnty'] += $row[csf("recv_qnty")];

			$data_array[$job_ref_arr[$row[csf("job_id")]]['style_ref_no']][$buyer_library[$row[csf("buyer_id")]]][$lib_body_part_arr[$row[csf("body_part_id")]]][$fabric_desc][$row[csf("fabric_ref")]][$row[csf("rd_no")]][$color_arr[$row[csf("color")]]]['order_rate'] += $row[csf("order_rate")];

			$data_array[$job_ref_arr[$row[csf("job_id")]]['style_ref_no']][$buyer_library[$row[csf("buyer_id")]]][$lib_body_part_arr[$row[csf("body_part_id")]]][$fabric_desc][$row[csf("fabric_ref")]][$row[csf("rd_no")]][$color_arr[$row[csf("color")]]]['order_amount'] += $row[csf("order_amount")];

			$data_array[$job_ref_arr[$row[csf("job_id")]]['style_ref_no']][$buyer_library[$row[csf("buyer_id")]]][$lib_body_part_arr[$row[csf("body_part_id")]]][$fabric_desc][$row[csf("fabric_ref")]][$row[csf("rd_no")]][$color_arr[$row[csf("color")]]]['remarks'] = $row[csf("remarks")];
		
		$i++;
	}

	$po_breakdown_ids = ltrim($po_breakdown_ids,",");
	$pay_modes = sql_select("select  pay_mode from wo_booking_mst where id in ($po_breakdown_ids) and status_active=1 and is_deleted=0 ");

	
	?>
	<div style="width:1230px; float: left;">
	    <table width="1200" cellspacing="0" >
	        <tr>
	            <td colspan="6" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong></td>
	        </tr>
	        <tr class="form_caption">
	        	<td colspan="6" align="center" style="font-size:14px">
					<?
						$nameArray=sql_select( "select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
					foreach ($nameArray as $result)
					{
					?>
						 <? echo $result[csf('plot_no')]; ?>
						 <? if($result[csf('level_no')]!="") echo ",".$result[csf('level_no')]?>
						 <? if($result[csf('road_no')]!="") echo ",".$result[csf('road_no')]; ?>
						 <? if($result[csf('block_no')]!="") echo ",".$result[csf('block_no')];?>
						 <? if($result[csf('city')]!="") echo ",".$result[csf('city')];?>
						 <? if($result[csf('zip_code')]!="") echo ",".$result[csf('zip_code')]; ?>
						 <? if($result[csf('province')]!="") echo ",".$result[csf('province')];?>
						 <? if($result[csf('country_id')]!="") echo ",".$country_arr[$result[csf('country_id')]]; ?><br>
						 Email:<? if($result[csf('email')]!="") echo $result[csf('email')].",";?>
						 Website:<? if($result[csf('website')]!="") echo $result[csf('website')];
					}
	                ?>
	            </td>
	        </tr>
	        <tr>
	            <td colspan="6" align="center" style="font-size:x-large"><strong><u><? echo $data[3]; ?></u></strong></td>
	        </tr>
	    </table>
	        <br>
	        <table cellspacing="0" width="1200"  border="1" rules="all" class=""  style="margin-bottom: 10px;">
	        <tr>
	        	<td width="300"><strong>MRR ID:</strong>&nbsp; <? echo $dataArray[0][csf('recv_number')]; ?></td>
	            <td width="300"><strong>Shrinkage No:</strong>&nbsp; <? echo $dataArray[0][csf('qc_name')]; ?></td>
				<td width="300"><strong>Receive Basis:</strong>&nbsp; <? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
	            <td width="300"><strong>WO/PI:</strong>&nbsp;  <? if ($dataArray[0][csf('receive_basis')]==2){ echo $wo_arr[$dataArray[0][csf('booking_id')]];} else if ($dataArray[0][csf('receive_basis')]==1){ echo $pi_arr[$dataArray[0][csf('booking_id')]];} ?> </td>

	        </tr>
	        <tr>
	            <td><strong>Challan No:</strong>&nbsp; <? echo $dataArray[0][csf('challan_no')]; ?></td>
				<td><strong>Challan Date:</strong>&nbsp; <? echo change_date_format($dataArray[0][csf('challan_date')]); ?></td>
				<td><strong>Location:</strong>&nbsp; <? echo $location_arr[$dataArray[0][csf('location_id')]]; ?> </td>
				<td><strong>Received Date:</strong>&nbsp;<? echo change_date_format($dataArray[0][csf('receive_date')]); ?> </td>

	        </tr>
	        <tr>
				<td><strong>Pay mode:</strong> <? echo $pay_mode[$pay_modes[0]['PAY_MODE']]; ?> </td>
	            <td><strong>Supplier:</strong> &nbsp;<? echo $supplier_arr[$dataArray[0][csf('supplier_id')]]; ?> </td>
	            <td><strong>L/C No:</strong> &nbsp;<? echo $lc_arr[$dataArray[0][csf('lc_no')]]; ?> </td>
				<td><strong>Store Name:</strong>&nbsp; <? echo $store_library[$dataArray[0][csf('store_id')]]; ?> </td>
	            
	        </tr>
	        <tr>
				<td><strong>Currency:</strong>&nbsp; <? echo $currency[$dataArray[0][csf('currency_id')]]; ?> </td>
	            <td><strong>Exchange Rate:</strong>&nbsp; <? echo $dataArray[0][csf('exchange_rate')]; ?> </td>
	            <td colspan="2"><strong>Source:</strong>&nbsp; <? echo $source[$dataArray[0][csf('source')]]; ?></td>
	        </tr>

	    </table>
	     <br>
	    <div style="width:100%;">

	<table cellspacing="0" width="1200"  border="1" rules="all" class="rpt_table" style="margin-bottom: 10px;" >
		<thead bgcolor="#E8E8E8" align="center">

			<th width="30">SL</th>
			<th width="80" >Buyer</th>
			<th width="80" >Style Ref</th>

			<th width="120" >Body Part</th>
			<th width="200" >Fabric Description</th>
			<th width="100" >Fabric Ref</th>

			<th width="80" >RD No</th>
			<th width="80" >Color</th>
			<th width="50" >No of Roll</th>

			<th width="70" >Quantity</th>
			<th width="60" >Rate</th>
			<th width="80" >Amount</th>
			<th>Remarks</th>

		</thead>
		<tbody>
			<?
			$i=1;
			$k ="";
			$roll_sum = $recv_sum = $order_rate_sum = $order_amount_sum = 0;
			$roll_total = $recv_total = $order_rate_total = $order_amount_total = 0;
			
			foreach($data_array as $style => $row1)
			{
				foreach($row1 as $buyer => $row2)
				{
					foreach($row2 as $body => $row3)
					{
						foreach($row3 as $fabric => $row4)
						{
							foreach($row4 as $fabric_ref => $row5)
							{
								foreach($row5 as $rd => $row6)
								{
									foreach($row6 as $color => $row)
									{
										if ($i%2==0){$bgcolor="#E9F3FF";}
										else {$bgcolor="#FFFFFF";}

										if($k ==""){$k = $style;}
											if($k != $style)
											{
												?>
													<tr bgcolor="#E8E8E8">
														<td align="right" colspan="8"><? echo "<strong>Style Total:</strong>"; ?></td>
														<td align="right" ><? echo number_format($roll_sum,2); ?></td>
														<td align="right" ><? echo number_format($recv_sum,2); ?></td>
														<td align="right" ><? echo number_format($order_rate_sum,2); ?></td>
														<td align="right" ><? echo number_format($order_amount_sum,2); ?></td>
														<td align="right" ><? ?></td>
													</tr>
												<?
												$k = $style;
												$roll_total += $roll_sum ; $recv_total += $recv_sum; $order_rate_total += $order_rate_sum; $order_amount_total += $order_amount_sum;
												$roll_sum = $recv_sum = $order_rate_sum = $order_amount_sum = 0;
											}
										?>
										<tr bgcolor="<? echo $bgcolor; ?>">
											<td align="center"><? echo $i; ?></td>
											<td align="center"><? echo $buyer; ?></td>
											<td align="center"><? echo $style; ?></td>
											<td align="center"><? echo $body; ?></td>
											<td align="center"><? echo $fabric_desc; ?></td>
											<td align="center"><? echo $fabric_ref; ?></td>
											<td align="center"><? echo $rd; ?></td>
											<td align="center"><? echo $color; ?></td>

											<td align="center"><? echo $row['roll']; $roll_sum += $row['roll']; ?></td>
											<td align="center"><? echo $row['recv_qnty']; $recv_sum += $row['recv_qnty']; ?></td>
											<td align="center"><? echo $row['order_rate'];  $order_rate_sum += $row['order_rate']; ?></td>
											<td align="center"><? echo $row['order_amount']; $order_amount_sum += $row['order_amount']; ?></td>
											<td align="right"><? //echo $row['remarks']; ?></td>
										</tr>
										<?
										$i++;
									}
								}
							}
						}
					}
				}
			}
				?>
        </tbody>

        <tfoot>
			<tr bgcolor="#E8E8E8">
				<td align="right" colspan="8"><? echo "<strong>Style Total:</strong>"; ?></td>
				<td align="right" ><? echo number_format($roll_sum,2); ?></td>
				<td align="right" ><? echo number_format($recv_sum,2); ?></td>
				<td align="right" ><? echo number_format($order_rate_sum,2); ?></td>
				<td align="right" ><? echo number_format($order_amount_sum,2); ?></td>
				<td align="right" ><? ?></td>
				<?$roll_total += $roll_sum ; $recv_total += $recv_sum; $order_rate_total += $order_rate_sum; $order_amount_total += $order_amount_sum; ?>
			</tr>
            <tr bgcolor="#E8E8E8">
                <td colspan="8" align="right"><strong>Grand Total :</strong></td>
                <td align="right"><?php echo number_format($roll_total, 2); ?></td>
                <td align="right"><?php echo number_format($recv_total, 2); ?></td>
                <td align="right"><?php echo number_format($order_rate_total,2); ?></td>
                <td align="right"><?php echo number_format($order_amount_total,2); ?></td>
				<td align="right" ><? ?></td>
            </tr>
        </tfoot>

    </table>

        <br>
		 <?
            echo signature_table(20, $data[0], "900px");
         ?>
      </div>
   </div>
	<?
 	exit();
	
}


?>

