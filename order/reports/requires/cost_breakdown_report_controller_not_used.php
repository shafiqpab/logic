<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------------------------------------------------------------------------------------------------
$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
connect();


if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 160, "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" );     	 
	exit();
}

if($action=="report_generate")
{ 
	
	$company_name=str_replace("'","",$cbo_company_name);
	if(str_replace("'","",$cbo_buyer_name)==0) $buyer_name="%%"; else $buyer_name=str_replace("'","",$cbo_buyer_name);
	$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
	$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
	ob_start();
?>
	<div style="width:2320px">
    <fieldset style="width:100%;">	
    	<table width="2300">
        	<tr class="form_caption">
            	<td colspan="28" align="center">Cost Breakdown Report</td>
            </tr>
            <tr class="form_caption">
            	<td colspan="28" align="center"><? echo $company_library[$company_name]; ?></td>
            </tr>
        </table>
        <table class="rpt_table" width="2300" cellpadding="0" cellspacing="0" border="1" rules="all">
        	<thead>
            	<th width="30">SL</th>
                <th width="50">Buyer</th>
                <th width="100">Job No</th>
                <th width="90">Order No</th>
                <th width="110">Garments Item</th>
                <th width="80">Order Qnty</th>
                <th width="50">UOM</th>
                <th width="80">Qnty (Pcs)</th>
                <th width="80">Shipment Date</th>
                <th width="130">Fabric Description</th>
                <th width="70">Knit Fab. Cons</th>
                <th width="60">Knit Fab. Rate</th>
                <th width="70">Woven Fab. Cons</th>
                <th width="65">Woven Fab. Rate</th>
                <th width="80">Fab. Cost/Dzn</th>
                <th width="80">Trims cost/Dzn</th>
                <th width="80">Print/Emb/ Dzn</th>
                <th width="80">CM cost/Dzn</th>
                <th width="85">Commission</th>
                <th width="80">Other Cost</th>
                <th width="80">Total cost/Dzn</th>
                <th width="80">Total CM cost</th>
                <th width="80">Total Cost</th>
                <th width="70">Cost Per unit</th>
                <th width="70">Order Price</th>
                <th width="90">Order Value</th>
                <th width="80">Margin</th>
                <th width="80">Total Trims Cost</th>
                <th>Total Emb/Print Cost</th>
            </thead>
    	</table>
        <div style="width:2320px; max-height:400px; overflow-y:scroll" id="scroll_body">
        	<table class="rpt_table" width="2300" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
            <?
			$i=1; $total_order_qnty=0; $total_order_qnty_in_pcs=0; $total_knit_fab_cons=0; $total_woven_fab_cons=0; $tot_fabric_cost_per_dzn=0; $tot_trims_cost_per_dzn=0; $tot_embell_cost_per_dzn=0; $tot_cm_cost_per_dzn=0; $tot_commission_cost=0; $tot_other_cost=0; $grand_tot_cost_per_dzn=0; $grand_tot_cm_cost=0; $grand_tot_cost=0; $tot_order_value=0; $tot_margin=0; $grand_tot_trims_cost=0; $grand_tot_embell_cost=0;
			
			$sql="select a.job_no, a.buyer_name, a.order_uom, a.gmts_item_id, a.total_set_qnty as ratio, b.id, b.po_number, b.pub_shipment_date, b.po_quantity, b.unit_price, b.po_total_price from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name='$company_name' and a.buyer_name like '$buyer_name' and b.pub_shipment_date between '$start_date' and '$end_date' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by b.id order by b.id, b.pub_shipment_date";
			$nameArray=sql_select($sql);
			$tot_rows=count($nameArray);
			foreach($nameArray as $row )
			{
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				
			?>
            	<tr bgcolor="<? echo $bgcolor;?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i; ?>">
                	<td width="30"><? echo $i; ?></td>
                    <td width="50"><? echo $buyer_short_name_library[$row[csf('buyer_name')]]; ?></td>
                    <td width="100"><? echo $row[csf('job_no')]; ?></td>
                    <td width="90"><div style="width:90px; word-wrap:break-word"><? echo $row[csf('po_number')]; ?></div></td>
                    <td width="110">
                        <div style="width:110px; word-wrap:break-word">
                        	<?
								$gmts_item='';
								$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
								foreach($gmts_item_id as $item_id)
								{
									if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
								}
								echo $gmts_item;
							?>
                        </div>
                    </td>
                    <td width="80" align="right">
						<? 
                        	echo number_format($row[csf('po_quantity')],0,'.',''); 
							$total_order_qnty+=$row[csf('po_quantity')];
                        ?>
                    </td>
                    <td width="50" align="center"><? echo $unit_of_measurement[$row[csf('order_uom')]]; ?></td>
                    <td width="80" align="right">
					<? 
						$order_qnty_in_pcs=$row[csf('po_quantity')]*$row[csf('ratio')];
						echo number_format($order_qnty_in_pcs,0,'.',''); 
						$total_order_qnty_in_pcs+=$order_qnty_in_pcs;
					?>
                    </td>
                    <td width="80" align="center"><? echo change_date_format($row[csf('pub_shipment_date')]); ?></td>
                    <?
					$fabric_desc=''; $fabric_cost_per_dzn=0; 
					$knit_fabric_cons=0; $knit_fabric_rate=0; $knit_fabric_amnt=0; $yarn_cost=0; $conversion_cost=0; $knit_fabric_purc_amnt=0;
					$woven_fabric_cons=0; $woven_fabric_rate=0; $woven_fabric_amnt=0; $other_cost=0;
					$tot_cost_per_dzn=0; $tot_cm_cost=0; $tot_cost=0; $tot_trims_cost=0; $tot_embell_cost=0; $cost_per_unit=0; $margin=0;
					
					$fabricArray=sql_select("select a.fab_nature_id, a.fabric_description, a.fabric_source, a.rate, a.avg_finish_cons, b.yarn_amount, b.conv_amount from wo_pre_cost_fabric_cost_dtls a, wo_pre_cost_sum_dtls b where a.job_no=b.job_no and a.job_no='$row[job_no]' and a.fabric_source!=3 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
					foreach($fabricArray as $fabricRow)
					{
						if($fabric_desc=="") $fabric_desc=$fabricRow[csf('fabric_description')]; else $fabric_desc.=",".$fabricRow[csf('fabric_description')];
						if($fabricRow[csf('fab_nature_id')]==2)
						{
							$knit_fabric_cons+=$fabricRow[csf('avg_finish_cons')];
							if($fabricRow[csf('fabric_source')]==2)
							{
								$knit_fabric_purc_amnt+=$fabricRow[csf('avg_finish_cons')]*$fabricRow[csf('rate')];	
							}
							
						}
						else if($fabricRow[csf('fab_nature_id')]==3)
						{
							if($fabricRow[csf('fabric_source')]==2)
							{
								$woven_fabric_cons+=$fabricRow[csf('avg_finish_cons')];
								$woven_fabric_amnt+=$fabricRow[csf('avg_finish_cons')]*$fabricRow[csf('rate')];
							}
						}
						
						$yarn_cost=$fabricRow[csf('yarn_amount')];
						$conversion_cost=$fabricRow[csf('conv_amount')];
					}
					
					$knit_fabric_amnt=$knit_fabric_purc_amnt+$yarn_cost+$conversion_cost;
					$knit_fabric_rate=$knit_fabric_amnt/$knit_fabric_cons;
					$woven_fabric_rate=$woven_fabric_amnt/$woven_fabric_cons;
					$fabric_cost_per_dzn=$knit_fabric_amnt+$woven_fabric_amnt;
					
					$fabriccostArray=sql_select("select costing_per_id, trims_cost, embel_cost, cm_cost, commission, common_oh, lab_test, inspection, freight, comm_cost from wo_pre_cost_dtls where job_no='$row[job_no]' and status_active=1 and is_deleted=0");
					
					$dzn_qnty=0;
					if($fabriccostArray[0][csf('costing_per_id')]==1)
					{
						$dzn_qnty=12;
					}
					else if($fabriccostArray[0][csf('costing_per_id')]==3)
					{
						$dzn_qnty=12*2;
					}
					else if($fabriccostArray[0][csf('costing_per_id')]==4)
					{
						$dzn_qnty=12*3;
					}
					else if($fabriccostArray[0][csf('costing_per_id')]==5)
					{
						$dzn_qnty=12*4;
					}
					else
					{
						$dzn_qnty=1;
					}
					
					$other_cost=$fabriccostArray[0][common_oh]+$fabriccostArray[0][lab_test]+$fabriccostArray[0][inspection]+$fabriccostArray[0][freight]+$fabriccostArray[0][comm_cost];
					
					$tot_cost_per_dzn=$fabric_cost_per_dzn+$fabriccostArray[0][trims_cost]+$fabriccostArray[0][cm_cost]+$fabriccostArray[0][commission]+$fabriccostArray[0][csf('embel_cost')]+$other_cost;
					$cost_per_unit=$tot_cost_per_dzn/$dzn_qnty;
					
					$trims_cons_cost=return_field_value("sum(b.cons*a.rate)","wo_pre_cost_trim_cost_dtls a, wo_pre_cost_trim_co_cons_dtls b","a.id=b.wo_pre_cost_trim_cost_dtls_id and b.po_break_down_id='$row[id]' and a.job_no='$row[job_no]' and a.status_active=1 and a.is_deleted=0");
					
					$tot_cm_cost=($row[csf('po_quantity')]/$dzn_qnty)*$fabriccostArray[0][csf('cm_cost')];
					$tot_cost=($row[csf('po_quantity')]/$dzn_qnty)*$tot_cost_per_dzn;
					$tot_trims_cost=($row[csf('po_quantity')]/$dzn_qnty)*$trims_cons_cost;//$fabriccostArray[0][csf('trims_cost')]
					$tot_embell_cost=($row[csf('po_quantity')]/$dzn_qnty)*$fabriccostArray[0][csf('embel_cost')];
					$margin=$row[csf('po_total_price')]-$tot_cost;
					?>
                    <td width="130">
                        <div style="width:130px; word-wrap:break-word">
                        	<? echo $fabric_desc; ?>
                        </div>
                    </td>
                    <td width="70" align="right">
						<? 
                        	echo number_format($knit_fabric_cons,2,'.','');
							$total_knit_fab_cons+=$knit_fabric_cons; 
                        ?>
                    </td>
                    <td width="60" align="right"><? echo number_format($knit_fabric_rate,2,'.',''); ?></td>
                    <td width="70" align="right">
					<? 
						echo number_format($woven_fabric_cons,2,'.',''); 
						$total_woven_fab_cons+=$woven_fabric_cons; 
					?>
                    </td>
                    <td width="65" align="right"><? echo number_format($woven_fabric_rate,2,'.',''); ?></td>
                    <td width="80" align="right">
					<? 
						echo number_format($fabric_cost_per_dzn,2,'.',''); 
						$tot_fabric_cost_per_dzn+=$fabric_cost_per_dzn;
						$fabric_cost_summary+=($row[csf('po_quantity')]/$dzn_qnty)*$fabric_cost_per_dzn;
					?>
                    </td>
                    <td width="80" align="right">
						<? 
                       		echo number_format($trims_cons_cost,2,'.',''); 
							$tot_trims_cost_per_dzn+=$trims_cons_cost;
                        ?>
                    </td>
                    <td width="80" align="right">
						<? 
                        	echo number_format($fabriccostArray[0][csf('embel_cost')],2,'.','');
							$tot_embell_cost_per_dzn+= $fabriccostArray[0][csf('embel_cost')];
                        ?>
                    </td>
                    <td width="80" align="right">
						<? 
                        	echo number_format($fabriccostArray[0][csf('cm_cost')],2,'.',''); 
							$tot_cm_cost_per_dzn+=$fabriccostArray[0][csf('cm_cost')];
                        ?>
                    </td>
                    <td width="85" align="right">
					<? 
						echo number_format($fabriccostArray[0][csf('commission')],2,'.',''); 
						$tot_commission_cost+=$fabriccostArray[0][csf('commission')];
						
						$comm_cost_summary+=($row[csf('po_quantity')]/$dzn_qnty)*$fabriccostArray[0][csf('commission')];
					?>
                    </td>
                    <td width="80" align="right">
					<? 
						echo number_format($other_cost,2,'.','')."<br>";
						$tot_other_cost+=$other_cost; 
						$other_cost_summary+=($row[csf('po_quantity')]/$dzn_qnty)*$other_cost;
					?>
                    </td>
                    <td width="80" align="right">
						<? 
                        	echo number_format($tot_cost_per_dzn,2,'.',''); 
							$grand_tot_cost_per_dzn+=$tot_cost_per_dzn;
                        ?>
                    </td>
                    <td width="80" align="right">
						<? 
                        	echo number_format($tot_cm_cost,2,'.',''); 
							$grand_tot_cm_cost+=$tot_cm_cost;
                        ?>
                    </td>
                    <td width="80" align="right">
						<? 
                        	echo number_format($tot_cost,2,'.','');
							$grand_tot_cost+=$tot_cost; 
                        ?>
                    </td>
                    <td width="70" align="right"><? echo number_format($cost_per_unit,2,'.',''); ?></td>
                    <td width="70" align="right"><? echo number_format($row[csf('unit_price')],2); ?></td>
                    <td width="90" align="right">
						<? 
                        	echo number_format($row[csf('po_total_price')],2,'.',''); 
							$tot_order_value+=$row[csf('po_total_price')];
                        ?>
                    </td>
                    <td width="80" align="right">
						<? 
                            echo number_format($margin,2,'.','');
							$tot_margin+=$margin; 
                        ?>
                    </td>
                    <td width="80" align="right">
						<? 
                        	echo number_format($tot_trims_cost,2,'.',''); 
							$grand_tot_trims_cost+=$tot_trims_cost;
                        ?>
                    </td>
                    <td align="right">
						<? 
                        	echo number_format($tot_embell_cost,2,'.','');
							$grand_tot_embell_cost+=$tot_embell_cost; 
                        ?>
                    </td>
                </tr>
            <?
			$i++;
			}
			?>
        	</table>
            <table class="rpt_table" width="2300" cellpadding="0" cellspacing="0" border="1" rules="all">
            	<tfoot>
                    <th width="30"></th>
                    <th width="50"></th>
                    <th width="100"></th>
                    <th width="90"></th>
                    <th width="110" align="right">Total</th>
                    <th width="80" align="right" id="total_order_qnty"><? echo number_format($total_order_qnty,0); ?></th>
                    <th width="50"></th>
                    <th width="80" align="right" id="total_order_qnty_in_pcs"><? echo number_format($total_order_qnty_in_pcs,0); ?></th>
                    <th width="80"></th>
                    <th width="130"></th>
                    <th width="70" align="right" id="value_knit_fab_cons"><? //echo number_format($total_knit_fab_cons,2); ?></th>
                    <th width="60"></th>
                    <th width="70" align="right" id="value_woven_fab_cons"><? //echo number_format($total_woven_fab_cons,2); ?></th>
                    <th width="65"></th>
                    <th width="80" align="right" id="value_fabric_cost_per_dzn"><? //echo number_format($tot_fabric_cost_per_dzn,2); ?></th>
                    <th width="80" align="right" id="value_trims_cost_per_dzn"><? //echo number_format($tot_trims_cost_per_dzn,2); ?></th>
                    <th width="80" align="right" id="value_embell_cost_per_dzn"><? //echo number_format($tot_embell_cost_per_dzn,2); ?></th>
                    <th width="80" align="right" id="value_cm_cost_per_dzn"><? //echo number_format($tot_cm_cost_per_dzn,2); ?></th>
                    <th width="85" align="right" id="value_commission_cost"><? //echo number_format($tot_commission_cost,2); ?></th>
                    <th width="80" align="right" id="value_other_cost"><? //echo number_format($tot_other_cost,2); ?></th>
                    <th width="80" align="right" id="value_tot_cost_per_dzn"><? //echo number_format($grand_tot_cost_per_dzn,2); ?></th>
                    <th width="80" align="right" id="value_tot_cm_cost"><? echo number_format($grand_tot_cm_cost,2); ?></th>
                    <th width="80" align="right" id="value_tot_cost"><? echo number_format($grand_tot_cost,2); ?></th>
                    <th width="70"></th>
                    <th width="70"></th>
                    <th width="90" align="right" id="value_order"><? echo number_format($tot_order_value,2); ?></th>
                    <th width="80" align="right" id="value_margin"><? echo number_format($tot_margin,2); ?></th>
                    <th width="80" align="right" id="value_tot_trims_cost"><? echo number_format($grand_tot_trims_cost,2); ?></th>
                    <th align="right" id="value_tot_embell_cost"><? echo number_format($grand_tot_embell_cost,2); ?></th>
                </tfoot>
            </table>
        </div>
        <table>
            <tr><td height="15"></td></tr>
        </table>
        <table width="1100" border="1">
        	<tr>
            	<td width="400">
                	<table class="rpt_table" width="100%" cellpadding="0" cellspacing="0" border="1" rules="all">
                    	<thead>
                        	<th width="140">Particulars</th>
                            <th width="160">Amount</th>
                            <th>Percentage</th>
                        </thead>
                        <tr bgcolor="#E9F3FF">
                        	<td>Fabric Cost</td>
                            <td align="right"><? echo number_format($fabric_cost_summary,2); ?>
                            </td>
                            <td align="right"><? echo number_format((($fabric_cost_summary*100)/$tot_order_value),2); ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Trims Cost</td>
                            <td align="right"><? echo number_format($grand_tot_trims_cost,2); ?></td>
                           <td align="right"><? echo number_format((($grand_tot_trims_cost*100)/$tot_order_value),2); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Embellish Cost</td>
                            <td align="right"><? echo number_format($grand_tot_embell_cost,2); ?></td>
                            <td align="right"><? echo number_format((($grand_tot_embell_cost*100)/$tot_order_value),2); ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Commision Cost</td>
                            <td align="right"><? echo number_format($comm_cost_summary,2); ?></td>
                            <td align="right"><? echo number_format((($comm_cost_summary*100)/$tot_order_value),2); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Other Cost</td>
                            <td align="right"><? echo number_format($other_cost_summary,2); ?></td>
                            <td align="right"><? echo number_format((($other_cost_summary*100)/$tot_order_value),2); ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Total Cost</td>
                            <td align="right"><? echo number_format($grand_tot_cost,2); ?></td>
                            <td align="right"><? echo number_format((($grand_tot_cost*100)/$tot_order_value),2); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>Total Order Value</td>
                            <td align="right"><? echo number_format($tot_order_value,2); ?></td>
                            <td align="right"><? echo number_format((($tot_order_value*100)/$tot_order_value),2); ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>CM Value</td>
                            <td align="right">
								<? 
									$cm_value=$tot_order_value-$grand_tot_cost;
                                    echo number_format($cm_value,2); 
                                ?>
                            </td>
                            <td align="right"><? echo number_format((($cm_value*100)/$tot_order_value),2); ?></td>
                        </tr>
                        <tr bgcolor="#E9F3FF">
                        	<td>CM</td>
                            <td align="right"><? echo number_format($grand_tot_cm_cost,2); ?></td>
                            <td align="right"><? echo number_format((($grand_tot_cm_cost*100)/$tot_order_value),2); ?></td>
                        </tr>
                        <tr bgcolor="#FFFFFF">
                        	<td>Margin</td>
                            <td align="right">
								<? 
									$margin_value=$cm_value-$grand_tot_cm_cost;
                                    echo number_format($margin_value,2); 
                                ?>
                            </td>
                            <td align="right"><? echo number_format((($margin_value*100)/$tot_order_value),2); ?></td>
                        </tr>
                    </table>
                </td>
                <td><input type="hidden" id="graph_data" value=""/></td>
                <td width="650" id="chartdiv"></td>
            </tr>
        </table>
        </fieldset>
    </div>
<?
	foreach (glob("*.xls") as $filename) {
	//if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data****requires/$filename****$tot_rows";
	exit();	
}
disconnect($con);
?>