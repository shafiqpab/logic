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

	echo create_drop_down("cbo_location", 130, $sql_location, "id,location_name", 1, "--Select Location--", 0,"load_drop_down( 'requires/woven_finish_fabric_grn_roll_qc_controller', $data+'_'+this.value, 'load_drop_down_store', 'store_td');");

}

if($action=="load_drop_down_store")
{
	$data= explode("_", $data);
	echo create_drop_down( "cbo_store_name", 152, "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and b.category_type=3 and a.company_id=$data[0] and a.location_id=$data[1] and a.status_active=1 and a.is_deleted=0 group by a.id, a.store_name order by a.store_name","id,store_name", 1, "--Select store--", 0, "" );
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
	load_room_rack_self_bin("requires/woven_finish_fabric_grn_roll_qc_controller",$data);
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
                        <? echo create_drop_down( "cbo_buyer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active=1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$company and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --","", "load_drop_down( 'woven_finish_fabric_grn_roll_qc_controller', this.value+'*'+1, 'load_drop_down_season', 'season_td'); load_drop_down( 'woven_finish_fabric_grn_roll_qc_controller', this.value+'*'+1, 'load_drop_down_brand', 'brand_td');","0" ); ?>
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
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('txt_style').value+'_'+document.getElementById('txt_order_search').value+'_'+document.getElementById('cbo_year_selection').value+'_'+document.getElementById('txt_actual_order_search').value+'_'+document.getElementById('cbo_brand_id').value+'_'+document.getElementById('cbo_season_id').value+'_'+document.getElementById('cbo_season_year').value+'_'+document.getElementById('txt_job').value+'_'+document.getElementById('cbo_search_type').value, 'create_wopi_search_list_view', 'search_div', 'woven_finish_fabric_grn_roll_qc_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
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
	//$ex_data = explode("**",$data);
	//$GRN_ID = $ex_data[0];
	$GRN_ID =$data;
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
	
	$sql="SELECT a.id as update_id, b.id as dtls_id, b.mst_id, b.wo_pi_id, b.wo_pi_no, b.item_category_id, b.entry_form, b.body_part_id, b.fabric_description_id,b.fabric_ref,b.rd_no,b.weight_type,b.width, b.cutable_width, b.color_name, b.gsm, b.uom, b.parking_quantity, b.rate, b.amount, b.no_of_roll, b.book_currency, b.ile,b.store_id,b.floor_id,b.room,b.rack,b.self, b.bin_box, b.remarks, b.inserted_by, b.insert_date, b.status_active, b.is_deleted, c.id as roll_table_id, c.barcode_no, c.roll_no, c.qnty, c.po_breakdown_id, b.job_no, a.booking_no, c.rf_id, a.receive_basis, a.booking_without_order
	from inv_receive_master a, quarantine_parking_dtls b, pro_roll_details c
	where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id and b.item_category_id=3 and a.id=$GRN_ID and a.entry_form=559 and c.entry_form=559 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";


	$result = sql_select($sql);

	foreach($result as $row)
	{
		$wo_pi_ID=$row[csf('wo_pi_id')];
		$wo_pi_no=$row[csf('wo_pi_no')];
		$receive_basis=$row[csf('receive_basis')];
		$booking_without_order=$row[csf('booking_without_order')];
		$details_arr[$row[csf("dtls_id")]]["body_part_id"] = $row[csf('body_part_id')];
		$details_arr[$row[csf("dtls_id")]]["lib_yarn_count_deter_id"] = $row[csf('fabric_description_id')];
		$details_arr[$row[csf("dtls_id")]]["gsm"] = $row[csf('gsm')];
		$details_arr[$row[csf("dtls_id")]]["uom"] = $row[csf('uom')];
		$details_arr[$row[csf("dtls_id")]]["width"] = $row[csf('width')];
		$details_arr[$row[csf("dtls_id")]]["color_name"] = $row[csf('color_name')];
		$details_arr[$row[csf("dtls_id")]]["weight_type"] = $row[csf('weight_type')];
		$details_arr[$row[csf("dtls_id")]]["cutable_width"] = $row[csf('cutable_width')];
		$details_arr[$row[csf("dtls_id")]]["parking_quantity"] = $row[csf('parking_quantity')];
		//$details_arr[$row[csf("dtls_id")]]["roll_data"] .= $row[csf('po_breakdown_id')] . "**" . $row[csf('job_no')] . "**" . $row[csf('booking_no')] . "**" . $row[csf('qnty')] . "**" . $row[csf('roll_no')] . "**" . "" . "**" . $row[csf('roll_table_id')] . "**" . $row[csf('barcode_no')] . "**" . $row[csf('rf_id')].",";

		$details_arr[$row[csf("dtls_id")]]["roll_data"] .= $row[csf('po_breakdown_id')]. "**" . $row[csf('booking_no')]. "**" . $row[csf('roll_no')] . "**" . $row[csf('barcode_no')] . "**" . ""  . "**" . $row[csf('qnty')]  . "**" . $row[csf('qnty')] . "**" . "" . "**" . $row[csf('rf_id')]. "**" . $row[csf('roll_table_id')]  . "**" . "" .",";

		//order +'**'+ booking_no +'**'+ roll_no+'**'+barcode+'**'+manual_roll+'**'+grn qnty+'**'+QC qnty+'**'+reject qnty+'**'+rfid+'**'+grn_roll_table_id+'**'+ qc_roll_table_id
	}

	$lib_body_part_arr=return_library_array("select id, body_part_full_name from lib_body_part", "id", "body_part_full_name");
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');

	if($receive_basis ==1)
	{
		$ref_info_sql = sql_select("SELECT a.determination_id as lib_yarn_count_deter_id, a.uom,a.fab_weight as gsm, a.dia_width, a.color_id, sum(a.quantity) as qnty, sum(a.quantity*a.rate) as amount, b.pi_basis_id, a.work_order_no, a.work_order_id, a.body_part_id from com_pi_item_details a,com_pi_master_details b  where b.id=a.pi_id and a.pi_id=$wo_pi_ID and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.determination_id, a.color_id, a.dia_width, a.uom, a.fab_weight,b.pi_basis_id,a.work_order_no, a.work_order_id, a.body_part_id");

		foreach($ref_info_sql as $row)
		{
			$req_qnty_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('body_part_id')]]['qnty'] +=$row[csf('qnty')];
		}
	}
	else if($receive_basis ==2)
	{
		if($booking_without_order ==0)
		{
			$refsql="SELECT b.body_part_id,b.lib_yarn_count_deter_id,b.uom, b.gsm_weight, a.booking_no as wopi_number, a.booking_mst_id, a.dia_width, a.fabric_color_id as color_id, sum(a.fin_fab_qnty) as qnty,sum(a.grey_fab_qnty*a.rate) as amount from wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b  where a.pre_cost_fabric_cost_dtls_id=b.id  and a.booking_no='$wopiNumber' and a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 group by b.body_part_id,a.booking_no, a.booking_mst_id, b.lib_yarn_count_deter_id,b.gsm_weight, a.dia_width, a.fabric_color_id,b.uom";

		}
		else
		{
			$refsql="SELECT body_part, booking_no as wopi_number, lib_yarn_count_deter_id, gsm_weight, dia_width, color_all_data as fabric_color_id, fabric_color, sum(finish_fabric) as qnty,uom, entry_form_id   
		 	from wo_non_ord_samp_booking_dtls
		 	where booking_no='$wopiNumber' and status_active=1 and is_deleted=0 
		 	group by booking_no,lib_yarn_count_deter_id, body_part, gsm_weight, dia_width,color_all_data,fabric_color,uom, entry_form_id";
		}
		$ref_info_sql = sql_select($refsql);
		foreach($ref_info_sql as $row)
		{
			if($booking_without_order==0)
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
			$req_qnty_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$color_id][$body_part_id]['qnty'] +=$row[csf('qnty')];
			
		}
	}

	$previous_qc_sql=sql_select("select body_part_id,fabric_description_id,gsm, width, uom, color_name, sum(parking_quantity) as qnty from quarantine_parking_dtls where entry_form=560 and wo_pi_id=$wo_pi_ID and wo_pi_no = '$wo_pi_no' and status_active=1 and is_deleted=0 group by body_part_id,fabric_description_id,gsm, width, uom, color_name");
	foreach($previous_qc_sql as $row)
	{
		$prev_qc_qnty_arr[$row[csf('body_part_id')]][$row[csf('fabric_description_id')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('uom')]][$row[csf('color_name')]]+=$row[csf('qnty')];
	}

	/* echo "<pre>";
	print_r($req_qnty_arr);
	echo "<br>==";
	print_r($prev_qc_qnty_arr); */
	//die;

	$i=1;
	foreach($details_arr as $dtls_id=> $row)
	{
		if ($i%2==0)$bgcolor="#E9F3FF";
		else $bgcolor="#FFFFFF";
		$fabric_desc=$lib_body_part_arr[$row['body_part_id']].', '.$composition_arr[$row['lib_yarn_count_deter_id']];
		$body_part_id=$row['body_part_id'];

		//Body Part	Fabrication		UOM	Weight	Actual Weight	Weight Type	Full Width	Cut. Width	QC Qty	Reject Qty

		$req_quantity = $req_qnty_arr[$row['lib_yarn_count_deter_id']][$row["uom"]][$row["gsm"]][$row["width"]][$row['color_name']][$body_part_id]['qnty'];
		$previous_qc_qnty = $prev_qc_qnty_arr[$body_part_id][$row['lib_yarn_count_deter_id']][$row["gsm"]][$row["width"]][$row["uom"]][$row['color_name']];
		$balance_qnty = $req_quantity-$previous_qc_qnty;
		
		
		?>
		<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick='change_color("<?php echo $i; ?>","#E9F3FF");'>
			<td width="120"><? echo $body_part[$body_part_id]; ?></td>
			<td width="120" class="wrap_break"><? echo $fabric_desc; ?></td>
			<td width="80"><? echo $unit_of_measurement[$row["uom"]]; ?>&nbsp;</td>
			<td width="100" class="wrap_break"><? echo $color_arr[$row['color_name']]; ?>&nbsp;</td>
			<td width="100"><? echo $row["gsm"]; ?>&nbsp;</td>
			<td width="100">
				<input type="text" id="txtActualWgt_<? echo $i; ?>" class="text_boxes_numeric"  style="width:50px" name="txtActualWgt[]" value="<? echo $row["gsm"];?>"/>
			</td>
			<td width="100"><? echo $fabric_weight_type[$row["weight_type"]]; ?>&nbsp;</td>
			<td width="100"><? echo $row["width"]; ?></td>
			<td width="100"><? echo $row["cutable_width"]; ?>&nbsp;</td>

			<td width="100">
				<input type="text" id="recvQty_<? echo $i; ?>" class="text_boxes"  style="width:50px" name="recvQty[]" onDblClick="openmypage_po(<? echo $i; ?>)" value="<? echo $row["parking_quantity"]; ?>" readonly/>
				<input type="hidden" value="<? echo chop($row['roll_data'],",");?>" id="rollData_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="rollData[]" />
				<input type="hidden" value="<? echo $dtls_id;?>" id="updateGRNDtlsId_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="updateGRNDtlsId[]" />
				<input type="hidden" value="<??>" id="updateDtlsId_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="updateDtlsId[]" />
			</td>
			<td width="100">
				<input type="text" id="txtRejQty_<? echo $i; ?>" class="text_boxes"  style="width:50px" name="txtRejQty[]" readonly disabled/>
			</td>
			<td width="100">
				<input type="text" value="<? echo number_format($balance_qnty,2,'.','');?>" id="txtBlaOrderQty_<? echo $i; ?>" class="text_boxes"  style="width:50px" name="txtBlaOrderQty[]" title="<? echo $req_quantity.'-'.$previous_qc_qnty;?>" readonly disabled/>
			</td>			
		</tr>
		<?
		$i++;
	}

	exit();
}


