<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');


$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_floor")
{
	echo create_drop_down( "cbo_floor_id", 130, "select a.id,a.floor_name from lib_prod_floor a,lib_machine_name b where a.id=b.floor_id and  a.status_active =1 and b.status_active=1 and a.company_id='$data'  group by a.id,a.floor_name order by a.floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "",0 );
}

if ($action == "style_ref_search_popup")
{
	//echo load_html_head_contents("Style Reference / Job No. Info", "../../../", 1, 1, '', '', '');
	echo load_html_head_contents("Style Reference / Job No. Info","../../../", 1, 1, $unicode); 
	extract($_REQUEST);

	?>

	<script>

		var selected_id = new Array;
		var selected_name = new Array;

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

		function js_set_value(str) {

			toggle(document.getElementById('search' + str), '#FFFFCC');

			if (jQuery.inArray($('#txt_job_id' + str).val(), selected_id) == -1) {
				selected_id.push($('#txt_job_id' + str).val());
				selected_name.push($('#txt_job_no' + str).val());

			}
			else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == $('#txt_job_id' + str).val()) break;
				}
				selected_id.splice(i, 1);
				selected_name.splice(i, 1);
			}
			var id = '';
			var name = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}

			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);

			$('#hide_job_id').val(id);
			$('#hide_job_no').val(name);
		}

	</script>

	</head>

	<body>
		<div align="center">
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:600px;">
					<table width="590" cellspacing="0" cellpadding="0" border="1" rules="all" align="center"
					class="rpt_table" id="tbl_list">
					<thead>
						<th>Within Group</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="170">Please Enter Sales Order No</th>
						<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:90px;"></th>
						<input type="hidden" name="hide_job_no" id="hide_job_no" value=""/>
						<input type="hidden" name="hide_job_id" id="hide_job_id" value=""/>
					</thead>
					<tbody>
						<tr>
							<td id="buyer_td" id="must_entry_form">
								<?
								echo create_drop_down("cbo_withing_group", 130, $yes_no,"", 1, "-- Select --", $selected, "");
								?>
							</td>
							<td align="center">
								<?
								$search_by_arr = array(1 => "FSO No", 2 => "Fabric Booking No",3=>"Style Ref");
								$dd = "change_search_event(this.value, '0*0*0', '0*0*0', '../../../') ";
								echo create_drop_down("cbo_search_by", 110, $search_by_arr, "",0, "--Select--", "", $dd, 0);
								?>
							</td>
							<td align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common"
								id="txt_search_common"/>
							</td>
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show"
								onClick="show_list_view ('<? echo $companyID; ?>**' +'<? echo $cbo_knitting_source; ?>'+'**'+document.getElementById('cbo_withing_group').value + '**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**', 'create_job_search_list_view', 'search_div', 'program_against_knitting_balance_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');"
								style="width:90px;"/>
							</td>
						</tr>
					</tbody>
				</table>
				<div style="margin-top:05px" id="search_div"></div>
			</fieldset>
		</form>
		</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
  </html>
  <?
  exit();
}
if ($action == "create_job_search_list_view")
{
	$data = explode('**', $data);
	//print_r($data);die;
	$company_arr = return_library_array("select id,company_short_name from lib_company", 'id', 'company_short_name');
	$buyer_arr = return_library_array("select id, short_name from lib_buyer", 'id', 'short_name');

	$company_id = $data[0];
	$cbo_knitting_source = $data[1];
	$within_group = $data[2];

	$search_by = $data[3];
	$search_string = trim($data[4]);

	$search_field_cond = '';
	if ($search_string != "") {
		if ($search_by == 1) {
			$search_field_cond = " and a.job_no like '%" . $search_string . "'";
		} else if($search_by == 2) {
			$search_field_cond = " and LOWER(a.sales_booking_no) like LOWER('%" . $search_string . "%')";
		}else{
			$search_field_cond = " and LOWER(a.style_ref_no) like LOWER('%" . $search_string . "%')";
		}
	}

	if ($within_group == 0 || $within_group=="") $within_group_cond = "and within_group in (1,2)"; else $within_group_cond = " and within_group=$within_group";
	
	if ($db_type == 0) $year_field = "YEAR(a.insert_date) as year";
	else if ($db_type == 2) $year_field = "to_char(a.insert_date,'YYYY') as year";
	else $year_field = "";//defined Later
	if ($within_group == 1) {
		$sql = " select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id, b.buyer_id po_buyer,b.booking_no_prefix_num from fabric_sales_order_mst a,wo_booking_mst b where a.sales_booking_no = b.booking_no and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id and within_group=$within_group $search_field_cond  and fabric_source in(1,2) order by a.id
		union all
		select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id, b.buyer_id po_buyer,b.booking_no_prefix_num from fabric_sales_order_mst a,wo_non_ord_samp_booking_mst b,wo_non_ord_samp_booking_dtls c where a.sales_booking_no = b.booking_no and b.booking_no=c.booking_no and a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id and within_group=$within_group $search_field_cond   and  (b.fabric_source in(1,2) or c.fabric_source in(1,2)) group by a.id, a.insert_date, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id, b.buyer_id,b.booking_no_prefix_num,c.fabric_source order by a.id";

	} else {
		$sql = " select a.id, $year_field, a.job_no_prefix_num, a.job_no, a.within_group, a.sales_booking_no booking_no_prefix_num, a.booking_date, a.buyer_id, a.style_ref_no, a.location_id from fabric_sales_order_mst a where a.status_active=1 and a.is_deleted=0 and a.company_id=$company_id  $within_group_cond $search_field_cond order by a.id";
	}
	//echo $sql;//die;
	$result = sql_select($sql);
	?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table">
			<thead>
				<th width="40">SL</th>
				<th width="70">Sales Order No</th>
				<th width="60">Year</th>
				<th width="80">Within Group</th>
				<th width="70">PO Buyer</th>
				<th width="70">PO Company</th>
				<th width="120">Sales/ Booking No</th>
				<th>Style Ref.</th>
			</thead>
		</table>
	<div style="width:600px; max-height:260px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="580" class="rpt_table"
		id="tbl_list_search">
			<?
			$i = 1;
			foreach ($result as $row)
			{
				if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";

				if ($row[csf('within_group')] == 1)
					$buyer = $company_arr[$row[csf('buyer_id')]];
				else
					$buyer = $buyer_arr[$row[csf('buyer_id')]];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer"
					onClick="js_set_value(<? echo $i; ?>);" id="search<? echo $i; ?>">
					<td width="40"><? echo $i; ?>
					<input type="hidden" name="txt_job_id" id="txt_job_id<?php echo $i ?>"
					value="<? echo $row[csf('id')]; ?>"/>
					<input type="hidden" name="txt_job_no" id="txt_job_no<?php echo $i ?>"
					value="<? echo $row[csf('job_no')]; ?>"/>
					</td>
					<td width="70"><p>&nbsp;<? echo $row[csf('job_no_prefix_num')]; ?></p></td>
					<td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
					<td width="80" align="center"><p><? echo $yes_no[$row[csf('within_group')]]; ?>&nbsp;</p></td>
					<td width="70"><p><? echo $buyer_arr[$row[csf('po_buyer')]]; ?>&nbsp;</p></td>
					<td width="70" align="center"><p><? echo $buyer; ?>&nbsp;</p></td>
					<td width="120" align="center"><p><? echo $row[csf('booking_no_prefix_num')]; ?></p></td>
					<td><p><? echo $row[csf('style_ref_no')]; ?></p></td>
				</tr>
				<?
				$i++;
			}
			?>
		</table>
	</div>
	<table width="600" cellspacing="0" cellpadding="0" style="border:none" align="center">
		<tr>
			<td align="center" height="30" valign="bottom">
				<div style="width:100%">
					<div style="width:50%; float:left" align="left">
						<input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()"/> Check /
						Uncheck All
					</div>
					<div style="width:50%; float:left" align="left">
						<input type="button" name="close" onClick="parent.emailwindow.hide();" class="formbutton"
						value="Close" style="width:100px"/>
					</div>
				</div>
			</td>
		</tr>
	</table>
<?
	exit();
}
 
