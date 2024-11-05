<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];


//$date=date('Y-m-d');
$buyer_short_name_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
//$company_short_name_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
//$company_team_name_arr=return_library_array( "select id,team_name from lib_marketing_team",'id','team_name');
//$company_team_member_name_arr=return_library_array( "select id,team_member_name from  lib_mkt_team_member_info",'id','team_member_name');
//$imge_arr=return_library_array( "select master_tble_id,image_location from   common_photo_library",'master_tble_id','image_location');
//$commission_for_shipment_schedule_arr=return_library_array( "select job_no,commission from  wo_pre_cost_dtls",'job_no','commission');

//$costing_per_arr=return_library_array( "select job_no,costing_per from  wo_pre_cost_mst",'job_no','costing_per');
//$company_arr=return_library_array( "select id, company_name from  lib_company",'id','company_name');
//$country_name_arr=return_library_array( "select id, country_name from lib_country",'id','country_name');


if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );   	 
} 

if ($action=="load_drop_down_team_member")
{
	echo create_drop_down( "cbo_team_member", 150, "select id,team_member_name 	 from lib_mkt_team_member_info  where team_id='$data' and status_active=1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-Select Team Member-", $selected, "" );   	 
}


if ($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_team_name=str_replace("'","",$cbo_team_name);
	$cbo_team_member=str_replace("'","",$cbo_team_member);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$cbo_year=str_replace("'","",$cbo_year);
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_ref_no=str_replace("'","",$txt_ref_no);
	$txt_file_no=str_replace("'","",$txt_file_no);
	
	
	if($txt_job_no!="")
	{
		if($cbo_year!=0)
		{
			if($db_type==0) $job_cond="and a.job_no_prefix_num like '%$txt_job_no' and year(a.insert_date)=$cbo_year";
			if($db_type==2) $job_cond="and a.job_no_prefix_num like '%$txt_job_no' and to_char(a.insert_date,'YYYY')=$cbo_year";
		}
		else 
		{
			$job_cond="";
		}
	}
	
	
	if($db_type==0)
	{
		$start_date=change_date_format($txt_date_from,'yyyy-mm-dd','-');
		$end_date=change_date_format($txt_date_to,'yyyy-mm-dd','-');
    }
	if($db_type==2)
	{
		$start_date=change_date_format($txt_date_from,'yyyy-mm-dd','-',1);
		$end_date=change_date_format($txt_date_to,'yyyy-mm-dd','-',1);
    }
	
	if ($start_date!="" && $end_date!="")
	{
		$date_cond="and b.shipment_date between '$start_date' and  '$end_date'";
	}
	else	
	{
		$date_cond="";
	}
	
	if ($txt_ref_no!="")
	{
		$ref_cond="and b.grouping like '%$txt_ref_no%'";
	}
	else	
	{
		$ref_cond="";
	}
	
	if ($txt_file_no!="")
	{
		$file_cond="and b.file_no like '%$txt_file_no%'";
	}
	else	
	{
		$file_cond="";
	}
	
	if($cbo_buyer_name==0) $buyer_name="%%"; else $buyer_name=$cbo_buyer_name;
	
	if(trim($cbo_team_name)=="0") $team_leader="%%"; else $team_leader="$cbo_team_name";
	if(trim($cbo_team_member)=="0") $dealing_marchant="%%"; else $dealing_marchant="$cbo_team_member";
	
	if($db_type==0) $job_year="year(a.insert_date) as job_year"; else if($db_type==2) $job_year="to_char(a.insert_date,'YYYY') as job_year";
	
		$sql="select a.id as job_id, a.buyer_name, a.job_no, $job_year, a.job_no_prefix_num, a.gmts_item_id, b.id as order_id, b.po_number, b.pub_shipment_date as pub_shipment_date, (b.po_quantity*a.total_set_qnty) as po_quantity,b.grouping,b.file_no from  wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and a.company_name='$cbo_company_name' and a.buyer_name like '$buyer_name' and a.team_leader like '$team_leader'  and a.dealing_marchant like '$dealing_marchant' $date_cond $job_cond $ref_cond $file_cond order by b.id";
	//echo $sql;die;
	$sql_result=sql_select($sql);
	$all_po_id="";
	foreach($sql_result as $row)
	{
		$all_po_id.=$row[csf('order_id')].',';
	}
	$all_po_id=rtrim($all_po_id,',');
	$all_po=array_unique(explode(",",$all_po_id));					
	$po_arr_cond=array_chunk($all_po,1000, true);
	$po_cond_for_in="";$po_cond_for_in2="";
	$pi=0;
	foreach($po_arr_cond as $key=>$value)
	{
	   if($pi==0)
	   {
		$po_cond_for_in=" and ( po_break_down_id  in(".implode(",",$value).")"; 
		$po_cond_for_in2=" and ( a.po_break_down_id  in(".implode(",",$value).")"; 
	   }
	   else //po_break_down_id
	   {
		$po_cond_for_in.=" or po_break_down_id  in(".implode(",",$value).")";
		$po_cond_for_in2.=" or a.po_break_down_id  in(".implode(",",$value).")";
	   }
	   $pi++;
	}	
	$po_cond_for_in.=" )";
	$po_cond_for_in2.=" )";
	
	$reqDataArray=sql_select("select a.po_break_down_id, sum((b.cons/b.pcs)*a.plan_cut_qnty) as finish_req from wo_po_color_size_breakdown a, wo_pre_cos_fab_co_avg_con_dtls b where a.po_break_down_id=b.po_break_down_id and a.color_number_id=b.color_number_id and a.size_number_id=b.gmts_sizes and a.is_deleted=0 and a.status_active=1  
and b.cons>0 and b.pcs>0 $po_cond_for_in2 group by a.po_break_down_id");
	//echo $reqDataArray;die;
	foreach($reqDataArray as $row)
	{
		$finish_require_arr[$row[csf("po_break_down_id")]]=$row[csf("finish_req")];
	}
	
	$finish_receive_qnty_arr=array();$count_day=array();
	$dataArrayTrans=sql_select("select a.po_breakdown_id, count(b.transaction_date) as day_running, 
						sum(CASE WHEN a.entry_form ='7' THEN a.quantity ELSE 0 END) AS finish_receive,
						sum(CASE WHEN a.entry_form ='37' THEN a.quantity ELSE 0 END) AS finish_purchase,
						sum(CASE WHEN a.entry_form ='15' and a.trans_type=5 THEN a.quantity ELSE 0 END) AS transfer_in_qnty,
						sum(CASE WHEN a.entry_form ='15' and a.trans_type=6 THEN a.quantity ELSE 0 END) AS transfer_out_qnty
						from order_wise_pro_details a, inv_transaction b 
						where a.trans_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.po_breakdown_id>0 and a.entry_form in(7,15,37) 
						group by a.po_breakdown_id");
	//echo $dataArrayTrans;die;
	foreach($dataArrayTrans as $row)
	{
		$finish_receive_qnty_arr[$row[csf('po_breakdown_id')]]['trans']=$row[csf('transfer_in_qnty')]-$row[csf('transfer_out_qnty')];
		$finish_receive_qnty_arr[$row[csf('po_breakdown_id')]]['finish_receive']=$row[csf('finish_receive')];
		$finish_receive_qnty_arr[$row[csf('po_breakdown_id')]]['finish_purchase']=$row[csf('finish_purchase')];
		$count_day[$row[csf('po_breakdown_id')]]=$row[csf('day_running')];
	}
	
	
	//LISTAGG(CAST(b.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.id) as tr_id
	if($db_type==0)
	{
		$dataProduction=sql_select("select po_break_down_id, group_concat(production_date) as day_running, sum(production_quantity) AS production_quantity, group_concat( sewing_line) as sewing_line from  pro_garments_production_mst where status_active=1 and is_deleted=0 and production_type=5 $po_cond_for_in group by po_break_down_id");
	}
	else if($db_type==2)
	{
		//$dataProduction=sql_select("select po_break_down_id, LISTAGG(CAST(production_date AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY production_date) as day_running, sum(production_quantity) AS production_quantity, LISTAGG(CAST(sewing_line AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY sewing_line) as sewing_line from  pro_garments_production_mst where status_active=1 and is_deleted=0 and production_type=5 $po_cond_for_in group by po_break_down_id");
		
		$dataProduction=sql_select("select po_break_down_id, production_date as day_running, production_quantity AS production_quantity, sewing_line from  pro_garments_production_mst where status_active=1 and is_deleted=0 and production_type=5 $po_cond_for_in");
	}
	//echo "select po_break_down_id, production_date as day_running, production_quantity AS production_quantity, sewing_line from  pro_garments_production_mst where status_active=1 and is_deleted=0 and production_type=5 $po_cond_for_in";die;
	$production_data_arr=$day_running_check=$sewing_line_check=array();
	foreach($dataProduction as $row)
	{
		$production_data_arr[$row[csf('po_break_down_id')]]['production_quantity']+=$row[csf('production_quantity')];
		if($day_running_check[$row[csf('po_break_down_id')]][$row[csf('day_running')]]=="")
		{
			$day_running_check[$row[csf('po_break_down_id')]][$row[csf('day_running')]]=$row[csf('day_running')];
			$production_data_arr[$row[csf('po_break_down_id')]]['day_running'].=$row[csf('day_running')].",";
		}
		if($sewing_line_check[$row[csf('po_break_down_id')]][$row[csf('sewing_line')]]=="")
		{
			$sewing_line_check[$row[csf('po_break_down_id')]][$row[csf('sewing_line')]]=$row[csf('sewing_line')];
			$production_data_arr[$row[csf('po_break_down_id')]]['sewing_line'].=$row[csf('sewing_line')].",";
		}
		//$production_data_arr[$row[csf('po_break_down_id')]]['day_running']=count(array_unique(explode(",",$row[csf('day_running')])));
		//$production_data_arr[$row[csf('po_break_down_id')]]['sewing_line']=count(array_unique(explode(",",$row[csf('sewing_line')])));
	}
	/*$sql_tna=sql_select( "select b.po_number_id, max(b.task_finish_date) as task_finish_date ,max(a.completion_percent) as completion_percent  from  tna_process_mst b,  lib_tna_task a where a.id=b.task_number and a.status_active=1 and  b.status_active=1 and b.task_number=86 group by po_number_id");
	foreach($sql_tna as $row)
	{
		$production_wise_tna_end[$row[csf("po_number_id")]]["task_finish_date"]=$row[csf("task_finish_date")];
		$production_wise_tna_end[$row[csf("po_number_id")]]["completion_percent"]=$row[csf("completion_percent")];
	}*/
	
	
	$tna_enddate_sql=sql_select("select po_number_id,
							max(case when task_number=73 then task_finish_date end) as fab_tna_end_date,
							max(case when task_number=86 then task_finish_date end) as sew_prod_end_date
							from  tna_process_mst 
							where status_active=1
							group by po_number_id");
	
	$tna_end_date_arr=array();
	foreach($tna_enddate_sql as $row)
	{
		$tna_end_date_arr[$row[csf("po_number_id")]]["fab_tna_end_date"]=$row[csf("fab_tna_end_date")];
		$tna_end_date_arr[$row[csf("po_number_id")]]["sew_prod_end_date"]=$row[csf("sew_prod_end_date")];
	}
	
	$fabric_source_array=return_library_array( "SELECT a.id, b.fabric_source FROM wo_po_break_down a, wo_pre_cost_fabric_cost_dtls b WHERE a.job_no_mst=b.job_no and a.status_active=1",'id','fabric_source');
	$exfact_qty=return_library_array( "SELECT po_break_down_id, 
	sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END)-sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_qnty
	 FROM pro_ex_factory_mst WHERE status_active=1 group by po_break_down_id",'po_break_down_id','ex_factory_qnty');
	

	
	ob_start();
	?>
    <div id="main_body">
    <fieldset>
        <div style="width:1540px;" align="left">
            <table width="1540" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <th width="40">SL</th>
                    <th width="60">Buyer</th>
                    <th width="60">Job Year</th>
                    <th width="60">Job No</th>
                    <th width="110">Order No</th>
                    <th width="110">Ref No</th>
                    <th width="110">File No</th>
                    <th width="100">Item</th>
                    <th width="65">Ship Date</th>
                    <th width="90">Fabric Source</th>
                    <th width="163" >Febric Recv Status</th>
                    <th width="163">Garments Prod. Status</th>
                    <th width="200">Future Prediction</th>
                    <th>Suggestion</th>
                </thead>
            </table>
        </div>
        <div style="max-height:340px; overflow-y:scroll; width:1540px"  align="left" id="scroll_body">
            <table width="1540" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
            <?
			if($db_type==0)
			{
				$library_work_hour=return_library_array( "SELECT date_format(applying_period_date,'%m') as work_month, working_hour FROM lib_standard_cm_entry WHERE status_active=1",'work_month','working_hour');
			}
			else if($db_type==2)
			{
				$library_work_hour=return_library_array( "SELECT to_char(applying_period_date,'mm') as work_month, working_hour FROM lib_standard_cm_entry WHERE status_active=1",'work_month','working_hour');
			}
			
			
			
			$i=1;
			foreach($sql_result as $row)
			{
				$prediction="";
				
				$month_tna_prod = date("m",strtotime($tna_end_date_arr[$row[csf("order_id")]]["sew_prod_end_date"]));
				
				$abailable_qty=($finish_receive_qnty_arr[$row[csf("order_id")]]['finish_purchase']+$finish_receive_qnty_arr[$row[csf("order_id")]]['finish_receive']+$finish_receive_qnty_arr[$row[csf("order_id")]]['trans']);
				$avg_prod_day=($production_data_arr[$row[csf('order_id')]]['production_quantity']/count(explode(",",chop($production_data_arr[$row[csf('order_id')]]['day_running'],","))));  
				$balance_garments_prod=($row[csf("po_quantity")]-$production_data_arr[$row[csf('order_id')]]['production_quantity']); 
				$day_remaining_porduct=datediff( 'd', $pc_date_time, $tna_end_date_arr[$row[csf("order_id")]]["sew_prod_end_date"]);
				$avg_prod_day_line =$avg_prod_day/count(explode(",",chop($production_data_arr[$row[csf('order_id')]]['sewing_line'],",")));
				
				if(is_infinite($avg_prod_day_line) || is_nan($avg_prod_day_line)){$avg_prod_day_line=0;}
				//echo $pc_date_time."kkk";die;
				$req_pord_day=$balance_garments_prod/$day_remaining_porduct;
				if(is_infinite($req_pord_day) || is_nan($req_pord_day)){$req_pord_day=0;}
				
				//echo $day_remaining_porduct."jahid";die;
				if($day_remaining_porduct!="")
				{
					$prediction=($day_remaining_porduct-($balance_garments_prod/$avg_prod_day));
					if(is_infinite($prediction) || is_nan($prediction)){$prediction=0;}
				}
				
				
				$prediction_adi_require=$req_pord_day-$avg_prod_day;
				$prediction_shortfall=($row[csf("po_quantity")]-($production_data_arr[$row[csf('order_id')]]['production_quantity']+($avg_prod_day*$day_remaining_porduct)));
				
				$suggation_1=$prediction_shortfall/$avg_prod_day_line;
				if(is_infinite($suggation_1) || is_nan($suggation_1)){$suggation_1=0;}
				$suggation_ref1=($prediction_adi_require/count(explode(",",chop($production_data_arr[$row[csf('order_id')]]['sewing_line'],","))));
				if(is_infinite($suggation_ref1) || is_nan($suggation_ref1)){$suggation_ref1=0;}
				$suggation_ref2=($avg_prod_day_line/$library_work_hour[$month_tna_prod]);
				if(is_infinite($suggation_ref2) || is_nan($suggation_ref2)){$suggation_ref2=0;}
				$suggation_2=$suggation_ref1/$suggation_ref2;
				if(is_infinite($suggation_2) || is_nan($suggation_2)){$suggation_2=0;}
				
				$item_name_all="";
				$item_name_arr=explode(",",$row[csf("gmts_item_id")]);
				if(!empty($item_name_arr))
				{
					foreach($item_name_arr as $item_id)
					{
						if($item_name_all!="") $item_name_all .=", ";
						$item_name_all .=$garments_item[$item_id];
					}
				}
				
				
				if ($i%2==0)  
				$bgcolor="#E9F3FF";
				else
				$bgcolor="#FFFFFF";	
				
				?>
            	<tr bgcolor="<? echo $bgcolor; ?>">
                	<td width="40" rowspan="12" valign="top"><? echo $i; ?></td>
                    <td width="60" rowspan="12" valign="top"><p><? echo $buyer_short_name_arr[$row[csf("buyer_name")]]; ?></p></td>
                    <td width="60" rowspan="12" valign="top" align="center"><p><? echo $row[csf("job_year")]; ?></p></td>
                    <td width="60" rowspan="12" valign="top" align="center"><p><? echo $row[csf("job_no_prefix_num")]; ?></p></td>
                   	<td width="110" rowspan="12" valign="top" align="center"><p><? echo $row[csf("po_number")]; ?></p></td>
                    <td width="110" rowspan="12" valign="top" align="center"><p><? echo $row[csf("grouping")]; ?></p></td>
                    <td width="110" rowspan="12" valign="top" align="center"><p><? echo $row[csf("file_no")]; ?></p></td>
                    <td width="100" rowspan="12" valign="top"><p><? echo $item_name_all; ?></p></td>
                    <td width="65" rowspan="12" valign="top" align="center"><p><? if($row[csf("pub_shipment_date")]!="" && $row[csf("pub_shipment_date")]!="0000-00-00") echo change_date_format($row[csf("pub_shipment_date")]); else echo "&nbsp;"; ?></p></td>
                    <td width="90" rowspan="12" valign="top"><p><? echo $fabric_source[$fabric_source_array[$row[csf("order_id")]]]; ?></p></td>
                    <td width="80">Fab. Req.</td>
                    <td width="80" align="right" style="padding-right:3px;"><p><? echo number_format($finish_require_arr[$row[csf("order_id")]],0);?></p></td>
                    <td width="80">Order Qty.</td>
                    <td width="80" align="right" style="padding-right:3px;"><p><? echo number_format($row[csf("po_quantity")],0);?></p></td>
                    <?
					if($prediction=== "" )
					{
						?>
						<td width="200" rowspan="12" ><p>&nbsp;</p></td>
						<?
					}
					else
					{
						if($day_remaining_porduct>0)
						{
							if($prediction<0)
							{
								?>
								<td width="200" rowspan="12" style="color:#FF0000; font-size:14px; font-weight:bold;">
								<p>&nbsp;As per current trend, production to be finished late by <? $reslut_day=substr(floor($prediction),1); if($reslut_day>1) echo $reslut_day." days"; else echo $reslut_day." day"; ?> </p>
								<br />
								<p>Need additional <? echo ceil($prediction_adi_require); ?>pcs production per day for remaining days</p>
								<br />
								<p>As per current trend, shortfall qnty would be <? echo ceil($prediction_shortfall); ?>pcs</p>
							   
								</td>
								<?
							}
							else if($prediction>0)
							{
								?>
								<td width="200" rowspan="12" style=" color:#00CC00; font-size:14px; font-weight:bold;"><p>&nbsp;As per current trend, production to be finished early by <? $reslut_day=floor($prediction); if($reslut_day>1) echo $reslut_day." days"; else echo $reslut_day." day"; ?> </p></td>
								<?
							}
							else if($prediction===0)
							{
								?>
								<td width="200" rowspan="12" style=" color:#00CC00; font-size:14px; font-weight:bold;"><p>&nbsp;Production to be finished on time</p></td>
								<?
							}
						}
						else
						{
							$s_completatio_peecent=(($abailable_qty/$row[csf("po_quantity")])*100);
							if(is_infinite($s_completatio_peecent) || is_nan($s_completatio_peecent)){$s_completatio_peecent=0;}
							if($s_completatio_peecent===$production_wise_tna_end[$row[csf("order_id")]]["completion_percent"])
							{
								?>
								<td width="200" rowspan="12" style=" color:#00CC00; font-size:14px; font-weight:bold;" ><p><? echo $production_wise_tna_end[$row[csf("order_id")]]["completion_percent"]."jjj"; ?>Production Completed</p></td>
								<?
							}
							else
							{
								?>
								<td width="200" rowspan="12" style=" color:#FF0000; font-size:14px; font-weight:bold;" >Sewing Completion Date Over But Production Not Completed<p>&nbsp;</p></td>
								<?
							}
						}
					}
					
					?>
                    <td rowspan="12" style="font-size:14px; font-weight:bold;">
                    <?
					if($day_remaining_porduct>0)
					{
						if($prediction<0)
						{
							?>
							<p>Can engage extra 1 line for <? echo ceil($suggation_1); ?> days or <? echo ceil($suggation_1); ?> line for 1 day</p>
							<br />
							<p>Can work extra <? echo ceil($suggation_2); ?> hours per day instead of engaging extra line</p>
							<br />
							<p>If line or extra hour not available, give subcontract</p>
							<?
						}
					}
					?>
                    
                    </td>
                </tr>
                <tr>
                    <td >Fab. Available</td>
                    <td  align="right" style="padding-right:3px;"><p><?  echo number_format($abailable_qty,0);  ?></p></td>
                    <td >Prod. Qty.</td>
                    <td align="right" style="padding-right:3px;"><p><? echo number_format($production_data_arr[$row[csf('order_id')]]['production_quantity'],0); ?></p></td>
                </tr>
                <tr>
                    <td >Balance</td>
                    <td align="right" style="padding-right:3px;"><p><? $balance=($finish_require_arr[$row[csf("order_id")]]-$abailable_qty);  echo number_format($balance,0); ?> </p></td>
                    <td >Balance</td>
                    <td align="right" style="padding-right:3px;"><p><?  echo number_format($balance_garments_prod,0); ?></p></td>
                </tr>
                <tr>
                    <td >Days Running</td>
                    <td align="center"><p><? echo $count_day[$row[csf("order_id")]]; ?></p></td>
                    <td >Days Running</td>
                    <td align="center"><p><? echo count(explode(",",chop($production_data_arr[$row[csf('order_id')]]['day_running'],",")));?></p></td>
                    <!--<td align="center"><p<? // ?>></p></td>-->
                </tr>
                <tr>
                    <td >Avg. Recv/Day</td>
                    <td align="right" style="padding-right:3px;"><p><? $avg_recv_day=($abailable_qty/$count_day[$row[csf("order_id")]]);if(is_infinite($avg_recv_day) || is_nan($avg_recv_day)){$avg_recv_day=0;}  echo number_format($avg_recv_day,0); ?> </p></td>
                    <td >Avg. Prod/Day</td>
                    <td align="right" style="padding-right:3px;"><p><? echo number_format($avg_prod_day,0); ?></p></td>
                </tr>
                <tr>
                    <td >TNA End Date</td>
                    <td align="center"><p><? if($tna_end_date_arr[$row[csf("order_id")]]["fab_tna_end_date"]!="" && $tna_end_date_arr[$row[csf("order_id")]]["fab_tna_end_date"]!='0000-00-00') echo change_date_format($tna_end_date_arr[$row[csf("order_id")]]["fab_tna_end_date"]); else echo "&nbsp;"; ?></p></td>
                    <td >Line Engaged</td>
                    <td align="center"><p><? echo count(explode(",",chop($production_data_arr[$row[csf('order_id')]]['sewing_line'],","))); ?></p></td>
                </tr>
                <tr>
                    <td >Days Remaining</td>
                    <td align="center"><p><? $day_remaining=datediff( 'd', $pc_date_time, $tna_end_date_arr[$row[csf("order_id")]]["fab_tna_end_date"]); echo $day_remaining; ?></p></td>
                    <td >Avg. Prod/Line/Day</td>
                    <td align="right" style="padding-right:3px;"><p><?  echo number_format($avg_prod_day_line,0); ?></p></td>
                </tr>
                <tr>
                    <td >Req. Recv/Day</td>
                    <td align="right" style="padding-right:3px;"><p><? 
					$req_recv_day=$balance/$day_remaining; 
					if(is_infinite($req_recv_day) || is_nan($req_recv_day)){$req_recv_day=0;}
					echo number_format($req_recv_day,0); ?></p></td>
                    <td >TNA End Date</td>
                    <td align="center"><p><? if($tna_end_date_arr[$row[csf("order_id")]]["sew_prod_end_date"]!="" && $tna_end_date_arr[$row[csf("order_id")]]["sew_prod_end_date"]!='0000-00-00')  echo change_date_format($tna_end_date_arr[$row[csf("order_id")]]["sew_prod_end_date"]); else echo "&nbsp;"; ?></p></td>
                </tr>
                <tr>
                    <td >Extra Req/Day</td>
                    <td align="right" style="padding-right:3px;"><p><? $ext_req_day=$req_recv_day-$avg_recv_day;if(is_infinite($ext_req_day) || is_nan($ext_req_day)){$ext_req_day=0;} echo number_format($ext_req_day,0); ?></p></td>
                    <td >Days Remaining</td>
                    <td  align="center" style="padding-right:3px;"><p><? echo $day_remaining_porduct; ?></p></td>
                </tr>
                <tr>
                    <td ></td>
                    <td ></td>
                    <td >Req. Prod/Day</td>
                    <td align="right" style="padding-right:3px;"><p><?  echo number_format($req_pord_day,0);?></p></td>
                </tr>
                 <tr>
                    <td ></td>
                    <td ></td>
                    <td >Ex-Fact Qnty</td>
                    <td align="right" style="padding-right:3px;"><p>
					<a href="##" onClick="generate_ex_factory_popup('ex_factory_popup','<? echo $row[csf('job_no_prefix_num')];?>',
 '<? echo $row[csf('order_id')]; ?>','550px')"><? echo  number_format($exfact_qty[$row[csf("order_id")]],0); ?></a>
					<?  //echo number_format($exfact_qty[$row[csf("order_id")]],0);?></p></td>
                </tr>
                 <tr>
                    <td ></td>
                    <td ></td>
                    <td >Balance</td>
                    <td align="right" style="padding-right:3px;"><p><? $ex_fact_balance=$row[csf("po_quantity")]-$exfact_qty[$row[csf("order_id")]];  echo number_format($ex_fact_balance,0);?></p></td>
                </tr>
             	<?
				$i++;
			}
			?>
            </table>
        </div>
    </fieldset>
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

//Ex-Factory Delv. and Return
if($action=="ex_factory_popup")
{
 	echo load_html_head_contents("Ex-Factory Details", "../../../../", 1, 1,$unicode,'','');
	extract($_REQUEST);
	//echo $id;//$job_no;
	?>
	<div style="width:100%" align="center">
		<fieldset style="width:500px"> 
        <div class="form_caption" align="center"><strong>Ex-Factory Details</strong></div><br />
            <div style="width:100%"> 
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="35">SL</th>
                        <th width="90">Ex-fac. Date</th>
                        <th width="120">System /Challan no</th>
                        <th width="100">Ex-Fact. Del.Qty.</th>
                        <th width="">Ex-Fact.Return Qty.</th>
                       
                     </tr>   
                </thead> 	 	
            </table>  
        </div>
        <div style="width:100%; max-height:400px;">
            <table cellpadding="0" width="100%" cellspacing="0" border="1" rules="all" class="rpt_table">
                <?
                $i=1;
              
		$exfac_sql=("select b.challan_no,a.sys_number,b.ex_factory_date, 
		CASE WHEN b.entry_form!=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_qnty,
		CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END as ex_factory_return_qnty 
		from  pro_ex_factory_delivery_mst a,  pro_ex_factory_mst b  where a.id=b.delivery_mst_id and a.status_active=1 and a.is_deleted=0 and b.po_break_down_id in($id) ");
                $sql_dtls=sql_select($exfac_sql);
                
                foreach($sql_dtls as $row_real)
                { 
                    if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";                               
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_l<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td width="35"><? echo $i; ?></td> 
                        <td width="90"><? echo change_date_format($row_real[csf("ex_factory_date")]); ?></td>
                        <td width="120"><? echo $row_real[csf("sys_number")]; ?></td>
                        <td width="100" align="right"><? echo $row_real[csf("ex_factory_qnty")]; ?></td>
                         <td width="" align="right"><? echo $row_real[csf("ex_factory_return_qnty")]; ?></td>
                    </tr>
                    <? 
                    $rec_qnty+=$row_real[csf("ex_factory_qnty")];
					 $rec_return_qnty+=$row_real[csf("ex_factory_return_qnty")];
                    $i++;
                }
                ?>
                <tfoot>
                <tr>
                    <th colspan="3">Total</th>
                    <th><? echo number_format($rec_qnty,2); ?></th>
                    <th><? echo number_format($rec_return_qnty,2); ?></th>
                </tr>
                <tr>
                 <th colspan="3">Total Balance</th>
                 <th colspan="2" align="right"><? echo number_format($rec_qnty-$rec_return_qnty,2); ?></th>
                </tr>
                </tfoot>
            </table>
        </div> 
		</fieldset>
	</div>    
	<?
    exit();	
}

?>