<?

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
//--------------------------------------------------------------------------------------------------------------------
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_id", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name","id,buyer_name", 1, "-All Buyer-", $selected, "" );
	exit();
}

if($action=="fso_search_popup")
{
	echo load_html_head_contents("Sales Order Info","../../../", 1, 1, '','1','');
	extract($_REQUEST);
	?>
	<script>

		function js_set_value(booking_data)
		{
			document.getElementById('hidden_booking_data').value=booking_data;
			parent.emailwindow.hide();
		}

	</script>
</head>
<body>
	<div align="center">
		<fieldset style="width:830px;margin-left:4px;">
			<form name="searchorderfrm_1"  id="searchorderfrm_1" autocomplete="off">
				<table cellpadding="0" cellspacing="0" width="700" border="1" rules="all" class="rpt_table">
					<thead>
						<th>Within Group</th>
						<th>Search By</th>
						<th>Search</th>
						<th>
							<input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
							<input type="hidden" name="hidden_booking_data" id="hidden_booking_data" value="">
						</th>
					</thead>
					<tr class="general">
						<td align="center"><? echo create_drop_down( "cbo_within_group", 150, $yes_no,"",1, "--Select--", 1,$dd,1 ); ?></td>
						<td align="center">
							<?
							$search_by_arr=array(1=>"Sales Order No",2=>"Sales / Booking No",3=>"Style Ref.");
							echo create_drop_down( "cbo_search_by", 150, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
						</td>
						<td align="center">
							<input type="text" style="width:140px" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
						</td>
						<td align="center">
							<input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('txt_search_common').value+'_'+document.getElementById('cbo_search_by').value+'_'+<? echo $companyID; ?>+'_'+document.getElementById('cbo_within_group').value, 'create_fso_search_list_view', 'search_div', 'delivery_date_wise_fin_fab_status_report_controller', 'setFilterGrid(\'tbl_list_search\',-1);')" style="width:100px;" />
						</td>
					</tr>
				</table>
				<div id="search_div" style="margin-top:10px"></div>
			</form>
		</fieldset>
	</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action=="create_fso_search_list_view")
{
	$data=explode('_',$data);

	$company_arr 	= return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
	$buyer_arr 		= return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$location_arr 	= return_library_array( "select id, location_name from lib_location",'id','location_name');

	$search_string  = trim($data[0]);
	$search_by 		= $data[1];
	$company_id 	= $data[2];
	$within_group 	= $data[3];

	$search_field_cond='';
	if($search_string!="")
	{
		if($search_by==1) $search_field_cond=" and job_no like '%".$search_string."'";
		else if($search_by==2) $search_field_cond=" and sales_booking_no like '%".$search_string."'";
		else $search_field_cond=" and style_ref_no like '".$search_string."%'";
	}

	if($within_group==0) $within_group_cond=""; else $within_group_cond=" and within_group=$within_group";

	if($db_type==0) $year_field="YEAR(insert_date) as year";
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year";
	else $year_field="";

	$sql = "select id, $year_field, job_no_prefix_num, job_no, within_group, sales_booking_no, booking_date, buyer_id, style_ref_no, location_id, po_buyer from fabric_sales_order_mst where status_active=1 and is_deleted=0 and company_id=$company_id $within_group_cond $search_field_cond order by id desc";
	$result = sql_select($sql);
	?>
	<table cellspacing="0" cellpadding="0" border="1" rules="all" width="800" class="rpt_table">
		<thead>
			<th width="40">SL</th>
			<th width="90">Sales Order No</th>
			<th width="60">Year</th>
			<th width="80">Within Group</th>
			<th width="70">Buyer</th>
			<th width="120">Sales/ Booking No</th>
			<th width="80">Booking date</th>
			<th width="110">Style Ref.</th>
			<th>Location</th>
		</thead>
	</table>
	<div style="width:800px; max-height:300px; overflow-y:scroll" id="list_container_batch" align="left">
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="780" class="rpt_table" id="tbl_list_search">
			<?
			$i=1;
			foreach ($result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

				if($row[csf('within_group')]==1)
					$buyer=$buyer_arr[$row[csf('po_buyer')]];
				else
					$buyer=$buyer_arr[$row[csf('buyer_id')]];

				$booking_data =$row[csf('id')]."**".$row[csf('job_no')];
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="js_set_value('<? echo $booking_data; ?>');">
					<td width="40"><? echo $i; ?></td>
					<td width="90"><p>&nbsp;<? echo $row[csf('job_no_prefix_num')]; ?></p></td>
					<td width="60" align="center"><p><? echo $row[csf('year')]; ?></p></td>
					<td width="80"><p><? echo $yes_no[$row[csf('within_group')]]; ?>&nbsp;</p></td>
					<td width="70"><p><? echo $buyer; ?>&nbsp;</p></td>
					<td width="120"><p><? echo $row[csf('sales_booking_no')]; ?></p></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('booking_date')]); ?></td>
					<td width="110"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
					<td><p><? echo $location_arr[$row[csf('location_id')]]; ?></p></td>
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

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$company_arr = return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
	$buyer_arr 	 = return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$color_arr 	 = return_library_array( "select id,color_name from lib_color", "id", "color_name");
	$season_arr 	 = return_library_array( "select id,season_name from lib_buyer_season", "id", "season_name");
	$booking_type_arr = array("118"=>"Main","108"=>"Partial","88"=>"Short","89"=>"Sample With Order","90"=>"Sample Without Order");

	$company_name 		= str_replace("'","",$cbo_company_id);
	$cbo_lc_company_id	= str_replace("'","",$cbo_lc_company_id);
	$cbo_buyer_id 		= str_replace("'","",$cbo_buyer_id);
	$cbo_year 			= str_replace("'","",$cbo_year);
	$txt_fso_no 		= str_replace("'","",$txt_fso_no);
	$hidden_fso_id 		= str_replace("'","",$hidden_fso_id);
	$txt_date_from 		= str_replace("'","",$txt_date_from);
	$txt_date_to 		= str_replace("'","",$txt_date_to);


	if($company_name==0) $company_cond=""; else $company_cond="and a.supplier_id='$company_name'";
	if($cbo_lc_company_id==0) $pocompany_cond=""; else $pocompany_cond="and d.company_name='$cbo_lc_company_id'";

	if ($cbo_buyer_id==0) $buyer_cond=""; else $buyer_cond=" and d.buyer_name=$cbo_buyer_id";

	if($db_type==0)
	{
		if($cbo_year!=0) $year_search_cond=" and year(d.insert_date)=$cbo_year"; else $year_search_cond="";
	}
	else if($db_type==2)
	{
		if($cbo_year!=0) $year_search_cond=" and TO_CHAR(d.insert_date,'YYYY')=$cbo_year"; else $year_search_cond="";
	}


	if($txt_date_from != "" && $txt_date_to != "")
	{
		$date_range_cond = " and (task_start_date >= '$txt_date_from' and task_start_date <= '$txt_date_from' or (task_finish_date >= '$txt_date_from' and task_finish_date <= '$txt_date_to') or ('$txt_date_from' between task_start_date and task_finish_date or ('$txt_date_to' between task_start_date and task_finish_date)))";
	}

	$bookind_id_arr = array(); $jobNos=''; $tot_rows=0; $jobNo_cond='';
	if($hidden_fso_id !=""){
		$sales_info_sql = "select a.id, a.job_no, a.booking_id, a.sales_booking_no, b.po_break_down_id, b.job_no as wjob_no from fabric_sales_order_mst a,wo_booking_dtls b where a.id=$hidden_fso_id and a.sales_booking_no=b.booking_no and a.status_active=1";
		$sales_info = sql_select($sales_info_sql);
		foreach ($sales_info as $fso_row) {
			$tot_rows++;
			$jobNos.="'".$fso_row[csf("wjob_no")]."',";
			$bookind_id_arr[$fso_row[csf("booking_id")]] = $fso_row[csf("booking_id")];
			$po_break_down_arr[$fso_row[csf("po_break_down_id")]] = $fso_row[csf("po_break_down_id")];
		}
		unset($sales_info);

		$jobNos=chop($jobNos,','); $jobNo_cond="";
		if($db_type==2 && $tot_rows>1000)
		{
			$jobNo_cond=" and (";
			$jobNosArr=array_chunk(explode(",",$jobNos),999);
			foreach($jobNosArr as $ids)
			{
				$ids=implode(",",$ids);
				$jobNo_cond.=" job_no in($ids) or ";
			}
			$jobNo_cond=chop($jobNo_cond,'or ');
			$jobNo_cond.=")";
		}
		else
		{
			$jobNo_cond=" and job_no in ($jobNos)";
		}
	}

	$budget_fabric_uom_arr=array();
	//if($jobNo_cond!=""){
		$sql_b="select id, uom from wo_pre_cost_fabric_cost_dtls where status_active=1 and is_deleted=0 $jobNo_cond";
		$sql_b_data = sql_select($sql_b);
		foreach ($sql_b_data as $brow) {
			$budget_fabric_uom_arr[$brow[csf("id")]] = $brow[csf("uom")];
		}
		unset($sql_b_data);
	//}

	$po_cond = (!empty($po_break_down_arr))?" and po_number_id in(".implode(",",$po_break_down_arr).")":"";
	$tna_process_sql = "select po_number_id,max(case when task_number = '73' then task_start_date || '_' || task_finish_date end ) as tna_date_range from tna_process_mst where task_number=73 and status_active=1 $po_cond $date_range_cond group by po_number_id";
	$tna_process_data = sql_select($tna_process_sql); $tot_rows=0; $poids='';
	foreach ($tna_process_data as $tna_row) {
		$totrows++;
		$poids.=$tna_row[csf("po_number_id")].',';
		$tna_data[$tna_row[csf("po_number_id")]] = $tna_row[csf("tna_date_range")];
		$tna_po_arr[$tna_row[csf("po_number_id")]] = $tna_row[csf("po_number_id")];
	}
	unset($tna_process_data);

	$fso_cond = (!empty($bookind_id_arr))? " and a.id in(".implode(",",$bookind_id_arr).")":"";

	$poids=chop($poids,','); $tna_po_cond="";
	$poids=implode(",",array_unique(explode(",",$poids)));

	if($db_type==2 && $totrows>1000)
	{
		$tna_po_cond=" and (";
		$poidsArr=array_chunk(explode(",",$poids),999);
		foreach($poidsArr as $ids)
		{
			$ids=implode(",",$ids);
			$tna_po_cond.=" b.po_break_down_id in($ids) or ";
		}
		$tna_po_cond=chop($tna_po_cond,'or ');
		$tna_po_cond.=")";
	}
	else
	{
		$tna_po_cond=" and b.po_break_down_id in ($poids)";
	}

	$tna_booking_sql = "select b.po_break_down_id, b.pre_cost_fabric_cost_dtls_id, d.company_name, d.buyer_name, d.style_ref_no, d.season_buyer_wise as season, d.job_no, b.booking_no, b.fabric_color_id, a.id booking_id, a.entry_form, a.booking_date, sum(b.fin_fab_qnty) as fin_fab_qnty, sum(b.grey_fab_qnty) as grey_fab_qnty
	from wo_booking_mst a, wo_booking_dtls b, wo_po_details_master d
	where a.booking_no=b.booking_no and b.job_no=d.job_no and a.booking_type=1 and a.pay_mode in (3,5) $company_cond $pocompany_cond $buyer_cond $fso_cond  $year_search_cond and a.status_active=1 and b.status_active=1 and d.status_active=1
	group by d.company_name,d.buyer_name,d.style_ref_no,d.season_buyer_wise,d.job_no,b.booking_no,b.fabric_color_id,a.id,a.entry_form,b.pre_cost_fabric_cost_dtls_id, a.booking_date,b.po_break_down_id";

	$tns_info = sql_select($tna_booking_sql); $summary_buyer_arr=array(); $summary_data=array();

	if(!empty($tns_info)){
		$report_data=array();
		foreach ($tns_info as $row) {
			$booking_ids_arr[$row[csf("booking_id")]] = $row[csf("booking_id")];
			$tna_dates ="";
			$summary_buyer_arr[$row[csf("booking_no")]]['buyer']=$row[csf("buyer_name")];
			$summary_buyer_arr[$row[csf("booking_no")]]['com']=$row[csf("company_name")];

			$tna_dates = explode("_",$tna_data[$row[csf("po_break_down_id")]]);
			$date_from = strtotime($tna_dates[0]);
			$date_to   = strtotime($tna_dates[1]);
			$num_of_days =0; $per_day_book_qnty =0; $uom=0;
			$num_of_days = round(abs(strtotime($tna_dates[0]) - strtotime($tna_dates[1]))/86400)+1;
			$per_day_book_qnty = number_format(($row[csf("grey_fab_qnty")]/$num_of_days),2,".","");
			$uom=$budget_fabric_uom_arr[$row[csf("pre_cost_fabric_cost_dtls_id")]];

			for ($i=$date_from; $i<=$date_to; $i+=86400) {
				if($txt_date_from!="" && $txt_date_to!="")
				{
					if( strtotime($txt_date_from)<=strtotime(date("d-M-Y", $i)) && strtotime($txt_date_to)>=strtotime(date("d-M-Y", $i)) )
					{
						//echo $uom;
						$per_day_kg_book_qnty =$per_day_yds_book_qnty =0;
						if($uom==12){
							$per_day_kg_book_qnty =$per_day_book_qnty;
						}
						if($uom==27){
							$per_day_yds_book_qnty =$per_day_book_qnty;
						}

						$ndate= strtotime(date("d-m-Y", $i));

						$report_data[$ndate][$row[csf("booking_no")]][$row[csf("fabric_color_id")]]["info"] = $row[csf("company_name")]."*".$row[csf("buyer_name")]."*".$row[csf("style_ref_no")]."*".$row[csf("season")]."*".$row[csf("job_no")]."*".$row[csf("entry_form")]."*".$tna_data[$row[csf("po_break_down_id")]];
						$report_data[$ndate][$row[csf("booking_no")]][$row[csf("fabric_color_id")]]["kg"] +=$per_day_kg_book_qnty;
						$report_data[$ndate][$row[csf("booking_no")]][$row[csf("fabric_color_id")]]["yds"] +=$per_day_yds_book_qnty;
						$report_data[$ndate][$row[csf("booking_no")]][$row[csf("fabric_color_id")]]["finreq"] =$row[csf("fin_fab_qnty")];
						$report_data[$ndate][$row[csf("booking_no")]][$row[csf("fabric_color_id")]]["greyreq"] =$row[csf("grey_fab_qnty")];
						$report_data[$ndate][$row[csf("booking_no")]][$row[csf("fabric_color_id")]]["dayQty"] =$per_day_book_qnty;
						$report_data[$ndate][$row[csf("booking_no")]][$row[csf("fabric_color_id")]]["noDays"] =$num_of_days;
					}
				}else{
					$per_day_kg_book_qnty =$per_day_yds_book_qnty =0;
					if($uom==12){
						$per_day_kg_book_qnty =$per_day_book_qnty;
					}
					if($uom==27){
						$per_day_yds_book_qnty =$per_day_book_qnty;
					}

					$ndate= strtotime(date("d-m-Y", $i));

					$report_data[$ndate][$row[csf("booking_no")]][$row[csf("fabric_color_id")]]["info"] = $row[csf("company_name")]."*".$row[csf("buyer_name")]."*".$row[csf("style_ref_no")]."*".$row[csf("season")]."*".$row[csf("job_no")]."*".$row[csf("entry_form")]."*".$tna_data[$row[csf("po_break_down_id")]];
					$report_data[$ndate][$row[csf("booking_no")]][$row[csf("fabric_color_id")]]["kg"] +=$per_day_kg_book_qnty;
					$report_data[$ndate][$row[csf("booking_no")]][$row[csf("fabric_color_id")]]["yds"] +=$per_day_yds_book_qnty;
					$report_data[$ndate][$row[csf("booking_no")]][$row[csf("fabric_color_id")]]["finreq"] =$row[csf("fin_fab_qnty")];
					$report_data[$ndate][$row[csf("booking_no")]][$row[csf("fabric_color_id")]]["greyreq"] =$row[csf("grey_fab_qnty")];
					$report_data[$ndate][$row[csf("booking_no")]][$row[csf("fabric_color_id")]]["dayQty"] =$per_day_book_qnty;
					$report_data[$ndate][$row[csf("booking_no")]][$row[csf("fabric_color_id")]]["noDays"] =$num_of_days;
				}
			}
		}
	}
	unset($tns_info);

	if(!empty($booking_ids_arr)){
		$booking_ids = implode(",", array_filter($booking_ids_arr));
		$allBookCond = "";
		if($db_type==2 && count($booking_ids_arr)>999)
		{
			$booking_id_arr_chunk=array_chunk($booking_ids_arr,999) ;
			foreach($booking_id_arr_chunk as $chunk_arr)
			{
				$bookCond.=" booking_id in(".implode(",",$chunk_arr).") or ";
			}

			$allBookCond.=" and (".chop($bookCond,'or ').")";
		}
		else
		{
			$allBookCond=" and booking_id in($booking_ids)";
		}

		$sales_info_sql = "select id,job_no,booking_id,sales_booking_no from fabric_sales_order_mst where status_active=1 $allBookCond";
		$sales_info = sql_select($sales_info_sql);
		foreach ($sales_info as $fso_row) {
			$sales_info_arr[$fso_row[csf("sales_booking_no")]]["job_no"] = $fso_row[csf("job_no")];
			$sales_info_arr[$fso_row[csf("sales_booking_no")]]["id"] = $fso_row[csf("id")];
			$sales_ids[$fso_row[csf("id")]]=$fso_row[csf("id")];
		}

		if(!empty($sales_ids)){
			$fso_ids = implode(",", array_filter($sales_ids));
			$fsoCond = $all_fso_cond = "";
			if($db_type==2 && count($sales_ids)>999)
			{
				$fso_id_arr_chunk=array_chunk($sales_ids,999) ;
				foreach($fso_id_arr_chunk as $chunk_arr)
				{
					$fsoCond.=" c.po_breakdown_id in(".implode(",",$chunk_arr).") or ";
				}

				$all_fso_cond.=" and (".chop($fsoCond,'or ').")";
			}
			else
			{
				$all_fso_cond=" and c.po_breakdown_id in($fso_ids)";
			}

			$delivery_qnty_sql = sql_select("select e.booking_no, c.po_breakdown_id, d.color, b.uom, a.transaction_date, sum(c.quantity) delivery_qnty from inv_finish_fabric_issue_dtls b, order_wise_pro_details c, product_details_master d, inv_transaction a, inv_issue_master e where a.company_id=$company_name and b.id=c.dtls_id and c.prod_id=d.id and c.trans_id = a.id and e.id=a.mst_id $all_fso_cond and b.status_active=1 and c.entry_form=224 and c.status_active=1 and a.status_active=1 group by e.booking_no, c.po_breakdown_id,d.color, b.uom,a.transaction_date");
			//echo "select e.booking_no, c.po_breakdown_id, d.color, b.uom, a.transaction_date, sum(c.quantity) delivery_qnty from inv_finish_fabric_issue_dtls b, order_wise_pro_details c, product_details_master d, inv_transaction a, inv_issue_master e where a.company_id=$company_name and b.id=c.dtls_id and c.prod_id=d.id and c.trans_id = a.id and e.id=a.mst_id $all_fso_cond and b.status_active=1 and c.entry_form=224 and c.status_active=1 and a.status_active=1 group by e.booking_no, c.po_breakdown_id,d.color, b.uom,a.transaction_date";

			foreach ($delivery_qnty_sql as $row)
			{
				$date = date("d-m-Y",strtotime($row[csf("transaction_date")]));
				if($row[csf("uom")]==12){
					$kg_delivery_info[$row[csf("po_breakdown_id")]][$row[csf("color")]][$date] += $row[csf("delivery_qnty")];
				}

				if($row[csf("uom")]==27){
					$yds_delivery_info[$row[csf("po_breakdown_id")]][$row[csf("color")]][$date] += $row[csf("delivery_qnty")];
				}
			}
			unset($delivery_qnty_sql);
		}
	}
	
	
	foreach ($report_data as $date=>$booking_row) {
		$date=date('d-m-Y',$date);
		foreach ($booking_row as $booking_no=>$color_row) {
			foreach ($color_row as $color_id=>$row) {
				if($a%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$data = explode("*", $row["info"]);
				
				$summary_data[$data[0]][$data[1]]["booking_qnty_kg"] += $row["kg"];
				$summary_data[$data[0]][$data[1]]["booking_qnty_yds"] += $row["yds"];
				$summary_data[$data[0]][$data[1]]["delivery_qnty_kg"] +=$kg_delivery_info[$sales_info_arr[$booking_no]["id"]][$color_id][$date];
				$summary_data[$data[0]][$data[1]]["delivery_qnty_yds"] +=$yds_delivery_info[$sales_info_arr[$booking_no]["id"]][$color_id][$date];
			}
		}
	}
	//print_r($kg_delivery_info);
	ob_start();

	if(!empty($summary_data))
	{
		?>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="760" class="rpt_table" >
			<thead>
				<tr>
					<th width="100">LC Company</th>
					<th width="100">PO Buyer</th>
					<th width="100">Required Qnty(Kg)</th>
					<th width="100">Delivery Qnty(Kg)</th>
					<th width="80">Short/Excess qnty(Kg)</th>
					<th width="100">Required Qnty(Yds)</th>
					<th width="100">Delivery Qnty(Yds)</th>
					<th width="80">Short/Excess qnty(Yds)</th>
				</tr>
			</thead>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="760" class="rpt_table" id="tbl_list_search">
			<?
			$total_booking_qnty=$total_delivery_qnty=$total_balance_qnty=0;
			foreach ($summary_data as $company_id=>$comp_data) {
				foreach ($comp_data as $buyer_id => $summ_data) {
					?>
					<tr>
						<td width="100" align="center"><? echo $company_arr[$company_id];?></td>
						<td width="100" align="center"><? echo $buyer_arr[$buyer_id];?></td>
						<td width="100" align="right"><? echo number_format($summ_data["booking_qnty_kg"],2,".","");?></td>
						<td width="100" align="right"><? echo number_format($summ_data["delivery_qnty_kg"],2,".","");?></td>
						<td width="80" align="right"><? echo number_format($summ_data["booking_qnty_kg"]-$summ_data["delivery_qnty_kg"],2,".","");?></td>

						<td width="100" align="right"><? echo number_format($summ_data["booking_qnty_yds"],2,".","");?></td>
						<td width="100" align="right"><? echo number_format($summ_data["delivery_qnty_yds"],2,".","");?></td>
						<td width="80" align="right"><? echo number_format($summ_data["booking_qnty_yds"]-$summ_data["delivery_qnty_yds"],2,".","");?></td>
					</tr>
					<?
					$total_booking_qnty_kg += $summ_data["booking_qnty_kg"];
					$total_delivery_qnty_kg += $summ_data["delivery_qnty_kg"];
					$total_balance_qnty_kg += ($summ_data["booking_qnty_kg"]-$summ_data["delivery_qnty_kg"]);

					$total_booking_qnty_yds += $summ_data["booking_qnty_yds"];
					$total_delivery_qnty_yds += $summ_data["delivery_qnty_yds"];
					$total_balance_qnty_yds += ($summ_data["booking_qnty_yds"]-$summ_data["delivery_qnty_yds"]);
				}
			}
			?>
			<tr>
				<th width="200" align="right" colspan="2">Total=</th>
				<th width="100" align="right" id="total_booking_qnty"><? echo number_format($total_booking_qnty_kg,0,".","");?></th>
				<th width="100" align="right" id="total_delivery_qnty"><? echo number_format($total_delivery_qnty_kg,0,".","");?></th>
				<th width="80" align="right" id="total_balance_qnty"><? echo number_format($total_balance_qnty_kg,0,".","");?></th>
				<th width="100" align="right" id="total_booking_qnty"><? echo number_format($total_booking_qnty_yds,0,".","");?></th>
				<th width="100" align="right" id="total_delivery_qnty"><? echo number_format($total_delivery_qnty_yds,0,".","");?></th>
				<th width="80" align="right" id="total_balance_qnty"><? echo number_format($total_balance_qnty_yds,0,".","");?></th>
			</tr>
		</table><br />
		<?
	}

	?>
	<style type="text/css">
		.word_wrap_break{
			word-wrap: break-word;
			word-break: break-all;
		}
	</style>

	<fieldset style="width:1480px;">
		<table width="1440" cellspacing="0" cellpadding="0" border="0" rules="all" >
			<tr class="form_caption">
				<td colspan="17" align="center" style="border:none;font-size:16px; font-weight:bold"> <? echo $report_title; ?></td>
			</tr>
			<tr class="form_caption">
				<td colspan="17" align="center"><? echo $company_arr[$company_name]; ?></td>
			</tr>
		</table>
		<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1480" class="rpt_table" >
			<thead>
				<tr>
					<th width="30">SL</th>
					<th width="80">Deliverable Date</th>
					<th width="70">LC Company</th>
					<th width="100">PO Buyer</th>
					<th width="100">Job Number</th>
					<th width="100">Style Ref.</th>
					<th width="80">Season</th>
					<th width="110">Booking No</th>
					<th width="80">Booking Type</th>
					<th width="120">FSO</th>
					<th width="110">Fabric Color</th>
					<th width="80">Booking Qnty(Kg)</th>
					<th width="80">Delivery Qnty(Kg)</th>
					<th width="80">Balance(Kg)</th>
					<th width="80">Booking Qnty(Yds)</th>
					<th width="80">Delivery Qnty(Yds)</th>
					<th>Balance(Yds)</th>
				</tr>
			</thead>
		</table>
		<div style="width:1480px; overflow-y:scroll; max-height:350px;" id="scroll_body">
			<table cellspacing="0" cellpadding="0" border="1" rules="all" width="1460" class="rpt_table" id="tbl_list_dtls_search">
				<?
				$a=1;
				$kg_total_book_qnty = $kg_total_deliv_qnty = $kg_total_bal_qnty = $yds_total_book_qnty = $yds_total_deliv_qnty = $yds_total_bal_qnty = 0;
				ksort($report_data);
				foreach ($report_data as $date=>$booking_row) {
					$date=date('d-m-Y',$date);
					foreach ($booking_row as $booking_no=>$color_row) {
						foreach ($color_row as $color_id=>$row) {
							if($a%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$data = explode("*", $row["info"]);

							$kg_date_wise_delivery_qnty=number_format($kg_delivery_info[$sales_info_arr[$booking_no]["id"]][$color_id][$date],2,".","");
							$kg_balance=number_format(($row["kg"]-$kg_date_wise_delivery_qnty),2,".","");

							$yds_date_wise_delivery_qnty=number_format($yds_delivery_info[$sales_info_arr[$booking_no]["id"]][$color_id][$date],2,".","");
							$yds_balance = number_format(($row["yds"]-$yds_date_wise_delivery_qnty),2,".","");

							?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $a; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $a; ?>">
								<td width="30" align="center"><? echo $a;?></td>
								<td width="80" align="center"><p><? echo $date;?></p></td>
								<td width="70" align="center"><p><? echo $company_arr[$data[0]];?></p></td>
								<td width="100"><p><? echo $buyer_arr[$data[1]];?></p></td>
								<td width="100"><p><? echo $data[4];?></p></td>
								<td width="100"><p><? echo $data[2];?></p></td>
								<td width="80"><? echo $season_arr[$data[3]];?></td>
								<td width="110"><? echo $booking_no;?></td>
								<td width="80" align="center"><? echo $booking_type_arr[$data[5]];?></td>
								<td width="120" title="<? echo $sales_info_arr[$booking_no]["id"];?>"><? echo $sales_info_arr[$booking_no]["job_no"];?></td>
								<td width="110" title="<? echo $color_id.'='.$row["finreq"];?>"><p><? echo $color_arr[$color_id];?></p></td>
								<td width="80" align="right"><? echo number_format($row["kg"],2);?></td>
								<td width="80" align="right"><a href="##" onClick="openmypage_delivery('<? echo $data[0];?>','<? echo $sales_info_arr[$booking_no]["id"];?>','<? echo $color_id;?>','12','<? echo $date;?>')"><? echo ($kg_date_wise_delivery_qnty>0)?$kg_date_wise_delivery_qnty:"";?></a></td>
								<td width="80" align="right"><? echo $kg_balance;?></td>
								<td width="80" align="right"><? echo number_format($row["yds"],2);?></td>
								<td width="80" align="right"><a href="##" onClick="openmypage_delivery('<? echo $data[0];?>','<? echo $sales_info_arr[$booking_no]["id"];?>','<? echo $color_id;?>','27','<? echo $date;?>')"><? echo ($yds_date_wise_delivery_qnty>0)?$yds_date_wise_delivery_qnty:"";?></a></td>
								<td align="right"><? echo $yds_balance;?></td>
							</tr>
							<?
							$kg_total_book_qnty += $row["kg"];
							$kg_total_deliv_qnty += $kg_date_wise_delivery_qnty;
							$kg_total_bal_qnty += $row["kg"]-$kg_date_wise_delivery_qnty;

							$yds_total_book_qnty += $row["yds"];
							$yds_total_deliv_qnty += $yds_date_wise_delivery_qnty;
							$yds_total_bal_qnty += $row["yds"]-$yds_date_wise_delivery_qnty;
							$a++;
						}
					}
				}
				?>
				<tr>
					<th colspan="11" align="right">Total = </th>
					<th width="80" align="right" id="kg_total_book_qnty"><? echo number_format($kg_total_book_qnty,0,".","");?></th>
					<th width="80" align="right" id="kg_total_deliv_qnty"><? echo number_format($kg_total_deliv_qnty,0,".","");?></th>
					<th width="80" align="right" id="kg_total_bal_qnty"><? echo number_format($kg_total_bal_qnty,0,".","");?></th>
					<th width="80" align="right" id="yds_total_book_qnty"><? echo number_format($yds_total_book_qnty,0,".","");?></th>
					<th width="80" align="right" id="yds_total_deliv_qnty"><? echo number_format($yds_total_deliv_qnty,0,".","");?></th>
					<th align="right" title="yds_total_bal_qnty"><? echo number_format($yds_total_bal_qnty,0,".","");?></th>
				</tr>
			</table>
		</div>
		<?
		$html = ob_get_contents();
		ob_clean();
		foreach (glob("*.xls") as $filename) {
			@unlink($filename);
		}
		$name=time();
		$filename=$user_id."_".$name.".xls";
		$create_new_doc = fopen($filename, 'w');
		$is_created = fwrite($create_new_doc, $html);
		echo "$html####$filename";
		exit();
	}

	if($action=="delivery_popup")
	{
		echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
		extract($_REQUEST);

		$company_id = $company_id;
		$fso_id = $fso_id;
		$color_id = $color_id;
		$uom_id = $uom;
		$delivery_date = date("d-M-Y",strtotime($date));

		$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name");
		?>

		<fieldset style="width:965px; margin-left:3px;margin:auto;">
			<input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
			<div style="width:100%" id="report_container">
				<table border="1" class="rpt_table" rules="all" width="965" cellpadding="0" cellspacing="0" id="table_header">
					<caption>
						<b>Finish Fabrics Delivery Info</b>
					</caption>
					<thead>
						<th width="20">SL</th>
						<th width="65">Delivery Date</th>
						<th width="120">Transaction ID</th>
						<th width="110">Booking No</th>
						<th width="120">Sales Order No</th>
						<th width="110">Fabric Description</th>
						<th width="60">Batch No</th>
						<th width="60">Ext. No</th>
						<th width="60">Color</th>
						<th width="60">UOM</th>
						<th width="60">Delivery Qty.</th>
						<th>Remarks</th>
					</thead>
				</table>
				<div style="width:965px; overflow-y:scroll; max-height:250px;font-size:12px; overflow-x:hidden;" id="scroll_body">
					<table border="1" class="rpt_table" rules="all" width="945" cellpadding="0" cellspacing="0" id="table_body">
						<tbody>
							<?
							$sql_data="select a.id issue_id,a.issue_number,a.issue_date,b.batch_id,b.uom,b.remarks,sum(c.quantity) delivery_qnty,d.color color_id,d.product_name_details,e.job_no,e.sales_booking_no from inv_issue_master a, inv_finish_fabric_issue_dtls b,order_wise_pro_details c,product_details_master d, fabric_sales_order_mst e where a.id = b.mst_id and b.id=c.dtls_id and c.prod_id=d.id and c.po_breakdown_id = e.id and a.entry_form = 224 and b.status_active=1 and c.entry_form=224 and c.is_sales = 1 and c.status_active=1 and e.po_company_id=$companyID and c.po_breakdown_id=$fso_id and d.color=$color_id and b.uom=$uom_id and a.issue_date='$delivery_date' group by a.id,a.issue_number,a.issue_date,b.batch_id,b.uom,b.remarks,d.color, d.product_name_details,e.job_no,e.sales_booking_no";

							$source_array=sql_select($sql_data);
							foreach($source_array as $row)
							{
								$batch_id_arr[$row[csf('batch_id')]] =	$row[csf('batch_id')];
							}

							$batch_id_arr = array_filter($batch_id_arr);
							$batch_ids = implode(",", $batch_id_arr);
							if($batch_ids != "")
							{
								$sql_batch= sql_select("select a.id as batch_id, a.batch_no,a.extention_no from pro_batch_create_mst a where a.id in ($batch_ids) and a.status_active=1");
								foreach ($sql_batch as $val)
								{
									$batch_data_arr[$val[csf("batch_id")]]["batch_no"] = $val[csf("batch_no")];
									$batch_data_arr[$val[csf("batch_id")]]["extention_no"] = $val[csf("extention_no")];
								}
							}

							$i=1;
							$total_deli_qnty = 0;
							foreach($source_array as $row)
							{
								$issue_date = $row[csf("issue_date")];
								$issue_number = $row[csf("issue_number")];
								$booking_no = $row[csf("sales_booking_no")];
								$sales_order_no = $row[csf("job_no")];

								$batch_no = $batch_data_arr[$row[csf("batch_id")]]["batch_no"];
								$extention_no = $batch_data_arr[$row[csf("batch_id")]]["extention_no"];
								$color_id = $row[csf("color_id")];
								$fab_desc = $row[csf("product_name_details")];
								$uom = $row[csf("uom")];

								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								if($row[csf("delivery_qnty")]>0)
								{
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
										<td width="20"><? echo $i;?></td>
										<td width="65"><? echo change_date_format($issue_date); ?></td>
										<td width="120"><? echo $issue_number;?></td>
										<td width="110"><? echo $booking_no;?></td>
										<td width="120"><? echo $sales_order_no;?></td>
										<td width="110"><? echo $fab_desc;?></td>
										<td width="60"><p style="word-wrap: break-word;word-break: break-all;"><? echo $batch_no;?></p></td>
										<td width="60"><? echo $extention_no;?></td>
										<td width="60"><p style="word-wrap: break-word;word-break: break-all;"><? echo $color_arr[$color_id];?></p></td>
										<td width="60" align="center"><? echo $unit_of_measurement[$uom];?></td>
										<td width="60" align="right"><? echo number_format($row[csf("delivery_qnty")],2,".","");?></td>
										<td><? echo $row[csf("remarks")];?></td>
									</tr>
									<?
									$total_deli_qnty += $row[csf("delivery_qnty")];
									$i++;
								}
							}

							?>
						</tbody>
						<tfoot>
							<th colspan="10" width="785" align="right">Total=</th>
							<th width="60" align="right"><? echo number_format($total_deli_qnty,2,".","");?></th>
							<th>&nbsp;</th>
						</tfoot>
					</table>
				</div>
			</div>
		</fieldset>
		<script>setFilterGrid('table_body',-1,tableFilters);</script>
		<?
		exit();
	}
	?>