<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');
require_once('../../../../includes/class3/class.conditions.php');
require_once('../../../../includes/class3/class.reports.php');
require_once('../../../../includes/class3/class.yarns.php');
require_once('../../../../includes/class3/class.conversions.php');
require_once('../../../../includes/class3/class.others.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
//$location_library=return_library_array( "select id,location_name from lib_location", "id", "location_name"  );
//$floor_library=return_library_array( "select id,floor_name from lib_prod_floor", "id", "floor_name"  );

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 150, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select --", $selected, "load_drop_down( 'requires/full_shipment_style_report_controller', $data+'**'+this.value, 'load_drop_down_del_floor', 'del_floor_td' );" );
}
if ($action=="load_drop_down_del_floor")
{
	$data=explode('**',$data);
	echo create_drop_down( "cbo_del_floor", 120, "select id,floor_name from lib_prod_floor where company_id='$data[0]' and location_id='$data[1]' and status_active =1 and is_deleted=0 and production_process=11 order by floor_name","id,floor_name", 1, "-- Select Floor --", $selected, "" );
	exit();
}



if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$reportType=str_replace("'","",$reportType);


	if($cbo_company_name>0)
	{
		 $company_cond="and c.company_name like '$cbo_company_name' ";
	}
	else
	{
		 $company_cond="";
	}
	if($txt_date_from!="" && $txt_date_to!="")
	{
		$str_cond="and a.ex_factory_date between '$txt_date_from' and  '$txt_date_to ' ";
	}
	else
	{
		$str_cond="";
	}


	$details_report="";
	$master_data=array();
	$current_date=date("Y-m-d");
	$date=date("Y-m-d");$break_id=0;$sc_lc_id=0;
	$sy = date('Y',strtotime($txt_date_from));
	$basic_smv_arr=return_library_array( "select comapny_id, basic_smv from lib_capacity_calc_mst where year=$sy",'comapny_id','basic_smv');

	$tot_cost_arr = return_library_array("select job_no, cm_for_sipment_sche from wo_pre_cost_dtls","job_no","cm_for_sipment_sche");
	$costing_per_arr = return_library_array("select job_no, costing_per from wo_pre_cost_mst","job_no","costing_per");


	ob_start();
	if($reportType==1)
	{

		$challan_sql="select  b.po_break_down_id, b.ex_factory_date,
			(case when b.entry_form!=85 then b.ex_factory_qnty else 0 end) as ex_factory_qnty,
			(case when b.entry_form=85 then b.ex_factory_qnty else 0 end) as ex_factory_return_qnty,
			b.total_carton_qnty as carton_qnty
			from  pro_ex_factory_mst b
			where b.status_active=1 and b.is_deleted=0";

		$challan_sql_result=sql_select($challan_sql);
		foreach($challan_sql_result as $row)
		{
			$exfact_qty_arr_without_current[$row[csf("po_break_down_id")]]["ex_fact"]+=$row[csf("ex_factory_qnty")]-$row[csf("ex_factory_return_qnty")];
			$exfact_qty_arr_without_current[$row[csf("po_break_down_id")]]["carton"]+=$row[csf("carton_qnty")];
		}

		$sql_res_1 = sql_select("SELECT a.buyer_name,a.style_ref_no, a.job_no_prefix_num,a.job_no,a.set_smv,d.ex_factory_date,c.country_ship_date,b.id as po_id, c.order_quantity as po_qnty,a.id as job_id,b.shiping_status,e.color_size_break_down_id as deli_color_size_id,e.production_qnty,d.carton_qnty
		FROM wo_po_details_master a , wo_po_break_down b,  wo_po_color_size_breakdown c,  pro_ex_factory_mst d , pro_ex_factory_dtls e
		where a.job_no = b.job_no_mst and a.job_no = c.job_no_mst and b.id = c.po_break_down_id and b.id = d.po_break_down_id and c.po_break_down_id = d.po_break_down_id and d.id = e.mst_id
		and c.id = e.color_size_break_down_id and c.status_active = 1 and c.is_deleted = 0 and a.company_name = $cbo_company_name and d.ex_factory_date between '$txt_date_from' and  '$txt_date_to ' and b.shiping_status =3
		order by a.buyer_name,a.style_ref_no,c.id desc");

		$primary_data_arr = array();

		$job_ids="";$chk_arr=array();
		foreach ($sql_res_1 as $value)
		{
			if($chk_arr[$value[csf("deli_color_size_id")]] == "")
			{
				$chk_arr[$value[csf("deli_color_size_id")]]  =$value[csf("deli_color_size_id")];
				/*if($delivery_job_max_date_arr[$value[csf("job_no")]]["ex_factory_date"]=="")
				{
					$delivery_job_max_date_arr[$value[csf("job_no")]]["ex_factory_date"]= $value[csf("ex_factory_date")];
				}else if($delivery_job_max_date_arr[$value[csf("job_no")]]["ex_factory_date"] < $value[csf("ex_factory_date")])
				{
					$delivery_job_max_date_arr[$value[csf("job_no")]]["ex_factory_date"]= $value[csf("ex_factory_date")];
				}*/

				$full_ship_po_job_ids .= $value[csf("job_id")].",";
			}
		}
		$full_ship_po_job= array_unique(explode(",",chop($full_ship_po_job_ids,",")));
		$full_ship_po_job_ids = implode(",",$full_ship_po_job);

		if($full_ship_po_job_ids == "")
		{
			echo '<br><span style="text-align:center;color:red;font-weight:bold;font-size:16px;">Data Not Found</span>';die;
		}

		$non_full_ship_job_res = sql_select("select a.job_no,a.id as job_id, b.id
		from wo_po_details_master a, wo_po_break_down b
		where a.job_no = b.job_no_mst
		and b.status_active = 1 and b.shiping_status <> 3 and  a.id in ($full_ship_po_job_ids)");
		foreach ($non_full_ship_job_res as $nonFull)
		{
			$nonFullJobIds .= $nonFull[csf("job_id")].",";
		}
		$nonFullJobIdArr= array_unique(explode(",",chop($nonFullJobIds,",")));
		$remainFullArr =  array_diff($full_ship_po_job, $nonFullJobIdArr);

		/*print_r($full_ship_po_job);
		echo "<br>";
		print_r($nonFullJobIdArr);
		echo "<br>";
		print_r($remainFullArr);
		die;*/
		//print_r($nonFullJobIdArr);
		$job_ids = implode(",",$remainFullArr);

		//echo $job_ids;die;
		if($job_ids == "")
		{
			echo '<br><span style="text-align:center;color:red;font-weight:bold;font-size:16px;">Data Not Found</span>';die;
		}

		$maxDateChkRes = sql_select("select a.job_no,a.id as job_id, max(c.ex_factory_date) ex_factory_date
		from wo_po_details_master a, wo_po_break_down b, pro_ex_factory_mst c
		where a.job_no = b.job_no_mst and b.id = c.po_break_down_id and b.status_active = 1 and c.status_active=1 and b.shiping_status = 3 and a.id in ($job_ids) group by a.job_no,a.id");
		foreach ($maxDateChkRes as $md)
		{
			/*if($maxDateChkArr[$md[csf("job_no")]]["ex_date"]=="")
			{
				$maxDateChkArr[$md[csf("job_no")]]["ex_date"]= $md[csf("ex_factory_date")];
			}
			else if($maxDateChkArr[$md[csf("job_no")]]["ex_date"] < $md[csf("ex_factory_date")])
			{
				$maxDateChkArr[$md[csf("job_no")]]["ex_date"]= $md[csf("ex_factory_date")];
			}*/
			$maxDateChkArr[$md[csf("job_no")]]["ex_date"]= $md[csf("ex_factory_date")];

		}

		$sql_res_2 = sql_select("select a.job_no,a.company_name,a.buyer_name ,b.id as po_id, b.unit_price/a.total_set_qnty as unit_price, c.order_quantity,c.country_ship_date, a.set_smv,a.style_ref_no
		from wo_po_details_master a, wo_po_break_down b ,wo_po_color_size_breakdown c
		where a.job_no = b.job_no_mst and b.id = c.po_break_down_id and a.status_active = 1 and a.is_deleted = 0 and b.status_active = 1 and b.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and a.id in  ($job_ids)");

		foreach ($sql_res_2 as $val)
		{

			if(strtotime($maxDateChkArr[$val[csf("job_no")]]["ex_date"]) <= strtotime($txt_date_to))
			{
				$details_data_arr[$val[csf("job_no")]]["buyer_name"] = $val[csf("buyer_name")];
				$details_data_arr[$val[csf("job_no")]]["style_ref_no"] = $val[csf("style_ref_no")];
				//$details_data_arr[$val[csf("job_no")]]["job_no"] = $val[csf("job_no_prefix_num")];
				$details_data_arr[$val[csf("job_no")]]["set_smv"] = $val[csf("set_smv")];

				$details_data_arr[$val[csf("job_no")]]["po_qnty"] +=$val[csf("order_quantity")];
				$master_data_arr[$val[csf("buyer_name")]]["po_qnty"] +=$val[csf("order_quantity")];

				$po_id_po_qnty[$val[csf("po_id")]]["po_qnty"] +=$val[csf("order_quantity")];


				if($deli_max_date_arr[$val[csf("job_no")]]["country_ship_date"]=="")
				{
					$details_data_arr[$val[csf("job_no")]]["country_ship_date"]= $val[csf("country_ship_date")];
				}else if($deli_max_date_arr[$val[csf("job_no")]]["country_ship_date"] < $val[csf("country_ship_date")])
				{
					$details_data_arr[$val[csf("job_no")]]["country_ship_date"]= $val[csf("country_ship_date")];
				}

				$job_numbers .= "'".$val[csf("job_no")]."',";
			}
		}

		$job_numbers = implode(",",array_unique(explode(",",chop($job_numbers,","))));
		//echo $job_numbers;die;


		$condition= new condition();
		if(str_replace("'","",$job_numbers) !=''){
			$condition->job_no("in ($job_numbers)");
		}
		$condition->init();
		//$costPerArr=$condition->getCostingPerArr();
		$other= new other($condition);
		$other_cost=$other->getAmountArray_by_job();

		/*echo "<pre>";
		print_r($other_cost);die;*/

		$cost_exch = sql_select("select a.costing_per, a.exchange_rate, a.job_no, a.costing_date  from wo_pre_cost_mst a where a.job_no in ($job_numbers)");
		foreach ($cost_exch as $ch)
		{

		/*	$quaCostingPer=$ch[csf('costing_per')];
			$quaCostingPerQty=0;
			if($quaCostingPer==1){
				$quaCostingPerQty=12;
			}
			if($quaCostingPer==2){
				$quaCostingPerQty=1;
			}
			if($quaCostingPer==3){
				$quaCostingPerQty=24;
			}
			if($quaCostingPer==4){
				$quaCostingPerQty=36;
			}
			if($quaCostingPer==5){
				$quaCostingPerQty=48;
			}

			$costExchangeFromJob[$ch[csf("job_no")]]["costing_per"] = $quaCostingPerQty;*/

			$costExchangeFromJob[$ch[csf("job_no")]]["exchange_rate"] = $ch[csf("exchange_rate")];
			$costExchangeFromJob[$ch[csf("job_no")]]["costing_date"] = $ch[csf("costing_date")];

		}


		$financial_para=array();
		$sql_std_para=sql_select("select cost_per_minute,applying_period_date as from_period_date from lib_standard_cm_entry where company_id=$cbo_company_name and status_active=1 and is_deleted=0  order by id desc");
		foreach($sql_std_para as $row)
		{
			$period_date=date("m-Y", strtotime($row[csf('from_period_date')]));
			$financial_para[$period_date]['cost_per_minute']=$row[csf('cost_per_minute')];
		}
		$sql_cm_cost="select a.jobNo,b.job_no, a.available_min,a.production_date,b.buyer_name from production_logicsoft a, wo_po_details_master b where a.jobNo = b.job_no and a.jobNo in ($job_numbers)";

		$cm_data_array=sql_select($sql_cm_cost);
		foreach($cm_data_array as $row)
		{
			$costing_date = $costExchangeFromJob[$row[csf('job_no')]]["costing_date"];
			$costing_date = date("m-Y", strtotime($costing_date));
			$cost_per_minute=$financial_para[$costing_date]['cost_per_minute'];
			$g_exchange_rate = $costExchangeFromJob[$row[csf('job_no')]]["exchange_rate"];
			$details_data_arr[$row[csf('job_no')]]['actual_cost_cm']+=($row[csf('available_min')]*$cost_per_minute)/$g_exchange_rate;
			$master_data_arr[$row[csf('buyer_name')]]['actual_cost_cm']+=($row[csf('available_min')]*$cost_per_minute)/$g_exchange_rate;
		}


		$po_chk = array();
		foreach ($sql_res_2 as $val2)
		{
			if(strtotime($maxDateChkArr[$val2[csf("job_no")]]["ex_date"]) <= strtotime($txt_date_to))
			{
				if($po_chk[$val2[csf("po_id")]] == "")
				{
					$po_chk[$val2[csf("po_id")]] = $val2[csf("po_id")];
					$details_data_arr[$val2[csf("job_no")]]["po_value"] += $po_id_po_qnty[$val2[csf("po_id")]]["po_qnty"]*$val2[csf("unit_price")];
					$details_data_arr[$val2[csf("job_no")]]["exfact_qnty"] += $exfact_qty_arr_without_current[$val2[csf("po_id")]]["ex_fact"];
					$details_data_arr[$val2[csf("job_no")]]["ex_value"] += $exfact_qty_arr_without_current[$val2[csf("po_id")]]["ex_fact"]*$val2[csf("unit_price")];
					$details_data_arr[$val2[csf("job_no")]]["carton_qnty"] += $exfact_qty_arr_without_current[$val2[csf("po_id")]]["carton"];

					$master_data_arr[$val2[csf("buyer_name")]]["po_value"] += $po_id_po_qnty[$val2[csf("po_id")]]["po_qnty"]*$val2[csf("unit_price")];
					$master_data_arr[$val2[csf("buyer_name")]]["exfact_qnty"] += $exfact_qty_arr_without_current[$val2[csf("po_id")]]["ex_fact"];
					$master_data_arr[$val2[csf("buyer_name")]]["exfact_value"] += $exfact_qty_arr_without_current[$val2[csf("po_id")]]["ex_fact"]*$val2[csf("unit_price")];
					$master_data_arr[$val2[csf("buyer_name")]]["basic_qnty"] += ($exfact_qty_arr_without_current[$val2[csf("po_id")]]["ex_fact"]*$val2[csf("set_smv")])/$basic_smv_arr[$val2[csf("company_name")]];


					// cm cost =====================>>>>

					$cm_rate = $other_cost[$val2[csf("job_no")]]["cm_cost"]/$details_data_arr[$val2[csf("job_no")]]["po_qnty"];
					$details_data_arr[$val2[csf("job_no")]]["pre_cost_cm"] += $exfact_qty_arr_without_current[$val2[csf("po_id")]]["ex_fact"]*$cm_rate;
					$master_data_arr[$val2[csf("buyer_name")]]["pre_cost_cm"] += $exfact_qty_arr_without_current[$val2[csf("po_id")]]["ex_fact"]*$cm_rate;

					// ==================================


				}
			}
		}


		foreach($master_data_arr as $rows)
		{
			$total_po_val+=$rows[po_value];
		}
		/*echo "<pre>";
		print_r($delivery_job_max_date_arr);
		die;*/
		?>
        <div style="width:1330px;">
           	<div style="width:1220px" >
                <table width="1200"  cellspacing="0"  align="center">
                    <tr>
                        <td align="center" colspan="11" class="form_caption">
                            <strong style="font-size:16px;">Company Name:<? echo  $company_library[$cbo_company_name] ;?></strong>
                        </td>
                    </tr>
                    <tr class="form_caption">
                        <td colspan="11" align="center" class="form_caption"> <strong style="font-size:15px;">Full Shipped Report</strong></td>
                    </tr>
                     <tr align="center">
                        <td colspan="11" align="center" class="form_caption"> <strong style="font-size:15px;">Total Summary</strong></td>
                    </tr>
                    </table>
                <table width="1200" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1">
                    <thead>
                        <th width="40" height="34">SL</th>
                        <th width="130">Buyer Name</th>
                        <th width="100">PO Qty.</th>
                        <th width="130">PO Value</th>
                        <th width="100">PO Value(%)</th>
                        <th width="100">Ex-Fact. Qty.</th>
                        <th width="130">Ex-Fact. Value </th>
                        <!-- <th width="100">Total Ex-Fact. (Basic Qty)</th> -->
                        <th width="100">Total Ex-Fact. Value %</th>
                        <th width="100">Pre-cost CM</th>
                        <th width="100">Actual CM</th>
                        <th >Variance</th>
                    </thead>
                </table>
                <table width="1200" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_body">
	                <?
	                $j=1;

	                foreach($master_data_arr as $buyer_id=> $rows)
	                {
	                    if ($j%2==0)
	                    $bgcolor="#E9F3FF";
	                    else
	                    $bgcolor="#FFFFFF";
	                     ?>
	                  	<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tri_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tri_<? echo $j; ?>" >
	                        <td width="40" align="center"><? echo $j; ?></td>
	                        <td width="130">
	                        <p><?
	                        echo $buyer_arr[$buyer_id];
	                        ?></p>
	                        </td>
	                        <td width="100" align="right">
	                        	<p>
	                        		<?  $po_quantity=$rows["po_qnty"];echo number_format($po_quantity,0); $total_buyer_po_quantity+=$po_quantity; ?>
	                        	</p>
	                        </td>
	                        <td width="130" align="right" >
	                        	<p  id="value_<? echo $j ; ?>"><? $buyer_po_value=$rows["po_value"]; echo number_format($buyer_po_value,2 ,'.', '');  $total_buyer_po_value+=$buyer_po_value; ?>
	                        	</p>
	                        </td>
	                        <td width="100" align="right">
	                         <? echo number_format(($buyer_po_value/$total_po_val)*100,2,'.','');$parcentages+=($buyer_po_value/$total_po_val)*100; ?>
	                        </td>

	                        <td align="right" width="100">
	                        <p><?
	                         $total_ex_fact_qty=$rows["exfact_qnty"]; echo number_format($total_ex_fact_qty,0,'',''); $mt_total_ex_fact_qty+=$total_ex_fact_qty;
	                        ?></p>
	                        </td>
	                        <td align="right" width="130">
	                        <p><?
	                         $total_ex_fact_value=$rows["exfact_value"];  echo  number_format($total_ex_fact_value,2,'.',''); $mt_total_ex_fact_value+=$total_ex_fact_value;
	                        ?></p>
	                        </td>

	                        <!-- <td width="100" align="right">
	                        <p><?
	                         //$buyer_basic_qnty=$rows["basic_qnty"];  echo number_format($buyer_basic_qnty,0,'',''); $total_buyer_basic_qnty +=$buyer_basic_qnty;
	                        ?></p>
	                        </td> -->

	                        <td width="100" align="right">
	                        <p><?
	                        $total_ex_fact_value_parcentage=($total_ex_fact_value/$buyer_po_value)*100;
	                        echo number_format($total_ex_fact_value_parcentage,0)
	                        ?> %</p>
	                        </td>
	                        <td width="100" align="right">
	                        	<?
	                        		echo number_format($rows["pre_cost_cm"],2,'.',''); $total_pre_cost_cm+= $rows["pre_cost_cm"];
	                        	?>
	                        </td>
	                        <td width="100" align="right">
	                        	<?
	                        		echo number_format($rows["actual_cost_cm"],2,'.',''); $total_actual_cost_cm+= $rows["actual_cost_cm"];
	                        	?>
	                        </td>
	                        <td align="right">
	                        	<?
	                        		$variance = $rows["pre_cost_cm"] - $rows["actual_cost_cm"]; $total_variance += $variance;
	                        		echo number_format($variance,2,'.','');
	                        	?>
	                        </td>
	                    </tr>
	                    <?
	                    $j++;
	                }
                    ?>

                    <tfoot>
                        <th align="right" colspan="2"><b>Total:</b></th>
                        <th  align="right"><? echo number_format($total_buyer_po_quantity,0);  ?></th>
                        <th  align="right" ><? echo number_format($total_buyer_po_value,2 ,'.', ''); ?> </th>
                        <th align="right"><? echo ceil($parcentages); ?></th>
                        <th align="right"><? echo number_format($mt_total_ex_fact_qty,0); ?></th>
                        <th align="right"><? echo number_format($mt_total_ex_fact_value,2); ?></th>
                        <!-- <th  align="right"><? //echo number_format($total_buyer_basic_qnty,0); ?></th> -->
                        <th align="right"></th>
                        <th align="right"><? echo number_format($total_pre_cost_cm,2); ?></th>
                        <th align="right"><? echo number_format($total_actual_cost_cm,2); ?></th>
                        <th align="right"><? echo number_format($total_variance,2); ?></th>
                    </tfoot>
                </table>
            </div>

            <br />
            <div>
                <table width=""  >
                    <tr>
                    <td colspan="12" class="form_caption"><strong style="font-size:16px;">Full Shipped Out Style Details Report</strong></td>
                    </tr>
                </table>
                <table width="1390" border="1" class="rpt_table" rules="all" id="table_header_2" style="margin-right: 10px; ">
                    <thead>
                    	<tr>
	                        <th width="40">SL</th>
	                        <th width="100">Buyer Name</th>
	                        <th width="100">Style Ref. no.</th>
	                        <th width="80">Job</th>
	                        <th width="60">Item SMV</th>
	                        <th width="100">Ex-Fac. Date</th>
	                        <th width="100">Shipment Date</th>
	                        <th width="80">PO Qtny. (pcs)</th>
	                        <th width="80">Ex-Fact. Qty.</th>
	                        <th width="80">Excess</th>
	                        <th width="80">Shortage Qty.</th>
	                        <th width="80">Cartoon Qnty Qty.</th>
	                        <th width="80">Ex.Fact Value</th>
	                        <th width="80">Pre-cost CM</th>
	                        <th width="80">Actual CM</th>
	                        <th width="80">Variance</th>
	                        <th width="">Actual CM %</th>
                    	</tr>
                    </thead>
                </table>
	            <div style="width:1408px; overflow-y:scroll; overflow-x:hidden; max-height:300px;"  id="scroll_body" >
	            	<table width="1390" cellspacing="0" cellpadding="0"  border="1"  class="rpt_table" rules="all" id="table_body2">
						<?
						$i = 1;$excessQnty=$diff=0;
						foreach($details_data_arr as $job_no => $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$diff = $row["exfact_qnty"]-$row["po_qnty"];
							if($diff >0 )
							{
								$excessQnty = $diff;
								$ShortageQnty = "";
								$tot_excess += $excessQnty;
							}else if($diff<0){
								$diff = $row["po_qnty"]-$row["exfact_qnty"];
								$excessQnty = "";
								$ShortageQnty = $diff;
								$tot_shortage += $ShortageQnty;
							}else{
								$excessQnty = "";
								$ShortageQnty = "";
							}
							$style_variance = $row["pre_cost_cm"] - $row["actual_cost_cm"];
							$actual_cm_per =  ($row["actual_cost_cm"] / $row["ex_value"])*100;

							$tot_poQnty += $row["po_qnty"];
							$tot_exfact_qnty += $row["exfact_qnty"];
							$tot_carton += $row["carton_qnty"];
							$tot_ex_value += $row["ex_value"];
							$tot_pre_cost_cm += $row["pre_cost_cm"];
							$tot_actual_cost_cm += $row["actual_cost_cm"];
							$tot_style_variance += $style_variance;
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="40" align="center"><? echo $i;?></td>
								<td width="100" align="center"><? echo $buyer_arr[$row["buyer_name"]];?></td>
								<td width="100" align="center"><p><? echo $row["style_ref_no"];?></p></td>
								<td width="80" align="center"><p><? echo $job_no;?></p></td>
								<td width="60" align="center"><? echo number_format($row["set_smv"],4);?></td>
								<td width="100" align="center"><? echo change_date_format($maxDateChkArr[$job_no]["ex_date"]); ?></td>
								<td width="100" align="center"><? echo change_date_format($row["country_ship_date"]);?></td>
								<td width="80" align="right"><? echo number_format($row["po_qnty"]);?></td>
								<td width="80" align="right"><? echo number_format($row["exfact_qnty"]);?></td>
								<td width="80" align="right"><? echo number_format($excessQnty);?></td>
								<td width="80" align="right"><? echo number_format($ShortageQnty);?></td>
								<td width="80" align="right"><? echo number_format($row["carton_qnty"]);?></td>
								<td width="80" align="right"><? echo number_format($row["ex_value"],2);?></td>
								<td width="80" align="right"><? echo number_format($row["pre_cost_cm"],2);?></td>
								<td width="80" align="right"><? echo number_format($row["actual_cost_cm"],2);?></td>
								<td width="80" align="right"><? echo number_format($style_variance,2,'.','');?></td>
								<td width="" align="right"><? echo number_format($actual_cm_per,2,'.','');?></td>
							</tr>
						<?
							$i++;
							}
						?>
						<tfoot>
		                    <tr>
		                    	<th width="40">&nbsp;</th>
		                        <th width="100">&nbsp;</th>
		                        <th width="100">&nbsp;</th>
		                        <th width="60">&nbsp;</th>
		                        <th width="60">&nbsp;</th>
		                        <th width="200" colspan="2" style="text-align: center;">Total</th>
		                        <th width="80" align="right"><? echo number_format($tot_poQnty,2);?></th>
		                        <th width="80" align="right"><? echo $tot_exfact_qnty;?></th>
		                        <th width="80" align="right"><? echo $tot_excess;?></th>
		                        <th width="80" align="right"><? echo $tot_shortage;?></th>
		                        <th width="80" align="right"><? echo $tot_carton;?></th>
		                        <th width="80" align="right"><? echo number_format($tot_ex_value,2,'.','');?></th>
		                        <th width="80" align="right"><? echo number_format($tot_pre_cost_cm,2,'.','');?></th>
		                        <th width="80" align="right"><? echo number_format($tot_actual_cost_cm,2,'.','');?></th>
		                        <th width="80" align="right"><? echo number_format($tot_style_variance,2,'.','');?></th>
		                        <th align="right"><? //echo number_format($tot_style_variance,2,'.','');?></th>
		                    </tr>
		                </tfoot>
	            	</table>
	            </div>
            </div>
	<!-- Styles -->
<style>
#chartdiv {
	width		: 500;
	height		: 300px;
	font-size	: 11px;
}
</style>

<!-- Resources -->
<?
	$chart_data='['; $buyer = "[";
			foreach($master_data_arr as $buyer_id=>$val3)
			{
				$buyer .= "'".$buyer_arr[$buyer_id]."',";

			}
			$buyer=chop($buyer,',');
			$buyer .= "]";

			$chart_data.="{ name: 'PO Quantity', data:[";
			foreach($master_data_arr as $buyer_id=>$val3)
			{
				$chart_data.=$val3["po_qnty"].",";

			}
			$chart_data=chop($chart_data,',');
			$chart_data.="]},{ name: 'Exfactory Quantity',data:[";

			foreach($master_data_arr as $buyer_id=>$val3)
			{
				$chart_data.=$val3["exfact_qnty"].",";

			}
			$chart_data=chop($chart_data,',');
			$chart_data .= "]}]";

	?>
		<!-- Chart code -->

		<div id="chartdiv"></div>
        </div>
		<script>

		var buyer =  <? echo $buyer;?>;
		var chart_data = <? echo $chart_data;?>;
		$('#chartdiv').highcharts({

		    chart: {
		        type: 'column'
		    },
		    title: {
		        text: 'PO Quantity VS Exfactory Quantity'
		    },
		    /*subtitle: {
		        text: 'Source: WorldClimate.com'
		    },*/
		    xAxis: {
		        categories: buyer,
		        crosshair: true
		    },
		    yAxis: {
		        min: 0,
		        title: {
		            text: 'Quantity'
		        }
		    },
		    tooltip: {
		        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
		        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
		            '<td style="padding:0"><b>{point.y:.1f}</b></td></tr>',
		        footerFormat: '</table>',
		        shared: true,
		        useHTML: true
		    },
		    plotOptions: {
		        column: {
		            pointPadding: 0.2,
		            borderWidth: 0
		        }
		    },
		    series: chart_data

		});

		setFilterGrid("table_body2",-1);
		</script>

		<?
	}

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
	echo "$total_data####$filename####$reportType";
	exit();
}

if($action=="ex_date_popup")
{
	echo load_html_head_contents("Report Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	$ex_factory_date=str_replace("'","",$ex_factory_date);
	$ex_factory_date_ref=explode("_",$ex_factory_date);
	$exfact_date=explode("*",$ex_factory_date_ref[0]);
	//echo $ex_factory_date."***".$company_id."***".$order_id."***".$challan_id;
	$country_arr=return_library_array( "select id,country_name from  lib_country", "id", "country_name"  );
	?>
	<div style="width:100%" align="center">
		<fieldset style="width:500px">
        <div class="form_caption" align="center"><strong>Ex-Factory Date Details</strong></div><br />
            <table cellpadding="0" width="100%" class="rpt_table" rules="all" border="1">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="100">Date</th>
                        <th width="100">Challan</th>
                        <th width="100">Country</th>
                        <th width="100">Delv. Qty</th>
                        <th width="">Return Qty</th>
                     </tr>
                </thead>
                <tbody>
					<?
					if($challan_id!=""){$challan_id_cond= "and delivery_mst_id=$challan_id"; }else{$challan_id_cond= "";}
						$sql_res=sql_select("select b.po_break_down_id as po_id,

						sum(CASE WHEN b.entry_form=85 THEN b.ex_factory_qnty ELSE 0 END) as return_qnty
						from  pro_ex_factory_mst b  where  b.status_active=1 and b.is_deleted=0 group by b.po_break_down_id");
						$ex_factory_qty_arr=array();
						foreach($sql_res as $row)
						{

							$ex_factory_qty_arr[$row[csf('po_id')]]['return_qty']=$row[csf('return_qnty')];
						}

						$i=1;
						if($ex_factory_date_ref[1]==2)
						{
							$sql_qnty="Select po_break_down_id,ex_factory_date,challan_no,country_id,
							sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_qnty,
							sum(CASE WHEN entry_form=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_ret_qnty

							 from pro_ex_factory_mst where po_break_down_id=$order_id and status_active=1 and is_deleted=0  group by po_break_down_id,ex_factory_date,challan_no,country_id order by ex_factory_date ";
						}
						else
						{

							 $sql_qnty="Select po_break_down_id,ex_factory_date,challan_no,country_id,

							sum(CASE WHEN entry_form!=85 THEN ex_factory_qnty ELSE 0 END) as ex_factory_qnty

							from pro_ex_factory_mst where po_break_down_id=$order_id and status_active=1 and is_deleted=0 and ex_factory_date between  '$exfact_date[0]' and '$exfact_date[1]' $challan_id_cond group by po_break_down_id,ex_factory_date,challan_no,country_id order by ex_factory_date ";

							/*$sql_qnty="Select c.ex_factory_date, sum(c.ex_factory_qnty) as ex_factory_qnty,c.challan_no,c.country_id
							from wo_po_details_master a, wo_po_break_down b,  pro_ex_factory_mst c
							where a.job_no=b.job_no_mst and b.id=c.po_break_down_id and a.company_name=$company_id and c.po_break_down_id=$order_id and c.status_active=1 and c.is_deleted=0 and c.ex_factory_date between  '$exfact_date[0]' and '$exfact_date[1]'
							group by c.ex_factory_date,c.challan_no,c.country_id order by c.ex_factory_date ";*/
						}
						//echo $sql_qnty;
						$sql_dtls=sql_select($sql_qnty);
						foreach($sql_dtls as $row_real)
						{
							 if ($i%2==0) $bgcolor="#EFEFEF"; else $bgcolor="#FFFFFF";
							 if($ex_factory_date_ref[1]==2)
							 {
								 $return_qty=$row_real[csf("ex_factory_ret_qnty")];
							 }
							 else
							 {
								$return_qty=$ex_factory_qty_arr[$row_real[csf("po_break_down_id")]]['return_qty'];
							 }


							 ?>
								<tr bgcolor="<? echo $bgcolor; ?>" id="tr_<? echo $i; ?>">
									<td><? echo $i; ?></td>
									<td  align="center"><? echo change_date_format($row_real[csf("ex_factory_date")]); ?></td>
                                    <td ><? echo $row_real[csf("challan_no")]; ?></td>
                                    <td ><? echo $country_arr[$row_real[csf("country_id")]]; ?></td>
									<td width="100" align="right"><? echo number_format($row_real[csf("ex_factory_qnty")]-$return_qty,2); ?>&nbsp;</td>
                                    <td width="" align="right"><? echo number_format($return_qty,2); ?>&nbsp;</td>
								</tr>
							<?
							$total_ex_qnty+=$row_real[csf("ex_factory_qnty")];
							$total_return_ex_qnty+=$return_qty;
							$i++;
						}
                    ?>
                </tbody>
                <tfoot>
                	<tr>
                    	<th colspan="4" align="right"><strong>Total :</strong></th>
                        <th align="right"><? echo number_format($total_ex_qnty,2); ?></th>
                        <th align="right"><? echo number_format($total_return_ex_qnty,2); ?> </th>
                    </tr>
                    <tr>
                    	<th colspan="4" align="right"><strong>Total Balance:</strong></th>
                        <th align="right" colspan="2"><? echo number_format($total_ex_qnty-$total_return_ex_qnty,2); ?></th>

                    </tr>
                </tfoot>
            </table>
        </fieldset>
    </div>
    <?
}
disconnect($con);
?>
