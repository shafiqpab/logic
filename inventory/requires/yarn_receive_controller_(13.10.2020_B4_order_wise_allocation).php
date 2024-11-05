<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if ($_SESSION['logic_erp']['user_id'] == "") { 
	header("location:login.php");
	die;
}
$permission = $_SESSION['page_permission'];

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
$user_store_ids = $_SESSION['logic_erp']['store_location_id'];
$user_supplier_ids = trim($_SESSION['logic_erp']['supplier_id']);
//--------------------------------------------------------------------------------------------

if($action=="upto_variable_settings")
{	
    $sql =  sql_select("select store_method from variable_settings_inventory where company_name = $data and item_category_id=1 and variable_list=21 and status_active=1 and is_deleted=0 and rack_balance=1");
	$return_data="";
    if(count($sql)>0)
	{
		$return_data=$sql[0][csf('store_method')];
	}
	else
	{ 
		$return_data=0; 
	}
	
	echo $return_data;
	die;
}

if ($action == "check_conversion_rate") {
	$data = explode("**", $data);
	if ($db_type == 0) {
		$conversion_date = change_date_format($data[1], "Y-m-d", "-", 1);
	} else {
		$conversion_date = change_date_format($data[1], "d-M-y", "-", 1);
	}
	$exchange_rate = set_conversion_rate($data[0], $conversion_date,$data[2]);
	echo $exchange_rate;
	exit();
}

