<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "")
header("location:login.php");

require_once('../../../includes/common.php');

$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

if ($action == "load_drop_down_location") {
    echo create_drop_down("cbo_location", 150, "select id,location_name from lib_location where status_active =1 and is_deleted=0 and company_id='$data' order by location_name", "id,location_name", 1, "-Select Location-", $selected, "", 0);
    exit();
}

if ($action == "load_drop_down_buyer") {
    echo create_drop_down("cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name", "id,buyer_name", 1, "-Select Buyer-", $selected, "");
    exit();
}

if ($action == "report_generate")
{
    $process = array(&$_POST);
    extract(check_magic_quote_gpc($process));

    //echo $datediff;
    $cbo_company = str_replace("'", "", $cbo_company_id);
    $cbo_location = str_replace("'", "", $cbo_location);
    $date_from = str_replace("'", "", $txt_date_from);
    $date_to = str_replace("'", "", $txt_date_to);
    $cbo_buyer = str_replace("'", "", $cbo_buyer_name);
    $cbo_type = str_replace("'", "", $cbo_type);
	$cbo_dealing_merchant = str_replace("'", "", $cbo_dealing_merchant);
    $sql_cond = "";

	$dealingMerchantArr = return_library_array("select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0 order by team_member_name", "id", "team_member_name");

	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name");
	$locationArr = return_library_array("select id,location_name from lib_location where status_active = 1 and is_deleted = 0", "id", "location_name");
	$buyer_res = sql_select("select id, buyer_name, delivery_buffer_days from lib_buyer  where status_active = 1 and is_deleted = 0");
	foreach($buyer_res as $b_row)
	{
		$buyerArr[$b_row[csf("id")]]["name"] = $b_row[csf("buyer_name")];
		$buyerArr[$b_row[csf("id")]]["buffer_time"] = $b_row[csf("delivery_buffer_days")];
	}

	if($report_type==3)
	{
		if ($date_from && $date_to) {
			if ($db_type == 0) {
				$sql_cond .= " and m.ex_factory_date between '" . change_date_format($date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($date_to, 'yyyy-mm-dd') . "'";
			} else {
				$sql_cond .= " and m.ex_factory_date between '" . date("j-M-Y", strtotime($date_from)) . "' and '" . date("j-M-Y", strtotime($date_to)) . "'";
			}
		}
		if ($cbo_company>0) {
			$sql_cond .= " and dm.delivery_company_id = $cbo_company";
		}
		if ($cbo_location>0) {
			$sql_cond .= " and dm.delivery_location_id = $cbo_location";
		}
		if ($cbo_buyer>0) {
			$sql_cond .= " and a.buyer_name = $cbo_buyer";
		}
		if ($db_type == 0) {
			$delayShortSelect = ", DATEDIFF(b.shipment_date, m.ex_factory_date) as delay_time ";
		}else{
			$delayShortSelect = ", (b.shipment_date - m.ex_factory_date) as delay_time ";
		}

		$sql_res=sql_select("select b.po_break_down_id as po_id,c.color_size_break_down_id, c.production_qnty as return_qnty, d.order_rate
                from pro_ex_factory_mst b, pro_ex_factory_dtls c,  wo_po_color_size_breakdown d
                where b.id = c.mst_id and c.color_size_break_down_id = d.id
                and b.entry_form = 85
                and b.status_active=1 and b.is_deleted=0
                and c.status_active = 1 and c.is_deleted = 0");
		$ex_return_qty_arr=array();
		foreach($sql_res as $row)
		{
                    $ex_return_value =  $row[csf('return_qnty')]*$row[csf('order_rate')];
                    $ex_return_qty_arr[$row[csf('po_id')]][$row[csf('color_size_break_down_id')]]['return_qty']=$row[csf('return_qnty')];
                    $ex_return_qty_arr[$row[csf('po_id')]][$row[csf('color_size_break_down_id')]]['return_value']=$ex_return_value;
                    $ex_return_qty_arr[$row[csf('po_id')]]['color_size_list'].=$row[csf('color_size_break_down_id')].",";
		}
       $sql_country_w="SELECT  m.id,dm.delivery_company_id,dm.delivery_location_id,m.ex_factory_date, m.ex_factory_qnty as ex_qnty, a.buyer_name,
        c.country_ship_date,m.po_break_down_id,d.production_qnty, c.order_rate*d.production_qnty as ex_value,c.id as color_size,dm.delivery_date
        from pro_ex_factory_delivery_mst dm,pro_ex_factory_mst m, wo_po_details_master a, wo_po_color_size_breakdown c,
        pro_ex_factory_dtls d
        where dm.id = m.delivery_mst_id
        and a.job_no = c.job_no_mst
        and m.id = d.mst_id $sql_cond
        and d.color_size_break_down_id = c.id
        and dm.delivery_company_id <> 0 and m.entry_form <> 85 and dm.delivery_company_id is not null
        and dm.status_active = 1 and dm.is_deleted = 0 and a.status_active = 1 and a.is_deleted = 0
        and m.status_active = 1 and m.is_deleted = 0
        order by m.id";

	    $country_w_res = sql_select($sql_country_w);
	    $data_buyer_wise = array();
	    $qntyChkArr = array();
	    foreach($country_w_res as  $row)
	    {
	        $buffer_time = 0;
	        if($buyerArr[$row[csf("buyer_name")]]["buffer_time"])
	        {
	            $buffer_time = "+".$buyerArr[$row[csf('buyer_name')]]['buffer_time']." days";
	        }

	        // $time_diff_buffer = strtotime($row[csf("country_ship_date")] . $buffer_time) - strtotime($row[csf("ex_factory_date")]);
			$time_diff_buffer = strtotime($row[csf("country_ship_date")] . $buffer_time) - strtotime($row[csf("delivery_date")]);
	        // $time_diff = strtotime($row[csf("country_ship_date")]) - strtotime($row[csf("ex_factory_date")]);
			$time_diff = strtotime($row[csf("country_ship_date")]) - strtotime($row[csf("delivery_date")]);

				// echo $time_diff."=>".$time_diff_buffer."<br>";
	        $data_buyer_wise[$row[csf("buyer_name")]]["ex_qnty"] += $row[csf("production_qnty")];
	        if($time_diff > 0)
	        {
	            $data_buyer_wise[$row[csf("buyer_name")]]["early"] += $row[csf("production_qnty")];
	        }
	        else if($time_diff_buffer < 0 )
	        {
	            $data_buyer_wise[$row[csf("buyer_name")]]["delay"] += $row[csf("production_qnty")];
	        }
	        else if($time_diff <= 0 && $time_diff_buffer >= 0)
	        {
	            $data_buyer_wise[$row[csf("buyer_name")]]["ontime"] += $row[csf("production_qnty")];
	        }
	    }
	    foreach($country_w_res as $row)
	    {
	        $result_array[$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]][$row[csf("buyer_name")]]['ex_qnty'] += $row[csf("production_qnty")];
	        $result_array[$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]][$row[csf("buyer_name")]]['ex_value'] += $row[csf("ex_value")];
	        $result_array[$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]][$row[csf("buyer_name")]]['po_id'] .= $row[csf("po_break_down_id")].",";
	        $result_array[$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]][$row[csf("buyer_name")]]['color_size'] .= $row[csf("color_size")].",";
	    }
    	$sql = "SELECT b.id,dm.delivery_company_id,dm.delivery_location_id, m.ex_factory_qnty as ex_qnty, a.buyer_name,(b.unit_price/a.total_set_qnty) as unit_price,(b.po_quantity*a.total_set_qnty) as po_quantity, ((b.unit_price/a.total_set_qnty)*(b.po_quantity*a.total_set_qnty)) as  po_value $delayShortSelect from pro_ex_factory_delivery_mst dm,pro_ex_factory_mst m, wo_po_details_master a, wo_po_break_down b where dm.id = m.delivery_mst_id and a.job_no = b.job_no_mst and b.id= m.po_break_down_id and dm.status_active = 1 and dm.is_deleted = 0 and m.status_active = 1 and m.is_deleted = 0 and dm.delivery_company_id <> 0 and m.entry_form <> 85 and dm.delivery_company_id is not null $sql_cond order by dm.delivery_company_id , dm.delivery_location_id, a.buyer_name asc ";
		$sql_res = sql_select($sql);
		$summar_arr = array();
		foreach($sql_res as $row)
		{
			$summar_arr[$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]][$row[csf("buyer_name")]]['ex_qnty'] += $row[csf("ex_qnty")];
			$summar_arr[$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]][$row[csf("buyer_name")]]['ex_value'] += $row[csf("po_value")];
			$summar_arr[$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]][$row[csf("buyer_name")]]['po_id'] .= $row[csf("po_break_down_id")].",";
			$summar_arr[$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]][$row[csf("buyer_name")]]['color_size'] .= $row[csf("color_size")].",";
		}

		// echo "<pre>";
		// print_r($data_buyer_wise);
		ob_start();
		?>
		<div style='font-family: "Arial Narrow", Arial, sans-serif;'>
			<table style="width:1306px; float: left;border:none;" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_header_1" >
				<thead>
					<tr style="border:none;">
						<td colspan="9" align="center" style="border:none;font-size:16px; font-weight:bold" ><? echo $companyArr[$cbo_company];?> </td>
					</tr>
					<tr style="border:none;">
						<td colspan="9" align="center" style="font-weight: bold; border: none;">
							<? echo strtoupper($report_title); ?>
							<?
							if ($date_from != "" && $date_to != "") {
								echo "<br/>From: ".change_date_format($date_from)."  To: ".change_date_format($date_to);
							}
							?>
						</td>
					</tr>
				</thead>
			</table>
			<fieldset style="width:550px;float:left;margin-bottom: 5px; margin-right: 5px;">
				<table style="width:550px" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" >
					<thead>
						<tr>
							<td colspan="5" style="font-size:15px; font-weight:bold">Shipment Evaluation</td>
						</tr>
						<tr>
							<th width="150" rowspan="2">Buyer</th>
							<th width="100" rowspan="2">Qty(pcs)</th>
							<th colspan="3">Status</th>
						</tr>
						<tr>
							<th width="100">Early</th>
							<th width="100">On Time</th>
							<th width="100">Delay</th>
						</tr>
					</thead>
				</table>
				<div style="width:570px; max-height:350px;overflow-y:scroll" id="scroll_body" >
				 <table width="550" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">

					 <?
						if($cbo_type == 1) // Shipment Date wise
						{

							$data_array=  sql_select($sql);
							foreach($data_array as $row)
							{
								$data_res["buy"][$row[csf("buyer_name")]] =  $row[csf("buyer_name")];
								if($data_res["buy"][$row[csf("buyer_name")]])
								{
									$data_res["exQnty"][$row[csf("buyer_name")]] += $row[csf("ex_qnty")];
								}
							}

							foreach($data_array as $row)
							{
								if($row[csf("delay_time")] >0)
								{
									$data_res["early"][$row[csf("buyer_name")]]  += ($row[csf("ex_qnty")]/$data_res["exQnty"][$row[csf("buyer_name")]])*100;
								}
								else if($row[csf("delay_time")] <0)
								{
									$data_res["delay"][$row[csf("buyer_name")]]  += ($row[csf("ex_qnty")]/$data_res["exQnty"][$row[csf("buyer_name")]])*100;
								}
								else
								{
									$data_res["on_time"][$row[csf("buyer_name")]]  += ($row[csf("ex_qnty")]/$data_res["exQnty"][$row[csf("buyer_name")]])*100;
								}
							}
							$grand_qnty = 0; $i = 1;
							foreach($data_res["buy"] as $key =>$value)
							{
								if(number_format($data_res["early"][$key],2) != 0.00)
								{
									$early_time = number_format($data_res["early"][$key],2)."%";
								}
								else
								{
									$early_time= "";
								}
								if(number_format($data_res["on_time"][$key],2) != 0.00)
								{
									$On_time = number_format($data_res["on_time"][$key],2)."%";
								}
								else
								{
									$On_time= "";
								}
								if(number_format($data_res["delay"][$key],2) != 0.00)
								{
								   $delay_time= number_format($data_res["delay"][$key],2)."%";
								}
								else
								{
									$delay_time= "";
								}

								if ($i % 2 == 0)
									$bgcolor = "#E9F3FF";
								else
									$bgcolor = "#FFFFFF";
								$grand_qnty += $data_res["exQnty"][$key];
								?>

								<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('se_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="se_<? echo $i; ?>">
									<td width="150"><? echo $buyerArr[$value]["name"];?></td>
									<td width="100" align="right"><? echo $data_res["exQnty"][$key];?></td>
									<td width="100" align="right"><? echo $early_time;?></td>
									<td width="100" align="right"><? echo $On_time;?></td>
									<td width="" align="right"><? echo $delay_time;?></td>
								</tr>
								<?
								$i++;
							}
						}
						else // Country Ship Date wise
						{
							$i = 1;
							foreach($data_buyer_wise as $buyerId => $row)
							{
								$onTime = ($data_buyer_wise[$buyerId]["ontime"]/$data_buyer_wise[$buyerId]["ex_qnty"])*100;
								$early = ($data_buyer_wise[$buyerId]["early"]/$data_buyer_wise[$buyerId]["ex_qnty"])*100;
								$delay = ($data_buyer_wise[$buyerId]["delay"]/$data_buyer_wise[$buyerId]["ex_qnty"])*100;
								if(number_format($onTime,2) != 0.00)
								{
									$onTime = number_format($onTime,2)."%";
								}
								else
								{
									$onTime= "";
								}
								if(number_format($early,2) != 0.00)
								{
									$early = number_format($early,2)."%";
								}
								else
								{
									$early= "";
								}
								if(number_format($delay,2) != 0.00)
								{
									$delay = number_format($delay,2)."%";
								}
								else
								{
									$delay= "";
								}

								if ($i % 2 == 0)
									$bgcolor = "#E9F3FF";
								else
									$bgcolor = "#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('se_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="se_<? echo $i; ?>">
									<td width="150"><? echo $buyerArr[$buyerId]["name"];?></td>
									<td width="100" align="right"><? echo $data_buyer_wise[$buyerId]["ex_qnty"];?></td>
									<td width="100" align="right"><? echo $early;?></td>
									<td width="100" align="right"><? echo $onTime;?></td>
									<td width="" align="right"><? echo $delay;?></td>
								</tr>
								<?
								$grand_qnty += $data_buyer_wise[$buyerId]["ex_qnty"];
								$i++;
							}
						}

						?>
						</table>
					</div>
						<table style="width:550px;" border="1" cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all" >
							<tr style="background-color:#e0e0e0; font-weight: bold">
								<td width="150">Total</td>
								<td width="100" align="right"><?echo $grand_qnty;?></td>
								<td colspan="3">&nbsp;</td>
							</tr>
						</table>
			</fieldset>

			<!-- ========================= Export Summary By Production Factory Start =========================-->

			<fieldset  style="width:720px; float:left;">
				<table style="width:700px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" >
					<thead>
						<tr>
							<td colspan="6" style="font-size:15px; font-weight:bold">Export Summary By Production Factory</td>
						</tr>
						<tr>
							<th width="150">Production Factory</th>
							<th width="150">Location</th>
							<th width="130">Buyer</th>
							<th width="100"><p>Export (Qty)<br> <small style="font-size: 10px;">Excluding Return</small></p></th>
							<th width="100">FOB</th>
							<th width="">Value in USD</th>
						</tr>
					</thead>
				</table>
				<div style=" max-height:350px; width:720px; overflow-y:scroll;" id="scroll_body2">
				<table style="width:700px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" >
					<?
					$buyer_row_span_arr = array();
					foreach($result_array as $company_id => $company_data)
					{

						foreach($company_data as $location_id => $location_data)
						{
							$buyer_row_span= 0;
							foreach($location_data as $buyer_id => $row)
							{
								$buyer_row_span++;
							}
							$buyer_row_span_arr[$company_id."*".$location_id] =$buyer_row_span;
						}
					}

					// echo  $buyer_row_span_arr[];
					// echo "<pre>";
					// print_r($buyer_row_span_arr);
					// echo "</pre>";

					$i=$m=1;

					foreach($result_array as $company_id => $company_data)
					{
						foreach($company_data as $location_id => $location_data)
						{
							$y = 1;
							foreach($location_data as $buyer_id => $row)
							{
								if ($m % 2 == 0)
								$bgcolor = "#E9F3FF";
								else
								$bgcolor = "#FFFFFF";
								$buyer_td_span = $buyer_row_span_arr[$company_id."*".$location_id];
								//$row["ex_qnty"];
								//echo $buyer_td_span."*";
								$po_arr =  array_filter(array_unique(explode(",",chop($row["po_id"],","))));
								$ex_return_qnty = 0;$ex_return_value=0;
								foreach($po_arr as $po_id)
								{
									$color_size_arr = array_filter(array_unique(explode(",",chop($ex_return_qty_arr[$po_id]["color_size_list"],","))));

									foreach($color_size_arr as $color_size_id)
									{
										$ex_return_qnty +=  $ex_return_qty_arr[$po_id][$color_size_id]['return_qty'];
										$ex_return_value +=  $ex_return_qty_arr[$po_id][$color_size_id]['return_value'];
									}
								}
								$ex_qnty_after_return=  $row["ex_qnty"] - $ex_return_qnty;
								$ex_value_after_return=  $row["ex_value"] - $ex_return_value;
								$FOB = $ex_value_after_return/$ex_qnty_after_return;
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $m; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
								<?
								if($y == 1)
								{
									?>
									<td width="150" rowspan="<? echo $buyer_td_span;?>"><? echo $companyArr[$company_id];?></td>
									<td width="150" rowspan="<? echo $buyer_td_span;?>"><div style="word-wrap:break-word; width:150px"><? echo $locationArr[$location_id];?></div></td>
									
									<?
								}
								$row["po_id"];$row["color_size"];
								?>
									<td width="130"><? echo $buyerArr[$buyer_id]["name"];?></td>
									<td width="100" align="right"><a href="##" onclick="openmypage_ex_popup('<? echo $company_id;?>','<? echo $location_id;?>','<? echo $buyer_id;?>','<? //echo $row["color_size"];?>','<? echo $date_from;?>','<? echo $date_to;?>','<? echo  $ex_return_qnty;?>');"><? echo $ex_qnty_after_return;?></a></td>
									<td width="100" align="right"><? echo number_format($FOB,2);?></td>
									<td width="" align="right"><? echo number_format($ex_value_after_return,2);?></td>
								</tr>
								<?
								$sub_ex_factory_qnty += $ex_qnty_after_return;
								$sub_ex_value += $ex_value_after_return;
								$grand_ex_factory_qnty += $ex_qnty_after_return;
								$grand_ex_value += $ex_value_after_return;
								$y++;$m++;
							}

							?>
							<tr style="background-color:#e0e0e0; font-weight: bold">
							<td colspan="3" align="right" ><b>Sub Total</b></td>
							<td align="right"><? echo number_format($sub_ex_factory_qnty,2); ?></td>
							<td>&nbsp;</td>
							<td align="right"><? echo number_format($sub_ex_value,2);?></td>
							</tr>
							<?
						  $i++;
						   $sub_ex_factory_qnty=$sub_ex_value=0;
						}
					}
					?>
					<tr style="background-color:#e0e0e0; font-weight: bold">
						<td colspan="3" align="right" ><b>Grand Total</b></td>
						<td align="right"><? echo number_format($grand_ex_factory_qnty,2); ?></td>
						<td>&nbsp;</td>
						<td align="right"><? echo number_format($grand_ex_value,2);?></td>
					</tr>
				</table>
				</div>
			</fieldset>
		<?
	}
	else if($report_type==4)
	{
		if ($date_from && $date_to && $cbo_type != 4)
		{
			if ($db_type == 0) {
				$sql_cond .= " and m.ex_factory_date between '" . change_date_format($date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($date_to, 'yyyy-mm-dd') . "'";
			} else {
				$sql_cond .= " and m.ex_factory_date between '" . date("j-M-Y", strtotime($date_from)) . "' and '" . date("j-M-Y", strtotime($date_to)) . "'";
			}
		}

		if ($cbo_company>0) $sql_cond .= " and dm.delivery_company_id = $cbo_company";
		if ($cbo_location>0) $sql_cond .= " and dm.delivery_location_id = $cbo_location";
		if ($cbo_buyer>0) $sql_cond .= " and a.buyer_name = $cbo_buyer";
		if ($cbo_dealing_merchant>0) $sql_cond .= " and a.DEALING_MARCHANT = $cbo_dealing_merchant";

		if($cbo_type == 1)
		{
			if ($db_type == 0) $delayShortSelect = ", DATEDIFF(b.pub_shipment_date, m.ex_factory_date) as delay_time ";
			else $delayShortSelect = ", (b.pub_shipment_date - m.ex_factory_date) as delay_time ";
		}
		else if($cbo_type == 2)
		{
			if ($db_type == 0) $delayShortSelect = ", DATEDIFF(c.country_ship_date, m.ex_factory_date) as delay_time ";
			else $delayShortSelect = ", (c.country_ship_date - m.ex_factory_date) as delay_time ";
		}
		else if($cbo_type == 3)
		{
			if ($db_type == 0) $delayShortSelect = ", DATEDIFF(b.shipment_date, m.ex_factory_date) as delay_time ";
			else $delayShortSelect = ", (b.shipment_date - m.ex_factory_date) as delay_time ";
		}
		else if($cbo_type == 4)
		{
			if ($db_type == 0) {
				$sql_cond .= " and b.PUB_SHIPMENT_DATE between '" . change_date_format($date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($date_to, 'yyyy-mm-dd') . "'";
			} else {
				$sql_cond .= " and b.PUB_SHIPMENT_DATE between '" . date("j-M-Y", strtotime($date_from)) . "' and '" . date("j-M-Y", strtotime($date_to)) . "'";
			}
		}
		//

		$sql_res=sql_select("select b.po_break_down_id as po_id, c.color_size_break_down_id, c.production_qnty as return_qnty, d.order_rate
                from pro_ex_factory_mst b, pro_ex_factory_dtls c, wo_po_color_size_breakdown d
                where b.id = c.mst_id and c.color_size_break_down_id = d.id
                and b.entry_form = 85
                and b.status_active=1 and b.is_deleted=0
                and c.status_active = 1 and c.is_deleted = 0");
		$ex_return_qty_arr=array();
		foreach($sql_res as $row)
		{
			$ex_return_value =  $row[csf('return_qnty')]*$row[csf('order_rate')];
			$ex_return_qty_arr[$row[csf('po_id')]][$row[csf('color_size_break_down_id')]]['return_qty']=$row[csf('return_qnty')];
			$ex_return_qty_arr[$row[csf('po_id')]][$row[csf('color_size_break_down_id')]]['return_value']=$ex_return_value;
			$ex_return_qty_arr[$row[csf('po_id')]]['color_size_list'].=$row[csf('color_size_break_down_id')].",";
		}

	   $sql_country_w="SELECT  dm.delivery_company_id, dm.delivery_location_id, m.id, m.po_break_down_id, m.foc_or_claim, m.ex_factory_date, m.ex_factory_qnty as ex_qnty, a.buyer_name,a.DEALING_MARCHANT, a.job_no_prefix_num, a.job_no, a.style_ref_no, a.ship_mode, b.po_number, b.shipment_date, b.pub_shipment_date, b.shiping_status, c.country_ship_date, c.order_quantity, c.order_rate, c.order_total, c.shiping_status as cshiping_status, d.production_qnty, d.production_qnty*c.order_rate as ex_value, c.id as color_size $delayShortSelect
        from pro_ex_factory_delivery_mst dm, pro_ex_factory_mst m, wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,
        pro_ex_factory_dtls d
        where dm.id = m.delivery_mst_id and a.job_no = c.job_no_mst and a.job_no = b.job_no_mst and b.id = c.po_break_down_id and m.id = d.mst_id $sql_cond and d.color_size_break_down_id = c.id  and dm.delivery_company_id<>0 and m.entry_form<>85 and dm.delivery_company_id is not null and dm.status_active = 1 and dm.is_deleted = 0 and a.status_active = 1 and a.is_deleted = 0 and m.status_active = 1 and m.is_deleted = 0 order by m.id";

		//echo $sql_country_w;

	    $country_w_res = sql_select($sql_country_w);
	    $data_buyer_wise = array(); $qntyChkArr = array(); $result_array= array(); $jobPoWiseArr= array(); $poIds='';
	    foreach($country_w_res as  $row)
	    {
			$poIds.=$row[csf("po_break_down_id")].",";
	        $buffer_time = 0;
	        if($buyerArr[$row[csf("buyer_name")]]["buffer_time"])
	        {
	            $buffer_time = "+".$buyerArr[$row[csf('buyer_name')]]['buffer_time']." days";
	        }
			//echo $row[csf("pub_shipment_date")]; die;
			$shipDate=$shippingStatus="";
			if($cbo_type == 1 && $cbo_type == 4){
				$time_diff_buffer = strtotime($row[csf("pub_shipment_date")] . $buffer_time) - strtotime($row[csf("ex_factory_date")]);
				$time_diff = strtotime($row[csf("pub_shipment_date")]) - strtotime($row[csf("ex_factory_date")]);
				$shipDate=$row[csf("pub_shipment_date")];
				$shippingStatus=$row[csf("shiping_status")];
			}
			else if($cbo_type == 2){
				//echo strtotime($row[csf("country_ship_date")] . $buffer_time);
				//echo $time = date("m/d/Y",strtotime($row[csf("country_ship_date")] . $buffer_time)); die;
				$time_diff_buffer = strtotime($row[csf("country_ship_date")] . $buffer_time) - strtotime($row[csf("ex_factory_date")]);
				$time_diff = strtotime($row[csf("country_ship_date")]) - strtotime($row[csf("ex_factory_date")]);
				$shipDate=$row[csf("country_ship_date")];
				$shippingStatus=$row[csf("cshiping_status")];
			}
			else if($cbo_type == 3){
				$time_diff_buffer = strtotime($row[csf("shipment_date")] . $buffer_time) - strtotime($row[csf("ex_factory_date")]);
				$time_diff = strtotime($row[csf("shipment_date")]) - strtotime($row[csf("ex_factory_date")]);
				$shipDate=$row[csf("shipment_date")];
				$shippingStatus=$row[csf("shiping_status")];
			}
			$poStr="";

			$poStr=$row[csf("buyer_name")].'__'.$row[csf("job_no")].'__'.$row[csf("style_ref_no")].'__'.$row[csf("po_number")].'__'.$row[csf("ship_mode")].'__'.$shipDate.'__'.$shippingStatus.'__'.$row[csf("po_break_down_id")].'__'.$row[csf("DEALING_MARCHANT")];

	        $data_buyer_wise[$row[csf("buyer_name")]]["ex_qnty"] += $row[csf("production_qnty")];
			$data_buyer_wise[$row[csf("buyer_name")]]["ex_val"] += $row[csf("ex_value")];
			if($row[csf("foc_or_claim")]==2){
				$data_buyer_wise[$row[csf("buyer_name")]]["exClaimQnty"] += $row[csf("production_qnty")];
				$result_array[$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]][$row[csf("buyer_name")]]['exClaimQnty'] += $row[csf("production_qnty")];
			}

	        if($time_diff_buffer > 0)
	        {
	            $data_buyer_wise[$row[csf("buyer_name")]]["early"] += $row[csf("production_qnty")];
				$result_array[$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]][$row[csf("buyer_name")]]['early'] += $row[csf("production_qnty")];

				$jobPoWiseArr[$poStr][$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]]['early']+= $row[csf("production_qnty")];
	        }
	        else if($time_diff_buffer < 0 )
	        {
	            $data_buyer_wise[$row[csf("buyer_name")]]["delay"] += $row[csf("production_qnty")];
				$result_array[$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]][$row[csf("buyer_name")]]['delay'] += $row[csf("production_qnty")];
				$jobPoWiseArr[$poStr][$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]]['delay']+= $row[csf("production_qnty")];
	        }
	        else if($time_diff <= 0 && $time_diff_buffer >= 0)
	        {
	            $data_buyer_wise[$row[csf("buyer_name")]]["ontime"] += $row[csf("production_qnty")];
				$result_array[$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]][$row[csf("buyer_name")]]['ontime'] += $row[csf("production_qnty")];
				$jobPoWiseArr[$poStr][$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]]['ontime']+= $row[csf("production_qnty")];
	        }

			$result_array[$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]][$row[csf("buyer_name")]]['ex_qnty'] += $row[csf("production_qnty")];
	        $result_array[$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]][$row[csf("buyer_name")]]['ex_value'] += $row[csf("ex_value")];
	        $result_array[$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]][$row[csf("buyer_name")]]['po_id'] .= $row[csf("po_break_down_id")].",";
	        $result_array[$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]][$row[csf("buyer_name")]]['color_size'] .= $row[csf("color_size")].",";


			$jobPoWiseArr[$poStr][$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]]['ex_qnty']+= $row[csf("production_qnty")];
			$jobPoWiseArr[$poStr][$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]]['ex_value']+= $row[csf("ex_value")];
			$jobPoWiseArr[$poStr][$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]]['ex_date']=$row[csf("ex_factory_date")];

			if (!in_array($row[csf("color_size")],$color_sizeTmpArr) )
	        {
				$jobPoWiseArr[$poStr][$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]]['poQty']+=$row[csf("order_quantity")];
				$jobPoWiseArr[$poStr][$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]]['poVal']+=$row[csf("order_total")];
				$color_sizeTmpArr[]=$row[csf('color_size')];
			}
	    }



		$poIds=implode(",",array_filter(array_unique(explode(",",$poIds))));
		$tot_rows=count(explode(",",$poIds));
		$poIds_country_cond="";

		if($db_type==2 && $tot_rows>1000)
		{
			$poIds_country_cond=" and (";

			$poIdsArr=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$poIds_country_cond.=" a.id in($ids) or ";
			}
			$poIds_country_cond=chop($poIds_country_cond,'or ');
			$poIds_country_cond.=")";
		}
		else
		{
			$poIds_country_cond=" and a.id in ($poIds)";
		}


		$sqlQty="SELECT a.id, a.shipment_date, a.pub_shipment_date, b.country_ship_date, c.production_qnty
	        from wo_po_break_down a, wo_po_color_size_breakdown b, pro_ex_factory_dtls c
	        where a.id = b.po_break_down_id c.color_size_break_down_id = b.id and a.status_active = 1 and a.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 $poIds_country_cond";
		$sqlQty_res = sql_select($sqlQty); $totExQtyArr=array();
		foreach($sqlQty_res as  $row)
		{
			$shipDate="";
			if($cbo_type == 1) $shipDate=$row[csf("pub_shipment_date")];
			else if($cbo_type == 2) $shipDate=$row[csf("country_ship_date")];
			else if($cbo_type == 3) $shipDate=$row[csf("shipment_date")];

			$totExQtyArr[$row[csf("id")]][$shipDate]+=$row[csf("production_qnty")];
		}
		unset($sqlQty_res);
		 /*$sql = "SELECT b.id,dm.delivery_company_id,dm.delivery_location_id, m.ex_factory_qnty as ex_qnty, a.buyer_name,(b.unit_price/a.total_set_qnty) as unit_price,(b.po_quantity*a.total_set_qnty) as po_quantity,
	            ((b.unit_price/a.total_set_qnty)*(b.po_quantity*a.total_set_qnty)) as  po_value $delayShortSelect ,m.foc_or_claim
	            from pro_ex_factory_delivery_mst dm,pro_ex_factory_mst m, wo_po_details_master a, wo_po_break_down b
	            where dm.id = m.delivery_mst_id and a.job_no = b.job_no_mst
	            and b.id= m.po_break_down_id and dm.status_active = 1
	            and dm.is_deleted = 0 and m.status_active = 1 and m.is_deleted = 0 and dm.delivery_company_id <> 0
	            and m.entry_form <> 85 and dm.delivery_company_id is not null
	            $sql_cond
	            order by dm.delivery_company_id , dm.delivery_location_id, a.buyer_name asc ";
			$sql_res = sql_select($sql);
			$summar_arr = array();
			foreach($sql_res as $row)
			{
				$summar_arr[$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]][$row[csf("buyer_name")]]['ex_qnty'] += $row[csf("ex_qnty")];
				$summar_arr[$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]][$row[csf("buyer_name")]]['ex_value'] += $row[csf("po_value")];
				$summar_arr[$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]][$row[csf("buyer_name")]]['po_id'] .= $row[csf("po_break_down_id")].",";
				$summar_arr[$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]][$row[csf("buyer_name")]]['color_size'] .= $row[csf("color_size")].",";
			}*/



		$invoiceSql = "select a.ID,a.BUYER_ID,a.LOCATION_ID,a.CLAIM_AMMOUNT,b.PO_BREAKDOWN_ID,b.CURRENT_INVOICE_VALUE  from COM_EXPORT_INVOICE_SHIP_MST a, COM_EXPORT_INVOICE_SHIP_DTLS b where a.id=B.MST_ID ".where_con_using_array(explode(",",$poIds),0,'b.PO_BREAKDOWN_ID')."";
		$invoiceSqlResult = sql_select($invoiceSql); $invoiceValue=array();
		foreach($invoiceSqlResult as  $row)
		{
			$invoiceValue[$row[LOCATION_ID]][$row[BUYER_ID]]+=$row[CURRENT_INVOICE_VALUE];
			$invoiceClimValue[$row[LOCATION_ID]][$row[BUYER_ID]][$row[ID]]=$row[CLAIM_AMMOUNT];

			$buyerInvoiceValue[$row[BUYER_ID]]+=$row[CURRENT_INVOICE_VALUE];
			$buyerInvoiceClimValue[$row[BUYER_ID]]=$row[CLAIM_AMMOUNT];

		}
		unset($invoiceSqlResult);


		ob_start();
		?>
		<div>
			<table style="width:900px;" border="0" cellpadding="2" cellspacing="0"  >
				<thead>
					<tr>
						<td colspan="10" align="center" style="border:none;font-size:16px; font-weight:bold" ><? echo $companyArr[$cbo_company];?> </td>
					</tr>
					<tr>
						<td colspan="10" align="center" style="font-weight: bold; border: none;">
							<?
							echo strtoupper($report_title);
							if ($date_from != "" && $date_to != "") {
								echo "<br/>From: ".change_date_format($date_from)."  To: ".change_date_format($date_to);
							}
							?>
						</td>
					</tr>
				</thead>
			</table>
			<fieldset style="width:1200px; margin-bottom: 5px; margin-right: 5px;">
				<table width="1200" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" >
					<thead>
						<tr>
							<td colspan="13" align="center" style="font-size:15px; font-weight:bold">Shipment Evaluation</td>
						</tr>
						<tr>
							<th width="150" rowspan="3">Buyer</th>
							<th width="100" rowspan="3">Qty(pcs)</th>
							<th width="100" rowspan="3">Value ($)</th>
                            <th width="100" rowspan="3">Invoice Value</th>
                            <th width="100" rowspan="3">Difference</th>
							<th width="" colspan="6">Shipment Status</th>
                            <th width="100" rowspan="3">Pre Paid (Penalty) %</th>
                            <th rowspan="3">Claim Value</th>
						</tr>
						<tr>
							<th colspan="2">Early</th>
							<th colspan="2">On Time</th>
							<th colspan="2">Delay</th>
						</tr>

                        <tr>
							<th width="100">Qty</th>
							<th width="50">%</th>
							<th width="100">Qty</th>
							<th width="50">%</th>
							<th width="100">Qty</th>
							<th width="50">%</th>
						</tr>

					</thead>
				</table>
				<div style="width:1220px; max-height:350px;overflow-y:scroll" id="scroll_body" >
				 <table width="1200"  border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">

					 <?

						if($cbo_type == 1 || $cbo_type == 3 || $cbo_type == 4) // Shipment Date wise
						{ //echo 1111;die;
							$sql = "SELECT b.id,dm.delivery_company_id,dm.delivery_location_id, m.ex_factory_qnty as ex_qnty, a.buyer_name,(b.unit_price/a.total_set_qnty) as unit_price,(b.po_quantity*a.total_set_qnty) as po_quantity,
								((b.unit_price/a.total_set_qnty)*(b.po_quantity*a.total_set_qnty)) as  po_value $delayShortSelect ,m.foc_or_claim
								from pro_ex_factory_delivery_mst dm,pro_ex_factory_mst m, wo_po_details_master a, wo_po_break_down b
								where dm.id = m.delivery_mst_id and a.job_no = b.job_no_mst
								and b.id= m.po_break_down_id and dm.status_active = 1
								and dm.is_deleted = 0 and m.status_active = 1 and m.is_deleted = 0 and dm.delivery_company_id <> 0
								and m.entry_form <> 85 and dm.delivery_company_id is not null
								$sql_cond
								order by dm.delivery_company_id , dm.delivery_location_id, a.buyer_name asc ";
								//echo $sql;
							$data_array=  sql_select($sql);
							foreach($data_array as $row)
							{
								$data_res["buy"][$row[csf("buyer_name")]] =  $row[csf("buyer_name")];
								if($data_res["buy"][$row[csf("buyer_name")]])
								{
									$data_res["exQnty"][$row[csf("buyer_name")]] += $row[csf("ex_qnty")];
									$data_res_ex_val[$row[csf("delivery_location_id")]][$row[csf("buyer_name")]] += $row[csf("ex_qnty")]*$row[csf("unit_price")];
									$data_res["exVal"][$row[csf("buyer_name")]] += $row[csf("ex_qnty")]*$row[csf("unit_price")];
									if($row[csf("foc_or_claim")]==2){
										$data_res["exClaimQnty"][$row[csf("buyer_name")]] += $row[csf("ex_qnty")];
									}
								}
							}

							foreach($data_array as $row)
							{
								if($row[csf("delay_time")] >0)
								{
									$data_res["early"][$row[csf("buyer_name")]]  += ($row[csf("ex_qnty")]/$data_res["exQnty"][$row[csf("buyer_name")]])*100;
								}
								else if($row[csf("delay_time")] <0)
								{
									$data_res["delay"][$row[csf("buyer_name")]]  += ($row[csf("ex_qnty")]/$data_res["exQnty"][$row[csf("buyer_name")]])*100;
								}
								else
								{
									$data_res["on_time"][$row[csf("buyer_name")]]  += ($row[csf("ex_qnty")]/$data_res["exQnty"][$row[csf("buyer_name")]])*100;
								}
							}

							$grand_qnty = 0; $i = 1;
							foreach($data_res["buy"] as $key =>$value)
							{
								if(number_format($data_res["early"][$key],2) != 0.00)
								{
									$early_qty= number_format($data_res["early"][$key]*$data_res["exQnty"][$key]/100);
									$early_time = number_format($data_res["early"][$key],2);
									$tot_early_qty+=($data_res["early"][$key]*$data_res["exQnty"][$key]/100);

								}
								else
								{
									$early_time= "";
								}

								if(number_format($data_res["on_time"][$key],2) != 0.00)
								{
									$On_qty= number_format($data_res["on_time"][$key]*$data_res["exQnty"][$key]/100);
									$On_time = number_format($data_res["on_time"][$key],2);
									$tot_onTime_qty+=($data_res["on_time"][$key]*$data_res["exQnty"][$key]/100);

								}
								else
								{
									$On_time= "";
									$On_qty= "";
								}

								if(number_format($data_res["delay"][$key],2) != 0.00)
								{
								   $delay_qty= number_format($data_res["delay"][$key]*$data_res["exQnty"][$key]/100);
								   $delay_time= number_format($data_res["delay"][$key],2);
								   $tot_delay_qty+=($data_res["delay"][$key]*$data_res["exQnty"][$key]/100);
								}
								else
								{
									$delay_time= "";
									$delay_qty= "";
								}

								if ($i % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
								$grand_qnty += $data_res["exQnty"][$key];
								$grand_val +=$data_res["exVal"][$key];


								$total_buyer_inv_val+=$buyerInvoiceValue[$key];
								$total_buyer_dif_val+=$data_res["exVal"][$key]-$buyerInvoiceValue[$key];
								$total_buyer_clim_inv_val+=$buyerInvoiceClimValue[$key];


								?>

								<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('se_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="se_<? echo $i; ?>">
									<td width="150"><? echo $buyerArr[$value]["name"];?></td>
									<td width="100" align="right"><? echo $data_res["exQnty"][$key];?></td>
									<td width="100" align="right"><? echo number_format($data_res["exVal"][$key],2);?></td>
									<td width="100" align="right"><? echo number_format($buyerInvoiceValue[$key],2);?></td>
                                    <td width="100" align="right"><? echo number_format($data_res["exVal"][$key]-$buyerInvoiceValue[$value],2);?></td>

                                    <td width="100" align="right"><? echo $early_qty;?></td>
                                    <td width="50" align="right"><? echo $early_time;?></td>
									<td width="100" align="right"><? echo $On_qty;?></td>
									<td width="50" align="right"><? echo $On_time;?></td>
									<td width="100" align="right"><? echo $delay_qty;?></td>
									<td width="50" align="right"><? echo $delay_time;?></td>
									<td width="100" align="right"><? echo number_format(($data_res["exClaimQnty"][$key]*100)/$data_res["exQnty"][$key],2);?></td>
                                    <td align="right"><? echo number_format($buyerInvoiceClimValue[$key],2);?></td>
								</tr>
								<?
								$i++;
							}
						}
						else // Country Ship Date wise
						{
							$i = 1;

							foreach($data_buyer_wise as $buyerId => $row)
							{
								$onTime = ($data_buyer_wise[$buyerId]["ontime"]/$data_buyer_wise[$buyerId]["ex_qnty"])*100;
								$early = ($data_buyer_wise[$buyerId]["early"]/$data_buyer_wise[$buyerId]["ex_qnty"])*100;
								$delay = ($data_buyer_wise[$buyerId]["delay"]/$data_buyer_wise[$buyerId]["ex_qnty"])*100;
								if(number_format($onTime,2)!= 0.00)
								{
									$onTime_qty= number_format($onTime*$data_buyer_wise[$buyerId]["ex_qnty"]/100);
									$tot_onTime_qty+=($onTime*$data_buyer_wise[$buyerId]["ex_qnty"]/100);
									$onTime = number_format($onTime,2);
								}
								else
								{
									$onTime= ""; $onTime_qty= "";
								}

								if(number_format($early,2)!= 0.00)
								{
									$early_qty= number_format($early*$data_buyer_wise[$buyerId]["ex_qnty"]/100);
									$tot_early_qty+=($early*$data_buyer_wise[$buyerId]["ex_qnty"]/100);
									$early = number_format($early,2);
								}
								else
								{
									$early= ""; $early_qty= "";
								}


								if(number_format($delay,2) != 0.00)
								{
									$delay_qty= number_format($delay*$data_buyer_wise[$buyerId]["ex_qnty"]/100);
									$tot_delay_qty+=($delay*$data_buyer_wise[$buyerId]["ex_qnty"]/100);
									$delay = number_format($delay,2);
								}
								else
								{
									$delay= ""; $delay_qty= "";
								}

								if ($i % 2 == 0) $bgcolor = "#E9F3FF";
								else $bgcolor = "#FFFFFF";

								?>
								<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('se_<? echo $i; ?>', '<? echo $bgcolor; ?>')" id="se_<? echo $i; ?>">
									<td width="150"><? echo $buyerArr[$buyerId]["name"];?></td>
									<td width="100" align="right"><? echo $data_buyer_wise[$buyerId]["ex_qnty"];?></td>
									<td width="100" align="right"><? echo number_format($data_buyer_wise[$buyerId]["ex_val"],2);?></td>

									<td width="100" align="right"><? echo number_format($buyerInvoiceValue[$key],2);?></td>
                                    <td width="100" align="right"><? echo number_format($data_res["exVal"][$key]-$buyerInvoiceValue[$value],2);?></td>


                                    <td width="100" align="right"><? echo $early_qty;?></td>
                                    <td width="50" align="right" title="<?='(' . $data_buyer_wise[$buyerId]["early"] . '/' . $data_buyer_wise[$buyerId]["ex_qnty"] . ')*100';?>"><? echo $early;?></td>
									<td width="100" align="right"><? echo $onTime_qty;?></td>
									<td width="50" align="right"><? echo $onTime;?></td>
									<td width="100" align="right"><? echo $delay_qty;?></td>
									<td width="50" align="right"><? echo $delay;?></td>
									<td width="100" align="right"><?=number_format(($data_buyer_wise[$buyerId]["exClaimQnty"] * 100) / $data_buyer_wise[$buyerId]["ex_qnty"], 2);?></td>
                                    <td align="right"><? echo number_format($buyerInvoiceClimValue[$key],2);?></td>

								</tr>
								<?
								$grand_qnty += $data_buyer_wise[$buyerId]["ex_qnty"];
								$grand_val += $data_buyer_wise[$buyerId]["ex_val"];

								$i++;
							}
						}

						?>
						</table>
					</div>
						<table style="width:1200px;" border="1" cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all" >
							<tr style="background-color:#e0e0e0; font-weight: bold">
								<td width="150">Total</td>
								<td width="100" align="right"><? echo $grand_qnty;?></td>
								<td width="100" align="right"><? echo number_format($grand_val,2);?></td>

								<td align="right" width="100"><? echo number_format($total_buyer_inv_val,2);?></td>
								<td align="right" width="100"><? echo number_format($total_buyer_dif_val,2);?></td>

								<td width="100"><? echo number_format($tot_early_qty,0);?></td>


                                <td width="50"><? echo number_format(($tot_early_qty*100)/$grand_qnty,2);?></td>
								<td width="100"><? echo number_format($tot_onTime_qty,0);?></td>
								<td width="50"><? echo number_format(($tot_onTime_qty*100)/$grand_qnty,2);?></td>
								<td width="100"><? echo number_format($tot_delay_qty,0);?></td>
								<td width="50"><? echo number_format(($tot_delay_qty*100)/$grand_qnty,2);?></td>
								<td width="100">&nbsp;</td>
								<td align="right"><? echo number_format($total_buyer_clim_inv_val,2);?></td>
							</tr>
						</table>
			</fieldset>

			<!-- ========================= Export Summary By Production Factory Start =========================-->

			<fieldset  style="width:1450px; float:left;">
				<table style="width:1430px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" >
					<thead>
						<tr>
							<td colspan="12" align="center" style="font-size:15px; font-weight:bold;">Export Summary By Production Factory</td>
						</tr>
						<tr>
							<th width="150" rowspan="2">Production Factory</th>
							<th width="150" rowspan="2">Location</th>
							<th width="130" rowspan="2">Buyer</th>
							<th width="100" rowspan="2"><p>Export (Qty)<br> <small style="font-size: 10px;">Excluding Return</small></p></th>

                            <th colspan="2">Early</th>
							<th colspan="2">On Time</th>
							<th colspan="2">Delay</th>

							<th width="100" rowspan="2">FOB</th>
							<th width="100" rowspan="2">Value in USD</th>

                            <th width="100" rowspan="2">Invoice Value</th>
                            <th width="100" rowspan="2">Difference</th>
                            <th rowspan="2">Claim Value</th>
						</tr>

                        <tr>
							<th width="100">Qty</th>
							<th width="50">%</th>
							<th width="100">Qty</th>
							<th width="50">%</th>
							<th width="100">Qty</th>
							<th width="50">%</th>
						</tr>
					</thead>
				</table>
				<div style=" max-height:350px; width:1450px; overflow-y:scroll;" id="scroll_body2">
				<table style="width:1430px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" >
					<?
					$buyer_row_span_arr = array();
					foreach($result_array as $company_id => $company_data)
					{

						foreach($company_data as $location_id => $location_data)
						{
							$buyer_row_span= 0;
							foreach($location_data as $buyer_id => $row)
							{
								$buyer_row_span++;
							}
							$buyer_row_span_arr[$company_id."*".$location_id] =$buyer_row_span;
						}
					}

					// echo  $buyer_row_span_arr[];
					// echo "<pre>";
					// print_r($buyer_row_span_arr);
					// echo "</pre>";

					$i=$m=1;

					foreach($result_array as $company_id => $company_data)
					{
						foreach($company_data as $location_id => $location_data)
						{
							$y = 1;
							foreach($location_data as $buyer_id => $row)
							{
								if ($m % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
								$buyer_td_span = $buyer_row_span_arr[$company_id."*".$location_id];
								$po_arr =  array_filter(array_unique(explode(",",chop($row["po_id"],","))));
								$ex_return_qnty = 0;$ex_return_value=0;
								foreach($po_arr as $po_id)
								{
									$color_size_arr = array_filter(array_unique(explode(",",chop($ex_return_qty_arr[$po_id]["color_size_list"],","))));

									foreach($color_size_arr as $color_size_id)
									{
										$ex_return_qnty +=  $ex_return_qty_arr[$po_id][$color_size_id]['return_qty'];
										$ex_return_value +=  $ex_return_qty_arr[$po_id][$color_size_id]['return_value'];
									}
								}
								$ex_qnty_after_return=  $row["ex_qnty"] - $ex_return_qnty;
								$ex_value_after_return=  $row["ex_value"] - $ex_return_value;
								$FOB = $ex_value_after_return/$ex_qnty_after_return;
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $m; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
								<?
								if($y == 1)
								{
									?>
									<td width="150" rowspan="<? echo $buyer_td_span;?>"><? echo $companyArr[$company_id];?></td>
									<td width="150" rowspan="<? echo $buyer_td_span;?>"><div style="word-wrap:break-word; width:150px"><? echo $locationArr[$location_id];?></div></td>
									<?
								}
								$row["po_id"]; $row["color_size"];

								$earlyPer = ($row["early"]/$row["ex_qnty"])*100;
								$onTimePer = ($row["ontime"]/$row["ex_qnty"])*100;
								$delayPer = ($row["delay"]/$row["ex_qnty"])*100;
								?>
									<td width="130"><?=$buyerArr[$buyer_id]["name"];?></td>
									<td width="100" align="right"><a href="##" onclick="openmypage_ex_popup('<?=$company_id;?>','<?=$location_id;?>','<?=$buyer_id;?>','','<?=$date_from;?>','<?=$date_to;?>','<?=$ex_return_qnty;?>');"><?=$ex_qnty_after_return;?></a></td>

                                    <td width="100" align="right"><?=$row["early"];?></td>
                                    <td width="50" align="right"><?=number_format($earlyPer, 2);?></td>
                                    <td width="100" align="right"><?=$row["ontime"];?></td>
                                    <td width="50" align="right"><?=number_format($onTimePer, 2);?></td>
                                    <td width="100" align="right"><?=$row["delay"];?></td>
                                    <td width="50" align="right"><?=number_format($delayPer, 2);?></td>

									<td width="100" align="right"><?=number_format($FOB, 2);?></td>
									<td width="100" align="right"><?=number_format($ex_value_after_return, 2);?></td>

                                    <td align="right" width="100"><?=number_format($invoiceValue[$location_id][$buyer_id], 2);?></td>
                                    <td align="right" width="100"><?=number_format($difference_val = $data_res_ex_val[$location_id][$buyer_id] - $invoiceValue[$location_id][$buyer_id], 2);?></td>
                                    <td align="right"><?=number_format(array_sum($invoiceClimValue[$location_id][$buyer_id]), 2);?></td>
								</tr>
								<?
								$subEarly+=$row["early"];
								$subOntime+=$row["ontime"];
								$subDelay+=$row["delay"];

								$totEarly+=$row["early"];
								$totOntime+=$row["ontime"];
								$totDelay+=$row["delay"];

								$sub_ex_factory_qnty += $ex_qnty_after_return;
								$sub_ex_value += $ex_value_after_return;
								$grand_ex_factory_qnty += $ex_qnty_after_return;
								$grand_ex_value += $ex_value_after_return;

								$sub_invoice_val+=$invoiceValue[$location_id][$buyer_id];
								$sub_difference_val+=$difference_val;
								$sub_claim_val+=array_sum($invoiceClimValue[$location_id][$buyer_id]);

								$grand_invoice_val+=$invoiceValue[$location_id][$buyer_id];
								$grand_difference_val+=$difference_val;
								$grand_claim_val+=array_sum($invoiceClimValue[$location_id][$buyer_id]);

								$y++; $m++;
							}

							?>
							<tr style="background-color:#e0e0e0; font-weight: bold">
                                <td colspan="3" align="right" ><b>Sub Total</b></td>
                                <td align="right"><?=number_format($sub_ex_factory_qnty, 2);?></td>

                                <td align="right"><?=number_format($subEarly, 2);?></td>
                                <td align="right"><?=number_format(($subEarly / $sub_ex_factory_qnty) * 100, 2);?></td>
                                <td align="right"><?=number_format($subOntime, 2);?></td>
                                <td align="right"><?=number_format(($subOntime / $sub_ex_factory_qnty) * 100, 2);?></td>
                                <td align="right"><?=number_format($subDelay, 2);?></td>
                                <td align="right"><?=number_format(($subDelay / $sub_ex_factory_qnty) * 100, 2);?></td>

                                <td>&nbsp;</td>
                                <td align="right"><?=number_format($sub_ex_value, 2);?></td>

                                <td align="right"><?=number_format($sub_invoice_val, 2);?></td>
                                <td align="right"><?=number_format($sub_difference_val, 2);?></td>
                                <td align="right"><?=number_format($sub_claim_val, 2);?></td>
							</tr>
							<?
						  $i++;
						   $sub_ex_factory_qnty=$sub_ex_value=$subEarly=$subOntime=$subDelay=0;
						   $sub_invoice_val=$sub_difference_val=$sub_claim_val=0;
						}
					}
					?>
					<tr style="background-color:#e0e0e0; font-weight: bold">
						<td colspan="3" align="right" ><b>Grand Total</b></td>
						<td align="right"><?=number_format($grand_ex_factory_qnty, 2);?></td>

                        <td align="right"><?=number_format($totEarly, 2);?></td>
                        <td align="right"><?=number_format(($totEarly / $grand_ex_factory_qnty) * 100, 2);?></td>
                        <td align="right"><?=number_format($totOntime, 2);?></td>
                        <td align="right"><?=number_format(($totOntime / $grand_ex_factory_qnty) * 100, 2);?></td>
                        <td align="right"><?=number_format($totDelay, 2);?></td>
                        <td align="right"><?=number_format(($totDelay / $grand_ex_factory_qnty) * 100, 2);?></td>

						<td>&nbsp;</td>
						<td align="right"><?=number_format($grand_ex_value, 2);?></td>

                        <td align="right"><?=number_format($grand_invoice_val, 2);?></td>
                        <td align="right"><?=number_format($grand_difference_val, 2);?></td>
                        <td align="right"><?=number_format($grand_claim_val, 2);?></td>
					</tr>
				</table>
				</div>
			</fieldset>

            <!-- ========================= Details Report : =========================-->

            <?
			$thCaption="";
			if($cbo_type == 1) $thCaption="Shipment Date";
			else if($cbo_type == 2) $thCaption="Country Shipment Date";
			else if($cbo_type == 3) $thCaption="Org. Shipment Date";
			?>

			<fieldset  style="width:1700px; float:left;">
				<table style="width:1680px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" >
					<thead>
						<tr>
							<td colspan="18" align="center" style="font-size:15px; font-weight:bold;">Details Report :</td>
						</tr>
						<tr>
                        	<th width="30">SL</th>
							<th width="120">Buyer Name</th>
                            <th width="150">Dealing Merchant</th>
							<th width="100">Job NO</th>
							<th width="100">Style Ref.</th>
							<th width="100">Order NO</th>

                            <th width="120">Del Company, Location</th>
							<th width="70"><?=$thCaption;?></th>
							<th width="100">Order Qty (PCS)</th>
							<th width="90">Shipping Mode</th>
							<th width="70">Ex-Fac. Date</th>

                            <th width="80">Current Ex-Fact. Qty (PCS)</th>
							<th width="90">Current Ex-Fact. Value</th>
							<th width="80">Early Qty</th>
							<th width="80">On Time Qty</th>
							<th width="80">Late Qty</th>

                            <th width="90">Total Shipment Qty</th>
							<th>Ex-Fact Status</th>
						</tr>
					</thead>
				</table>
				<div style=" max-height:350px; width:1700px; overflow-y:scroll;" id="scroll_body2">
				<table style="width:1680px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body4" >
					<?
					$q=1;

					foreach($jobPoWiseArr as $jobRow => $jobData)
					{
						foreach($jobData as $delComp => $delCompData)
						{
							foreach($delCompData as $delLoca => $row)
							{
								if ($q % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
								$exData=explode("__",$jobRow);
								$buyer_name=$job_no=$style_ref=$po_no=$ship_mode=$shipDate=$shippingStatus=$poid="";

								$buyer_name=$exData[0];
								$job_no=$exData[1];
								$style_ref=$exData[2];
								$po_no=$exData[3];
								$ship_mode=$exData[4];
								$shipDate=$exData[5];
								$shippingStatus=$exData[6];
								$poid=$exData[7];
								$deling_merchant=$exData[8];

								$delComLocation=$companyArr[$delComp].','.$locationArr[$delLoca];

								$totShipQty=$totExQtyArr[$poid][$shipDate]
								?>
								<tr bgcolor="<?=$bgcolor;?>" <?=$stylecolor;?> onClick="change_color('tr4_<?=$q;?>', '<?=$bgcolor;?>');" id="tr4_<?=$q;?>">
                                	<td width="30" align="center"><?=$q;?></td>
									<td width="120" style="word-break:break-all"><?=$buyerArr[$buyer_name]["name"];?></td>
									<td width="150"><?=$dealingMerchantArr[$deling_merchant];?></td>
                                    <td width="100" style="word-break:break-all"><?=$job_no;?></td>
                                    <td width="100" style="word-break:break-all"><?=$style_ref;?></td>
                                    <td width="100" style="word-break:break-all"><?=$po_no;?></td>

                                    <td width="120" style="word-break:break-all"><?=$delComLocation;?></td>
                                    <td width="70" style="word-break:break-all"><?=change_date_format($shipDate);?></td>
                                    <td width="100" align="right"><?=number_format($row["poQty"]);?></td>
                                    <td width="90" style="word-break:break-all"><?=$shipment_mode[$ship_mode];?></td>
                                    <td width="70" style="word-break:break-all"><?=change_date_format($row["ex_date"]);?></td>
									<td width="80" align="right"><?=$row["ex_qnty"];?></td>
									<td width="90" align="right"><?=number_format($row["ex_value"], 2);?></td>

                                    <td width="80" align="right"><a href="javascript:myPopup(<?=$poid;?>,'<?=$shipDate;?>',<?=$cbo_type;?>,'early_qty_dtls')"><?=$row["early"];?></a></td>
                                    <td width="80" align="right"><a href="javascript:myPopup(<?=$poid;?>,'<?=$shipDate;?>',<?=$cbo_type;?>,'ontime_qty_dtls')"><?=$row["ontime"];?></a></td>
                                    <td width="80" align="right"><a href="javascript:myPopup(<?=$poid;?>,'<?=$shipDate;?>',<?=$cbo_type;?>,'delay_qty_dtls')"><?=$row["delay"];?></a></td>

									<td width="90" align="right"><?=number_format($totShipQty, 2);?></td>
									<td style="word-break:break-all"><?=$shipment_status[$shippingStatus];?></td>
								</tr>
								<?
								$totDtlsEarly+=$row["early"];
								$totDtlsOntime+=$row["ontime"];
								$totDtlsDelay+=$row["delay"];
								$totDtlsPoQty+=$row["poQty"];
								$totDtlsExQty+=$row["ex_qnty"];
								$totDtlsExVal+=$row["ex_value"];
								$totDtlsShipQty+=$totShipQty;
								$q++;
							}
						}
					}
					?>
				</table>
				<table style="width:1680px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" >
					<tfoot>
                        <tr style="background-color:#e0e0e0; font-weight: bold">

                            <td width="30"></td>
                            <td width="120"></td>
                            <td width="150"></td>
                            <td width="100"></td>
                            <td width="100"></td>
                            <td width="100"></td>
                            <td width="120"></td>
                            <td width="70" align="right"><b>Grand Total</b></td>
                            <td width="100" align="right" id="totDtlsPoQty"><?=number_format($totDtlsPoQty);?></td>
                            <td width="90">&nbsp;</td>
                            <td width="70">&nbsp;</td>
                            <td width="80" align="right" id="totDtlsExQty"><?=number_format($totDtlsExQty);?></td>
                            <td width="90" align="right" id="value_totDtlsExVal"><?=number_format($totDtlsExVal, 2);?></td>
                            <td width="80" align="right" id="totDtlsEarly"><?=number_format($totDtlsEarly);?></td>
                            <td width="80" align="right" id="totDtlsOntime"><?=number_format($totDtlsOntime);?></td>
                            <td width="80" align="right" id="totDtlsDelay"><?=number_format($totDtlsDelay);?></td>
                            <td width="90" align="right" id="totDtlsShipQty"><?=number_format($totDtlsShipQty);?></td>
                            <td>&nbsp;</td>
                        </tr>
					</tfoot>
				</table>

				</div>
			</fieldset>
		<?

	}
	else if($report_type==5)
	{
		if ($date_from && $date_to && $cbo_type != 4)
		{
			if ($db_type == 0) {
				$sql_cond .= " and m.ex_factory_date between '" . change_date_format($date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($date_to, 'yyyy-mm-dd') . "'";
			} else {
				$sql_cond .= " and m.ex_factory_date between '" . date("j-M-Y", strtotime($date_from)) . "' and '" . date("j-M-Y", strtotime($date_to)) . "'";
			}
		}

		if ($cbo_company>0) $sql_cond .= " and dm.delivery_company_id = $cbo_company";
		if ($cbo_location>0) $sql_cond .= " and dm.delivery_location_id = $cbo_location";
		if ($cbo_buyer>0) $sql_cond .= " and a.buyer_name = $cbo_buyer";
		if ($cbo_dealing_merchant>0) $sql_cond .= " and a.DEALING_MARCHANT = $cbo_dealing_merchant";

		if($cbo_type == 1)
		{
			if ($db_type == 0) $delayShortSelect = ", DATEDIFF(b.pub_shipment_date, m.ex_factory_date) as delay_time ";
			else $delayShortSelect = ", (b.pub_shipment_date - m.ex_factory_date) as delay_time ";
		}
		else if($cbo_type == 2)
		{
			if ($db_type == 0) $delayShortSelect = ", DATEDIFF(c.country_ship_date, m.ex_factory_date) as delay_time ";
			else $delayShortSelect = ", (c.country_ship_date - m.ex_factory_date) as delay_time ";
		}
		else if($cbo_type == 3)
		{
			if ($db_type == 0) $delayShortSelect = ", DATEDIFF(b.shipment_date, m.ex_factory_date) as delay_time ";
			else $delayShortSelect = ", (b.shipment_date - m.ex_factory_date) as delay_time ";
		}
		else if($cbo_type == 4)
		{
			if ($db_type == 0) {
				$sql_cond .= " and b.PUB_SHIPMENT_DATE between '" . change_date_format($date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($date_to, 'yyyy-mm-dd') . "'";
			} else {
				$sql_cond .= " and b.PUB_SHIPMENT_DATE between '" . date("j-M-Y", strtotime($date_from)) . "' and '" . date("j-M-Y", strtotime($date_to)) . "'";
			}
		}
		else if($cbo_type == 5)
		{
			if ($db_type == 0) {
				//$sql_cond .= " and h.acc_ship_date between '" . change_date_format($date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($date_to, 'yyyy-mm-dd') . "'";
			} else {
				//$sql_cond .= " and h.acc_ship_date'" . date("j-M-Y", strtotime($date_from)) . "' and '" . date("j-M-Y", strtotime($date_to)) . "'";
			}
		}
		//
		$sql_ret= " and b.ex_factory_date between '" . date("j-M-Y", strtotime($date_from)) . "' and '" . date("j-M-Y", strtotime($date_to)) . "'";
		$sql_res=sql_select("select b.po_break_down_id as po_id, c.color_size_break_down_id, c.production_qnty as return_qnty, d.order_rate
                from pro_ex_factory_mst b, pro_ex_factory_dtls c, wo_po_color_size_breakdown d
                where b.id = c.mst_id and c.color_size_break_down_id = d.id
                and b.entry_form = 85
                and b.status_active=1 and b.is_deleted=0
                and c.status_active = 1 and c.is_deleted = 0 $sql_ret");
		$ex_return_qty_arr=array();
		foreach($sql_res as $row)
		{
			$ex_return_value =  $row[csf('return_qnty')]*$row[csf('order_rate')];
			$ex_return_qty_arr[$row[csf('po_id')]][$row[csf('color_size_break_down_id')]]['return_qty']=$row[csf('return_qnty')];
			$ex_return_qty_arr[$row[csf('po_id')]][$row[csf('color_size_break_down_id')]]['return_value']=$ex_return_value;
			$ex_return_qty_arr[$row[csf('po_id')]]['color_size_list'].=$row[csf('color_size_break_down_id')].",";
		}

	   $sql_country_w="SELECT  dm.delivery_company_id, dm.delivery_location_id, m.id, m.po_break_down_id, m.foc_or_claim, m.ex_factory_date, m.ex_factory_qnty as ex_qnty, a.buyer_name,a.DEALING_MARCHANT, a.job_no_prefix_num, a.job_no, a.style_ref_no, a.ship_mode, b.po_number, b.shipment_date, b.pub_shipment_date, b.shiping_status, c.country_ship_date, c.order_quantity, c.order_rate, c.order_total, c.shiping_status as cshiping_status, d.production_qnty, d.production_qnty*c.order_rate as ex_value, c.id as color_size,m.actual_po $delayShortSelect
        from pro_ex_factory_delivery_mst dm, pro_ex_factory_mst m, wo_po_details_master a, wo_po_break_down b, wo_po_color_size_breakdown c,
        pro_ex_factory_dtls d
        where dm.id = m.delivery_mst_id and a.job_no = c.job_no_mst and a.job_no = b.job_no_mst and b.id = c.po_break_down_id and m.id = d.mst_id $sql_cond and d.color_size_break_down_id = c.id  and dm.delivery_company_id<>0 and m.entry_form<>85 and dm.delivery_company_id is not null and dm.status_active = 1 and dm.is_deleted = 0 and a.status_active = 1 and a.is_deleted = 0 and m.status_active = 1 and m.is_deleted = 0 and length(m.actual_po)>0  order by m.id";

		//echo $sql_country_w;die;

	    $country_w_res = sql_select($sql_country_w);
	    $data_buyer_wise = array(); $qntyChkArr = array(); $result_array= array(); $jobPoWiseArr= array(); $poIds='';
	    $actual_po_arr=array();
	    $po_id_arrs=array();
	    foreach($country_w_res as  $row)
	    {
	    	array_push($po_id_arrs, $row[csf('po_break_down_id')]);
	    	$act=explode(",", $row[csf('actual_po')]);
	    	foreach ($act as $key => $act_po)
	    	{
	    		array_push($actual_po_arr, $act_po);
	    	}

		}
		//CLIENT_ID
		$po_id_cond=where_con_using_array(array_filter(array_unique($po_id_arrs)),0,"b.id");
		$client_id_arr=return_library_array( "select a.client_id,b.id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.is_deleted=0 and b.is_deleted=0  $po_id_cond order by id desc", "id", "client_id"  );

	    $actual_po_cond=where_con_using_array(array_filter(array_unique($actual_po_arr)),0,"id");

	    //$sql_actutal_po="SELECT acc_ship_date,po_break_down_id FROM wo_po_acc_po_info where status_active=1 AND is_deleted=0 $actual_po_cond";
	    $actutal_po_data=return_library_array( "select id,acc_ship_date from wo_po_acc_po_info where status_active=1 and is_deleted=0 $actual_po_cond order by id desc", "id", "acc_ship_date"  );

	    foreach($country_w_res as  $row)
	    {
			$poIds.=$row[csf("po_break_down_id")].",";
	        $buffer_time = 0;
	        $client_id=$client_id_arr[$row[csf("po_break_down_id")]];
	        if($buyerArr[$row[csf("buyer_name")]]["buffer_time"])
	        {
	            $buffer_time = "+".$buyerArr[$row[csf('buyer_name')]]['buffer_time']." days";
	        }
			//echo $row[csf("pub_shipment_date")]; die;
			$shipDate=$shippingStatus="";

			$acc_ship_date=
			$expt=explode(",", $row[csf('actual_po')]);
			//echo "<pre>". $actutal_po_data[$expt[0]]."</pre>";
			$time_diff_buffer = strtotime($actutal_po_data[$expt[0]]) - strtotime($row[csf("ex_factory_date")]);


			$time_diff = strtotime($actutal_po_data[$expt[0]]) - strtotime($row[csf("ex_factory_date")]);
			$shipDate=$actutal_po_data[$expt[0]];
			$shippingStatus=$row[csf("shiping_status")];

			$poStr="";

			$poStr=$row[csf("buyer_name")].'__'.$row[csf("job_no")].'__'.$row[csf("style_ref_no")].'__'.$row[csf("po_number")].'__'.$row[csf("ship_mode")].'__'.$shipDate.'__'.$shippingStatus.'__'.$row[csf("po_break_down_id")].'__'.$row[csf("DEALING_MARCHANT")];

	        $data_buyer_wise[$row[csf("buyer_name")]]["ex_qnty"] += $row[csf("production_qnty")];
			$data_buyer_wise[$row[csf("buyer_name")]]["ex_val"] += $row[csf("ex_value")];
			if($row[csf("foc_or_claim")]==2){
				$data_buyer_wise[$row[csf("buyer_name")]]["exClaimQnty"] += $row[csf("production_qnty")];
				//$result_array[$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]][$row[csf("buyer_name")]]['exClaimQnty'] += $row[csf("production_qnty")];
				$result_array[$row[csf("delivery_company_id")]][$client_id][$row[csf("buyer_name")]]['exClaimQnty'] += $row[csf("production_qnty")];
			}

	        if($time_diff_buffer > 0)
	        {
	            $data_buyer_wise[$row[csf("buyer_name")]]["early"] += $row[csf("production_qnty")];
				//$result_array[$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]][$row[csf("buyer_name")]]['early'] += $row[csf("production_qnty")];
				$result_array[$row[csf("delivery_company_id")]][$client_id][$row[csf("buyer_name")]]['early'] += $row[csf("production_qnty")];

				//$jobPoWiseArr[$poStr][$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]]['early']+= $row[csf("production_qnty")];
				$jobPoWiseArr[$poStr][$row[csf("delivery_company_id")]][$client_id]['early']+= $row[csf("production_qnty")];
	        }
	        else if($time_diff_buffer < 0 )
	        {
	            $data_buyer_wise[$row[csf("buyer_name")]]["delay"] += $row[csf("production_qnty")];

				//$result_array[$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]][$row[csf("buyer_name")]]['delay'] += $row[csf("production_qnty")];
				$result_array[$row[csf("delivery_company_id")]][$client_id][$row[csf("buyer_name")]]['delay'] += $row[csf("production_qnty")];

				//$jobPoWiseArr[$poStr][$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]]['delay']+= $row[csf("production_qnty")];
				$jobPoWiseArr[$poStr][$row[csf("delivery_company_id")]][$client_id]['delay']+= $row[csf("production_qnty")];
	        }
	        else if($time_diff_buffer == 0)
	        {
	            $data_buyer_wise[$row[csf("buyer_name")]]["ontime"] += $row[csf("production_qnty")];

				//$result_array[$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]][$row[csf("buyer_name")]]['ontime'] += $row[csf("production_qnty")];
				$result_array[$row[csf("delivery_company_id")]][$client_id][$row[csf("buyer_name")]]['ontime'] += $row[csf("production_qnty")];

				//$jobPoWiseArr[$poStr][$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]]['ontime']+= $row[csf("production_qnty")];
				$jobPoWiseArr[$poStr][$row[csf("delivery_company_id")]][$client_id]['ontime']+= $row[csf("production_qnty")];
	        }
			/*
			$result_array[$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]][$row[csf("buyer_name")]]['ex_qnty'] += $row[csf("production_qnty")];
	        $result_array[$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]][$row[csf("buyer_name")]]['ex_value'] += $row[csf("ex_value")];
	        $result_array[$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]][$row[csf("buyer_name")]]['po_id'] .= $row[csf("po_break_down_id")].",";
	        $result_array[$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]][$row[csf("buyer_name")]]['color_size'] .= $row[csf("color_size")].",";
	        */

	        $result_array[$row[csf("delivery_company_id")]][$client_id][$row[csf("buyer_name")]]['ex_qnty'] += $row[csf("production_qnty")];
	        $result_array[$row[csf("delivery_company_id")]][$client_id][$row[csf("buyer_name")]]['ex_value'] += $row[csf("ex_value")];
	        $result_array[$row[csf("delivery_company_id")]][$client_id][$row[csf("buyer_name")]]['po_id'] .= $row[csf("po_break_down_id")].",";
	        $result_array[$row[csf("delivery_company_id")]][$client_id][$row[csf("buyer_name")]]['color_size'] .= $row[csf("color_size")].",";

			/*
			$jobPoWiseArr[$poStr][$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]]['ex_qnty']+= $row[csf("production_qnty")];
			$jobPoWiseArr[$poStr][$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]]['ex_value']+= $row[csf("ex_value")];
			$jobPoWiseArr[$poStr][$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]]['ex_date']=$row[csf("ex_factory_date")];
			*/

			$jobPoWiseArr[$poStr][$row[csf("delivery_company_id")]][$client_id]['ex_qnty']+= $row[csf("production_qnty")];
			$jobPoWiseArr[$poStr][$row[csf("delivery_company_id")]][$client_id]['ex_value']+= $row[csf("ex_value")];
			$jobPoWiseArr[$poStr][$row[csf("delivery_company_id")]][$client_id]['ex_date']=$row[csf("ex_factory_date")];

			if (!in_array($row[csf("color_size")],$color_sizeTmpArr) )
	        {
				//$jobPoWiseArr[$poStr][$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]]['poQty']+=$row[csf("order_quantity")];
				//$jobPoWiseArr[$poStr][$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]]['poVal']+=$row[csf("order_total")];

				$jobPoWiseArr[$poStr][$row[csf("delivery_company_id")]][$client_id]['poQty']+=$row[csf("order_quantity")];
				$jobPoWiseArr[$poStr][$row[csf("delivery_company_id")]][$client_id]['poVal']+=$row[csf("order_total")];
				$color_sizeTmpArr[]=$row[csf('color_size')];
			}
	    }

		$poIds=implode(",",array_filter(array_unique(explode(",",$poIds))));
		$tot_rows=count(explode(",",$poIds));
		$poIds_country_cond="";

		if($db_type==2 && $tot_rows>1000)
		{
			$poIds_country_cond=" and (";

			$poIdsArr=array_chunk(explode(",",$poIds),999);
			foreach($poIdsArr as $ids)
			{
				$ids=implode(",",$ids);
				$poIds_country_cond.=" a.id in($ids) or ";
			}
			$poIds_country_cond=chop($poIds_country_cond,'or ');
			$poIds_country_cond.=")";
		}
		else
		{
			$poIds_country_cond=" and a.id in ($poIds)";
		}


		$sqlQty="SELECT a.id, a.shipment_date, a.pub_shipment_date, b.country_ship_date, c.production_qnty
	        from wo_po_break_down a, wo_po_color_size_breakdown b, pro_ex_factory_dtls c
	        where a.id = b.po_break_down_id c.color_size_break_down_id = b.id and a.status_active = 1 and a.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 $poIds_country_cond";
		$sqlQty_res = sql_select($sqlQty); $totExQtyArr=array();
		foreach($sqlQty_res as  $row)
		{
			$shipDate="";
			if($cbo_type == 1) $shipDate=$row[csf("pub_shipment_date")];
			else if($cbo_type == 2) $shipDate=$row[csf("country_ship_date")];
			else if($cbo_type == 3) $shipDate=$row[csf("shipment_date")];

			$totExQtyArr[$row[csf("id")]][$shipDate]+=$row[csf("production_qnty")];
		}
		unset($sqlQty_res);
		 /*$sql = "SELECT b.id,dm.delivery_company_id,dm.delivery_location_id, m.ex_factory_qnty as ex_qnty, a.buyer_name,(b.unit_price/a.total_set_qnty) as unit_price,(b.po_quantity*a.total_set_qnty) as po_quantity,
	            ((b.unit_price/a.total_set_qnty)*(b.po_quantity*a.total_set_qnty)) as  po_value $delayShortSelect ,m.foc_or_claim
	            from pro_ex_factory_delivery_mst dm,pro_ex_factory_mst m, wo_po_details_master a, wo_po_break_down b
	            where dm.id = m.delivery_mst_id and a.job_no = b.job_no_mst
	            and b.id= m.po_break_down_id and dm.status_active = 1
	            and dm.is_deleted = 0 and m.status_active = 1 and m.is_deleted = 0 and dm.delivery_company_id <> 0
	            and m.entry_form <> 85 and dm.delivery_company_id is not null
	            $sql_cond
	            order by dm.delivery_company_id , dm.delivery_location_id, a.buyer_name asc ";
			$sql_res = sql_select($sql);
			$summar_arr = array();
			foreach($sql_res as $row)
			{
				$summar_arr[$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]][$row[csf("buyer_name")]]['ex_qnty'] += $row[csf("ex_qnty")];
				$summar_arr[$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]][$row[csf("buyer_name")]]['ex_value'] += $row[csf("po_value")];
				$summar_arr[$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]][$row[csf("buyer_name")]]['po_id'] .= $row[csf("po_break_down_id")].",";
				$summar_arr[$row[csf("delivery_company_id")]][$row[csf("delivery_location_id")]][$row[csf("buyer_name")]]['color_size'] .= $row[csf("color_size")].",";
			}*/



		$invoiceSql = "select a.ID,a.BUYER_ID,a.LOCATION_ID,a.CLAIM_AMMOUNT,b.PO_BREAKDOWN_ID,b.CURRENT_INVOICE_VALUE  from COM_EXPORT_INVOICE_SHIP_MST a, COM_EXPORT_INVOICE_SHIP_DTLS b where a.id=B.MST_ID ".where_con_using_array(explode(",",$poIds),0,'b.PO_BREAKDOWN_ID')."";
		$invoiceSqlResult = sql_select($invoiceSql); $invoiceValue=array();
		foreach($invoiceSqlResult as  $row)
		{
			$client_id=$client_id_arr[$row[PO_BREAKDOWN_ID]];
			$invoiceValue[$client_id][$row[BUYER_ID]]+=$row[CURRENT_INVOICE_VALUE];
			$invoiceClimValue[$client_id][$row[BUYER_ID]][$row[ID]]=$row[CLAIM_AMMOUNT];

			$buyerInvoiceValue[$row[BUYER_ID]]+=$row[CURRENT_INVOICE_VALUE];
			$buyerInvoiceClimValue[$row[BUYER_ID]][$row[ID]]=$row[CLAIM_AMMOUNT];



		}
		unset($invoiceSqlResult);


		ob_start();
		?>
		<div>
			<table style="width:1200px;" border="0" cellpadding="2" cellspacing="0"  >
				<thead>
					<tr>
						<td colspan="10" align="center" style="border:none;font-size:16px; font-weight:bold" ><? echo $companyArr[$cbo_company];?> </td>
					</tr>
					<tr>
						<td colspan="10" align="center" style="font-weight: bold; border: none;">
							<?
							echo strtoupper($report_title);
							if ($date_from != "" && $date_to != "") {
								echo "<br/>From: ".change_date_format($date_from)."  To: ".change_date_format($date_to);
							}
							?>
						</td>
					</tr>
				</thead>
			</table>

			<!-- ========================= Export Summary By Production Factory Start =========================-->

			<fieldset  style="width:1350px; float:left;">
				<table style="width:1330px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" >
					<thead>
						<tr>
							<td colspan="18" align="center" style="font-size:15px; font-weight:bold;">Export Summary By Production Factory</td>
						</tr>
						<tr>
							<th width="110" rowspan="2" style="word-break: break-all;">Production Factory</th>
							<th width="100" rowspan="2" style="word-break: break-all;">Client/SBU</th>
							<th width="110" rowspan="2" style="word-break: break-all;">Buyer</th>
							<th width="70" rowspan="2" style="word-break: break-all;">Export Qty</th>
                            <th width="70" rowspan="2" style="word-break: break-all;">FOB</th>
							<th width="70" rowspan="2" style="word-break: break-all;">Exp Value</th>
                            <th colspan="4" style="word-break: break-all;">Early</th>
							<th colspan="4" style="word-break: break-all;">On Time</th>
							<th colspan="4" style="word-break: break-all;">Delay</th>

						</tr>

                        <tr>
							<th width="70" style="word-break: break-all;">Qty</th>
							<th width="50" style="word-break: break-all;">%</th>
							<th width="70" style="word-break: break-all;">Actual Val</th>
							<th width="50" style="word-break: break-all;">%</th>

							<th width="70" style="word-break: break-all;">Qty</th>
							<th width="50" style="word-break: break-all;">%</th>
							<th width="70" style="word-break: break-all;">Actual Val</th>
							<th width="50" style="word-break: break-all;">%</th>

							<th width="70" style="word-break: break-all;">Qty</th>
							<th width="50">%</th>
							<th width="70" style="word-break: break-all;">Actual Val</th>
							<th width="50" style="word-break: break-all;">%</th>
						</tr>
					</thead>
				</table>
				<div style=" max-height:350px; width:1350px; overflow-y:scroll;" id="scroll_body2">
					<table style="width:1330px" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" >
						<?
						$client_row_span_arr = array();
						$company_row_span_arr = array();
						$client_t=0;
						foreach($result_array as $company_id => $company_data)
						{
							$company_span=0;
							foreach($company_data as $client_id => $client_wise_data)
							{
								$client_t++;
								$buyer_row_span= 0;
								foreach($client_wise_data as $buyer_id => $row)
								{
									$buyer_row_span++;
									$company_span++;
								}
								$client_row_span_arr[$company_id][$client_id] =$buyer_row_span;
							}
							$company_row_span_arr[$company_id]=$company_span;
						}
						//if($client_t>0) $client_t--;

						//print_r($result_array);

						// echo  $buyer_row_span_arr[];
						// echo "<pre>";
						// print_r($result_array);
						// echo "</pre>";

						$i=$m=1;
						$tot_early=0;
						$tot_ontime=0;
						$tot_delay=0;
						foreach($result_array as $company_id => $company_data)
						{
							$com = 1;
							foreach($company_data as $client_id => $client_wise_data)
							{
								$y = 1;

								$sub_early=0;
								$sub_ontime=0;
								$sub_delay=0;
								foreach($client_wise_data as $buyer_id => $row)
								{
									if ($m % 2 == 0) $bgcolor = "#E9F3FF"; else $bgcolor = "#FFFFFF";
									$buyer_td_span = $client_row_span_arr[$company_id][$client_id];

									$com_td_span = $company_row_span_arr[$company_id]+$client_t;
									$po_arr =  array_filter(array_unique(explode(",",chop($row["po_id"],","))));
									$ex_return_qnty = 0;$ex_return_value=0;
									foreach($po_arr as $po_id)
									{
										$color_size_arr = array_filter(array_unique(explode(",",chop($ex_return_qty_arr[$po_id]["color_size_list"],","))));

										foreach($color_size_arr as $color_size_id)
										{
											$ex_return_qnty +=  $ex_return_qty_arr[$po_id][$color_size_id]['return_qty'];
											$ex_return_value +=  $ex_return_qty_arr[$po_id][$color_size_id]['return_value'];
										}
									}
									$ex_qnty_after_return=  $row["ex_qnty"] ;//- $ex_return_qnty;
									$ex_value_after_return=  $row["ex_value"];// - $ex_return_value;
									$FOB = $ex_value_after_return/$ex_qnty_after_return;
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" <? echo $stylecolor; ?> onClick="change_color('tr_<? echo $m; ?>', '<? echo $bgcolor; ?>')" id="tr_<? echo $m; ?>">
									<?
									if($com == 1)
									{
										?>
										<td width="110" style="word-break: break-all;" rowspan="<? echo $com_td_span;?>"><? echo $companyArr[$company_id];?></td>
										<?
									}
									if($y == 1)
									{
										?>
										<td width="100" style="word-break: break-all;" rowspan="<? echo $client_row_span_arr[$company_id][$client_id];?>"><? echo $buyerArr[$client_id]["name"];?></td>
										<?
									}
									$row["po_id"]; $row["color_size"];

									$earlyPer = ($row["early"]/$row["ex_qnty"])*100;
									$onTimePer = ($row["ontime"]/$row["ex_qnty"])*100;
									$delayPer = ($row["delay"]/$row["ex_qnty"])*100;

									$rate=fn_number_format($ex_value_after_return/$ex_qnty_after_return,4);

									?>
										<td width="110" style="word-break: break-all;"><?=$buyerArr[$buyer_id]["name"];?></td>
										<td width="70" style="word-break: break-all;" align="right" title="Return : <?=$ex_return_qnty?>"><?=$ex_qnty_after_return;?></td>

	                                    <td width="70" style="word-break: break-all;" align="right"><?=fn_number_format($FOB, 2);?></td>
										<td width="70" style="word-break: break-all;" align="right"><?=fn_number_format($ex_value_after_return, 0);?></td>

	                                    <td width="70" style="word-break: break-all;" align="right"><?=fn_number_format($row["early"], 0);?></td>
	                                    <td width="50" style="word-break: break-all;" align="right"><?=fn_number_format($earlyPer, 2);?></td>
	                                    <td width="70" style="word-break: break-all;" align="right"><?=fn_number_format($row["early"] * $rate, 0);?></td>
	                                    <td width="50" style="word-break: break-all;" align="right"><?=fn_number_format(($row["early"] * $rate * 100) / $ex_value_after_return, 2);?></td>



	                                    <td width="70" style="word-break: break-all;" align="right"><?=fn_number_format($row["ontime"], 0);?></td>
	                                    <td width="50"  style="word-break: break-all;" align="right"><?=fn_number_format($onTimePer, 2);?></td>
	                                    <td width="70" style="word-break: break-all;" align="right"><?=fn_number_format($row["ontime"] * $rate, 0);?></td>
	                                    <td width="50" style="word-break: break-all;" align="right"><?=fn_number_format(($row["ontime"] * $rate * 100) / $ex_value_after_return, 2);?></td>

	                                    <td width="70" style="word-break: break-all;" align="right"><?=fn_number_format($row["delay"], 0);?></td>
	                                    <td width="50" style="word-break: break-all;" align="right"><?=fn_number_format($delayPer, 2);?></td>
	                                    <td width="70" style="word-break: break-all;" align="right"><?=fn_number_format($row["delay"] * $rate, 0);?></td>
	                                     <td width="50" style="word-break: break-all;" align="right"><?=fn_number_format(($row["delay"] * $rate * 100) / $ex_value_after_return, 2);?></td>



									</tr>
									<?
									$subEarly+=$row["early"];
									$subOntime+=$row["ontime"];
									$subDelay+=$row["delay"];

									$totEarly+=$row["early"];
									$totOntime+=$row["ontime"];
									$totDelay+=$row["delay"];

									$sub_ex_factory_qnty += $ex_qnty_after_return;
									$sub_ex_value += $ex_value_after_return;
									$grand_ex_factory_qnty += $ex_qnty_after_return;
									$grand_ex_value += $ex_value_after_return;

									$sub_invoice_val+=$invoiceValue[$client_id][$buyer_id];
									$sub_difference_val+=$difference_val;
									$sub_claim_val+=array_sum($invoiceClimValue[$client_id][$buyer_id]);

									$grand_invoice_val+=$invoiceValue[$client_id][$buyer_id];
									$grand_difference_val+=$difference_val;
									$grand_claim_val+=array_sum($invoiceClimValue[$client_id][$buyer_id]);

									 $sub_early+=($row["early"]*$rate);
									 $sub_ontime+=($row["ontime"]*$rate);
									 $sub_delay+=($row["delay"]*$rate);
									 $tot_early+=($row["early"]*$rate);
									 $tot_ontime+=($row["ontime"]*$rate);
									 $tot_delay+=($row["delay"]*$rate);

									$y++; $m++;
									$com++;
								}

								?>
								<tr style="background-color:#e0e0e0; font-weight: bold">
	                                <td colspan="2" style="word-break: break-all;" align="right" ><b>Sub Total</b></td>
	                                <td align="right" style="word-break: break-all;"><?=fn_number_format($sub_ex_factory_qnty, 0);?></td>

	                                 <td style="word-break: break-all;">&nbsp;</td>
	                                <td align="right" style="word-break: break-all;"><?=fn_number_format($sub_ex_value, 0);?></td>

	                                <td align="right" style="word-break: break-all;"><?=fn_number_format($subEarly, 0);?></td>
	                                <td align="right" style="word-break: break-all;"><?=fn_number_format(($subEarly / $sub_ex_factory_qnty) * 100, 2);?></td>
	                                <td align="right" style="word-break: break-all;"><?=fn_number_format($sub_early, 0);?></td>
	                                <td align="right" style="word-break: break-all;"><?=fn_number_format(($sub_early * 100) / $sub_ex_value, 2);?></td>

	                                <td align="right" style="word-break: break-all;"><?=fn_number_format($subOntime, 0);?></td>
	                                <td align="right" style="word-break: break-all;"><?=fn_number_format(($subOntime / $sub_ex_factory_qnty) * 100, 2);?></td>
	                                <td align="right" style="word-break: break-all;"><?=fn_number_format($sub_ontime, 0);?></td>
	                                <td align="right" style="word-break: break-all;"><?=fn_number_format(($sub_ontime * 100) / $sub_ex_value, 2);?></td>

	                                <td align="right" style="word-break: break-all;"><?=fn_number_format($subDelay, 0);?></td>
	                                <td align="right" style="word-break: break-all;"><?=fn_number_format(($subDelay / $sub_ex_factory_qnty) * 100, 2);?></td>
	                                <td align="right" style="word-break: break-all;"><?=fn_number_format($sub_delay, 0);?></td>
	                                <td align="right" style="word-break: break-all;"><?=fn_number_format(($sub_delay * 100) / $sub_ex_value, 2);?></td>





								</tr>
								<?
							  $i++;
							   $sub_ex_factory_qnty=$sub_ex_value=$subEarly=$subOntime=$subDelay=0;
							   $sub_invoice_val=$sub_difference_val=$sub_claim_val=0;


							}
						}
						?>
						<tr style="background-color:#e0e0e0; font-weight: bold">
							<td colspan="3" align="right" style="word-break: break-all;"><b>Grand Total</b></td>
							<td align="right" style="word-break: break-all;"><?=fn_number_format($grand_ex_factory_qnty, 0);?></td>

	                        <td>&nbsp;</td>
							<td align="right" style="word-break: break-all;"><?=fn_number_format($grand_ex_value, 0);?></td>

	                        <td align="right" style="word-break: break-all;"><?=fn_number_format($totEarly, 0);?></td>
	                        <td align="right" style="word-break: break-all;"><?=fn_number_format(($totEarly / $grand_ex_factory_qnty) * 100, 2);?></td>
	                        <td align="right" style="word-break: break-all;"><?=fn_number_format($tot_early, 0);?></td>
	                        <td align="right" style="word-break: break-all;"><?=fn_number_format(($tot_early * 100) / $grand_ex_value, 2);?></td>

	                        <td align="right" style="word-break: break-all;"><?=fn_number_format($totOntime, 0);?></td>
	                        <td align="right" style="word-break: break-all;"><?=fn_number_format(($totOntime / $grand_ex_factory_qnty) * 100, 2);?></td>
	                        <td align="right" style="word-break: break-all;"><?=fn_number_format($tot_ontime, 0);?></td>
	                        <td align="right" style="word-break: break-all;"><?=fn_number_format(($tot_ontime * 100) / $grand_ex_value, 2);?></td>

	                        <td align="right" style="word-break: break-all;"><?=fn_number_format($totDelay, 0);?></td>
	                        <td align="right" style="word-break: break-all;"><?=fn_number_format(($totDelay / $grand_ex_factory_qnty) * 100, 2);?></td>
	                        <td align="right" style="word-break: break-all;"><?=fn_number_format($tot_delay, 0);?></td>
	                        <td align="right" style="word-break: break-all;"><?=fn_number_format(($tot_delay * 100) / $grand_ex_value, 2);?></td>


						</tr>
					</table>
				</div>
			</fieldset>


		<?

	}


	$html = ob_get_contents();
	ob_clean();
	foreach (glob("$user_id*.xls") as $filename) {
		@unlink($filename);
	}
	$name = time();
	$filename = $user_id . "_" . $name . ".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc, $html);
	//echo "$html####$filename####$report_type";
	echo $html."####".$filename."####".$report_type;
	exit();
}

if($action == "ex_qnty_popup")
{
    echo load_html_head_contents("Country Order Dtls Info", "../../../", 1, 1,'','','');
    extract($_REQUEST);
    //$color_size = chop($color_size,",");
    $order_data=sql_select( "select b.po_number,b.grouping, b.id from wo_po_color_size_breakdown a, wo_po_break_down b where a.po_break_down_id = b.id  group by b.po_number,b.grouping, b.id"); //and a.id in ($color_size)
	$order_arr=array();
	foreach($order_data as $row){
		$order_arr[$row[csf("id")]]["order_no"]=$row[csf("po_number")];
		$order_arr[$row[csf("id")]]["internal_ref"]=$row[csf("grouping")];
	}
    // $order_arr=return_library_array( "select b.po_number, b.id from wo_po_color_size_breakdown a, wo_po_break_down b where a.po_break_down_id = b.id  group by b.po_number, b.id", "id", "po_number"); //and a.id in ($color_size)

    $country_arr=return_library_array( "select id, country_name from lib_country where status_active = 1 and is_deleted = 0",'id','country_name');
    $color_library=return_library_array( "select id, color_name from lib_color where status_active = 1 and is_deleted = 0", "id", "color_name");
    $size_library=return_library_array( "select id, size_name from lib_size where status_active = 1 and is_deleted = 0", "id", "size_name");
    ?>
    <fieldset style="width:670px; margin-left:3px">
        <div id="report_div" align="center">
            <table border="1" class="rpt_table" rules="all" width="650" cellpadding="0" cellspacing="0" align="center">
                <thead>
                    <th width="30">SL</th>
                    <th width="100">Order No.</th>
                    <th width="100">Internal Ref</th>
                    <th width="100">Country</th>
                    <th width="100">Color</th>
                    <th width="100">Size</th>
                    <th>Export Qnty</th>
                </thead>
                <tbody id="table_body">
                        <?
                        $sql_cond = "";
                        $sql_cond2 = "";$sql_cond3 = "";
                        if ($db_type == 0) {
                            $sql_cond .= " and m.ex_factory_date between '" . change_date_format($date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($date_to, 'yyyy-mm-dd') . "'";
                            $sql_cond2 .= " and b.ex_factory_date between '" . change_date_format($date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($date_to, 'yyyy-mm-dd') . "'";
							 $sql_cond3= " and b.ex_factory_date between '" . change_date_format($date_from, 'yyyy-mm-dd') . "' and '" . change_date_format($date_to, 'yyyy-mm-dd') . "'";
                        } else {
                            $sql_cond .= " and m.ex_factory_date between '" . date("j-M-Y", strtotime($date_from)) . "' and '" . date("j-M-Y", strtotime($date_to)) . "'";
                            $sql_cond2 .= " and b.ex_factory_date between '" . date("j-M-Y", strtotime($date_from)) . "' and '" . date("j-M-Y", strtotime($date_to)) . "'";
							$sql_cond3= " and b.ex_factory_date between '" . date("j-M-Y", strtotime($date_from)) . "' and '" . date("j-M-Y", strtotime($date_to)) . "'";
                        }

                        if ($company) {
                            $sql_cond .= " and dm.delivery_company_id = $company";
                            $sql_cond2 .= " and a.delivery_company_id = $company";
                        }
                        //if ($location) {
                            $sql_cond .= " and dm.delivery_location_id = $location";
                            $sql_cond2 .= " and a.delivery_location_id = $location";
                        //}
                        if ($buyer) {
                            $sql_cond .= " and a.buyer_name = $buyer";
                        }
                        /*if($color_size){
                            $sql_cond .= " and c.id in ($color_size)";
                        }*/
		$sql_res=sql_select("select b.po_break_down_id as po_id,c.color_size_break_down_id, c.production_qnty as return_qnty, d.order_rate
		from pro_ex_factory_mst b, pro_ex_factory_dtls c,  wo_po_color_size_breakdown d
		where b.id = c.mst_id and c.color_size_break_down_id = d.id
		and b.entry_form = 85
		and b.status_active=1 and b.is_deleted=0
		and c.status_active = 1 and c.is_deleted = 0 $sql_cond3");

		$ex_return_qty_arr=array();
		foreach($sql_res as $row)
		{
			$ex_return_value =  $row[csf('return_qnty')]*$row[csf('order_rate')];
			$ex_return_qty_arr[$row[csf('po_id')]][$row[csf('color_size_break_down_id')]]['return_qty']=$row[csf('return_qnty')];
			$ex_return_qty_arr[$row[csf('po_id')]][$row[csf('color_size_break_down_id')]]['return_value']=$ex_return_value;
			$ex_return_qty_arr[$row[csf('po_id')]]['color_size_list'].=$row[csf('color_size_break_down_id')].",";
		}


		$sql = "SELECT c.id as color_size_mst_id,c.country_id,c.po_break_down_id,d.production_qnty as production_qnty,c.size_number_id,c.color_number_id
		from pro_ex_factory_delivery_mst dm,pro_ex_factory_mst m, wo_po_details_master a, wo_po_color_size_breakdown c, pro_ex_factory_dtls d
		where dm.id = m.delivery_mst_id and a.job_no = c.job_no_mst and m.id = d.mst_id
		and d.color_size_break_down_id = c.id and dm.delivery_company_id <> 0 and m.entry_form <> 85 and dm.delivery_company_id is not null
		and dm.status_active = 1 and dm.is_deleted = 0 and a.status_active = 1 and a.is_deleted = 0 and m.status_active = 1 and m.is_deleted = 0
		$sql_cond
		order by c.po_break_down_id,c.country_id";

						   $sql_dtls_miss="SELECT b.id,sum(c.production_qnty) as qnty  from pro_ex_factory_delivery_mst a,pro_ex_factory_mst b,pro_ex_factory_dtls c where a.id=b.delivery_mst_id and b.id=c.mst_id and a.status_active=1 and b.status_active=1 and c.status_active=1 $sql_cond2 group by b.id having sum(c.production_qnty) is null";
                       $dtl_miss_array=array();
                       foreach(sql_select($sql_dtls_miss) as $vals)
                       {
                            $dtl_miss_array[$vals[csf("id")]]=$vals[csf("id")];
                       }
                        $dtl_miss_id=implode(",", $dtl_miss_array);
                        if(!$dtl_miss_id)$dtl_miss_id=0;

                       /* $sql2 = "SELECT 0 as color_number_id,0 as size_number_id,  c.id as po_break_down_id,m.country_id,m.ex_factory_qnty as production_qnty
                        from pro_ex_factory_delivery_mst dm,pro_ex_factory_mst m, wo_po_details_master a, wo_po_break_down c
                        where dm.id = m.delivery_mst_id and a.job_no = c.job_no_mst
                        and m.po_break_down_id = c.id and dm.delivery_company_id <> 0 and m.entry_form <> 85 and dm.delivery_company_id is not null
                        and dm.status_active = 1 and dm.is_deleted = 0 and a.status_active = 1 and a.is_deleted = 0 and m.status_active = 1 and m.is_deleted = 0
                        and c.status_active in(1,2,3) and c.is_deleted = 0 $sql_cond and m.id in( $dtl_miss_id)
                        union
                        SELECT  color_number_id,size_number_id, c.id as po_break_down_id,m.country_id,production_qnty
                        from pro_ex_factory_delivery_mst dm,pro_ex_factory_mst m,pro_ex_factory_dtls d, wo_po_details_master a, wo_po_break_down c ,wo_po_color_size_breakdown e
                        where dm.id = m.delivery_mst_id and a.job_no = c.job_no_mst and m.id=d.mst_id
                        and m.po_break_down_id = c.id and e.id=d.color_size_break_down_id and e.status_active <> 1 and d.status_active=1 and dm.delivery_company_id <> 0 and m.entry_form <> 85 and dm.delivery_company_id is not null
                        and dm.status_active = 1 and dm.is_deleted = 0 and a.status_active = 1 and a.is_deleted = 0 and m.status_active = 1 and m.is_deleted = 0 and c.status_active in(1,2,3) and c.is_deleted = 0 $sql_cond   ";*/
                        //group by c.country_id,c.po_break_down_id
                      //echo $sql2;
                        $result = sql_select($sql);
                        $i = 1;
                        foreach ($result as $row) {
                            if ($i % 2 == 0)
                                $bgcolor = "#E9F3FF";
                            else
                                $bgcolor = "#FFFFFF";
								$size_return_qnty=$ex_return_qty_arr[$row[csf('po_break_down_id')]][$row[csf('color_size_mst_id')]]['return_qty'];
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                <td width="30"><p><? echo $i; ?></p></td>
                                <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $order_arr[$row[csf('po_break_down_id')]]['order_no']; ?></div></td>
                                <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $order_arr[$row[csf('po_break_down_id')]]['internal_ref']; ?></div></td>
                                <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $country_arr[$row[csf('country_id')]]; ?></div></td>
                                <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $color_library[$row[csf('color_number_id')]]; ?></div></td>
                                <td width="100"><div style="word-wrap:break-word; width:100px"><? echo $size_library[$row[csf('size_number_id')]]; ?></div></td>
                                <td align="right" title="<? echo "Return Qty=".$size_return_qnty;?>">
                                    <p>
                                        <?
                                            $production_without_return = $row[csf('production_qnty')] - $size_return_qnty;
                                            echo number_format($production_without_return,2);
                                        ?>
                                    </p>
                                </td>
                            </tr>
                        <?
                        $total += number_format($production_without_return,2,".","");
                        $i++;
                    }
                    ?>
                    </tbody>
                    <tfoot>
                        <tr class="tbl_bottom">
                            <td colspan="6" align="right">Total</td>
                            <td align="right"><? echo $total;?></td>
                        </tr>
                    </tfoot>
            </table>
        </div>
    </fieldset>
    <script type="text/javascript">
        setFilterGrid('table_body',-1);
    </script>
    <?
    exit();
}


if($action == "early_qty_dtls"){
	echo load_html_head_contents("Early Dtls", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	$country_arr=return_library_array( "select id, country_name from lib_country where status_active = 1 and is_deleted = 0",'id','country_name');


	$sql = "SELECT C.COUNTRY_ID,C.COUNTRY_SHIP_DATE,SUM(c.ORDER_QUANTITY) AS PRODUCTION_QNTY
	from wo_po_details_master a, wo_po_color_size_breakdown c
	where a.job_no = c.job_no_mst  and a.status_active = 1 and a.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and c.po_break_down_id=$po_id group by C.COUNTRY_ID,C.COUNTRY_SHIP_DATE";
   $dtl_miss_array=array();
   foreach(sql_select($sql) as $rows)
   {
		$orderDataArr[QTY][$rows[COUNTRY_ID]] = $rows[PRODUCTION_QNTY];
		$orderDataArr[SHIP_DATE][$rows[COUNTRY_ID]] = $rows[COUNTRY_SHIP_DATE];
   }
	 //echo $sql;

	$sql_dtls_miss="SELECT b.ENTRY_FORM,b.COUNTRY_ID,max(b.EX_FACTORY_DATE) as EX_FACTORY_DATE,sum(c.production_qnty) as QTY  from pro_ex_factory_delivery_mst a,pro_ex_factory_mst b,pro_ex_factory_dtls c where a.id=b.delivery_mst_id and b.id=c.mst_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and b.PO_BREAK_DOWN_ID=$po_id group by b.ENTRY_FORM,b.COUNTRY_ID";
   //echo $sql_dtls_miss;

   $dtl_miss_array=array();
   foreach(sql_select($sql_dtls_miss) as $vals)
   {
		$time_diff = strtotime($date) - strtotime($vals[EX_FACTORY_DATE]);
		if($time_diff > 0  && $vals[ENTRY_FORM] !=85 )
        {
            $exfactoryDataArr[QTY][$vals[COUNTRY_ID]]+=$vals[QTY];
            $exfactoryDataArr[EX_FACTORY_DATE][$vals[COUNTRY_ID]]=$vals[EX_FACTORY_DATE];
		}

		if($vals[ENTRY_FORM] ==85 )
        {
            $exfactoryDataArr[RET_QTY][$vals[COUNTRY_ID]]+=$vals[QTY];
		}
   }

?>

    <table width="95%" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" >
        <thead>
            <th>SL</th>
            <th>Country Date</th>
            <th>Ex-Fact.Date</th>
            <th>Country</th>
            <th>Order Qty</th>
            <th>Delv. Qty</th>
            <th>Return Qty</th>
        </thead>
        <?
        $i=1;
        foreach($exfactoryDataArr[QTY] as $country_id=>$val){
        ?>
        <tr>
            <td><?=$i;?></td>
            <td><?=change_date_format($orderDataArr[SHIP_DATE][$country_id]);?></td>
            <td><?=change_date_format($exfactoryDataArr[EX_FACTORY_DATE][$country_id]);?></td>
            <td><?=$country_arr[$country_id];?></td>
            <td align="right"><?=$orderDataArr[QTY][$country_id];?></td>
            <td align="right"><?=$val;?></td>
            <td align="right"><?=$exfactoryDataArr[RET_QTY][$country_id];?></td>
        </tr>
        <?
        $i++;
        }
        ?>
    </table>
<?



}


if($action == "ontime_qty_dtls"){
	echo load_html_head_contents("Early Dtls", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	$country_arr=return_library_array( "select id, country_name from lib_country where status_active = 1 and is_deleted = 0",'id','country_name');

	$buyer_res = sql_select("select id, buyer_name, delivery_buffer_days from lib_buyer  where status_active = 1 and is_deleted = 0");
	foreach($buyer_res as $b_row)
	{
		$buyerArr[$b_row[csf("id")]]["name"] = $b_row[csf("buyer_name")];
		$buyerArr[$b_row[csf("id")]]["buffer_time"] = $b_row[csf("delivery_buffer_days")];
	}



	$sql = "SELECT C.COUNTRY_ID,C.COUNTRY_SHIP_DATE,SUM(c.ORDER_QUANTITY) AS PRODUCTION_QNTY
	from wo_po_details_master a, wo_po_color_size_breakdown c
	where a.job_no = c.job_no_mst  and a.status_active = 1 and a.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and c.po_break_down_id=$po_id group by C.COUNTRY_ID,C.COUNTRY_SHIP_DATE";
   $dtl_miss_array=array();
   foreach(sql_select($sql) as $rows)
   {
		$orderDataArr[QTY][$rows[COUNTRY_ID]] = $rows[PRODUCTION_QNTY];
		$orderDataArr[SHIP_DATE][$rows[COUNTRY_ID]] = $rows[COUNTRY_SHIP_DATE];
   }
	 //echo $sql;

	$sql_dtls_miss="SELECT a.BUYER_ID,b.ENTRY_FORM,b.COUNTRY_ID,max(b.EX_FACTORY_DATE) as EX_FACTORY_DATE,sum(c.production_qnty) as QTY  from pro_ex_factory_delivery_mst a,pro_ex_factory_mst b,pro_ex_factory_dtls c where a.id=b.delivery_mst_id and b.id=c.mst_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and b.PO_BREAK_DOWN_ID=$po_id group by a.BUYER_ID,b.ENTRY_FORM,b.COUNTRY_ID";
    //echo $sql_dtls_miss;

   $dtl_miss_array=array();
   foreach(sql_select($sql_dtls_miss) as $vals)
   {
		$time_diff = strtotime($date) - strtotime($vals[EX_FACTORY_DATE]);

		//echo $date.'='.$vals[EX_FACTORY_DATE];

        if($buyerArr[$vals[BUYER_ID]]["buffer_time"])
        {
            $buffer_time = "+".$buyerArr[$vals[BUYER_ID]]['buffer_time']." days";
        }

		$time_diff_buffer = strtotime($date . $buffer_time) - strtotime($vals[EX_FACTORY_DATE]);

		if($time_diff <= 0 && $time_diff_buffer >= 0  && $vals[ENTRY_FORM] !=85 )
        {
            $exfactoryDataArr[QTY][$vals[COUNTRY_ID]]+=$vals[QTY];
            $exfactoryDataArr[EX_FACTORY_DATE][$vals[COUNTRY_ID]]=$vals[EX_FACTORY_DATE];
		}

		if($vals[ENTRY_FORM] ==85 )
        {
            $exfactoryDataArr[RET_QTY][$vals[COUNTRY_ID]]+=$vals[QTY];
		}
   }

?>

    <table width="95%" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" >
        <thead>
            <th>SL</th>
            <th>Country Date</th>
            <th>Ex-Fact.Date</th>
            <th>Country</th>
            <th>Order Qty</th>
            <th>Delv. Qty</th>
            <th>Return Qty</th>
        </thead>
        <?
        $i=1;
        foreach($exfactoryDataArr[QTY] as $country_id=>$val){
        ?>
        <tr>
            <td><?=$i;?></td>
            <td><?=change_date_format($orderDataArr[SHIP_DATE][$country_id]);?></td>
            <td><?=change_date_format($exfactoryDataArr[EX_FACTORY_DATE][$country_id]);?></td>
            <td><?=$country_arr[$country_id];?></td>
            <td align="right"><?=$orderDataArr[QTY][$country_id];?></td>
            <td align="right"><?=$val;?></td>
            <td align="right"><?=$exfactoryDataArr[RET_QTY][$country_id];?></td>
        </tr>
        <?
        $i++;
        }
        ?>
    </table>
<?



}

if($action == "delay_qty_dtls"){
	echo load_html_head_contents("Early Dtls", "../../../", 1, 1,'','','');
	extract($_REQUEST);

	$country_arr=return_library_array( "select id, country_name from lib_country where status_active = 1 and is_deleted = 0",'id','country_name');


	$sql = "SELECT C.COUNTRY_ID,C.COUNTRY_SHIP_DATE,SUM(c.ORDER_QUANTITY) AS PRODUCTION_QNTY
	from wo_po_details_master a, wo_po_color_size_breakdown c
	where a.job_no = c.job_no_mst  and a.status_active = 1 and a.is_deleted = 0 and c.status_active = 1 and c.is_deleted = 0 and c.po_break_down_id=$po_id group by C.COUNTRY_ID,C.COUNTRY_SHIP_DATE";
   $dtl_miss_array=array();
   foreach(sql_select($sql) as $rows)
   {
		$orderDataArr[QTY][$rows[COUNTRY_ID]] = $rows[PRODUCTION_QNTY];
		$orderDataArr[SHIP_DATE][$rows[COUNTRY_ID]] = $rows[COUNTRY_SHIP_DATE];
   }
	  //echo $sql;

	$sql_dtls_miss="SELECT b.ENTRY_FORM,b.COUNTRY_ID,max(b.EX_FACTORY_DATE) as EX_FACTORY_DATE,sum(c.production_qnty) as QTY  from pro_ex_factory_delivery_mst a,pro_ex_factory_mst b,pro_ex_factory_dtls c where a.id=b.delivery_mst_id and b.id=c.mst_id and a.status_active=1 and b.status_active=1 and c.status_active=1 and b.PO_BREAK_DOWN_ID=$po_id group by b.ENTRY_FORM,b.COUNTRY_ID";
   //echo $sql_dtls_miss;

   $dtl_miss_array=array();
   foreach(sql_select($sql_dtls_miss) as $vals)
   {
		$time_diff = strtotime($date) - strtotime($vals[EX_FACTORY_DATE]);
		if($time_diff < 0  && $vals[ENTRY_FORM] !=85 )
        {
            $exfactoryDataArr[QTY][$vals[COUNTRY_ID]]+=$vals[QTY];
            $exfactoryDataArr[EX_FACTORY_DATE][$vals[COUNTRY_ID]]=$vals[EX_FACTORY_DATE];
		}

		if($vals[ENTRY_FORM] ==85 )
        {
            $exfactoryDataArr[RET_QTY][$vals[COUNTRY_ID]]+=$vals[QTY];
		}
   }

?>

    <table width="95%" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" >
        <thead>
            <th>SL</th>
            <th>Country Date</th>
            <th>Ex-Fact.Date</th>
            <th>Country</th>
            <th>Order Qty</th>
            <th>Delv. Qty</th>
            <th>Return Qty</th>
        </thead>
        <?
        $i=1;
        foreach($exfactoryDataArr[QTY] as $country_id=>$val){
        ?>
        <tr>
            <td><?=$i;?></td>
            <td><?=change_date_format($orderDataArr[SHIP_DATE][$country_id]);?></td>
            <td><?=change_date_format($exfactoryDataArr[EX_FACTORY_DATE][$country_id]);?></td>
            <td><?=$country_arr[$country_id];?></td>
            <td align="right"><?=$orderDataArr[QTY][$country_id];?></td>
            <td align="right"><?=$val;?></td>
            <td align="right"><?=$exfactoryDataArr[RET_QTY][$country_id];?></td>
        </tr>
        <?
        $i++;
        }
        ?>
    </table>
<?



}





?>