if($action=="generate_report")
{ 
	$process = array( &$_POST );

	// echo "<pre>";
	// print_r($process);
	// echo "</pre>";
	// die;
	
	extract(check_magic_quote_gpc( $process ));

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$color_arr = return_library_array("select id, color_name from lib_color where status_active =1 and is_deleted=0","id","color_name");

	$brand_arr = return_library_array("select id, brand_name from lib_brand", 'id', 'brand_name');
	$machine_arrs = return_library_array("select id, machine_no from lib_machine_name", "id", "machine_no");
	$fabric_desc_arr = return_library_array("select id, item_description from product_details_master where item_category_id=13", "id", "item_description");
	$composition_arr = array();
	$sql_deter = "select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$data_array = sql_select($sql_deter);
	if (count($data_array) > 0) {
		foreach ($data_array as $row) {
			if (array_key_exists($row[csf('id')], $composition_arr)) {
				$composition_arr[$row[csf('id')]] = $composition_arr[$row[csf('id')]] . " " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
			} else {
				$composition_arr[$row[csf('id')]] = $row[csf('construction')] . ", " . $composition[$row[csf('copmposition_id')]] . " " . $row[csf('percent')] . "%";
			}
		}
	}
	$hide_job_id= str_replace("'","",$hide_job_id);
	$sales_con='';
	if(!empty($hide_job_id) && !empty($cbo_fso_no_txt))
	{
		$sales_con=" and a.id in($hide_job_id) ";
	}
	$result_sales=sql_select("select a.sales_booking_no,d.dtls_id,a.buyer_id,a.within_group,a.booking_id,a.job_no from fabric_sales_order_mst a,ppl_planning_entry_plan_dtls d where a.id=d.po_id $sales_con");
	//echo "select a.sales_booking_no,d.dtls_id,a.buyer_id,a.within_group,a.booking_id from fabric_sales_order_mst a,ppl_planning_entry_plan_dtls d where a.id=d.po_id and a.id in($hide_job_id)";
	$result_booking=sql_select("select booking_no,id from wo_booking_mst union all select booking_no ,id from wo_non_ord_samp_booking_mst");
	$booking_data=array();
	foreach ($result_booking as $row) {
		$booking_data[$row[csf('id')]]=$row[csf('booking_no')];
	}
	$sales_data=array();
	foreach ($result_sales as $row) {
		$sales_data[$row[csf('dtls_id')]]['sales_booking_no']=$row[csf('job_no')];
		$sales_data[$row[csf('dtls_id')]]['buyer_id']=$row[csf('buyer_id')];
		$sales_data[$row[csf('dtls_id')]]['within_group']=$row[csf('within_group')];
		$sales_data[$row[csf('dtls_id')]]['booking_no']=$booking_data[$row[csf('booking_id')]];
		if($row[csf('within_group')]==1)
		{
			$sales_data[$row[csf('dtls_id')]]['buyer']=$company_arr[$row[csf('buyer_id')]];
		}
		else
		{
			$sales_data[$row[csf('dtls_id')]]['buyer']=$buyer_arr[$row[csf('buyer_id')]];
		}
	}
	$yarn_count_details=return_library_array( "select id,yarn_count from lib_yarn_count", "id", "yarn_count"  );
	$imge_arr=return_library_array( "select master_tble_id, image_location from  common_photo_library where file_type=1",'master_tble_id','image_location');
	$supllier_arr = return_library_array("select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b where a.id=b.supplier_id and a.status_active=1 group by a.id,a.supplier_name order by a.supplier_name", 'id', 'supplier_name');
	//$machine_arr = return_library_array("select id,machine_no from lib_machine_name  where astatus_active=1 group by id,machine_no order by machine_no", 'id', 'machine_no');
	//print_r($machine_arr);die;
	$machine_arr=array();
	$cbo_company_id= str_replace("'","",$cbo_company_id);
	$result_machine=sql_select("select id,machine_no,brand from lib_machine_name where status_active=1 and category_id=1 and company_id=$cbo_company_id order by machine_no");
	$machine_no_ids=array();
	foreach ($result_machine as $row) {
		$machine_arr[$row[csf('id')]]['machine_no']=$row[csf('machine_no')];
		$machine_arr[$row[csf('id')]]['brand']=$row[csf('brand')];
		$machine_no_ids[]=$row[csf('id')];
	}

	
	$cbo_knitting_source= str_replace("'","",$cbo_knitting_source);
	$start_date=str_replace("'","",trim($txt_date_from));
	$end_date=str_replace("'","",trim($txt_date_to));
	$source_con='';
	$company_con='';
	//echo $cbo_search_by.'--'.$txt_search_common;die;
	if(!empty($cbo_knitting_source) && $cbo_knitting_source!=0)
	{
		$source_con=" and a.knitting_source=$cbo_knitting_source";
	}
	if(!empty($cbo_company_id) && $cbo_company_id!=0)
	{
		$company_con=" and  a.company_id=$cbo_company_id";
	}

	$wo_no_con='';
	$search_field_cond='';
	$receive_date='';
	$date_search_cond ='';

	if($db_type==0)
	{
		$date=change_date_format($start_date, "yyyy-mm-dd", "-");
		if ($start_date!="" &&  $end_date!="") $receive_date  = "and a.receive_date  between '".change_date_format($start_date, "yyyy-mm-dd", "-")."' and '".change_date_format($end_date, "yyyy-mm-dd", "-")."'"; else $wo_date_con ="";
	}

	if($db_type==2)
	{
		$date=change_date_format($start_date, "yyyy-mm-dd", "-",1);
		if ($start_date!="" &&  $end_date!="") $receive_date  = "and a.receive_date  between '".change_date_format($start_date, "yyyy-mm-dd", "-",1)."' and '".change_date_format($end_date, "yyyy-mm-dd", "-",1)."'"; else $wo_date_con ="";
	}

	
	

	$sql="SELECT a.within_group,d.machine_no_id,d.machine_dia,d.machine_gg,a.id as mst_id , a.recv_number, a.item_category,a.company_id,a.receive_basis,a.receive_purpose,a.receive_date,a.booking_id ,a.booking_no,a.currency_id,d.grey_receive_qnty,d.grey_receive_qnty_pcs,d.color_id,d.yarn_count,d.yarn_lot,d.gsm,d.prod_id,d.width,d.febric_description_id,d.body_part_id,d.brand_id,d.color_range_id, d.stitch_length,d.reject_fabric_receive ,d.reject_fabric_receive ,a.remarks from inv_receive_master a , pro_grey_prod_entry_dtls d where a.id=d.mst_id  and a.receive_basis=2 and  a.status_active=1    and a.entry_form=2  $receive_date $company_con $source_con order by d.machine_no_id asc";

	//echo "<pre>".$sql."</pre>";die;



	//$sql_machine="SELECT distinct d.machine_no_id  from inv_receive_master a , pro_grey_prod_entry_dtls d,pro_roll_details e where a.id=d.mst_id and a.id=e.mst_id and d.id=e.dtls_id and   a.status_active=1  and a.receive_basis=2   and a.entry_form=2  $receive_date $company_con $source_con order by d.machine_no_id ";



	//echo $sql ;
	
	$machine_wise_data=array();

	$result=sql_select($sql);
	$program_no_arr=array();
	$program_no_in_machine=array();
	foreach ($result as $row)
	{
		$machine_wise_data[$row[csf('machine_no_id')]][$row[csf('booking_no')]]['within_group']=$row[csf('within_group')];
		$machine_wise_data[$row[csf('machine_no_id')]][$row[csf('booking_no')]]['machine_no_id']=$row[csf('machine_no_id')];
		$machine_wise_data[$row[csf('machine_no_id')]][$row[csf('booking_no')]]['machine_dia']=$row[csf('machine_dia')];
		$machine_wise_data[$row[csf('machine_no_id')]][$row[csf('booking_no')]]['machine_gg']=$row[csf('machine_gg')];
		$machine_wise_data[$row[csf('machine_no_id')]][$row[csf('booking_no')]]['mst_id']=$row[csf('mst_id')];
		$machine_wise_data[$row[csf('machine_no_id')]][$row[csf('booking_no')]]['recv_number']=$row[csf('recv_number')];
		$machine_wise_data[$row[csf('machine_no_id')]][$row[csf('booking_no')]]['item_category']=$row[csf('item_category')];
		$machine_wise_data[$row[csf('machine_no_id')]][$row[csf('booking_no')]]['company_id']=$row[csf('company_id')];
		$machine_wise_data[$row[csf('machine_no_id')]][$row[csf('booking_no')]]['receive_basis']=$row[csf('receive_basis')];
		$machine_wise_data[$row[csf('machine_no_id')]][$row[csf('booking_no')]]['receive_purpose']=$row[csf('receive_purpose')];
		$machine_wise_data[$row[csf('machine_no_id')]][$row[csf('booking_no')]]['receive_date']=$row[csf('receive_date')];
		$machine_wise_data[$row[csf('machine_no_id')]][$row[csf('booking_no')]]['booking_id']=$row[csf('booking_id')];
		$machine_wise_data[$row[csf('machine_no_id')]][$row[csf('booking_no')]]['program_no']=$row[csf('booking_no')];
		$machine_wise_data[$row[csf('machine_no_id')]][$row[csf('booking_no')]]['qnty']+=$row[csf('grey_receive_qnty')];
		$machine_wise_data[$row[csf('machine_no_id')]][$row[csf('booking_no')]]['grey_receive_qnty_pcs']=$row[csf('grey_receive_qnty_pcs')];
		$machine_wise_data[$row[csf('machine_no_id')]][$row[csf('booking_no')]]['color_id']=$row[csf('color_id')];
		$machine_wise_data[$row[csf('machine_no_id')]][$row[csf('booking_no')]]['yarn_count']=$row[csf('yarn_count')];
		$machine_wise_data[$row[csf('machine_no_id')]][$row[csf('booking_no')]]['yarn_lot']=$row[csf('yarn_lot')];
		$machine_wise_data[$row[csf('machine_no_id')]][$row[csf('booking_no')]]['gsm']=$row[csf('gsm')];
		$machine_wise_data[$row[csf('machine_no_id')]][$row[csf('booking_no')]]['prod_id']=$row[csf('prod_id')];
		$machine_wise_data[$row[csf('machine_no_id')]][$row[csf('booking_no')]]['width']=$row[csf('width')];
		$machine_wise_data[$row[csf('machine_no_id')]][$row[csf('booking_no')]]['febric_description_id']=$row[csf('febric_description_id')];
		$machine_wise_data[$row[csf('machine_no_id')]][$row[csf('booking_no')]]['body_part_id']=$row[csf('body_part_id')];
		$machine_wise_data[$row[csf('machine_no_id')]][$row[csf('booking_no')]]['brand_id']=$row[csf('brand_id')];
		$machine_wise_data[$row[csf('machine_no_id')]][$row[csf('booking_no')]]['color_range_id']=$row[csf('color_range_id')];
		$machine_wise_data[$row[csf('machine_no_id')]][$row[csf('booking_no')]]['stitch_length']=$row[csf('stitch_length')];
		$machine_wise_data[$row[csf('machine_no_id')]][$row[csf('booking_no')]]['remarks']=$row[csf('remarks')];
		$machine_wise_data[$row[csf('machine_no_id')]][$row[csf('booking_no')]]['reject_fabric_receive']+=$row[csf('reject_fabric_receive')];
		

	}

	$priority_sql=sql_select("select priority,program_id,machine_id from program_wise_priority where company_id=$cbo_company_id and  machine_id in(".implode(',',$machine_no_ids).") ");
	//echo "select priority,program_id,machine_id from program_wise_priority where company_id=$cbo_company_id and  machine_id in(".implode(',',$machine_no_ids).") ";
	$priority_data=array();
	foreach ($priority_sql as $row) {
		$priority_data[$row[csf('program_id')]]['priority']=$row[csf('priority')];
		$program_no_arr[]=$row[csf('program_id')];
		$program_no_in_machine[$row[csf('machine_id')]][]= $row[csf('program_id')];
	}



	$program_no_arr=array_unique($program_no_arr);

	$sql_program=sql_select("select b.program_qnty,b.id from  ppl_planning_info_entry_dtls b where b.status_active=1 and b.id in(".implode(',',$program_no_arr).")");
	//echo "select b.program_qnty,b.id from  ppl_planning_info_entry_dtls b where b.status_active=1 and b.id in(".implode(',',$program_no_arr).")";

	$program_data=array();
	foreach ($sql_program as $row) {
		$program_data[$row[csf('id')]]['program_qnty']=$row[csf('program_qnty')];
	}

	$sql_prog=sql_select("SELECT a.booking_no,d.machine_no_id,sum(d.grey_receive_qnty) as previus_qnty,sum(d.reject_fabric_receive) as reject_fabric_receive from inv_receive_master a , pro_grey_prod_entry_dtls d where a.id=d.mst_id   and  a.status_active=1 and a.receive_basis=2 and  a.booking_no in(".implode(',',$program_no_arr).") and a.receive_date < '$date'  and a.entry_form=2 group by a.booking_no,d.machine_no_id  ");
	//echo "SELECT a.booking_no,sum(d.grey_receive_qnty) as previus_qnty from inv_receive_master a , pro_grey_prod_entry_dtls d where a.id=d.mst_id   and  a.status_active=1 and a.receive_basis=2 and a.booking_no in(".implode(',',$program_no_arr).") and a.receive_date < '$date'  and a.entry_form=2 group by a.booking_no  ";

	//echo $sql_prog;
	foreach ($sql_prog as $row) {
		$program_data_machine[$row[csf('machine_no_id')]][$row[csf('booking_no')]]['previus_qnty']+=$row[csf('previus_qnty')];
		$program_data_machine[$row[csf('machine_no_id')]][$row[csf('booking_no')]]['reject_fabric_receive']+=$row[csf('reject_fabric_receive')];
	}


	$program_start_dates=return_library_array( "select a.id ,b.start_date from ppl_planning_info_entry_dtls a,ppl_planning_info_machine_dtls b where a.id=b.dtls_id and a.status_active=1 and a.is_deleted=0 and a.id  in(".implode(',',$program_no_arr).") ", "id", "start_date");

	//echo $priority_sql;

	
	$table_width="1950"; $colspan="21";
	ob_start();
	?>
    <fieldset style="width:100%">	
        <table cellpadding="0" cellspacing="0" width="<?=$table_width; ?>" border="0" rules="all">
            <tr class="form_caption">
               <td align="center" width="100%" colspan="<?=$colspan; ?>" style="font-size:16px"><strong><?=$company_arr[$cbo_company_id]; ?></strong></td>
            </tr>
            <tr class="form_caption">
               <td align="center" width="100%" colspan="<?=$colspan; ?>" style="font-size:16px"><strong><?=$report_title; ?></strong></td>
            </tr>
        </table>
        <table class="rpt_table" border="1" rules="all" width="<?=$table_width; ?>" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                	<th width="40">SL</th>
                    <th width="40">Within Group</th>
                    <th width="60">M/C No.</th>
                    <th width="70">M/C Brand</th>
                    <th width="60">M/C Dia & Gauge</th>
                    <th width="100">Buyer</th>
                    <th width="130">Sales Order No</th>
                    <th width="120">Booking No</th>
                    <th width="80" >Body Part</th>
                    <th width="40">Yarn Count</th>
                    <th width="60">Brand</th>
                    <th width="60">Yarn Type</th>
                    <th width="50">Lot</th>
                    <th width="80">Construction</th>
                    <th width="100">Composition</th>
                    <th width="70">Color</th>
                    <th width="70">Color Range</th>
                    <th width="40">Stich</th>
                    <th width="40">Dia</th>
                    <th width="40">GSM</th>
                    <th width="60">Program No</th>
                    <th width="60">Program Qty.</th>
                    <th width="60">Previous<br>Production</th>
                    <th width="60">Today<br>Production</th>
                    <th width="60">TTL<br>Production</th>
                    <th width="60">Balance Qty.</th>
                    <th width="60">Next Program 1 </th>
                    <th width="60">Start Date</th>
                    <th width="60">Next Program 2</th>
                    <th width="60">Start Date</th>
                    <th width="60">Reject Qty</th>
                    <th >Remarks</th>
                    
                </tr>

            </thead>
       
        
           <tbody>
            
			<?php 
				$i=1;
				$j=1;
				$previous_bill_no='';
				$wo_qty=0;
				$bill_qty=0;
				$amount=0;
				$upcharge=0;
				$discount=0;
				$tot_bill_amt=0;
				function sortByPriority($a, $b)
				{
					if($a['priority']==$b['priority'])
						return $b['total']-$a['total'];
					else return $a['priority'] - $b['priority'];
					    
				}
				foreach ($result_machine as $row ) 
				{
					
					
					
					$progm_nos=array_unique($program_no_in_machine[$row[csf('id')]]);

					$list_data=[];
					
					for ($j=0;$j<count($progm_nos);$j++) {
						$program_no=$progm_nos[$j];
						//echo $priority_data[$program_no]['priority'];die;
						$program_qnty=$program_data[$program_no]['program_qnty'];
						$previus_qnty=$program_data_machine[$row[csf('id')]][$program_no]['previus_qnty'];
						$today_qnty=$machine_wise_data[$row[csf('id')]][$program_no]['qnty'];
						if($program_qnty>$previus_qnty+$today_qnty )
						{
							
							$list_data[$progm_nos[$j]]['priority']=$priority_data[$program_no]['priority'];
							$list_data[$progm_nos[$j]]['total']=$previus_qnty+$today_qnty;
							$list_data[$progm_nos[$j]]['program_no']=$progm_nos[$j];
						}
					}
					

					if(count($list_data))
					{
						// echo "<pre>";
						// print_r($list_data);
						// echo "</pre>";
						usort($list_data, 'sortByPriority');
						// echo "<pre>";
						// print_r($list_data);
						// echo "</pre>";
						// die;
					}
					//echo count($list_data);die;
					if(count($list_data)>0)
					{

						$first_program=$list_data[0]['program_no'];
					}
					else{
						$first_program=0;
					}
					if(count($list_data)>1){
						$next_program=$list_data[1]['program_no'];

					}
					else{
						$next_program=0;
					}
					if(count($list_data)>2){
						$next_next_program=$list_data[2]['program_no'];

					}
					else{
						$next_next_program=0;
					}



					//sort($list_data);


					
					$febric_description_id=$machine_wise_data[$row[csf('id')]][$first_program]['febric_description_id'];
					$prod_id=$machine_wise_data[$row[csf('id')]][$first_program]['prod_id'];

					$compos='';
					$constct='';
					if ($febric_description_id == 0 || $febric_description_id == "")
						$fabric_desc = $fabric_desc_arr[$prod_id];
					else
					{

						$fabric_desc = $composition_arr[$febric_description_id];
						$comsdata=explode(", ", $fabric_desc);
						$compos=$comsdata[1];
						$constct=$comsdata[0];
					}
					$color = '';
					$color_id = explode(",", $machine_wise_data[$row[csf('id')]][$first_program]['color_id']);
					foreach ($color_id as $val) {
						if ($val > 0) $color .= $color_arr[$val] . ",";
					}
					$color = chop($color, ',');

					if(!empty($sales_data[$first_program]['sales_booking_no']))
					{

						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						?>
						<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>">
							<td  ><?=$i; ?></td>
							<td ><p><?php echo $machine_wise_data[$row[csf('id')]][$first_program]['within_group'] == 1 ? 'Yes' : 'No';?></p></td>
							<td ><p><?=$row[csf('machine_no')]; ?></p></td>
							<td ><p><?=$row[csf('brand')]; ?></p></td>
							<td ><p><?=$machine_wise_data[$row[csf('id')]][$first_program]['machine_dia']." x ".$machine_wise_data[$row[csf('id')]][$first_program]['machine_gg']; ?></p></td>
							
							
							<td><p><?=$sales_data[$first_program]['buyer']; ?></p></td>
							<td><p><?=$sales_data[$first_program]['sales_booking_no']; ?></p></td>
							<td><p><?=$sales_data[$first_program]['booking_no']; ?></p></td>
							<td><p><? echo $body_part[$machine_wise_data[$row[csf('id')]][$first_program]['body_part_id']]; ?></p></td>
							<td><p>
								<? echo $yarn_count_details[$machine_wise_data[$row[csf('id')]][$first_program]['yarn_count']]; ?></p>
							</td>
							<td><p><? echo $brand_arr[$machine_wise_data[$row[csf('id')]][$first_program]['brand_id']]; ?></p></td>
							<td align="center">
								<p>
									<?
										$febric_description_id=array_unique(explode(",",$febric_description_id));
										$yarn_type_name='';
										foreach($febric_description_id as $y_id)
										{
											if($yarn_type_name=='') $yarn_type_name=$yarn_type[$y_id]; else $yarn_type_name.=",".$yarn_type[$y_id];
										}
										echo $yarn_type_name;

									?>
									
								</p>
							</td>
							<td align="center"><p><?=$machine_wise_data[$row[csf('id')]][$first_program]['yarn_lot']; ?></p></td>
							<td ><p><?=$constct; ?></p></td>
							<td ><p><?=$compos; ?></p></td>
							<td align="right"><p><?=$color; ?></p></td>
							<td align="right"><p><?=$color_range[$machine_wise_data[$row[csf('id')]][$first_program]['color_range_id']]; ?></p></td>
							<td align="center"><?=$machine_wise_data[$row[csf('id')]][$first_program]['stitch_length']; ?></td>
							<td align="center"> <?php echo $machine_wise_data[$row[csf('id')]][$first_program]['width']; ?></td>
							<td align="center"> <?php echo $machine_wise_data[$row[csf('id')]][$first_program]['gsm']; ?></td>
							<td align="center">
								<?php $within_group=$machine_wise_data[$row[csf('id')]][$first_program]['within_group'];if(empty($within_group)){ $within_group=0;}else{$within_group=1;} ?>
							 	<a href="#" onClick="generate_report2(<?php echo $cbo_company_id; ?>, <?php echo $first_program; ?>, <?php echo $within_group; ?>)"><?php echo $first_program; ?></a>
								
							</td>
							<td align="center"> <?php echo number_format($program_data[$first_program]['program_qnty'],2); ?></td>
							<td align="center"> <?php echo number_format($program_data_machine[$row[csf('id')]][$first_program]['previus_qnty'],2); ?></td>
							<td align="center"> <?php echo  number_format($machine_wise_data[$row[csf('id')]][$first_program]['qnty'],2); ?></td>
							<td align="center">
							 	<?php echo  number_format($program_data_machine[$row[csf('id')]][$first_program]['previus_qnty']+$machine_wise_data[$row[csf('id')]][$first_program]['qnty'],2); ?>
							 	
							</td>
							<td align="center"> 
								<?php echo  number_format($program_data[$first_program]['program_qnty']-($program_data_machine[$row[csf('id')]][$first_program]['previus_qnty']+$machine_wise_data[$row[csf('id')]][$first_program]['qnty']),2); ?>
									
							</td>
							<td>
								<?php $within_group=$machine_wise_data[$row[csf('id')]][$next_program]['within_group']; if(empty($within_group)){ $within_group=0;}else{$within_group=1;} ?>
								<a href="#" onClick="generate_report2(<?php echo $cbo_company_id; ?>, <?php echo $next_program; ?>, <?php echo  $within_group; ?>)"><?php echo $next_program; ?></a>
								
									
							</td>
							<td><?php echo change_date_format($program_start_dates[$next_program]); ?></td>
							<td>
								<?php $within_group=$machine_wise_data[$row[csf('id')]][$next_program]['next_next_program']; if(empty($within_group)){ $within_group=0;}else{$within_group=1;} ?>
								<a href="#" onClick="generate_report2(<?php echo $cbo_company_id; ?>, <?php echo $next_next_program; ?>, <?php echo $within_group; ?>)"><?php echo $next_next_program; ?></a>
							</td>
							<td><?php echo change_date_format($program_start_dates[$next_next_program]); ?></td>
							<td><?php echo number_format($machine_wise_data[$row[csf('id')]][$first_program]['reject_fabric_receive']+$program_data_machine[$row[csf('id')]][$first_program]['reject_fabric_receive'],2);  ?></td>
							<td> <?php echo $machine_wise_data[$row[csf('id')]][$first_program]['remarks']; ?></td>
						</tr>
						<?	
						$i++;
					}
				}
			 ?>
			
			</tbody>
			 <tfoot >
			 <tr>
			 	
			 </tr>
			 	
			 	
			 </tfoot>
           </table>
        
        
    </fieldset>
	
	<?
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}




?>