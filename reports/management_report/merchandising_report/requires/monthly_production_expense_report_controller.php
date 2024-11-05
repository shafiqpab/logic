<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../../includes/common.php');
require_once('../../../../includes/class3/class.conditions.php');
require_once('../../../../includes/class3/class.reports.php');
require_once('../../../../includes/class3/class.commisions.php');
require_once('../../../../includes/class3/class.commercials.php');
require_once('../../../../includes/class3/class.others.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 150, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select --", $selected, "",0 );  
	exit();  	 
}

if ($action=="eval_multi_select")
{
 	echo "set_multiselect('cbo_location','0','0','','0');\n";
	exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$cbo_company=str_replace("'","",$cbo_company_id);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	$type=str_replace("'","",$type);
	$location_id=str_replace("'","",$cbo_location);
	//echo $location_id.'kausar'; die;
	$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$costing_per_arr = return_library_array("select job_no, costing_per from wo_pre_cost_mst","job_no","costing_per"); 
	$tot_cost_arr = return_library_array("select job_no, cm_for_sipment_sche from wo_pre_cost_dtls","job_no","cm_for_sipment_sche"); 
	if($location_id=="") $location_con=""; else $location_con=" and a.location in ($location_id)";


	$condition= new condition();
	$condition->company_name("=$cbo_company_id");
	
	$condition->init();
	
	$commercial= new commercial($condition);
	$commercial_costing_arr=$commercial->getAmountArray_by_order();
	//print_r($commercial_costing_arr);
	//echo $commercial->getQuery(); die;
	
	$commission= new commision($condition);
	$commission_costing_arr=$commission->getAmountArray_by_orderAndItemid();	
	//echo $commission->getQuery(); die;	
			
	$other= new other($condition);
	$other_costing_arr=$other->getAmountArray_by_order();
	//echo $other->getQuery(); die;	
	
	ob_start();	
	?>
        <table width="2830px" cellpadding="0" cellspacing="0" id="caption" align="center">
            <tr>
            <td align="center" width="100%" colspan="28" class="form_caption"><strong style="font-size:18px">Company Name:<? echo $company_library[$cbo_company]; ?></strong></td>
            </tr> 
            <tr>  
               <td align="center" width="100%" colspan="28" class="form_caption" ><strong style="font-size:14px"><? echo $report_title; ?></strong></td>
            </tr>
            <tr>  
               <td align="center" width="100%" colspan="28" class="form_caption" ><strong style="font-size:14px"> <? echo "From : ".change_date_format(str_replace("'","",$txt_date_from))." To : ".change_date_format(str_replace("'","",$txt_date_to))."" ;?></strong></td>
            </tr>  
        </table>
        <table width="2830px " border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
            <thead>
            	<tr>
                    <th width="35" rowspan="4" valign="middle">SL</th>
                    <th width="90" rowspan="4" valign="middle">Production Date</th>
                    <th colspan="14">Production Status </th>
                    <th colspan="7">Order value and SMV</th>
                    <th colspan="2">Ex-Factory Details</th>
                    <th colspan="2" valign="middle">CM Value On Sewing Qty (USD)</th>
                    <th colspan="5" valign="middle">Others Cost on Total Sew Qty (USD)</th>
                </tr>
            	<tr>
                    <th width="80" rowspan="3" valign="middle">Knitting (Kg) </th>
                    <th width="80" rowspan="3" valign="middle">Finish Fabric (Kg)</th>
                    <th width="80" rowspan="3" valign="middle">Printing (Pcs)</th>
                    <th width="80" rowspan="3" valign="middle">Emb. (Pcs)</th>
                    <th colspan="3">Cutting (Pcs)</th>
                    <th colspan="3">Sewing (Pcs)</th>
                    <th colspan="3">Finish Gmts (Pcs)</th>
                    <th width="80" rowspan="3" valign="middle">Carton (Pcs)</th>
                    <th colspan="3">Order Value On Sewing Qty (USD)</th>
                    <th colspan="4">SMV (On Sewing Qty)</th>
                    <th rowspan="3" width="100">Ex-Factory Qty (Pcs)</th>
                    <th rowspan="3" width="80">Ex-Factory Value (USD)</th>
                    <th width="100" rowspan="3">In House</th>
                    <th width="100" rowspan="3">Sub Con</th>
                    
                    <th rowspan="3" width="80">Lab Test</th>
                    <th rowspan="3" width="80">Commercial Cost</th>
                    <th rowspan="3" width="80">BH Commission</th>
                    <th rowspan="3" width="80">Currier Cost</th>
                    <th rowspan="3">Certificate Cost</th>
                </tr>
            	<tr>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                    <th rowspan="2" width="80">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="80">Total</th>
                    <th rowspan="2" width="100">In House</th>
                    <th rowspan="2" width="80">Sub Contact</th>
                    <th rowspan="2" width="100">Total</th>
                    <th rowspan="2" width="100">SAH Available</th>
                    <th colspan="2">SAH Produced</th>
                    <th rowspan="2" width="100">Efficiency</th>
                </tr>
                <tr>
                    <th width="100">In House</th>
                    <th width="100">Sub Con</th>
                </tr>
            </thead>
        </table>
        <div style="max-height:420px; overflow-y:scroll; width:2850px" id="scroll_body">
        <table cellspacing="0" border="1" class="rpt_table"  width="2830px" rules="all" id="scroll_body" >
      <?
		$smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($cbo_company) and variable_list=25 and status_active=1 and is_deleted=0");
		if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
		$item_smv_array=array();
		if($smv_source==3)
		{
			$sql_item="select b.id, a.sam_style, a.gmts_item_id from ppl_gsd_entry_mst a, wo_po_break_down b where b.job_no_mst=a.po_job_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			$resultItem=sql_select($sql_item);
			foreach($resultItem as $itemData)
			{
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('sam_style')];
			}
		}
		else
		{
			$sql_item="select b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, c.smv_pcs, c.smv_pcs_precost from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no and a.company_name in($cbo_company) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			$resultItem=sql_select($sql_item);
			foreach($resultItem as $itemData)
			{
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]['smv_pcs']=$itemData[csf('smv_pcs')];
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]['smv_pcs_precost']=$itemData[csf('smv_pcs_precost')];
			}
		}
		unset($resultItem);
		
		$tpdArr=array(); $tsmvArr=array();
        $tpd_data_arr=sql_select( "select a.pr_date, sum(a.target_per_hour*a.working_hour) tpd, sum(a.man_power*a.working_hour) tsmv from prod_resource_dtls a, prod_resource_mst b where b.id=a.mst_id and b.company_id in($cbo_company) and a.pr_date between '$date_from' and '$date_to' and a.is_deleted=0 and b.is_deleted=0 group by  a.pr_date");
        foreach($tpd_data_arr as $row)
        {
			$production_date=date("Y-m-d", strtotime($row[csf('pr_date')])); 
			$tsmvArr[change_date_format($production_date)]['smv']=$row[csf('tsmv')]*60;
        }
		unset($tpd_data_arr);
		
		$job_array=array(); 
		$job_sql="select a.id, a.unit_price, b.job_no, b.total_set_qnty, b.set_smv, a.po_quantity from wo_po_break_down a, wo_po_details_master b where a.job_no_mst=b.job_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 ";
		
		$job_sql_result=sql_select($job_sql);
		foreach($job_sql_result as $row)
		{
			$job_array[$row[csf("id")]]['unit_price']=$row[csf("unit_price")];
			$job_array[$row[csf("id")]]['job_no']=$row[csf("job_no")];
			$job_array[$row[csf("id")]]['total_set_qnty']=$row[csf("total_set_qnty")];
			$job_array[$row[csf("id")]]['set_smv']=$row[csf("set_smv")];
			$job_array[$row[csf("id")]]['po_qty']=$row[csf("po_quantity")];
		}	  
	  	unset($job_sql_result);
	  
		  $total_knit=0;
		  $total_finishing=0;
		  $total_print=0;
		  $total_emb=0;
		  $total_cutting=0;
		  $total_cutting_inhouse=0;
		  $total_cutting_subcontract=0;
		  $total_sew=0;
		  $total_sew_inhouse=0;
		  $total_sew_subcontract=0;
		  $total_finishg=0;
		  $total_finish_inhouse=0;
		  $total_finish_subcontract=0;
		  $total_carton=0;
		 
		  $dtls_sql="SELECT production_date, po_break_down_id as po_breakdown_id, item_number_id,
					sum(CASE WHEN production_type =1 THEN production_quantity END) AS cutting_qnty,
					sum(CASE WHEN production_type =1 and production_source=1 THEN production_quantity END) AS cutting_qnty_inhouse,
					sum(CASE WHEN production_type =1 and production_source=3 THEN production_quantity END) AS cutting_qnty_outbound, 
					
					sum(CASE WHEN production_type =3 and embel_name=1 THEN production_quantity END) AS printing_qnty,
					sum(CASE WHEN production_type =3 and embel_name=1 and production_source=1 THEN production_quantity END) AS printing_qnty_inhouse,
					sum(CASE WHEN production_type =3 and embel_name=1 and production_source=3 THEN production_quantity END) AS printing_qnty_outbound, 
					
					sum(CASE WHEN production_type =3 and embel_name=2 THEN production_quantity END) AS emb_qnty,
					sum(CASE WHEN production_type =3 and embel_name=2 and production_source=1 THEN production_quantity END) AS emb_qnty_inhouse,
					sum(CASE WHEN production_type =3 and embel_name=2 and production_source=3 THEN production_quantity END) AS emb_qnty_outbound,
					 
					sum(CASE WHEN production_type =5 THEN production_quantity END) AS sewing_qnty,
					sum(CASE WHEN production_type =5 and production_source=1 THEN production_quantity END) AS sewingout_qnty_inhouse,
					sum(CASE WHEN production_type =5 and production_source=3 THEN production_quantity END) AS sewingout_qnty_outbound, 
					
					sum(CASE WHEN production_type =8 THEN production_quantity END) AS finish_qnty,
					sum(CASE WHEN production_type =8 and production_source=1 THEN production_quantity END) AS finish_qnty_inhouse, 
					sum(CASE WHEN production_type =8 and production_source=3 THEN production_quantity END) AS finish_qnty_outbound,
					sum(CASE WHEN production_type =8  THEN carton_qty END) AS carton_qty 
					from pro_garments_production_mst a, wo_po_break_down b
					where a.po_break_down_id=b.id $location_con and company_id like '$cbo_company' and production_date between '$date_from' and '$date_to' and a.is_deleted=0 and a.status_active=1 and b.status_active=1 group by production_date, po_break_down_id, item_number_id order by production_date asc";
			
			 //echo $dtls_sql;
			 $dtls_sql_result=sql_select($dtls_sql);
			 $prod_date=array();$po_id=""; $po_sewing_qty=array(); $other_cost_poid_arr=array();
			 foreach($dtls_sql_result as $row)
			 {
				 $production_date=date("Y-m-d", strtotime($row[csf('production_date')])); 
				 $prod_date[change_date_format($row[csf("production_date")])]['po_breakdown_id'].=$row[csf("po_breakdown_id")].",";
				 $prod_date[change_date_format($row[csf("production_date")])]['production_date']=$row[csf("production_date")];
				 $prod_date[change_date_format($row[csf("production_date")])]['printing_qnty']+=$row[csf("printing_qnty")];
				 $prod_date[change_date_format($row[csf("production_date")])]['emb_qnty']+=$row[csf("emb_qnty")];
				 $prod_date[change_date_format($row[csf("production_date")])]['cutting_qnty_inhouse']+=$row[csf("cutting_qnty_inhouse")];
				 $prod_date[change_date_format($row[csf("production_date")])]['cutting_qnty_outbound']+=$row[csf("cutting_qnty_outbound")];
				 $prod_date[change_date_format($row[csf("production_date")])]['cutting_qnty']+=$row[csf("cutting_qnty")];
				 $prod_date[change_date_format($row[csf("production_date")])]['sewingout_qnty_inhouse']+=$row[csf("sewingout_qnty_inhouse")];
				 $prod_date[change_date_format($row[csf("production_date")])]['sewingout_qnty_outbound']+=$row[csf("sewingout_qnty_outbound")];
				 $prod_date[change_date_format($row[csf("production_date")])]['sewing_qnty']+=$row[csf("sewing_qnty")];
				 $prod_date[change_date_format($row[csf("production_date")])]['sewingout_qnty_outbound_pcs']+=($row[csf("sewingout_qnty_outbound")]*$item_smv_array[$row[csf("po_breakdown_id")]][$row[csf("item_number_id")]]['smv_pcs']);
				 $prod_date[change_date_format($row[csf("production_date")])]['sewingout_qnty_inhouse_pcs']+=($row[csf("sewingout_qnty_inhouse")]*$item_smv_array[$row[csf("po_breakdown_id")]][$row[csf("item_number_id")]]['smv_pcs']);
				 $other_cost_poid_arr[$row[csf("po_breakdown_id")]][change_date_format($row[csf("production_date")])]['sewing_qnty']+=$row[csf("sewing_qnty")];
				 $item_smv=0;
				if($smv_source==2)
				{
					$item_smv=$item_smv_array[$row[csf("po_breakdown_id")]][$row[csf("item_number_id")]]['smv_pcs_precost'];
				}
				else if($smv_source==3)
				{
					$item_smv=$item_smv_array[$row[csf("po_breakdown_id")]][$row[csf("item_number_id")]];	
				}
				else
				{
					$item_smv=$item_smv_array[$row[csf("po_breakdown_id")]][$row[csf("item_number_id")]]['smv_pcs'];	
				}
				 
				 $prod_date[change_date_format($row[csf("production_date")])]['sewing_qnty_smv']+=$row[csf("sewing_qnty")]*$item_smv;
				 
				 $prod_date[change_date_format($row[csf("production_date")])]['finish_qnty_inhouse']+=$row[csf("finish_qnty_inhouse")];
				 $prod_date[change_date_format($row[csf("production_date")])]['finish_qnty_outbound']+=$row[csf("finish_qnty_outbound")];
				 $prod_date[change_date_format($row[csf("production_date")])]['finish_qnty']+=$row[csf("finish_qnty")];
				 $prod_date[change_date_format($row[csf("production_date")])]['carton_qty']+=$row[csf("carton_qty")];
				 
				 $prod_date[change_date_format($row[csf("production_date")])]['sewingout_value_inhouse']+=$row[csf("sewingout_qnty_inhouse")]*$job_array[$row[csf("po_breakdown_id")]]['unit_price'];
				 $prod_date[change_date_format($row[csf("production_date")])]['sewingout_value_outbound']+=$row[csf("sewingout_qnty_outbound")]*$job_array[$row[csf("po_breakdown_id")]]['unit_price'];
				 
				 $cm_value_in=0; $cm_value_out=0;  $sewing_qty_in=0; $sewing_qty_out=0;
				 // $sewing_qnty=$row[csf("sewing_qnty")];
				 $sewing_qty_in=$row[csf("sewingout_qnty_inhouse")];
				 $sewing_qty_out=$row[csf("sewingout_qnty_outbound")];
				 $job_no=$job_array[$row[csf("po_breakdown_id")]]['job_no'];
				 $total_set_qnty=$job_array[$row[csf("po_breakdown_id")]]['total_set_qnty'];
				 $costing_per=$costing_per_arr[$job_no];
				 
				 if($costing_per==1) $dzn_qnty=12;
				 else if($costing_per==3) $dzn_qnty=12*2;
				 else if($costing_per==4) $dzn_qnty=12*3;
				 else if($costing_per==5) $dzn_qnty=12*4;
				 else $dzn_qnty=1;
							
				 $dzn_qnty=$dzn_qnty*$total_set_qnty;
				// $cm_value=($tot_cost_arr[$job_no]/$dzn_qnty)*$sewing_qnty;
				 $cm_value_in=($tot_cost_arr[$job_no]/$dzn_qnty)*$sewing_qty_in;
				 $cm_value_out=($tot_cost_arr[$job_no]/$dzn_qnty)*$sewing_qty_out;
				
				 //$prod_date[change_date_format($row[csf("production_date")])]['cm_value']+=$cm_value;
				 $prod_date[change_date_format($row[csf("production_date")])]['cm_value_in']+=$cm_value_in;
				 $prod_date[change_date_format($row[csf("production_date")])]['cm_value_out']+=$cm_value_out;
			}
			unset($dtls_sql_result);
			
			if($location_id=="") $location_con_rcv=""; else $location_con_rcv=" and a.location_id in ($location_id)";
			$knited_query="select a.receive_date as production_date, sum(b.grey_receive_qnty) as kniting_qnty from inv_receive_master a, pro_grey_prod_entry_dtls b where a.id=b.mst_id and a.company_id='$cbo_company' $location_con_rcv and a.receive_date between '$date_from' and '$date_to' and a.entry_form=2 and a.item_category=13 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.receive_date";
			
			$knited_query_result=sql_select($knited_query);
			$count_knit=count($knited_query_result);
			foreach( $knited_query_result as $knit_row)
			{
				$prod_date[change_date_format($knit_row[csf("production_date")])]['kniting_qnty']=$knit_row[csf("kniting_qnty")];
			}
			//var_dump($prod_datek);
			unset($knited_query_result);

			$finish_query="select a.receive_date as production_date, sum(b.receive_qnty) as finishing_qnty from  inv_receive_master a, pro_finish_fabric_rcv_dtls b where a.id=b.mst_id and a.company_id='$cbo_company' $location_con_rcv and a.receive_date between '$date_from' and '$date_to' and a.entry_form=7 and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.receive_date";
			//echo $finish_query;
			$finish_query_result=sql_select($finish_query);
			$count_finish=count($finish_query_result);
			foreach( $finish_query_result as $finish_row)
			{
				$prod_date[change_date_format($finish_row[csf("production_date")])]['finishing_qnty']=$finish_row[csf("finishing_qnty")];
			}
			//var_dump($prod_date);
			unset($finish_query_result);
			
			$exfactory_res = sql_select("select a.ex_factory_date, a.po_break_down_id, 
			sum(CASE WHEN a.entry_form!=85 THEN a.ex_factory_qnty ELSE 0 END)-sum(CASE WHEN a.entry_form=85 THEN a.ex_factory_qnty ELSE 0 END) as ex_factory_qnty
			from pro_ex_factory_mst a, wo_po_break_down b, wo_po_details_master c
			where a.po_break_down_id=b.id and b.job_no_mst=c.job_no and c.company_name=$cbo_company $location_con and a.is_deleted=0 and a.status_active=1 and a.ex_factory_date between '$date_from' and '$date_to' group by a.ex_factory_date, a.po_break_down_id");
			foreach($exfactory_res as $ex_row)
			{
				$prod_date[change_date_format($ex_row[csf("ex_factory_date")])]['ex_factory']+=$ex_row[csf("ex_factory_qnty")];
				$prod_date[change_date_format($ex_row[csf("ex_factory_date")])]['ex_factory_val']+=$ex_row[csf("ex_factory_qnty")]*($job_array[$ex_row[csf("po_break_down_id")]]['unit_price']/$job_array[$ex_row[csf("po_break_down_id")]]['total_set_qnty']);
			}
			unset($exfactory_res);
			 ksort($prod_date);
			 $i=1;
			 $printing=0; $embing=0; $cuting_in=0; $cuting_out=0; $cuting=0; $sewing_in=0; $sewing_out=0; $sewing=0; $finish_in=0; $finish_out=0; $finish=0; $carton=0; $ord_in=0; $ord_out=0; $ord_tot=0;
			
			for($j=0;$j<$datediff;$j++)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
				$date_all=add_date(str_replace("'","",$txt_date_from),$j);
				$newdate =change_date_format($date_all);
				$po_id=$prod_date[$newdate]['po_breakdown_id'];
				$produce_qty=$prod_date[$newdate]['sewingout_qnty_inhouse_pcs']/60;
				$effiecy_aff_perc=$produce_qty/($tsmvArr[$newdate]['smv']/60)*100;
				
				$sweing_qty_inAndOut=$prod_date[$newdate]['sewing_qnty'];
				
				$lab_test_cost=$commercial_cost=$bh_commission_cost=$currier_cost=$certificate_cost=0;
				$ex_po=array_filter(array_unique(explode(',',$po_id)));
				foreach($ex_po as $poIds)
				{
					$sweing_qty_inAndOut=0;
					$sweing_qty_inAndOut=$other_cost_poid_arr[$poIds][$newdate]['sewing_qnty'];
					if($sweing_qty_inAndOut!=0)
					{
						$lab_test=$commercial=$bh_commission=$currier=$certificate=$po_qty=$foregin=$local=0;
						$po_qty=$job_array[$poIds]['po_qty'];
						
						$lab_test=$other_costing_arr[$poIds]['lab_test'];
						$commercial=$commercial_costing_arr[$poIds];
						$foregin=$commission_costing_arr[$poIds][1];
						$local=$commission_costing_arr[$poIds][2];
						$bh_commission=$foregin+$local;
						$currier=$other_costing_arr[$poIds]['currier_pre_cost'];
						$certificate=$other_costing_arr[$poIds]['certificate_pre_cost'];
						
						$lab_test_cost+=($lab_test/$po_qty)*$sweing_qty_inAndOut;
						$commercial_cost+=($commercial/$po_qty)*$sweing_qty_inAndOut;
						$bh_commission_cost+=($bh_commission/$po_qty)*$sweing_qty_inAndOut;
						$currier_cost+=($currier/$po_qty)*$sweing_qty_inAndOut;
						$certificate_cost+=($certificate/$po_qty)*$sweing_qty_inAndOut;
					}
				}
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="35"><? echo $i; ?></td>
					<td width="90"><? echo $newdate; $date=$date_all; //change_date_format($date_all); ?></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'grey_receive_qnty')"><?  echo number_format($prod_date[$newdate]['kniting_qnty'],2); ?></a><? $total_knit+=$prod_date[$newdate]['kniting_qnty']; //echo $po_id;  ?></td>
					<td width="80" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'finish_receive_qnty')"> <? echo number_format($prod_date[$newdate]['finishing_qnty'],2); ?></a><? $total_finishing+=$prod_date[$newdate]['finishing_qnty'];?></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'printreceived')"><? echo number_format($prod_date[$newdate]['printing_qnty'],2); ?></a> <? $total_print+=$prod_date[$newdate]['printing_qnty']; if($val['printing_qnty']>0) $printing++;  ?></td>
					<td width="80" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'emb_qnty')"><? echo number_format($prod_date[$newdate]['emb_qnty'],2); ?></a><? $total_emb+=$prod_date[$newdate]['emb_qnty']; if($prod_date[$newdate]['emb_qnty']>0) $embing++; ?></td>
                    
					<td width="80" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'cutting_inhouse')"><? echo number_format($prod_date[$newdate]['cutting_qnty_inhouse'],2); ?></a><? $total_cutting_inhouse+=$prod_date[$newdate]['cutting_qnty_inhouse']; if($prod_date[$newdate]['cutting_qnty_inhouse']>0) $cuting_in++; ?></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'cutting_subcontract')"><? echo number_format($prod_date[$newdate]['cutting_qnty_outbound'],2); ?></a><? $total_cutting_outbound+=$prod_date[$newdate]['cutting_qnty_outbound']; if($prod_date[$newdate]['cutting_qnty_outbound']>0) $cuting_out++; ?></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'cutting')"><? echo number_format($prod_date[$newdate]['cutting_qnty'],2); ?></a><? $total_cutting+=$prod_date[$newdate]['cutting_qnty']; if($prod_date[$newdate]['cutting_qnty']>0) $cuting++; ?></td>
                    
                    <td width="80" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'sewingout_inhouse')"><? echo number_format($prod_date[$newdate]['sewingout_qnty_inhouse'],2); ?></a><?  $total_sew_inhouse+=$prod_date[$newdate]['sewingout_qnty_inhouse']; if($prod_date[$newdate]['sewingout_qnty_inhouse']>0) $sewing_in++; ?></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'sewingout_subcontract')"><? echo number_format($prod_date[$newdate]['sewingout_qnty_outbound'],2); ?></a><? $total_sew_outbound+=$prod_date[$newdate]['sewingout_qnty_outbound']; if($prod_date[$newdate]['sewingout_qnty_outbound']>0) $sewing_out++; ?></td>
					<td width="80" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'sewingout')"><? echo number_format($prod_date[$newdate]['sewing_qnty'],2); ?></a><? $total_sew+=$prod_date[$newdate]['sewing_qnty']; if($prod_date[$newdate]['sewing_qnty']>0) $sewing++; ?></td>
					
                    <td width="80" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'finish_inhouse')"><? echo number_format($prod_date[$newdate]['finish_qnty_inhouse'],2); ?></a><? $total_finishg_inhouse+=$prod_date[$newdate]['finish_qnty_inhouse']; if($prod_date[$newdate]['finish_qnty_inhouse']>0) $finish_in++; ?></td>
					<td width="80" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'finish_subcontract')"><? echo number_format($prod_date[$newdate]['finish_qnty_outbound'],2); ?></a><? $total_finish_outbound+=$prod_date[$newdate]['finish_qnty_outbound']; if($prod_date[$newdate]['finish_qnty_outbound']>0) $finish_out++; ?></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'finish')"><? echo number_format($prod_date[$newdate]['finish_qnty'],2); ?></a><? $total_finish+=$prod_date[$newdate]['finish_qnty']; if($prod_date[$newdate]['finish_qnty']>0) $finish++; ?></td>
                    <td width="80" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'carton')"><? echo number_format($prod_date[$newdate]['carton_qty'],2); ?></a><? $total_carton+=$prod_date[$newdate]['carton_qty']; if($prod_date[$newdate]['carton_qty']>0) $carton++; ?></td>
                    
                    <td width="100" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'ord_sew_inhouse')"><? $swing_val_inhouse=$prod_date[$newdate]['sewingout_value_inhouse']; echo number_format($swing_val_inhouse,2);  $total_sewin_order_value+=$swing_val_inhouse; if($swing_val_inhouse>0) $ord_in++; ?></a></td>
                    <td width="80" align="right"><?
					$swing_val_outbound=$prod_date[$newdate]['sewingout_value_outbound']; echo number_format($swing_val_outbound,2);  $total_sewout_order_value+=$swing_val_outbound; if($swing_val_outbound>0) $ord_out++; ?></td>
                    
                    <td width="100" align="right"><? echo number_format(($swing_val_inhouse+$swing_val_outbound),2);  $total_sew_order_value+=($swing_val_inhouse+$swing_val_outbound); if(($swing_val_inhouse+$swing_val_outbound)>0) $ord_tot++; ?></td>
                     <td width="100" align="right"><? $swing_val_availble=$tsmvArr[$newdate]['smv']/60; echo number_format($swing_val_availble,2);  $total_swing_val_availble_value+=$swing_val_availble; ?></td>
                     
                    <td width="100" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'smv_sewing_popup',1)"><? $swing_val_produced=$produce_qty; echo number_format($swing_val_produced,2);  $total_sewout_order_value_produced+=$swing_val_produced; ?></a></td>
                    
                    <td width="100" align="right"><? $sewing_out_bound=($prod_date[$newdate]['sewingout_qnty_outbound_pcs'])/60; $total_sewing_out_bound+=$sewing_out_bound; ?><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'smv_sewing_popup',3)"><? echo number_format($sewing_out_bound,2);?></a></td>
                    
                    <td width="100" align="right"><? echo number_format(($effiecy_aff_perc),2);  $total_effiecy_aff_perc+=$effiecy_aff_perc; ?></td>

                    <td width="100" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'ex_factory_qty_popup')"><? $ex_factory_qty=$prod_date[$newdate]['ex_factory']; echo number_format($ex_factory_qty,2);  $total_ex_factory_qty+=$ex_factory_qty; if($ex_factory_qty>0) $ex_fac++; ?></a></td>
                    <td width="80" align="right"><? $ex_factory_value=$prod_date[$newdate]['ex_factory_val']; echo number_format($ex_factory_value,2);  $total_ex_factory_value+=$ex_factory_value; if($ex_factory_value>0) $ex_val++; ?></td>
                    <td width="100" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'cm_value_popup')"><? $cm_value_in=$prod_date[$newdate]['cm_value_in']; echo number_format($cm_value_in,2); $total_cm_value_in+=$cm_value_in; if($cm_value_in>0) $cm_val_in++; ?></a>
                    </td>
                    <td width="100" align="right"><a href="##" onclick="openmypage2(<? echo "'".$date."'"  ?>,<? echo "'".$cbo_company."'"  ?>,<? echo "'".$po_id."'"  ?>,<? echo "'".str_replace("'","",$cbo_location)."'"  ?>,'cm_value_popup')"><? $cm_value_out=$prod_date[$newdate]['cm_value_out']; echo number_format($cm_value_out,2); $total_cm_value_out+=$cm_value_out; if($cm_value_out>0) $cm_val_out++; ?></a>
                    </td>
                    <td width="80" align="right"><? echo number_format($lab_test_cost,2);  $total_lab_test_cost+=$lab_test_cost; ?></td>
                    <td width="80" align="right"><? echo number_format($commercial_cost,2);  $total_commercial_cost+=$commercial_cost; ?></td>
                    <td width="80" align="right"><? echo number_format($bh_commission_cost,2);  $total_bh_commission_cost+=$bh_commission_cost; ?></td>
                    <td width="80" align="right"><? echo number_format($currier_cost,2); $total_currier_cost+=$currier_cost; ?></td>
                    <td align="right"><? echo number_format($certificate_cost,2); $total_certificate_cost+=$certificate_cost; ?></td>
				</tr>
			<?
		$i++;
	}
	?>
    </table>
    <table width="2830px " border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
        <tfoot>
            <tr>
                <th width="35">&nbsp;</th>
                <th width="90">Total</th>
                <th width="80"><? echo number_format($total_knit,2); ?></th>
                <th width="80"><? echo number_format($total_finishing,2); ?></th>
                <th width="80"><? echo number_format($total_print,2); ?></th>
                <th width="80"><? echo number_format($total_emb,2); ?></th>
                <th width="80"><? echo number_format($total_cutting_inhouse,2); ?></th>
                <th width="80"><? echo number_format($total_cutting_outbound,2); ?></th>
                <th width="80"><? echo number_format($total_cutting,2); ?></th>
                <th width="80"><? echo number_format($total_sew_inhouse,2); ?></th>
                <th width="80"><? echo number_format($total_sew_outbound,2); ?></th>
                <th width="80"><? echo number_format($total_sew,2); ?></th>
                <th width="80"><? echo number_format($total_finishg_inhouse,2); ?></th>
                <th width="80"><? echo number_format($total_finish_outbound,2); ?></th>
                <th width="80"><? echo number_format($total_finish,2); ?></th>
                <th width="80"><? echo number_format($total_carton,2); ?></th>
                <th width="100"><? echo number_format($total_sewin_order_value,2); ?></th>
                <th width="80"><? echo number_format($total_sewout_order_value,2); ?></th>
                <th width="100"><? echo number_format($total_sew_order_value,2); ?></th>
                
                <th width="100"><? echo number_format($total_swing_val_availble_value,2); ?></th>
                <th width="100"><? echo number_format($total_sewout_order_value_produced,2); ?></th>
                <th width="100"><?  echo number_format($total_sewing_out_bound,2); ?></th>
                <th width="100"><?  //echo number_format($total_effiecy_aff_perc,2); ?></th>
                
                <th width="100"><? echo number_format($total_ex_factory_qty,2); ?></th>
                <th width="80"><? echo number_format($total_ex_factory_value,2); ?></th>
                <th width="100"><? echo number_format($total_cm_value_in,2); ?></th>
                <th width="100"><? echo number_format($total_cm_value_out,2); ?></th>
                
                <th width="80"><? echo number_format($total_lab_test_cost,2); ?></th>
                <th width="80"><? echo number_format($total_commercial_cost,2); ?></th>
                <th width="80"><? echo number_format($total_bh_commission_cost,2); ?></th>
                <th width="80"><? echo number_format($total_currier_cost,2); ?></th>
                <th><? echo number_format($total_certificate_cost,2); ?></th>
            </tr>
            <tr>
                <th width="35" >&nbsp;</th>
                <th width="90" >Avg.</th>
                <th width="80" ><? echo number_format($total_knit/$count_knit,2); ?></th>
                <th width="80" ><? echo number_format($total_finishing/$count_finish,2); ?></th>
                <th width="80" ><? echo number_format($total_print/$printing,2); ?></th>
                <th width="80" ><? echo number_format($total_emb/$embing,2); ?></th>
                <th width="80"><? echo number_format($total_cutting_inhouse/$cuting_in,2); ?></th>
                <th width="80"><? echo number_format($total_cutting_outbound/$cuting_out,2); ?></th>
                <th width="80"><? echo number_format($total_cutting/ $cuting,2); ?></th>
                <th width="80"><? echo number_format($total_sew_inhouse/$sewing_in,2); ?></th>
                <th width="80"><? echo number_format($total_sew_outbound/$sewing_out,2); ?></th>
                <th width="80"><? echo number_format($total_sew/$sewing,2); ?></th>
                <th width="80"><? echo number_format($total_finishg_inhouse/$finish_in,2); ?></th>
                <th width="80"><? echo number_format($total_finish_outbound/$finish_out,2); ?></th>
                <th width="80"><? echo number_format($total_finish/$finish,2); ?></th>
                <th width="80"><? echo number_format($total_carton/$carton,2); ?></th>
                <th width="100"><? echo number_format($total_sewin_order_value/$ord_in,2); ?></th>
                <th width="80"><? echo number_format($total_sewout_order_value/$ord_out,2); ?></th>
                <th width="100"><? echo number_format($total_sew_order_value/$ord_tot,2); ?></th>
                <th width="100"><? //echo number_format($total_sewin_order_value/$ord_in,2); ?></th>
                <th width="100"><? //echo number_format($total_sewout_order_value/$ord_out,2); ?></th>
                <th width="100"><? //echo number_format($total_sew_order_value/$ord_tot,2); ?></th>
                <th width="100"><? //echo number_format($total_sew_order_value/$ord_tot,2); ?></th>
                <th width="100"><? echo number_format($total_ex_factory_qty/$ex_fac,2); ?></th>
                <th width="80"><? echo number_format($total_ex_factory_value/$ex_val,2); ?></th>
                <th width="100"><? echo number_format($total_cm_value_in/$cm_val_in,2); ?></th>
                <th width="100"><? echo number_format($total_cm_value_out/$cm_val_out,2); ?></th>
                
                <th width="80"><? //echo number_format($total_sewin_order_value/$ord_in,2); ?></th>
                <th width="80"><? //echo number_format($total_sewout_order_value/$ord_out,2); ?></th>
                <th width="80"><? //echo number_format($total_sew_order_value/$ord_tot,2); ?></th>
                <th width="80"><? //echo number_format($total_sew_order_value/$ord_tot,2); ?></th>
                <th width=""><? //echo number_format($total_sew_order_value/$ord_tot,2); ?></th>
            </tr>
        </tfoot>
    </table>
    </div>
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

