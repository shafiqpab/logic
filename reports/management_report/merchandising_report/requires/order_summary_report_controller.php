<?
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../../includes/common.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

$buyer_arr=return_library_array("select id,short_name from  lib_buyer","id","short_name");
$buyer_ref_arr=return_library_array("select id,exporters_reference from  lib_buyer","id","exporters_reference");
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$lib_sub_dept_array=return_library_array( "select id,sub_department_name from lib_pro_sub_deparatment", "id", "sub_department_name"  );

function ordinal($number) {
    $ends = array('th','st','nd','rd','th','th','th','th','th','th');
    if ((($number % 100) >= 11) && (($number%100) <= 13))
        return $number. 'th';
    else
        return $number. $ends[$number % 10];
}

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 120, "SELECT buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company in($data) and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "- All Buyer -", $selected, "load_drop_down( 'requires/order_summary_report_controller', this.value, 'load_drop_down_sub_dep', 'sub_department_td' );" );
	exit();
}

if ($action=="load_drop_down_sub_dep")
{
	$data=explode("_",$data);
	echo create_drop_down( "cbo_sub_department", 120, "select id,sub_department_name from lib_pro_sub_deparatment where buyer_id in($data[0]) and status_active =1 and is_deleted=0 order by sub_department_name","id,sub_department_name", 1, "-- Select --", $selected, "" );
	exit();
}

if ($action=="load_drop_down_buyer_ref")
{
	$sql = "select distinct EXPORTERS_REFERENCE from lib_buyer  where status_active =1 and is_deleted=0 and id in($data) and exporters_reference is not null order by exporters_reference";
    $res = sql_select($sql);
    $ref_array = array();
    foreach ($res as $val) 
    {
    	$ref_array[$val['EXPORTERS_REFERENCE']] = $val['EXPORTERS_REFERENCE'];
    }

	echo create_drop_down( "cbo_buyer_ref", 120, $ref_array, "",1, "-- Select --", $selected, "" );
	exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	extract($_REQUEST);
	$company_name 	=str_replace("'","",$cbo_company_name);
	$buyer_name 	=str_replace("'","",$cbo_buyer_name);
	$buyer_ref 		=str_replace("'","",$cbo_buyer_ref);
	$sub_department =str_replace("'","",$cbo_sub_department);
	$shipping_status_id=str_replace("'","",$cbo_shipping_status);
	$date_type 		=str_replace("'","",$cbo_date_type);
	$from_year 		=str_replace("'","",$cbo_from_year);
	$from_month 	=str_replace("'","",$cbo_from_month);
	$to_year 		=str_replace("'","",$cbo_to_year);
	$to_month 		=str_replace("'","",$cbo_to_month);
	$reportType 	=str_replace("'","",$cbo_report_type);
	$show_value 	=str_replace("'","",$show_value);

	// =========================== MAKING QUERY COND ============================
	// echo cal_days_in_month(CAL_GREGORIAN, $to_month, $to_year); 
	// $dt = $from_year."-".$from_month;
	// echo date('d-m-Y',strtotime($dt));die();
	$shipping_status_chk=array(1,2);
	if(in_array($shipping_status_id,$shipping_status_chk))
	{
	$shipping_status_id="1,2";
	}
	//echo $shipping_status_id.'D';
	$sql_cond = "";
	$sql_cond .= ($company_name !=0) ? " and a.company_name = $company_name" : "";
	$sql_cond .= ($buyer_name !=0) ? " and a.buyer_name in($buyer_name)" : "";
	$sql_cond .= ($sub_department !=0) ? " and a.pro_sub_dep in($sub_department)" : "";
	$sql_cond .= ($shipping_status_id !="") ? " and b.shiping_status in($shipping_status_id)" : "";
	if($buyer_ref !=0)
	{
		$buyer_id_array = return_library_array("select id,id as ids from  lib_buyer where status_active=1 and exporters_reference='$buyer_ref'","id","ids");
	}
	// print_r($buyer_id_array);die();
	if(count($buyer_id_array)>0)
	{
		$buyer_id = implode(",", $buyer_id_array);
		$sql_cond .= " and a.buyer_name in($buyer_id)";
	}

	if($from_year !=0 && $from_month !=0 && $to_year !=0 && $to_month !=0)
	{
		$dt = $from_year."-".$from_month;
		$from_date = date('d-M-Y',strtotime($dt));

		$last_day = cal_days_in_month(CAL_GREGORIAN, $to_month, $to_year); 
		$dt = $to_year."-".$to_month."-".$last_day;
		$to_date = date('d-M-Y',strtotime($dt));

		if($date_type==1)
		{
			$sql_cond .= " and b.pub_shipment_date between '$from_date' and '$to_date'";
		}
		elseif ($date_type==2) 
		{
			$sql_cond .= " and b.txt_etd_ldd between '$from_date' and '$to_date'";
		}
		elseif ($date_type==3) 
		{
			$sql_cond .= " and b.shipment_date between '$from_date' and '$to_date'";
		}
	}

	// echo $sql_cond;die();

	function getMonthsInRange($startDate, $endDate) 
	{
		$months = array();
		while (strtotime($startDate) <= strtotime($endDate)) 
		{
		    // $months[] = array('year' => date('Y', strtotime($startDate)), 'month' => date('m', strtotime($startDate)), );
		    $months[strtoupper(date('M-Y', strtotime($startDate)))] = strtoupper(date('M-Y', strtotime($startDate)));
		    $startDate = date('01 M Y', strtotime($startDate.'+ 1 month')); // Set date to 1 so that new month is returned as the month changes.		    
		}

		return $months;
	}
	$date_range = getMonthsInRange($from_date,$to_date);

	$year_month_count = array();
	$month_year_width = array();
	$month_year_tot_width = 0;
	$colspan = 5;
	foreach ($date_range as $key => $val) 
	{
		$ex_data = explode("-", $val);
		$year_month_count[$ex_data[1]]++;
		$month_year_width[$ex_data[1]] += 80 ;
		$month_year_tot_width += 80;
		$colspan++;
	}
	// echo "<pre>";
	// print_r($month_year_width);
	// die();
	
	if($reportType==1) // sub dept wise
	{
		// =============================================== MAIN QUERY =========================================
		$sql = "SELECT a.BUYER_NAME,a.TOTAL_SET_QNTY,a.PRO_SUB_DEP,to_char(b.shipment_date,'MON-YYYY') as MONTH_YEAR, SUM(c.ORDER_QUANTITY) AS QTY,sum(c.ORDER_TOTAL) as VAL,c.ITEM_NUMBER_ID
		 from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c where a.id=b.job_id and a.id=c.job_id  $sql_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.id=c.po_break_down_id group by a.buyer_name,a.pro_sub_dep,b.id,a.total_set_qnty,b.shipment_date,c.item_number_id";//,a.total_set_qnty
		//echo $sql;die();

		$sql_res = sql_select($sql);
		if (count($sql_res) < 1)
		{
			echo '<div style="text-align: center;color: red;font-weight: bold;font-size: 20px;">Data not found.</div>';
			disconnect($con);
			die();
		}

		$sub_dep_dtls_array = array();
		$sub_dep_qty_array = array();
		$sub_dep_summary_array = array();
		$sub_dep_summary_qty_array = array();
		$item_summary_array = array();
		$item_summary_qty_array = array();
		foreach ($sql_res as $row)
		{
			// for sub dep wise summary
			$sub_dep_summary_array[$row['PRO_SUB_DEP']]['qty'] += $row['QTY'];
			$sub_dep_summary_array[$row['PRO_SUB_DEP']]['val'] += $row['VAL'];

			$sub_dep_summary_qty_array[$row['PRO_SUB_DEP']][$row['MONTH_YEAR']]['qty'] += $row['QTY'];
			$sub_dep_summary_qty_array[$row['PRO_SUB_DEP']][$row['MONTH_YEAR']]['val'] += $row['VAL'];

			// for item wise summary
			$item_summary_array[$row['ITEM_NUMBER_ID']]['qty'] += $row['QTY'];
			$item_summary_array[$row['ITEM_NUMBER_ID']]['val'] += $row['VAL'];

			$item_summary_qty_array[$row['ITEM_NUMBER_ID']][$row['MONTH_YEAR']]['qty'] += $row['QTY'];
			$item_summary_qty_array[$row['ITEM_NUMBER_ID']][$row['MONTH_YEAR']]['val'] += $row['VAL'];

			// for details
			$sub_dep_qty_array[$row['PRO_SUB_DEP']][$row['ITEM_NUMBER_ID']][$row['MONTH_YEAR']]['qty'] += $row['QTY'];
			$sub_dep_qty_array[$row['PRO_SUB_DEP']][$row['ITEM_NUMBER_ID']][$row['MONTH_YEAR']]['val'] += $row['VAL'];

			$sub_dep_dtls_array[$row['PRO_SUB_DEP']][$row['ITEM_NUMBER_ID']]['qty'] += $row['QTY'];
			$sub_dep_dtls_array[$row['PRO_SUB_DEP']][$row['ITEM_NUMBER_ID']]['val'] += $row['VAL'];

		}

		$rowspan = array();
		foreach ($sub_dep_dtls_array as $sub_key => $sub_value) 
		{
			foreach ($sub_value as $itm_key => $itm_value) 
			{
				$rowspan[$sub_key]++;
			}
		}

		$sub_dep_tbl_width = $month_year_tot_width + 300;
		$buyer_name_arr=return_library_array("select id,short_name from  lib_buyer where id in($buyer_name)","id","short_name");
		$buyer_ref_name_arr=return_library_array("select id,exporters_reference from  lib_buyer where id in($buyer_name)","id","exporters_reference");
		ob_start();
		?>

		    <fieldset style="width:100%;">
		    	<style type="text/css">
		    		.tr_odd td{font-size: 14px !important;}
		    		.tr_even td{font-size: 12px !important;}
		    	</style>
		    	<div id="heading_part">
			        <table width="<? echo $sub_dep_tbl_width+120;?>;" cellspacing="0"  align="center" >
			            <tr>
			                <td colspan="<? echo $colspan;?>" align="center" class="form_caption">
			                    <strong style="font-size:16px;">Company Name:<? echo  $company_library[$company_name] ;?></strong>
			                </td>
			            </tr>
			            <tr class="form_caption">
			                <td colspan="<? echo $colspan;?>" align="center" class="form_caption"> <strong style="font-size:15px;">Month Wise Quantity (Pcs) and Value</strong></td>
			            </tr>
			            <tr align="center">
			                <td colspan="<? echo $colspan;?>" align="center" class="form_caption"> <strong style="font-size:15px;">Date Type : <? echo ($date_type==1) ? "Shipment Date (RFI)": "Original Ship Date";?></strong></td>
			            </tr>
			            <tr align="center">
			                <td colspan="<? echo $colspan;?>" align="center" class="form_caption"> <strong style="font-size:15px;">Runtime : <? echo date("j F, Y h:i:s a");?></strong></td>
			            </tr>
			       	</table>
			       	<table width="<? echo $sub_dep_tbl_width;?>;" cellspacing="0"  align="center">
			            <tr class="form_caption">
			                <td colspan="<? echo $colspan;?>" align="center" class="form_caption"> 
			                	<div style="font-size:20px;text-align:left;">Buyer : <? echo implode(",", $buyer_name_arr);?></div>
			                	<div style="font-size:20px;text-align:left;">Buyer Ref. : <? echo implode(",", array_filter($buyer_ref_name_arr));?></div>
			                </td>
			            </tr>
			       	</table>
			    </div>
		<?
		if($show_value) // report generate with value
		{
			?>
		        <!-- ===================================================================================================/
		        / 										SUB DEP WISE SUMMARY PART									  	/
		        /==================================================================================================== -->
		        <div id="1st_part" style="width: <? echo $sub_dep_tbl_width;?>px;">
		            <table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1">
		            	<caption style="text-align:left;"><h2>Sub Dept wise Summary</h2></caption>
		                <thead>
		                	<tr>
			                    <th rowspan="2" width="100">Sub Dept.</th>
			                    <th rowspan="2" width="100">Quantity(pcs)/Value</th>
			                    <?
			                    foreach ($year_month_count as $key => $val) 
			                    {
			                    	?>
			                    	<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
			                    	<?
			                    }
			                    ?>
			                    <th rowspan="2" width="100">Grand Total</th>
			                </tr>
			                <tr>
			                	<?
			                	foreach ($date_range as $key => $val) 
			                	{
			                		?>
			                		<th width="80"><? echo $val;?></th>
			                		<?
			                	}
			                	?>
			                </tr>
		                </thead>
		                <tbody>
		                	<?
		                	$i=1;
		                	$vr_tot_qty = 0;
		                	$vr_tot_val = 0;
		                	$vr_gr_tot_qty = 0;
		                	$vr_gr_tot_val = 0;
		                	$mon_gr_tot_array = array();
		                	foreach ($sub_dep_summary_array as $sub_dep_key => $val) 
		                	{
		                		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		                		?>
		                		<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" class="tr_odd">
		                			<td valign="top" rowspan="2" title="<? echo $sub_dep_key;?>"><? echo strtoupper($lib_sub_dept_array[$sub_dep_key]);?></td>
		                			<td align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
		                			<?                			
		                			$hr_mon_tot_qty = 0;
				                	foreach ($date_range as $key => $val2) 
				                	{
				                		?>
				                		<td align="right"><? echo number_format($sub_dep_summary_qty_array[$sub_dep_key][$val2]['qty'],0);?></td>
				                		<?
				                		$hr_mon_tot_qty += $sub_dep_summary_qty_array[$sub_dep_key][$val2]['qty'];
				                		$vr_gr_tot_qty += $sub_dep_summary_qty_array[$sub_dep_key][$val2]['qty'];
				                		$mon_gr_tot_array[$val2]['qty'] += $sub_dep_summary_qty_array[$sub_dep_key][$val2]['qty'];
				                	}
				                	?>
				                	<td align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
		                		</tr>
		                		<tr class="tr_even">
		                		<td align="right">Value<? //echo number_format($val['val'],2);?></td>	
			                	<?
		                		$hr_mon_tot_val = 0;
			                	foreach ($date_range as $key2 => $val3) 
			                	{
			                		?>
			                		<td align="right">$ <? echo number_format($sub_dep_summary_qty_array[$sub_dep_key][$val3]['val'],2);?></td>
			                		<?
				                	$hr_mon_tot_val += $sub_dep_summary_qty_array[$sub_dep_key][$val3]['val'];
				                	$vr_gr_tot_val += $sub_dep_summary_qty_array[$sub_dep_key][$val3]['val'];
				                	$mon_gr_tot_array[$val3]['val'] += $sub_dep_summary_qty_array[$sub_dep_key][$val3]['val'];
			                	}
			                	?>
			                	<td align="right">$ <? echo number_format($hr_mon_tot_val,2);?></td>
			                </tr>
		                		<?
		                		$i++;

		                		$vr_tot_qty += $val['qty'];
		                		$vr_tot_val += $val['val'];
		                	}
		                	?>
		                </tbody>
		                <tfoot>
		                	<tr>
		                		<th rowspan="2">Grand Total</th>
		                		<th><? //echo number_format($vr_tot_qty,0);?></th>
		                		<?
			                	foreach ($date_range as $key2 => $val4) 
			                	{
			                		?>
			                		<th align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
			                		<?
			                	}
			                	?>
			                	<th><? echo number_format($vr_gr_tot_qty,0);?></th>
		                	</tr>
		                	<tr>
		                		<th><? //echo number_format($vr_tot_val,0);?></th>
		                		<?
			                	foreach ($date_range as $key2 => $val5) 
			                	{
			                		?>
			                		<th align="right">$ <? echo number_format($mon_gr_tot_array[$val5]['val'],2);?></th>
			                		<?
			                	}
			                	?>
			                	<th>$ <? echo number_format($vr_gr_tot_val,0);?></th>
		                	</tr>
		                </tfoot>
		            </table>
		        </div>        
		        <!-- ===================================================================================================/
		        / 											ITEM WISE SUMMARY PART									  	/
		        /==================================================================================================== -->
		        <div  id="2nd_part">
			        <div style="margin-top: 20px;width:<? echo $sub_dep_tbl_width+20;?>px;">            
			            
			            <table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
			            	<caption style="text-align:left;"><h2>Item wise Summary</h2></caption>
			                <thead>
			                	<tr>
				                    <th rowspan="2" width="100">Item</th>
				                    <th rowspan="2" width="100">Quantity(pcs)/Value</th>
				                    <?
				                    foreach ($year_month_count as $key => $val) 
				                    {
				                    	?>
				                    	<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
				                    	<?
				                    }
				                    ?>
				                    <th rowspan="2" width="100">Grand Total</th>
				                </tr>
				                <tr>
				                	<?
				                	foreach ($date_range as $key => $val) 
				                	{
				                		?>
				                		<th width="80"><? echo $val;?></th>
				                		<?
				                	}
				                	?>
				                </tr>
			                </thead>
			            </table>
			            <div style="width:<? echo $sub_dep_tbl_width+20;?>px;overflow-y:scroll;max-height:350px;" id="scroll_body_2">
				            <table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
				                <tbody>
				                	<?
				                	$k=1;
				                	$vr_tot_qty = 0;
				                	$vr_tot_val = 0;
				                	$vr_gr_tot_qty = 0;
				                	$vr_gr_tot_val = 0;
				                	$mon_gr_tot_array = array();
				                	foreach ($item_summary_array as $item_key => $val) 
				                	{
				                		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				                		?>
				                		<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" class="tr_odd">
				                			<td width="100" valign="top" rowspan="2"><? echo strtoupper($garments_item[$item_key]);?></td>
				                			<td width="100" align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
				                			<?                			
				                			$hr_mon_tot_qty = 0;
						                	foreach ($date_range as $key => $val2) 
						                	{
						                		?>
						                		<td width="80" align="right"><? echo number_format($item_summary_qty_array[$item_key][$val2]['qty'],0);?></td>
						                		<?
						                		$hr_mon_tot_qty += $item_summary_qty_array[$item_key][$val2]['qty'];
						                		$vr_gr_tot_qty += $item_summary_qty_array[$item_key][$val2]['qty'];
						                		$mon_gr_tot_array[$val2]['qty'] += $item_summary_qty_array[$item_key][$val2]['qty'];
						                	}
						                	?>
						                	<td width="100" align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
				                		</tr>
				                		<tr class="tr_even">
				                		<td width="100" align="right">Value<? //echo number_format($val['val'],2);?></td>	
					                	<?
				                		$hr_mon_tot_val = 0;
					                	foreach ($date_range as $key2 => $val3) 
					                	{
					                		?>
					                		<td width="80" align="right">$ <? echo number_format($item_summary_qty_array[$item_key][$val3]['val'],0);?></td>
					                		<?
						                	$hr_mon_tot_val += $item_summary_qty_array[$item_key][$val3]['val'];
						                	$vr_gr_tot_val += $item_summary_qty_array[$item_key][$val3]['val'];
						                	$mon_gr_tot_array[$val3]['val'] += $item_summary_qty_array[$item_key][$val3]['val'];
					                	}
					                	?>
					                	<td width="100" align="right">$ <? echo number_format($hr_mon_tot_val,0);?></td>
					                </tr>
				                		<?
				                		$i++;
				                		$k++;

				                		$vr_tot_qty += $val['qty'];
				                		$vr_tot_val += $val['val'];
				                	}
				                	?>
				                </tbody>
				            </table>
			            </div>
			            <table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
			                <tfoot>
			                	<tr>
			                		<th width="100" rowspan="2">Grand Total</th>
			                		<th width="100"><? //echo number_format($vr_tot_qty,0);?></th>
			                		<?
				                	foreach ($date_range as $key2 => $val4) 
				                	{
				                		?>
				                		<th width="80" align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
				                		<?
				                	}
				                	?>
				                	<th><? echo number_format($vr_gr_tot_qty,0);?></th>
			                	</tr>
			                	<tr>
			                		<th><? //echo number_format($vr_tot_val,2);?></th>
			                		<?
				                	foreach ($date_range as $key2 => $val5) 
				                	{
				                		?>
				                		<th width="80" align="right">$ <? echo number_format($mon_gr_tot_array[$val5]['val'],2);?></th>
				                		<?
				                	}
				                	?>
				                	<th width="100">$ <? echo number_format($vr_gr_tot_val,2);?></th>
			                	</tr>
			                </tfoot>
			            </table>            
			        </div>
		        </div>

		        <!-- ===================================================================================================/
		        / 												DETAILS PART									  		/
		        /==================================================================================================== -->
		        <div id="3rd_part">
			        <div style="margin-top: 20px;width:<? echo $sub_dep_tbl_width+120;?>px;">            
			            
			            <table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
			            	<caption style="text-align:left;"><h2>Details</h2></caption>
			                <thead>
			                	<tr>
				                    <th rowspan="2" width="100">Sub Dept.</th>
				                    <th rowspan="2" width="100">Item</th>
				                    <th rowspan="2" width="100">Quantity(pcs)/Value</th>
				                    <?
				                    foreach ($year_month_count as $key => $val) 
				                    {
				                    	?>
				                    	<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
				                    	<?
				                    }
				                    ?>
				                    <th rowspan="2" width="100">Grand Total</th>
				                </tr>
				                <tr>
				                	<?
				                	foreach ($date_range as $key => $val) 
				                	{
				                		?>
				                		<th width="80"><? echo $val;?></th>
				                		<?
				                	}
				                	?>
				                </tr>
			                </thead>
			            </table>
			            <div style="width:<? echo $sub_dep_tbl_width+120;?>px;overflow-y:scroll;max-height:350px;" id="scroll_body_3">
				            <table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
				                <tbody>
				                	<?
				                	$k=1;
				                	$vr_tot_qty = 0;
				                	$vr_tot_val = 0;
				                	$vr_gr_tot_qty = 0;
				                	$vr_gr_tot_val = 0;
				                	$mon_gr_tot_array = array();
				                	foreach ($sub_dep_dtls_array as $sub_dep_key => $sub_dep_val) 
				                	{
				                		foreach ($sub_dep_val as $item_key => $val) 
				                		{
					                		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					                		?>
					                		<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" class="tr_odd">
					                			<td width="100" valign="top" rowspan="2"><? echo strtoupper($lib_sub_dept_array[$sub_dep_key]);?></td>
					                			<td width="100" valign="top" rowspan="2"><? echo strtoupper($garments_item[$item_key]);?></td>
					                			<td width="100" align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
					                			<?                			
					                			$hr_mon_tot_qty = 0;
							                	foreach ($date_range as $key => $val2) 
							                	{
							                		?>
							                		<td width="80" align="right"><? echo number_format($sub_dep_qty_array[$sub_dep_key][$item_key][$val2]['qty'],0);?></td>
							                		<?
							                		$hr_mon_tot_qty += $sub_dep_qty_array[$sub_dep_key][$item_key][$val2]['qty'];
							                		$vr_gr_tot_qty += $sub_dep_qty_array[$sub_dep_key][$item_key][$val2]['qty'];
							                		$mon_gr_tot_array[$val2]['qty'] += $sub_dep_qty_array[$sub_dep_key][$item_key][$val2]['qty'];
							                	}
							                	?>
							                	<td width="100" align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
					                		</tr>
					                		<tr class="tr_even">
					                		<td width="100" align="right">Value<? //echo number_format($val['val'],2);?></td>	
						                	<?
					                		$hr_mon_tot_val = 0;
						                	foreach ($date_range as $key2 => $val3) 
						                	{
						                		?>
						                		<td width="80" align="right">$ <? echo number_format($sub_dep_qty_array[$sub_dep_key][$item_key][$val3]['val'],2);?></td>
						                		<?
							                	$hr_mon_tot_val += $sub_dep_qty_array[$sub_dep_key][$item_key][$val3]['val'];
							                	$vr_gr_tot_val += $sub_dep_qty_array[$sub_dep_key][$item_key][$val3]['val'];
							                	$mon_gr_tot_array[$val3]['val'] += $sub_dep_qty_array[$sub_dep_key][$item_key][$val3]['val'];
						                	}
						                	?>
						                	<td width="100" align="right">$ <? echo number_format($hr_mon_tot_val,2);?></td>
						                </tr>
					                		<?
					                		$i++;
					                		$k++;

					                		$vr_tot_qty += $val['qty'];
					                		$vr_tot_val += $val['val'];
					                	}
				                	}
				                	?>
				                </tbody>
				            </table>
			            </div>
			            <table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
			                <tfoot>
			                	<tr>
			                		<th width="100" rowspan="2"></th>
			                		<th width="100" rowspan="2">Grand Total</th>
			                		<th width="100"><? //echo number_format($vr_tot_qty,0);?></th>
			                		<?
				                	foreach ($date_range as $key2 => $val4) 
				                	{
				                		?>
				                		<th width="80" align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
				                		<?
				                	}
				                	?>
				                	<th><? echo number_format($vr_gr_tot_qty,0);?></th>
			                	</tr>
			                	<tr>
			                		<th><? //echo number_format($vr_tot_val,0);?></th>
			                		<?
				                	foreach ($date_range as $key2 => $val5) 
				                	{
				                		?>
				                		<th width="80" align="right">$ <? echo number_format($mon_gr_tot_array[$val5]['val'],2);?></th>
				                		<?
				                	}
				                	?>
				                	<th width="100">$ <? echo number_format($vr_gr_tot_val,2);?></th>
			                	</tr>
			                </tfoot>
			            </table>            
			        </div>
			    </div>
		    </fieldset>
			<?
		}
		else // report generate without value
		{
			?>
		        <!-- ===================================================================================================/
		        / 										SUB DEP WISE SUMMARY PART									  	/
		        /==================================================================================================== -->
		        <div id="1st_part">
		            <table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1">
		            	<caption style="text-align:left;"><h2>Sub Dept wise Summary</h2></caption>
		                <thead>
		                	<tr>
			                    <th rowspan="2" width="100">Sub Dept.</th>
			                    <th rowspan="2" width="100">Quantity(pcs)</th>
			                    <?
			                    foreach ($year_month_count as $key => $val) 
			                    {
			                    	?>
			                    	<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
			                    	<?
			                    }
			                    ?>
			                    <th rowspan="2" width="100">Grand Total</th>
			                </tr>
			                <tr>
			                	<?
			                	foreach ($date_range as $key => $val) 
			                	{
			                		?>
			                		<th width="80"><? echo $val;?></th>
			                		<?
			                	}
			                	?>
			                </tr>
		                </thead>
		                <tbody>
		                	<?
		                	$i=1;
		                	$vr_tot_qty = 0;
		                	$vr_tot_val = 0;
		                	$vr_gr_tot_qty = 0;
		                	$vr_gr_tot_val = 0;
		                	$mon_gr_tot_array = array();
		                	foreach ($sub_dep_summary_array as $sub_dep_key => $val) 
		                	{
		                		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		                		?>
		                		<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" class="tr_odd">
		                			<td><? echo strtoupper($lib_sub_dept_array[$sub_dep_key]);?></td>
		                			<td align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
		                			<?                			
		                			$hr_mon_tot_qty = 0;
				                	foreach ($date_range as $key => $val2) 
				                	{
				                		?>
				                		<td align="right"><? echo number_format($sub_dep_summary_qty_array[$sub_dep_key][$val2]['qty'],0);?></td>
				                		<?
				                		$hr_mon_tot_qty += $sub_dep_summary_qty_array[$sub_dep_key][$val2]['qty'];
				                		$vr_gr_tot_qty += $sub_dep_summary_qty_array[$sub_dep_key][$val2]['qty'];
				                		$mon_gr_tot_array[$val2]['qty'] += $sub_dep_summary_qty_array[$sub_dep_key][$val2]['qty'];
				                	}
				                	?>
				                	<td align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
		                		</tr>
		                		<?
		                		$i++;

		                		$vr_tot_qty += $val['qty'];
		                		$vr_tot_val += $val['val'];
		                	}
		                	?>
		                </tbody>
		                <tfoot>
		                	<tr>
		                		<th>Grand Total</th>
		                		<th><? //echo number_format($vr_tot_qty,0);?></th>
		                		<?
			                	foreach ($date_range as $key2 => $val4) 
			                	{
			                		?>
			                		<th align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
			                		<?
			                	}
			                	?>
			                	<th><? echo number_format($vr_gr_tot_qty,0);?></th>
		                	</tr>
		                </tfoot>
		            </table>
		        </div>        
		        <!-- ===================================================================================================/
		        / 											ITEM WISE SUMMARY PART									  	/
		        /==================================================================================================== -->
		        <div  id="2nd_part">
			        <div style="margin-top: 20px;width:<? echo $sub_dep_tbl_width+20;?>px;">            
			            
			            <table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
			            	<caption style="text-align:left;"><h2>Item wise Summary</h2></caption>
			                <thead>
			                	<tr>
				                    <th rowspan="2" width="100">Item</th>
				                    <th rowspan="2" width="100">Quantity(pcs)</th>
				                    <?
				                    foreach ($year_month_count as $key => $val) 
				                    {
				                    	?>
				                    	<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
				                    	<?
				                    }
				                    ?>
				                    <th rowspan="2" width="100">Grand Total</th>
				                </tr>
				                <tr>
				                	<?
				                	foreach ($date_range as $key => $val) 
				                	{
				                		?>
				                		<th width="80"><? echo $val;?></th>
				                		<?
				                	}
				                	?>
				                </tr>
			                </thead>
			            </table>
			            <div style="width:<? echo $sub_dep_tbl_width+20;?>px;overflow-y:scroll;max-height:350px;" id="scroll_body_2">
				            <table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
				                <tbody>
				                	<?
				                	$k=1;
				                	$vr_tot_qty = 0;
				                	$vr_tot_val = 0;
				                	$vr_gr_tot_qty = 0;
				                	$vr_gr_tot_val = 0;
				                	$mon_gr_tot_array = array();
				                	foreach ($item_summary_array as $item_key => $val) 
				                	{
				                		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				                		?>
				                		<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" class="tr_odd">
				                			<td width="100"><? echo strtoupper($garments_item[$item_key]);?></td>
				                			<td width="100" align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
				                			<?                			
				                			$hr_mon_tot_qty = 0;
						                	foreach ($date_range as $key => $val2) 
						                	{
						                		?>
						                		<td width="80" align="right"><? echo number_format($item_summary_qty_array[$item_key][$val2]['qty'],0);?></td>
						                		<?
						                		$hr_mon_tot_qty += $item_summary_qty_array[$item_key][$val2]['qty'];
						                		$vr_gr_tot_qty += $item_summary_qty_array[$item_key][$val2]['qty'];
						                		$mon_gr_tot_array[$val2]['qty'] += $item_summary_qty_array[$item_key][$val2]['qty'];
						                	}
						                	?>
						                	<td width="100" align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
				                		</tr>
				                		<?
				                		$i++;
				                		$k++;

				                		$vr_tot_qty += $val['qty'];
				                		$vr_tot_val += $val['val'];
				                	}
				                	?>
				                </tbody>
				            </table>
			            </div>
			            <table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
			                <tfoot>
			                	<tr>
			                		<th width="100">Grand Total</th>
			                		<th width="100"><? //echo number_format($vr_tot_qty,0);?></th>
			                		<?
				                	foreach ($date_range as $key2 => $val4) 
				                	{
				                		?>
				                		<th width="80" align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
				                		<?
				                	}
				                	?>
				                	<th><? echo number_format($vr_gr_tot_qty,0);?></th>
			                	</tr>
			                </tfoot>
			            </table>            
			        </div>
		        </div>
		        <!-- ===================================================================================================/
		        / 												DETAILS PART									  		/
		        /==================================================================================================== -->
		        <div  id="3rd_part">
			        <div style="margin-top: 20px;width:<? echo $sub_dep_tbl_width+120;?>px;">            
			            
			            <table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
			            	<caption style="text-align:left;"><h2>Details</h2></caption>
			                <thead>
			                	<tr>
				                    <th rowspan="2" width="100">Sub Dept.</th>
				                    <th rowspan="2" width="100">Item</th>
				                    <th rowspan="2" width="100">Quantity(pcs)</th>
				                    <?
				                    foreach ($year_month_count as $key => $val) 
				                    {
				                    	?>
				                    	<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
				                    	<?
				                    }
				                    ?>
				                    <th rowspan="2" width="100">Grand Total</th>
				                </tr>
				                <tr>
				                	<?
				                	foreach ($date_range as $key => $val) 
				                	{
				                		?>
				                		<th width="80"><? echo $val;?></th>
				                		<?
				                	}
				                	?>
				                </tr>
			                </thead>
			            </table>
			            <div style="width:<? echo $sub_dep_tbl_width+120;?>px;overflow-y:scroll;max-height:350px;" id="scroll_body_3">
				            <table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
				                <tbody>
				                	<?
				                	$k=1;
				                	$vr_tot_qty = 0;
				                	$vr_tot_val = 0;
				                	$vr_gr_tot_qty = 0;
				                	$vr_gr_tot_val = 0;
				                	$mon_gr_tot_array = array();
				                	foreach ($sub_dep_dtls_array as $sub_dep_key => $sub_dep_val) 
				                	{
				                		foreach ($sub_dep_val as $item_key => $val) 
				                		{
					                		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					                		?>
					                		<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" class="tr_odd">
					                			<td width="100"><? echo strtoupper($lib_sub_dept_array[$sub_dep_key]);?></td>
					                			<td width="100"><? echo strtoupper($garments_item[$item_key]);?></td>
					                			<td width="100" align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
					                			<?                			
					                			$hr_mon_tot_qty = 0;
							                	foreach ($date_range as $key => $val2) 
							                	{
							                		?>
							                		<td width="80" align="right"><? echo number_format($sub_dep_qty_array[$sub_dep_key][$item_key][$val2]['qty'],0);?></td>
							                		<?
							                		$hr_mon_tot_qty += $sub_dep_qty_array[$sub_dep_key][$item_key][$val2]['qty'];
							                		$vr_gr_tot_qty += $sub_dep_qty_array[$sub_dep_key][$item_key][$val2]['qty'];
							                		$mon_gr_tot_array[$val2]['qty'] += $sub_dep_qty_array[$sub_dep_key][$item_key][$val2]['qty'];
							                	}
							                	?>
							                	<td width="100" align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
					                		</tr>
					                		<?
					                		$i++;
					                		$k++;

					                		$vr_tot_qty += $val['qty'];
					                		$vr_tot_val += $val['val'];
					                	}
				                	}
				                	?>
				                </tbody>
				            </table>
			            </div>
			            <table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
			                <tfoot>
			                	<tr>
			                		<th width="100"></th>
			                		<th width="100">Grand Total</th>
			                		<th width="100"><? //echo number_format($vr_tot_qty,0);?></th>
			                		<?
				                	foreach ($date_range as $key2 => $val4) 
				                	{
				                		?>
				                		<th width="80" align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
				                		<?
				                	}
				                	?>
				                	<th><? echo number_format($vr_gr_tot_qty,0);?></th>
			                	</tr>
			                </tfoot>
			            </table>            
			        </div>
			    </div>
		    </fieldset>
			<?
		}
	}
	else // buyer wise 
	{		
		// =============================================== MAIN QUERY =========================================
		$sql = "SELECT a.BUYER_NAME,a.PRO_SUB_DEP,a.TOTAL_SET_QNTY,to_char(b.shipment_date,'MON-YYYY') as MONTH_YEAR, SUM(c.ORDER_QUANTITY) AS QTY,sum(c.ORDER_TOTAL) as VAL,c.ITEM_NUMBER_ID
		 from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c where a.id=b.job_id and a.id=c.job_id  $sql_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.id=c.po_break_down_id group by a.buyer_name,a.pro_sub_dep,b.id,b.shipment_date,c.item_number_id,a.total_set_qnty";
		  //echo $sql;die();

		$sql_res = sql_select($sql);
		if (count($sql_res) < 1)
		{
			echo '<div style="text-align: center;color: red;font-weight: bold;font-size: 20px;">Data not found.</div>';
			disconnect($con);
			die();
		}

		$sub_dep_dtls_array = array();
		$sub_dep_qty_array = array();
		$sub_dep_summary_array = array();
		$sub_dep_summary_qty_array = array();
		$item_summary_array = array();
		$item_summary_qty_array = array();
		foreach ($sql_res as $row)
		{
			// for summary
			$sub_dep_summary_array[$buyer_ref_arr[$row['BUYER_NAME']]][$row['BUYER_NAME']]['qty'] += $row['QTY'];
			$sub_dep_summary_array[$buyer_ref_arr[$row['BUYER_NAME']]][$row['BUYER_NAME']]['val'] += $row['VAL'];

			$sub_dep_summary_qty_array[$row['BUYER_NAME']][$row['MONTH_YEAR']]['qty'] += $row['QTY'];
			$sub_dep_summary_qty_array[$row['BUYER_NAME']][$row['MONTH_YEAR']]['val'] += $row['VAL'];

			// for item wise summary
			$item_summary_array[$row['ITEM_NUMBER_ID']]['qty'] += $row['QTY'];
			$item_summary_array[$row['ITEM_NUMBER_ID']]['val'] += $row['VAL'];

			$item_summary_qty_array[$row['ITEM_NUMBER_ID']][$row['MONTH_YEAR']]['qty'] += $row['QTY'];
			$item_summary_qty_array[$row['ITEM_NUMBER_ID']][$row['MONTH_YEAR']]['val'] += $row['VAL'];

			// for details
			$sub_dep_qty_array[$row['BUYER_NAME']][$row['ITEM_NUMBER_ID']][$row['MONTH_YEAR']]['qty'] += $row['QTY'];
			$sub_dep_qty_array[$row['BUYER_NAME']][$row['ITEM_NUMBER_ID']][$row['MONTH_YEAR']]['val'] += $row['VAL'];

			$sub_dep_dtls_array[$buyer_ref_arr[$row['BUYER_NAME']]][$row['BUYER_NAME']][$row['ITEM_NUMBER_ID']]['qty'] += $row['QTY'];
			$sub_dep_dtls_array[$buyer_ref_arr[$row['BUYER_NAME']]][$row['BUYER_NAME']][$row['ITEM_NUMBER_ID']]['val'] += $row['VAL'];

		}

		$rowspan = array();
		foreach ($sub_dep_summary_array as $ref_key => $ref_value) 
		{
			foreach ($ref_value as $buyer_key => $buyer_value) 
			{
				$rowspan[$ref_key]++;
			}
		}

		$rowspan_dtls = array();
		$rowspan_buyer = array();
		foreach ($sub_dep_dtls_array as $ref_key => $ref_value) 
		{
			foreach ($ref_value as $buyer_key => $buyer_value) 
			{
				foreach ($buyer_value as $item_key => $item_value) 
				{
					$rowspan_dtls[$ref_key]++;
					$rowspan_buyer[$ref_key][$buyer_key]++;
				}
			}
		}
		// echo "<pre>";print_r($rowspan_buyer);die();

		$sub_dep_tbl_width = $month_year_tot_width + 400;
		ob_start();
		?>

		    <fieldset style="width:100%;">
		    	<style type="text/css">
		    		.tr_odd td{font-size: 12px !important;}
		    		.tr_even td{font-size: 14px !important;}
		    	</style>
		        <table width="<? echo $sub_dep_tbl_width+120;?>;" cellspacing="0"  align="center">
		            <tr>
		                <td colspan="<? echo $colspan;?>" align="center" class="form_caption">
		                    <strong style="font-size:16px;">Company Name:<? echo  $company_library[$company_name] ;?></strong>
		                </td>
		            </tr>
		            <tr class="form_caption">
		                <td colspan="<? echo $colspan;?>" align="center" class="form_caption"> <strong style="font-size:15px;">Month Wise Quantity (Pcs) and Value</strong></td>
		            </tr>
		            <tr align="center">
		                <td colspan="<? echo $colspan;?>" align="center" class="form_caption"> <strong style="font-size:15px;">Date Type : <? echo ($date_type==1) ? "Shipment Date (RFI)": "Original Ship Date";?></strong></td>
		            </tr>
			            <tr align="center">
			                <td colspan="<? echo $colspan;?>" align="center" class="form_caption"> <strong style="font-size:15px;">Runtime : <? echo date("j F, Y h:i:s a");?></strong></td>
			            </tr>
		       	</table>
		<?
		if($show_value) // report generate with value
		{
			?>
		        <!-- ===================================================================================================/
		        / 											BUYER SUMMARY PART 										  	/
		        /==================================================================================================== -->
		        <div id="1st_part">
			        <div style="width:<? echo $sub_dep_tbl_width+20;?>px;">  
			            <table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
			            	<caption style="text-align:left;"><h2>Buyer wise Summary</h2></caption>
			                <thead>
			                	<tr>
				                    <th rowspan="2" width="100">Buyer Ref.</th>
				                    <th rowspan="2" width="100">Buyer</th>
				                    <th rowspan="2" width="100">Quantity(pcs)/Value</th>
				                    <?
				                    foreach ($year_month_count as $key => $val) 
				                    {
				                    	?>
				                    	<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
				                    	<?
				                    }
				                    ?>
				                    <th rowspan="2" width="100">Grand Total</th>
				                </tr>
				                <tr>
				                	<?
				                	foreach ($date_range as $key => $val) 
				                	{
				                		?>
				                		<th width="80"><? echo $val;?></th>
				                		<?
				                	}
				                	?>
				                </tr>
			                </thead>
			            </table>
			            <div style="width:<? echo $sub_dep_tbl_width+20;?>px;overflow-y:scroll;max-height:350px;" id="scroll_body_1">
				            <table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
				                <tbody>
				                	<?
				                	$i=1;
				                	$vr_tot_qty = 0;
				                	$vr_tot_val = 0;
				                	$vr_gr_tot_qty = 0;
				                	$vr_gr_tot_val = 0;
				                	$mon_gr_tot_array = array();
				                	foreach ($sub_dep_summary_array as $ref_key => $ref_val) 
				                	{
				                		$ref = 0;
				                		foreach ($ref_val as $buyer_key => $val) 
				                		{
					                		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					                		?>
					                		<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" class="tr_even">
					                			<? if($ref==0){?>
					                			<td valign="top" width="100"  rowspan="<? echo $rowspan[$ref_key]*2;?>"><? echo strtoupper($buyer_ref_arr[$buyer_key]);?></td>
					                			<? $ref++;}?>
					                			<td valign="top" width="100" rowspan="2"><? echo strtoupper($buyer_arr[$buyer_key]);?></td>
					                			<td width="100" align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
					                			<?                			
					                			$hr_mon_tot_qty = 0;
							                	foreach ($date_range as $key => $val2) 
							                	{
							                		?>
							                		<td width="80" align="right"><? echo number_format($sub_dep_summary_qty_array[$buyer_key][$val2]['qty'],0);?></td>
							                		<?
							                		$hr_mon_tot_qty += $sub_dep_summary_qty_array[$buyer_key][$val2]['qty'];
							                		$vr_gr_tot_qty += $sub_dep_summary_qty_array[$buyer_key][$val2]['qty'];
							                		$mon_gr_tot_array[$val2]['qty'] += $sub_dep_summary_qty_array[$buyer_key][$val2]['qty'];
							                	}
							                	?>
							                	<td width="100" align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
					                		</tr>
					                		<tr class="tr_odd">
					                		<td width="100" align="right">Value<? //echo number_format($val['val'],2);?></td>	
						                	<?
					                		$hr_mon_tot_val = 0;
						                	foreach ($date_range as $key2 => $val3) 
						                	{
						                		?>
						                		<td width="80" align="right">$ <? echo number_format($sub_dep_summary_qty_array[$buyer_key][$val3]['val'],2);?></td>
						                		<?
							                	$hr_mon_tot_val += $sub_dep_summary_qty_array[$buyer_key][$val3]['val'];
							                	$vr_gr_tot_val += $sub_dep_summary_qty_array[$buyer_key][$val3]['val'];
							                	$mon_gr_tot_array[$val3]['val'] += $sub_dep_summary_qty_array[$buyer_key][$val3]['val'];
						                	}
						                	?>
						                	<td width="100" align="right">$ <? echo number_format($hr_mon_tot_val,2);?></td>
						                </tr>
					                		<?
					                		$i++;

					                		$vr_tot_qty += $val['qty'];
					                		$vr_tot_val += $val['val'];
					                	}
				                	}
				                	?>
				                </tbody>	                
				            </table>
				        </div>
			            <table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
			                <tfoot>
			                	<tr>
			                		<th width="100" rowspan="2"></th>
			                		<th width="100" rowspan="2">Grand Total</th>
			                		<th width="100"><? //echo number_format($vr_tot_qty,0);?></th>
			                		<?
				                	foreach ($date_range as $key2 => $val4) 
				                	{
				                		?>
				                		<th width="80" align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
				                		<?
				                	}
				                	?>
				                	<th><? echo number_format($vr_gr_tot_qty,0);?></th>
			                	</tr>
			                	<tr>
			                		<th><? //echo number_format($vr_tot_val,2);?></th>
			                		<?
				                	foreach ($date_range as $key2 => $val5) 
				                	{
				                		?>
				                		<th width="80" align="right">$ <? echo number_format($mon_gr_tot_array[$val5]['val'],2);?></th>
				                		<?
				                	}
				                	?>
				                	<th>$ <? echo number_format($vr_gr_tot_val,2);?></th>
			                	</tr>
			                </tfoot>
			            </table>
			        </div>        
		        </div>
		        <!-- ===================================================================================================/
		        / 											ITEM WISE SUMMARY PART									  	/
		        /==================================================================================================== -->
		        <div id="2nd_part">
			        <div style="margin-top: 20px;width:<? echo $sub_dep_tbl_width+20;?>px;" id="">            
			            
			            <table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
			            	<caption style="text-align:left;"><h2>Item wise Summary</h2></caption>
			                <thead>
			                	<tr>
				                    <th rowspan="2" width="100">Item</th>
				                    <th rowspan="2" width="100">Quantity(pcs)/Value</th>
				                    <?
				                    foreach ($year_month_count as $key => $val) 
				                    {
				                    	?>
				                    	<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
				                    	<?
				                    }
				                    ?>
				                    <th rowspan="2" width="100">Grand Total</th>
				                </tr>
				                <tr>
				                	<?
				                	foreach ($date_range as $key => $val) 
				                	{
				                		?>
				                		<th width="80"><? echo $val;?></th>
				                		<?
				                	}
				                	?>
				                </tr>
			                </thead>
			            </table>
			            <div style="width:<? echo $sub_dep_tbl_width+20;?>px;overflow-y:scroll;max-height:350px;" id="scroll_body_2">
				            <table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
				                <tbody>
				                	<?
				                	$k=1;
				                	$vr_tot_qty = 0;
				                	$vr_tot_val = 0;
				                	$vr_gr_tot_qty = 0;
				                	$vr_gr_tot_val = 0;
				                	$mon_gr_tot_array = array();
				                	foreach ($item_summary_array as $item_key => $val) 
				                	{
				                		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				                		?>
				                		<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" class="tr_even">
				                			<td valign="top" width="100" rowspan="2"><? echo $garments_item[$item_key];?></td>
				                			<td width="100" align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
				                			<?                			
				                			$hr_mon_tot_qty = 0;
						                	foreach ($date_range as $key => $val2) 
						                	{
						                		?>
						                		<td width="80" align="right"><? echo number_format($item_summary_qty_array[$item_key][$val2]['qty'],0);?></td>
						                		<?
						                		$hr_mon_tot_qty += $item_summary_qty_array[$item_key][$val2]['qty'];
						                		$vr_gr_tot_qty += $item_summary_qty_array[$item_key][$val2]['qty'];
						                		$mon_gr_tot_array[$val2]['qty'] += $item_summary_qty_array[$item_key][$val2]['qty'];
						                	}
						                	?>
						                	<td width="100" align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
				                		</tr>
				                		<tr class="tr_odd">
				                		<td width="100" align="right">Value<? //echo number_format($val['val'],2);?></td>	
					                	<?
				                		$hr_mon_tot_val = 0;
					                	foreach ($date_range as $key2 => $val3) 
					                	{
					                		?>
					                		<td width="80" align="right">$ <? echo number_format($item_summary_qty_array[$item_key][$val3]['val'],2);?></td>
					                		<?
						                	$hr_mon_tot_val += $item_summary_qty_array[$item_key][$val3]['val'];
						                	$vr_gr_tot_val += $item_summary_qty_array[$item_key][$val3]['val'];
						                	$mon_gr_tot_array[$val3]['val'] += $item_summary_qty_array[$item_key][$val3]['val'];
					                	}
					                	?>
					                	<td width="100" align="right">$ <? echo number_format($hr_mon_tot_val,2);?></td>
					                </tr>
				                		<?
				                		$i++;
				                		$k++;

				                		$vr_tot_qty += $val['qty'];
				                		$vr_tot_val += $val['val'];
				                	}
				                	?>
				                </tbody>
				            </table>
			            </div>
			            <table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
			                <tfoot>
			                	<tr>
			                		<th width="100" rowspan="2">Grand Total</th>
			                		<th width="100"><? //echo number_format($vr_tot_qty,0);?></th>
			                		<?
				                	foreach ($date_range as $key2 => $val4) 
				                	{
				                		?>
				                		<th width="80" align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
				                		<?
				                	}
				                	?>
				                	<th><? echo number_format($vr_gr_tot_qty,0);?></th>
			                	</tr>
			                	<tr>
			                		<th><? //echo number_format($vr_tot_val,2);?></th>
			                		<?
				                	foreach ($date_range as $key2 => $val5) 
				                	{
				                		?>
				                		<th width="80" align="right">$ <? echo number_format($mon_gr_tot_array[$val5]['val'],2);?></th>
				                		<?
				                	}
				                	?>
				                	<th width="100">$ <? echo number_format($vr_gr_tot_val,2);?></th>
			                	</tr>
			                </tfoot>
			            </table>            
			        </div>
			    </div>

		        <!-- ===================================================================================================/
		        / 												DETAILS PART									  		/
		        /==================================================================================================== -->
		        <div id="3rd_part">
			        <div style="margin-top: 20px;width:<? echo $sub_dep_tbl_width+120;?>px;" id="">            
			            
			            <table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
			            	<caption style="text-align:left;"><h2>Details</h2></caption>
			                <thead>
			                	<tr>
				                    <th rowspan="2" width="100">Buyer Ref.</th>
				                    <th rowspan="2" width="100">Buyer</th>
				                    <th rowspan="2" width="100">Item</th>
				                    <th rowspan="2" width="100">Quantity(pcs)/Value</th>
				                    <?
				                    foreach ($year_month_count as $key => $val) 
				                    {
				                    	?>
				                    	<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
				                    	<?
				                    }
				                    ?>
				                    <th rowspan="2" width="100">Grand Total</th>
				                </tr>
				                <tr>
				                	<?
				                	foreach ($date_range as $key => $val) 
				                	{
				                		?>
				                		<th width="80"><? echo $val;?></th>
				                		<?
				                	}
				                	?>
				                </tr>
			                </thead>
			            </table>
			            <div style="width:<? echo $sub_dep_tbl_width+120;?>px;overflow-y:scroll;max-height:350px;" id="scroll_body_3">
				            <table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
				                <tbody>
				                	<?
				                	$k=1;
				                	$vr_tot_qty = 0;
				                	$vr_tot_val = 0;
				                	$vr_gr_tot_qty = 0;
				                	$vr_gr_tot_val = 0;
				                	$mon_gr_tot_array = array();
				                	foreach ($sub_dep_dtls_array as $ref_key => $ref_val) 
				                	{	
				                		$ref = 0;
				                		foreach ($ref_val as $buyer_key => $buyer_val) 
				                		{
				                			$byr = 0;
					                		foreach ($buyer_val as $item_key => $val) 
					                		{
						                		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						                		?>
						                		<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" class="tr_even">
						                			<? if($ref==0){?>
						                			<td valign="top" width="100" rowspan="<? echo $rowspan_dtls[$ref_key]*2;?>"><? echo strtoupper($buyer_ref_arr[$buyer_key]);?></td>
						                			<? $ref++;} if($byr==0){?>
						                			<td valign="top" width="100" rowspan="<? echo $rowspan_buyer[$ref_key][$buyer_key]*2;?>"><? echo strtoupper($buyer_arr[$buyer_key]);?></td>
						                			<? $byr++;}?>
						                			<td width="100" rowspan="2"><? echo $garments_item[$item_key];?></td>
						                			<td width="100" align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
						                			<?                			
						                			$hr_mon_tot_qty = 0;
								                	foreach ($date_range as $key => $val2) 
								                	{
								                		?>
								                		<td width="80" align="right"><? echo number_format($sub_dep_qty_array[$buyer_key][$item_key][$val2]['qty'],0);?></td>
								                		<?
								                		$hr_mon_tot_qty += $sub_dep_qty_array[$buyer_key][$item_key][$val2]['qty'];
								                		$vr_gr_tot_qty += $sub_dep_qty_array[$buyer_key][$item_key][$val2]['qty'];
								                		$mon_gr_tot_array[$val2]['qty'] += $sub_dep_qty_array[$buyer_key][$item_key][$val2]['qty'];
								                	}
								                	?>
								                	<td width="100" align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
						                		</tr>
						                		<tr class="tr_odd">
						                		<td width="100" align="right">Value<? //echo number_format($val['val'],2);?></td>	
							                	<?
						                		$hr_mon_tot_val = 0;
							                	foreach ($date_range as $key2 => $val3) 
							                	{
							                		?>
							                		<td width="80" align="right">$ <? echo number_format($sub_dep_qty_array[$buyer_key][$item_key][$val3]['val'],2);?></td>
							                		<?
								                	$hr_mon_tot_val += $sub_dep_qty_array[$buyer_key][$item_key][$val3]['val'];
								                	$vr_gr_tot_val += $sub_dep_qty_array[$buyer_key][$item_key][$val3]['val'];
								                	$mon_gr_tot_array[$val3]['val'] += $sub_dep_qty_array[$buyer_key][$item_key][$val3]['val'];
							                	}
							                	?>
							                	<td width="100" align="right">$ <? echo number_format($hr_mon_tot_val,2);?></td>
							                </tr>
						                		<?
						                		$i++;
						                		$k++;

						                		$vr_tot_qty += $val['qty'];
						                		$vr_tot_val += $val['val'];
						                	}
					                	}
				                	}
				                	?>
				                </tbody>
				            </table>
			            </div>
			            <table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
			                <tfoot>
			                	<tr>
			                		<th width="100" rowspan="2"></th>
			                		<th width="100" rowspan="2"></th>
			                		<th width="100" rowspan="2">Grand Total</th>
			                		<th width="100"><? //echo number_format($vr_tot_qty,0);?></th>
			                		<?
				                	foreach ($date_range as $key2 => $val4) 
				                	{
				                		?>
				                		<th width="80" align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
				                		<?
				                	}
				                	?>
				                	<th><? echo number_format($vr_gr_tot_qty,0);?></th>
			                	</tr>
			                	<tr>
			                		<th><? //echo number_format($vr_tot_val,2);?></th>
			                		<?
				                	foreach ($date_range as $key2 => $val5) 
				                	{
				                		?>
				                		<th width="80" align="right">$ <? echo number_format($mon_gr_tot_array[$val5]['val'],2);?></th>
				                		<?
				                	}
				                	?>
				                	<th width="100">$ <? echo number_format($vr_gr_tot_val,2);?></th>
			                	</tr>
			                </tfoot>
			            </table>            
			        </div>
			    </div>
		    </fieldset>
			<?
		}
		else // report generate without value
		{

			?>
		        <!-- ===================================================================================================/
		        / 										BUYER WISE SUMMARY PART										  	/
		        /==================================================================================================== -->
		        <div id="1st_part">
			        <div style="width:<? echo $sub_dep_tbl_width+20;?>px;">  
			            <table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
			            	<caption style="text-align:left;"><h2>Buyer wise Summary</h2></caption>
			                <thead>
			                	<tr>
				                    <th rowspan="2" width="100">Buyer Ref.</th>
				                    <th rowspan="2" width="100">Buyer</th>
				                    <th rowspan="2" width="100">Quantity(pcs)</th>
				                    <?
				                    foreach ($year_month_count as $key => $val) 
				                    {
				                    	?>
				                    	<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
				                    	<?
				                    }
				                    ?>
				                    <th rowspan="2" width="100">Grand Total</th>
				                </tr>
				                <tr>
				                	<?
				                	foreach ($date_range as $key => $val) 
				                	{
				                		?>
				                		<th width="80"><? echo $val;?></th>
				                		<?
				                	}
				                	?>
				                </tr>
			                </thead>
			            </table>
			            <div style="width:<? echo $sub_dep_tbl_width+20;?>px;overflow-y:scroll;max-height:350px;" id="scroll_body_1">
				            <table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
				                <tbody>
				                	<?
				                	$i=1;
				                	$vr_tot_qty = 0;
				                	$vr_tot_val = 0;
				                	$vr_gr_tot_qty = 0;
				                	$vr_gr_tot_val = 0;
				                	$mon_gr_tot_array = array();
				                	foreach ($sub_dep_summary_array as $ref_key => $ref_val) 
				                	{	
				                		$ref = 0;
				                		foreach ($ref_val as $buyer_key => $val) 
				                		{
					                		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					                		?>
					                		<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" style="font-size:12px">
					                			<? if($ref==0){?>
					                			<td valign="top" width="100" rowspan="<? echo $rowspan[$ref_key];?>" ><? echo strtoupper($buyer_ref_arr[$buyer_key]);?></td>
					                			<? $ref++;}?>
					                			<td width="100"><? echo strtoupper($buyer_arr[$buyer_key]);?></td>
					                			<td width="100" align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
					                			<?                			
					                			$hr_mon_tot_qty = 0;
							                	foreach ($date_range as $key => $val2) 
							                	{
							                		?>
							                		<td width="80" align="right"><? echo number_format($sub_dep_summary_qty_array[$buyer_key][$val2]['qty'],0);?></td>
							                		<?
							                		$hr_mon_tot_qty += $sub_dep_summary_qty_array[$buyer_key][$val2]['qty'];
							                		$vr_gr_tot_qty += $sub_dep_summary_qty_array[$buyer_key][$val2]['qty'];
							                		$mon_gr_tot_array[$val2]['qty'] += $sub_dep_summary_qty_array[$buyer_key][$val2]['qty'];
							                	}
							                	?>
							                	<td width="100" align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
					                		</tr>
					                		<?
					                		$i++;

					                		$vr_tot_qty += $val['qty'];
					                		$vr_tot_val += $val['val'];
					                	}
				                	}
				                	?>
				                </tbody>	                
				            </table>
				        </div>
			            <table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
			                <tfoot>
			                	<tr>
			                		<th width="100"></th>
			                		<th width="100">Grand Total</th>
			                		<th width="100"><? //echo number_format($vr_tot_qty,0);?></th>
			                		<?
				                	foreach ($date_range as $key2 => $val4) 
				                	{
				                		?>
				                		<th width="80" align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
				                		<?
				                	}
				                	?>
				                	<th><? echo number_format($vr_gr_tot_qty,0);?></th>
			                	</tr>
			                </tfoot>
			            </table>
			        </div>        
		        </div>
		        <!-- ===================================================================================================/
		        / 											ITEM WISE SUMMARY PART									  	/
		        /==================================================================================================== -->
		        <div id="2nd_part">
			        <div style="margin-top: 20px;width:<? echo $sub_dep_tbl_width+20;?>px;" id="">            
			            
			            <table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
			            	<caption style="text-align:left;"><h2>Item wise Summary</h2></caption>
			                <thead>
			                	<tr>
				                    <th rowspan="2" width="100">Item</th>
				                    <th rowspan="2" width="100">Quantity(pcs)</th>
				                    <?
				                    foreach ($year_month_count as $key => $val) 
				                    {
				                    	?>
				                    	<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
				                    	<?
				                    }
				                    ?>
				                    <th rowspan="2" width="100">Grand Total</th>
				                </tr>
				                <tr>
				                	<?
				                	foreach ($date_range as $key => $val) 
				                	{
				                		?>
				                		<th width="80"><? echo $val;?></th>
				                		<?
				                	}
				                	?>
				                </tr>
			                </thead>
			            </table>
			            <div style="width:<? echo $sub_dep_tbl_width+20;?>px;overflow-y:scroll;max-height:350px;" id="scroll_body_2">
				            <table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
				                <tbody>
				                	<?
				                	$k=1;
				                	$vr_tot_qty = 0;
				                	$vr_tot_val = 0;
				                	$vr_gr_tot_qty = 0;
				                	$vr_gr_tot_val = 0;
				                	$mon_gr_tot_array = array();
				                	foreach ($item_summary_array as $item_key => $val) 
				                	{
				                		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				                		?>
				                		<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" style="font-size:12px">
				                			<td width="100"><? echo $garments_item[$item_key];?></td>
				                			<td width="100" align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
				                			<?                			
				                			$hr_mon_tot_qty = 0;
						                	foreach ($date_range as $key => $val2) 
						                	{
						                		?>
						                		<td width="80" align="right"><? echo number_format($item_summary_qty_array[$item_key][$val2]['qty'],0);?></td>
						                		<?
						                		$hr_mon_tot_qty += $item_summary_qty_array[$item_key][$val2]['qty'];
						                		$vr_gr_tot_qty += $item_summary_qty_array[$item_key][$val2]['qty'];
						                		$mon_gr_tot_array[$val2]['qty'] += $item_summary_qty_array[$item_key][$val2]['qty'];
						                	}
						                	?>
						                	<td width="100" align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
				                		</tr>
				                		<?
				                		$i++;
				                		$k++;

				                		$vr_tot_qty += $val['qty'];
				                		$vr_tot_val += $val['val'];
				                	}
				                	?>
				                </tbody>
				            </table>
			            </div>
			            <table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
			                <tfoot>
			                	<tr>
			                		<th width="100">Grand Total</th>
			                		<th width="100"><? //echo number_format($vr_tot_qty,0);?></th>
			                		<?
				                	foreach ($date_range as $key2 => $val4) 
				                	{
				                		?>
				                		<th width="80" align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
				                		<?
				                	}
				                	?>
				                	<th width="100"><? echo number_format($vr_gr_tot_qty,0);?></th>
			                	</tr>
			                </tfoot>
			            </table>            
			        </div>
			    </div>

		        <!-- ===================================================================================================/
		        / 												DETAILS PART									  		/
		        /==================================================================================================== -->
		        <div id="3rd_part">
			        <div style="margin-top: 20px;width:<? echo $sub_dep_tbl_width+120;?>px;" id="">            
			            
			            <table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
			            	<caption style="text-align:left;"><h2>Details</h2></caption>
			                <thead>
			                	<tr>
				                    <th rowspan="2" width="100">Buyer Ref.</th>
				                    <th rowspan="2" width="100">Buyer</th>
				                    <th rowspan="2" width="100">Item</th>
				                    <th rowspan="2" width="100">Quantity(pcs)</th>
				                    <?
				                    foreach ($year_month_count as $key => $val) 
				                    {
				                    	?>
				                    	<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
				                    	<?
				                    }
				                    ?>
				                    <th rowspan="2" width="100">Grand Total</th>
				                </tr>
				                <tr>
				                	<?
				                	foreach ($date_range as $key => $val) 
				                	{
				                		?>
				                		<th width="80"><? echo $val;?></th>
				                		<?
				                	}
				                	?>
				                </tr>
			                </thead>
			            </table>
			            <div style="width:<? echo $sub_dep_tbl_width+120;?>px;overflow-y:scroll;max-height:350px;" id="scroll_body_3">
				            <table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
				                <tbody>
				                	<?
				                	$k=1;
				                	$vr_tot_qty = 0;
				                	$vr_tot_val = 0;
				                	$vr_gr_tot_qty = 0;
				                	$vr_gr_tot_val = 0;
				                	$mon_gr_tot_array = array();
				                	foreach ($sub_dep_dtls_array as $ref_key => $ref_val) 
				                	{
				                		$ref = 0;
					                	foreach ($ref_val as $buyer_key => $buyer_val) 
					                	{
					                		$byr = 0;
					                		foreach ($buyer_val as $item_key => $val) 
					                		{
						                		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						                		?>
						                		<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" class="tr_even">
						                			<? if($ref==0){?>
						                			<td valing="top" width="100" rowspan="<? echo $rowspan_dtls[$ref_key];?>"><? echo strtoupper($buyer_ref_arr[$buyer_key]);?></td>
						                			<? $ref++;} if($byr==0){?>
						                			<td valing="top" width="100" rowspan="<? echo $rowspan_buyer[$ref_key][$buyer_key];?>"><? echo strtoupper($buyer_arr[$buyer_key]);?></td>
						                			<? $byr++;}?>
						                			<td width="100"><? echo $garments_item[$item_key];?></td>
						                			<td width="100" align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
						                			<?                			
						                			$hr_mon_tot_qty = 0;
								                	foreach ($date_range as $key => $val2) 
								                	{
								                		?>
								                		<td width="80" align="right"><? echo number_format($sub_dep_qty_array[$buyer_key][$item_key][$val2]['qty'],0);?></td>
								                		<?
								                		$hr_mon_tot_qty += $sub_dep_qty_array[$buyer_key][$item_key][$val2]['qty'];
								                		$vr_gr_tot_qty += $sub_dep_qty_array[$buyer_key][$item_key][$val2]['qty'];
								                		$mon_gr_tot_array[$val2]['qty'] += $sub_dep_qty_array[$buyer_key][$item_key][$val2]['qty'];
								                	}
								                	?>
								                	<td width="100" align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
						                		</tr>
						                		<?
						                		$i++;
						                		$k++;

						                		$vr_tot_qty += $val['qty'];
						                		$vr_tot_val += $val['val'];
						                	}
					                	}
				                	}
				                	?>
				                </tbody>
				            </table>
			            </div>
			            <table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
			                <tfoot>
			                	<tr>
			                		<th width="100"></th>
			                		<th width="100"></th>
			                		<th width="100">Grand Total</th>
			                		<th width="100"><? //echo number_format($vr_tot_qty,0);?></th>
			                		<?
				                	foreach ($date_range as $key2 => $val4) 
				                	{
				                		?>
				                		<th width="80" align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
				                		<?
				                	}
				                	?>
				                	<th width="100"><? echo number_format($vr_gr_tot_qty,0);?></th>
			                	</tr>
			                </tfoot>
			            </table>            
			        </div>
			    </div>
		    </fieldset>
			<?
		}
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

if($action=="report_generate_powise")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	extract($_REQUEST);
	$company_name 	=str_replace("'","",$cbo_company_name);
	$buyer_name 	=str_replace("'","",$cbo_buyer_name);
	$buyer_ref 		=str_replace("'","",$cbo_buyer_ref);
	$sub_department =str_replace("'","",$cbo_sub_department);
	$shipping_status_id=str_replace("'","",$cbo_shipping_status);
	$cbo_order_status=str_replace("'","",$cbo_order_status);
	$date_type 		=str_replace("'","",$cbo_date_type);
	$from_year 		=str_replace("'","",$cbo_from_year);
	$from_month 	=str_replace("'","",$cbo_from_month);
	$to_year 		=str_replace("'","",$cbo_to_year);
	$to_month 		=str_replace("'","",$cbo_to_month);
	$report_type 	=str_replace("'","",$cbo_report_type);
	$type 			=str_replace("'","",$reportType);
	$show_value 	=str_replace("'","",$show_value);
	//echo $shipping_status_id.'DD';
	// =========================== MAKING QUERY COND ============================
	// echo cal_days_in_month(CAL_GREGORIAN, $to_month, $to_year); 
	// $dt = $from_year."-".$from_month;
	// echo date('d-m-Y',strtotime($dt));die();

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$shipping_status_chk=array(1,2);
	if(in_array($shipping_status_id,$shipping_status_chk))
	{
	$shipping_status_id="1,2";
	}
	// echo $shipping_status_id.'DDSs';
	// echo $cbo_order_status.'DDSs';die;
	$sql_cond = "";
	if($type==1){
	$sql_cond .= ($company_name !=0) ? " and a.company_name=$company_name" : "";
	}
	$sql_cond .= ($buyer_name !=0) ? " and a.buyer_name in($buyer_name)" : "";
	$sql_cond .= ($sub_department !=0) ? " and a.pro_sub_dep in($sub_department)" : "";
	$sql_cond .= ($shipping_status_id !="") ? " and b.shiping_status in($shipping_status_id)" : "";
	$sql_cond .= ($cbo_order_status !=0) ? " and b.is_confirmed =$cbo_order_status" : "";
	if($buyer_ref !=0)
	{
		$buyer_id_array = return_library_array("select id,id as ids from  lib_buyer where status_active=1 and exporters_reference='$buyer_ref'","id","ids");
	}

	// print_r($buyer_id_array);die();
	if(count($buyer_id_array)>0)
	{
		$buyer_id = implode(",", $buyer_id_array);
		$sql_cond .= " and a.buyer_name in($buyer_id)";
	}

	if($from_year !=0 && $from_month !=0 && $to_year !=0 && $to_month !=0)
	{
		$dt = $from_year."-".$from_month;
		$from_date = date('d-M-Y',strtotime($dt));

		$last_day = cal_days_in_month(CAL_GREGORIAN, $to_month, $to_year); 
		$dt = $to_year."-".$to_month."-".$last_day;
		$to_date = date('d-M-Y',strtotime($dt));

		if($date_type==1)
		{
			$sql_cond .= " and b.pub_shipment_date between '$from_date' and '$to_date'";
		}
		elseif ($date_type==2) 
		{
			$sql_cond .= " and b.txt_etd_ldd between '$from_date' and '$to_date'";
		}
		elseif ($date_type==2) 
		{
			$sql_cond .= " and b.shipment_date between '$from_date' and '$to_date'";
		}
	}

	// echo $sql_cond;die();

	function getMonthsInRange($startDate, $endDate) 
	{
		$months = array();
		while (strtotime($startDate) <= strtotime($endDate)) 
		{
		    // $months[] = array('year' => date('Y', strtotime($startDate)), 'month' => date('m', strtotime($startDate)), );
		    $months[strtoupper(date('M-Y', strtotime($startDate)))] = strtoupper(date('M-Y', strtotime($startDate)));
		    $startDate = date('01 M Y', strtotime($startDate.'+ 1 month')); // Set date to 1 so that new month is returned as the month changes.		    
		}

		return $months;
	}
	$date_range = getMonthsInRange($from_date,$to_date);

	$year_month_count = array();
	$month_year_width = array();
	$month_year_tot_width = 0;
	$colspan = 5;
	foreach ($date_range as $key => $val) 
	{
		$ex_data = explode("-", $val);
		$year_month_count[$ex_data[1]]++;
		$month_year_width[$ex_data[1]] += 80 ;
		$month_year_tot_width += 80;
		$colspan++;
	}
	//echo $report_type.'DDx';;
	//echo '<h2 style="text-align:center;">under construction</h2>';

	
	if($type==1){
		if($report_type==1) // sub dept wise
		{
			// =============================================== MAIN QUERY =========================================
			   /* $sql = "SELECT  b.id as PO_ID,a.BUYER_NAME, a.TOTAL_SET_QNTY, a.PRO_SUB_DEP, TO_CHAR (b.pub_shipment_date, 'MON-YYYY') AS MONTH_YEAR, (B.PO_QUANTITY) as qty, (B.PO_TOTAL_PRICE) as val, A.GMTS_ITEM_ID
			FROM wo_po_details_master a, wo_po_break_down b
			WHERE a.id = b.job_id $sql_cond AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active IN (1) AND b.is_deleted = 0
			"; */
			/* $sql = "SELECT  b.id as PO_ID,a.BUYER_NAME, a.TOTAL_SET_QNTY, a.PRO_SUB_DEP, TO_CHAR (b.pub_shipment_date, 'MON-YYYY') AS MONTH_YEAR, (c.ORDER_QUANTITY) as qty, (C.ORDER_TOTAL) as val, A.GMTS_ITEM_ID
			FROM wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c
			WHERE a.id = b.job_id and b.id=c.PO_BREAK_DOWN_ID and a.id = c.job_id and c.status_active=1  $sql_cond AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active IN (1) AND b.is_deleted = 0
			";*////GROUP BY a.buyer_name, a.pro_sub_dep, b.id, a.total_set_qnty, b.pub_shipment_date, A.GMTS_ITEM_ID
			$sql="SELECT  b.id as PO_ID,a.BUYER_NAME, a.TOTAL_SET_QNTY, a.PRO_SUB_DEP, TO_CHAR (b.pub_shipment_date, 'MON-YYYY') AS MONTH_YEAR, (B.PO_QUANTITY) as qty, (B.PO_TOTAL_PRICE) as val, C.GMTS_ITEM_ID
			FROM wo_po_details_master a, wo_po_break_down b,wo_po_details_mas_set_details c
			WHERE a.id = b.job_id and a.id = c.job_id and b.job_id = c.job_id $sql_cond AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active IN (1) AND b.is_deleted = 0
			";
			//echo $sql; die();

			$sql_res = sql_select($sql);
			if (count($sql_res) < 1)
			{
				echo '<div style="text-align: center;color: red;font-weight: bold;font-size: 20px;">Data not found.</div>';
				disconnect($con);
				die();
			}

			$sub_dep_dtls_array = array();
			$sub_dep_qty_array = array();
			$sub_dep_summary_array = array();
			$sub_dep_summary_qty_array = array();
			$item_summary_array = array();
			$item_summary_qty_array = array();
			foreach ($sql_res as $row)
			{
				// for summary
				$sub_dep_summary_array[$row['PRO_SUB_DEP']]['qty'] += $row['QTY'] ;//* $row['TOTAL_SET_QNTY']
				$sub_dep_summary_array[$row['PRO_SUB_DEP']]['val'] += $row['VAL'];

				$sub_dep_summary_qty_array[$row['PRO_SUB_DEP']][$row['MONTH_YEAR']]['qty'] += $row['QTY'] ;
				$sub_dep_summary_qty_array[$row['PRO_SUB_DEP']][$row['MONTH_YEAR']]['val'] += $row['VAL'];

				// for item wise summary
				$item_summary_array[$row[csf('gmts_item_id')]]['qty'] += $row['QTY'];
				$item_summary_array[$row[csf('gmts_item_id')]]['val'] += $row['VAL'];

				$item_summary_qty_array[$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['qty'] += $row['QTY'];
				$item_summary_qty_array[$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['val'] += $row['VAL'];
				$item_summary_qty_array[$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['id']=$row['PO_ID'];
				$item_summary_qty_array[$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['val']+=$row['VAL'];
				$item_summary_qty_array[$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['qty']+=$row['QTY'] ;

				// for details
				$sub_dep_qty_array[$row['PRO_SUB_DEP']][$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['id']=$row['PO_ID'];
				$sub_dep_qty_array[$row['PRO_SUB_DEP']][$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['val']+=$row['VAL'];
				$sub_dep_qty_array[$row['PRO_SUB_DEP']][$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['qty']+=$row['QTY'] ;
				$sub_dep_qty_array[$row['PRO_SUB_DEP']][$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['qty'] += $row['QTY'] ;
				$sub_dep_qty_array[$row['PRO_SUB_DEP']][$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['val'] += $row['VAL'];

				$sub_dep_dtls_array[$row['PRO_SUB_DEP']][$row[csf('gmts_item_id')]]['qty'] += $row['QTY'];// * $row['TOTAL_SET_QNTY']
				$sub_dep_dtls_array[$row['PRO_SUB_DEP']][$row[csf('gmts_item_id')]]['val'] += $row['VAL'];
			
				// $sub_dep_summary_qty_array[$row['PRO_SUB_DEP']][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]= $row['PO_ID'];
				$sub_dep_summary_qty_array[$row['PRO_SUB_DEP']][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['id']= $row['PO_ID'];
				$sub_dep_summary_qty_array[$row['PRO_SUB_DEP']][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['val']+= $row['VAL'];
				$sub_dep_summary_qty_array[$row['PRO_SUB_DEP']][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['qty']+=  $row['QTY'] ; //* $row['TOTAL_SET_QNTY']
				$all_po_array[$row['PO_ID']]= $row['PO_ID'];
			
			}

			$rowspan = array();
			foreach ($sub_dep_dtls_array as $sub_key => $sub_value) 
			{
				foreach ($sub_value as $itm_key => $itm_value) 
				{
					$rowspan[$sub_key]++;
				}
			}

			$sub_dep_tbl_width = $month_year_tot_width + 300;
			$buyer_name_arr=return_library_array("select id,short_name from  lib_buyer where id in($buyer_name)","id","short_name");
			$buyer_ref_name_arr=return_library_array("select id,exporters_reference from  lib_buyer where id in($buyer_name)","id","exporters_reference");
			
			
			
			$exfactoryQry="select PO_BREAK_DOWN_ID,EX_FACTORY_QNTY from PRO_EX_FACTORY_MST where  IS_DELETED=0 and STATUS_ACTIVE=1 ".where_con_using_array($all_po_array,0,'PO_BREAK_DOWN_ID')."";
			$exfactoryQry_result = sql_select($exfactoryQry);
			$exfactory_qty_arr=array();
			foreach($exfactoryQry_result as $row){
				$exfactory_qty_arr[$row[PO_BREAK_DOWN_ID]]+=$row[EX_FACTORY_QNTY];
			}
			
			//print_r($exfactory_qty_arr);die;
		
		
			
			
			ob_start();
			?>
				<fieldset style="width:100%;">
					<style type="text/css">
						.tr_odd td{font-size: 14px !important;}
						.tr_even td{font-size: 12px !important;}
					</style>
					<div id="heading_part">
						<table width="<? echo $sub_dep_tbl_width+120;?>;" cellspacing="0"  align="center" >
							<tr>
								<td colspan="<? echo $colspan;?>" align="center" class="form_caption">
									<strong style="font-size:16px;">Company Name:<? echo  $company_library[$company_name] ;?></strong>
								</td>
							</tr>
							<tr class="form_caption">
								<td colspan="<? echo $colspan;?>" align="center" class="form_caption"> <strong style="font-size:15px;">Month Wise Quantity (Pcs) and Value</strong></td>
							</tr>
							<tr align="center">
								<td colspan="<? echo $colspan;?>" align="center" class="form_caption"> <strong style="font-size:15px;">Date Type : <? echo ($date_type==1) ? "Shipment Date (RFI)": "Original Ship Date";?></strong></td>
							</tr>
							<tr align="center">
								<td colspan="<? echo $colspan;?>" align="center" class="form_caption"> <strong style="font-size:15px;">Runtime : <? echo date("j F, Y h:i:s a");?></strong></td>
							</tr>
						</table>
						<table width="<? echo $sub_dep_tbl_width;?>;" cellspacing="0"  align="center">
							<tr class="form_caption">
								<td colspan="<? echo $colspan;?>" align="center" class="form_caption"> 
									<div style="font-size:20px;text-align:left;">Buyer : <? echo implode(",", $buyer_name_arr);?></div>
									<div style="font-size:20px;text-align:left;">Buyer Ref. : <? echo implode(",", array_filter($buyer_ref_name_arr));?></div>
								</td>
							</tr>
						</table>
					</div>
			<?
			if($show_value) // report generate with value
			{
				?>
					<!-- ===================================================================================================/
					/ 										SUB DEP WISE SUMMARY PART									  	/
					/==================================================================================================== -->
					<div id="1st_part" style="width: <? echo $sub_dep_tbl_width;?>px;">
						<table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1">
							<caption style="text-align:left;"><h2>Sub Dept wise Summary</h2></caption>
							<thead>
								<tr>
									<th rowspan="2" width="100">Sub Dept.</th>
									<th rowspan="2" width="100">Quantity(pcs)/Value</th>
									<?
									foreach ($year_month_count as $key => $val) 
									{
										?>
										<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
										<?
									}
									?>
									<th rowspan="2" width="100">Grand Total</th>
								</tr>
								<tr>
									<?
									foreach ($date_range as $key => $val) 
									{
										?>
										<th width="80"><? echo $val;?></th>
										<?
									}
									?>
								</tr>
							</thead>
							<tbody>
								<?
								
								
								$i=1;
								$vr_tot_qty = 0;
								$vr_tot_val = 0;
								$vr_gr_tot_qty = 0;
								$vr_gr_tot_val = 0;
								$mon_gr_tot_array = array();
								foreach ($sub_dep_summary_array as $sub_dep_key => $val) 
								{
									if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
									<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" class="tr_odd">
										<td valign="top" rowspan="2" title="<? echo $sub_dep_key;?>"><? echo strtoupper($lib_sub_dept_array[$sub_dep_key]);?></td>
										<td align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
										<?
										$hr_mon_tot_qty = 0;
										foreach ($date_range as $key => $val2) 
										{
											$exfactoryQty=0;
											if(str_replace("'","",$cbo_shipping_status)==2){
												foreach($sub_dep_summary_qty_array[$sub_dep_key][$val2]['PO_ID'] as $po_id){
													$exfactoryQty+=$exfactory_qty_arr[$po_id['id']];
												}
											}
											
											?>
											<td align="right" title="Ex-FactQty=<? echo $exfactoryQty;?>"><? echo number_format(($sub_dep_summary_qty_array[$sub_dep_key][$val2]['qty']-$exfactoryQty),0);?></td>
											<?
											$hr_mon_tot_qty += $sub_dep_summary_qty_array[$sub_dep_key][$val2]['qty']-$exfactoryQty;
											$vr_gr_tot_qty += $sub_dep_summary_qty_array[$sub_dep_key][$val2]['qty']-$exfactoryQty;
											$mon_gr_tot_array[$val2]['qty'] += $sub_dep_summary_qty_array[$sub_dep_key][$val2]['qty']-$exfactoryQty;
										}
										?>
										<td align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
									</tr>
									<tr class="tr_even">
									<td align="right">Value<? //echo number_format($val['val'],2);?></td>	
									<?
									$hr_mon_tot_val = 0;
									foreach ($date_range as $key2 => $val3) 
									{
										$exfactoryVal=0;
										$ave_rate=0;
										if(str_replace("'","",$cbo_shipping_status)==2){
											foreach($sub_dep_summary_qty_array[$sub_dep_key][$val3]['PO_ID'] as $po_id){
												$ave_rate=$po_id['val']/$po_id['qty'];
												$exfactoryVal+=$exfactory_qty_arr[$po_id['id']]*$ave_rate;
											}
										}
										?>
										<td align="right">$ <? echo number_format($sub_dep_summary_qty_array[$sub_dep_key][$val3]['val']-$exfactoryVal,2);?></td>
										<?
										$hr_mon_tot_val += $sub_dep_summary_qty_array[$sub_dep_key][$val3]['val']-$exfactoryVal;
										$vr_gr_tot_val += $sub_dep_summary_qty_array[$sub_dep_key][$val3]['val']-$exfactoryVal;
										$mon_gr_tot_array[$val3]['val'] += $sub_dep_summary_qty_array[$sub_dep_key][$val3]['val']-$exfactoryVal;
									}
									?>
									<td align="right">$ <? echo number_format($hr_mon_tot_val,2);?></td>
								</tr>
									<?
									$i++;

									$vr_tot_qty += $val['qty'];
									$vr_tot_val += $val['val'];
								}
								?>
							</tbody>
							<tfoot>
								<tr>
									<th rowspan="2">Grand Total</th>
									<th><? //echo number_format($vr_tot_qty,0);?></th>
									<?
									foreach ($date_range as $key2 => $val4) 
									{
										?>
										<th align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
										<?
									}
									?>
									<th><? echo number_format($vr_gr_tot_qty,0);?></th>
								</tr>
								<tr>
									<th><? //echo number_format($vr_tot_val,0);?></th>
									<?
									foreach ($date_range as $key2 => $val5) 
									{
										?>
										<th align="right">$ <? echo number_format($mon_gr_tot_array[$val5]['val'],2);?></th>
										<?
									}
									?>
									<th>$ <? echo number_format($vr_gr_tot_val,0);?></th>
								</tr>
							</tfoot>
						</table>
					</div>        
					<!-- ===================================================================================================/
					/ 											ITEM WISE SUMMARY PART									  	/
					/==================================================================================================== -->
					<div  id="2nd_part">
						<div style="margin-top: 20px;width:<? echo $sub_dep_tbl_width+20;?>px;">            
							
							<table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
								<caption style="text-align:left;"><h2>Item wise Summary</h2></caption>
								<thead>
									<tr>
										<th rowspan="2" width="100">Item</th>
										<th rowspan="2" width="100">Quantity(pcs)/Value</th>
										<?
										foreach ($year_month_count as $key => $val) 
										{
											?>
											<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
											<?
										}
										?>
										<th rowspan="2" width="100">Grand Total</th>
									</tr>
									<tr>
										<?
										foreach ($date_range as $key => $val) 
										{
											?>
											<th width="80"><? echo $val;?></th>
											<?
										}
										?>
									</tr>
								</thead>
							</table>
							<div style="width:<? echo $sub_dep_tbl_width+20;?>px;overflow-y:scroll;max-height:350px;" id="scroll_body_2">
								<table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
									<tbody>
										<?
										$k=1;
										$vr_tot_qty = 0;
										$vr_tot_val = 0;
										$vr_gr_tot_qty = 0;
										$vr_gr_tot_val = 0;
										$mon_gr_tot_array = array();
										foreach ($item_summary_array as $item_key => $val) 
										{
											$item_arr = explode(',', $item_key);
											$items_str = '';

											foreach ($item_arr as $item_id) {
												$items_str .= $garments_item[$item_id] . ', ';
											}

											$items_str = rtrim($items_str, ' ,');
											if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
											?>
											<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" class="tr_odd">
												<td width="100" valign="top" rowspan="2"><? echo strtoupper($items_str);?></td>
												<td width="100" align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
												<?                			
												$hr_mon_tot_qty = 0;
												foreach ($date_range as $key => $val2) 
												{
													$exfactoryQty2=0;
													if(str_replace("'","",$cbo_shipping_status)==2){
														foreach($item_summary_qty_array[$item_key][$val2]['PO_ID'] as $po_id){
															$exfactoryQty2+=$exfactory_qty_arr[$po_id['id']];
														}
													}
													?>
													<td width="80" align="right"><? echo number_format($item_summary_qty_array[$item_key][$val2]['qty']-$exfactoryQty2,0);?></td>
													<?
													$hr_mon_tot_qty += $item_summary_qty_array[$item_key][$val2]['qty']-$exfactoryQty2;
													$vr_gr_tot_qty += $item_summary_qty_array[$item_key][$val2]['qty']-$exfactoryQty2;
													$mon_gr_tot_array[$val2]['qty'] += $item_summary_qty_array[$item_key][$val2]['qty']-$exfactoryQty2;
												}
												?>
												<td width="100" align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
											</tr>
											<tr class="tr_even">
											<td width="100" align="right">Value<? //echo number_format($val['val'],2);?></td>	
											<?
											$hr_mon_tot_val = 0;
											foreach ($date_range as $key2 => $val3) 
											{
												$exfactoryVal2=0;
												$ave_rate2=1;

												if(str_replace("'","",$cbo_shipping_status)==2){
													foreach($item_summary_qty_array[$item_key][$val3]['PO_ID'] as $po_id){
														$ave_rate2=$po_id['val']/$po_id['qty'];
														$exfactoryVal2+=$exfactory_qty_arr[$po_id['id']]*$ave_rate2;
													}
												}
												?>
												<td width="80" align="right">$ <? echo number_format($item_summary_qty_array[$item_key][$val3]['val']-$exfactoryVal2,0);?></td>
												<?
												$hr_mon_tot_val += $item_summary_qty_array[$item_key][$val3]['val']-$exfactoryVal2;
												$vr_gr_tot_val += $item_summary_qty_array[$item_key][$val3]['val']-$exfactoryVal2;
												$mon_gr_tot_array[$val3]['val'] += $item_summary_qty_array[$item_key][$val3]['val']-$exfactoryVal2;
											}
											?>
											<td width="100" align="right">$ <? echo number_format($hr_mon_tot_val,0);?></td>
										</tr>
											<?
											$i++;
											$k++;

											$vr_tot_qty += $val['qty'];
											$vr_tot_val += $val['val'];
										}
										?>
									</tbody>
								</table>
							</div>
							<table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
								<tfoot>
									<tr>
										<th width="100" rowspan="2">Grand Total</th>
										<th width="100"><? //echo number_format($vr_tot_qty,0);?></th>
										<?
										foreach ($date_range as $key2 => $val4) 
										{
											?>
											<th width="80" align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
											<?
										}
										?>
										<th><? echo number_format($vr_gr_tot_qty,0);?></th>
									</tr>
									<tr>
										<th><? //echo number_format($vr_tot_val,2);?></th>
										<?
										foreach ($date_range as $key2 => $val5) 
										{
											?>
											<th width="80" align="right">$ <? echo number_format($mon_gr_tot_array[$val5]['val'],2);?></th>
											<?
										}
										?>
										<th width="100">$ <? echo number_format($vr_gr_tot_val,2);?></th>
									</tr>
								</tfoot>
							</table>            
						</div>
					</div>

					<!-- ===================================================================================================/
					/ 												DETAILS PART									  		/
					/==================================================================================================== -->
					<div id="3rd_part">
						<div style="margin-top: 20px;width:<? echo $sub_dep_tbl_width+120;?>px;">            
							
							<table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
								<caption style="text-align:left;"><h2>Details</h2></caption>
								<thead>
									<tr>
										<th rowspan="2" width="100">Sub Dept.</th>
										<th rowspan="2" width="100">Item</th>
										<th rowspan="2" width="100">Quantity(pcs)/Value</th>
										<?
										foreach ($year_month_count as $key => $val) 
										{
											?>
											<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
											<?
										}
										?>
										<th rowspan="2" width="100">Grand Total</th>
									</tr>
									<tr>
										<?
										foreach ($date_range as $key => $val) 
										{
											?>
											<th width="80"><? echo $val;?></th>
											<?
										}
										?>
									</tr>
								</thead>
							</table>
							<div style="width:<? echo $sub_dep_tbl_width+120;?>px;overflow-y:scroll;max-height:350px;" id="scroll_body_3">
								<table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
									<tbody>
										<?
										$k=1;
										$vr_tot_qty = 0;
										$vr_tot_val = 0;
										$vr_gr_tot_qty = 0;
										$vr_gr_tot_val = 0;
										$mon_gr_tot_array = array();
										foreach ($sub_dep_dtls_array as $sub_dep_key => $sub_dep_val) 
										{
											foreach ($sub_dep_val as $item_key => $val) 
											{
												$item_arr = explode(',', $item_key);
												$items_str = '';

												foreach ($item_arr as $item_id) {
													$items_str .= $garments_item[$item_id] . ', ';
												}

												$items_str = rtrim($items_str, ' ,');

												if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
												?>
												<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" class="tr_odd">
													<td width="100" valign="top" rowspan="2"><? echo strtoupper($lib_sub_dept_array[$sub_dep_key]);?></td>
													<td width="100" valign="top" rowspan="2"><? echo strtoupper($items_str);?></td>
													<td width="100" align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
													<?                			
													$hr_mon_tot_qty = 0;
													foreach ($date_range as $key => $val2) 
													{
														$exfactoryQty3=0;
														if(str_replace("'","",$cbo_shipping_status)==2){
															foreach($sub_dep_qty_array[$sub_dep_key][$item_key][$val2]['PO_ID'] as $po_id){
																$exfactoryQty3+=$exfactory_qty_arr[$po_id['id']];
															}
														}
														?>
														<td width="80" align="right"><? echo number_format($sub_dep_qty_array[$sub_dep_key][$item_key][$val2]['qty']-$exfactoryQty3,0);?></td>
														<?
														$hr_mon_tot_qty += $sub_dep_qty_array[$sub_dep_key][$item_key][$val2]['qty']-$exfactoryQty3;
														$vr_gr_tot_qty += $sub_dep_qty_array[$sub_dep_key][$item_key][$val2]['qty']-$exfactoryQty3;
														$mon_gr_tot_array[$val2]['qty'] += $sub_dep_qty_array[$sub_dep_key][$item_key][$val2]['qty']-$exfactoryQty3;
													}
													?>
													<td width="100" align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
												</tr>
												<tr class="tr_even">
												<td width="100" align="right">Value<? //echo number_format($val['val'],2);?></td>	
												<?
												$hr_mon_tot_val = 0;
												foreach ($date_range as $key2 => $val3) 
												{
													$exfactoryVal3=0;
													$ave_rate3=1;
													if(str_replace("'","",$cbo_shipping_status)==2){
														foreach($sub_dep_qty_array[$sub_dep_key][$item_key][$val3]['PO_ID'] as $po_id){
															$ave_rate3=$po_id['val']/$po_id['qty'];
														$exfactoryVal3+=$exfactory_qty_arr[$po_id['id']]*$ave_rate3;
														}
													}
													?>
													<td width="80" align="right">$ <? echo number_format($sub_dep_qty_array[$sub_dep_key][$item_key][$val3]['val']-$exfactoryVal3,2);?></td>
													<?
													$hr_mon_tot_val += $sub_dep_qty_array[$sub_dep_key][$item_key][$val3]['val']-$exfactoryVal3;
													$vr_gr_tot_val += $sub_dep_qty_array[$sub_dep_key][$item_key][$val3]['val']-$exfactoryVal3;
													$mon_gr_tot_array[$val3]['val'] += $sub_dep_qty_array[$sub_dep_key][$item_key][$val3]['val']-$exfactoryVal3;
												}
												?>
												<td width="100" align="right">$ <? echo number_format($hr_mon_tot_val,2);?></td>
											</tr>
												<?
												$i++;
												$k++;

												$vr_tot_qty += $val['qty'];
												$vr_tot_val += $val['val'];
											}
										}
										?>
									</tbody>
								</table>
							</div>
							<table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
								<tfoot>
									<tr>
										<th width="100" rowspan="2"></th>
										<th width="100" rowspan="2">Grand Total</th>
										<th width="100"><? //echo number_format($vr_tot_qty,0);?></th>
										<?
										foreach ($date_range as $key2 => $val4) 
										{
											?>
											<th width="80" align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
											<?
										}
										?>
										<th><? echo number_format($vr_gr_tot_qty,0);?></th>
									</tr>
									<tr>
										<th><? //echo number_format($vr_tot_val,0);?></th>
										<?
										foreach ($date_range as $key2 => $val5) 
										{
											?>
											<th width="80" align="right">$ <? echo number_format($mon_gr_tot_array[$val5]['val'],2);?></th>
											<?
										}
										?>
										<th width="100">$ <? echo number_format($vr_gr_tot_val,2);?></th>
									</tr>
								</tfoot>
							</table>            
						</div>
					</div>
				</fieldset>
				<?
			}
			else // report generate without value
			{
				?>
					<!-- ===================================================================================================/
					/ 										SUB DEP WISE SUMMARY PART									  	/
					/==================================================================================================== -->
					<div id="1st_part">
						<table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1">
							<caption style="text-align:left;"><h2>Sub Dept wise Summary</h2></caption>
							<thead>
								<tr>
									<th rowspan="2" width="100">Sub Dept.</th>
									<th rowspan="2" width="100">Quantity(pcs)</th>
									<?
									foreach ($year_month_count as $key => $val) 
									{
										?>
										<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
										<?
									}
									?>
									<th rowspan="2" width="100">Grand Total</th>
								</tr>
								<tr>
									<?
									foreach ($date_range as $key => $val) 
									{
										?>
										<th width="80"><? echo $val;?></th>
										<?
									}
									?>
								</tr>
							</thead>
							<tbody>
								<?
								$i=1;
								$vr_tot_qty = 0;
								$vr_tot_val = 0;
								$vr_gr_tot_qty = 0;
								$vr_gr_tot_val = 0;
								$mon_gr_tot_array = array();
								foreach ($sub_dep_summary_array as $sub_dep_key => $val) 
								{
									if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
									<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" class="tr_odd">
										<td><? echo strtoupper($lib_sub_dept_array[$sub_dep_key]);?></td>
										<td align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
										<?                			
										$hr_mon_tot_qty = 0;
										foreach ($date_range as $key => $val2) 
										{
											$exfactoryQty=0;
											if(str_replace("'","",$cbo_shipping_status)==2){
												foreach($sub_dep_summary_qty_array[$sub_dep_key][$val2]['PO_ID'] as $po_id){
												$exfactoryQty+=$exfactory_qty_arr[$po_id['id']];
												}
											}

											?>
											<td align="right"><? echo number_format(($sub_dep_summary_qty_array[$sub_dep_key][$val2]['qty']-$exfactoryQty),0);?></td>
											<?
											$hr_mon_tot_qty += $sub_dep_summary_qty_array[$sub_dep_key][$val2]['qty']-$exfactoryQty;
											$vr_gr_tot_qty += $sub_dep_summary_qty_array[$sub_dep_key][$val2]['qty']-$exfactoryQty;
											$mon_gr_tot_array[$val2]['qty'] += $sub_dep_summary_qty_array[$sub_dep_key][$val2]['qty']-$exfactoryQty;
										}
										?>
										<td align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
									</tr>
									<?
									$i++;

									$vr_tot_qty += $val['qty'];
									$vr_tot_val += $val['val'];
								}
								?>
							</tbody>
							<tfoot>
								<tr>
									<th>Grand Total</th>
									<th><? //echo number_format($vr_tot_qty,0);?></th>
									<?
									foreach ($date_range as $key2 => $val4) 
									{
										?>
										<th align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
										<?
									}
									?>
									<th><? echo number_format($vr_gr_tot_qty,0);?></th>
								</tr>
							</tfoot>
						</table>
					</div>        
					<!-- ===================================================================================================/
					/ 											ITEM WISE SUMMARY PART									  	/
					/==================================================================================================== -->
					<div  id="2nd_part">
						<div style="margin-top: 20px;width:<? echo $sub_dep_tbl_width+20;?>px;">            
							
							<table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
								<caption style="text-align:left;"><h2>Item wise Summary</h2></caption>
								<thead>
									<tr>
										<th rowspan="2" width="100">Item</th>
										<th rowspan="2" width="100">Quantity(pcs)</th>
										<?
										foreach ($year_month_count as $key => $val) 
										{
											?>
											<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
											<?
										}
										?>
										<th rowspan="2" width="100">Grand Total</th>
									</tr>
									<tr>
										<?
										foreach ($date_range as $key => $val) 
										{
											?>
											<th width="80"><? echo $val;?></th>
											<?
										}
										?>
									</tr>
								</thead>
							</table>
							<div style="width:<? echo $sub_dep_tbl_width+20;?>px;overflow-y:scroll;max-height:350px;" id="scroll_body_2">
								<table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
									<tbody>
										<?
										$k=1;
										$vr_tot_qty = 0;
										$vr_tot_val = 0;
										$vr_gr_tot_qty = 0;
										$vr_gr_tot_val = 0;
										$mon_gr_tot_array = array();
										foreach ($item_summary_array as $item_key => $val) 
										{
											if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
											?>
											<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" class="tr_odd">
												<td width="100"><? echo strtoupper($garments_item[$item_key]);?></td>
												<td width="100" align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
												<?                			
												$hr_mon_tot_qty = 0;
												foreach ($date_range as $key => $val2) 
												{
													$exfactoryQty2=0;
													if(str_replace("'","",$cbo_shipping_status)==2){
														foreach($item_summary_qty_array[$item_key][$val2]['PO_ID'] as $po_id){
															$exfactoryQty2+=$exfactory_qty_arr[$po_id['id']];
														}
													}
													?>
													<td width="80" align="right"><? echo number_format($item_summary_qty_array[$item_key][$val2]['qty']-$exfactoryQty2,0);?></td>
													<?
													$hr_mon_tot_qty += $item_summary_qty_array[$item_key][$val2]['qty']-$exfactoryQty2;
													$vr_gr_tot_qty += $item_summary_qty_array[$item_key][$val2]['qty']-$exfactoryQty2;
													$mon_gr_tot_array[$val2]['qty'] += $item_summary_qty_array[$item_key][$val2]['qty']-$exfactoryQty2;
												}
												?>
												<td width="100" align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
											</tr>
											<?
											$i++;
											$k++;

											$vr_tot_qty += $val['qty'];
											$vr_tot_val += $val['val'];
										}
										?>
									</tbody>
								</table>
							</div>
							<table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
								<tfoot>
									<tr>
										<th width="100">Grand Total</th>
										<th width="100"><? //echo number_format($vr_tot_qty,0);?></th>
										<?
										foreach ($date_range as $key2 => $val4) 
										{
											?>
											<th width="80" align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
											<?
										}
										?>
										<th><? echo number_format($vr_gr_tot_qty,0);?></th>
									</tr>
								</tfoot>
							</table>            
						</div>
					</div>
					<!-- ===================================================================================================/
					/ 												DETAILS PART									  		/
					/==================================================================================================== -->
					<div  id="3rd_part">
						<div style="margin-top: 20px;width:<? echo $sub_dep_tbl_width+120;?>px;">            
							
							<table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
								<caption style="text-align:left;"><h2>Details</h2></caption>
								<thead>
									<tr>
										<th rowspan="2" width="100">Sub Dept.</th>
										<th rowspan="2" width="100">Item</th>
										<th rowspan="2" width="100">Quantity(pcs)</th>
										<?
										foreach ($year_month_count as $key => $val) 
										{
											?>
											<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
											<?
										}
										?>
										<th rowspan="2" width="100">Grand Total</th>
									</tr>
									<tr>
										<?
										foreach ($date_range as $key => $val) 
										{
											?>
											<th width="80"><? echo $val;?></th>
											<?
										}
										?>
									</tr>
								</thead>
							</table>
							<div style="width:<? echo $sub_dep_tbl_width+120;?>px;overflow-y:scroll;max-height:350px;" id="scroll_body_3">
								<table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
									<tbody>
										<?
										$k=1;
										$vr_tot_qty = 0;
										$vr_tot_val = 0;
										$vr_gr_tot_qty = 0;
										$vr_gr_tot_val = 0;
										$mon_gr_tot_array = array();
										foreach ($sub_dep_dtls_array as $sub_dep_key => $sub_dep_val) 
										{
											foreach ($sub_dep_val as $item_key => $val) 
											{
												if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
												?>
												<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" class="tr_odd">
													<td width="100"><? echo strtoupper($lib_sub_dept_array[$sub_dep_key]);?></td>
													<td width="100"><? echo strtoupper($garments_item[$item_key]);?></td>
													<td width="100" align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
													<?                			
													$hr_mon_tot_qty = 0;
													foreach ($date_range as $key => $val2) 
													{
														$exfactoryQty3=0;
														if(str_replace("'","",$cbo_shipping_status)==2){
															foreach($sub_dep_qty_array[$sub_dep_key][$item_key][$val2]['PO_ID'] as $po_id){
																$exfactoryQty3+=$exfactory_qty_arr[$po_id['id']];
															}
														}
														?>
														<td width="80" align="right"><? echo number_format($sub_dep_qty_array[$sub_dep_key][$item_key][$val2]['qty']-$exfactoryQty3,0);?></td>
														<?
														$hr_mon_tot_qty += $sub_dep_qty_array[$sub_dep_key][$item_key][$val2]['qty']-$exfactoryQty3;
														$vr_gr_tot_qty += $sub_dep_qty_array[$sub_dep_key][$item_key][$val2]['qty']-$exfactoryQty3;
														$mon_gr_tot_array[$val2]['qty'] += $sub_dep_qty_array[$sub_dep_key][$item_key][$val2]['qty']-$exfactoryQty3;
													}
													?>
													<td width="100" align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
												</tr>
												<?
												$i++;
												$k++;

												$vr_tot_qty += $val['qty'];
												$vr_tot_val += $val['val'];
											}
										}
										?>
									</tbody>
								</table>
							</div>
							<table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
								<tfoot>
									<tr>
										<th width="100"></th>
										<th width="100">Grand Total</th>
										<th width="100"><? //echo number_format($vr_tot_qty,0);?></th>
										<?
										foreach ($date_range as $key2 => $val4) 
										{
											?>
											<th width="80" align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
											<?
										}
										?>
										<th><? echo number_format($vr_gr_tot_qty,0);?></th>
									</tr>
								</tfoot>
							</table>            
						</div>
					</div>
				</fieldset>
				<?
			}
		}
		else // buyer wise 
		{		
			// =============================================== MAIN QUERY =========================================
			/*$sql = "SELECT a.BUYER_NAME,a.PRO_SUB_DEP,a.TOTAL_SET_QNTY,to_char(b.shipment_date,'MON-YYYY') as MONTH_YEAR, SUM(c.ORDER_QUANTITY) AS QTY,sum(c.ORDER_TOTAL) as VAL,c.ITEM_NUMBER_ID
			from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c where a.id=b.job_id and a.id=c.job_id  $sql_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.id=c.po_break_down_id group by a.buyer_name,a.pro_sub_dep,b.id,b.shipment_date,c.item_number_id,a.total_set_qnty";*/

			$sql = "SELECT b.id as PO_ID,a.BUYER_NAME, a.PRO_SUB_DEP, a.TOTAL_SET_QNTY, TO_CHAR (b.shipment_date, 'MON-YYYY') AS MONTH_YEAR, sum(B.PO_QUANTITY) as qty, sum(B.PO_TOTAL_PRICE) as val, A.GMTS_ITEM_ID
			FROM wo_po_details_master a, wo_po_break_down b
			WHERE a.id = b.job_id $sql_cond AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active IN (1) AND b.is_deleted = 0
			GROUP BY a.buyer_name, a.pro_sub_dep, b.id, b.shipment_date, a.total_set_qnty, A.GMTS_ITEM_ID";

			$sql_res = sql_select($sql);
			if (count($sql_res) < 1)
			{
				echo '<div style="text-align: center;color: red;font-weight: bold;font-size: 20px;">Data not found.</div>';
				disconnect($con);
				die();
			}

			$sub_dep_dtls_array = array();
			$sub_dep_qty_array = array();
			$sub_dep_summary_array = array();
			$sub_dep_summary_qty_array = array();
			$item_summary_array = array();
			$item_summary_qty_array = array();
			$count = 1;
			foreach ($sql_res as $row)
			{
				// for summary
				$sub_dep_summary_array[$buyer_ref_arr[$row['BUYER_NAME']]][$row['BUYER_NAME']]['val'] += $row['VAL'];
				$sub_dep_summary_array[$buyer_ref_arr[$row['BUYER_NAME']]][$row['BUYER_NAME']]['qty'] += $row['QTY'] * $row['TOTAL_SET_QNTY'];

				$sub_dep_summary_qty_array[$row['BUYER_NAME']][$row['MONTH_YEAR']]['val'] += $row['VAL'];
				$sub_dep_summary_qty_array[$row['BUYER_NAME']][$row['MONTH_YEAR']]['qty'] += $row['QTY'] * $row['TOTAL_SET_QNTY'];

				// for item wise summary
				$item_summary_array[$row[csf('gmts_item_id')]]['val'] += $row['VAL'];
				$item_summary_array[$row[csf('gmts_item_id')]]['qty'] += $row['QTY'] * $row['TOTAL_SET_QNTY'];

				$item_summary_qty_array[$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['val'] += $row['VAL'];
				$item_summary_qty_array[$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['qty'] += $row['QTY'] * $row['TOTAL_SET_QNTY'];
				$item_summary_qty_array[$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['id']=$row['PO_ID'];
				$item_summary_qty_array[$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['val']+=$row['VAL'];
				$item_summary_qty_array[$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['qty']+=$row['QTY'] * $row['TOTAL_SET_QNTY'];

				// for details
				$sub_dep_qty_array[$row['BUYER_NAME']][$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['id']=$row['PO_ID'];
				$sub_dep_qty_array[$row['BUYER_NAME']][$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['val']+=$row['VAL'];
				$sub_dep_qty_array[$row['BUYER_NAME']][$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['qty']+=$row['QTY'] * $row['TOTAL_SET_QNTY'];
				$sub_dep_qty_array[$row['BUYER_NAME']][$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['val'] += $row['VAL'];
				$sub_dep_qty_array[$row['BUYER_NAME']][$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['qty'] += $row['QTY'] * $row['TOTAL_SET_QNTY'];

				$sub_dep_dtls_array[$buyer_ref_arr[$row['BUYER_NAME']]][$row['BUYER_NAME']][$row[csf('gmts_item_id')]]['val'] += $row['VAL'];
				$sub_dep_dtls_array[$buyer_ref_arr[$row['BUYER_NAME']]][$row['BUYER_NAME']][$row[csf('gmts_item_id')]]['qty'] += $row['QTY'] * $row['TOTAL_SET_QNTY'];
			
				$sub_dep_summary_qty_array[$row['BUYER_NAME']][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['id']= $row['PO_ID'];
				$sub_dep_summary_qty_array[$row['BUYER_NAME']][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['val']+= $row['VAL'];
				$sub_dep_summary_qty_array[$row['BUYER_NAME']][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['qty']+=  $row['QTY'] * $row['TOTAL_SET_QNTY'];
				$all_po_array[$row['PO_ID']]= $row['PO_ID'];
			
			
			}

			$rowspan = array();
			foreach ($sub_dep_summary_array as $ref_key => $ref_value) 
			{
				foreach ($ref_value as $buyer_key => $buyer_value) 
				{
					$rowspan[$ref_key]++;
				}
			}

			$rowspan_dtls = array();
			$rowspan_buyer = array();
			foreach ($sub_dep_dtls_array as $ref_key => $ref_value) 
			{
				foreach ($ref_value as $buyer_key => $buyer_value) 
				{
					foreach ($buyer_value as $item_key => $item_value) 
					{
						$rowspan_dtls[$ref_key]++;
						$rowspan_buyer[$ref_key][$buyer_key]++;
					}
				}
			}
			// echo "<pre>";print_r($rowspan_buyer);die();

			
			$exfactoryQry="select PO_BREAK_DOWN_ID,EX_FACTORY_QNTY from PRO_EX_FACTORY_MST where  IS_DELETED=0 and STATUS_ACTIVE=1 ".where_con_using_array($all_po_array,0,'PO_BREAK_DOWN_ID')."";
			$exfactoryQry_result = sql_select($exfactoryQry);
			$exfactory_qty_arr=array();
			foreach($exfactoryQry_result as $row){
				$exfactory_qty_arr[$row[PO_BREAK_DOWN_ID]]+=$row[EX_FACTORY_QNTY];
			}

			$sub_dep_tbl_width = $month_year_tot_width + 400;
			ob_start();
			?>
				<fieldset style="width:100%;">
					<style type="text/css">
						.tr_odd td{font-size: 12px !important;}
						.tr_even td{font-size: 14px !important;}
					</style>
					<table width="<? echo $sub_dep_tbl_width+120;?>;" cellspacing="0"  align="center">
						<tr>
							<td colspan="<? echo $colspan;?>" align="center" class="form_caption">
								<strong style="font-size:16px;">Company Name:<? echo  $company_library[$company_name] ;?></strong>
							</td>
						</tr>
						<tr class="form_caption">
							<td colspan="<? echo $colspan;?>" align="center" class="form_caption"> <strong style="font-size:15px;">Month Wise Quantity (Pcs) and Value</strong></td>
						</tr>
						<tr align="center">
							<td colspan="<? echo $colspan;?>" align="center" class="form_caption"> <strong style="font-size:15px;">Date Type : <? echo ($date_type==1) ? "Shipment Date (RFI)": "Original Ship Date";?></strong></td>
						</tr>
							<tr align="center">
								<td colspan="<? echo $colspan;?>" align="center" class="form_caption"> <strong style="font-size:15px;">Runtime : <? echo date("j F, Y h:i:s a");?></strong></td>
							</tr>
					</table>
			<?
			if($show_value) // report generate with value
			{
				?>
					<!-- ===================================================================================================/
					/ 											BUYER SUMMARY PART 										  	/
					/==================================================================================================== -->
					<div id="1st_part">
						<div style="width:<? echo $sub_dep_tbl_width+20;?>px;">  
							<table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
								<caption style="text-align:left;"><h2>Buyer wise Summary</h2></caption>
								<thead>
									<tr>
										<th rowspan="2" width="100">Buyer Ref.</th>
										<th rowspan="2" width="100">Buyer</th>
										<th rowspan="2" width="100">Quantity(pcs)/Value</th>
										<?
										foreach ($year_month_count as $key => $val) 
										{
											?>
											<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
											<?
										}
										?>
										<th rowspan="2" width="100">Grand Total</th>
									</tr>
									<tr>
										<?
										foreach ($date_range as $key => $val) 
										{
											?>
											<th width="80"><? echo $val;?></th>
											<?
										}
										?>
									</tr>
								</thead>
							</table>
							<div style="width:<? echo $sub_dep_tbl_width+20;?>px;overflow-y:scroll;max-height:350px;" id="scroll_body_1">
								<table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
									<tbody>
										<?
										$i=1;
										$vr_tot_qty = 0;
										$vr_tot_val = 0;
										$vr_gr_tot_qty = 0;
										$vr_gr_tot_val = 0;
										$mon_gr_tot_array = array();
										foreach ($sub_dep_summary_array as $ref_key => $ref_val) 
										{
											$ref = 0;
											foreach ($ref_val as $buyer_key => $val) 
											{
												if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
												?>
												<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" class="tr_even">
													<? if($ref==0){?>
													<td valign="top" width="100"  rowspan="<? echo $rowspan[$ref_key]*2;?>"><? echo strtoupper($buyer_ref_arr[$buyer_key]);?></td>
													<? $ref++;}?>
													<td valign="top" width="100" rowspan="2"><? echo strtoupper($buyer_arr[$buyer_key]);?></td>
													<td width="100" align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
													<?                			
													$hr_mon_tot_qty = 0;
													foreach ($date_range as $key => $val2) 
													{
														
														$exfactoryQty=0;
														if(str_replace("'","",$cbo_shipping_status)==2){
															foreach($sub_dep_summary_qty_array[$buyer_key][$val2]['PO_ID'] as $po_id){
																$exfactoryQty+=$exfactory_qty_arr[$po_id['id']];
															}
														}
														
														?>
														<td width="80" align="right"><? echo number_format(($sub_dep_summary_qty_array[$buyer_key][$val2]['qty']-$exfactoryQty),0);?></td>
														<?
														$hr_mon_tot_qty += $sub_dep_summary_qty_array[$buyer_key][$val2]['qty']-$exfactoryQty;
														$vr_gr_tot_qty += $sub_dep_summary_qty_array[$buyer_key][$val2]['qty']-$exfactoryQty;
														$mon_gr_tot_array[$val2]['qty'] += $sub_dep_summary_qty_array[$buyer_key][$val2]['qty']-$exfactoryQty;
													}
													?>
													<td width="100" align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
												</tr>
												<tr class="tr_odd">
												<td width="100" align="right">Value<? //echo number_format($val['val'],2);?></td>	
												<?
												$hr_mon_tot_val = 0;
												foreach ($date_range as $key2 => $val3) 
												{
													$exfactoryVal=0;
													$ave_rate=0;
													if(str_replace("'","",$cbo_shipping_status)==2){
														foreach($sub_dep_summary_qty_array[$buyer_key][$val3]['PO_ID'] as $po_id){
															$ave_rate=$po_id['val']/$po_id['qty'];
															$exfactoryVal+=$exfactory_qty_arr[$po_id['id']]*$ave_rate;
														}
													}
													?>
													<td width="80" align="right">$ <? echo number_format($sub_dep_summary_qty_array[$buyer_key][$val3]['val']-$exfactoryVal,2);?></td>
													<?
													$hr_mon_tot_val += $sub_dep_summary_qty_array[$buyer_key][$val3]['val']-$exfactoryVal;
													$vr_gr_tot_val += $sub_dep_summary_qty_array[$buyer_key][$val3]['val']-$exfactoryVal;
													$mon_gr_tot_array[$val3]['val'] += $sub_dep_summary_qty_array[$buyer_key][$val3]['val']-$exfactoryVal;
												}
												?>
												<td width="100" align="right">$ <? echo number_format($hr_mon_tot_val,2);?></td>
											</tr>
												<?
												$i++;
												$exfactoryVal=0;
												$vr_tot_qty += $val['qty'];
												$vr_tot_val += $val['val'];
											}
										}
										?>
									</tbody>	                
								</table>
							</div>
							<table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
								<tfoot>
									<tr>
										<th width="100" rowspan="2"></th>
										<th width="100" rowspan="2">Grand Total</th>
										<th width="100"><? //echo number_format($vr_tot_qty,0);?></th>
										<?
										foreach ($date_range as $key2 => $val4) 
										{
											?>
											<th width="80" align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
											<?
										}
										?>
										<th><? echo number_format($vr_gr_tot_qty,0);?></th>
									</tr>
									<tr>
										<th><? //echo number_format($vr_tot_val,2);?></th>
										<?
										foreach ($date_range as $key2 => $val5) 
										{
											?>
											<th width="80" align="right">$ <? echo number_format($mon_gr_tot_array[$val5]['val'],2);?></th>
											<?
										}
										?>
										<th>$ <? echo number_format($vr_gr_tot_val,2);?></th>
									</tr>
								</tfoot>
							</table>
						</div>        
					</div>
					<!-- ===================================================================================================/
					/ 											ITEM WISE SUMMARY PART									  	/
					/==================================================================================================== -->
					<div id="2nd_part">
						<div style="margin-top: 20px;width:<? echo $sub_dep_tbl_width+20;?>px;" id="">            
							
							<table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
								<caption style="text-align:left;"><h2>Item wise Summary</h2></caption>
								<thead>
									<tr>
										<th rowspan="2" width="100">Item</th>
										<th rowspan="2" width="100">Quantity(pcs)/Value</th>
										<?
										foreach ($year_month_count as $key => $val) 
										{
											?>
											<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
											<?
										}
										?>
										<th rowspan="2" width="100">Grand Total</th>
									</tr>
									<tr>
										<?
										foreach ($date_range as $key => $val) 
										{
											?>
											<th width="80"><? echo $val;?></th>
											<?
										}
										?>
									</tr>
								</thead>
							</table>
							<div style="width:<? echo $sub_dep_tbl_width+20;?>px;overflow-y:scroll;max-height:350px;" id="scroll_body_2">
								<table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
									<tbody>
										<?
										$k=1;
										$vr_tot_qty = 0;
										$vr_tot_val = 0;
										$vr_gr_tot_qty = 0;
										$vr_gr_tot_val = 0;
										$mon_gr_tot_array = array();
										foreach ($item_summary_array as $item_key => $val) 
										{
											$item_arr = explode(',', $item_key);
											$items_str = '';

											foreach ($item_arr as $item_id) {
												$items_str .= $garments_item[$item_id] . ', ';
											}

											$items_str = rtrim($items_str, ' ,');

											if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
											?>
											<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" class="tr_even">
												<td valign="top" width="100" rowspan="2"><? echo strtoupper($items_str); ?></td>
												<td width="100" align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
												<?                			
												$hr_mon_tot_qty = 0;
												foreach ($date_range as $key => $val2) 
												{
													$exfactoryQty2=0;
													if(str_replace("'","",$cbo_shipping_status)==2){
														foreach($item_summary_qty_array[$item_key][$val2]['PO_ID'] as $po_id){
															$exfactoryQty2+=$exfactory_qty_arr[$po_id['id']];
														}
													}
													?>
													<td width="80" align="right"><? echo number_format($item_summary_qty_array[$item_key][$val2]['qty']-$exfactoryQty2,0);?></td>
													<?
													$hr_mon_tot_qty += $item_summary_qty_array[$item_key][$val2]['qty']-$exfactoryQty2;
													$vr_gr_tot_qty += $item_summary_qty_array[$item_key][$val2]['qty']-$exfactoryQty2;
													$mon_gr_tot_array[$val2]['qty'] += $item_summary_qty_array[$item_key][$val2]['qty']-$exfactoryQty2;
												}
												?>
												<td width="100" align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
											</tr>
											<tr class="tr_odd">
											<td width="100" align="right">Value<? //echo number_format($val['val'],2);?></td>	
											<?
											$hr_mon_tot_val = 0;
											foreach ($date_range as $key2 => $val3) 
											{
												$exfactoryVal2=0;
												$ave_rate2=1;

												if(str_replace("'","",$cbo_shipping_status)==2){
													foreach($item_summary_qty_array[$item_key][$val3]['PO_ID'] as $po_id){
														$ave_rate2=$po_id['val']/$po_id['qty'];
														$exfactoryVal2+=$exfactory_qty_arr[$po_id['id']]*$ave_rate2;
													}
												}
												?>
												<td width="80" align="right">$ <? echo number_format($item_summary_qty_array[$item_key][$val3]['val']-$exfactoryVal2,2);?></td>
												<?
												$hr_mon_tot_val += $item_summary_qty_array[$item_key][$val3]['val']-$exfactoryVal2;
												$vr_gr_tot_val += $item_summary_qty_array[$item_key][$val3]['val']-$exfactoryVal2;
												$mon_gr_tot_array[$val3]['val'] += $item_summary_qty_array[$item_key][$val3]['val']-$exfactoryVal2;
											}
											?>
											<td width="100" align="right">$ <? echo number_format($hr_mon_tot_val,2);?></td>
										</tr>
											<?
											$i++;
											$k++;

											$vr_tot_qty += $val['qty'];
											$vr_tot_val += $val['val'];
										}
										?>
									</tbody>
								</table>
							</div>
							<table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
								<tfoot>
									<tr>
										<th width="100" rowspan="2">Grand Total</th>
										<th width="100"><? //echo number_format($vr_tot_qty,0);?></th>
										<?
										foreach ($date_range as $key2 => $val4) 
										{
											?>
											<th width="80" align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
											<?
										}
										?>
										<th><? echo number_format($vr_gr_tot_qty,0);?></th>
									</tr>
									<tr>
										<th><? //echo number_format($vr_tot_val,2);?></th>
										<?
										foreach ($date_range as $key2 => $val5) 
										{
											?>
											<th width="80" align="right">$ <? echo number_format($mon_gr_tot_array[$val5]['val'],2);?></th>
											<?
										}
										?>
										<th width="100">$ <? echo number_format($vr_gr_tot_val,2);?></th>
									</tr>
								</tfoot>
							</table>            
						</div>
					</div>

					<!-- ===================================================================================================/
					/ 												DETAILS PART									  		/
					/==================================================================================================== -->
					<div id="3rd_part">
						<div style="margin-top: 20px;width:<? echo $sub_dep_tbl_width+120;?>px;" id="">            
							
							<table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
								<caption style="text-align:left;"><h2>Details</h2></caption>
								<thead>
									<tr>
										<th rowspan="2" width="100">Buyer Ref.</th>
										<th rowspan="2" width="100">Buyer</th>
										<th rowspan="2" width="100">Item</th>
										<th rowspan="2" width="100">Quantity(pcs)/Value</th>
										<?
										foreach ($year_month_count as $key => $val) 
										{
											?>
											<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
											<?
										}
										?>
										<th rowspan="2" width="100">Grand Total</th>
									</tr>
									<tr>
										<?
										foreach ($date_range as $key => $val) 
										{
											?>
											<th width="80"><? echo $val;?></th>
											<?
										}
										?>
									</tr>
								</thead>
							</table>
							<div style="width:<? echo $sub_dep_tbl_width+120;?>px;overflow-y:scroll;max-height:350px;" id="scroll_body_3">
								<table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
									<tbody>
										<?
										$k=1;
										$vr_tot_qty = 0;
										$vr_tot_val = 0;
										$vr_gr_tot_qty = 0;
										$vr_gr_tot_val = 0;
										$mon_gr_tot_array = array();
										foreach ($sub_dep_dtls_array as $ref_key => $ref_val) 
										{	
											$ref = 0;
											foreach ($ref_val as $buyer_key => $buyer_val) 
											{
												$byr = 0;
												foreach ($buyer_val as $item_key => $val) 
												{
													$item_arr = explode(',', $item_key);
													$items_str = '';

													foreach ($item_arr as $item_id) {
														$items_str .= $garments_item[$item_id] . ', ';
													}

													$items_str = rtrim($items_str, ' ,');

													if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
													?>
													<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" class="tr_even">
														<? if($ref==0){?>
														<td valign="top" width="100" rowspan="<? echo $rowspan_dtls[$ref_key]*2;?>"><? echo strtoupper($buyer_ref_arr[$buyer_key]);?></td>
														<? $ref++;} if($byr==0){?>
														<td valign="top" width="100" rowspan="<? echo $rowspan_buyer[$ref_key][$buyer_key]*2;?>"><? echo strtoupper($buyer_arr[$buyer_key]);?></td>
														<? $byr++;}?>
														<td width="100" rowspan="2"><? echo strtoupper($items_str); ?></td>
														<td width="100" align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
														<?                			
														$hr_mon_tot_qty = 0;
														foreach ($date_range as $key => $val2) 
														{
															$exfactoryQty3=0;
															if(str_replace("'","",$cbo_shipping_status)==2){
																foreach($sub_dep_qty_array[$buyer_key][$item_key][$val2]['PO_ID'] as $po_id){
																	$exfactoryQty3+=$exfactory_qty_arr[$po_id['id']];
																}
															}
															?>
															<td width="80" align="right"><? echo number_format($sub_dep_qty_array[$buyer_key][$item_key][$val2]['qty']-$exfactoryQty3,0);?></td>
															<?
															$hr_mon_tot_qty += $sub_dep_qty_array[$buyer_key][$item_key][$val2]['qty']-$exfactoryQty3;
															$vr_gr_tot_qty += $sub_dep_qty_array[$buyer_key][$item_key][$val2]['qty']-$exfactoryQty3;
															$mon_gr_tot_array[$val2]['qty'] += $sub_dep_qty_array[$buyer_key][$item_key][$val2]['qty']-$exfactoryQty3;
														}
														?>
														<td width="100" align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
													</tr>
													<tr class="tr_odd">
													<td width="100" align="right">Value<? //echo number_format($val['val'],2);?></td>	
													<?
													$hr_mon_tot_val = 0;
													foreach ($date_range as $key2 => $val3) 
													{
														$exfactoryVal3=0;
														$ave_rate3=1;
														if(str_replace("'","",$cbo_shipping_status)==2){
															foreach($sub_dep_qty_array[$buyer_key][$item_key][$val3]['PO_ID'] as $po_id){
																$ave_rate3=$po_id['val']/$po_id['qty'];
															$exfactoryVal3+=$exfactory_qty_arr[$po_id['id']]*$ave_rate3;
															}
														}
														?>
														<td width="80" align="right">$ <? echo number_format($sub_dep_qty_array[$buyer_key][$item_key][$val3]['val']-$exfactoryVal3,2);?></td>
														<?
														$hr_mon_tot_val += $sub_dep_qty_array[$buyer_key][$item_key][$val3]['val']-$exfactoryVal3;
														$vr_gr_tot_val += $sub_dep_qty_array[$buyer_key][$item_key][$val3]['val']-$exfactoryVal3;
														$mon_gr_tot_array[$val3]['val'] += $sub_dep_qty_array[$buyer_key][$item_key][$val3]['val']-$exfactoryVal3;
													}
													?>
													<td width="100" align="right">$ <? echo number_format($hr_mon_tot_val,2);?></td>
												</tr>
													<?
													$i++;
													$k++;

													$vr_tot_qty += $val['qty'];
													$vr_tot_val += $val['val'];
												}
											}
										}
										?>
									</tbody>
								</table>
							</div>
							<table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
								<tfoot>
									<tr>
										<th width="100" rowspan="2"></th>
										<th width="100" rowspan="2"></th>
										<th width="100" rowspan="2">Grand Total</th>
										<th width="100"><? //echo number_format($vr_tot_qty,0);?></th>
										<?
										foreach ($date_range as $key2 => $val4) 
										{
											?>
											<th width="80" align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
											<?
										}
										?>
										<th><? echo number_format($vr_gr_tot_qty,0);?></th>
									</tr>
									<tr>
										<th><? //echo number_format($vr_tot_val,2);?></th>
										<?
										foreach ($date_range as $key2 => $val5) 
										{
											?>
											<th width="80" align="right">$ <? echo number_format($mon_gr_tot_array[$val5]['val'],2);?></th>
											<?
										}
										?>
										<th width="100">$ <? echo number_format($vr_gr_tot_val,2);?></th>
									</tr>
								</tfoot>
							</table>            
						</div>
					</div>
				</fieldset>
				<?
			}
			else // report generate without value
			{

				?>
					<!-- ===================================================================================================/
					/ 										BUYER WISE SUMMARY PART										  	/
					/==================================================================================================== -->
					<div id="1st_part">
						<div style="width:<? echo $sub_dep_tbl_width+20;?>px;">  
							<table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
								<caption style="text-align:left;"><h2>Buyer wise Summary</h2></caption>
								<thead>
									<tr>
										<th rowspan="2" width="100">Buyer Ref.</th>
										<th rowspan="2" width="100">Buyer</th>
										<th rowspan="2" width="100">Quantity(pcs)</th>
										<?
										foreach ($year_month_count as $key => $val) 
										{
											?>
											<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
											<?
										}
										?>
										<th rowspan="2" width="100">Grand Total</th>
									</tr>
									<tr>
										<?
										foreach ($date_range as $key => $val) 
										{
											?>
											<th width="80"><? echo $val;?></th>
											<?
										}
										?>
									</tr>
								</thead>
							</table>
							<div style="width:<? echo $sub_dep_tbl_width+20;?>px;overflow-y:scroll;max-height:350px;" id="scroll_body_1">
								<table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
									<tbody>
										<?
										$i=1;
										$vr_tot_qty = 0;
										$vr_tot_val = 0;
										$vr_gr_tot_qty = 0;
										$vr_gr_tot_val = 0;
										$mon_gr_tot_array = array();
										foreach ($sub_dep_summary_array as $ref_key => $ref_val) 
										{	
											$ref = 0;
											foreach ($ref_val as $buyer_key => $val) 
											{
												
												if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
												?>
												<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" style="font-size:12px">
													<? if($ref==0){?>
													<td valign="top" width="100" rowspan="<? echo $rowspan[$ref_key];?>" ><? echo strtoupper($buyer_ref_arr[$buyer_key]);?></td>
													<? $ref++;}?>
													<td width="100"><? echo strtoupper($buyer_arr[$buyer_key]);?></td>
													<td width="100" align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
													<?                			
													$hr_mon_tot_qty = 0;
													foreach ($date_range as $key => $val2) 
													{
														
														$exfactoryQty=0;
														if(str_replace("'","",$cbo_shipping_status)==2){
															foreach($sub_dep_summary_qty_array[$buyer_key][$val2]['PO_ID'] as $po_id){
															$exfactoryQty+=$exfactory_qty_arr[$po_id['id']];
															}
														}

														?>
														<td width="80" align="right"><? echo number_format(($sub_dep_summary_qty_array[$buyer_key][$val2]['qty']-$exfactoryQty),0);?></td>
														<?
														$hr_mon_tot_qty += $sub_dep_summary_qty_array[$buyer_key][$val2]['qty']-$exfactoryQty;
														$vr_gr_tot_qty += $sub_dep_summary_qty_array[$buyer_key][$val2]['qty']-$exfactoryQty;
														$mon_gr_tot_array[$val2]['qty'] += $sub_dep_summary_qty_array[$buyer_key][$val2]['qty']-$exfactoryQty;
													}
													?>
													<td width="100" align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
												</tr>
												<?
												$i++;

												$vr_tot_qty += $val['qty'];
												$vr_tot_val += $val['val'];
											}
										}
										?>
									</tbody>	                
								</table>
							</div>
							<table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
								<tfoot>
									<tr>
										<th width="100"></th>
										<th width="100">Grand Total</th>
										<th width="100"><? //echo number_format($vr_tot_qty,0);?></th>
										<?
										foreach ($date_range as $key2 => $val4) 
										{
											?>
											<th width="80" align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
											<?
										}
										?>
										<th><? echo number_format($vr_gr_tot_qty,0);?></th>
									</tr>
								</tfoot>
							</table>
						</div>        
					</div>
					<!-- ===================================================================================================/
					/ 											ITEM WISE SUMMARY PART									  	/
					/==================================================================================================== -->
					<div id="2nd_part">
						<div style="margin-top: 20px;width:<? echo $sub_dep_tbl_width+20;?>px;" id="">            
							
							<table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
								<caption style="text-align:left;"><h2>Item wise Summary</h2></caption>
								<thead>
									<tr>
										<th rowspan="2" width="100">Item</th>
										<th rowspan="2" width="100">Quantity(pcs)</th>
										<?
										foreach ($year_month_count as $key => $val) 
										{
											?>
											<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
											<?
										}
										?>
										<th rowspan="2" width="100">Grand Total</th>
									</tr>
									<tr>
										<?
										foreach ($date_range as $key => $val) 
										{
											?>
											<th width="80"><? echo $val;?></th>
											<?
										}
										?>
									</tr>
								</thead>
							</table>
							<div style="width:<? echo $sub_dep_tbl_width+20;?>px;overflow-y:scroll;max-height:350px;" id="scroll_body_2">
								<table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
									<tbody>
										<?
										$k=1;
										$vr_tot_qty = 0;
										$vr_tot_val = 0;
										$vr_gr_tot_qty = 0;
										$vr_gr_tot_val = 0;
										$mon_gr_tot_array = array();
										foreach ($item_summary_array as $item_key => $val) 
										{
											if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
											?>
											<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" style="font-size:12px">
												<td width="100"><? echo $garments_item[$item_key];?></td>
												<td width="100" align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
												<?                			
												$hr_mon_tot_qty = 0;
												foreach ($date_range as $key => $val2) 
												{
													$exfactoryQty2=0;
													if(str_replace("'","",$cbo_shipping_status)==2){
														foreach($item_summary_qty_array[$item_key][$val2]['PO_ID'] as $po_id){
															$exfactoryQty2+=$exfactory_qty_arr[$po_id['id']];
														}
													}
													?>
													<td width="80" align="right"><? echo number_format($item_summary_qty_array[$item_key][$val2]['qty']-$exfactoryQty2,0);?></td>
													<?
													$hr_mon_tot_qty += $item_summary_qty_array[$item_key][$val2]['qty']-$exfactoryQty2;
													$vr_gr_tot_qty += $item_summary_qty_array[$item_key][$val2]['qty']-$exfactoryQty2;
													$mon_gr_tot_array[$val2]['qty'] += $item_summary_qty_array[$item_key][$val2]['qty']-$exfactoryQty2;
												}
												?>
												<td width="100" align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
											</tr>
											<?
											$i++;
											$k++;

											$vr_tot_qty += $val['qty'];
											$vr_tot_val += $val['val'];
										}
										?>
									</tbody>
								</table>
							</div>
							<table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
								<tfoot>
									<tr>
										<th width="100">Grand Total</th>
										<th width="100"><? //echo number_format($vr_tot_qty,0);?></th>
										<?
										foreach ($date_range as $key2 => $val4) 
										{
											?>
											<th width="80" align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
											<?
										}
										?>
										<th width="100"><? echo number_format($vr_gr_tot_qty,0);?></th>
									</tr>
								</tfoot>
							</table>            
						</div>
					</div>

					<!-- ===================================================================================================/
					/ 												DETAILS PART									  		/
					/==================================================================================================== -->
					<div id="3rd_part">
						<div style="margin-top: 20px;width:<? echo $sub_dep_tbl_width+120;?>px;" id="">            
							
							<table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
								<caption style="text-align:left;"><h2>Details</h2></caption>
								<thead>
									<tr>
										<th rowspan="2" width="100">Buyer Ref.</th>
										<th rowspan="2" width="100">Buyer</th>
										<th rowspan="2" width="100">Item</th>
										<th rowspan="2" width="100">Quantity(pcs)</th>
										<?
										foreach ($year_month_count as $key => $val) 
										{
											?>
											<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
											<?
										}
										?>
										<th rowspan="2" width="100">Grand Total</th>
									</tr>
									<tr>
										<?
										foreach ($date_range as $key => $val) 
										{
											?>
											<th width="80"><? echo $val;?></th>
											<?
										}
										?>
									</tr>
								</thead>
							</table>
							<div style="width:<? echo $sub_dep_tbl_width+120;?>px;overflow-y:scroll;max-height:350px;" id="scroll_body_3">
								<table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
									<tbody>
										<?
										$k=1;
										$vr_tot_qty = 0;
										$vr_tot_val = 0;
										$vr_gr_tot_qty = 0;
										$vr_gr_tot_val = 0;
										$mon_gr_tot_array = array();
										foreach ($sub_dep_dtls_array as $ref_key => $ref_val) 
										{
											$ref = 0;
											foreach ($ref_val as $buyer_key => $buyer_val) 
											{
												$byr = 0;
												foreach ($buyer_val as $item_key => $val) 
												{
													if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
													?>
													<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" class="tr_even">
														<? if($ref==0){?>
														<td valing="top" width="100" rowspan="<? echo $rowspan_dtls[$ref_key];?>"><? echo strtoupper($buyer_ref_arr[$buyer_key]);?></td>
														<? $ref++;} if($byr==0){?>
														<td valing="top" width="100" rowspan="<? echo $rowspan_buyer[$ref_key][$buyer_key];?>"><? echo strtoupper($buyer_arr[$buyer_key]);?></td>
														<? $byr++;}?>
														<td width="100"><? echo $garments_item[$item_key];?></td>
														<td width="100" align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
														<?                			
														$hr_mon_tot_qty = 0;
														foreach ($date_range as $key => $val2) 
														{
															$exfactoryQty3=0;
															if(str_replace("'","",$cbo_shipping_status)==2){
																foreach($sub_dep_qty_array[$buyer_key][$item_key][$val2]['PO_ID'] as $po_id){
																	$exfactoryQty3+=$exfactory_qty_arr[$po_id['id']];
																}
															}
															?>
															<td width="80" align="right"><? echo number_format($sub_dep_qty_array[$buyer_key][$item_key][$val2]['qty']-$exfactoryQty3,0);?></td>
															<?
															$hr_mon_tot_qty += $sub_dep_qty_array[$buyer_key][$item_key][$val2]['qty']-$exfactoryQty3;
															$vr_gr_tot_qty += $sub_dep_qty_array[$buyer_key][$item_key][$val2]['qty']-$exfactoryQty3;
															$mon_gr_tot_array[$val2]['qty'] += $sub_dep_qty_array[$buyer_key][$item_key][$val2]['qty']-$exfactoryQty3;
														}
														?>
														<td width="100" align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
													</tr>
													<?
													$i++;
													$k++;

													$vr_tot_qty += $val['qty'];
													$vr_tot_val += $val['val'];
												}
											}
										}
										?>
									</tbody>
								</table>
							</div>
							<table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
								<tfoot>
									<tr>
										<th width="100"></th>
										<th width="100"></th>
										<th width="100">Grand Total</th>
										<th width="100"><? //echo number_format($vr_tot_qty,0);?></th>
										<?
										foreach ($date_range as $key2 => $val4) 
										{
											?>
											<th width="80" align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
											<?
										}
										?>
										<th width="100"><? echo number_format($vr_gr_tot_qty,0);?></th>
									</tr>
								</tfoot>
							</table>            
						</div>
					</div>
				</fieldset>
				<?
			}
		}
	}else{
		
		// =============================================== MAIN QUERY =========================================
		/*$sql = "SELECT a.BUYER_NAME,a.PRO_SUB_DEP,a.TOTAL_SET_QNTY,to_char(b.shipment_date,'MON-YYYY') as MONTH_YEAR, SUM(c.ORDER_QUANTITY) AS QTY,sum(c.ORDER_TOTAL) as VAL,c.ITEM_NUMBER_ID
		 from wo_po_details_master a,wo_po_break_down b,wo_po_color_size_breakdown c where a.id=b.job_id and a.id=c.job_id  $sql_cond  and a.status_active=1 and a.is_deleted=0 and b.status_active in(1,2,3) and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.id=c.po_break_down_id group by a.buyer_name,a.pro_sub_dep,b.id,b.shipment_date,c.item_number_id,a.total_set_qnty";*/

		$sql = "SELECT b.id as PO_ID,a.BUYER_NAME,a.COMPANY_NAME, a.PRO_SUB_DEP, a.TOTAL_SET_QNTY, TO_CHAR (b.pub_shipment_date, 'MON-YYYY') AS MONTH_YEAR, sum(B.PO_QUANTITY) as qty, sum(B.PO_TOTAL_PRICE) as val, A.GMTS_ITEM_ID
    	FROM wo_po_details_master a, wo_po_break_down b
   		WHERE a.id = b.job_id $sql_cond AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active IN (1) AND b.is_deleted = 0
		GROUP BY a.buyer_name, a.pro_sub_dep, b.id, b.pub_shipment_date, a.total_set_qnty, A.GMTS_ITEM_ID,a.COMPANY_NAME";

	 //echo $sql;
		$sql_res = sql_select($sql);
		if (count($sql_res) < 1)
		{
			echo '<div style="text-align: center;color: red;font-weight: bold;font-size: 20px;">Data not found.</div>';
			disconnect($con);
			die();
		}

		$sub_dep_dtls_array = array();
		$sub_dep_qty_array = array();
		$sub_dep_summary_array = array();
		$sub_dep_summary_qty_array = array();
		$item_summary_array = array();
		$item_summary_qty_array = array();
		$count = 1;
		foreach ($sql_res as $row)
		{
			// for summary
			$sub_dep_summary_array[$company_arr[$row['COMPANY_NAME']]][$row['BUYER_NAME']]['val'] += $row['VAL'];
			$sub_dep_summary_array[$company_arr[$row['COMPANY_NAME']]][$row['BUYER_NAME']]['qty'] += $row['QTY'] * $row['TOTAL_SET_QNTY'];

			$sub_dep_summary_qty_array[$company_arr[$row['COMPANY_NAME']]][$row['BUYER_NAME']][$row['MONTH_YEAR']]['val'] += $row['VAL'];
			$sub_dep_summary_qty_array[$company_arr[$row['COMPANY_NAME']]][$row['BUYER_NAME']][$row['MONTH_YEAR']]['qty'] += $row['QTY'] * $row['TOTAL_SET_QNTY'];

			// for item wise summary
			$item_summary_array[$company_arr[$row['COMPANY_NAME']]][$row[csf('gmts_item_id')]]['val'] += $row['VAL'];
			$item_summary_array[$company_arr[$row['COMPANY_NAME']]][$row[csf('gmts_item_id')]]['qty'] += $row['QTY'] * $row['TOTAL_SET_QNTY'];

			$item_summary_qty_array[$company_arr[$row['COMPANY_NAME']]][$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['val'] += $row['VAL'];
			$item_summary_qty_array[$company_arr[$row['COMPANY_NAME']]][$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['qty'] += $row['QTY'] * $row['TOTAL_SET_QNTY'];
			$item_summary_qty_array[$company_arr[$row['COMPANY_NAME']]][$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['id']=$row['PO_ID'];
			$item_summary_qty_array[$company_arr[$row['COMPANY_NAME']]][$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['val']+=$row['VAL'];
			$item_summary_qty_array[$company_arr[$row['COMPANY_NAME']]][$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['qty']+=$row['QTY'] * $row['TOTAL_SET_QNTY'];

			// for details
			$sub_dep_qty_array[$company_arr[$row['COMPANY_NAME']]][$row['BUYER_NAME']][$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['id']=$row['PO_ID'];
			$sub_dep_qty_array[$company_arr[$row['COMPANY_NAME']]][$row['BUYER_NAME']][$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['val']+=$row['VAL'];
			$sub_dep_qty_array[$company_arr[$row['COMPANY_NAME']]][$row['BUYER_NAME']][$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['qty']+=$row['QTY'] * $row['TOTAL_SET_QNTY'];
			$sub_dep_qty_array[$company_arr[$row['COMPANY_NAME']]][$row['BUYER_NAME']][$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['val'] += $row['VAL'];
			$sub_dep_qty_array[$company_arr[$row['COMPANY_NAME']]][$row['BUYER_NAME']][$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['qty'] += $row['QTY'] * $row['TOTAL_SET_QNTY'];

			$sub_dep_dtls_array[$company_arr[$row['COMPANY_NAME']]][$row['BUYER_NAME']][$row[csf('gmts_item_id')]]['val'] += $row['VAL'];
			$sub_dep_dtls_array[$company_arr[$row['COMPANY_NAME']]][$row['BUYER_NAME']][$row[csf('gmts_item_id')]]['qty'] += $row['QTY'] * $row['TOTAL_SET_QNTY'];
		
			$sub_dep_summary_qty_array[$company_arr[$row['COMPANY_NAME']]][$row['BUYER_NAME']][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['id']= $row['PO_ID'];
			$sub_dep_summary_qty_array[$company_arr[$row['COMPANY_NAME']]][$row['BUYER_NAME']][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['val']+= $row['VAL'];
			$sub_dep_summary_qty_array[$company_arr[$row['COMPANY_NAME']]][$row['BUYER_NAME']][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['qty']+=  $row['QTY'] * $row['TOTAL_SET_QNTY'];
			$all_po_array[$row['PO_ID']]= $row['PO_ID'];
		
		
		}

		$rowspan = array();
		foreach ($sub_dep_summary_array as $ref_key => $ref_value) 
		{
			foreach ($ref_value as $buyer_key => $buyer_value) 
			{
				$rowspan[$ref_key]++;
			}
		}

		$rowspan_dtls = array();
		$rowspan_buyer = array();
		foreach ($sub_dep_dtls_array as $ref_key => $ref_value) 
		{
			foreach ($ref_value as $buyer_key => $buyer_value) 
			{
				foreach ($buyer_value as $item_key => $item_value) 
				{
					$rowspan_dtls[$ref_key]++;
					$rowspan_buyer[$ref_key][$buyer_key]++;
				}
			}
		}

		$rowspan_item = array();
		$rowspan_item_val = array();
		foreach ($item_summary_array as $comp_key => $item_data){
			foreach ($item_data as $item_key => $val){
				$rowspan_item[$comp_key]++;
			
			}}
		// echo "<pre>";print_r($rowspan_buyer);die();

		
		$exfactoryQry="select PO_BREAK_DOWN_ID,EX_FACTORY_QNTY from PRO_EX_FACTORY_MST where  IS_DELETED=0 and STATUS_ACTIVE=1 ".where_con_using_array($all_po_array,0,'PO_BREAK_DOWN_ID')."";
		$exfactoryQry_result = sql_select($exfactoryQry);
		$exfactory_qty_arr=array();
		foreach($exfactoryQry_result as $row){
			$exfactory_qty_arr[$row[PO_BREAK_DOWN_ID]]+=$row[EX_FACTORY_QNTY];
		}

		$sub_dep_tbl_width = $month_year_tot_width + 400;
		ob_start();
		?>
		    <fieldset style="width:100%;">
		    	<style type="text/css">
		    		.tr_odd td{font-size: 12px !important;}
		    		.tr_even td{font-size: 14px !important;}
		    	</style>
		        <table width="<? echo $sub_dep_tbl_width+120;?>;" cellspacing="0"  align="center">
		            <tr>
		                <td colspan="<? echo $colspan;?>" align="center" class="form_caption">
		                    <strong style="font-size:16px;">Company Name:<? echo  $company_library[$company_name] ;?></strong>
		                </td>
		            </tr>
		            <tr class="form_caption">
		                <td colspan="<? echo $colspan;?>" align="center" class="form_caption"> <strong style="font-size:15px;">Month Wise Quantity (Pcs) and Value</strong></td>
		            </tr>
		            <tr align="center">
		                <td colspan="<? echo $colspan;?>" align="center" class="form_caption"> <strong style="font-size:15px;">Date Type : <? echo ($date_type==1) ? "Shipment Date (RFI)": "Original Ship Date";?></strong></td>
		            </tr>
			            <tr align="center">
			                <td colspan="<? echo $colspan;?>" align="center" class="form_caption"> <strong style="font-size:15px;">Runtime : <? echo date("j F, Y h:i:s a");?></strong></td>
			            </tr>
		       	</table>
		<?
		if($show_value) // report generate with value
		{
			?>
		        <!-- ===================================================================================================/
		        / 											BUYER SUMMARY PART 										  	/
		        /==================================================================================================== -->
		        <div id="1st_part">
			        <div style="width:<? echo $sub_dep_tbl_width+20;?>px;">  
			            <table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
			            	<caption style="text-align:left;"><h2>Buyer wise Summary</h2></caption>
			                <thead>
			                	<tr>
				                    <th rowspan="2" width="100">Company</th>
				                    <th rowspan="2" width="100">Buyer</th>
				                    <th rowspan="2" width="100">Quantity(pcs)/Value</th>
				                    <?
				                    foreach ($year_month_count as $key => $val) 
				                    {
				                    	?>
				                    	<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
				                    	<?
				                    }
				                    ?>
				                    <th rowspan="2" width="100">Grand Total</th>
				                </tr>
				                <tr>
				                	<?
				                	foreach ($date_range as $key => $val) 
				                	{
				                		?>
				                		<th width="80"><? echo $val;?></th>
				                		<?
				                	}
				                	?>
				                </tr>
			                </thead>
			            </table>
			            <div style="width:<? echo $sub_dep_tbl_width+20;?>px;overflow-y:scroll;max-height:350px;" id="scroll_body_1">
				            <table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
				                <tbody>
				                	<?
				                	$i=1;
				                	$vr_tot_qty = 0;
				                	$vr_tot_val = 0;
				                	$vr_gr_tot_qty = 0;
				                	$vr_gr_tot_val = 0;
				                	$mon_gr_tot_array = array();
				                	foreach ($sub_dep_summary_array as $comp_key => $ref_val) 
				                	{
				                		$ref = 0;
				                		foreach ($ref_val as $buyer_key => $val) 
				                		{
					                		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					                		?>
					                		<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" class="tr_even">
					                			<? if($ref==0){?>
					                			<td valign="top" width="100"  rowspan="<? echo $rowspan[$comp_key]*2;?>"><? echo strtoupper($comp_key);?></td>
					                			<? $ref++;}?>
					                			<td valign="top" width="100" rowspan="2"><? echo strtoupper($buyer_arr[$buyer_key]);?></td>
					                			<td width="100" align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
					                			<?                			
					                			$hr_mon_tot_qty = 0;
							                	foreach ($date_range as $key => $val2) 
							                	{
							                		
													$exfactoryQty=0;
													if(str_replace("'","",$cbo_shipping_status)==2){
														foreach($sub_dep_summary_qty_array[$comp_key][$buyer_key][$val2]['PO_ID'] as $po_id){
															$exfactoryQty+=$exfactory_qty_arr[$po_id['id']];
														}
													}
													
													?>
							                		<td width="80" align="right"><? echo number_format(($sub_dep_summary_qty_array[$comp_key][$buyer_key][$val2]['qty']-$exfactoryQty),0);?></td>
							                		<?
							                		$hr_mon_tot_qty += $sub_dep_summary_qty_array[$comp_key][$buyer_key][$val2]['qty']-$exfactoryQty;
							                		$vr_gr_tot_qty += $sub_dep_summary_qty_array[$comp_key][$buyer_key][$val2]['qty']-$exfactoryQty;
							                		$mon_gr_tot_array[$val2]['qty'] += $sub_dep_summary_qty_array[$comp_key][$buyer_key][$val2]['qty']-$exfactoryQty;
							                	}
							                	?>
							                	<td width="100" align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
					                		</tr>
					                		<tr class="tr_odd">
					                		<td width="100" align="right">Value<? //echo number_format($val['val'],2);?></td>	
						                	<?
					                		$hr_mon_tot_val = 0;
						                	foreach ($date_range as $key2 => $val3) 
						                	{
												$exfactoryVal=0;
												$ave_rate=0;
												if(str_replace("'","",$cbo_shipping_status)==2){
													foreach($sub_dep_summary_qty_array[$comp_key][$buyer_key][$val3]['PO_ID'] as $po_id){
														$ave_rate=$po_id['val']/$po_id['qty'];
														$exfactoryVal+=$exfactory_qty_arr[$po_id['id']]*$ave_rate;
													}
												}
						                		?>
						                		<td width="80" align="right">$ <? echo number_format($sub_dep_summary_qty_array[$comp_key][$buyer_key][$val3]['val']-$exfactoryVal,2);?></td>
						                		<?
							                	$hr_mon_tot_val += $sub_dep_summary_qty_array[$comp_key][$buyer_key][$val3]['val']-$exfactoryVal;
							                	$vr_gr_tot_val += $sub_dep_summary_qty_array[$comp_key][$buyer_key][$val3]['val']-$exfactoryVal;
							                	$mon_gr_tot_array[$val3]['val'] += $sub_dep_summary_qty_array[$comp_key][$buyer_key][$val3]['val']-$exfactoryVal;
						                	}
						                	?>
						                	<td width="100" align="right">$ <? echo number_format($hr_mon_tot_val,2);?></td>
						                </tr>
					                		<?
					                		$i++;
											$exfactoryVal=0;
					                		$vr_tot_qty += $val['qty'];
					                		$vr_tot_val += $val['val'];
					                	}
				                	}
				                	?>
				                </tbody>	                
				            </table>
				        </div>
			            <table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
			                <tfoot>
			                	<tr>
			                		<th width="100" rowspan="2"></th>
			                		<th width="100" rowspan="2">Grand Total</th>
			                		<th width="100"><? //echo number_format($vr_tot_qty,0);?></th>
			                		<?
				                	foreach ($date_range as $key2 => $val4) 
				                	{
				                		?>
				                		<th width="80" align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
				                		<?
				                	}
				                	?>
				                	<th>s<? echo number_format($vr_gr_tot_qty,0);?></th>
			                	</tr>
			                	<tr>
			                		<th><? //echo number_format($vr_tot_val,2);?></th>
			                		<?
				                	foreach ($date_range as $key2 => $val5) 
				                	{
				                		?>
				                		<th width="80" align="right">$ <? echo number_format($mon_gr_tot_array[$val5]['val'],2);?></th>
				                		<?
				                	}
				                	?>
				                	<th>$ <? echo number_format($vr_gr_tot_val,2);?></th>
			                	</tr>
			                </tfoot>
			            </table>
			        </div>        
		        </div>
		        <!-- ===================================================================================================/
		        / 											ITEM WISE SUMMARY PART									  	/
		        /==================================================================================================== -->
		        <div id="2nd_part">
			        <div style="margin-top: 20px;width:<? echo $sub_dep_tbl_width+20;?>px;" id="">            
			            
			            <table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
			            	<caption style="text-align:left;"><h2>Item wise Summary</h2></caption>
			                <thead>
			                	<tr>
									<th rowspan="2" width="100">Company</th>
				                    <th rowspan="2" width="100">Item</th>
				                    <th rowspan="2" width="100">Quantity(pcs)/Value</th>
				                    <?
				                    foreach ($year_month_count as $key => $val) 
				                    {
				                    	?>
				                    	<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
				                    	<?
				                    }
				                    ?>
				                    <th rowspan="2" width="100">Grand Total</th>
				                </tr>
				                <tr>
				                	<?
				                	foreach ($date_range as $key => $val) 
				                	{
				                		?>
				                		<th width="80"><? echo $val;?></th>
				                		<?
				                	}
				                	?>
				                </tr>
			                </thead>
			            </table>
			            <div style="width:<? echo $sub_dep_tbl_width+20;?>px;overflow-y:scroll;max-height:350px;" id="scroll_body_2">
				            <table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
				                <tbody>
				                	<?
				                	$k=1;
				                	$vr_tot_qty = 0;
				                	$vr_tot_val = 0;
				                	$vr_gr_tot_qty = 0;
				                	$vr_gr_tot_val = 0;
				                	$mon_gr_tot_array = array();
				                	foreach ($item_summary_array as $comp_key => $item_data) 
				                	{
										$itemref=0;
										foreach ($item_data as $item_key => $val) 
				                	   	{
											$item_arr = explode(',', $item_key);
											$items_str = '';

											foreach ($item_arr as $item_id) {
												$items_str .= $garments_item[$item_id] . ', ';
											}

				                		$items_str = rtrim($items_str, ' ,');

				                		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				                		?>
				                		<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" class="tr_even">
											<? if($itemref==0){?>
											<td valign="top" width="100" title="<?=$rowspan_item[$comp_key];?>" rowspan="<?=$rowspan_item[$comp_key]*2;?>"><? echo strtoupper($comp_key); ?></td>
											<? $itemref++;}?>
										
				                			<td valign="top" width="100" rowspan="2"><? echo strtoupper($items_str); ?></td>
				                			<td width="100" align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
				                			<?                			
				                			$hr_mon_tot_qty = 0;
						                	foreach ($date_range as $key => $val2) 
						                	{
												$exfactoryQty2=0;
												if(str_replace("'","",$cbo_shipping_status)==2){
													foreach($item_summary_qty_array[$comp_key][$item_key][$val2]['PO_ID'] as $po_id){
														$exfactoryQty2+=$exfactory_qty_arr[$po_id['id']];
													}
												}
						                		?>
						                		<td width="80" align="right"><? echo number_format($item_summary_qty_array[$comp_key][$item_key][$val2]['qty']-$exfactoryQty2,0);?></td>
						                		<?
						                		$hr_mon_tot_qty += $item_summary_qty_array[$comp_key][$item_key][$val2]['qty']-$exfactoryQty2;
						                		$vr_gr_tot_qty += $item_summary_qty_array[$comp_key][$item_key][$val2]['qty']-$exfactoryQty2;
						                		$mon_gr_tot_array[$val2]['qty'] += $item_summary_qty_array[$comp_key][$item_key][$val2]['qty']-$exfactoryQty2;
						                	}
						                	?>
						                	<td width="100" align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
				                		</tr>
				                		<tr class="tr_odd">
									
				                		<td width="100" align="right">Value<? //echo number_format($val['val'],2);?></td>	
					                	<?
				                		$hr_mon_tot_val = 0;
					                	foreach ($date_range as $key2 => $val3) 
					                	{
											$exfactoryVal2=0;
											$ave_rate2=1;

											if(str_replace("'","",$cbo_shipping_status)==2){
												foreach($item_summary_qty_array[$comp_key][$item_key][$val3]['PO_ID'] as $po_id){
													$ave_rate2=$po_id['val']/$po_id['qty'];
													$exfactoryVal2+=$exfactory_qty_arr[$po_id['id']]*$ave_rate2;
												}
											}
					                		?>
					                		<td width="80" align="right">$ <? echo number_format($item_summary_qty_array[$comp_key][$item_key][$val3]['val']-$exfactoryVal2,2);?></td>
					                		<?
						                	$hr_mon_tot_val += $item_summary_qty_array[$comp_key][$item_key][$val3]['val']-$exfactoryVal2;
						                	$vr_gr_tot_val += $item_summary_qty_array[$comp_key][$item_key][$val3]['val']-$exfactoryVal2;
						                	$mon_gr_tot_array[$val3]['val'] += $item_summary_qty_array[$comp_key][$item_key][$val3]['val']-$exfactoryVal2;
					                	}
					                	?>
					                	<td width="100" align="right">$ <? echo number_format($hr_mon_tot_val,2);?></td>
					                </tr>
				                		<?
				                		$i++;
				                		$k++;

				                		$vr_tot_qty += $val['qty'];
				                		$vr_tot_val += $val['val'];
				                	}

								
								
										}
				                	?>
				                </tbody>
				            </table>
			            </div>
			            <table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
			                <tfoot>
			                	<tr>
			                		<th width="100" rowspan="2" colspan="2">Grand Total</th>
			                		<th width="100"><? //echo number_format($vr_tot_qty,0);?></th>
			                		<?
				                	foreach ($date_range as $key2 => $val4) 
				                	{
				                		?>
				                		<th width="80" align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
				                		<?
				                	}
				                	?>
				                	<th><? echo number_format($vr_gr_tot_qty,0);?></th>
			                	</tr>
			                	<tr>
			                		<th><? //echo number_format($vr_tot_val,2);?></th>
			                		<?
				                	foreach ($date_range as $key2 => $val5) 
				                	{
				                		?>
				                		<th width="80" align="right">$ <? echo number_format($mon_gr_tot_array[$val5]['val'],2);?></th>
				                		<?
				                	}
				                	?>
				                	<th width="100">$ <? echo number_format($vr_gr_tot_val,2);?></th>
			                	</tr>
			                </tfoot>
			            </table>            
			        </div>
			    </div>

		        <!-- ===================================================================================================/
		        / 												DETAILS PART									  		/
		        /==================================================================================================== -->
		        <div id="3rd_part">
			        <div style="margin-top: 20px;width:<? echo $sub_dep_tbl_width+120;?>px;" id="">            
			            
			            <table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
			            	<caption style="text-align:left;"><h2>Details</h2></caption>
			                <thead>
			                	<tr>
				                    <th rowspan="2" width="100">Company</th>
				                    <th rowspan="2" width="100">Buyer</th>
				                    <th rowspan="2" width="100">Item</th>
				                    <th rowspan="2" width="100">Quantity(pcs)/Value</th>
				                    <?
				                    foreach ($year_month_count as $key => $val) 
				                    {
				                    	?>
				                    	<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
				                    	<?
				                    }
				                    ?>
				                    <th rowspan="2" width="100">Grand Total</th>
				                </tr>
				                <tr>
				                	<?
				                	foreach ($date_range as $key => $val) 
				                	{
				                		?>
				                		<th width="80"><? echo $val;?></th>
				                		<?
				                	}
				                	?>
				                </tr>
			                </thead>
			            </table>
			            <div style="width:<? echo $sub_dep_tbl_width+120;?>px;overflow-y:scroll;max-height:350px;" id="scroll_body_3">
				            <table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
				                <tbody>
				                	<?
				                	$k=1;
				                	$vr_tot_qty = 0;
				                	$vr_tot_val = 0;
				                	$vr_gr_tot_qty = 0;
				                	$vr_gr_tot_val = 0;
				                	$mon_gr_tot_array = array();
				                	foreach ($sub_dep_dtls_array as $comp_key => $ref_val) 
				                	{	
				                		$ref = 0;
				                		foreach ($ref_val as $buyer_key => $buyer_val) 
				                		{
				                			$byr = 0;
					                		foreach ($buyer_val as $item_key => $val) 
					                		{
					                			$item_arr = explode(',', $item_key);
						                		$items_str = '';

						                		foreach ($item_arr as $item_id) {
						                			$items_str .= $garments_item[$item_id] . ', ';
						                		}

						                		$items_str = rtrim($items_str, ' ,');

						                		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						                		?>
						                		<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" class="tr_even">
						                			<? if($ref==0){?>
						                			<td valign="top" width="100" rowspan="<? echo $rowspan_dtls[$comp_key]*2;?>"><? echo strtoupper($comp_key);?></td>
						                			<? $ref++;} if($byr==0){?>
						                			<td valign="top" width="100" rowspan="<? echo $rowspan_buyer[$comp_key][$buyer_key]*2;?>"><? echo strtoupper($buyer_arr[$buyer_key]);?></td>
						                			<? $byr++;}?>
						                			<td width="100" rowspan="2"><? echo strtoupper($items_str); ?></td>
						                			<td width="100" align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
						                			<?                			
						                			$hr_mon_tot_qty = 0;
								                	foreach ($date_range as $key => $val2) 
								                	{
														$exfactoryQty3=0;
														if(str_replace("'","",$cbo_shipping_status)==2){
															foreach($sub_dep_qty_array[$comp_key][$buyer_key][$item_key][$val2]['PO_ID'] as $po_id){
																$exfactoryQty3+=$exfactory_qty_arr[$po_id['id']];
															}
														}
								                		?>
								                		<td width="80" align="right"><? echo number_format($sub_dep_qty_array[$comp_key][$buyer_key][$item_key][$val2]['qty']-$exfactoryQty3,0);?></td>
								                		<?
								                		$hr_mon_tot_qty += $sub_dep_qty_array[$comp_key][$buyer_key][$item_key][$val2]['qty']-$exfactoryQty3;
								                		$vr_gr_tot_qty += $sub_dep_qty_array[$comp_key][$buyer_key][$item_key][$val2]['qty']-$exfactoryQty3;
								                		$mon_gr_tot_array[$val2]['qty'] += $sub_dep_qty_array[$comp_key][$buyer_key][$item_key][$val2]['qty']-$exfactoryQty3;
								                	}
								                	?>
								                	<td width="100" align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
						                		</tr>
						                		<tr class="tr_odd">
						                		<td width="100" align="right">Value<? //echo number_format($val['val'],2);?></td>	
							                	<?
						                		$hr_mon_tot_val = 0;
							                	foreach ($date_range as $key2 => $val3) 
							                	{
													$exfactoryVal3=0;
													$ave_rate3=1;
											        if(str_replace("'","",$cbo_shipping_status)==2){
												        foreach($sub_dep_qty_array[$comp_key][$buyer_key][$item_key][$val3]['PO_ID'] as $po_id){
															$ave_rate3=$po_id['val']/$po_id['qty'];
													    $exfactoryVal3+=$exfactory_qty_arr[$po_id['id']]*$ave_rate3;
												        }
											        }
							                		?>
							                		<td width="80" align="right">$ <? echo number_format($sub_dep_qty_array[$comp_key][$buyer_key][$item_key][$val3]['val']-$exfactoryVal3,2);?></td>
							                		<?
								                	$hr_mon_tot_val += $sub_dep_qty_array[$comp_key][$buyer_key][$item_key][$val3]['val']-$exfactoryVal3;
								                	$vr_gr_tot_val += $sub_dep_qty_array[$comp_key][$buyer_key][$item_key][$val3]['val']-$exfactoryVal3;
								                	$mon_gr_tot_array[$val3]['val'] += $sub_dep_qty_array[$comp_key][$buyer_key][$item_key][$val3]['val']-$exfactoryVal3;
							                	}
							                	?>
							                	<td width="100" align="right">$ <? echo number_format($hr_mon_tot_val,2);?></td>
							                </tr>
						                		<?
						                		$i++;
						                		$k++;

						                		$vr_tot_qty += $val['qty'];
						                		$vr_tot_val += $val['val'];
						                	}
					                	}
				                	}
				                	?>
				                </tbody>
				            </table>
			            </div>
			            <table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
			                <tfoot>
			                	<tr>
			                		<th width="100" rowspan="2"></th>
			                		<th width="100" rowspan="2"></th>
			                		<th width="100" rowspan="2">Grand Total</th>
			                		<th width="100"><? //echo number_format($vr_tot_qty,0);?></th>
			                		<?
				                	foreach ($date_range as $key2 => $val4) 
				                	{
				                		?>
				                		<th width="80" align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
				                		<?
				                	}
				                	?>
				                	<th><? echo number_format($vr_gr_tot_qty,0);?></th>
			                	</tr>
			                	<tr>
			                		<th><? //echo number_format($vr_tot_val,2);?></th>
			                		<?
				                	foreach ($date_range as $key2 => $val5) 
				                	{
				                		?>
				                		<th width="80" align="right">$ <? echo number_format($mon_gr_tot_array[$val5]['val'],2);?></th>
				                		<?
				                	}
				                	?>
				                	<th width="100">$ <? echo number_format($vr_gr_tot_val,2);?></th>
			                	</tr>
			                </tfoot>
			            </table>            
			        </div>
			    </div>
		    </fieldset>
			<?
		}
		else // report generate without value
		{

			?>
		        <!-- ===================================================================================================/
		        / 										BUYER WISE SUMMARY PART										  	/
		        /==================================================================================================== -->
		        <div id="1st_part">
			        <div style="width:<? echo $sub_dep_tbl_width+20;?>px;">  
			            <table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
			            	<caption style="text-align:left;"><h2>Buyer wise Summary</h2></caption>
			                <thead>
			                	<tr>
				                    <th rowspan="2" width="100">Comapny</th>
				                    <th rowspan="2" width="100">Buyer</th>
				                    <th rowspan="2" width="100">Quantity(pcs)</th>
				                    <?
				                    foreach ($year_month_count as $key => $val) 
				                    {
				                    	?>
				                    	<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
				                    	<?
				                    }
				                    ?>
				                    <th rowspan="2" width="100">Grand Total</th>
				                </tr>
				                <tr>
				                	<?
				                	foreach ($date_range as $key => $val) 
				                	{
				                		?>
				                		<th width="80"><? echo $val;?></th>
				                		<?
				                	}
				                	?>
				                </tr>
			                </thead>
			            </table>
			            <div style="width:<? echo $sub_dep_tbl_width+20;?>px;overflow-y:scroll;max-height:350px;" id="scroll_body_1">
				            <table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
				                <tbody>
				                	<?
				                	$i=1;
				                	$vr_tot_qty = 0;
				                	$vr_tot_val = 0;
				                	$vr_gr_tot_qty = 0;
				                	$vr_gr_tot_val = 0;
				                	$mon_gr_tot_array = array();
				                	foreach ($sub_dep_summary_array as $comp_key => $ref_val) 
				                	{	
				                		$ref = 0;
				                		foreach ($ref_val as $buyer_key => $val) 
				                		{
											
					                		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					                		?>
					                		<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" style="font-size:12px">
					                			<? if($ref==0){?>
					                			<td valign="top" width="100" rowspan="<? echo $rowspan[$comp_key];?>" ><? echo strtoupper($comp_key);?></td>
					                			<? $ref++;}?>
					                			<td width="100"><? echo strtoupper($buyer_arr[$buyer_key]);?></td>
					                			<td width="100" align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
					                			<?                			
					                			$hr_mon_tot_qty = 0;
							                	foreach ($date_range as $key => $val2) 
							                	{
							                		
													$exfactoryQty=0;
											        if(str_replace("'","",$cbo_shipping_status)==2){
												        foreach($sub_dep_summary_qty_array[$comp_key][$buyer_key][$val2]['PO_ID'] as $po_id){
													    $exfactoryQty+=$exfactory_qty_arr[$po_id['id']];
												        }
											        }

													?>
							                		<td width="80" align="right"><? echo number_format(($sub_dep_summary_qty_array[$comp_key][$buyer_key][$val2]['qty']-$exfactoryQty),0);?></td>
							                		<?
							                		$hr_mon_tot_qty += $sub_dep_summary_qty_array[$comp_key][$buyer_key][$val2]['qty']-$exfactoryQty;
							                		$vr_gr_tot_qty += $sub_dep_summary_qty_array[$comp_key][$buyer_key][$val2]['qty']-$exfactoryQty;
							                		$mon_gr_tot_array[$val2]['qty'] += $sub_dep_summary_qty_array[$comp_key][$buyer_key][$val2]['qty']-$exfactoryQty;
							                	}
							                	?>
							                	<td width="100" align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
					                		</tr>
					                		<?
					                		$i++;

					                		$vr_tot_qty += $val['qty'];
					                		$vr_tot_val += $val['val'];
					                	}
				                	}
				                	?>
				                </tbody>	                
				            </table>
				        </div>
			            <table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
			                <tfoot>
			                	<tr>
			                		<th width="100"></th>
			                		<th width="100">Grand Total</th>
			                		<th width="100"><? //echo number_format($vr_tot_qty,0);?></th>
			                		<?
				                	foreach ($date_range as $key2 => $val4) 
				                	{
				                		?>
				                		<th width="80" align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
				                		<?
				                	}
				                	?>
				                	<th><? echo number_format($vr_gr_tot_qty,0);?></th>
			                	</tr>
			                </tfoot>
			            </table>
			        </div>        
		        </div>
		        <!-- ===================================================================================================/
		        / 											ITEM WISE SUMMARY PART									  	/
		        /==================================================================================================== -->
		        <div id="2nd_part">
			        <div style="margin-top: 20px;width:<? echo $sub_dep_tbl_width+20;?>px;" id="">            
			            
			            <table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
			            	<caption style="text-align:left;"><h2>Item wise Summary</h2></caption>
			                <thead>
			                	<tr>
									<th rowspan="2" width="100">Company</th>
				                    <th rowspan="2" width="100">Item</th>
				                    <th rowspan="2" width="100">Quantity(pcs)</th>
				                    <?
				                    foreach ($year_month_count as $key => $val) 
				                    {
				                    	?>
				                    	<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
				                    	<?
				                    }
				                    ?>
				                    <th rowspan="2" width="100">Grand Total</th>
				                </tr>
				                <tr>
				                	<?
				                	foreach ($date_range as $key => $val) 
				                	{
				                		?>
				                		<th width="80"><? echo $val;?></th>
				                		<?
				                	}
				                	?>
				                </tr>
			                </thead>
			            </table>
			            <div style="width:<? echo $sub_dep_tbl_width+20;?>px;overflow-y:scroll;max-height:350px;" id="scroll_body_2">
				            <table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
				                <tbody>
				                	<?
				                	$k=1;
				                	$vr_tot_qty = 0;
				                	$vr_tot_val = 0;
				                	$vr_gr_tot_qty = 0;
				                	$vr_gr_tot_val = 0;
				                	$mon_gr_tot_array = array();
				                	foreach ($item_summary_array as $comp_key => $item_data) 
				                	{
										$ref=0;
										foreach ($item_data as $item_key => $val) 
				                		{
				                		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				                		?>
				                		<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" style="font-size:12px">
										<?
											if($ref==0){?>
											<td width="100" rowspan="<?=$rowspan_item[$comp_key];?>"><? echo $comp_key;?></td>
											<?$ref++;}?>
				                			<td width="100"><? echo $garments_item[$item_key];?></td>
				                			<td width="100" align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
				                			<?                			
				                			$hr_mon_tot_qty = 0;
						                	foreach ($date_range as $key => $val2) 
						                	{
												$exfactoryQty2=0;
												if(str_replace("'","",$cbo_shipping_status)==2){
													foreach($item_summary_qty_array[$comp_key][$item_key][$val2]['PO_ID'] as $po_id){
														$exfactoryQty2+=$exfactory_qty_arr[$po_id['id']];
													}
												}
						                		?>
						                		<td width="80" align="right"><? echo number_format($item_summary_qty_array[$comp_key][$item_key][$val2]['qty']-$exfactoryQty2,0);?></td>
						                		<?
						                		$hr_mon_tot_qty += $item_summary_qty_array[$comp_key][$item_key][$val2]['qty']-$exfactoryQty2;
						                		$vr_gr_tot_qty += $item_summary_qty_array[$comp_key][$item_key][$val2]['qty']-$exfactoryQty2;
						                		$mon_gr_tot_array[$val2]['qty'] += $item_summary_qty_array[$comp_key][$item_key][$val2]['qty']-$exfactoryQty2;
						                	}
						                	?>
						                	<td width="100" align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
				                		</tr>
				                		<?
				                		$i++;
				                		$k++;

				                		$vr_tot_qty += $val['qty'];
				                		$vr_tot_val += $val['val'];
				                	}}
				                	?>
				                </tbody>
				            </table>
			            </div>
			            <table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
			                <tfoot>
			                	<tr>
									<th width="100"><? //echo number_format($vr_tot_qty,0);?></th>
			                		<th width="100">Grand Total</th>
			                		<th width="100"><? //echo number_format($vr_tot_qty,0);?></th>
			                		<?
				                	foreach ($date_range as $key2 => $val4) 
				                	{
				                		?>
				                		<th width="80" align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
				                		<?
				                	}
				                	?>
				                	<th width="100"><? echo number_format($vr_gr_tot_qty,0);?></th>
			                	</tr>
			                </tfoot>
			            </table>            
			        </div>
			    </div>

		        <!-- ===================================================================================================/
		        / 												DETAILS PART									  		/
		        /==================================================================================================== -->
		        <div id="3rd_part">
			        <div style="margin-top: 20px;width:<? echo $sub_dep_tbl_width+120;?>px;" id="">            
			            
			            <table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
			            	<caption style="text-align:left;"><h2>Details</h2></caption>
			                <thead>
			                	<tr>
				                    <th rowspan="2" width="100">Company</th>
				                    <th rowspan="2" width="100">Buyer</th>
				                    <th rowspan="2" width="100">Item</th>
				                    <th rowspan="2" width="100">Quantity(pcs)</th>
				                    <?
				                    foreach ($year_month_count as $key => $val) 
				                    {
				                    	?>
				                    	<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
				                    	<?
				                    }
				                    ?>
				                    <th rowspan="2" width="100">Grand Total</th>
				                </tr>
				                <tr>
				                	<?
				                	foreach ($date_range as $key => $val) 
				                	{
				                		?>
				                		<th width="80"><? echo $val;?></th>
				                		<?
				                	}
				                	?>
				                </tr>
			                </thead>
			            </table>
			            <div style="width:<? echo $sub_dep_tbl_width+120;?>px;overflow-y:scroll;max-height:350px;" id="scroll_body_3">
				            <table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
				                <tbody>
				                	<?
				                	$k=1;
				                	$vr_tot_qty = 0;
				                	$vr_tot_val = 0;
				                	$vr_gr_tot_qty = 0;
				                	$vr_gr_tot_val = 0;
				                	$mon_gr_tot_array = array();
				                	foreach ($sub_dep_dtls_array as $comp_key => $ref_val) 
				                	{
				                		$ref = 0;
					                	foreach ($ref_val as $buyer_key => $buyer_val) 
					                	{
					                		$byr = 0;
					                		foreach ($buyer_val as $item_key => $val) 
					                		{
						                		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						                		?>
						                		<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" class="tr_even">
						                			<? if($ref==0){?>
						                			<td valing="top" width="100" rowspan="<? echo $rowspan_dtls[$comp_key];?>"><? echo strtoupper($comp_key);?></td>
						                			<? $ref++;} if($byr==0){?>
						                			<td valing="top" width="100" rowspan="<? echo $rowspan_buyer[$comp_key][$buyer_key];?>"><? echo strtoupper($buyer_arr[$buyer_key]);?></td>
						                			<? $byr++;}?>
						                			<td width="100"><? echo $garments_item[$item_key];?></td>
						                			<td width="100" align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
						                			<?                			
						                			$hr_mon_tot_qty = 0;
								                	foreach ($date_range as $key => $val2) 
								                	{
														$exfactoryQty3=0;
														if(str_replace("'","",$cbo_shipping_status)==2){
															foreach($sub_dep_qty_array[$comp_key][$buyer_key][$item_key][$val2]['PO_ID'] as $po_id){
																$exfactoryQty3+=$exfactory_qty_arr[$po_id['id']];
															}
														}
								                		?>
								                		<td width="80" align="right"><? echo number_format($sub_dep_qty_array[$comp_key][$buyer_key][$item_key][$val2]['qty']-$exfactoryQty3,0);?></td>
								                		<?
								                		$hr_mon_tot_qty += $sub_dep_qty_array[$comp_key][$buyer_key][$item_key][$val2]['qty']-$exfactoryQty3;
								                		$vr_gr_tot_qty += $sub_dep_qty_array[$comp_key][$buyer_key][$item_key][$val2]['qty']-$exfactoryQty3;
								                		$mon_gr_tot_array[$val2]['qty'] += $sub_dep_qty_array[$comp_key][$buyer_key][$item_key][$val2]['qty']-$exfactoryQty3;
								                	}
								                	?>
								                	<td width="100" align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
						                		</tr>
						                		<?
						                		$i++;
						                		$k++;

						                		$vr_tot_qty += $val['qty'];
						                		$vr_tot_val += $val['val'];
						                	}
					                	}
				                	}
				                	?>
				                </tbody>
				            </table>
			            </div>
			            <table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
			                <tfoot>
			                	<tr>
			                		<th width="100"></th>
			                		<th width="100"></th>
			                		<th width="100">Grand Total</th>
			                		<th width="100"><? //echo number_format($vr_tot_qty,0);?></th>
			                		<?
				                	foreach ($date_range as $key2 => $val4) 
				                	{
				                		?>
				                		<th width="80" align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
				                		<?
				                	}
				                	?>
				                	<th width="100"><? echo number_format($vr_gr_tot_qty,0);?></th>
			                	</tr>
			                </tfoot>
			            </table>            
			        </div>
			    </div>
		    </fieldset>
			<?
		}
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
	echo "$total_data####$filename####$report_type";
	exit();
}
function weekOfMonth($strDate) {
	$dateArray = explode("/", $strDate);
	$date = new DateTime();
	//$date->setDate(Year, Month, Date);
	$date->setDate($dateArray[2], $dateArray[1], $dateArray[0]);
	return floor((date_format($date, 'j') - 1) / 7) + 1;  
}
if($action=="report_order_details")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	extract($_REQUEST);
	$company_name 	=str_replace("'","",$cbo_company_name);
	$buyer_name 	=str_replace("'","",$cbo_buyer_name);
	$buyer_ref 		=str_replace("'","",$cbo_buyer_ref);
	$sub_department =str_replace("'","",$cbo_sub_department);
	$shipping_status_id=str_replace("'","",$cbo_shipping_status);
	$cbo_order_status=str_replace("'","",$cbo_order_status);
	$date_type 		=str_replace("'","",$cbo_date_type);
	$from_year 		=str_replace("'","",$cbo_from_year);
	$from_month 	=str_replace("'","",$cbo_from_month);
	$to_year 		=str_replace("'","",$cbo_to_year);
	$to_month 		=str_replace("'","",$cbo_to_month);
	$report_type 	=str_replace("'","",$cbo_report_type);
	$type 			=str_replace("'","",$reportType);
	$show_value 	=str_replace("'","",$show_value);
	//echo $shipping_status_id.'DD';
	// =========================== MAKING QUERY COND ============================

	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$shipping_status_chk=array(1,2);
	if(in_array($shipping_status_id,$shipping_status_chk))
	{
	$shipping_status_id="1,2";
	}
	$sql_cond = "";
	if($type==3){
	$sql_cond .= ($company_name !=0) ? " and a.company_name=$company_name" : "";
	}
	$sql_cond .= ($buyer_name !=0) ? " and a.buyer_name in($buyer_name)" : "";
	$sql_cond .= ($sub_department !=0) ? " and a.pro_sub_dep in($sub_department)" : "";
	$sql_cond .= ($shipping_status_id !="") ? " and b.shiping_status in($shipping_status_id)" : "";
	$sql_cond .= ($cbo_order_status !=0) ? " and b.is_confirmed =$cbo_order_status" : "";
	if($buyer_ref !=0)
	{
		$buyer_id_array = return_library_array("select id,id as ids from  lib_buyer where status_active=1 and exporters_reference='$buyer_ref'","id","ids");
	}

	// print_r($buyer_id_array);die();
	if(count($buyer_id_array)>0)
	{
		$buyer_id = implode(",", $buyer_id_array);
		$sql_cond .= " and a.buyer_name in($buyer_id)";
	}

	if($from_year !=0 && $from_month !=0 && $to_year !=0 && $to_month !=0)
	{
		$dt = $from_year."-".$from_month;
		$from_date = date('d-M-Y',strtotime($dt));

		$last_day = cal_days_in_month(CAL_GREGORIAN, $to_month, $to_year); 
		$dt = $to_year."-".$to_month."-".$last_day;
		$to_date = date('d-M-Y',strtotime($dt));

		if($date_type==1)
		{
			$sql_cond .= " and b.pub_shipment_date between '$from_date' and '$to_date'";
		}
		elseif ($date_type==2) 
		{
			$sql_cond .= " and b.txt_etd_ldd between '$from_date' and '$to_date'";
		}
		elseif ($date_type==2) 
		{
			$sql_cond .= " and b.shipment_date between '$from_date' and '$to_date'";
		}
	}

	// echo $sql_cond;die();

	function getMonthsInRange($startDate, $endDate) 
	{
		$months = array();
		while (strtotime($startDate) <= strtotime($endDate)) 
		{
		    $months[strtoupper(date('M-Y', strtotime($startDate)))] = strtoupper(date('M-Y', strtotime($startDate)));
		    $startDate = date('01 M Y', strtotime($startDate.'+ 1 month')); // Set date to 1 so that new month is returned as the month changes.		    
		}

		return $months;
	}
	$date_range = getMonthsInRange($from_date,$to_date);

	$year_month_count = array();
	$month_year_width = array();
	$month_year_tot_width = 0;
	$colspan = 5;
	foreach ($date_range as $key => $val) 
	{
		$ex_data = explode("-", $val);
		$year_month_count[$ex_data[1]]++;
		$month_year_width[$ex_data[1]] += 80 ;
		$month_year_tot_width += 80;
		$colspan++;
	}
	if($type==3){
		if($report_type==1) // sub dept wise
		{
			// =============================================== MAIN QUERY =========================================
				/* $sql = "SELECT  b.id as PO_ID,a.BUYER_NAME, a.TOTAL_SET_QNTY, a.PRO_SUB_DEP, TO_CHAR (b.pub_shipment_date, 'MON-YYYY') AS MONTH_YEAR, (B.PO_QUANTITY) as qty, (B.PO_TOTAL_PRICE) as val, A.GMTS_ITEM_ID
			FROM wo_po_details_master a, wo_po_break_down b
			WHERE a.id = b.job_id $sql_cond AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active IN (1) AND b.is_deleted = 0
			"; */
			/* $sql = "SELECT  b.id as PO_ID,a.BUYER_NAME, a.TOTAL_SET_QNTY, a.PRO_SUB_DEP, TO_CHAR (b.pub_shipment_date, 'MON-YYYY') AS MONTH_YEAR, (c.ORDER_QUANTITY) as qty, (C.ORDER_TOTAL) as val, A.GMTS_ITEM_ID
			FROM wo_po_details_master a, wo_po_break_down b,wo_po_color_size_breakdown c
			WHERE a.id = b.job_id and b.id=c.PO_BREAK_DOWN_ID and a.id = c.job_id and c.status_active=1  $sql_cond AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active IN (1) AND b.is_deleted = 0
			";*////GROUP BY a.buyer_name, a.pro_sub_dep, b.id, a.total_set_qnty, b.pub_shipment_date, A.GMTS_ITEM_ID
			//echo $sql;// die();
			$sql="SELECT  b.id as PO_ID,a.BUYER_NAME, a.TOTAL_SET_QNTY, a.PRO_SUB_DEP, TO_CHAR (b.pub_shipment_date, 'MON-YYYY') AS MONTH_YEAR, (B.PO_QUANTITY) as qty, (B.PO_TOTAL_PRICE) as val, C.GMTS_ITEM_ID
			FROM wo_po_details_master a, wo_po_break_down b,wo_po_details_mas_set_details c
			WHERE a.id = b.job_id and a.id = c.job_id and b.job_id = c.job_id $sql_cond AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active IN (1) AND b.is_deleted = 0";
			$sql_res = sql_select($sql);
			if (count($sql_res) < 1)
			{
				echo '<div style="text-align: center;color: red;font-weight: bold;font-size: 20px;">Data not found.</div>';
				disconnect($con);
				die();
			}

			$sub_dep_dtls_array = array();
			$sub_dep_qty_array = array();
			$sub_dep_summary_array = array();
			$sub_dep_summary_qty_array = array();
			$item_summary_array = array();
			$item_summary_qty_array = array();
			foreach ($sql_res as $row)
			{
				// for summary
				$sub_dep_summary_array[$row['PRO_SUB_DEP']]['qty'] += $row['QTY'] ;//* $row['TOTAL_SET_QNTY']
				$sub_dep_summary_array[$row['PRO_SUB_DEP']]['val'] += $row['VAL'];

				$sub_dep_summary_qty_array[$row['PRO_SUB_DEP']][$row['MONTH_YEAR']]['qty'] += $row['QTY'] ;
				$sub_dep_summary_qty_array[$row['PRO_SUB_DEP']][$row['MONTH_YEAR']]['val'] += $row['VAL'];

				// for item wise summary
				$item_summary_array[$row[csf('gmts_item_id')]]['qty'] += $row['QTY'];
				$item_summary_array[$row[csf('gmts_item_id')]]['val'] += $row['VAL'];

				$item_summary_qty_array[$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['qty'] += $row['QTY'];
				$item_summary_qty_array[$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['val'] += $row['VAL'];
				$item_summary_qty_array[$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['id']=$row['PO_ID'];
				$item_summary_qty_array[$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['val']+=$row['VAL'];
				$item_summary_qty_array[$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['qty']+=$row['QTY'] ;

				// for details
				$sub_dep_qty_array[$row['PRO_SUB_DEP']][$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['id']=$row['PO_ID'];
				$sub_dep_qty_array[$row['PRO_SUB_DEP']][$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['val']+=$row['VAL'];
				$sub_dep_qty_array[$row['PRO_SUB_DEP']][$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['qty']+=$row['QTY'] ;
				$sub_dep_qty_array[$row['PRO_SUB_DEP']][$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['qty'] += $row['QTY'] ;
				$sub_dep_qty_array[$row['PRO_SUB_DEP']][$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['val'] += $row['VAL'];

				$sub_dep_dtls_array[$row['PRO_SUB_DEP']][$row[csf('gmts_item_id')]]['qty'] += $row['QTY'];// * $row['TOTAL_SET_QNTY']
				$sub_dep_dtls_array[$row['PRO_SUB_DEP']][$row[csf('gmts_item_id')]]['val'] += $row['VAL'];
			
				// $sub_dep_summary_qty_array[$row['PRO_SUB_DEP']][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]= $row['PO_ID'];
				$sub_dep_summary_qty_array[$row['PRO_SUB_DEP']][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['id']= $row['PO_ID'];
				$sub_dep_summary_qty_array[$row['PRO_SUB_DEP']][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['val']+= $row['VAL'];
				$sub_dep_summary_qty_array[$row['PRO_SUB_DEP']][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['qty']+=  $row['QTY'] ; //* $row['TOTAL_SET_QNTY']
				$all_po_array[$row['PO_ID']]= $row['PO_ID'];
			
			}

			$rowspan = array();
			foreach ($sub_dep_dtls_array as $sub_key => $sub_value) 
			{
				foreach ($sub_value as $itm_key => $itm_value) 
				{
					$rowspan[$sub_key]++;
				}
			}

			$sub_dep_tbl_width = $month_year_tot_width + 300;
			$buyer_name_arr=return_library_array("select id,short_name from  lib_buyer where id in($buyer_name)","id","short_name");
			$buyer_ref_name_arr=return_library_array("select id,exporters_reference from  lib_buyer where id in($buyer_name)","id","exporters_reference");
			
			
			
			$exfactoryQry="select PO_BREAK_DOWN_ID,EX_FACTORY_QNTY from PRO_EX_FACTORY_MST where  IS_DELETED=0 and STATUS_ACTIVE=1 ".where_con_using_array($all_po_array,0,'PO_BREAK_DOWN_ID')."";
			$exfactoryQry_result = sql_select($exfactoryQry);
			$exfactory_qty_arr=array();
			foreach($exfactoryQry_result as $row){
				$exfactory_qty_arr[$row[PO_BREAK_DOWN_ID]]+=$row[EX_FACTORY_QNTY];
			}
			
			//print_r($exfactory_qty_arr);die;
		
		
			
			
			ob_start();
			?>
				<fieldset style="width:100%;">
					<style type="text/css">
						.tr_odd td{font-size: 14px !important;}
						.tr_even td{font-size: 12px !important;}
					</style>
					<div id="heading_part">
						<table width="<? echo $sub_dep_tbl_width+120;?>;" cellspacing="0"  align="center" >
							<tr>
								<td colspan="<? echo $colspan;?>" align="center" class="form_caption">
									<strong style="font-size:16px;">Company Name:<? echo  $company_library[$company_name] ;?></strong>
								</td>
							</tr>
							<tr class="form_caption">
								<td colspan="<? echo $colspan;?>" align="center" class="form_caption"> <strong style="font-size:15px;">Month Wise Quantity (Pcs) and Value</strong></td>
							</tr>
							<tr align="center">
								<td colspan="<? echo $colspan;?>" align="center" class="form_caption"> <strong style="font-size:15px;">Date Type : <? echo ($date_type==1) ? "Shipment Date (RFI)": "Original Ship Date";?></strong></td>
							</tr>
							<tr align="center">
								<td colspan="<? echo $colspan;?>" align="center" class="form_caption"> <strong style="font-size:15px;">Runtime : <? echo date("j F, Y h:i:s a");?></strong></td>
							</tr>
						</table>
						<table width="<? echo $sub_dep_tbl_width;?>;" cellspacing="0"  align="center">
							<tr class="form_caption">
								<td colspan="<? echo $colspan;?>" align="center" class="form_caption"> 
									<div style="font-size:20px;text-align:left;">Buyer : <? echo implode(",", $buyer_name_arr);?></div>
									<div style="font-size:20px;text-align:left;">Buyer Ref. : <? echo implode(",", array_filter($buyer_ref_name_arr));?></div>
								</td>
							</tr>
						</table>
					</div>
			<?
			if($show_value) // report generate with value
			{
				?>
					<!-- ===================================================================================================/
					/ 										SUB DEP WISE SUMMARY PART									  	/
					/==================================================================================================== -->
					<div id="1st_part" style="width: <? echo $sub_dep_tbl_width;?>px;">
						<table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1">
							<caption style="text-align:left;"><h2>Sub Dept wise Summary</h2></caption>
							<thead>
								<tr>
									<th rowspan="2" width="100">Sub Dept.</th>
									<th rowspan="2" width="100">Quantity(pcs)/Value</th>
									<?
									foreach ($year_month_count as $key => $val) 
									{
										?>
										<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
										<?
									}
									?>
									<th rowspan="2" width="100">Grand Total</th>
								</tr>
								<tr>
									<?
									foreach ($date_range as $key => $val) 
									{
										?>
										<th width="80"><? echo $val;?></th>
										<?
									}
									?>
								</tr>
							</thead>
							<tbody>
								<?
								
								
								$i=1;
								$vr_tot_qty = 0;
								$vr_tot_val = 0;
								$vr_gr_tot_qty = 0;
								$vr_gr_tot_val = 0;
								$mon_gr_tot_array = array();
								foreach ($sub_dep_summary_array as $sub_dep_key => $val) 
								{
									if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
									<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" class="tr_odd">
										<td valign="top" rowspan="2" title="<? echo $sub_dep_key;?>"><? echo strtoupper($lib_sub_dept_array[$sub_dep_key]);?></td>
										<td align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
										<?
										$hr_mon_tot_qty = 0;
										foreach ($date_range as $key => $val2) 
										{
											$exfactoryQty=0;
											if(str_replace("'","",$cbo_shipping_status)==2){
												foreach($sub_dep_summary_qty_array[$sub_dep_key][$val2]['PO_ID'] as $po_id){
													$exfactoryQty+=$exfactory_qty_arr[$po_id['id']];
												}
											}
											
											?>
											<td align="right" title="Ex-FactQty=<? echo $exfactoryQty;?>"><? echo number_format(($sub_dep_summary_qty_array[$sub_dep_key][$val2]['qty']-$exfactoryQty),0);?></td>
											<?
											$hr_mon_tot_qty += $sub_dep_summary_qty_array[$sub_dep_key][$val2]['qty']-$exfactoryQty;
											$vr_gr_tot_qty += $sub_dep_summary_qty_array[$sub_dep_key][$val2]['qty']-$exfactoryQty;
											$mon_gr_tot_array[$val2]['qty'] += $sub_dep_summary_qty_array[$sub_dep_key][$val2]['qty']-$exfactoryQty;
										}
										?>
										<td align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
									</tr>
									<tr class="tr_even">
									<td align="right">Value<? //echo number_format($val['val'],2);?></td>	
									<?
									$hr_mon_tot_val = 0;
									foreach ($date_range as $key2 => $val3) 
									{
										$exfactoryVal=0;
										$ave_rate=0;
										if(str_replace("'","",$cbo_shipping_status)==2){
											foreach($sub_dep_summary_qty_array[$sub_dep_key][$val3]['PO_ID'] as $po_id){
												$ave_rate=$po_id['val']/$po_id['qty'];
												$exfactoryVal+=$exfactory_qty_arr[$po_id['id']]*$ave_rate;
											}
										}
										?>
										<td align="right">$ <? echo number_format($sub_dep_summary_qty_array[$sub_dep_key][$val3]['val']-$exfactoryVal,2);?></td>
										<?
										$hr_mon_tot_val += $sub_dep_summary_qty_array[$sub_dep_key][$val3]['val']-$exfactoryVal;
										$vr_gr_tot_val += $sub_dep_summary_qty_array[$sub_dep_key][$val3]['val']-$exfactoryVal;
										$mon_gr_tot_array[$val3]['val'] += $sub_dep_summary_qty_array[$sub_dep_key][$val3]['val']-$exfactoryVal;
									}
									?>
									<td align="right">$ <? echo number_format($hr_mon_tot_val,2);?></td>
								</tr>
									<?
									$i++;

									$vr_tot_qty += $val['qty'];
									$vr_tot_val += $val['val'];
								}
								?>
							</tbody>
							<tfoot>
								<tr>
									<th rowspan="2">Grand Total</th>
									<th><? //echo number_format($vr_tot_qty,0);?></th>
									<?
									foreach ($date_range as $key2 => $val4) 
									{
										?>
										<th align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
										<?
									}
									?>
									<th><? echo number_format($vr_gr_tot_qty,0);?></th>
								</tr>
								<tr>
									<th><? //echo number_format($vr_tot_val,0);?></th>
									<?
									foreach ($date_range as $key2 => $val5) 
									{
										?>
										<th align="right">$ <? echo number_format($mon_gr_tot_array[$val5]['val'],2);?></th>
										<?
									}
									?>
									<th>$ <? echo number_format($vr_gr_tot_val,0);?></th>
								</tr>
							</tfoot>
						</table>
					</div>        
					<!-- ===================================================================================================/
					/ 											ITEM WISE SUMMARY PART									  	/
					/==================================================================================================== -->
					<div  id="2nd_part">
						<div style="margin-top: 20px;width:<? echo $sub_dep_tbl_width+20;?>px;">            
							
							<table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
								<caption style="text-align:left;"><h2>Item wise Summary</h2></caption>
								<thead>
									<tr>
										<th rowspan="2" width="100">Item</th>
										<th rowspan="2" width="100">Quantity(pcs)/Value</th>
										<?
										foreach ($year_month_count as $key => $val) 
										{
											?>
											<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
											<?
										}
										?>
										<th rowspan="2" width="100">Grand Total</th>
									</tr>
									<tr>
										<?
										foreach ($date_range as $key => $val) 
										{
											?>
											<th width="80"><? echo $val;?></th>
											<?
										}
										?>
									</tr>
								</thead>
							</table>
							<div style="width:<? echo $sub_dep_tbl_width+20;?>px;overflow-y:scroll;max-height:350px;" id="scroll_body_2">
								<table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
									<tbody>
										<?
										$k=1;
										$vr_tot_qty = 0;
										$vr_tot_val = 0;
										$vr_gr_tot_qty = 0;
										$vr_gr_tot_val = 0;
										$mon_gr_tot_array = array();
										foreach ($item_summary_array as $item_key => $val) 
										{
											$item_arr = explode(',', $item_key);
											$items_str = '';

											foreach ($item_arr as $item_id) {
												$items_str .= $garments_item[$item_id] . ', ';
											}

											$items_str = rtrim($items_str, ' ,');
											if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
											?>
											<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" class="tr_odd">
												<td width="100" valign="top" rowspan="2"><? echo strtoupper($items_str);?></td>
												<td width="100" align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
												<?                			
												$hr_mon_tot_qty = 0;
												foreach ($date_range as $key => $val2) 
												{
													$exfactoryQty2=0;
													if(str_replace("'","",$cbo_shipping_status)==2){
														foreach($item_summary_qty_array[$item_key][$val2]['PO_ID'] as $po_id){
															$exfactoryQty2+=$exfactory_qty_arr[$po_id['id']];
														}
													}
													?>
													<td width="80" align="right"><? echo number_format($item_summary_qty_array[$item_key][$val2]['qty']-$exfactoryQty2,0);?></td>
													<?
													$hr_mon_tot_qty += $item_summary_qty_array[$item_key][$val2]['qty']-$exfactoryQty2;
													$vr_gr_tot_qty += $item_summary_qty_array[$item_key][$val2]['qty']-$exfactoryQty2;
													$mon_gr_tot_array[$val2]['qty'] += $item_summary_qty_array[$item_key][$val2]['qty']-$exfactoryQty2;
												}
												?>
												<td width="100" align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
											</tr>
											<tr class="tr_even">
											<td width="100" align="right">Value<? //echo number_format($val['val'],2);?></td>	
											<?
											$hr_mon_tot_val = 0;
											foreach ($date_range as $key2 => $val3) 
											{
												$exfactoryVal2=0;
												$ave_rate2=1;

												if(str_replace("'","",$cbo_shipping_status)==2){
													foreach($item_summary_qty_array[$item_key][$val3]['PO_ID'] as $po_id){
														$ave_rate2=$po_id['val']/$po_id['qty'];
														$exfactoryVal2+=$exfactory_qty_arr[$po_id['id']]*$ave_rate2;
													}
												}
												?>
												<td width="80" align="right">$ <? echo number_format($item_summary_qty_array[$item_key][$val3]['val']-$exfactoryVal2,0);?></td>
												<?
												$hr_mon_tot_val += $item_summary_qty_array[$item_key][$val3]['val']-$exfactoryVal2;
												$vr_gr_tot_val += $item_summary_qty_array[$item_key][$val3]['val']-$exfactoryVal2;
												$mon_gr_tot_array[$val3]['val'] += $item_summary_qty_array[$item_key][$val3]['val']-$exfactoryVal2;
											}
											?>
											<td width="100" align="right">$ <? echo number_format($hr_mon_tot_val,0);?></td>
										</tr>
											<?
											$i++;
											$k++;

											$vr_tot_qty += $val['qty'];
											$vr_tot_val += $val['val'];
										}
										?>
									</tbody>
								</table>
							</div>
							<table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
								<tfoot>
									<tr>
										<th width="100" rowspan="2">Grand Total</th>
										<th width="100"><? //echo number_format($vr_tot_qty,0);?></th>
										<?
										foreach ($date_range as $key2 => $val4) 
										{
											?>
											<th width="80" align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
											<?
										}
										?>
										<th><? echo number_format($vr_gr_tot_qty,0);?></th>
									</tr>
									<tr>
										<th><? //echo number_format($vr_tot_val,2);?></th>
										<?
										foreach ($date_range as $key2 => $val5) 
										{
											?>
											<th width="80" align="right">$ <? echo number_format($mon_gr_tot_array[$val5]['val'],2);?></th>
											<?
										}
										?>
										<th width="100">$ <? echo number_format($vr_gr_tot_val,2);?></th>
									</tr>
								</tfoot>
							</table>            
						</div>
					</div>

					<!-- ===================================================================================================/
					/ 												DETAILS PART									  		/
					/==================================================================================================== -->
					<div id="3rd_part">
						<div style="margin-top: 20px;width:<? echo $sub_dep_tbl_width+120;?>px;">            
							
							<table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
								<caption style="text-align:left;"><h2>Details</h2></caption>
								<thead>
									<tr>
										<th rowspan="2" width="100">Sub Dept.</th>
										<th rowspan="2" width="100">Item</th>
										<th rowspan="2" width="100">Quantity(pcs)/Value</th>
										<?
										foreach ($year_month_count as $key => $val) 
										{
											?>
											<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
											<?
										}
										?>
										<th rowspan="2" width="100">Grand Total</th>
									</tr>
									<tr>
										<?
										foreach ($date_range as $key => $val) 
										{
											?>
											<th width="80"><? echo $val;?></th>
											<?
										}
										?>
									</tr>
								</thead>
							</table>
							<div style="width:<? echo $sub_dep_tbl_width+120;?>px;overflow-y:scroll;max-height:350px;" id="scroll_body_3">
								<table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
									<tbody>
										<?
										$k=1;
										$vr_tot_qty = 0;
										$vr_tot_val = 0;
										$vr_gr_tot_qty = 0;
										$vr_gr_tot_val = 0;
										$mon_gr_tot_array = array();
										foreach ($sub_dep_dtls_array as $sub_dep_key => $sub_dep_val) 
										{
											foreach ($sub_dep_val as $item_key => $val) 
											{
												$item_arr = explode(',', $item_key);
												$items_str = '';

												foreach ($item_arr as $item_id) {
													$items_str .= $garments_item[$item_id] . ', ';
												}

												$items_str = rtrim($items_str, ' ,');

												if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
												?>
												<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" class="tr_odd">
													<td width="100" valign="top" rowspan="2"><? echo strtoupper($lib_sub_dept_array[$sub_dep_key]);?></td>
													<td width="100" valign="top" rowspan="2"><? echo strtoupper($items_str);?></td>
													<td width="100" align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
													<?                			
													$hr_mon_tot_qty = 0;
													foreach ($date_range as $key => $val2) 
													{
														$exfactoryQty3=0;
														if(str_replace("'","",$cbo_shipping_status)==2){
															foreach($sub_dep_qty_array[$sub_dep_key][$item_key][$val2]['PO_ID'] as $po_id){
																$exfactoryQty3+=$exfactory_qty_arr[$po_id['id']];
															}
														}
														?>
														<td width="80" align="right"><? echo number_format($sub_dep_qty_array[$sub_dep_key][$item_key][$val2]['qty']-$exfactoryQty3,0);?></td>
														<?
														$hr_mon_tot_qty += $sub_dep_qty_array[$sub_dep_key][$item_key][$val2]['qty']-$exfactoryQty3;
														$vr_gr_tot_qty += $sub_dep_qty_array[$sub_dep_key][$item_key][$val2]['qty']-$exfactoryQty3;
														$mon_gr_tot_array[$val2]['qty'] += $sub_dep_qty_array[$sub_dep_key][$item_key][$val2]['qty']-$exfactoryQty3;
													}
													?>
													<td width="100" align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
												</tr>
												<tr class="tr_even">
												<td width="100" align="right">Value<? //echo number_format($val['val'],2);?></td>	
												<?
												$hr_mon_tot_val = 0;
												foreach ($date_range as $key2 => $val3) 
												{
													$exfactoryVal3=0;
													$ave_rate3=1;
													if(str_replace("'","",$cbo_shipping_status)==2){
														foreach($sub_dep_qty_array[$sub_dep_key][$item_key][$val3]['PO_ID'] as $po_id){
															$ave_rate3=$po_id['val']/$po_id['qty'];
														$exfactoryVal3+=$exfactory_qty_arr[$po_id['id']]*$ave_rate3;
														}
													}
													?>
													<td width="80" align="right">$ <? echo number_format($sub_dep_qty_array[$sub_dep_key][$item_key][$val3]['val']-$exfactoryVal3,2);?></td>
													<?
													$hr_mon_tot_val += $sub_dep_qty_array[$sub_dep_key][$item_key][$val3]['val']-$exfactoryVal3;
													$vr_gr_tot_val += $sub_dep_qty_array[$sub_dep_key][$item_key][$val3]['val']-$exfactoryVal3;
													$mon_gr_tot_array[$val3]['val'] += $sub_dep_qty_array[$sub_dep_key][$item_key][$val3]['val']-$exfactoryVal3;
												}
												?>
												<td width="100" align="right">$ <? echo number_format($hr_mon_tot_val,2);?></td>
											</tr>
												<?
												$i++;
												$k++;

												$vr_tot_qty += $val['qty'];
												$vr_tot_val += $val['val'];
											}
										}
										?>
									</tbody>
								</table>
							</div>
							<table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
								<tfoot>
									<tr>
										<th width="100" rowspan="2"></th>
										<th width="100" rowspan="2">Grand Total</th>
										<th width="100"><? //echo number_format($vr_tot_qty,0);?></th>
										<?
										foreach ($date_range as $key2 => $val4) 
										{
											?>
											<th width="80" align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
											<?
										}
										?>
										<th><? echo number_format($vr_gr_tot_qty,0);?></th>
									</tr>
									<tr>
										<th><? //echo number_format($vr_tot_val,0);?></th>
										<?
										foreach ($date_range as $key2 => $val5) 
										{
											?>
											<th width="80" align="right">$ <? echo number_format($mon_gr_tot_array[$val5]['val'],2);?></th>
											<?
										}
										?>
										<th width="100">$ <? echo number_format($vr_gr_tot_val,2);?></th>
									</tr>
								</tfoot>
							</table>            
						</div>
					</div>
				</fieldset>
				<?
			}
			else // report generate without value
			{
				?>
					<!-- ===================================================================================================/
					/ 										SUB DEP WISE SUMMARY PART									  	/
					/==================================================================================================== -->
					<div id="1st_part">
						<table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1">
							<caption style="text-align:left;"><h2>Sub Dept wise Summary</h2></caption>
							<thead>
								<tr>
									<th rowspan="2" width="100">Sub Dept.</th>
									<th rowspan="2" width="100">Quantity(pcs)</th>
									<?
									foreach ($year_month_count as $key => $val) 
									{
										?>
										<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
										<?
									}
									?>
									<th rowspan="2" width="100">Grand Total</th>
								</tr>
								<tr>
									<?
									foreach ($date_range as $key => $val) 
									{
										?>
										<th width="80"><? echo $val;?></th>
										<?
									}
									?>
								</tr>
							</thead>
							<tbody>
								<?
								$i=1;
								$vr_tot_qty = 0;
								$vr_tot_val = 0;
								$vr_gr_tot_qty = 0;
								$vr_gr_tot_val = 0;
								$mon_gr_tot_array = array();
								foreach ($sub_dep_summary_array as $sub_dep_key => $val) 
								{
									if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
									<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" class="tr_odd">
										<td><? echo strtoupper($lib_sub_dept_array[$sub_dep_key]);?></td>
										<td align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
										<?                			
										$hr_mon_tot_qty = 0;
										foreach ($date_range as $key => $val2) 
										{
											$exfactoryQty=0;
											if(str_replace("'","",$cbo_shipping_status)==2){
												foreach($sub_dep_summary_qty_array[$sub_dep_key][$val2]['PO_ID'] as $po_id){
												$exfactoryQty+=$exfactory_qty_arr[$po_id['id']];
												}
											}

											?>
											<td align="right"><? echo number_format(($sub_dep_summary_qty_array[$sub_dep_key][$val2]['qty']-$exfactoryQty),0);?></td>
											<?
											$hr_mon_tot_qty += $sub_dep_summary_qty_array[$sub_dep_key][$val2]['qty']-$exfactoryQty;
											$vr_gr_tot_qty += $sub_dep_summary_qty_array[$sub_dep_key][$val2]['qty']-$exfactoryQty;
											$mon_gr_tot_array[$val2]['qty'] += $sub_dep_summary_qty_array[$sub_dep_key][$val2]['qty']-$exfactoryQty;
										}
										?>
										<td align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
									</tr>
									<?
									$i++;

									$vr_tot_qty += $val['qty'];
									$vr_tot_val += $val['val'];
								}
								?>
							</tbody>
							<tfoot>
								<tr>
									<th>Grand Total</th>
									<th><? //echo number_format($vr_tot_qty,0);?></th>
									<?
									foreach ($date_range as $key2 => $val4) 
									{
										?>
										<th align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
										<?
									}
									?>
									<th><? echo number_format($vr_gr_tot_qty,0);?></th>
								</tr>
							</tfoot>
						</table>
					</div>        
					<!-- ===================================================================================================/
					/ 											ITEM WISE SUMMARY PART									  	/
					/==================================================================================================== -->
					<div  id="2nd_part">
						<div style="margin-top: 20px;width:<? echo $sub_dep_tbl_width+20;?>px;">            
							
							<table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
								<caption style="text-align:left;"><h2>Item wise Summary</h2></caption>
								<thead>
									<tr>
										<th rowspan="2" width="100">Item</th>
										<th rowspan="2" width="100">Quantity(pcs)</th>
										<?
										foreach ($year_month_count as $key => $val) 
										{
											?>
											<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
											<?
										}
										?>
										<th rowspan="2" width="100">Grand Total</th>
									</tr>
									<tr>
										<?
										foreach ($date_range as $key => $val) 
										{
											?>
											<th width="80"><? echo $val;?></th>
											<?
										}
										?>
									</tr>
								</thead>
							</table>
							<div style="width:<? echo $sub_dep_tbl_width+20;?>px;overflow-y:scroll;max-height:350px;" id="scroll_body_2">
								<table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
									<tbody>
										<?
										$k=1;
										$vr_tot_qty = 0;
										$vr_tot_val = 0;
										$vr_gr_tot_qty = 0;
										$vr_gr_tot_val = 0;
										$mon_gr_tot_array = array();
										foreach ($item_summary_array as $item_key => $val) 
										{
											if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
											?>
											<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" class="tr_odd">
												<td width="100"><? echo strtoupper($garments_item[$item_key]);?></td>
												<td width="100" align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
												<?                			
												$hr_mon_tot_qty = 0;
												foreach ($date_range as $key => $val2) 
												{
													$exfactoryQty2=0;
													if(str_replace("'","",$cbo_shipping_status)==2){
														foreach($item_summary_qty_array[$item_key][$val2]['PO_ID'] as $po_id){
															$exfactoryQty2+=$exfactory_qty_arr[$po_id['id']];
														}
													}
													?>
													<td width="80" align="right"><? echo number_format($item_summary_qty_array[$item_key][$val2]['qty']-$exfactoryQty2,0);?></td>
													<?
													$hr_mon_tot_qty += $item_summary_qty_array[$item_key][$val2]['qty']-$exfactoryQty2;
													$vr_gr_tot_qty += $item_summary_qty_array[$item_key][$val2]['qty']-$exfactoryQty2;
													$mon_gr_tot_array[$val2]['qty'] += $item_summary_qty_array[$item_key][$val2]['qty']-$exfactoryQty2;
												}
												?>
												<td width="100" align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
											</tr>
											<?
											$i++;
											$k++;

											$vr_tot_qty += $val['qty'];
											$vr_tot_val += $val['val'];
										}
										?>
									</tbody>
								</table>
							</div>
							<table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
								<tfoot>
									<tr>
										<th width="100">Grand Total</th>
										<th width="100"><? //echo number_format($vr_tot_qty,0);?></th>
										<?
										foreach ($date_range as $key2 => $val4) 
										{
											?>
											<th width="80" align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
											<?
										}
										?>
										<th><? echo number_format($vr_gr_tot_qty,0);?></th>
									</tr>
								</tfoot>
							</table>            
						</div>
					</div>
					<!-- ===================================================================================================/
					/ 												DETAILS PART									  		/
					/==================================================================================================== -->
					<div  id="3rd_part">
						<div style="margin-top: 20px;width:<? echo $sub_dep_tbl_width+120;?>px;">            
							
							<table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
								<caption style="text-align:left;"><h2>Details</h2></caption>
								<thead>
									<tr>
										<th rowspan="2" width="100">Sub Dept.</th>
										<th rowspan="2" width="100">Item</th>
										<th rowspan="2" width="100">Quantity(pcs)</th>
										<?
										foreach ($year_month_count as $key => $val) 
										{
											?>
											<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
											<?
										}
										?>
										<th rowspan="2" width="100">Grand Total</th>
									</tr>
									<tr>
										<?
										foreach ($date_range as $key => $val) 
										{
											?>
											<th width="80"><? echo $val;?></th>
											<?
										}
										?>
									</tr>
								</thead>
							</table>
							<div style="width:<? echo $sub_dep_tbl_width+120;?>px;overflow-y:scroll;max-height:350px;" id="scroll_body_3">
								<table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
									<tbody>
										<?
										$k=1;
										$vr_tot_qty = 0;
										$vr_tot_val = 0;
										$vr_gr_tot_qty = 0;
										$vr_gr_tot_val = 0;
										$mon_gr_tot_array = array();
										foreach ($sub_dep_dtls_array as $sub_dep_key => $sub_dep_val) 
										{
											foreach ($sub_dep_val as $item_key => $val) 
											{
												if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
												?>
												<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" class="tr_odd">
													<td width="100"><? echo strtoupper($lib_sub_dept_array[$sub_dep_key]);?></td>
													<td width="100"><? echo strtoupper($garments_item[$item_key]);?></td>
													<td width="100" align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
													<?                			
													$hr_mon_tot_qty = 0;
													foreach ($date_range as $key => $val2) 
													{
														$exfactoryQty3=0;
														if(str_replace("'","",$cbo_shipping_status)==2){
															foreach($sub_dep_qty_array[$sub_dep_key][$item_key][$val2]['PO_ID'] as $po_id){
																$exfactoryQty3+=$exfactory_qty_arr[$po_id['id']];
															}
														}
														?>
														<td width="80" align="right"><? echo number_format($sub_dep_qty_array[$sub_dep_key][$item_key][$val2]['qty']-$exfactoryQty3,0);?></td>
														<?
														$hr_mon_tot_qty += $sub_dep_qty_array[$sub_dep_key][$item_key][$val2]['qty']-$exfactoryQty3;
														$vr_gr_tot_qty += $sub_dep_qty_array[$sub_dep_key][$item_key][$val2]['qty']-$exfactoryQty3;
														$mon_gr_tot_array[$val2]['qty'] += $sub_dep_qty_array[$sub_dep_key][$item_key][$val2]['qty']-$exfactoryQty3;
													}
													?>
													<td width="100" align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
												</tr>
												<?
												$i++;
												$k++;

												$vr_tot_qty += $val['qty'];
												$vr_tot_val += $val['val'];
											}
										}
										?>
									</tbody>
								</table>
							</div>
							<table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
								<tfoot>
									<tr>
										<th width="100"></th>
										<th width="100">Grand Total</th>
										<th width="100"><? //echo number_format($vr_tot_qty,0);?></th>
										<?
										foreach ($date_range as $key2 => $val4) 
										{
											?>
											<th width="80" align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
											<?
										}
										?>
										<th><? echo number_format($vr_gr_tot_qty,0);?></th>
									</tr>
								</tfoot>
							</table>            
						</div>
					</div>
				</fieldset>
				<?
			}
		}
		if($report_type==3) //dept wise
		{
			// =============================================== MAIN QUERY =========================================
			$sql = "SELECT  b.id as PO_ID,a.BUYER_NAME, a.TOTAL_SET_QNTY, a.PRO_SUB_DEP,b.pub_shipment_date, TO_CHAR (b.pub_shipment_date, 'MON-YYYY') AS MONTH_YEAR, (B.PO_QUANTITY) as qty, (B.PO_TOTAL_PRICE) as val, A.GMTS_ITEM_ID,  to_char(b.pub_shipment_date,'MM')as month, to_char(b.pub_shipment_date,'YYYY')as year, a.product_dept  FROM wo_po_details_master a, wo_po_break_down b WHERE a.id = b.job_id $sql_cond AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active IN (1) AND b.is_deleted = 0 order by b.pub_shipment_date asc";

			//echo $sql; die;
			$sql_res = sql_select($sql);
			if (count($sql_res) < 1)
			{
				echo '<div style="text-align: center;color: red;font-weight: bold;font-size: 20px;">Data not found.</div>';
				disconnect($con);
				die();
			}

			$sub_dep_dtls_array = array();
			$sub_dep_qty_array = array();
			$sub_dep_summary_array = array();
			$sub_dep_summary_qty_array = array();
			$item_summary_array = array();
			$item_summary_qty_array = array();
			foreach ($sql_res as $row)
			{
				$week_no=weekOfMonth($row[csf('pub_shipment_date')]);
				//month_week_summary
				$summary_month_week[$row[csf('year')]][$row[csf('month')]][$week_no]['QTY']+=$row['QTY']* $row['TOTAL_SET_QNTY'];
				$summary_month_week[$row[csf('year')]][$row[csf('month')]][$week_no]['VAL']+=$row['VAL'];
				//buyer wise Summary
				$buyer_summary_array[$row[csf('year')]][$row['BUYER_NAME']][(int)$row[csf('month')]]['val'] += $row['VAL'];
				$buyer_summary_array[$row[csf('year')]][$row['BUYER_NAME']][(int)$row[csf('month')]]['qty'] += $row['QTY'] * $row['TOTAL_SET_QNTY'];

				$buyer_summary_array[$row[csf('year')]][$row['BUYER_NAME']][(int)$row[csf('month')]]['PO_ID'][$row['PO_ID']]['id']= $row['PO_ID'];
				$buyer_summary_array[$row[csf('year')]][$row['BUYER_NAME']][(int)$row[csf('month')]]['PO_ID'][$row['PO_ID']]['val']+= $row['VAL'];
				$buyer_summary_array[$row[csf('year')]][$row['BUYER_NAME']][(int)$row[csf('month')]]['PO_ID'][$row['PO_ID']]['qty']+=  $row['QTY'] ;

				//All Buyer-Prod Dept wise Summary
				$sub_dep_dtls_array[$row[csf('year')]][$row['BUYER_NAME']][$row[csf('product_dept')]][(int)$row[csf('month')]]['val'] += $row['VAL'];
				$sub_dep_dtls_array[$row[csf('year')]][$row['BUYER_NAME']][$row[csf('product_dept')]][(int)$row[csf('month')]]['qty'] += $row['QTY'] * $row['TOTAL_SET_QNTY'];


				// for summary
				$sub_dep_summary_array[$row['PRO_SUB_DEP']]['qty'] += $row['QTY'] ;//* $row['TOTAL_SET_QNTY']
				$sub_dep_summary_array[$row['PRO_SUB_DEP']]['val'] += $row['VAL'];

				$sub_dep_summary_qty_array[$row['PRO_SUB_DEP']][$row['MONTH_YEAR']]['qty'] += $row['QTY'] ;
				$sub_dep_summary_qty_array[$row['PRO_SUB_DEP']][$row['MONTH_YEAR']]['val'] += $row['VAL'];

				// for item wise summary
				$item_summary_array[$row[csf('gmts_item_id')]]['qty'] += $row['QTY'];
				$item_summary_array[$row[csf('gmts_item_id')]]['val'] += $row['VAL'];

				$item_summary_qty_array[$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['qty'] += $row['QTY'];
				$item_summary_qty_array[$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['val'] += $row['VAL'];
				$item_summary_qty_array[$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['id']=$row['PO_ID'];
				$item_summary_qty_array[$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['val']+=$row['VAL'];
				$item_summary_qty_array[$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['qty']+=$row['QTY'] ;

				// for details
				$sub_dep_qty_array[$row['PRO_SUB_DEP']][$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['id']=$row['PO_ID'];
				$sub_dep_qty_array[$row['PRO_SUB_DEP']][$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['val']+=$row['VAL'];
				$sub_dep_qty_array[$row['PRO_SUB_DEP']][$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['qty']+=$row['QTY'] ;
				$sub_dep_qty_array[$row['PRO_SUB_DEP']][$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['qty'] += $row['QTY'] ;
				$sub_dep_qty_array[$row['PRO_SUB_DEP']][$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['val'] += $row['VAL'];

			
				// $sub_dep_summary_qty_array[$row['PRO_SUB_DEP']][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]= $row['PO_ID'];
				$sub_dep_summary_qty_array[$row['PRO_SUB_DEP']][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['id']= $row['PO_ID'];
				$sub_dep_summary_qty_array[$row['PRO_SUB_DEP']][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['val']+= $row['VAL'];
				$sub_dep_summary_qty_array[$row['PRO_SUB_DEP']][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['qty']+=  $row['QTY'] ; //* $row['TOTAL_SET_QNTY']
				$all_po_array[$row['PO_ID']]= $row['PO_ID'];

				$all_week_arr[$week_no]=$week_no;
				$month_week_arr[$row[csf('month')]][$week_no]=$week_no;
				$all_month_arr[$row[csf('month')]]=(int)$row[csf('month')];
			
			}

			/* echo '<pre>';
			print_r($buyer_summary_array); die; */

			$rowspan = array();
			foreach ($buyer_summary_array as $yearid => $buyerdata) 
			{
				foreach ($buyerdata as $buyerid => $data) 
				{
					$rowspan[$yearid]++;
				}
			}
			$rowspan_dtls = array();
			$rowspan_buyer = array();
			foreach ($sub_dep_dtls_array as $yearid => $yearvalue) 
			{
				foreach ($yearvalue as $buyer_key => $buyer_value) 
				{
					foreach ($buyer_value as $prodid => $month_data)
					{
						$rowspan_dtls[$yearid]++;
						$rowspan_buyer[$yearid][$buyer_key]++;
					}
				}
			}

			$sub_dep_tbl_width = $month_year_tot_width + 300;
			$buyer_name_arr=return_library_array("select id,short_name from  lib_buyer where id in($buyer_name)","id","short_name");
			$buyer_ref_name_arr=return_library_array("select id,exporters_reference from  lib_buyer where id in($buyer_name)","id","exporters_reference");
			
			
			
			$exfactoryQry="select PO_BREAK_DOWN_ID,EX_FACTORY_QNTY from PRO_EX_FACTORY_MST where  IS_DELETED=0 and STATUS_ACTIVE=1 ".where_con_using_array($all_po_array,0,'PO_BREAK_DOWN_ID')."";
			$exfactoryQry_result = sql_select($exfactoryQry);
			$exfactory_qty_arr=array();
			foreach($exfactoryQry_result as $row){
				$exfactory_qty_arr[$row['PO_BREAK_DOWN_ID']]+=$row['EX_FACTORY_QNTY'];
			}		
			ob_start();
			?>
				<fieldset style="width:100%;">
					<style type="text/css">
						.tr_odd td{font-size: 14px !important;}
						.tr_even td{font-size: 12px !important;}
					</style>
					<div id="heading_part">
						<table width="<? echo $sub_dep_tbl_width+120;?>;" cellspacing="0"  align="center" >
							<tr>
								<td colspan="<? echo $colspan;?>" align="center" class="form_caption">
									<strong style="font-size:16px;">Company Name:<? echo  $company_library[$company_name] ;?></strong>
								</td>
							</tr>
							<tr class="form_caption">
								<td colspan="<? echo $colspan;?>" align="center" class="form_caption"> <strong style="font-size:15px;">Month Wise Quantity (Pcs) and Value</strong></td>
							</tr>
							<tr align="center">
								<td colspan="<? echo $colspan;?>" align="center" class="form_caption"> <strong style="font-size:15px;">Date Type : <? echo ($date_type==1) ? "Shipment Date (RFI)": "Original Ship Date";?></strong></td>
							</tr>
							<tr align="center">
								<td colspan="<? echo $colspan;?>" align="center" class="form_caption"> <strong style="font-size:15px;">Runtime : <? echo date("j F, Y h:i:s a");?></strong></td>
							</tr>
						</table>
						<table width="<? echo $sub_dep_tbl_width;?>;" cellspacing="0"  align="center">
							<tr class="form_caption">
								<td colspan="<? echo $colspan;?>" align="center" class="form_caption"> 
									<div style="font-size:20px;text-align:left;">Buyer : <? echo implode(",", $buyer_name_arr);?></div>
									<div style="font-size:20px;text-align:left;">Buyer Ref. : <? echo implode(",", array_filter($buyer_ref_name_arr));?></div>
								</td>
							</tr>
						</table>
					</div>
			<?
			/* echo "<pre>";
			print_r($date_range); die; */
			if($show_value) // report generate with value
			{
				?>
					<!-- ===================================================================================================/
					/ 											Summary of Month/Week Wise								  	/
					/==================================================================================================== -->
					<div id="1st_part">
						<table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
							<caption style="text-align:left;"><h3>Summary of Month/Week wise Quantity [Pcs] and Value [$]</h3></caption>
							<thead>
								<tr>
									<th rowspan="2" width="100">Year</th>
									<th rowspan="2" width="100">Month</th>
									<th rowspan="2" width="200">Quantity (pcs)/Value $</th>
									<th colspan="<?= count($all_week_arr)+1 ?>" width="100+100*<?= count($all_week_arr) ?>">Week Wise</th>
								</tr>
								<tr>
									<? foreach ($all_week_arr as $week_no){ ?>
										<th width="100"><?= ordinal(intval($week_no)) ?> Week Qty</th>
									<? } ?>
									<th width="100">Total</th>
								</tr>									
							</thead>
							<tbody>
								<?								
									
									foreach($summary_month_week as $year=>$month_arr){ 
										
										?>
										<tr bgcolor="<?=$bgcolor;?>" class="tr_odd">
											<td rowspan="<?= count($month_arr)*2 ?>" valign="middle" align="center"><?= $year ?></td>
											<? $i=1;
											foreach($month_arr as $month_id=>$weekdata){ 
												if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
												$tr_class=($i%2==0 ? 'tr_even' : 'tr_odd');
												if($i!=1) { ?><tr bgcolor="<?=$bgcolor;?>" class="tr_odd"> <?}
												?>
												<td rowspan="2" valign="middle" align="center"><?= date('F', mktime(0, 0, 0, intval($month_id), 10)); ?></td>
												<td align="right">Quantity (pcs)</td>
												<?
												$total_qty=0;
												foreach($all_week_arr as $weekid){ ?>
													<td align="right"><?= ($weekdata[$weekid]['QTY'] > 0 ? number_format($weekdata[$weekid]['QTY']) : 0) ?></td>
												<?
													$total_qty+=$weekdata[$weekid]['QTY'];
													$week_wise_total[$weekid]['qty']+=$weekdata[$weekid]['QTY'];
												}
												?>
												<td align="right"><?= number_format($total_qty);  ?></td>
												<tr bgcolor="<?=$bgcolor;?>" class="tr_even">
												<td align="right">Value $</td>
												<?
												$total_val=0;
												foreach($all_week_arr as $weekid){ ?>
													<td align="right">$ <?= ($weekdata[$weekid]['VAL'] > 0 ? number_format($weekdata[$weekid]['VAL']) : 0) ?></td>
												<?
													$total_val+=$weekdata[$weekid]['VAL'];
													$week_wise_total[$weekid]['val']+=$weekdata[$weekid]['VAL'];
												}
												?>
												<td align="right">$ <?= number_format($total_val,2);  ?></td>
												</tr>
												<?
												$i++;
												}  ?>
										</tr>
										<?
										
									}
								?>
							</tbody>
							<tfoot>
								<tr class="tr_odd">
								<th colspan="2" rowspan="2" valign="middle" align="center">Grand Total</th>
								<th align="right">Quantity (pcs)</th>
								<?
									foreach($all_week_arr as $weekid){ ?>
										<th align="right"><?= ($week_wise_total[$weekid]['qty'] > 0 ? number_format($week_wise_total[$weekid]['qty'],2) : 0) ?></th>
									<?
									$grand_total_qty+=$week_wise_total[$weekid]['qty'];
									}
								?>
								<th align="right"><?= number_format($grand_total_qty,2) ?></th>
								</tr>
								<tr class="tr_even">
								<th align="right">Value $</th>
								<?
									foreach($all_week_arr as $weekid){ ?>
										<th align="right"><?= ($week_wise_total[$weekid]['val'] > 0 ? number_format($week_wise_total[$weekid]['val'],2) : 0) ?></th>
									<?
									$grand_total_val+=$week_wise_total[$weekid]['val'];
									}
								?>
								<th align="right">$ <?= number_format($grand_total_val,2) ?></th>
								</tr>
							</tfoot>
						</table>		
					</div>

					<!-- ===================================================================================================/
                    /                                           BUYER SUMMARY PART                                          /
                    /==================================================================================================== -->
                    <div id="1st_part"> 
						<table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
							<caption style="text-align:left;"><h2>Buyer wise Summary</h2></caption>
							<thead>
								<tr>
									<th rowspan="2" width="100">Year</th>
									<th rowspan="2" width="100">Buyer</th>
									<th rowspan="2" width="100">Quantity(pcs)/Value</th>
									<th width="<? echo count($all_month_arr)*80;?>" colspan="<? echo count($all_month_arr);?>">Month Wise</th>                                            
									<th rowspan="2" width="100">Grand Total</th>
								</tr>
								<tr>
									<?
									foreach ($all_month_arr as $key => $val) 
									{
										?>
										<th width="80"><? echo $months[$val];?></th>
										<?
									}
									?>
								</tr>
							</thead>
							<tbody>
								<?
								$i=1;
								$vr_tot_qty = 0;
								$vr_tot_val = 0;
								$vr_gr_tot_qty = 0;
								$vr_gr_tot_val = 0;
								$mon_gr_tot_array = array();
								foreach ($buyer_summary_array as $year_id => $buyer_data) 
								{
									$ref = 0;
									foreach ($buyer_data as $buyer_key => $mothdata) 
									{
										/* echo '<pre>';
										print_r($mothdata); die; */
										if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
										?>
										<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" class="tr_even">
											<? if($ref==0){?>
											<td valign="middle" align="center" width="100"  rowspan="<? echo $rowspan[$year_id]*2;?>"><? echo strtoupper($year_id);?></td>
											<? $ref++;}?>
											<td valign="middle" align="center" width="100" rowspan="2"><? echo strtoupper($buyer_arr[$buyer_key]);?></td>
											<td width="100" align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
											<?                          
											$total_qty = 0;
											foreach ($all_month_arr as $mid) 
											{
												
												$exfactoryQty=0; $total_val=0;
												if(str_replace("'","",$cbo_shipping_status)==2){
													foreach($buyer_summary_array[$year_id][$buyer_key][$mid]['PO_ID'] as $po_id){
														$exfactoryQty+=$exfactory_qty_arr[$po_id['id']];
													}
												}                                                        
												?>
												<td align="right" title="<?= $mothdata[$mid]['qty'].'-'.$exfactoryQty ?>"><?= ($mothdata[$mid]['qty'] > 0 ? fn_number_format($mothdata[$mid]['qty']-$exfactoryQty) : 0) ?></td>
												<?
												$total_qty+=$mothdata[$mid]['qty'];
												$yearbuyer_wise_total[$mid]['qty']+=$mothdata[$mid]['qty'];
											}
											?>
											<td width="100" align="right"><? echo number_format($total_qty,0);?></td>
										</tr>
										<tr class="tr_odd">
										<td width="100" align="right">Value</td>   
										<?
										$total_val = 0;
										foreach ($all_month_arr as $mid) 
										{
											$exfactoryVal=0;
											$ave_rate=0;
											if(str_replace("'","",$cbo_shipping_status)==2){
												foreach($buyer_summary_array[$year_id][$buyer_key][$mid]['PO_ID'] as $po_id){
													$ave_rate=$po_id['val']/$po_id['qty'];
													$exfactoryVal+=$exfactory_qty_arr[$po_id['id']]*$ave_rate;
												}
											}
											?>
											<td align="right"><?= ($mothdata[$mid]['val'] > 0 ? fn_number_format($mothdata[$mid]['val']-$exfactoryVal) : 0) ?></td>
											<?
											$total_val+=$mothdata[$mid]['val'];
											$yearbuyer_wise_total[$mid]['val']+=$mothdata[$mid]['val'];
										}
										?>
										<td width="100" align="right">$ <? echo number_format($total_val,2);?></td>
									</tr>
										<?
										$i++;
										$exfactoryVal=0;
										$vr_tot_qty += $total_qty;
										$vr_tot_val += $total_val;
									}
								}
								?>
							</tbody>
							<tfoot>
								<tr>
									<th width="100" rowspan="2"></th>
									<th width="100" rowspan="2">Grand Total</th>
									<th width="100"><? //echo number_format($vr_tot_qty,0);?></th>
									<?
									foreach ($all_month_arr as $mid) 
									{
										?>
										<th width="80" align="right"><? echo number_format($yearbuyer_wise_total[$mid]['qty'],0);?></th>
										<?
									}
									?>
									<th><? echo number_format($vr_tot_qty,0);?></th>
								</tr>
								<tr>
									<th><? //echo number_format($vr_tot_val,2);?></th>
									<?
									foreach ($all_month_arr as $mid) 
									{
										?>
										<th width="80" align="right">$ <? echo number_format($yearbuyer_wise_total[$mid]['val'],2);?></th>
										<?
									}
									?>
									<th>$ <? echo number_format($vr_tot_val,2);?></th>
								</tr>
							</tfoot>
						</table>       
                    </div>

					<!-- ===================================================================================================/
					/ 												BUYER-PROD DEPT WISE SUMMARY									  		/
					/==================================================================================================== -->
					<div id="3rd_part">
						<div style="margin-top: 20px;width:<? echo 520+(count($all_month_arr)*80);;?>px;" id="">            
							
							<table width="<? echo 500+(count($all_month_arr)*80);?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
								<caption style="text-align:left;"><h2>All Buyer-Prod Dept wise Summary of Month/Week wise Quantity [Pcs] and Value [$]</h2></caption>
								<thead>
									<tr>
										<th rowspan="2" width="100">Year</th>
										<th rowspan="2" width="100">Buyer</th>
										<th rowspan="2" width="100">Prod Dept</th>
										<th rowspan="2" width="100">Quantity(pcs)/Value</th>
										<th width="<? echo count($all_month_arr)*80;?>" colspan="<? echo count($all_month_arr);?>">Month Wise</th> 
										<th rowspan="2" width="100">Grand Total</th>
									</tr>
									<tr>
										<?
										foreach ($all_month_arr as $key => $val) 
										{
											?>
											<th width="80"><? echo $months[$val];?></th>
											<?
										}
										?>
									</tr>
								</thead>
							</table>
							<div style="width:<? echo 520+(count($all_month_arr)*80);;?>px;overflow-y:scroll;max-height:350px;" id="scroll_body_3">
								<table width="<? echo 500+(count($all_month_arr)*80);;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
									<tbody>
										<?
										$k=1; $vr_tot_qty=$vr_tot_val=0;
										foreach ($sub_dep_dtls_array as $year_id => $year_val) 
										{	
											$ref = 0;
											foreach ($year_val as $buyer_key => $buyer_val) 
											{
												$byr = 0;
												foreach ($buyer_val as $prod_id => $mothdata) 
												{
													
													if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
													?>
													<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" class="tr_even">
														<? if($ref==0){?>
														<td valign="top" width="100" rowspan="<? echo $rowspan_dtls[$year_id]*2;?>"><? echo strtoupper($year_id);?></td>
														<? $ref++;} if($byr==0){?>
														<td valign="top" width="100" rowspan="<? echo $rowspan_buyer[$year_id][$buyer_key]*2;?>"><? echo strtoupper($buyer_arr[$buyer_key]);?></td>
														<? $byr++;}?>
														<td width="100" rowspan="2"><? echo strtoupper($product_dept[$prod_id]); ?></td>
														<td width="100" align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
														<?                			
														$total_qty = 0;
														foreach ($all_month_arr as $mid) 
														{
															?>
															<td align="right" title="<?= $mothdata[$mid]['qty'].'-'.$exfactoryQty ?>"><?= ($mothdata[$mid]['qty'] > 0 ? fn_number_format($mothdata[$mid]['qty']) : 0) ?></td>
															<?
															$total_qty+=$mothdata[$mid]['qty'];
															$vr_tot_qty += $mothdata[$mid]['qty'];
															$yearbuyerdep_wise_total[$mid]['qty']+=$mothdata[$mid]['qty'];
														}
														?>
														<td width="100" align="right"><? echo number_format($total_qty,0);?></td>
													</tr>
													<tr class="tr_odd">
													<td width="100" align="right">Value<? //echo number_format($val['val'],2);?></td>	
													<?
													$total_val = 0;
													foreach ($all_month_arr as $mid) 
													{
														?>
														<td align="right" title="<?= $mothdata[$mid]['val'] ?>"><?= ($mothdata[$mid]['val'] > 0 ? fn_number_format($mothdata[$mid]['val']) : 0) ?></td>
														<?
														$total_val+=$mothdata[$mid]['val'];
														$vr_tot_val += $mothdata[$mid]['val'];
														$yearbuyerdep_wise_total[$mid]['val']+=$mothdata[$mid]['val'];
													}
													?>
													<td width="100" align="right">$ <? echo number_format($total_val,2);?></td>
												</tr>
													<?
													$i++;
													$k++;

													
													
												}
											}
										}
										?>
									</tbody>
								</table>
							</div>
							<table width="<? echo 500+(count($all_month_arr)*80);?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
								<tfoot>
									<tr>
										<th width="100" rowspan="2"></th>
										<th width="100" rowspan="2"></th>
										<th width="100" rowspan="2"></th>
										<th width="100" rowspan="2">Grand Total</th>
										<?
											foreach ($all_month_arr as $mid) 
											{
												?>
												<th width="80" align="right"><? echo number_format($yearbuyerdep_wise_total[$mid]['qty'],0);?></th>
												<?
											}
										?>
										<th width="100"><? echo number_format($vr_tot_qty,0);?></th>
									</tr>
									<tr>
										<?
											foreach ($all_month_arr as $mid) 
											{
												?>
												<th width="80" align="right"><? echo number_format($yearbuyerdep_wise_total[$mid]['val'],0);?></th>
												<?
											}
										?>
										<th width="100">$ <? echo number_format($vr_tot_val,2);?></th>
									</tr>
								</tfoot>
							</table>            
						</div>
					</div>
				</fieldset>
				<?
			}
			else // report generate without value
			{
				?>
					<!-- ===================================================================================================/
					/ 										SUB DEP WISE SUMMARY PART									  	/
					/==================================================================================================== -->
					<div id="1st_part">
						<table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1">
							<caption style="text-align:left;"><h2>Sub Dept wise Summary</h2></caption>
							<thead>
								<tr>
									<th rowspan="2" width="100">Sub Dept.</th>
									<th rowspan="2" width="100">Quantity(pcs)</th>
									<?
									foreach ($year_month_count as $key => $val) 
									{
										?>
										<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
										<?
									}
									?>
									<th rowspan="2" width="100">Grand Total</th>
								</tr>
								<tr>
									<?
									foreach ($date_range as $key => $val) 
									{
										?>
										<th width="80"><? echo $val;?></th>
										<?
									}
									?>
								</tr>
							</thead>
							<tbody>
								<?
								$i=1;
								$vr_tot_qty = 0;
								$vr_tot_val = 0;
								$vr_gr_tot_qty = 0;
								$vr_gr_tot_val = 0;
								$mon_gr_tot_array = array();
								foreach ($sub_dep_summary_array as $sub_dep_key => $val) 
								{
									if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
									<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" class="tr_odd">
										<td><? echo strtoupper($lib_sub_dept_array[$sub_dep_key]);?></td>
										<td align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
										<?                			
										$hr_mon_tot_qty = 0;
										foreach ($date_range as $key => $val2) 
										{
											$exfactoryQty=0;
											if(str_replace("'","",$cbo_shipping_status)==2){
												foreach($sub_dep_summary_qty_array[$sub_dep_key][$val2]['PO_ID'] as $po_id){
												$exfactoryQty+=$exfactory_qty_arr[$po_id['id']];
												}
											}

											?>
											<td align="right"><? echo number_format(($sub_dep_summary_qty_array[$sub_dep_key][$val2]['qty']-$exfactoryQty),0);?></td>
											<?
											$hr_mon_tot_qty += $sub_dep_summary_qty_array[$sub_dep_key][$val2]['qty']-$exfactoryQty;
											$vr_gr_tot_qty += $sub_dep_summary_qty_array[$sub_dep_key][$val2]['qty']-$exfactoryQty;
											$mon_gr_tot_array[$val2]['qty'] += $sub_dep_summary_qty_array[$sub_dep_key][$val2]['qty']-$exfactoryQty;
										}
										?>
										<td align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
									</tr>
									<?
									$i++;

									$vr_tot_qty += $val['qty'];
									$vr_tot_val += $val['val'];
								}
								?>
							</tbody>
							<tfoot>
								<tr>
									<th>Grand Total</th>
									<th><? //echo number_format($vr_tot_qty,0);?></th>
									<?
									foreach ($date_range as $key2 => $val4) 
									{
										?>
										<th align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
										<?
									}
									?>
									<th><? echo number_format($vr_gr_tot_qty,0);?></th>
								</tr>
							</tfoot>
						</table>
					</div>        
					<!-- ===================================================================================================/
					/ 											ITEM WISE SUMMARY PART									  	/
					/==================================================================================================== -->
					<div  id="2nd_part">
						<div style="margin-top: 20px;width:<? echo $sub_dep_tbl_width+20;?>px;">            
							
							<table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
								<caption style="text-align:left;"><h2>Item wise Summary</h2></caption>
								<thead>
									<tr>
										<th rowspan="2" width="100">Item</th>
										<th rowspan="2" width="100">Quantity(pcs)</th>
										<?
										foreach ($year_month_count as $key => $val) 
										{
											?>
											<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
											<?
										}
										?>
										<th rowspan="2" width="100">Grand Total</th>
									</tr>
									<tr>
										<?
										foreach ($date_range as $key => $val) 
										{
											?>
											<th width="80"><? echo $val;?></th>
											<?
										}
										?>
									</tr>
								</thead>
							</table>
							<div style="width:<? echo $sub_dep_tbl_width+20;?>px;overflow-y:scroll;max-height:350px;" id="scroll_body_2">
								<table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
									<tbody>
										<?
										$k=1;
										$vr_tot_qty = 0;
										$vr_tot_val = 0;
										$vr_gr_tot_qty = 0;
										$vr_gr_tot_val = 0;
										$mon_gr_tot_array = array();
										foreach ($item_summary_array as $item_key => $val) 
										{
											if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
											?>
											<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" class="tr_odd">
												<td width="100"><? echo strtoupper($garments_item[$item_key]);?></td>
												<td width="100" align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
												<?                			
												$hr_mon_tot_qty = 0;
												foreach ($date_range as $key => $val2) 
												{
													$exfactoryQty2=0;
													if(str_replace("'","",$cbo_shipping_status)==2){
														foreach($item_summary_qty_array[$item_key][$val2]['PO_ID'] as $po_id){
															$exfactoryQty2+=$exfactory_qty_arr[$po_id['id']];
														}
													}
													?>
													<td width="80" align="right"><? echo number_format($item_summary_qty_array[$item_key][$val2]['qty']-$exfactoryQty2,0);?></td>
													<?
													$hr_mon_tot_qty += $item_summary_qty_array[$item_key][$val2]['qty']-$exfactoryQty2;
													$vr_gr_tot_qty += $item_summary_qty_array[$item_key][$val2]['qty']-$exfactoryQty2;
													$mon_gr_tot_array[$val2]['qty'] += $item_summary_qty_array[$item_key][$val2]['qty']-$exfactoryQty2;
												}
												?>
												<td width="100" align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
											</tr>
											<?
											$i++;
											$k++;

											$vr_tot_qty += $val['qty'];
											$vr_tot_val += $val['val'];
										}
										?>
									</tbody>
								</table>
							</div>
							<table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
								<tfoot>
									<tr>
										<th width="100">Grand Total</th>
										<th width="100"><? //echo number_format($vr_tot_qty,0);?></th>
										<?
										foreach ($date_range as $key2 => $val4) 
										{
											?>
											<th width="80" align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
											<?
										}
										?>
										<th><? echo number_format($vr_gr_tot_qty,0);?></th>
									</tr>
								</tfoot>
							</table>            
						</div>
					</div>
					<!-- ===================================================================================================/
					/ 												DETAILS PART									  		/
					/==================================================================================================== -->
					<div  id="3rd_part">
						<div style="margin-top: 20px;width:<? echo $sub_dep_tbl_width+120;?>px;">            
							
							<table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
								<caption style="text-align:left;"><h2>Details</h2></caption>
								<thead>
									<tr>
										<th rowspan="2" width="100">Sub Dept.</th>
										<th rowspan="2" width="100">Item</th>
										<th rowspan="2" width="100">Quantity(pcs)</th>
										<?
										foreach ($year_month_count as $key => $val) 
										{
											?>
											<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
											<?
										}
										?>
										<th rowspan="2" width="100">Grand Total</th>
									</tr>
									<tr>
										<?
										foreach ($date_range as $key => $val) 
										{
											?>
											<th width="80"><? echo $val;?></th>
											<?
										}
										?>
									</tr>
								</thead>
							</table>
							<div style="width:<? echo $sub_dep_tbl_width+120;?>px;overflow-y:scroll;max-height:350px;" id="scroll_body_3">
								<table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
									<tbody>
										<?
										$k=1;
										$vr_tot_qty = 0;
										$vr_tot_val = 0;
										$vr_gr_tot_qty = 0;
										$vr_gr_tot_val = 0;
										$mon_gr_tot_array = array();
										foreach ($sub_dep_dtls_array as $sub_dep_key => $sub_dep_val) 
										{
											foreach ($sub_dep_val as $item_key => $val) 
											{
												if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
												?>
												<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" class="tr_odd">
													<td width="100"><? echo strtoupper($lib_sub_dept_array[$sub_dep_key]);?></td>
													<td width="100"><? echo strtoupper($garments_item[$item_key]);?></td>
													<td width="100" align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
													<?                			
													$hr_mon_tot_qty = 0;
													foreach ($date_range as $key => $val2) 
													{
														$exfactoryQty3=0;
														if(str_replace("'","",$cbo_shipping_status)==2){
															foreach($sub_dep_qty_array[$sub_dep_key][$item_key][$val2]['PO_ID'] as $po_id){
																$exfactoryQty3+=$exfactory_qty_arr[$po_id['id']];
															}
														}
														?>
														<td width="80" align="right"><? echo number_format($sub_dep_qty_array[$sub_dep_key][$item_key][$val2]['qty']-$exfactoryQty3,0);?></td>
														<?
														$hr_mon_tot_qty += $sub_dep_qty_array[$sub_dep_key][$item_key][$val2]['qty']-$exfactoryQty3;
														$vr_gr_tot_qty += $sub_dep_qty_array[$sub_dep_key][$item_key][$val2]['qty']-$exfactoryQty3;
														$mon_gr_tot_array[$val2]['qty'] += $sub_dep_qty_array[$sub_dep_key][$item_key][$val2]['qty']-$exfactoryQty3;
													}
													?>
													<td width="100" align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
												</tr>
												<?
												$i++;
												$k++;

												$vr_tot_qty += $val['qty'];
												$vr_tot_val += $val['val'];
											}
										}
										?>
									</tbody>
								</table>
							</div>
							<table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
								<tfoot>
									<tr>
										<th width="100"></th>
										<th width="100">Grand Total</th>
										<th width="100"><? //echo number_format($vr_tot_qty,0);?></th>
										<?
										foreach ($date_range as $key2 => $val4) 
										{
											?>
											<th width="80" align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
											<?
										}
										?>
										<th><? echo number_format($vr_gr_tot_qty,0);?></th>
									</tr>
								</tfoot>
							</table>            
						</div>
					</div>
				</fieldset>
				<?
			}

		}
		else // buyer wise 
		{		
			$sql = "SELECT b.id as PO_ID,a.BUYER_NAME, a.PRO_SUB_DEP, a.TOTAL_SET_QNTY, TO_CHAR (b.shipment_date, 'MON-YYYY') AS MONTH_YEAR, sum(B.PO_QUANTITY) as qty, sum(B.PO_TOTAL_PRICE) as val, A.GMTS_ITEM_ID
			FROM wo_po_details_master a, wo_po_break_down b
			WHERE a.id = b.job_id $sql_cond AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active IN (1) AND b.is_deleted = 0
			GROUP BY a.buyer_name, a.pro_sub_dep, b.id, b.shipment_date, a.total_set_qnty, A.GMTS_ITEM_ID";

			$sql_res = sql_select($sql);
			if (count($sql_res) < 1)
			{
				echo '<div style="text-align: center;color: red;font-weight: bold;font-size: 20px;">Data not found.</div>';
				disconnect($con);
				die();
			}

			$sub_dep_dtls_array = array();
			$sub_dep_qty_array = array();
			$sub_dep_summary_array = array();
			$sub_dep_summary_qty_array = array();
			$item_summary_array = array();
			$item_summary_qty_array = array();
			$count = 1;
			foreach ($sql_res as $row)
			{
				// for summary
				$sub_dep_summary_array[$buyer_ref_arr[$row['BUYER_NAME']]][$row['BUYER_NAME']]['val'] += $row['VAL'];
				$sub_dep_summary_array[$buyer_ref_arr[$row['BUYER_NAME']]][$row['BUYER_NAME']]['qty'] += $row['QTY'] * $row['TOTAL_SET_QNTY'];

				$sub_dep_summary_qty_array[$row['BUYER_NAME']][$row['MONTH_YEAR']]['val'] += $row['VAL'];
				$sub_dep_summary_qty_array[$row['BUYER_NAME']][$row['MONTH_YEAR']]['qty'] += $row['QTY'] * $row['TOTAL_SET_QNTY'];

				// for item wise summary
				$item_summary_array[$row[csf('gmts_item_id')]]['val'] += $row['VAL'];
				$item_summary_array[$row[csf('gmts_item_id')]]['qty'] += $row['QTY'] * $row['TOTAL_SET_QNTY'];

				$item_summary_qty_array[$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['val'] += $row['VAL'];
				$item_summary_qty_array[$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['qty'] += $row['QTY'] * $row['TOTAL_SET_QNTY'];
				$item_summary_qty_array[$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['id']=$row['PO_ID'];
				$item_summary_qty_array[$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['val']+=$row['VAL'];
				$item_summary_qty_array[$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['qty']+=$row['QTY'] * $row['TOTAL_SET_QNTY'];

				// for details
				$sub_dep_qty_array[$row['BUYER_NAME']][$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['id']=$row['PO_ID'];
				$sub_dep_qty_array[$row['BUYER_NAME']][$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['val']+=$row['VAL'];
				$sub_dep_qty_array[$row['BUYER_NAME']][$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['qty']+=$row['QTY'] * $row['TOTAL_SET_QNTY'];
				$sub_dep_qty_array[$row['BUYER_NAME']][$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['val'] += $row['VAL'];
				$sub_dep_qty_array[$row['BUYER_NAME']][$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['qty'] += $row['QTY'] * $row['TOTAL_SET_QNTY'];

				$sub_dep_dtls_array[$buyer_ref_arr[$row['BUYER_NAME']]][$row['BUYER_NAME']][$row[csf('gmts_item_id')]]['val'] += $row['VAL'];
				$sub_dep_dtls_array[$buyer_ref_arr[$row['BUYER_NAME']]][$row['BUYER_NAME']][$row[csf('gmts_item_id')]]['qty'] += $row['QTY'] * $row['TOTAL_SET_QNTY'];
			
				$sub_dep_summary_qty_array[$row['BUYER_NAME']][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['id']= $row['PO_ID'];
				$sub_dep_summary_qty_array[$row['BUYER_NAME']][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['val']+= $row['VAL'];
				$sub_dep_summary_qty_array[$row['BUYER_NAME']][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['qty']+=  $row['QTY'] * $row['TOTAL_SET_QNTY'];
				$all_po_array[$row['PO_ID']]= $row['PO_ID'];
			
			
			}

			$rowspan = array();
			foreach ($sub_dep_summary_array as $ref_key => $ref_value) 
			{
				foreach ($ref_value as $buyer_key => $buyer_value) 
				{
					$rowspan[$ref_key]++;
				}
			}

			$rowspan_dtls = array();
			$rowspan_buyer = array();
			foreach ($sub_dep_dtls_array as $ref_key => $ref_value) 
			{
				foreach ($ref_value as $buyer_key => $buyer_value) 
				{
					foreach ($buyer_value as $item_key => $item_value) 
					{
						$rowspan_dtls[$ref_key]++;
						$rowspan_buyer[$ref_key][$buyer_key]++;
					}
				}
			}
			// echo "<pre>";print_r($rowspan_buyer);die();

			
			$exfactoryQry="select PO_BREAK_DOWN_ID,EX_FACTORY_QNTY from PRO_EX_FACTORY_MST where  IS_DELETED=0 and STATUS_ACTIVE=1 ".where_con_using_array($all_po_array,0,'PO_BREAK_DOWN_ID')."";
			$exfactoryQry_result = sql_select($exfactoryQry);
			$exfactory_qty_arr=array();
			foreach($exfactoryQry_result as $row){
				$exfactory_qty_arr[$row['PO_BREAK_DOWN_ID']]+=$row['EX_FACTORY_QNTY'];
			}

			$sub_dep_tbl_width = $month_year_tot_width + 400;
			ob_start();
			?>
				<fieldset style="width:100%;">
					<style type="text/css">
						.tr_odd td{font-size: 12px !important;}
						.tr_even td{font-size: 14px !important;}
					</style>
					<table width="<? echo $sub_dep_tbl_width+120;?>;" cellspacing="0"  align="center">
						<tr>
							<td colspan="<? echo $colspan;?>" align="center" class="form_caption">
								<strong style="font-size:16px;">Company Name:<? echo  $company_library[$company_name] ;?></strong>
							</td>
						</tr>
						<tr class="form_caption">
							<td colspan="<? echo $colspan;?>" align="center" class="form_caption"> <strong style="font-size:15px;">Month Wise Quantity (Pcs) and Value</strong></td>
						</tr>
						<tr align="center">
							<td colspan="<? echo $colspan;?>" align="center" class="form_caption"> <strong style="font-size:15px;">Date Type : <? echo ($date_type==1) ? "Shipment Date (RFI)": "Original Ship Date";?></strong></td>
						</tr>
							<tr align="center">
								<td colspan="<? echo $colspan;?>" align="center" class="form_caption"> <strong style="font-size:15px;">Runtime : <? echo date("j F, Y h:i:s a");?></strong></td>
							</tr>
					</table>
			<?
			if($show_value) // report generate with value
			{
				?>
					<!-- ===================================================================================================/
					/ 											BUYER SUMMARY PART 										  	/
					/==================================================================================================== -->
					<div id="1st_part">
						<div style="width:<? echo $sub_dep_tbl_width+20;?>px;">  
							<table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
								<caption style="text-align:left;"><h2>Buyer wise Summary</h2></caption>
								<thead>
									<tr>
										<th rowspan="2" width="100">Buyer Ref.</th>
										<th rowspan="2" width="100">Buyer</th>
										<th rowspan="2" width="100">Quantity(pcs)/Value</th>
										<?
										foreach ($year_month_count as $key => $val) 
										{
											?>
											<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
											<?
										}
										?>
										<th rowspan="2" width="100">Grand Total</th>
									</tr>
									<tr>
										<?
										foreach ($date_range as $key => $val) 
										{
											?>
											<th width="80"><? echo $val;?></th>
											<?
										}
										?>
									</tr>
								</thead>
							</table>
							<div style="width:<? echo $sub_dep_tbl_width+20;?>px;overflow-y:scroll;max-height:350px;" id="scroll_body_1">
								<table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
									<tbody>
										<?
										$i=1;
										$vr_tot_qty = 0;
										$vr_tot_val = 0;
										$vr_gr_tot_qty = 0;
										$vr_gr_tot_val = 0;
										$mon_gr_tot_array = array();
										foreach ($sub_dep_summary_array as $ref_key => $ref_val) 
										{
											$ref = 0;
											foreach ($ref_val as $buyer_key => $val) 
											{
												if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
												?>
												<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" class="tr_even">
													<? if($ref==0){?>
													<td valign="top" width="100"  rowspan="<? echo $rowspan[$ref_key]*2;?>"><? echo strtoupper($buyer_ref_arr[$buyer_key]);?></td>
													<? $ref++;}?>
													<td valign="top" width="100" rowspan="2"><? echo strtoupper($buyer_arr[$buyer_key]);?></td>
													<td width="100" align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
													<?                			
													$hr_mon_tot_qty = 0;
													foreach ($date_range as $key => $val2) 
													{
														
														$exfactoryQty=0;
														if(str_replace("'","",$cbo_shipping_status)==2){
															foreach($sub_dep_summary_qty_array[$buyer_key][$val2]['PO_ID'] as $po_id){
																$exfactoryQty+=$exfactory_qty_arr[$po_id['id']];
															}
														}
														
														?>
														<td width="80" align="right"><? echo number_format(($sub_dep_summary_qty_array[$buyer_key][$val2]['qty']-$exfactoryQty),0);?></td>
														<?
														$hr_mon_tot_qty += $sub_dep_summary_qty_array[$buyer_key][$val2]['qty']-$exfactoryQty;
														$vr_gr_tot_qty += $sub_dep_summary_qty_array[$buyer_key][$val2]['qty']-$exfactoryQty;
														$mon_gr_tot_array[$val2]['qty'] += $sub_dep_summary_qty_array[$buyer_key][$val2]['qty']-$exfactoryQty;
													}
													?>
													<td width="100" align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
												</tr>
												<tr class="tr_odd">
												<td width="100" align="right">Value<? //echo number_format($val['val'],2);?></td>	
												<?
												$hr_mon_tot_val = 0;
												foreach ($date_range as $key2 => $val3) 
												{
													$exfactoryVal=0;
													$ave_rate=0;
													if(str_replace("'","",$cbo_shipping_status)==2){
														foreach($sub_dep_summary_qty_array[$buyer_key][$val3]['PO_ID'] as $po_id){
															$ave_rate=$po_id['val']/$po_id['qty'];
															$exfactoryVal+=$exfactory_qty_arr[$po_id['id']]*$ave_rate;
														}
													}
													?>
													<td width="80" align="right">$ <? echo number_format($sub_dep_summary_qty_array[$buyer_key][$val3]['val']-$exfactoryVal,2);?></td>
													<?
													$hr_mon_tot_val += $sub_dep_summary_qty_array[$buyer_key][$val3]['val']-$exfactoryVal;
													$vr_gr_tot_val += $sub_dep_summary_qty_array[$buyer_key][$val3]['val']-$exfactoryVal;
													$mon_gr_tot_array[$val3]['val'] += $sub_dep_summary_qty_array[$buyer_key][$val3]['val']-$exfactoryVal;
												}
												?>
												<td width="100" align="right">$ <? echo number_format($hr_mon_tot_val,2);?></td>
											</tr>
												<?
												$i++;
												$exfactoryVal=0;
												$vr_tot_qty += $val['qty'];
												$vr_tot_val += $val['val'];
											}
										}
										?>
									</tbody>	                
								</table>
							</div>
							<table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
								<tfoot>
									<tr>
										<th width="100" rowspan="2"></th>
										<th width="100" rowspan="2">Grand Total</th>
										<th width="100"><? //echo number_format($vr_tot_qty,0);?></th>
										<?
										foreach ($date_range as $key2 => $val4) 
										{
											?>
											<th width="80" align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
											<?
										}
										?>
										<th><? echo number_format($vr_gr_tot_qty,0);?></th>
									</tr>
									<tr>
										<th><? //echo number_format($vr_tot_val,2);?></th>
										<?
										foreach ($date_range as $key2 => $val5) 
										{
											?>
											<th width="80" align="right">$ <? echo number_format($mon_gr_tot_array[$val5]['val'],2);?></th>
											<?
										}
										?>
										<th>$ <? echo number_format($vr_gr_tot_val,2);?></th>
									</tr>
								</tfoot>
							</table>
						</div>        
					</div>
					<!-- ===================================================================================================/
					/ 											ITEM WISE SUMMARY PART									  	/
					/==================================================================================================== -->
					<div id="2nd_part">
						<div style="margin-top: 20px;width:<? echo $sub_dep_tbl_width+20;?>px;" id="">            
							
							<table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
								<caption style="text-align:left;"><h2>Item wise Summary</h2></caption>
								<thead>
									<tr>
										<th rowspan="2" width="100">Item</th>
										<th rowspan="2" width="100">Quantity(pcs)/Value</th>
										<?
										foreach ($year_month_count as $key => $val) 
										{
											?>
											<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
											<?
										}
										?>
										<th rowspan="2" width="100">Grand Total</th>
									</tr>
									<tr>
										<?
										foreach ($date_range as $key => $val) 
										{
											?>
											<th width="80"><? echo $val;?></th>
											<?
										}
										?>
									</tr>
								</thead>
							</table>
							<div style="width:<? echo $sub_dep_tbl_width+20;?>px;overflow-y:scroll;max-height:350px;" id="scroll_body_2">
								<table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
									<tbody>
										<?
										$k=1;
										$vr_tot_qty = 0;
										$vr_tot_val = 0;
										$vr_gr_tot_qty = 0;
										$vr_gr_tot_val = 0;
										$mon_gr_tot_array = array();
										foreach ($item_summary_array as $item_key => $val) 
										{
											$item_arr = explode(',', $item_key);
											$items_str = '';

											foreach ($item_arr as $item_id) {
												$items_str .= $garments_item[$item_id] . ', ';
											}

											$items_str = rtrim($items_str, ' ,');

											if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
											?>
											<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" class="tr_even">
												<td valign="top" width="100" rowspan="2"><? echo strtoupper($items_str); ?></td>
												<td width="100" align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
												<?                			
												$hr_mon_tot_qty = 0;
												foreach ($date_range as $key => $val2) 
												{
													$exfactoryQty2=0;
													if(str_replace("'","",$cbo_shipping_status)==2){
														foreach($item_summary_qty_array[$item_key][$val2]['PO_ID'] as $po_id){
															$exfactoryQty2+=$exfactory_qty_arr[$po_id['id']];
														}
													}
													?>
													<td width="80" align="right"><? echo number_format($item_summary_qty_array[$item_key][$val2]['qty']-$exfactoryQty2,0);?></td>
													<?
													$hr_mon_tot_qty += $item_summary_qty_array[$item_key][$val2]['qty']-$exfactoryQty2;
													$vr_gr_tot_qty += $item_summary_qty_array[$item_key][$val2]['qty']-$exfactoryQty2;
													$mon_gr_tot_array[$val2]['qty'] += $item_summary_qty_array[$item_key][$val2]['qty']-$exfactoryQty2;
												}
												?>
												<td width="100" align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
											</tr>
											<tr class="tr_odd">
											<td width="100" align="right">Value<? //echo number_format($val['val'],2);?></td>	
											<?
											$hr_mon_tot_val = 0;
											foreach ($date_range as $key2 => $val3) 
											{
												$exfactoryVal2=0;
												$ave_rate2=1;

												if(str_replace("'","",$cbo_shipping_status)==2){
													foreach($item_summary_qty_array[$item_key][$val3]['PO_ID'] as $po_id){
														$ave_rate2=$po_id['val']/$po_id['qty'];
														$exfactoryVal2+=$exfactory_qty_arr[$po_id['id']]*$ave_rate2;
													}
												}
												?>
												<td width="80" align="right">$ <? echo number_format($item_summary_qty_array[$item_key][$val3]['val']-$exfactoryVal2,2);?></td>
												<?
												$hr_mon_tot_val += $item_summary_qty_array[$item_key][$val3]['val']-$exfactoryVal2;
												$vr_gr_tot_val += $item_summary_qty_array[$item_key][$val3]['val']-$exfactoryVal2;
												$mon_gr_tot_array[$val3]['val'] += $item_summary_qty_array[$item_key][$val3]['val']-$exfactoryVal2;
											}
											?>
											<td width="100" align="right">$ <? echo number_format($hr_mon_tot_val,2);?></td>
										</tr>
											<?
											$i++;
											$k++;

											$vr_tot_qty += $val['qty'];
											$vr_tot_val += $val['val'];
										}
										?>
									</tbody>
								</table>
							</div>
							<table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
								<tfoot>
									<tr>
										<th width="100" rowspan="2">Grand Total</th>
										<th width="100"><? //echo number_format($vr_tot_qty,0);?></th>
										<?
										foreach ($date_range as $key2 => $val4) 
										{
											?>
											<th width="80" align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
											<?
										}
										?>
										<th><? echo number_format($vr_gr_tot_qty,0);?></th>
									</tr>
									<tr>
										<th><? //echo number_format($vr_tot_val,2);?></th>
										<?
										foreach ($date_range as $key2 => $val5) 
										{
											?>
											<th width="80" align="right">$ <? echo number_format($mon_gr_tot_array[$val5]['val'],2);?></th>
											<?
										}
										?>
										<th width="100">$ <? echo number_format($vr_gr_tot_val,2);?></th>
									</tr>
								</tfoot>
							</table>            
						</div>
					</div>

					<!-- ===================================================================================================/
					/ 												DETAILS PART									  		/
					/==================================================================================================== -->
					<div id="3rd_part">
						<div style="margin-top: 20px;width:<? echo $sub_dep_tbl_width+120;?>px;" id="">            
							
							<table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
								<caption style="text-align:left;"><h2>Details</h2></caption>
								<thead>
									<tr>
										<th rowspan="2" width="100">Buyer Ref.</th>
										<th rowspan="2" width="100">Buyer</th>
										<th rowspan="2" width="100">Item</th>
										<th rowspan="2" width="100">Quantity(pcs)/Value</th>
										<?
										foreach ($year_month_count as $key => $val) 
										{
											?>
											<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
											<?
										}
										?>
										<th rowspan="2" width="100">Grand Total</th>
									</tr>
									<tr>
										<?
										foreach ($date_range as $key => $val) 
										{
											?>
											<th width="80"><? echo $val;?></th>
											<?
										}
										?>
									</tr>
								</thead>
							</table>
							<div style="width:<? echo $sub_dep_tbl_width+120;?>px;overflow-y:scroll;max-height:350px;" id="scroll_body_3">
								<table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
									<tbody>
										<?
										$k=1;
										$vr_tot_qty = 0;
										$vr_tot_val = 0;
										$vr_gr_tot_qty = 0;
										$vr_gr_tot_val = 0;
										$mon_gr_tot_array = array();
										foreach ($sub_dep_dtls_array as $ref_key => $ref_val) 
										{	
											$ref = 0;
											foreach ($ref_val as $buyer_key => $buyer_val) 
											{
												$byr = 0;
												foreach ($buyer_val as $item_key => $val) 
												{
													$item_arr = explode(',', $item_key);
													$items_str = '';

													foreach ($item_arr as $item_id) {
														$items_str .= $garments_item[$item_id] . ', ';
													}

													$items_str = rtrim($items_str, ' ,');

													if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
													?>
													<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" class="tr_even">
														<? if($ref==0){?>
														<td valign="top" width="100" rowspan="<? echo $rowspan_dtls[$ref_key]*2;?>"><? echo strtoupper($buyer_ref_arr[$buyer_key]);?></td>
														<? $ref++;} if($byr==0){?>
														<td valign="top" width="100" rowspan="<? echo $rowspan_buyer[$ref_key][$buyer_key]*2;?>"><? echo strtoupper($buyer_arr[$buyer_key]);?></td>
														<? $byr++;}?>
														<td width="100" rowspan="2"><? echo strtoupper($items_str); ?></td>
														<td width="100" align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
														<?                			
														$hr_mon_tot_qty = 0;
														foreach ($date_range as $key => $val2) 
														{
															$exfactoryQty3=0;
															if(str_replace("'","",$cbo_shipping_status)==2){
																foreach($sub_dep_qty_array[$buyer_key][$item_key][$val2]['PO_ID'] as $po_id){
																	$exfactoryQty3+=$exfactory_qty_arr[$po_id['id']];
																}
															}
															?>
															<td width="80" align="right"><? echo number_format($sub_dep_qty_array[$buyer_key][$item_key][$val2]['qty']-$exfactoryQty3,0);?></td>
															<?
															$hr_mon_tot_qty += $sub_dep_qty_array[$buyer_key][$item_key][$val2]['qty']-$exfactoryQty3;
															$vr_gr_tot_qty += $sub_dep_qty_array[$buyer_key][$item_key][$val2]['qty']-$exfactoryQty3;
															$mon_gr_tot_array[$val2]['qty'] += $sub_dep_qty_array[$buyer_key][$item_key][$val2]['qty']-$exfactoryQty3;
														}
														?>
														<td width="100" align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
													</tr>
													<tr class="tr_odd">
													<td width="100" align="right">Value<? //echo number_format($val['val'],2);?></td>	
													<?
													$hr_mon_tot_val = 0;
													foreach ($date_range as $key2 => $val3) 
													{
														$exfactoryVal3=0;
														$ave_rate3=1;
														if(str_replace("'","",$cbo_shipping_status)==2){
															foreach($sub_dep_qty_array[$buyer_key][$item_key][$val3]['PO_ID'] as $po_id){
																$ave_rate3=$po_id['val']/$po_id['qty'];
															$exfactoryVal3+=$exfactory_qty_arr[$po_id['id']]*$ave_rate3;
															}
														}
														?>
														<td width="80" align="right">$ <? echo number_format($sub_dep_qty_array[$buyer_key][$item_key][$val3]['val']-$exfactoryVal3,2);?></td>
														<?
														$hr_mon_tot_val += $sub_dep_qty_array[$buyer_key][$item_key][$val3]['val']-$exfactoryVal3;
														$vr_gr_tot_val += $sub_dep_qty_array[$buyer_key][$item_key][$val3]['val']-$exfactoryVal3;
														$mon_gr_tot_array[$val3]['val'] += $sub_dep_qty_array[$buyer_key][$item_key][$val3]['val']-$exfactoryVal3;
													}
													?>
													<td width="100" align="right">$ <? echo number_format($hr_mon_tot_val,2);?></td>
												</tr>
													<?
													$i++;
													$k++;

													$vr_tot_qty += $val['qty'];
													$vr_tot_val += $val['val'];
												}
											}
										}
										?>
									</tbody>
								</table>
							</div>
							<table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
								<tfoot>
									<tr>
										<th width="100" rowspan="2"></th>
										<th width="100" rowspan="2"></th>
										<th width="100" rowspan="2">Grand Total</th>
										<th width="100"><? //echo number_format($vr_tot_qty,0);?></th>
										<?
										foreach ($date_range as $key2 => $val4) 
										{
											?>
											<th width="80" align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
											<?
										}
										?>
										<th><? echo number_format($vr_gr_tot_qty,0);?></th>
									</tr>
									<tr>
										<th><? //echo number_format($vr_tot_val,2);?></th>
										<?
										foreach ($date_range as $key2 => $val5) 
										{
											?>
											<th width="80" align="right">$ <? echo number_format($mon_gr_tot_array[$val5]['val'],2);?></th>
											<?
										}
										?>
										<th width="100">$ <? echo number_format($vr_gr_tot_val,2);?></th>
									</tr>
								</tfoot>
							</table>            
						</div>
					</div>
				</fieldset>
				<?
			}
			else // report generate without value
			{

				?>
					<!-- ===================================================================================================/
					/ 										BUYER WISE SUMMARY PART										  	/
					/==================================================================================================== -->
					<div id="1st_part">
						<div style="width:<? echo $sub_dep_tbl_width+20;?>px;">  
							<table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
								<caption style="text-align:left;"><h2>Buyer wise Summary</h2></caption>
								<thead>
									<tr>
										<th rowspan="2" width="100">Buyer Ref.</th>
										<th rowspan="2" width="100">Buyer</th>
										<th rowspan="2" width="100">Quantity(pcs)</th>
										<?
										foreach ($year_month_count as $key => $val) 
										{
											?>
											<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
											<?
										}
										?>
										<th rowspan="2" width="100">Grand Total</th>
									</tr>
									<tr>
										<?
										foreach ($date_range as $key => $val) 
										{
											?>
											<th width="80"><? echo $val;?></th>
											<?
										}
										?>
									</tr>
								</thead>
							</table>
							<div style="width:<? echo $sub_dep_tbl_width+20;?>px;overflow-y:scroll;max-height:350px;" id="scroll_body_1">
								<table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
									<tbody>
										<?
										$i=1;
										$vr_tot_qty = 0;
										$vr_tot_val = 0;
										$vr_gr_tot_qty = 0;
										$vr_gr_tot_val = 0;
										$mon_gr_tot_array = array();
										foreach ($sub_dep_summary_array as $ref_key => $ref_val) 
										{	
											$ref = 0;
											foreach ($ref_val as $buyer_key => $val) 
											{
												
												if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
												?>
												<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" style="font-size:12px">
													<? if($ref==0){?>
													<td valign="top" width="100" rowspan="<? echo $rowspan[$ref_key];?>" ><? echo strtoupper($buyer_ref_arr[$buyer_key]);?></td>
													<? $ref++;}?>
													<td width="100"><? echo strtoupper($buyer_arr[$buyer_key]);?></td>
													<td width="100" align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
													<?                			
													$hr_mon_tot_qty = 0;
													foreach ($date_range as $key => $val2) 
													{
														
														$exfactoryQty=0;
														if(str_replace("'","",$cbo_shipping_status)==2){
															foreach($sub_dep_summary_qty_array[$buyer_key][$val2]['PO_ID'] as $po_id){
															$exfactoryQty+=$exfactory_qty_arr[$po_id['id']];
															}
														}

														?>
														<td width="80" align="right"><? echo number_format(($sub_dep_summary_qty_array[$buyer_key][$val2]['qty']-$exfactoryQty),0);?></td>
														<?
														$hr_mon_tot_qty += $sub_dep_summary_qty_array[$buyer_key][$val2]['qty']-$exfactoryQty;
														$vr_gr_tot_qty += $sub_dep_summary_qty_array[$buyer_key][$val2]['qty']-$exfactoryQty;
														$mon_gr_tot_array[$val2]['qty'] += $sub_dep_summary_qty_array[$buyer_key][$val2]['qty']-$exfactoryQty;
													}
													?>
													<td width="100" align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
												</tr>
												<?
												$i++;

												$vr_tot_qty += $val['qty'];
												$vr_tot_val += $val['val'];
											}
										}
										?>
									</tbody>	                
								</table>
							</div>
							<table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
								<tfoot>
									<tr>
										<th width="100"></th>
										<th width="100">Grand Total</th>
										<th width="100"><? //echo number_format($vr_tot_qty,0);?></th>
										<?
										foreach ($date_range as $key2 => $val4) 
										{
											?>
											<th width="80" align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
											<?
										}
										?>
										<th><? echo number_format($vr_gr_tot_qty,0);?></th>
									</tr>
								</tfoot>
							</table>
						</div>        
					</div>
					<!-- ===================================================================================================/
					/ 											ITEM WISE SUMMARY PART									  	/
					/==================================================================================================== -->
					<div id="2nd_part">
						<div style="margin-top: 20px;width:<? echo $sub_dep_tbl_width+20;?>px;" id="">            
							
							<table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
								<caption style="text-align:left;"><h2>Item wise Summary</h2></caption>
								<thead>
									<tr>
										<th rowspan="2" width="100">Item</th>
										<th rowspan="2" width="100">Quantity(pcs)</th>
										<?
										foreach ($year_month_count as $key => $val) 
										{
											?>
											<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
											<?
										}
										?>
										<th rowspan="2" width="100">Grand Total</th>
									</tr>
									<tr>
										<?
										foreach ($date_range as $key => $val) 
										{
											?>
											<th width="80"><? echo $val;?></th>
											<?
										}
										?>
									</tr>
								</thead>
							</table>
							<div style="width:<? echo $sub_dep_tbl_width+20;?>px;overflow-y:scroll;max-height:350px;" id="scroll_body_2">
								<table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
									<tbody>
										<?
										$k=1;
										$vr_tot_qty = 0;
										$vr_tot_val = 0;
										$vr_gr_tot_qty = 0;
										$vr_gr_tot_val = 0;
										$mon_gr_tot_array = array();
										foreach ($item_summary_array as $item_key => $val) 
										{
											if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
											?>
											<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" style="font-size:12px">
												<td width="100"><? echo $garments_item[$item_key];?></td>
												<td width="100" align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
												<?                			
												$hr_mon_tot_qty = 0;
												foreach ($date_range as $key => $val2) 
												{
													$exfactoryQty2=0;
													if(str_replace("'","",$cbo_shipping_status)==2){
														foreach($item_summary_qty_array[$item_key][$val2]['PO_ID'] as $po_id){
															$exfactoryQty2+=$exfactory_qty_arr[$po_id['id']];
														}
													}
													?>
													<td width="80" align="right"><? echo number_format($item_summary_qty_array[$item_key][$val2]['qty']-$exfactoryQty2,0);?></td>
													<?
													$hr_mon_tot_qty += $item_summary_qty_array[$item_key][$val2]['qty']-$exfactoryQty2;
													$vr_gr_tot_qty += $item_summary_qty_array[$item_key][$val2]['qty']-$exfactoryQty2;
													$mon_gr_tot_array[$val2]['qty'] += $item_summary_qty_array[$item_key][$val2]['qty']-$exfactoryQty2;
												}
												?>
												<td width="100" align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
											</tr>
											<?
											$i++;
											$k++;

											$vr_tot_qty += $val['qty'];
											$vr_tot_val += $val['val'];
										}
										?>
									</tbody>
								</table>
							</div>
							<table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
								<tfoot>
									<tr>
										<th width="100">Grand Total</th>
										<th width="100"><? //echo number_format($vr_tot_qty,0);?></th>
										<?
										foreach ($date_range as $key2 => $val4) 
										{
											?>
											<th width="80" align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
											<?
										}
										?>
										<th width="100"><? echo number_format($vr_gr_tot_qty,0);?></th>
									</tr>
								</tfoot>
							</table>            
						</div>
					</div>

					<!-- ===================================================================================================/
					/ 												DETAILS PART									  		/
					/==================================================================================================== -->
					<div id="3rd_part">
						<div style="margin-top: 20px;width:<? echo $sub_dep_tbl_width+120;?>px;" id="">            
							
							<table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
								<caption style="text-align:left;"><h2>Details</h2></caption>
								<thead>
									<tr>
										<th rowspan="2" width="100">Buyer Ref.</th>
										<th rowspan="2" width="100">Buyer</th>
										<th rowspan="2" width="100">Item</th>
										<th rowspan="2" width="100">Quantity(pcs)</th>
										<?
										foreach ($year_month_count as $key => $val) 
										{
											?>
											<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
											<?
										}
										?>
										<th rowspan="2" width="100">Grand Total</th>
									</tr>
									<tr>
										<?
										foreach ($date_range as $key => $val) 
										{
											?>
											<th width="80"><? echo $val;?></th>
											<?
										}
										?>
									</tr>
								</thead>
							</table>
							<div style="width:<? echo $sub_dep_tbl_width+120;?>px;overflow-y:scroll;max-height:350px;" id="scroll_body_3">
								<table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
									<tbody>
										<?
										$k=1;
										$vr_tot_qty = 0;
										$vr_tot_val = 0;
										$vr_gr_tot_qty = 0;
										$vr_gr_tot_val = 0;
										$mon_gr_tot_array = array();
										foreach ($sub_dep_dtls_array as $ref_key => $ref_val) 
										{
											$ref = 0;
											foreach ($ref_val as $buyer_key => $buyer_val) 
											{
												$byr = 0;
												foreach ($buyer_val as $item_key => $val) 
												{
													if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
													?>
													<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" class="tr_even">
														<? if($ref==0){?>
														<td valing="top" width="100" rowspan="<? echo $rowspan_dtls[$ref_key];?>"><? echo strtoupper($buyer_ref_arr[$buyer_key]);?></td>
														<? $ref++;} if($byr==0){?>
														<td valing="top" width="100" rowspan="<? echo $rowspan_buyer[$ref_key][$buyer_key];?>"><? echo strtoupper($buyer_arr[$buyer_key]);?></td>
														<? $byr++;}?>
														<td width="100"><? echo $garments_item[$item_key];?></td>
														<td width="100" align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
														<?                			
														$hr_mon_tot_qty = 0;
														foreach ($date_range as $key => $val2) 
														{
															$exfactoryQty3=0;
															if(str_replace("'","",$cbo_shipping_status)==2){
																foreach($sub_dep_qty_array[$buyer_key][$item_key][$val2]['PO_ID'] as $po_id){
																	$exfactoryQty3+=$exfactory_qty_arr[$po_id['id']];
																}
															}
															?>
															<td width="80" align="right"><? echo number_format($sub_dep_qty_array[$buyer_key][$item_key][$val2]['qty']-$exfactoryQty3,0);?></td>
															<?
															$hr_mon_tot_qty += $sub_dep_qty_array[$buyer_key][$item_key][$val2]['qty']-$exfactoryQty3;
															$vr_gr_tot_qty += $sub_dep_qty_array[$buyer_key][$item_key][$val2]['qty']-$exfactoryQty3;
															$mon_gr_tot_array[$val2]['qty'] += $sub_dep_qty_array[$buyer_key][$item_key][$val2]['qty']-$exfactoryQty3;
														}
														?>
														<td width="100" align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
													</tr>
													<?
													$i++;
													$k++;

													$vr_tot_qty += $val['qty'];
													$vr_tot_val += $val['val'];
												}
											}
										}
										?>
									</tbody>
								</table>
							</div>
							<table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
								<tfoot>
									<tr>
										<th width="100"></th>
										<th width="100"></th>
										<th width="100">Grand Total</th>
										<th width="100"><? //echo number_format($vr_tot_qty,0);?></th>
										<?
										foreach ($date_range as $key2 => $val4) 
										{
											?>
											<th width="80" align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
											<?
										}
										?>
										<th width="100"><? echo number_format($vr_gr_tot_qty,0);?></th>
									</tr>
								</tfoot>
							</table>            
						</div>
					</div>
				</fieldset>
				<?
			}
		}
	}
	else{		
		// =============================================== MAIN QUERY =========================================
		$sql = "SELECT b.id as PO_ID,a.BUYER_NAME,a.COMPANY_NAME, a.PRO_SUB_DEP, a.TOTAL_SET_QNTY, TO_CHAR (b.pub_shipment_date, 'MON-YYYY') AS MONTH_YEAR, sum(B.PO_QUANTITY) as qty, sum(B.PO_TOTAL_PRICE) as val, A.GMTS_ITEM_ID
    	FROM wo_po_details_master a, wo_po_break_down b
   		WHERE a.id = b.job_id $sql_cond AND a.status_active = 1 AND a.is_deleted = 0 AND b.status_active IN (1) AND b.is_deleted = 0
		GROUP BY a.buyer_name, a.pro_sub_dep, b.id, b.pub_shipment_date, a.total_set_qnty, A.GMTS_ITEM_ID,a.COMPANY_NAME";

	 	//echo $sql;
		$sql_res = sql_select($sql);
		if (count($sql_res) < 1)
		{
			echo '<div style="text-align: center;color: red;font-weight: bold;font-size: 20px;">Data not found.</div>';
			disconnect($con);
			die();
		}

		$sub_dep_dtls_array = array();
		$sub_dep_qty_array = array();
		$sub_dep_summary_array = array();
		$sub_dep_summary_qty_array = array();
		$item_summary_array = array();
		$item_summary_qty_array = array();
		$count = 1;
		foreach ($sql_res as $row)
		{
			// for summary
			$sub_dep_summary_array[$company_arr[$row['COMPANY_NAME']]][$row['BUYER_NAME']]['val'] += $row['VAL'];
			$sub_dep_summary_array[$company_arr[$row['COMPANY_NAME']]][$row['BUYER_NAME']]['qty'] += $row['QTY'] * $row['TOTAL_SET_QNTY'];

			$sub_dep_summary_qty_array[$company_arr[$row['COMPANY_NAME']]][$row['BUYER_NAME']][$row['MONTH_YEAR']]['val'] += $row['VAL'];
			$sub_dep_summary_qty_array[$company_arr[$row['COMPANY_NAME']]][$row['BUYER_NAME']][$row['MONTH_YEAR']]['qty'] += $row['QTY'] * $row['TOTAL_SET_QNTY'];

			// for item wise summary
			$item_summary_array[$company_arr[$row['COMPANY_NAME']]][$row[csf('gmts_item_id')]]['val'] += $row['VAL'];
			$item_summary_array[$company_arr[$row['COMPANY_NAME']]][$row[csf('gmts_item_id')]]['qty'] += $row['QTY'] * $row['TOTAL_SET_QNTY'];

			$item_summary_qty_array[$company_arr[$row['COMPANY_NAME']]][$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['val'] += $row['VAL'];
			$item_summary_qty_array[$company_arr[$row['COMPANY_NAME']]][$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['qty'] += $row['QTY'] * $row['TOTAL_SET_QNTY'];
			$item_summary_qty_array[$company_arr[$row['COMPANY_NAME']]][$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['id']=$row['PO_ID'];
			$item_summary_qty_array[$company_arr[$row['COMPANY_NAME']]][$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['val']+=$row['VAL'];
			$item_summary_qty_array[$company_arr[$row['COMPANY_NAME']]][$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['qty']+=$row['QTY'] * $row['TOTAL_SET_QNTY'];

			// for details
			$sub_dep_qty_array[$company_arr[$row['COMPANY_NAME']]][$row['BUYER_NAME']][$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['id']=$row['PO_ID'];
			$sub_dep_qty_array[$company_arr[$row['COMPANY_NAME']]][$row['BUYER_NAME']][$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['val']+=$row['VAL'];
			$sub_dep_qty_array[$company_arr[$row['COMPANY_NAME']]][$row['BUYER_NAME']][$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['qty']+=$row['QTY'] * $row['TOTAL_SET_QNTY'];
			$sub_dep_qty_array[$company_arr[$row['COMPANY_NAME']]][$row['BUYER_NAME']][$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['val'] += $row['VAL'];
			$sub_dep_qty_array[$company_arr[$row['COMPANY_NAME']]][$row['BUYER_NAME']][$row[csf('gmts_item_id')]][$row['MONTH_YEAR']]['qty'] += $row['QTY'] * $row['TOTAL_SET_QNTY'];

			$sub_dep_dtls_array[$company_arr[$row['COMPANY_NAME']]][$row['BUYER_NAME']][$row[csf('gmts_item_id')]]['val'] += $row['VAL'];
			$sub_dep_dtls_array[$company_arr[$row['COMPANY_NAME']]][$row['BUYER_NAME']][$row[csf('gmts_item_id')]]['qty'] += $row['QTY'] * $row['TOTAL_SET_QNTY'];
		
			$sub_dep_summary_qty_array[$company_arr[$row['COMPANY_NAME']]][$row['BUYER_NAME']][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['id']= $row['PO_ID'];
			$sub_dep_summary_qty_array[$company_arr[$row['COMPANY_NAME']]][$row['BUYER_NAME']][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['val']+= $row['VAL'];
			$sub_dep_summary_qty_array[$company_arr[$row['COMPANY_NAME']]][$row['BUYER_NAME']][$row['MONTH_YEAR']]['PO_ID'][$row['PO_ID']]['qty']+=  $row['QTY'] * $row['TOTAL_SET_QNTY'];
			$all_po_array[$row['PO_ID']]= $row['PO_ID'];
		
		
		}

		$rowspan = array();
		foreach ($sub_dep_summary_array as $ref_key => $ref_value) 
		{
			foreach ($ref_value as $buyer_key => $buyer_value) 
			{
				$rowspan[$ref_key]++;
			}
		}

		$rowspan_dtls = array();
		$rowspan_buyer = array();
		foreach ($sub_dep_dtls_array as $ref_key => $ref_value) 
		{
			foreach ($ref_value as $buyer_key => $buyer_value) 
			{
				foreach ($buyer_value as $item_key => $item_value) 
				{
					$rowspan_dtls[$ref_key]++;
					$rowspan_buyer[$ref_key][$buyer_key]++;
				}
			}
		}

		$rowspan_item = array();
		$rowspan_item_val = array();
		foreach ($item_summary_array as $comp_key => $item_data){
			foreach ($item_data as $item_key => $val){
				$rowspan_item[$comp_key]++;
			
			}}
		// echo "<pre>";print_r($rowspan_buyer);die();

		
		$exfactoryQry="select PO_BREAK_DOWN_ID,EX_FACTORY_QNTY from PRO_EX_FACTORY_MST where  IS_DELETED=0 and STATUS_ACTIVE=1 ".where_con_using_array($all_po_array,0,'PO_BREAK_DOWN_ID')."";
		$exfactoryQry_result = sql_select($exfactoryQry);
		$exfactory_qty_arr=array();
		foreach($exfactoryQry_result as $row){
			$exfactory_qty_arr[$row[PO_BREAK_DOWN_ID]]+=$row[EX_FACTORY_QNTY];
		}

		$sub_dep_tbl_width = $month_year_tot_width + 400;
		ob_start();
		?>
		    <fieldset style="width:100%;">
		    	<style type="text/css">
		    		.tr_odd td{font-size: 12px !important;}
		    		.tr_even td{font-size: 14px !important;}
		    	</style>
		        <table width="<? echo $sub_dep_tbl_width+120;?>;" cellspacing="0"  align="center">
		            <tr>
		                <td colspan="<? echo $colspan;?>" align="center" class="form_caption">
		                    <strong style="font-size:16px;">Company Name:<? echo  $company_library[$company_name] ;?></strong>
		                </td>
		            </tr>
		            <tr class="form_caption">
		                <td colspan="<? echo $colspan;?>" align="center" class="form_caption"> <strong style="font-size:15px;">Month Wise Quantity (Pcs) and Value</strong></td>
		            </tr>
		            <tr align="center">
		                <td colspan="<? echo $colspan;?>" align="center" class="form_caption"> <strong style="font-size:15px;">Date Type : <? echo ($date_type==1) ? "Shipment Date (RFI)": "Original Ship Date";?></strong></td>
		            </tr>
			            <tr align="center">
			                <td colspan="<? echo $colspan;?>" align="center" class="form_caption"> <strong style="font-size:15px;">Runtime : <? echo date("j F, Y h:i:s a");?></strong></td>
			            </tr>
		       	</table>
		<?
		if($show_value) // report generate with value
		{
			?>
		        <!-- ===================================================================================================/
		        / 											BUYER SUMMARY PART 										  	/
		        /==================================================================================================== -->
		        <div id="1st_part">
			        <div style="width:<? echo $sub_dep_tbl_width+20;?>px;">  
			            <table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
			            	<caption style="text-align:left;"><h2>Buyer wise Summary</h2></caption>
			                <thead>
			                	<tr>
				                    <th rowspan="2" width="100">Company</th>
				                    <th rowspan="2" width="100">Buyer</th>
				                    <th rowspan="2" width="100">Quantity(pcs)/Value</th>
				                    <?
				                    foreach ($year_month_count as $key => $val) 
				                    {
				                    	?>
				                    	<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
				                    	<?
				                    }
				                    ?>
				                    <th rowspan="2" width="100">Grand Total</th>
				                </tr>
				                <tr>
				                	<?
				                	foreach ($date_range as $key => $val) 
				                	{
				                		?>
				                		<th width="80"><? echo $val;?></th>
				                		<?
				                	}
				                	?>
				                </tr>
			                </thead>
			            </table>
			            <div style="width:<? echo $sub_dep_tbl_width+20;?>px;overflow-y:scroll;max-height:350px;" id="scroll_body_1">
				            <table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
				                <tbody>
				                	<?
				                	$i=1;
				                	$vr_tot_qty = 0;
				                	$vr_tot_val = 0;
				                	$vr_gr_tot_qty = 0;
				                	$vr_gr_tot_val = 0;
				                	$mon_gr_tot_array = array();
				                	foreach ($sub_dep_summary_array as $comp_key => $ref_val) 
				                	{
				                		$ref = 0;
				                		foreach ($ref_val as $buyer_key => $val) 
				                		{
					                		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					                		?>
					                		<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" class="tr_even">
					                			<? if($ref==0){?>
					                			<td valign="top" width="100"  rowspan="<? echo $rowspan[$comp_key]*2;?>"><? echo strtoupper($comp_key);?></td>
					                			<? $ref++;}?>
					                			<td valign="top" width="100" rowspan="2"><? echo strtoupper($buyer_arr[$buyer_key]);?></td>
					                			<td width="100" align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
					                			<?                			
					                			$hr_mon_tot_qty = 0;
							                	foreach ($date_range as $key => $val2) 
							                	{
							                		
													$exfactoryQty=0;
													if(str_replace("'","",$cbo_shipping_status)==2){
														foreach($sub_dep_summary_qty_array[$comp_key][$buyer_key][$val2]['PO_ID'] as $po_id){
															$exfactoryQty+=$exfactory_qty_arr[$po_id['id']];
														}
													}
													
													?>
							                		<td width="80" align="right"><? echo number_format(($sub_dep_summary_qty_array[$comp_key][$buyer_key][$val2]['qty']-$exfactoryQty),0);?></td>
							                		<?
							                		$hr_mon_tot_qty += $sub_dep_summary_qty_array[$comp_key][$buyer_key][$val2]['qty']-$exfactoryQty;
							                		$vr_gr_tot_qty += $sub_dep_summary_qty_array[$comp_key][$buyer_key][$val2]['qty']-$exfactoryQty;
							                		$mon_gr_tot_array[$val2]['qty'] += $sub_dep_summary_qty_array[$comp_key][$buyer_key][$val2]['qty']-$exfactoryQty;
							                	}
							                	?>
							                	<td width="100" align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
					                		</tr>
					                		<tr class="tr_odd">
					                		<td width="100" align="right">Value<? //echo number_format($val['val'],2);?></td>	
						                	<?
					                		$hr_mon_tot_val = 0;
						                	foreach ($date_range as $key2 => $val3) 
						                	{
												$exfactoryVal=0;
												$ave_rate=0;
												if(str_replace("'","",$cbo_shipping_status)==2){
													foreach($sub_dep_summary_qty_array[$comp_key][$buyer_key][$val3]['PO_ID'] as $po_id){
														$ave_rate=$po_id['val']/$po_id['qty'];
														$exfactoryVal+=$exfactory_qty_arr[$po_id['id']]*$ave_rate;
													}
												}
						                		?>
						                		<td width="80" align="right">$ <? echo number_format($sub_dep_summary_qty_array[$comp_key][$buyer_key][$val3]['val']-$exfactoryVal,2);?></td>
						                		<?
							                	$hr_mon_tot_val += $sub_dep_summary_qty_array[$comp_key][$buyer_key][$val3]['val']-$exfactoryVal;
							                	$vr_gr_tot_val += $sub_dep_summary_qty_array[$comp_key][$buyer_key][$val3]['val']-$exfactoryVal;
							                	$mon_gr_tot_array[$val3]['val'] += $sub_dep_summary_qty_array[$comp_key][$buyer_key][$val3]['val']-$exfactoryVal;
						                	}
						                	?>
						                	<td width="100" align="right">$ <? echo number_format($hr_mon_tot_val,2);?></td>
						                </tr>
					                		<?
					                		$i++;
											$exfactoryVal=0;
					                		$vr_tot_qty += $val['qty'];
					                		$vr_tot_val += $val['val'];
					                	}
				                	}
				                	?>
				                </tbody>	                
				            </table>
				        </div>
			            <table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
			                <tfoot>
			                	<tr>
			                		<th width="100" rowspan="2"></th>
			                		<th width="100" rowspan="2">Grand Total</th>
			                		<th width="100"><? //echo number_format($vr_tot_qty,0);?></th>
			                		<?
				                	foreach ($date_range as $key2 => $val4) 
				                	{
				                		?>
				                		<th width="80" align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
				                		<?
				                	}
				                	?>
				                	<th>s<? echo number_format($vr_gr_tot_qty,0);?></th>
			                	</tr>
			                	<tr>
			                		<th><? //echo number_format($vr_tot_val,2);?></th>
			                		<?
				                	foreach ($date_range as $key2 => $val5) 
				                	{
				                		?>
				                		<th width="80" align="right">$ <? echo number_format($mon_gr_tot_array[$val5]['val'],2);?></th>
				                		<?
				                	}
				                	?>
				                	<th>$ <? echo number_format($vr_gr_tot_val,2);?></th>
			                	</tr>
			                </tfoot>
			            </table>
			        </div>        
		        </div>
		        <!-- ===================================================================================================/
		        / 											ITEM WISE SUMMARY PART									  	/
		        /==================================================================================================== -->
		        <div id="2nd_part">
			        <div style="margin-top: 20px;width:<? echo $sub_dep_tbl_width+20;?>px;" id="">            
			            
			            <table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
			            	<caption style="text-align:left;"><h2>Item wise Summary</h2></caption>
			                <thead>
			                	<tr>
									<th rowspan="2" width="100">Company</th>
				                    <th rowspan="2" width="100">Item</th>
				                    <th rowspan="2" width="100">Quantity(pcs)/Value</th>
				                    <?
				                    foreach ($year_month_count as $key => $val) 
				                    {
				                    	?>
				                    	<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
				                    	<?
				                    }
				                    ?>
				                    <th rowspan="2" width="100">Grand Total</th>
				                </tr>
				                <tr>
				                	<?
				                	foreach ($date_range as $key => $val) 
				                	{
				                		?>
				                		<th width="80"><? echo $val;?></th>
				                		<?
				                	}
				                	?>
				                </tr>
			                </thead>
			            </table>
			            <div style="width:<? echo $sub_dep_tbl_width+20;?>px;overflow-y:scroll;max-height:350px;" id="scroll_body_2">
				            <table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
				                <tbody>
				                	<?
				                	$k=1;
				                	$vr_tot_qty = 0;
				                	$vr_tot_val = 0;
				                	$vr_gr_tot_qty = 0;
				                	$vr_gr_tot_val = 0;
				                	$mon_gr_tot_array = array();
				                	foreach ($item_summary_array as $comp_key => $item_data) 
				                	{
										$itemref=0;
										foreach ($item_data as $item_key => $val) 
				                	   	{
											$item_arr = explode(',', $item_key);
											$items_str = '';

											foreach ($item_arr as $item_id) {
												$items_str .= $garments_item[$item_id] . ', ';
											}

				                		$items_str = rtrim($items_str, ' ,');

				                		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				                		?>
				                		<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" class="tr_even">
											<? if($itemref==0){?>
											<td valign="top" width="100" title="<?=$rowspan_item[$comp_key];?>" rowspan="<?=$rowspan_item[$comp_key]*2;?>"><? echo strtoupper($comp_key); ?></td>
											<? $itemref++;}?>
										
				                			<td valign="top" width="100" rowspan="2"><? echo strtoupper($items_str); ?></td>
				                			<td width="100" align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
				                			<?                			
				                			$hr_mon_tot_qty = 0;
						                	foreach ($date_range as $key => $val2) 
						                	{
												$exfactoryQty2=0;
												if(str_replace("'","",$cbo_shipping_status)==2){
													foreach($item_summary_qty_array[$comp_key][$item_key][$val2]['PO_ID'] as $po_id){
														$exfactoryQty2+=$exfactory_qty_arr[$po_id['id']];
													}
												}
						                		?>
						                		<td width="80" align="right"><? echo number_format($item_summary_qty_array[$comp_key][$item_key][$val2]['qty']-$exfactoryQty2,0);?></td>
						                		<?
						                		$hr_mon_tot_qty += $item_summary_qty_array[$comp_key][$item_key][$val2]['qty']-$exfactoryQty2;
						                		$vr_gr_tot_qty += $item_summary_qty_array[$comp_key][$item_key][$val2]['qty']-$exfactoryQty2;
						                		$mon_gr_tot_array[$val2]['qty'] += $item_summary_qty_array[$comp_key][$item_key][$val2]['qty']-$exfactoryQty2;
						                	}
						                	?>
						                	<td width="100" align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
				                		</tr>
				                		<tr class="tr_odd">
									
				                		<td width="100" align="right">Value<? //echo number_format($val['val'],2);?></td>	
					                	<?
				                		$hr_mon_tot_val = 0;
					                	foreach ($date_range as $key2 => $val3) 
					                	{
											$exfactoryVal2=0;
											$ave_rate2=1;

											if(str_replace("'","",$cbo_shipping_status)==2){
												foreach($item_summary_qty_array[$comp_key][$item_key][$val3]['PO_ID'] as $po_id){
													$ave_rate2=$po_id['val']/$po_id['qty'];
													$exfactoryVal2+=$exfactory_qty_arr[$po_id['id']]*$ave_rate2;
												}
											}
					                		?>
					                		<td width="80" align="right">$ <? echo number_format($item_summary_qty_array[$comp_key][$item_key][$val3]['val']-$exfactoryVal2,2);?></td>
					                		<?
						                	$hr_mon_tot_val += $item_summary_qty_array[$comp_key][$item_key][$val3]['val']-$exfactoryVal2;
						                	$vr_gr_tot_val += $item_summary_qty_array[$comp_key][$item_key][$val3]['val']-$exfactoryVal2;
						                	$mon_gr_tot_array[$val3]['val'] += $item_summary_qty_array[$comp_key][$item_key][$val3]['val']-$exfactoryVal2;
					                	}
					                	?>
					                	<td width="100" align="right">$ <? echo number_format($hr_mon_tot_val,2);?></td>
					                </tr>
				                		<?
				                		$i++;
				                		$k++;

				                		$vr_tot_qty += $val['qty'];
				                		$vr_tot_val += $val['val'];
				                	}

								
								
										}
				                	?>
				                </tbody>
				            </table>
			            </div>
			            <table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
			                <tfoot>
			                	<tr>
			                		<th width="100" rowspan="2" colspan="2">Grand Total</th>
			                		<th width="100"><? //echo number_format($vr_tot_qty,0);?></th>
			                		<?
				                	foreach ($date_range as $key2 => $val4) 
				                	{
				                		?>
				                		<th width="80" align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
				                		<?
				                	}
				                	?>
				                	<th><? echo number_format($vr_gr_tot_qty,0);?></th>
			                	</tr>
			                	<tr>
			                		<th><? //echo number_format($vr_tot_val,2);?></th>
			                		<?
				                	foreach ($date_range as $key2 => $val5) 
				                	{
				                		?>
				                		<th width="80" align="right">$ <? echo number_format($mon_gr_tot_array[$val5]['val'],2);?></th>
				                		<?
				                	}
				                	?>
				                	<th width="100">$ <? echo number_format($vr_gr_tot_val,2);?></th>
			                	</tr>
			                </tfoot>
			            </table>            
			        </div>
			    </div>

		        <!-- ===================================================================================================/
		        / 												DETAILS PART									  		/
		        /==================================================================================================== -->
		        <div id="3rd_part">
			        <div style="margin-top: 20px;width:<? echo $sub_dep_tbl_width+120;?>px;" id="">            
			            
			            <table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
			            	<caption style="text-align:left;"><h2>Details</h2></caption>
			                <thead>
			                	<tr>
				                    <th rowspan="2" width="100">Company</th>
				                    <th rowspan="2" width="100">Buyer</th>
				                    <th rowspan="2" width="100">Item</th>
				                    <th rowspan="2" width="100">Quantity(pcs)/Value</th>
				                    <?
				                    foreach ($year_month_count as $key => $val) 
				                    {
				                    	?>
				                    	<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
				                    	<?
				                    }
				                    ?>
				                    <th rowspan="2" width="100">Grand Total</th>
				                </tr>
				                <tr>
				                	<?
				                	foreach ($date_range as $key => $val) 
				                	{
				                		?>
				                		<th width="80"><? echo $val;?></th>
				                		<?
				                	}
				                	?>
				                </tr>
			                </thead>
			            </table>
			            <div style="width:<? echo $sub_dep_tbl_width+120;?>px;overflow-y:scroll;max-height:350px;" id="scroll_body_3">
				            <table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
				                <tbody>
				                	<?
				                	$k=1;
				                	$vr_tot_qty = 0;
				                	$vr_tot_val = 0;
				                	$vr_gr_tot_qty = 0;
				                	$vr_gr_tot_val = 0;
				                	$mon_gr_tot_array = array();
				                	foreach ($sub_dep_dtls_array as $comp_key => $ref_val) 
				                	{	
				                		$ref = 0;
				                		foreach ($ref_val as $buyer_key => $buyer_val) 
				                		{
				                			$byr = 0;
					                		foreach ($buyer_val as $item_key => $val) 
					                		{
					                			$item_arr = explode(',', $item_key);
						                		$items_str = '';

						                		foreach ($item_arr as $item_id) {
						                			$items_str .= $garments_item[$item_id] . ', ';
						                		}

						                		$items_str = rtrim($items_str, ' ,');

						                		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						                		?>
						                		<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" class="tr_even">
						                			<? if($ref==0){?>
						                			<td valign="top" width="100" rowspan="<? echo $rowspan_dtls[$comp_key]*2;?>"><? echo strtoupper($comp_key);?></td>
						                			<? $ref++;} if($byr==0){?>
						                			<td valign="top" width="100" rowspan="<? echo $rowspan_buyer[$comp_key][$buyer_key]*2;?>"><? echo strtoupper($buyer_arr[$buyer_key]);?></td>
						                			<? $byr++;}?>
						                			<td width="100" rowspan="2"><? echo strtoupper($items_str); ?></td>
						                			<td width="100" align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
						                			<?                			
						                			$hr_mon_tot_qty = 0;
								                	foreach ($date_range as $key => $val2) 
								                	{
														$exfactoryQty3=0;
														if(str_replace("'","",$cbo_shipping_status)==2){
															foreach($sub_dep_qty_array[$comp_key][$buyer_key][$item_key][$val2]['PO_ID'] as $po_id){
																$exfactoryQty3+=$exfactory_qty_arr[$po_id['id']];
															}
														}
								                		?>
								                		<td width="80" align="right"><? echo number_format($sub_dep_qty_array[$comp_key][$buyer_key][$item_key][$val2]['qty']-$exfactoryQty3,0);?></td>
								                		<?
								                		$hr_mon_tot_qty += $sub_dep_qty_array[$comp_key][$buyer_key][$item_key][$val2]['qty']-$exfactoryQty3;
								                		$vr_gr_tot_qty += $sub_dep_qty_array[$comp_key][$buyer_key][$item_key][$val2]['qty']-$exfactoryQty3;
								                		$mon_gr_tot_array[$val2]['qty'] += $sub_dep_qty_array[$comp_key][$buyer_key][$item_key][$val2]['qty']-$exfactoryQty3;
								                	}
								                	?>
								                	<td width="100" align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
						                		</tr>
						                		<tr class="tr_odd">
						                		<td width="100" align="right">Value<? //echo number_format($val['val'],2);?></td>	
							                	<?
						                		$hr_mon_tot_val = 0;
							                	foreach ($date_range as $key2 => $val3) 
							                	{
													$exfactoryVal3=0;
													$ave_rate3=1;
											        if(str_replace("'","",$cbo_shipping_status)==2){
												        foreach($sub_dep_qty_array[$comp_key][$buyer_key][$item_key][$val3]['PO_ID'] as $po_id){
															$ave_rate3=$po_id['val']/$po_id['qty'];
													    $exfactoryVal3+=$exfactory_qty_arr[$po_id['id']]*$ave_rate3;
												        }
											        }
							                		?>
							                		<td width="80" align="right">$ <? echo number_format($sub_dep_qty_array[$comp_key][$buyer_key][$item_key][$val3]['val']-$exfactoryVal3,2);?></td>
							                		<?
								                	$hr_mon_tot_val += $sub_dep_qty_array[$comp_key][$buyer_key][$item_key][$val3]['val']-$exfactoryVal3;
								                	$vr_gr_tot_val += $sub_dep_qty_array[$comp_key][$buyer_key][$item_key][$val3]['val']-$exfactoryVal3;
								                	$mon_gr_tot_array[$val3]['val'] += $sub_dep_qty_array[$comp_key][$buyer_key][$item_key][$val3]['val']-$exfactoryVal3;
							                	}
							                	?>
							                	<td width="100" align="right">$ <? echo number_format($hr_mon_tot_val,2);?></td>
							                </tr>
						                		<?
						                		$i++;
						                		$k++;

						                		$vr_tot_qty += $val['qty'];
						                		$vr_tot_val += $val['val'];
						                	}
					                	}
				                	}
				                	?>
				                </tbody>
				            </table>
			            </div>
			            <table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
			                <tfoot>
			                	<tr>
			                		<th width="100" rowspan="2"></th>
			                		<th width="100" rowspan="2"></th>
			                		<th width="100" rowspan="2">Grand Total</th>
			                		<th width="100"><? //echo number_format($vr_tot_qty,0);?></th>
			                		<?
				                	foreach ($date_range as $key2 => $val4) 
				                	{
				                		?>
				                		<th width="80" align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
				                		<?
				                	}
				                	?>
				                	<th><? echo number_format($vr_gr_tot_qty,0);?></th>
			                	</tr>
			                	<tr>
			                		<th><? //echo number_format($vr_tot_val,2);?></th>
			                		<?
				                	foreach ($date_range as $key2 => $val5) 
				                	{
				                		?>
				                		<th width="80" align="right">$ <? echo number_format($mon_gr_tot_array[$val5]['val'],2);?></th>
				                		<?
				                	}
				                	?>
				                	<th width="100">$ <? echo number_format($vr_gr_tot_val,2);?></th>
			                	</tr>
			                </tfoot>
			            </table>            
			        </div>
			    </div>
		    </fieldset>
			<?
		}
		else // report generate without value
		{

			?>
		        <!-- ===================================================================================================/
		        / 										BUYER WISE SUMMARY PART										  	/
		        /==================================================================================================== -->
		        <div id="1st_part">
			        <div style="width:<? echo $sub_dep_tbl_width+20;?>px;">  
			            <table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
			            	<caption style="text-align:left;"><h2>Buyer wise Summary</h2></caption>
			                <thead>
			                	<tr>
				                    <th rowspan="2" width="100">Comapny</th>
				                    <th rowspan="2" width="100">Buyer</th>
				                    <th rowspan="2" width="100">Quantity(pcs)</th>
				                    <?
				                    foreach ($year_month_count as $key => $val) 
				                    {
				                    	?>
				                    	<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
				                    	<?
				                    }
				                    ?>
				                    <th rowspan="2" width="100">Grand Total</th>
				                </tr>
				                <tr>
				                	<?
				                	foreach ($date_range as $key => $val) 
				                	{
				                		?>
				                		<th width="80"><? echo $val;?></th>
				                		<?
				                	}
				                	?>
				                </tr>
			                </thead>
			            </table>
			            <div style="width:<? echo $sub_dep_tbl_width+20;?>px;overflow-y:scroll;max-height:350px;" id="scroll_body_1">
				            <table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
				                <tbody>
				                	<?
				                	$i=1;
				                	$vr_tot_qty = 0;
				                	$vr_tot_val = 0;
				                	$vr_gr_tot_qty = 0;
				                	$vr_gr_tot_val = 0;
				                	$mon_gr_tot_array = array();
				                	foreach ($sub_dep_summary_array as $comp_key => $ref_val) 
				                	{	
				                		$ref = 0;
				                		foreach ($ref_val as $buyer_key => $val) 
				                		{
											
					                		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					                		?>
					                		<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" style="font-size:12px">
					                			<? if($ref==0){?>
					                			<td valign="top" width="100" rowspan="<? echo $rowspan[$comp_key];?>" ><? echo strtoupper($comp_key);?></td>
					                			<? $ref++;}?>
					                			<td width="100"><? echo strtoupper($buyer_arr[$buyer_key]);?></td>
					                			<td width="100" align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
					                			<?                			
					                			$hr_mon_tot_qty = 0;
							                	foreach ($date_range as $key => $val2) 
							                	{
							                		
													$exfactoryQty=0;
											        if(str_replace("'","",$cbo_shipping_status)==2){
												        foreach($sub_dep_summary_qty_array[$comp_key][$buyer_key][$val2]['PO_ID'] as $po_id){
													    $exfactoryQty+=$exfactory_qty_arr[$po_id['id']];
												        }
											        }

													?>
							                		<td width="80" align="right"><? echo number_format(($sub_dep_summary_qty_array[$comp_key][$buyer_key][$val2]['qty']-$exfactoryQty),0);?></td>
							                		<?
							                		$hr_mon_tot_qty += $sub_dep_summary_qty_array[$comp_key][$buyer_key][$val2]['qty']-$exfactoryQty;
							                		$vr_gr_tot_qty += $sub_dep_summary_qty_array[$comp_key][$buyer_key][$val2]['qty']-$exfactoryQty;
							                		$mon_gr_tot_array[$val2]['qty'] += $sub_dep_summary_qty_array[$comp_key][$buyer_key][$val2]['qty']-$exfactoryQty;
							                	}
							                	?>
							                	<td width="100" align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
					                		</tr>
					                		<?
					                		$i++;

					                		$vr_tot_qty += $val['qty'];
					                		$vr_tot_val += $val['val'];
					                	}
				                	}
				                	?>
				                </tbody>	                
				            </table>
				        </div>
			            <table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
			                <tfoot>
			                	<tr>
			                		<th width="100"></th>
			                		<th width="100">Grand Total</th>
			                		<th width="100"><? //echo number_format($vr_tot_qty,0);?></th>
			                		<?
				                	foreach ($date_range as $key2 => $val4) 
				                	{
				                		?>
				                		<th width="80" align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
				                		<?
				                	}
				                	?>
				                	<th><? echo number_format($vr_gr_tot_qty,0);?></th>
			                	</tr>
			                </tfoot>
			            </table>
			        </div>        
		        </div>
		        <!-- ===================================================================================================/
		        / 											ITEM WISE SUMMARY PART									  	/
		        /==================================================================================================== -->
		        <div id="2nd_part">
			        <div style="margin-top: 20px;width:<? echo $sub_dep_tbl_width+20;?>px;" id="">            
			            
			            <table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
			            	<caption style="text-align:left;"><h2>Item wise Summary</h2></caption>
			                <thead>
			                	<tr>
									<th rowspan="2" width="100">Company</th>
				                    <th rowspan="2" width="100">Item</th>
				                    <th rowspan="2" width="100">Quantity(pcs)</th>
				                    <?
				                    foreach ($year_month_count as $key => $val) 
				                    {
				                    	?>
				                    	<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
				                    	<?
				                    }
				                    ?>
				                    <th rowspan="2" width="100">Grand Total</th>
				                </tr>
				                <tr>
				                	<?
				                	foreach ($date_range as $key => $val) 
				                	{
				                		?>
				                		<th width="80"><? echo $val;?></th>
				                		<?
				                	}
				                	?>
				                </tr>
			                </thead>
			            </table>
			            <div style="width:<? echo $sub_dep_tbl_width+20;?>px;overflow-y:scroll;max-height:350px;" id="scroll_body_2">
				            <table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
				                <tbody>
				                	<?
				                	$k=1;
				                	$vr_tot_qty = 0;
				                	$vr_tot_val = 0;
				                	$vr_gr_tot_qty = 0;
				                	$vr_gr_tot_val = 0;
				                	$mon_gr_tot_array = array();
				                	foreach ($item_summary_array as $comp_key => $item_data) 
				                	{
										$ref=0;
										foreach ($item_data as $item_key => $val) 
				                		{
				                		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				                		?>
				                		<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" style="font-size:12px">
										<?
											if($ref==0){?>
											<td width="100" rowspan="<?=$rowspan_item[$comp_key];?>"><? echo $comp_key;?></td>
											<?$ref++;}?>
				                			<td width="100"><? echo $garments_item[$item_key];?></td>
				                			<td width="100" align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
				                			<?                			
				                			$hr_mon_tot_qty = 0;
						                	foreach ($date_range as $key => $val2) 
						                	{
												$exfactoryQty2=0;
												if(str_replace("'","",$cbo_shipping_status)==2){
													foreach($item_summary_qty_array[$comp_key][$item_key][$val2]['PO_ID'] as $po_id){
														$exfactoryQty2+=$exfactory_qty_arr[$po_id['id']];
													}
												}
						                		?>
						                		<td width="80" align="right"><? echo number_format($item_summary_qty_array[$comp_key][$item_key][$val2]['qty']-$exfactoryQty2,0);?></td>
						                		<?
						                		$hr_mon_tot_qty += $item_summary_qty_array[$comp_key][$item_key][$val2]['qty']-$exfactoryQty2;
						                		$vr_gr_tot_qty += $item_summary_qty_array[$comp_key][$item_key][$val2]['qty']-$exfactoryQty2;
						                		$mon_gr_tot_array[$val2]['qty'] += $item_summary_qty_array[$comp_key][$item_key][$val2]['qty']-$exfactoryQty2;
						                	}
						                	?>
						                	<td width="100" align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
				                		</tr>
				                		<?
				                		$i++;
				                		$k++;

				                		$vr_tot_qty += $val['qty'];
				                		$vr_tot_val += $val['val'];
				                	}}
				                	?>
				                </tbody>
				            </table>
			            </div>
			            <table width="<? echo $sub_dep_tbl_width;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
			                <tfoot>
			                	<tr>
									<th width="100"><? //echo number_format($vr_tot_qty,0);?></th>
			                		<th width="100">Grand Total</th>
			                		<th width="100"><? //echo number_format($vr_tot_qty,0);?></th>
			                		<?
				                	foreach ($date_range as $key2 => $val4) 
				                	{
				                		?>
				                		<th width="80" align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
				                		<?
				                	}
				                	?>
				                	<th width="100"><? echo number_format($vr_gr_tot_qty,0);?></th>
			                	</tr>
			                </tfoot>
			            </table>            
			        </div>
			    </div>

		        <!-- ===================================================================================================/
		        / 												DETAILS PART									  		/
		        /==================================================================================================== -->
		        <div id="3rd_part">
			        <div style="margin-top: 20px;width:<? echo $sub_dep_tbl_width+120;?>px;" id="">            
			            
			            <table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
			            	<caption style="text-align:left;"><h2>Details</h2></caption>
			                <thead>
			                	<tr>
				                    <th rowspan="2" width="100">Company</th>
				                    <th rowspan="2" width="100">Buyer</th>
				                    <th rowspan="2" width="100">Item</th>
				                    <th rowspan="2" width="100">Quantity(pcs)</th>
				                    <?
				                    foreach ($year_month_count as $key => $val) 
				                    {
				                    	?>
				                    	<th width="<? echo $month_year_width[$key];?>" colspan="<? echo $val;?>" width="80"><? echo $key;?></th>
				                    	<?
				                    }
				                    ?>
				                    <th rowspan="2" width="100">Grand Total</th>
				                </tr>
				                <tr>
				                	<?
				                	foreach ($date_range as $key => $val) 
				                	{
				                		?>
				                		<th width="80"><? echo $val;?></th>
				                		<?
				                	}
				                	?>
				                </tr>
			                </thead>
			            </table>
			            <div style="width:<? echo $sub_dep_tbl_width+120;?>px;overflow-y:scroll;max-height:350px;" id="scroll_body_3">
				            <table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
				                <tbody>
				                	<?
				                	$k=1;
				                	$vr_tot_qty = 0;
				                	$vr_tot_val = 0;
				                	$vr_gr_tot_qty = 0;
				                	$vr_gr_tot_val = 0;
				                	$mon_gr_tot_array = array();
				                	foreach ($sub_dep_dtls_array as $comp_key => $ref_val) 
				                	{
				                		$ref = 0;
					                	foreach ($ref_val as $buyer_key => $buyer_val) 
					                	{
					                		$byr = 0;
					                		foreach ($buyer_val as $item_key => $val) 
					                		{
						                		if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						                		?>
						                		<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>" class="tr_even">
						                			<? if($ref==0){?>
						                			<td valing="top" width="100" rowspan="<? echo $rowspan_dtls[$comp_key];?>"><? echo strtoupper($comp_key);?></td>
						                			<? $ref++;} if($byr==0){?>
						                			<td valing="top" width="100" rowspan="<? echo $rowspan_buyer[$comp_key][$buyer_key];?>"><? echo strtoupper($buyer_arr[$buyer_key]);?></td>
						                			<? $byr++;}?>
						                			<td width="100"><? echo $garments_item[$item_key];?></td>
						                			<td width="100" align="right">Quantity(pcs)<? //echo number_format($val['qty'],0);?></td>
						                			<?                			
						                			$hr_mon_tot_qty = 0;
								                	foreach ($date_range as $key => $val2) 
								                	{
														$exfactoryQty3=0;
														if(str_replace("'","",$cbo_shipping_status)==2){
															foreach($sub_dep_qty_array[$comp_key][$buyer_key][$item_key][$val2]['PO_ID'] as $po_id){
																$exfactoryQty3+=$exfactory_qty_arr[$po_id['id']];
															}
														}
								                		?>
								                		<td width="80" align="right"><? echo number_format($sub_dep_qty_array[$comp_key][$buyer_key][$item_key][$val2]['qty']-$exfactoryQty3,0);?></td>
								                		<?
								                		$hr_mon_tot_qty += $sub_dep_qty_array[$comp_key][$buyer_key][$item_key][$val2]['qty']-$exfactoryQty3;
								                		$vr_gr_tot_qty += $sub_dep_qty_array[$comp_key][$buyer_key][$item_key][$val2]['qty']-$exfactoryQty3;
								                		$mon_gr_tot_array[$val2]['qty'] += $sub_dep_qty_array[$comp_key][$buyer_key][$item_key][$val2]['qty']-$exfactoryQty3;
								                	}
								                	?>
								                	<td width="100" align="right"><? echo number_format($hr_mon_tot_qty,0);?></td>
						                		</tr>
						                		<?
						                		$i++;
						                		$k++;

						                		$vr_tot_qty += $val['qty'];
						                		$vr_tot_val += $val['val'];
						                	}
					                	}
				                	}
				                	?>
				                </tbody>
				            </table>
			            </div>
			            <table width="<? echo $sub_dep_tbl_width+100;?>" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" id="table_header_1" align="left">
			                <tfoot>
			                	<tr>
			                		<th width="100"></th>
			                		<th width="100"></th>
			                		<th width="100">Grand Total</th>
			                		<th width="100"><? //echo number_format($vr_tot_qty,0);?></th>
			                		<?
				                	foreach ($date_range as $key2 => $val4) 
				                	{
				                		?>
				                		<th width="80" align="right"><? echo number_format($mon_gr_tot_array[$val4]['qty'],0);?></th>
				                		<?
				                	}
				                	?>
				                	<th width="100"><? echo number_format($vr_gr_tot_qty,0);?></th>
			                	</tr>
			                </tfoot>
			            </table>            
			        </div>
			    </div>
		    </fieldset>
			<?
		}
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
	echo "$total_data####$filename####$report_type";
	exit();
}

disconnect($con);
?>
