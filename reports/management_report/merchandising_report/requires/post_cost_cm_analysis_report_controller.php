<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.commisions.php');
require_once('../../../../includes/class4/class.trims.php');
require_once('../../../../includes/class4/class.fabrics.php');
require_once('../../../../includes/class4/class.yarns.php');
require_once('../../../../includes/class4/class.conversions.php');
require_once('../../../../includes/class4/class.others.php');
require_once('../../../../includes/class4/class.emblishments.php');
require_once('../../../../includes/class4/class.commercials.php');
require_once('../../../../includes/class4/class.washes.php');


$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];

/*$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$company_short_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name"  );
$item_library=return_library_array( "select id,item_name from  lib_item_group", "id", "item_name"  );
$supplier_library=return_library_array( "select id,supplier_name from  lib_supplier", "id", "supplier_name"  );
$team_leader_library=return_library_array( "select id, team_leader_name from lib_marketing_team", "id", "team_leader_name");
$country_name_arr=return_library_array( "select id, country_name   from lib_country  where status_active=1 and is_deleted =0",'id','country_name');*/



//if($db_type==2) $select_date=" to_char(a.insert_date,'YYYY')";
//else if ($db_type==0) $select_date=" year(a.insert_date)";

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond group by buy.id, buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0);
	exit();
}

if ($action=="load_variable_settings")
{
	echo "$('#cbo_date_type').val(0);\n";
	$sql_result = sql_select("select report_date_catagory from variable_order_tracking where company_name='$data' and variable_list in (42) order by id");
 	foreach($sql_result as $result)
	{
		echo "$('#cbo_date_type').val(".$result[csf("report_date_catagory")].");\n";
		echo "search_by(".$result[csf("report_date_catagory")].",1);\n";
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
			var splitData = str.split("_");
			//alert (splitData[1]);
			$("#hide_job_id").val(splitData[0]); 
			$("#hide_job_no").val(splitData[1]); 
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
                    <th><input type="reset" name="button" class="formbutton" value="Reset" style="width:100px;" onClick="reset_form('styleRef_form','search_div','','','','');"></th> 					<input type="hidden" name="hide_job_id" id="hide_job_id" value="" />
                    <input type="hidden" name="hide_job_no" id="hide_job_no" value="" />
                </thead>
                <tbody>
                	<tr class="general">
                        <td><? echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID  $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",$buyer_name,"",0 ); ?></td>                 
                        <td>	
                    	<?
                       		$search_by_arr=array(1=>"Job No",2=>"Style Ref",3=>"Order No");
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../') ";							
							echo create_drop_down( "cbo_search_by", 130, $search_by_arr,"",0, "--Select--", "",$dd,0 );
						?>
                        </td>     
                        <td id="search_by_td"><input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" value="<? echo $txt_job_no;?>" /></td> 	
                        <td><input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>'+'**'+'<? echo $type; ?>', 'create_job_no_search_list_view', 'search_div', 'post_cost_cm_analysis_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" /></td>
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
	$type_id=$data[6];
	//echo $type_id;
	//echo $month_id;
	//echo $data[1];
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_id_cond=" and a.buyer_name=$data[1]";
	//echo $buyer_id_cond;
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";
	if($search_by==1) $search_field=" a.job_no";
	else if($search_by==2) $search_field=" a.style_ref_no";
	else $search_field="b.po_number";
	//$year="year(insert_date)";
	if($db_type==0) $year_field="YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(a.insert_date,'YYYY') as year";
	else $year_field="";
	
	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(a.insert_date)=$year_id"; else $year_cond="";	
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(a.insert_date,'YYYY')";
		if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";	
	}
	//if($month_id!=0) $month_cond=" and month(insert_date)=$month_id"; else $month_cond="";
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	// $sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_field from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond  order by job_no";
	
	if($type_id==1 && $search_by!=3)
	{
		  $sql= "select a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name,a.style_ref_no, $year_field from wo_po_details_master a where a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond  order by a.id DESC";
		echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","600","240",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','') ;
		exit(); 
	}
	else
	{
		  $sql= "select a.id, a.job_no, a.job_no_prefix_num, a.company_name, a.buyer_name,a.style_ref_no, $year_field,b.id,b.po_number from wo_po_details_master a,wo_po_break_down b where  a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond  order by a.id DESC";
		echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No,PO No", "120,130,80,60,100","700","240",0, $sql , "js_set_value", "id,po_number", "", 1, "company_name,buyer_name,0,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no,po_number", "",'','0,0,0,0,0,0','') ;
		exit(); 
	}
} // Job Search end

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$report_title=str_replace("'","",$report_title);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$year_id=str_replace("'","",$cbo_year);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_job_id=str_replace("'","",$txt_job_id);
	$date_type=str_replace("'","",$cbo_date_type);
	$start_date=str_replace("'","",$txt_date_from);
	$end_date=str_replace("'","",$txt_date_to);
	
	if($cbo_company_name==0) $company_name_cond=""; else $company_name_cond=" and a.company_name='$cbo_company_name' ";
	if($cbo_company_name==0) $company_name_cond2=""; else $company_name_cond2=" and company_id='$cbo_company_name' ";

	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
	
	$job_style_cond="";
	if(trim(str_replace("'","",$txt_job_no))!="")
	{
		if(str_replace("'","",$txt_job_id)!="") $job_style_cond=" and a.id in(".str_replace("'","",$txt_job_id).")";
		else $job_style_cond=" and a.job_no_prefix_num = '".trim(str_replace("'","",$txt_job_no))."'";
	}
	
	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(a.insert_date)=$year_id"; else $year_cond="";	
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(a.insert_date,'YYYY')";
		if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";	
	}
	$date_search_cond=""; $prev_date_cond=""; $finishing_po_cond="";
	if(str_replace("'","",$cbo_date_type)==1){
		if ($start_date=="" && $end_date=="") $date_search_cond=""; else $date_search_cond="and c.ex_factory_date between '$start_date' and '$end_date'";	
		if ($start_date=="") $prev_date_cond=""; else $prev_date_cond="and ex_factory_date<'$start_date'";
	}
	if(str_replace("'","",$cbo_date_type)==2){
		if ($start_date=="" && $end_date=="") $date_search_cond2=""; else $date_search_cond2="and production_date between '$start_date' and '$end_date'";
		$finishing_data=sql_select("SELECT po_break_down_id from pro_garments_production_mst where status_active=1 and is_deleted=0 $company_name_cond2  $date_search_cond2 group by po_break_down_id");
		$pack_finishing_po_arr=array();
		foreach($finishing_data as $row){
			$pack_finishing_po_arr[$row[csf('po_break_down_id')]]=$row[csf('po_break_down_id')];
		}
		if(count($pack_finishing_po_arr)>0){
			$finishing_po_cond=where_con_using_array($pack_finishing_po_arr,0,'b.id');
		}

	}	
	
	ob_start();
	
	$sql="select a.id, a.job_no_prefix_num as job_prefix, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.total_set_qnty as ratio, a.ship_mode, b.id as po_id, b.po_number,  b.pub_shipment_date, b.po_quantity, b.plan_cut, b.unit_price, b.po_total_price, max(c.ex_factory_date) as factory_date, max(c.shiping_status) as shiping_status, sum(CASE WHEN c.entry_form!=85 THEN c.ex_factory_qnty ELSE 0 END) as exf_qnty, sum(CASE WHEN c.entry_form=85 THEN c.ex_factory_qnty ELSE 0 END) as exf_ret_qnty from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $company_name_cond $year_cond $buyer_id_cond $job_style_cond $date_search_cond $finishing_po_cond group by a.id, a.job_no_prefix_num, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, a.total_set_qnty, a.ship_mode, b.id, b.po_number, b.pub_shipment_date, b.po_quantity, b.plan_cut, b.unit_price, b.po_total_price order  by a.id ASC";
	//echo $sql; die;
	$sql_po_result=sql_select($sql);
	
	$poIds=""; $jobId=""; $tot_rows=0; 
	$job_data_arr=array(); $poWiseJobArr=array(); $shipmentDataArr=array();
	foreach($sql_po_result as $row)
	{
		$tot_rows++;
		$poIds.=$row[csf("po_id")].",";
		if($jobId=="") $jobId="'".$row[csf('id')]."'"; else $jobId.=","."'".$row[csf('id')]."'";
		$job_no=$row[csf('job_no')];
		$order_qty_pcs=0;
		
		$order_qty_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
		
		$job_data_arr[$job_no]['buyer']=$row[csf('buyer_name')];
		$job_data_arr[$job_no]['style_ref']=$row[csf('style_ref_no')];
		$job_data_arr[$job_no]['poNo'].=$row[csf('po_number')].'___';
		$job_data_arr[$job_no]['jobQtyPcs']+=$order_qty_pcs;
		$job_data_arr[$job_no]['jobQty']+=$row[csf('po_quantity')];
		$job_data_arr[$job_no]['jobValue']+=$row[csf('po_total_price')];
		$job_data_arr[$job_no]['ship_mode']=$row[csf('ship_mode')];
		
		$poWiseJobArr[$row[csf("po_id")]]['job']=$job_no;
		
		$ship_qty=0;
		$ship_qty=$row[csf('exf_qnty')]-$row[csf('exf_ret_qnty')];
		
		$shipmentDataArr[$row[csf('job_no')]]['ship_date']=$row[csf('factory_date')];
		$shipmentDataArr[$row[csf('job_no')]]['shiping_status']=$row[csf('shiping_status')];
		$shipmentDataArr[$row[csf('job_no')]]['qty']+=$ship_qty;
	}
	unset($sql_po_result);
	
	$jobIdAll=implode(",",array_unique(array_filter(explode(",",$jobId)))); 