if($action=="grey_receive_qnty")
{
	extract($_REQUEST);
	echo load_html_head_contents("Production Report", "../../../../", 1, 1,$unicode,'','');	
	//echo $date." ".$po_id;die;//company_id
	?>
    <table border="1" class="rpt_table" rules="all" width="720">
        <thead>
            <tr>
                <th colspan="6"><b>Knitting <? echo change_date_format($date);  ?></b></th>
            </tr>
            <tr>
            	<th width="30">SL</th>
                <th width="70">Job</th>
                <th width="200">Po No</th>
                <th width="120">Buyer</th>
                <th width="120">Style</th>
                <th>Knitting Qty</th>
           </tr>
        </thead>
        <tbody>
    <?
		$order_array=array();
		$po_sql="select b.id, a.buyer_name, a.job_no_prefix_num, a.style_ref_no, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		$po_sql_result=sql_select($po_sql);
		foreach ($po_sql_result as $row)
		{
			$order_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$order_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
			$order_array[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
			$order_array[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
		}
		
		if(str_replace("'","",$location_id)==0){$location_con_rcv="";}else{$location_con_rcv=" and a.location_id=$location_id";}
		
        $total_grey_receive_qnty=0;
		if($db_type==0)
		{
			$date2=change_date_format($date,'yyyy-mm-dd');
		}
		else if($db_type==2)
		{	$date2=change_date_format($date,'','',1);
		}
		
		$knited_query="select a.receive_date as production_date, sum(c.quantity) as kniting_qnty, c.po_breakdown_id from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.company_id='$company_id' $location_con_rcv and a.receive_date='$date2' and a.entry_form=2 and c.entry_form=2 and c.trans_type=1 and a.item_category=13 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 group by a.receive_date, c.po_breakdown_id";
		
		$knited_query_nonOrder="select a.id, a.recv_number, a.buyer_id, a.receive_date as production_date, sum(b.grey_receive_qnty) as kniting_qnty 
		from inv_receive_master a, pro_grey_prod_entry_dtls b 
		where a.id=b.mst_id and a.company_id='$company_id' $location_con_rcv and a.receive_date='$date2' and a.entry_form=2 and a.item_category=13 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.booking_without_order=1 
		group by a.id, a.recv_number, a.buyer_id, a.receive_date";
		
		
		$knited_query_result=sql_select($knited_query);
		$knited_query_nonOrder_result=sql_select($knited_query_nonOrder);
        $buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
        $i=1;
        foreach ($knited_query_result as $row)  
        {
			if($order_array[$row[csf('po_breakdown_id')]]['po_number']){
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td><? echo $i; ?></td>
                <td><p><? echo $order_array[$row[csf('po_breakdown_id')]]['job']; ?>&nbsp;</p></td>
                <td><p><? echo $order_array[$row[csf('po_breakdown_id')]]['po_number']; ?>&nbsp;</p></td>
                <td><p><? echo $buyerArr[$order_array[$row[csf('po_breakdown_id')]]['buyer_name']]; ?>&nbsp;</p></td>
                <td><p><? echo $order_array[$row[csf('po_breakdown_id')]]['style_ref_no']; ?>&nbsp;</p></td>
                <td align="right"><? echo number_format($row[csf('kniting_qnty')],2); $total_grey_receive_qnty+=$row[csf('kniting_qnty')]; ?></td>
			</tr>
			<?
			$i++;
			}
        }//while ($row=mysql_fetch_array($company_sql)) 
		
		foreach ($knited_query_nonOrder_result as $row)  
        {
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td><? echo $i; ?></td>
                <td><p>&nbsp;</p></td>
                <td><p>Non Order</p></td>
                <td><? echo $buyerArr[$row[csf('buyer_id')]]; ?></td>
                <td><p><? echo $row[csf('recv_number')]; ?></p></td>
                <td align="right"><? echo number_format($row[csf('kniting_qnty')],2); $total_grey_receive_qnty+=$row[csf('kniting_qnty')]; ?></td>
			</tr>
			<?
			$i++;
        }//while ($row=mysql_fetch_array($company_sql))
        ?>
        </tbody>
        <tfoot>
            <th colspan="5">Total</th>
            <th width="90px"><? echo number_format($total_grey_receive_qnty,2) ?></th>
        </tfoot>
    </table>
	<?
    exit();	
}

if($action=="finish_receive_qnty")
{
	extract($_REQUEST);
	echo load_html_head_contents("Production Report", "../../../../", 1, 1,$unicode,'','');	
	//echo $date." ".$po_id;die;//echo $date;//company_id
	?>
    <table border="1" class="rpt_table" rules="all" width="720">
        <thead>
            <tr>
                <th colspan="6"><b>Finishing <? echo change_date_format($date);  ?></b></th>
            </tr>
            <tr>
            	<th width="30">SL</th>
                <th width="70">Job</th>
                <th width="200">Po No</th>
                <th width="120">Buyer</th>
                <th width="120">Style</th>
                <th>Finishing Qty</th>
           </tr>
        </thead>
    <?
		$order_array=array();
		$po_sql="select b.id, a.buyer_name, a.job_no_prefix_num, a.style_ref_no, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
		$po_sql_result=sql_select($po_sql);
		foreach ($po_sql_result as $row)
		{
			$order_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$order_array[$row[csf('id')]]['po_number']=$row[csf('po_number')];
			$order_array[$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
			$order_array[$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
		}        
		if(str_replace("'","",$location_id)==0){$location_con_rcv="";}else{$location_con_rcv=" and a.location_id=$location_id";}
		$total_finish_qnty=0;
		if($db_type==0)
		{
			$date2=change_date_format($date,'yyyy-mm-dd');
		}
		else if($db_type==2)
		{
			$date2=change_date_format($date,'','',1);
		}
		
		$finish_query="select a.receive_date as production_date, sum(c.quantity) as finishing_qnty, c.po_breakdown_id from  inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.company_id='$company_id' $location_con_rcv and a.receive_date='$date2' and a.entry_form=7 and a.item_category=2 and c.entry_form=7 and c.trans_type=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by a.receive_date, c.po_breakdown_id";
		
		$finish_query_nonOrder="select a.id, a.recv_number, a.buyer_id, a.receive_date as production_date, sum(b.cons_quantity) as finishing_qnty 
		from  inv_receive_master a,  inv_transaction b, pro_batch_create_mst c 
		where a.id=b.mst_id and b.pi_wo_batch_no=c.id and a.company_id='$company_id' $location_con_rcv and a.receive_date='$date2' and a.entry_form=7 and a.item_category=2 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.booking_without_order=1  and a.receive_basis=5
		group by a.id, a.recv_number, a.buyer_id, a.receive_date";
		
		
		$finish_query_result=sql_select($finish_query);
		$finish_query_nonOrder_result=sql_select($finish_query_nonOrder);
        $buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
        $i=1;
        foreach ($finish_query_result as $row)  
        {
			if($order_array[$row[csf('po_breakdown_id')]]['po_number']){
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td><? echo $i; ?></td>
                <td><p><? echo $order_array[$row[csf('po_breakdown_id')]]['job']; ?>&nbsp;</p></td>
                <td><p><? echo $order_array[$row[csf('po_breakdown_id')]]['po_number']; ?>&nbsp;</p></td>
                <td><p><? echo $buyerArr[$order_array[$row[csf('po_breakdown_id')]]['buyer_name']]; ?>&nbsp;</p></td>
                <td><p><? echo $order_array[$row[csf('po_breakdown_id')]]['style_ref_no']; ?>&nbsp;</p></td>
                <td align="right"><? echo number_format($row[csf('finishing_qnty')],2); $total_finish_qnty+=$row[csf('finishing_qnty')]; ?></td>
			</tr>
			<?
			$i++;
			}
        }
		
		foreach ($finish_query_nonOrder_result as $row)  
        {
			
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td><? echo $i; ?></td>
                <td><p>&nbsp;</p></td>
                <td><p>Non Order&nbsp;</p></td>
                <td><p><? echo $buyerArr[$row[csf('buyer_id')]]; ?>&nbsp;</p></td>
                <td><p><? echo $row[csf('recv_number')]; ?>&nbsp;</p></td>
                <td align="right"><? echo number_format($row[csf('finishing_qnty')],2); $total_finish_qnty+=$row[csf('finishing_qnty')]; ?></td>
			</tr>
			<?
			$i++;
        }
        ?>
        <tfoot>
            <th colspan="5">Total</th>
            <th width="90px"><? echo number_format($total_finish_qnty,2) ?></th>
        </tfoot>
    </table>
	<?
    exit();	
}

if($action=="printreceived")
{
	extract($_REQUEST);
	echo load_html_head_contents("Production Report", "../../../../", 1, 1,$unicode,'','');	
	//echo $date;//company_id
	?>
    <table border="1" class="rpt_table" rules="all" width="720">
        <thead>
            <tr>
                <th colspan="6"><b>Printing (<? echo change_date_format($date);  ?>)</b></th>
            </tr>
            <tr>
            	<th width="30px">SL</th>
                <th width="70px">Job</th>
                <th width="100px">Po No</th>
                <th width="100px">Buyer</th>
                <th width="120px">Style</th>
                <th width="90px">Printing Qty</th>
           </tr>
        </thead>
    <?
		$po_array=array();
		$query_po_break_down=sql_select("select a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach ($query_po_break_down as $row)
		{
			$po_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$po_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['po']=$row[csf('po_number')];
		}
		if(str_replace("'","",$location_id)==0){$location_con="";}else{$location_con=" and location=$location_id";}

        $total_printing_qnty=0;
		if($db_type==0)
		{
			$printing_query="select id, sum(production_quantity) as production_quantity, production_date, po_break_down_id from pro_garments_production_mst where company_id like '$company_id' $location_con and production_date='$date' and production_type=3 and embel_name=1 and status_active=1 and is_deleted=0 ";
		}
		else if($db_type==2)
		{
			$date2=change_date_format($date,'','',1);
			$printing_query="select sum(production_quantity) as production_quantity, po_break_down_id from pro_garments_production_mst where company_id like '$company_id' $location_con and production_date='$date2' and production_type=3 and embel_name=1 and status_active=1 and is_deleted=0  group by po_break_down_id";
		}
	
		$printing_query_result=sql_select($printing_query);
        $buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
        $i=1;
        foreach ($printing_query_result as $row)  
        {
			if($po_array[$row[csf('po_break_down_id')]]['po']){
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30px"><? echo $i; ?></td>
                <td width="70px"><? echo $po_array[$row[csf('po_break_down_id')]]['job']; ?></td>
                <td width="100px"><? echo $po_array[$row[csf('po_break_down_id')]]['po']; ?></td>
                <td width="100px"><? echo $buyerArr[$po_array[$row[csf('po_break_down_id')]]['buyer']]; ?></td>
                <td width="120px"><? echo $po_array[$row[csf('po_break_down_id')]]['style']; ?></td>
                <td width="90px" align="right"><? echo number_format($row[csf('production_quantity')],2); $total_printing_qnty+=$row[csf('production_quantity')]; ?></td>
			</tr>
			<?
			$i++;
			}
        }
        ?>
        <tfoot>
            <th colspan="5">Total</th>
            <th width="90px"><? echo number_format($total_printing_qnty,2) ?></th>
        </tfoot>
    </table>
	<?
    exit();	
}

if($action=="emb_qnty")
{
	extract($_REQUEST);
	echo load_html_head_contents("Production Report", "../../../../", 1, 1,$unicode,'','');	
	
	?>
    <table border="1" class="rpt_table" rules="all" width="720">
        <thead>
            <tr>
                <th colspan="6"><b>Embellishment (<? echo change_date_format($date);  ?>)</b></th>
            </tr>
            <tr>
            	<th width="30px">SL</th>
                <th width="70px">Job</th>
                <th width="100px">Po No</th>
                <th width="100px">Buyer</th>
                <th width="120px">Style</th>
                <th width="90px">Embellishment Qty</th>
           </tr>
        </thead>
    <?
		$po_array=array();
		$query_po_break_down=sql_select("select a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach ($query_po_break_down as $row)
		{
			$po_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$po_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['po']=$row[csf('po_number')];
		}
		
		if(str_replace("'","",$location_id)==0){$location_con="";}else{$location_con=" and location=$location_id";}
		
        $total_embellishment_qnty=0;
		if($db_type==0)
		{
			$embellishment_query="select id, sum(production_quantity) as production_quantity, production_date, po_break_down_id from pro_garments_production_mst where company_id like '$company_id' $location_con and production_date='$date' and production_type=3 and embel_name=2 and status_active=1 and is_deleted=0 ";
		}
		else if($db_type==2)
		{
			$date2=change_date_format($date,'','',1);
			$embellishment_query="select  sum(production_quantity) as production_quantity, po_break_down_id from pro_garments_production_mst where company_id like '$company_id' and production_date='$date2' $location_con and production_type=3 and embel_name=2 and status_active=1 and is_deleted=0  group by po_break_down_id";
		}
		
		$embellishment_query_result=sql_select($embellishment_query);
        $buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
        $i=1;
        foreach ($embellishment_query_result as $row)  
        {
			if($po_array[$row[csf('po_break_down_id')]]['po']){
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30px"><? echo $i; ?></td>
                <td width="70px"><? echo $po_array[$row[csf('po_break_down_id')]]['job']; ?></td>
                <td width="100px"><? echo $po_array[$row[csf('po_break_down_id')]]['po']; ?></td>
                <td width="100px"><? echo $buyerArr[$po_array[$row[csf('po_break_down_id')]]['buyer']]; ?></td>
                <td width="120px"><? echo $po_array[$row[csf('po_break_down_id')]]['style']; ?></td>
                <td width="90px" align="right"><? echo number_format($row[csf('production_quantity')],2); $total_embellishment_qnty+=$row[csf('production_quantity')]; ?></td>
			</tr>
			<?
			$i++;
		 	}
        }
        ?>
        <tfoot>
            <th colspan="5">Total</th>
            <th width="90px"><? echo number_format($total_embellishment_qnty,2) ?></th>
        </tfoot>
    </table>
	<?
    exit();	
}

if($action=="cutting_inhouse")
{
	extract($_REQUEST);
	echo load_html_head_contents("Production Report", "../../../../", 1, 1,$unicode,'','');	
	//echo $date;//company_id
	?>
    <table border="1" class="rpt_table" rules="all" width="720">
        <thead>
            <tr>
                <th colspan="6"><b>Cutting In House (<? echo change_date_format($date);  ?>)</b></th>
            </tr>
            <tr>
            	<th width="30px">SL</th>
                <th width="70px">Job</th>
                <th width="100px">Po Number</th>
                <th width="100px">Buyer</th>
                <th width="120px">Style</th>
                <th width="90px">Cutting Qty</th>
           </tr>
        </thead>
    <?
		$po_array=array();
		$query_po_break_down=sql_select("select a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach ($query_po_break_down as $row)
		{
			$po_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$po_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['po']=$row[csf('po_number')];
		}
		
		if(str_replace("'","",$location_id)==0){$location_con="";}else{$location_con=" and location=$location_id";}
        $total_cutting_qnty=0;
		if($db_type==0)
		{
			$cutting_query="select id, sum(production_quantity) as production_quantity, production_date, po_break_down_id from pro_garments_production_mst where company_id like '$company_id' $location_con and production_date='$date' and production_type=1 and production_source=1 and status_active=1 and is_deleted=0 ";
		}
		else if($db_type==2)
		{
			$date2=change_date_format($date,'','',1);
			$cutting_query="select sum(production_quantity) as production_quantity, po_break_down_id from pro_garments_production_mst where company_id like '$company_id' $location_con and production_date='$date2' and production_type=1 and production_source=1 and status_active=1 and is_deleted=0 group by po_break_down_id";
		}
		//echo $printing_query;
		$cutting_query_result=sql_select($cutting_query);
        $buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
        $i=1;
        foreach ($cutting_query_result as $row)  
        {
			if($po_array[$row[csf('po_break_down_id')]]['po']){
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30px"><? echo $i; ?></td>
                <td width="70px"><? echo $po_array[$row[csf('po_break_down_id')]]['job']; ?></td>
                <td width="100px"><? echo $po_array[$row[csf('po_break_down_id')]]['po']; ?></td>
                <td width="100px"><? echo $buyerArr[$po_array[$row[csf('po_break_down_id')]]['buyer']]; ?></td>
                <td width="120px"><? echo $po_array[$row[csf('po_break_down_id')]]['style']; ?></td>
                <td width="90px" align="right"><? echo number_format($row[csf('production_quantity')],2); $total_cutting_qnty+=$row[csf('production_quantity')]; ?></td>
			</tr>
			<?
			$i++;
			}
        }
        ?>
        <tfoot>
            <th colspan="5">Total</th>
            <th width="90px"><? echo number_format($total_cutting_qnty,2) ?></th>
        </tfoot>
    </table>
	<?
    exit();	
}

if($action=="cutting_subcontract")
{
	extract($_REQUEST);
	echo load_html_head_contents("Production Report", "../../../../", 1, 1,$unicode,'','');	
	//echo $date;//company_id
	?>
    <table border="1" class="rpt_table" rules="all" width="720">
        <thead>
            <tr>
                <th colspan="6"><b>Cutting Sub-Contract (<? echo change_date_format($date);  ?>)</b></th>
            </tr>
            <tr>
            	<th width="30px">SL</th>
                <th width="70px">Job</th>
                <th width="100px">Po Number</th>
                <th width="100px">Buyer</th>
                <th width="120px">Style</th>
                <th width="90px">Cutting Qty</th>
           </tr>
        </thead>
    <?
		$po_array=array();
		$query_po_break_down=sql_select("select a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach ($query_po_break_down as $row)
		{
			$po_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$po_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['po']=$row[csf('po_number')];
		}
		
		if(str_replace("'","",$location_id)==0){$location_con="";}else{$location_con=" and location=$location_id";}
        $total_cutting_qnty=0;
		if($db_type==0)
		{
			$cutting_query="select id, sum(production_quantity) as production_quantity, production_date, po_break_down_id from pro_garments_production_mst where company_id like '$company_id' $location_con and production_date='$date' and production_type=1 and production_source=3 and status_active=1 and is_deleted=0 ";
		}
		else if($db_type==2)
		{
			$date2=change_date_format($date,'','',1);
			$cutting_query="select sum(production_quantity) as production_quantity,po_break_down_id from pro_garments_production_mst where company_id like '$company_id' $location_con and production_date='$date2' and production_type=1 and production_source=3 and status_active=1 and is_deleted=0 group by po_break_down_id";
		}
		//echo $cutting_query;
		$cutting_query_result=sql_select($cutting_query);
        $buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
        $i=1;
        foreach ($cutting_query_result as $row)  
        {
			if($po_array[$row[csf('po_break_down_id')]]['po']){
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30px"><? echo $i; ?></td>
                <td width="70px"><? echo $po_array[$row[csf('po_break_down_id')]]['job']; ?></td>
                <td width="100px"><? echo $po_array[$row[csf('po_break_down_id')]]['po']; ?></td>
                <td width="100px"><? echo $buyerArr[$po_array[$row[csf('po_break_down_id')]]['buyer']]; ?></td>
                <td width="120px"><? echo $po_array[$row[csf('po_break_down_id')]]['style']; ?></td>
                <td width="90px" align="right"><? echo number_format($row[csf('production_quantity')],2); $total_cutting_qnty+=$row[csf('production_quantity')]; ?></td>
			</tr>
			<?
			$i++;
			}
        }
        ?>
        <tfoot>
            <th colspan="5">Total</th>
            <th width="90px"><? echo number_format($total_cutting_qnty,2) ?></th>
        </tfoot>
    </table>
	<?
    exit();	
}

if($action=="cutting")
{
	extract($_REQUEST);
	echo load_html_head_contents("Production Report", "../../../../", 1, 1,$unicode,'','');	
	//echo $date;//company_id
	?>
    <table border="1" class="rpt_table" rules="all" width="720">
        <thead>
            <tr>
                <th colspan="6"><b>Cutting Total (<? echo change_date_format($date);  ?>)</b></th>
            </tr>
            <tr>
            	<th width="30px">SL</th>
                <th width="70px">Job</th>
                <th width="100px">Po Number</th>
                <th width="100px">Buyer</th>
                <th width="120px">Style</th>
                <th width="90px">Cutting Qty</th>
           </tr>
        </thead>
    <?
		$po_array=array();
		$query_po_break_down=sql_select("select a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach ($query_po_break_down as $row)
		{
			$po_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$po_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['po']=$row[csf('po_number')];
		}
		
		if(str_replace("'","",$location_id)==0){$location_con="";}else{$location_con=" and location=$location_id";}
        $total_cutting_qnty=0;
		if($db_type==0)
		{
			$cutting_query="select id, sum(production_quantity) as production_quantity, production_date, po_break_down_id from pro_garments_production_mst where company_id like '$company_id' $location_con and production_date='$date' and production_type=1 and status_active=1 and is_deleted=0 ";
		}
		else if($db_type==2)
		{
			$date2=change_date_format($date,'','',1);
			$cutting_query="select sum(production_quantity) as production_quantity, po_break_down_id from pro_garments_production_mst where company_id like '$company_id' $location_con and production_date='$date2' and production_type=1 and status_active=1 and is_deleted=0  group by po_break_down_id";
		}
		
		$cutting_query_result=sql_select($cutting_query);
        $buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
        $i=1;
        foreach ($cutting_query_result as $row)  
        {
			if($po_array[$row[csf('po_break_down_id')]]['po']){
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30px"><? echo $i; ?></td>
                <td width="70px"><? echo $po_array[$row[csf('po_break_down_id')]]['job']; ?></td>
                <td width="100px"><? echo $po_array[$row[csf('po_break_down_id')]]['po']; ?></td>
                <td width="100px"><? echo $buyerArr[$po_array[$row[csf('po_break_down_id')]]['buyer']]; ?></td>
                <td width="120px"><? echo $po_array[$row[csf('po_break_down_id')]]['style'] ?></td>
                <td width="90px" align="right"><? echo number_format($row[csf('production_quantity')],2); $total_cutting_qnty+=$row[csf('production_quantity')]; ?></td>
			</tr>
			<?
			$i++;
			}
        }
        ?>
        <tfoot>
            <th colspan="5">Total</th>
            <th width="90px"><? echo number_format($total_cutting_qnty,2) ?></th>
        </tfoot>
    </table>
	<?
    exit();	
}

if($action=="sewingout_subcontract")
{
	extract($_REQUEST);
	echo load_html_head_contents("Production Report", "../../../../", 1, 1,$unicode,'','');	
	
	?>
    <table border="1" class="rpt_table" rules="all" width="720">
        <thead>
            <tr>
                <th colspan="6"><b>Sewing Sub-Contract (<? echo change_date_format($date);  ?>)</b></th>
            </tr>
            <tr>
            	<th width="30px">SL</th>
                <th width="70px">Job</th>
                <th width="100px">Po No</th>
                <th width="100px">Buyer</th>
                <th width="120px">Style</th>
                <th width="90px">Sewing Qty</th>
           </tr>
        </thead>
    <?
		$po_array=array();
		$query_po_break_down=sql_select("select a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach ($query_po_break_down as $row)
		{
			$po_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$po_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['po']=$row[csf('po_number')];
		}
		
		if(str_replace("'","",$location_id)==0){$location_con="";}else{$location_con=" and location=$location_id";}
        $total_sewing_qnty=0;
		if($db_type==0)
		{
			$sewing_query="select id, sum(production_quantity) as production_quantity, production_date, po_break_down_id from pro_garments_production_mst where company_id like '$company_id' $location_con and production_date='$date' and production_type=5 and production_source=3 and status_active=1 and is_deleted=0 ";
		}
		else if($db_type==2)
		{
			$date2=change_date_format($date,'','',1);
			$sewing_query="select sum(production_quantity) as production_quantity, po_break_down_id from pro_garments_production_mst where company_id like '$company_id' $location_con and production_date='$date2' and production_type=5 and production_source=3 and status_active=1 and is_deleted=0 group by po_break_down_id";
		}
		//echo $sewing_query;
		$sewing_query_result=sql_select($sewing_query);
        $buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
        $i=1;
        foreach ($sewing_query_result as $row)  
        {
			if($po_array[$row[csf('po_break_down_id')]]['po']){
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30px"><? echo $i; ?></td>
                <td width="70px"><? echo $po_array[$row[csf('po_break_down_id')]]['job']; ?></td>
                <td width="100px"><? echo $po_array[$row[csf('po_break_down_id')]]['po']; ?></td>
                <td width="100px"><? echo $buyerArr[$po_array[$row[csf('po_break_down_id')]]['buyer']]; ?></td>
                <td width="120px"><? echo $po_array[$row[csf('po_break_down_id')]]['style']; ?></td>
                <td width="90px" align="right"><? echo number_format($row[csf('production_quantity')],2); $total_sewing_qnty+=$row[csf('production_quantity')]; ?></td>
			</tr>
			<?
			$i++;
			}
        }
        ?>
        <tfoot>
            <th colspan="5">Total</th>
            <th width="90px"><? echo number_format($total_sewing_qnty,2) ?></th>
        </tfoot>
    </table>
	<?
    exit();	
}

if($action=="sewingout_inhouse")
{
	extract($_REQUEST);
	echo load_html_head_contents("Production Report", "../../../../", 1, 1,$unicode,'','');	
	//echo $date;//company_id
	?>
    <table border="1" class="rpt_table" rules="all" width="720">
        <thead>
            <tr>
                <th colspan="6"><b>Sewing In House (<? echo change_date_format($date);  ?>)</b></th>
            </tr>
            <tr>
            	<th width="30px">SL</th>
                <th width="70px">Job</th>
                <th width="100px">Po No</th>
                <th width="100px">Buyer</th>
                <th width="120px">Style</th>
                <th width="90px">Sewing Qty</th>
           </tr>
        </thead>
    <?
		$po_array=array();
		$query_po_break_down=sql_select("select a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach ($query_po_break_down as $row)
		{
			$po_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$po_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['po']=$row[csf('po_number')];
		}
		
		if(str_replace("'","",$location_id)==0){$location_con="";}else{$location_con=" and location=$location_id";}
        $total_sewing_qnty=0;
		if($db_type==0)
		{
			$sewing_query="select id, sum(production_quantity) as production_quantity, production_date, po_break_down_id from pro_garments_production_mst where company_id like '$company_id' $location_con and production_date='$date' and production_type=5 and production_source=1 and status_active=1 and is_deleted=0 ";
		}
		elseif($db_type==2)
		{
			 $date2=change_date_format($date,'','',1);
			$sewing_query="select  sum(production_quantity) as production_quantity,  po_break_down_id from pro_garments_production_mst where company_id like '$company_id' $location_con and production_date='$date2' and production_type=5 and production_source=1 and status_active=1 and is_deleted=0  group by po_break_down_id";
		}
		
		$sewing_query_result=sql_select($sewing_query);
        $buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
        $i=1;
        foreach ($sewing_query_result as $row)  
        {
			if($po_array[$row[csf('po_break_down_id')]]['po']){
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30px"><? echo $i; ?></td>
                <td width="70px"><? echo $po_array[$row[csf('po_break_down_id')]]['job']; ?></td>
                <td width="100px"><? echo $po_array[$row[csf('po_break_down_id')]]['po']; ?></td>
                <td width="100px"><? echo $buyerArr[$po_array[$row[csf('po_break_down_id')]]['buyer']]; ?></td>
                <td width="120px"><? echo $po_array[$row[csf('po_break_down_id')]]['style']; ?></td>
                <td width="90px" align="right"><? echo number_format($row[csf('production_quantity')],2); $total_sewing_qnty+=$row[csf('production_quantity')]; ?></td>
			</tr>
			<?
			$i++;
			}
        }
        ?>
        <tfoot>
            <th colspan="5">Total</th>
            <th width="90px"><? echo number_format($total_sewing_qnty,2) ?></th>
        </tfoot>
    </table>
	<?
    exit();	
}

if($action=="sewingout")
{
	extract($_REQUEST);
	echo load_html_head_contents("Production Report", "../../../../", 1, 1,$unicode,'','');	
	//echo $date;//company_id
	?>
    <table border="1" class="rpt_table" rules="all" width="100%">
        <thead>
            <tr>
                <th colspan="6"><b>Sewing Total (<? echo change_date_format($date);  ?>)</b></th>
            </tr>
            <tr>
            	<th width="30px">SL</th>
                <th width="70px">Job</th>
                <th width="100px">Po No</th>
                <th width="100px">Buyer</th>
                <th width="120px">Style</th>
                <th width="90px">Sewing Qty</th>
           </tr>
        </thead>
    <?
		$po_array=array();
		$query_po_break_down=sql_select("select a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach ($query_po_break_down as $row)
		{
			$po_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$po_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['po']=$row[csf('po_number')];
		}
		
		if(str_replace("'","",$location_id)==0){$location_con="";}else{$location_con=" and location=$location_id";}
        $total_sewing_qnty=0;
		if($db_type==0)
		{
			$sewing_query="select id, sum(production_quantity) as production_quantity, production_date, po_break_down_id from pro_garments_production_mst where company_id like '$company_id' $location_con and production_date='$date' and production_type=5 and status_active=1 and is_deleted=0 ";
		}
		elseif($db_type==2)
		{
			$date2=change_date_format($date,'','',1);
			$sewing_query="select sum(production_quantity) as production_quantity,  po_break_down_id from pro_garments_production_mst where company_id like '$company_id' $location_con and production_date='$date2' and production_type=5 and status_active=1 and is_deleted=0 group by po_break_down_id ";
		}
		
		//echo $sewing_query;
		$sewing_query_result=sql_select($sewing_query);
        $buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
        $i=1;
        foreach ($sewing_query_result as $row)  
        {	if($po_array[$row[csf('po_break_down_id')]]['po']){
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30px"><? echo $i; ?></td>
                <td width="70px"><? echo $po_array[$row[csf('po_break_down_id')]]['job']; ?></td>
                <td width="100px"><? echo $po_array[$row[csf('po_break_down_id')]]['po']; ?></td>
                <td width="100px"><? echo $buyerArr[$po_array[$row[csf('po_break_down_id')]]['buyer']]; ?></td>
                <td width="120px"><? echo $po_array[$row[csf('po_break_down_id')]]['style']; ?></td>
                <td width="90px" align="right"><? echo number_format($row[csf('production_quantity')],2); $total_sewing_qnty+=$row[csf('production_quantity')]; ?></td>
			</tr>
			<?
			$i++;
			}
        }
        ?>
        <tfoot>
            <th colspan="5">Total</th>
            <th width="90px"><? echo number_format($total_sewing_qnty,2) ?></th>
        </tfoot>
    </table>
	<?
    exit();	
}

if($action=="finish_inhouse")
{
	extract($_REQUEST);
	echo load_html_head_contents("Production Report", "../../../../", 1, 1,$unicode,'','');	
	//echo $date;//company_id
	?>
    <table border="1" class="rpt_table" rules="all" width="720">
        <thead>
            <tr>
                <th colspan="6"><b>Finishing In House (<? echo change_date_format($date);  ?>)</b></th>
            </tr>
            <tr>
            	<th width="30px">SL</th>
                <th width="70px">Job</th>
                <th width="100px">Po No</th>
                <th width="100px">Buyer</th>
                <th width="120px">Style</th>
                <th width="90px">Finishing Qty</th>
           </tr>
        </thead>
    <?
		$po_array=array();
		$query_po_break_down=sql_select("select a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach ($query_po_break_down as $row)
		{
			$po_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$po_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['po']=$row[csf('po_number')];
		}
		
		if(str_replace("'","",$location_id)==0){$location_con="";}else{$location_con=" and location=$location_id";}
        $total_finishing_qnty=0;
		if($db_type==0)
		{
			$finishing_query="select id, sum(production_quantity) as production_quantity, production_date, po_break_down_id from pro_garments_production_mst where company_id like '$company_id' $location_con and production_date='$date' and production_type=8 and production_source=1 and status_active=1 and is_deleted=0 ";
		}
		elseif($db_type==2)
		{
			$date2=change_date_format($date,'','',1);
			$finishing_query="select id,sum(production_quantity) as production_quantity, po_break_down_id from pro_garments_production_mst where company_id like '$company_id' $location_con and production_date='$date2' and production_type=8 and production_source=1 and status_active=1 and is_deleted=0 group by id,po_break_down_id";
		}
		
		$finishing_query_result=sql_select($finishing_query);
        $buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
        $i=1;
        foreach ($finishing_query_result as $row)  
        {	if($po_array[$row[csf('po_break_down_id')]]['po']){
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30px"><? echo $i; ?></td>
                <td width="70px"><? echo $po_array[$row[csf('po_break_down_id')]]['job']; ?></td>
                <td width="100px"><? echo $po_array[$row[csf('po_break_down_id')]]['po']; ?></td>
                <td width="100px"><? echo $buyerArr[$po_array[$row[csf('po_break_down_id')]]['buyer']]; ?></td>
                <td width="120px"><? echo $po_array[$row[csf('po_break_down_id')]]['style']; ?></td>
                <td width="90px" align="right"><? echo number_format($row[csf('production_quantity')],2); $total_finishing_qnty+=$row[csf('production_quantity')]; ?></td>
			</tr>
			<?
			$i++;
			}
        }
        ?>
        <tfoot>
            <th colspan="5">Total</th>
            <th width="90px"><? echo number_format($total_finishing_qnty,2) ?></th>
        </tfoot>
    </table>
	<?
    exit();	
}

if($action=="finish_subcontract")
{
	extract($_REQUEST);
	echo load_html_head_contents("Production Report", "../../../../", 1, 1,$unicode,'','');	
	//echo $date;//company_id
	?>
    <table border="1" class="rpt_table" rules="all" width="100%">
        <thead>
            <tr>
                <th colspan="6"><b>Finishing Sub-Contract (<? echo change_date_format($date);  ?>)</b></th>
            </tr>
            <tr>
            	<th width="30px">SL</th>
                <th width="70px">Job</th>
                <th width="100px">Po No</th>
                <th width="100px">Buyer</th>
                <th width="120px">Style</th>
                <th width="90px">Finishing Qty</th>
           </tr>
        </thead>
    <?
		$po_array=array();
		$query_po_break_down=sql_select("select a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach ($query_po_break_down as $row)
		{
			$po_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$po_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['po']=$row[csf('po_number')];
		}
		
		if(str_replace("'","",$location_id)==0){$location_con="";}else{$location_con=" and location=$location_id";}
        $total_finishing_qnty=0;
		if($db_type==0)
		{
			$finishing_query="select id, sum(production_quantity) as production_quantity, production_date, po_break_down_id from pro_garments_production_mst where company_id like '$company_id' $location_con and production_date='$date' and production_type=8 and production_source=3 and status_active=1 and is_deleted=0 ";
		}
		elseif($db_type==2)
		{
			$date2=change_date_format($date,'','',1);
			$finishing_query="select  sum(production_quantity) as production_quantity, po_break_down_id from pro_garments_production_mst where company_id like '$company_id' $location_con and production_date='$date2' and production_type=8 and production_source=3 and status_active=1 and is_deleted=0  group by po_break_down_id";
		}
		//echo $sewing_query;
		$finishing_query_result=sql_select($finishing_query);
        $buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
        $i=1;
        foreach ($finishing_query_result as $row)  
        {	if($po_array[$row[csf('po_break_down_id')]]['po']){
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30px"><? echo $i; ?></td>
                <td width="70px"><? echo $po_array[$row[csf('po_break_down_id')]]['job']; ?></td>
                <td width="100px"><? echo $po_array[$row[csf('po_break_down_id')]]['po']; ?></td>
                <td width="100px"><? echo $buyerArr[$po_array[$row[csf('po_break_down_id')]]['buyer']]; ?></td>
                <td width="120px"><? echo $po_array[$row[csf('po_break_down_id')]]['style']; ?></td>
                <td width="90px" align="right"><? echo number_format($row[csf('production_quantity')],2); $total_finishing_qnty+=$row[csf('production_quantity')]; ?></td>
			</tr>
			<?
			$i++;
			}
        }
        ?>
        <tfoot>
            <th colspan="5">Total</th>
            <th width="90px"><? echo number_format($total_finishing_qnty,2) ?></th>
        </tfoot>
    </table>
	<?
    exit();	
}

if($action=="finish")
{
	extract($_REQUEST);
	echo load_html_head_contents("Production Report", "../../../../", 1, 1,$unicode,'','');	
	//echo $date;//company_id
	?>
    <table border="1" class="rpt_table" rules="all" width="720">
        <thead>
            <tr>
                <th colspan="6"><b>Finishing (<? echo change_date_format($date);  ?>)</b></th>
            </tr>
            <tr>
            	<th width="30px">SL</th>
                <th width="70px">Job</th>
                <th width="100px">Po Number</th>
                <th width="100px">Buyer</th>
                <th width="120px">Style</th>
                <th width="90px">Finishing Qty</th>
           </tr>
        </thead>
    <?
		$po_array=array();
		$query_po_break_down=sql_select("select a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach ($query_po_break_down as $row)
		{
			$po_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$po_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['po']=$row[csf('po_number')];
		}
		if(str_replace("'","",$location_id)==0){$location_con="";}else{$location_con=" and location=$location_id";}
        $total_finishing_qnty=0;
		if($db_type==0)
		{
			$finishing_query="select id, sum(production_quantity) as production_quantity, production_date, po_break_down_id from pro_garments_production_mst where company_id like '$company_id' $location_con and production_date='$date' and production_type=8 and status_active=1 and is_deleted=0 ";
		}
		elseif($db_type==2)
		{
			$date2=change_date_format($date,'','',1);
			$finishing_query="select  sum(production_quantity) as production_quantity,po_break_down_id from pro_garments_production_mst where company_id like '$company_id' $location_con and production_date='$date2' and production_type=8 and status_active=1 and is_deleted=0 group by po_break_down_id";
		}
		//echo $sewing_query;
		$finishing_query_result=sql_select($finishing_query);
        $buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
        $i=1;
        foreach ($finishing_query_result as $row)  
        {
			if($po_array[$row[csf('po_break_down_id')]]['po']){
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30px"><? echo $i; ?></td>
                <td width="70px"><? echo $po_array[$row[csf('po_break_down_id')]]['job']; ?></td>
                <td width="100px"><? echo $po_array[$row[csf('po_break_down_id')]]['po']; ?></td>
                <td width="100px"><? echo $buyerArr[$po_array[$row[csf('po_break_down_id')]]['buyer']]; ?></td>
                <td width="120px"><? echo $po_array[$row[csf('po_break_down_id')]]['style']; ?></td>
                <td width="90px" align="right"><? echo number_format($row[csf('production_quantity')],2); $total_finishing_qnty+=$row[csf('production_quantity')]; ?></td>
			</tr>
			<?
			$i++;
			}
        }
        ?>
        <tfoot>
            <th colspan="5">Total</th>
            <th width="90px"><? echo number_format($total_finishing_qnty,2) ?></th>
        </tfoot>
    </table>
	<?
    exit();	
}

if($action=="carton")
{
	extract($_REQUEST);
	echo load_html_head_contents("Production Report", "../../../../", 1, 1,$unicode,'','');	
	//echo $date;//company_id
	?>
    <table border="1" class="rpt_table" rules="all" width="720px">
        <thead>
            <tr>
                <th colspan="6"><b>Carton (<? echo change_date_format($date);  ?>)</b></th>
            </tr>
            <tr>
            	<th width="30px">SL</th>
                <th width="70px">Job</th>
                <th width="100px">Po Number</th>
                <th width="100px">Buyer</th>
                <th width="120px">Style</th>
                <th width="90px">Carton Qnty</th>
           </tr>
        </thead>
    <?
		$po_array=array();
		$query_po_break_down=sql_select("select a.job_no_prefix_num, a.buyer_name, a.style_ref_no, b.id, b.po_number from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach ($query_po_break_down as $row)
		{
			$po_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$po_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['po']=$row[csf('po_number')];
		}
		
		if(str_replace("'","",$location_id)==0){$location_con="";}else{$location_con=" and location=$location_id";}
        $total_carton_qnty=0;
		if($db_type==0)
		{
			$carton_query="select id, sum(carton_qty) as production_quantity, production_date, po_break_down_id from pro_garments_production_mst where company_id like '$company_id' $location_con and production_date='$date' and production_type=8 and status_active=1 and is_deleted=0 ";
		}
		elseif($db_type==2)
		{
			$date2=change_date_format($date,'','',1);
			$carton_query="select  sum(carton_qty) as production_quantity, po_break_down_id from pro_garments_production_mst where company_id like '$company_id' $location_con and production_date='$date2' and production_type=8 and status_active=1 and is_deleted=0 group by po_break_down_id";
		}
		//echo $carton_query;
		$carton_query_result=sql_select($carton_query);
        $buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
        $i=1;
        foreach ($carton_query_result as $row)  
        {
			if($po_array[$row[csf('po_break_down_id')]]['po']){
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
			
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30px"><? echo $i; ?></td>
                <td width="70px"><? echo $po_array[$row[csf('po_break_down_id')]]['job']; ?></td>
                <td width="100px"><? echo $po_array[$row[csf('po_break_down_id')]]['po']; ?></td>
                <td width="100px"><? echo $buyerArr[$po_array[$row[csf('po_break_down_id')]]['buyer']]; ?></td>
                <td width="120px"><? echo $po_array[$row[csf('po_break_down_id')]]['style']; ?></td>
                <td width="90px" align="right"><? echo number_format($row[csf('production_quantity')],2); $total_carton_qnty+=$row[csf('production_quantity')]; ?></td>
			</tr>
			<?
			$i++;
			}
        }
        ?>
        <tfoot>
            <th colspan="5">Total</th>
            <th width="90px"><? echo number_format($total_carton_qnty,2) ?></th>
        </tfoot>
    </table>
	<?
    exit();	
}

if($action=="ex_factory_qty_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Ex-Factory Qty Popup", "../../../../", 1, 1,$unicode,'','');	
	//echo $date;//company_id
	?>
    <table border="1" class="rpt_table" rules="all" width="720px">
        <thead>
            <tr>
                <th colspan="8"><b>Ex-Factory Date (<? echo change_date_format($date); ?>)</b></th>
            </tr>
            <tr>
            	<th width="30px">SL</th>
                <th width="70px">Job</th>
                <th width="120px">Buyer</th>
                <th width="120px">Style</th>
                <th width="120px">Po Number</th>
                <th width="100px">Ex-Factory Qty</th>
                <th width="100px">Return Qty</th>
                <th>Ex-Factory Value</th>
           </tr>
        </thead>
    <?
        $buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
		
		$po_array=array();
		$query_po_break_down=sql_select("select a.job_no_prefix_num, a.buyer_name, a.style_ref_no, a.total_set_qnty, b.id, b.po_number, b.unit_price from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach ($query_po_break_down as $row)
		{
			$po_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$po_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['po']=$row[csf('po_number')];
			$po_array[$row[csf('id')]]['unit_price']=$row[csf('unit_price')];
			$po_array[$row[csf('id')]]['set_qnty']=$row[csf('total_set_qnty')];
		}
		
		if($db_type==0)
		{
			$date_cond=$date;
		}
		elseif($db_type==2)
		{
			$date_cond=change_date_format($date,'','',1);
		}
		
		if(str_replace("'","",$location_id)==0){$location_con="";}else{$location_con=" and location=$location_id";}
		
		$exfactory_query="select ex_factory_date, po_break_down_id, 
		sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as return_qnty 
		 from pro_ex_factory_mst where ex_factory_date='$date_cond' $location_con and status_active=1 and is_deleted=0 group by ex_factory_date, po_break_down_id";

		$exfactory_query_result=sql_select($exfactory_query); $i=1; //$total_exfactory_qnty=0;
        foreach ($exfactory_query_result as $row)  
        {
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$ex_factory_val=($row[csf("ex_factory_qnty")]-$row[csf("return_qnty")])*($po_array[$row[csf("po_break_down_id")]]['unit_price']/$po_array[$row[csf("po_break_down_id")]]['set_qnty']);
			
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30px"><? echo $i; ?></td>
                <td width="70px"><? echo $po_array[$row[csf('po_break_down_id')]]['job']; ?></td>
                <td width="120px"><? echo $buyerArr[$po_array[$row[csf('po_break_down_id')]]['buyer']]; ?></td>
                <td width="120px"><? echo $po_array[$row[csf('po_break_down_id')]]['style']; ?></td>
                <td width="120px"><? echo $po_array[$row[csf('po_break_down_id')]]['po'];; ?></td>
                <td width="100px" align="right"><? echo number_format($row[csf('ex_factory_qnty')],2); $total_ex_factory_qnty+=$row[csf('ex_factory_qnty')]; ?></td>
                  <td width="100px" align="right"><? echo number_format($row[csf('return_qnty')],2); $total_return_ex_factory_qnty+=$row[csf('return_qnty')]; ?></td>
                <td align="right"><? echo number_format($ex_factory_val,2); $total_exfactory_value+=$ex_factory_val; ?></td>
			</tr>
			<?
			$i++;
        }
        ?>
        <tfoot>
            <th colspan="5">Total</th>
            <th><? echo number_format($total_ex_factory_qnty,2) ?></th>
             <th><? echo number_format($total_return_ex_factory_qnty,2) ?></th>
            <th><? echo number_format($total_exfactory_value,2) ?></th>
           
        </tfoot>
    </table>
	<?
    exit();	
}

if($action=="cm_value_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("CM Value Popup", "../../../../", 1, 1,$unicode,'','');	
	//echo $date;//company_id
	?>
    <table border="1" class="rpt_table" rules="all" width="720px">
        <thead>
            <tr>
                <th colspan="8"><b>CM Value Popup (<? echo change_date_format($date); ?>)</b></th>
            </tr>
            <tr>
            	<th width="30px">SL</th>
                <th width="70px">Job</th>
                <th width="110px">Buyer</th>
                <th width="110px">Style</th>
                <th width="110px">Po Number</th>
                <th width="90px">Sewing Qty</th>
                <th width="90px">CM Per Pcs</th>
                <th>CM Value</th>
           </tr>
        </thead>
    <?
        $buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
		$costing_per_arr = return_library_array("select job_no, costing_per from wo_pre_cost_mst","job_no","costing_per"); 
		$tot_cost_arr = return_library_array("select job_no, cm_for_sipment_sche from wo_pre_cost_dtls","job_no","cm_for_sipment_sche"); 
		
		$po_array=array();
		$query_po_break_down=sql_select("select a.job_no, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, a.total_set_qnty, b.id, b.po_number, b.unit_price from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach ($query_po_break_down as $row)
		{
			$po_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
			$po_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$po_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['po']=$row[csf('po_number')];
			$po_array[$row[csf('id')]]['unit_price']=$row[csf('unit_price')];
			$po_array[$row[csf('id')]]['set_qnty']=$row[csf('total_set_qnty')];
		}
		
		if($db_type==0)
		{
			$date_cond=$date;
		}
		elseif($db_type==2)
		{
			$date_cond=change_date_format($date,'','',1);
		}
		if(str_replace("'","",$location_id)==0){$location_con="";}else{$location_con=" and location=$location_id";}
		$cm_query="SELECT production_date, po_break_down_id, sum(production_quantity) AS sewing_qnty from pro_garments_production_mst where company_id='$company_id' $location_con and production_date='$date_cond' and production_type =5 and is_deleted=0 and status_active=1 group by production_date, po_break_down_id";

		$cm_query_result=sql_select($cm_query); $i=1; //$total_exfactory_qnty=0;
        foreach ($cm_query_result as $row)  
        {
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$cm_val=$row[csf("sewing_qnty")]*($po_array[$row[csf("po_break_down_id")]]['unit_price']/$po_array[$row[csf("po_break_down_id")]]['set_qnty']);
			
			$costing_per=$costing_per_arr[$po_array[$row[csf('po_break_down_id')]]['job_no']];
			if($costing_per==1) $dzn_qnty=12;
			else if($costing_per==3) $dzn_qnty=12*2;
			else if($costing_per==4) $dzn_qnty=12*3;
			else if($costing_per==5) $dzn_qnty=12*4;
			else $dzn_qnty=1;
						
			$dzn_qnty=$dzn_qnty*$po_array[$row[csf("po_break_down_id")]]['set_qnty'];
			$cm_per_pcs=$tot_cost_arr[$po_array[$row[csf('po_break_down_id')]]['job_no']]/$dzn_qnty;
			$cm_value=($tot_cost_arr[$po_array[$row[csf('po_break_down_id')]]['job_no']]/$dzn_qnty)*$row[csf('sewing_qnty')];
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30px"><? echo $i; ?></td>
                <td width="70px"><? echo $po_array[$row[csf('po_break_down_id')]]['job']; ?></td>
                <td width="110px"><? echo $buyerArr[$po_array[$row[csf('po_break_down_id')]]['buyer']]; ?></td>
                <td width="110px"><? echo $po_array[$row[csf('po_break_down_id')]]['style']; ?></td>
                <td width="110px"><? echo $po_array[$row[csf('po_break_down_id')]]['po'];; ?></td>
                <td width="90px" align="right"><? echo number_format($row[csf('sewing_qnty')],2); $total_cm_qnty+=$row[csf('sewing_qnty')]; ?></td>
                <td width="90px" align="right"><? echo number_format($cm_per_pcs,2); $total_cm_per_pcs+=$cm_per_pcs; ?></td>
                <td align="right"><? echo number_format($cm_value,2); $total_cm_value+=$cm_value; ?></td>
			</tr>
			<?
			$i++;
        }
        ?>
        <tfoot>
            <th colspan="5">Total</th>
            <th><? echo number_format($total_cm_qnty,2) ?></th>
            <th><? //echo number_format($total_cm_per_pcs,2) ?></th>
            <th><? echo number_format($total_cm_value,2) ?></th>
        </tfoot>
    </table>
	<?
    exit();	
}

if($action=="ord_sew_inhouse")
{
	extract($_REQUEST);
	echo load_html_head_contents("Inhouse Order Value (On Sewing Qty) Popup", "../../../../", 1, 1,$unicode,'','');	
	//echo $date;//company_id
	?>
    <table border="1" class="rpt_table" rules="all" width="720px">
        <thead>
            <tr>
                <th colspan="8"><b>Inhouse Order Value (On Sewing Qty) Popup (<? echo change_date_format($date); ?>)</b></th>
            </tr>
            <tr>
            	<th width="30px">SL</th>
                <th width="70px">Job</th>
                <th width="110px">Buyer</th>
                <th width="110px">Style</th>
                <th width="110px">Po Number</th>
                <th width="90px">Sewing Qty</th>
                <th width="90px">Unite Price</th>
                <th>Sewing Ord. Qty</th>
           </tr>
        </thead>
    <?
        $buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
		$costing_per_arr = return_library_array("select job_no, costing_per from wo_pre_cost_mst","job_no","costing_per"); 
		$tot_cost_arr = return_library_array("select job_no, cm_for_sipment_sche from wo_pre_cost_dtls","job_no","cm_for_sipment_sche"); 
		
		$po_array=array();
		$query_po_break_down=sql_select("select a.job_no, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, a.total_set_qnty, b.id, b.po_number, b.unit_price from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		foreach ($query_po_break_down as $row)
		{
			$po_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
			$po_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$po_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['po']=$row[csf('po_number')];
			$po_array[$row[csf('id')]]['unit_price']=$row[csf('unit_price')];
			$po_array[$row[csf('id')]]['set_qnty']=$row[csf('total_set_qnty')];
		}
		
		if($db_type==0)
		{
			$date_cond=$date;
		}
		elseif($db_type==2)
		{
			$date_cond=change_date_format($date,'','',1);
		}
		if(str_replace("'","",$location_id)==0){$location_con="";}else{$location_con=" and location=$location_id";}
		$ord_sew_inhouse_sql="SELECT production_date, po_break_down_id, sum(production_quantity) AS sewing_qnty from pro_garments_production_mst where company_id='$company_id' $location_con and production_date='$date_cond' and is_deleted=0 and production_type=5 and production_source=1 and status_active=1 group by production_date, po_break_down_id order by production_date asc";
			
		$ord_sew_inhouse_sql_result=sql_select($ord_sew_inhouse_sql); $i=1; //$total_exfactory_qnty=0;
        foreach ($ord_sew_inhouse_sql_result as $row)  
        {
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$ord_sew_val=$row[csf("sewing_qnty")]*$po_array[$row[csf("po_break_down_id")]]['unit_price'];
			
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30px"><? echo $i; ?></td>
                <td width="70px"><? echo $po_array[$row[csf('po_break_down_id')]]['job']; ?></td>
                <td width="110px"><? echo $buyerArr[$po_array[$row[csf('po_break_down_id')]]['buyer']]; ?></td>
                <td width="110px"><? echo $po_array[$row[csf('po_break_down_id')]]['style']; ?></td>
                <td width="110px"><? echo $po_array[$row[csf('po_break_down_id')]]['po'];; ?></td>
                <td width="90px" align="right"><? echo number_format($row[csf("sewing_qnty")],2); $total_sewing_qnty+=$row[csf("sewing_qnty")]; ?></td>
                <td width="90px" align="right"><? echo number_format($po_array[$row[csf("po_break_down_id")]]['unit_price'],2); $total_unit_price+=$po_array[$row[csf("po_break_down_id")]]['unit_price']; ?></td>
                <td align="right"><? echo number_format($ord_sew_val,2); $total_ord_sew_val+=$ord_sew_val; ?></td>
			</tr>
			<?
			$i++;
        }
        ?>
        <tfoot>
            <th colspan="5">Total</th>
            <th><? echo number_format($total_sewing_qnty,2) ?></th>
            <th><? //echo number_format($total_cm_per_pcs,2) ?></th>
            <th><? echo number_format($total_ord_sew_val,2) ?></th>
        </tfoot>
    </table>
	<?
    exit();	
}

if($action=="smv_sewing_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("SMV (On Sewing Qty) Popup", "../../../../", 1, 1,$unicode,'','');	
	//echo $sewing_source;//company_id
	?>
    <table border="1" class="rpt_table" rules="all" width="720px">
        <thead>
            <tr>
                <th colspan="10"><b>SMV (On Sewing Qty) Popup (<? echo change_date_format($date); ?>)</b></th>
            </tr>
            <tr>
            	<th width="30">SL</th>
                <th width="60">Job</th>
                <th width="50">SMV</th>
                <th width="110">Buyer</th>
                <th width="110">Style</th>
                <th width="100">Item</th>
                <th width="110">Po Number</th>
                <th width="90">Sewing Qty</th>
                <th width="90">SAH Produced</th>
                <th>Efficiency</th>
           </tr>
        </thead>
    <?
        $buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
		
		$smv_source=return_field_value("smv_source","variable_settings_production","company_name in ($company_id) and variable_list=25 and status_active=1 and is_deleted=0");
		//echo $smv_source;die;
		if($smv_source=="") $smv_source=0; else $smv_source=$smv_source;
		$item_smv_array=array();
		//$smv_source=2;
		if($smv_source==3)
		{
			$sql_item="select b.id, a.sam_style, a.gmts_item_id from ppl_gsd_entry_mst a, wo_po_break_down b where b.job_no_mst=a.po_job_no and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			$resultItem=sql_select($sql_item);
			foreach($resultItem as $itemData)
			{
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]=$itemData[csf('sam_style')];
			}
		}
		else
		{
			$sql_item="select b.id, a.set_break_down, c.gmts_item_id, c.set_item_ratio, c.smv_pcs, c.smv_pcs_precost from wo_po_details_master a, wo_po_break_down b, wo_po_details_mas_set_details c where b.job_no_mst=a.job_no and b.job_no_mst=c.job_no and a.company_name in ($company_id) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
			$resultItem=sql_select($sql_item);
			foreach($resultItem as $itemData)
			{
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]['smv_pcs']=$itemData[csf('smv_pcs')];
				$item_smv_array[$itemData[csf('id')]][$itemData[csf('gmts_item_id')]]['smv_pcs_precost']=$itemData[csf('smv_pcs_precost')];
			}
		}
			
		$po_array=array();
		$query_po_break_down=sql_select("select a.job_no,a.set_smv, a.job_no_prefix_num, a.buyer_name, a.style_ref_no, a.total_set_qnty, b.id, b.po_number, b.unit_price from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		
		
		
		
		foreach ($query_po_break_down as $row)
		{
			$po_array[$row[csf('id')]]['job_no']=$row[csf('job_no')];
			$po_array[$row[csf('id')]]['set_smv']=$row[csf('set_smv')];
			$po_array[$row[csf('id')]]['job']=$row[csf('job_no_prefix_num')];
			$po_array[$row[csf('id')]]['buyer']=$row[csf('buyer_name')];
			$po_array[$row[csf('id')]]['style']=$row[csf('style_ref_no')];
			$po_array[$row[csf('id')]]['po']=$row[csf('po_number')];
			$po_array[$row[csf('id')]]['unit_price']=$row[csf('unit_price')];
			$po_array[$row[csf('id')]]['set_qnty']=$row[csf('total_set_qnty')];
		}
		
		if($db_type==0) { $date_cond=$date; } elseif($db_type==2) { $date_cond=change_date_format($date,'','',1); }
		$smv_array=array();
		$tpd_data_arr=sql_select( "select a.pr_date, sum(a.target_per_hour*a.working_hour) tpd, sum(a.man_power*a.working_hour) tsmv from prod_resource_dtls a, prod_resource_mst b where b.id=a.mst_id and b.company_id='$company_id' and a.pr_date='$date_cond' and a.is_deleted=0 and b.is_deleted=0 group by  a.pr_date");
        foreach($tpd_data_arr as $row)
        {
			$production_date=date("Y-m-d", strtotime($row[csf('pr_date')])); 
           // $tpdArr[$production_date]+=$row[csf('tpd')];
			$smv_array[change_date_format($production_date)]['smv']=$row[csf('tsmv')]*60;
        }
		//var_dump ($smv_array);
		
		if(str_replace("'","",$location_id)==0){$location_con="";}else{$location_con=" and location=$location_id";}
		
		$ord_sew_inhouse_sql="SELECT production_date, po_break_down_id, item_number_id, sum(production_quantity) AS sewing_qnty from pro_garments_production_mst where company_id='$company_id' $location_con and production_date='$date_cond' and is_deleted=0 and production_type=5 and production_source=$sewing_source and status_active=1 group by production_date, po_break_down_id, item_number_id order by production_date asc";
					
		$ord_sew_inhouse_sql_result=sql_select($ord_sew_inhouse_sql); $i=1;
        foreach ($ord_sew_inhouse_sql_result as $row)  
        {
			if($po_array[$row[csf('po_break_down_id')]]['po']){
			if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
			$production_date=change_date_format(date("Y-m-d", strtotime($row[csf('production_date')]))); 
			$item_smv=0;
			if($smv_source==2)
			{
				$item_smv=$item_smv_array[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]]['smv_pcs_precost'];
			}
			else if($smv_source==3)
			{
				$item_smv=$item_smv_array[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]];	
			}
			else
			{
				$item_smv=$item_smv_array[$row[csf("po_break_down_id")]][$row[csf("item_number_id")]]['smv_pcs'];	
			}
			//$ord_sew_smv=$row[csf("sewing_qnty")]*$item_smv;
			//$ord_sew_smv=($row[csf("sewing_qnty")]*$po_array[$row[csf('po_break_down_id')]]['set_smv'])/60;
			$ord_sew_smv=($row[csf("sewing_qnty")]*$item_smv_array[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]]['smv_pcs'])/60;
			//$effiecy_aff_perc=$ord_sew_smv/$smv_array[$production_date]['smv']*100;
			$effiecy_aff_perc=$ord_sew_smv/($smv_array[$production_date]['smv']/60)*100;
			?>
			<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
            	<td width="30"><? echo $i; ?></td>
                <td width="60"><? echo $po_array[$row[csf('po_break_down_id')]]['job']; ?></td>
                <td width="50" align="right"><? echo $item_smv_array[$row[csf('po_break_down_id')]][$row[csf('item_number_id')]]['smv_pcs']; ?></td>
                <td width="110"><? echo $buyerArr[$po_array[$row[csf('po_break_down_id')]]['buyer']]; ?></td>
                <td width="110"><? echo $po_array[$row[csf('po_break_down_id')]]['style']; ?></td>
                <td width="100"><? echo $garments_item[$row[csf('item_number_id')]]; ?></td>
                <td width="110"><? echo $po_array[$row[csf('po_break_down_id')]]['po']; ?></td>
                <td width="90" align="right"><? echo number_format($row[csf("sewing_qnty")],2); $total_sewing_qnty+=$row[csf("sewing_qnty")]; ?></td>
                <td width="90" align="right"><? echo number_format($ord_sew_smv,2); $total_ord_sew_smv+=$ord_sew_smv; ?></td>
                <td align="right"><? echo number_format($effiecy_aff_perc,2); $total_effiecy_aff_perc+=$effiecy_aff_perc; ?></td>
			</tr>
			<?
			$i++; 
			}
        }
        ?>
        <tfoot>
            <th colspan="7">Total</th>
            <th><? echo number_format($total_sewing_qnty,2); ?></th>
            <th><? echo number_format($total_ord_sew_smv,2); ?></th>
            <th><? echo number_format($total_effiecy_aff_perc,2); ?></th>
        </tfoot>
    </table>
	<?
    exit();	
}
?>