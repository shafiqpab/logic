<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];


if($action=="load_drop_down_buyer")
{
	$party="1,3,21,90";
	echo create_drop_down("cbo_buyer_id", 120, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in ($data) $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in ($party)) group by buy.id, buy.buyer_name order by buy.buyer_name", "id,buyer_name", 1, "-- Select Buyer --", $selected, "");
	exit();
}


if ($action=="load_drop_down_store")
{
	echo create_drop_down( "cbo_store_name", 120, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id  and a.status_active=1 and a.is_deleted=0 and a.company_id in ($data) and  b.category_type=2 group by a.id,a.store_name order by a.store_name","id,store_name", 1, "--Select Store--", 1, "",0 );
	exit();
}

if ($action=="load_drop_down_supplier")
{
	$dataArr = explode("_",$data);
	if($dataArr[0]==5 || $dataArr[0]==3)
	{
		echo create_drop_down( "cbo_supplier_id", 100, "select id,company_name from lib_company where status_active=1 and is_deleted=0 order by company_name", "id,company_name",1, "-- All Supplier --", "", "",0,"" );
	}
	else
	{
		echo create_drop_down( "cbo_supplier_id", 100, "select DISTINCT(c.id),c.supplier_name from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$dataArr[1]' and b.party_type in(1,9) order by id,supplier_name","id,supplier_name", 1, "-- All Supplier --", $selected, "",0 );
	}
}

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array, selected_name = new Array(); selected_attach_id = new Array();

		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}

		function js_set_value(id)
		{
			var str=id.split("_");
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFFF' );
			var strdt=str[2];
			str=str[1];

			if( jQuery.inArray(  str , selected_id ) == -1 ) {
				selected_id.push( str );
				selected_name.push( strdt );
			}
			else
			{
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str  ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i,1 );
			}
			var id = '';
			var ddd='';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				ddd += selected_name[i] + ',';
			}
			id = id.substr( 0, id.length - 1 );
			ddd = ddd.substr( 0, ddd.length - 1 );
			$('#hide_job_id').val( id );
			$('#hide_job_no').val( ddd );
		}

	</script>
	</head>
	<body>
		<div align="center">
			<form name="styleRef_form" id="styleRef_form">
				<fieldset style="width:580px;">
					<table width="570" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
						<thead>
							<th>Buyer</th>
							<th>Search By</th>
							<th id="search_by_td_up" width="170">Please Enter Job No</th>
							<th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
							<input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
						</thead>
						<tbody>
							<tr>
								<td align="center">
									<?
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 );
									?>
								</td>
								<td align="center">
									<?
									$search_by_arr=array(1=>"Job No",2=>"Style Ref");
									$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
									echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
									?>
								</td>
								<td align="center" id="search_by_td">
									<input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
								</td>
								<td align="center">
									<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>', 'create_job_no_search_list_view', 'search_div', 'buyer_wise_finish_fabric_stock_summary_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
								</td>
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

if($action=="create_job_no_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	$year_id=$data[4];

	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');

	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and buyer_name=$data[1]";
	}

	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";
	if($search_by==2) $search_field="style_ref_no"; else $search_field="job_no";
	//$year="year(insert_date)";
	if($db_type==0) $year_field_by="year(insert_date) as year ";
	else if($db_type==2) $year_field_by="to_char(insert_date,'YYYY') as year ";
	if($db_type==0) $month_field_by="and month(insert_date)";
	else if($db_type==2) $month_field_by="and to_char(insert_date,'MM')";
	if($db_type==0) $year_field="and year(insert_date)=$year_id";
	else if($db_type==2) $year_field="and to_char(insert_date,'YYYY')";

	if($db_type==0)
	{
		if($year_id==0)$year_cond=""; else $year_cond="and year(insert_date)='$year_id'";
	}
	else if($db_type==2)
	{
		if($year_id==0)$year_cond=""; else $year_cond="and to_char(insert_date,'YYYY')='$year_id'";
	}
	else $year_cond="";

	$arr=array (0=>$company_arr,1=>$buyer_arr);

	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_field_by from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond  order by job_no";

	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","620","270",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','',1) ;
	exit();
}

