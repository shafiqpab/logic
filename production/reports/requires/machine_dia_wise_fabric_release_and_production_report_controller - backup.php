<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$user_id=$_SESSION['logic_erp']['user_id'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_buyer")
{
	if($data!=0)
	{
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90)) $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0); 
	}
	else
	{
		echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy where buy.status_active =1 and buy.is_deleted=0  $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",0,"" );
	}
	exit();
}

if($action=="report_generate") // Knitting Production And Plan Report
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$cbo_buyer_name=trim(str_replace("'","",$cbo_buyer_name));
	if($cbo_buyer_name!="") $buyer_cond=" and a.buyer_name=$cbo_buyer_name"; else $buyer_cond='';
	$txt_file_no=trim(str_replace("'","",$txt_file_no));
	$txt_po_no=trim(str_replace("'","",$txt_po_no));
	if($txt_po_no!="") $po_cond=" and b.po_number LIKE '".$txt_po_no."%'"; else $po_cond='';
	$txt_file_no=trim(str_replace("'","",$txt_file_no));
	if($txt_file_no!="") $file_no_cond=" and b.file_no LIKE '".$txt_file_no."%'"; else $file_no_cond='';
	$txt_ref_no=trim(str_replace("'","",$txt_ref_no));
	if($txt_ref_no!="") $ref_no_cond=" and b.grouping LIKE '".$txt_ref_no."%'"; else $ref_no_cond='';

	if(str_replace("'","",$cbo_buyer_name)==0) $buyer_id_cond=""; else $buyer_id_cond= " and a.location_id =".$cbo_buyer_name."";

	if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="")
	{
		if($cbo_based_on==1)
		{
		 	if($db_type==0)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
			}
			else if($db_type==2)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
			}
			$program_date_cond=" and a.receive_date between '$start_date' and '$end_date'";
			$ship_date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'";
		}
		else
		{
		  if($db_type==0)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"yyyy-mm-dd","");
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"yyyy-mm-dd","");
			}
			else if($db_type==2)
			{
				$start_date=change_date_format(str_replace("'","",$txt_date_from),"","",1);
				$end_date=change_date_format(str_replace("'","",$txt_date_to),"","",1);
			}
			$program_date_cond=" and a.receive_date between '$start_date' and '$end_date'";
			$ship_date_cond=" and b.pub_shipment_date between '$start_date' and '$end_date'";
		}
	}
	
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$supplier_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name"  );
	$yarn_count_arr=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");
	$body_part_type_arr=return_library_array( "select id, body_part_type from lib_body_part where body_part_type in(40,50) and status_active=1 and is_deleted=0", "id", "body_part_type");
	// constuction composition query
	$deterIds_arr=array();
	$constuction_arr=array(); $composition_arr=array(); $colorRange_array=array(); $gsm_array=array(); $mapping_array=array();
	$sql_deter="SELECT a.id, a.construction, a.color_range_id, a.gsm_weight, b.count_id, b.copmposition_id, b.percent, b.type_id from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
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

	//========
	$mapDataArray=sql_select("select mst_id, machine_dia, fabric_dia, stitch_length from fabric_mapping");
	foreach($mapDataArray as $row )
	{
		$mapping_array[$row[csf('mst_id')]]['mdia']=$row[csf('machine_dia')];
		$mapping_array[$row[csf('mst_id')]]['fdia']=$row[csf('fabric_dia')];
		$mapping_array[$row[csf('mst_id')]]['sl']=$row[csf('stitch_length')];
	}
	
	// Order Entry
	$poArr=array(); $poIds=''; $tot_rows=0; $reqArr = array();
	$sql="SELECT a.job_no, a.buyer_name, b.id, b.po_number, b.pub_shipment_date 
	from wo_po_details_master a, wo_po_break_down b 
	where a.job_no=b.job_no_mst and a.company_name=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyer_cond $file_no_cond $ref_no_cond $po_cond $ship_date_cond order by b.pub_shipment_date, b.id";
	//echo $sql; // TMP_PO_ID
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
	
	// ===
	$poFabricsArr=array();
	$reqSQL = "SELECT b.po_break_down_id, c.lib_yarn_count_deter_id, (b.requirment/b.pcs)*a.plan_cut_qnty as requirment 
	from wo_po_color_size_breakdown a, wo_pre_cos_fab_co_avg_con_dtls b, wo_pre_cost_fabric_cost_dtls c 
	where a.po_break_down_id=b.po_break_down_id and a.color_number_id=b.color_number_id and a.size_number_id=b.gmts_sizes and b.pcs>0 and b.pre_cost_fabric_cost_dtls_id=c.id and a.job_no_mst=c.job_no and a.item_number_id=c.item_number_id and c.fab_nature_id=2 and a.is_deleted=0 and a.status_active=1 $poIds_cond";	
	$reqSQLresult = sql_select($reqSQL);
	foreach($reqSQLresult as $val)
	{
		$reqArr[$val[csf('po_break_down_id')]][$val[csf('lib_yarn_count_deter_id')]]+=$val[csf('requirment')];	
		$poFabricsArr[$val[csf('po_break_down_id')]].=$val[csf('lib_yarn_count_deter_id')].",";
	}
	unset($reqSQLresult);	
	
	// Booking 
	$grey_qnty_array=array();
	$greySql="SELECT b.po_break_down_id, c.lib_yarn_count_deter_id, c.body_part_id, sum(b.grey_fab_qnty) as grey_fab_qnty 
	from wo_booking_mst a, wo_booking_dtls b, wo_pre_cost_fabric_cost_dtls c 
	where a.booking_no=b.booking_no and b.pre_cost_fabric_cost_dtls_id=c.id and b.job_no=c.job_no and a.item_category in(2,13) and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 
	$poIds_cond group by b.po_break_down_id, c.lib_yarn_count_deter_id, c.body_part_id";
	// echo $greySql;
	$greyDataArray=sql_select($greySql);
	foreach($greyDataArray as $val)
	{
		$grey_qnty_array[$val[csf('po_break_down_id')]][$val[csf('lib_yarn_count_deter_id')]]=$val[csf('grey_fab_qnty')];	
		$body_part_type=$body_part_type_arr[$val[csf('body_part_id')]];
		if ($body_part_type !=40 && $body_part_type !=50) 
		{
			$booking_data_array[$val[csf('po_break_down_id')]][$val[csf('body_part_id')]][$val[csf('lib_yarn_count_deter_id')]]=$val[csf('grey_fab_qnty')];
		}	
	}
	unset($greyDataArray);	
	// echo "<pre>";print_r($grey_qnty_array);die;
	
	// Planing info entry
	$plan_arr=array();
	$sqlPlan="SELECT b.po_id, b.determination_id, a.knitting_source, a.knitting_party 
	from ppl_planning_info_entry_dtls a, ppl_planning_entry_plan_dtls b where a.id=b.dtls_id $poIds_cond_plan";
	// $program_date_cond
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

	// Knitting production
	$productionDataArr=array();
	$txt_production_date=str_replace("'","",trim($txt_production_date));
	if($program_date_cond!="")
	{
		$query="SELECT c.po_breakdown_id, b.shift_name, b.febric_description_id, sum(c.quantity) as quantity 
		from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c 
		WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 $poIds_cond_order $buyer_id_cond $program_date_cond 
		group by c.po_breakdown_id, b.shift_name, b.febric_description_id";
	}
	else
	{		
		$query="SELECT c.po_breakdown_id, b.shift_name, b.febric_description_id, c.quantity
		from inv_receive_master a, pro_grey_prod_entry_dtls b, order_wise_pro_details c 
		WHERE a.id=b.mst_id and b.id=c.dtls_id and a.entry_form=2 and c.entry_form=2 and c.status_active=1 and c.is_deleted=0 $poIds_cond_order $buyer_id_cond";
	}
	$data_array=sql_select($query);
	foreach($data_array as $row)
	{
		 $productionDataArr[$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]][$row[csf('shift_name')]][1]+=$row[csf('todayqty')];
		$productionDataArr[$row[csf('po_breakdown_id')]][$row[csf('febric_description_id')]][$row[csf('shift_name')]][2]+=$row[csf('quantity')];
	}	
	unset($data_array);	
	
	// ========
	$delivery_array=array();
	$sqlDelv="SELECT order_id, determination_id, sum(current_delivery) as qty from pro_grey_prod_delivery_dtls where entry_form in(53,56) and status_active=1 and is_deleted=0 $poIds_cond_delv group by order_id, determination_id";
	$delvData=sql_select($sqlDelv);
	foreach($delvData as $row)
	{
		$delivery_array[$row[csf('order_id')]][$row[csf('determination_id')]]=$row[csf('qty')];
	}
	unset($delvData);

	ob_start();
	?>
	<fieldset style="width:1660px">
		<table cellpadding="0" cellspacing="0" width="1660">
			<tr class="form_caption" style="border:none;">
			   <td align="center" width="100%" colspan="21" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
			</tr>
			<tr class="form_caption" style="border:none;">
			   <td align="center" width="100%" colspan="21" style="font-size:16px"><strong><? echo $company_library[str_replace("'","",$cbo_company_name)]; ?></strong></td>
			</tr>
		</table>
		<table width="1640" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
			<thead>
            	<tr>
                    <th width="40">Buyer</th>
                    <th width="80">Job No </th>
                    <th width="70">Ref No</th>
                    <th width="90">File No</th>
                    <th width="60">Style</th>
                    <th width="60">Booking No</th>
                    <th width="60">Booking Date</th>
                    <th width="110">Pub. Shipment Date</th>
                    <th width="100">Construction</th>
                    <th width="160">Fabric Description</th>
                    <th width="70">GSM</th>
                    <th width="80">Color Type</th>
                    <th width="90">Dia/Width Type</th>
                    <th width="90">M/C Dia X Gauge</th>
                    <th width="90">Booking Qty.</th>
                    <th width="90">Program Qty</th>
                    <th width="90">Yet to Program</th>
                	<th width="80">Knitting Productin</th>
                    <th>Production Balance</th>
                </tr>
			</thead>
		</table>
		<div style="width:1660px; overflow-y: scroll; max-height:380px;" id="scroll_body">
			<table width="1640" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left" id="table_body"> 
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
							<td width="40"><p><? echo $buyer_arr[$buyer_id]; ?></p></td>
                            <td width="80"><p><? echo 'Job No'; ?></p></td>
                            <td width="70"><p><? echo $buyer_arr[$buyer_id]; ?></p></td>
                            <td width="90"><p><? echo $poNo; ?></p></td>
                            <td width="60"><p><? echo 'Style'; ?></p></td>
                            <td width="60"><p><? echo "Booking No";//$fDia; ?></p></td>
                            <td width="60"><p><? echo 'Booking Date'; ?></p></td>
							<td width="110"><p><? echo $shipDate; ?></p></td>
                            <td width="100"><p><? echo $construction; ?></p></td>
							<td width="160"><p><? echo $description; ?></p></td>
                            <td width="70"><p><? echo $gsm; ?></p></td>
							<td width="80"><p><? echo $colorRange; ?></p></td>
                            <td width="90"><p><? echo 'Dia/Width Type'; ?></p></td>
                            <td width="90"><p><? echo $mDia; ?></p></td>
							<td width="90" align="right"><p><? echo number_format($booking_qty,2,'.',''); ?></p></td>
                            <td width="90" align="right"><p><? echo number_format($program_qty,2,'.',''); ?></p></td>
							<td width="90" align="right"><p><? echo 'Yet to Program'; ?></p></td>
							<td width="80" align="right"><p><? echo 'Knitting Productin'; ?></p></td>
							<td align="right"><p><? echo 'Production Balance'; ?></p></td>
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
		<table width="1640" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" align="left">
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
                    <th width="100">&nbsp;</th>
                    <th width="160">&nbsp;</th>
                    <th width="70">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th align="right" width="90">&nbsp;</th>
                    <th align="right" width="90">Total</th>
                    <th align="right" width="90"><? echo number_format($tot_booking_qty,2,'.',''); ?></th>
                    <th align="right" width="90"><? echo number_format($tot_cumTot_qty,2,'.',''); ?></th>
                    <th align="right" width="90"><? echo number_format($tot_balance_qty,2,'.',''); ?></th>
                    <th align="right" width="80"><? echo number_format($tot_aShift_qty,2,'.',''); ?></th>
                    <th align="right"><? echo number_format($tot_delivery_to_store_qty,2,'.',''); ?></th>
                </tr>
            </tfoot>
        </table>
	</fieldset>
	<?
	
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
    echo "$html####$filename"; 
    exit();
}
?>