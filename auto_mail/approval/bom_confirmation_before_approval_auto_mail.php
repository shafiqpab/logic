<?
/*--- ----------------------------------------- Comments
Purpose			: 	This form will create Operation Bulletin Entry				
Functionality	:	
JS Functions	:
Created by		:	Al-Hasan
Creation date 	: 	03-12-2023
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:
Comments		:

Note: Right site show_operation_list_view loading off when update and save;

*/

date_default_timezone_set("Asia/Dhaka");
require_once('../../includes/common.php');

include('../../includes/class4/class.conditions.php');
include('../../includes/class4/class.reports.php');
include('../../includes/class4/class.fabrics.php');
include('../../includes/class4/class.yarns.php');
include('../../includes/class4/class.conversions.php');
include('../../includes/class4/class.trims.php');
include('../../includes/class4/class.emblishments.php');
include('../../includes/class4/class.washes.php');
include('../../includes/class4/class.others.php');
include('../../includes/class4/class.commercials.php');
include('../../includes/class4/class.commisions.php');

extract($_REQUEST);

$company_library = return_library_array("select id, company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
$dealing_merchant_arr = return_library_array("select id, team_member_name from lib_mkt_team_member_info where status_active=1 and is_deleted=0", "id", "team_member_name");
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name"  );
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$user_arr=return_library_array( "select id, user_name from user_passwd", "id", "user_name"  );

$current_time = ($_REQUEST['view_date'])?strtotime($_REQUEST['view_date']):time();
$current_date = date("d-M-Y", $current_time);
$prev_date = change_date_format(date('Y-m-d H:i:s', strtotime('-1 day', strtotime($current_date))), '', '', 1);
 
//$prev_date = date('m/d/Y ', strtotime('-1 day', strtotime($current_date)));

//echo $prev_date;die;
 
$year_cond="to_char(a.insert_date,'YYYY') as year";
$year_cond_groupby="to_char(a.insert_date,'YYYY')";


$date_cond=" and b.costing_date <= '$prev_date'";
            
$sql= "SELECT b.id,b.sew_smv,a.set_smv,b.sew_effi_percent,b.exchange_rate, a.company_name, a.quotation_id,a.avg_unit_price, a.job_no_prefix_num, to_char(a.insert_date,'YYYY') as year, a.id as job_id, a.job_no, a.buyer_name, a.style_ref_no, b.costing_date, '0' as approval_id, b.approved, b.inserted_by, b.entry_from, min(d.shipment_date) as minship_date, max(d.shipment_date) as maxship_date, a.job_quantity, (a.job_quantity*a.total_set_qnty) as job_qty_pcs, (a.job_quantity * a.avg_unit_price) AS total_value, a.total_price, rtrim(xmlagg(xmlelement(e,d.grouping,',').extract('//text()') order by d.grouping).GetClobVal(),',') as internalRef, rtrim(xmlagg(xmlelement(e,d.file_no,',').extract('//text()') order by d.file_no).GetClobVal(),',') as fileNo FROM wo_pre_cost_mst b, wo_po_details_master a, wo_po_break_down d where a.job_no=b.job_no and a.job_no=d.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.ready_to_approved=1 and b.approved in(0,2,3) and b.confirm_approval=0  $date_cond group by b.id,b.exchange_rate, a.company_name, a.quotation_id,a.set_smv,b.sew_smv,b.sew_effi_percent,a.avg_unit_price, a.job_no_prefix_num, to_char(a.insert_date,'YYYY'), a.id, a.job_no, a.buyer_name, a.style_ref_no, b.costing_date, '0', b.approved, b.inserted_by, b.entry_from, a.job_quantity, a.total_set_qnty, a.total_price";
 
$nameArray = sql_select( $sql );
$dataArr2=array();
foreach ($nameArray as $row)
{ 
    $dataArr2[$row[csf('company_name')]]= $row;
}
//echo "<pre>";
//print_r($dataArr);die;

ob_start();
foreach ($company_library as $company_id => $company_name)
{ 
    if($dataArr2[$company_id]){
?>
        <table width="1300"  cellspacing="0" border="0">
            <tr>
                <td colspan="18" align="center">
                    <strong>Company: <?= $company_name; ?></strong>
                </td>
            </tr>
            <tr>
                <td colspan="18" align="center">
                    <b style="font-size:14px;">Order Entry Date :<?= date("d-m-Y",strtotime($prev_date));  ?></b>
                </td>
            </tr>
        </table>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1520" class="rpt_table" >
            <thead> 
                <th width="30">SL</th>
                <th width="120">Company</th>
                <th width="50">Job No</th>
                <th width="100">Buyer</th>
                <th width="50">Year</th>
                <th width="120">Style Ref.</th>
                <th width="100">Costing Date</th>
                <th width="100">Ship Start</th>
                <th width="100">Ship End</th>
                <th width="70">Job Qty[PCS]</th>
                <th width="70">Avg. Rate</th>
                <th width="80">Total Value</th>
                <th width="70">SMV</th>
                <th width="70">Sewing Efficiency</th>
                <th width="70">CM</th>
                <th width="70">EPM</th>
                <th width="70">CPM</th>
                <th width="70">CM %</th>
                <th width="70">Margin [PCS] %</th>
                <th width="120">Approved Date</th>
                <th width="120">Insert By</th>
            </thead>

            <tbody>
            <? 
            if(str_replace("'","",$cbo_buyer_name)==0)
            {
                if ($_SESSION['logic_erp']["data_level_secured"]==1)
                {
                    if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
                }
                else $buyer_id_cond="";
            }
            else $buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
            
            $job_no=str_replace("'","",$txt_job_no);
            $file_no=str_replace("'","",$txt_file_no);
            $internal_ref=str_replace("'","",$txt_internal_ref);
            $job_year=str_replace("'","",$cbo_year);
        
            if ($file_no=="") $file_no_cond=""; else $file_no_cond=" and d.file_no='".trim($file_no)."' ";
            if ($internal_ref=="") $internal_ref_cond=""; else $internal_ref_cond=" and d.grouping='".trim($internal_ref)."' ";
            if ($job_no=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num='".trim($job_no)."' ";
            if ($job_year=="" || $job_year==0) $job_year_cond="";
            else
            {
                if($db_type==2) $job_year_cond=" and to_char(a.insert_date,'YYYY')='".trim($job_year)."' ";
                else $job_year_cond=" and YEAR(a.insert_date)='".trim($job_year)."' ";
            } 
        
            $approval_type=str_replace("'","",$cbo_approval_type);
            if($previous_approved==1 && $approval_type==1) $previous_approved_type=1;
            $date = date("Y");

            $job_year_cond="and to_char(a.insert_date,'YYYY')='$date'";

            $approval_type = 2; 
            $previous_approved_type ='';

            $sqlVariableCheck=sql_select("select id, is_required, cm_std_per, cm_std_value, margin_std_per, margin_std_value from variable_approval_settings where company_name=$company_id and is_required=1 and variable_list=1 and status_active=1 and is_deleted=0");
                
            $isReq=2; $cm_std_per=$cm_std_value=$margin_std_per=$margin_std_value=0;
            foreach($sqlVariableCheck as $vrow)
            {
                $isReq=$vrow[csf('is_required')];
                $cm_std_per=number_format($vrow[csf('cm_std_per')]*1,6);
                $cm_std_value=number_format($vrow[csf('cm_std_value')]*1,6);
                $margin_std_per=number_format($vrow[csf('margin_std_per')]*1,6);
                $margin_std_value=number_format($vrow[csf('margin_std_value')]*1,6);
            } 

            if($isReq==1)
            {
                if($cm_std_per==0 && $cm_std_value==0 && $margin_std_per==0 && $margin_std_value==0)
                {
                    echo "<font style='color:#F00; font-size:14px; font-weight:bold'>No Value Set In Library For Confirmation Before Approval.</font>";
                    die;
                }
            }
            
            if($db_type==2)
            {
                $internalRefCond="rtrim(xmlagg(xmlelement(e,d.grouping,',').extract('//text()') order by d.grouping).GetClobVal(),',')"; 
                $fileNoCond="rtrim(xmlagg(xmlelement(e,d.file_no,',').extract('//text()') order by d.file_no).GetClobVal(),',')"; 
            }
            else 
            {
                $internalRefCond="rtrim(xmlagg(xmlelement(e,d.grouping,',').extract('//text()') order by d.grouping).GetClobVal(),',')"; 
                $fileNoCond="rtrim(xmlagg(xmlelement(e,d.file_no,',').extract('//text()') order by d.file_no).GetClobVal(),',')"; 
            } 

            $date_cond=" and b.costing_date <= '$prev_date'";
            
            $sql= "SELECT b.id,b.sew_smv,a.set_smv,b.sew_effi_percent,b.exchange_rate, a.quotation_id,a.avg_unit_price, a.job_no_prefix_num, to_char(a.insert_date,'YYYY') as year, a.id as job_id, a.job_no, a.buyer_name, a.style_ref_no, b.costing_date, '0' as approval_id, b.approved, b.inserted_by, b.entry_from, min(d.shipment_date) as minship_date, max(d.shipment_date) as maxship_date, a.job_quantity, (a.job_quantity*a.total_set_qnty) as job_qty_pcs, (a.job_quantity * a.avg_unit_price) AS total_value, a.total_price, rtrim(xmlagg(xmlelement(e,d.grouping,',').extract('//text()') order by d.grouping).GetClobVal(),',') as internalRef, rtrim(xmlagg(xmlelement(e,d.file_no,',').extract('//text()') order by d.file_no).GetClobVal(),',') as fileNo FROM wo_pre_cost_mst b, wo_po_details_master a, wo_po_break_down d where a.job_no=b.job_no and a.company_name=$company_id and a.job_no=d.job_no_mst and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and b.ready_to_approved=1 and b.approved in(0,2,3) and b.confirm_approval=0  $date_cond group by b.id,b.exchange_rate, a.quotation_id,a.set_smv,b.sew_smv,b.sew_effi_percent,a.avg_unit_price, a.job_no_prefix_num, to_char(a.insert_date,'YYYY'), a.id, a.job_no, a.buyer_name, a.style_ref_no, b.costing_date, '0', b.approved, b.inserted_by, b.entry_from, a.job_quantity, a.total_set_qnty, a.total_price";
                
            $nameArray=sql_select( $sql );

            $jobFobValue_arr=array(); $jobIds=""; $jobArr=array();
            foreach ($nameArray as $row)
            {
                $jobFobValue_arr[$row[csf('job_no')]]=$row[csf('total_price')];
                if($jobIds=='') $jobIds=$row[csf('job_id')]; else $jobIds.=','.$row[csf('job_id')];
                $jobArr[$row[csf('job_no')]]['jobqty']=$row[csf('job_qty_pcs')];
                $job_qnty_Arr[$row[csf('job_no')]]['job_quantity']+=$row[csf('job_quantity')];
                $job_aver_Arr[$row[csf('job_no')]]['avg_unit_price']=$row[csf('avg_unit_price')];
                $job_value_Arr[$row[csf('job_no')]]['total_value']+=$row[csf('total_value')];
                $order_values =$job_value_Arr[$row[csf('job_no')]]['total_value'];
                $poQty=$job_qnty_Arr[$row[csf('job_no')]]['job_quantity'];
                $pre_costing_date=change_date_format($row[csf('costing_date')],'','',1);
                $sew_effi_percent=$row[csf("sew_effi_percent")];
                $sew_effi_Arr[$row[csf('job_no')]]['sew_effi_percent']=$row[csf('sew_effi_percent')];
                $exchange_rate_Arr[$row[csf('job_no')]]['exchange_rate']=$row[csf('exchange_rate')];
                $sew_value_Arr[$row[csf('job_no')]]['sew_smv']=$row[csf('sew_smv')];  
                $pre_costing_date_arr[$row[csf('job_no')]]['costing_date']=change_date_format($row[csf('costing_date')],'','',1);  
                $sew_smv=$row[csf("sew_smv")];
                $exchange_rate=$row[csf("exchange_rate")];

                $job_no_array[$row[csf('job_no')]]=$row[csf('job_no')];
            } 
            
            //echo $exchange_rate;die;
            $jobIds=implode(",",array_filter(array_unique(explode(",",$jobIds))));
            $job_ids=count(explode(",",$jobIds)); $jobId_cond="";
            if($db_type==2 && $job_ids>1000)
            {
                $jobId_cond=" and (";
                $jobIdsArr=array_chunk(explode(",",$jobIds),999);
                foreach($jobIdsArr as $ids)
                {
                    $ids=implode(",",$ids);
                    $jobId_cond.=" job_id in($ids) or"; 
                }
                $jobId_cond=chop($jobId_cond,'or ');
                $jobId_cond.=")";
            }
            else $jobId_cond=" and job_id in($jobIds)";

            
            
            $bomDtls_arr=array();
            $bomDtlssql=sql_select( "select job_no, costing_per_id, fabric_cost_percent, trims_cost_percent, embel_cost_percent, wash_cost_percent, cm_cost_percent, cm_cost, margin_pcs_set_percent, margin_pcs_set from wo_pre_cost_dtls where status_active=1 and is_deleted=0 $jobId_cond");
            foreach ($bomDtlssql as $row)
            {
                
                $bomDtls_arr[$row[csf('job_no')]]['trimper']=$row[csf('trims_cost_percent')];
                $bomDtls_arr[$row[csf('job_no')]]['cm']=$row[csf('cm_cost_percent')];
                $bomDtls_arr[$row[csf('job_no')]]['ms']=$row[csf('fabric_cost_percent')]+$row[csf('trims_cost_percent')]+$row[csf('embel_cost_percent')]+$row[csf('wash_cost_percent')];
                $bomDtls_arr[$row[csf('job_no')]]['margin']=$row[csf('margin_pcs_set_percent')];
                
                $dzn_qnty=$cmpcs=$marginpcs=0;
                if($row[csf('costing_per_id')]==1) $dzn_qnty=12;
                else if($row[csf('costing_per_id')]==3) $dzn_qnty=12*2;
                else if($row[csf('costing_per_id')]==4) $dzn_qnty=12*3;
                else if($row[csf('costing_per_id')]==5) $dzn_qnty=12*4;
                else $dzn_qnty=1;
                $cmpcs=$row[csf('cm_cost')];
                $marginpcs=$row[csf('margin_pcs_set')];
                
                $bomDtls_arr[$row[csf('job_no')]]['cmval'] = $cmpcs; 
                $bomDtls_arr[$row[csf('job_no')]]['marginval'] = $marginpcs;
            }
            unset($bomDtlssql);

            
            if($jobIds!="")
            {
                $condition= new condition();
                $condition->company_name("=$company_id");
                if(str_replace("'","",$cbo_buyer_name)>0){
                    $condition->buyer_name("=$cbo_buyer_name");
                }
                if($jobIds!=''){
                    $condition->jobid_in("$jobIds");
                }
                if(str_replace("'","",$txt_file_no)!='')
                {
                    $condition->file_no("=$txt_file_no"); 
                }
                if(str_replace("'","",$txt_internal_ref)!='')
                {
                    $condition->grouping("=$txt_internal_ref"); 
                }

                $financial_para=array();
                $sql_std_para=sql_select("select interest_expense,income_tax,cost_per_minute,applying_period_date, applying_period_to_date from lib_standard_cm_entry where company_id=$company_id and status_active=1 and is_deleted=0 order by id");

                
                foreach($sql_std_para as $row )
                {
                    $applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
                    $applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
                    $diff=datediff('d',$applying_period_date,$applying_period_to_date);
                    for($j=0;$j<$diff;$j++)
                    {
                        $date_all=add_date(str_replace("'","",$applying_period_date),$j);
                        $newdate =change_date_format($date_all,'','',1);
                        $financial_para[$newdate]['interest_expense']=$row[csf('interest_expense')];
                        $financial_para[$newdate]['income_tax']=$row[csf('income_tax')];
                        $financial_para[$newdate]['cost_per_minute']=$row[csf('cost_per_minute')];
                    }
                }

                // print_r($financial_para);

                $condition->init();
                $yarn = new yarn($condition);
                $yarn_data_array = $yarn->getJobWiseYarnAmountArray();
                $fabric = new fabric($condition);
                $fabric_amount = $fabric->getAmountArray_by_Fabriccostid_knitAndwoven_greyAndfinish();
                $fabric_costing_arr = $fabric->getAmountArray_by_job_knitAndwoven_greyAndfinish();
                $conversion = new conversion($condition);
                $conv_amount_arr = $conversion->getAmountArray_by_jobAndProcess();
                

                $fabric = new fabric($condition);
                    
                $yarn = new yarn($condition);

                $conversion = new conversion($condition);
                
                $trim = new trims($condition);
                
                $emblishment = new emblishment($condition);
                $other = new other($condition);

                $commercial = new commercial($condition);
                
                $commision = new commision($condition);
                
                $wash = new wash($condition);
                
                
                $fabric_costing_arr = $fabric->getAmountArray_by_job_knitAndwoven_greyAndfinish();

                $yarn_costing_arr = $yarn->getJobWiseYarnAmountArray();
                
                $conversion_costing_arr_process = $conversion->getAmountArray_by_job();
                $trims_costing_arr = $trim->getAmountArray_by_job();
                $emblishment_costing_arr = $emblishment->getAmountArray_by_job();
                $emblishment_costing_arr_wash = $wash->getAmountArray_by_job();
                $commercial_costing_arr = $commercial->getAmountArray_by_job();
                $commission_costing_arr = $commision->getAmountArray_by_job();
                $other_costing_arr = $other->getAmountArray_by_job();

                $calCMarr=array();$cm=array();$margin=array();
                foreach($job_no_array as $job_no ){
                        
                    $ttl_cm_cost=$other_costing_arr[$job_no]['cm_cost'];
                    $fab_purchase_knit=array_sum($fabric_costing_arr['knit']['grey'][$job_no]);
                    $fab_purchase_woven=array_sum($fabric_costing_arr['woven']['grey'][$job_no]);
                    $yarn_costing=$yarn_costing_arr[$job_no];
                    $conversion_cost=array_sum($conversion_costing_arr_process[$job_no]);

                    $cpmCal=($financial_para[$pre_costing_date_arr[$job_no]['costing_date']]['cost_per_minute']/$exchange_rate_Arr[$job_no]['exchange_rate'])/($sew_effi_Arr[$job_no]['sew_effi_percent']/100);

                    $fabricCost=$fab_purchase_knit+$fab_purchase_woven+$yarn_costing+$conversion_cost;

                    $totMaterialCost=$fabricCost+$trims_costing_arr[$job_no]+$emblishment_costing_arr_wash[$job_no]+$emblishment_costing_arr[$job_no];
                    $otherCost=$commercial_costing_arr[$job_no]+$other_costing_arr[$job_no]['currier_pre_cost']+$commission_costing_arr[$job_no]+$other_costing_arr[$job_no]['lab_test']+$other_costing_arr[$job_no]['freight'];

                    $breakevencm = $cpmCal*$sew_value_Arr[$job_no]['sew_smv']*$job_qnty_Arr[$job_no]['job_quantity'];
                    $tot_qnty = $totMaterialCost+$otherCost;
                    $calCM = $job_value_Arr[$job_no]['total_value']-($totMaterialCost+$otherCost);


    
                    $totalMargin = $calCM-$breakevencm;
                    $marginPcs = $totalMargin/$poQty;
                    $calCMarr[$job_no] = $calCM /$job_qnty_Arr[$job_no]['job_quantity'];
                    $cm[$job_no] = ($calCM / $job_value_Arr[$job_no]['total_value'])*100;
                    $margin[$job_no] = ($totalMargin / $job_value_Arr[$job_no]['total_value'])*100;

                    $calCMarr[$job_no]=$calCM /$job_qnty_Arr[$job_no]['job_quantity'];

                        $cm[$job_no] =($calCM / $job_value_Arr[$job_no]['total_value'])*100;

                        $cpmCalarr[$job_no] =$cpmCal;
                        
                }
            }
            //print_r($margin); 

            $sql_fabric = "select id, job_no, uom, fabric_source from wo_pre_cost_fabric_cost_dtls where status_active=1 and is_deleted=0 $jobId_cond";
            $data_arr_fabric=sql_select($sql_fabric); $fabricPurchesamt_arr=array();
            foreach($data_arr_fabric as $fab_row)
            {
                $purchase_amt=0;
                if($fab_row[csf("fab_source")]==2)
                {
                    $purchase_amt=$fabric_amount['knit']['grey'][$fab_row[csf("id")]][$fab_row[csf("uom")]]+$fabric_amount['woven']['grey'][$fab_row[csf("id")]][$fab_row[csf("uom")]];
                    $fabricPurchesamt_arr[$fab_row[csf("job_no")]]['fabpur']+=$purchase_amt;
                }
            }
            unset($data_arr_fabric);
        
            $sql_unapproved=sql_select("select * from fabric_booking_approval_cause where  entry_form=15 and approval_type=2 and is_deleted=0 and status_active=1");
            $unapproved_request_arr=array();
            foreach($sql_unapproved as $rowu)
            {
                $unapproved_request_arr[$rowu[csf('booking_id')]]=$rowu[csf('approval_cause')];
            }
            
            //Pre cost button---------------------------------
            $print_report_format_ids = return_field_value("format_id","lib_report_template","template_name=".$company_id." and module_id=2 and report_id in (122) and is_deleted=0 and status_active=1");
            $format_ids=explode(",",$print_report_format_ids);
            $row_id=$format_ids[0];
            //print_r($row_id);
            //Order Wise Budget Report button---------------------------------
            $print_report_format_ids2 = return_field_value("format_id","lib_report_template","template_name=".$company_id." and module_id=11 and report_id=18 and is_deleted=0 and status_active=1");
            $format_ids2=explode(",",$print_report_format_ids2);
            $row_id2=$format_ids2[0];
            
            $ii=1;
            $aop_cost_arr=array(35,36,37,40);
            $app_data_arr=array();

            foreach ($nameArray as $row)
            {
                if ($ii%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                $value=$row[csf('id')];
                if($row[csf('approval_id')]==0) $print_cond=1;
                else
                {
                    if($duplicate_array[$row[csf('id')]][$row[csf('sequence_no')]][$row[csf('approved_by')]]=="")
                    {
                        $duplicate_array[$row[csf('id')]][$row[csf('sequence_no')]][$row[csf('approved_by')]]=$row[csf('approval_id')];
                        $print_cond=1;
                    }
                    else
                    {
                        if($all_approval_id=="") $all_approval_id=$row[csf('approval_id')]; else $all_approval_id.=",".$row[csf('approval_id')];
                        $print_cond=0;
                    }
                }
                if($row_id2==23){$type=1;/*Summary;*/}
                else if($row_id2==24){$type=2;}
                else if($row_id2==25){$type=3;/*Budget Report2;*/}
                else if($row_id2==26){$type=4;/*Quote Vs Budget;*/}
                else if($row_id2==27){$type=5;/*Budget On Shipout;*/}
                else if($row_id2==29){$type=6;/*C.Date Budget On Shipout;*/}
                else if($row_id2==182){$type=7;/*Budget Report 3;*/}
        
                $function2="generat_print_report($type,$company_id,0,'','',{$row[csf('job_no_prefix_num')]},'','','',".$row[csf('year')].",0,1,'','','','')";
                //{$row[csf('buyer_name')]}
                if($print_cond==1)
                {
                    if($row_id==50){$action='preCostRpt'; } //report_btn_1;
                    else if($row_id==51){$action='preCostRpt2';} //report_btn_2;
                    else if($row_id==52){$action='bomRpt';} //report_btn_3;
                    else if($row_id==63){$action='bomRpt2';} //report_btn_4;
                    else if($row_id==156){$action='accessories_details';} //report_btn_5;
                    else if($row_id==157){$action='accessories_details2';} //report_btn_6;
                    else if($row_id==158){$action='preCostRptWoven';} //report_btn_7;
                    else if($row_id==159){$action='bomRptWoven';} //report_btn_8;
                    else if($row_id==170){$action='preCostRpt3';} //report_btn_9;
                    else if($row_id==171){$action='preCostRpt4';} //report_btn_10;
                    else if($row_id==173){$action='preCostRpt5';} //report_btn_10;
                    else if($row_id==211){$action='mo_sheet';}
                    else if($row_id==142){$action='preCostRptBpkW';}
                    else if($row_id==197){$action='bomRpt3';}
                    else if($row_id==192){$action='checkListRpt';}
                    else if($row_id==221){$action='fabric_cost_detail';}
                    else if($row_id==238){$action='summary';}
                    else if($row_id==215){$action='budget3_details';}
                    else if($row_id==730){$action='budgetsheet';}
        
                    $function="generate_worder_report('".$action."','".$row[csf('job_no')]."',".$company_id.",".$row[csf('buyer_name')].",'".$row[csf('style_ref_no')]."','".$row[csf('costing_date')]."',".$row[csf('entry_from')].",'".$row[csf('quotation_id')]."');"; 
                    
                    $jobavgRate=0; $int_ref = ""; $file_numbers = "";
                    $jobavgRate=$row[csf('total_price')]/$row[csf('job_quantity')];
                    if($db_type==2) $row[csf('internalRef')]= $row[csf('internalRef')]->load();
                    //if($db_type==2) $row[csf('fileNo')]= $row[csf('fileNo')]->load();
                    
                    $int_ref=implode(",",array_unique(explode(",",chop($row[csf('internalRef')],","))));
                    //$file_numbers=implode(",",array_unique(explode(",",chop($row[csf('fileNo')],",")))); 
                    $yarnPercent=$trimPercent=$fabpurchase_per=$aopamt=$yarn_dyeingAmt=$yarn_dyeingPer=$msper=$aopPer=$cmper=$marginper=0;
                    $yarnPercent=($yarn_data_array[$row[csf('job_no')]]/$row[csf('total_price')])*100;
                    $trimPercent=$bomDtls_arr[$row[csf('job_no')]]['trimper'];
                    
                    $fabpurchase_per=($fabricPurchesamt_arr[$row[csf('job_no')]]['fabpur']/$row[csf('total_price')])*100;
                    
                    $yarn_dyeingAmt=array_sum($conv_amount_arr[$row[csf('job_no')]][30]);
                    $yarn_dyeingPer=($yarn_dyeingAmt/$row[csf('total_price')])*100;
                    
                    foreach($aop_cost_arr as $aop_process_id)
                    {
                        $aopamt+=array_sum($conv_amount_arr[$row[csf('job_no')]][$aop_process_id]);
                    }
                    $aopPer=($aopamt/$row[csf('total_price')])*100;
                    
                    $btwob_per=$yarnPercent+$fabpurchase_per+$trimPercent+$yarn_dyeingPer+$aopPer;
                    
                    $msper=$bomDtls_arr[$row[csf('job_no')]]['ms'];
                    $cmper=$bomDtls_arr[$row[csf('job_no')]]['cm'];
                    $marginper=$bomDtls_arr[$row[csf('job_no')]]['margin'];
                    if(empty($marginper) || $marginper=='')
                    {
                        $marginper=0;
                    }
        
                    $app_data_arr[$ii]['booking_id']=$value;
                    $app_data_arr[$ii]['booking_no']=$row[csf('job_no')];
                    $app_data_arr[$ii]['set_smv']=$row[csf('set_smv')];
                    $app_data_arr[$ii]['sew_effi_percent']=$row[csf('sew_effi_percent')];
                    $app_data_arr[$ii]['approval_id']=$row[csf('approval_id')];
                    $app_data_arr[$ii]['no_joooob']=$ii;
                    $app_data_arr[$ii]['no_joooob_id']=strtoupper($row[csf('job_no')]);
                    $app_data_arr[$ii]['cm_cost_id']=$cm_cost;
                    $app_data_arr[$ii]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
                    $app_data_arr[$ii]['function']=$function;
                    $app_data_arr[$ii]['int_ref']=$int_ref;
                    $app_data_arr[$ii]['cmPcs']=$cmPcs;
                    $app_data_arr[$ii]['buyer_id']=$row[csf('buyer_name')];
                    $app_data_arr[$ii]['buyer_name']=$buyer_arr[$row[csf('buyer_name')]];
                    $app_data_arr[$ii]['year']=$row[csf('year')];
                    $app_data_arr[$ii]['style_ref_no']=$row[csf('style_ref_no')];
                    $app_data_arr[$ii]['function2']=$function2;
                    $app_data_arr[$ii]['costing_date']=$row[csf('costing_date')];
                    $app_data_arr[$ii]['minship_date']=$row[csf('minship_date')];
                    $app_data_arr[$ii]['maxship_date']=$row[csf('maxship_date')];
                    $app_data_arr[$ii]['job_qty_pcs']=$row[csf('job_qty_pcs')];
                    $app_data_arr[$ii]['jobavgRate']=$jobavgRate;
                    $app_data_arr[$ii]['total_price']=$row[csf('total_price')];
                    $app_data_arr[$ii]['yarnPercent']=$yarnPercent;
                    $app_data_arr[$ii]['trimPercent']=$trimPercent;
                    $app_data_arr[$ii]['btwob_per']=$btwob_per;
                    $app_data_arr[$ii]['msper']=$msper;
                    $app_data_arr[$ii]['cmper']=$cmper;
                    $app_data_arr[$ii]['marginper']=$marginper;
                    $app_data_arr[$ii]['approval_type']=$approval_type;
                    $app_data_arr[$ii]['unapproved_request']=$unapproved_request_arr[$value];
                    $app_data_arr[$ii]['inserted_by']=ucfirst($user_arr[$row[csf('inserted_by')]]);
                    $app_data_arr[$ii]['approved_date']=$row[csf('approved_date')];
                    $app_data_arr[$ii]['all_approval_id']=$all_approval_id;
                    $app_data_arr[$ii]['print_cond']=$print_cond;
                    
                    $ii++;
                }
            }

            unset($nameArray);
            usort($app_data_arr, 'sortByMarginPercent');
            $f=1;
            $i=1;
            foreach ($app_data_arr as $index=>$row)
            {
                $value=$row['booking_id'];
                $booking_no=$row['booking_no'];
                $set_smv=$row['set_smv'];
                $sew_efficient=$row['sew_effi_percent'];
                $approval_id=$row['approval_id'];
                $no_joooob=$row['no_joooob'];
                $no_joooob_id=$row['no_joooob_id'];
                $cm_cost_id=$row['cm_cost_id'];
                $function=$row['function'];
                $job_no_prefix_num=$row['job_no_prefix_num'];
                $int_ref=$row['int_ref'];
                $cmPcs=$row['cmPcs'];
                $buyer_name=$row['buyer_name'];
                $year=$row['year'];
                $function2=$row['function2'];
                $style_ref_no=$row['style_ref_no'];
                $costing_date=$row['costing_date'];
                $minship_date=$row['minship_date'];
                $maxship_date=$row['maxship_date'];
                $job_qty_pcs=$row['job_qty_pcs'];
                $jobavgRate=$row['jobavgRate'];
                $total_price=$row['total_price'];
                $yarnPercent=$row['yarnPercent'];
                $trimPercent=$row['trimPercent'];
                $btwob_per=$row['btwob_per'];
                $msper=$row['msper'];
                $cmper=$row['cmper'];
                $marginper=$row['marginper'];
                $approval_type=$row['approval_type'];
                $unapproved_request=$row['unapproved_request'];
                $inserted_by=$row['inserted_by'];
                $approved_date=$row['approved_date'];
                $all_approval_id=$row['all_approval_id'];
                
                $cmCost=$bomDtls_arr[$booking_no]['cmval'];
                $marginval=$bomDtls_arr[$booking_no]['marginval'];

                if($isReq==1)
                {
                    if($cm_std_per>$cmper && $cm_std_per!=0)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        ?>
                        <tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>" align="center">
                            <!-- 19 -->
                            <td align="center"><?=$i; ?></td>
                            <td align="center"><?=$company_name;?></td>
                            <td><?=$job_no_prefix_num; ?></td>
                            <td style="word-break:break-all;"><?= $buyer_name; ?></td>
                            <td style="word-break:break-all;"><?=$year; ?></td>
                            <td align="center" style="word-break:break-all;"><?=$style_ref_no; ?></td>
                            <td align="center"><? if($costing_date!="0000-00-00") echo change_date_format($costing_date); ?>&nbsp;</td>
                            <td align="center" width="100"><? if($minship_date!="0000-00-00") echo change_date_format($minship_date); ?>&nbsp;</td>
                            <td align="center" width="100"><? if($maxship_date!="0000-00-00") echo change_date_format($maxship_date); ?>&nbsp;</td>
                            <td align="right" style="word-break:break-all;"><?=number_format($job_qty_pcs); ?></td>
                            <td align="right" style="word-break:break-all;"><?=number_format($jobavgRate,4); ?></td>
                            <td align="right" style="word-break:break-all;"><?=number_format($total_price,2); ?></td>
                            <td align="right" style="word-break:break-all;"><?=number_format($yarnPercent,2); ?></td>
                            <td width="60" align="right" style="word-break:break-all;"><?=$sew_efficient; ?></td>

                            <td align="right" style="word-break:break-all;"><?=number_format($trimPercent,2); ?></td>
                            <td align="right" style="word-break:break-all;"><?=number_format($btwob_per,2); ?></td>
                            <td align="right" style="word-break:break-all;" id="tdCm_<?=$i;?>" title="<?=$cmCost; ?>"><?=fn_number_format((($calCM/$order_values)*100),2).' %'; ?></td>
                            
                            <td align="right" style="word-break:break-all;"><?=number_format($cpmCalarr[$booking_no], 3); ?></td>

                            <td align="right" style="word-break:break-all;" title="<?=$marginval; ?>"><?=fn_number_format((($totalMargin/$order_values)*100),2).' %';?></td>
                            <td><? if($approved_date!="0000-00-00") echo change_date_format($approved_date); ?>&nbsp;</td>
                            <td style="word-break:break-all;"><?=$inserted_by;?>&nbsp;</td>
                        </tr>
                        <?
                        $i++;
                        $f++;
                    }
                    else if($cm_std_value>$cmCost && $cm_std_value!=0)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        ?>
                            <!-- 19 -->
                            <tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>" align="center">
                            <td align="center"><?=$i; ?></td>
                            <td align="center"><?=$company_name;?></td>
                            <td><?=$job_no_prefix_num; ?></td>
                            <td style="word-break:break-all;"><?=$buyer_name; ?></td>
                            <td style="word-break:break-all;"><?=$year; ?></td>
                            <td align="center" style="word-break:break-all;"><?=$style_ref_no; ?></td>
                            <td align="center"><? if($costing_date!="0000-00-00") echo change_date_format($costing_date); ?>&nbsp;</td>
                            <td align="center"><? if($minship_date!="0000-00-00") echo change_date_format($minship_date); ?>&nbsp;</td>
                            <td align="center"><? if($maxship_date!="0000-00-00") echo change_date_format($maxship_date); ?>&nbsp;</td>
                            <td align="right" style="word-break:break-all;"><?=number_format($job_qty_pcs); ?></td>

                            <td align="right" style="word-break:break-all;"><?=number_format($jobavgRate,4); ?></td>
                            <td align="right" style="word-break:break-all;"><?=number_format($total_price,2); ?></td>
                            <td align="right" style="word-break:break-all;"><?=number_format($set_smv,2); ?></td>
                            <td width="60" align="right" style="word-break:break-all;"><?=$sew_efficient; ?></td>
                            <td align="right" style="word-break:break-all;"><?=number_format($cmPcs,2); ?></td>
                            <td align="right" style="word-break:break-all;"><?=number_format($cmPcs/$set_smv,2); ?></td>
                            

                            <td align="right" style="word-break:break-all;"><?=number_format($cpmCalarr[$booking_no],3); ?></td>
                            <td align="right" style="word-break:break-all;" id="tdCm_<?=$i;?>" title="<?=$cmCost; ?>"><?=fn_number_format((($calCM/$order_values)*100),2).' %'; ?></td>
                            

                            <td align="right" style="word-break:break-all;" title="<?=$marginval; ?>"><?=fn_number_format((($totalMargin/$order_values)*100),2).' %';?></td>
                            <td><? if($approved_date!="0000-00-00") echo change_date_format($approved_date); ?>&nbsp;</td>
                            <td style="word-break:break-all;"><?=$inserted_by;?>&nbsp;</td>
                        </tr>
                        <?
                        $i++;
                        $f++;
                    }
                    else if($margin_std_per>$marginper && $margin_std_per!=0)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        ?>
                        <tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>" align="center">
                            <!-- 19 -->
                            <td align="center"><?=$i; ?></td>
                            <td align="center"><?=$company_name;?></td>
                            <td><?=$job_no_prefix_num; ?></td>
                            <td style="word-break:break-all;"><?=$buyer_name; ?></td>
                            <td style="word-break:break-all;"><?=$year; ?></td>
                            <td align="center" style="word-break:break-all;"><?=$style_ref_no; ?></td>
                            <td align="center"><? if($costing_date!="0000-00-00") echo change_date_format($costing_date); ?>&nbsp;</td>
                            <td align="center"><? if($minship_date!="0000-00-00") echo change_date_format($minship_date); ?>&nbsp;</td>
                            <td align="center"><? if($maxship_date!="0000-00-00") echo change_date_format($maxship_date); ?>&nbsp;</td>
                            <td align="right" style="word-break:break-all;"><?=number_format($job_qty_pcs); ?></td>

                            <td align="right" style="word-break:break-all;"><?=number_format($jobavgRate,4); ?></td>
                            <td align="right" style="word-break:break-all;"><?=number_format($total_price,2); ?></td>
                            <td align="right" style="word-break:break-all;"><?=number_format($set_smv,2); ?></td>
                            <td width="60" align="right" style="word-break:break-all;"><?=$sew_efficient; ?></td>
                            <td align="right" style="word-break:break-all;"><?=number_format($cmPcs,2); ?></td>  
                            <td align="right" style="word-break:break-all;"><?=number_format($cmPcs/$set_smv,2); ?></td>
                            

                            <td align="right" style="word-break:break-all;"><?=number_format($cpmCalarr[$booking_no],3); ?></td>
                            <td align="right" style="word-break:break-all;" id="tdCm_<?=$i;?>" title="<?=$cmCost; ?>"><?=fn_number_format((($calCM/$order_values)*100),2).' %';?></td>
                            

                            <td align="right" style="word-break:break-all;" title="<?=$marginval; ?>"><?=fn_number_format((($totalMargin/$order_values)*100),2).' %';?></td>
                            <td><? if($approved_date!="0000-00-00") echo change_date_format($approved_date); ?>&nbsp;</td>
                            <td style="word-break:break-all;"><?=$inserted_by;?>&nbsp;</td>
                        </tr>
                        <?
                        $i++; 
                    }
                    else if($margin_std_value>$marginval && $margin_std_value!=0)
                    {
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        ?>
                        <tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>" align="center">
                            <!-- 21 -->
                            <td align="center"><?=$i; ?></td>
                            <td align="center"><?=$company_name;?></td>
                            <td><?=$job_no_prefix_num; ?></td>
                            <!-- <td style="word-break:break-all;">< ?=$int_ref; ?></td> -->
                            <td style="word-break:break-all;"><?=$buyer_name; ?></td>
                            <td style="word-break:break-all;"><?=$year; ?></td>
                            <td align="center" style="word-break:break-all;"><?=$style_ref_no; ?></td>
                            <!-- <td align="right" style="word-break:break-all;" title="< ?=$marginval; ?>">< ?=number_format($marginper,2); ?></td> -->
                            <td align="center"><? if($costing_date!="0000-00-00") echo change_date_format($costing_date); ?>&nbsp;</td>
                            <td align="center"><? if($minship_date!="0000-00-00") echo change_date_format($minship_date); ?>&nbsp;</td>

                            <td align="center"><? if($maxship_date!="0000-00-00") echo change_date_format($maxship_date); ?>&nbsp;</td>
                            <td align="right" style="word-break:break-all;"><?=number_format($job_qty_pcs); ?></td>
                            <td align="right" style="word-break:break-all;"><?=number_format($jobavgRate,4); ?></td>
                            <td align="right" style="word-break:break-all;"><?=number_format($total_price,2); ?></td>
                            <td width="60" align="right" style="word-break:break-all;"><?=number_format($set_smv,2); ?></td>
                            <td width="60" align="right" style="word-break:break-all;"><?=$sew_efficient; ?></td>
                            <td width="60" align="right" style="word-break:break-all;">
                                <?php 
                                echo number_format($calCMarr[$booking_no], 3);
                                ?>
                            </td>
                            <?
                            $cpm=$calCMarr[$booking_no];
                            ?>
                            <td width="60" align="right" style="word-break:break-all;"><?=number_format($cpm/$set_smv,3); ?></td>
                            <td align="right" style="word-break:break-all;"><?=number_format($cpmCalarr[$booking_no],3); ?></td>
                            <td width="60" align="right" style="word-break:break-all;" id="tdCm_<?=$i;?>" title="<?=$cmCost; ?>"><?=number_format($cm[$booking_no],2).' %'; ?></td>

                            <td width="60" align="right" style="word-break:break-all;" title="<?=$marginval; ?>"><?=number_format($margin[$booking_no],2).' %'; ?></td>
                            <td width="140"><? if($approved_date!="0000-00-00") echo change_date_format($approved_date); ?>&nbsp;</td>
                            <td width="65" style="word-break:break-all;"><?=$inserted_by;?>&nbsp;</td>
                        </tr>
                        <?
                        $i++;
                    }
                }
            }
            ?>
            </tbody>
        </table>
    </div>
	<br/>
    <?}
}

