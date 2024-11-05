<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.trims.php');
require_once('../../../../includes/class4/class.fabrics.php');
require_once('../../../../includes/class4/class.yarns.php');
$user_id=$_SESSION['logic_erp']['user_id'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$colorname_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name"  );
$country_arr=return_library_array( "select id, country_name from lib_country", "id", "country_name");
$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
$buyer_short_library=return_library_array( "select id,buyer_name from lib_buyer", "id", "buyer_name"  );

if ($action=="load_drop_down_buyer")
{

	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
}


if ($action=="print_report_button_setting")
{

	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=7 and report_id=59 and is_deleted=0 and status_active=1");
	echo $print_report_format;
}




if($db_type==0) $insert_year="SUBSTRING_INDEX(a.insert_date, '-', 1)";
if($db_type==2) $insert_year="extract( year from b.insert_date)";


if ($action == "job_no_search_popup")
{
	echo load_html_head_contents("Order No Info", "../../../../", 1, 1, '', '', '');

	extract($_REQUEST);
	?>

	<script>

		var selected_id = new Array;
		var selected_name = new Array;
		var selected_year = new Array;

		function check_all_data()
		{
			var tbl_row_count = document.getElementById('tbl_list_search').rows.length;
			tbl_row_count = tbl_row_count - 1;

			for (var i = 1; i <= tbl_row_count; i++)
			{
				$('#tr_' + i).trigger('click');
			}
		}

		function toggle(x, origColor) {
			var newColor = 'yellow';
			if (x.style) {
				x.style.backgroundColor = (newColor == x.style.backgroundColor) ? origColor : newColor;
			}
		}

		function js_set_value_job(str) {


			if (str != "")
				str = str.split("_");

			toggle(document.getElementById('tr_' + str[0]), '#FFFFCC');

			if (jQuery.inArray(str[1], selected_id) == -1) {
				selected_id.push(str[1]);
				selected_name.push(str[2]);
				selected_year.push(str[3]);

			} else {
				for (var i = 0; i < selected_id.length; i++) {
					if (selected_id[i] == str[1])
						break;
				}
				selected_id.splice(i, 1);
				selected_name.splice(i, 1);
				selected_year.splice(i, 1);
			}
			var id = '';
			var name = '';
			let year = '';
			for (var i = 0; i < selected_id.length; i++) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
				year += selected_year[i] + '*';
			}

			id = id.substr(0, id.length - 1);
			name = name.substr(0, name.length - 1);
			year = year.substr(0, year.length - 1);

			$('#hide_job_id').val(id);
			$('#hide_job_no').val(name);
			$('#hide_job_year').val(year);
		}

	</script>

	</head>

	<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:780px;">
				<table width="770" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
					<thead>
						<th>Company</th>
						<th>Search By</th>
						<th id="search_by_td_up" width="170">Please Enter Job No</th>
						<th> Date</th>
						<th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th>
						<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
						<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
						<input type="hidden" name="hide_job_year" id="hide_job_year" value="" />
					</thead>
					<tbody>
						<tr>
							<td align="center">
								<?
								echo create_drop_down("company_id", 140, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0  order by company_name", "id,company_name", 1, "--Select--", $company, "", 0);
								?>
							</td>
							<td align="center">
								<?
								$search_by_arr = array(1 => "Job No", 2 => "Style Ref",3=> "Lot Ratio No");
								$dd = "change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";
								echo create_drop_down("cbo_search_by", 110, $search_by_arr, "", 0, "--Select--", "", $dd, 0);
								?>
							</td>
							<td align="center" id="search_by_td">
								<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
							</td>
							<td align="center">
								<input type="text" name="txt_date_from" id="txt_date_from" class="datepicker" style="width:70px" readonly>To
								<input type="text" name="txt_date_to" id="txt_date_to" class="datepicker" style="width:70px" readonly>
							</td>
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view( document.getElementById('company_id').value + '**' + document.getElementById('cbo_search_by').value + '**' + document.getElementById('txt_search_common').value + '**' + document.getElementById('txt_date_from').value + '**' + document.getElementById('txt_date_to').value+'**'+<?echo $style; ?>, 'create_job_no_search_list_view', 'search_div', 'production_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
							</td>
						</tr>
						<tr>
							<td colspan="5" height="20" valign="middle"><? echo load_month_buttons(1); ?></td>
						</tr>
					</tbody>
				</table>
				<div style="margin-top:15px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
	</body>
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if ($action == "create_job_no_search_list_view")
{
	$data = explode('**', $data);
	$company_id = $data[0];
	$cbo_year = "";

	$company_con='';
	if(empty($company_id))
	{
		echo "Select Company First";die;
	}else{
		$company_con=" and b.company_name=$company_id";
	}

	$search_by = $data[1];
	$search_string = "'%" . trim($data[2]) . "%'";
	$search_field='';
	if(!empty($data[2]))
	{
		if ($search_by == 1)
			$search_field = " and b.job_no_prefix_num =$data[2]";
		else if ($search_by == 2)
			$search_field = " and b.style_ref_no like ".$search_string;
		else if($search_by == 3)
			$search_field = " and c.cut_num_prefix_no like ".$search_string;
	}


	$start_date = $data[3];
	$end_date = $data[4];

	if ($start_date != "" && $end_date != "") {
		if ($db_type == 0) {
			$date_cond = " and a.insert_date between '" . change_date_format(trim($start_date), "yyyy-mm-dd") . "' and '" . change_date_format(trim($end_date), "yyyy-mm-dd") . "'";
		} else {
			$date_cond = " and a.insert_date between '" . change_date_format(trim($start_date), '', '', 1) . "' and '" . change_date_format(trim($end_date), '', '', 1) . "'";
		}
	} else {
		$date_cond = "";
	}

	$arr = array(0 => $company_arr, 1 => $buyer_short_library);
	if ($db_type == 0)
	{
		$year_field = "YEAR(a.insert_date) as year";
    //$year_cond = " and YEAR(a.insert_date) = $cbo_year ";
	}
	else if ($db_type == 2)
	{
		$year_field = "to_char(a.insert_date,'YYYY') as year";
    //$year_cond = " and to_char(a.insert_date,'YYYY') = $cbo_year ";
	}
	else
	{$year_field = "";
   // $year_cond = "";
    } //defined Later


  	if($search_by == 3)
  	{
  		$sql = "SELECT  b.id ,b.job_no,b.style_ref_no,b.job_no_prefix_num, b.company_name, b.buyer_name,to_char(b.insert_date,'YYYY') as year
		    from wo_po_break_down a, wo_po_details_master b,ppl_cut_lay_mst c where a.job_no_mst=b.job_no and  a.is_deleted=0 and a.status_active=1 and
	        b.status_active=1 and b.is_deleted=0 $company_con $date_cond   $search_field group by  b.id ,b.job_no,b.style_ref_no,b.job_no_prefix_num, b.company_name, b.buyer_name,b.insert_date order by job_no";
  	}
  	else
  	{
  		$sql = "SELECT  b.id ,b.job_no,b.style_ref_no,b.job_no_prefix_num, b.company_name, b.buyer_name,to_char(b.insert_date,'YYYY') as year
		    from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and  a.is_deleted=0 and a.status_active=1 and
	        b.status_active=1 and b.is_deleted=0 $company_con $date_cond   $search_field group by  b.id ,b.job_no,b.style_ref_no,b.job_no_prefix_num, b.company_name, b.buyer_name,b.insert_date order by job_no";

  	}



	// echo $sql;

	$conclick="id,job_no_prefix_num,year";
	 $style=$data[5];
	if($style==1)
	{
		$conclick="id,style_ref_no,year";
	}

    echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "150,130,140,100", "760", "320", 0, $sql, "js_set_value_job", $conclick, "", 1, "company_name,buyer_name,0,0,0", $arr, "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "", '', '0,0,0,0,0,0,3', '',1);
    exit();
}


if($action=="generate_report")
{
    $process = array( &$_POST );

   // print_r($process);die;
    extract(check_magic_quote_gpc( $process ));
	$lineArr = return_library_array("select id,line_name from lib_sewing_line","id","line_name");
	$prod_reso_arr=return_library_array( "select id, line_number from prod_resource_mst",'id','line_number');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0",'id','buyer_name');
	$supplier_arr=return_library_array( "select id,supplier_name from lib_supplier ",'id','supplier_name');
	$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	$size_library=return_library_array( "select id,size_name from lib_size", "id", "size_name"  );
	$lib_trims_group_name = return_library_array("select id,item_name from lib_item_group", "id", "item_name");


	$type=str_replace("'","",$type);
	$company_id = str_replace("'","",$cbo_company_name);
	$buyer_id = str_replace("'","",$cbo_buyer_name);
	$hidden_job_id = str_replace("'","",$hidden_job_id);
	$txt_job_no = str_replace("'","",$txt_job_no);
	$hidden_style_id = str_replace("'","",$hidden_style_id);
	$txt_style_no = str_replace("'","",$txt_style_no);
	$cbo_status = str_replace("'","",$cbo_status);
	$cbo_date_type = str_replace("'","",$cbo_date_type);
	$date_from = str_replace("'","",$txt_date_from);
	$date_to = str_replace("'","",$txt_date_to);
	$cbo_gg = str_replace("'","",$cbo_gg);
	$hidden_year = str_replace("'","",$hidden_year);

	$sql_cond = "";
	$sql_cond .= ($company_id) ? " and a.company_name=$company_id" : "";
	$sql_cond .= ($buyer_id) ? " and a.buyer_name=$buyer_id" : "";
	$sql_cond .= ($hidden_job_id !="") ? " and a.id in($hidden_job_id)" : "";
	$sql_cond .= ($hidden_style_id !="") ? " and a.id in($hidden_style_id)" : "";
	if($hidden_job_id=="")
	{
		$job_num = implode(",", explode("*",$txt_job_no));
		$sql_cond .= ($txt_job_no !="") ? " and a.job_no_prefix_num in($job_num)" : "";
	}
	if($hidden_style_id=="")
	{
		$style_no = "'".implode("','", explode("*",$txt_style_no))."'";
		$sql_cond .= ($txt_style_no !="") ? " and a.style_ref_no in($style_no)" : "";
	}
	if($hidden_year!="")
	{
		$hidden_year = "'".implode("','", explode("*",$hidden_year))."'";
		$sql_cond .= " and to_char(a.insert_date,'YYYY') in($hidden_year)";
	}
	$sql_cond .= ($cbo_gg) ? " and a.gauge=$cbo_gg" : "";

	if($cbo_status)
	{
		if($cbo_status==3)
		{
			$sql_cond .= " and b.shiping_status=3";
		}
		else
		{
			$sql_cond .= " and b.shiping_status !=3";
		}
	}

	if($date_from!="" && $date_to!="")
	{
		if($cbo_date_type==1)
		{
			$po_id_array=return_library_array( "SELECT id,id from WO_PO_BREAK_DOWN where status_active=1 and pub_shipment_date between $txt_date_from and $txt_date_to ", "id", "id"  );
		}
		elseif($cbo_date_type==2)
		{
			$po_id_array=return_library_array( "SELECT po_break_down_id,po_break_down_id from PRO_EX_FACTORY_MST where status_active=1 and ex_factory_date between $txt_date_from and $txt_date_to ", "po_break_down_id", "po_break_down_id"  );
		}
		$po_id_cond = where_con_using_array($po_id_array,0,"b.id");

	}

	$sql="SELECT  a.id,a.job_no,a.buyer_name,a.style_ref_no as style,a.gauge,b.id as po_id,b.po_number,b.po_quantity,b.plan_cut,b.shiping_status from wo_po_details_master a,wo_po_break_down b	where a.id=b.job_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $sql_cond $po_id_cond";
	// echo $sql;die;

	$po_id_array=array();
	$job_no_array=array();
	$job_id_array=array();
	$po_wise_job_array=array();
	$data_array=array();
	foreach(sql_select($sql) as $v)
	{
		$job_no_array[$v['JOB_NO']] = $v['JOB_NO'];
		$job_id_array[$v['ID']] = $v['ID'];
		$po_id_array[$v['PO_ID']] = $v['PO_ID'];
		$po_wise_job_array[$v['PO_ID']] = $v['JOB_NO'];
		$data_array[$v['JOB_NO']]['style'] = $v['STYLE'];
		$data_array[$v['JOB_NO']]['buyer_name'] = $v['BUYER_NAME'];
		$data_array[$v['JOB_NO']]['gauge'] = $v['GAUGE'];
		$data_array[$v['JOB_NO']]['po_quantity'] += $v['PO_QUANTITY'];
		$data_array[$v['JOB_NO']]['plan_cut'] += $v['PLAN_CUT'];
		$data_array[$v['JOB_NO']]['po_number'] .= $v['PO_NUMBER'].",";
		$data_array[$v['JOB_NO']]['shiping_status'] .= $shipment_status[$v['SHIPING_STATUS']].",";
	}

	// ============================== yarn req ============================
	$job_cond = where_con_using_array($job_no_array,"1","a.job_no");
	$sql="SELECT a.job_no, c.plan_cut_qnty as PLAN_CUT_QNTY, d.costing_per as COSTING_PER,g.measurement as MEASUREMENT
	from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c, wo_pre_cost_mst d, wo_pre_cost_fabric_cost_dtls e,wo_pre_cost_fab_yarn_cost_dtls f,wo_pre_stripe_color g
	where a.id=b.job_id and c.po_break_down_id=b.id and a.id=d.job_id and a.id=e.job_id and e.job_id=b.job_id and e.job_id=c.job_id and f.fabric_cost_dtls_id=e.id and e.id=g.pre_cost_fabric_cost_dtls_id and f.color=g.stripe_color and g.color_number_id=c.color_number_id
	 and a.status_active=1 and b.status_active=1 and c.status_active=1 and e.status_active=1 and f.status_active=1 and g.status_active=1 $job_cond";
	// echo $sql;die;
	$res = sql_select($sql);
	$yarn_data_array = array();
	foreach ($res as $v)
	{
		if($v["COSTING_PER"]==1){$order_price_per_dzn=12;}
		else if($v["COSTING_PER"]==2){$order_price_per_dzn=1;}
		else if($v["COSTING_PER"]==3){$order_price_per_dzn=24;}
		else if($v["COSTING_PER"]==4){$order_price_per_dzn=36;}
		else if($v["COSTING_PER"]==5){$order_price_per_dzn=48;}

		$yarn_data_array[$v['JOB_NO']]["req_qty"] += (($v["MEASUREMENT"]/$order_price_per_dzn)*$v["PLAN_CUT_QNTY"])*2.20462;
	}


	// ============================== yarn rcv data ========================
	$job_cond = where_con_using_array($job_no_array,"1","a.job_no");
	$sql="SELECT a.job_no as JOB_NO,
	sum(case when a.transaction_type=1 then a.cons_quantity else 0 end) as RCV_QNTY,
	sum(case when a.transaction_type=2 then a.cons_quantity else 0 end) as ISS_QNTY,
	sum(case when a.transaction_type=3 then a.cons_quantity else 0 end) as RCV_RTN_QNTY,
	sum(case when a.transaction_type=4 then a.cons_quantity else 0 end) as ISS_RTN_QNTY
	from inv_transaction a, product_details_master b
	where a.prod_id=b.id and a.item_category=1 and a.status_active=1 and b.status_active=1 $job_cond
	group by a.job_no";
	// echo $sql;die;
	$res = sql_select($sql);
	foreach ($res as $v)
	{
		$yarn_data_array[$v['JOB_NO']]['rcv'] += $v['RCV_QNTY'];
		$yarn_data_array[$v['JOB_NO']]['issue'] += $v['ISS_QNTY'];
		$yarn_data_array[$v['JOB_NO']]['rcv_rtn'] += $v['RCV_RTN_QNTY'];
		$yarn_data_array[$v['JOB_NO']]['issue_rtn'] += $v['ISS_RTN_QNTY'];
	}

	// ============================ trims Req =========================================
	$job_ids = implode(",",$job_id_array);
	$condition= new condition();
	$condition->jobid_in($job_ids);
	$condition->init();
	$trim= new trims($condition);
	// echo $trim->getQuery(); die;
	$trim_qty_arr=$trim->getQtyArray_by_jobAndItemid();
	// echo "<pre>";print_r($trim_qty_arr);die;

	$sql = "SELECT a.job_no,b.trim_group,b.CONS_UOM
	from wo_pre_cost_mst a, wo_pre_cost_trim_cost_dtls b, wo_pre_cost_trim_co_cons_dtls c
	where c.job_no=b.job_no and b.id=c.wo_pre_cost_trim_cost_dtls_id and a.job_no=b.job_no and a.status_active=1 and c.status_active=1 and  b.status_active=1 and c.cons>0 $job_cond group by a.job_no,b.trim_group,b.CONS_UOM";
	// echo $sql;
	$res = sql_select($sql);
	$trims_data_array = array();
	foreach ($res as $v)
	{
		$trims_data_array[$v['TRIM_GROUP']]['req_qty'] += $trim_qty_arr[$v['JOB_NO']][$v['TRIM_GROUP']];
		$trims_data_array[$v['TRIM_GROUP']]['uom'] = $v['CONS_UOM'];
	}

	/*======================================================================================/
	/                                    trims booking qty                                  /
	/======================================================================================*/
	$job_cond = where_con_using_array($job_no_array,"1","d.job_no");
	$sql = "SELECT b.trim_group as TRIM_GROUP, sum(case when a.is_short=2 then c.requirment else 0 end) as QTY, sum(case when a.is_short=1 then c.requirment else 0 end) as SHOR_QTY FROM wo_booking_mst a,wo_booking_dtls b,wo_trim_book_con_dtls c,wo_po_details_master d,wo_po_break_down e WHERE a.booking_no=b.booking_no and b.booking_no=c.booking_no and b.id=c.wo_trim_booking_dtls_id and d.id=e.job_id and c.po_break_down_id=e.id $poCon and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form in(252)  and a.item_category=4 and a.booking_type=2 $job_cond group by b.trim_group ";
	// echo $sql;
	$trimsRes = sql_select($sql);
	$tbArray = array();
	$trims_wo_array = array();
	foreach ($trimsRes as  $v)
	{
		$trims_wo_array[$v['TRIM_GROUP']]['book_qty'] += $v['QTY'];
		$trims_wo_array[$v['TRIM_GROUP']]['short_book_qty'] += $v['SHOR_QTY'];
	}
	// echo"<pre>";print_r($trims_wo_array);
	/*======================================================================================/
	/                                    trims receive qty                                  /
	/======================================================================================*/
	$job_cond = where_con_using_array($job_no_array,"1","d.job_no");
	$sqlacc = "SELECT  B.ITEM_GROUP_ID as TRIM_GROUP,c.quantity AS QNTY,c.reject_qty as REJ_QTY FROM inv_receive_master a,inv_trims_entry_dtls b,order_wise_pro_details c,wo_po_details_master d,wo_po_break_down e where a.id=b.mst_id and b.id=c.dtls_id and c.trans_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form=24 AND c.entry_form = 24 AND c.entry_form = 24 and a.item_category=4 and d.id=e.job_id and c.po_breakdown_id=e.id $job_cond";
	// echo $sqlacc;die();
	$accRes = sql_select($sqlacc);
	$trimsDataArray = array();
	foreach ($accRes as $v)
	{
		$trimsDataArray[$v['TRIM_GROUP']]['rcv_qty'] += $v['QNTY'];
		$trimsDataArray[$v['TRIM_GROUP']]['rej_qty'] += $v['REJ_QTY'];
	}

	/*======================================================================================/
	/                               trims issue and rcv return                              /
	/======================================================================================*/
	$sqlacc = "SELECT a.entry_form, B.ITEM_GROUP_ID as TRIM_GROUP,C.QUANTITY AS QNTY FROM inv_issue_master a,inv_trims_issue_dtls b,order_wise_pro_details c,wo_po_details_master d,wo_po_break_down e where a.id=b.mst_id and b.id=c.dtls_id and c.trans_type in(2,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form in(25,49) AND c.entry_form in(25,49) AND c.entry_form in(25,49) and a.item_category=4 and d.id=e.job_id and c.po_breakdown_id=e.id  $job_cond";
	// echo $sqlacc;die();
	$accRes = sql_select($sqlacc);
	foreach ($accRes as $v)
	{
		if($v['ENTRY_FORM']==49)
		{
			$trimsDataArray[$v['TRIM_GROUP']]['rcv_rtn_qty'] += $v['QNTY'];
		}
		if($v['ENTRY_FORM']==25)
		{
			$trimsDataArray[$v['TRIM_GROUP']]['issue_qty'] += $v['QNTY'];
		}
	}


	// =================================== lot ratio data =====================================
	$order_id_cond = where_con_using_array($po_id_array,0,"order_id");
	$sql = "SELECT order_id,size_qty from PPL_CUT_LAY_BUNDLE  where status_active=1 and status_active=1 $order_id_cond";
	// echo $sql;
	$res = sql_select($sql);
	$lotratio_data_array = array();
	foreach ($res as $v)
	{
		$lotratio_data_array[$po_wise_job_array[$v['ORDER_ID']]] += $v['SIZE_QTY'];
	}

	// =================================== rmg data =====================================
	$order_id_cond = where_con_using_array($po_id_array,0,"a.po_break_down_id");
	$sql = "SELECT a.production_type,a.po_break_down_id as po_id,b.production_qnty,b.re_production_qty as re_iron from PRO_GARMENTS_PRODUCTION_MST a, PRO_GARMENTS_PRODUCTION_DTLS b where a.id=b.mst_id and a.production_type in(1,3,4,5,8,63,64,67,111,112) and a.status_active=1 and b.status_active=1 $order_id_cond";
	//echo $sql;
	$res = sql_select($sql);
	$rmg_data_array = array();
	foreach ($res as $v)
	{
		$rmg_data_array[$po_wise_job_array[$v['PO_ID']]][$v['PRODUCTION_TYPE']] += $v['PRODUCTION_QNTY'];
		$reiron_data_array[$po_wise_job_array[$v['PO_ID']]][$v['PRODUCTION_TYPE']] += $v['RE_IRON'];
	}
	//echo "<pre>";print_r($rmg_data_array) ;die;
	// =================================== buyer inspection data =====================================
	$order_id_cond = where_con_using_array($po_id_array,0,"a.po_break_down_id");
	$sql = "SELECT a.job_no,b.ins_qty from PRO_BUYER_INSPECTION a, PRO_BUYER_INSPECTION_BREAKDOWN b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.inspection_level=3 and a.inspection_status=1 $order_id_cond";
	// echo $sql;
	$res = sql_select($sql);
	$insp_data_array = array();
	foreach ($res as $v)
	{
		$insp_data_array[$v['JOB_NO']] += $v['INS_QTY'];
	}
	// =================================== shipment data =====================================
	$order_id_cond = where_con_using_array($po_id_array,0,"a.po_break_down_id");
	$sql = "SELECT a.po_break_down_id as po_id,b.production_qnty from PRO_EX_FACTORY_MST a, PRO_EX_FACTORY_DTLS b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 $order_id_cond";
	// echo $sql;
	$res = sql_select($sql);
	$exf_data_array = array();
	foreach ($res as $v)
	{
		$exf_data_array[$po_wise_job_array[$v['PO_ID']]] += $v['PRODUCTION_QNTY'];
	}
	// =================================== gate pass data =====================================
	$order_id_cond = where_con_using_array($po_id_array,0,"b.BUYER_ORDER_ID");
	$sql = "SELECT b.BUYER_ORDER_ID as po_id,b.QUANTITY from INV_GATE_PASS_MST a, INV_GATE_PASS_DTLS b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 AND (b.SAMPLE_ID <> 0 and b.SAMPLE_ID IS NOT NULL) $order_id_cond";
	//  echo $sql;die;
	$res = sql_select($sql);
	$gatepass_data_array = array();
	foreach ($res as $v)
	{
		$gatepass_data_array[$po_wise_job_array[$v['PO_ID']]] += $v['QUANTITY'];
	}
	//echo "<pre>";print_r($gatepass_data_array);die;
	// =================================== leftover rcv data =====================================
	$order_id_cond = where_con_using_array($po_id_array,0,"b.po_break_down_id");
	$sql = "SELECT b.po_break_down_id as po_id,b.TOTAL_LEFT_OVER_RECEIVE as qty from PRO_LEFTOVER_GMTS_RCV_MST a, PRO_LEFTOVER_GMTS_RCV_DTLS b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.goods_type=1 $order_id_cond";
	 //echo $sql;die;
	$res = sql_select($sql);
	$lftovr_data_array_rcv = array();
	foreach ($res as $v)
	{
		$lftovr_data_array_rcv[$po_wise_job_array[$v['PO_ID']]]['rcv'] += $v['QTY'];
	}
	//  echo "<pre>";print_r($lftovr_data_array);die;
	// =================================== leftover issue data =====================================
	$order_id_cond = where_con_using_array($po_id_array,0,"b.po_break_down_id");
	$sql = "SELECT b.po_break_down_id as po_id,b.TOTAL_ISSUE as qty from PRO_LEFTOVER_GMTS_ISSUE_MST a, PRO_LEFTOVER_GMTS_ISSUE_DTLS b where a.id=b.mst_id and a.status_active=1 and b.status_active=1 and a.goods_type=1 $order_id_cond";
	 //echo $sql;
	$res = sql_select($sql);
	$lftovr_data_array_issue = array();
	foreach ($res as $r)
	{
		$lftovr_data_array_issue[$po_wise_job_array[$r['PO_ID']]]['issue'] += $r['QTY'];
	}
	//   echo "<pre>";print_r($lftovr_data_array_issue);die;
	$tbl_width = 4040;
	ob_start();


	?>
	<fieldset style="width:<?=$tbl_width;?>px;">
			<table  cellspacing="0" style="justify-content: center;text-align: center;width: 1720px;" >
				<tr class="form_caption" style="border:none;justify-content: center;text-align: center;">
						<td colspan="50" align="center" style="border:none;font-size:14px; font-weight:bold" > Production Status Report </td>
				</tr>
				<tr style="border:none;justify-content: center;text-align: center;">
						<td colspan="50" align="center" style="border:none; font-size:16px; font-weight:bold">
						Company Name:<? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?>
						</td>
					</tr>
					<tr style="border:none;justify-content: center;text-align: center;">
						<td colspan="50" align="center" style="border:none;font-size:12px; font-weight:bold">
						<? echo "Production date ".change_date_format(str_replace("'","",$txt_date_from))." to ". change_date_format(str_replace("'","",$txt_date_to)) ;?>
						</td>
					</tr>
			</table>

			<div style="width:<?=$tbl_width+20;?>pxpx; margin-left:18px" >
				<table cellspacing="0" border="1" class="rpt_table" width="<?=$tbl_width;?>" rules="all">
					<thead>
						<tr>
							<th colspan="10">Order Details</th>
							<th colspan="6">Yarn Information</th>
							<th colspan="4">Knitting</th>
							<th colspan="2">Linking</th>
							<th colspan="2">Trimming</th>
							<th colspan="2">Mending</th>
							<th colspan="2">Wash</th>
							<th colspan="2">Sewing</th>
							<th colspan="3">Embellishment</th>
							<th colspan="3">Iron</th>
							<th colspan="3">Packing & Finishing</th>
							<th colspan="2">Inspection</th>
							<th colspan="3">Shipment Status</th>
							<th colspan="5">Left over Status</th>
							<th width="120" rowspan="2">Status</th>
						</tr>
						<tr >
							<th width="40"><p>SL</p></th>
							<th width="80"><p>Buyer</p></th>
							<th width="130"><p>Style</p></th>
							<th width="80"><p>Job No</p></th>
							<th width="150"><p>PO Number</p></th>
							<th width="80"><p>GG</p></th>
							<th width="80"><p> Job Qty</p></th>
							<th width="80"><p>Cons. Per Pcs LBS</p></th>
							<th width="80"><p>Plan Knit Qty</p></th>
							<th width="80"><p>Ex. Knit %</p></th>

							<th width="80"><p>Yarn Req Qty</p></th>
							<th width="80"><p>Yarn Rcv</p></th>
							<th width="80"><p>Yarn Rcv Bal</p></th>
							<th width="80"><p>Yarn Issue to Knitting</p></th>
							<th width="80"><p>Yarn Issue Bal</p></th>
							<th width="80"><p>Possible Knitting Pcs </p></th>

							<th width="80"><p>Knitting  Qty</p></th>
							<th width="80"><p>Knitting  Bal</p></th>
							<th width="80"><p>Knit %</p></th>
							<th width="80"><p>Knitting  WIP</p></th>

							<th width="80"><p>Linking  Qty</p></th>
							<th width="80"><p>Linking  Bal</p></th>

							<th width="80"><p>Trimming  Qty</p></th>
							<th width="80"><p>Trimming  Bal</p></th>

							<th width="80"><p>Mending  Qty</p></th>
							<th width="80"><p>Mending  Bal</p></th>

							<th width="80"><p>Wash  Qty</p></th>
							<th width="80"><p>Wash  Bal</p></th>

							<th width="80"><p>Sewing  Qty</p></th>
							<th width="80"><p>Sewing  Bal</p></th>

							<th width="80"><p>Issue  Qty</p></th>
							<th width="80"><p>Rcv  Qty</p></th>
							<th width="80"><p>Bal  Qty</p></th>

							<th width="80"><p>Iron  Qty</p></th>
							<th width="80"><p>Iron  Bal</p></th>
							<th width="80"><p>Re-Iron</p></th>

							<th width="80"><p>Fin  Qty</p></th>
							<th width="80"><p>Fin  Bal</p></th>
							<th width="80"><p>Fin %</p></th>

							<th width="80"><p>Insp  Qty</p></th>
							<th width="80"><p>Insp  Bal</p></th>

							<th width="80"><p>KniEx-Factory tting  Qty</p></th>
							<th width="80"><p>Ex-Factory WIP</p></th>
							<th width="80"><p>Short/Excess</p></th>

							<th width="80"><p>Knit to Ship Balance Qty</p></th>
							<th width="80"><p>Gate Pas</p></th>
							<th width="80"><p>Left over Receive</p></th>
							<th width="80"><p>Left over Issue</p></th>
							<th width="80"><p>Left over Stock</p></th>

						</tr>
					</thead>
				</table>
				<div>
					<table cellspacing="0" border="1" class="rpt_table"  width="<?=$tbl_width;?>" rules="all"   style="max-height: 400px;overflow-y: auto;overflow-x: hidden;"  id="scroll_body">
						<tbody>
							<?
							$i=1;
							$gr_job_qty = 0;
							$gr_cons_qty = 0;
							$gr_plan_qty = 0;
							$gr_yarn_req_qty = 0;
							$gr_yarn_rcv_qty = 0;
							$gr_yarn_rcv_bal_qty = 0;
							$gr_yarn_issue_qty = 0;
							$gr_issue_bal_qty = 0;
							$gr_lay_qty = 0;
							$gr_knit_com_qty = 0;
							$gr_knit_bal_qty = 0;
							$gr_knit_prsnt_qty = 0;
							$gr_knit_wip_qty = 0;
							$gr_linking_qty = 0;
							$gr_linking_bal_qty = 0;
							$gr_triming_qty = 0;
							$gr_triming_bal_qty = 0;
							$gr_mending_qty = 0;
							$gr_mending_bal_qty = 0;
							$gr_wash_qty = 0;
							$gr_wash_bal_qty = 0;
							$gr_sewing_qty = 0;
							$gr_sewing_bal_qty = 0;
							$gr_emb_issue_qty = 0;
							$gr_emb_rcv_qty = 0;
							$gr_emb_bal_qty = 0;
							$gr_iron_qty = 0;
							$gr_iron_bal_qty = 0;
							$gr_reiron_qty = 0;
							$gr_fin_qty = 0;
							$gr_fin_bal_qty = 0;
							$gr_fin_prsnt = 0;
							$gr_insp_qty = 0;
							$gr_insp_bal_qty = 0;
							$gr_exf_qty = 0;
							$gr_exf_wip_qty = 0;
							$gr_exf_short_excess_qty = 0;
							$gr_knit_to_ship_qty = 0;
							$gr_gate_pass_qty = 0;
							$gr_leftover_issue_qty = 0;
							$gr_leftover_rcv_qty = 0;
							$gr_leftover_bal_qty = 0;
							foreach ($data_array as $job_no => $r)
							{
								
								$lotratio_qty = $lotratio_data_array[$job_no];

								$yarn_req_qty = $yarn_data_array[$job_no]['req_qty'];
								$yarn_rcv_qty = $yarn_data_array[$job_no]['rcv'];
								$yarn_issue_qty = $yarn_data_array[$job_no]['issue'];
								$yarn_rcv_rtn_qty = $yarn_data_array[$job_no]['rcv_rtn'];
								$yarn_issue_rtn_qty = $yarn_data_array[$job_no]['issue_rtn'];
								$yarn_rcv_bal = $yarn_req_qty - $yarn_rcv_qty;
								$yarn_issue_bal = $yarn_rcv_qty - $yarn_issue_qty;

								$cons_per_pcs = $yarn_req_qty/$r['po_quantity'];

								$knit_complete = $rmg_data_array[$job_no][1];
								$knit_bal = $r['plan_cut'] - $knit_complete;
								$knit_prsnt = (($knit_complete - $r['po_quantity'])/$r['po_quantity'])*100;
								$knit_wip = $lotratio_qty - $knit_complete;
								$linking_complete = $rmg_data_array[$job_no][4];
								$linking_wip = $knit_complete - $linking_complete;
								$triming_complete = $rmg_data_array[$job_no][111];
								$triming_bal = $linking_wip - $triming_complete;
								$mending_complete = $rmg_data_array[$job_no][112];
								$mending_bal = $triming_complete - $mending_complete;
								$wash_complete = $rmg_data_array[$job_no][3];
								$wash_bal = $mending_complete - $wash_complete;
								$sew_complete = $rmg_data_array[$job_no][5];
								$sew_bal = $wash_complete - $sew_complete;
								$iron_complete = $rmg_data_array[$job_no][67];
								$iron_bal = $wash_complete - $iron_complete;
								$re_iron_qty = $reiron_data_array[$job_no][67];
								$finishing_complete = $rmg_data_array[$job_no][8];
								$finishing_bal = $wash_complete - $finishing_complete;
								$finishing_prsnt = (($finishing_complete - $r['po_quantity'])/$r['po_quantity'])*100;
								$emb_issue_qty=$rmg_data_array[$job_no][63];
								$emb_rcv_qty=$rmg_data_array[$job_no][64];
								$emb_bal= ($rmg_data_array[$job_no][63] - $rmg_data_array[$job_no][64]);
								$insp_qty = $insp_data_array[$job_no];
								$insp_bal = $finishing_complete - $insp_qty;
								$exf_qty = $exf_data_array[$job_no];
								$exf_bal = $finishing_complete - $exf_qty;
								$exf_short_excess = $r['po_quantity'] - $exf_qty;
								$knit_to_ship = $knit_complete - $exf_qty;
								$gate_pass_qty = $gatepass_data_array[$job_no];
							
								$lftovr_rcv = $lftovr_data_array_rcv[$job_no]['rcv'];
							
								$lftovr_issue = $lftovr_data_array_issue[$job_no]['issue'];
								$lftovr_bal = $lftovr_rcv - $lftovr_issue;

								$ex_knit_prsnt = (($r['plan_cut'] - $r['po_quantity'])/$r['po_quantity'])*100;


								if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor='<? echo $bgcolor; ?>' onclick="change_color('tr_2nd<? echo $i;?>','<? echo $bgcolor; ?>')" id="tr_2nd<? echo $i;?>">
									<td width="40"><?=$i;?></td>
									<td width="80"><?=$buyer_arr[$r['buyer_name']];?></td>
									<td width="130"><p><?=$r['style'];?></p></td>
									<td width="80" ><?=$job_no;?></td>
									<td width="150" ><p><?=chop($r['po_number'],',');?></p></td>
									<td width="80" ><?=$gauge_arr[$r['gauge']];?></td>
									<td align="right" width="80"><?=$r['po_quantity'];?></td>
									<td align="right" width="80" ><?=number_format($cons_per_pcs,4);?></td>
									<td align="right" width="80" ><?=number_format($r['plan_cut'],0);?></td>
									<td align="right" width="80" ><?=number_format($ex_knit_prsnt,2);?></td>

									<td align="right" width="80" ><?=number_format($yarn_req_qty,0);?></td>
									<td align="right" width="80" ><?=number_format($yarn_rcv_qty,0);?></td>
									<td align="right" width="80"><?=number_format($yarn_rcv_bal,0);?></td>
									<td align="right" width="80" ><?=number_format($yarn_issue_qty,0);?></td>
									<td align="right" width="80" ><?=number_format($yarn_issue_bal,0);?></td>
									<td align="right" width="80"> <?=number_format($lotratio_qty,0);?></td>

									<td align="right" width="80"><?=number_format($knit_complete,0);?></td>
									<td align="right" width="80"><?=number_format($knit_bal,0);?></td>
									<td align="right" width="80"><?=number_format($knit_prsnt,2);?></td>
									<td align="right" width="80"> <?=number_format($knit_wip,0);?></td>

									<td align="right" width="80"><?=number_format($linking_complete,0);?></td>
									<td align="right" width="80"><?=number_format($linking_wip,0);?></td>

									<td align="right" width="80"> <?=number_format($triming_complete,0);?></td>
									<td align="right" title="<?=$linking_complete."__".$triming_complete?>" width="80"><?=number_format($linking_complete-$triming_complete,0);$gr_triming_bal_qty=+($linking_complete-$triming_complete);//$triming_bal?></td>

									<td align="right" width="80"><?=number_format($mending_complete,0);?></td>
									<td align="right" width="80"><?=number_format($mending_bal,0);?></td>

									<td align="right" width="80"><?=number_format($wash_complete,0);?></td>
									<td align="right" width="80"><?=number_format($wash_bal,0);?></td>

									<td align="right" width="80"><?=number_format($sew_complete,0);?></td>
									<td align="right" width="80"><?=number_format($sew_bal,0);?></td>

									<td align="right" width="80"><?=number_format($emb_issue_qty,0);?></td>
									<td align="right" width="80"><?=number_format($emb_rcv_qty,0);?></td>
									<td align="right" width="80"><?=number_format($emb_bal,0);?></td>

									<td align="right" width="80"><?=number_format($iron_complete,0);?></td>
									<td align="right" width="80"><?=number_format($iron_bal,0);?></td>
									<td align="right" width="80"><?=number_format($re_iron_qty,0);?></td>

									<td align="right" width="80"><?=number_format($finishing_complete,0);?></td>
									<td align="right" width="80"><?=number_format($finishing_bal,0);?></td>
									<td align="right" width="80"><?=number_format($finishing_prsnt,2);?></td>

									<td align="right" width="80"><?=number_format($insp_qty,0);?></td>
									<td align="right" width="80"><?=number_format($insp_bal,0);?></td>

									<td align="right" width="80"><?=number_format($exf_qty,0);?></td>
									<td align="right" width="80"><?=number_format($exf_bal,0);?></td>
									<td align="right" width="80"><?=number_format($exf_short_excess,0);?></td>

									<td align="right" width="80"><?=number_format($knit_to_ship,0);?></td>
									<td align="right" width="80"><?=number_format($gate_pass_qty,0);?></td>
									<td align="right" width="80"><?=number_format($lftovr_rcv,0);?></td>
									<td align="right" width="80"><?=number_format($lftovr_issue,0);?></td>
									<td align="right" width="80"><?=number_format($lftovr_bal,0);?></td>
									<td align="left" width="120"><p><?=chop($r['shiping_status'],",");?></p></td>
								</tr>
								<?
								$i++;
								$gr_job_qty += $r['po_quantity'];
								$gr_cons_qty += $a;
								$gr_plan_qty += $r['plan_cut'];

								$gr_yarn_req_qty += $yarn_req_qty;
								$gr_yarn_rcv_qty += $yarn_rcv_qty;
								$gr_yarn_rcv_bal_qty += $yarn_rcv_bal;
								$gr_yarn_issue_qty += $yarn_issue_qty;
								$gr_issue_bal_qty += $yarn_issue_bal;
								$gr_lay_qty += $lotratio_qty;

								$gr_knit_com_qty += $knit_complete;
								$gr_knit_bal_qty += $knit_bal;
								$gr_knit_prsnt_qty += $knit_prsnt;
								$gr_knit_wip_qty += $knit_wip;

								$gr_linking_qty += $linking_complete;
								$gr_linking_bal_qty += $linking_wip;
								$gr_triming_qty += $triming_complete;
								// $gr_triming_bal_qty += $triming_bal;
								$gr_mending_qty += $mending_complete;
								$gr_mending_bal_qty += $mending_bal;
								$gr_wash_qty += $wash_complete;
								$gr_wash_bal_qty += $wash_bal;
								$gr_sewing_qty += $sew_complete;
								$gr_sewing_bal_qty += $sew_bal;
								$gr_emb_issue_qty += $emb_issue_qty;
								$gr_emb_rcv_qty += $emb_rcv_qty;
								$gr_emb_bal_qty += $emb_bal;
								$gr_iron_qty += $iron_complete;
								$gr_iron_bal_qty += $iron_bal;
								$gr_reiron_qty += $re_iron_qty;
								$gr_fin_qty += $finishing_complete;
								$gr_fin_bal_qty += $finishing_bal;
								$gr_fin_prsnt += $finishing_prsnt;
								$gr_insp_qty += $insp_qty;
								$gr_insp_bal_qty += $insp_bal;
								$gr_exf_qty += $exf_qty;
								$gr_exf_wip_qty += $exf_bal;
								$gr_exf_short_excess_qty += $exf_short_excess;
								$gr_knit_to_ship_qty += $knit_to_ship;
								$gr_gate_pass_qty += $gate_pass_qty;
								$gr_leftover_issue_qty += $lftovr_issue;
								$gr_leftover_rcv_qty += $lftovr_rcv;
								$gr_leftover_bal_qty += $lftovr_bal;
							}
							?>
						</tbody>
					</table>
				</div>
				<table cellspacing="0" border="1" class="rpt_table" width="<?=$tbl_width;?>" rules="all">
						<tfoot>
						<tr>
							<th width="40"></th>
							<th width="80" ></th>
							<th width="130" ></th>
							<th width="80" ></th>
							<th width="150" ></th>
							<th width="80">Total</th>
							<th width="80"><?=number_format($gr_job_qty,0);?></th>
							<th width="80" ><?=number_format($gr_cons_qty,0);?></th>
							<th width="80" ><?=number_format($gr_plan_qty,0);?></th>
							<th width="80" ><?=number_format($a,0);?></th>

							<th width="80" ><?=number_format($gr_yarn_req_qty,0);?></th>
							<th width="80" ><?=number_format($gr_yarn_rcv_qty,0);?></th>
							<th width="80"><?=number_format($gr_yarn_rcv_bal_qty,0);?></th>
							<th width="80" ><?=number_format($gr_yarn_issue_qty,0);?></th>
							<th width="80" ><?=number_format($gr_issue_bal_qty,0);?></th>
							<th width="80"> <?=number_format($gr_lay_qty,0);?></th>

							<th width="80"><?=number_format($gr_knit_com_qty,0);?></th>
							<th width="80"><?=number_format($gr_knit_bal_qty,0);?></th>
							<th width="80"><?=number_format($gr_knit_prsnt_qty,0);?></th>
							<th width="80"> <?=number_format($gr_knit_wip_qty,0);?></th>

							<th width="80"><?=number_format($gr_linking_qty,0);?></th>
							<th width="80"><?=number_format($gr_linking_bal_qty,0);?></th>

							<th width="80"> <?=number_format($gr_triming_qty,0);?></th>
							<th width="80"><?=number_format($gr_triming_bal_qty,0);?></th>

							<th width="80"><?=number_format($gr_mending_qty,0);?></th>
							<th width="80"><?=number_format($gr_mending_bal_qty,0);?></th>

							<th width="80"><?=number_format($gr_wash_qty,0);?></th>
							<th width="80"><?=number_format($gr_wash_bal_qty,0);?></th>

							<th width="80"><?=number_format($gr_sewing_qty,0);?></th>
							<th width="80"><?=number_format($gr_sewing_bal_qty,0);?></th>

							<th width="80"><?=number_format($gr_emb_issue_qty,0);?></th>
							<th width="80"><?=number_format($gr_emb_rcv_qty,0);?></th>
							<th width="80"><?=number_format($gr_emb_bal_qty,0);?></th>

							<th width="80"><?=number_format($gr_iron_qty,0);?></th>
							<th width="80"><?=number_format($gr_iron_bal_qty,0);?></th>
							<th width="80"><?=number_format($gr_reiron_qty,0);?></th>

							<th width="80"><?=number_format($gr_fin_qty,0);?></th>
							<th width="80"><?=number_format($gr_fin_bal_qty,0);?></th>
							<th width="80"><?=number_format($gr_fin_prsnt,2);?></th>

							<th width="80"><?=number_format($gr_insp_qtya,0);?></th>
							<th width="80"><?=number_format($gr_insp_bal_qty,0);?></th>

							<th width="80"><?=number_format($gr_exf_qty,0);?></th>
							<th width="80"><?=number_format($gr_exf_wip_qty,0);?></th>
							<th width="80"><?=number_format($gr_exf_short_excess_qty,0);?></th>

							<th width="80"><?=number_format($gr_knit_to_ship_qty,0);?></th>
							<th width="80"><?=number_format($gr_gate_pass_qty,0);?></th>
						
							<th width="80"><?=number_format($gr_leftover_rcv_qty,0);?></th>
							<th width="80"><?=number_format($gr_leftover_issue_qty,0);?></th>
							<th width="80"><?=number_format($gr_leftover_bal_qty,0);?></th>
							<th width="120"></th>
						</tr>
					</tfoot>
				</table>

			</div>

			<div class="summary-container" style="float:left;width:1220px;margin-bottom:10px; margin-left:18px;">
				<table>
					<tr height="20"><td colspan="3"></td></tr>
					<tr>
						<td valign="top">
							<!-- ============================== Accessories Part ============================= -->
							<div style="float:left;width:640px;">
								<table cellspacing="0" border="1" class="rpt_table"  width="640" rules="all">
									<thead>
										<tr>
											<th colspan="8">Accessories Status</th>
										</tr>
										<tr>
											<th width="150">Item</th>
											<th width="70">UOM</th>
											<th width="70">Req. Qty.</th>
											<th width="70">WO Qty</th>
											<th width="70">Received</th>
											<th width="70">Recv. Balance</th>
											<th width="70">Issued</th>
											<th width="70">Left Over</th>
										</tr>
									</thead>
									<tbody>
										<?
										$tot_req_qty = 0;
										$tot_wo_qty = 0;
										$tot_rcv_qty = 0;
										$tot_rcv_bal = 0;
										$tot_issue_qty = 0;
										$tot_leftovr_qty = 0;
										foreach ($trims_data_array as $trims_id => $r)
										{
											$wo_qty = $trims_wo_array[$trims_id]['book_qty'];
											$rcv_qty = $trimsDataArray[$trims_id]['rcv_qty'];
											$issue_qty = $trimsDataArray[$trims_id]['issue_qty'];
											$rcv_bal = $wo_qty - $rcv_qty;
											$leftover = $rcv_qty - $issue_qty;
											?>
											<tr>
												<td><?=$lib_trims_group_name[$trims_id];?></td>
												<td><?=$unit_of_measurement[$r['uom']];?></td>
												<td align="right"><?=$r['req_qty'];?></td>
												<td align="right"><?=number_format($wo_qty,2);?></td>
												<td align="right"><?=number_format($rcv_qty,2);?></td>
												<td align="right"><?=number_format($rcv_bal,2);?></td>
												<td align="right"><?=number_format($issue_qty,2);?></td>
												<td align="right"><?=number_format($leftover,2);?></td>
											</tr>
											<?
											$tot_req_qty += $r['req_qty'];
											$tot_wo_qty += $wo_qty;
											$tot_rcv_qty += $rcv_qty;
											$tot_rcv_bal += $rcv_bal;
											$tot_issue_qty += $issue_qty;
											$tot_leftovr_qty += $leftover;
										}
										?>
									</tbody>
									<tfoot>
										<tr>
											<th>Total</th>
											<th></th>
											<th><?=number_format($tot_req_qty,2) ;?></th>
											<th><?=number_format($tot_wo_qty,2) ;?></th>
											<th><?=number_format($tot_rcv_qty,2) ;?></th>
											<th><?=number_format($tot_rcv_bal,2) ;?></th>
											<th><?=number_format($tot_issue_qty,2) ;?></th>
											<th><?=number_format($tot_leftovr_qty,2) ;?></th>
										</tr>
									</tfoot>
								</table>
							</div>
						</td>
						<td valign="top">
							<!-- ================================ YArn part =========================== -->
							<div style="float:left;width:200px;margin-left:10px;">
								<table cellspacing="0" border="1" class="rpt_table"  width="200" rules="all">
									<thead>
										<tr>
											<th colspan="2">Yarn Status</th>
										</tr>
										<tr>
											<th width="130">Particulars</th>
											<th width="70">Qty</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>Yarn Required</td>
											<td align="right"><?=number_format($gr_yarn_req_qty,0);?></td>
										</tr>
										<tr>
											<td>Yarn Received</td>
											<td align="right"><?=number_format($gr_yarn_rcv_qty,0);?></td>
										</tr>
										<tr>
											<td>Yarn Rcvd. Balance</td>
											<td align="right"><?=number_format($gr_yarn_rcv_bal_qty,0);?></td>
										</tr>
										<tr>
											<td>Yarn Issue</td>
											<td align="right"><?=number_format($gr_yarn_issue_qty,0);?></td>
										</tr>
										<tr>
											<td>Yarn Issue Balance</td>
											<td align="right"><?=number_format($gr_issue_bal_qty,0);?></td>
										</tr>
									</tbody>
								</table>
							</div>
						</td>
						<td valign="top">
							<!-- ============================= garments part ================================ -->
							<div style="float:left;width:270px;margin-left:10px;">
								<table cellspacing="0" border="1" class="rpt_table"  width="270" rules="all">
									<thead>
										<tr>
											<th colspan="3">Garments Status</th>
										</tr>
										<tr>
											<th width="30">Sl</th>
											<th width="170">Particulars</th>
											<th width="70">Left Over Qty</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>1</td>
											<td>Linking Balance</td>
											<td align="right"><?=number_format($gr_linking_bal_qty,0);?></td>
										</tr>
										<tr>
											<td>2</td>
											<td>Trimming Balance</td>
											<td align="right"><?=number_format($gr_triming_bal_qty,0);?></td>
										</tr>
										<tr>
											<td>3</td>
											<td>Mending Balance</td>
											<td align="right"><?=number_format($gr_mending_bal_qty,0);?></td>
										</tr>
										<tr>
											<td>4</td>
											<td>Wash Balance</td>
											<td align="right"><?=number_format($gr_wash_bal_qty,0);?></td>
										</tr>
										<tr>
											<td>5</td>
											<td>Sewing Balance</td>
											<td align="right"><?=number_format($gr_sewing_bal_qty,0);?></td>
										</tr>
										<tr>
											<td>6</td>
											<td>Embellishment Balance</td>
											<td align="right"><?=number_format($gr_emb_bal_qty,0);?></td>
										</tr>
										<tr>
											<td>7</td>
											<td>Iron Balance</td>
											<td align="right"><?=number_format($gr_iron_bal_qty,0);?></td>
										</tr>
										<tr>
											<td>8</td>
											<td>Finishing Balance</td>
											<td align="right"><?=number_format($gr_fin_bal_qty,0);?></td>
										</tr>
										<tr>
											<td>9</td>
											<td>Ex-factory Balance</td>
											<td align="right"><?=number_format($gr_exf_wip_qty,0);?></td>
										</tr>
										<tr>
											<td></td>
											<td><b>Total Qty (Knit ot Ship)</b></td>
											<td align="right"><?=number_format(($gr_linking_bal_qty+$gr_triming_bal_qty+$gr_mending_bal_qty+$gr_sewing_bal_qty+$gr_wash_bal_qty+$gr_emb_bal_qty+$gr_iron_bal_qty+$gr_fin_bal_qty+$gr_exf_wip_qty),0);?></td>
										</tr>
										<tr>
											<td>10</td>
											<td>Gate Pass</td>
											<td align="right"><?=number_format($gr_gate_pass_qty,0);?></td>
										</tr>
										<tr>
											<td>11</td>
											<td>Leftover Received</td>
											<td align="right"><?=number_format($gr_leftover_bal_qty,0);?></td>
										</tr>
										<tr>
											<td></td>
											<td><b>Leftover Outstanding</b></td>
											<td align="right"><?=number_format(($gr_gate_pass_qty+$gr_leftover_bal_qty),0);?></td>
										</tr>
									</tbody>
								</table>
							</div>
						</td>
					</tr>
				</table>
			</div>

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
	//$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();



}