if($action=="show_product_listview_update")
{
	$ex_data = explode("**",$data);
	$QC_ID = $ex_data[0];


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

	/*$sql="SELECT a.id as update_id, b.id as dtls_id, b.mst_id, b.wo_pi_id, b.wo_pi_no, b.item_category_id, b.entry_form, b.body_part_id, b.fabric_description_id,b.fabric_ref,b.rd_no,b.weight_type,b.width, b.cutable_width, b.color_name, b.gsm, b.uom, b.parking_quantity, b.rate, b.amount, b.no_of_roll, b.book_currency, b.ile,b.store_id,b.floor_id,b.room,b.rack,b.self, b.bin_box, b.remarks, b.inserted_by, b.insert_date, b.status_active, b.is_deleted, c.id as roll_table_id, c.barcode_no, c.roll_no, c.qnty, c.po_breakdown_id, b.job_no, a.booking_no, c.rf_id
	from inv_receive_master a, quarantine_parking_dtls b, pro_roll_details c
	where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id and b.item_category_id=3 and a.id=$QC_ID and a.entry_form=560 and c.entry_form=559 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";*/

	$sql="SELECT a.id as update_id, b.id as dtls_id, b.grn_dtls_id, b.mst_id, b.wo_pi_id, b.wo_pi_no, b.item_category_id, b.entry_form, b.body_part_id, b.fabric_description_id,b.fabric_ref,b.rd_no,b.weight_type,b.width, b.cutable_width, b.color_name, b.gsm, b.uom, b.parking_quantity, b.qc_qnty, b.qc_reject_qnty, b.rate, b.amount, b.no_of_roll, b.book_currency, b.ile,b.store_id,b.floor_id,b.room,b.rack,b.self, b.bin_box, b.remarks, b.actual_weight, c.id as roll_table_id, c.barcode_no, c.roll_no, c.qnty, c.qc_pass_qnty, c.reject_qnty, c.po_breakdown_id, b.job_no, a.grn_number, a.grn_id, c.rf_id, c.manual_roll_no, d.receive_basis, d.booking_without_order
	from quarantine_parking_mst a, quarantine_parking_dtls b, pro_roll_details c, inv_receive_master d
	where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id and b.item_category_id=3 and a.grn_id=d.id and a.id=$QC_ID and a.entry_form=560 and c.entry_form=560 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";


	$result = sql_select($sql);

	foreach($result as $row)
	{
		$wo_pi_ID=$row[csf('wo_pi_id')];
		$wo_pi_no=$row[csf('wo_pi_no')];
		$receive_basis=$row[csf('receive_basis')];
		$booking_without_order=$row[csf('booking_without_order')];
		$details_arr[$row[csf("dtls_id")]]["body_part_id"] = $row[csf('body_part_id')];
		$details_arr[$row[csf("dtls_id")]]["lib_yarn_count_deter_id"] = $row[csf('fabric_description_id')];
		$details_arr[$row[csf("dtls_id")]]["gsm"] = $row[csf('gsm')];
		$details_arr[$row[csf("dtls_id")]]["uom"] = $row[csf('uom')];
		$details_arr[$row[csf("dtls_id")]]["color_name"] = $row[csf('color_name')];
		$details_arr[$row[csf("dtls_id")]]["width"] = $row[csf('width')];
		$details_arr[$row[csf("dtls_id")]]["weight_type"] = $row[csf('weight_type')];
		$details_arr[$row[csf("dtls_id")]]["cutable_width"] = $row[csf('cutable_width')];
		$details_arr[$row[csf("dtls_id")]]["parking_quantity"] = $row[csf('parking_quantity')];
		$details_arr[$row[csf("dtls_id")]]["qc_qnty"] = $row[csf('qc_qnty')];
		$details_arr[$row[csf("dtls_id")]]["qc_reject_qnty"] = $row[csf('qc_reject_qnty')];
		$details_arr[$row[csf("dtls_id")]]["grn_dtls_id"] = $row[csf('grn_dtls_id')];
		$details_arr[$row[csf("dtls_id")]]["actual_weight"] = $row[csf('actual_weight')];
		//$details_arr[$row[csf("dtls_id")]]["roll_data"] .= $row[csf('po_breakdown_id')] . "**" . $row[csf('job_no')] . "**" . $row[csf('booking_no')] . "**" . $row[csf('qnty')] . "**" . $row[csf('roll_no')] . "**" . "" . "**" . $row[csf('roll_table_id')] . "**" . $row[csf('barcode_no')] . "**" . $row[csf('rf_id')].",";

		$details_arr[$row[csf("dtls_id")]]["roll_data"] .= $row[csf('po_breakdown_id')]. "**" . $row[csf('wo_pi_no')]. "**" . $row[csf('roll_no')] . "**" . $row[csf('barcode_no')] . "**" . $row[csf('manual_roll_no')]  . "**" . $row[csf('qnty')]  . "**" . $row[csf('qc_pass_qnty')] . "**" . $row[csf('reject_qnty')] . "**" . $row[csf('rf_id')]. "**" . ""  . "**" . $row[csf('roll_table_id')] .",";

		//order +'**'+ booking_no +'**'+ roll_no+'**'+barcode+'**'+manual_roll+'**'+grn qnty+'**'+QC qnty+'**'+reject qnty+'**'+rfid+'**'+grn_roll_table_id+'**'+ qc_roll_table_id
	}

	$lib_body_part_arr=return_library_array("select id, body_part_full_name from lib_body_part", "id", "body_part_full_name");
	$color_arr = return_library_array("select id, color_name from lib_color", 'id', 'color_name');

	if($receive_basis ==1)
	{
		$ref_info_sql = sql_select("SELECT a.determination_id as lib_yarn_count_deter_id, a.uom,a.fab_weight as gsm, a.dia_width, a.color_id, sum(a.quantity) as qnty, sum(a.quantity*a.rate) as amount, b.pi_basis_id, a.work_order_no, a.work_order_id, a.body_part_id from com_pi_item_details a,com_pi_master_details b  where b.id=a.pi_id and a.pi_id=$wo_pi_ID and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.determination_id, a.color_id, a.dia_width, a.uom, a.fab_weight,b.pi_basis_id,a.work_order_no, a.work_order_id, a.body_part_id");

		foreach($ref_info_sql as $row)
		{
			$req_qnty_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm')]][$row[csf('dia_width')]][$row[csf('color_id')]][$row[csf('body_part_id')]]['qnty'] +=$row[csf('qnty')];
		}
	}
	else if($receive_basis ==2)
	{
		if($booking_without_order ==0)
		{
			$refsql="SELECT b.body_part_id,b.lib_yarn_count_deter_id,b.uom, b.gsm_weight, a.booking_no as wopi_number, a.booking_mst_id, a.dia_width, a.fabric_color_id as color_id, sum(a.fin_fab_qnty) as qnty,sum(a.grey_fab_qnty*a.rate) as amount from wo_booking_dtls a, wo_pre_cost_fabric_cost_dtls b  where a.pre_cost_fabric_cost_dtls_id=b.id  and a.booking_no='$wopiNumber' and a.job_no=b.job_no and a.status_active=1 and a.is_deleted=0 group by b.body_part_id,a.booking_no, a.booking_mst_id, b.lib_yarn_count_deter_id,b.gsm_weight, a.dia_width, a.fabric_color_id,b.uom";

		}
		else
		{
			$refsql="SELECT body_part, booking_no as wopi_number, lib_yarn_count_deter_id, gsm_weight, dia_width, color_all_data as fabric_color_id, fabric_color, sum(finish_fabric) as qnty,uom, entry_form_id   
		 	from wo_non_ord_samp_booking_dtls
		 	where booking_no='$wopiNumber' and status_active=1 and is_deleted=0 
		 	group by booking_no,lib_yarn_count_deter_id, body_part, gsm_weight, dia_width,color_all_data,fabric_color,uom, entry_form_id";
		}
		$ref_info_sql = sql_select($refsql);
		foreach($ref_info_sql as $row)
		{
			if($booking_without_order==0)
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
			$req_qnty_arr[$row[csf('lib_yarn_count_deter_id')]][$row[csf('uom')]][$row[csf('gsm_weight')]][$row[csf('dia_width')]][$color_id][$body_part_id]['qnty'] +=$row[csf('qnty')];
			
		}
	}

	$previous_qc_sql=sql_select("select body_part_id,fabric_description_id,gsm, width, uom, color_name, sum(parking_quantity) as qnty from quarantine_parking_dtls where entry_form=560 and wo_pi_id=$wo_pi_ID and wo_pi_no = '$wo_pi_no' and mst_id != $QC_ID and status_active=1 and is_deleted=0 group by body_part_id,fabric_description_id,gsm, width, uom, color_name");
	foreach($previous_qc_sql as $row)
	{
		$prev_qc_qnty_arr[$row[csf('body_part_id')]][$row[csf('fabric_description_id')]][$row[csf('gsm')]][$row[csf('width')]][$row[csf('uom')]][$row[csf('color_name')]]+=$row[csf('qnty')];
	}

	$i=1;
	foreach($details_arr as $dtls_id=> $row)
	{
		if ($i%2==0)$bgcolor="#E9F3FF";
		else $bgcolor="#FFFFFF";
		$fabric_desc=$lib_body_part_arr[$row['body_part_id']].', '.$composition_arr[$row['lib_yarn_count_deter_id']];
		$body_part_id=$row['body_part_id'];

		//Body Part	Fabrication		UOM	Weight	Actual Weight	Weight Type	Full Width	Cut. Width	QC Qty	Reject Qty
		

		$req_quantity = $req_qnty_arr[$row['lib_yarn_count_deter_id']][$row["uom"]][$row["gsm"]][$row["width"]][$row['color_name']][$body_part_id]['qnty'];
		$previous_qc_qnty = $prev_qc_qnty_arr[$body_part_id][$row['lib_yarn_count_deter_id']][$row["gsm"]][$row["width"]][$row["uom"]][$row['color_name']];
		$balance_qnty = $req_quantity-$previous_qc_qnty;
		?>
		<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>" onClick='change_color("<?php echo $i; ?>","#E9F3FF");'>
			<td width="120"><? echo $body_part[$body_part_id]; ?></td>
			<td width="120" class="wrap_break"><? echo $fabric_desc; ?></td>
			<td width="80"><? echo $unit_of_measurement[$row["uom"]]; ?>&nbsp;</td>
			<td width="100" class="wrap_break"><? echo $color_arr[$row['color_name']]; ?>&nbsp;</td>
			<td width="100"><? echo $row["gsm"]; ?>&nbsp;</td>
			<td width="100">
				<input type="text" value="<? echo $row["actual_weight"];?>" id="txtActualWgt_<? echo $i; ?>" class="text_boxes_numeric"  style="width:50px" name="txtActualWgt[]" />
			</td>
			<td width="100"><? echo $fabric_weight_type[$row["weight_type"]]; ?>&nbsp;</td>
			<td width="100"><? echo $row["width"]; ?></td>
			<td width="100"><? echo $row["cutable_width"]; ?>&nbsp;</td>

			<td width="100">
				<input type="text" id="recvQty_<? echo $i; ?>" class="text_boxes"  style="width:50px" name="recvQty[]" onDblClick="openmypage_po(<? echo $i; ?>)" value="<? echo $row["qc_qnty"]; ?>" readonly/>
				<input type="hidden" value="<? echo chop($row['roll_data'],",");?>" id="rollData_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="rollData[]" />
				<input type="hidden" value="" id="updateGRNDtlsId_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="updateGRNDtlsId[]" />
				<input type="hidden" value="<? echo $dtls_id;?>" id="updateDtlsId_<? echo $i; ?>" class="text_boxes"  style="width:60px" name="updateDtlsId[]" />
			</td>
			<td width="100">
				<input type="text" value="<? echo $row["qc_reject_qnty"];?>" id="txtRejQty_<? echo $i; ?>" class="text_boxes"  style="width:50px" name="txtRejQty[]" readonly disabled/>
			</td>			
			<td width="100">
				<input type="text" value="<? echo number_format($balance_qnty,2,'.','');?>" id="txtBlaOrderQty_<? echo $i; ?>" class="text_boxes"  style="width:50px" name="txtBlaOrderQty[]" title="<? echo $req_quantity.'-'.$previous_qc_qnty;?>" readonly disabled/>
			</td>
		</tr>
		<?
		$i++;
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
			echo "load_drop_down( 'requires/woven_finish_fabric_grn_roll_qc_controller', '".$bodyPardId."', 'load_drop_down_bodypart','body_part_td');\n";
			//echo "load_drop_down( 'requires/woven_finish_fabric_grn_roll_qc_controller', '".$fabric_ref."', 'load_drop_down_bodypart','body_part_td');\n";

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
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $company; ?>, 'create_lc_search_list_view', 'search_div', 'woven_finish_fabric_grn_roll_qc_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
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


    if( $operation==0 ) // Insert Here----------------------------------------------------------
	{
		$con = connect();
		if($db_type==0)	{ mysql_query("BEGIN"); }

		//txt_mrr_no*update_id*cbo_company_id*txt_grn_no*hdn_grn_id*txt_receive_date*cbo_location*cbo_store_name*cbo_supplier
		$trims_recv_num=''; $master_id='';
		if(str_replace("'","",$update_id)=="")
		{
			if($db_type==0) $year_cond="YEAR(insert_date)"; 
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later
			
			$id = return_next_id_by_sequence("QUARANTINE_PARKING_MST_PK_SEQ", "quarantine_parking_mst", $con);
			$new_woven_qc_recv_system_id = explode("*", return_next_id_by_sequence("QUARANTINE_PARKING_MST_PK_SEQ", "quarantine_parking_mst",$con,1,$cbo_company_id,'WQC',560,date("Y",time()) ));
			$field_array="id, qc_number_prefix, qc_number_prefix_num, qc_number, entry_form, receive_date, company_id, supplier_id, grn_id, grn_number, inserted_by, insert_date";
			
			$data_array="(".$id.",'".$new_woven_qc_recv_system_id[1]."',".$new_woven_qc_recv_system_id[2].",'".$new_woven_qc_recv_system_id[0]."',560,".$txt_receive_date.",".$cbo_company_id.",".$cbo_supplier.",".$hdn_grn_id.",".$txt_grn_no.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			
			$qc_number=$new_woven_qc_recv_system_id[0];
			$master_id=$id;
		}
		else
		{
			$field_array_update="receive_date*updated_by*update_date";
			
			$data_array_update=$txt_receive_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
			$qc_number=str_replace("'","",$txt_mrr_no);
			$master_id=str_replace("'","",$update_id);
		}

		$field_array_dtls="id, mst_id, wo_pi_id, wo_pi_no, grn_dtls_id, item_category_id, entry_form, body_part_id, fabric_description_id,fabric_ref,rd_no,weight_type,width, cutable_width, color_name, gsm, actual_weight, uom, parking_quantity, qc_qnty, qc_reject_qnty, rate, amount, no_of_roll, book_currency, ile,store_id,floor_id,room,rack,self, bin_box, remarks, booking_no, booking_id, inserted_by, insert_date, status_active, is_deleted";
		$field_array_roll="id, barcode_no, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, qc_pass_qnty, reject_qnty, roll_no, booking_no, booking_without_order, manual_roll_no, rf_id, inserted_by, insert_date";

		$grn_dtls_sql = sql_select("SELECT a.id as update_id, b.id as dtls_id, b.mst_id, b.wo_pi_id, b.wo_pi_no, b.item_category_id, b.entry_form, b.body_part_id, b.fabric_description_id,b.fabric_ref,b.rd_no,b.weight_type,b.width, b.cutable_width, b.color_name, b.gsm, b.uom, b.parking_quantity, b.rate, b.amount, b.no_of_roll, b.book_currency, b.ile,b.store_id,b.floor_id,b.room,b.rack,b.self, b.bin_box, b.remarks, b.booking_no as grn_booking_no, b.booking_id as grn_booking_id, c.id as roll_table_id, c.barcode_no, c.roll_no, c.qnty, c.po_breakdown_id, c.booking_without_order, b.job_no, a.booking_no, c.rf_id from inv_receive_master a, quarantine_parking_dtls b, pro_roll_details c where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id and b.item_category_id=3 and a.id=$hdn_grn_id and a.entry_form=559 and c.entry_form=559 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");

		foreach ($grn_dtls_sql as $row) 
		{
			$grnDtlsData[$row[csf("dtls_id")]]['wo_pi_id']=$row[csf("wo_pi_id")];
			$grnDtlsData[$row[csf("dtls_id")]]['wo_pi_no']=$row[csf("wo_pi_no")];
			$grnDtlsData[$row[csf("dtls_id")]]['booking_no']=$row[csf("booking_no")];
			$grnDtlsData[$row[csf("dtls_id")]]['body_part_id']=$row[csf("body_part_id")];
			$grnDtlsData[$row[csf("dtls_id")]]['fabric_description_id']=$row[csf("fabric_description_id")];
			$grnDtlsData[$row[csf("dtls_id")]]['fabric_ref']=$row[csf("fabric_ref")];
			$grnDtlsData[$row[csf("dtls_id")]]['rd_no']=$row[csf("rd_no")];
			$grnDtlsData[$row[csf("dtls_id")]]['weight_type']=$row[csf("weight_type")];
			$grnDtlsData[$row[csf("dtls_id")]]['width']=$row[csf("width")];
			$grnDtlsData[$row[csf("dtls_id")]]['cutable_width']=$row[csf("cutable_width")];
			$grnDtlsData[$row[csf("dtls_id")]]['color_name']=$row[csf("color_name")];
			$grnDtlsData[$row[csf("dtls_id")]]['gsm']=$row[csf("gsm")];
			$grnDtlsData[$row[csf("dtls_id")]]['uom']=$row[csf("uom")];
			$grnDtlsData[$row[csf("dtls_id")]]['parking_quantity']=$row[csf("parking_quantity")];
			$grnDtlsData[$row[csf("dtls_id")]]['rate']=$row[csf("rate")];
			$grnDtlsData[$row[csf("dtls_id")]]['amount']=$row[csf("amount")];
			$grnDtlsData[$row[csf("dtls_id")]]['no_of_roll']=$row[csf("no_of_roll")];
			$grnDtlsData[$row[csf("dtls_id")]]['book_currency']=$row[csf("book_currency")];
			$grnDtlsData[$row[csf("dtls_id")]]['ile']=$row[csf("ile")];
			$grnDtlsData[$row[csf("dtls_id")]]['store_id']=$row[csf("store_id")];
			$grnDtlsData[$row[csf("dtls_id")]]['floor_id']=$row[csf("floor_id")];
			$grnDtlsData[$row[csf("dtls_id")]]['room']=$row[csf("room")];
			$grnDtlsData[$row[csf("dtls_id")]]['rack']=$row[csf("rack")];
			$grnDtlsData[$row[csf("dtls_id")]]['self']=$row[csf("self")];
			$grnDtlsData[$row[csf("dtls_id")]]['bin_box']=$row[csf("bin_box")];
			$grnDtlsData[$row[csf("dtls_id")]]['remarks']=$row[csf("remarks")];
			$grnDtlsData[$row[csf("dtls_id")]]['grn_booking_no']=$row[csf("grn_booking_no")];
			$grnDtlsData[$row[csf("dtls_id")]]['grn_booking_id']=$row[csf("grn_booking_id")];

			$grnDtlsRollData[$row[csf("dtls_id")]][$row[csf("roll_table_id")]]['roll_table_id']=$row[csf("roll_table_id")];
			$grnDtlsRollData[$row[csf("dtls_id")]][$row[csf("roll_table_id")]]['barcode_no']=$row[csf("barcode_no")];
			$grnDtlsRollData[$row[csf("dtls_id")]][$row[csf("roll_table_id")]]['roll_no']=$row[csf("roll_no")];
			$grnDtlsRollData[$row[csf("dtls_id")]][$row[csf("roll_table_id")]]['qnty']=$row[csf("qnty")];
			$grnDtlsRollData[$row[csf("dtls_id")]][$row[csf("roll_table_id")]]['po_breakdown_id']=$row[csf("po_breakdown_id")];
			$grnDtlsRollData[$row[csf("dtls_id")]][$row[csf("roll_table_id")]]['booking_without_order']=$row[csf("booking_without_order")];
			$grnDtlsRollData[$row[csf("dtls_id")]][$row[csf("roll_table_id")]]['booking_no']=$row[csf("booking_no")];
			$grnDtlsRollData[$row[csf("dtls_id")]][$row[csf("roll_table_id")]]['rf_id']=$row[csf("rf_id")];
		}

		for($j=1;$j<=$tot_row;$j++)
		{
			$txtActualWgt="txtActualWgt_".$j;
			$recvQty="recvQty_".$j;
			$txtRejQty="txtRejQty_".$j;
			$rollData="rollData_".$j;
			$updateGRNDtlsId="updateGRNDtlsId_".$j;
			$updateDtlsId="updateDtlsId_".$j;

			$wo_pi_id = $grnDtlsData[$$updateGRNDtlsId]['wo_pi_id'];
			$wo_pi_no = $grnDtlsData[$$updateGRNDtlsId]['wo_pi_no'];
			$cboBodyPart = $grnDtlsData[$$updateGRNDtlsId]['body_part_id'];
			$fabricDescId = $grnDtlsData[$$updateGRNDtlsId]['fabric_description_id'];
			$txtFabricRef = $grnDtlsData[$$updateGRNDtlsId]['fabric_ref'];
			$txtRDNo= $grnDtlsData[$$updateGRNDtlsId]['rd_no'];
			$txtWeightType = $grnDtlsData[$$updateGRNDtlsId]['weight_type'];
			$txtDiaWidth = $grnDtlsData[$$updateGRNDtlsId]['width'];
			$txtCutableWidth = $grnDtlsData[$$updateGRNDtlsId]['cutable_width'];
			$txtColor = $grnDtlsData[$$updateGRNDtlsId]['color_name'];
			$txtGsm = $grnDtlsData[$$updateGRNDtlsId]['gsm'];
			$cboUOM = $grnDtlsData[$$updateGRNDtlsId]['uom'];
			$parking_quantity = $grnDtlsData[$$updateGRNDtlsId]['parking_quantity'];
			
			$txtRate = $grnDtlsData[$$updateGRNDtlsId]['rate'];
			//$amount = $grnDtlsData[$$updateGRNDtlsId]['amount'];
			$no_of_roll = $grnDtlsData[$$updateGRNDtlsId]['no_of_roll'];
			//$grnDtlsData[$$updateGRNDtlsId]['book_currency'];
			$txtIle = $grnDtlsData[$$updateGRNDtlsId]['ile'];
			$store_id = $grnDtlsData[$$updateGRNDtlsId]['store_id'];
			$floor_id = $grnDtlsData[$$updateGRNDtlsId]['floor_id'];
			$room = $grnDtlsData[$$updateGRNDtlsId]['room'];
			$rack = $grnDtlsData[$$updateGRNDtlsId]['rack'];
			$self = $grnDtlsData[$$updateGRNDtlsId]['self'];
			$bin_box = $grnDtlsData[$$updateGRNDtlsId]['bin_box'];
			$remarks = $grnDtlsData[$$updateGRNDtlsId]['remarks'];
			$booking_no = $grnDtlsData[$$updateGRNDtlsId]['booking_no'];
			$grn_booking_no = $grnDtlsData[$$updateGRNDtlsId]['grn_booking_no'];
			$grn_booking_id = $grnDtlsData[$$updateGRNDtlsId]['grn_booking_id'];
		
			$txtAmount= $$recvQty * $txtRate;
			
			$id_dtls = return_next_id_by_sequence("QUARANTINE_PARKING_DTLS_PK_SEQ", "quarantine_parking_dtls", $con);
			$data_array_dtls.="(".$id_dtls.",".$master_id.",".$wo_pi_id.",'".$wo_pi_no."',".$$updateGRNDtlsId.",3,560,'".$cboBodyPart."','".$fabricDescId."','".$txtFabricRef."','".$txtRDNo."','".$txtWeightType."','".$txtDiaWidth."','".$txtCutableWidth."','".$txtColor."','".$txtGsm."','".$$txtActualWgt."','".$cboUOM."','".$parking_quantity."','".$$recvQty."','".$$txtRejQty."','".$txtRate."','".$txtAmount."','".$no_of_roll."','0','".$txtIle."',".$store_id.",'".$floor_id."','".$room."','".$rack."','".$self."','".$bin_box."','".$remarks."','".$grn_booking_no."','".$grn_booking_id."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."','1','0')";

			$grn_dtls_id_arr[str_replace("'","",$$updateGRNDtlsId)]=str_replace("'","",$$updateGRNDtlsId);

			$roll_str_multiple = explode(",",$$rollData);
			foreach ($roll_str_multiple as  $rollStr) 
			{
				$roll_val = explode("**",$rollStr);
				$txtPoId = $roll_val[0];
				$hdnBookingNo = $roll_val[1];
				$txtRoll = $roll_val[2];
				$txtBarcodeNo = $roll_val[3];
				$manualRollNo = $roll_val[4];
				$grnQty = $roll_val[5];
				$QCQty = $roll_val[6];
				$rejectQty = $roll_val[7];
				$txtRFId = $roll_val[8];
				$grnRollTableId = $roll_val[9];
				$QCRollTableId = $roll_val[10];
				$booking_without_order = $grnDtlsRollData[$$updateGRNDtlsId][$grnRollTableId]['booking_without_order'];

				$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);
				if($data_array_roll!="") $data_array_roll.=",";
				$data_array_roll.="(".$id_roll.",".$txtBarcodeNo.",".$master_id.",".$id_dtls.",'".$txtPoId."',560,'".$grnQty."','".$QCQty."','".$rejectQty."','".$txtRoll."','".$booking_no."',".$booking_without_order.",'".$manualRollNo."','".$txtRFId."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

				$grn_roll_table_id_arr[$grnRollTableId]=$grnRollTableId;
			}
			
		}

		$rID=$rID2=$rID3=$rID4=$rID5=true;

		if(str_replace("'","",$update_id)=="")
		{
			//echo "10**INSERT INTO quarantine_parking_mst (".$field_array.") VALUES ".$data_array.""; die;
			$rID=sql_insert("quarantine_parking_mst",$field_array,$data_array,0);
			if($rID) $flag=1; else $flag=0;
		}
		else
		{
			//echo "update quarantine_parking_mst set(".$field_array_update.")=".$data_array_update; die;
			$rID=sql_update("quarantine_parking_mst",$field_array_update,$data_array_update,"id",$update_id,1);
			if($rID) $flag=1; else $flag=0;
		}
		
		if($flag==1)
		{
			//echo "10**insert into quarantine_parking_dtls (".$field_array_dtls.") values".$data_array_dtls; die;
			$rID2 = sql_insert("quarantine_parking_dtls",$field_array_dtls,$data_array_dtls,1);
			if($rID2) $flag=1; else $flag=0;
		}

		if($flag==1)
		{
			//echo "10**INSERT INTO pro_roll_details (".$field_array_roll.") VALUES ".$data_array_roll.""; die;
			$rID3=sql_insert("pro_roll_details",$field_array_roll,$data_array_roll,0);
			if($flag==1)
			{
				if($rID3) $flag=1; else $flag=0;
			}
		}

		if(count($grn_dtls_id_arr)>0)
		{
			$rID4=execute_query("update quarantine_parking_dtls set IS_QC_PASS=1 where id in(".implode(",",$grn_dtls_id_arr).")");
			if($flag==1)
			{
				if($rID4) $flag=1; else $flag=0;
			}
		}

		if (count($grn_roll_table_id_arr))
		{
			//echo "10**update pro_roll_details set roll_used=1 where entry_form=559 and id in(".implode(",",$grn_roll_table_id_arr).")";die;
			$rID5=execute_query("update pro_roll_details set roll_used=1 where entry_form=559 and id in(".implode(",",$grn_roll_table_id_arr).")");
			if($flag==1)
			{
				if($rID5) $flag=1; else $flag=0;
			}
		}


		//echo "10**$rID=$rID2=$rID3=$rID4=$rID5=".$flag;oci_rollback($con);die();

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "0**".$master_id."**".$qc_number."**0";
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
				echo "0**".$master_id."**".$qc_number."**0";
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

		$field_array_update="receive_date*updated_by*update_date";
		$data_array_update=$txt_receive_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";


		$grn_dtls_sql = sql_select("SELECT a.id as update_id, b.id as dtls_id, b.mst_id, b.wo_pi_id, b.wo_pi_no, b.item_category_id, b.entry_form, b.body_part_id, b.fabric_description_id,b.fabric_ref,b.rd_no,b.weight_type,b.width, b.cutable_width, b.color_name, b.gsm, b.uom, b.parking_quantity, b.rate, b.amount, b.no_of_roll, b.book_currency, b.ile,b.store_id,b.floor_id,b.room,b.rack,b.self, b.bin_box, b.remarks, b.booking_no as grn_booking_no, b.booking_id as grn_booking_id, c.id as roll_table_id, c.barcode_no, c.roll_no, c.qnty, c.po_breakdown_id, c.booking_without_order, b.job_no, a.booking_no, c.rf_id from inv_receive_master a, quarantine_parking_dtls b, pro_roll_details c where a.id=b.mst_id and a.id=c.mst_id and b.id=c.dtls_id and b.item_category_id=3 and a.id=$hdn_grn_id and a.entry_form=559 and c.entry_form=559 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");

		foreach ($grn_dtls_sql as $row) 
		{
			$grnDtlsData[$row[csf("dtls_id")]]['rate']=$row[csf("rate")];
		}

		for($j=1;$j<=$tot_row;$j++)
		{
			$txtActualWgt="txtActualWgt_".$j;
			$recvQty="recvQty_".$j;
			$txtRejQty="txtRejQty_".$j;
			$rollData="rollData_".$j;
			$updateGRNDtlsId="updateGRNDtlsId_".$j;
			$updateDtlsId="updateDtlsId_".$j;


			$txtRate = $grnDtlsData[$$updateGRNDtlsId]['rate'];
			$txtAmount= $$recvQty * $txtRate;

			$update_array_dtls[$$updateDtlsId]=explode("*",("".$$recvQty."*'".$$txtActualWgt."'*'".$$txtAmount."'*'".$$txtRejQty."'*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
			$update_dtls_id_arr[] = $$updateDtlsId;


			$roll_str_multiple = explode(",",$$rollData);
			foreach ($roll_str_multiple as  $rollStr) 
			{
				$roll_val = explode("**",$rollStr);
				$txtPoId = $roll_val[0];
				$hdnBookingNo = $roll_val[1];
				$txtRoll = $roll_val[2];
				$txtBarcodeNo = $roll_val[3];
				$manualRollNo = $roll_val[4];
				$grnQty = $roll_val[5];
				$QCQty = $roll_val[6];
				$rejectQty = $roll_val[7];
				$txtRFId = $roll_val[8];
				$grnRollTableId = $roll_val[9];
				$QCRollTableId = $roll_val[10];

				$update_roll_id_arr[] = $QCRollTableId;
				$roll_data_array_update[$QCRollTableId] = explode("*", ("'".$QCQty ."'*'" .$rejectQty ."'*'" .$manualRollNo. "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));
			}
		}

		$rID=$rID2=$rID3=1;

		$rID=sql_update("quarantine_parking_mst",$field_array_update,$data_array_update,"id",$update_id,1);
		if($rID) $flag=1; else $flag=0;

		
		$field_array_dtls_update="qc_qnty*actual_weight*amount*qc_reject_qnty*updated_by*update_date";
		if(count($update_array_dtls)>0)
	 	{
			$rID2=execute_query(bulk_update_sql_statement("quarantine_parking_dtls","id",$field_array_dtls_update,$update_array_dtls,$update_dtls_id_arr),1);
			if($flag==1) 
			{
				if($rID2) $flag=1; else $flag=0; 
			} 
	 	}

		$field_array_roll_update="qc_pass_qnty*reject_qnty*manual_roll_no*updated_by*update_date";
		if(count($roll_data_array_update)>0)
	 	{
			//echo "10**".bulk_update_sql_statement("pro_roll_details","id",$field_array_roll_update,$roll_data_array_update,$update_roll_id_arr);die;
			$rID3=execute_query(bulk_update_sql_statement("pro_roll_details","id",$field_array_roll_update,$roll_data_array_update,$update_roll_id_arr),1);
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
		echo "10**";
		die;
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
                    <th width="130">Company</th>
                    <th width="130">Buyer Name</th>
                    <th width="100">Store Name</th>
                    <th width="120">Search By</th>
                    <th width="150" align="center" id="search_by_td_up">GRN No.</th>
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
                            $search_by = array(1=>'QC No',2=>'GRN No.',3=>'WO No',4=>'PI No');
							$dd="change_search_event(this.value, '0*0*0*0', '0*0*0*0', '../../') ";

							//$search_by = array(1=>'QC No',2=>'GRN No.');
							//$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 120, $search_by,"",0, "--Select--", "2",$dd,0 );
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
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+document.getElementById('cbo_store_name_id').value, 'create_mrr_search_list_view', 'search_div', 'woven_finish_fabric_grn_roll_qc_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
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
			$sql_cond .= " and c.recv_number LIKE '%$txt_search_common%'";

		}
		else if(trim($txt_search_by)==2) // for chllan no
		{
			$sql_cond .= " and c.challan_no LIKE '%$txt_search_common%'";
 		}
 		else if(trim($txt_search_by)==3) // for WO no
		{
			$sql_cond .= " and c.receive_basis=2 and c.booking_no LIKE '%$txt_search_common%'";
 		}
 		else if(trim($txt_search_by)==4) // for PI no
		{
			$sql_cond .= " and c.receive_basis=1 and c.booking_no LIKE '%$txt_search_common%'";
 		}

 	}

	if( $fromDate!="" && $toDate!="" )
	{
		if($db_type==0)
		{
			$sql_cond .= " and c.receive_date  between '".change_date_format($fromDate,'yyyy-mm-dd')."' and '".change_date_format($toDate,'yyyy-mm-dd')."'";
		}
		if($db_type==2 || $db_type==1)
		{
			$sql_cond .= " and c.receive_date  between '".change_date_format($fromDate,'','','1')."' and '".change_date_format($toDate,'','','1')."'";
		}
	}

	if(trim($company)!="") $sql_cond .= " and c.company_id='$company'";
	if(trim($supplier)!=0) $sql_cond .= " and c.supplier_id='$supplier'";
	if(trim($buyer)!=0) $sql_cond .= " and c.buyer_id='$buyer'";
	if(trim($store_id)!=0) $sql_cond .= " and c.store_id='$store_id'";

	$sql ="SELECT a.id, a.qc_number, a.grn_number as recv_number, a.grn_id, a.supplier_id, c.challan_no, c.buyer_id, c.lc_no as lc_number, c.receive_date, sum(b.parking_quantity) as receive_qnty,c.receive_basis,
	case WHEN c.receive_basis=2 THEN c.booking_no ELSE null end as wo_number,case WHEN c.receive_basis=1 THEN c.booking_no ELSE null end as  pi_number  
	from  quarantine_parking_mst a, quarantine_parking_dtls b, inv_receive_master c
	where a.id = b.mst_id and a.entry_form=560 and a.GRN_ID=c.id and c.entry_form=559 $sql_cond
	and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
	group by a.id, a.qc_number, a.grn_number, a.grn_id, a.supplier_id, c.challan_no, c.buyer_id, c.lc_no, c.receive_date,c.receive_basis,c.booking_no";
	//echo $sql;
	$supplier_arr = return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$arr=array(1=>$supplier_arr,2=>$buyer_arr,6=>$receive_basis_arr);
	echo create_list_view("list_view", "GRN No., Supplier Name,Buyer Name, Challan No, LC No, Receive Date, Receive Basis,WO Number,PI Number ,Receive Qnty","110,130,130,120,120,80,80,120,80,80","1100","260",0, $sql , "js_set_value", "qc_number", "", 1, "0,supplier_id,buyer_id,0,0,0,receive_basis,0,0,0", $arr, "recv_number,supplier_id,buyer_id,challan_no,lc_number,receive_date,receive_basis,wo_number,pi_number,receive_qnty", "",'','0,0,0,0,0,3,0,0,0,1') ;
	exit();

}

if($action=="grn_popup")
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
                    <th width="130">Company Name</th>
                    <th width="120">Search By</th>
                    <th width="150" align="center" id="search_by_td_up">Enter GRN Number</th>
                    <th width="200">Date Range</th>
                    <th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px" class="formbutton"  /></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <?
							echo create_drop_down( "cbo_company_id", 130, "select id,company_name from lib_company comp where status_active=1 and is_deleted=0 and core_business not in(3) $company_cond order by company_name","id,company_name", 1, "-- Select Company --", $company, "" ); 
                        ?>
                    </td>
                    <td>
                        <?
                            $search_by = array(1=>'GRN No.',2=>'Challan No',3=>'WO No',4=>'PI No');
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
                        <input type="button" name="btn_show" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_grn_search_list_view', 'search_div', 'woven_finish_fabric_grn_roll_qc_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
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


if($action=="create_grn_search_list_view")
{
	$ex_data = explode("_",$data);
	$company = $ex_data[0];
	$txt_search_by = $ex_data[1];
	$txt_search_common = $ex_data[2];
	$fromDate = $ex_data[3];
	$toDate = $ex_data[4];

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

	$sql = "SELECT b.mst_id, a.recv_number, a.supplier_id, a.challan_no, a.booking_without_order, c.lc_number, a.receive_date, a.receive_basis, sum(b.parking_quantity) as receive_qnty, a.buyer_id, case WHEN a.receive_basis=2 THEN a.booking_no ELSE null end as wo_number,case WHEN a.receive_basis=1 THEN a.booking_no ELSE null end as  pi_number  from quarantine_parking_dtls b, inv_receive_master a left join com_btb_lc_master_details c on a.lc_no=c.id where a.id=b.mst_id and a.entry_form=559 and b.entry_form=559 and a.item_category=3 and b.item_category_id=3 and b.is_qc_pass=0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond group by b.mst_id, a.recv_number, a.supplier_id, a.challan_no, a.booking_without_order, c.lc_number, a.receive_date, a.receive_basis, a.buyer_id,a.booking_no order by a.recv_number desc";

	//echo $sql;

	$supplier_arr = return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$arr=array(1=>$supplier_arr,2=>$buyer_arr,6=>$receive_basis_arr);
	echo create_list_view("list_view", "MRR No, Supplier Name,Buyer Name, Challan No, LC No, Receive Date, Receive Basis,WO Number,PI Number ,Receive Qnty","110,130,130,120,120,80,80,120,80,80","1100","260",0, $sql , "js_set_value", "recv_number", "", 1, "0,supplier_id,buyer_id,0,0,0,receive_basis,0,0,0", $arr, "recv_number,supplier_id,buyer_id,challan_no,lc_number,receive_date,receive_basis,wo_number,pi_number,receive_qnty", "",'','0,0,0,0,0,3,0,0,0,1') ;
	exit();

}

if($action=="populate_data_from_data")
{
	$sql = "SELECT a.id, a.recv_number, a.company_id, a.receive_basis, a.booking_id, a.booking_no, a.location_id, a.challan_no, a.store_id,  a.supplier_id, a.exchange_rate, a.currency_id, a.buyer_id from inv_receive_master a where a.recv_number='$data' and a.entry_form=559 and a.status_active=1";

	$res = sql_select($sql);
	foreach($res as $row)
	{
		echo "$('#cbo_company_id').val(".$row[csf("company_id")].");\n";
		
		$supplier_td = create_drop_down( "cbo_supplier", 160, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='".$row[csf("company_id")]."' and b.party_type =9 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
		echo "document.getElementById('supplier').innerHTML = '".$supplier_td."';\n";
		echo "$('#cbo_supplier').val(".$row[csf("supplier_id")].");\n";


		$sql_location = "select id, location_name from lib_location where company_id='".$row[csf("company_id")]."' $PreviligLocationCond and status_active=1 and is_deleted=0 group by id, location_name order by location_name"; 
		$location_td = create_drop_down("cbo_location", 130, $sql_location, "id,location_name", 1, "--Select Location--", 0,"store_change(this.value)");
		echo "document.getElementById('location_td').innerHTML = '".$location_td."';\n"; 
		echo "$('#cbo_location').val(".$row[csf("location_id")].");\n";

		echo "store_change(".$row[csf("location_id")].");\n";
		echo "$('#cbo_store_name').val(".$row[csf("store_id")].");\n";

		echo "$('#hdn_grn_id').val(".$row[csf("id")].");\n";

 	}
	exit();
}

if($action=="populate_data_from_data_update")
{
	//$sql = "SELECT a.id, a.recv_number, a.company_id, a.receive_basis, a.booking_id, a.booking_no, a.location_id, a.challan_no, a.store_id,  a.supplier_id, a.exchange_rate, a.currency_id, a.buyer_id from inv_receive_master a where a.recv_number='$data' and a.entry_form=559 and a.status_active=1";

	$sql = "SELECT a.id, a.qc_number, a.company_id, a.receive_date, a.grn_number, a.grn_id, a.supplier_id, b.store_id, b.location_id from  quarantine_parking_mst a, inv_receive_master b where a.qc_number='$data' AND a.grn_id=b.ID and a.entry_form=560 and b.entry_form=559 and a.status_active=1";

	$res = sql_select($sql);
	foreach($res as $row)
	{
		echo "$('#cbo_company_id').val(".$row[csf("company_id")].");\n";
		
		$supplier_td = create_drop_down( "cbo_supplier", 160, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='".$row[csf("company_id")]."' and b.party_type =9 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name","id,supplier_name", 1, "-- Select --", 0, "",0 );
		echo "document.getElementById('supplier').innerHTML = '".$supplier_td."';\n";
		echo "$('#cbo_supplier').val(".$row[csf("supplier_id")].");\n";


		$sql_location = "select id, location_name from lib_location where company_id='".$row[csf("company_id")]."' $PreviligLocationCond and status_active=1 and is_deleted=0 group by id, location_name order by location_name"; 
		$location_td = create_drop_down("cbo_location", 130, $sql_location, "id,location_name", 1, "--Select Location--", 0,"store_change(this.value)");
		echo "document.getElementById('location_td').innerHTML = '".$location_td."';\n"; 
		echo "$('#cbo_location').val(".$row[csf("location_id")].");\n";

		echo "store_change(".$row[csf("location_id")].");\n";
		echo "$('#cbo_store_name').val(".$row[csf("store_id")].");\n";

		echo "$('#txt_grn_no').val('".$row[csf("grn_number")]."');\n";
		echo "$('#hdn_grn_id').val(".$row[csf("grn_id")].");\n";
		echo "$('#txt_receive_date').val('".change_date_format($row[csf("receive_date")])."');\n";
		echo "$('#update_id').val(".$row[csf("id")].");\n";

		
		

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
                	<tr bgcolor="<? echo $bgcolor; ?>" onClick="fn_fabric_descriptin_variable_check();put_data_dtls_part('<? echo $row[csf('rcv_mst_id')].'**'.$wopi."**". $row[csf('recv_number')]."**".$row[csf('pro_roll_dtls_id')]."**".$row[csf('transection_id')]."**".$row[csf('body_part_id')]."**".$row[csf('receive_basis')]."**".$row[csf('pi_wo_batch_no')];?>','child_form_input_data','requires/woven_finish_fabric_grn_roll_qc_controller')" style="cursor:pointer" >

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
		echo "load_room_rack_self_bin('requires/woven_finish_fabric_grn_roll_qc_controller', 'room','room_td', '".$row[csf('company_id')]."','".$row[csf('location_id')]."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."',this.value);\n";
		echo "$('#cbo_room').val(".$row[csf("room")].");\n";
		echo "load_room_rack_self_bin('requires/woven_finish_fabric_grn_roll_qc_controller', 'rack','rack_td', '".$row[csf('company_id')]."','".$row[csf('location_id')]."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."',this.value);\n";
		echo "$('#txt_rack').val(".$row[csf("rack")].");\n";
		echo "load_room_rack_self_bin('requires/woven_finish_fabric_grn_roll_qc_controller', 'shelf','shelf_td', '".$row[csf('company_id')]."','".$row[csf('location_id')]."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."',this.value);\n";
		echo "$('#txt_shelf').val(".$row[csf("self")].");\n";
		echo "load_room_rack_self_bin('requires/woven_finish_fabric_grn_roll_qc_controller', 'bin','bin_td', '".$row[csf('company_id')]."','".$row[csf('location_id')]."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."','".$row[csf('self')]."',this.value);\n";
		echo "$('#cbo_bin').val(".$row[csf("bin_box")].");\n";
		echo "$('#update_dtls_id').val(".$row[csf("id")].");\n";
		echo "$('#cbo_body_part').val(".$row[csf("body_part_id")].");\n";

		if($row[csf("receive_basis")]==2 || $row[csf("receive_basis")]==1)
		{
			echo "load_drop_down( 'requires/woven_finish_fabric_grn_roll_qc_controller', '".$row[csf("body_part_id")]."', 'load_drop_down_bodypart','body_part_td');\n";
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

	$width="800";
	?>

	<script>
		function fnc_close()
		{
			var save_string = ''; var total_qc_quantity=0; var total_reject_quantity=0;
			$("#tbl_list_search").find('tr').each(function ()
			{
				var txtPoId = $(this).find('input[name="txtPoId[]"]').val();
				var hdnBookingNo = $(this).find('input[name="hdnBookingNo[]"]').val();
				var txtRoll = $(this).find('input[name="txtRoll[]"]').val();
				var txtBarcodeNo = $(this).find('input[name="txtBarcodeNo[]"]').val();
				var txtManuRoll = $(this).find('input[name="txtManuRoll[]"]').val();
				var txtGreyQnty = $(this).find('input[name="txtGreyQnty[]"]').val();
				var txtQCQnty = $(this).find('input[name="txtQCQnty[]"]').val();
				var txtRejQnty = $(this).find('input[name="txtRejQnty[]"]').val();
				var txtRFId = $(this).find('input[name="txtRFId[]"]').val();
				var txtRollTableId = $(this).find('input[name="txtRollTableId[]"]').val();
				var qcRollID = $(this).find('input[name="qcRollID[]"]').val();
				
				//order +'**'+ booking_no +'**'+ roll_no+'**'+barcode+'**'+manual_roll+'**'+grn qnty+'**'+QC qnty+'**'+reject qnty+'**'+rfid+'**'+grn_roll_table_id+'**'+ qc_roll_table_id

				if(txtGreyQnty*1>0)
				{
					if (save_string == "") 
					{
						save_string = txtPoId + "**" + hdnBookingNo + "**" + txtRoll + "**" + txtBarcodeNo + "**" + txtManuRoll + "**" + txtGreyQnty + "**" + txtQCQnty + "**" + txtRejQnty + "**" + txtRFId + "**" + txtRollTableId + "**" + qcRollID;
					}
					else 
					{
						save_string += "," + txtPoId + "**" + hdnBookingNo + "**" + txtRoll + "**" + txtBarcodeNo + "**" + txtManuRoll + "**" + txtGreyQnty + "**" + txtQCQnty + "**" + txtRejQnty + "**" + txtRFId + "**" + txtRollTableId + "**" + qcRollID;
					}

					total_qc_quantity +=txtQCQnty*1;
					total_reject_quantity +=txtRejQnty*1;
				}
			});

			$('#save_string').val( save_string );
			$('#total_qc_quantity').val( total_qc_quantity );
			$('#total_reject_quantity').val( total_reject_quantity );

			parent.emailwindow.hide();
		}

		function fnc_calculate_total(i)
		{
			if(isNaN($('#txtQCQnty_'+i).val()*1))
			{
				$('#txtQCQnty_'+i).val("");
			}

			if(isNaN($('#txtRejQnty_'+i).val()*1))
			{
				$('#txtRejQnty_'+i).val("");
			}

			if($('#txtGreyQnty_'+i).val()*1 >= $('#txtQCQnty_'+i).val()*1 ) 
			{
				var rej_qty = $('#txtGreyQnty_'+i).val()*1 - $('#txtQCQnty_'+i).val()*1
				rej_qty = number_format(rej_qty,2,'.','');
				$('#txtRejQnty_'+i).val(rej_qty);
			}

 
			if($('#txtGreyQnty_'+i).val()*1 < $('#txtQCQnty_'+i).val()*1 + $('#txtRejQnty_'+i).val()*1)
			{
				alert("QC quantity and reject quantity can not greater than GRN quantity.");
				$('#txtQCQnty_'+i).val("");
				$('#txtRejQnty_'+i).val("");
				return;
			}
			//alert($('#txtGreyQnty_'+i).val()*1 +' < '+ $('#txtQCQnty_'+i).val()*1 + ' + ' + $('#txtRejQnty_'+i).val()*1);

			var total_qc_qnty = 0; var total_rej_qnty=0;
			$("#tbl_list_search").find('tr').each(function ()
			{
				var txtQCQnty = $(this).find('input[name="txtQCQnty[]"]').val();
				var txtRejQnty = $(this).find('input[name="txtRejQnty[]"]').val();
				var txtGreyQnty = $(this).find('input[name="txtGreyQnty[]"]').val();

				total_qc_qnty += txtQCQnty*1;
				total_rej_qnty += txtRejQnty*1;
			});
			total_qc_qnty = number_format(total_qc_qnty,2,'.','');
			total_rej_qnty = number_format(total_rej_qnty,2,'.','');
			$('#total_qc_qty').text( total_qc_qnty );
			$('#total_rej_qty').text( total_rej_qnty );
		}


    </script>

	</head>

	<body>
		<div align="center">

		<div style="margin-left:5px; margin-top:5px">
				<table style="float: left;" class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="<? echo $width-20; ?>">
					<thead>
						<th width="50">Sup. Roll No.</th>
						<th width="100">Barcode Number</th>
						<th width="100">Manual Roll NO</th>
						<th width="100">GRN Qty</th>
						<th width="100">QC Qty</th>
						<th width="100">Reject Qty</th>
						<th width="100">RFID</th>											
					</thead>
					<tbody id="tbl_list_search">
						<?
							//order +'**'+ booking_no +'**'+ roll_no+'**'+barcode+'**'+manual_roll+'**'+grn qnty+'**'+QC qnty+'**'+reject qnty+'**'+rfid+'**'+grn_roll_table_id+'**'+ qc_roll_table_id
							$i=1;
							$explSaveData = explode(",",$save_data);
							foreach($explSaveData as $val)
							{
								$order_data = explode("**",$val);
								$order_id=$order_data[0];
								$hdnBookingNo=$order_data[1];
								$roll_no=$order_data[2];
								$barcode_no=$order_data[3];
								$manual_roll_no=$order_data[4];
								$fin_qty=$order_data[5];
								$qc_qty=$order_data[6];
								$reject_qty=$order_data[7];
								$rf_id=$order_data[8];
								$roll_table_id=$order_data[9];
								$qc_roll_table_id=$order_data[10];
							

								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

								?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
									<td style="width:50px"><p><? echo $roll_no; ?></p></td>
									<td>
										<p style="width:80px"><? echo $barcode_no; ?></p>
									</td>
									<td  align="right">
										<input type="text" class="text_boxes_numeric" name="txtManuRoll[]" id="txtManuRoll_<? echo $i; ?>" style="width:80px" value="<? echo $manual_roll_no;?>">
									</td>
									<td align="center">
										<input type="text" name="txtGreyQnty[]" id="txtGreyQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $fin_qty; ?>" readonly disabled/>
									</td>
									<td align="center">
										<input type="text" name="txtQCQnty[]" id="txtQCQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $qc_qty; ?>" onKeyUp="fnc_calculate_total(<? echo $i;?>);"/>
									</td>
									<td align="center">
										<input type="text" name="txtRejQnty[]" id="txtRejQnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $reject_qty; ?>" onKeyUp="fnc_calculate_total(<? echo $i;?>);" readonly/>
									</td>

									<td align="center">
										<input type="text" name="txtRFId[]" id="txtRFId_<? echo $i; ?>" class="text_boxes_numeric" style="width:80px" value="<? echo $rf_id; ?>" readonly disabled>

										<input type="hidden" name="txtPoId[]" id="txtPoId_<? echo $i; ?>" value="<? echo $order_id; ?>">
										<input type="hidden" name="hdnBookingNo[]" id="hdnBookingNo_<? echo $i; ?>" value="<? echo $hdnBookingNo; ?>">
										<input type="hidden" name="txtBarcodeNo[]" id="txtBarcodeNo_<? echo $i; ?>" value="<? echo $barcode_no; ?>">
										<input type="hidden" name="txtRoll[]" id="txtRoll_<? echo $i; ?>" value="<? echo $roll_no; ?>">
										<input type="hidden" name="txtRollTableId[]" id="txtRollTableId_<? echo $i; ?>" value="<? echo $roll_table_id; ?>">
										<input type="hidden" name="qcRollID[]" id="qcRollID_<? echo $i; ?>" value="<? echo $qc_roll_table_id; ?>">
									</td>
								</tr>
							<?
							$tot_grn_qty +=$fin_qty;
							$tot_qc_qty +=$qc_qty;
							$tot_reject_qty +=$reject_qty;
							$i++;
							}
						?>
					</tbody>
					<tfoot>
						<th width="50" colspan="3" width="250">Total :</th>
						<th width="100"><? echo $tot_grn_qty; ?></th>
						<th width="100" id="total_qc_qty"><? echo $tot_qc_qty; ?></th>
						<th width="100" id="total_rej_qty"><? echo $tot_reject_qty; ?></th>
						<th width="100">&nbsp;</th>	
					</tfoot>
				</table>
				<table width="<? echo $width-50; ?>">
					<tr>
						<td align="center" >
							<input type="button" name="close" class="formbutton" value="Close" id="main_close" onClick="fnc_close();" style="width:100px" />
							
							<input type="hidden" name="save_string" id="save_string" class="text_boxes" value="">
							<input type="hidden" name="total_qc_quantity" id="total_qc_quantity" class="text_boxes" value="">
							<input type="hidden" name="total_reject_quantity" id="total_reject_quantity" class="text_boxes" value="">
						</td>
					</tr>
				</table>
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
			show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('cbo_buyer_name').value+'_'+'<? echo $all_po_id; ?>', 'create_po_search_list_view', 'search_div', 'woven_finish_fabric_grn_roll_qc_controller', 'setFilterGrid(\'tbl_list_search\',-1);hidden_field_reset();');
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
				var response=return_global_ajax_value( data, 'roll_duplication_check', '', 'woven_finish_fabric_grn_roll_qc_controller');
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
			show_list_view ( po_id+'_'+'1'+'_'+'<? echo $dtls_id; ?>'+'_'+'<? echo $roll_maintained; ?>'+'_'+'<? echo $save_data; ?>'+'_'+'<? echo $prev_distribution_method; ?>'+'_'+'<? echo $receive_basis; ?>'+'_'+'<? echo $txt_deleted_id; ?>'+'_'+'<? echo $hidden_pi_id; ?>'+'_'+'<? echo $update_hdn_transaction_id; ?>', 'po_popup', 'search_div', 'woven_finish_fabric_grn_roll_qc_controller', '');   
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view (document.getElementById('txt_construction').value+'**'+document.getElementById('txt_gsm_weight').value+'**'+document.getElementById('txt_rd').value, 'fabric_description_popup_search_list_view', 'search_div', 'woven_finish_fabric_grn_roll_qc_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
//print button 2
if($action=="gwoven_finish_fabric_receive_print_2")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	$sql="select id, recv_number, receive_basis, booking_id, receive_date, challan_no, store_id, audit_by, audit_date, is_audited, supplier_id, lc_no, currency_id, exchange_rate, source, buyer_id from inv_receive_master where id='$data[1]' and company_id='$data[0]'";
	//echo $sql;die;
	$dataArray=sql_select($sql);
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$store_library=return_library_array( "select id, store_name from  lib_store_location", "id", "store_name"  );
	$country_arr=return_library_array( "select id, country_name from  lib_country", "id", "country_name"  );
	$supplier_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name"  );
	$wo_arr=return_library_array( "select id, booking_no from  wo_booking_mst", "id", "booking_no"  );
	$pi_arr=return_library_array( "select id, pi_number from  com_pi_master_details", "id", "pi_number"  );
	$lc_arr=return_library_array( "select id, lc_number from  com_btb_lc_master_details", "id", "lc_number"  );
    $user_name 		= return_library_array("select id,user_full_name from user_passwd", "id", "user_full_name");

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
	            <td colspan="6" align="center" style="font-size:x-large"><strong><u><? echo $data[3]; ?> Report</u></strong></td>
	        </tr>
	    </table>
	    <br>
	    <table align="center" cellspacing="0" width="800"  border="1" rules="all" class="">
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
	            <td><strong>Buyer:</strong></td> <td width="175px"><? echo $buyer_library[$dataArray[0][csf('buyer_id')]]; ?></td>
	        </tr>
	    </table>
	        <br>
	    <div style="width:100%;">
	    <table align="center" cellspacing="0" width="1000"  border="1" rules="all" class="rpt_table" >
	        <thead bgcolor="#dddddd" align="center">
	            <th width="30">SL</th>
	            <th width="260" >Fabric Details</th>
	            <th width="50" >Job No</th>
	            <th width="50" >Style Ref No</th>
	            <th width="50" >Order No</th>
	            <th width="40" >Width</th>
	            <th width="50" >Weight</th>
	            <th width="50" >Batch/Lot</th>
	            <th width="50" >UOM</th>
	            <th width="60" >Recv. Qnty.</th>
	            <th width="60" >Rate</th>
	            <th width="80" >Amount</th>
	            <th width="100" >Book Currency</th>
	            <th width="100" >Remarks</th>
	        </thead>
	        <tbody>
	<?

	  //$sql_dtls = "select a.recv_number, a.receive_basis, b.id, b.pi_wo_batch_no, c.product_name_details, c.dia_width, c.weight, b.order_uom, b.order_qnty, b.order_rate, b.order_ile_cost, b.order_amount, b.cons_amount,b.batch_lot, c.detarmination_id, h.job_no,e.style_ref_no,d.po_break_down_id from inv_receive_master a, inv_transaction b, product_details_master c,wo_booking_mst d,wo_po_details_master e,COM_PI_ITEM_DETAILS g,WO_BOOKING_DTLS h where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and b.item_category=3 and a.entry_form=17 and a.id='$data[1]' and a.booking_id=d.id and d.job_no=e.job_no and d.status_active=1 and d.is_deleted=0 and g.pi_id='$data[2]' and g.work_order_dtls_id=h.id group by  a.recv_number, a.receive_basis, b.id, b.pi_wo_batch_no, c.product_name_details, c.dia_width, c.weight, b.order_uom, b.order_qnty, b.order_rate, b.order_ile_cost, b.order_amount, b.cons_amount,b.batch_lot, c.weight, c.detarmination_id, h.job_no,e.style_ref_no,d.po_break_down_id";

	  	$po_number=return_library_array( "select id,po_number from wo_po_break_down", "id", "po_number");
	  	$po_array=array();
		//$job_prefix_num=array();
		$sql_po= sql_select("select a.po_break_down_id from wo_booking_mst  a,com_pi_item_details b where  b.pi_id='$data[2]' and b.work_order_id=a.id and a.status_active=1 and a.is_deleted=0");
		foreach($sql_po as $row){
			$po_id=explode(",",$row[csf("po_break_down_id")]);
			//$job_prefix_arr=explode("-",$row[csf("job_no")]);
			$po_number_string="";
			foreach($po_id as $key=> $value ){
				$po_number_string.=$po_number[$value].",";
			}
			$po_array[$row[csf("po_break_down_id")]]=rtrim($po_number_string,",");
			//$job_prefix_num[$row[csf("job_no")]]=ltrim($job_prefix_arr[2],0);
		}
		$sql_dtls = "SELECT a.job_no,a.po_break_down_id,d.product_name_details, d.dia_width, d.weight,d.detarmination_id,f.id, f.pi_wo_batch_no,f.order_uom, f.order_rate, f.order_ile_cost, f.order_amount, f.remarks, f.cons_amount ,f.batch_lot,e.recv_number, e.receive_basis ,g.po_breakdown_id from wo_booking_mst a,com_pi_item_details b, product_details_master d,inv_receive_master e, inv_transaction f,order_wise_pro_details g where b.pi_id='$data[2]' and b.work_order_id=a.id  and e.id=f.mst_id and f.prod_id=d.id and f.id=g.trans_id and f.transaction_type=1 and f.item_category=3 and e.entry_form=17 and e.id='$data[1]' and g.status_active=1 and g.is_deleted=0 group by a.job_no,a.po_break_down_id,d.product_name_details, d.dia_width, d.weight,d.detarmination_id,f.id, f.pi_wo_batch_no,f.order_uom, f.order_rate, f.order_ile_cost, f.order_amount, f.remarks, f.cons_amount ,f.batch_lot,e.recv_number, e.receive_basis,g.po_breakdown_id"; 
		// echo $sql_dtls;
		//c.style_ref_no,wo_po_details_master c,and a.job_no=c.job_no
		$sql_result= sql_select($sql_dtls); $poIds="";
		foreach($sql_result as $row)
		{
			$poIds.=$row[csf("po_breakdown_id")].",";
		}
		$poIds=chop($poIds,",");
	 	$sql_po=sql_select("select a.id,a.job_no_mst,a.po_number,b.style_ref_no from wo_po_break_down a,wo_po_details_master b where a.job_no_mst=b.job_no and a.status_active=1 and a.is_deleted=0 and  b.status_active=1 and b.is_deleted=0 and a.id in($poIds)");
		foreach($sql_po as $row)
		{
			$po_dtls_data_arr[$row[csf("id")]]["po_number"]=$row[csf("po_number")];
			$po_dtls_data_arr[$row[csf("id")]]["job_no_mst"]=$row[csf("job_no_mst")];
			$po_dtls_data_arr[$row[csf("id")]]["style_ref_no"]=$row[csf("style_ref_no")];
		}

	 	$sql_poRecvQnyt=sql_select("SELECT f.id, f.pi_wo_batch_no,f.order_uom, f.order_rate, f.order_ile_cost, f.order_amount, f.remarks, f.cons_amount ,f.batch_lot,e.recv_number, e.receive_basis ,g.po_breakdown_id,sum(g.quantity) as quantity from inv_receive_master e, inv_transaction f,order_wise_pro_details g  where  e.id=f.mst_id and f.id=g.trans_id and f.transaction_type=1 and f.item_category=3 and e.entry_form=17 and e.id='$data[1]' and g.status_active=1 and g.is_deleted=0  and  g.po_breakdown_id in($poIds) and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0  group by  f.id, f.pi_wo_batch_no,f.order_uom, f.order_rate, f.order_ile_cost, f.order_amount, f.remarks, f.cons_amount ,f.batch_lot,e.recv_number, e.receive_basis ,g.po_breakdown_id");
	 	// $sql_poRecvQnyt=sql_select("select g.po_breakdown_id,sum(g.quantity) as quantity from inv_receive_master e, inv_transaction f,order_wise_pro_details g  where  e.id=f.mst_id and f.id=g.trans_id and f.transaction_type=1 and f.item_category=3 and e.entry_form=17 and e.id='$data[1]' and g.status_active=1 and g.is_deleted=0  and  g.po_breakdown_id in($poIds) and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0  group by  g.po_breakdown_id");
		foreach($sql_poRecvQnyt as $row)
		{
			$poQntry_recv_arr[$row[csf("po_breakdown_id")]][$row[csf("id")]][$row[csf("order_uom")]][$row[csf("order_rate")]][$row[csf("order_ile_cost")]][$row[csf("order_amount")]][$row[csf("cons_amount")]][$row[csf("batch_lot")]][$row[csf("recv_number")]][$row[csf("receive_basis")]][$row[csf("pi_wo_batch_no")]]["quantity"]=$row[csf("quantity")];
			// $poQntry_recv_arr[$row[csf("po_breakdown_id")]]["quantity"]=$row[csf("quantity")];
		}


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
			$totalQnty +=$poQntry_recv_arr[$row[csf("po_breakdown_id")]]["quantity"];
			$poRcvQnty = $poQntry_recv_arr[$row[csf("po_breakdown_id")]][$row[csf("id")]][$row[csf("order_uom")]][$row[csf("order_rate")]][$row[csf("order_ile_cost")]][$row[csf("order_amount")]][$row[csf("cons_amount")]][$row[csf("batch_lot")]][$row[csf("recv_number")]][$row[csf("receive_basis")]][$row[csf("pi_wo_batch_no")]]["quantity"];


			?>
				<tr bgcolor="<? echo $bgcolor; ?>">
	                <td align="center"><? echo $i; ?></td>
	                <td><? echo $row[csf("product_name_details")]; ?></td>

	                <td><?  echo $po_dtls_data_arr[$row[csf("po_breakdown_id")]]["job_no_mst"]; ?></td>
	                <td><? echo $po_dtls_data_arr[$row[csf("po_breakdown_id")]]["style_ref_no"]; ?></td>
	                <td style="word-wrap: break-word;word-break: break-all; width:170px;"><? echo $po_dtls_data_arr[$row[csf("po_breakdown_id")]]["po_number"];//$po_array[$row[csf("po_break_down_id")]]; ?></td>

	                <td><? echo $row[csf("dia_width")]; ?></td>
	                <td><? echo $row[csf("weight")]; ?></td>
	                <td align="center"><? echo $row[csf("batch_lot")]; ?></td>
	                <td align="center"><? echo $unit_of_measurement[$row[csf("order_uom")]]; ?></td>
	                <td align="right"><? echo number_format($poRcvQnty,2); ?></td>
	                <td align="right"><? echo $row[csf("order_rate")]; ?></td>
	                <td align="right"><? $recvAmount=$poRcvQnty*$row[csf("order_rate")]; echo number_format($recvAmount,2);?></td>
	                <td align="right"><? echo number_format($recvAmount*$dataArray[0][csf('exchange_rate')],2); ?></td>
	                <td align="center"><? echo $row[csf("remarks")]; ?></td>
				</tr>
		<?
		$totalbookCurr +=$recvAmount*$dataArray[0][csf('exchange_rate')];
		$totalAmount +=$recvAmount;
		$totalRcvQnty +=$poRcvQnty;
		$i++;
	    } ?>
	        </tbody>
	        <tfoot>
	            <tr>
	                <td colspan="9" align="right"><strong>Total :</strong></td>
	                <td align="right"><?php echo number_format($totalRcvQnty,2); ?></td>
	                <td align="right" colspan="2"><?php echo number_format($totalAmount,2); ?></td>
	                <td align="right"><?php echo number_format($totalbookCurr,2); ?></td>
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

}


//PRINT BUTTON 3
if($action=="gwoven_finish_fabric_receive_print_3")
{
    extract($_REQUEST);
	$data=explode('*',$data);
	//print_r ($data);
	$sql="select id, recv_number, receive_basis, booking_id,booking_without_order, receive_date, challan_no, store_id, audit_by, audit_date, is_audited, supplier_id, lc_no, currency_id, exchange_rate, source from inv_receive_master where id='$data[1]' and company_id='$data[0]'";
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
	            <td colspan="6" align="center" style="font-size:x-large"><strong><u><? echo $data[3]; ?></u></strong></td>
	        </tr>
	    </table>
	        <br>
	        <table cellspacing="0" width="1100"  border="1" rules="all" class=""  style="margin-bottom: 10px;">
	        <tr>
	        	<td width="120"><strong>MRR ID :</strong></td><td width="175px"><? echo $dataArray[0][csf('recv_number')]; ?></td>
	            <td width="130"><strong>Receive Basis:</strong></td> <td width="175px"><? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
	            <td width="125"><strong>WO/PI</strong></td><td width="175px"><strong><? if ($dataArray[0][csf('receive_basis')]==2) echo $wo_arr[$dataArray[0][csf('booking_id')]]; else if ($dataArray[0][csf('receive_basis')]==1) echo $pi_arr[$dataArray[0][csf('booking_id')]]; ?></strong></td>
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
	    <table cellspacing="0" width="1300"  border="1" rules="all" class="rpt_table" >
	        <thead bgcolor="#dddddd" align="center">
	            <th width="30">SL</th>
	            <th width="70" >Style Ref.</th>
	            <th width="250" >Fabric Description</th>

	            <th width="70" >Fabric Ref</th>
	            <th width="70" >RD NO</th>
	            <th width="50" >Weight</th>
	            <th width="50" >Weight Type</th>
	            <th width="40" >Width</th>
	            <th width="70" >Cutable Width</th>

	            <th width="70" >Color</th>
	            <th width="70" >Batch/Lot</th>
	            <th width="50" >UOM</th>
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
			$sql_dtls = "select a.recv_number, a.receive_basis,a.booking_without_order,a.exchange_rate, b.id, b.pi_wo_batch_no,b.remarks,b.fabric_ref,b.rd_no,b.cutable_width,b.weight_type,c.color, c.product_name_details, c.dia_width, b.order_uom, sum(d.quantity) as order_qnty, b.order_rate, b.order_ile_cost, b.order_amount, b.cons_amount,b.batch_lot,d.no_of_roll as roll_no, c.weight, c.detarmination_id, listagg(cast( d.po_breakdown_id as varchar(4000)),',') within group (order by d.po_breakdown_id) as po_breakdown_id, f.style_ref_no  from inv_receive_master a,pro_finish_fabric_rcv_dtls x, inv_transaction b,  product_details_master c,order_wise_pro_details d, wo_po_break_down e,wo_po_details_master f where  a.id=x.mst_id and x.trans_id=b.id and a.id=b.mst_id and b.prod_id=c.id and d.po_breakdown_id=e.id and e.job_no_mst=f.job_no and b.transaction_type=1 and b.id=d.trans_id and b.item_category=3 and a.entry_form=17 and a.id='$data[1]' group by a.recv_number, a.receive_basis,a.booking_without_order,a.exchange_rate, b.id, b.pi_wo_batch_no,b.remarks,b.fabric_ref,b.rd_no,b.cutable_width,b.weight_type,c.color, c.product_name_details, c.dia_width, b.order_uom, b.order_rate, b.order_ile_cost, b.order_amount, b.cons_amount,b.batch_lot, c.weight,d.no_of_roll, c.detarmination_id, f.style_ref_no ";
		}
		else{
				$sql_dtls = " select a.recv_number,a.booking_id, a.receive_basis,a.booking_without_order, b.id, b.pi_wo_batch_no,b.remarks,c.color, c.product_name_details, c.dia_width, c.weight, b.order_uom, sum(b.order_qnty) as order_qnty, b.order_rate, b.order_ile_cost, b.order_amount, b.cons_amount,b.batch_lot,x.no_of_roll as roll_no,b.fabric_ref,b.rd_no,b.cutable_width,b.weight_type, c.weight, c.detarmination_id from inv_receive_master a,pro_finish_fabric_rcv_dtls x, inv_transaction b, product_details_master c where a.id=x.mst_id and x.trans_id=b.id and a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and b.item_category=3 and a.entry_form=17 and a.id='$data[1]' group by a.recv_number,a.booking_id, a.receive_basis,a.booking_without_order, b.id, b.pi_wo_batch_no,b.remarks,c.color, c.product_name_details, c.dia_width, c.weight, b.order_uom, b.order_rate, b.order_ile_cost, b.order_amount, b.cons_amount,b.batch_lot,x.no_of_roll,b.fabric_ref,b.rd_no,b.cutable_width,b.weight_type, c.weight, c.detarmination_id";

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
			//$style_ref = $po_number_details[$row[csf('po_breakdown_id')]]['style_ref_no'];
			$style_ref = $row[csf("style_ref_no")];
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
                <td><? echo $style_ref; ?></td>
                <td><? echo $row[csf("product_name_details")]; ?></td>
                <td align="center"><? echo $row[csf("fabric_ref")]; ?></td>
                <td align="center" ><? echo $row[csf("rd_no")]; ?></td>
                <td align="center"><? echo $row[csf("weight")]; ?></td>
                <td align="center"><? echo $fabric_weight_type[$row[csf("weight_type")]]; ?></td>
                <td align="center"><? echo $row[csf("dia_width")]; ?></td>
                <td align="center"><? echo $row[csf("cutable_width")]; ?></td>

                <td><? echo $color; ?></td>
                <td align="center"><? echo $row[csf("batch_lot")]; ?></td>
                <td align="center"><? echo $unit_of_measurement[$row[csf("order_uom")]]; ?></td>
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
                <td align="right"><? echo $row[csf("remarks")]; ?></td>
            </tr>
           	<?
           	$totalAmuntNew+=$amout_cal;
			$i++;
        	}
			?>
        </tbody>

        <tfoot>
            <tr>
                <td colspan="12" align="right"><strong>Total :</strong></td>
                <td align="right"><?php echo $total_roll_no; ?></td>
                <td align="right"><?php echo number_format($totalrollQty, 2); ?></td>
                <td></td>
                <!-- <td align="right"><?php //echo number_format($totalQnty, 2); ?></td> -->
                <td align="right"><? echo number_format($totalAmuntNew,2); ?></td>
                <!-- <td align="right"><?php //echo number_format($totalAmount, 2); ?></td> -->
                <td align="right"><?php //echo $totalbookCurr; ?></td>

            </tr>
        </tfoot>

      </table>
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
?>