//load drop down supplier//
if ($action == "load_drop_down_supplier")
{
	if($user_supplier_ids!="")
	{
		$user_supplier_cond = "and c.id in ($user_supplier_ids)";
	}else {
		$user_supplier_cond = "";
	}
	echo create_drop_down("cbo_supplier", 170, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data' $user_supplier_cond and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 0);
	exit();
}

if ($action == "load_drop_down_buyer") {
	//$data=explode("_",$data);
	echo create_drop_down("cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "", '');
	exit();
}

//load drop down party//
if ($action == "load_drop_down_party")
{
	echo create_drop_down("cbo_party", 170, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data' and b.party_type=91 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 0);
	exit();
}

if ($action == "load_drop_down_company_from_eheck_wo_paymode")
{
	$data=explode("_",$data);

	if($data[4]==94)
	{
		$service_type_cond = "and b.service_type in(2,12,15,38,46,50,51)";
	}else{
		$service_type_cond ="";
	}

	if($data[3]==3 || $data[3]==5)
	{
		echo create_drop_down("cbo_supplier", 170, "select a.id, a.company_name from lib_company a,wo_yarn_dyeing_mst b where a.id=b.supplier_id and b.ydw_no='$data[2]' and b.pay_mode=$data[3] $service_type_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.company_name","id,company_name", 0, "-- Select --", 1, "", 0);
	}else{
		//and b.entry_form=$data[4]
		echo create_drop_down("cbo_supplier", 170, "select a.id, a.supplier_name from lib_supplier a,wo_yarn_dyeing_mst b where a.id=b.supplier_id and b.ydw_no='$data[2]' and b.pay_mode=$data[3] $service_type_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 order by a.supplier_name","id,supplier_name", 0, "-- Select --", 1, "", 0);
	}

	exit();
}

if ($action == "load_drop_down_supplier_from_issue") {
	echo create_drop_down("cbo_supplier", 170, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c, inv_issue_master d where c.id=b.supplier_id and a.supplier_id = b.supplier_id and c.id=d.knit_dye_company and d.knit_dye_source=3 and d.issue_purpose in(15,50,51) and d.entry_form=3 and a.tag_company='$data' and b.party_type in(2,93,94) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 0);
	exit();
}


if ($action=="load_room_rack_self_bin")
{
	$explodeData = explode('*', $data);
	$explodeData[11] = 'storeUpdateUptoDisable()';
	$data=implode('*', $explodeData);
	load_room_rack_self_bin("requires/yarn_receive_controller",$data);
}

//load drop down color
if ($action == "load_drop_down_color") {
	if ($data == "") {
		if ($db_type == 0) $color_cond = " and color_name!=''"; else $color_cond = " and color_name IS NOT NULL";
		$sql = "select id,color_name from lib_color where status_active=1 and is_deleted=0 $color_cond order by color_name";
	} else {
		$sql = "select a.id, a.color_name from lib_color a, wo_yarn_dyeing_dtls b where a.id=b.yarn_color and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and b.mst_id='$data' group by a.id, a.color_name order by a.color_name";
	}

	echo create_drop_down("cbo_color", 110, $sql, "id,color_name", 1, "--Select--", 0, "", 0);

	echo '<input type="button" name="btn_color" id="btn_color" class="formbuttonplasminus"  style="width:20px" onClick="fn_color_new(this.id)" value="N" />';
	exit();
}

if ($action == "load_drop_down_color2") {
	
	if ($data == 16) {
		if ($db_type == 0) $color_cond = " and color_name!=''"; else $color_cond = " and color_name IS NOT NULL";
		$sql = "select id, color_name from lib_color where status_active=1 and is_deleted=0 and grey_color=1 $color_cond order by color_name";		
	} else {
		if ($db_type == 0) $color_cond = " and color_name!=''"; else $color_cond = " and color_name IS NOT NULL";
		$sql = "select id, color_name from lib_color where status_active=1 and is_deleted=0 $color_cond order by color_name";
	}

	echo create_drop_down("cbo_color", 110, $sql, "id,color_name", 1, "--Select--", 0, "", 0);

	echo '<input type="button" name="btn_color" id="btn_color" class="formbuttonplasminus"  style="width:20px" onClick="fn_color_new(this.id)" value="N" />';
	exit();
}


//load drop down composition
if ($action == "load_drop_down_composition") // not used
{
	if ($data == 1)//new if
	{
		echo create_drop_down("cbocomposition1", 80, $composition, "", 1, "-- Select --", "", "", $disabled, "");
		echo '<input type="text" id="percentage1" name="percentage1" class="text_boxes_numeric" style="width:40px" maxlength="3" placeholder="%" />';
		echo create_drop_down("cbocomposition2", 80, $composition, "", 1, "-- Select --", "", "", $disabled, "");
		echo '<input type="text" id="percentage2" name="percentage2" class="text_boxes_numeric" style="width:40px" maxlength="3" placeholder="%" />';
		echo '<input type="button" class="formbutton" name="btn_composition" id="btn_composition" width="15" onClick="fn_comp_new(this.id)" value="F" />';
	} else {
		$sql = sql_select("select id,composition1,percentage1,composition2,percentage2 from lib_composition where is_deleted=0 AND status_active=1");
		$arr = array();
		foreach ($sql as $row) {
			if ($row[csf("composition1")] == 0) {
				$row[csf("composition1")] = "";
				$row[csf("percentage1")] = "";
			}
			if ($row[csf("composition2")] == 0) {
				$row[csf("composition2")] = "";
				$row[csf("percentage2")] = "";
			}
			$arr[$row[csf("id")]] = $composition[$row[csf("composition1")]] . " " . $row[csf("percentage1")] . " " . $composition[$row[csf("composition2")]] . " " . $row[csf("percentage2")];
		}

		echo create_drop_down("cbo_composition", 110, $arr, "", 1, "--Select--", 0, "", 0);
		echo '<input type="button" class="formbutton" name="btn_composition" id="btn_composition" width="15" onClick="fn_comp_new(this.id)" value="N" />';
	}
	exit();
}


// wo/pi popup here----------------------//
if ($action == "wopi_popup") {
	echo load_html_head_contents("Popup Info", "../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>

	<script>
		function js_set_value(str) {
			var splitData = str.split("_");
            $("#hidden_tbl_id").val(splitData[0]); // wo/pi id
            $("#hidden_wopi_number").val(splitData[1]); // wo/pi number
            $("#hidden_paymode").val(splitData[2]); // paymode
            $("#hidden_entry_form").val(splitData[3]); // entry form
            if(splitData[4]==1)
            {
            	alert("This PI Already Closed");return;
            }
            parent.emailwindow.hide();
        }

        function fn_supplier(company_id) {
        	var page_link = 'yarn_receive_controller.php?action=supplier_popup&company_id=' + company_id;
        	var title = "Supplier Info";
        	emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px, height=300px, center=1, resize=0, scrolling=0', '../')
        	emailwindow.onclose = function () {
        		var theform = this.contentDoc.forms[0];
        		var suplier_id = this.contentDoc.getElementById("hidden_supplier_id").value;
        		var suplier_name = this.contentDoc.getElementById("hidden_supplier_name").value;

        		$('#txt_supplier_id').val(suplier_id);
        		$('#txt_supplier_no').val(suplier_name);
        	}
        }

    </script>

</head>

<body>
	<div align="center" style="width:100%;">
		<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
			<table width="900" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
				<thead>
                <!--<tr>
						<td colspan="4" align="center"><?// echo create_drop_down( "cbo_string_search_type", 130, $string_search_type,'', 1, "-- Searching Type --" );
				?></td>
			</tr>-->
			<tr>
				<th width="150">PI No</th>
				<th width="150">Lc No</th>
				<th width="150">WO No</th>
				<th width="150">Supplier</th>
				<th width="200">Date Range</th>
				<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px"
					class="formbutton"/></th>
				</tr>
			</thead>
			<tbody>
				<tr class="general">
					<td style="display:none">
						<?
						echo create_drop_down("cbo_search_by", 170, $receive_basis_arr, "", 1, "--Select--", $receive_basis, "", 1);
						?>
					</td>
					<td>
						<input type="text" style="width:130px" class="text_boxes" name="txt_pi_no" id="txt_pi_no"
						placeholder="write" <?php echo ($receive_basis == 1) ? "" : "disabled='disabled'"; ?> />
					</td>
					<td>
						<input type="text" style="width:130px" class="text_boxes" name="txt_lc_no" id="txt_lc_no"
						<?php echo ($receive_basis == 1) ? "" : "disabled='disabled'"; ?> placeholder="write"/>
					</td>
					<td>
						<input type="text" style="width:130px" class="text_boxes" name="txt_wo_no" id="txt_wo_no"
						<?php echo ($receive_basis == 2) ? "" : "disabled='disabled'"; ?> placeholder="write"/>
					</td>
					<td>
						<input type="text" style="width:130px" class="text_boxes" name="txt_supplier_no"
						id="txt_supplier_no" placeholder="Browse"
						onDblClick="fn_supplier(<? echo $company; ?>)"/>
						<input type="hidden" name="txt_supplier_id" id="txt_supplier_id"/>
					</td>
					<td align="center">
						<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"
						placeholder="From Date"/>
						<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"
						placeholder="To Date"/>
					</td>
					<td align="center">
						<input type="button" name="btn_show" class="formbutton" value="Show"
						onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_pi_no').value+'_'+document.getElementById('txt_lc_no').value+'_'+document.getElementById('txt_wo_no').value+'_'+document.getElementById('txt_supplier_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_<? echo $company; ?>'+'_<? echo $receive_purpose; ?>'+'_<? echo $receive_basis; ?>', 'create_wopi_search_list_view', 'search_div', 'yarn_receive_controller', 'setFilterGrid(\'list_view\',-1)')"
						style="width:100px;"/>
					</td>
				</tr>
				<tr>
					<td align="center" height="40" valign="middle" colspan="6">
						<? echo load_month_buttons(1); ?>
						<!-- Hidden field here -->
						<input type="hidden" id="hidden_tbl_id" value=""/>
						<input type="hidden" id="hidden_wopi_number" value="hidden_wopi_number"/>
						<input type="hidden" id="hidden_paymode" value="hidden_paymode"/>
						<input type="hidden" id="hidden_entry_form" value="hidden_entry_form"/>
						<!-- -END -->
					</td>
				</tr>
			</tbody>
		</tr>
	</table>
	<div align="center" style="margin-top:5px" valign="top" id="search_div"></div>
</form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit;
}

//after select wo/pi number get form data here---------------------------//
if ($action == "create_wopi_search_list_view") {

	$ex_data = explode("_", $data);
	$txt_search_by = trim($ex_data[0]);
	$txt_pi_no = trim($ex_data[1]);
	$txt_lc_no = trim($ex_data[2]);
	$txt_wo_no = trim($ex_data[3]);
	$txt_supplier_id = trim($ex_data[4]);
	$txt_date_from = trim($ex_data[5]);
	$txt_date_to = trim($ex_data[6]);
	$company = trim($ex_data[7]);
	$receive_purpose = trim($ex_data[8]);
	$receive_basis = trim($ex_data[9]);

	if ($txt_search_by == 1) {
		$sql_cond = "";
		if ($txt_pi_no != "") $sql_cond .= " and a.pi_number like '%$txt_pi_no%'";
		if ($txt_lc_no != "") $sql_cond .= " and c.lc_number like '%$txt_lc_no%'";
		if ($txt_supplier_id != "") $sql_cond .= " and a.supplier_id=$txt_supplier_id";
		if ($txt_date_from != "" && $txt_date_to != "") {
			if ($db_type == 0) {
				$sql_cond .= " and a.pi_date between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
			} else {
				$sql_cond .= " and a.pi_date between '" . change_date_format($txt_date_from, '', '', 1) . "' and '" . change_date_format($txt_date_to, '', '', 1) . "'";
			}
		}

		$approval_status_cond="";
		if($db_type==0)
		{
			$approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format(date('d-m-Y'),'yyyy-mm-dd')."' and company_id='$company')) and page_id=18 and status_active=1 and is_deleted=0";
		}
		else
		{
			$approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format(date('d-m-Y'), "", "",1)."' and company_id='$company')) and page_id=18 and status_active=1 and is_deleted=0";
		}

		//echo $approval_status;


		$approval_status=sql_select($approval_status);
		if($approval_status[0][csf('approval_need')]==1)
		{
			$approval_status_cond= "and a.approved = 1";
		}

		$sql = "select a.id as id, a.pi_number as wopi_number, a.pi_number as wopi_prefix, a.pi_date as wopi_date, a.supplier_id as supplier_id, a.currency_id as currency_id, a.source as source, c.lc_number as lc_number, a.ref_closing_status
		from com_pi_item_details m, com_pi_master_details a
		left join com_btb_lc_pi b on a.id=b.pi_id
		left join com_btb_lc_master_details c on b.com_btb_lc_master_details_id=c.id
		where m.pi_id=a.id and a.item_category_id = 1 and a.importer_id=$company and a.status_active=1 and a.is_deleted=0 and a.goods_rcv_status=2 and m.order_source <>5 $sql_cond $approval_status_cond
		group by a.id, a.pi_number, a.pi_number, a.pi_date, a.supplier_id, a.currency_id, a.source, c.lc_number, a.ref_closing_status";
	} else if ($txt_search_by == 2) {

		$approval_status_cond="";
		if($db_type==0)
		{
			$approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format(date('d-m-Y'),'yyyy-mm-dd')."' and company_id='$company')) and page_id=15 and status_active=1 and is_deleted=0";
		}
		else
		{
			$approval_status="select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format(date('d-m-Y'), "", "",1)."' and company_id='$company')) and page_id=15 and status_active=1 and is_deleted=0";
		}

		$approval_status=sql_select($approval_status);
		if($approval_status[0][csf('approval_need')]==1)
		{
			$approval_status_cond= "and is_approved = 1";
		}


		if ($receive_purpose == 2 || $receive_purpose == 7 || $receive_purpose == 12 || $receive_purpose == 15 || $receive_purpose == 38 || $receive_purpose == 46 || $receive_purpose == 50 || $receive_purpose == 51 )
		{
			$sql_cond = "";
			if ($txt_date_from != "" && $txt_date_to != "") {
				if ($db_type == 0) {
					$sql_cond .= " and booking_date between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
				} else {
					$sql_cond .= " and booking_date between '" . change_date_format($txt_date_from, '', '', 1) . "' and '" . change_date_format($txt_date_to, '', '', 1) . "'";
				}
			}

			if ($txt_wo_no != "") $sql_cond .= " and yarn_dyeing_prefix_num='$txt_wo_no'";
			if ($txt_supplier_id != "") $sql_cond .= " and supplier_id=$txt_supplier_id";

			if($receive_purpose == 2){
				$entry_form ="(41,42,114,125,135)";
				$purpose = "";
				$select_purpose = " 2 as service_type";
			}else{
				$entry_form = "(94)";
				$purpose = " and service_type = $receive_purpose";
				$select_purpose = "service_type";
			}

			$sql = "select id, yarn_dyeing_prefix_num, ydw_no, booking_date, delivery_date, supplier_id, currency, source, pay_mode, entry_form, $select_purpose, 0 as ref_closing_status
			from wo_yarn_dyeing_mst
			where  status_active=1 and is_deleted=0 and entry_form in $entry_form $purpose and pay_mode!=2 and company_id='$company' $sql_cond ";
		}
		else
		{
			$sql_cond = "";
			if ($txt_date_from != "" && $txt_date_to != "") {
				if ($db_type == 0) {
					$sql_cond .= " and wo_date between '" . change_date_format($txt_date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($txt_date_to, 'yyyy-mm-dd') . "'";
				} else {
					$sql_cond .= " and wo_date between '" . change_date_format($txt_date_from, '', '', 1) . "' and '" . change_date_format($txt_date_to, '', '', 1) . "'";
				}
			}

			if ($txt_wo_no != "") $sql_cond .= " and wo_number_prefix_num='$txt_wo_no'";
			if ($txt_supplier_id != "") $sql_cond .= " and supplier_id=$txt_supplier_id";

			$sql = "select id,wo_number as wopi_number,wo_number_prefix_num as wopi_prefix,' ' as lc_number,wo_date as wopi_date,supplier_id as supplier_id,currency_id as currency_id,source as source, 0 as ref_closing_status
			from wo_non_order_info_mst
			where status_active=1 and is_deleted=0 and entry_form=144 and company_name='$company' and pay_mode!=2 and payterm_id<>5 $sql_cond $approval_status_cond order by id";
		}
	}
	// echo $sql;
	$result = sql_select($sql);
	$company_arr = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	foreach ($result as $row) {
		if($receive_basis == 2 && ($receive_purpose == 2 || $receive_purpose == 7 || $receive_purpose == 12 || $receive_purpose == 15 || $receive_purpose == 38 || $receive_purpose == 46 || $receive_purpose == 50 || $receive_purpose == 51  ) )
		{
			if($row[csf('pay_mode')]==3 || $row[csf('pay_mode')]==5)
			{
				$supplier_ref_arr[$row[csf('id')]]=$company_arr[$row[csf('supplier_id')]];
			}else{
				$supplier_ref_arr[$row[csf('id')]]=$supplier_arr[$row[csf('supplier_id')]];
			}
		}
		else
		{
			if($row[csf('pay_mode')]==3 || $row[csf('pay_mode')]==5)
			{
				$supplier_ref_arr[$row[csf('id')]] = $company_arr[$row[csf('supplier_id')]];
			}else{
				$supplier_ref_arr[$row[csf('id')]] = $supplier_arr[$row[csf('supplier_id')]];
			}
		}
	}
	//echo $sql;
	if (($receive_purpose == 2 || $receive_purpose == 7 || $receive_purpose == 12 || $receive_purpose == 15 || $receive_purpose == 38 || $receive_purpose == 46 || $receive_purpose == 50 || $receive_purpose == 51 ) && $txt_search_by == 2)
	{
		$arr = array(1=>$yarn_issue_purpose,4 => $supplier_ref_arr, 5 => $currency, 6 => $source, 7 => $pay_mode);

		echo create_list_view("list_view", "WO No,Service Type,Booking Date,Delivery Date,Supplier,Currency,Source,Pay Mode", "50,100,80,80,180,100,80,120", "900", "260", 0, $sql, "js_set_value", "id,ydw_no,pay_mode,entry_form,ref_closing_status", "", 1, "0,service_type,0,0,id,currency,source,pay_mode", $arr, "yarn_dyeing_prefix_num,service_type,booking_date,delivery_date,id,currency,source,pay_mode", "", '', '0,0,3,3,0,0,0,0');
	}
	else
	{
		$arr = array(3 => $supplier_ref_arr, 4 => $currency, 5 => $source);
		echo create_list_view("list_view", "WO/PI No, LC ,Date, Supplier, Currency, Source", "80,120,90,250,100,100", "800", "260", 0, $sql, "js_set_value", "id,wopi_number,wopi_prefix,0,ref_closing_status", "", 1, "0,0,0,id,currency_id,source", $arr, "wopi_prefix,lc_number,wopi_date,id,currency_id,source", "", '', '0,0,3,0,0,0');
	}
	exit();

}


//right side product list create here--------------------//
if ($action == "show_product_listview") 
{
	$ex_data = explode("**", $data);
	$receive_basis = $ex_data[0];
	$wo_pi_ID = $ex_data[1];
	$receive_purpose = $ex_data[2];
	$company_id = $ex_data[3];

	$variable_rcv_result=sql_select("select id, user_given_code_status from variable_settings_inventory where company_name=$company_id and variable_list='31' and status_active=1 and is_deleted=0");
	$variable_rcv_level = $variable_rcv_result[0][csf("user_given_code_status")];

	if ($receive_basis == 2 && ($receive_purpose == 2 || $receive_purpose == 7 || $receive_purpose == 12 || $receive_purpose == 15 || $receive_purpose == 38 || $receive_purpose == 46 || $receive_purpose == 50 || $receive_purpose == 51))
	{
		if($receive_purpose == 15 || $receive_purpose == 50 || $receive_purpose == 51)
		{
			$sql = "select a.ydw_no,a.company_id,b.id,b.mst_id,b.dtls_id,b.job_no,b.yarn_count count,b.yarn_comp yarn_comp_type1st,b.yarn_perc yarn_comp_percent1st,b.yarn_type,b.yarn_color,b.yarn_rate,a.ecchange_rate from wo_yarn_dyeing_mst a,wo_yarn_dyeing_dtls_fin_prod b where a.id=b.mst_id and a.id=$wo_pi_ID and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";			
			
			if($db_type==0){
				$yarn_dyeing_sql = "select b.mst_id,b.id,group_concat(b.product_id) as product_id,sum(b.yarn_wo_qty) quantity from wo_yarn_dyeing_dtls b where b.status_active =1 and b.is_deleted=0 and b.mst_id=$wo_pi_ID group by b.mst_id,b.id";
			}
			else
			{
				$yarn_dyeing_sql = "select b.mst_id,b.id,listagg(b.product_id, ',') within group (order by b.product_id asc) as product_id,sum(b.yarn_wo_qty) quantity from wo_yarn_dyeing_dtls b where b.status_active =1 and b.is_deleted=0 and b.mst_id=$wo_pi_ID group by b.mst_id,b.id";
			}

			$yarn_dyeing_result = sql_select($yarn_dyeing_sql);
			foreach ($yarn_dyeing_result as $row) {
				$product_id_arr[$row[csf("id")]] += $row[csf("quantity")];
				//$twisting_product[$row[csf("mst_id")]] = $row[csf("product_id")];
			}
		}
		else
		{
			$sql = "select a.ydw_no,a.company_id,a.ecchange_rate,b.id,b.job_no,b.yarn_color,b.yarn_wo_qty quantity,b.yarn_description,b.yarn_comp_percent1st,b.yarn_type,b.yarn_comp_type1st, b.count,b.product_id,b.entry_form,b.dyeing_charge as yarn_rate from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and b.mst_id=$wo_pi_ID and a.status_active=1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 group by a.ydw_no,a.company_id,a.ecchange_rate,b.id,b.job_no,b.yarn_color,b.yarn_wo_qty, b.yarn_description,b.yarn_comp_percent1st,b.yarn_type,b.yarn_comp_type1st, b.count,b.product_id,b.entry_form,b.dyeing_charge";						

			$product_sql = "select a.job_no, c.id as prod_id, c.yarn_comp_percent1st,c.yarn_comp_type1st,c.yarn_count_id,c.yarn_type from inv_transaction a, inv_issue_master b, product_details_master c,wo_yarn_dyeing_dtls d where a.mst_id=b.id and a.prod_id=c.id and b.booking_id=d.mst_id and b.booking_id=$wo_pi_ID and b.entry_form=3 and b.issue_basis=1 and a.item_category=1 and a.transaction_type=2 and a.status_active=1 and a.is_deleted=0 group by c.id, a.job_no,c.yarn_comp_percent1st,c.yarn_comp_type1st,c.yarn_count_id,c.yarn_type";

			$pr_result = sql_select($product_sql);// and b.issue_purpose=$receive_purpose
			$product_arr = array();
			foreach ($pr_result as $pr_row) {
				$product_arr[$pr_row[csf("job_no")]][$pr_row[csf("yarn_comp_percent1st")]][$pr_row[csf("yarn_comp_type1st")]][$pr_row[csf("yarn_count_id")]][$pr_row[csf("yarn_type")]] = $pr_row[csf("prod_id")];
			}

		}

		//echo $sql;
		$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
		$color_arr = return_library_array("select id, color_name from lib_color where status_active = 1 and is_deleted=0", 'id', 'color_name');

		$i = 1;
		?>
		<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" id="tbl_product">
			<thead>
				<tr>
					<th>SL</th>
					<th>Job/Fso No</th>
					<th>Product Name</th>
					<th>Color</th>
					<th>Qnty</th>
				</tr>
			</thead>
			<tbody>
				<?
				//echo $sql;
				$result = sql_select($sql);
				foreach ($result as $row) {
					if ($i % 2 == 0) $bgcolor = "#E9F3FF";
					else $bgcolor = "#FFFFFF";

					$yarn = $yarn_count_arr[$row[csf("count")]] . " " . $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")];

					if($row[csf("product_id")]!="" ){
						$product_id = $row[csf("product_id")];
					}else{
						$product_id = $product_arr[$row[csf("job_no")]][$row[csf("yarn_comp_percent1st")]][$row[csf("yarn_comp_type1st")]][$row[csf("count")]][$row[csf("yarn_type")]];
					}
					if( $receive_purpose == 12 || $receive_purpose == 15 || $receive_purpose == 38 || $receive_purpose == 46 || $receive_purpose == 50 || $receive_purpose == 51)
					{
						$on_click_action = "wo_product_form_yarn_services";
						$on_click_action_param = $row[csf("job_no")] . "**" . $row[csf("count")] . "**" . $row[csf("yarn_comp_type1st")] . "**" . $row[csf("yarn_comp_percent1st")] . "**" . $row[csf("yarn_type")] . "**" . $row[csf("yarn_color")] . "**" . $row[csf("id")] . "**" . $wo_pi_ID . "**" . $receive_purpose . "**" . $row[csf("yarn_rate")] . "**" . $row[csf("ecchange_rate")] . "**" . $row[csf("company_id")]."**".$product_id;
					}else{
						$on_click_action = "wo_product_form_yarn_dyeing";
						$on_click_action_param = $product_id . "**" . $row[csf("job_no")] . "**" . $wo_pi_ID . "**" . $row[csf("yarn_color")] . "**" . $receive_purpose . "**" . $row[csf("id")]. "**" . $row[csf("entry_form")]. "**" . $row[csf("count")] . "**" . $row[csf("yarn_comp_type1st")] . "**" . $row[csf("yarn_comp_percent1st")] . "**" . $row[csf("yarn_type")];
					}
					?>
					<tr bgcolor="<? echo $bgcolor; ?>"
						onClick='get_php_form_data("<? echo $on_click_action_param; ?>","<? echo $on_click_action ?>","requires/yarn_receive_controller");change_color_tr("<? echo $i; ?>","<? echo $bgcolor; ?>")'
						style="cursor:pointer" id="tr_<? echo $i; ?>">
						<td><? echo $i; ?></td>
						<td title="<?php echo $row[csf("ydw_no")]; ?>"><? echo $row[csf("job_no")]; ?></td>
						<td><? echo $yarn; ?></td>
						<td><? echo $color_arr[$row[csf("yarn_color")]]; ?></td>
						<td align="right">
							<?
							$dtls_qnty=0;
							$dtls_ids = explode(",",$row[csf("dtls_id")]);
							foreach ($dtls_ids as $dtls_row) {
								$dtls_qnty += $product_id_arr[$dtls_row];
							}
							echo ($receive_purpose==15 || $receive_purpose==50 || $receive_purpose==51)?$dtls_qnty:$row[csf("quantity")];
							?>
						</td>
					</tr>
					<?
					$i++;
				}
				?>
			</tbody>
		</table>
		<?
	}
	else
	{
		if ($receive_basis == 1) // pi basis
		{
			if($variable_rcv_level)
			{
				$sql = "select  a.id as mst_id, a.pi_number, a.pi_basis_id, b.work_order_no as wo_pi_no, a.importer_id as company_id, a.supplier_id, b.id, b.count_name as yarn_count, b.yarn_composition_item1 as yarn_comp_type1st, b.yarn_composition_percentage1 as yarn_comp_percent1st, b.yarn_composition_item2 as yarn_comp_type2nd, b.yarn_composition_percentage2 as yarn_comp_percent2nd, b.yarn_type, b.color_id as color_name, b.rate, b.quantity as quantity
				from com_pi_master_details a, com_pi_item_details b
				where a.id=b.pi_id and a.id=$wo_pi_ID and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.order_source<>5";
			}
			else
			{
				if($db_type==0)
				{
					$sql = "select a.id as mst_id, a.pi_number, a.pi_basis_id, a.importer_id as company_id, a.supplier_id, group_concat(b.id) as id, b.count_name as yarn_count, b.yarn_composition_item1 as yarn_comp_type1st, b.yarn_composition_percentage1 as yarn_comp_percent1st, b.yarn_composition_item2 as yarn_comp_type2nd, b.yarn_composition_percentage2 as yarn_comp_percent2nd, b.yarn_type, b.color_id as color_name, avg(b.rate) as rate, sum(b.quantity) as quantity
					from com_pi_master_details a, com_pi_item_details b
					where a.id=b.pi_id and a.id=$wo_pi_ID and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.order_source<>5
					group by a.id, a.pi_number, a.pi_basis_id, a.importer_id, a.supplier_id, b.count_name, b.yarn_composition_item1, b.yarn_composition_percentage1, b.yarn_composition_item2, b.yarn_composition_percentage2, b.yarn_type, b.color_id";
				}
				else
				{
					$sql = "select a.id as mst_id, a.pi_number, a.pi_basis_id, a.importer_id as company_id, a.supplier_id, listagg(cast(b.id as varchar(4000)),',') within group (order by b.id) as id, b.count_name as yarn_count, b.yarn_composition_item1 as yarn_comp_type1st, b.yarn_composition_percentage1 as yarn_comp_percent1st, b.yarn_composition_item2 as yarn_comp_type2nd, b.yarn_composition_percentage2 as yarn_comp_percent2nd, b.yarn_type, b.color_id as color_name, avg(b.rate) as rate, sum(b.quantity) as quantity
					from com_pi_master_details a, com_pi_item_details b
					where a.id=b.pi_id and a.id=$wo_pi_ID and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.order_source<>5
					group by a.id, a.pi_number, a.pi_basis_id, a.importer_id, a.supplier_id, b.count_name, b.yarn_composition_item1, b.yarn_composition_percentage1, b.yarn_composition_item2, b.yarn_composition_percentage2, b.yarn_type, b.color_id";
				}
				
			}
			

		}
		else if ($receive_basis == 2) // wo basis
		{
			if($variable_rcv_level)
			{
				$sql = "select a.id as mst_id, a.company_name as company_id ,a.supplier_id, a.wo_number as wo_pi_no, b.id, b.yarn_count, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color_name, b.rate, b.supplier_order_quantity as quantity
				from wo_non_order_info_mst a, wo_non_order_info_dtls b
				where a.id=b.mst_id and a.id=$wo_pi_ID and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			}
			else
			{
				if($db_type==0)
				{
					$sql = "select a.id as mst_id, a.company_name as company_id ,a.supplier_id, a.wo_number as wo_pi_no, group_concat(b.id) as id, b.yarn_count, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color_name, avg(b.rate) as rate, sum(b.supplier_order_quantity) as quantity
					from wo_non_order_info_mst a, wo_non_order_info_dtls b
					where a.id=b.mst_id and a.id=$wo_pi_ID and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
					group by a.id, a.company_name, a.supplier_id, a.wo_number, b.yarn_count, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color_name";
				}
				else
				{
					$sql = "select a.id as mst_id, a.company_name as company_id ,a.supplier_id, a.wo_number as wo_pi_no, listagg(cast(b.id as varchar(4000)),',') within group (order by b.id) as id, b.yarn_count, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color_name, avg(b.rate) as rate, sum(b.supplier_order_quantity) as quantity
					from wo_non_order_info_mst a, wo_non_order_info_dtls b
					where a.id=b.mst_id and a.id=$wo_pi_ID and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
					group by a.id, a.company_name, a.supplier_id, a.wo_number, b.yarn_count, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, b.color_name";
				}
			}
		}
		//echo $sql;//die;
		$result = sql_select($sql);
		$yarn_sum_receive_arr = array();
		foreach ($result as $row_count) {
			$yarn_sum_receive_arr[$row_count[csf("mst_id")]][$row_count[csf("supplier_id")]][$row_count[csf("yarn_count")]][$row_count[csf("yarn_comp_type1st")]][$row_count[csf("yarn_comp_percent1st")]][$row_count[csf("yarn_type")]][$row_count[csf("color_name")]] += $row_count[csf("quantity")];
		}
		$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
		$color_name_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
		$i = 1;
		?>

		<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" id="tbl_product" width="100%">
			<thead>
				<tr>
					<th width="20">SL</th>
					<th width="200">Product Name</th>
					<th width="50">Rate</th>
					<th>Qnty</th>
				</tr>
			</thead>
			<tbody>
				<?
				foreach ($result as $row) {
					if ($i % 2 == 0) $bgcolor = "#E9F3FF";
					else $bgcolor = "#FFFFFF";

					$compositionPart = $composition[$row[csf("yarn_comp_type1st")]] . " " . $row[csf("yarn_comp_percent1st")];
					if ($row[csf("yarn_comp_type2nd")] != 0) {
						$compositionPart .= " " . $composition[$row[csf("yarn_comp_type2nd")]] . " " . $row[csf("yarn_comp_percent2nd")];
					}

					$productName = $yarn_count_arr[$row[csf("yarn_count")]] . " " . $compositionPart . " " . $yarn_type[$row[csf("yarn_type")]] . " " . $color_name_arr[$row[csf("color_name")]];
					$data = $row[csf("yarn_comp_type1st")] . "_" . $row[csf("yarn_comp_percent1st")] . "_" . $row[csf("yarn_comp_type2nd")] . "_" . $row[csf("yarn_comp_percent2nd")] . "_" . $row[csf("yarn_count")] . "_" . $row[csf("yarn_type")] . "_" . $row[csf("color_name")];
					$quantity = $yarn_sum_receive_arr[$row[csf("mst_id")]][$row[csf("supplier_id")]][$row[csf("yarn_count")]][$row[csf("yarn_comp_type1st")]][$row[csf("yarn_comp_percent1st")]][$row[csf("yarn_type")]][$row[csf("color_name")]];
					if($variable_rcv_level==2) $wo_pi_qnty= $row[csf("quantity")]; else $wo_pi_qnty= $quantity;
					?>
					<tr bgcolor="<? echo $bgcolor; ?>"
						onClick='get_php_form_data("<? echo $receive_basis . "**" . $row[csf("id")] . "**" . $row[csf("company_id")] . "**" . $row[csf("supplier_id")] . "**" . $row[csf("quantity")];// $quantity ?>","wo_pi_product_form_input","requires/yarn_receive_controller");change_color_tr("<? echo $i; ?>","<? echo $bgcolor; ?>")'
						style="cursor:pointer" id="tr_<? echo $i; ?>">
						<td align="center"><? echo $i; ?></td>
						<td title="<?php echo $row[csf("wo_pi_no")] ; ?>"><? echo $productName; ?></td>
						<td align="right"><? echo number_format($row[csf("rate")],2); ?></td>
						<td align="right"><? echo $row[csf("quantity")]; ?></td>
					</tr>
					<? $i++;
				} ?>
			</tbody>
		</table>
		<?
	}
	exit();
}


//after select wo/pi number get form data here---------------------------//
if ($action == "populate_data_from_wopi_popup")
{
	$ex_data = explode("**", $data);
	$receive_basis = $ex_data[0];
	$wo_pi_ID = $ex_data[1];
	$receive_purpose = $ex_data[2];
	$company_id = $ex_data[3];

	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name" );
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');

	if ($receive_basis == 1) {
		$sql = "select c.id as id, c.lc_number as lc_number,a.supplier_id as supplier_id,a.currency_id as currency_id,a.source as source
		from com_pi_master_details a
		left join com_btb_lc_pi b on a.id=b.pi_id
		left join com_btb_lc_master_details c on b.com_btb_lc_master_details_id=c.id
		where
		a.item_category_id = 1 and
		a.status_active=1 and a.is_deleted=0 and
		a.id=$wo_pi_ID";
	}
	else if ($receive_basis == 2)
	{
		if ($receive_purpose == 2 || $receive_purpose == 12 || $receive_purpose == 15 || $receive_purpose == 38 || $receive_purpose == 46 || $receive_purpose == 50 || $receive_purpose == 51 ) //currency as currency_id,
		{
			$sql = "select id, '' as lc_number, supplier_id, 1 as currency_id, source, pay_mode, ydw_no,entry_form
			from wo_yarn_dyeing_mst
			where status_active=1 and  is_deleted=0 and id=$wo_pi_ID";
		}
		else
		{
			$sql = "select id,wo_number as ydw_no, '' as lc_number,supplier_id as supplier_id,currency_id as currency_id, source as source,entry_form
			from wo_non_order_info_mst
			where status_active=1 and is_deleted=0 and  id=$wo_pi_ID";
		}
	}
	//echo $sql;die;
	$result = sql_select($sql);
	foreach ($result as $row)
	{
		if ($receive_basis == 2)
		{
			if ($receive_purpose == 2 || $receive_purpose == 12 || $receive_purpose == 15 || $receive_purpose == 38 || $receive_purpose == 46 || $receive_purpose == 50 || $receive_purpose == 51 )
			{
				$ydw_no = $row[csf('ydw_no')];
				$wopi_pay_mode = $row[csf('pay_mode')];
				$wo_entry_form = $row[csf('entry_form')];

				$data_ref="'".$receive_basis."_".$receive_purpose."_".$ydw_no."_".$wopi_pay_mode."_".$wo_entry_form."'";// die();

				echo "load_drop_down( 'requires/yarn_receive_controller', $data_ref, 'load_drop_down_company_from_eheck_wo_paymode', 'supplier');\n";
			}
		}
		
		echo "$('#cbo_supplier').val(" . $row[csf('supplier_id')] . ");\n";
		echo "$('#cbo_currency').val(" . $row[csf("currency_id")] . ");\n";
		echo "$('#cbo_source').val(" . $row[csf("source")] . ");\n";
		echo "$('#txt_lc_no').val('" . $row[csf("lc_number")] . "');\n";

		if ($row[csf("lc_number")] != "")
		{
			echo "$('#hidden_lc_id').val(" . $row[csf("id")] . ");\n";
		}

		if ($row[csf("currency_id")] == 1)
		{
			echo "$('#txt_exchange_rate').val(1);\n";
		}


		echo "$('#cbo_supplier').attr('disabled','disabled');\n";
	}
	exit();
}

// get form data from product click in right side
if ($action == "wo_pi_product_form_input")
{
	$ex_data = explode("**", $data);
	$receive_basis = $ex_data[0];
	$wo_pi_ID = $ex_data[1];
	$company_id = $ex_data[2];
	$supplier_id = $ex_data[3];
	$wo_po_qnty = $ex_data[4];

	$variable_rcv_result=sql_select("select id, user_given_code_status from variable_settings_inventory where company_name=$company_id and variable_list='31' and status_active=1 and is_deleted=0");
	$variable_rcv_level = $variable_rcv_result[0][csf("user_given_code_status")];
	
	if ($db_type == 0) {
		$orderBy_cond = "IFNULL";
		$select_dtls_id = "group_concat(distinct(b.id)) as dtls_id";
	} else if ($db_type == 2) {
		$orderBy_cond = "NVL";
		$select_dtls_id = "listagg(b.id,',') within group (order by b.id) as dtls_id";
	} else {
		$orderBy_cond = "ISNULL";
		$select_dtls_id = "";
	}

	if ($receive_basis == 1) // pi basis
	{ 
		$sql = "select a.id as mst_id, a.version, a.pi_basis_id, $select_dtls_id, b.count_name as yarn_count, b.yarn_composition_item1 as yarn_comp_type1st, b.yarn_composition_percentage1 as yarn_comp_percent1st, b.yarn_composition_item2 as yarn_comp_type2nd, b.yarn_composition_percentage2 as yarn_comp_percent2nd, b.yarn_type, b.color_id as color_name, sum(b.quantity)as quantity,avg(b.rate) as rate, b.uom from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and b.id in($wo_pi_ID) group by a.id,a.version,a.pi_basis_id,b.count_name,b.yarn_composition_item1,b.yarn_composition_percentage1,b.yarn_composition_item2,b.yarn_composition_percentage2,b.yarn_type, b.color_id,b.uom";
 
	}
	else if ($receive_basis == 2) // wo basis
	{
		$sql = "select a.id as mst_id, b.id as dtls_id, b.yarn_count, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type,b.color_name, b.supplier_order_quantity as quantity, rate, uom  
		from wo_non_order_info_mst a, wo_non_order_info_dtls b 
		where a.id=b.mst_id and b.id in($wo_pi_ID)";
	}
	//echo $sql;die;
	$result = sql_select($sql);
	foreach ($result as $row)
	{
		if($receive_basis == 1)
		{
			if($row[csf('version')] == 0){
				echo "$('#cbo_yarn_count').removeAttr('disabled','disabled');\n";
				echo "$('#cbocomposition1').removeAttr('disabled','disabled');\n";
				echo "$('#cbocomposition2').removeAttr('disabled','disabled');\n";
				echo "$('#percentage2').removeAttr('disabled','disabled');\n";
				echo "$('#cbo_yarn_type').removeAttr('disabled','disabled');\n";
				echo "$('#txt_rate').removeAttr('disabled','disabled');\n";
			}
			if($row[csf('version')] == 1){
				echo "$('#cbo_yarn_count').attr('disabled','disabled');\n";
				echo "$('#cbocomposition1').attr('disabled','disabled');\n";
				echo "$('#cbocomposition2').attr('disabled','disabled');\n";
				echo "$('#percentage2').attr('disabled','disabled');\n";
				echo "$('#cbo_yarn_type').attr('disabled','disabled');\n";
				echo "$('#txt_rate').attr('disabled','disabled');\n";
			}
		}

		echo "$('#txt_pi_basis').val(" . $row[csf("pi_basis_id")] . ");\n";
		echo "$('#txt_wo_pi_dtls_id').val(" . $row[csf("dtls_id")] . ");\n";
		echo "$('#cbo_yarn_count').val(" . $row[csf("yarn_count")] . ");\n";
		echo "$('#cbocomposition1').val(" . $row[csf("yarn_comp_type1st")] . ");\n";
		echo "$('#percentage1').val(" . $row[csf("yarn_comp_percent1st")] . ");\n";
		if ($row[csf("yarn_comp_percent2nd")] > 0) {
			echo "$('#cbocomposition2').val(" . $row[csf("yarn_comp_type2nd")] . ");\n";
			echo "$('#percentage2').val(" . $row[csf("yarn_comp_percent2nd")] . ");\n";
		} else {
			echo "$('#cbocomposition2').val(0);\n";
			echo "$('#percentage2').val('');\n";
		}

		echo "$('#cbo_yarn_type').val(" . $row[csf("yarn_type")] . ");\n";
		echo "$('#cbo_uom').val(" . $row[csf("uom")] . ");\n";
		echo "$('#cbo_color').val(" . $row[csf("color_name")] . ").attr('disabled','disabled');\n";
		echo "$('#txt_rate').val(" . $row[csf("rate")] . ");\n";

		if ($row[csf("yarn_comp_type2nd")] <= 0 || $row[csf("yarn_comp_type2nd")] == "") {
			$yarn_comp_type2nd = 0;
		} else {
			$yarn_comp_type2nd = $row[csf("yarn_comp_type2nd")];
		}
		if ($row[csf("yarn_comp_percent2nd")] <= 0 || $row[csf("yarn_comp_percent2nd")] == "") {
			$yarn_comp_percent2nd = 0;
		} else {
			$yarn_comp_percent2nd = $row[csf("yarn_comp_percent2nd")];
		}


		if($variable_rcv_level==2) // ############## for wo pi dtls level
		{
			$whereCondition =" a.id=b.prod_id and b.mst_id = c.id and b.company_id=$company_id and a.supplier_id=$supplier_id and a.item_category_id=1 and b.transaction_type=1 and b.item_category=1 and b.pi_wo_req_dtls_id=" . $row[csf("dtls_id")] . " and b.receive_basis=$receive_basis and b.status_active=1 and b.is_deleted = 0";
		}
		else // ############## for wo pi item level
		{
			$whereCondition = "a.id=b.prod_id and b.mst_id = c.id and a.yarn_count_id=" . $row[csf("yarn_count")] . " and a.yarn_comp_type1st=" . $row[csf("yarn_comp_type1st")] . " and a.yarn_comp_percent1st=" . $row[csf("yarn_comp_percent1st")] . " and $orderBy_cond(a.yarn_comp_type2nd,0)=" . $yarn_comp_type2nd . " and $orderBy_cond(a.yarn_comp_percent2nd,0)=" . $yarn_comp_percent2nd . " and a.yarn_type=" . $row[csf("yarn_type")] . " and a.color=" . $row[csf("color_name")] . " and b.company_id=$company_id and a.supplier_id=$supplier_id and a.item_category_id=1 and b.transaction_type=1 and b.item_category=1 and b.pi_wo_batch_no=" . $row[csf("mst_id")] . " and b.receive_basis=$receive_basis and b.status_active=1 and b.is_deleted = 0";
		}

	 	//echo $whereCondition;die;
	 	$totalRcvQnty = 0;
	 	$mrr_result = sql_select("select sum(b.cons_quantity) as recv_qnty, c.recv_number,a.id as prod_id from product_details_master a, inv_transaction b, inv_receive_master c where $whereCondition group by c.recv_number,a.id");

	 	foreach ($mrr_result as $val)
	 	{
	 		$totalRcvQnty += $val[csf("recv_qnty")];
	 		$mrr_arr[$val[csf("recv_number")]] = $val[csf("recv_number")];
	 		$prod_arr[$val[csf("prod_id")]] = $val[csf("prod_id")];
	 	}
	 	unset($mrr_result);
	}

	$mrr_nos = "'".implode("','",array_filter($mrr_arr))."'";
	$prod_ids = implode(",", array_filter($prod_arr));

	if ($prod_ids != '')
	{
		$prod_ids_condition = " and b.prod_id in ($prod_ids)";
	}
	else
	{
		$prod_ids_condition = "";
	}

	if(str_replace("'", "", $mrr_nos) != "" && $prod_arr!="")
	{
		//Receive Return From Receive Return Mrr

	 	$direct_mrr_return_res = sql_select("select c.id,sum(b.cons_quantity) as ret_qnty
	 		from product_details_master a, inv_transaction b , inv_issue_master c
	 		where a.id=b.prod_id and b.mst_id = c.id and c.entry_form = 8 and c.received_mrr_no in ($mrr_nos)
	 		and a.id in ($prod_ids) and b.status_active = 1 and b.is_deleted=0 and c.status_active = 1 and c.is_deleted = 0
	 		group by c.id");

	 	$rcvReturnQnty=0;

	 	foreach ($direct_mrr_return_res as $val)
	 	{
	 		$direct_mrr_rtn_id[$val[csf("id")]]=$val[csf("id")];
	 		$rcvReturnQnty += $val[csf("ret_qnty")];
	 	}
	 	unset($direct_mrr_return_res);

	 	$direct_mrr_rtn_ids = implode(",", array_filter($direct_mrr_rtn_id));
	}

 	if($direct_mrr_rtn_ids!="")
	{	//Rcv return from Issuer Return
		$mrr_rcv_return_not_cond = "and a.id not in($direct_mrr_rtn_ids)";
	}

	if ($prod_ids != '')
	{
		$rcvReturnQntyFromIssueRetArr = sql_select("select sum(b.cons_quantity) as return_qnty
		from  inv_issue_master a, inv_transaction b where a.id = b.mst_id and a.entry_form =8 and b.item_category = 1 and b.transaction_type=3 and b.status_active = 1 and b.is_deleted = 0 and b.prod_id in ($prod_ids) $mrr_rcv_return_not_cond");
	}

	foreach ($rcvReturnQntyFromIssueRetArr as $val)
	{
		$rcvReturnQnty += $val[csf("return_qnty")];
	}


	$variable_set_invent = sql_select("select category,over_rcv_status,over_rcv_percent,over_rcv_payment from variable_inv_ile_standard where company_name=$company_id and variable_list=23 and category = 1 order by id");
	$over_receive_limit = (!empty($variable_set_invent)) ? $variable_set_invent[0][csf('over_rcv_percent')] : 0;
	$over_receive_limit_qnty = ($over_receive_limit>0)?($over_receive_limit / 100) * $wo_po_qnty:0;

	//$orderQnty = $wo_po_qnty - $totalRcvQnty + $rcvReturnQnty + $over_receive_limit_qnty;
	$actual_rcv = ($totalRcvQnty-$rcvReturnQnty);
	$orderQnty = ($wo_po_qnty - $actual_rcv);

	echo "$('#txt_order_qty').val('" . number_format($orderQnty, 2, '.', '') . "');\n";
	echo "$('#txt_over_recv_limt').val(" . $over_receive_limit_qnty . ");\n";
	echo "$('#txt_woQnty').val(" . $wo_po_qnty . ");\n";
	echo "$('#txt_overRecPerc').val(" . $over_receive_limit . ");\n";
	echo "control_composition('percent_one');\n";
	echo "$('#txt_totRecv').val(" . $actual_rcv . ");\n";
	exit();

}

if ($action == "wo_product_form_yarn_dyeing")
{
	$ex_data = explode("**", $data);
	$prod_id = $ex_data[0];
	$job_no = $ex_data[1];
	$wo_pi_ID = $ex_data[2];
	$color_id = $ex_data[3];
	$purpose = $ex_data[4];
	$wo_dtls_ID = $ex_data[5];
	$entry_form = $ex_data[6];
	$count = $ex_data[7];
	$yarn_comp_type1st = $ex_data[8];
	$yarn_comp_percent1st = $ex_data[9];
	$yarn_type = $ex_data[10];

	if( $purpose==12 || $purpose==38 || $purpose==46 || $purpose==15 ||  $purpose==50 || $purpose==51 )
	{
		$item_category_cond=" and e.item_category_id=0";
	} 
	else {
		$item_category_cond=" and e.item_category_id=24";
	}

	$avg_rate = return_field_value("(sum(cons_amount)/ sum(cons_quantity)) as order_rate", "inv_transaction", "status_active=1 and is_deleted=0 and prod_id=$prod_id and item_category=1 and transaction_type in(1,5)", "order_rate");

	if($entry_form==125 || $entry_form==114)
	{
		$productCond = "";
	}else{
		$productCond = "and d.product_id=$prod_id";
	}

	$dyeing_charge_sql = "select e.ecchange_rate*d.dyeing_charge as dyeing_charge from wo_yarn_dyeing_dtls d,wo_yarn_dyeing_mst e where d.mst_id=e.id and d.id=$wo_dtls_ID  and d.status_active=1 and d.is_deleted=0 and d.yarn_color=$color_id $productCond $item_category_cond";

	//echo $dyeing_charge_sql;//die();

	$dyeing_charge_result = sql_select($dyeing_charge_sql);

	$dyeing_charge = $dyeing_charge_result[0][csf("dyeing_charge")];
	//echo $dyeing_charge;die;
	$dyed_yarn_rate = $avg_rate + $dyeing_charge;

	if($prod_id!="")
	{
		$sql = "select yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, unit_of_measure,color from product_details_master where id=$prod_id";
		//echo $sql;//die;	
		$result = sql_select($sql);
		foreach ($result as $row) {
			echo "$('#cbo_yarn_count').val(" . $row[csf("yarn_count_id")] . ");\n";
			echo "$('#cbocomposition1').val(" . $row[csf("yarn_comp_type1st")] . ");\n";
			echo "$('#percentage1').val(" . $row[csf("yarn_comp_percent1st")] . ");\n";
			if ($row[csf("yarn_comp_type2nd")] > 0) {
				echo "$('#cbocomposition2').val(" . $row[csf("yarn_comp_type2nd")] . ");\n";
				echo "$('#percentage2').val(" . $row[csf("yarn_comp_percent2nd")] . ");\n";
			} else {
				echo "$('#cbocomposition2').val(0);\n";
				echo "$('#percentage2').val('');\n";
			}
			echo "$('#cbo_yarn_type').val(" . $row[csf("yarn_type")] . ");\n";
			echo "$('#cbo_uom').val(" . $row[csf("unit_of_measure")] . ");\n";
			echo "$('#txt_rate').val('" . number_format($dyed_yarn_rate, 4, '.', '') . "');\n";
			echo "$('#txt_avg_rate').val('" . number_format($avg_rate, 3, '.', '') . "');\n";

			echo "$('#txt_dyeing_charge').val('" . number_format($dyeing_charge, 3, '.', '') . "');\n";
			echo "$('#job_no').val('" . $job_no . "');\n";
			if( $purpose==12 || $purpose==38 || $purpose==46 )
			{
				echo "$('#cbo_color').val('" . $row[csf("color")] . "');\n";
			}else{
				echo "$('#cbo_color').val('" . $color_id . "');\n";
			}
			
			echo "$('#txt_order_qty').val('');\n";
		}
	}else{
		echo "$('#cbo_yarn_count').val(" . $count . ");\n";
		echo "$('#cbocomposition1').val(" . $yarn_comp_type1st . ");\n";
		echo "$('#percentage1').val(" . $yarn_comp_percent1st . ");\n";
		echo "$('#cbo_yarn_type').val(" . $yarn_type . ");\n";
		echo "$('#cbo_uom').val(12);\n";
		echo "$('#txt_rate').val('" . number_format($dyed_yarn_rate, 4, '.', '') . "');\n";
		echo "$('#txt_avg_rate').val('" . number_format($avg_rate, 3, '.', '') . "');\n";

		echo "$('#txt_dyeing_charge').val('" . number_format($dyeing_charge, 3, '.', '') . "');\n";
		echo "$('#job_no').val('" . $job_no . "');\n";
		echo "$('#cbo_color').val('" . $color_id . "').attr('disabled','disabled');\n";
		echo "$('#txt_order_qty').val('');\n";
	}	
   
	exit();
}

if ($action == "wo_product_form_yarn_services") 
{
	$ex_data = explode("**", $data);
	
	$job_no = $ex_data[0];
	$count = $ex_data[1];
	$yarn_comp_type1st = $ex_data[2];
	$yarn_comp_percent1st = $ex_data[3];
	$yarn_type = $ex_data[4];
	$color_id = $ex_data[5];
	$wo_dtls_ID = $ex_data[6];
	$wo_pi_ID = $ex_data[7];
	$purpose = $ex_data[8];
	$exchange_rate = $ex_data[10];
	$dyeing_charge = $ex_data[9]*$exchange_rate;
	$company_id = $ex_data[11];
	$product_ids = $ex_data[12];


	$avg_rate = return_field_value("(sum(cons_amount)/ sum(cons_quantity)) as order_rate", "inv_transaction", "status_active=1 and is_deleted=0 and prod_id in($product_ids) and item_category=1 and transaction_type in(1,5)", "order_rate");

	$dyed_yarn_rate = $avg_rate + $dyeing_charge;

	if($product_ids!="")
	{
		$sql = "select yarn_count_id, yarn_comp_type1st, yarn_comp_percent1st, yarn_comp_type2nd, yarn_comp_percent2nd, yarn_type, unit_of_measure,color from product_details_master where id=$product_ids";
		//echo $sql;//die;	
		$result = sql_select($sql);
		foreach ($result as $row) {
			echo "$('#cbo_yarn_count').val(" . $row[csf("yarn_count_id")] . ");\n";
			echo "$('#cbocomposition1').val(" . $row[csf("yarn_comp_type1st")] . ");\n";
			echo "$('#percentage1').val(" . $row[csf("yarn_comp_percent1st")] . ");\n";
			if ($row[csf("yarn_comp_type2nd")] > 0) {
				echo "$('#cbocomposition2').val(" . $row[csf("yarn_comp_type2nd")] . ");\n";
				echo "$('#percentage2').val(" . $row[csf("yarn_comp_percent2nd")] . ");\n";
			} else {
				echo "$('#cbocomposition2').val(0);\n";
				echo "$('#percentage2').val('');\n";
			}
			echo "$('#cbo_yarn_type').val(" . $row[csf("yarn_type")] . ");\n";
			echo "$('#cbo_uom').val(" . $row[csf("unit_of_measure")] . ");\n";
			echo "$('#txt_rate').val('" . number_format($dyed_yarn_rate, 4, '.', '') . "');\n";
			echo "$('#txt_avg_rate').val('" . number_format($avg_rate, 3, '.', '') . "');\n";

			echo "$('#txt_dyeing_charge').val('" . number_format($dyeing_charge, 3, '.', '') . "');\n";
			echo "$('#job_no').val('" . $job_no . "');\n";
			if( $purpose==12 || $purpose==38 || $purpose==46 )
			{
				echo "$('#cbo_color').val('" . $row[csf("color")] . "');\n";
			}else{
				echo "$('#cbo_color').val('" . $color_id . "');\n";
			}
			
			echo "$('#txt_order_qty').val('');\n";
		}
	}else{
		echo "$('#cbo_yarn_count').val(" . $count . ");\n";
		echo "$('#cbocomposition1').val(" . $yarn_comp_type1st . ");\n";
		echo "$('#percentage1').val(" . $yarn_comp_percent1st . ");\n";
		echo "$('#cbo_yarn_type').val(" . $yarn_type . ");\n";
		echo "$('#cbo_uom').val(12);\n";
		echo "$('#txt_rate').val('" . number_format($dyed_yarn_rate, 4, '.', '') . "');\n";
		echo "$('#txt_avg_rate').val('" . number_format($avg_rate, 3, '.', '') . "');\n";

		echo "$('#txt_dyeing_charge').val('" . number_format($dyeing_charge, 3, '.', '') . "');\n";
		echo "$('#job_no').val('" . $job_no . "');\n";
		echo "$('#cbo_color').val('" . $color_id . "').attr('disabled','disabled');\n";
		echo "$('#txt_order_qty').val('');\n";
	}

	exit();
}

// LC popup here----------------------Not Used//
if ($action == "lc_popup") 
{
	echo load_html_head_contents("Popup Info", "../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>

	<script>
		function js_set_value(str) {
			var splitData = str.split("_");
        $("#hidden_tbl_id").val(splitData[0]); // wo/pi id
        $("#hidden_wopi_number").val(splitData[1]); // wo/pi number
        parent.emailwindow.hide();
    }
    </script>
	</head>
	<body>
		<div align="center" style="width:100%;">
			<form name="searchlcfrm_1" id="searchlcfrm_1" autocomplete="off">
				<table width="600" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
					<thead>
						<tr>
							<th width="150">Search By</th>
							<th width="150" align="center" id="search_by_td_up">Enter WO/PI Number</th>
							<th>
								<input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px"
								class="formbutton"/>
								<!-- Hidden field here-->
								<input type="hidden" id="hidden_tbl_id" value=""/>
								<input type="hidden" id="hidden_wopi_number" value="hidden_wopi_number"/>
								<!-- END -->
							</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<?
								$search_by_arr = array(0 => 'LC Number', 1 => 'Supplier Name');
								$dd = "change_search_event(this.value, '0*1', '0*select id, supplier_name from lib_supplier', '../../') ";
								echo create_drop_down("cbo_search_by", 170, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>
							<td width="180" align="center" id="search_by_td">
								<input type="text" style="width:230px" class="text_boxes" name="txt_search_common"
								id="txt_search_common"/>
							</td>
							<td align="center">
								<input type="button" name="btn_show" class="formbutton" value="Show"
								onClick="show_list_view ( document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+<? echo $company; ?>, 'create_lc_search_list_view', 'search_div', 'yarn_receive_controller', 'setFilterGrid(\'list_view\',-1)')"
								style="width:100px;"/>
							</td>
						</tr>
					</tbody>
				</table>
				<div align="center" valign="top" id="search_div"></div>
			</form>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

//Not Used
if ($action == "create_lc_search_list_view") 
{
	$ex_data = explode("_", $data);
	$cbo_search_by = $ex_data[0];
	$txt_search_common = $ex_data[1];
	$company = $ex_data[2];

	if ($cbo_search_by == 1 && $txt_search_common != "") // lc number
	{
		$sql = "select id,lc_number,item_category_id,lc_serial,supplier_id,importer_id,lc_value from com_btb_lc_master_details where lc_number LIKE '%$search_string%' and importer_id=$company and item_category_id=1 and is_deleted=0 and status_active=1";
	} else if ($cbo_search_by == 1 && $txt_search_common != "") //supplier
	{
		$sql = "select id,lc_number,item_category_id,lc_serial,supplier_id,importer_id,lc_value from com_btb_lc_master_details where supplier_id='$search_string' and importer_id=$company and item_category_id=1 and is_deleted=0 and status_active=1";
	} else {
		$sql = "select id,lc_number,item_category_id,lc_serial,supplier_id,importer_id,lc_value from com_btb_lc_master_details where importer_id=$company and item_category_id=1 and is_deleted=0 and status_active=1";
	}

	$company_arr = return_library_array("select id,company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	$arr = array(1 => $company_arr, 2 => $supplier_arr, 3 => $item_category);
	echo create_list_view("list_view", "LC No,Importer,Supplier Name,Item Category,Value", "120,150,150,120,120", "750", "260", 0, $sql, "js_set_value", "id,lc_number", "", 1, "0,importer_id,supplier_id,item_category_id,0", $arr, "lc_number,importer_id,supplier_id,item_category_id,lc_value", "", '', '0,0,0,0,0,1');
	exit();

}


if ($action == "show_ile") 
{
	$ex_data = explode("**", $data);
	$company = $ex_data[0];
	$source = $ex_data[1];
	$rate = $ex_data[2];

	$sql = "select standard from variable_inv_ile_standard where source='$source' and company_name='$company' and category=1 and status_active=1 and is_deleted=0";
	//echo $sql;
	$result = sql_select($sql, 1);
	foreach ($result as $row) {
		// NOTE :- ILE=standard, ILE% = standard/100*rate
		$ile = $row[csf("standard")];
		$ile_percentage = ($row[csf("standard")] / 100) * $rate;
		echo $ile . "**" . number_format($ile_percentage, $dec_place[3], ".", "");
		exit();
	}
	exit();
}

//data save update delete here------------------------------//
if ($action == "save_update_delete") 
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$con = connect();
	if ($db_type == 0) {
		mysql_query("BEGIN");
	}
	$variable_set_invent = return_field_value("user_given_code_status", "variable_settings_inventory", "company_name=$cbo_company_id and variable_list=19 and item_category_id=1", "user_given_code_status");
	// check variable settings if allocation is available or not
	$variable_set_allocation = return_field_value("allocation", "variable_settings_inventory", "company_name=$cbo_company_id and variable_list=18 and item_category_id = 1");

	$variable_set_invent = sql_select("select category,over_rcv_status,over_rcv_percent,over_rcv_payment from variable_inv_ile_standard where company_name=$cbo_company_id and variable_list=23 and category = 1 order by id");
	$over_receive_limit = !empty($variable_set_invent) ? $variable_set_invent[0][csf('over_rcv_percent')] : 0;

	if(str_replace("'", "", $cbo_receive_basis) ==2 && str_replace("'","",$txt_wo_pi_id)>0 && (str_replace("'","",$cbo_receive_purpose)==16 || str_replace("'","",$cbo_receive_purpose)==5) )
	{
		$sql_wo=sql_select("select pay_mode, payterm_id from wo_non_order_info_mst where id=".str_replace("'","",$txt_wo_pi_id)." and status_active=1");
		$pay_mode=$sql_wo[0][csf("pay_mode")];
		$payterm_id=$sql_wo[0][csf("payterm_id")];
		if($pay_mode!=1)
		{
			echo "40** WO Pay Mode Not Match.";disconnect($con); die;
		}
		if($payterm_id ==5)
		{
			echo "40** WO Pay Term Not Match.";disconnect($con); die;
		}
	}

	if(str_replace("'", "", $cbo_receive_basis) ==1)
	{
		$pi_sql=sql_select("select ref_closing_status from com_pi_master_details where id=$txt_wo_pi_id");
		$ref_closing_status=$pi_sql[0][csf("ref_closing_status")];
		if($ref_closing_status==1)
		{
			echo "30** This PI is Already Closed.";
			disconnect($con);
			die;
		}
	}

	
	// 11-05-2019
	if ($operation == 0 || $operation == 1)
	{
		if((str_replace("'", "", $cbo_receive_basis) == 1))
		{
			if ($db_type == 0) {
				$orderBy_cond = "IFNULL";
			} else if ($db_type == 2) {
				$orderBy_cond = "NVL";
			} else {
				$orderBy_cond = "ISNULL";
			}

			$variable_rcv_result=sql_select("select id, user_given_code_status from variable_settings_inventory where company_name=$cbo_company_id and variable_list='31' and status_active=1 and is_deleted=0");
			$variable_rcv_level = $variable_rcv_result[0][csf("user_given_code_status")];

			if($variable_rcv_level==2) // ############## for wo pi dtls level
			{
				$pi_qty_data = sql_select("select sum(quantity) as quantity from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and b.id=$txt_wo_pi_dtls_id");

				$whereCondition =" a.id=b.prod_id and b.mst_id = c.id and b.company_id=$cbo_company_id and a.supplier_id=$cbo_supplier and a.item_category_id=1 and b.transaction_type=1 and b.item_category=1 and b.pi_wo_req_dtls_id=" . $txt_wo_pi_dtls_id . " and b.receive_basis=$cbo_receive_basis and b.status_active=1 and b.is_deleted = 0";

				$recev_return = sql_select("select sum(c.cons_quantity) as recv_rtn_qnty from  inv_receive_master a, inv_issue_master b,inv_transaction c left join product_details_master d on c.prod_id=d.id and d.status_active=1 and d.is_deleted=0 where a.pi_id=$txt_wo_pi_id and a.id=b.received_id and b.id=c.mst_id and a.entry_form = 1 and a.receive_basis = 2 and a.status_active=1 and b.status_active=1 and c.transaction_type=3 and b.entry_form=8");
			}
			else // ############## for wo pi item level
			{
				$percentage2 = ( str_replace("'", "", $percentage2) =="")?0:$percentage2;
				$cbocomposition2 = (str_replace("'", "", $cbocomposition2) =="")?0:$cbocomposition2;

				$whereCondition = "a.id=b.prod_id and b.mst_id = c.id and a.yarn_count_id=" . $cbo_yarn_count . " and a.yarn_comp_type1st=" . $cbocomposition1 . " and a.yarn_comp_percent1st=" . $percentage1 . " and $orderBy_cond(a.yarn_comp_type2nd,0)=" . $cbocomposition2 . " and $orderBy_cond(a.yarn_comp_percent2nd,0)=" . $percentage2 . " and a.yarn_type=" . $cbo_yarn_type . " and a.color=" . $cbo_color . " and b.company_id=$cbo_company_id and a.supplier_id=$cbo_supplier and a.item_category_id=1 and b.transaction_type=1 and b.item_category=1 and b.pi_wo_batch_no=" . $txt_wo_pi_id . " and b.receive_basis=$cbo_receive_basis and b.status_active=1 and b.is_deleted = 0";

				$pi_qty_data = sql_select("select sum(quantity) as quantity from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and b.pi_id=$txt_wo_pi_id and b.color_id=$cbo_color and b.yarn_composition_item1=$cbocomposition1 and b.yarn_composition_percentage1=$percentage1 and b.yarn_type=$cbo_yarn_type and b.count_name=" . $cbo_yarn_count . "");
			}
			
			$mrr_result = sql_select("select b.id as trans_id, b.cons_quantity as recv_qnty, c.recv_number, a.id as prod_id 
			from product_details_master a, inv_transaction b, inv_receive_master c 
			where $whereCondition and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0");

			$prev_rev_qnty=0;
			foreach ($mrr_result as $val)
			{
				if(str_replace("'","",$update_id) != $val[csf("trans_id")])
				{
					$prev_rev_qnty += $val[csf("recv_qnty")];
				}				
				$mrr_arr[$val[csf("recv_number")]] = $val[csf("recv_number")];
				$prod_arr[$val[csf("prod_id")]] = $val[csf("prod_id")];
			}
			unset($mrr_result);

			$mrr_nos = "'".implode("','",array_filter($mrr_arr))."'";
			$prod_ids = implode(",", array_filter($prod_arr));

			if(str_replace("'", "", $mrr_nos) != "" && $prod_arr!="")
			{
				$direct_mrr_return_res = sql_select("select c.id,sum(b.cons_quantity) as ret_qnty from product_details_master a,inv_transaction b , inv_issue_master c where a.id=b.prod_id and b.mst_id = c.id and c.entry_form = 8 and c.received_mrr_no in ($mrr_nos) and a.id in ($prod_ids) and b.status_active = 1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted = 0 group by c.id");

				$rcvReturnQnty=0;
				foreach ($direct_mrr_return_res as $val)
				{
					$rcvReturnQnty += $val[csf("ret_qnty")];
					$direct_mrr_rtn_id[$val[csf("id")]]=$val[csf("id")];
				}
				unset($direct_mrr_return_res);

				$direct_mrr_rtn_ids = implode(",", array_filter($direct_mrr_rtn_id));

			 	if($direct_mrr_rtn_ids!="")
				{	//Rcv return from Issuer Return
					$mrr_rcv_return_not_cond = "and a.id not in($direct_mrr_rtn_ids)";
				}

				if ($prod_ids != '')
				{
					$rcvReturnQntyFromIssueRetArr = sql_select("select sum(b.cons_quantity) as return_qnty
					from  inv_issue_master a, inv_transaction b where a.id = b.mst_id and a.entry_form =8 and b.item_category = 1 and b.transaction_type=3 and b.status_active = 1 and b.is_deleted = 0 and b.prod_id in ($prod_ids) $mrr_rcv_return_not_cond");
				}

				foreach ($rcvReturnQntyFromIssueRetArr as $val)
				{
					$rcvReturnQnty += $val[csf("return_qnty")];
				}

				/*
				//Issue Nos From Receive Mrr
				$issue_sql = sql_select("select a.id,b.cons_quantity, b.id as trans_id
					from  inv_issue_master a, inv_transaction b, inv_mrr_wise_issue_details c, inv_receive_master d, inv_transaction e
					where a.id = b.mst_id and b.id = c.issue_trans_id and c.recv_trans_id = e.id and d.id = e.mst_id
					and d.recv_number in ($mrr_nos) and b.prod_id in ($prod_ids)
					and a.entry_form = 3 and c.entry_form = 3 and d.item_category=1 and b.status_active = 1 and b.is_deleted = 0");

				foreach ($issue_sql as $val)
				{
					$issue_id_arr[$val[csf("id")]] = $val[csf("id")];
				}

				$issue_ids = implode(",", array_filter($issue_id_arr));

				if($issue_ids)
				{
					// $Issue Return From Issue
					$issue_ret_sql = sql_select("select a.id,a.recv_number,b.cons_quantity, b.id as trans_id
						from  inv_receive_master a, inv_transaction b
						where a.id = b.mst_id and a.entry_form =9 and b.item_category = 1
						and b.status_active = 1 and b.is_deleted = 0 and a.issue_id in ($issue_ids) and b.prod_id in ($prod_ids)");

					foreach ($issue_ret_sql as $val)
					{
						$issue_ret_ids[$val[csf("id")]] = $val[csf("id")];
					}
					unset($issue_ret_sql);

					$issue_ret_ids = implode(",", array_filter($issue_ret_ids));

					if($issue_ret_ids)
					{
						//Receive Return From Issue Return
						$rcvReturnQntyFromIssueRetArr = sql_select("select sum(b.cons_quantity) as return_qnty
							from  inv_issue_master a, inv_transaction b
							where a.id = b.mst_id and a.entry_form =8 and b.item_category = 1
							and b.status_active = 1 and b.is_deleted = 0 and a.received_id in ($issue_ret_ids) and b.prod_id in ($prod_ids)");

						foreach ($rcvReturnQntyFromIssueRetArr as $val)
						{
							$rcvReturnQnty += $val[csf("return_qnty")];
						}
					}
				}*/
			}

			$piQnty = $pi_qty_data[0][csf('quantity')];
			$over_receive_limit_qnty = ($over_receive_limit>0)?($over_receive_limit / 100) * $piQnty:0;
			$allow_total_qty = number_format(($piQnty +$over_receive_limit_qnty),2,'.','');
			$overRecvLimitMsg="Over Receive limit = $over_receive_limit% ($over_receive_limit_qnty Kg.)";
			$txt_receive_qty = str_replace("'", "", $txt_receive_qty);

			$total_recvQnty= number_format( (($txt_receive_qty+$prev_rev_qnty)-$rcvReturnQnty),2,'.','' );

			//echo "40**".$prev_rev_qnty."==".$rcvReturnQnty."test"; die();
			if( $allow_total_qty<$total_recvQnty )
			{
				echo "40**Receive quantity can not be greater than PI quantity\nPI quantity=$piQnty\n$overRecvLimitMsg $over_msg\nAllowed quantity = $allow_total_qty\nPrevious Receive quantity = $prev_rev_qnty\nU Input = $txt_receive_qty";disconnect($con);die;
			}
		}
	}

	if ($operation == 0) // Insert Here
	{
		$flag = 1;

		//--------Check Receive control on Gate Entry according to variable settings inventory--------//
		if ($variable_set_invent == 1)
		{
			$challan_no = str_replace("'", "", $txt_challan_no);
			if ($challan_no != "") {
				$variable_set_invent = return_field_value("a.id as id", " inv_gate_in_mst a,  inv_gate_in_dtl b", "a.id=b.mst_id and a.company_id=$cbo_company_id and a.challan_no='$challan_no' and b.item_category_id=1  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "id");
				if (empty($variable_set_invent)) {
					echo "30** This Item Not Found In Gate Entry. \n Please Gate Entry First.";
					disconnect($con);
					die;
				}
			}
		}
		//---------------End Check Receive control on Gate Entry---------------------------//

		//-------------Yarn Service Booking. Receive can not be greater than service booking(wo/booking) ----//
		if((str_replace("'", "", $cbo_receive_basis) == 2))
		{
			$cbo_receive_purpose=str_replace("'", "", $cbo_receive_purpose);
			$cbo_color=str_replace("'", "", $cbo_color);

			if ($cbo_receive_purpose == 2 || $cbo_receive_purpose == 12 || $cbo_receive_purpose == 15 || $cbo_receive_purpose == 38 || $cbo_receive_purpose == 46 || $cbo_receive_purpose == 50 || $cbo_receive_purpose == 51 )
			{
				$sql_exis_servc_recvQnty=sql_select("select listagg(a.recv_number_prefix_num, ',') within group (order by a.id) as rcv_no,a.booking_id,sum(b.cons_quantity) as recv_quantity,sum(b.grey_quantity) as grey_quantity from inv_receive_master a, inv_transaction b left join product_details_master c on b.prod_id=c.id where a.id=b.mst_id and a.company_id=$cbo_company_id and a.booking_id=$txt_wo_pi_id and b. transaction_type=1 and a.entry_form=1 and a.receive_basis=2 and a.receive_purpose=$cbo_receive_purpose and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.yarn_type=$cbo_yarn_type and c.color=$cbo_color and c.yarn_count_id=$cbo_yarn_count  and c.yarn_comp_type1st=$cbocomposition1 and yarn_comp_percent1st=$percentage1 group by a.booking_id"); // and c.lot='$txt_yarn_lot'

				$sql_wo_yarn_qnty=sql_select("select a.id,sum(b.yarn_wo_qty) as yarn_wo_qty from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_id and a.id=$txt_wo_pi_id and a.entry_form in(41,42,94,114,125) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by a.id");
			}
			else
			{
				$sql_exis_servc_recvQnty=sql_select("select listagg(a.recv_number_prefix_num, ',') within group (order by a.id) as rcv_no,a.booking_id,sum(b.cons_quantity) as recv_quantity from inv_receive_master a, inv_transaction b left join product_details_master c on b.prod_id=c.id where a.id=b.mst_id and a.company_id=$cbo_company_id and a.booking_id=$txt_wo_pi_id and b. transaction_type=1 and a.entry_form=1 and a.receive_basis=2 and a.receive_purpose=$cbo_receive_purpose and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.yarn_type=$cbo_yarn_type and c.color=$cbo_color and c.yarn_count_id=$cbo_yarn_count  and c.yarn_comp_type1st=$cbocomposition1 and yarn_comp_percent1st=$percentage1 group by a.booking_id");

				$sql_wo_yarn_qnty=sql_select("select a.id,sum(b.supplier_order_quantity) as yarn_wo_qty from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.company_name=$cbo_company_id and a.id=$txt_wo_pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by a.id");
			}

			//issue check only for service
			if ($cbo_receive_purpose == 2 || $cbo_receive_purpose == 12 || $cbo_receive_purpose == 15 || $cbo_receive_purpose == 38 || $cbo_receive_purpose == 46 || $cbo_receive_purpose == 50 || $cbo_receive_purpose == 51 )
			{

				if($cbo_receive_purpose == 15 || $cbo_receive_purpose == 50 || $cbo_receive_purpose == 51)
				{
					$sql_issue_qnty=sql_select("select sum(b.cons_quantity) as cons_quantity from inv_issue_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id and a.company_id=$cbo_company_id and a.issue_basis=1 and a.issue_purpose in(15,50,51) and a. item_category=1 and a.entry_form= 3 and b.receive_basis = 1 and b.item_category=1 and b.transaction_type=2 and a.booking_id=$txt_wo_pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
				}
				else
				{
					if($cbo_receive_purpose == 2)
					{
						$color_cond = "and b.dyeing_color_id=$cbo_color";	
					}
					
					$sql_issue_qnty=sql_select("select sum(b.cons_quantity) as cons_quantity from inv_issue_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id and a.company_id=$cbo_company_id and a.issue_basis=1 and a.issue_purpose in(2,12,38,46) and a. item_category=1 and a.entry_form= 3 and b.receive_basis = 1 and b.item_category=1 and b.transaction_type=2 and a.booking_id=$txt_wo_pi_id and c.yarn_type=$cbo_yarn_type and c.yarn_count_id=$cbo_yarn_count  and c.yarn_comp_type1st=$cbocomposition1 and yarn_comp_percent1st=$percentage1 $color_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
				}
				
				$issueQnty_service=$sql_issue_qnty[0][csf('cons_quantity')];
			}

			//query for deduction receive return qunatity
			$sql_return_recev=	sql_select("select sum(c.cons_quantity  ) as issue_qnty from  inv_receive_master a, inv_issue_master b,inv_transaction c left join product_details_master d on c.prod_id=d.id and d.status_active=1 and d.is_deleted=0 where a.booking_id=$txt_wo_pi_id and a.id=b.received_id and b.id=c.mst_id and a.entry_form = 1 and a.receive_basis = 2 and a.status_active=1 and b.status_active=1 and c.transaction_type=3 and b.entry_form=8 and d.color=$cbo_color and d.yarn_count_id=$cbo_yarn_count and d.yarn_comp_type1st=$cbocomposition1 and d.yarn_comp_type2nd=$cbocomposition2 and d.yarn_comp_percent1st=$percentage1 and d.yarn_type=$cbo_yarn_type"); //and d.lot='$txt_yarn_lot'

			$return_receiveQnty=$sql_return_recev[0][csf('issue_qnty')];
			$woYarnQnty=$sql_wo_yarn_qnty[0][csf('yarn_wo_qty')];

			$over_receive_limit_qnty = ($over_receive_limit>0)?($over_receive_limit / 100) * $woYarnQnty:0;

			if ($cbo_receive_purpose == 2 || $cbo_receive_purpose == 12 || $cbo_receive_purpose == 15 || $cbo_receive_purpose == 38 || $cbo_receive_purpose == 46 || $cbo_receive_purpose == 50 || $cbo_receive_purpose == 51)
			{
				$wo_qnty = $issueQnty_service;
				$allow_total_val=$issueQnty_service + $over_receive_limit_qnty;
				$issud_msg="Issue";
				$overRecvLimitMsg="Over Receive limit = $over_receive_limit% ($over_receive_limit_qnty Kg.)";
			}
			else
			{
				$wo_qnty = $woYarnQnty;
				$allow_total_val = $woYarnQnty + $over_receive_limit_qnty;
				$overRecvLimitMsg="Over Receive limit = $over_receive_limit% ($over_receive_limit_qnty Kg.)";
				$issud_msg="";
			}

			$txt_receive_qty = str_replace("'", "", $txt_receive_qty);
			$hdn_receive_qty = str_replace("'", "", $hdn_receive_qty);
			$total_recvQnty=($sql_exis_servc_recvQnty[0][csf('recv_quantity')]-$hdn_receive_qty)+$txt_receive_qty;

			$allow_total_val = number_format($allow_total_val,2,'.','');
			$balance = number_format(($total_recvQnty-$return_receiveQnty),2,'.','');

			// Grey used qnty validation		
			if( $cbo_receive_purpose== 2 || $cbo_receive_purpose==12 || $cbo_receive_purpose==15 || $cbo_receive_purpose==38 )
			{
				$txt_grey_qty = str_replace("'", "", $txt_grey_qty)*1;
				$txt_receive_qty = str_replace("'", "", $txt_receive_qty)*1;

				$hdn_grey_qty = str_replace("'", "", $hdn_grey_qty);
				$total_greyQnty=($sql_exis_servc_recvQnty[0][csf('grey_quantity')]-$hdn_grey_qty)+$txt_grey_qty;
				//-$return_receiveQnty
				$grey_quantity_balance = number_format(($total_greyQnty),2,'.','');

				if($txt_grey_qty<$txt_receive_qty)
				{
					echo "40**Grey quantity can not be less than received quantity";
					die;
				}

				if( ($allow_total_val-$over_receive_limit_qnty)<$grey_quantity_balance )
				{
					$thismrrGreyQty = str_replace("'", "", $hdn_grey_qty);
					$previousGreyQty = $sql_exis_servc_recvQnty[0][csf('grey_quantity')];
					$rcvNumbers = $sql_exis_servc_recvQnty[0][csf('rcv_no')];
					$actualGreyQty = (($previousGreyQty+$thismrrGreyQty)); 
					//$over_grey_msg = ($over_receive_limit>0)?"\nAllowed Quantity = $allow_total_val":"";
					$allowedGreyBalance = number_format(($wo_qnty-$actualGreyQty),2,'.','');

					echo "40**Grey quantity can not be greater than $issud_msg quantity.\n$issud_msg quantity = $wo_qnty\nReceived No=$rcvNumbers\nGrey Qty = $actualGreyQty\nBalance = $allowedGreyBalance";
					disconnect($con);die;	
				}

			}
			
			if($allow_total_val<$balance) 
			{
				$thismrrRcv = str_replace("'", "", $hdn_receive_qty);
				$previousRcv = $sql_exis_servc_recvQnty[0][csf('recv_quantity')];
				$rcvNumbers = $sql_exis_servc_recvQnty[0][csf('rcv_no')];
				$actualRcv = (($previousRcv+$thismrrRcv)-$return_receiveQnty); 
				$over_msg = ($over_receive_limit>0)?"\nAllowed Quantity = $allow_total_val":"";
				$allowedBalance = number_format(($wo_qnty-$actualRcv),2,'.','');
				echo "40**Recv. quantity can not be greater than $issud_msg quantity.\n$issud_msg quantity = $wo_qnty\nReceived No=$rcvNumbers\nReceived Qty = $actualRcv\n$overRecvLimitMsg $over_msg\nBalance = $allowedBalance";
				disconnect($con);die;
			}

		}
		//-------------End Yarn Service Booking. Receive can not be greater than service booking(wo/booking) ----//

		//---------------Check Color---------------------------//
		/* Old color system generator 
		if (str_replace("'", "", $btn_color) == 'F') {
			$color_library = return_library_array("select id,color_name from lib_color where status_active =1 and is_deleted=0", 'id', 'color_name');
			$cbo_color = return_id(str_replace("'", "", $cbo_color), $color_library, "lib_color", "id,color_name");
		}
		*/

		if (str_replace("'", "", $btn_color) == 'F') {
			// Simillar like order entry page
			if(str_replace("'","",$cbo_color)!="")
			{
				$color_library = return_library_array("select id,color_name from lib_color where status_active =1 and is_deleted=0", 'id', 'color_name');

				if (!in_array(str_replace("'","",$cbo_color),$new_array_color))
				{
					$color_id = return_id( str_replace("'","",$cbo_color), $color_library, "lib_color", "id,color_name","1");
					//echo $cbo_color.'='.$color_id.'<br>';
					$new_array_color[$color_id]=str_replace("'","",$cbo_color);

					//echo "10**hi==$color_id"; die();
				}
				else {
					$color_id = array_search(str_replace("'","",$cbo_color), $new_array_color);
					//echo "10**by==$color_id"; die();
				}
			}
			else
			{
				$color_id=0;
			}
		}else{
			$color_id = str_replace("'","",$cbo_color);
		}
		//----------------Check Color END---------------------//


		//---------------Check Brand---------------------------//
		if (str_replace("'", "", $txt_brand) != "") {
			//$brand_library = return_library_array("select id,brand_name from lib_brand", 'id', 'brand_name');
			//$txt_brand = return_id(str_replace("'", "", $txt_brand), $brand_library, "lib_brand", "id,brand_name");
			$brand_arr=return_library_array( "select id, brand_name from lib_brand where status_active=1",'id','brand_name');
		
			if (str_replace("'", "", trim($txt_brand)) != "") {
				if (!in_array(str_replace("'", "", trim($txt_brand)),$new_array_brand)){
					$brand_id = return_id( str_replace("'", "", trim($txt_brand)), $brand_arr, "lib_brand", "id,brand_name","1");
					$new_array_brand[$brand_id]=str_replace("'", "", trim($txt_brand));

				}
				else $brand_id =  array_search(str_replace("'", "", trim($txt_brand)), $new_array_brand);
			} else $brand_id = 0;
			
			$txt_brand =$brand_id;
		}
		//----------------Check Brand END---------------------//


		//---------------Check Product ID --------------------------//
		$insertR = true;
		$rtnString = return_product_id($cbo_yarn_count, $cbocomposition1, $cbocomposition2, $percentage1, $percentage2, $cbo_yarn_type, $color_id, $txt_yarn_lot, $txt_prod_code, $cbo_company_id, $cbo_supplier, $cbo_store_name, $cbo_uom, $yarn_type, $composition,$cbo_receive_purpose);
		$expString = explode("***", $rtnString);

		if ($expString[0] == true && $expString[0] != "") {
			$prodMSTID = $expString[1];
		} else {
			$field_array = $expString[1];
			$data_array = $expString[2];
			//echo "20**"."insert into product_details_master (".$field_array.") values ".$data_array;die;
			$insertR = sql_insert("product_details_master", $field_array, $data_array, 0);
			$prodMSTID = $expString[3];
		}
                //---------------Check Receive date with Last Transaction date-------------//
		$max_transaction_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$prodMSTID and store_id=$cbo_store_name  and status_active = 1", "max_date");
		if($max_transaction_date != "")
		{
			$max_transaction_date = date("Y-m-d", strtotime($max_transaction_date));
			$receive_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_receive_date)));
			if ($receive_date < $max_transaction_date)
			{
				echo "20**Receive Date Can not Be Less Than Last Transaction Date Of This Lot";
				disconnect($con);
				die;
			}
		}

		//---------------Check Duplicate product in Same return number ------------------------//
		$duplicate = is_duplicate_field("b.id", "inv_receive_master a, inv_transaction b", "a.id=b.mst_id and a.recv_number=$txt_mrr_no and b.prod_id=$prodMSTID and b.transaction_type=1 and a.item_category=1");
		if ($duplicate == 1 && str_replace("'", "", $txt_mrr_no) == "") {
			echo "20**Duplicate Product is Not Allow in Same Return Number.";
			disconnect($con);
			die;
		}
		//------------------------------Check Duplicate product END---------------------------------------//

		//---------------Check yarn sticker page ------------------------//
		$sticker_cond = "";

		if (str_replace("'", "", $cbo_receive_basis) == 1) {
			$sticker_cond = " and receive_basis=1";
		} else if (str_replace("'", "", $cbo_receive_basis) == 2) {
			if (str_replace("'", "", $cbo_receive_purpose) == 2)
				$sticker_cond = " and receive_basis in(3,4)";
			else
				$sticker_cond = " and receive_basis=2";
		}

		if (str_replace("'", "", $cbo_receive_basis) == 1 || str_replace("'", "", $cbo_receive_basis) == 2) {
			$wo_pi_stiker = return_field_value("wo_pi_no", "com_yarn_bag_sticker", " status_active=1 and wo_pi_no=$txt_wo_pi_id  $sticker_cond", "wo_pi_no");

			if ($wo_pi_stiker != "") {
				echo "20**Yarn Sticker Found, Receive Not Allow.";
				disconnect($con);
				die;
			}
		}

		$sql = sql_select("select product_name_details,avg_rate_per_unit,last_purchased_qnty,current_stock,stock_value,available_qnty,allocated_qnty from product_details_master where id=$prodMSTID and status_active=1 and is_deleted=0");
		$presentStock = $presentStockValue = $presentAvgRate = $allocated_qnty = $available_qnty = 0;
		$product_name_details = "";
		foreach ($sql as $result) {
			$presentStock = $result[csf("current_stock")];
			$presentStockValue = $result[csf("stock_value")];
			$presentAvgRate = $result[csf("avg_rate_per_unit")];
			$product_name_details = $result[csf("product_name_details")];
			$available_qnty = $result[csf("available_qnty")];
			$allocated_qnty = $result[csf("allocated_qnty")];
		}
		//----------------Check Product ID END---------------------//

		if (str_replace("'", "", $txt_mrr_no) != "") {
			$new_recv_number[0] = str_replace("'", "", $txt_mrr_no);
			$prev_dataArray = sql_select("select id, currency_id from inv_receive_master where recv_number=$txt_mrr_no");
			$id = $prev_dataArray[0][csf('id')];
			$prev_currency_id = $prev_dataArray[0][csf('currency_id')];

			if (str_replace("'", "", $cbo_currency) != $prev_currency_id) {
				echo "30**Multiple Currency Not Allow In Same MRR Number";
				disconnect($con);
				die;
			}
			//yarn master table UPDATE here START----------------------//
			$field_array = "item_category*receive_basis*receive_purpose*receive_date*booking_id*challan_no*store_id*exchange_rate*currency_id*supplier_id*loan_party*yarn_issue_challan_no*issue_id*lc_no*source*remarks*updated_by*update_date";
			$data_array = "1*" . $cbo_receive_basis . "*" . $cbo_receive_purpose . "*" . $txt_receive_date . "*" . $txt_wo_pi_id . "*" . $txt_challan_no . "*" . $cbo_store_name . "*" . $txt_exchange_rate . "*" . $cbo_currency . "*" . $cbo_supplier . "*" . $cbo_party . "*" . $txt_issue_challan_no . "*" . $txt_issue_id . "*" . $hidden_lc_id . "*" . $cbo_source . "*" . $txt_mst_remarks . "*'" . $user_id . "'*'" . $pc_date_time . "'";
			//yarn master table UPDATE here END---------------------------------------//
		} else {
			// yarn master table entry here START---------------------------------------//
			$id = return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con);

			if ($db_type == 0) $year_cond = "YEAR(insert_date)";
			else if ($db_type == 2) $year_cond = "to_char(insert_date,'YYYY')";
			else $year_cond = "";

			$new_recv_number = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master",$con,1,$cbo_company_id,'YRV',1,date("Y",time()),1 ));

			$field_array = "id, recv_number_prefix, recv_number_prefix_num, recv_number, entry_form, item_category, company_id, receive_basis, receive_purpose, receive_date, booking_id, challan_no, store_id, exchange_rate, currency_id, supplier_id, loan_party, yarn_issue_challan_no, issue_id, lc_no, source,remarks, inserted_by, insert_date";
			$data_array = "(" . $id . ",'" . $new_recv_number[1] . "','" . $new_recv_number[2] . "','" . $new_recv_number[0] . "',1,1," . $cbo_company_id . "," . $cbo_receive_basis . "," . $cbo_receive_purpose . "," . $txt_receive_date . "," . $txt_wo_pi_id . "," . $txt_challan_no . "," . $cbo_store_name . "," . $txt_exchange_rate . "," . $cbo_currency . "," . $cbo_supplier . "," . $cbo_party . "," . $txt_issue_challan_no . "," . $txt_issue_id . "," . $hidden_lc_id . "," . $cbo_source . "," . $txt_mst_remarks . ",'" . $user_id . "','" . $pc_date_time . "')";
			// yarn master table entry here END---------------------------------------//
		}


		// yarn details table entry here START-----------------------------------//
		$rate = str_replace("'", "", $txt_rate);
		$txt_ile = str_replace("'", "", $txt_ile);
		$cbo_buyer_name = str_replace("'", "", $cbo_buyer_name);
		if ($txt_ile == '') $txt_ile = 0;
		$txt_receive_qty = str_replace("'", "", $txt_receive_qty);
		$ile = ($txt_ile / $rate) * 100; // ile cost to ile
		$ile_cost = str_replace("'", "", $txt_ile); //ile cost = (ile/100)*rate
		$order_amount=(($txt_receive_qty*$rate)+$ile_cost);
		$exchange_rate = str_replace("'", "", $txt_exchange_rate);
		$conversion_factor = 1; // yarn always KG
		$domestic_rate = return_domestic_rate($rate, $ile_cost, $exchange_rate, $conversion_factor);
		$cons_rate = number_format($domestic_rate, $dec_place[3], ".", "");//number_format($rate*$exchange_rate,$dec_place[3],".","");
		$con_amount = $cons_rate * $txt_receive_qty;
		$con_ile = $ile;//($ile/$domestic_rate)*100;
		$con_ile_cost = ($ile / 100) * ($rate * $exchange_rate);

		$dtlsid = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
		$field_array_trans = "id,mst_id,receive_basis,pi_wo_batch_no,company_id,supplier_id,prod_id,origin_prod_id,product_code,item_category,transaction_type,transaction_date,store_id,brand_id,order_uom,order_qnty,order_rate,order_ile,order_ile_cost,order_amount,cons_uom,cons_quantity,cons_rate,cons_avg_rate,dye_charge,cons_ile,cons_ile_cost,cons_amount,balance_qnty,balance_amount,no_of_bags,cone_per_bag,no_loose_cone,weight_per_bag,weight_per_cone,room,rack,self,bin_box,floor_id,remarks,job_no,buyer_id,inserted_by,insert_date,pi_wo_req_dtls_id,grey_quantity";

		$data_array_trans = "(" . $dtlsid . "," . $id . "," . $cbo_receive_basis . "," . $txt_wo_pi_id . "," . $cbo_company_id . "," . $cbo_supplier . "," . $prodMSTID . "," . $prodMSTID . "," . $txt_prod_code . ",1,1," . $txt_receive_date . "," . $cbo_store_name . "," . $txt_brand . "," . $cbo_uom . "," . $txt_receive_qty . "," . $txt_rate . "," . $ile . "," . $ile_cost . "," . $order_amount . "," . $cbo_uom . "," . $txt_receive_qty . "," . $cons_rate . "," . $txt_avg_rate . "," . $txt_dyeing_charge . "," . $con_ile . "," . $con_ile_cost . "," . $con_amount . "," . $txt_receive_qty . "," . $con_amount . "," . $txt_no_bag . "," . $txt_cone_per_bag . "," . $txt_no_loose_cone . "," . $txt_weight_per_bag . "," . $txt_weight_per_cone . "," . $cbo_room . "," . $txt_rack . "," . $txt_shelf . "," . $cbo_bin . "," . $cbo_floor . "," . $txt_remarks . "," . $job_no . ",'" . $cbo_buyer_name . "','" . $user_id . "','" . $pc_date_time . "'," . $txt_wo_pi_dtls_id . "," . $txt_grey_qty . ")";
		//yarn details table entry here END-----------------------------------//

		//product master table data UPDATE START----------------------------------------------------------//
		$stock_value = $domestic_rate * $txt_receive_qty;
		$currentStock = $presentStock + $txt_receive_qty;

		$StockValue = $presentStockValue + $stock_value;
		$avgRate = $StockValue / $currentStock;
		$is_without_order = return_field_value("entry_form", "wo_yarn_dyeing_mst", " status_active=1 and id=$txt_wo_pi_id", "entry_form");

		$is_with_order_yarn_service_work_order = return_field_value("booking_without_order", "wo_yarn_dyeing_mst", " status_active=1 and id=$txt_wo_pi_id", "booking_without_order");

		// yarn allocation variable set to yes
		if ($variable_set_allocation == 1) {
			if ((str_replace("'", "", $cbo_receive_purpose) == 2 || str_replace("'", "", $cbo_receive_purpose) == 12 || str_replace("'", "", $cbo_receive_purpose) == 15 || str_replace("'", "", $cbo_receive_purpose) == 38 || str_replace("'", "", $cbo_receive_purpose) == 46 || str_replace("'", "", $cbo_receive_purpose) == 50 || str_replace("'", "", $cbo_receive_purpose) == 51 ) && (str_replace("'", "", $cbo_receive_basis) == 2)) 
			{
				if ($is_without_order == 135 || ( $is_without_order == 94 && $is_with_order_yarn_service_work_order==2) ) 
				{ 
					$allocated_qnty = $allocated_qnty;
					$available_qnty = $available_qnty + $txt_receive_qty;

				} else {
					$allocated_qnty = $allocated_qnty + $txt_receive_qty;
					$available_qnty = $available_qnty;
				}
			} else {
				$allocated_qnty = $allocated_qnty;
				$available_qnty = $available_qnty + $txt_receive_qty;
			}
		} else {

			$allocated_qnty = $allocated_qnty;
			$available_qnty = $available_qnty + $txt_receive_qty;
		}

		if (str_replace("'", "", $txt_brand) == "") $txt_brand = 0;
		$field_array_prod_update = "brand*avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*allocated_qnty*available_qnty*updated_by*update_date";
		$data_array_prod_update = "" . $txt_brand . "*" . number_format($avgRate, $dec_place[3], ".", "") . "*" . $txt_receive_qty . "*" . $currentStock . "*" . number_format($StockValue, $dec_place[4], ".", "") . "*'" . $allocated_qnty . "'*'" . $available_qnty . "'*'" . $user_id . "'*'" . $pc_date_time . "'";

		if ($variable_set_allocation == 1) {
			// update dyied yarn allocation start
			if ((str_replace("'", "", $cbo_receive_purpose) == 2 || str_replace("'", "", $cbo_receive_purpose) == 12 || str_replace("'", "", $cbo_receive_purpose) == 15 || str_replace("'", "", $cbo_receive_purpose) == 38 || str_replace("'", "", $cbo_receive_purpose) == 46 || str_replace("'", "", $cbo_receive_purpose) == 50 || str_replace("'", "", $cbo_receive_purpose) == 51 ) && (str_replace("'", "", $cbo_receive_basis) == 2)) 
			{
				if (str_replace("'", "", $cbo_receive_purpose) == 2 && ( $is_without_order == 42 || $is_without_order == 114))
				{
					//$allocation_mst_insert = 1;
					//$allocation_dtls_insert = 1;
					//for booking no
					$sqlBookingNo = "SELECT c.id, c.booking_no FROM wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b, wo_non_ord_samp_booking_mst c WHERE a.id = b.mst_id AND b.booking_no = c.booking_no AND a.id = ".$txt_wo_pi_id." AND a.status_active = 1 AND a.is_deleted = 0 AND a.entry_form in (42,114)";
					//AND b.product_id = ".$prodMSTID."
					//oci_rollback($con);
					//echo "10**".$sqlBookingNo; die;
					
					$sqlBookingNo = sql_select($sqlBookingNo);
					$bookingId = '';
					$bookingNo = '';
					foreach($sqlBookingNo as $row)
					{
						$bookingId = $row[csf('id')];
						$bookingNo = $row[csf('booking_no')];
					}

					$check_allocation_info = sql_select("SELECT a.id, a.qnty FROM inv_material_allocation_mst a WHERE a.booking_no = '".$bookingNo."' AND a.item_id = ".$prodMSTID." AND a.is_dyied_yarn = 1 AND a.status_active = 1 AND a.is_deleted = 0");
					if (!empty($check_allocation_info))
					{
						$allocation_id = $check_allocation_info[0][csf('id')];
						$allocation_qnty = $check_allocation_info[0][csf('qnty')] + $txt_receive_qty;
						$field_allocation = "qnty*updated_by*update_date";
						$data_allocation = "". $allocation_qnty . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

						$allocation_dtls_id = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
						$field_allocation_dtls = "id,mst_id,job_no,item_category,allocation_date,item_id,qnty,is_dyied_yarn,inserted_by,insert_date";
						$data_allocation_dtls = "(" . $allocation_dtls_id . "," . $allocation_id . "," . $job_no . ",1," . $txt_receive_date . "," . $prodMSTID . "," . $allocation_qnty . ",1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

						$allocation_mst_insert = sql_update("inv_material_allocation_mst", $field_allocation, $data_allocation, "id", "" . $allocation_id . "", 0);
						$allocation_dtls_delete = execute_query("delete from inv_material_allocation_dtls where mst_id='$allocation_id'", 1);
						if ($allocation_mst_insert)
						{
							$allocation_dtls_insert = sql_insert("inv_material_allocation_dtls", $field_allocation_dtls, $data_allocation_dtls, 0);
						}
					}
					else
					{
						$allocation_id = return_next_id_by_sequence("INV_ALLOCATION_MST_PK_SEQ", "inv_material_allocation_mst", $con);
						$field_allocation = "id,mst_id,entry_form,job_no,item_category,allocation_date,item_id,qnty,is_dyied_yarn,po_break_down_id,booking_no,booking_without_order,inserted_by,insert_date";
						$data_allocation = "(" . $allocation_id . ",".$id.",1," . $job_no . ",1" . "," . $txt_receive_date . "," . $prodMSTID . "," . $txt_receive_qty . ",1,".$bookingId.",'".$bookingNo."',1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

						$allocation_dtls_id = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
						$field_allocation_dtls = "id,mst_id,job_no,item_category,allocation_date,item_id,qnty,is_dyied_yarn,po_break_down_id,booking_no,inserted_by,insert_date";
						$data_allocation_dtls = "(" . $allocation_dtls_id . "," . $allocation_id . "," . $job_no . ",1," . $txt_receive_date . "," . $prodMSTID . "," . $txt_receive_qty . ",1,".$bookingId.",'".$bookingNo."'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

						$allocation_mst_insert = sql_insert("inv_material_allocation_mst", $field_allocation, $data_allocation, 0);

						if ($allocation_mst_insert)
						{
							$allocation_dtls_insert = sql_insert("inv_material_allocation_dtls", $field_allocation_dtls, $data_allocation_dtls, 0);
						}
					}
				}
				//check_allocation_info
				else if ($is_without_order == 135 || ( $is_without_order == 94 && $is_with_order_yarn_service_work_order==2) )
				{
					$allocation_mst_insert = 1;
					$allocation_dtls_insert = 1;
				}
				else
				{
					$check_allocation_info = sql_select("select a.* from inv_material_allocation_mst a where a.job_no=$job_no and a.item_id=$prodMSTID and a.is_dyied_yarn=1 and a.status_active=1 and a.is_deleted=0");
					if (!empty($check_allocation_info))
					{
						$allocation_id = $check_allocation_info[0][csf('id')];
						$allocation_qnty = $check_allocation_info[0][csf('qnty')] + $txt_receive_qty;
						$field_allocation = "qnty*updated_by*update_date";
						$data_allocation = "" . $allocation_qnty . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

						$allocation_dtls_id = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
						$field_allocation_dtls = "id,mst_id,job_no,item_category,allocation_date,item_id,qnty,is_dyied_yarn,inserted_by,insert_date";
						$data_allocation_dtls = "(" . $allocation_dtls_id . "," . $allocation_id . "," . $job_no . ",1," . $txt_receive_date . "," . $prodMSTID . "," . $allocation_qnty . ",1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

						$allocation_mst_insert = sql_update("inv_material_allocation_mst", $field_allocation, $data_allocation, "id", "" . $allocation_id . "", 0);
						$allocation_dtls_delete = execute_query("delete from inv_material_allocation_dtls where mst_id='$allocation_id'", 1);
						if ($allocation_mst_insert)
						{
							$allocation_dtls_insert = sql_insert("inv_material_allocation_dtls", $field_allocation_dtls, $data_allocation_dtls, 0);
						}
					}
					else
					{

						$allocation_id = return_next_id_by_sequence("INV_ALLOCATION_MST_PK_SEQ", "inv_material_allocation_mst", $con);
						$field_allocation = "id,mst_id,entry_form,job_no,item_category,allocation_date,item_id,qnty,is_dyied_yarn,inserted_by,insert_date";
						$data_allocation = "(" . $allocation_id . ",".$id.",1," . $job_no . ",1" . "," . $txt_receive_date . "," . $prodMSTID . "," . $txt_receive_qty . ",1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

						$allocation_dtls_id = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
						$field_allocation_dtls = "id,mst_id,job_no,item_category,allocation_date,item_id,qnty,is_dyied_yarn,inserted_by,insert_date";
						$data_allocation_dtls = "(" . $allocation_dtls_id . "," . $allocation_id . "," . $job_no . ",1," . $txt_receive_date . "," . $prodMSTID . "," . $txt_receive_qty . ",1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

						$allocation_mst_insert = sql_insert("inv_material_allocation_mst", $field_allocation, $data_allocation, 0);

						if ($allocation_mst_insert) {
							$allocation_dtls_insert = sql_insert("inv_material_allocation_dtls", $field_allocation_dtls, $data_allocation_dtls, 0);
						}
					}
				}
			} 
			else 
			{
				$allocation_mst_insert = 1;
				$allocation_dtls_insert = 1;
			}
		} else {

			$allocation_mst_insert = 1;
			$allocation_dtls_insert = 1;
		}

		if (str_replace("'", "", $txt_mrr_no) != "") {
			$rID = sql_update("inv_receive_master", $field_array, $data_array, "id", $id, 0);
		} else {
			$rID = sql_insert("inv_receive_master", $field_array, $data_array, 0);
		}


		$dtlsrID = sql_insert("inv_transaction", $field_array_trans, $data_array_trans, 0);
		//echo "10**insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;die;

		// update allocation end
		$prodUpdate = sql_update("product_details_master", $field_array_prod_update, $data_array_prod_update, "id", $prodMSTID, 1);
		//echo "10**".$rID ."&&". $dtlsrID ."&&". $prodUpdate ."&&". $insertR ."&&". $allocation_mst_insert ."&&". $allocation_dtls_insert;die;
		if ($db_type == 0) {
			if ($rID && $dtlsrID && $prodUpdate && $insertR && $allocation_mst_insert && $allocation_dtls_insert) {
				mysql_query("COMMIT");
				echo "0**" . $new_recv_number[0];
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . $new_recv_number[0];
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $dtlsrID && $prodUpdate && $insertR && $allocation_mst_insert && $allocation_dtls_insert) {
				oci_commit($con);
				echo "0**" . $new_recv_number[0];
			} else {
				oci_rollback($con);
				echo "10**" . $new_recv_number[0];
			}
		}
		//check_table_status($_SESSION['menu_id'], 0);
		disconnect($con);
		die;
	}
	else if ($operation == 1) // Update Here----------------------------------------------------------
	{
		$cbo_receive_purpose=str_replace("'", "", $cbo_receive_purpose);

		$prev_data = sql_select("select a.id,a.currency_id,count(b.id) as dtls_row from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.recv_number=$txt_mrr_no and b.id!=$update_id and a.entry_form=1 and b.item_category=1 and b.transaction_type=1 group by a.id, a.currency_id");

		if ($prev_data[0][csf('dtls_row')] > 0) {
			if (str_replace("'", "", $cbo_currency) != $prev_data[0][csf('currency_id')]) {
				echo "30**Multiple Currency Not Allow In Same MRR Number";
				disconnect($con);die;
			}
		}
		//---------------Check Receive control on Gate Entry according to variable settings inventory---------------------------//
		if ($variable_set_invent == 1) {
			$challan_no = str_replace("'", "", $txt_challan_no);
			if ($challan_no != "") {
				$variable_set_invent = return_field_value("a.id as id", " inv_gate_in_mst a,  inv_gate_in_dtl b", "a.id=b.mst_id and a.company_id=$cbo_company_id and a.challan_no='$challan_no' and b.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "id");
				if (empty($variable_set_invent)) {
					echo "30** This Item Not Found In Gate Entry. \n Please Gate Entry First.";
					disconnect($con);die;
				}
			}
		}
		$mrr_issue_check = return_field_value("sum(issue_qnty) issue_qnty", "inv_mrr_wise_issue_details", "recv_trans_id=$update_id and status_active=1 and	is_deleted=0", "issue_qnty");
		if (str_replace("'", "", $txt_receive_qty) < $mrr_issue_check) {
			echo "30**Receive quantity can not be less than Issue quantity";
			disconnect($con);die;
		}
		//---------------End Check Receive control on Gate Entry---------------------------//

		//-------------Yarn Service Booking. Receive can not be greater than service booking----//

		if((str_replace("'", "", $cbo_receive_basis) == 2))
		{
			$cbo_receive_purpose=str_replace("'", "", $cbo_receive_purpose);
			$cbo_color=str_replace("'", "", $cbo_color);

			$sql_exis_servc_recvQnty=sql_select("select listagg(a.recv_number_prefix_num, ',') within group (order by a.id) as rcv_no,a.booking_id,sum(b.cons_quantity) as recv_quantity,sum(b.grey_quantity) as grey_quantity from inv_receive_master a, inv_transaction b left join product_details_master c on b.prod_id=c.id where a.id=b.mst_id and b.id != $update_id and a.company_id=$cbo_company_id and a.booking_id=$txt_wo_pi_id and b. transaction_type=1 and a.entry_form=1 and a.receive_basis=2 and a.receive_purpose=$cbo_receive_purpose and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.yarn_type=$cbo_yarn_type and c.color=$cbo_color and c.yarn_count_id=$cbo_yarn_count  and c.yarn_comp_type1st=$cbocomposition1 and c.yarn_comp_type2nd=$cbocomposition2 and yarn_comp_percent1st=$percentage1 group by a.booking_id");

			if ($cbo_receive_purpose == 2 || $cbo_receive_purpose == 12 || $cbo_receive_purpose == 15 || $cbo_receive_purpose == 38 || $cbo_receive_purpose == 46 || $cbo_receive_purpose == 50 || $cbo_receive_purpose == 51 )
			{
				$sql_wo_yarn_qnty=sql_select("select a.id,sum(b.yarn_wo_qty) as yarn_wo_qty from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.company_id=$cbo_company_id and a.id=$txt_wo_pi_id and a.entry_form in(41,42,94,114,125) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by a.id");
			}
			else
			{
				$sql_wo_yarn_qnty=sql_select("select a.id,sum(b.supplier_order_quantity) as yarn_wo_qty from wo_non_order_info_mst a, wo_non_order_info_dtls b where a.id=b.mst_id and a.company_name=$cbo_company_id and a.id=$txt_wo_pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by a.id");
			}
			//issue check only for service
			//issue check only for service
			if ($cbo_receive_purpose == 2 || $cbo_receive_purpose == 12 || $cbo_receive_purpose == 15 || $cbo_receive_purpose == 38 || $cbo_receive_purpose == 46 || $cbo_receive_purpose == 50 || $cbo_receive_purpose == 51 )
			{

				if($cbo_receive_purpose == 15 || $cbo_receive_purpose == 50 || $cbo_receive_purpose == 51)
				{
					$sql_issue_qnty=sql_select("select sum(b.cons_quantity) as cons_quantity from inv_issue_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id and a.company_id=$cbo_company_id and a.issue_basis=1 and a.issue_purpose in(15,50,51) and a. item_category=1 and a.entry_form= 3 and b.receive_basis = 1 and b.item_category=1 and b.transaction_type=2 and a.booking_id=$txt_wo_pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
				}
				else
				{
					if($cbo_receive_purpose == 2)
					{
						$color_cond = "and b.dyeing_color_id=$cbo_color";	
					}
					
					$sql_issue_qnty=sql_select("select sum(b.cons_quantity) as cons_quantity from inv_issue_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id and a.company_id=$cbo_company_id and a.issue_basis=1 and a.issue_purpose in(2,12,38,46) and a. item_category=1 and a.entry_form= 3 and b.receive_basis = 1 and b.item_category=1 and b.transaction_type=2 and a.booking_id=$txt_wo_pi_id and c.yarn_type=$cbo_yarn_type and c.yarn_count_id=$cbo_yarn_count  and c.yarn_comp_type1st=$cbocomposition1 and yarn_comp_percent1st=$percentage1 $color_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
				}
				
				$issueQnty_service=$sql_issue_qnty[0][csf('cons_quantity')];
			}
			//query for deduction receive return qunatity
			$sql_return_recev=	sql_select("select a.recv_number, sum(c.cons_quantity  ) as issue_qnty from  inv_receive_master a, inv_issue_master b,inv_transaction c left join product_details_master d on c.prod_id=d.id and d.status_active=1 and d.is_deleted=0 where a.booking_id=$txt_wo_pi_id and a.id=b.received_id and b.id=c.mst_id and a.entry_form = 1 and a.receive_basis = 2 and a.status_active=1 and b.status_active=1 and c.transaction_type=3 and b.entry_form=8 and d.color=$cbo_color and d.yarn_count_id=$cbo_yarn_count and d.yarn_comp_type1st=$cbocomposition1 and d.yarn_comp_type2nd=$cbocomposition2 and d.yarn_comp_percent1st=$percentage1 and d.yarn_type=$cbo_yarn_type");

			$return_receiveQnty=$sql_return_recev[0][csf('issue_qnty')];

			$txt_receive_qty = str_replace("'", "", $txt_receive_qty);
			$total_recvQnty=$sql_exis_servc_recvQnty[0][csf('recv_quantity')]+$txt_receive_qty;
			$woYarnQnty=$sql_wo_yarn_qnty[0][csf('yarn_wo_qty')];

			$over_receive_limit_qnty = ($over_receive_limit>0)?($over_receive_limit / 100) * $woYarnQnty:0;

			if ($cbo_receive_purpose == 2 || $cbo_receive_purpose == 12 || $cbo_receive_purpose == 15 || $cbo_receive_purpose == 38 || $cbo_receive_purpose == 46 || $cbo_receive_purpose == 50 || $cbo_receive_purpose == 51)
			{
				$issud_msg="Issue";
				$wo_qnty = $issueQnty_service;
				$allow_total_val=$issueQnty_service + $over_receive_limit_qnty;
				$overRecvLimitMsg="Over Receive limit = $over_receive_limit% ($over_receive_limit_qnty Kg.)";
			}
			else
			{
				$issud_msg="";
				$wo_qnty = $woYarnQnty;
				$allow_total_val = $woYarnQnty + $over_receive_limit_qnty;
				$overRecvLimitMsg="Over Receive limit = $over_receive_limit% ($over_receive_limit_qnty Kg.)";
			}

			$allow_total_val = number_format($allow_total_val,2,'.','');
			$balance = number_format(($total_recvQnty-$return_receiveQnty),2,'.','');


			// Grey used qnty validation		
			if( $cbo_receive_purpose== 2 || $cbo_receive_purpose==12 || $cbo_receive_purpose==15 || $cbo_receive_purpose==38 )
			{
				$txt_grey_qty = str_replace("'", "", $txt_grey_qty)*1;
				$txt_receive_qty = str_replace("'", "", $txt_receive_qty)*1;

				$hdn_grey_qty = str_replace("'", "", $hdn_grey_qty);
				$total_greyQnty=($sql_exis_servc_recvQnty[0][csf('grey_quantity')]-$hdn_grey_qty)+$txt_grey_qty;
				//-$return_receiveQnty
				$grey_quantity_balance = number_format(($total_greyQnty),2,'.','');

				if($txt_grey_qty<$txt_receive_qty)
				{
					echo "40**Grey quantity can not be less than received quantity";
					disconnect($con);die;
				}

				if( ($allow_total_val-$over_receive_limit_qnty)<$grey_quantity_balance )
				{
					$thismrrGreyQty = str_replace("'", "", $hdn_grey_qty);
					$previousGreyQty = $sql_exis_servc_recvQnty[0][csf('grey_quantity')];
					$rcvNumbers = $sql_exis_servc_recvQnty[0][csf('rcv_no')];
					$actualGreyQty = (($previousGreyQty+$thismrrGreyQty)); 
					//$over_grey_msg = ($over_receive_limit>0)?"\nAllowed Quantity = $allow_total_val":"";
					$allowedGreyBalance = number_format(($wo_qnty-$actualGreyQty),2,'.','');

					echo "40**Grey quantity can not be greater than $issud_msg quantity.\n$issud_msg quantity = $wo_qnty\nReceived No=$rcvNumbers\nGrey Qty = $actualGreyQty\nBalance = $allowedGreyBalance";
					disconnect($con);die;	
				}

			}

			if($allow_total_val<$balance) 
			{
				$thismrrRcv = str_replace("'", "", $hdn_receive_qty);
				$receivedNumber = $sql_exis_servc_recvQnty[0][csf('rcv_no')];
				$previousRcv = $sql_exis_servc_recvQnty[0][csf('recv_quantity')];
				$actualRcv = (($previousRcv+$thismrrRcv)-$return_receiveQnty); 
				$over_msg = ($over_receive_limit>0)?"\nAllowed Quantity = $allow_total_val":"";
				$allowedBalance = number_format(($wo_qnty-$actualRcv),2,'.','');
				echo "40**Recv. quantity can not be greater than $issud_msg quantity.\n$issud_msg quantity = $wo_qnty \nReceived No=$receivedNumber\nReceived Qty = $actualRcv\n$overRecvLimitMsg $over_msg\nBalance = $allowedBalance";
				disconnect($con);die;
			}
		}
		//-------------End Yarn Service Booking. Receive can not be greater than service booking ----//

		//previous product stock adjust here--------------------------//
		//product master table UPDATE here START ---------------------//

		if( (str_replace("'", "", $cbo_receive_basis) == 1)  && $cbo_receive_purpose == 16 )
		{
			$hdn_receive_qty = str_replace("'", "", $hdn_receive_qty);
			$txt_receive_qty = str_replace("'", "", $txt_receive_qty);
			$txt_rate = str_replace("'", "", $txt_rate);

			if($txt_wo_pi_id!="")
			{
				$total_rcv_value = return_field_value("sum(order_qnty*order_rate) as rcv_value", "inv_receive_master a, inv_transaction b", "a.id=b.mst_id and a.booking_id=$txt_wo_pi_id and b.pi_wo_batch_no=$txt_wo_pi_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0", "rcv_value");

				$current_acceptance_value = return_field_value("a.current_acceptance_value", "com_import_invoice_dtls a", "a.pi_id=$txt_wo_pi_id and a.status_active=1 and  a.is_deleted=0", "current_acceptance_value");
			}

			$previous_amount = 	($hdn_receive_qty*$txt_rate);
			$current_text_amount = ($txt_receive_qty*$txt_rate);

			$pi_against_current_rcv = ($total_rcv_value-$previous_amount)+$current_text_amount;
			$reduce_value = ($total_rcv_value-$current_acceptance_value);

			//echo "40**ttt".$total_rcv_value."==".$txt_amount."==".$pi_against_current_rcv; die();
			if($pi_against_current_rcv<$current_acceptance_value)
			{
				$current_not_allowed_value = ($current_acceptance_value-$current_text_amount);

				echo "40**Total Payable value=  $total_rcv_value\n\nTotal Accp.value= $current_acceptance_value\n\nAllowed to reduce value=$reduce_value\n\nSo current reduce value $current_not_allowed_value is not allowed";
				disconnect($con);die;
			}
		}

		$sql = sql_select("select a.prod_id,a.cons_quantity,a.cons_rate,a.cons_amount,a.balance_qnty,a.balance_amount,b.avg_rate_per_unit,b.current_stock,b.stock_value,b.allocated_qnty,b.available_qnty,c.receive_purpose,c.receive_basis,b.dyed_type from inv_transaction a, product_details_master b, inv_receive_master c where a.id=$update_id and a.prod_id=b.id and a.mst_id=c.id and c.entry_form=1 and a.status_active=1 and b.status_active=1 and c.status_active=1");
		$before_prod_id = $before_receive_qnty = $before_rate = $beforeAmount = $beforeBalanceQnty = $beforeBalanceAmount = 0;
		$before_brand = "";
		$beforeStock = $beforeStockValue = $beforeAvgRate = $before_allocated_qnty = $before_available_qnty = $adj_allocated_qnty = $adj_beforeAvailableQnty = 0;
		foreach ($sql as $row) {
			$before_prod_id = $row[csf("prod_id")];
			$before_receive_qnty = $row[csf("cons_quantity")]; //stock qnty
			$before_rate = $row[csf("cons_rate")];
			$beforeAmount = $row[csf("cons_amount")]; //stock value
			$beforeBalanceQnty = $row[csf("balance_qnty")];
			$beforeBalanceAmount = $row[csf("balance_amount")];

			$before_brand = $row[csf("brand")];
			$beforeStock = $row[csf("current_stock")];
			$beforeStockValue = $row[csf("stock_value")];
			$beforeAvgRate = $row[csf("avg_rate_per_unit")];
			$before_available_qnty = $row[csf("available_qnty")];
			$before_allocated_qnty = $row[csf("allocated_qnty")];
			$before_receive_purpose = $row[csf("receive_purpose")];
			$before_receive_basis = $row[csf("receive_basis")];
			$dyed_type = $row[csf("dyed_type")];
		}

		if($dyed_type!=1 && (str_replace("'", "", $txt_receive_qty) < ($before_allocated_qnty-$before_receive_qnty))){
			echo "30**Receive quantity can not be less than Allocation quantity.\nAllocation Quantity=".$before_allocated_qnty;
			disconnect($con);
			die;
		}

		//stock value minus here---------------------------//
		$adj_beforeStock = $beforeStock - $before_receive_qnty;
		$adj_beforeStockValue = $beforeStockValue - $beforeAmount;
		if ($adj_beforeStock == 0) {
			$adj_beforeAvgRate = 0;
		} else {
			$adj_beforeAvgRate = number_format(($adj_beforeStockValue / $adj_beforeStock), $dec_place[3], '.', '');
		}
		$is_without_order = return_field_value("entry_form", "wo_yarn_dyeing_mst", " status_active=1 and id=$txt_wo_pi_id", "entry_form");

		$is_with_order_yarn_service_work_order = return_field_value("booking_without_order", "wo_yarn_dyeing_mst", " status_active=1 and id=$txt_wo_pi_id", "booking_without_order");

		if ($variable_set_allocation == 1) {
			// update dyied yarn allocation start
			if (($before_receive_purpose == 2 || $before_receive_purpose == 12 || $before_receive_purpose == 15 || $before_receive_purpose == 38 || $before_receive_purpose == 46 || $before_receive_purpose == 50 || $before_receive_purpose == 51) && $before_receive_basis == 2) {
				if ( $is_without_order == 135 || ( $is_without_order == 94 && $is_with_order_yarn_service_work_order==2) ) {
					$adj_allocated_qnty = $before_allocated_qnty;
					$adj_beforeAvailableQnty = $before_available_qnty - $before_receive_qnty;
				} else {
					$adj_allocated_qnty = $before_allocated_qnty - $before_receive_qnty;
					$adj_beforeAvailableQnty = $before_available_qnty;
				}
			} else {
				$adj_allocated_qnty = $before_allocated_qnty;
				$adj_beforeAvailableQnty = $before_available_qnty - $before_receive_qnty;
			}
		}
		//product master table UPDATE here END   ---------------------//
		//----------------- END PREVIOUS STOCK ADJUST-----------------//


		//---------------Check Color---------------------------//
		
		/* old color generate system 
		if (str_replace("'", "", $btn_color) == 'F') {
			$color_library = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
			$cbo_color = return_id(str_replace("'", "", $cbo_color), $color_library, "lib_color", "id,color_name");
		}
		*/
		// Simillar like order entry page
		if (str_replace("'", "", $btn_color) == 'F') {

			if(str_replace("'","",$cbo_color)!="")
			{
				$color_library = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');

				if (!in_array(str_replace("'","",$cbo_color),$new_array_color))
				{
					$color_id = return_id( str_replace("'","",$cbo_color), $color_library, "lib_color", "id,color_name","1");
					//echo $cbo_color.'='.$color_id.'<br>';
					$new_array_color[$color_id]=str_replace("'","",$cbo_color);

				}
				else $color_id = array_search(str_replace("'","",$cbo_color), $new_array_color);
			}
			else
			{
				$color_id=0;
			}
		}else{
			$color_id = str_replace("'","",$cbo_color);
		}

		//----------------Check Color END---------------------//


		//---------------Check Brand---------------------------//
		if (str_replace("'", "", $txt_brand) != "") {
			//$brand_library = return_library_array("select id,brand_name from lib_brand", 'id', 'brand_name');
			//$txt_brand = return_id(str_replace("'", "", $txt_brand), $brand_library, "lib_brand", "id,brand_name");
			
			$brand_arr=return_library_array( "select id, brand_name from lib_brand where status_active=1",'id','brand_name');
		
			if (str_replace("'", "", trim($txt_brand)) != "") {
				if (!in_array(str_replace("'", "", trim($txt_brand)),$new_array_brand)){
					$brand_id = return_id( str_replace("'", "", trim($txt_brand)), $brand_arr, "lib_brand", "id,brand_name","1");
					$new_array_brand[$brand_id]=str_replace("'", "", trim($txt_brand));
				}
				else $brand_id =  array_search(str_replace("'", "", trim($txt_brand)), $new_array_brand);
			} else $brand_id = 0;
			
			$txt_brand =$brand_id;
		}
		//----------------Check Brand END---------------------//
		//---------------Check Product ID --------------------------//
		$insertR = true;
		$rtnString = return_product_id($cbo_yarn_count, $cbocomposition1, $cbocomposition2, $percentage1, $percentage2, $cbo_yarn_type, $color_id, $txt_yarn_lot, $txt_prod_code, $cbo_company_id, $cbo_supplier, $cbo_store_name, $cbo_uom, $yarn_type, $composition,$cbo_receive_purpose);


		$expString = explode("***", $rtnString);
		if ($expString[0] == true) {
			$prodMSTID = $expString[1];
		} else {
			if ($adj_beforeStock < 0) {
				echo "30** Stock cannot be less than zero.";
				disconnect($con);
				die;
			}

			$field_array_prod = $expString[1];
			$data_array_prod = $expString[2];
			$prodMSTID = $expString[3];
		}

		if ($before_prod_id != $prodMSTID) {
			if($db_type==0){

				$check_requisition = sql_select("select group_concat(requisition_no) as requisition_no from ppl_yarn_requisition_entry where prod_id=$before_prod_id and status_active=1 and is_deleted=0");
				if (!empty($check_requisition) && $check_requisition[0][csf('requisition_no')] != "") {
					echo "13**Lot can not be changed.Requisition found(".$check_requisition[0][csf('requisition_no')].")."; disconnect($con);
					mysql_query("ROLLBACK"); die;
				}
			}else{
				$check_requisition = sql_select("select listagg(requisition_no, ',') within group (order by requisition_no) as requisition_no from ppl_yarn_requisition_entry where prod_id=$before_prod_id and status_active=1 and is_deleted=0");
				if (!empty($check_requisition) && $check_requisition[0][csf('requisition_no')] != "") {
					echo "13**Lot can not be changed.Requisition found(".$check_requisition[0][csf('requisition_no')].")."; disconnect($con);
					disconnect($con);oci_rollback($con); die;
				}
			}
		}

        //---------------Check Receive Date with Issue Date -----------//
		$max_transaction_date = return_field_value("max(transaction_date) as max_date", "inv_transaction", "prod_id=$prodMSTID and store_id= $cbo_store_name and id <> $update_id  and status_active = 1", "max_date");
		if($max_transaction_date != "")
		{
			$max_transaction_date = date("Y-m-d", strtotime($max_transaction_date));
			$receive_date = date("Y-m-d", strtotime(str_replace("'", "", $txt_receive_date)));
			if ($receive_date < $max_transaction_date)
			{
				echo "20**Receive Date Can not Be Less Than Last Transaction Date Of This Lot";
				disconnect($con);
				die;
			}
		}

		//current product stock-------------------------//
		$sql = sql_select("select product_name_details,avg_rate_per_unit,last_purchased_qnty,current_stock,stock_value,available_qnty,allocated_qnty from product_details_master where id=$prodMSTID");
		$presentStock = $presentStockValue = $presentAvgRate = $allocated_qnty = $available_qnty = 0;
		$product_name_details = "";
		foreach ($sql as $result) {
			$presentStock = $result[csf("current_stock")];
			$presentStockValue = $result[csf("stock_value")];
			$presentAvgRate = $result[csf("avg_rate_per_unit")];
			$product_name_details = $result[csf("product_name_details")];
			$available_qnty = $result[csf("available_qnty")];
			$allocated_qnty = $result[csf("allocated_qnty")];
		}
		if ($allocated_qnty == "") $allocated_qnty = 0;
		if ($available_qnty == "") $available_qnty = 0;
		//----------------Check Product ID END---------------------//

		//yarn master table UPDATE here START----------------------//
		$field_array_update = "item_category*receive_date*challan_no*store_id*loan_party*yarn_issue_challan_no*issue_id*lc_no*remarks	*updated_by*update_date";
		$data_array_update = "1*" . $txt_receive_date . "*" . $txt_challan_no . "*" . $cbo_store_name . "*" . $cbo_party . "*" . $txt_issue_challan_no . "*" . $txt_issue_id . "*" . $hidden_lc_id . "*" . $txt_mst_remarks . "*'" . $user_id . "'*'" . $pc_date_time . "'";

		// yarn details table UPDATE here START-----------------------------------//
		$rate = str_replace("'", "", $txt_rate);
		$txt_ile = str_replace("'", "", $txt_ile);
		$cbo_buyer_name = str_replace("'", "", $cbo_buyer_name);
		if ($txt_ile == '') $txt_ile = 0;
		$txt_receive_qty = str_replace("'", "", $txt_receive_qty);
		$hdn_receive_qty = str_replace("'", "", $hdn_receive_qty);
		$ile = ($txt_ile / $rate) * 100; // ile cost to ile
		$ile_cost = str_replace("'", "", $txt_ile); //ile cost = (ile/100)*rate
		$order_amount=(($txt_receive_qty*$rate)+$ile_cost);
		$exchange_rate = str_replace("'", "", $txt_exchange_rate);
		$conversion_factor = 1; // yarn always KG
		$domestic_rate = return_domestic_rate($rate, $ile_cost, $exchange_rate, $conversion_factor);
		$cons_rate = number_format($domestic_rate, $dec_place[3], ".", "");//number_format($rate*$exchange_rate,$dec_place[3],".","");

		$con_amount = $cons_rate * $txt_receive_qty;
		$con_ile = $ile;
		$con_ile_cost = ($ile / 100) * ($rate * $exchange_rate);

		$adjBalanceQnty = $beforeBalanceQnty - $before_receive_qnty + $txt_receive_qty;
		$adjBalanceAmount = $beforeBalanceAmount - $beforeAmount + $con_amount;

		$field_array_trans = "receive_basis*pi_wo_batch_no*company_id*supplier_id*prod_id*origin_prod_id*product_code*item_category*transaction_type*transaction_date*store_id*brand_id*order_uom*order_qnty*order_rate*order_ile*order_ile_cost*order_amount*cons_uom*cons_quantity*cons_rate*cons_avg_rate*dye_charge*cons_ile*cons_ile_cost*cons_amount*balance_qnty*balance_amount*no_of_bags*cone_per_bag*no_loose_cone*weight_per_bag*weight_per_cone*room*rack*self*bin_box*floor_id*remarks*job_no*buyer_id*updated_by*update_date*pi_wo_req_dtls_id*grey_quantity";
		$data_array_trans = "" . $cbo_receive_basis . "*" . $txt_wo_pi_id . "*" . $cbo_company_id . "*" . $cbo_supplier . "*" . $prodMSTID . "*" . $prodMSTID . "*" . $txt_prod_code . "*1*1*" . $txt_receive_date . "*" . $cbo_store_name . "*" . $txt_brand . "*" . $cbo_uom . "*" . $txt_receive_qty . "*" . $txt_rate . "*" . $ile . "*" . $ile_cost . "*" . $order_amount . "*" . $cbo_uom . "*" . $txt_receive_qty . "*" . $cons_rate . "*" . $txt_avg_rate . "*" . $txt_dyeing_charge . "*" . $con_ile . "*" . $con_ile_cost . "*" . $con_amount . "*" . $adjBalanceQnty . "*" . $adjBalanceAmount . "*" . $txt_no_bag . "*" . $txt_cone_per_bag . "*" . $txt_no_loose_cone . "*" . $txt_weight_per_bag . "*" . $txt_weight_per_cone . "*" . $cbo_room . "*" . $txt_rack . "*" . $txt_shelf . "*" . $cbo_bin . "*" . $cbo_floor . "*" . $txt_remarks . "*" . $job_no . "*'" . $cbo_buyer_name . "'*'" . $user_id . "'*'" . $pc_date_time . "'*" . $txt_wo_pi_dtls_id . "*" . $txt_grey_qty . "";

		//product master table data UPDATE START
		$field_array = "brand*avg_rate_per_unit*last_purchased_qnty*current_stock*stock_value*allocated_qnty*available_qnty*updated_by*update_date";
		$allocation_mst_insert = $allocation_dtls_insert=1;
		if ($before_prod_id == $prodMSTID)
		{
			$currentStock = $adj_beforeStock + $txt_receive_qty;
			//echo "30**".$txt_receive_qty."==".$hdn_receive_qty; die();
			if ( $currentStock <= 0 )
			{
				echo "30**Stock cannot be less than zero.";
				disconnect($con);
				die;
			}
			//echo "10**";
			$StockValue = $adj_beforeStockValue + ($domestic_rate * $txt_receive_qty);
			$avgRate = number_format($StockValue / $currentStock, $dec_place[3], '.', '')*1;

			if ($variable_set_allocation == 1) 
			{
				// update dyied yarn allocation start
				if ((str_replace("'", "", $cbo_receive_purpose) == 2 || str_replace("'", "", $cbo_receive_purpose) == 12 || str_replace("'", "", $cbo_receive_purpose) == 15 || str_replace("'", "", $cbo_receive_purpose) == 38 ||str_replace("'", "", $cbo_receive_purpose) == 46 || str_replace("'", "", $cbo_receive_purpose) == 50 || str_replace("'", "", $cbo_receive_purpose) == 51 ) && (str_replace("'", "", $cbo_receive_basis) == 2)) {

					if ( str_replace("'", "", $cbo_receive_purpose) == 2 && $is_without_order == 42 || $is_without_order == 114 )
					{
						$allocated_qnty = $adj_allocated_qnty + $txt_receive_qty;
						$available_qnty = $adj_beforeAvailableQnty; 
						
						$check_allocation_info = sql_select("select a.id, a.qnty from inv_material_allocation_mst a where a.item_id=".$prodMSTID." and a.is_dyied_yarn=1");
						//$check_allocation_info = sql_select("select a.id, a.qnty from inv_material_allocation_mst a where a.item_id=".$prodMSTID." and id = 10147");
						if (!empty($check_allocation_info))
						{
							$allocation_id = $check_allocation_info[0][csf('id')];
							$allocation_qnty = ($check_allocation_info[0][csf('qnty')] + ($txt_receive_qty-$hdn_receive_qty));

							execute_query("update inv_material_allocation_mst set qnty=$allocation_qnty,status_active=1,is_deleted=0,updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where id=$allocation_id", 0);
							execute_query("update inv_material_allocation_dtls set qnty=$allocation_qnty,status_active=1,is_deleted=0,updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where mst_id=$allocation_id", 0);
						}
						else
						{
							// INSERT NEW PRODUCT ALLOCATION
							$recieve_id = return_field_value("id", " inv_receive_master", "recv_number=" . $txt_mrr_no . "", "id");
							$allocation_id = return_next_id_by_sequence("INV_ALLOCATION_MST_PK_SEQ", "inv_material_allocation_mst", $con);
							$field_allocation = "id,mst_id,entry_form,job_no,item_category,allocation_date,item_id,qnty,is_dyied_yarn,inserted_by,insert_date";
							$data_allocation = "(" . $allocation_id . ",".$recieve_id.",1," . $job_no . ",1" . "," . $txt_receive_date . "," . $prodMSTID . "," . $txt_receive_qty . ",1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

							$allocation_dtls_id = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
							$field_allocation_dtls = "id,mst_id,job_no,item_category,allocation_date,item_id,qnty,is_dyied_yarn,inserted_by,insert_date";
							$data_allocation_dtls = "(" . $allocation_dtls_id . "," . $allocation_id . "," . $job_no . ",1," . $txt_receive_date . "," . $prodMSTID . "," . $txt_receive_qty . ",1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

							$allocation_mst_insert = sql_insert("inv_material_allocation_mst", $field_allocation, $data_allocation, 0);
							if ($allocation_mst_insert)
							{
								$allocation_dtls_insert = sql_insert("inv_material_allocation_dtls", $field_allocation_dtls, $data_allocation_dtls, 0);
							}
						}
					}
					else if ($is_without_order == 135 || ( $is_without_order == 94 && $is_with_order_yarn_service_work_order==2) )
					{
						$allocated_qnty = $adj_allocated_qnty;
						$available_qnty = $adj_beforeAvailableQnty + $txt_receive_qty;
					}
					else
					{
						$allocated_qnty = $adj_allocated_qnty + $txt_receive_qty;

						$available_qnty = $adj_beforeAvailableQnty;

						$check_allocation_info = sql_select("select a.* from inv_material_allocation_mst a where a.job_no=$job_no and a.item_id=$prodMSTID and a.is_dyied_yarn=1");
						if (!empty($check_allocation_info)) {
							$allocation_id = $check_allocation_info[0][csf('id')];
							$allocation_qnty = ($check_allocation_info[0][csf('qnty')] + ($txt_receive_qty-$hdn_receive_qty));

							execute_query("update inv_material_allocation_mst set qnty=$allocation_qnty,status_active=1,is_deleted=0,updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where id=$allocation_id", 0);
							execute_query("update inv_material_allocation_dtls set qnty=$allocation_qnty,status_active=1,is_deleted=0,updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where mst_id=$allocation_id", 0);
						}else{
							// INSERT NEW PRODUCT ALLOCATION
							$recieve_id = return_field_value("id", " inv_receive_master", "recv_number=" . $txt_mrr_no . "", "id");
							$allocation_id = return_next_id_by_sequence("INV_ALLOCATION_MST_PK_SEQ", "inv_material_allocation_mst", $con);
							$field_allocation = "id,mst_id,entry_form,job_no,item_category,allocation_date,item_id,qnty,is_dyied_yarn,inserted_by,insert_date";
							$data_allocation = "(" . $allocation_id . ",".$recieve_id.",1," . $job_no . ",1" . "," . $txt_receive_date . "," . $prodMSTID . "," . $txt_receive_qty . ",1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

							$allocation_dtls_id = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
							$field_allocation_dtls = "id,mst_id,job_no,item_category,allocation_date,item_id,qnty,is_dyied_yarn,inserted_by,insert_date";
							$data_allocation_dtls = "(" . $allocation_dtls_id . "," . $allocation_id . "," . $job_no . ",1," . $txt_receive_date . "," . $prodMSTID . "," . $txt_receive_qty . ",1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

							$allocation_mst_insert = sql_insert("inv_material_allocation_mst", $field_allocation, $data_allocation, 0);
							if ($allocation_mst_insert) {
								$allocation_dtls_insert = sql_insert("inv_material_allocation_dtls", $field_allocation_dtls, $data_allocation_dtls, 0);
							}
						}
					}
				} else {
					$allocated_qnty = $adj_allocated_qnty;
					$available_qnty = $adj_beforeAvailableQnty + $txt_receive_qty;
				}
			}
			else
			{
				$allocated_qnty = $adj_allocated_qnty;
				$available_qnty = $adj_beforeAvailableQnty + $txt_receive_qty;
			}

			if (str_replace("'", "", $txt_brand) == "") $txt_brand = 0;
			$data_array = "" . $txt_brand . "*" . $avgRate . "*" . $txt_receive_qty . "*" . $currentStock . "*" . number_format($StockValue, $dec_place[4], '.', '') . "*'" . $allocated_qnty . "'*'" . $available_qnty . "'*'" . $user_id . "'*'" . $pc_date_time . "'";
		}
		else
		{
			//before
			$updateID_array = $update_data = array();
			$updateID_array[] = $before_prod_id;
			if (str_replace("'", "", $before_brand) == "") $before_brand = 0;

			$update_data[$before_prod_id] = explode("*", ("" . $before_brand . "*" . $adj_beforeAvgRate . "*" . $before_receive_qnty . "*" . $adj_beforeStock . "*" . number_format($adj_beforeStockValue, $dec_place[4], '.', '') . "*" . $adj_allocated_qnty . "*" . $adj_beforeAvailableQnty . "*'" . $user_id . "'*'" . $pc_date_time . "'"));

			//current
			$presentStock = $presentStock + $txt_receive_qty;
			//$available_qnty  		= $available_qnty+$txt_receive_qty;

			$presentStockValue = $presentStockValue + ($domestic_rate * $txt_receive_qty);
			$presentAvgRate = number_format($presentStockValue / $presentStock, $dec_place[3], '.', '');
			if ($variable_set_allocation == 1) 
			{
				if ((str_replace("'", "", $cbo_receive_purpose) == 2 || str_replace("'", "", $cbo_receive_purpose) == 12 || str_replace("'", "", $cbo_receive_purpose) == 15 || str_replace("'", "", $cbo_receive_purpose) == 38 || str_replace("'", "", $cbo_receive_purpose) == 46 || str_replace("'", "", $cbo_receive_purpose) == 50 || str_replace("'", "", $cbo_receive_purpose) == 51 ) && (str_replace("'", "", $cbo_receive_basis) == 2)) 
				{
					if (str_replace("'", "", $cbo_receive_purpose) == 2 && ($is_without_order == 42 || $is_without_order == 114))
					{
						$allocated_qnty = $allocated_qnty+$txt_receive_qty;
						$available_qnty = $available_qnty;
						//=================================================
						//$allocation_mst_insert = 1;
						//$allocation_dtls_insert = 1;
						//for booking no
						$sqlBookingNo = "SELECT c.id, c.booking_no FROM wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b, wo_non_ord_samp_booking_mst c WHERE a.id = b.mst_id AND b.booking_no = c.booking_no AND a.id = ".$txt_wo_pi_id." AND a.status_active = 1 AND a.is_deleted = 0 AND a.entry_form in (42,114)";
						//AND b.product_id = ".$prodMSTID."
						//oci_rollback($con);
						//echo "10**".$sqlBookingNo; die;
					
						$sqlBookingNo = sql_select($sqlBookingNo);
						$bookingId = '';
						$bookingNo = '';
						foreach($sqlBookingNo as $row)
						{
							$bookingId = $row[csf('id')];
							$bookingNo = $row[csf('booking_no')];
						}

						$check_allocation_info = sql_select("SELECT a.id, a.qnty FROM inv_material_allocation_mst a WHERE a.booking_no = '".$bookingNo."' AND a.item_id = ".$prodMSTID." AND a.is_dyied_yarn = 1 AND a.status_active = 1 AND a.is_deleted = 0");
						if (!empty($check_allocation_info))
						{
							$allocation_id = $check_allocation_info[0][csf('id')];
							$allocation_qnty = $check_allocation_info[0][csf('qnty')] + $txt_receive_qty;
							$field_allocation = "qnty*updated_by*update_date";
							$data_allocation = "". $allocation_qnty . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

							$allocation_dtls_id = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
							$field_allocation_dtls = "id,mst_id,job_no,item_category,allocation_date,item_id,qnty,is_dyied_yarn,inserted_by,insert_date";
							$data_allocation_dtls = "(" . $allocation_dtls_id . "," . $allocation_id . "," . $job_no . ",1," . $txt_receive_date . "," . $prodMSTID . "," . $allocation_qnty . ",1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

							$allocation_mst_insert = sql_update("inv_material_allocation_mst", $field_allocation, $data_allocation, "id", "" . $allocation_id . "", 0);
							$allocation_dtls_delete = execute_query("delete from inv_material_allocation_dtls where mst_id='$allocation_id'", 1);
							if ($allocation_mst_insert)
							{
								$allocation_dtls_insert = sql_insert("inv_material_allocation_dtls", $field_allocation_dtls, $data_allocation_dtls, 0);
							}
						}
						else
						{
							$allocation_id = return_next_id_by_sequence("INV_ALLOCATION_MST_PK_SEQ", "inv_material_allocation_mst", $con);
							$field_allocation = "id,mst_id,entry_form,job_no,item_category,allocation_date,item_id,qnty,is_dyied_yarn,po_break_down_id,booking_no,booking_without_order,inserted_by,insert_date";
							$data_allocation = "(" . $allocation_id . ",".$id.",1," . $job_no . ",1" . "," . $txt_receive_date . "," . $prodMSTID . "," . $txt_receive_qty . ",1,".$bookingId.",'".$bookingNo."',1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

							$allocation_dtls_id = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
							$field_allocation_dtls = "id,mst_id,job_no,item_category,allocation_date,item_id,qnty,is_dyied_yarn,po_break_down_id,booking_no,inserted_by,insert_date";
							$data_allocation_dtls = "(" . $allocation_dtls_id . "," . $allocation_id . "," . $job_no . ",1," . $txt_receive_date . "," . $prodMSTID . "," . $txt_receive_qty . ",1,".$bookingId.",'".$bookingNo."'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

							$allocation_mst_insert = sql_insert("inv_material_allocation_mst", $field_allocation, $data_allocation, 0);

							if ($allocation_mst_insert)
							{
								$allocation_dtls_insert = sql_insert("inv_material_allocation_dtls", $field_allocation_dtls, $data_allocation_dtls, 0);
							}
						}
						
						//=====================================
						$sqlBookingNo = "SELECT c.id, c.booking_no FROM wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b, wo_non_ord_samp_booking_mst c WHERE a.id = b.mst_id AND b.booking_no = c.booking_no AND a.id = ".$txt_wo_pi_id." AND a.status_active = 1 AND a.is_deleted = 0 AND a.entry_form in(42,114)"; //AND b.product_id = ".$prodMSTID."
						$sqlBookingNo = sql_select($sqlBookingNo);
						$bookingId = '';
						$bookingNo = '';
						foreach($sqlBookingNo as $row)
						{
							$bookingId = $row[csf('id')];
							$bookingNo = $row[csf('booking_no')];
						}

						$check_allocation_info = sql_select("SELECT a.id FROM inv_material_allocation_mst a WHERE a.booking_no = '".$bookingNo."' AND a.item_id = ".$before_prod_id." AND a.is_dyied_yarn = 1");
						if (!empty($check_allocation_info))
						{
							$allocation_id = $check_allocation_info[0][csf('id')];
							execute_query("UPDATE inv_material_allocation_mst SET qnty=(qnty-$before_receive_qnty),status_active=1,is_deleted=0,updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' WHERE id = ".$allocation_id."", 0);

							execute_query("UPDATE inv_material_allocation_dtls SET qnty=(qnty-$before_receive_qnty),status_active=1,is_deleted=0,updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' WHERE mst_id = ".$allocation_id." AND item_id =".$before_prod_id."", 0);
						}
						
						if ($expString[0] == true)
						{
							$check_allocation_info = sql_select("SELECT a.id FROM inv_material_allocation_mst a WHERE a.booking_no = '".$bookingNo."' AND a.item_id = ".$prodMSTID." AND a.is_dyied_yarn = 1");
							if (!empty($check_allocation_info))
							{
								$allocation_id = $check_allocation_info[0][csf('id')];
								execute_query("update inv_material_allocation_mst set qnty=qnty+$txt_receive_qty,status_active=1,is_deleted=0,updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where id=$allocation_id", 0);

								execute_query("update inv_material_allocation_dtls set qnty=qnty+$txt_receive_qty,status_active=1,is_deleted=0,updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where mst_id=$allocation_id", 0);
							}
							else
							{
								//INSERT NEW PRODUCT ALLOCATION
								$recieve_id = return_field_value("id", " inv_receive_master", "recv_number=" . $txt_mrr_no . "", "id");
								$allocation_id = return_next_id_by_sequence("INV_ALLOCATION_MST_PK_SEQ", "inv_material_allocation_mst", $con);
								$field_allocation = "id,mst_id,entry_form,job_no,item_category,allocation_date,item_id,qnty,is_dyied_yarn,po_break_down_id,booking_no,booking_without_order,inserted_by,insert_date";
								$data_allocation = "(" . $allocation_id . ",".$recieve_id.",1," . $job_no . ",1" . "," . $txt_receive_date . "," . $prodMSTID . "," . $txt_receive_qty . ",1,".$bookingId.",'".$bookingNo."',1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

								$allocation_dtls_id = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
								$field_allocation_dtls = "id,mst_id,job_no,item_category,allocation_date,item_id,qnty,is_dyied_yarn,po_break_down_id,booking_no,inserted_by,insert_date";
								$data_allocation_dtls = "(" . $allocation_dtls_id . "," . $allocation_id . "," . $job_no . ",1," . $txt_receive_date . "," . $prodMSTID . "," . $txt_receive_qty . ",1,".$bookingId.",'".$bookingNo."'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

								$allocation_mst_insert = sql_insert("inv_material_allocation_mst", $field_allocation, $data_allocation, 0);
								if ($allocation_mst_insert)
								{
									$allocation_dtls_insert = sql_insert("inv_material_allocation_dtls", $field_allocation_dtls, $data_allocation_dtls, 0);
								}
							}
						}
						else
						{
							$recieve_id = return_field_value("id", " inv_receive_master", "recv_number=" . $txt_mrr_no . "", "id");
							$allocation_id = return_next_id_by_sequence("INV_ALLOCATION_MST_PK_SEQ", "inv_material_allocation_mst", $con);
							$field_allocation = "id,mst_id,entry_form,job_no,item_category,allocation_date,item_id,qnty,is_dyied_yarn,po_break_down_id,booking_no,booking_without_order,inserted_by,insert_date";
							$data_allocation = "(" . $allocation_id . ",".$recieve_id.",1," . $job_no . ",1" . "," . $txt_receive_date . "," . $prodMSTID . "," . $txt_receive_qty . ",1,".$bookingId.",'".$bookingNo."',1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

							$allocation_dtls_id = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
							$field_allocation_dtls = "id,mst_id,job_no,item_category,allocation_date,item_id,qnty,is_dyied_yarn,po_break_down_id,booking_no,inserted_by,insert_date";
							$data_allocation_dtls = "(" . $allocation_dtls_id . "," . $allocation_id . "," . $job_no . ",1," . $txt_receive_date . "," . $prodMSTID . "," . $txt_receive_qty . ",1,".$bookingId.",'".$bookingNo."'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

							$allocation_mst_insert = sql_insert("inv_material_allocation_mst", $field_allocation, $data_allocation, 0);
							if ($allocation_mst_insert)
							{
								$allocation_dtls_insert = sql_insert("inv_material_allocation_dtls", $field_allocation_dtls, $data_allocation_dtls, 0);
							}
						}
					}
					else if ($is_without_order == 135)
					{
						$allocated_qnty = $allocated_qnty;
						$available_qnty = $available_qnty + $txt_receive_qty;
					}
					else
					{
						$allocated_qnty = $allocated_qnty + $txt_receive_qty;
						$available_qnty = $available_qnty;
						// DECREASE PREVIOUS ALLOCATION
						$check_allocation_info = sql_select("select a.* from inv_material_allocation_mst a where a.job_no=$job_no and a.item_id=$before_prod_id and a.is_dyied_yarn=1");

						if (!empty($check_allocation_info)) {
							$allocation_id = $check_allocation_info[0][csf('id')];
							execute_query("update inv_material_allocation_mst set qnty=(qnty-$before_receive_qnty),status_active=1,is_deleted=0,updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where id=$allocation_id", 0);

							execute_query("update inv_material_allocation_dtls set qnty=(qnty-$before_receive_qnty),status_active=1,is_deleted=0,updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where mst_id=$allocation_id and item_id=$before_prod_id", 0);
						}
						// INSERT NEW PRODUCT ALLOCATION

						if ($expString[0] == true) {
							//
							$check_allocation_info = sql_select("select a.* from inv_material_allocation_mst a where a.job_no=$job_no and a.item_id=$prodMSTID and a.is_dyied_yarn=1");
							if (!empty($check_allocation_info)) {
								$allocation_id = $check_allocation_info[0][csf('id')];
								execute_query("update inv_material_allocation_mst set qnty=qnty+$txt_receive_qty,status_active=1,is_deleted=0,updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where id=$allocation_id", 0);

								execute_query("update inv_material_allocation_dtls set qnty=qnty+$txt_receive_qty,status_active=1,is_deleted=0,updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where mst_id=$allocation_id", 0);
							}else{
							// INSERT NEW PRODUCT ALLOCATION
								$recieve_id = return_field_value("id", " inv_receive_master", "recv_number=" . $txt_mrr_no . "", "id");
								$allocation_id = return_next_id_by_sequence("INV_ALLOCATION_MST_PK_SEQ", "inv_material_allocation_mst", $con);
								$field_allocation = "id,mst_id,entry_form,job_no,item_category,allocation_date,item_id,qnty,is_dyied_yarn,inserted_by,insert_date";
								$data_allocation = "(" . $allocation_id . ",".$recieve_id.",1," . $job_no . ",1" . "," . $txt_receive_date . "," . $prodMSTID . "," . $txt_receive_qty . ",1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

								$allocation_dtls_id = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
								$field_allocation_dtls = "id,mst_id,job_no,item_category,allocation_date,item_id,qnty,is_dyied_yarn,inserted_by,insert_date";
								$data_allocation_dtls = "(" . $allocation_dtls_id . "," . $allocation_id . "," . $job_no . ",1," . $txt_receive_date . "," . $prodMSTID . "," . $txt_receive_qty . ",1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

								$allocation_mst_insert = sql_insert("inv_material_allocation_mst", $field_allocation, $data_allocation, 0);
								if ($allocation_mst_insert) {
									$allocation_dtls_insert = sql_insert("inv_material_allocation_dtls", $field_allocation_dtls, $data_allocation_dtls, 0);
								}
							}
						}else{
							$recieve_id = return_field_value("id", " inv_receive_master", "recv_number=" . $txt_mrr_no . "", "id");
							$allocation_id = return_next_id_by_sequence("INV_ALLOCATION_MST_PK_SEQ", "inv_material_allocation_mst", $con);
							$field_allocation = "id,mst_id,entry_form,job_no,item_category,allocation_date,item_id,qnty,is_dyied_yarn,inserted_by,insert_date";
							$data_allocation = "(" . $allocation_id . ",".$recieve_id.",1," . $job_no . ",1" . "," . $txt_receive_date . "," . $prodMSTID . "," . $txt_receive_qty . ",1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

							$allocation_dtls_id = return_next_id_by_sequence("INV_ALLOCATION_DTLS_PK_SEQ", "inv_material_allocation_dtls", $con);
							$field_allocation_dtls = "id,mst_id,job_no,item_category,allocation_date,item_id,qnty,is_dyied_yarn,inserted_by,insert_date";
							$data_allocation_dtls = "(" . $allocation_dtls_id . "," . $allocation_id . "," . $job_no . ",1," . $txt_receive_date . "," . $prodMSTID . "," . $txt_receive_qty . ",1," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

							$allocation_mst_insert = sql_insert("inv_material_allocation_mst", $field_allocation, $data_allocation, 0);
							if ($allocation_mst_insert) {
								$allocation_dtls_insert = sql_insert("inv_material_allocation_dtls", $field_allocation_dtls, $data_allocation_dtls, 0);
							}
						}
					}
				}
				else
				{
					$allocated_qnty = $allocated_qnty;
					$available_qnty = $available_qnty + $txt_receive_qty;
				}
			} 
			else 
			{
				$allocated_qnty = $allocated_qnty;
				$available_qnty = $available_qnty + $txt_receive_qty;
			}
			if (str_replace("'", "", $txt_brand) == "") $txt_brand = 0;

			$updateID_array[] = $prodMSTID;
			$update_data[$prodMSTID] = explode("*", ("" . $txt_brand . "*" . $presentAvgRate . "*" . $txt_receive_qty . "*" . $presentStock . "*" . number_format($presentStockValue, $dec_place[4], '.', '') . "*" . $allocated_qnty . "*" . $available_qnty . "*'" . $user_id . "'*'" . $pc_date_time . "'"));
		}
		//------------------ product_details_master END---------------------------------------------------//

		//echo "20**".$beforeAmount."==".$adj_beforeAvailableQnty."==".$avgRate;mysql_query("ROLLBACK");die;
		$prodUpdate = $insertR = $rID_adj = $rID_adjal = true;
		$rID = sql_update("inv_receive_master", $field_array_update, $data_array_update, "recv_number", $txt_mrr_no, 0);
		$dtlsrID = sql_update("inv_transaction", $field_array_trans, $data_array_trans, "id", $update_id, 1);
		if ($before_prod_id == $prodMSTID) 
		{
			//echo "10**".$data_array;die;
			$prodUpdate = sql_update("product_details_master", $field_array, $data_array, "id", $prodMSTID, 0);
		} 
		else 
		{
			if ($data_array_prod != "") {
				$insertR = sql_insert("product_details_master", $field_array_prod, $data_array_prod, 0);

				$rID_adj = execute_query("update product_details_master set allocated_qnty=(allocated_qnty-$txt_receive_qty) where id=$before_prod_id", 0);
				$rID_adjal = execute_query("update product_details_master set available_qnty=(current_stock-allocated_qnty) where id=$before_prod_id", 0);
			}
			$prodUpdate = execute_query(bulk_update_sql_statement("product_details_master", "id", $field_array, $update_data, $updateID_array));
		}
		//echo "10**". $prodUpdate;die;
		//echo "10**".$rID ."&&". $dtlsrID ."&&". $prodUpdate ."&&". $insertR ."&&". $rID_adj ."&&". $rID_adjal;die;
		if ($db_type == 0) {
			if ($rID && $dtlsrID && $prodUpdate && $insertR && $rID_adj && $rID_adjal && $allocation_mst_insert && $allocation_dtls_insert) {
				mysql_query("COMMIT");
				echo "1**" . str_replace("'", "", $txt_mrr_no);
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . str_replace("'", "", $txt_mrr_no);
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($rID && $dtlsrID && $prodUpdate && $insertR) {
				oci_commit($con);
				echo "1**" . str_replace("'", "", $txt_mrr_no);
			} else {
				oci_rollback($con);
				echo "10**" . str_replace("'", "", $txt_mrr_no);
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation == 2) //Delete Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		$mrr_data=sql_select("select a.id, a.is_posted_account, a.receive_purpose, a.receive_basis, b.cons_quantity, b.cons_rate, b.cons_amount, c.id as prod_id, c.current_stock, c.stock_value, c.allocated_qnty, c.available_qnty from inv_receive_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id and b.item_category=1 and b.transaction_type=1 and a.status_active=1 and b.status_active=1 and b.id=$update_id");
		$master_id=$mrr_data[0][csf("id")];

		$is_posted_account=$mrr_data[0][csf("is_posted_account")]*1;
		$receive_purpose=$mrr_data[0][csf("receive_purpose")];
		$receive_basis=$mrr_data[0][csf("receive_basis")];
		$cons_quantity=$mrr_data[0][csf("cons_quantity")];
		$cons_rate=$mrr_data[0][csf("cons_rate")];
		$cons_amount=$mrr_data[0][csf("cons_amount")];
		$prod_id=$mrr_data[0][csf("prod_id")];
		$current_stock=$mrr_data[0][csf("current_stock")];
		$stock_value=$mrr_data[0][csf("stock_value")];
		$allocated_qnty=$mrr_data[0][csf("allocated_qnty")];
		$available_qnty=$mrr_data[0][csf("available_qnty")];

		$cu_current_stock=$current_stock-$cons_quantity;
		$cu_stock_value=$stock_value-$cons_amount;
		if($cu_stock_value>0 && $cu_current_stock>0) $cu_avg_rate=$cu_stock_value/$cu_current_stock; else $cu_avg_rate=0;

		$is_with_order_yarn_service_work_order = return_field_value("booking_without_order", "wo_yarn_dyeing_mst", " status_active=1 and id=$txt_wo_pi_id", "booking_without_order");

		if ($variable_set_allocation == 1)
		{
			if ((str_replace("'", "", $cbo_receive_purpose) == 2 || str_replace("'", "", $cbo_receive_purpose) == 12 || str_replace("'", "", $cbo_receive_purpose) == 15 || str_replace("'", "", $cbo_receive_purpose) == 38 || str_replace("'", "", $cbo_receive_purpose) == 46 || str_replace("'", "", $cbo_receive_purpose) == 50 || str_replace("'", "", $cbo_receive_purpose) == 51 ) && (str_replace("'", "", $cbo_receive_basis) == 2)) 
			{
				if ($is_without_order == 135 || ($is_without_order == 94 && $is_with_order_yarn_service_work_order==2 ) )
				{
					$cu_allocated_qnty=$allocated_qnty;
					$cu_available_qnty=$available_qnty-$cons_quantity;
				}else{
					$cu_allocated_qnty=$allocated_qnty-$cons_quantity;
					$cu_available_qnty=$available_qnty;

					// DECREASE ALLOCATION
					$check_allocation_info = sql_select("select a.* from inv_material_allocation_mst a where a.job_no=$job_no and a.item_id=$prod_id and a.is_dyied_yarn=1");
					if (!empty($check_allocation_info)) {
						$allocation_id = $check_allocation_info[0][csf('id')];
						$allocation_qnty = $check_allocation_info[0][csf('qnty')]-$cons_quantity;
						execute_query("update inv_material_allocation_mst set qnty=$allocation_qnty,status_active=1,is_deleted=0,updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where id=$allocation_id",0);
						execute_query("update inv_material_allocation_dtls set qnty=$allocation_qnty,status_active=1,is_deleted=0,updated_by=" . $_SESSION['logic_erp']['user_id'] . ",update_date='" . $pc_date_time . "' where mst_id=$allocation_id and item_id=$prod_id",0);
					}
				}
			}
			else
			{
				$cu_allocated_qnty=$allocated_qnty;
				$cu_available_qnty=$available_qnty-$cons_quantity;
			}
		}
		else
		{
			$cu_allocated_qnty=$allocated_qnty;
			$cu_available_qnty=$available_qnty-$cons_quantity;
		}

		if($is_posted_account>0)
		{
			echo "13**Delete restricted, This Information is used in another Table."; disconnect($con); oci_rollback($con); die;
		}

		$next_operation=return_field_value("max(id) as max_trans_id", "inv_transaction", "status_active=1 and item_category=1 and transaction_type<>1 and prod_id=$prod_id", "max_trans_id");
		if($next_operation)
		{
			if($next_operation>str_replace("'","",$update_id))
			{
				echo "13**Delete restricted, This Information is used in another Table."; disconnect($con); oci_rollback($con); die;
			}
		}

		$field_array = "updated_by*update_date*status_active*is_deleted";
		$data_array = "'" . $user_id . "'*'" . $pc_date_time . "'*0*1";

		$field_array_prod = "current_stock*avg_rate_per_unit*stock_value*allocated_qnty*available_qnty*updated_by*update_date";
		$data_array_prod = "".$cu_current_stock."*".$cu_avg_rate."*".$cu_stock_value."*'".$cu_allocated_qnty."'*'".$cu_available_qnty."'*'".$user_id."'*'".$pc_date_time."'";

		$dtlsrID = sql_update("inv_transaction", $field_array, $data_array, "id", "$update_id", 1);
		$prodID = sql_update("product_details_master", $field_array_prod, $data_array_prod, "id", "$prod_id", 1);

		//echo "10**$prodID == $dtlsrID";oci_rollback($con);disconnect($con); die;

		if ($db_type == 0) {
			if ($prodID && $dtlsrID) {
				mysql_query("COMMIT");
				echo "2**" . str_replace("'", "", $txt_mrr_no);
			} else {
				mysql_query("ROLLBACK");
				echo "10**" . str_replace("'", "", $txt_mrr_no);
			}
		} else if ($db_type == 2 || $db_type == 1) {
			if ($prodID && $dtlsrID) {
				oci_commit($con);
				echo "2**" . str_replace("'", "", $txt_mrr_no);
			} else {
				oci_rollback($con);
				echo "10**" . str_replace("'", "", $txt_mrr_no);
			}
		}
		disconnect($con);
		die;
	}
}


if ($action == "mrr_popup_info") {
	echo load_html_head_contents("Popup Info", "../../", 1, 1, $unicode);
	extract($_REQUEST);
	?>

	<script>
		function js_set_value(mrrID) {
			var splitArr = mrrID.split("_");
            $("#hidden_recv_id").val(splitArr[0]); 		// id number
            $("#hidden_recv_number").val(splitArr[1]); 	// mrr number
            $("#hidden_posted_in_account").val(splitArr[2]); 	// check posted in account
            parent.emailwindow.hide();
        }
    </script>

</head>

<body>
	<div align="center" style="width:100%;">
		<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
			<table width="880" cellspacing="0" cellpadding="0" border="0" class="rpt_table" align="center">
				<thead>
					<tr>
						<th width="150">Supplier</th>
						<th width="150">Search By</th>
						<th width="250" align="center" id="search_by_td_up">Enter MRR Number</th>
						<th width="200">Date Range</th>
						<th><input type="reset" name="re_button" id="re_button" value="Reset" style="width:100px"
							class="formbutton"/></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<?
								echo create_drop_down("cbo_supplier", 150, "select c.supplier_name,c.id from lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and b.party_type=2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 0);
								?>
							</td>
							<td>
								<?
								$search_by = array(1 => 'MRR No', 2 => 'Challan No');
								$dd = "change_search_event(this.value, '0*0', '0*0', '../../') ";
								echo create_drop_down("cbo_search_by", 120, $search_by, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>
							<td width="" align="center" id="search_by_td">
								<input type="text" style="width:230px" class="text_boxes" name="txt_search_common"
								id="txt_search_common"/>
							</td>
							<td align="center">
								<input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px"
								placeholder="From Date"/>
								<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px"
								placeholder="To Date"/>
							</td>
							<td align="center">
								<input type="button" name="btn_show" class="formbutton" value="Show"
								onClick="show_list_view ( document.getElementById('cbo_supplier').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_search_common').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $company; ?>, 'create_mrr_search_list_view', 'search_div', 'yarn_receive_controller', 'setFilterGrid(\'list_view\',-1)')"
								style="width:100px;"/>
							</td>
						</tr>
						<tr>
							<td align="center" height="40" valign="middle" colspan="5">
								<? echo load_month_buttons(1); ?>
								<!-- Hidden field here -->
								<input type="hidden" id="hidden_recv_number" value=""/>
								<input type="hidden" id="hidden_recv_id" value=""/>
								<input type="hidden" id="hidden_posted_in_account" value=""/>
								<!--END -->
							</td>
						</tr>
					</tbody>
				</tr>
			</table>
			<div align="center" valign="top" id="search_div"></div>
		</form>
	</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?

exit;
}


if ($action == "create_mrr_search_list_view")
{

	$ex_data = explode("_", $data);
	$supplier = $ex_data[0];
	$txt_search_by = $ex_data[1];
	$txt_search_common = $ex_data[2];
	$fromDate = $ex_data[3];
	$toDate = $ex_data[4];
	$company = $ex_data[5];

	$sql_cond = "";
	if (trim($txt_search_common) != "") {
		if (trim($txt_search_by) == 1) // for mrr
		{
			$sql_cond .= " and a.recv_number LIKE '%$txt_search_common'";

		} else if (trim($txt_search_by) == 2) // for chllan no
		{
			$sql_cond .= " and a.challan_no LIKE '%$txt_search_common%'";
		}

	}

	if ($fromDate != "" && $toDate != "") {
		if ($db_type == 0) {
			$sql_cond .= " and a.receive_date between '" . change_date_format($fromDate, 'yyyy-mm-dd') . "' and '" . change_date_format($toDate, 'yyyy-mm-dd') . "'";
		} else {
			$sql_cond .= " and a.receive_date between '" . change_date_format($fromDate, '', '', 1) . "' and '" . change_date_format($toDate, '', '', 1) . "'";
		}
	}

	if (trim($company) != "") $sql_cond .= " and a.company_id='$company'";
	if (trim($supplier) != 0) $sql_cond .= " and a.supplier_id='$supplier'";

	if ($db_type == 0) $year_field = "YEAR(a.insert_date) as year,";
	else if ($db_type == 2) $year_field = "to_char(a.insert_date,'YYYY') as year,";
	else $year_field = "";//defined Later


	if($user_store_ids) $user_store_cond = " and a.store_id in ($user_store_ids)"; else $user_store_cond = "";

	$sql = "select a.id, $year_field a.recv_number_prefix_num, a.recv_number,a.yarn_bag_receive, a.supplier_id, a.challan_no, a.receive_date, a.receive_basis, sum(b.cons_quantity) as receive_qnty,a.is_posted_account,c.lc_number, d.pay_mode from inv_transaction b left join wo_yarn_dyeing_mst d on b.pi_wo_batch_no=d.id, inv_receive_master a left join com_btb_lc_master_details c on a.lc_no=c.id where a.id=b.mst_id and a.entry_form=1 and b.item_category=1 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $sql_cond $user_store_cond group by a.id, a.recv_number_prefix_num, a.recv_number, a.supplier_id, a.challan_no, a.receive_date, a.receive_basis,a.insert_date,a.is_posted_account,a.yarn_bag_receive,c.lc_number,d.pay_mode order by a.id"; //a.yarn_bag_receive !=1 and

	$sqlresult = sql_select($sql);

	if( $sqlresult[0][csf("pay_mode")] == 3 || $sqlresult[0][csf("pay_mode")]== 5)
	{
		$supplier_arr = return_library_array("select id,company_name from lib_company", 'id', 'company_name');
	}else{
		$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	}
	//echo $sql;//die;

	$arr = array(2 => $supplier_arr, 6 => $receive_basis_arr);

	echo create_list_view("list_view", "Year, MRR No, Supplier Name, Challan No, LC No, Receive Date, Receive Basis, Receive Qnty", "60,70,130,120,120,120,100,100", "900", "260", 0, $sql, "js_set_value", "id,recv_number,is_posted_account", "", 1, "0,0,supplier_id,0,0,0,receive_basis,0", $arr, "year,recv_number_prefix_num,supplier_id,challan_no,lc_number,receive_date,receive_basis,receive_qnty", "", '', '0,0,0,0,0,3,0,1');

	exit();

}

if ($action == "populate_data_from_data") {
	$ex_data = explode("_", $data);
	$mrrNo = $ex_data[0];
	$rcvID = $ex_data[1];

	$sql = "select id,recv_number,company_id,receive_basis,receive_purpose,receive_date,booking_id,challan_no,store_id,lc_no,supplier_id,loan_party,exchange_rate,currency_id,lc_no,source, yarn_issue_challan_no, issue_id, remarks
	from inv_receive_master
	where id=$rcvID and recv_number='$mrrNo' and entry_form=1";
	//echo $sql;die;
	$res = sql_select($sql);
	foreach ($res as $row) {
		echo "$('#cbo_company_id').val(" . $row[csf("company_id")] . ");\n";
		echo "$('#cbo_receive_basis').val(" . $row[csf("receive_basis")] . ");\n";
		echo "$('#cbo_receive_purpose').val(" . $row[csf("receive_purpose")] . ");\n";
		echo "$('#txt_receive_date').val('" . change_date_format($row[csf("receive_date")]) . "');\n";
		echo "$('#txt_challan_no').val('" . $row[csf("challan_no")] . "');\n";

		echo "load_room_rack_self_bin('requires/yarn_receive_controller*1', 'store','store_td', '".$row[csf('company_id')]."','"."',this.value);\n";

		echo "$('#cbo_store_name').val(" . $row[csf("store_id")] . ");\n";
		echo "$('#cbo_store_name').attr('disabled','disabled');\n";

		echo "$('#cbo_currency').val(" . $row[csf("currency_id")] . ");\n";
		echo "$('#txt_exchange_rate').val(" . $row[csf("exchange_rate")] . ");\n";
		echo "$('#cbo_source').val(" . $row[csf("source")] . ");\n";
		echo "$('#txt_mst_remarks').val('" . $row[csf("remarks")] . "');\n";

		if ($row[csf("receive_basis")] == 1)
		{
			$wopi = return_field_value("pi_number", "com_pi_master_details", "id=" . $row[csf("booking_id")] . "");
			$pi_basis = return_field_value("pi_basis_id", "com_pi_master_details", "id=" . $row[csf("booking_id")] . "");
		}
		else if ($row[csf("receive_basis")] == 2 && ($row[csf("receive_purpose")] == 2 || $row[csf("receive_purpose")] == 12 || $row[csf("receive_purpose")] == 15 || $row[csf("receive_purpose")] == 38 || $row[csf("receive_purpose")] == 46 || $row[csf("receive_purpose")] == 50 || $row[csf("receive_purpose")] == 51 ))
		{
			//$wopi = return_field_value("ydw_no", "wo_yarn_dyeing_mst", "id=" . $row[csf("booking_id")] . "");
			$wopi_sql=sql_select("select ydw_no, pay_mode,entry_form from wo_yarn_dyeing_mst where id=" . $row[csf("booking_id")] . "");
			$wopi = $wopi_sql[0][csf("ydw_no")];
			$wopi_pay_mode = $wopi_sql[0][csf("pay_mode")];
			$wo_entry_form = $wopi_sql[0][csf("entry_form")];
		}
		else
		{

			$wo_non_pi_sql=sql_select("select wo_number,pay_mode, entry_form from wo_non_order_info_mst where id=" . $row[csf("booking_id")] . "");

			$wopi = $wo_non_pi_sql[0][csf("wo_number")];
			$wopi_pay_mode = $wo_non_pi_sql[0][csf("pay_mode")];
			$wo_entry_form = $wo_non_pi_sql[0][csf("entry_form")];
		}

		echo "$('#txt_wo_pi').val('" . $wopi . "');\n";
		echo "$('#txt_wo_pi_id').val(" . $row[csf("booking_id")] . ");\n";
		echo "$('#txt_pi_basis').val(" . $pi_basis . ");\n";


		if ($row[csf("receive_basis")] == 1 || $row[csf("receive_basis")] == 2) {
			echo "show_list_view('" . $row[csf("receive_basis")] . "**" . $row[csf("booking_id")] . "**" . $row[csf("receive_purpose")] . "**" . $row[csf("company_id")]. "','show_product_listview','list_product_container','requires/yarn_receive_controller','');\n";

			if ($row[csf("receive_basis")] == 2 && $row[csf("receive_purpose")] == 2) {
				echo "load_drop_down( 'requires/yarn_receive_controller','" . $row[csf("booking_id")] . "','load_drop_down_color', 'color_td_id' );\n";
			}
			echo "$('#cbo_source').attr('disabled','disabled');\n";
		} else {

			echo "$('#cbo_source').removeAttr('disabled','disabled');\n";
		}

		if ($row[csf("receive_basis")] == 2 && ($wopi_pay_mode==3 || $wopi_pay_mode==5) && ($row[csf("receive_purpose")]==2 || $row[csf("receive_purpose")]==12 || $row[csf("receive_purpose")]==15 || $row[csf("receive_purpose")]==38 || $row[csf("receive_purpose")]==46 || $row[csf("receive_purpose")]==50 || $row[csf("receive_purpose")]==51) )
		{
			$data_ref="'".$row[csf("receive_basis")]."_".$row[csf("receive_purpose")]."_".$wopi."_".$wopi_pay_mode."_".$wo_entry_form."'";
			echo "load_drop_down( 'requires/yarn_receive_controller', $data_ref,'load_drop_down_company_from_eheck_wo_paymode', 'supplier' );\n";
		}
		else
		{
			echo "load_supplier();\n";
		}

		echo "$('#cbo_supplier').val(" . $row[csf("supplier_id")] . ");\n";
		echo "$('#cbo_party').val(" . $row[csf("loan_party")] . ");\n";
		echo "$('#txt_issue_challan_no').val(" . $row[csf("yarn_issue_challan_no")] . ");\n";
		echo "$('#txt_issue_id').val(" . $row[csf("issue_id")] . ");\n";

		
		echo "$('#hidden_lc_id').val(" . $row[csf("lc_no")] . ");\n";
		$lcNumber = return_field_value("lc_number", "com_btb_lc_master_details", "id=" . $row[csf("lc_no")] . "");
		echo "$('#txt_lc_no').val('" . $lcNumber . "');\n";
		

		//right side list view
		echo "show_list_view('" . $row[csf("recv_number")] . "**" . $row[csf("id")] . "','show_dtls_list_view','list_container_yarn','requires/yarn_receive_controller','');\n";

	}
	exit();
}

if ($action == "show_dtls_list_view") {
	$ex_data = explode("**", $data);
	$recv_number = $ex_data[0];
	$rcv_mst_id = $ex_data[1];

	$cond = "";
	if ($recv_number != "") $cond .= " and a.recv_number='$recv_number'";
	if ($rcv_mst_id != "") $cond .= " and a.id='$rcv_mst_id'";

	$sql = "select a.recv_number, a.receive_purpose, b.id, a.booking_id, a.receive_basis,b.pi_wo_batch_no,c.product_name_details,c.lot,b.order_uom,b.order_qnty,b.order_rate,b.order_ile_cost,b.order_amount,b.cons_amount
	from inv_receive_master a, inv_transaction b, product_details_master c
	where a.id=b.mst_id and b.prod_id=c.id and c.item_category_id=1 and b.transaction_type=1 and b.item_category=1 and a.entry_form=1 and b.status_active=1 and b.is_deleted=0 $cond";
	//echo $sql;
	$result = sql_select($sql);
	$i = 1;
	$totalQnty = 0;
	$totalAmount = 0;
	$totalbookCurr = 0;
	?>
	<table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all">
		<thead>
			<tr>
				<th>SL</th>
				<th>WO/PI No</th>
				<th>MRR No</th>
				<th>Product Details</th>
				<th>Yarn Lot</th>
				<th>UOM</th>
				<th>Receive Qty</th>
				<th>Rate</th>
				<th>ILE Cost</th>
				<th>Amount</th>
				<th>Book Currency</th>
			</tr>
		</thead>
		<tbody>
			<? foreach ($result as $row) {

				if ($i % 2 == 0) $bgcolor = "#E9F3FF";
				else $bgcolor = "#FFFFFF";

				$wopi = "";
				if ($row[csf("receive_basis")] == 1)
					$wopi = return_field_value("pi_number", "com_pi_master_details", "id=" . $row[csf("booking_id")] . "");
				else if ($row[csf("receive_basis")] == 2 && ($row[csf("receive_purpose")] == 2 || $row[csf("receive_purpose")] == 12 || $row[csf("receive_purpose")] == 7 || $row[csf("receive_purpose")] == 15 || $row[csf("receive_purpose")] == 38 || $row[csf("receive_purpose")] == 46 || $row[csf("receive_purpose")] == 50 || $row[csf("receive_purpose")] == 51))
					$wopi = return_field_value("ydw_no", "wo_yarn_dyeing_mst", "id=" . $row[csf("booking_id")] . "");
				else
					$wopi = return_field_value("wo_number", "wo_non_order_info_mst", "id=" . $row[csf("booking_id")] . "");

				$totalQnty += $row[csf("order_qnty")];
				$totalAmount += ($row[csf("order_rate")]*$row[csf("order_qnty")]);
				$totalbookCurr += $row[csf("cons_amount")];

				?>
				<tr bgcolor="<? echo $bgcolor; ?>"
					onClick='get_php_form_data("<? echo $row[csf("id")]; ?>","child_form_input_data","requires/yarn_receive_controller")'
					style="cursor:pointer">
					<td width="30"><?php echo $i; ?></td>
					<td width="100"><p><?php echo $wopi; ?></p></td>
					<td width="130"><p><?php echo $row[csf("recv_number")]; ?></p></td>
					<td width="200"><p><?php echo $row[csf("product_name_details")]; ?></p></td>
					<td width="80"><p><?php echo $row[csf("lot")]; ?></p></td>
					<td width="60"><p><?php echo $unit_of_measurement[$row[csf("order_uom")]]; ?></p></td>
					<td width="80" align="right"><p><?php echo number_format($row[csf("order_qnty")],2); ?></p></td>
					<td width="60" align="right"><p><?php echo $row[csf("order_rate")]; ?></p></td>
					<td width="80" align="right"><p><?php echo $row[csf("order_ile_cost")]; ?></p></td>
					<td width="70" align="right"><p><?php echo number_format(($row[csf("order_rate")]*$row[csf("order_qnty")]), 2); ?></p></td>
					<td width="80" align="right"><p><?php echo number_format($row[csf("cons_amount")], 2); ?></p></td>
				</tr>
				<? $i++;
			} ?>
			<tfoot>
				<th colspan="6">Total= </th>
				<th><?php echo number_format($totalQnty,2); ?></th>
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


if ($action == "child_form_input_data") {
	$rcv_dtls_id = $data;
	$sql = "select a.currency_id, a.exchange_rate, b.id, b.receive_basis, b.job_no,a.company_id, a.receive_purpose, b.pi_wo_batch_no, b.prod_id, b.brand_id, c.lot,c.yarn_count_id,c.yarn_comp_type1st,c.yarn_comp_percent1st,c.yarn_comp_type2nd,c.yarn_comp_percent2nd,c.yarn_type,c.color, b.order_uom, b.order_qnty,grey_quantity,b.order_rate, b.cons_avg_rate, b.dye_charge, b.order_ile_cost, b.order_amount, b.cons_amount, b.no_of_bags, b.product_code,b.store_id, b.room, b.rack, b.self, b.bin_box,b.floor_id, b.cone_per_bag,b.no_loose_cone, b.weight_per_bag, b.weight_per_cone, b.remarks, b.supplier_id, b.buyer_id, b.pi_wo_req_dtls_id
	from inv_receive_master a, inv_transaction b, product_details_master c
	where a.id=b.mst_id and b.prod_id=c.id and b.id='$rcv_dtls_id'";
	//echo $sql;
	$rcvDtlsResult = sql_select($sql);

	$rcvDtlsResult[0][csf("prod_id")];
	$company_id=$rcvDtlsResult[0][csf("company_id")];
	$brand_id = $rcvDtlsResult[0][csf("brand_id")];
	$receive_basis = $rcvDtlsResult[0][csf("receive_basis")];

	$brand_name = return_field_value("brand_name", "lib_brand", "id=" . $brand_id . "");

	$variable_set_invent = sql_select("select category,over_rcv_status,over_rcv_percent,over_rcv_payment from variable_inv_ile_standard where company_name=$company_id and variable_list=23 and category = 1 order by id");

	$variable_rcv_result=sql_select("select id, user_given_code_status from variable_settings_inventory where company_name=$company_id and variable_list='31' and status_active=1 and is_deleted=0");

	$variable_rcv_level = $variable_rcv_result[0][csf("user_given_code_status")];

	if ($db_type == 0) {
		$orderBy_cond = "IFNULL";
	} else if ($db_type == 2) {
		$orderBy_cond = "NVL";
	} else {
		$orderBy_cond = "ISNULL";
	}

	$wo_po_qnty = 0;
	$totalRcvQnty = 0;
	$updateRcvQnty = 0;
	if ($receive_basis == 1 || $receive_basis == 2)
	{
		if ($rcvDtlsResult[0][csf("yarn_comp_type2nd")] == "") {
			$yarn_comp_type2nd = 0;
		} else {
			$yarn_comp_type2nd = $rcvDtlsResult[0][csf("yarn_comp_type2nd")];
		}
		if ($rcvDtlsResult[0][csf("yarn_comp_percent2nd")] == "") {
			$yarn_comp_percent2nd = 0;
		} else {
			$yarn_comp_percent2nd = $rcvDtlsResult[0][csf("yarn_comp_percent2nd")];
		}

		if($variable_rcv_level==2) // ############## for wo pi dtls level
		{
			$whereCondition =" a.id=b.prod_id and b.mst_id = c.id and b.company_id=$company_id and a.supplier_id=" . $rcvDtlsResult[0][csf("supplier_id")] . " and a.item_category_id=1 and b.transaction_type=1 and b.item_category=1 and b.pi_wo_req_dtls_id=" . $rcvDtlsResult[0][csf("pi_wo_req_dtls_id")] . " and b.receive_basis=" . $receive_basis . " and b.status_active=1 and b.is_deleted = 0";
		}
		else // ############## for wo pi item level
		{
			$whereCondition = "a.id=b.prod_id and b.mst_id = c.id and a.yarn_count_id=" . $rcvDtlsResult[0][csf("yarn_count_id")] . " and a.yarn_comp_type1st=" . $rcvDtlsResult[0][csf("yarn_comp_type1st")] . " and a.yarn_comp_percent1st=" . $rcvDtlsResult[0][csf("yarn_comp_percent1st")] . " and a.yarn_comp_type2nd=" . $rcvDtlsResult[0][csf("yarn_comp_type2nd")] . " and a.yarn_comp_percent2nd=" . $yarn_comp_percent2nd . " and a.yarn_type=" . $rcvDtlsResult[0][csf("yarn_type")] . " and a.color=" . $rcvDtlsResult[0][csf("color")] . " and a.supplier_id=" . $rcvDtlsResult[0][csf("supplier_id")] . " and a.item_category_id=1 and b.transaction_type=1 and b.item_category=1 and c.entry_form=1 and b.pi_wo_batch_no=" . $rcvDtlsResult[0][csf("pi_wo_batch_no")] . " and b.receive_basis=" . $receive_basis . "";
		}


		if ($whereCondition == "") $totalRcvQnty = 0;

		if ($receive_basis == 1)
		{
			if($variable_rcv_level==2)
			{
				$wo_po_qnty = return_field_value("sum(b.quantity) as qnty", "com_pi_master_details a, com_pi_item_details b", "a.id=b.pi_id and b.id=" . $rcvDtlsResult[0][csf("pi_wo_req_dtls_id")] . " and b.status_active=1 and b.is_deleted=0", "qnty");
			}
			else
			{
				$wo_po_qnty = return_field_value("sum(b.quantity) as qnty", "com_pi_master_details a, com_pi_item_details b", "a.id=b.pi_id and a.id=" . $rcvDtlsResult[0][csf("pi_wo_batch_no")] . " and b.count_name=" . $rcvDtlsResult[0][csf("yarn_count_id")] . " and b.yarn_composition_item1=" . $rcvDtlsResult[0][csf("yarn_comp_type1st")] . " and b.yarn_composition_percentage1=" . $rcvDtlsResult[0][csf("yarn_comp_percent1st")] . " and $orderBy_cond(b.yarn_composition_item2,0)=" . $yarn_comp_type2nd . " and $orderBy_cond(b.yarn_composition_percentage2,0)=" . $yarn_comp_percent2nd . " and b.yarn_type=" . $rcvDtlsResult[0][csf("yarn_type")] . " and b.color_id=" . $rcvDtlsResult[0][csf("color")] . " and b.status_active=1 and b.is_deleted=0", "qnty");
			}
		}
		else if ($receive_basis == 2)
		{
			if ($rcvDtlsResult[0][csf("receive_purpose")] == 2)
			{
				$wo_po_qnty = return_field_value("sum(b.yarn_wo_qty) as qnty", "wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b", " a.id=b.mst_id and a.id=" . $rcvDtlsResult[0][csf("pi_wo_batch_no")] . " and b.count=" . $rcvDtlsResult[0][csf("yarn_count_id")] . " and b.yarn_color=" . $rcvDtlsResult[0][csf("color")] . " and b.status_active=1 and b.is_deleted=0","qnty");
			}
			else if ($rcvDtlsResult[0][csf("receive_purpose")] == 15 || $rcvDtlsResult[0][csf("receive_purpose")] == 50 || $rcvDtlsResult[0][csf("receive_purpose")] == 51)
			{
				$wo_po_qnty = return_field_value("sum(b.yarn_wo_qty) as qnty", "wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b,wo_yarn_dyeing_dtls_fin_prod c", " a.id=b.mst_id and b.mst_id=c.mst_id and a.id=" . $rcvDtlsResult[0][csf("pi_wo_batch_no")] . " and b.count=" . $rcvDtlsResult[0][csf("yarn_count_id")] . " and c.yarn_color=" . $rcvDtlsResult[0][csf("color")] . " and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0","qnty");
			}
			else
			{
				if($variable_rcv_level==2)
				{
					$wo_po_qnty = return_field_value("sum(b.supplier_order_quantity) as qnty", "wo_non_order_info_mst a, wo_non_order_info_dtls b", "a.id=b.mst_id and b.id=" . $rcvDtlsResult[0][csf("pi_wo_req_dtls_id")] . " and b.status_active=1 and b.is_deleted=0", "qnty");
				}
				else
				{
					$wo_po_qnty = return_field_value("sum(b.supplier_order_quantity) as qnty", "wo_non_order_info_mst a, wo_non_order_info_dtls b", "a.id=b.mst_id and a.id=" . $rcvDtlsResult[0][csf("pi_wo_batch_no")] . " and b.yarn_count=" . $rcvDtlsResult[0][csf("yarn_count_id")] . " and b.yarn_comp_type1st=" . $rcvDtlsResult[0][csf("yarn_comp_type1st")] . " and b.yarn_comp_percent1st=" . $rcvDtlsResult[0][csf("yarn_comp_percent1st")] . " and $orderBy_cond(b.yarn_comp_type2nd,0)=" . $yarn_comp_type2nd . " and $orderBy_cond(b.yarn_comp_percent2nd,0)=" . $yarn_comp_percent2nd . " and b.yarn_type=" . $rcvDtlsResult[0][csf("yarn_type")] . " and b.color_name=" . $rcvDtlsResult[0][csf("color")] . " and b.status_active=1 and b.is_deleted=0", "qnty");
				}
			}
		}
		
		$mrr_result = sql_select("select sum(b.cons_quantity) as recv_qnty, c.recv_number,a.id as prod_id from product_details_master a, inv_transaction b, inv_receive_master c where $whereCondition and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.recv_number,a.id");
		foreach ($mrr_result as $val)
		{
			$totalRcvQnty += $val[csf("recv_qnty")];
			$mrr_arr[$val[csf("recv_number")]] = $val[csf("recv_number")];
			$prod_arr[$val[csf("prod_id")]] = $val[csf("prod_id")];
		}
		unset($mrr_result);

		$mrr_result_update = sql_select("select sum(b.cons_quantity) as recv_qnty, c.recv_number,a.id as prod_id from product_details_master a, inv_transaction b, inv_receive_master c where $whereCondition and b.id not in($rcv_dtls_id) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.recv_number,a.id");
		foreach ($mrr_result_update as $val)
		{
			$updateRcvQnty += $val[csf("recv_qnty")];
		}
		unset($mrr_result_update);
	}

	$mrr_nos = "'".implode("','",array_filter($mrr_arr))."'";
	$prod_ids = implode(",", array_filter($prod_arr));

	if(str_replace("'", "", $mrr_nos) != "" && $prod_arr!="")
	{
		$direct_mrr_return_res = sql_select("select sum(b.cons_quantity) as ret_qnty
			from product_details_master a, inv_transaction b , inv_issue_master c
			where a.id=b.prod_id and b.mst_id = c.id and c.entry_form = 8 and c.received_mrr_no in ($mrr_nos)
			and a.id in ($prod_ids) and b.status_active = 1 and b.is_deleted=0 and c.status_active = 1 and c.is_deleted = 0
			group by c.issue_number, b.prod_id");
		$rcvReturnQnty=0;
		foreach ($direct_mrr_return_res as $val)
		{
			$rcvReturnQnty += $val[csf("ret_qnty")];
		}
		unset($direct_mrr_return_res);

		//Issue Nos From Receive Mrr
		$issue_sql = sql_select("select a.id,b.cons_quantity, b.id as trans_id
			from  inv_issue_master a, inv_transaction b, inv_mrr_wise_issue_details c, inv_receive_master d, inv_transaction e
			where a.id = b.mst_id and b.id = c.issue_trans_id and c.recv_trans_id = e.id and d.id = e.mst_id
			and d.recv_number in ($mrr_nos) and b.prod_id in ($prod_ids)
			and a.entry_form = 3 and c.entry_form = 3 and d.item_category=1 and b.status_active = 1 and b.is_deleted = 0");
		foreach ($issue_sql as $val)
		{
			$issue_id_arr[$val[csf("id")]] = $val[csf("id")];
		}

		$issue_ids = implode(",", array_filter($issue_id_arr));

		if($issue_ids)
		{
			// $Issue Return From Issue
			$issue_ret_sql = sql_select("select a.id,a.recv_number,b.cons_quantity, b.id as trans_id
				from  inv_receive_master a, inv_transaction b
				where a.id = b.mst_id and a.entry_form =9 and b.item_category = 1
				and b.status_active = 1 and b.is_deleted = 0 and a.issue_id in ($issue_ids) and b.prod_id in ($prod_ids)");

			foreach ($issue_ret_sql as $val)
			{
				$issue_ret_ids[$val[csf("id")]] = $val[csf("id")];
			}
			unset($issue_ret_sql);

			$issue_ret_ids = implode(",", array_filter($issue_ret_ids));

			if($issue_ret_ids)
			{
				//Receive Return From Issue Return
				$rcvReturnQntyFromIssueRetArr = sql_select("select sum(b.cons_quantity) as return_qnty
					from  inv_issue_master a, inv_transaction b
					where a.id = b.mst_id and a.entry_form =8 and b.item_category = 1
					and b.status_active = 1 and b.is_deleted = 0 and a.received_id in ($issue_ret_ids) and b.prod_id in ($prod_ids)");
				foreach ($rcvReturnQntyFromIssueRetArr as $val)
				{
					$rcvReturnQnty += $val[csf("return_qnty")];
				}
			}
		}
	}
	
	foreach ($rcvDtlsResult as $row) {
		$over_receive_limit = (!empty($variable_set_invent)) ? $variable_set_invent[0][csf('over_rcv_percent')] : 0;
		$color_id = $row[csf('color')];
		echo "$('#cbo_yarn_count').val(" . $row[csf("yarn_count_id")] . ");\n";
		echo "$('#cbocomposition1').val(" . $row[csf("yarn_comp_type1st")] . ");\n";
		echo "$('#percentage1').val(" . $row[csf("yarn_comp_percent1st")] . ");\n";
		echo "$('#cbocomposition2').val(" . $row[csf("yarn_comp_type2nd")] . ");\n";
		if ($row[csf("yarn_comp_percent2nd")] == 0) $row[csf("yarn_comp_percent2nd")] = "";
		echo "$('#percentage2').val('" . $row[csf("yarn_comp_percent2nd")] . "');\n";
		echo "control_composition('percent_one');\n";
		echo "$('#cbo_yarn_type').val(" . $row[csf("yarn_type")] . ");\n";
		echo "rate_cond(" . $row[csf("receive_purpose")] . ");\n";


		$rcvPurpose = $row[csf("receive_purpose")];
		echo "load_drop_down( 'requires/yarn_receive_controller', $rcvPurpose,'load_drop_down_color2', 'cbo_color' );\n";

		echo "$('#cbo_color').val(" . $color_id . ");\n";
		if ($row[csf("receive_purpose")] == 16) {
			echo "$('#cbo_color').attr('disabled','disabled');\n"; 
		}

		echo "$('#txt_yarn_lot').val('" . $row[csf("lot")] . "');\n";
		
		echo "$('#txt_brand').val('" . $brand_name . "');\n";
		echo "$('#txt_receive_qty').val(" . $row[csf("order_qnty")] . ");\n";
		echo "$('#hdn_receive_qty').val(" . $row[csf("order_qnty")] . ");\n";
		echo "$('#txt_grey_qty').val(" . $row[csf("grey_quantity")] . ");\n";
		echo "$('#hdn_grey_qty').val(" . $row[csf("grey_quantity")] . ");\n";
		echo "$('#txt_rate').val(" . $row[csf("order_rate")] . ");\n";
		echo "$('#txt_avg_rate').val(" . $row[csf("cons_avg_rate")] . ");\n";
		echo "$('#txt_dyeing_charge').val(" . $row[csf("dye_charge")] . ");\n";
		echo "$('#txt_ile').val(" . $row[csf("order_ile_cost")] . ");\n";
		echo "$('#cbo_uom').val(" . $row[csf("order_uom")] . ");\n";
		echo "$('#txt_amount').val(" . $row[csf("order_amount")] . ");\n";
		echo "$('#txt_book_currency').val(" . $row[csf("cons_amount")] . ");\n";
		echo "$('#txt_order_qty').val(0);\n";
		echo "$('#txt_no_bag').val(" . $row[csf("no_of_bags")] . ");\n";
		echo "$('#txt_cone_per_bag').val(" . $row[csf("cone_per_bag")] . ");\n";
		echo "$('#txt_no_loose_cone').val(" . $row[csf("no_loose_cone")] . ");\n";
		echo "$('#txt_weight_per_bag').val(" . $row[csf("weight_per_bag")] . ");\n";
		echo "$('#txt_weight_per_cone').val(" . $row[csf("weight_per_cone")] . ");\n";

		echo "$('#txt_prod_code').val(" . $row[csf("prod_id")] . ");\n";
		echo "$('#job_no').val('" . $row[csf("job_no")] . "');\n";

		echo "load_room_rack_self_bin('requires/yarn_receive_controller', 'floor','floor_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."',this.value);\n";

		echo "$('#cbo_floor').val(" . $row[csf("floor_id")] . ");\n";

		echo "load_room_rack_self_bin('requires/yarn_receive_controller', 'room','room_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."',this.value);\n";

		echo "$('#cbo_room').val(" . $row[csf("room")] . ");\n";

		echo "load_room_rack_self_bin('requires/yarn_receive_controller', 'rack','rack_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."',this.value);\n";

		echo "$('#txt_rack').val(" . $row[csf("rack")] . ");\n";

		echo "load_room_rack_self_bin('requires/yarn_receive_controller', 'shelf','shelf_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."',this.value);\n";

		echo "$('#txt_shelf').val(" . $row[csf("self")] . ");\n";

		echo "load_room_rack_self_bin('requires/yarn_receive_controller', 'bin','bin_td', '".$row[csf('company_id')]."','"."','".$row[csf('store_id')]."','".$row[csf('floor_id')]."','".$row[csf('room')]."','".$row[csf('rack')]."','".$row[csf('self')]."',this.value);\n";

		echo "$('#cbo_bin').val(" . $row[csf("bin_box")] . ");\n";
		//echo "$('#txt_remarks').val(".$row[csf("remarks")].");\n";
		echo "$('#txt_remarks').val('" . $row[csf("remarks")] . "');\n";
		echo "$('#cbo_buyer_name').val('" . $row[csf("buyer_id")] . "');\n";
		//update id here
		echo "$('#update_id').val(" . $row[csf("id")] . ");\n";
		echo "$('#txt_wo_pi_dtls_id').val(" . $row[csf("pi_wo_req_dtls_id")] . ");\n";
		//echo "\n\n $wo_po_qnty - $totalRcvQnty + $rcvReturnQnty ". $row[csf("order_qnty")];
		$over_receive_limit_qnty = ($over_receive_limit>0)?($over_receive_limit / 100) * $wo_po_qnty:0;
		//$orderQnty = $wo_po_qnty - $totalRcvQnty + $rcvReturnQnty + $row[csf("order_qnty")]+$over_receive_limit_qnty;
		$orderQnty = $wo_po_qnty - ($totalRcvQnty-$rcvReturnQnty);
		$actual_rcv = ($updateRcvQnty-$rcvReturnQnty);
		echo "$('#txt_over_recv_limt').val(" . $over_receive_limit_qnty . ");\n";
		echo "$('#txt_order_qty').val(".$orderQnty. ");\n";
		echo "$('#txt_woQnty').val(" . $wo_po_qnty . ");\n";
		echo "$('#txt_overRecPerc').val(" . $over_receive_limit . ");\n";
		echo "$('#txt_totRecv').val(" . $actual_rcv . ");\n";
		echo "set_button_status(1, permission, 'fnc_yarn_receive_entry',1,1);\n";
		echo "fn_calile();\n";
		echo "storeUpdateUptoDisable();\n";
		echo "disable_enable_fields( 'cbo_yarn_count*cbocomposition1*percentage1*cbo_yarn_type*txt_yarn_lot', 1, '', '' );\n";
		

	}

	exit();
}


if ($action == "issue_challan_popup_info") {
	echo load_html_head_contents("Popup Info", "../../", 1, 1, $unicode);
	extract($_REQUEST);

	$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');
	$supplier_library = return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
	?>

	<script>
		function js_set_value(data) {
			var splitArr = data.split("_");
            $("#hidden_issue_id").val(splitArr[0]); 		// id number
            $("#hidden_challan_number").val(splitArr[1]); 	// mrr number
            parent.emailwindow.hide();
        }

        $(document).ready(function (e) {
        	setFilterGrid('tbl_list_search', -1);
        });
    </script>

</head>

<body>
	<div align="center" style="width:100%; margin-top:10px">
		<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
			<input type="hidden" name="hidden_issue_id" id="hidden_issue_id">
			<input type="hidden" name="hidden_challan_number" id="hidden_challan_number">
			<table width="630" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
					<th width="40">SL</th>
					<th width="130">Issue System Id</th>
					<th width="130">Challan No</th>
					<th width="140">Issue To</th>
					<th>Yarn Count</th>
				</thead>
			</table>
			<div style="width:630px; overflow-y: scroll; max-height:320px;" id="scroll_body">
				<table width="612" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table"
				id="tbl_list_search">
				<?
				$i = 1;
				if ($db_type == 0) {
					$sql = "select a.id, a.issue_number, a.challan_no, a.knit_dye_company, group_concat(distinct(c.yarn_count_id)) as yarn_count_id from inv_issue_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id and b.item_category=1 and b.transaction_type=2 and a.knit_dye_source=3 and a.knit_dye_company='$supplier' and a.issue_purpose=15 and a.entry_form=3 and a.status_active=1 and a.is_deleted=0 group by a.id";
				} else {
					$sql = "select a.id, a.issue_number, a.challan_no, a.knit_dye_company, LISTAGG(c.yarn_count_id, ',') WITHIN GROUP (ORDER BY c.yarn_count_id) as yarn_count_id from inv_issue_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and b.prod_id=c.id and b.item_category=1 and b.transaction_type=2 and a.knit_dye_source=3 and a.knit_dye_company='$supplier' and a.issue_purpose=15 and a.entry_form=3 and a.status_active=1 and a.is_deleted=0 group by a.id, a.issue_number, a.challan_no, a.knit_dye_company";
				}
				$result = sql_select($sql);
				foreach ($result as $row) {
					if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
					$data = $row[csf('id')] . "_" . $row[csf('challan_no')];

					$issue_to = $supplier_library[$row[csf('knit_dye_company')]];

					$yarn_count_id = array_unique(explode(",", $row[csf('yarn_count_id')]));
					$yarn_count = '';
					foreach ($yarn_count_id as $count_id) {
						if ($yarn_count == "") $yarn_count = $yarn_count_arr[$count_id]; else $yarn_count .= "," . $yarn_count_arr[$count_id];
					}
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer"
						onClick="js_set_value('<? echo $data; ?>');">
						<td width="40"><? echo $i; ?></td>
						<td width="130"><p>&nbsp;<? echo $row[csf('issue_number')]; ?></p></td>
						<td width="130"><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
						<td width="140"><p><? echo $issue_to; ?>&nbsp;</p></td>
						<td><p><? echo $yarn_count; ?>&nbsp;</p></td>
					</tr>
					<?
					$i++;
				}
				?>
			</table>
		</div>
	</form>
</div>
</body>
<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?

exit;
}

if ($action == "is_allocation_maintained") {
	$allocation_maintained = return_field_value("allocation", "variable_settings_inventory", "company_name =$data and variable_list=18 and item_category_id=1 and is_deleted=0 and status_active=1");
	if ($allocation_maintained != 1) $allocation_maintained = 0;
	echo "document.getElementById('allocation_maintained').value 	= '" . $allocation_maintained . "';\n";
	$variable_rcv_level =sql_select("select id, user_given_code_status from variable_settings_inventory where company_name='$data' and variable_list='31' and status_active=1 and is_deleted=0");
	echo "document.getElementById('variable_recv_level').value 	= '".$variable_rcv_level[0][csf("user_given_code_status")]."';\n";
	exit();
}


//################################################# function Here #########################################//


//function for domestic rate find--------------//
//parameters rate,ile cost,exchange rate,conversion factor
function return_domestic_rate($rate, $ile_cost, $exchange_rate, $conversion_factor)
{
	$rate_ile = $rate + $ile_cost;
	$rate_ile_exchange = $rate_ile * $exchange_rate;
	$doemstic_rate = $rate_ile_exchange / $conversion_factor;
	return $doemstic_rate;
}


//return product master table id ----------------------------------------//
function return_product_id($yarncount, $composition_one, $composition_two, $percentage_one, $percentage_two, $yarntype, $color, $yarnlot, $prodCode, $company, $supplier, $store, $uom, $yarn_type, $composition,$cbo_receive_purpose)
{

	$composition_one = str_replace("'", "", $composition_one);
	$composition_two = str_replace("'", "", $composition_two);
	$percentage_one = str_replace("'", "", $percentage_one);
	$percentage_two = str_replace("'", "", $percentage_two);
	$yarntype = str_replace("'", "", $yarntype);
	$color = str_replace("'", "", $color);
	$yarncount = str_replace("'", "", $yarncount);
	if ($percentage_one == "") $percentage_one = 0;
	if ($percentage_two == "") $percentage_two = 0;
	$cbo_receive_purpose = str_replace("'", "", $cbo_receive_purpose);
	if($cbo_receive_purpose==2 || $cbo_receive_purpose==15 || $cbo_receive_purpose==38 || $cbo_receive_purpose==43 || $cbo_receive_purpose==46 ) $dyed_type=1; else $dyed_type=2;
	if($cbo_receive_purpose==15) $is_twisted=1; else $is_twisted=0;

	//NOTE :- Yarn category array ID=1
	$whereCondition = "yarn_count_id=$yarncount and yarn_comp_type1st=$composition_one and yarn_comp_percent1st=$percentage_one and yarn_comp_type2nd=$composition_two and yarn_comp_percent2nd=$percentage_two and yarn_type=$yarntype and color=$color and company_id=$company and supplier_id=$supplier and item_category_id=1 and lot=$yarnlot and status_active=1 and is_deleted=0"; //and store_id=$store
	$prodMSTID = return_field_value("id", "product_details_master", "$whereCondition");
//return "select id from product_details_master where $whereCondition";die;
	$insertResult = true;
	if ($prodMSTID == false || $prodMSTID == "")
	{
		// new product create here--------------------------//
		$yarn_count_arr = return_library_array("select id, yarn_count from lib_yarn_count", 'id', 'yarn_count');

		$color_name_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');

		$compositionPart = $composition[$composition_one] . " " . $percentage_one;
		if ($percentage_two != 0) {
			$compositionPart .= " " . $composition[$composition_two] . " " . $percentage_two;
		}

		//$yarn_count.','.$composition.','.$ytype.','.$color;
		$product_name_details = $yarn_count_arr[$yarncount] . " " . $compositionPart . " " . $yarn_type[$yarntype] . " " . $color_name_arr[$color];
		$prodMSTID = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
		$field_array = "id,company_id,supplier_id,item_category_id,product_name_details,lot,item_code,unit_of_measure,yarn_count_id,yarn_comp_type1st,yarn_comp_percent1st,yarn_comp_type2nd,yarn_comp_percent2nd,yarn_type,color,dyed_type,inserted_by,insert_date,is_twisted";
		$data_array = "(" . $prodMSTID . "," . $company . "," . $supplier . ",1,'" . $product_name_details . "'," . $yarnlot . "," . $prodCode . "," . $uom . "," . $yarncount . "," . $composition_one . "," . $percentage_one . "," . $composition_two . "," . $percentage_two . "," . $yarntype . "," . $color . ",'" . $dyed_type . "','" . $user_id . "','" . $pc_date_time . "',".$is_twisted.")";
		//echo $field_array."<br>".$data_array."--".$product_name_details;die;
		$insertResult = false;
		//$insertResult = sql_insert("product_details_master",$field_array,$data_array,1);
	}
	if ($insertResult == true) {
		return $insertResult . "***" . $prodMSTID;
	} else {
		return $insertResult . "***" . $field_array . "***" . $data_array . "***" . $prodMSTID;
	}
}

if ($action == "yarn_receive_print") {
	extract($_REQUEST);
	$data = explode('*', $data);
	//print_r ($data);

	$sql = " select id, recv_number,supplier_id,currency_id,challan_no, receive_date, exchange_rate, store_id, receive_basis,lc_no,receive_purpose,booking_id from inv_receive_master where recv_number='$data[1]'";


	$dataArray = sql_select($sql);
	$receive_pur = $dataArray[0][csf("receive_purpose")];
	$receive_basis = $dataArray[0][csf("receive_basis")];

	$wo_id = $dataArray[0][csf("booking_id")];

	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$location = return_field_value("location_name", "lib_location", "company_id=$data[0]");
	$address = return_field_value("address", "lib_location", "company_id=$data[0]");
	$supplier_library = return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
	$store_library = return_library_array("select id, store_name from  lib_store_location", "id", "store_name");
	$yarn_desc_arr = return_library_array("select id,yarn_description from lib_subcon_charge", 'id', 'yarn_description');
	$const_comp_arr = return_library_array("select id,const_comp from lib_subcon_charge", 'id', 'const_comp');
	$lcNum = return_library_array("select id,lc_number from com_btb_lc_master_details", 'id', 'lc_number');
	$country_arr = return_library_array("select id,country_name from lib_country", 'id', 'country_name');

	$exchange_currency = 0;
	if ($receive_pur == 2) {
		$table_width = 1415;
		$exchange_currency = return_field_value("ecchange_rate", "wo_yarn_dyeing_mst", "id=$wo_id", "ecchange_rate");
	} else {
		$table_width = 930;
	}
	//echo $exchange_currency."Jhs<br>";

	if ( $receive_basis == 2 && ($receive_pur == 2 || $receive_pur == 12 || $receive_pur == 15 || $receive_pur == 38 || $receive_pur == 46 || $receive_pur == 50 || $receive_pur == 51) )  
	{

		$pay_mode = return_field_value("pay_mode", "wo_yarn_dyeing_mst", "id=$wo_id", "pay_mode");

		if($pay_mode==3 || $pay_mode==5)
		{
			$supplier_name = $company_library[$dataArray[0][csf('supplier_id')]];
		}else{
			$supplier_name = $supplier_library[$dataArray[0][csf('supplier_id')]];
		}
	}else{
		$supplier_name = $supplier_library[$dataArray[0][csf('supplier_id')]];
	}

	?>
	<div id="table_row" style="width:<? echo $table_width; ?>px;">
		<table width="<? echo $table_width; ?>" align="right">
			<tr class="form_caption">
				<td colspan="6" align="center" style="font-size:20px">
					<strong><? echo $company_library[$data[0]]; ?></strong></td>
				</tr>
				<tr class="form_caption">
					<td colspan="6" align="center" style="font-size:14px">
						<?
						$nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
						foreach ($nameArray as $result) {
							?>
							<? echo $result[csf('plot_no')]; ?>
							<? echo $result[csf('level_no')] ?>
							<? echo $result[csf('road_no')]; ?>
							<? echo $result[csf('block_no')]; ?>
							<? echo $result[csf('city')]; ?>
							<? echo $result[csf('zip_code')]; ?>
							<?php echo $result[csf('province')]; ?>
							<? echo $country_arr[$result[csf('country_id')]]; ?><br>
							<? echo $result[csf('email')]; ?>
							<? echo $result[csf('website')];
						}
						?>
					</td>
				</tr>
				<tr>
					<td colspan="6" align="center" style="font-size:16px"><strong><u>Material Receive
					Report</u></strong></center></td>
				</tr>
				<tr style="font-size:14px">
					<td width="120"><strong>Supplier Name:</strong></td>
					<td width="210px"><? echo $supplier_name; ?></td>
					<td width="110"><strong>MRR No:</strong></td>
					<td width="200px"><? echo $dataArray[0][csf('recv_number')]; ?></td>
					<td width="115"><strong>Currency:</strong></td>
					<td><? echo $currency[$dataArray[0][csf('currency_id')]]; ?></td>
				</tr>
				<tr style="font-size:14px">
					<td><strong>Challan No:</strong></td>
					<td><? echo $dataArray[0][csf('challan_no')]; ?></td>
					<td><strong>Receive Date:</strong></td>
					<td><? echo change_date_format($dataArray[0][csf('receive_date')]); ?></td>
					<? if ($exchange_currency > 0) {
						?>
						<td><strong>WO Exc. Rate:</strong></td>
						<td><? echo $exchange_currency; ?></td>
						<?
					} else {
						?>
						<td><strong>Exchange Rate:</strong></td>
						<td><? echo $dataArray[0][csf('exchange_rate')]; ?></td>
						<?
					}
					?>
				</tr>
				<tr
				=style="font-size:14px">
				<td><strong>Store Name:</strong></td>
				<td><? echo $store_library[$dataArray[0][csf('store_id')]]; ?></td>
				<td><strong>Receive Basis:</strong></td>
				<td><? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
				<?
				if ($dataArray[0][csf('receive_basis')] == 1) {
					?>
					<td><strong>LC NO:</strong></td>
					<td><? echo $lcNum[$dataArray[0][csf('lc_no')]]; ?></td>
					<?
				}
				if ($receive_pur == 2) $rate_text = "Avg. Rate BDT"; else $rate_text = "Rate";
				?>

			</tr>
		</table>
		<br>
		<div style="width:100%;">
			<table align="right" cellspacing="0" width="<? echo $table_width; ?>" border="1" rules="all"
				class="rpt_table">
				<thead bgcolor="#dddddd" align="center" style="font-size:12px">
					<tr>
						<th rowspan="2" width="30">SL</th>
						<th rowspan="2" width="100">WO/PI No</th>
						<th rowspan="2" width="100">Buyer</th>
						<th rowspan="2" width="140">Item Details</th>
						<?
						if ( $receive_basis == 2 && $receive_pur == 2) {
							?>
							<th rowspan="2" width="100">Color Range</th>
							<?
						}
						?>
						<th rowspan="2" width="60">Yarn Lot</th>
						<th rowspan="2" width="40">UOM</th>
						<th rowspan="2" width="60">Receive Qty</th>
						<th rowspan="2" width="50"><? echo $rate_text; ?></th>
						<?
						if ($receive_pur == 2) {
							?>
							<th rowspan="2"  width="60">Avg. Rate Currency</th>
							<th rowspan="2"  width="60">Grey Rate BDT</th>
							<th rowspan="2"  width="60">Grey Rate Currency</th>
							<th rowspan="2"  width="60">Dye. Charge BDT</th>
							<th rowspan="2"  width="60">Dye. Charge Currency</th>
							<?
						}
						?>
						<th rowspan="2" width="60">ILE Cost</th>
						<?
						if ($dataArray[0][csf('currency_id')] != 1) {

							?>
							<th colspan="2" width="120">Amount(<? echo $currency[$dataArray[0][csf('currency_id')]]; ?>)</th>
							<?
						}
						?>
						<th colspan="2" width="150">Amount(BDT)</th>
						<?
						if ($receive_pur == 2) {
							?>
							<th rowspan="2" width="80">Amount Currency</th>
							<?
						}
						?>
						<th rowspan="2" width="50">No. Of Bag</th>
						<th rowspan="2" width="50">No. Cons Per Bag</th>
						<th rowspan="2" width="50">No. Of Loose Cone</th>
						<th rowspan="2">Remarks</th>
					</tr>
					<tr>
						<?
						if ($dataArray[0][csf('currency_id')] != 1) {
							?>
							<th width="50">With ILE</th>
							<th width="50">Without ILE</th>
						<? } ?>
						<th width="50">With ILE</th>
						<th width="50">Without ILE</th>
					</tr>
				</thead>
				<?
				if ($db_type == 0) $wo_no_cond = " group_concat(b.work_order_no)"; else if ($db_type == 2) $wo_no_cond = "LISTAGG(b.work_order_no, ',') WITHIN GROUP (ORDER BY b.work_order_no)";
				$pi_arr = array();
				$pi_sql = "select a.id, a.pi_number, a.pi_basis_id, $wo_no_cond as work_order_no from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=1 group by a.id, a.pi_number, a.pi_basis_id";
				$pi_sql_res = sql_select($pi_sql);
				foreach ($pi_sql_res as $row) {
					if ($row[csf('pi_basis_id')] == 1) $wowoderno = implode(',', array_unique(explode(',', $row[csf('work_order_no')])));
					else if ($row[csf('pi_basis_id')] == 2) $wowoderno = "Independent"; else $wowoderno = "";
					$pi_arr[$row[csf('id')]]['pi_number'] = $row[csf('pi_number')];
					$pi_arr[$row[csf('id')]]['work_order'] = $wowoderno;
				}

				$wo_library = return_library_array("select id,wo_number from wo_non_order_info_mst where entry_form=144", "id", "wo_number");

				//$wo_yrn_library = return_library_array("select id, ydw_no from wo_yarn_dyeing_mst", "id", "ydw_no");

				if($wo_id!="")
				{	
					$wo_yarn_sql = "select a.id,a.ydw_no,b.count,b.yarn_color, b.color_range from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.id=$wo_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";

					$wo_yarn_data = sql_select($wo_yarn_sql);
					$wo_yarn_data_array = array();
					foreach ($wo_yarn_data as $row) {
						 $wo_yarn_data_array[$row[csf('id')]][$row[csf('count')]][$row[csf('yarn_color')]]['color_range'] = $row[csf('color_range')];
						 $wo_yrn_library[$row[csf('id')]] = $row[csf('ydw_no')];
					}

				}
				
				$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
				$cond = "";
				if ($data[1] != "") $cond .= " and a.recv_number='$data[1]'";

				$i = 1;
				$sql_result = sql_select("select a.recv_number, a.receive_basis, a.receive_purpose, b.id, b.receive_basis, b.pi_wo_batch_no, b.cone_per_bag, c.product_name_details, c.lot,c.yarn_count_id,c.color, b.order_uom, b.order_qnty, b.order_rate, b.cons_avg_rate, b.dye_charge, b.order_ile_cost, b.order_amount, b.cons_amount, b.no_of_bags,b.no_loose_cone, b.remarks,b.buyer_id
					from inv_receive_master a, inv_transaction b,  product_details_master c
					where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and b.item_category=1 and a.entry_form=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $cond");
				//echo $sql_result;
				$total_amt_currency = 0;
				foreach ($sql_result as $row) {
					if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
					$order_qnty_val_sum += $row[csf('order_qnty')];
					$order_amount_val_sum += $row[csf('order_amount')];
					$order_amount_val_without_ile_sum += $row[csf('order_amount')]-($row[csf('order_qnty')]*$row[csf('order_ile_cost')]);
					$no_of_bags_val_sum += $row[csf('no_of_bags')];
					$con_per_bags_sum += $row[csf('cone_per_bag')];
					$no_of_loose_cone += $row[csf('no_loose_cone')];

					if ($row[csf("receive_basis")] == 1)
						$receive_basis_cond = $pi_arr[$row[csf('pi_wo_batch_no')]]['pi_number'] . '<br><i>' . $pi_arr[$row[csf('pi_wo_batch_no')]]['work_order'] . '</i>';
					else if ($row[csf("receive_basis")] == 2 && ($row[csf("receive_purpose")] == 2 || $row[csf("receive_purpose")] == 7 || $row[csf("receive_purpose")] == 12 || $row[csf("receive_purpose")] == 15 || $row[csf("receive_purpose")] == 38 || $row[csf("receive_purpose")] == 46 || $row[csf("receive_purpose")] == 50 || $row[csf("receive_purpose")] == 51))
						$receive_basis_cond = $wo_yrn_library[$row[csf('pi_wo_batch_no')]];
					else
						$receive_basis_cond = $wo_library[$row[csf('pi_wo_batch_no')]];

					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:12px">
						<td><? echo $i; ?></td>
						<td>
							<div style="word-wrap:break-word; width:80px"><? echo $receive_basis_cond; ?></div>
						</td>
						<td>
							<div style="word-wrap:break-word; width:80px"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></div>
						</td>
						<td>
							<div style="word-wrap:break-word; width:140px"><? echo $row[csf('product_name_details')]; ?></div>
						</td>

						<?
						if ($receive_basis == 2 && $receive_pur == 2) {
							$color_range_id = $wo_yarn_data_array[$row[csf('pi_wo_batch_no')]][$row[csf('yarn_count_id')]][$row[csf('color')]]['color_range'];
							?>
							<td style="word-wrap:break-word; width:140px"><? echo $color_range[$color_range_id]; ?></td>
							<?
						}
						?>	

						<td><? echo $row[csf('lot')]; ?></td>
						<td><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
						<td align="right"><? echo number_format($row[csf('order_qnty')], 2, '.', ',');?></td>
						<td align="right"><? echo number_format($row[csf('order_rate')], 2, '.', ','); ?></td>
						<?
						if ($receive_pur == 2) {
							?>
							<td align="right"><? echo number_format(($row[csf('order_rate')] / $exchange_currency), 4, '.', ','); ?></td>
							<td align="right"><? echo number_format($row[csf('cons_avg_rate')], 2, '.', ','); ?></td>
							<td align="right"><? echo number_format(($row[csf('cons_avg_rate')] / $exchange_currency), 2, '.', ','); ?></td>
							<td align="right"><? echo number_format(($row[csf('dye_charge')]), 2, '.', ','); ?></td>
							<td align="right"><? echo number_format(($row[csf('dye_charge')] / $exchange_currency), 2, '.', ','); ?></td>
							<?
						}
						?>
						<td align="right"><? echo $row[csf('order_ile_cost')]; ?></td>
						<? if ($dataArray[0][csf('currency_id')] != 1) {
							?>
							<td align="right">
								<? echo number_format(($row[csf('order_amount')]), 2, '.', ',');
								$total_amt_currency += ($row[csf('order_amount')]); ?>
							</td>
							<td align="right">
								<? echo number_format(($row[csf('order_amount')] - ($row[csf('order_qnty')]*$row[csf('order_ile_cost')])), 2, '.', ',');
								$total_amt_currency += ($row[csf('order_amount')] - ($row[csf('order_qnty')]*$row[csf('order_ile_cost')])); ?>
							</td>
						<? }
						?>
						<td align="right">
							<? echo number_format(($row[csf('order_amount')] * $dataArray[0][csf('exchange_rate')]), 2, '.', ',');
							$total_bdt_amt_currency += ($row[csf('order_amount')] * $dataArray[0][csf('exchange_rate')]); ?>
						</td>
						<td align="right">
							<? echo number_format((($row[csf('order_amount')] - ($row[csf('order_qnty')]*$row[csf('order_ile_cost')])) * $dataArray[0][csf('exchange_rate')]), 2, '.', ',');
							$total_bdt_amt_currency_without_ile += (($row[csf('order_amount')] - ($row[csf('order_qnty')]*$row[csf('order_ile_cost')])) * $dataArray[0][csf('exchange_rate')]); ?>
						</td>

						<?
						if ($receive_pur == 2) {
							?>
							<td align="right"><? echo number_format(($row[csf('order_amount')] / $exchange_currency), 2, '.', ',');
							$total_amt_currency += ($row[csf('order_amount')] / $exchange_currency); ?></td>

							<?
						}
						?>
						<td align="right"><? echo $row[csf('no_of_bags')]; ?></td>
						<td align="right"><? echo $row[csf('cone_per_bag')]; ?></td>
						<td align="right"><? echo $row[csf('no_loose_cone')]; ?></td>
						<td><? echo $row[csf('remarks')]; ?></td>
					</tr>
					<?php
					$i++;
				}
				?>
				<tr>
					<td align="right" colspan="<? echo $colspan = ($receive_basis == 2 && $receive_pur == 2)?7:6; ?>">Total :</td>
					<td align="right"><? echo number_format($order_qnty_val_sum, 2, '.', ','); ?></td>
					<td align="right">&nbsp;</td>
					<?
					if ($receive_pur == 2) {
						?>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<td align="right">&nbsp;</td>
						<?
					}
					if ($dataArray[0][csf('currency_id')] == 1 || $receive_pur == 2) {
						$colspan = 2;
					} else {
						$colspan = 1;
					}

					if ($dataArray[0][csf('currency_id')] != 1) {
						?>
						<td align="right" colspan="2"><? echo number_format($order_amount_val_sum, 2, '.', ','); ?></td>
						<td align="right"><? echo number_format($order_amount_val_without_ile_sum, 2, '.', ','); ?></td>
						<?
					}
					?>
					<td align="right" colspan="<? echo $colspan ?>"><? echo number_format($total_bdt_amt_currency, 2, '.', ','); ?></td>
					<td align="right"><? echo number_format($total_bdt_amt_currency_without_ile, 2, '.', ','); ?></td>
					<?
					if ($receive_pur == 2) {
						?>
						<td align="right"><? //echo number_format($total_amt_currency,2,'.',',') ?></td>
						<?
					}
					?>
					<td align="right"><? echo $no_of_bags_val_sum; ?></td>
					<td align="right"><? echo $con_per_bags_sum; ?></td>
					<td align="right"><? echo $no_of_loose_cone; ?></td>
					<td align="right">&nbsp;</td>
				</tr>
			</table>
			<br>
			<?
			echo signature_table(65, $data[0], $table_width . "px");
			?>
		</div>
	</div>
	<?
	exit();
}
?>
