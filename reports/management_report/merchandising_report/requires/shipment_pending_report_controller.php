<?
include('../../../../includes/common.php');

session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');

$buyer_short_name_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
$company_short_name_arr=return_library_array( "select id,company_short_name from lib_company",'id','company_short_name');
$company_details=return_library_array( "select id,company_name from lib_company",'id','company_name');
$company_team_name_arr=return_library_array( "select id,team_name from lib_marketing_team",'id','team_name');

$imge_arr=return_library_array("select master_tble_id,image_location from common_photo_library",'master_tble_id','image_location');
//$cm_for_shipment_schedule_arr=return_library_array( "select job_no,cm_for_sipment_sche from  wo_pre_cost_dtls",'job_no','cm_for_sipment_sche');
$template_id_arr=return_library_array("select po_number_id, template_id from tna_process_mst group by po_number_id, template_id","po_number_id","template_id");

//$company_team_member_name_arr=return_library_array( "select id,team_member_name from  lib_mkt_team_member_info",'id','team_member_name');
$team_mem=sql_select("select id,team_member_name,member_contact_no from  lib_mkt_team_member_info");
foreach($team_mem as $tm)
{
	$company_team_member_name_arr[$tm[csf('id')]]=$tm[csf('team_member_name')];
	$company_team_member_contact_arr[$tm[csf('id')]]=$tm[csf('member_contact_no')];
}

$team_leader=sql_select("select id,team_leader_name,team_contact_no from  lib_marketing_team");
foreach($team_leader as $tl)
{
	$team_leader_arr[$tl[csf('id')]]=$tl[csf('team_leader_name')];
	$company_team_leader_contact_arr[$tl[csf('id')]]=$tl[csf('team_contact_no')];
}


