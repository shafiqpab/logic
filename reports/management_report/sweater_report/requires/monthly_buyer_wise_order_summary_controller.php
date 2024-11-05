<?
/*-------------------------------------------- Comments
Version                  :   V1
Purpose			         : 	This form will create  Monthly Capacity and order qty Report
Functionality	         :
JS Functions	         :
Created by		         :	Saidul Islam
Creation date 	         :  05 July,2015
Requirment Client        :
Requirment By            :
Requirment type          :
Requirment               :
Affected page            :
Affected Code            :
DB Script                :
Updated by 		         :
Update date		         :
QC Performed BY	         :
QC Date			         :
Comments		         : From this version oracle conversion is start
*/

include('../../../../includes/common.php');
session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');

$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
$buyer_short_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
$team_leader_arr=return_library_array( "select id, team_leader_name from lib_marketing_team",'id','team_leader_name');

if($action=="load_report_format")
{
	$print_report_format=return_field_value("format_id","lib_report_template","template_name ='".$data."' and module_id=11 and report_id in(44) and is_deleted=0 and status_active=1");
 	echo trim($print_report_format);
	exit();

}

if($action=="report_generate")
{
	ob_start();
	extract($_REQUEST);
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_year_name=str_replace("'","",$cbo_year_name);
	$cbo_month=str_replace("'","",$cbo_month);
	$cbo_month_end=str_replace("'","",$cbo_month_end);
	$cbo_end_year_name=str_replace("'","",$cbo_end_year_name);
	$cbo_style_owner=str_replace("'","",$cbo_style_owner);

	if($cbo_company_name==0) $company_conds=""; else $company_conds=" and a.company_name in($cbo_company_name) ";
	if($cbo_style_owner==0) $style_owner_cond=""; else $style_owner_cond=" and a.style_owner in($cbo_style_owner) ";

	$sql_data_smv=sql_select("select comapny_id,year, basic_smv from lib_capacity_calc_mst where year between $cbo_year_name and $cbo_end_year_name");
	foreach( $sql_data_smv as $row)
	{
		$basic_smv_arr[$row[csf("comapny_id")]][$row[csf("year")]]=$row[csf("basic_smv")];
	}

	$daysinmonth=cal_days_in_month(CAL_GREGORIAN, $cbo_month_end, $cbo_year_name);
	$s_date=$cbo_year_name."-".$cbo_month."-"."01";
	$e_date=$cbo_end_year_name."-".$cbo_month_end."-".$daysinmonth;
	if($db_type==2)
	{
		$s_date=change_date_format($s_date,'yyyy-mm-dd','-',1);
		$e_date=change_date_format($e_date,'yyyy-mm-dd','-',1);
	}


	$tot_month = datediff( 'm', $s_date,$e_date);
	for($i=0; $i<= $tot_month; $i++ )
	{
		$next_month=month_add($s_date,$i);
		$month_arr[]=date("Y-m",strtotime($next_month));
	}

  	if($type==3 || $type==4) // country ship query
	{
		 $sql_con_po="SELECT a.buyer_name, a.team_leader, b.country_ship_date as shipment_date, b.order_quantity as po_quantity, b.order_rate as unit_price, b.order_total as po_total_price, c.buyer_name as buyer_full from wo_po_details_master a, wo_po_color_size_breakdown b, lib_buyer c where a.job_no=b.job_no_mst and b.country_ship_date between '$s_date' and '$e_date' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.buyer_name=c.id and a.garments_nature =100  $company_conds $style_owner_cond order by a.team_leader asc";
	}
	else
	{
		$sql_con_po="SELECT a.buyer_name, a.team_leader, b.shipment_date, (b.po_quantity*a.total_set_qnty) as po_quantity, b.unit_price, b.po_total_price, c.buyer_name as buyer_full FROM wo_po_details_master a, wo_po_break_down b, lib_buyer c WHERE a.job_no=b.job_no_mst  AND b.shipment_date between '$s_date' and '$e_date' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.buyer_name=c.id and a.garments_nature =100  $company_conds $style_owner_cond order by a.team_leader asc";
	}
	//echo $sql_con_po;
	$sql_data_po=sql_select($sql_con_po); $buyer_sum_arr=array(); $team_ledr_arr=array();
	foreach( $sql_data_po as $row_po)
	{
		$date_key=date("Y-m",strtotime($row_po[csf("shipment_date")]));
		$po_qnty_as_buyer[$date_key][$row_po[csf("buyer_name")]]+=$row_po[csf("po_quantity")];
		$po_val_as_buyer[$date_key][$row_po[csf("buyer_name")]]+=$row_po[csf("po_total_price")];
		$buyer_sum_arr[$row_po[csf("buyer_name")]]=$row_po[csf("buyer_full")];
		$team_ledr_arr[$row_po[csf("buyer_name")]].=','.$row_po[csf("team_leader")];
		$buyer_summery[$row_po[csf("buyer_name")]]['qty'] +=$row_po[csf("po_quantity")];
		$buyer_summery[$row_po[csf("buyer_name")]]['price'] +=$row_po[csf("po_total_price")];
	}

	if($type==1)
	{
		$buyerQtyData='[';
		$buyerValData='[';
		foreach($buyer_summery as $buyer=>$data):
			$buyerQtyData.="{name: '".$buyer_short_arr[$buyer]."',y: ".$data['qty']."},";
			$buyerValData.="{name: '".$buyer_short_arr[$buyer]."',y: ".$data['price']."},";
		endforeach;
		$buyerQtyData=rtrim($buyerQtyData,',');
		$buyerValData=rtrim($buyerValData,',');
		$buyerQtyData.=']';
		$buyerValData.=']';
		?>
			<table border="0" style="margin-top:10px;">
		 <tr>
		 <td width="450" valign="top">
			<fieldset style="margin-right:5px; width:450px;">
			 <div id="container6000"></div>
			 <script>hs_chart(6000,<? echo $buyerValData;?>,'Value');</script>
			 </fieldset>
		 </td>
		 <td>
		 <fieldset>
			<div style="430px; text-align:center; margin:5px 0;">
				  <h2>Summary:</h2>
			 </div>
			<table cellspacing="0" width="430"  border="1" rules="all" class="rpt_table" >
				<thead>
					<th width="40">SL</th>
					<th width="120" align="center">Buyer</th>
					<th width="80" align="center">PO Qty (Pcs)</th>
					<th width="80" align="center">Avg. Rate</th>
					<th align="center">Po Value ($)</th>
				</thead>
				<tbody>
			<?
				$i=1; $tot_qty=$tot_val=0;
				foreach($buyer_summery as $buyer_id=>$data)
				{
					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					$buyer_rate =  $data['price']/$data['qty'];
					$buyer_val = $data['price'];
					$tot_qty+=$data['qty'];
					$tot_val+=$data['price'];
					$grand_tot_qty+=$data['qty'];
					$grand_tot_val+=$buyer_val;
				?>
					<tr bgcolor="<? echo $bgcolor ; ?>" onclick="change_color('tr_<? echo $y.$m.$i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $y.$m.$i; ?>">
						<td width="40" align="center"><? echo $i; ?></td>
						<td align="left" width="120"><p><? echo $buyer_arr[$buyer_id]; ?></p></td>
						<td align="right" width="80"><? echo $data['qty']; ?></td>
						<td align="right" width="80"><? echo number_format($buyer_rate,4);?></td>
						<td align="right"><? echo number_format($buyer_val,2);?></td>
					</tr>
				<?
				$i++;
				}
				//echo "select id,capacity_in_value,currency_id from  variable_settings_commercial where company_name in($cbo_company_name) and variable_list=5 order by id";
				$nameArray=sql_select( "select sum(capacity_in_value) as capacity_in_value  from  variable_settings_commercial where company_name in($cbo_company_name) and variable_list=5 order by id" );
			?>
			   </tbody>
			   <tfoot>
					<tr>
						<th colspan="2">Total </th>
						<th align="right" width="80"><? echo $tot_qty;?></th>
						<th align="right" width="80"><? echo number_format($tot_val/$tot_qty,4); ?></th>
						<th align="right" width="104"><? echo number_format($tot_val,2);?></th>
					</tr>
					<tr>
						<th colspan="4">Capacity Value </th>
						<th align="right" width="104"><? echo number_format($nameArray[0][csf('capacity_in_value')],2);?></th>
					</tr>
					<tr>
						<th colspan="4">Balance Value </th>
						<th align="right" width="104"><? echo number_format($nameArray[0][csf('capacity_in_value')]-$tot_val,2);?></th>
					</tr>
					<tr>
						<th colspan="4">Capacity Booked(%) </th>
						<th align="right" width="104"><? echo number_format(($tot_val/$nameArray[0][csf('capacity_in_value')])*100,2);?></th>
					</tr>
					<tr>
						<th colspan="4">Status </th>
						<th width="104">
						<?
						if($tot_val<$nameArray[0][csf('capacity_in_value')])
						{
							echo "<div style='background:#093; color:#000; text-align:center;'> Under Booked </div>";
						}
						else
						{
							echo "<div style='background:#F00; color:#000; text-align:center;'> Over Booked </div>";
						}
						?>
						</th>
					</tr>
				</tfoot>
			</table>
		  </fieldset>
		  </td>
		 <td width="450" valign="top">
			<fieldset style="margin-left:5px; width:450px;">
				<div id="container7000"></div>
				<script>hs_chart(7000,<? echo $buyerQtyData;?>,'Qty');</script>
			</fieldset>
		  </td>
		  </tr>
		</table>
		<?
		$grand_tot_qty=$grand_tot_val=0;
		foreach($month_arr as $month){
		list($y,$m)=explode('-',$month); $m=$m*1;

		$cQtyData='[';
		$cValData='[';
		foreach($po_qnty_as_buyer[$month] as $buyer=>$qty):
			$cQtyData.="{name: '".$buyer_short_arr[$buyer]."',y: $qty},";
			$cValData.="{name: '".$buyer_short_arr[$buyer]."',y: ".$po_val_as_buyer[$month][$buyer]."},";
		endforeach;
		$cQtyData=rtrim($cQtyData,',');
		$cValData=rtrim($cValData,',');
		$cQtyData.=']';
		$cValData.=']';


		?>


		<table border="0" style="margin-top:10px;">
		 <tr>
		 <td width="450" valign="top">
			<fieldset style="margin-right:5px; width:450px;">
			 <div id="container<? echo $y.$m;?>1"></div>
			 <script>hs_chart(<? echo $y.$m;?>1,<? echo $cValData;?>,'Value');</script>
			 </fieldset>
		 </td>
		 <td>
		 <fieldset>
			<div style="430px; text-align:center; margin:5px 0;">
				  <h2>Summary: <? echo $months[$m].', '.$y; ?></h2>
			 </div>
			<table cellspacing="0" width="430"  border="1" rules="all" class="rpt_table" >
				<thead>
					<th width="40">SL</th>
					<th width="120" align="center">Buyer</th>
					<th width="80" align="center">PO Qty (Pcs)</th>
					<th width="80" align="center">Avg. Rate</th>
					<th align="center">Po Value ($)</th>
				</thead>
				<tbody>
			<?
				$i=1; $tot_qty=$tot_val=0;
				foreach($po_qnty_as_buyer[$month] as $buyer_id=>$buyer_qty)
				{
					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					$buyer_rate =  $po_val_as_buyer[$month][$buyer_id]/$buyer_qty;
					$buyer_val = $po_val_as_buyer[$month][$buyer_id];
					$tot_qty+=$buyer_qty;
					$tot_val+=$buyer_val;
					$grand_tot_qty+=$buyer_qty;
					$grand_tot_val+=$buyer_val;
				?>
					<tr bgcolor="<? echo $bgcolor ; ?>" onclick="change_color('tr_<? echo $y.$m.$i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $y.$m.$i; ?>">
						<td width="40" align="center"><? echo $i; ?></td>
						<td align="left" width="120"><p><? echo $buyer_arr[$buyer_id]; ?></p></td>
						<td align="right" width="80"><? echo $buyer_qty; ?></td>
						<td align="right" width="80"><? echo number_format($buyer_rate,4);?></td>
						<td align="right"><? echo number_format($buyer_val,2);?></td>
					</tr>
				<?
				$i++;
				}
				//echo "select id,capacity_in_value,currency_id from  variable_settings_commercial where company_name in($cbo_company_name) and variable_list=5 order by id";
				$nameArray=sql_select( "select sum(capacity_in_value) as capacity_in_value  from  variable_settings_commercial where company_name in($cbo_company_name) and variable_list=5 order by id" );
			?>
			   </tbody>
			   <tfoot>
					<tr>
						<th colspan="2">Total </th>
						<th align="right" width="80"><? echo $tot_qty;?></th>
						<th align="right" width="80"><? echo number_format($tot_val/$tot_qty,4); ?></th>
						<th align="right" width="104"><? echo number_format($tot_val,2);?></th>
					</tr>
					<tr>
						<th colspan="4">Capacity Value </th>
						<th align="right" width="104"><? echo number_format($nameArray[0][csf('capacity_in_value')],2);?></th>
					</tr>
					<tr>
						<th colspan="4">Balance Value </th>
						<th align="right" width="104"><? echo number_format($nameArray[0][csf('capacity_in_value')]-$tot_val,2);?></th>
					</tr>
					<tr>
						<th colspan="4">Capacity Booked(%) </th>
						<th align="right" width="104"><? echo number_format(($tot_val/$nameArray[0][csf('capacity_in_value')])*100,2);?></th>
					</tr>
					<tr>
						<th colspan="4">Status </th>
						<th width="104">
						<?
						if($tot_val<$nameArray[0][csf('capacity_in_value')])
						{
							echo "<div style='background:#093; color:#000; text-align:center;'> Under Booked </div>";
						}
						else
						{
							echo "<div style='background:#F00; color:#000; text-align:center;'> Over Booked </div>";
						}
						?>
						</th>
					</tr>
				</tfoot>
			</table>
		  </fieldset>
		  </td>
		  <td width="450" valign="top">
			<fieldset style="margin-left:5px; width:450px;">
				<div id="container<? echo $y.$m;?>2"></div>
				<script>hs_chart(<? echo $y.$m;?>2,<? echo $cQtyData;?>,'Qty');</script>
			</fieldset>
		  </td>
		  </tr>
		</table>
		<?
		}
	}
	else if($type==2)
	{
		$month_count=count($month_arr);
		$tbl_width=590+($month_count*200);
		?>
        <div align="left">
        <table width="<? echo $tbl_width; ?>" border="1" cellpadding="2" cellspacing="0"  id="caption" rules="all">
            <thead>
                <tr class="form_caption" style="border:none;">
                    <td colspan="<? echo $month_count+4; ?>" align="center" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?></td>
                </tr>
                <tr class="form_caption" style="border:none;">
                    <td colspan="<? echo $month_count+4; ?>" align="center" style="border:none; font-size:14px;">
                       <b>Company Name : <? echo $companyArr[str_replace("'","",$cbo_company_name)]; ?></b>
                    </td>
                </tr>
            </thead>
        </table>
        <table cellspacing="0" width="<? echo $tbl_width; ?>" border="1" rules="all" class="rpt_table" style="font-size:10px">
            <thead>
            	<tr>
                    <th rowspan="2" width="30">SL</th>
                    <th rowspan="2" width="130" align="center">Buyer</th>
                    <th rowspan="2" width="160" align="center">Team Leader</th>
                    <?
                        foreach($month_arr as $month_id)
                        {
							$ex_month='';
							$ex_month=explode('-',$month_id);
							$monthId=0;
							if($ex_month[1]==10)
							{
								$monthId=$ex_month[1];
							}
							else
							{
								$monthId=str_replace('0','',$ex_month[1]);
							}
                    ?>
                        <th colspan="2"><? echo $months[$monthId].', '.$ex_month[0]; ?></th>
                    <? } ?>

                    <th colspan="2">Buyer Total</th>
                </tr>
                <tr>
                    <?
                        foreach($month_arr as $month_id)
                        {
                    ?>
                        <th width="95">Qty (Pcs)</th>
                        <th width="100">Value (Usd)</th>
                    <? } ?>

                    <th width="110">Qty (Pcs)</th>
                    <th>Value (Usd)</th>
                </tr>
            </thead>
         </table>

            <div style="width:<? echo $tbl_width+17; ?>px; max-height:350px; overflow-y:scroll" id="scroll_body" >
		<table width="<? echo $tbl_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
            <? $i=1; $tot_buyer_qty=0; $tot_buyer_val=0; $column_tot_qty_val_arr=array();
			foreach($buyer_sum_arr as $buyer_id=>$buyer_name)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="30" ><? echo $i; ?></td>
                    <td width="130" ><? echo $buyer_name; ?></td>
                    <td width="160" ><?
					$team_leader="";
					$ex_tmLdr=array_filter(array_unique(explode(',',$team_ledr_arr[$buyer_id])));
					foreach($ex_tmLdr as $tm_ldr_id)
					{
						if($team_leader=="") $team_leader=$team_leader_arr[$tm_ldr_id]; else $team_leader.=', '.$team_leader_arr[$tm_ldr_id];
					}

					echo $team_leader; ?></td>
                    <?
						$row_qty=0; $row_val=0;
                        foreach($month_arr as $month_id)
                        {
							$buyer_qty =0; $buyer_val =0;
							$buyer_qty = $po_qnty_as_buyer[$month_id][$buyer_id];
							$buyer_val = $po_val_as_buyer[$month_id][$buyer_id];
							$row_qty+=$buyer_qty;
							$row_val+=$buyer_val;

							$column_tot_qty_val_arr[$month_id]['qty']+=$buyer_qty;
							$column_tot_qty_val_arr[$month_id]['val']+=$buyer_val;
						?>
						   <td width="95" align="right"><? echo number_format($buyer_qty,0,'',','); ?></td>
						   <td width="100" align="right"><? echo number_format($buyer_val,2); ?></td>
                    <? } ?>
                    <td width="110" align="right"><? echo number_format($row_qty,0,'',','); ?></td>
                    <td align="right"><? echo number_format($row_val,2); ?></td>
                </tr>
                <?
				$tot_buyer_qty+=$row_qty;
				$tot_buyer_val+=$row_val;
				$i++;
			}
			?>
            </table>
        </div>
        	<table cellspacing="0" width="<? echo $tbl_width; ?>" border="1" rules="all" class="tbl_bottom">
            	<tr>
                	<td width="30">&nbsp;</td>
                    <td width="130">&nbsp;</td>
                    <td width="160">Total</td>
                    <?
                    foreach($month_arr as $monthid)
					{
						?>
						   <td width="95" align="right"><? echo number_format($column_tot_qty_val_arr[$monthid]['qty'],0,'',','); ?></td>
						   <td width="100" align="right"><? echo number_format($column_tot_qty_val_arr[$monthid]['val'],2); ?></td>
                    	<?
					}
					?>
                    <td width="110" align="right"><? echo number_format($tot_buyer_qty,0,'',','); ?></td>
					<td align="right"><? echo number_format($tot_buyer_val,2); ?></td>
                </tr>
            </table>
    	</div>
        <?
	}
	else if($type==3) // country ship date
	{
		$grand_tot_qty=$grand_tot_val=0;
		foreach($month_arr as $month){
		list($y,$m)=explode('-',$month); $m=$m*1;

		$cQtyData='[';
		$cValData='[';
		foreach($po_qnty_as_buyer[$month] as $buyer=>$qty):
			$cQtyData.="{name: '".$buyer_short_arr[$buyer]."',y: $qty},";
			$cValData.="{name: '".$buyer_short_arr[$buyer]."',y: ".$po_val_as_buyer[$month][$buyer]."},";
		endforeach;
		$cQtyData=rtrim($cQtyData,',');
		$cValData=rtrim($cValData,',');
		$cQtyData.=']';
		$cValData.=']';


	?>

		<table border="0" style="margin-top:10px;">
		 <tr>
		 <td width="450" valign="top">
			<fieldset style="margin-right:5px; width:450px;">
			 <div id="container<? echo $y.$m;?>1"></div>
			 <script>hs_chart(<? echo $y.$m;?>1,<? echo $cValData;?>,'Value');</script>
			 </fieldset>
		 </td>
		 <td>
		 <fieldset>
			<div style="430px; text-align:center; margin:5px 0;">
				  <h2>Summary: <? echo $months[$m].', '.$y; ?></h2>
			 </div>
			<table cellspacing="0" width="430"  border="1" rules="all" class="rpt_table" >
				<thead>
					<th width="40">SL</th>
					<th width="120" align="center">Buyer</th>
					<th width="80" align="center">PO Qty (Pcs)</th>
					<th width="80" align="center">Avg. Rate</th>
					<th align="center">Po Value ($)</th>
				</thead>
				<tbody>
			<?
				$i=1; $tot_qty=$tot_val=0;
				foreach($po_qnty_as_buyer[$month] as $buyer_id=>$buyer_qty)
				{
					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					$buyer_rate =  $po_val_as_buyer[$month][$buyer_id]/$buyer_qty;
					$buyer_val = $po_val_as_buyer[$month][$buyer_id];
					$tot_qty+=$buyer_qty;
					$tot_val+=$buyer_val;
					$grand_tot_qty+=$buyer_qty;
					$grand_tot_val+=$buyer_val;
				?>
					<tr bgcolor="<? echo $bgcolor ; ?>" onclick="change_color('tr_<? echo $y.$m.$i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $y.$m.$i; ?>">
						<td width="40" align="center"><? echo $i; ?></td>
						<td align="left" width="120"><p><? echo $buyer_arr[$buyer_id]; ?></p></td>
						<td align="right" width="80"><? echo $buyer_qty; ?></td>
						<td align="right" width="80"><? echo number_format($buyer_rate,4);?></td>
						<td align="right"><? echo number_format($buyer_val,2);?></td>
					</tr>
				<?
				$i++;
				}
				if($cbo_company_name>0)
				{
				  	$nameArray=sql_select( "select sum(capacity_in_value) as  capacity_in_value from  variable_settings_commercial where company_name in($cbo_company_name) and variable_list=5 order by id" );
				}
				else
				{
					$nameArray=sql_select( "select sum(capacity_in_value) as capacity_in_value  from  variable_settings_commercial where company_name in($cbo_style_owner) and variable_list=5 order by id");
				}
			?>
			   </tbody>
			   <tfoot>
					<tr>
						<th colspan="2">Total </th>
						<th align="right" width="80"><? echo $tot_qty;?></th>
						<th align="right" width="80"><? echo number_format($tot_val/$tot_qty,4); ?></th>
						<th align="right" width="104"><? echo number_format($tot_val,2);?></th>
					</tr>
					<tr>
						<th colspan="4">Capacity Value </th>
						<th align="right" width="104"><? echo number_format($nameArray[0][csf('capacity_in_value')],2);?></th>
					</tr>
					<tr>
						<th colspan="4">Balance Value </th>
						<th align="right" width="104"><? echo number_format($nameArray[0][csf('capacity_in_value')]-$tot_val,2);?></th>
					</tr>
					<tr>
						<th colspan="4">Capacity Booked(%) </th>
						<th align="right" width="104"><? echo number_format(($tot_val/$nameArray[0][csf('capacity_in_value')])*100,2);?></th>
					</tr>
					<tr>
						<th colspan="4">Status </th>
						<th width="104">
						<?
						if($tot_val<$nameArray[0][csf('capacity_in_value')])
						{
							echo "<div style='background:#093; color:#000; text-align:center;'> Under Booked </div>";
						}
						else
						{
							echo "<div style='background:#F00; color:#000; text-align:center;'> Over Booked </div>";
						}
						?>
						</th>
					</tr>
				</tfoot>
			</table>
		  </fieldset>
		  </td>
		  <td width="450" valign="top">
			<fieldset style="margin-left:5px; width:450px;">
				<div id="container<? echo $y.$m;?>2"></div>
				<script>hs_chart(<? echo $y.$m;?>2,<? echo $cQtyData;?>,'Qty');</script>
			</fieldset>
		  </td>
		  </tr>
		</table>
		<?
		}
	}
	else if($type==4) // summery country ship date
	{
		$month_count=count($month_arr);
		$tbl_width=590+($month_count*200);
		?>
        <div align="left">
        <table width="<? echo $tbl_width; ?>" border="1" cellpadding="2" cellspacing="0"  id="caption" rules="all">
            <thead>
                <tr class="form_caption" style="border:none;">
                    <td colspan="<? echo $month_count+4; ?>" align="center" style="border:none;font-size:16px; font-weight:bold" ><? echo $report_title; ?></td>
                </tr>
                <tr class="form_caption" style="border:none;">
                    <td colspan="<? echo $month_count+4; ?>" align="center" style="border:none; font-size:14px;">
                       <b>Company Name : <? echo $companyArr[str_replace("'","",$cbo_company_name)]; ?></b>
                    </td>
                </tr>
            </thead>
        </table>
        <table cellspacing="0" width="<? echo $tbl_width; ?>" border="1" rules="all" class="rpt_table" style="font-size:10px">
            <thead>
            	<tr>
                    <th rowspan="2" width="30">SL</th>
                    <th rowspan="2" width="130" align="center">Buyer</th>
                    <th rowspan="2" width="160" align="center">Team Leader</th>
                    <?
                        foreach($month_arr as $month_id)
                        {
							$ex_month='';
							$ex_month=explode('-',$month_id);
							$monthId=0;
							if($ex_month[1]==10)
							{
								$monthId=$ex_month[1];
							}
							else
							{
								$monthId=str_replace('0','',$ex_month[1]);
							}
                    ?>
                        <th colspan="2"><? echo $months[$monthId].', '.$ex_month[0]; ?></th>
                    <? } ?>

                    <th colspan="2">Buyer Total</th>
                </tr>
                <tr>
                    <?
                        foreach($month_arr as $month_id)
                        {
                    ?>
                        <th width="95">Qty (Pcs)</th>
                        <th width="100">Value (Usd)</th>
                    <? } ?>

                    <th width="110">Qty (Pcs)</th>
                    <th>Value (Usd)</th>
                </tr>
            </thead>
         </table>

            <div style="width:<? echo $tbl_width+17; ?>px; max-height:350px; overflow-y:scroll" id="scroll_body" >
		<table width="<? echo $tbl_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
            <? $i=1; $tot_buyer_qty=0; $tot_buyer_val=0; $column_tot_qty_val_arr=array();
			foreach($buyer_sum_arr as $buyer_id=>$buyer_name)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="30" ><? echo $i; ?></td>
                    <td width="130" ><? echo $buyer_name; ?></td>
                    <td width="160" ><?
					$team_leader="";
					$ex_tmLdr=array_filter(array_unique(explode(',',$team_ledr_arr[$buyer_id])));
					foreach($ex_tmLdr as $tm_ldr_id)
					{
						if($team_leader=="") $team_leader=$team_leader_arr[$tm_ldr_id]; else $team_leader.=', '.$team_leader_arr[$tm_ldr_id];
					}

					echo $team_leader; ?></td>
                    <?
						$row_qty=0; $row_val=0;
                        foreach($month_arr as $month_id)
                        {
							$buyer_qty =0; $buyer_val =0;
							$buyer_qty = $po_qnty_as_buyer[$month_id][$buyer_id];
							$buyer_val = $po_val_as_buyer[$month_id][$buyer_id];
							$row_qty+=$buyer_qty;
							$row_val+=$buyer_val;

							$column_tot_qty_val_arr[$month_id]['qty']+=$buyer_qty;
							$column_tot_qty_val_arr[$month_id]['val']+=$buyer_val;
						?>
						   <td width="95" align="right"><? echo number_format($buyer_qty,0,'',','); ?></td>
						   <td width="100" align="right"><? echo number_format($buyer_val,2); ?></td>
                    <? } ?>
                    <td width="110" align="right"><? echo number_format($row_qty,0,'',','); ?></td>
                    <td align="right"><? echo number_format($row_val,2); ?></td>
                </tr>
                <?
				$tot_buyer_qty+=$row_qty;
				$tot_buyer_val+=$row_val;
				$i++;
			}
			?>
            </table>
        </div>
        	<table cellspacing="0" width="<? echo $tbl_width; ?>" border="1" rules="all" class="tbl_bottom">
            	<tr>
                	<td width="30">&nbsp;</td>
                    <td width="130">&nbsp;</td>
                    <td width="160">Total</td>
                    <?
                    foreach($month_arr as $monthid)
					{
						?>
						   <td width="95" align="right"><? echo number_format($column_tot_qty_val_arr[$monthid]['qty'],0,'',','); ?></td>
						   <td width="100" align="right"><? echo number_format($column_tot_qty_val_arr[$monthid]['val'],2); ?></td>
                    	<?
					}
					?>
                    <td width="110" align="right"><? echo number_format($tot_buyer_qty,0,'',','); ?></td>
					<td align="right"><? echo number_format($tot_buyer_val,2); ?></td>
                </tr>
            </table>
    	</div>
        <?
	}
	foreach (glob("*.xls") as $filename)
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$filename=time().".xls";
	$create_new_doc = fopen($filename, 'w');
	$fdata=ob_get_contents();
	fwrite($create_new_doc,$fdata);
	ob_end_clean();
	echo "$fdata####$filename";
	exit();
}
?>