<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.trims.php');
require_once('../../../includes/class4/class.fabrics.php');
require_once('../../../includes/class4/class.yarns.php');
require_once('../../../includes/class4/class.conversions.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//Company Details
$sql = "SELECT id, company_name, company_short_name, group_id FROM lib_company";
$result = sql_select($sql);
$company_library = array();
$company_short_details=array();
$group_id_details = array();
foreach($result as $row)
{
	$company_library[$row[csf('id')]] = $row[csf('company_name')];
	$company_short_details[$row[csf('id')]] = $row[csf('company_short_name')];
	$group_id_details[$row[csf('id')]] = $row[csf('group_id')];
}

$bank_details=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');
$buyer_details=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name'); 
$supplier_details=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name'); 
$job_company_library=return_library_array( "select job_no, company_name from wo_po_details_master", "job_no", "company_name"  );
$group_details = return_library_array( "select id, group_name from lib_group", "id", "group_name"  );
$item_library=return_library_array( "select id, item_name from lib_item_group", "id", "item_name"  );
//Bank Percent Details
$sql = "SELECT account_id, company_id, loan_limit FROM lib_bank_account where account_type=20 and loan_type=0 and status_active=1 and is_deleted=0";
$result = sql_select($sql);

$percent_details = array();
foreach($result as $row)
{
	$percent_details[$row[csf('account_id')]][$row[csf('company_id')]] = $row[csf('loan_limit')];
}
unset($result);

$sql_cost_heads="SELECT company_name, cost_heads, cost_heads_status FROM variable_settings_commercial where variable_list=17 and status_active=1 and is_deleted=0";
$result_cost_heads = sql_select($sql_cost_heads);

$cost_heads_fabric_array=array(); 
$cost_heads_embellish_array=array();
$cost_details=array();
foreach( $result_cost_heads as $row )
{
	if($row[csf('cost_heads_status')]==1)
	{
		if($row[csf('cost_heads')]=="101" || $row[csf('cost_heads')]=="102" || $row[csf('cost_heads')]=="103")
		{
			if (array_key_exists($row[csf('company_name')], $cost_heads_embellish_array)) 
			{
				$cost_heads_embellish_array[$row[csf('company_name')]].=",".substr($row[csf('cost_heads')],-1);
			}
			else
			{
				$cost_heads_embellish_array[$row[csf('company_name')]]=substr($row[csf('cost_heads')],-1);	
			}
		}
		else
		{
			if (array_key_exists($row[csf('company_name')], $cost_heads_fabric_array)) 
			{
				$cost_heads_fabric_array[$row[csf('company_name')]].=",".$row[csf('cost_heads')];	
			}
			else
			{
				$cost_heads_fabric_array[$row[csf('company_name')]]=$row[csf('cost_heads')];		
			}
		}
	}
	
	$cost_heads=$row[csf('cost_heads')];
	$cost_details[$row[csf('company_name')]][$cost_heads] = $row[csf('cost_heads_status')];
}
unset($result_cost_heads);
//print_r($cost_details[3]);
if($action=="internal_file_no_info")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	$file_info=explode("**",$data);
	$file_no=$file_info[0];
	$company_name=$file_info[3];
	$bank_id=$file_info[4];
	$text_year=$file_info[5];
	$file_value=$file_info[6];
	
?>
<style>

.r90{
	 writing-mode: tb-rl;
     filter: flipv fliph;
    -webkit-transform: rotate(270deg);
    -moz-transform: rotate(270deg);
    -o-transform: rotate(270deg);
    -ms-transform: rotate(270deg);
    transform: rotate(180deg);
    width: 1em;
    line-height: 1em;
    }
	@media print {thead {display: table-header-group;}}
</style>


<script>

	function new_window(html_filter_print,type)
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'+
	'<html><head><title></title><style>.r90{-webkit-transform: rotate(270deg);-moz-transform: rotate(270deg);-o-transform: rotate(270deg);-ms-transform: rotate(270deg);transform: rotate(270deg);width: 1em;line-height: 1em;};@media print {thead {display: table-header-group;}}</style></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		d.close();
		
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="450px";
	}
	
	function openmypage_trims(job_no,po_id,order_no,order_qnty,action,title)
	{
		emailwindow=dhtmlmodal.open('EmailBox', 'iframe', 'internal_file_info_report.php?job_no='+job_no+'&po_id='+po_id+'&order_no='+order_no+'&order_qnty='+order_qnty+'&action='+action, title, 'width=650px,height=370px,center=1,resize=0,scrolling=0','../../');
	}
	
	function show_agent(val)
	{
		document.getElementById('agent_name').innerHTML=val;
	}
</script>	
	
