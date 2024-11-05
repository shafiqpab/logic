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
	echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "");
	exit();
}

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location", 120, "select id, location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "--Select Location--", $selected, "load_drop_down('requires/accounts_component_report_controller', this.value+'_'+$('#cbo_production_process').val(), 'load_drop_down_floor', 'floor_td' );" );	
	exit();
}

if ($action == "load_drop_down_floor") 
{
	$exdata=explode("_",$data);
	$location_id=$exdata[0];
	$production_process=$exdata[1];
	if($production_process==1) $production_process_data="5";
	if($production_process==2) $production_process_data="7,8,9";
	if($location_id!=0 && $production_process!=0)
	{
		echo create_drop_down("cbo_floor", 120, "select id, floor_name from lib_prod_floor where status_active =1 and is_deleted=0 and location_id='$location_id' and production_process in ($production_process_data) order by floor_name", "id,floor_name", 1, "--Select Floor--", $selected, "", 0);
	}
	else
	{
		echo create_drop_down( "cbo_floor", 120, $blank_array,"", 1,"--Select Floor--", $selected, "",1 );
	}
    exit();
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$report_type=str_replace("'","",$reporttype);
	$wcompany_name=str_replace("'","",$cbo_wcompany_name);
	$cbo_location=str_replace("'","",$cbo_location);
	$production_process=str_replace("'","",$cbo_production_process);
	$cbo_floor=str_replace("'","",$cbo_floor);
	$cbo_year=str_replace("'","",$cbo_year);
	$hidden_style_ref_id=str_replace("'","",$hidden_style_ref_id);
	
	//*****txt_date_from*txt_date_to

	if($wcompany_name!=0) $wcompany_cond=" and c.serving_company=$wcompany_name"; else $wcompany_cond="";
	if($cbo_location!=0) $wlocation_cond=" and c.location=$cbo_location"; else $wlocation_cond="";
	if($production_process==1)  //Sewing Output
	{
		$production_process_cond="5";
		$production_input_cond="4"; 
		$process_output="Sewing Output";
		$processtitle_formula="Sewing Output= Input-(Output+Reject)";
	}
	else if($production_process==2) //Poly Output
	{
		$production_process_cond="11";
		$production_input_cond="5"; 
		$process_output="Poly Output";
		$processtitle_formula="Poly Output = Sewing Output-(Poly Output+Reject)";
	}
	if($cbo_floor!=0) $floor_cond=" and c.floor_id=$cbo_floor"; else $floor_cond="";

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
		$year_field_con=" and to_char(a.insert_date,'YYYY')";
		if(trim($cbo_year)!=0) $year_cond=" $year_field_con=$cbo_year"; else $year_cond="";
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
		$date_cond=" and c.production_date between '$start_date' and '$end_date'";
		
		$date_string=change_date_format($start_date).' To '.change_date_format($end_date);
	}
	$style_job_cond = '';
	if(!empty($hidden_style_ref_id))
	{
		$style_job_cond = " and a.id in ($hidden_style_ref_id) ";
	}
	$companyArr=return_library_array( "select id,company_name from lib_company", "id", "company_name");
	$buyerArr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$seasonArr=return_library_array( "select id, season_name from lib_buyer_season", "id", "season_name");
	ob_start();
	if($report_type==1)
	{
		$sql_mst="select a.buyer_name, a.client_id, a.style_ref_no, a.job_no_prefix_num, a.job_no, a.season_buyer_wise, a.job_quantity, a.order_uom, a.total_set_qnty as ratio, b.id as po_id, b.po_number, b.po_quantity, b.unit_price, sum(c.production_quantity) as outputQty, sum(c.reject_qnty) as reject_qnty
		
		from wo_po_details_master a, wo_po_break_down b, pro_garments_production_mst c where a.job_no=b.job_no_mst and b.id=c.po_break_down_id 
		and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.production_type='$production_process_cond'
		
		$wcompany_cond $style_job_cond $wlocation_cond $floor_cond $buyer_id_cond $date_cond  group by a.buyer_name, a.client_id, a.style_ref_no, a.job_no_prefix_num, a.job_no, a.season_buyer_wise, a.job_quantity, a.order_uom, a.total_set_qnty, b.id, b.po_number, b.po_quantity, b.unit_price order by b.id ASC";//$year_cond and a.job_no='UG-19-00152'
		//echo $sql_mst; // die;
		$sql_mst_res=sql_select($sql_mst); $mst_data_arr=array(); $tot_rows=0; $poIds=''; $jobNos="";
		foreach($sql_mst_res as $row)
		{
			$tot_rows++;
			$poIds.=$row[csf("po_id")].",";
			$jobNos.="'".$row[csf("job_no")]."',";
			
			$output_qty=0; $reject_qty=0;
			$output_qty=$row[csf('outputQty')];
			$reject_qty=$row[csf('reject_qnty')];
			
			$mst_data_arr[$row[csf('po_id')]]=$row[csf('buyer_name')].'__'.$row[csf('client_id')].'__'.$row[csf('style_ref_no')].'__'.$row[csf('job_no_prefix_num')].'__'.$row[csf('job_no')].'__'.$row[csf('season_buyer_wise')].'__'.$row[csf('job_quantity')].'__'.$row[csf('order_uom')].'__'.$row[csf('ratio')].'__'.$row[csf('po_number')].'__'.$row[csf('po_quantity')].'__'.$row[csf('unit_price')].'__'.$output_qty.'__'.$reject_qty;
		}
		unset($sql_mst_res);
		//echo "<pre>";
		//print_r($mst_data_arr);
		
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
		
		$poIds_cond=""; $bpoIds_cond=""; $lpoIds_cond=""; $issPoIds_cond="";
		if($db_type==2 && $tot_rows>1000)
		{
			$poIds_cond=" and (";
			$bpoIds_cond=" and (";
			$lpoIds_cond=" and (";
			$issPoIds_cond=" and (";
			$poIdsArr=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$poIds_cond.=" po_break_down_id in($ids) or ";
				$bpoIds_cond.=" b.po_break_down_id in($ids) or ";
				$lpoIds_cond.=" po_id in($ids) or ";
				$issPoIds_cond.=" b.order_id in($ids) or ";
			}
				
			$poIds_cond=chop($poIds_cond,'or ');
			$poIds_cond.=")";
			
			$bpoIds_cond=chop($bpoIds_cond,'or ');
			$bpoIds_cond.=")";
			
			$lpoIds_cond=chop($lpoIds_cond,'or ');
			$lpoIds_cond.=")";
			
			$issPoIds_cond=chop($issPoIds_cond,'or ');
			$issPoIds_cond.=")";
		}
		else
		{
			$poIds_cond=" and po_break_down_id in ($poIds)";
			$bpoIds_cond=" and b.po_break_down_id in ($poIds)";
			$lpoIds_cond=" and po_id in ($poIds)";
			$issPoIds_cond=" and b.order_id in ($poIds)";
		}
		
		$sql_input_output="select po_break_down_id, sum(production_quantity) as inputQty from pro_garments_production_mst where status_active=1 and is_deleted=0 and production_type='$production_input_cond' $poIds_cond group by po_break_down_id";
		$sql_input_output_res=sql_select($sql_input_output); $input_output_data_arr=array();
		foreach($sql_input_output_res as $irow)
		{
			$input_output_data_arr[$irow[csf('po_break_down_id')]]['qty']=$irow[csf('inputQty')];
		}
		unset($sql_input_output_res);
		
		$budget_sql="select job_no, costing_per, sew_smv from wo_pre_cost_mst where status_active=1 and is_deleted=0 $budget_job_cond";
		$budget_sql_res=sql_select($budget_sql); $budget_arr=array();
		
		foreach($budget_sql_res as $brow)
		{
			$budget_arr[$brow[csf('job_no')]]['smv']=$brow[csf('sew_smv')];
			$budget_arr[$brow[csf('job_no')]]['costing_per']=$brow[csf('costing_per')];
		}
		unset($budget_sql_res);
		
		$booking_sql="select a.booking_type, a.short_booking_type, a.entry_form, a.item_category, b.process, b.po_break_down_id, b.amount from wo_booking_mst a, wo_booking_dtls b where a.booking_no=b.booking_no and a.booking_type in (1,2,3,4,5,6) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $bpoIds_cond";
		$booking_sql_res=sql_select($booking_sql); $booking_arr=array();
		foreach($booking_sql_res as $rowb)
		{
			$bookingType=0;
			if($rowb[csf('booking_type')]==1 || $rowb[csf('booking_type')]==4) //Fabric
			{
				if($rowb[csf('short_booking_type')]!=2) 
				{
					if($rowb[csf('short_booking_type')]!=3) $bookingType=1;
				}
			}
			if($rowb[csf('booking_type')]==2 || $rowb[csf('booking_type')]==5) $bookingType=2; //Trims
			//if($rowb[csf('booking_type')]==3 && $rowb[csf('process')]==35) $bookingType=3; //Aop
			if($rowb[csf('process')]==35) $bookingType=3; //Aop
			if($rowb[csf('booking_type')]==6) $bookingType=6; //EMB
			
			$booking_arr[$rowb[csf('po_break_down_id')]][$bookingType]['amt']+=$rowb[csf('amount')];
		}
		unset($booking_sql_res);
		
		$sql_gen_acc= "select b.order_id, c.item_group_id, d.recv_trans_id, sum(d.issue_qnty) as cons_quantity, avg(b.cons_rate) as rate, sum(b.cons_amount) as amount 
				 from inv_issue_master a, inv_transaction b, product_details_master c, inv_mrr_wise_issue_details d where a.id=b.mst_id and b.transaction_type in(2) and d.issue_trans_id=b.id and c.id=d.prod_id and a.entry_form=21 and d.entry_form=21 and c.id=b.prod_id and  b.status_active=1 and  b.item_category=4 and a.is_deleted=0 and b.cons_quantity>0 $issPoIds_cond group by b.order_id, c.item_group_id, d.recv_trans_id";
				 
		$gen_trim_result=sql_select($sql_gen_acc);
		$sum_total_accesso_amount=0;
		//$exchane_rate=return_field_value("conversion_rate","currency_conversion_rate"," currency=2  and is_deleted=0 and status_active=1 order by id desc");
		$trans_rec_ids='';
		foreach($gen_trim_result as $rows)
		{
			if($trans_rec_ids=='') $trans_rec_ids=$rows[csf('recv_trans_id')];else $trans_rec_ids.=",".$rows[csf('recv_trans_id')];
		}
		$all_transID=array_unique(explode(",",$trans_rec_ids));
		
		$trans_arr_cond=array_chunk($all_transID,1000, true);
		$trans_arr_cond_in=""; $t=0;
		foreach($trans_arr_cond as $key=>$value)
		{
			if($t==0)
			{
				$trans_arr_cond_in=" and d.recv_trans_id  in(".implode(",",$value).")"; 
			}
			else 
			{
				$trans_arr_cond_in.=" or d.recv_trans_id  in(".implode(",",$value).")";
			}
			$t++;
		}
		
		$conv_sql="select a.id as item_id,a.conversion_factor from lib_item_group a,product_details_master b where a.id=b.item_group_id and b.entry_form=20 and b.item_category_id=4 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 ";
		$sql_conv_result=sql_select($conv_sql); $conversion_arr=array();
		foreach($sql_conv_result as $row)
		{
			$conversion_arr[$row[csf("item_id")]]['conver_rate']=$row[csf("conversion_factor")];
		}
		unset($sql_conv_result);
		
		$sql_mrr_recv= "select b.id as trans_id, b.order_rate, b.order_ile, c.item_group_id
			from inv_receive_master a, inv_transaction b, product_details_master c, inv_mrr_wise_issue_details d where a.id=b.mst_id and c.id=b.prod_id and d.recv_trans_id=b.id and b.transaction_type=1 and a.entry_form=20 and b.status_active=1 and b.is_deleted=0 $trans_arr_cond_in ";
		$mrr_recv_result=sql_select($sql_mrr_recv);
		$mrr_recv_arr=array();
		foreach($mrr_recv_result as $row)
		{
			$mrr_recv_arr[$row[csf('trans_id')]]['rate']=$row[csf('order_rate')]+$row[csf('order_ile')];
		}
		unset($mrr_recv_result);
		
		foreach($gen_trim_result as $row) //For Summary
		{
			$converrate=$conversion_arr[$row[csf("item_group_id")]]['conver_rate'];
			$orderrate=($mrr_recv_arr[$row[csf('recv_trans_id')]]['rate']/$converrate);
			$gen_amount=$row[csf('cons_quantity')]*$orderrate;
			//echo $amount=number_format($rows[csf('cons_quantity')]*$gen_rate,6,'.','');
			//$sum_total_accesso_amount+=number_format($gen_amount,6,'.','');
			$booking_arr[$row[csf('order_id')]]['2']['amt']+=$gen_amount;
		}
		unset($gen_trim_result);
		
		//$lab_sql="select order_id, wo_value from wo_labtest_order_dtls where status_active=1 and is_deleted=0 $lpoIds_cond ";// Lab Test
		$lab_sql="select po_id, amount from wo_labtest_dtls where status_active=1 and is_deleted=0 $lpoIds_cond ";
		$lab_sql_res=sql_select($lab_sql); $lab_arr=array();
		foreach($lab_sql_res as $rowl)
		{
			$lab_arr[$rowl[csf('po_id')]]['amt']+=$rowl[csf('amount')];
		}
		unset($lab_sql_res);
		
		?>
		<div>
		<fieldset style="width:100%;">
            <table width="2400">
            	<tr class="form_caption">
                	<td colspan="29" align="center"><strong><? echo $companyArr[$wcompany_name]; ?></strong></td>
                </tr>
                <tr class="form_caption">
                	<td colspan="29" align="center"><strong><? echo $report_title.' '.$date_string.' ['.$process_output.']'; ?></strong></td>
                </tr>
            </table>
            <table class="rpt_table" width="2400" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <tr>
                        <th width="30" rowspan="2">SL</th>
                        <th colspan="10">Style Info.</th>
                        <th colspan="6">As On Budget (DZN)</th>
                        <th colspan="10">As On Booking</th>
                        <th colspan="2">WIP Info.</th>
                    </tr>
                    <tr>
                    	<th width="100">Buyer</th>
                        <th width="100">Buyer Client</th>
                        <th width="100">Style Ref.</th>
                        <th width="100">Job No</th>
                        <th width="90">Season</th>
                        <th width="100">PO No</th>
                        <th width="90">Job Qty.</th>
                        <th width="80">PO Qty.</th>
                        <th width="70">SMV</th>
                        <th width="70">FOB Price</th>
                        
                        <th width="80" title="(Consumption (Kg)/PO Qty.)*12">Fb Cons. (Kg)</th>
                        <th width="80" title="(Consumption (Yds)/PO Qty.)*12">Fb Cons. (Yds)</th>
                        <th width="80" title="(Consumption (Mtr)/PO Qty.)*12">Fb Cons. (Mtr)</th>
                        <th width="70" title="(Amount (Kg)/PO Qty.)*12">Avg. Price (Kg)</th>
                        <th width="70" title="(Amount (Yds)/PO Qty.)*12">Avg. Price (Yds)</th>
                        <th width="70" title="(Amount (Mtr)/PO Qty.)*12">Avg. Price (Mtr)</th>
                        
                        <th width="100">F.Booking Amount</th>
                        <th width="80">TTL AOP Cost</th>
                        <th width="70">AOP Cost/Dzn</th>
                        <th width="80">TTL Acc. Amount</th>
                        <th width="70">Acc. Cost/Dzn</th>
                        <th width="80">TTL Emb Cost</th>
                        <th width="70">Emb Cost/Dzn</th>
                        <th width="80">TTL Lab Test</th>
                        <th width="70">Lab test /Dzn</th>
                        <th width="100">TTL Cost</th>
                        
                        <th width="80" title="<? echo $processtitle_formula; ?>">WIP Qty</th>
                        <th title="(TTL Cost / PO Qty.) * WIP Qty.">WIP Value</th>
                    </tr>
                </thead>
            </table>
            <div style="width:2400px; max-height:400px; overflow-y:scroll" id="scroll_body">
            <table class="rpt_table" width="2380" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
            <?
            $condition= new condition();
            
            if($jobNos!=''){
                 //$condition->job_no(" in ($jobNos) ");
				 //$condition->job_no(" in ('OG-19-00523') ");
				 $condition->po_id_in("$poIds"); 
            }
             
            $condition->init();
			$fabric= new fabric($condition);
			//echo $fabric->getQuery(); die;
            $fabric_costing_arr=$fabric->getQtyArray_by_order_knitAndwoven_greyAndfinish();
			$fabric_amount_arr=$fabric->getAmountArray_by_order_knitAndwoven_greyAndfinish();
			
			//print_r($fabric_amount_arr); die;
    		$i=1;
            foreach($mst_data_arr as $po_id=>$rowd)
            {
                if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$exdata=explode("__",$rowd);
				$buyer_name=$client_id=$style_ref_no=$job_no_prefix_num=$job_no=$season_buyer_wise=$order_uom=$po_number=""; 
				$job_quantity=$ratio=$po_quantity=$unit_price=$outputQty=$rejectQty=$inputQty=$wipQty=0;
				$buyer_name=$exdata[0];
				$client_id=$exdata[1];
				$style_ref_no=$exdata[2];
				$job_no_prefix_num=$exdata[3];
				$job_no=$exdata[4];
				$season_buyer_wise=$exdata[5];
				$job_quantity=$exdata[6];
				$order_uom=$exdata[7];
				$ratio=$exdata[8];
				$po_number=$exdata[9];
				$po_quantity=$exdata[10];
				$unit_price=$exdata[11];
				$outputQty=$exdata[12];
				$rejectQty=$exdata[13];
				
				$inputQty=$input_output_data_arr[$po_id]['qty'];
				$wipQty=$inputQty-($outputQty+$rejectQty);
				
				$title_output=$inputQty.'-('.$outputQty.'+'.$rejectQty.')';
				
				$greyConsKg=$finConsKg=$greyConsYds=$finConsYds=$greyConsMtr=$finConsMtr=$kgCons=$ydsCons=$mtrCons=0;
				
				$greyConsKg=$fabric_costing_arr['knit']['grey'][$po_id][12];
				$finConsKg=$fabric_costing_arr['knit']['finish'][$po_id][12];
				
				$greyConsMtr=$fabric_costing_arr['knit']['grey'][$po_id][23];
				$finConsMtr=$fabric_costing_arr['knit']['finish'][$po_id][23];
				
				$greyConsYds=$fabric_costing_arr['knit']['grey'][$po_id][27];
				$finConsYds=$fabric_costing_arr['knit']['finish'][$po_id][27];
				
				$kgCons=$greyConsKg;//+$finConsKg
				$ydsCons=$greyConsYds;//+$finConsYds
				$mtrCons=$greyConsMtr;//+$finConsMtr
				
				$kgConsDzn=$ydsConsDzn=$mtrConsDzn=0;
				
				$kgConsDzn=($kgCons/$po_quantity)*12;
				$ydsConsDzn=($ydsCons/$po_quantity)*12;
				$mtrConsDzn=($mtrCons/$po_quantity)*12;
				
				$greyAmtKg=$finAmtKg=$greyAmtYds=$finAmtYds=$greyAmtMtr=$finAmtMtr=$kgAmt=$ydsAmt=$mtrAmt=0;
				
				$greyAmtKg=$fabric_amount_arr['knit']['grey'][$po_id][12];
				$finAmtKg=$fabric_amount_arr['knit']['finish'][$po_id][12];
				
				$greyAmtMtr=$fabric_amount_arr['knit']['grey'][$po_id][23];
				$finAmtMtr=$fabric_amount_arr['knit']['finish'][$po_id][23];
				
				$greyAmtYds=$fabric_amount_arr['knit']['grey'][$po_id][27];
				$finAmtYds=$fabric_amount_arr['knit']['finish'][$po_id][27];
				
				$kgAmt=$greyAmtKg;//+$finAmtKg
				$ydsAmt=$greyAmtYds;//+$finAmtYds
				$mtrAmt=$greyAmtMtr;//+$finAmtMtr
				
				$kgAmtDzn=$ydsAmtDzn=$mtrAmtDzn=0;
				
				$kgAmtDzn=($kgAmt/$po_quantity)*12;
				$ydsAmtDzn=($ydsAmt/$po_quantity)*12;
				$mtrAmtDzn=($mtrAmt/$po_quantity)*12;
				
				$fabAmt=$aopAmt=$accAmt=$embAmt=$labAmt=$ttlCost=$wipValue=0;
				$fabAmtDzn=$aopAmtDzn=$accAmtDzn=$embAmDznt=$labAmtDzn=0;
				
				$fabAmt=$booking_arr[$po_id][1]['amt'];
				$aopAmt=$booking_arr[$po_id][3]['amt'];
				$accAmt=$booking_arr[$po_id][2]['amt'];
				$embAmt=$booking_arr[$po_id][6]['amt'];
				$labAmt=$lab_arr[$po_id]['amt'];
				
				//$fabAmtDzn=($fabAmt/$po_quantity)*12;
				$aopAmtDzn=($aopAmt/$po_quantity)*12;
				$accAmtDzn=($accAmt/$po_quantity)*12;
				$embAmDznt=($embAmt/$po_quantity)*12;
				$labAmtDzn=($labAmt/$po_quantity)*12;
				
				$ttlCost=$fabAmt+$aopAmt+$accAmt+$embAmt+$labAmt;
				$wipValue=($ttlCost/$po_quantity)*$wipQty;
				
               
                ?>
                 <tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>" style="font-size:11px">
                    <td width="30" align="center"><? echo $i; ?></td>
                    <td width="100"><? echo $buyerArr[$buyer_name]; ?></td>
                    <td width="100"><? echo $buyerArr[$client_id]; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $style_ref_no; ?></td>
                    <td width="100"><? echo $job_no; ?></td>
                    <td width="90"><? echo $seasonArr[$season_buyer_wise]; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $po_number; ?></td>
                    <td width="90" align="right"><? echo number_format($job_quantity); ?></td>
                    <td width="80" align="right"><? echo number_format($po_quantity); ?></td>
                    <td width="70" align="right"><? echo number_format($budget_arr[$job_no]['smv'],4); ?></td>
                    <td width="70" align="right"><? echo number_format($unit_price,4); ?></td>
                    <td width="80" align="right" title="<? echo '('.$kgCons.'/'.$po_quantity.')*12'; ?>"><? if($kgConsDzn!=0) echo number_format($kgConsDzn,4); else echo""; ?></td>
                    <td width="80" align="right" title="<? echo '('.$ydsCons.'/'.$po_quantity.')*12'; ?>"><? if($ydsConsDzn!=0) echo number_format($ydsConsDzn,4); else echo""; ?></td>
                    <td width="80" align="right" title="<? echo '('.$mtrCons.'/'.$po_quantity.')*12'; ?>"><? if($mtrConsDzn!=0) echo number_format($mtrConsDzn,4); else echo""; ?></td>
                    
                    <td width="70" align="right" title="<? echo '('.$kgAmt.'/'.$po_quantity.')*12'; ?>"><? if($kgAmtDzn!=0) echo number_format($kgAmtDzn,4); else echo""; ?></td>
                    <td width="70" align="right" title="<? echo '('.$ydsAmt.'/'.$po_quantity.')*12'; ?>"><? if($ydsAmtDzn!=0) echo number_format($ydsAmtDzn,4); else echo""; ?></td>
                    <td width="70" align="right" title="<? echo '('.$mtrAmt.'/'.$po_quantity.')*12'; ?>"><? if($mtrAmtDzn!=0) echo number_format($mtrAmtDzn,4); else echo""; ?></td>
                    <td width="100" align="right" ><? if($fabAmt!=0) echo number_format($fabAmt,4); else echo""; ?></td>
                    <td width="80" align="right"><? if($aopAmt!=0) echo number_format($aopAmt,4); else echo""; ?></td>
                    <td width="70" align="right"><? if($aopAmtDzn!=0) echo number_format($aopAmtDzn,4); else echo""; ?></td>
                    <td width="80" align="right"><? if($accAmt!=0) echo number_format($accAmt,4); else echo""; ?></td>
                    <td width="70" align="right"><? if($accAmtDzn!=0) echo number_format($accAmtDzn,4); else echo""; ?></td>
                    <td width="80" align="right"><? if($embAmt!=0) echo number_format($embAmt,4); else echo""; ?></td>
                    <td width="70" align="right"><? if($embAmDznt!=0) echo number_format($embAmDznt,4); else echo""; ?></td>
                    <td width="80" align="right"><? if($labAmt!=0) echo number_format($labAmt,4); else echo""; ?></td>
                    <td width="70" align="right"><? if($labAmtDzn!=0) echo number_format($labAmtDzn,4); else echo""; ?></td>
                    <td width="100" align="right"><? if($ttlCost!=0) echo number_format($ttlCost,4); else echo""; ?></td>
                    <td width="80" align="right" title="<? echo $title_output;  ?>"><? if($wipQty!=0) echo number_format($wipQty); else echo""; ?></td>
                    <td align="right" title="<? echo '('.$ttlCost.'/'.$po_quantity.')*.'.$wipQty; ?>"><? if($wipValue!=0) echo number_format($wipValue,4); else echo""; ?></td>
                  </tr>
                <?
                $i++;
				$grandFabAmt+=$fabAmt;
				$grandAopAmt+=$aopAmt;
				$grandAccAmt+=$accAmt;
				$grandEmbAmt+=$embAmt;
				$grandLabAmt+=$labAmt;
				$grandTtlCost+=$ttlCost;
				$grandWipQty+=$wipQty;
				$grandWipValue+=$wipValue;
            }
            ?>
        </table>
        </div>
            <table class="tbl_bottom" width="2400" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <tr>
                        <td width="30">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="90">&nbsp;</td>
                        <td width="100">&nbsp;</td>
                        <td width="90">&nbsp;</td>
                        <td width="80">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="80">&nbsp;</td>
                        <td width="80">&nbsp;</td>
                        <td width="80">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="70">&nbsp;</td>
                        <td width="70">Total:</td>
                        <td width="100" id="td_fabAmt"><? echo number_format($grandFabAmt,4); ?></td>
                        <td width="80" id="td_aopAmt"><? echo number_format($grandAopAmt,4); ?></td>
                        <td width="70">&nbsp;</td>
                        <td width="80" id="td_accAmt"><? echo number_format($grandAccAmt,4); ?></td>
                        <td width="70">&nbsp;</td>
                        <td width="80" id="td_embAmt"><? echo number_format($grandEmbAmt,4); ?></td>
                        <td width="70">&nbsp;</td>
                        <td width="80" id="td_labAmt"><? echo number_format($grandLabAmt,4); ?></td>
                        <td width="70">&nbsp;</td>
                        <td width="100" id="td_ttlAmt"><? echo number_format($grandTtlCost,4); ?></td>
                        <td width="80" id="td_wipQty"><? echo number_format($grandWipQty,4); ?></td>
                        <td id="td_wipValue"><? echo number_format($grandWipValue,4); ?></td>
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

