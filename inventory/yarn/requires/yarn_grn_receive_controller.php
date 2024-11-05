<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");
$permission = $_SESSION['page_permission'];
include('../../../includes/common.php');
$payment_yes_no = array(0 => "yes", 1 => "No");

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];
$user_id = $_SESSION['logic_erp']['user_id'];
$user_store_ids = $_SESSION['logic_erp']['store_location_id'];
$user_supplier_ids = trim($_SESSION['logic_erp']['supplier_id']);

$userCredential = sql_select("SELECT store_location_id  FROM user_passwd where id=$user_id");
$storeCredentialId = $userCredential[0][csf('store_location_id')];
if ($storeCredentialId != '') {
	$store_location_credential_cond = " and a.id in($storeCredentialId)";
}

if ($action == "load_drop_down_supplier") {
	if ($user_supplier_ids != "") {
		$user_supplier_cond = "and c.id in ($user_supplier_ids)";
	} else {
		$user_supplier_cond = "";
	}
	echo create_drop_down("cbo_supplier", 142, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data' $user_supplier_cond and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 0);
	exit();
}

if ($action == "load_drop_down_supplier_from_issue") {
	echo create_drop_down("cbo_supplier", 142, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c, inv_issue_master d where c.id=b.supplier_id and a.supplier_id = b.supplier_id and c.id=d.knit_dye_company and d.knit_dye_source=3 and d.issue_purpose in(15,50,51) and d.entry_form=3 and a.tag_company='$data' and b.party_type in(2,93,94) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 0);
	exit();
}

if ($action == "load_drop_down_store") {
	$data = explode("_", $data);
	$category_id = 1;
	$sql_store = "select a.id, a.store_name from lib_store_location a, lib_store_location_category b where a.id= b.store_location_id and
	a.company_id='$data[0]' and b.category_type=$category_id and a.status_active=1 and a.is_deleted=0 $store_location_credential_cond group by a.id, a.store_name order by a.store_name";
	echo create_drop_down("cbo_store_name", 142, $sql_store, "id,store_name", 1, "--Select store--", 0, "");
	exit();
}

if ($action == "load_drop_down_party") {
	echo create_drop_down("cbo_party", 142, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data' and b.party_type=91 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 0);
	exit();
}


if ($action == "get_library_exchange_rate") {
	$data_ref = explode("**", $data);
	$exchange_rate = sql_select("select conversion_rate from currency_conversion_rate where currency=$data_ref[0] and COMPANY_ID=$data_ref[1] and status_active=1 and is_deleted=0 order by id desc");
	if ($data == 1) {
		echo "document.getElementById('txt_exchange_rate').value 	= '1';\n";
	} else {
		echo "document.getElementById('txt_exchange_rate').value 	= '" . $exchange_rate[0][csf("conversion_rate")] . "';\n";
	}
	exit();
}


if ($action == "wo_pi_popup") {
	echo load_html_head_contents("WO/PI Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>

	<script>
		var update_id = '<? echo $update_id; ?>';

		function js_set_value(id, no, data, receive_basis) {
			if (update_id != "") {
				var response = trim(return_global_ajax_value(update_id, 'duplication_check', '', 'yarn_grn_receive_controller'));
				if (response != "") {
					var curr_data = data.split("**");
					var curr_supplier_id = curr_data[0];
					var curr_currency_id = curr_data[1];
					var curr_source = curr_data[2];
					var curr_lc_id = curr_data[4];

					var prev_data = response.split("**");
					var prev_supplier_id = prev_data[0];
					var prev_currency_id = prev_data[1];
					var prev_source = prev_data[2];
					var prev_lc_id = prev_data[3];

					if (!(curr_supplier_id == prev_supplier_id && curr_currency_id == prev_currency_id && curr_source == prev_source)) {
						alert("Supplier, Currency and Source Mix not allow in Same Received ID \n");
						//alert("Supplier, Currency and Source Mix not allow in Same Received ID \n"+curr_supplier_id+"=="+prev_supplier_id+"=="+curr_currency_id+"=="+prev_currency_id+"=="+curr_source+"=="+prev_source);
						return;
					}
				}
			}
			//alert("Fuad");return;
			$('#hidden_wo_pi_id').val(id);
			$('#hidden_wo_pi_no').val(no);
			$('#hidden_data').val(data);
			$('#receive_basis').val(receive_basis);
			parent.emailwindow.hide();
		}
	</script>

	</head>

	<body onLoad="set_hotkey()">
		<div align="center" style="width:1190px;">
			<form name="searchwofrm" id="searchwofrm">
				<fieldset style="width:990px; margin-left:5px">
					<legend>Enter search words</legend>
					<table cellpadding="0" cellspacing="0" border="1" rules="all" width="990" class="rpt_table">
						<thead>
							<th width="180">Receive Basis</th>
							<th width="180">Supplier Name</th>
							<th width="140">WO No</th>
							<th width="140">PI No</th>
							<th width="200">WO/PI Date</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:60px" class="formbutton" />
								<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
								<input type="hidden" name="hidden_wo_pi_id" id="hidden_wo_pi_id" class="text_boxes" value="">
								<input type="hidden" name="hidden_wo_pi_no" id="hidden_wo_pi_no" class="text_boxes" value="">
								<input type="hidden" name="booking_without_order" id="booking_without_order" class="text_boxes" value="">
								<input type="hidden" name="hidden_data" id="hidden_data" class="text_boxes" value="">
								<input type="hidden" name="receive_basis" id="receive_basis" class="text_boxes" value="">
								<input type="hidden" name="hid_booking_type" id="hid_booking_type" class="text_boxes" value="">
							</th>
						</thead>
						<tr class="general">
							<td align="center">
								<? echo create_drop_down("cbo_receive_basis", 140, $receive_basis_arr, "", 1, "-- Select --", $recieve_basis, "", 1, "1,2"); ?>
							</td>
							<td align="center" id="supplier_td_id">
								<?
								$sup_cond = "";

								if (str_replace("'", "", $cbo_supplier) > 0) $sup_cond = " and a.id=$cbo_supplier";
								//echo create_drop_down( "cbo_supplier", 140,"select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company=$cbo_company_id $sup_cond and b.party_type in(4,5) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name",'id,supplier_name', 1, '-- Select Supplier --',0,0);
								echo create_drop_down("cbo_supplier", 142, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company=$cbo_company_id $sup_cond and b.party_type =2 and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id,supplier_name", 1, "-- Select --", 0, "", 0);
								?>
							</td>
							<td align="center">
								<input type="text" style="width:100px" class="text_boxes" name="txt_wo_num" id="txt_wo_num" />
							</td>
							<td align="center">
								<input type="text" style="width:100px" class="text_boxes" name="txt_pi_no" id="txt_pi_no" />
							</td>
							<td align="center">
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:55px">To
								<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:55px">
							</td>
							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_wo_num').value+'_'+document.getElementById('cbo_receive_basis').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+<? echo $cbo_currency_id ?>+'_'+<? echo $cbo_source ?>+'_'+document.getElementById('cbo_supplier').value+'_'+document.getElementById('txt_pi_no').value+'_'+document.getElementById('cbo_year_selection').value+'_'+<? echo $cbo_receive_purpose ?>, 'create_wo_pi_search_list_view', 'search_div', 'yarn_grn_receive_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:60px;" />
							</td>
						</tr>
						<tr>
							<td colspan="8" align="center" height="30" valign="middle"><? echo load_month_buttons(1); ?></td>
						</tr>
					</table>
					<div style="margin-top:10px;" id="search_div" align="left"></div>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
	<?
}

if ($action == "loose_weight_popup") {
	echo load_html_head_contents("WO/PI Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>
	<script>
		function close_pop(table_id)
		{
			// alert("got it");
			var brkdwn = "";
			var row_num = $('#data_table tr').length - 2;
			var total = 0;
			for (var i = 1; i <= row_num; i++) {
				
				var rfid_id = "txt_rfid_" + i;
				var r_id = "txt_r_id_" + i;
				var weight_id = "txt_weight_" + i;
				
				var rfid = $("#" + rfid_id).val();
				var weight = $("#" + weight_id).val();
				var id = $("#" + r_id).val();

				if(rfid != '' && weight != '')
				{
					if(i!= 1)
					brkdwn += "__";
					if(r_id!="")
					{
						brkdwn += rfid+"-"+weight+"-"+id;
					}
					else
					{
						brkdwn += rfid+"-"+weight;				
					}
					total += parseFloat(weight) || 0;
				}			
			}
			// alert(brkdwn);
			// var total = $('#txt_weight_total').val(); receiveqnty_
			var recv_qty = 1*(parseFloat(parent.$('#receiveqnty_'+table_id+'').val()));
			var no_of_bag = parseFloat(parent.$('#noOfBag_'+table_id+'').val());
			var weight_per_bag = parseFloat(parent.$('#wetPerBag_'+table_id+'').val());
			var total_weight = (no_of_bag*weight_per_bag) + total;
			var balance = recv_qty-total_weight;
			if(balance<0)
			{
				alert("Total weight can not be greater than receive quantity\nReceive Quantity = "+recv_qty+"\nTotal Weight = "+total_weight+"");
				return;
			}

			parent.$('#looseBagWt_'+table_id+'').val(total);
			parent.$('#looseBagWt_brkdwn_'+table_id+'').val(brkdwn);
			parent.emailwindow.hide();
		}
		
		function increase_row(i) {
			var row_num = $('#data_table tr').length - 2;
			id = row_num + 1;

			var newRow = $('<tr class="general_' + id + '">' +
				'<td align="center">' + id + '</td>' +
				'<td align="center"><input type="text" class="text_boxes" name="txt_rfid_' + id + '" id="txt_rfid_' + id + '" readonly ondblclick="openmypage_rfid_popup('+id+');" value="Dbl-Click to browse RFID" Readonly style="width:180px;text-align: center; color:gray"/></td>' +
				'<input type="hidden" style="width:180px" class="text_boxes_numeric" name="txt_r_id_' + id + '" id="txt_r_id_' + id + '" value="" />'+
				'<td align="center"><input type="text" style="width:180px" class="text_boxes_numeric" onblur="calculate_total();" name="txt_weight_' + id + '" id="txt_weight_' + id + '"/></td>' +
				'<td align="center"><input type="button" id="increasetrim_' + id + '" onclick="increase_row(' + id + ')" style="width:30px" class="formbutton" value="+" ></td>' +
				'</tr>');

			$('#tbody').append(newRow);
		}

		function calculate_total() {
			var row_num = $('#data_table tr').length - 2;
			var total = 0;

			for (var i = 1; i <= row_num; i++) {
				var id = "txt_weight_" + i;
				var value = parseFloat($("#" + id).val()) || 0;
				total += value;
			}
			$('#txt_weight_total').val(total);
		}

		function openmypage_rfid_popup(tb_row) {
			// var breakdown = $('#looseBagWt_brkdwn_'+table_id+'').val();
			var title = 'RFID';
			var page_link = '../requires/yarn_grn_receive_controller.php?action=rfid_popup&tb_row='+tb_row+'';
			emailwindow = dhtmlmodal.open('EmailBox', 'iframe', page_link, title, 'width=400px,height=250px,center=1,resize=1,scrolling=0', '../');
			// alert(tb_row);
		}
	</script>
	</head>
	
	<body>
		<div align="center" style="width:470px;">
				<fieldset style="width:470px; margin-left:5px">
					<table cellpadding="0" cellspacing="0" border="1" rules="all" width="470px" class="rpt_table" id="data_table">
						<thead>
							<th width="80">SL</th>
							<th width="180">RFID No</th>
							<th width="180">Weight</th>
							<th width="30">&nbsp;</th>
						</thead>
						<?
						$i = 1;
						?>
						<tbody id="tbody">
							<?
								$total = 0;
								$breakdown_arr = explode("__", $breakdown);
								foreach($breakdown_arr as $key=>$row)
								{
									$row_arr = explode("-", $row);
									$r_rfid = $row_arr[0];
									$r_weight = $row_arr[1];
									$r_id = $row_arr[2];
									$total += (float)$r_weight;
									// echo $r_weight."<br>";
									?>
									<tr class="general_<? echo $i;?>">
										<td align="center">
											<?= $i; ?> 
										</td>
										<td align="center">
											<?
												if($r_rfid)
												{
													?>
													<input type="text" style="width:180px;text-align: center;" class="text_boxes_numeric" name="txt_rfid_<? echo $i; ?>" id="txt_rfid_<? echo $i; ?>" value="<? if($r_rfid) echo $r_rfid; ?>" ondblclick="openmypage_rfid_popup(<? echo $i;?>);" readonly />
													<?

												}
												else
												{
													?>
													<input type="text" style="width:180px;text-align: center; color:gray;" class="text_boxes_numeric" name="txt_rfid_<? echo $i; ?>" id="txt_rfid_<? echo $i; ?>" value="Dbl-Click to browse RFID" ondblclick="openmypage_rfid_popup(<? echo $i;?>);" readonly />
													<?
												}
											?>
											
											<input type="hidden" style="width:180px" class="text_boxes_numeric" name="txt_r_id_<? echo $i; ?>" id="txt_r_id_<? echo $i; ?>" value="<?= $r_id ?>" />
										</td>
										<td align="center">
											<input type="text" style="width:180px" class="text_boxes_numeric" onblur="calculate_total();" name="txt_weight_<? echo $i; ?>" id="txt_weight_<? echo $i; ?>" value="<?= $r_weight ?>" />
										</td>
										<td align="center">
											<input type="button" id="increasetrim_<? echo $i; ?>" onclick="increase_row(<? echo $i; ?>)" style="width:30px" class="formbutton" value="+" >
										</td>
									</tr>
									<?
									$i++;
								}
								// echo "<pre>";
								// print_r($breakdown_arr);
							?>
							
						</tbody>
						<tr>
							<td colspan="2" align="center"><p><b>Total</b></p></td>
							<td>
								<input type="text" style="width:180px" class="text_boxes_numeric" name="txt_weight_total" id="txt_weight_total" value="<?= $total; ?>" readonly />
							</td>
						</tr>
					</table>
					<div style="margin-top:10px;" id="search_div" align="left"></div>
				</fieldset>
				<div>
					<input type="button" id="close_btn" style="width:80px" class="formbutton" value="Close" onclick="close_pop(<? echo $table_id; ?>);">
				</div>
			
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
	<?
}

if ($action == "rfid_popup") {
	echo load_html_head_contents("Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	$status_arr = array(1=>"Active",2=>"Inactive");
	$rfid_type_arr = array(1=>"Regular",2=>"Loose Bag");
	?>
	<script>
		function load_data_to_parent(i)
		{
			var rfid = $('#rfid_'+i+'').html();
			parent.$('#txt_rfid_'+<?=$tb_row?>+'').val(rfid);
			parent.$('#txt_rfid_'+<?=$tb_row?>+'').css({"color": "black"});

			parent.emailwindow.hide();
		}
	</script>
	</head>
	
	<body>
		<?
		 $sql_result  = sql_select("SELECT id, rfid_number,rfid_type, status_active from lib_rfid_mst where is_deleted=0 order by id desc");
		?>
		<table class="rpt_table" width="100%" cellspacing="0" cellpadding="0" border="1" rules="all">
			<thead>
				<tr>
					<th>SL</th>
					<th>RFID No</th>
					<th>RFID Type</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody id="table_body" style="background: white;">
				<?
				$i = 1;
				foreach($sql_result as $row)
				{
					?>
					<tr id="row_<?=$i;?>" onclick="load_data_to_parent(<?= $i; ?>)" style="cursor:pointer !important;">
						<td><?= $i; ?></td>
						<td id="rfid_<?=$i;?>"><?= $row['RFID_NUMBER']; ?></td>
						<td><?= $rfid_type_arr[$row['RFID_TYPE']]; ?></td>
						<td><?= $status_arr[$row['STATUS_ACTIVE']]; ?></td>
					</tr>	
					<?
					$i++;
				}
				?>
			</tbody>
		</table>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	<script type="text/javascript">
		setFilterGrid('table_body',-1);
	</script>

	</html>
	<?
}

if ($action == "create_wo_pi_search_list_view") {
	$data = explode("_", $data);
	//echo $data[1]."jahid";die;
	$recieve_basis = $data[1];
	$company_id = $data[2];
	$date_form = $data[3];
	$date_to = $data[4];

	$cbo_currency_id = $data[5];
	$cbo_source = $data[6];
	$cbo_supplier = $data[7];
	$pi_num = $data[8];
	$wo_num = $data[0];
	$cbo_year = $data[9];
	$receive_purpose = $data[10];
	//echo $pay_mode.jahid;die;

	if ($recieve_basis < 1) {
		echo "Please Select Receive Basis.";
		die;
	}
	if ($wo_num == "" && $pi_num == "" && $date_form == "" && $date_to == "" && $cbo_supplier == 0) {
		echo "Please select date range.";
		die;
	}


	if ($date_form != "" && $date_to != "") {
		if ($db_type == 0) {
			$date_form = change_date_format($date_form, 'yyyy-mm-dd', "-");
			$date_to = change_date_format($date_to, 'yyyy-mm-dd', "-");
		} else {
			$date_form = change_date_format($date_form, '', '', 1);
			$date_to = change_date_format($date_to, '', '', 1);
		}
	} else {
		if ($db_type == 0) {
			$year_cond = " and year(a.insert_date)=$cbo_year ";
		} else if ($db_type == 2) {
			$year_cond = " and to_char(a.insert_date,'YYYY')=$cbo_year ";
		}
	}


	//echo $date_form."==".$date_to;die;
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$user_name_arr = return_library_array("select id, user_full_name from user_passwd", "id", "user_full_name");
	$sql_receive = "SELECT a.BOOKING_ID, sum(b.PARKING_QUANTITY) as PARKING_QUANTITY
	from INV_RECEIVE_MASTER a, QUARANTINE_PARKING_DTLS b  
	where a.id=b.mst_id and a.RECEIVE_BASIS=$recieve_basis and b.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.COMPANY_ID=$company_id and a.ENTRY_FORM=529 and b.ENTRY_FORM=529
	group by a.BOOKING_ID";
	//echo $sql_receive;//die;
	$sql_receive_result = sql_select($sql_receive);
	$pi_receive_data = array();
	foreach ($sql_receive_result as $val) {
		$pi_receive_data[$val["BOOKING_ID"]] += $val["PARKING_QUANTITY"];
	}
	unset($sql_receive_result);

	if ($recieve_basis == 1) {
		$search_field_cond = "";
		if (trim($wo_num) != "") {
			$search_field_cond .= " and b.work_order_no like '%$wo_num'";
		}

		if (trim($pi_num) != "") {
			$search_field_cond .= " and a.pi_number like '$pi_num'";
		}

		if ($date_form != "" && $date_to != "") {
			$search_field_cond .= " and a.pi_date between '$date_form' and '$date_to'";
		}

		if ($cbo_currency_id > 0) $search_field_cond .= " and a.currency_id=$cbo_currency_id";
		if ($cbo_source > 0) $search_field_cond .= " and a.source=$cbo_source";
		if ($cbo_supplier > 0) $search_field_cond .= " and a.supplier_id=$cbo_supplier";

		$btbLcArr = array();
		$lc_data = sql_select("select a.pi_id, b.id, b.lc_number from com_btb_lc_pi a, com_btb_lc_master_details b where a.status_active=1 and a.is_deleted=0 and a.com_btb_lc_master_details_id=b.id");
		foreach ($lc_data as $row) {
			$btbLcArr[$row[csf('pi_id')]] = $row[csf('id')] . "**" . $row[csf('lc_number')];
		}

		$approval_status_cond = "";
		if ($db_type == 0) {
			$approval_status = "select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company_id' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '" . change_date_format(date('d-m-Y'), 'yyyy-mm-dd') . "' and company_id='$company_id')) and page_id=18 and status_active=1 and is_deleted=0";
		} else {
			$approval_status = "select approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company_id' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '" . change_date_format(date('d-m-Y'), "", "", 1) . "' and company_id='$company_id')) and page_id=18 and status_active=1 and is_deleted=0";
		}
		$approval_status = sql_select($approval_status);
		if ($approval_status[0][csf('approval_need')] == 1) {
			$approval_status_cond = "and a.approved = 1";
		}

		//print_r($pi_receive_data);
		$sql = "SELECT a.ID, a.PI_NUMBER, a.SUPPLIER_ID, a.PI_DATE, a.LAST_SHIPMENT_DATE, a.PI_BASIS_ID, a.INTERNAL_FILE_NO, a.CURRENCY_ID, a.SOURCE, a.INSERTED_BY, sum(b.QUANTITY) as QUANTITY 
		from com_pi_master_details a,  com_pi_item_details b  
		where a.id=b.pi_id and a.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and a.importer_id=$company_id and a.goods_rcv_status<>1 $search_field_cond $approval_status_cond $year_cond
		group by a.id, a.pi_number, a.supplier_id, a.pi_date, a.last_shipment_date, a.pi_basis_id, a.internal_file_no, a.currency_id, a.source,a.inserted_by 
		order by a.ID desc";
		//echo $sql;
		$result = sql_select($sql);
	?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1090" class="rpt_table">
			<thead>
				<tr>
					<th colspan="10"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --", 4); ?></th>
				</tr>
				<tr>
					<th width="50">SL</th>
					<th width="135">PI No</th>
					<th width="80">PI Date</th>
					<th width="110">PI Basis</th>
					<th width="200">Supplier</th>
					<th width="100">Last Shipment Date</th>
					<th width="100">Internal File No</th>
					<th width="80">Currency</th>
					<th width="60">Source</th>
					<th>Insert User</th>
				</tr>
			</thead>
		</table>
		<div style="width:1110px; max-height:280px; overflow-y:scroll" id="list_container_batch" align="left">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1090" class="rpt_table" id="tbl_list_search">
				<?
				$i = 1;
				foreach ($result as $row) {
					$balance = $row['QUANTITY'] - $pi_receive_data[$row['ID']];
					if ($balance > 0) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";

						$data = $row['SUPPLIER_ID'] . "**" . $row['CURRENCY_ID'] . "**" . $row['SOURCE'] . "**" . $row['CURRENCY_ID'];
				?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row['ID']; ?>,'<? echo $row['PI_NUMBER']; ?>','<? echo $data; ?>','<? echo $recieve_basis; ?>');">
							<td width="50" align="center"><? echo $i; ?></td>
							<td width="135">
								<p><? echo $row['PI_NUMBER']; ?></p>
							</td>
							<td width="80" align="center"><? echo change_date_format($row['PI_DATE']); ?></td>
							<td width="110"><? echo $pi_basis[$row['PI_BASIS_ID']]; ?></td>
							<td width="200">
								<p><? echo $supplier_arr[$row['SUPPLIER_ID']]; ?>&nbsp;</p>
							</td>
							<td width="100" align="center"><? echo change_date_format($row['LAST_SHIPMENT_DATE']); ?>&nbsp;</td>
							<td width="100">
								<p><? echo $row['INTERNAL_FILE_NO']; ?></p>
							</td>
							<td width="80">
								<p><? echo $currency[$row['CURRENCY_ID']]; ?></p>
							</td>
							<td width="60">
								<p><? echo $source[$row['SOURCE']]; ?></p>
							</td>
							<td>
								<p><? echo $user_name_arr[$row['INSERTED_BY']]; ?></p>
							</td>
						</tr>
				<?
						$i++;
					}
				}
				?>
			</table>
		</div>
	<?
	} else if ($recieve_basis == 2) {
		//$search_string="%".trim($wo_num);

		/*if($db_type==0)
		{ 
			$approval_status="select page_id, approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company_id' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format(date('d-m-Y'),'yyyy-mm-dd')."' and company_id='$company_id')) and page_id in(9,10) and status_active=1 and is_deleted=0";
		}
		else
		{
			$approval_status="select page_id, approval_need from approval_setup_dtls where mst_id=(select id from approval_setup_mst where company_id='$company_id' and setup_date=(select max(setup_date) from approval_setup_mst where setup_date <= '".change_date_format(date('d-m-Y'), "", "",1)."' and company_id='$company_id')) and page_id in(9,10) and status_active=1 and is_deleted=0";
		}
		//echo $approval_status;die;
		$approval_status=sql_select($approval_status);
		$approval_status_cond_main=$approval_status_cond_short="";
		foreach($approval_status as $row)
		{
			if($row[csf("page_id")]==9 && $row[csf("approval_need")]==1)
			{
				$approval_status_cond_main=" and a.is_approved = 1";
			}
			if($row[csf("page_id")]==10 && $row[csf("approval_need")]==1)
			{
				$approval_status_cond_short=" and a.is_approved = 1";
			}
		}*/

		if ($receive_purpose == 2 || $receive_purpose == 7 || $receive_purpose == 12 || $receive_purpose == 15 || $receive_purpose == 38 || $receive_purpose == 46 || $receive_purpose == 50 || $receive_purpose == 51) {
			$sql_cond = "";
			if ($date_form != "" && $date_to != "") {
				$sql_cond .= " and a.booking_date between '$date_form' and '$date_to'";
			}

			if ($wo_num != "") $sql_cond .= " and a.YDW_NO like '%$wo_num'";
			if ($cbo_supplier > 0) $sql_cond .= " and a.supplier_id=$cbo_supplier";
			if ($cbo_currency_id > 0) $sql_cond .= " and a.CURRENCY=$cbo_currency_id";
			if ($cbo_source > 0) $sql_cond .= " and a.SOURCE=$cbo_source";

			if ($receive_purpose == 2) {
				$entry_form = "(41,42,114,125,135)";
				$purpose = "";
				$select_purpose = " 2 as SERVICE_TYPE";
				$group_by_service = "";
			} else {
				$entry_form = "(94,340)";
				$purpose = " and a.SERVICE_TYPE = $receive_purpose";
				$select_purpose = "a.SERVICE_TYPE";
				$group_by_service = ",a.SERVICE_TYPE";
			}

			$sql = "select a.ID, a.YARN_DYEING_PREFIX_NUM as WO_NUMBER_PREFIX_NUM, a.YDW_NO as WO_NUMBER, a.BOOKING_DATE as WO_DATE, a.DELIVERY_DATE as DELIVERY_DATE, a.SUPPLIER_ID, b.JOB_NO, a.ENTRY_FORM, a.BOOKING_WITHOUT_ORDER, a.IS_SALES, a.CURRENCY as CURRENCY_ID, $select_purpose, to_char(a.insert_date,'YYYY') as YEAR, a.INSERTED_BY, a.SOURCE, sum(b.YARN_WO_QTY) as QUANTITY
			from wo_yarn_dyeing_mst a, WO_YARN_DYEING_DTLS b
			where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form in $entry_form $purpose and a.pay_mode!=2 and a.company_id='$company_id' $sql_cond 
			group by a.ID, a.YARN_DYEING_PREFIX_NUM, a.YDW_NO, a.BOOKING_DATE, a.DELIVERY_DATE, a.SUPPLIER_ID, b.JOB_NO, a.ENTRY_FORM, a.BOOKING_WITHOUT_ORDER, a.IS_SALES, a.CURRENCY $group_by_service, a.insert_date, a.INSERTED_BY, a.SOURCE
			order by a.ID desc";
		} else {
			$search_field_cond = "";
			if (trim($wo_num) != "") {
				$search_field_cond = "and a.WO_NUMBER like '%$wo_num'";
			}

			if ($date_form != "" && $date_to != "") {
				$search_field_cond .= " and a.WO_DATE between '$date_form' and '$date_to'";
			}

			if ($cbo_currency_id > 0) $search_field_cond .= " and a.CURRENCY_ID=$cbo_currency_id";
			if ($cbo_source > 0) $search_field_cond .= " and a.SOURCE=$cbo_source";
			if ($cbo_supplier > 0) $search_field_cond .= " and a.SUPPLIER_ID=$cbo_supplier";


			//print_r($pi_receive_data);
			$sql = "SELECT a.ID, a.WO_NUMBER_PREFIX_NUM, a.WO_NUMBER, a.WO_DATE, a.DELIVERY_DATE, a.CURRENCY_ID, a.SOURCE, a.SUPPLIER_ID, b.JOB_NO, to_char(a.insert_date,'YYYY') as YEAR, a.INSERTED_BY, sum(b.SUPPLIER_ORDER_QUANTITY) as QUANTITY
			from WO_NON_ORDER_INFO_MST a, WO_NON_ORDER_INFO_DTLS b 
			where a.id=b.mst_id and a.pay_mode<>2 and a.COMPANY_NAME=$company_id and a.ENTRY_FORM=144 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.ITEM_CATEGORY_ID = 1 $search_field_cond $year_cond $approval_status_cond_main
			group by a.ID, a.WO_NUMBER_PREFIX_NUM, a.WO_NUMBER, a.WO_DATE, a.DELIVERY_DATE, a.CURRENCY_ID, a.SOURCE, a.SUPPLIER_ID, b.JOB_NO, a.INSERT_DATE, a.INSERTED_BY
			order by a.ID desc";
		}


		//echo $sql;//die;
		$result = sql_select($sql);
	?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1090" class="rpt_table">
			<thead>
				<tr>
					<th colspan="9"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --", 4); ?></th>
				</tr>
				<tr>
					<th width="50">SL</th>
					<th width="120">WO No</th>
					<th width="100">WO Date</th>
					<th width="150">Supplier</th>
					<th width="100">Delivary date</th>
					<th width="100">Source</th>
					<th width="100">Currency</th>
					<th width="120">Job No</th>
					<th>Insert User</th>
				</tr>
			</thead>
		</table>
		<div style="width:1110px; max-height:280px; overflow-y:scroll" id="list_container_batch" align="left">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1090" class="rpt_table" id="tbl_list_search">
				<?
				$i = 1;
				foreach ($result as $row) {
					$balance = $row['QUANTITY'] - $pi_receive_data[$row['ID']];
					//echo $balance."=".$row['QUANTITY']."=".$pi_receive_data[$row['ID']]."<br>";
					if ($balance > 0) {
						if ($i % 2 == 0)
							$bgcolor = "#E9F3FF";
						else
							$bgcolor = "#FFFFFF";

						$data = $row['SUPPLIER_ID'] . "**" . $row['CURRENCY_ID'] . "**" . $row['SOURCE'] . "**" . $row['CURRENCY_ID'];
				?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value(<? echo $row["ID"]; ?>,'<? echo $row['WO_NUMBER']; ?>','<? echo $data; ?>','<? echo $recieve_basis; ?>');">
							<td width="50" align="center"><? echo $i; ?></td>
							<td width="120" align="center">
								<p><? echo $row['WO_NUMBER']; ?></p>
							</td>
							<td width="100" align="center">
								<p> <? echo change_date_format($row['WO_DATE']); ?> </p>
							</td>
							<td width="150" align="center"><? echo $supplier_arr[$row['SUPPLIER_ID']]; ?></td>
							<td width="100" align="center">
								<p><? echo change_date_format($row['DELIVERY_DATE']); ?></p>
							</td>
							<td width="100" align="center"><? echo $source[$row['SOURCE']]; ?>&nbsp;</td>
							<td width="100">
								<p><? echo $currency[$row['CURRENCY_ID']]; ?>&nbsp;</p>
							</td>
							<td width="120" align="center"><? echo $row['JOB_NO']; ?>&nbsp;</td>
							<td>
								<p><? echo $user_name_arr[$row['INSERTED_BY']]; ?></p>
							</td>
						</tr>
				<?
						$i++;
					}
				}
				?>
			</table>
		</div>
	<?
	}
	exit();
}


if ($action == 'mrr_details') {
	//echo $data;die;

	?>
	<tr id="row_1" align="center">
		<!-- <td id="count_1"></td>
        <td id="composition_1"></td>
        <td id="comPersent_1"></td>
        <td id="yarnType_1"></td>
        <td id="color_1"></td>
        <td id="tdlot_1"><input type="text" name="TxtLot[]" id="TxtLot_1" class="text_boxes" style="width:60px;" value="" /></td>
        <td id="tdbrand_1"><input type="text" name="TxtBrand[]" id="TxtBrand_1" class="text_boxes" style="width:60px;" value="" /></td>
        <td id="tdreceiveqnty_1"><input type="text" name="receiveqnty[]" id="receiveqnty_1" class="text_boxes_numeric" style="width:60px;" value="" onBlur="calculate(1);"/></td>
        <td id="tdgreyqnty_1"><input type="text" name="greyqnty[]" id="greyqnty_1" class="text_boxes_numeric" style="width:60px;" value="" onBlur="calculate(1);"/></td>
        <td id="uom_1"></td> -->
		<td id="td_count_1">
			<?
			echo create_drop_down("count_1", 70, "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1 order by yarn_count", "id,yarn_count", 1, "-- Select --", 0, "", 0, "");
			?>
		</td>
		<td id="td_composition_1">
			<?
			echo create_drop_down("composition_1", 100, $composition, "", 1, "-- Select --", 0, "", 0, "", "", "", $ommitComposition);
			?>
		</td>
		<td id="td_comPersent_1">
			<input type="text" name="txtpacent_1" id="comPersent_1" class="text_boxes" value="100" style="width:40px;" />
		</td>
		<td id="td_yarnType_1">
			<?
			echo create_drop_down("yarnType_1", 100, $yarn_type, "", 1, "-- Select --", $row[csf("type_id")], "", $disabled, "", "", "", $ommitYarnType);
			?>
		</td>
		<td id="td_color_1">
			<input type="text" name="txtyarncolor[]" id="color_1" class="text_boxes" value="GREY" style="width:75px;" onFocus="add_auto_complete( 1 )" />
		</td>
		<td id="tdlot_1"><input type="text" name="TxtLot[]" id="TxtLot_1" class="text_boxes" style="width:40px;" value="" /></td>
		<td id="tdbrand_1"><input type="text" name="TxtBrand[]" id="TxtBrand_1" class="text_boxes" style="width:60px;" value="" /></td>
		<td id="td_uom_1">
			<?
			echo create_drop_down("uom_1", 60, $unit_of_measurement, "", 1, "-- Select--", 12, "", 0);
			?>
		</td>
		<td id="tdreceiveqnty_1"><input type="text" name="receiveqnty[]" id="receiveqnty_1" class="text_boxes_numeric" style="width:50px;" value="" onchange="calculate_wt_per_cone(1); calculate_loose_bag(1); calculate_ammount(1);" /></td>
		<td id="tdgreyqnty_1"><input type="text" name="greyqnty[]" id="greyqnty_1" class="text_boxes_numeric" style="width:45px;" value="" /></td>

		<td id="td_rate_1">
			<input type="text" name="rate[]" id="rate_1" class="text_boxes" value="" style="width:75px;" onchange="calculate_ammount(1); calculate_rate_in_bdt(1);" />
		</td>
		<td id="td_greyRate_1">
			<input type="text" name="greyrate[]" id="greyRate_1" class="text_boxes" value="" style="width:75px;" />
		</td>
		<td id="td_DCharge_1">
			<input type="text" name="DCharge[]" id="DCharge_1" class="text_boxes" value="" style="width:75px;" />
		</td>
		<td id="td_ilePersent_1">
			<input type="text" name="ilePersent[]" id="ilePersent_1" class="text_boxes" value="" style="width:75px;" />
		</td>
		<td id="td_amount_1">
			<input type="text" name="amount[]" id="amount_1" class="text_boxes" value="" style="width:75px;" onclick="calculate_ammount(1);" readonly />
		</td>
		<td id="td_avgRate_1">
			<input type="text" name="avgRate[]" id="avgRate_1" class="text_boxes" value="" style="width:75px;" />
		</td>
		<td id="td_bookCurrency_1">
			<input type="text" name="bookCurrency[]" id="bookCurrency_1" class="text_boxes" value="" style="width:75px;" />
		</td>
		<td id="td_woPiBalQnty_1">
			<input type="text" name="woPiBalQnty[]" id="woPiBalQnty_1" class="text_boxes" value="" style="width:75px;" readonly />
		</td>
		<td id="td_overRcvQnty_1">
			<input type="text" name="overRcvQnty[]" id="overRcvQnty_1" class="text_boxes" value="" style="width:75px;" />
		</td>
		<td id="tdNoOfBag_1"><input type="text" name="noOfBag[]" id="noOfBag_1" class="text_boxes_numeric" style="width:40px;" value="" onchange="calculate_wt_per_cone(1); calculate_loose_bag(1);" /></td>	
		<td id="tdWetPerBag_1"><input type="text" name="wetPerBag[]" id="wetPerBag_1" class="text_boxes_numeric" style="width:40px;" value="" onchange="calculate_wt_per_cone(1); calculate_loose_bag(1);" /></td>
		<td id="tdNoOfLooseBag_1"><input type="text" name="noOfLooseBag[]" id="noOfLooseBag_1" class="text_boxes_numeric" style="width:40px;" value="" onchange="calculate_wt_per_cone(1); calculate_loose_bag(1);" /></td>
		<td id="tdLooseBagWt_1">
			<input type="text" name="looseBagWt[]" id="looseBagWt_1" class="text_boxes_numeric" style="width:40px;" value="" onclick="calculate_wt_per_cone(1);" ondblclick="openmypage_lose_wt_popup(1);" readonly />
			<input type="hidden" name="looseBagWt_brkdwn[]" id="looseBagWt_brkdwn_1" value="" readonly>
		</td>
		
		<td id="tdConPerBag_1"><input type="text" name="conPerBag[]" id="conPerBag_1" class="text_boxes_numeric" style="width:40px;" value="" onchange="calculate_wt_per_cone(1); calculate_loose_bag(1);" /></td>
		<td id="tdLoseCone_1"><input type="text" name="loseCone[]" id="loseCone_1" class="text_boxes_numeric" style="width:40px;" value="" onchange="calculate_wt_per_cone(1); calculate_loose_bag(1);" /></td>
		<td id="tdWetPerCon_1"><input type="text" name="wetPerCon[]" id="wetPerCon_1" class="text_boxes_numeric" style="width:40px;" value="" onclick="calculate_wt_per_cone(1); calculate_loose_bag(1);" /></td>
		<td></td>
		<td id="remarks_1">
			<input type="text" name="remarks[]" id="remarks_1" class="text_boxes" value="" style="width:75px;" />
			<input type="hidden" name="updatedtlsid[]" id="updatedtlsid_1" value="" readonly>
			<input type="hidden" name="piWoDtlsId[]" id="piWoDtlsId_1" value="" readonly>
		</td>
	</tr>
	<?
	exit();
}


if ($action == 'show_fabric_desc_listview') {
	$data = explode("**", $data);
	//print_r($data);
	$bookingNo_piId = $data[0];
	$receive_basis = $data[1];
	$booking_without_order = $data[2];
	$company = $data[3];
	$source = $data[4];
	$exchange_rate = $data[5];
	$receive_purpose = $data[6];
	$cr_date = date("d-m-Y");
	//echo $cr_date."=".$company;
	$exchange_rate = set_conversion_rate(2, $cr_date, $company);

	$sql = "select standard from variable_inv_ile_standard where source='$source' and company_name=$company and category=1 and status_active=1 and is_deleted=0";
	//echo $sql;
	$result = sql_select($sql, 1);
	foreach ($result as $row) {
		$ile = $row[csf("standard")];
		$ile_percentage = ($row[csf("standard")] / 100) * $rate;
	}
	if ($ile_percentage == "") $ile_percentage = 0;

	$variable_set_invent = sql_select("select category,over_rcv_status,over_rcv_percent,over_rcv_payment from variable_inv_ile_standard where company_name=$company and variable_list=23 and category = 1 order by id");
	$over_receive_limit = !empty($variable_set_invent) ? $variable_set_invent[0][csf('over_rcv_percent')] : 0;

	$prev_return_entry = "select b.WO_PI_DTLS_ID, sum(b.PARKING_QUANTITY) as PARKING_QUANTITY   
	from INV_ISSUE_MASTER a, quarantine_parking_dtls b
	where a.id=b.mst_id and a.entry_form=531 and b.entry_form=531 and b.ITEM_CATEGORY_ID=1 and a.company_id=$company and a.ISSUE_BASIS=$receive_basis and b.WO_PI_ID='$bookingNo_piId' and a.status_active=1 and b.status_active=1
	group by b.WO_PI_DTLS_ID";

	$prev_return_entry_result = sql_select($prev_return_entry);
	$prev_return = array();
	foreach ($prev_return_entry_result as $row) {
		$prev_return[$row["WO_PI_DTLS_ID"]] = $row["PARKING_QUANTITY"];
	}

	$prev_entry = "select b.WO_PI_DTLS_ID, sum(b.PARKING_QUANTITY) as PARKING_QUANTITY   
	from inv_receive_master a, quarantine_parking_dtls b
	where a.id=b.mst_id and a.entry_form=529 and b.entry_form=529 and b.ITEM_CATEGORY_ID=1 and a.company_id=$company and a.receive_basis=$receive_basis and b.WO_PI_ID='$bookingNo_piId' and a.status_active=1 and b.status_active=1
	group by b.WO_PI_DTLS_ID";
	//echo $prev_entry;
	$prev_entry_result = sql_select($prev_entry);
	$prev_data = array();
	foreach ($prev_entry_result as $row) {
		$prev_data[$row["WO_PI_DTLS_ID"]] = $row["PARKING_QUANTITY"] - $prev_return[$row["WO_PI_DTLS_ID"]];
	}

	if ($receive_basis == 1) {
		$sql = "select a.ID, a.PI_ID as MST_ID, a.WORK_ORDER_ID, a.WORK_ORDER_DTLS_ID, a.COUNT_NAME, a.YARN_COMPOSITION_ITEM1, a.YARN_COMPOSITION_PERCENTAGE1, a.YARN_TYPE, a.COLOR_ID, a.UOM, a.QUANTITY, a.NET_PI_RATE, a.NET_PI_AMOUNT
		from com_pi_item_details a
		where a.pi_id='$bookingNo_piId' and a.status_active=1 and a.is_deleted=0";
	} else if ($receive_basis == 2) {
		$ydwo_ecchange_rate = return_field_value("ecchange_rate", "wo_yarn_dyeing_mst", "id=$bookingNo_piId", "ecchange_rate");
		if ($ydwo_ecchange_rate == 1) $wo_ecchange_rate = $exchange_rate;
		else $wo_ecchange_rate = 1;
		if ($receive_purpose == 15 || $receive_purpose == 50 || $receive_purpose == 51) {
			$sql = "select b.ID, b.MST_ID, b.MST_ID as WORK_ORDER_ID, 0 as REQUISITION_DTLS_ID, a.YARN_COUNT as COUNT_NAME, a.YARN_COMP as YARN_COMPOSITION_ITEM1, a.YARN_PERC as YARN_COMPOSITION_PERCENTAGE1, a.YARN_TYPE, a.YARN_COLOR as COLOR_ID, b.UOM, b.YARN_WO_QTY as QUANTITY, ((c.AVG_RATE_PER_UNIT/$exchange_rate)+(b.DYEING_CHARGE/$wo_ecchange_rate)) as NET_PI_RATE, ((c.AVG_RATE_PER_UNIT/$exchange_rate)+(b.DYEING_CHARGE/$wo_ecchange_rate))*b.YARN_WO_QTY as NET_PI_AMOUNT, b.DYEING_CHARGE, 0 as ENTRY_FORM, c.AVG_RATE_PER_UNIT
			from wo_yarn_dyeing_dtls_fin_prod a, wo_yarn_dyeing_dtls b, PRODUCT_DETAILS_MASTER c
			where a.MST_ID=b.MST_ID and b.PRODUCT_ID=c.id and b.MST_ID='$bookingNo_piId' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		} elseif ($receive_purpose == 2 || $receive_purpose == 7 || $receive_purpose == 12 || $receive_purpose == 38 || $receive_purpose == 46) {
			$item_issue_prod = " select c.id as PROD_ID, c.YARN_COUNT_ID, c.YARN_COMP_TYPE1ST, c.YARN_TYPE, c.AVG_RATE_PER_UNIT from INV_ISSUE_MASTER a, INV_TRANSACTION b, PRODUCT_DETAILS_MASTER c where a.id=b.mst_id and b.prod_id=c.id and b.item_category=1 and b.transaction_type=2 and a.status_active=1 and b.status_active=1 and a.BOOKING_ID=$bookingNo_piId and a.ISSUE_BASIS=1 and a.ISSUE_PURPOSE=2";
			$item_issue_prod_result = sql_select($item_issue_prod);
			$yarn_service_prod_rate = array();
			foreach ($item_issue_prod_result as $val) {
				$yarn_service_prod_rate[$val["YARN_COUNT_ID"]][$val["YARN_COMP_TYPE1ST"]][$val["YARN_TYPE"]] = $val["AVG_RATE_PER_UNIT"];
			}

			$sql = "select a.ID, a.MST_ID, a.MST_ID as WORK_ORDER_ID, 0 as REQUISITION_DTLS_ID, b.YARN_COUNT_ID as COUNT_NAME, b.YARN_COMP_TYPE1ST as YARN_COMPOSITION_ITEM1, b.YARN_COMP_PERCENT1ST as YARN_COMPOSITION_PERCENTAGE1, b.YARN_TYPE, b.COLOR as COLOR_ID, a.UOM, a.YARN_WO_QTY as QUANTITY, ((b.AVG_RATE_PER_UNIT/$exchange_rate)+(a.DYEING_CHARGE/$wo_ecchange_rate)) as NET_PI_RATE, ((b.AVG_RATE_PER_UNIT/$exchange_rate)+(a.DYEING_CHARGE/$wo_ecchange_rate))*a.YARN_WO_QTY as NET_PI_AMOUNT, a.DYEING_CHARGE, a.ENTRY_FORM, b.AVG_RATE_PER_UNIT 
			from WO_YARN_DYEING_DTLS a, PRODUCT_DETAILS_MASTER b
			where a.PRODUCT_ID=b.id and a.MST_ID='$bookingNo_piId' and a.status_active=1 and a.is_deleted=0 and a.entry_form in(41,42,94,135)
			union all
			select a.ID, a.MST_ID, a.MST_ID as WORK_ORDER_ID, 0 as REQUISITION_DTLS_ID, a.COUNT as COUNT_NAME, a.YARN_COMP_TYPE1ST as YARN_COMPOSITION_ITEM1, a.YARN_COMP_PERCENT1ST as YARN_COMPOSITION_PERCENTAGE1, a.YARN_TYPE, a.YARN_COLOR as COLOR_ID, a.UOM, a.YARN_WO_QTY as QUANTITY, 0 as NET_PI_RATE, 0 as NET_PI_AMOUNT, a.DYEING_CHARGE, a.ENTRY_FORM, 0 as AVG_RATE_PER_UNIT
			from WO_YARN_DYEING_DTLS a
			where a.MST_ID='$bookingNo_piId' and a.status_active=1 and a.is_deleted=0 and a.entry_form in(125,114,340)";
		} else {
			$sql = "select a.ID, a.MST_ID, a.MST_ID as WORK_ORDER_ID, a.REQUISITION_DTLS_ID, a.YARN_COUNT as COUNT_NAME, a.YARN_COMP_TYPE1ST as YARN_COMPOSITION_ITEM1, a.YARN_COMP_PERCENT1ST as YARN_COMPOSITION_PERCENTAGE1, a.YARN_TYPE, a.COLOR_NAME as COLOR_ID, a.UOM, a.SUPPLIER_ORDER_QUANTITY as QUANTITY, a.RATE as NET_PI_RATE, a.AMOUNT as NET_PI_AMOUNT, 0 as DYEING_CHARGE, 0 as AVG_RATE_PER_UNIT
			from WO_NON_ORDER_INFO_DTLS a
			where a.MST_ID='$bookingNo_piId' and a.status_active=1 and a.is_deleted=0";
		}
	}
	//echo $sql; //die;
	$data_array = sql_select($sql);
	$yarn_count_library = return_library_array("select id,yarn_count from lib_yarn_count where status_active=1 and is_deleted=0", 'id', 'yarn_count');
	$color_library = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$i = 1;
	foreach ($data_array as $row) {
		if ($receive_basis == 2) {
			if ($receive_purpose == 15 || $receive_purpose == 50 || $receive_purpose == 51) {
				$grey_rate_tk = $row["AVG_RATE_PER_UNIT"];
			} else if ($receive_purpose == 2 || $receive_purpose == 7 || $receive_purpose == 12 || $receive_purpose == 38 || $receive_purpose == 46) {
				if ($row['ENTRY_FORM'] == 125 || $row['ENTRY_FORM'] == 114 || $row['ENTRY_FORM'] == 340) {
					$row["NET_PI_RATE"] = ($yarn_service_prod_rate[$row["COUNT_NAME"]][$row["YARN_COMPOSITION_ITEM1"]][$row["YARN_TYPE"]] / $exchange_rate) + ($row['DYEING_CHARGE'] / $wo_ecchange_rate);
					$grey_rate_tk = $yarn_service_prod_rate[$row["COUNT_NAME"]][$row["YARN_COMPOSITION_ITEM1"]][$row["YARN_TYPE"]];
				} else {
					$grey_rate_tk = $row["AVG_RATE_PER_UNIT"];
				}
			} else {
				$grey_rate_tk = 0;
			}
		}

		$prev_rcv_qnty = $prev_data[$row["ID"]];
		$qnty = ($row['QUANTITY'] - $prev_rcv_qnty);
		$ile_cost = 0;
		if ($ile_percentage > 0) $ile_cost = $ile_percentage * $row["NET_PI_RATE"]; //ile cost = (ile/100)*rate
		$conversion_factor = 1; // yarn always KG
		$domestic_rate = return_domestic_rate($row["NET_PI_RATE"], $ile_cost, $exchange_rate, $conversion_factor);
		$book_currency = $qnty * $domestic_rate;
		$amount = $qnty * $row["NET_PI_RATE"];
		$over_receive_limit_qnty = ($over_receive_limit > 0) ? ($over_receive_limit / 100) * $qnty : 0;
		$allow_total_qty = number_format(($qnty + $over_receive_limit_qnty), 2, '.', '');
		//echo $qnty."=".$row['QUANTITY']."=".$prev_rcv_qnty."<br>";
		if ($qnty > 0) {
			if ($i % 2 == 0) $bgcolor = "#E9F3FF";
			else $bgcolor = "#FFFFFF";
	?>

			<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $i; ?>" align="center">
				<td id="count_<? echo $i; ?>" title="<? echo $row["COUNT_NAME"]; ?>"><? echo $yarn_count_library[$row["COUNT_NAME"]]; ?></td>
				<td id="composition_<? echo $i; ?>" title="<? echo $row["YARN_COMPOSITION_ITEM1"]; ?>"><? echo $composition[$row["YARN_COMPOSITION_ITEM1"]]; ?></td>
				<td id="comPersent_<? echo $i; ?>" title="<? echo $row["YARN_COMPOSITION_PERCENTAGE1"]; ?>"><? echo $row["YARN_COMPOSITION_PERCENTAGE1"]; ?></td>
				<td id="yarnType_<? echo $i; ?>" title="<? echo $row["YARN_TYPE"]; ?>"><? echo $yarn_type[$row["YARN_TYPE"]]; ?></td>
				<td id="color_<? echo $i; ?>" title="<? echo $row['COLOR_ID']; ?>"><? echo $color_library[$row["COLOR_ID"]]; ?></td>
				<td id="tdlot_<? echo $i; ?>" title=""><input type="text" name="TxtLot[]" id="TxtLot_<? echo $i; ?>" class="text_boxes" style="width:40px;" value="" /></td>
				<td id="tdbrand_<? echo $i; ?>"><input onchange="copy_value(this.value, 'TxtBrand_',<?= $i ?>);" type="text" name="TxtBrand[]" id="TxtBrand_<? echo $i; ?>" class="text_boxes" style="width:60px;" value="" /></td>
				<td id="uom_<? echo $i; ?>" title="<? echo $row["UOM"]; ?>"><? echo $unit_of_measurement[$row["UOM"]]; ?></td>
				<td id="tdreceiveqnty_<? echo $i; ?>" title=""><input type="text" name="receiveqnty[]" id="receiveqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px;" value="<? echo number_format($qnty, 3, '.', ''); ?>" title="<?= $qnty; ?>" onBlur="calculate(<? echo $i; ?>);" /></td>
				<td id="item_color_<? echo $i; ?>" title=""><input type="text" name="greyqnty[]" id="greyqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:45px;" value="" onBlur="calculate(<? echo $i; ?>);" /></td>

				<td id="rate_<? echo $i; ?>" title="<? echo $row['NET_PI_RATE']; ?>" align="right"><? echo number_format($row['NET_PI_RATE'], 3, '.', ''); ?></td>
				<td id="greyRate_<? echo $i; ?>" title="<? echo $grey_rate_tk; ?>" align="right"><? echo number_format($grey_rate_tk, 2, '.', ''); ?></td>
				<td id="DCharge_<? echo $i; ?>" title="<?= $row['DYEING_CHARGE'] * $ydwo_ecchange_rate; ?>"><? echo number_format($row['DYEING_CHARGE'] * $ydwo_ecchange_rate, 3, '.', ''); ?></td>
				<td id="ilePersent_<? echo $i; ?>" title="<? echo $ile_percentage; ?>" align="right"><? echo number_format($ile_percentage, 2, '.', ''); ?></td>
				<td id="amount_<? echo $i; ?>" title="<? echo $amount; ?>" align="right"><? echo number_format($amount, 2, '.', ''); ?></td>
				<td id="avgRate_<? echo $i; ?>" title="<? echo $domestic_rate; ?>" align="right"><? echo number_format($domestic_rate, 2, '.', ''); ?></td>
				<td id="bookCurrency_<? echo $i; ?>" title="<? echo $book_currency; ?>" align="right"><? echo number_format($book_currency, 2, '.', ''); ?></td>
				<td id="woPiBalQnty_<? echo $i; ?>" title="<? echo $qnty; ?>" align="right"><? echo number_format($qnty, 2, '.', ''); ?></td>
				<td id="overRcvQnty_<? echo $i; ?>" title="<? echo $allow_total_qty; ?>" align="right"><? echo number_format($allow_total_qty, 2, '.', ''); ?></td>

				<td id="tdNoOfBag_<? echo $i; ?>"><input type="text" name="noOfBag[]" id="noOfBag_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;" value="" onChange="calculate_wt_per_cone(<? echo $i; ?> ); calculate_loose_bag(<? echo $i; ?> )" /></td>
				<td id="tdWetPerBag_<? echo $i; ?>"><input type="text" name="wetPerBag[]" id="wetPerBag_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;" value="" onChange="calculate_wt_per_cone(<? echo $i; ?> ); calculate_loose_bag(<? echo $i; ?> )" /></td>
				<td id="tdNoOfLooseBag_<? echo $i; ?>"><input type="text" name="noOfLooseBag[]" id="noOfLooseBag_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;" value="" onChange="calculate_wt_per_cone(<? echo $i; ?> ); calculate_loose_bag(<? echo $i; ?> )" /></td>
				<td id="tdLooseBagWt_<? echo $i; ?>">
					<input type="text" name="looseBagWt[]" id="looseBagWt_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;" value=""  ondblclick="openmypage_lose_wt_popup(<? echo $i; ?>);" readonly />
					<input type="hidden" name="looseBagWt_brkdwn[]" id="looseBagWt_brkdwn_<? echo $i; ?>" value="" readonly>
				</td>

				<td id="tdConPerBag_<? echo $i; ?>"><input type="text" name="conPerBag[]" id="conPerBag_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;" value="" onChange="calculate_wt_per_cone(<? echo $i; ?> )" /></td>

				<td id="tdLoseCone_<? echo $i; ?>"><input type="text" name="loseCone[]" id="loseCone_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;" value="" onChange="calculate_wt_per_cone(<? echo $i; ?> )" /></td>

				<td id="tdWetPerCon_<? echo $i; ?>"><input type="text" name="wetPerCon[]" id="wetPerCon_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;" value="" onclick="calculate_wt_per_cone(<? echo $i; ?> )" readonly /></td>

				<td id="productCode_<? echo $i; ?>"></td>
				<td id="remarks_<? echo $i; ?>">
					<input type="hidden" name="updatedtlsid[]" id="updatedtlsid_<? echo $i; ?>" value="" readonly>
					<input type="hidden" name="piWoDtlsId[]" id="piWoDtlsId_<? echo $i; ?>" value="<? echo $row["ID"]; ?>" readonly>
				</td>
			</tr>
		<?
			$i++;
		}
	}
	exit();
}

if ($action == 'show_fabric_desc_listview_update') {
	$data = explode("**", $data);
	//print_r($data);
	$bookingNo_piId = $data[0];
	$receive_basis = $data[1];
	$booking_without_order = $data[2];
	$company = $data[3];
	$source = $data[4];
	$exchange_rate = $data[5];
	$mst_id = $data[6];
	$receive_purpose = $data[9];
	$cr_date = date("d-m-Y");
	//echo $cr_date."=".$company;
	$exchange_rate = set_conversion_rate(2, $cr_date, $company);


	$sql = "select standard from variable_inv_ile_standard where source='$source' and company_name=$company and category=1 and status_active=1 and is_deleted=0";
	//echo $sql;
	$result = sql_select($sql, 1);
	foreach ($result as $row) {
		$ile = $row[csf("standard")];
		$ile_percentage = ($row[csf("standard")] / 100) * $rate;
	}
	if ($ile_percentage == "") $ile_percentage = 0;

	$variable_set_invent = sql_select("select category,over_rcv_status,over_rcv_percent,over_rcv_payment from variable_inv_ile_standard where company_name=$company and variable_list=23 and category = 1 order by id");
	$over_receive_limit = !empty($variable_set_invent) ? $variable_set_invent[0][csf('over_rcv_percent')] : 0;

	$prev_return_entry = "select b.WO_PI_DTLS_ID, sum(b.PARKING_QUANTITY) as PARKING_QUANTITY   
	from INV_ISSUE_MASTER a, quarantine_parking_dtls b
	where a.id=b.mst_id and a.entry_form=531 and b.entry_form=531 and b.ITEM_CATEGORY_ID=1 and a.company_id=$company and a.ISSUE_BASIS=$receive_basis and b.WO_PI_ID='$bookingNo_piId' and a.status_active=1 and b.status_active=1
	group by b.WO_PI_DTLS_ID";

	$prev_return_entry_result = sql_select($prev_return_entry);
	$prev_return = array();
	foreach ($prev_return_entry_result as $row) {
		$prev_return[$row["WO_PI_DTLS_ID"]] = $row["PARKING_QUANTITY"];
	}

	$prev_entry = "select b.WO_PI_DTLS_ID, sum(b.PARKING_QUANTITY) as PARKING_QUANTITY   
	from inv_receive_master a, quarantine_parking_dtls b
	where a.id=b.mst_id and a.entry_form=529 and b.entry_form=529 and b.ITEM_CATEGORY_ID=1 and a.company_id=$company and a.receive_basis=$receive_basis and b.WO_PI_ID='$bookingNo_piId' and a.id<>$mst_id and a.status_active=1 and b.status_active=1
	group by b.WO_PI_DTLS_ID";

	$prev_entry_result = sql_select($prev_entry);
	$prev_data = array();
	foreach ($prev_entry_result as $row) {
		$prev_data[$row["WO_PI_DTLS_ID"]] = $row["PARKING_QUANTITY"] - $prev_return[$row["WO_PI_DTLS_ID"]];
	}


	if ($receive_basis == 1) {
		$sql = "select a.ID, a.PI_ID as MST_ID, a.QUANTITY, a.NET_PI_RATE, a.NET_PI_AMOUNT
		from com_pi_item_details a
		where a.pi_id='$bookingNo_piId' and a.status_active=1 and a.is_deleted=0";
		//echo $sql;
		$sql_result = sql_select($sql);
		$booking_pi_data = array();
		foreach ($sql_result as $row) {
			$booking_pi_data[$row["ID"]]["book_qnty"] = $row["QUANTITY"];
			$booking_pi_data[$row["ID"]]["rate"] = $row["NET_PI_RATE"];
			$booking_pi_data[$row["ID"]]["book_amount"] = $row["NET_PI_AMOUNT"];
		}
		// group by item_group, item_description, brand_supplier, color_id, item_color, size_id, item_size
	} else if ($receive_basis == 2) {
		$wo_ecchange_rate = return_field_value("ecchange_rate", "wo_yarn_dyeing_mst", "id=$bookingNo_piId", "ecchange_rate");
		if ($wo_ecchange_rate == 1) $wo_ecchange_rate = $exchange_rate;
		else $wo_ecchange_rate = 1;
		if ($receive_purpose == 15 || $receive_purpose == 50 || $receive_purpose == 51) {
			$sql = "select b.ID, b.MST_ID, b.YARN_WO_QTY as QUANTITY, ((c.AVG_RATE_PER_UNIT/$exchange_rate)+(b.DYEING_CHARGE/$wo_ecchange_rate)) as NET_PI_RATE, ((c.AVG_RATE_PER_UNIT/$exchange_rate)+(b.DYEING_CHARGE/$wo_ecchange_rate))*b.YARN_WO_QTY as NET_PI_AMOUNT 
			from wo_yarn_dyeing_dtls_fin_prod a, wo_yarn_dyeing_dtls b, PRODUCT_DETAILS_MASTER c
			where a.MST_ID=b.MST_ID and b.PRODUCT_ID=c.id and b.MST_ID='$bookingNo_piId' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
			//echo $sql;die;
			$sql_result = sql_select($sql);
			$booking_pi_data = array();
			foreach ($sql_result as $row) {
				$booking_pi_data[$row["ID"]]["book_qnty"] = $row["QUANTITY"];
				$booking_pi_data[$row["ID"]]["rate"] = $row["NET_PI_RATE"];
				$booking_pi_data[$row["ID"]]["book_amount"] = $row["NET_PI_AMOUNT"];
			}
		} elseif ($receive_purpose == 2 || $receive_purpose == 7 || $receive_purpose == 12 || $receive_purpose == 38 || $receive_purpose == 46) {
			$item_issue_prod = " select c.id as PROD_ID, c.YARN_COUNT_ID, c.YARN_COMP_TYPE1ST, c.YARN_TYPE, c.AVG_RATE_PER_UNIT from INV_ISSUE_MASTER a, INV_TRANSACTION b, PRODUCT_DETAILS_MASTER c where a.id=b.mst_id and b.prod_id=c.id and b.item_category=1 and b.transaction_type=2 and a.status_active=1 and b.status_active=1 and a.BOOKING_ID=$bookingNo_piId and a.ISSUE_BASIS=1 and a.ISSUE_PURPOSE=2";
			$item_issue_prod_result = sql_select($item_issue_prod);
			$yarn_service_prod_rate = array();
			foreach ($item_issue_prod_result as $val) {
				$yarn_service_prod_rate[$val["YARN_COUNT_ID"]][$val["YARN_COMP_TYPE1ST"]][$val["YARN_TYPE"]] = $val["AVG_RATE_PER_UNIT"];
			}
			$sql = "select a.ID, a.MST_ID, b.YARN_COUNT_ID as COUNT_NAME, b.YARN_COMP_TYPE1ST as YARN_COMPOSITION_ITEM1, b.YARN_COMP_PERCENT1ST as YARN_COMPOSITION_PERCENTAGE1, b.YARN_TYPE, a.YARN_WO_QTY as QUANTITY, ((b.AVG_RATE_PER_UNIT/$exchange_rate)+(a.DYEING_CHARGE/$wo_ecchange_rate)) as NET_PI_RATE, ((b.AVG_RATE_PER_UNIT/$exchange_rate)+(a.DYEING_CHARGE/$wo_ecchange_rate))*a.YARN_WO_QTY as NET_PI_AMOUNT, a.DYEING_CHARGE, a.ENTRY_FORM 
			from WO_YARN_DYEING_DTLS a, PRODUCT_DETAILS_MASTER b
			where a.PRODUCT_ID=b.id and a.MST_ID='$bookingNo_piId' and a.status_active=1 and a.is_deleted=0 and a.entry_form in(41,42,94,135)
			union all
			select a.ID, a.MST_ID, a.COUNT as COUNT_NAME, a.YARN_COMP_TYPE1ST as YARN_COMPOSITION_ITEM1, a.YARN_COMP_PERCENT1ST as YARN_COMPOSITION_PERCENTAGE1, a.YARN_TYPE, a.YARN_WO_QTY as QUANTITY, 0 as NET_PI_RATE, 0 as NET_PI_AMOUNT, a.DYEING_CHARGE, a.ENTRY_FORM
			from WO_YARN_DYEING_DTLS a
			where a.MST_ID='$bookingNo_piId' and a.status_active=1 and a.is_deleted=0 and a.entry_form in(125,114,340)";
			//echo $sql;die;
			$sql_result = sql_select($sql);
			$booking_pi_data = array();
			foreach ($sql_result as $row) {
				if ($receive_basis == 2 && ($receive_purpose == 2 || $receive_purpose == 7 || $receive_purpose == 12 || $receive_purpose == 38 || $receive_purpose == 46) && ($row['ENTRY_FORM'] == 125 || $row['ENTRY_FORM'] == 114 || $row['ENTRY_FORM'] == 340)) {
					$row["NET_PI_RATE"] = ($yarn_service_prod_rate[$row["COUNT_NAME"]][$row["YARN_COMPOSITION_ITEM1"]][$row["YARN_TYPE"]] / $exchange_rate) + ($row['DYEING_CHARGE'] / $wo_ecchange_rate);
				}
				$booking_pi_data[$row["ID"]]["book_qnty"] = $row["QUANTITY"];
				$booking_pi_data[$row["ID"]]["rate"] = $row["NET_PI_RATE"];
				$booking_pi_data[$row["ID"]]["book_amount"] = $row["QUANTITY"] * $row["NET_PI_RATE"];
			}
		} else {
			$sql = "select a.ID, a.MST_ID, a.SUPPLIER_ORDER_QUANTITY as QUANTITY, a.RATE as NET_PI_RATE, a.AMOUNT as NET_PI_AMOUNT 
			from WO_NON_ORDER_INFO_DTLS a
			where a.MST_ID='$bookingNo_piId' and a.status_active=1 and a.is_deleted=0";
			//echo $sql;die;
			$sql_result = sql_select($sql);
			$booking_pi_data = array();
			foreach ($sql_result as $row) {
				$booking_pi_data[$row["ID"]]["book_qnty"] = $row["QUANTITY"];
				$booking_pi_data[$row["ID"]]["rate"] = $row["NET_PI_RATE"];
				$booking_pi_data[$row["ID"]]["book_amount"] = $row["NET_PI_AMOUNT"];
			}
		}

		//$sql = "select a.ID, a.MST_ID, a.SUPPLIER_ORDER_QUANTITY as QUANTITY, a.RATE as NET_PI_RATE, a.AMOUNT as NET_PI_AMOUNT 
		//		from WO_NON_ORDER_INFO_DTLS a
		//		where a.MST_ID='$bookingNo_piId' and a.status_active=1 and a.is_deleted=0";
		//		//echo $sql;die;
		//		$sql_result=sql_select($sql);
		//		$booking_pi_data=array();
		//		foreach($sql_result as $row)
		//		{
		//			$booking_pi_data[$row["ID"]]["book_qnty"] =$row["QUANTITY"];
		//			$booking_pi_data[$row["ID"]]["rate"] =$row["NET_PI_RATE"];
		//			$booking_pi_data[$row["ID"]]["book_amount"] =$row["NET_PI_AMOUNT"];
		//		}
	}

	if ($receive_basis == 4) {
		$sql_receive = "select b.ID, b.MST_ID, b.WO_PI_ID, b.WO_PI_DTLS_ID, b.WO_PI_NO, b.PO_BREAK_DOWN_ID, b.ITEM_ID, b.YARN_COUNT, b.YARN_COMP_TYPE1ST, b.YARN_COMP_PERCENT1ST, b.YARN_TYPE, b.COLOR_NAME, b.LOT, b.BRAND_NAME, b.PRODUCT_CODE, b.UOM, b.WO_PI_QUANTITY, b.PARKING_QUANTITY, b.GREY_QUANTITY, b.RATE, b.DYEING_CHARGE, b.AVG_RATE, b.AMOUNT, b.CONS_AMOUNT, b.NO_OF_BAG, b.NO_OF_LOOSE_BAG, b.loose_bag_wt_brkdwn, b.CONE_PER_BAG, b.LOSE_CONE, b.WEIGHT_PER_BAG, b.WEIGHT_CONE, b.ILE_PERCENT, b.IS_QC_PASS  from QUARANTINE_PARKING_DTLS b where b.entry_form=529 and b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0";

		$sql_receive_brkdwn = "select b.ID, b.MST_ID, c.id as brkdwn_id, c.rfid_no, c.LOOSE_BAG_WEIGHT from QUARANTINE_PARKING_DTLS b LEFT JOIN QUARANTINE_PAR_DTLS_LOOSE_RFID c on b.id=c.dtls_id and b.mst_id=c.mst_id where b.entry_form=529 and b.mst_id=$mst_id and b.status_active=1 and b.is_deleted=0";
	} else {
		$sql_receive = "select b.ID, b.MST_ID, b.WO_PI_ID, b.WO_PI_DTLS_ID, b.WO_PI_NO, b.PO_BREAK_DOWN_ID, b.ITEM_ID, b.YARN_COUNT, b.YARN_COMP_TYPE1ST, b.YARN_COMP_PERCENT1ST, b.YARN_TYPE, b.COLOR_NAME, b.LOT, b.BRAND_NAME, b.PRODUCT_CODE, b.UOM, b.WO_PI_QUANTITY, b.PARKING_QUANTITY, b.GREY_QUANTITY, b.RATE, b.DYEING_CHARGE, b.AVG_RATE, b.AMOUNT, b.CONS_AMOUNT, b.NO_OF_BAG, b.NO_OF_LOOSE_BAG, b.loose_bag_wt_brkdwn, b.CONE_PER_BAG, b.LOSE_CONE, b.WEIGHT_PER_BAG, b.WEIGHT_CONE, b.ILE_PERCENT, b.IS_QC_PASS from QUARANTINE_PARKING_DTLS b where b.entry_form=529 and b.mst_id=$mst_id and b.WO_PI_ID=$bookingNo_piId and b.status_active=1 and b.is_deleted=0";
		
		$sql_receive_brkdwn = "select b.ID, b.MST_ID, c.id as brkdwn_id, c.rfid_no, c.LOOSE_BAG_WEIGHT from QUARANTINE_PARKING_DTLS b LEFT JOIN QUARANTINE_PAR_DTLS_LOOSE_RFID c on b.id=c.dtls_id and b.mst_id=c.mst_id where b.entry_form=529 and b.mst_id=$mst_id and b.WO_PI_ID=$bookingNo_piId and b.status_active=1 and b.is_deleted=0";
	}

	// echo $sql_receive;//die;
	$data_brkdwn_array = sql_select($sql_receive_brkdwn);
	$data_array = sql_select($sql_receive);
	$breakdown_pattern_arr = array();
	$loose_bag_total = array();
	foreach($data_brkdwn_array as $row)
	{
		if($breakdown_pattern_arr[$row['ID']]['BREAKDOWN'] != "")
		{
			$breakdown_pattern_arr[$row['ID']]['BREAKDOWN'] .= "__";
		}
		$breakdown_pattern_arr[$row['ID']]['BREAKDOWN'] .= $row['RFID_NO']."-".$row['LOOSE_BAG_WEIGHT']."-".$row['BRKDWN_ID'];
		$loose_bag_total[$row['ID']]['TOTAL'] += $row['LOOSE_BAG_WEIGHT'];
	}

	// echo "<pre>";
	// print_r($breakdown_pattern_arr); die;
	$yarn_count_library = return_library_array("select id,yarn_count from lib_yarn_count where status_active=1 and is_deleted=0", 'id', 'yarn_count');
	$color_library = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$i = 1;
	foreach ($data_array as $row) {
		$prev_rcv_qnty = $prev_data[$row["WO_PI_DTLS_ID"]];
		$qnty = ($booking_pi_data[$row["WO_PI_DTLS_ID"]]["book_qnty"] - $prev_rcv_qnty);
		$ile_cost = 0;
		if ($ile_percentage > 0) $ile_cost = $ile_percentage * $booking_pi_data[$row["WO_PI_DTLS_ID"]]["rate"]; //ile cost = (ile/100)*rate
		$conversion_factor = 1; // yarn always KG
		$domestic_rate = return_domestic_rate($booking_pi_data[$row["WO_PI_DTLS_ID"]]["rate"], $ile_cost, $exchange_rate, $conversion_factor);
		$book_currency = $qnty * $domestic_rate;
		$amount = $qnty * $booking_pi_data[$row["WO_PI_DTLS_ID"]]["rate"];
		$over_receive_limit_qnty = ($over_receive_limit > 0) ? ($over_receive_limit / 100) * $qnty : 0;
		$allow_total_qty = number_format(($qnty + $over_receive_limit_qnty), 2, '.', '');
		if ($row["IS_QC_PASS"] == 1) $disable_qnty = "disabled readonly";
		else $disable_qnty = "";
		//echo $booking_pi_data[$row["WO_PI_DTLS_ID"]]["book_qnty"]."=".$prev_rcv_qnty;
		$recv_qty = "";
		$no_of_bag = "";
		$weight_per_bag = "";
		$loose_bag_weight = '';
		$loose_bag_wt_brkdwn = $row['LOOSE_BAG_WT_BRKDWN'];
		$cone_per_bag = "";
		$weight_cone = "";
		$loose_cone = "";
		if ($qnty > 0 || $receive_basis == 4) {
			if ($i % 2 == 0) $bgcolor = "#E9F3FF";
			else $bgcolor = "#FFFFFF";
			$recv_qty = $row["PARKING_QUANTITY"];
			$no_of_bag = $row["NO_OF_BAG"];
			$weight_per_bag = $row["WEIGHT_PER_BAG"];
			$cone_per_bag = $row["CONE_PER_BAG"];
			$loose_cone = $row["LOSE_CONE"];
			if($loose_bag_total[$row['ID']]['TOTAL'] == '' || $loose_bag_total[$row['ID']]['TOTAL'] == 0)
			{
				$loose_bag_weight = $recv_qty - ($no_of_bag * $weight_per_bag);
			}
			else
			{
				$loose_bag_weight = $loose_bag_total[$row['ID']]['TOTAL'];
			}
			$weight_cone = $recv_qty / (($no_of_bag * $cone_per_bag) + $loose_cone);
			if ($weight_cone > $recv_qty) {
				$weight_cone = "";
			}
		?>
			<tr bgcolor="<? echo $bgcolor; ?>" id="row_<? echo $i; ?>" align="center">
				<td id="count_<? echo $i; ?>" title="<? echo $row["YARN_COUNT"]; ?>"><? echo $yarn_count_library[$row["YARN_COUNT"]]; ?></td>
				<td id="composition_<? echo $i; ?>" title="<? echo $row["YARN_COMP_TYPE1ST"]; ?>"><? echo $composition[$row["YARN_COMP_TYPE1ST"]]; ?></td>
				<td id="comPersent_<? echo $i; ?>" title="<? echo $row["YARN_COMP_PERCENT1ST"]; ?>"><? echo $row["YARN_COMP_PERCENT1ST"]; ?></td>
				<td id="yarnType_<? echo $i; ?>" title="<? echo $row["YARN_TYPE"]; ?>"><? echo $yarn_type[$row["YARN_TYPE"]]; ?></td>
				<td id="color_<? echo $i; ?>" title="<? echo $row['COLOR_NAME']; ?>"><? echo $color_library[$row["COLOR_NAME"]]; ?></td>
				<td id="tdlot_<? echo $i; ?>" title=""><input type="text" name="TxtLot[]" id="TxtLot_<? echo $i; ?>" class="text_boxes" style="width:40px;" value="<?= $row["LOT"]; ?>" <?= $disable_qnty; ?> /></td>
				<td id="tdbrand_<? echo $i; ?>"><input onchange="copy_value(this.value, 'TxtBrand_',<?= $i ?>);" type="text" name="TxtBrand[]" id="TxtBrand_<? echo $i; ?>" class="text_boxes" style="width:60px;" value="<?= $row["BRAND_NAME"]; ?>" <?= $disable_qnty; ?> /></td>
				<td id="uom_<? echo $i; ?>" title="<? echo $row["UOM"]; ?>"><? echo $unit_of_measurement[$row["UOM"]]; ?></td>
				<td id="tdreceiveqnty_<? echo $i; ?>" title=""><input type="text" name="receiveqnty[]" id="receiveqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:50px;" value="<? echo number_format($row["PARKING_QUANTITY"], 3, '.', ''); ?>" title="<?= $qnty; ?>" onBlur="calculate(<? echo $i; ?>);" <?= $disable_qnty; ?> /></td>
				<td id="item_color_<? echo $i; ?>" title=""><input type="text" name="greyqnty[]" id="greyqnty_<? echo $i; ?>" class="text_boxes_numeric" style="width:45px;" value="<? echo number_format($row["GREY_QUANTITY"], 2, '.', ''); ?>" onBlur="calculate(<? echo $i; ?>);" <?= $disable_qnty; ?> /></td>

				<td id="rate_<? echo $i; ?>" title="<? echo $row['RATE']; ?>" align="right"><? echo number_format($row['RATE'], 3, '.', ''); ?></td>
				<td id="greyRate_<? echo $i; ?>" title="<? if ($row['DYEING_CHARGE'] > 0) echo $row['AVG_RATE'] - $row['DYEING_CHARGE'];
														else echo "0.00"; ?>" align="right"><? if ($row['DYEING_CHARGE'] > 0) echo number_format($row['AVG_RATE'] - $row['DYEING_CHARGE'], 2, '.', '');
																							else echo "0.00"; ?></td>
				<td id="DCharge_<? echo $i; ?>" title="<? echo $row['DYEING_CHARGE']; ?>" align="right"><? echo number_format($row['DYEING_CHARGE'], 2, '.', ''); ?></td>
				<td id="ilePersent_<? echo $i; ?>" title="<? echo $row['ILE_PERCENT']; ?>" align="right"><? echo number_format($row['ILE_PERCENT'], 2, '.', ''); ?></td>
				<td id="amount_<? echo $i; ?>" title="<? echo $row['AMOUNT']; ?>" align="right"><? echo number_format($row['AMOUNT'], 2, '.', ''); ?></td>
				<td id="avgRate_<? echo $i; ?>" title="<? echo $row['AVG_RATE']; ?>" align="right"><? echo number_format($row['AVG_RATE'], 2, '.', ''); ?></td>
				<td id="bookCurrency_<? echo $i; ?>" title="<? echo $row['CONS_AMOUNT']; ?>" align="right"><? echo number_format($row['CONS_AMOUNT'], 2, '.', ''); ?></td>
				<td id="woPiBalQnty_<? echo $i; ?>" title="<? echo $qnty; ?>" align="right"><? echo number_format($qnty, 2, '.', ''); ?></td>
				<td id="overRcvQnty_<? echo $i; ?>" title="<? echo $allow_total_qty; ?>" align="right"><? echo number_format($allow_total_qty, 2, '.', ''); ?></td>
				<td id="tdNoOfBag_<? echo $i; ?>"><input type="text" name="noOfBag[]" id="noOfBag_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;" value="<?= $row["NO_OF_BAG"]; ?>" onChange="calculate_wt_per_cone(<? echo $i; ?> ); calculate_loose_bag(<? echo $i; ?> )" /></td>
				
				<td id="tdWetPerBag_<? echo $i; ?>"><input type="text" name="wetPerBag[]" id="wetPerBag_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;" value="<?= $row["WEIGHT_PER_BAG"]; ?>" onChange="calculate_wt_per_cone(<? echo $i; ?> ); calculate_loose_bag(<? echo $i; ?> )" /></td>
				<td id="tdNoOfLooseBag_<? echo $i; ?>"><input type="text" name="noOfLooseBag[]" id="noOfLooseBag_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;" value="<?= $row["NO_OF_LOOSE_BAG"]; ?>" onChange="calculate_wt_per_cone(<? echo $i; ?> ); calculate_loose_bag(<? echo $i; ?> )" /></td>
				<td id="tdLooseBagWt_<? echo $i; ?>">
					<input type="text" name="looseBagWt[]" id="looseBagWt_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;" value="<?= $loose_bag_weight; ?>" ondblclick="openmypage_lose_wt_popup(<? echo $i; ?>);" readonly />
					<input type="hidden" name="looseBagWt_brkdwn[]" id="looseBagWt_brkdwn_<? echo $i; ?>" value="<? echo $breakdown_pattern_arr[$row['ID']]['BREAKDOWN']; ?>" readonly>
				</td>
				<td id="tdConPerBag_<? echo $i; ?>"><input type="text" name="conPerBag[]" id="conPerBag_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;" value="<?= $row["CONE_PER_BAG"]; ?>" onChange="calculate_wt_per_cone(<? echo $i; ?> )" /></td>
				<td id="tdLoseCone_<? echo $i; ?>"><input type="text" name="loseCone[]" id="loseCone_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;" value="<?= $row["LOSE_CONE"]; ?>" onChange="calculate_wt_per_cone(<? echo $i; ?> )" /></td>

				<td id="tdWetPerCon_<? echo $i; ?>"><input type="text" name="wetPerCon[]" id="wetPerCon_<? echo $i; ?>" class="text_boxes_numeric" style="width:40px;" value="<?= $weight_cone; ?>" onclick="calculate_wt_per_cone(<? echo $i; ?> )" readonly /></td>
				<td id="productCode_<? echo $i; ?>"></td>
				<td id="remarks_<? echo $i; ?>">
					<input type="hidden" name="updatedtlsid[]" id="updatedtlsid_<? echo $i; ?>" value="<?= $row["ID"]; ?>" readonly>
					<input type="hidden" name="piWoDtlsId[]" id="piWoDtlsId_<? echo $i; ?>" value="<? echo $row["WO_PI_DTLS_ID"]; ?>" readonly>
				</td>
			</tr>
	<?
			$i++;
		}
	}
	exit();
}



if ($action == "duplication_check") {
	$data = explode("**", $data);
	$update_id = $data[0];
	$dtls_id = $data[1];

	if ($dtls_id == "") $dtls_id_cond = "";
	else $dtls_id_cond = " and b.id!=$dtls_id";

	//$sql="select a.supplier_id, a.currency_id, a.source, a.lc_no from inv_receive_master a, inv_trims_entry_dtls b where a.id=b.mst_id and a.id=$update_id and a.item_category=4 and a.entry_form=24 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $dtls_id_cond";

	$sql = "select a.supplier_id, a.currency_id, a.source, a.lc_no from inv_receive_master a where a.id=$update_id and a.item_category=4 and a.entry_form=24 and a.status_active=1 and a.is_deleted=0";
	$dataArray = sql_select($sql);
	$data = $dataArray[0][csf('supplier_id')] . "**" . $dataArray[0][csf('currency_id')] . "**" . $dataArray[0][csf('source')] . "**" . $dataArray[0][csf('lc_no')];
	echo $data;
	exit();
}

if ($action == "save_update_delete") {
	//$process = array( &$_POST );
	$process = $_POST;
	extract(check_magic_quote_gpc($process));

	$sql = "select standard from variable_inv_ile_standard where source=$cbo_source and company_name=$cbo_company_id and category=1 and status_active=1 and is_deleted=0";
	//echo $sql;
	$color_id_arr = return_library_array("select COLOR_NAME, ID from LIB_COLOR", 'COLOR_NAME', 'ID');
	$result = sql_select($sql, 1);
	foreach ($result as $row) {
		$ile = $row[csf("standard")];
		$ile_percentage = ($row[csf("standard")] / 100) * $rate;
	}
	if ($ile_percentage == "") $ile_percentage = 0;

	$variable_set_invent = sql_select("select category,over_rcv_status,over_rcv_percent,over_rcv_payment from variable_inv_ile_standard where company_name=$cbo_company_id and variable_list=23 and category = 1 order by id");
	$over_receive_limit = !empty($variable_set_invent) ? $variable_set_invent[0][csf('over_rcv_percent')] : 0;



	if ($operation == 0)  // Insert Here
	{
		// approval check 
		$app_sql = "select b.APPROVAL_NEED, a.SETUP_DATE from APPROVAL_SETUP_MST a, APPROVAL_SETUP_DTLS b where a.id = b.mst_id and a.COMPANY_ID = $cbo_company_id and b.page_id = 15 and b.is_deleted = 0 and a.is_deleted = 0 and a.status_active = 1 and b.status_active = 1 order by a.SETUP_DATE desc";
		$app_result = sql_select($app_sql);
		$is_appoval_need = $app_result[0]["APPROVAL_NEED"];


		if ($is_appoval_need == '1') //approval needed
		{
			$is_approved_sql = "select a.CURRENT_APPROVAL_STATUS from APPROVAL_HISTORY a, WO_NON_ORDER_INFO_MST b where b.id = a.MST_ID and b.status_active = 1 and b.IS_DELETED = 0  and b.id = $txt_wo_pi_id";
			$is_approved_arr = sql_select($is_approved_sql);

			$is_approved =  $is_approved_arr[0]["CURRENT_APPROVAL_STATUS"];
			if ($is_approved != '1') // not approved
			{
				// echo "WO/PI is not approved!";
				echo "app_check";
				die;
			} else //approved
			{
				$con = connect();
				if ($db_type == 0) {
					mysql_query("BEGIN");
				}

				$trims_recv_num = '';
				$master_id = '';
				if (str_replace("'", "", $update_id) == "") {
					if ($db_type == 0) $year_cond = "YEAR(insert_date)";
					else if ($db_type == 2) $year_cond = "to_char(insert_date,'YYYY')";
					else $year_cond = ""; //defined Later

					$id = return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con);
					$new_trims_recv_system_id = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con, 1, $cbo_company_id, 'YGRN', 529, date("Y", time())));
					$field_array = "id, recv_number_prefix, recv_number_prefix_num, recv_number, entry_form, item_category, receive_basis, receive_purpose, company_id, receive_date, challan_no, booking_id, booking_no, store_id, loan_party, source, supplier_id, currency_id, exchange_rate, remarks, gate_entry_no, gate_entry_date, inserted_by, insert_date";
					$data_array = "(" . $id . ",'" . $new_trims_recv_system_id[1] . "'," . $new_trims_recv_system_id[2] . ",'" . $new_trims_recv_system_id[0] . "',529,1," . $cbo_receive_basis . "," . $cbo_receive_purpose . "," . $cbo_company_id . "," . $txt_receive_date . "," . $txt_receive_chal_no . "," . $txt_wo_pi_id . "," . $txt_booking_pi_no . "," . $cbo_store_name . "," . $cbo_party . "," . $cbo_source . "," . $cbo_supplier . "," . $cbo_currency_id . "," . $txt_exchange_rate . "," . $txt_mst_remarks . "," . $txt_gate_entry_no . "," . $txt_gate_entry_date . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

					$trims_recv_num = $new_trims_recv_system_id[0];
					$master_id = $id;
				} else {
					$original_receive_basis = sql_select("select receive_basis, supplier_id, source from inv_receive_master where id=$update_id");
					if (str_replace("'", "", $cbo_receive_basis) != $original_receive_basis[0][csf('receive_basis')]) {
						echo "40**Multiple Receive Basis Not Allow In Same Received ID";
						disconnect($con);
						die;
					}
					if (str_replace("'", "", $cbo_source) != $original_receive_basis[0][csf('source')]) {
						echo "40**Multiple Source Not Allow In Same Received ID";
						disconnect($con);
						die;
					}

					if (str_replace("'", "", $cbo_supplier) != $original_receive_basis[0][csf('supplier_id')]) {
						echo "40**Multiple Supplier Not Allow In Same Received ID";
						disconnect($con);
						die;
					}

					$field_array_update = "receive_basis*receive_purpose*receive_date*challan_no*booking_id*booking_no*store_id*loan_party*source*supplier_id*currency_id*exchange_rate*remarks*updated_by*update_date";

					$data_array_update = $cbo_receive_basis . "*" . $cbo_receive_purpose . "*" . $txt_receive_date . "*" . $txt_receive_chal_no . "*" . $txt_wo_pi_id . "*" . $txt_booking_pi_no . "*" . $cbo_store_name . "*" . $cbo_party . "*" . $cbo_source . "*" . $cbo_supplier . "*" . $cbo_currency_id . "*" . $txt_exchange_rate . "*" . $txt_mst_remarks . "*" . $txt_gate_entry_no . "*" . $txt_gate_entry_date . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

					$trims_recv_num = str_replace("'", "", $txt_recieved_id);
					$master_id = str_replace("'", "", $update_id);
				}

				$field_array_dtls = "id, mst_id, wo_pi_id, wo_pi_dtls_id, wo_pi_no, yarn_count, yarn_comp_type1st, yarn_comp_percent1st, yarn_type, color_name, lot, brand_name, product_code, uom, parking_quantity, grey_quantity, grey_rate, rate, dyeing_charge, avg_rate, amount, cons_amount, no_of_bag,no_of_loose_bag, cone_per_bag, lose_cone, weight_per_bag, weight_cone, inserted_by, insert_date, status_active, is_deleted, item_category_id, entry_form";
				$data_array_dtls_brkdwn = "";
				$k=0;
				for ($i = 1; $i <= $tot_row; $i++) {
					$count = "count" . $i;
					$composition = "composition" . $i;
					$comPersent = "comPersent" . $i;
					$yarnType = "yarnType" . $i;
					$color = "color" . $i;
					$TxtLot = "TxtLot" . $i;
					$TxtBrand = "TxtBrand" . $i;
					$receiveqnty = "receiveqnty" . $i;
					$greyqnty = "greyqnty" . $i;
					$greyRate = "greyRate" . $i;

					$uom = "uom" . $i;
					$rate = "rate" . $i;

					$avgRate = "avgRate" . $i;
					$DCharge = "DCharge" . $i;
					$ilePersent = "ilePersent" . $i;
					$amount = "amount" . $i;
					$bookCurrency = "bookCurrency" . $i;

					$woPiBalQnty = "woPiBalQnty" . $i;
					$overRcvQnty = "overRcvQnty" . $i;
					$noOfBag = "noOfBag" . $i;
					$noOfLooseBag = "noOfLooseBag" . $i;
					$looseBagWtBrkdwn = "looseBagWt_brkdwn" . $i;
					$conPerBag = "conPerBag" . $i;
					$loseCone = "loseCone" . $i;
					$wetPerBag = "wetPerBag" . $i;
					$wetPerCon = "wetPerCon" . $i;
					$productCode = "productCode" . $i;

					$piWoDtlsId = "piWoDtlsId" . $i;
					$updatedtlsid = "updatedtlsid" . $i;
					$cbo_receive_basis = str_replace("'", "", $cbo_receive_basis);
					if ($cbo_receive_basis != 4) {
						if ($$receiveqnty > $$woPiBalQnty) {
							echo "20**Receive Quantity Not Allow Over Balance Quantity";
							disconnect($con);
							die;
						}
					}



					$id_dtls = return_next_id_by_sequence("QUARANTINE_PARKING_DTLS_PK_SEQ", "quarantine_parking_dtls", $con);


					$data_array_dtls .= "(" . $id_dtls . "," . $master_id . "," . $txt_wo_pi_id . ",'" . $$piWoDtlsId . "'," . $txt_booking_pi_no . ",'" . $$count . "','" . $$composition . "','" . $$comPersent . "','" . $$yarnType . "','" . $$color . "','" . $$TxtLot . "','" . $$TxtBrand . "','" . $$productCode . "','" . $$uom . "','" . $$receiveqnty . "','" . $$greyqnty . "','" . $$greyRate . "','" . $$rate . "','" . $$DCharge . "','" . $$avgRate . "','" . $$amount . "','" . $$bookCurrency . "','" . $$noOfBag .  "','" . $$looseBagWtBrkdwn . "','" . $$conPerBag . "','" . $$loseCone . "','" . $$wetPerBag . "','" . $$wetPerCon . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "','1','0','1','529')";

					if($$looseBagWtBrkdwn != '')
					{
						$data_dtls_brkdwn = explode("__",$$looseBagWtBrkdwn);

						foreach($data_dtls_brkdwn  as $val)
						{
							if ($k > 0) $data_array_dtls_brkdwn .= ",";

							$break_down_id = return_next_id_by_sequence("QUARANTINE_PAR_DTLS_LOOSE_RFID_PK_SEQ", "quarantine_par_dtls_loose_rfid", $con);
							
							$newData = explode("-",$val);
							$rfid = $newData[0];
							$weight = $newData[1];
							// print_r($newData);
							$field_array_dtls_brkdwn = "id, dtls_id, mst_id, RFID_NO, LOOSE_BAG_WEIGHT, insert_date, status_active, is_deleted";

							$data_array_dtls_brkdwn .= "(" . $break_down_id . "," . $id_dtls . "," . $master_id . ",'" . $rfid . "'," . $weight . ",'" . $pc_date_time . "','1','0' )";
							$k++;
						}
					}

					
				}

				//echo "10**insert into inv_receive_master (".$field_array.") values ".$data_array;oci_rollback($con);die;
				$rID = $rID2 = $rID3 = true;
				if (str_replace("'", "", $update_id) == "") {
					$rID = sql_insert("inv_receive_master", $field_array, $data_array, 0);
				} else {
					$rID = sql_update("inv_receive_master", $field_array_update, $data_array_update, "id", $update_id, 1);
				}

				//echo "10**insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;oci_rollback($con);disconnect($con);die;
				if ($data_array_dtls != "") {
					//echo "10**insert into quarantine_parking_dtls (".$field_array_dtls.") values ".$data_array_dtls;oci_rollback($con);disconnect($con);die;
					$rID2 = sql_insert("quarantine_parking_dtls", $field_array_dtls, $data_array_dtls, 0);
				}

				if ($data_array_dtls_brkdwn != "") {
					//echo "10**insert into quarantine_par_dtls_loose_rfid (".$field_array_dtls_brkdwn.") values ".$data_array_dtls_brkdwn;oci_rollback($con);disconnect($con);die;
					$rID3 = sql_insert("quarantine_par_dtls_loose_rfid", $field_array_dtls_brkdwn, $data_array_dtls_brkdwn, 0);
				}

				/* echo "10**$rID=$rID2";
				oci_rollback($con);
				disconnect($con);
				die; */
				//oci_rollback($con);check_table_status( $_SESSION['menu_id'],0);disconnect($con);die;$ordProdUpdate=$ordProdInsert=

				//check_table_status( $_SESSION['menu_id'],0);
				if ($db_type == 0) {
					if ($rID && $rID2 && $rID3) {
						mysql_query("COMMIT");
						echo "0**" . $master_id . "**" . $trims_recv_num . "**0";
					} else {
						mysql_query("ROLLBACK");
						echo "5**0**" . "&nbsp;" . "**0";
					}
				} else if ($db_type == 2 || $db_type == 1) {
					if ($rID && $rID2 && $rID3) {
						oci_commit($con);
						echo "0**" . $master_id . "**" . $trims_recv_num . "**0";
					} else {
						oci_rollback($con);
						echo "5**0**" . "&nbsp;" . "**0";
					}
				}
				//check_table_status( $_SESSION['menu_id'],0);
				disconnect($con);
				die;
			}
		} else  //approval needed false
		{
			$con = connect();
			if ($db_type == 0) {
				mysql_query("BEGIN");
			}

			$trims_recv_num = '';
			$master_id = '';
			if (str_replace("'", "", $update_id) == "") {
				if ($db_type == 0) $year_cond = "YEAR(insert_date)";
				else if ($db_type == 2) $year_cond = "to_char(insert_date,'YYYY')";
				else $year_cond = ""; //defined Later

				$id = return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con);
				$new_trims_recv_system_id = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con, 1, $cbo_company_id, 'YGRN', 529, date("Y", time())));
				$field_array = "id, recv_number_prefix, recv_number_prefix_num, recv_number, entry_form, item_category, receive_basis, receive_purpose, company_id, receive_date, challan_no, booking_id, booking_no, store_id, loan_party, source, supplier_id, currency_id, exchange_rate, remarks, gate_entry_no, gate_entry_date, inserted_by, insert_date";
				$data_array = "(" . $id . ",'" . $new_trims_recv_system_id[1] . "'," . $new_trims_recv_system_id[2] . ",'" . $new_trims_recv_system_id[0] . "',529,1," . $cbo_receive_basis . "," . $cbo_receive_purpose . "," . $cbo_company_id . "," . $txt_receive_date . "," . $txt_receive_chal_no . "," . $txt_wo_pi_id . "," . $txt_booking_pi_no . "," . $cbo_store_name . "," . $cbo_party . "," . $cbo_source . "," . $cbo_supplier . "," . $cbo_currency_id . "," . $txt_exchange_rate . "," . $txt_mst_remarks . "," . $txt_gate_entry_no . "," . $txt_gate_entry_date . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "')";

				$trims_recv_num = $new_trims_recv_system_id[0];
				$master_id = $id;
			} else {
				$original_receive_basis = sql_select("select receive_basis, supplier_id, source from inv_receive_master where id=$update_id");
				if (str_replace("'", "", $cbo_receive_basis) != $original_receive_basis[0][csf('receive_basis')]) {
					echo "40**Multiple Receive Basis Not Allow In Same Received ID";
					disconnect($con);
					die;
				}
				if (str_replace("'", "", $cbo_source) != $original_receive_basis[0][csf('source')]) {
					echo "40**Multiple Source Not Allow In Same Received ID";
					disconnect($con);
					die;
				}

				if (str_replace("'", "", $cbo_supplier) != $original_receive_basis[0][csf('supplier_id')]) {
					echo "40**Multiple Supplier Not Allow In Same Received ID";
					disconnect($con);
					die;
				}

				$field_array_update = "receive_basis*receive_purpose*receive_date*challan_no*booking_id*booking_no*store_id*loan_party*source*supplier_id*currency_id*exchange_rate*remarks*updated_by*update_date";

				$data_array_update = $cbo_receive_basis . "*" . $cbo_receive_purpose . "*" . $txt_receive_date . "*" . $txt_receive_chal_no . "*" . $txt_wo_pi_id . "*" . $txt_booking_pi_no . "*" . $cbo_store_name . "*" . $cbo_party . "*" . $cbo_source . "*" . $cbo_supplier . "*" . $cbo_currency_id . "*" . $txt_exchange_rate . "*" . $txt_mst_remarks . "*" . $txt_gate_entry_no . "*" . $txt_gate_entry_date . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

				$trims_recv_num = str_replace("'", "", $txt_recieved_id);
				$master_id = str_replace("'", "", $update_id);
			}

			$field_array_dtls = "id, mst_id, wo_pi_id, wo_pi_dtls_id, wo_pi_no, yarn_count, yarn_comp_type1st, yarn_comp_percent1st, yarn_type, color_name, lot, brand_name, product_code, uom, parking_quantity, grey_quantity, grey_rate, rate, dyeing_charge, avg_rate, amount, cons_amount, no_of_bag, no_of_loose_bag, cone_per_bag, lose_cone, weight_per_bag, weight_cone, inserted_by, insert_date, status_active, is_deleted, item_category_id, entry_form";
			$field_array_dtls_brkdwn = "id, mst_id, dtls_id, RFID_NO, LOOSE_BAG_WEIGHT, insert_date, status_active, is_deleted";
			$data_array_dtls_brkdwn = "";
			$k=0;
			for ($i = 1; $i <= $tot_row; $i++) {
				$count = "count" . $i;
				$composition = "composition" . $i;
				$comPersent = "comPersent" . $i;
				$yarnType = "yarnType" . $i;
				$color = "color" . $i;
				$TxtLot = "TxtLot" . $i;
				$TxtBrand = "TxtBrand" . $i;
				$receiveqnty = "receiveqnty" . $i;
				$greyqnty = "greyqnty" . $i;
				$greyRate = "greyRate" . $i;

				$uom = "uom" . $i;
				$rate = "rate" . $i;

				$avgRate = "avgRate" . $i;
				$DCharge = "DCharge" . $i;
				$ilePersent = "ilePersent" . $i;
				$amount = "amount" . $i;
				$bookCurrency = "bookCurrency" . $i;

				$woPiBalQnty = "woPiBalQnty" . $i;
				$overRcvQnty = "overRcvQnty" . $i;
				$noOfBag = "noOfBag" . $i;
				$noOfLooseBag = "noOfLooseBag" . $i;
				$looseBagWtBrkdwn = "looseBagWt_brkdwn" . $i;
				$conPerBag = "conPerBag" . $i;
				$loseCone = "loseCone" . $i;
				$wetPerBag = "wetPerBag" . $i;
				$wetPerCon = "wetPerCon" . $i;
				$productCode = "productCode" . $i;

				$piWoDtlsId = "piWoDtlsId" . $i;
				$updatedtlsid = "updatedtlsid" . $i;
				$color_id = $color_id_arr[$$color];
               

				// echo "color Id: ".$color_id." done";
				// if(str_replace("'","",$$color)!="")
				// {
				// 	if (!in_array(str_replace("'","",$$color),$new_array_color))
				// 	{
				// 		$color_id = return_id( str_replace("'","",$$color), $color_arr, "lib_color", "id,color_name","70");
				// 		$new_array_color[$color_id]=str_replace("'","",$$color);
				// 	}
				// 	else $color_id =  array_search(str_replace("'","",$$color), $new_array_color);
				// }
				// else
				// {
				// 	$color_id=0;
				// }
				$cbo_receive_basis = str_replace("'", "", $cbo_receive_basis);
				if ($cbo_receive_basis != 4) {
					if ($$receiveqnty > $$woPiBalQnty) {
						echo "20**Receive Quantity Not Allow Over Balance Quantity";
						disconnect($con);
						die;
					}
				}
				// if($$receiveqnty > $$woPiBalQnty)
				// {
				// 	echo "20**Receive Quantity Not Allow Over Balance Quantity";disconnect($con);die;
				// }


				$id_dtls = return_next_id_by_sequence("QUARANTINE_PARKING_DTLS_PK_SEQ", "quarantine_parking_dtls", $con);

				$data_array_dtls .= "(" . $id_dtls . "," . $master_id . "," . $txt_wo_pi_id . ",'" . $$piWoDtlsId . "'," . $txt_booking_pi_no . ",'" . $$count . "','" . $$composition . "','" . $$comPersent . "','" . $$yarnType . "','" . $$color . "','" . $$TxtLot . "','" . $$TxtBrand . "','" . $$productCode . "','" . $$uom . "','" . $$receiveqnty . "','" . $$greyqnty . "','" . $$greyRate . "','" . $$rate . "','" . $$DCharge . "','" . $$avgRate . "','" . $$amount . "','" . $$bookCurrency . "','" . $$noOfBag . "','" . $$noOfLooseBag . "','" . $$conPerBag . "','" . $$loseCone . "','" . $$wetPerBag . "','" . $$wetPerCon . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "','1','0','1','529')";

				if($$looseBagWtBrkdwn != '')
				{
					$data_dtls_brkdwn = explode("__",$$looseBagWtBrkdwn);

					foreach($data_dtls_brkdwn  as $val)
					{
						if ($k > 0) $data_array_dtls_brkdwn .= ",";

						$break_down_id = return_next_id_by_sequence("QUARANTINE_PAR_DTLS_LOOSE_RFID_PK_SEQ", "quarantine_par_dtls_loose_rfid", $con);
						
						$newData = explode("-",$val);
						$rfid = $newData[0];
						$weight = $newData[1];
						// print_r($newData);
						$field_array_dtls_brkdwn = "id, dtls_id, mst_id, RFID_NO, LOOSE_BAG_WEIGHT, insert_date, status_active, is_deleted";

						$data_array_dtls_brkdwn .= "(" . $break_down_id . "," . $id_dtls . "," . $master_id . ",'" . $rfid . "'," . $weight . ",'" . $pc_date_time . "','1','0' )";
						$k++;
					}
				}

				
			}
		    // die;
			// echo "10**insert into inv_receive_master (".$field_array.") values ".$data_array;oci_rollback($con);die;
			$rID = $rID2 = $rID3 = true;
			if (str_replace("'", "", $update_id) == "") {
				$rID = sql_insert("inv_receive_master", $field_array, $data_array, 0);
			} else {
				$rID = sql_update("inv_receive_master", $field_array_update, $data_array_update, "id", $update_id, 1);
			}

			// echo "10**insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;oci_rollback($con);disconnect($con);die;
			if ($data_array_dtls != "") {
				//  echo "10**insert into quarantine_parking_dtls (".$field_array_dtls.") values ".$data_array_dtls;oci_rollback($con);disconnect($con);die;
				$rID2 = sql_insert("quarantine_parking_dtls", $field_array_dtls, $data_array_dtls, 0);
			}

			if ($data_array_dtls_brkdwn != "") {
				//echo "10**insert into quarantine_par_dtls_loose_rfid (".$field_array_dtls_brkdwn.") values ".$data_array_dtls_brkdwn;oci_rollback($con);disconnect($con);die;
				$rID3 = sql_insert("quarantine_par_dtls_loose_rfid", $field_array_dtls_brkdwn, $data_array_dtls_brkdwn, 0);
			}

			
			// echo "10**insert into inv_transaction (".$field_array_trans.") values ".$data_array_trans;oci_rollback($con);disconnect($con);die;
			//echo "10**".$rID ."&&". $rID2 ."&&". $rID3."&&". oci_rollback($con);disconnect($con);die;
			//oci_rollback($con);check_table_status( $_SESSION['menu_id'],0);disconnect($con);die;$ordProdUpdate=$ordProdInsert=

			//check_table_status( $_SESSION['menu_id'],0);
			if ($rID && $rID2 && $rID3) {
				oci_commit($con);
				echo "0**" . $master_id . "**" . $trims_recv_num . "**0";
			} else {
				oci_rollback($con);
				echo "5**0**" . "&nbsp;" . "**0";
				//echo $rID;
				//echo $rID2;
				//echo $rID3;
			}
			//check_table_status( $_SESSION['menu_id'],0);
			disconnect($con);
			die;
		}
	} else if ($operation == 1)   // Update Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		$trims_recv_num = str_replace("'", "", $txt_recieved_id);
		$master_id = str_replace("'", "", $update_id);

		if ($master_id < 1) {
			echo "40**Update Not Allow";
			//check_table_status( $_SESSION['menu_id'],0);
			disconnect($con);
			die;
		}


		$prev_grn_dlts_sql = sql_select("select ID from QUARANTINE_PARKING_DTLS where status_active=1 and is_deleted=0 and mst_id=$master_id");
		$prev_grn_dlts_brkdwn_sql = sql_select("select id, dtls_id from quarantine_par_dtls_loose_rfid where status_active=1 and is_deleted=0 and mst_id=$master_id");
		
		$prev_grn_dlts_ids = array();
		foreach ($prev_grn_dlts_sql as $row) {
			$prev_grn_dlts_ids[$row["ID"]] = $row["ID"];
		}
		// echo "<pre>"; 
		// print_r($prev_grn_dlts_ids);
		// die;

		//echo "10**select receive_basis, supplier_id, source, qc_check_by from inv_receive_master where id=$master_id";disconnect($con);die;
		$original_receive_basis = sql_select("select receive_basis, supplier_id, source, qc_check_by from inv_receive_master where id=$master_id");
		if (str_replace("'", "", $cbo_receive_basis) != $original_receive_basis[0][csf('receive_basis')]) {
			echo "40**Multiple Receive Basis Not Allow In Same Received ID";
			disconnect($con);
			die;
		}

		if (str_replace("'", "", $cbo_source) != $original_receive_basis[0][csf('source')]) {
			echo "40**Multiple Source Not Allow In Same Received ID";
			disconnect($con);
			die;
		}
		if (str_replace("'", "", $cbo_supplier) != $original_receive_basis[0][csf('supplier_id')]) {
			echo "40**Multiple Supplier Not Allow In Same Received ID";
			disconnect($con);
			die;
		}

		if ($original_receive_basis[0][csf('qc_check_by')] > 0) {
			echo "40**This GRN Already QC Passed";
			disconnect($con);
			die;
		}

		$field_array_update = "receive_basis*receive_purpose*receive_date*challan_no*booking_id*booking_no*store_id*loan_party*source*supplier_id*currency_id*exchange_rate*remarks*gate_entry_no*gate_entry_date*updated_by*update_date";

		$data_array_update = $cbo_receive_basis . "*" . $cbo_receive_purpose . "*" . $txt_receive_date . "*" . $txt_receive_chal_no . "*" . $txt_wo_pi_id . "*" . $txt_booking_pi_no . "*" . $cbo_store_name . "*" . $cbo_party . "*" . $cbo_source . "*" . $cbo_supplier . "*" . $cbo_currency_id . "*" . $txt_exchange_rate . "*" . $txt_mst_remarks . "*" . $txt_gate_entry_no . "*" . $txt_gate_entry_date . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'";

		$field_array_dtls = "id, mst_id, wo_pi_id, wo_pi_dtls_id, wo_pi_no, yarn_count, yarn_comp_type1st, yarn_comp_percent1st, yarn_type, color_name, lot, brand_name, product_code, uom, parking_quantity, grey_quantity, grey_rate, rate, dyeing_charge, avg_rate, amount, cons_amount, no_of_bag, NO_OF_LOOSE_BAG, cone_per_bag, lose_cone, weight_per_bag, weight_cone, inserted_by, insert_date, status_active, is_deleted, item_category_id, entry_form";
		$field_array_dtls_brkdwn = "id, mst_id, dtls_id, rfid_no, loose_bag_weight";


		$field_array_dtls_update = "lot*brand_name*parking_quantity*grey_quantity*grey_rate*amount*cons_amount*no_of_bag*no_of_loose_bag*cone_per_bag*lose_cone*weight_per_bag*weight_cone*updated_by*update_date";
		$field_array_dtls_brkdwn_update = "rfid_no*loose_bag_weight*insert_date";
		$updateDtlsID_array = array();
		$data_array_dtls = "";
		$data_array_dtls_brkdwn = "";
		$k=0;
		for ($i = 1; $i <= $tot_row; $i++) {
			$count = "count" . $i;
			$composition = "composition" . $i;
			$comPersent = "comPersent" . $i;
			$yarnType = "yarnType" . $i;
			$color = "color" . $i;
			$TxtLot = "TxtLot" . $i;
			$TxtBrand = "TxtBrand" . $i;
			$receiveqnty = "receiveqnty" . $i;
			$greyqnty = "greyqnty" . $i;
			$greyRate = "greyRate" . $i;
			$uom = "uom" . $i;
			$rate = "rate" . $i;

			$avgRate = "avgRate" . $i;
			$DCharge = "DCharge" . $i;
			$ilePersent = "ilePersent" . $i;
			$amount = "amount" . $i;
			$bookCurrency = "bookCurrency" . $i;

			$woPiBalQnty = "woPiBalQnty" . $i;
			$overRcvQnty = "overRcvQnty" . $i;
			$noOfBag = "noOfBag" . $i;
			$noOfLooseBag = "noOfLooseBag" . $i;
			$looseBagWtBrkdwn = "looseBagWt_brkdwn" . $i;
			$conPerBag = "conPerBag" . $i;
			$loseCone = "loseCone" . $i;
			$wetPerBag = "wetPerBag" . $i;
			$wetPerCon = "wetPerCon" . $i;
			$productCode = "productCode" . $i;

			$piWoDtlsId = "piWoDtlsId" . $i;
			$updatedtlsid = "updatedtlsid" . $i;
			$cbo_receive_basis = str_replace("'", "", $cbo_receive_basis);

			if ($cbo_receive_basis != 4) {
				if ($$receiveqnty > $$woPiBalQnty) {
					echo "20**Receive Quantity Not Allow Over Balance Quantity";
					disconnect($con);
					die;
				}
			}

			// echo $$updatedtlsid."<br>";
			$updatebrkdwnid = array();
			foreach($prev_grn_dlts_brkdwn_sql as $val)
			{
				if($val['DTLS_ID']==$$updatedtlsid)
				{
					$updatebrkdwnid[$val['ID']]=$val['ID'];
				}
			}

			$data_dtls_brkdwn = explode("__",$$looseBagWtBrkdwn);

			foreach($data_dtls_brkdwn  as $val)
			{
				$newData = explode("-",$val);
				$rfid = $newData[0];
				$weight = $newData[1];

				// echo $newData[2]."<br>";
				if($newData[2] != "") // Existing data update
				{
					$loos_update_id_arr[] = $newData[2];
					$loose_rfid_update_data_array[$newData[2]]= explode("*", ("".$rfid."*".$weight."*'".$pc_date_time."'"));					
				}
				else // Newly insert 
				{
					if ($k > 0) $data_array_dtls_brkdwn .= ",";
					$break_down_id = return_next_id_by_sequence("QUARANTINE_PAR_DTLS_LOOSE_RFID_PK_SEQ", "quarantine_par_dtls_loose_rfid", $con);
					// print_r($newData);
					$field_array_dtls_brkdwn = "id, dtls_id, mst_id, RFID_NO, LOOSE_BAG_WEIGHT, insert_date, status_active, is_deleted";
					$data_array_dtls_brkdwn .= "(" . $break_down_id . "," . $$updatedtlsid . "," . $master_id . ",'" . $rfid . "'," . $weight . ",'" . $pc_date_time . "','1','0' )";
					$k++;
				}
				
			}

			if ($$updatedtlsid > 0) {
				$updateDtlsID_array[] = $$updatedtlsid;
				$data_array_dtls_update[$$updatedtlsid] = explode("*", ("'" . $$TxtLot . "'*'" . $$TxtBrand . "'*'" . $$receiveqnty . "'*'" . $$greyqnty . "'*'" . $$greyRate . "'*'" . $$amount . "'*'" . $$bookCurrency . "'*'" . $$noOfBag . "'*'" . $$noOfLooseBag . "'*'" . $$conPerBag . "'*'" . $$loseCone . "'*'" . $$wetPerBag . "'*'" . $$wetPerCon . "'*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));
				unset($prev_grn_dlts_ids[$$updatedtlsid]);
			} else {
				$id_dtls = return_next_id_by_sequence("QUARANTINE_PARKING_DTLS_PK_SEQ", "quarantine_parking_dtls", $con);
				$data_array_dtls .= "(" . $id_dtls . "," . $master_id . "," . $txt_wo_pi_id . ",'" . $$piWoDtlsId . "'," . $txt_booking_pi_no . ",'" . $$count . "','" . $$composition . "','" . $$comPersent . "','" . $$yarnType . "','" . $$color . "','" . $$TxtLot . "','" . $$TxtBrand . "','" . $$productCode . "','" . $$uom . "','" . $$receiveqnty . "','" . $$greyqnty . "','" . $$greyRate . "','" . $$rate . "','" . $$DCharge . "','" . $$avgRate . "','" . $$amount . "','" . $$bookCurrency . "','" . $$noOfBag . "','" . $$noOfLooseBag . "','" . $$conPerBag . "','" . $$loseCone . "','" . $$wetPerBag . "','" . $$wetPerCon . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "','1','0','1','529')";
			}
		}

		//print_r($data_array_dtls_update);die;

		//echo "insert into quarantine_parking_dtls $field_array_dtls_update values $data_array_dtls_update";die;


		$rID = $dtlsUpdate = $rID2 = $rID3 = $rID4 = $rID5 = true;

		$rID = sql_update("inv_receive_master", $field_array_update, $data_array_update, "id", $master_id, 1);

		if (count($updateDtlsID_array) > 0) {
			$dtlsUpdate = execute_query(bulk_update_sql_statement("quarantine_parking_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $updateDtlsID_array), 1);
		}

		if ($data_array_dtls != "") {
			$rID2 = sql_insert("quarantine_parking_dtls", $field_array_dtls, $data_array_dtls, 0);
		}


		if (count($prev_grn_dlts_ids) > 0) {
			$rID3 = execute_query("update quarantine_parking_dtls set status_active=0, is_deleted=1, updated_by=" . $_SESSION['logic_erp']['user_id'] . ", update_date='" . $pc_date_time . "' where id in(" . implode(",", $prev_grn_dlts_ids) . ")");
		}


		if(!empty($loose_rfid_update_data_array))
		{
			$rID4 = execute_query(bulk_update_sql_statement("quarantine_par_dtls_loose_rfid", "id", $field_array_dtls_brkdwn_update, $loose_rfid_update_data_array, $loos_update_id_arr),0);
		}

		if ($data_array_dtls_brkdwn != "") {
			// echo "10**insert into quarantine_par_dtls_loose_rfid (".$field_array_dtls_brkdwn.") values ".$data_array_dtls_brkdwn;oci_rollback($con);disconnect($con);die;
			$rID5 = sql_insert("quarantine_par_dtls_loose_rfid", $field_array_dtls_brkdwn, $data_array_dtls_brkdwn, 0);
		}
		

		// echo "10**$rID&&$dtlsUpdate&&$rID2&&$rID3 && $rID4 && $rID5 ** ";oci_rollback($con);disconnect($con);die;
		//oci_rollback($con);check_table_status( $_SESSION['menu_id'],0);disconnect($con);die; 10**1=0=1=1
		


		if ($db_type == 0) {
			if ($rID && $dtlsUpdate && $rID2 && $rID3 && $rID4 && $rID5 ) {
				mysql_query("COMMIT");
				echo "1**" . str_replace("'", '', $master_id) . "**" . str_replace("'", '', $txt_recieved_id) . "**0";
			} else {
				mysql_query("ROLLBACK");
				echo "6**0**0**1";
			}
		}

		if ($db_type == 2 || $db_type == 1) {
			if ($rID && $dtlsUpdate && $rID2 && $rID3 && $rID4 && $rID5) {
				oci_commit($con);
				echo "1**" . str_replace("'", '', $master_id) . "**" . str_replace("'", '', $txt_recieved_id) . "**0";
			} else {
				oci_rollback($con);
				echo "6**0**0**1";
			}
		}
		//check_table_status( $_SESSION['menu_id'],0);
		disconnect($con);
		die;
	} else if ($operation == 2)   // Delete Here
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		//if( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}		

		$update_id = str_replace("'", "", $update_id);

		if ($update_id > 0) {
			if ($db_type == 0) $trns_id_select = ", group_concat(id) as all_rcv_id";
			else $trns_id_select = ", LISTAGG(cast(id as varchar2(4000)), ',') WITHIN GROUP (ORDER BY id) as all_rcv_id";
			$rcv_sql = sql_select("select max(id) as rcv_id, prod_id, sum(cons_quantity) as rcv_qnty, sum(cons_amount) as rcv_amt  $trns_id_select  from inv_transaction where transaction_type=1 and mst_id=$update_id and status_active=1 group by prod_id order by prod_id");
			$receive_data = array();
			$all_rcv_trans_id = "";
			$all_product_id = "";
			$prod_wise_data = array();
			foreach ($rcv_sql as $row) {
				$receive_data[$row[csf("prod_id")]] = $row[csf("rcv_id")];
				$all_rcv_trans_id .= $row[csf("rcv_id")] . ",";
				$all_rcv_id .= $row[csf("all_rcv_id")] . ",";
				$all_product_id .= $row[csf("prod_id")] . ",";
				$prod_wise_data[$row[csf("prod_id")]]["rcv_qnty"] = $row[csf("rcv_qnty")];
				$prod_wise_data[$row[csf("prod_id")]]["rcv_amt"] = $row[csf("rcv_amt")];
			}

			$all_rcv_id = chop($all_rcv_id, ",");
			$all_rcv_trans_id = chop($all_rcv_trans_id, ",");
			$all_product_id = chop($all_product_id, ",");


			$issue_sql = sql_select("select min(a.id) as issue_id, min(b.issue_number) as issue_number, a.prod_id  
			from inv_transaction a, inv_issue_master b 
			where a.mst_id=b.id and a.transaction_type in(2,3) and a.transaction_date >='" . str_replace("'", "", $txt_receive_date) . "' and a.status_active=1 and a.prod_id in($all_product_id)
			group by prod_id
			union all 
			select min(a.id) as issue_id, min(b.transfer_system_id) as issue_number, a.prod_id  
			from inv_transaction a, inv_item_transfer_mst b 
			where a.mst_id=b.id and a.transaction_type in(2,3) and a.transaction_date >='" . str_replace("'", "", $txt_receive_date) . "' and a.status_active=1 and a.prod_id in($all_product_id)
			group by prod_id");
			$issue_data = array();
			foreach ($issue_sql as $row) {
				$issue_data[$row[csf("prod_id")]]["issue_id"] = $row[csf("issue_id")];
				$issue_data[$row[csf("prod_id")]]["issue_number"] = $row[csf("issue_number")];
			}

			foreach ($receive_data as $prod_id => $rcv_val) {
				if ($issue_data[$prod_id]["issue_id"] > 0) {
					if ($issue_data[$prod_id]["issue_id"] > $rcv_val) {
						$issue_num = $issue_data[$prod_id]["issue_number"];
						echo "20**Issue Number $issue_num Found, Product Id $prod_id , Delete Not Allow.";
						die;
					}
				}
			}


			$field_array_prod_update = "avg_rate_per_unit*current_stock*stock_value*updated_by*update_date";
			$row_prod = sql_select("select id, current_stock, avg_rate_per_unit, stock_value 
			from product_details_master where id in($all_product_id) and status_active=1 and is_deleted=0");
			foreach ($row_prod as $row) {
				$prev_prod_qnty = $prod_wise_data[$row[csf("id")]]["rcv_qnty"];
				$prev_prod_amount = $prod_wise_data[$row[csf("id")]]["rcv_amt"];

				$curr_stock_qnty = ($row[csf("current_stock")] - $prev_prod_qnty);
				if ($curr_stock_qnty != 0) {
					$curr_stock_value = ($row[csf("current_stock")] - $prev_prod_amount);
					$avg_rate_per_unit = 0;
					if ($curr_stock_value != 0 && $curr_stock_qnty != 0) $avg_rate_per_unit = abs($curr_stock_value / $curr_stock_qnty);
					else $avg_rate_per_unit = 0;
				} else {
					$curr_stock_value = 0;
					$avg_rate_per_unit = 0;
				}

				$updateProdID_array[] = $row[csf("id")];
				$data_array_prod_update[$row[csf("id")]] = explode("*", ("" . $avg_rate_per_unit . "*" . $curr_stock_qnty . "*" . $curr_stock_value . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));
			}
			//echo "10**";print_r($updateProdID_array);print_r($data_array_prod_update);die;

			$order_wise_sql = sql_select("select prod_id, po_breakdown_id, quantity, order_amount from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form=24 and trans_id in($all_rcv_id)");
			$prod_order_data = array();
			foreach ($order_wise_sql as $row) {
				$all_prod_ids[$row[csf("prod_id")]] = $row[csf("prod_id")];
				$all_order_ids[$row[csf("po_breakdown_id")]] = $row[csf("po_breakdown_id")];
				$prod_order_data[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]]["quantity"] += $row[csf("quantity")];
				$prod_order_data[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]]["order_amount"] += $row[csf("order_amount")];
			}
			$field_array_prod_order_update = "avg_rate*stock_quantity*stock_amount*updated_by*update_date";
			$prod_order_sql = sql_select("select id, prod_id, po_breakdown_id, stock_quantity, stock_amount from order_wise_stock where status_active=1 and is_deleted=0 and prod_id in(" . implode(",", $all_prod_ids) . ") and po_breakdown_id in(" . implode(",", $all_order_ids) . ")");
			$avg_rate_per_unit = $curr_stock_qnty = $curr_stock_value = 0;
			foreach ($prod_order_sql as $row) {
				$prev_prod_ord_qnty = $prod_order_data[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]]["quantity"];
				$prev_prod_ord_amount = $prod_order_data[$row[csf("prod_id")]][$row[csf("po_breakdown_id")]]["order_amount"];

				$curr_stock_qnty = ($row[csf("stock_quantity")] - $prev_prod_ord_qnty);
				$curr_stock_value = ($row[csf("stock_amount")] - $prev_prod_ord_amount);
				if ($curr_stock_qnty > 0 && $curr_stock_value > 0) {
					$avg_rate_per_unit = 0;
					if ($curr_stock_value != 0 && $curr_stock_qnty != 0) $avg_rate_per_unit = abs($curr_stock_value / $curr_stock_qnty);
				} else {
					$avg_rate_per_unit = 0;
				}

				$updateProdOrderID_array[] = $row[csf("id")];
				$data_array_prod_order_update[$row[csf("id")]] = explode("*", ("" . $avg_rate_per_unit . "*" . $curr_stock_qnty . "*" . $curr_stock_value . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'"));
			}

			$rID = $rID2 = $rID3 = $rID4 = $rID5 = $rID6 = true;
			if (count($data_array_prod_update) > 0) {
				$rID = execute_query(bulk_update_sql_statement("product_details_master", "id", $field_array_prod_update, $data_array_prod_update, $updateProdID_array), 1);
			}
			if (count($data_array_prod_order_update) > 0) {
				$rID5 = execute_query(bulk_update_sql_statement("order_wise_stock", "id", $field_array_prod_order_update, $data_array_prod_order_update, $updateProdOrderID_array), 1);
			}
			$rID6 = execute_query("update inv_receive_master set status_active=0, is_deleted=1, updated_by='" . $_SESSION['logic_erp']['user_id'] . "', update_date='" . $pc_date_time . "' where id=$update_id");
			$rID2 = execute_query("update inv_transaction set status_active=0, is_deleted=1, updated_by='" . $_SESSION['logic_erp']['user_id'] . "', update_date='" . $pc_date_time . "' where transaction_type=1 and mst_id=$update_id");
			$rID3 = execute_query("update inv_trims_entry_dtls set status_active=0, is_deleted=1, updated_by='" . $_SESSION['logic_erp']['user_id'] . "', update_date='" . $pc_date_time . "' where mst_id=$update_id");
			$rID4 = execute_query("update order_wise_pro_details set status_active=0, is_deleted=1, updated_by='" . $_SESSION['logic_erp']['user_id'] . "', update_date='" . $pc_date_time . "' where trans_id in($all_rcv_id) and trans_type=1 and entry_form=24");

			//echo "10**$rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6";die;

			if ($db_type == 0) {
				if ($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6) {
					mysql_query("COMMIT");
					echo "2**" . str_replace("'", '', $update_id) . "**" . str_replace("'", '', $txt_recieved_id) . "**0";
				} else {
					mysql_query("ROLLBACK");
					echo "7**0**0**1";
				}
			} else if ($db_type == 2 || $db_type == 1) {
				if ($rID && $rID2 && $rID3 && $rID4 && $rID5 && $rID6) {
					oci_commit($con);
					echo "2**" . str_replace("'", '', $update_id) . "**" . str_replace("'", '', $txt_recieved_id) . "**0";
				} else {
					oci_rollback($con);
					echo "7**0**0**1";
				}
			}
			//check_table_status( $_SESSION['menu_id'],0);
			disconnect($con);
			die;
		}
	}
}



if ($action == "yarn_receive_popup_search") {
	echo load_html_head_contents("Yarn Receive Info", "../../../", 1, 1, '', '', '');
	extract($_REQUEST);
	?>

	<script>
		function js_set_value(id) {
			var ids = id.split("_");
			$('#hidden_recv_id').val(ids[0]);
			$('#hidden_posted_in_account').val(ids[1]);
			parent.emailwindow.hide();
		}
	</script>

	</head>

	<body>
		<div align="center" style="width:885px;">
			<form name="searchwofrm" id="searchwofrm">
				<fieldset style="width:883px; margin-left:3px">
					<legend>Enter search words</legend>
					<table cellpadding="0" cellspacing="0" width="820" class="rpt_table" border="1" rules="all">
						<thead>
							<th>Supplier</th>
							<th>Received Date Range</th>
							<th>Search By</th>
							<th id="search_by_td_up" width="200">Enter Received ID No</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
								<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
								<input type="hidden" name="hidden_recv_id" id="hidden_recv_id" class="text_boxes" value="">
								<input type="hidden" name="hidden_posted_in_account" id="hidden_posted_in_account" class="text_boxes" value="">
							</th>
						</thead>
						<tr class="general">
							<td align="center">
								<?
								echo create_drop_down("cbo_supplier", 150, "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$cbo_company_id' and b.party_type in(4,5) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", 'id,supplier_name', 1, '-- ALL Supplier --', 0);
								?>
							</td>
							<td align="center">
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px">To
								<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px">
							</td>
							<td align="center">
								<?
								$search_by_arr = array(1 => "Received ID", 2 => "WO/PI", 3 => "Challan No");
								$dd = "change_search_event(this.value, '0*0*0', '0*0*0', '../../../') ";
								echo create_drop_down("cbo_search_by", 150, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>
							<td align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_supplier').value+'_'+document.getElementById('cbo_year_selection').value, 'create_trims_recv_search_list_view', 'search_div', 'yarn_grn_receive_controller', 'setFilterGrid(\'list_view\',-1);')" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td colspan="5" align="center" height="40" valign="middle"><? echo load_month_buttons(1); ?></td>
						</tr>
					</table>
					<div style="width:100%; margin-top:5px; margin-left:2px" id="search_div" align="left"></div>
				</fieldset>
			</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>

	</html>
<?
}

if ($action == "create_trims_recv_search_list_view") {
	$data = explode("_", $data);

	$search_string = "%" . trim($data[0]) . "%";
	$search_by = $data[1];
	$start_date = $data[2];
	$end_date = $data[3];
	$company_id = $data[4];
	$supplier_id = $data[5];
	$cbo_year = $data[6];

	if ($supplier_id == 0) $supplier_name = "%%";
	else $supplier_name = $supplier_id;
	$com_arr = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", "id", "supplier_name");
	$store_arr = return_library_array("select id, store_name from lib_store_location", "id", "store_name");
	$user_name_arr = return_library_array("select id, user_name from user_passwd", "id", "user_name");

	if ($start_date != "" && $end_date != "") {
		if ($db_type == 0) {
			$date_cond = "and receive_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd", "-") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd", "-") . "'";
		} else {
			$date_cond = "and receive_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";
		}
	} else {
		if ($db_type == 0) {
			$year_cond = " and year(insert_date)=$cbo_year";
		} else if ($db_type == 2) {
			$year_cond = " and to_char(insert_date,'YYYY')=$cbo_year";
		}
	}


	if (trim($data[0]) != "") {
		if ($search_by == 1)
			$search_field_cond = "and recv_number like '$search_string'";
		else if ($search_by == 2)
			$search_field_cond = "and booking_no like '$search_string'";
		else
			$search_field_cond = "and challan_no like '$search_string'";
	} else {
		$search_field_cond = "";
	}

	if ($db_type == 0) {
		$year_field = "YEAR(insert_date) as year";
	} else if ($db_type == 2) {
		$year_field = "to_char(insert_date,'YYYY') as year";
	} else {
		$year_field = "";
	} //defined Later

	$sql = "SELECT id, recv_number_prefix_num, $year_field, recv_number, receive_basis, receive_purpose, supplier_id, store_id, source, currency_id, receive_date, challan_no, challan_date, pay_mode, is_posted_account,inserted_by from inv_receive_master where entry_form=529 and status_active=1 and is_deleted=0 and company_id=$company_id and supplier_id like '$supplier_name' $search_field_cond $date_cond  $year_cond order by id desc";
	//echo $sql;

	//$arr=array(2=>$receive_basis_arr,3=>$supplier_arr,4=>$store_arr,8=>$currency,9=>$source);
	//echo create_list_view("list_view", "Received No,Year,Receive Basis,Supplier,Store,Receive date,Challan No,Challan Date,Currency,Source", "75,50,105,130,80,75,75,80,60","870","240",0, $sql, "js_set_value", "id", "", 1, "0,0,receive_basis,supplier_id,store_id,0,0,0,currency_id,source", $arr, "recv_number_prefix_num,year,receive_basis,supplier_id,store_id,receive_date,challan_no,challan_date,currency_id,source", "",'','0,0,0,0,0,3,0,3,0,0');
?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="970" class="rpt_table">
		<thead>
			<tr>
				<th colspan="12"><?php echo create_drop_down("cbo_string_search_type", 130, $string_search_type, '', 1, "-- Searching Type --", 4); ?></th>
			</tr>
			<tr>
				<th width="40">Sl</th>
				<th width="75">Received No</th>
				<th width="50">Year</th>
				<th width="105">Receive Basis</th>
				<th width="130">Supplier</th>
				<th width="80">Store</th>
				<th width="80">Receive Purpose</th>
				<th width="75">Receive date</th>
				<th width="75">Challan No</th>
				<th width="60">Currency</th>
				<th width="60">Source</th>
				<th>Insert User</th>
			</tr>
		</thead>
	</table>
	<div style="width:970px; max-height:240px; overflow-y:scroll" id="search_div" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="950" class="rpt_table" id="list_view">
			<tbody>
			</tbody>
			<?
			$i = 1;
			$result = sql_select($sql);
			foreach ($result as $row) {
				if ($i % 2 == 0) $bgcolor = "#E9F3FF";
				else $bgcolor = "#FFFFFF";
			?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $row[csf('id')] . "_" . $row[csf('is_posted_account')]; ?>');">
					<td width="40" align="center">
						<p><? echo $i; ?></p>
					</td>
					<td width="75">
						<p><? echo $row[csf('recv_number_prefix_num')]; ?>&nbsp;</p>
					</td>
					<td width="50" align="center">
						<p><? echo $row[csf('year')]; ?>&nbsp;</p>
					</td>
					<td width="105">
						<p><? echo $receive_basis_arr[$row[csf('receive_basis')]]; ?>&nbsp;</p>
					</td>
					<td width="130" title="<? echo $row[csf('pay_mode')] . "==" . $row[csf('supplier_id')]; ?>">
						<p><? if ($row[csf('pay_mode')] == 3 || $row[csf('pay_mode')] == 5) echo $com_arr[$row[csf('supplier_id')]];
							else echo $supplier_arr[$row[csf('supplier_id')]]; ?>&nbsp;</p>
					</td>
					<td width="80">
						<p><? echo $store_arr[$row[csf('store_id')]]; ?>&nbsp;</p>
					</td>
					<td width="80" align="center">
						<p><? echo $yarn_issue_purpose[$row[csf('receive_purpose')]]; ?>&nbsp;</p>
					</td>
					<td width="75" align="center">
						<p><? if ($row[csf('receive_date')] != "" && $row[csf('receive_date')] != "0000-00-00") echo change_date_format($row[csf('receive_date')]); ?>&nbsp;</p>
					</td>
					<td width="75">
						<p><? echo $row[csf('challan_no')]; ?>&nbsp;</p>
					</td>

					<td width="60">
						<p><? echo $currency[$row[csf('currency_id')]]; ?>&nbsp;</p>
					</td>
					<td width="60">
						<p><? echo $source[$row[csf('source')]]; ?>&nbsp;</p>
					</td>
					<td>
						<p><? echo $user_name_arr[$row[csf('inserted_by')]]; ?>&nbsp;</p>
					</td>
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

if ($action == 'populate_data_from_trims_recv') {
	$data_array = sql_select("select id, recv_number, company_id, receive_basis, pay_mode, supplier_id, store_id, source, currency_id, challan_no, receive_date, challan_date, lc_no, exchange_rate, booking_id, booking_no, booking_without_order, receive_purpose, loan_party, remarks, gate_entry_no, gate_entry_date from inv_receive_master where id='$data'"); //, booking_id, booking_no, booking_without_order
	foreach ($data_array as $row) {
		echo "document.getElementById('txt_recieved_id').value 				= '" . $row[csf("recv_number")] . "';\n";
		echo "document.getElementById('cbo_company_id').value 				= '" . $row[csf("company_id")] . "';\n";
		echo "document.getElementById('cbo_receive_basis').value 			= '" . $row[csf("receive_basis")] . "';\n";
		echo "document.getElementById('cbo_receive_purpose').value 			= '" . $row[csf("receive_purpose")] . "';\n";


		if ($row[csf("receive_basis")] != '4') {
			echo "$('#cbo_company_id').attr('disabled','true')" . ";\n";
			echo "set_receive_basis(0);\n";
		}


		echo "document.getElementById('txt_receive_date').value 			= '" . change_date_format($row[csf("receive_date")]) . "';\n";
		echo "document.getElementById('txt_gate_entry_date').value 			= '" . change_date_format($row[csf("gate_entry_date")]) . "';\n";
		echo "document.getElementById('txt_receive_chal_no').value 			= '" . $row[csf("challan_no")] . "';\n";
		echo "document.getElementById('txt_mst_remarks').value 			= '" . $row[csf("remarks")] . "';\n";
		echo "document.getElementById('txt_gate_entry_no').value 			= '" . $row[csf("gate_entry_no")] . "';\n";

		//$lc_no=return_field_value("lc_number","com_btb_lc_master_details","id='".$row[csf("lc_no")]."'");
		//echo "document.getElementById('txt_challan_date').value 			= '".change_date_format($row[csf("challan_date")])."';\n";
		//echo "load_room_rack_self_bin('requires/yarn_grn_receive_controller*4', 'store','store_td', '".$row[csf('company_id')]."','"."',this.value);\n";
		//echo "document.getElementById('txt_lc_no').value 					= '".$lc_no."';\n";
		//echo "document.getElementById('lc_id').value 						= '".$row[csf("lc_no")]."';\n";
		echo "load_drop_down( 'requires/yarn_grn_receive_controller', '" . $row[csf('company_id')] . "', 'load_drop_down_store', 'store_td');\n";
		echo "document.getElementById('cbo_source').value 					= '" . $row[csf("source")] . "';\n";
		echo "document.getElementById('cbo_store_name').value 				= '" . $row[csf("store_id")] . "';\n";
		echo "document.getElementById('txt_wo_pi_id').value 				= '" . $row[csf("booking_id")] . "';\n";
		echo "document.getElementById('txt_booking_pi_no').value 				= '" . $row[csf("booking_no")] . "';\n";
		//echo "document.getElementById('booking_without_order').value 			= '".$row[csf("booking_without_order")]."';\n";
		echo "$('#cbo_store_name').attr('disabled','true')" . ";\n";

		echo "$('#cbo_currency_id').attr('disabled','true')" . ";\n";
		echo "$('#cbo_source').attr('disabled','true')" . ";\n";
		//echo "document.getElementById('cbo_pay_mode').value 			= '".$row[csf("pay_mode")]."';\n";
		echo "load_drop_down( 'requires/yarn_grn_receive_controller', '" . $row[csf("company_id")] . "', 'load_drop_down_supplier', 'supplier' );";
		echo "document.getElementById('cbo_supplier').value 			= '" . $row[csf("supplier_id")] . "';\n";
		echo "load_drop_down( 'requires/yarn_grn_receive_controller', '" . $row[csf("company_id")] . "', 'load_drop_down_party', 'loanParty' );";
		echo "document.getElementById('cbo_party').value 			= '" . $row[csf("loan_party")] . "';\n";
		echo "document.getElementById('cbo_currency_id').value 				= '" . $row[csf("currency_id")] . "';\n";
		echo "document.getElementById('txt_exchange_rate').value 			= '" . $row[csf("exchange_rate")] . "';\n";
		echo "document.getElementById('update_id').value 					= '" . $row[csf("id")] . "';\n";

		echo "$('#cbo_supplier').attr('disabled','true')" . ";\n";


		echo "set_button_status(1, '" . $_SESSION['page_permission'] . "', 'fnc_trims_receive',1,1);\n";
		exit();
	}
}

function return_domestic_rate($rate, $ile_cost, $exchange_rate, $conversion_factor)
{
	$rate_ile = $rate + $ile_cost;
	$rate_ile_exchange = $rate_ile * $exchange_rate;
	$doemstic_rate = $rate_ile_exchange / $conversion_factor;
	return $doemstic_rate;
}

if ($action == "trims_receive_entry_print") {
	extract($_REQUEST);
	$data = explode('*', $data);
	//print_r ($data);

	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$supplier_library = return_library_array("select id,supplier_name from lib_supplier", "id", "supplier_name");
	$item_library = return_library_array("select id, item_name from lib_item_group", "id", "item_name");
	$color_library = return_library_array("select id, color_name from lib_color", "id", "color_name");

	$sql = "select id, recv_number, receive_basis, receive_date, booking_id, booking_no, challan_no, challan_date, lc_no, source, store_id, supplier_id, currency_id, exchange_rate, booking_without_order, pay_mode from inv_receive_master where id='$data[1]' and status_active=1 and is_deleted=0 and entry_form=24 ";
	//echo $sql;
	$dataArray = sql_select($sql);

?>
	<div style="width:985px; margin-left:20px;">
		<table width="980" cellspacing="0" align="right" border="0">
			<tr>
				<td colspan="7" align="center" style="font-size:xx-large"><strong><? echo $company_library[$data[0]]; ?></strong>
					<br><b style="font-size:13px">
						<?
						$nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$data[0]");
						foreach ($nameArray as $result) {
							echo $result[csf('plot_no')] . ', ';
							echo $result[csf('level_no')] . ', ';
							echo $result[csf('road_no')] . ', ';
							echo $result[csf('block_no')] . ', ';
							echo $result[csf('city')] . ', ';
							echo $result[csf('zip_code')] . ', ';
							echo $result[csf('province')] . ', ';
							echo $country_arr[$result[csf('country_id')]];
						}
						?>
					</b>
				</td>
			</tr>

			<tr>
				<td colspan="7" align="center" style="font-size:x-large"><strong><u>Trims Receive Challan</u></strong></center>
				</td>
			</tr>
			<tr>
				<td width="160"><strong>System ID:</strong></td>
				<td width="175px"><? echo $dataArray[0][csf('recv_number')]; ?></td>
				<td width="120"><strong> Receive Basis :</strong></td>
				<td width="175px"><? echo $receive_basis_arr[$dataArray[0][csf('receive_basis')]]; ?></td>
				<td width="125"><strong>Received Date:</strong></td>
				<td width="175px"><? echo  change_date_format($dataArray[0][csf('receive_date')]); ?></td>
			</tr>
			<tr>
				<!--<td><strong>WO/PI:</strong></td> <td width="175px"><? echo $dataArray[0][csf('booking_no')]; ?></td>-->
				<td><strong>Challan No :</strong></td>
				<td width="175px"><? echo $dataArray[0][csf('challan_no')]; ?></td>
				<td><strong>Challan Date:</strong></td>
				<td width="175px"><? echo  change_date_format($dataArray[0][csf('challan_date')]); ?></td>
				<td><strong>Source:</strong></td>
				<td width="175px"><? echo $source[$dataArray[0][csf('source')]]; ?></td>
			</tr>
			<tr>
				<td><strong>Supplier:</strong></td>
				<td width="175px"><? if ($dataArray[0][csf('pay_mode')] == 3 || $dataArray[0][csf('pay_mode')] == 5) echo $company_library[$dataArray[0][csf('supplier_id')]];
									else echo $supplier_library[$dataArray[0][csf('supplier_id')]]; ?></td>
				<td><strong> Currency:</strong></td>
				<td width="175px"><? echo $currency[$dataArray[0][csf('currency_id')]]; ?></td>
			</tr>
		</table>
		<br>
		<div style="width:100%;">
			<table align="right" cellspacing="0" width="980" border="1" rules="all" class="rpt_table">
				<thead bgcolor="#dddddd" align="center">
					<th width="30">SL</th>
					<th width="90" align="center">WO/PI No.</th>
					<th width="90" align="center">Item Group</th>
					<th width="110" align="center">Item Des.</th>
					<th width="70" align="center">Gmts Color</th>
					<th width="70" align="center">Item Color</th>
					<th width="70" align="center">Item Size</th>
					<th width="70" align="center">Buyer Order</th>
					<th width="70" align="center">Internal Ref. No</th>
					<th width="40" align="center">UOM</th>
					<th width="70" align="center">WO. Qty </th>
					<th width="70" align="center">Curr. Rec. Qty </th>
					<th width="60" align="center">Rate</th>
					<th width="70" align="center">Amount</th>
					<th width="70" align="center">Total Recv. Qty.</th>
					<th width="70" align="center">Balance Qty.</th>
					<th width="50" align="center">Reject Qty</th>
				</thead>
				<?
				$mst_id = $dataArray[0][csf('id')];
				$booking_nos = '';
				$booking_sam_nos = '';
				$pi_ids = '';
				$orderIds = '';
				//echo "select booking_no, booking_id, booking_without_order, order_id from inv_trims_entry_dtls where mst_id='$mst_id' and status_active='1' and is_deleted='0'";
				$dtls_data = sql_select("select booking_no, booking_id, booking_without_order, order_id from inv_trims_entry_dtls where mst_id='$mst_id' and status_active='1' and is_deleted='0'");
				foreach ($dtls_data as $row) {
					$orderIds .= $row[csf('order_id')] . ",";

					if ($dataArray[0][csf('receive_basis')] == 1) {
						$pi_ids .= $row[csf('booking_id')] . ",";
					} else if ($dataArray[0][csf('receive_basis')] == 12) {
						$booking_nos .= "'" . $row[csf('booking_no')] . "',";
					} else if ($dataArray[0][csf('receive_basis')] == 2) {
						if ($row[csf('booking_without_order')] == 1) {
							$booking_sam_nos .= "'" . $row[csf('booking_no')] . "',";
						} else {
							$booking_nos .= "'" . $row[csf('booking_no')] . "',";
						}
					}
				}

				$orderIds = chop($orderIds, ',');
				$piArray = array();
				//echo $orderIds.test;
				if ($orderIds != "") {
					$orderIds = implode(",", array_unique(explode(",", $orderIds)));

					$piArray = array();
					$sql = "select a.id, a.po_number, a.grouping as internal_ref from wo_po_break_down a where a.id in($orderIds)";
					//echo $sql;
					$po_data = sql_select($sql);
					foreach ($po_data as $row) {

						$piArray[$row[csf('id')]]['po_number'] = $row[csf('po_number')];
						$piArray[$row[csf('id')]]['grouping'] = $row[csf('internal_ref')];
					}
				}
				//echo "<pre>";print_r($piArray);die;
				//echo $dataArray[0][csf('receive_basis')];die;
				if ($dataArray[0][csf('receive_basis')] == 2) {

					$recv_wo_data_arr = array();
					$recv_wo_data_arr_amt = array();
					$sql_recv = "select a.booking_no, b.order_id as po_id, b.item_group_id as item_group, b.item_description, b.gmts_color_id, b.item_color, b.item_size, a.recv_number, sum(c.quantity) as receive_qnty 
			from inv_receive_master a, inv_trims_entry_dtls b, order_wise_pro_details c
			where a.id=b.mst_id  and a.booking_no=b.booking_no and b.id=c.dtls_id and b.trans_id=c.trans_id and c.entry_form=24 and a.entry_form=24 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.trans_type=1 and c.po_breakdown_id in($orderIds) 
			group by a.recv_number, a.booking_no, b.item_group_id, b.item_description, b.gmts_color_id, b.item_color, b.item_size, b.order_id";
					//echo $sql_recv;//die;
					$recv_data = sql_select($sql_recv);
					foreach ($recv_data as $row) { //pre_cost_fabric_cost_dtls_id
						$po_id_arr = array_unique(explode(",", $row[csf('po_id')]));
						foreach ($po_id_arr as $po) {
							$recv_wo_data_arr[$row[csf('booking_no')]][$po][$row[csf('item_group')]][$row[csf('item_description')]]['recv_no'] .= $row[csf('recv_number')] . ',';
							$recv_wo_data_arr_amt[$row[csf('recv_number')]][$row[csf('booking_no')]][$po][$row[csf('item_group')]][$row[csf('item_description')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$row[csf('item_size')]]['recv_qty'] = $row[csf('receive_qnty')];
						}
					}
					//echo "<pre>";
					//print_r($recv_wo_data_arr_amt);


					$booking_nos = chop($booking_nos, ',');
					$booking_sam_nos = chop($booking_sam_nos, ',');
					//echo $booking_nos.kok;
					if ($booking_nos != "") {
						$booking_sam_nos = implode(",", array_unique(explode(",", $booking_sam_nos)));
						//,b.po_break_down_id
						$sql_bookingqty = sql_select("select b.booking_no, sum(c.cons) as wo_qnty, b.trim_group as item_group, c.color_number_id, c.item_color, c.description, c.gmts_sizes, c.item_size 
				from wo_booking_dtls b,wo_trim_book_con_dtls c 
				where b.id=c.wo_trim_booking_dtls_id and b.booking_no=c.booking_no and c.cons>0 and c.status_active=1 and c.is_deleted=0 and b.booking_no in($booking_nos) 
				group by b.booking_no, b.trim_group, c.color_number_id, c.item_color, c.description, c.gmts_sizes, c.item_size");
					}

					foreach ($sql_bookingqty as $b_qty) {
						if ($b_qty[csf('color_number_id')] == "") $b_qty[csf('color_number_id')] = 0;
						if ($b_qty[csf('item_color')] == "") $b_qty[csf('item_color')] = 0;
						$booking_qty_arr[$b_qty[csf('booking_no')]][$b_qty[csf('item_group')]][$b_qty[csf('color_number_id')]][$b_qty[csf('item_color')]][$b_qty[csf('description')]][$b_qty[csf('gmts_sizes')]][$b_qty[csf('item_size')]] += $b_qty[csf('wo_qnty')];
					}

					if ($booking_sam_nos != "") {
						$booking_sam_nos = implode(",", array_unique(explode(",", $booking_sam_nos)));
						$sql_bookingqtysam = sql_select("select a.booking_no, 0 as po_break_down_id, sum(a.trim_qty) as wo_qnty,a.trim_group as item_group,a.fabric_color as item_color,a.gmts_color as color_number_id,a.fabric_description as description, a.item_size, a.gmts_size 
				from wo_non_ord_samp_booking_dtls a 
				where a.booking_no in($booking_sam_nos) and a.status_active=1 and a.is_deleted=0 
				group by a.booking_no,b.po_break_down_id, a.trim_group, a.fabric_color, a.gmts_color, a.fabric_description, a.item_size, a.gmts_size ");
					}
					foreach ($sql_bookingqtysam as $b_qty) {
						if ($b_qty[csf('color_number_id')] == "") $b_qty[csf('color_number_id')] = 0;
						if ($b_qty[csf('item_color')] == "") $b_qty[csf('item_color')] = 0;
						$booking_qty_arr[$b_qty[csf('booking_no')]][$b_qty[csf('item_group')]][$b_qty[csf('color_number_id')]][$b_qty[csf('item_color')]][$b_qty[csf('description')]][$b_qty[csf('gmts_size')]][$b_qty[csf('item_size')]] += $b_qty[csf('wo_qnty')];
					}
				} else if ($dataArray[0][csf('receive_basis')] == 1) {
					$pi_ids = chop($pi_ids, ',');
					$sql_bookingqty = sql_select("select a.id, b.item_group, b.item_color, b.color_id as color_number_id, b.item_description as description, c.po_break_down_id, sum(b.quantity) as wo_qnty 
			from com_pi_master_details a, com_pi_item_details b, wo_booking_dtls c 
			where a.id=b.pi_id and b.work_order_dtls_id=c.id and a.id in($pi_ids) and b.status_active=1 and b.is_deleted=0 
			group by a.id, b.item_group, b.item_color, b.color_id, b.item_description, c.po_break_down_id");
					foreach ($sql_bookingqty as $b_qty) {
						if ($b_qty[csf('color_number_id')] == "") $b_qty[csf('color_number_id')] = 0;
						if ($b_qty[csf('item_color')] == "") $b_qty[csf('item_color')] = 0;
						$booking_qty_arr[$b_qty[csf('id')]][$b_qty[csf('item_group')]][$b_qty[csf('color_number_id')]][$b_qty[csf('item_color')]][$b_qty[csf('description')]] += $b_qty[csf('wo_qnty')];
					}
				} else if ($dataArray[0][csf('receive_basis')] == 12) {
					$booking_nos = chop($booking_nos, ',');
					$sql_bookingqty = sql_select("select b.po_break_down_id,c.trim_group as item_group,b.booking_no, sum(b.requirment) as wo_qnty,b.item_color,b.gmts_sizes ,b.description from 
			wo_booking_mst a,wo_trim_book_con_dtls b,wo_booking_dtls c
			 where a.booking_no=b.booking_no and a.supplier_id=147 and a.item_category=4 and c.po_break_down_id=b.po_break_down_id and c.job_no=b.job_no and c.booking_no=b.booking_no and c.booking_type=2  and b.booking_no in($booking_nos) group by b.booking_no,b.po_break_down_id, c.trim_group, b.item_color, b.gmts_sizes, b.description");

					foreach ($sql_bookingqty as $b_qty) {
						if ($b_qty[csf('gmts_sizes')] == "") $b_qty[csf('gmts_sizes')] = 0;
						if ($b_qty[csf('item_color')] == "") $b_qty[csf('item_color')] = 0;
						$booking_qty_arr[$b_qty[csf('booking_no')]][$b_qty[csf('po_break_down_id')]][$b_qty[csf('item_group')]][$b_qty[csf('gmts_sizes')]][$b_qty[csf('item_color')]][$b_qty[csf('description')]] = $b_qty[csf('wo_qnty')];
					}
				}

				//echo "<pre>";print_r($booking_qty_arr);die;

				$i = 1;
				$total_rec_qty = 0;
				$total_rec_balance_qty = 0;

				$sql_dtls = "select b.booking_no, b.booking_id, b.booking_without_order, b.item_group_id, b.item_description, b.order_id, b.gmts_color_id, b.item_color, b.item_size, sum(b.cons_qnty) as cons_qnty, b.order_uom, b.cons_uom, sum(b.receive_qnty) as receive_qnty, max(b.rate) as rate, sum(b.amount) as amount, sum(b.reject_receive_qnty) as reject_receive_qnty, b.gmts_size_id 
		from inv_trims_entry_dtls b 
		where b.mst_id='$mst_id' and b.status_active='1' and b.is_deleted='0'
		group by b.booking_no, b.booking_id, b.booking_without_order, b.item_group_id, b.item_description, b.order_id, b.gmts_color_id, b.item_color, b.item_size, b.order_uom, b.cons_uom, b.gmts_size_id";

				//echo $sql_dtls;
				$sql_result = sql_select($sql_dtls);
				foreach ($sql_result as $row) {
					//print_r($booking_qty_arr);
					if ($i % 2 == 0)
						$bgcolor = "#E9F3FF";
					else
						$bgcolor = "#FFFFFF";

					$order_id_arr = explode(",", $row[csf('order_id')]);

					$order_number = '';
					$recv_no_arr = '';
					$grouping_number = '';
					$grouping_number_arr = array();
					//echo "<pre>";print_r($piArray);
					foreach ($order_id_arr as $po_id) {
						$prev_recv_qty = 0;
						//echo $po_id."=".$piArray[$po_id]['po_number'];die;
						$order_number .= $piArray[$po_id]['po_number'] . ',';
						$grouping_number_arr[$piArray[$po_id]['grouping']] = $piArray[$po_id]['grouping'];
						$recv_no_arr = implode(",", array_unique(explode(",", $recv_wo_data_arr[$row[csf('booking_no')]][$po_id][$row[csf('item_group_id')]][$row[csf('item_description')]]['recv_no'])));

						$recv_id_arr = explode(",", $recv_no_arr);
						foreach ($recv_id_arr as $recv_id) {
							if ($recv_id != $dataArray[0][csf('recv_number')]) {
								$prev_recv_qty += $recv_wo_data_arr_amt[$recv_id][$row[csf('booking_no')]][$po_id][$row[csf('item_group_id')]][$row[csf('item_description')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$row[csf('item_size')]]['recv_qty'];
							}
						}
					}
					//echo $prev_recv_qty;
					$order_number = chop($order_number, ',');
					//$grouping_number=chop($grouping_number,',');
					$grouping_number = implode(',', $grouping_number_arr);
				?>
					<tr bgcolor="<? echo $bgcolor; ?>">
						<td><? echo $i; ?></td>
						<td title="<?= $row[csf('order_id')]; ?>">
							<p><? echo $row[csf('booking_no')]; ?></p>
						</td>
						<td>
							<p><? echo $item_library[$row[csf('item_group_id')]]; ?></p>
						</td>
						<td>
							<p><? echo $row[csf('item_description')]; ?></p>
						</td>
						<td>
							<p><? echo $color_library[$row[csf('gmts_color_id')]]; ?></p>
						</td>
						<td>
							<p><? echo $color_library[$row[csf('item_color')]]; ?></p>
						</td>
						<td>
							<p><? echo $row[csf('item_size')]; ?></p>
						</td>
						<td width="170" style="word-break:break-all;"><? echo $order_number; ?></td>
						<td width="170" style="word-break:break-all;"><? echo $grouping_number; ?></td>
						<td align="center"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
						<td align="right" title="<? echo $row[csf('booking_no')] . "=" . $row[csf('item_group_id')] . "=" . $row[csf('gmts_color_id')] . "=" . $row[csf('item_color')] . "=" . $des_dtls . "=" . $row[csf('gmts_size_id')] . "=" . $row[csf('item_size')]; ?>">
							<?
							if ($row[csf('gmts_size_id')] == "") $row[csf('gmts_size_id')] = 0;
							if ($row[csf('gmts_color_id')] == "") $row[csf('gmts_color_id')] = 0;
							if ($row[csf('item_color')] == "") $row[csf('item_color')] = 0;
							$woorder_qty = '';
							$descrip_arr = explode(",", $row[csf('item_description')]);
							$last_index = end(array_values($descrip_arr));
							$last_index = str_replace("[", "", $last_index);
							$last_index = str_replace("]", "", $last_index);
							if (trim($last_index) == "BS") $des_dtls = chop($row[csf('item_description')], ', [BS]');
							else $des_dtls = $row[csf('item_description')];
							if ($dataArray[0][csf('receive_basis')] == 1) {
								$woorder_qty = $booking_qty_arr[$row[csf('booking_id')]][$row[csf('item_group_id')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$des_dtls];
							}
							if ($dataArray[0][csf('receive_basis')] == 12) {
								$woorder_qty = $booking_qty_arr[$row[csf('booking_no')]][$row[csf('order_id')]][$row[csf('item_group_id')]][$row[csf('gmts_size_id')]][$row[csf('item_color')]][$row[csf('item_description')]];
							} else {
								$woorder_qty = $booking_qty_arr[$row[csf('booking_no')]][$row[csf('item_group_id')]][$row[csf('gmts_color_id')]][$row[csf('item_color')]][$des_dtls][$row[csf('gmts_size_id')]][$row[csf('item_size')]];
							}
							$total_woorder_qty += $woorder_qty;
							echo number_format($woorder_qty, 2, ".", "");
							$tot_recv_qty = $row[csf('receive_qnty')] + $prev_recv_qty;
							$tot_recv_balance = $woorder_qty - $tot_recv_qty; //$row[csf('receive_qnty')]+$prev_recv_qty;
							?>
						</td>
						<td align="right" title="<? echo $des_dtls; ?>"><? echo number_format($row[csf('receive_qnty')], 2, ".", ""); ?></td>
						<td align="right"><? echo number_format($row[csf('rate')], 4, '.', ''); ?></td>
						<td align="right"><? echo number_format($row[csf('amount')], 2, '.', ''); ?></td>
						<td align="right"><? echo number_format($tot_recv_qty, 2, '.', ''); ?></td>
						<td align="right"><? echo number_format($tot_recv_balance, 2, '.', ''); ?></td>
						<td align="right"><? echo number_format($row[csf('reject_receive_qnty')], 2, '.', ''); ?></td>
					</tr>
				<?
					$i++;
					$tot_rec_qty += $row[csf('receive_qnty')];
					$tot_amount += $row[csf('amount')];
					$tot_reject_qty += $row[csf('reject_receive_qnty')];
					$total_rec_qty += $tot_recv_qty;
					$total_rec_balance_qty += $tot_recv_balance;
				}
				?>
				<tr bgcolor="#dddddd">
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>

					<td colspan="2" align="right"><b>Total :</b></td>
					<td align="right"><? echo number_format($total_woorder_qty, 2, '.', ''); ?></td>
					<td align="right"><? echo number_format($tot_rec_qty, 2, '.', ''); ?></td>
					<td>&nbsp;</td>
					<td align="right"><? echo number_format($tot_amount, 2, '.', ''); ?></td>
					<td align="right"><? echo number_format($total_rec_qty, 2, '.', ''); ?></td>
					<td align="right"><? echo number_format($total_rec_balance_qty, 2, '.', ''); ?></td>
					<td align="right"><? echo number_format($tot_reject_qty, 2, '.', ''); ?></td>
				</tr>
			</table>
			<br>
			<?
			echo signature_table(35, $data[0], "980px");
			?>
		</div>
	</div>
<?
	exit();
}

if ($action == "yarn_receive_print2") {
	extract($_REQUEST);
	$data = explode('*', $data);
	//print_r ($data);

	$sql = "select id, recv_number,supplier_id,currency_id,challan_no, receive_date, exchange_rate, store_id, receive_basis,lc_no,receive_purpose,booking_id, challan_date, gate_entry_no, gate_entry_date, boe_mushak_challan_no,boe_mushak_challan_date from inv_receive_master where recv_number='$data[1]'";


	$dataArray = sql_select($sql);
	$receive_pur = $dataArray[0][csf("receive_purpose")];
	$receive_basis = $dataArray[0][csf("receive_basis")];

	$wo_id = $dataArray[0][csf("booking_id")];

	$company_library = return_library_array("select id, company_name from lib_company", "id", "company_name");
	$location = return_field_value("location_name", "lib_location", "company_id=$data[0]");
	$address = return_field_value("address", "lib_location", "company_id=$data[0]");
	$supplier_library = return_library_array("select id,supplier_name from  lib_supplier", "id", "supplier_name");
	$supplier_address_library = return_library_array("select id,ADDRESS_1 from  lib_supplier", "id", "ADDRESS_1");
	$store_library = return_library_array("select id, store_name from  lib_store_location", "id", "store_name");
	$yarn_desc_arr = return_library_array("select id,yarn_description from lib_subcon_charge", 'id', 'yarn_description');
	$const_comp_arr = return_library_array("select id,const_comp from lib_subcon_charge", 'id', 'const_comp');
	$lcNum = return_library_array("select id,lc_number from com_btb_lc_master_details", 'id', 'lc_number');
	$country_arr = return_library_array("select id,country_name from lib_country", 'id', 'country_name');
	$user_id 		= return_library_array("select id,user_name from user_passwd", "id", "user_name");
	$user_name 		= return_library_array("select id,user_full_name from user_passwd", "id", "user_full_name");

	$floor_room_rack_arr = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst", 'floor_room_rack_id', 'floor_room_rack_name');

	$wo_service_type = array(2, 7, 12, 15, 38, 46, 50, 51);
	$exchange_currency = 0;
	if (in_array($receive_pur, $wo_service_type)) {
		$table_width = 1715;
		$exchange_currency = return_field_value("ecchange_rate", "wo_yarn_dyeing_mst", "id=$wo_id", "ecchange_rate");
	} else {
		$table_width = 1230;
	}

	if ($receive_basis == 2 && ($receive_pur == 2 || $receive_pur == 12 || $receive_pur == 15 || $receive_pur == 38 || $receive_pur == 46 || $receive_pur == 50 || $receive_pur == 51)) {

		$pay_mode = return_field_value("pay_mode", "wo_yarn_dyeing_mst", "id=$wo_id", "pay_mode");

		if ($pay_mode == 3 || $pay_mode == 5) {
			$supplier_name = $company_library[$dataArray[0][csf('supplier_id')]];
			$com_nameArray = sql_select("select plot_no,level_no,road_no,block_no,country_id,province,city,zip_code,email,website from lib_company where id=$dataArray[0][csf('supplier_id')]");
			foreach ($com_nameArray as $result) {
				$supplier_adress =  $result[csf('plot_no')] . $result[csf('level_no')] . $result[csf('road_no')] . $result[csf('block_no')] . $result[csf('city')] . $result[csf('zip_code')];
			}
		} else {
			$supplier_name = $supplier_library[$dataArray[0][csf('supplier_id')]];
			$supplier_adress = $supplier_address_library[$dataArray[0][csf('supplier_id')]];
		}
	} else {
		$supplier_name = $supplier_library[$dataArray[0][csf('supplier_id')]];
		$supplier_adress = $supplier_address_library[$dataArray[0][csf('supplier_id')]];
	}

?>
	<div id="table_row" style="width:<? echo $table_width; ?>px;">
		<table width="<? echo $table_width; ?>" align="right">
			<tr class="form_caption">
				<td colspan="6" align="center" style="font-size:20px">
					<strong><? echo $company_library[$data[0]]; ?></strong>
					<div style="float:right;width:24px; margin-right:80px; text-align:right">
						<div style="height:13px; width:15px;" id="qrcode"></div>
					</div>
				</td>

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
							Report GRN</u></strong></center>
				</td>
			</tr>
			<tr style="font-size:14px">
				<td width="120" valign='top'><strong>Supplier Name:</strong></td>
				<td width="210px">
					<? echo $supplier_name . '<br>' . $supplier_adress; ?>
				</td>
				<td width="140"><strong>MRR No:</strong></td>
				<td width="200px"><? echo $dataArray[0][csf('recv_number')]; ?></td>
				<td width="120"><strong>Currency:</strong></td>
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
			<tr style="font-size:14px">
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
				if ($receive_pur == 2) $rate_text = "Avg. Rate BDT";
				else $rate_text = "Rate";
				?>

			</tr>
			<tr style="font-size:14px">
				<td><strong>Gate Entry No:</strong></td>
				<td><? echo $dataArray[0][csf('gate_entry_no')]; ?></td>
				<td><strong>Gate Entry Date:</strong></td>
				<td><? echo change_date_format($dataArray[0][csf('gate_entry_date')]); ?></td>
				<td>&nbsp</strong></td>
				<td>&nbsp<? //echo change_date_format($dataArray[0][csf('challan_date')]); 
							?></td>
			</tr>

		</table>
		<br>
		<div style="width:100%;">
			<table align="right" cellspacing="0" width="<? echo $table_width; ?>" border="1" rules="all" class="rpt_table">
				<thead bgcolor="#dddddd" align="center" style="font-size:12px">
					<tr>
						<th width="30">SL</th>
						<th width="100">WO/PI No</th>
						<!-- <th width="100">Buyer</th> -->
						<th width="140">Item Details</th>

						<th width="60">Yarn Lot</th>
						<th width="40">UOM</th>
						<th width="60">Receive Qty</th>
						<th width="50">Rate</th>
						<th width="150">Amount</th>
						<th width="50">No. Of Bag</th>
						<th width="50">Cons Per Bag</th>
						<th width="50">Loose Cone</th>
						<th width="100">Weight per Bag</th>
						<th width="100">Loose Bag Weight</th>
						<th width="100">Wght @ Cone</th>
					</tr>
				</thead>
				<?
				if ($db_type == 0) $wo_no_cond = " group_concat(b.work_order_no)";
				else if ($db_type == 2) $wo_no_cond = "LISTAGG(b.work_order_no, ',') WITHIN GROUP (ORDER BY b.work_order_no)";
				$pi_arr = array();
				$pi_sql = "select a.id, a.pi_number, a.pi_basis_id, $wo_no_cond as work_order_no from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and a.item_category_id=1 group by a.id, a.pi_number, a.pi_basis_id";
				$pi_sql_res = sql_select($pi_sql);
				foreach ($pi_sql_res as $row) {
					if ($row[csf('pi_basis_id')] == 1) $wowoderno = implode(',', array_unique(explode(',', $row[csf('work_order_no')])));
					else if ($row[csf('pi_basis_id')] == 2) $wowoderno = "Independent";
					else $wowoderno = "";
					$pi_arr[$row[csf('id')]]['pi_number'] = $row[csf('pi_number')];
					$pi_arr[$row[csf('id')]]['work_order'] = $wowoderno;
				}

				$wo_library = return_library_array("select id,wo_number from wo_non_order_info_mst where entry_form=144", "id", "wo_number");

				//$wo_yrn_library = return_library_array("select id, ydw_no from wo_yarn_dyeing_mst", "id", "ydw_no");

				if ($wo_id != "") {
					$wo_yarn_sql = "select a.id,a.ydw_no,b.count,b.yarn_color, b.color_range from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.id=$wo_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";

					$wo_yarn_data = sql_select($wo_yarn_sql);
					$wo_yarn_data_array = array();
					foreach ($wo_yarn_data as $row) {
						$wo_yarn_data_array[$row[csf('id')]][$row[csf('count')]][$row[csf('yarn_color')]]['color_range'] = $row[csf('color_range')];
						$wo_yrn_library[$row[csf('id')]] = $row[csf('ydw_no')];
					}
				}
				$yarn_count_library = return_library_array("select id,yarn_count from lib_yarn_count where status_active=1 and is_deleted=0", 'id', 'yarn_count');
				$color_library = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
				$buyer_arr = return_library_array("select id, buyer_name from lib_buyer", 'id', 'buyer_name');
				$cond = "";
				if ($data[1] != "") $cond .= " and a.recv_number='$data[1]'";

				$i = 1;
				// $sql = "select a.recv_number, a.receive_basis, a.receive_purpose, b.id, b.receive_basis, b.pi_wo_batch_no, b.cone_per_bag, c.product_name_details, c.lot,c.yarn_count_id,c.color, b.order_uom, b.order_qnty, b.order_rate, b.cons_avg_rate, b.dye_charge, b.order_ile_cost, b.order_amount, b.cons_amount, b.no_of_bags,b.no_loose_cone, b.remarks,b.buyer_id,b.booking_no,a.audit_by, a.audit_date, a.is_audited, b.floor_id, b.room
				// from inv_receive_master a, inv_transaction b,  product_details_master c
				// where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and b.item_category=1 and a.entry_form=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $cond";
				$sql = "select a.buyer_id, a.receive_basis, b.wo_pi_no, b.lot, b.uom, b.parking_quantity as recv_qty, b.rate, b.amount, b.no_of_bag, b.cone_per_bag, b.lose_cone, b.weight_per_bag, b.weight_cone, b.yarn_count, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_type, b.color_name, sum(c.LOOSE_BAG_WEIGHT) as loose_bag_weight from inv_receive_master a, quarantine_parking_dtls b left join QUARANTINE_PAR_DTLS_LOOSE_RFID c on b.id = c.dtls_id where a.status_active=1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted=0 and a.id = b.mst_id $cond group by  a.buyer_id, a.receive_basis, b.wo_pi_no, b.lot, b.uom, b.parking_quantity, b.rate, b.amount, b.no_of_bag, b.cone_per_bag, b.lose_cone, b.weight_per_bag, b.weight_cone, b.yarn_count, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_type, b.color_name";
				// echo $sql; die;
				$sql_result = sql_select($sql);
				// echo "<pre>";
				// print_r($sql_result);
				$total_amt_currency = 0;
				foreach ($sql_result as $row) {
					$item_details = $yarn_count_library[$row["YARN_COUNT"]] . " " . $composition[$row["YARN_COMP_TYPE1ST"]] . " " . $row["YARN_COMP_PERCENT1ST"] . "% " . $yarn_type[$row["YARN_TYPE"]] . " " . $color_library[$row["COLOR_NAME"]];
					$loose_bag_weight = $row['LOOSE_BAG_WEIGHT'];
					$recv_qty = $row["RECV_QTY"];
					$weight_per_bag = $row["WEIGHT_PER_BAG"];
					$no_of_bag = $row["NO_OF_BAG"];
					if($loose_bag_weight == '' || $loose_bag_weight == 0)
					{
						$loose_bag_weight = $recv_qty - ($no_of_bag * $weight_per_bag);
					}
					if ($i % 2 == 0) $bgcolor = "#E9F3FF";
					else $bgcolor = "#FFFFFF";
					//order amount calculation here
				?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="font-size:12px">
						<td><? echo $i; ?></td>
						<td align="center"><?= $row[csf('wo_pi_no')] ?></td>
						<!-- <td align="center"><? //= $buyer_arr[$row[csf('buyer_id')]]
												?></td> -->
						<td align="center"><?= $item_details ?></td>
						<td align="center"><?= $row[csf('lot')] ?></td>
						<td align="center"><?= $unit_of_measurement[$row[csf('uom')]] ?></td>
						<td align="center"><?= $row[csf('recv_qty')] ?></td>
						<td align="center"><?= $row[csf('rate')] ?></td>
						<td align="center"><?= $row[csf('amount')] ?></td>
						<td align="center"><?= $row[csf('no_of_bag')] ?></td>
						<td align="center"><?= $row[csf('cone_per_bag')] ?></td>
						<td align="center"><?= $row[csf('lose_cone')] ?></td>
						<td align="center"><?= $row[csf('weight_per_bag')] ?></td>
						<td align="center"><?= $loose_bag_weight ?></td>
						<td align="center"><?= number_format($row[csf('weight_cone')], 2, '.', '') ?></td>
					</tr>

				<?php
					$i++;
				}
				?>
				<!-- tfoot here  -->

			</table>

			<br>
			<?
			echo signature_table(65, $data[0], $table_width . "px");
			?>
		</div>
	</div>
	<script type="text/javascript" src="../../js/jquery.js"></script>
	<script type="text/javascript" src="../../js/jquery.qrcode.min.js"></script>
	<script>
		var main_value = '<? echo $data[1]; ?>';
		//alert(main_value);
		$('#qrcode').qrcode(main_value);
	</script>
<?
	exit();
}




?>