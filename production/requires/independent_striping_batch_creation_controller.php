
<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

include('../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

if ($action == "load_drop_machine")
{
	if($db_type==2)
	{
		echo create_drop_down( "cbo_machine_name", 144, "select id,machine_no || '-' || brand as machine_name from lib_machine_name where category_id=2 and floor_id=$data and status_active=1 and is_deleted=0 and is_locked=0 order by seq_no","id,machine_name", 1,"-- Select Machine --", $selected, "","" );

	}
	else if($db_type==0)
	{
		echo create_drop_down( "cbo_machine_name", 144, "select id,concat(machine_no,'-',brand) as machine_name from lib_machine_name where category_id=2 and floor_id=$data and status_active=1 and is_deleted=0 and is_locked=0 order by  seq_no","id,machine_name", 1, "-- Select Machine --", $selected, "","" );
	}
}

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor", 144, "select id,floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and production_process=3 and company_id='$data' order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "load_drop_down( 'requires/independent_striping_batch_creation_controller',this.value, 'load_drop_machine', 'td_dyeing_machine' );",0 );

	exit();
}

if ($action == "fabricBooking_popup")
{
	echo load_html_head_contents("WO Info", "../../", 1, 1, '', '', '', 1);
	extract($_REQUEST);
	?>
	<script>
		$(document).on("click", ".view_order", function (e) {
			e.preventDefault();
			var job_no = $(this).attr("data-job");
			var company_id = $(this).attr("data-company");
			dhtmlmodal.open('EmailBox', 'iframe', 'independent_striping_batch_creation_controller.php?company_id=' + company_id + '&job_no=' + job_no + '&action=order_popup', 'Order Popup', 'width=640px,height=350px,center=1,resize=1,scrolling=0', '')
		});

		$(document).on("click", "#order_tbl table tbody tr td:not(:last-child)", function (e) {
			var thisAttrValues = $(this).parent("tr").attr("data-values");
			var dataValues = thisAttrValues.split("##");
			$('#hidden_booking_id').val(dataValues[0]);
			$('#hidden_booking_no').val(dataValues[1]);
			$('#hidden_color_id').val(dataValues[2]);
			$('#hidden_color').val(dataValues[3]);
			$('#hidden_job_no').val(dataValues[4]);
			$('#booking_without_order').val(dataValues[6]);
			$('#hidden_sales_booking_no').val(dataValues[5]);
			$('#hidden_search_type').val(dataValues[7]);
			$('#hidden_within_group').val(dataValues[8]);
			$('#hidden_sales_id').val(dataValues[9]);
			$('#hidden_sales_remarks').val(dataValues[10]);
			$('#hidden_is_sales').val(dataValues[11]);
			$('#hidden_color_type').val(dataValues[12]);
			parent.emailwindow.hide();
		});

		function js_set_value(booking_id, booking_no, color_id, color, job_no, sales_booking_no, type, search_by,color_type, entry_form){

			$('#hidden_booking_id').val(booking_id);
			$('#hidden_booking_no').val(booking_no);
			$('#hidden_color_id').val(color_id);
			$('#hidden_color').val(color);
			$('#hidden_job_no').val(job_no);
			$('#booking_without_order').val(type);
			$('#hidden_sales_booking_no').val(sales_booking_no);
			$('#hidden_search_type').val(search_by);
			$('#hidden_color_type').val(color_type);
			$('#hidden_entry_form').val(entry_form);
			parent.emailwindow.hide();
		}
		function field_visible(thisValue) {
			$("#chkIsSales").prop("checked", false);
			if (thisValue == 1 || thisValue == 5) {
				$("#is_sales_booking").css("display", "block");
			} else {
				$("#is_sales_booking").css("display", "none");
			}
		}

		/*var sales_batch_flag = "<?php //echo $sales_batch_flag; ?>";
		if (sales_batch_flag==1) 
		{
			$("#chkIsSales").prop("checked", true);
		}*/
	</script>
	</head>

	<body>
		<div align="center" style="width:962px;">
			<form name="searchwofrm" id="searchwofrm" autocomplete=off>
				<fieldset style="width:100%;">
					<legend>Enter search words</legend>
					<table cellpadding="0" cellspacing="0" width="840" class="rpt_table" border="1" rules="all">
						<thead>
							<th>Booking Year</th>
							<th>Buyer</th>
							<th>Booking Type</th>
							<th>Fabric Source</th>
							<th>Search By</th>
							<th id="search_by_td_up"><?php echo ($batch_against == 7) ? "Enter Sales Order No" : "Enter Booking No"; ?></th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton"/>
								<input type="hidden" name="txt_company_id" id="txt_company_id" class="text_boxes" value="<? echo $cbo_company_id; ?>">
								<input type="hidden" name="hidden_booking_id" id="hidden_booking_id" class="text_boxes" value="">
								<input type="hidden" name="hidden_booking_no" id="hidden_booking_no" class="text_boxes" value="">
								<input type="hidden" name="hidden_color_id" id="hidden_color_id" class="text_boxes" value="">
								<input type="hidden" name="hidden_color" id="hidden_color" class="text_boxes" value="">
								<input type="hidden" name="hidden_job_no" id="hidden_job_no" class="text_boxes" value="">
								<input type="hidden" name="booking_without_order" id="booking_without_order" class="text_boxes" value="">
								<input type="hidden" name="hidden_sales_booking_no" id="hidden_sales_booking_no" class="text_boxes" value="">
								<input type="hidden" name="hidden_search_type" id="hidden_search_type" class="text_boxes" value="">
								<input type="hidden" name="hidden_within_group" id="hidden_within_group" class="text_boxes" value="">
								<input type="hidden" name="hidden_sales_id" id="hidden_sales_id" class="text_boxes" value="">
								<input type="hidden" name="hidden_color_type" id="hidden_color_type" class="text_boxes" value="">
								<input type="hidden" name="hidden_entry_form" id="hidden_entry_form" class="text_boxes" value="">
								<input type="hidden" name="hidden_sales_remarks" id="hidden_sales_remarks" class="text_boxes" value="">
								<input type="hidden" name="hidden_is_sales" id="hidden_is_sales" class="text_boxes" value="">
							</th>
						</thead>
						<tr>
							<td align="center">
								<?
								echo create_drop_down( "cbo_year", 60, create_year_array(),"", 1,"-- All --", date("Y",time()), "",0,"" );
								?>
							</td>
							<td align="center">
								<?
								echo create_drop_down("cbo_buyer_name", 170, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$cbo_company_id' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name", "id,buyer_name", 1, "-- All Buyer --", $selected, "", $data[0]);
								?>
							</td>
							<td align="center">
								<?
								if ($batch_against == 3) {
									$disabled = 0;
								} else {
									$disabled = 1;
								}


								$booking_type = array(1 => "With Order", 2 => "Without Order");
								echo create_drop_down("cbo_booking_type", 140, $booking_type, "", 0, "-- All --", '', '', $disabled);
								?>
							</td>
							<td align="center">
								<?
								echo create_drop_down("cbo_fabric_source", 140, $fabric_source, "", 0, "-- All --", '', '', "","","","","4");
								?>
							</td>
							<td align="center">
								<?
								if ($batch_against == 7) 
								{
									$disabled = 1;
									$selected = 7;
								} 
								else 
								{
									$disabled = 0;
									$selected = 1;
								}
								if ($sales_batch_flag==1) 
								{
									$disabled = 1;
									$selected = 7;
									$checked = 'checked="" disabled=""';
									$search_by_arr = array(1 => "Booking No", 2 => "Buyer Order", 3 => "Job No", 4 => "Booking Date", 5 => "Internal Ref.", 6 => "File No", 7 => "Sales Order", 8 => "Style Ref.");
								}
								else
								{
									$checked = 'disabled=""';
									$search_by_arr = array(1 => "Booking No", 2 => "Buyer Order", 3 => "Job No", 4 => "Booking Date", 5 => "Internal Ref.", 6 => "File No", 8 => "Style Ref.");
								}
								
								$dd = "change_search_event(this.value, '0*0*0*3*0*0*0*0', '0*0*0*3*0*0*0*0', '../../');field_visible(this.value);";
								echo create_drop_down("cbo_search_by", 150, $search_by_arr, "", 0, "--Select--", $selected, $dd, $disabled);
								?>
							</td>
							<td align="center">
								<div id="search_by_td">
									<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
									id="txt_search_common"/>
								</div>
								<div id="is_sales_booking"><input type="checkbox" name="chkIsSales" id="chkIsSales" <? echo $checked; ?> /> <label
									for="chkIsSales">For sales order </label></div>
								</td>
								<td align="center">
									<input type="button" name="button2" class="formbutton" value="Show"
									onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+document.getElementById('txt_company_id').value+'_'+document.getElementById('cbo_buyer_name').value+'_'+'<? echo $batch_against; ?>'+'_'+document.getElementById('cbo_booking_type').value+'_'+document.getElementById('chkIsSales').checked+'_'+document.getElementById('cbo_year').value+'_'+document.getElementById('cbo_fabric_source').value+'_'+'<? echo $sales_batch_flag; ?>', 'create_booking_search_list_view', 'search_div', 'independent_striping_batch_creation_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
									style="width:100px;"/>
								</td>
							</tr>
						</table>
						<div style="width:100%; margin-top:10px; margin-left:3px" id="search_div" align="left"></div>
					</fieldset>
				</form>
			</div>
		</body>
		<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
		</html>
		<?
		exit();
}

if ($action == "create_booking_search_list_view")
{
	$data = explode("_", $data);
	$search_string 		= "%" . trim($data[0]) . "%";
	$search_by 			= $data[1];
	$company_id 		= $data[2];
	$buyer_id 			= $data[3];
	$batch_against 		= $data[4];
	$booking_type 		= $data[5];
	$is_sales_booking 	= $data[6];
	$booking_year 		= $data[7];
	$cbo_fabric_source	= $data[8];
	$sales_batch_flag	= $data[9];



	if ($buyer_id == 0) {
		if ($_SESSION['logic_erp']["data_level_secured"] == 1) {
			if ($_SESSION['logic_erp']["buyer_id"] > 0) $buyer_id_cond = " and a.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")"; else $buyer_id_cond = "";
			if ($_SESSION['logic_erp']["buyer_id"] > 0) $buyer_id_samp_cond = " and s.buyer_id in (" . $_SESSION['logic_erp']["buyer_id"] . ")"; else $buyer_id_samp_cond = "";
		} else {
			$buyer_id_cond = "";
			$buyer_id_samp_cond = "";
		}
	} else {
		$buyer_id_cond 		= " and a.buyer_id=$buyer_id";
		$buyer_id_samp_cond = " and s.buyer_id=$buyer_id";
	}

	if (trim($data[0]) != "") {
		if ($search_by == 1)
		{
			$search_field_cond = "and a.booking_no_prefix_num =". trim($data[0]) ."";
		}
		else if ($search_by == 2)
		{
			$search_field_cond = "and c.po_number like '$search_string'";
		}
		else if ($search_by == 3)
		{
			$search_field_cond = "and d.job_no_prefix_num = ". trim($data[0]) ."";
		}
		else if ($search_by == 5)
		{
			$search_field_cond = "and c.grouping like '$search_string'";
		}
		else if ($search_by == 6)
		{
			$search_field_cond = "and c.file_no like '$search_string'";
		}
		else if ($search_by == 8)
		{
			$search_field_cond = "and d.style_ref_no like '$search_string'";
		}
		else {
			if ($db_type == 0) {
				$search_field_cond = "and a.booking_date like '" . change_date_format(trim($data[0]), "yyyy-mm-dd", "-") . "'";
			} else {
				$search_field_cond = "and a.booking_date like '" . change_date_format(trim($data[0]), '', '', 1) . "'";
			}
		}
	} else {
		$search_field_cond = "";
	}

	$color_arr = return_library_array("select id,color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');

	//Without Order
	if ($booking_type == 2 && $batch_against == 3 && $is_sales_booking == "false" && $search_by != 7)
	{
		$buyer_arr = return_library_array("select id,short_name from lib_buyer", 'id', 'short_name');

		if(trim($data[0]) != "")
		{
			if ($search_by == 1)
			{
				$search_field_cond_sample = "and s.booking_no_prefix_num = ". trim($data[0]) ."";
			}
			else if ($search_by == 4) {
				if ($db_type == 0) {
					$search_field_cond_sample = "and s.booking_date like '" . change_date_format(trim($data[0]), "yyyy-mm-dd", "-") . "'";
				} else {
					$search_field_cond_sample = "and s.booking_date like '" . change_date_format(trim($data[0]), '', '', 1) . "'";
				}
			} else
			$search_field_cond_sample = "";
		}

		$booking_year_cond="";
		if($booking_year>0)
		{
			if ($db_type == 0)
			{
				$booking_year_cond=" and YEAR(s.booking_date)=$booking_year";
			}
			else if ($db_type == 2)
			{
				$booking_year_cond=" and to_char(s.booking_date,'YYYY')=$booking_year";
			}
		}

		$sql = "SELECT s.id, s.booking_no, s.booking_date, s.buyer_id, f.style_des, f.body_part, f.fabric_description, f.color_type_id, null as job_no_mst, s.entry_form_id, 1 as types, (case when  f.fabric_color is null or f.fabric_color<=0 then f.fabric_color else f.fabric_color end )  as fabric_color_id, f.fabric_color,s.booking_type,s.is_short
		FROM wo_non_ord_samp_booking_mst s, wo_non_ord_samp_booking_dtls f
		WHERE s.booking_no=f.booking_no  and s.company_id=$company_id and s.status_active =1 and s.is_deleted =0 and f.status_active =1 and f.is_deleted =0 and s.item_category=2 and (s.fabric_source='$cbo_fabric_source' or f.fabric_source='$cbo_fabric_source') $buyer_id_samp_cond $search_field_cond_sample $booking_year_cond order by f.fabric_color";
		// echo $sql;
		$result = sql_select($sql);
		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1070" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="115">Booking No</th>
				<th width="100">Booking Type</th>
				<th width="75">Booking Date</th>
				<th width="60">Buyer</th>
				<th width="90">Color</th>
				<th width="90">Color Type</th>
				<th width="50">Without Order</th>
				<th width="145">Style Desc.</th>
				<th width="85">Body Part</th>
				<th>Construction/ Composition</th>
			</thead>
		</table>
		<div style="width:1090px; max-height:270px; overflow-y:scroll" id="list_container_batch" align="left">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1070" class="rpt_table"	id="tbl_list_search">
				<?
				$i = 1;
				foreach ($result as $row) 
				{
					if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

					if($row[csf('entry_form_id')] == 140 )
					{
						$fabric_color_id = $row[csf('fabric_color_id')];
					}else{
						$fabric_color_id = $row[csf('fabric_color')];
					}

					if($row[csf('booking_type')] ==4)
					{
						$booking_type_text="Sample";
					}
					else
					{
						$booking_type_text="";
					}
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
						onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('booking_no')]; ?>','<? echo $fabric_color_id; ?>','<? echo $color_arr[$fabric_color_id]; ?>','<? echo $row[csf('job_no_mst')]; ?>','',<? echo 1; ?>,'<? echo $search_by; ?>','<? echo $row[csf('color_type_id')];?>','<? echo $row[csf('entry_form_id')]?>');">
						<td width="30"><? echo $i; ?></td>
						<td width="115"><p><? echo $row[csf('booking_no')]; ?></p></td>
						<td width="100"><p><? //echo $row[csf('booking_no')]; ?></p></td>
						<td width="75" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
						<td width="60"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
						<td width="90"><p><? echo $color_arr[$fabric_color_id]; ?>&nbsp;</p></td>
						<td width="90"><p><? echo $color_type[$row[csf('color_type_id')]]; ?>&nbsp;</p></td>
						<td width="50" align="center"><? echo "Yes"; ?></td>
						<td width="145"><p><? echo $row[csf('style_des')]; ?>&nbsp;</p></td>
						<td width="85"><p><? echo $body_part[$row[csf('body_part')]]; ?>&nbsp;</p></td>

						<td><p><? echo $row[csf('fabric_description')]; ?>&nbsp;</p></td>
					</tr>
					<?
					$i++;
				}
				?>
			</table>
		</div>
		<?
	}
	else if ($search_by == 7 || $is_sales_booking == "true")
	{
		// if search by sales order or booking or Internal Ref. agains sales order
		$search_string=trim($data[0]);
		if ($search_by==5 && $search_string!="")
		{
			$po_sql="SELECT a.id, a.po_number, a.grouping, b.booking_no, b.booking_mst_id from wo_po_break_down a, wo_booking_dtls b 
			where a.id=b.po_break_down_id and a.is_deleted=0 and a.status_active=1 and a.grouping ='$search_string' and b.booking_type in(1,4) and b.status_active=1 and b.is_deleted=0";
			// echo $po_sql;die;
			$po_sql_result=sql_select($po_sql);
			$refBooking_cond="";
			foreach ($po_sql_result as $key => $row) 
			{
				//$po_id_arr[$row[csf('id')]]=$row[csf('id')];
				$bookingNo_arr[$row[csf('booking_mst_id')]] = $row[csf('booking_mst_id')];
			}
		}

		$buyer_arr = return_library_array("select id,buyer_name from lib_buyer", 'id', 'buyer_name');
		$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
		if ($search_by==1 && $is_sales_booking == "true") 
		{
			$sales_job_cond = " and s.sales_booking_no LIKE trim('%$data[0]%')";
		} 
		elseif ($search_by==5 && $is_sales_booking == "true") 
		{
			$sales_job_cond=" and s.booking_id in(".implode(",",$bookingNo_arr).") ";
		}
		else 
		{
			$sales_job_cond = " and s.job_no LIKE trim('%$data[0]%')";
		}

		$booking_year_cond="";
		if($booking_year>0)
		{
			if ($db_type == 0)
			{
				$booking_year_cond=" and YEAR(s.booking_date)=$booking_year";
			}
			else if ($db_type == 2)
			{
				$booking_year_cond=" and to_char(s.booking_date,'YYYY')=$booking_year";
			}
		}

		$sql = "SELECT s.id, sd.mst_id,s.job_no, s.company_id, s.sales_booking_no, s.booking_date, s.within_group, s.buyer_id, s.style_ref_no, s.remarks, s.booking_without_order, sd.color_id, sd.color_type_id,s.booking_type, s.po_job_no, s.po_buyer, a.is_short 
		from fabric_sales_order_mst s left join wo_booking_mst a on s.sales_booking_no=a.booking_no and s.booking_without_order =0 and s.within_group=1,fabric_sales_order_dtls sd 
		where s.id = sd.mst_id and s.company_id = $company_id $sales_job_cond $booking_year_cond and s.status_active=1 and s.is_deleted=0 and sd.status_active=1 and sd.is_deleted=0 
		group by s.id, sd.mst_id, s.job_no, s.company_id, s.sales_booking_no, sd.color_type_id, s.booking_date, s.within_group, s.buyer_id, s.style_ref_no, s.remarks, s.booking_without_order, sd.color_id,s.booking_type, s.po_job_no, s.po_buyer, a.is_short order by s.booking_date desc";
		// echo $sql;
		$result = sql_select($sql);
		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" width="1120">
			<thead>
				<th width="30">SL</th>
				<th width="115">Booking No</th>
				<th width="100">Booking Type</th>
				<th width="75">Booking Date</th>
				<th width="60">Buyer</th>
				<th width="115">Sales Order No</th>
				<th width="70">Within Group</th>
				<th width="115">Job No</th>
				<th width="100">Style Ref.ee</th>
				<th width="70">Color</th>
				<th width="90">Color Type</th>
				<th>Buyer Order</th>
			</thead>
		</table>
		<div style="width:1140px; max-height:270px; overflow-y:scroll; text-align: center;" id="order_tbl">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1120" class="rpt_table"
			id="tbl_list_search">
			<?
			$i = 1;$search_by = 7;
			foreach ($result as $row) 
			{
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
				$job = $row[csf('po_job_no')];
				$within_group = $row[csf('within_group')];
				$buyer  = ($within_group == 1) ? $buyer_arr[$row[csf('po_buyer')]] : $buyer_arr[$row[csf('buyer_id')]];
				$job_no = ($within_group == 1 && $row[csf('booking_without_order')]*1 !=1) ? $row[csf('po_job_no')] : "";
				$style  =  $row[csf('style_ref_no')];

				if($row[csf('within_group')] == 1 && $row[csf('booking_type')] ==4)
				{
					$booking_type_text="Sample";
				}
				else if($row[csf('within_group')] == 1)
				{
					if($row[csf('is_short')]==1)
					{
						$booking_type_text="Short";
					}
					else
					{
						$booking_type_text="Main";

					}
				}
				else
				{
					$booking_type_text="";
				}

				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
					data-values="<? echo $row[csf('dtls_id')]; ?>##<? echo $row[csf('job_no')]; ?>##<? echo $row[csf('color_id')]; ?>##<? echo $color_arr[$row[csf('color_id')]]; ?>##''##<? echo $row[csf('sales_booking_no')]; ?>##0##<? echo $search_by; ?>##<? echo $row[csf('within_group')]; ?>##<? echo $row[csf('id')]; ?>##<? echo $row[csf('remarks')];?>##1##<? echo $row[csf('color_type_id')];?>">
					<td width="30"><? echo $i; ?></td>
					<td width="115"><? echo $row[csf('sales_booking_no')]; ?></td>
					<td width="100"><? echo $booking_type_text; ?></td>
					<td width="75" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
					<td width="60"><? echo $buyer; ?></td>
					<td width="115" align="center"><? echo $row[csf('job_no')]; ?></td>
					<td width="70" align="center"><? echo ($row[csf('within_group')] == 1) ? "Yes" : "No"; ?></td>
					<td width="115" align="center"><? echo $job_no; ?></td>
					<td width="100"><? echo $style; ?></td>
					<td width="70"><? echo $color_arr[$row[csf('color_id')]]; ?></td>
					<td width="90"><? echo $color_type[$row[csf('color_type_id')]]; ?></td>
					<td>
						<?php if ($job != "") { ?>
							<A href='#' data-job="<? echo $job; ?>" data-company="<? echo $company_id; ?>"
								class="view_order">view</A>
							<?php } ?>
					</td>
				</tr>
				<?
				$i++;
			}
			?>
			</table>
		</div>
		<?
	}
	else
	{
		$booking_year_cond="";
		if($booking_year>0)
		{
			if ($db_type == 0)
			{
				$booking_year_cond=" and YEAR(a.booking_date)=$booking_year";
			}
			else if ($db_type == 2)
			{
				$booking_year_cond=" and to_char(a.booking_date,'YYYY')=$booking_year";
			}
		}

		if($batch_against==3)
		{
			$booking_type_cond = " and a.booking_type=4";
		}
		else if($batch_against==1){
			$booking_type_cond = " and a.booking_type=1";
		}

		$buyer_arr = return_library_array("select id,buyer_name from lib_buyer", 'id', 'buyer_name');
		$sql = "select a.id, a.booking_no, a.booking_date, a.buyer_id, b.fabric_color_id,b.color_type, c.job_no_mst,c.id po_id,c.po_number,c.file_no,c.grouping, 0 as type,d.style_ref_no, a.entry_form,a.booking_type,a.is_short from wo_booking_mst a, wo_booking_dtls b, wo_po_break_down c,wo_po_details_master d where a.booking_no=b.booking_no and b.po_break_down_id=c.id and c.job_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted =0 and a.item_category=2 and a.company_id=$company_id $buyer_id_cond $search_field_cond $booking_type_cond $booking_year_cond and a.fabric_source='$cbo_fabric_source' and d.status_active =1 and d.is_deleted=0 group by a.id, b.fabric_color_id,b.color_type, a.booking_no, a.booking_date, a.buyer_id, c.job_no_mst,c.id,c.po_number,c.file_no, c.grouping,d.style_ref_no, a.entry_form,a.booking_type,a.is_short order by a.booking_date desc";

		$result = sql_select($sql);
		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1120" class="rpt_table">
			<thead>
				<th width="30">SL</th>
				<th width="115">Booking No</th>
				<th width="100">Booking Type</th>
				<th width="75">Booking Date</th>
				<th width="100">Buyer</th>
				<th width="85">Job No</th>
				<th width="100">Style Ref.</th>
				<th width="70">Color</th>
				<th width="90">Color Type</th>
				<? if ($batch_against == 3) { ?>
				<th width="60">Without Order</th><? } ?>
				<th width="100">Internal Ref.</th>
				<th width="80">File No</th>
				<th>Buyer Order</th>
			</thead>
		</table>
		<div style="width:1140px; max-height:270px; overflow-y:scroll" id="list_container_batch" align="left">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1120" class="rpt_table" id="tbl_list_search">
				<?php $i = 1;
				foreach ($result as $row)
				{
					if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
					$po_no = $row[csf('po_number')];
					$intl_ref = $row[csf('grouping')];
					$file_no = $row[csf('file_no')];
					if($row[csf('booking_type')]==4)
					{
						$booking_type_text="Sample";
					}
					else
					{
						if($row[csf('is_short')]==1)
						{
							$booking_type_text="Short";
						}
						else
						{
							$booking_type_text="Main";

						}
					}
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
						onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('booking_no')]; ?>','<? echo $row[csf('fabric_color_id')]; ?>','<? echo $color_arr[$row[csf('fabric_color_id')]]; ?>','<? echo $row[csf('job_no_mst')]; ?>','','<? echo $row[csf('type')]; ?>','<? echo $search_by; ?>','<? echo $row[csf('color_type')]; ?>','<? echo $row[csf('entry_form')]?>');">
						<td width="30"><? echo $i; ?></td>
						<td width="115"><p><? echo $row[csf('booking_no')]; ?></p></td>
						<td width="100"><p><? echo $booking_type_text; ?></p></td>
						<td width="75" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
						<td width="100"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
						<td width="85" align="center"><p><? echo $row[csf('job_no_mst')]; ?>&nbsp;</p></td>
						<td width="100"><p><? echo $row[csf('style_ref_no')]; ?>&nbsp;</p></td>
						<td width="70"><p><? echo $color_arr[$row[csf('fabric_color_id')]]; ?>&nbsp;</p>
						</td>
						<td width="90"><p><? echo $color_type[$row[csf('color_type')]]; ?>&nbsp;</p>
						</td>
						<? if ($batch_against == 3) { ?>
						<td width="60"
						align="center"><? echo "No";// if($row[csf('type')]==0) echo "No"; else echo "Yes"; ?></td><? } ?>
						<td width="100"><p><? echo $intl_ref; ?>&nbsp;</p></td>
						<td width="80"><p><? echo $file_no; ?>&nbsp;</p></td>
						<td><p><? echo $po_no; ?>&nbsp;</p></td>
					</tr>
					<? $i++;
				} ?>
			</table>
		</div>
		<?
	}
	exit();
}

