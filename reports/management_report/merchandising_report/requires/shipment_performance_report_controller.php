<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.fabrics.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
//--------------------------------------------------------------------------------------------------------------------
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if ($action=="load_drop_down_buyer")
{
	if($data!="")
	{
		echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in ($data) $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "");
	}
	else
	{
		echo create_drop_down( "cbo_buyer_name", 140, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
	}
	exit();
}

if($action=="job_no_popup")
{
	echo load_html_head_contents("Job Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>

		function js_set_value(str)
		{
			$("#hide_job_no").val(str);
			parent.emailwindow.hide();
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
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"><input type="hidden" name="hide_job_no" id="hide_job_no" value="" /></th> 					
                </thead>
                <tbody>
                	<tr class="general">
                        <td><? echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 ); ?></td>
                        <td>
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref");
							$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>
                        <td id="search_by_td">
                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />
                        </td>
                        <td align="center">
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_job_no_search_list_view', 'search_div', 'shipment_performance_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	$month_id=$data[5];
	//echo $month_id;
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$location_arr=return_library_array( "select id, location_name from lib_location",'id','location_name');
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_id_cond=" and buyer_name=$data[1]";

	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";
	if($search_by==2) $search_field="style_ref_no"; else $search_field="job_no";
	//$year="year(insert_date)";
	
	$year_field=""; $year_cond="";
	if($db_type==0)
	{
		$year_field="YEAR(insert_date) as year";
		if($year_id!=0) $year_cond=" and year(insert_date)=$year_id";
	}
	else if($db_type==2)
	{
		$year_field="to_char(insert_date,'YYYY') as year";
		if($year_id!=0) $year_cond=" and to_char(insert_date,'YYYY')=$year_id";
	}
	$arr=array (0=>$location_arr,1=>$buyer_arr);
	$sql= "select id, job_no, job_no_prefix_num, company_name, location_name, buyer_name, style_ref_no, $year_field from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond order by id DESC";

	echo create_list_view("tbl_list_search", "Location,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","600","240",0, $sql , "js_set_value", "job_no", "", 1, "location_name,buyer_name,0,0,0", $arr , "location_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','') ;
	exit();
} // Job Search end

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	//var data="action=report_generate&reporttype="+type+get_submitted_data_string('cbo_company_name*cbo_buyer_name*cbo_year*txt_job_no*txt_style_ref*txt_date_from*txt_date_to',"../../../")+'&report_title='+report_title;
	$report_type=str_replace("'","",$reporttype);
	$company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_year=str_replace("'","",$cbo_year);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_style_ref=str_replace("'","",$txt_style_ref);
	
	//*****txt_date_from*txt_date_to

	if($company_name!=0) $company_cond=" and a.company_name in ($company_name)"; else $company_cond="";
	if($txt_job_no!="") $job_cond=" and a.job_no='$txt_job_no'"; else $job_cond="";
	if($txt_style_ref!="") $style_cond=" and a.style_ref_no='$txt_style_ref'"; else $style_cond="";

	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";

	if($db_type==0)
	{
		if(trim($cbo_year)!=0) $year_cond=" and YEAR(a.insert_date)=$cbo_year"; else $year_cond="";
	}
	else if($db_type==2)
	{
		if(trim($cbo_year)!=0) $year_cond=" and to_char(a.insert_date,'YYYY')=$cbo_year"; else $year_cond="";
	}
	
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);

	$date_cond=''; $date_string="";
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		if($db_type==0)
		{
			$start_date=change_date_format($date_from,"yyyy-mm-dd","");
			$end_date=change_date_format($date_to,"yyyy-mm-dd","");
		}
		else if($db_type==2)
		{
			$start_date=change_date_format($date_from,"","",1);
			$end_date=change_date_format($date_to,"","",1);
		}
		$date_cond=" and c.ex_factory_date between '$start_date' and '$end_date'";
		$totExQtydate_cond=" and ex_factory_date <= '$end_date'";
		
		$date_string=change_date_format($start_date).' To '.change_date_format($end_date);
	}
	
	$companyArr=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$supplierArr=return_library_array( "select id,supplier_name from lib_supplier", "id", "supplier_name");
	$seasonArr=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name");
	$buyerArr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	ob_start();
	if($report_type==1)
	{
		$sql_mst="SELECT (sum(e.order_total)/sum(e.order_quantity)) as unit_price, a.company_name, a.buyer_name, a.style_ref_no, a.job_no_prefix_num, a.job_no, a.season_buyer_wise, a.gmts_item_id, a.order_uom, a.total_set_qnty as ratio, a.set_smv, b.id as po_id, b.po_number, b.po_quantity, b.pub_shipment_date, c.ex_factory_date as ex_factory_date, c.shiping_mode, c.foc_or_claim, d.delivery_company_id, d.source, sum(c.ex_factory_qnty) as outputQty
		
		from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c, pro_ex_factory_delivery_mst d, wo_po_color_size_breakdown e where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.delivery_mst_id=d.id and b.id=e.po_break_down_id and 
		 a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 
		
		$company_cond $buyer_id_cond $job_cond $style_cond $year_cond $date_cond group by a.company_name, a.buyer_name, a.style_ref_no, a.job_no_prefix_num, a.job_no, a.season_buyer_wise, a.gmts_item_id, a.job_quantity, a.order_uom, a.total_set_qnty, a.set_smv, b.id, b.po_number, b.po_quantity, b.pub_shipment_date, c.ex_factory_date, c.shiping_mode, c.foc_or_claim, d.delivery_company_id, d.source order by b.id, ex_factory_date ASC";
		
		//$year_cond and a.job_no='UG-19-00152' and d.entry_form=85
		//echo $sql_mst;
		$sql_mst_res=sql_select($sql_mst); $mst_data_arr=array(); $ex_data_arr=array(); $air_claim_arr=array(); $tot_rows=0; $poIds=''; $jobNos="";
		foreach($sql_mst_res as $row)
		{
			$tot_rows++;
			$poIds.=$row[csf("po_id")].",";
			$jobNos.="'".$row[csf("job_no")]."',";
			$mst_data_arr[$row[csf('po_id')]]['str']=$row[csf('company_name')].'__'.$row[csf('buyer_name')].'__'.$row[csf('style_ref_no')].'__'.$row[csf('job_no_prefix_num')].'__'.$row[csf('job_no')].'__'.$row[csf('season_buyer_wise')].'__'.$row[csf('gmts_item_id')].'__'.$row[csf('order_uom')].'__'.$row[csf('ratio')].'__'.$row[csf('po_number')].'__'.$row[csf('po_quantity')].'__'.$row[csf('unit_price')].'__'.$row[csf('pub_shipment_date')].'__'.$row[csf('set_smv')];
			
			$ex_data_arr[$row[csf('po_id')]][$row[csf('source')]][$row[csf('delivery_company_id')]]['manu_company']=$row[csf('delivery_company_id')];
			$ex_data_arr[$row[csf('po_id')]][$row[csf('source')]][$row[csf('delivery_company_id')]]['ex_factory_date'].=$row[csf('ex_factory_date')].',';
			$ex_data_arr[$row[csf('po_id')]][$row[csf('source')]][$row[csf('delivery_company_id')]]['ex_factory_qty']+=$row[csf('outputQty')];
			
			if($row[csf('shiping_mode')]==2 && $row[csf('foc_or_claim')]==2) $air_claim_arr[$row[csf('po_id')]][$row[csf('source')]][$row[csf('delivery_company_id')]]['airclaimex']+=$row[csf('outputQty')];
		}
		unset($sql_mst_res);
		//echo "<pre>";
		//print_r($air_claim_arr);
		
		$poIds=chop($poIds,',');
		$jobNos=chop($jobNos,',');
		$jobCount=count(array_unique(explode(",",$jobNos)));
		
		$jobNos=implode(",",array_unique(explode(",",$jobNos)));
		$budget_job_cond="";
		if($db_type==2 && $jobCount>1000)
		{
			$budget_job_cond=" and (";
			$jobNosArr=array_chunk(explode(",",$jobNos),999);
			foreach($jobNosArr as $jobs)
			{
				$jobs=implode(",",$jobs);
				$budget_job_cond.=" job_no in($jobs) or ";
			}
			$budget_job_cond=chop($budget_job_cond,'or ');
			$budget_job_cond.=")";
		}
		else
		{
			$budget_job_cond=" and job_no in ($jobNos)";
		}
		
		$poIds_cond=""; $bpoIds_cond=""; $lpoIds_cond=""; 
		if($db_type==2 && $tot_rows>1000)
		{
			$poIds_cond=" and (";
			$bpoIds_cond=" and (";
			$lpoIds_cond=" and (";
			$poIdsArr=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$poIds_cond.=" po_break_down_id in($ids) or ";
				$bpoIds_cond.=" b.po_break_down_id in($ids) or ";
				$lpoIds_cond.=" po_id in($ids) or ";
			}
				
			$poIds_cond=chop($poIds_cond,'or ');
			$poIds_cond.=")";
			
			$bpoIds_cond=chop($bpoIds_cond,'or ');
			$bpoIds_cond.=")";
			
			$lpoIds_cond=chop($lpoIds_cond,'or ');
			$lpoIds_cond.=")";
		}
		else
		{
			$poIds_cond=" and po_break_down_id in ($poIds)";
			$bpoIds_cond=" and b.po_break_down_id in ($poIds)";
			$lpoIds_cond=" and po_id in ($poIds)";
		}
		
		$ex_qnty = "select po_break_down_id, ex_factory_qnty, shiping_status from pro_ex_factory_mst where is_deleted=0 and status_active=1 $poIds_cond $totExQtydate_cond";
		$exSQLresult = sql_select($ex_qnty);
		$exQtyArr = array();
		foreach ($exSQLresult as $val) {
			$exQtyArr[$val[csf('po_break_down_id')]]['ship']+=$val[csf('ex_factory_qnty')];
			$exQtyArr[$val[csf('po_break_down_id')]]['shipstatus']=$val[csf('shiping_status')];
		}
		unset($exSQLresult);
		
		$item_smv="select job_no, gmts_item_id, smv_pcs from wo_po_details_mas_set_details where 1=1 $budget_job_cond";
		
		$item_smv_res=sql_select($item_smv); $item_smv_arr=array();
		foreach($item_smv_res as $srow)
		{
			$item_smv_arr[$srow[csf('job_no')]][$srow[csf('gmts_item_id')]]=$srow[csf('smv_pcs')];
		}
		unset($item_smv_res);
		
		$budget_sql="select id, emb_name from wo_pre_cost_embe_cost_dtls where status_active=1 and is_deleted=0 $budget_job_cond";
		$budget_sql_res=sql_select($budget_sql); $budget_arr=array();
		
		foreach($budget_sql_res as $brow)
		{
			$budget_arr[$brow[csf('id')]]['emb_name']=$brow[csf('emb_name')];
		}
		unset($budget_sql_res);
		
		$polyoutput_data_arr=array();
		$poly_sql="select po_break_down_id, item_number_id, production_quantity from pro_garments_production_mst where production_type='11' and status_active=1 and is_deleted=0 $poIds_cond";
		$poly_sql_data=sql_select($poly_sql);
		foreach($poly_sql_data as $prow)
		{
			$polyoutput_data_arr[$prow[csf('po_break_down_id')]][$prow[csf('item_number_id')]]['polyoutput']+=$prow[csf('production_quantity')];
		}
		unset($poly_sql_data);
		
		$booking_sql="select a.booking_type, a.is_short, a.entry_form, a.item_category, a.pay_mode, a.short_booking_type, b.pre_cost_fabric_cost_dtls_id as pre_cost_id, b.process, b.po_break_down_id, b.amount from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type in (1,2,3,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bpoIds_cond";
		$booking_sql_res=sql_select($booking_sql); $booking_arr=array();
		foreach($booking_sql_res as $rowb)
		{
			$emb_name=$budget_arr[$rowb[csf('pre_cost_id')]]['emb_name'];
			$bookingType=0;
			if($rowb[csf('booking_type')]==1 && $rowb[csf('is_short')]==2)
			{
				if($rowb[csf('pay_mode')]!=2) $bookingType=1; //Fabric Local
				else if($rowb[csf('pay_mode')]==2) $bookingType=9; //Fabric Import
			}
			else if($rowb[csf('booking_type')]==1 && $rowb[csf('is_short')]==1 && $rowb[csf('short_booking_type')]==1)
			{
				if($rowb[csf('pay_mode')]!=2) $bookingType=1; //Fabric Local
				else if($rowb[csf('pay_mode')]==2) $bookingType=9; //Fabric Import
			}
			if($rowb[csf('booking_type')]==2) $bookingType=2; //Trims
			if($rowb[csf('process')]==35) $bookingType=3; //Aop
			if($rowb[csf('booking_type')]==6)
			{
				if($emb_name==1) $bookingType=6; //Printing
				else if($emb_name==2) $bookingType=7; //EMBROIDERY
				else $bookingType=8; //Others
			}
			$booking_arr[$rowb[csf('po_break_down_id')]][$bookingType]['amt']+=$rowb[csf('amount')];
		}
		unset($booking_sql_res);
		
		$lab_sql="select po_id, amount from wo_labtest_dtls where status_active=1 and is_deleted=0 $lpoIds_cond ";
		$lab_sql_res=sql_select($lab_sql); $lab_arr=array();
		foreach($lab_sql_res as $rowl)
		{
			$lab_arr[$rowl[csf('po_id')]]['amt']+=$rowl[csf('amount')];
		}
		unset($lab_sql_res);
		
		$buyer_sql="select mst_id, com_cost_imp_fabric, short_realization_per, effective_date as effective_date from lib_comm_import_fabric where status_active=1 and is_deleted=0  order by effective_date asc";
		$buyer_sql_res=sql_select($buyer_sql); $buyerData_arr=array();
		foreach($buyer_sql_res as $info)
		{
			$buyerData_arr[$info[csf('mst_id')]]['comm_per']=$info[csf('com_cost_imp_fabric')];
			$buyerData_arr[$info[csf('mst_id')]]['short_per']=$info[csf('short_realization_per')];
		}
		unset($buyer_sql_res);
		?>
		<div>
		<fieldset style="width:100%;">
            <table width="3380">
            	<!--<tr class="form_caption">
                	<td colspan="40" align="center"><strong><?// echo $companyArr[$company_name]; ?></strong></td>
                </tr>-->
                <tr class="form_caption">
                	<td colspan="40" align="center"><strong><? echo $report_title.' '.$date_string; ?></strong></td>
                </tr>
            </table>
            <table class="rpt_table" width="3380" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <tr>
                        <th width="30" rowspan="2">SL</th>
                        <th colspan="13">Style Info.</th>
                        <th colspan="4">Export</th>
                        <th colspan="12">Material Cost</th>
                        <th width="100" rowspan="2" title="Fabric Amt Local+Fabric Import+Comm. Cost Import Value+Acc Amt+Aop Value+Printing Amt+Embodary Amt+Others Amt+Short Realization Value">Total Cost</th>
                        <th colspan="8">Production</th>
                        <th rowspan="2" title="Value (Net)-Total Cost">CM</th>
                    </tr>
                    <tr style="font-size:11px">
                    	<th width="100">LC Unit</th>
                        <th width="100">Delivery Unit</th>
                        <th width="60">Year</th>
                        <th width="70">Month</th>
                        <th width="90">Buyer</th>
                        <th width="100">Style Ref.</th>
                        <th width="100">Job No</th>
                        <th width="80">Season</th>
                        <th width="110">Gmts. Item</th>
                        <th width="100">Po No</th>
                        <th width="70">Ship Date</th>
                        <th width="70">EX-Factory Date</th>
                        <th width="80">Order Qty (Pcs)</th>
                        
                        <th width="80">Shipment (Pcs)</th>
                        <th width="80">Total Shipment (Pcs)</th>
                        <th width="80">Fob / Pcs</th>
                        <th width="80" title="Total Shipment (Pcs)*Fob / Pcs">Value (Net)</th>
                        
                        <th width="80">Fabric (WG/Local)</th>
                        <th width="80">Fabric Import</th>
                        <th width="80" title="Fabric Import*(Comm. Cost Per Import for Fabric/100)">Com. Cost for Imported Fabric</th>
                        <th width="80">Accessories</th>
                        <th width="80">Aop</th>
                        <th width="80">Printing</th>
                        <th width="80">Embroidery</th>
                        <th width="80">Other</th>
                        <th width="80">Air (Pcs)</th>
                        <th width="80">Air Freight Value (Claim)</th>
                        <th width="70">Short Realization %</th>
                        <th width="80" title="Value (Net)*(Short Realization % /100)">Short Realization</th>
                        
                        <th width="80">Production (Pcs)</th>
                        <th width="100">Spend Hour</th>
                        <th width="90" title="Production Hour=(SMV*Poly Qty)/60">Production Hour</th>
                        <th width="70">OH Rate</th>
                        <th width="70">SubCon Rate</th>
                        <th width="80">SubCon OH / Pcs</th>
                        <th width="70">OH Cost</th>
                        <th width="70">SubCon Cost</th>
                    </tr>
                </thead>
            </table>
            <div style="width:3380px; max-height:400px; overflow-y:scroll" id="scroll_body">
            <table class="rpt_table" width="3360" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
            <?
    		$i=1;
            foreach($ex_data_arr as $po_id=>$porow)
            {
				foreach($porow as $source=>$sourcerow)
				{
					foreach($sourcerow as $manuf_unit=>$manufrow)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$exdata=explode("__",$mst_data_arr[$po_id]['str']);
						$company_name=$buyer_name=$style_ref_no=$job_no_prefix_num=$job_no=$season_buyer_wise=$gmts_item_id=$order_uom=$po_number=$pub_shipment_date=""; 
						$ratio=$po_quantity=$unit_price=$outputQty=$set_smv=0;
						
						$company_name=$exdata[0];
						$buyer_name=$exdata[1];
						$style_ref_no=$exdata[2];
						$job_no_prefix_num=$exdata[3];
						$job_no=$exdata[4];
						$season_buyer_wise=$exdata[5];
						$gmts_item_id=$exdata[6];
						$order_uom=$exdata[7];
						$ratio=$exdata[8];
						$po_number=$exdata[9];
						$po_quantity=$exdata[10];
						$unit_price=$exdata[11];
						$pub_shipment_date=$exdata[12];
						$set_smv=$exdata[13];
						
						$manu_company='';
						if($source==1) $manu_company=$companyArr[$manuf_unit]; else if($source==3) $manu_company=$supplierArr[$manuf_unit];

						$ex_factory_date=''; $gmts_item=""; $strExfactory_date="";
						$exFactoryDate=array_filter(array_unique(explode(",",$manufrow['ex_factory_date'])));
						$maxexFactoryDate=max($exFactoryDate);
						foreach($exFactoryDate as $efdate)
						{
							if($strExfactory_date=="") $strExfactory_date=change_date_format($efdate); else $strExfactory_date.=', '.change_date_format($efdate);
						}
						
						$ex_factory_date=change_date_format($maxexFactoryDate);
						$exex_factory_date=explode("-",$ex_factory_date);
						$ex_gmts_item=explode(",",$gmts_item_id);
						
						$productionHour=0; $titel_prod_hour="";
						foreach($ex_gmts_item as $item_id)
						{
							$set_smv=0;
							$set_smv=$item_smv_arr[$job_no][$item_id];
							if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=', '.$garments_item[$item_id];
							$outputQty+=$polyoutput_data_arr[$po_id][$item_id]['polyoutput'];
							$productionHour+=($set_smv*$polyoutput_data_arr[$po_id][$item_id]['polyoutput'])/60;
							if($titel_prod_hour=="") $titel_prod_hour=$garments_item[$item_id].'=('.$set_smv.'*'.$polyoutput_data_arr[$po_id][$item_id]['polyoutput'].')/60';
							else $titel_prod_hour.=' : '.$garments_item[$item_id].'= ('.$set_smv.'*'.$polyoutput_data_arr[$po_id][$item_id]['polyoutput'].')/60';
						}
						
						$poQtyPcs=$exFactoryQty=$fobPcs=$exFactoryValue=$exQtyPer=$rowExFactoryQty=$shipstatus=0;
						$poQtyPcs=$po_quantity*$ratio;
						$exFactoryQty=$manufrow['ex_factory_qty'];
						$rowExFactoryQty=$exQtyArr[$po_id]['ship'];
						$shipstatus=$exQtyArr[$po_id]['shipstatus'];
						$fobPcs=$unit_price/$ratio;
						$exFactoryValue=$rowExFactoryQty*$fobPcs;
						
						$calQty=0;
						if($rowExFactoryQty>=$poQtyPcs) $calQty=$poQtyPcs;
						if($rowExFactoryQty<$poQtyPcs && $shipstatus==2) $calQty=$rowExFactoryQty;
						if($rowExFactoryQty<$poQtyPcs && $shipstatus==3) $calQty=$poQtyPcs;
						
						$exQtyPer=$calQty/$poQtyPcs;
						
						$commCostPerImport=$shortRealization_per=$shortRealization=0;
						$commCostPerImport=$buyerData_arr[$buyer_name]['comm_per'];
						$shortRealization_per=$buyerData_arr[$buyer_name]['short_per'];
						$shortRealization=$exFactoryValue*($shortRealization_per/100);
						
						$fabricAmtLocal=$fabricAmtImport=$commCostImportAmt=$accAmt=$aopAmt=$printingAmt=$embodaryAmt=$othersAmt=$airexFactory=$airClaimexFactory=0;
						$fabricAmtLocal=$booking_arr[$po_id][1]['amt']*$exQtyPer;
						$fabricAmtImport=$booking_arr[$po_id][9]['amt']*$exQtyPer;
						
						$commCostImportAmt=$fabricAmtImport*($commCostPerImport/100);
						
						$accAmt=$booking_arr[$po_id][2]['amt']*$exQtyPer;
						$aopAmt=$booking_arr[$po_id][3]['amt']*$exQtyPer;
						$printingAmt=$booking_arr[$po_id][6]['amt']*$exQtyPer;
						$embodaryAmt=$booking_arr[$po_id][7]['amt']*$exQtyPer;
						$othersAmt=($booking_arr[$po_id][8]['amt']+$lab_arr[$po_id]['amt'])*$exQtyPer;
						
						$airexFactory=$air_claim_arr[$po_id][$source][$manuf_unit]['airclaimex'];//$air_arr[$po_id]['airex'];
						$airClaimexFactory=$airexFactory*$fobPcs;
						
						$rowTtlCost=$rowCm=0;
						$rowTtlCost=$fabricAmtLocal+$fabricAmtImport+$commCostImportAmt+$accAmt+$aopAmt+$printingAmt+$embodaryAmt+$othersAmt+$shortRealization;
						$rowCm=$exFactoryValue-$rowTtlCost;
						
						if($rowCm<0) $cmColor="red"; else $cmColor="";
						
						?>
						 <tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" style="font-size:11px">
							<td width="30" align="center"><?=$i; ?></td>
							<td width="100" style="word-break:break-all"><?=$companyArr[$company_name]; ?></td>
							<td width="100" style="word-break:break-all"><?=$manu_company; ?></td>
							<td width="60" align="center"><?=$exex_factory_date[2]; ?></td>
							<td width="70"><?=$months_short[($exex_factory_date[1]*1)]; ?></td>
							<td width="90" style="word-break:break-all"><?=$buyerArr[$buyer_name]; ?></td>
							<td width="100" style="word-break:break-all"><?=$style_ref_no; ?></td>
							<td width="100"><?=$job_no; ?></td>
							<td width="80" style="word-break:break-all"><?=$seasonArr[$season_buyer_wise]; ?></td>
							<td width="110" style="word-break:break-all"><?=$gmts_item; ?></td>
							
							<td width="100" style="word-break:break-all"><?=$po_number; ?></td>
							<td width="70"><?=change_date_format($pub_shipment_date); ?></td>
                            <td width="70" style="word-break:break-all"><?=$strExfactory_date; ?></td>
							<td width="80" align="right"><?=number_format($poQtyPcs); ?></td>
							<td width="80" align="right" title="<?=number_format($exQtyPer,4); ?>"><?=number_format($exFactoryQty); ?></td>
                            <td width="80" align="right" title="<?=number_format($rowExFactoryQty,4); ?>"><?=number_format($rowExFactoryQty); ?></td>
							<td width="80" align="right"><?=number_format($fobPcs,4); ?></td>
							<td width="80" align="right" title="<?=$rowExFactoryQty.'*'.$fobPcs; ?>"><?=number_format($exFactoryValue,4); ?></td>
							<td width="80" align="right"><? if($fabricAmtLocal!=0) echo number_format($fabricAmtLocal,4); else echo""; ?></td>
							<td width="80" align="right"><? if($fabricAmtImport!=0) echo number_format($fabricAmtImport,4); else echo""; ?></td>
							<td width="80" align="right" title="<?='%: '.$commCostPerImport; ?>"><? if($commCostImportAmt!=0) echo number_format($commCostImportAmt,4); else echo""; ?></td>
							<td width="80" align="right"><? if($accAmt!=0) echo number_format($accAmt,4); else echo""; ?></td>
							<td width="80" align="right"><? if($aopAmt!=0) echo number_format($aopAmt,4); else echo""; ?></td>
							<td width="80" align="right"><? if($printingAmt!=0) echo number_format($printingAmt,4); else echo""; ?></td>
							<td width="80" align="right"><? if($embodaryAmt!=0) echo number_format($embodaryAmt,4); else echo""; ?></td>
							<td width="80" align="right"><? if($othersAmt!=0) echo number_format($othersAmt,4); else echo""; ?></td>
							<td width="80" align="right"><? if($airexFactory!=0) echo number_format($airexFactory,4); else echo""; ?></td>
							<td width="80" align="right"><? if($airClaimexFactory!=0) echo number_format($airClaimexFactory,4); else echo""; ?></td>
							<td width="70" align="right"><? if($shortRealization_per!=0) echo number_format($shortRealization_per,4); else echo""; ?></td>
							<td width="80" align="right"><? if($shortRealization!=0) echo number_format($shortRealization,4); else echo""; ?></td>
							<td width="100" align="right"><? if($rowTtlCost!=0) echo number_format($rowTtlCost,4); else echo""; ?></td>
							
                            <td width="80" align="right"><? if($outputQty!=0) echo number_format($outputQty); else echo""; ?></td>
							<td width="100" align="right">&nbsp;</td>
							<td width="90" align="right" title="<?=$titel_prod_hour; ?>"><? if($productionHour!=0) echo number_format($productionHour,4); else echo""; ?></td>
							<td width="70" align="right">&nbsp;</td>
							<td width="70" align="right">&nbsp;</td>
							<td width="80" align="right">&nbsp;</td>
							<td width="70" align="right">&nbsp;</td>
							<td width="70" align="right">&nbsp;</td>
							<td align="right" style="background-color:<?=$cmColor; ?>"><? if($rowCm!=0) echo number_format($rowCm,4); else echo""; ?></td>
						  </tr>
						<?
						$i++;
						$gpoQtyPcs+=$poQtyPcs;
						$gExQtyPcs+=$exFactoryQty;
						$growExFactoryQtyPcs+=$rowExFactoryQty;
						$gexFactoryValue+=$exFactoryValue;

						$gfabricAmtLocal+=$fabricAmtLocal;
						$gfabricAmtImport+=$fabricAmtImport;
						$gcommCostImportAmt+=$commCostImportAmt;
						$grandAccAmt+=$accAmt;
						$grandAopAmt+=$aopAmt;
						$grandPrintingAmt+=$printingAmt;
						$grandEmbAmt+=$embodaryAmt;
						$grandOthersAmt+=$othersAmt;
						
						$grandAirexFactory+=$airexFactory;
						$grandAirClaimexFactory+=$airClaimexFactory;
						$gshortRealization+=$shortRealization;
						$grandTtlCost+=$rowTtlCost;
						$gproductionHour+=$productionHour;
						$grandOutputQty+=$outputQty;
						$grandCm+=$rowCm;
					}
				}
            }
            ?>
        </table>
        </div>
            <table class="tbl_bottom" width="3380" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <tr>
                        <td width="30">&nbsp;</td>
                    	<td width="100">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="60">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="90">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="80">&nbsp;</td>
                        <td width="110">&nbsp;</td>
                        <td width="100">Total:</td>
                        <td width="70">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="80" id="td_poQty"><?=number_format($gpoQtyPcs); ?></td>
                        
                        <td width="80" id="td_shipQty"><?=number_format($gExQtyPcs); ?></td>
                        <td width="80" id="td_totshipQty"><?=number_format($growExFactoryQtyPcs); ?></td>
                        <td width="80">&nbsp;</td>
                        <td width="80" id="td_shipValue"><?=number_format($gexFactoryValue,4); ?></td>
                        
                        <td width="80" id="td_fabricLocal"><?=number_format($gfabricAmtLocal,4); ?></td>
                        <td width="80" id="td_fabricImport"><?=number_format($gfabricAmtImport,4); ?></td>
                        <td width="80" id="td_commImportValue"><?=number_format($gcommCostImportAmt,4); ?></td>
                        <td width="80" id="td_accAmt"><?=number_format($grandAccAmt,4); ?></td>
                        <td width="80" id="td_aopAmt"><?=number_format($grandAopAmt,4); ?></td>
                        <td width="80" id="td_printAmt"><?=number_format($grandPrintingAmt,4); ?></td>
                        <td width="80" id="td_embAmt"><?=number_format($grandEmbAmt,4); ?></td>
                        <td width="80" id="td_otherAmt"><?=number_format($grandOthersAmt,4); ?></td>
                        <td width="80" id="td_airPcs"><?=number_format($grandAirexFactory); ?></td>
                        <td width="80" id="td_airAmt"><?=number_format($grandAirClaimexFactory,4); ?></td>
                        <td width="70">&nbsp;</td>
                        <td width="80" id="td_shortValue"><?=number_format($gshortRealization,4); ?></td>
                        <td width="100" id="td_ttlCost"><?=number_format($grandTtlCost,4); ?></td>
                        <td width="80" id="td_prodPcs"><?=number_format($grandOutputQty); ?></td>
                        <td width="100">&nbsp;</td>
                        <td width="90" id="td_prodHour"><?=number_format($gproductionHour,4); ?></td>
                        <td width="70">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="80">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td id="td_cm"><?=number_format($grandCm,4); ?></td>
                    </tr>
                </thead>
            </table>
		</fieldset>
		</div>
		<?
	} //1st button end
	

	$html = ob_get_contents();
	ob_clean();
	//$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename";
	exit();
}
?>