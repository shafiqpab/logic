<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
require_once('../../../includes/class3/class.conditions.php');
require_once('../../../includes/class3/class.reports.php');
require_once('../../../includes/class3/class.yarns.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$date=date('Y-m-d');
//--------------------------------------------------------------------------------------------------------------------

//$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
//$user_name_library=return_library_array( "select id, user_name from  user_passwd", "id", "user_name"  );
//$team_name_library=return_library_array( "select id,team_name from lib_marketing_team", "id", "team_name"  );
//$team_member_name_library=return_library_array( "select id,team_member_name from lib_mkt_team_member_info", "id", "team_member_name"  );

if($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );   	 
	exit();
}
	
$team_member_arr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info",'id','team_member_name');
$lib_yarn_count_arr=return_library_array( "select id,yarn_count from lib_yarn_count", "id", "yarn_count"  );
$lib_season_name_arr=return_library_array( "select id,season_name from lib_buyer_season", "id", "season_name"  );
$company_library=return_library_array( "select id, company_short_name from lib_company", "id", "company_short_name"  );
	
if ($action=="load_drop_down_team_member")
{
if($data!=0)
	{
        echo create_drop_down( "cbo_team_member", 150, "select id,team_member_name 	 from lib_mkt_team_member_info  where team_id='$data' and status_active=1 and is_deleted=0 order by team_member_name","id,team_member_name", 1, "-Select Team Member-", $selected, "" ); 
	}
 else
   {
		 echo create_drop_down( "cbo_team_member", 150, $blank_array,"", 1, "-Select Team Member- ", $selected, "" );
   }
}
$tmplte=explode("**",$data);
if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;


if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company_name=str_replace("'","",$cbo_company_name);
	$cbo_search_type=str_replace("'","",$cbo_search_type);
	$txt_yarn_count=str_replace("'","",$txt_yarn_count);
	$cbo_bh_mer_name=str_replace("'","",$cbo_bh_mer_name);
	if($txt_yarn_count!='') $yarn_count_con="and yarn_count Like '%$txt_yarn_count%' ";else $yarn_count_con='';
	
//	echo $yarn_count_con;die;
	//$serch_by=str_replace("'","",$cbo_search_by);
	$buyer_id_cond="";
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{
				$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else
			{
				$buyer_id_cond="";
			}
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}
	

	
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_job_no=trim($txt_job_no);
	if($txt_job_no !="" || $txt_job_no !=0)
	{
		//$year = substr(str_replace("'","",$cbo_year_selection), -2); 
		//$job_no=$company_library[$company_name]."-".$year."-".str_pad($txt_job_no, 5, 0, STR_PAD_LEFT);
		$jobcond="and a.job_no_prefix_num in(".$txt_job_no.")";
	}
	else
	{
		$jobcond="";	
	}
	
	
	$date_cond='';
	/*if(str_replace("'","",$cbo_category_by)==1)
	{*/
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			$start_date=(str_replace("'","",$txt_date_from));
			$end_date=(str_replace("'","",$txt_date_to));
			
			$date_cond="and b.pub_shipment_date between '$start_date' and '$end_date'";
		}
	//}
	/*if(str_replace("'","",$cbo_category_by)==2)
	{
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			$start_date=(str_replace("'","",$txt_date_from));
			$end_date=(str_replace("'","",$txt_date_to));
			$date_cond="and b.po_received_date between '$start_date' and '$end_date'";
		}
	}*/
	//if (str_replace("'","",$txt_job_no)=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in (".str_replace("'","",$txt_job_no).") ";
	
	if(str_replace("'","",$txt_style_ref)!="") $style_ref_cond=" and a.style_ref_no like '%".str_replace("'","",$txt_style_ref)."%'"; else $style_ref_cond="";
	if(str_replace("'","",$txt_order_no)!="")
	{
		$ordercond=" and b.po_number like '%".str_replace("'","",$txt_order_no)."%'"; 
	}
	else
	{
		$ordercond="";
	}
	
	$team_cond="";
	if(str_replace("'","",$cbo_team_name)!=0) $team_cond=" and a.team_leader=".str_replace("'","",$cbo_team_name)."  ";
	if(str_replace("'","",$cbo_team_member)!=0) $team_cond.=" and a.dealing_marchant=".str_replace("'","",$cbo_team_member)."  ";
	//echo $cbo_bh_mer_name;die;
	$cbo_bh_mer_name=str_replace("0","",$cbo_bh_mer_name);
	if($cbo_bh_mer_name!=0 || $cbo_bh_mer_name!='') $bh_mer_name_con="and a.bh_merchant='$cbo_bh_mer_name' ";else $bh_mer_name_con='';
	//echo $bh_mer_name_con;die;
	 $cbo_year_selection=str_replace("'","",$cbo_year_selection);
	
	$start_ddd= strtotime(change_date_format($start_date,"dd-mm-yyyy","-"));
	$start_ddd=date("Y-m-d",$start_ddd);
	$start_end_date= strtotime(change_date_format($end_date,"dd-mm-yyyy","-"));
	$start_end_date=date("Y-m-d",$start_end_date);
	 //$dd= date('d-m-Y',strtotime($start_date));
	//$start_date_week= date($dd, time() - 1296000);//$start_date;
	//echo $last_day_this_month  = date($start_date_week, time() -1814400); //date('t-m-Y');
	
	$dateee = strtotime($start_ddd);
	$start_end_date = strtotime($start_end_date);
	
	$date_start = date("Y-m-d",strtotime("-28 day", $dateee));
	$date_end = date("Y-m-d",strtotime("+28 day", $start_end_date));
	//echo $date_end;
	//$end_date_week=  addday("$end_date", time() -1814400);//$start_date;//$start_date;
	if($db_type==0)
	{
		$date_start= change_date_format($date_start,"yyyy-mm-dd");
		$date_end= change_date_format($date_end,"yyyy-mm-dd");
	}
	else
	{
		$date_start= change_date_format($date_start,"dd-mm-yyyy","-",1);
		$date_end= change_date_format($date_end,"dd-mm-yyyy","-",1);
	}
	
	
	//echo "select week_date,week from week_of_year where week_date between '$date_start' and  '$date_end' order by week_date asc";die;
	// date("d-m-Y", time() - 1296000);
	$week_for_arr=array();$no_of_week_for_header=array();
	$sql_week_header=sql_select("select week_date,week from week_of_year where week_date between '$date_start' and  '$date_end' order by week_date asc");
	$week_check_head=array();
	foreach ($sql_week_header as $row_week_header)
	{
		if($week_check_head[$row_week_header[csf("week")]]=='')
		{
			$week_counter_header[date("Y-M",strtotime($row_week_header[csf("week_date")]))][$row_week_header[csf("week")]]=$row_week_header[csf("week")];
			$week_check_head[$row_week_header[csf("week")]]=$week_counter_header[date("Y-M",strtotime($row_week_header[csf("week_date")]))][$row_week_header[csf("week")]];
		}
		$tmp=add_date($row_week_header[csf("week_date")],-1);
		if($db_type==0) $tmp_cond=date("d-m-y",strtotime($tmp));
		else $tmp_cond=date("d-M-y",strtotime($tmp));
					
		$no_of_week_for_header_calc[$tmp_cond]=$row_week_header[csf("week")];	
		$no_of_week_for_header[$row_week_header[csf("week_date")]]=$row_week_header[csf("week")];
		$test[$row_week_header[csf("week")]]=$row_week_header[csf("week")];
	}
	
	unset($sql_week_header);
	$week_start_day=array();
	$week_end_day=array();$week_day_arr=array();
	//week_day
	//where week_date between '$date_start' and  '$date_end'
	//$date_start2=add_date($date_start,-1);
	//if($db_type==0) $date_start=date("d-m-Y",strtotime($date_start2));
	//else $date_start=date("d-M-Y",strtotime($date_start2));
		
	$sql_week_start_end_date=sql_select("select week,min(week_date) as week_start_day, Max(week_date) as week_end_day from week_of_year where week_date between '$date_start' and  '$date_end' group by week");
	foreach ($sql_week_start_end_date as $row_week)
	{
		$week_start_day[$row_week[csf("week")]][week_start_day]=$row_week[csf("week_start_day")];
		$week_end_day[$row_week[csf("week")]][week_end_day]=$row_week[csf("week_end_day")];
		//$week_day_arr[$row_week[csf("week")]][$row_week[csf("week_start_day")]][week_first_day]=$row_week[csf("min_week_day")];
		$week_day_arr[$row_week[csf("week_end_day")]]['week_last_day']=$row_week[csf("last_week_day")];
	}
	unset($sql_week_start_end_date);
	//print_r($week_day_arr);
	function distribute_projection( $base_week, $qnty)
	{
		//5. Distribution ratios are: Base week 52%, Immediate before 24%, Week before immediate week 20%, Immediate week after base week 2%, next 2 weeks as 1%
		global $test;
		 
		$tmpbase_week=$test[$base_week-3];
		$k=0;
		for($i=0; $i<6; $i++)
		{
			$tmpbase_week++;
			if( $test[$tmpbase_week]=='') { $tmpbase_week=1; $k=0; }
			
			
			if($i==0) $distr_arr[$tmpbase_week]=($qnty*20)/100;
			if($i==1) $distr_arr[$tmpbase_week]=($qnty*24)/100;
			if($i==2) $distr_arr[$tmpbase_week]=($qnty*52)/100;
			if($i==3) $distr_arr[$tmpbase_week]=($qnty*2)/100;
			if($i==4) $distr_arr[$tmpbase_week]=($qnty*1)/100;
			if($i==5) $distr_arr[$tmpbase_week]=($qnty*1)/100;
			$k++;
			
		}
		 
		return $distr_arr;
		/*5000
		b=52
		b-1=21
		b-2=15
		b+1=1
		b+2=1*/
	}
//echo "<per>";
 //print_r(distribute_projection("2016-Dec",51, 5000)); die;
	$count_data=sql_select("select yarn_count,id from  lib_yarn_count  where status_active=1 and is_deleted=0 and yarn_count is not null $yarn_count_con");
	$yarn_id='';
	foreach($count_data as $row)
	{
			if($yarn_id=='') $yarn_id=$row[csf('id')];else $yarn_id.=",".$row[csf('id')];
	}
		$yarn_ids=count(array_unique(explode(",",$yarn_id)));
		$yarn_numIds=chop($yarn_id,','); $yarnIds_cond="";
		if($yarn_id!='' || $yarn_id!=0)
		{
			if($db_type==2 && $yarn_ids>1000)
			{
				$yarnIds_cond=" and (";
				$yIdsArr=array_chunk(explode(",",$yarn_numIds),990);
				foreach($yIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$yarnIds_cond.=" d.count_id  in($ids) or ";
				}
				$yarnIds_cond=chop($yarnIds_cond,'or ');
				$yarnIds_cond.=")";
			}
			else
			{
				$yarnIds_cond=" and  d.count_id  in($yarn_id)";
			}
		}
		
	  $yarn_sql_data=("select b.id as po_id,d.count_id,d.copm_one_id,d.copm_two_id,d.percent_one,d.percent_two,sum(d.cons_qnty) as cons_qnty,e.construction,e.gsm_weight from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fab_yarn_cost_dtls d,wo_pre_cost_fabric_cost_dtls e where a.job_no=b.job_no_mst  and c.job_no_mst=a.job_no and b.id=c.po_break_down_id and d.job_no=a.job_no and 
	e.id=d.fabric_cost_dtls_id and  c.item_number_id= e.item_number_id and e.job_no=a.job_no and e.body_part_id in(1,20) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1   and e.body_part_id in(1,20) and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $team_cond $yarnIds_cond  group by   b.id ,d.count_id,d.copm_one_id,d.copm_two_id,d.percent_one,d.percent_two,e.construction,e.gsm_weight"); 
	$sql_result_y=sql_select($yarn_sql_data);
	$wo_fab_data_arr=array();
	$all_yarn_po_id="";
	foreach($sql_result_y as $row)
		{
			if($all_yarn_po_id=="") $all_yarn_po_id=$row[csf("po_id")]; else $all_yarn_po_id.=",".$row[csf("po_id")];
			
			$wo_fab_data_arr[$row[csf('po_id')]]['percent_one']=$row[csf('percent_one')];
			$wo_fab_data_arr[$row[csf('po_id')]]['percent_two']=$row[csf('percent_two')];
			$wo_fab_data_arr[$row[csf('po_id')]]['copm_two_id']=$row[csf('copm_two_id')];
			$wo_fab_data_arr[$row[csf('po_id')]]['copm_one_id']=$row[csf('copm_one_id')];
			$wo_fab_data_arr[$row[csf('po_id')]]['construction'].=$row[csf('construction')].',';
			$wo_fab_data_arr[$row[csf('po_id')]]['gsm_weight'].=$row[csf('gsm_weight')].',';
			$wo_fab_count_data_arr[$row[csf('po_id')]]['count_id'].=$row[csf('count_id')].",";
			$wo_yarn_data_arr[$row[csf('po_id')]]['cons_qnty']+=$row[csf('cons_qnty')];
		}
		unset($sql_result_y);
		$yarn_po_ids=count(array_unique(explode(",",$all_yarn_po_id)));
		$yarnPo_numIds=chop($all_yarn_po_id,','); $yarnPoIds_cond="";
		if($txt_yarn_count!='')
		{
			if($all_yarn_po_id!='' || $all_yarn_po_id!=0)
			{
				if($db_type==2 && $yarn_po_ids>1000)
				{
					$yarnPoIds_cond=" and (";
					$yPoIdsArr=array_chunk(explode(",",$yarnPo_numIds),990);
					foreach($yPoIdsArr as $ids)
					{
						$ids=implode(",",$ids);
						$yarnPoIds_cond.=" b.id  in($ids) or ";
					}
					$yarnPoIds_cond=chop($yarnPoIds_cond,'or ');
					$yarnPoIds_cond.=")";
				}
				else
				{
					$yarnPoIds_cond=" and  b.id  in($all_yarn_po_id)";
				}
			}
		}

	//echo $yarn_id;die;
	$exfactory_data_array=array();
	$exfactory_data=sql_select("select po_break_down_id,
	sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
	sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as return_qnty,
	MAX(ex_factory_date) as ex_factory_date from pro_ex_factory_mst  where status_active=1 and is_deleted=0 group by po_break_down_id");
	foreach($exfactory_data as $exfatory_row)
	{
			$exfactory_data_array[$exfatory_row[csf('po_break_down_id')]][ex_factory_qnty]=$exfatory_row[csf('ex_factory_qnty')]-$exfatory_row[csf('return_qnty')];
	}
	unset($exfactory_data);
	if($template==1)
	{
	$wo_color_data_arr=array();
	$week_wise_order_qty=array();
	$po_id_arr=array();
	$week_wise_order_qty_arr=array();	
	//if($db_type==2) $pre_group="LISTAGG(CAST(d.fabric_cost_dtls_id AS VARCHAR2(4000)),',') WITHIN GROUP ( ORDER BY b.id) as fabric_cost_dtls_id";
	//else if($db_type==0) $pre_group="group_concat(distinct d.fabric_cost_dtls_id) as fabric_cost_dtls_id";
	$wo_color_data_arr2=array();
	
	 $sql_data_c=("select b.id as po_id,b.is_confirmed,a.bh_merchant,c.order_quantity as order_quantity,c.country_ship_date as  conf_ship_date from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
				where a.job_no=b.job_no_mst and  c.po_break_down_id=b.id and  c.job_no_mst=b.job_no_mst   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.status_active=1 and c.is_deleted=0   and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $team_cond ");
	$sql_result_c=sql_select($sql_data_c);
		foreach($sql_result_c as $rowc)
		{
			
				if($rowc[csf('is_confirmed')]==1) //Confirm
				{
					if($db_type==0) $date_week_cond=date("d-m-y",strtotime($rowc[csf('conf_ship_date')]));
					else $date_week_cond=date("d-M-y",strtotime($rowc[csf('conf_ship_date')]));
					
					$wo_color_data_arr2[$rowc[csf('po_id')]]['shipdate'].=$rowc[csf('conf_ship_date')].',';
					$week_wise_order_qty_arr['po_qty'][$rowc[csf("po_id")]][$no_of_week_for_header_calc[$date_week_cond]]+=$rowc[csf("order_quantity")];
					$week_wise_order_qty_arr_t['po_qty'][$rowc[csf("po_id")]][$no_of_week_for_header[$date_week_cond]]+=$rowc[csf("order_quantity")];
				}
			
		}
		
	  $sql_data_po="select a.job_no,a.job_no_prefix_num,a.company_name,a.buyer_name,a.set_smv,a.season_buyer_wise  as 	season,a.product_code,a.bh_merchant,a.dealing_marchant,a.style_ref_no,b.id,b.is_confirmed,b.po_number,b.po_received_date,b.pub_shipment_date,b.shipment_date,a.total_set_qnty as set_ratio,(b.po_quantity*a.total_set_qnty) as po_qty_pcs,b.po_total_price,b.unit_price 
	   from wo_po_details_master a, wo_po_break_down b
	 where a.job_no=b.job_no_mst   and a.is_deleted=0 and a.status_active=1  and b.is_deleted=0 and b.status_active=1   and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $team_cond $yarnPoIds_cond $bh_mer_name_con order by a.id,a.job_no";
					 
	$sql_data=sql_select($sql_data_po);
	$tot_rows=count($sql_data);
	$all_po_id="";
	foreach( $sql_data as $row)
	{
		if($all_po_id=="") $all_po_id=$row[csf("id")]; else $all_po_id.=",".$row[csf("id")];
	}
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
					$poIds_cond.=" b.id  in($ids) or ";
				}
				$poIds_cond=chop($poIds_cond,'or ');
				$poIds_cond.=")";
			}
			else
			{
				$poIds_cond=" and  b.id  in($all_po_id)";
			}
		}
		ob_start();
		$rowcount=0;
		foreach($week_counter_header as $mon_key=>$week)
		{
			$rowcount+=count($week);
		}
		 $rowb=$rowcount*2;
		 $td_width=2060+($rowcount*45)+$rowb;
		 
		  $tot_month = datediff( 'm', $date_start,$date_end);
		for($i=0; $i<= $tot_month; $i++ )
		{
			$next_month=month_add($date_start,$i);
			$month_arr.=date("Y-M",strtotime($next_month)).',';
		}
		$month_arr_id=rtrim($month_arr,',');
		if($tot_rows>0)
		{
	?>
		
        <script>
			var mon_week ='<? echo $month_arr_id; ?>';
			var mon_week=mon_week.split(",");
			
		for (var k=0; k<mon_week.length; k++)
		{
			var mon_data=document.getElementById('summary_footer_td_'+mon_week[k]).innerHTML;
			var mon_arr=mon_data.split("$");
			var mon_qnty=number_format(mon_arr[0],0);
			var mon_amt=number_format(mon_arr[1],0);
			var avg_order_smv=number_format(mon_arr[2],2);
			var avg_rate=(number_format(mon_arr[1],0,'.','')/number_format(mon_arr[0],0,'.',''));
			
			//alert(mon_amt);
			document.getElementById('summary_header_td_qty_'+mon_week[k]).innerHTML=mon_qnty;
			document.getElementById('summary_header_td_val_'+mon_week[k]).innerHTML='$'+mon_amt;
			
			document.getElementById('summary_header_td_rate_'+mon_week[k]).innerHTML='FOB:'+number_format(avg_rate,2);
			document.getElementById('summary_header_td_smv_'+mon_week[k]).innerHTML='SMV:'+number_format(avg_order_smv,2);
			
		}
		var yarn_req_qty=document.getElementById('td_yarn_req').innerHTML;
		var yarnreq_qty=number_format(yarn_req_qty,0);
		document.getElementById('yarn_summary_tot').innerHTML=yarnreq_qty;
		//alert(yarn_req_qty);
			</script>
            <?
		}
			?>
        <div style="width:<? echo $td_width+20;?>px">
        
		<fieldset style="width:100%;">	
      
        
			<table width="<? echo $td_width+20;?>px">
				<tr class="form_caption" style="display:none">
					<td align="center" style="margin-left:400px" colspan="<? echo $rowcount+25; ?>">Order Booking Status</td>
				</tr>
				<tr class="form_caption" align="center" style="display:none">
					<td colspan="<? echo $rowcount+26; ?>" align="center"><? //echo $company_library[$company_name]; ?></td>
				</tr>
			</table>
			<table style="font-size:9px" class="rpt_table" width="<? echo $td_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
                  <tr>
                     <th  width="" colspan="11" rowspan="2">&nbsp;</th>
                     <th  rowspan="2" id="yarn_summary_tot"></th>
                    <th  width="" colspan="14" rowspan="2">&nbsp;</th>
                     <?
					foreach($week_counter_header as $mon_key=>$week)
					{
					?>
					<th  style="width:auto" align="center" colspan="<? echo count($week);?>">
					<? 
					echo $mon_key;
					?>
					</th>
					<?
					}
            ?>
                </tr>
                  <tr>
                     <?
					foreach($week_counter_header as $mon_key=>$week_val)
					{
					?>
					<th style="width:auto;font-size:0.836em;"  align="center" colspan="<? echo count($week_val)?>">
                              <b id="summary_header_td_qty_<? echo $mon_key ;?>"  style="text-align:left; font-size: 0.775em;">  </b> &nbsp;
                              <b id="summary_header_td_val_<? echo $mon_key ;?>"  style="text-align: right; font-size: 0.775em;"> </b> &nbsp;
                              <b id="summary_header_td_rate_<? echo $mon_key ;?>"  style="text-align: right; font-size: 0.775em;"></b> 
                              <b id="summary_header_td_smv_<? echo $mon_key ;?>"  title="AVG SMV" style="text-align: right; font-size: 0.775em;"></b>
					</th>
					<?
					}
            ?>
                   
                </tr>
                <tr style="font-size:9px" >
					<th width="30">SL</th>
                    <th width="55">Season</th>
                    <th width="80">BH Mer.</th>
					<th width="80">FFL Mer.</th>
                    <th width="55">Job No</th>
					<th width="200">Style Ref</th>
                    <th width="60">Comp%</th>
                    <th width="120">Comp Name</th>
                    <th width="120">Const.</th>
                    <th width="50">GSM</th>
					<th width="120">Y/C</th>
                    <th width="70">Yarn Qty</th>
					<th width="60">Status</th>
					<th width="35">OPD</th>
					<th width="45">OPD Date</th>
					<th width="40">TOD</th>
                    <th width="70">Ord Qty</th>
					<th width="350">Order No</th>
                    <th width="40">Dept </th>
					<th width="40">Lead Time</th>
					<th width="40">Unit Price</th>
					<th width="70">Value</th>
					<th width="40">SMV/ Pcs</th>
                    <th width="60">Total SMV</th>
                    <th width="60">Delv Qty</th>
                    <th width="60">Delv.Val</th>
                     <?
					foreach($week_counter_header as $mon_key=>$week)
					{
						foreach($week as $week_key)
						{
							$start_week_date=$week_start_day[$week_key][week_start_day];
							$end_week_date=$week_end_day[$week_key][week_end_day];
							if($db_type==2)
							{
								$fisrtday=date("D",strtotime(change_date_format($start_week_date,"dd-mm-yyyy","-")));
								$lastday=date("D",strtotime(change_date_format($end_week_date,"dd-mm-yyyy","-")));
							}
							else
							{
								$fisrtday=date("D",strtotime(change_date_format($start_week_date,"dd-mm-yyyy")));
								$lastday=date("D",strtotime(change_date_format($end_week_date,"dd-mm-yyyy")));
							}
							$weekstart_date=explode("-",date("d-M",strtotime($start_week_date)));
							$weekstart_date=$weekstart_date[0].'/'.$weekstart_date[1];
						?>
						<th title="<? echo change_date_format($start_week_date,"dd-mm-yyyy","-").'('.$fisrtday.')'." To ".change_date_format($end_week_date,"dd-mm-yyyy","-").'('.$lastday.')'; ?>" width="45"  align="center">
						<? 
						echo 'W'.$week_key.'<br/>'.$weekstart_date;
						?>
						</th>
						<?
						}
					}
            ?>
                </tr>    
				</thead>
			</table>
			<div style="width:<? echo $td_width+20;?>px; max-height:245px; overflow-y:scroll" id="scroll_body">
			<table class="rpt_table" width="<? echo $td_width;?>px" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
	<?
	
			$condition= new condition();
			 $condition->company_name("=$cbo_company_name");
			 if(str_replace("'","",$cbo_buyer_name)>0){
				  $condition->buyer_name("=$cbo_buyer_name");
			 }
			 if(str_replace("'","",$txt_job_no) !=''){
				  $condition->job_no_prefix_num("in($txt_job_no)");
			 }
					if(str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!=''){
					  $condition->pub_shipment_date(" between '$start_date' and '$end_date'");
				 	}
				
			 if(str_replace("'","",$txt_order_no)!='')
			 {
				$condition->po_number("=$txt_order_no"); 
			 }
			 if(str_replace("'","",$txt_season)!='')
			 {
				//$condition->season("=$txt_season"); 
			 }
			 $condition->init();
			 
			 	
        		 $yarn= new yarn($condition);
		 		// echo $yarn->getQuery(); die;
				 $yarn_req_qty_arr=$yarn->getOrderWiseYarnQtyArray();
				//print_r($yarn_req_qty_arr);
				$i=1;$total_order_qty=0;$total_order_value=0;$total_week_qty=0;$total_yarn_req_qty=0;$total_smv_qty=0;
				foreach($sql_data as $row_data)
				{
				   if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				//if($row_data[csf('bh_merchant')]!='')
				//{
					if($row_data[csf('bh_merchant')]=='') $row_data[csf('bh_merchant')]='0';else $row_data[csf('bh_merchant')]=$row_data[csf('bh_merchant')];
			
				$po_received_date=$row_data[csf('po_received_date')];
				$countryship_date=$wo_color_data_arr[$row_data[csf('id')]]['c_shipdate'];
				$po_orgi_shipdate=$row_data[csf('shipment_date')];
				//echo $wo_color_data_arr2[$row_data[csf('id')]]['shipdate'];
				$conf_country_ship_date=rtrim($wo_color_data_arr2[$row_data[csf('id')]]['shipdate'],',');
				$conf_country_ship_date=array_unique(explode(",",$conf_country_ship_date));
				//print_r($conf_country_ship_date);
				$yarn_req=$yarn_req_qty_arr[$row_data[csf('id')]];//($yarn_req_qty_arr[$row_data[csf('id')]]/$row_data[csf('po_qty_pcs')])*12;
				$percent_one=$wo_fab_data_arr[$row_data[csf('id')]]['percent_one'];
				$percent_two=$wo_fab_data_arr[$row_data[csf('id')]]['percent_two'];
				if($percent_one!=0 && $percent_two!=0)
				{
					//if($percent_two!=0)
					$percent_all=$percent_one.'/'.$percent_two;
				}
				else if($percent_one!=0 && $percent_two==0)
				{
					//if($percent_two!=0)
					$percent_all=$percent_one;
				}
				else
				{
					$percent_all='';	
				}
				$copm_two_id=$wo_fab_data_arr[$row_data[csf('id')]]['copm_two_id'];
				$copm_one_id=$wo_fab_data_arr[$row_data[csf('id')]]['copm_one_id'];
				if($copm_one_id!=0 && $copm_two_id!=0)
				{ 
					$copm_one_name=$composition[$copm_one_id].'/'.$composition[$copm_two_id];
				}
				else if($copm_one_id!=0 && $copm_two_id==0)
				{ 
					$copm_one_name=$composition[$copm_one_id];
				
				}
				else 
				{
					$copm_one_name='';
				}
				$construction=rtrim($wo_fab_data_arr[$row_data[csf('id')]]['construction'],',');
				$construction=implode(",",array_unique(explode(",", $construction)));
				$gsm_weight=rtrim($wo_fab_data_arr[$row_data[csf('id')]]['gsm_weight'],',');
				$gsm_weight=implode(",",array_unique(explode(",", $gsm_weight)));
				$count_id=rtrim($wo_fab_count_data_arr[$row_data[csf('id')]]['count_id'],',');
				$count_ids=array_unique(explode(",", $count_id));
			    $yarn_count_value="";
				foreach($count_ids as $val)
				{
						if($yarn_count_value=="") $yarn_count_value=$lib_yarn_count_arr[$val]; else $yarn_count_value.=", ".$lib_yarn_count_arr[$val];
				}
				
					$lead_time = datediff( 'd', $po_received_date,$po_orgi_shipdate);
					$qnty_array2=array();
					if($row_data[csf('is_confirmed')]==1) //Confirm
					{
						//$order_qty=0;
						foreach($conf_country_ship_date as $c_date)
						{
							
							if($db_type==0) $c_date_con=date("d-m-y",strtotime($c_date));
							else $c_date_con=date("d-M-y",strtotime($c_date));
							
							$qnty_array2[$no_of_week_for_header_calc[$c_date_con]]=$week_wise_order_qty_arr['po_qty'][$row_data[csf("id")]][$no_of_week_for_header_calc[$c_date_con]];//date("d-M-y",strtotime($rowc[csf('conf_ship_date')]))]
						}
					}
					else 
					{
						 $order_qty_proj=$row_data[csf('po_qty_pcs')];
						 $qnty_array2=distribute_projection($no_of_week_for_header[$po_orgi_shipdate], $order_qty_proj);
					}
					$order_qty=0;
                   	foreach($week_counter_header as $mon_key=>$week)
					{
						foreach($week as $week_key)
						{
							 $order_qty+=$qnty_array2[$week_key];
						}
					}
				?>
                <tr style="font-size:9px" align="center" bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
					<td width="30"><? echo $i; ?></td>
                    <td width="55" style="word-wrap:break-word; word-break: break-all;"><? echo $row_data[csf('season')];?></td>
                     <td width="80" style="word-wrap:break-word; word-break: break-all; text-align:left" ><? echo $row_data[csf('bh_merchant')]; ?></td> 
					<td width="80" style="word-wrap:break-word; word-break: break-all; text-align:left" ><? echo $team_member_arr[$row_data[csf('dealing_marchant')]]; ?></td> 
                  
                    <td width="55" style="word-wrap:break-word; word-break: break-all;"><? echo $row_data[csf('job_no_prefix_num')];//$buyer_short_name_library[$row_data[csf('buyer_name')]]; ?></td>
					<td  width="200" style="word-wrap:break-word; word-break: break-all;text-align:left"><? echo $row_data[csf('style_ref_no')]; ?></td>
                   
                    
                    <td width="60" style="word-wrap:break-word; word-break: break-all;text-align:left"><? echo $percent_all; ?></td>
                    <td  width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $copm_one_name; ?></td>
                    
                    
                    <td  width="120" style="word-wrap:break-word; word-break: break-all;text-align:left"><? echo $construction;?></td>
                    <td width="50"  style="word-wrap:break-word; word-break: break-all;"><? echo $gsm_weight; ?></td>
					<td  width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $yarn_count_value; ?></td>
                    <td  width="70" style="word-wrap:break-word; word-break: break-all;text-align:right">
					<?
							echo number_format($yarn_req);
					?>
                    </td>
					<td  width="60" style="word-wrap:break-word; word-break: break-all;">
					<?
					
					if($row_data[csf('is_confirmed')]==2)
					{
						echo "Booked";	
					}
					else
					{
						echo "Placed";
					}
					//echo $order_status[$row_data[csf('is_confirmed')]];
					?> 
                    </td>
					<td  width="35" style="word-wrap:break-word; word-break: break-all;">
					<?
					if($no_of_week_for_header[$row_data[csf("po_received_date")]]!='') echo 'W'.$no_of_week_for_header[$row_data[csf("po_received_date")]];else echo '';
					
					?>
                    </td>
					<td  width="45" title="<? echo date("d-M-y",strtotime($row_data[csf('po_received_date')]))?>" style="word-wrap:break-word; word-break: break-all;">
					<?
					if($row_data[csf('po_received_date')] !="" && $row_data[csf('po_received_date')] !="0000-00-00" && $row_data[csf('po_received_date')] !="0")
					{

					//echo change_date_format($row_data[csf('po_received_date')],'dd-mm-yyyy','-');
					echo date("d-M",strtotime($row_data[csf('po_received_date')]));
					}
					?>
                    </td>
					<td width="40" title="Orgi. Ship Date=<? echo $po_orgi_shipdate;?>" style="word-wrap:break-word; word-break: break-all; text-align: center">
					<? 
					if($no_of_week_for_header[$po_orgi_shipdate]!='') echo 'W'.$no_of_week_for_header[$po_orgi_shipdate];else echo ''; ?>
                    </td>
                    <td  width="70" style="word-wrap:break-word; word-break: break-all; text-align:right">
					<? echo number_format($order_qty);?>
                    </td>
					<td  width="350" style="word-wrap:break-word; word-break: break-all; text-align:left">
					<? echo $row_data[csf('po_number')]; ?>
                    </td>
                    <td  width="40" style="word-wrap:break-word; word-break: break-all; text-align:left" >
					<? echo $row_data[csf('product_code')];?>
                    </td>
					<td  width="40" title="TOD-OPD" style="word-wrap:break-word; word-break: break-all; text-align: center">
					<? echo $lead_time; ?>
                    </td>
					<td  width="40" style="word-wrap:break-word; word-break: break-all; text-align:right">
					<? echo number_format($row_data[csf('unit_price')],2); ?>
                    </td>
                   
					<td width="70" style="word-wrap:break-word; word-break: break-all;text-align:right">
                   <? echo number_format($order_qty*$row_data[csf('unit_price')],0);
				  	 $set_smv=number_format($row_data[csf('set_smv')],2);
				    ?>
                    </td>
					<td width="40" title="Set SMV/Set Ratio" style="word-wrap:break-word; word-break: break-all;">
					<? 
					$smv_rate=0;
					$smv_rate=$set_smv/$row_data[csf('set_ratio')];
					echo number_format($smv_rate,2); ?>
                    </td>
					
                    <td  width="60" title="Set SMV Per Pcs*Order Qty" style="word-wrap:break-word; word-break: break-all;text-align:right">
					<? echo number_format(($set_smv/$row_data[csf('set_ratio')])*$order_qty); ?>
                    </td>
                     <td  width="60" style="word-wrap:break-word; word-break: break-all;text-align:right">
					<? 
					$del_qty=$exfactory_data_array[$row_data[csf('id')]][ex_factory_qnty];
					$del_amount=$exfactory_data_array[$row_data[csf('id')]][ex_factory_qnty]*$row_data[csf('unit_price')];
					if($del_qty>0) echo number_format($del_qty);else echo ' '; ?>
                    </td>
                    <td width="60" style="word-wrap:break-word; word-break: break-all;text-align:right">
                    <? if($del_amount>0)  echo  number_format($del_amount,0); else echo ' '; ?>
                    </td>
                    <?
					$qnty_array=array();
					if($row_data[csf('is_confirmed')]==1) //Confirm
					{
						foreach($conf_country_ship_date as $c_date)
						{
								if($db_type==0) $c_date_con=date("d-m-y",strtotime($c_date));
								else $c_date_con=date("d-M-y",strtotime($c_date));
							$qnty_array[$no_of_week_for_header_calc[$c_date_con]]=$week_wise_order_qty_arr['po_qty'][$row_data[csf("id")]][$no_of_week_for_header_calc[$c_date_con]];
							//date("d-M-y",strtotime($rowc[csf('conf_ship_date')]))
							//$week_wise_order_qty[$row[csf("po_id")]][$no_of_week_for_header[$conf_country_ship_date]]['po_quantity']
						}
					
					}
					else
					{
						 $order_qty_proj=$row_data[csf('po_qty_pcs')];
						$qnty_array=distribute_projection($no_of_week_for_header[$po_orgi_shipdate], $order_qty_proj);
					}
					// print_r($qnty_array); die;
					$week_qty=0;
					$week_check=array();
                   	foreach($week_counter_header as $mon_key=>$week)
					{
						foreach($week as $week_key)
						{
						?>
						<td  style="word-wrap:break-word; word-break: break-all;" width="45" align="right">
						<? 
							 	$week_qty=$qnty_array[$week_key];
								if($week_qty>0) echo number_format($week_qty,0);else echo ' ';
								$tot_week_qty[$mon_key][$week_key]+=$week_qty;
								$tot_week_amount[$mon_key][$week_key]+=$week_qty*$row_data[csf('unit_price')];
								$smv_avg=0;
								$smv_avg=$smv_rate*$week_qty;
								$month_smv_arr[$mon_key]+=$smv_avg;
						?>
						</td>
						<?
						}
					}
					?>
                    </tr>
                    <?
					$total_order_qty+=$order_qty;
					$total_yarn_req_qty+=$yarn_req;
					$total_order_value+=$order_qty*$row_data[csf('unit_price')];
					$total_week_qty+=$week_qty;
					$total_smv_qty+=($set_smv/$row_data[csf('set_ratio')])*$order_qty;
					//=====================================================================================================================
				$i++;
				//}
				}
				?>
                 
				</table>
            </div>
				<table  style="font-family:'Arial Narrow'; font-size:9px" class="tbl_bottom" width="<? echo $td_width;?>px" cellpadding="0"  id="report_table_footer"  cellspacing="0" border="1" rules="all" >
					<tr>
					<td width="30">&nbsp;</td>
                    <td width="55">&nbsp;</td>
                    <td width="80">&nbsp;</td>
					<td width="80">&nbsp;</td>
                   
                    <td width="55">&nbsp;</td>
					<td width="200">&nbsp;</td>
                    <td width="60">&nbsp;</td>
                    <td width="120">&nbsp;</td>
                    <td width="120">&nbsp;</td>
                    <td width="50">&nbsp;</td>
					<td width="120">&nbsp;</td>
                    <td width="70"  style="word-wrap:break-word; word-break: break-all;font-size: smaller"  id="td_yarn_req"><? echo number_format($total_yarn_req_qty);?></td>
					<td width="60">&nbsp;</td>
					<td width="35">&nbsp;</td>
					<td width="45">&nbsp;</td>
					<td width="40">&nbsp;</td>
                    <td width="70" style="word-wrap:break-word; word-break: break-all;font-size: smaller" id="td_po_qty"><? echo $total_order_qty;?></td>
					<td width="350">&nbsp;</td>
                    <td width="40">&nbsp; </td>
					<td width="40">&nbsp;</td>
					<td width="40"><? echo number_format($total_order_value/$total_order_qty,2);?></td>
					<td width="70" id="td_po_val" style="word-wrap:break-word; word-break: break-all;font-size: smaller"><? echo number_format($total_order_value);?></td>
					<td width="40"><? echo number_format($total_smv_qty/$total_order_qty,2);?></td>
                    <td width="60"  id="td_po_smv" style="word-wrap:break-word; word-break: break-all;font-size: smaller"><? echo number_format($total_smv_qty,0);?></td>
                    <td width="60">&nbsp;</td>
                    <td width="60">&nbsp;</td>
                     <?
					//foreach($week_for_header as $week_key)
					$total_week_qty=0; $total_week_amt=0; 
					foreach($week_counter_header as $mon_key=>$week)
					{
						foreach($week as $week_key)
						{
						?>
						<td width="45" title="<? echo $tot_week_amount[$mon_key][$week_key];?>" style="word-wrap:break-word; word-break: break-all;font-size: smaller"   align="center">
						<? 
						$total_week_qty=$tot_week_qty[$mon_key][$week_key];
						$total_week_amt=$tot_week_amount[$mon_key][$week_key];
						echo number_format($total_week_qty,0);
						$tot_mon_qty[$mon_key]+=$total_week_qty;
						$tot_mon_amount[$mon_key]+=$total_week_amt;
						?>
						</td>
						<?
						$w++;
						}
						
					}
            ?>
					</tr>
                     <tr style="display:none">
					<td colspan="26">&nbsp;</td>
                     <?
					$total_week_qty=0; $m=1;
					foreach($week_counter_header as $mon_key=>$week)
					{
						?>
						<td width="" id="summary_footer_td_<? echo $mon_key?>" colspan="<? echo count($week);?>" title="<? echo $tot_week_amount[$mon_key][$week_key];?>" style="word-wrap:break-word; word-break: break-all;"   align="center">
						
						<? 
						$tot_smv=0;
						$tot_smv=$month_smv_arr[$mon_key]/$tot_mon_qty[$mon_key];
						$head_td_data=$tot_mon_qty[$mon_key].'$'.$tot_mon_amount[$mon_key].'$'.$tot_smv;
						echo $head_td_data;
						?>
						</td>
						<?
					}
            ?>
                    </tr>
				</table>
				
			</fieldset>
		</div>
        
            <style>
			@font-face {
   				 font-family:"Arial Narrow"; font-size:10px;
   				 
			}
			TD{font-family:"Arial Narrow";font-size:11px;}
			TH{font-family:"Arial Narrow";font-size:10px;}
			
			.rpt_table TD{font-family: "Arial Narrow";font-size:10px;}
			.rpt_table TH{font-family:"Arial Narrow";font-size:10px;}
			
			</style>
        
	<?
	}
//===========================================================================================================================================================
	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data****$filename****$tot_rows****$cbo_search_type";
	exit();	
} //Order Wise End
else if($action=="report_generate_merchand")
{
 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company_name=str_replace("'","",$cbo_company_name);
	$cbo_search_type=str_replace("'","",$cbo_search_type);
	$cbo_bh_mer_name=str_replace("'","",$cbo_bh_mer_name);
	$txt_yarn_count=str_replace("'","",$txt_yarn_count);
	if($txt_yarn_count!='') $yarn_count_con="and yarn_count Like '%$txt_yarn_count%' ";else $yarn_count_con='';
	$buyer_id_cond="";
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{
				$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else
			{
				$buyer_id_cond="";
			}
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}
	

	
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_job_no=trim($txt_job_no);
	if($txt_job_no !="" || $txt_job_no !=0)
	{
		//$year = substr(str_replace("'","",$cbo_year_selection), -2); 
		//$job_no=$company_library[$company_name]."-".$year."-".str_pad($txt_job_no, 5, 0, STR_PAD_LEFT);
		$jobcond="and a.job_no_prefix_num in (".$txt_job_no.")";
	}
	else
	{
		$jobcond="";	
	}
	
	//echo $jobcond.'fd';
	$date_cond='';

		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			$start_date=(str_replace("'","",$txt_date_from));
			$end_date=(str_replace("'","",$txt_date_to));
			
			$date_cond="and b.pub_shipment_date between '$start_date' and '$end_date'";
		}
	
	//if (str_replace("'","",$txt_job_no)=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in (".str_replace("'","",$txt_job_no).") ";
	
	if(str_replace("'","",$txt_style_ref)!="") $style_ref_cond=" and a.style_ref_no like '%".str_replace("'","",$txt_style_ref)."%'"; else $style_ref_cond="";
	if(str_replace("'","",$txt_order_no)!="")
	{
		$ordercond=" and b.po_number like '%".str_replace("'","",$txt_order_no)."%'"; 
	}
	else
	{
		$ordercond="";
	}
	
	$team_cond="";
	if(str_replace("'","",$cbo_team_name)!=0) $team_cond=" and a.team_leader=".str_replace("'","",$cbo_team_name)."  ";
	if(str_replace("'","",$cbo_team_member)!=0) $team_cond.=" and a.dealing_marchant=".str_replace("'","",$cbo_team_member)."  ";
	$cbo_bh_mer_name=str_replace("0","",$cbo_bh_mer_name);
	if($cbo_bh_mer_name!=0 || $cbo_bh_mer_name!='') $bh_mer_name_con="and a.bh_merchant='$cbo_bh_mer_name' ";else $bh_mer_name_con='';
	 $cbo_year_selection=str_replace("'","",$cbo_year_selection);

	$start_ddd= strtotime(change_date_format($start_date,"dd-mm-yyyy","-"));
	$start_ddd=date("Y-m-d",$start_ddd);
	$start_end_date= strtotime(change_date_format($end_date,"dd-mm-yyyy","-"));
	$start_end_date=date("Y-m-d",$start_end_date);
	 //$dd= date('d-m-Y',strtotime($start_date));
	//$start_date_week= date($dd, time() - 1296000);//$start_date;
	//echo $last_day_this_month  = date($start_date_week, time() -1814400); //date('t-m-Y');
	
	$dateee = strtotime($start_ddd);
	$start_end_date = strtotime($start_end_date);
	
	$date_start = date("Y-m-d",strtotime("-28 day", $dateee));
	$date_end = date("Y-m-d",strtotime("+28 day", $start_end_date));
	//echo $date_end;
	//$end_date_week=  addday("$end_date", time() -1814400);//$start_date;//$start_date;
	if($db_type==0)
	{
		$date_start= change_date_format($date_start,"yyyy-mm-dd");
		$date_end= change_date_format($date_end,"yyyy-mm-dd");
	}
	else
	{
		$date_start= change_date_format($date_start,"dd-mm-yyyy","-",1);
		$date_end= change_date_format($date_end,"dd-mm-yyyy","-",1);
	}
	
	
	//echo "select week_date,week from week_of_year where week_date between '$date_start' and  '$date_end' order by week_date asc";die;
	// date("d-m-Y", time() - 1296000);
	$week_for_arr=array();$no_of_week_for_header=array();
	$sql_week_header=sql_select("select week_date,week from week_of_year where week_date between '$date_start' and  '$date_end' order by week_date asc");
	$week_check_head=array();
	foreach ($sql_week_header as $row_week_header)
	{
		if($week_check_head[$row_week_header[csf("week")]]=='')
		{
			$week_counter_header[date("Y-M",strtotime($row_week_header[csf("week_date")]))][$row_week_header[csf("week")]]=$row_week_header[csf("week")];
			$week_check_head[$row_week_header[csf("week")]]=$week_counter_header[date("Y-M",strtotime($row_week_header[csf("week_date")]))][$row_week_header[csf("week")]];
		}
		$tmp=add_date($row_week_header[csf("week_date")],-1);
		if($db_type==0) $tmp_cond=date("d-m-y",strtotime($tmp));
		else $tmp_cond=date("d-M-y",strtotime($tmp));
		$no_of_week_for_header_calc[$tmp_cond]=$row_week_header[csf("week")];	
		$no_of_week_for_header[$row_week_header[csf("week_date")]]=$row_week_header[csf("week")];
		$test[$row_week_header[csf("week")]]=$row_week_header[csf("week")];
	}
	unset($sql_week_header);
	$week_start_day=array();
	$week_end_day=array();$week_day_arr=array();
	//week_day
	$sql_week_start_end_date=sql_select("select week,min(week_date) as week_start_day, Max(week_date) as week_end_day from week_of_year where week_date between '$date_start' and  '$date_end' group by week");
	foreach ($sql_week_start_end_date as $row_week)
	{
		$week_start_day[$row_week[csf("week")]][week_start_day]=$row_week[csf("week_start_day")];
		$week_end_day[$row_week[csf("week")]][week_end_day]=$row_week[csf("week_end_day")];
		//$week_day_arr[$row_week[csf("week")]][$row_week[csf("week_start_day")]][week_first_day]=$row_week[csf("min_week_day")];
		$week_day_arr[$row_week[csf("week_end_day")]]['week_last_day']=$row_week[csf("last_week_day")];
	}
	unset($sql_week_start_end_date);
	//print_r($week_day_arr);
	function distribute_projection( $base_week, $qnty)
	{
		//5. Distribution ratios are: Base week 52%, Immediate before 24%, Week before immediate week 20%, Immediate week after base week 2%, next 2 weeks as 1%
		global $test;
		 
		$tmpbase_week=$test[$base_week-3];
		$k=0;
		for($i=0; $i<6; $i++)
		{
			$tmpbase_week++;
			if( $test[$tmpbase_week]=='') { $tmpbase_week=1; $k=0; }
			
			
			if($i==0) $distr_arr[$tmpbase_week]=($qnty*20)/100;
			if($i==1) $distr_arr[$tmpbase_week]=($qnty*24)/100;
			if($i==2) $distr_arr[$tmpbase_week]=($qnty*52)/100;
			if($i==3) $distr_arr[$tmpbase_week]=($qnty*2)/100;
			if($i==4) $distr_arr[$tmpbase_week]=($qnty*1)/100;
			if($i==5) $distr_arr[$tmpbase_week]=($qnty*1)/100;
			$k++;
			
		}
		 
		return $distr_arr;
		/*5000
		b=52
		b-1=21
		b-2=15
		b+1=1
		b+2=1*/
	}
//echo "<per>";
 //print_r(distribute_projection("2016-Dec",51, 5000)); die;


	
	if($template==1)
	{
	$po_wise_buyer=array();
	$buyer_wise_data=array();
	$week_wise_order_qty_arr=array();
	$wo_color_data_arr2=array();	
	
	$count_data=sql_select("select yarn_count,id from  lib_yarn_count  where status_active=1 and is_deleted=0 and yarn_count is not null $yarn_count_con");
	$yarn_id='';
	foreach($count_data as $row)
	{
			if($yarn_id=='') $yarn_id=$row[csf('id')];else $yarn_id.=",".$row[csf('id')];
			
	}
		$yarn_ids=count(array_unique(explode(",",$yarn_id)));
		$yarn_numIds=chop($yarn_id,','); $yarnIds_cond="";
		if($yarn_id!='' || $yarn_id!=0)
		{
			if($db_type==2 && $yarn_ids>1000)
			{
				$yarnIds_cond=" and (";
				$yIdsArr=array_chunk(explode(",",$yarn_numIds),990);
				foreach($yIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$yarnIds_cond.=" d.count_id  in($ids) or ";
				}
				$yarnIds_cond=chop($yarnIds_cond,'or ');
				$yarnIds_cond.=")";
			}
			else
			{
				$yarnIds_cond=" and  d.count_id  in($yarn_id)";
			}
		}
	$yarn_sql_data=("select a.dealing_marchant,b.is_confirmed,b.id as po_id,d.count_id,d.copm_one_id,d.copm_two_id,d.percent_one,d.percent_two,sum(d.cons_qnty) as cons_qnty,e.construction,e.gsm_weight,b.shipment_date as orgi_ship_date from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fab_yarn_cost_dtls d,wo_pre_cost_fabric_cost_dtls e where a.job_no=b.job_no_mst  and c.job_no_mst=a.job_no and b.id=c.po_break_down_id and d.job_no=a.job_no and 
	e.id=d.fabric_cost_dtls_id and  c.item_number_id= e.item_number_id and e.job_no=a.job_no and e.body_part_id in(1,20) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1   and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $team_cond $yarnIds_cond group by   b.id ,d.count_id,d.copm_one_id,d.copm_two_id,d.percent_one,d.percent_two,e.construction,e.gsm_weight,a.dealing_marchant,b.is_confirmed,b.shipment_date"); 
	$sql_result_y=sql_select($yarn_sql_data);
	$wo_fab_data_arr=array();
	$all_yarn_po_id="";
	foreach($sql_result_y as $row)
		{
			if($all_yarn_po_id=="") $all_yarn_po_id=$row[csf("po_id")]; else $all_yarn_po_id.=",".$row[csf("po_id")];
			$tod_week=$no_of_week_for_header[$row[csf('orgi_ship_date')]];
			$wo_fab_data_arr[$row[csf('dealing_marchant')]][$row[csf('is_confirmed')]]['percent_one']=$row[csf('percent_one')];
			$wo_fab_data_arr[$row[csf('dealing_marchant')]][$row[csf('is_confirmed')]]['percent_two']=$row[csf('percent_two')];
			$wo_fab_data_arr[$row[csf('dealing_marchant')]][$row[csf('is_confirmed')]]['copm_two_id']=$row[csf('copm_two_id')];
			$wo_fab_data_arr[$row[csf('dealing_marchant')]][$row[csf('is_confirmed')]]['copm_one_id']=$row[csf('copm_one_id')];
			$wo_fab_data_arr[$row[csf('dealing_marchant')]][$row[csf('is_confirmed')]]['construction'].=$row[csf('construction')].',';
			$wo_fab_data_arr[$row[csf('dealing_marchant')]][$row[csf('is_confirmed')]]['gsm_weight'].=$row[csf('gsm_weight')].',';
			$wo_fab_count_data_arr[$row[csf('dealing_marchant')]][$row[csf('is_confirmed')]]['count_id'].=$row[csf('count_id')].",";
			//$wo_yarn_data_arr[$row[csf('po_id')]]['cons_qnty']+=$row[csf('cons_qnty')];
		}
		unset($sql_result_y);
		$yarn_po_ids=count(array_unique(explode(",",$all_yarn_po_id)));
		$yarnPo_numIds=chop($all_yarn_po_id,','); $yarnPoIds_cond="";
		if($txt_yarn_count!='')
		{
		if($all_yarn_po_id!='' || $all_yarn_po_id!=0)
		{
			if($db_type==2 && $yarn_po_ids>1000)
			{
				$yarnPoIds_cond=" and (";
				$yPoIdsArr=array_chunk(explode(",",$yarnPo_numIds),990);
				foreach($yPoIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$yarnPoIds_cond.=" b.id  in($ids) or ";
				}
				$yarnPoIds_cond=chop($yarnPoIds_cond,'or ');
				$yarnPoIds_cond.=")";
			}
			else
			{
				$yarnPoIds_cond=" and  b.id  in($all_yarn_po_id)";
			}
		}
		}
	 	$delmarchant_wise_arr=array();$delmarchant_wise_arr_qty=array();
		 $sql_data_c=("select b.id as po_id,a.dealing_marchant,b.shipment_date as orgi_ship_date,b.is_confirmed,(b.po_quantity*a.total_set_qnty) as po_qty_pcs
		from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $team_cond $bh_mer_name_con order by a.dealing_marchant,b.shipment_date ");
		$sql_result_c=sql_select($sql_data_c);
		foreach($sql_result_c as $row)
		{
					if($row[csf('is_confirmed')]==2)
					{
						if($row[csf('dealing_marchant')]=='') $del_merchant=0;else $del_merchant=$row[csf('dealing_marchant')];
						$tod_week=$no_of_week_for_header[$row[csf('orgi_ship_date')]];
						$delmarchant_wise_arr_qty[$del_merchant][$row[csf('is_confirmed')]][$tod_week]['po_qty']+=$row[csf('po_qty_pcs')];
						$delmarchant_wise_arr[$del_merchant][$row[csf('is_confirmed')]]['orgi_ship_date'].=$row[csf('orgi_ship_date')].',';
					}
		}	
		//print_r($delmarchant_wise_arr);
		
		if($db_type==0) $date_dif="DATEDIFF(b.shipment_date, b.po_received_date) as lead_time_days";
		else  $date_dif="(b.shipment_date-b.po_received_date) as lead_time_days";
		$sql_query=("select a.job_no_prefix_num as job_no,a.set_smv,a.season_buyer_wise  as 	
	season,a.product_code,a.bh_merchant,a.dealing_marchant,a.style_ref_no,b.id,b.is_confirmed,b.po_number,b.po_received_date,b.shipment_date as orgi_ship_date,a.total_set_qnty as 
	set_ratio,(b.po_quantity*a.total_set_qnty) as po_qty_pcs,b.unit_price as unit_price, b.po_total_price, $date_dif,c.country_ship_date,c.order_quantity,c.order_total
	 from wo_po_details_master a, wo_po_break_down b
	 LEFT JOIN wo_po_color_size_breakdown c on  c.po_break_down_id=b.id  and  c.status_active=1
	 where a.job_no=b.job_no_mst  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1   and a.company_name=$company_name $buyer_id_cond $date_cond 
	 $style_ref_cond $jobcond $ordercond $team_cond $yarnPoIds_cond  $bh_mer_name_con order by a.dealing_marchant,b.shipment_date ");
	 
	$sql_data=sql_select($sql_query);
	$tot_rows=count($sql_data);
	$all_po_id="";
	$marchant_wise_arr=array();
	foreach( $sql_data as $row)
	{
		//$week_wise_qty_arr[date("Y-m",strtotime($row_data[csf("pub_shipment_date")]))]=$row_data[csf('pub_shipment_date')];
		if($row[csf('dealing_marchant')]=='') $del_merchant=0;else $del_merchant=$row[csf('dealing_marchant')];
		$country_ship_date=$wo_color_data_arr[$del_merchant][$row[csf('is_confirmed')]]['c_shipdate'];
		if($all_po_id=="") $all_po_id=$row[csf("id")]; else $all_po_id.=",".$row[csf("id")];
		
		//$c_date_week=$no_of_week_for_header[$row[csf('country_ship_date')]];
		if($db_type==0) $date_week_cond=date("d-m-y",strtotime($row[csf('country_ship_date')]));
		else $date_week_cond=date("d-M-y",strtotime($row[csf('country_ship_date')]));
		$c_date_week=$no_of_week_for_header_calc[$date_week_cond];
		
		if($row[csf('season')]!='' || $row[csf('season')]!=0)
		{
			$row[csf('season')]=$row[csf('season')];
		}
		else
		{
			$row[csf('season')]='';	
		}
		$marchant_wise_arr[$del_merchant][$row[csf('is_confirmed')]]['season'].=$row[csf('season')].',';	
		//if($row[csf('bh_merchant')]!='')
		//{
		$marchant_wise_arr[$del_merchant][$row[csf('is_confirmed')]]['bh_mer'].=$row[csf('bh_merchant')].',';	
		//}
		if($row[csf('is_confirmed')]==1)
		{
			$order_val=$row[csf('order_total')];
		}
		else
		{
			$order_val=$row[csf('po_total_price')];
		}
		$marchant_wise_arr[$del_merchant][$row[csf('is_confirmed')]]['po_qty']+=$row[csf('order_quantity')];
		$marchant_wise_arr[$del_merchant][$row[csf('is_confirmed')]]['set_smv']+=$row[csf('set_smv')];
		$marchant_wise_arr[$del_merchant][$row[csf('is_confirmed')]]['set_ratio']+=$row[csf('set_ratio')];
		$marchant_wise_arr[$del_merchant][$row[csf('is_confirmed')]]['po_val']+=$order_val;
		$marchant_wise_arr[$del_merchant][$row[csf('is_confirmed')]]['lead_day']+=$row[csf('lead_time_days')];
		$marchant_wise_arr[$del_merchant][$row[csf('is_confirmed')]]['job_no'].=$row[csf('job_no')].',';
		
		$marchant_wise_arr[$del_merchant][$row[csf('is_confirmed')]]['pcode'].=$row[csf('product_code')].',';
		$marchant_wise_arr[$del_merchant][$row[csf('is_confirmed')]]['style'].=$row[csf('style_ref_no')].',';
		$marchant_wise_arr[$del_merchant][$row[csf('is_confirmed')]]['po_no'].=$row[csf('po_number')].',';
		$marchant_wise_arr[$del_merchant][$row[csf('is_confirmed')]]['po_id'].=$row[csf('id')].',';	
		if($row[csf('is_confirmed')]==2)
		{
		$marchant_wise_arr[$del_merchant][$row[csf('is_confirmed')]]['orgi_shipdate'].=$row[csf('orgi_ship_date')].',';
		}
		//$marchant_wise_arr[$row[csf('dealing_marchant')]][$row[csf('is_confirmed')]]['bh_mer'].=$row[csf('bh_merchant')].',';
		$marchant_wise_arr[$del_merchant][$row[csf('is_confirmed')]]['po_received_date'].=$row[csf('po_received_date')].',';
		
		
		if($row[csf('is_confirmed')]==1)
		{		
			$week_wise_order_qty[$del_merchant][$row[csf('is_confirmed')]][$c_date_week]['po_quantity']+=$row[csf("order_quantity")];
			$marchant_wise_arr[$del_merchant][$row[csf('is_confirmed')]]['c_ship_date'].=$row[csf('country_ship_date')].',';
		}
	}
	//print_r($week_wise_order_qty);
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
					$poIds_cond.=" b.id  in($ids) or ";
				}
				$poIds_cond=chop($poIds_cond,'or ');
				$poIds_cond.=")";
			}
			else
			{
				$poIds_cond=" and  b.id  in($all_po_id)";
			}
		}
		$exfactory_data_array=array();
		$exfactory_sql=("select a.dealing_marchant,b.is_confirmed,b.shipment_date as orgi_ship_date,
		sum(CASE WHEN c.entry_form!=85 THEN c.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		sum(CASE WHEN c.entry_form=85 THEN c.ex_factory_qnty ELSE 0 END) as return_qnty
		 from wo_po_details_master a, wo_po_break_down b,pro_ex_factory_mst c  where  a.job_no=b.job_no_mst and  c.po_break_down_id=b.id and  a.company_name=$company_name  and c.status_active=1 and c.is_deleted=0 $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $team_cond  $poIds_cond group by a.dealing_marchant,b.is_confirmed,b.shipment_date");
		$exfactory_data=sql_select($exfactory_sql);
		foreach($exfactory_data as $row)
		{
				if($row[csf('dealing_marchant')]=='') $del_merchant=0;else $del_merchant=$row[csf('dealing_marchant')];
				$exfactory_data_array[$del_merchant][$row[csf('is_confirmed')]][ex_qnty]+=$row[csf('ex_factory_qnty')]-$row[csf('return_qnty')];
		}
		unset($exfactory_data);
		ob_start();
		$rowcount=0;
		foreach($week_counter_header as $mon_key=>$week)
		{
			$rowcount+=count($week);
		}
		 $rowb=$rowcount*2;
		 $td_width=1805+($rowcount*45)+$rowb;
		 
		  $tot_month = datediff( 'm', $date_start,$date_end);
		for($i=0; $i<= $tot_month; $i++ )
		{
			$next_month=month_add($date_start,$i);
			$month_arr.=date("Y-M",strtotime($next_month)).',';
		}
	 	$month_arr_id=rtrim($month_arr,',');
	if($tot_rows>0)
	{
	?>
		
         <script>
			var mon_week ='<? echo $month_arr_id; ?>';
			var mon_week=mon_week.split(",");
			var order_smv=document.getElementById('total_order_smv').innerHTML;
		for (var k=0; k<mon_week.length; k++)
		{
			var mon_data=document.getElementById('summary_footer_td_'+mon_week[k]).innerHTML;
			var mon_arr=mon_data.split("$");
			var mon_qnty=number_format(mon_arr[0],0);
			var mon_amt=number_format(mon_arr[1],0);
			var avg_order_smv=number_format(mon_arr[2],2);
			
			var avg_rate=(number_format(mon_arr[1],0,'.','')/number_format(mon_arr[0],0,'.',''));
			//alert(mon_amt);
			document.getElementById('summary_header_td_qty_'+mon_week[k]).innerHTML=mon_qnty;
			document.getElementById('summary_header_td_val_'+mon_week[k]).innerHTML='$'+mon_amt;
			
			document.getElementById('summary_header_td_rate_'+mon_week[k]).innerHTML='FOB:'+number_format(avg_rate,2);
			document.getElementById('summary_header_td_smv_'+mon_week[k]).innerHTML='SMV:'+number_format(avg_order_smv,2);
		}
		var yarn_req_qty=document.getElementById('td_yarn_req_march').innerHTML;
		var yarnreq_qty=number_format(yarn_req_qty,0);
		document.getElementById('yarn_summary_tot').innerHTML=yarnreq_qty;
		</script>
        <?
	} 
		?>
         
        <div style="width:<? echo $td_width+20;?>px">
		<fieldset style="width:100%;">	
			<table width="<? echo $td_width+20;?>px">
				<tr class="form_caption" style="display:none">
					<td align="center" style="margin-left:400px" colspan="<? echo $rowcount+25; ?>">Order Booking Status</td>
				</tr>
				<tr class="form_caption" align="center" style="display:none">
					<td colspan="<? echo $rowcount+26; ?>" align="center"><? //echo $company_library[$company_name]; ?></td>
				</tr>
			</table>
			<table style="font-size:9px" class="rpt_table" width="<? echo $td_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
                  <tr>
                    <th  width="" colspan="11" rowspan="2">&nbsp;</th>
                     <th  rowspan="2" id="yarn_summary_tot"></th>
                     <th  width="" colspan="14" rowspan="2">&nbsp;</th>
                     <?
					foreach($week_counter_header as $mon_key=>$week)
					{
					?>
					<th   align="center" colspan="<? echo count($week);?>">
					<? 
					echo $mon_key;
					?>
					</th>
					<?
					}
            ?>
                   
                </tr>
                  <tr>
                     <?
					foreach($week_counter_header as $mon_key=>$week_val)
					{
					?>
					<th width="60"  style="font-size:0.836em;"   align="center" colspan="<? echo count($week_val)?>">
                           
                                <b id="summary_header_td_qty_<? echo $mon_key ;?>"  style="text-align:left;font-size: 0.795em;"></b> &nbsp;
                                <b id="summary_header_td_val_<? echo $mon_key ;?>" style="text-align: right;font-size: 0.795em;">  </b> &nbsp;
                                <b id="summary_header_td_rate_<? echo $mon_key ;?>"  style="text-align: right;font-size: 0.795em;"></b> 
                             	<b id="summary_header_td_smv_<? echo $mon_key ;?>"  title="Avg smv" style="text-align: right;font-size: 0.795em;"></b>
					</th>
					<?
					}
            ?>
                   
                </tr>
                <tr style="font-size:9px">
					<th width="30">SL</th>
                    <th width="55">Season</th>
                    <th width="100">BH Mer.</th>
					<th width="140">FFL Mer.</th>
                    <th width="55">Job No</th>
					<th width="100">Style Ref</th>
                    <th width="60">Comp%</th>
                    <th width="120">Comp Name</th>
                    <th width="120">Const.</th>
                    <th width="50">GSM</th>
					<th width="120">Y/C</th>
                    <th width="70">Yarn Qty</th>
					<th width="60">Status</th>
					<th width="45">OPD</th>
					<th width="40">OPD Date</th>
					<th width="45">TOD</th>
                    <th width="70">Ord Qty</th>
					<th width="100">Order No</th>
                    <th width="40">Dept </th>
					<th width="40">Lead Time</th>
					<th width="35">Unit Price</th>
					<th width="90">Value</th>
					<th width="40">SMV/ Pcs</th>
                    <th width="60">Total SMV</th>
                    <th width="60">Delv Qty</th>
                    <th width="60">Delv.Val</th>
                     <?
					//foreach($week_for_header as $week_key)
					foreach($week_counter_header as $mon_key=>$week)
					{
						foreach($week as $week_key)
						{
							$start_week_date=$week_start_day[$week_key][week_start_day];
							$end_week_date=$week_end_day[$week_key][week_end_day];
							
							if($db_type==2)
							{
								$fisrtday=date("D",strtotime(change_date_format($start_week_date,"dd-mm-yyyy","-")));
								$lastday=date("D",strtotime(change_date_format($end_week_date,"dd-mm-yyyy","-")));
							}
							else
							{
								$fisrtday=date("D",strtotime(change_date_format($start_week_date,"dd-mm-yyyy")));
								$lastday=date("D",strtotime(change_date_format($end_week_date,"dd-mm-yyyy")));
							}
							$weekstart_date=explode("-",date("d-M",strtotime($start_week_date)));
							$weekstart_date=$weekstart_date[0].'/'.$weekstart_date[1];
							
						?>
						<th title="<? echo change_date_format($start_week_date,"dd-mm-yyyy","-").'('.$fisrtday.')'." To ".change_date_format($end_week_date,"dd-mm-yyyy","-").'('.$lastday.')'; ?>" width="45"  align="center">
						
						<? 
						echo 'W'.$week_key.'<br/>'.$weekstart_date;
						?>
						</th>
						<?
						}
					}
            ?>
                   
                </tr>    
				</thead>
			</table>
			<div style="width:<? echo $td_width+20;?>px; max-height:245px; overflow-y:scroll" id="scroll_body">
			<table style="font-size:9px" class="rpt_table" width="<? echo $td_width;?>px" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body_marchant">
	<?
	
			$condition= new condition();
			 $condition->company_name("=$cbo_company_name");
			 if(str_replace("'","",$cbo_buyer_name)>0)
			 {
				  $condition->buyer_name("=$cbo_buyer_name");
			 }
			 if(str_replace("'","",$txt_job_no) !='')
			 {
				  $condition->job_no_prefix_num("in($txt_job_no)");
			 }
			
			 if(str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
			 {
			 $condition->pub_shipment_date(" between '$start_date' and '$end_date'");
			 }
				
			 if(str_replace("'","",$txt_order_no)!='')
			 {
				$condition->po_number("=$txt_order_no"); 
			 }
			 $condition->init();
        	 $yarn= new yarn($condition);
		  //echo $yarn->getQuery(); die;
			 $yarn_req_qty_arr=$yarn->getOrderWiseYarnQtyArray();
			//print_r($yarn_req_qty_arr);
				$i=1;$total_order_qty=0;$total_order_value=0;$total_week_qty=0;$total_yarn_req_qty=0;$total_order_smv=0;$total_ex_fact_val=0;$total_ex_fact_qty=0;
				foreach($marchant_wise_arr as $marchant_key=>$marchant_data)
				{  
				   	foreach($marchant_data as $status_key=>$status_val)
					{
						
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";   
						  //echo $status_val['po_qty'].'sds';
				$po_received_date=$status_val['po_received_date'];
				$po_id=rtrim($status_val['po_id'],',');
				$po_id=explode(",",$po_id);
				$percent_one=$wo_fab_data_arr[$marchant_key][$status_key]['percent_one'];
				$percent_two=$wo_fab_data_arr[$marchant_key][$status_key]['percent_two'];
				if($percent_one!=0 && $percent_two!=0)
				{
					//if($percent_two!=0)
					$percent_all=$percent_one.'/'.$percent_two;
				}
				else if($percent_one!=0 && $percent_two==0)
				{
					//if($percent_two!=0)
					$percent_all=$percent_one;
				}
				else
				{
					$percent_all='';	
				}
				$copm_two_id=$wo_fab_data_arr[$marchant_key][$status_key]['copm_two_id'];
				$copm_one_id=$wo_fab_data_arr[$marchant_key][$status_key]['copm_one_id'];
				if($copm_one_id!=0 && $copm_two_id!=0)
				{ 
					$copm_one_name=$composition[$copm_one_id].'/'.$composition[$copm_two_id];
				}
				else if($copm_one_id!=0 && $copm_two_id==0)
				{ 
					$copm_one_name=$composition[$copm_one_id];
				
				}
				else 
				{
					$copm_one_name='';
				}
				
				$construction=rtrim($wo_fab_data_arr[$marchant_key][$status_key]['construction'],',');
				$construction=implode(",",array_unique(explode(",", $construction)));
				$gsm_weight=rtrim($wo_fab_data_arr[$marchant_key][$status_key]['gsm_weight'],',');
				$gsm_weight=implode(",",array_unique(explode(",", $gsm_weight)));
				$count_id=rtrim($wo_fab_count_data_arr[$marchant_key][$status_key]['count_id'],',');
				 $count_ids=array_unique(explode(",", $count_id));
				 $yarn_count_value="";
				foreach($count_ids as $val)
				{
						if($yarn_count_value=="") $yarn_count_value=$lib_yarn_count_arr[$val]; else $yarn_count_value.=", ".$lib_yarn_count_arr[$val];
				}
				 $seasion_ids=rtrim($status_val['season'],',');
				 //$seasion_ids=ltrim(',',$seasion_ids);
				$seasion_ids=array_unique(explode(",",$seasion_ids));
				$seasion_id=$seasion_ids[0];
				
				$job_no=rtrim($status_val['job_no'],',');
				$style=rtrim($status_val['style'],',');
				$po_no=rtrim($status_val['po_no'],',');
				$po_id=rtrim($status_val['po_id'],',');
				$bh_mers=rtrim($status_val['bh_mer'],',');
				//$bh_mer=ltrim(',',$status_val['bh_mer']);
				$bh_mers=array_unique(explode(",",$bh_mers));
				$bh_mer=$bh_mers[0];
				//$bh_mer=implode(",",array_unique(explode(",",$bh_mer)));
				$po_received_date=rtrim($status_val['po_received_date'],',');
				$job_no=implode(",",array_unique(explode(",",$job_no)));
				$style=implode(",",array_unique(explode(",",$style)));
				$po_no=implode(",",array_unique(explode(",",$po_no)));
				$po_id=array_unique(explode(",",$po_id));
				$conf_ship_date=rtrim($wo_color_data_arr2[$marchant_key][$status_key]['shipdate'],',');
				$conf_country_ship_date=array_unique(explode(",",$conf_ship_date));
				$yarn_req=0;
				foreach($po_id as $pid)
				{
				$yarn_req+=$yarn_req_qty_arr[$pid];
				}
				//$seasion_cond=implode(",",array_unique(explode(",",$seasion_id)));
				/*$seasion_cond="";
				foreach($seasion_id as $sid)
				{
					if($seasion_cond=="") $seasion_cond=$lib_season_name_arr[$sid];else $seasion_cond.=",".$lib_season_name_arr[$sid];
				}*/
				$po_received_date=array_unique(explode(",",$po_received_date));
				$opd_week="";$opd_recv_date="";$opd_recv_date_full="";
				//$lead_time=0;
				foreach($po_received_date as $op_id)
				{
					if($opd_week=="") $opd_week=$no_of_week_for_header[$op_id];else $opd_week.=", ".$no_of_week_for_header[$op_id];
					if($opd_recv_date=="") $opd_recv_date=date("d-M",strtotime($op_id));else $opd_recv_date.=",".date("d-M",strtotime($op_id));
					if($opd_recv_date_full=="") $opd_recv_date_full=date("d-M-y",strtotime($op_id));else $opd_recv_date_full.=",".date("d-M-y",strtotime($op_id));
				}
				$po_orgi_shipdate=rtrim($status_val['orgi_shipdate'],',');
				$po_orgi_shipdate=array_unique(explode(",",$po_orgi_shipdate));
				$tod_weeks="";$tod_recv_date="";
				foreach($po_orgi_shipdate as $orgi_id)
				{
					//echo $orgi_id;
					if($tod_weeks=="") $tod_weeks=$no_of_week_for_header[$orgi_id];else $tod_weeks.=",".$no_of_week_for_header[$orgi_id];
					//if($tod_recv_date=="") $tod_recv_date=change_date_format($ogi_id);else $tod_recv_date.=", ".change_date_format($ogi_id);
				}
				$tod_weeks=implode(",",array_unique(explode(",",$tod_weeks)));
				$lead_time=$status_val['lead_day'];
				$opd_week=implode(",",array_unique(explode(", ",$opd_week)));
				$opd_recv_date=implode(",",array_unique(explode(", ",$opd_recv_date)));
				$prod_cond=rtrim($status_val['pcode'],',');
				$prod_cond=implode(",",array_unique(explode(",",$prod_cond)));
				
				$orgi_shipdate=rtrim($status_val['orgi_shipdate'],',');
				$c_ship_date=rtrim($status_val['c_ship_date'],',');
				//$qnty_array2=array();
				if($status_key==1) //Confirm $conf_country_ship_date
				{
					$c_ship_date_con=array_unique(explode(",",$c_ship_date));$qnty_array_dd=array();
					$c_week_qty=0;
					foreach($c_ship_date_con as $c_date)
					{
								if($db_type==0) $c_date_con=date("d-m-y",strtotime($c_date));
								else $c_date_con=date("d-M-y",strtotime($c_date));
								$c_week=$no_of_week_for_header_calc[$c_date_con];
								//if($c_week!='')
								//{
								
									$c_week_qty=$week_wise_order_qty[$marchant_key][$status_key][$c_week]['po_quantity'];
										//echo $c_week.'=='.$c_week_qty.'hj';
								//}
								$qnty_array_dd[$c_week]=$c_week_qty;
					}
					//print_r($qnty_array_dd);
						$tot_order_qty=0;$tot_order_qty_arr=array();
						foreach($week_counter_header as $mon_key=>$week)
						{
							//$order_qty_arr=array();
							foreach($week as $week_key)
							{
								 $tot_order_qty=$qnty_array_dd[$week_key];
								  $order_qty_arr[$week_key]=$tot_order_qty;
								  $tot_order_qty_arr[$marchant_key]+=$tot_order_qty;
							}
						}
						
				}
				else 
				{
						$orgi_ship_date=rtrim($status_val['orgi_shipdate'],',');
						$proj_orgi_shipdate=array_unique(explode(",",$orgi_ship_date));
						$qnty_array_mm=array();$proj_po_qty=0;
						//$qnty_arr=distribute_projection($tod_week, $tot_proj_po_qty);
					
						foreach($proj_orgi_shipdate as $tod_key)
						{
							$tod_week=$no_of_week_for_header[$tod_key];
							$proj_po_qty=$delmarchant_wise_arr_qty[$marchant_key][$status_key][$tod_week]['po_qty'];
						
							$qnty_arr=distribute_projection($tod_week, $proj_po_qty);
							foreach($qnty_arr as $key=>$val)
							{
								$qnty_array_mm[$key]+=$val;
							}
						}
					
							$order_qty=0;$tot_order_qty_arr=array();
							foreach($week_counter_header as $mon_key=>$week)
							{
								foreach($week as $week_key)
								{
									$order_qty=$qnty_array_mm[$week_key];
									 $order_qty_arr[$week_key]=$order_qty;
									  $tot_order_qty_arr[$marchant_key]+=$order_qty;
								}
							}
				}
				
				$tot_po_qty=$tot_order_qty_arr[$marchant_key];
				$po_value=$status_val['po_val'];
				$unit_price=$po_value/$tot_po_qty;
				//echo $team_member_arr[$marchant_key].'='.$status_val['po_val'].',';
				$weeks_id='';
				if($status_key==1) //Confirm
				{
					foreach($conf_country_ship_date as $c_date)
					{
						$week_qty_check=$delmarchant_wise_arr_qty[$marchant_key][$status_key][$tod_week]['po_qty'];//$week_wise_order_qty_arr[$marchant_key][$status_key][$no_of_week_for_header[$c_date]]['po_quantity'];
						if($week_qty_check>0)
						{
							if($weeks_id=='') $weeks_id=$no_of_week_for_header[$c_date];else $weeks_id.=",".$no_of_week_for_header[$c_date];
						}
					}
				}
					$todweeks=array_unique(explode(",",$tod_weeks));
					$weeksid=array_unique(explode(",",$weeks_id));
					 $weekss=array_merge($weeksid,$todweeks);
					 $weeksIds='';
					 foreach($weekss as $w_id)
					 {
						if($weeksIds=='')   $weeksIds=$w_id;else  $weeksIds.=",".$w_id;
					 }
					
					// echo $weeksIds;
						$job_no_row=count(array_unique(explode(",",$job_no)));
						if($job_no_row>1)
						{
							 $view_button="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$marchant_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
						}
						else
						{
							$view_button=$job_no;	
						}
						$style_row=count(array_unique(explode(",",$style)));
						if($style_row>1)
						{
							 $view_button_style="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$marchant_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
						}
						else
						{
							$view_button_style=$style;	
						}
						
						$tod_weeks_row=count(array_unique(explode(",",$tod_weeks)));
						if($tod_weeks_row>1)
						{
							 $view_button_tod="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$marchant_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
						}
						else
						{
							$view_button_tod=$tod_weeks;	
						}
						$opd_week_row=count(array_unique(explode(",",$opd_week)));
						$opd_recv_date_row=count(array_unique(explode(",",$opd_recv_date)));
						if($opd_week_row>1)
						{
							 $view_button_opd="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$marchant_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
						}
						else
						{
							$view_button_opd=$opd_week;	
						}
						if($opd_recv_date_row>1)
						{
							 $view_button_opd_date="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$marchant_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
						}
						else
						{
							$view_button_opd_date=$opd_recv_date;	
						}
						//echo $po_no;
						$po_no_row=count(array_unique(explode(",",$po_no)));
						if($po_no_row>1)
						{
							 $view_button_po="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$marchant_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
						}
						else
						{
							$view_button_po=$po_no;	
						}
						
						$bh_mer_row=count(array_unique(explode(",",$bh_mer)));
						if($bh_mer_row>1)
						{
							 $view_button_bh="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$marchant_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
						}
						else
						{
							$view_button_bh=$bh_mer;	
						}
						
						$seasion_cond_row=count(array_unique(explode(",",$seasion_cond)));
						if($seasion_cond_row>1)
						{
							 $view_button_season="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$marchant_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
						}
						else
						{
							$view_button_season=$seasion_cond;	
						}
						
						$prod_cond_row=count(array_unique(explode(",",$prod_cond)));
						if($prod_cond_row>1)
						{
							 $view_button_dept="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$marchant_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
						}
						else
						{
							$view_button_dept=$prod_cond;	
						}
						$yarn_count_value_row=count(array_unique(explode(",",$yarn_count_value)));
						if($yarn_count_value_row>1)
						{
							 $view_button_count="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$marchant_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
						}
						else
						{
							$view_button_count=$yarn_count_value;	
						}
						$gsm_weight_row=count(array_unique(explode(",",$gsm_weight)));
						if($gsm_weight_row>1)
						{
							 $view_button_gsm="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$marchant_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
						}
						else
						{
							$view_button_gsm=$gsm_weight;	
						}
						
						$construction_row=count(array_unique(explode(",",$construction)));
						if($construction_row>1)
						{
							 $view_button_const="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$marchant_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
						}
						else
						{
							$view_button_const=$construction;	
						}
						$copm_one_name_row=count(array_unique(explode(",",$copm_one_name)));
						if($copm_one_name_row>1)
						{
							 $view_button_comp="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$marchant_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\">View<a/>";
						}
						else
						{
							$view_button_comp=$copm_one_name;	
						}
				?>
                <tr style="font-size:9px" align="center" bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
					<td width="30"><? echo $i; ?></td>
                    <td width="55" style="word-wrap:break-word; word-break: break-all;"><? echo $seasion_id;?></td>
                    <td width="100" style="word-wrap:break-word; word-break: break-all;" ><? echo $bh_mer; ?></td> 
                    <td width="140" style="word-wrap:break-word; word-break: break-all;" ><? echo $team_member_arr[$marchant_key]; ?></td> 
                    <td width="55" style="word-wrap:break-word; word-break: break-all;">
              				 <? echo $view_button?>
                     </td>
                    <td  width="100" style="word-wrap:break-word; word-break: break-all;">  
                     <? echo $view_button_style?>
                    </td>
                    <td width="60" style="word-wrap:break-word; word-break: break-all;"><? echo $percent_all; ?></td>
                    <td  width="120" style="word-wrap:break-word; word-break: break-all;"> <? echo $view_button_comp; ?></td>
                    <td  width="120" style="word-wrap:break-word; word-break: break-all;"> <? echo $view_button_const;?></td>
                    <td width="50"  style="word-wrap:break-word; word-break: break-all;"><? echo $view_button_gsm; ?></td>
					<td  width="120" style="word-wrap:break-word; word-break: break-all;"> <? echo $view_button_count; ?></td>
                    <td  width="70" style="word-wrap:break-word; word-break: break-all; text-align:right">
					<?
							echo number_format($yarn_req);
					?>
                    </td>
					<td  width="60" style="word-wrap:break-word; word-break: break-all;">
					<?
					
					if($status_key==2)
					{
						echo "Booked";	
					}
					else
					{
						echo "Placed";
					}
					?> 
                    </td>
					<td  width="45" style="word-wrap:break-word; word-break: break-all;">
					<?
					echo $view_button_opd;
					?>
                    </td>
					<td  width="40" title="<? echo $opd_recv_date_full;?>" style="word-wrap:break-word; word-break: break-all;">
                    
					<?
					if($opd_recv_date!="" && $opd_recv_date !="0000-00-00" && $opd_recv_date!="0")
					{
						$opd_recv_date=$opd_recv_date;
					}
					echo $view_button_opd_date;
					?>
                     
                    </td>
					<td width="45" title="Orgi Ship Date<? echo $orgi_shipdate;?>" style="word-wrap:break-word; word-break: break-all; text-align: center">
						<? echo $view_button_tod; ?>
                    </td>
                    <td  width="70" style="word-wrap:break-word; word-break: break-all; text-align:right">
					<? echo number_format($tot_po_qty);?>
                    </td>
					<td  width="100" style="word-wrap:break-word; word-break: break-all; text-align: center">
					<? echo $view_button_po; ?>
                    </td>
                    <td  width="40" style="word-wrap:break-word; word-break: break-all; text-align:left" >
					<? echo $view_button_dept;?>
                    </td>
					<td  width="40" title="TOD-OPD" style="word-wrap:break-word; word-break: break-all; text-align: center">
					<? //echo $lead_time; ?>
                    </td>
					<td  width="35" title="PO Value/Po Qty" style="word-wrap:break-word; word-break: break-all; text-align:right">
					<? echo number_format($unit_price,2); ?>
                    </td>
                   
					<td width="90" title="Po Qty*Unit Price" style="word-wrap:break-word; word-break: break-all;text-align:right">
                   <? echo number_format($po_value,0);
				  	 $set_smv=number_format($status_val['set_smv'],2);
				    ?>
                    </td>
					<td width="40" title="Set SMV/Set Ratio" style="word-wrap:break-word; word-break: break-all;">
					<? 
					$smv_rate=0;
					$smv_rate=$set_smv/$status_val['set_ratio'];
					echo number_format($smv_rate,2); ?>
                    </td>
					
                    <td  width="60" title="Set SMV Per Pcs*Order Qty" style="word-wrap:break-word; word-break: break-all;text-align:right">
					<? echo number_format(($set_smv/$status_val['set_ratio'])*$tot_po_qty); ?>
                    </td>
                     <td  width="60" style="word-wrap:break-word; word-break: break-all;text-align:right">
					<? echo number_format($exfactory_data_array[$marchant_key][$status_key][ex_qnty]); ?>
                    </td>
                    <td width="60" title="Ex-Fact Qty*Unit Price" style="word-wrap:break-word; word-break: break-all;text-align:right">
                    <? echo  number_format($exfactory_data_array[$marchant_key][$status_key][ex_qnty]*$unit_price,0);
					 ?>
                    </td>
                    <?
					/*$qnty_array=array();
					if($status_key==1) //Confirm $conf_country_ship_date
					{
							$c_ship_date_con=array_unique(explode(",",$c_ship_date));
							foreach($c_ship_date_con as $c_date)
							{
								if($db_type==0) $c_date_con=date("d-m-y",strtotime($c_date));
								else $c_date_con=date("d-M-y",strtotime($c_date));
								//$qnty_array[$no_of_week_for_header_calc[$c_date_con]]=$week_wise_order_qty[$marchant_key][$status_key][$no_of_week_for_header_calc[$c_date_con]]['po_quantity'];
							}
					}
					else
					{
						foreach($po_orgi_shipdate as $tod_key)
						{
						
							//$qnty_array=distribute_projection($no_of_week_for_header[$tod_key], $status_val['po_qty']);
						}
					}*/
					$week_qty=0;
					$week_check=array();
                   	foreach($week_counter_header as $mon_key=>$week)
					{
						foreach($week as $week_key)
						{
						?>
						<td    title="<? echo $conf_ship_date;?>" style="word-wrap:break-word; word-break: break-all;"  width="45" align="right">
						<? 
							 	$week_qty=$order_qty_arr[$week_key];
								if($week_qty>0) echo number_format($week_qty,0);else echo ' ';
								$tot_week_qty[$mon_key][$week_key]+=$week_qty;
								$tot_week_amount[$mon_key][$week_key]+=$week_qty*$unit_price;
								$smv_avg=0;
								$smv_avg=$smv_rate*$week_qty;
								$month_smv_arr[$mon_key]+=$smv_avg;
						?>
						</td>
						<?
						}
					}
					?>
                    </tr>
                    <?
					$total_order_qty+=$tot_po_qty;
					$total_yarn_req_qty+=$yarn_req;	
					$total_ex_fact_qty+=$exfactory_data_array[$marchant_key][$status_key][ex_qnty];
					$total_ex_fact_val+=$exfactory_data_array[$marchant_key][$status_key][ex_qnty]*$unit_price;
					$total_order_value+=$tot_po_qty*$unit_price;
					$total_order_smv+=($set_smv/$status_val['set_ratio'])*$tot_po_qty;
					$total_week_qty+=$week_qty;
					//=====================================================================================================================
				$i++;
						}
				}
				?>
				</table>
				</div>
                <table style="font-size:9px" class="tbl_bottom" width="<? echo $td_width;?>px" cellpadding="0" cellspacing="0" id="report_table_footer"  border="1" rules="all" >
					<tr>
					<td width="30">&nbsp;</td>
                    <td width="55">&nbsp;</td>
                    <td width="100">&nbsp;</td>
					<td width="140">&nbsp;</td>
                    <td width="55">&nbsp;</td>
					<td width="100">&nbsp;</td>
                    <td width="60">&nbsp;</td>
                    <td width="120">&nbsp;</td>
                    <td width="120">&nbsp;</td>
                    <td width="50">&nbsp;</td>
					<td width="120">&nbsp;</td>
                    <td width="70"  style="word-wrap:break-word; word-break: break-all; text-align:right; font-size:smaller"  id="td_yarn_req_march"><? echo $total_yarn_req_qty;?></td>
					<td width="60">&nbsp;</td>
					<td width="45">&nbsp;</td>
					<td width="40">&nbsp;</td>
					<td width="45">&nbsp;</td>
                    <td width="70" style="word-wrap:break-word; word-break: break-all; font-size: smaller" id="td_po_qty_march"><? echo $total_order_qty;?></td>
					<td width="100">&nbsp;</td>
                    <td width="40">&nbsp; </td>
					<td width="40">&nbsp;</td>
					<td width="35"><? echo number_format($total_order_value/$total_order_qty,2);?></td>
					<td width="90" id="td_po_val_march" style="word-wrap:break-word; word-break: break-all; font-size:smaller"><? echo number_format($total_order_value);?></td>
					<td width="40"><? echo number_format($total_order_smv/$total_order_qty,2);?></td>
                    <td width="60"  style="word-wrap:break-word; word-break: break-all; font-size:smaller" id="total_order_smv"><? echo number_format($total_order_smv,0);?></td>
                    <td width="60"  style="word-wrap:break-word; word-break: break-all; font-size:smaller"  id="total_order_exfc_qty"><? echo number_format($total_ex_fact_qty,0);?></td>
                    <td width="60"  style="word-wrap:break-word; word-break: break-all; font-size:smaller"  id="total_order_exfc_val"><? echo number_format($total_ex_fact_val,0);?></td>
                     <?
					//foreach($week_for_header as $week_key)
					$total_week_qty=0; $total_week_amt=0; 
					foreach($week_counter_header as $mon_key=>$week)
					{
						foreach($week as $week_key)
						{
						?>
						<td width="45"  style="word-wrap:break-word; word-break: break-all; font-size:smaller"  align="center">
						
						<? 
						$total_week_qty=$tot_week_qty[$mon_key][$week_key];
						$total_week_amt=$tot_week_amount[$mon_key][$week_key];
						echo number_format($total_week_qty,0);
						$tot_mon_qty[$mon_key]+=$total_week_qty;
						$tot_mon_amount[$mon_key]+=$total_week_amt;
						?>
						</td>
						<?
						
						}
					}
            ?>
					</tr>
                     <tr style="display:none">
					<td colspan="26">&nbsp;</td>
                     <?
					 $m=1;
					foreach($week_counter_header as $mon_key=>$week)
					{
						?>
						<td width="" id="summary_footer_td_<? echo $mon_key?>" colspan="<? echo count($week);?>" title="<? echo $tot_week_amount[$mon_key][$week_key];?>" style="word-wrap:break-word; word-break: break-all;"   align="center">
						
						<? 
						$tot_smv=0;
						$tot_smv=$month_smv_arr[$mon_key]/$tot_mon_qty[$mon_key];
						
						$head_td_data=$tot_mon_qty[$mon_key].'$'.$tot_mon_amount[$mon_key].'$'.$tot_smv;
						echo $head_td_data;
						?>
						</td>
						<?
					}
            ?>
                    </tr>
				</table>
			</fieldset>
		</div>
         <style>
			@font-face {
   				 font-family:"Arial Narrow"; font-size:11px;
   				 
			}
			TD{font-family:"Arial Narrow";font-size:11px;}
			TH{font-family:"Arial Narrow";font-size:11px;}
			
			.rpt_table TD{font-family: "Arial Narrow";font-size:11px;}
			.rpt_table TH{font-family:"Arial Narrow";font-size:11px;}
			
		</style>
	<?
	}
//===========================================================================================================================================================
	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data****$filename****$tot_rows****$cbo_search_type";
	exit();	
	
} //Merchand End

else if($action=="report_generate_bh_mer")
{
 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company_name=str_replace("'","",$cbo_company_name);
	$cbo_search_type=str_replace("'","",$cbo_search_type);
	$cbo_bh_mer_name=str_replace("'","",$cbo_bh_mer_name);
	$txt_yarn_count=str_replace("'","",$txt_yarn_count);
	if($txt_yarn_count!='') $yarn_count_con="and yarn_count Like '%$txt_yarn_count%' ";else $yarn_count_con='';
	$buyer_id_cond="";
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{
				$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else
			{
				$buyer_id_cond="";
			}
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}
	

	
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_job_no=trim($txt_job_no);
	if($txt_job_no !="" || $txt_job_no !=0)
	{
		//$year = substr(str_replace("'","",$cbo_year_selection), -2); 
		//$job_no=$company_library[$company_name]."-".$year."-".str_pad($txt_job_no, 5, 0, STR_PAD_LEFT);
		$jobcond="and a.job_no_prefix_num in (".$txt_job_no.")";
	}
	else
	{
		$jobcond="";	
	}
	
	//echo $jobcond.'fd';
	$date_cond='';
	
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			$start_date=(str_replace("'","",$txt_date_from));
			$end_date=(str_replace("'","",$txt_date_to));
			
			$date_cond="and b.pub_shipment_date between '$start_date' and '$end_date'";
		}
	
	
	if(str_replace("'","",$txt_style_ref)!="") $style_ref_cond=" and a.style_ref_no like '%".str_replace("'","",$txt_style_ref)."%'"; else $style_ref_cond="";
	if(str_replace("'","",$txt_order_no)!="")
	{
		$ordercond=" and b.po_number like '%".str_replace("'","",$txt_order_no)."%'"; 
	}
	else
	{
		$ordercond="";
	}
	
	$team_cond="";
	if(str_replace("'","",$cbo_team_name)!=0) $team_cond=" and a.team_leader=".str_replace("'","",$cbo_team_name)."  ";
	if(str_replace("'","",$cbo_team_member)!=0) $team_cond.=" and a.dealing_marchant=".str_replace("'","",$cbo_team_member)."  ";
	$cbo_bh_mer_name=str_replace("0","",$cbo_bh_mer_name);
	if($cbo_bh_mer_name!=0 || $cbo_bh_mer_name!='') $bh_mer_name_con="and a.bh_merchant='$cbo_bh_mer_name' ";else $bh_mer_name_con='';
	 $cbo_year_selection=str_replace("'","",$cbo_year_selection);
	
	$start_ddd= strtotime(change_date_format($start_date,"dd-mm-yyyy","-"));
	$start_ddd=date("Y-m-d",$start_ddd);
	$start_end_date= strtotime(change_date_format($end_date,"dd-mm-yyyy","-"));
	$start_end_date=date("Y-m-d",$start_end_date);
	 //$dd= date('d-m-Y',strtotime($start_date));
	//$start_date_week= date($dd, time() - 1296000);//$start_date;
	//echo $last_day_this_month  = date($start_date_week, time() -1814400); //date('t-m-Y');
	
	$dateee = strtotime($start_ddd);
	$start_end_date = strtotime($start_end_date);
	
	$date_start = date("Y-m-d",strtotime("-28 day", $dateee));
	$date_end = date("Y-m-d",strtotime("+28 day", $start_end_date));
	//echo $date_end;
	//$end_date_week=  addday("$end_date", time() -1814400);//$start_date;//$start_date;
	//$weekarr=week_of_year($cbo_year_selection,"Monday");
	if($db_type==0)
	{
		$date_start= change_date_format($date_start,"yyyy-mm-dd");
		$date_end= change_date_format($date_end,"yyyy-mm-dd");
	}
	else
	{
		$date_start= change_date_format($date_start,"dd-mm-yyyy","-",1);
		$date_end= change_date_format($date_end,"dd-mm-yyyy","-",1);
	}
	
	
	//echo "select week_date,week from week_of_year where week_date between '$date_start' and  '$date_end' order by week_date asc";die;
	// date("d-m-Y", time() - 1296000);
	$week_for_arr=array();$no_of_week_for_header=array();
	$sql_week_header=sql_select("select week_date,week from week_of_year where week_date between '$date_start' and  '$date_end' order by week_date asc");
	$week_check_head=array();
	foreach ($sql_week_header as $row_week_header)
	{
		if($week_check_head[$row_week_header[csf("week")]]=='')
		{
			$week_counter_header[date("Y-M",strtotime($row_week_header[csf("week_date")]))][$row_week_header[csf("week")]]=$row_week_header[csf("week")];
			$week_check_head[$row_week_header[csf("week")]]=$week_counter_header[date("Y-M",strtotime($row_week_header[csf("week_date")]))][$row_week_header[csf("week")]];
		}
			
		$tmp=add_date($row_week_header[csf("week_date")],-1);
		if($db_type==0) $tmp_cond=date("d-m-y",strtotime($tmp));
		else $tmp_cond=date("d-M-y",strtotime($tmp));
		
		$no_of_week_for_header_calc[$tmp_cond]=$row_week_header[csf("week")];	
		$no_of_week_for_header[$row_week_header[csf("week_date")]]=$row_week_header[csf("week")];
		$test[$row_week_header[csf("week")]]=$row_week_header[csf("week")];
	}
	unset($sql_week_header);
	$week_start_day=array();
	$week_end_day=array();$week_day_arr=array();
	//week_day
	$sql_week_start_end_date=sql_select("select week,min(week_date) as week_start_day, Max(week_date) as week_end_day from week_of_year where week_date between '$date_start' and  '$date_end' group by week");
	foreach ($sql_week_start_end_date as $row_week)
	{
		$week_start_day[$row_week[csf("week")]][week_start_day]=$row_week[csf("week_start_day")];
		$week_end_day[$row_week[csf("week")]][week_end_day]=$row_week[csf("week_end_day")];
		//$week_day_arr[$row_week[csf("week")]][$row_week[csf("week_start_day")]][week_first_day]=$row_week[csf("min_week_day")];
		$week_day_arr[$row_week[csf("week_end_day")]]['week_last_day']=$row_week[csf("last_week_day")];
	}
	unset($sql_week_start_end_date);
	//print_r($week_day_arr);
	function distribute_projection( $base_week, $qnty)
	{
		//5. Distribution ratios are: Base week 52%, Immediate before 24%, Week before immediate week 20%, Immediate week after base week 2%, next 2 weeks as 1%
		global $test;
		 
		$tmpbase_week=$test[$base_week-3];
		$k=0;
		for($i=0; $i<6; $i++)
		{
			$tmpbase_week++;
			if( $test[$tmpbase_week]=='') { $tmpbase_week=1; $k=0; }
			
			
			if($i==0) $distr_arr[$tmpbase_week]=($qnty*20)/100;
			if($i==1) $distr_arr[$tmpbase_week]=($qnty*24)/100;
			if($i==2) $distr_arr[$tmpbase_week]=($qnty*52)/100;
			if($i==3) $distr_arr[$tmpbase_week]=($qnty*2)/100;
			if($i==4) $distr_arr[$tmpbase_week]=($qnty*1)/100;
			if($i==5) $distr_arr[$tmpbase_week]=($qnty*1)/100;
			$k++;
			
		}
		 
		return $distr_arr;
		/*5000
		b=52
		b-1=21
		b-2=15
		b+1=1
		b+2=1*/
	}
//echo "<per>";
 //print_r(distribute_projection("2016-Dec",51, 5000)); die;


	
	if($template==1)
	{
	$po_wise_buyer=array();
	$buyer_wise_data=array();
	$po_id_arr=array();
	$wo_color_data_arr2=array();	
	//b.is_confirmed,min(c.country_ship_date) as country_ship_date,sum(c.order_quantity) as order_quantity,c.country_ship_date as  conf_country_ship_date
	$count_data=sql_select("select yarn_count,id from  lib_yarn_count  where status_active=1 and is_deleted=0 and yarn_count is not null $yarn_count_con");
	$yarn_id='';
	foreach($count_data as $row)
	{
			if($yarn_id=='') $yarn_id=$row[csf('id')];else $yarn_id.=",".$row[csf('id')];
	}

		$yarn_ids=count(array_unique(explode(",",$yarn_id)));
		$yarn_numIds=chop($yarn_id,','); $yarnIds_cond="";
		if($yarn_id!='' || $yarn_id!=0)
		{
			if($db_type==2 && $yarn_ids>1000)
			{
				$yarnIds_cond=" and (";
				$yIdsArr=array_chunk(explode(",",$yarn_numIds),990);
				//print_r($gate_outIds);
				foreach($yIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$yarnIds_cond.=" d.count_id  in($ids) or ";
				}
				$yarnIds_cond=chop($yarnIds_cond,'or ');
				$yarnIds_cond.=")";
			}
			else
			{
				$yarnIds_cond=" and  d.count_id  in($yarn_id)";
			}
		}
	$yarn_sql_data=("select a.bh_merchant,b.is_confirmed,b.id as po_id,d.count_id,d.copm_one_id,d.copm_two_id,d.percent_one,d.percent_two,sum(d.cons_qnty) as cons_qnty,e.construction,e.gsm_weight,b.shipment_date as orgi_ship_date from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fab_yarn_cost_dtls d,wo_pre_cost_fabric_cost_dtls e where a.job_no=b.job_no_mst  and c.job_no_mst=a.job_no and b.id=c.po_break_down_id and d.job_no=a.job_no and 
	e.id=d.fabric_cost_dtls_id and  c.item_number_id= e.item_number_id and e.job_no=a.job_no and e.body_part_id in(1,20) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1   and a.company_name=$company_name   $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $team_cond $yarnIds_cond group by   b.id ,d.count_id,d.copm_one_id,d.copm_two_id,d.percent_one,d.percent_two,e.construction,e.gsm_weight,a.bh_merchant,b.is_confirmed,b.shipment_date"); 
	$sql_result_y=sql_select($yarn_sql_data);
	$wo_fab_data_arr=array();
	$all_yarn_po_id="";
	foreach($sql_result_y as $row)
		{
			if($row[csf('bh_merchant')]=='') $bh_merchant=0;else $bh_merchant=$row[csf('bh_merchant')];
			if($all_yarn_po_id=="") $all_yarn_po_id=$row[csf("po_id")]; else $all_yarn_po_id.=",".$row[csf("po_id")];
			$tod_week=$no_of_week_for_header[$row[csf('orgi_ship_date')]];
			$wo_fab_data_arr[$bh_merchant][$row[csf('is_confirmed')]]['percent_one']=$row[csf('percent_one')];
			$wo_fab_data_arr[$bh_merchant][$row[csf('is_confirmed')]]['percent_two']=$row[csf('percent_two')];
			$wo_fab_data_arr[$bh_merchant][$row[csf('is_confirmed')]]['copm_two_id']=$row[csf('copm_two_id')];
			$wo_fab_data_arr[$bh_merchant][$row[csf('is_confirmed')]]['copm_one_id']=$row[csf('copm_one_id')];
			$wo_fab_data_arr[$bh_merchant][$row[csf('is_confirmed')]]['construction'].=$row[csf('construction')].',';
			$wo_fab_data_arr[$bh_merchant][$row[csf('is_confirmed')]]['gsm_weight'].=$row[csf('gsm_weight')].',';
			$wo_fab_count_data_arr[$bh_merchant][$row[csf('is_confirmed')]]['count_id'].=$row[csf('count_id')].",";
			//$wo_yarn_data_arr[$row[csf('po_id')]]['cons_qnty']+=$row[csf('cons_qnty')];
		}
		unset($sql_result_y);
		$yarn_po_ids=count(array_unique(explode(",",$all_yarn_po_id)));
		$yarnPo_numIds=chop($all_yarn_po_id,','); $yarnPoIds_cond="";
		if($txt_yarn_count!='')
		{
			if($all_yarn_po_id!='' || $all_yarn_po_id!=0)
			{
				if($db_type==2 && $yarn_po_ids>1000)
				{
					$yarnPoIds_cond=" and (";
					$yPoIdsArr=array_chunk(explode(",",$yarnPo_numIds),990);
					foreach($yPoIdsArr as $ids)
					{
						$ids=implode(",",$ids);
						$yarnPoIds_cond.=" b.id  in($ids) or ";
					}
					$yarnPoIds_cond=chop($yarnPoIds_cond,'or ');
					$yarnPoIds_cond.=")";
				}
				else
				{
					$yarnPoIds_cond=" and  b.id  in($all_yarn_po_id)";
				}
			}
		}
		
		
		 $bhmarchant_wise_arr=array();$bhmarchant_wise_arr_qty=array();
		$sql_data_c=("select b.id as po_id,a.bh_merchant,b.shipment_date as orgi_ship_date,b.is_confirmed,(b.po_quantity*a.total_set_qnty) as po_qty_pcs
		from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $team_cond $bh_mer_name_con order by a.bh_merchant,b.shipment_date");
		$sql_result_c=sql_select($sql_data_c);
		foreach($sql_result_c as $row)
		{
			
					if($row[csf('is_confirmed')]==2)
					{
						if($row[csf('bh_merchant')]=='') $bh_merchant=0;else $bh_merchant=$row[csf('bh_merchant')];
						$tod_week=$no_of_week_for_header[$row[csf('orgi_ship_date')]];
						$bhmarchant_wise_arr_qty[$bh_merchant][$row[csf('is_confirmed')]][$tod_week]['po_qty']+=$row[csf('po_qty_pcs')];
						$bhmarchant_wise_arr[$bh_merchant][$row[csf('is_confirmed')]]['orgi_ship_date'].=$row[csf('orgi_ship_date')].',';
					}
			
		}	
		//print_r($bhmarchant_wise_arr);
		
		
if($db_type==0) $date_dif="DATEDIFF(b.shipment_date, b.po_received_date) as lead_time_days";
else  $date_dif="(b.shipment_date-b.po_received_date) as lead_time_days";
	$sql_query=("select a.job_no_prefix_num as job_no,a.set_smv,a.season_buyer_wise  as season,a.product_code,a.bh_merchant,a.dealing_marchant,a.style_ref_no,b.id,b.is_confirmed,b.po_number,b.po_received_date,b.shipment_date as orgi_ship_date,a.total_set_qnty as set_ratio,(b.po_quantity*a.total_set_qnty) as po_qty_pcs,b.po_total_price,b.unit_price as unit_price,$date_dif,c.order_total,
c.country_ship_date,c.order_quantity
	 from wo_po_details_master a, wo_po_break_down b
	  LEFT JOIN wo_po_color_size_breakdown c on  c.po_break_down_id=b.id and c.status_active=1
	 where a.job_no=b.job_no_mst   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $team_cond $yarnPoIds_cond  $bh_mer_name_con order by a.bh_merchant,b.shipment_date ");
					 
	$sql_data=sql_select($sql_query);
	$tot_rows=count($sql_data);
	$all_po_id="";
	$marchant_wise_arr=array();$week_wise_order_qty=array();$po_quantity=0;$report_marchant_wise_arr=array();
	foreach( $sql_data as $row)
	{
		if($all_po_id=="") $all_po_id=$row[csf("id")]; else $all_po_id.=",".$row[csf("id")];
		
		if($row[csf('bh_merchant')]=='') $bh_merchant=0;else $bh_merchant=$row[csf('bh_merchant')];
		
			if($db_type==0) $date_week_cond=date("d-m-y",strtotime($row[csf('country_ship_date')]));
			else $date_week_cond=date("d-M-y",strtotime($row[csf('country_ship_date')]));
			$c_date_week=$no_of_week_for_header_calc[$date_week_cond];
			/*if($row[csf('is_confirmed')]==2)
			{
				$marchant_wise_arr[$row[csf('bh_merchant')]][$row[csf('is_confirmed')]]['po_qty']+=$row[csf('order_quantity')];
			}*/
			if($row[csf('is_confirmed')]==2)
			{
				$order_val=$row[csf('po_total_price')];
				
			}
			else
			{
				
				$order_val=$row[csf('order_total')];
				
			}
			//echo $order_val.'g';
			$marchant_wise_arr[$bh_merchant][$row[csf('is_confirmed')]]['po_val']+=$order_val;
			$marchant_wise_arr[$bh_merchant][$row[csf('is_confirmed')]]['set_smv']+=$row[csf('set_smv')];
			$marchant_wise_arr[$bh_merchant][$row[csf('is_confirmed')]]['set_ratio']+=$row[csf('set_ratio')];
			$marchant_wise_arr[$bh_merchant][$row[csf('is_confirmed')]]['unit_price']+=$row[csf('unit_price')];
			$marchant_wise_arr[$bh_merchant][$row[csf('is_confirmed')]]['lead_day']+=$row[csf('lead_time_days')];
			$marchant_wise_arr[$bh_merchant][$row[csf('is_confirmed')]]['job_no'].=$row[csf('job_no')].',';
			
			if($row[csf('season')]!='' || $row[csf('season')]!=0)
			{
				$row[csf('season')]=$row[csf('season')];
			}
			else
			{
				$row[csf('season')]='';	
			}
			
			$marchant_wise_arr[$bh_merchant][$row[csf('is_confirmed')]]['season'].=$row[csf('season')].',';
			
			$marchant_wise_arr[$bh_merchant][$row[csf('is_confirmed')]]['pcode'].=$row[csf('product_code')].',';
			$marchant_wise_arr[$bh_merchant][$row[csf('is_confirmed')]]['style'].=$row[csf('style_ref_no')].',';
			$marchant_wise_arr[$bh_merchant][$row[csf('is_confirmed')]]['po_no'].=$row[csf('po_number')].',';
			$marchant_wise_arr[$bh_merchant][$row[csf('is_confirmed')]]['po_id'].=$row[csf('id')].',';
				
			$marchant_wise_arr[$bh_merchant][$row[csf('is_confirmed')]]['mer'].=$row[csf('dealing_marchant')].',';
			$marchant_wise_arr[$bh_merchant][$row[csf('is_confirmed')]]['po_received_date'].=$row[csf('po_received_date')].',';

			if($row[csf('is_confirmed')]==2)
			{
				$marchant_wise_arr[$bh_merchant][$row[csf('is_confirmed')]]['orgi_shipdate'].=$row[csf('orgi_ship_date')].',';
			}
			
			if($row[csf('is_confirmed')]==1)
			{
				$marchant_wise_arr[$bh_merchant][$row[csf('is_confirmed')]]['c_ship_date'].=$row[csf('country_ship_date')].',';
				$week_wise_order_qty[$bh_merchant][$row[csf('is_confirmed')]][$c_date_week]['po_quantity']+=$row[csf("order_quantity")];
			}
		//print_r($mon_qnty_array);
	}
	/*foreach($bhmarchant_wise_arr_qty as $key=>$value){
		$marchant_wise_arr[$key][2]['orgi_ship_date']='tt';
	}*/
	
	//print_r($marchant_wise_arr);
			
			
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
					$poIds_cond.=" b.id  in($ids) or ";
				}
				$poIds_cond=chop($poIds_cond,'or ');
				$poIds_cond.=")";
			}
			else
			{
				$poIds_cond=" and  b.id  in($all_po_id)";
			}
		}
		$exfactory_data_array=array();
		$exfactory_sql=("select a.bh_merchant,b.is_confirmed,b.shipment_date as orgi_ship_date,
		sum(CASE WHEN c.entry_form!=85 THEN c.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		sum(CASE WHEN c.entry_form=85 THEN c.ex_factory_qnty ELSE 0 END) as return_qnty
		 from wo_po_details_master a, wo_po_break_down b,pro_ex_factory_mst c  where  a.job_no=b.job_no_mst and  c.po_break_down_id=b.id and  a.company_name=$company_name  and c.status_active=1 and c.is_deleted=0 $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $team_cond  $poIds_cond group by a.dealing_marchant,b.is_confirmed,b.shipment_date");
		$exfactory_data=sql_select($exfactory_sql);
		foreach($exfactory_data as $row)
		{
				if($row[csf('bh_merchant')]=='') $bh_merchant=0;else $bh_merchant=$row[csf('bh_merchant')];
				$exfactory_data_array[$bh_merchant][$row[csf('is_confirmed')]][ex_qnty]+=$row[csf('ex_factory_qnty')]-$row[csf('return_qnty')];
		}
		unset($exfactory_data);
		ob_start();
		$rowcount=0;
		foreach($week_counter_header as $mon_key=>$week)
		{
			$rowcount+=count($week);
		}
		 $rowb=$rowcount*2;
		 $td_width=1725+($rowcount*45)+$rowb;
	   $tot_month = datediff( 'm', $date_start,$date_end);
		for($i=0; $i<= $tot_month; $i++ )
		{
			$next_month=month_add($date_start,$i);
			$month_arr.=date("Y-M",strtotime($next_month)).',';
		}
	 $month_arr_id=rtrim($month_arr,',');
	 
	 
	if($tot_rows>0)
	{
	?>
		
         <script>
			var mon_week ='<? echo $month_arr_id; ?>';
			var mon_week=mon_week.split(",");
			 var order_smv=document.getElementById('total_order_smv').innerHTML;
		for (var k=0; k<mon_week.length; k++)
		{
			var mon_data=document.getElementById('summary_footer_td_'+mon_week[k]).innerHTML;
			var mon_arr=mon_data.split("$");
			var mon_qnty=number_format(mon_arr[0],0);
			var mon_amt=number_format(mon_arr[1],0);
			var avg_order_smv=number_format(mon_arr[2],2);
			var avg_rate=(number_format(mon_arr[1],0,'.','')/number_format(mon_arr[0],0,'.',''));
			
			//alert(mon_amt);
			document.getElementById('summary_header_td_qty_'+mon_week[k]).innerHTML=mon_qnty;
			document.getElementById('summary_header_td_val_'+mon_week[k]).innerHTML='$'+mon_amt;
			document.getElementById('summary_header_td_rate_'+mon_week[k]).innerHTML='FOB:'+number_format(avg_rate,2);
			document.getElementById('summary_header_td_smv_'+mon_week[k]).innerHTML='SMV:'+number_format(avg_order_smv,2);
		}
		var yarn_req_qty=document.getElementById('td_yarn_req_march').innerHTML;
		var yarnreq_qty=number_format(yarn_req_qty,0);
		document.getElementById('yarn_summary_tot').innerHTML=yarnreq_qty;
		</script>
        <?
	}
		?>
		<div style="width:<? echo $td_width+20;?>px">
		<fieldset style="width:100%;">	
        
			<table width="<? echo $td_width+20;?>px">
				<tr class="form_caption" style="display:none">
					<td align="center" style="margin-left:400px" colspan="<? echo $rowcount+25; ?>">Order Booking Status</td>
				</tr>
				<tr class="form_caption" align="center" style="display:none">
					<td colspan="<? echo $rowcount+26; ?>" align="center"><? //echo $company_library[$company_name]; ?></td>
				</tr>
			</table>
			<table style="font-size:9px" class="rpt_table" width="<? echo $td_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
                  <tr>
					
                      <th  width="" colspan="11" rowspan="2">&nbsp;</th>
                     <th  rowspan="2" id="yarn_summary_tot"></th>
                     <th  width="" colspan="14" rowspan="2">&nbsp;</th>
                     <?
					
					foreach($week_counter_header as $mon_key=>$week)
					{
					?>
					<th   align="center" colspan="<? echo count($week);?>">
					<? 
					echo $mon_key;
					?>
					</th>
					<?
					}
            ?>
                </tr>
                  <tr>
                     <?
					foreach($week_counter_header as $mon_key=>$week_val)
					{
					?>
					<th width="60" style="font-size:0.836em;"   align="center" colspan="<? echo count($week_val)?>">
					 	
                               <b id="summary_header_td_qty_<? echo $mon_key ;?>"  style="text-align:left;font-size: 0.795em;"> </b> &nbsp;
                                <b id="summary_header_td_val_<? echo $mon_key ;?>"  style="text-align: right;font-size: 0.795em;"> </b> &nbsp;
                                <b id="summary_header_td_rate_<? echo $mon_key ;?>"  style="text-align: right;font-size: 0.795em;"></b> &nbsp;
                               <b  id="summary_header_td_smv_<? echo $mon_key ;?>" title="Avg SMV"  style="text-align: right;font-size: 0.795em;"></b>
					</th>
					<?
					}
            ?>
                   
                </tr>
                <tr>
					<th width="30">SL</th>
                    <th width="55">Season</th>
                    <th width="80">BH Mer.</th>
					<th width="80">FFL Mer.</th>
                   
                    <th width="55">Job No</th>
					<th width="110">Style Ref</th>
                    <th width="60">Comp%</th>
                    <th width="120">Comp Name</th>
                    <th width="120">Const.</th>
                    <th width="50">GSM</th>
					<th width="120">Y/C</th>
                    <th width="70">Yarn Qty</th>
					<th width="60">Status</th>
					<th width="45">OPD</th>
					<th width="40">OPD Date</th>
					<th width="45">TOD</th>
                    <th width="70">Ord Qty</th>
					<th width="100">Order No</th>
                    <th width="40">Dept </th>
					<th width="40">Lead Time</th>
					<th width="35">Unit Price</th>
					<th width="90">Value</th>
					<th width="40">SMV/ Pcs</th>
                    <th width="60">Total SMV</th>
                    <th width="60">Delv Qty</th>
                    <th width="60">Delv.Val</th>
                     <?
					foreach($week_counter_header as $mon_key=>$week)
					{
						foreach($week as $week_key)
						{
							$start_week_date=$week_start_day[$week_key][week_start_day];
							$end_week_date=$week_end_day[$week_key][week_end_day];
							if($db_type==2)
							{
								$fisrtday=date("D",strtotime(change_date_format($start_week_date,"dd-mm-yyyy","-")));
								$lastday=date("D",strtotime(change_date_format($end_week_date,"dd-mm-yyyy","-")));
							}
							else
							{
								$fisrtday=date("D",strtotime(change_date_format($start_week_date,"dd-mm-yyyy")));
								$lastday=date("D",strtotime(change_date_format($end_week_date,"dd-mm-yyyy")));
							}
							$weekstart_date=explode("-",date("d-M",strtotime($start_week_date)));
							$weekstart_date=$weekstart_date[0].'/'.$weekstart_date[1];
						?>
						<th title="<? echo change_date_format($start_week_date,"dd-mm-yyyy","-").'('.$fisrtday.')'." To ".change_date_format($end_week_date,"dd-mm-yyyy","-").'('.$lastday.')'; ?>" width="45"  align="center">
						
						<? 
						echo 'W'.$week_key.'<br/>'.$weekstart_date;
						?>
						</th>
						<?
						}
					}
            ?>
                </tr>    
				</thead>
			</table>
			<div style="width:<? echo $td_width+20;?>px; max-height:245px; overflow-y:scroll" id="scroll_body">
			<table style="font-size:9px" class="rpt_table" width="<? echo $td_width;?>px" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body_bh">
	<?
			$condition= new condition();
			 $condition->company_name("=$cbo_company_name");
			 if(str_replace("'","",$cbo_buyer_name)>0)
			 {
				  $condition->buyer_name("=$cbo_buyer_name");
			 }
			 if(str_replace("'","",$txt_job_no) !='')
			 {
				  $condition->job_no_prefix_num("in($txt_job_no)");
			 }
			
			 if(str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
			 {
			 $condition->pub_shipment_date(" between '$start_date' and '$end_date'");
			 }
				
			 if(str_replace("'","",$txt_order_no)!='')
			 {
				$condition->po_number("=$txt_order_no"); 
			 }
			 if(str_replace("'","",$txt_season)!='')
			 {
				//$condition->season("=$txt_season"); 
			 }
			 $condition->init();
			 
         	$yarn= new yarn($condition);
		  //echo $yarn->getQuery(); die;
		 	$yarn_req_qty_arr=$yarn->getOrderWiseYarnQtyArray();
			//print_r($yarn_req_qty_arr);
				$i=1;$total_order_qty=0;$total_order_value=0;$total_week_qty=0;$total_yarn_req_qty=0;$total_order_smv=0;$total_ex_fact_val=0;$total_ex_fact_qty=0;
				foreach($marchant_wise_arr as $marchant_key=>$marchant_data)
				{  
				   	foreach($marchant_data as $status_key=>$status_val)
					{
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";   
						$po_received_date=$status_val['po_received_date'];
						$countryship_date=$wo_color_data_arr[$marchant_key][$status_key]['c_shipdate'];
						$po_id=rtrim($status_val['po_id'],',');
						$po_id=explode(",",$po_id);
						$conf_country_ship_date=explode(",",$conf_country_ship_date);
						$conf_country_ship_date=rtrim($wo_color_data_arr2[$marchant_key][$status_key]['shipdate'],',');
						$conf_country_ship_date=explode(",",$conf_country_ship_date);
						
						$percent_one=$wo_fab_data_arr[$marchant_key][$status_key]['percent_one'];
						$percent_two=$wo_fab_data_arr[$marchant_key][$status_key]['percent_two'];
						if($percent_one!=0 && $percent_two!=0)
						{
							//if($percent_two!=0)
							$percent_all=$percent_one.'/'.$percent_two;
						}
						else if($percent_one!=0 && $percent_two==0)
						{
							//if($percent_two!=0)
							$percent_all=$percent_one;
						}
						else
						{
							$percent_all='';	
						}
						$copm_two_id=$wo_fab_data_arr[$marchant_key][$status_key]['copm_two_id'];
						$copm_one_id=$wo_fab_data_arr[$marchant_key][$status_key]['copm_one_id'];
						if($copm_one_id!=0 && $copm_two_id!=0)
						{ 
							$copm_one_name=$composition[$copm_one_id].'/'.$composition[$copm_two_id];
						}
						else if($copm_one_id!=0 && $copm_two_id==0)
						{ 
							$copm_one_name=$composition[$copm_one_id];
						
						}
						else 
						{
							$copm_one_name='';
						}
						//echo $copm_one_name;
						$construction=rtrim($wo_fab_data_arr[$marchant_key][$status_key]['construction'],',');
						$construction=implode(",",array_unique(explode(",", $construction)));
						$gsm_weight=rtrim($wo_fab_data_arr[$marchant_key][$status_key]['gsm_weight'],',');
						$gsm_weight=implode(",",array_unique(explode(",", $gsm_weight)));
						$count_id=rtrim($wo_fab_count_data_arr[$marchant_key][$status_key]['count_id'],',');
						$count_ids=array_unique(explode(",", $count_id));
						$yarn_count_value="";
						foreach($count_ids as $val)
						{
								if($yarn_count_value=="") $yarn_count_value=$lib_yarn_count_arr[$val]; else $yarn_count_value.=", ".$lib_yarn_count_arr[$val];
						}
						$merchant=rtrim($status_val['mer'],',');
						$merchant=array_unique(explode(",",$merchant));
						 $merchant_name="";
						foreach($merchant as $m_id)
						{
								if($merchant_name=="") $merchant_name=$team_member_arr[$m_id]; else $merchant_name.=", ".$team_member_arr[$m_id];
						}
						
						$seasion_ids=rtrim($status_val['season'],',');
						$seasion_ids=array_unique(explode(",",$seasion_ids));
						$seasion_id=$seasion_ids[0];
						$job_no=rtrim($status_val['job_no'],',');
						$style=rtrim($status_val['style'],',');
						$po_no=rtrim($status_val['po_no'],',');
						$po_id=rtrim($status_val['po_id'],',');
						
						$po_received_date=rtrim($status_val['po_received_date'],',');
						$c_ship_date=rtrim($status_val['c_ship_date'],',');
						$job_no=implode(",",array_unique(explode(",",$job_no)));
						$style=implode(",",array_unique(explode(",",$style)));
						$po_no=implode(",",array_unique(explode(",",$po_no)));
						$po_id=array_unique(explode(",",$po_id));
						$yarn_req=0;
						foreach($po_id as $pid)
						{
						$yarn_req+=$yarn_req_qty_arr[$pid];
						}
						//echo $yarn_req.'<br>';
						
						$seasion_cond=implode(",",array_unique(explode(",",$seasion_id)));
						/*$seasion_cond="";
						foreach($seasion_id as $sid)
						{
							if($seasion_cond=="") $seasion_cond=$lib_season_name_arr[$sid];else $seasion_cond.=",".$lib_season_name_arr[$sid];
						}*/
						$po_received_date=array_unique(explode(",",$po_received_date));
						$opd_week="";$opd_recv_date="";$opd_recv_date_full="";
						foreach($po_received_date as $op_id)
						{
							if($opd_week=="") $opd_week=$no_of_week_for_header[$op_id];else $opd_week.=", ".$no_of_week_for_header[$op_id];
							//if($opd_recv_date=="") $opd_recv_date=change_date_format($op_id);else $opd_recv_date.=", ".change_date_format($op_id);
							if($opd_recv_date=="") $opd_recv_date=date("d-M",strtotime($op_id));else $opd_recv_date.=",".date("d-M",strtotime($op_id));
							if($opd_recv_date_full=="") $opd_recv_date_full=date("d-M-y",strtotime($op_id));else $opd_recv_date_full.=",".date("d-M-y",strtotime($op_id));
						//$lead_time = datediff( 'd', $op_id,$countryship_date);
						}
						//$orgi_ship_date=rtrim($status_val['orgi_shipdate'],',');
						//$proj_orgi_shipdate=array_unique(explode(",",$orgi_ship_date));
						
						$po_orgi_shipdate=rtrim($status_val['orgi_shipdate'],',');
						$po_orgi_shipdate=array_unique(explode(",",$po_orgi_shipdate));
						$tod_weeks="";$tod_recv_date="";
						foreach($po_orgi_shipdate as $orgi_id)
						{
							if($tod_weeks=="") $tod_weeks=$no_of_week_for_header[$orgi_id];else $tod_weeks.=",".$no_of_week_for_header[$orgi_id];
						}
						//echo $tod_weeks.'ff';
						$tod_weeks=implode(",",array_unique(explode(",",$tod_weeks)));
						
						$lead_time=$status_val['lead_day'];
						$opd_week=implode(",",array_unique(explode(", ",$opd_week)));
						$opd_recv_date=implode(",",array_unique(explode(", ",$opd_recv_date)));
						$prod_cond=rtrim($status_val['pcode'],',');
						$prod_cond=implode(",",array_unique(explode(",",$prod_cond)));
						
						
						
					/*if($status_key==1) //Confirm
					{
						$c_ship_date_con=array_unique(explode(",",$c_ship_date));
						$cc_week_qty=0;$qnty_array_dd=array();
						foreach($c_ship_date_con as $c_date)
						{
							if($db_type==0) $c_date_con=date("d-m-y",strtotime($c_date));
							else $c_date_con=date("d-M-y",strtotime($c_date));
								$c_week=$no_of_week_for_header_calc[$c_date_con];
								if($c_week!='')
								{
									$cc_week_qty=$week_wise_order_qty[$marchant_key][$status_key][$c_week]['po_quantity'];
								}
								$qnty_array_dd[$c_week]=$cc_week_qty;
						}
							$tot_order_qty=0;
							foreach($week_counter_header as $mon_key=>$week)
							{
								//$order_qty_arr=array();
								foreach($week as $week_key)
								{
									 $tot_order_qty=$qnty_array_dd[$week_key];
									  $tot_order_qty_arr[$marchant_key]+=$tot_order_qty;
								}
							}
					}
					else
					{
						$orgi_ship_date=rtrim($status_val['orgi_shipdate']);//orgi_ship_date
						$proj_orgi_shipdate=array_unique(explode(",",$orgi_ship_date));
						$qnty_array_mm=array();$tot_proj_po_qty=0;//$qnty_arr=array();
						foreach($proj_orgi_shipdate as $tod_key)
						{
							$tod_week=$no_of_week_for_header[$tod_key];
							$tot_proj_po_qty=$bhmarchant_wise_arr_qty[$marchant_key][$status_key][$tod_week]['po_qty'];
							//echo $tot_proj_po_qty.'fs';
								//echo $marchant_key.'='.$tod_week.'='.$proj_po_qty.'<br/>';
								$qnty_arr=distribute_projection($tod_week, $tot_proj_po_qty);
								//unset($qnty_arr);
							foreach($qnty_arr as $key=>$val)
							{
								$qnty_array_mm[$key]+=$val;
							}
						
						}
							//unset($qnty_array_mm);
							$tot_order_qty=0;
							foreach($week_counter_header as $mon_key=>$week)
							{
								foreach($week as $week_key)
								{
									$tot_order_qty=$qnty_array_mm[$week_key];
									 $tot_order_qty_arr[$marchant_key]+=$tot_order_qty;
								}
							}
					}*/
					
				if($status_key==1) //Confirm 
				{
					$c_ship_date_con=array_unique(explode(",",$c_ship_date));$qnty_array_dd=array();
					$c_week_qty=0;
					foreach($c_ship_date_con as $c_date)
					{
								if($db_type==0) $c_date_con=date("d-m-y",strtotime($c_date));
								else $c_date_con=date("d-M-y",strtotime($c_date));
								$c_week=$no_of_week_for_header_calc[$c_date_con];
								$c_week_qty=$week_wise_order_qty[$marchant_key][$status_key][$c_week]['po_quantity'];
								$qnty_array_dd[$c_week]=$c_week_qty;
					}
					//print_r($qnty_array_dd);
						$tot_order_qty=0;$tot_order_qty_arr=array();
						foreach($week_counter_header as $mon_key=>$week)
						{
							//$order_qty_arr=array();
							foreach($week as $week_key)
							{
								 $tot_order_qty=$qnty_array_dd[$week_key];
								  $order_qty_arr[$week_key]=$tot_order_qty;
								  $tot_order_qty_arr[$marchant_key]+=$tot_order_qty;
							}
						}
						
				}
				else 
				{
						$orgi_ship_date=rtrim($status_val['orgi_shipdate'],',');
						$proj_orgi_shipdate=array_unique(explode(",",$orgi_ship_date));
						$qnty_array_mm=array();
						//$qnty_arr=distribute_projection($tod_week, $tot_proj_po_qty);
						$week_check=array();
						foreach($proj_orgi_shipdate as $tod_key)
						{
							$tod_week=$no_of_week_for_header[$tod_key];$proj_po_qty=0;
							if($week_check[$tod_week]=='')
							{
								
								$proj_po_qty=$bhmarchant_wise_arr_qty[$marchant_key][$status_key][$tod_week]['po_qty'];
								//echo $proj_po_qty;
								$qnty_arr=distribute_projection($tod_week, $proj_po_qty);
								foreach($qnty_arr as $key=>$val)
								{
									$qnty_array_mm[$key]+=$val;
								}
								$week_check[$tod_week]=$proj_po_qty;
							}
							
						
							
						}
					
							$tot_order_qty=0;$tot_qty=0;$tot_order_qty_arr=array();
							foreach($week_counter_header as $mon_key=>$week)
							{
								 
								foreach($week as $week_key)
								{
									$tot_order_qty=$qnty_array_mm[$week_key];
									 $order_qty_arr[$week_key]=$tot_order_qty;
									 $tot_qty+=$qnty_array_mm[$week_key];
									 $tot_order_qty_arr[$marchant_key]+=$tot_order_qty;
								}
							}
				}
						//print_r($order_qty_arr);
						
						$tot_po_qty=$tot_order_qty_arr[$marchant_key];
						$po_value=$status_val['po_val'];//*$status_val['unit_price'];
						//echo $tot_po_qty.'gg';
						$unit_price=$po_value/$tot_po_qty;
						$orgi_shipdate=rtrim($status_val['orgi_shipdate'],',');
						$job_no_row=count(array_unique(explode(",",$job_no)));
						$style_row=count(array_unique(explode(",",$style)));
						if($job_no_row>1)
						{
							 $view_button="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$marchant_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
						}
						else
						{
							$view_button=$job_no;	
						}
						
						if($style_row>1)
						{
							 $view_button_style="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$marchant_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
						}
						else
						{
							$view_button_style=$style;	
						}
						
						$tod_weeks_row=count(array_unique(explode(",",$tod_weeks)));
						if($tod_weeks_row>1)
						{
							 $view_button_tod="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$marchant_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
						}
						else
						{
							$view_button_tod=$tod_weeks;	
						}
						$opd_week_row=count(array_unique(explode(",",$opd_week)));
						$opd_recv_date_row=count(array_unique(explode(",",$opd_recv_date)));
						if($opd_week_row>1)
						{
							 $view_button_opd="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$marchant_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
						}
						else
						{
							$view_button_opd=$opd_week;	
						}
						if($opd_recv_date_row>1)
						{
							 $view_button_opd_date="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$marchant_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
						}
						else
						{
							$view_button_opd_date=$opd_recv_date;	
						}
						$po_no_row=count(array_unique(explode(",",$po_no)));
						if($po_no_row>1)
						{
							 $view_button_po="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$marchant_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
						}
						else
						{
							$view_button_po=$po_no;	
						}
						$merchant_name_row=count(array_unique(explode(",",$merchant_name)));
						
						
						if($merchant_name_row>1)
						{
							 $view_button_merch="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$marchant_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
						}
						else
						{
							$view_button_merch=$merchant_name;	
						}
						$seasion_cond_row=count(array_unique(explode(",",$seasion_cond)));
						if($seasion_cond_row>1)
						{
							 $view_button_season="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$marchant_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
						}
						else
						{
							$view_button_season=$seasion_cond;	
						}
						$prod_cond_row=count(array_unique(explode(",",$prod_cond)));
						if($prod_cond_row>1)
						{
							 $view_button_dept="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$marchant_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
						}
						else
						{
							$view_button_dept=$prod_cond;	
						}
						$yarn_count_value_row=count(array_unique(explode(",",$yarn_count_value)));
						if($yarn_count_value_row>1)
						{
							 $view_button_count="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$marchant_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
						}
						else
						{
							$view_button_count=$yarn_count_value;	
						}
						$gsm_weight_row=count(array_unique(explode(",",$gsm_weight)));
						if($gsm_weight_row>1)
						{
							 $view_button_gsm="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$marchant_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
						}
						else
						{
							$view_button_gsm=$gsm_weight;	
						}
						
						$construction_row=count(array_unique(explode(",",$construction)));
						if($construction_row>1)
						{
							 $view_button_const="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$marchant_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
						}
						else
						{
							$view_button_const=$construction;	
						}
						$copm_one_name_row=count(array_unique(explode(",",$copm_one_name)));
						if($copm_one_name_row>1)
						{
							 $view_button_comp="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$marchant_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
						}
						else
						{
							$view_button_comp=$copm_one_name;	
						}
				?>
                <tr style="font-size:9px" align="center" bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
					<td width="30"><? echo $i; ?></td>
                    <td width="55" style="word-wrap:break-word; word-break: break-all;"><? echo $seasion_id;?></td>
                     <td width="80" style="word-wrap:break-word; word-break: break-all;" ><? echo $marchant_key; ?></td> 
					<td width="80" style="word-wrap:break-word; word-break: break-all;" ><? echo $view_button_merch; ?></td> 
                  
                    <td width="55" style="word-wrap:break-word; word-break: break-all;">
							 <? echo $view_button; ?></td>
					<td  width="110" style="word-wrap:break-word; word-break: break-all;">  <? echo $view_button_style; ?></td>
                   
                    
                    <td width="60" style="word-wrap:break-word; word-break: break-all;"><? echo $percent_all; ?></td>
                    <td  width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $view_button_comp; ?></td>
                    
                    
                    <td  width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $view_button_const;?></td>
                    <td width="50"  style="word-wrap:break-word; word-break: break-all;"><? echo $view_button_gsm; ?></td>
					<td  width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $view_button_count; ?></td>
                    <td  width="70" style="word-wrap:break-word; word-break: break-all; text-align:right">
					<?
							echo number_format($yarn_req);
					?>
                    </td>
					<td  width="60" style="word-wrap:break-word; word-break: break-all;">
					<?
					
					if($status_key==2)
					{
						echo "Booked";	
					}
					else
					{
						echo "Placed";
					}
					//echo $order_status[$row_data[csf('is_confirmed')]];
					?> 
                    </td>
					<td  width="45" style="word-wrap:break-word; word-break: break-all;">
					<?
					echo $view_button_opd;
					
					?>
                    </td>
					<td  width="40" title="<? echo $opd_recv_date_full;?>" style="word-wrap:break-word; word-break: break-all;">
                    
					<?
					if($opd_recv_date!="" && $opd_recv_date !="0000-00-00" && $opd_recv_date!="0")
					{
						$opd_recv_date=$opd_recv_date;
					}
					echo $view_button_opd_date;
					?>
                      
                    </td>
					<td width="45" title="Orgi Ship Date<? echo $orgi_shipdate;?>" style="word-wrap:break-word; word-break: break-all; text-align: center">
					<? 	echo $view_button_tod; ?>
                    </td>
                    <td  width="70" style="word-wrap:break-word; word-break: break-all; text-align:right">
					<? 
					
					echo number_format($tot_po_qty);?>
                    </td>
					<td  width="100" style="word-wrap:break-word; word-break: break-all; text-align: center">
                   
					<? echo $view_button_po; ?>
                    </td>
                    <td  width="40" style="word-wrap:break-word; word-break: break-all; text-align:left" >
					<? echo $view_button_dept;?>
                    </td>
					<td  width="40" title="TOD-OPD" style="word-wrap:break-word; word-break: break-all; text-align: center">
					<? //echo $lead_time; ?>
                    </td>
					<td  width="35" title="PO Value/Po Qty" style="word-wrap:break-word; word-break: break-all; text-align:right">
					<? echo number_format($unit_price,2); ?>
                    </td>
                   
					<td width="90" title="Po Qty*Unit Price" style="word-wrap:break-word; word-break: break-all;text-align:right">
                   <? echo number_format($tot_po_qty*$unit_price,0);
				  	 $set_smv=number_format($status_val['set_smv'],2);
				    ?>
                    </td>
					<td width="40" title="Set SMV/Set Ratio" style="word-wrap:break-word; word-break: break-all;">
					<? 
					$smv_rate=0;
					$smv_rate=$set_smv/$status_val['set_ratio'];
					echo number_format($smv_rate,2); ?>
                    </td>
					
                    <td  width="60" title="Set SMV Per Pcs*Order Qty" style="word-wrap:break-word; word-break: break-all;text-align:right">
					<? echo number_format(($set_smv/$status_val['set_ratio'])*$tot_po_qty); ?>
                    </td>
                     <td  width="60" style="word-wrap:break-word; word-break: break-all;text-align:right">
					<? echo number_format($exfactory_data_array[$marchant_key][$status_key][ex_qnty]); ?>
                    </td>
                    <td width="60" title="Ex-Fact Qty*Unit Price" style="word-wrap:break-word; word-break: break-all;text-align:right">
                    <? echo  number_format($exfactory_data_array[$marchant_key][$status_key][ex_qnty]*$unit_price,0);
					//echo $exfactory_data_array[$marchant_key][$status_key][ex_qnty].'='.$status_val['unit_price'].'<br/>';
					 ?>
                    </td>
                    <?
					
					
					/*if($status_key==1) //Confirm
					{
						$c_ship_date_con=array_unique(explode(",",$c_ship_date));
						$c_week_qty=0;$qnty_array_dd=array();
						foreach($c_ship_date_con as $c_date)
						{
							if($db_type==0) $c_date_con=date("d-m-y",strtotime($c_date));
							else $c_date_con=date("d-M-y",strtotime($c_date));
								$c_week=$no_of_week_for_header_calc[$c_date_con];
								if($c_week!='')
								{
									$c_week_qty=$week_wise_order_qty[$marchant_key][$status_key][$c_week]['po_quantity'];
								}
								$qnty_array_dd[$c_week]=$c_week_qty;
						}
							$order_qty=0;
							foreach($week_counter_header as $mon_key=>$week)
							{
								//$order_qty_arr=array();
								foreach($week as $week_key)
								{
									 $order_qty=$qnty_array_dd[$week_key];
									  $order_qty_arr[$week_key]=$order_qty;
								}
							}
					}
					else
					{
						$orgi_ship_date=rtrim($status_val['orgi_shipdate'],',');
						$proj_orgi_shipdate=array_unique(explode(",",$orgi_ship_date));
						$qnty_array_mm=array();$proj_po_qty=0;
						foreach($proj_orgi_shipdate as $tod_key)
						{
							$tod_week=$no_of_week_for_header[$tod_key];
							$proj_po_qty=$bhmarchant_wise_arr_qty[$marchant_key][$status_key][$tod_week]['po_qty'];
							//echo $marchant_key.'='.$tod_key.'='.$proj_po_qty.'<br/>';
							$qnty_arr=distribute_projection($tod_week, $proj_po_qty);
								
							foreach($qnty_arr as $key=>$val)
							{
								//echo $marchant_key.'='.$key.'='.$val.'<br/>';
								$qnty_array_mm[$key]+=$val;
							}
							//unset($qnty_arr);
							//print_r($qnty_array_mm);
							
						}
							//unset($qnty_array_mm);
							$order_qty=0;
							foreach($week_counter_header as $mon_key=>$week)
							{
								foreach($week as $week_key)
								{
									$order_qty=$qnty_array_mm[$week_key];
									 $order_qty_arr[$week_key]=$order_qty;
								}
							}
					}*/
							
					// print_r($order_qty_arr); 
					$week_qty=0;
					$week_check=array();
                   	foreach($week_counter_header as $mon_key=>$week)
					{
						foreach($week as $week_key)
						{
						?>
						<td   style="word-wrap:break-word; word-break: break-all;"  width="45" align="right">
						
						<? 
							 	//echo number_format($order_qty_arr[$week_key],0); $order_qty_arr[$week_key]
								$week_qty=$order_qty_arr[$week_key];//$qnty_array[$week_key];
								if($week_qty>0) echo number_format($week_qty,0); else echo ' ';
								$tot_week_qty[$mon_key][$week_key]+=$week_qty;
								$tot_week_amount[$mon_key][$week_key]+=$week_qty*$unit_price;
								$smv_avg=0;
								$smv_avg=$smv_rate*$week_qty;
								$month_smv_arr[$mon_key]+=$smv_avg;
						?>
						</td>
						<?
						}
					}
					?>
                    </tr>
                    <?
					$total_order_qty+=$tot_po_qty;
					$total_yarn_req_qty+=$yarn_req;	
					$total_ex_fact_qty+=$exfactory_data_array[$marchant_key][$status_key][ex_qnty];
					$total_ex_fact_val+=$exfactory_data_array[$marchant_key][$status_key][ex_qnty]*$unit_price;
					$total_order_value+=$tot_po_qty*$unit_price;
					$total_order_smv+=($set_smv/$status_val['set_ratio'])*$tot_po_qty;
					$total_week_qty+=$week_qty;
					//=====================================================================================================================
				$i++;
						}
				}
				?>
                 
				</table>
				</div>
                <table class="tbl_bottom" width="<? echo $td_width;?>px" cellpadding="0" cellspacing="0" id="report_table_footer" border="1" rules="all" >
					<tr>
					<td width="30">&nbsp;</td>
                    <td width="55">&nbsp;</td>
                    <td width="80">&nbsp;</td>
					<td width="80">&nbsp;</td>
                 
                    <td width="55">&nbsp;</td>
					<td width="110">&nbsp;</td>
                    <td width="60">&nbsp;</td>
                    <td width="120">&nbsp;</td>
                    <td width="120">&nbsp;</td>
                    <td width="50">&nbsp;</td>
					<td width="120">&nbsp;</td>
                    <td width="70"  style="word-wrap:break-word; word-break: break-all; font-size:smaller"  id="td_yarn_req_march"><? echo $total_yarn_req_qty;?></td>
					<td width="60">&nbsp;</td>
					<td width="45">&nbsp;</td>
					<td width="40">&nbsp;</td>
					<td width="45">&nbsp;</td>
                    <td width="70" style="word-wrap:break-word; word-break: break-all;font-size:smaller" id="td_po_qty_march"><? echo $total_order_qty;?></td>
					<td width="100">&nbsp;</td>
                    <td width="40">&nbsp; </td>
					<td width="40">&nbsp;</td>
					<td width="35">&nbsp;</td>
					<td width="90" id="td_po_val_march" style="word-wrap:break-word; word-break: break-all;font-size:smaller"><? echo number_format($total_order_value);?></td>
					<td width="40"><? echo number_format($total_order_smv/$total_order_qty,2);?></th>
                    <td width="60"  style="word-wrap:break-word; word-break: break-all;font-size:smaller" id="total_order_smv"><? echo number_format($total_order_smv,0);?></td>
                    <td width="60"  style="word-wrap:break-word; word-break: break-all;font-size:smaller"  id="total_order_exfc_qty"><? echo number_format($total_ex_fact_qty,0);?></td>
                    <td width="60"  style="word-wrap:break-word; word-break: break-all;font-size:smaller"  id="total_order_exfc_val"><? echo number_format($total_ex_fact_val,0);?></td>
                     <?
					$total_week_qty=0; $total_week_amt=0; 
					foreach($week_counter_header as $mon_key=>$week)
					{
						foreach($week as $week_key)
						{
						?>
						<td width="45"  style="word-wrap:break-word; word-break: break-all;font-size:smaller"  align="center">
						<? 
						$total_week_qty=$tot_week_qty[$mon_key][$week_key];
						$total_week_amt=$tot_week_amount[$mon_key][$week_key];
						echo number_format($total_week_qty,0);
						
						$tot_mon_qty[$mon_key]+=$total_week_qty;
						$tot_mon_amount[$mon_key]+=$total_week_amt;
						
						?>
						</td>
						<?
						
						}
					}
            ?>
					</tr>
                     <tr style="display:none">
					<td colspan="26">&nbsp;</td>
                     <?
					$total_week_qt=0; $m=1;
					foreach($week_counter_header as $mon_key=>$week)
					{
						?>
						<td width="" id="summary_footer_td_<? echo $mon_key?>" colspan="<? echo count($week);?>" title="<? echo $tot_week_amount[$mon_key][$week_key];?>" style="word-wrap:break-word; word-break: break-all;"   align="center">
						
						<? 
						$tot_smv=0;
						$tot_smv=$month_smv_arr[$mon_key]/$tot_mon_qty[$mon_key];
						
						$head_td_data=$tot_mon_qty[$mon_key].'$'.$tot_mon_amount[$mon_key].'$'.$tot_smv;
						echo $head_td_data;
						?>
						</td>
						<?
					}
            ?>
                    </tr>
				</table>
			</fieldset>
		</div>
          <style>
			@font-face {
   				 font-family:"Arial Narrow"; font-size:11px;
   				 
			}
			TD{font-family:"Arial Narrow";font-size:11px;}
			TH{font-family:"Arial Narrow";font-size:11px;}
			
			.rpt_table TD{font-family: "Arial Narrow";font-size:11px;}
			.rpt_table TH{font-family:"Arial Narrow";font-size:11px;}
			
		</style>
	<?
	}
//===========================================================================================================================================================
	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data****$filename****$tot_rows****$cbo_search_type";
	exit();	
	
} //BH End

else if($action=="report_generate_style") //Style
{
 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company_name=str_replace("'","",$cbo_company_name);
	$cbo_search_type=str_replace("'","",$cbo_search_type);
	$cbo_bh_mer_name=str_replace("'","",$cbo_bh_mer_name);
	$txt_yarn_count=str_replace("'","",$txt_yarn_count);
	if($txt_yarn_count!='') $yarn_count_con="and yarn_count Like '%$txt_yarn_count%' ";else $yarn_count_con='';
	
	//$serch_by=str_replace("'","",$cbo_search_by);
	$buyer_id_cond="";
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{
				$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else
			{
				$buyer_id_cond="";
			}
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}
	
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_job_no=trim($txt_job_no);
	if($txt_job_no !="" || $txt_job_no !=0)
	{
		//$year = substr(str_replace("'","",$cbo_year_selection), -2); 
		//$job_no=$company_library[$company_name]."-".$year."-".str_pad($txt_job_no, 5, 0, STR_PAD_LEFT);
		$jobcond="and a.job_no_prefix_num in(".$txt_job_no.")";
	}
	else
	{
		$jobcond="";	
	}
	
	$date_cond='';
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			$start_date=(str_replace("'","",$txt_date_from));
			$end_date=(str_replace("'","",$txt_date_to));
			
			$date_cond="and b.pub_shipment_date between '$start_date' and '$end_date'";
		}
	
	if(str_replace("'","",$txt_style_ref)!="") $style_ref_cond=" and a.style_ref_no like '%".str_replace("'","",$txt_style_ref)."%'"; else $style_ref_cond="";
	if(str_replace("'","",$txt_order_no)!="")
	{
		$ordercond=" and b.po_number like '%".str_replace("'","",$txt_order_no)."%'"; 
	}
	else
	{
		$ordercond="";
	}
	
	$team_cond="";
	if(str_replace("'","",$cbo_team_name)!=0) $team_cond=" and a.team_leader=".str_replace("'","",$cbo_team_name)."  ";
	if(str_replace("'","",$cbo_team_member)!=0) $team_cond.=" and a.dealing_marchant=".str_replace("'","",$cbo_team_member)."  ";
	$cbo_bh_mer_name=str_replace("0","",$cbo_bh_mer_name);
	if($cbo_bh_mer_name!=0 || $cbo_bh_mer_name!='') $bh_mer_name_con="and a.bh_merchant='$cbo_bh_mer_name' ";else $bh_mer_name_con='';
	 $cbo_year_selection=str_replace("'","",$cbo_year_selection);

	$start_ddd= strtotime(change_date_format($start_date,"dd-mm-yyyy","-"));
	$start_ddd=date("Y-m-d",$start_ddd);
	$start_end_date= strtotime(change_date_format($end_date,"dd-mm-yyyy","-"));
	$start_end_date=date("Y-m-d",$start_end_date);
	 //$dd= date('d-m-Y',strtotime($start_date));
	//$start_date_week= date($dd, time() - 1296000);//$start_date;
	//echo $last_day_this_month  = date($start_date_week, time() -1814400); //date('t-m-Y');
	
	$dateee = strtotime($start_ddd);
	$start_end_date = strtotime($start_end_date);
	
	$date_start = date("Y-m-d",strtotime("-28 day", $dateee));
	$date_end = date("Y-m-d",strtotime("+28 day", $start_end_date));
	//echo $date_end;
	//$end_date_week=  addday("$end_date", time() -1814400);//$start_date;//$start_date;
	
	if($db_type==0)
	{
		$date_start= change_date_format($date_start,"yyyy-mm-dd");
		$date_end= change_date_format($date_end,"yyyy-mm-dd");
	}
	else
	{
		$date_start= change_date_format($date_start,"dd-mm-yyyy","-",1);
		$date_end= change_date_format($date_end,"dd-mm-yyyy","-",1);
	}
	
	
	//echo "select week_date,week from week_of_year where week_date between '$date_start' and  '$date_end' order by week_date asc";die;
	// date("d-m-Y", time() - 1296000);
	$week_for_arr=array();$no_of_week_for_header=array();
	$sql_week_header=sql_select("select week_date,week from week_of_year where week_date between '$date_start' and  '$date_end' order by week_date asc");
	$week_check_head=array();
	foreach ($sql_week_header as $row_week_header)
	{
		if($week_check_head[$row_week_header[csf("week")]]=='')
		{
			$week_counter_header[date("Y-M",strtotime($row_week_header[csf("week_date")]))][$row_week_header[csf("week")]]=$row_week_header[csf("week")];
			$week_check_head[$row_week_header[csf("week")]]=$week_counter_header[date("Y-M",strtotime($row_week_header[csf("week_date")]))][$row_week_header[csf("week")]];
		}
		$tmp=add_date($row_week_header[csf("week_date")],-1);
		if($db_type==0) $tmp_cond=date("d-m-y",strtotime($tmp));
		else $tmp_cond=date("d-M-y",strtotime($tmp));
		
		$no_of_week_for_header_calc[$tmp_cond]=$row_week_header[csf("week")];
		$no_of_week_for_header[$row_week_header[csf("week_date")]]=$row_week_header[csf("week")];
		$test[$row_week_header[csf("week")]]=$row_week_header[csf("week")];
	}
	unset($sql_week_header);
	$week_start_day=array();
	$week_end_day=array();$week_day_arr=array();
	//week_day
	$sql_week_start_end_date=sql_select("select week,min(week_date) as week_start_day, Max(week_date) as week_end_day from week_of_year where week_date between '$date_start' and  '$date_end' group by week");
	foreach ($sql_week_start_end_date as $row_week)
	{
		$week_start_day[$row_week[csf("week")]][week_start_day]=$row_week[csf("week_start_day")];
		$week_end_day[$row_week[csf("week")]][week_end_day]=$row_week[csf("week_end_day")];
		//$week_day_arr[$row_week[csf("week")]][$row_week[csf("week_start_day")]][week_first_day]=$row_week[csf("min_week_day")];
		$week_day_arr[$row_week[csf("week_end_day")]]['week_last_day']=$row_week[csf("last_week_day")];
	}
	unset($sql_week_start_end_date);
	//print_r($week_day_arr);
	function distribute_projection( $base_week, $qnty)
	{
		//5. Distribution ratios are: Base week 52%, Immediate before 24%, Week before immediate week 20%, Immediate week after base week 2%, next 2 weeks as 1%
		global $test;
		 
		$tmpbase_week=$test[$base_week-3];
		$k=0;
		for($i=0; $i<6; $i++)
		{
			$tmpbase_week++;
			if( $test[$tmpbase_week]=='') { $tmpbase_week=1; $k=0; }
			
			
			if($i==0) $distr_arr[$tmpbase_week]=($qnty*20)/100;
			if($i==1) $distr_arr[$tmpbase_week]=($qnty*24)/100;
			if($i==2) $distr_arr[$tmpbase_week]=($qnty*52)/100;
			if($i==3) $distr_arr[$tmpbase_week]=($qnty*2)/100;
			if($i==4) $distr_arr[$tmpbase_week]=($qnty*1)/100;
			if($i==5) $distr_arr[$tmpbase_week]=($qnty*1)/100;
			$k++;
			
		}
		 
		return $distr_arr;
		/*5000
		b=52
		b-1=21
		b-2=15
		b+1=1
		b+2=1*/
	}
	//echo "<per>";
	 //print_r(distribute_projection("2016-Dec",51, 5000)); die;


	
	if($template==1)
	{
		$po_wise_buyer=array();
		$buyer_wise_data=array();
		$po_id_arr=array();
		$report_data_arr=array();
		
		$count_data=sql_select("select yarn_count,id from  lib_yarn_count  where status_active=1 and is_deleted=0 and yarn_count is not null $yarn_count_con");
		$yarn_id='';
		foreach($count_data as $row)
		{
				if($yarn_id=='') $yarn_id=$row[csf('id')];else $yarn_id.=",".$row[csf('id')];
		}

		$yarn_ids=count(array_unique(explode(",",$yarn_id)));
		$yarn_numIds=chop($yarn_id,','); $yarnIds_cond="";
		if($yarn_id!='' || $yarn_id!=0)
		{
			if($db_type==2 && $yarn_ids>1000)
			{
				$yarnIds_cond=" and (";
				$yIdsArr=array_chunk(explode(",",$yarn_numIds),990);
				//print_r($gate_outIds);
				foreach($yIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$yarnIds_cond.=" d.count_id  in($ids) or ";
				}
				$yarnIds_cond=chop($yarnIds_cond,'or ');
				$yarnIds_cond.=")";
			}
			else
			{
				$yarnIds_cond=" and  d.count_id  in($yarn_id)";
			}
		}	
	
	$yarn_sql_data=("select a.style_ref_no,b.is_confirmed,b.id as po_id,d.count_id,d.copm_one_id,d.copm_two_id,d.percent_one,d.percent_two,sum(d.cons_qnty) as cons_qnty,e.construction,e.gsm_weight,b.shipment_date as orgi_ship_date from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fab_yarn_cost_dtls d,wo_pre_cost_fabric_cost_dtls e where a.job_no=b.job_no_mst  and c.job_no_mst=a.job_no and b.id=c.po_break_down_id and d.job_no=a.job_no and 
	e.id=d.fabric_cost_dtls_id and  c.item_number_id= e.item_number_id and e.job_no=a.job_no and  e.body_part_id in(1,20) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1   and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $team_cond  $yarnIds_cond group by   b.id ,d.count_id,d.copm_one_id,d.copm_two_id,d.percent_one,d.percent_two,e.construction,e.gsm_weight,b.shipment_date,a.style_ref_no,b.is_confirmed"); 
	$sql_result_y=sql_select($yarn_sql_data);
	$wo_fab_data_arr=array();
	foreach($sql_result_y as $row)
		{
			if($all_yarn_po_id=="") $all_yarn_po_id=$row[csf("po_id")]; else $all_yarn_po_id.=",".$row[csf("po_id")];
			$tod_week=$no_of_week_for_header[$row[csf('orgi_ship_date')]];
			$wo_fab_data_arr[$row[csf('style_ref_no')]][$row[csf('is_confirmed')]]['percent_one']=$row[csf('percent_one')];
			$wo_fab_data_arr[$row[csf('style_ref_no')]][$row[csf('is_confirmed')]]['percent_two']=$row[csf('percent_two')];
			$wo_fab_data_arr[$row[csf('style_ref_no')]][$row[csf('is_confirmed')]]['copm_two_id']=$row[csf('copm_two_id')];
			$wo_fab_data_arr[$row[csf('style_ref_no')]][$row[csf('is_confirmed')]]['copm_one_id']=$row[csf('copm_one_id')];
			$wo_fab_data_arr[$row[csf('style_ref_no')]][$row[csf('is_confirmed')]]['construction'].=$row[csf('construction')].',';
			$wo_fab_data_arr[$row[csf('style_ref_no')]][$row[csf('is_confirmed')]]['gsm_weight'].=$row[csf('gsm_weight')].',';
			$wo_fab_count_data_arr[$row[csf('style_ref_no')]][$row[csf('is_confirmed')]]['count_id'].=$row[csf('count_id')].",";
			//$wo_yarn_data_arr[$row[csf('po_id')]]['cons_qnty']+=$row[csf('cons_qnty')];
		}
		unset($sql_result_y);
		$yarn_po_ids=count(array_unique(explode(",",$all_yarn_po_id)));
		$yarnPo_numIds=chop($all_yarn_po_id,','); $yarnPoIds_cond="";
		if($txt_yarn_count!='')
		{
			if($all_yarn_po_id!='' || $all_yarn_po_id!=0)
			{
				if($db_type==2 && $yarn_po_ids>1000)
				{
					$yarnPoIds_cond=" and (";
					$yPoIdsArr=array_chunk(explode(",",$yarnPo_numIds),990);
					foreach($yPoIdsArr as $ids)
					{
						$ids=implode(",",$ids);
						$yarnPoIds_cond.=" b.id  in($ids) or ";
					}
					$yarnPoIds_cond=chop($yarnPoIds_cond,'or ');
					$yarnPoIds_cond.=")";
				}
				else
				{
					$yarnPoIds_cond=" and  b.id  in($all_yarn_po_id)";
				}
			}
		}
	
		$style_wise_arr_orgi_date=array();$style_wise_arr_qty=array();
		 $sql_data_c=("select b.id as po_id,a.style_ref_no,b.shipment_date as orgi_ship_date,b.is_confirmed,(b.po_quantity*a.total_set_qnty) as po_qty_pcs
		from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $team_cond $bh_mer_name_con order by a.style_ref_no,b.shipment_date ");
		$sql_result_c=sql_select($sql_data_c);
		foreach($sql_result_c as $row)
		{
			if($row[csf('is_confirmed')]==2)
			{
				//if($row[csf('style_ref_no')]=='') $del_merchant=0;else $del_merchant=$row[csf('style_ref_no')];
				$tod_week=$no_of_week_for_header[$row[csf('orgi_ship_date')]];
				$style_wise_arr_qty[$row[csf('style_ref_no')]][$row[csf('is_confirmed')]][$tod_week]['po_qty']+=$row[csf('po_qty_pcs')];
				$style_wise_arr_orgi_date[$row[csf('style_ref_no')]][$row[csf('is_confirmed')]]['orgi_ship_date'].=$row[csf('orgi_ship_date')].',';
			}
		}	
	if($db_type==0) $date_dif="DATEDIFF(b.shipment_date, b.po_received_date) as lead_time_days";
else  $date_dif="(b.shipment_date-b.po_received_date) as lead_time_days";

	 $sql_query=("select $date_dif,a.job_no_prefix_num as job_no,a.set_smv,a.season_buyer_wise  as 	season,a.product_code,a.bh_merchant,a.dealing_marchant,a.style_ref_no,b.id,b.is_confirmed,b.po_number,b.po_received_date,b.shipment_date as orgi_ship_date,a.total_set_qnty as set_ratio,(b.po_quantity*a.total_set_qnty) as po_qty_pcs,b.po_total_price,a.avg_unit_price as unit_price ,c.country_ship_date,c.order_quantity,c.order_total
	 from wo_po_details_master a, wo_po_break_down b
					LEFT JOIN wo_po_color_size_breakdown c on  c.po_break_down_id=b.id and  c.status_active=1
	 where a.job_no=b.job_no_mst  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1   and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $bh_mer_name_con $team_cond order by a.style_ref_no,a.job_no ");
	 
	$sql_data=sql_select($sql_query);
	$tot_rows=count($sql_data);
	$all_po_id="";
	$marchant_wise_arr=array();
	foreach( $sql_data as $row)
	{
		$country_ship_date=$wo_color_data_arr[$row[csf('style_ref_no')]][$row[csf('is_confirmed')]]['c_shipdate'];
		
		if($db_type==0) $c_date_con=date("d-m-y",strtotime($row[csf('country_ship_date')]));
		else $c_date_con=date("d-M-y",strtotime($row[csf('country_ship_date')]));
		$c_date_week=$no_of_week_for_header_calc[$c_date_con];
		
		if($all_po_id=="") $all_po_id=$row[csf("id")]; else $all_po_id.=",".$row[csf("id")];
		if($row[csf('is_confirmed')]==1)
		{
			$order_val=$row[csf('order_total')];
		}
		else
		{
			$order_val=$row[csf('po_total_price')];
		}
		//$style_wise_arr[$row[csf('style_ref_no')]][$row[csf('is_confirmed')]]['po_qty']+=$row[csf('po_qty_pcs')];
		$style_wise_arr[$row[csf('style_ref_no')]][$row[csf('is_confirmed')]]['po_qty']+=$row[csf('order_quantity')];
		//$style_wise_arr[$row[csf('style_ref_no')]][$row[csf('is_confirmed')]]['po_val']+=$row[csf('po_total_price')];
		$style_wise_arr[$row[csf('style_ref_no')]][$row[csf('is_confirmed')]]['po_val']+=$order_val;
		$style_wise_arr[$row[csf('style_ref_no')]][$row[csf('is_confirmed')]]['set_smv']+=$row[csf('set_smv')];
		$style_wise_arr[$row[csf('style_ref_no')]][$row[csf('is_confirmed')]]['lead_day']+=$row[csf('lead_time_days')];
		$style_wise_arr[$row[csf('style_ref_no')]][$row[csf('is_confirmed')]]['set_ratio']+=$row[csf('set_ratio')];
		$style_wise_arr[$row[csf('style_ref_no')]][$row[csf('is_confirmed')]]['unit_price']+=$row[csf('unit_price')];
		$style_wise_arr[$row[csf('style_ref_no')]][$row[csf('is_confirmed')]]['job_no'].=$row[csf('job_no')].',';
		$style_wise_arr[$row[csf('style_ref_no')]][$row[csf('is_confirmed')]]['season'].=$row[csf('season')].',';
		$style_wise_arr[$row[csf('style_ref_no')]][$row[csf('is_confirmed')]]['pcode'].=$row[csf('product_code')].',';
		$style_wise_arr[$row[csf('style_ref_no')]][$row[csf('is_confirmed')]]['dealing_marchant'].=$row[csf('dealing_marchant')].',';
		$style_wise_arr[$row[csf('style_ref_no')]][$row[csf('is_confirmed')]]['bh_mer'].=$row[csf('bh_merchant')].',';
		$style_wise_arr[$row[csf('style_ref_no')]][$row[csf('is_confirmed')]]['po_no'].=$row[csf('po_number')].',';
		$style_wise_arr[$row[csf('style_ref_no')]][$row[csf('is_confirmed')]]['po_id'].=$row[csf('id')].',';
		$style_wise_arr[$row[csf('style_ref_no')]][$row[csf('is_confirmed')]]['po_received_date'].=$row[csf('po_received_date')].',';
		if($row[csf('is_confirmed')]==2)
		{
		  $style_wise_arr[$row[csf('style_ref_no')]][$row[csf('is_confirmed')]]['orgi_ship_date'].=$row[csf('orgi_ship_date')].',';
		}
		$style_wise_arr[$row[csf('style_ref_no')]][$row[csf('is_confirmed')]]['c_ship_date'].=$row[csf('country_ship_date')].',';
		if($row[csf('is_confirmed')]==1)
		{
		  $week_wise_order_qty[$row[csf('style_ref_no')]][$row[csf('is_confirmed')]][$c_date_week]['po_quantity']+=$row[csf("order_quantity")];
		}
	}
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
					$poIds_cond.=" b.id  in($ids) or ";
				}
				$poIds_cond=chop($poIds_cond,'or ');
				$poIds_cond.=")";
			}
			else
			{
				$poIds_cond=" and  b.id  in($all_po_id)";
			}
		}
		$exfactory_data_array=array();
		$exfactory_sql=("select a.style_ref_no,b.is_confirmed,
		sum(CASE WHEN c.entry_form!=85 THEN c.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		sum(CASE WHEN c.entry_form=85 THEN c.ex_factory_qnty ELSE 0 END) as return_qnty
		 from wo_po_details_master a, wo_po_break_down b,pro_ex_factory_mst c  where  a.job_no=b.job_no_mst and  c.po_break_down_id=b.id and  a.company_name=$company_name  and c.status_active=1 and c.is_deleted=0 $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $team_cond  $poIds_cond group by a.style_ref_no,b.is_confirmed");
		$exfactory_data=sql_select($exfactory_sql);
		foreach($exfactory_data as $row)
		{
				$exfactory_data_array[$row[csf('style_ref_no')]][$row[csf('is_confirmed')]][ex_qnty]+=$row[csf('ex_factory_qnty')]-$row[csf('return_qnty')];
		}
		unset($exfactory_data);
	
		ob_start();
		$rowcount=0;
		foreach($week_counter_header as $mon_key=>$week)
		{
			$rowcount+=count($week);
		}
		 $rowb=$rowcount*2;
		 $td_width=1825+($rowcount*45)+$rowb;
	 	$tot_month = datediff( 'm', $date_start,$date_end);
		for($i=0; $i<= $tot_month; $i++ )
		{
			$next_month=month_add($date_start,$i);
			$month_arr.=date("Y-M",strtotime($next_month)).',';
		}
		$month_arr_id=rtrim($month_arr,',');
	if($tot_rows>0)
	{
	?>
		
        <script>
			var mon_week ='<? echo $month_arr_id; ?>';
			var mon_week=mon_week.split(",");
		for (var k=0; k<mon_week.length; k++)
		{
			var mon_data=document.getElementById('summary_footer_td_'+mon_week[k]).innerHTML;
			var mon_arr=mon_data.split("$");
			var mon_qnty=number_format(mon_arr[0],0);
			var mon_amt=number_format(mon_arr[1],0);
			var avg_order_smv=number_format(mon_arr[2],2);
			var avg_rate=(number_format(mon_arr[1],0,'.','')/number_format(mon_arr[0],0,'.',''));
			
		//	alert(avg_rate);
			document.getElementById('summary_header_td_qty_'+mon_week[k]).innerHTML=mon_qnty;
			document.getElementById('summary_header_td_val_'+mon_week[k]).innerHTML='$'+mon_amt;
			document.getElementById('summary_header_td_rate_'+mon_week[k]).innerHTML='FOB:'+number_format(avg_rate,2);
			document.getElementById('summary_header_td_smv_'+mon_week[k]).innerHTML='SMV:'+number_format(avg_order_smv,2);
			
		}
		
		
		var yarn_req_qty=document.getElementById('td_yarn_req_march').innerHTML;
		var yarnreq_qty=number_format(yarn_req_qty,0);
		document.getElementById('yarn_summary_tot').innerHTML=yarnreq_qty;
        </script>
            <?
	}
			?>
		<div style="width:<? echo $td_width+20;?>px">
		<fieldset style="width:100%;">	
      
        
			<table width="<? echo $td_width+20;?>px">
				<tr class="form_caption" style="display:none">
					<td align="center" style="margin-left:400px" colspan="<? echo $rowcount+25; ?>">Order Booking Status/Style</td>
				</tr>
				<tr class="form_caption" align="center" style="display:none">
					<td colspan="<? echo $rowcount+26; ?>" align="center"><? //echo $company_library[$company_name]; ?></td>
				</tr>
			</table>
			<table class="rpt_table" width="<? echo $td_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
                  <tr>
					
                     <th  width="" colspan="11" rowspan="2">&nbsp;</th>
                     <th  rowspan="2" id="yarn_summary_tot"></th>
                     <th  width="" colspan="14" rowspan="2">&nbsp;</th>
                     <?
					
					foreach($week_counter_header as $mon_key=>$week)
					{
					?>
					<th   align="center" colspan="<? echo count($week);?>">
					<? 
				
					echo $mon_key;
					?>
					</th>
					<?
					}
            ?>
                   
                </tr>
                  <tr>
                     <?
					foreach($week_counter_header as $mon_key=>$week_val)
					{
					?>
					<th width="70"   style="font-size: 0.825em;"  align="center" colspan="<? echo count($week_val)?>">
                              <b id="summary_header_td_qty_<? echo $mon_key ;?>"  style="text-align:left;font-size: 0.795em;"> </b> &nbsp;
                              <b id="summary_header_td_val_<? echo $mon_key ;?>"  style="text-align: right;font-size: 0.795em;"></b> &nbsp;
                              <b id="summary_header_td_rate_<? echo $mon_key ;?>"  title="Avg FOB" style="text-align: right;font-size: 0.795em;"></b> &nbsp;
                              <b title="Avg SMV" id="summary_header_td_smv_<? echo $mon_key ;?>"  style="text-align:right;font-size: 0.795em;"></b>
					</th>
					<?
					}
            ?>
                </tr>
                <tr>
					<th width="30">SL</th>
                    <th width="55">Season</th>
                    <th width="100">BH Mer.</th>
					<th width="140">FFL Mer.</th>
                    <th width="55">Job No</th>
					<th width="120">Style Ref</th>
                    <th width="60">Comp%</th>
                    <th width="120">Comp Name</th>
                    <th width="120">Const.</th>
                    <th width="50">GSM</th>
					<th width="120">Y/C</th>
                    <th width="70">Yarn Qty</th>
					<th width="60">Status</th>
					<th width="45">OPD</th>
					<th width="40">OPD Date</th>
					<th width="45">TOD</th>
                    <th width="70">Ord Qty</th>
					<th width="100">Order No</th>
                    <th width="40">Dept </th>
					<th width="40">Lead Time</th>
					<th width="35">Unit Price</th>
					<th width="90">Value</th>
					<th width="40">SMV/ Pcs</th>
                    <th width="60">Ord.SMV</th>
                    <th width="60">Delv Qty</th>
                    <th width="60">Delv.Val</th>
                     <?
					foreach($week_counter_header as $mon_key=>$week)
					{
						foreach($week as $week_key)
						{
							$start_week_date=$week_start_day[$week_key][week_start_day];
							$end_week_date=$week_end_day[$week_key][week_end_day];
							if($db_type==2)
							{
								$fisrtday=date("D",strtotime(change_date_format($start_week_date,"dd-mm-yyyy","-")));
								$lastday=date("D",strtotime(change_date_format($end_week_date,"dd-mm-yyyy","-")));
							}
							else
							{
								$fisrtday=date("D",strtotime(change_date_format($start_week_date,"dd-mm-yyyy")));
								$lastday=date("D",strtotime(change_date_format($end_week_date,"dd-mm-yyyy")));
							}
							$weekstart_date=explode("-",date("d-M",strtotime($start_week_date)));
							$weekstart_date=$weekstart_date[0].'/'.$weekstart_date[1];
							
						?>
						<th title="<? echo change_date_format($start_week_date,"dd-mm-yyyy","-").'('.$fisrtday.')'." To ".change_date_format($end_week_date,"dd-mm-yyyy","-").'('.$lastday.')'; ?>" width="45"  align="center">
						
						<? 
						echo 'W'.$week_key.'<br/>'.$weekstart_date;
						?>
						</th>
						<?
						}
					}
            ?>
                   
                </tr>    
				</thead>
			</table>
			<div style="width:<? echo $td_width+20;?>px; max-height:245px; overflow-y:scroll" id="scroll_body">
			<table class="rpt_table" width="<? echo $td_width;?>px" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body_style">
	<?
			$condition= new condition();
			 $condition->company_name("=$cbo_company_name");
			 if(str_replace("'","",$cbo_buyer_name)>0)
			 {
				  $condition->buyer_name("=$cbo_buyer_name");
			 }
			 if(str_replace("'","",$txt_job_no) !='')
			 {
				  $condition->job_no_prefix_num("in($txt_job_no)");
			 }
			
			 if(str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
			 {
			 $condition->pub_shipment_date(" between '$start_date' and '$end_date'");
			 }
				
			 if(str_replace("'","",$txt_order_no)!='')
			 {
				$condition->po_number("=$txt_order_no"); 
			 }
			 if(str_replace("'","",$txt_season)!='')
			 {
				//$condition->season("=$txt_season"); 
			 }
			 $condition->init();
			 	
         	$yarn= new yarn($condition);
		  //echo $yarn->getQuery(); die;
		 	$yarn_req_qty_arr=$yarn->getOrderWiseYarnQtyArray();
			//print_r($yarn_req_qty_arr);
				$i=1;$total_order_qty=0;$total_order_value=0;$total_yarn_req_qty=0;$total_order_smv=0;$total_ex_fact_val=0;$total_ex_fact_qty=0;
				foreach($style_wise_arr as $style_key=>$style_data)
				{  
				   	foreach($style_data as $status_key=>$status_val)
					{
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";   
				$po_received_date=$status_val['po_received_date'];
				$countryship_date=$wo_color_data_arr[$style_key][$status_key]['c_shipdate'];
				
				$conf_country_ship_date=rtrim($wo_color_data_arr2[$style_key][$status_key]['c_ship_date'],',');
				$conf_country_ship_date=explode(",",$conf_country_ship_date);
				$percent_one=$wo_fab_data_arr[$style_key][$status_key]['percent_one'];
				$percent_two=$wo_fab_data_arr[$style_key][$status_key]['percent_two'];
				if($percent_one!=0 && $percent_two!=0)
				{
					$percent_all=$percent_one.'/'.$percent_two;
				}
				else if($percent_one!=0 && $percent_two==0)
				{
					$percent_all=$percent_one;
				}
				else
				{
					$percent_all='';	
				}
				$copm_two_id=$wo_fab_data_arr[$style_key][$status_key]['copm_two_id'];
				$copm_one_id=$wo_fab_data_arr[$style_key][$status_key]['copm_one_id'];
				if($copm_one_id!=0 && $copm_two_id!=0)
				{ 
					$copm_one_name=$composition[$copm_one_id].'/'.$composition[$copm_two_id];
				}
				else if($copm_one_id!=0 && $copm_two_id==0)
				{ 
					$copm_one_name=$composition[$copm_one_id];
				
				}
				else 
				{
					$copm_one_name='';
				}
				
				$construction=rtrim($wo_fab_data_arr[$style_key][$status_key]['construction'],',');
				$construction=implode(",",array_unique(explode(",", $construction)));
				
				$gsm_weight=rtrim($wo_fab_data_arr[$style_key][$status_key]['gsm_weight'],',');
				$gsm_weight=implode(",",array_unique(explode(",", $gsm_weight)));
				$count_id=rtrim($wo_fab_count_data_arr[$style_key][$status_key]['count_id'],',');
				 $count_ids=array_unique(explode(",", $count_id));
				 $yarn_count_value="";
				foreach($count_ids as $val)
				{
						if($yarn_count_value=="") $yarn_count_value=$lib_yarn_count_arr[$val]; else $yarn_count_value.=", ".$lib_yarn_count_arr[$val];
				}
				$seasion_ids=rtrim($status_val['season'],',');
				$seasion_ids=array_unique(explode(",",$seasion_ids));
				//echo $seasion_ids[0];
				$seasion_id=$seasion_ids[0];
				$job_no=rtrim($status_val['job_no'],',');
				$style=rtrim($status_val['style'],',');
				$po_no=rtrim($status_val['po_no'],',');
				$po_id=rtrim($status_val['po_id'],',');
				$dealing_marchant=rtrim($status_val['dealing_marchant'],',');
				$c_ship_date=rtrim($status_val['c_ship_date'],',');
				$bh_mers=rtrim($status_val['bh_mer'],',');
				//$bh_mer=ltrim(',',$status_val['bh_mer']);
				$bh_mers=array_unique(explode(",",$bh_mers));
				$bh_mer=$bh_mers[0];
				//$bh_mer=implode(",",array_unique(explode(",",$bh_mer)));
				$po_received_date=rtrim($status_val['po_received_date'],',');
				$job_no=implode(",",array_unique(explode(",",$job_no)));
				$style=implode(",",array_unique(explode(",",$style)));
				$po_no=implode(",",array_unique(explode(",",$po_no)));
				$po_id=array_unique(explode(",",$po_id));
			
				$yarn_req=0;
				foreach($po_id as $pid)
				{
				$yarn_req+=$yarn_req_qty_arr[$pid];
				}
				//$seasion_id=array_unique(explode(",",$seasion_id));
				//$seasion_cond="";
				foreach($seasion_id as $sid)
				{
					//if($seasion_cond=="") $seasion_cond=$lib_season_name_arr[$sid];else $seasion_cond.=",".$lib_season_name_arr[$sid];
				}
				$dealing_marchant=array_unique(explode(",",$dealing_marchant));
				$dealing_marchant_con="";
				foreach($dealing_marchant as $mid)
				{
					if($dealing_marchant_con=="") $dealing_marchant_con=$team_member_arr[$mid];else $dealing_marchant_con.=",".$team_member_arr[$mid];
				}
				$po_received_date=array_unique(explode(",",$po_received_date));
				$opd_week="";$opd_recv_date="";
				//$lead_time=0;
				foreach($po_received_date as $op_id)
				{
					if($opd_week=="") $opd_week=$no_of_week_for_header[$op_id];else $opd_week.=",".$no_of_week_for_header[$op_id];
					//if($opd_recv_date=="") $opd_recv_date=change_date_format($op_id);else $opd_recv_date.=",".change_date_format($op_id);
					if($opd_recv_date=="") $opd_recv_date=date("d-M",strtotime($op_id));else $opd_recv_date.=",".date("d-M",strtotime($op_id));
					if($opd_recv_date_full=="") $opd_recv_date_full=date("d-M-y",strtotime($op_id));else $opd_recv_date_full.=",".date("d-M-y",strtotime($op_id));
				}
				$po_orgi_shipdate=rtrim($status_val['orgi_ship_date'],',');
				$po_orgi_shipdate=array_unique(explode(",",$po_orgi_shipdate));
				$tod_weeks="";$tod_recv_date="";
				foreach($po_orgi_shipdate as $orgi_id)
				{
					if($tod_weeks=="") $tod_weeks=$no_of_week_for_header[$orgi_id];else $tod_weeks.=",".$no_of_week_for_header[$orgi_id];
				}
				$tod_weeks=implode(",",array_unique(explode(",",$tod_weeks)));
				$lead_time=$status_val['lead_day'];
				$opd_week=implode(",",array_unique(explode(",",$opd_week)));
				$opd_recv_date=implode(",",array_unique(explode(",",$opd_recv_date)));
				$prod_cond=rtrim($status_val['pcode'],',');
				$prod_cond=implode(",",array_unique(explode(",",$prod_cond)));
				//$po_received_date
				
				
				if($status_key==1) //Confirm $conf_country_ship_date
				{
					$c_ship_date_con=array_unique(explode(",",$c_ship_date));$qnty_array_dd=array();
					$c_week_qty=0;
					foreach($c_ship_date_con as $c_date)
					{
								if($db_type==0) $c_date_con=date("d-m-y",strtotime($c_date));
								else $c_date_con=date("d-M-y",strtotime($c_date));
								$c_week=$no_of_week_for_header_calc[$c_date_con];
								
									$c_week_qty=$week_wise_order_qty[$style_key][$status_key][$c_week]['po_quantity'];
										//echo $c_week.'=='.$c_week_qty.'hj';
								$qnty_array_dd[$c_week]=$c_week_qty;
					}
					//print_r($qnty_array_dd);
						$tot_order_qty=0;$tot_order_qty_arr=array();
						foreach($week_counter_header as $mon_key=>$week)
						{
							//$order_qty_arr=array();
							foreach($week as $week_key)
							{
								 $tot_order_qty=$qnty_array_dd[$week_key];
								  $order_qty_arr[$week_key]=$tot_order_qty;
								  $tot_order_qty_arr[$style_key]+=$tot_order_qty;
							}
						}
						
				}
				else 
				{
						$po_orgi_shipdate=rtrim($status_val['orgi_ship_date'],',');
						$proj_orgi_shipdate=array_unique(explode(",",$po_orgi_shipdate));
						$qnty_array_mm=array();$proj_po_qty=0;
						//$qnty_arr=distribute_projection($tod_week, $tot_proj_po_qty);
							$week_check=array();
						foreach($proj_orgi_shipdate as $tod_key)
						{
							$tod_week=$no_of_week_for_header[$tod_key];
							if($week_check[$tod_week]=='')
							{
								$proj_po_qty=$style_wise_arr_qty[$style_key][$status_key][$tod_week]['po_qty'];
							
								$qnty_arr=distribute_projection($tod_week, $proj_po_qty);
								foreach($qnty_arr as $key=>$val)
								{
									$qnty_array_mm[$key]+=$val;
								}
								
								$week_check[$tod_week]=$proj_po_qty;
							}
						}
					
							$order_qty=0;$tot_order_qty_arr=array();
							foreach($week_counter_header as $mon_key=>$week)
							{
								foreach($week as $week_key)
								{
									$order_qty=$qnty_array_mm[$week_key];
									$order_qty_arr[$week_key]=$order_qty;
									$tot_order_qty_arr[$style_key]+=$order_qty;
								}
							}
				}
				
				$tot_po_qty=$tot_order_qty_arr[$style_key];
				
				$po_value=$status_val['po_val'];
				$unit_price=$po_value/$tot_po_qty;
				$job_no_row=count(array_unique(explode(",",$job_no)));
				if($job_no_row>1)
				{
					 $view_button="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$style_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button=$job_no;	
				}
				
				$tod_weeks_row=count(array_unique(explode(",",$tod_weeks)));
				if($tod_weeks_row>1)
				{
					 $view_button_tod="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$style_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_tod=$tod_weeks;	
				}
				
				$opd_week_row=count(array_unique(explode(",",$opd_week)));
				if($opd_week_row>1)
				{
					 $view_button_opd="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$style_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_opd=$opd_week;	
				}
				$opd_recv_date_row=count(array_unique(explode(",",$opd_recv_date)));
				if($opd_recv_date_row>1)
				{
					 $view_button_opd_date="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$style_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_opd_date=$opd_recv_date;	
				}
				$po_no_row=count(array_unique(explode(",",$po_no)));
				if($po_no_row>1)
				{
					 $view_button_po="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$style_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_po=$po_no;	
				}
				$dealing_marchant_con_row=count(array_unique(explode(",",$dealing_marchant_con)));
				//$bh_mer_con_row=count(array_unique(explode(",",$bh_mer)));
				$seasion_cond_row=count(array_unique(explode(",",$seasion_cond)));
				if($dealing_marchant_con_row>1)
				{
					 $view_button_deal="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$style_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_deal=$dealing_marchant_con;
					//$view_button_bh=$bh_mer;
					//$view_button_season=$seasion_cond;	
				}
				if($seasion_cond_row>1)
				{
					 $view_button_season="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$marchant_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_season=$seasion_cond;	
				}
				?>
                <tr style="font-size: 9px" align="center" bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
					<td width="30"><? echo $i; ?></td>
                    <td width="55" style="word-wrap:break-word; word-break: break-all;"><? echo $seasion_id;?></td>
                    <td width="100" style="word-wrap:break-word; word-break: break-all;" ><? echo $bh_mer; ?></td> 
					<td width="140" style="word-wrap:break-word; word-break: break-all;" ><? echo $view_button_deal; ?></td> 
                    <td width="55" style="word-wrap:break-word; word-break: break-all;">
					<? echo $view_button; ?></td>
					<td  width="120" style="word-wrap:break-word; word-break: break-all;"> <? echo $style_key; ?></td>
                    <td width="60" style="word-wrap:break-word; word-break: break-all;"><? echo $percent_all; ?></td>
                    <td  width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $copm_one_name; ?></td>
                    <td  width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $view_button; ?></td>
                    <td width="50"  style="word-wrap:break-word; word-break: break-all;"><? echo $gsm_weight; ?></td>
					<td  width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $view_button; ?></td>
                    <td  width="70" style="word-wrap:break-word; word-break: break-all; text-align:right">
					<?
							echo number_format($yarn_req);
				
					?>
                    </td>
					<td  width="60" style="word-wrap:break-word; word-break: break-all;">
					<?
					
					if($status_key==2)
					{
						echo "Booked";	
					}
					else
					{
						echo "Placed";
					}
					//echo $order_status[$row_data[csf('is_confirmed')]];
					?> 
                    </td>
					<td  width="45" style="word-wrap:break-word; word-break: break-all;">
					<?
				 		echo  $view_button_opd;
					
					?>
                    </td>
					<td  width="40" title="<? echo $opd_recv_date_full;?>" style="word-wrap:break-word; word-break: break-all;">
                    
					<?
					if($opd_recv_date!="" && $opd_recv_date !="0000-00-00" && $opd_recv_date!="0")
					{
						$opd_recv_date=$opd_recv_date;
					}
					echo  $view_button_opd_date;
					?>
                     
                    </td>
					<td width="45" title="Orgi. Ship Date" style="word-wrap:break-word; word-break: break-all; text-align: center">
					<? 
					//if($no_of_week_for_header[$countryship_date]!='') echo 'W'.$no_of_week_for_header[$countryship_date];else echo '';
					echo $view_button_tod;
					//echo $row_data[csf('country_ship_date')]; ?>
                    </td>
                    <td  width="70" style="word-wrap:break-word; word-break: break-all; text-align:right">
					<? echo number_format($tot_po_qty);?>
                    </td>
					<td  width="100" style="word-wrap:break-word; word-break: break-all; text-align: center">
                   
					<? echo $view_button_po; ?>
                    </td>
                    <td  width="40" style="word-wrap:break-word; word-break: break-all; text-align:left" >
					<? echo $prod_cond;?>
                    </td>
					<td  width="40" title="TOD-OPD" style="word-wrap:break-word; word-break: break-all; text-align: center">
					<? //echo $lead_time; ?>
                    </td>
					<td  width="35" title="PO Value/Po Qty" style="word-wrap:break-word; word-break: break-all; text-align:right">
					<? echo number_format($unit_price,2); ?>
                    </td>
                   
					<td width="90" title="Po Qty*Unit Price" style="word-wrap:break-word; word-break: break-all;text-align:right">
                   <? echo number_format($tot_po_qty*$unit_price,0);
				  	 $set_smv=number_format($status_val['set_smv'],2);
				    ?>
                    </td>
					<td width="40" title="Set SMV/Set Ratio" style="word-wrap:break-word; word-break: break-all;">
					<? 
					$smv_rate=0;
					$smv_rate=$set_smv/$status_val['set_ratio'];
					echo number_format($smv_rate,2); ?>
                    </td>
					
                    <td  width="60" title="Set SMV Per Pcs*Order Qty" style="word-wrap:break-word; word-break: break-all;text-align:right">
					<? echo number_format(($set_smv/$status_val['set_ratio'])*$tot_po_qty); ?>
                    </td>
                     <td  width="60" style="word-wrap:break-word; word-break: break-all;text-align:right">
					<? echo number_format($exfactory_data_array[$style_key][$status_key][ex_qnty]); ?>
                    </td>
                    <td width="60" title="Ex-Fact Qty*Unit Price" style="word-wrap:break-word; word-break: break-all;text-align:right">
                    <? echo  number_format($exfactory_data_array[$style_key][$status_key][ex_qnty]*$unit_price,0);
					//echo $exfactory_data_array[$marchant_key][$status_key][ex_qnty].'='.$status_val['unit_price'].'<br/>';
					 ?>
                    </td>
                    <?
					
					$week_qty=0;
					$week_check=array();
                   	foreach($week_counter_header as $mon_key=>$week)
					{
						foreach($week as $week_key)
						{
						?>
						<td   style="word-wrap:break-word; word-break: break-all;"  width="45" align="right">
						
						<? 
							 	$week_qty=$order_qty_arr[$week_key];
								 //echo number_format($week_qty,0);
								 if($week_qty>0) echo number_format($week_qty,0); else echo ' ';
								$tot_week_qty[$mon_key][$week_key]+=$week_qty;
								$tot_week_amount[$mon_key][$week_key]+=$week_qty*$unit_price;
								$smv_avg=0;
								$smv_avg=$smv_rate*$week_qty;
								$month_smv_arr[$mon_key]+=$smv_avg;
							
						?>
						</td>
						<?
						}
					}
					?>
                    </tr>
                    <?
					$total_order_qty+=$tot_po_qty;
					$total_yarn_req_qty+=$yarn_req;	
					$total_ex_fact_qty+=$exfactory_data_array[$style_key][$status_key][ex_qnty];
					$total_ex_fact_val+=$exfactory_data_array[$style_key][$status_key][ex_qnty]*$unit_price;
					$total_order_value+=$tot_po_qty*$unit_price;
					$total_order_smv+=($set_smv/$status_val['set_ratio'])*$tot_po_qty;
					//$total_week_qty+=$week_qty;
					//=====================================================================================================================
				$i++;
						}
				}
				?>
                 
				</table>
            
				
				</div>
                <table class="tbl_bottom"  style="font-size:9px" width="<? echo $td_width;?>px" id="report_table_footer"  cellpadding="0" cellspacing="0" border="1" rules="all" >
					<tr>
					<td width="30">&nbsp;</td>
                    <td width="55">&nbsp;</td>
                    <td width="100">&nbsp;</td>
					<td width="140">&nbsp;</td>
                    <td width="55">&nbsp;</td>
					<td width="120">&nbsp;</td>
                    <td width="60">&nbsp;</td>
                    <td width="120">&nbsp;</td>
                    <td width="120">&nbsp;</td>
                    <td width="50">&nbsp;</td>
					<td width="120">&nbsp;</td>
                    <td width="70"  style="word-wrap:break-word; word-break: break-all;font-size: smaller"  id="td_yarn_req_march"><? echo $total_yarn_req_qty;?></td>
					<td width="60">&nbsp;</td>
					<td width="45">&nbsp;</td>
					<td width="40">&nbsp;</td>
					<td width="45">&nbsp;</td>
                    <td width="70" style="word-wrap:break-word; word-break: break-all;font-size: smaller" id="td_po_qty_march"><? echo $total_order_qty;?></td>
					<td width="100">&nbsp;</td>
                    <td width="40">&nbsp; </td>
					<td width="40">&nbsp;</td>
					<td width="35"><? echo number_format($total_order_value/$total_order_qty,2);?></td>
					<td width="90" id="td_po_val_march" style="word-wrap:break-word; word-break: break-all;font-size: smaller"><? echo number_format($total_order_value);?></td>
					<td width="40"><? echo number_format($total_order_smv/$total_order_qty,2);?></td>
                    <td width="60"  style="word-wrap:break-word; word-break: break-all;font-size: smaller" id="total_order_smv"><? echo number_format($total_order_smv,0);?></td>
                    <td width="60" style="word-wrap:break-word; word-break: break-all;font-size: smaller" id="total_order_exfc_qty"><? echo number_format($total_ex_fact_qty,0);?></td>
                    <td width="60" style="word-wrap:break-word; word-break: break-all;font-size: smaller" id="total_order_exfc_val"><? echo number_format($total_ex_fact_val,0);?></td>
                     <?
					//foreach($week_for_header as $week_key)
					$total_week_qty=0; $total_week_amt=0; 
					foreach($week_counter_header as $mon_key=>$week)
					{
						foreach($week as $week_key)
						{
						?>
						<td width="45"  style="word-wrap:break-word; word-break: break-all;font-size: smaller"  align="center">
						
						<? 
						$total_week_qty=$tot_week_qty[$mon_key][$week_key];
						$total_week_amt=$tot_week_amount[$mon_key][$week_key];
						echo number_format($total_week_qty,0);
						
						$tot_mon_qty[$mon_key]+=$total_week_qty;
						$tot_mon_amount[$mon_key]+=$total_week_amt;
						?>
						</td>
						<?
						
						}
					}
            ?>
					</tr>
                    <tr style="display:none">
                    <td colspan="26">&nbsp;</td>
                     <?
					//foreach($week_for_header as $week_key)
					$total_week_qt=0; $m=1;
					foreach($week_counter_header as $mon_key=>$week)
					{
						?>
						<td width="" id="summary_footer_td_<? echo $mon_key?>" colspan="<? echo count($week);?>" style="word-wrap:break-word; word-break: break-all;font-size: smaller"   align="center">
						
						<? 
						$tot_smv=0;
						$tot_smv=$month_smv_arr[$mon_key]/$tot_mon_qty[$mon_key];
						$head_td_data=$tot_mon_qty[$mon_key].'$'.$tot_mon_amount[$mon_key].'$'.$tot_smv;
						echo $head_td_data;
						?>
						</td>
						<?
						
					}
            ?>
                    
                    </tr>
				</table>
			</fieldset>
		</div>
          <style>
			@font-face {
   				 font-family:"Arial Narrow"; font-size:11px;
   				 
			}
			TD{font-family:"Arial Narrow";font-size:11px;}
			TH{font-family:"Arial Narrow";font-size:11px;}
			
			.rpt_table TD{font-family: "Arial Narrow";font-size:11px;}
			.rpt_table TH{font-family:"Arial Narrow";font-size:11px;}
			
		</style>
	<?
	}
//===========================================================================================================================================================
	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data****$filename****$tot_rows****$cbo_search_type";
	exit();	
	
}
else if($action=="report_generate_job") //Job
{
 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company_name=str_replace("'","",$cbo_company_name);
	$cbo_search_type=str_replace("'","",$cbo_search_type);
	$cbo_bh_mer_name=str_replace("'","",$cbo_bh_mer_name);
	$txt_yarn_count=str_replace("'","",$txt_yarn_count);
	if($txt_yarn_count!='') $yarn_count_con="and yarn_count Like '%$txt_yarn_count%' ";else $yarn_count_con='';
	//$serch_by=str_replace("'","",$cbo_search_by);
	$buyer_id_cond="";
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{
				$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else
			{
				$buyer_id_cond="";
			}
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}
	

	
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_job_no=trim($txt_job_no);
	if($txt_job_no !="" || $txt_job_no !=0)
	{
		//$year = substr(str_replace("'","",$cbo_year_selection), -2); 
		//$job_no=$company_library[$company_name]."-".$year."-".str_pad($txt_job_no, 5, 0, STR_PAD_LEFT);
		$jobcond="and a.job_no_prefix_num in(".$txt_job_no.")";
	}
	else
	{
		$jobcond="";	
	}
	
	
	$date_cond='';
	/*if(str_replace("'","",$cbo_category_by)==1)
	{*/
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			$start_date=(str_replace("'","",$txt_date_from));
			$end_date=(str_replace("'","",$txt_date_to));
			
			$date_cond="and b.pub_shipment_date between '$start_date' and '$end_date'";
		}
	//}
	/*if(str_replace("'","",$cbo_category_by)==2)
	{
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			$start_date=(str_replace("'","",$txt_date_from));
			$end_date=(str_replace("'","",$txt_date_to));
			$date_cond="and b.po_received_date between '$start_date' and '$end_date'";
		}
	}*/
	//if (str_replace("'","",$txt_job_no)=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in (".str_replace("'","",$txt_job_no).") ";
	
	if(str_replace("'","",$txt_style_ref)!="") $style_ref_cond=" and a.style_ref_no like '%".str_replace("'","",$txt_style_ref)."%'"; else $style_ref_cond="";
	if(str_replace("'","",$txt_order_no)!="")
	{
		$ordercond=" and b.po_number like '%".str_replace("'","",$txt_order_no)."%'"; 
	}
	else
	{
		$ordercond="";
	}
	
	$team_cond="";
	if(str_replace("'","",$cbo_team_name)!=0) $team_cond=" and a.team_leader=".str_replace("'","",$cbo_team_name)."  ";
	if(str_replace("'","",$cbo_team_member)!=0) $team_cond.=" and a.dealing_marchant=".str_replace("'","",$cbo_team_member)."  ";
	$cbo_bh_mer_name=str_replace("0","",$cbo_bh_mer_name);
	if($cbo_bh_mer_name!=0 || $cbo_bh_mer_name!='') $bh_mer_name_con="and a.bh_merchant='$cbo_bh_mer_name' ";else $bh_mer_name_con='';
	 $cbo_year_selection=str_replace("'","",$cbo_year_selection);
	

	$start_ddd= strtotime(change_date_format($start_date,"dd-mm-yyyy","-"));
	$start_ddd=date("Y-m-d",$start_ddd);
	$start_end_date= strtotime(change_date_format($end_date,"dd-mm-yyyy","-"));
	$start_end_date=date("Y-m-d",$start_end_date);
	 //$dd= date('d-m-Y',strtotime($start_date));
	//$start_date_week= date($dd, time() - 1296000);//$start_date;
	//echo $last_day_this_month  = date($start_date_week, time() -1814400); //date('t-m-Y');
	
	$dateee = strtotime($start_ddd);
	$start_end_date = strtotime($start_end_date);
	
	$date_start = date("Y-m-d",strtotime("-28 day", $dateee));
	$date_end = date("Y-m-d",strtotime("+28 day", $start_end_date));
	//echo $date_end;
	//$end_date_week=  addday("$end_date", time() -1814400);//$start_date;//$start_date;
	if($db_type==0)
	{
		$date_start= change_date_format($date_start,"yyyy-mm-dd");
		$date_end= change_date_format($date_end,"yyyy-mm-dd");
	}
	else
	{
		$date_start= change_date_format($date_start,"dd-mm-yyyy","-",1);
		$date_end= change_date_format($date_end,"dd-mm-yyyy","-",1);
	}
	
	
	//echo "select week_date,week from week_of_year where week_date between '$date_start' and  '$date_end' order by week_date asc";die;
	// date("d-m-Y", time() - 1296000);
	$week_for_arr=array();$no_of_week_for_header=array();
	
	$sql_week_header=sql_select("select week_date,week from week_of_year where week_date between '$date_start' and  '$date_end' order by week_date asc");
	//echo "select week_date,week from week_of_year where week_date between '$date_start' and  '$date_end' order by week_date asc";
	
	
	$week_check_head=array();
	foreach ($sql_week_header as $row_week_header)
	{
		if($week_check_head[$row_week_header[csf("week")]]=='')
		{
			$week_counter_header[date("Y-M",strtotime($row_week_header[csf("week_date")]))][$row_week_header[csf("week")]]=$row_week_header[csf("week")];
			$week_check_head[$row_week_header[csf("week")]]=$week_counter_header[date("Y-M",strtotime($row_week_header[csf("week_date")]))][$row_week_header[csf("week")]];
		}
		$tmp=add_date($row_week_header[csf("week_date")],-1);
		if($db_type==0) $tmp_cond=date("d-m-y",strtotime($tmp));
		else $tmp_cond=date("d-M-y",strtotime($tmp));
		
		$no_of_week_for_header_calc[$tmp_cond]=$row_week_header[csf("week")];
		$no_of_week_for_header[$row_week_header[csf("week_date")]]=$row_week_header[csf("week")];
		$test[$row_week_header[csf("week")]]=$row_week_header[csf("week")];
	}
	//print_r($no_of_week_for_header_calc);
	unset($sql_week_header);
	$week_start_day=array();
	$week_end_day=array();$week_day_arr=array();
	//week_day
	$sql_week_start_end_date=sql_select("select week,min(week_date) as week_start_day, Max(week_date) as week_end_day from week_of_year where week_date between '$date_start' and  '$date_end' group by week");
	foreach ($sql_week_start_end_date as $row_week)
	{
		$week_start_day[$row_week[csf("week")]][week_start_day]=$row_week[csf("week_start_day")];
		$week_end_day[$row_week[csf("week")]][week_end_day]=$row_week[csf("week_end_day")];
		//$week_day_arr[$row_week[csf("week")]][$row_week[csf("week_start_day")]][week_first_day]=$row_week[csf("min_week_day")];
		$week_day_arr[$row_week[csf("week_end_day")]]['week_last_day']=$row_week[csf("last_week_day")];
	}
	unset($sql_week_start_end_date);
	//print_r($week_day_arr);
	function distribute_projection( $base_week, $qnty)
	{
		//5. Distribution ratios are: Base week 52%, Immediate before 24%, Week before immediate week 20%, Immediate week after base week 2%, next 2 weeks as 1%
		global $test;
		 
		$tmpbase_week=$test[$base_week-3];
		$k=0;
		for($i=0; $i<6; $i++)
		{
			$tmpbase_week++;
			if( $test[$tmpbase_week]=='') { $tmpbase_week=1; $k=0; }
			
			
			if($i==0) $distr_arr[$tmpbase_week]=($qnty*20)/100;
			if($i==1) $distr_arr[$tmpbase_week]=($qnty*24)/100;
			if($i==2) $distr_arr[$tmpbase_week]=($qnty*52)/100;
			if($i==3) $distr_arr[$tmpbase_week]=($qnty*2)/100;
			if($i==4) $distr_arr[$tmpbase_week]=($qnty*1)/100;
			if($i==5) $distr_arr[$tmpbase_week]=($qnty*1)/100;
			$k++;
			
		}
		 
		return $distr_arr;
		/*5000
		b=52
		b-1=21
		b-2=15
		b+1=1
		b+2=1*/
	}
	//echo "<per>";
	 //print_r(distribute_projection("2016-Dec",51, 5000)); die;


	
	if($template==1)
	{
	$po_wise_buyer=array();
	$buyer_wise_data=array();
	$po_id_arr=array();
	$report_data_arr=array();	
	
	$count_data=sql_select("select yarn_count,id from  lib_yarn_count  where status_active=1 and is_deleted=0 and yarn_count is not null $yarn_count_con");
	$yarn_id='';
	foreach($count_data as $row)
	{
			if($yarn_id=='') $yarn_id=$row[csf('id')];else $yarn_id.=",".$row[csf('id')];
			
	}

		$yarn_ids=count(array_unique(explode(",",$yarn_id)));
		$yarn_numIds=chop($yarn_id,','); $yarnIds_cond="";
		if($yarn_id!='' || $yarn_id!=0)
		{
			if($db_type==2 && $yarn_ids>1000)
			{
				$yarnIds_cond=" and (";
				$yIdsArr=array_chunk(explode(",",$yarn_numIds),990);
				//print_r($gate_outIds);
				foreach($yIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$yarnIds_cond.=" d.count_id  in($ids) or ";
				}
				$yarnIds_cond=chop($yarnIds_cond,'or ');
				$yarnIds_cond.=")";
			}
			else
			{
				$yarnIds_cond=" and  d.count_id  in($yarn_id)";
			}
		}
		$yarn_sql_data=("select a.job_no,b.is_confirmed,b.shipment_date as orgi_ship_date,b.id as po_id,d.count_id,d.copm_one_id,d.copm_two_id,d.percent_one,d.percent_two,sum(d.cons_qnty) as cons_qnty,e.construction,e.gsm_weight from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fab_yarn_cost_dtls d,wo_pre_cost_fabric_cost_dtls e where a.job_no=b.job_no_mst  and c.job_no_mst=a.job_no and b.id=c.po_break_down_id and d.job_no=a.job_no and 
	e.id=d.fabric_cost_dtls_id and  c.item_number_id= e.item_number_id and e.job_no=a.job_no and e.body_part_id in(1,20) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1   and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $team_cond $yarnIds_cond group by   b.id ,d.count_id,d.copm_one_id,d.copm_two_id,d.percent_one,d.percent_two,e.construction,e.gsm_weight,a.job_no,b.is_confirmed,b.shipment_date"); 
	$sql_result_y=sql_select($yarn_sql_data);
	$wo_fab_data_arr=array();
		$all_yarn_po_id="";
	foreach($sql_result_y as $row)
		{
			if($all_yarn_po_id=="") $all_yarn_po_id=$row[csf("po_id")]; else $all_yarn_po_id.=",".$row[csf("po_id")];
			$tod_week=$no_of_week_for_header[$row[csf('orgi_ship_date')]];
			$wo_fab_data_arr[$row[csf('job_no')]][$row[csf('is_confirmed')]]['percent_one']=$row[csf('percent_one')];
			$wo_fab_data_arr[$row[csf('job_no')]][$row[csf('is_confirmed')]]['percent_two']=$row[csf('percent_two')];
			$wo_fab_data_arr[$row[csf('job_no')]][$row[csf('is_confirmed')]]['copm_two_id']=$row[csf('copm_two_id')];
			$wo_fab_data_arr[$row[csf('job_no')]][$row[csf('is_confirmed')]]['copm_one_id']=$row[csf('copm_one_id')];
			$wo_fab_data_arr[$row[csf('job_no')]][$row[csf('is_confirmed')]]['construction'].=$row[csf('construction')].',';
			$wo_fab_data_arr[$row[csf('job_no')]][$row[csf('is_confirmed')]]['gsm_weight'].=$row[csf('gsm_weight')].',';
			$wo_fab_count_data_arr[$row[csf('job_no')]][$row[csf('is_confirmed')]]['count_id'].=$row[csf('count_id')].",";
			//$wo_yarn_data_arr[$row[csf('po_id')]]['cons_qnty']+=$row[csf('cons_qnty')];
		}
		unset($sql_result_y);
		$yarn_po_ids=count(array_unique(explode(",",$all_yarn_po_id)));
		$yarnPo_numIds=chop($all_yarn_po_id,','); $yarnPoIds_cond="";
		if($txt_yarn_count!='')
		{
			if($all_yarn_po_id!='' || $all_yarn_po_id!=0)
			{
				if($db_type==2 && $yarn_po_ids>1000)
				{
					$yarnPoIds_cond=" and (";
					$yPoIdsArr=array_chunk(explode(",",$yarnPo_numIds),990);
					//print_r($gate_outIds);
					foreach($yPoIdsArr as $ids)
					{
						$ids=implode(",",$ids);
						$yarnPoIds_cond.=" b.id  in($ids) or ";
					}
					$yarnPoIds_cond=chop($yarnPoIds_cond,'or ');
					$yarnPoIds_cond.=")";
				}
				else
				{
					$yarnPoIds_cond=" and  b.id  in($all_yarn_po_id)";
				}
			}
		}
		
		 $job_wise_arr_qty=array();
		 $sql_data_c=("select b.id as po_id,a.job_no,b.shipment_date as orgi_ship_date,b.is_confirmed,(b.po_quantity*a.total_set_qnty) as po_qty_pcs
		from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $team_cond $bh_mer_name_con order by a.job_no,b.shipment_date ");
		$sql_result_c=sql_select($sql_data_c);
		foreach($sql_result_c as $row)
		{
			if($row[csf('is_confirmed')]==2)
			{
				$tod_week=$no_of_week_for_header[$row[csf('orgi_ship_date')]];
				$job_wise_arr_qty[$row[csf('job_no')]][$row[csf('is_confirmed')]][$tod_week]['po_qty']+=$row[csf('po_qty_pcs')];
			}
		}	
		
		if($db_type==0) $date_dif="DATEDIFF(b.shipment_date, b.po_received_date) as lead_time_days";
		else  $date_dif="(b.shipment_date-b.po_received_date) as lead_time_days";

	 $sql_query=("select  $date_dif,a.job_no,a.job_no_prefix_num as prefix_job_no,a.set_smv,a.season_buyer_wise  as 	season,a.product_code,a.dealing_marchant,a.bh_merchant,a.style_ref_no,b.id,b.is_confirmed,b.po_number,b.po_received_date,b.shipment_date as orgi_ship_date,a.total_set_qnty as set_ratio,(b.po_quantity*a.total_set_qnty) as po_qty_pcs,b.po_total_price,a.avg_unit_price as unit_price ,c.country_ship_date,c.order_quantity,c.order_total
	 from wo_po_details_master a, wo_po_break_down b
	 LEFT JOIN wo_po_color_size_breakdown c on  c.po_break_down_id=b.id  and c.status_active=1 
	 where a.job_no=b.job_no_mst  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1   and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $bh_mer_name_con $jobcond $ordercond $team_cond $yarnPoIds_cond order by a.job_no "); 
	 
	$sql_data=sql_select($sql_query);
	$tot_rows=count($sql_data);
	$all_po_id="";
	$marchant_wise_arr=array();$week_wise_order_qty=array();
	foreach( $sql_data as $row)
	{
			if($all_po_id=="") $all_po_id=$row[csf("id")]; else $all_po_id.=",".$row[csf("id")];
			if($db_type==0) $c_date_con=date("d-m-y",strtotime($row[csf('country_ship_date')]));
			else $c_date_con=date("d-M-y",strtotime($row[csf('country_ship_date')]));
			$c_date_week=$no_of_week_for_header_calc[$c_date_con];
			//echo $c_date_week.'='.$c_date_con.',';
			if($row[csf('is_confirmed')]==1)
			{
				$order_val=$row[csf('order_total')];
			}
			else
			{
				$order_val=$row[csf('po_total_price')];
			}
		
			//$c_date_week=$no_of_week_for_header[$row[csf('country_ship_date')]];
			$job_wise_arr[$row[csf('job_no')]][$row[csf('is_confirmed')]]['po_qty']+=$row[csf('order_quantity')];
			$job_wise_arr[$row[csf('job_no')]][$row[csf('is_confirmed')]]['po_val']+=$order_val;
			$job_wise_arr[$row[csf('job_no')]][$row[csf('is_confirmed')]]['set_smv']+=$row[csf('set_smv')];
			$job_wise_arr[$row[csf('job_no')]][$row[csf('is_confirmed')]]['po_val']+=$row[csf('po_total_price')];
			$job_wise_arr[$row[csf('job_no')]][$row[csf('is_confirmed')]]['lead_day']+=$row[csf('lead_time_days')];
			$job_wise_arr[$row[csf('job_no')]][$row[csf('is_confirmed')]]['set_ratio']+=$row[csf('set_ratio')];
			$job_wise_arr[$row[csf('job_no')]][$row[csf('is_confirmed')]]['unit_price']+=$row[csf('unit_price')];
			$job_wise_arr[$row[csf('job_no')]][$row[csf('is_confirmed')]]['job_no']=$row[csf('prefix_job_no')];
			$job_wise_arr[$row[csf('job_no')]][$row[csf('is_confirmed')]]['style'].=$row[csf('style_ref_no')].',';
			//if($row[csf('season')]!='')
			//{
			$job_wise_arr[$row[csf('job_no')]][$row[csf('is_confirmed')]]['season'].=$row[csf('season')].',';
			//}
			$job_wise_arr[$row[csf('job_no')]][$row[csf('is_confirmed')]]['pcode'].=$row[csf('product_code')].',';
			$job_wise_arr[$row[csf('job_no')]][$row[csf('is_confirmed')]]['dealing_marchant'].=$row[csf('dealing_marchant')].',';
			if($row[csf('bh_merchant')]!='')
			{
			$job_wise_arr[$row[csf('job_no')]][$row[csf('is_confirmed')]]['bh_mer'].=$row[csf('bh_merchant')].',';
			}
			$job_wise_arr[$row[csf('job_no')]][$row[csf('is_confirmed')]]['po_no'].=$row[csf('po_number')].',';
			$job_wise_arr[$row[csf('job_no')]][$row[csf('is_confirmed')]]['po_id'].=$row[csf('id')].',';
			$job_wise_arr[$row[csf('job_no')]][$row[csf('is_confirmed')]]['po_received_date'].=$row[csf('po_received_date')].',';
			//if($row[csf('is_confirmed')]==2)
			//{
				$job_wise_arr[$row[csf('job_no')]][$row[csf('is_confirmed')]]['orgi_ship_date'].=$row[csf('orgi_ship_date')].',';
			//}
			$job_wise_arr[$row[csf('job_no')]][$row[csf('is_confirmed')]]['c_ship_date'].=$row[csf('country_ship_date')].',';
			if($row[csf('is_confirmed')]==1)
			{
				//echo $c_date_week.', ';
				$week_wise_order_qty[$row[csf('job_no')]][$row[csf('is_confirmed')]][$c_date_week]['po_quantity']+=$row[csf("order_quantity")];
			}
		
	}
	//print_r($week_wise_order_qty);
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
					$poIds_cond.=" b.id  in($ids) or ";
				}
				$poIds_cond=chop($poIds_cond,'or ');
				$poIds_cond.=")";
			}
			else
			{
				$poIds_cond=" and  b.id  in($all_po_id)";
			}
		}
		$exfactory_data_array=array();
		$exfactory_sql=("select a.job_no,b.is_confirmed,b.shipment_date as orgi_ship_date,
		sum(CASE WHEN c.entry_form!=85 THEN c.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		sum(CASE WHEN c.entry_form=85 THEN c.ex_factory_qnty ELSE 0 END) as return_qnty
		 from wo_po_details_master a, wo_po_break_down b,pro_ex_factory_mst c  where  a.job_no=b.job_no_mst and  c.po_break_down_id=b.id and  a.company_name=$company_name  and c.status_active=1 and c.is_deleted=0 $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $team_cond  $poIds_cond group by  a.job_no, b.is_confirmed,b.shipment_date");
		$exfactory_data=sql_select($exfactory_sql);
		foreach($exfactory_data as $row)
		{
				$tod_week=$no_of_week_for_header[$row[csf('orgi_ship_date')]];
				$exfactory_data_array[$row[csf('job_no')]][$row[csf('is_confirmed')]][ex_qnty]+=$row[csf('ex_factory_qnty')]-$row[csf('return_qnty')];
		}
		unset($exfactory_data);
		ob_start();
		$rowcount=0;
		foreach($week_counter_header as $mon_key=>$week)
		{
			$rowcount+=count($week);
		}
		 $rowb=$rowcount*2;
		 $td_width=1790+($rowcount*45)+$rowb;
	   $tot_month = datediff( 'm', $date_start,$date_end);
		for($i=0; $i<= $tot_month; $i++ )
		{
			$next_month=month_add($date_start,$i);
			$month_arr.=date("Y-M",strtotime($next_month)).',';
		}
	  $month_arr_id=rtrim($month_arr,',');
	if($tot_rows>0)
	{
	?>
		
        <script>
			var mon_week ='<? echo $month_arr_id; ?>';//month_smv_avg_
			var mon_week=mon_week.split(",");
			
		for (var k=0; k<mon_week.length; k++)
		{
			var mon_data=document.getElementById('summary_footer_td_'+mon_week[k]).innerHTML;
			//alert(mon_data);return;
			var mon_arr=mon_data.split("$");
			var mon_qnty=number_format(mon_arr[0],0);
			var mon_amt=number_format(mon_arr[1],0);
			var order_smv=number_format(mon_arr[2],2);
			var avg_rate=(number_format(mon_arr[1],0,'.','')/number_format(mon_arr[0],0,'.',''));
			document.getElementById('summary_header_td_qty_'+mon_week[k]).innerHTML=mon_qnty;
			document.getElementById('summary_header_td_val_'+mon_week[k]).innerHTML='$'+mon_amt;
			document.getElementById('summary_header_td_rate_'+mon_week[k]).innerHTML='FOB:'+number_format(avg_rate,2);
			document.getElementById('summary_header_td_smv_'+mon_week[k]).innerHTML='SMV:'+number_format(order_smv,2);
			
		}
		var yarn_req_qty=document.getElementById('td_yarn_req_march').innerHTML;
		var yarnreq_qty=number_format(yarn_req_qty,0,'.','');
		document.getElementById('yarn_summary_tot').innerHTML=yarnreq_qty;
		
		</script>
            <?
	  }
			?>
		<div style="width:<? echo $td_width+20;?>px">
		<fieldset style="width:100%;">	
      
        
			<table width="<? echo $td_width+20;?>px">
				<tr class="form_caption">
					<td align="left" style="margin-left:10px" colspan="<? echo $rowcount+26; ?>">Job Wise</td>
				</tr>
				<tr class="form_caption" align="center" style="display:none">
					<td colspan="<? echo $rowcount+26; ?>" align="center"><? //echo $company_library[$company_name]; ?></td>
				</tr>
			</table>
			<table class="rpt_table" width="<? echo $td_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
                  <tr>
					
                   <th  width="" colspan="11" rowspan="2">&nbsp;</th>
                     <th  rowspan="2" id="yarn_summary_tot"></th>
                     <th  width="" colspan="14" rowspan="2">&nbsp;</th>
                     <?
					
					foreach($week_counter_header as $mon_key=>$week)
					{
					?>
					<th   align="center" colspan="<? echo count($week);?>">
					<? 
					echo $mon_key;
					?>
					</th>
					<?
					}
            ?>
                   
                </tr>
                  <tr>
                     <?
					foreach($week_counter_header as $mon_key=>$week_val)
					{
					?>
					<th width="60" style="font-size:0.875em;"   align="center" colspan="<? echo count($week_val)?>">
					 		
                          
                               <b id="summary_header_td_qty_<? echo $mon_key ;?>"  style="text-align:left; font-size: 0.775em;"> </b> &nbsp;
                               <b id="summary_header_td_val_<? echo $mon_key ;?>" style="text-align: right;font-size: 0.775em;">  </b> &nbsp;
                               <b id="summary_header_td_rate_<? echo $mon_key ;?>"  style="text-align: right;font-size: 0.775em;"></b> &nbsp;
                               <b title="Avg Smv" id="summary_header_td_smv_<? echo $mon_key ;?>"  style="text-align: right;font-size: 0.775em;"></b>
					</th>
					<?
					}
            ?>
                   
                </tr>
                <tr>
					<th width="30">SL</th>
                    <th width="55">Season</th>
					<th width="80">BH Mer.</th>
                    <th width="70">FFL Mer.</th>
                    <th width="55">Job No</th>
					<th width="120">Style Ref</th>
                    <th width="60">Comp%</th>
                    <th width="120">Comp Name</th>
                    <th width="120">Const.</th>
                    <th width="50">GSM</th>
					<th width="120">Y/C</th>
                    <th width="70">Yarn Qty</th>
					<th width="60">Status</th>
					<th width="45">OPD</th>
					<th width="60">OPD Date</th>
					<th width="45">TOD</th>
                    <th width="70">Ord Qty</th>
					<th width="100">Order No</th>
                    <th width="40">Dept </th>
					<th width="40">Lead Time</th>
					<th width="40">Unit Price</th>
					<th width="90">Value</th>
					<th width="40">SMV/ Pcs</th>
                    <th width="90">Ord.SMV</th>
                    <th width="60">Delv Qty</th>
                    <th width="60">Delv.Val</th>
                     <?
					foreach($week_counter_header as $mon_key=>$week)
					{
						foreach($week as $week_key)
						{
							$start_week_date=$week_start_day[$week_key][week_start_day];
							$end_week_date=$week_end_day[$week_key][week_end_day];
							if($db_type==2)
							{
								$fisrtday=date("D",strtotime(change_date_format($start_week_date,"dd-mm-yyyy","-")));
								$lastday=date("D",strtotime(change_date_format($end_week_date,"dd-mm-yyyy","-")));
							}
							else
							{
								$fisrtday=date("D",strtotime(change_date_format($start_week_date,"dd-mm-yyyy")));
								$lastday=date("D",strtotime(change_date_format($end_week_date,"dd-mm-yyyy")));
							}
							$weekstart_date=explode("-",date("d-M",strtotime($start_week_date)));
							$weekstart_date=$weekstart_date[0].'/'.$weekstart_date[1];
							
						?>
						<th title="<? echo change_date_format($start_week_date,"dd-mm-yyyy","-").'('.$fisrtday.')'." To ".change_date_format($end_week_date,"dd-mm-yyyy","-").'('.$lastday.')'; ?>" width="45"  align="center">
						
						<? 
							echo 'W'.$week_key.'<br/>'.$weekstart_date;
						?>
						</th>
						<?
						}
					}
            ?>
                </tr>    
				</thead>
			</table>
			<div style="width:<? echo $td_width+20;?>px; max-height:245px; overflow-y:scroll" id="scroll_body">
			<table class="rpt_table" width="<? echo $td_width;?>px" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body_job">
	<?
			$condition= new condition();
			 $condition->company_name("=$cbo_company_name");
			 if(str_replace("'","",$cbo_buyer_name)>0)
			 {
				  $condition->buyer_name("=$cbo_buyer_name");
			 }
			 if(str_replace("'","",$txt_job_no) !='')
			 {
				  $condition->job_no_prefix_num("in($txt_job_no)");
			 }
			
			 if(str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
			 {
			 $condition->pub_shipment_date(" between '$start_date' and '$end_date'");
			 }
				
			 if(str_replace("'","",$txt_order_no)!='')
			 {
				$condition->po_number("=$txt_order_no"); 
			 }
			 if(str_replace("'","",$txt_season)!='')
			 {
				//$condition->season("=$txt_season"); 
			 }
			 $condition->init();
        	 $yarn= new yarn($condition);
		  	//echo $yarn->getQuery(); die;
			 $yarn_req_qty_arr=$yarn->getOrderWiseYarnQtyArray();
			//print_r($yarn_req_qty_arr);
				$i=1;$total_order_qty=0;$total_order_value=0;$total_week_qty=0;$total_yarn_req_qty=0;$total_order_smv=0;$total_ex_fact_val=0;$total_ex_fact_qty=0;
				foreach($job_wise_arr as $job_key=>$job_data)
				{  
				   	foreach($job_data as $status_key=>$status_val)
					{
						 
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";   
				$po_received_date=$status_val['po_received_date'];
				$countryship_date=$wo_color_data_arr[$job_key][$status_key]['c_shipdate'];
				$conf_country_ship_date=rtrim($wo_color_data_arr2[$job_key][$status_key]['shipdate'],',');
				$conf_country_ship_date=explode(",",$conf_country_ship_date);
				
				$percent_one=$wo_fab_data_arr[$job_key][$status_key]['percent_one'];
				$percent_two=$wo_fab_data_arr[$job_key][$status_key]['percent_two'];
				if($percent_one!=0 && $percent_two!=0)
				{
					//if($percent_two!=0)
					$percent_all=$percent_one.'/'.$percent_two;
				}
				else if($percent_one!=0 && $percent_two==0)
				{
					//if($percent_two!=0)
					$percent_all=$percent_one;
				}
				else
				{
					$percent_all='';	
				}
				$copm_two_id=$wo_fab_data_arr[$job_key][$status_key]['copm_two_id'];
				$copm_one_id=$wo_fab_data_arr[$job_key][$status_key]['copm_one_id'];
				if($copm_one_id!=0 && $copm_two_id!=0)
				{ 
					$copm_one_name=$composition[$copm_one_id].'/'.$composition[$copm_two_id];
				}
				else if($copm_one_id!=0 && $copm_two_id==0)
				{ 
					$copm_one_name=$composition[$copm_one_id];
				
				}
				else 
				{
					$copm_one_name='';
				}
				
				//$copm_one_name=$composition[$copm_one_id].'/'.$composition[$copm_two_id];
				
				$construction=rtrim($wo_fab_data_arr[$job_key][$status_key]['construction'],',');
				$construction=implode(",",array_unique(explode(",",$construction)));
				$gsm_weight=rtrim($wo_fab_data_arr[$job_key][$status_key]['gsm_weight'],',');
				$gsm_weight=implode(",",array_unique(explode(",",$gsm_weight)));
				$count_id=rtrim($wo_fab_count_data_arr[$job_key][$status_key]['count_id'],',');
				 $count_ids=array_unique(explode(",", $count_id));
				 $yarn_count_value="";
				foreach($count_ids as $val)
				{
					//if($val>0)
					//{
						if($yarn_count_value=="") $yarn_count_value=$lib_yarn_count_arr[$val]; else $yarn_count_value.=", ".$lib_yarn_count_arr[$val];
					//}
				}
				$seasion_ids=rtrim($status_val['season'],',');
				$seasion_ids=array_unique(explode(",",$seasion_ids));
				$seasion_id=$seasion_ids[0];
				$job_no=rtrim($status_val['job_no'],',');
				$style=rtrim($status_val['style'],',');
				$po_no=rtrim($status_val['po_no'],',');
				$po_id=rtrim($status_val['po_id'],',');
				$dealing_marchant=rtrim($status_val['dealing_marchant'],',');
				$bh_mers=rtrim($status_val['bh_mer'],',');
				$bh_mers=array_unique(explode(",",$bh_mers));
				$bh_mer=$bh_mers[0];
				//$bh_mer=implode(",",array_unique(explode(",",$bh_mer)));
				$po_received_date=rtrim($status_val['po_received_date'],',');
				$c_ship_date=rtrim($status_val['c_ship_date'],',');
				$job_no=implode(",",array_unique(explode(",",$job_no)));
				$style=implode(",",array_unique(explode(",",$style)));
				$po_no=implode(",",array_unique(explode(",",$po_no)));
				$po_id=array_unique(explode(",",$po_id));
			
				$yarn_req=0;
				foreach($po_id as $pid)
				{
				$yarn_req+=$yarn_req_qty_arr[$pid];
				}
				//echo $yarn_req.'<br>';
				
				$seasion_cond=implode(",",array_unique(explode(",",$seasion_id)));
				/*$seasion_cond="";
				foreach($seasion_id as $sid)
				{
					if($seasion_cond=="") $seasion_cond=$lib_season_name_arr[$sid];else $seasion_cond.=",".$lib_season_name_arr[$sid];
				}*/
				$dealing_marchant=array_unique(explode(",",$dealing_marchant));
				$dealing_marchant_con="";
				foreach($dealing_marchant as $mid)
				{
					if($dealing_marchant_con=="") $dealing_marchant_con=$team_member_arr[$mid];else $dealing_marchant_con.=",".$team_member_arr[$mid];
				}
				$po_received_date=array_unique(explode(",",$po_received_date));
				$opd_week="";$opd_recv_date="";
				$lead_time=0;
				foreach($po_received_date as $op_id)
				{
					if($opd_week=="") $opd_week=$no_of_week_for_header[$op_id];else $opd_week.=", ".$no_of_week_for_header[$op_id];
					//if($opd_recv_date=="") $opd_recv_date=change_date_format($op_id);else $opd_recv_date.=", ".change_date_format($op_id);
					if($opd_recv_date=="") $opd_recv_date=date("d-M",strtotime($op_id));else $opd_recv_date.=",".date("d-M",strtotime($op_id));
					if($opd_recv_date_full=="") $opd_recv_date_full=date("d-M-y",strtotime($op_id));else $opd_recv_date_full.=",".date("d-M-y",strtotime($op_id));
				//$lead_time = datediff( 'd', $op_id,$countryship_date);
				}
				$po_orgi_shipdate=rtrim($status_val['orgi_ship_date'],',');
				$po_orgi_shipdate=array_unique(explode(",",$po_orgi_shipdate));
				$tod_weeks="";$tod_recv_date="";
				foreach($po_orgi_shipdate as $orgi_id)
				{
					//echo $orgi_id;
					if($tod_weeks=="") $tod_weeks=$no_of_week_for_header[$orgi_id];else $tod_weeks.=",".$no_of_week_for_header[$orgi_id];
				}
				//echo $tod_weeks;
				$tod_weeks=implode(",",array_unique(explode(",",$tod_weeks)));
				
				$lead_time =$status_val['lead_day'];
				$opd_week=implode(",",array_unique(explode(", ",$opd_week)));
				$opd_recv_date=implode(",",array_unique(explode(", ",$opd_recv_date)));
				$pcode=rtrim($status_val['pcode'],',');
				$prod_cond=implode(",",array_unique(explode(",",$pcode)));
				if($prod_cond!='') $prod_cond=$prod_cond;else $prod_cond='';
				if($status_key==1) //Confirm $conf_country_ship_date
				{
					$c_ship_date_con=array_unique(explode(",",$c_ship_date));$qnty_array_dd=array();
					$c_week_qty=0;
					foreach($c_ship_date_con as $c_date)
					{
								if($db_type==0) $c_date_con=date("d-m-y",strtotime($c_date));
								else $c_date_con=date("d-M-y",strtotime($c_date));
								$c_week=$no_of_week_for_header_calc[$c_date_con];
								$c_week_qty=$week_wise_order_qty[$job_key][$status_key][$c_week]['po_quantity'];
										//echo $c_week.'=='.$c_week_qty.'hj';
								$qnty_array_dd[$c_week]=$c_week_qty;
					}
					//print_r($qnty_array_dd);
						$tot_order_qty=0;$tot_order_qty_arr=array();
						foreach($week_counter_header as $mon_key=>$week)
						{
							foreach($week as $week_key)
							{
								 $tot_order_qty=$qnty_array_dd[$week_key];
								 $order_qty_arr[$week_key]=$tot_order_qty;
								  $tot_order_qty_arr[$job_key]+=$tot_order_qty;
							}
						}
						
				}
				else 
				{
						$po_orgi_shipdate=rtrim($status_val['orgi_ship_date'],',');
						$proj_orgi_shipdate=array_unique(explode(",",$po_orgi_shipdate));
						$qnty_array_mm=array();$proj_po_qty=0;
						$week_check=array();
						foreach($proj_orgi_shipdate as $tod_key)
						{
							$tod_week=$no_of_week_for_header[$tod_key];
							if($week_check[$tod_week]=='')
							{
								 $proj_po_qty=$job_wise_arr_qty[$job_key][$status_key][$tod_week]['po_qty'];
							
								$qnty_arr=distribute_projection($tod_week, $proj_po_qty);
								foreach($qnty_arr as $key=>$val)
								{
									$qnty_array_mm[$key]+=$val;
								}
								
								$week_check[$tod_week]=$proj_po_qty;
							}
						}
					
							$order_qty=0;$tot_order_qty_arr=array();
							foreach($week_counter_header as $mon_key=>$week)
							{
								foreach($week as $week_key)
								{
									$order_qty=$qnty_array_mm[$week_key];
									$order_qty_arr[$week_key]=$order_qty;
									$tot_order_qty_arr[$job_key]+=$order_qty;
								}
							}
				}
				$tot_po_qty=$tot_order_qty_arr[$job_key];
				$po_value=$status_val['po_val'];
				//echo $po_value.'='.$tot_po_qty.', ';
				if($tot_po_qty)
				{
				$unit_price=$po_value/$tot_po_qty;
				} else $unit_price=0;
				
				$job_no_row=count(array_unique(explode(",",$status_val['job_no'])));
				$style_key_row=count(array_unique(explode(",",$style)));
				if($job_no_row>1)
				{
					 $view_button="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$job_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button=$job_no;	
				}
				if($style_key_row>1)
				{
					 $view_button_style="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$job_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_style=$style;	
				}
				
				$tod_weeks_row=count(array_unique(explode(",",$tod_weeks)));
				if($tod_weeks_row>1)
				{
					 $view_button_tod="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$job_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_tod=$tod_weeks;	
				}
				$opd_week_row=count(array_unique(explode(",",$opd_week)));
				if($opd_week_row>1)
				{
					 $view_button_opd="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$job_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_opd=$opd_week;	
				}
				$opd_recv_date_row=count(array_unique(explode(",",$opd_recv_date)));
				if($opd_recv_date_row>1)
				{
					 $view_button_opd_date="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$job_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_opd_date=$opd_recv_date;	
				}
				$po_no_row=count(array_unique(explode(",",$po_no)));
				if($po_no_row>1)
				{
					 $view_button_po="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$job_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_po=$po_no;	
				}
				$yarn_count_value_row=count(array_unique(explode(",",$yarn_count_value)));
				$style_row=count(array_unique(explode(",",$style)));
				$construction_row=count(array_unique(explode(",",$construction)));
				$prod_cond_row=count(array_unique(explode(",",$prod_cond)));
				if($yarn_count_value_row>1)
				{
					 $view_button_count="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$job_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_count=$yarn_count_value;	
				}
				if($construction_row>1)
				{
					 $view_button_const="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$job_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."',,'".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_const=$construction;	
				}
				if($prod_cond_row>1)
				{
					 $view_button_dept="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$job_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."',,'".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_dept=$prod_cond;	
				}
				
				
				?>
                <tr style="font:'Arial Narrow'; font-size:9px" align="center" bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
					<td width="30"><? echo $i; ?></td>
                    <td width="55" style="word-wrap:break-word; word-break: break-all;"><? echo $seasion_id;?></td>
                    <td width="80" style="word-wrap:break-word; word-break: break-all;" ><? echo $bh_mer; ?></td> 
					<td width="70" style="word-wrap:break-word; word-break: break-all;" ><? echo $dealing_marchant_con; ?></td> 
                    <td width="55" style="word-wrap:break-word; word-break: break-all;">
					<? echo $status_val['job_no'];//$buyer_short_name_library[$row_data[csf('buyer_name')]]; ?></td>
					<td  width="120" style="word-wrap:break-word; word-break: break-all;"> <? echo $view_button_style; ?></td>
                   
                    <td width="60" style="word-wrap:break-word; word-break: break-all;"><? echo $percent_all; ?></td>
                    <td  width="120"><div style="word-wrap:break-word:120px;"><? echo $copm_one_name; ?></div></td>
                    <td  width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $view_button_const;?></td>
                    <td width="50"  style="word-wrap:break-word; word-break: break-all;"><? echo $gsm_weight; ?></td>
					<td  width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $view_button_count; ?></td>
                    <td  width="70" style="word-wrap:break-word; word-break: break-all; text-align:right">
					<?
							echo number_format($yarn_req);
				
					?>
                    </td>
					<td  width="60" style="word-wrap:break-word; word-break: break-all;">
					<?
					
					if($status_key==2)
					{
						echo "Booked";	
					}
					else
					{
						echo "Placed";
					}
					//echo $order_status[$row_data[csf('is_confirmed')]];
					?> 
                    </td>
					<td  width="45" style="word-wrap:break-word; word-break: break-all;">
					<?
					echo $view_button_opd;
					
					?>
                    </td>
					<td  width="60" title="<? echo $opd_recv_date_full;?>" style="word-wrap:break-word; word-break: break-all;">
                    
					<?
					if($opd_recv_date!="" && $opd_recv_date !="0000-00-00" && $opd_recv_date!="0")
					{
						$opd_recv_date=$opd_recv_date;
					}
					echo $view_button_opd_date;
					?>
                      
                    </td>
					<td width="45" title="Orgi Ship Date" style="word-wrap:break-word; word-break: break-all; text-align: center">
						<?  echo $view_button_tod; ?>
                    </td>
                    <td  width="70" style="word-wrap:break-word; word-break: break-all; text-align:right">
					<? echo number_format($tot_po_qty);?>
                    </td>
					<td  width="100" style="word-wrap:break-word; word-break: break-all; text-align: center">
                   
					<? echo $view_button_po; ?>
                    </td>
                    <td  width="40" style="word-wrap:break-word; word-break: break-all; text-align:left" >
					<? echo $view_button_dept;?>
                    </td>
					<td  width="40" title="TOD-OPD" style="word-wrap:break-word; word-break: break-all; text-align: center">
					<? //echo $lead_time; ?>
                    </td>
					<td  width="40" title="PO Value/Po Qty" style="word-wrap:break-word; word-break: break-all; text-align:right">
					<? echo number_format($unit_price,2); ?>
                    </td>
                   
					<td width="90" title="Po Qty*Unit Price" style="word-wrap:break-word; word-break: break-all;text-align:right">
                   <? echo number_format($tot_po_qty*$unit_price,0);
				  	 $set_smv=number_format($status_val['set_smv'],2);
				    ?>
                    </td>
					<td width="40" title="Set SMV/Set Ratio" style="word-wrap:break-word; word-break: break-all;">
					<? 
					$smv_rate=0;
					$smv_rate=$set_smv/$status_val['set_ratio'];
					//echo $set_smv/$row_data[csf('set_ratio')];
					echo number_format($smv_rate,2); ?>
                    </td>
					
                    <td  width="90" title="Set SMV Per Pcs*Order Qty" style="word-wrap:break-word; word-break: break-all;text-align:right">
					<? echo number_format(($set_smv/$status_val['set_ratio'])*$tot_po_qty); ?>
                    </td>
                     <td  width="60" style="word-wrap:break-word; word-break: break-all;text-align:right">
					<? echo number_format($exfactory_data_array[$job_key][$status_key][ex_qnty]); ?>
                    </td>
                    <td width="60" title="Ex-Fact Qty*Unit Price" style="word-wrap:break-word; word-break: break-all;text-align:right">
                    <? echo  number_format($exfactory_data_array[$job_key][$status_key][ex_qnty]*$unit_price,0);
					//echo $exfactory_data_array[$marchant_key][$status_key][ex_qnty].'='.$status_val['unit_price'].'<br/>';
					 ?>
                    </td>
                    <?
					/*$qnty_array=array();
					if($status_key==1) //Confirm
					{
						$c_ship_date_con=array_unique(explode(",",$c_ship_date));
						foreach($c_ship_date_con as $c_date)
						{
							if($db_type==0) $c_date_cond=date("d-m-y",strtotime($c_date));
							else $c_date_cond=date("d-M-y",strtotime($c_date));
							$qnty_array[$no_of_week_for_header_calc[$c_date_cond]]=$week_wise_order_qty[$job_key][$status_key][$no_of_week_for_header_calc[$c_date_cond]]['po_quantity'];
						}
					}
					else
					{
						foreach($po_orgi_shipdate as $tod_key)
						{
							$qnty_array=distribute_projection($no_of_week_for_header[$tod_key], $status_val['po_qty']);
						}
					}*/
					$week_qty=0;
					$week_check=array();
                   	foreach($week_counter_header as $mon_key=>$week)
					{
						foreach($week as $week_key)
						{
						?>
						<td   style="word-wrap:break-word; word-break: break-all;"  width="45" align="right">
						<? 
						
								$week_qty=$order_qty_arr[$week_key];
								 if($week_qty>0) echo number_format($week_qty,0); else echo 0;
								$tot_week_qty[$mon_key][$week_key]+=$week_qty;
								//echo $week_qty.'*'.$unit_price.',';
								$tot_week_amount[$mon_key][$week_key]+=$week_qty*$unit_price;
								$smv_avg=0;
								$smv_avg=$smv_rate*$week_qty;
								$month_smv_arr[$mon_key]+=$smv_avg;
								//echo $week_qty*$unit_price.',';
						?>
						</td>
						<?
						}
					}
					?>
                    </tr>
                    <?
					$total_order_qty+=$tot_po_qty;
					$total_yarn_req_qty+=$yarn_req;	
					$total_ex_fact_qty+=$exfactory_data_array[$job_key][$status_key][ex_qnty];
					$total_ex_fact_val+=$exfactory_data_array[$job_key][$status_key][ex_qnty]*$unit_price;
					$total_order_value+=$tot_po_qty*$unit_price;
					$total_order_smv+=($set_smv/$status_val['set_ratio'])*$tot_po_qty;
					//$total_week_qty+=$week_qty;
					//=====================================================================================================================
				$i++;
					 }
				}
				?>
				</table>
				</div>
                <table style="font:'Arial Narrow'; font-size:9px" class="tbl_bottom" width="<? echo $td_width;?>px" cellpadding="0" cellspacing="0" id="report_table_footer"  border="1" rules="all" >
					<tr>
					<td width="30">&nbsp;</td>
                    <td width="55">&nbsp;</td>
                    <td width="80">&nbsp;</td>
					<td width="70">&nbsp;</td>
                    <td width="55">&nbsp;</td>
					<td width="120">&nbsp;</td>
                    <td width="60">&nbsp;</td>
                    <td width="120">&nbsp;</td>
                    <td width="120">&nbsp;</td>
                    <td width="50">&nbsp;</td>
					<td width="120">&nbsp;</td>
                    <td width="70" id="td_yarn_req_march" style="word-wrap:break-word; word-break: break-all; font-size: smaller" ><? echo $total_yarn_req_qty;?></td>
					<td width="60">&nbsp;</td>
					<td width="45">&nbsp;</td>
					<td width="60">&nbsp;</td>
					<td width="45">&nbsp;</td>
                    <td width="70" style="word-wrap:break-word; word-break: break-all; font-size: smaller" id="td_po_qty_march"><? echo $total_order_qty;?></td>
					<td width="100">&nbsp;</td>
                    <td width="40">&nbsp; </td>
					<td width="40">&nbsp;</td>
					<td width="40"><? echo number_format($total_order_value/$total_order_qty,2);?></td>
					<td width="90" id="td_po_val_march" style="word-wrap:break-word; word-break: break-all; font-size: smaller"><? echo number_format($total_order_value);?></td>
					<td width="40" style="word-wrap:break-word; word-break: break-all; font-size: smaller" id="td_po_qty_march"><? echo number_format($total_order_smv/$total_order_qty,2);?></th>
                    <td width="90"  style="word-wrap:break-word; word-break: break-all; font-size: smaller" id="total_order_smv"><? echo number_format($total_order_smv,0);?></td>
                    <td width="60" style="word-wrap:break-word; word-break: break-all; font-size: smaller" id="total_order_exfc_qty"><? echo number_format($total_ex_fact_qty,0);?></td>
                    <td width="60" style="word-wrap:break-word; word-break: break-all; font-size: smaller" id="total_order_exfc_val"><? echo number_format($total_ex_fact_val,0);?></td>
                     <?
					//foreach($week_for_header as $week_key)
					$total_week_qty=0; $total_week_amt=0; 
					foreach($week_counter_header as $mon_key=>$week)
					{
						foreach($week as $week_key)
						{
						?>
						<td width="45" style="word-wrap:break-word; word-break: break-all;font-size: smaller"   align="center">
						
						<? 
						$total_week_qty=$tot_week_qty[$mon_key][$week_key];
						$total_week_amt=$tot_week_amount[$mon_key][$week_key];
						if($total_week_amt) $total_week_amt=$total_week_amt;else $total_week_amt=0;
						echo number_format($total_week_qty,0);
						$tot_mon_qty[$mon_key]+=$total_week_qty;
						$tot_mon_amount[$mon_key]+=$total_week_amt;
						?>
						</td>
						<?
						
						}
					}
            ?>
					</tr>
                     <tr style=" display:none" >
					<td colspan="26">&nbsp;</td>
                     <?
					//foreach($week_for_header as $week_key)
					$total_week_qt=0;
					foreach($week_counter_header as $mon_key=>$week)
					{
						
						?>
						<td width="" id="summary_footer_td_<? echo $mon_key?>" colspan="<? echo count($week);?>" title="<? echo $tot_week_amount[$mon_key][$week_key];?>" style="word-wrap:break-word; word-break: break-all;"   align="center">
						
						<? 
						$tot_smv=0;
						if($month_smv_arr[$mon_key])
						{
						$tot_smv=$month_smv_arr[$mon_key]/$tot_mon_qty[$mon_key];
						}
						if($tot_mon_amount[$mon_key]) $tot_mon_amount[$mon_key]=$tot_mon_amount[$mon_key];else $tot_mon_amount[$mon_key]=0;
						
						$head_td_data=$tot_mon_qty[$mon_key].'$'.$tot_mon_amount[$mon_key].'$'.$tot_smv;
						echo $head_td_data;
						
						?>
                        
						</td>
						<?
					}
            ?>
                    </tr>
				</table>
			</fieldset>
		</div>
         <style>
			@font-face {
   				 font-family:"Arial Narrow"; font-size:11px;
   				 
			}
			TD{font-family:"Arial Narrow";font-size:10px;}
			TH{font-family:"Arial Narrow";font-size:11px;}
			
			.rpt_table TD{font-family: "Arial Narrow";font-size:11px;}
			.rpt_table TH{font-family:"Arial Narrow";font-size:11px;}
			
		</style>
	<?
	}
//===========================================================================================================================================================
	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data****$filename****$tot_rows****$cbo_search_type";
	exit();	
	
}
else if($action=="report_generate_composition") //Composition Wise
{
 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company_name=str_replace("'","",$cbo_company_name);
	$cbo_search_type=str_replace("'","",$cbo_search_type);
	$cbo_bh_mer_name=str_replace("'","",$cbo_bh_mer_name);
	$txt_yarn_count=str_replace("'","",$txt_yarn_count);
	if($txt_yarn_count!='') $yarn_count_con="and yarn_count Like '%$txt_yarn_count%' ";else $yarn_count_con='';
	//echo $yarn_count_con;die;
	$buyer_id_cond="";
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{
				$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else
			{
				$buyer_id_cond="";
			}
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}
	

	
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_job_no=trim($txt_job_no);
	if($txt_job_no !="" || $txt_job_no !=0)
	{
		//$year = substr(str_replace("'","",$cbo_year_selection), -2); 
		//$job_no=$company_library[$company_name]."-".$year."-".str_pad($txt_job_no, 5, 0, STR_PAD_LEFT);
		$jobcond="and a.job_no_prefix_num in(".$txt_job_no.")";
	}
	else
	{
		$jobcond="";	
	}
	
	
	$date_cond='';
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			$start_date=(str_replace("'","",$txt_date_from));
			$end_date=(str_replace("'","",$txt_date_to));
			
			$date_cond="and b.pub_shipment_date between '$start_date' and '$end_date'";
		}
	
	if(str_replace("'","",$txt_style_ref)!="") $style_ref_cond=" and a.style_ref_no like '%".str_replace("'","",$txt_style_ref)."%'"; else $style_ref_cond="";
	if(str_replace("'","",$txt_order_no)!="")
	{
		$ordercond=" and b.po_number like '%".str_replace("'","",$txt_order_no)."%'"; 
	}
	else
	{
		$ordercond="";
	}
	
	$team_cond="";
	if(str_replace("'","",$cbo_team_name)!=0) $team_cond=" and a.team_leader=".str_replace("'","",$cbo_team_name)."  ";
	if(str_replace("'","",$cbo_team_member)!=0) $team_cond.=" and a.dealing_marchant=".str_replace("'","",$cbo_team_member)."  ";
	$cbo_bh_mer_name=str_replace("0","",$cbo_bh_mer_name);
	if($cbo_bh_mer_name!=0 || $cbo_bh_mer_name!='') $bh_mer_name_con="and a.bh_merchant='$cbo_bh_mer_name' ";else $bh_mer_name_con='';
	 $cbo_year_selection=str_replace("'","",$cbo_year_selection);
	

	$start_ddd= strtotime(change_date_format($start_date,"dd-mm-yyyy","-"));
	$start_ddd=date("Y-m-d",$start_ddd);
	$start_end_date= strtotime(change_date_format($end_date,"dd-mm-yyyy","-"));
	$start_end_date=date("Y-m-d",$start_end_date);
	
	$dateee = strtotime($start_ddd);
	$start_end_date = strtotime($start_end_date);
	
	$date_start = date("Y-m-d",strtotime("-28 day", $dateee));
	$date_end = date("Y-m-d",strtotime("+28 day", $start_end_date));
	//echo $date_end;
	//$end_date_week=  addday("$end_date", time() -1814400);//$start_date;//$start_date;
	if($db_type==0)
	{
		$date_start= change_date_format($date_start,"yyyy-mm-dd");
		$date_end= change_date_format($date_end,"yyyy-mm-dd");
	}
	else
	{
		$date_start= change_date_format($date_start,"dd-mm-yyyy","-",1);
		$date_end= change_date_format($date_end,"dd-mm-yyyy","-",1);
	}
	
	
	//echo "select week_date,week from week_of_year where week_date between '$date_start' and  '$date_end' order by week_date asc";die;
	// date("d-m-Y", time() - 1296000);
	$week_for_arr=array();$no_of_week_for_header=array();
	$sql_week_header=sql_select("select week_date,week from week_of_year where week_date between '$date_start' and  '$date_end' order by week_date asc");
	$week_check_head=array();
	foreach ($sql_week_header as $row_week_header)
	{
		if($week_check_head[$row_week_header[csf("week")]]=='')
		{
			$week_counter_header[date("Y-M",strtotime($row_week_header[csf("week_date")]))][$row_week_header[csf("week")]]=$row_week_header[csf("week")];
			$week_check_head[$row_week_header[csf("week")]]=$week_counter_header[date("Y-M",strtotime($row_week_header[csf("week_date")]))][$row_week_header[csf("week")]];
		}
		
		$tmp=add_date($row_week_header[csf("week_date")],-1);
		if($db_type==0) $tmp_cond=date("d-m-y",strtotime($tmp));
		else $tmp_cond=date("d-M-y",strtotime($tmp));
		$no_of_week_for_header_calc[$tmp_cond]=$row_week_header[csf("week")];
			
		$no_of_week_for_header[$row_week_header[csf("week_date")]]=$row_week_header[csf("week")];
		$test[$row_week_header[csf("week")]]=$row_week_header[csf("week")];
	}
	unset($sql_week_header);
	$week_start_day=array();
	$week_end_day=array();$week_day_arr=array();
	//week_day
	$sql_week_start_end_date=sql_select("select week,min(week_date) as week_start_day, Max(week_date) as week_end_day from week_of_year where week_date between '$date_start' and  '$date_end' group by week");
	foreach ($sql_week_start_end_date as $row_week)
	{
		$week_start_day[$row_week[csf("week")]][week_start_day]=$row_week[csf("week_start_day")];
		$week_end_day[$row_week[csf("week")]][week_end_day]=$row_week[csf("week_end_day")];
		//$week_day_arr[$row_week[csf("week")]][$row_week[csf("week_start_day")]][week_first_day]=$row_week[csf("min_week_day")];
		$week_day_arr[$row_week[csf("week_end_day")]]['week_last_day']=$row_week[csf("last_week_day")];
	}
	unset($sql_week_start_end_date);
	//print_r($week_day_arr);
	function distribute_projection( $base_week, $qnty)
	{
		//5. Distribution ratios are: Base week 52%, Immediate before 24%, Week before immediate week 20%, Immediate week after base week 2%, next 2 weeks as 1%
		global $test;
		 
		$tmpbase_week=$test[$base_week-3];
		$k=0;
		for($i=0; $i<6; $i++)
		{
			$tmpbase_week++;
			if( $test[$tmpbase_week]=='') { $tmpbase_week=1; $k=0; }
			
			
			if($i==0) $distr_arr[$tmpbase_week]=($qnty*20)/100;
			if($i==1) $distr_arr[$tmpbase_week]=($qnty*24)/100;
			if($i==2) $distr_arr[$tmpbase_week]=($qnty*52)/100;
			if($i==3) $distr_arr[$tmpbase_week]=($qnty*2)/100;
			if($i==4) $distr_arr[$tmpbase_week]=($qnty*1)/100;
			if($i==5) $distr_arr[$tmpbase_week]=($qnty*1)/100;
			$k++;
			
		}
		 
		return $distr_arr;
		/*5000
		b=52
		b-1=21
		b-2=15
		b+1=1
		b+2=1*/
	}
	//echo "<per>";
	 //print_r(distribute_projection("2016-Dec",51, 5000)); die;


	
	if($template==1)
	{
	$po_wise_buyer=array();
	$buyer_wise_data=array();
	$wo_color_data_arr2=array();
	$week_wise_order_qty_arr=array();	
	//echo "select yarn_count,id from  lib_yarn_count  where status_active=1 and is_deleted=0 and yarn_count is not null $yarn_count_con";
	
	 $count_data=sql_select("select yarn_count,id from  lib_yarn_count  where status_active=1 and is_deleted=0 and yarn_count is not null $yarn_count_con");
	$yarn_id='';
	foreach($count_data as $row)
	{
			if($yarn_id=='') $yarn_id=$row[csf('id')];else $yarn_id.=",".$row[csf('id')];
	}
		$yarn_ids=count(array_unique(explode(",",$yarn_id)));
		$yarn_numIds=chop($yarn_id,','); $yarnIds_cond="";
		if($txt_yarn_count!='')
		{
			if($yarn_id!='' || $yarn_id!=0)
			{
				if($db_type==2 && $yarn_ids>1000)
				{
					$yarnIds_cond=" and (";
					$yIdsArr=array_chunk(explode(",",$yarn_numIds),990);
					foreach($yIdsArr as $ids)
					{
						$ids=implode(",",$ids);
						$yarnIds_cond.=" d.count_id  in($ids) or ";
					}
					$yarnIds_cond=chop($yarnIds_cond,'or ');
					$yarnIds_cond.=")";
				}
				else
				{
					$yarnIds_cond=" and  d.count_id  in($yarn_id)";
				}
			}
		}
	 	$comp_wise_arr_qty=array();
		 $sql_data_c=("select b.id as po_id,a.style_ref_no,b.shipment_date as orgi_ship_date,b.is_confirmed,(b.po_quantity*a.total_set_qnty) as po_qty_pcs
		from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $team_cond $bh_mer_name_con order by a.style_ref_no,b.shipment_date ");
		$sql_result_c=sql_select($sql_data_c);
		foreach($sql_result_c as $row)
		{
			if($row[csf('is_confirmed')]==2)
			{
				$tod_week=$no_of_week_for_header[$row[csf('orgi_ship_date')]];
				$comp_wise_arr_qty[$row[csf('style_ref_no')]][$row[csf('is_confirmed')]][$tod_week]['po_qty']+=$row[csf('po_qty_pcs')];
				
			}
		}	
		
	if($db_type==0) $date_dif="DATEDIFF(b.shipment_date, b.po_received_date) as lead_time_days";
	else  $date_dif="(b.shipment_date-b.po_received_date) as lead_time_days";
	$sql_query=("select $date_dif,a.job_no,a.job_no_prefix_num as prefix_job_no,b.shipment_date as orgi_ship_date,a.set_smv,a.season_buyer_wise  as season,a.product_code,a.bh_merchant,a.dealing_marchant,a.style_ref_no,b.id,b.is_confirmed,b.po_total_price,b.po_number,b.po_received_date,a.total_set_qnty as set_ratio,(b.po_quantity*a.total_set_qnty) as po_qty_pcs,a.avg_unit_price as unit_price,c.country_ship_date,c.order_quantity,c.order_total,d.count_id,d.copm_one_id,d.copm_two_id,d.percent_one,d.percent_two,e.construction,e.gsm_weight 
	from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fab_yarn_cost_dtls d,wo_pre_cost_fabric_cost_dtls e
	 
	 where a.job_no=b.job_no_mst and c.job_no_mst=a.job_no and b.id=c.po_break_down_id and d.job_no=a.job_no and 
	e.id=d.fabric_cost_dtls_id and  c.item_number_id=e.item_number_id and e.job_no=a.job_no  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1   and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $bh_mer_name_con $team_cond $yarnIds_cond order by d.copm_one_id,a.job_no "); 
	
					 
	$sql_data=sql_select($sql_query);
	$tot_rows=count($sql_data);
	$all_po_id="";
	$marchant_wise_arr=array();
	foreach( $sql_data as $row)
	{
		$comp_one_id=$row[csf('copm_one_id')].'**'.$row[csf('copm_two_id')];
		if($all_po_id=="") $all_po_id=$row[csf("id")]; else $all_po_id.=",".$row[csf("id")];
		if($row[csf('is_confirmed')]==1)
		{
			$order_val=$row[csf('order_total')]; //Place
		}
		else
		{
			$order_val=$row[csf('po_total_price')]; //Projection
		}
		
		
		if($row[csf('percent_two')]!=0) $row[csf('percent_two')]=$row[csf('percent_two')];else $row[csf('percent_two')]='';
		$percent_one=$row[csf('percent_one')].'**'.$row[csf('percent_two')];
		
		if($db_type==0) $c_date_con=date("d-m-y",strtotime($row[csf('country_ship_date')]));
		else $c_date_con=date("d-M-y",strtotime($row[csf('country_ship_date')]));
		$c_date_week=$no_of_week_for_header_calc[$c_date_con];
		//$c_date_week=$no_of_week_for_header[$row[csf('country_ship_date')]];
		
		$comp_wise_arr[$comp_one_id][$row[csf('is_confirmed')]]['po_qty']+=$row[csf('order_quantity')];
		$comp_wise_arr[$comp_one_id][$row[csf('is_confirmed')]]['set_smv']+=$row[csf('set_smv')];
		$comp_wise_arr[$comp_one_id][$row[csf('is_confirmed')]]['set_ratio']+=$row[csf('set_ratio')];
		$comp_wise_arr[$comp_one_id][$row[csf('is_confirmed')]]['po_val']+=$order_val;
		$comp_wise_arr[$comp_one_id][$row[csf('is_confirmed')]]['unit_price']+=$row[csf('unit_price')];
		$comp_wise_arr[$comp_one_id][$row[csf('is_confirmed')]]['lead_day']+=$row[csf('lead_time_days')];
		$comp_wise_arr[$comp_one_id][$row[csf('is_confirmed')]]['job_no'].=$row[csf('prefix_job_no')].',';
		$comp_wise_arr[$comp_one_id][$row[csf('is_confirmed')]]['style'].=$row[csf('style_ref_no')].',';
		$comp_wise_arr[$comp_one_id][$row[csf('is_confirmed')]]['season'].=$row[csf('season')].',';
		$comp_wise_arr[$comp_one_id][$row[csf('is_confirmed')]]['pcode'].=$row[csf('product_code')].',';
		$comp_wise_arr[$comp_one_id][$row[csf('is_confirmed')]]['dealing_marchant'].=$row[csf('dealing_marchant')].',';	
		$comp_wise_arr[$comp_one_id][$row[csf('is_confirmed')]]['bh_mer'].=$row[csf('bh_merchant')].',';
		$comp_wise_arr[$comp_one_id][$row[csf('is_confirmed')]]['po_no'].=$row[csf('po_number')].',';
		$comp_wise_arr[$comp_one_id][$row[csf('is_confirmed')]]['po_id'].=$row[csf('id')].',';
		$comp_wise_arr[$comp_one_id][$row[csf('is_confirmed')]]['po_received_date'].=$row[csf('po_received_date')].',';
		$comp_wise_arr[$comp_one_id][$row[csf('is_confirmed')]]['orgi_ship_date'].=$row[csf('orgi_ship_date')].',';
		$comp_wise_arr[$comp_one_id][$row[csf('is_confirmed')]]['c_ship_date'].=$row[csf('country_ship_date')].',';
		//d.percent_one,d.percent_two 
		$comp_wise_arr[$comp_one_id][$row[csf('is_confirmed')]]['percent'].=$percent_one.',';
		$comp_wise_arr[$comp_one_id][$row[csf('is_confirmed')]]['const'].=$row[csf('construction')].',';
		$comp_wise_arr[$comp_one_id][$row[csf('is_confirmed')]]['gsm'].=$row[csf('gsm_weight')].',';
		$comp_wise_arr[$comp_one_id][$row[csf('is_confirmed')]]['count_id'].=$row[csf('count_id')].',';
		if($row[csf('is_confirmed')]==1)
		{
			$week_wise_order_qty[$comp_one_id][$row[csf('is_confirmed')]][$c_date_week]['po_quantity']+=$row[csf("order_quantity")];
		}
		$tod_week=$no_of_week_for_header[$row[csf('orgi_ship_date')]];
		if($row[csf('is_confirmed')]==2)
			{
				//$tod_week=$no_of_week_for_header[$row[csf('orgi_ship_date')]];
				$comp_wise_arr2[$comp_one_id][$row[csf('is_confirmed')]][$tod_week]['proj_po_qty']+=$row[csf('po_qty_pcs')];
				
			}
		
		
	}
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
					$poIds_cond.=" b.id  in($ids) or ";
				}
				$poIds_cond=chop($poIds_cond,'or ');
				$poIds_cond.=")";
			}
			else
			{
				$poIds_cond=" and  b.id  in($all_po_id)";
			}
		}
		$exfactory_data_array=array();
		$exfactory_sql=("select b.shipment_date as orgi_ship_date,d.copm_one_id,d.copm_two_id,b.is_confirmed,
		sum(CASE WHEN f.entry_form!=85 THEN f.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		sum(CASE WHEN f.entry_form=85 THEN f.ex_factory_qnty ELSE 0 END) as return_qnty
		 from wo_po_details_master a, wo_po_break_down b,pro_ex_factory_mst f,wo_po_color_size_breakdown c,wo_pre_cost_fab_yarn_cost_dtls d,wo_pre_cost_fabric_cost_dtls e  where  a.job_no=b.job_no_mst and  f.po_break_down_id=b.id and c.job_no_mst=a.job_no and b.id=c.po_break_down_id and d.job_no=a.job_no and 
	e.id=d.fabric_cost_dtls_id and  c.item_number_id=e.item_number_id and e.job_no=a.job_no and c.po_break_down_id=f.po_break_down_id   and  a.company_name=$company_name  and f.status_active=1 and f.is_deleted=0 $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $team_cond group by  d.copm_one_id,d.copm_two_id, b.is_confirmed,b.shipment_date");
		$exfactory_data=sql_select($exfactory_sql);
		foreach($exfactory_data as $row)
		{
				$tod_week=$no_of_week_for_header[$row[csf('orgi_ship_date')]];
				$comp_one_id=$row[csf('copm_one_id')].'**'.$row[csf('copm_two_id')];
				$exfactory_data_array[$comp_one_id][$row[csf('is_confirmed')]][ex_qnty]+=$row[csf('ex_factory_qnty')]-$row[csf('return_qnty')];
		}
		unset($exfactory_data);
	
		ob_start();
		$rowcount=0;
		foreach($week_counter_header as $mon_key=>$week)
		{
			$rowcount+=count($week);
		}
		 $rowb=$rowcount*2;
		 $td_width=1780+($rowcount*45)+$rowb;
		 
		 $tot_month = datediff( 'm', $date_start,$date_end);
		for($i=0; $i<= $tot_month; $i++ )
		{
			$next_month=month_add($date_start,$i);
			$month_arr.=date("Y-M",strtotime($next_month)).',';
		}
	 	$month_arr_id=rtrim($month_arr,',');
	if($tot_rows>0)
	{
	?>
		 <script>
			var mon_week ='<? echo $month_arr_id; ?>';
			var mon_week=mon_week.split(",");
			
		for (var k=0; k<mon_week.length; k++)
		{
			var mon_data=document.getElementById('summary_footer_td_'+mon_week[k]).innerHTML;
			var mon_arr=mon_data.split("$");
			var mon_qnty=number_format(mon_arr[0],0);
			var mon_amt=number_format(mon_arr[1],0);
			var avg_order_smv=number_format(mon_arr[2],2);
			var avg_rate=(number_format(mon_arr[1],0,'.','')/number_format(mon_arr[0],0,'.',''));
		
			//alert(mon_arr[2]);
			document.getElementById('summary_header_td_qty_'+mon_week[k]).innerHTML=mon_qnty;
			document.getElementById('summary_header_td_val_'+mon_week[k]).innerHTML='$'+mon_amt;
			document.getElementById('summary_header_td_rate_'+mon_week[k]).innerHTML='FOB:'+number_format(avg_rate,2);
			document.getElementById('summary_header_td_smv_'+mon_week[k]).innerHTML='SMV:'+number_format(avg_order_smv,2);
			
		}
			var yarn_req_qty=document.getElementById('td_yarn_req_march').innerHTML;
			var yarnreq_qty=number_format(yarn_req_qty,0);
			document.getElementById('yarn_summary_tot').innerHTML=yarnreq_qty;
			</script>
            
         <?
	}
		?>
        <div style="width:<? echo $td_width+20;?>px">
		<fieldset style="width:100%;">	
      
        
			<table width="<? echo $td_width+20;?>px">
				<tr class="form_caption">
					<td align="left" style="margin-left:10px" colspan="<? echo $rowcount+26; ?>">Composition Wise</td>
				</tr>
				<tr class="form_caption" align="center" style="display:none">
					<td colspan="<? echo $rowcount+26; ?>" align="center"><? //echo $company_library[$company_name]; ?></td>
				</tr>
			</table>
			<table class="rpt_table" width="<? echo $td_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
                  <tr>
					
                    <th  width="" colspan="11" rowspan="2">&nbsp;</th>
                     <th  rowspan="2" id="yarn_summary_tot"></th>
                     <th  width="" colspan="14" rowspan="2">&nbsp;</th>
                     <?
					
					foreach($week_counter_header as $mon_key=>$week)
					{
					?>
					<th   align="center" colspan="<? echo count($week);?>">
					<? 
					echo $mon_key;
					?>
					</th>
					<?
					}
            ?>
                </tr>
                  <tr>
                     <?
					foreach($week_counter_header as $mon_key=>$week_val)
					{
					?>
					<th width="60" style="font-size:0.836em;"   align="center" colspan="<? echo count($week_val)?>">
                           
                              <b id="summary_header_td_qty_<? echo $mon_key ;?>"  style="text-align:left;font-size: 0.795em;"> </b> &nbsp; 
                              <b id="summary_header_td_val_<? echo $mon_key ;?>" style="text-align: right;font-size: 0.795em;">  </b> &nbsp;
                              <b id="summary_header_td_rate_<? echo $mon_key ;?>"  style="text-align: right;font-size: 0.795em;"></b> &nbsp;
                              <b title="Avg SMV" id="summary_header_td_smv_<? echo $mon_key ;?>"  style="text-align: right;font-size: 0.795em;"></b>
					</th>
					<?
					}
            ?>
                </tr>
                <tr style="font:'Arial Narrow';font-size:9px">
					<th width="30">SL</th>
                    <th width="55">Season</th>
                    <th width="80">BH Mer.</th>
					<th width="70">FFL Mer.</th>
                    <th width="55">Job No</th>
					<th width="120">Style Ref</th>
                    <th width="60">Comp%</th>
                    <th width="120">Comp Name</th>
                    <th width="120">Const.</th>
                    <th width="50">GSM</th>
					<th width="120">Y/C</th>
                    <th width="70">Yarn Qty</th>
					<th width="60">Status</th>
					<th width="45">OPD</th>
					<th width="50">OPD Date</th>
					<th width="45">TOD</th>
                    <th width="70">Ord Qty</th>
					<th width="100">Order No</th>
                    <th width="40">Dept </th>
					<th width="40">Lead Time</th>
					<th width="40">Unit Price</th>
					<th width="90">Value</th>
					<th width="40">SMV/ Pcs</th>
                    <th width="90">Ord.SMV</th>
                    <th width="60">Delv Qty</th>
                    <th width="60">Delv.Val</th>
                     <?
					foreach($week_counter_header as $mon_key=>$week)
					{
						foreach($week as $week_key)
						{
							$start_week_date=$week_start_day[$week_key][week_start_day];
							$end_week_date=$week_end_day[$week_key][week_end_day];
							if($db_type==2)
							{
								$fisrtday=date("D",strtotime(change_date_format($start_week_date,"dd-mm-yyyy","-")));
								$lastday=date("D",strtotime(change_date_format($end_week_date,"dd-mm-yyyy","-")));
							}
							else
							{
								$fisrtday=date("D",strtotime(change_date_format($start_week_date,"dd-mm-yyyy")));
								$lastday=date("D",strtotime(change_date_format($end_week_date,"dd-mm-yyyy")));
							}
							$weekstart_date=explode("-",date("d-M",strtotime($start_week_date)));
							//$weekstart_date=explode("-",change_date_format($start_week_date,"dd-mm-yyyy","-"));
							//$weekstart_date=explode("-",date("d-M",strtotime($weekstart_date)));
							$weekstart_date=$weekstart_date[0].'/'.$weekstart_date[1];
						?>
						<th title="<? echo change_date_format($start_week_date,"dd-mm-yyyy","-").'('.$fisrtday.')'." To ".change_date_format($end_week_date,"dd-mm-yyyy","-").'('.$lastday.')'; ?>" width="45"  align="center">
						
						<? 
							echo 'W'.$week_key.'<br/>'.$weekstart_date;
						?>
						</th>
						<?
						}
					}
            ?>
                   
                </tr>    
				</thead>
			</table>
			<div style="width:<? echo $td_width+20;?>px; max-height:245px; overflow-y:scroll;overflow-x:hidden;" id="scroll_body">
			<table class="rpt_table" width="<? echo $td_width;?>px" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body_comp">
	<?
	
               
			$condition= new condition();
			 $condition->company_name("=$cbo_company_name");
			 if(str_replace("'","",$cbo_buyer_name)>0)
			 {
				  $condition->buyer_name("=$cbo_buyer_name");
			 }
			 if(str_replace("'","",$txt_job_no) !='')
			 {
				  $condition->job_no_prefix_num("in($txt_job_no)");
			 }
			
			 if(str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
			 {
			 $condition->pub_shipment_date(" between '$start_date' and '$end_date'");
			 }
				
			 if(str_replace("'","",$txt_order_no)!='')
			 {
				$condition->po_number("=$txt_order_no"); 
			 }
			 if(str_replace("'","",$txt_season)!='')
			 {
				//$condition->season("=$txt_season"); 
			 }
			 $condition->init();
			 
			 	
        	 $yarn= new yarn($condition);
		  //echo $yarn->getQuery(); die;
			 $yarn_req_qty_arr=$yarn->getOrderWiseYarnQtyArray();
			//print_r($yarn_req_qty_arr);
				$i=1;$total_order_qty=0;$total_order_value=0;$total_yarn_req_qty=0;$total_order_smv=0;$total_ex_fact_val=0;$total_ex_fact_qty=0;
				foreach($comp_wise_arr as $comp_key=>$comp_data)
				{  
				   
				   	foreach($comp_data as $status_key=>$status_val)
					{
						
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";   
				 $po_received_date=$status_val['po_received_date'];
				$countryship_date=$wo_color_data_arr[$comp_key][$status_key]['c_shipdate'];
				$comp_data=explode("**",$comp_key);
				$copm_one_id=$comp_data[0];
				$copm_two_id=$comp_data[1];
				
				$conf_country_ship_date=rtrim($wo_color_data_arr2[$comp_key][$status_key]['shipdate'],',');
				$conf_country_ship_date=explode(",",$conf_country_ship_date);
			
				
				$percent_one=rtrim($status_val['percent'],',');
				$construction=rtrim($status_val['const'],',');
				$gsm_weight=rtrim($status_val['gsm'],',');
				$gsm_weight=implode(",",array_unique(explode(",",$gsm_weight)));
				$construction=implode(",",array_unique(explode(",",$construction)));
				
				$percent_data=array_unique(explode(",",$percent_one));
				
				$percent_one_cond="";$percent_two_cond="";
				foreach($percent_data as $perid)
				{
						$percent_data=array_unique(explode("**",$perid));
						$percent_one=$percent_data[0];
						$percent_two=$percent_data[1];
					if($percent_one_cond=="") $percent_one_cond=$percent_one;else $percent_one_cond.=",".$percent_one;
					if($percent_two_cond=="") $percent_two_cond=$percent_two;else $percent_two_cond.=",".$percent_two;
				}
				if($percent_one_cond!='' && $percent_two_cond!='')
						{
							//if($percent_two!=0)
							$percent_all=$percent_one_cond.'/'.$percent_two_cond;
						}
						else if($percent_one_cond!='' && $percent_two_cond=='')
						{
							//if($percent_two!=0)
							$percent_all=$percent_one_cond;
						}
						else if($percent_one_cond=='' && $percent_two_cond!='')
						{
							//if($percent_two!=0)
							$percent_all=$percent_two_cond;
						}
						else
						{
							$percent_all='';	
						}
				
				if($copm_one_id!=0 && $copm_two_id!=0)
				{ 
					$copm_one_name=$composition[$copm_one_id].'/'.$composition[$copm_two_id];
				}
				else if($copm_one_id!=0 && $copm_two_id==0)
				{ 
					$copm_one_name=$composition[$copm_one_id];
				
				}
				else if($copm_one_id==0 && $copm_two_id!=0)
				{ 
					$copm_one_name=$composition[$copm_two_id];
				
				}
				else 
				{
					$copm_one_name='';
				}
				
				$count_id=rtrim($status_val['count_id'],',');
				 $count_ids=array_unique(explode(",", $count_id));
				 $yarn_count_value="";
				foreach($count_ids as $val)
				{
					//if($val>0)
					//{
						if($yarn_count_value=="") $yarn_count_value=$lib_yarn_count_arr[$val]; else $yarn_count_value.=", ".$lib_yarn_count_arr[$val];
					//}
				}
				$seasion_ids=rtrim($status_val['season'],',');
				$seasion_ids=array_unique(explode(",",$seasion_ids));
				$seasion_id=$seasion_ids[0];
				$job_no=rtrim($status_val['job_no'],',');
				$job_no=implode(",",array_unique(explode(",",$job_no)));
				$style=rtrim($status_val['style'],',');
				$po_no=rtrim($status_val['po_no'],',');
				$po_id=rtrim($status_val['po_id'],',');
				$po_ids=implode(",",array_unique(explode(",",$po_id)));
			
				$dealing_marchant=rtrim($status_val['dealing_marchant'],',');
				$bh_mers=rtrim($status_val['bh_mer'],',');
				$bh_mers=array_unique(explode(",",$bh_mers));
				$bh_mer=$bh_mers[0];
				//$bh_mer=implode(",",array_unique(explode(",",$bh_mer)));
				$po_received_date=rtrim($status_val['po_received_date'],',');
				$c_ship_date=rtrim($status_val['c_ship_date'],',');
				$job_no=implode(",",array_unique(explode(",",$job_no)));
				$style=implode(",",array_unique(explode(",",$style)));
				$po_no=implode(",",array_unique(explode(",",$po_no)));
				$po_id=array_unique(explode(",",$po_id));
			
				$yarn_req=0;
				foreach($po_id as $pid)
				{
				$yarn_req+=$yarn_req_qty_arr[$pid];
				}
				//echo $yarn_req.'<br>';
				
				$seasion_cond=implode(",",array_unique(explode(",",$seasion_id)));
				/*$seasion_cond="";
				foreach($seasion_id as $sid)
				{
					if($seasion_cond=="") $seasion_cond=$lib_season_name_arr[$sid];else $seasion_cond.=",".$lib_season_name_arr[$sid];
				}*/
				$dealing_marchant=array_unique(explode(",",$dealing_marchant));
				$dealing_marchant_con="";
				foreach($dealing_marchant as $mid)
				{
					if($dealing_marchant_con=="") $dealing_marchant_con=$team_member_arr[$mid];else $dealing_marchant_con.=",".$team_member_arr[$mid];
				}
				$po_received_date=array_unique(explode(",",$po_received_date));
				$opd_week="";$opd_recv_date="";$opd_recv_date_full="";
				//$lead_time=0;
				foreach($po_received_date as $op_id)
				{
					if($opd_week=="") $opd_week=$no_of_week_for_header[$op_id];else $opd_week.=",".$no_of_week_for_header[$op_id];
					if($opd_recv_date=="") $opd_recv_date=date("d-M",strtotime($op_id));else $opd_recv_date.=",".date("d-M",strtotime($op_id));
					if($opd_recv_date_full=="") $opd_recv_date_full=date("d-M-y",strtotime($op_id));else $opd_recv_date_full.=",".date("d-M-y",strtotime($op_id));
				//$lead_time = datediff( 'd', $op_id,$countryship_date);
				}
				$po_orgi_shipdate=rtrim($status_val['orgi_ship_date'],',');	
				$po_orgi_shipdate=array_unique(explode(",",$po_orgi_shipdate));
				$tod_weeks="";$tod_recv_date="";
				foreach($po_orgi_shipdate as $orgi_id)
				{
					//echo $orgi_id;
					if($tod_weeks=="") $tod_weeks=$no_of_week_for_header[$orgi_id];else $tod_weeks.=",".$no_of_week_for_header[$orgi_id];
				}
				//echo $tod_weeks;
				$tod_weeks=implode(",",array_unique(explode(",",$tod_weeks)));
				$lead_time =$status_val['lead_day'];
				 $opd_week=implode(",",array_unique(explode(",",$opd_week)));
				$opd_recv_date=implode(",",array_unique(explode(", ",$opd_recv_date)));
				$prod_cond=rtrim($status_val['pcode'],',');
				$prod_cond=implode(",",array_unique(explode(",",$prod_cond)));
				
				if($status_key==1) //Confirm $conf_country_ship_date
				{
					$c_ship_date_con=array_unique(explode(",",$c_ship_date));$qnty_array_dd=array();
					$c_week_qty=0;
					foreach($c_ship_date_con as $c_date)
					{
						if($db_type==0) $c_date_con=date("d-m-y",strtotime($c_date));
						else $c_date_con=date("d-M-y",strtotime($c_date));
						$c_week=$no_of_week_for_header_calc[$c_date_con];
						$c_week_qty=$week_wise_order_qty[$comp_key][$status_key][$c_week]['po_quantity'];
								//echo $c_week.'=='.$c_week_qty.'hj';
						$qnty_array_dd[$c_week]=$c_week_qty;
					}
					//print_r($qnty_array_dd);
						$tot_order_qty=0;$tot_order_qty_arr=array();
						foreach($week_counter_header as $mon_key=>$week)
						{
							//$order_qty_arr=array();
							foreach($week as $week_key)
							{
								 $tot_order_qty=$qnty_array_dd[$week_key];
								  $order_qty_arr[$week_key]=$tot_order_qty;
								  $tot_order_qty_arr[$comp_key]+=$tot_order_qty;
							}
						}
				}
				else 
				{
						$po_orgi_shipdate=rtrim($status_val['orgi_ship_date'],',');
						$proj_orgi_shipdate=array_unique(explode(",",$po_orgi_shipdate));
						$qnty_array_mm=array();$proj_po_qty=0;
						//$qnty_arr=distribute_projection($tod_week, $tot_proj_po_qty);
						$week_check=array();
						foreach($proj_orgi_shipdate as $tod_key)
						{
							$tod_week=$no_of_week_for_header[$tod_key];
							if($week_check[$tod_week]=='')
							{ //
								$proj_po_qty=$comp_wise_arr2[$comp_key][$status_key][$tod_week]['proj_po_qty'];
							
								$qnty_arr=distribute_projection($tod_week, $proj_po_qty);
								foreach($qnty_arr as $key=>$val)
								{
									$qnty_array_mm[$key]+=$val;
								}
								
								$week_check[$tod_week]=$proj_po_qty;
							}
						}
					
							$order_qty=0;$tot_order_qty_arr=array();
							foreach($week_counter_header as $mon_key=>$week)
							{
								foreach($week as $week_key)
								{
									$order_qty=$qnty_array_mm[$week_key];
									$order_qty_arr[$week_key]=$order_qty;
									$tot_order_qty_arr[$comp_key]+=$order_qty;
								}
							}
				}
				$tot_po_qty=$tot_order_qty_arr[$comp_key];
				$po_value=$status_val['po_val'];
				$unit_price=$po_value/$tot_po_qty;
				
				$job_no_row=count(array_unique(explode(",",$job_no)));
				if($job_no_row>1)
				{
					 $view_button="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".$po_ids."','".$comp_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button=$job_no;	
				}
				$style_row=count(array_unique(explode(",",$style)));
				if($style_row>1)
				{
					 $view_button_style="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".$po_ids."','".$comp_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_style=$style;	
				}
				
				$tod_weeks_row=count(array_unique(explode(",",$tod_weeks)));
				if($tod_weeks_row>1)
				{
					 $view_button_tod="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".$po_ids."','".$comp_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_tod=$tod_weeks;	
				}
				
				$opd_week_row=count(array_unique(explode(",",$opd_week)));
				if($opd_week_row>1)
				{
					 $view_button_opd="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".$po_ids."','".$comp_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_opd=$opd_week;	
				}
				$opd_recv_date_row=count(array_unique(explode(",",$opd_recv_date)));
				if($opd_recv_date_row>1)
				{
					 $view_button_opd_date="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".$po_ids."','".$comp_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_opd_date=$opd_recv_date;	
				}
				$po_no_row=count(array_unique(explode(",",$po_no)));
				if($po_no_row>1)
				{
					 $view_button_po="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".$po_ids."','".$comp_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_po=$po_no;	
				}
				$dealing_marchant_con_row=count(array_unique(explode(",",$dealing_marchant_con)));
				$bh_mer_con_row=count(array_unique(explode(",",$bh_mer)));
				$seasion_cond_row=count(array_unique(explode(",",$seasion_cond)));
				$construction_cond_row=count(array_unique(explode(",",$construction)));
				$gsm_weight_cond_row=count(array_unique(explode(",",$gsm_weight)));
				$yarn_count_value_cond_row=count(array_unique(explode(",",$yarn_count_value)));
				$prod_cond_cond_row=count(array_unique(explode(",",$prod_cond)));
				if($dealing_marchant_con_row>1)
				{
					 $view_button_deal="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".$po_ids."','".$comp_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_deal=$dealing_marchant_con;
					//$view_button_bh=$bh_mer;
					//$view_button_season=$seasion_cond;	
				}
				if($bh_mer_con_row>1)
				{
					 $view_button_bh="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".$po_ids."','".$comp_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					
					$view_button_bh=$bh_mer;
					//$view_button_season=$seasion_cond;	
				}
				if($prod_cond_cond_row>1)
				{
					 $view_button_dept="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".$po_ids."','".$comp_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_dept=$prod_cond;
				}
				if($seasion_cond_row>1)
				{
					 $view_button_season="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".$po_ids."','".$comp_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_season=$seasion_cond;	
				}
				if($construction_cond_row>1)
				{
					 $view_button_const="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".$po_ids."','".$comp_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_const=$construction;	
				}
				if($gsm_weight_cond_row>1)
				{
					 $view_button_gsm="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".$po_ids."','".$comp_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_gsm=$$gsm_weight;	
				}
				if($yarn_count_value_cond_row>1)
				{
					 $view_button_count="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".$po_ids."','".$comp_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_count=$yarn_count_value;	
				}
				//yarn_count_value_cond_row
				?>
                <tr style="font:'Arial Narrow';font-size:9px" align="center" bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
					<td width="30"><? echo $i; ?></td>
                    <td width="55" style="word-wrap:break-word; word-break: break-all;"><? echo $seasion_id;?></td>
                    <td width="80" style="word-wrap:break-word; word-break: break-all;" ><? echo $bh_mer; ?></td> 
					<td width="70" style="word-wrap:break-word; word-break: break-all;" ><? echo $view_button_deal; ?></td> 
                    <td width="55" style="word-wrap:break-word; word-break: break-all;">
                   
					<? echo $view_button; ?></td>
					<td  width="120" style="word-wrap:break-word; word-break: break-all;"> <? echo $view_button_style; ?></td>
                    <td width="60" style="word-wrap:break-word; word-break: break-all;"><? echo $percent_all; ?></td>
                    <td  width="120"><div style="word-wrap:break-word:120px;"><? echo $copm_one_name; ?></div></td>
                    <td  width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $view_button_const;?></td>
                    <td width="50"  style="word-wrap:break-word; word-break: break-all;"><? echo $view_button_gsm; ?></td>
					<td  width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $view_button_count; ?></td>
                    <td  width="70" title="<? echo $yarn_req;?>" style="word-wrap:break-word; word-break: break-all; text-align:right">
					<?
						echo number_format($yarn_req);
				
					?>
                    </td>
					<td  width="60" style="word-wrap:break-word; word-break: break-all;">
					<?
					
					if($status_key==2)
					{
						echo "Booked";	
					}
					else
					{
						echo "Placed";
					}
					//echo $order_status[$row_data[csf('is_confirmed')]];
					?> 
                    </td>
					<td  width="45" style="word-wrap:break-word; word-break: break-all;">
					<?
					//if($opd_week!='') echo "W:".$opd_week; else echo '';
					echo $view_button_opd;
					?>
                    </td>
					<td  width="50" title="<? echo $opd_recv_date_full; ?>" style="word-wrap:break-word; word-break: break-all;">
                    
					<?
					if($opd_recv_date!="" && $opd_recv_date !="0000-00-00" && $opd_recv_date!="0")
					{
						$opd_recv_date=$opd_recv_date;
					}
					echo $view_button_opd_date;
					?>
                      
                    </td>
					<td width="45" title="Orgi. Ship Date" style="word-wrap:break-word; word-break: break-all; text-align: center">
					<? 
					// echo 'W'.$tod_weeks;
					 echo $view_button_tod;
					?>
                   
                    </td>
                    <td  width="70" style="word-wrap:break-word; word-break: break-all; text-align:right">
					<? echo number_format($tot_po_qty);?>
                    </td>
					<td  width="100" style="word-wrap:break-word; word-break: break-all; text-align: center">
                   
					<? echo $view_button_po; ?>
                    </td>
                    <td  width="40" style="word-wrap:break-word; word-break: break-all; text-align:left" >
					<? echo $view_button_dept;?>
                    </td>
					<td  width="40" title="TOD-OPD" style="word-wrap:break-word; word-break: break-all; text-align: center">
					<? //echo $lead_time; ?>
                    </td>
					<td  width="40" title="PO Value/Po Qty" style="word-wrap:break-word; word-break: break-all; text-align:right">
					<? echo number_format($unit_price,2); ?>
                    </td>
                   
					<td width="90" title="Po Qty*Unit Price" style="word-wrap:break-word; word-break: break-all;text-align:right">
                   <? echo number_format($tot_po_qty*$unit_price,0);
				  	 $set_smv=number_format($status_val['set_smv'],2);
				    ?>
                    </td>
					<td width="40" title="Set SMV/Set Ratio" style="word-wrap:break-word; word-break: break-all;">
					<? 
					$smv_rate=0;
					$smv_rate=$set_smv/$status_val['set_ratio'];
					echo number_format($smv_rate,2); ?>
                    </td>
					
                    <td  width="90" title="Set SMV Per Pcs*Order Qty" style="word-wrap:break-word; word-break: break-all;text-align:right">
					<? echo number_format(($set_smv/$status_val['set_ratio'])*$tot_po_qty); ?>
                    </td>
                     <td  width="60" style="word-wrap:break-word; word-break: break-all;text-align:right">
					<? echo number_format($exfactory_data_array[$comp_key][$status_key][ex_qnty]); ?>
                    </td>
                    <td width="60" title="Ex-Fact Qty*Unit Price" style="word-wrap:break-word; word-break: break-all;text-align:right">
                    <? echo  number_format($exfactory_data_array[$comp_key][$status_key][ex_qnty]*$unit_price,0);
					//echo $exfactory_data_array[$marchant_key][$status_key][ex_qnty].'='.$status_val['unit_price'].'<br/>';
					 ?>
                    </td>
                    <?
					
					$week_qty=0;
					$week_check=array();
                   	foreach($week_counter_header as $mon_key=>$week)
					{
						foreach($week as $week_key)
						{
						?>
						<td   style="word-wrap:break-word; word-break: break-all;"  width="45" align="right">
						<? 
								$week_qty=$order_qty_arr[$week_key];
								if($week_qty>0) echo number_format($week_qty,0); else echo ' ';
								$tot_week_qty[$mon_key][$week_key]+=$week_qty;
								$tot_week_amount[$mon_key][$week_key]+=$week_qty*$unit_price;
								$smv_avg=0;
								$smv_avg=$smv_rate*$week_qty;
								$month_smv_arr[$mon_key]+=$smv_avg;
						?>
						</td>
						<?
						}
					}
					?>
                    </tr>
                    <?
					$total_order_qty+=$tot_po_qty;
					$total_yarn_req_qty+=$yarn_req;	
					$total_ex_fact_qty+=$exfactory_data_array[$comp_key][$status_key][ex_qnty];
					$total_ex_fact_val+=$exfactory_data_array[$comp_key][$status_key][ex_qnty]*$unit_price;
					$total_order_value+=$tot_po_qty*$unit_price;
					$total_order_smv+=($set_smv/$status_val['set_ratio'])*$tot_po_qty;
					//$total_week_qty+=$week_qty;
					//=====================================================================================================================
				$i++;
						}
				}
				?>
                 
				</table>
				</div>
                <table style="font:'Arial Narrow';font-size:9px" class="tbl_bottom"  width="<? echo $td_width;?>px" cellpadding="0" cellspacing="0" id="report_table_footer" border="1" rules="all" >
					<tr>
					<td width="30">&nbsp;</td>
                    <td width="55">&nbsp;</td>
                  	<td width="80">&nbsp;</td>
					<td width="70">&nbsp;</td>
                    <td width="55">&nbsp;</td>
					<td width="120">&nbsp;</td>
                    <td width="60">&nbsp;</td>
                    <td width="120">&nbsp;</td>
                    <td width="120">&nbsp;</td>
                    <td width="50">&nbsp;</td>
					<td width="120">&nbsp;</td>
                    <td width="70" style="word-wrap:break-word; word-break: break-all; font-size:smaller" id="td_yarn_req_march"><? echo $total_yarn_req_qty;?></td>
					<td width="60">&nbsp;</td>
					<td width="45">&nbsp;</td>
					<td width="50">&nbsp;</td>
					<td width="45">&nbsp;</td>
                    <td width="70" style="word-wrap:break-word; word-break: break-all;font-size:smaller" id="td_po_qty_march"><? echo $total_order_qty;?></td>
					<td width="100">&nbsp;</td>
                    <td width="40">&nbsp; </td>
					<td width="40">&nbsp;</td>
					<td width="40"><? echo number_format($total_order_value/$total_order_qty,2);?></td>
					<td width="90" id="td_po_val_march" style="word-wrap:break-word; word-break: break-all;font-size:smaller"><? echo number_format($total_order_value);?></td>
					<td width="40"><? echo number_format($total_order_smv/$total_order_qty,2);?></td>
                    <td width="90" style="word-wrap:break-word; word-break: break-all;font-size:smaller" id="total_order_smv"><? echo number_format($total_order_smv,0);?></td>
                    <td width="60" style="word-wrap:break-word; word-break: break-all;font-size:smaller" id="total_order_exfc_qty"><? echo number_format($total_ex_fact_qty,0);?></td>
                    <td width="60" style="word-wrap:break-word; word-break: break-all;font-size:smaller" id="total_order_exfc_val"><? echo number_format($total_ex_fact_val,0);?></td>
                     <?
					$total_week_qty=0; $total_week_amt=0; 
					foreach($week_counter_header as $mon_key=>$week)
					{
						foreach($week as $week_key)
						{
						?>
						<td width="45" style="word-wrap:break-word; word-break: break-all;font-size:smaller"  align="center">
						
						<? 
						$total_week_qty=$tot_week_qty[$mon_key][$week_key];
						$total_week_amt=$tot_week_amount[$mon_key][$week_key];
						echo number_format($total_week_qty,0);
						$tot_mon_qty[$mon_key]+=$total_week_qty;
						$tot_mon_amount[$mon_key]+=$total_week_amt;
						?>
						</td>
						<?
						
						}
					}
            ?>
					</tr>
                    <tr style="display:none">
					<td colspan="26">&nbsp;</td>
                     <?
					$total_week_qt=0; 
					foreach($week_counter_header as $mon_key=>$week)
					{
						?>
						<td width="" id="summary_footer_td_<? echo $mon_key?>" colspan="<? echo count($week);?>" title="<? echo $tot_week_amount[$mon_key][$week_key];?>" style="word-wrap:break-word; word-break: break-all;"   align="center">
						
						<? 
						$tot_smv=0;
						$tot_smv=$month_smv_arr[$mon_key]/$tot_mon_qty[$mon_key];
						
						$head_td_data=$tot_mon_qty[$mon_key].'$'.$tot_mon_amount[$mon_key].'$'.$tot_smv;
						echo $head_td_data;
						?>
						</td>
						<?
					}
            ?>
                    </tr>
				</table>
			</fieldset>
		</div>
         <style>
			@font-face {
   				 font-family:"Arial Narrow"; font-size:11px;
			}
			TD{font-family:"Arial Narrow";font-size:10px;}
			TH{font-family:"Arial Narrow";font-size:11px;}
			.rpt_table TD{font-family: "Arial Narrow";font-size:11px;}
			.rpt_table TH{font-family:"Arial Narrow";font-size:11px;}
		</style>
	<?
	}
//===========================================================================================================================================================
	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data****$filename****$tot_rows****$cbo_search_type";
	exit();	
	
}
else if($action=="report_generate_const") //Construction Wise
{
 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company_name=str_replace("'","",$cbo_company_name);
	$cbo_search_type=str_replace("'","",$cbo_search_type);
	$cbo_bh_mer_name=str_replace("'","",$cbo_bh_mer_name);
	$txt_yarn_count=str_replace("'","",$txt_yarn_count);
	if($txt_yarn_count!='') $yarn_count_con="and yarn_count Like '%$txt_yarn_count%' ";else $yarn_count_con='';
	$buyer_id_cond="";
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{
				$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else
			{
				$buyer_id_cond="";
			}
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}
	
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_job_no=trim($txt_job_no);
	if($txt_job_no !="" || $txt_job_no !=0)
	{
		$jobcond="and a.job_no_prefix_num in(".$txt_job_no.")";
	}
	else
	{
		$jobcond="";	
	}
	
		$date_cond='';
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			$start_date=(str_replace("'","",$txt_date_from));
			$end_date=(str_replace("'","",$txt_date_to));
			
			$date_cond="and b.pub_shipment_date between '$start_date' and '$end_date'";
		}
	
	
	if(str_replace("'","",$txt_style_ref)!="") $style_ref_cond=" and a.style_ref_no like '%".str_replace("'","",$txt_style_ref)."%'"; else $style_ref_cond="";
	if(str_replace("'","",$txt_order_no)!="")
	{
		$ordercond=" and b.po_number like '%".str_replace("'","",$txt_order_no)."%'"; 
	}
	else
	{
		$ordercond="";
	}
	
	$team_cond="";
	if(str_replace("'","",$cbo_team_name)!=0) $team_cond=" and a.team_leader=".str_replace("'","",$cbo_team_name)."  ";
	if(str_replace("'","",$cbo_team_member)!=0) $team_cond.=" and a.dealing_marchant=".str_replace("'","",$cbo_team_member)."  ";
	$cbo_bh_mer_name=str_replace("0","",$cbo_bh_mer_name);
	if($cbo_bh_mer_name!=0 || $cbo_bh_mer_name!='') $bh_mer_name_con="and a.bh_merchant='$cbo_bh_mer_name' ";else $bh_mer_name_con='';
	 $cbo_year_selection=str_replace("'","",$cbo_year_selection);
	
$start_ddd= strtotime(change_date_format($start_date,"dd-mm-yyyy","-"));
	$start_ddd=date("Y-m-d",$start_ddd);
	$start_end_date= strtotime(change_date_format($end_date,"dd-mm-yyyy","-"));
	$start_end_date=date("Y-m-d",$start_end_date);
	 //$dd= date('d-m-Y',strtotime($start_date));
	//$start_date_week= date($dd, time() - 1296000);//$start_date;
	//echo $last_day_this_month  = date($start_date_week, time() -1814400); //date('t-m-Y');
	
	$dateee = strtotime($start_ddd);
	$start_end_date = strtotime($start_end_date);
	
	$date_start = date("Y-m-d",strtotime("-28 day", $dateee));
	$date_end = date("Y-m-d",strtotime("+28 day", $start_end_date));
	//echo $date_end;
	//$end_date_week=  addday("$end_date", time() -1814400);//$start_date;//$start_date;
	if($db_type==0)
	{
		$date_start= change_date_format($date_start,"yyyy-mm-dd");
		$date_end= change_date_format($date_end,"yyyy-mm-dd");
	}
	else
	{
		$date_start= change_date_format($date_start,"dd-mm-yyyy","-",1);
		$date_end= change_date_format($date_end,"dd-mm-yyyy","-",1);
	}
	
	
	//echo "select week_date,week from week_of_year where week_date between '$date_start' and  '$date_end' order by week_date asc";die;
	// date("d-m-Y", time() - 1296000);
	$week_for_arr=array();$no_of_week_for_header=array();
	$sql_week_header=sql_select("select week_date,week from week_of_year where week_date between '$date_start' and  '$date_end' order by week_date asc");
	$week_check_head=array();
	foreach ($sql_week_header as $row_week_header)
	{
		if($week_check_head[$row_week_header[csf("week")]]=='')
		{
			$week_counter_header[date("Y-M",strtotime($row_week_header[csf("week_date")]))][$row_week_header[csf("week")]]=$row_week_header[csf("week")];
			$week_check_head[$row_week_header[csf("week")]]=$week_counter_header[date("Y-M",strtotime($row_week_header[csf("week_date")]))][$row_week_header[csf("week")]];
		}
		$tmp=add_date($row_week_header[csf("week_date")],-1);
		if($db_type==0) $tmp_cond=date("d-m-y",strtotime($tmp));
		else $tmp_cond=date("d-M-y",strtotime($tmp));
		$no_of_week_for_header_calc[$tmp_cond]=$row_week_header[csf("week")];	
			
		$no_of_week_for_header[$row_week_header[csf("week_date")]]=$row_week_header[csf("week")];
		$test[$row_week_header[csf("week")]]=$row_week_header[csf("week")];
	}
	unset($sql_week_header);
	$week_start_day=array();
	$week_end_day=array();$week_day_arr=array();
	//week_day
	$sql_week_start_end_date=sql_select("select week,min(week_date) as week_start_day, Max(week_date) as week_end_day from week_of_year where week_date between '$date_start' and  '$date_end' group by week");
	foreach ($sql_week_start_end_date as $row_week)
	{
		$week_start_day[$row_week[csf("week")]][week_start_day]=$row_week[csf("week_start_day")];
		$week_end_day[$row_week[csf("week")]][week_end_day]=$row_week[csf("week_end_day")];
		//$week_day_arr[$row_week[csf("week")]][$row_week[csf("week_start_day")]][week_first_day]=$row_week[csf("min_week_day")];
		$week_day_arr[$row_week[csf("week_end_day")]]['week_last_day']=$row_week[csf("last_week_day")];
	}
	unset($sql_week_start_end_date);
	//print_r($week_day_arr);
	function distribute_projection( $base_week, $qnty)
	{
		//5. Distribution ratios are: Base week 52%, Immediate before 24%, Week before immediate week 20%, Immediate week after base week 2%, next 2 weeks as 1%
		global $test;
		 
		$tmpbase_week=$test[$base_week-3];
		$k=0;
		for($i=0; $i<6; $i++)
		{
			$tmpbase_week++;
			if( $test[$tmpbase_week]=='') { $tmpbase_week=1; $k=0; }
			
			
			if($i==0) $distr_arr[$tmpbase_week]=($qnty*20)/100;
			if($i==1) $distr_arr[$tmpbase_week]=($qnty*24)/100;
			if($i==2) $distr_arr[$tmpbase_week]=($qnty*52)/100;
			if($i==3) $distr_arr[$tmpbase_week]=($qnty*2)/100;
			if($i==4) $distr_arr[$tmpbase_week]=($qnty*1)/100;
			if($i==5) $distr_arr[$tmpbase_week]=($qnty*1)/100;
			$k++;
			
		}
		 
		return $distr_arr;
		/*5000
		b=52
		b-1=21
		b-2=15
		b+1=1
		b+2=1*/
	}
	//echo "<per>";
	 //print_r(distribute_projection("2016-Dec",51, 5000)); die;


	
	if($template==1)
	{
		$po_wise_buyer=array();
		$buyer_wise_data=array();
		$po_id_arr=array();
		$week_wise_order_qty_arr=array();	
	
		 $count_data=sql_select("select yarn_count,id from  lib_yarn_count  where status_active=1 and is_deleted=0 and yarn_count is not null $yarn_count_con");
		$yarn_id='';
		foreach($count_data as $row)
		{
				if($yarn_id=='') $yarn_id=$row[csf('id')];else $yarn_id.=",".$row[csf('id')];
				
		}
		$yarn_ids=count(array_unique(explode(",",$yarn_id)));
		$yarn_numIds=chop($yarn_id,','); $yarnIds_cond="";
		if($yarn_id!='' || $yarn_id!=0)
		{
			if($db_type==2 && $yarn_ids>1000)
			{
				$yarnIds_cond=" and (";
				$yIdsArr=array_chunk(explode(",",$yarn_numIds),990);
				foreach($yIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$yarnIds_cond.=" d.count_id  in($ids) or ";
				}
				$yarnIds_cond=chop($yarnIds_cond,'or ');
				$yarnIds_cond.=")";
			}
			else
			{
				$yarnIds_cond=" and  d.count_id  in($yarn_id)";
			}
		}
	 
	 
	 $sql_data_c=("select e.construction,b.is_confirmed,c.country_ship_date as  conf_ship_date,b.shipment_date as orgi_ship_date,sum(c.order_quantity) as order_quantity from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,wo_pre_cost_fab_yarn_cost_dtls d,wo_pre_cost_fabric_cost_dtls e
				where a.job_no=b.job_no_mst and  c.po_break_down_id=b.id and  c.job_no_mst=b.job_no_mst  and d.job_no=a.job_no and 
	e.id=d.fabric_cost_dtls_id and  c.item_number_id= e.item_number_id and e.job_no=a.job_no  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $team_cond group by  e.construction,b.is_confirmed,c.country_ship_date,b.shipment_date"); 
	$sql_result_c=sql_select($sql_data_c);
		foreach($sql_result_c as $row)
		{
			$const_data=$row[csf('construction')];
		//	$wo_color_data_arr[$const_data][$row[csf('is_confirmed')]]['c_shipdate']=$row[csf('country_ship_date')];
			$conf_country_ship_date=$row[csf('conf_ship_date')];
			if($row[csf('is_confirmed')]==1) //Confirm
			{
				if($db_type==0) $date_week_cond=date("d-m-y",strtotime($row[csf('conf_ship_date')]));
				else $date_week_cond=date("d-M-y",strtotime($row[csf('conf_ship_date')]));
				
			$wo_color_data_arr2[$const_data][$row[csf('is_confirmed')]]['shipdate'].=$row[csf('conf_ship_date')].',';
			$week_wise_order_qty_arr[$const_data][$row[csf('is_confirmed')]][$no_of_week_for_header_calc[$date_week_cond]]['po_quantity']+=$row[csf("order_quantity")];
			}
				
		}
		unset($sql_result_c);
	
	if($db_type==0) $date_dif="DATEDIFF(b.shipment_date, b.po_received_date) as lead_time_days";
	else  $date_dif="(b.shipment_date-b.po_received_date) as lead_time_days";
	  $sql_query=("select $date_dif,a.job_no,a.job_no_prefix_num as prefix_job_no,a.set_smv,a.season_buyer_wise  as season,a.product_code,a.dealing_marchant,a.bh_merchant,a.style_ref_no,b.id,b.is_confirmed,b.po_number,b.po_received_date,b.shipment_date as orgi_ship_date,a.total_set_qnty as set_ratio,(b.po_quantity*a.total_set_qnty) as po_qty_pcs,b.po_total_price,a.avg_unit_price as unit_price,c.country_ship_date,c.order_quantity,c.order_total,d.count_id,d.copm_one_id,d.copm_two_id,d.percent_one,d.percent_two,e.construction,e.gsm_weight from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fab_yarn_cost_dtls d,wo_pre_cost_fabric_cost_dtls e
	 where a.job_no=b.job_no_mst and c.job_no_mst=a.job_no and b.id=c.po_break_down_id and d.job_no=a.job_no and 
	e.id=d.fabric_cost_dtls_id and  c.item_number_id= e.item_number_id and e.job_no=a.job_no  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1  and e.construction is not null and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $bh_mer_name_con $ordercond $team_cond  $yarnIds_cond order by e.construction,a.job_no "); 
	
					 
	$sql_data=sql_select($sql_query);
	$tot_rows=count($sql_data);
	$all_po_id="";
	$marchant_wise_arr=array();
	foreach( $sql_data as $row)
	{
		//$week_wise_qty_arr[date("Y-m",strtotime($row_data[csf("pub_shipment_date")]))]=$row_data[csf('pub_shipment_date')];
		$const_data=$row[csf('construction')];
		$country_ship_date=$wo_color_data_arr[$const_data][$row[csf('is_confirmed')]]['c_shipdate'];
		if($all_po_id=="") $all_po_id=$row[csf("id")]; else $all_po_id.=",".$row[csf("id")];
		
		
		if($db_type==0) $date_week_cond=date("d-m-y",strtotime($row[csf('country_ship_date')]));
		else $date_week_cond=date("d-M-y",strtotime($row[csf('country_ship_date')]));
		$c_date_week=$no_of_week_for_header_calc[$date_week_cond];
				
		if($row[csf('percent_two')]!=0) $row[csf('percent_two')]=$row[csf('percent_two')];else $row[csf('percent_two')]='';
		$percent_one=$row[csf('percent_one')].'**'.$row[csf('percent_two')];
		
		if($row[csf('copm_two_id')]!=0) $row[csf('copm_two_id')]=$row[csf('copm_two_id')];else $row[csf('copm_two_id')]='';
		$copm_one_id=$row[csf('copm_one_id')].'**'.$row[csf('copm_two_id')];
		if($row[csf('is_confirmed')]==1)
		{
			$order_val=$row[csf('order_total')];
		}
		else
		{
			$order_val=$row[csf('po_total_price')];
		}
		$const_wise_arr[$const_data][$row[csf('is_confirmed')]]['po_qty']+=$row[csf('order_quantity')];
		$const_wise_arr[$const_data][$row[csf('is_confirmed')]]['po_val']+=$order_val;
		$const_wise_arr[$const_data][$row[csf('is_confirmed')]]['set_smv']+=$row[csf('set_smv')];
		$const_wise_arr[$const_data][$row[csf('is_confirmed')]]['set_ratio']+=$row[csf('set_ratio')];
		$const_wise_arr[$const_data][$row[csf('is_confirmed')]]['lead_day']+=$row[csf('lead_time_days')];
		$const_wise_arr[$const_data][$row[csf('is_confirmed')]]['unit_price']+=$row[csf('unit_price')];
		$const_wise_arr[$const_data][$row[csf('is_confirmed')]]['job_no'].=$row[csf('prefix_job_no')].',';
		$const_wise_arr[$const_data][$row[csf('is_confirmed')]]['style'].=$row[csf('style_ref_no')].',';
		//if($row[csf('season')]!='')
		//{
		$const_wise_arr[$const_data][$row[csf('is_confirmed')]]['season'].=$row[csf('season')].',';
		//}
		$const_wise_arr[$const_data][$row[csf('is_confirmed')]]['pcode'].=$row[csf('product_code')].',';
		$const_wise_arr[$const_data][$row[csf('is_confirmed')]]['dealing_marchant'].=$row[csf('dealing_marchant')].',';
		//if($row[csf('bh_merchant')]!='')
		//{
		//$const_wise_arr[$const_data][$row[csf('is_confirmed')]]['bh_mer'].=$row[csf('bh_merchant')].',';
		//}
		$const_wise_arr[$const_data][$row[csf('is_confirmed')]]['po_no'].=$row[csf('po_number')].',';
		$const_wise_arr[$const_data][$row[csf('is_confirmed')]]['po_id'].=$row[csf('id')].',';
		$const_wise_arr[$const_data][$row[csf('is_confirmed')]]['po_received_date'].=$row[csf('po_received_date')].',';
		$const_wise_arr[$const_data][$row[csf('is_confirmed')]]['orgi_ship_date'].=$row[csf('orgi_ship_date')].',';
		$const_wise_arr[$const_data][$row[csf('is_confirmed')]]['c_ship_date'].=$row[csf('country_ship_date')].',';
		//d.percent_one,d.percent_two 
		$const_wise_arr[$const_data][$row[csf('is_confirmed')]]['percent'].=$percent_one.',';
		$const_wise_arr[$const_data][$row[csf('is_confirmed')]]['comp'].=$copm_one_id.',';
		$const_wise_arr[$const_data][$row[csf('is_confirmed')]]['const']=$row[csf('construction')];
		$const_wise_arr[$const_data][$row[csf('is_confirmed')]]['gsm'].=$row[csf('gsm_weight')].',';
		$const_wise_arr[$const_data][$row[csf('is_confirmed')]]['count_id'].=$row[csf('count_id')].',';
		$orgi_ship_date=$row[csf('orgi_ship_date')];
		$tod_week=$no_of_week_for_header[$orgi_ship_date];
		if($row[csf('is_confirmed')]==1)
		{
			$week_wise_order_qty[$const_data][$row[csf('is_confirmed')]][$c_date_week]['po_quantity']+=$row[csf("order_quantity")];
		}
		if($row[csf('is_confirmed')]==2)
		{
			$const_wise_arr2[$const_data][$row[csf('is_confirmed')]][$tod_week]['proj_po_qty']+=$row[csf("po_qty_pcs")];
			
		}
		
	}
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
					$poIds_cond.=" b.id  in($ids) or ";
				}
				$poIds_cond=chop($poIds_cond,'or ');
				$poIds_cond.=")";
			}
			else
			{
				$poIds_cond=" and  b.id  in($all_po_id)";
			}
		}
		$exfactory_data_array=array();
		$exfactory_sql=("select e.construction,b.is_confirmed,
		sum(CASE WHEN f.entry_form!=85 THEN f.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		sum(CASE WHEN f.entry_form=85 THEN f.ex_factory_qnty ELSE 0 END) as return_qnty
		 from wo_po_details_master a, wo_po_break_down b,pro_ex_factory_mst f,wo_po_color_size_breakdown c,wo_pre_cost_fab_yarn_cost_dtls d,wo_pre_cost_fabric_cost_dtls e  where  a.job_no=b.job_no_mst and  f.po_break_down_id=b.id and c.job_no_mst=a.job_no and b.id=c.po_break_down_id and d.job_no=a.job_no and 
	e.id=d.fabric_cost_dtls_id and  c.item_number_id=e.item_number_id and e.job_no=a.job_no and c.po_break_down_id=f.po_break_down_id   and  a.company_name=$company_name  and f.status_active=1 and f.is_deleted=0 $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $team_cond group by  e.construction, b.is_confirmed");
		$exfactory_data=sql_select($exfactory_sql);
		foreach($exfactory_data as $row)
		{
				$const_data=$row[csf('construction')];
				$exfactory_data_array[$const_data][$row[csf('is_confirmed')]][ex_qnty]+=$row[csf('ex_factory_qnty')]-$row[csf('return_qnty')];
		}
		unset($exfactory_data);
	
		ob_start();
		$rowcount=0;
		foreach($week_counter_header as $mon_key=>$week)
		{
			$rowcount+=count($week);
		}
		 $rowb=$rowcount*2;
		 $td_width=1780+($rowcount*45)+$rowb;
		$tot_month = datediff( 'm', $date_start,$date_end);
		for($i=0; $i<= $tot_month; $i++ )
		{
			$next_month=month_add($date_start,$i);
			$month_arr.=date("Y-M",strtotime($next_month)).',';
		}
	 	$month_arr_id=rtrim($month_arr,',');
	if($tot_rows>0)
	{
	?>
		 <script>
			var mon_week ='<? echo $month_arr_id; ?>';
			var mon_week=mon_week.split(",");
			
		for (var k=0; k<mon_week.length; k++)
		{
			var mon_data=document.getElementById('summary_footer_td_'+mon_week[k]).innerHTML;
			var mon_arr=mon_data.split("$");
			var mon_qnty=number_format(mon_arr[0],0);
			var mon_amt=number_format(mon_arr[1],0);
			var avg_rate=(number_format(mon_arr[1],0,'.','')/number_format(mon_arr[0],0,'.',''));
			
			var avg_order_smv=number_format(mon_arr[2],2);
			document.getElementById('summary_header_td_qty_'+mon_week[k]).innerHTML=mon_qnty;
			document.getElementById('summary_header_td_val_'+mon_week[k]).innerHTML='$'+mon_amt;
			document.getElementById('summary_header_td_rate_'+mon_week[k]).innerHTML='FOB:'+number_format(avg_rate,2);
			document.getElementById('summary_header_td_smv_'+mon_week[k]).innerHTML='SMV:'+number_format(avg_order_smv,2);
			
		}
		var yarn_req_qty=document.getElementById('td_yarn_req_march').innerHTML;
		var yarnreq_qty=number_format(yarn_req_qty,0);
		document.getElementById('yarn_summary_tot').innerHTML=yarnreq_qty;
		</script>
        
        <?
	}
		?>
		<div style="width:<? echo $td_width+20;?>px">
		<fieldset style="width:100%;">	
      
        
			<table width="<? echo $td_width+20;?>px">
				<tr class="form_caption">
					<td align="left" style="margin-left:10px" colspan="<? echo $rowcount+25; ?>">Construction Wise</td>
				</tr>
				<tr class="form_caption" align="center" style="display:none">
					<td colspan="<? echo $rowcount+26; ?>" align="center"><? //echo $company_library[$company_name]; ?></td>
				</tr>
			</table>
			<table  style="font:'Arial Narrow'; font-size:9px"  class="rpt_table" width="<? echo $td_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
                  <tr>
					
                   <th  width="" colspan="11" rowspan="2">&nbsp;</th>
                     <th  rowspan="2" id="yarn_summary_tot"></th>
                     <th  width="" colspan="14" rowspan="2">&nbsp;</th>
                     <?
					
					foreach($week_counter_header as $mon_key=>$week)
					{
					?>
					<th   align="center" colspan="<? echo count($week);?>">
					<? 
					//echo $week_key."<br/>".change_date_format($week_start_day[$week_key][week_start_day],"dd-mm-yyyy","-")." To ".change_date_format($week_end_day[$week_key][week_end_day],"dd-mm-yyyy","-");
					echo $mon_key;
					?>
					</th>
					<?
					}
            ?>
                   
                </tr>
                  <tr>
					
                     <?
					//foreach($month_arr as $mon_key)
				
					foreach($week_counter_header as $mon_key=>$week_val)
					{
					?>
					<th width="60"   style="font-size: 0.836em;"  align="center" colspan="<? echo count($week_val)?>">
                               <b id="summary_header_td_qty_<? echo $mon_key ;?>"  style="text-align:left;font-size: 0.795em;"> </b> &nbsp;
                                <b id="summary_header_td_val_<? echo $mon_key ;?>" style="text-align: right;font-size: 0.795em;"> </b> &nbsp;
                                <b id="summary_header_td_rate_<? echo $mon_key ;?>"  style="text-align: right;font-size: 0.795em;"></b> &nbsp;
                             	<b id="summary_header_td_smv_<? echo $mon_key ;?>"  title="Avg smv" style="text-align: right;font-size: 0.795em;"></b>
					</th>
					<?
					}
            ?>
                   
                </tr>
                <tr>
					<th width="30">SL</th>
                    <th width="55">Season</th>
                    <th width="80">BH Mer.</th>
					<th width="70">FFL Mer.</th>
                    <th width="55">Job No</th>
					<th width="120">Style Ref</th>
                    <th width="60">Comp%</th>
                    <th width="120">Comp Name</th>
                    <th width="120">Const.</th>
                    <th width="50">GSM</th>
					<th width="120">Y/C</th>
                    <th width="70">Yarn Qty</th>
					<th width="60">Status</th>
					<th width="45">OPD</th>
					<th width="50">OPD Date</th>
					<th width="45">TOD</th>
                    <th width="70">Ord Qty</th>
					<th width="100">Order No</th>
                    <th width="40">Dept </th>
					<th width="40">Lead Time</th>
					<th width="40">Unit Price</th>
					<th width="90">Value</th>
					<th width="40">SMV/ Pcs</th>
                    <th width="90">Ord.SMV</th>
                    <th width="60">Delv Qty</th>
                    <th width="60">Delv.Val</th>
                     <?
					//foreach($week_for_header as $week_key)
					foreach($week_counter_header as $mon_key=>$week)
					{
						foreach($week as $week_key)
						{
							$start_week_date=$week_start_day[$week_key][week_start_day];
							$end_week_date=$week_end_day[$week_key][week_end_day];
							//$fist_day=$week_day_arr[$week_key][$start_week_date][week_first_day];
							//$last_day=$week_day_arr[$week_key][$end_week_date][week_last_day];
							//.'('.$fist_day.')'
							if($db_type==2)
							{
								$fisrtday=date("D",strtotime(change_date_format($start_week_date,"dd-mm-yyyy","-")));
								$lastday=date("D",strtotime(change_date_format($end_week_date,"dd-mm-yyyy","-")));
							}
							else
							{
								$fisrtday=date("D",strtotime(change_date_format($start_week_date,"dd-mm-yyyy")));
								$lastday=date("D",strtotime(change_date_format($end_week_date,"dd-mm-yyyy")));
							}
							$weekstart_date=explode("-",date("d-M",strtotime($start_week_date)));
							$weekstart_date=$weekstart_date[0].'/'.$weekstart_date[1];
							
						?>
						<th title="<? echo change_date_format($start_week_date,"dd-mm-yyyy","-").'('.$fisrtday.')'." To ".change_date_format($end_week_date,"dd-mm-yyyy","-").'('.$lastday.')'; ?>" width="45"  align="center">
						
						<? 
							echo 'W'.$week_key.'<br/>'.$weekstart_date;
						?>
						</th>
						<?
						}
					}
            ?>
                   
                </tr>    
				</thead>
			</table>
			<div style="width:<? echo $td_width+20;?>px; max-height:245px; overflow-y:scroll;overflow-x:hidden;" id="scroll_body">
			<table class="rpt_table" width="<? echo $td_width;?>px" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body_const">
	<?
	
               
			$condition= new condition();
			 $condition->company_name("=$cbo_company_name");
			 if(str_replace("'","",$cbo_buyer_name)>0)
			 {
				  $condition->buyer_name("=$cbo_buyer_name");
			 }
			 if(str_replace("'","",$txt_job_no) !='')
			 {
				  $condition->job_no_prefix_num("in($txt_job_no)");
			 }
			
			 if(str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
			 {
			 $condition->pub_shipment_date(" between '$start_date' and '$end_date'");
			 }
				
			 if(str_replace("'","",$txt_order_no)!='')
			 {
				$condition->po_number("=$txt_order_no"); 
			 }
			 if(str_replace("'","",$txt_season)!='')
			 {
				//$condition->season("=$txt_season"); 
			 }
			 $condition->init();
			 
			 	
         		$yarn= new yarn($condition);
		 	 	//echo $yarn->getQuery(); die;
		 		$yarn_req_qty_arr=$yarn->getOrderWiseYarnQtyArray();
				//print_r($yarn_req_qty_arr);
				$i=1;$total_order_qty=0;$total_order_value=0;$total_week_qty=0;$total_yarn_req_qty=0;$total_order_smv=0;$total_ex_fact_val=0;$total_ex_fact_qty=0;
				foreach($const_wise_arr as $const_key=>$const_data)
				{  
				   
				   	foreach($const_data as $status_key=>$status_val)
					{
				 if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";   
				$po_received_date=$status_val['po_received_date'];
				//$conf_country_ship_date=rtrim($wo_color_data_arr2[$const_key][$status_key]['shipdate'],',');
				//$conf_country_ship_date=explode(",",$conf_country_ship_date);
				
				$comp_data=explode("**",$comp_key);
				$copm_one_id=$comp_data[0];
				$copm_two_id=$comp_data[1];
				
				$percent_one=rtrim($status_val['percent'],',');
				$construction=rtrim($status_val['const'],',');
				$gsm_weight=rtrim($status_val['gsm'],',');
				$comp=rtrim($status_val['comp'],',');
				$gsm_weight=implode(",",array_unique(explode(",",$gsm_weight)));
				$construction=implode(",",array_unique(explode(",",$construction)));
				
				$percent_data=array_unique(explode(",",$percent_one));
				$comp_data=array_unique(explode(",",$comp));
				
				$percent_one_cond="";$percent_two_cond="";
				foreach($percent_data as $perid)
				{
						$percent_data=array_unique(explode("**",$perid));
						$percent_one=$percent_data[0];
						$percent_two=$percent_data[1];
					if($percent_one_cond=="") $percent_one_cond=$percent_one;else $percent_one_cond.=",".$percent_one;
					if($percent_two_cond=="") $percent_two_cond=$percent_two;else $percent_two_cond.=",".$percent_two;
				}
				if($percent_one_cond!='' && $percent_two_cond!='')
					{
						//if($percent_two!=0)
						$percent_all=$percent_one_cond.'/'.$percent_two_cond;
					}
					else if($percent_one_cond!='' && $percent_two_cond=='')
					{
						//if($percent_two!=0)
						$percent_all=$percent_one_cond;
					}
					else if($percent_one_cond=='' && $percent_two_cond!='')
					{
						//if($percent_two!=0)
						$percent_all=$percent_two_cond;
					}
					else
					{
						$percent_all='';	
					}
				
				$comp_one_cond="";$comp_two_cond="";
				foreach($comp_data as $compid)
				{
						$comp_data=array_unique(explode("**",$compid));
						$comp_one=$comp_data[0];
						$comp_two=$comp_data[1];
						
					if($comp_one_cond=="") $comp_one_cond=$composition[$comp_one];else $comp_one_cond.=",".$composition[$comp_one];
					if($comp_two_cond=="") $comp_two_cond=$composition[$comp_two];else $comp_two_cond.=",".$composition[$comp_two];
				}
				
				if($comp_one_cond!='' && $comp_two_cond!='')
				{ 
					$copm_one_name=$comp_one_cond.'/'.$comp_two_cond;
				}
				else if($comp_one_cond!='' && $comp_two_cond=='')
				{ 
					$copm_one_name=$comp_one_cond;
				
				}
				else if($comp_one_cond=='' && $comp_two_cond!='')
				{ 
					$copm_one_name=$comp_two_cond;
				
				}
				else 
				{
					$copm_one_name='';
				}
				
				$count_id=rtrim($status_val['count_id'],',');
				 $count_ids=array_unique(explode(",", $count_id));
				 $yarn_count_value="";
				foreach($count_ids as $val)
				{
						if($yarn_count_value=="") $yarn_count_value=$lib_yarn_count_arr[$val]; else $yarn_count_value.=", ".$lib_yarn_count_arr[$val];
				}
				$seasion_ids=rtrim($status_val['season'],',');
				$seasion_ids=array_unique(explode(",",$seasion_ids));
				$seasion_id=$seasion_ids[0];
				$job_no=rtrim($status_val['job_no'],',');
				$job_no=implode(",",array_unique(explode(",",$job_no)));
				$style=rtrim($status_val['style'],',');
				$po_no=rtrim($status_val['po_no'],',');
				$po_id=rtrim($status_val['po_id'],',');
			
				$dealing_marchant=rtrim($status_val['dealing_marchant'],',');
				$bh_mers=rtrim($status_val['bh_mer'],',');
				//$bh_mer=implode(",",array_unique(explode(",",$bh_mer)));
				$bh_mers=array_unique(explode(",",$bh_mers));
				$bh_mer=$bh_mers[0];
				$po_received_date=rtrim($status_val['po_received_date'],',');
				$c_ship_date=rtrim($status_val['c_ship_date'],',');
				$job_no=implode(",",array_unique(explode(",",$job_no)));
				$style=implode(",",array_unique(explode(",",$style)));
				$po_no=implode(",",array_unique(explode(",",$po_no)));
				$po_id=array_unique(explode(",",$po_id));
			
				$yarn_req=0;
				foreach($po_id as $pid)
				{
				$yarn_req+=$yarn_req_qty_arr[$pid];
				}
				
				$seasion_cond=implode(",",array_unique(explode(",",$seasion_id)));
				/*$seasion_cond="";
				foreach($seasion_id as $sid)
				{
					if($seasion_cond=="") $seasion_cond=$lib_season_name_arr[$sid];else $seasion_cond.=",".$lib_season_name_arr[$sid];
				}*/
				$dealing_marchant=array_unique(explode(",",$dealing_marchant));
				$dealing_marchant_con="";
				foreach($dealing_marchant as $mid)
				{
					if($dealing_marchant_con=="") $dealing_marchant_con=$team_member_arr[$mid];else $dealing_marchant_con.=",".$team_member_arr[$mid];
				}
				$po_received_date=array_unique(explode(",",$po_received_date));
				$opd_week="";$opd_recv_date="";$opd_recv_date_full="";
				//$lead_time=0;
				foreach($po_received_date as $op_id)
				{
					if($opd_week=="") $opd_week=$no_of_week_for_header[$op_id];else $opd_week.=",".$no_of_week_for_header[$op_id];
					if($opd_recv_date=="") $opd_recv_date=date("d-M",strtotime($op_id));else $opd_recv_date.=",".date("d-M",strtotime($op_id));
					if($opd_recv_date_full=="") $opd_recv_date_full=date("d-M-y",strtotime($op_id));else $opd_recv_date_full.=",".date("d-M-y",strtotime($op_id));
				//$lead_time = datediff( 'd', $op_id,$countryship_date);
				}
				$po_orgi_shipdate=rtrim($status_val['orgi_ship_date'],',');	
				$po_orgi_shipdate=array_unique(explode(",",$po_orgi_shipdate));
				$tod_weeks="";$tod_recv_date="";
				foreach($po_orgi_shipdate as $orgi_id)
				{
					//echo $orgi_id;
					if($tod_weeks=="") $tod_weeks=$no_of_week_for_header[$orgi_id];else $tod_weeks.=",".$no_of_week_for_header[$orgi_id];
				}
				$tod_weeks=implode(",",array_unique(explode(",",$tod_weeks)));
				
				$lead_time =$status_val['lead_day'];
				$opd_week=implode(",",array_unique(explode(", ",$opd_week)));
				$opd_recv_date=implode(",",array_unique(explode(", ",$opd_recv_date)));
				$prod_cond=rtrim($status_val['pcode'],',');
				$prod_cond=implode(",",array_unique(explode(",",$prod_cond)));
				if($status_key==1) //Confirm $conf_country_ship_date
				{
					$c_ship_date_con=array_unique(explode(",",$c_ship_date));$qnty_array_dd=array();
					$c_week_qty=0;
					foreach($c_ship_date_con as $c_date)
					{
						if($db_type==0) $c_date_con=date("d-m-y",strtotime($c_date));
						else $c_date_con=date("d-M-y",strtotime($c_date));
						$c_week=$no_of_week_for_header_calc[$c_date_con];
						$c_week_qty=$week_wise_order_qty[$const_key][$status_key][$c_week]['po_quantity'];
								//echo $c_week.'=='.$c_week_qty.'hj';
						$qnty_array_dd[$c_week]=$c_week_qty;
					}
					//print_r($qnty_array_dd);
						$tot_order_qty=0;$tot_order_qty_arr=array();
						foreach($week_counter_header as $mon_key=>$week)
						{
							//$order_qty_arr=array();
							foreach($week as $week_key)
							{
								 $tot_order_qty=$qnty_array_dd[$week_key];
								  $order_qty_arr[$week_key]=$tot_order_qty;
								  $tot_order_qty_arr[$const_key]+=$tot_order_qty;
							}
						}
				}
				else 
				{
						$po_orgi_shipdate=rtrim($status_val['orgi_ship_date'],',');
						$proj_orgi_shipdate=array_unique(explode(",",$po_orgi_shipdate));
						$qnty_array_mm=array();$proj_po_qty=0;
						//$qnty_arr=distribute_projection($tod_week, $tot_proj_po_qty);
						$week_check=array();
						foreach($proj_orgi_shipdate as $tod_key)
						{
							$tod_week=$no_of_week_for_header[$tod_key];
							if($week_check[$tod_week]=='')
							{ //
								$proj_po_qty=$const_wise_arr2[$const_key][$status_key][$tod_week]['proj_po_qty'];
							
								$qnty_arr=distribute_projection($tod_week, $proj_po_qty);
								foreach($qnty_arr as $key=>$val)
								{
									$qnty_array_mm[$key]+=$val;
								}
								
								$week_check[$tod_week]=$proj_po_qty;
							}
						}
					
							$order_qty=0;$tot_order_qty_arr=array();
							foreach($week_counter_header as $mon_key=>$week)
							{
								foreach($week as $week_key)
								{
									$order_qty=$qnty_array_mm[$week_key];
									$order_qty_arr[$week_key]=$order_qty;
									$tot_order_qty_arr[$const_key]+=$order_qty;
								}
							}
				}
				$tot_po_qty=$tot_order_qty_arr[$const_key];
				$po_value=$status_val['po_val'];
				$unit_price=$po_value/$tot_po_qty;
				
				$job_no_row=count(array_unique(explode(",",$job_no)));
				if($job_no_row>1)
				{
					 $view_button="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$const_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button=$job_no;	
				}
				//echo $tod_weeks;
				$tod_weeks_row=count(array_unique(explode(",",$tod_weeks)));
				if($tod_weeks_row>1)
				{
					 $view_button_tod="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$const_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_tod=$tod_weeks;	
				}
				
				$opd_week_row=count(array_unique(explode(",",$opd_week)));
				if($opd_week_row>1)
				{
					 $view_button_opd="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$const_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_opd=$opd_week;	
				}
				$opd_recv_date_row=count(array_unique(explode(",",$opd_recv_date)));
				if($opd_recv_date_row>1)
				{
					 $view_button_opd_date="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$const_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_opd_date=$opd_recv_date;	
				}
				$po_no_row=count(array_unique(explode(",",$po_no)));
				if($po_no_row>1)
				{
					 $view_button_po="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$const_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_po=$po_no;	
				}
				$dealing_marchant_con_row=count(array_unique(explode(",",$dealing_marchant_con)));
				$bh_mer_con_row=count(array_unique(explode(",",$bh_mer)));
				$seasion_cond_row=count(array_unique(explode(",",$seasion_cond)));
				$construction_cond_row=count(array_unique(explode(",",$construction)));
				$gsm_weight_cond_row=count(array_unique(explode(",",$gsm_weight)));
				$yarn_count_value_cond_row=count(array_unique(explode(",",$yarn_count_value)));
				$style_cond_row=count(array_unique(explode(",",$style)));
				$copm_cond_row=count(array_unique(explode(",",$copm_one_name)));
				$prod_cond_row=count(array_unique(explode(",",$prod_cond)));
				if($dealing_marchant_con_row>1)
				{
					 $view_button_deal="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$const_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_deal=$dealing_marchant_con;
					$view_button_bh=$bh_mer;
					//$view_button_season=$seasion_cond;	
				}
				if($bh_mer_con_row>1)
				{
					 $view_button_bh="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$const_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					
					$view_button_bh=$bh_mer;
					//$view_button_season=$seasion_cond;	
				}
				if($seasion_cond_row>1)
				{
					 $view_button_season="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$const_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_season=$seasion_cond;	
				}
				if($construction_cond_row>1)
				{
					 $view_button_const="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$const_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_const=$construction;	
				}
				if($gsm_weight_cond_row>1)
				{
					 $view_button_gsm="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$const_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_gsm=$$gsm_weight;	
				}
				if($yarn_count_value_cond_row>1)
				{
					 $view_button_count="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$const_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_count=$yarn_count_value;	
				}
				if($style_cond_row>1)
				{
					 $view_button_style="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$const_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_style=$style;	
				}
				if($copm_cond_row>1)
				{
					 $view_button_comp="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$const_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_comp=$copm_one_name;	
				}
				if($prod_cond_row>1)
				{
					 $view_button_dept="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$const_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_dept=$prod_cond;
				}
				?>
                <tr style="font:'Arial Narrow'; font-size:9px" align="center" bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
					<td width="30"><? echo $i; ?></td>
                    <td width="55" style="word-wrap:break-word; word-break: break-all;"><? echo $seasion_id;?></td>
                    <td width="80" style="word-wrap:break-word; word-break: break-all;"><? echo $bh_mer; ?></td> 
					<td width="70" style="word-wrap:break-word; word-break: break-all;"><? echo $view_button_deal; ?></td> 
                    <td width="55" style="word-wrap:break-word; word-break: break-all;"> <? echo $view_button; ?></td>
					<td width="120" style="word-wrap:break-word; word-break: break-all;"> <? echo $view_button_style; ?></td>
                    <td width="60" style="word-wrap:break-word; word-break: break-all;"><? echo $percent_all; ?></td>
                    <td  width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $view_button_comp; ?></td>
                    <td  width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $const_key;?></td>
                    <td width="50" style="word-wrap:break-word; word-break: break-all;"><? echo $view_button_gsm; ?></td>
					<td  width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $view_button_count; ?></td>
                    <td  width="70" title="<? echo $yarn_req;?>" style="word-wrap:break-word; word-break: break-all; text-align:right">
					<?
							echo number_format($yarn_req);
				
					?>
                    </td>
					<td  width="60" style="word-wrap:break-word; word-break: break-all;">
					<?
					
					if($status_key==2)
					{
						echo "Booked";	
					}
					else
					{
						echo "Placed";
					}
					//echo $order_status[$row_data[csf('is_confirmed')]];
					?> 
                    </td>
					<td  width="45" style="word-wrap:break-word; word-break: break-all;">
					<?
					 echo $view_button_opd;
					
					?>
                    </td>
					<td  width="50" title="<? echo $opd_recv_date_full;?>" style="word-wrap:break-word; word-break: break-all;">
                    
					<?
					if($opd_recv_date!="" && $opd_recv_date !="0000-00-00" && $opd_recv_date!="0")
					{
						$opd_recv_date=$opd_recv_date;
					}
					 echo $view_button_opd_date;
					?>
                     
                    </td>
					<td width="45" title="Orgi Ship Date" style="word-wrap:break-word; word-break: break-all; text-align: center">
					<?  echo $view_button_tod;
					//if($no_of_week_for_header[$countryship_date]!='') echo 'W'.$no_of_week_for_header[$countryship_date];else echo ''; ?>
                    </td>
                    <td  width="70" style="word-wrap:break-word; word-break: break-all; text-align:right">
					<? echo number_format($tot_po_qty);?>
                    </td>
					<td  width="100" style="word-wrap:break-word; word-break: break-all; text-align: center">
                   
					<? echo $view_button_po; ?>
                    </td>
                    <td  width="40" style="word-wrap:break-word; word-break: break-all; text-align:left" >
					<? echo $view_button_dept;?>
                    </td>
					<td  width="40" title="TOD-OPD" style="word-wrap:break-word; word-break: break-all; text-align: center">
					<? //echo $lead_time; ?>
                    </td>
					<td  width="40" title="PO Value/Po Qty" style="word-wrap:break-word; word-break: break-all; text-align:right">
					<? echo number_format($unit_price,2); ?>
                    </td>
                   
					<td width="90" title="Po Qty*Unit Price" style="word-wrap:break-word; word-break: break-all;text-align:right">
                   <? echo number_format($tot_po_qty*$unit_price,0);
				  	 $set_smv=number_format($status_val['set_smv'],2);
				    ?>
                    </td>
					<td width="40" title="Set SMV/Set Ratio" style="word-wrap:break-word; word-break: break-all;">
					<? 
					$smv_rate=0;
					$smv_rate=$set_smv/$status_val['set_ratio'];
					echo number_format($smv_rate,2); ?>
                    </td>
					
                    <td  width="90" title="Set SMV Per Pcs*Order Qty" style="word-wrap:break-word; word-break: break-all;text-align:right">
					<? echo number_format(($set_smv/$status_val['set_ratio'])*$tot_po_qty); ?>
                    </td>
                     <td  width="60" style="word-wrap:break-word; word-break: break-all;text-align:right">
					<? echo number_format($exfactory_data_array[$const_key][$status_key][ex_qnty]); ?>
                    </td>
                    <td width="60" title="Ex-Fact Qty*Unit Price" style="word-wrap:break-word; word-break: break-all;text-align:right">
                    <? echo  number_format($exfactory_data_array[$const_key][$status_key][ex_qnty]*$unit_price,0);
					 ?>
                    </td>
                    <?
					
					$week_qty=0;
					$week_check=array();
                   	foreach($week_counter_header as $mon_key=>$week)
					{
						foreach($week as $week_key)
						{
						?>
						<td  style="word-wrap:break-word; word-break: break-all;" width="45" align="right">
						<? 
							 	$week_qty=$order_qty_arr[$week_key];
								if($week_qty>0) echo number_format($week_qty,0); else echo ' ';
								$tot_week_qty[$mon_key][$week_key]+=$week_qty;
								$tot_week_amount[$mon_key][$week_key]+=$week_qty*$unit_price;
								$smv_avg=0;
								$smv_avg=$smv_rate*$week_qty;
								$month_smv_arr[$mon_key]+=$smv_avg;
						?>
						</td>
						<?
						}
					}
					?>
                    </tr>
                    <?
					$total_order_qty+=$tot_po_qty;
					$total_yarn_req_qty+=$yarn_req;	
					$total_ex_fact_qty+=$exfactory_data_array[$const_key][$status_key][ex_qnty];
					$total_ex_fact_val+=$exfactory_data_array[$const_key][$status_key][ex_qnty]*$unit_price;
					$total_order_value+=$tot_po_qty*$unit_price;
					$total_order_smv+=($set_smv/$status_val['set_ratio'])*$tot_po_qty;
					$total_week_qty+=$week_qty;
					//=====================================================================================================================
				$i++;
					}
					
				}
				?>
                 
				</table>
				</div>
                <table style="font:'Arial Narrow'; font-size:9px" class="tbl_bottom" width="<? echo $td_width;?>px" cellpadding="0" cellspacing="0" id="report_table_footer" border="1" rules="all" >
					<tr>
					<td width="30">&nbsp;</td>
                    <td width="55">&nbsp;</td>
                    <td width="80">&nbsp;</td>
					<td width="70">&nbsp;</td>
                    <td width="55">&nbsp;</td>
					<td width="120">&nbsp;</td>
                    <td width="60">&nbsp;</td>
                    <td width="120">&nbsp;</td>
                    <td width="120">&nbsp;</td>
                    <td width="50">&nbsp;</td>
					<td width="120">&nbsp;</td>
                    <td width="70" style="word-wrap:break-word; word-break: break-all; font-size:smaller" id="td_yarn_req_march"><? echo $total_yarn_req_qty;?></td>
					<td width="60">&nbsp;</td>
					<td width="45">&nbsp;</td>
					<td width="50">&nbsp;</td>
					<td width="45">&nbsp;</td>
                    <td width="70" style="word-wrap:break-word; word-break: break-all; font-size:smaller" id="td_po_qty_march"><? echo $total_order_qty;?></td>
					<td width="100">&nbsp;</td>
                    <td width="40">&nbsp; </td>
					<td width="40">&nbsp;</td>
					<td width="40"><? echo number_format($total_order_value/$total_order_qty,2);?></td>
					<td width="90" id="td_po_val_march" style="word-wrap:break-word; word-break: break-all; font-size:smaller"><? echo number_format($total_order_value);?></td>
					<td width="40"><? echo number_format($total_order_smv/$total_order_qty,2);?></td>
                    <td width="90" style="word-wrap:break-word; word-break: break-all; font-size:smaller" id="total_order_smv"><? echo number_format($total_order_smv,0);?></th>
                    <td width="60" style="word-wrap:break-word; word-break: break-all; font-size:smaller" id="total_order_exfc_qty"><? echo number_format($total_ex_fact_qty,0);?></td>
                    <td width="60" style="word-wrap:break-word; word-break: break-all; font-size:smaller" id="total_order_exfc_val"><? echo number_format($total_ex_fact_val,0);?></td>
                     <?
					//foreach($week_for_header as $week_key)
					$total_week_qt=0; 
					foreach($week_counter_header as $mon_key=>$week)
					{
						foreach($week as $week_key)
						{
						?>
						<td width="45" style="word-wrap:break-word; word-break: break-all;font-size:smaller"  align="center">
						
						<? 
						$total_week_qty=$tot_week_qty[$mon_key][$week_key];
						$total_week_amt=$tot_week_amount[$mon_key][$week_key];
						echo number_format($total_week_qty,0);
						$tot_mon_qty[$mon_key]+=$total_week_qty;
						$tot_mon_amount[$mon_key]+=$total_week_amt;
						?>
						</td>
						<?
						
						}
					}
            ?>
					</tr>
                    <tr style="display:none">
					<td colspan="26">&nbsp;</td>
                     <?
					$total_week_qt=0; 
					foreach($week_counter_header as $mon_key=>$week)
					{
						?>
						<td width="" id="summary_footer_td_<? echo $mon_key?>" colspan="<? echo count($week);?>" title="<? echo $tot_week_amount[$mon_key][$week_key];?>" style="word-wrap:break-word; word-break: break-all;"   align="center">
						
						<? 
						$tot_smv=0;
						$tot_smv=$month_smv_arr[$mon_key]/$tot_mon_qty[$mon_key];
						
						$head_td_data=$tot_mon_qty[$mon_key].'$'.$tot_mon_amount[$mon_key].'$'.$tot_smv;
						echo $head_td_data;
						?>
						</td>
						<?
					}
            ?>
                    
                    </tr>
				</table>
			</fieldset>
		</div>
          <style>
			@font-face {
   				 font-family:"Arial Narrow"; font-size:11px;
			}
			TD{font-family:"Arial Narrow";font-size:10px;}
			TH{font-family:"Arial Narrow";font-size:11px;}
			.rpt_table TD{font-family: "Arial Narrow";font-size:11px;}
			.rpt_table TH{font-family:"Arial Narrow";font-size:11px;}
		</style>
	<?
	}
//===========================================================================================================================================================
	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data****$filename****$tot_rows****$cbo_search_type";
	exit();	
	
}
else if($action=="report_generate_count") //Count Wise
{
 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company_name=str_replace("'","",$cbo_company_name);
	$cbo_search_type=str_replace("'","",$cbo_search_type);
	$cbo_bh_mer_name=str_replace("'","",$cbo_bh_mer_name);
	$txt_yarn_count=str_replace("'","",$txt_yarn_count);
	if($txt_yarn_count!='') $yarn_count_con="and yarn_count Like '%$txt_yarn_count%' ";else $yarn_count_con='';
	$buyer_id_cond="";
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{
				$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else
			{
				$buyer_id_cond="";
			}
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}
	

	
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_job_no=trim($txt_job_no);
	if($txt_job_no !="" || $txt_job_no !=0)
	{
		//$year = substr(str_replace("'","",$cbo_year_selection), -2); 
		//$job_no=$company_library[$company_name]."-".$year."-".str_pad($txt_job_no, 5, 0, STR_PAD_LEFT);
		$jobcond="and a.job_no_prefix_num in(".$txt_job_no.")";
	}
	else
	{
		$jobcond="";	
	}
	
	
	$date_cond='';
	/*if(str_replace("'","",$cbo_category_by)==1)
	{*/
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			$start_date=(str_replace("'","",$txt_date_from));
			$end_date=(str_replace("'","",$txt_date_to));
			
			$date_cond="and b.pub_shipment_date between '$start_date' and '$end_date'";
		}
	//}
	/*if(str_replace("'","",$cbo_category_by)==2)
	{
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			$start_date=(str_replace("'","",$txt_date_from));
			$end_date=(str_replace("'","",$txt_date_to));
			$date_cond="and b.po_received_date between '$start_date' and '$end_date'";
		}
	}*/
	//if (str_replace("'","",$txt_job_no)=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in (".str_replace("'","",$txt_job_no).") ";
	
	if(str_replace("'","",$txt_style_ref)!="") $style_ref_cond=" and a.style_ref_no like '%".str_replace("'","",$txt_style_ref)."%'"; else $style_ref_cond="";
	if(str_replace("'","",$txt_order_no)!="")
	{
		$ordercond=" and b.po_number like '%".str_replace("'","",$txt_order_no)."%'"; 
	}
	else
	{
		$ordercond="";
	}
	
	$team_cond="";
	if(str_replace("'","",$cbo_team_name)!=0) $team_cond=" and a.team_leader=".str_replace("'","",$cbo_team_name)."  ";
	if(str_replace("'","",$cbo_team_member)!=0) $team_cond.=" and a.dealing_marchant=".str_replace("'","",$cbo_team_member)."  ";
	$cbo_bh_mer_name=str_replace("0","",$cbo_bh_mer_name);
	if($cbo_bh_mer_name!=0 || $cbo_bh_mer_name!='') $bh_mer_name_con="and a.bh_merchant='$cbo_bh_mer_name' ";else $bh_mer_name_con='';
	 $cbo_year_selection=str_replace("'","",$cbo_year_selection);
	

function week_of_year($year,$week_start_day)
{
	$week_array=array();
	$week=0;
	for($i=1;$i<=12; $i++)
	{
		$month=str_pad($i, 2, '0', STR_PAD_LEFT);
		$year=$year;
		$first_date_of_year=$year."-01-01";
		$first_day_of_year=date('l', strtotime($first_date_of_year));
		if($i==1)
		{
		if(date('l', strtotime($first_day_of_year))==$week_start_day)
		{
			$week=0;
		}
		else
		{
			$week=1;
		}
		}
		$days_in_month = cal_days_in_month(0, $month, $year) ;
		
		foreach (range(1, $days_in_month) as $day) 
		{
			$test_date = $year."-".$month."-" . str_pad($day, 2, '0', STR_PAD_LEFT);
			global $db_type;
			if($db_type==2)
			{
				
				$test_date = change_date_format($test_date,'dd-mm-yyyy','-',1);
				//echo $test_date;
			}
			
			if(date('l', strtotime($test_date))==$week_start_day)
			{
			  $week++;
			}
			
			$week_day=date('l', strtotime($test_date));
			$week_array[$test_date]=$week;
			
			
			/*$con = connect();//the connection have to be called out of function
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$id=return_next_id( "id", "week_of_year", 1 );
			$field_array="id, year, month, week, week_start_day, week_date,week_day";
			$data_array="(".$id.",".$year.",".$month.",".$week.",'".$week_start_day."','".$test_date."','".$week_day."')";
			$rID=sql_insert("week_of_year",$field_array,$data_array,0);
			if($db_type==0)
			{
				if($rID){
					mysql_query("COMMIT");  
				}
				else{
					mysql_query("ROLLBACK"); 
				}
			}
			if($db_type==2 || $db_type==1 )
			{
				if($rID){
					oci_commit($con); 
				}
				else{
					oci_rollback($con); 
				}
			}*/
			
		}
	
	}
	return $week_array ;
}
$start_ddd= strtotime(change_date_format($start_date,"dd-mm-yyyy","-"));
$start_ddd=date("Y-m-d",$start_ddd);
$start_end_date= strtotime(change_date_format($end_date,"dd-mm-yyyy","-"));
$start_end_date=date("Y-m-d",$start_end_date);
 //$dd= date('d-m-Y',strtotime($start_date));
//$start_date_week= date($dd, time() - 1296000);//$start_date;
//echo $last_day_this_month  = date($start_date_week, time() -1814400); //date('t-m-Y');

$dateee = strtotime($start_ddd);
$start_end_date = strtotime($start_end_date);

$date_start = date("Y-m-d",strtotime("-28 day", $dateee));
$date_end = date("Y-m-d",strtotime("+28 day", $start_end_date));
//echo $date_end;
//$end_date_week=  addday("$end_date", time() -1814400);//$start_date;//$start_date;
$weekarr=week_of_year($cbo_year_selection,"Monday");
if($db_type==0)
{
	$date_start= change_date_format($date_start,"yyyy-mm-dd");
	$date_end= change_date_format($date_end,"yyyy-mm-dd");
}
else
{
	$date_start= change_date_format($date_start,"dd-mm-yyyy","-",1);
	$date_end= change_date_format($date_end,"dd-mm-yyyy","-",1);
}


//echo "select week_date,week from week_of_year where week_date between '$date_start' and  '$date_end' order by week_date asc";die;
// date("d-m-Y", time() - 1296000);
$week_for_arr=array();$no_of_week_for_header=array();
$sql_week_header=sql_select("select week_date,week from week_of_year where week_date between '$date_start' and  '$date_end' order by week_date asc");
$week_check_head=array();
foreach ($sql_week_header as $row_week_header)
{
	if($week_check_head[$row_week_header[csf("week")]]=='')
	{
		$week_counter_header[date("Y-M",strtotime($row_week_header[csf("week_date")]))][$row_week_header[csf("week")]]=$row_week_header[csf("week")];
		$week_check_head[$row_week_header[csf("week")]]=$week_counter_header[date("Y-M",strtotime($row_week_header[csf("week_date")]))][$row_week_header[csf("week")]];
	}
		
	$tmp=add_date($row_week_header[csf("week_date")],-1);
	if($db_type==0) $tmp_cond=date("d-m-y",strtotime($tmp));
	else $tmp_cond=date("d-M-y",strtotime($tmp));
	$no_of_week_for_header_calc[$tmp_cond]=$row_week_header[csf("week")];
	$no_of_week_for_header[$row_week_header[csf("week_date")]]=$row_week_header[csf("week")];
	$test[$row_week_header[csf("week")]]=$row_week_header[csf("week")];
}
unset($sql_week_header);
$week_start_day=array();
$week_end_day=array();$week_day_arr=array();
//week_day
$sql_week_start_end_date=sql_select("select week,min(week_date) as week_start_day, Max(week_date) as week_end_day from week_of_year where week_date between '$date_start' and  '$date_end' group by week");
foreach ($sql_week_start_end_date as $row_week)
{
	$week_start_day[$row_week[csf("week")]][week_start_day]=$row_week[csf("week_start_day")];
	$week_end_day[$row_week[csf("week")]][week_end_day]=$row_week[csf("week_end_day")];
	//$week_day_arr[$row_week[csf("week")]][$row_week[csf("week_start_day")]][week_first_day]=$row_week[csf("min_week_day")];
	$week_day_arr[$row_week[csf("week_end_day")]]['week_last_day']=$row_week[csf("last_week_day")];
}
unset($sql_week_start_end_date);
function distribute_projection( $base_week, $qnty)
{
	//5. Distribution ratios are: Base week 52%, Immediate before 24%, Week before immediate week 20%, Immediate week after base week 2%, next 2 weeks as 1%
	global $test;
	 
	$tmpbase_week=$test[$base_week-3];
	$k=0;
	for($i=0; $i<6; $i++)
	{
		$tmpbase_week++;
		if( $test[$tmpbase_week]=='') { $tmpbase_week=1; $k=0; }
		
		
		if($i==0) $distr_arr[$tmpbase_week]=($qnty*20)/100;
		if($i==1) $distr_arr[$tmpbase_week]=($qnty*24)/100;
		if($i==2) $distr_arr[$tmpbase_week]=($qnty*52)/100;
		if($i==3) $distr_arr[$tmpbase_week]=($qnty*2)/100;
		if($i==4) $distr_arr[$tmpbase_week]=($qnty*1)/100;
		if($i==5) $distr_arr[$tmpbase_week]=($qnty*1)/100;
		$k++;
		
	}
	 
	return $distr_arr;
	/*5000
	b=52
	b-1=21
	b-2=15
	b+1=1
	b+2=1*/
}
//echo "<per>";
 //print_r(distribute_projection("2016-Dec",51, 5000)); die;


	
	if($template==1)
	{
	$po_wise_buyer=array();
	$buyer_wise_data=array();
	$po_id_arr=array();
	$week_wise_order_qty_arr=array();	
	
	 $count_data=sql_select("select yarn_count,id from  lib_yarn_count  where status_active=1 and is_deleted=0 and yarn_count is not null $yarn_count_con");
	$yarn_id='';
	foreach($count_data as $row)
	{
			if($yarn_id=='') $yarn_id=$row[csf('id')];else $yarn_id.=",".$row[csf('id')];
			
	}
		$yarn_ids=count(array_unique(explode(",",$yarn_id)));
		$yarn_numIds=chop($yarn_id,','); $yarnIds_cond="";
		if($txt_yarn_count!='')
		{
			if($yarn_id!='' || $yarn_id!=0)
			{
				if($db_type==2 && $yarn_ids>1000)
				{
					$yarnIds_cond=" and (";
					$yIdsArr=array_chunk(explode(",",$yarn_numIds),990);
					foreach($yIdsArr as $ids)
					{
						$ids=implode(",",$ids);
						$yarnIds_cond.=" d.count_id  in($ids) or ";
					}
					$yarnIds_cond=chop($yarnIds_cond,'or ');
					$yarnIds_cond.=")";
				}
				else
				{
					$yarnIds_cond=" and  d.count_id  in($yarn_id)";
				}
			}
		}
	
	
	 if($db_type==0) $date_dif="DATEDIFF(b.shipment_date, b.po_received_date) as lead_time_days";
	else  $date_dif="(b.shipment_date-b.po_received_date) as lead_time_days";
	  $sql_query=("select $date_dif,a.job_no,a.job_no_prefix_num as prefix_job_no,a.set_smv,a.season_buyer_wise  as season,a.product_code,a.dealing_marchant,a.bh_merchant,a.style_ref_no,b.id,b.is_confirmed,b.po_number,b.po_received_date,b.shipment_date as orgi_ship_date,a.total_set_qnty as set_ratio,(b.po_quantity*a.total_set_qnty) as po_qty_pcs,c.order_quantity,c.order_total,c.country_ship_date,b.po_total_price,a.avg_unit_price as unit_price,d.count_id,d.copm_one_id,d.copm_two_id,d.percent_one,d.percent_two,e.construction,e.gsm_weight from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fab_yarn_cost_dtls d,wo_pre_cost_fabric_cost_dtls e
	 where a.job_no=b.job_no_mst and c.job_no_mst=a.job_no and b.id=c.po_break_down_id and d.job_no=a.job_no and 
	e.id=d.fabric_cost_dtls_id and  c.item_number_id= e.item_number_id and e.job_no=a.job_no  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1 and  d.count_id!=0  and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $bh_mer_name_con $ordercond $team_cond $yarnIds_cond order by d.count_id,a.job_no,b.is_confirmed "); 
	
					 
	$sql_data=sql_select($sql_query);
	$tot_rows=count($sql_data);
	$all_po_id="";
	$marchant_wise_arr=array();
	foreach( $sql_data as $row)
	{
		//$week_wise_qty_arr[date("Y-m",strtotime($row_data[csf("pub_shipment_date")]))]=$row_data[csf('pub_shipment_date')];
		$count_data=$row[csf('count_id')];
		$country_ship_date=$wo_color_data_arr[$count_data][$row[csf('is_confirmed')]]['c_shipdate'];
		if($all_po_id=="") $all_po_id=$row[csf("id")]; else $all_po_id.=",".$row[csf("id")];
		 
		if($row[csf('percent_two')]!=0) $row[csf('percent_two')]=$row[csf('percent_two')];else $row[csf('percent_two')]='';
		$percent_one=$row[csf('percent_one')].'**'.$row[csf('percent_two')];
		
		if($row[csf('copm_two_id')]!=0) $row[csf('copm_two_id')]=$row[csf('copm_two_id')];else $row[csf('copm_two_id')]='';
		$copm_one_id=$row[csf('copm_one_id')].'**'.$row[csf('copm_two_id')];
		
		if($db_type==0) $c_date_con=date("d-m-y",strtotime($row[csf('country_ship_date')]));
		else $c_date_con=date("d-M-y",strtotime($row[csf('country_ship_date')]));
		$c_date_week=$no_of_week_for_header_calc[$c_date_con];
		//$c_date_week=$no_of_week_for_header[$row[csf('country_ship_date')]];
		if($row[csf('is_confirmed')]==1)
		{
			$order_val=$row[csf('order_total')];
		}
		else
		{
			$order_val=$row[csf('po_total_price')];
		}
		$count_wise_arr[$count_data][$row[csf('is_confirmed')]]['po_qty']+=$row[csf('po_qty_pcs')];
		$count_wise_arr[$count_data][$row[csf('is_confirmed')]]['po_val']+=$order_val;
		$count_wise_arr[$count_data][$row[csf('is_confirmed')]]['set_smv']+=$row[csf('set_smv')];
		$count_wise_arr[$count_data][$row[csf('is_confirmed')]]['set_ratio']+=$row[csf('set_ratio')];
		$count_wise_arr[$count_data][$row[csf('is_confirmed')]]['lead_day']+=$row[csf('lead_time_days')];
		$count_wise_arr[$count_data][$row[csf('is_confirmed')]]['unit_price']+=$row[csf('unit_price')];
		$count_wise_arr[$count_data][$row[csf('is_confirmed')]]['job_no'].=$row[csf('prefix_job_no')].',';
		$count_wise_arr[$count_data][$row[csf('is_confirmed')]]['style'].=$row[csf('style_ref_no')].',';
		$count_wise_arr[$count_data][$row[csf('is_confirmed')]]['season'].=$row[csf('season')].',';
		$count_wise_arr[$count_data][$row[csf('is_confirmed')]]['pcode'].=$row[csf('product_code')].',';
		$count_wise_arr[$count_data][$row[csf('is_confirmed')]]['dealing_marchant'].=$row[csf('dealing_marchant')].',';
		$count_wise_arr[$count_data][$row[csf('is_confirmed')]]['bh_mer'].=$row[csf('bh_merchant')].',';
		$count_wise_arr[$count_data][$row[csf('is_confirmed')]]['po_no'].=$row[csf('po_number')].',';
		$count_wise_arr[$count_data][$row[csf('is_confirmed')]]['po_id'].=$row[csf('id')].',';
		$count_wise_arr[$count_data][$row[csf('is_confirmed')]]['po_received_date'].=$row[csf('po_received_date')].',';
		$count_wise_arr[$count_data][$row[csf('is_confirmed')]]['orgi_ship_date'].=$row[csf('orgi_ship_date')].',';
		$count_wise_arr[$count_data][$row[csf('is_confirmed')]]['c_ship_date'].=$row[csf('country_ship_date')].',';
		//d.percent_one,d.percent_two 
		$count_wise_arr[$count_data][$row[csf('is_confirmed')]]['percent'].=$percent_one.',';
		$count_wise_arr[$count_data][$row[csf('is_confirmed')]]['comp'].=$copm_one_id.',';
		$count_wise_arr[$count_data][$row[csf('is_confirmed')]]['const'].=$row[csf('construction')].',';
		$count_wise_arr[$count_data][$row[csf('is_confirmed')]]['gsm'].=$row[csf('gsm_weight')].',';
		$count_wise_arr[$count_data][$row[csf('is_confirmed')]]['count_id']=$row[csf('count_id')];
		$tod_week=$no_of_week_for_header[$row[csf('orgi_ship_date')]];
		if($row[csf('is_confirmed')]==1)
		{
			$week_wise_order_qty[$count_data][$row[csf('is_confirmed')]][$c_date_week]['po_quantity']+=$row[csf("order_quantity")];
		}
		if($row[csf('is_confirmed')]==2)
		{
			$week_wise_order_qty2[$count_data][$row[csf('is_confirmed')]][$tod_week]['proj_po_qty']+=$row[csf("po_qty_pcs")];
			
		}
	}
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
					$poIds_cond.=" b.id  in($ids) or ";
				}
				$poIds_cond=chop($poIds_cond,'or ');
				$poIds_cond.=")";
			}
			else
			{
				$poIds_cond=" and  b.id  in($all_po_id)";
			}
		}
		$exfactory_data_array=array();
		$exfactory_sql=("select d.count_id,b.is_confirmed,
		sum(CASE WHEN f.entry_form!=85 THEN f.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		sum(CASE WHEN f.entry_form=85 THEN f.ex_factory_qnty ELSE 0 END) as return_qnty
		 from wo_po_details_master a, wo_po_break_down b,pro_ex_factory_mst f,wo_po_color_size_breakdown c,wo_pre_cost_fab_yarn_cost_dtls d,wo_pre_cost_fabric_cost_dtls e  where  a.job_no=b.job_no_mst and  f.po_break_down_id=b.id and c.job_no_mst=a.job_no and b.id=c.po_break_down_id and d.job_no=a.job_no and 
	e.id=d.fabric_cost_dtls_id and  c.item_number_id=e.item_number_id and e.job_no=a.job_no and c.po_break_down_id=f.po_break_down_id   and  a.company_name=$company_name  and f.status_active=1 and f.is_deleted=0 $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $team_cond group by  d.count_id, b.is_confirmed");
		$exfactory_data=sql_select($exfactory_sql);
		foreach($exfactory_data as $row)
		{
				$count_data=$row[csf('count_id')];
				$exfactory_data_array[$count_data][$row[csf('is_confirmed')]][ex_qnty]+=$row[csf('ex_factory_qnty')]-$row[csf('return_qnty')];
		}
		unset($exfactory_data);
	
		ob_start();
		$rowcount=0;
		foreach($week_counter_header as $mon_key=>$week)
		{
			$rowcount+=count($week);
		}
		 $rowb=$rowcount*2;
		 $td_width=1780+($rowcount*45)+$rowb;
		 $tot_month = datediff( 'm', $date_start,$date_end);
		for($i=0; $i<= $tot_month; $i++ )
		{
			$next_month=month_add($date_start,$i);
			$month_arr.=date("Y-M",strtotime($next_month)).',';
		}
	 	$month_arr_id=rtrim($month_arr,',');
		
		if($tot_rows>0)
		{
	?>
		 <script>
			var mon_week ='<? echo $month_arr_id; ?>';
			var mon_week=mon_week.split(",");
		for (var k=0; k<mon_week.length; k++)
		{
			var mon_data=document.getElementById('summary_footer_td_'+mon_week[k]).innerHTML;
			var mon_arr=mon_data.split("$");
			var mon_qnty=number_format(mon_arr[0],0);
			var mon_amt=number_format(mon_arr[1],0);
			var avg_order_smv=number_format(mon_arr[2],2);
			var avg_rate=(number_format(mon_arr[1],0,'.','')/number_format(mon_arr[0],0,'.',''));
			//alert(mon_amt);
			document.getElementById('summary_header_td_qty_'+mon_week[k]).innerHTML=mon_qnty;
			document.getElementById('summary_header_td_val_'+mon_week[k]).innerHTML='$'+mon_amt;
			document.getElementById('summary_header_td_rate_'+mon_week[k]).innerHTML='FOB:'+number_format(avg_rate,2);
			document.getElementById('summary_header_td_smv_'+mon_week[k]).innerHTML='SMV:'+number_format(avg_order_smv,2);
			
		}
			var yarn_req_qty=document.getElementById('td_yarn_req_march').innerHTML;
			var yarnreq_qty=number_format(yarn_req_qty,0);
			document.getElementById('yarn_summary_tot').innerHTML=yarnreq_qty;
		</script>
        <?
		}
		?>
        <div style="width:<? echo $td_width+20;?>px">
		<fieldset style="width:100%;">	
      
        
			<table width="<? echo $td_width+20;?>px">
				<tr class="form_caption">
					<td align="left" style="margin-left:10px" colspan="<? echo $rowcount+25; ?>">Construction Wise</td>
				</tr>
				<tr class="form_caption" align="center" style="display:none">
					<td colspan="<? echo $rowcount+26; ?>" align="center"><? //echo $company_library[$company_name]; ?></td>
				</tr>
			</table>
			<table class="rpt_table" style="font:'Arial Narrow; font-size:9px'" width="<? echo $td_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
                  <tr>
					
                     <th  width="" colspan="11" rowspan="2">&nbsp;</th>
                     <th  rowspan="2" id="yarn_summary_tot"></th>
                     <th  width="" colspan="14" rowspan="2">&nbsp;</th>
                     <?
					
					foreach($week_counter_header as $mon_key=>$week)
					{
					?>
					<th   align="center" colspan="<? echo count($week);?>">
					<? 
					echo $mon_key;
					?>
					</th>
					<?
					}
            ?>
                   
                </tr>
                  <tr>
					
                     <?
					//foreach($month_arr as $mon_key)
				
					foreach($week_counter_header as $mon_key=>$week_val)
					{
					?>
					<th width="60" style="font-size: 0.836em;"   align="center" colspan="<? echo count($week_val)?>">
					 		
                            <b id="summary_header_td_qty_<? echo $mon_key ;?>"  style="text-align:left;font-size: 0.797em;"> </b> &nbsp; 
                            <b id="summary_header_td_val_<? echo $mon_key ;?>"  style="text-align: right;font-size: 0.797em;">  </b> &nbsp;
                            <b id="summary_header_td_rate_<? echo $mon_key ;?>"  title="Avg FOB" style="text-align: right;font-size: 0.797em;"></b> &nbsp;
                             <b title="Avg SMV" id="summary_header_td_smv_<? echo $mon_key ;?>"  style="text-align:right;font-size: 0.797em;"></b>
					</th>
					<?
					}
            ?>
                   
                </tr>
                <tr>
					<th width="30">SL</th>
                    <th width="55">Season</th>
                    <th width="80">BH Mer.</th>
					<th width="70">FFL Mer.</th>
                    <th width="55">Job No</th>
					<th width="120">Style Ref</th>
                    <th width="60">Comp%</th>
                    <th width="120">Comp Name</th>
                    <th width="120">Const.</th>
                    <th width="50">GSM</th>
					<th width="120">Y/C</th>
                    <th width="70">Yarn Qty</th>
					<th width="60">Status</th>
					<th width="45">OPD</th>
					<th width="50">OPD Date</th>
					<th width="45">TOD</th>
                    <th width="70">Ord Qty</th>
					<th width="100">Order No</th>
                    <th width="40">Dept </th>
					<th width="40">Lead Time</th>
					<th width="40">Unit Price</th>
					<th width="90">Value</th>
					<th width="40">SMV/ Pcs</th>
                    <th width="90">Ord.SMV</th>
                    <th width="60">Delv Qty</th>
                    <th width="60">Delv.Val</th>
                     <?
					//foreach($week_for_header as $week_key)
					foreach($week_counter_header as $mon_key=>$week)
					{
						foreach($week as $week_key)
						{
							$start_week_date=$week_start_day[$week_key][week_start_day];
							$end_week_date=$week_end_day[$week_key][week_end_day];
							if($db_type==2)
							{
								$fisrtday=date("D",strtotime(change_date_format($start_week_date,"dd-mm-yyyy","-")));
								$lastday=date("D",strtotime(change_date_format($end_week_date,"dd-mm-yyyy","-")));
							}
							else
							{
								$fisrtday=date("D",strtotime(change_date_format($start_week_date,"dd-mm-yyyy")));
								$lastday=date("D",strtotime(change_date_format($end_week_date,"dd-mm-yyyy")));
							}
							$weekstart_date=explode("-",date("d-M",strtotime($start_week_date)));
							$weekstart_date=$weekstart_date[0].'/'.$weekstart_date[1];
							
						?>
						<th title="<? echo change_date_format($start_week_date,"dd-mm-yyyy","-").'('.$fisrtday.')'." To ".change_date_format($end_week_date,"dd-mm-yyyy","-").'('.$lastday.')'; ?>" width="45"  align="center">
						
						<? 
							echo 'W'.$week_key.'<br/>'.$weekstart_date;
						?>
						</th>
						<?
						}
					}
            ?>
                </tr>    
				</thead>
			</table>
			<div style="width:<? echo $td_width+20;?>px; max-height:245px; overflow-y:scroll;overflow-x:hidden;" id="scroll_body">
			<table class="rpt_table" width="<? echo $td_width;?>px" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body_count">
	<?
			$condition= new condition();
			 $condition->company_name("=$cbo_company_name");
			 if(str_replace("'","",$cbo_buyer_name)>0)
			 {
				  $condition->buyer_name("=$cbo_buyer_name");
			 }
			 if(str_replace("'","",$txt_job_no) !='')
			 {
				  $condition->job_no_prefix_num("in($txt_job_no)");
			 }
			
			 if(str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
			 {
			 $condition->pub_shipment_date(" between '$start_date' and '$end_date'");
			 }
				
			 if(str_replace("'","",$txt_order_no)!='')
			 {
				$condition->po_number("=$txt_order_no"); 
			 }
			 if(str_replace("'","",$txt_season)!='')
			 {
				//$condition->season("=$txt_season"); 
			 }
			 $condition->init();
        	 $yarn= new yarn($condition);
		  //echo $yarn->getQuery(); die;
			 $yarn_req_qty_arr=$yarn->getOrderWiseYarnQtyArray();
				$i=1;$total_order_qty=0;$total_order_value=0;$total_yarn_req_qty=0;$total_order_smv=0;$total_ex_fact_val=0;$total_ex_fact_qty=0;
				foreach($count_wise_arr as $count_key=>$count_data)
				{  
				   	foreach($count_data as $status_key=>$status_val)
					{
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";   
				$po_received_date=$status_val['po_received_date'];
				$conf_country_ship_date=rtrim($wo_color_data_arr2[$count_key][$status_key]['shipdate'],',');
				$conf_country_ship_date=explode(",",$conf_country_ship_date);
				$comp_data=explode("**",$comp_key);
				$copm_one_id=$comp_data[0];
				$copm_two_id=$comp_data[1];
				
				$percent_one=rtrim($status_val['percent'],',');
				$construction=rtrim($status_val['const'],',');
				$gsm_weight=rtrim($status_val['gsm'],',');
				$comp=rtrim($status_val['comp'],',');
				$gsm_weight=implode(",",array_unique(explode(",",$gsm_weight)));
				$construction=implode(",",array_unique(explode(",",$construction)));
				
				$percent_data=array_unique(explode(",",$percent_one));
				$comp_data=array_unique(explode(",",$comp));
				
				$percent_one_cond="";$percent_two_cond="";
				foreach($percent_data as $perid)
				{
					$percent_data=array_unique(explode("**",$perid));
					$percent_one=$percent_data[0];
					$percent_two=$percent_data[1];
					if($percent_one_cond=="") $percent_one_cond=$percent_one;else $percent_one_cond.=",".$percent_one;
					if($percent_two_cond=="") $percent_two_cond=$percent_two;else $percent_two_cond.=",".$percent_two;
				}
				if($percent_one_cond!='' && $percent_two_cond!='')
					{
						$percent_all=$percent_one_cond.'/'.$percent_two_cond;
					}
					else if($percent_one_cond!='' && $percent_two_cond=='')
					{
						$percent_all=$percent_one_cond;
					}
					else if($percent_one_cond=='' && $percent_two_cond!='')
					{
						$percent_all=$percent_two_cond;
					}
					else
					{
						$percent_all='';	
					}
				
				$comp_one_cond="";$comp_two_cond="";
				foreach($comp_data as $compid)
				{
						$comp_data=array_unique(explode("**",$compid));
						$comp_one=$comp_data[0];
						$comp_two=$comp_data[1];
						
					if($comp_one_cond=="") $comp_one_cond=$composition[$comp_one];else $comp_one_cond.=",".$composition[$comp_one];
					if($comp_two_cond=="") $comp_two_cond=$composition[$comp_two];else $comp_two_cond.=",".$composition[$comp_two];
				}
				
				if($comp_one_cond!='' && $comp_two_cond!='')
				{ 
					$copm_one_name=$comp_one_cond.'/'.$comp_two_cond;
				}
				else if($comp_one_cond!='' && $comp_two_cond=='')
				{ 
					$copm_one_name=$comp_one_cond;
				
				}
				else if($comp_one_cond=='' && $comp_two_cond!='')
				{ 
					$copm_one_name=$comp_two_cond;
				
				}
				else 
				{
					$copm_one_name='';
				}
				$count_id=rtrim($status_val['count_id'],',');
				//$count_id=rtrim($wo_fab_count_data_arr[$comp_key][$status_key]['count_id'],',');
				 $count_ids=array_unique(explode(",", $count_id));
				 $yarn_count_value="";
				foreach($count_ids as $val)
				{
						if($yarn_count_value=="") $yarn_count_value=$lib_yarn_count_arr[$val]; else $yarn_count_value.=", ".$lib_yarn_count_arr[$val];
				}
				$seasion_ids=rtrim($status_val['season'],',');
				$seasion_ids=array_unique(explode(",",$seasion_ids));
				$seasion_id=$seasion_ids[0];
				$job_no=rtrim($status_val['job_no'],',');
				$job_no=implode(",",array_unique(explode(",",$job_no)));
				$style=rtrim($status_val['style'],',');
				$po_no=rtrim($status_val['po_no'],',');
				$po_id=rtrim($status_val['po_id'],',');
			
				$dealing_marchant=rtrim($status_val['dealing_marchant'],',');
				$bh_mers=rtrim($status_val['bh_mer'],',');
				//$bh_mer=implode(",",array_unique(explode(",",$bh_mer)));
				$bh_mers=array_unique(explode(",",$bh_mers));
				$bh_mer=$bh_mers[0];
				$po_received_date=rtrim($status_val['po_received_date'],',');
				$c_ship_date=rtrim($status_val['c_ship_date'],',');
				$job_no=implode(",",array_unique(explode(",",$job_no)));
				$style=implode(",",array_unique(explode(",",$style)));
				$po_no=implode(",",array_unique(explode(",",$po_no)));
				$po_id=array_unique(explode(",",$po_id));
			
				$yarn_req=0;
				foreach($po_id as $pid)
				{
				$yarn_req+=$yarn_req_qty_arr[$pid];
				}
				//echo $yarn_req.'<br>';
				
				/*$seasion_cond=implode(",",array_unique(explode(",",$seasion_id)));
				$seasion_cond="";
				foreach($seasion_id as $sid)
				{
					if($seasion_cond=="") $seasion_cond=$lib_season_name_arr[$sid];else $seasion_cond.=",".$lib_season_name_arr[$sid];
				}*/
				$dealing_marchant=array_unique(explode(",",$dealing_marchant));
				$dealing_marchant_con="";
				foreach($dealing_marchant as $mid)
				{
					if($dealing_marchant_con=="") $dealing_marchant_con=$team_member_arr[$mid];else $dealing_marchant_con.=",".$team_member_arr[$mid];
				}
				$po_received_date=array_unique(explode(",",$po_received_date));
				$opb_week="";$opd_recv_date="";
				//$lead_time=0;
				foreach($po_received_date as $op_id)
				{
					if($opb_week=="") $opb_week=$no_of_week_for_header[$op_id];else $opb_week.=", ".$no_of_week_for_header[$op_id];
					if($opd_recv_date=="") $opd_recv_date=date("d-M",strtotime($op_id));else $opd_recv_date.=",".date("d-M",strtotime($op_id));
					if($opd_recv_date_full=="") $opd_recv_date_full=date("d-M-y",strtotime($op_id));else $opd_recv_date_full.=",".date("d-M-y",strtotime($op_id));
				//$lead_time = datediff( 'd', $op_id,$countryship_date);
				}
				$po_orgi_shipdate=rtrim($status_val['orgi_ship_date'],',');	
				$po_orgi_shipdate=array_unique(explode(",",$po_orgi_shipdate));
				$tod_weeks="";$tod_recv_date="";
				foreach($po_orgi_shipdate as $orgi_id)
				{
					//echo $orgi_id;
					if($tod_weeks=="") $tod_weeks=$no_of_week_for_header[$orgi_id];else $tod_weeks.=",".$no_of_week_for_header[$orgi_id];
				}
				$tod_weeks=implode(",",array_unique(explode(",",$tod_weeks)));
				$lead_time =$status_val['lead_day'];
				$opb_week=implode(",",array_unique(explode(", ",$opb_week)));
				$opd_recv_date=implode(",",array_unique(explode(", ",$opd_recv_date)));
				$prod_cond=rtrim($status_val['pcode'],',');
				$prod_cond=implode(",",array_unique(explode(",",$prod_cond)));
				//$po_received_date
				if($status_key==1) //Confirm $conf_country_ship_date
				{
					$c_ship_date_con=array_unique(explode(",",$c_ship_date));$qnty_array_dd=array();
					$c_week_qty=0;
					foreach($c_ship_date_con as $c_date)
					{
						if($db_type==0) $c_date_con=date("d-m-y",strtotime($c_date));
						else $c_date_con=date("d-M-y",strtotime($c_date));
						$c_week=$no_of_week_for_header_calc[$c_date_con];
						$c_week_qty=$week_wise_order_qty[$count_key][$status_key][$c_week]['po_quantity'];
								//echo $c_week.'=='.$c_week_qty.'hj';
						$qnty_array_dd[$c_week]=$c_week_qty;
					}
					//print_r($qnty_array_dd);
						$tot_order_qty=0;$tot_order_qty_arr=array();
						foreach($week_counter_header as $mon_key=>$week)
						{
							//$order_qty_arr=array();
							foreach($week as $week_key)
							{
								 $tot_order_qty=$qnty_array_dd[$week_key];
								  $order_qty_arr[$week_key]=$tot_order_qty;
								  $tot_order_qty_arr[$count_key]+=$tot_order_qty;
							}
						}
				}
				else 
				{
						$po_orgi_shipdate=rtrim($status_val['orgi_ship_date'],',');
						$proj_orgi_shipdate=array_unique(explode(",",$po_orgi_shipdate));
						$qnty_array_mm=array();$proj_po_qty=0;
						//$qnty_arr=distribute_projection($tod_week, $tot_proj_po_qty);
						$week_check=array();
						foreach($proj_orgi_shipdate as $tod_key)
						{
							$tod_week=$no_of_week_for_header[$tod_key];
							if($week_check[$tod_week]=='')
							{ //
								$proj_po_qty=$week_wise_order_qty2[$count_key][$status_key][$tod_week]['proj_po_qty'];
							
								$qnty_arr=distribute_projection($tod_week, $proj_po_qty);
								foreach($qnty_arr as $key=>$val)
								{
									$qnty_array_mm[$key]+=$val;
								}
								
								$week_check[$tod_week]=$proj_po_qty;
							}
						}
					
							$order_qty=0;$tot_order_qty_arr=array();
							foreach($week_counter_header as $mon_key=>$week)
							{
								foreach($week as $week_key)
								{
									$order_qty=$qnty_array_mm[$week_key];
									$order_qty_arr[$week_key]=$order_qty;
									$tot_order_qty_arr[$count_key]+=$order_qty;
								}
							}
				}
				$tot_po_qty=$tot_order_qty_arr[$count_key];
				
				$po_value=$status_val['po_val'];
				$unit_price=$po_value/$tot_po_qty;
				$job_no_row=count(array_unique(explode(",",$job_no)));
				$style_row=count(array_unique(explode(",",$style)));
				$prod_cond_row=count(array_unique(explode(",",$prod_cond)));
				if($job_no_row>1)
				{
					 $view_button="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$count_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button=$job_no;	
				}
				
				if($style_row>1)
				{
					 $view_button_style="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$count_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_style=$style;	
				}
				
				$tod_weeks_row=count(array_unique(explode(",",$tod_weeks)));
				if($tod_weeks_row>1)
				{
					 $view_button_tod="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$count_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_tod=$tod_weeks;	
				}
				
				$opd_week_row=count(array_unique(explode(",",$opd_week)));
				if($opd_week_row>1)
				{
					 $view_button_opd="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$count_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_opd=$opd_week;	
				}
				$opd_recv_date_row=count(array_unique(explode(",",$opd_recv_date)));
				if($opd_recv_date_row>1)
				{
					 $view_button_opd_date="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$count_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_opd_date=$opd_recv_date;	
				}
				$po_no_row=count(array_unique(explode(",",$po_no)));
				if($po_no_row>1)
				{
					 $view_button_po="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$count_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_po=$po_no;	
				}
				$dealing_marchant_con_row=count(array_unique(explode(",",$dealing_marchant_con)));
				$bh_mer_con_row=count(array_unique(explode(",",$bh_mer)));
				$seasion_cond_row=count(array_unique(explode(",",$seasion_cond)));
				$construction_cond_row=count(array_unique(explode(",",$construction)));
				$gsm_weight_cond_row=count(array_unique(explode(",",$gsm_weight)));
				$copm_one_name_cond_row=count(array_unique(explode(",",$copm_one_name)));
				if($dealing_marchant_con_row>1)
				{
					 $view_button_deal="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$count_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_deal=$dealing_marchant_con;
				}
				if($copm_one_name_cond_row>1)
				{
					 $view_button_comp="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$count_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_comp=$copm_one_name;
				}
				if($bh_mer_con_row>1)
				{
					 $view_button_bh="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$count_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					
					$view_button_bh=$bh_mer;
					//$view_button_season=$seasion_cond;	
				}
				if($seasion_cond_row>1)
				{
					 $view_button_season="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$count_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_season=$seasion_cond;	
				}
				if($construction_cond_row>1)
				{
					 $view_button_const="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$count_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_const=$construction;	
				}
				if($gsm_weight_cond_row>1)
				{
					 $view_button_gsm="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$comp_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_gsm=$$gsm_weight;	
				}
				if($prod_cond_row>1)
				{
					 $view_button_dept="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$count_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_dept=$prod_cond;
				}
				
				
				?>
                <tr style="font:'Arial Narrow; font-size:9px'" align="center" bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
					<td width="30"><? echo $i; ?></td>
                    <td width="55" style="word-wrap:break-word; word-break: break-all;"><? echo $seasion_id;?></td>
                     <td width="80" style="word-wrap:break-word; word-break: break-all;" ><? echo $bh_mer; ?></td> 
					<td width="70" style="word-wrap:break-word; word-break: break-all;" ><? echo $view_button_deal; ?></td> 
                    <td width="55" style="word-wrap:break-word; word-break: break-all;">
                   
					<? echo $view_button; ?></td>
					<td  width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $view_button_style; ?></td>
                   
                    
                    <td width="60" style="word-wrap:break-word; word-break: break-all;"><? echo $percent_all; ?></td>
                    <td  width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $view_button_comp; ?></td>
                    
                    
                    <td  width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $view_button_const;?></td>
                    <td width="50"  style="word-wrap:break-word; word-break: break-all;"><? echo $view_button_gsm; ?></td>
					<td  width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $lib_yarn_count_arr[$count_key]; ?></td>
                    <td  width="70" title="<? echo $yarn_req;?>" style="word-wrap:break-word; word-break: break-all; text-align:right">
					<?
							echo number_format($yarn_req);
				
					?>
                    </td>
					<td  width="60" style="word-wrap:break-word; word-break: break-all;">
					<?
					
					if($status_key==2)
					{
						echo "Booked";	
					}
					else
					{
						echo "Placed";
					}
					//echo $order_status[$row_data[csf('is_confirmed')]];
					?> 
                    </td>
					<td  width="45" style="word-wrap:break-word; word-break: break-all;">
					<?
					 echo $view_button_opd;
					
					?>
                    </td>
					<td  width="50" title="<? echo $opd_recv_date_full;?>" style="word-wrap:break-word; word-break: break-all;">
                    
					<?
					if($opd_recv_date!="" && $opd_recv_date !="0000-00-00" && $opd_recv_date!="0")
					{
						$opd_recv_date=$opd_recv_date;
					}
						echo $view_button_opd_date;
					?>
                     
                    </td>
					<td width="45" title="Orgi Ship Date" style="word-wrap:break-word; word-break: break-all; text-align: center">
					<? 
						echo $view_button_tod; ?>
                    </td>
                    <td  width="70" style="word-wrap:break-word; word-break: break-all; text-align:right">
					<? echo number_format($tot_po_qty);?>
                    </td>
					<td  width="100" style="word-wrap:break-word; word-break: break-all; text-align: center">
                    
					<? echo $view_button_po; ?>
                    </td>
                    <td  width="40" style="word-wrap:break-word; word-break: break-all; text-align:left" >
					<? echo $view_button_dept;?>
                    </td>
					<td  width="40" title="TOD-OPD" style="word-wrap:break-word; word-break: break-all; text-align: center">
					<? echo $lead_time; ?>
                    </td>
					<td  width="40" title="PO Value/Po Qty" style="word-wrap:break-word; word-break: break-all; text-align:right">
					<? echo number_format($unit_price,2); ?>
                    </td>
                   
					<td width="90" title="Po Qty*Unit Price" style="word-wrap:break-word; word-break: break-all;text-align:right">
                   <? echo number_format($tot_po_qty*$unit_price,0);
				  	 $set_smv=number_format($status_val['set_smv'],2);
				    ?>
                    </td>
					<td width="40" title="Set SMV/Set Ratio" style="word-wrap:break-word; word-break: break-all;">
					<? 
					$smv_rate=0;
					$smv_rate=$set_smv/$status_val['set_ratio'];
					echo number_format($smv_rate,2); ?>
                    </td>
					
                    <td  width="90" title="Set SMV Per Pcs*Order Qty" style="word-wrap:break-word; word-break: break-all;text-align:right">
					<? echo number_format(($set_smv/$status_val['set_ratio'])*$tot_po_qty); ?>
                    </td>
                     <td  width="60" style="word-wrap:break-word; word-break: break-all;text-align:right">
					<? echo number_format($exfactory_data_array[$count_key][$status_key][ex_qnty]); ?>
                    </td>
                    <td width="60" title="Ex-Fact Qty*Unit Price" style="word-wrap:break-word; word-break: break-all;text-align:right">
                    <? echo  number_format($exfactory_data_array[$count_key][$status_key][ex_qnty]*$unit_price,0);
					 ?>
                    </td>
                    <?
					
					$week_qty=0;
					$week_check=array();
                   	foreach($week_counter_header as $mon_key=>$week)
					{
						foreach($week as $week_key)
						{
						?>
						<td  style="word-wrap:break-word; word-break: break-all;" width="45" align="right">
						<? 
							 $week_qty=$order_qty_arr[$week_key];
							if($week_qty>0) echo number_format($week_qty,0); else echo ' ';
							$tot_week_qty[$mon_key][$week_key]+=$week_qty;
							$tot_week_amount[$mon_key][$week_key]+=$week_qty*$unit_price;
							$smv_avg=0;
							$smv_avg=$smv_rate*$week_qty;
							$month_smv_arr[$mon_key]+=$smv_avg;
						?>
						</td>
						<?
						}
					}
					?>
                    </tr>
                    <?
					$total_order_qty+=$tot_po_qty;
					$total_yarn_req_qty+=$yarn_req;	
					$total_ex_fact_qty+=$exfactory_data_array[$count_key][$status_key][ex_qnty];
					$total_ex_fact_val+=$exfactory_data_array[$count_key][$status_key][ex_qnty]*$unit_price;
					$total_order_value+=$tot_po_qty*$unit_price;
					$total_order_smv+=($set_smv/$status_val['set_ratio'])*$tot_po_qty;
					//$total_week_qty+=$week_qty;
					//=====================================================================================================================
				$i++;
					}
				}
				?>
				</table>
				</div>
                <table style="font:'Arial Narrow; font-size:9px'" class="tbl_bottom" width="<? echo $td_width;?>px" cellpadding="0"  id="report_table_footer" cellspacing="0" border="1" rules="all" >
					<tr>
					<td width="30">&nbsp;</td>
                    <td width="55">&nbsp;</td>
                    <td width="80">&nbsp;</td>
					<td width="70">&nbsp;</td>
                    <td width="55">&nbsp;</td>
					<td width="120">&nbsp;</td>
                    <td width="60">&nbsp;</td>
                    <td width="120">&nbsp;</td>
                    <td width="120">&nbsp;</td>
                    <td width="50">&nbsp;</td>
					<td width="120">&nbsp;</td>
                    <td width="70" style="word-wrap:break-word; word-break: break-all; font-size:smaller" id="td_yarn_req_march"><? echo $total_yarn_req_qty;?></td>
					<td width="60">&nbsp;</td>
					<td width="45">&nbsp;</td>
					<td width="50">&nbsp;</td>
					<td width="45">&nbsp;</td>
                    <td width="70" style="word-wrap:break-word; word-break: break-all; font-size:smaller" id="td_po_qty_march"><? echo $total_order_qty;?></td>
					<td width="100">&nbsp;</td>
                    <td width="40">&nbsp; </td>
					<td width="40">&nbsp;</td>
					<td width="40"><? echo number_format($total_order_value/$total_order_qty,2);?></td>
					<td width="90" id="td_po_val_march" style="word-wrap:break-word; word-break: break-all; font-size:smaller"><? echo number_format($total_order_value);?></td>
					<td width="40"><? echo number_format($total_order_smv/$total_order_qty,0);?></td>
                    <td width="90" style="word-wrap:break-word; word-break: break-all; font-size:smaller" id="total_order_smv"><? echo number_format($total_order_smv,0);?></td>
                    <td width="60" style="word-wrap:break-word; word-break: break-all; font-size:smaller" id="total_order_exfc_qty"><? echo number_format($total_ex_fact_qty,0);?></td>
                    <td width="60" style="word-wrap:break-word; word-break: break-all; font-size:smaller" id="total_order_exfc_val"><? echo number_format($total_ex_fact_val,0);?></td>
                     <?
					//foreach($week_for_header as $week_key)
					$total_week_qty=0; $total_week_amt=0; 
					foreach($week_counter_header as $mon_key=>$week)
					{
						foreach($week as $week_key)
						{
						?>
						<td width="45" style="word-wrap:break-word; word-break: break-all; font-size:smaller"  align="center">
						
						<? 
						$total_week_qty=$tot_week_qty[$mon_key][$week_key];
						$total_week_amt=$tot_week_amount[$mon_key][$week_key];
						echo number_format($total_week_qty,0);
						$tot_mon_qty[$mon_key]+=$total_week_qty;
						$tot_mon_amount[$mon_key]+=$total_week_amt;
						?>
						</td>
						<?
						}
					}
            ?>
					</tr>
                    <tr style="display:none">
					<td colspan="26">&nbsp;</td>
                     <?
					$total_week_qt=0; 
					foreach($week_counter_header as $mon_key=>$week)
					{
						?>
						<td width="" id="summary_footer_td_<? echo $mon_key?>" colspan="<? echo count($week);?>" title="<? echo $tot_week_amount[$mon_key][$week_key];?>" style="word-wrap:break-word; word-break: break-all;"   align="center">
						
						<? 
						$tot_smv=0;
						$tot_smv=$month_smv_arr[$mon_key]/$tot_mon_qty[$mon_key];
						$head_td_data=$tot_mon_qty[$mon_key].'$'.$tot_mon_amount[$mon_key].'$'.$tot_smv;
						echo $head_td_data;
						?>
						</td>
						<?
					}
            ?>
                    </tr>
				</table>
			</fieldset>
		</div>
         <style>
			@font-face {
   				 font-family:"Arial Narrow"; font-size:11px;
			}
			TD{font-family:"Arial Narrow";font-size:10px;}
			TH{font-family:"Arial Narrow";font-size:11px;}
			.rpt_table TD{font-family: "Arial Narrow";font-size:11px;}
			.rpt_table TH{font-family:"Arial Narrow";font-size:11px;}
		</style>
	<?
	}
//===========================================================================================================================================================
	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data****$filename****$tot_rows****$cbo_search_type";
	exit();	
	
}
else if($action=="report_generate_opd") //Opd Week Wise
{
 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company_name=str_replace("'","",$cbo_company_name);
	$cbo_search_type=str_replace("'","",$cbo_search_type);
	$cbo_bh_mer_name=str_replace("'","",$cbo_bh_mer_name);
	$txt_yarn_count=str_replace("'","",$txt_yarn_count);
	if($txt_yarn_count!='') $yarn_count_con="and yarn_count Like '%$txt_yarn_count%' ";else $yarn_count_con='';
	$buyer_id_cond="";
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{
				$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else
			{
				$buyer_id_cond="";
			}
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_job_no=trim($txt_job_no);
	if($txt_job_no !="" || $txt_job_no !=0)
	{
		//$year = substr(str_replace("'","",$cbo_year_selection), -2); 
		//$job_no=$company_library[$company_name]."-".$year."-".str_pad($txt_job_no, 5, 0, STR_PAD_LEFT);
		$jobcond="and a.job_no_prefix_num in(".$txt_job_no.")";
	}
	else
	{
		$jobcond="";	
	}
	
	
  	$date_cond='';
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		$start_date=(str_replace("'","",$txt_date_from));
		$end_date=(str_replace("'","",$txt_date_to));
		
		$date_cond="and b.pub_shipment_date between '$start_date' and '$end_date'";
	}
	//if (str_replace("'","",$txt_job_no)=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in (".str_replace("'","",$txt_job_no).") ";
	
	if(str_replace("'","",$txt_style_ref)!="") $style_ref_cond=" and a.style_ref_no like '%".str_replace("'","",$txt_style_ref)."%'"; else $style_ref_cond="";
	if(str_replace("'","",$txt_order_no)!="")
	{
		$ordercond=" and b.po_number like '%".str_replace("'","",$txt_order_no)."%'"; 
	}
	else
	{
		$ordercond="";
	}
	
	$team_cond="";
	if(str_replace("'","",$cbo_team_name)!=0) $team_cond=" and a.team_leader=".str_replace("'","",$cbo_team_name)."  ";
	if(str_replace("'","",$cbo_team_member)!=0) $team_cond.=" and a.dealing_marchant=".str_replace("'","",$cbo_team_member)."  ";
	$cbo_bh_mer_name=str_replace("0","",$cbo_bh_mer_name);
	if($cbo_bh_mer_name!=0 || $cbo_bh_mer_name!='') $bh_mer_name_con="and a.bh_merchant='$cbo_bh_mer_name' ";else $bh_mer_name_con='';
	 $cbo_year_selection=str_replace("'","",$cbo_year_selection);
	
	$start_ddd= strtotime(change_date_format($start_date,"dd-mm-yyyy","-"));
	$start_ddd=date("Y-m-d",$start_ddd);
	$start_end_date= strtotime(change_date_format($end_date,"dd-mm-yyyy","-"));
	$start_end_date=date("Y-m-d",$start_end_date);
	 //$dd= date('d-m-Y',strtotime($start_date));
	//$start_date_week= date($dd, time() - 1296000);//$start_date;
	//echo $last_day_this_month  = date($start_date_week, time() -1814400); //date('t-m-Y');
	
	$dateee = strtotime($start_ddd);
	$start_end_date = strtotime($start_end_date);
	
	$date_start = date("Y-m-d",strtotime("-28 day", $dateee));
	$date_end = date("Y-m-d",strtotime("+28 day", $start_end_date));
	//echo $date_end;
	//$end_date_week=  addday("$end_date", time() -1814400);//$start_date;//$start_date;
	if($db_type==0)
	{
		$date_start= change_date_format($date_start,"yyyy-mm-dd");
		$date_end= change_date_format($date_end,"yyyy-mm-dd");
	}
	else
	{
		$date_start= change_date_format($date_start,"dd-mm-yyyy","-",1);
		$date_end= change_date_format($date_end,"dd-mm-yyyy","-",1);
	}
	
	
	//echo "select week_date,week from week_of_year where week_date between '$date_start' and  '$date_end' order by week_date asc";die;
	// date("d-m-Y", time() - 1296000);
	$week_for_arr=array();$no_of_week_for_header=array();
	$sql_week_header=sql_select("select week_date,week from week_of_year where week_date between '$date_start' and  '$date_end' order by week_date asc");
	$week_check_head=array();
	foreach ($sql_week_header as $row_week_header)
	{
		if($week_check_head[$row_week_header[csf("week")]]=='')
		{
			$week_counter_header[date("Y-M",strtotime($row_week_header[csf("week_date")]))][$row_week_header[csf("week")]]=$row_week_header[csf("week")];
			$week_check_head[$row_week_header[csf("week")]]=$week_counter_header[date("Y-M",strtotime($row_week_header[csf("week_date")]))][$row_week_header[csf("week")]];
		}
		$tmp=add_date($row_week_header[csf("week_date")],-1);
		if($db_type==0) $tmp_cond=date("d-m-y",strtotime($tmp));
		else $tmp_cond=date("d-M-y",strtotime($tmp));
		$no_of_week_for_header_calc[$tmp_cond]=$row_week_header[csf("week")];
		$no_of_week_for_header[$row_week_header[csf("week_date")]]=$row_week_header[csf("week")];
		$test[$row_week_header[csf("week")]]=$row_week_header[csf("week")];
	}
	unset($sql_week_header);
	$week_start_day=array();
	$week_end_day=array();$week_day_arr=array();
	//week_day
	$sql_week_start_end_date=sql_select("select week,min(week_date) as week_start_day, Max(week_date) as week_end_day from week_of_year where week_date between '$date_start' and  '$date_end' group by week");
	foreach ($sql_week_start_end_date as $row_week)
	{
		$week_start_day[$row_week[csf("week")]][week_start_day]=$row_week[csf("week_start_day")];
		$week_end_day[$row_week[csf("week")]][week_end_day]=$row_week[csf("week_end_day")];
		//$week_day_arr[$row_week[csf("week")]][$row_week[csf("week_start_day")]][week_first_day]=$row_week[csf("min_week_day")];
		$week_day_arr[$row_week[csf("week_end_day")]]['week_last_day']=$row_week[csf("last_week_day")];
	}
	unset($sql_week_start_end_date);
	//print_r($week_day_arr);
	function distribute_projection( $base_week, $qnty)
	{
		//5. Distribution ratios are: Base week 52%, Immediate before 24%, Week before immediate week 20%, Immediate week after base week 2%, next 2 weeks as 1%
		global $test;
		 
		$tmpbase_week=$test[$base_week-3];
		$k=0;
		for($i=0; $i<6; $i++)
		{
			$tmpbase_week++;
			if( $test[$tmpbase_week]=='') { $tmpbase_week=1; $k=0; }
			
			
			if($i==0) $distr_arr[$tmpbase_week]=($qnty*20)/100;
			if($i==1) $distr_arr[$tmpbase_week]=($qnty*24)/100;
			if($i==2) $distr_arr[$tmpbase_week]=($qnty*52)/100;
			if($i==3) $distr_arr[$tmpbase_week]=($qnty*2)/100;
			if($i==4) $distr_arr[$tmpbase_week]=($qnty*1)/100;
			if($i==5) $distr_arr[$tmpbase_week]=($qnty*1)/100;
			$k++;
			
		}
		 
		return $distr_arr;
		/*5000
		b=52
		b-1=21
		b-2=15
		b+1=1
		b+2=1*/
	}
	//echo "<per>";
	 //print_r(distribute_projection("2016-Dec",51, 5000)); die;
	

	
	if($template==1)
	{
	$po_wise_buyer=array();
	$buyer_wise_data=array();
	$po_id_arr=array();
	$week_wise_order_qty_arr=array();	
	
	$count_data=sql_select("select yarn_count,id from  lib_yarn_count  where status_active=1 and is_deleted=0 and yarn_count is not null $yarn_count_con");
	$yarn_id='';
	foreach($count_data as $row)
	{
			if($yarn_id=='') $yarn_id=$row[csf('id')];else $yarn_id.=",".$row[csf('id')];
			
	}

		$yarn_ids=count(array_unique(explode(",",$yarn_id)));
		$yarn_numIds=chop($yarn_id,','); $yarnIds_cond="";
		if($yarn_id!='' || $yarn_id!=0)
		{
			if($db_type==2 && $yarn_ids>1000)
			{
				$yarnIds_cond=" and (";
				$yIdsArr=array_chunk(explode(",",$yarn_numIds),990);
				foreach($yIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$yarnIds_cond.=" d.count_id  in($ids) or ";
				}
				$yarnIds_cond=chop($yarnIds_cond,'or ');
				$yarnIds_cond.=")";
			}
			else
			{
				$yarnIds_cond=" and  d.count_id  in($yarn_id)";
			}
		}
		 $yarn_sql_data=("select b.po_received_date,b.is_confirmed,b.shipment_date as orgi_ship_date,b.id as po_id,d.count_id,d.copm_one_id,d.copm_two_id,d.percent_one,d.percent_two,sum(d.cons_qnty) as cons_qnty,e.construction,e.gsm_weight from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fab_yarn_cost_dtls d,wo_pre_cost_fabric_cost_dtls e where a.job_no=b.job_no_mst  and c.job_no_mst=a.job_no and b.id=c.po_break_down_id and d.job_no=a.job_no and 
	e.id=d.fabric_cost_dtls_id and  c.item_number_id= e.item_number_id and e.job_no=a.job_no and e.body_part_id in(1,20) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1   and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $team_cond $yarnIds_cond group by   b.id ,d.count_id,d.copm_one_id,d.copm_two_id,b.shipment_date,d.percent_one,d.percent_two,e.construction,e.gsm_weight,b.po_received_date,b.is_confirmed"); 
		$sql_result_y=sql_select($yarn_sql_data);
		$wo_fab_data_arr=array();
		$all_yarn_po_id="";
		if($all_yarn_po_id=="") $all_yarn_po_id=$row[csf("po_id")]; else $all_yarn_po_id.=",".$row[csf("po_id")];
		foreach($sql_result_y as $row)
		{
			if($row[csf('percent_two')]!=0) $row[csf('percent_two')]=$row[csf('percent_two')];else $row[csf('percent_two')]='';
			$percent_one=$row[csf('percent_one')].'**'.$row[csf('percent_two')];
			
			if($row[csf('copm_two_id')]!=0) $row[csf('copm_two_id')]=$row[csf('copm_two_id')];else $row[csf('copm_two_id')]='';
			$copm_one_id=$row[csf('copm_one_id')].'**'.$row[csf('copm_two_id')];
			
			$opd_week=$no_of_week_for_header[$row[csf('po_received_date')]];
			$wo_fab_data_arr[$opd_week][$row[csf('is_confirmed')]]['percent_one']=$percent_one;
			//$wo_fab_data_arr[$opd_week][$row[csf('is_confirmed')]]['percent_two']=$row[csf('percent_two')];
			$wo_fab_data_arr[$opd_week][$row[csf('is_confirmed')]]['copm_two_id']=$copm_one_id;
			//$wo_fab_data_arr[$opd_week][$row[csf('is_confirmed')]]['copm_one_id']=$row[csf('copm_one_id')];
			$wo_fab_data_arr[$opd_week][$row[csf('is_confirmed')]]['construction'].=$row[csf('construction')].',';
			$wo_fab_data_arr[$opd_week][$row[csf('is_confirmed')]]['gsm_weight'].=$row[csf('gsm_weight')].',';
			$wo_fab_count_data_arr[$opd_week][$row[csf('is_confirmed')]]['count_id'].=$row[csf('count_id')].",";
		}
		unset($sql_result_y);
		$yarn_po_ids=count(array_unique(explode(",",$all_yarn_po_id)));
		$yarnPo_numIds=chop($all_yarn_po_id,','); $yarnPoIds_cond="";
		if($txt_yarn_count!='')
		{
			if($all_yarn_po_id!='' || $all_yarn_po_id!=0)
			{
				if($db_type==2 && $yarn_po_ids>1000)
				{
					$yarnPoIds_cond=" and (";
					$yPoIdsArr=array_chunk(explode(",",$yarnPo_numIds),990);
					//print_r($gate_outIds);
					foreach($yPoIdsArr as $ids)
					{
						$ids=implode(",",$ids);
						$yarnPoIds_cond.=" b.id  in($ids) or ";
					}
					$yarnPoIds_cond=chop($yarnPoIds_cond,'or ');
					$yarnPoIds_cond.=")";
				}
				else
				{
					$yarnPoIds_cond=" and  b.id  in($all_yarn_po_id)";
				}
			}
		}
		$opd_wise_arr_qty=array();
		 $sql_data_c=("select b.id as po_id,a.job_no,b.po_received_date,b.shipment_date as orgi_ship_date,b.is_confirmed,(b.po_quantity*a.total_set_qnty) as po_qty_pcs
		from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $team_cond $bh_mer_name_con order by b.po_received_date,b.shipment_date ");
		$sql_result_c=sql_select($sql_data_c);
		foreach($sql_result_c as $row)
		{
			if($row[csf('is_confirmed')]==2)
			{
				$tod_week=$no_of_week_for_header[$row[csf('orgi_ship_date')]];
				$opd_week=$no_of_week_for_header[$row[csf('po_received_date')]];
				$opd_wise_arr_qty[$opd_week][$row[csf('is_confirmed')]][$tod_week]['po_qty']+=$row[csf('po_qty_pcs')];
			}
		}	

	if($db_type==0) $date_dif="DATEDIFF(b.shipment_date, b.po_received_date) as lead_time_days";
else  $date_dif="(b.shipment_date-b.po_received_date) as lead_time_days";
	  $sql_query=("select  $date_dif,a.job_no,a.job_no_prefix_num as prefix_job_no,a.set_smv,a.season_buyer_wise  as season,a.product_code,a.bh_merchant,a.dealing_marchant,a.style_ref_no,b.id,b.is_confirmed,b.po_number,b.shipment_date as orgi_ship_date,b.po_received_date,a.total_set_qnty as set_ratio,(b.po_quantity*a.total_set_qnty) as po_qty_pcs,b.po_total_price,a.avg_unit_price as unit_price ,c.country_ship_date,c.order_quantity,c.order_total
	  from wo_po_details_master a, wo_po_break_down b
	  LEFT JOIN wo_po_color_size_breakdown c on  c.po_break_down_id=b.id  and c.status_active=1
	 where a.job_no=b.job_no_mst  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1   and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $bh_mer_name_con $yarnPoIds_cond $team_cond order by b.po_received_date,a.job_no,b.is_confirmed ");
					 
	$sql_data=sql_select($sql_query);
	$tot_rows=count($sql_data);
	$all_po_id="";
	$marchant_wise_arr=array();
	foreach( $sql_data as $row)
	{
		$opd_week=$no_of_week_for_header[$row[csf('po_received_date')]];
		if($all_po_id=="") $all_po_id=$row[csf("id")]; else $all_po_id.=",".$row[csf("id")];
		if($row[csf('is_confirmed')]==1)
			{
				$order_val=$row[csf('order_total')];
			}
			else
			{
				$order_val=$row[csf('po_total_price')];
			}
		
		
			//if($row[csf('product_code')]!='' || $row[csf('product_code')]!=0)
			//{ 		
				$opd_wise_arr[$opd_week][$row[csf('is_confirmed')]]['pcode'].=$row[csf('product_code')].',';
			//}
		//$c_date_week=$no_of_week_for_header[$row[csf('country_ship_date')]];	
		if($db_type==0) $c_date_con=date("d-m-y",strtotime($row[csf('country_ship_date')]));
		else $c_date_con=date("d-M-y",strtotime($row[csf('country_ship_date')]));
		$c_date_week=$no_of_week_for_header_calc[$c_date_con];
		$opd_wise_arr[$opd_week][$row[csf('is_confirmed')]]['po_qty']+=$row[csf('order_quantity')];
		$opd_wise_arr[$opd_week][$row[csf('is_confirmed')]]['po_val']+=$order_val;
		$opd_wise_arr[$opd_week][$row[csf('is_confirmed')]]['set_smv']+=$row[csf('set_smv')];
		$opd_wise_arr[$opd_week][$row[csf('is_confirmed')]]['set_ratio']+=$row[csf('set_ratio')];
		$opd_wise_arr[$opd_week][$row[csf('is_confirmed')]]['lead_day']+=$row[csf('lead_time_days')];
		$opd_wise_arr[$opd_week][$row[csf('is_confirmed')]]['unit_price']+=$row[csf('unit_price')];
		$opd_wise_arr[$opd_week][$row[csf('is_confirmed')]]['job_no'].=$row[csf('prefix_job_no')].',';
		$opd_wise_arr[$opd_week][$row[csf('is_confirmed')]]['style'].=$row[csf('style_ref_no')].',';
		$opd_wise_arr[$opd_week][$row[csf('is_confirmed')]]['season'].=$row[csf('season')].',';
		$opd_wise_arr[$opd_week][$row[csf('is_confirmed')]]['dealing_marchant'].=$row[csf('dealing_marchant')].',';
		$opd_wise_arr[$opd_week][$row[csf('is_confirmed')]]['bh_mer'].=$row[csf('bh_merchant')].',';
		$opd_wise_arr[$opd_week][$row[csf('is_confirmed')]]['po_no'].=$row[csf('po_number')].',';
		$opd_wise_arr[$opd_week][$row[csf('is_confirmed')]]['po_id'].=$row[csf('id')].',';
		$opd_wise_arr[$opd_week][$row[csf('is_confirmed')]]['po_received_date'].=$row[csf('po_received_date')].',';
		$opd_wise_arr[$opd_week][$row[csf('is_confirmed')]]['orgi_ship_date'].=$row[csf('orgi_ship_date')].',';
		$opd_wise_arr[$opd_week][$row[csf('is_confirmed')]]['c_ship_date'].=$row[csf('country_ship_date')].',';
		if($row[csf('is_confirmed')]==1)
		{
		 $week_wise_order_qty[$opd_week][$row[csf('is_confirmed')]][$c_date_week]['po_quantity']+=$row[csf("order_quantity")];
		}
		
	}
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
					$poIds_cond.=" b.id  in($ids) or ";
				}
				$poIds_cond=chop($poIds_cond,'or ');
				$poIds_cond.=")";
			}
			else
			{
				$poIds_cond=" and  b.id  in($all_po_id)";
			}
		}
		$exfactory_data_array=array();
		$exfactory_sql=("select b.po_received_date,b.is_confirmed,
		sum(CASE WHEN f.entry_form!=85 THEN f.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		sum(CASE WHEN f.entry_form=85 THEN f.ex_factory_qnty ELSE 0 END) as return_qnty
		 from wo_po_details_master a, wo_po_break_down b,pro_ex_factory_mst f  where  a.job_no=b.job_no_mst and  f.po_break_down_id=b.id  and  a.company_name=$company_name  and f.status_active=1 and f.is_deleted=0 $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $team_cond group by  b.po_received_date, b.is_confirmed");
		$exfactory_data=sql_select($exfactory_sql);
		foreach($exfactory_data as $row)
		{
				$opd_week=$no_of_week_for_header[$row[csf('po_received_date')]];;
				$exfactory_data_array[$opd_week][$row[csf('is_confirmed')]][ex_qnty]+=$row[csf('ex_factory_qnty')]-$row[csf('return_qnty')];
		}
		unset($exfactory_data);
		
	
	
		ob_start();
		$rowcount=0;
		foreach($week_counter_header as $mon_key=>$week)
		{
			$rowcount+=count($week);
		}
		 $rowb=$rowcount*2;
		 $td_width=1780+($rowcount*45)+$rowb;
		 
		  $tot_month = datediff( 'm', $date_start,$date_end);
		for($i=0; $i<= $tot_month; $i++ )
		{
			$next_month=month_add($date_start,$i);
			$month_arr.=date("Y-M",strtotime($next_month)).',';
		}
	   $month_arr_id=rtrim($month_arr,',');
	   if($tot_rows>0)
	   {
	?>
		 <script>
			var mon_week ='<? echo $month_arr_id; ?>';
			var mon_week=mon_week.split(",");
		for (var k=0; k<mon_week.length; k++)
		{
			var mon_data=document.getElementById('summary_footer_td_'+mon_week[k]).innerHTML;
			var mon_arr=mon_data.split("$");
			var mon_qnty=number_format(mon_arr[0],0);
			var mon_amt=number_format(mon_arr[1],0);
			var avg_order_smv=number_format(mon_arr[2],2);
			var avg_rate=(number_format(mon_arr[1],0,'.','')/number_format(mon_arr[0],0,'.',''));
			//alert(mon_amt);
			document.getElementById('summary_header_td_qty_'+mon_week[k]).innerHTML=mon_qnty;
			document.getElementById('summary_header_td_val_'+mon_week[k]).innerHTML='$'+mon_amt;
			document.getElementById('summary_header_td_rate_'+mon_week[k]).innerHTML='FOB:'+number_format(avg_rate,2);
			document.getElementById('summary_header_td_smv_'+mon_week[k]).innerHTML='SMV:'+number_format(avg_order_smv,2);
		}
			var yarn_req_qty=document.getElementById('td_yarn_req_march').innerHTML;
			var yarnreq_qty=number_format(yarn_req_qty,0);
			document.getElementById('yarn_summary_tot').innerHTML=yarnreq_qty;
		</script>
        <?
	   }
		?>
        <div style="width:<? echo $td_width+20;?>px">
		<fieldset style="width:100%;">	
      
        
			<table width="<? echo $td_width+20;?>px">
				<tr class="form_caption">
					<td align="left" style="margin-left:10px" colspan="<? echo $rowcount+25; ?>">OPD Week Wise</td>
				</tr>
				<tr class="form_caption" align="center" style="display:none">
					<td colspan="<? echo $rowcount+26; ?>" align="center"><? //echo $company_library[$company_name]; ?></td>
				</tr>
			</table>
			<table style="font-size:9px" class="rpt_table" width="<? echo $td_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
                  <tr>
                    <th  width="" colspan="11" rowspan="2">&nbsp;</th>
                     <th  rowspan="2" id="yarn_summary_tot"></th>
                     <th  width="" colspan="14" rowspan="2">&nbsp;</th>
                     <?
					
					foreach($week_counter_header as $mon_key=>$week)
					{
					?>
					<th   align="center" colspan="<? echo count($week);?>">
					<? 
					//echo $week_key."<br/>".change_date_format($week_start_day[$week_key][week_start_day],"dd-mm-yyyy","-")." To ".change_date_format($week_end_day[$week_key][week_end_day],"dd-mm-yyyy","-");
					echo $mon_key;
					?>
					</th>
					<?
					}
            ?>
                </tr>
                  <tr>
                     <?
					foreach($week_counter_header as $mon_key=>$week_val)
					{
					?>
					<th width="60" style="font-size: 0.836em;"   align="center" colspan="<? echo count($week_val)?>">
                      <b id="summary_header_td_qty_<? echo $mon_key ;?>"  style="text-align:left;font-size: 0.795em;">  </b> &nbsp;
                       <b id="summary_header_td_val_<? echo $mon_key ;?>" style="text-align: right;font-size: 0.795em;"> </b> &nbsp;
                       <b id="summary_header_td_rate_<? echo $mon_key ;?>"  title="Avg FOB" style="text-align: right;font-size: 0.795em;"></b> &nbsp;
                      <b title="Avg SMV" id="summary_header_td_smv_<? echo $mon_key ;?>"  style="text-align:right;font-size: 0.795em;"></b>
					</th>
					<?
					}
            ?>
                </tr>
                <tr>
					<th width="30">SL</th>
                    <th width="55">Season</th>
                    <th width="80">BH Mer.</th>
					<th width="70">Merchant</th>
                    <th width="55">Job No</th>
					<th width="120">Style Ref</th>
                    <th width="60">Comp%</th>
                    <th width="120">Comp Name</th>
                    <th width="120">Const.</th>
                    <th width="50">GSM</th>
					<th width="120">Y/C</th>
                    <th width="70">Yarn Qty</th>
					<th width="60">Status</th>
					<th width="45">OPD</th>
					<th width="50">OPD Date</th>
					<th width="45">TOD</th>
                    <th width="70">Ord Qty</th>
					<th width="100">Order No</th>
                    <th width="40">Dept </th>
					<th width="40">Lead Time</th>
					<th width="40">Unit Price</th>
					<th width="90">Value</th>
					<th width="40">SMV/ Pcs</th>
                    <th width="90">Ord.SMV</th>
                    <th width="60">Delv Qty</th>
                    <th width="60">Delv.Val</th>
                     <?
					//foreach($week_for_header as $week_key)
					foreach($week_counter_header as $mon_key=>$week)
					{
						foreach($week as $week_key)
						{
							$start_week_date=$week_start_day[$week_key][week_start_day];
							$end_week_date=$week_end_day[$week_key][week_end_day];
							//$fist_day=$week_day_arr[$week_key][$start_week_date][week_first_day];
							//$last_day=$week_day_arr[$week_key][$end_week_date][week_last_day];
							//.'('.$fist_day.')'
							if($db_type==2)
							{
								$fisrtday=date("D",strtotime(change_date_format($start_week_date,"dd-mm-yyyy","-")));
								$lastday=date("D",strtotime(change_date_format($end_week_date,"dd-mm-yyyy","-")));
							}
							else
							{
								$fisrtday=date("D",strtotime(change_date_format($start_week_date,"dd-mm-yyyy")));
								$lastday=date("D",strtotime(change_date_format($end_week_date,"dd-mm-yyyy")));
							}
							$weekstart_date=explode("-",date("d-M",strtotime($start_week_date)));
							$weekstart_date=$weekstart_date[0].'/'.$weekstart_date[1];
							
						?>
						<th title="<? echo change_date_format($start_week_date,"dd-mm-yyyy","-").'('.$fisrtday.')'." To ".change_date_format($end_week_date,"dd-mm-yyyy","-").'('.$lastday.')'; ?>" width="45"  align="center">
						
						<? 
							echo 'W'.$week_key.'<br/>'.$weekstart_date;
						?>
						</th>
						<?
						}
					}
            ?>
                   
                </tr>    
				</thead>
			</table>
			<div style="width:<? echo $td_width+20;?>px; max-height:245px; overflow-y:scroll;overflow-x:hidden;" id="scroll_body">
			<table class="rpt_table" width="<? echo $td_width;?>px" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body_opd">
	<?
	
               
			$condition= new condition();
			 $condition->company_name("=$cbo_company_name");
			 if(str_replace("'","",$cbo_buyer_name)>0)
			 {
				  $condition->buyer_name("=$cbo_buyer_name");
			 }
			 if(str_replace("'","",$txt_job_no) !='')
			 {
				  $condition->job_no_prefix_num("in($txt_job_no)");
			 }
			
			 if(str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
			 {
			 $condition->pub_shipment_date(" between '$start_date' and '$end_date'");
			 }
				
			 if(str_replace("'","",$txt_order_no)!='')
			 {
				$condition->po_number("=$txt_order_no"); 
			 }
			 if(str_replace("'","",$txt_season)!='')
			 {
				//$condition->season("=$txt_season"); 
			 }
			 $condition->init();
			 
			 	
         $yarn= new yarn($condition);
		  //echo $yarn->getQuery(); die;
		 $yarn_req_qty_arr=$yarn->getOrderWiseYarnQtyArray();
			//print_r($yarn_req_qty_arr);
				$i=1;$total_order_qty=0;$total_order_value=0;$total_yarn_req_qty=0;$total_order_smv=0;$total_ex_fact_val=0;$total_ex_fact_qty=0;
				foreach($opd_wise_arr as $opd_key=>$opd_data)
				{  
				   
				   	foreach($opd_data as $status_key=>$status_val)
					{
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";  
						//  echo $opd_key.'=T'.'<br/>'; 
				$po_received_date=$status_val['po_received_date'];
				$countryship_date=$wo_color_data_arr[$opd_key][$status_key]['c_shipdate'];
				$conf_country_ship_date=rtrim($wo_color_data_arr2[$opd_key][$status_key]['shipdate'],',');
				$conf_country_ship_date=explode(",",$conf_country_ship_date);
				
				$percent_one=$wo_fab_data_arr[$opd_key][$row[csf('is_confirmed')]]['percent_one'];//rtrim($status_val['percent'],',');
				$comp=$wo_fab_data_arr[$opd_key][$row[csf('is_confirmed')]]['copm_two_id'];//rtrim($status_val['comp'],',');
				$construction=implode(",",array_unique(explode(",",$construction)));
				$percent_data=array_unique(explode(",",$percent_one));
				$comp_data=array_unique(explode(",",$comp));
				
				$percent_one_cond="";$percent_two_cond="";
				foreach($percent_data as $perid)
				{
						$percent_data=array_unique(explode("**",$perid));
						$percent_one=$percent_data[0];
						$percent_two=$percent_data[1];
					if($percent_one_cond=="") $percent_one_cond=$percent_one;else $percent_one_cond.=",".$percent_one;
					if($percent_two_cond=="") $percent_two_cond=$percent_two;else $percent_two_cond.=",".$percent_two;
				}
				if($percent_one_cond!='' && $percent_two_cond!='')
					{
						//if($percent_two!=0)
						$percent_all=$percent_one_cond.'/'.$percent_two_cond;
					}
					else if($percent_one_cond!='' && $percent_two_cond=='')
					{
						//if($percent_two!=0)
						$percent_all=$percent_one_cond;
					}
					else if($percent_one_cond=='' && $percent_two_cond!='')
					{
						//if($percent_two!=0)
						$percent_all=$percent_two_cond;
					}
					else
					{
						$percent_all='';	
					}
				
				$comp_one_cond="";$comp_two_cond="";
				foreach($comp_data as $compid)
				{
						$comp_data=array_unique(explode("**",$compid));
						$comp_one=$comp_data[0];
						$comp_two=$comp_data[1];
						
					if($comp_one_cond=="") $comp_one_cond=$composition[$comp_one];else $comp_one_cond.=",".$composition[$comp_one];
					if($comp_two_cond=="") $comp_two_cond=$composition[$comp_two];else $comp_two_cond.=",".$composition[$comp_two];
				}
				
				if($comp_one_cond!='' && $comp_two_cond!='')
				{ 
					$copm_one_name=$comp_one_cond.'/'.$comp_two_cond;
				}
				else if($comp_one_cond!='' && $comp_two_cond=='')
				{ 
					$copm_one_name=$comp_one_cond;
				
				}
				else if($comp_one_cond=='' && $comp_two_cond!='')
				{ 
					$copm_one_name=$comp_two_cond;
				
				}
				else 
				{
					$copm_one_name='';
				}
				
				//$construction=rtrim($wo_fab_data_arr[$opd_key][$status_key]['construction']);
				//$gsm_weight=$wo_fab_data_arr[$opd_key][$status_key]['gsm_weight'];
				
				$construction=rtrim($wo_fab_data_arr[$opd_key][$status_key]['construction'],',');
				$construction=implode(",",array_unique(explode(",", $construction)));
				$gsm_weight=rtrim($wo_fab_data_arr[$opd_key][$status_key]['gsm_weight'],',');
				$gsm_weight=implode(",",array_unique(explode(",", $gsm_weight)));
				
				$count_id=$wo_fab_count_data_arr[$opd_key][$status_key]['count_id'];
				$count_id=rtrim($count_id,',');
				 $count_ids=array_unique(explode(",", $count_id));
				 $yarn_count_value="";
				foreach($count_ids as $val)
				{
					//if($val>0)
					//{
						if($yarn_count_value=="") $yarn_count_value=$lib_yarn_count_arr[$val]; else $yarn_count_value.=", ".$lib_yarn_count_arr[$val];
					//}
				}
				
				$seasion_ids=rtrim($status_val['season'],',');
				$seasion_ids=array_unique(explode(",",$seasion_ids));
				$seasion_id=$seasion_ids[0];
				$job_no=rtrim($status_val['job_no'],',');
				$job_no=implode(",",array_unique(explode(",",$job_no)));
				$style=rtrim($status_val['style'],',');
				$po_no=rtrim($status_val['po_no'],',');
				$po_id=rtrim($status_val['po_id'],',');
			
				$dealing_marchant=rtrim($status_val['dealing_marchant'],',');
				$bh_mers=rtrim($status_val['bh_mer'],',');
				//$bh_mer=implode(",",array_unique(explode(",",$bh_mer)));
				$bh_mers=array_unique(explode(",",$bh_mers));
				$bh_mer=$bh_mers[0];
				$po_received_date=rtrim($status_val['po_received_date'],',');
				$c_ship_date=rtrim($status_val['c_ship_date'],',');
				$job_no=implode(",",array_unique(explode(",",$job_no)));
				$style=implode(",",array_unique(explode(",",$style)));
				$po_no=implode(",",array_unique(explode(",",$po_no)));
				$po_id=array_unique(explode(",",$po_id));
			
				$yarn_req=0;
				foreach($po_id as $pid)
				{
				$yarn_req+=$yarn_req_qty_arr[$pid];
				}
				//echo $yarn_req.'<br>';
				
				$seasion_cond=implode(",",array_unique(explode(",",$seasion_id)));
				/*$seasion_cond="";
				foreach($seasion_id as $sid)
				{
					if($seasion_cond=="") $seasion_cond=$lib_season_name_arr[$sid];else $seasion_cond.=",".$lib_season_name_arr[$sid];
				}*/
				$dealing_marchant=array_unique(explode(",",$dealing_marchant));
				$dealing_marchant_con="";
				foreach($dealing_marchant as $mid)
				{
					if($dealing_marchant_con=="") $dealing_marchant_con=$team_member_arr[$mid];else $dealing_marchant_con.=",".$team_member_arr[$mid];
				}
				$po_received_date=array_unique(explode(",",$po_received_date));
				$pod_week="";$opd_recv_date="";$opd_recv_date_full="";
				foreach($po_received_date as $op_id)
				{
					//if($pod_week=="") $pod_week=$no_of_week_for_header[$op_id];else $pod_week.=", ".$no_of_week_for_header[$op_id];
					//if($opd_recv_date=="") $opd_recv_date=change_date_format($op_id);else $opd_recv_date.=", ".change_date_format($op_id);
					if($opd_recv_date=="") $opd_recv_date=date("d-M",strtotime($op_id));else $opd_recv_date.=",".date("d-M",strtotime($op_id));
					if($opd_recv_date_full=="") $opd_recv_date_full=date("d-M-y",strtotime($op_id));else $opd_recv_date_full.=",".date("d-M-y",strtotime($op_id));
				}
				
				$orgi_ship_date=rtrim($status_val['orgi_ship_date'],',');
				$orgi_ship_date=array_unique(explode(",",$orgi_ship_date));
				$tod_weeks="";$tod_recv_date="";
				foreach($orgi_ship_date as $orgi_id)
				{
					//echo $orgi_id;
					if($tod_weeks=="") $tod_weeks=$no_of_week_for_header[$orgi_id];else $tod_weeks.=",".$no_of_week_for_header[$orgi_id];
					//if($tod_recv_date=="") $tod_recv_date=change_date_format($ogi_id);else $tod_recv_date.=", ".change_date_format($ogi_id);
				}
				$tod_weeks=implode(",",array_unique(explode(",",$tod_weeks)));
				$lead_time=$status_val['lead_day'];
				
				//$pod_week=implode(",",array_unique(explode(", ",$pod_week)));
				$opd_recv_date=implode(",",array_unique(explode(", ",$opd_recv_date)));
				$prod_cond=substr($status_val['pcode'],0,-1);
				$prod_cond=implode(",",array_unique(explode(",",$prod_cond)));
				if($status_key==1) //Confirm $conf_country_ship_date
				{
					$c_ship_date_con=array_unique(explode(",",$c_ship_date));$qnty_array_dd=array();
					$c_week_qty=0;
					foreach($c_ship_date_con as $c_date)
					{
								if($db_type==0) $c_date_con=date("d-m-y",strtotime($c_date));
								else $c_date_con=date("d-M-y",strtotime($c_date));
								$c_week=$no_of_week_for_header_calc[$c_date_con];
								$c_week_qty=$week_wise_order_qty[$opd_key][$status_key][$c_week]['po_quantity'];
										//echo $c_week.'=='.$c_date.'hj';
								$qnty_array_dd[$c_week]=$c_week_qty;
					}
					//print_r($qnty_array_dd);
						$tot_order_qty=0;$tot_order_qty_arr=array();
						foreach($week_counter_header as $mon_key=>$week)
						{
							foreach($week as $week_key)
							{
								 $tot_order_qty=$qnty_array_dd[$week_key];
								 $order_qty_arr[$week_key]=$tot_order_qty;
								  $tot_order_qty_arr[$opd_key]+=$tot_order_qty;
							}
						}
						
				}
				else 
				{
						$po_orgi_shipdate=rtrim($status_val['orgi_ship_date'],',');
						$proj_orgi_shipdate=array_unique(explode(",",$po_orgi_shipdate));
						$qnty_array_mm=array();$proj_po_qty=0;
						$week_check=array();
						foreach($proj_orgi_shipdate as $tod_key)
						{
							$tod_week=$no_of_week_for_header[$tod_key];
							if($week_check[$tod_week]=='')
							{
								 $proj_po_qty=$opd_wise_arr_qty[$opd_key][$status_key][$tod_week]['po_qty'];
							
								$qnty_arr=distribute_projection($tod_week, $proj_po_qty);
								foreach($qnty_arr as $key=>$val)
								{
									$qnty_array_mm[$key]+=$val;
								}
								
								$week_check[$tod_week]=$proj_po_qty;
							}
						}
					
							$order_qty=0;$tot_order_qty_arr=array();
							foreach($week_counter_header as $mon_key=>$week)
							{
								foreach($week as $week_key)
								{
									$order_qty=$qnty_array_mm[$week_key];
									$order_qty_arr[$week_key]=$order_qty;
									$tot_order_qty_arr[$opd_key]+=$order_qty;
								}
							}
				}
				$tot_po_qty=$tot_order_qty_arr[$opd_key];
				$po_value=$status_val['po_val'];
				$unit_price=$po_value/$tot_po_qty;
				$po_received_datess=rtrim($status_val['po_received_date'],',');
				$job_no_row=count(array_unique(explode(",",$job_no)));
				if($job_no_row>1)
				{
					 $view_button="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$po_received_datess."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button=$job_no;	
				}
				
				
				$tod_weeks_row=count(array_unique(explode(",",$tod_weeks)));
				if($tod_weeks_row>1)
				{
					 $view_button_tod="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$po_received_datess."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_tod=$tod_weeks;	
				}
				
				
				$opd_recv_date_row=count(array_unique(explode(",",$opd_recv_date)));
				if($opd_recv_date_row>1)
				{
					 $view_button_opd_date="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$po_received_datess."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_opd_date=$opd_recv_date;	
				}
				$po_no_row=count(array_unique(explode(",",$po_no)));
				if($po_no_row>1)
				{
					 $view_button_po="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$po_received_datess."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_po=$po_no;	
				}
				$dealing_marchant_con_row=count(array_unique(explode(",",$dealing_marchant_con)));
				$bh_mer_con_row=count(array_unique(explode(",",$bh_mer)));
				$seasion_cond_row=count(array_unique(explode(",",$seasion_cond)));
				$construction_cond_row=count(array_unique(explode(",",$construction)));
				$gsm_weight_cond_row=count(array_unique(explode(",",$gsm_weight)));
				$yarn_count_value_cond_row=count(array_unique(explode(",",$yarn_count_value)));
				$style_cond_row=count(array_unique(explode(",",$style)));
				$copm_cond_row=count(array_unique(explode(",",$copm_one_name)));
				
				if($dealing_marchant_con_row>1)
				{
					 $view_button_deal="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$po_received_datess."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_deal=$dealing_marchant_con;
					$view_button_bh=$bh_mer;
					//$view_button_season=$seasion_cond;	
				}
				if($bh_mer_con_row>1)
				{
					 $view_button_bh="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$po_received_datess."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					
					$view_button_bh=$bh_mer;
					//$view_button_season=$seasion_cond;	
				}
				if($seasion_cond_row>1)
				{
					 $view_button_season="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$po_received_datess."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_season=$seasion_cond;	
				}
				if($construction_cond_row>1)
				{
					 $view_button_const="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$po_received_datess."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_const=$construction;	
				}
				if($gsm_weight_cond_row>1)
				{
					 $view_button_gsm="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$po_received_datess."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_gsm=$$gsm_weight;	
				}
				if($yarn_count_value_cond_row>1)
				{
					 $view_button_count="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$po_received_datess."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_count=$yarn_count_value;	
				}
				if($style_cond_row>1)
				{
					 $view_button_style="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$po_received_datess."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_style=$style;	
				}
				if($copm_cond_row>1)
				{
					 $view_button_comp="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$po_received_datess."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_comp=$copm_one_name;	
				}
				 $prod_cond_row=count(array_unique(explode(",",$prod_cond)));
				if($prod_cond_row>1)
				{
					 $view_button_dept="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$po_received_datess."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_dept=$prod_cond;	
				}
				
				?>
                <tr style="font:'Arial Narrow'; font-size:9px" align="center" bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
					<td width="30"><? echo $i; ?></td>
                    <td width="55" style="word-wrap:break-word; word-break: break-all;"><? echo $seasion_id;?></td>
                    <td width="80" style="word-wrap:break-word; word-break: break-all;" ><? echo $bh_mer; ?></td> 
					<td width="70" style="word-wrap:break-word; word-break: break-all;" ><? echo $view_button_deal; ?></td> 
                    <td width="55" style="word-wrap:break-word; word-break: break-all;">
							<? echo $view_button; ?></td>
					<td  width="120" style="word-wrap:break-word; word-break: break-all;"> <? echo  $view_button_style; ?></td>
                   
                    
                    <td width="60" style="word-wrap:break-word; word-break: break-all;"><? echo $percent_all; ?></td>
                    <td  width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $view_button_comp; ?></td>
                    
                    
                    <td  width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $view_button_const;?></td>
                    <td width="50"  style="word-wrap:break-word; word-break: break-all;"><? echo $view_button_gsm; ?></td>
					<td  width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $view_button_count ?></td>
                    <td  width="70" title="<? echo $yarn_req;?>" style="word-wrap:break-word; word-break: break-all; text-align:right">
					<?
							echo number_format($yarn_req);
				
					?>
                    </td>
					<td  width="60" style="word-wrap:break-word; word-break: break-all;">
					<?
					
					if($status_key==2)
					{
						echo "Booked";	
					}
					else
					{
						echo "Placed";
					}
					//echo $order_status[$row_data[csf('is_confirmed')]];
					?> 
                    </td>
					<td  width="45" style="word-wrap:break-word; word-break: break-all;">
					<?
					
					echo  'w'.$opd_key;
					?>
                    </td>
					<td  width="50" title="<? echo $opd_recv_date_full;?>" style="word-wrap:break-word; word-break: break-all;">
                    
					<?
					if($opd_recv_date!="" && $opd_recv_date !="0000-00-00" && $opd_recv_date!="0")
					{
						$opd_recv_date=$opd_recv_date;
					}
					echo $view_button_opd_date;
					?>
                      
                    </td>
					<td width="45" title="Orgi Ship Date" style="word-wrap:break-word; word-break: break-all; text-align: center">
					<? 
					echo $view_button_tod; ?>
                    </td>
                    <td  width="70" style="word-wrap:break-word; word-break: break-all; text-align:right">
					<? echo number_format($tot_po_qty);?>
                    </td>
					<td  width="100" style="word-wrap:break-word; word-break: break-all; text-align: center">
						<? echo $view_button_po; ?>
                    </td>
                    <td  width="40" style="word-wrap:break-word; word-break: break-all; text-align:left" >
					<? echo $view_button_dept;?>
                    </td>
					<td  width="40" title="TOD-OPD" style="word-wrap:break-word; word-break: break-all; text-align: center">
					<? //echo $lead_time; ?>
                    </td>
					<td  width="40" title="PO Value/Po Qty" style="word-wrap:break-word; word-break: break-all; text-align:right">
					<? echo number_format($unit_price,2); ?>
                    </td>
                   
					<td width="90" title="Po Qty*Unit Price" style="word-wrap:break-word; word-break: break-all;text-align:right">
                   <? echo number_format($tot_po_qty*$unit_price,0);
				  	 $set_smv=number_format($status_val['set_smv'],2);
				    ?>
                    </td>
					<td width="40" title="Set SMV/Set Ratio" style="word-wrap:break-word; word-break: break-all;">
					<? 
					$smv_rate=0;
					$smv_rate=$set_smv/$status_val['set_ratio'];
					echo number_format($smv_rate,2); ?>
                    </td>
					
                    <td  width="90" title="Set SMV Per Pcs*Order Qty" style="word-wrap:break-word; word-break: break-all;text-align:right">
					<? echo number_format(($set_smv/$status_val['set_ratio'])*$tot_po_qty); ?>
                    </td>
                     <td  width="60" style="word-wrap:break-word; word-break: break-all;text-align:right">
					<? echo number_format($exfactory_data_array[$opd_key][$status_key][ex_qnty]); ?>
                    </td>
                    <td width="60" title="Ex-Fact Qty*Unit Price" style="word-wrap:break-word; word-break: break-all;text-align:right">
                    <? echo  number_format($exfactory_data_array[$opd_key][$status_key][ex_qnty]*$unit_price,0);
					//echo $exfactory_data_array[$marchant_key][$status_key][ex_qnty].'='.$status_val['unit_price'].'<br/>';
					 ?>
                    </td>
                    <?
					
					// print_r($qnty_array); die;
					$week_qty=0;
					$week_check=array();
                   	foreach($week_counter_header as $mon_key=>$week)
					{
						foreach($week as $week_key)
						{
						?>
						<td  width="45" style="word-wrap:break-word; word-break: break-all;" align="right">
						
						<? 
						   $week_qty=$order_qty_arr[$week_key];
							if($week_qty>0) echo number_format($week_qty,0);else echo '';
							$tot_week_qty[$mon_key][$week_key]+=$week_qty;
							$tot_week_amount[$mon_key][$week_key]+=$week_qty*$unit_price;
							$smv_avg=0;
							$smv_avg=$smv_rate*$week_qty;
							$month_smv_arr[$mon_key]+=$smv_avg;
							
						?>
						</td>
						<?
						}
					}
					?>
                    </tr>
                    <?
					$total_order_qty+=$tot_po_qty;
					$total_yarn_req_qty+=$yarn_req;	
					$total_ex_fact_qty+=$exfactory_data_array[$opd_key][$status_key][ex_qnty];
					$total_ex_fact_val+=$exfactory_data_array[$opd_key][$status_key][ex_qnty]*$unit_price;
					$total_order_value+=$tot_po_qty*$unit_price;
					$total_order_smv+=($set_smv/$status_val['set_ratio'])*$tot_po_qty;
					//$total_week_qty+=$week_qty;
					//=====================================================================================================================
				$i++;
					}
					
				}
				?>
                 
				</table>
				</div>
                <table class="tbl_bottom" style="font:'Arial Narrow'; font-size:9px"  width="<? echo $td_width;?>px" cellpadding="0" cellspacing="0" border="1" rules="all" >
					<tr>
					<td width="30">&nbsp;</td>
                    <td width="55">&nbsp;</td>
                    <td width="80">&nbsp;</td>
					<td width="70">&nbsp;</td>
                    <td width="55">&nbsp;</td>
					<td width="120">&nbsp;</td>
                    <td width="60">&nbsp;</td>
                    <td width="120">&nbsp;</td>
                    <td width="120">&nbsp;</td>
                    <td width="50">&nbsp;</td>
					<td width="120">&nbsp;</td>
                    <td width="70" style="word-wrap:break-word; word-break: break-all; font-size:smaller" id="td_yarn_req_march"><? echo $total_yarn_req_qty;?></td>
					<td width="60">&nbsp;</td>
					<td width="45">&nbsp;</td>
					<td width="50">&nbsp;</td>
					<td width="45">&nbsp;</td>
                    <td width="70" style="word-wrap:break-word; word-break: break-all; font-size:smaller" id="td_po_qty_march"><? echo $total_order_qty;?></td>
					<td width="100">&nbsp;</td>
                    <td width="40">&nbsp; </td>
					<td width="40">&nbsp;</td>
					<td width="40"><? echo number_format($total_order_value/$total_order_qty,2);?></td>
					<td width="90" id="td_po_val_march" style="word-wrap:break-word; word-break: break-all; font-size:smaller"><? echo number_format($total_order_value);?></td>
					<td width="40">&nbsp;</th>
                    <td width="90" style="word-wrap:break-word; word-break: break-all; font-size:smaller" id="total_order_smv"><? echo number_format($total_order_smv,0);?></td>
                    <td width="60" style="word-wrap:break-word; word-break: break-all; font-size:smaller" id="total_order_exfc_qty"><? echo number_format($total_ex_fact_qty,0);?></td>
                    <td width="60" style="word-wrap:break-word; word-break: break-all; font-size:smaller" id="total_order_exfc_val"><? echo number_format($total_ex_fact_val,0);?></td>
                     <?
					//foreach($week_for_header as $week_key)
					$total_week_qty=0; $total_week_amt=0; 
					foreach($week_counter_header as $mon_key=>$week)
					{
						foreach($week as $week_key)
						{
						?>
						<td width="45" style="word-wrap:break-word; word-break: break-all; font-size:smaller"  align="center">
						
						<? 
						$total_week_qty=$tot_week_qty[$mon_key][$week_key];
						$total_week_amt=$tot_week_amount[$mon_key][$week_key];
						echo number_format($total_week_qty,0);
						$tot_mon_qty[$mon_key]+=$total_week_qty;
						$tot_mon_amount[$mon_key]+=$total_week_amt;
						?>
						</td>
						<?
						
						}
					}
            ?>
					</tr>
                     <tr style="display:none">
					<td colspan="26">&nbsp;</td>
                     <?
				
					$total_week_qt=0; 
					foreach($week_counter_header as $mon_key=>$week)
					{
						?>
						<td width="" id="summary_footer_td_<? echo $mon_key?>" colspan="<? echo count($week);?>" title="<? echo $tot_week_amount[$mon_key][$week_key];?>" style="word-wrap:break-word; word-break: break-all;"   align="center">
						
						<? 
						$tot_smv=0;
						$tot_smv=$month_smv_arr[$mon_key]/$tot_mon_qty[$mon_key];
						$head_td_data=$tot_mon_qty[$mon_key].'$'.$tot_mon_amount[$mon_key].'$'.$tot_smv;
						echo $head_td_data;
						?>
						</td>
						<?
					}
            ?>
                    
                    </tr>
				</table>
				</table>
			</fieldset>
		</div>
	<?
	}
//===========================================================================================================================================================
	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data****$filename****$tot_rows****$cbo_search_type";
	exit();	
	
}
else if($action=="report_generate_pcode") //Prod Code Wise
{
 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company_name=str_replace("'","",$cbo_company_name);
	$cbo_search_type=str_replace("'","",$cbo_search_type);
	$cbo_bh_mer_name=str_replace("'","",$cbo_bh_mer_name);
	$txt_yarn_count=str_replace("'","",$txt_yarn_count);
	if($txt_yarn_count!='') $yarn_count_con="and yarn_count Like '%$txt_yarn_count%' ";else $yarn_count_con='';
	$buyer_id_cond="";
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{
				$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else
			{
				$buyer_id_cond="";
			}
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_job_no=trim($txt_job_no);
	if($txt_job_no !="" || $txt_job_no !=0)
	{
		//$year = substr(str_replace("'","",$cbo_year_selection), -2); 
		//$job_no=$company_library[$company_name]."-".$year."-".str_pad($txt_job_no, 5, 0, STR_PAD_LEFT);
		$jobcond="and a.job_no_prefix_num in(".$txt_job_no.")";
	}
	else
	{
		$jobcond="";	
	}
	
	
  	$date_cond='';
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		$start_date=(str_replace("'","",$txt_date_from));
		$end_date=(str_replace("'","",$txt_date_to));
		
		$date_cond="and b.pub_shipment_date between '$start_date' and '$end_date'";
	}
	//if (str_replace("'","",$txt_job_no)=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in (".str_replace("'","",$txt_job_no).") ";
	
	if(str_replace("'","",$txt_style_ref)!="") $style_ref_cond=" and a.style_ref_no like '%".str_replace("'","",$txt_style_ref)."%'"; else $style_ref_cond="";
	if(str_replace("'","",$txt_order_no)!="")
	{
		$ordercond=" and b.po_number like '%".str_replace("'","",$txt_order_no)."%'"; 
	}
	else
	{
		$ordercond="";
	}
	
	$team_cond="";
	if(str_replace("'","",$cbo_team_name)!=0) $team_cond=" and a.team_leader=".str_replace("'","",$cbo_team_name)."  ";
	if(str_replace("'","",$cbo_team_member)!=0) $team_cond.=" and a.dealing_marchant=".str_replace("'","",$cbo_team_member)."  ";
	$cbo_bh_mer_name=str_replace("0","",$cbo_bh_mer_name);
	if($cbo_bh_mer_name!=0 || $cbo_bh_mer_name!='') $bh_mer_name_con="and a.bh_merchant='$cbo_bh_mer_name' ";else $bh_mer_name_con='';
	 $cbo_year_selection=str_replace("'","",$cbo_year_selection);
	
	
	$start_ddd= strtotime(change_date_format($start_date,"dd-mm-yyyy","-"));
	$start_ddd=date("Y-m-d",$start_ddd);
	$start_end_date= strtotime(change_date_format($end_date,"dd-mm-yyyy","-"));
	$start_end_date=date("Y-m-d",$start_end_date);
	 //$dd= date('d-m-Y',strtotime($start_date));
	//$start_date_week= date($dd, time() - 1296000);//$start_date;
	//echo $last_day_this_month  = date($start_date_week, time() -1814400); //date('t-m-Y');
	
	$dateee = strtotime($start_ddd);
	$start_end_date = strtotime($start_end_date);
	
	$date_start = date("Y-m-d",strtotime("-28 day", $dateee));
	$date_end = date("Y-m-d",strtotime("+28 day", $start_end_date));
	//echo $date_end;
	//$end_date_week=  addday("$end_date", time() -1814400);//$start_date;//$start_date;
	if($db_type==0)
	{
		$date_start= change_date_format($date_start,"yyyy-mm-dd");
		$date_end= change_date_format($date_end,"yyyy-mm-dd");
	}
	else
	{
		$date_start= change_date_format($date_start,"dd-mm-yyyy","-",1);
		$date_end= change_date_format($date_end,"dd-mm-yyyy","-",1);
	}
	
	
	//echo "select week_date,week from week_of_year where week_date between '$date_start' and  '$date_end' order by week_date asc";die;
	// date("d-m-Y", time() - 1296000);
	$week_for_arr=array();$no_of_week_for_header=array();
	$sql_week_header=sql_select("select week_date,week from week_of_year where week_date between '$date_start' and  '$date_end' order by week_date asc");
	$week_check_head=array();
	foreach ($sql_week_header as $row_week_header)
	{
		if($week_check_head[$row_week_header[csf("week")]]=='')
		{
			$week_counter_header[date("Y-M",strtotime($row_week_header[csf("week_date")]))][$row_week_header[csf("week")]]=$row_week_header[csf("week")];
			$week_check_head[$row_week_header[csf("week")]]=$week_counter_header[date("Y-M",strtotime($row_week_header[csf("week_date")]))][$row_week_header[csf("week")]];
		}
		$tmp=add_date($row_week_header[csf("week_date")],-1);
		if($db_type==0) $tmp_cond=date("d-m-y",strtotime($tmp));
		else $tmp_cond=date("d-M-y",strtotime($tmp));
		$no_of_week_for_header_calc[$tmp_cond]=$row_week_header[csf("week")];
		
		$no_of_week_for_header[$row_week_header[csf("week_date")]]=$row_week_header[csf("week")];
		$test[$row_week_header[csf("week")]]=$row_week_header[csf("week")];
	}
	unset($sql_week_header);
	$week_start_day=array();
	$week_end_day=array();$week_day_arr=array();
	//week_day
	$sql_week_start_end_date=sql_select("select week,min(week_date) as week_start_day, Max(week_date) as week_end_day from week_of_year where week_date between '$date_start' and  '$date_end' group by week");
	foreach ($sql_week_start_end_date as $row_week)
	{
		$week_start_day[$row_week[csf("week")]][week_start_day]=$row_week[csf("week_start_day")];
		$week_end_day[$row_week[csf("week")]][week_end_day]=$row_week[csf("week_end_day")];
		//$week_day_arr[$row_week[csf("week")]][$row_week[csf("week_start_day")]][week_first_day]=$row_week[csf("min_week_day")];
		$week_day_arr[$row_week[csf("week_end_day")]]['week_last_day']=$row_week[csf("last_week_day")];
	}
	unset($sql_week_start_end_date);
	//print_r($week_day_arr);
	function distribute_projection( $base_week, $qnty)
	{
		//5. Distribution ratios are: Base week 52%, Immediate before 24%, Week before immediate week 20%, Immediate week after base week 2%, next 2 weeks as 1%
		global $test;
		 
		$tmpbase_week=$test[$base_week-3];
		$k=0;
		for($i=0; $i<6; $i++)
		{
			$tmpbase_week++;
			if( $test[$tmpbase_week]=='') { $tmpbase_week=1; $k=0; }
			
			
			if($i==0) $distr_arr[$tmpbase_week]=($qnty*20)/100;
			if($i==1) $distr_arr[$tmpbase_week]=($qnty*24)/100;
			if($i==2) $distr_arr[$tmpbase_week]=($qnty*52)/100;
			if($i==3) $distr_arr[$tmpbase_week]=($qnty*2)/100;
			if($i==4) $distr_arr[$tmpbase_week]=($qnty*1)/100;
			if($i==5) $distr_arr[$tmpbase_week]=($qnty*1)/100;
			$k++;
			
		}
		 
		return $distr_arr;
		/*5000
		b=52
		b-1=21
		b-2=15
		b+1=1
		b+2=1*/
	}
	//echo "<per>";
	 //print_r(distribute_projection("2016-Dec",51, 5000)); die;
	

	
	if($template==1)
	{
	$po_wise_buyer=array();
	$buyer_wise_data=array();
	$po_id_arr=array();
	$week_wise_order_qty_arr=array();
		
	$count_data=sql_select("select yarn_count,id from  lib_yarn_count  where status_active=1 and is_deleted=0 and yarn_count is not null $yarn_count_con");
	$yarn_id='';
	foreach($count_data as $row)
	{
			if($yarn_id=='') $yarn_id=$row[csf('id')];else $yarn_id.=",".$row[csf('id')];
	}
	$yarn_ids=count(array_unique(explode(",",$yarn_id)));
		$yarn_numIds=chop($yarn_id,','); $yarnIds_cond="";
		if($yarn_id!='' || $yarn_id!=0)
		{
			if($db_type==2 && $yarn_ids>1000)
			{
				$yarnIds_cond=" and (";
				$yIdsArr=array_chunk(explode(",",$yarn_numIds),990);
				foreach($yIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$yarnIds_cond.=" d.count_id  in($ids) or ";
				}
				$yarnIds_cond=chop($yarnIds_cond,'or ');
				$yarnIds_cond.=")";
			}
			else
			{
				$yarnIds_cond=" and  d.count_id  in($yarn_id)";
			}
		}
	$yarn_sql_data=("select a.product_code,b.is_confirmed,b.id as po_id,d.count_id,d.copm_one_id,d.copm_two_id,d.percent_one,d.percent_two,sum(d.cons_qnty) as cons_qnty,e.construction,e.gsm_weight from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fab_yarn_cost_dtls d,wo_pre_cost_fabric_cost_dtls e where a.job_no=b.job_no_mst  and c.job_no_mst=a.job_no and b.id=c.po_break_down_id and d.job_no=a.job_no and 
	e.id=d.fabric_cost_dtls_id and  c.item_number_id= e.item_number_id and e.job_no=a.job_no and e.body_part_id in(1,20) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1   and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $yarnIds_cond $ordercond $team_cond $poIds_cond group by   b.id ,d.count_id,d.copm_one_id,d.copm_two_id,d.percent_one,d.percent_two,e.construction,e.gsm_weight,a.product_code,b.is_confirmed"); 
	$sql_result_y=sql_select($yarn_sql_data);
	$wo_fab_data_arr=array();
	$all_yarn_po_id="";
	foreach($sql_result_y as $row)
		{
			if($all_yarn_po_id=="") $all_yarn_po_id=$row[csf("po_id")]; else $all_yarn_po_id.=",".$row[csf("po_id")];
			if($row[csf('percent_two')]!=0) $row[csf('percent_two')]=$row[csf('percent_two')];else $row[csf('percent_two')]='';
			$percent_one=$row[csf('percent_one')].'**'.$row[csf('percent_two')];
			
			if($row[csf('copm_two_id')]!=0) $row[csf('copm_two_id')]=$row[csf('copm_two_id')];else $row[csf('copm_two_id')]='';
			$copm_one_id=$row[csf('copm_one_id')].'**'.$row[csf('copm_two_id')];
			
			$prod_code=$row[csf('product_code')];
			$wo_fab_data_arr[$prod_code][$row[csf('is_confirmed')]]['percent_one']=$percent_one;
			//$wo_fab_data_arr[$prod_code][$row[csf('is_confirmed')]]['percent_two']=$row[csf('percent_two')];
			$wo_fab_data_arr[$prod_code][$row[csf('is_confirmed')]]['copm_two_id']=$copm_one_id;
			//$wo_fab_data_arr[$prod_code][$row[csf('is_confirmed')]]['copm_one_id']=$row[csf('copm_one_id')];
			$wo_fab_data_arr[$prod_code][$row[csf('is_confirmed')]]['construction']=$row[csf('construction')];
			$wo_fab_data_arr[$prod_code][$row[csf('is_confirmed')]]['gsm_weight']=$row[csf('gsm_weight')];
			$wo_fab_count_data_arr[$prod_code][$row[csf('is_confirmed')]]['count_id'].=$row[csf('count_id')].",";
			//$wo_yarn_data_arr[$row[csf('po_id')]]['cons_qnty']+=$row[csf('cons_qnty')];
		}
		unset($sql_result_y);
		$yarn_po_ids=count(array_unique(explode(",",$all_yarn_po_id)));
		$yarnPo_numIds=chop($all_yarn_po_id,','); $yarnPoIds_cond="";
		if($txt_yarn_count!='')
		{
			if($all_yarn_po_id!='' || $all_yarn_po_id!=0)
			{
				if($db_type==2 && $yarn_po_ids>1000)
				{
					$yarnPoIds_cond=" and (";
					$yPoIdsArr=array_chunk(explode(",",$yarnPo_numIds),990);
					foreach($yPoIdsArr as $ids)
					{
						$ids=implode(",",$ids);
						$yarnPoIds_cond.=" b.id  in($ids) or ";
					}
					$yarnPoIds_cond=chop($yarnPoIds_cond,'or ');
					$yarnPoIds_cond.=")";
				}
				else
				{
					$yarnPoIds_cond=" and  b.id  in($all_yarn_po_id)";
				}
			}
		}
		$prod_wise_arr_qty=array();
		$sql_data_c=("select b.id as po_id,a.product_code,b.shipment_date as orgi_ship_date,b.is_confirmed,(b.po_quantity*a.total_set_qnty) as po_qty_pcs
		from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $team_cond $bh_mer_name_con order by a.product_code,b.shipment_date ");
		$sql_result_c=sql_select($sql_data_c);
		foreach($sql_result_c as $row)
		{
			if($row[csf('is_confirmed')]==2)
			{
				$tod_week=$no_of_week_for_header[$row[csf('orgi_ship_date')]];
				$prod_code=$row[csf('product_code')];
				if($prod_code=='') $prod_code=0;else $prod_code=$prod_code;
				$prod_wise_arr_qty[$prod_code][$row[csf('is_confirmed')]][$tod_week]['po_qty']+=$row[csf('po_qty_pcs')];
			}
		}	
//print_r($prod_wise_arr_qty);
	if($db_type==0) $date_dif="DATEDIFF(b.shipment_date, b.po_received_date) as lead_time_days";
else  $date_dif="(b.shipment_date-b.po_received_date) as lead_time_days";
	   $sql_query=("select $date_dif,a.job_no,a.job_no_prefix_num as prefix_job_no,a.set_smv,a.season_buyer_wise  as 	season,a.product_code,a.bh_merchant,a.dealing_marchant,a.style_ref_no,b.id,b.is_confirmed,b.po_number,b.po_received_date,b.shipment_date as orgi_ship_date,a.total_set_qnty as set_ratio,(b.po_quantity*a.total_set_qnty) as po_qty_pcs,b.po_total_price,a.avg_unit_price as unit_price,c.country_ship_date,c.order_quantity,c.order_total
	   from wo_po_details_master a, wo_po_break_down b
	  	LEFT JOIN wo_po_color_size_breakdown c on  c.po_break_down_id=b.id  and c.status_active=1 
	 where a.job_no=b.job_no_mst  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1   and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $bh_mer_name_con $team_cond $yarnPoIds_cond order by a.product_code,a.job_no ");
	  
	
					 
	$sql_data=sql_select($sql_query);
	$tot_rows=count($sql_data);
	$all_po_id="";
	$marchant_wise_arr=array();
	foreach( $sql_data as $row)
	{
		$prod_code=$row[csf('product_code')];
		
		if($prod_code=='') $prod_code=0;else $prod_code=$prod_code;
		//echo $prod_code.',';
		if($db_type==0) $c_date_con=date("d-m-y",strtotime($row[csf('country_ship_date')]));
		else $c_date_con=date("d-M-y",strtotime($row[csf('country_ship_date')]));
		$c_date_week=$no_of_week_for_header_calc[$c_date_con];
		if($row[csf('is_confirmed')]==1)
		{
			$order_val=$row[csf('order_total')];
		}
		else
		{
			$order_val=$row[csf('po_total_price')];
		}
		
		if($all_po_id=="") $all_po_id=$row[csf("id")]; else $all_po_id.=",".$row[csf("id")];
		
		$pcode_wise_arr[$prod_code][$row[csf('is_confirmed')]]['po_qty']+=$row[csf('po_qty_pcs')];
		$pcode_wise_arr[$prod_code][$row[csf('is_confirmed')]]['po_val']+=$order_val;
		$pcode_wise_arr[$prod_code][$row[csf('is_confirmed')]]['set_smv']+=$row[csf('set_smv')];
		$pcode_wise_arr[$prod_code][$row[csf('is_confirmed')]]['set_ratio']+=$row[csf('set_ratio')];
		$pcode_wise_arr[$prod_code][$row[csf('is_confirmed')]]['bh_mer']+=$row[csf('bh_merchant')];
		$pcode_wise_arr[$prod_code][$row[csf('is_confirmed')]]['unit_price']+=$row[csf('unit_price')];
		$pcode_wise_arr[$prod_code][$row[csf('is_confirmed')]]['job_no'].=$row[csf('prefix_job_no')].',';
		$pcode_wise_arr[$prod_code][$row[csf('is_confirmed')]]['style'].=$row[csf('style_ref_no')].',';
		$pcode_wise_arr[$prod_code][$row[csf('is_confirmed')]]['season'].=$row[csf('season')].',';

		$pcode_wise_arr[$prod_code][$row[csf('is_confirmed')]]['dealing_marchant'].=$row[csf('dealing_marchant')].',';
		$pcode_wise_arr[$prod_code][$row[csf('is_confirmed')]]['po_no'].=$row[csf('po_number')].',';
		$pcode_wise_arr[$prod_code][$row[csf('is_confirmed')]]['po_id'].=$row[csf('id')].',';
		$pcode_wise_arr[$prod_code][$row[csf('is_confirmed')]]['po_received_date'].=$row[csf('po_received_date')].',';
		$pcode_wise_arr[$prod_code][$row[csf('is_confirmed')]]['orgi_ship_date'].=$row[csf('orgi_ship_date')].',';
		$pcode_wise_arr[$prod_code][$row[csf('is_confirmed')]]['c_ship_date'].=$row[csf('country_ship_date')].',';
		//d.percent_one,d.percent_two 
		$pcode_wise_arr[$prod_code][$row[csf('is_confirmed')]]['percent'].=$percent_one.',';
		$pcode_wise_arr[$prod_code][$row[csf('is_confirmed')]]['comp'].=$copm_one_id.',';
		$pcode_wise_arr[$prod_code][$row[csf('is_confirmed')]]['lead_day']+=$row[csf('lead_time_days')];
		if($row[csf('is_confirmed')]==1)
		{
			$week_wise_order_qty[$prod_code][$row[csf('is_confirmed')]][$c_date_week]['po_quantity']+=$row[csf("order_quantity")];
		}
		
	}
	//print_r($week_wise_order_qty);
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
					$poIds_cond.=" b.id  in($ids) or ";
				}
				$poIds_cond=chop($poIds_cond,'or ');
				$poIds_cond.=")";
			}
			else
			{
				$poIds_cond=" and  b.id  in($all_po_id)";
			}
		}
		$exfactory_data_array=array();
		$exfactory_sql=("select a.product_code,b.is_confirmed,
		sum(CASE WHEN f.entry_form!=85 THEN f.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		sum(CASE WHEN f.entry_form=85 THEN f.ex_factory_qnty ELSE 0 END) as return_qnty
		 from wo_po_details_master a, wo_po_break_down b,pro_ex_factory_mst f  where  a.job_no=b.job_no_mst and  f.po_break_down_id=b.id  and  a.company_name=$company_name  and f.status_active=1 and f.is_deleted=0 $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $team_cond group by  a.product_code, b.is_confirmed");
		$exfactory_data=sql_select($exfactory_sql);
		foreach($exfactory_data as $row)
		{
				$prod_code=$row[csf('product_code')];
				$exfactory_data_array[$prod_code][$row[csf('is_confirmed')]][ex_qnty]+=$row[csf('ex_factory_qnty')]-$row[csf('return_qnty')];
		}
		unset($exfactory_data);
	
		ob_start();
		$rowcount=0;
		foreach($week_counter_header as $mon_key=>$week)
		{
			$rowcount+=count($week);
		}
		 $rowb=$rowcount*2;
		 $td_width=1750+($rowcount*45)+$rowb;
		 
		 $tot_month = datediff( 'm', $date_start,$date_end);
		for($i=0; $i<= $tot_month; $i++ )
		{
			$next_month=month_add($date_start,$i);
			$month_arr.=date("Y-M",strtotime($next_month)).',';
		}
	 	$month_arr_id=rtrim($month_arr,',');
	if($tot_rows>0)
	{
	?>
      <script>
		var mon_week ='<? echo $month_arr_id; ?>';
		var mon_week=mon_week.split(",");
		for (var k=0; k<mon_week.length; k++)
		{
			var mon_data=document.getElementById('summary_footer_td_'+mon_week[k]).innerHTML;
			var mon_arr=mon_data.split("$");
			var mon_qnty=number_format(mon_arr[0],0);
			var mon_amt=number_format(mon_arr[1],0);
			var order_smv=number_format(mon_arr[2],2);
			//alert(mon_amt);
			var avg_rate=(number_format(mon_arr[1],0,'.','')/number_format(mon_arr[0],0,'.',''));
			document.getElementById('summary_header_td_qty_'+mon_week[k]).innerHTML=mon_qnty;
			document.getElementById('summary_header_td_val_'+mon_week[k]).innerHTML='$'+mon_amt;
			document.getElementById('summary_header_td_rate_'+mon_week[k]).innerHTML='FOB:'+number_format(avg_rate,2);
			document.getElementById('summary_header_td_smv_'+mon_week[k]).innerHTML='SMV:'+number_format(order_smv,2);
		}
			var yarn_req_qty=document.getElementById('td_yarn_req_march').innerHTML;
			var yarnreq_qty=number_format(yarn_req_qty,0);
			document.getElementById('yarn_summary_tot').innerHTML=yarnreq_qty;
		</script>
        <?
	}
		?>
		<div style="width:<? echo $td_width+20;?>px">
		<fieldset style="width:100%;">	
      
        
			<table width="<? echo $td_width+20;?>px">
				<tr class="form_caption">
					<td align="left" style="margin-left:10px" colspan="<? echo $rowcount+25; ?>">Prod Code Wise</td>
				</tr>
				<tr class="form_caption" align="center" style="display:none">
					<td colspan="<? echo $rowcount+26; ?>" align="center"><? //echo $company_library[$company_name]; ?></td>
				</tr>
			</table>
			<table style="font:'Arial Narrow';font-size:9px" class="rpt_table" width="<? echo $td_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
                  <tr>
					
                    <th  width="" colspan="11" rowspan="2">&nbsp;</th>
                    <th  rowspan="2" id="yarn_summary_tot"></th>
                    <th  width="" colspan="14" rowspan="2">&nbsp;</th>
                     <?
					
					foreach($week_counter_header as $mon_key=>$week)
					{
					?>
					<th   align="center" colspan="<? echo count($week);?>">
					<? 
					echo $mon_key;
					?>
					</th>
					<?
					}
            ?>
                   
                </tr>
                  <tr>
					
                     <?
					foreach($week_counter_header as $mon_key=>$week_val)
					{
					?>
					<th width="60"  style="font-size:0.875em;"  align="center" colspan="<? echo count($week_val)?>">
                       <b id="summary_header_td_qty_<? echo $mon_key ;?>"  style="text-align:left; font-size: 0.775em;"> </b> &nbsp; 
                       <b id="summary_header_td_val_<? echo $mon_key ;?>" style="text-align: right; font-size: 0.775em;">  </b> &nbsp;
                       <b id="summary_header_td_rate_<? echo $mon_key ;?>"  style="text-align: right;font-size: 0.775em;"></b> &nbsp;
                        <b title="Avg Smv" id="summary_header_td_smv_<? echo $mon_key ;?>"  style="text-align: right;font-size: 0.775em;"></b>
					</th>
					<?
					}
            ?>
                   
                </tr>
                <tr>
					<th width="30">SL</th>
                    <th width="55">Season</th>
                    <th width="80">BH Mer.</th>
					<th width="70">Merchant</th>
                    <th width="55">Job No</th>
					<th width="100">Style Ref</th>
                    <th width="60">Comp%</th>
                    <th width="120">Comp Name</th>
                    <th width="120">Const.</th>
                    <th width="50">GSM</th>
					<th width="120">Y/C</th>
                    <th width="70">Yarn Qty</th>
					<th width="60">Status</th>
					<th width="45">OPD</th>
					<th width="50">OPD Date</th>
					<th width="45">TOD</th>
                    <th width="70">Ord Qty</th>
					<th width="100">Order No</th>
                    <th width="40">Dept </th>
					<th width="40">Lead Time</th>
					<th width="40">Unit Price</th>
					<th width="90">Value</th>
					<th width="40">SMV/ Pcs</th>
                    <th width="90">Ord.SMV</th>
                    <th width="60">Delv Qty</th>
                    <th width="60">Delv.Val</th>
                     <?
					//foreach($week_for_header as $week_key)
					foreach($week_counter_header as $mon_key=>$week)
					{
						foreach($week as $week_key)
						{
							$start_week_date=$week_start_day[$week_key][week_start_day];
							$end_week_date=$week_end_day[$week_key][week_end_day];
							//$fist_day=$week_day_arr[$week_key][$start_week_date][week_first_day];
							//$last_day=$week_day_arr[$week_key][$end_week_date][week_last_day];
							//.'('.$fist_day.')'
							if($db_type==2)
							{
								$fisrtday=date("D",strtotime(change_date_format($start_week_date,"dd-mm-yyyy","-")));
								$lastday=date("D",strtotime(change_date_format($end_week_date,"dd-mm-yyyy","-")));
							}
							else
							{
								$fisrtday=date("D",strtotime(change_date_format($start_week_date,"dd-mm-yyyy")));
								$lastday=date("D",strtotime(change_date_format($end_week_date,"dd-mm-yyyy")));
							}
							$weekstart_date=explode("-",date("d-M",strtotime($start_week_date)));
							$weekstart_date=$weekstart_date[0].'/'.$weekstart_date[1];
							
						?>
						<th title="<? echo change_date_format($start_week_date,"dd-mm-yyyy","-").'('.$fisrtday.')'." To ".change_date_format($end_week_date,"dd-mm-yyyy","-").'('.$lastday.')'; ?>" width="45"  align="center">
						
						<? 
						echo 'W'.$week_key.'<br/>'.$weekstart_date;
						?>
						</th>
						<?
						}
					}
            ?>
                   
                </tr>    
				</thead>
			</table>
			<div style="width:<? echo $td_width+20;?>px; max-height:245px; overflow-y:scroll;overflow-x:hidden;" id="scroll_body">
			<table class="rpt_table" width="<? echo $td_width;?>px" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body_pcode">
	<?
	
               
			$condition= new condition();
			 $condition->company_name("=$cbo_company_name");
			 if(str_replace("'","",$cbo_buyer_name)>0)
			 {
				  $condition->buyer_name("=$cbo_buyer_name");
			 }
			 if(str_replace("'","",$txt_job_no) !='')
			 {
				  $condition->job_no_prefix_num("in($txt_job_no)");
			 }
			
			 if(str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
			 {
			 $condition->pub_shipment_date(" between '$start_date' and '$end_date'");
			 }
				
			 if(str_replace("'","",$txt_order_no)!='')
			 {
				$condition->po_number("=$txt_order_no"); 
			 }
			 if(str_replace("'","",$txt_season)!='')
			 {
				//$condition->season("=$txt_season"); 
			 }
			 	$condition->init();
       		 	$yarn= new yarn($condition);
		  	//echo $yarn->getQuery(); die;
		 		$yarn_req_qty_arr=$yarn->getOrderWiseYarnQtyArray();
				$i=1;$total_order_qty=0;$total_order_value=0;$total_yarn_req_qty=0;$total_order_smv=0;$total_ex_fact_val=0;$total_ex_fact_qty=0;
				foreach($pcode_wise_arr as $pcode_key=>$pcode_data)
				{  
				   	foreach($pcode_data as $status_key=>$status_val)
					{
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";  
				$po_received_date=$status_val['po_received_date'];
				//$conf_country_ship_date=rtrim($wo_color_data_arr2[$pcode_key][$status_key]['shipdate'],',');
				//$conf_country_ship_date=explode(",",$conf_country_ship_date);
				
				$percent_one=$wo_fab_data_arr[$pcode_key][$row[csf('is_confirmed')]]['percent_one'];//rtrim($status_val['percent'],',');
				$construction=rtrim($status_val['const'],',');
				$gsm_weight=rtrim($status_val['gsm'],',');
				$comp=$wo_fab_data_arr[$pcode_key][$row[csf('is_confirmed')]]['copm_two_id'];//rtrim($status_val['comp'],',');
				$gsm_weight=implode(",",array_unique(explode(",",$gsm_weight)));
				$construction=implode(",",array_unique(explode(",",$construction)));
				
				$percent_data=array_unique(explode(",",$percent_one));
				$comp_data=array_unique(explode(",",$comp));
				
				$percent_one_cond="";$percent_two_cond="";
				foreach($percent_data as $perid)
				{
						$percent_data=array_unique(explode("**",$perid));
						$percent_one=$percent_data[0];
						$percent_two=$percent_data[1];
						if($percent_one_cond=="") $percent_one_cond=$percent_one;else $percent_one_cond.=",".$percent_one;
						if($percent_two_cond=="") $percent_two_cond=$percent_two;else $percent_two_cond.=",".$percent_two;
				}
				if($percent_one_cond!='' && $percent_two_cond!='')
					{
						//if($percent_two!=0)
						$percent_all=$percent_one_cond.'/'.$percent_two_cond;
					}
					else if($percent_one_cond!='' && $percent_two_cond=='')
					{
						//if($percent_two!=0)
						$percent_all=$percent_one_cond;
					}
					else if($percent_one_cond=='' && $percent_two_cond!='')
					{
						//if($percent_two!=0)
						$percent_all=$percent_two_cond;
					}
					else
					{
						$percent_all='';	
					}
				$comp_one_cond="";$comp_two_cond="";
				foreach($comp_data as $compid)
				{
						$comp_data=array_unique(explode("**",$compid));
						$comp_one=$comp_data[0];
						$comp_two=$comp_data[1];
						
					if($comp_one_cond=="") $comp_one_cond=$composition[$comp_one];else $comp_one_cond.=",".$composition[$comp_one];
					if($comp_two_cond=="") $comp_two_cond=$composition[$comp_two];else $comp_two_cond.=",".$composition[$comp_two];
				}
				
				if($comp_one_cond!='' && $comp_two_cond!='')
				{ 
					$copm_one_name=$comp_one_cond.'/'.$comp_two_cond;
				}
				else if($comp_one_cond!='' && $comp_two_cond=='')
				{ 
					$copm_one_name=$comp_one_cond;
				
				}
				else if($comp_one_cond=='' && $comp_two_cond!='')
				{ 
					$copm_one_name=$comp_two_cond;
				
				}
				else 
				{
					$copm_one_name='';
				}
				
				$construction=$wo_fab_data_arr[$pcode_key][$status_key]['construction'];
				$gsm_weight=$wo_fab_data_arr[$pcode_key][$status_key]['gsm_weight'];
				$count_id=$wo_fab_count_data_arr[$pcode_key][$status_key]['count_id'];
				$count_id=rtrim($count_id,',');
				$count_ids=array_unique(explode(",", $count_id));
				$yarn_count_value="";
				foreach($count_ids as $val)
				{
						if($yarn_count_value=="") $yarn_count_value=$lib_yarn_count_arr[$val]; else $yarn_count_value.=", ".$lib_yarn_count_arr[$val];
				}
				$seasion_id=rtrim($status_val['season'],',');
				$job_no=rtrim($status_val['job_no'],',');
				$job_no=implode(",",array_unique(explode(",",$job_no)));
				$style=rtrim($status_val['style'],',');
				$po_no=rtrim($status_val['po_no'],',');
				$po_id=rtrim($status_val['po_id'],',');
			
				$dealing_marchant=rtrim($status_val['dealing_marchant'],',');
				$bh_mer=rtrim($status_val['bh_mer'],',');
				$bh_mer=implode(",",array_unique(explode(",",$bh_mer)));
				
				$po_received_date=rtrim($status_val['po_received_date'],',');
				$c_ship_date=rtrim($status_val['c_ship_date'],',');
				$job_no=implode(",",array_unique(explode(",",$job_no)));
				$style=implode(",",array_unique(explode(",",$style)));
				$po_no=implode(",",array_unique(explode(",",$po_no)));
				$po_id=array_unique(explode(",",$po_id));
			
				$yarn_req=0;
				foreach($po_id as $pid)
				{
				$yarn_req+=$yarn_req_qty_arr[$pid];
				}
				//echo $yarn_req.'<br>';
				
				$seasion_id=array_unique(explode(",",$seasion_id));
				$seasion_cond="";
				foreach($seasion_id as $sid)
				{
					if($seasion_cond=="") $seasion_cond=$lib_season_name_arr[$sid];else $seasion_cond.=",".$lib_season_name_arr[$sid];
				}
				$dealing_marchant=array_unique(explode(",",$dealing_marchant));
				$dealing_marchant_con="";
				foreach($dealing_marchant as $mid)
				{
					if($dealing_marchant_con=="") $dealing_marchant_con=$team_member_arr[$mid];else $dealing_marchant_con.=",".$team_member_arr[$mid];
				}
				$po_received_date=array_unique(explode(",",$po_received_date));
				$opd_week="";$opd_recv_date="";$opd_recv_date_full="";
				//$lead_time=0;
				foreach($po_received_date as $op_id)
				{
					if($opd_week=="") $opd_week=$no_of_week_for_header[$op_id];else $opd_week.=", ".$no_of_week_for_header[$op_id];
					//if($opd_recv_date=="") $opd_recv_date=change_date_format($op_id);else $opd_recv_date.=", ".change_date_format($op_id);
					if($opd_recv_date=="") $opd_recv_date=date("d-M",strtotime($op_id));else $opd_recv_date.=",".date("d-M",strtotime($op_id));
					if($opd_recv_date_full=="") $opd_recv_date_full=date("d-M-y",strtotime($op_id));else $opd_recv_date_full.=",".date("d-M-y",strtotime($op_id));
				//$lead_time = datediff( 'd', $op_id,$countryship_date);
				}
				//echo $status_val['orgi_ship_date'];
				$po_orgi_shipdate=rtrim($status_val['orgi_ship_date'],',');
				$po_orgi_shipdate=implode(",",array_unique(explode(",",$po_orgi_shipdate)));
				$po_orgi_shipdate=array_unique(explode(",",$po_orgi_shipdate));
				$tod_weeks="";$tod_recv_date="";
				foreach($po_orgi_shipdate as $orgi_id)
				{
					if($tod_weeks=="") $tod_weeks=$no_of_week_for_header[$orgi_id];else $tod_weeks.=",".$no_of_week_for_header[$orgi_id];
				}
				$tod_weeks=implode(",",array_unique(explode(",",$tod_weeks)));
				$lead_time=$status_val['lead_day'];
				$opd_recv_date=implode(",",array_unique(explode(", ",$opd_recv_date)));
				
				if($status_key==1) //Confirm $conf_country_ship_date
				{
					$c_ship_date_con=array_unique(explode(",",$c_ship_date));$qnty_array_dd=array();
					$c_week_qty=0;
					foreach($c_ship_date_con as $c_date)
					{
								if($db_type==0) $c_date_con=date("d-m-y",strtotime($c_date));
								else $c_date_con=date("d-M-y",strtotime($c_date));
								$c_week=$no_of_week_for_header_calc[$c_date_con];
								//echo $c_week;
								$c_week_qty=$week_wise_order_qty[$pcode_key][$status_key][$c_week]['po_quantity'];
										//echo $c_week.'=='.$c_date.'hj';
								$qnty_array_dd[$c_week]=$c_week_qty;
					}
					//print_r($qnty_array_dd);
						$tot_order_qty=0;$tot_order_qty_arr=array();
						foreach($week_counter_header as $mon_key=>$week)
						{
							foreach($week as $week_key)
							{
								 $tot_order_qty=$qnty_array_dd[$week_key];
								 $order_qty_arr[$week_key]=$tot_order_qty;
								  $tot_order_qty_arr[$pcode_key]+=$tot_order_qty;
							}
						}
						
				}
				else 
				{
						$po_orgi_shipdate=rtrim($status_val['orgi_ship_date'],',');
						//$po_orgi_shipdate=implode(",",array_unique(explode(",",$po_orgi_shipdate)));
						$proj_orgi_shipdate=array_unique(explode(",",$po_orgi_shipdate));
						$qnty_array_mm=array();$proj_po_qty=0;
						$week_check=array();
						foreach($proj_orgi_shipdate as $tod_key)
						{
							$tod_week=$no_of_week_for_header[$tod_key];
							//echo $tod_week.',';
							if($week_check[$tod_week]=='')
							{
								 $proj_po_qty=$prod_wise_arr_qty[$pcode_key][$status_key][$tod_week]['po_qty'];
							
								$qnty_arr=distribute_projection($tod_week, $proj_po_qty);
								foreach($qnty_arr as $key=>$val)
								{
									$qnty_array_mm[$key]+=$val;
								}
								
								$week_check[$tod_week]=$proj_po_qty;
							}
						}
					
							$order_qty=0;$tot_order_qty_arr=array();
							foreach($week_counter_header as $mon_key=>$week)
							{
								foreach($week as $week_key)
								{
									$order_qty=$qnty_array_mm[$week_key];
									$order_qty_arr[$week_key]=$order_qty;
									$tot_order_qty_arr[$pcode_key]+=$order_qty;
								}
							}
				}
				$tot_po_qty=$tot_order_qty_arr[$pcode_key];
				$po_value=$status_val['po_val'];
				$unit_price=$po_value/$tot_po_qty;
				//echo $po_value.'='.$tot_po_qty.',';
				
				$job_no_row=count(array_unique(explode(",",$job_no)));
				if($job_no_row>1)
				{
					 $view_button="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$pcode_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button=$job_no;	
				}
				
				$tod_weeks_row=count(array_unique(explode(",",$tod_weeks)));
				if($tod_weeks_row>1)
				{
					 $view_button_tod="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$pcode_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_tod=$tod_weeks;	
				}
				
				$opd_week_row=count(array_unique(explode(",",$opd_week)));
				if($opd_week_row>1)
				{
					 $view_button_opd="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$pcode_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_opd=$opd_week;	
				}
				$opd_recv_date_row=count(array_unique(explode(",",$opd_recv_date)));
				if($opd_recv_date_row>1)
				{
					 $view_button_opd_date="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$pcode_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_opd_date=$opd_recv_date;	
				}
				$po_no_row=count(array_unique(explode(",",$po_no)));
				if($po_no_row>1)
				{
					 $view_button_po="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$pcode_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_po=$po_no;	
				}
				$dealing_marchant_con_row=count(array_unique(explode(",",$dealing_marchant_con)));
				$bh_mer_con_row=count(array_unique(explode(",",$bh_mer)));
				$seasion_cond_row=count(array_unique(explode(",",$seasion_cond)));
				$construction_cond_row=count(array_unique(explode(",",$construction)));
				$gsm_weight_cond_row=count(array_unique(explode(",",$gsm_weight)));
				$yarn_count_value_cond_row=count(array_unique(explode(",",$yarn_count_value)));
				$style_cond_row=count(array_unique(explode(",",$style)));
				$copm_cond_row=count(array_unique(explode(",",$copm_one_name)));
				
				if($dealing_marchant_con_row>1)
				{
					 $view_button_deal="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$pcode_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_deal=$dealing_marchant_con;
				}
				if($bh_mer_con_row>1)
				{
					 $view_button_bh="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$pcode_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					
					$view_button_bh=$bh_mer;
					//$view_button_season=$seasion_cond;	
				}
				if($seasion_cond_row>1)
				{
					 $view_button_season="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$pcode_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_season=$seasion_cond;	
				}
				if($construction_cond_row>1)
				{
					 $view_button_const="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$pcode_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_const=$construction;	
				}
				if($gsm_weight_cond_row>1)
				{
					 $view_button_gsm="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$pcode_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_gsm=$$gsm_weight;	
				}
				if($yarn_count_value_cond_row>1)
				{
					 $view_button_count="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$pcode_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_count=$yarn_count_value;	
				}
				if($style_cond_row>1)
				{
					 $view_button_style="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$pcode_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_style=$style;	
				}
				if($copm_cond_row>1)
				{
					 $view_button_comp="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$pcode_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_comp=$copm_one_name;	
				}
				 
							
				?>
                <tr style="font:'Arial Narrow'; font-size:9px" align="center" bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
					<td width="30"><? echo $i; ?></td>
                    <td width="55" style="word-wrap:break-word; word-break: break-all;"><? echo $view_button_season;?></td>
                    <td width="80" style="word-wrap:break-word; word-break: break-all;" ><? echo $view_button_bh; ?></td> 
					<td width="70" style="word-wrap:break-word; word-break: break-all;" ><? echo $view_button_deal; ?></td> 
                    <td width="55" style="word-wrap:break-word; word-break: break-all;">
                    
					<? echo $view_button_JOB;//$buyer_short_name_library[$row_data[csf('buyer_name')]]; ?></td>
					<td  width="100" style="word-wrap:break-word; word-break: break-all;"> <? echo $view_button_style; ?></td>
                    <td width="60" style="word-wrap:break-word; word-break: break-all;"><? echo $percent_all; ?></td>
                    <td  width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $view_button_comp; ?></td>
                    <td  width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $view_button_const;?></td>
                    <td width="50"  style="word-wrap:break-word; word-break: break-all;"><? echo $view_button_gsm; ?></td>
					<td  width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $view_button_count ?></td>
                    <td  width="70" title="<? echo $yarn_req;?>" style="word-wrap:break-word; word-break: break-all; text-align:right">
					<?
							echo number_format($yarn_req);
					?>
                    </td>
					<td  width="60" style="word-wrap:break-word; word-break: break-all;">
					<?
					
					if($status_key==2)
					{
						echo "Booked";	
					}
					else
					{
						echo "Placed";
					}
					?> 
                    </td>
					<td  width="45" style="word-wrap:break-word; word-break: break-all;">
					<?
					echo $view_button_opd;
					?>
                    </td>
					<td  width="50" title="<? echo $opd_recv_date_full;?>" style="word-wrap:break-word; word-break: break-all;">
                    
					<?
					if($opd_recv_date!="" && $opd_recv_date !="0000-00-00" && $opd_recv_date!="0")
					{
						$opd_recv_date=$opd_recv_date;
					}
					echo $view_button_opd_date;
					?>
                     
                    </td>
					<td width="45" title="Orgi Ship Date" style="word-wrap:break-word; word-break: break-all; text-align: center">
					<? 
					
					echo $view_button_tod;
					//echo $row_data[csf('country_ship_date')]; ?>
                    </td>
                    <td  width="70" style="word-wrap:break-word; word-break: break-all; text-align:right">
					<? echo number_format($tot_po_qty);?>
                    </td>
					<td  width="100" style="word-wrap:break-word; word-break: break-all; text-align: center">
                    
					<? echo $view_button_po; ?>
                    </td>
                    <td  width="40" style="word-wrap:break-word; word-break: break-all; text-align:left" >
					<? echo $pcode_key;?>
                    </td>
					<td  width="40" title="TOD-OPD" style="word-wrap:break-word; word-break: break-all; text-align: center">
					<? echo $lead_time; ?>
                    </td>
					<td  width="40" title="PO Value/Po Qty" style="word-wrap:break-word; word-break: break-all; text-align:right">
					<? echo number_format($unit_price,2); ?>
                    </td>
                   
					<td width="90" title="Po Qty*Unit Price" style="word-wrap:break-word; word-break: break-all;text-align:right">
                   <? echo number_format($tot_po_qty*$unit_price,0);
				  	 $set_smv=number_format($status_val['set_smv'],2);
				    ?>
                    </td>
					<td width="40" title="Set SMV/Set Ratio" style="word-wrap:break-word; word-break: break-all;">
					<? 
					$smv_rate=0;
					$smv_rate=$set_smv/$status_val['set_ratio'];
					echo number_format($smv_rate,2); ?>
                    </td>
					
                    <td  width="90" title="Set SMV Per Pcs*Order Qty" style="word-wrap:break-word; word-break: break-all;text-align:right">
					<? echo number_format(($set_smv/$status_val['set_ratio'])*$tot_po_qty); ?>
                    </td>
                     <td  width="60" style="word-wrap:break-word; word-break: break-all;text-align:right">
					<? echo number_format($exfactory_data_array[$pcode_key][$status_key][ex_qnty]); ?>
                    </td>
                    <td width="60" title="Ex-Fact Qty*Unit Price" style="word-wrap:break-word; word-break: break-all;text-align:right">
                    <? echo  number_format($exfactory_data_array[$pcode_key][$status_key][ex_qnty]*$unit_price,0);
					 ?>
                    </td>
                    <?
					$week_qty=0;
					$week_check=array();
                   	foreach($week_counter_header as $mon_key=>$week)
					{
						foreach($week as $week_key)
						{
						?>
						<td  width="45" style="word-wrap:break-word; word-break: break-all;" align="right">
						
						<? 
							 $week_qty=$order_qty_arr[$week_key];
							if($week_qty>0)  echo number_format($week_qty,0);else echo '';
							$tot_week_qty[$mon_key][$week_key]+=$week_qty;
							$tot_week_amount[$mon_key][$week_key]+=$week_qty*$unit_price;
							
							$smv_avg=0;
							$smv_avg=$smv_rate*$week_qty;
							$month_smv_arr[$mon_key]+=$smv_avg;
						?>
						</td>
						<?
						}
					}
					?>
                    </tr>
                    <?
					$total_order_qty+=$tot_po_qty;
					$total_yarn_req_qty+=$yarn_req;	
					$total_ex_fact_qty+=$exfactory_data_array[$pcode_key][$status_key][ex_qnty];
					$total_ex_fact_val+=$exfactory_data_array[$pcode_key][$status_key][ex_qnty]*$unit_price;
					$total_order_value+=$tot_po_qty*$unit_price;
					$total_order_smv+=($set_smv/$status_val['set_ratio'])*$tot_po_qty;
					//$total_week_qty+=$week_qty;
					//=====================================================================================================================
				$i++;
					}
					
				}
				?>
                 
				</table>
				</div>
                <table class="tbl_bottom" style="font:'Arial Narrow'; font-size:9px" width="<? echo $td_width;?>px" cellpadding="0" cellspacing="0" border="1" rules="all" >
					<tr>
					<td width="30">&nbsp;</td>
                    <td width="55">&nbsp;</td>
                    <td width="70">&nbsp;</td>
					<td width="80">&nbsp;</td>
                    <td width="55">&nbsp;</td>
					<td width="100">&nbsp;</td>
                    <td width="60">&nbsp;</td>
                    <td width="120">&nbsp;</td>
                    <td width="120">&nbsp;</td>
                    <td width="50">&nbsp;</td>
					<td width="120">&nbsp;</td>
                    <td width="70" style="word-wrap:break-word; word-break: break-all; font-size:smaller" id="td_yarn_req_march"><? echo $total_yarn_req_qty;?></td>
					<td width="60">&nbsp;</td>
					<td width="45">&nbsp;</td>
					<td width="50">&nbsp;</td>
					<td width="45">&nbsp;</td>
                    <td width="70" style="word-wrap:break-word; word-break: break-all;font-size:smaller" id="td_po_qty_march"><? echo $total_order_qty;?></td>
					<td width="100">&nbsp;</td>
                    <td width="40">&nbsp; </td>
					<td width="40">&nbsp;</td>
					<td width="40">&nbsp;</td>
					<td width="90" id="td_po_val_march" style="word-wrap:break-word; word-break: break-all;font-size:smaller"><? echo number_format($total_order_value);?></td>
					<td width="40"><? echo number_format($total_order_smv/$total_order_qty,0);?></td>
                    <td width="90" style="word-wrap:break-word; word-break: break-all;font-size:smaller" id="total_order_smv"><? echo number_format($total_order_smv,0);?></td>
                    <td width="60" style="word-wrap:break-word; word-break: break-all;font-size:smaller" id="total_order_exfc_qty"><? echo number_format($total_ex_fact_qty,0);?></td>
                    <td width="60" style="word-wrap:break-word; word-break: break-all;font-size:smaller" id="total_order_exfc_val"><? echo number_format($total_ex_fact_val,0);?></td>
                     <?
					//foreach($week_for_header as $week_key)
					$total_week_qty=0; $total_week_amt=0; 
					foreach($week_counter_header as $mon_key=>$week)
					{
						foreach($week as $week_key)
						{
						?>
						<td width="45" style="word-wrap:break-word; word-break: break-all;font-size:smaller"   align="center">
						
						<? 
						$total_week_qty=$tot_week_qty[$mon_key][$week_key];
						$total_week_amt=$tot_week_amount[$mon_key][$week_key];
						echo number_format($total_week_qty,0);
						$tot_mon_qty[$mon_key]+=$total_week_qty;
						$tot_mon_amount[$mon_key]+=$total_week_amt;
						?>
						</td>
						<?
						
						}
					}
            ?>
					</tr>
                    <tr style="display:none">
					<td colspan="26">&nbsp;</td>
                     <?
					
					foreach($week_counter_header as $mon_key=>$week)
					{
						?>
						<td width="" id="summary_footer_td_<? echo $mon_key?>" colspan="<? echo count($week);?>" title="<? echo $tot_week_amount[$mon_key][$week_key];?>" style="word-wrap:break-word; word-break: break-all;"   align="center">
						<? 
						$tot_smv=0;
						$tot_smv=$month_smv_arr[$mon_key]/$tot_mon_qty[$mon_key];
						
						$head_td_data=$tot_mon_qty[$mon_key].'$'.$tot_mon_amount[$mon_key].'$'.$tot_smv;
						echo $head_td_data;
						?>
						</td>
						<?
					}
            ?>
                    </tr>
				</table>
			</fieldset>
		</div>
         <style>
			@font-face {
   				 font-family:"Arial Narrow"; font-size:11px;
			}
			TD{font-family:"Arial Narrow";font-size:10px;}
			TH{font-family:"Arial Narrow";font-size:11px;}
			
			.rpt_table TD{font-family: "Arial Narrow";font-size:11px;}
			.rpt_table TH{font-family:"Arial Narrow";font-size:11px;}
		</style>
	<?
	}
//===========================================================================================================================================================
	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data****$filename****$tot_rows****$cbo_search_type";
	exit();	
	
}
else if($action=="report_generate_tod") //Prod Code Wise
{
 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company_name=str_replace("'","",$cbo_company_name);
	$cbo_search_type=str_replace("'","",$cbo_search_type);
	$cbo_bh_mer_name=str_replace("'","",$cbo_bh_mer_name);
	$txt_yarn_count=str_replace("'","",$txt_yarn_count);
	if($txt_yarn_count!='') $yarn_count_con="and yarn_count Like '%$txt_yarn_count%' ";else $yarn_count_con='';
	$buyer_id_cond="";
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{
				$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else
			{
				$buyer_id_cond="";
			}
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_job_no=trim($txt_job_no);
	if($txt_job_no !="" || $txt_job_no !=0)
	{
		//$year = substr(str_replace("'","",$cbo_year_selection), -2); 
		//$job_no=$company_library[$company_name]."-".$year."-".str_pad($txt_job_no, 5, 0, STR_PAD_LEFT);
		$jobcond="and a.job_no_prefix_num in(".$txt_job_no.")";
	}
	else
	{
		$jobcond="";	
	}
	
	
  	$date_cond='';
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		$start_date=(str_replace("'","",$txt_date_from));
		$end_date=(str_replace("'","",$txt_date_to));
		
		$date_cond="and b.pub_shipment_date between '$start_date' and '$end_date'";
	}
	//if (str_replace("'","",$txt_job_no)=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in (".str_replace("'","",$txt_job_no).") ";
	
	if(str_replace("'","",$txt_style_ref)!="") $style_ref_cond=" and a.style_ref_no like '%".str_replace("'","",$txt_style_ref)."%'"; else $style_ref_cond="";
	if(str_replace("'","",$txt_order_no)!="")
	{
		$ordercond=" and b.po_number like '%".str_replace("'","",$txt_order_no)."%'"; 
	}
	else
	{
		$ordercond="";
	}
	
	$team_cond="";
	if(str_replace("'","",$cbo_team_name)!=0) $team_cond=" and a.team_leader=".str_replace("'","",$cbo_team_name)."  ";
	if(str_replace("'","",$cbo_team_member)!=0) $team_cond.=" and a.dealing_marchant=".str_replace("'","",$cbo_team_member)."  ";
	$cbo_bh_mer_name=str_replace("0","",$cbo_bh_mer_name);
	if($cbo_bh_mer_name!=0 || $cbo_bh_mer_name!='') $bh_mer_name_con="and a.bh_merchant='$cbo_bh_mer_name' ";else $bh_mer_name_con='';
	
	 $cbo_year_selection=str_replace("'","",$cbo_year_selection);
	
	$start_ddd= strtotime(change_date_format($start_date,"dd-mm-yyyy","-"));
	$start_ddd=date("Y-m-d",$start_ddd);
	$start_end_date= strtotime(change_date_format($end_date,"dd-mm-yyyy","-"));
	$start_end_date=date("Y-m-d",$start_end_date);
	 //$dd= date('d-m-Y',strtotime($start_date));
	//$start_date_week= date($dd, time() - 1296000);//$start_date;
	//echo $last_day_this_month  = date($start_date_week, time() -1814400); //date('t-m-Y');
	
	$dateee = strtotime($start_ddd);
	$start_end_date = strtotime($start_end_date);
	
	$date_start = date("Y-m-d",strtotime("-30 day", $dateee));
	$date_end = date("Y-m-d",strtotime("+30 day", $start_end_date));
	//echo $date_end;
	//$end_date_week=  addday("$end_date", time() -1814400);//$start_date;//$start_date;
	if($db_type==0)
	{
		$date_start= change_date_format($date_start,"yyyy-mm-dd");
		$date_end= change_date_format($date_end,"yyyy-mm-dd");
	}
	else
	{
		$date_start= change_date_format($date_start,"dd-mm-yyyy","-",1);
		$date_end= change_date_format($date_end,"dd-mm-yyyy","-",1);
	}
	
	
	//echo "select week_date,week from week_of_year where week_date between '$date_start' and  '$date_end' order by week_date asc";die;
	// date("d-m-Y", time() - 1296000);
	$week_for_arr=array();$no_of_week_for_header=array();
	$sql_week_header=sql_select("select week_date,week from week_of_year where week_date between '$date_start' and  '$date_end' order by week_date asc");
	$week_check_head=array();
	foreach ($sql_week_header as $row_week_header)
	{
		if($week_check_head[$row_week_header[csf("week")]]=='')
		{
			$week_counter_header[date("Y-M",strtotime($row_week_header[csf("week_date")]))][$row_week_header[csf("week")]]=$row_week_header[csf("week")];
			$week_check_head[$row_week_header[csf("week")]]=$week_counter_header[date("Y-M",strtotime($row_week_header[csf("week_date")]))][$row_week_header[csf("week")]];
		}
		$tmp=add_date($row_week_header[csf("week_date")],-1);
		if($db_type==0) $tmp_cond=date("d-m-y",strtotime($tmp));
		else $tmp_cond=date("d-M-y",strtotime($tmp));
		$no_of_week_for_header_calc[$tmp_cond]=$row_week_header[csf("week")];	
		$no_of_week_for_header[$row_week_header[csf("week_date")]]=$row_week_header[csf("week")];
		$test[$row_week_header[csf("week")]]=$row_week_header[csf("week")];
	}
	unset($sql_week_header);
	$week_start_day=array();
	$week_end_day=array();$week_day_arr=array();
	//week_day
	$sql_week_start_end_date=sql_select("select week,min(week_date) as week_start_day, Max(week_date) as week_end_day from week_of_year where week_date between '$date_start' and  '$date_end' group by week");
	foreach ($sql_week_start_end_date as $row_week)
	{
		$week_start_day[$row_week[csf("week")]][week_start_day]=$row_week[csf("week_start_day")];
		$week_end_day[$row_week[csf("week")]][week_end_day]=$row_week[csf("week_end_day")];
		//$week_day_arr[$row_week[csf("week")]][$row_week[csf("week_start_day")]][week_first_day]=$row_week[csf("min_week_day")];
		$week_day_arr[$row_week[csf("week_end_day")]]['week_last_day']=$row_week[csf("last_week_day")];
	}
	unset($sql_week_start_end_date);
	//print_r($week_day_arr);
	function distribute_projection( $base_week, $qnty)
	{
		//5. Distribution ratios are: Base week 52%, Immediate before 24%, Week before immediate week 20%, Immediate week after base week 2%, next 2 weeks as 1%
		global $test;
		 
		$tmpbase_week=$test[$base_week-3];
		$k=0;
		for($i=0; $i<6; $i++)
		{
			$tmpbase_week++;
			if( $test[$tmpbase_week]=='') { $tmpbase_week=1; $k=0; }
			
			
			if($i==0) $distr_arr[$tmpbase_week]=($qnty*20)/100;
			if($i==1) $distr_arr[$tmpbase_week]=($qnty*24)/100;
			if($i==2) $distr_arr[$tmpbase_week]=($qnty*52)/100;
			if($i==3) $distr_arr[$tmpbase_week]=($qnty*2)/100;
			if($i==4) $distr_arr[$tmpbase_week]=($qnty*1)/100;
			if($i==5) $distr_arr[$tmpbase_week]=($qnty*1)/100;
			$k++;
			
		}
		 
		return $distr_arr;
		/*5000
		b=52
		b-1=21
		b-2=15
		b+1=1
		b+2=1*/
	}
	//echo "<per>";
	 //print_r(distribute_projection("2016-Dec",51, 5000)); die;
	

	
	if($template==1)
	{
	$po_wise_buyer=array();
	$buyer_wise_data=array();
	$po_id_arr=array();
	$week_wise_order_qty_arr=array();	
	//echo "select yarn_count,id from  lib_yarn_count  where status_active=1 and is_deleted=0 and yarn_count is not null $yarn_count_con";
	$count_data=sql_select("select yarn_count,id from  lib_yarn_count  where status_active=1 and is_deleted=0 and yarn_count is not null $yarn_count_con");
	$yarn_id='';
	foreach($count_data as $row)
	{
			if($yarn_id=='') $yarn_id=$row[csf('id')];else $yarn_id.=",".$row[csf('id')];
	}
	$yarn_ids=count(array_unique(explode(",",$yarn_id)));
	$yarn_numIds=chop($yarn_id,','); $yarnIds_cond="";
		if($yarn_id!='' || $yarn_id!=0)
		{
			if($db_type==2 && $yarn_ids>1000)
			{
				$yarnIds_cond=" and (";
				$yIdsArr=array_chunk(explode(",",$yarn_numIds),990);
				foreach($yIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$yarnIds_cond.=" d.count_id  in($ids) or ";
				}
				$yarnIds_cond=chop($yarnIds_cond,'or ');
				$yarnIds_cond.=")";
			}
			else
			{
				$yarnIds_cond=" and  d.count_id  in($yarn_id)";
			}
		}
		
		$yarn_sql_data=("select min(c.country_ship_date) as country_ship_date,b.is_confirmed,b.id as po_id,d.count_id,d.copm_one_id,d.copm_two_id,d.percent_one,d.percent_two,sum(d.cons_qnty) as cons_qnty,e.construction,e.gsm_weight from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fab_yarn_cost_dtls d,wo_pre_cost_fabric_cost_dtls e where a.job_no=b.job_no_mst  and c.job_no_mst=a.job_no and b.id=c.po_break_down_id and d.job_no=a.job_no and 
	e.id=d.fabric_cost_dtls_id and  c.item_number_id= e.item_number_id and e.job_no=a.job_no and e.body_part_id in(1,20) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1   and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $yarnIds_cond $team_cond  group by   b.id ,d.count_id,d.copm_one_id,d.copm_two_id,d.percent_one,d.percent_two,e.construction,e.gsm_weight,b.is_confirmed"); 
	$sql_result_y=sql_select($yarn_sql_data);
	$wo_fab_data_arr=array();
	$all_yarn_po_id="";
	foreach($sql_result_y as $row)
		{
			if($all_yarn_po_id=="") $all_yarn_po_id=$row[csf("po_id")]; else $all_yarn_po_id.=",".$row[csf("po_id")];
			if($row[csf('percent_two')]!=0) $row[csf('percent_two')]=$row[csf('percent_two')];else $row[csf('percent_two')]='';
			$percent_one=$row[csf('percent_one')].'**'.$row[csf('percent_two')];
			
			if($row[csf('copm_two_id')]!=0) $row[csf('copm_two_id')]=$row[csf('copm_two_id')];else $row[csf('copm_two_id')]='';
			$copm_one_id=$row[csf('copm_one_id')].'**'.$row[csf('copm_two_id')];
			
			//$prod_code=$row[csf('product_code')];
			$tod_week=$no_of_week_for_header[$row[csf('country_ship_date')]];
			$wo_fab_data_arr[$tod_week][$row[csf('is_confirmed')]]['percent_one']=$percent_one;
			//$wo_fab_data_arr[$prod_code][$row[csf('is_confirmed')]]['percent_two']=$row[csf('percent_two')];
			$wo_fab_data_arr[$tod_week][$row[csf('is_confirmed')]]['copm_two_id']=$copm_one_id;
			//$wo_fab_data_arr[$prod_code][$row[csf('is_confirmed')]]['copm_one_id']=$row[csf('copm_one_id')];
			$wo_fab_data_arr[$tod_week][$row[csf('is_confirmed')]]['construction']=$row[csf('construction')];
			$wo_fab_data_arr[$tod_week][$row[csf('is_confirmed')]]['gsm_weight']=$row[csf('gsm_weight')];
			$wo_fab_count_data_arr[$tod_week][$row[csf('is_confirmed')]]['count_id'].=$row[csf('count_id')].",";
			//$wo_yarn_data_arr[$row[csf('po_id')]]['cons_qnty']+=$row[csf('cons_qnty')];
		}
		//echo $all_yarn_po_id;
	unset($sql_result_y);
	$yarn_po_ids=count(array_unique(explode(",",$all_yarn_po_id)));
		$yarnPo_numIds=chop($all_yarn_po_id,','); $yarnPoIds_cond="";
		if($txt_yarn_count!='')
		{
			if($all_yarn_po_id!='' || $all_yarn_po_id!=0)
			{
				if($db_type==2 && $yarn_po_ids>1000)
				{
					$yarnPoIds_cond=" and (";
					$yPoIdsArr=array_chunk(explode(",",$yarnPo_numIds),990);
					foreach($yPoIdsArr as $ids)
					{
						$ids=implode(",",$ids);
						$yarnPoIds_cond.=" b.id  in($ids) or ";
					}
					$yarnPoIds_cond=chop($yarnPoIds_cond,'or ');
					$yarnPoIds_cond.=")";
				}
				else
				{
					$yarnPoIds_cond=" and  b.id  in($all_yarn_po_id)";
				}
			}
		}
		
	 $tod_wise_arr_qty=array();
	$sql_data_tod=("select b.id as po_id,b.shipment_date as orgi_ship_date,b.is_confirmed,(b.po_quantity*a.total_set_qnty) as po_qty_pcs
		from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $team_cond $bh_mer_name_con order by b.shipment_date ");
		$sql_result_tod=sql_select($sql_data_tod);
		foreach($sql_result_tod as $row)
		{
			if($row[csf('is_confirmed')]==2)
			{
				$tod_week=$no_of_week_for_header[$row[csf('orgi_ship_date')]];
				$tod_wise_arr_qty[$tod_week][$row[csf('is_confirmed')]][$tod_week]['po_qty']+=$row[csf('po_qty_pcs')];
			}
		}	
		
	if($db_type==0) $date_dif="DATEDIFF(b.shipment_date, b.po_received_date) as lead_time_days";
else  $date_dif="(b.shipment_date-b.po_received_date) as lead_time_days";
	$sql_query=("select $date_dif,a.job_no,a.job_no_prefix_num as prefix_job_no,a.set_smv,a.season_buyer_wise  as 	season,a.product_code,a.dealing_marchant,a.bh_merchant,a.style_ref_no,b.id,b.is_confirmed,b.po_number,b.po_received_date,b.shipment_date as orgi_ship_date,a.total_set_qnty as set_ratio,(b.po_quantity*a.total_set_qnty) as po_qty_pcs,b.po_total_price as po_total_price,a.avg_unit_price as unit_price ,c.country_ship_date,c.order_quantity,c.order_total
	from wo_po_details_master a, wo_po_break_down b
	LEFT JOIN wo_po_color_size_breakdown c on  c.po_break_down_id=b.id  and c.status_active=1
	 where a.job_no=b.job_no_mst   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1   and a.company_name=$company_name  $buyer_id_cond $date_cond $style_ref_cond $bh_mer_name_con $jobcond $ordercond $yarnPoIds_cond $team_cond  order by b.shipment_date,a.job_no,b.is_confirmed ");
	  
	
					 
	$sql_data=sql_select($sql_query);
	$tot_rows=count($sql_data);
	$all_po_id="";
	$marchant_wise_arr=array();
	foreach( $sql_data as $row)
	{
		$tod_week=$no_of_week_for_header[$row[csf('orgi_ship_date')]];
		if($all_po_id=="") $all_po_id=$row[csf("id")]; else $all_po_id.=",".$row[csf("id")];
		if($db_type==0) $c_date_con=date("d-m-y",strtotime($row[csf('country_ship_date')]));
			else $c_date_con=date("d-M-y",strtotime($row[csf('country_ship_date')]));
			$c_date_week=$no_of_week_for_header_calc[$c_date_con];
			if($row[csf('is_confirmed')]==1)
			{
				$order_val=$row[csf('order_total')];
			}
			else
			{
				$order_val=$row[csf('po_total_price')];
			}
			
		
		//$c_date_week=$no_of_week_for_header[$row[csf('country_ship_date')]];
		$tod_wise_arr[$tod_week][$row[csf('is_confirmed')]]['po_qty']+=$row[csf('po_qty_pcs')];
		$tod_wise_arr[$tod_week][$row[csf('is_confirmed')]]['po_val']+=$order_val;
		$tod_wise_arr[$tod_week][$row[csf('is_confirmed')]]['set_smv']+=$row[csf('set_smv')];
		$tod_wise_arr[$tod_week][$row[csf('is_confirmed')]]['set_ratio']+=$row[csf('set_ratio')];
		$tod_wise_arr[$tod_week][$row[csf('is_confirmed')]]['lead_day']+=$row[csf('lead_time_days')];
		$tod_wise_arr[$tod_week][$row[csf('is_confirmed')]]['unit_price']+=$row[csf('unit_price')];
		$tod_wise_arr[$tod_week][$row[csf('is_confirmed')]]['job_no'].=$row[csf('prefix_job_no')].',';
		$tod_wise_arr[$tod_week][$row[csf('is_confirmed')]]['style'].=$row[csf('style_ref_no')].',';
		$tod_wise_arr[$tod_week][$row[csf('is_confirmed')]]['season'].=$row[csf('season')].',';

		$tod_wise_arr[$tod_week][$row[csf('is_confirmed')]]['dealing_marchant'].=$row[csf('dealing_marchant')].',';
		$tod_wise_arr[$tod_week][$row[csf('is_confirmed')]]['bh_mer'].=$row[csf('bh_merchant')].',';
		$tod_wise_arr[$tod_week][$row[csf('is_confirmed')]]['po_no'].=$row[csf('po_number')].',';
		$tod_wise_arr[$tod_week][$row[csf('is_confirmed')]]['po_id'].=$row[csf('id')].',';
		$tod_wise_arr[$tod_week][$row[csf('is_confirmed')]]['po_received_date'].=$row[csf('po_received_date')].',';
		$tod_wise_arr[$tod_week][$row[csf('is_confirmed')]]['orgi_ship_date'].=$row[csf('orgi_ship_date')].',';
		$tod_wise_arr[$tod_week][$row[csf('is_confirmed')]]['c_ship_date'].=$row[csf('country_ship_date')].',';
		if($row[csf('is_confirmed')]==1)
		{
		$week_wise_order_qty[$tod_week][$row[csf('is_confirmed')]][$c_date_week]['po_quantity']+=$row[csf("order_quantity")];
		}
		
	}
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
					$poIds_cond.=" b.id  in($ids) or ";
				}
				$poIds_cond=chop($poIds_cond,'or ');
				$poIds_cond.=")";
			}
			else
			{
				$poIds_cond=" and  b.id  in($all_po_id)";
			}
		}
		$exfactory_data_array=array();
		$exfactory_sql=("select b.shipment_date as shipment_date,b.is_confirmed,
		sum(CASE WHEN f.entry_form!=85 THEN f.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		sum(CASE WHEN f.entry_form=85 THEN f.ex_factory_qnty ELSE 0 END) as return_qnty
		 from wo_po_details_master a, wo_po_break_down b,pro_ex_factory_mst f,wo_po_color_size_breakdown c  where  a.job_no=b.job_no_mst and  f.po_break_down_id=b.id  and c.job_no_mst=a.job_no and b.id=c.po_break_down_id  and f.po_break_down_id=c.po_break_down_id and  a.company_name=$company_name  and f.status_active=1 and f.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $team_cond group by  b.shipment_date, b.is_confirmed");
		$exfactory_data=sql_select($exfactory_sql);
		foreach($exfactory_data as $row)
		{
				$tod_week=$no_of_week_for_header[$row[csf('shipment_date')]];
				//$prod_code=$row[csf('country_ship_date')];
				$exfactory_data_array[$tod_week][$row[csf('is_confirmed')]][ex_qnty]+=$row[csf('ex_factory_qnty')]-$row[csf('return_qnty')];
		}
		unset($exfactory_data);
		 
		ob_start();
		$rowcount=0;
		foreach($week_counter_header as $mon_key=>$week)
		{
			$rowcount+=count($week);
		}
		 $rowb=$rowcount*2;
		$td_width=1780+($rowcount*45)+$rowb;
		$tot_month = datediff( 'm', $date_start,$date_end);
		for($i=0; $i<= $tot_month; $i++ )
		{
			$next_month=month_add($date_start,$i);
			$month_arr.=date("Y-M",strtotime($next_month)).',';
		}
		$month_arr_id=rtrim($month_arr,',');
	if($tot_rows>0)
	{
	?>
		
         <script>
			var mon_week ='<? echo $month_arr_id; ?>';
			var mon_week=mon_week.split(",");
		for (var k=0; k<mon_week.length; k++)
		{
			var mon_data=document.getElementById('summary_footer_td_'+mon_week[k]).innerHTML;
			var mon_arr=mon_data.split("$");
			var mon_qnty=number_format(mon_arr[0],0);
			var mon_amt=number_format(mon_arr[1],0);
			var order_smv=number_format(mon_arr[2],2);
			var avg_rate=(number_format(mon_arr[1],0,'.','')/number_format(mon_arr[0],0,'.',''));
			//alert(mon_amt);
			document.getElementById('summary_header_td_qty_'+mon_week[k]).innerHTML=mon_qnty;
			document.getElementById('summary_header_td_val_'+mon_week[k]).innerHTML='$'+mon_amt;
			document.getElementById('summary_header_td_rate_'+mon_week[k]).innerHTML='FOB:'+number_format(avg_rate,2);
			document.getElementById('summary_header_td_smv_'+mon_week[k]).innerHTML='SMV:'+number_format(order_smv,2);
		}
		var yarn_req_qty=document.getElementById('td_yarn_req_march').innerHTML;
		var yarnreq_qty=number_format(yarn_req_qty,0);
		document.getElementById('yarn_summary_tot').innerHTML=yarnreq_qty;
		</script>
        <?
	 }
		?>
		<div style="width:<? echo $td_width+20;?>px">
		<fieldset style="width:100%;">	
      
        
			<table width="<? echo $td_width+20;?>px">
				<tr class="form_caption">
					<td align="left" style="margin-left:10px" colspan="<? echo $rowcount+25; ?>">TOD Wise</td>
				</tr>
				<tr class="form_caption" align="center" style="display:none">
					<td colspan="<? echo $rowcount+26; ?>" align="center"><? //echo $company_library[$company_name]; ?></td>
				</tr>
			</table>
			<table class="rpt_table" width="<? echo $td_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
                  <tr>
                   	 <th  width="" colspan="11" rowspan="2">&nbsp;</th>
                     <th  rowspan="2" id="yarn_summary_tot"></th>
                     <th  width="" colspan="14" rowspan="2">&nbsp;</th>
                     <?
					foreach($week_counter_header as $mon_key=>$week)
					{
					?>
					<th   align="center" colspan="<? echo count($week);?>">
					<? 
				
					echo $mon_key;
					?>
					</th>
					<?
					}
            ?>
                   
                </tr>
                  <tr>
					
                     <?
					//foreach($month_arr as $mon_key)
				
					foreach($week_counter_header as $mon_key=>$week_val)
					{
					?>
					<th width="60"  style="font-size:0.875em;"  align="center" colspan="<? echo count($week_val)?>">
					 		   <b id="summary_header_td_qty_<? echo $mon_key ;?>"  style="text-align:left;font-size: 0.775em;"> </b> &nbsp;
                               <b id="summary_header_td_val_<? echo $mon_key ;?>" style="text-align: right;font-size: 0.775em;"> </b> &nbsp;
                               <b id="summary_header_td_rate_<? echo $mon_key ;?>"  style="text-align: right;font-size: 0.775em;"></b> &nbsp;
                               <b title="Avg Smv" id="summary_header_td_smv_<? echo $mon_key ;?>"  style="text-align: right;font-size: 0.775em;"></b>
					</th>
					<?
					}
            ?>
                   
                </tr>
                <tr>
					<th width="30">SL</th>
                    <th width="55">Season</th>
                    <th width="80">BH Mer.</th>
					<th width="70">Merchant</th>
                    <th width="55">Job No</th>
					<th width="120">Style Ref</th>
                    <th width="60">Comp%</th>
                    <th width="120">Comp Name</th>
                    <th width="120">Const.</th>
                    <th width="50">GSM</th>
					<th width="120">Y/C</th>
                    <th width="70">Yarn Qty</th>
					<th width="60">Status</th>
					<th width="45">OPD</th>
					<th width="50">OPD Date</th>
					<th width="45">TOD</th>
                    <th width="70">Ord Qty</th>
					<th width="100">Order No</th>
                    <th width="40">Dept </th>
					<th width="40">Lead Time</th>
					<th width="40">Unit Price</th>
					<th width="90">Value</th>
					<th width="40">SMV/ Pcs</th>
                    <th width="90">Ord.SMV</th>
                    <th width="60">Delv Qty</th>
                    <th width="60">Delv.Val</th>
                     <?
					//foreach($week_for_header as $week_key)
					foreach($week_counter_header as $mon_key=>$week)
					{
						foreach($week as $week_key)
						{
							$start_week_date=$week_start_day[$week_key][week_start_day];
							$end_week_date=$week_end_day[$week_key][week_end_day];
							//$fist_day=$week_day_arr[$week_key][$start_week_date][week_first_day];
							//$last_day=$week_day_arr[$week_key][$end_week_date][week_last_day];
							//.'('.$fist_day.')'
							if($db_type==2)
							{
								$fisrtday=date("D",strtotime(change_date_format($start_week_date,"dd-mm-yyyy","-")));
								$lastday=date("D",strtotime(change_date_format($end_week_date,"dd-mm-yyyy","-")));
							}
							else
							{
								$fisrtday=date("D",strtotime(change_date_format($start_week_date,"dd-mm-yyyy")));
								$lastday=date("D",strtotime(change_date_format($end_week_date,"dd-mm-yyyy")));
							}
							$weekstart_date=explode("-",date("d-M",strtotime($start_week_date)));
							$weekstart_date=$weekstart_date[0].'/'.$weekstart_date[1];
						?>
						<th title="<? echo change_date_format($start_week_date,"dd-mm-yyyy","-").'('.$fisrtday.')'." To ".change_date_format($end_week_date,"dd-mm-yyyy","-").'('.$lastday.')'; ?>" width="45"  align="center">
						
						<? 
						echo 'W'.$week_key.'<br/>'.$weekstart_date;
						?>
						</th>
						<?
						}
					}
            ?>
                   
                </tr>    
				</thead>
			</table>
			<div style="width:<? echo $td_width+20;?>px; max-height:245px; overflow-y:scroll;overflow-x:hidden;" id="scroll_body">
			<table class="rpt_table" width="<? echo $td_width;?>px" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body_tod">
	<?
	
               
			$condition= new condition();
			 $condition->company_name("=$cbo_company_name");
			 if(str_replace("'","",$cbo_buyer_name)>0)
			 {
				  $condition->buyer_name("=$cbo_buyer_name");
			 }
			 if(str_replace("'","",$txt_job_no) !='')
			 {
				  $condition->job_no_prefix_num("in($txt_job_no)");
			 }
			
			 if(str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
			 {
			 $condition->pub_shipment_date(" between '$start_date' and '$end_date'");
			 }
				
			 if(str_replace("'","",$txt_order_no)!='')
			 {
				$condition->po_number("=$txt_order_no"); 
			 }
			 if(str_replace("'","",$txt_season)!='')
			 {
				//$condition->season("=$txt_season"); 
			 }
			 $condition->init();
			 
			 	
        	 $yarn= new yarn($condition);
		  //echo $yarn->getQuery(); die;
			 $yarn_req_qty_arr=$yarn->getOrderWiseYarnQtyArray();
			//print_r($yarn_req_qty_arr);
				$i=1;$total_order_qty=0;$total_order_value=0;$total_yarn_req_qty=0;$total_order_smv=0;$total_ex_fact_val=0;$total_ex_fact_qty=0;
				foreach($tod_wise_arr as $tod_key=>$tod_data)
				{  
				   	foreach($tod_data as $status_key=>$status_val)
					{
						  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";  
						//  echo $opd_key.'=T'.'<br/>'; 
				$po_received_date=$status_val['po_received_date'];
				 $conf_country_ship_date=rtrim($wo_color_data_arr2[$tod_key][$status_key]['shipdate'],',');
				$conf_country_ship_date=explode(",",$conf_country_ship_date);
				
				$percent_one=$wo_fab_data_arr[$tod_key][$row[csf('is_confirmed')]]['percent_one'];//rtrim($status_val['percent'],',');
				$construction=rtrim($status_val['const'],',');
				$gsm_weight=rtrim($status_val['gsm'],',');
				$comp=$wo_fab_data_arr[$tod_key][$row[csf('is_confirmed')]]['copm_two_id'];//rtrim($status_val['comp'],',');
				$gsm_weight=implode(",",array_unique(explode(",",$gsm_weight)));
				$construction=implode(",",array_unique(explode(",",$construction)));
				
				$percent_data=array_unique(explode(",",$percent_one));
				$comp_data=array_unique(explode(",",$comp));
				
				$percent_one_cond="";$percent_two_cond="";
				foreach($percent_data as $perid)
				{
						$percent_data=array_unique(explode("**",$perid));
						$percent_one=$percent_data[0];
						$percent_two=$percent_data[1];
					if($percent_one_cond=="") $percent_one_cond=$percent_one;else $percent_one_cond.=",".$percent_one;
					if($percent_two_cond=="") $percent_two_cond=$percent_two;else $percent_two_cond.=",".$percent_two;
				}
				if($percent_one_cond!='' && $percent_two_cond!='')
					{
						//if($percent_two!=0)
						$percent_all=$percent_one_cond.'/'.$percent_two_cond;
					}
					else if($percent_one_cond!='' && $percent_two_cond=='')
					{
						//if($percent_two!=0)
						$percent_all=$percent_one_cond;
					}
					else if($percent_one_cond=='' && $percent_two_cond!='')
					{
						//if($percent_two!=0)
						$percent_all=$percent_two_cond;
					}
					else
					{
						$percent_all='';	
					}
				$comp_one_cond="";$comp_two_cond="";
				foreach($comp_data as $compid)
				{
						$comp_data=array_unique(explode("**",$compid));
						$comp_one=$comp_data[0];
						$comp_two=$comp_data[1];
						
					if($comp_one_cond=="") $comp_one_cond=$composition[$comp_one];else $comp_one_cond.=",".$composition[$comp_one];
					if($comp_two_cond=="") $comp_two_cond=$composition[$comp_two];else $comp_two_cond.=",".$composition[$comp_two];
				}
				
				if($comp_one_cond!='' && $comp_two_cond!='')
				{ 
					$copm_one_name=$comp_one_cond.'/'.$comp_two_cond;
				}
				else if($comp_one_cond!='' && $comp_two_cond=='')
				{ 
					$copm_one_name=$comp_one_cond;
				
				}
				else if($comp_one_cond=='' && $comp_two_cond!='')
				{ 
					$copm_one_name=$comp_two_cond;
				
				}
				else 
				{
					$copm_one_name='';
				}
				
				$construction=$wo_fab_data_arr[$tod_key][$status_key]['construction'];
				$gsm_weight=$wo_fab_data_arr[$tod_key][$status_key]['gsm_weight'];
				$count_id=$wo_fab_count_data_arr[$tod_key][$status_key]['count_id'];
				$count_id=rtrim($count_id,',');
				 $count_ids=array_unique(explode(",", $count_id));
				 $yarn_count_value="";
				foreach($count_ids as $val)
				{
					//if($val>0)
					//{
						if($yarn_count_value=="") $yarn_count_value=$lib_yarn_count_arr[$val]; else $yarn_count_value.=", ".$lib_yarn_count_arr[$val];
					//}
				}
				$seasion_id=rtrim($status_val['season'],',');
				$job_no=rtrim($status_val['job_no'],',');
				$job_no=implode(",",array_unique(explode(",",$job_no)));
				$style=rtrim($status_val['style'],',');
				$po_no=rtrim($status_val['po_no'],',');
				$po_id=rtrim($status_val['po_id'],',');
			
				$dealing_marchant=rtrim($status_val['dealing_marchant'],',');
				$bh_mer=rtrim($status_val['bh_mer'],',');
				$bh_mer=implode(",",array_unique(explode(",",$bh_mer)));
				$po_received_date=rtrim($status_val['po_received_date'],',');
				$c_ship_date=rtrim($status_val['c_ship_date'],',');
				$job_no=implode(",",array_unique(explode(",",$job_no)));
				$style=implode(",",array_unique(explode(",",$style)));
				$po_no=implode(",",array_unique(explode(",",$po_no)));
				$po_id=array_unique(explode(",",$po_id));
			
				$yarn_req=0;
				foreach($po_id as $pid)
				{
				$yarn_req+=$yarn_req_qty_arr[$pid];
				}
				//echo $yarn_req.'<br>';
				
				$seasion_cond=implode(",",array_unique(explode(",",$seasion_id)));
				/*$seasion_cond="";
				foreach($seasion_id as $sid)
				{
					if($seasion_cond=="") $seasion_cond=$lib_season_name_arr[$sid];else $seasion_cond.=",".$lib_season_name_arr[$sid];
				}*/
				$dealing_marchant=array_unique(explode(",",$dealing_marchant));
				$dealing_marchant_con="";
				foreach($dealing_marchant as $mid)
				{
					if($dealing_marchant_con=="") $dealing_marchant_con=$team_member_arr[$mid];else $dealing_marchant_con.=",".$team_member_arr[$mid];
				}
				$po_received_date=array_unique(explode(",",$po_received_date));
				$opd_week="";$opd_recv_date="";$opd_recv_date_full="";
				//$lead_time=0;
				foreach($po_received_date as $op_id)
				{
					if($opd_week=="") $opd_week=$no_of_week_for_header[$op_id];else $opd_week.=", ".$no_of_week_for_header[$op_id];
					if($opd_recv_date=="") $opd_recv_date=date("d-M",strtotime($op_id));else $opd_recv_date.=",".date("d-M",strtotime($op_id));
					if($opd_recv_date_full=="") $opd_recv_date_full=date("d-M-y",strtotime($op_id));else $opd_recv_date_full.=",".date("d-M-y",strtotime($op_id));
					//$lead_time = datediff( 'd', $op_id,$countryship_date);
				}
				$po_orgi_shipdate=rtrim($status_val['orgi_ship_date'],',');
				$po_orgi_shipdate=array_unique(explode(",",$po_orgi_shipdate));
				/*$tod_weeks="";$tod_recv_date="";
				foreach($po_orgi_shipdate as $orgi_id)
				{
					//echo $orgi_id;
					//if($tod_weeks=="") $tod_weeks=$no_of_week_for_header[$orgi_id];else $tod_weeks.=",".$no_of_week_for_header[$orgi_id];
				}*/
				$lead_time=$status_val['lead_day'];
				$opd_week=implode(",",array_unique(explode(",",$opd_week)));
				$opd_recv_date=implode(",",array_unique(explode(", ",$opd_recv_date)));
				
				if($status_key==1) //Confirm $conf_country_ship_date
				{
					$c_ship_date_con=array_unique(explode(",",$c_ship_date));$qnty_array_dd=array();
					$c_week_qty=0;
					foreach($c_ship_date_con as $c_date)
					{
								if($db_type==0) $c_date_con=date("d-m-y",strtotime($c_date));
								else $c_date_con=date("d-M-y",strtotime($c_date));
								$c_week=$no_of_week_for_header_calc[$c_date_con];
								$c_week_qty=$week_wise_order_qty[$tod_key][$status_key][$c_week]['po_quantity'];
										//echo $c_week.'=='.$c_date.'hj';
								$qnty_array_dd[$c_week]=$c_week_qty;
					}
					//print_r($qnty_array_dd);
						$tot_order_qty=0;$tot_order_qty_arr=array();
						foreach($week_counter_header as $mon_key=>$week)
						{
							foreach($week as $week_key)
							{
								 $tot_order_qty=$qnty_array_dd[$week_key];
								 $order_qty_arr[$week_key]=$tot_order_qty;
								  $tot_order_qty_arr[$tod_key]+=$tot_order_qty;
							}
						}
						
				}
				else 
				{
						$po_orgi_shipdate=rtrim($status_val['orgi_ship_date'],',');
						$proj_orgi_shipdate=array_unique(explode(",",$po_orgi_shipdate));
						$qnty_array_mm=array();//$proj_po_qty=0;
						 $proj_po_qty=$tod_wise_arr_qty[$tod_key][$status_key][$tod_key]['po_qty'];
							$qnty_array_mm=distribute_projection($tod_key, $proj_po_qty);
							$order_qty=0;$tot_order_qty_arr=array();
							foreach($week_counter_header as $mon_key=>$week)
							{
								foreach($week as $week_key)
								{
									$order_qty=$qnty_array_mm[$week_key];
									$order_qty_arr[$week_key]=$order_qty;
									$tot_order_qty_arr[$tod_key]+=$order_qty;
								}
							}
				}
				$tot_po_qty=$tot_order_qty_arr[$tod_key];
				
				$po_value=$status_val['po_val'];
				$unit_price=$po_value/$tot_po_qty;
				
				$po_orgi_shipdatess=rtrim($status_val['orgi_ship_date'],',');
				$job_no_row=count(array_unique(explode(",",$job_no)));
				if($job_no_row>1)
				{
					 $view_button="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$po_orgi_shipdatess."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button=$job_no;	
				}
				
				/*$tod_weeks_row=count(array_unique(explode(",",$tod_weeks)));
				if($tod_weeks_row>1)
				{
					 $view_button_tod="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$po_orgi_shipdatess."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_tod=$tod_weeks;	
				}
				*/
				$opd_week_row=count(array_unique(explode(",",$opd_week)));
				if($opd_week_row>1)
				{
					 $view_button_opd="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$po_orgi_shipdatess."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_opd=$opd_week;	
				}
				$opd_recv_date_row=count(array_unique(explode(",",$opd_recv_date)));
				if($opd_recv_date_row>1)
				{
					 $view_button_opd_date="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$po_orgi_shipdatess."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_opd_date=$opd_recv_date;	
				}
				$po_no_row=count(array_unique(explode(",",$po_no)));
				if($po_no_row>1)
				{
					 $view_button_po="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$po_orgi_shipdatess."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_po=$po_no;	
				}
				$dealing_marchant_con_row=count(array_unique(explode(",",$dealing_marchant_con)));
				$bh_mer_con_row=count(array_unique(explode(",",$bh_mer)));
				$seasion_cond_row=count(array_unique(explode(",",$seasion_cond)));
				$construction_cond_row=count(array_unique(explode(",",$construction)));
				$gsm_weight_cond_row=count(array_unique(explode(",",$gsm_weight)));
				$yarn_count_value_cond_row=count(array_unique(explode(",",$yarn_count_value)));
				$style_cond_row=count(array_unique(explode(",",$style)));
				$copm_cond_row=count(array_unique(explode(",",$copm_one_name)));
				
				if($dealing_marchant_con_row>1)
				{
					 $view_button_deal="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$po_orgi_shipdatess."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_deal=$dealing_marchant_con;
				}
				if($bh_mer_con_row>1)
				{
					 $view_button_bh="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$po_orgi_shipdatess."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					
					$view_button_bh=$bh_mer;
					//$view_button_season=$seasion_cond;	
				}
				if($seasion_cond_row>1)
				{
					 $view_button_season="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$po_orgi_shipdatess."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_season=$seasion_cond;	
				}
				if($construction_cond_row>1)
				{
					 $view_button_const="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$po_orgi_shipdatess."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_const=$construction;	
				}
				if($gsm_weight_cond_row>1)
				{
					 $view_button_gsm="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$po_orgi_shipdatess."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_gsm=$$gsm_weight;	
				}
				if($yarn_count_value_cond_row>1)
				{
					 $view_button_count="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$po_orgi_shipdatess."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_count=$yarn_count_value;	
				}
				if($style_cond_row>1)
				{
					 $view_button_style="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$po_orgi_shipdatess."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_style=$style;	
				}
				if($copm_cond_row>1)
				{
					 $view_button_comp="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$po_orgi_shipdatess."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_comp=$copm_one_name;	
				}
				
				?>
                <tr style="font:'Arial Narrow'; font-size:9px" align="center" bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
					<td width="30"><? echo $i; ?></td>
                    <td width="55" style="word-wrap:break-word; word-break: break-all;"><? echo $view_button_season;?></td>
                    <td width="80" style="word-wrap:break-word; word-break: break-all;" ><? echo $view_button_bh; ?></td> 
					<td width="70" style="word-wrap:break-word; word-break: break-all;" ><? echo $view_button_deal; ?></td> 
                    <td width="55" style="word-wrap:break-word; word-break: break-all;">
                  
					<? echo $view_button_job;//$buyer_short_name_library[$row_data[csf('buyer_name')]]; ?></td>
					<td  width="120" style="word-wrap:break-word; word-break: break-all;"> <? echo $view_button_style; ?></td>
                   
                    
                    <td width="60" style="word-wrap:break-word; word-break: break-all;"><? echo $percent_all; ?></td>
                    <td  width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $view_button_comp; ?></td>
                    
                    
                    <td  width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $view_button_const;?></td>
                    <td width="50"  style="word-wrap:break-word; word-break: break-all;"><? echo $view_button_gsm; ?></td>
					<td  width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $view_button_count ?></td>
                    <td  width="70" title="<? echo $yarn_req;?>" style="word-wrap:break-word; word-break: break-all; text-align:right">
					<?
							echo number_format($yarn_req);
				
					?>
                    </td>
					<td  width="60" style="word-wrap:break-word; word-break: break-all;">
					<?
					
					if($status_key==2)
					{
						echo "Booked";	
					}
					else
					{
						echo "Placed";
					}
					//echo $order_status[$row_data[csf('is_confirmed')]];
					?> 
                    </td>
					<td  width="45" style="word-wrap:break-word; word-break: break-all;">
					<?
					//if($opd_week!='') echo "W:".$opd_week; else echo '';
					echo $view_button_opd;
					?>
                    </td>
					<td  width="50" title="<? echo $opd_recv_date_full;?>" style="word-wrap:break-word; word-break: break-all;">
                    
					<?
					if($opd_recv_date!="" && $opd_recv_date !="0000-00-00" && $opd_recv_date!="0")
					{
						$opd_recv_date=$opd_recv_date;
					}
					echo $view_button_opd_date;
					?>
                     
                    </td>
					<td width="45" title="Orgi Ship Date" style="word-wrap:break-word; word-break: break-all; text-align: center">
						<? echo 'W'.$tod_key;	 ?>
                    </td>
                    <td  width="70" style="word-wrap:break-word; word-break: break-all; text-align:right">
					<? echo number_format($tot_po_qty);?>
                    </td>
					<td  width="100" style="word-wrap:break-word; word-break: break-all; text-align: center">
                   
					<? echo $view_button_po; ?>
                    </td>
                    <td  width="40" style="word-wrap:break-word; word-break: break-all; text-align:left" >
					<? echo $prod_code;?>
                    </td>
					<td  width="40" title="TOD-OPD" style="word-wrap:break-word; word-break: break-all; text-align: center">
					<? //echo $lead_time; ?>
                    </td>
					<td  width="40" title="PO Value/Po Qty" style="word-wrap:break-word; word-break: break-all; text-align:right">
					<? echo number_format($unit_price,2); ?>
                    </td>
                   
					<td width="90" title="Po Qty*Unit Price" style="word-wrap:break-word; word-break: break-all;text-align:right">
                   <? echo number_format($tot_po_qty*$unit_price,0);
				  	 $set_smv=number_format($status_val['set_smv'],2);
				    ?>
                    </td>
					<td width="40" title="Set SMV/Set Ratio" style="word-wrap:break-word; word-break: break-all;">
					<? 
					$smv_rate=0;
					$smv_rate=$set_smv/$status_val['set_ratio'];
					echo number_format($smv_rate,2); ?>
                    </td>
					
                    <td  width="90" title="Set SMV Per Pcs*Order Qty" style="word-wrap:break-word; word-break: break-all;text-align:right">
					<? echo number_format(($set_smv/$status_val['set_ratio'])*$tot_po_qty); ?>
                    </td>
                     <td  width="60" style="word-wrap:break-word; word-break: break-all;text-align:right">
					<? echo number_format($exfactory_data_array[$tod_key][$status_key][ex_qnty]); ?>
                    </td>
                    <td width="60" title="Ex-Fact Qty*Unit Price" style="word-wrap:break-word; word-break: break-all;text-align:right">
                    <? echo  number_format($exfactory_data_array[$tod_key][$status_key][ex_qnty]*$unit_price,0);
					 ?>
                    </td>
                    <?
					
					$week_qty=0;
					$week_check=array();
                   	foreach($week_counter_header as $mon_key=>$week)
					{
						foreach($week as $week_key)
						{
						?>
						<td  style="word-wrap:break-word; word-break: break-all;" width="45" align="right">
						
						<? 
							 $week_qty=$order_qty_arr[$week_key];
							if($week_qty>0) echo number_format($week_qty,0);else echo '';
							$tot_week_qty[$mon_key][$week_key]+=$week_qty;
							$tot_week_amount[$mon_key][$week_key]+=$week_qty*$unit_price;
							$smv_avg=0;
							$smv_avg=$smv_rate*$week_qty;
							$month_smv_arr[$mon_key]+=$smv_avg;
							
						?>
						</td>
						<?
						}
					}
					?>
                    </tr>
                    <?
					$total_order_qty+=$tot_po_qty;
					$total_yarn_req_qty+=$yarn_req;	
					$total_ex_fact_qty+=$exfactory_data_array[$tod_key][$status_key][ex_qnty];
					$total_ex_fact_val+=$exfactory_data_array[$tod_key][$status_key][ex_qnty]*$unit_price;
					$total_order_value+=$tot_po_qty*$unit_price;
					$total_order_smv+=($set_smv/$status_val['set_ratio'])*$tot_po_qty;
					//$total_week_qty+=$week_qty;
					//=====================================================================================================================
				$i++;
					}
					
				}
				?>
                 
				</table>
				</div>
                <table class="tbl_bottom" style="font:'Arial Narrow'; font-size:9px" width="<? echo $td_width;?>px" cellpadding="0" cellspacing="0" border="1" rules="all" >
					<tr>
					<td width="30">&nbsp;</td>
                    <td width="55">&nbsp;</td>
                    <td width="80">&nbsp;</td>
					<td width="70">&nbsp;</td>
                    <td width="55">&nbsp;</td>
					<td width="120">&nbsp;</td>
                    <td width="60">&nbsp;</td>
                    <td width="120">&nbsp;</td>
                    <td width="120">&nbsp;</td>
                    <td width="50">&nbsp;</td>
					<td width="120">&nbsp;</td>
                    <td width="70" style="word-wrap:break-word; word-break: break-all; font-size:smaller" id="td_yarn_req_march"><? echo $total_yarn_req_qty;?></td>
					<td width="60">&nbsp;</td>
					<td width="45">&nbsp;</td>
					<td width="50">&nbsp;</td>
					<td width="45">&nbsp;</td>
                    <td width="70" style="word-wrap:break-word; word-break: break-all; font-size:smaller" id="td_po_qty_march"><? echo $total_order_qty;?></td>
					<td width="100">&nbsp;</td>
                    <td width="40">&nbsp; </td>
					<td width="40">&nbsp;</td>
					<td width="40">&nbsp;</td>
					<td width="90" id="td_po_val_march" style="word-wrap:break-word; word-break: break-all; font-size:smaller"><? echo number_format($total_order_value);?></td>
					<td width="40"><? echo number_format($total_order_smv/$total_order_qty,0);?></th>
                    <td width="90" style="word-wrap:break-word; word-break: break-all; font-size:smaller" id="total_order_smv"><? echo number_format($total_order_smv,0);?></td>
                    <td width="60" style="word-wrap:break-word; word-break: break-all; font-size:smaller" id="total_order_exfc_qty"><? echo number_format($total_ex_fact_qty,0);?></td>
                    <td width="60" style="word-wrap:break-word; word-break: break-all; font-size:smaller" id="total_order_exfc_val"><? echo number_format($total_ex_fact_val,0);?></td>
                     <?
					//foreach($week_for_header as $week_key)
					$total_week_qty=0; $total_week_amt=0; 
					foreach($week_counter_header as $mon_key=>$week)
					{
						foreach($week as $week_key)
						{
						?>
						<td width="45" style="word-wrap:break-word; word-break: break-all; font-size:smaller"   align="center">
						
						<? 
						$total_week_qty=$tot_week_qty[$mon_key][$week_key];
						$total_week_amt=$tot_week_amount[$mon_key][$week_key];
						echo number_format($total_week_qty,0);
						$tot_mon_qty[$mon_key]+=$total_week_qty;
						$tot_mon_amount[$mon_key]+=$total_week_amt;
						?>
						</td>
						<?
						
						}
					}
            ?>
					</tr>
                     <tr style="display:none">
					<td colspan="26">&nbsp;</td>
                     <?
					//foreach($week_for_header as $week_key)
					$total_week_qt=0; $m=1;
					foreach($week_counter_header as $mon_key=>$week)
					{
						?>
						<td width="" id="summary_footer_td_<? echo $mon_key?>" colspan="<? echo count($week);?>" title="<? echo $tot_week_amount[$mon_key][$week_key];?>" style="word-wrap:break-word; word-break: break-all;"   align="center">
						<? 
						$tot_smv=0;
						$tot_smv=$month_smv_arr[$mon_key]/$tot_mon_qty[$mon_key];
						$head_td_data=$tot_mon_qty[$mon_key].'$'.$tot_mon_amount[$mon_key].'$'.$tot_smv;
						echo $head_td_data;
						?>
						</td>
						<?
					}
            ?>
                    </tr>
				</table>
			</fieldset>
		</div>
        <style>
			@font-face {
   				 font-family:"Arial Narrow"; font-size:11px;
			}
			TD{font-family:"Arial Narrow";font-size:10px;}
			TH{font-family:"Arial Narrow";font-size:11px;}
			
			.rpt_table TD{font-family: "Arial Narrow";font-size:11px;}
			.rpt_table TH{font-family:"Arial Narrow";font-size:11px;}
		</style>
	<?
	}
//===========================================================================================================================================================
	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data****$filename****$tot_rows****$cbo_search_type";
	exit();	
	
}

else if($action=="report_generate_status") //Status Wise
{
 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company_name=str_replace("'","",$cbo_company_name);
	$cbo_search_type=str_replace("'","",$cbo_search_type);
	$cbo_bh_mer_name=str_replace("'","",$cbo_bh_mer_name);
	$txt_yarn_count=str_replace("'","",$txt_yarn_count);
	if($txt_yarn_count!='') $yarn_count_con="and yarn_count Like '%$txt_yarn_count%' ";else $yarn_count_con='';
	
	$buyer_id_cond="";
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{
				$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else
			{
				$buyer_id_cond="";
			}
		}
		else
		{
			$buyer_id_cond="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_job_no=trim($txt_job_no);
	if($txt_job_no !="" || $txt_job_no !=0)
	{
		//$year = substr(str_replace("'","",$cbo_year_selection), -2); 
		//$job_no=$company_library[$company_name]."-".$year."-".str_pad($txt_job_no, 5, 0, STR_PAD_LEFT);
		$jobcond="and a.job_no_prefix_num in(".$txt_job_no.")";
	}
	else
	{
		$jobcond="";	
	}
	
	
  	$date_cond='';
	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		$start_date=(str_replace("'","",$txt_date_from));
		$end_date=(str_replace("'","",$txt_date_to));
		
		$date_cond="and b.pub_shipment_date between '$start_date' and '$end_date'";
	}
	//if (str_replace("'","",$txt_job_no)=="") $job_no_cond=""; else $job_no_cond=" and a.job_no_prefix_num in (".str_replace("'","",$txt_job_no).") ";
	
	if(str_replace("'","",$txt_style_ref)!="") $style_ref_cond=" and a.style_ref_no like '%".str_replace("'","",$txt_style_ref)."%'"; else $style_ref_cond="";
	if(str_replace("'","",$txt_order_no)!="")
	{
		$ordercond=" and b.po_number like '%".str_replace("'","",$txt_order_no)."%'"; 
	}
	else
	{
		$ordercond="";
	}
	
	$team_cond="";
	if(str_replace("'","",$cbo_team_name)!=0) $team_cond=" and a.team_leader=".str_replace("'","",$cbo_team_name)."  ";
	if(str_replace("'","",$cbo_team_member)!=0) $team_cond.=" and a.dealing_marchant=".str_replace("'","",$cbo_team_member)."  ";
	$cbo_bh_mer_name=str_replace("0","",$cbo_bh_mer_name);
	if($cbo_bh_mer_name!=0 || $cbo_bh_mer_name!='') $bh_mer_name_con="and a.bh_merchant='$cbo_bh_mer_name' ";else $bh_mer_name_con='';
	$cbo_year_selection=str_replace("'","",$cbo_year_selection);

	$start_ddd= strtotime(change_date_format($start_date,"dd-mm-yyyy","-"));
	$start_ddd=date("Y-m-d",$start_ddd);
	$start_end_date= strtotime(change_date_format($end_date,"dd-mm-yyyy","-"));
	$start_end_date=date("Y-m-d",$start_end_date);
	 //$dd= date('d-m-Y',strtotime($start_date));
	//$start_date_week= date($dd, time() - 1296000);//$start_date;
	//echo $last_day_this_month  = date($start_date_week, time() -1814400); //date('t-m-Y');
	
	$dateee = strtotime($start_ddd);
	$start_end_date = strtotime($start_end_date);
	
	$date_start = date("Y-m-d",strtotime("-28 day", $dateee));
	$date_end = date("Y-m-d",strtotime("+28 day", $start_end_date));
	//echo $date_end;
	//$end_date_week=  addday("$end_date", time() -1814400);//$start_date;//$start_date;
	if($db_type==0)
	{
		$date_start= change_date_format($date_start,"yyyy-mm-dd");
		$date_end= change_date_format($date_end,"yyyy-mm-dd");
	}
	else
	{
		$date_start= change_date_format($date_start,"dd-mm-yyyy","-",1);
		$date_end= change_date_format($date_end,"dd-mm-yyyy","-",1);
	}
	
	
	//echo "select week_date,week from week_of_year where week_date between '$date_start' and  '$date_end' order by week_date asc";die;
	// date("d-m-Y", time() - 1296000);
	$week_for_arr=array();$no_of_week_for_header=array();
	$sql_week_header=sql_select("select week_date,week from week_of_year where week_date between '$date_start' and  '$date_end' order by week_date asc");
	$week_check_head=array();
	foreach ($sql_week_header as $row_week_header)
	{
		if($week_check_head[$row_week_header[csf("week")]]=='')
		{
			$week_counter_header[date("Y-M",strtotime($row_week_header[csf("week_date")]))][$row_week_header[csf("week")]]=$row_week_header[csf("week")];
			$week_check_head[$row_week_header[csf("week")]]=$week_counter_header[date("Y-M",strtotime($row_week_header[csf("week_date")]))][$row_week_header[csf("week")]];
		}
		$tmp=add_date($row_week_header[csf("week_date")],-1);
		if($db_type==0) $tmp_cond=date("d-m-y",strtotime($tmp));
		else $tmp_cond=date("d-M-y",strtotime($tmp));
		$no_of_week_for_header_calc[$tmp_cond]=$row_week_header[csf("week")];	
		$no_of_week_for_header[$row_week_header[csf("week_date")]]=$row_week_header[csf("week")];
		$test[$row_week_header[csf("week")]]=$row_week_header[csf("week")];
	}
	unset($sql_week_header);
	$week_start_day=array();
	$week_end_day=array();$week_day_arr=array();
	//week_day
	$sql_week_start_end_date=sql_select("select week,min(week_date) as week_start_day, Max(week_date) as week_end_day from week_of_year where week_date between '$date_start' and  '$date_end' group by week");
	foreach ($sql_week_start_end_date as $row_week)
	{
		$week_start_day[$row_week[csf("week")]][week_start_day]=$row_week[csf("week_start_day")];
		$week_end_day[$row_week[csf("week")]][week_end_day]=$row_week[csf("week_end_day")];
		//$week_day_arr[$row_week[csf("week")]][$row_week[csf("week_start_day")]][week_first_day]=$row_week[csf("min_week_day")];
		$week_day_arr[$row_week[csf("week_end_day")]]['week_last_day']=$row_week[csf("last_week_day")];
	}
	unset($sql_week_start_end_date);
	//print_r($week_day_arr);
	function distribute_projection( $base_week, $qnty)
	{
		//5. Distribution ratios are: Base week 52%, Immediate before 24%, Week before immediate week 20%, Immediate week after base week 2%, next 2 weeks as 1%
		global $test;
		 
		$tmpbase_week=$test[$base_week-3];
		$k=0;
		for($i=0; $i<6; $i++)
		{
			$tmpbase_week++;
			if( $test[$tmpbase_week]=='') { $tmpbase_week=1; $k=0; }
			
			
			if($i==0) $distr_arr[$tmpbase_week]=($qnty*20)/100;
			if($i==1) $distr_arr[$tmpbase_week]=($qnty*24)/100;
			if($i==2) $distr_arr[$tmpbase_week]=($qnty*52)/100;
			if($i==3) $distr_arr[$tmpbase_week]=($qnty*2)/100;
			if($i==4) $distr_arr[$tmpbase_week]=($qnty*1)/100;
			if($i==5) $distr_arr[$tmpbase_week]=($qnty*1)/100;
			$k++;
			
		}
		 
		return $distr_arr;
		/*5000
		b=52
		b-1=21
		b-2=15
		b+1=1
		b+2=1*/
	}
	//echo "<per>";
	 //print_r(distribute_projection("2016-Dec",51, 5000)); die;
	

	
	if($template==1)
	{
	$po_wise_buyer=array();
	$buyer_wise_data=array();
	$po_id_arr=array();
	$week_wise_order_qty_arr=array();	
	
	
	$count_data=sql_select("select yarn_count,id from  lib_yarn_count  where status_active=1 and is_deleted=0 and yarn_count is not null $yarn_count_con");
	$yarn_id='';
	foreach($count_data as $row)
	{
			if($yarn_id=='') $yarn_id=$row[csf('id')];else $yarn_id.=",".$row[csf('id')];
			
	}
	$yarn_ids=count(array_unique(explode(",",$yarn_id)));
		$yarn_numIds=chop($yarn_id,','); $yarnIds_cond="";
		if($yarn_id!='' || $yarn_id!=0)
		{
			if($db_type==2 && $yarn_ids>1000)
			{
				$yarnIds_cond=" and (";
				$yIdsArr=array_chunk(explode(",",$yarn_numIds),990);
				foreach($yIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$yarnIds_cond.=" d.count_id  in($ids) or ";
				}
				$yarnIds_cond=chop($yarnIds_cond,'or ');
				$yarnIds_cond.=")";
			}
			else
			{
				$yarnIds_cond=" and  d.count_id  in($yarn_id)";
			}
		}
		$yarn_sql_data=("select b.is_confirmed,b.id as po_id,d.count_id,d.copm_one_id,d.copm_two_id,d.percent_one,d.percent_two,sum(d.cons_qnty) as cons_qnty,e.construction,e.gsm_weight from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fab_yarn_cost_dtls d,wo_pre_cost_fabric_cost_dtls e where a.job_no=b.job_no_mst  and c.job_no_mst=a.job_no and b.id=c.po_break_down_id and d.job_no=a.job_no and 
	e.id=d.fabric_cost_dtls_id and  c.item_number_id= e.item_number_id and e.job_no=a.job_no and a.is_deleted=0 and e.body_part_id in(1,20) and a.status_active=1 and b.is_deleted=0 and b.status_active=1   and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $yarnIds_cond $jobcond $ordercond $team_cond $bh_mer_name_con group by   b.id ,d.count_id,d.copm_one_id,d.copm_two_id,d.percent_one,d.percent_two,e.construction,e.gsm_weight,b.is_confirmed"); 
	$sql_result_y=sql_select($yarn_sql_data);
	$wo_fab_data_arr=array();
	$all_yarn_po_id="";
	foreach($sql_result_y as $row)
		{
			if($all_yarn_po_id=="") $all_yarn_po_id=$row[csf("po_id")]; else $all_yarn_po_id.=",".$row[csf("po_id")];
			if($row[csf('percent_two')]!=0) $row[csf('percent_two')]=$row[csf('percent_two')];else $row[csf('percent_two')]='';
			$percent_one=$row[csf('percent_one')].'**'.$row[csf('percent_two')];
			if($row[csf('copm_two_id')]!=0) $row[csf('copm_two_id')]=$row[csf('copm_two_id')];else $row[csf('copm_two_id')]='';
			$copm_one_id=$row[csf('copm_one_id')].'**'.$row[csf('copm_two_id')];
			$wo_fab_data_arr[$row[csf('is_confirmed')]]['percent_one']=$percent_one;
			//$wo_fab_data_arr[$prod_code][$row[csf('is_confirmed')]]['percent_two']=$row[csf('percent_two')];
			$wo_fab_data_arr[$row[csf('is_confirmed')]]['copm_two_id']=$copm_one_id;
			//$wo_fab_data_arr[$prod_code][$row[csf('is_confirmed')]]['copm_one_id']=$row[csf('copm_one_id')];
			$wo_fab_data_arr[$row[csf('is_confirmed')]]['construction'].=$row[csf('construction')].',';
			$wo_fab_data_arr[$row[csf('is_confirmed')]]['gsm_weight'].=$row[csf('gsm_weight')].',';
			$wo_fab_count_data_arr[$row[csf('is_confirmed')]]['count_id'].=$row[csf('count_id')].",";
			//$wo_yarn_data_arr[$row[csf('po_id')]]['cons_qnty']+=$row[csf('cons_qnty')];
		}
		unset($sql_result_y);
		$yarn_po_ids=count(array_unique(explode(",",$all_yarn_po_id)));
		$yarnPo_numIds=chop($all_yarn_po_id,','); $yarnPoIds_cond="";
		if($txt_yarn_count!='')
		{
			if($all_yarn_po_id!='' || $all_yarn_po_id!=0)
			{
				if($db_type==2 && $yarn_po_ids>1000)
				{
					$yarnPoIds_cond=" and (";
					$yPoIdsArr=array_chunk(explode(",",$yarnPo_numIds),990);
					foreach($yPoIdsArr as $ids)
					{
						$ids=implode(",",$ids);
						$yarnPoIds_cond.=" b.id  in($ids) or ";
					}
					$yarnPoIds_cond=chop($yarnPoIds_cond,'or ');
					$yarnPoIds_cond.=")";
				}
				else
				{
					$yarnPoIds_cond=" and  b.id  in($all_yarn_po_id)";
				}
			}
		}
		 $status_wise_arr_qty=array();
		 $sql_data_c=("select b.id as po_id,a.job_no,b.shipment_date as orgi_ship_date,b.is_confirmed,(b.po_quantity*a.total_set_qnty) as po_qty_pcs
		from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $team_cond $bh_mer_name_con order by b.shipment_date ");
		$sql_result_c=sql_select($sql_data_c);
		foreach($sql_result_c as $row)
		{
			if($row[csf('is_confirmed')]==2)
			{
				$tod_week=$no_of_week_for_header[$row[csf('orgi_ship_date')]];
				$status_wise_arr_qty[$row[csf('is_confirmed')]][$tod_week]['po_qty']+=$row[csf('po_qty_pcs')];
			}
		}	
	
	if($db_type==0) $date_dif="DATEDIFF(b.shipment_date, b.po_received_date) as lead_time_days";
	else  $date_dif="(b.shipment_date-b.po_received_date) as lead_time_days";
	 $sql_query=("select $date_dif,a.job_no,a.job_no_prefix_num as prefix_job_no,a.set_smv,a.season_buyer_wise  as season,a.product_code,a.bh_merchant,a.dealing_marchant,a.style_ref_no,b.id,b.is_confirmed,b.po_number,b.po_received_date,b.shipment_date as orgi_ship_date,a.total_set_qnty as set_ratio,(b.po_quantity*a.total_set_qnty) as po_qty_pcs,b.po_total_price,a.avg_unit_price as unit_price,c.country_ship_date,c.order_quantity,c.order_total
	   from wo_po_details_master a, wo_po_break_down b
	   LEFT JOIN wo_po_color_size_breakdown c on  c.po_break_down_id=b.id  and c.status_active=1
	 where a.job_no=b.job_no_mst  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1   and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $yarnPoIds_cond $bh_mer_name_con $team_cond order by b.is_confirmed,a.job_no ");
	  
	$sql_data=sql_select($sql_query);
	$tot_rows=count($sql_data);
	$all_po_id="";
	$marchant_wise_arr=array();
	foreach( $sql_data as $row)
	{
		
		$is_confirmed=$row[csf('is_confirmed')];
		if($all_po_id=="") $all_po_id=$row[csf("id")]; else $all_po_id.=",".$row[csf("id")];
		
		if($db_type==0) $c_date_con=date("d-m-y",strtotime($row[csf('country_ship_date')]));
		else $c_date_con=date("d-M-y",strtotime($row[csf('country_ship_date')]));
		$c_date_week=$no_of_week_for_header_calc[$c_date_con];
		if($row[csf('is_confirmed')]==1)
		{
			$order_val=$row[csf('order_total')];
		}
		else
		{
			$order_val=$row[csf('po_total_price')];
		}
		//$c_date_week=$no_of_week_for_header[$row[csf('country_ship_date')]];
		$status_wise_arr[$row[csf('is_confirmed')]]['po_qty']+=$row[csf('po_qty_pcs')];
		$status_wise_arr[$row[csf('is_confirmed')]]['po_val']+=$order_val;
		$status_wise_arr[$row[csf('is_confirmed')]]['set_smv']+=$row[csf('set_smv')];
		$status_wise_arr[$row[csf('is_confirmed')]]['set_ratio']+=$row[csf('set_ratio')];
		$status_wise_arr[$row[csf('is_confirmed')]]['lead_days']+=$row[csf('lead_time_days')];
		$status_wise_arr[$row[csf('is_confirmed')]]['unit_price']+=$row[csf('unit_price')];
		$status_wise_arr[$row[csf('is_confirmed')]]['job_no'].=$row[csf('prefix_job_no')].',';
		$status_wise_arr[$row[csf('is_confirmed')]]['style'].=$row[csf('style_ref_no')].',';
		$status_wise_arr[$row[csf('is_confirmed')]]['season'].=$row[csf('season')].',';

		$status_wise_arr[$row[csf('is_confirmed')]]['dealing_marchant'].=$row[csf('dealing_marchant')].',';
		$status_wise_arr[$row[csf('is_confirmed')]]['bh_mer'].=$row[csf('bh_merchant')].',';
		$status_wise_arr[$row[csf('is_confirmed')]]['po_no'].=$row[csf('po_number')].',';
		$status_wise_arr[$row[csf('is_confirmed')]]['po_id'].=$row[csf('id')].',';
		$status_wise_arr[$row[csf('is_confirmed')]]['product_code'].=$row[csf('product_code')].',';
		$status_wise_arr[$row[csf('is_confirmed')]]['po_received_date'].=$row[csf('po_received_date')].',';
		$status_wise_arr[$row[csf('is_confirmed')]]['orgi_ship_date'].=$row[csf('orgi_ship_date')].',';
		$status_wise_arr[$row[csf('is_confirmed')]]['c_ship_date'].=$row[csf('country_ship_date')].',';
		if($row[csf('is_confirmed')]==1)
		{
		$week_wise_order_qty[$row[csf('is_confirmed')]][$c_date_week]['po_quantity']+=$row[csf("order_quantity")];
		}

	}
	//print_r($status_wise_arr);
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
					$poIds_cond.=" b.id  in($ids) or ";
				}
				$poIds_cond=chop($poIds_cond,'or ');
				$poIds_cond.=")";
			}
			else
			{
				$poIds_cond=" and  b.id  in($all_po_id)";
			}
		}
		$exfactory_data_array=array();
		$exfactory_sql=("select b.is_confirmed,
		sum(CASE WHEN f.entry_form!=85 THEN f.ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
		sum(CASE WHEN f.entry_form=85 THEN f.ex_factory_qnty ELSE 0 END) as return_qnty
		 from wo_po_details_master a, wo_po_break_down b,pro_ex_factory_mst f  where  a.job_no=b.job_no_mst and  f.po_break_down_id=b.id  and  a.company_name=$company_name  and f.status_active=1 and f.is_deleted=0 $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $team_cond group by  b.is_confirmed");
		$exfactory_data=sql_select($exfactory_sql);
		foreach($exfactory_data as $row)
		{
				//$prod_code=$row[csf('product_code')];
				$exfactory_data_array[$row[csf('is_confirmed')]][ex_qnty]+=$row[csf('ex_factory_qnty')]-$row[csf('return_qnty')];
		}
		unset($exfactory_data);
	
		ob_start();
		$rowcount=0;
		foreach($week_counter_header as $mon_key=>$week)
		{
			$rowcount+=count($week);
		}
		 $rowb=$rowcount*2;
		 $td_width=1870+($rowcount*45)+$rowb;
		 
		$tot_month = datediff( 'm', $date_start,$date_end);
		for($i=0; $i<= $tot_month; $i++ )
		{
			$next_month=month_add($date_start,$i);
			$month_arr.=date("Y-M",strtotime($next_month)).',';
		}
	 $month_arr_id=rtrim($month_arr,',');
	if($tot_rows>0)
	{
	?>
    
     <script>
		var mon_week ='<? echo $month_arr_id; ?>';
		var mon_week=mon_week.split(",");
		for (var k=0; k<mon_week.length; k++)
		{
			var mon_data=document.getElementById('summary_footer_td_'+mon_week[k]).innerHTML;
			var mon_arr=mon_data.split("$");
			var mon_qnty=number_format(mon_arr[0],0);
			var mon_amt=number_format(mon_arr[1],0);
			var order_smv=number_format(mon_arr[2],2);
			var avg_rate=(number_format(mon_arr[1],0,'.','')/number_format(mon_arr[0],0,'.',''));
			document.getElementById('summary_header_td_qty_'+mon_week[k]).innerHTML=mon_qnty;
			document.getElementById('summary_header_td_val_'+mon_week[k]).innerHTML='$'+mon_amt;
			document.getElementById('summary_header_td_rate_'+mon_week[k]).innerHTML='FOB:'+number_format(avg_rate,2);
			document.getElementById('summary_header_td_smv_'+mon_week[k]).innerHTML='SMV:'+number_format(order_smv,2);
		}
		var yarn_req_qty=document.getElementById('td_yarn_req_march').innerHTML;
		var yarnreq_qty=number_format(yarn_req_qty,0);
		document.getElementById('yarn_summary_tot').innerHTML=yarnreq_qty;
		</script>
        <?
	 }
		?>
		<div style="width:<? echo $td_width+20;?>px">
		<fieldset style="width:100%;">	
			<table width="<? echo $td_width+20;?>px">
				<tr class="form_caption">
					<td align="left" style="margin-left:10px" colspan="<? echo $rowcount+25; ?>">Status Wise</td>
				</tr>
				<tr class="form_caption" align="center" style="display:none">
					<td colspan="<? echo $rowcount+26; ?>" align="center"><? //echo $company_library[$company_name]; ?></td>
				</tr>
			</table>
			<table class="rpt_table" width="<? echo $td_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
                  <tr>
                    <th  width="" colspan="11" rowspan="2">&nbsp;</th>
                     <th  rowspan="2" id="yarn_summary_tot"></th>
                     <th  width="" colspan="14" rowspan="2">&nbsp;</th>
                     <?
					foreach($week_counter_header as $mon_key=>$week)
					{
					?>
					<th   align="center" colspan="<? echo count($week);?>">
					<? 
						echo $mon_key;
					?>
					</th>
					<?
					}
            ?>
                </tr>
                  <tr>
                     <?
					foreach($week_counter_header as $mon_key=>$week_val)
					{
					?>
					<th width="60"  style="font-size:0.875em;"  align="center" colspan="<? echo count($week_val)?>">
                             <b id="summary_header_td_qty_<? echo $mon_key ;?>"  style="text-align:left;font-size: 0.775em;">  </b> &nbsp; 
                             <b id="summary_header_td_val_<? echo $mon_key ;?>"  style="text-align: right;font-size: 0.775em;">  </b> &nbsp;
                             <b id="summary_header_td_rate_<? echo $mon_key ;?>"  style="text-align: right;font-size: 0.775em;"></b> &nbsp;
                             <b title="Avg Smv" id="summary_header_td_smv_<? echo $mon_key ;?>"  style="text-align: right;font-size: 0.775em;"></b>
					</th>
					<?
					}
            ?>
                </tr>
                <tr>
					<th width="30">SL</th>
                    <th width="55">Season</th>
                    <th width="100">BH Mer.</th>
					<th width="140">Merchant</th>
                    <th width="55">Job No</th>
					<th width="100">Style Ref</th>
                    <th width="60">Comp%</th>
                    <th width="120">Comp Name</th>
                    <th width="120">Const.</th>
                    <th width="50">GSM</th>
					<th width="120">Y/C</th>
                    <th width="70">Yarn Qty</th>
					<th width="60">Status</th>
					<th width="45">OPD</th>
					<th width="60">OPD Date</th>
					<th width="45">TOD</th>
                    <th width="70">Ord Qty</th>
					<th width="100">Order No</th>
                    <th width="40">Dept </th>
					<th width="40">Lead Time</th>
					<th width="50">Unit Price</th>
					<th width="90">Value</th>
					<th width="40">SMV/ Pcs</th>
                    <th width="90">Ord.SMV</th>
                    <th width="60">Delv Qty</th>
                    <th width="60">Delv.Val</th>
                     <?
					//foreach($week_for_header as $week_key)
					foreach($week_counter_header as $mon_key=>$week)
					{
						foreach($week as $week_key)
						{
							$start_week_date=$week_start_day[$week_key][week_start_day];
							$end_week_date=$week_end_day[$week_key][week_end_day];
							if($db_type==2)
							{
								$fisrtday=date("D",strtotime(change_date_format($start_week_date,"dd-mm-yyyy","-")));
								$lastday=date("D",strtotime(change_date_format($end_week_date,"dd-mm-yyyy","-")));
							}
							else
							{
								$fisrtday=date("D",strtotime(change_date_format($start_week_date,"dd-mm-yyyy")));
								$lastday=date("D",strtotime(change_date_format($end_week_date,"dd-mm-yyyy")));
							}
							$weekstart_date=explode("-",date("d-M",strtotime($start_week_date)));
							$weekstart_date=$weekstart_date[0].'/'.$weekstart_date[1];
						?>
						<th title="<? echo change_date_format($start_week_date,"dd-mm-yyyy","-").'('.$fisrtday.')'." To ".change_date_format($end_week_date,"dd-mm-yyyy","-").'('.$lastday.')'; ?>" width="45"  align="center">
						<? 
							echo 'W'.$week_key.'<br/>'.$weekstart_date;
						?>
						</th>
						<?
						}
					}
            ?>                   
                </tr>    
				</thead>
			</table>
			<div style="width:<? echo $td_width+20;?>px; max-height:245px; overflow-y:scroll;overflow-x:hidden;" id="scroll_body">
			<table class="rpt_table" width="<? echo $td_width;?>px" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body_status">
	<?
			$condition= new condition();
			 $condition->company_name("=$cbo_company_name");
			 if(str_replace("'","",$cbo_buyer_name)>0)
			 {
				  $condition->buyer_name("=$cbo_buyer_name");
			 }
			 if(str_replace("'","",$txt_job_no) !='')
			 {
				  $condition->job_no_prefix_num("in($txt_job_no)");
			 }
			
			 if(str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!='')
			 {
			 $condition->pub_shipment_date(" between '$start_date' and '$end_date'");
			 }
				
			 if(str_replace("'","",$txt_order_no)!='')
			 {
				$condition->po_number("=$txt_order_no"); 
			 }
			 if(str_replace("'","",$txt_season)!='')
			 {
				//$condition->season("=$txt_season"); 
			 }
			 $condition->init();
			 
         		$yarn= new yarn($condition);
		  		//echo $yarn->getQuery(); die;
		 		$yarn_req_qty_arr=$yarn->getOrderWiseYarnQtyArray();
				$i=1;$total_order_qty=0;$total_order_value=0;$total_yarn_req_qty=0;$total_order_smv=0;$total_ex_fact_val=0;$total_ex_fact_qty=0;
				foreach($status_wise_arr as $status_key=>$status_val)
				{  
				 if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";  
				$po_received_date=$status_val['po_received_date'];
				$conf_country_ship_date=rtrim($wo_color_data_arr2[$status_key]['shipdate'],',');
				$conf_country_ship_date=explode(",",$conf_country_ship_date);
				$percent_one=$wo_fab_data_arr[$status_key]['percent_one'];//rtrim($status_val['percent'],',');
				$comp=$wo_fab_data_arr[$status_key]['copm_two_id'];//rtrim($status_val['comp'],',');
				$percent_data=array_unique(explode(",",$percent_one));
				$comp_data=array_unique(explode(",",$comp));
				
				$percent_one_cond="";$percent_two_cond="";
				foreach($percent_data as $perid)
				{
					$percent_data=array_unique(explode("**",$perid));
					$percent_one=$percent_data[0];
					$percent_two=$percent_data[1];
					if($percent_one_cond=="") $percent_one_cond=$percent_one;else $percent_one_cond.=",".$percent_one;
					if($percent_two_cond=="") $percent_two_cond=$percent_two;else $percent_two_cond.=",".$percent_two;
				}
				if($percent_one_cond!='' && $percent_two_cond!='')
					{
						$percent_all=$percent_one_cond.'/'.$percent_two_cond;
					}
					else if($percent_one_cond!='' && $percent_two_cond=='')
					{
						$percent_all=$percent_one_cond;
					}
					else if($percent_one_cond=='' && $percent_two_cond!='')
					{
						$percent_all=$percent_two_cond;
					}
					else
					{
						$percent_all='';	
					}
				
				$comp_one_cond="";$comp_two_cond="";
				foreach($comp_data as $compid)
				{
						$comp_data=array_unique(explode("**",$compid));
						$comp_one=$comp_data[0];
						$comp_two=$comp_data[1];
						
					if($comp_one_cond=="") $comp_one_cond=$composition[$comp_one];else $comp_one_cond.=",".$composition[$comp_one];
					if($comp_two_cond=="") $comp_two_cond=$composition[$comp_two];else $comp_two_cond.=",".$composition[$comp_two];
				}
				
				if($comp_one_cond!='' && $comp_two_cond!='')
				{ 
					$copm_one_name=$comp_one_cond.'/'.$comp_two_cond;
				}
				else if($comp_one_cond!='' && $comp_two_cond=='')
				{ 
					$copm_one_name=$comp_one_cond;
				
				}
				else if($comp_one_cond=='' && $comp_two_cond!='')
				{ 
					$copm_one_name=$comp_two_cond;
				}
				else 
				{
					$copm_one_name='';
				}
				
				$construction=rtrim($wo_fab_data_arr[$status_key]['construction'],',');
				$construction=implode(",",array_unique(explode(",",$construction)));
				
				$gsm_weight=rtrim($wo_fab_data_arr[$status_key]['gsm_weight'],',');
				$gsm_weight=implode(",",array_unique(explode(",",$gsm_weight)));
				$count_id=$wo_fab_count_data_arr[$status_key]['count_id'];
				$count_id=rtrim($count_id,',');
				 $count_ids=array_unique(explode(",", $count_id));
				 $yarn_count_value="";
				foreach($count_ids as $val)
				{
						if($yarn_count_value=="") $yarn_count_value=$lib_yarn_count_arr[$val]; else $yarn_count_value.=", ".$lib_yarn_count_arr[$val];
				}
				$seasion_id=rtrim($status_val['season'],',');
				$job_no=rtrim($status_val['job_no'],',');
				$job_no=implode(",",array_unique(explode(",",$job_no)));
				$style=rtrim($status_val['style'],',');
				$po_no=rtrim($status_val['po_no'],',');
				$po_id=rtrim($status_val['po_id'],',');
			
				$dealing_marchant=rtrim($status_val['dealing_marchant'],',');
				$bh_mer=rtrim($status_val['bh_mer'],',');
				$bh_mer=implode(",",array_unique(explode(",",$bh_mer)));
				
				$po_received_date=rtrim($status_val['po_received_date'],',');
				$product_code=rtrim($status_val['product_code'],',');
				$prod_code=implode(",",array_unique(explode(",",$product_code)));
				$c_ship_date=rtrim($status_val['c_ship_date'],',');
				$job_no=implode(",",array_unique(explode(",",$job_no)));
				$style=implode(",",array_unique(explode(",",$style)));
				$po_no=implode(",",array_unique(explode(",",$po_no)));
				$po_id=array_unique(explode(",",$po_id));
			
				$yarn_req=0;
				foreach($po_id as $pid)
				{
				$yarn_req+=$yarn_req_qty_arr[$pid];
				}
				
				$seasion_cond=implode(",",array_unique(explode(",",$seasion_id)));
				/*$seasion_cond="";
				foreach($seasion_id as $sid)
				{
					if($seasion_cond=="") $seasion_cond=$lib_season_name_arr[$sid];else $seasion_cond.=",".$lib_season_name_arr[$sid];
				}*/
				$dealing_marchant=array_unique(explode(",",$dealing_marchant));
				$dealing_marchant_con="";
				foreach($dealing_marchant as $mid)
				{
					if($dealing_marchant_con=="") $dealing_marchant_con=$team_member_arr[$mid];else $dealing_marchant_con.=",".$team_member_arr[$mid];
				}
				$po_received_date=array_unique(explode(",",$po_received_date));
				$opd_week="";$opd_recv_date="";$opd_recv_date_full="";
				//$lead_time=0;
				foreach($po_received_date as $op_id)
				{
					if($opd_week=="") $opd_week=$no_of_week_for_header[$op_id];else $opd_week.=",".$no_of_week_for_header[$op_id];
					if($opd_recv_date=="") $opd_recv_date=date("d-M",strtotime($op_id));else $opd_recv_date.=",".date("d-M",strtotime($op_id));
					if($opd_recv_date_full=="") $opd_recv_date_full=date("d-M-y",strtotime($op_id));else $opd_recv_date_full.=",".date("d-M-y",strtotime($op_id));
				//$lead_time = datediff( 'd', $op_id,$countryship_date);
				}
				$po_orgi_shipdate=rtrim($status_val['orgi_ship_date'],',');
				$po_orgi_shipdate=array_unique(explode(",",$po_orgi_shipdate));
				$tod_weeks="";$tod_recv_date="";
				foreach($po_orgi_shipdate as $orgi_id)
				{
					if($tod_weeks=="") $tod_weeks=$no_of_week_for_header[$orgi_id];else $tod_weeks.=",".$no_of_week_for_header[$orgi_id];
				}
				$tod_weeks=implode(",",array_unique(explode(",",$tod_weeks)));
				$lead_time =$status_val['lead_days'];
				
				$opd_week=implode(",",array_unique(explode(",",$opd_week)));
				$opd_recv_date=implode(",",array_unique(explode(", ",$opd_recv_date)));
				if($status_key==1) //Confirm $conf_country_ship_date
				{
					$c_ship_date_con=array_unique(explode(",",$c_ship_date));$qnty_array_dd=array();
					$c_week_qty=0;
					foreach($c_ship_date_con as $c_date)
					{
								if($db_type==0) $c_date_con=date("d-m-y",strtotime($c_date));
								else $c_date_con=date("d-M-y",strtotime($c_date));
								$c_week=$no_of_week_for_header_calc[$c_date_con];
								$c_week_qty=$week_wise_order_qty[$status_key][$c_week]['po_quantity'];
										//echo $c_week.'=='.$c_date.'hj';
								$qnty_array_dd[$c_week]=$c_week_qty;
					}
					//print_r($qnty_array_dd);
						$tot_order_qty=0;$tot_order_qty_arr=array();
						foreach($week_counter_header as $mon_key=>$week)
						{
							foreach($week as $week_key)
							{
								 $tot_order_qty=$qnty_array_dd[$week_key];
								 $order_qty_arr[$week_key]=$tot_order_qty;
								  $tot_order_qty_arr[$status_key]+=$tot_order_qty;
							}
						}
						
				}
				else 
				{
						$po_orgi_shipdate=rtrim($status_val['orgi_ship_date'],',');
						$proj_orgi_shipdate=array_unique(explode(",",$po_orgi_shipdate));
						$qnty_array_mm=array();$proj_po_qty=0;
						$week_check=array();
						foreach($proj_orgi_shipdate as $tod_key)
						{
							$tod_week=$no_of_week_for_header[$tod_key];
							if($week_check[$tod_week]=='')
							{
								 $proj_po_qty=$status_wise_arr_qty[$status_key][$tod_week]['po_qty'];
							
								$qnty_arr=distribute_projection($tod_week, $proj_po_qty);
								foreach($qnty_arr as $key=>$val)
								{
									$qnty_array_mm[$key]+=$val;
								}
								
								$week_check[$status_key]=$proj_po_qty;
							}
						}
					
							$order_qty=0;$tot_order_qty_arr=array();
							foreach($week_counter_header as $mon_key=>$week)
							{
								foreach($week as $week_key)
								{
									$order_qty=$qnty_array_mm[$week_key];
									$order_qty_arr[$week_key]=$order_qty;
									$tot_order_qty_arr[$status_key]+=$order_qty;
								}
							}
				}
				$tot_po_qty=$tot_order_qty_arr[$status_key];
				$po_value=$status_val['po_val'];
			//	echo $po_value.'='.$status_val['po_qty'].'<br>';
				$unit_price=$po_value/$tot_po_qty;
				$job_no_row=count(array_unique(explode(",",$job_no)));
				$prod_code_row=count(array_unique(explode(",",$prod_code)));
				if($job_no_row>1)
				{
					 $view_button="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$status_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button=$job_no;	
				}
			
				if($prod_code_row>1)
				{
					 $view_button_prod="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$status_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_prod=$prod_code;	
				}
				
				$tod_weeks_row=count(array_unique(explode(",",$tod_weeks)));
				if($tod_weeks_row>1)
				{
					 $view_button_tod="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$status_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_tod=$tod_weeks;	
				}
				
				$opd_week_row=count(array_unique(explode(",",$opd_week)));
				if($opd_week_row>1)
				{
					 $view_button_opd="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$status_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_opd=$opd_week;	
				}
				$opd_recv_date_row=count(array_unique(explode(",",$opd_recv_date)));
				if($opd_recv_date_row>1)
				{
					 $view_button_opd_date="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$status_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_opd_date=$opd_recv_date;	
				}
				$po_no_row=count(array_unique(explode(",",$po_no)));
				if($po_no_row>1)
				{
					 $view_button_po="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$status_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_po=$po_no;	
				}
				$dealing_marchant_con_row=count(array_unique(explode(",",$dealing_marchant_con)));
				$bh_mer_con_row=count(array_unique(explode(",",$bh_mer)));
				$seasion_cond_row=count(array_unique(explode(",",$seasion_cond)));
				$construction_cond_row=count(array_unique(explode(",",$construction)));
				$gsm_weight_cond_row=count(array_unique(explode(",",$gsm_weight)));
				$yarn_count_value_cond_row=count(array_unique(explode(",",$yarn_count_value)));
				$style_cond_row=count(array_unique(explode(",",$style)));
				$copm_cond_row=count(array_unique(explode(",",$copm_one_name)));
				
				if($dealing_marchant_con_row>1)
				{
					 $view_button_deal="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$status_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_deal=$dealing_marchant_con;
				}
				if($bh_mer_con_row>1)
				{
					 $view_button_bh="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$status_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					
					$view_button_bh=$bh_mer;
					//$view_button_season=$seasion_cond;	
				}
				if($seasion_cond_row>1)
				{
					 $view_button_season="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$status_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_season=$seasion_cond;	
				}
				if($construction_cond_row>1)
				{
					 $view_button_const="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$status_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_const=$construction;	
				}
				if($gsm_weight_cond_row>1)
				{
					 $view_button_gsm="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$status_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_gsm=$$gsm_weight;	
				}
				if($yarn_count_value_cond_row>1)
				{
					 $view_button_count="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$status_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_count=$yarn_count_value;	
				}
				if($style_cond_row>1)
				{
					 $view_button_style="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$status_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_style=$style;	
				}
				if($copm_cond_row>1)
				{
					 $view_button_comp="<a href='##' value='View' onClick=\"fn_week_wise_detail_popup('".$job_no."','".rtrim($status_val['po_id'],',')."','".$tod_key."','".$status_key."','".$date_start."','".$date_end."','".$weeksIds."','".$cbo_search_type."','week_wise_detail_data')\"> View<a/>";
				}
				else
				{
					$view_button_comp=$copm_one_name;	
				}
				
				?>
                <tr style="font:'Arial Narrow'" align="center" bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
					<td width="30"><? echo $i; ?></td>
                    <td width="55" style="word-wrap:break-word; word-break: break-all;"><? echo $view_button_season;?></td>
                    <td width="100" style="word-wrap:break-word; word-break: break-all;" ><? echo $view_button_bh; ?></td> 
					<td width="140" style="word-wrap:break-word; word-break: break-all;" ><? echo $view_button_deal; ?></td> 
                    <td width="55" style="word-wrap:break-word; word-break: break-all;">
                   
					<? echo $view_button;//$buyer_short_name_library[$row_data[csf('buyer_name')]]; ?></td>
					<td  width="100" style="word-wrap:break-word; word-break: break-all;"><? echo $view_button_style; ?></td>
                   
                    <td width="60" style="word-wrap:break-word; word-break: break-all;"><? echo $percent_all; ?></td>
                    <td  width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $view_button_comp; ?></td>
                    <td  width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $view_button_const;?></td>
                    <td width="50"  style="word-wrap:break-word; word-break: break-all;"><? echo $view_button_gsm; ?></td>
					<td  width="120" style="word-wrap:break-word; word-break: break-all;"><? echo $view_button_count ?></td>
                    <td  width="70" title="<? echo $yarn_req;?>" style="word-wrap:break-word; word-break: break-all; text-align:right">
					<?
							echo number_format($yarn_req);
					?>
                    </td>
					<td  width="60" style="word-wrap:break-word; word-break: break-all;">
					<?
					
					if($status_key==2)
					{
						echo "Booked";	
					}
					else
					{
						echo "Placed";
					}
					//echo $order_status[$row_data[csf('is_confirmed')]];
					?> 
                    </td>
					<td  width="45" style="word-wrap:break-word; word-break: break-all;">
					<?
					//if($opd_week!='') echo "W:".$opd_week; else echo '';
					echo $view_button_opd;
					?>
                    </td>
					<td  width="60" title="Po Recv Date" style="word-wrap:break-word; word-break: break-all;">
                    
					<?
					if($opd_recv_date!="" && $opd_recv_date !="0000-00-00" && $opd_recv_date!="0")
					{
						$opd_recv_date=$opd_recv_date;
					}
					echo $view_button_opd_date;
					?>
                      
                    </td>
					<td width="45" title="Orgi Ship Date" style="word-wrap:break-word; word-break: break-all; text-align: center">
					<? echo $view_button_tod; ?>
                    </td>
                    <td  width="70" style="word-wrap:break-word; word-break: break-all; text-align:right">
					<? echo number_format($tot_po_qty);?>
                    </td>
					<td  width="100" style="word-wrap:break-word; word-break: break-all; text-align: center">
                  
					<? echo $view_button_po; ?>
                    </td>
                    <td  width="40" style="word-wrap:break-word; word-break: break-all; text-align:left" >
					<? echo $view_button_prod;?>
                    </td>
					<td  width="40" title="TOD-OPD" style="word-wrap:break-word; word-break: break-all; text-align: center">
					<? //echo $lead_time; ?>
                    </td>
					<td  width="50" title="PO Value/Po Qty" style="word-wrap:break-word; word-break: break-all; text-align:right">
					<? echo number_format($unit_price,2); ?>
                    </td>
                   
					<td width="90" title="Po Qty*Unit Price" style="word-wrap:break-word; word-break: break-all;text-align:right">
                   <? echo number_format($tot_po_qty*$unit_price,0);
				  	 $set_smv=number_format($status_val['set_smv'],2);
				    ?>
                    </td>
					<td width="40" title="Set SMV/Set Ratio" style="word-wrap:break-word; word-break: break-all;">
					<? 
					$smv_rate=0;
					$smv_rate=$set_smv/$status_val['set_ratio'];
					echo number_format($smv_rate,2); ?>
                    </td>
					
                    <td  width="90" title="Set SMV Per Pcs*Order Qty" style="word-wrap:break-word; word-break: break-all;text-align:right">
					<? echo number_format(($set_smv/$status_val['set_ratio'])*$tot_po_qty); ?>
                    </td>
                     <td  width="60" style="word-wrap:break-word; word-break: break-all;text-align:right">
					<? echo number_format($exfactory_data_array[$status_key][ex_qnty]); ?>
                    </td>
                    <td width="60" title="Ex-Fact Qty*Unit Price" style="word-wrap:break-word; word-break: break-all;text-align:right">
                    <? echo  number_format($exfactory_data_array[$status_key][ex_qnty]*$unit_price,0);
					 ?>
                    </td>
                    <?
					
					$week_qty=0;
					$week_check=array();
                   	foreach($week_counter_header as $mon_key=>$week)
					{
						foreach($week as $week_key)
						{
						?>
						<td  style="word-wrap:break-word; word-break: break-all; font-size:smaller" width="45" align="right">
						<? 
							 	$week_qty=$order_qty_arr[$week_key];
								if($week_qty>0) echo number_format($week_qty,0);else echo '';
								$tot_week_qty[$mon_key][$week_key]+=$week_qty;
								$tot_week_amount[$mon_key][$week_key]+=$week_qty*$unit_price;
								$smv_avg=0;
								$smv_avg=$smv_rate*$week_qty;
								$month_smv_arr[$mon_key]+=$smv_avg;
						?>
						</td>
						<?
						}
					}
					?>
                    </tr>
                    <?
					$total_order_qty+=$tot_po_qty;
					$total_yarn_req_qty+=$yarn_req;	
					$total_ex_fact_qty+=$exfactory_data_array[$status_key][ex_qnty];
					$total_ex_fact_val+=$exfactory_data_array[$status_key][ex_qnty]*$unit_price;
					$total_order_value+=$tot_po_qty*$unit_price;
					$total_order_smv+=($set_smv/$status_val['set_ratio'])*$tot_po_qty;
					//$total_week_qty+=$week_qty;
					//=====================================================================================================================
				$i++;
				}
				?>
				</table>
				</div>
                <table class="tbl_bottom" style="font:'Arial Narrow'; font-size:9px" width="<? echo $td_width;?>px" cellpadding="0" cellspacing="0" border="1" rules="all" >
					<tr>
					<td width="30">&nbsp;</td>
                    <td width="55">&nbsp;</td>
                    <td width="100">&nbsp;</td>
					<td width="140">&nbsp;</td>
                    <td width="55">&nbsp;</td>
					<td width="100">&nbsp;</td>
                    <td width="60">&nbsp;</td>
                    <td width="120">&nbsp;</td>
                    <td width="120">&nbsp;</td>
                    <td width="50">&nbsp;</td>
					<td width="120">&nbsp;</td>
                    <td width="70" style="word-wrap:break-word; word-break: break-all;break-all;font-size: smaller" id="td_yarn_req_march"><? echo $total_yarn_req_qty;?></td>
					<td width="60">&nbsp;</td>
					<td width="45">&nbsp;</td>
					<td width="60">&nbsp;</td>
					<td width="45">&nbsp;</td>
                    <td width="70" style="word-wrap:break-word; word-break: break-all;break-all;font-size: smaller" id="td_po_qty_march"><? echo $total_order_qty;?></td>
					<td width="100">&nbsp;</td>
                    <td width="40">&nbsp; </td>
					<td width="40">&nbsp;</td>
					<td width="50">&nbsp;</td>
					<td width="90" id="td_po_val_march" style="word-wrap:break-word; word-break: break-all;break-all;font-size: smaller"><? echo number_format($total_order_value);?></td>
					<td width="40"><? echo number_format($total_order_smv/$total_order_qty,0);?></td>
                    <td width="90" style="word-wrap:break-word; word-break: break-all;break-all;font-size: smaller" id="total_order_smv"><? echo number_format($total_order_smv,0);?></td>
                    <td width="60" style="word-wrap:break-word; word-break: break-all;break-all;font-size: smaller" id="total_order_exfc_qty"><? echo number_format($total_ex_fact_qty,0);?></td>
                    <td width="60" style="word-wrap:break-word; word-break: break-all;break-all;font-size: smaller" id="total_order_exfc_val"><? echo number_format($total_ex_fact_val,0);?></td>
                     <?
					//foreach($week_for_header as $week_key)
					$total_week_qty=0; $total_week_amt=0; 
					foreach($week_counter_header as $mon_key=>$week)
					{
						foreach($week as $week_key)
						{
						?>
						<td width="45" style="word-wrap:break-word; word-break: break-all;break-all;font-size: smaller"   align="center">
						
						<? 
						$total_week_qty=$tot_week_qty[$mon_key][$week_key];
						$total_week_amt=$tot_week_amount[$mon_key][$week_key];
						echo number_format($total_week_qty,0);
						$tot_mon_qty[$mon_key]+=$total_week_qty;
						$tot_mon_amount[$mon_key]+=$total_week_amt;
						?>
						</td>
						<?
						
						}
					}
            ?>
					</tr>
                    <tr style="display:none">
					<td colspan="26">&nbsp;</td>
                     <?
					//foreach($week_for_header as $week_key)
					$total_week_qt=0;
					foreach($week_counter_header as $mon_key=>$week)
					{
						
						?>
						<td width="" id="summary_footer_td_<? echo $mon_key?>" colspan="<? echo count($week);?>" title="<? echo $tot_week_amount[$mon_key][$week_key];?>" style="word-wrap:break-word; word-break: break-all;"   align="center">
						
						<? 
						//echo $tot_mon_qty[$mon_key].'='.$month_smv_arr[$mon_key].',';
						$tot_smv=0;
						$tot_smv=$month_smv_arr[$mon_key]/$tot_mon_qty[$mon_key];
						$head_td_data=$tot_mon_qty[$mon_key].'$'.$tot_mon_amount[$mon_key].'$'.$tot_smv;
						echo $head_td_data;
						?>
						</td>
						<?
					}
            ?>
                    </tr>
				</table>
			</fieldset>
		</div>
         <style>
			@font-face {
   				 font-family:"Arial Narrow"; font-size:11px;
			}
			TD{font-family:"Arial Narrow";font-size:10px;}
			TH{font-family:"Arial Narrow";font-size:11px;}
			
			.rpt_table TD{font-family: "Arial Narrow";font-size:11px;}
			.rpt_table TH{font-family:"Arial Narrow";font-size:11px;}
		</style>
	<?
	}
//===========================================================================================================================================================
	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data****$filename****$tot_rows****$cbo_search_type";
	exit();	
	
}

if($action=="report_generate_bh_summary") 
{
 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company_name=str_replace("'","",$cbo_company_name);

	$cbo_search_type=str_replace("'","",$cbo_search_type);
	$cbo_bh_mer_name=str_replace("'","",$cbo_bh_mer_name);
	$txt_yarn_count=str_replace("'","",$txt_yarn_count);
	if($txt_yarn_count!='') $yarn_count_con="and yarn_count Like '%$txt_yarn_count%' ";else $yarn_count_con='';
	$buyer_id_cond="";
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{
				$buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
				$buyer_id_cond2=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else
			{
				$buyer_id_cond="";$buyer_id_cond2="";
			}
		}
		else
		{
			$buyer_id_cond="";$buyer_id_cond2="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";
		$buyer_id_cond2=" and a.buyer_id=$cbo_buyer_name";
		//.str_replace("'","",$cbo_buyer_name)
	}
	

	
	$txt_job_no=str_replace("'","",$txt_job_no);
	$txt_job_no=trim($txt_job_no);
	if($txt_job_no !="" || $txt_job_no !=0)
	{
		//$year = substr(str_replace("'","",$cbo_year_selection), -2); 
		//$job_no=$company_library[$company_name]."-".$year."-".str_pad($txt_job_no, 5, 0, STR_PAD_LEFT);
		$jobcond="and a.job_no_prefix_num in (".$txt_job_no.")";
	}
	else
	{
		$jobcond="";	
	}
	
	//echo $jobcond.'fd';
	$date_cond='';$date_cond2='';
		if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
		{
			$start_date=(str_replace("'","",$txt_date_from));
			$end_date=(str_replace("'","",$txt_date_to));
			
			$date_cond="and b.pub_shipment_date between '$start_date' and '$end_date'";
			$date_cond2="and b.sales_target_date between '$start_date' and '$end_date'";
		}

	
	if(str_replace("'","",$txt_style_ref)!="") $style_ref_cond=" and a.style_ref_no like '%".str_replace("'","",$txt_style_ref)."%'"; else $style_ref_cond="";
	if(str_replace("'","",$txt_order_no)!="")
	{
		$ordercond=" and b.po_number like '%".str_replace("'","",$txt_order_no)."%'"; 
	}
	else
	{
		$ordercond="";
	}
	
	$team_cond="";
	if(str_replace("'","",$cbo_team_name)!=0) $team_cond=" and a.team_leader=".str_replace("'","",$cbo_team_name)."  ";
	if(str_replace("'","",$cbo_team_member)!=0) $team_cond.=" and a.dealing_marchant=".str_replace("'","",$cbo_team_member)."  ";
	$cbo_bh_mer_name=str_replace("0","",$cbo_bh_mer_name);
	if($cbo_bh_mer_name!=0 || $cbo_bh_mer_name!='') $bh_mer_name_con="and a.bh_merchant='$cbo_bh_mer_name' ";else $bh_mer_name_con='';
	 $cbo_year_selection=str_replace("'","",$cbo_year_selection);
	
	
	$start_ddd= strtotime(change_date_format($start_date,"dd-mm-yyyy","-"));
	$start_ddd=date("Y-m-d",$start_ddd);
	$start_end_date= strtotime(change_date_format($end_date,"dd-mm-yyyy","-"));
	$start_end_date=date("Y-m-d",$start_end_date);
		
	
	$dateee = strtotime($start_ddd);
	$start_end_date = strtotime($start_end_date);
	
	$date_start = date("Y-m-d",strtotime("-28 day", $dateee));
	$date_end = date("Y-m-d",strtotime("+28 day", $start_end_date));
	//echo $date_end;
	//$end_date_week=  addday("$end_date", time() -1814400);//$start_date;//$start_date;

	if($db_type==0)
	{
		$date_start= change_date_format($date_start,"yyyy-mm-dd");
		$date_end= change_date_format($date_end,"yyyy-mm-dd");
	}
	else
	{
		$date_start= change_date_format($date_start,"dd-mm-yyyy","-",1);
		$date_end= change_date_format($date_end,"dd-mm-yyyy","-",1);
	}
	
	//echo "select week_date,week from week_of_year where week_date between '$date_start' and  '$date_end' order by week_date asc";die;
	// date("d-m-Y", time() - 1296000);
	$week_for_arr=array();$no_of_week_for_header=array();
	$sql_week_header=sql_select("select week_date,week from week_of_year where week_date between '$date_start' and  '$date_end' order by week_date asc");
	$week_check_head=array();
	foreach ($sql_week_header as $row_week_header)
	{
		if($week_check_head[$row_week_header[csf("week")]]=='')
		{
			$week_counter_header[date("Y-M",strtotime($row_week_header[csf("week_date")]))][$row_week_header[csf("week")]]=$row_week_header[csf("week")];
			$week_check_head[$row_week_header[csf("week")]]=$week_counter_header[date("Y-M",strtotime($row_week_header[csf("week_date")]))][$row_week_header[csf("week")]];
		}
			
		$tmp=add_date($row_week_header[csf("week_date")],-1);
		if($db_type==0) $tmp_cond=date("d-m-y",strtotime($tmp));
		else $tmp_cond=date("d-M-y",strtotime($tmp));
		//echo $tmp_cond;
		$no_of_week_for_header_calc[$tmp_cond]=$row_week_header[csf("week")];	
		$no_of_week_for_header[$row_week_header[csf("week_date")]]=$row_week_header[csf("week")];
		$test[$row_week_header[csf("week")]]=$row_week_header[csf("week")];
		$month_by_week[$row_week_header[csf("week")]]=date("Y-M",strtotime($row_week_header[csf("week_date")]));
	}
	//print_r($month_by_week);
	unset($sql_week_header);
	$week_start_day=array();
	$week_end_day=array();$week_day_arr=array();
	//week_day
	$sql_week_start_end_date=sql_select("select week,min(week_date) as week_start_day, Max(week_date) as week_end_day from week_of_year where week_date between '$date_start' and  '$date_end' group by week");
	foreach ($sql_week_start_end_date as $row_week)
	{
		$week_start_day[$row_week[csf("week")]][week_start_day]=$row_week[csf("week_start_day")];
		$week_end_day[$row_week[csf("week")]][week_end_day]=$row_week[csf("week_end_day")];
		//$week_day_arr[$row_week[csf("week")]][$row_week[csf("week_start_day")]][week_first_day]=$row_week[csf("min_week_day")];
		$week_day_arr[$row_week[csf("week_end_day")]]['week_last_day']=$row_week[csf("last_week_day")];
	}
	unset($sql_week_start_end_date);
	//print_r($week_day_arr);
	
	function distribute_projection( $base_week, $qnty)
	{
		//5. Distribution ratios are: Base week 52%, Immediate before 24%, Week before immediate week 20%, Immediate week after base week 2%, next 2 weeks as 1%
		global $test;
		 
		$tmpbase_week=$test[$base_week-3];
		$k=0;
		for($i=0; $i<6; $i++)
		{
			$tmpbase_week++;
			if( $test[$tmpbase_week]=='') { $tmpbase_week=1; $k=0; }
			
			
			if($i==0) $distr_arr[$tmpbase_week]=($qnty*20)/100;
			if($i==1) $distr_arr[$tmpbase_week]=($qnty*24)/100;
			if($i==2) $distr_arr[$tmpbase_week]=($qnty*52)/100;
			if($i==3) $distr_arr[$tmpbase_week]=($qnty*2)/100;
			if($i==4) $distr_arr[$tmpbase_week]=($qnty*1)/100;
			if($i==5) $distr_arr[$tmpbase_week]=($qnty*1)/100;
			$k++;
			
		}
		 
		return $distr_arr;
		/*5000
		b=52
		b-1=21
		b-2=15
		b+1=1
		b+2=1*/
	}
//echo "<per>";
 //print_r(distribute_projection("2016-Dec",51, 5000)); die;
	
	if($template==1)
	{
		$po_wise_buyer=array();
		$buyer_wise_data=array();
		$po_id_arr=array();
		$wo_color_data_arr2=array();	
		
		//b.is_confirmed,min(c.country_ship_date) as country_ship_date,sum(c.order_quantity) as order_quantity,c.country_ship_date as  conf_country_ship_date
		
		$count_data=sql_select("select yarn_count,id from  lib_yarn_count  where status_active=1 and is_deleted=0 and yarn_count is not null $yarn_count_con");
		$yarn_id='';
		foreach($count_data as $row)
		{
				if($yarn_id=='') $yarn_id=$row[csf('id')];else $yarn_id.=",".$row[csf('id')];
				
		}

		$yarn_ids=count(array_unique(explode(",",$yarn_id)));
		$yarn_numIds=chop($yarn_id,','); $yarnIds_cond="";
		if($yarn_id!='' || $yarn_id!=0)
		{
			if($db_type==2 && $yarn_ids>1000)
			{
				$yarnIds_cond=" and (";
				$yIdsArr=array_chunk(explode(",",$yarn_numIds),990);
				foreach($yIdsArr as $ids)
				{
					$ids=implode(",",$ids);
					$yarnIds_cond.=" d.count_id  in($ids) or ";
				}
				$yarnIds_cond=chop($yarnIds_cond,'or ');
				$yarnIds_cond.=")";
			}
			else
			{
				$yarnIds_cond=" and  d.count_id  in($yarn_id)";
			}
		}
	$yarn_sql_data=("select a.bh_merchant,b.is_confirmed,b.id as po_id,d.count_id,d.copm_one_id,d.copm_two_id,d.percent_one,d.percent_two,sum(d.cons_qnty) as cons_qnty,e.construction,e.gsm_weight,b.shipment_date as orgi_ship_date from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fab_yarn_cost_dtls d,wo_pre_cost_fabric_cost_dtls e where a.job_no=b.job_no_mst  and c.job_no_mst=a.job_no and b.id=c.po_break_down_id and d.job_no=a.job_no and 
	e.id=d.fabric_cost_dtls_id and  c.item_number_id= e.item_number_id and e.job_no=a.job_no and e.body_part_id in(1,20) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1   and a.company_name=$company_name   $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $team_cond $yarnIds_cond group by   b.id ,d.count_id,d.copm_one_id,d.copm_two_id,d.percent_one,d.percent_two,e.construction,e.gsm_weight,a.bh_merchant,b.is_confirmed,b.shipment_date"); 
	$sql_result_y=sql_select($yarn_sql_data);
	$wo_fab_data_arr=array();
	$all_yarn_po_id="";
	foreach($sql_result_y as $row)
		{
			if($all_yarn_po_id=="") $all_yarn_po_id=$row[csf("po_id")]; else $all_yarn_po_id.=",".$row[csf("po_id")];
			
		}
		unset($sql_result_y);
		$yarn_po_ids=count(array_unique(explode(",",$all_yarn_po_id)));
		$yarnPo_numIds=chop($all_yarn_po_id,','); $yarnPoIds_cond="";
		if($txt_yarn_count!='')
		{
			if($all_yarn_po_id!='' && $all_yarn_po_id!=0)
			{
				if($db_type==2 && $yarn_po_ids>1000)
				{
					$yarnPoIds_cond=" and (";
					$yPoIdsArr=array_chunk(explode(",",$yarnPo_numIds),990);
					foreach($yPoIdsArr as $ids)
					{
						$ids=implode(",",$ids);
						$yarnPoIds_cond.=" b.id  in($ids) or ";
					}
					$yarnPoIds_cond=chop($yarnPoIds_cond,'or ');
					$yarnPoIds_cond.=")";
				}
				else
				{
					$yarnPoIds_cond=" and  b.id  in($all_yarn_po_id)";
				}
			}
		}
		$sales_marchant_wise_arr=array();
		$sql_data_sales=("select a.buyer_id,(b.sales_target_qty) as target_qty,b.sales_target_date as target_date
		from wo_sales_target_mst a, wo_sales_target_dtls b where a.id=b.sales_target_mst_id  and a.is_deleted=0 and a.status_active=1 and a.company_id=$company_name $buyer_id_cond2 $date_cond2 order by  b.sales_target_date ");
		$sql_result_sales=sql_select($sql_data_sales);
		foreach($sql_result_sales as $row)
		{
					
			$sales_month_wise_arr[date("Y-M",strtotime($row[csf("target_date")]))][$row[csf('buyer_id')]]['sales_qty']+=$row[csf("target_qty")];
		}	
		//print_r($sales_month_wise_arr);
		$bhmarchant_wise_arr=array();$bhmarchant_wise_arr_qty=array();
		 $sql_data_c=("select b.id as po_id,a.bh_merchant,b.shipment_date as orgi_ship_date,b.is_confirmed,(b.po_quantity*a.total_set_qnty) as po_qty_pcs
		from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $team_cond $bh_mer_name_con ");
		$sql_result_c=sql_select($sql_data_c);
		foreach($sql_result_c as $row)
		{
			if($row[csf('is_confirmed')]==2)
			{
				if($row[csf('bh_merchant')]=='') $bh_merchant=0;else $bh_merchant=$row[csf('bh_merchant')];
				$tod_week=$no_of_week_for_header[$row[csf('orgi_ship_date')]];
				$bhmarchant_wise_arr_qty[$bh_merchant][$tod_week]['po_qty']+=$row[csf('po_qty_pcs')];
				$bhmarchant_wise_arr[$bh_merchant]['orgi_ship_date'].=$row[csf('orgi_ship_date')].',';
			}
		}	

	$sql_query=("select a.job_no_prefix_num as job_no,a.buyer_name,a.bh_merchant,b.id,b.is_confirmed,b.shipment_date as orgi_ship_date,
c.country_ship_date,c.order_quantity,b.po_quantity
	 from wo_po_details_master a, wo_po_break_down b
	  LEFT JOIN wo_po_color_size_breakdown c on  c.po_break_down_id=b.id and c.status_active=1
	 where a.job_no=b.job_no_mst   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1   and a.company_name=$company_name   $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $team_cond $yarnPoIds_cond  $bh_mer_name_con order by a.dealing_marchant,b.shipment_date ");
					 
	$sql_data=sql_select($sql_query);
	
	$tot_rows=count($sql_data);
	$marchant_wise_arr=array();$week_wise_order_qty_tod=array();
	$report_data=array();$order_qty=0;
	foreach( $sql_data as $row)
	{
			/*if($row[csf('is_confirmed')]==1)
			{
				if($db_type==0) $c_date_con=date("d-m-y",strtotime($row[csf('country_ship_date')]));
				else $c_date_con=date("d-M-y",strtotime($row[csf('country_ship_date')]));
				$week_key=$no_of_week_for_header_calc[$c_date_con];
				$week_wise_order_qty[$row[csf('bh_merchant')]][$week_key]['po_quantity']+=$row[csf("order_quantity")];
			}*/
			if($row[csf('bh_merchant')]=='') $bh_merchant=0;else $bh_merchant=$row[csf('bh_merchant')];
			$marchant_wise_arr[$bh_merchant]['is_confirmed'].=$row[csf('is_confirmed')].',';
			$marchant_wise_arr[$bh_merchant]['buyer_name'].=$row[csf('buyer_name')].',';
			$marchant_wise_arr[$bh_merchant]['bh_merchant']=$bh_merchant;
			if($row[csf('is_confirmed')]==2)// Projected
			{
				$marchant_wise_arr[$bh_merchant]['orgi_shipdate'].=$row[csf('orgi_ship_date')].',';
				$marchant_wise_arr[$bh_merchant]['po_qty']+=$row[csf('order_quantity')];
				
				
				//$tod_week=$no_of_week_for_header[$row[csf('orgi_ship_date')]];
				//$report_data[$row[csf('bh_merchant')]][$month_by_week[$tod_week]]['conf_po_qnty']+=$row[csf("order_quantity")];
				//$dd=distribute_projection($tod_week, $row[csf('order_quantity')]);
				//print_r($dd);
				//foreach($dd as $key=>$value){
					//$report_data[$row[csf('bh_merchant')]][$month_by_week[$key]]['proj_po_qnty']+=$value;
				//}
				//unset($dd);
			}
			else
			{ 
				if($db_type==0) $date_week_date=date("d-m-y",strtotime($row[csf('country_ship_date')]));
				else $date_week_date=date("d-M-y",strtotime($row[csf('country_ship_date')]));
				$c_week=$no_of_week_for_header_calc[$date_week_date];
				
				$marchant_wise_arr[$bh_merchant]['country_ship_date'].=$row[csf('country_ship_date')].',';
				$week_wise_order_qty[$bh_merchant][$row[csf('is_confirmed')]][$c_week]['po_quantity']+=$row[csf("order_quantity")];
				//$week_wise_order_qty[$row[csf('bh_merchant')]][$c_week]['po_quantity']=$row[csf("order_quantity")];
				//echo $date_week_cond2;
				//$c_week=$no_of_week_for_header_calc[$date_week_cond2];
				//echo $row[csf('order_quantity')].',';
				//$order_qty+=$week_wise_order_qty2[$row[csf('bh_merchant')]][$c_week]['po_quantity']=$row[csf("order_quantity")];
				//$report_data[$row[csf('bh_merchant')]][$month_by_week[$c_week]]['conf_po_qnty']+=$row[csf("order_quantity")];
				// print_r($report_data);
			} 
			//$report_data[$row[csf('bh_merchant')]][$month_by_week[$c_week]]['conf_po_qnty']+=$row[csf("order_quantity")];
			
	}
	//echo "<pre>";
	//print_r($report_data);
	//die;
	
		
		ob_start();
		$rowcount=0;
		foreach($week_counter_header as $mon_key=>$week)
		{
			$rowcount+=count($mon_key);
		}
		//echo $rowcount;
		// $rowb=$rowcount*2;
		 $td_width=300+($rowcount*90);
	  
	?>
		<div style="width:<? echo $td_width+20;?>px">
		<fieldset style="width:100%;">	
			<table width="<? echo $td_width+20;?>px">
				<tr class="form_caption">
					<td align="center" style="margin-left:400px"><strong>Monthly Order Receiving status (H&M Merchandiser Wise)</strong></td>
				</tr>
				<tr class="form_caption" align="center" style="display:none">
					<td align="center"><? //echo $company_library[$company_name]; ?></td>
				</tr>
			</table>
			<table style="font-size:9px" class="rpt_table" width="<? echo $td_width;?>" cellpadding="0" cellspacing="0" border="1" rules="all">
				<thead>
                  <tr>
                    <th width="30">SL</th>
                    <th width="150">Merchandiser</th>
                    <th width="110">Total.</th>
                     <?
					 $start_week_date='';$end_week_date='';
					foreach($week_counter_header as $mon_key=>$week)
					{
							
					?>
					<th width="90" align="center">
					<? 
					echo $mon_key;
					?>
					</th>
					<?
					}
            ?>
                </tr>
				</thead>
			</table>
			<div style="width:<? echo $td_width+20;?>px; max-height:245px; overflow-y:scroll" id="scroll_body">
			<table style="font-size:9px" class="rpt_table" width="<? echo $td_width;?>px" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
	<?
			 
				$i=1;$total_order_qty=0;$total_week_qty=0;$total_sales_qty=0;$all_buyer_ids='';$total_order_qty_up=0;
				foreach($marchant_wise_arr as $mech=>$merdata)
				{
					if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
						$is_confirmed=rtrim($marchant_wise_arr[$mech]['is_confirmed'],',');
						$is_confirmed=array_unique(explode(",", $is_confirmed));
						$c_ship_date=rtrim($marchant_wise_arr[$mech]['country_ship_date'],',');
						$orgi_shipdate=rtrim($marchant_wise_arr[$mech]['orgi_shipdate'],',');
						
						$buyer_ids=rtrim($marchant_wise_arr[$mech]['buyer_name'],',');
						$buyer_ids=array_unique(explode(",", $buyer_ids));
						//print_r($buyer_ids);
						
						foreach($buyer_ids as $buyerid)
						{
							if($all_buyer_ids!='') $all_buyer_ids=$buyerid;else $all_buyer_ids.=",".$buyerid;
						}
						
						foreach($is_confirmed as $conf_key)
						{
							$po_qty=0;
							if($conf_key==2) //Project 
							{
								$qnty_array_mm=array();$proj_po_qty=0;
								//$marchant_wise_arr[$mech]['po_qty'];
								$week_check=array();
								$po_orgi_shipdate=array_unique(explode(",", $orgi_shipdate));
								foreach($po_orgi_shipdate as $tod_key)
								{
									$tod_week=$no_of_week_for_header[$tod_key];
									//$qnty_array2=distribute_projection($tod_week, $po_qty);
									//unset($qnty_array2);
									if($week_check[$tod_week]=='')
									{
										$proj_po_qty=$bhmarchant_wise_arr_qty[$mech][$tod_week]['po_qty'];
										//echo $proj_po_qty;
										$qnty_arr=distribute_projection($tod_week, $proj_po_qty);
										foreach($qnty_arr as $key=>$val)
										{
											//echo $marchant_key.'='.$key.'='.$val.'<br/>';
											$qnty_array_mm[$key]+=$val;
											
										}
										$week_check[$tod_week]=$proj_po_qty;
									}
								}
									$order_qty=0;//
									foreach($week_counter_header as $mon_key=>$week)
									{
										
										foreach($week as $week_key)
										{
											 $order_qty=$qnty_array_mm[$week_key];
											 
											  $order_qty_arr[$mech][$mon_key]+=$order_qty;
											   $order_qty_arr_tot[$mech]+=$order_qty;
										}
									}
									//print_r($order_qty_arr);
							}
							else 
							{
								$c_ship_date_con=array_unique(explode(",",$c_ship_date));
								//$order_qty_arr=array();
								$qnty_array=array();
								foreach($c_ship_date_con as $c_date)
								{
									if($db_type==0) $c_date_con=date("d-m-y",strtotime($c_date));
									else $c_date_con=date("d-M-y",strtotime($c_date));
									//echo $week_wise_order_qty[$mech][$conf_key][$no_of_week_for_header_calc[$c_date_con]]['po_quantity'].'<br>';
									$qnty_array[$no_of_week_for_header_calc[$c_date_con]]=$week_wise_order_qty[$mech][$conf_key][$no_of_week_for_header_calc[$c_date_con]]['po_quantity'];									
								}
									$order_qty=0;
									foreach($week_counter_header as $mon_key=>$week)
									{
										//$order_qty_arr=array();
										foreach($week as $week_key)
										{
											 $order_qty=$qnty_array[$week_key];
											  $order_qty_arr[$mech][$mon_key]+=$order_qty;
											   $order_qty_arr_tot[$mech]+=$order_qty;
										}
									}
								//print_r($qnty_array2);
							}
						}
								
						//echo $all_buyer_ids;
				// print_r($order_qty_arr);
				?>
                <tr style="font-size:9px" align="center" bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
					<td width="30"><? echo $i; ?></td>
                     <td width="150" style="word-wrap:break-word; word-break: break-all;" ><? echo $mech; ?></td> 
					<td width="110" style="word-wrap:break-word; word-break: break-all;" align="right" ><? echo number_format($order_qty_arr_tot[$mech],0); ?></td> 
                    <?
					$tot_mon_qty=0;$tot_sales_qty=0;
					foreach($week_counter_header as $mon_key=>$weeks)
					{
							$tot_mon_qty=$order_qty_arr[$mech][$mon_key];
						?>
						<td   style="word-wrap:break-word; word-break: break-all;" width="90" align="right">
						<? 
							echo number_format($tot_mon_qty,2)//.'='.$mech;
						?>
						</td>
						<?
						$tot_week_qty_sum[$mon_key]+=$tot_mon_qty;
					}
					?>
                    </tr>
                    <?
					$total_order_qty+=$order_qty_arr_tot[$mech];
					$total_order_qty_up+=$order_qty_arr_tot[$mech];
					//=====================================================================================================================
				$i++;
				}
				?>
                 
				</table>
				</div>
                <table class="tbl_bottom" width="<? echo $td_width;?>px" cellpadding="0" cellspacing="0" id="report_table_footer" border="1" rules="all" >
					<tr>
					<td width="30">&nbsp;</td>
                    <td width="150">Month Wise Total:</td>
					<td width="110" id="total_month_qty" align="right" ><? echo number_format($total_order_qty,0);?></td>
                     <?
					$total_mon_qty=0;
					foreach($week_counter_header as $mon_key=>$week)
					{
						?>
						<td width="90"  style="word-wrap:break-word; word-break: break-all;font-size:smaller"  align="right">
						<? 
						 $total_mon_qty=$tot_week_qty_sum[$mon_key];
							echo number_format($total_mon_qty,0);	
						?>
						</td>
						<?
					}
					$all_buyerids=array_unique(explode(",",$all_buyer_ids));
					foreach($week_counter_header as $mon_key=>$week)
					{
							$tots_sales_qty=0;
							foreach($all_buyerids as $b_id)
							{
								$tots_sales_qty=$sales_month_wise_arr[$mon_key][$b_id]['sales_qty'];
								$totals_sales_qty+=$tots_sales_qty;
								
							}
					}
            ?>
					</tr>
                    <tr>
					<td width="30">&nbsp;</td>
                    <td width="150">H&M Booking plan:</td>
					<td width="110"><? echo number_format($totals_sales_qty);?></td>
                     <?
					// print_r($total_sales_qty_sum_arr);
					$tot_booking_plan=0;$all_buyer_ids=array_unique(explode(",",$all_buyer_ids));
					foreach($week_counter_header as $mon_key=>$week)
					{
							$totsales_qty=0;
							foreach($all_buyer_ids as $b_id)
							{
								$totsales_qty=$sales_month_wise_arr[$mon_key][$b_id]['sales_qty'];
								//$tot_sales_qty+=$totsales_qty;
								
							}
						?>
						<td width="90"  style="word-wrap:break-word; word-break: break-all;font-size:smaller"  align="right">
						<? 
						 //$total_week_qty=$tot_week_qty_sum[$mon_key];
							echo number_format($totsales_qty,0);	
						?>
						</td>
						<?
					}
            ?>
					</tr>
                     <tr>
					<td width="30">&nbsp;</td>
                    <td width="150">Yet to get:</td>
					<td width="110"  title="Month Wise Qty-Booking Plan Qty" ><? echo number_format(($total_order_qty-$totals_sales_qty),0);?></td>
                     <?
					$total_week_qty=0;
					foreach($week_counter_header as $mon_key=>$week)
					{
						 $yet_total_mon_qty=$tot_week_qty_sum[$mon_key];
						 $yet_totsales_qty=0;
							foreach($all_buyer_ids as $b_id)
							{
								 $yet_totsales_qty=$sales_month_wise_arr[$mon_key][$b_id]['sales_qty'];
							}
						?>
						<td width="90"  title="Month Wise Qty-Booking Plan Qty" style="word-wrap:break-word; word-break: break-all;font-size:smaller"  align="center">
						<? 
						 $yet_to_total_mon_qty=$yet_total_mon_qty-$yet_totsales_qty;
							echo number_format($yet_to_total_mon_qty,0);	
						?>
						</td>
						<?
					}
            ?>
					</tr>
                    <tr>
					<td width="30">&nbsp;</td>
                    <td width="150">%</td>
					<td width="110"><?
					
					$tot_yet_to_per=($total_order_qty-$totals_sales_qty);
					$tot_yet_to_perent=($tot_yet_to_per/$totals_sales_qty)*100;
					 echo number_format(($tot_yet_to_perent),2);?></td>
                     <?
					
					foreach($week_counter_header as $mon_key=>$week)
					{
						$yet_total_mon_qty_per=$tot_week_qty_sum[$mon_key];
						 $yet_totsales_qty_per=0;
							foreach($all_buyer_ids as $b_id)
							{
								 $yet_totsales_qty_per=$sales_month_wise_arr[$mon_key][$b_id]['sales_qty'];
							}
							
							 $yet_to_total_mon_qty_per=$yet_total_mon_qty_per-$yet_totsales_qty_per;
						?>
						<td width="90"  style="word-wrap:break-word; word-break: break-all;font-size:smaller"  align="center">
						<? 
						 $tot_perent=($yet_to_total_mon_qty_per/$yet_totsales_qty_per)*100;
							echo number_format($tot_perent,2);	
						?>
						</td>
						<?
					}
            ?>
					</tr>
                     
				</table>
			</fieldset>
		</div>
          <style>
			@font-face {
   				 font-family:"Arial Narrow"; font-size:11px;
   				 
			}
			TD{font-family:"Arial Narrow";font-size:11px;}
			TH{font-family:"Arial Narrow";font-size:11px;}
			
			.rpt_table TD{font-family: "Arial Narrow";font-size:11px;}
			.rpt_table TH{font-family:"Arial Narrow";font-size:11px;}
			
		</style>
	<?
	}
//===========================================================================================================================================================
	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data****$filename****$tot_rows****$cbo_search_type";
	exit();	
	
} //BH End


if($action=="week_wise_detail_data")
{
		echo load_html_head_contents("Week Details ", "../../../", 1, 1,'','','');
		extract($_REQUEST);
		$week_for_arr=array();$no_of_week_for_header=array();
		
		$sql_week_header=sql_select("select week_date,week from week_of_year where week_date between '$from_date' and  '$to_date' order by week_date asc");
		$week_check_head=array();
		foreach ($sql_week_header as $row_week_header)
		{
			if($week_check_head[$row_week_header[csf("week")]]=='')
			{
				$week_counter_header[date("Y-M",strtotime($row_week_header[csf("week_date")]))][$row_week_header[csf("week")]]=$row_week_header[csf("week")];
				$week_check_head[$row_week_header[csf("week")]]=$week_counter_header[date("Y-M",strtotime($row_week_header[csf("week_date")]))][$row_week_header[csf("week")]];
			}
			$tmp=add_date($row_week_header[csf("week_date")],-1);
			if($db_type==0) $tmp_cond=date("d-m-y",strtotime($tmp));
			else $tmp_cond=date("d-M-y",strtotime($tmp));
			$no_of_week_for_header_calc[$tmp_cond]=$row_week_header[csf("week")];
			$no_of_week_for_header[$row_week_header[csf("week_date")]]=$row_week_header[csf("week")];
			$test[$row_week_header[csf("week")]]=$row_week_header[csf("week")];
		}
		unset($sql_week_header);
		$week_start_day=array();
		$week_end_day=array();$week_day_arr=array();
		//where week_date between '$date_start' and  '$date_end'
		$sql_week_start_end_date=sql_select("select week,min(week_date) as week_start_day, Max(week_date) as week_end_day from week_of_year where week_date between '$from_date' and  '$to_date'  group by week");
		foreach ($sql_week_start_end_date as $row_week)
		{
			$week_start_day[$row_week[csf("week")]][week_start_day]=$row_week[csf("week_start_day")];
			$week_end_day[$row_week[csf("week")]][week_end_day]=$row_week[csf("week_end_day")];
			$week_day_arr[$row_week[csf("week_end_day")]]['week_last_day']=$row_week[csf("last_week_day")];
		}
		unset($sql_week_start_end_date);
		//echo $group_by_id.'dfdff';die;
			$type_arr=array(6,7,8);
			if(!in_array($type,$type_arr)) //|| $type!=7 || $type!=8
				{
					if($type==2)
					{
					$group_by_cond="and a.dealing_marchant=$group_by_id";	
					}
					else if($type==3) //Bh
					{
					$group_by_cond="and a.bh_merchant='$group_by_id'";	
					}
					else if($type==4)
					{
					$group_by_cond="and a.style_ref_no='$group_by_id'";	
					}
					else if($type==5)
					{
					$group_by_cond="and a.job_no='$group_by_id'";	
					}
					else if($type==9) //opd week
					{
						//$group_by_cond="and d.count_id=$group_by_id";	
					}
					else if($type==10) //prod Dept
					{
						$group_by_cond="and a.product_code='$group_by_id'";	
					}
					else if($type==11) //Tod Week
					{
						//$group_by_cond="and a.product_code=$group_by_id";	
					}
					else if($type==12) //Status 
					{
						$group_by_cond="and b.is_confirmed=$group_by_id";	
					}
				}
				else
				{
					if($type==6)
					{
						$comp_data=explode("**",$group_by_id);
						$group_by_cond="and d.copm_one_id=$comp_data[0] and d.copm_two_id=$comp_data[1]";	
					}
					else if($type==7)//const
					{
						$group_by_cond="and e.construction='$group_by_id'";	
					}
					else if($type==8) //Count
					{
						$group_by_cond="and d.count_id=$group_by_id";	
					}
					
				}
			
			$po_id=str_replace("'","",$po_id);
			$po_id=implode(",",array_unique(explode(",",$po_id)));
			$po_ids=count(array_unique(explode(",",$po_id)));
			$po_numIds=chop($po_id,','); $poIds_cond="";
			if($po_id!='' || $po_id!=0)
			{
				if($db_type==2 && $po_ids>999)
				{
					$poIds_cond=" and (";
					$poIdsArr=array_chunk(explode(",",$po_numIds),999);
					foreach($poIdsArr as $ids)
					{
						$ids=implode(",",$ids);
						$poIds_cond.=" b.id  in($ids) or ";
					}
					$poIds_cond=chop($poIds_cond,'or ');
					$poIds_cond.=")";
				}
				else
				{
					$poIds_cond=" and  b.id  in($po_id)";
				}
			}
		//echo $poIds_cond;die;
		
	if(!in_array($type,$type_arr)) //|| $type!=7 || $type!=8
		{	
		 $yarn_sql_data=("select a.dealing_marchant,b.is_confirmed,b.shipment_date as orgi_ship_date,b.id as po_id,d.count_id,d.copm_one_id,d.copm_two_id,d.percent_one,d.percent_two,sum(d.cons_qnty) as cons_qnty,e.construction,e.gsm_weight from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fab_yarn_cost_dtls d,wo_pre_cost_fabric_cost_dtls e where a.job_no=b.job_no_mst  and c.job_no_mst=a.job_no and b.id=c.po_break_down_id and d.job_no=a.job_no and 
	e.id=d.fabric_cost_dtls_id and  c.item_number_id= e.item_number_id and e.job_no=a.job_no and e.body_part_id in(1,20) and a.is_deleted=0 and a.status_active=1 
	and b.is_deleted=0 and b.status_active=1  and a.company_name=$companyID  and b.is_confirmed in($status_id) $group_by_cond  $poIds_cond group by 
	  b.id ,d.count_id,d.copm_one_id,d.copm_two_id,d.percent_one,d.percent_two,e.construction,e.gsm_weight,a.dealing_marchant,b.is_confirmed,b.shipment_date"); 
	$sql_result_y=sql_select($yarn_sql_data);
	$wo_fab_data_arr=array();
	foreach($sql_result_y as $row)
		{
			$wo_fab_data_arr[$row[csf('dealing_marchant')]][$row[csf('is_confirmed')]]['percent_one']=$row[csf('percent_one')];
			$wo_fab_data_arr[$row[csf('dealing_marchant')]][$row[csf('is_confirmed')]]['percent_two']=$row[csf('percent_two')];
			$wo_fab_data_arr[$row[csf('dealing_marchant')]][$row[csf('is_confirmed')]]['copm_two_id']=$row[csf('copm_two_id')];
			$wo_fab_data_arr[$row[csf('dealing_marchant')]][$row[csf('is_confirmed')]]['copm_one_id']=$row[csf('copm_one_id')];
			$wo_fab_data_arr[$row[csf('dealing_marchant')]][$row[csf('is_confirmed')]]['construction'].=$row[csf('construction')].',';
			$wo_fab_data_arr[$row[csf('dealing_marchant')]][$row[csf('is_confirmed')]]['gsm_weight'].=$row[csf('gsm_weight')].',';
			$wo_fab_count_data_arr[$row[csf('dealing_marchant')]][$row[csf('is_confirmed')]]['count_id'].=$row[csf('count_id')].",";
			//$wo_yarn_data_arr[$row[csf('po_id')]]['cons_qnty']+=$row[csf('cons_qnty')];
		}
		unset($sql_result_y);
	 }
	
		
		function distribute_projection( $base_week, $qnty)
		{
			//5. Distribution ratios are: Base week 52%, Immediate before 24%, Week before immediate week 20%, Immediate week after base week 2%, next 2 weeks as 1%
			global $test;
			 
			$tmpbase_week=$test[$base_week-3];
			$k=0;
			for($i=0; $i<6; $i++)
			{
				$tmpbase_week++;
				if( $test[$tmpbase_week]=='') { $tmpbase_week=1; $k=0; }
				
				
				if($i==0) $distr_arr[$tmpbase_week]=($qnty*20)/100;
				if($i==1) $distr_arr[$tmpbase_week]=($qnty*24)/100;
				if($i==2) $distr_arr[$tmpbase_week]=($qnty*52)/100;
				if($i==3) $distr_arr[$tmpbase_week]=($qnty*2)/100;
				if($i==4) $distr_arr[$tmpbase_week]=($qnty*1)/100;
				if($i==5) $distr_arr[$tmpbase_week]=($qnty*1)/100;
				$k++;
			}
			return $distr_arr;
			/*5000
			b=52
			b-1=21
			b-2=15
			b+1=1
			b+2=1*/
		}
				$rowcount=0;
		foreach($week_counter_header as $mon_key=>$week)
		{
			$rowcount+=count($week);
		}
		$rowb=$rowcount*2;
		 $td_width=1010+($rowcount*40);
	?>
    <script>
		function print_window()
		{
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			$("#table_body_merchant tr:first").hide();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
			'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('print_report').innerHTML+'</body</html>');
			d.close();
			$("#table_body_merchant tr:first").show();
		}	
	</script>	
    
		<fieldset style="width:<? echo $td_width+20;?>px; margin-left:3px">
 		<div style="width:800px;" align="center">
        	<input  type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
        </div>
        <div id="print_report" style="width:<? echo $td_width;?>px;">
			<table border="1" class="rpt_table" rules="all" style="font:'Arial Narrow'; font-size:9px"  width="<? echo $td_width;?>" cellpadding="0" cellspacing="0" align="center">				<thead>
                <tr>
                    <th width="30">Sl</th>
                    <th width="100">BH Mer</th>
                    <th width="100">Merchant</th>
                    <th width="50">Job</th>
                    <th width="40">Job Year</th>
                    <th width="80">Style Ref</th>
                    <th width="50">Comp%</th>
                    <th width="90">Comp Name</th>
                    <th width="80">Const.</th>
                    <th width="40">GSM</th>
                    <th width="60">Y/C</th>
                    <th width="70">Y. Qty</th>
                    <th width="40">OPD</th>
                    <th width="70">OPD Date</th>
                    
                    <th width="40">TOD</th>
                    <th width="80">Ord. Qty</th>
                    <th width="70">Ord SMV</th>
                  
					 <?
					//foreach($week_for_header as $week_key)
					foreach($week_counter_header as $mon_key=>$week)
					{
						foreach($week as $week_key)
						{
							$start_week_date=$week_start_day[$week_key][week_start_day];
							$end_week_date=$week_end_day[$week_key][week_end_day];
							if($db_type==2)
							{
								$fisrtday=date("D",strtotime(change_date_format($start_week_date,"dd-mm-yyyy","-")));
								$lastday=date("D",strtotime(change_date_format($end_week_date,"dd-mm-yyyy","-")));
							}
							else
							{
								$fisrtday=date("D",strtotime(change_date_format($start_week_date,"dd-mm-yyyy")));
								$lastday=date("D",strtotime(change_date_format($end_week_date,"dd-mm-yyyy")));
							}
							$weekstart_date=explode("-",change_date_format($start_week_date,"dd-mm-yyyy","-"));
							$weekstart_date=$weekstart_date[0].'/'.$weekstart_date[1];
						?>
						<th title="<? echo change_date_format($start_week_date,"dd-mm-yyyy","-").'('.$fisrtday.')'." To ".change_date_format($end_week_date,"dd-mm-yyyy","-").'('.$lastday.')'; ?>" width="40"  align="center">
						<? 
						echo 'W'.$week_key.'<br/>';
						?>
						</th>
						<?
						}
					}
            ?>
                   </tr>
				</thead>
                </table>
                <div style="width:<? echo $td_width+20;?>px; max-height:245px; overflow-y:scroll" id="scroll_body">
			<table style="font:'Arial Narrow'; font-size:9px"  class="rpt_table" width="<? echo $td_width;?>px" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body_merchant">
            <?
			if($db_type==0)
			{
				$year=" YEAR(a.insert_date) as year";
			}
			elseif($db_type==2)
			{
				$year="TO_CHAR(a.insert_date,'YYYY') as year";
			}
			
		/*	$sql_query=("select $date_dif,a.job_no,a.job_no_prefix_num as prefix_job_no,b.shipment_date as orgi_ship_date,a.set_smv,a.season  as season,a.product_code,a.bh_merchant,a.dealing_marchant,a.style_ref_no,b.id,b.is_confirmed,b.po_total_price,b.po_number,b.po_received_date,a.total_set_qnty as set_ratio,(b.po_quantity*a.total_set_qnty) as po_qty_pcs,a.avg_unit_price as unit_price,c.country_ship_date,c.order_quantity,d.count_id,d.copm_one_id,d.copm_two_id,d.percent_one,d.percent_two,e.construction,e.gsm_weight 
	from wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c,wo_pre_cost_fab_yarn_cost_dtls d,wo_pre_cost_fabric_cost_dtls e
	 
	 where a.job_no=b.job_no_mst and c.job_no_mst=a.job_no and b.id=c.po_break_down_id and d.job_no=a.job_no and 
	e.id=d.fabric_cost_dtls_id and  c.item_number_id= e.item_number_id and e.job_no=a.job_no  and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.is_deleted=0 and c.status_active=1   and a.company_name=$company_name $buyer_id_cond $date_cond $style_ref_cond $jobcond $ordercond $bh_mer_name_con $team_cond $yarnIds_cond order by d.copm_one_id,a.job_no "); */
	//echo $type;die;
	
	//echo $opd_dates;
	//print_r($group_by_id);
	//echo $group_by_id.'sdsds';
		if(!in_array($type,$type_arr)) //|| $type!=7 || $type!=8
			{
				if($type==2)
					{
					$group_by_cond="and a.dealing_marchant=$group_by_id";	
					}
					else if($type==3) //Bh
					{
					$group_by_cond="and a.bh_merchant='$group_by_id'";	
					}
					else if($type==4)
					{
					$group_by_cond="and a.style_ref_no='$group_by_id'";	
					}
					else if($type==5)
					{
					$group_by_cond="and a.job_no='$group_by_id'";	
					}
					else if($type==9) //opd week
					{
						
						$group_by_id=array_unique(explode(",",$group_by_id));
						$opd_dates='';
						foreach($group_by_id as $opd_date)
						{
							if($opd_dates=='') $opd_dates="'".$opd_date."'";else $opd_dates.=","."'".$opd_date."'";
						}
						$group_by_cond="and b.po_received_date in($opd_dates)";	
					}
					else if($type==10) //prod Dept
					{
						$group_by_cond="and a.product_code='$group_by_id'";	
					}
					else if($type==11) //Tod Week
					{
						$group_by_id=array_unique(explode(",",$group_by_id));
						$tod_dates='';
						foreach($group_by_id as $tod_date)
						{
							if($tod_dates=='') $tod_dates="'".$tod_date."'";else $tod_dates.=","."'".$tod_date."'";
						}
						$group_by_cond="and b.shipment_date in($tod_dates)";	
					}
					else if($type==12) //Status 
					{
						$group_by_cond="and b.is_confirmed=$group_by_id";	
					}
					
					$sql_data_c=("select b.id as po_id,b.is_confirmed,a.bh_merchant,c.order_quantity as order_quantity,c.country_ship_date as  conf_ship_date from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c
				where a.job_no=b.job_no_mst and  c.po_break_down_id=b.id and  c.job_no_mst=b.job_no_mst   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1  and c.status_active=1 and c.is_deleted=0   and a.company_name=$companyID  and b.is_confirmed in($status_id)  $group_by_cond $poIds_cond order by a.dealing_marchant ");
	$sql_result_c=sql_select($sql_data_c);$week_wise_order_qty_arr=array();
		foreach($sql_result_c as $rowc)
		{
				if($rowc[csf('is_confirmed')]==1) //Confirm
				{
					if($db_type==0) $date_week_cond=date("d-m-y",strtotime($rowc[csf('conf_ship_date')]));
					else $date_week_cond=date("d-M-y",strtotime($rowc[csf('conf_ship_date')]));
					
					$wo_color_data_arr2[$rowc[csf('po_id')]]['shipdate'].=$rowc[csf('conf_ship_date')].',';
					$week_wise_order_qty_arr['po_qty'][$rowc[csf("po_id")]][$no_of_week_for_header_calc[$date_week_cond]]+=$rowc[csf("order_quantity")];
					
				}
			
		}
		//print_r($week_wise_order_qty_arr);die;
		
			 $sql_data_po="select $year,a.job_no_prefix_num,a.set_smv,a.bh_merchant,a.dealing_marchant,a.style_ref_no,b.id as po_id,
			 b.is_confirmed,b.po_received_date,b.shipment_date as orgi_ship_date,a.total_set_qnty as set_ratio,(b.po_quantity*a.total_set_qnty) as po_qty_pcs from wo_po_details_master a, wo_po_break_down b
			  where a.job_no=b.job_no_mst   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1   
	   and a.company_name=$companyID  and b.is_confirmed in($status_id)  $group_by_cond $poIds_cond order by a.dealing_marchant";
			}
			else
			{
				if($type==6)
				{
					$comp_data=explode("**",$group_by_id);
					$group_by_cond="and d.copm_one_id=$comp_data[0] and d.copm_two_id=$comp_data[1]";	
				}
				else if($type==7)//const
				{
					$group_by_cond="and e.construction='$group_by_id'";	
				}
				else if($type==8) //Count
				{
					$group_by_cond="and d.count_id=$group_by_id";	
				}
				/*else if($type==9) //opd week
				{
					//$group_by_cond="and d.count_id=$group_by_id";	
				}
				else if($type==10) //prod Dept
				{
					 $group_by_cond="and a.product_code='$group_by_id'";	
				}
				else if($type==11) //Tod Week
				{
					//$group_by_cond="and a.product_code=$group_by_id";	
				}
				else if($type==12) //Status 
				{
					$group_by_cond="and b.is_confirmed=$group_by_id";	
				}*/
				//echo $group_by_cond.'gthgt';
				//echo $po_id;
				$po_id=implode(",",array_unique(explode(",",$po_id)));
				$po_id=str_replace("'","",$po_id);
				$po_ids=count(array_unique(explode(",",$po_id)));
				$po_numIds=chop($po_id,','); $poIds_conds="";
				if($po_id!='' || $po_id!=0)
				{
					if($db_type==2 && $po_ids>999)
					{
						//echo $po_ids.'sdsds';
						$poIds_conds=" and (";
						$poIdsArr=array_chunk(explode(",",$po_numIds),999);
						foreach($poIdsArr as $ids)
						{
							$ids=implode(",",$ids);
							$poIds_conds.=" b.id  in($ids) or ";
						}
						$poIds_conds=chop($poIds_conds,'or ');
						$poIds_conds.=")";
					}
					else
					{
						//echo $po_ids.'fff';
						$poIds_conds=" and  b.id  in($po_id)";
					}
				}
				//echo $poIds_cond;die;
				   $sql_data_po="select $year,a.job_no_prefix_num,a.set_smv,a.bh_merchant,a.dealing_marchant,a.style_ref_no,b.id,
			 b.is_confirmed,b.po_received_date,b.shipment_date as orgi_ship_date,a.total_set_qnty as set_ratio,(b.po_quantity*a.total_set_qnty) as po_qty_pcs,
			 c.order_quantity as order_quantity,c.country_ship_date as  conf_ship_date,c.order_quantity,d.count_id,d.copm_one_id,d.copm_two_id,d.percent_one,d.percent_two,e.construction,e.gsm_weight 
			  from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,
			  wo_pre_cost_fab_yarn_cost_dtls d,wo_pre_cost_fabric_cost_dtls e
			  
	 where a.job_no=b.job_no_mst  and  c.po_break_down_id=b.id and  c.job_no_mst=b.job_no_mst  and d.job_no=a.job_no and 
	e.id=d.fabric_cost_dtls_id and  c.item_number_id= e.item_number_id and e.job_no=a.job_no and e.body_part_id in(1,20) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1  
	   and a.company_name=$companyID  and b.is_confirmed in($status_id) $group_by_cond $poIds_conds order by a.dealing_marchant";
			}
			//echo $sql_data_po;
			//echo $poIds_cond ;die;
		$sql_data=sql_select($sql_data_po);
		foreach($sql_data as $row)
		{
				$conf_ship_date=$row[csf('conf_ship_date')];
			//$row[csf('conf_ship_date')];
			if($row[csf('is_confirmed')]==1)
			{
				if(!in_array($type,$type_arr)) //6,7,8
				{
					//echo 'fdss';
					//$week_wise_order_qty_arr[$row[csf('style_ref_no')]][$row[csf('is_confirmed')]][$no_of_week_for_header[$conf_ship_date]]['po_quantity']=$row[csf('order_quantity')];
				}
			}
		}
			$tot_rows=count($sql_data);
            $condition= new condition();
			 $condition->company_name("=$companyID");
			 if(str_replace("'","",$cbo_buyer_name)>0){
				 // $condition->buyer_name("=$cbo_buyer_name");
			 }
			 if(str_replace("'","",$job_no) !=''){
				  $condition->job_no_prefix_num("in($job_no)");
			 }
					if(str_replace("'","",$txt_date_from)!='' && str_replace("'","",$txt_date_to)!=''){
					  //$condition->pub_shipment_date(" between '$start_date' and '$end_date'");
				 	}
				
			 if(str_replace("'","",$po_id)!='')
			 {
				$condition->po_id("in($po_id)"); 
			 }
			 	$condition->init();
        		 $yarn= new yarn($condition);
				 // echo $yarn->getQuery(); die;
				 $yarn_req_qty_arr=$yarn->getOrderWiseYarnQtyArray();
				 $i=1;
				 foreach($sql_data as $row_data)
				 {
					  if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					  
				$country_ship_date=$row_data[csf('conf_ship_date')];
				$orgi_ship_date=$row_data[csf('orgi_ship_date')];
				$percent_one=$wo_fab_data_arr[$row_data[csf('dealing_marchant')]][$row_data[csf('is_confirmed')]]['percent_one'];
				$percent_two=$wo_fab_data_arr[$row_data[csf('dealing_marchant')]][$row_data[csf('is_confirmed')]]['percent_two'];
			 	$percent_con=$percent_one.$percent_two;
				if($percent_one!=0 && $percent_two!=0)
				{
					//if($percent_two!=0)
					$percent_all=$percent_one.'/'.$percent_two;
				}
				else if($percent_one!=0 && $percent_two==0)
				{
					//if($percent_two!=0)
					$percent_all=$percent_one;
				}
				else
				{
					$percent_all='';	
				}
				$copm_two_id=$wo_fab_data_arr[$row_data[csf('dealing_marchant')]][$row_data[csf('is_confirmed')]]['copm_two_id'];
				$copm_one_id=$wo_fab_data_arr[$row_data[csf('dealing_marchant')]][$row_data[csf('is_confirmed')]]['copm_one_id'];
				if($copm_one_id!=0 && $copm_two_id!=0)
				{ 
					$copm_one_name=$composition[$copm_one_id].'/'.$composition[$copm_two_id];
				}
				else if($copm_one_id!=0 && $copm_two_id==0)
				{ 
					$copm_one_name=$composition[$copm_one_id];
				
				}
				else 
				{
					$copm_one_name='';
				}
				$construction=rtrim($wo_fab_data_arr[$row_data[csf('dealing_marchant')]][$row_data[csf('is_confirmed')]]['construction'],',');
				$construction=implode(",",array_unique(explode(",",$construction)));
				$gsm_weight=rtrim($wo_fab_data_arr[$row_data[csf('dealing_marchant')]][$row_data[csf('is_confirmed')]]['gsm_weight'],',');
				$gsm_weight=implode(",",array_unique(explode(",",$gsm_weight)));
				$copm_one_name=implode(",",array_unique(explode(",",$copm_one_name)));
				
				$count_id=rtrim($wo_fab_count_data_arr[$row_data[csf('dealing_marchant')]][$row_data[csf('is_confirmed')]]['count_id'],',');
				$count_ids=array_unique(explode(",", $count_id));
				$yarn_count_value="";
				foreach($count_ids as $val)
				{
						if($yarn_count_value=="") $yarn_count_value=$lib_yarn_count_arr[$val]; else $yarn_count_value.=", ".$lib_yarn_count_arr[$val];
				}
				$yarn_req=$yarn_req_qty_arr[$row_data[csf('id')]];
				 $conf_country_ship_date=rtrim($wo_color_data_arr2[$row_data[csf('po_id')]]['shipdate'],',');
				//$conf_country_ship_date=array_unique(explode(",",$conf_country_ship_date));
 				/*$qnty_array_popup=array();
				if($row_data[csf('is_confirmed')]==1)
				{
						 $order_qty=$row_data[csf('order_quantity')];
						if($db_type==0) $c_date_con=date("d-m-y",strtotime($country_ship_date));
						else $c_date_con=date("d-M-y",strtotime($country_ship_date));
					$c_date_week=$no_of_week_for_header_calc[$c_date_con];
					$qnty_array_popup=$week_wise_order_qty_arr[$row_data[csf('style_ref_no')]][$row_data[csf('is_confirmed')]][$c_date_week]['po_quantity'];
				}
				else
				{
					$order_qty=$row_data[csf('po_qty_pcs')];
					$qnty_array_popup=distribute_projection($no_of_week_for_header[$orgi_ship_date], $row_data[csf('po_qty_pcs')]);
				}*/
				$status_key=$row_data[csf('is_confirmed')];
				if($status_key==1) //Confirm $conf_country_ship_date
				{
					$c_ship_date_con=array_unique(explode(",",$conf_country_ship_date));$qnty_array_dd=array();
					foreach($c_ship_date_con as $c_date)
						{
							//echo $c_date;
							if($db_type==0) $c_date_con=date("d-m-y",strtotime($c_date));
							else $c_date_con=date("d-M-y",strtotime($c_date));
							$c_week=$no_of_week_for_header_calc[$c_date_con];
							$qnty_array_dd[$c_week]=$week_wise_order_qty_arr['po_qty'][$row_data[csf("po_id")]][$c_week];
						}
					//print_r($qnty_array_dd);
						$tot_order_qty=0;$tot_order_qty_arr=array();
						foreach($week_counter_header as $mon_key=>$week)
						{
							//$order_qty_arr=array();
							foreach($week as $week_key)
							{
								 $tot_order_qty=$qnty_array_dd[$week_key];
								  $order_qty_arr[$week_key]=$tot_order_qty;
								  $tot_order_qty_arr[$row_data[csf("po_id")]]+=$tot_order_qty;
							}
						}
						
				}
				else 
				{
						
						$qnty_array_mm=array();
						$orgi_ship_date=$row_data[csf('orgi_ship_date')];
						 $proj_po_qty=$row_data[csf('po_qty_pcs')];
						 $qnty_array_mm=distribute_projection($no_of_week_for_header[$orgi_ship_date], $proj_po_qty);
					
							$order_qty=0;$tot_order_qty_arr=array();
							foreach($week_counter_header as $mon_key=>$week)
							{
								foreach($week as $week_key)
								{
									$order_qty=$qnty_array_mm[$week_key];
									$order_qty_arr[$week_key]=$order_qty;
									$tot_order_qty_arr[$row_data[csf("po_id")]]+=$order_qty;
								}
							}
				}
				
				$tot_po_qty=$tot_order_qty_arr[$row_data[csf("po_id")]];
					  
			?>
            <tr style="font:'Arial Narrow'; font-size:9px" align="center" bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
					<td width="30"><? echo $i; ?></td>
					<td width="100" style="word-wrap:break-word; word-break: break-all; text-align:left" ><? echo $team_member_arr[$row_data[csf('bh_merchant')]]; ?></td> 
                    <td width="100" style="word-wrap:break-word; word-break: break-all; text-align:left" ><? echo $team_member_arr[$row_data[csf('dealing_marchant')]]; ?></td> 
                  
                    <td width="50" style="word-wrap:break-word; word-break: break-all;"><? echo $row_data[csf('job_no_prefix_num')];//$buyer_short_name_library[$row_data[csf('shipment_date')]]; ?></td>
                     <td width="40" style="word-wrap:break-word; word-break: break-all;"><? echo $row_data[csf('year')];//$buyer_short_name_library[$row_data[csf('buyer_name')]]; ?></td>
					<td  width="80" style="word-wrap:break-word; word-break: break-all;text-align:left"><? echo $row_data[csf('style_ref_no')]; ?></td>
                   
                    
                    <td width="50" style="word-wrap:break-word; word-break: break-all;text-align:left"><? echo $percent_all; ?></td>
                    <td  width="90" style="word-wrap:break-word; word-break: break-all;"><? echo $copm_one_name; ?></td>
                    
                    
                    <td  width="80" style="word-wrap:break-word; word-break: break-all;text-align:left"><? echo $construction;?></td>
                    <td width="40"  style="word-wrap:break-word; word-break: break-all;"><? echo $gsm_weight; ?></td>
					<td  width="60" style="word-wrap:break-word; word-break: break-all;"><? echo $yarn_count_value; ?></td>
                    <td  width="70" style="word-wrap:break-word; word-break: break-all;text-align:right">
					<?
							echo number_format($yarn_req);
					?>
                    </td>
					
					<td  width="40" style="word-wrap:break-word; word-break: break-all;">
					<?
					if($no_of_week_for_header[$row_data[csf("po_received_date")]]!='') echo 'W'.$no_of_week_for_header[$row_data[csf("po_received_date")]];else echo '';
					
					?>
                    </td>
					<td  width="70" title="Po Recv Date" style="word-wrap:break-word; word-break: break-all;">
					<?
					if($row_data[csf('po_received_date')] !="" && $row_data[csf('po_received_date')] !="0000-00-00" && $row_data[csf('po_received_date')] !="0")
					{

					echo change_date_format($row_data[csf('po_received_date')],'dd-mm-yyyy','-');
					}
					?>
                    </td>
					<td width="40" title="Orgi. Ship Date=<? echo $orgi_ship_date;?>" style="word-wrap:break-word; word-break: break-all; text-align: center">
					<? 
					echo 'W'.$no_of_week_for_header[$orgi_ship_date];
					?>
                    </td>
                    <td  width="80" style="word-wrap:break-word; word-break: break-all; text-align:right">
					<? echo number_format($tot_po_qty);?>
                    </td>
                   
					<td width="70"  title="Set SMV Per Pcs*Order Qty" style="word-wrap:break-word; word-break: break-all;text-align:right">
                   <? //echo number_format($row_data[csf('po_qty_pcs')]*$row_data[csf('unit_price')],0);
				  	 $set_smv=$row_data[csf('set_smv')];
					 
					 echo number_format(($set_smv/$row_data[csf('set_ratio')])*$tot_po_qty);
				    ?>
                    </td>
                    <?
					$qnty_array=array();
					if($row_data[csf('is_confirmed')]==1) //Confirm
					{
						
							$qnty_array[$no_of_week_for_header_calc[$country_ship_date]]=$order_qty;
							//$qnty_array[$no_of_week_for_header[$country_ship_date]]=$week_wise_order_qty_arr[$row_data[csf('style_ref_no')]][$row_data[csf('is_confirmed')]][$no_of_week_for_header[$country_ship_date]]['po_quantity'];
							
					}
					else
					{
							$qnty_array[$no_of_week_for_header[$orgi_ship_date]]=$order_qty;
							//$qnty_array=distribute_projection($no_of_week_for_header[$row_data[csf('shipment_date')]], $row_data[csf('po_qty_pcs')]);
					}
					$week_qty=0;
					$week_check=array();
                   	foreach($week_counter_header as $mon_key=>$week)
					{
						foreach($week as $week_key)
						{
						?>
						<td  style="word-wrap:break-word; word-break: break-all;" width="40" align="right">
						<? 
							 	$week_qty=$order_qty_arr[$week_key];
								if($week_qty>0) echo number_format($week_qty,0);else echo ' ';
								$tot_week_qty[$mon_key][$week_key]+=$week_qty;
								//$tot_week_amount[$mon_key][$week_key]+=$week_qty*$row_data[csf('unit_price')];
							
						?>
						</td>
						<?
						}
					}
					?>
                    </tr>
                    <?
					$total_order_qty+=$tot_po_qty;
					$total_yarn_req_qty+=$yarn_req;
					//$total_order_value+=$row_data[csf('po_qty_pcs')]*$row_data[csf('unit_price')];
					//$total_week_qty+=$week_qty;
					$total_smv_qty+=($set_smv/$row_data[csf('set_ratio')])*$tot_po_qty;
					//=====================================================================================================================
				$i++;
				}
				?>
                
            </table>
            </div>
            <table class="tbl_bottom" style="font:'Arial Narrow'; font-size:9px" width="<? echo $td_width;?>px" cellpadding="0"  id="report_table_footer"  cellspacing="0" border="1" rules="all" >
					<tr>
					<td width="30">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="100">&nbsp;</td>
                    <td width="50">&nbsp;</td>
					<td width="40">&nbsp;</td>
                   
                    <td width="80">&nbsp;</td>
					<td width="50">&nbsp;</td>
                    <td width="90">&nbsp;</td>
                    <td width="80">&nbsp;</td>
                    <td width="40"></td>
                    <td width="60">&nbsp;</td>
					<td width="70"  style="word-wrap:break-word; word-break: break-all;" id="td_yarn_req"><? echo number_format($total_yarn_req_qty);?></td>
                    <td width="40">&nbsp;</td>
					<td width="70">&nbsp;</td>
					
					<td width="40">&nbsp;</td>
					<td width="80" style="word-wrap:break-word; word-break: break-all;" id="td_po_qty"><? echo number_format($total_order_qty);?></td>
                    <td width="70" style="word-wrap:break-word; word-break: break-all;" id="td_po_smv_qty"><?  echo number_format($total_smv_qty);?></td>
					
                     <?
					//foreach($week_for_header as $week_key)
					$total_week_qty=0; $total_week_amt=0; 
					foreach($week_counter_header as $mon_key=>$week)
					{
						foreach($week as $week_key)
						{
						?>
						<td width="40" title="<? echo $tot_week_amount[$mon_key][$week_key];?>" style="word-wrap:break-word; word-break: break-all;"   align="center">
						
						<? 
						$total_week_qty=$tot_week_qty[$mon_key][$week_key];
						$total_week_amt=$tot_week_amount[$mon_key][$week_key];
						echo number_format($total_week_qty,0);
						$tot_mon_qty[$mon_key]+=$total_week_qty;
						$tot_mon_amount[$mon_key]+=$total_week_amt;
						?>
						</td>
						<?
						
						}
						
					}
            ?>
					</tr>
                     
				</table>
 <script>
 	setFilterGrid("table_body_merchant",-1);
 </script>
     <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</div>
 </fieldset>
  <?  
  exit();            
 }
?>