//echo $poIds; die;
	$poIds=chop($poIds,',');
	$all_po_id=implode(",",$poIds); $poIdIssueTransCond=""; $poIdBookingCond=""; $poIdLabCond=""; $prevPoIdExCond=""; $poIdLcScCond=""; $poIdUpDisCond=""; $shipStatusPoIdCond=""; $sewingPoIdCond="";
	if($all_po_id=="") $all_po_id=$poIds;
	//echo $all_po_id; die;
	if($db_type==2 && $tot_rows>1000)
	{
		$poIdIssueTransCond=" and (";
		$poIdBookingCond=" and (";
		$poIdLabCond=" and (";
		$prevPoIdExCond=" and (";
		$poIdLcScCond=" and (";
		$poIdUpDisCond=" and (";
		$shipStatusPoIdCond=" and (";
		$sewingPoIdCond=" and (";
		
		$poIdsArr=array_chunk(explode(",",$poIds),999);
		foreach($poIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$poIdIssueTransCond.=" b.po_breakdown_id in($ids) or";
			$poIdBookingCond.=" b.po_break_down_id in($ids) or"; 
			$poIdLabCond.=" po_id in($ids) or";
			$prevPoIdExCond.=" po_break_down_id in($ids) or";
			$poIdLcScCond.=" b.wo_po_break_down_id in($ids) or";
			$poIdUpDisCond.=" b.po_breakdown_id in($ids) or";
			$shipStatusPoIdCond.=" id in($ids) or";
			$sewingPoIdCond.=" po_break_down_id in($ids) or";
		}
		$poIdIssueTransCond=chop($poIdIssueTransCond,'or ');
		$poIdIssueTransCond.=")";
		
		$poIdBookingCond=chop($poIdBookingCond,'or ');
		$poIdBookingCond.=")";
		
		$poIdLabCond=chop($poIdLabCond,'or ');
		$poIdLabCond.=")";
		
		$prevPoIdExCond=chop($prevPoIdExCond,'or ');
		$prevPoIdExCond.=")";
		
		$poIdLcScCond=chop($poIdLcScCond,'or ');
		$poIdLcScCond.=")";
		
		$poIdUpDisCond=chop($poIdUpDisCond,'or ');
		$poIdUpDisCond.=")";
		
		$shipStatusPoIdCond=chop($shipStatusPoIdCond,'or ');
		$shipStatusPoIdCond.=")";
		
		$sewingPoIdCond=chop($sewingPoIdCond,'or ');
		$sewingPoIdCond.=")";
	}
	else
	{
		$poIdIssueTransCond=" and b.po_breakdown_id in ($poIds)";
		$poIdBookingCond=" and b.po_break_down_id in ($poIds)";
		$poIdLabCond=" and po_id in ($poIds)";
		$prevPoIdExCond=" and po_break_down_id in ($poIds)";
		$poIdLcScCond=" and b.wo_po_break_down_id in ($poIds)";
		$poIdUpDisCond=" and b.po_breakdown_id in ($poIds)";
		$shipStatusPoIdCond=" and id in ($poIds)";
		$sewingPoIdCond=" and po_break_down_id in ($poIds)";
	}
	
	$poShipStatusSql=sql_select("Select job_no_mst, max(shiping_status) as shiping_status from wo_po_break_down where status_active=1 and is_deleted=0 $shipStatusPoIdCond group by job_no_mst");
	foreach($poShipStatusSql as $ssrow)
	{
		$job_data_arr[$ssrow[csf('job_no_mst')]]['shipstatus']=$ssrow[csf('shiping_status')];
	}
	unset($poShipStatusSql);
	
	$prevExSql=sql_select("select po_break_down_id, sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as exf_qnty,
	sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as exf_ret_qnty from pro_ex_factory_mst where status_active=1 and is_deleted=0 $prevPoIdExCond  $prev_date_cond group by po_break_down_id");
	 
	/*echo "Select po_break_down_id, sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as exf_qnty,
	sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as exf_ret_qnty from pro_ex_factory_mst where status_active=1 and is_deleted=0 $prevPoIdExCond  $prev_date_cond group by po_break_down_id";*/
	foreach($prevExSql as $pxrow)
	{
		$ship_qty=0;
		$ship_qty=$pxrow[csf('exf_qnty')]-$pxrow[csf('exf_ret_qnty')];
		$job_no=$poWiseJobArr[$pxrow[csf('po_break_down_id')]]['job'];
		$job_data_arr[$job_no]['preXqty']+=$ship_qty;
		//3 full,2 partial
		
		 
	}
	unset($prevExSql);
	//print_r($job_data_arr['OG-20-00050']['shipstatus']);die;
	
	$exchangeRateArr=return_library_array("select job_no, exchange_rate from  wo_pre_cost_mst where status_active=1 and is_deleted=0","job_no","exchange_rate");
	
	$transSql="select a.transaction_type, b.entry_form, b.issue_purpose, b.po_breakdown_id, a.item_category, a.cons_rate, b.quantity from inv_transaction a, order_wise_pro_details b where a.id=b.trans_id and a.item_category in(1,2) and a.transaction_type in(2,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 $poIdIssueTransCond ";
	//echo $transSql;
	$transDataSql=sql_select($transSql);
	$issueTransArr=array(); $issueQtyArr=array();
	foreach($transDataSql as $invRow)
	{
		$issueAmt=0; $job_no=''; $exchange_rate=0;
		$job_no=$poWiseJobArr[$invRow[csf('po_breakdown_id')]]['job'];
		$exchange_rate=$exchangeRateArr[$job_no];
		if($invRow[csf('item_category')]==1)
		{
			if($invRow[csf('transaction_type')]==2 || $invRow[csf('transaction_type')]==4 || $invRow[csf('transaction_type')]==5 || $invRow[csf('transaction_type')]==6)
			{
				$yarnIssueAmt=$yarnIssueRetAmt=$yarnTrnsInAmt=$yarnTrnsOutAmt=$iss_amnt=0;
				if($invRow[csf('entry_form')]==3)
				{
					if($invRow[csf('issue_purpose')]!=2) $yarnIssueAmt=$invRow[csf('quantity')]*$invRow[csf('cons_rate')];
					$issueQtyArr[$job_no]['yarnIssueQty']+=$invRow[csf('quantity')];
				}
				if($invRow[csf('entry_form')]==18 || $invRow[csf('entry_form')]==71) $issueQtyArr[$job_no]['issuetoCutQty']+=$invRow[csf('quantity')];
				if($invRow[csf('entry_form')]==9) $yarnIssueRetAmt=$invRow[csf('quantity')]*$invRow[csf('cons_rate')];
				if($invRow[csf('entry_form')]==5) $yarnTrnsInAmt=$invRow[csf('quantity')]*$invRow[csf('cons_rate')];
				if($invRow[csf('entry_form')]==6) $yarnTrnsOutAmt=$invRow[csf('quantity')]*$invRow[csf('cons_rate')];
				$issueAmt=$yarnIssueAmt+$yarnTrnsInAmt-($yarnIssueRetAmt+$yarnTrnsOutAmt);
				//echo $invRow[csf('quantity')].'='.$invRow[csf('cons_rate')].'='.$exchange_rate.'<br>';
				
				$iss_amnt=$issueAmt/$exchange_rate;
				
				$issueTransArr[$job_no]['yarn']+=$iss_amnt;
			}
		}
	}
	unset($transDataSql);
	//print_r($issueTransArr); die;
	
	$bookingSql="select a.booking_type, a.fabric_source, a.item_category,a.exchange_rate as mst_exchange, a.currency_id, b.exchange_rate, b.po_break_down_id, b.process, b.amount, b.pre_cost_fabric_cost_dtls_id from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.item_category in(2,3,4,12,25) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $poIdBookingCond";
	//echo $bookingSql;die;
	$bookingDataArr=sql_select($bookingSql);
	$emblWashPostArr=array(); $trimsCostPostArr=array(); $serviceCostPostArr=array(); $fabPurchaseCostArr=array();
	foreach($bookingDataArr as $woRow)
	{
		$amount=0; $trimsAmnt=0; $serviceAmt=0; $exchange_rate=0; $job_no='';
		$job_no=$poWiseJobArr[$woRow[csf('po_break_down_id')]]['job'];
		$exchange_rate=$exchangeRateArr[$job_no];
		if($woRow[csf('currency_id')]==1) { $amount=$woRow[csf('amount')]/$exchange_rate; } else { $amount=$woRow[csf('amount')]; }
		
		if($woRow[csf('item_category')]==25 && ($woRow[csf('booking_type')]==3 || $woRow[csf('booking_type')]==6)) 
		{ 
			$emblWashPostArr[$job_no]+=$amount; 
		}
		else if($woRow[csf('item_category')]==4)
		{
			if($woRow[csf('currency_id')]==1) $trimsAmnt=$woRow[csf('amount')]/$woRow[csf('exchange_rate')]; else $trimsAmnt=$woRow[csf('amount')];
			$trimsCostPostArr[$job_no]+=$trimsAmnt; 
		}
		else if($woRow[csf('item_category')]==12 && ($woRow[csf('booking_type')]==3 || $woRow[csf('booking_type')]==6)) 
		{ 
			if($woRow[csf('currency_id')]==1) $serviceAmt=$woRow[csf('amount')]/$woRow[csf('mst_exchange')]; else $serviceAmt=$woRow[csf('amount')];
			$serviceCostPostArr[$job_no][$woRow[csf('process')]]+=$serviceAmt; 
		}
		else if($woRow[csf('item_category')]==2 || $woRow[csf('item_category')]==3) 
		{ 
			if($woRow[csf('booking_type')]==1 && $woRow[csf('fabric_source')]==2)
			{
				if($woRow[csf('currency_id')]==1) $purchaseAmt=$woRow[csf('amount')]/$woRow[csf('exchange_rate')]; else $purchaseAmt=$woRow[csf('amount')];
				//echo $purchaseAmt.'<br>';
				$fabPurchaseCostArr[$job_no]['fabPur']+=$purchaseAmt; 
			}
		}
	}
	unset($bookingDataArr);
	
	$bookingYDSql="select a.currency, b.job_no, b.amount as ydamt from wo_yarn_dyeing_mst a, wo_yarn_dyeing_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
	//echo $bookingSql;die;
	$bookingYDDataArr=sql_select($bookingYDSql);
	$ydCostPostArr=array();
	foreach($bookingYDDataArr as $wydRow)
	{
		$amount=0;
		$exchange_rate=$exchangeRateArr[$wydRow[csf('job_no')]];
		if($wydRow[csf('currency')]==1) { $amount=$wydRow[csf('ydamt')]/$exchange_rate; } else { $amount=$wydRow[csf('ydamt')]; }
		
		$ydCostPostArr[$wydRow[csf('job_no')]]+=$amount; 
	}
	unset($bookingYDDataArr);	
	
	$lab_sql="select po_id, amount from wo_labtest_dtls where status_active=1 and is_deleted=0 $poIdLabCond ";
	$lab_sql_res=sql_select($lab_sql); $labCostPostArr=array();
	foreach($lab_sql_res as $rowl)
	{
		$job_no="";
		$job_no=$poWiseJobArr[$rowl[csf('po_id')]]['job'];
		$labCostPostArr[$job_no]+=$rowl[csf('amount')];
	}
	unset($lab_sql_res);
	
	$sqlLsSc= "select a.internal_file_no, b.wo_po_break_down_id, '1' as type
			from com_sales_contract a, com_sales_contract_order_info b
			where a.id=b.com_sales_contract_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $poIdLcScCond group by a.internal_file_no, b.wo_po_break_down_id
			UNION ALL
			select a.internal_file_no, b.wo_po_break_down_id, '0' as type
			from com_export_lc a, com_export_lc_order_info b
			where a.id=b.com_export_lc_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $poIdLcScCond group by a.internal_file_no, b.wo_po_break_down_id";
	//echo $sqlLsSc; die;		
	$sqlLsScData=sql_select($sqlLsSc); $lcScDataArr=array();
	foreach($sqlLsScData as $lsrow)
	{
		$job_no=$file_no='';
		$job_no=$poWiseJobArr[$lsrow[csf('wo_po_break_down_id')]]['job'];
		
		if($file_no=="") $file_no=$lsrow[csf('internal_file_no')]; else $file_no.=', '.$lsrow[csf('internal_file_no')];
		$lcScDataArr[$job_no]['file']=$file_no;
	}
	unset($sqlLsScData);
	
	$sqlUpDis="SELECT a.invoice_no, a.discount_ammount, a.claim_ammount, a.upcharge, a.invoice_value, b.po_breakdown_id, b.current_invoice_value from com_export_invoice_ship_mst a, com_export_invoice_ship_dtls b WHERE a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $poIdUpDisCond";
	$sqlUpDisData=sql_select($sqlUpDis); $upDisDataArr=array();
	
	foreach($sqlUpDisData as $udrow)
	{
		$job_no='';
		$upCharge=$discount=$claim=$discountClaim=0;
		$job_no=$poWiseJobArr[$udrow[csf('po_breakdown_id')]]['job'];
		$upCharge=($udrow[csf('current_invoice_value')]/$udrow[csf('invoice_value')])*$udrow[csf('upcharge')];
		$discount=($udrow[csf('current_invoice_value')]/$udrow[csf('invoice_value')])*$udrow[csf('discount_ammount')];
		$claim=($udrow[csf('current_invoice_value')]/$udrow[csf('invoice_value')])*$udrow[csf('claim_ammount')];
		$discountClaim=$discount+$claim;
		
		$upDisDataArr[$job_no]['up']+=$upCharge;
		$upDisDataArr[$job_no]['dis']+=$discountClaim;
		$upDisDataArr[$job_no]['invNo'].=$udrow[csf('invoice_no')].'___';
	}
	unset($sqlLsScData); 
	
	$sqlSewing="select po_break_down_id, production_source, production_quantity from pro_garments_production_mst where production_type=5 and status_active=1 and is_deleted=0 $sewingPoIdCond";
	
	$sqlSewingData=sql_select($sqlSewing); $sewingDataArr=array();
	
	foreach($sqlSewingData as $srow)
	{
		$job_no='';
		$job_no=$poWiseJobArr[$srow[csf('po_break_down_id')]]['job'];
		if($srow[csf('production_source')]==1)
		{
			$sewingDataArr[$job_no]['inhouse']+=$srow[csf('production_quantity')];
		}
		else if($srow[csf('production_source')]==3)
		{
			$sewingDataArr[$job_no]['outbound']+=$srow[csf('production_quantity')];
		}
	}
	unset($sqlSewingData); 
	
	$companyArr=return_library_array( "select id,company_name from lib_company", "id", "company_name");	
	$buyerArr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
	
	//echo $all_po_id;  die;
	
	$condition= new condition();
	$condition->company_name("=$cbo_company_name");
	if(str_replace("'","",$cbo_buyer_name)>0){
		$condition->buyer_name("=$cbo_buyer_name");
	}
	if($year_id!=0) $condition->job_year("$year_cond"); 
	
	if(str_replace("'","",$all_po_id)!='') $condition->po_id_in("$all_po_id");
				 
	/*if ($start_date!="" && $end_date!="")
	{	
		if($date_type==1) $condition->country_ship_date(" between '$start_date' and '$end_date'");
		else if($date_type==2) $condition->pub_shipment_date(" between '$start_date' and '$end_date'");
		//else if($date_type==3) //$condition->po_received_date(" between '$start_date' and '$end_date'");
		else if($date_type==4) $condition->po_received_date(" between '$start_date' and '$end_date'");
	}*/
	
	$condition->init();
	$fabric= new fabric($condition);
	$fabricCostingArr=$fabric->getAmountArray_by_job_knitAndwoven_greyAndfinish();
	
	$yarn= new yarn($condition);
	$yarnCostingArr=$yarn->getJobWiseYarnAmountArray();
	
	$conversion= new conversion($condition);
	$conversionCostArr=$conversion->getAmountArray_by_jobAndProcess();
	
	$trims= new trims($condition);
	$trimCostArr=$trims->getAmountArray_by_job();
	
	$emblishment= new emblishment($condition);
	//echo $emblishment->getQuery(); die;
	$emblCostArr=$emblishment->getAmountArray_by_job();
	//print_r($emblCostArr); die;
	
	$wash= new wash($condition);
	$washCostArr=$wash->getAmountArray_by_job();
	
	$other= new other($condition);
	$otherCostArr=$other->getAmountArray_by_job();
	
	$commercial= new commercial($condition);
	$commercialCostArr=$commercial->getAmountArray_by_job();
	
	$commission= new commision($condition);
	$commissionCostArr=$commission->getAmountArray_by_job();	
	?>
    <fieldset style="width:100%">	
        <table cellpadding="0" cellspacing="0" width="5550">
            <tr>
               <td align="center" width="100%" colspan="68" style="font-size:16px"><strong><?=$companyArr[$cbo_company_name]; ?></strong></td>
            </tr>
            <tr>
               <td align="center" width="100%" colspan="68" style="font-size:16px"><strong><?=$report_title; ?></strong></td>
            </tr>
            <tr>
               <td align="center" width="100%" colspan="68" style="font-size:16px"><strong><? if($start_date!="" && $end_date!="") echo "From ". change_date_format($start_date). " To ". change_date_format($end_date);?></strong></td>
            </tr>
        </table>
        <table class="rpt_table" border="1" rules="all" width="5550" cellpadding="0" cellspacing="0" id="table_header_1">
            <thead>
                <tr>
                    <th rowspan="2" width="40">SL</th>
                    <th colspan="8" style="background:#CF9">Style Details</th>
                    <th colspan="11" style="background:#FFFF66">Material Cost @ Pre-Cost</th>
                    <th colspan="11" style="background:#CCFFCC">Material Cost @ Post-Cost</th>
                    <th colspan="11" style="background:#00FFFF">Surplus / [Deficit] of Material Consumption</th>
                    <th colspan="5" style="background:#9FF">Shipment Status</th>
                    
                    <th colspan="2" style="background:#00FFAA">CM Analysis @ Pre-Cost</th>
                    <th colspan="2" style="background:#00FFDD">CM Analysis @ Post-Cost</th>
                    <th colspan="2" style="background:#FFCCFF">Short Shipment</th>
                    <th colspan="2" style="background:#FFFF66">Excess Shipment</th>
                    <th colspan="2" style="background:#99FFFF">Up-charges /Discount</th>
                    
                    <th width="80" rowspan="2">Adjusted CM</th>
                    <th width="80" rowspan="2">Invoice No.</th>
                    <th width="80" rowspan="2">Mode of Shipment</th>
                    <th width="80" rowspan="2">Shipment Status</th>
                    <th width="80" rowspan="2">Previous Ship Qty.</th>
                    <th width="80" rowspan="2">Total Agent Commission</th>
                    <th width="80" rowspan="2">In-House Qty.</th>
                    <th width="80" rowspan="2">Sub-Con Qty.</th>
                    <th width="80" rowspan="2">Yarn Qty.[Issue]</th>
                    <th width="80" rowspan="2">Fabric Qty.[Issue to Cut]</th>
                    
                    <th rowspan="2">Comments</th>
                </tr>
                <tr>
                    <th width="110" title="LC/SC">File</th>
                    <th width="100">Name of Buyer</th>
                    <th width="110">Style Ref.</th>
                    <th width="100">Job No.</th>
                    <th width="110">PO No.</th>
                    <th width="90">Style Qty. (Pcs)</th>
                    <th width="70">Avg. Unit Price</th>
                    <th width="100">Style Value</th><!--700-->
                    
                    <th width="80">Yarn</th>
                    <th width="80">Dyeing</th>
                    <th width="80">Knitting</th>
                    <th width="80">Fabric Purchase</th>
                    <th width="80">Others Fabric Cost</th>
                    <th width="80">Accessories</th>
                    <th width="80">Pint /Emb./Wash</th>
                    <th width="80">Test</th>
                    <th width="80">Others Cost</th>
                    <th width="100">Total</th>
                    <th width="70">% of Total Cost</th><!--890-->
                    
                    <th width="80">Yarn Consumption</th>
                    <th width="80">Dyeing</th>
                    <th width="80">Knitting</th>
                    <th width="80">Fabric Purchase</th>
                    <th width="80">Others Fabric Cost</th>
                    <th width="80">Accessories</th>
                    <th width="80">Pint /Emb./Wash</th>
                    <th width="80">Test</th>
                    <th width="80">Others Cost</th>
                    <th width="100">Total</th>
                    <th width="70">% of Total Cost</th><!--890-->
                    
                    <th width="80">Yarn</th>
                    <th width="80">Dyeing</th>
                    <th width="80">Knitting</th>
                    <th width="80">Fabric Purchase</th>
                    <th width="80">Others Fabric Cost</th>
                    <th width="80">Accessories</th>
                    <th width="80">Pint /Emb./Wash</th>
                    <th width="80">Test</th>
                    <th width="80">Others Cost</th>
                    <th width="100">Total</th>
                    <th width="70">% of Total Cost</th><!--890-->
                    
                    
                    <th width="80">Ship. Qty.</th>
                    <th width="70" title="Unit Price-Commission">Net Unit Price</th>
                    <th width="70" title="Both Local and Foreign">Agent Commission</th>
                    <th width="80">Net Shipment Value</th>
                    <th width="80">Date of Shipment</th><!--380-->
                    
                    <th width="70" title="FOB-Material">CM /Dzn</th>
                    <th width="70" title="FOB-Material">Total CM</th>
                    
                    <th width="70" title="FOB-Material">CM /Dzn</th>
                    <th width="70" title="FOB-Material">Total CM</th>
                    
                    <th width="70">Quantity</th>
                    <th width="70">Value</th>
                    
                    <th width="70">Quantity</th>
                    <th width="70">Value</th>
                    
                    <th width="70">Up-Charges</th>
                    <th width="70">Discount & Claim</th><!--700-->
                </tr>
            </thead>
        </table>
        <div style="width:5550px; overflow-y:scroll; max-height:400px;" id="scroll_body">
        	<table width="5530" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body" >
			<? 	
            $i=1;
			$fabricYd_dyeingCost_arr=array(25,26,30,31,32,60,61,62,63,72,80,81,84,85,86,87,38,39,74,78,79,101,133,137,138,139,146,147,149,158);
			$fabric_dyeingCost_arr=array(25,31,26,32,60,61,62,63,72,80,81,84,85,86,87,38,39,74,78,79,101,133,137,138,139,146,147,149);
			$not_process_id_print_array = array(1, 2, 3, 25,26,30,31,32,60,61,62,63,72,80,81,84,85,86,87,38,39,74,78,79,101,133,137,138,139,146,147,149,158);
            foreach($job_data_arr as $jobno=>$row)
            {
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                $avgUnitPrice=$fabricAmtBom=$yarnAmtBom=$dyeingAmtBom=$knittingAmtBom=$otherFabAmtBom=$accAmtBom=$emblWashAmtBom=$labTestAmtBom=$othersAmtBom=$commercialAmtBom=$commissionAmtBom=$rowTotBom=$matrialPerBom=0;
				$fileNo='';
				$fileNo=$lcScDataArr[$jobno]['file'];
				$fileNo=implode(", ",array_unique(array_filter(explode(",",$fileNo))));
				
                $avgUnitPrice=$row['jobValue']/$row['jobQtyPcs'];
				
				//Precost
				$fab_purchase_knit=$fab_purchase_woven=0;
				$fab_purchase_knit=array_sum($fabricCostingArr['knit']['grey'][$jobno]);
				$fab_purchase_woven=array_sum($fabricCostingArr['woven']['grey'][$jobno]);
				
				$fabricAmtBom=$fab_purchase_knit+$fab_purchase_woven;
				$yarnAmtBom=$yarnCostingArr[$jobno];
				foreach($fabricYd_dyeingCost_arr as $dye_id)
				{
					$dyeingAmtBom+=array_sum($conversionCostArr[$jobno][$dye_id]);
				}
				
				$knittingAmtBom=array_sum($conversionCostArr[$jobno][1])+array_sum($conversionCostArr[$jobno][2])+array_sum($conversionCostArr[$jobno][3]);
				
				foreach($conversion_cost_head_array as $processid=>$process_val)
				{
					if (!in_array($processid, $not_process_id_print_array)) 
					{
						$otherFabAmtBom+=array_sum($conversionCostArr[$jobno][$processid]);
					}
				}
				
				$accAmtBom=$trimCostArr[$jobno];
				$emblWashAmtBom=$emblCostArr[$jobno]+$washCostArr[$jobno];
				$labTestAmtBom=$otherCostArr[$jobno]['lab_test'];
				//$commercialAmtBom=$commissionCostArr[$jobno];
				$commissionAmtBom=$commissionCostArr[$jobno];
				//$othersAmtBom=$otherCostArr[$jobno]['freight']+$otherCostArr[$jobno]['inspection']+$otherCostArr[$jobno]['certificate_pre_cost']+$otherCostArr[$jobno]['currier_pre_cost']+$otherCostArr[$jobno]['common_oh']+$otherCostArr[$jobno]['depr_amor_pre_cost']+$otherCostArr[$jobno]['income_tax']+$commissionAmtBom+$commercialAmtBom;//interest_expense,
				
				$rowTotBom=$fabricAmtBom+$yarnAmtBom+$dyeingAmtBom+$knittingAmtBom+$otherFabAmtBom+$accAmtBom+$emblWashAmtBom+$labTestAmtBom+$othersAmtBom;
				$matrialPerBom=($rowTotBom/$row['jobValue'])*100;
				
				$poNo="";
				$poNo=implode(",",array_filter(array_unique(explode("___",$row['poNo']))));
				
				
                ?>
                <tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>">
                    <td width="40" align="center"><?=$i; ?></td>
                    
                    <td width="110" style="word-break:break-all" title="From LC/SC"><?=$fileNo; ?></td>
                    <td width="100" style="word-break:break-all"><?=$buyerArr[$row['buyer']]; ?></td>
                    <td width="110" style="word-break:break-all"><?=$row['style_ref']; ?></td>
                    <td width="100" align="center" style="word-break:break-all"><?=$jobno; ?></td>
                    <td width="110" align="center" style="word-break:break-all"><?=$poNo; ?></td>
                    <td width="90" align="right"><?=fn_number_format($row['jobQtyPcs'],0,'.',''); ?></td>
                    <td width="70" align="right"><?=fn_number_format($avgUnitPrice,2,'.',''); ?></td>
                    <td width="100" align="right"><?=fn_number_format($row['jobValue'],2,'.',''); ?></td>
                    
                    <td width="80" align="right"><?=fn_number_format($yarnAmtBom,2,'.',''); ?></td>
                    <td width="80" align="right"><?=fn_number_format($dyeingAmtBom,2,'.',''); ?></td>
                    <td width="80" align="right"><?=fn_number_format($knittingAmtBom,2,'.',''); ?></td>
                    <td width="80" align="right" ><?=fn_number_format($fabricAmtBom,2,'.',''); ?></td>
                    <td width="80" align="right"><?=fn_number_format($otherFabAmtBom,2,'.',''); ?></td>
                    <td width="80" align="right"><?=fn_number_format($accAmtBom,2,'.',''); ?></td>
                    <td width="80" align="right" ><?=fn_number_format($emblWashAmtBom,2,'.',''); ?></td>
                    <td width="80" align="right"><?=fn_number_format($labTestAmtBom,2,'.',''); ?></td>
                    <td width="80" align="right"><? //=fn_number_format($othersAmtBom,2,'.',''); ?></td>
                    <td width="100" align="right"><?=fn_number_format($rowTotBom,2,'.',''); ?></td>
                    <td width="70" align="right"><?=fn_number_format($matrialPerBom,2,'.',''); ?></td>
                    <?
					//Material Cost @ Post-Cost
					$fabricAmtPost=$yarnAmtPost=$dyeingAmtPost=$knittingAmtPost=$otherFabAmtPost=$accAmtPost=$emblWashAmtPost=$labTestAmtPost=$othersAmtPost=$commercialAmtPost=$commissionAmtPost=$rowTotPost=$matrialPerPost=0;
					
					$fabricAmtPost=$fabPurchaseCostArr[$jobno]['fabPur'];
					$yarnAmtPost=$issueTransArr[$jobno]['yarn'];
					foreach($fabric_dyeingCost_arr as $dye_id)
					{
						$dyeingAmtPost+=$serviceCostPostArr[$jobno][$dye_id];
					}
					$dyeingAmtPost+=$ydCostPostArr[$jobno];
					$knittingAmtPost=$serviceCostPostArr[$jobno][1]+$serviceCostPostArr[$jobno][2]+$serviceCostPostArr[$jobno][3];
					
					foreach($conversion_cost_head_array as $processid=>$process_val)
					{
						if (!in_array($processid, $not_process_id_print_array)) 
						{
							$otherFabAmtPost+=$serviceCostPostArr[$jobno][$processid];
						}
					}
					
					$accAmtPost=$trimsCostPostArr[$jobno];
					$emblWashAmtPost=$emblWashPostArr[$jobno];
					$labTestAmtPost=$labCostPostArr[$jobno];
					
					$shipstatus_id=$job_data_arr[$jobno]['shipstatus'];
					$fabricAmtPost_cal=$fabricAmtPost;
					$accAmtPost_cal=$accAmtPost;
					$emblWashAmtPost_cal=$emblWashAmtPost;
					$yarnAmtPost_cal=$yarnAmtPost;
					if($shipstatus_id==3) //Full Ship
					{
						$fab_msg_title="";$yarn_msg_title="";$acc_msg_title="";$embl_msg_title="";
						//echo $fabricAmtPost.'DD';
						$fabricAmtPost=$fabricAmtPost;
						$accAmtPost=$accAmtPost;
						$emblWashAmtPost=$emblWashAmtPost;
						
						$yarnAmtPost=$yarnAmtPost;
						$dyeingAmtPost=$dyeingAmtPost;
						$knittingAmtPost=$knittingAmtPost;
						$otherFabAmtPost=$otherFabAmtPost;
					}
					else
					{
						
						$fab_msg_title="{Actual WO Cost($fabricAmtPost_cal)/Style Qty. Pcs } X Partial Shipment Qty";
						$acc_msg_title="{Actual WO Cost($accAmtPost_cal)/Style Qty. Pcs } X Partial Shipment Qty";
						$embl_msg_title="{Actual WO Cost($emblWashAmtPost_cal)/Style Qty. Pcs } X Partial Shipment Qty";
						$yarn_msg_title="{Actual WO Cost($yarnAmtPost_cal)/Style Qty. Pcs } X Partial Shipment Qty";
						$dyeing_msg_title="{Actual WO Cost($dyeingAmtPost)/Style Qty. Pcs } X Partial Shipment Qty";
						
						$partial_shipQty=$shipmentDataArr[$jobno]['qty'];
						
						$fabricAmtPost=($fabricAmtPost/$row['jobQtyPcs'])*$partial_shipQty;
						$accAmtPost=($accAmtPost/$row['jobQtyPcs'])*$partial_shipQty;
						$emblWashAmtPost=($emblWashAmtPost/$row['jobQtyPcs'])*$partial_shipQty;;
						
						$yarnAmtPost=($yarnAmtPost/$row['jobQtyPcs'])*$partial_shipQty;
						$dyeingAmtPost=($dyeingAmtPost/$row['jobQtyPcs'])*$partial_shipQty;
						$knittingAmtPost=($knittingAmtPost/$row['jobQtyPcs'])*$partial_shipQty;
						$otherFabAmtPost=($otherFabAmtPost/$row['jobQtyPcs'])*$partial_shipQty;
					}
					
					//$commercialAmtPost=$commissionCostArr[$jobno];
					//$commissionAmtPost=$commissionCostArr[$jobno][1]+$commissionCostArr[$jobno][2];
					//$othersAmtPost=$otherCostArr[$jobno]['freight']+$otherCostArr[$jobno]['inspection']+$otherCostArr[$jobno]['certificate_pre_cost']+$otherCostArr[$jobno]['currier_pre_cost']+$otherCostArr[$jobno]['cm_cost']+$otherCostArr[$jobno]['common_oh']+$otherCostArr[$jobno]['depr_amor_pre_cost']+$otherCostArr[$jobno]['income_tax']+$commercialAmtPost+$commissionAmtPost;//interest_expense,
					
					$rowTotPost=$fabricAmtPost+$yarnAmtPost+$dyeingAmtPost+$knittingAmtPost+$otherFabAmtPost+$accAmtPost+$emblWashAmtPost+$labTestAmtPost+$othersAmtPost;
					$matrialPerPost=($rowTotPost/$row['jobValue'])*100;
					?>
                    
                    <td width="80" align="right" title="<? echo $yarn_msg_title;?>"><?=fn_number_format($yarnAmtPost,2,'.',''); ?></td>
                    <td width="80" align="right" title="<? echo $dyeing_msg_title;?>"><?=fn_number_format($dyeingAmtPost,2,'.',''); ?></td>
                    <td width="80" align="right"><?=fn_number_format($knittingAmtPost,2,'.',''); ?></td>
                    <td width="80" align="right" title="<? echo $fab_msg_title;?>"><?=fn_number_format($fabricAmtPost,2,'.',''); ?></td>
                    <td width="80" align="right" ><?=fn_number_format($otherFabAmtPost,2,'.',''); ?></td>
                    <td width="80" align="right" title="<? echo $acc_msg_title;?>"><?=fn_number_format($accAmtPost,2,'.',''); ?></td>
                    <td width="80" align="right" title="<? echo $embl_msg_title;?>"><?=fn_number_format($emblWashAmtPost,2,'.',''); ?></td>
                    <td width="80" align="right"><?=fn_number_format($labTestAmtPost,2,'.',''); ?></td>
                    <td width="80" align="right"><? //=fn_number_format($othersAmtPost,2,'.',''); //Others Cost ?></td>
                    <td width="100" align="right"><?=fn_number_format($rowTotPost,2,'.',''); ?></td>
                    <td width="70" align="right"><?=fn_number_format($matrialPerPost,2,'.',''); ?></td>
                    <?
					//Surplus / (deficit) of Material consumption
					$fabricAmtSD=$yarnAmtSD=$dyeingAmtSD=$knittingAmtSD=$otherFabAmtSD=$accAmtSD=$emblWashAmtSD=$labTestAmtSD=$othersAmtSD=$commercialAmtSD=$commissionAmtSD=$rowTotSD=$matrialPerSD=0;
					
					$fabricAmtSD=$fabricAmtBom-$fabricAmtPost;
					$yarnAmtSD=$yarnAmtBom-$yarnAmtPost;
					$dyeingAmtSD=$dyeingAmtBom-$dyeingAmtPost;
					$knittingAmtSD=$knittingAmtBom-$knittingAmtPost;
					$otherFabAmtSD=$otherFabAmtBom-$otherFabAmtPost;
					$accAmtSD=$accAmtBom-$accAmtPost;
					$emblWashAmtSD=$emblWashAmtBom-$emblWashAmtPost;
					$labTestAmtSD=$labTestAmtBom-$labTestAmtPost;
					//$commercialAmtSD=$commercialAmtBom-$commercialAmtPost;
					//$commissionAmtSD=$commissionAmtBom-$commissionAmtPost;
					//$othersAmtSD=$othersAmtBom-$othersAmtPost;//interest_expense,
					
					$rowTotSD=$rowTotBom-$rowTotPost;
					$matrialPerSD=($rowTotSD/$row['jobValue'])*100;
					?>
                    
                    <td width="80" align="right"><?=fn_number_format($yarnAmtSD,2,'.',''); ?></td>
                    <td width="80" align="right"><?=fn_number_format($dyeingAmtSD,2,'.',''); ?></td>
                    <td width="80" align="right"><?=fn_number_format($knittingAmtSD,2,'.',''); ?></td>
                    <td width="80" align="right"><?=fn_number_format($fabricAmtSD,2,'.',''); ?></td>
                    <td width="80" align="right"><?=fn_number_format($otherFabAmtSD,2,'.',''); ?></td>
                    <td width="80" align="right"><?=fn_number_format($accAmtSD,2,'.',''); ?></td>
                    <td width="80" align="right"><?=fn_number_format($emblWashAmtSD,2,'.',''); ?></td>
                    <td width="80" align="right"><?=fn_number_format($labTestAmtSD,2,'.',''); ?></td>
                    <td width="80" align="right"><? //=fn_number_format($othersAmtSD,2,'.',''); ?></td>
                    <td width="100" align="right"><?=fn_number_format($rowTotSD,2,'.',''); ?></td>
                    <td width="70" align="right"><?=fn_number_format($matrialPerSD,2,'.',''); ?></td>
                    
                    <?
					//Shipment Status
					$shipQty=$netUnitPrice=$agentCommission=$netShpValue=$shipingStatus=0;
					$shipQty=$shipmentDataArr[$jobno]['qty'];
					$agentCommission=($commissionAmtBom/$row['jobQtyPcs']);
					$netUnitPrice=$avgUnitPrice-$agentCommission;
					$netShpValue=$shipQty*$netUnitPrice;
					$shipingStatus=$shipmentDataArr[$jobno]['shiping_status'];
					
					?>
                    <td width="80" align="right"><?=fn_number_format($shipQty,0,'.',''); ?></td>
                    <td width="70" title="Avg. Unit Price-Commission" align="right"><?=fn_number_format($netUnitPrice,2,'.',''); ?></td>
                    <td width="70" title="Both Local and Foreign" align="right"><?=fn_number_format($agentCommission,2,'.',''); ?></td>
                    <td width="80" align="right"><?=fn_number_format($netShpValue,2,'.',''); ?></td>
                    <td width="80"><?=change_date_format($shipmentDataArr[$jobno]['ship_date']); ?></td>
                    <?
					//CM Analysis @ Pre-Cost
					$rowTotCmCostBom=$cmCostBom=0;
					$rowTotCmCostBom=($row['jobValue']-$rowTotBom);
					$cmCostBom=($rowTotCmCostBom/$row['jobQtyPcs'])*12;
					
					//CM Analysis @ Post-Cost
					$rowTotCmCostPost=$cmCostPost=0;
					$rowTotCmCostPost=($row['jobValue']-$rowTotPost);
					$cmCostPost=($rowTotCmCostPost/$row['jobQtyPcs'])*12;
					$rowPreShipQty=$job_data_arr[$jobno]['preXqty'];
					$rowShipQty=$rowPreShipQty+$shipQty;
					
					//Short and Excess Ship
					$shipBalance=$shortShipQty=$shortShipVal=$excessShipQty=$excessShipVal=$upcharge=$discount=0;
					if($shipingStatus==3)
					{
						$shipBalance=$row['jobQtyPcs']-$rowShipQty;
						
						if($shipBalance>0)
						{
							$shortShipQty=str_replace("-","",$shipBalance);
							$shortShipVal=str_replace("-","",$shipBalance)*$avgUnitPrice;
						}
						if($shipBalance<0)
						{
							$excessShipQty=str_replace("-","",$shipBalance);
							$excessShipVal=str_replace("-","",$shipBalance)*$avgUnitPrice;
						}
					}
					//Up-charges /(Discount)
					$upcharge=$upDisDataArr[$jobno]['up'];
					$discount=$upDisDataArr[$jobno]['dis'];
					?>
                    
                    <td width="70" title="((FOB-Material)/Style Qty)*12" align="right"><?=fn_number_format($cmCostBom,2,'.',''); ?></td>
                    <td width="70" title="FOB-Material" align="right"><?=fn_number_format($rowTotCmCostBom,2,'.',''); ?></td>
                    
                    <td width="70" title="((FOB-Material)/Style Qty)*12" align="right"><?=fn_number_format($cmCostPost,2,'.',''); ?></td>
                    <td width="70" title="FOB-Material" align="right"><?=fn_number_format($rowTotCmCostPost,2,'.',''); ?></td>
                    
                    <td width="70" align="right"><?=fn_number_format($shortShipQty,0,'.',''); ?></td>
                    <td width="70" align="right"><?=fn_number_format($shortShipVal,2,'.',''); ?></td>
                    
                    <td width="70" align="right"><?=fn_number_format($excessShipQty,0,'.',''); ?></td>
                    <td width="70" align="right"><?=fn_number_format($excessShipVal,2,'.',''); ?></td>
                    
                    <td width="70" align="right"><?=fn_number_format($upcharge,2,'.',''); ?></td>
                    <td width="70" align="right"><?=fn_number_format($discount,2,'.',''); ?></td>
                    
                    <?
					$adjustedCm=$previousShipQty=$totAgentCommission=$inHouseQty=$subconQty=$yarnIssueQty=$fabricIssueCutQty=0; 
					$invNo=$shipMode=$poShipStatus="";
					
					$adjustedCm=$rowTotCmCostPost-$shortShipVal+($excessShipVal+($upcharge-$discount));
					
					$invNo=implode(",",array_filter(array_unique(explode("___",$upDisDataArr[$jobno]['invNo']))));
					$shipMode=$job_data_arr[$jobno]['ship_mode'];
					$poShipStatus=$job_data_arr[$jobno]['shipstatus'];
					$previousShipQty=$job_data_arr[$jobno]['preXqty'];
					$totAgentCommission=($shipQty*$agentCommission);
					$inHouseQty=$sewingDataArr[$jobno]['inhouse'];
					$subconQty=$sewingDataArr[$jobno]['outbound'];
					$yarnIssueQty=$issueQtyArr[$jobno]['yarnIssueQty'];
					$fabricIssueCutQty=$issueQtyArr[$jobno]['issuetoCutQty'];
					
					//$shipment_status 
					
					?>
                    <td width="80" align="right"><?=fn_number_format($adjustedCm,0,'.',''); ?></td>
                    <td width="80" style="word-break:break-all"><?=$invNo; ?></td>
                    
                    <td width="80" style="word-break:break-all"><?=$shipment_mode[$shipMode]; ?></td>
                    <td width="80" style="word-break:break-all"><?=$shipment_status[$poShipStatus]; ?></td>
                    
                    <td width="80" align="right"><?=fn_number_format($previousShipQty,2,'.',''); ?></td>
                    <td width="80" align="right"><?=fn_number_format($totAgentCommission,2,'.',''); ?></td>
                    <td width="80" align="right"><?=fn_number_format($inHouseQty,2,'.',''); ?></td>
                    
                    <td width="80" align="right"><?=fn_number_format($subconQty,2,'.',''); ?></td>
                    <td width="80" align="right"><?=fn_number_format($yarnIssueQty,2,'.',''); ?></td>
                    <td width="80" align="right"><?=fn_number_format($fabricIssueCutQty,2,'.',''); ?></td>
                    
                    <td>&nbsp;</td>
                </tr>
                <?
                $i++;
				
				$gStyleQty+=$row['jobQtyPcs'];
				$gStylevalue+=$row['jobValue'];
				$gYarnCostBom+=$yarnAmtBom;
				$gDyeingCostBom+=$dyeingAmtBom;
				$gKnittingCostBom+=$knittingAmtBom;
				$gFabricCostBom+=$fabricAmtBom;
				$gOtherFabCostBom+=$otherFabAmtBom;
				$gAccCostBom+=$accAmtBom;
				$gEmblWashCostBom+=$emblWashAmtBom;
				$gTestCostBom+=$labTestAmtBom;
				$gOtherCostBom+=$othersAmtBom;
				$gTotalCostBom+=$rowTotBom;
				
				$gYarnCostPost+=$yarnAmtPost;
				$gDyeingCostPost+=$dyeingAmtPost;
				$gKnittingCostPost+=$knittingAmtPost;
				$gFabricCostPost+=$fabricAmtPost;
				$gOtherFabCostPost+=$otherFabAmtPost;
				$gAccCostPost+=$accAmtPost;
				$gEmblWashCostPost+=$emblWashAmtPost;
				$gTestCostPost+=$labTestAmtPost;
				$gOtherCostPost+=$othersAmtPost;
				$gTotalCostPost+=$rowTotPost;
				
				$gYarnCostSD+=$yarnAmtSD;
				$gDyeingCostSD+=$dyeingAmtSD;
				$gKnittingCostSD+=$knittingAmtSD;
				$gFabricCostSD+=$fabricAmtSD;
				$gOtherFabCostSD+=$otherFabAmtSD;
				$gAccCostSD+=$accAmtSD;
				$gEmblWashCostSD+=$emblWashAmtSD;
				$gTestCostSD+=$labTestAmtSD;
				$gOtherCostSD+=$othersAmtSD;
				$gTotalCostSD+=$rowTotSD;
				
				$gShipmentQty+=$shipQty;
				$gCommisionCost+=$commissionAmtBom;
				$gShipmentValue+=$netShpValue;
				$gTotalCmCostBom+=$rowTotCmCostBom;
				$gTotalCmCostPost+=$rowTotCmCostPost;
				$gShortShipQty+=$shortShipQty;
				$gShortShipValue+=$shortShipVal;
				$gExcessShipQty+=$excessShipQty;
				$gExcessShipValue+=$excessShipVal;
				$gUpCharge+=$upcharge;
				$gDiscount+=$discount;
				
				$gAdjustedCm+=$adjustedCm;
				$gPreviousShipQty+=$previousShipQty;
				$gtTotAgentCommission+=$totAgentCommission;
				$gInHouseQty+=$inHouseQty;
				$gSubconQty+=$subconQty;
				$gYarnIssueQty+=$yarnIssueQty;
				$gFabricIssueCut+=$fabricIssueCutQty;
            }
			?>
			</table>
		</div>
		<table class="tbl_bottom" width="5550" cellpadding="0" cellspacing="0" border="1" rules="all">
            <tr align="right">
                <td width="40">&nbsp;</td>
                <td width="110">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="110">&nbsp;</td>
                <td width="100">Total:</td>
                <td width="110">&nbsp;</td>
                <td width="90" id="value_styleQty"><?=fn_number_format($gStyleQty,0,'.',''); ?></td>
                <td width="70">&nbsp;</td>
                <td width="100" id="value_styleVal"><?=fn_number_format($gStylevalue,0,'.',''); ?></td><!--700-->
                
                <td width="80" id="value_yarnBom"><?=fn_number_format($gYarnCostBom,0,'.',''); ?></td>
                <td width="80" id="value_dyeingBom"><?=fn_number_format($gDyeingCostBom,0,'.',''); ?></td>
                <td width="80" id="value_knittingBom"><?=fn_number_format($gKnittingCostBom,0,'.',''); ?></td>
                <td width="80" id="value_fabricBom"><?=fn_number_format($gFabricCostBom,0,'.',''); ?></td>
                <td width="80" id="value_otherFabBom"><?=fn_number_format($gOtherFabCostBom,0,'.',''); ?></td>
                <td width="80" id="value_accBom"><?=fn_number_format($gAccCostBom,0,'.',''); ?></td>
                <td width="80" id="value_emblWashBom"><?=fn_number_format($gEmblWashCostBom,0,'.',''); ?></td>
                <td width="80" id="value_testBom"><?=fn_number_format($gTestCostBom,0,'.',''); ?></td>
                <td width="80" id="value_otherBom"><?=fn_number_format($gOtherCostBom,0,'.',''); ?></td>
                <td width="100" id="value_totalBom"><?=fn_number_format($gTotalCostBom,0,'.',''); ?></td>
                <td width="70">&nbsp;</td><!--890-->
                
                <td width="80" id="value_yarnPost"><?=fn_number_format($gYarnCostPost,0,'.',''); ?></td>
                <td width="80" id="value_dyeingPost"><?=fn_number_format($gDyeingCostPost,0,'.',''); ?></td>
                <td width="80" id="value_knittingPost"><?=fn_number_format($gKnittingCostPost,0,'.',''); ?></td>
                <td width="80" id="value_fabricPost"><?=fn_number_format($gFabricCostPost,0,'.',''); ?></td>
                <td width="80" id="value_otherFabPost"><?=fn_number_format($gOtherFabCostPost,0,'.',''); ?></td>
                <td width="80" id="value_accPost"><?=fn_number_format($gAccCostPost,0,'.',''); ?></td>
                <td width="80" id="value_emblWashPost"><?=fn_number_format($gEmblWashCostPost,0,'.',''); ?></td>
                <td width="80" id="value_testPost"><?=fn_number_format($gTestCostPost,0,'.',''); ?></td>
                <td width="80" id="value_otherPost"><?=fn_number_format($gOtherCostPost,0,'.',''); ?></td>
                <td width="100" id="value_totalPost"><?=fn_number_format($gTotalCostPost,0,'.',''); ?></td>
                <td width="70">&nbsp;</td><!--890-->
                
                <td width="80" id="value_yarnSD"><?=fn_number_format($gYarnCostSD,0,'.',''); ?></td>
                <td width="80" id="value_dyeingSD"><?=fn_number_format($gDyeingCostSD,0,'.',''); ?></td>
                <td width="80" id="value_knittingSD"><?=fn_number_format($gKnittingCostSD,0,'.',''); ?></td>
                <td width="80" id="value_fabricSD"><?=fn_number_format($gFabricCostSD,0,'.',''); ?></td>
                <td width="80" id="value_otherFabSD"><?=fn_number_format($gOtherFabCostSD,0,'.',''); ?></td>
                <td width="80" id="value_accSD"><?=fn_number_format($gAccCostSD,0,'.',''); ?></td>
                <td width="80" id="value_emblWashSD"><?=fn_number_format($gEmblWashCostSD,0,'.',''); ?></td>
                <td width="80" id="value_testSD"><?=fn_number_format($gTestCostSD,0,'.',''); ?></td>
                <td width="80" id="value_otherSD"><?=fn_number_format($gOtherCostSD,0,'.',''); ?></td>
                <td width="100" id="value_totalSD"><?=fn_number_format($gTotalCostSD,0,'.',''); ?></td>
                <td width="70">&nbsp;</td><!--890-->
                
                <td width="80" id="value_shipQty"><?=fn_number_format($gShipmentQty,0,'.',''); ?></td>
                <td width="70">&nbsp;</td>
                <td width="70" id="value_commission"><?=fn_number_format($gCommisionCost,0,'.',''); ?></td>
                <td width="80" id="value_shipVal"><?=fn_number_format($gShipmentValue,0,'.',''); ?></td>
                <td width="80">&nbsp;</td><!--380-->
                
                <td width="70">&nbsp;</td>
                <td width="70" id="value_cmBom"><?=fn_number_format($gTotalCmCostBom,0,'.',''); ?></td>
                
                <td width="70">&nbsp;</td>
                <td width="70" id="value_cmPost"><?=fn_number_format($gTotalCmCostPost,0,'.',''); ?></td>
                
                <td width="70" id="value_shortQty"><?=fn_number_format($gShortShipQty,0,'.',''); ?></td>
                <td width="70" id="value_shortVal"><?=fn_number_format($gShortShipValue,0,'.',''); ?></td>
                
                <td width="70" id="value_excessQty"><?=fn_number_format($gExcessShipQty,0,'.',''); ?></td>
                <td width="70" id="value_excessVal"><?=fn_number_format($gExcessShipValue,0,'.',''); ?></td>
                
                <td width="70" id="value_upcharge"><?=fn_number_format($gUpCharge,0,'.',''); ?></td>
                <td width="70" id="value_discount"><?=fn_number_format($gDiscount,0,'.',''); ?></td><!--700-->
                
                <td width="80" id="value_adjCm"><?=fn_number_format($gAdjustedCm,0,'.',''); ?></td>
                <td width="80">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="80" id="value_prevShipQty"><?=fn_number_format($gPreviousShipQty,0,'.',''); ?></td>
                <td width="80" id="value_totAgentComm"><?=fn_number_format($gtTotAgentCommission,0,'.',''); ?></td>
                <td width="80" id="value_inHouseQty"><?=fn_number_format($gInHouseQty,0,'.',''); ?></td>
                <td width="80" id="value_subconQty"><?=fn_number_format($gSubconQty,0,'.',''); ?></td>
                <td width="80" id="value_yarnIssueQty"><?=fn_number_format($gYarnIssueQty,0,'.',''); ?></td>
                <td width="80" id="value_fabricIssueCut"><?=fn_number_format($gFabricIssueCut,0,'.',''); ?></td>
                
                <td>&nbsp;</td>
            </tr>
		</table>
    </fieldset>
	<?
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
    echo "$html****$filename"; 
    exit();
}
?>
