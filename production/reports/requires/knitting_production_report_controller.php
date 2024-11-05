<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 140, "select id,location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-- Select Location --", 0, "" );
	exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$supplier_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name"  );
	$yarn_count_arr=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	
	$txt_construction=trim(str_replace("'","",$txt_construction));
	if($txt_construction!="") $construction_cond=" and a.construction LIKE '".$txt_construction."%'"; else $construction_cond='';

	$deterIds_arr=array();
	$constuction_arr=array(); $composition_arr=array(); $colorRange_array=array(); $gsm_array=array(); $mapping_array=array();
	$sql_deter="select a.id, a.construction, a.color_range_id, a.gsm_weight, b.count_id, b.copmposition_id, b.percent, b.type_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id $construction_cond";
	$data_array=sql_select($sql_deter);
	foreach($data_array as $row )
	{
		if(array_key_exists($row[csf('id')],$composition_arr))
		{
			$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$yarn_count_arr[$row[csf('count_id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
		}
		else
		{
			$composition_arr[$row[csf('id')]]=$yarn_count_arr[$row[csf('count_id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
		}
		
		$constuction_arr[$row[csf('id')]]=$row[csf('construction')];
		$colorRange_array[$row[csf('id')]]=$color_range[$row[csf('color_range_id')]];
		$gsm_array[$row[csf('id')]]=$row[csf('gsm_weight')];
		
		$deterIds_arr[$row[csf('id')]]=$row[csf('id')];
	}
	$deterIds_cond='';
	if($txt_construction!="")
	{
		if(count($deterIds_arr)>0)
		{
			$deterIds_cond=' and c.lib_yarn_count_deter_id in('.implode(",",$deterIds_arr).')';
		}
		else
		{
			$deterIds_cond=' and c.lib_yarn_count_deter_id in(0)';
		}
	}
	//echo $deterIds_cond;
	$mapDataArray=sql_select("select mst_id, machine_dia, fabric_dia, stitch_length from fabric_mapping");
	foreach($mapDataArray as $row )
	{
		$mapping_array[$row[csf('mst_id')]]['mdia']=$row[csf('machine_dia')];
		$mapping_array[$row[csf('mst_id')]]['fdia']=$row[csf('fabric_dia')];
		$mapping_array[$row[csf('mst_id')]]['sl']=$row[csf('stitch_length')];
	}
	
	$txt_po_no=trim(str_replace("'","",$txt_po_no));
	if($txt_po_no!="") $po_cond=" and b.po_number LIKE '".$txt_po_no."%'"; else $po_cond='';

	$start_date=str_replace("'","",trim($txt_date_from));
	$end_date=str_replace("'","",trim($txt_date_to));
	if($start_date!="" && $end_date!="")
	{
		$str_cond="and b.pub_shipment_date between '$start_date' and '$end_date'";
	}
	else
		$str_cond="";
		
	if(str_replace("'","",$cbo_location_name)==0) $location_cond=""; else $location_cond= " and a.location_id =".$cbo_location_name."";
		
	$poArr=array(); $poIds=''; $tot_rows=0; $reqArr = array();
	$sql="select a.job_no, a.buyer_name, b.id, b.po_number, b.pub_shipment_date from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $po_cond $str_cond order by b.pub_shipment_date, b.id";
	$result=sql_select($sql);
	foreach($result as $row)
	{
		$tot_rows++;
		$poData=$row[csf('buyer_name')]."_".$row[csf('job_no')]."_".$row[csf('po_number')]."_".$row[csf('pub_shipment_date')];
		$poArr[$row[csf('id')]]=$poData;
		$poIds.=$row[csf('id')].",";
		
	}
	unset($result);	

	$poIds=chop($poIds,','); $poIds_cond=""; $poIds_cond_plan=""; $poIds_cond_order=""; $poIds_cond_delv="";
	if($db_type==2 && $tot_rows>1000)
	{
		$poIds_cond_pre=" and ("; $poIds_cond_suff.=")";
		$poIdsArr=array_chunk(explode(",",$poIds),999);
		foreach($poIdsArr as $ids)
		{
			$ids=implode(",",$ids);
			$poIds_cond.=" b.po_break_down_id in($ids) or ";
			$poIds_cond_plan.=" b.po_id in($ids) or ";
			$poIds_cond_order.=" c.po_breakdown_id in($ids) or ";
			$poIds_cond_delv.=" order_id in($ids) or ";
		}
		
		$poIds_cond=$poIds_cond_pre.chop($poIds_cond,'or ').$poIds_cond_suff;
		$poIds_cond_plan=$poIds_cond_pre.chop($poIds_cond_roll,'or ').$poIds_cond_suff;
		$poIds_cond_order=$poIds_cond_pre.chop($poIds_cond_roll,'or ').$poIds_cond_suff;
		$poIds_cond_delv=$poIds_cond_pre.chop($poIds_cond_delv,'or ').$poIds_cond_suff;
	}
	else
	{
		$poIds_cond=" and b.po_break_down_id in($poIds)";
		$poIds_cond_plan=" and b.po_id in($poIds)";
		$poIds_cond_order=" and c.po_breakdown_id in($poIds)";
		$poIds_cond_delv=" and order_id in($poIds)";
	}
	
	$poFabricsArr=array();
	$reqSQL = "select b.po_break_down_id, c.lib_yarn_count_deter_id, (b.requirment/b.pcs)*a.plan_cut_qnty as requirment from wo_po_color_size_breakdown a, wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls c where a.po_break_down_id=b.po_break_down_id and a.color_number_id=b.color_number_id and a.size_number_id=b.gmts_sizes and b.pcs>0 and b.pre_cost_fabric_cost_dtls_id=c.id and a.job_no_mst=c.job_no and a.item_number_id=c.item_number_id and c.fab_nature_id=2 and a.is_deleted=0 and a.status_active=1 $poIds_cond $deterIds_cond";	
	$reqSQLresult = sql_select($reqSQL);
	foreach($reqSQLresult as $val)
	{
		$reqArr[$val[csf('po_break_down_id')]][$val[csf('lib_yarn_count_deter_id')]]+=$val[csf('requirment')];	
		$poFabricsArr[$val[csf('po_break_down_id')]].=$val[csf('lib_yarn_count_deter_id')].",";
	}
	unset($reqSQLresult);	
	
	$grey_qnty_array=array();
	$greySql="select b.po_break_down_id, c.lib_yarn_count_deter_id, sum(b.grey_fab_qnty) as grey_fab_qnty from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and b.job_no=c.job_no and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $poIds_cond group by b.po_break_down_id, c.lib_yarn_count_deter_id";
	$greyDataArray=sql_select($greySql);
	foreach($greyDataArray as $val)
	{
		$grey_qnty_array[$val[csf('po_break_down_id')]][$val[csf('lib_yarn_count_deter_id')]]=$val[csf('grey_fab_qnty')];	
	}
	unset($greyDataArray);	
	
	$plan_arr=array();
	$sqlPlan="select b.po_id, b.determination_id, a.knitting_source, a.knitting_party from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b where a.id=b.dtls_id $poIds_cond_plan";
	$plan_data=sql_select($sqlPlan);
	foreach($plan_data as $row)
	{
		$knitting_company='';
		if($row[csf('knitting_source')]==1)
		{
			$knitting_company=$company_library[$row[csf('knitting_party')]];
		}
		else if($row[csf('knitting_source')]==3)
		{
			$knitting_company=$supplier_arr[$row[csf('knitting_party')]];
		}
		
		$plan_arr[$row[csf('po_id')]][$row[csf('determination_id')]]=$knitting_company;
	}
	unset($plan_data);	

	$productionDataArr=array();
	$txt_production_date=str_replace("'","",trim($txt_production_date));
	if($txt_production_date!="")
	{
		$query="select c.po_breakdown_id, b.shift_name, b.febric_description_id, sum(c.quantity) as quantity, sum(case when a.receive_date='".$txt_production_date."' then c.quantity end) as todayqty from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 $poIds_cond_order $location_cond group by c.po_breakdown_id, b.shift_name, b.febric_description_id";
	}
	else
	{		
		$query="select c.po_breakdown_id, b.shift_name, b.febric_description_id, c.quantity, 0 as todayqty from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 $poIds_cond_order $location_cond";
	}
	$data_array=sql_select($query);
	foreach($data_array as $row)
	{
		 $productionDataArr[$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]][$row[csf('shift_name')]][1]+=$row[csf('todayqty')];
		$productionDataArr[$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]][$row[csf('shift_name')]][2]+=$row[csf('quantity')];
	}
	
	unset($data_array);	
	
	$delivery_array=array();
	$sqlDelv="select order_id, determination_id, sum(current_delivery) as qty from pro_grey_prod_delivery_dtls where entry_form in(53,56) and status_active=1 and is_deleted=0 $poIds_cond_delv group by order_id, determination_id";
	$delvData=sql_select($sqlDelv);
	foreach($delvData as $row)
	{
		$delivery_array[$row[csf('order_id')]][$row[csf('determination_id')]]=$row[csf('qty')];
	}
	unset($delvData);	
	ob_start();
	?>
	<fieldset style="width:1820px">
		<table cellpadding="0" cellspacing="0" width="1820">
			<tr class="form_caption" style="border:none;">
			   <td align="center" width="100%" colspan="21" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
			</tr>
			<tr class="form_caption" style="border:none;">
			   <td align="center" width="100%" colspan="21" style="font-size:16px"><strong><? echo $company_library[str_replace("'","",$cbo_company_name)]; ?></strong></td>
			</tr>
		</table>
		<table width="1800" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">            	
			<thead>
            	<tr>
                    <th width="40" rowspan="2">SL</th>
                    <th width="80" rowspan="2">Shipment Date</th>
                    <th width="70" rowspan="2">Buyer</th>
                    <th width="90" rowspan="2">Order No</th>
                    <th width="60" rowspan="2">M/Dia</th>
                    <th width="60" rowspan="2">F/Dia</th>
                    <th width="60" rowspan="2">Req. S/L</th>
                    <th width="110" rowspan="2">Factory</th>
                    <th width="160" rowspan="2">Yarn Description</th>
                    <th width="100" rowspan="2">Construction</th>
                    <th width="90" rowspan="2">BOM Qty.</th>
                    <th width="70" rowspan="2">F. GSM</th>
                    <th width="80" rowspan="2">Color Range</th>
                    <th width="90" rowspan="2">Booking Qty.</th>
                    <th width="240" colspan="3">Production</th>
                    <th width="90" rowspan="2">Total</th>
                    <th width="90" rowspan="2">Cum. Total</th>
                    <th width="90" rowspan="2">Balance</th>
                    <th rowspan="2">Delivery To Store</th>
                </tr>
                <tr>
                	<th width="80" rowspan="2">A-Shift</th>
                    <th width="80" rowspan="2">B-Shift</th>
                    <th width="80" rowspan="2">C-Shift</th>
                </tr>
			</thead>
		</table>
		<div style="width:1820px; overflow-y: scroll; max-height:380px;" id="scroll_body">
			<table width="1800" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body"> 
			<?  
				$i=1; $tot_booking_qty=0; $tot_aShift_qty=0; $tot_bShift_qty=0; $tot_bom_qty=0;
				$tot_cShift_qty=0; $tot_dayTot_qty=0; $tot_cumTot_qty=0; $tot_balance_qty=0; $tot_delivery_to_store_qty=0;
				$construction_data_arr=array();
				foreach($poArr as $po_id=>$poData)
				{
					$poDatas=explode("_",$poData);
					$buyer_id=$poDatas[0];
					$job_no=$poDatas[1];
					$poNo=$poDatas[2];
					$shipDate=change_date_format($poDatas[3]);
					
					$poFabricDatasArr=array_unique(explode(",",chop($poFabricsArr[$po_id],',')));
					foreach($poFabricDatasArr as $deter_id)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						
						$description=$composition_arr[$deter_id];
						$construction=$constuction_arr[$deter_id];
						$colorRange=$colorRange_array[$deter_id];
						$gsm=$gsm_array[$deter_id];
						$factory=$plan_arr[$po_id][$deter_id];
						
						$mDia=$mapping_array[$deter_id]['mdia'];
						$fDia=$mapping_array[$deter_id]['fdia'];
						$stitch_length=$mapping_array[$deter_id]['sl'];
						
						$bom_qty=$reqArr[$po_id][$deter_id];
						$booking_qty=$grey_qnty_array[$po_id][$deter_id];
						
						$aShift_qty=$productionDataArr[$po_id][$deter_id][1][1];
						$bShift_qty=$productionDataArr[$po_id][$deter_id][2][1];
						$cShift_qty=$productionDataArr[$po_id][$deter_id][3][1];
						$dayTot_qty=$aShift_qty+$bShift_qty+$cShift_qty;
						$construction_data_arr[$deter_id]['qty']+=$dayTot_qty;
						
						$cumTot_qty=$productionDataArr[$po_id][$deter_id][1][2]+$productionDataArr[$po_id][$deter_id][2][2]+$productionDataArr[$po_id][$deter_id][3][2];
						
						$balance_qty=$booking_qty-$cumTot_qty;
						$delivery_to_store_qty=$delivery_array[$po_id][$deter_id];
						
						?>
						<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
							<td width="40"><? echo $i; ?></td>
                            <td width="80" align="center"><? echo $shipDate; ?></td>
                            <td width="70"><div style="width:70px; word-wrap:break-word;"><? echo $buyer_arr[$buyer_id]; ?>&nbsp;</div></td>
                            <td width="90"><div style="width:90px; word-wrap:break-word;"><? echo $poNo; ?>&nbsp;</div></td>
                            <td width="60"><p><? echo $mDia; ?>&nbsp;</p></td>
                            <td width="60"><p><? echo $fDia; ?>&nbsp;</p></td>
                            <td width="60"><p><? echo $stitch_length; ?>&nbsp;</p></td>
							<td width="110"><p><? echo $factory; ?>&nbsp;</p></td>
							<td width="160"><div style="width:160px; word-wrap:break-word;"><? echo $description; ?>&nbsp;</div></td>
                            <td width="100"><div style="width:100px; word-wrap:break-word;"><? echo $construction; ?>&nbsp;</div></td>
                            <td width="90" align="right"><? echo number_format($bom_qty,2,'.',''); ?></td>
                            <td width="70"><p><? echo $gsm; ?>&nbsp;</p></td>
							<td width="80"><div style="width:80px; word-wrap:break-word;"><? echo $colorRange; ?>&nbsp;</div></td>
							<td width="90" align="right"><? echo number_format($booking_qty,2,'.',''); ?></td>
							<td width="80" align="right"><? echo number_format($aShift_qty,2,'.',''); ?></td>
                            <td width="80" align="right"><? echo number_format($bShift_qty,2,'.',''); ?></td>
							<td width="80" align="right"><? echo number_format($cShift_qty,2,'.',''); ?></td>
                            <td width="90" align="right"><? echo number_format($dayTot_qty,2,'.',''); ?></td>
                            <td width="90" align="right"><? echo number_format($cumTot_qty,2,'.',''); ?></td>
							<td width="90" align="right"><? echo number_format($balance_qty,2,'.',''); ?></td>
							<td align="right"><? echo number_format($delivery_to_store_qty,2,'.',''); ?></td>
						</tr>
					<?	
						$i++;
						
						$tot_bom_qty+=$bom_qty;
						$tot_booking_qty+=$booking_qty; 
						$tot_aShift_qty+=$aShift_qty; 
						$tot_bShift_qty+=$bShift_qty;
						$tot_cShift_qty+=$cShift_qty;
						$tot_dayTot_qty+=$dayTot_qty;
						$tot_cumTot_qty+=$cumTot_qty;
						$tot_balance_qty+=$balance_qty;
						$tot_delivery_to_store_qty+=$delivery_to_store_qty;
					}
				}
				?>
			</table>
		</div>     
		<table width="1800" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">               
            <tfoot>
                <tr>
                	<th width="40">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="90">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="60">&nbsp;</th>
                    <th width="110">&nbsp;</th>
                    <th width="160">&nbsp;</th>
                    <th align="right" width="100">Total</th>
                    <th align="right" width="90" id="value_tot_bom"><? echo number_format($tot_bom_qty,2,'.',''); ?></th>
                    <th width="70">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th align="right" width="90" id="value_tot_booking"><? echo number_format($tot_booking_qty,2,'.',''); ?></th>
                    <th align="right" width="80" id="value_tot_aShift"><? echo number_format($tot_aShift_qty,2,'.',''); ?></th>
                    <th align="right" width="80" id="value_tot_bShift"><? echo number_format($tot_bShift_qty,2,'.',''); ?></th>
                    <th align="right" width="80" id="value_tot_cShift"><? echo number_format($tot_cShift_qty,2,'.',''); ?></th>
                    <th align="right" width="90" id="value_tot_dayTot_qty"><? echo number_format($tot_dayTot_qty,2,'.',''); ?></th>
                    <th align="right" width="90" id="value_tot_cumTot_qty"><? echo number_format($tot_cumTot_qty,2,'.',''); ?></th>
                    <th align="right" width="90" id="value_tot_balance"><? echo number_format($tot_balance_qty,2,'.',''); ?></th>
                    <th align="right" id="value_tot_delivery"><? echo number_format($tot_delivery_to_store_qty,2,'.',''); ?></th>
                </tr>
            </tfoot>
        </table>
        
	</fieldset>
    <br>
    <div style="width:310px; margin-left:5px;">
    <table width="300" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
   <caption> <strong>Construction Summary</strong></caption>
   <thead>
    <tr>
    	<th width="200"> Construction </th><th width="100"> Production </th>
    </tr>
    </thead>
    <?
	$k=1;$total_prod_qty=0;
	foreach($construction_data_arr as $cons_id=>$prod_qty)
	{
		if ($k%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
		if($prod_qty['qty']>0)
		{
		?>
		<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('trsum<? echo $k;?>','<? echo $bgcolor;?>')" id="trsum<? echo $k;?>">
		 <td><? echo $constuction_arr[$cons_id];?> </td>  <td align="right"><? echo number_format($prod_qty['qty'],2);?> </td>
		</tr>
		<?
		}
		$total_prod_qty+=$prod_qty['qty'];
		$k++;
	}
	?>
    <tfoot>
        <tr> 
       	 <th> Total</th> <th align="right"> <? echo number_format($total_prod_qty,2);?></th>
        </tr>
    </tfoot>
    </table>
    </div>
<?
	
    $html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("$user_id*.xls") as $filename) {
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
}
?>