<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
require_once('../../../../includes/class4/class.conditions.php');
require_once('../../../../includes/class4/class.reports.php');
require_once('../../../../includes/class4/class.yarns.php');
require_once('../../../../includes/class4/class.conversions.php');
require_once('../../../../includes/class4/class.emblishments.php');
require_once('../../../../includes/class4/class.commisions.php');
require_once('../../../../includes/class4/class.commercials.php');
require_once('../../../../includes/class4/class.others.php');
require_once('../../../../includes/class4/class.trims.php');
require_once('../../../../includes/class4/class.fabrics.php');
require_once('../../../../includes/class4/class.washes.php');


$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
//--------------------------------------------------------------------------------------------------------------------
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];


if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "");     	 
	exit();
}

$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
$yarn_count_library=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
$team_member_arr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
//$order_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number");
$approve_arr=return_library_array( "select job_no, approved from wo_pre_cost_mst", "job_no", "approved");

if($action=="print_button_variable_setting")
{
	$print_report_format=0;
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=11 and report_id=18 and is_deleted=0 and status_active=1");
	echo "document.getElementById('report_ids').value = '".$print_report_format."';\n";
	echo "print_report_button_setting('".$print_report_format."');\n";
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
                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+'<? echo $cbo_year_id; ?>'+'**'+'<? echo $cbo_month_id; ?>', 'create_job_no_search_list_view', 'search_div', 'qcmargin_vs_budget_cost_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
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
	if($db_type==0) $year_field="YEAR(insert_date) as year"; 
	else if($db_type==2) $year_field="to_char(insert_date,'YYYY') as year";
	else $year_field="";
	
	if($db_type==0)
	{
		if($year_id!=0) $year_cond=" and year(insert_date)=$year_id"; else $year_cond="";	
	}
	else if($db_type==2)
	{
		$year_field_con=" and to_char(insert_date,'YYYY')";
		if($year_id!=0) $year_cond="$year_field_con=$year_id"; else $year_cond="";	
	}
	//if($month_id!=0) $month_cond=" and month(insert_date)=$month_id"; else $month_cond="";
	$arr=array (0=>$company_arr,1=>$buyer_arr);
	$sql= "select id, job_no, job_no_prefix_num, company_name, buyer_name, style_ref_no, $year_field from wo_po_details_master where status_active=1 and is_deleted=0 and company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond  order by job_no";
	
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Job No,Year,Style Ref. No", "120,130,80,60","600","240",0, $sql , "js_set_value", "id,job_no_prefix_num", "", 1, "company_name,buyer_name,0,0,0", $arr , "company_name,buyer_name,job_no_prefix_num,year,style_ref_no", "",'','0,0,0,0,0','') ;
	exit(); 
} // Job Search end

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_name=str_replace("'","",$cbo_company_name);
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
	}

	$cbo_year=str_replace("'","",$cbo_year);
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
	if($db_type==2)
	{
		$date_from=change_date_format($date_from,'yyyy-mm-dd','-',1);
		$date_to2=change_date_format($date_to2,'yyyy-mm-dd','-',1);
	}
	$date_cond='';
	if(str_replace("'","",$cbo_search_date)==2)
	{
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			if($db_type==0)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
			}
			else if($db_type==2)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
			}
			$date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'";
		}
	}
	else if(str_replace("'","",$cbo_search_date)==1)
	{
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			if($db_type==0)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
			}
			else if($db_type==2)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
			}
			$date_cond=" and c.costing_date between '$start_date' and '$end_date'";
		}
	}		
	$job_no=str_replace("'","",$txt_job_no);
	if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in ($job_no) ";
	$job_id=str_replace("'","",$txt_job_id);
	if ($job_id=="") $job_id_cond=""; else $job_id_cond=" and a.id in ($job_no) ";
	$main_data=sql_select("SELECT a.id as job_id, a.job_no_prefix_num,a.set_smv, b.insert_date, a.job_no, a.company_name, a.buyer_name, a.style_ref_no, b.is_confirmed, a.quotation_id, a.agent_name, a.avg_unit_price, a.dealing_marchant, a.gmts_item_id, a.total_set_qnty as ratio, b.plan_cut, b.id as po_id, b.po_number, b.pub_shipment_date, b.po_received_date, b.po_quantity, b.unit_price, b.grouping, b.file_no, b.po_total_price, c.costing_date from wo_po_details_master a join wo_po_break_down b on a.id=b.job_id join wo_pre_cost_mst c on a.id=c.job_id where c.entry_from=158  and a.company_name='$company_name' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $date_cond $buyer_id_cond $year_cond $job_no_cond order by b.pub_shipment_date, b.id");

	foreach ($main_data as $row) {
		$job_id_arr[$row[csf('job_id')]] = $row[csf('job_id')];
		$qc_id_arr[$row[csf('quotation_id')]] =$row[csf('quotation_id')];
	}
	$qc_arr_cond=array_chunk($qc_id_arr,1000, true);
	$qc_cond_for_in="";
	$qc_cond_for_in1="";
	$q=0;
	foreach($qc_arr_cond as $key=>$value)
	{
	   if($q==0)
	   {
		$qc_cond_for_in=" and b.mst_id  in(".implode(",",$value).")";
		$qc_cond_for_in1=" and qc_no in(".implode(",",$value).")";
	
	   }
	   else
	   {
		$qc_cond_for_in.=" or b.mst_id  in(".implode(",",$value).")";
		$qc_cond_for_in1.=" or qc_no in(".implode(",",$value).")";
		
	   }
	   $q++;
	}

	$jobid_arr_cond=array_chunk($job_id_arr,1000, true);
	$job_id_cond_in="";
	$k=0;
	foreach($jobid_arr_cond as $key=>$value)
	{
	   if($k==0)
	   {
		$job_id_cond_in=" and a.job_id  in(".implode(",",$value).")";
	
	   }
	   else
	   {
		$job_id_cond_in.=" or a.job_id  in(".implode(",",$value).")";
		
	   }
	   $k++;
	}

	$qc_yarn_cost_sql = sql_select("SELECT a.actual_rate, a.tot_cons, a.ex_percent, b.particular_type_id, b.mst_id from qc_margin_dtls a join qc_cons_rate_dtls b on a.rate_data_id=b.id where a.type=1 and a.status_active=1 and a.is_deleted=0 and b.type=1 and b.status_active=1 and b.is_deleted=0 $qc_cond_for_in");
	$qc_cost_arr=array();
	foreach ($qc_yarn_cost_sql as $row) {
		$total_cons= $row[csf('tot_cons')]*($row[csf('ex_percent')]/100);
		$total_value= $total_cons*$row[csf('actual_rate')];
		$qc_cost_arr[$row[csf('mst_id')]]['yarn']+=$total_value;
	}
	$qc_margin_attr = array('trim_cost', 'test_cost', 'mis_offer_qty', 'other_cost', 'cm_cost', 'fob', 'total_yarn_cost', 'knitting_cost', 'df_cost', 'aop_cost', 'fabricpurchasekg', 'fabricpurchaseyds', 'commercial_cost','special_operation','com_cost', 'frieght_cost','avl_min','smv');
	$qc_margin_sql= sql_select("SELECT qc_no, accessories_cost as trim_cost, lab_test_cost as test_cost, mis_offer_qty, other_cost, cm_cost, fob, total_yarn_cost, knitting_cost, df_cost, aop_cost, fabricpurchasekg, fabricpurchaseyds, commercial_cost, special_operation, com_cost, frieght_cost, avl_min, smv from qc_margin_mst where status_active=1 and is_deleted=0 $qc_cond_for_in1");
	foreach ($qc_margin_sql as $row) {
		foreach ($qc_margin_attr as $att) {
			$qc_cost_arr[$row[csf('qc_no')]][$att] = $row[csf($att)];
		}
	}

	$budget_cost_dtls=sql_select("SELECT c.set_smv as sew_smv, b.job_no, b.job_id,  b.costing_per_id, b.trims_cost, b.embel_cost, b.cm_cost, b.commission, b.common_oh, b.lab_test, b.inspection, b.freight, b.comm_cost, b.certificate_pre_cost, b.currier_pre_cost, a.sew_effi_percent from  wo_pre_cost_mst a join wo_pre_cost_dtls b on a.job_id=b.job_id join wo_po_details_master c on c.id=a.job_id where a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $job_id_cond_in");
	$budget_dtls_attr=array('costing_per_id', 'trims_cost', 'embel_cost', 'cm_cost', 'commission', 'common_oh', 'lab_test', 'inspection', 'freight', 'comm_cost', 'certificate_pre_cost', 'currier_pre_cost','sew_smv','sew_effi_percent');
	$budget_dtls_arr=array();
	foreach ($budget_cost_dtls as $row) {
		foreach ($budget_dtls_attr as $att) {
			$budget_dtls_arr[$row[csf('job_id')]][$att] = $row[csf($att)];
		}
		
	}


	$condition= new condition();
	$condition->company_name("=$cbo_company_name");
	if(str_replace("'","",$cbo_buyer_name)>0){
	  $condition->buyer_name("=$cbo_buyer_name");
	}
	$condition->jobid_in(implode(",", $job_id_arr));
	$condition->init();
	$yarn= new yarn($condition);
	$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
	$yarn= new yarn($condition);
	$yarn_req_qty_arr=$yarn->getOrderWiseYarnQtyArray();
	$trims= new trims($condition);
	$trims_costing_arr=$trims->getAmountArray_by_order();

	$fabric= new fabric($condition);
	$fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
	$conversion= new conversion($condition);
	$conversion_costing_arr_process=$conversion->getAmountArray_by_orderAndProcess();
	
	$emblishment= new emblishment($condition);
	$emblishment_costing_arr_name=$emblishment->getAmountArray_by_orderAndEmbname();
	$commission= new commision($condition);
	$commission_costing_arr=$commission->getAmountArray_by_orderAndItemid();
	$commercial= new commercial($condition);
	$commercial_costing_arr=$commercial->getAmountArray_by_order();
	$other= new other($condition);
	$other_costing_arr=$other->getAmountArray_by_order();
	$wash= new wash($condition);
	$emblishment_costing_arr_name_wash=$wash->getAmountArray_by_orderAndEmbname();
	
	$knit_cost_arr=array(1,2,3,4);
	$fabric_dyeingCost_arr=array(25,31,26,32,60,61,62,63,72,80,81,84,85,86,87,38,39,74,78,79,101,133,137,138,139,146,147,149);
	$aop_cost_arr=array(35,36,37,40);
	$fab_finish_cost_arr=array(33,34,38,63,65,66,67,68,69,70,71,72,73,75,76,77,88,82,89,90,91,92,93,94,128,129,135,136,141,143,150,151,155,156,157,145,82,89,132,144);
	$washing_cost_arr=array(140,142,148,64);
	/*echo '<pre>';
	print_r($qc_cost_arr); die;*/
	foreach ($main_data as $row) {
		$order_value=$row[csf('po_total_price')];
		$quotation_id= $row[csf('quotation_id')];
		$po_qty= $row[csf('po_quantity')];
		$order_qty_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
		$dzn_qnty_p=12;
		$dzn_qnty_p=$dzn_qnty_p*$row[csf('ratio')];
		$qc_yarn_cost= $order_qty_pcs/$dzn_qnty_p*$qc_cost_arr[$quotation_id]['yarn'];
		$qc_fabricpurchasekg= $order_qty_pcs/$dzn_qnty_p*$qc_cost_arr[$quotation_id]['fabricpurchasekg'];
		$qc_fabricpurchaseyds= $order_qty_pcs/$dzn_qnty_p*$qc_cost_arr[$quotation_id]['fabricpurchaseyds'];
		$qc_fabricpurchase_cost=$qc_fabricpurchasekg+$qc_fabricpurchaseyds;
		$qc_knitting_cost= $order_qty_pcs/$dzn_qnty_p*$qc_cost_arr[$quotation_id]['knitting_cost'];
		$qc_fd_cost= $order_qty_pcs/$dzn_qnty_p*$qc_cost_arr[$quotation_id]['df_cost'];
		$qc_aop_cost= $order_qty_pcs/$dzn_qnty_p*$qc_cost_arr[$quotation_id]['aop_cost'];
		$qc_trim_cost= $order_qty_pcs/$dzn_qnty_p*$qc_cost_arr[$quotation_id]['trim_cost'];
		$qc_emb_cost= $order_qty_pcs/$dzn_qnty_p*$qc_cost_arr[$quotation_id]['special_operation'];
		$qc_commercial_cost= $order_qty_pcs/$dzn_qnty_p*$qc_cost_arr[$quotation_id]['commercial_cost'];
		$qc_commision_cost= $order_qty_pcs/$dzn_qnty_p*$qc_cost_arr[$quotation_id]['com_cost'];
		$qc_lab_cost= $order_qty_pcs/$dzn_qnty_p*$qc_cost_arr[$quotation_id]['test_cost'];
		$qc_frieght_cost= $order_qty_pcs/$dzn_qnty_p*$qc_cost_arr[$quotation_id]['frieght_cost'];
		$qc_cm_cost= $order_qty_pcs/$dzn_qnty_p*$qc_cost_arr[$quotation_id]['cm_cost'];
		$qc_total_cost= $qc_yarn_cost+$qc_fabricpurchasekg+$qc_fabricpurchaseyds+$qc_knitting_cost+$qc_fd_cost+$qc_aop_cost+$qc_trim_cost+$qc_emb_cost+$qc_commercial_cost+$qc_commision_cost+$qc_lab_cost+$qc_frieght_cost+$qc_cm_cost;
		$qc_profit_loss = $order_value-$qc_total_cost;
		$qc_profit_loss_per= $qc_profit_loss*100/$order_value;

		$qc_mis_cost= $order_qty_pcs/$dzn_qnty_p*$qc_cost_arr[$quotation_id]['mis_offer_qty'];
		$qc_other_cost= $order_qty_pcs/$dzn_qnty_p*$qc_cost_arr[$quotation_id]['other_cost'];

		$total_qc_yarn_cost+=$qc_yarn_cost;
		$total_qc_fabricpurchase_cost+=$qc_fabricpurchase_cost;
		$total_qc_knitting_cost+=$qc_knitting_cost;
		$total_qc_fd_cost+=$qc_fd_cost;
		$total_qc_aop_cost+=$qc_aop_cost;
		$total_qc_trim_cost+=$qc_trim_cost;
		$total_qc_emb_cost+=$qc_emb_cost;
		$total_qc_commercial_cost+=$qc_commercial_cost;
		$total_qc_commision_cost+=$qc_commision_cost;
		$total_qc_lab_cost+=$qc_lab_cost;
		$total_qc_frieght_cost+=$qc_frieght_cost;
		$total_qc_cm_cost+=$qc_cm_cost;
		$total_qc_total_cost+=$qc_total_cost;
		$total_qc_profit_loss+=$qc_profit_loss;
		$total_qc_profit_loss_per+=$qc_profit_loss_per;
		$total_qc_mis_cost+=$qc_mis_cost;
		$total_qc_other_cost+=$qc_other_cost;

		$total_qc_material_value+=$qc_yarn_cost+$qc_fabricpurchase_cost+$qc_knitting_cost+$qc_fd_cost+$qc_aop_cost;

		$emblishment_arr=array(1,2,3,4,5);
	  	$dzn_qnty=0;
        $costing_per_id=$budget_dtls_arr[$row[csf('job_id')]]['costing_per_id'];
        if($costing_per_id==1) $dzn_qnty=12;
        else if($costing_per_id==3) $dzn_qnty=12*2;
        else if($costing_per_id==4) $dzn_qnty=12*3;
        else if($costing_per_id==5) $dzn_qnty=12*4;
        else $dzn_qnty=1;
		$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
		$order_qty_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
		$commercial_cost_dzn=$budget_dtls_arr[$row[csf('job_id')]]['comm_cost'];
		$commercial_cost=($commercial_cost_dzn/$dzn_qnty)*$order_qty_pcs;
		$budget_smv= $budget_dtls_arr[$row[csf('job_id')]]['sew_smv'];
		$sew_effi_percent= $budget_dtls_arr[$row[csf('job_id')]]['sew_effi_percent'];

		$avl_min = ($order_qty_pcs*$budget_smv)/$sew_effi_percent*100;

	  	$budget_yarn_cost=$yarn_costing_arr[$row[csf('po_id')]];
	  	$fab_purchase_knit=array_sum($fabric_costing_arr['knit']['grey'][$row[csf('po_id')]]);
		if(is_infinite($fab_purchase_knit) || is_nan($fab_purchase_knit)){$fab_purchase_knit=0;}
		$fab_purchase_woven=array_sum($fabric_costing_arr['woven']['grey'][$row[csf('po_id')]]);
		if(is_infinite($fab_purchase_woven) || is_nan($fab_purchase_woven)){$fab_purchase_woven=0;}
		$budget_fab_purchase=$fab_purchase_knit+$fab_purchase_woven;
		$budget_knit_cost=0;
		foreach($knit_cost_arr as $process_id)
		{
			$budget_knit_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$process_id]);	
		}
		$fabric_dyeing_cost=0;
		foreach($fabric_dyeingCost_arr as $fab_process_id)
		{
			$fabric_dyeing_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$fab_process_id]);	
		}
		$all_over_cost=0;
		foreach($aop_cost_arr as $aop_process_id)
		{
			$all_over_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$aop_process_id]);	
		}
		$trim_amount= $trims_costing_arr[$row[csf('po_id')]];
		if(is_infinite($trim_amount) || is_nan($trim_amount)){$trim_amount=0;}

		foreach ($emblishment_arr as $emb_id) {
			$emblishment_amount+=$emblishment_costing_arr_name[$row[csf('po_id')]][$emb_id];
		}
		if(is_infinite($emblishment_amount) || is_nan($emblishment_amount)){$emblishment_amount=0;}

		$foreign=$commission_costing_arr[$row[csf('po_id')]][1];
		if(is_infinite($foreign) || is_nan($foreign)){$foreign=0;}
		$local=$commission_costing_arr[$row[csf('po_id')]][2];
		if(is_infinite($local) || is_nan($local)){$local=0;}
		$budget_commision_cost = $foreign+$local;

		$test_cost=$other_costing_arr[$row[csf('po_id')]]['lab_test'];
		if(is_infinite($test_cost) || is_nan($test_cost)){$test_cost=0;}

		$freight_cost=$other_costing_arr[$row[csf('po_id')]]['freight'];
		if(is_infinite($freight_cost) || is_nan($freight_cost)){$freight_cost=0;}
		$inspection=$other_costing_arr[$row[csf('po_id')]]['inspection'];
		if(is_infinite($inspection) || is_nan($inspection)){$inspection=0;}
		$currier_cost=$other_costing_arr[$row[csf('po_id')]]['currier_pre_cost'];
		if(is_infinite($currier_cost) || is_nan($currier_cost)){$currier_cost=0;}
		$cm_cost=$other_costing_arr[$row[csf('po_id')]]['cm_cost'];
		if(is_infinite($cm_cost) || is_nan($cm_cost)){$cm_cost=0;}
		$cm_cost_dzn=($cm_cost/$order_qty_pcs)*12;
		if(is_infinite($cm_cost_dzn) || is_nan($cm_cost_dzn)){$cm_cost_dzn=0;}
		$budget_total_cost=$budget_yarn_cost+$budget_fab_purchase+$budget_knit_cost+$fabric_dyeing_cost+$all_over_cost+$trim_amount+$emblishment_amount+$commercial_cost+$budget_commision_cost+$test_cost+$freight_cost+$inspection+$currier_cost+$cm_cost;            				
		$budget_profit_loss=$order_value-$budget_total_cost;
		$budget_profit_loss_per= $budget_profit_loss*100/$order_value;

		$total_budget_yarn_cost += $budget_yarn_cost;
		$total_budget_fab_purchase += $budget_fab_purchase;
		$total_budget_knit_cost += $budget_knit_cost;
		$total_fabric_dyeing_cost += $fabric_dyeing_cost;
		$total_all_over_cost += $all_over_cost;
		$total_trim_amount += $trim_amount;
		$total_emblishment_amount += $emblishment_amount;
		$total_commercial_cost += $commercial_cost;
		$total_budget_commision_cost += $budget_commision_cost;
		$total_test_cost += $test_cost;
		$total_freight_cost += $freight_cost;
		$total_inspection_cost += $inspection;
		$total_currier_cost += $currier_cost;
		$total_cm_cost_dzn += $cm_cost_dzn;
		$total_cm_cost += $cm_cost;
		$total_budget_total_cost += $budget_total_cost;
		$total_budget_profit_loss += $budget_profit_loss;
		$total_budget_profit_loss_per += $budget_profit_loss_per;

		$total_budget_material_value+=$budget_yarn_cost+$budget_fab_purchase+$budget_knit_cost+$fabric_dyeing_cost+$all_over_cost;

		$yarn_cost_variance= $qc_yarn_cost-$budget_yarn_cost;
  		$fabricpurchase_cost_variance= $qc_fabricpurchase_cost-$budget_fab_purchase;
  		$knitting_cost_variance= $qc_knitting_cost-$budget_knit_cost;
  		$fd_cost_variance= $qc_fd_cost-$fabric_dyeing_cost;
  		$aop_cost_variance= $qc_aop_cost-$all_over_cost;
  		$trim_cost_variance= $qc_trim_cost-$trim_amount;
  		$emb_cost_variance= $qc_emb_cost-$emblishment_amount;
  		$commercial_cost_variance= $qc_commercial_cost-$commercial_cost;
  		$commision_cost_variance= $qc_commision_cost-$budget_commision_cost;
  		$lab_cost_variance= $qc_lab_cost-$test_cost;
  		$freight_cost_variance= $qc_frieght_cost-$freight_cost;
  		$inspection_cost_variance= 0-$inspection;
  		$currier_cost_variance= 0-$currier_cost;
  		$cm_cost_dzn_variance= $qc_cost_arr[$quotation_id]['cm_cost']-$cm_cost_dzn;
  		$cm_cost_variance= $qc_cm_cost-$cm_cost;
  		$total_cost_variance= $qc_total_cost-$budget_total_cost;
  		$profit_variance= $budget_profit_loss-$qc_profit_loss;
  		$profit_per_variance= $qc_profit_loss_per-$budget_profit_loss_per;
  		$mis_cost_variance= 0-$qc_mis_cost;
  		$other_cost_variance= 0-$qc_other_cost;

  		$total_yarn_cost_variance += $yarn_cost_variance;
  		$total_fabricpurchase_cost_variance += $fabricpurchase_cost_variance;
  		$total_knitting_cost_variance += $knitting_cost_variance;
  		$total_fd_cost_variance += $fd_cost_variance;
  		$total_aop_cost_variance += $aop_cost_variance;
  		$total_trim_cost_variance += $trim_cost_variance;
  		$total_emb_cost_variance += $emb_cost_variance;
  		$total_commercial_cost_variance += $commercial_cost_variance;
  		$total_commision_cost_variance += $commision_cost_variance;
  		$total_lab_cost_variance += $lab_cost_variance;
  		$total_freight_cost_variance += $freight_cost_variance;
  		$total_inspection_cost_variance += $inspection_cost_variance;
  		$total_currier_cost_variance += $currier_cost_variance;
  		$total_cm_cost_dzn_variance += $cm_cost_dzn_variance;
  		$total_cm_cost_variance += $cm_cost_variance;
  		$total_total_cost_variance += $total_cost_variance;
  		$total_profit_variance += $profit_variance;
  		$total_profit_per_variance += $profit_per_variance;
  		$total_mis_cost_variance += $mis_cost_variance;
  		$total_other_cost_variance += $other_cost_variance;

  		$total_order_value+=$order_value;
  		
	}	
	$style1="#E9F3FF"; 
	$style="#FFFFFF";
	ob_start();
	?>
	<div style="width:4870px;">
        <div style="width:900px; margin-top: 10px" align="left">
            <table width="800" border="1" rules="all" class="rpt_table" cellpadding="0" cellspacing="2">
                <thead align="center">
                    <tr><th colspan="8">QC Margin Vs Budget Variance Summary</th></tr>
                    <tr>
                        <th width="20">SL</th>
                        <th width="140">Particulars</th>
                        <th width="110">QC Margin</th>
                        <th width="80">% On Order Value</th>
                        <th width="110">Budgeted Cost</th>
                        <th width="80">% On Order Value</th>
                        <th width="100">Variance</th>
                        <th>% On Mkt. Cost</th>
                    </tr>
                </thead>
                <tr bgcolor="<?  echo $style1; ?>">
                    <td>1</td><td>Yarn Cost</td>
                    <td align="right"><? echo number_format($total_qc_yarn_cost,2) ?></td>
                    <td align="right"><? echo number_format(($total_qc_yarn_cost/$total_order_value)*100,2) ?></td>
                    <td align="right"><? echo number_format($total_budget_yarn_cost,2) ?></td>
                    <td align="right"><? echo number_format(($total_budget_yarn_cost/$total_order_value)*100,2) ?></td>
                    <td align="right"><? echo number_format($total_yarn_cost_variance,2) ?></td>
                    <td align="right"><? echo number_format(($total_yarn_cost_variance/$total_qc_yarn_cost)*100,2) ?></td>
                </tr>
                <tr bgcolor="<?  echo $style; ?>">
                    <td>2</td><td>Fabric Purchase</td>
                    <td align="right"><? echo number_format($total_qc_fabricpurchase_cost,2) ?></td>
                    <td align="right"><? echo number_format(($total_qc_fabricpurchase_cost/$total_order_value)*100,2) ?></td>
                    <td align="right"><? echo number_format($total_budget_fab_purchase,2) ?></td>
                    <td align="right"><? echo number_format(($total_budget_fab_purchase/$total_order_value)*100,2) ?></td>
                    <td align="right"><? echo number_format($total_fabricpurchase_cost_variance,2) ?></td>
                    <td align="right"><? echo number_format(($total_fabricpurchase_cost_variance/$total_qc_fabricpurchase_cost)*100,2) ?></td>
                </tr>
                <tr bgcolor="<?  echo $style1; ?>">
                    <td>3</td><td>Knitting Cost</td>
                    <td align="right"><? echo number_format($total_qc_knitting_cost,2) ?></td>
                    <td align="right"><? echo number_format(($total_qc_knitting_cost/$total_order_value)*100,2) ?></td>
                    <td align="right"><? echo number_format($total_budget_knit_cost,2) ?></td>
                    <td align="right"><? echo number_format(($total_budget_knit_cost/$total_order_value)*100,2) ?></td>
                    <td align="right"><? echo number_format($total_knitting_cost_variance,2) ?></td>
                    <td align="right"><? echo number_format(($total_knitting_cost_variance/$total_qc_knitting_cost)*100,2) ?></td>
                </tr>
                <tr bgcolor="<?  echo $style1; ?>">
                    <td>4</td><td>AOP Cost</td>
                    <td align="right"><? echo number_format($total_qc_aop_cost,2) ?></td>
                    <td align="right"><? echo number_format(($total_qc_aop_cost/$total_order_value)*100,2) ?></td>
                    <td align="right"><? echo number_format($total_all_over_cost,2) ?></td>
                    <td align="right"><? echo number_format(($total_all_over_cost/$total_order_value)*100,2) ?></td>
                    <td align="right"><? echo number_format($total_aop_cost_variance,2) ?></td>
                    <td align="right"><? echo number_format(($total_aop_cost_variance/$total_qc_aop_cost)*100,2) ?></td>
                </tr>
                <tr bgcolor="<?  echo $style; ?>">
                    <td>5</td><td>Dyeing & Finishing Cost</td>
                    <td align="right"><? echo number_format($total_qc_fd_cost,2) ?></td>
                    <td align="right"><? echo number_format(($total_qc_fd_cost/$total_order_value)*100,2) ?></td>
                    <td align="right"><? echo number_format($total_fabric_dyeing_cost,2) ?></td>
                    <td align="right"><? echo number_format(($total_fabric_dyeing_cost/$total_order_value)*100,2) ?></td>
                    <td align="right"><? echo number_format($total_fd_cost_variance,2) ?></td>
                    <td align="right"><? echo number_format(($total_fd_cost_variance/$total_qc_fd_cost)*100,2) ?></td>
                </tr>
                <tr bgcolor="#CCCCCC">
                    <td colspan="2"><strong>Total Material & Service Cost</strong></td>
                    <td align="right"><? echo number_format($total_qc_material_value,2); ?></td>
                    <td align="right"><? echo number_format(($total_qc_material_value/$total_order_value)*100,2) ?></td>
                    <td align="right"><? echo number_format($total_budget_material_value,2) ?></td>
                    <td align="right"><? echo number_format(($total_budget_material_value/$total_order_value)*100,2) ?></td>
                    <td align="right"><? echo number_format($total_qc_material_value-$total_budget_material_value,2) ?></td>
                    <td align="right"><? $mvariance=$total_qc_material_value-$total_budget_material_value; echo number_format(($mvariance/$total_qc_material_value)*100,2) ?></td>
                </tr>
                <tr bgcolor="<?  echo $style1; ?>">
                    <td>6</td><td>Trims Cost</td>
                    <td align="right"><? echo number_format($total_qc_trim_cost,2) ?></td>
                    <td align="right"><? echo number_format(($total_qc_trim_cost/$total_order_value)*100,2) ?></td>
                    <td align="right"><? echo number_format($total_trim_amount,2) ?></td>
                    <td align="right"><? echo number_format(($total_trim_amount/$order_value)*100,2) ?></td>
                    <td align="right"><? echo number_format($total_trim_cost_variance,2) ?></td>
                    <td align="right"><? echo number_format(($total_trim_cost_variance/$total_qc_trim_cost)*100,2) ?></td>
                </tr>
                <tr bgcolor="<? echo $style; ?>">
                    <td>7</td><td>EMB Cost</td>
                    <td align="right"><? echo number_format($total_qc_emb_cost,2) ?></td>
                    <td align="right"><? echo number_format(($total_qc_emb_cost/$total_order_value)*100,2) ?></td>
                    <td align="right"><? echo number_format($total_emblishment_amount,2) ?></td>
                    <td align="right"><? echo number_format(($total_emblishment_amount/$order_value)*100,2) ?></td>
                    <td align="right"><? echo number_format($total_emb_cost_variance,2) ?></td>
                    <td align="right"><? echo number_format(($total_emb_cost_variance/$total_qc_emb_cost)*100,2) ?></td>
                </tr>
                <tr bgcolor="<? echo $style1; ?>">
                    <td>9</td><td>Commercial Cost</td>
                    <td align="right"><? echo number_format($total_qc_commercial_cost,2) ?></td>
                    <td align="right"><? echo number_format(($total_qc_commercial_cost/$total_order_value)*100,2) ?></td>
                    <td align="right"><? echo number_format($total_commercial_cost,2) ?></td>
                    <td align="right"><? echo number_format(($total_commercial_cost/$order_value)*100,2) ?></td>
                    <td align="right"><? echo number_format($total_commercial_cost_variance,2) ?></td>
                    <td align="right"><? echo number_format(($total_commercial_cost_variance/$total_qc_commercial_cost)*100,2) ?></td>
                </tr>
                <tr bgcolor="<? echo $style; ?>">
                    <td>10</td><td>Commision Cost</td>
                    <td align="right"><? echo number_format($total_qc_commision_cost,2) ?></td>
                    <td align="right"><? echo number_format(($total_qc_commision_cost/$total_order_value)*100,2) ?></td>
                    <td align="right"><? echo number_format($total_budget_commision_cost,2) ?></td>
                    <td align="right"><? echo number_format(($total_budget_commision_cost/$total_order_value)*100,2) ?></td>
                    <td align="right"><? echo number_format($total_commision_cost_variance,2) ?></td>
                    <td align="right"><? echo number_format(($total_commision_cost_variance/$total_qc_commision_cost)*100,2) ?></td>
                </tr>
                <tr bgcolor="<? echo $style1; ?>">
                    <td>11</td><td>Testing Cost</td>
                    <td align="right"><? echo number_format($total_qc_lab_cost,2) ?></td>
                    <td align="right"><? echo number_format(($total_qc_lab_cost/$total_order_value)*100,2) ?></td>
                    <td align="right"><? echo number_format($total_test_cost,2) ?></td>
                    <td align="right"><? echo number_format(($total_test_cost/$total_order_value)*100,2) ?></td>
                    <td align="right"><? echo number_format($total_lab_cost_variance,2) ?></td>
                    <td align="right"><? echo number_format(($total_lab_cost_variance/$total_qc_lab_cost)*100,2) ?></td>
                </tr>
                <tr bgcolor="<? echo $style; ?>">
                    <td>12</td><td>Freight Cost</td>
                    <td align="right"><? echo number_format($total_qc_frieght_cost,2) ?></td>
                    <td align="right"><? echo number_format(($total_qc_frieght_cost/$total_order_value)*100,2) ?></td>
                    <td align="right"><? echo number_format($total_freight_cost,2) ?></td>
                    <td align="right"><? echo number_format(($total_freight_cost/$total_order_value)*100,2) ?></td>
                    <td align="right"><? echo number_format($total_freight_cost_variance,2) ?></td>
                    <td align="right"><? echo number_format(($total_freight_cost_variance/$total_qc_frieght_cost)*100,2) ?></td>
                </tr>
                <tr bgcolor="<? echo $style1; ?>">
                    <td>13</td><td>Inspection Cost</td>
                    <td align="right">0</td>
                    <td align="right">0</td>
                    <td align="right"><? echo number_format($total_inspection_cost,2) ?></td>
                    <td align="right"><? echo number_format(($total_inspection_cost/$total_order_value)*100,2) ?></td>
                    <td align="right"><? echo number_format($total_inspection_cost_variance,2) ?></td>
                    <td align="right">0</td>
                </tr>
                <tr bgcolor="<? echo $style; ?>">
                    <td>14</td><td>Courier Cost</td>
                    <td align="right">0</td>
                    <td align="right">0</td>
                    <td align="right"><? echo number_format($total_currier_cost,2) ?></td>
                    <td align="right"><? echo number_format(($total_currier_cost/$total_order_value)*100,2) ?></td>
                    <td align="right"><? echo number_format($total_currier_cost_variance,2) ?></td>
                    <td align="right">0</td>
                </tr>
                <tr bgcolor="<? echo $style1; ?>">
                    <td>14</td><td>CM Cost</td>
                    <td align="right"><? echo number_format($total_qc_cm_cost,2) ?></td>
                    <td align="right"><? echo number_format(($total_qc_cm_cost/$total_order_value)*100,2) ?></td>
                    <td align="right"><? echo number_format($total_cm_cost,2) ?></td>
                    <td align="right"><? echo number_format(($total_cm_cost/$total_order_value)*100,2) ?></td>
                    <td align="right"><? echo number_format($total_cm_cost_variance,2) ?></td>
                    <td align="right"><? echo number_format(($total_cm_cost_variance/$total_qc_cm_cost)*100,2) ?></td>
                </tr>
                <tr bgcolor="<? echo $style; ?>">
                    <td>15</td><td>Total Cost</td>
                    <td align="right"><? echo number_format($total_qc_total_cost,2) ?></td>
                    <td align="right"><? echo number_format(($total_qc_total_cost/$total_order_value)*100,2) ?></td>
                    <td align="right"><? echo number_format($total_budget_total_cost,2) ?></td>
                    <td align="right"><? echo number_format(($total_budget_total_cost/$total_order_value)*100,2) ?></td>
                    <td align="right"><? echo number_format($total_qc_total_cost-$total_budget_total_cost,2) ?></td>
                    <td align="right"><? $tvariance=$total_qc_total_cost-$total_budget_total_cost; echo number_format(($tvariance/$total_qc_total_cost)*100,2) ?></td>
                </tr>
                <tr bgcolor="<? echo $style1; ?>">
                    <td>16</td><td>Total Order Value</td>
                    <td align="right"><? echo number_format($total_order_value,2) ?></td>
                    <td align="right"><? echo number_format(($total_order_value/$total_order_value)*100,2) ?></td>
                    <td align="right"><? echo number_format($total_order_value,2) ?></td>
                    <td align="right"><? echo number_format(($total_order_value/$total_order_value)*100,2) ?></td>
                    <td align="right">0</td>
                    <td align="right">0</td>
                </tr>
                <tr bgcolor="<? echo $style; ?>">
                    <td>17</td><td>Profit/Loss </td>
                    <td align="right"><? echo number_format($total_qc_profit_loss,2) ?></td>
                    <td align="right"><? echo number_format(($total_qc_profit_loss/$total_order_value)*100,2) ?></td>
                    <td align="right"><? echo number_format($total_budget_profit_loss,2) ?></td>
                    <td align="right"><? echo number_format(($total_budget_profit_loss/$total_order_value)*100,2) ?></td>
                    <td align="right"><? echo number_format($total_profit_variance,2) ?></td>
                    <td align="right"><? echo number_format(($total_profit_variance/$total_order_value)*100,2) ?></td>
                </tr>
            </table>
        </div>
        <h3 align="left" id="accordion_h2" style="width:2500px" class="accordion_h" onClick="accordion_menu( this.id,'content_search_panel2', '')">Details Report</h3>
        	<fieldset style="width:2500px;" id="content_search_panel2">	
	            <table width="2500">
	                <tr class="form_caption">
	                    <td align="center"><strong><? echo $company_library[$company_name]; ?></strong></td>
	                </tr>
	                <tr class="form_caption">
	                    <td align="center"><strong>QC Margin Vs Budget Variance Details Report </strong></td>
	                </tr>
	            </table>
	            <table id="table_header_1" class="rpt_table" width="2500" cellpadding="0" cellspacing="0" border="1" rules="all">
	                <thead>
	                    <tr>
	                        <th width="40" rowspan="2">SL</th>
	                        <th width="80" rowspan="2">Buyer</th>
	                        <th width="80" rowspan="2">Job No</th>
	                        <th width="100" rowspan="2">Style</th>
	                        <th width="100" rowspan="2">Order No</th>
	                        <th width="80" rowspan="2">Costing Date</th>
	                        <th width="80" rowspan="2">Ship. Date</th>
	                        <th width="80" rowspan="2">Order Qty</th>
	                        <th width="80" rowspan="2">Avg Unit Price</th>
	                        <th width="80" rowspan="2">Order Value</th>
	                        <th width="100" rowspan="2">Particulars</th>
	                        <th colspan="5">Fabric Cost</th>
	                        <th width="80" rowspan="2">Trim Cost</th>
	                        <th width="80" rowspan="2">Embell. Cost</th>
	                        <th width="80" rowspan="2">Commercial Cost</th>
	                        <th width="80" rowspan="2">Commission</th>
	                        <th width="80" rowspan="2">Lab Test Cost</th>
	                        <th width="80" rowspan="2">Freight Cost</th>
	                        <th width="80" rowspan="2">Inspection Cost</th>
	                        <th width="80" rowspan="2">Courier Cost</th>
	                        <th width="80" rowspan="2">CM/DZN</th>
	                        <th width="80" rowspan="2">CM Cost</th>
	                        <th width="80" rowspan="2">Total Cost</th>
	                        <th width="80" rowspan="2">Profit/Loss</th>
	                        <th width="80" rowspan="2">Profit/Loss %</th>
	                        <th width="80" rowspan="2">SMV</th>
	                        <th width="" rowspan="2">AVL Min</th>
	                    </tr>
	                    <tr>
	                        <th width="80">Yarn Cost</th>
	                        <th width="80">Fabric Purchase</th>
	                        <th width="80">Knitting Cost</th>
	                        <th width="80">Fabric Dyeing Cost</th>
	                        <th width="80">All Over Print</th>
	                    </tr>
	                </thead>
            		<? $i=1;
            			$total_yarn_cost_variance=$total_fabricpurchase_cost_variance=$total_knitting_cost_variance=$total_fd_cost_variance= $total_aop_cost_variance =$total_trim_cost_variance=$total_emb_cost_variance=$total_commercial_cost_variance=$total_commision_cost_variance=$total_lab_cost_variance=$total_freight_cost_variance=$total_inspection_cost_variance=$total_currier_cost_variance=$total_cm_cost_dzn_variance=$total_cm_cost_variance=$total_total_cost_variance=$total_profit_variance=$total_profit_per_variance=$total_mis_cost_variance=$total_other_cost_variance=$total_budget_yarn_cost=$total_budget_fab_purchase=$total_budget_knit_cost=$total_fabric_dyeing_cost=$total_all_over_cost=	$total_trim_amount=$total_emblishment_amount =$total_commercial_cost=$total_budget_commision_cost =$total_test_cost =$total_freight_cost =$total_inspection_cost =$total_currier_cost =$total_cm_cost_dzn =$total_cm_cost =	$total_budget_total_cost =$total_budget_profit_loss =$total_budget_profit_loss_per=$total_qc_yarn_cost=$total_qc_fabricpurchase_cost=$total_qc_knitting_cost=$total_qc_fd_cost=$total_qc_aop_cost=	$total_qc_trim_cost=$total_qc_emb_cost=$total_qc_commercial_cost=$total_qc_commision_cost=$total_qc_lab_cost=	$total_qc_frieght_cost=$total_qc_cm_cost=$total_qc_total_cost=$total_qc_profit_loss=$total_qc_profit_loss_per=	$total_qc_mis_cost=$total_qc_other_cost=0;
            			foreach ($main_data as $row) {
            				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
            				$order_value=$row[csf('po_total_price')];
            				$quotation_id= $row[csf('quotation_id')];
            				$po_qty= $row[csf('po_quantity')];
            				$order_qty_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
            				$dzn_qnty_p=12;
            				$dzn_qnty_p=$dzn_qnty_p*$row[csf('ratio')];
            				$qc_yarn_cost= $order_qty_pcs/$dzn_qnty_p*$qc_cost_arr[$quotation_id]['yarn'];
            				$qc_fabricpurchasekg= $order_qty_pcs/$dzn_qnty_p*$qc_cost_arr[$quotation_id]['fabricpurchasekg'];
            				$qc_fabricpurchaseyds= $order_qty_pcs/$dzn_qnty_p*$qc_cost_arr[$quotation_id]['fabricpurchaseyds'];
            				$qc_fabricpurchase_cost=$qc_fabricpurchasekg+$qc_fabricpurchaseyds;
            				$qc_knitting_cost= $order_qty_pcs/$dzn_qnty_p*$qc_cost_arr[$quotation_id]['knitting_cost'];
            				$qc_fd_cost= $order_qty_pcs/$dzn_qnty_p*$qc_cost_arr[$quotation_id]['df_cost'];
            				$qc_aop_cost= $order_qty_pcs/$dzn_qnty_p*$qc_cost_arr[$quotation_id]['aop_cost'];
            				$qc_trim_cost= $order_qty_pcs/$dzn_qnty_p*$qc_cost_arr[$quotation_id]['trim_cost'];
            				$qc_emb_cost= $order_qty_pcs/$dzn_qnty_p*$qc_cost_arr[$quotation_id]['special_operation'];
            				$qc_commercial_cost= $order_qty_pcs/$dzn_qnty_p*$qc_cost_arr[$quotation_id]['commercial_cost'];
            				$qc_commision_cost= $order_qty_pcs/$dzn_qnty_p*$qc_cost_arr[$quotation_id]['com_cost'];
            				$qc_lab_cost= $order_qty_pcs/$dzn_qnty_p*$qc_cost_arr[$quotation_id]['test_cost'];
            				$qc_frieght_cost= $order_qty_pcs/$dzn_qnty_p*$qc_cost_arr[$quotation_id]['frieght_cost'];
            				$qc_cm_cost= $order_qty_pcs/$dzn_qnty_p*$qc_cost_arr[$quotation_id]['cm_cost'];
            				$qc_total_cost= $qc_yarn_cost+$qc_fabricpurchasekg+$qc_fabricpurchaseyds+$qc_knitting_cost+$qc_fd_cost+$qc_aop_cost+$qc_trim_cost+$qc_emb_cost+$qc_commercial_cost+$qc_commision_cost+$qc_lab_cost+$qc_frieght_cost+$qc_cm_cost;
            				$qc_profit_loss = $order_value-$qc_total_cost;
            				$qc_profit_loss_per= $qc_profit_loss*100/$order_value;

            				$qc_mis_cost= $order_qty_pcs/$dzn_qnty_p*$qc_cost_arr[$quotation_id]['mis_offer_qty'];
            				$qc_other_cost= $order_qty_pcs/$dzn_qnty_p*$qc_cost_arr[$quotation_id]['other_cost'];

            				$total_qc_yarn_cost+=$qc_yarn_cost;
            				$total_qc_fabricpurchase_cost+=$qc_fabricpurchase_cost;
            				$total_qc_knitting_cost+=$qc_knitting_cost;
            				$total_qc_fd_cost+=$qc_fd_cost;
            				$total_qc_aop_cost+=$qc_aop_cost;
            				$total_qc_trim_cost+=$qc_trim_cost;
            				$total_qc_emb_cost+=$qc_emb_cost;
            				$total_qc_commercial_cost+=$qc_commercial_cost;
            				$total_qc_commision_cost+=$qc_commision_cost;
            				$total_qc_lab_cost+=$qc_lab_cost;
            				$total_qc_frieght_cost+=$qc_frieght_cost;
            				$total_qc_cm_cost+=$qc_cm_cost;
            				$total_qc_total_cost+=$qc_total_cost;
            				$total_qc_profit_loss+=$qc_profit_loss;
            				$total_qc_profit_loss_per+=$qc_profit_loss_per;
            				$total_qc_mis_cost+=$qc_mis_cost;
            				$total_qc_other_cost+=$qc_other_cost;

            			 ?>
        				  <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
        				  	<td width="40" rowspan="4"><? echo $i; ?></td>
        				  	<td width="80" rowspan="4"><? echo $buyer_library[$row[csf('buyer_name')]] ?></td>
        				  	<td width="80" rowspan="4"><? echo $row[csf('job_no_prefix_num')];  ?></td>
        				  	<td width="100" rowspan="4"><? echo $row[csf('style_ref_no')]; ?></td>
        				  	<td width="100" rowspan="4"><? echo $row[csf('po_number')]; ?></td>
        				  	<td width="80" rowspan="4"><? echo change_date_format($row[csf('costing_date')]); ?></td>
        				  	<td width="80" rowspan="4"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
        				  	<td width="80" rowspan="4"><? echo number_format($row[csf('po_quantity')],2); ?></td>
		                    <td width="80" rowspan="4"><? echo number_format($row[csf('unit_price')],4); ?></td>
		                    <td rowspan="4" width="80"><? echo number_format($order_value,2); ?></td>
		                    <td width="100">QC Margin</td>
		                    <td width="80"><? echo number_format($qc_yarn_cost,2); ?></td>
		                    <td width="80"><? echo number_format($qc_fabricpurchasekg+$qc_fabricpurchaseyds,2); ?></td>
		                    <td width="80"><? echo number_format($qc_knitting_cost,2); ?></td>
		                    <td width="80"><? echo number_format($qc_fd_cost,2); ?></td>
		                    <td width="80"><? echo number_format($qc_aop_cost,2); ?></td>
		                    <td width="80"><? echo number_format($qc_trim_cost,2); ?></td>
		                    <td width="80"><? echo number_format($qc_emb_cost,2); ?></td>
		                    <td width="80"><? echo number_format($qc_commercial_cost,2); ?></td>
		                    <td width="80"><? echo number_format($qc_commision_cost,2); ?></td>
		                    <td width="80"><? echo number_format($qc_lab_cost,2); ?></td>
		                    <td width="80"><? echo number_format($qc_frieght_cost,2); ?></td>
		                    <td width="80"></td>
		                    <td width="80"></td>
		                    <td width="80"><? echo number_format($qc_cost_arr[$quotation_id]['cm_cost'],2); ?></td>
		                    <td width="80"><? echo number_format($qc_cm_cost,2); ?></td>
		                    <td width="80"><? echo number_format($qc_total_cost,2); ?></td>
		                    <td width="80"><? echo number_format($qc_profit_loss,2); ?></td>
		                    <td width="80"><? echo number_format($qc_profit_loss_per,2); ?>&#37;</td>
		                    <td width="80"><? echo $qc_cost_arr[$quotation_id]['smv'] ?></td>
		                    <td width="80"><? echo $qc_cost_arr[$quotation_id]['avl_min'] ?></td>
        				  </tr>
        				  <?
        				  	$emblishment_arr=array(1,2,3,4,5);
        				  	$dzn_qnty=0;
		                    $costing_per_id=$budget_dtls_arr[$row[csf('job_id')]]['costing_per_id'];
		                    if($costing_per_id==1) $dzn_qnty=12;
		                    else if($costing_per_id==3) $dzn_qnty=12*2;
		                    else if($costing_per_id==4) $dzn_qnty=12*3;
		                    else if($costing_per_id==5) $dzn_qnty=12*4;
		                    else $dzn_qnty=1;
							$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
							$order_qty_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
							$commercial_cost_dzn=$budget_dtls_arr[$row[csf('job_id')]]['comm_cost'];
                    		$commercial_cost=($commercial_cost_dzn/$dzn_qnty)*$order_qty_pcs;
                    		$budget_smv= $budget_dtls_arr[$row[csf('job_id')]]['sew_smv'];
                    		$sew_effi_percent= $budget_dtls_arr[$row[csf('job_id')]]['sew_effi_percent'];

                    		$avl_min = ($order_qty_pcs*$budget_smv)/$sew_effi_percent*100;

        				  	$budget_yarn_cost=$yarn_costing_arr[$row[csf('po_id')]];
        				  	$fab_purchase_knit=array_sum($fabric_costing_arr['knit']['grey'][$row[csf('po_id')]]);
							if(is_infinite($fab_purchase_knit) || is_nan($fab_purchase_knit)){$fab_purchase_knit=0;}
							$fab_purchase_woven=array_sum($fabric_costing_arr['woven']['grey'][$row[csf('po_id')]]);
							if(is_infinite($fab_purchase_woven) || is_nan($fab_purchase_woven)){$fab_purchase_woven=0;}
							$budget_fab_purchase=$fab_purchase_knit+$fab_purchase_woven;
							$budget_knit_cost=0;
							foreach($knit_cost_arr as $process_id)
							{
								$budget_knit_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$process_id]);	
							}
							$fabric_dyeing_cost=0;
							foreach($fabric_dyeingCost_arr as $fab_process_id)
							{
								$fabric_dyeing_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$fab_process_id]);	
							}
							$all_over_cost=0;
							foreach($aop_cost_arr as $aop_process_id)
							{
								$all_over_cost+=array_sum($conversion_costing_arr_process[$row[csf('po_id')]][$aop_process_id]);	
							}
							$trim_amount= $trims_costing_arr[$row[csf('po_id')]];
							if(is_infinite($trim_amount) || is_nan($trim_amount)){$trim_amount=0;}

							foreach ($emblishment_arr as $emb_id) {
								$emblishment_amount+=$emblishment_costing_arr_name[$row[csf('po_id')]][$emb_id];
							}
							if(is_infinite($emblishment_amount) || is_nan($emblishment_amount)){$emblishment_amount=0;}

							$foreign=$commission_costing_arr[$row[csf('po_id')]][1];
							if(is_infinite($foreign) || is_nan($foreign)){$foreign=0;}
							$local=$commission_costing_arr[$row[csf('po_id')]][2];
							if(is_infinite($local) || is_nan($local)){$local=0;}
							$budget_commision_cost = $foreign+$local;

							$test_cost=$other_costing_arr[$row[csf('po_id')]]['lab_test'];
							if(is_infinite($test_cost) || is_nan($test_cost)){$test_cost=0;}

							$freight_cost=$other_costing_arr[$row[csf('po_id')]]['freight'];
							if(is_infinite($freight_cost) || is_nan($freight_cost)){$freight_cost=0;}
							$inspection=$other_costing_arr[$row[csf('po_id')]]['inspection'];
							if(is_infinite($inspection) || is_nan($inspection)){$inspection=0;}
							$currier_cost=$other_costing_arr[$row[csf('po_id')]]['currier_pre_cost'];
							if(is_infinite($currier_cost) || is_nan($currier_cost)){$currier_cost=0;}
							$cm_cost=$other_costing_arr[$row[csf('po_id')]]['cm_cost'];
							if(is_infinite($cm_cost) || is_nan($cm_cost)){$cm_cost=0;}
							$cm_cost_dzn=($cm_cost/$order_qty_pcs)*12;
							if(is_infinite($cm_cost_dzn) || is_nan($cm_cost_dzn)){$cm_cost_dzn=0;}
							$budget_total_cost=$budget_yarn_cost+$budget_fab_purchase+$budget_knit_cost+$fabric_dyeing_cost+$all_over_cost+$trim_amount+$emblishment_amount+$commercial_cost+$budget_commision_cost+$test_cost+$freight_cost+$inspection+$currier_cost+$cm_cost;            				
							$budget_profit_loss=$order_value-$budget_total_cost;
							$budget_profit_loss_per= $budget_profit_loss*100/$order_value;

							$total_budget_yarn_cost += $budget_yarn_cost;
							$total_budget_fab_purchase += $budget_fab_purchase;
							$total_budget_knit_cost += $budget_knit_cost;
							$total_fabric_dyeing_cost += $fabric_dyeing_cost;
							$total_all_over_cost += $all_over_cost;
							$total_trim_amount += $trim_amount;
							$total_emblishment_amount += $emblishment_amount;
							$total_commercial_cost += $commercial_cost;
							$total_budget_commision_cost += $budget_commision_cost;
							$total_test_cost += $test_cost;
							$total_freight_cost += $freight_cost;
							$total_inspection_cost += $inspection;
							$total_currier_cost += $currier_cost;
							$total_cm_cost_dzn += $cm_cost_dzn;
							$total_cm_cost += $cm_cost;
							$total_budget_total_cost += $budget_total_cost;
							$total_budget_profit_loss += $budget_profit_loss;
							$total_budget_profit_loss_per += $budget_profit_loss_per;
        				  ?>
        				  <tr>
        				  	<td width="100">Pre Cost</td>
		                    <td width="80"><? echo number_format($budget_yarn_cost,2); ?></td>
		                    <td width="80"><? echo number_format($budget_fab_purchase,2); ?></td>
		                    <td width="80"><? echo number_format($budget_knit_cost,2); ?></td>
		                    <td width="80"><? echo number_format($fabric_dyeing_cost,2); ?></td>
		                    <td width="80"><? echo number_format($all_over_cost,2); ?></td>
		                    <td width="80"><? echo number_format($trim_amount,2); ?></td>
		                    <td width="80"><? echo number_format($emblishment_amount,2); ?></td>
		                    <td width="80"><? echo number_format($commercial_cost,2); ?></td>
		                    <td width="80"><? echo number_format($budget_commision_cost,2); ?></td>
		                    <td width="80"><? echo number_format($test_cost,2); ?></td>
		                    <td width="80"><? echo number_format($freight_cost,2); ?></td>
		                    <td width="80"><? echo number_format($inspection,2); ?></td>
		                    <td width="80"><? echo number_format($currier_cost,2); ?></td>
		                    <td width="80"><? echo number_format($cm_cost_dzn,2); ?></td>
		                    <td width="80"><? echo number_format($cm_cost,2); ?></td>
		                    <td width="80"><? echo number_format($budget_total_cost,2); ?></td>
		                    <td width="80"><? echo number_format($budget_profit_loss,2); ?></td>
		                    <td width="80"><? echo number_format($budget_profit_loss_per,2); ?>&#37;</td>
		                    <td width="80"><? echo $budget_dtls_arr[$row[csf('job_id')]]['sew_smv']; ?></td>
		                    <td width="80"><? echo number_format($avl_min,2); ?></td>
        				  </tr>
        				  	<?
        				  		$yarn_cost_variance= $qc_yarn_cost-$budget_yarn_cost;
        				  		$fabricpurchase_cost_variance= $qc_fabricpurchase_cost-$budget_fab_purchase;
        				  		$knitting_cost_variance= $qc_knitting_cost-$budget_knit_cost;
        				  		$fd_cost_variance= $qc_fd_cost-$fabric_dyeing_cost;
        				  		$aop_cost_variance= $qc_aop_cost-$all_over_cost;
        				  		$trim_cost_variance= $qc_trim_cost-$trim_amount;
        				  		$emb_cost_variance= $qc_emb_cost-$emblishment_amount;
        				  		$commercial_cost_variance= $qc_commercial_cost-$commercial_cost;
        				  		$commision_cost_variance= $qc_commision_cost-$budget_commision_cost;
        				  		$lab_cost_variance= $qc_lab_cost-$test_cost;
        				  		$freight_cost_variance= $qc_frieght_cost-$freight_cost;
        				  		$inspection_cost_variance= 0-$inspection;
        				  		$currier_cost_variance= 0-$currier_cost;
        				  		$cm_cost_dzn_variance= $qc_cost_arr[$quotation_id]['cm_cost']-$cm_cost_dzn;
        				  		$cm_cost_variance= $qc_cm_cost-$cm_cost;
        				  		$total_cost_variance= $qc_total_cost-$budget_total_cost;
        				  		$profit_variance= $budget_profit_loss-$qc_profit_loss;
        				  		$profit_per_variance= $qc_profit_loss_per-$budget_profit_loss_per;
        				  		$mis_cost_variance= 0-$qc_mis_cost;
        				  		$other_cost_variance= 0-$qc_other_cost;

        				  		$total_yarn_cost_variance += $yarn_cost_variance;
        				  		$total_fabricpurchase_cost_variance += $fabricpurchase_cost_variance;
        				  		$total_knitting_cost_variance += $knitting_cost_variance;
        				  		$total_fd_cost_variance += $fd_cost_variance;
        				  		$total_aop_cost_variance += $aop_cost_variance;
        				  		$total_trim_cost_variance += $trim_cost_variance;
        				  		$total_emb_cost_variance += $emb_cost_variance;
        				  		$total_commercial_cost_variance += $commercial_cost_variance;
        				  		$total_commision_cost_variance += $commision_cost_variance;
        				  		$total_lab_cost_variance += $lab_cost_variance;
        				  		$total_freight_cost_variance += $freight_cost_variance;
        				  		$total_inspection_cost_variance += $inspection_cost_variance;
        				  		$total_currier_cost_variance += $currier_cost_variance;
        				  		$total_cm_cost_dzn_variance += $cm_cost_dzn_variance;
        				  		$total_cm_cost_variance += $cm_cost_variance;
        				  		$total_total_cost_variance += $total_cost_variance;
        				  		$total_profit_variance += $profit_variance;
        				  		$total_profit_per_variance += $profit_per_variance;
        				  		$total_mis_cost_variance += $mis_cost_variance;
        				  		$total_other_cost_variance += $other_cost_variance;
        				  	?>
        				  <tr>
        				  	<td width="100">Variance</td>
		                    <td width="80"><? echo number_format($yarn_cost_variance,2); ?></td>
		                    <td width="80"><? echo number_format($fabricpurchase_cost_variance,2); ?></td>
		                    <td width="80"><? echo number_format($knitting_cost_variance,2); ?></td>
		                    <td width="80"><? echo number_format($fd_cost_variance,2); ?></td>
		                    <td width="80"><? echo number_format($aop_cost_variance,2); ?></td>
		                    <td width="80"><? echo number_format($trim_cost_variance,2); ?></td>
		                    <td width="80"><? echo number_format($emb_cost_variance,2); ?></td>
		                    <td width="80"><? echo number_format($commercial_cost_variance,2); ?></td>
		                    <td width="80"><? echo number_format($commision_cost_variance,2); ?></td>
		                    <td width="80"><? echo number_format($lab_cost_variance,2); ?></td>
		                    <td width="80"><? echo number_format($freight_cost_variance,2); ?></td>
		                    <td width="80"><? echo number_format($inspection_cost_variance,2); ?></td>
		                    <td width="80"><? echo number_format($currier_cost_variance,2); ?></td>
		                    <td width="80"><? echo number_format($cm_cost_dzn_variance,2); ?></td>
		                    <td width="80"><? echo number_format($cm_cost_variance,2); ?></td>
		                    <td width="80"><? echo number_format($total_cost_variance,2); ?></td>
		                    <td width="80"><? echo number_format($profit_variance,2); ?></td>
		                    <td width="80"><? echo number_format($total_profit_per_variance,2); ?>&#37;</td>
		                    <td width="80"></td>
		                    <td width="80"></td>
        				  </tr>
        				  <?
        				  	$yarn_cost_variance_per = $yarn_cost_variance*100/$qc_yarn_cost;
    				  		$fabricpurchase_cost_variance_per=$fabricpurchase_cost_variance*100/$qc_fabricpurchase_cost;
    				  		$knitting_cost_variance_per=$knitting_cost_variance*100/$qc_knitting_cost;
    				  		$fd_cost_variance_per= $fd_cost_variance*100/$qc_fd_cost;
    				  		$aop_cost_variance_per= $aop_cost_variance*100/$qc_aop_cost;
    				  		$trim_cost_variance_per= $trim_cost_variance*100/$qc_trim_cost;
    				  		$emb_cost_variance_per= $emb_cost_variance*100/$qc_emb_cost;
    				  		$commercial_cost_variance_per= $commercial_cost_variance*100/$qc_commercial_cost;
    				  		$commision_cost_variance_per= $commision_cost_variance*100/$qc_commision_cost;
    				  		$lab_cost_variance_per= $lab_cost_variance*100/$qc_lab_cost;
    				  		$freight_cost_variance_per= $freight_cost_variance*100/$qc_frieght_cost;
    				  		$cm_cost_variance_per= $cm_cost_variance_per*100/$qc_cm_cost;
    				  		$total_cost_variance_per= $total_cost_variance*100/$qc_total_cost;
    				  		$profit_variance_per= $profit_variance*100/$qc_profit_loss;

        				  ?>
        				  <tr>
        				  	<td width="100">Variance %</td>
		                    <td width="80"><? echo number_format($yarn_cost_variance_per,2); ?></td>
		                    <td width="80"><? echo number_format($fabricpurchase_cost_variance_per,2); ?></td>
		                    <td width="80"><? echo number_format($knitting_cost_variance_per,2); ?></td>
		                    <td width="80"><? echo number_format($fd_cost_variance_per,2); ?></td>
		                    <td width="80"><? echo number_format($aop_cost_variance_per,2); ?></td>
		                    <td width="80"><? echo number_format($trim_cost_variance_per,2); ?></td>
		                    <td width="80"><? echo number_format($emb_cost_variance_per,2); ?></td>
		                    <td width="80"><? echo number_format($commercial_cost_variance_per,2); ?></td>
		                    <td width="80"><? echo number_format($commision_cost_variance_per,2); ?></td>
		                    <td width="80"><? echo number_format($lab_cost_variance_per,2); ?></td>
		                    <td width="80"><? echo number_format($freight_cost_variance_per,2); ?></td>
		                    <td width="80"></td>
		                    <td width="80"></td>
		                    <td width="80"></td>
		                    <td width="80"><? echo number_format($cm_cost_variance_per,2); ?></td>
		                    <td width="80"><? echo number_format($total_cost_variance_per,2); ?></td>
		                    <td width="80"><? echo number_format($profit_variance_per,2); ?></td>
		                    <td width="80"><? echo number_format($qc_profit_loss_per,2); ?>&#37;</td>
		                    <td width="80"></td>
		                    <td width="80"></td>
        				  </tr>
            			<?
            				$i++;
            				$total_order_qty+=$row[csf('po_quantity')];
            				$total_order_value += $order_value;
            			}
            		?>
            		<tfoot>
            			<tr>
            				<td colspan="7" align="right">QC Margin</td>
            				<td><? echo number_format($total_order_qty,2); ?></td>
            				<td></td>
            				<td id="total_order_value"><? echo number_format($total_order_value,2); ?></td>
            				<td></td>
            				<td id="qc_yarn"><? echo number_format($total_qc_yarn_cost,2); ?></td>
            				<td id="qc_fabric"><? echo number_format($total_qc_fabricpurchase_cost,2); ?></td>
            				<td id="qc_knit"><? echo number_format($total_qc_knitting_cost,2); ?></td>
            				<td id="qc_fd"><? echo number_format($total_qc_fd_cost,2); ?></td>
            				<td id="qc_aop"><? echo number_format($total_qc_aop_cost,2); ?></td>
            				<td id="qc_trim"><? echo number_format($total_qc_trim_cost,2); ?></td>
            				<td id="qc_emb"><? echo number_format($total_qc_emb_cost,2); ?></td>
            				<td id="qc_commercial"><? echo number_format($total_qc_commercial_cost,2); ?></td>
            				<td id="qc_commision"><? echo number_format($total_qc_commision_cost,2); ?></td>
            				<td id="qc_test"><? echo number_format($total_qc_lab_cost,2); ?></td>
            				<td id="qc_freight"><? echo number_format($total_qc_frieght_cost,2); ?></td>
            				<td id="qc_inspection"></td>
            				<td></td>
            				<td></td>
            				<td id="qc_cm"><? echo number_format($total_qc_cm_cost,2); ?></td>
            				<td id="qc_total_cost"><? echo number_format($total_qc_total_cost,2); ?></td>
            				<td id="qc_profit_loss"><? echo number_format($total_qc_profit_loss,2); ?></td>
            				<td id="qc_pl_per"><? echo number_format($total_qc_profit_loss_per,2); ?></td>
            				<td></td>
            				<td></td>
            			</tr>
            			<tr>
            				<td colspan="7" align="right">Pre Cost</td>
            				<td><? echo number_format($total_order_qty,2); ?></td>
            				<td></td>
            				<td><? echo number_format($total_order_value,2); ?></td>
            				<td></td>
            				<td id="budget_yarn"><? echo number_format($total_budget_yarn_cost,2); ?></td>
            				<td id="budget_fabric"><? echo number_format($total_budget_fab_purchase,2); ?></td>
            				<td id="budget_knit"><? echo number_format($total_budget_knit_cost,2); ?></td>
            				<td id="budget_fd"><? echo number_format($total_fabric_dyeing_cost,2); ?></td>
            				<td id="budget_aop"><? echo number_format($total_all_over_cost,2); ?></td>
            				<td id="budget_trim"><? echo number_format($total_trim_amount,2); ?></td>
            				<td id="budget_emb"><? echo number_format($total_emblishment_amount,2); ?></td>
            				<td id="budget_commercial"><? echo number_format($total_commercial_cost,2); ?></td>
            				<td id="budget_commision"><? echo number_format($total_budget_commision_cost,2); ?></td>
            				<td id="budget_test"><? echo number_format($total_test_cost,2); ?></td>
            				<td id="budget_freight"><? echo number_format($total_freight_cost,2); ?></td>
            				<td id="budget_inspection"><? echo number_format($total_inspection_cost,2); ?></td>
            				<td id="budget_currier"><? echo number_format($total_currier_cost,2); ?></td>
            				<td></td>
            				<td id="budget_cm"><? echo number_format($total_cm_cost,2); ?></td>
            				<td id="budget_total_cost"><? echo number_format($total_budget_total_cost,2); ?></td>
            				<td id="budget_profit_loss"><? echo number_format($total_budget_profit_loss,2); ?></td>
            				<td id="budget_pl_per"><? echo number_format($total_budget_profit_loss_per,2); ?></td>
            				<td></td>
            				<td></td>
            			</tr>
            			<tr>
            				<td colspan="7" align="right">Variance</td>
            				<td></td>
            				<td></td>
            				<td></td>
            				<td></td>
            				<td id="yarn_variance"><? echo number_format($total_yarn_cost_variance,2); ?></td>
            				<td id="fabric_variance"><? echo number_format($total_fabricpurchase_cost_variance,2); ?></td>
            				<td id="variance_knit"><? echo number_format($total_knitting_cost_variance,2); ?></td>
            				<td id="variance_fd"><? echo number_format($total_fd_cost_variance,2); ?></td>
            				<td id="variance_aop"><? echo number_format($total_aop_cost_variance,2); ?></td>
            				<td id="variance_trim"><? echo number_format($total_trim_cost_variance,2); ?></td>
            				<td id="variance_emb"><? echo number_format($total_emb_cost_variance,2); ?></td>
            				<td id="variance_commercial"><? echo number_format($total_commercial_cost_variance,2); ?></td>
            				<td id="variance_commision"><? echo number_format($total_commision_cost_variance,2); ?></td>
            				<td id="variance_test"><? echo number_format($total_lab_cost_variance,2); ?></td>
            				<td id="variance_freight"><? echo number_format($total_freight_cost_variance,2); ?></td>
            				<td id="variance_inspection"><? echo number_format($total_inspection_cost_variance,2); ?></td>
            				<td id="variance_currier"><? echo number_format($total_currier_cost_variance,2); ?></td>
            				<td></td>
            				<td id="variance_cm"><? echo number_format($total_cm_cost_variance,2); ?></td>
            				<td id="variance_total_cost"><? echo number_format($total_total_cost_variance,2); ?></td>
            				<td id="variance_profit"><? echo number_format($total_profit_variance,2); ?></td>
            				<td><? echo number_format($total_profit_per_variance,2); ?></td>
            				<td></td>
            				<td></td>
            			</tr>
            		</tfoot>
            		</table>
            </fieldset>            
    </div>
	<?
	$html = ob_get_contents();
	ob_clean();
	foreach (glob("*.xls") as $filename) {
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc, $html);
	echo "$html****$filename****$report_type"; 
    exit();
}
?>