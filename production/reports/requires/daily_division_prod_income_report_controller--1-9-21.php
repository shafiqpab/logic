<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------------------------------

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_id", 150, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name","id,location_name", 1, "-- Select --", $selected, "",0 );     	 
}

	$company_library=return_library_array( "select id,company_name from lib_company ", "id", "company_name"  );
	$buyer_short_library=return_library_array( "select id,short_name from lib_buyer", "id", "short_name");
	$company_short_library=return_library_array( "select id,company_short_name from lib_company", "id", "company_short_name"  );

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	//echo $datediff;
	$cbo_company=str_replace("'","",$cbo_company_id);
	$buyer_id=str_replace("'","",$cbo_buyer_id);
	$division_id=str_replace("'","",$cbo_division_id);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	
	if($cbo_company==0) $cbo_company_cond=""; else $cbo_company_cond=" and a.company_id=$cbo_company";
	if($cbo_company==0) $cbo_company_cond2=""; else $cbo_company_cond2=" and b.company_name=$cbo_company";
	if($cbo_company==0) $cbo_company_cond3=""; else $cbo_company_cond3=" and company_id=$cbo_company";
	if($cbo_company==0) $knit_company_cond=""; else $knit_company_cond=" and a.knitting_company=$cbo_company";
	
	if($buyer_id=='') 
	{
		 $buyerCond=""; $sub_buyerCond="";$buyerCond2="";
	}
	else {
		$buyerCond=" and b.buyer_name in($buyer_id)";
		$sub_buyerCond=" and a.party_id in($buyer_id)";
		$buyerCond2=" and f.buyer_name in($buyer_id)";
	}
	
	
	
	
        $date_data_popup=$date_from.'_'.$date_to;
	if($type==1){
		if($db_type==0)
		{
			if( $date_from==0 && $date_to==0 )
			{ 
			 $production_date=""; $dyeing_prod_date=""; $kniting_prod_date="";
			}
		   else 
			{ //receive_date
				$production_date= " and c.production_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";			
				$dyeing_prod_date= " and c.process_end_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
				$kniting_prod_date= " and a.receive_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
				$issue_dateCond= " and a.issue_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
			}
		}
		else
		{
			if($date_from==0 && $date_to==0 ) 
			{
				 $production_date="";  $dyeing_prod_date="";
			}
			else 
			{ 
				$production_date= " and c.production_date between '".date("j-M-Y",strtotime(str_replace("'","",$date_from)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$date_to)))."'";
				$dyeing_prod_date= " and c.process_end_date between '".date("j-M-Y",strtotime(str_replace("'","",$date_from)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$date_to)))."'";
				$kniting_prod_date= " and a.receive_date between '".date("j-M-Y",strtotime(str_replace("'","",$date_from)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$date_to)))."'";
				$issue_dateCond= " and a.issue_date between '".date("j-M-Y",strtotime(str_replace("'","",$date_from)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$date_to)))."'";
				$production_date2= " and production_date between '".date("j-M-Y",strtotime(str_replace("'","",$date_from)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$date_to)))."'";
				//$issue_date_cond= " and a.issue_date between '".date("j-M-Y",strtotime(str_replace("'","",$date_from)))."' and '".date("j-M-Y",strtotime(str_replace("'","",$date_to)))."'";
					//$issue_date_cond=" and a.issue_date between '$start_date' and '$end_date'";
			}
		}
			
	//	$sql_floor=sql_select("Select a.id, a.floor_name from  lib_prod_floor a where a.location_id=$cbo_location $cbo_company_cond and a.status_active=1 and a.is_deleted=0 order by a.floor_name ");
    //  $sql_floor_cutting=sql_select("SELECT a.floor_id from  pro_garments_production_mst a where a.location=$cbo_location $cbo_company_cond and a.production_type=1 and a.status_active=1 and a.is_deleted=0 $production_date    group by a.floor_id order by  a.floor_id ");

      //  $sql_floor_sewing=sql_select("SELECT a.floor_id from  pro_garments_production_mst a where a.location=$cbo_location $cbo_company_cond and a.production_type=5 and a.status_active=1 and a.is_deleted=0 $production_date   group by a.floor_id order by  a.floor_id ");

	if($division_id==1)
	{
		$search_typeCond="1";		
	}
	else if($division_id==2)
	{
		$search_typeCond="2";		
	}
	else if($division_id==3)
	{
		$search_typeCond="3";		
	}
	else if($division_id==4)
	{
		$search_typeCond="4";		
	}
	else $search_typeCond="0";
	
       if($division_id==0 || $search_typeCond==1 || $search_typeCond==2 || $search_typeCond==3 || $search_typeCond==4) //All Division
	   {
			  $sewing_outSql = "select a.id as po_id, a.po_quantity, a.plan_cut, a.po_number, a.po_quantity, b.company_name, b.buyer_name, b.style_ref_no, b.gmts_item_id, b.order_uom, b.job_no,a.job_id, b.location_name,c.production_quantity ,c.item_number_id
			from wo_po_break_down a, wo_po_details_master b,pro_garments_production_mst c
			where a.job_no_mst=b.job_no and a.id=c.po_break_down_id and c.production_type=5 and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 and  c.status_active=1 and c.is_deleted=0 $cbo_company_cond2 $buyerCond  $production_date "; 
			$sewing_prod=sql_select($sewing_outSql);
			foreach($sewing_prod as $row)
			{
				$allPoArr[$row[csf("po_id")]]=$row[csf("po_id")];
				$allJobIdArr[$row[csf("job_id")]]=$row[csf("job_id")];
				$job_po_arr[$row[csf("po_id")]]=$row[csf("job_no")];
				
			}
			
			
			$job_no_cond=where_con_using_array($allJobIdArr,0,'a.job_id');       
			 $preCostSql="select  a.job_no, a.costing_per,a.sew_effi_percent,a.efficiency_wastage_percent,a.cut_smv,a.cut_effi_percent,a.costing_date,a.exchange_rate,b.smv_set,b.smv_pcs_precost,b.gmts_item_id from wo_pre_cost_mst a,wo_po_details_mas_set_details b where a.job_id=b.job_id and a.status_active=1 and a.is_deleted=0  $job_no_cond ";
			$preCostSqlRes=sql_select($preCostSql);
			foreach($preCostSqlRes as $row)
			{
				$dzn_qnty=0;
				if($row[csf("costing_per")]==1) $dzn_qnty=12;
				else if($row[csf("costing_per")]==3) $dzn_qnty=12*2;
				else if($row[csf("costing_per")]==4) $dzn_qnty=12*3;
				else if($row[csf("costing_per")]==5) $dzn_qnty=12*4;
				else $dzn_qnty=1;
				
				$precostDataArr[$row[csf("job_no")]]['costing_per']=$dzn_qnty;
				$precostDataArr[$row[csf("job_no")]]['efficiency_wastage_percent']=$row[csf("efficiency_wastage_percent")]/100;
				$precostDataArr[$row[csf("job_no")]]['cut_smv']=$row[csf("cut_smv")];
				$precostDataArr[$row[csf("job_no")]]['cut_effi_percent']=((100-$row[csf("cut_effi_percent")]))/100;
				$precostDataArr[$row[csf("job_no")]]['costing_date']=change_date_format($row[csf("costing_date")],'','',1);
				$precostDataArr[$row[csf("job_no")]]['exchange_rate']=$row[csf("exchange_rate")];
				$item_sm_arr[$row[csf("job_no")]][$row[csf('gmts_item_id')]]=$row[csf('smv_set')];
            	$item_precost_smv_arr[$row[csf("job_no")]][$row[csf('gmts_item_id')]]=$row[csf('smv_pcs_precost')];
			
			}
 //print_r($item_precost_smv_arr);
			// =============Sewing out Production net Income============
			$sql_std_para=sql_select("select company_id,interest_expense, income_tax, cost_per_minute, applying_period_date, applying_period_to_date from lib_standard_cm_entry where  status_active=1 and is_deleted=0 order by id");
			foreach($sql_std_para as $row )
			{
				$company_id=$row[csf('company_id')];
				$applying_period_date=change_date_format($row[csf('applying_period_date')],'','',1);
				$applying_period_to_date=change_date_format($row[csf('applying_period_to_date')],'','',1);
				$diff=datediff('d',$applying_period_date,$applying_period_to_date);
				for($j=0;$j<$diff;$j++)
				{
					$date_all=add_date(str_replace("'","",$applying_period_date),$j);
					$newdate =change_date_format($date_all,'','',1);
					//$financial_para[$newdate][interest_expense]=$row[csf('interest_expense')];
					//$financial_para[$newdate][income_tax]=$row[csf('income_tax')];
					$financial_para[$company_id][$newdate][cost_per_minute]=$row[csf('cost_per_minute')];
				}
			}
			//print_r($financial_para);
		
			$sewing_prod_hnm_nextQty=$others_sewing_prod_hnm_nextQty=$sewing_prod_hnm_nextNetVal=$others_sewing_prod_hnm_nextNetVal=0;
			
			foreach($sewing_prod as $row)
			{
				//CM value calculation---------------
				$company_name=$row[csf("company_name")];
				$job_no=$job_po_arr[$row[csf("po_id")]];
				
				$dzn_qnty=$precostDataArr[$job_no]['costing_per'];
				$costing_date=$precostDataArr[$job_no]['costing_date'];
				$cpm=$financial_para[$company_name][$costing_date][cost_per_minute];
				$CUT_SMV=$precostDataArr[$job_no]['cut_smv'];
				$efficiency_wastage_per=$precostDataArr[$job_no]['efficiency_wastage_percent'];
				$exchange_rate=$precostDataArr[$job_no]['exchange_rate'];
			  	$item_smv=$item_precost_smv_arr[$job_no][$row[csf("item_number_id")]];
				
				//echo $item_smv.'='.$efficiency_wastage_per.'='.$dzn_qnty.', ';
				if($exchange_rate)
				{ 
				$CMPcs=(($item_smv*$cpm*$dzn_qnty)+($item_smv*$cpm*$dzn_qnty*$efficiency_wastage_per))/$exchange_rate;
			//	echo $exchange_rate.'='.$efficiency_wastage_per.'<br>';
				}
			  	$cm_pcs_value=$CMPcs;
				 
				if($row[csf('buyer_name')]==1 || $row[csf('buyer_name')]==10) // HnM and Next
				{
				$sewing_prod_hnm_nextQty+=$row[csf('production_quantity')];
				$buyer_hnmNext[$buyer_short_library[$row[csf('buyer_name')]]]=$buyer_short_library[$row[csf('buyer_name')]];
				$sewing_prod_hnm_nextNetVal+=($row[csf("production_quantity")]*($cm_pcs_value/12));
				}
				else
				{
					//echo $row[csf('production_quantity')].'D';;
					$others_sewing_prod_hnm_nextQty+=$row[csf('production_quantity')];
					$others_buyer_hnmNext[$buyer_short_library[$row[csf('buyer_name')]]]=$buyer_short_library[$row[csf('buyer_name')]];
					if($cm_pcs_value)
					{
					$others_sewing_prod_hnm_nextNetVal+=($row[csf("production_quantity")]*($cm_pcs_value/12));
					}
				}
			} 
			//echo $others_sewing_prod_hnm_nextNetVal;
			//========Garment End==================
			//wo_po_break_down a,wo_po_details_master b
			//==================================================Dyeing Prod===========.========================
		  $sql_data="SELECT a.id,a.batch_no,a.color_id,a.floor_id,c.entry_form,a.batch_against,(b.batch_qnty) as batch_qty,c.process_id, c.process_end_date as prod_date,a.batch_weight, a.working_company_id,b.prod_id,b.po_id,b.width_dia_type,a.color_id,a.booking_no,a.extention_no, b.item_description, a.booking_without_order,d.detarmination_id as deter_id 
		from pro_batch_create_mst a, pro_batch_create_dtls b,pro_fab_subprocess c ,product_details_master d,wo_po_break_down e,wo_po_details_master f
		where  a.id=b.mst_id  and c.batch_id=a.id and c.batch_id=b.mst_id and d.id=b.prod_id and e.id=b.po_id and e.job_id=f.id  and c.entry_form in(35)  and a.is_sales=0 and c.load_unload_id=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0
  and a.status_active=1 and a.is_deleted=0  and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and b.po_id>0 $cbo_company_cond   $buyerCond2  $dyeing_prod_date  order by a.id"; 		//and a.batch_against not in(2)
	//echo $sql_data;
	$batch_against_chk=array(2);
	$nameArray=sql_select($sql_data);
	foreach($nameArray as $row)
    {
		if(!in_array($row[csf('batch_against')],$batch_against_chk))
		{
		$po_id_array[$row[csf('po_id')]] = $row[csf('po_id')];
		}
		$batch_id_array[$row[csf('id')]] = $row[csf('id')];
		$prod_id_id_array[$row[csf('prod_id')]] = $row[csf('prod_id')];
		$all_batch_id_array[$row[csf('id')]] = $row[csf('id')];
	}
	//$job_no_cond=where_con_using_array($batch_id_array,0,'a.job_id');
	 $sql_data_special="SELECT e.id as dtls_id,a.id,a.batch_no,a.color_id,a.floor_id,c.entry_form,a.batch_against,(e.production_qty) as batch_qty,c.process_id,c.process_end_date as prod_date,a.batch_weight, a.working_company_id,b.prod_id,b.po_id,b.width_dia_type,a.color_id,a.booking_no,a.extention_no, b.item_description, a.booking_without_order,d.detarmination_id as deter_id 
		from pro_batch_create_mst a, pro_batch_create_dtls b,pro_fab_subprocess c,pro_fab_subprocess_dtls e ,product_details_master d
		where  a.id=b.mst_id  and c.batch_id=a.id and c.batch_id=b.mst_id and d.id=b.prod_id and c.id=e.mst_id and e.prod_id=b.prod_id and e.prod_id=d.id  and c.entry_form in(32,48,33,34)  and a.is_sales=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0  and e.status_active=1 and e.is_deleted=0
  and a.status_active=1 and a.is_deleted=0  and d.status_active=1 and d.is_deleted=0 and b.po_id>0 $cbo_company_cond $dyeing_prod_date
		order by a.id";		//and a.batch_against not in(2)
	//echo $sql_data;
	//$batch_against_chk=array(2);
	$nameArray_special=sql_select($sql_data_special);
	foreach($nameArray_special as $row)
    {
		$po_id_array[$row[csf('po_id')]] = $row[csf('po_id')];
	}
	//print_r($po_id_array);
	 $po_id_cond=where_con_using_array($po_id_array,0,'b.id');
	  $sql_po_buyer="SELECT a.buyer_name,b.id,b.job_no_mst,b.shipment_date,b.po_quantity,b.pub_shipment_date,b.grouping as ref_no,c.color_number_id as color_id from wo_po_break_down b,wo_po_details_master a,wo_po_color_size_breakdown c where  a.id=b.job_id and a.id=c.job_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1  $po_id_cond order by b.id asc";
	$sql_po_result_buyer = sql_select($sql_po_buyer);
	foreach ($sql_po_result_buyer as $val) 
	{
		$po_buyer_array[$val[csf('id')]]= $val[csf('buyer_name')];
	}
	
	 $sql_po="SELECT f.id as conv_id,a.buyer_name,b.id,b.job_no_mst,b.shipment_date,b.po_quantity,b.pub_shipment_date,b.grouping as ref_no,c.color_number_id as color_id,d.lib_yarn_count_deter_id as deter_id,f.cons_process,f.charge_unit,f.color_break_down from wo_po_break_down b,wo_po_details_master a,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_conv_cost_dtls f where  a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.id=f.job_id and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.id=f.fabric_description  and e.cons !=0   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and f.cons_process not in(1,30,35) and d.status_active=1  and e.is_deleted=0 and e.status_active=1 and f.is_deleted=0 and f.status_active=1 $po_id_cond order by f.id asc";
	$sql_po_result = sql_select($sql_po);
	foreach ($sql_po_result as $val) 
	{
		$color_break_down=$val[csf('color_break_down')];
		$po_qty_array[$val[csf('id')]] = $val[csf('po_quantity')];
		$po_job_array[$val[csf('id')]]= $val[csf('job_no_mst')];
		//$po_buyer_array[$val[csf('id')]]= $val[csf('buyer_name')];
		if($val[csf('cons_process')]==31 && $color_break_down!='')
		{
			$po_color_fab_array[$val[csf('id')]][$val[csf('color_id')]][$val[csf('deter_id')]][$val[csf('cons_process')]]['color_break_down'] = $color_break_down;

			$arr_1=explode("__",$color_break_down);
			for($ci=0;$ci<count($arr_1);$ci++)
			{
			$arr_2=explode("_",$arr_1[$ci]);
			//$this->_rateArray[$id][$arr_2[0]][$arr_2[3]]=$arr_2[1];
				if($arr_2[1]>0)
				{
				$po_color_fab_array[$val[csf('id')]][$arr_2[3]][$val[csf('deter_id')]][$val[csf('cons_process')]]['rate'] =$arr_2[1];
				$po_color_fabricDying_array[$val[csf('id')]][$arr_2[3]][$val[csf('cons_process')]]['rate'] =$arr_2[1];
				}
			}
		}
		else
		{
			$po_color_fab_array[$val[csf('id')]][$val[csf('color_id')]][$val[csf('deter_id')]][$val[csf('cons_process')]]['rate'] = $val[csf('charge_unit')];
			$po_color_only_fab_array[$val[csf('id')]][$val[csf('cons_process')]]['rate'] = $val[csf('charge_unit')];
		}
		
	}
	$process_arr_chk=array(1,30,35);$dyeing_prod_self_amount=$dyeing_prod_self_qty=0;
	foreach($nameArray as $row)
    {
		//$batch_wise_process_arr[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('id')]][$row[csf('item_description')]]['batch_qty']+=$row[csf('batch_qty')];
		if($self_po_id=="") $self_po_id=$row[csf('po_id')]; else $self_po_id.=",".$row[csf('po_id')];
		$prod_date_arr[$row[csf('prod_date')]]=$row[csf('prod_date')];
		$dtls_id=$row[csf('dtls_id')];
		$process_idArr=array_unique(explode(",",$row[csf('process_id')]));
		$po_buyer=$po_buyer_array[$row[csf('po_id')]];
		//print_r($process_idArr);
		$tot_amt=0;$dyeing_prod_self_amount_hnm_next=$dyeing_prod_self_qty_hnm_next=0;
		foreach ($process_idArr as $key => $key_id) //conversion_cost_head_array
		{
			$color_break_down=$po_color_fab_array[$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('deter_id')]][$key_id]['color_break_down'];
				//$conv_rate=0;
				if($key_id==31)
				{
					/*$arr_1=explode("__",$color_break_down);
					for($ci=0;$ci<count($arr_1);$ci++)
					{
					$arr_2=explode("_",$arr_1[$ci]);
					}*/
					$conv_rate=$po_color_fab_array[$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('deter_id')]][$key_id]['rate'];
					$fab_conv_rate=$po_color_fabricDying_array[$row[csf('po_id')]][$row[csf('color_id')]][$key_id]['rate'];
					//echo $row[csf('prod_date')].'='.$fab_conv_rate.'='.$row[csf('po_id')].'<br>';
					if($conv_rate==0 || $conv_rate=='') $conv_rate=$fab_conv_rate;
				}
				
				if($row[csf('entry_form')]==35 && $conv_rate>0)
				{
					if(!in_array($row[csf('batch_against')],$batch_against_chk))
					{
					$tot_amt+=$row[csf('batch_qty')]*$conv_rate;
					}
				}
		}
	
			if(!in_array($row[csf('batch_against')],$batch_against_chk))
			{
				if($po_buyer==1 || $po_buyer==10) // HnM and Next
				{
					$dyeing_prod_self_amount_hnm_next+=$tot_amt;
					$dyeing_prod_self_qty_hnm_next+=$row[csf('batch_qty')];
				}
				else
				{
					$dyeing_prod_self_amount+=$tot_amt;
					$dyeing_prod_self_qty+=$row[csf('batch_qty')];
					$dying_buyer_otherArr[$buyer_short_library[$po_buyer]]=$buyer_short_library[$po_buyer];
					$dying_buyer_id_otherArr[$po_buyer]=$po_buyer;
				}
				
			}
		
      }
 		// ============Dyeing End==================
		 //==============================Specila Finish here*****====================
		 $special_finish_prod_amt=$special_finish_prod_qty=0;
		foreach($nameArray_special as $row)// for Finish Production 
		{
				$sfin_fab_conv_rate=$po_color_only_fab_array[$row[csf('po_id')]][$row[csf('process_id')]]['rate'];
				$special_fin_amt=$row[csf('batch_qty')]*$sfin_fab_conv_rate;
				$po_buyer=$po_buyer_array[$row[csf('po_id')]];
				if($special_fin_amt>0)
				{
					$dtls_id=$row[csf('dtls_id')];
					if($dtls_chk_arr[$dtls_id]=="")
					{
						if($po_buyer==1 || $po_buyer==10) // HnM and Next
						{
							$special_finish_prod_amt+=$special_fin_amt;
							$special_finish_prod_qty+=$row[csf('batch_qty')];	
						}
						else
						{
							$others_special_finish_prod_amt+=$special_fin_amt;
							$others_special_finish_prod_qty+=$row[csf('batch_qty')];	
							$special_buyer_otherArr[$buyer_short_library[$po_buyer]]=$buyer_short_library[$po_buyer];
							$special_buyer_id_otherArr[$po_buyer]=$po_buyer;
						}
					//$prod_date_qty_arr[$row[csf('prod_date')]]['special_finish_qty']+=$row[csf('batch_qty')];
					$dtls_chk_arr[$dtls_id]=$dtls_id;
					}
					 
				}
		}
		unset($nameArray_special);
		
		///////===========Sub Con ========================
		  $sql_data_sub="SELECT a.id,a.batch_no,a.floor_id,c.entry_form,a.batch_against,(b.batch_qnty) as batch_qty, c.process_end_date as prod_date,a.batch_weight, a.working_company_id,b.prod_id,b.po_id,b.width_dia_type,a.color_id,a.booking_no,a.extention_no, b.item_description, a.booking_without_order 
		from pro_batch_create_mst a, pro_batch_create_dtls b,pro_fab_subprocess c 
		where  a.id=b.mst_id  and c.batch_id=a.id and c.batch_id=b.mst_id and c.entry_form in(38)   and a.is_sales=0 and c.load_unload_id=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.po_id>0 $cbo_company_cond $dyeing_prod_date 
		order by a.id";		
	//echo $sql_data;
		$sub_nameArray=sql_select($sql_data_sub);
		foreach($sub_nameArray as $row)
		{
			$sub_po_id_array[$row[csf('po_id')]] = $row[csf('po_id')];
			$sub_batch_id_array[$row[csf('id')]] = $row[csf('id')];
			$prod_id_id_array[$row[csf('prod_id')]] = $row[csf('prod_id')];
			//$all_batch_id_array[$row[csf('id')]] = $row[csf('id')];
			
		}
	  $sub_po_id_cond=where_con_using_array($sub_po_id_array,0,'b.id');
	   $all_prod_id_cond=where_con_using_array($prod_id_id_array,0,'b.prod_id');
	//print_r($sub_po_id_array);
	 $sql_subcon="SELECT a.currency_id,b.main_process_id,b.id,b.rate from subcon_ord_dtls b,subcon_ord_mst a where a.subcon_job=b.job_no_mst and b.status_active=1 and b.is_deleted=0 and b.rate>0 and b.main_process_id in(4,3) $sub_po_id_cond  ";
	$sql_subcon_res=sql_select($sql_subcon);
	$exchange_rate=80;
	foreach($sql_subcon_res as $row)
    {
		$sub_order_wise_arr[$row[csf('id')]]['process']  = $row[csf('main_process_id')];
		//$sub_order_wise_arr[$row[csf('id')]]['currency_id']  = $row[csf('currency_id')];
		if($row[csf('currency_id')]==1) //TK
		{ 
			$sub_order_wise_arr[$row[csf('id')]]['rate'] = $row[csf('rate')]/$exchange_rate;
		}
		else
		{
			$sub_order_wise_arr[$row[csf('id')]]['rate'] = $row[csf('rate')];
		}
	}
	//--------Subcon----
	$batch_against_chk2=array(2);
	$subcon_prod_amount=$subcon_prod_qty=0;
	foreach($sub_nameArray as $row)
    {
			if(!in_array($row[csf('batch_against')],$batch_against_chk2))
			{
			$sub_rate=$sub_order_wise_arr[$row[csf('po_id')]]['rate'];
		 
			$subcon_prod_qty+=$row[csf('batch_qty')];
			$subcon_prod_amount+=$row[csf('batch_qty')]*$sub_rate;
			$all_sub_batch_id_array[$row[csf('id')]]=$row[csf('id')];
			}
	}
	//==========Chemical and Wash Cost====================
	 $sql_dyes_cost =sql_select("select a.batch_no,a.buyer_id,b.item_category,(b.cons_amount) as dyes_chemical_cost
    from inv_issue_master a, inv_transaction b
    where a.id=b.mst_id and b.transaction_type=2  and  a.issue_purpose not in(13)  and a.entry_form=5 and a.batch_no  is not null  and   b.item_category in (5,6,7) $cbo_company_cond "); 
	 

	$dyes_chemical_arr=array();
	$mm_chk=array();$mm=1;$others_tot_chemical_cost=$tot_chemical_cost=$subcon_total_chemical_cost=0;
	foreach($sql_dyes_cost as $val)
	{
		$batchArr=array_unique(explode(",",$val[csf("batch_no")]));
		$buyer_id=$val[csf("buyer_id")];
		foreach($batchArr as $bid)
		{
			$all_batch_id=$all_batch_id_array[$bid];//Dyeing only
			$all_sub_batch_id=$all_sub_batch_id_array[$bid];//Subcon Dyeing only
			if($all_batch_id==$bid)
			{
				if($buyer_id==1 || $buyer_id==10) // HnM and Next
				{
				 	 $tot_chemical_cost+=$val[csf("dyes_chemical_cost")]/$exchange_rate;
				}
				else
				{
					$others_tot_chemical_cost+=$val[csf("dyes_chemical_cost")]/$exchange_rate;
				}
				//$subcon_total_chemical_cost+=$val[csf("dyes_chemical_cost")]/$exchange_rate;
			}
			if($all_sub_batch_id==$bid)
			{
				
				$subcon_total_chemical_cost+=$val[csf("dyes_chemical_cost")]/$exchange_rate;
			}
		}
	}
//echo $tot_chemical_cost.'='.$others_tot_chemical_cost;
	$sql_wash_dyes_cost =sql_select("select a.batch_no,a.issue_date,b.item_category,(b.cons_amount) as dyes_chemical_cost
    from inv_issue_master a, inv_transaction b
    where a.id=b.mst_id and b.transaction_type=2  and a.issue_purpose in(13)  and a.entry_form=5 and (a.batch_no  is null or a.batch_no=0)  and   b.item_category in (5,6,7) $cbo_company_cond $issue_dateCond"); 
	/*echo "select a.batch_no,a.issue_date,b.item_category,(b.cons_amount) as dyes_chemical_cost
    from inv_issue_master a, inv_transaction b
    where a.id=b.mst_id and b.transaction_type=2   and a.issue_purpose in(13)  and a.entry_form=5 and (a.batch_no  is null or a.batch_no=0)  and   b.item_category in (5,6,7) $cbo_company_cond $issue_dateCond";*/
	 
	 
	$tot_wash_dyes_chemical_cost=0;
	foreach($sql_wash_dyes_cost as $row)
	{
		/*$batchArr=array_unique(explode(",",$val[csf("batch_no")]));
		foreach($batchArr as $bid)
		{
		$dyes_chemical_arr[$bid]['chemical_cost']+=$val[csf("dyes_chemical_cost")]/$exchange_rate;
		
		}*/
		//echo $val[csf("dyes_chemical_cost")].'DD';
		//$prod_date_arr[$row[csf('issue_date')]]=$row[csf('issue_date')];
		$tot_wash_dyes_chemical_cost+=$row[csf("dyes_chemical_cost")]/$exchange_rate;
	}
	unset($sql_wash_dyes_cost);
	
	// ===============Knitting==================
		//$conversion_rate=return_field_value("conversion_rate","currency_conversion_rate a","a.is_deleted=0 and a.status_active=1  and a.id=(select max(a.id) from currency_conversion_rate a where a.currency=2 and a.is_deleted=0 and a.status_active=1 $conversion_company_cond $conversion_company_cond2 )","",$con);
		 $sql_conv_rate="select id,company_id,conversion_rate from currency_conversion_rate a where a.is_deleted=0 and a.status_active=1 $cbo_company_cond3 order by id desc" ;
		$sql_rate_result=sql_select($sql_conv_rate);
		foreach($sql_rate_result as $row)
		{
			$conversion_rateArr[$row[csf('company_id')]]=$row[csf('conversion_rate')];
		}
		$fabricData = sql_select("select fabric_roll_level from variable_settings_production where company_name in($cbo_company) and variable_list=3 and item_category_id=13 and is_deleted=0 and status_active=1");
		foreach ($fabricData as $row)
		{
			$roll_maintained_yesNo = $row[csf('fabric_roll_level')];
		}
		//echo $roll_maintained_yesNo;die;
		
   /* $sql_inhouse="SELECT  a.company_id,a.knitting_company, a.receive_date,a.buyer_id, b.febric_description_id, c.po_breakdown_id,f.id as job_id,f.job_no,
	 g.qnty as qnty_all,
	 (case when a.knitting_source in(1) and b.machine_no_id>0 then g.qnty else 0 end ) as qnty
	 from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c, lib_machine_name d, wo_po_break_down e,  wo_po_details_master f,pro_roll_details g
							where c.po_breakdown_id=e.id  and a.id=b.mst_id  and b.id=c.dtls_id and e.job_no_mst=f.job_no and b.id=g.dtls_id and c.dtls_id=g.dtls_id and c.po_breakdown_id=g.po_breakdown_id and e.id=g.po_breakdown_id  and c.is_sales=0 and a.entry_form=2   and a.item_category=13 and g.entry_form=2 and c.entry_form=2 and c.trans_type=1   and b.machine_no_id=d.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and g.status_active=1  and a.receive_basis!=4 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $cbo_company_cond $kniting_prod_date";
		$sql_inhouse_data=sql_select($sql_inhouse);*/
		if($roll_maintained_yesNo==1)
		{
		$sql_inhouse="SELECT  a.company_id,a.knitting_company,a.knitting_source, a.receive_date,a.buyer_id, b.febric_description_id, c.po_breakdown_id,f.id as job_id,f.job_no,
	 g.qnty as qnty_all,
	 (case when a.knitting_source in(1,3) and b.machine_no_id>0 then g.qnty else 0 end ) as qnty,
	 (case when a.knitting_source=3  then g.qnty else 0 end ) as out_qnty
	 from inv_receive_master a, pro_grey_prod_entry_dtls b left join lib_machine_name d on b.machine_no_id=d.id, order_wise_pro_details c, wo_po_break_down e,  wo_po_details_master f,pro_roll_details g
							where a.id=b.mst_id  and c.po_breakdown_id=e.id  and  b.id=c.dtls_id and e.job_no_mst=f.job_no and b.id=g.dtls_id and c.dtls_id=g.dtls_id and c.po_breakdown_id=g.po_breakdown_id and e.id=g.po_breakdown_id  and c.is_sales=0 and a.entry_form=2   and a.item_category=13 and g.entry_form=2 and c.entry_form=2 and c.trans_type=1   and a.status_active=1 and a.is_deleted=0 and b.status_active=1  and g.status_active=1  and a.receive_basis!=4 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $knit_company_cond $kniting_prod_date"; 
		}
		else
		{
			$sql_inhouse="SELECT  a.company_id,a.knitting_company,a.knitting_source, a.receive_date,a.buyer_id, b.febric_description_id, c.po_breakdown_id,f.id as job_id,f.job_no,
	 
	 (case when a.knitting_source in(1,3) and b.machine_no_id>0 then c.quantity else 0 end ) as qnty,
	 (case when a.knitting_source=3  then c.quantity else 0 end ) as out_qnty
	 from inv_receive_master a, pro_grey_prod_entry_dtls b left join lib_machine_name d on b.machine_no_id=d.id, order_wise_pro_details c, wo_po_break_down e,  wo_po_details_master f
							where a.id=b.mst_id  and c.po_breakdown_id=e.id  and  b.id=c.dtls_id and e.job_no_mst=f.job_no  and a.entry_form=2  and a.item_category=13   and c.entry_form=2 and c.trans_type=1   and a.status_active=1 and a.is_deleted=0 and b.status_active=1   and a.receive_basis!=4 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $knit_company_cond $kniting_prod_date"; 
		}
		$sql_inhouse_data=sql_select($sql_inhouse);
		foreach ($sql_inhouse_data as $row) 
		{
			$knit_job_id_array[$row[csf('job_id')]] = $row[csf('job_id')];
			
		}
		
		$knit_job_id_cond=where_con_using_array($knit_job_id_array,0,'b.job_id');				
	  $pre_rate_sql="SELECT b.id as conv_id,b.job_no, b.fabric_description, b.charge_unit, c.lib_yarn_count_deter_id
		from wo_po_details_master a, wo_pre_cost_fab_conv_cost_dtls b, wo_pre_cost_fabric_cost_dtls c
		where a.job_no=b.job_no and b.job_no=c.job_no and b.fabric_description=c.id and b.cons_process=1  $knit_job_id_cond order by b.id asc";
		$pre_rate_data=sql_select($pre_rate_sql);
		$rate_arr=array();
		foreach ($pre_rate_data as $row) 
		{
			$rate_arr[$row[csf('job_no')]][$row[csf('lib_yarn_count_deter_id')]]=$row[csf('charge_unit')];
		}
		$knitting_prodAmt=$knitting_prodQty=0;
		foreach ($sql_inhouse_data as $row) 
		{
			
			$rate_pre=$rate_arr[$row[csf('job_no')]][$row[csf('febric_description_id')]];
			$rate_in_usd=$rate_pre;
			//echo $rate_in_tk.'D';
				if($row[csf('buyer_id')]==1 || $row[csf('buyer_id')]==10)
				{
					$knitting_prodQty+=$row[csf('qnty')];
					if($rate_in_usd)
					{
					$knitting_prodAmt+=$row[csf('qnty')]*$rate_in_usd;
					}
					$buyer_knit_next[$buyer_short_library[$row[csf('buyer_id')]]]=$buyer_short_library[$row[csf('buyer_id')]];
				}
				else
				{
					$others_knitting_prodQty+=$row[csf('qnty')];
					if($rate_in_usd)
					{
					$others_knitting_prodAmt+=$row[csf('qnty')]*$rate_in_usd;
					}
					$others_buyer_knit[$buyer_short_library[$row[csf('buyer_id')]]]=$buyer_short_library[$row[csf('buyer_id')]];
				}
		}
		//echo $others_knitting_prodQty;
		
		 // ============================Print/Embrodiory-==========================
		 $print_embro_data_array="SELECT  a.po_number, a.po_received_date, a.pub_shipment_date, a.shipment_date, a.po_quantity, a.unit_price,a.job_id, a.po_total_price,a.id,a.job_no_mst as job_no,c.item_number_id,b.buyer_name,c.production_quantity,
		   (case when c.embel_name=1 then c.production_quantity else 0 end) as p_qnty,
		    (case when c.embel_name=2 then c.production_quantity else 0 end) as e_qnty,
		  c.po_break_down_id,b.company_name,c.embel_name,c.embel_type,c.production_type,d.emb_type,d.rate,d.emb_name,e.costing_per from wo_po_break_down a,wo_po_details_master b,pro_garments_production_mst c,wo_pre_cost_embe_cost_dtls d,wo_pre_cost_mst e where a.job_id=b.id and a.job_id=d.job_id and a.job_id=e.job_id and  c.po_break_down_id=a.id and d.emb_name in (1,2) and c.embel_name in (1,2) and c.production_source=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 $cbo_company_cond2 $buyerCond  $production_date order by a.id ASC";
		 $print_embro_data_result=sql_select($print_embro_data_array);
		 foreach($print_embro_data_result as $row)
		 {
			 $po_id_print_arr[$row[csf('id')]]=$row[csf('id')];
			 $job_id_print_arr[$row[csf('job_id')]]=$row[csf('job_id')];
		 }
		 $print_po_id_cond=where_con_using_array($po_id_print_arr,0,'b.order_id');
		 $print_job_id_cond=where_con_using_array($job_id_print_arr,0,'a.job_id');	
		 
		 $p_rate_data=sql_select("SELECT  a.id,a.emb_name,a.emb_type,a.job_no,a.cons_dzn_gmts, a.rate, a.amount,b.costing_per from wo_pre_cost_embe_cost_dtls a,wo_pre_cost_mst b where  a.emb_name in(1,2)  and  a.job_no=b.job_no $job_cond2 and a.status_active=1 and a.is_deleted=0 $print_job_id_cond  order by a.id");
	$rate_cal=1;
 
	$print_rate_arr=array();
	foreach($p_rate_data as $row){

				if($row[csf("costing_per")]=1){//For 1 Dzn
					$rate_cal=12;
				}elseif($row[csf("costing_per")]=2){//For 1 Pcs
					$rate_cal=1;
				}elseif($row[csf("costing_per")]=3){//For 2 Dzn
					$rate_cal=24;
				}elseif($row[csf("costing_per")]=4){//For 3 Dzn
					$rate_cal=36;
				}elseif($row[csf("costing_per")]=5){//For 4 Dzn
					$rate_cal=48;
				}

		
		// $print_rate_arr[$row[csf("emb_name")]]=$row[csf("rate")]/$rate_cal;
		if($row[csf("emb_name")]==1)
		{
		$print_rate_arr[$row[csf("job_no")]][$row[csf("emb_type")]]=$row[csf("rate")]/$rate_cal;
		$print_rate_id[$row[csf("job_no")]]=$row[csf("id")];
		}
		else if($row[csf("emb_name")]==2)
		{
		$embl_rate_arr[$row[csf("job_no")]][$row[csf("emb_type")]]=$row[csf("rate")]/$rate_cal;
		$embl_rate_id[$row[csf("job_no")]]=$row[csf("id")];
		}

		//  $print_rate_arr[$row[csf("job_no")]]=$row[csf("job_no")];

	
}
//print_r($print_rate_arr);
		 
		 $issue_print_data=sql_select("SELECT a.id,a.company_id,a.issue_number, b.id as bid, b.cons_quantity, b.prod_id,c.item_category_id, c.item_group_id, b.order_id,c.avg_rate_per_unit
			from inv_issue_master a, inv_transaction b, product_details_master c where   b.prod_id=c.id and  c.item_category_id=22 and  a.entry_form=21 and a.id=b.mst_id $issue_date_cond and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $issue_dateCond $print_po_id_cond");
			 
		foreach($issue_print_data as $row){
			$conversion_rate=$conversion_rateArr[$row[csf('company_id')]];
			
				$issue_print_qty_arr[$row[csf("order_id")]]=$row[csf("cons_quantity")];
				//echo ($row[csf("cons_quantity")]*$row[csf("avg_rate_per_unit")])/$conversion_rate.', ';
				$issue_p_rate_arr[$row[csf("order_id")]]=($row[csf("cons_quantity")]*$row[csf("avg_rate_per_unit")])/$conversion_rate;
				$iss_print_issue_num_arr[$row[csf("order_id")]]=$row[csf("bid")];
		}
		//print_r($issue_p_rate_arr);
			 



		$issue_qty=sql_select("SELECT a.id,a.issue_number,b.id as bid, c.item_description, c.item_category_id, c.item_group_id,b.item_return_qty, b.cons_quantity, b.cons_uom, b.order_id,c.avg_rate_per_unit from inv_issue_master a,inv_transaction b, product_details_master c where b.prod_id=c.id and b.transaction_type=2 and b.item_category=57 and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $issue_dateCond");
		foreach($issue_qty as $row){

			
				$issue_embl_qty_arr[$row[csf("order_id")]]=$row[csf("cons_quantity")];		
				$issue_e_rate_arr[$row[csf("order_id")]] +=($row[csf("cons_quantity")]*$row[csf("avg_rate_per_unit")])/$conversion_rate;			
				$issue_e_oder_id_arr[$row[csf("order_id")]]=$row[csf("prod_id")];
				$iss_eble_issue_num_arr[$row[csf("order_id")]]=$row[csf("bid")];
				//  $issue_num_arr[$row[csf("order_id")]]+=$row[csf("cons_quantity")];
				//  $issue_num_arr[$row[csf("order_id")]]+=$row[csf("cons_quantity")];
		}
		//print_r($issue_e_rate_arr);
		
	///============================print qty========================


	$print_qty_data=sql_select("SELECT id, po_break_down_id,embel_name,production_source, item_number_id, country_id, production_date, production_quantity, reject_qnty, production_source, serving_company, embel_name,embel_type from pro_garments_production_mst where   embel_name in (1,2) and production_type='3'and production_source=1 and status_active=1 and is_deleted=0 $production_date2 order by id");

	foreach($print_qty_data as $row){
				if($row[csf("embel_name")]==1){
					$print_qty[$row[csf("po_break_down_id")]][$row[csf("embel_type")]]  +=$row[csf("production_quantity")];
				}else{
					$emb_qty[$row[csf("po_break_down_id")]][$row[csf("embel_type")]] +=$row[csf("production_quantity")];
				}
	}

	//  echo "<pre>";
	//  print_r($emb_qty);
	

	 $data_array=sql_select("SELECT  a.po_number, a.po_received_date, a.pub_shipment_date, a.shipment_date, a.po_quantity, a.unit_price, a.po_total_price,a.id,a.job_no_mst,c.item_number_id,b.buyer_name,c.po_break_down_id,b.company_name,c.embel_name,c.embel_type,d.emb_type,d.emb_name,c.production_quantity,c.production_type from wo_po_break_down a,wo_po_details_master b,pro_garments_production_mst c,wo_pre_cost_embe_cost_dtls d where a.JOB_NO_MST=b.job_no and a.JOB_NO_MST=d.job_no and  c.po_break_down_id=a.id and d.emb_name in (1,2) and c.embel_name in (1,2) and c.production_source=1 and c.production_type='3' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  $cbo_company_cond2 $buyerCond and production_type='3' $production_date  order by a.id ASC");


	 $buyer_wise_qty_id=array();
	 foreach($data_array as $row ){

		$order_wise_data_arr[$row[csf("id")]][$row[csf("emb_type")]]['id']=$row[csf("id")];
		$order_wise_data_arr[$row[csf("id")]][$row[csf("emb_type")]]['buyer_name'] =$row[csf("buyer_name")];
		$order_wise_data_arr[$row[csf("id")]][$row[csf("emb_type")]]['job_no']=$row[csf("job_no_mst")];		
		if($row[csf("id")] !==$po_number ){	
			if (!in_array($row[csf('buyer_name')],$buyer_wise_qty_id))
				{ $z++;
					 $buyer_wise_qty_id[]=$row[csf('buyer_name')];					
					  $buyer_wise_issue_print_val[$row[csf("buyer_name")]]['issue_p_val'] +=$issue_p_rate_arr[$row[csf("id")]];
					  	
					  $buyer_wise_issue_emb_val[$row[csf("buyer_name")]]['issue_e_val'] +=$issue_e_rate_arr[$row[csf("id")]];	
					  $po_number=$row[csf("id")];			
				}
				else
				{
					 $buyer_wise_qty_id=0;
				}			
		
	}	
	$buyer_id=$row[csf("buyer_name")];
			
 }

	 foreach ($order_wise_data_arr as $id => $emb_data) 
	 {
		 foreach ($emb_data as $emb_data_id => $row) 
		 {
			$buyer_wise_print_qty[$row["buyer_name"]]['print_qty']+=$print_qty[$id][$emb_data_id];
			$buyer_wise_emb_qty[$row["buyer_name"]]['emb_qty']+=$emb_qty[$id][$emb_data_id];
			$buyer_wise_print_val[$row["buyer_name"]]['print_value'] +=($print_qty[$id][$emb_data_id]*$print_rate_arr[$row["job_no"]][$emb_data_id]);
			$buyer_wise_emb_val[$row["buyer_name"]]['embr_value'] +=($emb_qty[$id][$emb_data_id]*$embl_rate_arr[$row["job_no"]][$emb_data_id]);
			// $buyer_wises_print_qty[$row["buyer_name"]]['issue_p_val'] +=$issue_p_rate_arr[$id];
		 
		}
	}

		//    echo "<pre>";
	    // print_r($buyer_wise_issue_emb_val);	





		$print_value_hnm_next=$others_print_value_hnm_next=$print_qty_hnm_next=$others_print_qty_hnm_next=$others_embro_value_hnm_next=$embro_value_hnm_next=$others_embro_qty_hnm_next=$embro_qty_hnm_next=0;
		$buyer_wise_qty_arr=array();$z=0;$m=0;$buyer_check_array=array();
		foreach ($print_embro_data_result as $row) 
		{
			if($row[csf("costing_per")]=1){//For 1 Dzn
					$rate_cal=12;
				}elseif($row[csf("costing_per")]=2){//For 1 Pcs
					$rate_cal=1;
				}elseif($row[csf("costing_per")]=3){//For 2 Dzn
					$rate_cal=24;
				}elseif($row[csf("costing_per")]=4){//For 3 Dzn
					$rate_cal=36;
				}elseif($row[csf("costing_per")]=5){//For 4 Dzn
					$rate_cal=48;
				}
				$pre_rate=$row[csf("rate")]/$rate_cal;
				//echo $row[csf("emb_type")].'=='.$row[csf("production_quantity")].',';
				if($row[csf("emb_name")]==2 && $row[csf("production_type")]==3)//Embrodoiry
				{
				 if($row[csf('buyer_name')]==1 || $row[csf('buyer_name')]==10)
					{
					$embl_value=($row[csf("e_qnty")]*$embl_rate_arr[$row[csf("job_no")]][$row[csf("emb_type")]]);
					$issue_emb=$issue_e_rate_arr[$row[csf("po_break_down_id")]];
					// $embro_value_hnm_next+=$embl_value-$issue_emb;
					// $embro_qty_hnm_next+=$row[csf("e_qnty")];
					

					$buyer_no=$row[csf("buyer_name")];
					if (!in_array($buyer_no,$buyer_check_array))
						{ $z++;
							 $buyer_check_array[]=$row[csf("buyer_name")];
							 $embro_qty_hnm_next +=$buyer_wise_emb_qty[$row[csf("buyer_name")]]['emb_qty'];
							 $embro_value_hnm_next +=$buyer_wise_emb_val[$row[csf("buyer_name")]]['embr_value']-$buyer_wise_issue_emb_val[$row[csf("buyer_name")]]['issue_e_val'];
						}
				//	echo $embro_qty_hnm_next.'D';
					$buyer_embro_next[$buyer_short_library[$row[csf('buyer_name')]]]=$buyer_short_library[$row[csf('buyer_name')]];
					}
					else
					{
					$embl_value_o=($row[csf("e_qnty")]*$embl_rate_arr[$row[csf("job_no")]][$row[csf("emb_type")]]);
					$buyer_name=$row[csf('buyer_name')];
						if (!in_array($buyer_name,$buyer_wise_qty_arr))
						{ $z++;
							$buyer_wise_qty_arr[]=$buyer_name;	
							$issue_emb_o=$issue_e_rate_arr[$row[csf("po_break_down_id")]];
						}
						else $issue_emb_o=0;
					//echo $embl_value_o.'='.$issue_emb_o.'<br>';
					//if($embl_value_o)
					//{

						$buyer_no=$row[csf("buyer_name")];
					if (!in_array($buyer_no,$buyer_check_array))
						{ $z++;
							 $buyer_check_array[]=$row[csf("buyer_name")];
							 $others_embro_qty_hnm_next +=$buyer_wise_emb_qty[$row[csf("buyer_name")]]['emb_qty'];
							 $others_embro_value_hnm_next +=$buyer_wise_emb_val[$row[csf("buyer_name")]]['embr_value']-$buyer_wise_issue_emb_val[$row[csf("buyer_name")]]['issue_e_val'];
						}
					// $others_embro_value_hnm_next+=$embl_value_o-$issue_emb_o;
					// $others_embro_qty_hnm_next+=$row[csf("e_qnty")];
					//echo $row[csf("production_quantity")].'E';
					$others_buyer_embro_next[$buyer_short_library[$row[csf('buyer_name')]]]=$buyer_short_library[$row[csf('buyer_name')]];
					//}
					}
				}
				else if($row[csf("emb_name")]==1 && $row[csf("production_type")]==3)//Print
				{
				$emb_name=$row[csf("emb_name")];
				//echo $emb_name.'='.$row[csf("production_quantity")].',';
				
					if($row[csf('buyer_name')]==1 || $row[csf('buyer_name')]==10)
					{
					$print_value=($row[csf("p_qnty")]*$print_rate_arr[$row[csf("job_no")]][$row[csf("emb_type")]]);
					$issue_p=$issue_p_rate_arr[$row[csf("po_break_down_id")]];
					// $print_value_hnm_next+=$print_value-$issue_p;
					// $print_qty_hnm_next+=$row[csf("p_qnty")];

					$buyer_no=$row[csf("buyer_name")];
					if (!in_array($buyer_no,$buyer_emb_check_array))
						{ $z++;
							 $buyer_emb_check_array[]=$row[csf("buyer_name")];
							 $print_qty_hnm_next +=$buyer_wise_print_qty[$row[csf("buyer_name")]]['print_qty'];
						     $print_value_hnm_next +=$buyer_wise_print_val[$row[csf("buyer_name")]]['print_value']- $buyer_wise_issue_print_val[$row[csf("buyer_name")]]['issue_p_val'];
						}


					$buyer_print_next[$buyer_short_library[$row[csf('buyer_name')]]]=$buyer_short_library[$row[csf('buyer_name')]];
					}
					else //Others
					{
					$print_value_other=($row[csf("p_qnty")]*$print_rate_arr[$row[csf("job_no")]][$row[csf("emb_type")]]);
					$issue_p_o=$issue_p_rate_arr[$row[csf("po_break_down_id")]];
					
					// $others_print_value_hnm_next+=$print_value_other-$issue_p_o;
					//echo $print_value_other.'='.$issue_p_o.'<br>';
					// $others_print_qty_hnm_next+=$row[csf("p_qnty")];

					$buyer_no=$row[csf("buyer_name")];
					if (!in_array($buyer_no,$buyer_emb_other_check_array))
						{ $m++;
							 $buyer_emb_other_check_array[]=$row[csf("buyer_name")];
							 $others_print_qty_hnm_next +=$buyer_wise_print_qty[$row[csf("buyer_name")]]['print_qty'];
							 $others_print_value_hnm_next+=$buyer_wise_print_val[$row[csf("buyer_name")]]['print_value']- $buyer_wise_issue_print_val[$row[csf("buyer_name")]]['issue_p_val'];
						}

					$others_buyer_print_next[$buyer_short_library[$row[csf('buyer_name')]]]=$buyer_short_library[$row[csf('buyer_name')]];
					}
				}
			
			
			
		}
		
	
	} //Division All  End
	
	//echo $special_finish_prod_amt.'='.$dyeing_prod_self_amount.'='.$subcon_prod_amount;die;
			
	
		$table_width=800;
		ob_start();	
		//$table_width=90+($datediff*160);
	?>
		<div id="scroll_body" align="center" style="height:auto; width:auto; margin:0 auto; padding:0;">
			<table width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="caption" align="center">
				<tr>
				   <td align="center" width="100%" colspan="<? echo $count_hd; ?>" class="form_caption" ><strong style="font-size:18px">
				   <? 
				   if ($cbo_company!=0){echo ' Company Name:' .$company_library[$cbo_company];} else {echo '';}
				   ?>
					</strong>
				   </td>
				</tr> 
				<tr>  
				   <td align="center" width="100%" colspan="<? echo $count_hd; ?>" class="form_caption" ><strong style="font-size:14px"><? echo $report_title; ?></strong></td>
				</tr>
				<tr>  
				   <td align="center" width="100%" colspan="<? echo $count_hd; ?>" class="form_caption" ><strong style="font-size:14px"> <? echo "From : ".change_date_format(str_replace("'","",$txt_date_from))." To : ".change_date_format(str_replace("'","",$txt_date_to))."" ;?></strong></td>
				</tr>  
			</table>
			<?
			
			//$floor_arr = return_library_array("select id,floor_name from  lib_prod_floor ","id","floor_name");
			?>
			<div align="center" style="height:auto;">
			<table width="<? echo $table_width; ?> " border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all"> 
           		 
				<thead>
					
                    <tr>
						<th style="word-break: break-all;word-wrap: break-word;" width="250">Production Type</th>
                        <th style="word-break: break-all;word-wrap: break-word;" width="100">Production(Pcs)</th>
                        <th style="word-break: break-all;word-wrap: break-word;" width="100">Net Income</th>
						
					</tr>
                  </thead>
                  <tbody>
                  <?
                  if($division_id==0 || $search_typeCond==1)
				  {
					   if($sewing_prod_hnm_nextNetVal) $sewing_prod_hnm_nextNetVal=$sewing_prod_hnm_nextNetVal;else $sewing_prod_hnm_nextNetVal=0;
					   if($others_sewing_prod_hnm_nextNetVal) $others_sewing_prod_hnm_nextNetVal=$others_sewing_prod_hnm_nextNetVal;else $others_sewing_prod_hnm_nextNetVal=0;
                   $total_sewingQty=$sewing_prod_hnm_nextQty+$others_sewing_prod_hnm_nextQty;
				   $total_sewingVal=$sewing_prod_hnm_nextNetVal+$others_sewing_prod_hnm_nextNetVal;
				   
				  ?>
                  <tr>
                    <td style="word-break: break-all; background: #9C9" colspan="3" align="center" width=""><b>Garments Division -$<? echo number_format($total_sewingVal,2);?></b></td>
                    </tr>
                    <?
                     $bgcolor="#E9F3FF"; 
					 $bgcolor2="#FFFFFF";
					?>
				    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd1','<? echo $bgcolor; ?>')" id="tr_1nd1">
					<td width="250"  style="word-break: break-all;word-wrap: break-word;"> Garments Sewing Output(<? echo implode(",",$buyer_hnmNext);?>)</td>
                    <td width="100" align="right"  style="word-break: break-all;word-wrap: break-word;"><? echo number_format($sewing_prod_hnm_nextQty,0);?></td>
                    <td width="100"  align="right"style="word-break: break-all;word-wrap: break-word;"><? echo number_format($sewing_prod_hnm_nextNetVal,2);?></td>
				   </tr>
                  <tr bgcolor="<? echo $bgcolor2; ?>" onclick="change_color('tr_1nd2','<? echo $bgcolor2; ?>')" id="tr_1nd2">
					<td width="250"  style="word-break: break-all;word-wrap: break-word;"> Garments Sewing Output Others Buyer(<? echo implode(",",$others_buyer_hnmNext);?>)</td>
                    <td width="100"  align="right" style="word-break: break-all;word-wrap: break-word;"><? echo number_format($others_sewing_prod_hnm_nextQty,0);?></td>
                    <td width="100" align="right"  style="word-break: break-all;word-wrap: break-word;"><?  if($others_sewing_prod_hnm_nextNetVal) echo number_format($others_sewing_prod_hnm_nextNetVal,2);else echo "0";?></td>
				   </tr>
                  
                   <tr>
                    <td style="word-break: break-all;" align="right" width="250"><b>Total:</b></td>
                    <td style="word-break: break-all;" align="right" width="100"><b><? echo number_format($total_sewingQty,0);?></b></td>
                    <td style="word-break: break-all;" align="right" width="100"><b><? if($total_sewingVal) echo number_format($total_sewingVal,2);else echo "0";?></b></td>
                    </tr>
                    <?
				  }
				  if($division_id==0 || $search_typeCond==2)
				  {
					  $dyeing_prod_self_amount_hnm_next=$dyeing_prod_self_amount_hnm_next-$tot_chemical_cost;
					  $dyeing_prod_self_amount=$dyeing_prod_self_amount-$others_tot_chemical_cost;
					  $subcon_prod_amount=$subcon_prod_amount-$subcon_total_chemical_cost;
					  //echo $others_tot_chemical_cost.'D';
					  
                   $total_dyeingQty=$dyeing_prod_self_qty+$dyeing_prod_self_qty_hnm_next+$subcon_prod_qty;
				   $total_dyeingVal=$dyeing_prod_self_amount+$dyeing_prod_self_amount_hnm_next+$subcon_prod_amount+$tot_wash_dyes_chemical_cost;
				   $total_dyeingsubFinVal=$dyeing_prod_self_amount+$dyeing_prod_self_amount_hnm_next+$subcon_prod_amount+$others_special_finish_prod_amt+$tot_wash_dyes_chemical_cost;
				    
				  ?>
					
                    
                    <tr>
                    <td style="word-break: break-all; background: #9C9" colspan="3" align="center" width=""><b>Dyeing Division-$<? echo number_format($total_dyeingsubFinVal,2);?></b></td>
                    </tr>
                    <?
                     $bgcolor="#E9F3FF"; 
					 $bgcolor2="#FFFFFF";//$special_finish_prod_amt.'='.$dyeing_prod_self_amount.'='.$subcon_prod_amount;
					 
					 $othersbuyerId=implode(",",$dying_buyer_id_otherArr);
					 $buyer_id='1,10';
					?>
				    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd3','<? echo $bgcolor; ?>')" id="tr_1nd3">
					<td width="250"  style="word-break: break-all;word-wrap: break-word;"> Dyeing:(HnM and Next)</td>
                    <td width="100" align="right"  style="word-break: break-all;word-wrap: break-word;"><? echo number_format($dyeing_prod_self_qty_hnm_next,0);?></td>
                    <td width="100"  align="right"style="word-break: break-all;word-wrap: break-word;"><a href="##"  onClick="fnc_dyeing_popup('<? echo $date_data_popup; ?>','<? echo $buyer_id; ?>','<? echo $cbo_company; ?>','dyeing_earn_inhouse_popup',1)"><? echo number_format($dyeing_prod_self_amount_hnm_next,2); ?></a><? //echo number_format($dyeing_prod_self_amount+$subcon_prod_amount,0);?></td>
				   </tr>
                    <tr bgcolor="<? echo $bgcolor2; ?>" onclick="change_color('tr_1ndDO','<? echo $bgcolor; ?>')" id="tr_1ndDO">
					<td width="250"  style="word-break: break-all;word-wrap: break-word;"> Dyeing Others Buyer:(<? echo implode(",",$dying_buyer_otherArr);?>)</td>
                    <td width="100" align="right"  style="word-break: break-all;word-wrap: break-word;"><? echo number_format($dyeing_prod_self_qty,0);?></td>
                    <td width="100"  align="right"style="word-break: break-all;word-wrap: break-word;"><a href="##"  onClick="fnc_dyeing_popup('<? echo $date_data_popup; ?>','<? echo $othersbuyerId; ?>','<? echo $cbo_company; ?>','dyeing_earn_inhouse_popup',2)"><? echo number_format($dyeing_prod_self_amount,2); ?></a><? //echo number_format($dyeing_prod_self_amount+$subcon_prod_amount,0);?></td>
				   </tr>
                   
                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1ndSUB','<? echo $bgcolor; ?>')" id="tr_1ndSUB">
					<td width="250"  style="word-break: break-all;word-wrap: break-word;"> SubContract:</td>
                    <td width="100" align="right"  style="word-break: break-all;word-wrap: break-word;"><? echo number_format($subcon_prod_qty,0);?></td>
                    <td width="100"  align="right"style="word-break: break-all;word-wrap: break-word;"><a href="##"  onClick="fnc_dyeing_popup('<? echo $date_data_popup; ?>','<? echo $buyer_id; ?>','<? echo $cbo_company; ?>','dyeing_earn_inhouse_popup',3)"><? echo number_format($subcon_prod_amount,2); ?></a><? //echo number_format($dyeing_prod_self_amount+$subcon_prod_amount,0);?></td>
				   </tr>
                   
                     <tr bgcolor="<? echo $bgcolor2; ?>" onclick="change_color('tr_1ndWash','<? echo $bgcolor; ?>')" id="tr_1ndWash">
					<td width="250"  style="word-break: break-all;word-wrap: break-word;">Cost(Machine Wash,Lab Test):</td>
                    <td width="100" align="right"  style="word-break: break-all;word-wrap: break-word;"><? //echo number_format($subcon_prod_qty,0);?></td>
                    <td width="100"  align="right"style="word-break: break-all;word-wrap: break-word;"><a href="##"  onClick="fnc_dyeing_popup('<? echo $date_data_popup; ?>','<? echo $buyer_id; ?>','<? echo $cbo_company; ?>','dyeing_earn_inhouse_popup',4)"><? //echo number_format($subcon_prod_amount,2); ?></a><? echo number_format($tot_wash_dyes_chemical_cost,0);?></td>
				   </tr>
                   
                     <tr>
                    <td style="word-break: break-all;" align="right" width="250"><b>Total:</b></td>
                    <td style="word-break: break-all;" align="right" width="100"><b><? echo number_format($total_dyeingQty,0);?></b></td>
                    <td style="word-break: break-all;" align="right" width="100"><b><? if($total_dyeingVal) echo number_format($total_dyeingVal,2);else echo "0";?></b></td>
                    </tr>
                  
							<? //$special_buyer_id_otherArr[$po_buyer]=$po_buyer;
							$othersspecial_buyerId=implode(",",$special_buyer_id_otherArr);
					 			$special_buyer_id='1,10';
							 ?>
                   
                  <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd4','<? echo $bgcolor2; ?>')" id="tr_1nd4">
					<td width="250"  style="word-break: break-all;word-wrap: break-word;"> Finishing:(HnM and Next):(Heatset,Stentering,Brush,Peach Finish):</td>
                    <td width="100"  align="right" style="word-break: break-all;word-wrap: break-word;"><? echo number_format($special_finish_prod_qty,0);?></td>
                    <td width="100" align="right"  style="word-break: break-all;word-wrap: break-word;"><a href="##"  onClick="fnc_dyeing_popup('<? echo $date_data_popup; ?>','<? echo $special_buyer_id; ?>','<? echo $cbo_company; ?>','finish_earn_inhouse_popup',1)"><? echo number_format($special_finish_prod_amt,2); ?></a><? //echo number_format($special_finish_prod_amt,0);?></td>
				   </tr>
                     <tr bgcolor="<? echo $bgcolor2; ?>" onclick="change_color('tr_1ndfin','<? echo $bgcolor2; ?>')" id="tr_1ndfin">
					<td width="250"  style="word-break: break-all;word-wrap: break-word;">Finishing Others Buyer(<? echo implode(",",$special_buyer_otherArr);?>):(Heatset,Stentering,Brush,Peach Finish):</td>
                    <td width="100"  align="right" style="word-break: break-all;word-wrap: break-word;"><? echo number_format($others_special_finish_prod_qty,0);?></td>
                    <td width="100" align="right"  style="word-break: break-all;word-wrap: break-word;"><a href="##"  onClick="fnc_dyeing_popup('<? echo $date_data_popup; ?>','<? echo $othersspecial_buyerId; ?>','<? echo $cbo_company; ?>','finish_earn_inhouse_popup',2)"><? echo number_format($others_special_finish_prod_amt,2); ?></a><? //echo number_format($special_finish_prod_amt,0);?></td>
				   </tr>
                   <?
                   $tot_special_finish_prod_amt=$special_finish_prod_amt+$others_special_finish_prod_amt;
				   $tot_special_finish_prod_qty=$special_finish_prod_qty+$others_special_finish_prod_qty;
				   ?>
                    <tr>
                    <td style="word-break: break-all;" align="right" width="250"><b>Total Finish:</b></td>
                    <td style="word-break: break-all;" align="right" width="100"><b><? echo number_format($tot_special_finish_prod_qty,0);?></b></td>
                    <td style="word-break: break-all;" align="right" width="100"><b><? if($tot_special_finish_prod_amt) echo number_format($tot_special_finish_prod_amt,2);else echo "0";?></b></td>
                    </tr>
                    <?
                   // $total_dyeingVal=$tot_special_finish_prod_amt;
					?>
                     <tr>
                    <td style="word-break: break-all;" align="right" width="250"><b>Total Dyeing+Total Finish:</b></td>
                    <td style="word-break: break-all;" align="right" width="100"><b><? echo number_format($total_dyeingQty+$tot_special_finish_prod_qty,0);?></b></td>
                    <td style="word-break: break-all;" align="right" width="100"><b><? if($tot_special_finish_prod_amt+$total_dyeingVal) echo number_format($tot_special_finish_prod_amt+$total_dyeingVal,2);else echo "0";?></b></td>
                    </tr>
                    <?
				  }
				  if($division_id==0 || $search_typeCond==3)
				  {
				    $total_knittingQty=$knitting_prodQty+$others_knitting_prodQty;
				   $total_knittingVal=$knitting_prodAmt+$others_knitting_prodAmt;
				  ?>
					 
                    <tr>
                    <td style="word-break: break-all; background: #9C9" colspan="3" align="center" width=""><b>Knitting Division-$<? echo number_format($total_knittingVal,2);?></b></td>
                    </tr>
                    <?
                     $bgcolor="#E9F3FF"; 
					 $bgcolor2="#FFFFFF";//$special_finish_prod_amt.'='.$dyeing_prod_self_amount.'='.$subcon_prod_amount;
					?>
				    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd5','<? echo $bgcolor; ?>')" id="tr_1nd5">
					<td width="250"  style="word-break: break-all;word-wrap: break-word;"> Inhouse Knitting(<? echo implode(",",$buyer_knit_next);?>):</td>
                    <td width="100" align="right"  style="word-break: break-all;word-wrap: break-word;"><? echo number_format($knitting_prodQty,0);?></td>
                    <td width="100"  align="right"style="word-break: break-all;word-wrap: break-word;"><? echo number_format($knitting_prodAmt,2);?></td>
				   </tr>
                  <tr bgcolor="<? echo $bgcolor2; ?>" onclick="change_color('tr_1nd6','<? echo $bgcolor2; ?>')" id="tr_1nd6">
					<td width="250"  style="word-break: break-all;word-wrap: break-word;"> Inhouse Knitting Others Buyer (<? echo implode(",",$others_buyer_knit);?>):</td>
                    <td width="100"  align="right" style="word-break: break-all;word-wrap: break-word;"><? echo number_format($others_knitting_prodQty,0);?></td>
                    <td width="100" align="right"  style="word-break: break-all;word-wrap: break-word;"><? echo number_format($others_knitting_prodAmt,2);?></td>
				   </tr>
                     <?
                 
				   ?>
                    <tr>
                    <td style="word-break: break-all;" align="right" width="250"><b>Total:</b></td>
                    <td style="word-break: break-all;" align="right" width="100"><b><? echo number_format($total_knittingQty,0);?></b></td>
                    <td style="word-break: break-all;" align="right" width="100"><b><? if($total_knittingVal) echo number_format($total_knittingVal,2);else echo "0";?></b></td>
                    </tr>
                    <?
				  }
				  if($division_id==0 || $search_typeCond==4)
				  {
					 $total_print_qty=$print_qty_hnm_next+$others_print_qty_hnm_next;
					$total_print_value=$print_value_hnm_next+$others_print_value_hnm_next;
					
					
					
					$total_embro_qty=$embro_qty_hnm_next+$others_embro_qty_hnm_next;
					$total_embro_value=$others_embro_value_hnm_next+$embro_value_hnm_next;
					$total_print_embro_qty=$total_print_qty+$total_embro_qty;
					$total_print_embro_value=$total_print_value+$total_embro_value;
					?>
                   <tr>
                    <td style="word-break: break-all; background: #9C9" colspan="3" align="center" width=""><b>Printting/Embroidary Division-$<? echo number_format($total_print_embro_value,2);?></b></td>
                    </tr>
                    <?
                     $bgcolor="#E9F3FF"; 
					 $bgcolor2="#FFFFFF";//$special_finish_prod_amt.'='.$dyeing_prod_self_amount.'='.$subcon_prod_amount;
					?>
				    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd7','<? echo $bgcolor; ?>')" id="tr_1nd7">
					<td width="250"  style="word-break: break-all;word-wrap: break-word;"> Printing(<? echo implode(",",$buyer_print_next);?>):</td>
                    <td width="100" align="right"  style="word-break: break-all;word-wrap: break-word;"><? echo number_format($print_qty_hnm_next,0);?></td>
                    <td width="100"  align="right"style="word-break: break-all;word-wrap: break-word;"><? echo number_format($print_value_hnm_next,2);?></td>
				   </tr>
                  <tr bgcolor="<? echo $bgcolor2; ?>" onclick="change_color('tr_1nd8','<? echo $bgcolor2; ?>')" id="tr_1nd8">
					<td width="250"  style="word-break: break-all;word-wrap: break-word;"> Printing Others Buyer(<? echo implode(",",$others_buyer_print_next);?>):</td>
                    <td width="100"  align="right" style="word-break: break-all;word-wrap: break-word;"><? echo number_format($others_print_qty_hnm_next,0);?></td>
                    <td width="100" align="right"  style="word-break: break-all;word-wrap: break-word;"><? echo number_format($others_print_value_hnm_next,2);?></td>
				   </tr>
                    <?
                   
					?>
                    <tr>
                    <td style="word-break: break-all;" align="right" width="250"><b>Total:</b></td>
                    <td style="word-break: break-all;" align="right" width="100"><b><? echo number_format($total_print_qty,0);?></b></td>
                    <td style="word-break: break-all;" align="right" width="100"><b><? echo number_format($total_print_value,2);?></b></td>
                    </tr>
                    
                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_1nd9','<? echo $bgcolor; ?>')" id="tr_1nd9">
					<td width="250"  style="word-break: break-all;word-wrap: break-word;"> Embroidary(<? echo implode(",",$buyer_embro_next);?>):</td>
                    <td width="100" align="right"  style="word-break: break-all;word-wrap: break-word;"><? echo number_format($embro_qty_hnm_next,0);?></td>
                    <td width="100"  align="right"style="word-break: break-all;word-wrap: break-word;"><? echo number_format($embro_value_hnm_next,2);?></td>
				   </tr>
                  <tr bgcolor="<? echo $bgcolor2; ?>" onclick="change_color('tr_1nd10','<? echo $bgcolor2; ?>')" id="tr_1nd10">
					<td width="250"  style="word-break: break-all;word-wrap: break-word;"> Embroidary Others Buyer(<? echo implode(",",$others_buyer_embro_next);?>):</td>
                    <td width="100"  align="right" style="word-break: break-all;word-wrap: break-word;"><? echo number_format($others_embro_qty_hnm_next,0);?></td>
                    <td width="100" align="right"  style="word-break: break-all;word-wrap: break-word;"><? echo number_format($others_embro_value_hnm_next,2);?></td>
                     <?
					?>
                     <tr>
                    <td style="word-break: break-all;" align="right" width="250"><b>Total:</b></td>
                    <td style="word-break: break-all;" align="right" width="100"><b><? echo number_format($total_embro_qty,0);?></b></td>
                    <td style="word-break: break-all;" align="right" width="100"><b><? echo number_format($total_embro_value,2);?></b></td>
                    </tr>
                    <?
				  
					?>
                    <tr>
                    <td style="word-break: break-all;" align="right" width="250"><b>Total Printing+ Embroidary:</b></td>
                    <td style="word-break: break-all;" align="right" width="100"><b><? echo number_format($total_print_embro_qty,0);?></b></td>
                    <td style="word-break: break-all;" align="right" width="100"><b><? echo number_format($total_print_embro_value,2);?></b></td>
                    </tr>
				   </tr>
                   <?
	              }
				   ?>
                   </tbody>
                   <tfoot>
                   <?
                 //  $total_net_income_qty=$sewing_prod_hnm_nextNetVal+$others_sewing_prod_hnm_nextNetVal+$dyeing_prodAmt;
				    $total_net_income_amt=$total_sewingVal+$total_dyeingsubFinVal+$total_knittingVal+$total_print_embro_value;
				   ?>
                     <tr>
					<th width="250"  style="word-break: break-all;word-wrap: break-word;">Total Net Income($)</th>
                    <th width="100"   style="word-break: break-all;word-wrap: break-word;"><? //echo number_format($total_net_income_qty,0);?></th>
                    <th width="100"  style="word-break: break-all;word-wrap: break-word;"><? echo number_format($total_net_income_amt,0);?></th>
				   </tr>
                   </tfoot>
			</table>
			</div>
			
            
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
	
}
if ($action=="dyeing_earn_inhouse_popup")  // All Production Data popup dyeing_earn_inboundSub_popup
{
	
	extract($_REQUEST);
 	echo load_html_head_contents("Dyeing inhouse and Subcon poppup Info","../../../", 1, 1, $unicode,'','');
	
	$date_key=str_replace("'","",$date_key);
	$date_Arr=explode("_",$date_key);
	$from_date=$date_Arr[0];
	$to_date=$date_Arr[1];
	$buyer_id=str_replace("'","",$buyer_id);
	$company_id=str_replace("'","",$company_id);
	$type_id=str_replace("'","",$type);
	//echo $date_key.'dd';die;
	//echo $type_id.'sds';
	 if($db_type==0)
			{
				$start_date=change_date_format(str_replace("'","",$to_date),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$from_date),"yyyy-mm-dd","");
			}
			else if($db_type==2)
			{
				$start_date=change_date_format(str_replace("'","",$from_date),"","",1);
				$end_date=change_date_format(str_replace("'","",$to_date),"","",1);
			}
				$dyeing_prod_date=" and c.process_end_date between '$start_date' and '$end_date'";
				//==================================================Dyeing Prod===========.========================
					//==================================================Dyeing Prod===========.========================
		  if($company_id==0) $cbo_company_cond=""; else $cbo_company_cond=" and a.company_id=$company_id";
		 // echo $type_id.'SDSD';die;
		  if($type_id==1 || $type_id==2)
		  {
		   if($type_id==1)
		   {
			   $buyerCondIn="and a.buyer_id in($buyer_id)";
			   $buyerCondIn2="and a.buyer_name in($buyer_id)";
			   $buyerCondIn3="and f.buyer_name in($buyer_id)";
		   }
		   if($type_id==2)
		   {
			   $buyerCondIn="and a.buyer_id  in($buyer_id)";
			   
			   $buyerCondIn2="and a.buyer_name  in($buyer_id)";
			   $buyerCondIn3="and f.buyer_name  in($buyer_id)";
		   }
		 
 		 $exchange_rate=80;
		$sql_data="SELECT a.id,a.batch_no,a.color_id,a.floor_id,c.entry_form,a.batch_against,(b.batch_qnty) as batch_qty,c.process_id, c.process_end_date as prod_date,a.batch_weight, a.working_company_id,b.prod_id,b.po_id,b.width_dia_type,a.color_id,a.booking_no,a.extention_no, b.item_description, a.booking_without_order,d.detarmination_id as deter_id 
		from pro_batch_create_mst a, pro_batch_create_dtls b,pro_fab_subprocess c ,product_details_master d,wo_po_break_down e,wo_po_details_master f
		where  a.id=b.mst_id  and c.batch_id=a.id and c.batch_id=b.mst_id and d.id=b.prod_id and e.id=b.po_id and e.job_id=f.id and c.entry_form in(35)  and a.is_sales=0 and c.load_unload_id=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0
  and a.status_active=1 and a.is_deleted=0  and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and f.status_active=1 and f.is_deleted=0 and b.po_id>0    $dyeing_prod_date $cbo_company_cond $buyerCondIn3 order by a.id"; 		//and a.batch_against not in(2)
	//echo $sql_data;
	$batch_against_chk=array(2);
	$nameArray=sql_select($sql_data);
	foreach($nameArray as $row)
    {
		if(!in_array($row[csf('batch_against')],$batch_against_chk))
		{
		$po_id_array[$row[csf('po_id')]] = $row[csf('po_id')];
		}
		$batch_id_array[$row[csf('id')]] = $row[csf('id')];
		$all_batch_id_array[$row[csf('id')]] = $row[csf('id')];
		
	}
	//$job_no_cond=where_con_using_array($batch_id_array,0,'a.job_id');
	
	 $po_id_cond=where_con_using_array($po_id_array,0,'b.id');
	   $sql_dyes_cost =sql_select("select a.batch_no,a.buyer_id,b.item_category,(b.cons_amount) as dyes_chemical_cost
    from inv_issue_master a, inv_transaction b
    where a.id=b.mst_id and b.transaction_type=2  and  a.issue_purpose not in(13)  and a.entry_form=5 and a.batch_no  is not null  and   b.item_category in (5,6,7) $cbo_company_cond  $buyerCondIn ");
	
			$dyes_chemical_arr=array();
			$mm_chk=array();$mm=1;$others_tot_chemical_cost=$tot_chemical_cost_hnmNext=0;
			foreach($sql_dyes_cost as $val)
			{
				$batchArr=array_unique(explode(",",$val[csf("batch_no")]));
				$buyer_id=$val[csf("buyer_id")];
				foreach($batchArr as $bid)
				{
					$all_batch_id=$all_batch_id_array[$bid];//Dyeing only
					if($all_batch_id==$bid)
					{
						if($buyer_id==1 || $buyer_id==10) // HnM and Next
						{
							 $tot_chemical_cost_hnmNextArr[$buyer_id]+=$val[csf("dyes_chemical_cost")]/$exchange_rate;
						}
						else
						{
							//echo $val[csf("dyes_chemical_cost")]."=".$buyer_id.'<br>';
							$others_tot_chemical_costArr[$buyer_id]+=$val[csf("dyes_chemical_cost")]/$exchange_rate;
						}
					}
				}
			}
		 
	
	 
	 
	 $sql_po_buyer="SELECT a.buyer_name,b.id,b.job_no_mst,b.shipment_date,b.po_quantity,b.pub_shipment_date,b.grouping as ref_no,c.color_number_id as color_id from wo_po_break_down b,wo_po_details_master a,wo_po_color_size_breakdown c where  a.id=b.job_id and a.id=c.job_id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1  $po_id_cond $buyerCondIn2 order by b.id asc";
	$sql_po_result_buyer = sql_select($sql_po_buyer);
	foreach ($sql_po_result_buyer as $val) 
	{
		$po_buyer_array[$val[csf('id')]]= $val[csf('buyer_name')];
	}
	unset($sql_po_result_buyer);
	
	 $sql_po="SELECT f.id as conv_id,a.buyer_name,b.id,b.job_no_mst,b.shipment_date,b.po_quantity,b.pub_shipment_date,b.grouping as ref_no,c.color_number_id as color_id,d.lib_yarn_count_deter_id as deter_id,f.cons_process,f.charge_unit,f.color_break_down from wo_po_break_down b,wo_po_details_master a,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_conv_cost_dtls f where  a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.id=f.job_id and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.id=f.fabric_description  and e.cons !=0   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and f.cons_process not in(1,30,35) and d.status_active=1  and e.is_deleted=0 and e.status_active=1 and f.is_deleted=0 and f.status_active=1 $po_id_cond $buyerCondIn2 order by f.id asc";
	$sql_po_result = sql_select($sql_po);
	foreach ($sql_po_result as $val) 
	{
		$color_break_down=$val[csf('color_break_down')];
		$po_qty_array[$val[csf('id')]] = $val[csf('po_quantity')];
		$po_job_array[$val[csf('id')]]= $val[csf('job_no_mst')];
		//$po_buyer_array[$val[csf('id')]]= $val[csf('buyer_name')];
		if($val[csf('cons_process')]==31 && $color_break_down!='')
		{
			$po_color_fab_array[$val[csf('id')]][$val[csf('color_id')]][$val[csf('deter_id')]][$val[csf('cons_process')]]['color_break_down'] = $color_break_down;

			$arr_1=explode("__",$color_break_down);
			for($ci=0;$ci<count($arr_1);$ci++)
			{
			$arr_2=explode("_",$arr_1[$ci]);
			//$this->_rateArray[$id][$arr_2[0]][$arr_2[3]]=$arr_2[1];
				if($arr_2[1]>0)
				{
				$po_color_fab_array[$val[csf('id')]][$arr_2[3]][$val[csf('deter_id')]][$val[csf('cons_process')]]['rate'] =$arr_2[1];
				$po_color_fabricDying_array[$val[csf('id')]][$arr_2[3]][$val[csf('cons_process')]]['rate'] =$arr_2[1];
				}
			}
		}
		else
		{
			$po_color_fab_array[$val[csf('id')]][$val[csf('color_id')]][$val[csf('deter_id')]][$val[csf('cons_process')]]['rate'] = $val[csf('charge_unit')];
			$po_color_only_fab_array[$val[csf('id')]][$val[csf('cons_process')]]['rate'] = $val[csf('charge_unit')];
		}
		
	}
	$process_arr_chk=array(1,30,35);$dyeing_prod_self_amount=$dyeing_prod_self_qty=0;
	foreach($nameArray as $row)
    {
		//$batch_wise_process_arr[$row[csf('booking_no')]][$row[csf('color_id')]][$row[csf('id')]][$row[csf('item_description')]]['batch_qty']+=$row[csf('batch_qty')];
		if($self_po_id=="") $self_po_id=$row[csf('po_id')]; else $self_po_id.=",".$row[csf('po_id')];
		$prod_date_arr[$row[csf('prod_date')]]=$row[csf('prod_date')];
		$dtls_id=$row[csf('dtls_id')];
		$process_idArr=array_unique(explode(",",$row[csf('process_id')]));
		$buyer_id=$po_buyer_array[$row[csf('po_id')]];
		$po_buyer=$buyer_id.'_'.$buyer_short_library[$po_buyer_array[$row[csf('po_id')]]];
		
		//echo $po_buyer_array[$row[csf('po_id')]].'='.$row[csf('id')].'<br>';
		//print_r($process_idArr);
		$tot_amt=0;
		foreach ($process_idArr as $key => $key_id) //conversion_cost_head_array
		{
			$color_break_down=$po_color_fab_array[$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('deter_id')]][$key_id]['color_break_down'];
				//$conv_rate=0;
				if($key_id==31)
				{
					/*$arr_1=explode("__",$color_break_down);
					for($ci=0;$ci<count($arr_1);$ci++)
					{
					$arr_2=explode("_",$arr_1[$ci]);
					}*/
					$conv_rate=$po_color_fab_array[$row[csf('po_id')]][$row[csf('color_id')]][$row[csf('deter_id')]][$key_id]['rate'];
					$fab_conv_rate=$po_color_fabricDying_array[$row[csf('po_id')]][$row[csf('color_id')]][$key_id]['rate'];
					//echo $row[csf('prod_date')].'='.$fab_conv_rate.'='.$row[csf('po_id')].'<br>';
					if($conv_rate==0 || $conv_rate=='') $conv_rate=$fab_conv_rate;
				}
				
				if($row[csf('entry_form')]==35 && $conv_rate>0)
				{
					if(!in_array($row[csf('batch_against')],$batch_against_chk))
					{
						//echo $row[csf('id')].'='.$row[csf('batch_qty')].'='.$conv_rate.'<br>';
						if($conv_rate)
			 			{
							$tot_amt+=$row[csf('batch_qty')]*$conv_rate;
						}
					}
				}
		}
	
			if(!in_array($row[csf('batch_against')],$batch_against_chk))
			{
				$dyeing_prod_Arr[$po_buyer]['in_amt']+=$tot_amt;
				$dyeing_prod_Arr[$po_buyer]['in_qty']+=$row[csf('batch_qty')];
				$dyeing_prod_Arr[$po_buyer]['batch_no']+=$row[csf('batch_no')];
				$dyeing_buyer_Arr[$po_buyer]=$buyer_short_library[$po_buyer_array[$row[csf('po_id')]]];
			}
		
       }
	 } //-======// ============Dyeing End==================
 		
		 
		 
		
		///////===========Sub Con ========================
		  if($type_id==3)
		  {
			$sql_data_sub="SELECT a.id,a.batch_no,a.floor_id,c.entry_form,a.batch_against,(b.batch_qnty) as batch_qty, c.process_end_date as prod_date,a.batch_weight, a.working_company_id,b.prod_id,b.po_id,b.width_dia_type,a.color_id,a.booking_no,a.extention_no, b.item_description, a.booking_without_order 
			from pro_batch_create_mst a, pro_batch_create_dtls b,pro_fab_subprocess c 
			where  a.id=b.mst_id  and c.batch_id=a.id and c.batch_id=b.mst_id and c.entry_form in(38)   and a.is_sales=0 and c.load_unload_id=2 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0 and b.po_id>0 $cbo_company_cond $dyeing_prod_date 
			order by a.id";		
		//echo $sql_data;
			$sub_nameArray=sql_select($sql_data_sub);
			foreach($sub_nameArray as $row)
			{
				$sub_po_id_array[$row[csf('po_id')]] = $row[csf('po_id')];
				$sub_batch_id_array[$row[csf('id')]] = $row[csf('id')];
			}
		  $sub_po_id_cond=where_con_using_array($sub_po_id_array,0,'b.id');
		//print_r($sub_po_id_array);
		 $sql_subcon="SELECT a.currency_id,a.party_id,a.within_group,b.main_process_id,b.id,b.rate from subcon_ord_dtls b,subcon_ord_mst a where a.subcon_job=b.job_no_mst and b.status_active=1 and b.is_deleted=0 and b.rate>0 and b.main_process_id in(4,3) $sub_po_id_cond  ";
		$sql_subcon_res=sql_select($sql_subcon);
			$exchange_rate=80;
		foreach($sql_subcon_res as $row)
		{
			//if($row[csf('within_group')]==1)
			//{
			$sub_party_arr[$row[csf('id')]]= $buyer_short_library[$row[csf('party_id')]];
			$sub_partyId_arr[$row[csf('id')]]= $row[csf('party_id')];
			//}
			
			$sub_order_wise_arr[$row[csf('id')]]['process']  = $row[csf('main_process_id')];
			//$sub_order_wise_arr[$row[csf('id')]]['currency_id']  = $row[csf('currency_id')];
			if($row[csf('currency_id')]==1) //TK
			{ 
				$sub_order_wise_arr[$row[csf('id')]]['rate'] = $row[csf('rate')]/$exchange_rate;
			}
			else
			{
				$sub_order_wise_arr[$row[csf('id')]]['rate'] = $row[csf('rate')];
			}
		}
		//--------Subcon----
		$batch_against_chk2=array(2);
		$subcon_prod_amount=$subcon_prod_qty=0;
		foreach($sub_nameArray as $row)
		{
				$sub_partyId=$sub_partyId_arr[$row[csf('po_id')]];
				$sub_party=$sub_partyId.'_'.$sub_party_arr[$row[csf('po_id')]];
				if(!in_array($row[csf('batch_against')],$batch_against_chk2))
				{
				$sub_rate=$sub_order_wise_arr[$row[csf('po_id')]]['rate'];
				//echo $sub_rate.', ';
				 if($sub_rate)
				 {
					$dyeing_prod_Arr[$sub_party]['sub_qty']+=$row[csf('batch_qty')];
					$dyeing_prod_Arr[$sub_party]['sub_amt']+=$row[csf('batch_qty')]*$sub_rate;
					$sub_buyer_Arr[$sub_party_arr[$row[csf('po_id')]]]=$sub_party_arr[$row[csf('po_id')]];
					$all_batch_id_array[$row[csf('id')]]=$row[csf('id')];
				 }
				}
		}
		$sql_dyes_cost =sql_select("select a.batch_no,a.buyer_id,b.item_category,(b.cons_amount) as dyes_chemical_cost
    from inv_issue_master a, inv_transaction b
    where a.id=b.mst_id and b.transaction_type=2  and  a.issue_purpose not in(13)  and a.entry_form=5 and a.batch_no  is not null  and   b.item_category in (5,6,7) $cbo_company_cond  $buyerCondIn ");
	
	
			$dyes_chemical_arr=array();
			$mm_chk=array();$mm=1;$others_tot_chemical_cost=$tot_chemical_cost_hnmNext=0;
			foreach($sql_dyes_cost as $val)
			{
				$batchArr=array_unique(explode(",",$val[csf("batch_no")]));
				$buyer_id=$val[csf("buyer_id")];
				foreach($batchArr as $bid)
				{
					$all_batch_id=$all_batch_id_array[$bid];//Dyeing only
					if($all_batch_id==$bid)
					{
					$subcon_tot_chemical_costArr[$buyer_id]+=$val[csf("dyes_chemical_cost")]/$exchange_rate;
					}
				}
			}
			
 }//Subcon 
	//print_r($subcon_tot_chemical_costArr);
	///===================****End***********======
	    
	
	//$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
//$color_library=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
 /*$sql_job="select b.id,a.buyer_name,b.po_number from wo_po_break_down b,wo_po_details_master a where  a.id=b.job_id  $po_cond";
	$sql_job_result = sql_select($sql_job);
	foreach ($sql_job_result as $val) 
	{
		$po_buyer_array[$val[csf('id')]]['buyer']=$buyer_library[$val[csf('buyer_name')]];
		$po_buyer_array[$val[csf('id')]]['po_no']=$val[csf('po_number')];
	}
	unset($sql_job_result);*/

	 
	 ob_start();
	?>
	<div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onClick="new_window()" />
         <div id="report_container"> </div>
        <?
        $table_width=730;
		 if($type_id!=3)
		 {
			$head_type="Dyeing"; 
			$dyeing_buyer=implode(",",$dyeing_buyer_Arr);
		 }
		 else
		 {
			 $head_type="Subcon Dyeing"; 
			 $dyeing_buyer=implode(",",$sub_buyer_Arr);
		 }
		?>
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
         <caption> <b> <? echo $head_type; ?> Details-(<? echo $dyeing_buyer;?>)</b></caption>
            <thead>
                <tr>
                    <th width="40" >SL</th>
                    <th width="200">Buyer </th>
                    <th width="100">Prod. Qty</th>
                    <?
                   // if($type_id!=3)
					//{
					?>
                    <th width="100">Amount</th>
                    <th width="100">Cost</th>
                    <?
					//}
					?>
                    <th width="100">Income</th>
                </tr>
            </thead>
            <tbody>
                 <?
                        $i=1;$tot_dyeing_qty=$tot_dyeing_amount=$tot_chemical_cost=$tot_income=0;
                        foreach($dyeing_prod_Arr as $buyerData=>$row)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$buyerArr=explode("_",$buyerData);
							$buyerId=$buyerArr[0];
							$buyerName=$buyerArr[1];
							$prod_qty=$row[('in_qty')]+$row[('sub_qty')];
							if($type_id!=3)
							{
							  if($type_id==1)//HnM & Next Buyer
							  {
								$chemical_cost=$tot_chemical_cost_hnmNextArr[$buyerId];
							  }
							  else
							  {
								  $chemical_cost=$others_tot_chemical_costArr[$buyerId];
							  }
							}
							else
							{
								//$subcon_tot_chemical_costArr[$buyer_id];
								$chemical_cost=$subcon_tot_chemical_costArr[$buyerId];	
							}
							$prod_amt=$row[('sub_amt')]+$row[('in_amt')];
							$income=$prod_amt-$chemical_cost;
							if($prod_amt) $prod_amt=$prod_amt;else $prod_amt=0;
							if($income) $income=$income;else $income=0;
							 
                         ?>
                           <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trself_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trself_<? echo $i; ?>">
                                <td  width="20"><? echo $i; ?></td>
                                <td  width="200"><? echo $buyerName; ?></td>
                                <td  width="100" align="right" title="SubAmt=<? echo $row[('sub_amt')];?>"><? echo number_format($prod_qty,2);?></td>
                             <?
							//if($type_id!=3)
							//{
							?>
                                <td  width="100" align="right" title="Rate*Batch Qty"><? echo number_format($prod_amt,2); ?></td>
                                <td  width="100" align="right" title=""><? echo number_format($chemical_cost,2); ?></td>
                                <?
							//}?>
                                <td  width="100" align="right" title="Amount-Cost"><? echo number_format($income,2); ?></td>
                                <?
								$col_spna=3;
							 
								?>
                            </tr>
                        <?
                        $i++;
						$tot_dyeing_qty+=$prod_qty;
						$tot_dyeing_amount+=$prod_amt;
						$tot_chemical_cost+=$chemical_cost;
						$tot_income+=$income;
                        }
                        ?>
                        <tr bgcolor="#CCCCCC"> 
                        <td align="right" colspan="2"><strong>Total</strong></td>
                        <td align="right"><? echo number_format($tot_dyeing_qty,2); ?>&nbsp;</td>
                           <?
							//if($type_id!=3)
							//{
							?>
                        <td align="right"><? echo number_format($tot_dyeing_amount,2,'.',''); ?>&nbsp;</td>
                        <td align="right"><? echo number_format($tot_chemical_cost,2,'.',''); ?>&nbsp;</td>
                        <?
							//}
						?>
                        <td align="right"><? echo number_format($tot_income,2,'.',''); ?>&nbsp;</td>
                       
                    </tr>
            </tbody>
		</table>
        <?
		$html=ob_get_contents();
			ob_flush();
			foreach (glob(""."*.xls") as $filename) 
			{
			   @unlink($filename);
			}
			
			//html to xls convert
			$name=time();
			$name=$user_id."_".$name.".xls";
			$create_new_excel = fopen(''.$name, 'w');	
			$is_created = fwrite($create_new_excel,$html);
	?>
      <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
    <script>
	$(document).ready(function(e) {
				document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
		});	
	</script>
     </div>
    <?
	exit();
}