if($action=="report_generate")
{
	$started = microtime(true);
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_company_id 	= str_replace("'","",$cbo_company_id);
	$cbo_store_name 	= str_replace("'","",$cbo_store_name);
	$buyer_id 			= str_replace("'","",$cbo_buyer_id);
	$job_no 			= trim(str_replace("'","",$txt_job_no));
	$job_year 			= str_replace("'","",$cbo_year);
	$cbo_value_with 	= str_replace("'","",$cbo_value_with);
	$date_from 		 	= str_replace("'","",$txt_date_from);
	$date_to 		 	= str_replace("'","",$txt_date_to);
	$report_type 		= str_replace("'","",$cbo_report_type);

	if($cbo_store_name > 0){
		$store_cond = " and b.store_id in ($cbo_store_name)";
		$store_cond_2 = " and c.store_id in ($cbo_store_name)";
	}

	if($buyer_id==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and d.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and d.buyer_id=$buyer_id";
	}

	if($db_type==0)
	{
		if($job_year==0) $year_cond=""; else $year_cond=" and YEAR(f.insert_date)=$job_year";
	}
	else if($db_type==2)
	{
		if($job_year==0) $year_cond=""; else $year_cond=" and to_char(f.insert_date,'YYYY')=$job_year";
	}

	$date_cond="";
	if($date_from!="" && $date_to!="")
	{
		if($db_type==0) $start_date=change_date_format($date_from,"yyyy-mm-dd","");
		else if($db_type==2) $start_date=change_date_format($date_from,"","",1);

		if($db_type==0)$end_date=change_date_format($date_to,"yyyy-mm-dd","");
		else if($db_type==2) $end_date=change_date_format($date_to,"","",1);

		$date_cond   = " and b.transaction_date <= '$end_date'";
		$date_cond_2 = " and c.transaction_date <= '$end_date'";
	}

	$company_arr 	= return_library_array("select id, company_name from lib_company where status_active=1","id","company_name");
	$buyer_arr 		= return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );

	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and f.job_no_prefix_num in ($job_no) ";

	if($job_no != "" || $buyer_id!=0)
	{
		$serch_ref_sql_1 = "SELECT c.booking_no from wo_booking_dtls c, wo_booking_mst d, wo_po_break_down e, wo_po_details_master f where f.job_no=e.job_no_mst and c.booking_type in (1,4) and c.booking_no=d.booking_no and c.po_break_down_id=e.id and f.company_name in ($cbo_company_id) $buyer_id_cond $job_no_cond $year_cond ";

		$concate="";
		if($job_no == "")
		{
			$concate = " union all ";
			$serch_ref_sql_2 = " SELECT d.booking_no from wo_non_ord_samp_booking_mst d where d.booking_type = 4 and d.company_id in ($cbo_company_id) $booking_no_cond $buyer_id_cond ";
		}
		$serch_ref_sql = $serch_ref_sql_1.$concate.$serch_ref_sql_2;

		$serch_ref_result = sql_select($serch_ref_sql);

		foreach ($serch_ref_result as $val)
		{
			$search_book_arr[$val[csf("booking_no")]] = $val[csf("booking_no")];
		}
		if(empty($search_book_arr))
		{
			echo "<p style='font-weight:bold;text-align:center;font-size:20px;'>Booking No not found</p>";
			die;
		}
	}

	if(!empty($search_book_arr))
	{
		$search_book_nos="'".implode("','",$search_book_arr)."'";
		$search_book_arr = explode(",", $search_book_nos);

		$all_book_nos_cond=""; $bookCond="";
		if($db_type==2 && count($search_book_arr)>999)
		{
			$all_search_book_arr_chunk=array_chunk($search_book_arr,999) ;
			foreach($all_search_book_arr_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$bookCond.="  e.booking_no in($chunk_arr_value) or ";
			}

			$all_book_nos_cond.=" and (".chop($bookCond,'or ').")";
		}
		else
		{
			$all_book_nos_cond=" and e.booking_no in($search_book_nos)";
		}
	}


	$rcv_select = " b.floor_id, b.room, b.rack, b.self,";
	$rcv_group = " b.floor_id, b.room, b.rack, b.self,";

	$rcv_sql = "SELECT b.id,e.id as batchId,e.booking_no, e.booking_no_id, e.booking_without_order, a.company_id,a.receive_basis, a.knitting_source,a.knitting_company,a.booking_id as wo_pi_prod_id,a.booking_no as wo_pi_prod_no, b.transaction_date, b.prod_id, b.store_id, $rcv_select c.body_part_id,c.fabric_description_id, c.gsm, c.width, f.color as color_id, b.cons_uom,listagg(c.dia_width_type,',') within group (order by c.dia_width_type) as dia_width_type, listagg(d.po_breakdown_id,',') within group (order by d.po_breakdown_id) as po_breakdown_id, b.cons_quantity as quantity,b.order_rate, b.order_amount, b.pi_wo_batch_no, a.lc_sc_no
	FROM inv_receive_master a, inv_transaction b, pro_finish_fabric_rcv_dtls c left join order_wise_pro_details d on c.trans_id = d.trans_id and d.entry_form=37 and d.po_breakdown_id <>0, pro_batch_create_mst e, product_details_master f
	WHERE a.company_id in ($cbo_company_id) and a.id = b.mst_id and b.id=c.trans_id and b.transaction_type=1 and a.entry_form=37 and a.status_active =1 and b.status_active =1 and c.status_active =1 and b.pi_wo_batch_no=e.id and b.prod_id=f.id $store_cond $date_cond  $all_book_nos_cond
	group by b.id,e.id,e.booking_no,e.booking_no_id, e.booking_without_order, a.company_id,a.receive_basis, a.knitting_source,a.knitting_company, a.booking_id, a.booking_no, b.transaction_date, b.prod_id, b.store_id, $rcv_group c.body_part_id, c.fabric_description_id, c.gsm, c.width, f.color, b.cons_uom,c.dia_width_type,b.cons_quantity, b.order_rate, b.order_amount, b.pi_wo_batch_no, a.lc_sc_no order by a.company_id,e.booking_no"; 
	// echo $rcv_sql;die;
	$rcv_data = sql_select($rcv_sql);
	foreach ($rcv_data as  $val)
	{
		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($val[csf('transaction_date')]));		
		$dia_width_type_ref = implode(",",array_unique(explode(",", $val[csf("dia_width_type")])));

		$all_prod_id[$val[csf("prod_id")]] = $val[csf("prod_id")];

		$data=explode("-", $val[csf("booking_no")]);
		if ($data[1]=='SMN') 
		{
			$all_samp_book_arr[$val[csf("booking_no")]] = "'".$val[csf("booking_no")]."'";
		}
		else
		{
			$all_po_id_arr[$val[csf("po_breakdown_id")]] = $val[csf("po_breakdown_id")];
		}
		// $booking_no_arr[$val[csf("booking_no")]] = "'".$val[csf("booking_no")]."'";
		$batch_id_arr[$val[csf("pi_wo_batch_no")]] = $val[csf("pi_wo_batch_no")];
	}
	// echo "<pre>"; print_r($all_samp_book_arr);die;


	$trans_in_select = " c.floor_id, c.room, c.rack, c.self,";
	$trans_in_group = " c.floor_id, c.room, c.rack, c.self,";

	$trans_in_sql = "SELECT c.transaction_date, c.pi_wo_batch_no,e.id as batchId, e.batch_no,e.booking_no, e.booking_no_id, e.booking_without_order, c.body_part_id, c.prod_id,c.store_id, $trans_in_select d.detarmination_id, d.gsm, d.dia_width as width, d.color as color_id, c.cons_uom, sum(c.cons_quantity) as quantity,c.order_rate, c.order_amount, listagg(f.po_breakdown_id,',') within group (order by f.po_breakdown_id) as po_breakdown_id
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c left join order_wise_pro_details f on c.id = f.trans_id and f.trans_type = 5 and f.status_active=1 and f.po_breakdown_id<>0, product_details_master d, pro_batch_create_mst e
	where a.id=b.mst_id and b.to_trans_id=c.id and c.prod_id=d.id and c.pi_wo_batch_no=e.id and c.company_id in ($cbo_company_id) and c.item_category=2 and c.transaction_type=5 and a.status_active=1 and b.status_active=1 and c.status_active=1  and a.entry_form in (14,15,306) $store_cond_2 $date_cond_2 $all_book_nos_cond
	group by c.transaction_date, c.pi_wo_batch_no,e.id, e.batch_no,e.booking_no, e.booking_no_id, e.booking_without_order, c.company_id, c.body_part_id, c.prod_id,c.store_id, $trans_in_group d.detarmination_id, d.gsm, d.dia_width, d.color, c.cons_uom, c.order_rate, c.order_amount order by c.company_id,e.booking_no";
	//echo $trans_in_sql;die;
	$trans_in_data = sql_select($trans_in_sql);
	foreach ($trans_in_data as  $val)
	{
		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($val[csf('transaction_date')]));

		$all_prod_id[$val[csf("prod_id")]] = $val[csf("prod_id")];

		$data=explode("-", $val[csf("booking_no")]);
		if ($data[1]=='SMN') 
		{
			$all_samp_book_arr[$val[csf("booking_no")]] = "'".$val[csf("booking_no")]."'";
		}
		else
		{
			$all_po_id_arr[$val[csf("po_breakdown_id")]] = $val[csf("po_breakdown_id")];
		}
		// $booking_no_arr[$val[csf("booking_no")]] = "'".$val[csf("booking_no")]."'";
		$batch_id_arr[$val[csf("pi_wo_batch_no")]] = $val[csf("pi_wo_batch_no")];
	}
	
	// echo "<pre>"; print_r($all_samp_book_arr); die;

	$all_po_id_arr = array_filter($all_po_id_arr);
	$all_po_id_arr = array_unique(explode(",",implode(",", $all_po_id_arr)));
	if(!empty($all_po_id_arr))
	{
		$all_po_ids=implode(",",$all_po_id_arr);
		$all_po_id_cond=""; $poCond="";
		$all_po_id_cond_2=""; $poCond_2="";
		if($db_type==2 && count($all_po_id_arr)>999)
		{
			$all_po_id_arr_chunk=array_chunk($all_po_id_arr,999) ;
			foreach($all_po_id_arr_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$poCond.="  e.id in($chunk_arr_value) or ";
				$poCond_2.="  b.po_break_down_id in($chunk_arr_value) or ";
			}

			$all_po_id_cond.=" and (".chop($poCond,'or ').")";
			$all_po_id_cond_2.=" and (".chop($poCond_2,'or ').")";
		}
		else
		{
			$all_po_id_cond=" and e.id in($all_po_ids)";
			$all_po_id_cond_2=" and b.po_break_down_id in($all_po_ids)";
		}

		$booking_sql = sql_select("SELECT a.body_part_id,c.booking_no,a.lib_yarn_count_deter_id, c.fabric_color_id, c.gmts_color_id, c.color_type, d.booking_date, d.pay_mode, d.booking_type, d.entry_form, d.is_short,f.company_name, f.job_no, f.style_ref_no, f.buyer_name, f.client_id, f.season_buyer_wise, f.total_set_qnty, f.job_quantity, c.fin_fab_qnty, a.uom, c.rate, d.supplier_id, d.short_booking_type
		from wo_pre_cost_fabric_cost_dtls a,wo_booking_dtls c, wo_booking_mst d, wo_po_break_down e, wo_po_details_master f
		where a.job_no=c.job_no and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and e.status_active=1 and a.id = c.pre_cost_fabric_cost_dtls_id and  f.job_no = e.job_no_mst and c.booking_type =1 and c.booking_no = d.booking_no and c.po_break_down_id = e.id $all_po_id_cond
		union all
		select b.body_part_id,c.booking_no,b.lib_yarn_count_deter_id, c.fabric_color_id, c.gmts_color_id,c.color_type, d.booking_date, d.pay_mode, d.booking_type, d.entry_form, d.is_short,f.company_name, f.job_no, f.style_ref_no, f.buyer_name, f.client_id, f.season_buyer_wise,f.total_set_qnty, f.job_quantity, c.fin_fab_qnty, b.uom, c.rate, d.supplier_id, d.short_booking_type
		from wo_pre_cost_fab_conv_cost_dtls a, wo_pre_cost_fabric_cost_dtls b, wo_booking_dtls c ,  wo_booking_mst d , wo_po_break_down e, wo_po_details_master f
		where b.job_no=c.job_no and a.status_active=1 and b.status_active=1 and c.status_active=1 and e.status_active=1 and a.id = c.pre_cost_fabric_cost_dtls_id and  f.job_no = e.job_no_mst and a.fabric_description = b.id and c.booking_type =4 and c.booking_no = d.booking_no  and c.po_break_down_id = e.id $all_po_id_cond");

		foreach ($booking_sql as  $val)
		{
			$book_po_ref[$val[csf("booking_no")]]["company_name"] 	= $val[csf("company_name")];
			$book_po_ref[$val[csf("booking_no")]]["buyer_name"] 	= $val[csf("buyer_name")];
			$book_po_ref[$val[csf("booking_no")]]["job_no"] 		.= $val[csf("job_no")].",";
			$book_po_ref[$val[csf("booking_no")]]["client_id"] 		= $val[csf("client_id")];
			$book_po_ref[$val[csf("booking_no")]]["season"] 		.= $val[csf("season_buyer_wise")].",";
			$book_po_ref[$val[csf("booking_no")]]["style_ref_no"] 	.= $val[csf("style_ref_no")].",";
			$book_po_ref[$val[csf("booking_no")]]["booking_no"] 	= $val[csf("booking_no")];
			$book_po_ref[$val[csf("booking_no")]]["booking_date"] 	= $val[csf("booking_date")];
			$book_po_ref[$val[csf("booking_no")]]["pay_mode"] 		= $pay_mode[$val[csf("pay_mode")]];
			$book_po_ref[$val[csf("booking_no")]]["short_booking_type"] 		= $short_booking_type[$val[csf("short_booking_type")]];
			if($val[csf("pay_mode")] == 3 || $val[csf("pay_mode")] == 5)
			{
				$book_po_ref[$val[csf("booking_no")]]["supplier"] = $company_arr[$val[csf("supplier_id")]];
			}else{
				$book_po_ref[$val[csf("booking_no")]]["supplier"] = $supplier_arr[$val[csf("supplier_id")]];
			}

			$job_qnty_arr[$val[csf("job_no")]]["qnty"] = $val[csf("job_quantity")]*$val[csf("total_set_qnty")];
			$book_po_ref[$val[csf("booking_no")]][$val[csf("body_part_id")]][$val[csf("lib_yarn_count_deter_id")]][$val[csf("fabric_color_id")]]["qnty"] += $val[csf("fin_fab_qnty")];
			$book_po_ref[$val[csf("booking_no")]][$val[csf("body_part_id")]][$val[csf("lib_yarn_count_deter_id")]][$val[csf("fabric_color_id")]]["color_type"] .= $color_type[$val[csf("color_type")]].",";

			$book_po_ref[$val[csf("booking_no")]][$val[csf("body_part_id")]][$val[csf("lib_yarn_count_deter_id")]][$val[csf("fabric_color_id")]]["amount"] += $val[csf("fin_fab_qnty")]*$val[csf("rate")];

			$bookingType="";
			if($val[csf('booking_type')] == 4)
			{
				$bookingType = "Sample With Order";
			}
			else
			{
				$bookingType = $booking_type_arr[$val[csf('entry_form')]];
			}
			$book_po_ref[$val[csf("booking_no")]]["booking_type"] = $bookingType;
		}
	}

	if(!empty($all_samp_book_arr))
	{
		$all_samp_book_nos_cond=""; $sampBookCond="";
		if($db_type==2 && count($all_samp_book_arr)>999)
		{
			$all_samp_book_arr_chunk=array_chunk($all_samp_book_arr,999) ;
			foreach($all_samp_book_arr_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$sampBookCond.="  a.booking_no in($chunk_arr_value) or ";
			}

			$all_samp_book_nos_cond.=" and (".chop($sampBookCond,'or ').")";
		}
		else
		{
			$all_samp_book_nos_cond=" and a.booking_no in(".implode(",",$all_samp_book_arr).")";
		}

		$non_samp_sql = sql_select("SELECT a.booking_date, a.booking_no, a.pay_mode, a.company_id, a.supplier_id, b.lib_yarn_count_deter_id, b.fabric_color, b.uom, b.color_type_id, b.body_part, a.buyer_id, b.style_des, b.finish_fabric, b.rate 
		from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b 
		where a.booking_no=b.booking_no and b.status_active =1 and a.booking_type =4 $all_samp_book_nos_cond");

		foreach ($non_samp_sql as  $val)
		{
			$book_po_ref[$val[csf("booking_no")]]["booking_no"]   	= $val[csf("booking_no")];
			$book_po_ref[$val[csf("booking_no")]]["booking_date"]  	= $val[csf("booking_date")];
			$book_po_ref[$val[csf("booking_no")]]["company_name"] 	= $val[csf("company_id")];
			$book_po_ref[$val[csf("booking_no")]]["buyer_name"] 	= $val[csf("buyer_id")];
			$book_po_ref[$val[csf("booking_no")]]["style_ref_no"] 	= $val[csf("style_des")];
			$book_po_ref[$val[csf("booking_no")]]["booking_type"] 	= "Sample WithOut Order";
			if($val[csf("pay_mode")] == 3 || $val[csf("pay_mode")] 	== 5)
			{
				$book_po_ref[$val[csf("booking_no")]]["supplier"] = $company_arr[$val[csf("supplier_id")]];
			}else{
				$book_po_ref[$val[csf("booking_no")]]["supplier"] = $supplier_arr[$val[csf("supplier_id")]];
			}

			$book_po_ref[$val[csf("booking_no")]][$val[csf("body_part")]][$val[csf("lib_yarn_count_deter_id")]][$val[csf("fabric_color")]]["qnty"] += $val[csf("finish_fabric")];
			$book_po_ref[$val[csf("booking_no")]][$val[csf("body_part")]][$val[csf("lib_yarn_count_deter_id")]][$val[csf("fabric_color")]]["color_type"] .= $color_type[$val[csf("color_type_id")]].",";

			$book_po_ref[$val[csf("booking_no")]][$val[csf("body_part")]][$val[csf("lib_yarn_count_deter_id")]][$val[csf("fabric_color")]]["amount"] += $val[csf("finish_fabric")]*$val[csf("rate")];
		}
		unset($non_samp_sql);
	}

	foreach ($rcv_data as  $val)
	{
		$buyerId = $book_po_ref[$val[csf("booking_no")]]["buyer_name"];
		$client_id = $book_po_ref[$val[csf("booking_no")]]["client_id"];
		$pay_mode = $book_po_ref[$val[csf("booking_no")]]["pay_mode"];
		if ($client_id=="") $client_id=0;

		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($val[csf('transaction_date')]));
		$dia_width_type_ref = implode(",",array_unique(explode(",", $val[csf("dia_width_type")])));
		
		if($transaction_date >= $date_frm)
		{
			$data_array[$buyerId][$client_id][$pay_mode][$val[csf("cons_uom")]] .= $val[csf("quantity")]."*".$val[csf("order_rate")]."*".$val[csf("receive_basis")]."*".$val[csf("wo_pi_prod_no")]."*".$dia_width_type_ref."*".$val[csf("lc_sc_no")]."*"."1*1"."*".$val[csf("cons_uom")]."__";
		}
		else
		{
			$data_array[$buyerId][$client_id][$pay_mode][$val[csf("cons_uom")]] .= $val[csf("quantity")]."*".$val[csf("order_rate")]."*".$val[csf("receive_basis")]."*".$val[csf("wo_pi_prod_no")]."*".$dia_width_type_ref."*".$val[csf("lc_sc_no")]."*"."1*2"."*".$val[csf("cons_uom")]."__";
		}
		$rate_arr_booking_and_product_wise[$buyerId][$client_id][$pay_mode][$val[csf("cons_uom")]]["quantity"] += $val[csf("quantity")];
		$rate_arr_booking_and_product_wise[$buyerId][$client_id][$pay_mode][$val[csf("cons_uom")]]["amount"] += $val[csf("order_amount")];

		$summary_data_arr[$buyerId][$client_id][$val[csf("cons_uom")]] += $val[csf("quantity")];
		$summary_data_arr[$buyerId][$client_id]['booking_no'] .= $val[csf("booking_no")].',';
		$summary_data_arr[$buyerId][$client_id]['prod_id'] .= $val[csf("prod_id")].',';
		$summary_data_arr[$buyerId][$client_id]['store_id'] .= $val[csf("store_id")].',';
		$summary_data_arr[$buyerId][$client_id]['rack'] .= $val[csf("rack")].',';
		$summary_data_arr[$buyerId][$client_id]['batchId'] .= $val[csf("batchId")].',';
	}
	// echo "<pre>";print_r($summary_data_arr);die;

	foreach ($trans_in_data as  $val) 
	{
		$buyerId = $book_po_ref[$val[csf("booking_no")]]["buyer_name"];
		$client_id = $book_po_ref[$val[csf("booking_no")]]["client_id"];
		$pay_mode = $book_po_ref[$val[csf("booking_no")]]["pay_mode"];
		if ($client_id=="") $client_id=0;

		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($val[csf('transaction_date')]));
		
		if($transaction_date >= $date_frm)
		{
			$data_array[$buyerId][$client_id][$pay_mode][$val[csf("cons_uom")]] .= $val[csf("quantity")]."*".$val[csf("order_rate")]."*"."*".""."*".""."*"."*5*1"."*".$val[csf("cons_uom")]."__";
		}
		else
		{
			$data_array[$buyerId][$client_id][$pay_mode][$val[csf("cons_uom")]] .= $val[csf("quantity")]."*".$val[csf("order_rate")]."*"."*".""."*".""."*"."*5*2"."*".$val[csf("cons_uom")]."__";
		}
		$rate_arr_booking_and_product_wise[$buyerId][$client_id][$pay_mode][$val[csf("cons_uom")]]["quantity"] += $val[csf("quantity")];
		$rate_arr_booking_and_product_wise[$buyerId][$client_id][$pay_mode][$val[csf("cons_uom")]]["amount"] += $val[csf("order_amount")];

		$summary_data_arr[$buyerId][$client_id][$val[csf("cons_uom")]] += $val[csf("quantity")];
		$summary_data_arr[$buyerId][$client_id]['booking_no'] .= $val[csf("booking_no")].',';
		$summary_data_arr[$buyerId][$client_id]['prod_id'] .= $val[csf("prod_id")].',';
		$summary_data_arr[$buyerId][$client_id]['store_id'] .= $val[csf("store_id")].',';
		$summary_data_arr[$buyerId][$client_id]['rack'] .= $val[csf("rack")].',';
		$summary_data_arr[$buyerId][$client_id]['batchId'] .= $val[csf("batchId")].',';
	}
	// echo "<pre>"; print_r($data_array);die;
	// echo "<pre>";print_r($summary_data_arr);die;
	
	$batch_id_arr = array_filter($batch_id_arr);
	if(!empty($batch_id_arr))
	{
		$batch_ids= implode(",",$batch_id_arr);

		$all_batch_ids_cond=""; $batchCond="";
		if($db_type==2 && count($batch_id_arr)>999)
		{
			$batch_id_arr_chunk=array_chunk($batch_id_arr,999) ;
			foreach($batch_id_arr_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$batchCond.="  e.id in($chunk_arr_value) or ";
			}
			$all_batch_ids_cond.=" and (".chop($batchCond,'or ').")";
		}
		else
		{
			$all_batch_ids_cond=" and e.id in($batch_ids)";
		}
	}

	if($report_type == 2)
	{
		$issue_return_select = " c.floor_id, c.room, c.rack, c.self,";
	}

	$issRtnSql = "SELECT c.transaction_date, d.knit_dye_source, b.body_part_id, b.prod_id,c.store_id, $issue_return_select b.fabric_description_id, b.gsm, b.width, f.color as color_id,c.cons_uom, c.cons_quantity as quantity, c.order_rate, b.batch_id, e.id as batchId, e.batch_no, e.booking_no, e.booking_without_order 
	from inv_receive_master a, pro_finish_fabric_rcv_dtls b,inv_transaction c, inv_issue_master d, pro_batch_create_mst e, product_details_master f 
	where a.id = b.mst_id and b.trans_id=c.id and c.issue_id=d.id and a.entry_form=52 and a.item_category=2 and c.pi_wo_batch_no=e.id and c.prod_id=f.id and a.status_active =1 and b.status_active=1 and c.status_active =1 and c.company_id in  ($cbo_company_id) $store_cond_2 $date_cond_2 $all_batch_ids_cond";
	$issRtnData = sql_select($issRtnSql);
	foreach ($issRtnData as $val)
	{
		// $issRtnRef_str = $val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("fabric_description_id")]."*".$val[csf("color_id")]."*".$val[csf("cons_uom")];

		$buyerId = $book_po_ref[$val[csf("booking_no")]]["buyer_name"];
		$client_id = $book_po_ref[$val[csf("booking_no")]]["client_id"];
		$pay_mode = $book_po_ref[$val[csf("booking_no")]]["pay_mode"];
		if ($client_id=="") $client_id=0;

		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($val[csf('transaction_date')]));
		$ref_str="";
		if($transaction_date >= $date_frm)
		{
			$issue_return_data[$buyerId][$client_id][$pay_mode][$val[csf("cons_uom")]]["issue_return"] += $val[csf("quantity")];
			$issue_return_data[$buyerId][$client_id][$pay_mode][$val[csf("cons_uom")]]["issue_return_amount"] += $val[csf("quantity")]*$val[csf("order_rate")];
		}
		else
		{
			$issue_return_data[$buyerId][$client_id][$pay_mode][$val[csf("cons_uom")]]["opening"] += $val[csf("quantity")];
			$issue_return_data[$buyerId][$client_id][$pay_mode][$val[csf("cons_uom")]]["opening_amount"] +=$val[csf("quantity")]*$val[csf("order_rate")];
		}
		$summary_data_arr[$buyerId][$client_id][$val[csf("cons_uom")]] += $val[csf("quantity")];
		$summary_data_arr[$buyerId][$client_id]['booking_no'] .= $val[csf("booking_no")].',';
		$summary_data_arr[$buyerId][$client_id]['prod_id'] .= $val[csf("prod_id")].',';
		$summary_data_arr[$buyerId][$client_id]['store_id'] .= $val[csf("store_id")].',';
		$summary_data_arr[$buyerId][$client_id]['rack'] .= $val[csf("rack")].',';
		$summary_data_arr[$buyerId][$client_id]['batchId'] .= $val[csf("batchId")].',';
	}
	// echo "<pre>";print_r($summary_data_arr);die;

	if($report_type == 2)
	{
		$issue_select = " c.floor_id, c.room, c.rack, c.self,";
		$issue_group = " c.floor_id, c.room, c.rack, c.self,";
	}

	$issue_sql = sql_select("SELECT a.knit_dye_source, a.issue_purpose, c.prod_id, b.body_part_id, c.cons_uom, c.store_id, $issue_select c.cons_quantity, c.id as trans_id,c.transaction_date, d.detarmination_id, d.gsm, d.dia_width, d.color, e.booking_no, e.booking_without_order, round(c.order_rate,2) as order_rate 
	from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c, product_details_master d, pro_batch_create_mst e  
	where a.id = b.mst_id and b.trans_id = c.id and c.prod_id = d.id and c.pi_wo_batch_no= e.id and c.company_id in ($cbo_company_id) $store_cond_2 $date_cond_2 $all_batch_ids_cond and a.entry_form=18 and c.status_active=1 and b.status_active=1 and a.status_active=1 and c.item_category =2 and c.transaction_type =2 
	group by a.knit_dye_source, a.issue_purpose, c.prod_id, b.body_part_id, c.cons_uom, c.store_id, $issue_group c.cons_quantity, c.id, c.transaction_date, d.detarmination_id, d.gsm, d.dia_width, d.color, e.booking_no, e.booking_without_order, round(c.order_rate,2)");
	foreach ($issue_sql as $val)
	{
		// $issRef_str="";
		// $issRef_str = $val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("color")]."*".$val[csf("cons_uom")];

		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($val[csf('transaction_date')]));

		$buyerId = $book_po_ref[$val[csf("booking_no")]]["buyer_name"];
		$client_id = $book_po_ref[$val[csf("booking_no")]]["client_id"];
		$pay_mode = $book_po_ref[$val[csf("booking_no")]]["pay_mode"];
		if ($client_id=="") $client_id=0;
		
		if($transaction_date >= $date_frm)
		{
			$issue_data[$buyerId][$client_id][$pay_mode][$val[csf("cons_uom")]]["issue_qty"] += $val[csf("cons_quantity")];
			$issue_data[$buyerId][$client_id][$pay_mode][$val[csf("cons_uom")]]["issue_amount"] += $val[csf("cons_quantity")]*$val[csf("order_rate")];
		}
		else
		{
			$issue_data[$buyerId][$client_id][$pay_mode][$val[csf("cons_uom")]]["opening_issue"] += $val[csf("cons_quantity")];
			$issue_data[$buyerId][$client_id][$pay_mode][$val[csf("cons_uom")]]["opening_issue_amount"] += $val[csf("cons_quantity")]*$val[csf("order_rate")];
		}
		$summary_issue_data_arr[$buyerId][$client_id][$val[csf("cons_uom")]] += $val[csf("cons_quantity")];
		$summary_issue_data_arr[$buyerId][$client_id]['rack'] .= $val[csf("rack")].',';
	}
	// echo "<pre>";print_r($summary_issue_data_arr);

	//, c.order_rate new add
	$rcv_return_select = " c.floor_id, c.room, c.rack, c.self,";
	$rcvRtnSql = sql_select("SELECT c.transaction_date, c.company_id, c.prod_id, c.store_id, $rcv_return_select c.cons_quantity, c.cons_uom, d.detarmination_id, d.gsm, d.dia_width, d.color, e.booking_no, e.booking_without_order, b.body_part_id, c.order_rate
	from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c, product_details_master d, pro_batch_create_mst e 
	where a.id = b.mst_id and b.trans_id=c.id and a.entry_form =46 and c.company_id in ($cbo_company_id) $store_cond_2 $date_cond_2 $all_batch_ids_cond and c.prod_id=d.id and c.pi_wo_batch_no=e.id and a.status_active =1 and b.status_active =1 and c.status_active =1");

	foreach ($rcvRtnSql as $val)
	{
		// $rcvRtn_str = $val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("color")]."*".$val[csf("cons_uom")];
		$buyerId = $book_po_ref[$val[csf("booking_no")]]["buyer_name"];
		$client_id = $book_po_ref[$val[csf("booking_no")]]["client_id"];
		$pay_mode = $book_po_ref[$val[csf("booking_no")]]["pay_mode"];
		if ($client_id=="") $client_id=0;

		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($val[csf('transaction_date')]));
		$ref_str="";
		if($transaction_date >= $date_frm)
		{
			$rcv_return_data[$buyerId][$client_id][$pay_mode][$val[csf("cons_uom")]]["qnty"] += $val[csf("cons_quantity")];
			$rcv_return_data[$buyerId][$client_id][$pay_mode][$val[csf("cons_uom")]]["amount"] += $val[csf("cons_quantity")]*$val[csf("order_rate")];
		}
		else
		{
			$rcv_return_data[$buyerId][$client_id][$pay_mode][$val[csf("cons_uom")]]["opening_qnty"] += $val[csf("cons_quantity")];
			$rcv_return_data[$buyerId][$client_id][$pay_mode][$val[csf("cons_uom")]]["opening_amount"] += $val[csf("cons_quantity")]*$val[csf("order_rate")];
		}
		$summary_issue_data_arr[$buyerId][$client_id][$val[csf("cons_uom")]] += $val[csf("cons_quantity")];
		$summary_issue_data_arr[$buyerId][$client_id]['rack'] .= $val[csf("rack")].',';
	}
	// echo "<pre>";print_r($summary_issue_data_arr);

	$trans_out_select = " c.floor_id, c.room, c.rack, c.self,";
	$transOutSql = sql_select("SELECT c.transaction_date,c.pi_wo_batch_no, e.batch_no,e.booking_no, e.booking_no_id, e.booking_without_order, c.body_part_id, c.prod_id, c.store_id, $trans_out_select d.detarmination_id, d.gsm, d.dia_width, d.color, c.cons_uom, c.cons_quantity,c.order_rate 
	from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c,product_details_master d, pro_batch_create_mst e 
	where a.id=b.mst_id and b.trans_id=c.id and c.prod_id=d.id and c.pi_wo_batch_no=e.id and c.company_id in ($cbo_company_id) $store_cond_2 $date_cond_2 $all_batch_ids_cond and c.item_category=2 and c.transaction_type=6 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.entry_form in (14,15,306)");

	foreach ($transOutSql as $val)
	{
		// $transOut_str = $val[csf("store_id")]."*".$val[csf("body_part_id")]."*".$val[csf("detarmination_id")]."*".$val[csf("color")]."*".$val[csf("cons_uom")];

		$buyerId = $book_po_ref[$val[csf("booking_no")]]["buyer_name"];
		$client_id = $book_po_ref[$val[csf("booking_no")]]["client_id"];
		$pay_mode = $book_po_ref[$val[csf("booking_no")]]["pay_mode"];
		if ($client_id=="") $client_id=0;

		$date_frm=date('Y-m-d',strtotime($start_date));
		$transaction_date=date('Y-m-d',strtotime($val[csf('transaction_date')]));
		// $ref_str="";
		if($transaction_date >= $date_frm)
		{
			$trans_out_data[$buyerId][$client_id][$pay_mode][$val[csf("cons_uom")]]["qnty"] += $val[csf("cons_quantity")];
			$trans_out_data[$buyerId][$client_id][$pay_mode][$val[csf("cons_uom")]]["amount"] += $val[csf("cons_quantity")]*$val[csf("order_rate")];
		}
		else
		{
			$trans_out_data[$buyerId][$client_id][$pay_mode][$val[csf("cons_uom")]]["opening_qnty"] += $val[csf("cons_quantity")];
			$trans_out_data[$buyerId][$client_id][$pay_mode][$val[csf("cons_uom")]]["opening_amount"] += $val[csf("cons_quantity")]*$val[csf("order_rate")];
		}
		$summary_issue_data_arr[$buyerId][$client_id][$val[csf("cons_uom")]] += $val[csf("cons_quantity")];
		$summary_issue_data_arr[$buyerId][$client_id]['rack'] .= $val[csf("rack")].',';
	}
	// echo "<pre>";print_r($summary_issue_data_arr);

    $floor_room_rack_arr = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst where status_active =1 and is_deleted=0","floor_room_rack_id","floor_room_rack_name");


	$table_width = "3190";
	$sWidth=590;
	
	ob_start();
	?>
	<style type="text/css">
		.word_break_wrap {
			word-break: break-all;
			word-wrap: break-word;
		}
		.grad1 {
			  background-image: linear-gradient(#e6e6e6, #b1b1cd, #e0e0eb);
			}
	</style>
	<fieldset style="width:<? echo $table_width+20;?>px;">
		<table cellpadding="0" cellspacing="0" width="2080">
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="23" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
			</tr>
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="23" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
			</tr>
			<tr  class="form_caption" style="border:none;">
				<td align="center" width="100%" colspan="23" style="font-size:14px"><strong> <? if($date_from!="") echo "From : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
			</tr>
		</table>

		<!-- ================ Summary Start ==========================-->    
	    <div id="summary_report_container" style="margin: 5px auto;">
		    <div style="width:<?= $sWidth+20;?>px;">
				<table width="<?= $sWidth;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="left">
					<thead>
				        <tr style="font-size:12px;">
				        	<th width="40">Sl</th>
				            <th width="80">Buyer</th>
				            <th width="80">Buyer Client</th>
				            <th width="80">KG</th>
				            <th width="80">YDS</th>
				            <th width="80">MTR</th>
				            <th width="">Rack No</th>
				        </tr>
				    </thead>
				</table>
			</div>
			<div style="width:<?= $sWidth+18;?>px; max-height:250px; overflow-y:scroll; clear:both;" id="scroll_body2">
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $sWidth;?>" class="rpt_table" align="left" id="tbl_list_dtls2">
				    <tbody>
				    	<?
				        $i=1;
				        $summary_tot_kg_qnty=$summary_tot_yds_qty=$summary_tot_mtr_qty=0;
						foreach ($summary_data_arr as $buyer => $buyer_data) 
						{
							foreach ($buyer_data as $client => $rows) 
							{
								$rack_arr=array_filter(array_unique(explode(",", $rows['rack'])));
								foreach ($rack_arr as $key => $rack_id) 
								{
									$rack.=$floor_room_rack_arr[$rack_id].',';
								}

								$booking_no=chop(implode(",", array_unique(explode(",", $rows['booking_no']))),",");
								$prod_ids=chop(implode(",", array_unique(explode(",", $rows['prod_id']))),",");
								$store_ids=chop(implode(",", array_unique(explode(",", $rows['store_id']))),",");
								$rack_ids=chop(implode(",", array_unique(explode(",", $rows['rack']))),",");
								$batchIds=chop(implode(",", array_unique(explode(",", $rows['batchId']))),",");
								// echo $rows[12].'='.$summary_issue_data_arr[$buyer][$client][12];
								$kg_stock=$rows[12]-$summary_issue_data_arr[$buyer][$client][12];
								$yds_stock=$rows[27]-$summary_issue_data_arr[$buyer][$client][27];
								$mtr_stock=$rows[23]-$summary_issue_data_arr[$buyer][$client][23];
								$summary_stock_qty=$kg_stock+$yds_stock+$mtr_stock;
								if(number_format($summary_stock_qty,2,".","") == "-0.00" || number_format($summary_stock_qty,2,".","") < "0.00")
								{
									// $summary_stock_qty=max($summary_stock_qty, 0);
									$summary_stock_qty=0;
								}
								if($summary_stock_qty!=0)
								{
									$bgcolor = ($i%2==0) ? "#E9F3FF" : "#FFFFFF";
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" id="tr1_<?= $i; ?>" onClick="change_color('tr1_<?= $i; ?>','<?= $bgcolor; ?>')" style="cursor:pointer;">
										<td width="40" align="center"><?= $i;?></td>
						                <td width="80" align="center" title="<? echo $buyer;?>"><p><?= $buyer_arr[$buyer];?></p></td>
						                <td width="80" align="center" title="<? echo $client;?>"><p><?= $buyer_arr[$client];?></p></td>

						                <td width="80" align="right" title="<? echo $rows[12].'-'.$summary_issue_data_arr[$buyer][$client][12]; ?>"><p><a href="##" onclick="openmypage('<? echo $cbo_company_id;?>','<? echo $cbo_store_name;?>','<? echo $buyer_id;?>','<? echo $job_no;?>','<? echo $job_year;?>','<? echo $date_from;?>','<? echo $date_to;?>','<? echo $buyer;?>','<? echo $client;?>','12','stock_in_kg_yds_mtr_pupup');"><? echo number_format($kg_stock,2,".","");?></a></p></td>

						                <td width="80" align="right"><p><a href="##" onclick="openmypage('<? echo $cbo_company_id;?>','<? echo $cbo_store_name;?>','<? echo $buyer_id;?>','<? echo $job_no;?>','<? echo $job_year;?>','<? echo $date_from;?>','<? echo $date_to;?>','<? echo $buyer;?>','<? echo $client;?>','27','stock_in_kg_yds_mtr_pupup');"><? echo number_format($yds_stock,2,".","");?></a></p></td>

						                <td width="80" align="right"><p><a href="##" onclick="openmypage('<? echo $cbo_company_id;?>','<? echo $cbo_store_name;?>','<? echo $buyer_id;?>','<? echo $job_no;?>','<? echo $job_year;?>','<? echo $date_from;?>','<? echo $date_to;?>','<? echo $buyer;?>','<? echo $client;?>','23','stock_in_kg_yds_mtr_pupup');"><? echo number_format($mtr_stock,2,".","");?></a></p></td>

						                <td width="" align="center"><p><?= chop($rack,",");?></p></td>
						            </tr>
									<?
									$summary_tot_kg_qnty+=$kg_stock;
									$summary_tot_yds_qty+=$yds_stock;
									$summary_tot_mtr_qty+=$mtr_stock;
								    $i++;
								}
							}
						}
		       	 		?>       	 		
				    </tbody>
				</table>
				<table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?= $sWidth;?>" class="rpt_table" id="report_table_footer">
					<tfoot>
						<th width="40"></th>
						<th width="80"></th>
			            <th width="80" align="right">Total:</th>
			            <th width="80"><strong><?= number_format($summary_tot_kg_qnty,2,'.',''); ?></strong></th>
			            <th width="80"><strong><?= number_format($summary_tot_yds_qty,2,'.',''); ?></strong></th>
			            <th width="80"><strong><?= number_format($summary_tot_mtr_qty,2,'.',''); ?></strong></th>
			            <th width=""></strong></th>
					</tfoot>
				</table>
			</div>
		</div>
		<br clear="all">		
		<!-- ============ Summary End ===============-->

		<!-- ============ Details Start ===============-->
		<table width="<? echo $table_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">
			<thead>
				<tr>
					<th rowspan="3" width="30">SL</th>
					<th rowspan="3" width="100">Buyer</th>
					<th rowspan="3" width="100">Buyer Client</th>
					<th rowspan="3" width="100">Paymode</th>

					<th colspan="8">KG</th>
					<th colspan="8">Yds</th>
					<th colspan="8">Mtr</th>

					<th width="100">Opening</th>
					<th width="100">Receive</th>
					<th width="100">Issue</th>
					<th width="">Closing</th>
				</tr>
				<tr>
					<th colspan="2">OPENING</th>
					<th colspan="2">RECEIVE</th>
					<th colspan="2">ISSUE</th>
					<th colspan="2">CLOSING</th>

					<th colspan="2">OPENING</th>
					<th colspan="2">RECEIVE</th>
					<th colspan="2">ISSUE</th>
					<th colspan="2">CLOSING</th>

					<th colspan="2">OPENING</th>
					<th colspan="2">RECEIVE</th>
					<th colspan="2">ISSUE</th>
					<th colspan="2">CLOSING</th>

					<th rowspan="2" width="100">Grand Total Value$</th>
					<th rowspan="2" width="100">Grand Total Value$</th>
					<th rowspan="2" width="100">Grand Total Value$</th>
					<th rowspan="2" width="">Grand Total Value$</th>
				</tr>
				<tr>
					<th width="100">QTY</th>
					<th width="100">VALUE</th>
					<th width="100">QTY</th>
					<th width="100">VALUE</th>
					<th width="100">QTY</th>
					<th width="100">VALUE</th>			
					<th width="100">QTY</th>
					<th width="100">VALUE</th>

					<th width="100">QTY</th>
					<th width="100">VALUE</th>
					<th width="100">QTY</th>
					<th width="100">VALUE</th>
					<th width="100">QTY</th>
					<th width="100">VALUE</th>			
					<th width="100">QTY</th>
					<th width="100">VALUE</th>

					<th width="100">QTY</th>
					<th width="100">VALUE</th>
					<th width="100">QTY</th>
					<th width="100">VALUE</th>
					<th width="100">QTY</th>
					<th width="100">VALUE</th>			
					<th width="100">QTY</th>
					<th width="100">VALUE</th>
				</tr>	
			</thead>
		</table>
		<div style="width:<? echo $table_width+20;?>px; max-height:350px; overflow-y:scroll;" id="scroll_body">
			<table width="<? echo $table_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" >
				<?
				// echo "<pre>"; print_r($data_array); echo "</pre>";
				$i=1;
				$uom_total_opening_qnty_kg=$uom_total_opening_amount_kg=$uom_total_receive_kg=$uom_total_receive_amount_kg=$uom_total_issue_kg=$uom_total_issue_amount_kg=$uom_total_stock_qnty_kg=$uom_total_stock_amount_kg=$uom_total_opening_qnty_yds=$uom_total_opening_amount_yds=$uom_total_receive_yds=$uom_total_receive_amount_yds=$uom_total_issue_yds=$uom_total_issue_amount_yds=$uom_total_stock_qnty_yds=$uom_total_stock_amount_yds=$uom_total_opening_qnty_mtr=$uom_total_opening_amount_mtr=$uom_total_receive_mtr=$uom_total_receive_amount_mtr=$uom_total_issue_mtr=$uom_total_issue_amount_mtr=$uom_total_stock_qnty_mtr=$uom_total_stock_amount_mtr=$uom_total_opening_grand_total_value=$uom_total_receive_grand_total_value=$uom_total_issue_grand_total_value=$uom_total_closing_grand_total_value=0;
				foreach ($data_array as $buyer => $buyer_data)
				{			
					foreach ($buyer_data as $client => $client_data)
					{
						foreach ($client_data as $paymode => $row)
						{
							$trans_in_qty_kg=$trans_in_qty_mtr=$trans_in_qty_yds=$opening_trans_kg=$opening_trans_mtr=$opening_trans_yds=$recv_qnty_kg=$recv_qnty_mtr=$recv_qnty_yds=$opening_recv_kg=$opening_recv_mtr=$opening_recv_yds=0;
							$opening_amount_kg=$tot_receive_amount_kg=$total_issue_amount_kg=$stock_amount_kg=$opening_amount_yds=$tot_receive_amount_yds=$total_issue_amount_yds=$stock_amount_yds=$opening_amount_mtr=$tot_receive_amount_mtr=$total_issue_amount_mtr=$stock_amount_mtr=0;

							$ref_qnty_arr_kg = explode("__", $row[12]); // kg
							$ref_qnty_arr_yds = explode("__", $row[27]); // yds
							$ref_qnty_arr_mtr = explode("__", $row[23]); // mtr

							foreach ($ref_qnty_arr_kg as $ref_qnty)
							{
								$ref_qnty = explode("*", $ref_qnty);

								if($ref_qnty[6] == 1)
								{
									if($ref_qnty[7]==1)
									{
										$recv_qnty_kg += $ref_qnty[0];
										$recv_amount_kg += $ref_qnty[0]*$ref_qnty[1];
									}
									else
									{
										$opening_recv_kg +=$ref_qnty[0];
										$opening_recv_amount_kg +=$ref_qnty[0]*$ref_qnty[1];
									}
								}
								if($ref_qnty[6] == 5)
								{
									if($ref_qnty[7]==1)
									{
										$trans_in_qty_kg += $ref_qnty[0];
										$trans_in_amount_kg += $ref_qnty[0]*$ref_qnty[1];
									}
									else
									{
										$opening_trans_kg +=$ref_qnty[0];
										$opening_trans_amount_kg +=$ref_qnty[0]*$ref_qnty[1];
									}
								}
							}
							// echo $recv_qnty_kg+$trans_in_qty_kg.'==<br>';
							foreach ($ref_qnty_arr_yds as $ref_qnty)
							{
								$ref_qnty = explode("*", $ref_qnty);

								if($ref_qnty[6] == 1)
								{
									if($ref_qnty[7]==1)
									{
										$recv_qnty_yds += $ref_qnty[0];
										$recv_amount_yds += $ref_qnty[0]*$ref_qnty[1];
									}
									else
									{
										$opening_recv_yds +=$ref_qnty[0];
										$opening_recv_amount_yds +=$ref_qnty[0]*$ref_qnty[1];
									}
								}
								if($ref_qnty[6] == 5)
								{
									if($ref_qnty[7]==1)
									{
										$trans_in_qty_yds += $ref_qnty[0];
										$trans_in_amount_yds += $ref_qnty[0]*$ref_qnty[1];
									}
									else
									{
										$opening_trans_yds +=$ref_qnty[0];
										$opening_trans_amount_yds +=$ref_qnty[0]*$ref_qnty[1];
									}
								}
							}

							foreach ($ref_qnty_arr_mtr as $ref_qnty)
							{
								$ref_qnty = explode("*", $ref_qnty);

								if($ref_qnty[6] == 1)
								{
									if($ref_qnty[7]==1)
									{
										$recv_qnty_mtr += $ref_qnty[0];
										$recv_amount_mtr += $ref_qnty[0]*$ref_qnty[1];
									}
									else
									{
										$opening_recv_mtr +=$ref_qnty[0];
										$opening_recv_amount_mtr +=$ref_qnty[0]*$ref_qnty[1];
									}
								}
								if($ref_qnty[6] == 5)
								{
									if($ref_qnty[7]==1)
									{
										$trans_in_qty_mtr += $ref_qnty[0];
										$trans_in_amount_mtr += $ref_qnty[0]*$ref_qnty[1];
									}
									else
									{
										$opening_trans_mtr +=$ref_qnty[0];
										$opening_trans_amount_mtr +=$ref_qnty[0]*$ref_qnty[1];
									}
								}
							}

							$issue_return_kg 		= $issue_return_data[$buyer][$client][$paymode][12]["issue_return"];
							$issue_return_yds 		= $issue_return_data[$buyer][$client][$paymode][27]["issue_return"];
							$issue_return_mtr 		= $issue_return_data[$buyer][$client][$paymode][23]["issue_return"];
							// $inside_return_amount 	= $issue_return_data[$booking_no][$issRtnRef_str]["issue_return_amount"];
							// $outside_return 		= $issue_return_data[$booking_no][$issRtnRef_str]["outside_return"];
							// $outside_return_amount  = $issue_return_data[$booking_no][$issRtnRef_str]["outside_return_amount"];
							$opening_iss_return_kg 	= $issue_return_data[$buyer][$client][$paymode][12]["opening"];
							$opening_iss_return_yds = $issue_return_data[$buyer][$client][$paymode][27]["opening"];
							$opening_iss_return_mtr = $issue_return_data[$buyer][$client][$paymode][23]["opening"];
							// $opening_iss_return_amount = $issue_return_data[$booking_no][$issRtnRef_str]["opening_amount"];

							$tot_receive_kg 		= $recv_qnty_kg + $trans_in_qty_kg + $issue_return_kg;
							$tot_receive_yds 		= $recv_qnty_yds+$trans_in_qty_yds + $issue_return_yds;
							$tot_receive_mtr 		= $recv_qnty_mtr+$trans_in_qty_mtr + $issue_return_mtr;


							$tot_receive 			= $tot_receive_kg + $tot_receive_yds + $tot_receive_mtr;
							// $tot_receive 			= $recv_qnty + $trans_in_qty + $inside_return + $outside_return;
							

							$issue_qty_kg 		= $issue_data[$buyer][$client][$paymode][12]["issue_qty"];
							$issue_qty_yds 		= $issue_data[$buyer][$client][$paymode][27]["issue_qty"];
							$issue_qty_mtr 		= $issue_data[$buyer][$client][$paymode][23]["issue_qty"];
							// $issue_amount 	= $issue_data[$booking_no][$issRtnRef_str]["issue_amount"];
							$opening_issue_kg 	= $issue_data[$buyer][$client][$paymode][12]["opening_issue"];
							$opening_issue_yds	= $issue_data[$buyer][$client][$paymode][27]["opening_issue"];
							$opening_issue_mtr 	= $issue_data[$buyer][$client][$paymode][23]["opening_issue"];
							// $opening_issue_amount 	= $issue_data[$booking_no][$issRtnRef_str]["opening_issue_amount"];

							
							$rcv_return_qnty_kg  	= $rcv_return_data[$buyer][$client][$paymode][12]["qnty"];
							$rcv_return_qnty_yds  	= $rcv_return_data[$buyer][$client][$paymode][27]["qnty"];
							$rcv_return_qnty_mtr  	= $rcv_return_data[$buyer][$client][$paymode][23]["qnty"];
							// $rcv_return_amount  	= $rcv_return_data[$booking_no][$issRtnRef_str]["amount"];
							$rcv_return_opening_qnty_kg = $rcv_return_data[$buyer][$client][$paymode][12]["opening_qnty"];
							$rcv_return_opening_qnty_yds = $rcv_return_data[$buyer][$client][$paymode][27]["opening_qnty"];
							$rcv_return_opening_qnty_mtr = $rcv_return_data[$buyer][$client][$paymode][23]["opening_qnty"];
							// $rcv_return_opening_amount = $rcv_return_data[$booking_no][$issRtnRef_str]["opening_amount"];

							// $trans_out_amount  	= $trans_out_data[$booking_no][$issRtnRef_str]["amount"];
							$trans_out_qnty_kg  	= $trans_out_data[$buyer][$client][$paymode][12]["qnty"];
							$trans_out_qnty_yds  	= $trans_out_data[$buyer][$client][$paymode][27]["qnty"];
							$trans_out_qnty_mtr  	= $trans_out_data[$buyer][$client][$paymode][23]["qnty"];

							$trans_out_opening_qnty_kg = $trans_out_data[$buyer][$client][$paymode][12]["opening_qnty"];
							$trans_out_opening_qnty_yds = $trans_out_data[$buyer][$client][$paymode][27]["opening_qnty"];
							$trans_out_opening_qnty_mtr = $trans_out_data[$buyer][$client][$paymode][23]["opening_qnty"];
							// $trans_out_opening_amount= $trans_out_data[$booking_no][$issRtnRef_str]["opening_amount"];
							
							$tot_issue_kg 	= $issue_qty_kg + $rcv_return_qnty_kg+$trans_out_qnty_kg;
							$tot_issue_yds 	= $issue_qty_yds + $rcv_return_qnty_yds+$trans_out_qnty_yds;
							$tot_issue_mtr 	=  $issue_qty_mtr + $rcv_return_qnty_mtr+$trans_out_qnty_mtr;

							$total_issue  	= $tot_issue_kg + $tot_issue_yds + $tot_issue_mtr;
							// $total_issue 	= $cutting_inside + $cutting_outside + $other_issue + $rcv_return_qnty + $trans_out_qnty;							

							$opening_recv_qnty_kg 	= $opening_recv_kg+$opening_trans_kg;
							$opening_recv_qnty_yds 	= $opening_recv_yds+$opening_trans_yds;
							$opening_recv_qnty_mtr 	= $opening_recv_mtr+$opening_trans_mtr;
							// echo $opening_recv_yds.'<br>';
							$opening_qnty_kg 	= ($opening_recv_qnty_kg + $opening_iss_return_kg) - ($opening_issue_kg + $rcv_return_opening_qnty_kg +$trans_out_opening_qnty_kg);
							$opening_qnty_yds 	= ($opening_recv_qnty_yds + $opening_iss_return_yds) - ($opening_issue_yds + $rcv_return_opening_qnty_yds +$trans_out_opening_qnty_yds);
							$opening_qnty_mtr 	= ($opening_recv_qnty_mtr + $opening_iss_return_mtr) - ($opening_issue_mtr + $rcv_return_opening_qnty_mtr +$trans_out_opening_qnty_mtr);
							// echo $opening_qnty_yds.'==<br>';
							// echo $opening_qnty_kg.'==<br>';

							$stock_qnty_kg 		= $opening_qnty_kg + ($tot_receive_kg - $tot_issue_kg);
							$stock_qnty_yds 	= $opening_qnty_yds + ($tot_receive_yds - $tot_issue_yds);
							$stock_qnty_mtr 	= $opening_qnty_mtr + ($tot_receive_mtr - $tot_issue_mtr);


							// $opening_title 	= "Receive:".$opening_recv ." + Transfer In:". $opening_trans ." + Issue Return:" . $opening_iss_return . "\n";
							// $opening_title 	.= "Issue:".$opening_issue ." + Transfer Out:". $trans_out_opening_qnty ." + Receive Return:" . $rcv_return_opening_qnty;
							$opening_qnty 	= $opening_qnty_kg+$opening_qnty_mtr+$opening_qnty_yds;
							// $opening_qnty 	= ($opening_recv + $opening_trans + $opening_iss_return) - ($opening_issue + $rcv_return_opening_qnty +$trans_out_opening_qnty);
							$stock_qnty 	= $opening_qnty + ($tot_receive - $total_issue);
							// $stock_title 	= "Opening:".$opening_qnty ." + (Receive:". $tot_receive ."- Issue:". $total_issue.")";
							// ==================================================
							$tot_receive_rate_kg=$tot_receive_rate_yds=$tot_receive_rate_mtr=0;					
							$booking_and_product_wise_quantity_kg = $rate_arr_booking_and_product_wise[$buyer][$client][$paymode][12]["quantity"];
							$booking_and_product_wise_quantity_yds = $rate_arr_booking_and_product_wise[$buyer][$client][$paymode][27]["quantity"];
							$booking_and_product_wise_quantity_mtr = $rate_arr_booking_and_product_wise[$buyer][$client][$paymode][23]["quantity"];
							// ==================================================
							$booking_and_product_wise_amount_kg = $rate_arr_booking_and_product_wise[$buyer][$client][$paymode][12]["amount"];
							$booking_and_product_wise_amount_yds = $rate_arr_booking_and_product_wise[$buyer][$client][$paymode][27]["amount"];
							$booking_and_product_wise_amount_mtr = $rate_arr_booking_and_product_wise[$buyer][$client][$paymode][23]["amount"];
							// ==================================================
							if($booking_and_product_wise_amount_kg>0 && $booking_and_product_wise_quantity_kg>0)
							{
								// echo $booking_and_product_wise_amount_kg.'/'.$booking_and_product_wise_quantity_kg.'<br>';
								$booking_and_product_wise_rate_kg = $booking_and_product_wise_amount_kg/$booking_and_product_wise_quantity_kg;
							}
							else
							{
								$booking_and_product_wise_rate_kg = 0;
							}
							$tot_receive_rate_kg =$booking_and_product_wise_rate_kg;
							if($tot_receive_kg>0)
							{
								$tot_receive_amount_kg 	= $tot_receive_rate_kg*$tot_receive_kg;
								// echo $tot_receive_rate_kg.'*'.$tot_receive_kg;
							}
							
							$opening_rate_kg=$opening_amount_kg=0;
							if($opening_qnty_kg>0)
							{
								$opening_rate_kg = $tot_receive_rate_kg;
								$opening_amount_kg = $opening_rate_kg*$opening_qnty_kg;
							}

							$tot_issue_rate_kg = $tot_receive_rate_kg;
							$total_issue_amount_kg = $tot_issue_kg * $tot_issue_rate_kg;

							$stock_rate_kg = $tot_receive_rate_kg;
							$stock_amount_kg = $stock_qnty_kg * $stock_rate_kg;
							// ==================================================
							if($booking_and_product_wise_amount_yds>0 && $booking_and_product_wise_quantity_yds>0)
							{
								$booking_and_product_wise_rate_yds = $booking_and_product_wise_amount_yds/$booking_and_product_wise_quantity_yds;
							}
							else
							{
								$booking_and_product_wise_rate_yds = 0;
							}
							$tot_receive_rate_yds =$booking_and_product_wise_rate_yds;
							if($tot_receive_yds>0)
							{
								$tot_receive_amount_yds 	= $tot_receive_rate_yds*$tot_receive_yds;
							}
							
							$opening_rate_yds=$opening_amount_yds=0;
							if($opening_qnty_yds>0)
							{
								$opening_rate_yds = $tot_receive_rate_yds;
								$opening_amount_yds = $opening_rate_yds*$opening_qnty_yds;
							}

							$tot_issue_rate_yds = $tot_receive_rate_yds;
							$total_issue_amount_yds = $tot_issue_yds * $tot_issue_rate_yds;

							$stock_rate_yds = $tot_receive_rate_yds;
							$stock_amount_yds = $stock_qnty_yds * $stock_rate_yds;
							// ==================================================
							if($booking_and_product_wise_amount_mtr>0 && $booking_and_product_wise_quantity_mtr>0)
							{
								$booking_and_product_wise_rate_mtr = $booking_and_product_wise_amount_mtr/$booking_and_product_wise_quantity_mtr;
							}
							else
							{
								$booking_and_product_wise_rate_mtr = 0;
							}
							$tot_receive_rate_mtr =$booking_and_product_wise_rate_mtr;
							if($tot_receive_mtr>0)
							{
								$tot_receive_amount_mtr 	= $tot_receive_rate_mtr*$tot_receive_mtr;
							}
							
							$opening_rate_mtr=$opening_amount_mtr=0;
							if($opening_qnty_mtr>0)
							{
								$opening_rate_mtr = $tot_receive_rate_mtr;
								$opening_amount_mtr = $opening_rate_mtr*$opening_qnty_mtr;
							}

							$tot_issue_rate_mtr = $tot_receive_rate_mtr;
							$total_issue_amount_mtr = $tot_issue_mtr * $tot_issue_rate_mtr;

							$stock_rate_mtr = $tot_receive_rate_mtr;
							$stock_amount_mtr = $stock_qnty_mtr * $stock_rate_mtr;
							// ==================================================

							/*$booking_and_product_wise_quantity = $rate_arr_booking_and_product_wise[$booking_no][$issRtnRef_str]["quantity"];
							$booking_and_product_wise_amount = $rate_arr_booking_and_product_wise[$booking_no][$issRtnRef_str]["amount"];
							if($booking_and_product_wise_amount>0 && $booking_and_product_wise_quantity>0)
							{
								$booking_and_product_wise_rate = $booking_and_product_wise_amount/$booking_and_product_wise_quantity;
							}
							else
							{
								$booking_and_product_wise_rate = 0;
							}
							$tot_receive_rate =$booking_and_product_wise_rate;


							if($tot_receive>0)
							{
								$tot_receive_amount 	= $tot_receive_rate*$tot_receive;
							}
							
							$opening_rate=$opening_amount=0;
							if($opening_qnty>0)
							{
								$opening_rate = $tot_receive_rate;
								$opening_amount = $opening_rate*$opening_qnty;
							}

							$tot_issue_rate = $tot_receive_rate;
							$total_issue_amount = $total_issue * $tot_issue_rate;

							$stock_rate = $tot_receive_rate;
							$stock_amount = $stock_qnty * $stock_rate;*/

							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							if(number_format($stock_qnty,2,".","") == "-0.00")
							{
								$stock_qnty=0;
							}
							if($stock_qnty!=0 && $cbo_value_with==2)
							{
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
									<td width="30"><? echo $i;?></td>
									<td width="100"><? echo $buyer_arr[$buyer];?></td>
									<td width="100"><? echo $buyer_arr[$client];?></td>
									<td width="100"><? echo $paymode;?></td>

									<td width="100" align="right" title="<? echo $opening_title;?>"><? echo number_format($opening_qnty_kg,2,".","");?></td>
									<td width="100" align="right" ><? echo number_format($opening_amount_kg,2,".",""); ?></td>
									<td width="100" align="right" title="<? echo $tot_receive_kg;?>"><? echo number_format($tot_receive_kg,2,".",""); ?></td>
									<td width="100" align="right" ><? echo number_format($tot_receive_amount_kg,2,".",""); ?></td>
									<td width="100" align="right"><? echo number_format($tot_issue_kg,2,".",""); ?></td>
									<td width="100" align="right"><? echo number_format($total_issue_amount_kg,2,".","");?></td>
									<td width="100" align="right" title="<? echo $stock_title;?>"><? echo number_format($stock_qnty_kg,2,".","");?></td>
									<td width="100" align="right"><? echo number_format($stock_amount_kg,2,".","");?></td>

									<td width="100" align="right" title="<? echo $opening_title;?>"><? echo number_format($opening_qnty_yds,2,".","");?></td>
									<td width="100" align="right" ><? echo number_format($opening_amount_yds,2,".",""); ?></td>
									<td width="100" align="right"><? echo number_format($tot_receive_yds,2,".",""); ?></td>
									<td width="100" align="right" ><? echo number_format($tot_receive_amount_yds,2,".",""); ?></td>
									<td width="100" align="right"><? echo number_format($tot_issue_yds,2,".",""); ?></td>
									<td width="100" align="right"><? echo number_format($total_issue_amount_yds,2,".","");?></td>
									<td width="100" align="right" title="<? echo $stock_title;?>"><? echo number_format($stock_qnty_yds,2,".","");?></td>
									<td width="100" align="right"><? echo number_format($stock_amount_yds,2,".","");?></td>
									
									<td width="100" align="right" title="<? echo $opening_title;?>"><? echo number_format($opening_qnty_mtr,2,".","");?></td>
									<td width="100" align="right" ><? echo number_format($opening_amount_mtr,2,".",""); ?></td>
									<td width="100" align="right"><? echo number_format($tot_receive_mtr,2,".",""); ?></td>
									<td width="100" align="right" ><? echo number_format($tot_receive_amount_mtr,2,".",""); ?></td>
									<td width="100" align="right"><? echo number_format($tot_issue_mtr,2,".",""); ?></td>
									<td width="100" align="right"><? echo number_format($total_issue_amount_mtr,2,".","");?></td>
									<td width="100" align="right" title="<? echo $stock_title;?>"><? echo number_format($stock_qnty_mtr,2,".","");?></td>
									<td width="100" align="right"><? echo number_format($stock_amount_mtr,2,".","");?></td>

									<td width="100" align="right"><? echo number_format($opening_amount_kg+$opening_amount_yds+$opening_amount_mtr,2,".",""); ?></td>
									<td width="100" align="right"><? echo number_format($tot_receive_amount_kg+$tot_receive_amount_yds+$tot_receive_amount_mtr,2,".",""); ?></td>
									<td width="100" align="right"><? echo number_format($total_issue_amount_kg+$total_issue_amount_yds+$total_issue_amount_mtr,2,".","");?></td>
									<td width="" align="right"><? echo number_format($stock_amount_kg+$stock_amount_yds+$stock_amount_mtr,2,".","");?></td>
								</tr>
								<?
								$i++;
								$uom_total_opening_qnty_kg+=$opening_qnty_kg;
								$uom_total_opening_amount_kg+=$opening_amount_kg;
								$uom_total_receive_kg+=$tot_receive_kg;
								$uom_total_receive_amount_kg+=$tot_receive_amount_kg;
								$uom_total_issue_kg+=$tot_issue_kg;
								$uom_total_issue_amount_kg+=$total_issue_amount_kg;
								$uom_total_stock_qnty_kg+=$stock_qnty_kg;
								$uom_total_stock_amount_kg+=$stock_amount_kg;

								$uom_total_opening_qnty_yds+=$opening_qnty_yds;
								$uom_total_opening_amount_yds+=$opening_amount_yds;
								$uom_total_receive_yds+=$tot_receive_yds;
								$uom_total_receive_amount_yds+=$tot_receive_amount_yds;
								$uom_total_issue_yds+=$tot_issue_yds;
								$uom_total_issue_amount_yds+=$total_issue_amount_yds;
								$uom_total_stock_qnty_yds+=$stock_qnty_yds;
								$uom_total_stock_amount_yds+=$stock_amount_yds;

								$uom_total_opening_qnty_mtr+=$opening_qnty_mtr;
								$uom_total_opening_amount_mtr+=$opening_amount_mtr;
								$uom_total_receive_mtr+=$tot_receive_mtr;
								$uom_total_receive_amount_mtr+=$tot_receive_amount_mtr;
								$uom_total_issue_mtr+=$tot_issue_mtr;
								$uom_total_issue_amount_mtr+=$total_issue_amount_mtr;
								$uom_total_stock_qnty_mtr+=$stock_qnty_mtr;
								$uom_total_stock_amount_mtr+=$stock_amount_mtr;

								$uom_total_opening_grand_total_value+=$opening_amount_kg+$opening_amount_yds+$opening_amount_mtr;
								$uom_total_receive_grand_total_value+=$tot_receive_amount_kg+$tot_receive_amount_yds+$tot_receive_amount_mtr;
								$uom_total_issue_grand_total_value+=$total_issue_amount_kg+$total_issue_amount_yds+$total_issue_amount_mtr;
								$uom_total_closing_grand_total_value+=$stock_amount_kg+$stock_amount_yds+$stock_amount_mtr;
							}
							// else if($cbo_value_with==1)
							else if(($cbo_value_with==1) && (number_format($opening_qnty,2,".","")!='0.00' ||number_format($tot_receive,2,".","")!='0.00' || number_format($total_issue,2,".","")!='0.00'))
								// ($cbo_value_with==1) && ($opening_qnty!=0 || $tot_receive!=0 || $total_issue!=0)
							{
								// echo number_format($opening_qnty,2,".","").'='.number_format($tot_receive,2,".","").'='.number_format($total_issue,2,".","").'<br>';
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
									<td width="30"><? echo $i;?></td>
									<td width="100"><? echo $buyer_arr[$buyer];?></td>
									<td width="100"><? echo $buyer_arr[$client];?></td>
									<td width="100"><? echo $paymode;?></td>

									<td width="100" align="right" title="<? echo $opening_title;?>"><? echo number_format($opening_qnty_kg,2,".","");?></td>
									<td width="100" align="right" ><? echo number_format($opening_amount_kg,2,".",""); ?></td>
									<td width="100" align="right" title="<? echo $tot_receive_kg;?>"><? echo number_format($tot_receive_kg,2,".",""); ?></td>
									<td width="100" align="right" ><? echo number_format($tot_receive_amount_kg,2,".",""); ?></td>
									<td width="100" align="right"><? echo number_format($tot_issue_kg,2,".",""); ?></td>
									<td width="100" align="right"><? echo number_format($total_issue_amount_kg,2,".","");?></td>
									<td width="100" align="right" title="<? echo $stock_title;?>"><? echo number_format($stock_qnty_kg,2,".","");?></td>
									<td width="100" align="right"><? echo number_format($stock_amount_kg,2,".","");?></td>

									<td width="100" align="right" title="<? echo $opening_title;?>"><? echo number_format($opening_qnty_yds,2,".","");?></td>
									<td width="100" align="right" ><? echo number_format($opening_amount_yds,2,".",""); ?></td>
									<td width="100" align="right"><? echo number_format($tot_receive_yds,2,".",""); ?></td>
									<td width="100" align="right" ><? echo number_format($tot_receive_amount_yds,2,".",""); ?></td>
									<td width="100" align="right"><? echo number_format($tot_issue_yds,2,".",""); ?></td>
									<td width="100" align="right"><? echo number_format($total_issue_amount_yds,2,".","");?></td>
									<td width="100" align="right" title="<? echo $stock_title;?>"><? echo number_format($stock_qnty_yds,2,".","");?></td>
									<td width="100" align="right"><? echo number_format($stock_amount_yds,2,".","");?></td>
									
									<td width="100" align="right" title="<? echo $opening_title;?>"><? echo number_format($opening_qnty_mtr,2,".","");?></td>
									<td width="100" align="right" ><? echo number_format($opening_amount_mtr,2,".",""); ?></td>
									<td width="100" align="right"><? echo number_format($tot_receive_mtr,2,".",""); ?></td>
									<td width="100" align="right" ><? echo number_format($tot_receive_amount_mtr,2,".",""); ?></td>
									<td width="100" align="right"><? echo number_format($tot_issue_mtr,2,".",""); ?></td>
									<td width="100" align="right"><? echo number_format($total_issue_amount_mtr,2,".","");?></td>
									<td width="100" align="right" title="<? echo $stock_title;?>"><? echo number_format($stock_qnty_mtr,2,".","");?></td>
									<td width="100" align="right"><? echo number_format($stock_amount_mtr,2,".","");?></td>

									<td width="100" align="right"><? echo number_format($opening_amount_kg+$opening_amount_yds+$opening_amount_mtr,2,".",""); ?></td>
									<td width="100" align="right"><? echo number_format($tot_receive_amount_kg+$tot_receive_amount_yds+$tot_receive_amount_mtr,2,".",""); ?></td>
									<td width="100" align="right"><? echo number_format($total_issue_amount_kg+$total_issue_amount_yds+$total_issue_amount_mtr,2,".","");?></td>
									<td width="" align="right"><? echo number_format($stock_amount_kg+$stock_amount_yds+$stock_amount_mtr,2,".","");?></td>
								</tr>
								<?
								$i++;
								$uom_total_opening_qnty_kg+=$opening_qnty_kg;
								$uom_total_opening_amount_kg+=$opening_amount_kg;
								$uom_total_receive_kg+=$tot_receive_kg;
								$uom_total_receive_amount_kg+=$tot_receive_amount_kg;
								$uom_total_issue_kg+=$tot_issue_kg;
								$uom_total_issue_amount_kg+=$total_issue_amount_kg;
								$uom_total_stock_qnty_kg+=$stock_qnty_kg;
								$uom_total_stock_amount_kg+=$stock_amount_kg;

								$uom_total_opening_qnty_yds+=$opening_qnty_yds;
								$uom_total_opening_amount_yds+=$opening_amount_yds;
								$uom_total_receive_yds+=$tot_receive_yds;
								$uom_total_receive_amount_yds+=$tot_receive_amount_yds;
								$uom_total_issue_yds+=$tot_issue_yds;
								$uom_total_issue_amount_yds+=$total_issue_amount_yds;
								$uom_total_stock_qnty_yds+=$stock_qnty_yds;
								$uom_total_stock_amount_yds+=$stock_amount_yds;

								$uom_total_opening_qnty_mtr+=$opening_qnty_mtr;
								$uom_total_opening_amount_mtr+=$opening_amount_mtr;
								$uom_total_receive_mtr+=$tot_receive_mtr;
								$uom_total_receive_amount_mtr+=$tot_receive_amount_mtr;
								$uom_total_issue_mtr+=$tot_issue_mtr;
								$uom_total_issue_amount_mtr+=$total_issue_amount_mtr;
								$uom_total_stock_qnty_mtr+=$stock_qnty_mtr;
								$uom_total_stock_amount_mtr+=$stock_amount_mtr;

								$uom_total_opening_grand_total_value+=$opening_amount_kg+$opening_amount_yds+$opening_amount_mtr;
								$uom_total_receive_grand_total_value+=$tot_receive_amount_kg+$tot_receive_amount_yds+$tot_receive_amount_mtr;
								$uom_total_issue_grand_total_value+=$total_issue_amount_kg+$total_issue_amount_yds+$total_issue_amount_mtr;
								$uom_total_closing_grand_total_value+=$stock_amount_kg+$stock_amount_yds+$stock_amount_mtr;
							}
						}
					}
				}
				?>
			</table>
		</div>
		<table width="<? echo $table_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" >
			<tfoot>
				<th width="30">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="100">&nbsp;</th>
				<th width="100">&nbsp;</th>

				<th width="100" id="value_opening_qnty_kg"><? echo $uom_total_opening_qnty_kg; ?></th>
				<th width="100" id="value_opening_amount_kg"><? echo $uom_total_opening_amount_kg; ?></th>
				<th width="100" id="value_receive_kg"><? echo $uom_total_receive_kg; ?></th>
				<th width="100" id="value_receive_amount_kg"><? echo $uom_total_receive_amount_kg; ?></th>
				<th width="100" id="value_issue_kg"><? echo $uom_total_issue_kg; ?></th>
				<th width="100" id="value_issue_amount_kg"><? echo $uom_total_issue_amount_kg; ?></th>
				<th width="100" id="value_stock_qnty_kg"><? echo $uom_total_stock_qnty_kg; ?></th>
				<th width="100" id="value_stock_amount_kg"><? echo $uom_total_stock_amount_kg; ?></th>

				<th width="100" id="value_opening_qnty_yds"><? echo $uom_total_opening_qnty_yds; ?></th>
				<th width="100" id="value_opening_amount_yds"><? echo $uom_total_opening_amount_yds; ?></th>
				<th width="100" id="value_receive_yds"><? echo $uom_total_receive_yds; ?></th>
				<th width="100" id="value_receive_amount_yds"><? echo $uom_total_receive_amount_yds; ?></th>
				<th width="100" id="value_issue_yds"><? echo $uom_total_issue_yds; ?></th>
				<th width="100" id="value_issue_amount_yds"><? echo $uom_total_issue_amount_yds; ?></th>
				<th width="100" id="value_stock_qnty_yds"><? echo $uom_total_stock_qnty_yds; ?></th>
				<th width="100" id="value_stock_amount_yds"><? echo $uom_total_stock_amount_yds; ?></th>

				<th width="100" id="value_opening_qnty_mtr"><? echo $uom_total_opening_qnty_mtr; ?></th>
				<th width="100" id="value_opening_amount_mtr"><? echo $uom_total_opening_amount_mtr; ?></th>
				<th width="100" id="value_receive_mtr"><? echo $uom_total_receive_mtr; ?></th>
				<th width="100" id="value_receive_amount_mtr"><? echo $uom_total_receive_amount_mtr; ?></th>
				<th width="100" id="value_issue_mtr"><? echo $uom_total_issue_mtr; ?></th>
				<th width="100" id="value_issue_amount_mtr"><? echo $uom_total_issue_amount_mtr; ?></th>
				<th width="100" id="value_stock_qnty_mtr"><? echo $uom_total_stock_qnty_mtr; ?></th>
				<th width="100" id="value_stock_amount_mtr"><? echo $uom_total_stock_amount_mtr; ?></th>

				<th width="100" id="value_opening_grand_total_value"><? echo $uom_total_opening_grand_total_value; ?></th>
				<th width="100" id="value_receive_grand_total_value"><? echo $uom_total_receive_grand_total_value; ?></th>
				<th width="100" id="value_issue_grand_total_value"><? echo $uom_total_issue_grand_total_value; ?></th>
				<th width="" id="value_closing_grand_total_value"><? echo $uom_total_closing_grand_total_value; ?></th>
			</tfoot>
		</table>
		<!-- ============ Details Start ===============-->
	</fieldset>
	<?
	//echo "Execution Time: " . (microtime(true) - $started) . "S";
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
	echo "$total_data####$filename####$report_type";

	exit();
}

if($action=="stock_in_kg_yds_mtr_pupup______backup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	// echo "<pre>";print_r($_REQUEST);
	?>
	<fieldset style="width:570px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th width="30">Sl</th>
						<th width="75">Store Name</th>
						<th width="100">Rack Name</th>
						<th width="100">Shelf</th>
						<th width="100">Style no</th>
						<th width="80">Qty.</th>
					</tr>
				</thead>
				<tbody>
					<?					
					/*if($rack_ids)
					{
						$rack_cond = " and b.rack in($rack_ids)";
					}*/

					$booking_no_arr=explode(",",$booking_no);
					// $all_booking_no="'".implode("','",$booking_no_arr)."'";

					$all_book_nos_cond=""; $sampBookCond="";
					if($db_type==2 && count($booking_no_arr)>999)
					{
						$all_samp_book_arr_chunk=array_chunk($booking_no_arr,999) ;
						foreach($all_samp_book_arr_chunk as $chunk_arr)
						{
							$chunk_arr_value="'".implode("','",$chunk_arr)."'";
							$sampBookCond.="  e.booking_no in($chunk_arr_value) or ";
						}

						$all_book_nos_cond.=" and (".chop($sampBookCond,'or ').")";
					}
					else
					{
						$all_book_nos_cond=" and e.booking_no in('".implode("','",$booking_no_arr)."')";
					}
					// echo $all_book_nos_cond;die;

					$prod_ids_arr=explode(",",$prod_ids);
					$all_prod_ids_cond=""; $prod_idsCond="";
					if($db_type==2 && count($prod_ids_arr)>999)
					{
						$all_prod_ids_arr_chunk=array_chunk($prod_ids_arr,999) ;
						foreach($all_prod_ids_arr_chunk as $chunk_arr)
						{
							$chunk_arr_value=implode(",",$chunk_arr);
							$prod_idsCond.="  c.prod_id in($chunk_arr_value) or ";
						}

						$all_prod_ids_cond.=" and (".chop($prod_idsCond,'or ').")";
					}
					else
					{
						$all_prod_ids_cond=" and c.prod_id in(".implode(",",$prod_ids_arr).")";
					}
					// echo $all_prod_ids_cond;die;

					$batchIds_arr=explode(",",$batchIds);
					$all_batchIds_cond=""; $batchIdsCond="";
					if($db_type==2 && count($batchIds_arr)>999)
					{
						$all_batchIds_arr_chunk=array_chunk($batchIds_arr,999) ;
						foreach($all_batchIds_arr_chunk as $chunk_arr)
						{
							$chunk_arr_value=implode(",",$chunk_arr);
							$batchIdsCond.="  e.id in($chunk_arr_value) or ";
						}

						$all_batchIds_cond.=" and (".chop($batchIdsCond,'or ').")";
					}
					else
					{
						$all_batchIds_cond=" and e.id in(".implode(",",$batchIds_arr).")";
					}
					// echo $all_batchIds_cond;die;



					$rcv_sql = sql_select("SELECT a.recv_number, c.prod_id, c.cons_quantity as quantity, c.store_id, c.rack, c.self, e.batch_no, e.booking_no
					from inv_receive_master a,inv_transaction c,pro_finish_fabric_rcv_dtls d, pro_batch_create_mst e  
					WHERE a.company_id in ($companyID) and a.id = c.mst_id and c.id = d.trans_id  and c.transaction_type =1 and c.item_category=2 and a.entry_form = 37 and a.status_active =1 and c.status_active =1 and d.status_active =1  and c.pi_wo_batch_no = e.id  and c.store_id in($store_ids) and c.cons_uom = $uom $all_batchIds_cond  $all_prod_ids_cond $all_book_nos_cond"); // and b.rack in($rack_ids) and e.booking_no in($all_booking_no) and c.prod_id in($prod_ids) and e.id in($batchIds)

					foreach($rcv_sql as $row)
					{
						$recv_data_arr[$row[csf('store_id')]][$row[csf('rack')]][$row[csf('self')]]['quantity']+=$row[csf('quantity')];
						$recv_data_arr[$row[csf('store_id')]][$row[csf('rack')]][$row[csf('self')]]['booking_no'].=$row[csf('booking_no')].',';
					}

					$trans_in_sql = sql_select("SELECT a.transfer_system_id, c.store_id, c.rack, c.self , c.cons_quantity as quantity, e.batch_no, e.booking_no 
					from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c, product_details_master d, pro_batch_create_mst e 
					where a.id = b.mst_id and b.to_trans_id = c.id  and c.prod_id = d.id and c.pi_wo_batch_no = e.id and c.company_id in ($companyID) and c.item_category=2  and c.store_id in($store_ids) and c.cons_uom = $uom and c.transaction_type = 5 and a.status_active =1 and b.status_active =1 and c.status_active =1  and a.entry_form in (14,15,306) $all_batchIds_cond  $all_prod_ids_cond $all_book_nos_cond"); //  and c.rack in($rack_ids)  and e.booking_no in($all_booking_no)  and c.prod_id in($prod_ids) and e.id in($batchIds)
					foreach($trans_in_sql as $row)
					{
						$recv_data_arr[$row[csf('store_id')]][$row[csf('rack')]][$row[csf('self')]]['quantity']+=$row[csf('quantity')];
						$recv_data_arr[$row[csf('store_id')]][$row[csf('rack')]][$row[csf('self')]]['booking_no'].=$row[csf('booking_no')].',';
					}

					$issRtnSql = sql_select("SELECT a.recv_number, c.store_id, c.rack, c.self, c.cons_quantity as quantity, e.batch_no, e.booking_no
					from inv_receive_master a, pro_finish_fabric_rcv_dtls b, inv_transaction c, inv_issue_master d, pro_batch_create_mst e, product_details_master f 
					where a.id = b.mst_id and b.trans_id=c.id and c.issue_id=d.id and a.entry_form=52 and a.item_category=2 and c.pi_wo_batch_no=e.id and c.prod_id=f.id and a.status_active =1 and b.status_active=1 and c.status_active =1 and c.company_id in ($companyID)  and c.store_id in($store_ids)  and c.item_category=2 and c.cons_uom = $uom $all_batchIds_cond  $all_prod_ids_cond $all_book_nos_cond"); // and c.rack in($rack_ids) and e.booking_no in($all_booking_no) and c.prod_id in($prod_ids) and e.id in($batchIds)
					foreach($issRtnSql as $row)
					{
						$recv_data_arr[$row[csf('store_id')]][$row[csf('rack')]][$row[csf('self')]]['quantity']+=$row[csf('quantity')];
						$recv_data_arr[$row[csf('store_id')]][$row[csf('rack')]][$row[csf('self')]]['booking_no'].=$row[csf('booking_no')].',';
					}
					// echo "<pre>";print_r($recv_data_arr);die;
					// ================================ All Recv End ==========================================

					// ================================ All Issue Start =======================================
					$issue_sql = sql_select("SELECT a.issue_number, c.store_id, c.rack, c.self, c.cons_quantity as quantity 
					from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c , product_details_master d, pro_batch_create_mst e  
					where a.id = b.mst_id and b.trans_id = c.id and c.prod_id = d.id and c.pi_wo_batch_no= e.id and c.company_id in ($companyID) and c.store_id in($store_ids) and c.cons_uom = $uom and a.entry_form = 18 and c.status_active =1 and b.status_active=1 and a.status_active =1 and c.item_category=2 and c.transaction_type =2 $all_batchIds_cond  $all_prod_ids_cond $all_book_nos_cond"); //  and c.rack in($rack_ids) and e.booking_no in($all_booking_no) and c.prod_id in($prod_ids) and e.id in($batchIds)
					
					foreach($issue_sql as $row)
					{
						$issue_data_arr[$row[csf('store_id')]][$row[csf('rack')]][$row[csf('self')]]['issue_qty']+=$row[csf('quantity')];
					}

					$trans_out_sql = sql_select("SELECT a.transfer_system_id, c.store_id, c.rack, c.self, c.cons_quantity as quantity
					from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c, product_details_master d, pro_batch_create_mst e 
					where a.id = b.mst_id and b.trans_id = c.id  and c.prod_id = d.id and c.pi_wo_batch_no = e.id and c.company_id in ($companyID) and c.item_category=2 and c.store_id in($store_ids) and c.cons_uom = $uom and c.transaction_type = 6 and a.status_active =1 and b.status_active =1 and c.status_active =1  and a.entry_form in (14,15,306) $all_batchIds_cond  $all_prod_ids_cond $all_book_nos_cond"); //  and c.rack in($rack_ids) and c.prod_id  and e.booking_no in($all_booking_no) in($prod_ids) and e.id in($batchIds)
					foreach($trans_out_sql as $row)
					{
						$issue_data_arr[$row[csf('store_id')]][$row[csf('rack')]][$row[csf('self')]]['issue_qty']+=$row[csf('quantity')];
					}

					$rcvRtnSql = sql_select("SELECT c.store_id, c.rack, c.self, c.cons_quantity as quantity, e.booking_without_order
					from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c, product_details_master d, pro_batch_create_mst e 
					where a.id = b.mst_id and b.trans_id=c.id and a.entry_form =46 and c.company_id in ($companyID)  and c.store_id in($store_ids) and c.cons_uom = $uom and c.prod_id=d.id and c.pi_wo_batch_no=e.id and a.status_active =1 and b.status_active =1 and c.status_active =1 $all_batchIds_cond  $all_prod_ids_cond $all_book_nos_cond"); //  and c.rack in($rack_ids) and e.booking_no in($all_booking_no) and c.prod_id in($prod_ids) and e.id in($batchIds)
					foreach($rcvRtnSql as $row)
					{
						$issue_data_arr[$row[csf('store_id')]][$row[csf('rack')]][$row[csf('self')]]['issue_qty']+=$row[csf('quantity')];
					}
					// ================================ All Issue End =======================================

					$booking_sql = sql_select("SELECT c.booking_no, f.style_ref_no
					from wo_booking_dtls c, wo_booking_mst d, wo_po_break_down e, wo_po_details_master f
					where c.booking_no = d.booking_no and e.job_no_mst=f.job_no and c.po_break_down_id = e.id and c.status_active=1 and e.status_active=1 and c.booking_type in(1,4) and c.booking_no in($all_booking_no)");
					foreach ($booking_sql as  $val)
					{
						$book_po_ref[$val[csf("booking_no")]]["style_ref_no"] .= $val[csf("style_ref_no")].",";
					}
					unset($booking_sql);

					$non_samp_sql = sql_select("SELECT a.booking_no, b.style_des from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and b.status_active =1 and a.booking_type=4 and a.booking_no in($all_booking_no)");

					foreach ($non_samp_sql as  $val)
					{
						$book_po_ref[$val[csf("booking_no")]]["style_ref_no"] .= $val[csf("style_des")].",";
					}
					unset($non_samp_sql);
					// echo "<pre>";print_r($book_po_ref);

					$floor_room_rack_arr = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst where status_active =1 and is_deleted=0","floor_room_rack_id","floor_room_rack_name");
					$store_arr 		= return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
					$i=1;
					foreach($recv_data_arr as $store_id => $store_data)
					{
						foreach ($store_data as $rack_id => $rack_data) 
						{
							foreach ($rack_data as $self_id => $row) 
							{	
								$style_no="";
								$booking_array=array_unique(explode(",",chop($row["booking_no"],",")));
								foreach ($booking_array as $key => $booking) 
								{
									// echo $booking.'<br>';
									$style_no.=$book_po_ref[$booking]["style_ref_no"].',';
								}
								$style_ref_no = implode(",",array_unique(explode(",",chop($style_no,","))));;
								
								// echo $row['quantity'].'-'.$issue_data_arr[$store_id][$rack_id][$self_id]['issue_qty'].'<br>';
								$stock_qty=$row['quantity']-$issue_data_arr[$store_id][$rack_id][$self_id]['issue_qty'];
								
								if(number_format($stock_qty,2,".","") == "-0.00")
								{
									$stock_qty=0;
								}
								if($stock_qty!=0)
								{
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
									<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
										<td width="30"><p><? echo $i; ?></p></td>
										<td width="75"><p><? echo $store_arr[$store_id]; ?></p></td>
										<td width="100"><p><? echo $floor_room_rack_arr[$rack_id]; ?></p></td>
										<td width="100"><p><? echo $floor_room_rack_arr[$self_id]; ?></p></td>
										<td width="50"><p><? echo chop($style_ref_no,","); ?></p></td>
										<td width="80" align="right"><p><? echo number_format($stock_qty,2,".",""); ?></p></td>
									</tr>
									<?
									$tot_qty+=$stock_qty;
									$i++;
								}
							}
						}
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="5" align="right">G.TTL</td>
						<td align="right">&nbsp;<? echo number_format($tot_qty,2); ?>&nbsp;</td>
					</tr>
				</tfoot>
			</table>

		</div>
	</fieldset>
	<?
	exit();
}

if($action=="stock_in_kg_yds_mtr_pupup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	// echo "<pre>";print_r($_REQUEST);

	if($cbo_store_name > 0){
		$store_cond = " and b.store_id in ($cbo_store_name)";
		$store_cond_2 = " and c.store_id in ($cbo_store_name)";
	}

	if($db_type==0)
	{
		if($job_year==0) $year_cond=""; else $year_cond=" and YEAR(g.insert_date)=$job_year";
	}
	else if($db_type==2)
	{
		if($job_year==0) $year_cond=""; else $year_cond=" and to_char(g.insert_date,'YYYY')=$job_year";
	}
	// echo $year_cond.'==';die;
	$date_cond="";
	if($date_from!="" && $date_to!="")
	{
		if($db_type==0) $start_date=change_date_format($date_from,"yyyy-mm-dd","");
		else if($db_type==2) $start_date=change_date_format($date_from,"","",1);

		if($db_type==0)$end_date=change_date_format($date_to,"yyyy-mm-dd","");
		else if($db_type==2) $end_date=change_date_format($date_to,"","",1);

		$date_cond   = " and b.transaction_date <= '$end_date'";
		$date_cond_2 = " and c.transaction_date <= '$end_date'";
	}

	$buyer_arr 		= return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );

	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and g.job_no_prefix_num in ($job_no) ";
	if ($buyer=="") $buyer_cond=""; else $buyer_cond=" and g.buyer_name in ($buyer) ";
	if ($buyer=="") $buyer_cond2=""; else $buyer_cond2=" and g.buyer_id in ($buyer) ";
	// if ($client==0) $client="";
	if ($client==0) $client_cond=" and (g.client_id is null or g.client_id=0)" ; else $client_cond=" and g.client_id =$client ";	
	
	if ($buyer_id!=0) 
	{
		if($db_type==0)
		{
			if($job_year==0) $year_cond=""; else $year_cond=" and YEAR(g.insert_date)=$job_year";
		}
		else if($db_type==2)
		{
			if($job_year==0) $year_cond=""; else $year_cond=" and to_char(g.insert_date,'YYYY')=$job_year";
		}
	}
	


	$rcv_sql = "SELECT b.id,e.id as batchId,e.booking_no, e.booking_no_id, e.booking_without_order, b.store_id, b.floor_id, b.room, b.rack, b.self, b.cons_uom, d.po_breakdown_id, d.quantity, b.pi_wo_batch_no, 
	g.buyer_name as buyer_id, g.client_id
	FROM inv_transaction b, pro_finish_fabric_rcv_dtls c, order_wise_pro_details d, WO_PO_BREAK_DOWN f, wo_po_details_master g,  pro_batch_create_mst e
	WHERE b.company_id in ($cbo_company_id) and b.id=c.trans_id and c.trans_id = d.trans_id and D.PO_BREAKDOWN_ID=f.id and F.JOB_NO_MST=G.JOB_NO and d.entry_form=37 and d.po_breakdown_id <>0 and b.transaction_type=1 and b.status_active =1 and c.status_active =1 and b.pi_wo_batch_no=e.id and b.cons_uom = $uom $store_cond $date_cond $job_no_cond  $buyer_cond $client_cond $year_cond"; // $year_cond

	if ($client==0)
	{
		$rcv_sql .= " union all SELECT b.id,e.id as batchId,e.booking_no, e.booking_no_id, e.booking_without_order, b.store_id, b.floor_id, b.room, b.rack, b.self, b.cons_uom, null as po_breakdown_id, b.cons_quantity as quantity, b.pi_wo_batch_no
		, g.buyer_id, null as client_id 
		FROM inv_transaction b, pro_finish_fabric_rcv_dtls c, pro_batch_create_mst e, wo_non_ord_samp_booking_mst g
		WHERE b.company_id in ($cbo_company_id) and b.id=c.trans_id and e.booking_no=g.booking_no and b.transaction_type=1
		and b.status_active =1 and c.status_active =1 and b.pi_wo_batch_no=e.id and b.cons_uom = $uom $store_cond $date_cond $buyer_cond2";
	}
	
	// echo $rcv_sql;die;
	$rcv_data = sql_select($rcv_sql);
	foreach ($rcv_data as  $row)
	{
		$recv_data_arr[$row[csf('store_id')]][$row[csf('rack')]][$row[csf('self')]]['quantity']+=$row[csf('quantity')];
		$recv_data_arr[$row[csf('store_id')]][$row[csf('rack')]][$row[csf('self')]]['booking_no'].=$row[csf('booking_no')].',';
		$batch_id_arr[$row[csf("pi_wo_batch_no")]] = $row[csf("pi_wo_batch_no")];
		$booking_no_arr[$row[csf("booking_no")]] = $row[csf("booking_no")];
	}
	// echo "<pre>"; print_r($recv_data_arr); die;
	
	$trans_in_sql = "SELECT c.transaction_date, c.pi_wo_batch_no,e.id as batchid, e.batch_no, e.booking_no, e.booking_no_id,c.store_id, c.floor_id, c.room, c.rack, c.self, c.cons_uom, d.quantity as quantity, d.po_breakdown_id, g.buyer_name as buyer_id, g.client_id
	from inv_item_transfer_dtls b, inv_transaction c, order_wise_pro_details d, wo_po_break_down f, wo_po_details_master g, pro_batch_create_mst e 
	where b.to_trans_id=c.id and c.pi_wo_batch_no=e.id and c.id = d.trans_id and d.po_breakdown_id=f.id and f.job_no_mst=g.job_no and d.trans_type = 5 and d.status_active=1 and d.po_breakdown_id<>0
	and c.company_id in (3) and c.item_category=2 and c.transaction_type=5 and b.status_active=1
	and c.status_active=1 and d.entry_form in (14,15,306) and c.cons_uom = $uom $store_cond_2 $date_cond_2 $job_no_cond  $buyer_cond  $client_cond $year_cond"; // $year_cond
	
	// union all
	if ($client==0) 
	{
		$trans_in_sql .= " union all SELECT c.transaction_date, c.pi_wo_batch_no,e.id as batchid, e.batch_no, e.booking_no, e.booking_no_id,c.store_id, c.floor_id, c.room, c.rack, c.self, c.cons_uom, c.cons_quantity as quantity, null as po_breakdown_id, g.buyer_id, null as client_id
		from inv_item_transfer_dtls b, inv_transaction c, pro_batch_create_mst e, wo_non_ord_samp_booking_mst g 
		where b.to_trans_id=c.id and c.pi_wo_batch_no=e.id and e.booking_no=g.booking_no and c.transaction_type=5 and c.company_id in (3) and c.item_category=2 and c.transaction_type=5 and b.status_active=1 and c.status_active=1 and c.cons_uom = $uom $store_cond_2 $date_cond_2 $buyer_cond2";
	}
	// echo $trans_in_sql;die;
	$trans_in_data = sql_select($trans_in_sql);
	foreach ($trans_in_data as  $row)
	{
		$recv_data_arr[$row[csf('store_id')]][$row[csf('rack')]][$row[csf('self')]]['quantity']+=$row[csf('quantity')];
		$recv_data_arr[$row[csf('store_id')]][$row[csf('rack')]][$row[csf('self')]]['booking_no'].=$row[csf('booking_no')].',';
		$batch_id_arr[$row[csf("pi_wo_batch_no")]] = $row[csf("pi_wo_batch_no")];
		$booking_no_arr[$row[csf("booking_no")]] = $row[csf("booking_no")];
	}	
	// echo "<pre>"; print_r($recv_data_arr); die;

	$batch_id_arr = array_filter($batch_id_arr);
	if(!empty($batch_id_arr))
	{
		$batch_ids= implode(",",$batch_id_arr);

		$all_batch_ids_cond=""; $batchCond="";
		if($db_type==2 && count($batch_id_arr)>999)
		{
			$batch_id_arr_chunk=array_chunk($batch_id_arr,999) ;
			foreach($batch_id_arr_chunk as $chunk_arr)
			{
				$chunk_arr_value=implode(",",$chunk_arr);
				$batchCond.="  e.id in($chunk_arr_value) or ";
			}
			$all_batch_ids_cond.=" and (".chop($batchCond,'or ').")";
		}
		else
		{
			$all_batch_ids_cond=" and e.id in($batch_ids)";
		}

		$issRtnSql = "SELECT c.transaction_date, d.knit_dye_source, b.body_part_id, b.prod_id,c.store_id, c.floor_id, c.room, c.rack, c.self, b.fabric_description_id, b.gsm, b.width, f.color as color_id,c.cons_uom, c.cons_quantity as quantity, c.order_rate, b.batch_id, e.id as batchId, e.batch_no, e.booking_no, e.booking_without_order 
		from inv_receive_master a, pro_finish_fabric_rcv_dtls b,inv_transaction c, inv_issue_master d, pro_batch_create_mst e, product_details_master f 
		where a.id = b.mst_id and b.trans_id=c.id and c.issue_id=d.id and a.entry_form=52 and a.item_category=2 and c.pi_wo_batch_no=e.id and c.prod_id=f.id and a.status_active =1 and b.status_active=1 and c.status_active =1 and c.company_id in  ($cbo_company_id) and c.cons_uom = $uom $store_cond_2 $date_cond_2 $all_batch_ids_cond";
		$issRtnData = sql_select($issRtnSql);
		foreach ($issRtnData as $row)
		{
			$recv_data_arr[$row[csf('store_id')]][$row[csf('rack')]][$row[csf('self')]]['quantity']+=$row[csf('quantity')];
			$recv_data_arr[$row[csf('store_id')]][$row[csf('rack')]][$row[csf('self')]]['booking_no'].=$row[csf('booking_no')].',';
			$booking_no_arr[$row[csf("booking_no")]] = $row[csf("booking_no")];
		}
		// echo "<pre>";print_r($recv_data_arr);die;

		$issue_sql = sql_select("SELECT a.knit_dye_source, a.issue_purpose, c.prod_id, b.body_part_id, c.cons_uom, c.store_id, c.floor_id, c.room, c.rack, c.self, c.cons_quantity, c.id as trans_id,c.transaction_date, d.detarmination_id, d.gsm, d.dia_width, d.color, e.booking_no, e.booking_without_order 
		from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c, product_details_master d, pro_batch_create_mst e  
		where a.id = b.mst_id and b.trans_id = c.id and c.prod_id = d.id and c.pi_wo_batch_no= e.id and c.company_id in ($cbo_company_id) and c.cons_uom = $uom $store_cond_2 $date_cond_2 $all_batch_ids_cond and a.entry_form=18 and c.status_active=1 and b.status_active=1 and a.status_active=1 and c.item_category =2 and c.transaction_type =2");
		foreach ($issue_sql as $row)
		{
			$issue_data_arr[$row[csf('store_id')]][$row[csf('rack')]][$row[csf('self')]]['issue_qty']+=$row[csf('cons_quantity')];
		}
		// echo "<pre>";print_r($issue_data_arr);

		$rcvRtnSql = sql_select("SELECT c.transaction_date, c.company_id, c.prod_id, c.store_id, c.floor_id, c.room, c.rack, c.self, c.cons_quantity, c.cons_uom, d.detarmination_id, d.gsm, d.dia_width, d.color, e.booking_no, e.booking_without_order, b.body_part_id, c.order_rate
		from inv_issue_master a, inv_finish_fabric_issue_dtls b, inv_transaction c, product_details_master d, pro_batch_create_mst e 
		where a.id = b.mst_id and b.trans_id=c.id and a.entry_form =46 and c.company_id in ($cbo_company_id) and c.cons_uom = $uom $store_cond_2 $date_cond_2 $all_batch_ids_cond and c.prod_id=d.id and c.pi_wo_batch_no=e.id and a.status_active =1 and b.status_active =1 and c.status_active =1");

		foreach ($rcvRtnSql as $row)
		{
			$issue_data_arr[$row[csf('store_id')]][$row[csf('rack')]][$row[csf('self')]]['issue_qty']+=$row[csf('cons_quantity')];
		}
		// echo "<pre>";print_r($issue_data_arr);

		$transOutSql = sql_select("SELECT c.transaction_date,c.pi_wo_batch_no, e.batch_no,e.booking_no, e.booking_no_id, e.booking_without_order, c.body_part_id, c.prod_id, c.store_id, c.floor_id, c.room, c.rack, c.self, d.detarmination_id, d.gsm, d.dia_width, d.color, c.cons_uom, c.cons_quantity,c.order_rate 
		from inv_item_transfer_mst a, inv_item_transfer_dtls b, inv_transaction c,product_details_master d, pro_batch_create_mst e 
		where a.id=b.mst_id and b.trans_id=c.id and c.prod_id=d.id and c.pi_wo_batch_no=e.id and c.company_id in ($cbo_company_id) and c.cons_uom = $uom $store_cond_2 $date_cond_2 $all_batch_ids_cond and c.item_category=2 and c.transaction_type=6 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.entry_form in (14,15,306)");

		foreach ($transOutSql as $row)
		{
			$issue_data_arr[$row[csf('store_id')]][$row[csf('rack')]][$row[csf('self')]]['issue_qty']+=$row[csf('cons_quantity')];
		}
	}
	// echo "<pre>";print_r($issue_data_arr);

	$floor_room_rack_arr = return_library_array("select floor_room_rack_id, floor_room_rack_name from lib_floor_room_rack_mst where status_active =1 and is_deleted=0","floor_room_rack_id","floor_room_rack_name");
	$store_arr 		= return_library_array("select id, store_name from lib_store_location", 'id', 'store_name');
	?>
	<fieldset style="width:570px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="570" cellpadding="0" cellspacing="0" align="center">
				<thead>
					<tr>
						<th width="30">Sl</th>
						<th width="75">Store Name</th>
						<th width="100">Rack Name</th>
						<th width="100">Shelf</th>
						<th width="100">Style no</th>
						<th width="80">Qty.</th>
					</tr>
				</thead>
				<tbody>
					<?
					if (!empty($booking_no_arr)) 
					{
						$all_book_nos_cond=""; $sampBookCond="";
						if($db_type==2 && count($booking_no_arr)>999)
						{
							$all_samp_book_arr_chunk=array_chunk($booking_no_arr,999) ;
							foreach($all_samp_book_arr_chunk as $chunk_arr)
							{
								$chunk_arr_value="'".implode("','",$chunk_arr)."'";
								$sampBookCond.="  a.booking_no in($chunk_arr_value) or ";
							}

							$all_book_nos_cond.=" and (".chop($sampBookCond,'or ').")";
						}
						else
						{
							$all_book_nos_cond=" and a.booking_no in('".implode("','",$booking_no_arr)."')";
						}
						// echo $all_book_nos_cond;die;

						$booking_sql = sql_select("SELECT a.booking_no, f.style_ref_no
						from wo_booking_dtls a, wo_booking_mst d, wo_po_break_down e, wo_po_details_master f
						where a.booking_no = d.booking_no and e.job_no_mst=f.job_no and a.po_break_down_id = e.id and a.status_active=1 and e.status_active=1 and a.booking_type in(1,4) $all_book_nos_cond");
						foreach ($booking_sql as  $val)
						{
							$book_po_ref[$val[csf("booking_no")]]["style_ref_no"] .= $val[csf("style_ref_no")].",";
						}
						unset($booking_sql);

						$non_samp_sql = sql_select("SELECT a.booking_no, b.style_des from wo_non_ord_samp_booking_mst a, wo_non_ord_samp_booking_dtls b where a.booking_no=b.booking_no and b.status_active =1 and a.booking_type=4 $all_book_nos_cond");

						foreach ($non_samp_sql as  $val)
						{
							$book_po_ref[$val[csf("booking_no")]]["style_ref_no"] .= $val[csf("style_des")].",";
						}
						unset($non_samp_sql);
						// echo "<pre>";print_r($book_po_ref);
					}
					

					
					$i=1;
					foreach($recv_data_arr as $store_id => $store_data)
					{
						foreach ($store_data as $rack_id => $rack_data) 
						{
							foreach ($rack_data as $self_id => $row) 
							{	
								$style_no="";
								$booking_array=array_unique(explode(",",chop($row["booking_no"],",")));
								foreach ($booking_array as $key => $booking) 
								{
									// echo $booking.'<br>';
									$style_no.=$book_po_ref[$booking]["style_ref_no"].',';
								}
								$style_ref_no = implode(",",array_unique(explode(",",chop($style_no,","))));;
								
								$issue_qty=$issue_data_arr[$store_id][$rack_id][$self_id]['issue_qty'];
								$stock_qty=$row['quantity']-$issue_qty;
								
								if(number_format($stock_qty,2,".","") == "-0.00")
								{
									$stock_qty=0;
								}
								if($stock_qty!=0)
								{
									if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
									<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
										<td width="30"><p><? echo $i; ?></p></td>
										<td width="75"><p><? echo $store_arr[$store_id]; ?></p></td>
										<td width="100"><p><? echo $floor_room_rack_arr[$rack_id]; ?></p></td>
										<td width="100"><p><? echo $floor_room_rack_arr[$self_id]; ?></p></td>
										<td width="50"><p><? echo chop($style_ref_no,","); ?></p></td>
										<td width="80" align="right" title="<?echo $row['quantity'].'-'.$issue_qty;?>"><p><? echo number_format($stock_qty,2,".",""); ?></p></td>
									</tr>
									<?
									$tot_qty+=$stock_qty;
									$i++;
								}
							}
						}
					}
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="5" align="right">G.TTL</td>
						<td align="right">&nbsp;<? echo number_format($tot_qty,2); ?>&nbsp;</td>
					</tr>
				</tfoot>
			</table>

		</div>
	</fieldset>
	<?
	exit();
}

?>