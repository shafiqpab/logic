<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');
require_once('../../../includes/class4/class.conditions.php');
require_once('../../../includes/class4/class.reports.php');
require_once('../../../includes/class4/class.yarns.php');
require_once('../../../includes/class4/class.conversions.php');
require_once('../../../includes/class4/class.emblishments.php');
require_once('../../../includes/class4/class.commisions.php');
require_once('../../../includes/class4/class.commercials.php');
require_once('../../../includes/class4/class.others.php');
require_once('../../../includes/class4/class.trims.php');
require_once('../../../includes/class4/class.washes.php');
require_once('../../../includes/class4/class.fabrics.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$buyer_cond=set_user_lavel_filtering(' and buy.id','buyer_id');
$company_cond=set_user_lavel_filtering(' and comp.id','company_id');

//--------------------------------------------------------------------------------------------------------------------
$company_library=return_library_array( "select id, company_name from lib_company where status_active =1 and is_deleted=0", "id", "company_name"  );

$trims_lib_arr=return_library_array( "select id,item_name from lib_item_group where item_category=4 and status_active=1 and is_deleted=0 order by item_name", "id", "item_name"  );
$buyer_arr_library=return_library_array( "select id, buyer_name from lib_buyer where status_active =1 and is_deleted=0", "id", "buyer_name"  );

$team_leader_arr_library=return_library_array( "select id,team_leader_name from lib_marketing_team where status_active =1 and is_deleted=0", "id", "team_leader_name");

//$agent_arr_library=return_library_array( "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id  and a.id in (select  buyer_id from lib_buyer_party_type where party_type in (20,21)) order by a.buyer_name", "id", "buyer_name"  );
$agent_arr_library=return_library_array( "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id  and a.id in (select  buyer_id from lib_buyer_party_type where party_type in (20,21)) and a.status_active =1 and a.is_deleted=0 group by a.id,a.buyer_name order by buyer_name", "id", "buyer_name"  );
$order_arr=return_library_array("select id, po_number from wo_po_break_down", "id", "po_number");

$supplier_lib_arr=return_library_array("select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id  and b.party_type in(4,5) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name", "id", "supplier_name");

if($action=="load_drop_down_buyer")
{
	if($data!="")
	{
		echo create_drop_down( "cbo_buyer_name", 130, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in ($data) $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "");
	}
	else
	{
		echo create_drop_down( "cbo_buyer_name", 130, $blank_array,"", 1, "-- All Buyer --", $selected, "",0,"" );
	}   	 
	exit();
}

if($action=="load_drop_down_supplier")
{
	$data_ref=explode("_",$data);
	if($data_ref[1]==3 || $data_ref[1]==5)
	{
		echo create_drop_down( "cbo_supplier_name", 130,"select company_name,id from lib_company where status_active=1 and is_deleted=0 order by company_name",'id,company_name', 1, '-- Select Supplier --',0,0,1);
	}
	else
	{
		//echo "select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data_ref[0]' and b.party_type in(4,5) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name";die;
		echo create_drop_down( "cbo_supplier_name", 130,"select c.supplier_name,c.id from lib_supplier_tag_company a,lib_supplier_party_type b, lib_supplier c where c.id=b.supplier_id and a.supplier_id = b.supplier_id and a.tag_company='$data_ref[0]' and b.party_type in(4,5) and c.status_active=1 and c.is_deleted=0 group by c.id, c.supplier_name order by supplier_name",'id,supplier_name', 1, '-- Select Supplier --',0,0,1);
	}
	exit();
}

$tmplte=explode("**",$data);
if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_name=str_replace("'","",$cbo_company_name);
	$trims_name=str_replace("'","",$cbo_trims_name);
	$supplier_name=str_replace("'","",$cbo_supplier_name);
	$buyer_name=str_replace("'","",$cbo_buyer_name);



	
	if($supplier_name==0){	$supplier_name_cond="";	}else{	$supplier_name_cond=" and a.supplier_id in($supplier_name)";}
	
	if($trims_name==0){	$trims_name_cond="";}
	else{$trims_name_cond=" and b.item_group_id in ($trims_name)";}
	
	if($buyer_name==0){	$buyer_name_cond="";}
	else{$buyer_name_cond=" and c.buyer_id in ($buyer_name)";}
	

	//echo $buyer_id_cond;die;
	//if($year_from!=0 && $month_from!=0)
	//{

	$txt_date_from=str_replace("'","",$txt_date_from);

	
	
	$txt_date_to=str_replace("'","",$txt_date_to);
	$num_days = cal_days_in_month(CAL_GREGORIAN, $month_to, $year_to);
	$end_date=$year_to."-".$month_to."-$num_days";
	
	if($db_type==0) 
	{
		$date_conds=" and  receive_date between '$txt_date_from' and '$txt_date_to'";
		
	}
	if($db_type==2) 
	{
		$date_conds=" and  receive_date between '".date("j-M-Y",strtotime($txt_date_from))."' and '".date("j-M-Y",strtotime($txt_date_to))."'";
		
	}
	
	ob_start();
	$buyer_tem_arr=array();$agent_tem_arr=array();$date_arr=array();
		
	




	//echo '<pre>';print_r($sale_data_arr);
	 
	//var_dump($sale_data_arr['2015-01'][30]['target_qty']);
	//$noOfPo=count($poDataArray);

	$ex_com=explode(",",$company_name); $companyStr="";
	foreach($ex_com as $compid)
	{
		if($companyStr=="") $companyStr=$company_library[$compid]; else $companyStr.=', '.$company_library[$compid];
	}

	// $trims_data_sql="select a.id, a.recv_number, a.company_id, a.supplier_id, a.receive_date, b.booking_no,b.gmts_color_id,b.receive_qnty,b.trans_id,	b.prod_id,b.item_group_id,b.order_id,b.order_uom,b.item_description  from inv_receive_master  a,inv_trims_entry_dtls  b where  a.id=b.mst_id and a.company_id=$company_name $date_conds $supplier_name_cond $trims_name_cond ";
	

	$trims_data_sql="select a.id, a.recv_number, a.company_id, a.supplier_id, a.receive_date, b.booking_no,b.gmts_color_id,b.receive_qnty,b.trans_id, b.prod_id,b.item_group_id,b.order_id,b.order_uom,b.item_description,d.po_break_down_id,d.job_no,d.trim_group,d.wo_qnty,c.buyer_id,c.booking_date,c.delivery_date from inv_receive_master a,inv_trims_entry_dtls b 	left join wo_booking_mst c on b.booking_no=c.booking_no  left join wo_booking_dtls d on d.booking_no=c.booking_no   where a.id=b.mst_id  and a.status_active =1 and a.is_deleted=0 and b.status_active =1 and b.is_deleted=0 and d.status_active =1 and d.is_deleted=0 and c.status_active =1 and c.is_deleted=0 and a.company_id=$company_name $date_conds $supplier_name_cond $trims_name_cond $buyer_name_cond  group by a.id, a.recv_number, a.company_id, a.supplier_id, a.receive_date, b.booking_no,b.gmts_color_id,b.receive_qnty,b.trans_id, b.prod_id,b.item_group_id,b.order_id,b.order_uom,b.item_description,d.po_break_down_id,d.job_no,d.trim_group,d.wo_qnty,c.buyer_id,c.booking_date,c.delivery_date  order by a.id asc";

	// echo $trims_data_sql;
	$trims_data_array=sql_select($trims_data_sql);
	$main_arr=array();
	foreach($trims_data_array as $row)
	{
		$main_arr[$row[csf('recv_number')]][$row[csf('booking_no')]][$row[csf('trim_group')]]['recv_number']=$row[csf('recv_number')];
		$main_arr[$row[csf('recv_number')]][$row[csf('booking_no')]][$row[csf('trim_group')]]['company']=$row[csf('company_id')];
		$main_arr[$row[csf('recv_number')]][$row[csf('booking_no')]][$row[csf('trim_group')]]['supplier']=$row[csf('supplier_id')];
		$main_arr[$row[csf('recv_number')]][$row[csf('booking_no')]][$row[csf('trim_group')]]['receive_date']=$row[csf('receive_date')];
		$main_arr[$row[csf('recv_number')]][$row[csf('booking_no')]][$row[csf('trim_group')]]['booking_no']=$row[csf('booking_no')];
		$main_arr[$row[csf('recv_number')]][$row[csf('booking_no')]][$row[csf('trim_group')]]['booking_id']=$row[csf('booking_id')];
		$main_arr[$row[csf('recv_number')]][$row[csf('booking_no')]][$row[csf('trim_group')]]['gmts_color_id']=$row[csf('gmts_color_id')];			

		$main_arr[$row[csf('recv_number')]][$row[csf('booking_no')]][$row[csf('trim_group')]]['item_group_id']=$row[csf('trim_group')];

	
		$main_arr[$row[csf('recv_number')]][$row[csf('booking_no')]][$row[csf('trim_group')]]['order_uom']=$row[csf('order_uom')];
		$main_arr[$row[csf('recv_number')]][$row[csf('booking_no')]][$row[csf('trim_group')]]['item_description']=$row[csf('item_description')];
		$main_arr[$row[csf('recv_number')]][$row[csf('booking_no')]][$row[csf('trim_group')]]['wo_qnty']+=$row[csf('wo_qnty')];
		$main_arr[$row[csf('recv_number')]][$row[csf('booking_no')]][$row[csf('trim_group')]]['booking_date']=$row[csf('booking_date')];
		$main_arr[$row[csf('recv_number')]][$row[csf('booking_no')]][$row[csf('trim_group')]]['delivery_date']=$row[csf('delivery_date')];					
		$main_arr[$row[csf('recv_number')]][$row[csf('booking_no')]][$row[csf('trim_group')]]['job_no']=$row[csf('job_no')];
		$main_arr[$row[csf('recv_number')]][$row[csf('booking_no')]][$row[csf('trim_group')]]['buyer_name']=$buyer_arr_library[$row[csf('buyer_id')]];
		

		$bookingArr[$row[csf('booking_no')]]=$row[csf('booking_no')];
	
		$booking_orderid_arr[$row[csf('recv_number')]][$row[csf('booking_no')]][$row[csf('trim_group')]][$order_arr[$row[csf('po_break_down_id')]]]=$order_arr[$row[csf('po_break_down_id')]];
		$booking_job_arr[$row[csf('recv_number')]][$row[csf('booking_no')]][$row[csf('trim_group')]][$row[csf('job_no')]]=$row[csf('job_no')];

		$receive_qnty_arr[$row[csf('recv_number')]][$row[csf('booking_no')]][$row[csf('item_group_id')]]['receive_qnty']=$row[csf('receive_qnty')];
	}

	// echo "<pre>";
	// print_r($booking_style_arr);

	//====================================booking===========================================
	$sql= "select a.booking_no,a.booking_date,a.company_id,a.buyer_id,a.supplier_id,a.item_from_precost,a.delivery_date,a.delivery_address,a.trime_type,b.job_no, b.po_break_down_id,b.wo_qnty,b.trim_group,c.style_ref_no from wo_booking_mst a,wo_booking_dtls b left join wo_po_details_master c on b.job_no=c.job_no where  a.booking_no=b.booking_no and a.company_id=$company_name  and a.status_active =1 and a.is_deleted=0 ".where_con_using_array($bookingArr,1,'a.booking_no')."";

	// echo $sql;
	$data_array=sql_select($sql);
		

	foreach($data_array as $rows)
	{
		$booking_arr[$rows[csf('booking_no')]][$rows[csf('trim_group')]]['job_no']=$rows[csf('job_no')];
		$booking_arr[$rows[csf('booking_no')]][$rows[csf('trim_group')]]['buyer_id']=$rows[csf('buyer_id')];
		$booking_arr[$rows[csf('booking_no')]][$rows[csf('trim_group')]]['booking_date']=$rows[csf('booking_date')];
		$booking_arr[$rows[csf('booking_no')]][$rows[csf('trim_group')]]['delivery_date']=$rows[csf('delivery_date')];
		$booking_arr[$rows[csf('booking_no')]][$rows[csf('trim_group')]]['wo_qnty']+=$rows[csf('wo_qnty')];
		$booking_style_arr[$rows[csf('booking_no')]][$rows[csf('trim_group')]][$rows[csf('style_ref_no')]]=$rows[csf('style_ref_no')];
	}

	$total_qnty=0;
	$early_qnty=0;
	$dely_qnty=0;

	foreach($main_arr as $recv_id=>$booking_data)
	{
		foreach($booking_data as $booking_no=> $item_data)
		{
			foreach($item_data as $item_id=> $val)
			{ 
				
				if($receive_qnty_arr[$recv_id][$booking_no][$item_id]['receive_qnty']!="")
				{
					$delivery_date=$val['delivery_date'];
					$receive_date=$val['receive_date'];
					if($delivery_date!="")
					{				
							
						$recv_day=floor(strtotime($receive_date) / (60*60*24));
						$del_day=floor(strtotime($delivery_date) / (60*60*24));
						$days=$del_day-$recv_day;
					
						if($days>=0)
						{
							
							$early_qnty++;
						}
						else
						{
							$dely_qnty++;
						}
						$total_qnty++;	
					}
				}
			}
		}
	}	
	?>
	<br>
 	<fieldset style="width:1730px;"> 
 		<table style="margin-left: 1195px;" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table">
 			<thead>
 				<tr>
 					
                    <th width="100">Total Quant: <?=fn_number_format($total_qnty)?></th>
                    <th width="100">Early: <?=fn_number_format($early_qnty)?></th>                   
                    <th width="100">Dealy: <?=fn_number_format($dely_qnty)?></th>
                    <th width="100">Early: <?=fn_number_format(($early_qnty/$total_qnty)*100,2)?>%</th>
					<th width="100" style="color: red;">Dealy: <?=fn_number_format(($dely_qnty/$total_qnty)*100,2)?>% </th>
 				</tr>
 			</thead>
 		</table>   
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1700" class="rpt_table" >
            <thead>
            	<tr>
                    <th width="40">SL</th>
                    <th width="100">Supplier</th>
                    <th width="100">Buyer</th>
                    <th width="100">Job</th>                   
                    <th width="150">Order Number</th>
                    <th width="100">Style Ref</th>
					<th width="100">Trims Name</th>
                    <th width="100">Item Description</th>
                    <th width="100">WO Date</th>                   
                    <th width="100">WO Number</th>
                    <th width="100">WO Qty</th>
					<th width="100">Trims UOM</th>
                    <th width="100">Resevied ID</th>
                    <th width="100">In-House Qty</th>                   
                    <th width="100">Approxe Delivery Date</th>
                    <th width="100">In house Date</th>
					<th width="100">Dealy/Early</th>
                </tr>
            </thead>
        </table>
		<div style="width:1740px; overflow-y:scroll; max-height:330px;" id="scroll_body">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="1700" class="rpt_table" style="margin-left:15px;" id="table_body" >
				<? 

				$i=1;
				


				//  echo "<pre>";
				//  print_r($main_arr);


				$order_arr=array();
				foreach($main_arr as $recv_id=>$booking_data){
					foreach($booking_data as $booking_no=> $item_data){
						foreach($item_data as $item_id=> $val){ 
							
							if($receive_qnty_arr[$recv_id][$booking_no][$item_id]['receive_qnty']!=""){
							?>

						
					<tr>
						<td width="40"><?=$i;?></td>
						<td width="100"><?=$supplier_lib_arr[$val['supplier']];?></td>
						<td width="100"><?=$val['buyer_name'];?></td>
						<td width="100"><div style="word-wrap:break-word; width:80px"><?=implode(",",$booking_job_arr[$recv_id][$val['booking_no']][$item_id]);?></div></td>
						<td width="150"><div style="word-wrap:break-word; width:150px"><? echo implode(",",$booking_orderid_arr[$recv_id][$val['booking_no']][$item_id]);?>
						</div>
						</td>
						<td width="100"><div style="word-wrap:break-word; width:80px"><?=implode(",",$booking_style_arr[$val['booking_no']][$item_id]);?></div></td>
						<td width="100"><?=$trims_lib_arr[$val['item_group_id']];?></td>
						<td width="100"><div style="word-wrap:break-word; width:100px"><?=$val['item_description'];?></div></td>
						<td width="100" align="center"><?=$val['booking_date']?></td>
						<td width="100" align="center"><?=$val['booking_no'];?></td>
						<td width="100"><?=$booking_arr[$val['booking_no']][$item_id]['wo_qnty'];?></td>
						<td width="100"><?=$unit_of_measurement[$val['order_uom']];?></td>
						<td width="100" align="center"><?=$val['recv_number'];?></td>
						<td width="100" align="center"><?=$receive_qnty_arr[$recv_id][$booking_no][$item_id]['receive_qnty'];?></td>
						<td width="100" align="center"><?=$val['delivery_date'];?></td>
						<td width="100" align="center"><?=$val['receive_date'];?></td>
						<td width="100" align="center"><?
						$delivery_date=$val['delivery_date'];
						$receive_date=$val['receive_date'];
						if($delivery_date!=""){				
								// $diff = abs(strtotime($delivery_date) - strtotime($receive_date));
								// $days = floor($diff / (60*60*24));
							
								$recv_day=floor(strtotime($receive_date) / (60*60*24));
								$del_day=floor(strtotime($delivery_date) / (60*60*24));
								$days=$del_day-$recv_day;
							
								if($days>=0){?>
								<p><?=$days;?></p>
								<?}elseif($days<0){?>
								<p  style="color:red;"><?=$days;?></p>
								<?}
						}
						?></td>
					</tr>  	
				<?
						$i++;	}
				
			}}}?>	    
			</table>
		</div>                 
    </fieldset>
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

if($action=="report_generate_2")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_name=str_replace("'","",$cbo_company_name);
	$agent_name=str_replace("'","",$cbo_agent);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$buyer_id_cond="";
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{
				$buyer_id_cond=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
				$buyer_id_cond_2=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else
			{
				$buyer_id_cond="";
				$buyer_id_cond_2="";
			}
		}
		else
		{
			$buyer_id_cond="";
			$buyer_id_cond_2="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_id in ($cbo_buyer_name)";//.str_replace("'","",$cbo_buyer_name)
		$buyer_id_cond_2=" and a.buyer_name in ($cbo_buyer_name)";//.str_replace("'","",$cbo_buyer_name)
	}
	
	if($agent_name==0)
	{
	 $agent_cond="";
	 $agent_cond_order="";
	}
	else
	{
		$agent_cond=" and a.agent=$agent_name";
		$agent_cond_order=" and a.agent_name=$agent_name";
	}
	
	if(str_replace("'","",$cbo_team_leader)==0)
	{
		$team_leader_cond="";
	}
	else
	{
		$team_leader_cond=" and a.team_leader=$cbo_team_leader";
	}	
	
	//echo $buyer_id_cond;die;
	//if($year_from!=0 && $month_from!=0)
	//{
	$year_from=str_replace("'","",$cbo_year_from);
	$month_from=str_replace("'","",$cbo_month_from);
	$start_date=$year_from."-".$month_from."-01";
	
	$year_to=str_replace("'","",$cbo_year_to);
	$month_to=str_replace("'","",$cbo_month_to);
	$num_days = cal_days_in_month(CAL_GREGORIAN, $month_to, $year_to);
	$end_date=$year_to."-".$month_to."-$num_days";
	
	if($db_type==0) 
	{
		$date_cond_sales=" and b.sales_target_date between '$start_date' and '$end_date'";
		//$date_cond_order=" and c.country_ship_date between '$start_date' and '$end_date'";
		$date_cond_order=" and b.pub_shipment_date between '$start_date' and '$end_date'";
	}
	if($db_type==2) 
	{
		$date_cond_sales=" and b.sales_target_date between '".date("j-M-Y",strtotime($start_date))."' and '".date("j-M-Y",strtotime($end_date))."'";
		// $date_cond_order=" and c.country_ship_date between '".date("j-M-Y",strtotime($start_date))."' and '".date("j-M-Y",strtotime($end_date))."'";
		$date_cond_order=" and b.pub_shipment_date between '".date("j-M-Y",strtotime($start_date))."' and '".date("j-M-Y",strtotime($end_date))."'";	
	}
	
	ob_start();
	$buyer_tem_arr=array();$agent_tem_arr=array();$date_arr=array();
		
	/*	$sql_order= sql_select("select a.buyer_name,a.agent_name,a.team_leader, sum(c.order_quantity) as po_quantity,c.country_ship_date, sum(c.order_total) as amount,b.is_confirmed from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.job_no_mst=b.job_no_mst and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and c.is_deleted=0 and a.company_name='$company_name' ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $buyer_id_cond_2 $agent_cond_order $team_leader_cond $date_cond_order GROUP BY b.is_confirmed,a.buyer_name,a.agent_name,a.team_leader,c.country_ship_date order by a.buyer_name");
	*/		
	
	$sql_order= sql_select("select a.buyer_name,a.agent_name, a.set_smv,a.team_leader, sum(b.po_quantity*a.total_set_qnty) as po_quantity,b.pub_shipment_date as country_ship_date, sum(b.po_total_price/a.total_set_qnty) as amount,b.is_confirmed from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.status_active=1 and b.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and a.company_name in ($company_name) ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $buyer_id_cond_2 $agent_cond_order $team_leader_cond $date_cond_order GROUP BY b.is_confirmed,a.buyer_name,a.agent_name,a.set_smv,a.team_leader,b.pub_shipment_date order by a.team_leader asc");
	
	
	foreach ($sql_order as $row)
	{ 
		$key=$row[csf("buyer_name")].$row[csf("agent_name")].$row[csf("team_leader")];
		if($row[csf("is_confirmed")]==1)
		{
			$order_data_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$key]['confirmqty']+=$row[csf("po_quantity")];
			$order_data_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$key]['confirmamount']+=$row[csf("amount")];
			$order_data_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$key]['conf_min']+=$row[csf("set_smv")]*$row[csf("po_quantity")];
		}
		else
		{
			$order_data_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$key]['projectqty']+=$row[csf("po_quantity")];
			$order_data_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$key]['projectamount']+=$row[csf("amount")];
			$order_data_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$key]['proj_min']+=$row[csf("set_smv")]*$row[csf("po_quantity")];
		}
		if($row[csf("po_quantity")]>0) {
			$buyer_tem_arr[$row[csf("team_leader")]][$row[csf("buyer_name")]][$row[csf("agent_name")]]=$key;
		}

		$agent_tem_arr[$key]=$row[csf("agent_name")];
		$date_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))]=date("Y-m",strtotime($row[csf("country_ship_date")]));
		$team_leader_arr[$key]=$row[csf("team_leader")];	
	}
	//asort($team_leader_arr);
    //var_dump($date_arr);	

	$sql_sales=sql_select("select a.buyer_id,a.agent,a.team_leader, b.sales_target_date,b.sales_target_mint,b.sales_target_qty as sales_target_qty,b.sales_target_value from wo_sales_target_mst a, wo_sales_target_dtls b where a.id=b.sales_target_mst_id and a.status_active=1 and a.is_deleted=0 and a.company_id in ($company_name) ".set_user_lavel_filtering(' and a.buyer_id','buyer_id')." $buyer_id_cond $agent_cond $team_leader_cond  $date_cond_sales order by a.buyer_id");
	$sale_data_arr=array();
	foreach($sql_sales as $row)
	{
		$key=$row[csf("buyer_id")].$row[csf("agent")].$row[csf("team_leader")];
		$sale_data_arr[date("Y-m",strtotime($row[csf("sales_target_date")]))][$key]['target_qty']+=$row[csf("sales_target_qty")];
		$sale_data_arr[date("Y-m",strtotime($row[csf("sales_target_date")]))][$key]['target_val']+=$row[csf("sales_target_value")];
		$sale_data_arr[date("Y-m",strtotime($row[csf("sales_target_date")]))][$key]['sales_target_mint']+=$row[csf("sales_target_mint")];
		$sale_data_arr[date("Y-m",strtotime($row[csf("sales_target_date")]))][$key]['buyer_id']=$row[csf("buyer_id")];
		//$sale_data_arr[date("Y-m",strtotime($row[csf("sales_target_date")]))][$row[csf("buyer_id")]]['agent']=$row[csf("agent")];
		$date_arr[date("Y-m",strtotime($row[csf("sales_target_date")]))]=date("Y-m",strtotime($row[csf("sales_target_date")]));

		if($row[csf("sales_target_qty")]>0)	{
			$buyer_tem_arr[$key]=$row[csf("buyer_id")];
		}

		$agent_tem_arr[$key]=$row[csf("agent")];
		$team_leader_arr[$key]=$row[csf("team_leader")];
	
	} 
	//var_dump($sale_data_arr['2015-01'][30]['target_qty']);
	//$noOfPo=count($poDataArray);	
	//var_dump($sale_data_arr['2017-06']);		
		
	$total_month=count($date_arr);
	$width=$total_month*(3*90)+(450+100+150)+($total_month*20);
	//$width=($total_month*735)+100; 
	$colspan=$total_month;
	$colspan_mon=$total_month+3;
	asort($date_arr);
	asort($buyer_tem_arr);
	
	foreach($date_arr as $dateValue)
	{
		list($year,$month)=explode("-",$dateValue);	
		$newArr[$month][$year]=$dateValue;		
	}	
	
	$date_arr=array();
	foreach($newArr as $monthStageArr)
	{
		foreach($monthStageArr as $yearStageValue){
			$date_arr[$yearStageValue]=$yearStageValue;
		}
	}
	
	$ex_com=explode(",",$company_name); $companyStr="";
	foreach($ex_com as $compid)
	{
		if($companyStr=="") $companyStr=$company_library[$compid]; else $companyStr.=', '.$company_library[$compid];
	}
		
	//var_dump($date_arr);die;		
	?>
	<br>
 	<fieldset style="width:<? echo $width+20; ?>px;">
    	<table cellpadding="0" cellspacing="0" width="<? echo $width; ?>">
            <tr>
               <td align="center" width="100%" colspan="<? echo $total_month*2+5+1 ?>" class="form_caption" style="font-size:16px;"><? echo $companyStr; ?></td>
            </tr>
             <tr>
               <td align="center" width="100%" colspan="<? echo $total_month*2+5+1 ?>" class="form_caption"><strong>Sales Forecasting </strong></td>
            </tr>
        </table>	
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width; ?>" class="rpt_table" >
            <thead>
            	<tr>
                    <th width="40" rowspan="3">SL</th>
                    <th width="100" rowspan="3">Buyer Name</th>
                    <th width="100" rowspan="3">Agent Name</th>
                    <th rowspan="3">Team Leader</th>
                    <th width="100" rowspan="3">Particulars</th>
                    <?
						foreach($date_arr as $yearMonth=>$val)
						{
							list($year,$month)=explode("-",$yearMonth);
						?>
                        	<th width="200" colspan="3"><p><? echo  $year; ?></p></th>
                        <?	
						}
					?>
                <th rowspan="2" colspan="3" width="200">Total</th>
                </tr>
            	<tr>
                    <?
						foreach($date_arr as $yearMonth=>$val)
						{
							 $month_arr=explode("-",$yearMonth);
							$month_val=($month_arr[1]*1);
						?>
                        	<th width="200" colspan="3"><p><? echo  $months[$month_val]; ?></p></th>
                        <?	
						}
					?>
                </tr>
                <tr>
                	<?
						for($z=1;$z<=$total_month;$z++)
						{
						?>
                        	<th width="90">Quantity</th>
                            <th width="110">Value</th>
                            <th width="90">Minute</th>
                        <?	
						}
					?>
                    <th width="90">Quantity</th>
                    <th width="110">Value</th>
                    <th width="90">Minute</th>
                </tr>
            </thead>
        </table>
		<div style="width:<? echo $width+18; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width; ?>" class="rpt_table" id="table_body" >
				<? 
				$i=1;
                foreach($buyer_tem_arr as $teamid=>$teamdata)
                {
					foreach($teamdata as $buyer_id=>$buyerdata) 
					{
						foreach($buyerdata as $agent_id=>$key) 
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$sales_qnty=$sale_data_arr[$month_id][$key]['target_qty'];
							
							$agent_id=$agent_tem_arr[$key]
							?>
							<!-- //Forecast..................--> 
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_colors('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>"> 
								<td width="40" rowspan="4" valign="middle" align="center"><? echo $i; ?></td>
								<td width="100" rowspan="4" valign="middle"><p><? echo $buyer_arr_library[$buyer_id]; ?>&nbsp;</p></td>
								<td width="100" rowspan="4" valign="middle"><p><? echo  $agent_arr_library[$agent_id]; ?>&nbsp;</p></td>
								<td rowspan="4" valign="middle"><p><? echo $team_leader_arr_library[$teamid]; ?></p></td>
								<td width="100"><p>Forecast</p></td>
								<? 
								//$agent_arr_library
								$tot_sales_qty=0; $tot_sales_qnty_val=0; $z=1;$tot_sales_target_min=0;
								foreach($date_arr as $month_id=>$result)
								{
									//echo $buyer_id;
									$sales_qnty=$sale_data_arr[$month_id][$key]['target_qty'];
									$sales_qnty_val=$sale_data_arr[$month_id][$key]['target_val'];
									$sales_target_mint=$sale_data_arr[$month_id][$key]['sales_target_mint'];
									?>
									<td width="90" align="right"><? echo number_format($sales_qnty); ?></td>
									<td width="110" align="right"><? echo number_format($sales_qnty_val,2); ?></td>
                                    <td width="90" align="right"><? echo number_format($sales_target_mint,2); ?></td>
									<?
									$z++;
									$tot_sales_qty+=$sales_qnty;	
									$tot_sales_qnty_val+=$sales_qnty_val;
									$tot_sales_target_min+=$sales_target_mint;	
									$tot_sales_qty_month[$month_id]+=$sales_qnty;	
									$tot_sales_qnty_val_month[$month_id]+=$sales_qnty_val;	
									$tot_sales_target_min_month[$month_id]+=$sales_target_mint;	
								}
								?>
								<td align="right" width="90"><? echo number_format($tot_sales_qty); ?></td>
								<td align="right" width="110"><? echo number_format($tot_sales_qnty_val,2,'.',','); ?></td>
                                <td align="right" width="90"><? echo number_format($tot_sales_target_min,2,'.',','); ?></td>
							</tr>
		
							<!-- //Projection.........................--> 
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_colors('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>1"> 
							   <td width="100"><p>Projection</p></td>
								<? 
								//$agent_arr_library
								$tot_projectqty=$tot_proj_min=0; $tot_projectqty=0;$tot_projectamount=0; $z=1;
								foreach($date_arr as $month_id=>$result)
								{
									//echo $buyer_id;
									$projectqty=$order_data_arr[$month_id][$key]['projectqty'];
									$projectamount=$order_data_arr[$month_id][$key]['projectamount'];
									$proj_min=$order_data_arr[$month_id][$key]['proj_min'];
									?>
									<td width="90" align="right"><? echo number_format($projectqty); ?></td>
									<td width="90" align="right"><? echo number_format($projectamount,2); ?></td>
                                    <td width="90" align="right"><? echo number_format($proj_min,2); ?></td>
									<?
									$z++;
									$tot_projectqty+=$projectqty;	
									$tot_projectamount+=$projectamount;
									$tot_proj_min+=$proj_min;	
									$tot_projectqty_month[$month_id]+=$projectqty;	
									$tot_projectamount_month[$month_id]+=$projectamount;
									$tot_projectmin_month[$month_id]+=$proj_min;	
								}
								?>
								<td align="right" width="90"><? echo number_format($tot_projectqty,2,'.',','); ?></td>
								<td align="right"><? echo number_format($tot_projectamount,2,'.',','); ?></td>
                                <td align="right" width="90"><? echo number_format($tot_proj_min,2,'.',','); ?></td>
							</tr>
							<!-- //Confirm............................--> 
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_colors('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>2"> 
							   <td width="100"><p>Confirm</p></td>
								<? //$agent_arr_library
								$tot_confirmqty=$tot_conf_min=0; $tot_confirmamount=0; $z=1;
								foreach($date_arr as $month_id=>$result)
								{
									//echo $buyer_id;
									$confirmqty=$order_data_arr[$month_id][$key]['confirmqty'];
									$confirmamount=$order_data_arr[$month_id][$key]['confirmamount'];
									$conf_min=$order_data_arr[$month_id][$key]['conf_min'];
									?>
									<td width="90" align="right"><? echo number_format($confirmqty); ?></td>
									<td width="90" align="right"><? echo number_format($confirmamount,2); ?></td>
                                    <td width="90" align="right"><? echo number_format($conf_min,2); ?></td>
									<?
									$z++;
									$tot_confirmqty+=$confirmqty;	
									$tot_confirmamount+=$confirmamount;	
									$tot_conf_min+=$conf_min;	
									$tot_confirmqty_month[$month_id]+=$confirmqty;	
									$tot_confirmamount_month[$month_id]+=$confirmamount;
									$tot_confirmmin_month[$month_id]+=$conf_min;	
								}
								?>
								<td align="right" width="90"><? echo number_format($tot_confirmqty,2,'.',','); ?></td>
								<td align="right"><? echo number_format($tot_confirmamount,2,'.',','); ?></td>
                                <td align="right"  width="90"><? echo number_format($tot_conf_min,2,'.',','); ?></td>
							</tr>
		
							<!-- //Variance..........................--> 
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_colors('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>3"> 
							   <td width="100"><b>Variance</b></td>
								<? 
								//$agent_arr_library
								$tot_variance_qnty=0; $tot_variance_amount=$tot_variance_minute=0; $z=1;
								foreach($date_arr as $month_id=>$result)
								{
									//echo $buyer_id;										
									$sales_qnty=$sale_data_arr[$month_id][$key]['target_qty'];
									$sales_qnty_val=$sale_data_arr[$month_id][$key]['target_val'];
									$sales_target_mint=$sale_data_arr[$month_id][$key]['sales_target_mint'];
		
									$projectqty=$order_data_arr[$month_id][$key]['projectqty'];
									$projectamount=$order_data_arr[$month_id][$key]['projectamount'];
									
									$proj_min=$order_data_arr[$month_id][$key]['proj_min'];
									$conf_min=$order_data_arr[$month_id][$key]['conf_min'];
		
									$confirmqty=$order_data_arr[$month_id][$key]['confirmqty'];
									$confirmamount=$order_data_arr[$month_id][$key]['confirmamount'];
									
									
									$variance_qnty=($projectqty+$confirmqty)-$sales_qnty;
									$variance_amount=($projectamount+$confirmamount)-$sales_qnty_val;
									
									$variance_minute=($proj_min+$conf_min)-$sales_target_mint;
									$td_va_color=$td_vq_color='';
									if( $variance_qnty < 0){$td_vq_color="#f00";}
									if( $variance_amount < 0){$td_va_color="#f00";}
									?>
									<td width="90" align="right" bgcolor="<? echo $td_vq_color;?>"><b><? echo number_format($variance_qnty); ?></b></td>
									<td width="90" align="right" bgcolor="<? echo $td_va_color;?>"><b><? echo number_format($variance_amount,2); ?></b></td>
                                    <td width="90" align="right" bgcolor="<? echo $td_va_color;?>"><b><? echo number_format($variance_minute,2); ?></b></td>
									<?
									$z++;
									$tot_variance_qnty+=$variance_qnty;	
									$tot_variance_amount+=$variance_amount;	
									$tot_variance_minute+=$variance_minute;	
									$tot_variance_qnty_month[$month_id]+=$variance_qnty;	
									$tot_variance_amount_month[$month_id]+=$variance_amount;
									$tot_variance_minute_month[$month_id]+=$variance_minute;	
								}
								
								$td_va_color=$td_vq_color='';
								if( $tot_variance_qnty < 0){$td_vq_color="#f00";}
								if( ($tot_confirmamount+$tot_projectamount)-$tot_sales_qnty_val < 0){$td_va_color="#f00";}
								?>
								<td align="right" width="90" bgcolor="<? echo $td_vq_color;?>"><b><? echo number_format($tot_variance_qnty); ?></b></td>
								<td align="right" bgcolor="<? echo $td_va_color;?>"><b><? echo number_format(($tot_confirmamount+$tot_projectamount)-$tot_sales_qnty_val,2,'.',',');//echo number_format($tot_variance_amount,2,'.',','); ?></b></td>
                                <td align="right" width="90" bgcolor="<? echo $td_va_color;?>"><b><? echo number_format(($tot_proj_min+$tot_conf_min)-$tot_sales_target_min,2,'.',',');//echo number_format($tot_variance_amount,2,'.',','); ?></b></td>
							</tr>
		
							<?
							$i++;
							
							$total_sales_qty+=$tot_sales_qty;
							$total_tot_sales_qnty_val+=$tot_sales_qnty_val;
							$total_tot_target_min_val+=$tot_sales_target_min;
							
							$grand_projectqty+=$tot_projectqty;//grand_projectmin
							$grand_projectamount+=$tot_projectamount;
							$grand_projectmin+=$tot_proj_min;
							
							$grand_confirmqty+=$tot_confirmqty;
							$grand_confirmamount+=$tot_confirmamount;
							$grand_projamount+=$tot_conf_min;
							
							$grand_variance_qnty+=$tot_variance_qnty;
							$grand_variance_amount+=$tot_variance_amount;
							$grand_variance_minute+=$tot_variance_minute;
						}
					}
				}
			    ?>
                
            	<tfoot>
                	<tr style="background:#FF9">
                        <th rowspan="4" colspan="3" align="right">Total</th>
                        <td colspan="2" align="right"><b>Forecast</b></td>
						<?
                        foreach($date_arr as $month_id=>$result)
                        {
                            ?>
                            <td align="right"><b><? echo number_format($tot_sales_qty_month[$month_id]); ?></b></td>
                            <td align="right"><b><? echo number_format($tot_sales_qnty_val_month[$month_id],2,'.',','); ?></b></td>
                            <td align="right"><b><? echo number_format($tot_sales_target_min_month[$month_id],2,'.',','); ?></b></td>
                        	<?	
                        }
                        ?>
                        <td align="right"><b><? echo number_format($total_sales_qty); ?></b></td>
                        <td align="right"><b><? echo number_format($total_tot_sales_qnty_val,2,'.',','); ?></b></td>
                         <td align="right"><b><? echo number_format($total_tot_target_min_val,2,'.',','); ?></b></td>
                    </tr>

                	<tr>
                        <th colspan="2">Projection</th>
                        <?
                        foreach($date_arr as $month_id=>$result)
                        {
                            ?>
                        	<th align="right"><? echo number_format($tot_projectqty_month[$month_id]); ?></th>
                        	<th align="right"><? echo number_format($tot_projectamount_month[$month_id],2,'.',','); ?></th>
                            <th align="right"><? echo number_format($tot_projectmin_month[$month_id],2,'.',','); ?></th>
                        	<?	
                        }
                        ?>
                        <th align="right"><? echo number_format($grand_projectqty,2,'.',','); ?></th>
                        <th align="right"><? echo number_format($grand_projectamount,2,'.',','); ?></th>
                        <th align="right"><? echo number_format($grand_projectmin,2,'.',','); ?></th>
                    </tr>
                    
                    <tr>
                        <th colspan="2">Confirm</th>
                        <?
                        foreach($date_arr as $month_id=>$result)
                        {
                            ?>
                        	<th align="right"><? echo number_format($tot_confirmqty_month[$month_id]); ?></th>
                        	<th align="right"><? echo number_format($tot_confirmamount_month[$month_id],2,'.',','); ?></th>
                            <th align="right"><? echo number_format($tot_confirmmin_month[$month_id],2,'.',','); ?></th>
                        	<?	
                        }
                        ?>
                        <th align="right"><? echo number_format($grand_confirmqty); ?></th>
                        <th align="right"><? echo number_format($grand_confirmamount,2,'.',','); ?></th>
                        <th align="right"><? echo number_format($grand_projamount,2,'.',','); ?></th>
                    </tr>
                    
                	<tr style="background:#FF9">
                        <td align="right" colspan="2"><b>Variance(Proj+Conf)-Forecast</b></td>
                        <?
                        foreach($date_arr as $month_id=>$result)
                        {
							$td_va_color=$td_vq_color='';
							if( $tot_variance_qnty_month[$month_id] < 0){$td_vq_color="#f00";}
							if($tot_variance_amount_month[$month_id] < 0){$td_va_color="#f00";}						   
							?>
                        	<td align="right" bgcolor="<? echo $td_vq_color;?>"><b><? echo number_format($tot_variance_qnty_month[$month_id]); ?></b></td>
                        	<td align="right" bgcolor="<? echo $td_va_color;?>"><b><? echo number_format($tot_variance_amount_month[$month_id],2,'.',','); ?></b></td>
                            <td align="right" bgcolor="<? echo $td_va_color;?>"><b><? echo number_format($tot_variance_minute_month[$month_id],2,'.',','); ?></b></td>
                        	<?	
                        }								
						$td_va_color=$td_vq_color='';
						if( $grand_variance_qnty < 0){$td_vq_color="#f00";}
						if((($grand_projectamount+$grand_confirmamount)-$total_tot_sales_qnty_val) < 0){$td_va_color="#f00";}								
                        ?>
                        <td align="right" bgcolor="<? echo $td_vq_color;?>"><b><? echo number_format($grand_variance_qnty); ?></b></td>
                        <td align="right" bgcolor="<? echo $td_va_color;?>"><b><? echo number_format(($grand_projectamount+$grand_confirmamount)-$total_tot_sales_qnty_val,2,'.',',');//echo number_format($grand_variance_amount,2,'.',','); ?></b></td>
                          <td align="right" bgcolor="<? echo $td_va_color;?>"><b><? //echo number_format(($grand_projectamount+$grand_confirmamount)-$total_tot_sales_qnty_val,2,'.',',');
						  echo number_format($grand_variance_minute,2,'.',','); ?></b></td>
                    </tr>
                </tfoot>    
			</table>
		</div>                 
    </fieldset> 
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

if($action=="report_generate_3")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$company_name=str_replace("'","",$cbo_company_name);
	$agent_name=str_replace("'","",$cbo_agent);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$buyer_id_cond="";
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="")
			{
				$buyer_id_cond=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")";
				$buyer_id_cond_2=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")";
			}
			else
			{
				$buyer_id_cond="";
				$buyer_id_cond_2="";
			}
		}
		else
		{
			$buyer_id_cond="";
			$buyer_id_cond_2="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_id in ($cbo_buyer_name)";//.str_replace("'","",$cbo_buyer_name)
		$buyer_id_cond_2=" and a.buyer_name in ($cbo_buyer_name)";//.str_replace("'","",$cbo_buyer_name)
	}
	
	if($agent_name==0)
	{
		$agent_cond="";
		$agent_cond_order="";
	}
	else
	{
		$agent_cond=" and a.agent=$agent_name";
		$agent_cond_order=" and a.agent_name=$agent_name";
	}
	
	if(str_replace("'","",$cbo_team_leader)==0) $team_leader_cond=""; else $team_leader_cond=" and a.team_leader=$cbo_team_leader";
	
	//echo $buyer_id_cond;die;
	//if($year_from!=0 && $month_from!=0)
	//{
		
	$buyerTeamLdrArr=return_library_array( "select id, marketing_team_id from lib_buyer where status_active =1 and is_deleted=0", "id", "marketing_team_id");
	$year_from=str_replace("'","",$cbo_year_from);
	$month_from=str_replace("'","",$cbo_month_from);
	$start_date=$year_from."-".$month_from."-01";
	
	$year_to=str_replace("'","",$cbo_year_to);
	$month_to=str_replace("'","",$cbo_month_to);
	$num_days = cal_days_in_month(CAL_GREGORIAN, $month_to, $year_to);
	$end_date=$year_to."-".$month_to."-$num_days";
	
	if($db_type==0) 
	{
		$date_cond_sales=" and b.sales_target_date between '$start_date' and '$end_date'";
		$dateCondcapacity=" and b.date_calc between '$start_date' and '$end_date'";
		$date_cond_order=" and b.pub_shipment_date between '$start_date' and '$end_date'";
		$date_cond_qc=" and a.delivery_date between '$start_date' and '$end_date'";
	}
	if($db_type==2) 
	{
		$date_cond_sales=" and b.sales_target_date between '".date("j-M-Y",strtotime($start_date))."' and '".date("j-M-Y",strtotime($end_date))."'";
		$dateCondcapacity=" and b.date_calc between '".date("j-M-Y",strtotime($start_date))."' and '".date("j-M-Y",strtotime($end_date))."'";
		$date_cond_order=" and b.pub_shipment_date between '".date("j-M-Y",strtotime($start_date))."' and '".date("j-M-Y",strtotime($end_date))."'";
		$date_cond_qc=" and a.delivery_date between '".date("j-M-Y",strtotime($start_date))."' and '".date("j-M-Y",strtotime($end_date))."'";	
	}
	
	ob_start();
	$buyer_tem_arr=array(); $agent_tem_arr=array(); $date_arr=array();
		
	$sqlEffSlab="Select a.company_id, a.location_id, a.gmts_item_id, a.buyer_id, b.smv_lower_limit, b.smv_upper_limit, b.learning_cub_percentage from efficiency_percentage_slab_mst a, efficiency_percentage_slab_dtl b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$sqlEffRes=	sql_select($sqlEffSlab); $effSlabArr=array();
	foreach ($sqlEffRes as $row)
	{
		$excubPer=explode(",",$row[csf("learning_cub_percentage")]);
		$last_percentage=end($excubPer);
		for($k=$row[csf("smv_lower_limit")]; $k<=$row[csf("smv_upper_limit")]; $k++)
		{
			$effSlabArr[$row[csf("company_id")]][$row[csf("location_id")]][$row[csf("buyer_id")]][$row[csf("gmts_item_id")]][$k]=$last_percentage;
		}
	}
	unset($sqlEffRes);
	$sqlItemSmv="select job_no, gmts_item_id, smv_set from wo_po_details_mas_set_details";
	$sqlItemSmvData= sql_select($sqlItemSmv); $itemSmvArr=array();
	foreach($sqlItemSmvData as $irow)
	{
		$itemSmvArr[$irow[csf("job_no")]][$irow[csf("gmts_item_id")]]=$irow[csf("smv_set")];
	}
	unset($sqlItemSmvData);
	
	$sqlJobCm="select b.job_no, b.cm_cost, b.costing_per_id, b.total_cost, a.sew_effi_percent from wo_pre_cost_mst a, wo_pre_cost_dtls b where a.job_id=b.job_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
	$sqlJobCmData= sql_select($sqlJobCm); $jobCmArr=array();
	foreach($sqlJobCmData as $crow)
	{
		$cons_dzn=0;
		if($crow[csf("costing_per_id")]==1) $cons_dzn=12;
		if($crow[csf("costing_per_id")]==2) $cons_dzn=1;
		if($crow[csf("costing_per_id")]==3) $cons_dzn=24;
		if($crow[csf("costing_per_id")]==4) $cons_dzn=36;
		if($crow[csf("costing_per_id")]==5) $cons_dzn=48;
		$jobCmArr[$crow[csf("job_no")]]["cm"]=($crow[csf("cm_cost")]/$cons_dzn);
		$jobCmArr[$crow[csf("job_no")]]["eff"]=$crow[csf("sew_effi_percent")];
		$jobCmArr[$crow[csf("job_no")]]["tcost"]=($crow[csf("total_cost")]/$cons_dzn);
	}
	unset($sqlJobCmData);
	
	
	$condition= new condition();
	$condition->company_name("=$cbo_company_name");
	if(str_replace("'","",$cbo_buyer_name)>0){
		 $condition->buyer_name("=$cbo_buyer_name");
	}
	 
	if(str_replace("'","",$start_date)!='' && str_replace("'","",$end_date)!=''){
		 $condition->pub_shipment_date(" between '".date("j-M-Y",strtotime($start_date))."' and '".date("j-M-Y",strtotime($end_date))."'");
	}
	 
	$condition->init();
	$yarn= new yarn($condition);
	//echo $yarn->getQuery(); die;
	$yarn_costing_arr=$yarn->getOrderWiseYarnAmountArray();
	
	//print_r($yarn_costing_arr); die;
	$trims= new trims($condition);
	$trims_costing_arr=$trims->getAmountArray_by_order();

	$fabric= new fabric($condition);
	$fabric_costing_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
	
	$conversion= new conversion($condition);
	$conversion_costing_arr=$conversion->getAmountArray_by_orderAndProcess();
	//print_r($conversion_costing_arr_process[49054]); die;
	
	$emblishment= new emblishment($condition);
	$emblishment_costing_arr_name=$emblishment->getAmountArray_by_orderAndEmbname();
	
	
	$commercial= new commercial($condition);
	$commercial_costing_arr=$commercial->getAmountArray_by_order();
	$commission= new commision($condition);
	$commission_costing_arr=$commission->getAmountArray_by_orderAndItemid();
	$other= new other($condition);
	$other_costing_arr=$other->getAmountArray_by_order();
	
	$wash= new wash($condition);
	$washingCostArr=$wash->getAmountArray_by_orderAndEmbname();
	//print_r($washingCostArr); die;
	
	$sql_order= sql_select("select a.company_name, a.location_name, a.buyer_name, a.job_no, a.agent_name, a.team_leader, b.id, b.is_confirmed, b.pub_shipment_date as country_ship_date, c.item_number_id, c.order_quantity as po_quantity, (c.order_quantity/a.total_set_qnty) as poqtyuom, c.plan_cut_qnty as plan_cut, c.order_total as amount from wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and c.job_no_mst=b.job_no_mst and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.company_name in ($company_name) ".set_user_lavel_filtering(' and a.buyer_name','buyer_id')." $buyer_id_cond_2 $agent_cond_order $team_leader_cond $date_cond_order order by a.team_leader");
	
	foreach ($sql_order as $row)
	{ 
		$key=$row[csf("buyer_name")].$row[csf("agent_name")].$row[csf("team_leader")];
		
		$ordmin=$itemSmv=$effPer=$avlmin=0;
		$itemSmv=$itemSmvArr[$row[csf("job_no")]][$row[csf("item_number_id")]];
		$effPer=$effSlabArr[$row[csf("company_name")]][$row[csf("location_name")]][$row[csf("buyer_name")]][$row[csf("item_number_id")]][$itemSmv]/100;
		$ordmin=($row[csf("poqtyuom")]*$itemSmv)/$effPer;
		
		$jobcm=$jobCmArr[$row[csf("job_no")]]["cm"];
		$jobeff=$jobCmArr[$row[csf("job_no")]]["eff"];
		$jobCmVal=$row[csf("poqtyuom")]*$jobcm;
		$bomtotCost=$jobCmArr[$row[csf("job_no")]]["tcost"]*$row[csf("plan_cut")];
		$avlmin=($row[csf("poqtyuom")]*$itemSmv)/($jobeff/100);
		if($row[csf("is_confirmed")]==1)
		{
			$order_data_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$key]['confirmqty']+=$row[csf("po_quantity")];
			$order_data_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$key]['confirmamount']+=$row[csf("amount")];
			$order_data_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$key]['conf_min']+=$ordmin;
			$order_data_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$key]['conf_cmval']+=$jobCmVal;
			$order_data_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$key]['conf_cmCost']+=$jobcm;
			$order_data_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$key]['conf_avlmin']+=$avlmin;
			$order_data_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$key]['conf_cost']+=$bomtotCost;
		}
		else
		{
			$order_data_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$key]['projectqty']+=$row[csf("po_quantity")];
			$order_data_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$key]['projectamount']+=$row[csf("amount")];
			$order_data_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$key]['proj_min']+=$ordmin;
			$order_data_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$key]['proj_cmval']+=$jobCmVal;
			$order_data_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$key]['proj_cmCost']+=$jobcm;
			$order_data_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$key]['prof_avlmin']+=$avlmin;
			$order_data_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$key]['proj_cost']+=$bomtotCost;
		}
		if($row[csf("po_quantity")]>0) {
			$buyer_tem_arr[$row[csf("team_leader")]][$row[csf("buyer_name")]][$row[csf("agent_name")]]=$key;
		}
		
		$bomMatrialCost=$bomMatrialCostMar=$fab_purchase_knit=$conversion_cost=$emblCost=$washCost=$otherCost=$commercial_cost=$commission_cost=0;
		if (!in_array($row[csf("id")],$matrialPoArr) )
		{
			$fab_purchase_knit=array_sum($fabric_costing_arr['knit']['grey'][$row[csf('id')]])+array_sum($fabric_costing_arr['woven']['grey'][$row[csf('id')]]);
			//$conversion_cost=array_sum($conversion_costing_arr[$row[csf('id')]]);
			//
			//print_r($conversion_costing_arr[$row[csf('id')]]);
			foreach($conversion_costing_arr[$row[csf('id')]] as  $pid=>$pdata)
			{
				foreach($pdata as  $uomid=>$amtdata)
				{
					$conversion_cost+=$amtdata;
				}
			}
			$otherCostAll=0;
			$emblCost=array_sum($emblishment_costing_arr_name[$row[csf('id')]]);
			$washCost=$washingCostArr[$row[csf('id')]][3];
			$otherCostAll=array_sum($other_costing_arr[$row[csf('id')]]);
			$otherCost=$otherCostAll-($other_costing_arr[$row[csf('id')]]['design_cost']+$other_costing_arr[$row[csf('id')]]['studio_cost']+$other_costing_arr[$row[csf('id')]]['interest_cost']+$other_costing_arr[$row[csf('id')]]['incometax_cost']);
			//print_r($other_costing_arr[$row[csf('id')]]);
			$commercial_cost=$commercial_costing_arr[$row[csf('id')]];
			$commission_cost=array_sum($commission_costing_arr[$row[csf('id')]]);
			/*$lab_test=$other_costing_arr[$row[csf('id')]]['lab_test'];
			$inspection=$other_costing_arr[$row[csf('id')]]['inspection'];
			$freight_cost=$other_costing_arr[$row[csf('id')]]['freight'];	
			$currier_cost=$other_costing_arr[$row[csf('id')]]['currier_pre_cost'];
			$certificate_cost=$other_costing_arr[$row[csf('id')]]['certificate_pre_cost'];*/
			
			$bomMatrialCost=$yarn_costing_arr[$row[csf('id')]]+$fab_purchase_knit+$conversion_cost;
			$bomMatrialCostMar=$yarn_costing_arr[$row[csf('id')]]+$fab_purchase_knit+$conversion_cost+$trims_costing_arr[$row[csf('id')]]+$emblCost+$washCost+$otherCost+$commercial_cost+$commission_cost;
			//echo $row[csf("id")].'='.$yarn_costing_arr[$row[csf('id')]].'='.$fab_purchase_knit.'='.$conversion_cost.'='.$trims_costing_arr[$row[csf('id')]].'='.$emblCost.'='.$washCost.'='.$otherCost.'='.$commercial_cost.'='.$commission_cost.'<br>';
			
			$matrialPoArr[]=$row[csf('id')];  
		}
		if($row[csf("is_confirmed")]==1)
		{
			$order_data_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$key]['conf_matrialCost']+=$bomMatrialCost;
			$order_data_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$key]['conf_matrialCostMar']+=$bomMatrialCostMar;
		}
		else
		{
			$order_data_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$key]['proj_matrialCost']+=$bomMatrialCost;
			$order_data_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))][$key]['proj_matrialCostMar']+=$bomMatrialCostMar;
		}

		$agent_tem_arr[$key]=$row[csf("agent_name")];
		$date_arr[date("Y-m",strtotime($row[csf("country_ship_date")]))]=date("Y-m",strtotime($row[csf("country_ship_date")]));
		$team_leader_arr[$key]=$row[csf("team_leader")];	
	}
	//print_r($order_data_arr2); die;
	
	$sql_cons_rate="select id, mst_id, item_id, type, particular_type_id, consumption, ex_percent, tot_cons, unit, is_calculation, rate, rate_data, value from qc_cons_rate_dtls where status_active=1 and is_deleted=0 order by id asc";
	$sql_result_cons_rate=sql_select($sql_cons_rate); $yarn_dtls_arr=array(); $other_cost_arr=array();
	foreach ($sql_result_cons_rate as $rowConsRate)
	{
		if($rowConsRate[csf("type")]==1)
		{
			$edata="";
			if($rowConsRate[csf("rate_data")]!="")
			{
				$edata=explode("~~",$rowConsRate[csf("rate_data")]);
				$rate=$edata[3]+$edata[7]+$edata[11];
				$yarn_dtls_arr[$rowConsRate[csf("mst_id")]]['yarnkg']+=$rowConsRate[csf("tot_cons")];
				$yarn_dtls_arr[$rowConsRate[csf("mst_id")]]['yarnamt']+=$rowConsRate[csf("tot_cons")]*$rate;
				$yarn_dtls_arr[$rowConsRate[csf("mst_id")]]['knitamt']+=$rowConsRate[csf("tot_cons")]*$edata[14];
				$yarn_dtls_arr[$rowConsRate[csf("mst_id")]]['dyeamt']+=$rowConsRate[csf("tot_cons")]*$edata[15];
				$yarn_dtls_arr[$rowConsRate[csf("mst_id")]]['aopamt']+=$rowConsRate[csf("tot_cons")]*$edata[16];
				$yarn_dtls_arr[$rowConsRate[csf("mst_id")]]['finamt']+=$rowConsRate[csf("tot_cons")]*$edata[17];
				$yarn_dtls_arr[$rowConsRate[csf("mst_id")]]['otheramt']+=$rowConsRate[csf("tot_cons")]*$edata[18];
			}
			else
				$yarn_dtls_arr[$rowConsRate[csf("mst_id")]]['fabpurchase']+=$rowConsRate[csf("tot_cons")]*$rowConsRate[csf("rate")];
		}
		if($rowConsRate[csf("type")]==2 && $rowConsRate[csf("particular_type_id")]==3)
			$yarn_dtls_arr[$rowConsRate[csf("mst_id")]]['washamt']+=$rowConsRate[csf("tot_cons")]*$rowConsRate[csf("rate")];
		if($rowConsRate[csf("type")]==2 && $rowConsRate[csf("particular_type_id")]==1)
			$yarn_dtls_arr[$rowConsRate[csf("mst_id")]]['printamt']+=$rowConsRate[csf("tot_cons")]*$rowConsRate[csf("rate")];
		if($rowConsRate[csf("type")]==2 && $rowConsRate[csf("particular_type_id")]==2)
			$yarn_dtls_arr[$rowConsRate[csf("mst_id")]]['embamt']+=$rowConsRate[csf("tot_cons")]*$rowConsRate[csf("rate")];
		if($rowConsRate[csf("type")]==2 && $rowConsRate[csf("particular_type_id")]==4)
			$yarn_dtls_arr[$rowConsRate[csf("mst_id")]]['spcamt']+=$rowConsRate[csf("tot_cons")]*$rowConsRate[csf("rate")];
		if($rowConsRate[csf("type")]==2 && $rowConsRate[csf("particular_type_id")]==99)
			$yarn_dtls_arr[$rowConsRate[csf("mst_id")]]['othamt']+=$rowConsRate[csf("tot_cons")]*$rowConsRate[csf("rate")];
		if($rowConsRate[csf("type")]==3)
			$yarn_dtls_arr[$rowConsRate[csf("mst_id")]]['trimsamt']+=$rowConsRate[csf("tot_cons")]*$rowConsRate[csf("rate")];
	}

	$sql_sales=sql_select("select a.buyer_id, a.agent, a.team_leader, b.sales_target_date, b.sales_target_mint, b.sales_target_qty as sales_target_qty, b.sales_target_value, b.cm, b.cm_val_per, b.rm_val_per, b.actual_margin_per from wo_sales_target_mst a, wo_sales_target_dtls b where a.id=b.sales_target_mst_id and a.status_active=1 and a.is_deleted=0 and a.company_id in ($company_name) ".set_user_lavel_filtering(' and a.buyer_id','buyer_id')." $buyer_id_cond $agent_cond $team_leader_cond  $date_cond_sales order by a.buyer_id");
	$sale_data_arr=array();
	foreach($sql_sales as $row)
	{
		$key=$row[csf("buyer_id")].$row[csf("agent")].$row[csf("team_leader")];
		$sale_data_arr[date("Y-m",strtotime($row[csf("sales_target_date")]))][$key]['target_qty']+=$row[csf("sales_target_qty")];
		$sale_data_arr[date("Y-m",strtotime($row[csf("sales_target_date")]))][$key]['target_val']+=$row[csf("sales_target_value")];
		$sale_data_arr[date("Y-m",strtotime($row[csf("sales_target_date")]))][$key]['sales_target_mint']+=$row[csf("sales_target_mint")];
		
		$sale_data_arr[date("Y-m",strtotime($row[csf("sales_target_date")]))][$key]['cm']+=$row[csf("cm")];
		$sale_data_arr[date("Y-m",strtotime($row[csf("sales_target_date")]))][$key]['cm_val_per']+=$row[csf("cm_val_per")];
		$sale_data_arr[date("Y-m",strtotime($row[csf("sales_target_date")]))][$key]['rm_val_per']+=$row[csf("rm_val_per")];
		$sale_data_arr[date("Y-m",strtotime($row[csf("sales_target_date")]))][$key]['actual_margin_per']+=$row[csf("actual_margin_per")];
		
		$sale_data_arr[date("Y-m",strtotime($row[csf("sales_target_date")]))][$key]['buyer_id']=$row[csf("buyer_id")];
		//$sale_data_arr[date("Y-m",strtotime($row[csf("sales_target_date")]))][$row[csf("buyer_id")]]['agent']=$row[csf("agent")];
		$date_arr[date("Y-m",strtotime($row[csf("sales_target_date")]))]=date("Y-m",strtotime($row[csf("sales_target_date")]));

		if($row[csf("sales_target_qty")]>0)	{
			//$buyer_tem_arr[$key]=$row[csf("buyer_id")];
			$buyer_tem_arr[$row[csf("team_leader")]][$row[csf("buyer_id")]][$row[csf("agent")]]=$key;
		}

		$agent_tem_arr[$key]=$row[csf("agent")];
		$team_leader_arr[$key]=$row[csf("team_leader")];
	} 
	//var_dump($sale_data_arr['2015-01'][30]['target_qty']);
	//$noOfPo=count($poDataArray);	
	//var_dump($sale_data_arr['2017-06']);
	$workingHourArr=array();
	$sqlWorkingHour=sql_select( "select applying_period_date, working_hour, max_profit from lib_standard_cm_entry where company_id in ($company_name) and is_deleted=0 and  status_active=1");
	foreach($sqlWorkingHour as $wrow)
	{
		$workingHourArr[date("Y-m",strtotime($wrow[csf("applying_period_date")]))]['wh']=$wrow[csf("working_hour")];
		$workingHourArr[date("Y-m",strtotime($wrow[csf("applying_period_date")]))]['mxprofit']=$wrow[csf("max_profit")];
	}
	unset($sqlWorkingHour);
	
	$capacitySql="select a.avg_machine_line, a.basic_smv, a.avg_rate, b.date_calc, b.no_of_line,b.capacity_pcs,b.capacity_min from lib_capacity_calc_mst a, lib_capacity_calc_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.day_status=1 and a.comapny_id in ($company_name) $dateCondcapacity";	
	$capacityArr=array();
	$sqlCapRes=sql_select($capacitySql);
	foreach($sqlCapRes as $crow)
	{
		$capmin=$cappcs=$capval=0;
		
		$capmin=$workingHourArr[date("Y-m",strtotime($crow[csf("date_calc")]))]['wh']* 60 * $crow[csf("avg_machine_line")] * $crow[csf("no_of_line")] ;
		
		//echo $crow[csf("date_calc")].'=='.$workingHourArr[date("Y-m",strtotime($crow[csf("date_calc")]))]['wh'].'=='. 60 .'=='. $crow[csf("avg_machine_line")] .'=='. $crow[csf("no_of_line")] ."<br>";
		
		
		$cappcs=$capmin/$crow[csf("basic_smv")];
		$capval=$cappcs*$crow[csf("avg_rate")]; // previous source 
		$capval2=$crow[csf("capacity_pcs")]*$crow[csf("avg_rate")]; // source change by 
		$capmin2=$crow[csf("capacity_min")];
		
		$capacityArr[date("Y-m",strtotime($crow[csf("date_calc")]))]['capmin']+=$capmin;
		$capacityArr[date("Y-m",strtotime($crow[csf("date_calc")]))]['capmin2']+=$capmin2;
		$capacityArr[date("Y-m",strtotime($crow[csf("date_calc")]))]['cappcs']+=$cappcs;
		$capacityArr[date("Y-m",strtotime($crow[csf("date_calc")]))]['capacity_pcs']+=$crow[csf("capacity_pcs")];
		$capacityArr[date("Y-m",strtotime($crow[csf("date_calc")]))]['capval']+=$capval;
		$capacityArr[date("Y-m",strtotime($crow[csf("date_calc")]))]['capval2']+=$capval2;
	}
	unset($sqlCapRes);
		
	$total_month=count($date_arr);
	$width=$total_month*(7*90)+1100;
	//$width=($total_month*735)+100; 
	$colspan=$total_month;
	$colspan_mon=$total_month+3;
	asort($date_arr);
	asort($buyer_tem_arr);
	
	foreach($date_arr as $dateValue)
	{
		list($year,$month)=explode("-",$dateValue);	
		$newArr[$month][$year]=$dateValue;		
	}	
	
	$date_arr=array();
	foreach($newArr as $monthStageArr)
	{
		foreach($monthStageArr as $yearStageValue){
			$date_arr[$yearStageValue]=$yearStageValue;
		}
	}
	
	$ex_com=explode(",",$company_name); $companyStr="";
	foreach($ex_com as $compid)
	{
		if($companyStr=="") $companyStr=$company_library[$compid]; else $companyStr.=', '.$company_library[$compid];
	}
		
	//var_dump($date_arr);die;		
	?>
	<br>
 	<fieldset style="width:<? echo $width+20; ?>px;">
    	<table cellpadding="0" cellspacing="0" width="<? echo $width; ?>">
            <tr>
               <td align="center" width="100%" colspan="<? echo $total_month*2+5+1 ?>" class="form_caption" style="font-size:16px;"><? echo $companyStr; ?></td>
            </tr>
             <tr>
               <td align="center" width="100%" colspan="<? echo $total_month*2+5+1 ?>" class="form_caption"><strong>Sales Forecasting </strong></td>
            </tr>
        </table>	
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<? echo $width; ?>" class="rpt_table" >
            <thead>
            	<tr>
                    <th width="40" rowspan="3">SL</th>
                    <th width="100" rowspan="3">Buyer Name</th>
                    <th width="100" rowspan="3">Agent Name</th>
                    <th width="100" rowspan="3">Team Leader</th>
                    <th width="100" rowspan="3">Particulars</th>
                    <?
						foreach($date_arr as $yearMonth=>$val)
						{
							list($year,$month)=explode("-",$yearMonth);
						?>
                        	<th colspan="7"><p><?=$year; ?></p></th>
                        <?	
						}
					?>
                <th rowspan="2" colspan="7">Total</th>
                </tr>
            	<tr>
                    <?
						foreach($date_arr as $yearMonth=>$val)
						{
							$month_arr=explode("-",$yearMonth);
							$month_val=($month_arr[1]*1);
						?>
                        	<th colspan="7"><p><?=$months[$month_val]; ?></p></th>
                        <?	
						}
					?>
                    
                </tr>
                <tr>
                	<?
						for($z=1;$z<=$total_month;$z++)
						{
						?>
                        	<th width="90">Quantity</th>
                            <th width="90">Value</th>
                            <th width="90">Avl Minute</th>
                            
                            <th width="90">Avg CM /DZN</th>
                            <th width="90">RM %</th>
                            <th width="90">CM %</th>
                            <th width="90">Actual Margin %</th>
                        <?	
						}
					?>
                    <th width="90">Quantity</th>
                    <th width="90">Value</th>
                    <th width="90">Avl Minute</th>
                    
                    <th width="80">Avg CM /DZN</th>
                    <th width="80">RM %</th>
                    <th width="80">CM %</th>
                    <th>Actual Margin %</th>
                </tr>
            </thead>
        </table>
		<div style="width:<?=$width+18; ?>px; overflow-y:scroll; max-height:330px;" id="scroll_body">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="<?=$width; ?>" class="rpt_table" id="table_body" >
				<? 
				$i=1; $qcqtymonthArr=array(); $qcamtmonthArr=array(); $qcminmonthArr=array();
                foreach($buyer_tem_arr as $teamid=>$teamdata)
                {
					foreach($teamdata as $buyer_id=>$buyerdata) 
					{
						foreach($buyerdata as $agent_id=>$key) 
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$sales_qnty=$sale_data_arr[$month_id][$key]['target_qty'];
							
							$agent_id=$agent_tem_arr[$key]
							?>
							<!-- //Forecast..................--> 
							<tr bgcolor="<?=$bgcolor; ?>" onClick="change_colors('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>"> 
								<td width="40" rowspan="5" valign="middle" align="center"><?=$i; ?></td>
								<td width="100" rowspan="5" valign="middle" style="word-break:break-all"><?=$buyer_arr_library[$buyer_id]; ?>&nbsp;</td>
								<td width="100" rowspan="5" valign="middle" style="word-break:break-all"><?=$agent_arr_library[$agent_id]; ?>&nbsp;</td>
								<td width="100" rowspan="5" valign="middle" style="word-break:break-all"><?=$team_leader_arr_library[$teamid]; ?></td>
								<td width="100"><p>Forecast</p></td>
								<? 
								//$agent_arr_library
								$rowsales_qty=0; $rowsales_qnty_val=0; $rowsales_target_min=0; $rowsales_cm=0; $rowsales_cmper=0; $rowsales_rm=0; $rowsales_actual=0;
								
								$z=1;
								$cap=0;
								foreach($date_arr as $month_id=>$result)
								{
									//echo $buyer_id;
									$sales_qnty=$sale_data_arr[$month_id][$key]['target_qty'];
									$sales_qnty_val=$sale_data_arr[$month_id][$key]['target_val'];
									$sales_target_mint=$sale_data_arr[$month_id][$key]['sales_target_mint'];
									
									$saleCm=$sale_data_arr[$month_id][$key]['cm'];
									$saleCmValPer=$sale_data_arr[$month_id][$key]['cm_val_per'];
									$saleRmValPer=$sale_data_arr[$month_id][$key]['rm_val_per'];
									$saleActMarPer=$sale_data_arr[$month_id][$key]['actual_margin_per'];
									?>
									<td width="90" align="right" style="word-break:break-all"><?=number_format($sales_qnty); ?></td>
									<td width="90" align="right" style="word-break:break-all"><?=number_format($sales_qnty_val,2); ?></td>
                                    <td width="90" align="right" style="word-break:break-all"><?=number_format($sales_target_mint,2); ?></td>
                                    
                                    <td width="90" align="right" style="word-break:break-all"><?=number_format($saleCm,2); ?></td>
                                    <td width="90" align="right" style="word-break:break-all"><?=number_format($saleRmValPer,2); ?></td>
                                    <td width="90" align="right" style="word-break:break-all"><?=number_format($saleCmValPer,2); ?></td>
                                    <td width="90" align="right" style="word-break:break-all"><?=number_format($saleActMarPer,2); ?></td>
									<?
									$z++;
									$rowsales_qty+=$sales_qnty;	
									$rowsales_qnty_val+=$sales_qnty_val;
									$rowsales_target_min+=$sales_target_mint;
									
									$rowsales_cm+=$saleCm;
										
									$rowsales_cmper+=$saleCmValPer;	
									$rowsales_rm+=$saleRmValPer;	
									$rowsales_actual+=$saleActMarPer;	
										
									$tot_sales_qty_month[$month_id]+=$sales_qnty;	
									$tot_sales_qnty_val_month[$month_id]+=$sales_qnty_val;	
									$tot_sales_target_min_month[$month_id]+=$sales_target_mint;	
									$cap++;
								}
								?>
								<td width="90" align="right" style="word-break:break-all"><?=number_format($rowsales_qty); ?></td>
								<td width="90" align="right" style="word-break:break-all"><?=number_format($rowsales_qnty_val,2,'.',','); ?></td>
                                <td width="90" align="right" style="word-break:break-all"><?=number_format($rowsales_target_min,2,'.',','); ?></td>
                                
                                <td width="80" align="right" style="word-break:break-all"><?=number_format($rowsales_cm/$cap,2,'.',','); ?></td>
                                
                                <td width="80" align="right" style="word-break:break-all"><?=number_format($rowsales_rm/$cap,2,'.',','); ?></td>
                                <td width="80" align="right" style="word-break:break-all"><?=number_format($rowsales_cmper/$cap,2,'.',','); ?></td>
                                <td align="right" style="word-break:break-all"><?=number_format($rowsales_actual/$cap,2,'.',','); ?></td>
							</tr>
                            
                            <!-- //Quoted.........................--> 
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_colors('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>1"> 
							   <td width="100"><p>Quoted</p></td>
								<? 
								//$agent_arr_library
								$row_quotqty=$row_qcamt=$row_quot_min=0; $row_qccm=$row_qcrm=$row_qccmper=$row_qcactual=0; $q=1;
								$cap=0;
								foreach($date_arr as $month_id=>$result)
								{
									//echo $key.'-';
									$offerQty=$qcDataArr[$month_id][$key]['qcQty'];
									$qcamount=$qcDataArr[$month_id][$key]['qcVal'];
									$qc_min=$qcDataArr[$month_id][$key]['qcMin'];
									
									$cmValue=$qcDataArr[$month_id][$key]['qcCmVal'];
									$cmQc=$cmValue/$offerQty;
									
									$qcFabCost=$qcDataArr[$month_id][$key]['qcFab'];
									$actualFabCost=$qcDataArr[$month_id][$key]['actualFab'];
									$rmQc=(($qcFabCost-$actualFabCost)/$qcFabCost)*100;
									
									$qcCm=$qcDataArr[$month_id][$key]['qcCm'];
									$actulaCm=$qcDataArr[$month_id][$key]['cmMargin'];
									$cmPerQc=(($qcCm-$actulaCm)/$qcCm)*100;
									
									$qcFobPcs=$qcDataArr[$month_id][$key]['qcFob'];
									$actualFobPcs=$qcDataArr[$month_id][$key]['actualFob'];
									
									$actualQc=(($qcFobPcs-$actualFobPcs)/$qcFobPcs)*100;
									?>
									<td align="right" style="word-break:break-all"><?=number_format($offerQty); ?></td>
									<td align="right" style="word-break:break-all"><?=number_format($qcamount,2); ?></td>
                                    <td align="right" style="word-break:break-all"><?=number_format($qc_min,2); ?></td>
                                    
                                    <td align="right" style="word-break:break-all"><?=number_format($cmQc,2); ?></td>
                                    <td align="right" style="word-break:break-all"><?=number_format($rmQc,2); ?></td>
                                    <td align="right" style="word-break:break-all"><?=number_format($cmPerQc,2); ?></td>
                                    <td align="right" style="word-break:break-all"><?=number_format($actualQc,2); ?></td>
									<?
									$q++;
									$row_quotqty+=$offerQty;	
									$row_qcamt+=$qcamount;
									$row_quot_min+=$qc_min;
									
									$row_qccm+=$cmQc;
									$row_qcrm+=$rmQc;
									$row_qccmper+=$cmPerQc;
									$row_qcactual+=$actualQc;
										
									$qcqtymonthArr[$month_id]+=$offerQty;	
									$qcamtmonthArr[$month_id]+=$qcamount;
									$qcminmonthArr[$month_id]+=$qc_min;	
									$cap++;
								}
								?>
								<td align="right" style="word-break:break-all"><?=number_format($row_quotqty,2,'.',','); ?></td>
								<td align="right" style="word-break:break-all"><?=number_format($row_qcamt,2,'.',','); ?></td>
                                <td align="right" style="word-break:break-all"><?=number_format($row_quot_min,2,'.',','); ?></td>
                                
                                <td align="right" style="word-break:break-all"><?=number_format($row_qccm/$cap,2,'.',','); ?></td>
                                <td align="right" style="word-break:break-all"><?=number_format($row_qcrm/$cap,2,'.',','); ?></td>
                                <td align="right" style="word-break:break-all"><?=number_format($row_qccmper/$cap,2,'.',','); ?></td>
                                <td align="right" style="word-break:break-all"><?=number_format($row_qcactual/$cap,2,'.',','); ?></td>
							</tr>
		
							<!-- //Projection.........................--> 
							<tr bgcolor="<?=$bgcolor; ?>" onClick="change_colors('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>2"> 
							   <td width="100"><p>Projection</p></td>
								<? 
								//$agent_arr_library
								$tot_projectqty=$tot_proj_min=0; $tot_projectqty=0;$tot_projectamount=0; $z=1;
								$cap=0;
								foreach($date_arr as $month_id=>$result)
								{
									//echo $buyer_id;
									$projectqty=$order_data_arr[$month_id][$key]['projectqty'];
									$projectamount=$order_data_arr[$month_id][$key]['projectamount'];
									$proj_min=$order_data_arr[$month_id][$key]['proj_min'];
									
									$proj_cmval=$order_data_arr[$month_id][$key]['proj_cmval'];
									$projCm=$proj_cmval/$projectqty;
									$proj_rm=0;
									$actulaProjCm=$qcDataArr[$month_id][$key]['cmMargin'];
									$proj_cmCost=$order_data_arr[$month_id][$key]['proj_cmCost'];
									$proj_cmMar=(($actulaProjCm-$proj_cmCost)/$actulaProjCm)*100;
									?>
									<td align="right" style="word-break:break-all"><?=number_format($projectqty); ?></td>
									<td align="right" style="word-break:break-all"><?=number_format($projectamount,2); ?></td>
                                    <td align="right" style="word-break:break-all"><?=number_format($proj_min,2); ?></td>
                                    
                                    <td align="right" style="word-break:break-all"><?=number_format($projCm,2); ?></td>
                                    <td align="right" style="word-break:break-all"><?=number_format($proj_rm,2); ?></td>
                                    <td align="right" style="word-break:break-all"><?=number_format($proj_cmMar,2); ?></td>
                                    <td align="right" style="word-break:break-all">&nbsp;</td>
									<?
									$z++;
									$tot_projectqty+=$projectqty;	
									$tot_projectamount+=$projectamount;
									$tot_proj_min+=$proj_min;
										
									$tot_projectqty_month[$month_id]+=$projectqty;	
									$tot_projectamount_month[$month_id]+=$projectamount;
									$tot_projectmin_month[$month_id]+=$proj_min;
									$cap++;	
								}
								?>
								<td align="right" style="word-break:break-all"><?=number_format($tot_projectqty,2,'.',','); ?></td>
								<td align="right" style="word-break:break-all"><?=number_format($tot_projectamount,2,'.',','); ?></td>
                                <td align="right" style="word-break:break-all"><?=number_format($tot_proj_min,2,'.',','); ?></td>
                                
                                <td align="right" style="word-break:break-all">&nbsp;</td>
                                <td align="right" style="word-break:break-all">&nbsp;</td>
                                <td align="right" style="word-break:break-all">&nbsp;</td>
                                <td align="right" style="word-break:break-all">&nbsp;</td>
							</tr>
							<!-- //Confirm............................--> 
							<tr bgcolor="<?=$bgcolor; ?>" onClick="change_colors('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>3"> 
							   <td width="100"><p>Confirm</p></td>
								<? //$agent_arr_library
								$tot_confirmqty=$tot_conf_min=0; $tot_confirmamount=0; $row_cfcm=$row_cfrm=$row_cfcmper=$row_cfactual=$rowConfQty=$rowCmVal=$rowConfVal=$rowConfMatCost=0; $z=1;
								$cap=0;
								foreach($date_arr as $month_id=>$result)
								{
									//echo $buyer_id;
									
									$confirmqty=$confirmamount=$conf_min=$confTotCost=$rmCf=0;
									$confirmqty=$order_data_arr[$month_id][$key]['confirmqty'];
									$confirmamount=$order_data_arr[$month_id][$key]['confirmamount'];
									$conf_min=$order_data_arr[$month_id][$key]['conf_avlmin'];
									//echo $conf_min.'=='.$confirmqty.'=='.$order_data_arr[$month_id][$key]['conf_smv'].'=='.$order_data_arr[$month_id][$key]['conf_eff'].'<br>';
									  ///$order_data_arr[$month_id][$key]['conf_min'];
									//echo $conf_min.'=='.$confirmqty.'=='.$order_data_arr[$month_id][$key]['conf_smv'].'=='.$qcDataArr[$month_id][$key]['efficency'];
									$conf_matrialCostMar=$order_data_arr[$month_id][$key]['conf_matrialCostMar'];
									$conf_cmval=$order_data_arr[$month_id][$key]['conf_cmval'];
									$confCm=($conf_cmval/$confirmqty)*12;
									
									$qcMaterialCost=$qcDataArr[$month_id][$key]['qcMatrialCost'];
									$bomMaterialCost=$order_data_arr[$month_id][$key]['conf_matrialCost'];
									//$rmCf=(($qcMaterialCost-$bomMaterialCost)/$qcMaterialCost)*100;
									$confTotCost=$order_data_arr[$month_id][$key]['conf_cost'];
									$rmCf=(($conf_matrialCostMar-$conf_cmval)/$confirmamount)*100;
									//echo $rmCf.'=='.$confTotCost.'=='.$conf_cmval.'=='.$confirmamount.'<br>';
									
									$actulaConfCm=$qcDataArr[$month_id][$key]['cmMargin'];
									$conf_cmCost=$order_data_arr[$month_id][$key]['conf_cmCost'];
									//$conf_cmMar=(($actulaConfCm-$conf_cmCost)/$actulaConfCm)*100;
									$conf_cmMar=($conf_cmval/$confirmamount)*100;
									
									//$actualCf=(($confirmamount-$conf_matrialCostMar)/$confirmamount)*100;
									$actualCf=($confirmamount-$conf_matrialCostMar)/$confirmamount*100;
									
									//echo $rmCf.'=='.$actualCf.'=='.$confirmamount.'=='.$conf_matrialCostMar.'=='.$conf_cmval.'=='.$conf_cmval.'<br>';
									?>
									<td align="right" style="word-break:break-all"><?=number_format($confirmqty); ?></td>
									<td align="right" style="word-break:break-all"><?=number_format($confirmamount,2); ?></td>
                                    <td align="right" style="word-break:break-all"><?=number_format($conf_min,2); ?></td>
                                    
                                    <td align="right" style="word-break:break-all"><?=number_format($confCm,2); ?></td>
                                    <td align="right" style="word-break:break-all" title="<?='('.$conf_matrialCostMar.'-'.$conf_cmval.')/'.$confirmamount.')*100'; ?>"><?=number_format($rmCf,2); ?></td>
                                    <td align="right" style="word-break:break-all"><?=number_format($conf_cmMar,2); ?></td>
                                    <td align="right" style="word-break:break-all" title="<?='('.$confirmamount.'-'.$conf_matrialCostMar.')/'.$confirmamount.'*100'; ?>"><?=number_format($actualCf,2); ?></td>
									<?
									$z++;
									$tot_confirmqty+=$confirmqty;	
									$tot_confirmamount+=$confirmamount;	
									$tot_conf_min+=$conf_min;
									
									$row_cfcm+=$confCm;	
									$row_cfrm+=$rmCf;	
									$row_cfcmper+=$conf_cmMar;
									$row_cfactual+=$actualCf;
									$rowCmVal+=$conf_cmval;
									$rowConfQty+=$confirmqty;
									$rowConfVal+=$confirmamount;
									$rowConfMatCost+=$conf_matrialCostMar;
									
									$tot_confirmqty_month[$month_id]+=$confirmqty;	
									$tot_confirmamount_month[$month_id]+=$confirmamount;
									$tot_confirmmin_month[$month_id]+=$conf_min;
									$totConData[$month_id]['cmval']+=$conf_cmval;
									$totConData[$month_id]['material']+=$conf_matrialCostMar;
									$totConData[$month_id]['poamt']+=$confirmamount;
								}
								$rowTotCmDzn=($rowCmVal/$rowConfQty)*12;
								$rowTotRmPer=(($rowConfMatCost-$rowCmVal)/$rowConfVal)*100;
								$rowTotCmPer=($rowCmVal/$rowConfVal)*100;
								$rowTotActualMar=(($rowConfVal-$rowConfMatCost)/$rowConfVal)*100;
								$cap++;
								//echo $rowCmVal.'='.$rowConfQty.'='.$rowConfMatCost.'='.$rowConfVal.'<br>';
								?>
								<td align="right" style="word-break:break-all"><?=number_format($tot_confirmqty,2,'.',','); ?></td>
								<td align="right" style="word-break:break-all"><?=number_format($tot_confirmamount,2,'.',','); ?></td>
                                <td align="right" style="word-break:break-all"><?=number_format($tot_conf_min,2,'.',','); ?></td>
                                
                                <td align="right" style="word-break:break-all"><?=number_format($rowTotCmDzn/$cap,2,'.',','); ?></td>
                                <td align="right" style="word-break:break-all"><?=number_format($rowTotRmPer/$cap,2,'.',','); ?></td>
                                <td align="right" style="word-break:break-all"><?=number_format($rowTotCmPer/$cap,2,'.',','); ?></td>
                                <td align="right" style="word-break:break-all"><?=number_format($rowTotActualMar/$cap,2,'.',','); ?></td>
							</tr>
		
							<!-- //Variance..........................--> 
							<tr bgcolor="<?=$bgcolor; ?>" onClick="change_colors('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>4"> 
							   <td width="100"><b>Variance</b></td>
								<? 
								//$agent_arr_library
								$tot_variance_qnty=0; $tot_variance_amount=$tot_variance_minute=0; $rowCmVar=$rowRmVar=$cmPerVar=$actualVar=$rowCmVal=$rowConfQty=$rowConfVal=$rowConfMatCost=$rowSalesAvlMin=$rowSalesCmDzn=$rowSalesRmPer=$rowSalesCm=$rowSalesActualMar=0; $z=1;
								$cap=0;
								foreach($date_arr as $month_id=>$result)
								{
									
									$sales_qnty=$sale_data_arr[$month_id][$key]['target_qty'];
									$sales_qnty_val=$sale_data_arr[$month_id][$key]['target_val'];
									$sales_target_mint=$sale_data_arr[$month_id][$key]['sales_target_mint'];
		
									$projectqty=$order_data_arr[$month_id][$key]['projectqty'];
									$projectamount=$order_data_arr[$month_id][$key]['projectamount'];
									$proj_min=$order_data_arr[$month_id][$key]['proj_min'];
									
									$confirmqty=$confirmamount=$conf_min=$confirmqty=$confirmamount=$conf_matrialCostMar=$conf_cmval=0;
		
									$confirmqty=$order_data_arr[$month_id][$key]['confirmqty'];
									//echo $confirmqty.'<br>';
									$confirmamount=$order_data_arr[$month_id][$key]['confirmamount'];
									$conf_matrialCostMar=$order_data_arr[$month_id][$key]['conf_matrialCostMar'];
									$conf_cmval=$order_data_arr[$month_id][$key]['conf_cmval'];
									$conf_min=$order_data_arr[$month_id][$key]['conf_avlmin'];//$order_data_arr[$month_id][$key]['conf_min'];
									
									$variance_qnty=($projectqty+$confirmqty)-$sales_qnty;
									$variance_amount=($projectamount+$confirmamount)-$sales_qnty_val;
									//echo $projectamount.'=='.$confirmamount.'=='.$sales_qnty_val.'<br>';
									
									$variance_minute=($proj_min+$conf_min)-$sales_target_mint;
									$td_va_color=$td_vq_color='';
									if( $variance_qnty < 0){$td_vq_color="#f00";}
									if( $variance_amount < 0){$td_va_color="#f00";}
									
									//$cmCf=($order_data_arr[$month_id][$key]['conf_cmval']/$confirmqty)*12;
									$rmCf=0;//$qcDataArr[$month_id][$key]['qcVal'];
									$cmPerCf=$qcDataArr[$month_id][$key]['cmMargin'];
									$actualCf=$qcDataArr[$month_id][$key]['actualMargin'];
									
									$saleCm=$sale_data_arr[$month_id][$key]['cm'];
									$saleCmValPer=$sale_data_arr[$month_id][$key]['cm_val_per'];
									$saleRmValPer=$sale_data_arr[$month_id][$key]['rm_val_per'];
									$saleActMarPer=$sale_data_arr[$month_id][$key]['actual_margin_per'];
									
									$cmVar=(($conf_cmval/$confirmqty)*12)-$saleCm;//$cmCf-$saleCm;
									$rmVar=((($conf_matrialCostMar-$conf_cmval)/$confirmamount)*100)-$saleRmValPer;//$rmCf-$saleRmValPer;
									$cmPerVar=(($conf_cmval/$confirmamount)*100)-$saleCmValPer;//$cmPerCf-$saleCmValPer;
									$actualPerVar=(($confirmamount-$conf_matrialCostMar)/$confirmamount*100)-$saleActMarPer;//$actualCf-$saleActMarPer;
									?>
									<td align="right" style="word-break:break-all" bgcolor="<?=$td_vq_color;?>"><b><?=number_format($variance_qnty); ?></b></td>
									<td align="right" style="word-break:break-all" bgcolor="<?=$td_va_color;?>"><b><?=number_format($variance_amount,2); ?></b></td>
                                    <td align="right" style="word-break:break-all" bgcolor="<?=$td_va_color;?>"><b><?=number_format($variance_minute,2); ?></b></td>
                                    
                                    <td align="right" style="word-break:break-all"><?=number_format($cmVar,2); ?></td>
                                    <td align="right" style="word-break:break-all"><?=number_format($rmVar,2); ?></td>
                                    <td align="right" style="word-break:break-all"><?=number_format($cmPerVar,2); ?></td>
                                    <td align="right" style="word-break:break-all"><?=number_format($actualPerVar,2); ?></td>
									<?
									$z++;
									$tot_variance_qnty+=$variance_qnty;	
									$tot_variance_amount+=$variance_amount;	
									$tot_variance_minute+=$variance_minute;
									
									$rowCmVar+=$cmVar;
									$rowRmVar+=$rmVar;
									$cmPerVar+=$cmPerVar;
									$actualVar+=$actualPerVar;
									
									$rowCmVal+=$conf_cmval;
									$rowConfQty+=$confirmqty;
									$rowConfVal+=$confirmamount;
									$rowConfMatCost+=$conf_matrialCostMar;
									$rowSalesAvlMin+=$sales_target_mint;
									$rowSalesCmDzn+=$saleCm;
									$rowSalesRmPer+=$saleRmValPer;
									$rowSalesCm+=$saleCmValPer;
									$rowSalesActualMar+=$saleActMarPer;
									
									$tot_variance_qnty_month[$month_id]+=$variance_qnty;	
									$tot_variance_amount_month[$month_id]+=$variance_amount;
									$tot_variance_minute_month[$month_id]+=$variance_minute;
									$cap++;	
								}
								
								$rowVarTotCmDzn=(($rowCmVal/$rowConfQty)*12)-$rowSalesCmDzn;
								$rowVarTotRmPer=((($rowConfMatCost-$rowCmVal)/$rowConfVal)*100)-$rowSalesRmPer;
								$rowVarTotCmPer=(($rowCmVal/$rowConfVal)*100)-$rowSalesCm;
								$rowVarTotActualMar=((($rowConfVal-$rowConfMatCost)/$rowConfVal)*100)-$rowSalesActualMar;
								
								//echo $rowCmVal.'='.$rowConfQty.'='.$rowConfMatCost.'='.$rowConfVal.'<br>';
								
								$td_va_color=$td_vq_color='';
								if( $tot_variance_qnty < 0){$td_vq_color="#f00";}
								if( ($tot_confirmamount+$tot_projectamount)-$tot_sales_qnty_val < 0){$td_va_color="#f00";}
								?>
								<td align="right" style="word-break:break-all" bgcolor="<?=$td_vq_color;?>"><b><?=number_format($tot_variance_qnty); ?></b></td>
								<td align="right" style="word-break:break-all" bgcolor="<?=$td_va_color;?>"><b><?=number_format($tot_variance_amount,2,'.',','); ?></b></td>
                                <td align="right" style="word-break:break-all" bgcolor="<?=$td_va_color;?>"><b><?=number_format($tot_variance_minute,2,'.',','); ?></b></td>
                                
                                <td align="right" style="word-break:break-all"><?=number_format($rowVarTotCmDzn/$cap,2); ?></td>
                                <td align="right" style="word-break:break-all"><?=number_format($rowVarTotRmPer/$cap,2); ?></td>
                                <td align="right" style="word-break:break-all"><?=number_format($rowVarTotCmPer/$cap,2); ?></td>
                                <td align="right" style="word-break:break-all"><?=number_format($rowVarTotActualMar/$cap,2); ?></td>
							</tr>
							<?
							$i++;
							
							$total_sales_qty+=$tot_sales_qty;
							$total_tot_sales_qnty_val+=$tot_sales_qnty_val;
							$total_tot_target_min_val+=$tot_sales_target_min;
							
							$grand_projectqty+=$tot_projectqty;//grand_projectmin
							$grand_projectamount+=$tot_projectamount;
							$grand_projectmin+=$tot_proj_min;
							
							$grand_confirmqty+=$tot_confirmqty;
							$grand_confirmamount+=$tot_confirmamount;
							$grand_projamount+=$tot_conf_min;
							
							$grand_variance_qnty+=$tot_variance_qnty;
							$grand_variance_amount+=$tot_variance_amount;
							$grand_variance_minute+=$tot_variance_minute;
						}
					}
				}
			    ?>
            	<tfoot>
                	<tr style="background:#FF9">
                        <th rowspan="6" colspan="3" align="right">Total</th>
                        <td colspan="2" align="right"><b>Forecast</b></td>
						<?
                        foreach($date_arr as $month_id=>$result)
                        {
                            ?>
                            <td align="right" style="word-break:break-all"><b><?=number_format($tot_sales_qty_month[$month_id]); ?></b></td>
                            <td align="right" style="word-break:break-all"><b><?=number_format($tot_sales_qnty_val_month[$month_id],2,'.',','); ?></b></td>
                            <td align="right" style="word-break:break-all"><b><?=number_format($tot_sales_target_min_month[$month_id],2,'.',','); ?></b></td>
                            
                            <td align="right" style="word-break:break-all">&nbsp;</td>
                            <td align="right" style="word-break:break-all">&nbsp;</td>
                            <td align="right" style="word-break:break-all">&nbsp;</td>
                            <td align="right" style="word-break:break-all">&nbsp;</td>
                        	<?	
                        }
                        ?>
                        <td align="right" style="word-break:break-all"><b><?=number_format($total_sales_qty); ?></b></td>
                        <td align="right" style="word-break:break-all"><b><?=number_format($total_tot_sales_qnty_val,2,'.',','); ?></b></td>
                        <td align="right" style="word-break:break-all"><b><?=number_format($total_tot_target_min_val,2,'.',','); ?></b></td>
                         
                        <td align="right" style="word-break:break-all">&nbsp;</td>
                        <td align="right" style="word-break:break-all">&nbsp;</td>
                        <td align="right" style="word-break:break-all">&nbsp;</td>
                        <td align="right" style="word-break:break-all">&nbsp;</td>
                    </tr>

                	<tr>
                        <th colspan="2">Projection</th>
                        <?
                        foreach($date_arr as $month_id=>$result)
                        {
                            ?>
                        	<th align="right" style="word-break:break-all"><?=number_format($tot_projectqty_month[$month_id]); ?></th>
                        	<th align="right" style="word-break:break-all"><?=number_format($tot_projectamount_month[$month_id],2,'.',','); ?></th>
                            <th align="right" style="word-break:break-all"><?=number_format($tot_projectmin_month[$month_id],2,'.',','); ?></th>
                            
                            <td align="right" style="word-break:break-all">&nbsp;</td>
                            <td align="right" style="word-break:break-all">&nbsp;</td>
                            <td align="right" style="word-break:break-all">&nbsp;</td>
                            <td align="right" style="word-break:break-all">&nbsp;</td>
                        	<?	
                        }
                        ?>
                        <th align="right" style="word-break:break-all"><?=number_format($grand_projectqty,2,'.',','); ?></th>
                        <th align="right" style="word-break:break-all"><?=number_format($grand_projectamount,2,'.',','); ?></th>
                        <th align="right" style="word-break:break-all"><?=number_format($grand_projectmin,2,'.',','); ?></th>
                        
                        <td align="right" style="word-break:break-all">&nbsp;</td>
                        <td align="right" style="word-break:break-all">&nbsp;</td>
                        <td align="right" style="word-break:break-all">&nbsp;</td>
                        <td align="right" style="word-break:break-all">&nbsp;</td>
                    </tr>
                    
                    <tr>
                        <th colspan="2">Capacity</th>
                        <? 
						$rowGProfPer=0; $cap=0;
						$grand_pcs=0;
						$grand_val=0;
						$grand_min=0;
                        foreach($date_arr as $month_id=>$result)
                        {
							$monthCapacityPcs=$monthCapacityVal=$monthCapacityMin=$monthFinProfit=0;
							//$monthCapacityPcs=$capacityArr[$month_id]['cappcs']; // previous source
							$monthCapacityPcs=$capacityArr[$month_id]['capacity_pcs']; // source change by 9390
							//$monthCapacityVal=$capacityArr[$month_id]['capval']; // previous source
							$monthCapacityVal=$capacityArr[$month_id]['capval2']; // source change by 9390
							$monthCapacityMin=$capacityArr[$month_id]['capmin'];
							//$monthCapacityMin=$capacityArr[$month_id]['capmin2'];
							$monthFinProfit=$workingHourArr[$month_id]['mxprofit'];

							$grand_pcs+=$monthCapacityPcs;
							$grand_val+=$monthCapacityVal;
							$grand_min+=$monthCapacityMin;
                            ?>
                        	<th align="right" style="word-break:break-all"><?=number_format($monthCapacityPcs); ?></th>
                        	<th align="right" style="word-break:break-all"><?=number_format($monthCapacityVal,2,'.',','); ?></th>
                            <th align="right" style="word-break:break-all"><?=number_format($monthCapacityMin,2,'.',','); ?></th>
                            
                            <th align="right" style="word-break:break-all">&nbsp;</th>
                            <th align="right" style="word-break:break-all">&nbsp;</th>
                            <th align="right" style="word-break:break-all">&nbsp;</th>
                            <th align="right" style="word-break:break-all"><?=number_format($monthFinProfit,2,'.',','); ?></th>
                        	<?	
							$rowGProfPer+=$monthFinProfit;
							$cap++;
                        }
						$growtotprofitper=$rowGProfPer/$cap;
						$grand_pcs+=$monthCapacityPcs;
							$grand_val+=$monthCapacityVal;
							$grand_min+=$monthCapacityMin;
                        ?>
                        <th align="right" style="word-break:break-all"><?=number_format($grand_pcs,2,'.',',');?></th>
                        <th align="right" style="word-break:break-all"><?=number_format($grand_val,2,'.',',');?></th>
                        <th align="right" style="word-break:break-all"><?=number_format($grand_min,2,'.',',');?></th>
                        
                        <th align="right" style="word-break:break-all">&nbsp;</th>
                        <th align="right" style="word-break:break-all">&nbsp;</th>
                        <th align="right" style="word-break:break-all">&nbsp;</th>
                        <th align="right" style="word-break:break-all"><?=number_format($growtotprofitper,2,'.',','); ?></th>
                    </tr>
                    
                    <tr>
                        <th colspan="2">Confirm</th>
                        <? $gCmVal=$gPoQty=$gMaterial=$gPoAmt=0;
                        $cap=0;
                         $tot_monthCmDzn=$tot_monthRmPer=$tot_monthCmMar=$tot_monthActualCf=0;
                        foreach($date_arr as $month_id=>$result)
                        {
							$monthCmDzn=($totConData[$month_id]['cmval']/$tot_confirmqty_month[$month_id])*12;
							$monthRmPer=(($totConData[$month_id]['material']-$totConData[$month_id]['cmval'])/$totConData[$month_id]['poamt'])*100;
							$monthCmMar=($totConData[$month_id]['cmval']/$totConData[$month_id]['poamt'])*100;
							$monthActualCf=($totConData[$month_id]['poamt']-$totConData[$month_id]['material'])/$totConData[$month_id]['poamt']*100;
							
							$gCmVal+=$totConData[$month_id]['cmval'];
							$gPoQty+=$tot_confirmqty_month[$month_id];
							$gMaterial+=$totConData[$month_id]['material'];
							$gPoAmt+=$totConData[$month_id]['poamt'];
                            ?>
                        	<th align="right" style="word-break:break-all"><?=number_format($tot_confirmqty_month[$month_id]); ?></th>
                        	<th align="right" style="word-break:break-all"><?=number_format($tot_confirmamount_month[$month_id],2,'.',','); ?></th>
                            <th align="right" style="word-break:break-all"><?=number_format($tot_confirmmin_month[$month_id],2,'.',','); ?></th>
                            
                            <th align="right" style="word-break:break-all"><?=number_format($monthCmDzn,2,'.',','); ?></th>
                            <th align="right" style="word-break:break-all"><?=number_format($monthRmPer,2,'.',','); ?></th>
                            <th align="right" style="word-break:break-all"><?=number_format($monthCmMar,2,'.',','); ?></th>
                            <th align="right" style="word-break:break-all"><?=number_format($monthActualCf,2,'.',','); ?></th>
                        	<?	
                        	$cap++;
                        	$tot_monthCmDzn+=$monthCmDzn;
                        	 $tot_monthRmPer+=$monthRmPer;
                        	 $tot_monthCmMar+=$monthCmMar;
                        	 $tot_monthActualCf+=$monthActualCf;
                        }
						$gConfCmDzn=(($gCmVal/$gPoQty)*12)/$cap;
						$gConfRmPer=((($gMaterial-$gCmVal)/$gPoAmt)*100)/$cap;
						$gConfCmMar=(($gCmVal/$gPoAmt)*100)/$cap;
						$gConfActualCf=(($gPoAmt-$gMaterial)/$gPoAmt*100)/$cap;
						
                        ?>
                        <th align="right" style="word-break:break-all"><?=number_format($grand_confirmqty); ?></th>
                        <th align="right" style="word-break:break-all"><?=number_format($grand_confirmamount,2,'.',','); ?></th>
                        <th align="right" style="word-break:break-all"><?=number_format($grand_projamount,2,'.',','); ?></th>
                        
                        <th align="right" style="word-break:break-all"><?=number_format($tot_monthCmDzn/$cap,2,'.',','); ?></th>
                        <th align="right" style="word-break:break-all"><?=number_format($tot_monthRmPer/$cap,2,'.',','); ?></th>
                        <th align="right" style="word-break:break-all"><?=number_format($tot_monthCmMar/$cap,2,'.',','); ?></th>
                        <th align="right" style="word-break:break-all"><?=number_format($tot_monthActualCf/$cap,2,'.',','); ?></th>
                    </tr>

                     <tr>
                        <th colspan="2">Capacity Vs Confirm</th>
                        <? $gCmVal=$gPoQty=$gMaterial=$gPoAmt=0;
                        $cap=0;
                         $tot_monthCmDzn=$tot_monthRmPer=$tot_monthCmMar=$tot_monthActualCf=0;
                        foreach($date_arr as $month_id=>$result)
                        {
							$monthCmDzn=($totConData[$month_id]['cmval']/$tot_confirmqty_month[$month_id])*12;
							$monthRmPer=(($totConData[$month_id]['material']-$totConData[$month_id]['cmval'])/$totConData[$month_id]['poamt'])*100;
							$monthCmMar=($totConData[$month_id]['cmval']/$totConData[$month_id]['poamt'])*100;
							$monthActualCf=($totConData[$month_id]['poamt']-$totConData[$month_id]['material'])/$totConData[$month_id]['poamt']*100;
							
							$gCmVal+=$totConData[$month_id]['cmval'];
							$gPoQty+=$tot_confirmqty_month[$month_id];
							$gMaterial+=$totConData[$month_id]['material'];
							$gPoAmt+=$totConData[$month_id]['poamt'];
							//$monthCapacityPcs=$capacityArr[$month_id]['capacity_pcs'];
                            ?>
                        	<th align="right" style="word-break:break-all" ><?=number_format($capacityArr[$month_id]['capacity_pcs']-$tot_confirmqty_month[$month_id]); ?></th>
                        	<th align="right" style="word-break:break-all"><?=number_format($capacityArr[$month_id]['capval2']-$tot_confirmamount_month[$month_id],2,'.',','); ?></th>
                            <th align="right" style="word-break:break-all"><?=number_format($capacityArr[$month_id]['capmin']-$tot_confirmmin_month[$month_id],2,'.',','); ?></th>
                            
                            <th align="right" style="word-break:break-all"></th>
                            <th align="right" style="word-break:break-all"></th>
                            <th align="right" style="word-break:break-all"></th>
                            <th align="right" style="word-break:break-all"><?=number_format($workingHourArr[$month_id]['mxprofit']-$monthActualCf,2,'.',','); ?></th>
                        	<?	
                        	$cap++;
                        	 $tot_monthCmDzn+=$monthCmDzn;
                        	 $tot_monthRmPer+=$monthRmPer;
                        	 $tot_monthCmMar+=$monthCmMar;
                        	 $tot_monthActualCf+=($workingHourArr[$month_id]['mxprofit']-$monthActualCf);
                        }
						$gConfCmDzn=(($gCmVal/$gPoQty)*12)/$cap;
						$gConfRmPer=((($gMaterial-$gCmVal)/$gPoAmt)*100)/$cap;
						$gConfCmMar=(($gCmVal/$gPoAmt)*100)/$cap;
						$gConfActualCf=(($gPoAmt-$gMaterial)/$gPoAmt*100)/$cap;

						
						
                        ?>
                        <th align="right" style="word-break:break-all"><?=number_format($grand_pcs-$grand_confirmqty); ?></th>
                        <th align="right" style="word-break:break-all"><?=number_format($grand_val-$grand_confirmamount,2,'.',','); ?></th>
                        <th align="right" style="word-break:break-all"><?=number_format($grand_min-$grand_projamount,2,'.',','); ?></th>
                        
                        <th align="right" style="word-break:break-all">-<?=number_format($tot_monthCmDzn/$cap,2,'.',','); ?></th>
                        <th align="right" style="word-break:break-all">-<?=number_format($tot_monthRmPer/$cap,2,'.',','); ?></th>
                        <th align="right" style="word-break:break-all">-<?=number_format($tot_monthCmMar/$cap,2,'.',','); ?></th>
                        <th align="right" style="word-break:break-all"><?=number_format($tot_monthActualCf/$cap,2,'.',','); ?></th>
                    </tr>
                    
                	<tr style="background:#FF9">
                        <td align="right" colspan="2" style="word-break:break-all"><b>Variance(Proj+Conf)-Forecast</b></td>
                        <?
                        foreach($date_arr as $month_id=>$result)
                        {
							$td_va_color=$td_vq_color='';
							if( $tot_variance_qnty_month[$month_id] < 0){$td_vq_color="#f00";}
							if($tot_variance_amount_month[$month_id] < 0){$td_va_color="#f00";}						   
							?>
                        	<td align="right" style="word-break:break-all" bgcolor="<?=$td_vq_color;?>"><b><?=number_format($tot_variance_qnty_month[$month_id]); ?></b></td>
                        	<td align="right" style="word-break:break-all" bgcolor="<?=$td_va_color;?>"><b><?=number_format($tot_variance_amount_month[$month_id],2,'.',','); ?></b></td>
                            <td align="right" style="word-break:break-all" bgcolor="<?=$td_va_color;?>"><b><?=number_format($tot_variance_minute_month[$month_id],2,'.',','); ?></b></td>
                            
                            <td align="right" style="word-break:break-all">&nbsp;</td>
                            <td align="right" style="word-break:break-all">&nbsp;</td>
                            <td align="right" style="word-break:break-all">&nbsp;</td>
                            <td align="right" style="word-break:break-all">&nbsp;</td>
                        	<?	
                        }								
						$td_va_color=$td_vq_color='';
						if( $grand_variance_qnty < 0){$td_vq_color="#f00";}
						if((($grand_projectamount+$grand_confirmamount)-$total_tot_sales_qnty_val) < 0){$td_va_color="#f00";}								
                        ?>
                        <td align="right" style="word-break:break-all" bgcolor="<?=$td_vq_color;?>"><b><?=number_format($grand_variance_qnty); ?></b></td>
                        <td align="right" style="word-break:break-all" bgcolor="<?=$td_va_color;?>"><b><?=number_format($grand_variance_amount,2,'.',','); ?></b></td>
                        <td align="right" style="word-break:break-all" bgcolor="<?=$td_va_color;?>"><b><?=number_format($grand_variance_minute,2,'.',','); ?></b></td>
                          
                        <td align="right" style="word-break:break-all">&nbsp;</td>
                        <td align="right" style="word-break:break-all">&nbsp;</td>
                        <td align="right" style="word-break:break-all">&nbsp;</td>
                        <td align="right" style="word-break:break-all">&nbsp;</td>
                    </tr>
                </tfoot>    
			</table>
		</div>                 
    </fieldset> 
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