if ($action=="finish_earn_inhouse_popup")  // All Production Data popup dyeing_earn_inboundSub_popup
{
	
	extract($_REQUEST);
 	echo load_html_head_contents("Dyeing inhouse poppup Info","../../../", 1, 1, $unicode,'','');
	
	$date_key=str_replace("'","",$date_key);
	$date_Arr=explode("_",$date_key);
	$from_date=$date_Arr[0];
	$to_date=$date_Arr[1];
	$buyer_id=str_replace("'","",$buyer_id);
	$company_id=str_replace("'","",$company_id);
	$type_id=str_replace("'","",$type);
	//echo $buyer_id.'dd';die;
	 	//if($type_id==1)
		  // {
			   $buyerCondIn="and a.buyer_id in($buyer_id)";
			   $buyerCondIn2="and a.buyer_name in($buyer_id)";
			   $buyerCondIn3="and f.buyer_name in($buyer_id)";
		  // }
		   
	 if($db_type==0)
			{
				$start_date=change_date_format(str_replace("'","",$to_date),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$from_date),"yyyy-mm-dd","");
			}
			else if($db_type==2)
			{
				$start_date=change_date_format(str_replace("'","",$from_date),"","",1);
				$end_date=change_date_format(str_replace("'","",$to_date),"","",1);
			}
				$dyeing_prod_date=" and c.process_end_date between '$start_date' and '$end_date'";
				//==================================================Dyeing Prod===========.========================
				 if($company_id==0) $cbo_company_cond=""; else $cbo_company_cond=" and a.company_id=$company_id";
		$sql_data_special="SELECT e.id as dtls_id,a.id,a.batch_no,a.color_id,a.floor_id,c.entry_form,a.batch_against,(e.production_qty) as batch_qty,c.process_id,c.process_end_date as prod_date,a.batch_weight, a.working_company_id,b.prod_id,b.po_id,b.width_dia_type,a.color_id,a.booking_no,a.extention_no, b.item_description,c.entry_form, a.booking_without_order,d.detarmination_id as deter_id 
		from pro_batch_create_mst a, pro_batch_create_dtls b,pro_fab_subprocess c,pro_fab_subprocess_dtls e ,product_details_master d
		where  a.id=b.mst_id  and c.batch_id=a.id and c.batch_id=b.mst_id and d.id=b.prod_id and c.id=e.mst_id and e.prod_id=b.prod_id and e.prod_id=d.id  and c.entry_form in(32,48,33,34)  and a.is_sales=0 and b.status_active=1 and b.is_deleted=0 and a.status_active=1 and a.is_deleted=0  and c.status_active=1 and c.is_deleted=0  and e.status_active=1 and e.is_deleted=0
  and a.status_active=1 and a.is_deleted=0  and d.status_active=1 and d.is_deleted=0 and b.po_id>0 $cbo_company_cond  $dyeing_prod_date
		order by a.id";		//and a.batch_against not in(2)
	//echo $sql_data;
	//$batch_against_chk=array(2);
	$nameArray_special=sql_select($sql_data_special);
	foreach($nameArray_special as $row)
    {
		$po_id_array[$row[csf('po_id')]] = $row[csf('po_id')];
	}	//and a.batch_against not in(2)
	//echo $sql_data;
	 
	
	 $po_id_cond=where_con_using_array($po_id_array,0,'b.id');
	 $sql_po_buyer="SELECT a.buyer_name,b.id from wo_po_break_down b,wo_po_details_master a where  a.id=b.job_id   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1   $po_id_cond $buyerCondIn2 order by b.id asc";
	$sql_po_result_buyer = sql_select($sql_po_buyer);
	foreach ($sql_po_result_buyer as $row) 
	{
		$po_buyer_array[$row[csf('id')]]= $row[csf('buyer_name')];
	}
	unset($sql_po_buyer);
	
	 $sql_po="SELECT f.id as conv_id,a.buyer_name,b.id,b.job_no_mst,b.shipment_date,b.po_quantity,b.pub_shipment_date,b.grouping as ref_no,c.color_number_id as color_id,d.lib_yarn_count_deter_id as deter_id,f.cons_process,f.charge_unit,f.color_break_down from wo_po_break_down b,wo_po_details_master a,wo_po_color_size_breakdown c,wo_pre_cost_fabric_cost_dtls d,wo_pre_cos_fab_co_avg_con_dtls e,wo_pre_cost_fab_conv_cost_dtls f where  a.id=b.job_id and a.id=c.job_id and a.id=d.job_id and a.id=e.job_id and a.id=f.job_id and b.id=c.po_break_down_id and d.id=e.pre_cost_fabric_cost_dtls_id and c.po_break_down_id=e.po_break_down_id and c.item_number_id= d.item_number_id and c.color_number_id=e.color_number_id and c.size_number_id=e.gmts_sizes and d.id=f.fabric_description  and e.cons !=0   and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and f.cons_process not in(1,30,35) and d.status_active=1  and e.is_deleted=0 and e.status_active=1 and f.is_deleted=0 and f.status_active=1 $po_id_cond $buyerCondIn2 order by f.id asc";
	$sql_po_result = sql_select($sql_po);
	foreach ($sql_po_result as $val) 
	{
		$color_break_down=$val[csf('color_break_down')];
		$po_qty_array[$val[csf('id')]] = $val[csf('po_quantity')];
		$po_job_array[$val[csf('id')]]= $val[csf('job_no_mst')];
		
		if($val[csf('cons_process')]==31 && $color_break_down!='')
		{
			$po_color_fab_array[$val[csf('id')]][$val[csf('color_id')]][$val[csf('deter_id')]][$val[csf('cons_process')]]['color_break_down'] = $color_break_down;

			$arr_1=explode("__",$color_break_down);
			for($ci=0;$ci<count($arr_1);$ci++)
			{
			$arr_2=explode("_",$arr_1[$ci]);
			//$this->_rateArray[$id][$arr_2[0]][$arr_2[3]]=$arr_2[1];
			$po_color_fab_array[$val[csf('id')]][$arr_2[3]][$val[csf('deter_id')]][$val[csf('cons_process')]]['rate'] =$arr_2[1];
			$po_color_fabricDying_array[$val[csf('id')]][$arr_2[3]][$val[csf('cons_process')]]['rate'] =$arr_2[1];
			}
		}
		else
		{
			$po_color_fab_array[$val[csf('id')]][$val[csf('color_id')]][$val[csf('deter_id')]][$val[csf('cons_process')]]['rate'] = $val[csf('charge_unit')];
			$po_color_only_fab_array[$val[csf('id')]][$val[csf('cons_process')]]['rate'] = $val[csf('charge_unit')];
		}
		
	}
	
 		 //==============================Specila Finish here*****====================
		 $special_finish_prod_amt=$special_finish_prod_qty=0;
		foreach($nameArray_special as $row)// for Finish Production 
		{
					$po_buyer=$po_buyer_array[$row[csf('po_id')]];
				$sfin_fab_conv_rate=$po_color_only_fab_array[$row[csf('po_id')]][$row[csf('process_id')]]['rate'];
				$special_fin_amt=$row[csf('batch_qty')]*$sfin_fab_conv_rate;
				$entry_formId=$row[csf('entry_form')];
				if($special_fin_amt>0)
				{
					$dtls_id=$row[csf('dtls_id')];
					if($dtls_chk_arr[$dtls_id]=="")
					{
					$special_finish_prod_arr[$entry_formId][$po_buyer]['amt']+=$special_fin_amt;
					$special_finish_prod_arr[$entry_formId][$po_buyer]['qty']+=$row[csf('batch_qty')];
					//$prod_date_qty_arr[$row[csf('prod_date')]]['special_finish_qty']+=$row[csf('batch_qty')];
					$special_buyer_arr[$buyer_short_library[$po_buyer]]=$buyer_short_library[$po_buyer];
					$dtls_chk_arr[$dtls_id]=$dtls_id;
					}
					 
				}
		}
		unset($nameArray_special);
		 
		 	
	 
	 ob_start();
	?>
	<div id="data_panel" style="width:100%">
			<script>
				function new_window()
				{
					var w = window.open("Surprise", "#");
					var d = w.document.open();
					d.write(document.getElementById('details_reports').innerHTML);
					d.close();
				}
            </script>
        <input type="button" value="Print" id="print" class="formbutton" style="width:100px;" onClick="new_window()" />
         <div id="report_container"> </div>
        <?
        $table_width=530;
		?>
        </div>
        <div style="width:<? echo $table_width; ?>px" align="center" id="details_reports">
		<table cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" width="<? echo $table_width; ?>">
         <caption> <b>Finishing Details(<? echo implode(",",$special_buyer_arr);?>)</b></caption>
            <thead>
                <tr>
                    <th width="40" >SL</th>
                    <th width="200">Buyer </th>
                    <th width="100">Process </th>
                    <th width="100">Finish. Qty</th>
                    <th width="100">Income</th>
                </tr>
            </thead>
            
                 <?
                        $i=1;$tot_dyeing_qty=$tot_dyeing_amount=0;
                        foreach($special_finish_prod_arr as $processId=>$processArr)
                        {
							foreach($processArr as $buyerId=>$row)
                        	{
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$prod_qty=$row[('qty')];
							 
							$prod_amt=$row[('amt')];
							if($prod_amt) $prod_amt=$prod_amt;else $prod_amt=0;
							 
                         ?>
                           <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('trself_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="trself_<? echo $i; ?>">
                                <td  width="20"><? echo $i; ?></td>
                                <td  width="200"><? echo $buyer_short_library[$buyerId]; ?></td>
                                <td  width="100"><? echo $entry_form[$processId]; ?></td>
                                <td  width="100" align="right" title=""><? echo number_format($prod_qty,2);?></td>
                                <td  width="100" align="right" title="Rate*Batch Qty"><? echo number_format($prod_amt,2); ?></td>
                                <?
								$col_spna=3;
							 
								?>
                            </tr>
                        <?
                        $i++;
						$tot_dyeing_qty+=$prod_qty;
						$tot_dyeing_amount+=$prod_amt;
							}
                        }
                        ?>
                        <tr bgcolor="#CCCCCC"> 
                        <td align="right" colspan="3"><strong>Total</strong></td>
                        <td align="right"><? echo number_format($tot_dyeing_qty,2); ?>&nbsp;</td>
                        <td align="right"><? echo number_format($tot_dyeing_amount,2,'.',''); ?>&nbsp;</td>
                       
                    </tr>
             
		</table>
        <?
		$html=ob_get_contents();
			ob_flush();
			foreach (glob(""."*.xls") as $filename) 
			{
			   @unlink($filename);
			}
			
			//html to xls convert
			$name=time();
			$name=$user_id."_".$name.".xls";
			$create_new_excel = fopen(''.$name, 'w');	
			$is_created = fwrite($create_new_excel,$html);
	?>
      <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
    <script>
	$(document).ready(function(e) {
				document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
		});	
	</script>
     </div>
    <?
	exit();
}



?>