$message = ob_get_contents();
ob_clean();
$mail_item = 141;
$sql = "SELECT c.EMAIL_ADDRESS FROM mail_group_mst a, mail_group_child b, user_mail_address c where a.id=b.mail_group_mst_id and b.mail_user_setup_id=c.id  and a.mail_item=141 and a.IS_DELETED=0 and a.STATUS_ACTIVE=1 AND a.MAIL_TYPE=1 and b.IS_DELETED=0 and b.STATUS_ACTIVE=1   and c.IS_DELETED=0 and c.STATUS_ACTIVE=1";
//echo $sql;die;
$mail_sql = sql_select($sql);
foreach ($mail_sql as $row) {
    if ($row["EMAIL_ADDRESS"]) {
        $toArr[$row["EMAIL_ADDRESS"]] = $row["EMAIL_ADDRESS"];
    }
}
$to = implode(',', $toArr);

if ($_REQUEST['isview'] == 1) {
    
    if ($to) {
        echo 'Mail Item:' . $form_list_for_mail[$mail_item] . '=>' . $to;
    } else {
        echo "Mail address not set. [Please set mail from  Mail Recipient Group, Mail Item: <b>" . $form_list_for_mail[$mail_item] . "</b>]<br>";
    }
    echo $message;
} else {
    require_once('../../mailer/class.phpmailer.php');
    require_once('../setting/mail_setting.php');
    $header = mailHeader();
    $subject = "BOM Confirmation Before Approval";
    if ($to != "") {
        // $to = "alhassan.cse@gmail.com";
        // echo "send";
        echo sendMailMailer($to, $subject, $message, $from_mail);
    }
}
 
?>