if ($action=="load_drop_down_location")
{
	 echo create_drop_down( "cbo_location_id", 172, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--All--", $selected, "","","","","","",3 );		 
}


if($action=="report_generate")
{
	$data=explode("_",$data);
	if($data[0]==0) $company_name="%%"; else $company_name=$data[0];
	//if(trim($data[1])!="") $job_number="%".$data[1]."%"; else  $job_number="%%";
	//if(trim($data[2])!="") $txt_style_number="%".$data[2]."%"; else $txt_style_number="%%";
	//if(trim($data[3])!="") $txt_po_number="%".trim($data[3])."%"; else $txt_po_number="%%";
	//if(trim($data[4])!="") $txt_order_qnty="%".$data[4]."%"; else $txt_order_qnty="%%";
	//if(trim($data[5])!="") $buyer_name="%".$data[5]."%"; else $buyer_name="%%";
	$company_id=str_replace("'","",$cbo_company_id);
	$txt_job_number=str_replace("'","",$txt_job_number);
	$buyer_name=str_replace("'","",$buyer_name);
	$txt_agent_name=str_replace("'","",$txt_agent_name);
	$txt_po_number=str_replace("'","",$txt_po_number);
	$txt_style_number=str_replace("'","",$txt_style_number);
	$txt_order_qnty=str_replace("'","",$txt_order_qnty);
		
	if($cbo_location_id=="") $location_id=""; else $location_id=$cbo_location_id;
	if($location_id!=0)
	{
		$home_page_location_con=" and b.location_name like '$location_id'";
	}
	//echo $home_page_location_con;
	
	if(trim($buyer_name)!="") 
	{
		$buyer_type_cond="and b.buyer_name in(select id from lib_buyer where LOWER(buyer_name) like LOWER('%$buyer_name%') and status_active=1 and is_deleted=0)";	
	}
	else
	{
		$buyer_type_cond="";
	}
	
	
	if(trim($txt_agent_name)!="") 
	{
		$agent_cond="and b.agent_name in(select a.id from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company=$cbo_company_id and LOWER(a.buyer_name) like LOWER('%$txt_agent_name%') and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (20,21)))";	
	}
	else
	{
		$agent_cond="";
	}
	
	$start_date = return_field_value("min(a.pub_shipment_date)","wo_po_break_down a, wo_po_details_master b"," b.job_no=a.job_no_mst and b.company_name like '$company_id' and a.shiping_status!=3  and a.is_confirmed=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_cond $item_cond"); //and b.job_no_prefix_num like '$txt_job_number' and b.style_ref_no like '$txt_style_number' and a.po_number like '$txt_po_number' and a.po_quantity like '$txt_order_qnty'
	
	//$enddate = return_field_value("max(a.pub_shipment_date)","wo_po_break_down a, wo_po_details_master b"," b.job_no=a.job_no_mst and b.company_name like '$company_id' and a.shiping_status!=3  and a.is_confirmed=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_cond $item_cond");

	$end_date=date("Y-m-01"); 

	$start_month=date("Y-m",strtotime($start_date));
	//$end_month=date("Y-m");
	$end_month=date("Y-m",strtotime("-1 days"));
	$end_date2=date("Y-m-d",strtotime("-1 days"));
	
	if($db_type==2)
	{
		$start_date=change_date_format($start_date,'yyyy-mm-dd','-',1);
		$end_date2=change_date_format($end_date2,'yyyy-mm-dd','-',1);
	}
	
	//$diff = abs(strtotime($start_month) - strtotime($end_month));
	//$total_months = floor($diff / (30*60*60*24));
	$total_months=datediff("m",$start_month,$end_month);
	
	$last_month=date("Y-m", strtotime("+1 Months", strtotime($end_month)));
	
	$previous_month_year=date("Y-m",strtotime("-1 Months", strtotime($end_month)));
	$array_previous_month_year=explode("-",$previous_month_year);
	$number_of_dayes_prev_moth=cal_days_in_month(CAL_GREGORIAN, $array_previous_month_year[1], $array_previous_month_year[0]);
	$previous_month_end_date=$previous_month_year."-".$number_of_dayes_prev_moth;
	
	if($db_type==2)
	{
		$previous_month_end_date=change_date_format($previous_month_end_date,'yyyy-mm-dd','-',1);
	}
	
	$month_identify=explode("-",$end_date2);
	$month=$month_identify[1];
	$year=$month_identify[0];
	$num_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
	//$current_month_end_date=$year."-".$month."-".$num_days;
	//$current_month_end_date=$year."-".$month;
	$current_month_end_date=date("Y-m-d",strtotime("-1 days"));
	if($db_type==2)
	{
		$current_month_end_date=change_date_format($current_month_end_date,'yyyy-mm-dd','-',1);
	}
	//echo $current_month_end_date.'dd';die;
	if($end_date!="")
	{
		$str_cond="and a.pub_shipment_date between '$start_date' and '$previous_month_end_date'";
		
		$end_date3=date("Y-m-01",strtotime($end_date2));
		if($db_type==2)
		{
			$end_date3=change_date_format($end_date3,'yyyy-mm-dd','-',1);
		}
		$str_cond_curr="and a.pub_shipment_date between '$end_date3' and '$current_month_end_date'";
	}
	else
	{
		$str_cond="";
		$str_cond_curr="";
	}
	//echo $str_cond .'=='.$str_cond_curr;die;
	ob_start();	
?>
<!--=============================================================Total Summary Start=============================================================================================-->
    <div style="width:1200px">
    <table width="1200px"  cellspacing="0">
        <tr>
            <td colspan="7" align="center" ><font size="3"><strong><?php echo $company_details[$company_id]; ?></strong></font></td>
        </tr>
        <tr class="form_caption">
            <td colspan="7" align="center"><font size="3"><strong>Total Pending Order Summary </strong></font></td>
        </tr>
    </table>
	<table border="1" rules="all" class="rpt_table" width="1200">
        <thead>
            <th width="30">SL</th>
            <th width="130"> Month </th>
            <th width="130">Pending PO Qty. </th>
            <th width="140">Pending PO Value</th>
            <th width="80">Pending SAH</th>
            <th width="135">Pending Plan Cut Qty.</th>
            <th width="125">Cutting Pending </th>
            <th width="125">Sewing Pending</th>
            <th>Finishing Pending </th>
        </thead>
		<?
        //$sql_summary_ex_factory=return_library_array("SELECT  from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c, pro_ex_factory_mst d where a.job_no=b.job_no_mst and a.job_no=c.job_no_mst and b.id=c.po_break_down_id  and b.id=d.po_break_down_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0   and   c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0  group by po_break_down_id",'po_break_down_id','ex_factory_qnty');

		$sql_summary_ex_factory=return_library_array("SELECT po_break_down_id,sum(ex_factory_qnty) AS ex_factory_qnty from pro_ex_factory_mst  where status_active=1 and is_deleted=0 group by po_break_down_id",'po_break_down_id','ex_factory_qnty');
		
		$cutting_qnty=return_library_array("SELECT po_break_down_id,sum(production_quantity) AS production_quantity  from pro_garments_production_mst where production_type ='1' and is_deleted=0 and status_active=1 group by po_break_down_id",'po_break_down_id','production_quantity');
		
		$sewingin_qnty=return_library_array("SELECT po_break_down_id,sum(production_quantity) AS production_quantity  from pro_garments_production_mst where production_type ='5' and is_deleted=0 and status_active=1 group by po_break_down_id",'po_break_down_id','production_quantity');
		
		$finish_qnty=return_library_array("SELECT po_break_down_id,sum(production_quantity) AS production_quantity  from pro_garments_production_mst where production_type ='8' and is_deleted=0 and status_active=1 group by po_break_down_id",'po_break_down_id','production_quantity');

		$prev_PendingSAH=0;$prev_po_qnty=0; $prev_po_val=0; $prev_sew_qnty=0; $prev_cut_qnty=0; $prev_finish_qnty=0;
		
	
		$sql_summary=sql_select( "SELECT a.id, b.order_uom, a.shiping_status,a.pub_shipment_date,a.extended_ship_date, a.job_no_mst, a.po_quantity, b.total_set_qnty, a.plan_cut, (a.unit_price/b.total_set_qnty) as unit_price,set_smv  from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name like '$company_id' and a.shiping_status!=3 and a.is_confirmed=1 and a.po_quantity>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_cond $item_cond $str_cond $home_page_location_con ");  //and b.job_no_prefix_num like '$txt_job_number' and  b.style_ref_no like '$txt_style_number' and a.po_number like '$txt_po_number' and a.po_quantity like '$txt_order_qnty' 
		
		$current_month_enddate=date("Y-m-d",strtotime("-1 days"));
		foreach( $sql_summary as $row_summary)
		{
			
			$pub_shipment_date=date("Y-m-d",strtotime($row_summary[csf('pub_shipment_date')]));
			$extended_ship_date=date("Y-m-d",strtotime($row_summary[csf('extended_ship_date')]));
				
			
			if($row_summary[csf('extended_ship_date')]=='') $extended_ship_date=$current_month_enddate;  
			if($extended_ship_date<=$current_month_enddate) //Check here
			{
				if($row_summary[csf('shiping_status')]==2)
				{
					$order_ex_quantity=0;
					$order_ex_quantity=$sql_summary_ex_factory[$row_summary[csf('id')]];
				}
				else
				{
					$order_ex_quantity=0;
				}
				$pre_po_quantity=$row_summary[csf('po_quantity')]*$row_summary[csf('total_set_qnty')];
				$pre_plan_cut_qty=$row_summary[csf('plan_cut')]*$row_summary[csf('total_set_qnty')];
				$tot_pre_plan_cut+=$pre_plan_cut_qty;
				$order_quantity=$pre_po_quantity-$order_ex_quantity;
				$prev_po_qnty+=$order_quantity;
				$prev_po_val+=$order_quantity*$row_summary[csf('unit_price')];
				if($pre_plan_cut_qty-$cutting_qnty[$row_summary[csf('id')]]>0)
				{
					$prev_cut_qnty+=$pre_plan_cut_qty-$cutting_qnty[$row_summary[csf('id')]];
				}
				if($pre_po_quantity-$sewingin_qnty[$row_summary[csf('id')]]>0)
				{
					$prev_sew_qnty+=$pre_po_quantity-$sewingin_qnty[$row_summary[csf('id')]];
				}
				if($pre_po_quantity-$finish_qnty[$row_summary[csf('id')]]>0)
				{
					$prev_finish_qnty+=$pre_po_quantity-$finish_qnty[$row_summary[csf('id')]];
				}
				
				$prev_PendingSAH+=($order_quantity*$row_summary[csf('set_smv')])/60;
			}
			
		}
		
		$curr_PendingSAH=0;$curr_po_qnty=0; $curr_po_val=0; $curr_cut_qnty=0; $curr_sew_qnty=0; $curr_finish_qnty=0;
		$sql_summary2=sql_select("SELECT a.id, b.order_uom, a.shiping_status,a.pub_shipment_date, a.extended_ship_date,a.job_no_mst, a.po_quantity, b.total_set_qnty, a.plan_cut, (a.unit_price/b.total_set_qnty) as unit_price,set_smv from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name like '$company_id' and a.shiping_status!=3  and a.is_confirmed=1 and a.po_quantity>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_cond $item_cond $str_cond_curr"); //and b.job_no_prefix_num like '$txt_job_number' and  b.style_ref_no like '$txt_style_number' and a.po_number like '$txt_po_number' and a.po_quantity like '$txt_order_qnty'
		//echo "SELECT a.id, b.order_uom, a.shiping_status,a.pub_shipment_date, a.extended_ship_date,a.job_no_mst, a.po_quantity, b.total_set_qnty, a.plan_cut, (a.unit_price/b.total_set_qnty) as unit_price,set_smv from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name like '$company_id' and a.shiping_status!=3  and a.is_confirmed=1 and a.po_quantity>0 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_cond $item_cond $str_cond_curr";
		
		foreach($sql_summary2 as $row_summary2)
		{
			$pub_shipment_date=date("Y-m-d",strtotime($row_summary2[csf('pub_shipment_date')]));
			$extended_ship_date=date("Y-m-d",strtotime($row_summary2[csf('extended_ship_date')]));
			
			if($row_summary2[csf('extended_ship_date')]=='') $extended_ship_date=$current_month_enddate;
			if($extended_ship_date<=$current_month_enddate) //Check here
			{
				if($row_summary2[csf('shiping_status')]==2)
				{
					$order_ex_quantity2=0;
					$order_ex_quantity2=$sql_summary_ex_factory[$row_summary2[csf('id')]];
				}
				else
				{
					$order_ex_quantity2=0;
				}
				$curr_po_quantity=$row_summary2[csf('po_quantity')]*$row_summary2[csf('total_set_qnty')];
				$curr_plan_cut_qty=$row_summary2[csf('plan_cut')]*$row_summary2[csf('total_set_qnty')];
				$tot_curr_plan_cut+=$curr_plan_cut_qty;
				$order_quantity2=$curr_po_quantity-$order_ex_quantity2;
				$curr_po_qnty+=$order_quantity2;
				$curr_po_val+=$order_quantity2*$row_summary2[csf('unit_price')];
				if($curr_plan_cut_qty-$cutting_qnty[$row_summary2[csf('id')]]>0)
				{
					$curr_cut_qnty+=$curr_plan_cut_qty-$cutting_qnty[$row_summary2[csf('id')]];
				}
				if($curr_po_quantity-$sewingin_qnty[$row_summary2[csf('id')]]>0)
				{
					$curr_sew_qnty+=$curr_po_quantity-$sewingin_qnty[$row_summary2[csf('id')]];
				}
				if($curr_po_quantity-$finish_qnty[$row_summary2[csf('id')]]>0)
				{
					$curr_finish_qnty+=$curr_po_quantity-$finish_qnty[$row_summary2[csf('id')]];
				}
				
				
				
				$curr_PendingSAH+=($order_quantity2*$row_summary2[csf('set_smv')])/60;
			}
			
		}
		
		$curr_month=date("F",strtotime($end_month)).", ".date("Y",strtotime($end_month));
		
		$summary_grand_total_po_qny=0;
		$summary_grand_total_lc_value=0;
		$summary_grand_total_cut_qny=0;
		$summary_grand_total_sewing_qny=0;
		$summary_grand_total_finish_qny=0;
		$bgcolor1='#E9F3FF';
		$bgcolor2='#FFFFFF';
		?>
        <tr bgcolor="<? echo $bgcolor1; ?>" onclick="change_color('tr1st_1','<? echo $bgcolor1; ?>')" id="tr1st_1">
            <td>1</td>
            <td>Previous To Current Month</td>
            <td align="right"><? echo number_format($prev_po_qnty,0); $summary_grand_total_po_qny+=$prev_po_qnty; ?></td>
            <td align="right"><? echo number_format($prev_po_val,2); $summary_grand_total_lc_value+=$prev_po_val; ?></td>
            <td align="right"><? echo number_format($prev_PendingSAH,2);$summary_grand_total_PendingSAH+=$prev_PendingSAH;?></td>
            <td align="right"><? echo number_format($tot_pre_plan_cut,0); $summary_grand_total_plan_cut+=$tot_pre_plan_cut; ?></td>
            <td align="right"><? echo number_format($prev_cut_qnty,0); $summary_grand_total_cut_qny+=$prev_cut_qnty; ?></td>
            <td align="right"><? echo number_format($prev_sew_qnty,0); $summary_grand_total_sewing_qny+=$prev_sew_qnty; ?></td>
            <td align="right"><? echo number_format($prev_finish_qnty,0); $summary_grand_total_finish_qny+=$prev_finish_qnty; ?></td>
        </tr>
        <tr bgcolor="<? echo $bgcolor2; ?>" onclick="change_color('tr1st_2','<? echo $bgcolor2; ?>')" id="tr1st_2">
            <td>2</td>
            <td> <? echo $curr_month; ?> </td>
            <td align="right"><? echo number_format($curr_po_qnty,0); $summary_grand_total_po_qny+=$curr_po_qnty; ?></td>
            <td align="right"><? echo number_format($curr_po_val,2); $summary_grand_total_lc_value+=$curr_po_val; ?></td>
            <td align="right"><? echo number_format($curr_PendingSAH,2);$summary_grand_total_PendingSAH+=$curr_PendingSAH;?></td>
            <td align="right"><? echo number_format($tot_curr_plan_cut,0); $summary_grand_total_plan_cut+=$tot_curr_plan_cut; ?></td>
            <td align="right"><? echo number_format($curr_cut_qnty,0); $summary_grand_total_cut_qny+=$curr_cut_qnty; ?></td>
            <td align="right"><? echo number_format($curr_sew_qnty,0); $summary_grand_total_sewing_qny+=$curr_sew_qnty; ?></td>
            <td align="right"><? echo number_format($curr_finish_qnty,0); $summary_grand_total_finish_qny+=$curr_finish_qnty; ?></td>
        </tr>
        <tfoot>
            <th colspan="2" align="right">Total</th>
            <th align="right"><? echo number_format($summary_grand_total_po_qny,0); ?></th>
            <th align="right"><? echo number_format($summary_grand_total_lc_value,2); ?></th>
            <th align="right"><? echo number_format($summary_grand_total_PendingSAH,2); ?></th>
            <th align="right"><? echo number_format($summary_grand_total_plan_cut,0); ?></th>
            <th align="right"><? echo number_format($summary_grand_total_cut_qny,0); ?></th>
            <th align="right"><? echo number_format($summary_grand_total_sewing_qny,0); ?></th>
            <th align="right"><? echo number_format($summary_grand_total_finish_qny,0); ?> </th>
        </tfoot>
    </table> 
   
    <br/> 
    <table width="1200">
        <tr>
        <?	$s=0;$head_check_arr=array();
			for($i=0;$i<=$total_months;$i++)
			{
				$last_month=date("Y-m", strtotime("-1 Months", strtotime($last_month)));
				$month_query=$last_month."-"."%%"; 
				//$exte_value=array();
				if($i==0)
				{
					$month_query_start_date=$last_month."-01"; 
					$month_query_end_date=date("d",strtotime("-1 days"));
					$month_query_end_date=$last_month."-".$month_query_end_date; 
					if($db_type==2)
					{
						$month_query_start_date=change_date_format($month_query_start_date,'yyyy-mm-dd','-',1);
						$month_query_end_date=change_date_format($month_query_end_date,'yyyy-mm-dd','-',1);
					}
					$month_query_cond="and a.pub_shipment_date between '$month_query_start_date' and '$month_query_end_date'";
					$month_query_cond2="and b.pub_shipment_date between '$month_query_start_date' and '$month_query_end_date'";
					$month_query_cond3="and a.pub_shipment_date between '$month_query_start_date' and '$month_query_end_date'";
				}
				else
				{
					$month_query_cond="and a.pub_shipment_date like '$month_query'";
					if($db_type==2)
					{
						$month_query_cond="and to_char(a.pub_shipment_date,'YYYY-MM-DD') like '$month_query'";
						$month_query_cond2="and to_char(b.pub_shipment_date ,'YYYY-MM-DD') like '$month_query'";
						$month_query_cond3="and to_char(a.pub_shipment_date ,'YYYY-MM-DD') like '$month_query'";
					}
				}
				
				 $sql_month="SELECT a.id, b.buyer_name,a.pub_shipment_date,a.shiping_status, a.extended_ship_date,a.po_quantity, b.total_set_qnty, a.plan_cut, (a.unit_price/b.total_set_qnty) as order_rate,b.set_smv from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name like '$company_id' and a.shiping_status!=3 and a.is_confirmed=1 and a.po_quantity>0  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $month_query_cond3 $buyer_cond $item_cond";
				 
				$res_month=sql_select($sql_month);
				foreach($res_month as $row)
				{
					$pub_shipment_date=date("Y-m-d",strtotime($row[csf('pub_shipment_date')]));
					$extended_ship_date=date("Y-m-d",strtotime($row[csf('extended_ship_date')]));
					if($row[csf('extended_ship_date')]=='') $extended_ship_date=$current_month_enddate;
					
					/*if($row[csf('shiping_status')]==2)
					{
						$buyer_ex_quantity=0;
						$buyer_ex_quantity=$sql_summary_ex_factory[$row[csf('id')]];
					}
					else
					{
						$buyer_ex_quantity=0;
					}
					$po_quantity=$row[csf('po_quantity')]*$row[csf('total_set_qnty')];
					$buyer_order_quantity=$po_quantity-$buyer_ex_quantity;
					//$tot_buyer_order_quantity+=$buyer_order_quantity;
					$buyer_order_val+=$buyer_order_quantity*$row[csf('order_rate')];
					
					$buyer_SAH=($buyer_order_quantity*$row[csf('set_smv')])/60;*/
										
					if($extended_ship_date<=$current_month_enddate) //Check here
					{ 
					//if($row[csf('extended_ship_date')]!="")	 $row[csf('pub_shipment_date')]=$row[csf('extended_ship_date')];
					$summary_buyer_arr[$row[csf('buyer_name')]]['pub_shipment_date']=$row[csf('pub_shipment_date')];
					$summary_buyer_arr[$row[csf('buyer_name')]]['extended_ship_date']=$row[csf('extended_ship_date')];
					//$summary_buyer_arr[$row[csf('buyer_name')]]['tot_buyer_order_quantity']=$tot_buyer_order_quantity;
					//$summary_buyer_arr[$row[csf('buyer_name')]]['buyer_order_val']=$buyer_order_quantity*$row[csf('order_rate')];
					//$summary_buyer_arr[$row[csf('buyer_name')]]['buyer_order_val']=$buyer_order_val;
					}
				}
				$tot_rows=count($res_month);
				$buyer_check_arr=array();
				if($tot_rows>0) 
				{
					if($s%3==0) $tr="</tr><tr>"; else $tr="";
					echo $tr;
				?>
					<td valign="top">
						<div style="width:400px">
						
                            <?
									
							$d=1; $tot_po_qnty=0;$tot_po_val=0; $tot_SAHl=0;
					$m=1;	
					foreach( $summary_buyer_arr as $buyer_key=>$row_month)
					{
						if ($d%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									
							$pub_shipment_date=date("Y-m-d",strtotime($row_month['pub_shipment_date']));
							$extended_ship_date=date("Y-m-d",strtotime($row_month['extended_ship_date']));
							
							
								//$ext_ship_date=date("Y-m",strtotime($row_month['extended_ship_date']));
								
							//$last_month=date("Y-m", strtotime($row_month['pub_shipment_date']));
									
							if($row_month['extended_ship_date']=='') $extended_ship_date=$current_month_enddate;
							//echo $ext_ship_date.'=='.$last_month;
							/*if($ext_ship_date==$last_month)
							{
								$last_month=$last_month;
							}
							else if($ext_ship_date!=$last_month)
							{
								$last_month=$ext_ship_date;
							}*/
							//die;
							//if($extended_ship_date<=$current_month_enddate) //Check here
							//{ 
							if($m==1){
									?>
						<table width="400px"  cellspacing="0"  class="display">
							<tr>
								<td colspan="4" align="center"><font size="3"><strong>Total Summary <? $month_name=date("F",strtotime($last_month)).", ".date("Y",strtotime($last_month)); echo $month_name; ?></strong></font></td>
							</tr>
						</table>
						<table width="400px" class="rpt_table" border="1" rules="all">
							<thead>
								<th width="30">SL</th>
								<th width="100">Buyer Name</th>
								<th width="90">PO Qnty</th>
								<th width="90">PO Value</th>
                                <th width="70">SAH</th>
							</thead>
									<?
									$m=0;
								}
							
							 
								 $sql="SELECT a.buyer_name,b.id, b.shiping_status, b.po_quantity,b.pub_shipment_date, b.extended_ship_date, a.total_set_qnty, b.plan_cut, (b.unit_price/a.total_set_qnty) as order_rate,a.set_smv from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$company_id' $month_query_cond2 and a.buyer_name='".$buyer_key."' and b.po_quantity>0  and b.is_confirmed=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.shiping_status!=3 ";//and a.job_no_prefix_num like '$txt_job_number' 
								
								//echo $sql;
								$result=sql_select($sql); 
								$buyer_SAH=0;$buyer_order_quantity=0; $buyer_order_val=0;$tot_buyer_order_quantity=0;$tot_buyer_order_quantity2=0;
								$buyer_check_arr=array();
								foreach( $result as $row)
								{
									$pub_shipment_date=date("Y-m-d",strtotime($row[csf('pub_shipment_date')]));
									$extended_ship_date=date("Y-m-d",strtotime($row[csf('extended_ship_date')]));
									if($row[csf('extended_ship_date')]=='') $extended_ship_date=$current_month_enddate;
									$month_key_ext=date("Y-m",strtotime($row[csf('extended_ship_date')]));
									$month_key_pubship=date("Y-m",strtotime($row[csf('pub_shipment_date')])); 
									
									if($extended_ship_date<=$current_month_enddate ) //Check here
									{ 
										if($row[csf('shiping_status')]==2)
										{
											$buyer_ex_quantity=0;
											$buyer_ex_quantity=$sql_summary_ex_factory[$row[csf('id')]];
										}
										else
										{
											$buyer_ex_quantity=0;
										}
										$po_quantity=$row[csf('po_quantity')]*$row[csf('total_set_qnty')];
										$buyer_order_quantity=$po_quantity-$buyer_ex_quantity;
										$tot_buyer_order_quantity+=$buyer_order_quantity;
										$buyer_order_val+=$buyer_order_quantity*$row[csf('order_rate')];
										
										$buyer_SAH+=($buyer_order_quantity*$row[csf('set_smv')])/60;
									}
									else
									{
										
										 
											if($row[csf('shiping_status')]==2)
											{
												$buyer_ex_quantity=0;
												$buyer_ex_quantity=$sql_summary_ex_factory[$row[csf('id')]];
											}
											else
											{
												$buyer_ex_quantity=0;
											}
											$buyer_order_val+=$buyer_order_quantity*$row[csf('order_rate')];
											$buyer_SAH+=($buyer_order_quantity*$row[csf('set_smv')])/60;
										
										//	echo "**";
											$exte_value[$buyer_key][$month_key_ext]=($row[csf('po_quantity')]*$row[csf('total_set_qnty')])-$buyer_ex_quantity;
										//$tot_buyer_order_quantity+=$exte_value[$buyer_key];
										 
									}
									
								}
								if($tot_buyer_order_quantity>0)
								{
							?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr1st_<? echo $i; ?><? echo $d; ?>','<? echo $bgcolor; ?>')" id="tr1st_<? echo $i; ?><? echo $d; ?>">
                                	<td><? echo $d; ?></td>
                                    <td><p><? echo $buyer_short_name_arr[$buyer_key]; ?></p></td>
                                    <td align="right"><? echo number_format($tot_buyer_order_quantity+$exte_value[$buyer_key][$month_key_pubship],0); $tot_po_qnty+=$tot_buyer_order_quantity+$exte_value[$buyer_key][$month_key_pubship]; ?></td>
                                    <td align="right"><? echo number_format($buyer_order_val,2); $tot_po_val+=$buyer_order_val; ?></td>
                                    <td align="right"><? echo number_format($buyer_SAH,2); $tot_SAHl+=$buyer_SAH; ?></td>
                                </tr>
							<?
							
							
								$d++;
							}
						//}
						}
							if($tot_po_qnty>0){
						
							?>
                            <tfoot>
                            	<th colspan="2" align="right">Total</th>
                                <th align="right"><? echo number_format($tot_po_qnty,0); ?></th>
                                <th align="right"><? echo number_format($tot_po_val,2); ?></th>
                                <th align="right"><? echo number_format($tot_SAHl,2); ?></th>
                            </tfoot>
							<?
							}
							?>
						</table>
						</div>  
					</td> 
				 <?
				$s++;}
				
			}		 
		?>
    	</tr>
    </table>
	 </div>
    <br/>
    <?
    //ob_start();	
    ?>
    <div>
    <div align="left" style="background-color:#E1E1E1; color:#000; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif; width:2660px"><strong><u><i> Details Report</i></u></strong></div>        
    	<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="2660">
        	<thead>
            	<tr>
                    <th width="40">SL</th>
                    <th width="70">Job No</th>
                    <th width="80">Buyer Name</th>
                    <th width="80">Agent Name</th>
                    <th width="110">PO Number</th>
                    <th width="50">Image</th> 
                    <th width="120">Style Name</th>
                    <th width="140">Item Name</th>
                    <th width="60">Sew SMV</th>
                    <th width="100">PO Qnty.</th>
                    <th width="100">Plan Cut Qty.</th>
                    <th width="90">Ship Date</th>
                    <th width="60">Delay</th>
                    
                    <th width="90">Fabric Booking No</th>
                    <th width="90">
                        Yarn Issue Balance <span style="font-size:9px">[Grey Req As FB - (Yarn Issue + Net Trans)]</span>
                    </th>
                    <th width="90">
                        Knitting Balance <span style="font-size:9px">[Grey Req. As Booking - Grey Prod.]</span>
                    </th>
                    <th width="90">
                        Finish Fab. Balance <span style="font-size:9px">[Finish Req. As Booking - Finish Prod.]</span>
                    </th>

                    <th width="90">Cut Qnty</th>
                    <th width="80">Cut Wastage</th>
                    <th width="90">Sewing Qnty </th>
                    <th width="90">Finish Qnty</th>
                    <th width="90">Finish Pending</th>
                    <th width="90">Ship Qnty</th>
                    <th width="100">Pending PO Qnty.</th>
                    <th width="100">Pending PO Value.</th>
                    <th width="90">Pending SAH</th>
                    <th width="100">Team Leader</th>
                    <th width="100">Dealing Merchant</th>
                    <th>Remarks</th>
                </tr>
                <tr>
                	<th width="40">&nbsp;</th>
                	<th width="70"><input type="text" value="<? echo str_replace("%","",$job_number); ?>" onkeyup="show_inner_filter(event);" name="txt_job_number" id="txt_job_number" class="text_boxes" style="width:50px" /></th>
                    <th width="80"><input type="text" name="buyer_name" onkeyup="show_inner_filter(event);" value="<? echo str_replace("%","",$buyer_name); ?>" id="buyer_name" class="text_boxes" style="width:60px" /></th>
                    
                    <th width="80"><input type="text" name="txt_agent_name" onkeyup="show_inner_filter(event);" value="<? echo str_replace("%","",$txt_agent_name); ?>" id="txt_agent_name" class="text_boxes" style="width:60px" /></th>
                    
                    
                    <th width="110"><input type="text" name="txt_po_number" onkeyup="show_inner_filter(event);" value="<? echo str_replace("%","",$txt_po_number); ?>" id="txt_po_number" class="text_boxes" style="width:80px" /></th>
                    <th width="50">&nbsp;</th>
                    <th width="120"><input type="text" name="txt_style_number" onkeyup="show_inner_filter(event);" value="<? echo str_replace("%","",$txt_style_number); ?>" id="txt_style_number" class="text_boxes" style="width:80px" /></th>
                    <th width="100">&nbsp;</th>
                    <th width="50">&nbsp;</th>
                    <th width="100"><input type="text" name="txt_order_qnty" onkeyup="show_inner_filter(event);" value="<? echo str_replace("%","",$txt_order_qnty); ?>" id="txt_order_qnty" class="text_boxes" style="width:80px" /></th>
                    <th colspan="19">&nbsp;</th>
                </tr>
        	</thead>
		</table>
     	<div style="width:2678px; max-height:410px; overflow-y: scroll;" id="scroll_body">
        <table cellspacing="0" cellpadding="0"  width="2660"  border="1" rules="all" class="rpt_table" id="table_body" >
			<?
			
			//---------------------------------
			$sql="select a.po_break_down_id,a.booking_no,a.booking_no_prefix_num,a.booking_type from wo_booking_mst a where a.company_id=$cbo_company_id and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1";
			$bookingResul=sql_select($sql);
			foreach($bookingResul as $rows){
				if($rows[csf('booking_type')]==4)$bookingType="SF: "; else $bookingType="MF: ";
				$bookingDataArr[$rows[csf('po_break_down_id')]][]=$bookingType.$rows[csf('booking_no_prefix_num')];
			}
			
			
			
		  $sql="select b.po_break_down_id, a.booking_type,  b.fin_fab_qnty as req_qnty,  b.grey_fab_qnty as grey_req_qnty from wo_booking_mst a, wo_booking_dtls b where a.company_id=$cbo_company_id and a.booking_no=b.booking_no and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
 					$tot_req_qnty[$row[csf('po_break_down_id')]]+=$row[csf('grey_req_qnty')];
 					$tot_fin_req_qnty[$row[csf('po_break_down_id')]]+=$row[csf('req_qnty')];
					}
			
			
			
			$sql="select d.prod_id,b.quantity as issue_qnty from order_wise_pro_details b, inv_transaction d where d.id=b.trans_id and b.trans_type in(2,6) and d.company_id=$cbo_company_id and b.status_active=1 and b.is_deleted=0";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
						//$yarn_issued[$row[csf('prod_id')]]+=$row[csf('issue_qnty')];

					}
			
			
		$dataArrayTrans=sql_select("select po_breakdown_id, 
								sum(CASE WHEN entry_form ='7' THEN quantity ELSE 0 END) AS finish_receive,
								sum(CASE WHEN entry_form ='66' THEN quantity ELSE 0 END) AS finish_receive_roll_wise,
								sum(CASE WHEN entry_form ='2' THEN quantity ELSE 0 END) AS grey_receive,
								
								sum(CASE WHEN entry_form ='46' and trans_type=3 THEN quantity ELSE 0 END) AS recv_rtn_qnty,
								sum(CASE WHEN entry_form ='52' and trans_type=4 THEN quantity ELSE 0 END) AS iss_retn_qnty,
								
								sum(CASE WHEN entry_form ='3' and issue_purpose!=2 THEN quantity ELSE 0 END) AS issue_qnty,
								sum(CASE WHEN entry_form ='9' THEN quantity ELSE 0 END) AS return_qnty,
								sum(CASE WHEN entry_form ='11' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_yarn,
								sum(CASE WHEN entry_form ='11' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_yarn,
								
								sum(CASE WHEN entry_form ='15' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty,
								sum(CASE WHEN entry_form ='15' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty,							
							
								sum(CASE WHEN entry_form ='37' THEN quantity ELSE 0 END) AS finish_purchase								
								
								
								from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form in(7,66,2,46,52,11,3,9,15,37) group by po_breakdown_id");
		foreach($dataArrayTrans as $row)
		{
			
			$finish_receive_qnty_arr[$row[csf('po_breakdown_id')]]=$row[csf('finish_receive')]+$row[csf('finish_receive_roll_wise')];
			$grey_receive_qnty_arr[$row[csf('po_breakdown_id')]]=$row[csf('grey_receive')];
			//$yarn_issued[$row[csf('po_breakdown_id')]]+=($row[csf('issue_qnty')]+$row[csf('transfer_in_qnty_yarn')])+($row[csf('return_qnty')]+$row[csf('transfer_out_qnty_yarn')]);
			$yarn_issued[$row[csf('po_breakdown_id')]]=$row[csf('issue_qnty')]+($row[csf('transfer_in_qnty_yarn')]-$row[csf('transfer_out_qnty_yarn')]);
			$net_tensfer[$row[csf('po_breakdown_id')]]=($row[csf('transfer_in_qnty')]-$row[csf('transfer_out_qnty')]);
			$fin_rec_ret[$row[csf('po_breakdown_id')]]+=$row[csf('recv_rtn_qnty')];
		}	
			
			
		$sql_fin_purchase="select c.po_breakdown_id, sum(c.quantity) as finish_purchase from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.receive_basis<>9 and a.entry_form=37 and c.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.po_breakdown_id";
		$dataArrayFinPurchase=sql_select($sql_fin_purchase);
		foreach($dataArrayFinPurchase as $finRow)
		{
			$fin_purchase[$finRow[csf('po_breakdown_id')]]=$finRow[csf('finish_purchase')];
		}
			//----------------------------------
			
			$excess_percent_arr=array();
			$standard_excess_cut=sql_select("select id, company_name, slab_rang_start, slab_rang_end, excess_percent from variable_prod_excess_slab");
			foreach( $standard_excess_cut as $excRow)
			{
				$excess_percent_arr[$excRow[csf('company_name')]][$excRow[csf('id')]]['start']=$excRow[csf('slab_rang_start')];
				$excess_percent_arr[$excRow[csf('company_name')]][$excRow[csf('id')]]['end']=$excRow[csf('slab_rang_end')];
				$excess_percent_arr[$excRow[csf('company_name')]][$excRow[csf('id')]]['percent']=$excRow[csf('excess_percent')];
			}
			//echo $start_date.'=='.$current_month_end_date;
            if ($start_date!="") $str_cond3="and a.pub_shipment_date between '$start_date' and '$current_month_end_date' "; else $str_cond3="";
            
            $ii=1; $k=1; $total_po_qnty=0; $total_cut_qnty=0; $total_sew_qnty=0; $total_finish_qnty=0; $total_ship_qnty=0; $total_balance_qnty=0;
			
            $month_array=array();
		   $sql_orderlevel="SELECT b.team_leader,b.dealing_marchant,a.id, a.po_number, a.pub_shipment_date,a.extended_ship_date, b.order_uom, a.details_remarks, b.buyer_name, b.agent_name, b.company_name, b.style_ref_no, b.gmts_item_id, b.job_no_prefix_num, a.shiping_status, a.job_no_mst, a.po_quantity, b.total_set_qnty, a.plan_cut, (a.unit_price/b.total_set_qnty) as unit_price,set_smv from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name like '$company_id' and b.job_no_prefix_num like '%$txt_job_number%' and LOWER(b.style_ref_no) like LOWER('%$txt_style_number%')   and a.shiping_status!=3 and LOWER(a.po_number) like LOWER('%$txt_po_number%') and a.po_quantity like '%$txt_order_qnty%'  and a.is_confirmed=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_type_cond $agent_cond $item_cond $str_cond3 order by a.pub_shipment_date DESC";
		$sql_order_level=sql_select($sql_orderlevel);
			
			$row_tot=count($sql_order_level);
			//$current_month_enddate=change_date_format($current_month_end_date);
			
            foreach( $sql_order_level as $row_order_level)
            {
                if ($ii%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$pub_shipment_date=date("Y-m-d",strtotime($row_order_level[csf('pub_shipment_date')]));//change_date_format($row_order_level[csf('pub_shipment_date')]);
				$extended_ship_date=date("Y-m-d",strtotime($row_order_level[csf('extended_ship_date')]));//change_date_format($row_order_level[csf('extended_ship_date')]);
				//echo $extended_ship_date.'<br>';
				if($row_order_level[csf('extended_ship_date')]=='') $extended_ship_date=$current_month_enddate;
				//echo $extended_ship_date.'='.$current_month_enddate;
				
					//echo $extended_ship_date.'='.'<br>'.$current_month_enddate;
					$template_id=$template_id_arr[$row_order_level[csf('id')]];
                
               		if($row_order_level[csf('shiping_status')]==2) $ex_factory_qnty=$sql_summary_ex_factory[$row_order_level[csf('id')]]; else $ex_factory_qnty=0;
                    
                $po_quantity=($row_order_level[csf('po_quantity')]*$row_order_level[csf('total_set_qnty')]);
				$plan_cut_qty=$row_order_level[csf('plan_cut')]*$row_order_level[csf('total_set_qnty')];
				if($row_order_level[csf('extended_ship_date')]!='')
				{
              	  $month=date("Y-m",strtotime($row_order_level[csf('extended_ship_date')]));
				}
				else
				{
					$month=date("Y-m",strtotime($row_order_level[csf('pub_shipment_date')]));
				}
				$pending_po_value=(($po_quantity-$ex_factory_qnty)*$row_order_level[csf('unit_price')]);
               
			   $days_remian=datediff('d',date('d-m-Y',time()),$row_order_level[csf('pub_shipment_date')])-1;
			   if($extended_ship_date<=$current_month_enddate) //Check here
				{
			    if(!in_array($month, $month_array))
                {
                    if ($k!=1)
                    {
                    ?>
                        <tr bgcolor="#CCCCCC">
                            <td colspan="8" align="right"><b>Monthly Total</b></td>
                            <td></td>
                            <td align="right"><? echo  number_format($monthly_total_po_qnty,0);?></td>
                            <td align="right"><? echo  number_format($monthly_total_planCut_qnty,0);?></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            
                            <td>&nbsp;</td>
                            <td align="right"><? echo  number_format($monthly_total_yarn_bal,0);?></td>
                            <td align="right"><? echo  number_format($monthly_total_knit_bal,0);?></td>
                            <td align="right"><? echo  number_format($monthly_total_fin_bal,0);?></td>
                            
                            <td align="right"><? echo number_format($monthly_total_cut_qnty,0); ?></td>
                            <td>&nbsp;</td>
                            <td align="right"><? echo number_format($monthly_total_sew_qnty,0); ?></td>
                            <td align="right"><? echo number_format($monthly_total_finish_qnty,0); ?></td>
                            <td align="right"><? echo number_format($monthly_total_po_qnty-$monthly_total_finish_qnty,0); ?></td>
                            <td align="right"><? echo number_format($monthly_total_ship_qnty,0); ?></td>
                            <td align="right"><? echo number_format($monthly_total_po_qnty - $monthly_total_ship_qnty,0); ?></td>
                            <td align="right"><? echo number_format($monthly_pending_po_value,2); ?></td>
                            <td align="right"><? echo number_format($monthlyPendingSAH,2); ?></td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    	<?
                        $monthly_total_po_qnty = 0;
						$monthly_total_planCut_qnty=0;
                        $monthly_total_cut_qnty = 0;
                        $monthly_total_sew_qnty = 0;
                        $monthly_total_finish_qnty = 0;
                        $monthly_total_ship_qnty = 0;
						$monthly_total_yarn_bal = 0;
						$monthly_total_knit_bal = 0;
						$monthly_total_fin_bal = 0;
						$monthly_pending_po_value=0;
						$monthlyPendingSAH=0;
						
						
                      }
                    $k++;
                    ?>
                    <tr bgcolor="#EFEFEF">
                        <td colspan="29"><b><?php 
						if($row_order_level[csf('extended_ship_date')]!='')  echo date("F",strtotime($row_order_level[csf('extended_ship_date')])).", ".date("Y",strtotime($row_order_level[csf('extended_ship_date')]));
						else echo date("F",strtotime($row_order_level[csf('pub_shipment_date')])).", ".date("Y",strtotime($row_order_level[csf('pub_shipment_date')]));
						
						?></b></td>
                    </tr>
                <?
                    $month_array[]=$month;
                }
				
				$garments_name_details="";
				$ex_gmts_item=explode(',',$row_order_level[csf('gmts_item_id')]);
				foreach($ex_gmts_item as $item_id)
				{
					if ($garments_name_details=="") $garments_name_details=$garments_item[$item_id]; else $garments_name_details.=', '.$garments_item[$item_id];
				}
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $ii;?>','<? echo $bgcolor;?>')" id="tr_<? echo $ii;?>">
                    <td width="40"><? echo $ii; ?></td>
                    <td width="70" align="center"><? echo $row_order_level[csf('job_no_prefix_num')]; ?></td>
                    <td width="80"><div style="word-wrap:break-word; width:80px"><? echo $buyer_short_name_arr[$row_order_level[csf('buyer_name')]]; ?></div></td>
                    <td width="80"><div style="word-wrap:break-word; width:80px"><? echo $buyer_short_name_arr[$row_order_level[csf('agent_name')]]; ?></div></td>
                    
                    <td width="110"><div style="word-wrap:break-word; width:110px"><a href="##" onClick="order_dtls_popup('<? echo $row_order_level[csf('job_no_mst')];?>', '<? echo $row_order_level[csf('id')]; ?>','<? echo $template_id; ?>','<? echo $tna_process_type; ?>')"><? echo $row_order_level[csf('po_number')]; ?></a></div></td>
                    <td width="50" onclick="openmypage_image('requires/capacity_and_order_booking_status_controller.php?action=show_image&job_no=<? echo $row_order_level[csf('job_no_mst')]; ?>','Image View')"><img src='../../../<? echo $imge_arr[$row_order_level[csf('job_no_mst')]]; ?>' height='25' width='30' /></td>
                    <td width="120"><div style="word-wrap:break-word; width:120px"><? echo $row_order_level[csf('style_ref_no')]; ?></div></td>
                    <td width="140"><div style="word-wrap:break-word; width:140px"> <? echo $garments_name_details;?></div></td>
                    <td width="60" align="right"><? echo $row_order_level[csf('set_smv')]; ?></td>
                    <td align="right" width="100"><? echo number_format($po_quantity,0); ?></td>
                    <td align="right" width="100"><? echo number_format($plan_cut_qty,0); ?></td>
                    <td width="90" align="center" title="Ext. Shipdate<? echo $row_order_level[csf('extended_ship_date')];?>"><? echo change_date_format($row_order_level[csf('pub_shipment_date')],'dd-mm-yyyy','-'); ?></td>
                    <td width="60" align="center" bgcolor="<? //echo $color; ?>" ><? echo $days_remian; ?> </td>
                    
                    <td width="90"><p><? echo implode(',',$bookingDataArr[$row_order_level[csf('id')]]); ?></p></td>
                    <td width="90" align="right">
						<?  
                           // $yarn_bal=$tot_req_qnty[$row_order_level[csf('id')]]-$yarn_issued[$row_order_level[csf('id')]];
						   echo $yarn_bal=number_format($tot_req_qnty[$row_order_level[csf('id')]]-$yarn_issued[$row_order_level[csf('id')]],2);
                        ?> 
                    </td>
                    <td width="90" align="right"><?  
					echo $knit_bal=number_format($tot_req_qnty[$row_order_level[csf('id')]]-$grey_receive_qnty_arr[$row_order_level[csf('id')]],2); 
					?> </td>
                    <td width="90" align="right"><?  
							//$finish_bal = $tot_fin_req_qnty[$row_order_level[csf('id')]]-(($fin_purchase[$row_order_level[csf('id')]]-$fin_rec_ret[$row_order_level[csf('id')]])+$net_tensfer[$row_order_level[csf('id')]]+$finish_receive_qnty_arr[$row_order_level[csf('id')]]);			
							echo $finish_bal =number_format($tot_fin_req_qnty[$row_order_level[csf('id')]]-(($fin_purchase[$row_order_level[csf('id')]]-$fin_rec_ret[$row_order_level[csf('id')]])+$net_tensfer[$row_order_level[csf('id')]]+$finish_receive_qnty_arr[$row_order_level[csf('id')]]),2);
					
					?> </td>
                    
                    <td align="right" width="90"><? echo number_format($cutting_qnty[$row_order_level[csf('id')]],0);  ?></td>
                    <?
					foreach($excess_percent_arr[$row_order_level[csf('company_name')]] as $key=>$value)
					{
						//echo $key."<br>";
						$start_limit=$excess_percent_arr[$row_order_level[csf('company_name')]][$key]['start'];
						$end_limit=$excess_percent_arr[$row_order_level[csf('company_name')]][$key]['end'];
						
						if($po_quantity>=$start_limit && $po_quantity<=$end_limit)
						{
							$excess_percent=$excess_percent_arr[$row_order_level[csf('company_name')]][$key]['percent'];
							//$subtotal+=$excess_percent;
							break;
						}
						else $excess_percent=0;
					}
					//echo $excess_percent;
                    //list($row11)=$standard_excess_cut;
                   // $excess_percent= $row11[('excess_percent')];
                    $actual_excess_cut=0; $actual_excess_cut2=0;
                    $actual_excess_cut=(($cutting_qnty[$row_order_level[csf('id')]]-$po_quantity)/$po_quantity)*100;
                    $exceed_cut=$actual_excess_cut - $excess_percent;
					
                    if($actual_excess_cut>$excess_percent) $bg_color="red"; else $bg_color="green";	

                   // $actual_excess_cut=round($actual_excess_cut,2);
                    $actual_excess_cut2=$cutting_qnty[$row_order_level[csf('id')]]-$po_quantity;
                    if($actual_excess_cut2==0 || $actual_excess_cut2<0)
                    {
                    ?>
                        <td align="left" width="80" title="Cutting Qnty Not Exceed Order Qnty" bgcolor="<? echo $bg_color ?>"><? echo "N/A"; ?></td>
                    <?
                    }
                    else
                    {
                    ?>
                        <td align="right" width="80" bgcolor="<? echo $bg_color ?>"><? echo number_format($actual_excess_cut,2)." %"; ?></td>
                    <?
                    }
                    ?>
                    <td align="right" width="90"><? echo $sewing_tot= number_format($sewingin_qnty[$row_order_level[csf('id')]],0); ?></td>
                    <td align="right" width="90"><? echo  $finish_tot= number_format($finish_qnty[$row_order_level[csf('id')]],0); ?></td>
                    <td align="right" width="90"><? echo number_format($po_quantity-$finish_qnty[$row_order_level[csf('id')]],0); ?></td>
                    <td align="right" width="90"><? echo number_format($ex_factory_qnty,0); ?></td>
                    <td align="right" width="100"><? echo number_format(($po_quantity - $ex_factory_qnty),0);?> </td>
                    <td align="right" width="100"><? echo $pending_po_tot= number_format($pending_po_value,0);?> </td>
                    <td align="right" width="90"><? echo number_format((($po_quantity - $ex_factory_qnty)*$row_order_level[csf('set_smv')])/60,2); ?></td>
                    <td align="right" width="100"><? 
					$team_leader_contact=$company_team_leader_contact_arr[$row_order_level[csf('team_leader')]];
					echo $team_leader_arr[$row_order_level[csf('team_leader')]].'<br>'.$team_leader_contact;;?></td>
                    <td align="right" width="100"><? echo 
					$team_member_contact_no=$company_team_member_contact_arr[$row_order_level[csf('dealing_marchant')]];
					$company_team_member_name_arr[$row_order_level[csf('dealing_marchant')]].'<br>'.$team_member_contact_no; ?></td>
                    <td><div style="word-wrap:break-word; width:110px"><? echo $row_order_level[csf('details_remarks')]; ?></div></td>
               </tr>
			<?
                $monthly_total_po_qnty+=$po_quantity;
				$monthly_total_planCut_qnty+=$plan_cut_qty;
                $monthly_total_cut_qnty+=$cutting_qnty[$row_order_level[csf('id')]];
                $monthly_total_sew_qnty+=$sewingin_qnty[$row_order_level[csf('id')]];
                $monthly_total_finish_qnty+=$finish_qnty[$row_order_level[csf('id')]];
                $monthly_total_ship_qnty+=$ex_factory_qnty;
				
				$monthly_total_yarn_bal += str_replace(",", "", $yarn_bal);
				$monthly_total_knit_bal += str_replace(",", "", $knit_bal);
				$monthly_total_fin_bal +=str_replace(",", "", $finish_bal) ;
				$monthly_pending_po_value	+=str_replace(",", "", $pending_po_tot);				
				$monthlyPendingSAH+=(($po_quantity - $ex_factory_qnty)*$row_order_level[csf('set_smv')])/60;
				
                
                $total_po_qnty+=$po_quantity;
				$total_planCut_qty+=$plan_cut_qty;
                $total_cut_qnty+=$cutting_qnty[$row_order_level[csf('id')]];
				
				
				$total_sew_qnty+= str_replace(",", "", $sewing_tot);
                //$total_sew_qnty+= $cutting_qnty[$row_order_level[csf('id')]];	
                $total_finish_qnty+=str_replace(",", "", $finish_tot);
                $total_ship_qnty+=$ex_factory_qnty;
				
				$total_yarn_bal += str_replace(",", "", $yarn_bal);
				$total_knit_bal += str_replace(",", "", $knit_bal);
				$total_fin_bal += str_replace(",", "", $finish_bal);
				
				/*$total_yarn_bal += $monthly_total_yarn_bal;
				$total_knit_bal += $monthly_total_knit_bal;
				$total_fin_bal += $monthly_total_fin_bal;*/
				$total_pending_po_value	+=str_replace(",", "", $pending_po_tot);
				
				$total_PendingSAH +=(($po_quantity - $ex_factory_qnty)*$row_order_level[csf('set_smv')])/60;
							
                
                $ii++;
				}
            }
			if($row_tot>0)
			{
			?>
				<tr bgcolor="#CCCCCC">
					<td colspan="8" align="right"><b>Monthly Total</b></td>
					<td align="right"></td>
					<td align="right"><? echo  number_format($monthly_total_po_qnty,0);?></td>
                    <td align="right"><? echo  number_format($monthly_total_planCut_qnty,0);?></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
                    
					<td>&nbsp;</td>
                    <td align="right"><? echo number_format($monthly_total_yarn_bal,2);?></td>
                    <td align="right"><? echo number_format($monthly_total_knit_bal,2);?></td>
                    <td align="right"><? echo number_format($monthly_total_fin_bal,2);?></td>
                    
					<td align="right"><? echo number_format($monthly_total_cut_qnty,0); ?></td>
					<td>&nbsp;</td>
					<td align="right"><? echo number_format($monthly_total_sew_qnty,0); ?></td>
					<td align="right"><? echo number_format($monthly_total_finish_qnty,0); ?></td>
					<td align="right"><? echo number_format($monthly_total_po_qnty-$monthly_total_finish_qnty,0); ?></td>
					<td align="right"><? echo number_format($monthly_total_ship_qnty,0); ?></td>
					<td align="right"><? echo number_format($monthly_total_po_qnty - $monthly_total_ship_qnty,0); ?></td>
                    <td align="right"><? echo number_format($monthly_pending_po_value,2); ?></td>
					<td align="right"><? echo number_format($monthlyPendingSAH,2); ?></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
			<?	
			}
			?>
            </table>
            </div>
            <table cellspacing="0" cellpadding="0"  width="2660px"  border="1" rules="all" class="tbl_bottom">
             <tr>
             	<td width="40">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="110">&nbsp;</td>
                <td width="50">&nbsp;</td>
                <td width="120">&nbsp;</td>
                <td width="140" align="right">Grand Total</td>
                <td width="60"></td>
                <td width="100" align="right"><? echo  number_format($total_po_qnty,0);?></td>
                <td width="100" align="right"><? echo  number_format($total_planCut_qty,0);?></td>
                <td width="90">&nbsp;</td>
                <td width="60">&nbsp;</td>
                
                <td width="90">&nbsp;</td>
                <td width="90" align="right"><? echo  number_format($total_yarn_bal,2);?></td>
                <td width="90" align="right"><? echo  number_format($total_knit_bal,2);?></td>
                <td width="90" align="right"><? echo  number_format($total_fin_bal,2);?></td>
                
                <td width="90" align="right"><? echo number_format($total_cut_qnty,0); ?></td>
                <td width="80">&nbsp;</td>
                <td width="90" align="right"><? echo number_format($total_sew_qnty,0); ?></td>
                <td width="90" align="right"><? echo number_format($total_finish_qnty,0); ?></td>
                <td width="90" align="right"><? echo number_format($total_po_qnty-$total_finish_qnty,0); //number_format($total_planCut_qty-$total_finish_qnty,0); ?></td>
                <td width="90" align="right"><? echo number_format($total_ship_qnty,0); ?></td>
                <td width="100" align="right"><? echo number_format($total_po_qnty - $total_ship_qnty,0); ?></td>
                <td width="100" align="right"><? echo number_format($total_pending_po_value,2); ?></td>
                <td width="90" align="right"><? echo number_format($total_PendingSAH ,2); ?></td>
                <td width="100">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </table> 
    </fieldset> 
</div>   
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
    echo "$html####$filename"; 
    exit();
	/*$html = ob_get_contents();
	ob_clean();
	 
	foreach (glob($_SESSION['logic_erp']['user_id']."*.xls") as $filename) {
	@unlink($filename);
	}
	$name=time();
	$filename=$_SESSION['logic_erp']['user_id']."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,$html);
	echo "$html****$filename";*/
		
}  // end if($type=="sewing_production_summary")

if($action=="report_generate2")
{ 
	$data=explode("_",$data);
	if($data[0]==0) $company_name="%%"; else $company_name=$data[0];
	//if(trim($data[1])!="") $job_number="%".$data[1]."%"; else  $job_number="%%";
	//if(trim($data[2])!="") $txt_style_number="%".$data[2]."%"; else $txt_style_number="%%";
	//if(trim($data[3])!="") $txt_po_number="%".trim($data[3])."%"; else $txt_po_number="%%";
	//if(trim($data[4])!="") $txt_order_qnty="%".$data[4]."%"; else $txt_order_qnty="%%";
	//if(trim($data[5])!="") $buyer_name="%".$data[5]."%"; else $buyer_name="%%";
	$company_id=str_replace("'","",$cbo_company_id);
	$txt_job_number=str_replace("'","",$txt_job_number);
	$buyer_name=str_replace("'","",$buyer_name);
	$txt_agent_name=str_replace("'","",$txt_agent_name);
	$txt_po_number=str_replace("'","",$txt_po_number);
	$txt_style_number=str_replace("'","",$txt_style_number);
	$txt_order_qnty=str_replace("'","",$txt_order_qnty);
	$cbo_date_category=str_replace("'","",$cbo_date_category);
	$cbo_location_id=str_replace("'","",$cbo_location_id);
	$txt_internal_file=str_replace("'","",$txt_internal_file);
	$txt_from_date=str_replace("'","",$txt_from_date);
	$txt_to_date=str_replace("'","",$txt_to_date);
	//echo $cbo_date_category.'XXX';
		
	/*	if($cbo_location_id=="") $location_id=""; else $location_id=$cbo_location_id;
		if($location_id!=0)
		{
			$home_page_location_con=" and b.location_name like '$location_id'";
		}
	*/

	//echo 'company_id='.$company_id. '&&txt_job_number='.$txt_job_number.  '&&buyer_name='.$buyer_name. '&&txt_agent_name='.$txt_agent_name. '&&txt_po_number='.$txt_po_number. '&&txt_style_number='.$txt_style_number. '&&txt_order_qnty='.$txt_order_qnty. '&&cbo_date_category='.$cbo_date_category. '&&cbo_location_id='.$cbo_location_id. '&&txt_internal_file='.$txt_internal_file. '&&txt_from_date='.$txt_from_date. '&&txt_to_date='.$txt_to_date;
	
	if($cbo_location_id!=0)
	{
		$location_con=" and b.location_name=$cbo_location_id";
	}
	
	//echo $home_page_location_con;
	if(trim($buyer_name)!="") 
	{
		$buyer_type_cond="and b.buyer_name in(select id from lib_buyer where LOWER(buyer_name) like LOWER('%$buyer_name%') and status_active=1 and is_deleted=0)";	
	}
	else
	{
		$buyer_type_cond="";
	}
	if(trim($txt_agent_name)!="") 
	{
		$agent_cond="and b.agent_name in(select a.id from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company=$cbo_company_id and LOWER(a.buyer_name) like LOWER('%$txt_agent_name%') and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (20,21)))";	
	}
	else
	{
		$agent_cond="";
	}
	


	if($txt_from_date && $txt_to_date){
		$start_month=date("Y-m",strtotime($txt_to_date));
		$start_date=date("Y-m d",strtotime($txt_to_date));
		$end_date=date("Y-m d",strtotime($txt_from_date));
		$end_date2=date("Y-m d",strtotime($txt_from_date));
		$previous_month_end_date=date("Y-m d",strtotime($txt_from_date));
	}
	else{
		$end_date=date("Y-m-01"); 
		$start_month=date("Y-m",strtotime($start_date));
	}

	//echo $end_date2.'DD';
	if($db_type==2)
	{
		$start_date=change_date_format($start_date,'yyyy-mm-dd','-',1);
		$end_date2=change_date_format($end_date2,'yyyy-mm-dd','-',1);
	}
	
	//$total_months=datediff("m",$start_month,$end_month);
	//$dd=strtotime(date("Y-m-d", time()))
	$current_month_end_date=date("Y-m-d",strtotime("-1 days"));
	if($end_date!="")
	{
		$str_cond="and a.pub_shipment_date between '$start_date' and '$previous_month_end_date'";
		
		$end_date3=date("Y-m-01",strtotime($end_date2));
		if($db_type==2)
		{
			$end_date3=change_date_format($end_date3,'yyyy-mm-dd','-',1);
		}
		$str_cond_curr="and a.pub_shipment_date between '$end_date3' and '$current_month_end_date'";
	}
	else
	{
		$str_cond="";
		$str_cond_curr="";
	}
	//echo $str_cond .'=='.$str_cond_curr;die;
	
	if($db_type==0)
	{
		$date_start= change_date_format($current_month_end_date,"yyyy-mm-dd");
		//$date_end= change_date_format($date_end,"yyyy-mm-dd");
	}
	else
	{
		$date_start= change_date_format($current_month_end_date,"dd-mm-yyyy","-",1);
		//$date_end= change_date_format($date_end,"dd-mm-yyyy","-",1);
	}
	
	$str_cond="and a.pub_shipment_date<='$date_start' ";

	if($txt_from_date && $txt_to_date){
		$str_cond="and a.pub_shipment_date between '$txt_from_date' and  '$txt_to_date' ";
	}

	//echo $str_cond;die;
	
	$sql_summary_ex_factory=return_library_array("SELECT po_break_down_id,sum(ex_factory_qnty) AS ex_factory_qnty from pro_ex_factory_mst  where status_active=1 and is_deleted=0 group by po_break_down_id",'po_break_down_id','ex_factory_qnty');
	if($txt_internal_file)
	{
		$int_ref_cond="and LOWER(a.grouping) like LOWER('%$txt_internal_file%')";
	}
	else $int_ref_cond="";
	
	  $production_sql="select c.po_break_down_id as po_id,
		sum(CASE WHEN c.production_type =1 THEN c.production_quantity ELSE 0 END) AS cutting_qnty,
		sum(CASE WHEN c.production_type =5 THEN c.production_quantity ELSE 0 END) AS sewing_out_qnty,	
		sum(CASE WHEN c.production_type =8 THEN c.production_quantity ELSE 0 END) AS finish_qnty
		from  pro_garments_production_mst c ,wo_po_break_down a,wo_po_details_master b
		where c.po_break_down_id=a.id and a.is_confirmed=1 and a.shiping_status!=3  and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.production_type in(1,5,8) and b.job_no=a.job_no_mst and b.company_name like '$company_id' $location_con and b.job_no_prefix_num like '%$txt_job_number%' and LOWER(b.style_ref_no) like LOWER('%$txt_style_number%')   and LOWER(a.po_number) like LOWER('%$txt_po_number%') and a.po_quantity like '%$txt_order_qnty%' $int_ref_cond   $buyer_type_cond $agent_cond $item_cond $str_cond 
		group by c.po_break_down_id order by c.po_break_down_id";
		//echo $production_sql;die;
		
		
		 // and LOWER(a.po_number) like LOWER('%$txt_internal_file%')
		
		$production_result=sql_select($production_sql);
		
		$tot_cutting_qnty=$tot_sewing_out_qnty=$tot_finish_qnty=$tot_prev_cut_qnty=$tot_prev_sew_qnty=$tot_prev_finish_qnty=$tot_current_sewing_qty=$tot_current_finish_qty=0;
		$tot_plan_cut_qnty=0;
		foreach($production_result as $row)
		{
			$prod_qty_arr[$row[csf('po_id')]]['cutting_qnty']=$row[csf('cutting_qnty')];
			$prod_qty_arr[$row[csf('po_id')]]['sewing_out_qnty']=$row[csf('sewing_out_qnty')];
			$prod_qty_arr[$row[csf('po_id')]]['finish_qnty']=$row[csf('finish_qnty')];
			
			if($extended_ship_date!='')
			{
				$pub_current_month=date("Y-m", strtotime($extended_ship_date));
			}
			else
			{
				$pub_current_month=date("Y-m", strtotime($pub_ship_date));
			}
			
			if($pub_current_month==$current_month)//Current Month
			{
				//$curr_po_qty=$po_wise_arr2[$row[csf('po_id')]]['prev_po_qty'];
				////$curr_plan_cut=$po_wise_arr2[$row[csf('po_id')]]['prev_plan_cut'];
				//$tot_current_cut_qty+=$curr_plan_cut-$row[csf('cutting_qnty')];
				//$tot_current_sewing_qty+=$curr_po_qty-$row[csf('sewing_out_qnty')];
				//$tot_current_finish_qty+=$curr_po_qty-$row[csf('finish_qnty')];
			}
			
		}
		//echo $tot_plan_cut_qnty.'D';
		unset($production_result);
		//print_r($prod_qty_arr);
		if($txt_internal_file) $txt_internal_fileCond="and LOWER(a.grouping) like LOWER('%$txt_internal_file%')";else $txt_internal_fileCond="";
	
		$sql_orderlevel="SELECT b.team_leader,b.dealing_marchant,a.id, a.po_number, a.pub_shipment_date,a.extended_ship_date,a.sea_discount,a.air_discount,a.extend_ship_mode,a.po_total_price, b.order_uom, a.details_remarks, b.buyer_name, b.agent_name, b.company_name, b.style_ref_no, b.gmts_item_id, b.job_no_prefix_num, a.shiping_status, a.job_no_mst, a.grouping, a.po_quantity, b.total_set_qnty, a.plan_cut, (a.unit_price/b.total_set_qnty) as unit_price,b.set_smv from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name like '$company_id'  $location_con and b.job_no_prefix_num like '%$txt_job_number%' and LOWER(b.style_ref_no) like LOWER('%$txt_style_number%') and a.shiping_status!=3 and LOWER(a.po_number) like LOWER('%$txt_po_number%') and a.po_quantity like '%$txt_order_qnty%'    and a.is_confirmed=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $txt_internal_fileCond $buyer_type_cond $agent_cond $item_cond $str_cond order by a.pub_shipment_date DESC";
		
		
		
		
		$sql_order_level=sql_select($sql_orderlevel);
		$row_tot=count($sql_order_level);
		$tot_pending_po_qty_previ=$tot_prev_po_val=$tot_prev_PendingSAH=$tot_pre_plan_cut=$tot_current_pending_po_quantity=$tot_current_pending_po_val=$tot_current_PendingSAH=$tot_current_plan_cut=0;
		
		$all_po_id="";$curr_po_id="";
		$end_month=date("Y-m",strtotime("-1 days"));
		$current_month=date("Y-m", strtotime($end_month));
		//$current_month=date("Y-m", strtotime($current_month));
		//echo $current_month;die;
		$buyer_wise_arr=array();
		foreach($sql_order_level as $row)
		{
			if($all_po_id=="") $all_po_id=$row[csf("id")]; else $all_po_id.=",".$row[csf("id")];
			$po_id_arr[$row[csf("id")]]=$row[csf("id")];
			$pub_shipment_date=date("Y-m-d",strtotime($row[csf('pub_shipment_date')]));
			$extended_ship_date=date("Y-m-d",strtotime($row[csf('extended_ship_date')]));
			//echo $cbo_date_category.'DD';
			if($cbo_date_category==2) //Exten Ship
			{
				if($extended_ship_date=='')
				{
					 $extended_ship_date=$current_month_end_date;
					 //echo $extended_ship_date.'x';
				}
				//echo $date_cond_dynamic="($extended_ship_date<=$current_month_end_date)";
				$date_cond_dynamic = ($extended_ship_date<=$current_month_end_date);
			}
			else
			{
				//if($row[csf('extended_ship_date')]!='') 
				$extended_ship_date=$pub_shipment_date;
				$date_cond_dynamic= ($pub_shipment_date!='');
			}
			//echo $date_cond_dynamic.',';
				//echo $extended_ship_date.'='.$current_month_end_date.'<br>';
			//if($extended_ship_date<=$current_month_end_date) //Check here
			if($date_cond_dynamic) //Check here
			{
			
				$month_date='';
				
				if($row[csf('extended_ship_date')]!='')
				{
					if($cbo_date_category==1)
					{
					 $month_date=date("Y-m",strtotime($row[csf('pub_shipment_date')]));
					 $pub_current_month=date("Y-m", strtotime($row[csf('pub_shipment_date')]));
					}
					else
					{
					 $month_date=date("Y-m",strtotime($row[csf('extended_ship_date')]));
					
					 $pub_current_month=date("Y-m", strtotime($row[csf('extended_ship_date')]));
					}
					 
				}
				else
				{
					if($row[csf('extended_ship_date')]!='')
					{
						if($cbo_date_category==1)
						{
							$month_date=date("Y-m",strtotime($row[csf('pub_shipment_date')]));
							$pub_current_month=date("Y-m", strtotime($row[csf('pub_shipment_date')]));
						}
						else
						{
							$month_date=date("Y-m",strtotime($row[csf('extended_ship_date')]));
							$pub_current_month=date("Y-m", strtotime($row[csf('extended_ship_date')]));
						}
					}
					else
					{
						if($cbo_date_category==1)
						{
							$month_date=date("Y-m",strtotime($row[csf('pub_shipment_date')]));
							$pub_current_month=date("Y-m", strtotime($row[csf('pub_shipment_date')]));
						}
						else
						{
							$month_date=date("Y-m",strtotime($row[csf('pub_shipment_date')]));
							$pub_current_month=date("Y-m", strtotime($row[csf('pub_shipment_date')]));
						}
					}
					
				}
				if($row[csf('shiping_status')]==2)
					{
						$ex_fact_qty=$sql_summary_ex_factory[$row[csf('id')]];
					}
					else
					{
						$ex_fact_qty=0;
					}
					$plan_cut_qty=$row[csf('plan_cut')]*$row[csf('total_set_qnty')];
					//echo $ex_fact_qty.'='.$row[csf('shiping_status')].'<br>';
					if($current_month==$pub_current_month)//Current Month
					{
						$curr_po_quantity=$row[csf('po_quantity')]*$row[csf('total_set_qnty')];
						if(($curr_po_quantity-$ex_fact_qty)>0){
							$tot_current_pending_po_quantity+=$curr_po_quantity-$ex_fact_qty;
							$tot_pending_qty=$curr_po_quantity-$ex_fact_qty;
						//	echo $pub_current_month.'='.$current_month.'<br>';
							if($tot_pending_qty>0)
							{
							$tot_current_pending_po_val+=$tot_pending_qty*$row[csf('unit_price')];
							}
							$tot_current_plan_cut+=$row[csf('plan_cut')]*$row[csf('total_set_qnty')];
							$tot_current_PendingSAH+=(($curr_po_quantity-$ex_fact_qty)*$row[csf('set_smv')])/60;
							//Production
							
							
							if($plan_cut_qty-$prod_qty_arr[$row[csf('id')]]['cutting_qnty']>0)
							{
								$tot_current_cut_qty+=$plan_cut_qty-$prod_qty_arr[$row[csf('id')]]['cutting_qnty'];
							}
							if($curr_po_quantity-$prod_qty_arr[$row[csf('id')]]['sewing_out_qnty']>0)
							{
								$tot_current_sewing_qty+=$curr_po_quantity-$prod_qty_arr[$row[csf('id')]]['sewing_out_qnty'];
							}
							if($curr_po_quantity-$prod_qty_arr[$row[csf('id')]]['finish_qnty']>0)
							{
								$tot_current_finish_qty+=$curr_po_quantity-$prod_qty_arr[$row[csf('id')]]['finish_qnty'];
							}
							
							//$tot_current_cut_qty+=$curr_plan_cut-$row[csf('cutting_qnty')];
							if($curr_po_id=="") $curr_po_id=$row[csf("id")]; else $curr_po_id.=",".$row[csf("id")];
						}
					}
					
					$prev_po_quantity=$row[csf('po_quantity')]*$row[csf('total_set_qnty')];
					$prev_plan_cut_qty=$row[csf('plan_cut')]*$row[csf('total_set_qnty')];
					$pending_po_quantity=$prev_po_quantity-$ex_fact_qty;
					if($prev_po_quantity-$prod_qty_arr[$row[csf('id')]]['finish_qnty']>0)
					{
						$tot_prev_finish_qnty+=$prev_po_quantity-$prod_qty_arr[$row[csf('id')]]['finish_qnty'];
					}
					if($prev_po_quantity-$prod_qty_arr[$row[csf('id')]]['sewing_out_qnty']>0)
					{
						$tot_prev_sew_qnty+=$prev_po_quantity-$prod_qty_arr[$row[csf('id')]]['sewing_out_qnty'];
					}
					
					if($prev_plan_cut_qty-$prod_qty_arr[$row[csf('id')]]['cutting_qnty']>0)
					{
						$tot_prev_cut_qnty+=$prev_plan_cut_qty-$prod_qty_arr[$row[csf('id')]]['cutting_qnty'];
					}
					
					if($pending_po_quantity>0)
					{
					$tot_prev_po_val+=$pending_po_quantity*$row[csf('unit_price')];
					}
					if(($prev_po_quantity-$ex_fact_qty)>0){
						$tot_prev_pending_po_qty_previ+=$prev_po_quantity-$ex_fact_qty;
					}
					if($pending_po_quantity>0)
					{
					$tot_prev_PendingSAH+=($pending_po_quantity*$row[csf('set_smv')])/60;
					}
					$pre_plan_cut_qty=$row[csf('plan_cut')]*$row[csf('total_set_qnty')];
					$tot_pre_plan_cut+=$pre_plan_cut_qty;
					//$prev_PendingSAH+=($pending_po_quantity*$row[csf('set_smv')])/60;
				//echo $month_date;die;
				if($pending_po_quantity>0)
				{
			 	$po_wise_arr[$month_date][$row[csf('id')]]['pending_po_quantity']=$pending_po_quantity;
				$po_wise_arr[$month_date][$row[csf('id')]]['shipout_qty']=$ex_fact_qty;
				$po_wise_arr[$month_date][$row[csf('id')]]['pending_po_value']=$pending_po_quantity*$row[csf('unit_price')];
				$po_wise_arr[$month_date][$row[csf('id')]]['po_total_price']=$row[csf('po_total_price')];
				$po_wise_arr[$month_date][$row[csf('id')]]['sea_discount']=$row[csf('sea_discount')];
				$po_wise_arr[$month_date][$row[csf('id')]]['air_discount']=$row[csf('air_discount')];
				$po_wise_arr[$month_date][$row[csf('id')]]['extend_ship_mode']=$row[csf('extend_ship_mode')];
				
				$po_wise_arr[$month_date][$row[csf('id')]]['pub_shipment_date']=$row[csf('pub_shipment_date')];
				$po_wise_arr[$month_date][$row[csf('id')]]['extended_ship_date']=$row[csf('extended_ship_date')];
				$po_wise_arr[$month_date][$row[csf('id')]]['dealing_marchant']=$row[csf('dealing_marchant')];
				
				$po_wise_arr[$month_date][$row[csf('id')]]['order_uom']=$row[csf('order_uom')];
				$po_wise_arr[$month_date][$row[csf('id')]]['details_remarks']=$row[csf('details_remarks')];
				$po_wise_arr[$month_date][$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
				$po_wise_arr[$month_date][$row[csf('id')]]['agent_name']=$row[csf('agent_name')];
				$po_wise_arr[$month_date][$row[csf('id')]]['company_name']=$row[csf('company_name')];
				$po_wise_arr[$month_date][$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
				$po_wise_arr[$month_date][$row[csf('id')]]['gmts_item_id']=$row[csf('gmts_item_id')];
				$po_wise_arr[$month_date][$row[csf('id')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
				$po_wise_arr[$month_date][$row[csf('id')]]['job_no_mst']=$row[csf('job_no_mst')];
				$po_wise_arr[$month_date][$row[csf('id')]]['po_quantity']=$row[csf('po_quantity')];
				$po_wise_arr[$month_date][$row[csf('id')]]['total_set_qnty']=$row[csf('total_set_qnty')];
				$po_wise_arr[$month_date][$row[csf('id')]]['plan_cut']=$row[csf('plan_cut')];
				$po_wise_arr[$month_date][$row[csf('id')]]['unit_price']=$row[csf('unit_price')];
				$po_wise_arr[$month_date][$row[csf('id')]]['set_smv']=$row[csf('set_smv')];
				$po_wise_arr[$month_date][$row[csf('id')]]['po_number']=$row[csf('po_number')];
				$po_wise_arr[$month_date][$row[csf('id')]]['grouping']=$row[csf('grouping')];
				$po_wise_arr[$month_date][$row[csf('id')]]['team_leader']=$row[csf('team_leader')];
				
				$po_wise_arr[$month_date][$row[csf('id')]]['prev_plan_cut']=$pre_plan_cut_qty;
				$po_wise_arr[$month_date][$row[csf('id')]]['prev_po_qty']=$prev_po_quantity;
				
					//Buyer wise summary
				$buyer_wise_arr[$month_date][$row[csf('buyer_name')]]['pending_poqty']+=$pending_po_quantity;
				$buyer_wise_arr[$month_date][$row[csf('buyer_name')]]['total_set_qnty']=$row[csf('total_set_qnty')];
				$buyer_wise_arr[$month_date][$row[csf('buyer_name')]]['plan_cut']+=$row[csf('plan_cut')];
				$buyer_wise_arr[$month_date][$row[csf('buyer_name')]]['unit_price']=$row[csf('unit_price')];
				$buyer_wise_arr[$month_date][$row[csf('buyer_name')]]['po_value']+=$pending_po_quantity*$row[csf('unit_price')];
				$buyer_wise_arr[$month_date][$row[csf('buyer_name')]]['buyer_sah']+=($pending_po_quantity*$row[csf('set_smv')])/60;
				
				$po_wise_arr2[$row[csf('id')]]['prev_plan_cut']=$pre_plan_cut_qty;
				$po_wise_arr2[$row[csf('id')]]['prev_po_qty']=$prev_po_quantity;
				$po_wise_arr2[$row[csf('id')]]['pub_shipment_date']=$row[csf('pub_shipment_date')];
				$po_wise_arr2[$row[csf('id')]]['extended_ship_date']=$row[csf('extended_ship_date')];
					
				}
				
			
			} //Check End
			
		}
		
		
		
		
		//echo $curr_po_id.'=';
		//print_r($po_wise_arr);die;
		//echo $tot_current_pending_po_quantity.'DD';
		$po_ids=count(array_unique(explode(",",$all_po_id)));
		$po_numIds=chop($all_po_id,','); $poIds_cond="";
		if($all_po_id!='' || $all_po_id!=0)
		{
			if($db_type==2 && $po_ids>1000)
			{
				$poIds_cond=" and (";
				$poIdsArr=array_chunk(explode(",",$po_numIds),990);
				foreach($poIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$poIds_cond.=" c.po_break_down_id  in($ids) or ";
				}
				$poIds_cond=chop($poIds_cond,'or ');
				$poIds_cond.=")";
			}
			else
			{
				$poIds_cond=" and  c.po_break_down_id  in($all_po_id)";
			}
		}
		//b.job_no=a.job_no_mst and b.company_name like '$company_id' and b.job_no_prefix_num like '%$txt_job_number%' and LOWER(b.style_ref_no) like LOWER('%$txt_style_number%') and a.shiping_status!=3 and LOWER(a.po_number) like LOWER('%$txt_po_number%') and a.po_quantity like '%$txt_order_qnty%'   and a.is_confirmed=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_type_cond $agent_cond $item_cond $str_cond order by a.pub_shipment_date DESC
		
		//die;
	ob_start();	
	?>
	<!--=============================================================Total Summary Start=============================================================================================-->
    <div style="width:1300px;">
    <table width="1300px" style=" float:left;"  cellspacing="0">
        <tr>
            <td colspan="7" align="center" ><font size="3"><strong><?php echo $company_details[$company_id]; ?></strong></font></td>
        </tr>
        <tr class="form_caption">
            <td colspan="7" align="center"><font size="3"><strong>Total Pending Order Summary </strong></font></td>
        </tr>
    </table>
	<table border="1" style=" float:left;" rules="all" class="rpt_table" width="1200">
        <thead>
            <th width="30">SL</th>
            <th width="130"> Month </th>
            <th width="130">Pending PO Qty. </th>
            <th width="140">Pending PO Value</th>
            <th width="80">Pending SAH</th>
            <th width="135">Plan Cut Qty.</th>
            <th width="125">Cutting Pending </th>
            <th width="125">Sewing Pending</th>
            <th>Finishing Pending </th>
        </thead>
		<?
		$prev_PendingSAH=0;$prev_po_qnty=0; $prev_po_val=0; $prev_sew_qnty=0; $prev_cut_qnty=0; $prev_finish_qnty=0;
		$curr_month=date("F",strtotime($current_month_end_date)).", ".date("Y",strtotime($current_month_end_date));
		
		$summary_grand_total_po_qny=0;
		$summary_grand_total_lc_value=0;
		$summary_grand_total_cut_qny=0;
		$summary_grand_total_sewing_qny=0;
		$summary_grand_total_finish_qny=0;
		$bgcolor1='#E9F3FF';
		$bgcolor2='#FFFFFF';
		?>
        <tr bgcolor="<? echo $bgcolor1; ?>" onclick="change_color('tr1st_1','<? echo $bgcolor1; ?>')" id="tr1st_1">
            <td>1</td>
            <td>Previous To Current Month</td>
            <td align="right"  title="Prev PO Qty=<? echo $tot_prev_pending_po_qty_previ;?> -Shipout Qty=<? echo $tot_current_pending_po_quantity;?>"><? 
			$tot_prev_pending_po_qty_previ=$tot_prev_pending_po_qty_previ-$tot_current_pending_po_quantity;
			echo number_format($tot_prev_pending_po_qty_previ,0); $summary_grand_total_po_qny+=$tot_prev_pending_po_qty_previ; ?></td>
            <td align="right" title="Prev PO Value=<? echo $tot_prev_po_val;?>-Shipout Value=<? echo $tot_current_pending_po_val;?>"><? 
			$tot_prev_po_val=$tot_prev_po_val-$tot_current_pending_po_val;$tot_prev_PendingSAH=$tot_prev_PendingSAH-$tot_current_PendingSAH;
			$tot_pre_plan_cut=$tot_pre_plan_cut-$tot_current_plan_cut;$tot_prev_cut_qnty=$tot_prev_cut_qnty-$tot_current_cut_qty;
			$tot_prev_sew_qnty=$tot_prev_sew_qnty-$tot_current_sewing_qty;$tot_prev_finish_qnty=$tot_prev_finish_qnty-$tot_current_finish_qty;
			
			echo number_format($tot_prev_po_val,2); $summary_grand_total_lc_value+=$tot_prev_po_val; ?></td>
            <td align="right"  title="Prev Pending Po Qty*SMV/60"><? echo number_format($tot_prev_PendingSAH,2);$summary_grand_total_PendingSAH+=$tot_prev_PendingSAH;?></td>
            <td align="right"> <? echo number_format($tot_pre_plan_cut,0); $summary_grand_total_plan_cut+=$tot_pre_plan_cut; ?></td>
            <td align="right" title="Prev Plan Cut -Cutting Prod. Qty"><? echo number_format($tot_prev_cut_qnty,0); $summary_grand_total_cut_qny+=$tot_prev_cut_qnty; ?></td>
            <td align="right"  title="Prev PO Qty -Cutting Prod. Qty"><? echo number_format($tot_prev_sew_qnty,0); $summary_grand_total_sewing_qny+=$tot_prev_sew_qnty; ?></td>
            <td align="right"  title="Prev PO Qty -Finish Prod. Qty"><? echo number_format($tot_prev_finish_qnty,0); $summary_grand_total_finish_qny+=$tot_prev_finish_qnty; ?></td>
        </tr>
        <tr bgcolor="<? echo $bgcolor2; ?>" onclick="change_color('tr1st_2','<? echo $bgcolor2; ?>')" id="tr1st_2">
            <td>2</td>
            <td> <? echo $curr_month; ?> </td>
            <td align="right"  title="Current PO Qty -Shipout Qty"><? echo number_format($tot_current_pending_po_quantity,0); $summary_grand_total_po_qny+=$tot_current_pending_po_quantity; ?></td>
            <td align="right" title="Current PO Value -Shipout Value"><? echo number_format($tot_current_pending_po_val,2); $summary_grand_total_lc_value+=$tot_current_pending_po_val; ?></td>
            <td align="right"  title="Current Pending Po Qty*SMV/60"><? echo number_format($tot_current_PendingSAH,2);$summary_grand_total_PendingSAH+=$tot_current_PendingSAH;?></td>
            <td align="right"><? echo number_format($tot_current_plan_cut,0); $summary_grand_total_plan_cut+=$tot_current_plan_cut; ?></td>
            <td align="right" title="Plan Cut -Cutting Prod. Qty"><? echo number_format($tot_current_cut_qty,0); $summary_grand_total_cut_qny+=$tot_current_cut_qty; ?></td>
            <td align="right"  title="PO Qty -Sewing Prod. Qty"><? echo number_format($tot_current_sewing_qty,0); $summary_grand_total_sewing_qny+=$tot_current_sewing_qty; ?></td>
            <td align="right"  title="PO Qty -Finish Prod. Qty"><? echo number_format($tot_current_finish_qty,0); $summary_grand_total_finish_qny+=$tot_current_finish_qty; ?></td>
        </tr>
        <tfoot>
            <th colspan="2" align="right">Total</th>
            <th align="right"><? echo number_format($summary_grand_total_po_qny,0); ?></th>
            <th align="right"><? echo number_format($summary_grand_total_lc_value,2); ?></th>
            <th align="right"><? echo number_format($summary_grand_total_PendingSAH,2); ?></th>
            <th align="right"><? echo number_format($summary_grand_total_plan_cut,0); ?></th>
            <th align="right"><? echo number_format($summary_grand_total_cut_qny,0); ?></th>
            <th align="right"><? echo number_format($summary_grand_total_sewing_qny,0); ?></th>
            <th align="right"><? echo number_format($summary_grand_total_finish_qny,0); ?> </th>
        </tfoot>
    </table> 
   <? //die; ?>
    <br/> 
    <table width="1300">
        <tr>
		<td valign="top">
		<?
			foreach( $buyer_wise_arr as $month_key=>$row_month)
			{
						?>
				<div style="width:400px; float:left; margin:5px;">
				<table width="400px"  cellspacing="0"  class="display">
					<tr>
						<td colspan="4" align="center"><font size="3"><strong>Total Summary <? $month_name=date("F",strtotime($month_key)).", ".date("Y",strtotime($month_key)); echo $month_name; ?></strong></font></td>
					</tr>
				</table>
				<table width="400px" class="rpt_table" border="1" rules="all">
					<thead>
						<th width="30">SL</th>
						<th width="100">Buyer Name</th>
						<th width="90">Pending Qnty</th>
						<th width="90">Pending Value</th>
						<th width="70">Pending  SAH</th>
					</thead>
				<?
				$tot_buyer_po_qnty=$tot_buyer_po_val=$tot_buyer_SAH=0;
				$m=1;
				foreach( $row_month as $buyer_key=>$row)
				{
				if ($m%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$tot_buyer_order_quantity=$row['pending_poqty'];
				$buyer_order_val=$row['po_value'];
				$buyer_SAH=$row['buyer_sah'];
			?>
			
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr1st_<? echo $i; ?><? echo $d; ?>','<? echo $bgcolor; ?>')" id="tr1st_<? echo $i; ?><? echo $d; ?>">
							<td><? echo $m; ?></td>
							<td><p><? echo $buyer_short_name_arr[$buyer_key]; ?></p></td>
							<td align="right"><? echo number_format($tot_buyer_order_quantity,0); $tot_buyer_po_qnty+=$tot_buyer_order_quantity; ?></td>
							<td align="right"><? echo number_format($buyer_order_val,2); $tot_buyer_po_val+=$buyer_order_val; ?></td>
							<td align="right"><? echo number_format($buyer_SAH,2); $tot_buyer_SAH+=$buyer_SAH; ?></td>
                          </tr>
			<?	
				$m++;
				}
				?>
				 <tfoot>
						<th colspan="2" align="right">Total</th>
						<th align="right"><? echo number_format($tot_buyer_po_qnty,0); ?></th>
						<th align="right"><? echo number_format($tot_buyer_po_val,2); ?></th>
						<th align="right"><? echo number_format($tot_buyer_SAH,2); ?></th>
                  </tfoot>
			</table>
			</div>	
				<?
				
			}
		?>
		</td>
    	</tr>
    </table>
	 </div>
    <br/>
    <?
    //ob_start();	
    ?>
    <div>
    <div align="left" style="background-color:#E1E1E1; color:#000; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif; width:3940px"><strong><u><i> Details Report</i></u></strong></div>        
    	<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="3940">
        	<thead>
            	<tr>
                    <th width="40">SL</th>
                    <th width="70">Job No</th>
                    <th width="80">Buyer Name</th>
                    <th width="80">Agent Name</th>
                    <th width="100">Internal Ref.</th>
                    <th width="110">PO Number</th>
                    <th width="50">Image</th> 
                    <th width="120">Style Name</th>
                    <th width="140">Item Name</th>
                    <th width="60">Sew SMV</th>
                    <th width="100">PO Qnty.</th>
                    <th width="100">Plan Cut Qty.</th>
                    <th width="90">Ship Date</th>
					<th width="80">Ext.Ship Date</th>
                    <th width="60">Delay</th>
                    <th width="90">Fabric Booking No</th>
                    
                    <th width="90">Yarn Allocation</th>
                    <th width="90">Yarn Issue</th>
                    <th width="90">
                        Yarn Issue Balance <span style="font-size:9px">[Grey Req As FB - (Yarn Issue + Net Trans)]</span>
                    </th>
                    <th width="90">Grey Req.</th>
                    <th width="90">Grey Prod.</th>
                    
                    <th width="90">
                        Knitting Balance <span style="font-size:9px">[Grey Req. As Booking - Grey Prod.]</span>
                    </th>
                    <th width="90">Fabrics  Req.</th>
                    <th width="90"> Fab. Avl.</th>
                    
                    <th width="90">
                        Finish Fab. Balance <span style="font-size:9px">[Finish Req. As Booking - Finish Prod.]</span>
                    </th>

                    <th width="90">Cut Qnty</th>
                    <th width="90">Cut Pending</th>
                    <th width="80">Cut Wastage</th>
                    <th width="90">Sewing Qnty</th>
                    <th width="90">Sew Pending</th>
                    <th width="90">Finish Qnty</th>
                    <th width="90">Finish Pending</th>
                    <th width="90">Ship Qnty</th>
                    <th width="100">Pending PO Qnty.</th>
                    <th width="100">Pending PO Value.</th>
                    <th width="90">Pending SAH</th>
                    <th width="140">Team Leader</th>
                    <th width="140">Dealing Merchant</th>
					<th width="100">Extended Ship Mode</th>
                    <th width="100">Sea Discount On FOB</th>
                    <th width="100">Air Discount On FOB</th>
					
                    <th>Remarks</th>
                </tr>
                <tr>
                	<th width="40">&nbsp;</th>
                	<th width="70"><input type="text" value="<? echo str_replace("%","",$job_number); ?>" onkeyup="show_inner_filter(event);" name="txt_job_number" id="txt_job_number" class="text_boxes" style="width:50px" /></th>
                    <th width="80"><input type="text" name="buyer_name" onkeyup="show_inner_filter(event);" value="<? echo str_replace("%","",$buyer_name); ?>" id="buyer_name" class="text_boxes" style="width:60px" /></th>
                    
                    <th width="80"><input type="text" name="txt_agent_name" onkeyup="show_inner_filter(event);" value="<? echo str_replace("%","",$txt_agent_name); ?>" id="txt_agent_name" class="text_boxes" style="width:60px" /></th>
                    
                    <th width="100"><input type="text" name="txt_internal_file" onkeyup="show_inner_filter(event);" value="<? echo str_replace("%","",$txt_internal_file); ?>" id="txt_internal_file" class="text_boxes" style="width:60px" /></th> 
                    
                    <th width="110"><input type="text" name="txt_po_number" onkeyup="show_inner_filter(event);" value="<? echo str_replace("%","",$txt_po_number); ?>" id="txt_po_number" class="text_boxes" style="width:80px" /></th>
                    <th width="50">&nbsp;</th>
                    <th width="120"><input type="text" name="txt_style_number" onkeyup="show_inner_filter(event);" value="<? echo str_replace("%","",$txt_style_number); ?>" id="txt_style_number" class="text_boxes" style="width:80px" /></th>
                    <th width="100">&nbsp;</th>
                    <th width="50">&nbsp;</th>
                    <th width="100"><input type="text" name="txt_order_qnty" onkeyup="show_inner_filter(event);" value="<? echo str_replace("%","",$txt_order_qnty); ?>" id="txt_order_qnty" class="text_boxes" style="width:80px" /></th>
                    <th colspan="31">&nbsp;</th>
                </tr>
        	</thead>
		</table>
     	<div style="width:3958px; max-height:410px; overflow-y: scroll;" id="scroll_body">
        <table cellspacing="0" cellpadding="0"  width="3940"  border="1" rules="all" class="rpt_table" id="table_body">
			<?
			
			//---------------------------------
			/*$sql="select a.po_break_down_id,a.booking_no,a.booking_no_prefix_num,a.booking_type from wo_booking_mst a where a.company_id=$cbo_company_id and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1";
			$bookingResul=sql_select($sql);
			foreach($bookingResul as $rows){
				if($rows[csf('booking_type')]==4)$bookingType="SF: "; else $bookingType="MF: ";
				$bookingDataArr[$rows[csf('po_break_down_id')]][]=$bookingType.$rows[csf('booking_no_prefix_num')];
			}
			unset($bookingResul);*/
		$yarn_allo="SELECT   po_break_down_id,  item_id, qnty  FROM inv_material_allocation_dtls Where  status_active = 1 and is_deleted=0 and (is_dyied_yarn!=1 or is_dyied_yarn is null)  ".where_con_using_array($po_id_arr,0,'po_break_down_id')."  ";//die;
		$yarn_allo_arr=array();

		foreach(sql_select($yarn_allo) as $v)
		{
			 $yarn_allo_arr[$v[csf("po_break_down_id")]]+=$v[csf("qnty")];
		}

		/*$yarn_issue="SELECT   po_breakdown_id as po_break_down_id,   c.quantity as qnty  FROM INV_ISSUE_MASTER a,INV_TRANSACTION b,ORDER_WISE_PRO_DETAILS c Where a.id=b.mst_id and b.id=c.trans_id and  a.issue_basis=3 and a.entry_form=3 and a.status_active = 1 and b.status_active = 1 and c.status_active = 1  ".where_con_using_array($po_id_arr,0,'po_breakdown_id')."  ";
		$yarn_issue_arr=array();

		foreach(sql_select($yarn_issue) as $v) 
		{
			$yarn_issue_arr[$v[csf("po_break_down_id")]] +=$v[csf("qnty")];
		}

		  $yarn_issue="SELECT   po_breakdown_id as po_break_down_id,   c.quantity as qnty  FROM INV_ISSUE_MASTER a,INV_TRANSACTION b,ORDER_WISE_PRO_DETAILS c Where a.id=b.mst_id and b.id=c.trans_id and  a.issue_basis=3 and a.entry_form=3 and a.status_active = 1 and b.status_active = 1   AND b.transaction_type=2 and c.trans_type=2
       and b.receive_basis = 3  and c.status_active = 1  ".where_con_using_array($po_id_arr,0,'po_breakdown_id')."  ";
		$yarn_issue_arr=array();

		foreach(sql_select($yarn_issue) as $v)  
		{ 
		$yarn_issue_arr[$v[csf("po_break_down_id")]] +=$v[csf("qnty")];
		}*/
		$sql_return_data = sql_select("SELECT c.id, (b.quantity) as returned_qnty from inv_receive_master a,  order_wise_pro_details b, inv_transaction d,wo_po_break_down c  where a.id = d.mst_id and a.entry_form=9 and a.item_category =1 and a.receive_basis = 3 and d.transaction_type=4 and d.item_category=1 and d.id=b.trans_id and b.po_breakdown_id = c.id   and b.trans_type=4 and b.entry_form=9 and b.status_active=1 and b.is_deleted=0 and b.issue_purpose!=2 ".where_con_using_array($po_id_arr,0,'c.id')."");

		$return_qty_arr=array();
		foreach ($sql_return_data as $row) {
			$yarn_issue_return_qty_arr[$row[csf('id')]]['returned_qnty'] += $row[csf('returned_qnty')];
			 	
		}
		
		$recvData=sql_select("select a.po_breakdown_id, sum(quantity) as grey_prod_qnty from order_wise_pro_details a, pro_grey_prod_entry_dtls b where a.dtls_id=b.id and a.prod_id=b.prod_id and a.entry_form =2 and a.is_deleted=0 and a.status_active=1 $poCon4 group by a.po_breakdown_id");
		foreach($recvData as $row)
		{
			$gp_qty_array[$row[csf('po_breakdown_id')]]=$row[csf('grey_prod_qnty')];
		}
		unset($recvData);
			
			
			
		  $sql="select b.po_break_down_id, a.booking_no_prefix_num,a.booking_type,a.item_category,  b.fin_fab_qnty as req_qnty,  b.grey_fab_qnty as grey_req_qnty,
		  (case when a.fabric_source=1 and a.booking_type=1 then b.grey_fab_qnty else 0 end) as grey_fab_qnty_req,
		   (case when a.booking_type=1 then b.grey_fab_qnty else 0 end) as fin_fab_qnty_req
		  
		   from wo_booking_mst a, wo_booking_dtls b where a.company_id=$cbo_company_id and a.booking_no=b.booking_no and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 ".where_con_using_array($po_id_arr,0,'b.po_break_down_id')."";
			$result=sql_select($sql);
			foreach($result as $row)
			{
				if($row[csf('item_category')]==13 || $row[csf('item_category')]==2)
				{
				$tot_req_qnty[$row[csf('po_break_down_id')]]+=$row[csf('grey_req_qnty')];
				$tot_fin_req_qnty[$row[csf('po_break_down_id')]]+=$row[csf('req_qnty')];
				}
				$grey_fab_req_qnty[$row[csf('po_break_down_id')]]+=$row[csf('grey_fab_qnty_req')];
				$grey_fin_fab_req_qnty[$row[csf('po_break_down_id')]]+=$row[csf('fin_fab_qnty_req')];
				if($row[csf('booking_type')]==4)$bookingType="SF: "; else $bookingType="MF: ";
				$bookingDataArr[$row[csf('po_break_down_id')]].=$bookingType.$row[csf('booking_no_prefix_num')].',';
			}
	unset($result);
	$sqls_avl="SELECT b.po_breakdown_id, sum(b.quantity) as quantity from inv_transaction a,order_wise_pro_details b where a.id=b.trans_id and a.item_category in(2,3) and b.entry_form in(37,17,7)  $poCon5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ".where_con_using_array($po_id_arr,0,'b.po_breakdown_id')." group by b.po_breakdown_id ";// and a.receive_basis in(1,2,4)
		    foreach(sql_select($sqls_avl) as $vals)
		    {
		    	$fabrics_avl_qnty_array[$vals[csf("po_breakdown_id")]]+=$vals[csf("quantity")];
		    }
			
			/*$sql="select d.prod_id,b.quantity as issue_qnty from order_wise_pro_details b, inv_transaction d where d.id=b.trans_id and b.trans_type in(2,6) and d.company_id=$cbo_company_id and b.status_active=1 and b.is_deleted=0";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
						//$yarn_issued[$row[csf('prod_id')]]+=$row[csf('issue_qnty')];

					}*/
			
		$dataArrayTrans=sql_select("select po_breakdown_id, 
								sum(CASE WHEN entry_form ='7' THEN quantity ELSE 0 END) AS finish_receive,
								sum(CASE WHEN entry_form ='66' THEN quantity ELSE 0 END) AS finish_receive_roll_wise,
								sum(CASE WHEN entry_form ='2' THEN quantity ELSE 0 END) AS grey_receive,
								
								sum(CASE WHEN entry_form ='46' and trans_type=3 THEN quantity ELSE 0 END) AS recv_rtn_qnty,
								sum(CASE WHEN entry_form ='52' and trans_type=4 THEN quantity ELSE 0 END) AS iss_retn_qnty,
								
								sum(CASE WHEN entry_form ='3' and issue_purpose!=2 THEN quantity ELSE 0 END) AS issue_qnty,
								sum(CASE WHEN entry_form ='9' THEN quantity ELSE 0 END) AS return_qnty,
								sum(CASE WHEN entry_form ='11' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_yarn,
								sum(CASE WHEN entry_form ='11' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_yarn,
								
								sum(CASE WHEN entry_form ='15' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty,
								sum(CASE WHEN entry_form ='15' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty,							
								sum(CASE WHEN entry_form ='37' THEN quantity ELSE 0 END) AS finish_purchase								
								from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form in(7,66,2,46,52,11,3,9,15,37) group by po_breakdown_id");
		foreach($dataArrayTrans as $row)
		{
			
			$finish_receive_qnty_arr[$row[csf('po_breakdown_id')]]=$row[csf('finish_receive')]+$row[csf('finish_receive_roll_wise')];
			$grey_receive_qnty_arr[$row[csf('po_breakdown_id')]]=$row[csf('grey_receive')];			//$yarn_issued[$row[csf('po_breakdown_id')]]+=($row[csf('issue_qnty')]+$row[csf('transfer_in_qnty_yarn')])+($row[csf('return_qnty')]+$row[csf('transfer_out_qnty_yarn')]);
			$yarn_issued[$row[csf('po_breakdown_id')]]=$row[csf('issue_qnty')]+($row[csf('transfer_in_qnty_yarn')]-$row[csf('transfer_out_qnty_yarn')]);
			$net_tensfer[$row[csf('po_breakdown_id')]]=($row[csf('transfer_in_qnty')]-$row[csf('transfer_out_qnty')]);
			$fin_rec_ret[$row[csf('po_breakdown_id')]]+=$row[csf('recv_rtn_qnty')];
		}	
		unset($dataArrayTrans);
			
		$sql_fin_purchase="select c.po_breakdown_id, sum(c.quantity) as finish_purchase from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.receive_basis<>9 and a.entry_form=37 and c.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.po_breakdown_id";
		$dataArrayFinPurchase=sql_select($sql_fin_purchase);
		foreach($dataArrayFinPurchase as $finRow)
		{
			$fin_purchase[$finRow[csf('po_breakdown_id')]]=$finRow[csf('finish_purchase')];
		}
		unset($dataArrayFinPurchase);
			
			
			//----------------------------------
			$excess_percent_arr=array();
			$standard_excess_cut=sql_select("select id, company_name, slab_rang_start, slab_rang_end, excess_percent from variable_prod_excess_slab");
			foreach( $standard_excess_cut as $excRow)
			{
				$excess_percent_arr[$excRow[csf('company_name')]][$excRow[csf('id')]]['start']=$excRow[csf('slab_rang_start')];
				$excess_percent_arr[$excRow[csf('company_name')]][$excRow[csf('id')]]['end']=$excRow[csf('slab_rang_end')];
				$excess_percent_arr[$excRow[csf('company_name')]][$excRow[csf('id')]]['percent']=$excRow[csf('excess_percent')];
			}
			unset($standard_excess_cut);
            $ii=1; $k=1; $total_po_qnty=$total_pending_po_qnty=0; $total_cut_qnty=0; $total_sew_qnty=0; $total_finish_qnty=0; $total_ship_qnty=0; $total_balance_qnty=0;$total_cut_pending=0;$total_finish_pending=0;$total_sew_pending=0;
			   $total_yarn_allow_qnty=$total_fab_avl_qnty=$total_yarn_issue_qnty=$total_fin_req_qnty=$total_gp_qnty=$total_grey_req_qnty=0;
            $month_array=array();
		 foreach( $po_wise_arr as $month_key=>$month_data)
         {
            foreach( $month_data as $po_key=>$row_order_level)
            {
                if ($ii%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$pub_shipment_date=date("Y-m-d",strtotime($row_order_level['pub_shipment_date']));//change_date_format($row_order_level[csf('pub_shipment_date')]);
				$extended_ship_date=date("Y-m-d",strtotime($row_order_level['extended_ship_date']));//change_date_format($row_order_level[csf('extended_ship_date')]);
				$template_id=$template_id_arr[$po_key];
               		//if($row_order_level['shiping_status']==2) $ex_factory_qnty=$sql_summary_ex_factory[$po_key]; else $ex_factory_qnty=0;
					$ex_factory_qnty=$row_order_level['shipout_qty'];
                $po_quantity=($row_order_level['po_quantity']*$row_order_level['total_set_qnty']);
				$plan_cut_qty=$row_order_level['plan_cut']*$row_order_level['total_set_qnty'];
				$month=date("Y-m",strtotime($month_key));
				$pending_po_value=(($po_quantity-$ex_factory_qnty)*$row_order_level['unit_price']);
			   $days_remian=datediff('d',date('d-m-Y',time()),$row_order_level['pub_shipment_date'])-1;
			    if(!in_array($month, $month_array))
                {
                    if ($k!=1)
                    {
                    ?>
                        <tr bgcolor="#CCCCCC">
                            <td colspan="9" align="right"><b>Monthly Total</b></td>
                            <td>&nbsp;</td>
                            <td align="right"><? echo  number_format($monthly_total_po_qnty,0);?></td>
                            <td align="right"><? echo  number_format($monthly_total_planCut_qnty,0);?></td>
                            <td>&nbsp;</td>
							<td>&nbsp;</td>
                            <td>&nbsp;</td>
                            
                            <td>&nbsp;</td>
                          
                            
                            <td align="right"><? echo  number_format($mon_total_yarn_allow_qnty,0);?></td>
                            <td align="right"><? echo  number_format($mon_total_yarn_issue_qnty,0);?></td>
                              
                            <td align="right"><? echo  number_format($monthly_total_yarn_bal,0);?></td>
                            
                            
                          <td align="right"><? echo  number_format($mon_total_fab_req_qnty,0);?></td>
                           <td align="right"><? echo  number_format($mon_total_gp_qnty,0);?></td>
                            
                            <td align="right"><? echo  number_format($monthly_total_knit_bal,0);?></td>
                            
                            <td align="right"><? echo  number_format($mon_total_fin_req_qnty,0);?></td>
                            <td align="right"><? echo  number_format($mon_total_gp_qnty,0);?></td>
                            
                            <td align="right"><? echo  number_format($monthly_total_fin_bal,0);?></td>
                            
                            <td align="right"><? echo number_format($monthly_total_cut_qnty,0); ?></td>
                            <td align="right"><? echo number_format($monthly_total_cut_pending,0); ?></td>
                            <td>&nbsp;</td>
                            <td align="right"><? echo number_format($monthly_total_sew_qnty,0); ?></td>
                            <td align="right"><? echo number_format($monthly_total_sew_pending,0); ?></td>
                            <td align="right"><? echo number_format($monthly_total_finish_qnty,0); ?></td>
                            <td align="right"><? echo number_format($monthly_total_finish_pending,0); ?></td>
                            <td align="right"><? echo number_format($monthly_total_ship_qnty,0); ?></td>
                            <td align="right"><? echo number_format($monthly_total_pending_po_qnty,0); ?></td>
                            <td align="right"><? echo number_format($monthly_pending_po_value,2); ?></td>
                            <td align="right"><? echo number_format($monthlyPendingSAH,2); ?></td>
							<td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    	<?
						  $mon_total_yarn_allow_qnty=0;
						  $mon_total_yarn_issue_qnty=0;
						  $mon_total_fin_req_qnty=0;
						  $mon_total_gp_qnty=0;
						  $mon_total_fab_avl_qnty=0;
                        $monthly_total_po_qnty = 0;$monthly_total_pending_po_qnty= 0;
						$monthly_total_planCut_qnty=0;
                        $monthly_total_cut_qnty = 0;
                        $monthly_total_cut_pending = 0;
                        $monthly_total_sew_qnty = 0;
                        $monthly_total_sew_pending = 0;
                        $monthly_total_finish_qnty = 0;
                        $monthly_total_finish_pending = 0;
                        $monthly_total_ship_qnty = 0;
						$monthly_total_yarn_bal = 0;
						$monthly_total_knit_bal = 0;
						$monthly_total_fin_bal = 0;
						$monthly_pending_po_value=0;
						$monthlyPendingSAH=0;
						
						
                      }
                    $k++;
                    ?>
                    <tr bgcolor="#EFEFEF">
                        <td colspan="42"><b><?php 
						//if($row_order_level['extended_ship_date']!='')  echo date("F",strtotime($row_order_level['extended_ship_date'])).", ".date("Y",strtotime($row_order_level['extended_ship_date']));
						//else echo date("F",strtotime($row_order_level['pub_shipment_date'])).", ".date("Y",strtotime($row_order_level['pub_shipment_date']));
						echo date("F",strtotime($month_key)).", ".date("Y",strtotime($month_key));
						
						?></b></td>
                    </tr>
                <?
                    $month_array[]=$month;
                }
				
				$garments_name_details="";
				$ex_gmts_item=explode(',',$row_order_level['gmts_item_id']);
				foreach($ex_gmts_item as $item_id)
				{
					if ($garments_name_details=="") $garments_name_details=$garments_item[$item_id]; else $garments_name_details.=', '.$garments_item[$item_id];
				}
				if($row_order_level['pending_po_quantity']>0)
				{
					$yarn_issue=$yarn_issue_arr[$po_key];
					$yarn_issue_ret=$yarn_issue_return_qty_arr[$po_key]['returned_qnty'];
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $ii;?>','<? echo $bgcolor;?>')" id="tr_<? echo $ii;?>">
                    <td style="word-break: break-all;word-wrap: break-word;" width="40"><? echo $ii; ?></td>
                    <td style="word-break: break-all;word-wrap: break-word;"  width="70" align="center"><? echo $row_order_level['job_no_prefix_num']; ?></td>
                    <td style="word-break: break-all;word-wrap: break-word;"  width="80"><p><? echo $buyer_short_name_arr[$row_order_level['buyer_name']]; ?></p></td>
                    <td style="word-break: break-all;word-wrap: break-word;"  width="80"><p><? echo $buyer_short_name_arr[$row_order_level['agent_name']]; ?></p></td>
                    
                    <td style="word-break: break-all;word-wrap: break-word;"  width="100"><?=$row_order_level['grouping']; ?></td>
                    
                    <td style="word-break: break-all;word-wrap: break-word;"  width="110"><p><a href="##" onClick="order_dtls_popup('<? echo $row_order_level['job_no_mst'];?>', '<? echo $row_order_level[csf('id')]; ?>','<? echo $template_id; ?>','<? echo $tna_process_type; ?>')"><? echo $row_order_level['po_number']; ?></a></p></td>
                    <td style="word-break: break-all;word-wrap: break-word;"  width="50" onclick="openmypage_image('requires/capacity_and_order_booking_status_controller.php?action=show_image&job_no=<? echo $row_order_level['job_no_mst']; ?>','Image View')"><img src='../../../<? echo $imge_arr[$row_order_level['job_no_mst']]; ?>' height='25' width='30' /></td>
                    <td style="word-break: break-all;word-wrap: break-word;"  width="120"><p><? echo $row_order_level['style_ref_no']; ?></p></td>
                    <td style="word-break: break-all;word-wrap: break-word;"  width="140"><p> <? echo $garments_name_details;?></p></td>
                    <td style="word-break: break-all;word-wrap: break-word;"  width="60" align="right"><? echo $row_order_level['set_smv']; ?></td>
                    <td style="word-break: break-all;word-wrap: break-word;"  align="right" width="100"><? echo number_format($po_quantity,0); ?></td>
                    <td style="word-break: break-all;word-wrap: break-word;"  align="right" width="100"><? echo number_format($plan_cut_qty,0); ?></td>
                    <td style="word-break: break-all;word-wrap: break-word;"  width="90" align="center"><? echo change_date_format($row_order_level['pub_shipment_date'],'dd-mm-yyyy','-'); ?></td>
					<td style="word-break: break-all;word-wrap: break-word;"  width="80" align="center"><? echo change_date_format($row_order_level['extended_ship_date'],'dd-mm-yyyy','-'); ?></td>
                    <td style="word-break: break-all;word-wrap: break-word;"  width="60" align="center" bgcolor="<? //echo $color; ?>" ><? echo $days_remian; ?> </td>
                    
                    <td style="word-break: break-all;word-wrap: break-word;"  width="90"><p><? $bookingType=rtrim($bookingDataArr[$po_key],',');$bookingTypeArr=implode(",",array_unique(explode(",",$bookingType)));
					echo $bookingTypeArr;  ?></p></td>
                    
                     <td style="word-break: break-all;word-wrap: break-word;"  width="90" align="right" bgcolor="<? //echo $color; ?>" ><? echo number_format($yarn_allo_arr[$po_key],0); ?> </td>
                     <td style="word-break: break-all;word-wrap: break-word;" title="Issue=<? echo $yarn_issued[$po_key].',Ret='.$yarn_issue_ret;?>"  width="90" align="right" bgcolor="<? //echo $color; ?>" ><? echo number_format($yarn_issued[$po_key]-$yarn_issue_return_qty_arr[$po_key]['returned_qnty'],0); ?> </td>
                      
                    <td style="word-break: break-all;word-wrap: break-word;"  width="90" align="right">
						<?  //$grey_fab_req_qnty[$row[csf('po_break_down_id')]]
			//$fabrics_avl_qnty_array[$vals[csf("po_breakdown_id")]]
		//	$gp_qty_array[$row[csf('po_breakdown_id')]]
		
                           // $yarn_bal=$tot_req_qnty[$row_order_level[csf('id')]]-$yarn_issued[$row_order_level[csf('id')]];
						  // echo $yarn_bal=number_format($tot_req_qnty[$po_key]-($yarn_issue-$yarn_issue_ret),2); 
						   echo $yarn_bal=number_format($grey_fab_req_qnty[$po_key]-($yarn_issued[$po_key]-$yarn_issue_return_qty_arr[$po_key]['returned_qnty']),2);
                        ?> 
                    </td>
                      <td style="word-break: break-all;word-wrap: break-word;" title="GreyReq=<? echo $grey_fab_req_qnty[$po_key];?>"  width="90" align="right" bgcolor="<? //echo $color; ?>" ><? echo number_format($grey_fab_req_qnty[$po_key],0); ?> </td>
                     <td style="word-break: break-all;word-wrap: break-word;"  width="90" align="right" bgcolor="<? //echo $color; ?>" ><? echo number_format($gp_qty_array[$po_key],0); ?> </td>
                     
                    <td style="word-break: break-all;word-wrap: break-word;"  width="90" align="right"><?  
					echo $knit_bal=number_format($tot_req_qnty[$po_key]-$grey_receive_qnty_arr[$po_key],2); 
					?> </td>
                      <td style="word-break: break-all;word-wrap: break-word;"  width="90" align="right" bgcolor="<? //echo $color; ?>" ><? echo number_format($grey_fin_fab_req_qnty[$po_key],0); ?> </td>
                     <td style="word-break: break-all;word-wrap: break-word;"  width="90" align="right" bgcolor="<? //echo $color; ?>" ><? echo number_format($fabrics_avl_qnty_array[$po_key],0); ?> </td>
                     
                    <td style="word-break: break-all;word-wrap: break-word;"  width="90" align="right"><?  
							//$finish_bal = $tot_fin_req_qnty[$row_order_level[csf('id')]]-(($fin_purchase[$row_order_level[csf('id')]]-$fin_rec_ret[$row_order_level[csf('id')]])+$net_tensfer[$row_order_level[csf('id')]]+$finish_receive_qnty_arr[$row_order_level[csf('id')]]);			
							echo $finish_bal =number_format($tot_fin_req_qnty[$po_key]-(($fin_purchase[$po_key]-$fin_rec_ret[$po_key])+$net_tensfer[$po_key]+$finish_receive_qnty_arr[$po_key]),2);
					
					?> </td>
                    
                    <td style="word-break: break-all;word-wrap: break-word;"  align="right" width="90"><? echo number_format($prod_qty_arr[$po_key]['cutting_qnty'],0);  ?></td>
                    <td style="word-break: break-all;word-wrap: break-word;"  align="right" width="90"><? $cut_pending=$plan_cut_qty-$prod_qty_arr[$po_key]['cutting_qnty']; if($cut_pending>0) echo number_format($cut_pending,0); else echo 0;  ?></td>
                    <?
					foreach($excess_percent_arr[$row_order_level['company_name']] as $key=>$value)
					{
						//echo $key."<br>";
						$start_limit=$excess_percent_arr[$row_order_level['company_name']][$key]['start'];
						$end_limit=$excess_percent_arr[$row_order_level['company_name']][$key]['end'];
						
						if($po_quantity>=$start_limit && $po_quantity<=$end_limit)
						{
							$excess_percent=$excess_percent_arr[$row_order_level['company_name']][$key]['percent'];
							//$subtotal+=$excess_percent;
							break;
						}
						else $excess_percent=0;
					}
					//echo $excess_percent;
                    //list($row11)=$standard_excess_cut;
                   // $excess_percent= $row11[('excess_percent')];
				  
                    $actual_excess_cut=0; $actual_excess_cut2=0;
                    $actual_excess_cut=(($prod_qty_arr[$po_key]['cutting_qnty']-$po_quantity)/$po_quantity)*100;
                    $exceed_cut=$actual_excess_cut - $excess_percent;
					
                    if($actual_excess_cut>$excess_percent) $bg_color="red"; else $bg_color="green";	

                   // $actual_excess_cut=round($actual_excess_cut,2);
                    $actual_excess_cut2=$prod_qty_arr[$po_key]['cutting_qnty']-$po_quantity;
                    if($actual_excess_cut2==0 || $actual_excess_cut2<0)
                    {
                    ?>
                        <td style="word-break: break-all;word-wrap: break-word;"  align="left" width="80" title="Cutting Qnty Not Exceed Order Qnty" bgcolor="<? echo $bg_color ?>"><? echo "N/A"; ?></td>
                    <?
                    }
                    else
                    {
                    ?>
                        <td style="word-break: break-all;word-wrap: break-word;"  align="right" width="80" bgcolor="<? echo $bg_color ?>"><? echo number_format($actual_excess_cut,2)." %"; ?></td>
                    <?
                    } 
                    ?> 
                    <td style="word-break: break-all;word-wrap: break-word;"  align="right" width="90"><? echo $sewing_tot= number_format($prod_qty_arr[$po_key]['sewing_out_qnty'],0); ?></td>
                    <td style="word-break: break-all;word-wrap: break-word;"  align="right" width="90"><?  $sewing_pending=$po_quantity-$prod_qty_arr[$po_key]['sewing_out_qnty']; if ($sewing_pending>0) echo  number_format($sewing_pending,0); else echo 0; ?></td>
                    <td style="word-break: break-all;word-wrap: break-word;"  align="right" width="90"><? echo  $finish_tot= number_format($prod_qty_arr[$po_key]['finish_qnty'],0); ?></td>
                    <td style="word-break: break-all;word-wrap: break-word;"  align="right" width="90"><? $finish_pending=$po_quantity-$prod_qty_arr[$po_key]['finish_qnty']; if($finish_pending>0) echo number_format($finish_pending,0); else echo 0; ?></td>
                    <td style="word-break: break-all;word-wrap: break-word;"  align="right" width="90"><? echo number_format($ex_factory_qnty,0); ?></td>
                    <td style="word-break: break-all;word-wrap: break-word;"  align="right" width="100"><? $pending_po_quantity=$row_order_level['pending_po_quantity'];
					echo number_format($pending_po_quantity,0);?> </td>
                    <td style="word-break: break-all;word-wrap: break-word;"  align="right" width="100"><? echo $pending_po_tot= number_format($pending_po_value,0);?> </td>
                    <td style="word-break: break-all;word-wrap: break-word;"  align="right" width="90"> &nbsp;<? echo number_format((($po_quantity - $ex_factory_qnty)*$row_order_level['set_smv'])/60,2); ?></td>
					 <td align="center" width="140"><p><? 
					$team_leader_contact=$company_team_leader_contact_arr[$row_order_level['team_leader']];
					echo $team_leader_arr[$row_order_level['team_leader']].','.$team_leader_contact;;?></p></td>
                    <td align="center" width="140"><p><?  
					$team_member_contact_no=$company_team_member_contact_arr[$row_order_level['dealing_marchant']];
					echo $company_team_member_name_arr[$row_order_level['dealing_marchant']].','.$team_member_contact_no; ?></p></td>
					
                  <td width="100" align="center"><p><? echo $extend_shipment_mode[$row_order_level[('extend_ship_mode')]];?></p></td>
					<td width="100" align="center" title="PO Value(<? echo $row_order_level[('po_total_price')];?>),Sea Discount(<? echo $row_order_level[('sea_discount')];?>)"><p><? $sea_discount= ($row_order_level[('po_total_price')]*( $row_order_level[('sea_discount')]/100));echo number_format($sea_discount,2); ?></p></td>
					<td width="100" align="center" title="PO Value(<? echo $row_order_level[('po_total_price')];?>),Air Discount(<? echo $row_order_level[('air_discount')];?>)"><p><? $air_discount= ($row_order_level[('po_total_price')]*( $row_order_level[('air_discount')]/100));echo number_format($air_discount,2);?></p></td>
                    <td  style="word-break: break-all;word-wrap: break-word;"  >
						<div style="word-wrap:break-word; width:110px"><? echo $row_order_level['details_remarks']; ?>
					</div></td>
               </tr>
			<?
                $monthly_total_po_qnty+=$po_quantity;
				$monthly_total_pending_po_qnty+=$pending_po_quantity;
				$monthly_total_planCut_qnty+=$plan_cut_qty;
                $monthly_total_cut_qnty+= $prod_qty_arr[$po_key]['cutting_qnty'];
				
				
				 $mon_total_yarn_allow_qnty+=$yarn_allo_arr[$po_key];
				 $mon_total_yarn_issue_qnty+=$yarn_issued[$po_key]-$yarn_issue_return_qty_arr[$po_key]['returned_qnty'];
				 $mon_total_fin_req_qnty+=$grey_fin_fab_req_qnty[$po_key]; 
				
				 $mon_total_gp_qnty+=$gp_qty_array[$po_key]; 
				 $mon_total_fab_req_qnty+=$grey_fab_req_qnty[$po_key];
				 $mon_total_fab_avl_qnty+=$fabrics_avl_qnty_array[$po_key];
				 
				 
				 
                if($cut_pending>0)
                {
                	$monthly_total_cut_pending+=$plan_cut_qty-$prod_qty_arr[$po_key]['cutting_qnty'];
                	$total_cut_pending+= $cut_pending;
                }
                if($sewing_pending>0)
                {
                	$monthly_total_sew_pending+= $sewing_pending;
                	$total_sew_pending+= $sewing_pending;

                }
                if($finish_pending>0)
                {
                	$monthly_total_finish_pending+= $finish_pending;
                	$total_finish_pending+= $finish_pending;
                }
                
                $monthly_total_sew_qnty+=$prod_qty_arr[$po_key]['sewing_out_qnty'];
                $monthly_total_finish_qnty+=$prod_qty_arr[$po_key]['finish_qnty'];
                $monthly_total_ship_qnty+=$ex_factory_qnty;
				
				$monthly_total_yarn_bal += str_replace(",", "", $yarn_bal);
				$monthly_total_knit_bal += str_replace(",", "", $knit_bal);
				$monthly_total_fin_bal +=str_replace(",", "", $finish_bal) ;
				$monthly_pending_po_value	+=str_replace(",", "", $pending_po_tot);				
				$monthlyPendingSAH+=(($po_quantity - $ex_factory_qnty)*$row_order_level['set_smv'])/60;
				
                
                $total_po_qnty+=$po_quantity;
				$total_pending_po_qnty+=$pending_po_quantity;
				$total_planCut_qty+=$plan_cut_qty;
                $total_cut_qnty+= $prod_qty_arr[$po_key]['cutting_qnty'];
				
				 $total_yarn_allow_qnty+= $yarn_allo_arr[$po_key];
				 $total_yarn_issue_qnty+= $yarn_issued[$po_key]-$yarn_issue_return_qty_arr[$po_key]['returned_qnty'];
				 $total_fin_req_qnty+= $grey_fin_fab_req_qnty[$po_key]; 
				 $total_gp_qnty+= $gp_qty_array[$po_key]; 
				 $total_grey_req_qnty+= $grey_fab_req_qnty[$po_key];
				 $total_fab_avl_qnty+= $fabrics_avl_qnty_array[$po_key];
					
            
				
				
				$total_sew_qnty+= str_replace(",", "", $sewing_tot);
                //$total_sew_qnty+= $cutting_qnty[$row_order_level[csf('id')]];	
                $total_finish_qnty+=str_replace(",", "", $finish_tot);
                $total_ship_qnty+=$ex_factory_qnty;
				
				$total_yarn_bal += str_replace(",", "", $yarn_bal);
				$total_knit_bal += str_replace(",", "", $knit_bal);
				$total_fin_bal += str_replace(",", "", $finish_bal);
				
				/*$total_yarn_bal += $monthly_total_yarn_bal;
				$total_knit_bal += $monthly_total_knit_bal;
				$total_fin_bal += $monthly_total_fin_bal;*/
				$total_pending_po_value	+=str_replace(",", "", $pending_po_tot);
				$total_PendingSAH +=(($po_quantity - $ex_factory_qnty)*$row_order_level['set_smv'])/60;
                
                $ii++;
            }
				}
            }
			if($row_tot>0)
			{
			?>
				<tr bgcolor="#CCCCCC">
					<td colspan="9" align="right"><b>Monthly Total</b></td>
					<td align="right">&nbsp;</td>
					<td align="right"><? echo  number_format($monthly_total_po_qnty,0);?></td>
                    <td align="right"><? echo  number_format($monthly_total_planCut_qnty,0);?></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
                    
                    
                     <td align="right"><? echo  number_format($mon_total_yarn_allow_qnty,0);?></td>

                      <td align="right"><? echo  number_format($mon_total_yarn_issue_qnty,0);?></td>
                    
                    <td align="right"><? echo number_format($monthly_total_yarn_bal,2);?></td>
                    
                     <td align="right"><? echo  number_format($mon_total_fab_req_qnty,0);?></td>
                     <td align="right"><? echo  number_format($mon_total_gp_qnty,0);?></td>
                    
                    <td align="right"><? echo number_format($monthly_total_knit_bal,2);?></td>
                    
                   <td align="right"><? echo  number_format($mon_total_fin_req_qnty,0);?></td>
                   <td align="right"> <? echo  number_format($mon_total_gp_qnty,0);?></td>
                    
                    <td align="right"><? echo number_format($monthly_total_fin_bal,2);?></td>
                    
					<td align="right"><? echo number_format($monthly_total_cut_qnty,0); ?></td>
					<td align="right"><? echo number_format($monthly_total_cut_pending,0); ?></td>
					<td>&nbsp;</td>
					<td align="right"><? echo number_format($monthly_total_sew_qnty,0); ?></td>
					<td align="right"><? echo number_format($monthly_total_sew_pending,0); ?></td>
					<td align="right"><? echo number_format($monthly_total_finish_qnty,0); ?></td>
					<td align="right"><? echo number_format($monthly_total_finish_pending,0); ?></td>
					<td align="right"><? echo number_format($monthly_total_ship_qnty,0); ?></td>
					<td align="right"><? echo number_format($monthly_total_po_qnty - $monthly_total_ship_qnty,0); ?></td>
                    <td align="right"><? echo number_format($monthly_pending_po_value,2); ?></td>
					<td align="right"><? echo number_format($monthlyPendingSAH,2); ?></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
			<?	
			}
			?>
            </table>
            </div>
            <table cellspacing="0" cellpadding="0"  width="3940"  border="1" rules="all" class="tbl_bottom">
             <tr>
             	<td style="word-break: break-all;word-wrap: break-word;" width="40">&nbsp;</td>
                <td  style="word-break: break-all;word-wrap: break-word;" width="70">&nbsp;</td>
                <td style="word-break: break-all;word-wrap: break-word;"  width="80">&nbsp;</td>
                <td style="word-break: break-all;word-wrap: break-word;"  width="80">&nbsp;</td>
                <td  style="word-break: break-all;word-wrap: break-word;" width="100">&nbsp;</td>
                <td  style="word-break: break-all;word-wrap: break-word;" width="110">&nbsp;</td>
                <td style="word-break: break-all;word-wrap: break-word;"  width="50">&nbsp;</td>
                <td  style="word-break: break-all;word-wrap: break-word;" width="120">&nbsp;</td>
                <td  style="word-break: break-all;word-wrap: break-word;" width="140" align="right">Grand Total</td>
                <td  style="word-break: break-all;word-wrap: break-word;" width="60"></td>
                <td style="word-break: break-all;word-wrap: break-word;"  width="100" align="right"><? echo  number_format($total_po_qnty,0);?></td>
                <td style="word-break: break-all;word-wrap: break-word;"  width="100" align="right"><? echo  number_format($total_planCut_qty,0);?></td>
                <td style="word-break: break-all;word-wrap: break-word;"  width="90">&nbsp;</td>
				<td style="word-break: break-all;word-wrap: break-word;"  width="80">&nbsp;</td>
                <td  style="word-break: break-all;word-wrap: break-word;" width="60">&nbsp;</td>
                
                <td style="word-break: break-all;word-wrap: break-word;"  width="90">&nbsp;</td>
                
                <td style="word-break: break-all;word-wrap: break-word;"  width="90"><? echo  number_format($total_yarn_allow_qnty,0);?></td>
                <td style="word-break: break-all;word-wrap: break-word;" title="Yarn issue"  width="90"><? echo  number_format($total_yarn_issue_qnty,0);?></td>
                
                <td  style="word-break: break-all;word-wrap: break-word;" width="90" align="right"><? echo  number_format($total_yarn_bal,2);?></td>
                 <td style="word-break: break-all;word-wrap: break-word;"  width="90"><? echo  number_format($total_grey_req_qnty,2);?></td>
                  <td style="word-break: break-all;word-wrap: break-word;"  width="90"><? echo  number_format($total_gp_qnty,2);?></td>
                  
                <td style="word-break: break-all;word-wrap: break-word;"  width="90" align="right"><? echo  number_format($total_knit_bal,2);?></td>
                  <td style="word-break: break-all;word-wrap: break-word;"  width="90"><? echo  number_format($total_fin_req_qnty,2);?></td>
                  <td style="word-break: break-all;word-wrap: break-word;"  width="90"><? echo  number_format($total_fab_avl_qnty,2);?></td>
                  
                
                <td style="word-break: break-all;word-wrap: break-word;"  width="90" align="right"><? echo  number_format($total_fin_bal,2);?></td>
                
                <td style="word-break: break-all;word-wrap: break-word;"  width="90" align="right"><? echo number_format($total_cut_qnty,0); ?></td>
                <td style="word-break: break-all;word-wrap: break-word;"  width="90" align="right"><? echo number_format($total_cut_pending,0); ?></td>
                <td style="word-break: break-all;word-wrap: break-word;"  width="80">&nbsp;</td>
                <td style="word-break: break-all;word-wrap: break-word;"  width="90" align="right"><? echo number_format($total_sew_qnty,0); ?></td>
                <td style="word-break: break-all;word-wrap: break-word;"  width="90" align="right"><? echo number_format($total_sew_pending,0); ?></td>
                <td style="word-break: break-all;word-wrap: break-word;"  width="90" align="right"><? echo number_format($total_finish_qnty,0); ?></td>
                <td style="word-break: break-all;word-wrap: break-word;"  width="90" align="right"><? echo number_format($total_finish_pending,0); ?></td>
                
                <td style="word-break: break-all;word-wrap: break-word;"  width="90" align="right"><? echo number_format($total_ship_qnty,0); ?></td>
                <td style="word-break: break-all;word-wrap: break-word;"  width="100" align="right"><? echo number_format($total_pending_po_qnty,0); ?></td>
                <td style="word-break: break-all;word-wrap: break-word;"  width="100" align="right"><? echo number_format($total_pending_po_value,2); ?></td>
                <td style="word-break: break-all;word-wrap: break-word;"  width="90" align="right"><? echo number_format($total_PendingSAH ,2); ?></td>
                <td style="word-break: break-all;word-wrap: break-word;"  width="140">&nbsp;</td>
                <td style="word-break: break-all;word-wrap: break-word;"  width="140">&nbsp;</td>
                <td style="word-break: break-all;word-wrap: break-word;" width="100">&nbsp;</td>
				<td style="word-break: break-all;word-wrap: break-word;" width="100">&nbsp;</td>
				<td style="word-break: break-all;word-wrap: break-word;" width="100">&nbsp;</td>
				<td>&nbsp;</td>
            </tr>
        </table> 
    </fieldset> 
	</div>   
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
    echo "$html####$filename"; 
    exit();
	/*$html = ob_get_contents();
	ob_clean();
	 
	foreach (glob($_SESSION['logic_erp']['user_id']."*.xls") as $filename) {
	@unlink($filename);
	}
	$name=time();
	$filename=$_SESSION['logic_erp']['user_id']."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,$html);
	echo "$html****$filename";*/
		
}


if($action=="report_generate4")//Show 3
{
	
	$data=explode("_",$data);
	if($data[0]==0) $company_name="%%"; else $company_name=$data[0];
	//if(trim($data[1])!="") $job_number="%".$data[1]."%"; else  $job_number="%%";
	//if(trim($data[2])!="") $txt_style_number="%".$data[2]."%"; else $txt_style_number="%%";
	//if(trim($data[3])!="") $txt_po_number="%".trim($data[3])."%"; else $txt_po_number="%%";
	//if(trim($data[4])!="") $txt_order_qnty="%".$data[4]."%"; else $txt_order_qnty="%%";
	//if(trim($data[5])!="") $buyer_name="%".$data[5]."%"; else $buyer_name="%%";
	$company_id=str_replace("'","",$cbo_company_id);
	$txt_job_number=str_replace("'","",$txt_job_number);
	$buyer_name=str_replace("'","",$buyer_name);
	$txt_agent_name=str_replace("'","",$txt_agent_name);
	$txt_po_number=str_replace("'","",$txt_po_number);
	$txt_style_number=str_replace("'","",$txt_style_number);
	$txt_order_qnty=str_replace("'","",$txt_order_qnty);
	$cbo_date_category=str_replace("'","",$cbo_date_category);
	$cbo_location_id=str_replace("'","",$cbo_location_id);
	$txt_internal_file=str_replace("'","",$txt_internal_file);
	//echo $cbo_date_category.'XXX';
		
/*	if($cbo_location_id=="") $location_id=""; else $location_id=$cbo_location_id;
	if($location_id!=0)
	{
		$home_page_location_con=" and b.location_name like '$location_id'";
	}
*/	
	if($cbo_location_id!=0)
	{
		$location_con=" and b.location_name=$cbo_location_id";
	}
	
	//echo $home_page_location_con;
	if(trim($buyer_name)!="") 
	{
		$buyer_type_cond="and b.buyer_name in(select id from lib_buyer where LOWER(buyer_name) like LOWER('%$buyer_name%') and status_active=1 and is_deleted=0)";	
	}
	else
	{
		$buyer_type_cond="";
	}
	if(trim($txt_agent_name)!="") 
	{
		$agent_cond="and b.agent_name in(select a.id from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company=$cbo_company_id and LOWER(a.buyer_name) like LOWER('%$txt_agent_name%') and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (20,21)))";	
	}
	else
	{
		$agent_cond="";
	}
	
	//$start_date = return_field_value("min(a.pub_shipment_date)","wo_po_break_down a, wo_po_details_master b"," b.job_no=a.job_no_mst and b.company_name like '$company_id' and a.shiping_status!=3  and a.is_confirmed=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_cond $item_cond"); //and b.job_no_prefix_num like '$txt_job_number' and b.style_ref_no like '$txt_style_number' and a.po_number like '$txt_po_number' and a.po_quantity like '$txt_order_qnty'

	$end_date=date("Y-m-01"); 
	$start_month=date("Y-m",strtotime($start_date));
	//echo $end_date2.'DD';
	if($db_type==2)
	{
		$start_date=change_date_format($start_date,'yyyy-mm-dd','-',1);
		$end_date2=change_date_format($end_date2,'yyyy-mm-dd','-',1);
	}
	
	//$total_months=datediff("m",$start_month,$end_month);
	//$dd=strtotime(date("Y-m-d", time()))
	$current_month_end_date=date("Y-m-d",strtotime("-1 days"));
	if($end_date!="")
	{
		$str_cond="and a.pub_shipment_date between '$start_date' and '$previous_month_end_date'";
		
		$end_date3=date("Y-m-01",strtotime($end_date2));
		if($db_type==2)
		{
			$end_date3=change_date_format($end_date3,'yyyy-mm-dd','-',1);
		}
		$str_cond_curr="and a.pub_shipment_date between '$end_date3' and '$current_month_end_date'";
	}
	else
	{
		$str_cond="";
		$str_cond_curr="";
	}
	//echo $str_cond .'=='.$str_cond_curr;die;
	
	if($db_type==0)
	{
		$date_start= change_date_format($current_month_end_date,"yyyy-mm-dd");
		//$date_end= change_date_format($date_end,"yyyy-mm-dd");
	}
	else
	{
		$date_start= change_date_format($current_month_end_date,"dd-mm-yyyy","-",1);
		//$date_end= change_date_format($date_end,"dd-mm-yyyy","-",1);
	}
	
	$str_cond="and a.pub_shipment_date<='$date_start' ";
	
	$sql_summary_ex_factory=return_library_array("SELECT po_break_down_id,sum(ex_factory_qnty) AS ex_factory_qnty from pro_ex_factory_mst  where status_active=1 and is_deleted=0 group by po_break_down_id",'po_break_down_id','ex_factory_qnty');
	if($txt_internal_file)
	{
		$int_ref_cond="and LOWER(a.grouping) like LOWER('%$txt_internal_file%')";
	}
	else $int_ref_cond="";
	
	  $production_sql="select c.po_break_down_id as po_id,
		sum(CASE WHEN c.production_type =1 THEN c.production_quantity ELSE 0 END) AS cutting_qnty,
		sum(CASE WHEN c.production_type =5 THEN c.production_quantity ELSE 0 END) AS sewing_out_qnty,	
		sum(CASE WHEN c.production_type =8 THEN c.production_quantity ELSE 0 END) AS finish_qnty
		from  pro_garments_production_mst c ,wo_po_break_down a,wo_po_details_master b
		where c.po_break_down_id=a.id and a.is_confirmed=1 and a.shiping_status!=3  and c.status_active=1 and c.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and c.production_type in(1,5,8) and b.job_no=a.job_no_mst and b.company_name like '$company_id' $location_con and b.job_no_prefix_num like '%$txt_job_number%' and LOWER(b.style_ref_no) like LOWER('%$txt_style_number%')   and LOWER(a.po_number) like LOWER('%$txt_po_number%') and a.po_quantity like '%$txt_order_qnty%' $int_ref_cond   $buyer_type_cond $agent_cond $item_cond $str_cond 
		group by c.po_break_down_id order by c.po_break_down_id";
		//echo $production_sql;die;
		
		
		 // and LOWER(a.po_number) like LOWER('%$txt_internal_file%')
		
		$production_result=sql_select($production_sql);
		
		$tot_cutting_qnty=$tot_sewing_out_qnty=$tot_finish_qnty=$tot_prev_cut_qnty=$tot_prev_sew_qnty=$tot_prev_finish_qnty=$tot_current_sewing_qty=$tot_current_finish_qty=0;
		$tot_plan_cut_qnty=0;
		foreach($production_result as $row)
		{
			$prod_qty_arr[$row[csf('po_id')]]['cutting_qnty']=$row[csf('cutting_qnty')];
			$prod_qty_arr[$row[csf('po_id')]]['sewing_out_qnty']=$row[csf('sewing_out_qnty')];
			$prod_qty_arr[$row[csf('po_id')]]['finish_qnty']=$row[csf('finish_qnty')];
			
			if($extended_ship_date!='')
			{
				$pub_current_month=date("Y-m", strtotime($extended_ship_date));
			}
			else
			{
				$pub_current_month=date("Y-m", strtotime($pub_ship_date));
			}
			
			if($pub_current_month==$current_month)//Current Month
			{
				//$curr_po_qty=$po_wise_arr2[$row[csf('po_id')]]['prev_po_qty'];
				////$curr_plan_cut=$po_wise_arr2[$row[csf('po_id')]]['prev_plan_cut'];
				//$tot_current_cut_qty+=$curr_plan_cut-$row[csf('cutting_qnty')];
				//$tot_current_sewing_qty+=$curr_po_qty-$row[csf('sewing_out_qnty')];
				//$tot_current_finish_qty+=$curr_po_qty-$row[csf('finish_qnty')];
			}
			
		}
		//echo $tot_plan_cut_qnty.'D';
		unset($production_result);
		//print_r($prod_qty_arr);
		if($txt_internal_file) $txt_internal_fileCond="and LOWER(a.grouping) like LOWER('%$txt_internal_file%')";else $txt_internal_fileCond="";
		
		$sql_summary_insp=sql_select( "SELECT a.id,a.po_quantity, b.total_set_qnty,  c.inspection_qnty,c.inspection_status from wo_po_break_down a, wo_po_details_master b,pro_buyer_inspection c where b.job_no=a.job_no_mst and a.id=c.po_break_down_id and c.job_no=b.job_no  and b.company_name like '$company_id'  $location_con and b.job_no_prefix_num like '%$txt_job_number%' and LOWER(b.style_ref_no) like LOWER('%$txt_style_number%') and a.shiping_status!=3 and LOWER(a.po_number) like LOWER('%$txt_po_number%') and a.po_quantity like '%$txt_order_qnty%'   and c.inspection_status=1  and a.is_confirmed=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $txt_internal_fileCond $buyer_type_cond $agent_cond $item_cond $str_cond order by a.pub_shipment_date DESC");
		 
		
		
		 $po_id_arr=array();
		foreach( $sql_summary_insp as $row)
		{
			$inspect_qty_arr[$row[csf('id')]]+=$row[csf('inspection_qnty')];
			//$po_id_arr[$row[csf('id')]]=$row[csf('id')];
		}
		unset($sql_summary_insp);
		
	
		$sql_orderlevel="SELECT b.team_leader,b.dealing_marchant,a.id, a.po_number, a.pub_shipment_date,a.extended_ship_date,a.sea_discount,a.air_discount,a.extend_ship_mode,a.po_total_price, b.order_uom, a.details_remarks, b.buyer_name, b.agent_name, b.company_name, b.style_ref_no, b.gmts_item_id, b.job_no_prefix_num, a.shiping_status, a.job_no_mst, a.grouping, a.po_quantity, b.total_set_qnty, a.plan_cut, (a.unit_price/b.total_set_qnty) as unit_price,b.set_smv from wo_po_break_down a, wo_po_details_master b where b.job_no=a.job_no_mst and b.company_name like '$company_id'  $location_con and b.job_no_prefix_num like '%$txt_job_number%' and LOWER(b.style_ref_no) like LOWER('%$txt_style_number%') and a.shiping_status!=3 and LOWER(a.po_number) like LOWER('%$txt_po_number%') and a.po_quantity like '%$txt_order_qnty%'   and a.is_confirmed=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $txt_internal_fileCond $buyer_type_cond $agent_cond $item_cond $str_cond order by a.pub_shipment_date DESC";
		
		
		
		
		$sql_order_level=sql_select($sql_orderlevel);
		$row_tot=count($sql_order_level);
		$tot_pending_po_qty_previ=$tot_prev_po_val=$tot_prev_PendingSAH=$tot_pre_plan_cut=$tot_current_pending_po_quantity=$tot_current_pending_po_val=$tot_current_PendingSAH=$tot_current_plan_cut=0;
		
		$all_po_id="";$curr_po_id="";
		$end_month=date("Y-m",strtotime("-1 days"));
		$current_month=date("Y-m", strtotime($end_month));
		//$current_month=date("Y-m", strtotime($current_month));
		//echo $current_month;die;
		$buyer_wise_arr=array();
		foreach($sql_order_level as $row)
		{
			if($all_po_id=="") $all_po_id=$row[csf("id")]; else $all_po_id.=",".$row[csf("id")];
			$po_id_arr[$row[csf("id")]]=$row[csf("id")];
			$pub_shipment_date=date("Y-m-d",strtotime($row[csf('pub_shipment_date')]));
			$extended_ship_date=date("Y-m-d",strtotime($row[csf('extended_ship_date')]));
			//echo $cbo_date_category.'DD';
			if($cbo_date_category==2) //Exten Ship
			{
				if($extended_ship_date=='')
				{
					 $extended_ship_date=$current_month_end_date;
					 //echo $extended_ship_date.'x';
				}
				//echo $date_cond_dynamic="($extended_ship_date<=$current_month_end_date)";
				$date_cond_dynamic = ($extended_ship_date<=$current_month_end_date);
			}
			else
			{
				//if($row[csf('extended_ship_date')]!='') 
				$extended_ship_date=$pub_shipment_date;
				$date_cond_dynamic= ($pub_shipment_date!='');
			}
			$inspect_qty=$inspect_qty_arr[$row[csf('id')]];
			$po_quantity=$row[csf('po_quantity')]*$row[csf('total_set_qnty')];
			// && $inspect_qty<$po_quantity
			
			//echo $date_cond_dynamic.',';
				//echo $extended_ship_date.'='.$current_month_end_date.'<br>';
			//if($extended_ship_date<=$current_month_end_date) //Check here
			//echo $inspect_qty.'='.$po_quantity.'='.$row[csf('id')].'<br>';
			if($date_cond_dynamic && $inspect_qty<$po_quantity) //Check here
			{
			
				$month_date='';
				
				if($row[csf('extended_ship_date')]!='')
				{
					if($cbo_date_category==1)
					{
					 $month_date=date("Y-m",strtotime($row[csf('pub_shipment_date')]));
					 $pub_current_month=date("Y-m", strtotime($row[csf('pub_shipment_date')]));
					}
					else
					{
					 $month_date=date("Y-m",strtotime($row[csf('extended_ship_date')]));
					
					 $pub_current_month=date("Y-m", strtotime($row[csf('extended_ship_date')]));
					}
					 
				}
				else
				{
					if($row[csf('extended_ship_date')]!='')
					{
						if($cbo_date_category==1)
						{
							$month_date=date("Y-m",strtotime($row[csf('pub_shipment_date')]));
							$pub_current_month=date("Y-m", strtotime($row[csf('pub_shipment_date')]));
						}
						else
						{
							$month_date=date("Y-m",strtotime($row[csf('extended_ship_date')]));
							$pub_current_month=date("Y-m", strtotime($row[csf('extended_ship_date')]));
						}
					}
					else
					{
						if($cbo_date_category==1)
						{
							$month_date=date("Y-m",strtotime($row[csf('pub_shipment_date')]));
							$pub_current_month=date("Y-m", strtotime($row[csf('pub_shipment_date')]));
						}
						else
						{
							$month_date=date("Y-m",strtotime($row[csf('pub_shipment_date')]));
							$pub_current_month=date("Y-m", strtotime($row[csf('pub_shipment_date')]));
						}
					}
					
				}
				if($row[csf('shiping_status')]==2)
					{
						$ex_fact_qty=$sql_summary_ex_factory[$row[csf('id')]];
					}
					else
					{
						$ex_fact_qty=0;
					}
					$plan_cut_qty=$row[csf('plan_cut')]*$row[csf('total_set_qnty')];
					//echo $ex_fact_qty.'='.$row[csf('shiping_status')].'<br>';
					if($current_month==$pub_current_month)//Current Month
					{
						$curr_po_quantity=$row[csf('po_quantity')]*$row[csf('total_set_qnty')];
						if(($curr_po_quantity-$ex_fact_qty)>0){
							$tot_current_pending_po_quantity+=$curr_po_quantity-$ex_fact_qty;
							$tot_pending_qty=$curr_po_quantity-$ex_fact_qty;
						//	echo $pub_current_month.'='.$current_month.'<br>';
							if($tot_pending_qty>0)
							{
							$tot_current_pending_po_val+=$tot_pending_qty*$row[csf('unit_price')];
							}
							$tot_current_plan_cut+=$row[csf('plan_cut')]*$row[csf('total_set_qnty')];
							$tot_current_PendingSAH+=(($curr_po_quantity-$ex_fact_qty)*$row[csf('set_smv')])/60;
							//Production
							
							
							if($plan_cut_qty-$prod_qty_arr[$row[csf('id')]]['cutting_qnty']>0)
							{
								$tot_current_cut_qty+=$plan_cut_qty-$prod_qty_arr[$row[csf('id')]]['cutting_qnty'];
							}
							if($curr_po_quantity-$prod_qty_arr[$row[csf('id')]]['sewing_out_qnty']>0)
							{
								$tot_current_sewing_qty+=$curr_po_quantity-$prod_qty_arr[$row[csf('id')]]['sewing_out_qnty'];
							}
							if($curr_po_quantity-$prod_qty_arr[$row[csf('id')]]['finish_qnty']>0)
							{
								$tot_current_finish_qty+=$curr_po_quantity-$prod_qty_arr[$row[csf('id')]]['finish_qnty'];
							}
							
							//$tot_current_cut_qty+=$curr_plan_cut-$row[csf('cutting_qnty')];
							if($curr_po_id=="") $curr_po_id=$row[csf("id")]; else $curr_po_id.=",".$row[csf("id")];
						}
					}
					
					$prev_po_quantity=$row[csf('po_quantity')]*$row[csf('total_set_qnty')];
					$prev_plan_cut_qty=$row[csf('plan_cut')]*$row[csf('total_set_qnty')];
					$pending_po_quantity=$prev_po_quantity-$ex_fact_qty;
					if($prev_po_quantity-$prod_qty_arr[$row[csf('id')]]['finish_qnty']>0)
					{
						$tot_prev_finish_qnty+=$prev_po_quantity-$prod_qty_arr[$row[csf('id')]]['finish_qnty'];
					}
					if($prev_po_quantity-$prod_qty_arr[$row[csf('id')]]['sewing_out_qnty']>0)
					{
						$tot_prev_sew_qnty+=$prev_po_quantity-$prod_qty_arr[$row[csf('id')]]['sewing_out_qnty'];
					}
					
					if($prev_plan_cut_qty-$prod_qty_arr[$row[csf('id')]]['cutting_qnty']>0)
					{
						$tot_prev_cut_qnty+=$prev_plan_cut_qty-$prod_qty_arr[$row[csf('id')]]['cutting_qnty'];
					}
					
					if($pending_po_quantity>0)
					{
					$tot_prev_po_val+=$pending_po_quantity*$row[csf('unit_price')];
					}
					if(($prev_po_quantity-$ex_fact_qty)>0){
						$tot_prev_pending_po_qty_previ+=$prev_po_quantity-$ex_fact_qty;
					}
					if($pending_po_quantity>0)
					{
					$tot_prev_PendingSAH+=($pending_po_quantity*$row[csf('set_smv')])/60;
					}
					$pre_plan_cut_qty=$row[csf('plan_cut')]*$row[csf('total_set_qnty')];
					$tot_pre_plan_cut+=$pre_plan_cut_qty;
					//$prev_PendingSAH+=($pending_po_quantity*$row[csf('set_smv')])/60;
				//echo $month_date;die;
				if($pending_po_quantity>0)
				{
			 	$po_wise_arr[$month_date][$row[csf('id')]]['pending_po_quantity']=$pending_po_quantity;
				$po_wise_arr[$month_date][$row[csf('id')]]['shipout_qty']=$ex_fact_qty;
				$po_wise_arr[$month_date][$row[csf('id')]]['pending_po_value']=$pending_po_quantity*$row[csf('unit_price')];
				$po_wise_arr[$month_date][$row[csf('id')]]['po_total_price']=$row[csf('po_total_price')];
				$po_wise_arr[$month_date][$row[csf('id')]]['sea_discount']=$row[csf('sea_discount')];
				$po_wise_arr[$month_date][$row[csf('id')]]['air_discount']=$row[csf('air_discount')];
				$po_wise_arr[$month_date][$row[csf('id')]]['extend_ship_mode']=$row[csf('extend_ship_mode')];
				
				$po_wise_arr[$month_date][$row[csf('id')]]['pub_shipment_date']=$row[csf('pub_shipment_date')];
				$po_wise_arr[$month_date][$row[csf('id')]]['extended_ship_date']=$row[csf('extended_ship_date')];
				$po_wise_arr[$month_date][$row[csf('id')]]['dealing_marchant']=$row[csf('dealing_marchant')];
				
				$po_wise_arr[$month_date][$row[csf('id')]]['order_uom']=$row[csf('order_uom')];
				$po_wise_arr[$month_date][$row[csf('id')]]['details_remarks']=$row[csf('details_remarks')];
				$po_wise_arr[$month_date][$row[csf('id')]]['buyer_name']=$row[csf('buyer_name')];
				$po_wise_arr[$month_date][$row[csf('id')]]['agent_name']=$row[csf('agent_name')];
				$po_wise_arr[$month_date][$row[csf('id')]]['company_name']=$row[csf('company_name')];
				$po_wise_arr[$month_date][$row[csf('id')]]['style_ref_no']=$row[csf('style_ref_no')];
				$po_wise_arr[$month_date][$row[csf('id')]]['gmts_item_id']=$row[csf('gmts_item_id')];
				$po_wise_arr[$month_date][$row[csf('id')]]['job_no_prefix_num']=$row[csf('job_no_prefix_num')];
				$po_wise_arr[$month_date][$row[csf('id')]]['job_no_mst']=$row[csf('job_no_mst')];
				$po_wise_arr[$month_date][$row[csf('id')]]['po_quantity']=$row[csf('po_quantity')];
				$po_wise_arr[$month_date][$row[csf('id')]]['total_set_qnty']=$row[csf('total_set_qnty')];
				$po_wise_arr[$month_date][$row[csf('id')]]['plan_cut']=$row[csf('plan_cut')];
				$po_wise_arr[$month_date][$row[csf('id')]]['unit_price']=$row[csf('unit_price')];
				$po_wise_arr[$month_date][$row[csf('id')]]['set_smv']=$row[csf('set_smv')];
				$po_wise_arr[$month_date][$row[csf('id')]]['po_number']=$row[csf('po_number')];
				$po_wise_arr[$month_date][$row[csf('id')]]['grouping']=$row[csf('grouping')];
				$po_wise_arr[$month_date][$row[csf('id')]]['team_leader']=$row[csf('team_leader')];
				
				$po_wise_arr[$month_date][$row[csf('id')]]['prev_plan_cut']=$pre_plan_cut_qty;
				$po_wise_arr[$month_date][$row[csf('id')]]['prev_po_qty']=$prev_po_quantity;
				
					//Buyer wise summary
				$buyer_wise_arr[$month_date][$row[csf('buyer_name')]]['pending_poqty']+=$pending_po_quantity;
				$buyer_wise_arr[$month_date][$row[csf('buyer_name')]]['total_set_qnty']=$row[csf('total_set_qnty')];
				$buyer_wise_arr[$month_date][$row[csf('buyer_name')]]['plan_cut']+=$row[csf('plan_cut')];
				$buyer_wise_arr[$month_date][$row[csf('buyer_name')]]['unit_price']=$row[csf('unit_price')];
				$buyer_wise_arr[$month_date][$row[csf('buyer_name')]]['po_value']+=$pending_po_quantity*$row[csf('unit_price')];
				$buyer_wise_arr[$month_date][$row[csf('buyer_name')]]['buyer_sah']+=($pending_po_quantity*$row[csf('set_smv')])/60;
				
				$po_wise_arr2[$row[csf('id')]]['prev_plan_cut']=$pre_plan_cut_qty;
				$po_wise_arr2[$row[csf('id')]]['prev_po_qty']=$prev_po_quantity;
				$po_wise_arr2[$row[csf('id')]]['pub_shipment_date']=$row[csf('pub_shipment_date')];
				$po_wise_arr2[$row[csf('id')]]['extended_ship_date']=$row[csf('extended_ship_date')];
					
				}
				
			
			} //Check End
			
		}
		
		
		
		
		//echo $curr_po_id.'=';
		//print_r($po_wise_arr);die;
		//echo $tot_current_pending_po_quantity.'DD';
		$po_ids=count(array_unique(explode(",",$all_po_id)));
		$po_numIds=chop($all_po_id,','); $poIds_cond="";
		if($all_po_id!='' || $all_po_id!=0)
		{
			if($db_type==2 && $po_ids>1000)
			{
				$poIds_cond=" and (";
				$poIdsArr=array_chunk(explode(",",$po_numIds),990);
				foreach($poIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$poIds_cond.=" c.po_break_down_id  in($ids) or ";
				}
				$poIds_cond=chop($poIds_cond,'or ');
				$poIds_cond.=")";
			}
			else
			{
				$poIds_cond=" and  c.po_break_down_id  in($all_po_id)";
			}
		}
		//b.job_no=a.job_no_mst and b.company_name like '$company_id' and b.job_no_prefix_num like '%$txt_job_number%' and LOWER(b.style_ref_no) like LOWER('%$txt_style_number%') and a.shiping_status!=3 and LOWER(a.po_number) like LOWER('%$txt_po_number%') and a.po_quantity like '%$txt_order_qnty%'   and a.is_confirmed=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_type_cond $agent_cond $item_cond $str_cond order by a.pub_shipment_date DESC
		
		//die;
	ob_start();	
?>
<!--=============================================================Total Summary Start=============================================================================================-->
    <div style="width:1300px;">
    <table width="1300px" style=" float:left;"  cellspacing="0">
        <tr>
            <td colspan="7" align="center" ><font size="3"><strong><?php echo $company_details[$company_id]; ?></strong></font></td>
        </tr>
        <tr class="form_caption">
            <td colspan="7" align="center"><font size="3"><strong>Total Pending Order Summary </strong></font></td>
        </tr>
    </table>
	<table border="1" style=" float:left;" rules="all" class="rpt_table" width="1200">
        <thead>
            <th width="30">SL</th>
            <th width="130"> Month </th>
            <th width="130">Pending PO Qty. </th>
            <th width="140">Pending PO Value</th>
            <th width="80">Pending SAH</th>
            <th width="135">Plan Cut Qty.</th>
            <th width="125">Cutting Pending </th>
            <th width="125">Sewing Pending</th>
            <th>Finishing Pending </th>
        </thead>
		<?
		$prev_PendingSAH=0;$prev_po_qnty=0; $prev_po_val=0; $prev_sew_qnty=0; $prev_cut_qnty=0; $prev_finish_qnty=0;
		$curr_month=date("F",strtotime($current_month_end_date)).", ".date("Y",strtotime($current_month_end_date));
		
		$summary_grand_total_po_qny=0;
		$summary_grand_total_lc_value=0;
		$summary_grand_total_cut_qny=0;
		$summary_grand_total_sewing_qny=0;
		$summary_grand_total_finish_qny=0;
		$bgcolor1='#E9F3FF';
		$bgcolor2='#FFFFFF';
		?>
        <tr bgcolor="<? echo $bgcolor1; ?>" onclick="change_color('tr1st_1','<? echo $bgcolor1; ?>')" id="tr1st_1">
            <td>1</td>
            <td>Previous To Current Month</td>
            <td align="right"  title="Prev PO Qty=<? echo $tot_prev_pending_po_qty_previ;?> -Shipout Qty=<? echo $tot_current_pending_po_quantity;?>"><? 
			$tot_prev_pending_po_qty_previ=$tot_prev_pending_po_qty_previ-$tot_current_pending_po_quantity;
			echo number_format($tot_prev_pending_po_qty_previ,0); $summary_grand_total_po_qny+=$tot_prev_pending_po_qty_previ; ?></td>
            <td align="right" title="Prev PO Value=<? echo $tot_prev_po_val;?>-Shipout Value=<? echo $tot_current_pending_po_val;?>"><? 
			$tot_prev_po_val=$tot_prev_po_val-$tot_current_pending_po_val;$tot_prev_PendingSAH=$tot_prev_PendingSAH-$tot_current_PendingSAH;
			$tot_pre_plan_cut=$tot_pre_plan_cut-$tot_current_plan_cut;$tot_prev_cut_qnty=$tot_prev_cut_qnty-$tot_current_cut_qty;
			$tot_prev_sew_qnty=$tot_prev_sew_qnty-$tot_current_sewing_qty;$tot_prev_finish_qnty=$tot_prev_finish_qnty-$tot_current_finish_qty;
			
			echo number_format($tot_prev_po_val,2); $summary_grand_total_lc_value+=$tot_prev_po_val; ?></td>
            <td align="right"  title="Prev Pending Po Qty*SMV/60"><? echo number_format($tot_prev_PendingSAH,2);$summary_grand_total_PendingSAH+=$tot_prev_PendingSAH;?></td>
            <td align="right"> <? echo number_format($tot_pre_plan_cut,0); $summary_grand_total_plan_cut+=$tot_pre_plan_cut; ?></td>
            <td align="right" title="Prev Plan Cut -Cutting Prod. Qty"><? echo number_format($tot_prev_cut_qnty,0); $summary_grand_total_cut_qny+=$tot_prev_cut_qnty; ?></td>
            <td align="right"  title="Prev PO Qty -Cutting Prod. Qty"><? echo number_format($tot_prev_sew_qnty,0); $summary_grand_total_sewing_qny+=$tot_prev_sew_qnty; ?></td>
            <td align="right"  title="Prev PO Qty -Finish Prod. Qty"><? echo number_format($tot_prev_finish_qnty,0); $summary_grand_total_finish_qny+=$tot_prev_finish_qnty; ?></td>
        </tr>
        <tr bgcolor="<? echo $bgcolor2; ?>" onclick="change_color('tr1st_2','<? echo $bgcolor2; ?>')" id="tr1st_2">
            <td>2</td>
            <td> <? echo $curr_month; ?> </td>
            <td align="right"  title="Current PO Qty -Shipout Qty"><? echo number_format($tot_current_pending_po_quantity,0); $summary_grand_total_po_qny+=$tot_current_pending_po_quantity; ?></td>
            <td align="right" title="Current PO Value -Shipout Value"><? echo number_format($tot_current_pending_po_val,2); $summary_grand_total_lc_value+=$tot_current_pending_po_val; ?></td>
            <td align="right"  title="Current Pending Po Qty*SMV/60"><? echo number_format($tot_current_PendingSAH,2);$summary_grand_total_PendingSAH+=$tot_current_PendingSAH;?></td>
            <td align="right"><? echo number_format($tot_current_plan_cut,0); $summary_grand_total_plan_cut+=$tot_current_plan_cut; ?></td>
            <td align="right" title="Plan Cut -Cutting Prod. Qty"><? echo number_format($tot_current_cut_qty,0); $summary_grand_total_cut_qny+=$tot_current_cut_qty; ?></td>
            <td align="right"  title="PO Qty -Sewing Prod. Qty"><? echo number_format($tot_current_sewing_qty,0); $summary_grand_total_sewing_qny+=$tot_current_sewing_qty; ?></td>
            <td align="right"  title="PO Qty -Finish Prod. Qty"><? echo number_format($tot_current_finish_qty,0); $summary_grand_total_finish_qny+=$tot_current_finish_qty; ?></td>
        </tr>
        <tfoot>
            <th colspan="2" align="right">Total</th>
            <th align="right"><? echo number_format($summary_grand_total_po_qny,0); ?></th>
            <th align="right"><? echo number_format($summary_grand_total_lc_value,2); ?></th>
            <th align="right"><? echo number_format($summary_grand_total_PendingSAH,2); ?></th>
            <th align="right"><? echo number_format($summary_grand_total_plan_cut,0); ?></th>
            <th align="right"><? echo number_format($summary_grand_total_cut_qny,0); ?></th>
            <th align="right"><? echo number_format($summary_grand_total_sewing_qny,0); ?></th>
            <th align="right"><? echo number_format($summary_grand_total_finish_qny,0); ?> </th>
        </tfoot>
    </table> 
   <? //die; ?>
    <br/> 
    <table width="1300">
        <tr>
		<td valign="top">
		<?
			foreach( $buyer_wise_arr as $month_key=>$row_month)
			{
						?>
				<div style="width:400px; float:left; margin:5px;">
				<table width="400px"  cellspacing="0"  class="display">
					<tr>
						<td colspan="4" align="center"><font size="3"><strong>Total Summary <? $month_name=date("F",strtotime($month_key)).", ".date("Y",strtotime($month_key)); echo $month_name; ?></strong></font></td>
					</tr>
				</table>
				<table width="400px" class="rpt_table" border="1" rules="all">
					<thead>
						<th width="30">SL</th>
						<th width="100">Buyer Name</th>
						<th width="90">Pending Qnty</th>
						<th width="90">Pending Value</th>
						<th width="70">Pending  SAH</th>
					</thead>
				<?
				$tot_buyer_po_qnty=$tot_buyer_po_val=$tot_buyer_SAH=0;
				$m=1;
				foreach( $row_month as $buyer_key=>$row)
				{
				if ($m%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$tot_buyer_order_quantity=$row['pending_poqty'];
				$buyer_order_val=$row['po_value'];
				$buyer_SAH=$row['buyer_sah'];
			?>
			
						<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr1st_<? echo $i; ?><? echo $d; ?>','<? echo $bgcolor; ?>')" id="tr1st_<? echo $i; ?><? echo $d; ?>">
							<td><? echo $m; ?></td>
							<td><p><? echo $buyer_short_name_arr[$buyer_key]; ?></p></td>
							<td align="right"><? echo number_format($tot_buyer_order_quantity,0); $tot_buyer_po_qnty+=$tot_buyer_order_quantity; ?></td>
							<td align="right"><? echo number_format($buyer_order_val,2); $tot_buyer_po_val+=$buyer_order_val; ?></td>
							<td align="right"><? echo number_format($buyer_SAH,2); $tot_buyer_SAH+=$buyer_SAH; ?></td>
                          </tr>
			<?	
				$m++;
				}
				?>
				 <tfoot>
						<th colspan="2" align="right">Total</th>
						<th align="right"><? echo number_format($tot_buyer_po_qnty,0); ?></th>
						<th align="right"><? echo number_format($tot_buyer_po_val,2); ?></th>
						<th align="right"><? echo number_format($tot_buyer_SAH,2); ?></th>
                  </tfoot>
			</table>
			</div>	
				<?
				
			}
		?>
		</td>
    	</tr>
    </table>
	 </div>
    <br/>
    <?
    //ob_start();	
    ?>
    <div>
    <div align="left" style="background-color:#E1E1E1; color:#000; font-size:14px; font-family:Georgia, 'Times New Roman', Times, serif; width:3940px"><strong><u><i> Details Report</i></u></strong></div>        
    	<table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="3940">
        	<thead>
            	<tr>
                    <th width="40">SL</th>
                    <th width="70">Job No</th>
                    <th width="80">Buyer Name</th>
                    <th width="80">Agent Name</th>
                    <th width="100">Internal Ref.</th>
                    <th width="110">PO Number</th>
                    <th width="50">Image</th> 
                    <th width="120">Style Name</th>
                    <th width="140">Item Name</th>
                    <th width="60">Sew SMV</th>
                    <th width="100">PO Qnty.</th>
                    <th width="100">Plan Cut Qty.</th>
                    <th width="90">Ship Date</th>
					<th width="80">Ext.Ship Date</th>
                    <th width="60">Delay</th>
                    <th width="90">Fabric Booking No</th>
                    
                    <th width="90">Yarn Allocation</th>
                    <th width="90">Yarn Issue</th>
                    <th width="90">
                        Yarn Issue Balance <span style="font-size:9px">[Grey Req As FB - (Yarn Issue + Net Trans)]</span>
                    </th>
                    <th width="90">Grey Req.</th>
                    <th width="90">Grey Prod.</th>
                    
                    <th width="90">
                        Knitting Balance <span style="font-size:9px">[Grey Req. As Booking - Grey Prod.]</span>
                    </th>
                    <th width="90">Fabrics  Req.</th>
                    <th width="90"> Fab. Avl.</th>
                    
                    <th width="90">
                        Finish Fab. Balance <span style="font-size:9px">[Finish Req. As Booking - Finish Prod.]</span>
                    </th>

                    <th width="90">Cut Qnty</th>
                    <th width="90">Cut Pending</th>
                    <th width="80">Cut Wastage</th>
                    <th width="90">Sewing Qnty</th>
                    <th width="90">Sew Pending</th>
                    <th width="90">Finish Qnty</th>
                    <th width="90">Finish Pending</th>
                    <th width="90">Ship Qnty</th>
                    <th width="100">Pending PO Qnty.</th>
                    <th width="100">Pending PO Value.</th>
                    <th width="90">Pending SAH</th>
                    <th width="140">Team Leader</th>
                    <th width="140">Dealing Merchant</th>
					<th width="100">Extended Ship Mode</th>
                    <th width="100">Sea Discount On FOB</th>
                    <th width="100">Air Discount On FOB</th>
					
                    <th>Remarks</th>
                </tr>
                <tr>
                	<th width="40">&nbsp;</th>
                	<th width="70"><input type="text" value="<? echo str_replace("%","",$job_number); ?>" onkeyup="show_inner_filter(event);" name="txt_job_number" id="txt_job_number" class="text_boxes" style="width:50px" /></th>
                    <th width="80"><input type="text" name="buyer_name" onkeyup="show_inner_filter(event);" value="<? echo str_replace("%","",$buyer_name); ?>" id="buyer_name" class="text_boxes" style="width:60px" /></th>
                    
                    <th width="80"><input type="text" name="txt_agent_name" onkeyup="show_inner_filter(event);" value="<? echo str_replace("%","",$txt_agent_name); ?>" id="txt_agent_name" class="text_boxes" style="width:60px" /></th>
                    
                    <th width="100"><input type="text" name="txt_internal_file" onkeyup="show_inner_filter(event);" value="<? echo str_replace("%","",$txt_internal_file); ?>" id="txt_internal_file" class="text_boxes" style="width:60px" /></th> 
                    
                    <th width="110"><input type="text" name="txt_po_number" onkeyup="show_inner_filter(event);" value="<? echo str_replace("%","",$txt_po_number); ?>" id="txt_po_number" class="text_boxes" style="width:80px" /></th>
                    <th width="50">&nbsp;</th>
                    <th width="120"><input type="text" name="txt_style_number" onkeyup="show_inner_filter(event);" value="<? echo str_replace("%","",$txt_style_number); ?>" id="txt_style_number" class="text_boxes" style="width:80px" /></th>
                    <th width="100">&nbsp;</th>
                    <th width="50">&nbsp;</th>
                    <th width="100"><input type="text" name="txt_order_qnty" onkeyup="show_inner_filter(event);" value="<? echo str_replace("%","",$txt_order_qnty); ?>" id="txt_order_qnty" class="text_boxes" style="width:80px" /></th>
                    <th colspan="31">&nbsp;</th>
                </tr>
        	</thead>
		</table>
     	<div style="width:3958px; max-height:410px; overflow-y: scroll;" id="scroll_body">
        <table cellspacing="0" cellpadding="0"  width="3940"  border="1" rules="all" class="rpt_table" id="table_body">
			<?
			
			//---------------------------------
			/*$sql="select a.po_break_down_id,a.booking_no,a.booking_no_prefix_num,a.booking_type from wo_booking_mst a where a.company_id=$cbo_company_id and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1";
			$bookingResul=sql_select($sql);
			foreach($bookingResul as $rows){
				if($rows[csf('booking_type')]==4)$bookingType="SF: "; else $bookingType="MF: ";
				$bookingDataArr[$rows[csf('po_break_down_id')]][]=$bookingType.$rows[csf('booking_no_prefix_num')];
			}
			unset($bookingResul);*/
		$yarn_allo="SELECT   po_break_down_id,  item_id, qnty  FROM inv_material_allocation_dtls Where  status_active = 1 and is_deleted=0 and (is_dyied_yarn!=1 or is_dyied_yarn is null)  ".where_con_using_array($po_id_arr,0,'po_break_down_id')."  ";//die;
		$yarn_allo_arr=array();

		foreach(sql_select($yarn_allo) as $v)
		{
			 $yarn_allo_arr[$v[csf("po_break_down_id")]]+=$v[csf("qnty")];
		}

		/*$yarn_issue="SELECT   po_breakdown_id as po_break_down_id,   c.quantity as qnty  FROM INV_ISSUE_MASTER a,INV_TRANSACTION b,ORDER_WISE_PRO_DETAILS c Where a.id=b.mst_id and b.id=c.trans_id and  a.issue_basis=3 and a.entry_form=3 and a.status_active = 1 and b.status_active = 1 and c.status_active = 1  ".where_con_using_array($po_id_arr,0,'po_breakdown_id')."  ";
		$yarn_issue_arr=array();

		foreach(sql_select($yarn_issue) as $v) 
		{
			$yarn_issue_arr[$v[csf("po_break_down_id")]] +=$v[csf("qnty")];
		}

		  $yarn_issue="SELECT   po_breakdown_id as po_break_down_id,   c.quantity as qnty  FROM INV_ISSUE_MASTER a,INV_TRANSACTION b,ORDER_WISE_PRO_DETAILS c Where a.id=b.mst_id and b.id=c.trans_id and  a.issue_basis=3 and a.entry_form=3 and a.status_active = 1 and b.status_active = 1   AND b.transaction_type=2 and c.trans_type=2
       and b.receive_basis = 3  and c.status_active = 1  ".where_con_using_array($po_id_arr,0,'po_breakdown_id')."  ";
		$yarn_issue_arr=array();

		foreach(sql_select($yarn_issue) as $v)  
		{ 
		$yarn_issue_arr[$v[csf("po_break_down_id")]] +=$v[csf("qnty")];
		}*/
		$sql_return_data = sql_select("SELECT c.id, (b.quantity) as returned_qnty from inv_receive_master a,  order_wise_pro_details b, inv_transaction d,wo_po_break_down c  where a.id = d.mst_id and a.entry_form=9 and a.item_category =1 and a.receive_basis = 3 and d.transaction_type=4 and d.item_category=1 and d.id=b.trans_id and b.po_breakdown_id = c.id   and b.trans_type=4 and b.entry_form=9 and b.status_active=1 and b.is_deleted=0 and b.issue_purpose!=2 ".where_con_using_array($po_id_arr,0,'c.id')."");

		$return_qty_arr=array();
		foreach ($sql_return_data as $row) {
			$yarn_issue_return_qty_arr[$row[csf('id')]]['returned_qnty'] += $row[csf('returned_qnty')];
			 	
		}
		
		$recvData=sql_select("select a.po_breakdown_id, sum(quantity) as grey_prod_qnty from order_wise_pro_details a, pro_grey_prod_entry_dtls b where a.dtls_id=b.id and a.prod_id=b.prod_id and a.entry_form =2 and a.is_deleted=0 and a.status_active=1 $poCon4 group by a.po_breakdown_id");
		foreach($recvData as $row)
		{
			$gp_qty_array[$row[csf('po_breakdown_id')]]=$row[csf('grey_prod_qnty')];
		}
		unset($recvData);
			
			
			
		  $sql="select b.po_break_down_id, a.booking_no_prefix_num,a.booking_type,a.item_category,  b.fin_fab_qnty as req_qnty,  b.grey_fab_qnty as grey_req_qnty,
		  (case when a.fabric_source=1 and a.booking_type=1 then b.grey_fab_qnty else 0 end) as grey_fab_qnty_req,
		   (case when a.booking_type=1 then b.grey_fab_qnty else 0 end) as fin_fab_qnty_req
		  
		   from wo_booking_mst a, wo_booking_dtls b where a.company_id=$cbo_company_id and a.booking_no=b.booking_no and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 ".where_con_using_array($po_id_arr,0,'b.po_break_down_id')."";
			$result=sql_select($sql);
			foreach($result as $row)
			{
				if($row[csf('item_category')]==13 || $row[csf('item_category')]==2)
				{
				$tot_req_qnty[$row[csf('po_break_down_id')]]+=$row[csf('grey_req_qnty')];
				$tot_fin_req_qnty[$row[csf('po_break_down_id')]]+=$row[csf('req_qnty')];
				}
				$grey_fab_req_qnty[$row[csf('po_break_down_id')]]+=$row[csf('grey_fab_qnty_req')];
				$grey_fin_fab_req_qnty[$row[csf('po_break_down_id')]]+=$row[csf('fin_fab_qnty_req')];
				if($row[csf('booking_type')]==4)$bookingType="SF: "; else $bookingType="MF: ";
				$bookingDataArr[$row[csf('po_break_down_id')]].=$bookingType.$row[csf('booking_no_prefix_num')].',';
			}
	unset($result);
	$sqls_avl="SELECT b.po_breakdown_id, sum(b.quantity) as quantity from inv_transaction a,order_wise_pro_details b where a.id=b.trans_id and a.item_category in(2,3) and b.entry_form in(37,17,7)  $poCon5 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ".where_con_using_array($po_id_arr,0,'b.po_breakdown_id')." group by b.po_breakdown_id ";// and a.receive_basis in(1,2,4)
		    foreach(sql_select($sqls_avl) as $vals)
		    {
		    	$fabrics_avl_qnty_array[$vals[csf("po_breakdown_id")]]+=$vals[csf("quantity")];
		    }
			
			/*$sql="select d.prod_id,b.quantity as issue_qnty from order_wise_pro_details b, inv_transaction d where d.id=b.trans_id and b.trans_type in(2,6) and d.company_id=$cbo_company_id and b.status_active=1 and b.is_deleted=0";
                    $result=sql_select($sql);
        			foreach($result as $row)
                    {
						//$yarn_issued[$row[csf('prod_id')]]+=$row[csf('issue_qnty')];

					}*/
			
		$dataArrayTrans=sql_select("select po_breakdown_id, 
								sum(CASE WHEN entry_form ='7' THEN quantity ELSE 0 END) AS finish_receive,
								sum(CASE WHEN entry_form ='66' THEN quantity ELSE 0 END) AS finish_receive_roll_wise,
								sum(CASE WHEN entry_form ='2' THEN quantity ELSE 0 END) AS grey_receive,
								
								sum(CASE WHEN entry_form ='46' and trans_type=3 THEN quantity ELSE 0 END) AS recv_rtn_qnty,
								sum(CASE WHEN entry_form ='52' and trans_type=4 THEN quantity ELSE 0 END) AS iss_retn_qnty,
								
								sum(CASE WHEN entry_form ='3' and issue_purpose!=2 THEN quantity ELSE 0 END) AS issue_qnty,
								sum(CASE WHEN entry_form ='9' THEN quantity ELSE 0 END) AS return_qnty,
								sum(CASE WHEN entry_form ='11' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_yarn,
								sum(CASE WHEN entry_form ='11' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_yarn,
								
								sum(CASE WHEN entry_form ='15' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty,
								sum(CASE WHEN entry_form ='15' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty,							
								sum(CASE WHEN entry_form ='37' THEN quantity ELSE 0 END) AS finish_purchase								
								from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form in(7,66,2,46,52,11,3,9,15,37) group by po_breakdown_id");
		foreach($dataArrayTrans as $row)
		{
			
			$finish_receive_qnty_arr[$row[csf('po_breakdown_id')]]=$row[csf('finish_receive')]+$row[csf('finish_receive_roll_wise')];
			$grey_receive_qnty_arr[$row[csf('po_breakdown_id')]]=$row[csf('grey_receive')];			//$yarn_issued[$row[csf('po_breakdown_id')]]+=($row[csf('issue_qnty')]+$row[csf('transfer_in_qnty_yarn')])+($row[csf('return_qnty')]+$row[csf('transfer_out_qnty_yarn')]);
			$yarn_issued[$row[csf('po_breakdown_id')]]=$row[csf('issue_qnty')]+($row[csf('transfer_in_qnty_yarn')]-$row[csf('transfer_out_qnty_yarn')]);
			$net_tensfer[$row[csf('po_breakdown_id')]]=($row[csf('transfer_in_qnty')]-$row[csf('transfer_out_qnty')]);
			$fin_rec_ret[$row[csf('po_breakdown_id')]]+=$row[csf('recv_rtn_qnty')];
		}	
		unset($dataArrayTrans);
			
		$sql_fin_purchase="select c.po_breakdown_id, sum(c.quantity) as finish_purchase from inv_receive_master a, pro_finish_fabric_rcv_dtls b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.dtls_id and a.receive_basis<>9 and a.entry_form=37 and c.entry_form=37 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 group by c.po_breakdown_id";
		$dataArrayFinPurchase=sql_select($sql_fin_purchase);
		foreach($dataArrayFinPurchase as $finRow)
		{
			$fin_purchase[$finRow[csf('po_breakdown_id')]]=$finRow[csf('finish_purchase')];
		}
		unset($dataArrayFinPurchase);
			
			
			//----------------------------------
			$excess_percent_arr=array();
			$standard_excess_cut=sql_select("select id, company_name, slab_rang_start, slab_rang_end, excess_percent from variable_prod_excess_slab");
			foreach( $standard_excess_cut as $excRow)
			{
				$excess_percent_arr[$excRow[csf('company_name')]][$excRow[csf('id')]]['start']=$excRow[csf('slab_rang_start')];
				$excess_percent_arr[$excRow[csf('company_name')]][$excRow[csf('id')]]['end']=$excRow[csf('slab_rang_end')];
				$excess_percent_arr[$excRow[csf('company_name')]][$excRow[csf('id')]]['percent']=$excRow[csf('excess_percent')];
			}
			unset($standard_excess_cut);
            $ii=1; $k=1; $total_po_qnty=$total_pending_po_qnty=0; $total_cut_qnty=0; $total_sew_qnty=0; $total_finish_qnty=0; $total_ship_qnty=0; $total_balance_qnty=0;$total_cut_pending=0;$total_finish_pending=0;$total_sew_pending=0;
			   $total_yarn_allow_qnty=$total_fab_avl_qnty=$total_yarn_issue_qnty=$total_fin_req_qnty=$total_gp_qnty=$total_grey_req_qnty=0;
            $month_array=array();
		 foreach( $po_wise_arr as $month_key=>$month_data)
         {
            foreach( $month_data as $po_key=>$row_order_level)
            {
                if ($ii%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$pub_shipment_date=date("Y-m-d",strtotime($row_order_level['pub_shipment_date']));//change_date_format($row_order_level[csf('pub_shipment_date')]);
				$extended_ship_date=date("Y-m-d",strtotime($row_order_level['extended_ship_date']));//change_date_format($row_order_level[csf('extended_ship_date')]);
				$template_id=$template_id_arr[$po_key];
               		//if($row_order_level['shiping_status']==2) $ex_factory_qnty=$sql_summary_ex_factory[$po_key]; else $ex_factory_qnty=0;
					$ex_factory_qnty=$row_order_level['shipout_qty'];
                $po_quantity=($row_order_level['po_quantity']*$row_order_level['total_set_qnty']);
				$plan_cut_qty=$row_order_level['plan_cut']*$row_order_level['total_set_qnty'];
				$month=date("Y-m",strtotime($month_key));
				$pending_po_value=(($po_quantity-$ex_factory_qnty)*$row_order_level['unit_price']);
			   $days_remian=datediff('d',date('d-m-Y',time()),$row_order_level['pub_shipment_date'])-1;
			    if(!in_array($month, $month_array))
                {
                    if ($k!=1)
                    {
                    ?>
                        <tr bgcolor="#CCCCCC">
                            <td colspan="9" align="right"><b>Monthly Total</b></td>
                            <td>&nbsp;</td>
                            <td align="right"><? echo  number_format($monthly_total_po_qnty,0);?></td>
                            <td align="right"><? echo  number_format($monthly_total_planCut_qnty,0);?></td>
                            <td>&nbsp;</td>
							<td>&nbsp;</td>
                            <td>&nbsp;</td>
                            
                            <td>&nbsp;</td>
                          
                            
                            <td align="right"><? echo  number_format($mon_total_yarn_allow_qnty,0);?></td>
                            <td align="right"><? echo  number_format($mon_total_yarn_issue_qnty,0);?></td>
                              
                            <td align="right"><? echo  number_format($monthly_total_yarn_bal,0);?></td>
                            
                            
                          <td align="right"><? echo  number_format($mon_total_fab_req_qnty,0);?></td>
                           <td align="right"><? echo  number_format($mon_total_gp_qnty,0);?></td>
                            
                            <td align="right"><? echo  number_format($monthly_total_knit_bal,0);?></td>
                            
                            <td align="right"><? echo  number_format($mon_total_fin_req_qnty,0);?></td>
                            <td align="right"><? echo  number_format($mon_total_gp_qnty,0);?></td>
                            
                            <td align="right"><? echo  number_format($monthly_total_fin_bal,0);?></td>
                            
                            <td align="right"><? echo number_format($monthly_total_cut_qnty,0); ?></td>
                            <td align="right"><? echo number_format($monthly_total_cut_pending,0); ?></td>
                            <td>&nbsp;</td>
                            <td align="right"><? echo number_format($monthly_total_sew_qnty,0); ?></td>
                            <td align="right"><? echo number_format($monthly_total_sew_pending,0); ?></td>
                            <td align="right"><? echo number_format($monthly_total_finish_qnty,0); ?></td>
                            <td align="right"><? echo number_format($monthly_total_finish_pending,0); ?></td>
                            <td align="right"><? echo number_format($monthly_total_ship_qnty,0); ?></td>
                            <td align="right"><? echo number_format($monthly_total_pending_po_qnty,0); ?></td>
                            <td align="right"><? echo number_format($monthly_pending_po_value,2); ?></td>
                            <td align="right"><? echo number_format($monthlyPendingSAH,2); ?></td>
							<td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    	<?
						  $mon_total_yarn_allow_qnty=0;
						  $mon_total_yarn_issue_qnty=0;
						  $mon_total_fin_req_qnty=0;
						  $mon_total_gp_qnty=0;
						  $mon_total_fab_avl_qnty=0;
                        $monthly_total_po_qnty = 0;$monthly_total_pending_po_qnty= 0;
						$monthly_total_planCut_qnty=0;
                        $monthly_total_cut_qnty = 0;
                        $monthly_total_cut_pending = 0;
                        $monthly_total_sew_qnty = 0;
                        $monthly_total_sew_pending = 0;
                        $monthly_total_finish_qnty = 0;
                        $monthly_total_finish_pending = 0;
                        $monthly_total_ship_qnty = 0;
						$monthly_total_yarn_bal = 0;
						$monthly_total_knit_bal = 0;
						$monthly_total_fin_bal = 0;
						$monthly_pending_po_value=0;
						$monthlyPendingSAH=0;
						
						
                      }
                    $k++;
                    ?>
                    <tr bgcolor="#EFEFEF">
                        <td colspan="42"><b><?php 
						//if($row_order_level['extended_ship_date']!='')  echo date("F",strtotime($row_order_level['extended_ship_date'])).", ".date("Y",strtotime($row_order_level['extended_ship_date']));
						//else echo date("F",strtotime($row_order_level['pub_shipment_date'])).", ".date("Y",strtotime($row_order_level['pub_shipment_date']));
						echo date("F",strtotime($month_key)).", ".date("Y",strtotime($month_key));
						
						?></b></td>
                    </tr>
                <?
                    $month_array[]=$month;
                }
				
				$garments_name_details="";
				$ex_gmts_item=explode(',',$row_order_level['gmts_item_id']);
				foreach($ex_gmts_item as $item_id)
				{
					if ($garments_name_details=="") $garments_name_details=$garments_item[$item_id]; else $garments_name_details.=', '.$garments_item[$item_id];
				}
				if($row_order_level['pending_po_quantity']>0)
				{
					$yarn_issue=$yarn_issue_arr[$po_key];
					$yarn_issue_ret=$yarn_issue_return_qty_arr[$po_key]['returned_qnty'];
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $ii;?>','<? echo $bgcolor;?>')" id="tr_<? echo $ii;?>">
                    <td style="word-break: break-all;word-wrap: break-word;" width="40"><? echo $ii; ?></td>
                    <td style="word-break: break-all;word-wrap: break-word;"  width="70" align="center"><? echo $row_order_level['job_no_prefix_num']; ?></td>
                    <td style="word-break: break-all;word-wrap: break-word;"  width="80"><p><? echo $buyer_short_name_arr[$row_order_level['buyer_name']]; ?></p></td>
                    <td style="word-break: break-all;word-wrap: break-word;"  width="80"><p><? echo $buyer_short_name_arr[$row_order_level['agent_name']]; ?></p></td>
                    
                    <td style="word-break: break-all;word-wrap: break-word;"  width="100"><?=$row_order_level['grouping']; ?></td>
                    
                    <td style="word-break: break-all;word-wrap: break-word;"  width="110"><p><a href="##" onClick="order_dtls_popup('<? echo $row_order_level['job_no_mst'];?>', '<? echo $row_order_level[csf('id')]; ?>','<? echo $template_id; ?>','<? echo $tna_process_type; ?>')"><? echo $row_order_level['po_number']; ?></a></p></td>
                    <td style="word-break: break-all;word-wrap: break-word;"  width="50" onclick="openmypage_image('requires/capacity_and_order_booking_status_controller.php?action=show_image&job_no=<? echo $row_order_level['job_no_mst']; ?>','Image View')"><img src='../../../<? echo $imge_arr[$row_order_level['job_no_mst']]; ?>' height='25' width='30' /></td>
                    <td style="word-break: break-all;word-wrap: break-word;"  width="120"><p><? echo $row_order_level['style_ref_no']; ?></p></td>
                    <td style="word-break: break-all;word-wrap: break-word;"  width="140"><p> <? echo $garments_name_details;?></p></td>
                    <td style="word-break: break-all;word-wrap: break-word;"  width="60" align="right"><? echo $row_order_level['set_smv']; ?></td>
                    <td style="word-break: break-all;word-wrap: break-word;"  align="right" width="100"><? echo number_format($po_quantity,0); ?></td>
                    <td style="word-break: break-all;word-wrap: break-word;"  align="right" width="100"><? echo number_format($plan_cut_qty,0); ?></td>
                    <td style="word-break: break-all;word-wrap: break-word;"  width="90" align="center"><? echo change_date_format($row_order_level['pub_shipment_date'],'dd-mm-yyyy','-'); ?></td>
					<td style="word-break: break-all;word-wrap: break-word;"  width="80" align="center"><? echo change_date_format($row_order_level['extended_ship_date'],'dd-mm-yyyy','-'); ?></td>
                    <td style="word-break: break-all;word-wrap: break-word;"  width="60" align="center" bgcolor="<? //echo $color; ?>" ><? echo $days_remian; ?> </td>
                    
                    <td style="word-break: break-all;word-wrap: break-word;"  width="90"><p><? $bookingType=rtrim($bookingDataArr[$po_key],',');$bookingTypeArr=implode(",",array_unique(explode(",",$bookingType)));
					echo $bookingTypeArr;  ?></p></td>
                    
                     <td style="word-break: break-all;word-wrap: break-word;"  width="90" align="right" bgcolor="<? //echo $color; ?>" ><? echo number_format($yarn_allo_arr[$po_key],0); ?> </td>
                     <td style="word-break: break-all;word-wrap: break-word;" title="Issue=<? echo $yarn_issued[$po_key].',Ret='.$yarn_issue_ret;?>"  width="90" align="right" bgcolor="<? //echo $color; ?>" ><? echo number_format($yarn_issued[$po_key]-$yarn_issue_return_qty_arr[$po_key]['returned_qnty'],0); ?> </td>
                      
                    <td style="word-break: break-all;word-wrap: break-word;"  width="90" align="right">
						<?  //$grey_fab_req_qnty[$row[csf('po_break_down_id')]]
			//$fabrics_avl_qnty_array[$vals[csf("po_breakdown_id")]]
		//	$gp_qty_array[$row[csf('po_breakdown_id')]]
		
                           // $yarn_bal=$tot_req_qnty[$row_order_level[csf('id')]]-$yarn_issued[$row_order_level[csf('id')]];
						  // echo $yarn_bal=number_format($tot_req_qnty[$po_key]-($yarn_issue-$yarn_issue_ret),2); 
						   echo $yarn_bal=number_format($grey_fab_req_qnty[$po_key]-($yarn_issued[$po_key]-$yarn_issue_return_qty_arr[$po_key]['returned_qnty']),2);
                        ?> 
                    </td>
                      <td style="word-break: break-all;word-wrap: break-word;" title="GreyReq=<? echo $grey_fab_req_qnty[$po_key];?>"  width="90" align="right" bgcolor="<? //echo $color; ?>" ><? echo number_format($grey_fab_req_qnty[$po_key],0); ?> </td>
                     <td style="word-break: break-all;word-wrap: break-word;"  width="90" align="right" bgcolor="<? //echo $color; ?>" ><? echo number_format($gp_qty_array[$po_key],0); ?> </td>
                     
                    <td style="word-break: break-all;word-wrap: break-word;"  width="90" align="right"><?  
					echo $knit_bal=number_format($tot_req_qnty[$po_key]-$grey_receive_qnty_arr[$po_key],2); 
					?> </td>
                      <td style="word-break: break-all;word-wrap: break-word;"  width="90" align="right" bgcolor="<? //echo $color; ?>" ><? echo number_format($grey_fin_fab_req_qnty[$po_key],0); ?> </td>
                     <td style="word-break: break-all;word-wrap: break-word;"  width="90" align="right" bgcolor="<? //echo $color; ?>" ><? echo number_format($fabrics_avl_qnty_array[$po_key],0); ?> </td>
                     
                    <td style="word-break: break-all;word-wrap: break-word;"  width="90" align="right"><?  
							//$finish_bal = $tot_fin_req_qnty[$row_order_level[csf('id')]]-(($fin_purchase[$row_order_level[csf('id')]]-$fin_rec_ret[$row_order_level[csf('id')]])+$net_tensfer[$row_order_level[csf('id')]]+$finish_receive_qnty_arr[$row_order_level[csf('id')]]);			
							echo $finish_bal =number_format($tot_fin_req_qnty[$po_key]-(($fin_purchase[$po_key]-$fin_rec_ret[$po_key])+$net_tensfer[$po_key]+$finish_receive_qnty_arr[$po_key]),2);
					
					?> </td>
                    
                    <td style="word-break: break-all;word-wrap: break-word;"  align="right" width="90"><? echo number_format($prod_qty_arr[$po_key]['cutting_qnty'],0);  ?></td>
                    <td style="word-break: break-all;word-wrap: break-word;"  align="right" width="90"><? $cut_pending=$plan_cut_qty-$prod_qty_arr[$po_key]['cutting_qnty']; if($cut_pending>0) echo number_format($cut_pending,0); else echo 0;  ?></td>
                    <?
					foreach($excess_percent_arr[$row_order_level['company_name']] as $key=>$value)
					{
						//echo $key."<br>";
						$start_limit=$excess_percent_arr[$row_order_level['company_name']][$key]['start'];
						$end_limit=$excess_percent_arr[$row_order_level['company_name']][$key]['end'];
						
						if($po_quantity>=$start_limit && $po_quantity<=$end_limit)
						{
							$excess_percent=$excess_percent_arr[$row_order_level['company_name']][$key]['percent'];
							//$subtotal+=$excess_percent;
							break;
						}
						else $excess_percent=0;
					}
					//echo $excess_percent;
                    //list($row11)=$standard_excess_cut;
                   // $excess_percent= $row11[('excess_percent')];
				  
                    $actual_excess_cut=0; $actual_excess_cut2=0;
                    $actual_excess_cut=(($prod_qty_arr[$po_key]['cutting_qnty']-$po_quantity)/$po_quantity)*100;
                    $exceed_cut=$actual_excess_cut - $excess_percent;
					
                    if($actual_excess_cut>$excess_percent) $bg_color="red"; else $bg_color="green";	

                   // $actual_excess_cut=round($actual_excess_cut,2);
                    $actual_excess_cut2=$prod_qty_arr[$po_key]['cutting_qnty']-$po_quantity;
                    if($actual_excess_cut2==0 || $actual_excess_cut2<0)
                    {
                    ?>
                        <td style="word-break: break-all;word-wrap: break-word;"  align="left" width="80" title="Cutting Qnty Not Exceed Order Qnty" bgcolor="<? echo $bg_color ?>"><? echo "N/A"; ?></td>
                    <?
                    }
                    else
                    {
                    ?>
                        <td style="word-break: break-all;word-wrap: break-word;"  align="right" width="80" bgcolor="<? echo $bg_color ?>"><? echo number_format($actual_excess_cut,2)." %"; ?></td>
                    <?
                    } 
                    ?> 
                    <td style="word-break: break-all;word-wrap: break-word;"  align="right" width="90"><? echo $sewing_tot= number_format($prod_qty_arr[$po_key]['sewing_out_qnty'],0); ?></td>
                    <td style="word-break: break-all;word-wrap: break-word;"  align="right" width="90"><?  $sewing_pending=$po_quantity-$prod_qty_arr[$po_key]['sewing_out_qnty']; if ($sewing_pending>0) echo  number_format($sewing_pending,0); else echo 0; ?></td>
                    <td style="word-break: break-all;word-wrap: break-word;"  align="right" width="90"><? echo  $finish_tot= number_format($prod_qty_arr[$po_key]['finish_qnty'],0); ?></td>
                    <td style="word-break: break-all;word-wrap: break-word;"  align="right" width="90"><? $finish_pending=$po_quantity-$prod_qty_arr[$po_key]['finish_qnty']; if($finish_pending>0) echo number_format($finish_pending,0); else echo 0; ?></td>
                    <td style="word-break: break-all;word-wrap: break-word;"  align="right" width="90"><? echo number_format($ex_factory_qnty,0); ?></td>
                    <td style="word-break: break-all;word-wrap: break-word;"  align="right" width="100"><? $pending_po_quantity=$row_order_level['pending_po_quantity'];
					echo number_format($pending_po_quantity,0);?> </td>
                    <td style="word-break: break-all;word-wrap: break-word;"  align="right" width="100"><? echo $pending_po_tot= number_format($pending_po_value,0);?> </td>
                    <td style="word-break: break-all;word-wrap: break-word;"  align="right" width="90"> &nbsp;<? echo number_format((($po_quantity - $ex_factory_qnty)*$row_order_level['set_smv'])/60,2); ?></td>
					 <td align="center" width="140"><p><? 
					$team_leader_contact=$company_team_leader_contact_arr[$row_order_level['team_leader']];
					echo $team_leader_arr[$row_order_level['team_leader']].','.$team_leader_contact;;?></p></td>
                    <td align="center" width="140"><p><?  
					$team_member_contact_no=$company_team_member_contact_arr[$row_order_level['dealing_marchant']];
					echo $company_team_member_name_arr[$row_order_level['dealing_marchant']].','.$team_member_contact_no; ?></p></td>
					
                  <td width="100" align="center"><p><? echo $extend_shipment_mode[$row_order_level[('extend_ship_mode')]];?></p></td>
					<td width="100" align="center" title="PO Value(<? echo $row_order_level[('po_total_price')];?>),Sea Discount(<? echo $row_order_level[('sea_discount')];?>)"><p><? $sea_discount= ($row_order_level[('po_total_price')]*( $row_order_level[('sea_discount')]/100));echo number_format($sea_discount,2); ?></p></td>
					<td width="100" align="center" title="PO Value(<? echo $row_order_level[('po_total_price')];?>),Air Discount(<? echo $row_order_level[('air_discount')];?>)"><p><? $air_discount= ($row_order_level[('po_total_price')]*( $row_order_level[('air_discount')]/100));echo number_format($air_discount,2);?></p></td>
                    <td  style="word-break: break-all;word-wrap: break-word;"  >
						<div style="word-wrap:break-word; width:110px"><? echo $row_order_level['details_remarks']; ?>
					</div></td>
               </tr>
			<?
                $monthly_total_po_qnty+=$po_quantity;
				$monthly_total_pending_po_qnty+=$pending_po_quantity;
				$monthly_total_planCut_qnty+=$plan_cut_qty;
                $monthly_total_cut_qnty+= $prod_qty_arr[$po_key]['cutting_qnty'];
				
				
				 $mon_total_yarn_allow_qnty+=$yarn_allo_arr[$po_key];
				 $mon_total_yarn_issue_qnty+=$yarn_issued[$po_key]-$yarn_issue_return_qty_arr[$po_key]['returned_qnty'];
				 $mon_total_fin_req_qnty+=$grey_fin_fab_req_qnty[$po_key]; 
				
				 $mon_total_gp_qnty+=$gp_qty_array[$po_key]; 
				 $mon_total_fab_req_qnty+=$grey_fab_req_qnty[$po_key];
				 $mon_total_fab_avl_qnty+=$fabrics_avl_qnty_array[$po_key];
				 
				 
				 
                if($cut_pending>0)
                {
                	$monthly_total_cut_pending+=$plan_cut_qty-$prod_qty_arr[$po_key]['cutting_qnty'];
                	$total_cut_pending+= $cut_pending;
                }
                if($sewing_pending>0)
                {
                	$monthly_total_sew_pending+= $sewing_pending;
                	$total_sew_pending+= $sewing_pending;

                }
                if($finish_pending>0)
                {
                	$monthly_total_finish_pending+= $finish_pending;
                	$total_finish_pending+= $finish_pending;
                }
                
                $monthly_total_sew_qnty+=$prod_qty_arr[$po_key]['sewing_out_qnty'];
                $monthly_total_finish_qnty+=$prod_qty_arr[$po_key]['finish_qnty'];
                $monthly_total_ship_qnty+=$ex_factory_qnty;
				
				$monthly_total_yarn_bal += str_replace(",", "", $yarn_bal);
				$monthly_total_knit_bal += str_replace(",", "", $knit_bal);
				$monthly_total_fin_bal +=str_replace(",", "", $finish_bal) ;
				$monthly_pending_po_value	+=str_replace(",", "", $pending_po_tot);				
				$monthlyPendingSAH+=(($po_quantity - $ex_factory_qnty)*$row_order_level['set_smv'])/60;
				
                
                $total_po_qnty+=$po_quantity;
				$total_pending_po_qnty+=$pending_po_quantity;
				$total_planCut_qty+=$plan_cut_qty;
                $total_cut_qnty+= $prod_qty_arr[$po_key]['cutting_qnty'];
				
				 $total_yarn_allow_qnty+= $yarn_allo_arr[$po_key];
				 $total_yarn_issue_qnty+= $yarn_issued[$po_key]-$yarn_issue_return_qty_arr[$po_key]['returned_qnty'];
				 $total_fin_req_qnty+= $grey_fin_fab_req_qnty[$po_key]; 
				 $total_gp_qnty+= $gp_qty_array[$po_key]; 
				 $total_grey_req_qnty+= $grey_fab_req_qnty[$po_key];
				 $total_fab_avl_qnty+= $fabrics_avl_qnty_array[$po_key];
					
            
				
				
				$total_sew_qnty+= str_replace(",", "", $sewing_tot);
                //$total_sew_qnty+= $cutting_qnty[$row_order_level[csf('id')]];	
                $total_finish_qnty+=str_replace(",", "", $finish_tot);
                $total_ship_qnty+=$ex_factory_qnty;
				
				$total_yarn_bal += str_replace(",", "", $yarn_bal);
				$total_knit_bal += str_replace(",", "", $knit_bal);
				$total_fin_bal += str_replace(",", "", $finish_bal);
				
				/*$total_yarn_bal += $monthly_total_yarn_bal;
				$total_knit_bal += $monthly_total_knit_bal;
				$total_fin_bal += $monthly_total_fin_bal;*/
				$total_pending_po_value	+=str_replace(",", "", $pending_po_tot);
				$total_PendingSAH +=(($po_quantity - $ex_factory_qnty)*$row_order_level['set_smv'])/60;
                
                $ii++;
            }
				}
            }
			if($row_tot>0)
			{
			?>
				<tr bgcolor="#CCCCCC">
					<td colspan="9" align="right"><b>Monthly Total</b></td>
					<td align="right">&nbsp;</td>
					<td align="right"><? echo  number_format($monthly_total_po_qnty,0);?></td>
                    <td align="right"><? echo  number_format($monthly_total_planCut_qnty,0);?></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
                    
                    
                     <td align="right"><? echo  number_format($mon_total_yarn_allow_qnty,0);?></td>

                      <td align="right"><? echo  number_format($mon_total_yarn_issue_qnty,0);?></td>
                    
                    <td align="right"><? echo number_format($monthly_total_yarn_bal,2);?></td>
                    
                     <td align="right"><? echo  number_format($mon_total_fab_req_qnty,0);?></td>
                     <td align="right"><? echo  number_format($mon_total_gp_qnty,0);?></td>
                    
                    <td align="right"><? echo number_format($monthly_total_knit_bal,2);?></td>
                    
                   <td align="right"><? echo  number_format($mon_total_fin_req_qnty,0);?></td>
                   <td align="right"> <? echo  number_format($mon_total_gp_qnty,0);?></td>
                    
                    <td align="right"><? echo number_format($monthly_total_fin_bal,2);?></td>
                    
					<td align="right"><? echo number_format($monthly_total_cut_qnty,0); ?></td>
					<td align="right"><? echo number_format($monthly_total_cut_pending,0); ?></td>
					<td>&nbsp;</td>
					<td align="right"><? echo number_format($monthly_total_sew_qnty,0); ?></td>
					<td align="right"><? echo number_format($monthly_total_sew_pending,0); ?></td>
					<td align="right"><? echo number_format($monthly_total_finish_qnty,0); ?></td>
					<td align="right"><? echo number_format($monthly_total_finish_pending,0); ?></td>
					<td align="right"><? echo number_format($monthly_total_ship_qnty,0); ?></td>
					<td align="right"><? echo number_format($monthly_total_po_qnty - $monthly_total_ship_qnty,0); ?></td>
                    <td align="right"><? echo number_format($monthly_pending_po_value,2); ?></td>
					<td align="right"><? echo number_format($monthlyPendingSAH,2); ?></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
			<?	
			}
			?>
            </table>
            </div>
            <table cellspacing="0" cellpadding="0"  width="3940"  border="1" rules="all" class="tbl_bottom">
             <tr>
             	<td style="word-break: break-all;word-wrap: break-word;" width="40">&nbsp;</td>
                <td  style="word-break: break-all;word-wrap: break-word;" width="70">&nbsp;</td>
                <td style="word-break: break-all;word-wrap: break-word;"  width="80">&nbsp;</td>
                <td style="word-break: break-all;word-wrap: break-word;"  width="80">&nbsp;</td>
                <td  style="word-break: break-all;word-wrap: break-word;" width="100">&nbsp;</td>
                <td  style="word-break: break-all;word-wrap: break-word;" width="110">&nbsp;</td>
                <td style="word-break: break-all;word-wrap: break-word;"  width="50">&nbsp;</td>
                <td  style="word-break: break-all;word-wrap: break-word;" width="120">&nbsp;</td>
                <td  style="word-break: break-all;word-wrap: break-word;" width="140" align="right">Grand Total</td>
                <td  style="word-break: break-all;word-wrap: break-word;" width="60"></td>
                <td style="word-break: break-all;word-wrap: break-word;"  width="100" align="right"><? echo  number_format($total_po_qnty,0);?></td>
                <td style="word-break: break-all;word-wrap: break-word;"  width="100" align="right"><? echo  number_format($total_planCut_qty,0);?></td>
                <td style="word-break: break-all;word-wrap: break-word;"  width="90">&nbsp;</td>
				<td style="word-break: break-all;word-wrap: break-word;"  width="80">&nbsp;</td>
                <td  style="word-break: break-all;word-wrap: break-word;" width="60">&nbsp;</td>
                
                <td style="word-break: break-all;word-wrap: break-word;"  width="90">&nbsp;</td>
                
                <td style="word-break: break-all;word-wrap: break-word;"  width="90"><? echo  number_format($total_yarn_allow_qnty,0);?></td>
                <td style="word-break: break-all;word-wrap: break-word;" title="Yarn issue"  width="90"><? echo  number_format($total_yarn_issue_qnty,0);?></td>
                
                <td  style="word-break: break-all;word-wrap: break-word;" width="90" align="right"><? echo  number_format($total_yarn_bal,2);?></td>
                 <td style="word-break: break-all;word-wrap: break-word;"  width="90"><? echo  number_format($total_grey_req_qnty,2);?></td>
                  <td style="word-break: break-all;word-wrap: break-word;"  width="90"><? echo  number_format($total_gp_qnty,2);?></td>
                  
                <td style="word-break: break-all;word-wrap: break-word;"  width="90" align="right"><? echo  number_format($total_knit_bal,2);?></td>
                  <td style="word-break: break-all;word-wrap: break-word;"  width="90"><? echo  number_format($total_fin_req_qnty,2);?></td>
                  <td style="word-break: break-all;word-wrap: break-word;"  width="90"><? echo  number_format($total_fab_avl_qnty,2);?></td>
                  
                
                <td style="word-break: break-all;word-wrap: break-word;"  width="90" align="right"><? echo  number_format($total_fin_bal,2);?></td>
                
                <td style="word-break: break-all;word-wrap: break-word;"  width="90" align="right"><? echo number_format($total_cut_qnty,0); ?></td>
                <td style="word-break: break-all;word-wrap: break-word;"  width="90" align="right"><? echo number_format($total_cut_pending,0); ?></td>
                <td style="word-break: break-all;word-wrap: break-word;"  width="80">&nbsp;</td>
                <td style="word-break: break-all;word-wrap: break-word;"  width="90" align="right"><? echo number_format($total_sew_qnty,0); ?></td>
                <td style="word-break: break-all;word-wrap: break-word;"  width="90" align="right"><? echo number_format($total_sew_pending,0); ?></td>
                <td style="word-break: break-all;word-wrap: break-word;"  width="90" align="right"><? echo number_format($total_finish_qnty,0); ?></td>
                <td style="word-break: break-all;word-wrap: break-word;"  width="90" align="right"><? echo number_format($total_finish_pending,0); ?></td>
                
                <td style="word-break: break-all;word-wrap: break-word;"  width="90" align="right"><? echo number_format($total_ship_qnty,0); ?></td>
                <td style="word-break: break-all;word-wrap: break-word;"  width="100" align="right"><? echo number_format($total_pending_po_qnty,0); ?></td>
                <td style="word-break: break-all;word-wrap: break-word;"  width="100" align="right"><? echo number_format($total_pending_po_value,2); ?></td>
                <td style="word-break: break-all;word-wrap: break-word;"  width="90" align="right"><? echo number_format($total_PendingSAH ,2); ?></td>
                <td style="word-break: break-all;word-wrap: break-word;"  width="140">&nbsp;</td>
                <td style="word-break: break-all;word-wrap: break-word;"  width="140">&nbsp;</td>
                <td style="word-break: break-all;word-wrap: break-word;" width="100">&nbsp;</td>
				<td style="word-break: break-all;word-wrap: break-word;" width="100">&nbsp;</td>
				<td style="word-break: break-all;word-wrap: break-word;" width="100">&nbsp;</td>
				<td>&nbsp;</td>
            </tr>
        </table> 
    </fieldset> 
</div>   
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
    echo "$html####$filename"; 
    exit();
	/*$html = ob_get_contents();
	ob_clean();
	 
	foreach (glob($_SESSION['logic_erp']['user_id']."*.xls") as $filename) {
	@unlink($filename);
	}
	$name=time();
	$filename=$_SESSION['logic_erp']['user_id']."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,$html);
	echo "$html****$filename";*/
		
}
?>