if ($action == "order_popup") // FSO Buyer Order View //  Fabric Sales/Booking No
{
	echo load_html_head_contents("Order Info", "../../", 1, 1, '', '1', '');
	extract($_REQUEST);
	$po_info = sql_select("select a.job_no,a.style_ref_no,b.po_number from wo_po_details_master a,wo_po_break_down b where a.id=b.job_id and a.company_name=$company_id and a.job_no='$job_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="100%" class="rpt_table" id="tbl_list_search">
		<thead>
			<th width="30">SL</th>
			<th width="115">Job No</th>
			<th width="75">Style Reference No</th>
			<th width="60">PO Number</th>
		</thead>
		<tbody>
			<?php
			$i = 1;
			foreach ($po_info as $row) {
				?>
				<tr bgcolor="<? echo $bgcolor; ?>">
					<td width="30" align="center"><? echo $i; ?></td>
					<td width="115" align="center"><? echo $row[csf('job_no')]; ?></td>
					<td width="75" align="center"><? echo $row[csf('style_ref_no')]; ?></td>
					<td width="60" align="center"><? echo $row[csf('po_number')]; ?></td>
				</tr>
				<?php
				$i++;
			}
			?>
		</tbody>
	</table>
	<?php
	exit();
}

if ($action == "save_update_delete")
{
	$process = array(&$_POST);
	extract(check_magic_quote_gpc($process));

	$product_array 	= return_library_array("select id, product_name_details from product_details_master where item_category_id=13", 'id', 'product_name_details');
	$color_arr 		= return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');

	/*
	|--------------------------------------------------------------------------
	| Insert
	|--------------------------------------------------------------------------
	|
	*/
	if ($operation == 0)
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		$batch_update_id 	= '';
		$roll_maintained 	= str_replace("'", "", $roll_maintained);
		$txt_search_type 	= str_replace("'", "", $txt_search_type);

		$color_id=$txt_new_batch_color_id;
		if (str_replace("'", "", $update_id) == "")
		{
			/*
			|--------------------------------------------------------------------------
			| pro_batch_create_mst
			| data preparing for
			| $data_array
			|--------------------------------------------------------------------------
			|
			*/

			if($db_type==0) $year_cond="YEAR(insert_date)";
			else if($db_type==2) $year_cond="to_char(insert_date,'YYYY')";
			else $year_cond="";//defined Later

			$system_entry_form=639; $prefix='ISBC';
			$new_batch_sl_system_id = explode("*", return_next_id_by_sequence("PRO_BATCH_CREATE_MST_PK_SEQ", "pro_batch_create_mst",$con,1,$cbo_company_id,$prefix,$system_entry_form,date("Y",time()),13 ));

			//echo "10**"; print_r($new_batch_sl_system_id); die;


			$id = return_next_id_by_sequence("PRO_BATCH_CREATE_MST_PK_SEQ", "pro_batch_create_mst", $con);
			$batch_update_id = $id;
			//$serial_no = date("y", strtotime($pc_date_time)) . "-" . $id;
			$serial_no = $new_batch_sl_system_id[0];
			
			/*
			|--------------------------------------------------------------------------
			| pro_batch_create_mst
			| duplicate checking
			|--------------------------------------------------------------------------
			|
			*/
			if (is_duplicate_field("batch_no", "pro_batch_create_mst", "batch_no=$txt_new_batch_number and and status_active=1 and is_deleted=0") == 1)
			{
				echo "11**0";
				disconnect($con);
				die;
			}
			$txt_new_batch_number = $txt_new_batch_number;

			$sales_order_no = ($txt_search_type == 7) ? $txt_booking_no : "''";
			$txt_booking_no = ($txt_search_type != 7) ? $txt_booking_no : $txt_sales_booking_no;
			$txt_sales_id = ($txt_search_type == 7) ? str_replace("'", "", $txt_sales_id) : "''";
			$is_sales = ($txt_search_type == 7) ? 1 : 0;
			$data_array = "(" . $id . "," . $txt_new_batch_number . "," . $hiddden_from_batch_id . "," . $txt_from_batch_number . "," . $txt_batch_date . "," . $system_entry_form . "," . $cbo_batch_against . "," . $cbo_company_id . "," . $cbo_working_company_id . "," . $txt_booking_no_id . "," . $txt_booking_no . "," . $booking_without_order . "," . $txt_new_batch_color_id . "," . $txt_batch_color_id . "," . $txt_batch_weight . "," . $txt_tot_trims_weight . "," . $save_data . "," . $cbo_color_range . "," . $txt_process_id . "," . $txt_du_req_hr . "," . $txt_du_req_min . "," . $txt_collar_qty . "," . $txt_cuff_qty . "," . $cbo_floor . "," . $cbo_machine_name . "," . $txt_remarks . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $sales_order_no . "," . $txt_sales_id . "," . $is_sales . "," . $txt_process_seq .",". $hidden_booking_entry_form.",". $cbo_double_dyeing . ",'" . $new_batch_sl_system_id[1] ."',".$new_batch_sl_system_id[2] .",'".$new_batch_sl_system_id[0]."',".$cbo_shift_name .",".$txt_dyeing_pdo .")";

			//$new_batch_sl_system_id[1] .",".$new_batch_sl_system_id[2] .",".$new_batch_sl_system_id[0]
			//batch_sl_prefix, batch_sl_prefix_num, batch_sl_no
		}
		else
		{
			$batch_update_id = str_replace("'", "", $update_id);
			$serial_no = str_replace("'", "", $txt_batch_sl_no);

			/*
			|--------------------------------------------------------------------------
			| pro_batch_create_mst
			| duplicate checking
			|--------------------------------------------------------------------------
			|
			*/
			if (is_duplicate_field("batch_no", "pro_batch_create_mst", "batch_no=$txt_new_batch_number and and id<>$update_id and status_active=1 and is_deleted=0") == 1)
			{
				echo "11**0";
				disconnect($con);
				die;
			}

			/*
			|--------------------------------------------------------------------------
			| pro_batch_create_mst
			| data preparing for
			| $data_array_update
			|--------------------------------------------------------------------------
			|
			*/
			$data_array_update = $txt_new_batch_number . "*" . $txt_batch_date . "*" . $cbo_batch_against . "*" . $cbo_company_id . "*" . $cbo_working_company_id . "*" . $txt_booking_no_id . "*" . $txt_booking_no . "*" . $booking_without_order . "*" . $color_id . "*" . $txt_batch_weight . "*" . $txt_tot_trims_weight . "*" . $save_data . "*" . $cbo_color_range . "*" . $txt_process_id . "*" . $txt_du_req_hr . "*" . $txt_du_req_min . "*" . $txt_collar_qty . "*" . $txt_cuff_qty . "*" . $cbo_floor . "*" . $cbo_machine_name . "*" . $txt_remarks ."*" . $txt_process_seq. "*". $hidden_booking_entry_form ."*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*".$cbo_double_dyeing."*".$cbo_shift_name."*".$txt_dyeing_pdo;
		}


		$roll_table_id = '';
		for ($i = 1; $i <= $total_row; $i++)
		{
			$id_dtls = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
			$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);

			$program_no = "programNo_" . $i;
			$po_id = "poId_" . $i;
			$prod_id = "productId_" . $i;
			$body_part_id = "bodyPartId_" . $i;
			$cboDiaWidthType = "cboDiaWidthType_" . $i;
			$txtRollNo = "txtRollNo_" . $i;
			$hideRollNo = "rollId_" . $i;
			$barcodeNo = "barcodeNo_" . $i;
			$txtBatchQnty = "txtBatchQnty_" . $i;
			$txtQtyPcs = "txtQtyPcs_" . $i;
			$txtSize = "txtSize_" . $i;
			$txtPoBatchNo = "txtPoBatchNo_" . $i;
			$txtRemarks = "txtRemarks_" . $i;
			$cboColorType = "cboColorType_" . $i;
			$isSalesOrder = "isSalesOrder_" . $i;
			$fromBatchDtlsId = "fromBatchDtlsId_" . $i;
			$ItemDesc = $product_array[str_replace("'", "", $$prod_id)];

			$is_sales = ($txt_search_type == 7) ? 1 : 0;
			if (str_replace("'", "", $$hideRollNo) != "")
			{
				if (str_replace("'", "", $booking_without_order) == 1 && $is_sales != 1)
				{
					$bookingNo = str_replace("'", "", $txt_booking_no);
					$poId = str_replace("'", "", $txt_booking_no_id);
				}
				else
				{
					$bookingNo = '';
					$poId = str_replace("'", "", $$po_id);
				}

				/*
				|--------------------------------------------------------------------------
				| pro_roll_details
				| data preparing for
				| $data_array_roll
				|--------------------------------------------------------------------------
				|
				*/
				if ($data_array_roll != "")
					$data_array_roll .= ",";
				$data_array_roll .= "(" . $id_roll . "," . $batch_update_id . "," . $id_dtls . ",'" . $poId . "',639," . $$txtBatchQnty . "," . $$txtRollNo . "," . $$hideRollNo . "," . $$barcodeNo . ",'" . $bookingNo . "'," . $booking_without_order . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $is_sales . "," . $$txtQtyPcs . ")";

				//$all_barcode_nos_arr[str_replace("'", "", $$barcodeNo)]= str_replace("'", "", $$barcodeNo);
			}

			/*
			|--------------------------------------------------------------------------
			| pro_batch_create_dtls
			| data preparing for
			| $data_array_dtls
			|--------------------------------------------------------------------------
			|
			*/
			if ($data_array_dtls != "")
				$data_array_dtls .= ",";
			$is_sales = ($txt_search_type == 7) ? 1 : 0;
			$data_array_dtls .= "(" . $id_dtls . "," . $batch_update_id . "," . $$program_no . "," . $$po_id . "," . $$txtPoBatchNo . "," . $$prod_id . ",'" . $ItemDesc . "'," . $$body_part_id . "," . $$cboDiaWidthType . ",'" . $$txtRollNo . "','" . $$hideRollNo . "'," . $$barcodeNo . "," . $$txtBatchQnty . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $$isSalesOrder . "," . $$fromBatchDtlsId . "," . $$txtQtyPcs. "," . $$cboColorType. "," . $$txtSize. "," . $$txtRemarks . ")";
		}

		$save_string = explode("!!", str_replace("'", "", $save_data)); //Trims Weight String
		for ($i = 0; $i < count($save_string); $i++)
		{
			$id_dtls_trim = return_next_id_by_sequence("PRO_BATCH_TRIMS_DTLS_PK_SEQ", "pro_batch_trims_dtls", $con);
			$data = explode("_", $save_string[$i]);
			$item_des = $data[0];
			$trims_qty = $data[1];
			$remarks = $data[2];

			if ($trims_qty > 0)
			{
				/*
				|--------------------------------------------------------------------------
				| pro_batch_trims_dtls
				| data preparing for
				| $data_array_dtls_trims
				|--------------------------------------------------------------------------
				|
				*/
				if ($data_array_dtls_trims != "")
					$data_array_dtls_trims .= ",";
				$data_array_dtls_trims .= "(" . $id_dtls_trim . "," . $batch_update_id . ",'" . $item_des . "'," . $trims_qty . ",'" . $remarks . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',1,0)";
			}
		}

		$rID = true;
		$rID1 = true;
		$rID2 = true;
		$rID3 = true;
		$rID4 = true;

		if (str_replace("'", "", $update_id) == "")
		{
			/*
			|--------------------------------------------------------------------------
			| pro_batch_create_mst
			| data inserting
			|--------------------------------------------------------------------------
			|
			*/
			$field_array = "id, batch_no, from_batch_id, from_batch_no, batch_date, entry_form, batch_against, company_id,working_company_id, booking_no_id, booking_no, booking_without_order, color_id, from_color_id, batch_weight, total_trims_weight,save_string, color_range_id, process_id, dur_req_hr, dur_req_min, collar_qty, cuff_qty,floor_id, dyeing_machine, remarks, inserted_by, insert_date,sales_order_no,sales_order_id,is_sales,process_seq,booking_entry_form,double_dyeing,batch_sl_prefix,batch_sl_prefix_num,batch_sl_no,shift_id,dyeing_pdo";
			// echo "10**insert into pro_batch_create_mst (".$field_array.") values ".$data_array; die;
			$rID = sql_insert("pro_batch_create_mst", $field_array, $data_array, 0);
			if ($rID)
				$flag = 1;
			else
				$flag = 0;
		}
		else
		{
			/*
			|--------------------------------------------------------------------------
			| pro_batch_create_mst
			| data updating
			|--------------------------------------------------------------------------
			|
			*/
			$field_array_update = "batch_no*batch_date*batch_against*company_id*working_company_id*booking_no_id*booking_no*booking_without_order*color_id*batch_weight*total_trims_weight*save_string*color_range_id*process_id*dur_req_hr*dur_req_min*collar_qty*cuff_qty*floor_id*dyeing_machine*remarks*process_seq*booking_entry_form*updated_by*update_date*double_dyeing*shift_id*dyeing_pdo";
			$rID = sql_update("pro_batch_create_mst", $field_array_update, $data_array_update, "id", $update_id, 1);
			if ($rID)
				$flag = 1;
			else
				$flag = 0;
		}

		/*
		|--------------------------------------------------------------------------
		| pro_batch_create_dtls
		| data inserting
		|--------------------------------------------------------------------------
		|
		*/
		$field_array_dtls = "id, mst_id, program_no, po_id, po_batch_no, prod_id, item_description, body_part_id, width_dia_type, roll_no, roll_id,barcode_no,batch_qnty, inserted_by, insert_date,is_sales,dtls_id,batch_qty_pcs,color_type,item_size,remarks";
		// echo "10**insert into pro_batch_create_dtls (".$field_array_dtls.") values ".$data_array_dtls; die;
		$rID2 = sql_insert("pro_batch_create_dtls", $field_array_dtls, $data_array_dtls, 1);
		if ($flag == 1)
		{
			if ($rID2)
				$flag = 1;
			else
				$flag = 0;
		}


		if ($data_array_roll != "" && $roll_maintained == 1)
		{
			/*
			|--------------------------------------------------------------------------
			| pro_roll_details
			| data inserting
			|--------------------------------------------------------------------------
			|
			*/
			$field_array_roll = "id, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, roll_no, roll_id, barcode_no, booking_no, booking_without_order, inserted_by, insert_date,is_sales,qc_pass_qnty_pcs";
			// echo "10**insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll;die;
			$rID3 = sql_insert("pro_roll_details", $field_array_roll, $data_array_roll, 0);
			if ($flag == 1)
			{
				if ($rID3)
					$flag = 1;
				else
					$flag = 0;
			}
		}

		if ($data_array_dtls_trims != "")
		{
			/*
			|--------------------------------------------------------------------------
			| pro_batch_trims_dtls
			| data inserting
			|--------------------------------------------------------------------------
			|
			*/
			$field_array_dtls_trims = "id,mst_id,item_description,trims_wgt_qnty,remarks,inserted_by,insert_date,status_active,is_deleted";
			//echo "insert into pro_batch_trims_dtls (".$field_array_dtls_trims.") values ".$data_array_dtls_trims;die;
			$rID4 = sql_insert("pro_batch_trims_dtls", $field_array_dtls_trims, $data_array_dtls_trims, 1);
			if ($flag == 1)
			{
				if ($rID4)
					$flag = 1;
				else
					$flag = 0;
			}
		}
		//echo "10**".$rID . "**".$rID1 . "**".$rID2 . "**".$rID3 . "**".$rID4 . "**". $flag; oci_rollback($con); die;

		/*
		|--------------------------------------------------------------------------
		| MYSQL Database
		| data COMMIT here
		|--------------------------------------------------------------------------
		|
		*/
		if ($db_type == 0)
		{
			if ($flag == 1) {
				mysql_query("COMMIT");
				echo "0**" . $batch_update_id . "**" . $serial_no . "**" . str_replace("'", "", $txt_new_batch_number);
			} else {
				mysql_query("ROLLBACK");
				echo "5**0**0";
			}
		}

		/*
		|--------------------------------------------------------------------------
		| ORACLE Database
		| data COMMIT here
		|--------------------------------------------------------------------------
		|
		*/
		else if ($db_type == 2 || $db_type == 1)
		{
			if ($flag == 1) {
				oci_commit($con);
				echo "0**" . $batch_update_id . "**" . $serial_no . "**" . str_replace("'", "", $txt_new_batch_number);
			} else {
				oci_rollback($con);
				echo "5**0**0";
			}
		}

		disconnect($con);
		die;
	}

	/*
	|--------------------------------------------------------------------------
	| Update
	|--------------------------------------------------------------------------
	|
	*/
	else if ($operation == 1)
	{
		$con = connect();
		if ($db_type == 0) {
			mysql_query("BEGIN");
		}

		$prev_batch_data_arr = array();
		$prev_batch_data = sql_select("select a.id as dtls_id, a.po_id, b.color_id,b.batch_weight from pro_batch_create_dtls a, pro_batch_create_mst b where a.mst_id=b.id and b.id=$update_id");
		foreach ($prev_batch_data as $row)
		{
			$prev_batch_data_arr[$row[csf('dtls_id')]]['po_id'] = $row[csf('po_id')];
			$prev_batch_data_arr[$row[csf('dtls_id')]]['color'] = $row[csf('color_id')];
		}

		//dyes_chem_requ_recipe_att
		$recipe_data = sql_select("select b.batch_id from  pro_recipe_entry_mst b,pro_batch_create_mst a where a.id=b.batch_id and a.id=$update_id and a.status_active=1 and b.status_active=1");
		$recipe_batch_id=0;
		foreach ($recipe_data as $row)
		{
			$recipe_batch_id= $row[csf('batch_id')];

		}
		$issue_req_recipe_data = sql_select("select b.batch_id from  pro_recipe_entry_mst b,pro_batch_create_mst a,dyes_chem_requ_recipe_att c where a.id=b.batch_id and a.id=$update_id and c.recipe_id=b.id and a.status_active=1 and b.status_active=1");
		$issue_req_batch_id=0;
		foreach ($issue_req_recipe_data as $row)
		{
			$issue_req_batch_id= $row[csf('batch_id')];

		}

		$recipe_data = sql_select("select b.batch_id from  pro_fab_subprocess b,pro_batch_create_mst a where a.id=b.batch_id and a.id=$update_id and a.status_active=1 and b.status_active=1 and b.entry_form in(35,32)");
		$subpro_batch_id=0;
		foreach ($recipe_data as $row)
		{
			$subpro_batch_id=$row[csf('batch_id')];
		}

		$production_data = sql_select("SELECT b.batch_id, a.recv_number from inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id = b.mst_id and a.entry_form=66 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.batch_id=$update_id");
		$production_roll_batch_id=0;
		foreach ($production_data as $row)
		{
			$production_roll_batch_id=$row[csf('batch_id')];
		}
		$sub_msg="";
		if($recipe_batch_id || $subpro_batch_id || $issue_req_batch_id || $production_roll_batch_id)
		{
			if($subpro_batch_id) $sub_msg="Dyeing/HeatSet Prod found";
			else if($recipe_batch_id) $sub_msg="Recipe found";
			else if($issue_req_batch_id) $sub_msg="Issue Req. found";
			else if($production_roll_batch_id) $sub_msg="Roll Production found";
			$msg_next_process="101**$sub_msg,Update/Delete not allowed";
			echo $msg_next_process;
			disconnect($con);
			die;
		}

		$color_id=0;

		$flag = 1;
		$roll_maintained 	= str_replace("'", "", $roll_maintained);
		$txt_search_type 	= str_replace("'", "", $txt_search_type);

		if (is_duplicate_field("batch_no", "pro_fab_subprocess", "batch_id=$update_id and entry_form=35 and load_unload_id in(1) and status_active=1 and is_deleted=0") == 1)
		{
			echo "14**0**Already Loaded/Unload,Update not allowed";
			disconnect($con);
			die;
		}


		$poBatchNoArr = array();
		$batch_update_id = str_replace("'", "", $update_id);
		$serial_no = str_replace("'", "", $txt_batch_sl_no);

		/*
		|--------------------------------------------------------------------------
		| pro_batch_create_mst
		| duplicate checking
		|--------------------------------------------------------------------------
		|
		*/
		if (is_duplicate_field("batch_no", "pro_batch_create_mst", "batch_no=$txt_new_batch_number and id<>$update_id and status_active=1 and is_deleted=0") == 1)
		{
			echo "11**0";
			disconnect($con);
			die;
		}

		/*
		|--------------------------------------------------------------------------
		| pro_batch_create_mst
		| data preparing for
		| $data_array_update
		|--------------------------------------------------------------------------
		|
		*/
		$data_array_update = $txt_batch_date . "*" . $txt_batch_weight . "*" . $txt_tot_trims_weight . "*" . $save_data . "*" . $cbo_color_range . "*" . $txt_process_id . "*" . $txt_du_req_hr . "*" . $txt_du_req_min . "*" . $txt_collar_qty . "*" . $txt_cuff_qty . "*" . $cbo_floor . "*" . $cbo_machine_name . "*" . $txt_remarks . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*" . $txt_process_seq."*".$cbo_double_dyeing."*".$cbo_shift_name."*".$txt_dyeing_pdo;

		for ($i = 1; $i <= $total_row; $i++)
		{
			$id_dtls_batch = return_next_id_by_sequence("PRO_BATCH_CREATE_DTLS_PK_SEQ", "pro_batch_create_dtls", $con);
			$id_roll = return_next_id_by_sequence("PRO_ROLL_DTLS_PK_SEQ", "pro_roll_details", $con);

			$program_no = "programNo_" . $i;
			$po_id = "poId_" . $i;
			$prod_id = "productId_" . $i;
			$body_part_id = "bodyPartId_" . $i;
			$cboDiaWidthType = "cboDiaWidthType_" . $i;
			$txtRollNo = "txtRollNo_" . $i;
			$hideRollNo = "rollId_" . $i;
			$barcodeNo = "barcodeNo_" . $i;
			$txtBatchQnty = "txtBatchQnty_" . $i;
			$txtQtyPcs = "txtQtyPcs_" . $i;
			$txtSize = "txtSize_" . $i;
			$txtPoBatchNo = "txtPoBatchNo_" . $i;
			$txtRemarks = "txtRemarks_" . $i;
			$cboColorType = "cboColorType_" . $i;
			$isSalesOrder = "isSalesOrder_" . $i;
			$fromBatchDtlsId = "fromBatchDtlsId_" . $i;
			$updateIdDtls = "updateIdDtls_" . $i;
			$ItemDesc = $product_array[str_replace("'", "", $$prod_id)];

			if (str_replace("'", "", $$updateIdDtls) != "")
			{
				/*
				|--------------------------------------------------------------------------
				| pro_batch_create_dtls
				| data preparing for
				| $data_array_dtls_update
				|--------------------------------------------------------------------------
				|
				*/
				$id_arr[] = str_replace("'", '', $$updateIdDtls);
				$data_array_dtls_update[str_replace("'", '', $$updateIdDtls)] = explode("*", ($$program_no . "*" . $$po_id . "*" . $$txtPoBatchNo . "*" . $$prod_id . "*'" . $ItemDesc . "'*" . $$body_part_id . "*" . $$cboDiaWidthType . "*" . $$txtRollNo . "*" . $$hideRollNo . "*" . $$barcodeNo . "*" . $$txtBatchQnty . "*" . $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*" . $$txtQtyPcs. "*" . $$cboColorType. "*" . $$txtSize. "*" . $$txtRemarks ));
				$id_dtls = str_replace("'", '', $$updateIdDtls);
			}

			if (str_replace("'", "", $$hideRollNo) != "")
			{
				/*
				|--------------------------------------------------------------------------
				| pro_roll_details
				| data preparing for
				| $data_array_roll
				|--------------------------------------------------------------------------
				|
				*/
				$is_sales = ($txt_search_type == 7) ? 1 : 0;
				if (str_replace("'", "", $booking_without_order) == 1 && $is_sales != 1)
				{
					$bookingNo = str_replace("'", "", $txt_booking_no);
					$poId = str_replace("'", "", $txt_booking_no_id);
				}
				else
				{
					$bookingNo = '';
					$poId = str_replace("'", "", $$po_id);
				}

				if ($data_array_roll != "")
					$data_array_roll .= ",";
				$data_array_roll .= "(" . $id_roll . "," . $batch_update_id . "," . $id_dtls . ",'" . $poId . "',639," . $$txtBatchQnty . "," . $$txtRollNo . "," . $$hideRollNo . "," . $$barcodeNo . ",'" . $bookingNo . "'," . $booking_without_order . "," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "'," . $is_sales . "," . $$txtQtyPcs . ")";
				//$all_barcode_nos_arr[str_replace("'", "", $$barcodeNo)]= str_replace("'", "", $$barcodeNo);
			}
		}

		/*
		|--------------------------------------------------------------------------
		| pro_batch_trims_dtls
		| data preparing for
		| $data_array_dtls_trims
		|--------------------------------------------------------------------------
		|
		*/
		$save_string = explode("!!", str_replace("'", "", $save_data));
		for ($i = 0; $i < count($save_string); $i++)
		{
			$id_dtls_trim = return_next_id_by_sequence("PRO_BATCH_TRIMS_DTLS_PK_SEQ", "pro_batch_trims_dtls", $con);
			$data = explode("_", $save_string[$i]);
			$item_des = $data[0];
			$trims_qty = $data[1];
			$remarks = $data[2];
			if ($trims_qty > 0)
			{
				if ($data_array_dtls_trims != "")
					$data_array_dtls_trims .= ",";
				$data_array_dtls_trims .= "(" . $id_dtls_trim . "," . $batch_update_id . ",'" . $item_des . "'," . $trims_qty . ",'" . $remarks . "'," . $_SESSION['logic_erp']['user_id'] . ",'" . $pc_date_time . "',1,0)";
			}
		}

		/*
		|--------------------------------------------------------------------------
		| pro_batch_create_mst
		| data updating
		|--------------------------------------------------------------------------
		|
		*/
		$field_array_update = "batch_date*batch_weight*total_trims_weight*save_string*color_range_id*process_id*dur_req_hr*dur_req_min*collar_qty*cuff_qty*floor_id*dyeing_machine*remarks*updated_by*update_date*process_seq*double_dyeing*shift_id*dyeing_pdo";
		//echo "insert into pro_batch_create_mst (".$field_array_update.") values ".$data_array_update;die;
		$rID = sql_update("pro_batch_create_mst", $field_array_update, $data_array_update, "id", $update_id, 1);
		if ($rID)
			$flag = 1;
		else
			$flag = 0;

		/*
		|--------------------------------------------------------------------------
		| pro_roll_details
		| data deleting
		|--------------------------------------------------------------------------
		|
		*/
		//echo "delete from pro_roll_details where mst_id=$update_id and entry_form=64";
		$delete_roll = execute_query("delete from pro_roll_details where mst_id=".$update_id." and entry_form=639", 1);
		if ($flag == 1)
		{
			if ($delete_roll)
				$flag = 1;
			else
				$flag = 0;
		}

		if ($data_array_dtls_update != "")
		{
			/*
			|--------------------------------------------------------------------------
			| pro_batch_create_dtls
			| data updating
			|--------------------------------------------------------------------------
			|
			*/
			$field_array_dtls_update = "program_no*po_id*po_batch_no*prod_id*item_description*body_part_id*width_dia_type*roll_no*roll_id*barcode_no*batch_qnty*updated_by*update_date*batch_qty_pcs*color_type*item_size*remarks";
			//echo bulk_update_sql_statement( "pro_batch_create_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr );die;
			$rID2 = execute_query(bulk_update_sql_statement("pro_batch_create_dtls", "id", $field_array_dtls_update, $data_array_dtls_update, $id_arr));
			if ($flag == 1)
			{
				if ($rID2)
					$flag = 1;
				else
					$flag = 0;
			}
		}

		if ($txt_deleted_id != "")
		{
			/*
			|--------------------------------------------------------------------------
			| pro_batch_create_dtls
			| data updating
			|--------------------------------------------------------------------------
			|
			*/
			$field_array_status = "updated_by*update_date*status_active*is_deleted";
			$data_array_status = $_SESSION['logic_erp']['user_id'] . "*'" . $pc_date_time . "'*0*1";
			$rID4 = sql_multirow_update("pro_batch_create_dtls", $field_array_status, $data_array_status, "id", $txt_deleted_id, 1);
			if ($flag == 1)
			{
				if ($rID4)
					$flag = 1;
				else
					$flag = 0;
			}
		}

		if ($data_array_roll != "" && $roll_maintained == 1)
		{
			/*
			|--------------------------------------------------------------------------
			| pro_roll_details
			| data inserting
			|--------------------------------------------------------------------------
			|
			*/
			$field_array_roll = "id, mst_id, dtls_id, po_breakdown_id, entry_form, qnty, roll_no, roll_id, barcode_no, booking_no, booking_without_order, inserted_by, insert_date,is_sales,qc_pass_qnty_pcs";
			//echo "6**0**insert into pro_roll_details (".$field_array_roll.") values ".$data_array_roll;die;
			$rID5 = sql_insert("pro_roll_details", $field_array_roll, $data_array_roll, 0);
			if ($flag == 1)
			{
				if ($rID5)
					$flag = 1;
				else
					$flag = 0;
			}
		}

		/*
		|--------------------------------------------------------------------------
		| pro_batch_trims_dtls
		| data deleting
		|--------------------------------------------------------------------------
		|
		*/
		//echo "delete from pro_batch_trims_dtls where mst_id=$batch_update_id";
		$delete_trims_dtls = execute_query("delete from pro_batch_trims_dtls where mst_id=".$batch_update_id."", 0);
		if ($flag == 1)
		{
			if ($delete_trims_dtls)
				$flag = 1;
			else
				$flag = 0;
		}

		if ($data_array_dtls_trims != "")
		{
			/*
			|--------------------------------------------------------------------------
			| pro_batch_trims_dtls
			| data inserting
			|--------------------------------------------------------------------------
			|
			*/
			$field_array_dtls_trims = "id,mst_id,item_description,trims_wgt_qnty,remarks,inserted_by,insert_date,status_active,is_deleted";
			//echo "10**insert into pro_batch_trims_dtls (".$field_array_dtls_trims.") values ".$data_array_dtls_trims; die;
			$rID6 = sql_insert("pro_batch_trims_dtls", $field_array_dtls_trims, $data_array_dtls_trims, 1);
			if ($flag == 1)
			{
				if ($rID6)
					$flag = 1;
				else
					$flag = 0;
			}
		}

		//echo "10**". $rID . "**" . $rID2 . "**" . $rID4 . "**" . $rID5 . "**" . $rID6 .'=='. $flag ; oci_rollback($con);die;

		/*
		|--------------------------------------------------------------------------
		| MYSQL Database
		| data COMMIT here
		|--------------------------------------------------------------------------
		|
		*/
		if ($db_type == 0)
		{
			if ($flag == 1)
			{
				mysql_query("COMMIT");
				echo "1**" . $batch_update_id . "**" . $serial_no . "**" . str_replace("'", "", $txt_new_batch_number);
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "6**0**1";
			}
		}

		/*
		|--------------------------------------------------------------------------
		| ORACLE Database
		| data COMMIT here
		|--------------------------------------------------------------------------
		|
		*/
		else if ($db_type == 2 || $db_type == 1)
		{
			if ($flag == 1)
			{
				oci_commit($con);
				echo "1**" . $batch_update_id . "**" . $serial_no . "**" . str_replace("'", "", $txt_new_batch_number);
			}
			else
			{
				oci_rollback($con);
				echo "6**0**1";
			}
		}
		disconnect($con);
		die;
	}
}

if ($action == "batch_popup")
{
	echo load_html_head_contents("Batch Info", "../../", 1, 1, '', '1', '');
	extract($_REQUEST);
	?>
	<script>

		function js_set_value(batch_id,batch_no,unloaded_batch,ext_from,batch_against,sales_batch) {
			document.getElementById('hidden_batch_id').value = batch_id;
			document.getElementById('hidden_batch_no').value = batch_no;
			document.getElementById('hidden_unloaded_batch').value = unloaded_batch;
			document.getElementById('hidden_ext_from').value = ext_from;
			document.getElementById('hidden_batch_against').value = batch_against;
			document.getElementById('hidden_sales_batch').value = sales_batch;
			parent.emailwindow.hide();
		}

	</script>
	</head>
	<body>
		<div align="center">
			<fieldset style="width:1030px;margin-left:4px;">
				<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
					<table cellpadding="0" cellspacing="0" width="500" border="1" rules="all" class="rpt_table">
						<thead>
							<th>Search By</th>
							<th>Search</th>
							<th colspan="2">Batch Date</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:100px"
								class="formbutton"/>
								<input type="hidden" name="hidden_batch_id" id="hidden_batch_id" value="">
								<input type="hidden" name="hidden_batch_no" id="hidden_batch_no" value="">
								<input type="hidden" name="hidden_unloaded_batch" id="hidden_unloaded_batch" value="">
								<input type="hidden" name="hidden_ext_from" id="hidden_ext_from" value="">
								<input type="hidden" name="hidden_batch_against" id="hidden_batch_against" value="">
								<input type="hidden" name="hidden_sales_batch" id="hidden_sales_batch" value="">
							</th>
						</thead>
						<tr class="general">
							<td align="center">
								<?
								$search_by_arr = array(1 => "Batch No", 2 => "Booking No");
								echo create_drop_down("cbo_search_by", 150, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>
							<td align="center">
								<input type="text" style="width:140px" class="text_boxes" name="txt_search_common"
								id="txt_search_common"/>
							</td>
	                        <td>
	                        	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:160px;" tabindex="6" value="" />
	                        </td>
	                        <td>
	                        	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:160px;" tabindex="6" value="" />
	                        	<div id="is_sales_booking"><input type="checkbox" name="chkIsSales" id="chkIsSales"/> <label
									for="chkIsSales">Is FSO</label></div>
	                        </td>
							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show"
								onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('chkIsSales').checked, 'create_batch_search_list_view', 'search_div', 'independent_striping_batch_creation_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
								style="width:100px;"/>
							</td>
						</tr>
	                    <tr>
	                    	<td colspan="5"><? echo load_month_buttons(1);  ?></td>
	                    </tr>
					</table>
					<div id="search_div" style="margin-top:10px"></div>
				</form>
			</fieldset>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if ($action == "create_batch_search_list_view") 
{
	$data = explode('_', $data);
	// echo "<pre>";print_r($data);die;
	$search_string = "%" . trim($data[0]) . "%";
	$search_by = $data[1];
	$company_id = $data[2];
	$date_from 	= $data[3];
	$date_to	= $data[4];
	$is_sales_flag	= $data[5];

	if($date_from!="" && $date_to!="")
	{
		if($db_type==0)
		{
			$date_from=change_date_format(str_replace("'","",$date_from),"yyyy-mm-dd","");
			$date_to=change_date_format(str_replace("'","",$date_to),"yyyy-mm-dd","");
		}
		else
		{
			$date_from=date("j-M-Y",strtotime(str_replace("'","",$date_from)));
			$date_to=date("j-M-Y",strtotime(str_replace("'","",$date_to)));
		}
		$date_con=" and a.batch_date between '$date_from' and '$date_to'";
	}

	if ($search_by == 1)
		$search_field = 'a.batch_no';
	else
		$search_field = 'a.booking_no';

	$batch_cond = "";
	//if ($batch_against_id != 2) $batch_cond = " and a.batch_against=$batch_against_id";
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');

	$po_name_arr = array();
	if ($db_type == 2) $group_concat = "  listagg(cast(b.po_number AS VARCHAR2(4000)),',') within group (order by b.id) as order_no";
	else if ($db_type == 0) $group_concat = " group_concat(b.po_number) as order_no";

	if ($db_type == 2) $group_concat2 = "  listagg(cast(b.po_id AS VARCHAR2(4000)),',') within group (order by b.id) as po_id";
	else if ($db_type == 0) $group_concat2 = " group_concat(b.po_id) as po_id";
	if ($is_sales_flag == "true") 
	{
		$is_sales_batch=1;
		$is_sales_cond=" and a.is_sales=1";
	}
	else
	{
		$is_sales_cond=" and a.is_sales=0";
	}
	//echo $is_sales_cond;die;

	// Without Sample Batch
	$sql ="SELECT a.id, a.batch_no,a.sales_order_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.batch_against, a.batch_for, a.booking_no, a.color_id, a.is_sales, a.re_dyeing_from 
	from pro_batch_create_mst a 
	where  a.company_id=$company_id and $search_field like '$search_string' and a.booking_without_order!=1 and a.page_without_roll=0 and a.status_active=1 and a.entry_form=0 and a.is_deleted=0 $date_con $is_sales_cond order by a.batch_date desc";
	// echo $sql;
	$result = sql_select($sql);

	if(count($result)<1)
	{
		echo "<span>Data Not Found</span>";die;
	}
	$batch_id=array();
	foreach ($result as $row) 
	{
		$ids = implode(",", array_unique(explode(",", $row[csf("po_id")])));
		$po_ids .= $ids . ",";
		$is_sales[] = $row[csf("is_sales")];
		$batch_id[] .= $row[csf("id")];
	}
	$po_ids = rtrim($po_ids, ",");
	if($po_ids!="") $po_ids=$po_ids;else $po_ids=0;
	/*$sql_po = sql_select("select b.id,b.po_number from wo_po_break_down b where b.status_active=1 and b.is_deleted=0 and b.id in($po_ids)");
	$po_name_arr = array();
	foreach ($sql_po as $p_name) {
		$po_name_arr[$p_name[csf('id')]] = $p_name[csf('po_number')];
	}*/

	$sql_po = sql_select("select a.id,b.po_number from wo_po_break_down b,pro_batch_create_mst a, pro_batch_create_dtls c where a.id=c.mst_id and b.id=c.po_id and a.company_id=$company_id and $search_field like '$search_string' and a.page_without_roll=0 and a.status_active=1 and a.entry_form=0 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $batch_cond $date_con and b.status_active=1 and b.is_deleted=0 ");
	$po_name_arr = array();
	foreach ($sql_po as $p_name) {
		$po_name_arr[$p_name[csf('id')]] .= $p_name[csf('po_number')].',';
	}


	$sql_load_unload="select id, batch_id,load_unload_id,result from pro_fab_subprocess where batch_id in (".implode(",",$batch_id).") and load_unload_id in (2) and entry_form=35 and is_deleted=0 and status_active=1";
	$load_unload_data=sql_select($sql_load_unload);
	foreach ($load_unload_data as $row)
	{
		if($row[csf('load_unload_id')]==1)
		{
			$load_unload_arr[$row[csf('batch_id')]] = $row[csf('load_unload_id')];
		}
		else if($row[csf('load_unload_id')]==2 )
		{
			$unloaded_batch[$row[csf('batch_id')]] = $row[csf('batch_id')];
		}
	}
	$batch_id_cond=where_con_using_array($batch_id,0,'re_dyeing_from');
	$re_dyeing_from = return_library_array("select re_dyeing_from from pro_batch_create_mst where re_dyeing_from <>0 and status_active = 1 and is_deleted = 0 $batch_id_cond","re_dyeing_from","re_dyeing_from");

	//print_r($re_dyeing_from);
	?>
	<style>
		table tbody tr td {
			text-align: center;
		}
	</style>
	<table class="rpt_table" id="rpt_tabletbl_list_search" rules="all" width="1020" cellspacing="0" cellpadding="0"
	border="0">
	<thead>
		<tr>
			<th width="50">SL No</th>
			<th width="100">Batch No</th>
			<th width="70">Ext. No</th>
			<th width="150">PO No./FSO No</th>
			<th width="105">Booking No</th>
			<th width="80">Batch Weight</th>
			<th width="80">Total Trims Weight</th>
			<th width="80">Batch Date</th>
			<th width="80">Batch Against</th>
			<th width="85">Batch For</th>
			<th>Color</th>
		</tr>
	</thead>
	<tbody>
		<?
		$i = 1;
		foreach ($result as $row)
		{
			if( $unloaded_batch[$row[csf('id')]])
			{
				if ($row[csf("is_sales")] != 1) 
				{
					$order_id=rtrim($po_name_arr[$row[csf('id')]],',');
					$order_ids=implode(",",array_unique(explode(",",$order_id)));
				}
				else 
				{
					$order_ids = $row[csf("sales_order_no")];
				}
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

				if($re_dyeing_from[$row[csf('id')]])
				{
					$ext_from = $re_dyeing_from[$row[csf('id')]];
				}else{
					$ext_from = "0";
				}
				?>
				<tr onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('batch_no')]; ?>','<? echo $unloaded_batch[$row[csf('id')]]; ?>','<? echo $ext_from;?>','<? echo $row[csf('batch_against')];?>','<? echo $row[csf("is_sales")];?>')" bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer">
					<td width="50"><? echo $i; ?></td>
					<td width="100"><? echo $row[csf("batch_no")]; ?></td>
					<td width="70"><? echo $row[csf("extention_no")]; ?></td>
					<td width="150"><p><? echo trim($order_ids, ","); ?></p></td>
					<td width="105"><? echo $row[csf("booking_no")]; ?></td>
					<td width="80"><? echo $row[csf("batch_weight")]; ?></td>
					<td width="80"><? echo $row[csf("total_trims_weight")]; ?></td>
					<td width="80"><? echo $row[csf("batch_date")]; ?></td>
					<td width="80"><? echo $batch_against[$row[csf("batch_against")]]; ?></td>
					<td width="85"><? echo $batch_for[$row[csf("batch_for")]]; ?></td>
					<td><? echo $color_arr[$row[csf("color_id")]]; ?></td>
				</tr>
				<?
				$i++;
			}
		}
		?>
	</tbody>
	</table>
	<?
	exit();
}

if ($action == "populate_data_from_search_popup") // From Batch Number popup onclose
{
	$data = explode("**", $data);
	$batch_against = $data[0];
	$batch_id = $data[1];
	$batch_no = $data[2];
	$company_id = $data[3];
	$unloaded_batch = $data[4];
	$ext_from = $data[5];

	if ($db_type == 0) $year_field = "DATE_FORMAT(insert_date,'%y')";
	else if ($db_type == 2) $year_field = "to_char(insert_date,'YY')";
	else $year_cond = "";//defined Later

	$color_arr = return_library_array("SELECT id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$data_array = sql_select("SELECT id, company_id,working_company_id, batch_no,batch_type_id, extention_no,floor_id, batch_weight, total_trims_weight,save_string, batch_date, batch_against, batch_for, booking_no, booking_no_id,booking_without_order, color_id, re_dyeing_from, color_range_id, organic, process_id, dur_req_hr, dur_req_min, collar_qty, cuff_qty, dyeing_machine, remarks,ready_to_approved,is_approved, $year_field as year, sales_order_no, sales_order_id, is_sales, process_seq, booking_entry_form, service_booking_id, service_booking_no, double_dyeing, batch_sl_no, style_breakdown,shift_id,dyeing_pdo from pro_batch_create_mst where id='$batch_id'");

	foreach ($data_array as $row)
	{
		$process_name = '';
		$process_id_array = explode(",", $row[csf("process_id")]);
		foreach ($process_id_array as $val)
		{
			if ($process_name == "")
				$process_name = $conversion_cost_head_array[$val];
			else
				$process_name .= "," . $conversion_cost_head_array[$val];
		}
		echo "document.getElementById('txt_batch_weight').value = '" . $row[csf("batch_weight")] . "';\n";
		echo "document.getElementById('cbo_company_id').value = '" . $row[csf("company_id")] . "';\n";
		echo "document.getElementById('cbo_working_company_id').value = '" . $row[csf("working_company_id")] . "';\n";
		echo "document.getElementById('txt_tot_trims_weight').value = '" . $row[csf("total_trims_weight")] . "';\n";
		echo "document.getElementById('save_data').value = '" . $row[csf("save_string")] . "';\n";		
		echo "document.getElementById('txt_from_batch_number').value = '" . $row[csf("batch_no")] . "';\n";
		echo "load_drop_down('requires/independent_striping_batch_creation_controller','".$row[csf("working_company_id")]."', 'load_drop_down_floor', 'td_floor' );\n";
		echo "document.getElementById('cbo_floor').value = '" . $row[csf("floor_id")] . "';\n";
		echo "document.getElementById('txt_batch_color_id').value = '" . $row[csf("color_id")] . "';\n";
		echo "document.getElementById('txt_batch_color').value = '" . $color_arr[$row[csf("color_id")]] . "';\n";
		
		echo "document.getElementById('cbo_color_range').value = '" . $row[csf("color_range_id")] . "';\n";
		echo "document.getElementById('txt_process_id').value = '" . $row[csf("process_id")] . "';\n";
		echo "document.getElementById('txt_process_name').value = '" . $process_name . "';\n";
		echo "document.getElementById('txt_process_seq').value = '" . $row[csf("process_seq")] . "';\n";
		echo "document.getElementById('txt_du_req_hr').value = '" . $row[csf("dur_req_hr")] . "';\n";
		echo "document.getElementById('txt_du_req_min').value = '" . $row[csf("dur_req_min")] . "';\n";
		echo "document.getElementById('txt_collar_qty').value = '" . $row[csf("collar_qty")] . "';\n";
		echo "document.getElementById('txt_cuff_qty').value = '" . $row[csf("cuff_qty")] . "';\n";
		echo "document.getElementById('txt_remarks').value = '" . $row[csf("remarks")] . "';\n";
		//echo "document.getElementById('from_batch_id').value = '" . $row[csf("id")] . "';\n";//update_id
		echo "load_drop_down('requires/independent_striping_batch_creation_controller','".$row[csf("floor_id")]."', 'load_drop_machine', 'td_dyeing_machine');\n";
		echo "document.getElementById('cbo_machine_name').value = '" . $row[csf("dyeing_machine")] . "';\n";
		echo "document.getElementById('cbo_double_dyeing').value = '" . $row[csf("double_dyeing")] . "';\n";

		echo "document.getElementById('unloaded_batch').value = '".$unloaded_batch."';\n";
		echo "document.getElementById('ext_from').value = '".$ext_from."';\n";
		//echo "set_button_status(1, '" . $_SESSION['page_permission'] . "', 'fnc_batch_creation',1);\n";

		echo "document.getElementById('cbo_shift_name').value = '" . $row[csf("shift_id")] . "';\n";
		echo "document.getElementById('txt_dyeing_pdo').value = '" . $row[csf("dyeing_pdo")] . "';\n";
	}

	exit();
}

if ($action == 'batch_details') // From Batch Number popup onclose
{
	$data = explode('**', $data);
	$batch_id = $data[0];
	$roll_maintained = $data[1];

	$data_array = sql_select("SELECT a.batch_against,a.company_id, a.batch_for, a.booking_no, a.re_dyeing_from, a.color_id, a.booking_without_order,a.is_sales,a.sales_order_id, b.id as from_batch_dtls_id, b.program_no, b.po_id, b.prod_id, b.item_description, b.body_part_id, b.width_dia_type, b.roll_no, b.roll_id, b.barcode_no, b.batch_qnty, b.batch_qty_pcs, b.po_batch_no, b.color_type, b.item_size, b.remarks 
	from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and b.mst_id=$batch_id and b.status_active=1 and b.is_deleted=0");

	foreach ($data_array as $row)
	{
		$all_role_id_arr[$row[csf("roll_id")]] = $row[csf("roll_id")];
	}

	$all_role_id_arr = array_filter(array_unique($all_role_id_arr));
	if(count($all_role_id_arr)>0)
	{
		$all_role_ids = implode(",", $all_role_id_arr);
		$rollCond = $all_roll_id_cond = "";

		if($db_type==2 && count($all_role_id_arr)>999)
		{
			$all_role_id_chunk=array_chunk($all_role_id_arr,999) ;
			foreach($all_role_id_chunk as $chunk_arr)
			{
				$rollCond.=" c.id in(".implode(",",$chunk_arr).") or ";
			}

			$all_roll_id_cond.=" and (".chop($rollCond,'or ').")";
		}
		else
		{
			$all_roll_id_cond=" and c.id in($all_role_ids)";
		}
	}

	if(!empty($all_role_id_arr))
	{
		$barcodeData_batch = sql_select("SELECT b.prod_id, c.id as roll_id, c.barcode_no, c.coller_cuff_size, b.yarn_lot FROM pro_grey_prod_entry_dtls b, pro_roll_details c WHERE b.id=c.dtls_id and c.entry_form in (2,22) and c.status_active=1 and c.is_deleted=0 $all_roll_id_cond ");

		foreach ($barcodeData_batch as $val)
		{
			$batch_barcode_size_arr[$val[csf('roll_id')]] = $val[csf('coller_cuff_size')];
		}
	}

	$fso_data_array = return_library_array("SELECT a.id, a.job_no FROM fabric_sales_order_mst a, pro_batch_create_dtls b WHERE a.id=b.po_id and b.mst_id=$batch_id and b.status_active=1 and b.is_deleted=0 group by a.id, a.job_no", 'id', 'job_no');

	$po_array = return_library_array("SELECT a.id, a.po_number FROM wo_po_break_down a, pro_batch_create_dtls b WHERE a.id=b.po_id and b.mst_id=$batch_id and b.status_active=1 and b.is_deleted=0 group by a.id, a.po_number", 'id', 'po_number');

	$fab_description_array = return_library_array("SELECT a.id, a.product_name_details from product_details_master a, pro_batch_create_dtls b where a.id=b.prod_id and b.mst_id=$batch_id and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details", 'id', 'product_name_details');

	$isbc_sql="SELECT b.dtls_id, a.from_batch_id from PRO_BATCH_CREATE_MST a, PRO_BATCH_CREATE_DTLS b 
	where a.id=b.mst_id and a.from_batch_id=$batch_id and a.entry_form=639 and b.is_deleted=0 and b.status_active=1";
	// echo $isbc_sql;die;
	$isbc_sql_data=sql_select($isbc_sql);
	foreach ($isbc_sql_data as $row)
	{
		$dtls_batch_id[$row[csf('dtls_id')]] = $row[csf('dtls_id')];
	}
	// echo "<pre>";print_r($dtls_batch_id);die;

	$tblRow = 0;
	foreach ($data_array as $row)
	{
		if ($row[csf('is_sales')] == 1)
		{
			$po_no=$fso_data_array[$row[csf('po_id')]];
		}
		else
		{
			$po_no=$po_array[$row[csf('po_id')]];
		}

		if( $dtls_batch_id[$row[csf('from_batch_dtls_id')]]=="")
		{
			$tblRow++;
			?>
			<tr id="tr_<? echo $tblRow; ?>">
				<td id="slTd_<? echo $tblRow; ?>"><? echo $tblRow; ?></td>
				<td style="word-break:break-all; width: 80" id="cboProgramNo_<? echo $tblRow; ?>"><? echo $row[csf('program_no')];?></td>
				<td style="word-break:break-all; width: 130" id="cboPoNo_<? echo $tblRow; ?>"><? echo $po_no;?></td>
				<td style="word-break:break-all; width: 180" id="cboItemDesc_<? echo $tblRow; ?>"><? echo $fab_description_array[$row[csf('prod_id')]];?></td>
				<td style="word-break:break-all; width: 120" id="cboBodyPart_<? echo $tblRow; ?>"><? echo $body_part[$row[csf('body_part_id')]];?></td>
				<td style="word-break:break-all; width: 90" id="cboDiaWidthType_<? echo $tblRow; ?>"><? echo $fabric_typee[$row[csf('width_dia_type')]];?></td>
				<td style="word-break:break-all; width: 50" id="txtRollNo_<? echo $tblRow; ?>"><? if ($row[csf('roll_no')] != 0) echo $row[csf('roll_no')];?></td>
				<td style="word-break:break-all; width: 90" id="barcodeNo_<? echo $tblRow; ?>"><? echo $row[csf('barcode_no')];?></td>
				<td>
					<input type="text" name="txtBatchQnty[]" id="txtBatchQnty_<? echo $tblRow; ?>"
					class="text_boxes_numeric" onKeyUp="calculate_batch_qnty();" style="width:60px" value="<? echo $row[csf('batch_qnty')]; ?>" disabled/>
				</td>			
				<td>
					<input type="text" name="txtQtyPcs[]" id="txtQtyPcs_<? echo $tblRow; ?>"
					class="text_boxes_numeric" onKeyUp="calculate_qtyPcs();" style="width:60px" value="<? echo $row[csf('batch_qty_pcs')]; ?>" disabled/>
				</td>
				<td style="word-break:break-all; width: 60" id="txtSize_<? echo $tblRow; ?>"><? if($roll_maintained==1) echo $item_size=$batch_barcode_size_arr[$row[csf('roll_id')]]; else echo $item_size=$row[csf('item_size')]; ?></td>
				<td style="word-break:break-all; width: 60" id="txtPoBatchNo_<? echo $tblRow; ?>"><? echo $row[csf('po_batch_no')]; ?></td>
				<td style="word-break:break-all; width: 100" id="cboColorType_<? echo $tblRow; ?>"><? echo $color_type[$row[csf("color_type")]]; ?>
				</td>
				<td>
	                <input type="text" name="txtRemarks[]" id="txtRemarks_<? echo $tblRow; ?>" class="text_boxes" style="width:100px;" />
	            </td>
				<td width="65">
					<input type="button" id="decrease_<? echo $tblRow; ?>" name="decrease[]" style="width:30px"
					class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $tblRow; ?>);"/>

					<input type="hidden" name="programNo[]" id="programNo_<? echo $tblRow; ?>" value="<? echo $row[csf('program_no')]; ?>"/>
					<input type="hidden" name="poId[]" id="poId_<? echo $tblRow; ?>" value="<? echo $row[csf('po_id')]; ?>"/>
					<input type="hidden" name="productId[]" id="productId_<? echo $tblRow; ?>" value="<? echo $row[csf('prod_id')]; ?>"/>
					<input type="hidden" name="bodyPartId[]" id="bodyPartId_<? echo $tblRow; ?>" value="<? echo $row[csf('body_part_id')]; ?>"/>
					<input type="hidden" name="widthDiaType[]" id="widthDiaType_<? echo $tblRow; ?>" value="<? echo $row[csf('width_dia_type')]; ?>"/>
					<input type="hidden" name="hideRollNo[]" id="hideRollNo_<? echo $tblRow; ?>" value="<? if ($row[csf('roll_no')] != 0) echo $row[csf('roll_no')]; ?>"/>
					<input type="hidden" name="rollId[]" id="rollId_<? echo $tblRow; ?>" value="<? echo $row[csf('roll_id')]; ?>"/>
					<input type="hidden" name="barcodeNo[]" id="barcodeNo_<? echo $tblRow; ?>" value="<? echo $row[csf('barcode_no')]; ?>"/>
					<input type="hidden" name="batchQty[]" id="batchQty_<? echo $tblRow; ?>" value="<? echo $row[csf('batch_qnty')]; ?>"/>
	                <input type="hidden" name="batchQtyPcs[]" id="batchQtyPcs_<? echo $tblRow; ?>" value="<? echo $row[csf('batch_qty_pcs')]; ?>"/>
	                <input type="hidden" name="itemSize[]" id="itemSize_<? echo $tblRow; ?>" value="<? echo $item_size; ?>"/>
	                <input type="hidden" name="poBatchNo[]" id="poBatchNo_<? echo $tblRow; ?>" value="<? echo $row[csf('po_batch_no')]; ?>"/>
					<input type="hidden" name="colorTypeId[]" id="colorTypeId_<? echo $tblRow; ?>" value="<? echo $row[csf("color_type")]; ?>"/>
					<input type="hidden" name="fromBatchDtlsId[]" id="fromBatchDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf("from_batch_dtls_id")]; ?>"/>
					<input type="hidden" name="isSalesOrder[]" id="isSalesOrder_<? echo $tblRow; ?>" value="<? echo $row[csf("is_sales")]; ?>"/>
					<input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $tblRow; ?>"/>

				</td>
			</tr>
			<?
		}
	}

	exit();
}

if ($action == "new_batch_popup") // New Batch Number popup onclose
{
	echo load_html_head_contents("Batch Info", "../../", 1, 1, '', '1', '');
	extract($_REQUEST);
	?>
	<script>

		function js_set_value(batch_id,batch_no,unloaded_batch,ext_from,batch_against,sales_batch) {
			document.getElementById('hidden_batch_id').value = batch_id;
			document.getElementById('hidden_batch_no').value = batch_no;
			document.getElementById('hidden_unloaded_batch').value = unloaded_batch;
			document.getElementById('hidden_ext_from').value = ext_from;
			document.getElementById('hidden_batch_against').value = batch_against;
			document.getElementById('hidden_sales_batch').value = sales_batch;
			parent.emailwindow.hide();
		}

	</script>
	</head>
	<body>
		<div align="center">
			<fieldset style="width:1030px;margin-left:4px;">
				<form name="searchorderfrm_1" id="searchorderfrm_1" autocomplete="off">
					<table cellpadding="0" cellspacing="0" width="500" border="1" rules="all" class="rpt_table">
						<thead>
							<th>Search By</th>
							<th>Search</th>
							<th colspan="2">Batch Date</th>
							<th>
								<input type="reset" name="reset" id="reset" value="Reset" style="width:100px"
								class="formbutton"/>
								<input type="hidden" name="hidden_batch_id" id="hidden_batch_id" value="">
								<input type="hidden" name="hidden_batch_no" id="hidden_batch_no" value="">
								<input type="hidden" name="hidden_unloaded_batch" id="hidden_unloaded_batch" value="">
								<input type="hidden" name="hidden_ext_from" id="hidden_ext_from" value="">
								<input type="hidden" name="hidden_batch_against" id="hidden_batch_against" value="">
								<input type="hidden" name="hidden_sales_batch" id="hidden_sales_batch" value="">
							</th>
						</thead>
						<tr class="general">
							<td align="center">
								<?
								$search_by_arr = array(1 => "Batch No", 2 => "Booking No");
								echo create_drop_down("cbo_search_by", 150, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>
							<td align="center">
								<input type="text" style="width:140px" class="text_boxes" name="txt_search_common"
								id="txt_search_common"/>
							</td>
	                        <td>
	                        	<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:160px;" tabindex="6" value="" />
	                        </td>
	                        <td>
	                        	<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:160px;" tabindex="6" value="" />
	                        </td>
							<td align="center">
								<input type="button" name="button2" class="formbutton" value="Show"
								onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $cbo_company_id; ?>+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value, 'create_new_batch_search_list_view', 'search_div', 'independent_striping_batch_creation_controller', 'setFilterGrid(\'tbl_list_search\',-1);')"
								style="width:100px;"/>
							</td>
						</tr>
	                    <tr>
	                    	<td colspan="5"><? echo load_month_buttons(1);  ?></td>
	                    </tr>
					</table>
					<div id="search_div" style="margin-top:10px"></div>
				</form>
			</fieldset>
		</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if ($action == "create_new_batch_search_list_view") // New Batch Number popup onclose
{
	$data = explode('_', $data);
	// echo "<pre>";print_r($data);die;
	$search_string = "%" . trim($data[0]) . "%";
	$search_by = $data[1];
	$company_id = $data[2];
	$date_from 	= $data[3];
	$date_to	= $data[4];

	if($date_from!="" && $date_to!="")
	{
		if($db_type==0)
		{
			$date_from=change_date_format(str_replace("'","",$date_from),"yyyy-mm-dd","");
			$date_to=change_date_format(str_replace("'","",$date_to),"yyyy-mm-dd","");
		}
		else
		{
			$date_from=date("j-M-Y",strtotime(str_replace("'","",$date_from)));
			$date_to=date("j-M-Y",strtotime(str_replace("'","",$date_to)));
		}
		$date_con=" and a.batch_date between '$date_from' and '$date_to'";
	}

	if ($search_by == 1)
		$search_field = 'a.batch_no';
	else
		$search_field = 'a.booking_no';

	$batch_cond = "";
	//if ($batch_against_id != 2) $batch_cond = " and a.batch_against=$batch_against_id";
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier", 'id', 'supplier_name');
	$color_arr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');

	$po_name_arr = array();
	if ($db_type == 2) $group_concat = "  listagg(cast(b.po_number AS VARCHAR2(4000)),',') within group (order by b.id) as order_no";
	else if ($db_type == 0) $group_concat = " group_concat(b.po_number) as order_no";

	if ($db_type == 2) $group_concat2 = "  listagg(cast(b.po_id AS VARCHAR2(4000)),',') within group (order by b.id) as po_id";
	else if ($db_type == 0) $group_concat2 = " group_concat(b.po_id) as po_id";

	// Without Sample Batch
	$sql ="SELECT a.id, a.batch_no,a.sales_order_no, a.extention_no, a.batch_weight, a.total_trims_weight, a.batch_date, a.batch_against, a.batch_for, a.booking_no, a.color_id, a.is_sales, a.re_dyeing_from 
	from pro_batch_create_mst a 
	where  a.company_id=$company_id and $search_field like '$search_string' and a.booking_without_order!=1 and a.page_without_roll=0 and a.status_active=1 and a.entry_form=639 and a.is_deleted=0 $date_con  order by a.batch_date desc";
	// echo $sql;
	$result = sql_select($sql);

	if(count($result)<1)
	{
		echo "<span>Data Not Found</span>";die;
	}
	$batch_id=array();
	foreach ($result as $row) 
	{
		$ids = implode(",", array_unique(explode(",", $row[csf("po_id")])));
		$po_ids .= $ids . ",";
		$is_sales[] = $row[csf("is_sales")];
		$batch_id[] .= $row[csf("id")];
	}
	$po_ids = rtrim($po_ids, ",");
	if($po_ids!="") $po_ids=$po_ids;else $po_ids=0;

	$sql_po = sql_select("SELECT a.id,b.po_number from wo_po_break_down b,pro_batch_create_mst a, pro_batch_create_dtls c where a.id=c.mst_id and b.id=c.po_id and a.company_id=$company_id and $search_field like '$search_string' and a.page_without_roll=0 and a.status_active=1 and a.entry_form=0 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $batch_cond $date_con and b.status_active=1 and b.is_deleted=0 ");
	$po_name_arr = array();
	foreach ($sql_po as $p_name) {
		$po_name_arr[$p_name[csf('id')]] .= $p_name[csf('po_number')].',';
	}

	//print_r($re_dyeing_from);
	?>
	<style>
		table tbody tr td {
			text-align: center;
		}
	</style>
	<table class="rpt_table" id="rpt_tabletbl_list_search" rules="all" width="1020" cellspacing="0" cellpadding="0"
	border="0">
	<thead>
		<tr>
			<th width="50">SL No</th>
			<th width="100">Batch No</th>
			<th width="70">Ext. No</th>
			<th width="150">PO No./FSO No</th>
			<th width="105">Booking No</th>
			<th width="80">Batch Weight</th>
			<th width="80">Total Trims Weight</th>
			<th width="80">Batch Date</th>
			<th width="80">Batch Against</th>
			<th width="85">Batch For</th>
			<th>Color</th>
		</tr>
	</thead>
	<tbody>
		<?
		$i = 1;
		foreach ($result as $row)
		{
			if ($row[csf("is_sales")] != 1) 
			{
				$order_id=rtrim($po_name_arr[$row[csf('id')]],',');
				$order_ids=implode(",",array_unique(explode(",",$order_id)));
			}
			else 
			{
				$order_ids = $row[csf("sales_order_no")];
			}
			if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

			$ext_from = 0;
			?>
			<tr onClick="js_set_value(<? echo $row[csf('id')]; ?>,'<? echo $row[csf('batch_no')]; ?>','<? echo $unloaded_batch[$row[csf('id')]]; ?>','<? echo $ext_from;?>','<? echo $row[csf('batch_against')];?>','<? echo $row[csf("is_sales")];?>')" bgcolor="<? echo $bgcolor; ?>" style="cursor:pointer">
				<td width="50"><? echo $i; ?></td>
				<td width="100"><? echo $row[csf("batch_no")]; ?></td>
				<td width="70"><? echo $row[csf("extention_no")]; ?></td>
				<td width="150"><p><? echo trim($order_ids, ","); ?></p></td>
				<td width="105"><? echo $row[csf("booking_no")]; ?></td>
				<td width="80"><? echo $row[csf("batch_weight")]; ?></td>
				<td width="80"><? echo $row[csf("total_trims_weight")]; ?></td>
				<td width="80"><? echo $row[csf("batch_date")]; ?></td>
				<td width="80"><? echo $batch_against[$row[csf("batch_against")]]; ?></td>
				<td width="85"><? echo $batch_for[$row[csf("batch_for")]]; ?></td>
				<td><? echo $color_arr[$row[csf("color_id")]]; ?></td>
			</tr>
			<?
			$i++;
		}
		?>
	</tbody>
	</table>
	<?
	exit();
}

if ($action == "populate_data_from_search_popup_new_batch") // New Batch Number popup onclose
{
	$data = explode("**", $data);
	$batch_id = $data[0];
	$batch_no = $data[1];
	$company_id = $data[2];

	if ($db_type == 0) $year_field = "DATE_FORMAT(insert_date,'%y')";
	else if ($db_type == 2) $year_field = "to_char(insert_date,'YY')";
	else $year_cond = "";//defined Later

	$color_arr = return_library_array("SELECT id, color_name from lib_color where status_active=1 and is_deleted=0", 'id', 'color_name');
	$data_array = sql_select("SELECT id, company_id,working_company_id, batch_no,batch_type_id, extention_no,floor_id, batch_weight, total_trims_weight,save_string, batch_date, batch_against, batch_for, booking_no, booking_no_id,booking_without_order, color_id, from_color_id, re_dyeing_from, color_range_id, organic, process_id, dur_req_hr, dur_req_min, collar_qty, cuff_qty, dyeing_machine, remarks,ready_to_approved,is_approved, $year_field as year, sales_order_no, sales_order_id, is_sales, process_seq, booking_entry_form, service_booking_id, service_booking_no, double_dyeing, batch_sl_no, style_breakdown,shift_id,dyeing_pdo,from_batch_no, from_batch_id from pro_batch_create_mst where id=$batch_id and entry_form=639");

	foreach ($data_array as $row)
	{
		$process_name = '';
		$process_id_array = explode(",", $row[csf("process_id")]);
		foreach ($process_id_array as $val)
		{
			if ($process_name == "")
				$process_name = $conversion_cost_head_array[$val];
			else
				$process_name .= "," . $conversion_cost_head_array[$val];
		}

		if ($row[csf("is_sales")] != 1) 
		{
			echo "document.getElementById('txt_booking_no').value = '" . $row[csf("booking_no")] . "';\n";
		} 
		else 
		{
			echo "document.getElementById('txt_booking_no').value = '" . $row[csf("sales_order_no")] . "';\n";
			echo "document.getElementById('txt_sales_booking_no').value = '" . $row[csf("booking_no")] . "';\n";
			echo "document.getElementById('txt_sales_id').value = '" . $row[csf("sales_order_id")] . "';\n";
			echo "document.getElementById('txt_search_type').value = '7';\n";
		}
		echo "document.getElementById('txt_batch_sl_no').value = '" . $row[csf("batch_sl_no")] . "';\n";
		echo "document.getElementById('txt_batch_weight').value = '" . $row[csf("batch_weight")] . "';\n";
		echo "document.getElementById('cbo_company_id').value = '" . $row[csf("company_id")] . "';\n";
		echo "document.getElementById('cbo_working_company_id').value = '" . $row[csf("working_company_id")] . "';\n";
		echo "document.getElementById('txt_tot_trims_weight').value = '" . $row[csf("total_trims_weight")] . "';\n";
		echo "document.getElementById('save_data').value = '" . $row[csf("save_string")] . "';\n";		
		echo "document.getElementById('txt_new_batch_number').value = '" . $row[csf("batch_no")] . "';\n";
		echo "document.getElementById('update_id').value = '" . $row[csf("id")] . "';\n";//update_id
		echo "document.getElementById('txt_from_batch_number').value = '" . $row[csf("from_batch_no")] . "';\n";
		echo "document.getElementById('hiddden_from_batch_id').value = '" . $row[csf("from_batch_id")] . "';\n";
		echo "document.getElementById('txt_new_batch_color_id').value = '" . $row[csf("color_id")] . "';\n";
		echo "document.getElementById('txt_new_batch_color').value = '" . $color_arr[$row[csf("color_id")]] . "';\n";
		echo "document.getElementById('txt_batch_color_id').value = '" . $row[csf("from_color_id")] . "';\n";
		echo "document.getElementById('txt_batch_color').value = '" . $color_arr[$row[csf("from_color_id")]] . "';\n";

		echo "load_drop_down('requires/independent_striping_batch_creation_controller','".$row[csf("working_company_id")]."', 'load_drop_down_floor', 'td_floor' );\n";
		echo "document.getElementById('cbo_floor').value = '" . $row[csf("floor_id")] . "';\n";
		echo "document.getElementById('txt_booking_no_id').value = '" . $row[csf("booking_no_id")] . "';\n";
		echo "document.getElementById('booking_without_order').value = '" . $row[csf("booking_without_order")] . "';\n";
		echo "document.getElementById('cbo_color_range').value = '" . $row[csf("color_range_id")] . "';\n";
		echo "document.getElementById('txt_process_id').value = '" . $row[csf("process_id")] . "';\n";
		echo "document.getElementById('txt_process_name').value = '" . $process_name . "';\n";
		echo "document.getElementById('txt_process_seq').value = '" . $row[csf("process_seq")] . "';\n";
		echo "document.getElementById('txt_du_req_hr').value = '" . $row[csf("dur_req_hr")] . "';\n";
		echo "document.getElementById('txt_du_req_min').value = '" . $row[csf("dur_req_min")] . "';\n";
		echo "document.getElementById('txt_collar_qty').value = '" . $row[csf("collar_qty")] . "';\n";
		echo "document.getElementById('txt_cuff_qty').value = '" . $row[csf("cuff_qty")] . "';\n";
		echo "document.getElementById('txt_remarks').value = '" . $row[csf("remarks")] . "';\n";
		
		echo "load_drop_down('requires/independent_striping_batch_creation_controller','".$row[csf("floor_id")]."', 'load_drop_machine', 'td_dyeing_machine');\n";
		echo "document.getElementById('cbo_machine_name').value = '" . $row[csf("dyeing_machine")] . "';\n";
		echo "document.getElementById('cbo_double_dyeing').value = '" . $row[csf("double_dyeing")] . "';\n";
		echo "document.getElementById('cbo_shift_name').value = '" . $row[csf("shift_id")] . "';\n";
		echo "document.getElementById('txt_dyeing_pdo').value = '" . $row[csf("dyeing_pdo")] . "';\n";

		echo "set_button_status(1, '" . $_SESSION['page_permission'] . "', 'fnc_batch_creation',1);\n";

		echo "$('#txt_from_batch_number').attr('disabled',true);\n";
		echo "$('#txt_booking_no').attr('disabled',true);\n";
		echo "$('#cbo_working_company_id').attr('disabled',true);\n";
		echo "$('#txt_new_batch_number').attr('readonly',true);\n";
	}

	exit();
}

if ($action == 'batch_details_update') // ISBC saved data
{
	$data = explode('**', $data);
	$batch_id = $data[0];
	$roll_maintained = $data[1];

	$data_array = sql_select("SELECT a.batch_against,a.company_id, a.booking_no, a.re_dyeing_from, a.color_id, a.booking_without_order,a.is_sales,a.sales_order_id, a.from_batch_id, a.from_batch_no, a.from_color_id, b.id, b.dtls_id as from_batch_dtls_id, b.program_no, b.po_id, b.prod_id, b.item_description, b.body_part_id, b.width_dia_type, b.roll_no, b.roll_id, b.barcode_no, b.batch_qnty, b.batch_qty_pcs, b.po_batch_no, b.color_type, b.item_size, b.remarks 
	from pro_batch_create_mst a, pro_batch_create_dtls b where a.id=b.mst_id and b.mst_id=$batch_id and a.entry_form=639 and b.status_active=1 and b.is_deleted=0");

	$fso_data_array = return_library_array("SELECT a.id, a.job_no FROM fabric_sales_order_mst a, pro_batch_create_dtls b WHERE a.id=b.po_id and b.mst_id=$batch_id and b.status_active=1 and b.is_deleted=0 group by a.id, a.job_no", 'id', 'job_no');

	$po_array = return_library_array("SELECT a.id, a.po_number FROM wo_po_break_down a, pro_batch_create_dtls b WHERE a.id=b.po_id and b.mst_id=$batch_id and b.status_active=1 and b.is_deleted=0 group by a.id, a.po_number", 'id', 'po_number');

	//$fab_description_array = return_library_array("SELECT a.id, a.product_name_details from product_details_master a, pro_batch_create_dtls b where a.id=b.prod_id and b.mst_id=$batch_id and b.status_active=1 and b.is_deleted=0 group by a.id, a.product_name_details", 'id', 'product_name_details');

	$tblRow = 0;
	foreach ($data_array as $row)
	{
		if ($row[csf('is_sales')] == 1)
		{
			$po_no=$fso_data_array[$row[csf('po_id')]];
		}
		else
		{
			$po_no=$po_array[$row[csf('po_id')]];
		}

		$tblRow++;
		?>
		<tr id="tr_<? echo $tblRow; ?>">
			<td id="slTd_<? echo $tblRow; ?>"><? echo $tblRow; ?></td>
			<td style="word-break:break-all; width: 80" id="cboProgramNo_<? echo $tblRow; ?>"><? echo $row[csf('program_no')];?></td>
			<td style="word-break:break-all; width: 130" id="cboPoNo_<? echo $tblRow; ?>"><? echo $po_no;?></td>
			<td style="word-break:break-all; width: 180" id="cboItemDesc_<? echo $tblRow; ?>"><? echo $row[csf('item_description')];?></td>
			<td style="word-break:break-all; width: 120" id="cboBodyPart_<? echo $tblRow; ?>"><? echo $body_part[$row[csf('body_part_id')]];?></td>
			<td style="word-break:break-all; width: 90" id="cboDiaWidthType_<? echo $tblRow; ?>"><? echo $fabric_typee[$row[csf('width_dia_type')]];?></td>
			<td style="word-break:break-all; width: 50" id="txtRollNo_<? echo $tblRow; ?>"><? if ($row[csf('roll_no')] != 0) echo $row[csf('roll_no')];?></td>
			<td style="word-break:break-all; width: 90" id="barcodeNo_<? echo $tblRow; ?>"><? echo $row[csf('barcode_no')];?></td>
			<td>
				<input type="text" name="txtBatchQnty[]" id="txtBatchQnty_<? echo $tblRow; ?>"
				class="text_boxes_numeric" onKeyUp="calculate_batch_qnty();" style="width:60px" value="<? echo $row[csf('batch_qnty')]; ?>" disabled/>
			</td>			
			<td>
				<input type="text" name="txtQtyPcs[]" id="txtQtyPcs_<? echo $tblRow; ?>"
				class="text_boxes_numeric" onKeyUp="calculate_qtyPcs();" style="width:60px" value="<? echo $row[csf('batch_qty_pcs')]; ?>" disabled/>
			</td>
			<td style="word-break:break-all; width: 60" id="txtSize_<? echo $tblRow; ?>"><? echo $row[csf('item_size')]; ?></td>
			<td style="word-break:break-all; width: 60" id="txtPoBatchNo_<? echo $tblRow; ?>"><? echo $row[csf('po_batch_no')]; ?></td>
			<td style="word-break:break-all; width: 100" id="cboColorType_<? echo $tblRow; ?>"><? echo $color_type[$row[csf("color_type")]]; ?>
			</td>
			<td>
                <input type="text" name="txtRemarks[]" id="txtRemarks_<? echo $tblRow; ?>" class="text_boxes" style="width:100px;" value="<? echo $row[csf('remarks')]; ?>"/>
            </td>
			<td width="65">
				<input type="button" id="decrease_<? echo $tblRow; ?>" name="decrease[]" style="width:30px"
				class="formbuttonplasminus" value="-" onClick="fn_deleteRow(<? echo $tblRow; ?>);"/>

				<input type="hidden" name="programNo[]" id="programNo_<? echo $tblRow; ?>" value="<? echo $row[csf('program_no')]; ?>"/>
				<input type="hidden" name="poId[]" id="poId_<? echo $tblRow; ?>" value="<? echo $row[csf('po_id')]; ?>"/>
				<input type="hidden" name="productId[]" id="productId_<? echo $tblRow; ?>" value="<? echo $row[csf('prod_id')]; ?>"/>
				<input type="hidden" name="bodyPartId[]" id="bodyPartId_<? echo $tblRow; ?>" value="<? echo $row[csf('body_part_id')]; ?>"/>
				<input type="hidden" name="widthDiaType[]" id="widthDiaType_<? echo $tblRow; ?>" value="<? echo $row[csf('width_dia_type')]; ?>"/>
				<input type="hidden" name="hideRollNo[]" id="hideRollNo_<? echo $tblRow; ?>" value="<? if ($row[csf('roll_no')] != 0) echo $row[csf('roll_no')]; ?>"/>
				<input type="hidden" name="rollId[]" id="rollId_<? echo $tblRow; ?>" value="<? echo $row[csf('roll_id')]; ?>"/>
				<input type="hidden" name="barcodeNo[]" id="barcodeNo_<? echo $tblRow; ?>" value="<? echo $row[csf('barcode_no')]; ?>"/>
				<input type="hidden" name="batchQty[]" id="batchQty_<? echo $tblRow; ?>" value="<? echo $row[csf('batch_qnty')]; ?>"/>
                <input type="hidden" name="batchQtyPcs[]" id="batchQtyPcs_<? echo $tblRow; ?>" value="<? echo $row[csf('batch_qty_pcs')]; ?>"/>
                <input type="hidden" name="itemSize[]" id="itemSize_<? echo $tblRow; ?>" value="<? echo $item_size; ?>"/>
                <input type="hidden" name="poBatchNo[]" id="poBatchNo_<? echo $tblRow; ?>" value="<? echo $row[csf('po_batch_no')]; ?>"/>
				<input type="hidden" name="colorTypeId[]" id="colorTypeId_<? echo $tblRow; ?>" value="<? echo $row[csf("color_type")]; ?>"/>
				<input type="hidden" name="fromBatchDtlsId[]" id="fromBatchDtlsId_<? echo $tblRow; ?>" value="<? echo $row[csf("from_batch_dtls_id")]; ?>"/>
				<input type="hidden" name="isSalesOrder[]" id="isSalesOrder_<? echo $tblRow; ?>" value="<? echo $row[csf("is_sales")]; ?>"/>
				<input type="hidden" name="updateIdDtls[]" id="updateIdDtls_<? echo $tblRow; ?>" value="<? echo $row[csf("id")]; ?>"/>

			</td>
		</tr>
		<?
	}

	exit();
}

if ($action == "process_name_popup") 
{
	echo load_html_head_contents("Process Name Info", "../../", 1, 1, '', '1', '');
	extract($_REQUEST);
	?>
	<script>

		$(document).ready(function (e) {
			setFilterGrid('tbl_list_search', -1);
		});

		var selected_id = new Array();
		var selected_name = new Array();

		function check_all_data() {
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;

			tbl_row_count = tbl_row_count - 1;
			for (var i = 1; i <= tbl_row_count; i++) {
				js_set_value(i);
			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor ) ? origColor : newColor;
			}
		}

		function set_all() {
			var old_seq = document.getElementById('txt_process_seq').value;
			var old = document.getElementById('txt_process_row_id').value;
			if (old != "") {
				old = old.split(",");
				if(old_seq!=""){
					oldArr = old_seq.split(",");
				}

				for (var k = 0; k < old.length; k++) {
					if(old_seq!=""){
						idSeq = oldArr[k].split("_");
						$('#txt_sequence'+idSeq[0]).val(idSeq[1]);
						//$('#txt_sequence'+old[k]).val(oldArr[k]);
					}

					js_set_value(old[k]);
				}
			}
		}

		function js_set_value(str) {
            /*var currentRowColor=document.getElementById( 'search' + str ).style.backgroundColor;
             if(currentRowColor=='yellow')
             {
             var mandatory=$('#txt_mandatory' + str).val();
             var process_name=$('#txt_individual' + str).val();
             if(mandatory==1)
             {
             alert(process_name+" Subprocess is Mandatory. So You can't De-select");
             return;
             }
         }*/

         toggle(document.getElementById('search' + str), '#FFFFCC');

         if (jQuery.inArray($('#txt_individual_id' + str).val(), selected_id) == -1) {
         	selected_id.push($('#txt_individual_id' + str).val());
         	selected_name.push($('#txt_individual' + str).val());
         }
         else {
         	for (var i = 0; i < selected_id.length; i++) {
         		if (selected_id[i] == $('#txt_individual_id' + str).val()) break;
         	}
         	selected_id.splice(i, 1);
         	selected_name.splice(i, 1);
         }

         var id = '';
         var name = '';
         for (var i = 0; i < selected_id.length; i++) {
         	id += selected_id[i] + ',';
         	name += selected_name[i] + ',';
         }

         id = id.substr(0, id.length - 1);
         name = name.substr(0, name.length - 1);

         $('#hidden_process_id').val(id);
         $('#hidden_process_name').val(name);
     }

     function window_close(){

     	var old = document.getElementById('hidden_process_id').value;
     	if (old != "") {
     		old = old.split(",");
     		var seq='';
     		for (var k = 0; k < old.length; k++) {
     			if(seq==''){seq=old[k]+'_'+$('#txt_sequence'+old[k]).val();}
     			else{seq+=','+old[k]+'_'+$('#txt_sequence'+old[k]).val();}
     		}
     	}
     	$('#hidden_process_seq').val(seq);
			//var oldArr = old_seq.split(",");


			parent.emailwindow.hide();
		}

	</script>

	</head>

	<body>
		<div align="center">
			<fieldset style="width:370px;margin-left:10px">
				<input type="hidden" name="hidden_process_id" id="hidden_process_id" class="text_boxes" value="">
				<input type="hidden" name="hidden_process_seq" id="hidden_process_seq" class="text_boxes" value="">
				<input type="hidden" name="hidden_process_name" id="hidden_process_name" class="text_boxes"
				value="">
				<form name="searchprocessfrm_1" id="searchprocessfrm_1" autocomplete="off">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="350" class="rpt_table">
						<thead>
							<th width="50">SL</th>
							<th>Process Name</th>
							<th width="82">Sequence</th>
						</thead>
					</table>
					<div style="width:350px; overflow-y:scroll; max-height:280px;" id="buyer_list_view"
					align="center">
					<table cellspacing="0" cellpadding="0" border="1" rules="all" width="332" class="rpt_table"
					id="tbl_list_search">
					<?
					$i = 1;
					$process_row_id = '';
							$not_process_id_print_array = array(1, 2, 3, 4, 101, 120, 121, 122, 123, 124); //$mandatory_subprocess_array=array(33,63,65,94);
							$hidden_process_id = explode(",", $txt_process_id);
							foreach ($conversion_cost_head_array as $id => $name) {
								if (!in_array($id, $not_process_id_print_array)) {
									if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

									if (in_array($id, $hidden_process_id)) {
										if ($process_row_id == "") $process_row_id = $i; else $process_row_id .= "," . $i;
									}
									/*$mandatory=0;
								if(in_array($id,$mandatory_subprocess_array))
								{
									$mandatory=1;
								}*/
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i; ?>" onClick="js_set_value(<? echo $i; ?>)">
									<td width="50" align="center"><?php echo "$i"; ?>
									<input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $id; ?>"/>
									<input type="hidden" name="txt_individual" id="txt_individual<?php echo $i ?>" value="<? echo $name; ?>"/>
									<input type="hidden" name="txt_mandatory" id="txt_mandatory<?php echo $i ?>" value="<? echo $mandatory; ?>"/>
								</td>
								<td><p><? echo $name; ?></p></td>
								<td width="65" align="center"><input type="text" id="txt_sequence<? echo $id ?>" name="txt_sequence<? echo $id ?>" value="" class="text_boxes_numeric" style=" width:50px;"></td>
							</tr>
							<?
							$i++;
						}
					}
					?>
					<input type="hidden" name="txt_process_row_id" id="txt_process_row_id" value="<?php echo $process_row_id; ?>"/>

					<input type="hidden" name="txt_process_seq" id="txt_process_seq" value="<?php echo $process_seq; ?>"/>



				</table>
			</div>
			<table width="350" cellspacing="0" cellpadding="0" style="border:none" align="center">
				<tr>
					<td align="center" height="30" valign="bottom">
						<div style="width:100%">
							<div style="width:50%; float:left" align="left">
								<input type="checkbox" name="check_all" id="check_all"
								onClick="check_all_data()"/>
								Check / Uncheck All
							</div>
							<div style="width:50%; float:left" align="left">
								<input type="button" name="close" onClick="window_close()"
								class="formbutton" value="Close" style="width:100px"/>
							</div>
						</div>
					</td>
				</tr>
			</table>
		</form>
	</fieldset>
	</div>
	</body>
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	<script>
		set_all();
	</script>
	</html>
	<?
	exit();
}

if ($action == "batch_no_creation")
{
	$batch_maintained = '';
	$sql = sql_select("select variable_list, batch_maintained, yes_no_permission from variable_settings_production where company_name=$data and variable_list in (13) and status_active=1 and is_deleted=0");
	foreach ($sql as $row) 
	{
		$batch_maintained = $row[csf('batch_maintained')];
	}

	if ($batch_maintained != 1) $batch_maintained = 0;
	echo "document.getElementById('batch_maintained').value 				= '" . $batch_maintained . "';\n";
	echo "$('#txt_from_batch_number').val('');\n";
	echo "$('#hiddden_from_batch_id').val('');\n";
	exit();
}

if ($action == "roll_maintained") 
{
	//Add New category id 50, old=category id was 3
	$roll_maintained = 0;
	$nameArray= sql_select("select fabric_roll_level from variable_settings_production where company_name='$data' and item_category_id=50 and variable_list=3 and status_active=1 and is_deleted= 0 order by id");

	foreach($nameArray as $row)
	{
		$roll_maintained = $row[csf('fabric_roll_level')];
	}

	if ($roll_maintained == "" || $roll_maintained == 2) $roll_maintained = 0; else $roll_maintained = $roll_maintained;
	echo "document.getElementById('roll_maintained').value 				= '" . $roll_maintained . "';\n";
	exit();
}

if ($action == "trims_weight_popup") 
{
	echo load_html_head_contents("Trims Weight Entry", "../../", 1, 1, '', '', '');
	extract($_REQUEST);
	//echo $save_data;

	?>
	<script>
		var permission = '<? echo $permission; ?>';
		function fn_addRow_trims(i) {
            //var row_num=$('#tbl_item_details tbody tr').length;
            //var lastTrId = $('#tbl_list tbody tr:last').attr('id').split('_');
            //alert(lastTrId);
            //var row_num=lastTrId[1];
            var row_num = $('#tbl_list tbody tr').length;
            //alert(lastTrId[1]);
            if (row_num != i) {
            	return false;
            }
            else {
            	i++;

            	$("#tbl_list tbody tr:last").clone().find("input,select").each(function () {

            		$(this).attr({
            			'id': function (_, id) {
            				var id = id.split("_");
            				return id[0] + "_" + i
            			},
            			'name': function (_, name) {
            				return name
            			},
            			'value': function (_, value) {
            				return value
            			}
            		});

            	}).end().appendTo("#tbl_list");

            	$('#slTd_' + i).val('');
            	$('#txtitemDesc_' + i).val('');
            	$('#trimsWeight_' + i).val('');
            	$('#remarks_' + i).val('');
            	$("#tbl_list tbody tr:last").removeAttr('id').attr('id', 'tr_' + i);
            	$('#tr_' + i).find("td:eq(0)").removeAttr('id').attr('id', 'slTd_' + i);
            	$('#tr_' + i).find("td:eq(0)").text(i);

            	$('#increase_' + i).removeAttr("value").attr("value", "+");
            	$('#decrease_' + i).removeAttr("value").attr("value", "-");
            	$('#increase_' + i).removeAttr("onclick").attr("onclick", "fn_addRow_trims(" + i + ");");
            	$('#decrease_' + i).removeAttr("onclick").attr("onclick", "fn_deleteRow(" + i + ");");
            }
            set_all_onclick();
        }

        function fn_deleteRow(rowNo) {

        	var row_num = $('#tbl_list tbody tr').length;

        	if (row_num != 1) {
                //alert(row_num);
                $("#tr_" + rowNo).remove();
            }
        }

        function window_close() {
        	var save_data = '';
        	var tot_trims_qnty = '';

        	$("#tbl_list").find('tr').each(function () {
        		var txtitemDesc = $(this).find('input[name="txtitemDesc[]"]').val();
        		var trimsWeight = $(this).find('input[name="trimsWeight[]"]').val();
        		var remarks = $(this).find('input[name="remarks[]"]').val();
        		if (trimsWeight * 1 > 0) {

        			if (save_data == "") {
        				save_data = txtitemDesc + "_" + trimsWeight + "_" + remarks;
        			}
        			else {
        				save_data += "!!" + txtitemDesc + "_" + trimsWeight + "_" + remarks;
        			}
        			tot_trims_qnty = tot_trims_qnty * 1 + trimsWeight * 1;

        		}

        	});
            //alert(tot_trims_qnty);
            $('#save_data').val(save_data);
            $('#tot_trims_qnty').val(tot_trims_qnty);
            parent.emailwindow.hide();
        }

        function calculate_trims_qnty() {
        	var total_trims_qnty = '';
        	$("#tbl_list tbody").find('tr').each(function () {
        		var trimsQnty = $(this).find('input[name="trimsWeight[]"]').val();
        		total_trims_qnty = total_trims_qnty * 1 + trimsQnty * 1;
        	});

        	$('#txt_total_trims_qnty').val(total_trims_qnty.toFixed(2));

        }

    </script>

	</head>

	<body>
		<div align="center">
			<? echo load_freeze_divs("../../", $permission, 1); ?>
			<form name="trimsWeight_1" id="trimsWeight_1">

				<fieldset style="width:600px; margin-top:10px">
					<legend>Batch Trims Pop Up</legend>
					<?
					if ($save_data != "") {
						?>
						<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="600"
						id="tbl_list">
						<thead>
							<th width="40">SL</th>
							<th width="200">Item Description</th>
							<th width="80">Weight In Kg</th>
							<th width="150">Remarks</th>
							<th></th>
							<input type="hidden" name="save_data" id="save_data" class="text_boxes">
							<input type="hidden" name="tot_trims_qnty" id="tot_trims_qnty" class="text_boxes">
						</thead>
						<tbody>

							<?
							$tot_trims_wgt = 0;
							$k = 0;
							$explSaveData = explode("!!", $save_data);
							for ($z = 0; $z < count($explSaveData); $z++) {
								$data_all = explode("_", $explSaveData[$z]);
								$item_des = $data_all[0];
								$trims_wgt = $data_all[1];
								$remark = $data_all[2];
								$tot_trims_wgt += $trims_wgt;
								$k++;

								?>
								<tr id="tr_<? echo $k; ?>">
									<td id="slTd_<? echo $k; ?>" width="30"><? echo $k; ?></td>
									<td>
										<input type="text" name="txtitemDesc[]" id="txtitemDesc_<? echo $k; ?>"
										class="text_boxes" style="width:200px;" value="<? echo $item_des; ?>"/>
									</td>
									<td>
										<input type="text" name="trimsWeight[]" id="trimsWeight_<? echo $k; ?>"
										class="text_boxes_numeric" style="width:80px;"
										onKeyUp="calculate_trims_qnty();" value="<? echo $trims_wgt; ?>"/>
									</td>
									<td>
										<input type="text" name="remarks[]" id="remarks_<? echo $k; ?>"
										class="text_boxes"
										style="width:150px;" value="<? echo $remark; ?>"/>
									</td>

									<td>
										<input type="button" id="increase_<? echo $k; ?>" name="increase[]"
										style="width:30px" class="formbuttonplasminus" value="+"
										onClick="fn_addRow_trims(<? echo $k; ?>)"/>
										<input type="button" id="decrease_<? echo $k; ?>" name="decrease[]"
										style="width:30px" class="formbuttonplasminus" value="-"
										onClick="fn_deleteRow(<? echo $k; ?>);"/>
									</td>
								</tr>
								<?
							}
							?>

						</tbody>
						<tfoot class="tbl_bottom">
							<td>&nbsp;</td>

							<td>Sum</td>
							<td><input type="text" name="txt_total_trims_qnty" id="txt_total_trims_qnty"
								class="text_boxes_numeric" style="width:80px" readonly
								value="<? echo $tot_trims_wgt; ?>"/></td>

								<td colspan="2">&nbsp;</td>
							</tfoot>
						</table>
						<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="600">

							<tr>
								<td colspan="5" align="center">

									<input type="button" name="close" class="formbutton" value="Close" id="main_close"
									onClick="window_close();" style="width:80px"/>

								</td>
							</tr>
						</table>

						<?
					} else { ?>
						<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="600"
						id="tbl_list">
						<thead>
							<th width="40">SL</th>
							<th width="200">Item Description</th>
							<th width="80">Weight In Kg</th>
							<th width="150">Remarks</th>
							<th></th>
							<input type="hidden" name="save_data" id="save_data" class="text_boxes">
							<input type="hidden" name="tot_trims_qnty" id="tot_trims_qnty" class="text_boxes">
						</thead>
						<tbody>
							<tr id="tr_1">
								<td id="slTd_1" width="30">1</td>
								<td>
									<input type="text" name="txtitemDesc[]" id="txtitemDesc_1" class="text_boxes"
									style="width:200px;"/>
								</td>
								<td>
									<input type="text" name="trimsWeight[]" id="trimsWeight_1"
									class="text_boxes_numeric"
									style="width:80px;" onKeyUp="calculate_trims_qnty();"/>
								</td>
								<td>
									<input type="text" name="remarks[]" id="remarks_1" class="text_boxes"
									style="width:150px;"/>
								</td>

								<td>
									<input type="button" id="increase_1" name="increase[]" style="width:30px"
									class="formbuttonplasminus" value="+" onClick="fn_addRow_trims(1)"/>
									<input type="button" id="decrease_1" name="decrease[]" style="width:30px"
									class="formbuttonplasminus" value="-" onClick="fn_deleteRow(1);"/>
								</td>
							</tr>

						</tbody>
						<tfoot class="tbl_bottom">
							<td>&nbsp;</td>

							<td>Sum</td>
							<td><input type="text" name="txt_total_trims_qnty" id="txt_total_trims_qnty"
								class="text_boxes_numeric" style="width:80px" readonly/></td>

								<td colspan="2">&nbsp;</td>
							</tfoot>
						</table>
						<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="600">

							<tr>
								<td colspan="5" align="center">

									<input type="button" name="close" class="formbutton" value="Close" id="main_close"
									onClick="window_close();" style="width:80px"/>

								</td>
							</tr>
						</table>
					<? }
					?>
				</fieldset>
			</form>
		</div>
	</body>

	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
}

if ($action == "load_process_loss_from_fso") //  FSO/Book. No onClose
{
	$sql_result = sql_select("SELECT process_seq_main from fabric_sales_order_dtls where status_active=1 and is_deleted=0 and mst_id=$data and process_seq_main is not null");
	if ($sql_result)
	{
		foreach ($sql_result as $result)
		{
			//0__0__0__0@@0__0__0__0__457_1_0,458_2_0,31_3_0__0@@0__0______0____0@@0__0____0@@0__0
			$txtProcessWiseRateArr =explode("@@", $result[csf("process_seq_main")]);
			$dyeProcessRateArr = $txtProcessWiseRateArr[1];
			$dyeProcessRateArrDtls = explode("__",$dyeProcessRateArr);  //1__1__1__107__60_1_,289_2___0
			$DYEING_SUBPROCESS = $dyeProcessRateArrDtls[4];
			$dye_sub_f = explode(",",$DYEING_SUBPROCESS);
			foreach ($dye_sub_f as $subr )
			{
				$dye_sub_s = explode("_",$subr);
				$txtProcessIdDyeing .= $dye_sub_s[0].",";
				$txtProcessIdDyeingSeq .= $dye_sub_s[0]."_".$dye_sub_s[1].",";
				$txtProcessNameDyeing .= $conversion_cost_head_array[$dye_sub_s[0]].",";
			}
		}

		$txtProcessNameDyeing = chop($txtProcessNameDyeing,",");
		$txtProcessIdDyeing = chop($txtProcessIdDyeing,",");
		$txtProcessIdDyeingSeq = chop($txtProcessIdDyeingSeq,",");

		if($txtProcessIdDyeing)
		{
			echo "$('#txt_process_name').val('" . $txtProcessNameDyeing . "');\n";
			echo "$('#txt_process_id').val('" . $txtProcessIdDyeing . "');\n";
			echo "$('#txt_process_seq').val('" . $txtProcessIdDyeingSeq  . "');\n";
		}
	}
	exit();
}
?>