if($action=="style_search_popup")
{
	echo load_html_head_contents("Style Ref Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		var selected_id = new Array; var selected_name = new Array;
		function check_all_data()
		{
			var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;
			tbl_row_count = tbl_row_count - 1;

			for( var i = 1; i <= tbl_row_count; i++ )
			{
				$('#tr_'+i).trigger('click'); 
			}
		}
		
		function toggle( x, origColor ) {
			var newColor = 'yellow';
			if ( x.style ) {
				x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
			}
		}
		
		function js_set_value( str ) {
			console.log(str);
			if (str!="") str=str.split("_");
			 
			toggle( document.getElementById( 'tr_' + str[0] ), '#FFFFCC' );
			 
			if( jQuery.inArray( str[1], selected_id ) == -1 ) {
				selected_id.push( str[1] );
				selected_name.push( str[2] );
				
			}
			else {
				for( var i = 0; i < selected_id.length; i++ ) {
					if( selected_id[i] == str[1] ) break;
				}
				selected_id.splice( i, 1 );
				selected_name.splice( i, 1 );
			}
			var id = ''; var name = '';
			for( var i = 0; i < selected_id.length; i++ ) {
				id += selected_id[i] + ',';
				name += selected_name[i] + '*';
			}
			
			id = id.substr( 0, id.length - 1 );
			name = name.substr( 0, name.length - 1 );
			console.log(name);
			//console.log(id);
			$('#hide_style_id').val( id );
			$('#hide_style_ref').val( name );
		}
	
    </script>

	</head>

	<body>
	<div align="center">
		<form name="styleRef_form" id="styleRef_form">
			<fieldset style="width:750px;">
	            <table width="750" cellspacing="0" cellpadding="0" border="1" rules="all" align="center" class="rpt_table" id="tbl_list">
	            	<thead>
	                    <th>Buyer</th>
	                    <th>Search By</th>
	                    <th id="search_by_td_up" width="170">Please Enter Order No</th>
	                    
	                    <th><input type="reset" name="button" class="formbutton" value="Reset"  style="width:100px;"></th> 
	                    <input type="hidden" name="hide_style_ref" id="hide_style_ref" value="" />
	                    <input type="hidden" name="hide_style_id" id="hide_style_id" value="" />
						
	                </thead>
	                <tbody>
	                	<tr>
	                        <td align="center">
	                        	 <? 
									echo create_drop_down( "cbo_buyer_name", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company=$companyID $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buy.buyer_name","id,buyer_name",1, "-- All Buyer--",0,"",0 );
								?>
	                        </td>                 
	                        <td align="center">	
	                    	<?
	                       		$search_by_arr=array(1=>"Style Ref",2=>"Job No");
								$dd="change_search_event(this.value, '0*0', '0*0', '../../') ";							
								echo create_drop_down( "cbo_search_by", 110, $search_by_arr,"",0, "--Select--", "",$dd,0 );
							?>
	                        </td>     
	                        <td align="center" id="search_by_td">				
	                            <input type="text" style="width:130px" class="text_boxes" name="txt_search_common" id="txt_search_common" />	
	                        </td> 
	                        
	                        <td align="center">
	                        	<input type="button" name="button" class="formbutton" value="Show" onClick="show_list_view ('<? echo $companyID; ?>'+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('cbo_year_selection').value, 'create_style_ref_search_list_view', 'search_div', 'accounts_component_report_controller', 'setFilterGrid(\'tbl_list_search\',-1)');" style="width:100px;" />
	                    	</td>
	                    </tr>
	                    <tr>
	                        <td colspan="5" valign="middle"><? echo load_month_buttons(1); ?></td>
	                    </tr>
	            	</tbody>
	           	</table>
	            <div style="margin-top:5px" id="search_div"></div>
			</fieldset>
		</form>
	</div>
	</body>           
	<script src="../../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
		exit(); 
}

if($action=="create_style_ref_search_list_view")
{
	$data=explode('**',$data);
	$company_id=$data[0];
	// echo "<pre>";
	// print_r($data);

	if($data[1]==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else $buyer_id_cond="";
	}
	else $buyer_id_cond=" and a.buyer_name=$data[1]";
	
	$search_by=$data[2];
	$search_string="%".trim($data[3])."%";

	if($search_by==1) $search_field="a.style_ref_no"; 	
	else $search_field="a.job_no";

	
	$company_short_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$arr=array (0=>$company_short_arr,1=>$buyer_arr);
	
	if($db_type==0) $year_field=" YEAR(a.insert_date) as year"; 
	else if($db_type==2) $year_field="  to_char(a.insert_date,'YYYY') as year";
	else $year_field="";//defined Later
	 
	
	$year_cond = '';
	if($db_type==0) $year_cond=" and YEAR(a.insert_date)=$data[4]"; 
	else if($db_type==2) $year_cond=" and to_char(a.insert_date,'YYYY')=$data[4]";
	$sql="SELECT a.id,a.job_no, $year_field, a.job_no_prefix_num, a.company_name, a.buyer_name, a.style_ref_no from wo_po_details_master a where a.status_active=1 and a.is_deleted=0 and a.company_name=$company_id and $search_field like '$search_string' $buyer_id_cond $year_cond  order by a.id";
	//echo $sql;
	echo create_list_view("tbl_list_search", "Company,Buyer Name,Year,Job No,Style Ref. No", "70,70,50,70","500","210",0, $sql , "js_set_value", "id,style_ref_no","",1,"company_name,buyer_name,0,0,0",$arr,"company_name,buyer_name,year,job_no_prefix_num,style_ref_no","",'','','',1) ;
   exit(); 
}
?>