<div align="center" style="height:30px; width:1000px; font-size:18px" class="form_caption"><input type="button" onClick="new_window()" value="Print Preview" name="Print" class="formbutton" style="width:100px"/></div>
<? if($report_type == 1) : ?>
<fieldset style="width:100%; border:hidden">
<div id="report_container">
	<div align="center" style="height:30px; width:1900px; font-size:18px; border:hidden" class="form_caption">Cost Sheet Analysis [Attach Qty. Wise] Knit</div>
	<div style="width:1900px">
        <table width="1890" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" style="font-size:12px">
            <thead style="width:100%;" class="table_header" >
                <tr bgcolor="#CCCCCC" height="30">
                    <td colspan="54" style="font-size:16px; border:none" width="100%"><b>File No: <? echo $file_no; ?>, &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Buyer name: <? echo $buyer_details[$file_info[1]]; ?>, &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Applicant name: <? echo $buyer_details[$file_info[2]]; ?></b><span id="agent_name"></span>
                    </td>
                </tr>
                <tr height="100">
                    <th width="20" valign="middle">SL</th>
                    <th width="100" valign="middle">LC/SC No.</th>
                    <th width="70" valign="middle">Style Ref.</th>
                    <th width="50" valign="middle">Work. Factory</th>
                    <th width="60" valign="middle">Order No</th>
                    <th width="70" valign="middle">Fabric Desc.</th>
                    <th width="30" valign="bottom"><div class="r90">Po&nbsp;Qnty</div></th>
                    <th width="30" valign="bottom"><div class="r90">Attached&nbsp;Qnty</div></th>
                    <th width="30" valign="bottom"><div class="r90">Balance</div></th>
                    <th width="30" valign="bottom"><div class="r90">Price(C/S)</div></th>
                    <th width="30" valign="bottom"><div class="r90">Price(P/O)</div></th>
                    <th width="30" valign="bottom"><div class="r90">Price(L/C)</div></th>
                    <th width="30" valign="bottom"><div class="r90">Value(C/S)</div></th>
                    <th width="30" valign="bottom"><div class="r90">Value(L/C)</div></th>
                    <th width="30" valign="bottom"><div class="r90">CommerCost</div></th>
                    <th width="30" valign="bottom"><div class="r90">FreightCost</div></th>
                    <th width="30" valign="bottom"><div class="r90">Commi(C/S)</div></th>
                    <th width="30" valign="bottom"><div class="r90">Commi(L/C)</div></th>
                    <th width="30" valign="bottom"><div class="r90">NetValue(C/S)</div></th>
                    <th width="30" valign="bottom"><div class="r90">NetValue(L/C)</div></th>  
                    <th width="30" valign="bottom"><div class="r90">ShipmentDate</div></th>
                    <th colspan="33">
                        <table width="100%" height="100" border="0" cellpadding="0" cellspacing="0" rules="all">
                            <tr height="50%">
                                <th colspan="4" width="123" align="center" style="height: 50px;">Yarn</th>
                                <th colspan="2" width="61" align="center" style="height: 50px;">Access.</th>
                                <th colspan="2" width="61" align="center" style="height: 50px;">Knitting</th>
                                <th colspan="2" width="61" align="center" style="height: 50px;">Dyeing</th>
                                <th colspan="2" width="61" align="center" style="height: 50px;">Finishing</th>
                                <th colspan="2" width="61" align="center" style="height: 50px;">Printing & Embro.</th>
                                <th colspan="2" width="61" align="center" style="height: 50px;">AOP</th>
                                <th colspan="2" width="61" align="center" style="height: 50px;">Washing</th>
                                <th colspan="2" width="61" align="center" style="height: 50px;">Test</th>
                                <th colspan="2" width="61" align="center" style="height: 50px;">Insp.</th>
                                <th colspan="4" width="122" align="center" style="height: 50px;">Knit Fab. Purc.</th>
                                <th colspan="4" width="122" align="center" style="height: 50px;">Woven Fab. Purc.</th>
                                <th colspan="3" width="92" align="center" style="height: 50px;">CM</th>
                            </tr>
                            <tr>
                                <th width="30" valign="bottom"><div class="r90">Qnty</div></th>
                                <th width="30" valign="bottom"><div class="r90">Rate</div></th>
                                <th width="30" valign="bottom"><div class="r90">Cost</div></th>
                                <th width="30" valign="bottom"><div class="r90">%</div></th>
                                <th width="30" valign="bottom"><div class="r90">Cost</div></th>
                                <th width="30" valign="bottom"><div class="r90">%</div></th>
                                <th width="30" valign="bottom"><div class="r90">Cost</div></th>
                                <th width="30" valign="bottom"><div class="r90">%</div></th>
                                <th width="30" valign="bottom"><div class="r90">Cost</div></th>
                                <th width="30" valign="bottom"><div class="r90">%</div></th>
                                <th width="30" valign="bottom"><div class="r90">Cost</div></th>
                                <th width="30" valign="bottom"><div class="r90">%</div></th>
                                <th width="30" valign="bottom"><div class="r90">Cost</div></th>
                                <th width="30" valign="bottom"><div class="r90">%</div></th>
                                <th width="30" valign="bottom"><div class="r90">Cost</div></th>
                                <th width="30" valign="bottom"><div class="r90">%</div></th>
                                <th width="30" valign="bottom"><div class="r90">Cost</div></th>
                                <th width="30" valign="bottom"><div class="r90">%</div></th>
                                <th width="30" valign="bottom"><div class="r90">Cost</div></th>
                                <th width="30" valign="bottom"><div class="r90">%</div></th>
                                <th width="30" valign="bottom"><div class="r90">Cost</div></th>
                                <th width="30" valign="bottom"><div class="r90">%</div></th>
                                
                                <th width="30" valign="bottom"><div class="r90">Qnty</div></th>
                                <th width="30" valign="bottom"><div class="r90">Rate</div></th>
                                <th width="30" valign="bottom"><div class="r90">Cost</div></th>
                                <th width="30" valign="bottom"><div class="r90">%</div></th>
                                
                                <th width="30" valign="bottom"><div class="r90">Qnty</div></th>
                                <th width="30" valign="bottom"><div class="r90">Rate</div></th>
                                <th width="30" valign="bottom"><div class="r90">Cost</div></th>
                                <th width="30" valign="bottom"><div class="r90">%</div></th>
                                
                                <th width="30" valign="bottom"><div class="r90">Value</div></th>
                                <th width="30" valign="bottom"><div class="r90">%</div></th>
                                <th width="30" valign="bottom"><div class="r90">Per&nbsp;Dzn</div></th>
                            </tr>
                        </table>
                    </th>
                </tr>
            </thead>
            <tbody class="table_body" id="scroll_body" width="1878">
        	<? 
				$i=1; $c=1; $agent_name=''; $knit_charge=0; $aop_charge=0; $fab_charge=0; $print_embro_charge=0; $wash_charge=0; $tot_gmts_qnty=0; $cost_heads_knit_purchase=0; $cost_heads_woven_purc=0;
				
				$sql_order="select b.wo_po_break_down_id as po_id, 1 as type 
				from com_export_lc a, com_export_lc_order_info b 
				where a.id=b.com_export_lc_id and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.lc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
				union 
				select b.wo_po_break_down_id as po_id, 2 as type 
				from com_sales_contract a, com_sales_contract_order_info b 
				where a.id=b.com_sales_contract_id and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.sc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
				";
				$result_order=sql_select( $sql_order );
				$all_order_id="";
				foreach($result_order as $row)
				{
					if($ord_check[$row[csf("po_id")]]=="")
					{
						$ord_check[$row[csf("po_id")]]=$row[csf("po_id")];
						$all_order_id.=$row[csf("po_id")].",";
					}
				}
				$all_order_id=chop($all_order_id,",");
				
				if($all_order_id!="")
				{
					$job_sql=sql_select("select job_no_mst, job_id from wo_po_break_down where status_active=1 and is_deleted=0 and id in($all_order_id)");
					$all_job=$all_job_id="";
					foreach($job_sql as $row)
					{
						if($job_check[$row[csf("job_no_mst")]]=="")
						{
							$job_check[$row[csf("job_no_mst")]]=$row[csf("job_no_mst")];
							$all_job.="'".$row[csf("job_no_mst")]."',";
                            $all_job_id.=$row[csf("job_id")].',';
						}
					}
					$all_job=chop($all_job,",");
                    $all_job_ids=chop($all_job_id,",");
					$fabriccostArray=array();
					$costing_sql=sql_select("select job_no, costing_per_id, freight, comm_cost, commission, lab_test, inspection, common_oh from wo_pre_cost_dtls where status_active=1 and is_deleted=0 and job_id in($all_job_ids)");
					foreach($costing_sql as $row)
					{
						$fabriccostArray[$row[csf('job_no')]]['cpi']=$row[csf('costing_per_id')]; 
						$fabriccostArray[$row[csf('job_no')]]['freight']=$row[csf('freight')];
						$fabriccostArray[$row[csf('job_no')]]['comm_cost']=$row[csf('comm_cost')];
						$fabriccostArray[$row[csf('job_no')]]['commission']=$row[csf('commission')];
						$fabriccostArray[$row[csf('job_no')]]['lab_test']=$row[csf('lab_test')];
						$fabriccostArray[$row[csf('job_no')]]['inspection']=$row[csf('inspection')];
						$fabriccostArray[$row[csf('job_no')]]['common_oh']=$row[csf('common_oh')];
					}
					unset($costing_sql);
				}
				
				$trimscostArray=array();
				$condition= new condition();
				
				if($all_order_id !=""){
					//$condition->po_id(" in($all_order_id)");
					$condition->po_id_in("$all_order_id"); 
                    $condition->jobid_in("$all_job_ids");
                    $condition->company_name($company_name);
				
					$condition->init();
					$trims= new trims($condition);
					//echo $trims->getQuery(); die;
					$trimscostArray=$trims->getAmountArray_by_order();
					
					$yarns= new yarn($condition);
					//echo $yarns->getQuery(); die;
					$yarnQntyArray=$yarns->getOrderWiseYarnQtyArray();
					$yarncostArray=$yarns->getOrderWiseYarnAmountArray();
					//echo "<pre>";print_r($yarncostArray);die;
					
					$fabric= new fabric($condition);
					//echo $fabric->getQuery(); die;
					$fabricCostArrayClass=$fabric->getAmountArray_by_orderAndFabricSource_knitAndwoven_greyAndfinish();
					$fabricQntyArrayClass=$fabric->getQtyArray_by_orderAndFabricSource_knitAndwoven_greyAndfinish();
					
					$conversion= new conversion($condition);
					//echo $conversion->getQuery(); die;
					$conversion_cost_arr=$conversion->getAmountArray_by_orderAndProcess();
				}
				//echo "<pre>";print_r($conversion_cost_arr);die;
				//echo "<pre>";print_r($fabricQntyArrayClass['knit']['finish'][23590][2]);die;
				
				$emblcostArray=array();
				$emblArray=sql_select("select job_no, emb_name, sum(amount) as amount from wo_pre_cost_embe_cost_dtls where status_active=1 and is_deleted=0 group by job_no, emb_name");
				foreach($emblArray as $row)
				{
					$emblcostArray[$row[csf('job_no')]][$row[csf('emb_name')]]=$row[csf('amount')]; 
				}
				unset($emblArray);
				
				$woven_knit_purchase_cost_arr=array(); $fabric_desc_arr=array();
				
				$fabricArray=sql_select("select job_no, fab_nature_id, fabric_source, fabric_description, amount from wo_pre_cost_fabric_cost_dtls where status_active=1 and is_deleted=0 and fabric_source!=3");
				foreach($fabricArray as $row)
				{
					$fabric_desc_arr[$row[csf('job_no')]].=$row[csf('fabric_description')].","; 
				}
				unset($fabricArray);
				
				$fabricPurcArray=sql_select("select job_no, woven_amount, knit_amount from wo_pre_cost_sum_dtls where status_active=1 and is_deleted=0");
				foreach($fabricPurcArray as $row)
				{
					$woven_knit_purchase_cost_arr[$row[csf('job_no')]]['woven']+=$row[csf('woven_amount')];
					$woven_knit_purchase_cost_arr[$row[csf('job_no')]]['knit']+=$row[csf('knit_amount')];  
				}
				unset($fabricPurcArray);
				
		 		$sql="select a.id, a.export_lc_no as sc_lc_no, a.lc_date as lc_sc_date, a.last_shipment_date, a.lc_value as lc_sc_value, a.foreign_comn, a.local_comn, 1 as type from com_export_lc a where a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.lc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 
				union 
					select b.id, b.contract_no as sc_lc_no, b.contract_date as lc_sc_date, b.last_shipment_date, b.contract_value as lc_sc_value, b.foreign_comn, b.local_comn, 2 as type from com_sales_contract b where b.internal_file_no='$file_no' and b.beneficiary_name='$company_name' and b.lien_bank like '$bank_id' and b.sc_year like '$text_year' and b.status_active=1 and b.is_deleted=0
				";
				$nameArray=sql_select( $sql );
				$gr_tot_yarn_cost=$gr_tot_trims_cost=$gr_tot_knitt_cost=$gr_tot_dye_cost=$gr_tot_print_embro_cost=$gr_tot_wash_cost=$gr_tot_test_cost=$gr_tot_commercial_cost=$gr_tot_commission_cost=$gr_tot_freight_cost=$gr_tot_inspection_cost=$gr_tot_finish_cost=$gr_tot_knit_purc_cost=0;
				foreach ($nameArray as $selectResult)
				{
					$j=1; $tot_order_qnty=0; $tot_order_val=0; $tot_lc_value=0; $tot_commercial_cost=0; $tot_freight_cost=0; $tot_commission_cost=0; $tot_lc_comn_cost=0; $tot_net_cs_val=0; $tot_net_lc_val=0; $tot_yarn_qnty=0; $tot_yarn_cost=0; $tot_trims_cost=0; $tot_knitt_cost=0; $tot_dye_cost=0; $tot_print_embro_cost=0; $tot_wash_cost=0; $tot_test_cost=0; $tot_inspection_cost=0; $tot_finish_cost=0; $tot_cm_cost=0; $tot_knit_purc_cost=0; $tot_woven_purc_cost=0;$tot_aop_cost=0;$tot_knit_purc_qnty=0;$tot_woven_purc_qnty=0;$tot_po_qnty_pcs=0;$tot_po_balance=0;
					
					if($selectResult[csf('type')]==1)
					{
						$query="select d.company_name, d.style_ref_no, d.agent_name, d.order_uom, c.id as po_id, c.po_number, c.pub_shipment_date as shipment_date, c.job_no_mst, c.po_quantity,(d.total_set_qnty*c.po_quantity) as po_qnty_in_pcs, ((d.total_set_qnty*c.po_quantity)*d.set_smv) as smvmin, c.unit_price, b.attached_qnty, b.attached_rate, b.attached_value, d.total_set_qnty from com_export_lc_order_info b, wo_po_break_down c, wo_po_details_master d where b.com_export_lc_id='".$selectResult[csf('id')]."' and b.wo_po_break_down_id=c.id and c.job_no_mst=d.job_no and b.status_active=1 and b.is_deleted=0 order by d.company_name";
					}
					else
					{
						$query="select d.company_name, d.style_ref_no, d.agent_name, d.order_uom, c.id as po_id, c.po_number, c.pub_shipment_date as shipment_date, c.job_no_mst, c.po_quantity,(d.total_set_qnty*c.po_quantity) as po_qnty_in_pcs, ((d.total_set_qnty*c.po_quantity)*d.set_smv) as smvmin, c.unit_price, b.attached_qnty, b.attached_rate, b.attached_value, d.total_set_qnty from com_sales_contract_order_info b, wo_po_break_down c, wo_po_details_master d where b.com_sales_contract_id='".$selectResult[csf('id')]."' and b.wo_po_break_down_id=c.id and c.job_no_mst=d.job_no and b.status_active=1 and b.is_deleted=0 order by d.company_name";
					}
					
					$result=sql_select( $query );
					$tot_rows=count($result);
					foreach ($result as $row)
					{
						if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						$tot_gmts_qnty+=$row[csf('po_qnty_in_pcs')];
						
						$tot_minutes+=$row[csf('smvmin')];	
						if($agent_name=='')	$agent_name=$buyer_details[$row[csf('agent_name')]]; else $agent_name=$agent_name;
						
						$commercial_cost=0; $freight_cost=0; $commission_cost=0; $lc_comn_cost=0; $net_cs_val=0; $net_lc_val=0; $yarn_qnty=0; $yarn_cost=0; $yarn_cost_perc=0; $trims_cost=0; $trims_cost_perc=0; $test_cost=0; $test_cost_perc=0; $inspection_cost=0; $inspection_cost_perc=0; $finish_cost=0; $finish_cost_perc=0; $knitt_cost=0; $knitt_cost_percent=0; $others_cost=0; $others_cost_percent=0; $dye_cost=0; $dye_cost_percent=0; $wash_cost=0; $wash_cost_percent=0; $print_embro_cost=0; $print_embro_cost_percent=0; $cm_cost=0; $cm_cost_perc=0;$aop_cost=0;$aop_cost_percent=0;$attach_woven_purc_qnty=$woven_purc_rate=$attach_yarn_cost=$attach_knit_purc_qnty=$knit_purc_rate=0;
						
						$unit_price=$row[csf('unit_price')];
						$order_quantity=$row[csf('attached_qnty')];
						$order_val=$row[csf('attached_qnty')]*$unit_price;
						$att_vale=$row[csf('attached_value')];
						
						$dzn_qnty=0;
						$costing_per_id=$fabriccostArray[$row[csf('job_no_mst')]]['cpi'];
						if($costing_per_id==1) $dzn_qnty=12;
						else if($costing_per_id==3) $dzn_qnty=12*2;
						else if($costing_per_id==4) $dzn_qnty=12*3;
						else if($costing_per_id==5) $dzn_qnty=12*4;
						else $dzn_qnty=1;
						
						//$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
						
						$freight_cost=$fabriccostArray[$row[csf('job_no_mst')]]['freight'];
						$commercial_cost=$fabriccostArray[$row[csf('job_no_mst')]]['comm_cost'];
						$commission_cost=$fabriccostArray[$row[csf('job_no_mst')]]['commission'];
						
						$test_cost=$fabriccostArray[$row[csf('job_no_mst')]]['lab_test'];
						$test_cost_perc=($test_cost/$order_val)*100;
						
						$inspection_cost=$fabriccostArray[$row[csf('job_no_mst')]]['inspection'];
						$inspection_cost_perc=($inspection_cost/$order_val)*100;
						 
						$common_oh_cost=$fabriccostArray[$row[csf('job_no_mst')]]['common_oh'];// yet not used
						$common_oh_cost_perc=($common_oh_cost/$order_val)*100;// yet not used
						
						
						//$trims_cost_total=$trimscostArray[0][csf('trims_cost_total')];
						$trims_cost_total=$trimscostArray[$row[csf('po_id')]];
						//$trims_cost=($order_quantity/$dzn_qnty)*$trims_cost_total;
						$trims_cost=$trimscostArray[$row[csf('po_id')]];
						$trims_cost_perc=($trims_cost/$order_val)*100;
						
						$foreign_comn=$selectResult[csf('foreign_comn')];
						$local_comn=$selectResult[csf('local_comn')];
						
						
						$yarn_qnty=$yarn_avg_qnty=$yarn_cost=$yarn_cost_perc=0;
						if($yarnQntyArray[$row[csf('po_id')]]) $yarn_avg_qnty=$yarnQntyArray[$row[csf('po_id')]];
						if($yarncostArray[$row[csf('po_id')]]) $yarn_cost=$yarncostArray[$row[csf('po_id')]];
						$yarn_cost_perc=($yarn_cost/$order_val)*100;
						
						//echo $yarn_cost."_".$row[csf('po_id')]."<br>";
						
						$fabric_desc=implode(",",array_unique(explode(",",chop($fabric_desc_arr[$row[csf('job_no_mst')]],','))));
						
						$lc_comn_cost=($att_vale*($foreign_comn+$local_comn))/100;
						$net_cs_val=$order_val-$commercial_cost-$freight_cost-$commission_cost;
						$net_lc_val=$att_vale-$commercial_cost-$freight_cost-$lc_comn_cost;
						foreach($conversion_cost_arr[$row[csf('po_id')]] as $cons_process=>$uomdata)
						{
							foreach($uomdata as $uom=>$amnt)
							{
								if($cons_process==1 || $cons_process==3 || $cons_process==4 || $cons_process==134)
								{
									$knitt_cost+=($amnt/$row[csf('po_quantity')])*$order_quantity;
									if($cost_details[$row[csf('company_name')]][1]==1)
									{
										//$knit_charge+=($order_quantity/$dzn_qnty)*$amnt;
										$knit_charge+=($amnt/$row[csf('po_quantity')])*$order_quantity;
									}
								}
								else if($cons_process==30 || $cons_process==31)
								{
									$dye_cost+=($amnt/$row[csf('po_quantity')])*$order_quantity;
									
									if($cost_details[$row[csf('company_name')]][$cons_process]==1)
									{
										$fab_charge+=($amnt/$row[csf('po_quantity')])*$order_quantity;
									}
								}
								else
								{
									$finish_cost+=($amnt/$row[csf('po_quantity')])*$order_quantity;
									if($cons_process==35 || $cons_process==36 || $cons_process==37 || $cons_process==40)
									{
										if($cost_details[$row[csf('company_name')]][35]==1)
										{
											//$aop_charge+=($order_quantity/$dzn_qnty)*$amnt;
											$aop_charge+=($amnt/$row[csf('po_quantity')])*$order_quantity;
											$aop_cost+=($amnt/$row[csf('po_quantity')])*$order_quantity;
										}
									}
									else
									{
										if($cost_details[$row[csf('company_name')]][31]==1)
										{
											$fab_charge+=($amnt/$row[csf('po_quantity')])*$order_quantity;
										}
									}
								}
							}
						}
						
						$knitt_cost_percent=($knitt_cost/$order_val)*100;
						$dye_cost_percent=($dye_cost/$order_val)*100;
						$finish_cost_percent=($finish_cost/$order_val)*100;
						
						foreach($emblcostArray[$row[csf('job_no_mst')]] as $emb_name=>$amnt)
						{
							$cost_heads_new=$emb_name+100;
							if($emb_name==3)
							{
								$wash_cost=($amnt/$dzn_qnty)*$order_quantity;
								if($cost_details[$row[csf('company_name')]][$cost_heads_new]==1)
								{
									$wash_charge+=((($amnt/$dzn_qnty)*$order_quantity)/$row[csf('po_quantity')])*$order_quantity;
								}
							}
							else
							{
								$print_embro_cost+=($amnt/$dzn_qnty)*$order_quantity;
								if($cost_details[$row[csf('company_name')]][$cost_heads_new]==1) 
								{
									$print_embro_charge+=((($amnt/$dzn_qnty)*$order_quantity)/$row[csf('po_quantity')])*$order_quantity;
								}
							}
						}
						
						if($cost_details[$row[csf('company_name')]][75]==1)
						{
							//echo $woven_knit_purchase_cost_arr[$row[csf('job_no_mst')]]['knit']."<br>All ";
							$knit_purchase_cost=$woven_knit_purchase_cost_arr[$row[csf('job_no_mst')]]['knit'];
							$cost_heads_knit_purchase+=($knit_purchase_cost/$row[csf('po_quantity')])*$order_quantity;
							//($order_quantity/$dzn_qnty)*$knit_purchase_cost;
						}
						
						if($cost_details[$row[csf('company_name')]][78]==1)
						{
							$woven_purchase_cost=$woven_knit_purchase_cost_arr[$row[csf('job_no_mst')]]['woven'];
							$cost_heads_woven_purc+=($order_quantity/$dzn_qnty)*$woven_purchase_cost;
						}
						$knit_purc_cost=0;
						foreach($fabricCostArrayClass['knit']['finish'][$row[csf('po_id')]][2] as $fab_amt)
						{
							$knit_purc_cost+=$fab_amt;
						}
						$knit_purc_qnty=0;
						foreach($fabricQntyArrayClass['knit']['finish'][$row[csf('po_id')]][2] as $fab_qnty)
						{
							$knit_purc_qnty+=$fab_qnty;
						}
						$woven_purc_qnty=0;
						foreach($fabricQntyArrayClass['woven']['finish'][$row[csf('po_id')]][2] as $fab_qnty)
						{
							$woven_purc_qnty+=$fab_qnty;
						}
						//$knit_purc_cost=($order_quantity/$dzn_qnty)*$woven_knit_purchase_cost_arr[$row[csf('job_no_mst')]]['knit'];
						$woven_purc_cost=($woven_knit_purchase_cost_arr[$row[csf('job_no_mst')]]['woven']/$row[csf('po_quantity')])*$order_quantity;
						
						$wash_cost_percent=($wash_cost/$order_val)*100;
						$print_embro_cost_percent=($print_embro_cost/$order_val)*100;
						$aop_cost_percent=($aop_cost/$order_val)*100;
						$knit_purc_cost_percent=($knit_purc_cost/$order_val)*100;
						$woven_purc_cost_percent=($woven_purc_cost/$order_val)*100;
						
						$cm_cost=$order_val-$commercial_cost-$freight_cost-$commission_cost-$yarn_cost-$trims_cost-$knitt_cost-$dye_cost-$print_embro_cost-$wash_cost-$test_cost-$inspection_cost-$finish_cost-$knit_purc_cost-$woven_purc_cost;
						$cm_cost_perc=($cm_cost/$order_val)*100;
						$cm_per_dzn=($cm_cost/$order_quantity)*12;
						
						$job_no=$row[csf('job_no_mst')];
						$po_id=$row[csf('po_id')];
						$po_no=$row[csf('po_number')];
						
						
						// for yarn yarn_avg_qnty attach_yarn_avg_qnty
						$yarn_rate=$yarn_cost/$yarn_avg_qnty;
						$attached_qnty_pcs=($row[csf('attached_qnty')]*$row[csf('total_set_qnty')]);
						//$att_vale=$attached_qnty_pcs*$row[csf('attached_rate')];
						$ord_val=$row[csf('po_qnty_in_pcs')]*$unit_price;
						$attach_yarn_avg_qnty=(($yarn_avg_qnty*$attached_qnty_pcs)/$row[csf('po_qnty_in_pcs')]);
						if($attach_yarn_avg_qnty>0 && $yarn_rate>0) $attach_yarn_cost=$attach_yarn_avg_qnty*$yarn_rate;
						$attach_yarn_cost_perc=($attach_yarn_cost/$att_vale)*100; 
						
						// for knitting purchase 
						if($knit_purc_cost>0 && $knit_purc_qnty>0) $knit_purc_rate=$knit_purc_cost/$knit_purc_qnty; 
						if($knit_purc_qnty>0) $attach_knit_purc_qnty=($knit_purc_qnty/$row[csf('po_quantity')])*$order_quantity;
						$attach_knit_purc_cost=$attach_knit_purc_qnty*$knit_purc_rate;
						$attach_knit_purc_cost_percent=($attach_knit_purc_cost/$att_vale)*100;
						
						 
						// for woven purchase  
						if($woven_purc_cost>0 && $woven_purc_qnty>0) $woven_purc_rate+=$woven_purc_cost/$woven_purc_qnty; 
						if($woven_purc_qnty>0) $attach_woven_purc_qnty=(($woven_purc_qnty*$attached_qnty_pcs)/$row[csf('po_qnty_in_pcs')]);
						$attach_woven_purc_cost=$attach_woven_purc_qnty*$woven_purc_rate;
						$attach_woven_purc_cost_percent=($attach_woven_purc_cost/$att_vale)*100;
						
						// for trims trims_cost_perc knitt_cost
						$attach_trims_cost=($trims_cost/$row[csf('po_quantity')])*$order_quantity;
						$attach_trims_cost_perc=(($attach_trims_cost/$att_vale)*100);
						
						// for Knitting  knitt_cost knitt_cost_percent
						$attach_knitt_cost=$knitt_cost;//($knitt_cost/$row[csf('po_quantity')])*$order_quantity;
						$attach_knitt_cost_percent=(($attach_knitt_cost/$att_vale)*100);
						
						// for dyeing  dye_cost dye_cost_percent 
						$attach_dye_cost=$dye_cost;
						$attach_dye_cost_percent=(($attach_dye_cost/$att_vale)*100);
						
						// for Finish   finish_cost finish_cost_percent
						$attach_finish_cost=$finish_cost;
						$attach_finish_cost_percent=(($attach_finish_cost/$att_vale)*100);
						
						// for print embro print_embro_cost print_embro_cost_percent 
						$attach_print_embro_cost=($print_embro_cost/$row[csf('po_quantity')])*$order_quantity;
						$attach_print_embro_cost_percent=(($attach_print_embro_cost/$att_vale)*100);
						
						// for aop aop_cost aop_cost_percent 
						$attach_aop_cost=$aop_cost;
						$attach_aop_cost_percent=(($attach_aop_cost/$att_vale)*100);
						
						// for wash wash_cost wash_cost_percent 
						$attach_wash_cost=$wash_cost;
						$attach_wash_cost_percent=(($attach_wash_cost/$att_vale)*100);
						
						// for test test_cost test_cost_perc 
						$attach_test_cost=($test_cost/$row[csf('po_quantity')])*$order_quantity;
						$attach_test_cost_perc=(($attach_test_cost/$att_vale)*100);
						
						// for inspection inspection_cost inspection_cost_perc 
						$attach_inspection_cost=($inspection_cost/$row[csf('po_quantity')])*$order_quantity;
						$attach_inspection_cost_perc=(($attach_inspection_cost/$att_vale)*100);
						//if(is_numeric($attach_yarn_cost)) $attach_yarn_cost=$attach_yarn_cost; else $attach_yarn_cost=0;
						//if($attach_yarn_cost==NAN || $attach_yarn_cost=="nan" || $attach_yarn_cost=="") $attach_yarn_cost=0;
						$attach_cost=$commercial_cost+$freight_cost+$commission_cost+$attach_yarn_cost+$attach_trims_cost+$attach_knitt_cost+$attach_dye_cost+$attach_print_embro_cost+$attach_wash_cost+$attach_test_cost+$attach_inspection_cost+$attach_finish_cost+$attach_knit_purc_cost+$attach_woven_purc_cost+$attach_aop_cost;
						
						$tot_attach_cost+=$attach_cost;
						
						$attach_cm_cost=$att_vale-$commercial_cost-$freight_cost-$commission_cost-$attach_yarn_cost-$attach_trims_cost-$attach_knitt_cost-$attach_dye_cost-$attach_print_embro_cost-$attach_wash_cost-$attach_test_cost-$attach_inspection_cost-$attach_finish_cost-$attach_knit_purc_cost-$attach_woven_purc_cost-$attach_aop_cost;
						/*if($attach_cm_cost<0) 
						{
							echo $row[csf('po_number')]."=".$att_vale."=".$commercial_cost."=".$freight_cost."=".$commission_cost."=".$attach_yarn_cost."=".$attach_trims_cost."=".$attach_knitt_cost."=".$attach_dye_cost."=".$attach_print_embro_cost."=".$attach_wash_cost."=".$attach_test_cost."=".$attach_inspection_cost."=".$attach_finish_cost."=".$attach_knit_purc_cost."=".$attach_woven_purc_cost."=".$attach_aop_cost."=".$woven_purc_qnty."=".$woven_purc_cost."=".$attach_woven_purc_qnty."=".$woven_purc_rate;
							die;
						}*/
						$attach_cm_cost_perc=($attach_cm_cost/$att_vale)*100;
						$attach_cm_per_dzn=($attach_cm_cost/$attached_qnty_pcs)*12;
						
						if($j==1)
						{
						?>
                            <tr height="90" bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_l<? echo $c; ?>','<? echo $bgcolor; ?>')" id="tr_l<? echo $c; ?>">
                                <td rowspan="<? echo $tot_rows; ?>" width="20"><? echo $i; ?></td>	
                                <td rowspan="<? echo $tot_rows; ?>" width="100" align="center">
                                    <p>
										<?
											if($selectResult[csf('type')]==1)
											{
												echo "<b>LC : ".$selectResult[csf('sc_lc_no')]."</b><br>";
												echo "Dt: ".change_date_format($selectResult[csf('lc_sc_date')])."<br>"; 
												echo "Val: ".fn_number_format($selectResult[csf('lc_sc_value')],2,'.','')."<br>"; $total_lc_sc_val=$selectResult[csf('lc_sc_value')];
												echo "L. Dt. Ship: ".change_date_format($selectResult[csf('last_shipment_date')])."<br>";
												
												$sql_amnd="select amendment_no, amendment_date, amendment_value, value_change_by, last_shipment_date from com_export_lc_amendment where export_lc_id='".$selectResult[csf('id')]."' and is_original<>0 and status_active=1 and is_deleted=0";
												$res_amnd=sql_select($sql_amnd );
												foreach($res_amnd as $row_amnd)
												{
													echo "Amnd No: ".$row_amnd[csf('amendment_no')]."<br>";
													echo "Dt: ".change_date_format($row_amnd[csf('amendment_date')])."<br>";
													
													if($row_amnd[csf('last_shipment_date')] >0)
													{
														echo $increase_decrease[$row_amnd[csf('value_change_by')]]." Value#"."<br>";
														echo fn_number_format($row_amnd[csf('amendment_value')],2,'.','')."<br>";
													}
													else echo "Value# "."<br>";
													
													if($row_amnd[csf('last_shipment_date')]!="0000-00-00") $amend_shipment_date=change_date_format($row_amnd[csf('last_shipment_date')]);
													else $amend_shipment_date="";
													
													echo "L. Dt. Ship: ". $amend_shipment_date."<br>";
												}
											}
											else
											{
												echo "<b>SC : ".$selectResult[csf('sc_lc_no')]."</b><br>";
												echo "Dt: ".change_date_format($selectResult[csf('lc_sc_date')])."<br>"; 
												echo "Val: ".fn_number_format($selectResult[csf('lc_sc_value')],2,'.','')."<br>"; $total_lc_sc_val=$selectResult[csf('lc_sc_value')];
												echo "L. Dt. Ship: ".change_date_format($selectResult[csf('last_shipment_date')])."<br>";
												
												$sql_amnd="select amendment_no, amendment_date, amendment_value, value_change_by, last_shipment_date from com_sales_contract_amendment where contract_id='".$selectResult[csf('id')]."' and is_original<>0 and status_active=1 and is_deleted=0";
												$res_amnd=sql_select($sql_amnd );
												foreach($res_amnd as $row_amnd)
												{
													echo "Amnd No: ".$row_amnd[csf('amendment_no')]."<br>";
													echo "Dt: ".change_date_format($row_amnd[csf('amendment_date')])."<br>";
													
													if($row_amnd[csf('last_shipment_date')] >0)
													{
														echo $increase_decrease[$row_amnd[csf('value_change_by')]]." Value#"."<br>";
														echo fn_number_format($row_amnd[csf('amendment_value')],2,'.','')."<br>";
													}
													else echo "Value# "."<br>";
													
													if($row_amnd[csf('last_shipment_date')]!="0000-00-00") $amend_shipment_date=change_date_format($row_amnd[csf('last_shipment_date')]);
													else $amend_shipment_date="";
													
													echo "L. Dt. Ship: ". $amend_shipment_date."<br>";
												}
											}
                                        ?>
                                    </p>
                                </td>
                                <td width="70"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                                <td width="50"><? echo $company_short_details[$row[csf('company_name')]]; ?></td>
                                <td width="60"><p><? echo $row[csf('po_number')]; ?></p></td>
                                <td width="70"><p><? $fabric_desc=explode(",",$fabric_desc); echo implode(",<br>",$fabric_desc); ?></p></td>
                                <td width="30" valign="bottom" align="center">
                                    <div class="r90">
                                        <? 
                                            echo fn_number_format($row[csf('po_qnty_in_pcs')],0,'.',''); 
                                            $tot_po_qnty_pcs+=$row[csf('po_qnty_in_pcs')]; 
                                        ?>
                                    </div>
                                </td>
                                <td width="30" valign="bottom" align="center">
                                    <div class="r90">
                                        <?
                                            echo fn_number_format($attached_qnty_pcs,0,'.',''); 
                                            $tot_order_qnty+=$attached_qnty_pcs; 
                                        ?>
                                    </div>
                                </td>
                                <td width="30" valign="bottom" align="center">
                                    <div class="r90">
                                        <? 
											$po_balance=$row[csf('po_qnty_in_pcs')]-($row[csf('attached_qnty')]*$row[csf('total_set_qnty')]);
                                            echo fn_number_format($po_balance,0,'.',''); 
                                            $tot_po_balance+=$po_balance; 
                                        ?>
                                    </div>
                                </td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($unit_price,2,'.',''); ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($unit_price,2,'.',''); ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($row[csf('attached_rate')],2,'.','');?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($order_val,2,'.',''); $tot_order_val+=$order_val; ?></div></td>
                                <td width="30" valign="bottom" align="center">
                                    <div class="r90">
                                        <? 
                                            //$att_vale=$row[csf('attached_value')];
                                            echo fn_number_format($att_vale,2,'.','');
                                            $tot_lc_value+=$att_vale; 
                                        ?>
                                    </div>
                                </td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($commercial_cost,2,'.',''); $tot_commercial_cost+=$commercial_cost;?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($freight_cost,2,'.',''); $tot_freight_cost+=$freight_cost; ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($commission_cost,2,'.',''); $tot_commission_cost+=$commission_cost;?></div></td>
                                <td width="30" valign="bottom" align="center">
                                    <div class="r90">
                                        <? 
                                            echo fn_number_format($lc_comn_cost,2,'.',''); 
                                            $tot_lc_comn_cost+=$lc_comn_cost; 
                                        ?>
                                    </div>
                                </td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($net_cs_val,2,'.',''); $tot_net_cs_val+=$net_cs_val; ?></div>
                                </td>
                                <td width="30" valign="bottom" align="center">
                                    <div class="r90">
                                        <?
                                            echo fn_number_format($net_lc_val,2,'.','');  
                                            $tot_net_lc_val+=$net_lc_val; 
                                        ?>
                                    </div>
                                </td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo change_date_format($row[csf('shipment_date')]); ?></div></td>
                                <td width="30" valign="bottom" align="center">
                                    <div class="r90">
                                        <? 
                                            //echo fn_number_format($yarn_qnty,2,'.',''); $tot_yarn_qnty+=$yarn_qnty;
											//echo fn_number_format($yarn_avg_qnty,2,'.',''); 
											echo fn_number_format($attach_yarn_avg_qnty,2,'.','');
											$tot_yarn_qnty+=$attach_yarn_avg_qnty;
                                            if($attach_yarn_avg_qnty>0) $tot_order_qnty_yarn+=$order_quantity; 
                                        ?>
                                    </div>
                                </td>
                                <td width="30" valign="bottom" align="center">
                                    <div class="r90">
                                        <?
                                            /* $yarn_rate=$yarn_cost/$yarn_qnty; 
                                            echo fn_number_format($yarn_rate,2,'.','');*/
											 
                                            if($attached_qnty_pcs>0) echo fn_number_format($yarn_rate,2,'.','');
                                        ?>
                                    </div>
                                </td>
                                <td width="30" valign="bottom" align="center" title="<? echo $attach_yarn_cost; ?>"><div class="r90"><? echo fn_number_format($attach_yarn_cost,2,'.',''); $tot_yarn_cost+=$attach_yarn_cost; ?></div></td>
                                <td width="30" valign="bottom" align="center" title="<? echo "tims cost :".$trims_cost."Attach ord val :".$att_vale."ord val :".$ord_val; ?>"><div class="r90"><? echo fn_number_format($attach_yarn_cost_perc,2,'.',''); ?></div></td>
                                <td width="30" valign="bottom" align="center" title="<? echo $trims_cost_total; ?>"><div class="r90"><? echo "<a href='#report_details' style='text-decoration:none' onclick= \"openmypage_trims('$job_no','$po_id','$po_no','$order_quantity','trims_info','Trims Info');\">".fn_number_format($attach_trims_cost,2,'.','')."</a>"; $tot_trims_cost+=$attach_trims_cost; ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_trims_cost_perc,2,'.',''); ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_knitt_cost,2,'.',''); $tot_knitt_cost+=$attach_knitt_cost; ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_knitt_cost_percent,2,'.',''); ?></div></td> 
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_dye_cost,2,'.',''); $tot_dye_cost+=$attach_dye_cost; ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_dye_cost_percent,2,'.',''); ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_finish_cost,2,'.',''); $tot_finish_cost+=$attach_finish_cost;?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_finish_cost_percent,2,'.',''); ?></div></td> 
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_print_embro_cost,2,'.',''); $tot_print_embro_cost+=$attach_print_embro_cost; ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_print_embro_cost_percent,2,'.',''); ?></div></td> 
                                
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_aop_cost,2,'.',''); $tot_aop_cost+=$attach_aop_cost; ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_aop_cost_percent,2,'.',''); ?></div></td> 
                                
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_wash_cost,2,'.',''); $tot_wash_cost+=$attach_wash_cost; ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_wash_cost_percent,2,'.',''); ?></div></td> 
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_test_cost,2,'.',''); $tot_test_cost+=$attach_test_cost; ?></div></td>
                                <td  width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_test_cost_perc,2,'.',''); ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_inspection_cost,2,'.',''); $tot_inspection_cost+=$attach_inspection_cost; ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_inspection_cost_perc,2,'.',''); ?></div></td>  
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_knit_purc_qnty,2,'.','');$tot_knit_purc_qnty+=$attach_knit_purc_qnty; ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($knit_purc_rate,2,'.','');?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_knit_purc_cost,2,'.','');$tot_knit_purc_cost+=$attach_knit_purc_cost; ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_knit_purc_cost_percent,2,'.',''); ?></div></td>  
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_woven_purc_qnty,2,'.',''); $tot_woven_purc_qnty+=$attach_woven_purc_qnty; ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($woven_purc_rate,2,'.',''); ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_woven_purc_cost,2,'.',''); $tot_woven_purc_cost+=$attach_woven_purc_cost; ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_woven_purc_cost_percent,2,'.',''); ?></div></td>
                                <td width="30" valign="bottom" align="center" title="<? echo $attach_knit_purc_cost; ?>"><div class="r90"><? echo fn_number_format($attach_cm_cost,2,'.',''); $tot_cm_cost+=$attach_cm_cost; ?></div></td>
                                <td width="30" valign="bottom" align="center" title="<? echo $knit_purc_qnty; ?>"><div class="r90"><? echo fn_number_format($attach_cm_cost_perc,2,'.',''); ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_cm_per_dzn,2,'.',''); ?></div></td>
                            </tr>
                        <?
						}
						else
						{
						?>
                            <tr height="90" bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_l<? echo $c; ?>','<? echo $bgcolor; ?>')" id="tr_l<? echo $c;?>">
                                <td width="70"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                                <td width="50"><? echo $company_short_details[$row[csf('company_name')]]; ?></td>
                                <td width="60"><p><? echo $row[csf('po_number')]; ?></p></td>
                                <td width="70"><p><? $fabric_desc=explode(",",$fabric_desc); echo implode(",<br>",$fabric_desc); ?></p></td>
                                <td width="30" valign="bottom" align="center">
                                    <div class="r90">
                                        <? 
                                            echo fn_number_format($row[csf('po_qnty_in_pcs')],0,'.',''); 
                                            $tot_po_qnty_pcs+=$row[csf('po_qnty_in_pcs')]; 
                                        ?>
                                    </div>
                                </td>
                                <td width="30" valign="bottom" align="center">
                                    <div class="r90">
                                        <? 
                                            echo fn_number_format(($row[csf('attached_qnty')]*$row[csf('total_set_qnty')]),0,'.',''); 
                                            $tot_order_qnty+=($row[csf('attached_qnty')]*$row[csf('total_set_qnty')]); 
                                        ?>
                                    </div>
                                </td>
                                <td width="30" valign="bottom" align="center">
                                    <div class="r90">
                                        <? 
											$po_balance=$row[csf('po_qnty_in_pcs')]-($row[csf('attached_qnty')]*$row[csf('total_set_qnty')]);
                                            echo fn_number_format($po_balance,0,'.',''); 
                                            $tot_po_balance+=$po_balance; 
                                        ?>
                                    </div>
                                </td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($unit_price,2,'.',''); ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($unit_price,2,'.',''); ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($row[csf('attached_rate')],2,'.','');?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($order_val,2,'.',''); $tot_order_val+=$order_val; ?></div></td>
                                <td width="30" valign="bottom" align="center">
                                    <div class="r90">
                                        <? 
                                            //$att_vale=$row[csf('attached_value')];
                                            echo fn_number_format($att_vale,2,'.','');
                                            $tot_lc_value+=$att_vale; 
                                        ?>
                                    </div>
                                </td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($commercial_cost,2,'.',''); $tot_commercial_cost+=$commercial_cost;?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($freight_cost,2,'.',''); $tot_freight_cost+=$freight_cost; ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($commission_cost,2,'.',''); $tot_commission_cost+=$commission_cost;?></div></td>
                                <td width="30" valign="bottom" align="center">
                                    <div class="r90">
                                        <? 
                                            echo fn_number_format($lc_comn_cost,2,'.',''); 
                                            $tot_lc_comn_cost+=$lc_comn_cost; 
                                        ?>
                                    </div>
                                </td>
                                <td width="30" valign="bottom" align="center">
                                	<div class="r90"><? echo fn_number_format($net_cs_val,2,'.',''); $tot_net_cs_val+=$net_cs_val; ?></div>
                                </td>
                                <td width="30" valign="bottom" align="center">
                                    <div class="r90">
                                        <?
                                            echo fn_number_format($net_lc_val,2,'.','');  
                                            $tot_net_lc_val+=$net_lc_val; 
                                        ?>
                                    </div>
                                </td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo change_date_format($row[csf('shipment_date')]); ?></div></td>
                                <td width="30" valign="bottom" align="center">
                                    <div class="r90">
                                        <? 
											echo fn_number_format($attach_yarn_avg_qnty,2,'.',''); $tot_yarn_qnty+=$attach_yarn_avg_qnty;
                                            if($attach_yarn_avg_qnty>0) $tot_order_qnty_yarn+=$order_quantity; 
                                        ?>
                                    </div>
                                </td>
                                <td width="30" valign="bottom" align="center">
                                    <div class="r90">
                                        <?
                                             /*$yarn_rate=$yarn_cost/$yarn_qnty; 
                                            echo fn_number_format($yarn_rate,2,'.','');*/
											$yarn_rate=$yarn_cost/$yarn_avg_qnty; 
                                            echo fn_number_format($yarn_rate,2,'.','');
                                        ?>
                                    </div>
                                </td>
                                <td width="30" valign="bottom" align="center" title="<? echo $attach_yarn_cost; ?>"><div class="r90"><? echo fn_number_format($attach_yarn_cost,2,'.',''); $tot_yarn_cost+=$attach_yarn_cost; ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90" title="<? echo "tims cost :".$trims_cost."Attach ord val :".$att_vale."ord val :".$ord_val; ?>"><? echo fn_number_format($attach_yarn_cost_perc,2,'.',''); ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo "<a href='#report_details' style='text-decoration:none' onclick= \"openmypage_trims('$job_no','$po_id','$po_no','$order_quantity','trims_info','Trims Info');\">".fn_number_format($attach_trims_cost,2,'.','')."</a>"; $tot_trims_cost+=$attach_trims_cost; ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_trims_cost_perc,2,'.',''); ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_knitt_cost,2,'.',''); $tot_knitt_cost+=$attach_knitt_cost; ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_knitt_cost_percent,2,'.',''); ?></div></td> 
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_dye_cost,2,'.',''); $tot_dye_cost+=$attach_dye_cost; ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_dye_cost_percent,2,'.',''); ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_finish_cost,2,'.',''); $tot_finish_cost+=$attach_finish_cost;?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_finish_cost_percent,2,'.',''); ?></div></td> 
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_print_embro_cost,2,'.',''); $tot_print_embro_cost+=$attach_print_embro_cost; ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_print_embro_cost_percent,2,'.',''); ?></div></td> 
                                
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_aop_cost,2,'.',''); $tot_aop_cost+=$attach_aop_cost; ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_aop_cost_percent,2,'.',''); ?></div></td> 
                                
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_wash_cost,2,'.',''); $tot_wash_cost+=$attach_wash_cost; ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_wash_cost_percent,2,'.',''); ?></div></td> 
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_test_cost,2,'.',''); $tot_test_cost+=$attach_test_cost; ?></div></td>
                                <td  width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_test_cost_perc,2,'.',''); ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_inspection_cost,2,'.',''); $tot_inspection_cost+=$attach_inspection_cost; ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_inspection_cost_perc,2,'.',''); ?></div></td>  
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_knit_purc_qnty,2,'.','');$tot_knit_purc_qnty+=$attach_knit_purc_qnty; ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($knit_purc_rate,2,'.','');?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_knit_purc_cost,2,'.','');$tot_knit_purc_cost+=$attach_knit_purc_cost; ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_knit_purc_cost_percent,2,'.',''); ?></div></td>  
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_woven_purc_qnty,2,'.',''); $tot_woven_purc_qnty+=$attach_woven_purc_qnty; ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($woven_purc_rate,2,'.',''); ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_woven_purc_cost,2,'.',''); $tot_woven_purc_cost+=$attach_woven_purc_cost; ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_woven_purc_cost_percent,2,'.',''); ?></div></td>
                                <td width="30" valign="bottom" align="center" title="<? echo $attach_knit_purc_cost; ?>"><div class="r90"><? echo fn_number_format($attach_cm_cost,2,'.',''); $tot_cm_cost+=$attach_cm_cost; ?></div></td>
                                <td width="30" valign="bottom" align="center" title="<? echo $knit_purc_qnty; ?>"><div class="r90"><? echo fn_number_format($attach_cm_cost_perc,2,'.',''); ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_cm_per_dzn,2,'.',''); ?></div></td>
                            </tr>
                        	<?
						}
						$c++;
						$j++;
					}
					
					if($tot_rows>0)
					{
						?>
                        <tr bgcolor="#CCCCCC" height="90">
                            <td align="right"><b>T</b></td>
                            <td align="right"><? echo fn_number_format($total_lc_sc_val,2,'.',''); $gr_total_lc_sc_val+=$total_lc_sc_val; ?></td>
                            <td align="right">&nbsp;</td>
                            <td align="right">&nbsp;</td>
                            <td align="right">&nbsp;</td>
                            <td align="right">&nbsp;</td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_po_qnty_pcs,0,'.',''); $gr_tot_po_qnty_pcs+=$tot_po_qnty_pcs; ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_order_qnty,0,'.',''); $gr_tot_order_qnty+=$tot_order_qnty; ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_po_balance,0,'.',''); $gr_tot_po_balance+=$tot_po_balance; ?></div></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td> 
                            <td>&nbsp;</td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_order_val,2,'.',''); $gr_tot_order_val+=$tot_order_val; ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_lc_value,2,'.',''); $gr_tot_lc_value+=$tot_lc_value; ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_commercial_cost,2,'.',''); if($tot_commercial_cost>0)$gr_tot_commercial_cost+=$tot_commercial_cost; ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_freight_cost,2,'.',''); if($tot_freight_cost>0) $gr_tot_freight_cost+=$tot_freight_cost; ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_commission_cost,2,'.',''); if($tot_commission_cost>0) $gr_tot_commission_cost+=$tot_commission_cost; ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_lc_comn_cost,2,'.',''); $gr_tot_lc_comn_cost+=$tot_lc_comn_cost; ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_net_cs_val,2,'.',''); $gr_tot_net_cs_val+=$tot_net_cs_val; ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_net_lc_val,2,'.',''); $gr_tot_net_lc_val+=$tot_net_lc_val; ?></div></td>
                            <td>&nbsp;</td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_yarn_qnty,2,'.',''); $gr_tot_yarn_qnty+=$tot_yarn_qnty; ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? $tot_yarn_rate=$tot_yarn_cost/$tot_yarn_qnty; echo fn_number_format($tot_yarn_rate,2,'.',''); ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_yarn_cost,2,'.',''); 
							if($tot_yarn_cost>0) $gr_tot_yarn_cost += $tot_yarn_cost; ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format(($tot_yarn_cost/$tot_order_val)*100,2,'.',''); ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_trims_cost,2,'.',''); if($tot_trims_cost>0)$gr_tot_trims_cost+=$tot_trims_cost; ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format(($tot_trims_cost/$tot_order_val)*100,2,'.',''); ?></div></td>                           
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_knitt_cost,2,'.',''); if($tot_knitt_cost>0) $gr_tot_knitt_cost+=$tot_knitt_cost; ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format(($tot_knitt_cost/$tot_order_val)*100,2,'.',''); ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_dye_cost,2,'.',''); if($tot_dye_cost>0) $gr_tot_dye_cost+=$tot_dye_cost; ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format(($tot_dye_cost/$tot_order_val)*100,2,'.',''); ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_finish_cost,2,'.',''); if($tot_finish_cost>0) $gr_tot_finish_cost+=$tot_finish_cost;?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format(($tot_finish_cost/$tot_order_val)*100,2,'.',''); ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_print_embro_cost,2,'.',''); if($tot_print_embro_cost>0) $gr_tot_print_embro_cost+=$tot_print_embro_cost;?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format(($tot_print_embro_cost/$tot_order_val)*100,2,'.',''); ?></div></td>
                            
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_aop_cost,2,'.',''); $gr_tot_aop_cost+=$tot_aop_cost;?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format(($tot_aop_cost/$tot_order_val)*100,2,'.',''); ?></div></td>                            
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_wash_cost,2,'.',''); if($tot_wash_cost>0) $gr_tot_wash_cost+=$tot_wash_cost; ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format(($tot_wash_cost/$tot_order_val)*100,2,'.',''); ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_test_cost,2,'.',''); if($tot_test_cost>0) $gr_tot_test_cost+=$tot_test_cost; ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format(($tot_test_cost/$tot_order_val)*100,2,'.',''); ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_inspection_cost,2,'.',''); if($tot_inspection_cost>0) $gr_tot_inspection_cost+=$tot_inspection_cost; ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format(($tot_inspection_cost/$tot_order_val)*100,2,'.',''); ?></div></td>
                            
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_knit_purc_qnty,2,'.',''); $gr_tot_knit_purc_qnty+=$tot_knit_purc_qnty; ?></div></td>
                            <td valign="bottom" align="center"><div class="r90">&nbsp;</div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_knit_purc_cost,2,'.',''); if($tot_knit_purc_cost>0) $gr_tot_knit_purc_cost+=$tot_knit_purc_cost; ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format(($tot_knit_purc_cost/$tot_order_val)*100,2,'.',''); ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_woven_purc_qnty,2,'.',''); $gr_tot_woven_purc_qnty+=$tot_woven_purc_qnty; ?></div></td>
                            <td valign="bottom" align="center"><div class="r90">&nbsp;</div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_woven_purc_cost,2,'.',''); $gr_tot_woven_purc_cost+=$tot_woven_purc_cost; ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format(($tot_woven_purc_cost/$tot_order_val)*100,2,'.',''); ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_cm_cost,2,'.',''); $gr_tot_cm_cost+=$tot_cm_cost; ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format(($tot_cm_cost/$tot_order_val)*100,2,'.','');?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format(($tot_cm_cost/$tot_order_qnty)*12,2,'.','');?></div></td>
                        </tr>
                        <?
					}
				$i++;
				}
				?>
                <tr bgcolor="#E9F3FF" height="90">
                    <td align="right"><b>GT</b></td>
                    <td align="right"><b><? echo fn_number_format($gr_total_lc_sc_val,2,'.',''); ?></b></td>
                    <td align="right">&nbsp;</td>
                    <td align="right">&nbsp;</td>
                    <td align="right">&nbsp;</td>
                    <td align="right">&nbsp;</td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_po_qnty_pcs,0,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_order_qnty,0,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_po_balance,0,'.',''); ?></div></b></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_order_val,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_lc_value,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_commercial_cost,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_freight_cost,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_commission_cost,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_lc_comn_cost,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_net_cs_val,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_net_lc_val,2,'.',''); ?></div></b></td>
                    <td>&nbsp;</td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_yarn_qnty,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_yarn_cost/$gr_tot_yarn_qnty,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center" title="<? echo $gr_tot_yarn_cost;?>"><b><div class="r90"><? echo fn_number_format($gr_tot_yarn_cost,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format(($gr_tot_yarn_cost/$gr_tot_order_val)*100,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_trims_cost,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format(($gr_tot_trims_cost/$gr_tot_order_val)*100,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_knitt_cost,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format(($gr_tot_knitt_cost/$gr_tot_order_val)*100,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_dye_cost,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format(($gr_tot_dye_cost/$gr_tot_order_val)*100,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_finish_cost,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format(($gr_tot_finish_cost/$gr_tot_order_val)*100,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_print_embro_cost,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format(($gr_tot_print_embro_cost/$gr_tot_order_val)*100,2,'.',''); ?></div></b></td>
                    
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_aop_cost,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format(($gr_tot_aop_cost/$gr_tot_order_val)*100,2,'.',''); ?></div></b></td>
                    
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_wash_cost,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format(($gr_tot_wash_cost/$gr_tot_order_val)*100,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_test_cost,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format(($gr_tot_test_cost/$gr_tot_order_val)*100,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_inspection_cost,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format(($gr_tot_inspection_cost/$gr_tot_order_val)*100,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_knit_purc_qnty,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90">&nbsp;</div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_knit_purc_cost,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format(($gr_tot_knit_purc_cost/$gr_tot_order_val)*100,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_woven_purc_qnty,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90">&nbsp;</div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_woven_purc_cost,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format(($gr_tot_woven_purc_cost/$gr_tot_order_val)*100,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_cm_cost,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format(($gr_tot_cm_cost/$gr_tot_order_val)*100,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format(($gr_tot_cm_cost/$gr_tot_order_qnty)*12,2,'.',''); ?></div></b></td>
               </tr>
               </tbody>
            </table>
        </div>
        <br />
        <table width="1480">
        	<tr>
            	<td colspan="3" style="font-size:16px; font-weight:bold;">File Value : <? echo fn_number_format($file_value,2);?></td>
            </tr>
            <tr>
            	<td colspan="3">&nbsp;</td>
            </tr>
        	<tr>
            	<td width="610" valign="top">
                    <u><b>CM [Cost of Manufature] calculated between L/C value and all other costs:</b></u>
                    <table width="100%" class="rpt_table" cellpadding="0" cellspacing="0" rules="all" border="1"> 
                        <thead>
                            <th width="170">Cost Item</th>
                            <th width="120">Cost Amount</th>
                            <th width="120">Consumption/Dzn Gmts</th>
                            <th width="120">Cost</th>
                            <th width="80">UOM</th>
                        </thead>
                        <?
                            $tot_cost_heads=$gr_tot_yarn_cost+$gr_tot_trims_cost+$gr_tot_knitt_cost+$gr_tot_dye_cost+$gr_tot_print_embro_cost+$gr_tot_wash_cost+$gr_tot_test_cost+$gr_tot_commercial_cost+$gr_tot_commission_cost+$gr_tot_freight_cost+$gr_tot_inspection_cost+$gr_tot_finish_cost+$gr_tot_knit_purc_cost+$gr_tot_woven_purc_cost;
                        ?>
                        <tr bgcolor="#E9F3FF">
                            <td>Yarn</td>
                            <td align="right"><? echo fn_number_format($gr_tot_yarn_cost,2); ?></td>
                            <td align="right"><? echo fn_number_format(($gr_tot_yarn_qnty/$tot_order_qnty_yarn)*12,4); ?></td>
                            <td align="right"><? echo fn_number_format(($gr_tot_yarn_cost/$gr_tot_yarn_qnty),4); ?></td>
                            <td align="center">KG</td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                            <td>Accessories</td>
                            <td align="right"><? echo fn_number_format($gr_tot_trims_cost,2); ?></td>
                            <td align="right">&nbsp;</td>
                            <td align="right"><? echo fn_number_format(($gr_tot_trims_cost/$gr_tot_order_qnty)*12,4); ?></td>
                            <td align="center">Dzn</td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                            <td>Knitting</td>
                            <td align="right"><? echo fn_number_format($gr_tot_knitt_cost,2); ?></td>
                            <td align="right">&nbsp;</td>
                            <td align="right"><? echo fn_number_format(($gr_tot_knitt_cost/$gr_tot_yarn_qnty),4); ?></td>
                            <td align="center">KG</td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                            <td>Dyeing/Yarn Dyeing</td>
                            <td align="right"><? echo fn_number_format($gr_tot_dye_cost,2); ?></td>
                            <td align="right">&nbsp;</td>
                            <td align="right"><? echo fn_number_format(($gr_tot_dye_cost/$gr_tot_yarn_qnty),4); ?></td>
                            <td align="center">KG</td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                            <td>Finishing</td>
                            <td align="right"><? echo fn_number_format($gr_tot_finish_cost,2); ?></td>
                            <td align="right">&nbsp;</td>
                            <td align="right"><? echo fn_number_format(($gr_tot_finish_cost/$gr_tot_order_qnty)*12,4); ?></td>
                            <td align="center">Dzn</td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                            <td>Printing & Embroidery</td>
                            <td align="right"><? echo fn_number_format($gr_tot_print_embro_cost,2); ?></td>
                            <td align="right">&nbsp;</td>
                            <td align="right"><? echo fn_number_format(($gr_tot_print_embro_cost/$gr_tot_order_qnty)*12,4); ?></td>
                            <td align="center">Dzn</td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                            <td>Washing</td>
                            <td align="right"><? echo fn_number_format($gr_tot_wash_cost,2); ?></td>
                            <td align="right">&nbsp;</td>
                            <td align="right"><? echo fn_number_format(($gr_tot_wash_cost/$gr_tot_order_qnty)*12,4); ?></td>
                            <td align="center">Dzn</td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                            <td>Testing</td>
                            <td align="right"><? echo fn_number_format($gr_tot_test_cost,2); ?></td>
                            <td align="right">&nbsp;</td>
                            <td align="right"><? echo fn_number_format(($gr_tot_test_cost/$gr_tot_order_qnty)*12,4); ?></td>
                            <td align="center">Dzn</td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                            <td>Commercial</td>
                            <td align="right"><? echo fn_number_format($gr_tot_commercial_cost,2); ?></td>
                            <td align="right">&nbsp;</td>
                            <td align="right"><? echo fn_number_format(($gr_tot_commercial_cost/$gr_tot_order_qnty)*12,4); ?></td>
                            <td align="center">Dzn</td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                            <td>Commisssion</td>
                            <td align="right"><? echo fn_number_format($gr_tot_commission_cost,2); ?></td>
                            <td align="right">&nbsp;</td>
                            <td align="right"><? echo fn_number_format(($gr_tot_commission_cost/$gr_tot_order_val)*100,4);?></td>
                            <td align="center">%</td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                            <td>Freight</td>
                            <td align="right"><? echo fn_number_format($gr_tot_freight_cost,2); ?></td>
                            <td align="right">&nbsp;</td>
                            <td align="right"><? echo fn_number_format(($gr_tot_freight_cost/$gr_tot_order_qnty)*12,4); ?></td>
                            <td align="center">Dzn</td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                            <td>Inspection</td>
                            <td align="right"><? echo fn_number_format($gr_tot_inspection_cost,2); ?></td>
                            <td align="right">&nbsp;</td>
                            <td align="right"><? echo fn_number_format(($gr_tot_inspection_cost/$gr_tot_order_qnty)*12,4); ?></td>
                            <td align="center">Dzn</td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                            <td>Knit Fabric Purchase</td>
                            <td align="right"><? echo fn_number_format($gr_tot_knit_purc_cost,2); ?></td>
                            <td align="right">&nbsp;</td>
                            <td align="right"><? echo fn_number_format(($gr_tot_knit_purc_cost/$gr_tot_order_qnty)*12,4); ?></td>
                            <td align="center">KG</td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                            <td>Woven Fabric Purchase</td>
                            <td align="right"><? echo fn_number_format($gr_tot_woven_purc_cost,2); ?></td>
                            <td align="right">&nbsp;</td>
                            <td align="right"><? echo fn_number_format(($gr_tot_woven_purc_cost/$gr_tot_order_qnty)*12,4); ?></td>
                            <td align="center">Yds</td>
                        </tr>
                        <tr height="15"><td colspan="4">&nbsp;</td></tr>
                        <tr bgcolor="#E9F3FF">
                            <td align="right"><b>Total</b></td>
                            <td align="right" title="<? echo $tot_cost_heads."=yarn".$gr_tot_yarn_cost."=trims".$gr_tot_trims_cost."=knit".$gr_tot_knitt_cost."=dyeing".$gr_tot_dye_cost."=embro".$gr_tot_print_embro_cost."=wash".$gr_tot_wash_cost."=test".$gr_tot_test_cost."=commercial".$gr_tot_commercial_cost."=commission".$gr_tot_commission_cost."=freight".$gr_tot_freight_cost."=inspection".$gr_tot_inspection_cost."=finish".$gr_tot_finish_cost."=knit purchase".$gr_tot_knit_purc_cost."=weven purchase".$gr_tot_woven_purc_cost; ?>"><b><? echo fn_number_format($tot_cost_heads,2); ?></b></td>
                            <td align="right"><b><? echo fn_number_format($cost_heads_percentage=($tot_cost_heads/$gr_tot_order_val)*100,2)."%"; ?></b></td> 
                            <td align="right">&nbsp;</td>
                            <td align="center">&nbsp;</td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
    
                            <td align="right"><b>CM Percentage</b></td>
                            <td align="right" title="<? echo $test_cm_cos; ?>"><b><? echo fn_number_format($gr_tot_cm_cost,2); ?></b></td>
                            <td align="right"><b><? echo fn_number_format($cm_percentage=($gr_tot_cm_cost/$gr_tot_order_val)*100,2)."%"; ?></b></td> 
                            <td align="right">&nbsp;</td>
                            <td align="center">&nbsp;</td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                            <td align="right"><b>Grand Total</b></td>
                            <td align="right"><b><? echo fn_number_format($tot_cost_heads+$gr_tot_cm_cost,2); ?></b></td>
                            <td align="right"><b><? echo fn_number_format($cost_heads_percentage+$cm_percentage,2)."%"; ?></b></td> 
                            <td align="right">&nbsp;</td>
                            <td align="center">&nbsp;</td>
                        </tr>
                    </table>
                </td>
                <td valign="bottom" width="400" style="font-size:14px; padding-left:30px" align="left">
                    <b>Total Garments Qnty (Pcs):&nbsp;&nbsp;<? echo fn_number_format($tot_gmts_qnty,2); ?></b><br />
                    <b>Avg Unit Price (Pcs):&nbsp;&nbsp;<? echo fn_number_format($gr_tot_order_val/$gr_tot_order_qnty,2); ?></b><br />
                    <b>Avg CM Per Dzn:&nbsp;&nbsp;<? echo fn_number_format(($gr_tot_cm_cost/$gr_tot_order_qnty)*12,2); ?></b><br />
                    
                    <b title="<?='Tot Min='.$tot_minutes; ?>">Avg SMV:&nbsp;&nbsp;<? echo fn_number_format(($tot_minutes/$gr_tot_order_qnty),2); ?></b><br />
                    <b title="<?='Tot Att. Cost='.$tot_attach_cost; ?>">Avg CPM:&nbsp;&nbsp;<? echo fn_number_format(($tot_attach_cost/$tot_minutes),2); ?></b><br />
                    <b title="<?='Tot Att. CM='.$gr_tot_cm_cost; ?>">Avg EPM:&nbsp;&nbsp;<? echo fn_number_format(($gr_tot_cm_cost/$tot_minutes),2); ?></b>
                </td>
                <td width="470" valign="top">
                    <u><b>Material Cost for BBLC only:</b></u>
                    <table class="rpt_table" cellpadding="0" cellspacing="0" rules="all" border="1"> 
                        <thead>
                            <th width="170">Cost Item</th>
                            <th width="130">Cost Amount</th>
                            <th width="130">% On Net Value</th>
                        </thead>
                        <tr bgcolor="#E9F3FF">
                            <td>Yarn</td>
                            <td align="right"><? echo fn_number_format($gr_tot_yarn_cost,2); $tot_charge+=$gr_tot_yarn_cost; ?></td>
                            <td align="right"><? echo fn_number_format(($gr_tot_yarn_cost/$gr_tot_net_cs_val)*100,4); $tot_charge_perc+=($gr_tot_yarn_cost/$gr_tot_net_cs_val)*100; ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                            <td>Accessories</td>
                            <td align="right"><? echo fn_number_format($gr_tot_trims_cost,2); $tot_charge+=$gr_tot_trims_cost; ?></td>
                            <td align="right"><? echo fn_number_format(($gr_tot_trims_cost/$gr_tot_net_cs_val)*100,4); $tot_charge_perc+=($gr_tot_trims_cost/$gr_tot_net_cs_val)*100; ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                            <td>Knitting</td>
                            <td align="right"><? echo fn_number_format($knit_charge,2); $tot_charge+=$knit_charge; ?></td>
                            <td align="right"><? $knit_charge_perc=($knit_charge/$gr_tot_net_cs_val)*100; echo fn_number_format($knit_charge_perc,4); ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                            <td>Dyeing/Yarn Dyeing</td>
                            <td align="right"><? echo fn_number_format($fab_charge,2); $tot_charge+=$fab_charge; ?></td>
                            <td align="right"><? $fab_charge_perc=($fab_charge/$gr_tot_net_cs_val)*100; echo fn_number_format($fab_charge_perc,4); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                            <td>AOP</td>
                            <td align="right"><? echo fn_number_format($aop_charge,2); $tot_charge+=$aop_charge; ?></td>
                            <td align="right"><? $aop_charge_perc=($aop_charge/$gr_tot_net_cs_val)*100; echo fn_number_format($aop_charge_perc,4); ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                            <td>Printing & Embroidery</td>
                            <td align="right"><? echo fn_number_format($print_embro_charge,2); $tot_charge+=$print_embro_charge; ?></td>
                            <td align="right"><? $print_embro_charge_perc=($print_embro_charge/$gr_tot_net_cs_val)*100; echo fn_number_format($print_embro_charge_perc,4); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                            <td>Washing</td>
                            <td align="right"><? echo fn_number_format($wash_charge,2); $tot_charge+=$wash_charge; ?></td>
                            <td align="right"><? $wash_charge_perc=($wash_charge/$gr_tot_net_cs_val)*100; echo fn_number_format($wash_charge_perc,4); ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                            <td>Knit Fabric Purchase</td>
                            <td align="right"><? echo fn_number_format($tot_knit_purc_cost,2); $tot_charge+=$tot_knit_purc_cost;//fn_number_format($cost_heads_knit_purchase,2); $tot_charge+=$cost_heads_knit_purchase; ?></td>
                            <td align="right"><? $knit_purchase_perc=($cost_heads_knit_purchase/$gr_tot_net_cs_val)*100; echo fn_number_format($knit_purchase_perc,4); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                            <td>Woven Fabric Purchase</td>
                            <td align="right"><? echo fn_number_format($cost_heads_woven_purc,2); $tot_charge+=$cost_heads_woven_purc; ?></td>
                            <td align="right"><? $woven_purchase_perc=($cost_heads_woven_purc/$gr_tot_net_cs_val)*100; echo fn_number_format($woven_purchase_perc,4); ?></td>
                        </tr>
                        <tr height="15"><td colspan="3">&nbsp;</td></tr>
                        <tr bgcolor="#CCCCCC">
                            <td align="right"><b>Total</b></td>
                            <td align="right"><b><? echo fn_number_format($tot_charge,2); ?></b></td>
                            <td align="right"><b><? $tot_charge_perc=($tot_charge/$gr_tot_net_cs_val)*100; echo fn_number_format($tot_charge_perc,4); ?></b></td>
                        </tr>
                    </table>
                </td>
        	</tr>
            <tr>
            	<td height="30" colspan="3">&nbsp;</td>
            </tr>
            <tr>
            	<td>
                <table border="1" rules="all">
                	<tr align="center">
                    	<td width="200"><b>Recommendation For:</b></td>
                        <td width="200"><b>BB Limit</b></td>
                        <td width="200"><b><? echo fn_number_format($tot_charge_perc,2)."%"; ?></b></td>
                    </tr>
                </table>
                </td>
                <td>
                <table border="1" rules="all">
                	<tr align="center">
                    	<td width="200"><b>Approved</b></td>
                        <td width="200"><b>BB Limit</b></td>
                        <td width="100">&nbsp;</td>
                    </tr>
                </table>
                </td>
                <td>&nbsp;</td>
            </tr>
        </table>
        <?
		$signeture_allow_or_no = return_field_value("report_list","variable_settings_report","module_id=7 and company_id=$company_name");
		$signeture_list = explode('#',$signeture_allow_or_no);
		$td_width=1400/count($signeture_list);
		if($signeture_allow_or_no!= "")	
		{
		 echo '<table width="1400">
			<tr>
				<td width="100%" height="90" colspan="'.count($signeture_list).'"> </td>
			</tr> 
			<tr style="font-size:14px">';
			foreach($signeture_list as $key=>$value)
			{
				echo '<td width="'.$td_width.'" align="center"><strong style="text-decoration:overline">'.$value.'</strong><br /><strong>'.$group_details[$group_id_details[$company_name]].'</strong></td>';
			}
			echo '</tr></table>';
		}
    	echo signature_table(97, $company_name, "1700px");
    ?>
	</div>
</fieldset>
<? else: ?>
<fieldset style="width:100%; border:hidden">
<div id="report_container">
	<div align="center" style="height:30px; width:1625px; font-size:18px; border:hidden" class="form_caption">Cost Sheet Analysis [Attach Qty. Wise] Woven</div>
	<div style="width:1625px">
        <table width="1620" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" style="font-size:12px">
            <thead style="width:100%;" class="table_header" >
                <tr bgcolor="#CCCCCC" height="30">
                    <td colspan="57" style="font-size:16px; border:none" width="100%"><b>File No: <? echo $file_no; ?>, &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Buyer name: <? echo $buyer_details[$file_info[1]]; ?>, &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Applicant name: <? echo $buyer_details[$file_info[2]]; ?></b><span id="agent_name"></span>
                    </td>
                </tr>
                <tr height="100">
                    <th width="20" valign="middle">SL</th>
                    <th width="100" valign="middle">LC/SC No.</th>
                    <th width="70" valign="middle">Style Ref.</th>
                    <th width="50" valign="middle">Work. Factory</th>
                    <th width="60" valign="middle">Order No</th>
                    <th width="70" valign="middle">Fabric Desc.</th>
                    <th width="30" valign="bottom"><div class="r90">Po&nbsp;Qty</div></th>
                    <th width="30" valign="bottom"><div class="r90">Attached&nbsp;Qty</div></th>
                    <th width="30" valign="bottom"><div class="r90">Balance</div></th>
                    <th width="30" valign="bottom"><div class="r90">Price[C/S]</div></th>
                    <th width="30" valign="bottom"><div class="r90">Price[P/O]</div></th>
                    <th width="30" valign="bottom"><div class="r90">Price[L/C]</div></th>
                    <th width="30" valign="bottom"><div class="r90">Value[C/S]</div></th>
                    <th width="30" valign="bottom"><div class="r90">Value[L/C]</div></th>
                    <th width="30" valign="bottom"><div class="r90">CommerCost</div></th>
                    <th width="30" valign="bottom"><div class="r90">FreightCost</div></th>
                    <th width="30" valign="bottom"><div class="r90">Commi[C/S]</div></th>
                    <th width="30" valign="bottom"><div class="r90">Commi[L/C]</div></th>
                    <th width="30" valign="bottom"><div class="r90">NetValue[C/S]</div></th>
                    <th width="30" valign="bottom"><div class="r90">NetValue[L/C]</div></th>  
                    <th width="30" valign="bottom"><div class="r90">ShipmentDate</div></th>
                    <th width="30" valign="bottom"><div class="r90">Ex-FactoryDate</div></th>
                    <th width="30" valign="bottom"><div class="r90">Ex-FactoryQty</div></th>
                    <th width="30" valign="bottom"><div class="r90">Ex-FactoryValue</div></th>
                    <th colspan="33">
                        <table width="100%" height="100" border="0" cellpadding="0" cellspacing="0" rules="all">
                            <tr height="50%">
                                <th colspan="2" width="61" align="center" style="height: 50px;">Access.</th>
                                <th colspan="2" width="61" align="center" style="height: 50px;">Print & Embro.</th>
                                <th colspan="2" width="61" align="center" style="height: 50px;">Washing</th>
                                <th colspan="2" width="61" align="center" style="height: 50px;">Test</th>
                                <th colspan="2" width="61" align="center" style="height: 50px;">Insp.</th>
                                <th colspan="4" width="124" align="center" style="height: 50px;">Knit Fab. Purc.</th>
                                <th colspan="4" width="124" align="center" style="height: 50px;">Woven Fab. Purc.</th>
                                <th colspan="3" width="93" align="center" style="height: 50px;">CM</th>
                            </tr>
                            <tr>
                                <th width="30" valign="bottom"><div class="r90">Cost</div></th>
                                <th width="30" valign="bottom"><div class="r90">%</div></th>

                                <th width="30" valign="bottom"><div class="r90">Cost</div></th>
                                <th width="30" valign="bottom"><div class="r90">%</div></th>

                                <th width="30" valign="bottom"><div class="r90">Cost</div></th>
                                <th width="30" valign="bottom"><div class="r90">%</div></th>
                                <th width="30" valign="bottom"><div class="r90">Cost</div></th>
                                <th width="30" valign="bottom"><div class="r90">%</div></th>
                                <th width="30" valign="bottom"><div class="r90">Cost</div></th>
                                <th width="30" valign="bottom"><div class="r90">%</div></th>
                                
                                <th width="30" valign="bottom"><div class="r90">Qnty</div></th>
                                <th width="30" valign="bottom"><div class="r90">Rate</div></th>
                                <th width="30" valign="bottom"><div class="r90">Cost</div></th>
                                <th width="30" valign="bottom"><div class="r90">%</div></th>
                                
                                <th width="30" valign="bottom"><div class="r90">Qnty</div></th>
                                <th width="30" valign="bottom"><div class="r90">Rate</div></th>
                                <th width="30" valign="bottom"><div class="r90">Cost</div></th>
                                <th width="30" valign="bottom"><div class="r90">%</div></th>
                                
                                <th width="30" valign="bottom"><div class="r90">Value</div></th>
                                <th width="30" valign="bottom"><div class="r90">%</div></th>
                                <th width="30" valign="bottom"><div class="r90">Per&nbsp;Dzn</div></th>
                            </tr>
                        </table>
                    </th>
                </tr>
            </thead>
            <tbody class="table_body" id="scroll_body" width="1608">
        	<? 
				$i=1; $c=1; $agent_name=''; $knit_charge=0; $aop_charge=0; $fab_charge=0; $print_embro_charge=0; $wash_charge=0; $tot_gmts_qnty=0; $cost_heads_knit_purchase=0; $cost_heads_woven_purc=0;
				
				$sql_order="select b.wo_po_break_down_id as po_id, 1 as type 
				from com_export_lc a, com_export_lc_order_info b 
				where a.id=b.com_export_lc_id and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.lc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 
				union 
				select b.wo_po_break_down_id as po_id, 2 as type 
				from com_sales_contract a, com_sales_contract_order_info b 
				where a.id=b.com_sales_contract_id and a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.sc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
				$result_order=sql_select( $sql_order );
				$all_order_id="";
				foreach($result_order as $row)
				{
					if($ord_check[$row[csf("po_id")]]=="")
					{
						$ord_check[$row[csf("po_id")]]=$row[csf("po_id")];
						$all_order_id.=$row[csf("po_id")].",";
					}
				}
				$all_order_id=chop($all_order_id,",");
				$exfactory_arr=array();
				if($all_order_id!="")
				{
					$job_sql=sql_select("select job_no_mst, job_id from wo_po_break_down where status_active=1 and is_deleted=0 and id in($all_order_id)");
					$all_job=$all_job_id="";
					foreach($job_sql as $row)
					{
						if($job_check[$row[csf("job_no_mst")]]=="")
						{
							$job_check[$row[csf("job_no_mst")]]=$row[csf("job_no_mst")];
							$all_job.="'".$row[csf("job_no_mst")]."',";
                            $all_job_id.=$row[csf("job_id")].',';
						}
					}
					$all_job=chop($all_job,",");
                    $all_job_ids=chop($all_job_id,",");
					$fabriccostArray=array();
					$costing_sql=sql_select("select job_no, costing_per_id, freight, comm_cost, commission, lab_test, inspection, common_oh from wo_pre_cost_dtls where status_active=1 and is_deleted=0 and job_id in($all_job_ids)");
					foreach($costing_sql as $row)
					{
						$fabriccostArray[$row[csf('job_no')]]['cpi']=$row[csf('costing_per_id')]; 
						$fabriccostArray[$row[csf('job_no')]]['freight']=$row[csf('freight')];
						$fabriccostArray[$row[csf('job_no')]]['comm_cost']=$row[csf('comm_cost')];
						$fabriccostArray[$row[csf('job_no')]]['commission']=$row[csf('commission')];
						$fabriccostArray[$row[csf('job_no')]]['lab_test']=$row[csf('lab_test')];
						$fabriccostArray[$row[csf('job_no')]]['inspection']=$row[csf('inspection')];
						$fabriccostArray[$row[csf('job_no')]]['common_oh']=$row[csf('common_oh')];
					}
					unset($costing_sql);
					
					$sql_ship="select A.PO_BREAK_DOWN_ID, MAX(A.EX_FACTORY_DATE) AS EX_FACTORY_DATE, SUM(A.EX_FACTORY_QNTY) AS EX_FACTORY_QNTY from pro_ex_factory_mst a where a.po_break_down_id in($all_order_id) and a.is_deleted=0 and a.status_active=1 group by a.po_break_down_id";
					$sql_ship_res=sql_select($sql_ship);
					foreach($sql_ship_res as $row)
					{
						$exfactory_arr[$row["PO_BREAK_DOWN_ID"]]['exfactqty']=$row["EX_FACTORY_QNTY"];
						$exfactory_arr[$row["PO_BREAK_DOWN_ID"]]['exfactdate']=$row["EX_FACTORY_DATE"];
					}
					unset($sql_ship_res);
				}
				
				
				$trimscostArray=array();
				$condition= new condition();
				/*if($company_name>0){
					$condition->company_name("=$company_name");
				}*/
				if($all_order_id !=""){
					//$condition->po_id(" in($all_order_id)");
					$condition->po_id_in("$all_order_id"); 
                    $condition->jobid_in("$all_job_ids");
                   // $condition->company_name($company_name);
                    $condition->company_name("=$company_name");
				
					$condition->init();
					$trims= new trims($condition);
					//echo $trims->getQuery(); die;
					$trimscostArray=$trims->getAmountArray_by_order();
					
					$yarns= new yarn($condition);
					//echo $yarns->getQuery(); die;
					$yarnQntyArray=$yarns->getOrderWiseYarnQtyArray();
					$yarncostArray=$yarns->getOrderWiseYarnAmountArray();
					//echo "<pre>";print_r($yarncostArray);die;
					
					$fabric= new fabric($condition);
					//echo $fabric->getQuery(); die;
					$fabricCostArrayClass=$fabric->getAmountArray_by_orderAndFabricSource_knitAndwoven_greyAndfinish();
					$fabricQntyArrayClass=$fabric->getQtyArray_by_orderAndFabricSource_knitAndwoven_greyAndfinish();
					
					$conversion= new conversion($condition);
					//echo $conversion->getQuery(); die;
					$conversion_cost_arr=$conversion->getAmountArray_by_orderAndProcess();
				}
				//echo "<pre>";print_r($conversion_cost_arr);die;
				//echo "<pre>";print_r($fabricQntyArrayClass['knit']['finish'][23590][2]);die;
				
				$emblcostArray=array();
				$emblArray=sql_select("select job_no, emb_name, sum(amount) as amount from wo_pre_cost_embe_cost_dtls where status_active=1 and is_deleted=0 group by job_no, emb_name");
				foreach($emblArray as $row)
				{
					$emblcostArray[$row[csf('job_no')]][$row[csf('emb_name')]]=$row[csf('amount')]; 
				}
				unset($emblArray);
				
				$woven_knit_purchase_cost_arr=array(); $fabric_desc_arr=array();
				
				$fabricArray=sql_select("select job_no, fab_nature_id, fabric_source, fabric_description, amount from wo_pre_cost_fabric_cost_dtls where status_active=1 and is_deleted=0 and fabric_source!=3");
				foreach($fabricArray as $row)
				{
					$fabric_desc_arr[$row[csf('job_no')]].=$row[csf('fabric_description')].","; 
				}
				unset($fabricArray);
				
				$fabricPurcArray=sql_select("select job_no, woven_amount, knit_amount from wo_pre_cost_sum_dtls where status_active=1 and is_deleted=0");
				foreach($fabricPurcArray as $row)
				{
					$woven_knit_purchase_cost_arr[$row[csf('job_no')]]['woven']+=$row[csf('woven_amount')];
					$woven_knit_purchase_cost_arr[$row[csf('job_no')]]['knit']+=$row[csf('knit_amount')];  
				}
				unset($fabricPurcArray);
				
				//print_r($woven_knit_purchase_cost_arr['SCL1-23-00064']);
				
				
		 		$sql="select a.id, a.export_lc_no as sc_lc_no, a.lc_date as lc_sc_date, a.last_shipment_date, a.lc_value as lc_sc_value, a.foreign_comn, a.local_comn, 1 as type from com_export_lc a where a.internal_file_no='$file_no' and a.beneficiary_name='$company_name' and a.lien_bank like '$bank_id' and a.lc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 
				union 
					select b.id, b.contract_no as sc_lc_no, b.contract_date as lc_sc_date, b.last_shipment_date, b.contract_value as lc_sc_value, b.foreign_comn, b.local_comn, 2 as type from com_sales_contract b where b.internal_file_no='$file_no' and b.beneficiary_name='$company_name' and b.lien_bank like '$bank_id' and b.sc_year like '$text_year' and b.status_active=1 and b.is_deleted=0
				";
				$nameArray=sql_select( $sql );
				$gr_tot_yarn_cost=$gr_tot_trims_cost=$gr_tot_knitt_cost=$gr_tot_dye_cost=$gr_tot_print_embro_cost=$gr_tot_wash_cost=$gr_tot_test_cost=$gr_tot_commercial_cost=$gr_tot_commission_cost=$gr_tot_freight_cost=$gr_tot_inspection_cost=$gr_tot_finish_cost=$gr_tot_knit_purc_cost=0;
				foreach ($nameArray as $selectResult)
				{
					$j=1; $tot_order_qnty=0; $tot_order_val=0; $tot_lc_value=0; $tot_commercial_cost=0; $tot_freight_cost=0; $tot_commission_cost=0; $tot_lc_comn_cost=0; $tot_net_cs_val=0; $tot_net_lc_val=0; $tot_yarn_qnty=0; $tot_yarn_cost=0; $tot_trims_cost=0; $tot_knitt_cost=0; $tot_dye_cost=0; $tot_print_embro_cost=0; $tot_wash_cost=0; $tot_test_cost=0; $tot_inspection_cost=0; $tot_finish_cost=0; $tot_cm_cost=0; $tot_knit_purc_cost=0; $tot_woven_purc_cost=0;$tot_aop_cost=0;$tot_knit_purc_qnty=0;$tot_woven_purc_qnty=0;$tot_po_qnty_pcs=0;$tot_po_balance=0; $tot_exfactoryqty=0; $tot_exfactoryvalue=0;
					
					if($selectResult[csf('type')]==1)
					{
						$query="select d.company_name, d.style_ref_no, d.agent_name, d.order_uom, c.id as po_id, c.po_number, c.pub_shipment_date as shipment_date, c.job_no_mst, c.po_quantity,(d.total_set_qnty*c.po_quantity) as po_qnty_in_pcs, ((d.total_set_qnty*c.po_quantity)*d.set_smv) as smvmin, c.unit_price, b.attached_qnty, b.attached_rate, b.attached_value, d.total_set_qnty from com_export_lc_order_info b, wo_po_break_down c, wo_po_details_master d where b.com_export_lc_id='".$selectResult[csf('id')]."' and b.wo_po_break_down_id=c.id and c.job_no_mst=d.job_no and b.status_active=1 and b.is_deleted=0 order by d.company_name";
					}
					else
					{
						$query="select d.company_name, d.style_ref_no, d.agent_name, d.order_uom, c.id as po_id, c.po_number, c.pub_shipment_date as shipment_date, c.job_no_mst, c.po_quantity,(d.total_set_qnty*c.po_quantity) as po_qnty_in_pcs, ((d.total_set_qnty*c.po_quantity)*d.set_smv) as smvmin, c.unit_price, b.attached_qnty, b.attached_rate, b.attached_value, d.total_set_qnty from com_sales_contract_order_info b, wo_po_break_down c, wo_po_details_master d where b.com_sales_contract_id='".$selectResult[csf('id')]."' and b.wo_po_break_down_id=c.id and c.job_no_mst=d.job_no and b.status_active=1 and b.is_deleted=0 order by d.company_name";

					}
					
					$result=sql_select( $query );
					$tot_rows=count($result);
					foreach ($result as $row)
					{
						if ($j%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						$tot_gmts_qnty+=$row[csf('po_qnty_in_pcs')];
						
						$tot_minutes+=$row[csf('smvmin')];	
						if($agent_name=='')	$agent_name=$buyer_details[$row[csf('agent_name')]]; else $agent_name=$agent_name;
						
						$commercial_cost=0; $freight_cost=0; $commission_cost=0; $lc_comn_cost=0; $net_cs_val=0; $net_lc_val=0; $yarn_qnty=0; $yarn_cost=0; $yarn_cost_perc=0; $trims_cost=0; $trims_cost_perc=0; $test_cost=0; $test_cost_perc=0; $inspection_cost=0; $inspection_cost_perc=0; $finish_cost=0; $finish_cost_perc=0; $knitt_cost=0; $knitt_cost_percent=0; $others_cost=0; $others_cost_percent=0; $dye_cost=0; $dye_cost_percent=0; $wash_cost=0; $wash_cost_percent=0; $print_embro_cost=0; $print_embro_cost_percent=0; $cm_cost=0; $cm_cost_perc=0;$aop_cost=0;$aop_cost_percent=0;$attach_woven_purc_qnty=$woven_purc_rate=$attach_yarn_cost=$attach_knit_purc_qnty=$knit_purc_rate=0;
						
						$unit_price=$row[csf('unit_price')];
						$order_quantity=$row[csf('attached_qnty')];
						$order_val=$row[csf('attached_qnty')]*$unit_price;
						$att_vale=$row[csf('attached_value')];
						
						$dzn_qnty=0;
						$costing_per_id=$fabriccostArray[$row[csf('job_no_mst')]]['cpi'];
						if($costing_per_id==1) $dzn_qnty=12;
						else if($costing_per_id==3) $dzn_qnty=12*2;
						else if($costing_per_id==4) $dzn_qnty=12*3;
						else if($costing_per_id==5) $dzn_qnty=12*4;
						else $dzn_qnty=1;
						
						//$dzn_qnty=$dzn_qnty*$row[csf('ratio')];
						
						$freight_cost=($order_quantity/$dzn_qnty)*$fabriccostArray[$row[csf('job_no_mst')]]['freight'];
						$commercial_cost=($order_quantity/$dzn_qnty)*$fabriccostArray[$row[csf('job_no_mst')]]['comm_cost'];
						$commission_cost=($order_quantity/$dzn_qnty)*$fabriccostArray[$row[csf('job_no_mst')]]['commission'];
						/*$freight_cost=$fabriccostArray[$row[csf('job_no_mst')]]['freight'];
						$commercial_cost=$fabriccostArray[$row[csf('job_no_mst')]]['comm_cost'];
						$commission_cost=$fabriccostArray[$row[csf('job_no_mst')]]['commission'];*/
						
						//$test_cost=$fabriccostArray[$row[csf('job_no_mst')]]['lab_test'];
						$test_cost=($order_quantity/$dzn_qnty)*$fabriccostArray[$row[csf('job_no_mst')]]['lab_test'];
						$test_cost_perc=($test_cost/$order_val)*100;
						$inspection_cost=($order_quantity/$dzn_qnty)*$fabriccostArray[$row[csf('job_no_mst')]]['inspection'];
						//$inspection_cost=$fabriccostArray[$row[csf('job_no_mst')]]['inspection'];
						$inspection_cost_perc=($inspection_cost/$order_val)*100;
						 
						$common_oh_cost=$fabriccostArray[$row[csf('job_no_mst')]]['common_oh'];// yet not used
						$common_oh_cost_perc=($common_oh_cost/$order_val)*100;// yet not used
						
						//$trims_cost_total=$trimscostArray[0][csf('trims_cost_total')];
						$trims_cost_total=$trimscostArray[$row[csf('po_id')]];
						//$trims_cost=($order_quantity/$dzn_qnty)*$trims_cost_total;
						$trims_cost=$trimscostArray[$row[csf('po_id')]];
						$trims_cost_perc=($trims_cost/$order_val)*100;
						
						$foreign_comn=$selectResult[csf('foreign_comn')];
						$local_comn=$selectResult[csf('local_comn')];
						$yarn_qnty=$yarn_avg_qnty=$yarn_cost=$yarn_cost_perc=0;
						//$yarn_qnty=($order_quantity/$dzn_qnty)*$yarn_cons_cost_arr[$row[csf('job_no_mst')]]['qnty'];
						//$yarn_avg_qnty=($order_quantity/$dzn_qnty)*$yarn_cons_cost_arr[$row[csf('job_no_mst')]]['avg_qnty'];
						//$yarn_cost=($order_quantity/$dzn_qnty)*$yarn_cons_cost_arr[$row[csf('job_no_mst')]]['amount'];
						if($yarnQntyArray[$row[csf('po_id')]]) $yarn_avg_qnty=$yarnQntyArray[$row[csf('po_id')]];
						if($yarncostArray[$row[csf('po_id')]]) $yarn_cost=$yarncostArray[$row[csf('po_id')]];
						$yarn_cost_perc=($yarn_cost/$order_val)*100;
						
						//echo $yarn_cost."_".$row[csf('po_id')]."<br>";
						$fabric_desc=implode(",",array_unique(explode(",",chop($fabric_desc_arr[$row[csf('job_no_mst')]],','))));
						
						$lc_comn_cost=($att_vale*($foreign_comn+$local_comn))/100;
						$net_cs_val=$order_val-$commercial_cost-$freight_cost-$commission_cost;
						$net_lc_val=$att_vale-$commercial_cost-$freight_cost-$lc_comn_cost;
						
						foreach($conversion_cost_arr[$row[csf('po_id')]] as $cons_process=>$uomdata)
						{
							foreach($uomdata as $uom=>$amnt)
							{
								if($cons_process==1 || $cons_process==3 || $cons_process==4 || $cons_process==134)
								{
									$knitt_cost+=($amnt/$row[csf('po_quantity')])*$order_quantity;
									if($cost_details[$row[csf('company_name')]][1]==1)
									{
										//$knit_charge+=($order_quantity/$dzn_qnty)*$amnt;
										$knit_charge+=($amnt/$row[csf('po_quantity')])*$order_quantity;
									}
								}
								else if($cons_process==30 || $cons_process==31)
								{
									$dye_cost+=($amnt/$row[csf('po_quantity')])*$order_quantity;
									
									if($cost_details[$row[csf('company_name')]][$cons_process]==1)
									{
										$fab_charge+=($amnt/$row[csf('po_quantity')])*$order_quantity;
									}
								}
								else
								{
									$finish_cost+=($amnt/$row[csf('po_quantity')])*$order_quantity;
									if($cons_process==35 || $cons_process==36 || $cons_process==37 || $cons_process==40)
									{
										if($cost_details[$row[csf('company_name')]][35]==1)
										{
											//$aop_charge+=($order_quantity/$dzn_qnty)*$amnt;
											$aop_charge+=($amnt/$row[csf('po_quantity')])*$order_quantity;
											$aop_cost+=($amnt/$row[csf('po_quantity')])*$order_quantity;
										}
									}
									else
									{
										if($cost_details[$row[csf('company_name')]][31]==1)
										{
											$fab_charge+=($amnt/$row[csf('po_quantity')])*$order_quantity;
										}
									}
								}
							}
						}
						
						$knitt_cost_percent=($knitt_cost/$order_val)*100;
						$dye_cost_percent=($dye_cost/$order_val)*100;
						$finish_cost_percent=($finish_cost/$order_val)*100;
						
						foreach($emblcostArray[$row[csf('job_no_mst')]] as $emb_name=>$amnt)
						{
							$cost_heads_new=$emb_name+100;
							if($emb_name==3)
							{
								$wash_cost=($amnt/$dzn_qnty)*$order_quantity;
								if($cost_details[$row[csf('company_name')]][$cost_heads_new]==1)
								{
									$wash_charge+=((($amnt/$dzn_qnty)*$order_quantity)/$row[csf('po_quantity')])*$order_quantity;
								}
							}
							else
							{
								$print_embro_cost+=($amnt/$dzn_qnty)*$order_quantity;
								if($cost_details[$row[csf('company_name')]][$cost_heads_new]==1) 
								{
									$print_embro_charge+=((($amnt/$dzn_qnty)*$order_quantity)/$row[csf('po_quantity')])*$order_quantity;
								}
							}
						}
						
						if($cost_details[$row[csf('company_name')]][75]==1)
						{
							//echo $woven_knit_purchase_cost_arr[$row[csf('job_no_mst')]]['knit']."<br>All ";
							$knit_purchase_cost=$woven_knit_purchase_cost_arr[$row[csf('job_no_mst')]]['knit'];
							$cost_heads_knit_purchase+=($knit_purchase_cost/$row[csf('po_quantity')])*$order_quantity;
							//($order_quantity/$dzn_qnty)*$knit_purchase_cost;
						}
						
						if($cost_details[$row[csf('company_name')]][78]==1)
						{
							$woven_purchase_cost=$woven_knit_purchase_cost_arr[$row[csf('job_no_mst')]]['woven'];
							$cost_heads_woven_purc+=($order_quantity/$dzn_qnty)*$woven_purchase_cost;
						}
						$knit_purc_cost=0;
						foreach($fabricCostArrayClass['knit']['finish'][$row[csf('po_id')]][2] as $fab_amt)
						{
							$knit_purc_cost+=$fab_amt;
						}
						$knit_purc_qnty=0;
						foreach($fabricQntyArrayClass['knit']['finish'][$row[csf('po_id')]][2] as $fab_qnty)
						{
							$knit_purc_qnty+=$fab_qnty;
						}
						$woven_purc_qnty=0;
						foreach($fabricQntyArrayClass['woven']['finish'][$row[csf('po_id')]][2] as $fab_qnty)
						{
							$woven_purc_qnty+=$fab_qnty;
						}
						//$knit_purc_cost=($order_quantity/$dzn_qnty)*$woven_knit_purchase_cost_arr[$row[csf('job_no_mst')]]['knit'];
						
						//$woven_purc_cost=($woven_knit_purchase_cost_arr[$row[csf('job_no_mst')]]['woven']/$row[csf('po_quantity')])*$order_quantity;
						$woven_purc_cost=($order_quantity/$dzn_qnty)*$woven_knit_purchase_cost_arr[$row[csf('job_no_mst')]]['woven'];
						//echo $woven_purc_cost.'=('.$woven_knit_purchase_cost_arr[$row[csf('job_no_mst')]]['woven'].'/'.$row[csf('po_quantity')].')*'.$order_quantity.'<br>';
						$wash_cost_percent=($wash_cost/$order_val)*100;
						$print_embro_cost_percent=($print_embro_cost/$order_val)*100;
						$aop_cost_percent=($aop_cost/$order_val)*100;
						$knit_purc_cost_percent=($knit_purc_cost/$order_val)*100;
						$woven_purc_cost_percent=($woven_purc_cost/$order_val)*100;
						
						$cm_cost=$order_val-$commercial_cost-$freight_cost-$commission_cost-$yarn_cost-$trims_cost-$knitt_cost-$dye_cost-$print_embro_cost-$wash_cost-$test_cost-$inspection_cost-$finish_cost-$knit_purc_cost-$woven_purc_cost;
						$cm_cost_perc=($cm_cost/$order_val)*100;
						$cm_per_dzn=($cm_cost/$order_quantity)*12;
						
						$job_no=$row[csf('job_no_mst')];
						$po_id=$row[csf('po_id')];
						$po_no=$row[csf('po_number')];
						
						
						// for yarn yarn_avg_qnty attach_yarn_avg_qnty
						$yarn_rate=$yarn_cost/$yarn_avg_qnty;
						$attached_qnty_pcs=($row[csf('attached_qnty')]*$row[csf('total_set_qnty')]);
						//$att_vale=$attached_qnty_pcs*$row[csf('attached_rate')];
						$ord_val=$row[csf('po_qnty_in_pcs')]*$unit_price;
						$attach_yarn_avg_qnty=(($yarn_avg_qnty*$attached_qnty_pcs)/$row[csf('po_qnty_in_pcs')]);
						if($attach_yarn_avg_qnty>0 && $yarn_rate>0) $attach_yarn_cost=$attach_yarn_avg_qnty*$yarn_rate;
						$attach_yarn_cost_perc=($attach_yarn_cost/$att_vale)*100; 
						
						// for knitting purchase 
						if($knit_purc_cost>0 && $knit_purc_qnty>0) $knit_purc_rate=$knit_purc_cost/$knit_purc_qnty; 
						if($knit_purc_qnty>0) $attach_knit_purc_qnty=($knit_purc_qnty/$row[csf('po_quantity')])*$order_quantity;
						$attach_knit_purc_cost=$attach_knit_purc_qnty*$knit_purc_rate;
						$attach_knit_purc_cost_percent=($attach_knit_purc_cost/$att_vale)*100;
						
						 
						// for woven purchase  
						if($woven_purc_cost>0 && $woven_purc_qnty>0) $woven_purc_rate+=$woven_purc_cost/$woven_purc_qnty; 
						if($woven_purc_qnty>0) $attach_woven_purc_qnty=(($woven_purc_qnty*$attached_qnty_pcs)/$row[csf('po_qnty_in_pcs')]);
						$attach_woven_purc_cost=$attach_woven_purc_qnty*$woven_purc_rate;
						
						$attach_woven_purc_cost_percent=($attach_woven_purc_cost/$att_vale)*100;
						
						// for trims trims_cost_perc knitt_cost
						$attach_trims_cost=($trims_cost/$row[csf('po_quantity')])*$order_quantity;
						$attach_trims_cost_perc=(($attach_trims_cost/$att_vale)*100);
						
						// for Knitting  knitt_cost knitt_cost_percent
						$attach_knitt_cost=$knitt_cost;//($knitt_cost/$row[csf('po_quantity')])*$order_quantity;
						$attach_knitt_cost_percent=(($attach_knitt_cost/$att_vale)*100);
						
						// for dyeing  dye_cost dye_cost_percent 
						$attach_dye_cost=$dye_cost;
						$attach_dye_cost_percent=(($attach_dye_cost/$att_vale)*100);
						
						// for Finish   finish_cost finish_cost_percent
						$attach_finish_cost=$finish_cost;
						$attach_finish_cost_percent=(($attach_finish_cost/$att_vale)*100);
						
						// for print embro print_embro_cost print_embro_cost_percent 
						$attach_print_embro_cost=($print_embro_cost/$row[csf('po_quantity')])*$order_quantity;
						$attach_print_embro_cost_percent=(($attach_print_embro_cost/$att_vale)*100);
						
						// for aop aop_cost aop_cost_percent 
						$attach_aop_cost=$aop_cost;
						$attach_aop_cost_percent=(($attach_aop_cost/$att_vale)*100);
						
						// for wash wash_cost wash_cost_percent 
						$attach_wash_cost=$wash_cost;
						$attach_wash_cost_percent=(($attach_wash_cost/$att_vale)*100);
						
						// for test test_cost test_cost_perc 
						$attach_test_cost=($test_cost/$row[csf('po_quantity')])*$order_quantity;
						$attach_test_cost_perc=(($attach_test_cost/$att_vale)*100);
						
						// for inspection inspection_cost inspection_cost_perc 
						$attach_inspection_cost=($inspection_cost/$row[csf('po_quantity')])*$order_quantity;
						$attach_inspection_cost_perc=(($attach_inspection_cost/$att_vale)*100);
						//if(is_numeric($attach_yarn_cost)) $attach_yarn_cost=$attach_yarn_cost; else $attach_yarn_cost=0;
						//if($attach_yarn_cost==NAN || $attach_yarn_cost=="nan" || $attach_yarn_cost=="") $attach_yarn_cost=0;
						$attach_cost=$commercial_cost+$freight_cost+$commission_cost+$attach_yarn_cost+$attach_trims_cost+$attach_knitt_cost+$attach_dye_cost+$attach_print_embro_cost+$attach_wash_cost+$attach_test_cost+$attach_inspection_cost+$attach_finish_cost+$attach_knit_purc_cost+$attach_woven_purc_cost+$attach_aop_cost;
						
						$tot_attach_cost+=$attach_cost;
						
						$attach_cm_cost=$att_vale-$commercial_cost-$freight_cost-$commission_cost-$attach_yarn_cost-$attach_trims_cost-$attach_knitt_cost-$attach_dye_cost-$attach_print_embro_cost-$attach_wash_cost-$attach_test_cost-$attach_inspection_cost-$attach_finish_cost-$attach_knit_purc_cost-$attach_woven_purc_cost-$attach_aop_cost;
						/*if($attach_cm_cost<0) 
						{
							echo $row[csf('po_number')]."=".$att_vale."=".$commercial_cost."=".$freight_cost."=".$commission_cost."=".$attach_yarn_cost."=".$attach_trims_cost."=".$attach_knitt_cost."=".$attach_dye_cost."=".$attach_print_embro_cost."=".$attach_wash_cost."=".$attach_test_cost."=".$attach_inspection_cost."=".$attach_finish_cost."=".$attach_knit_purc_cost."=".$attach_woven_purc_cost."=".$attach_aop_cost."=".$woven_purc_qnty."=".$woven_purc_cost."=".$attach_woven_purc_qnty."=".$woven_purc_rate;
							die;
						}*/
						$attach_cm_cost_perc=($attach_cm_cost/$att_vale)*100;
						$attach_cm_per_dzn=($attach_cm_cost/$attached_qnty_pcs)*12;
						
						if($j==1)
						{
						?>
                            <tr height="90" bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_l<? echo $c; ?>','<? echo $bgcolor; ?>')" id="tr_l<? echo $c; ?>">
                                <td rowspan="<? echo $tot_rows; ?>" width="20"><? echo $i; ?></td>	
                                <td rowspan="<? echo $tot_rows; ?>" width="100" align="center">
                                    <p>
										<?
											if($selectResult[csf('type')]==1)
											{
												echo "<b>LC : ".$selectResult[csf('sc_lc_no')]."</b><br>";
												echo "Dt: ".change_date_format($selectResult[csf('lc_sc_date')])."<br>"; 
												echo "Val: ".fn_number_format($selectResult[csf('lc_sc_value')],2,'.','')."<br>"; $total_lc_sc_val=$selectResult[csf('lc_sc_value')];
												echo "L. Dt. Ship: ".change_date_format($selectResult[csf('last_shipment_date')])."<br>";
												
												$sql_amnd="select amendment_no, amendment_date, amendment_value, value_change_by, last_shipment_date from com_export_lc_amendment where export_lc_id='".$selectResult[csf('id')]."' and is_original<>0 and status_active=1 and is_deleted=0";
												$res_amnd=sql_select($sql_amnd );
												foreach($res_amnd as $row_amnd)
												{
													echo "Amnd No: ".$row_amnd[csf('amendment_no')]."<br>";
													echo "Dt: ".change_date_format($row_amnd[csf('amendment_date')])."<br>";
													
													if($row_amnd[csf('last_shipment_date')] >0)
													{
														echo $increase_decrease[$row_amnd[csf('value_change_by')]]." Value#"."<br>";
														echo fn_number_format($row_amnd[csf('amendment_value')],2,'.','')."<br>";
													}
													else echo "Value# "."<br>";
													
													if($row_amnd[csf('last_shipment_date')]!="0000-00-00") $amend_shipment_date=change_date_format($row_amnd[csf('last_shipment_date')]);
													else $amend_shipment_date="";
													
													echo "L. Dt. Ship: ". $amend_shipment_date."<br>";
												}
											}
											else
											{
												echo "<b>SC : ".$selectResult[csf('sc_lc_no')]."</b><br>";
												echo "Dt: ".change_date_format($selectResult[csf('lc_sc_date')])."<br>"; 
												echo "Val: ".fn_number_format($selectResult[csf('lc_sc_value')],2,'.','')."<br>"; $total_lc_sc_val=$selectResult[csf('lc_sc_value')];
												echo "L. Dt. Ship: ".change_date_format($selectResult[csf('last_shipment_date')])."<br>";
												
												$sql_amnd="select amendment_no, amendment_date, amendment_value, value_change_by, last_shipment_date from com_sales_contract_amendment where contract_id='".$selectResult[csf('id')]."' and is_original<>0 and status_active=1 and is_deleted=0";
												$res_amnd=sql_select($sql_amnd );
												foreach($res_amnd as $row_amnd)
												{
													echo "Amnd No: ".$row_amnd[csf('amendment_no')]."<br>";
													echo "Dt: ".change_date_format($row_amnd[csf('amendment_date')])."<br>";
													
													if($row_amnd[csf('last_shipment_date')] >0)
													{
														echo $increase_decrease[$row_amnd[csf('value_change_by')]]." Value#"."<br>";
														echo fn_number_format($row_amnd[csf('amendment_value')],2,'.','')."<br>";
													}
													else echo "Value# "."<br>";
													
													if($row_amnd[csf('last_shipment_date')]!="0000-00-00") $amend_shipment_date=change_date_format($row_amnd[csf('last_shipment_date')]);
													else $amend_shipment_date="";
													
													echo "L. Dt. Ship: ". $amend_shipment_date."<br>";
												}
											}
                                        ?>
                                    </p>
                                </td>
                                <td width="70"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                                <td width="50"><? echo $company_short_details[$row[csf('company_name')]]; ?></td>
                                <td width="60"><p><? echo $row[csf('po_number')]; ?></p></td>
                                <td width="70"><p><? $fabric_desc=explode(",",$fabric_desc); echo implode(",<br>",$fabric_desc); ?></p></td>
                                <td width="30" valign="bottom" align="center">
                                    <div class="r90">
                                        <? 
                                            echo fn_number_format($row[csf('po_qnty_in_pcs')],0,'.',''); 
                                            $tot_po_qnty_pcs+=$row[csf('po_qnty_in_pcs')]; 
                                        ?>
                                    </div>
                                </td>
                                <td width="30" valign="bottom" align="center">
                                    <div class="r90">
                                        <?
                                            echo fn_number_format($attached_qnty_pcs,0,'.',''); 
                                            $tot_order_qnty+=$attached_qnty_pcs; 
                                        ?>
                                    </div>
                                </td>
                                <td width="30" valign="bottom" align="center">
                                    <div class="r90">
                                        <? 
											$po_balance=$row[csf('po_qnty_in_pcs')]-($row[csf('attached_qnty')]*$row[csf('total_set_qnty')]);
                                            echo fn_number_format($po_balance,0,'.',''); 
                                            $tot_po_balance+=$po_balance; 
                                        ?>
                                    </div>
                                </td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($unit_price,2,'.',''); ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($unit_price,2,'.',''); ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($row[csf('attached_rate')],2,'.','');?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($order_val,2,'.',''); $tot_order_val+=$order_val; ?></div></td>
                                <td width="30" valign="bottom" align="center">
                                    <div class="r90">
                                        <? 
                                            //$att_vale=$row[csf('attached_value')];
                                            echo fn_number_format($att_vale,2,'.','');
                                            $tot_lc_value+=$att_vale; 
                                        ?>
                                    </div>
                                </td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($commercial_cost,2,'.',''); $tot_commercial_cost+=$commercial_cost;?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($freight_cost,2,'.',''); $tot_freight_cost+=$freight_cost; ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($commission_cost,2,'.',''); $tot_commission_cost+=$commission_cost;?></div></td>
                                <td width="30" valign="bottom" align="center">
                                    <div class="r90">
                                        <? 
                                            echo fn_number_format($lc_comn_cost,2,'.',''); 
                                            $tot_lc_comn_cost+=$lc_comn_cost; 
                                        ?>
                                    </div>
                                </td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($net_cs_val,2,'.',''); $tot_net_cs_val+=$net_cs_val; ?></div>
                                </td>
                                <td width="30" valign="bottom" align="center">
                                    <div class="r90">
                                        <?
                                            echo fn_number_format($net_lc_val,2,'.','');  
                                            $tot_net_lc_val+=$net_lc_val; 
											$tot_exfactoryqty+=$exfactory_arr[$row[csf("po_id")]]['exfactqty']; 
											$tot_exfactoryvalue+=$exfactory_arr[$row[csf("po_id")]]['exfactqty']*$unit_price;
                                        ?>
                                    </div>
                                </td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo change_date_format($row[csf('shipment_date')]); ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo change_date_format($exfactory_arr[$row[csf("po_id")]]['exfactdate']); ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($exfactory_arr[$row[csf("po_id")]]['exfactqty']); ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($exfactory_arr[$row[csf("po_id")]]['exfactqty']*$unit_price,2); ?></div></td>


                                <td width="30" valign="bottom" align="center" title="<? echo $trims_cost_total; ?>"><div class="r90"><? echo "<a href='#report_details' style='text-decoration:none' onclick= \"openmypage_trims('$job_no','$po_id','$po_no','$order_quantity','trims_info','Trims Info');\">".fn_number_format($attach_trims_cost,2,'.','')."</a>"; $tot_trims_cost+=$attach_trims_cost; ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_trims_cost_perc,2,'.',''); ?></div></td>

                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_print_embro_cost,2,'.',''); $tot_print_embro_cost+=$attach_print_embro_cost; ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_print_embro_cost_percent,2,'.',''); ?></div></td> 
                                
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_wash_cost,2,'.',''); $tot_wash_cost+=$attach_wash_cost; ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_wash_cost_percent,2,'.',''); ?></div></td> 
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_test_cost,2,'.',''); $tot_test_cost+=$attach_test_cost; ?></div></td>
                                <td  width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_test_cost_perc,2,'.',''); ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_inspection_cost,2,'.',''); $tot_inspection_cost+=$attach_inspection_cost; ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_inspection_cost_perc,2,'.',''); ?></div></td>  
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_knit_purc_qnty,2,'.','');$tot_knit_purc_qnty+=$attach_knit_purc_qnty; ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($knit_purc_rate,2,'.','');?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_knit_purc_cost,2,'.','');$tot_knit_purc_cost+=$attach_knit_purc_cost; ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_knit_purc_cost_percent,2,'.',''); ?></div></td>  
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_woven_purc_qnty,2,'.',''); $tot_woven_purc_qnty+=$attach_woven_purc_qnty; ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($woven_purc_rate,2,'.',''); ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_woven_purc_cost,2,'.',''); $tot_woven_purc_cost+=$attach_woven_purc_cost; ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_woven_purc_cost_percent,2,'.',''); ?></div></td>
                                <td width="30" valign="bottom" align="center" title="<? echo $attach_knit_purc_cost; ?>"><div class="r90"><? echo fn_number_format($attach_cm_cost,2,'.',''); $tot_cm_cost+=$attach_cm_cost; ?></div></td>
                                <td width="30" valign="bottom" align="center" title="<? echo $knit_purc_qnty; ?>"><div class="r90"><? echo fn_number_format($attach_cm_cost_perc,2,'.',''); ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_cm_per_dzn,2,'.',''); ?></div></td>
                            </tr>
                        <?
						}
						else
						{
						?>
                            <tr height="90" bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_l<? echo $c; ?>','<? echo $bgcolor; ?>')" id="tr_l<? echo $c;?>">
                                <td width="70"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
                                <td width="50"><? echo $company_short_details[$row[csf('company_name')]]; ?></td>
                                <td width="60"><p><? echo $row[csf('po_number')]; ?></p></td>
                                <td width="70"><p><? $fabric_desc=explode(",",$fabric_desc); echo implode(",<br>",$fabric_desc); ?></p></td>
                                <td width="30" valign="bottom" align="center">
                                    <div class="r90">
                                        <? 
                                            echo fn_number_format($row[csf('po_qnty_in_pcs')],0,'.',''); 
                                            $tot_po_qnty_pcs+=$row[csf('po_qnty_in_pcs')]; 
                                        ?>
                                    </div>
                                </td>
                                <td width="30" valign="bottom" align="center">
                                    <div class="r90">
                                        <? 
                                            echo fn_number_format(($row[csf('attached_qnty')]*$row[csf('total_set_qnty')]),0,'.',''); 
                                            $tot_order_qnty+=($row[csf('attached_qnty')]*$row[csf('total_set_qnty')]); 
                                        ?>
                                    </div>
                                </td>
                                <td width="30" valign="bottom" align="center">
                                    <div class="r90">
                                        <? 
											$po_balance=$row[csf('po_qnty_in_pcs')]-($row[csf('attached_qnty')]*$row[csf('total_set_qnty')]);
                                            echo fn_number_format($po_balance,0,'.',''); 
                                            $tot_po_balance+=$po_balance; 
                                        ?>
                                    </div>
                                </td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($unit_price,2,'.',''); ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($unit_price,2,'.',''); ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($row[csf('attached_rate')],2,'.','');?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($order_val,2,'.',''); $tot_order_val+=$order_val; ?></div></td>
                                <td width="30" valign="bottom" align="center">
                                    <div class="r90">
                                        <? 
                                            //$att_vale=$row[csf('attached_value')];
                                            echo fn_number_format($att_vale,2,'.','');
                                            $tot_lc_value+=$att_vale; 
                                        ?>
                                    </div>
                                </td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($commercial_cost,2,'.',''); $tot_commercial_cost+=$commercial_cost;?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($freight_cost,2,'.',''); $tot_freight_cost+=$freight_cost; ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($commission_cost,2,'.',''); $tot_commission_cost+=$commission_cost;?></div></td>
                                <td width="30" valign="bottom" align="center">
                                    <div class="r90">
                                        <? 
                                            echo fn_number_format($lc_comn_cost,2,'.',''); 
                                            $tot_lc_comn_cost+=$lc_comn_cost; 
                                        ?>
                                    </div>
                                </td>
                                <td width="30" valign="bottom" align="center">
                                	<div class="r90"><? echo fn_number_format($net_cs_val,2,'.',''); $tot_net_cs_val+=$net_cs_val; ?></div>
                                </td>
                                <td width="30" valign="bottom" align="center">
                                    <div class="r90">
                                        <?
                                            echo fn_number_format($net_lc_val,2,'.','');  
                                            $tot_net_lc_val+=$net_lc_val; 
											$tot_exfactoryqty+=$exfactory_arr[$row[csf("po_id")]]['exfactqty']; 
											$tot_exfactoryvalue+=$exfactory_arr[$row[csf("po_id")]]['exfactqty']*$unit_price;
                                        ?>
                                    </div>
                                </td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo change_date_format($row[csf('shipment_date')]); ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo change_date_format($exfactory_arr[$row[csf("po_id")]]['exfactdate']); ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($exfactory_arr[$row[csf("po_id")]]['exfactqty']); ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($exfactory_arr[$row[csf("po_id")]]['exfactqty']*$unit_price,2); ?></div></td>

                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo "<a href='#report_details' style='text-decoration:none' onclick= \"openmypage_trims('$job_no','$po_id','$po_no','$order_quantity','trims_info','Trims Info');\">".fn_number_format($attach_trims_cost,2,'.','')."</a>"; $tot_trims_cost+=$attach_trims_cost; ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_trims_cost_perc,2,'.',''); ?></div></td>

                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_print_embro_cost,2,'.',''); $tot_print_embro_cost+=$attach_print_embro_cost; ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_print_embro_cost_percent,2,'.',''); ?></div></td> 
                                
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_wash_cost,2,'.',''); $tot_wash_cost+=$attach_wash_cost; ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_wash_cost_percent,2,'.',''); ?></div></td> 
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_test_cost,2,'.',''); $tot_test_cost+=$attach_test_cost; ?></div></td>
                                <td  width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_test_cost_perc,2,'.',''); ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_inspection_cost,2,'.',''); $tot_inspection_cost+=$attach_inspection_cost; ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_inspection_cost_perc,2,'.',''); ?></div></td>  
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_knit_purc_qnty,2,'.','');$tot_knit_purc_qnty+=$attach_knit_purc_qnty; ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($knit_purc_rate,2,'.','');?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_knit_purc_cost,2,'.','');$tot_knit_purc_cost+=$attach_knit_purc_cost; ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_knit_purc_cost_percent,2,'.',''); ?></div></td>  
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_woven_purc_qnty,2,'.',''); $tot_woven_purc_qnty+=$attach_woven_purc_qnty; ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($woven_purc_rate,2,'.',''); ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_woven_purc_cost,2,'.',''); $tot_woven_purc_cost+=$attach_woven_purc_cost; ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_woven_purc_cost_percent,2,'.',''); ?></div></td>
                                <td width="30" valign="bottom" align="center" title="<? echo $attach_knit_purc_cost; ?>"><div class="r90"><? echo fn_number_format($attach_cm_cost,2,'.',''); $tot_cm_cost+=$attach_cm_cost; ?></div></td>
                                <td width="30" valign="bottom" align="center" title="<? echo $knit_purc_qnty; ?>"><div class="r90"><? echo fn_number_format($attach_cm_cost_perc,2,'.',''); ?></div></td>
                                <td width="30" valign="bottom" align="center"><div class="r90"><? echo fn_number_format($attach_cm_per_dzn,2,'.',''); ?></div></td>
                            </tr>
                        	<?
						}
						$c++;
						$j++;
					}
					
					if($tot_rows>0)
					{
						?>
                        <tr bgcolor="#CCCCCC" height="90">
                            <td align="right"><b>T</b></td>
                            <td align="right"><? echo fn_number_format($total_lc_sc_val,2,'.',''); $gr_total_lc_sc_val+=$total_lc_sc_val; ?></td>
                            <td align="right">&nbsp;</td>
                            <td align="right">&nbsp;</td>
                            <td align="right">&nbsp;</td>
                            <td align="right">&nbsp;</td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_po_qnty_pcs,0,'.',''); $gr_tot_po_qnty_pcs+=$tot_po_qnty_pcs; ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_order_qnty,0,'.',''); $gr_tot_order_qnty+=$tot_order_qnty; ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_po_balance,0,'.',''); $gr_tot_po_balance+=$tot_po_balance; ?></div></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td> 
                            <td>&nbsp;</td>
                            
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_order_val,2,'.',''); $gr_tot_order_val+=$tot_order_val; ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_lc_value,2,'.',''); $gr_tot_lc_value+=$tot_lc_value; ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_commercial_cost,2,'.',''); if($tot_commercial_cost>0)$gr_tot_commercial_cost+=$tot_commercial_cost; ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_freight_cost,2,'.',''); if($tot_freight_cost>0) $gr_tot_freight_cost+=$tot_freight_cost; ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_commission_cost,2,'.',''); if($tot_commission_cost>0) $gr_tot_commission_cost+=$tot_commission_cost; ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_lc_comn_cost,2,'.',''); $gr_tot_lc_comn_cost+=$tot_lc_comn_cost; ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_net_cs_val,2,'.',''); $gr_tot_net_cs_val+=$tot_net_cs_val; ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_net_lc_val,2,'.',''); $gr_tot_net_lc_val+=$tot_net_lc_val; ?></div></td>
                            <td>&nbsp;</td>
							<td>&nbsp;</td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_exfactoryqty,0,'.',''); $gr_tot_exfactoryqty+=$tot_exfactoryqty; ?></div></td> 
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_exfactoryvalue,2,'.',''); $gr_tot_exfactoryvalue+=$tot_exfactoryvalue; ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_trims_cost,2,'.',''); if($tot_trims_cost>0)$gr_tot_trims_cost+=$tot_trims_cost; ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format(($tot_trims_cost/$tot_order_val)*100,2,'.',''); ?></div></td>

                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_print_embro_cost,2,'.',''); if($tot_print_embro_cost>0) $gr_tot_print_embro_cost+=$tot_print_embro_cost;?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format(($tot_print_embro_cost/$tot_order_val)*100,2,'.',''); ?></div></td>
                          
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_wash_cost,2,'.',''); if($tot_wash_cost>0) $gr_tot_wash_cost+=$tot_wash_cost; ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format(($tot_wash_cost/$tot_order_val)*100,2,'.',''); ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_test_cost,2,'.',''); if($tot_test_cost>0) $gr_tot_test_cost+=$tot_test_cost; ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format(($tot_test_cost/$tot_order_val)*100,2,'.',''); ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_inspection_cost,2,'.',''); if($tot_inspection_cost>0) $gr_tot_inspection_cost+=$tot_inspection_cost; ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format(($tot_inspection_cost/$tot_order_val)*100,2,'.',''); ?></div></td>
                            
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_knit_purc_qnty,2,'.',''); $gr_tot_knit_purc_qnty+=$tot_knit_purc_qnty; ?></div></td>
                            <td valign="bottom" align="center"><div class="r90">&nbsp;</div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_knit_purc_cost,2,'.',''); if($tot_knit_purc_cost>0) $gr_tot_knit_purc_cost+=$tot_knit_purc_cost; ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format(($tot_knit_purc_cost/$tot_order_val)*100,2,'.',''); ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_woven_purc_qnty,2,'.',''); $gr_tot_woven_purc_qnty+=$tot_woven_purc_qnty; ?></div></td>
                            <td valign="bottom" align="center"><div class="r90">&nbsp;</div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_woven_purc_cost,2,'.',''); $gr_tot_woven_purc_cost+=$tot_woven_purc_cost; ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format(($tot_woven_purc_cost/$tot_order_val)*100,2,'.',''); ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format($tot_cm_cost,2,'.',''); $gr_tot_cm_cost+=$tot_cm_cost; ?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format(($tot_cm_cost/$tot_order_val)*100,2,'.','');?></div></td>
                            <td valign="bottom" align="center"><div class="r90"><? echo fn_number_format(($tot_cm_cost/$tot_order_qnty)*12,2,'.','');?></div></td>
                        </tr>
                        <?
					}
				$i++;
				}
				?>
                <tr bgcolor="#E9F3FF" height="90">
                    <td align="right"><b>GT</b></td>
                    <td align="right"><b><? echo fn_number_format($gr_total_lc_sc_val,2,'.',''); ?></b></td>
                    <td align="right">&nbsp;</td>
                    <td align="right">&nbsp;</td>
                    <td align="right">&nbsp;</td>
                    <td align="right">&nbsp;</td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_po_qnty_pcs,0,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_order_qnty,0,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_po_balance,0,'.',''); ?></div></b></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_order_val,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_lc_value,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_commercial_cost,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_freight_cost,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_commission_cost,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_lc_comn_cost,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_net_cs_val,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_net_lc_val,2,'.',''); ?></div></b></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_exfactoryqty,0,'.',''); ?></div></b></td> 					
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_exfactoryvalue,2,'.',''); ?></div></b></td>

                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_trims_cost,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format(($gr_tot_trims_cost/$gr_tot_order_val)*100,2,'.',''); ?></div></b></td>

                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_print_embro_cost,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format(($gr_tot_print_embro_cost/$gr_tot_order_val)*100,2,'.',''); ?></div></b></td>
                    
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_wash_cost,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format(($gr_tot_wash_cost/$gr_tot_order_val)*100,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_test_cost,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format(($gr_tot_test_cost/$gr_tot_order_val)*100,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_inspection_cost,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format(($gr_tot_inspection_cost/$gr_tot_order_val)*100,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_knit_purc_qnty,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90">&nbsp;</div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_knit_purc_cost,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format(($gr_tot_knit_purc_cost/$gr_tot_order_val)*100,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_woven_purc_qnty,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90">&nbsp;</div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_woven_purc_cost,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format(($gr_tot_woven_purc_cost/$gr_tot_order_val)*100,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format($gr_tot_cm_cost,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format(($gr_tot_cm_cost/$gr_tot_order_val)*100,2,'.',''); ?></div></b></td>
                    <td valign="bottom" align="center"><b><div class="r90"><? echo fn_number_format(($gr_tot_cm_cost/$gr_tot_order_qnty)*12,2,'.',''); ?></div></b></td>
               </tr>
               </tbody>
            </table>
        </div>
        <br />
        <table width="1480">
        	<tr>
            	<td colspan="3" style="font-size:16px; font-weight:bold;">File Value : <? echo fn_number_format($file_value,2);?></td>
            </tr>
            <tr>
            	<td colspan="3">&nbsp;</td>
            </tr>
        	<tr>
            	<td width="610" valign="top">
                    <u><b>CM [Cost of Manufature] calculated between L/C value and all other costs:</b></u>
                    <table width="100%" class="rpt_table" cellpadding="0" cellspacing="0" rules="all" border="1"> 
                        <thead>
                            <th width="170">Cost Item</th>
                            <th width="120">Cost Amount</th>
                            <th width="120">Consumption/Dzn Gmts</th>
                            <th width="120">Cost</th>
                            <th width="80">UOM</th>
                        </thead>
                        <?
                            $tot_cost_heads=$gr_tot_yarn_cost+$gr_tot_trims_cost+$gr_tot_knitt_cost+$gr_tot_dye_cost+$gr_tot_print_embro_cost+$gr_tot_wash_cost+$gr_tot_test_cost+$gr_tot_commercial_cost+$gr_tot_commission_cost+$gr_tot_freight_cost+$gr_tot_inspection_cost+$gr_tot_finish_cost+$gr_tot_knit_purc_cost+$gr_tot_woven_purc_cost;
                        ?>
                        <tr bgcolor="#FFFFFF">
                            <td>Accessories</td>
                            <td align="right"><? echo fn_number_format($gr_tot_trims_cost,2); ?></td>
                            <td align="right">&nbsp;</td>
                            <td align="right"><? echo fn_number_format(($gr_tot_trims_cost/$gr_tot_order_qnty)*12,4); ?></td>
                            <td align="center">Dzn</td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                            <td>Printing & Embroidery</td>
                            <td align="right"><? echo fn_number_format($gr_tot_print_embro_cost,2); ?></td>
                            <td align="right">&nbsp;</td>
                            <td align="right"><? echo fn_number_format(($gr_tot_print_embro_cost/$gr_tot_order_qnty)*12,4); ?></td>
                            <td align="center">Dzn</td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                            <td>Washing</td>
                            <td align="right"><? echo fn_number_format($gr_tot_wash_cost,2); ?></td>
                            <td align="right">&nbsp;</td>
                            <td align="right"><? echo fn_number_format(($gr_tot_wash_cost/$gr_tot_order_qnty)*12,4); ?></td>
                            <td align="center">Dzn</td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                            <td>Testing</td>
                            <td align="right"><? echo fn_number_format($gr_tot_test_cost,2); ?></td>
                            <td align="right">&nbsp;</td>
                            <td align="right"><? echo fn_number_format(($gr_tot_test_cost/$gr_tot_order_qnty)*12,4); ?></td>
                            <td align="center">Dzn</td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                            <td>Commercial</td>
                            <td align="right"><? echo fn_number_format($gr_tot_commercial_cost,2); ?></td>
                            <td align="right">&nbsp;</td>
                            <td align="right"><? echo fn_number_format(($gr_tot_commercial_cost/$gr_tot_order_qnty)*12,4); ?></td>
                            <td align="center">Dzn</td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                            <td>Commisssion</td>
                            <td align="right"><? echo fn_number_format($gr_tot_commission_cost,2); ?></td>
                            <td align="right">&nbsp;</td>
                            <td align="right"><? echo fn_number_format(($gr_tot_commission_cost/$gr_tot_order_val)*100,4);?></td>
                            <td align="center">%</td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                            <td>Freight</td>
                            <td align="right"><? echo fn_number_format($gr_tot_freight_cost,2); ?></td>
                            <td align="right">&nbsp;</td>
                            <td align="right"><? echo fn_number_format(($gr_tot_freight_cost/$gr_tot_order_qnty)*12,4); ?></td>
                            <td align="center">Dzn</td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                            <td>Inspection</td>
                            <td align="right"><? echo fn_number_format($gr_tot_inspection_cost,2); ?></td>
                            <td align="right">&nbsp;</td>
                            <td align="right"><? echo fn_number_format(($gr_tot_inspection_cost/$gr_tot_order_qnty)*12,4); ?></td>
                            <td align="center">Dzn</td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                            <td>Knit Fabric Purchase</td>
                            <td align="right"><? echo fn_number_format($gr_tot_knit_purc_cost,2); ?></td>
                            <td align="right">&nbsp;</td>
                            <td align="right"><? echo fn_number_format(($gr_tot_knit_purc_cost/$gr_tot_order_qnty)*12,4); ?></td>
                            <td align="center">KG</td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                            <td>Woven Fabric Purchase</td>
                            <td align="right"><? echo fn_number_format($gr_tot_woven_purc_cost,2); ?></td>
                            <td align="right">&nbsp;</td>
                            <td align="right"><? echo fn_number_format(($gr_tot_woven_purc_cost/$gr_tot_order_qnty)*12,4); ?></td>
                            <td align="center">Yds</td>
                        </tr>
                        <tr height="15"><td colspan="4">&nbsp;</td></tr>
                        <tr bgcolor="#E9F3FF">
                            <td align="right"><b>Total</b></td>
                            <td align="right" title="<? echo $tot_cost_heads."=yarn".$gr_tot_yarn_cost."=trims".$gr_tot_trims_cost."=knit".$gr_tot_knitt_cost."=dyeing".$gr_tot_dye_cost."=embro".$gr_tot_print_embro_cost."=wash".$gr_tot_wash_cost."=test".$gr_tot_test_cost."=commercial".$gr_tot_commercial_cost."=commission".$gr_tot_commission_cost."=freight".$gr_tot_freight_cost."=inspection".$gr_tot_inspection_cost."=finish".$gr_tot_finish_cost."=knit purchase".$gr_tot_knit_purc_cost."=weven purchase".$gr_tot_woven_purc_cost; ?>"><b><? echo fn_number_format($tot_cost_heads,2); ?></b></td>
                            <td align="right"><b><? echo fn_number_format($cost_heads_percentage=($tot_cost_heads/$gr_tot_order_val)*100,2)."%"; ?></b></td> 
                            <td align="right">&nbsp;</td>
                            <td align="center">&nbsp;</td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
    
                            <td align="right"><b>CM Percentage</b></td>
                            <td align="right" title="<? echo $test_cm_cos; ?>"><b><? echo fn_number_format($gr_tot_cm_cost,2); ?></b></td>
                            <td align="right"><b><? echo fn_number_format($cm_percentage=($gr_tot_cm_cost/$gr_tot_order_val)*100,2)."%"; ?></b></td> 
                            <td align="right">&nbsp;</td>
                            <td align="center">&nbsp;</td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                            <td align="right"><b>Grand Total</b></td>
                            <td align="right"><b><? echo fn_number_format($tot_cost_heads+$gr_tot_cm_cost,2); ?></b></td>
                            <td align="right"><b><? echo fn_number_format($cost_heads_percentage+$cm_percentage,2)."%"; ?></b></td> 
                            <td align="right">&nbsp;</td>
                            <td align="center">&nbsp;</td>
                        </tr>
                    </table>
                </td>
                <td valign="bottom" width="400" style="font-size:14px; padding-left:30px" align="left">
                    <b>Total Garments Qnty (Pcs):&nbsp;&nbsp;<? echo fn_number_format($tot_gmts_qnty,2); ?></b><br />
                    <b>Avg Unit Price (Pcs):&nbsp;&nbsp;<? echo fn_number_format($gr_tot_order_val/$gr_tot_order_qnty,2); ?></b><br />
                    <b>Avg CM Per Dzn:&nbsp;&nbsp;<? echo fn_number_format(($gr_tot_cm_cost/$gr_tot_order_qnty)*12,2); ?></b><br />
                    
                    <b title="<?='Tot Min='.$tot_minutes; ?>">Avg SMV:&nbsp;&nbsp;<? echo fn_number_format(($tot_minutes/$gr_tot_order_qnty),2); ?></b><br />
                    <b title="<?='Tot Att. Cost='.$tot_attach_cost; ?>">Avg CPM:&nbsp;&nbsp;<? echo fn_number_format(($tot_attach_cost/$tot_minutes),2); ?></b><br />
                    <b title="<?='Tot Att. CM='.$gr_tot_cm_cost; ?>">Avg EPM:&nbsp;&nbsp;<? echo fn_number_format(($gr_tot_cm_cost/$tot_minutes),2); ?></b>
                </td>
                <td width="470" valign="top">
                    <u><b>Material Cost for BBLC only:</b></u>
                    <table class="rpt_table" cellpadding="0" cellspacing="0" rules="all" border="1"> 
                        <thead>
                            <th width="170">Cost Item</th>
                            <th width="130">Cost Amount</th>
                            <th width="130">% On Net Value</th>
                        </thead>
                        <tr bgcolor="#E9F3FF">
                            <td>Accessories</td>
                            <td align="right"><? echo fn_number_format($gr_tot_trims_cost,2); $tot_charge+=$gr_tot_trims_cost; ?></td>
                            <td align="right"><? echo fn_number_format(($gr_tot_trims_cost/$gr_tot_net_cs_val)*100,4); $tot_charge_perc+=($gr_tot_trims_cost/$gr_tot_net_cs_val)*100; ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                            <td>Printing & Embroidery</td>
                            <td align="right"><? echo fn_number_format($print_embro_charge,2); $tot_charge+=$print_embro_charge; ?></td>
                            <td align="right"><? $print_embro_charge_perc=($print_embro_charge/$gr_tot_net_cs_val)*100; echo fn_number_format($print_embro_charge_perc,4); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                            <td>Washing</td>
                            <td align="right"><? echo fn_number_format($wash_charge,2); $tot_charge+=$wash_charge; ?></td>
                            <td align="right"><? $wash_charge_perc=($wash_charge/$gr_tot_net_cs_val)*100; echo fn_number_format($wash_charge_perc,4); ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                            <td>Knit Fabric Purchase</td>
                            <td align="right"><? echo fn_number_format($tot_knit_purc_cost,2); $tot_charge+=$tot_knit_purc_cost;//fn_number_format($cost_heads_knit_purchase,2); $tot_charge+=$cost_heads_knit_purchase; ?></td>
                            <td align="right"><? $knit_purchase_perc=($cost_heads_knit_purchase/$gr_tot_net_cs_val)*100; echo fn_number_format($knit_purchase_perc,4); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                            <td>Woven Fabric Purchase</td>
                            <td align="right"><? echo fn_number_format($cost_heads_woven_purc,2); $tot_charge+=$cost_heads_woven_purc; ?></td>
                            <td align="right"><? $woven_purchase_perc=($cost_heads_woven_purc/$gr_tot_net_cs_val)*100; echo fn_number_format($woven_purchase_perc,4); ?></td>
                        </tr>
                        <tr height="15"><td colspan="3">&nbsp;</td></tr>
                        <tr bgcolor="#CCCCCC">
                            <td align="right"><b>Total</b></td>
                            <td align="right"><b><? echo fn_number_format($tot_charge,2); ?></b></td>
                            <td align="right"><b><? $tot_charge_perc=($tot_charge/$gr_tot_net_cs_val)*100; echo fn_number_format($tot_charge_perc,4); ?></b></td>
                        </tr>
                    </table>
                </td>
        	</tr>
            <tr>
            	<td height="30" colspan="3">&nbsp;</td>
            </tr>
            <tr>
            	<td>
                <table border="1" rules="all">
                	<tr align="center">
                    	<td width="200"><b>Recommendation For:</b></td>
                        <td width="200"><b>BB Limit</b></td>
                        <td width="200"><b><? echo fn_number_format($tot_charge_perc,2)."%"; ?></b></td>
                    </tr>
                </table>
                </td>
                <td>
                <table border="1" rules="all">
                	<tr align="center">
                    	<td width="200"><b>Approved</b></td>
                        <td width="200"><b>BB Limit</b></td>
                        <td width="100">&nbsp;</td>
                    </tr>
                </table>
                </td>
                <td>&nbsp;</td>
            </tr>
        </table>
        <?
		$signeture_allow_or_no = return_field_value("report_list","variable_settings_report","module_id=7 and company_id=$company_name");
		$signeture_list = explode('#',$signeture_allow_or_no);
		$td_width=1400/count($signeture_list);
		if($signeture_allow_or_no!= "")	
		{
		 echo '<table width="1400">
			<tr>
				<td width="100%" height="90" colspan="'.count($signeture_list).'"> </td>
			</tr> 
			<tr style="font-size:14px">';
			foreach($signeture_list as $key=>$value)
			{
				echo '<td width="'.$td_width.'" align="center"><strong style="text-decoration:overline">'.$value.'</strong><br /><strong>'.$group_details[$group_id_details[$company_name]].'</strong></td>';
			}
			echo '</tr></table>';
		}
    	echo signature_table(97, $company_name, "1330px");
    ?>
	</div>
    </fieldset>
    <? endif; ?>
    <script>
    show_agent('<? echo "<b>,&nbsp;&nbsp;&nbsp;Agent Name: ".$agent_name."</b>"; ?>');
    </script>
    <?
    exit();
}

if($action=="trims_info")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
?>
<fieldset style="width:620px; margin-left:10px">
    <div id="report_container" align="center" style="width:100%">
        <div style="width:610px">
            <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="400" align="center">
                <thead>
                    <th width="100">Job No</th>
                    <th>Order No</th>
                    <th width="120">Order Qnty</th>
                </thead>
                <tr bgcolor="#EFEFEF">
                    <td align="center"><? echo $job_no; ?></td>
                    <td><? echo $order_no; ?></td>
                    <td align="right"><? echo fn_number_format($order_qnty,0); ?></td>
                </tr>
            </table>
            <br />
            <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
                <thead>
                    <th width="40">SL</th>
                    <th width="140">Item Name</th>
                    <th width="70">UOM</th>
                    <th width="110">Cons/Dzn</th>
                    <th width="90">Rate</th>
                    <th>Amount</th>
                </thead>
            </table>
        </div>
        <div style="width:610px; overflow-y:scroll; max-height:260px;" id="scroll_body" align="left" >
            <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
            <? 
                $i=1;
               $sql="select a.trim_group, a.cons_uom, a.rate, b.cons from wo_pre_cost_trim_cost_dtls a, wo_pre_cost_trim_co_cons_dtls b where a.id=b.wo_pre_cost_trim_cost_dtls_id and b.po_break_down_id='$po_id' and a.job_no='$job_no' and a.status_active=1 and a.is_deleted=0";
                $result=sql_select($sql);
                $total_trims_cost = 0;
                foreach($result as $row) 
                {
                    if ($i%2==0)  
                        $bgcolor="#E9F3FF";
                    else
                        $bgcolor="#FFFFFF";
                
                    $costing_per_id=return_field_value("costing_per_id","wo_pre_cost_dtls","job_no='$job_no'");
                    
                    if($costing_per_id==1) $dzn_qnty=12;
                    else if($costing_per_id==3) $dzn_qnty=12*2;
                    else if($costing_per_id==4) $dzn_qnty=12*3;
                    else if($costing_per_id==5) $dzn_qnty=12*4;
                    else $dzn_qnty=1;

                    $trims_cost=($order_qnty/$dzn_qnty)*$row[csf('cons')]*$row[csf('rate')];
                    $total_trims_cost+= $trims_cost;
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="40" align="center"><? echo $i; ?></td>
                        <td width="140"><p><? echo $item_library[$row[csf('trim_group')]]; ?></p></td>
                        <td width="70" align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></td>
                        <td width="110" align="right"><? echo fn_number_format($row[csf('cons')],2); ?></td>
                        <td width="90" align="right"><? echo fn_number_format($row[csf('rate')],4); ?></td>
                        <td align="right"><? echo fn_number_format($trims_cost,2); ?></td>
                    </tr>
                    <?
                $i++;
                }
                ?>
                 <tfoot>
                    <th colspan="5" align="right">Total</th>
                    <th align="right"><? echo fn_number_format($total_trims_cost,2); ?></th>
                </tfoot>
            </table>
        </div>
    </div>
</fieldset>
<?
exit();
}
disconnect($con);
?>
