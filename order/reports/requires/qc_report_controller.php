<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if ($action=="report_generate")
{
	extract($_REQUEST);
	$cbo_buyer=str_replace("'","",$cbo_buyer_id);
	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	
	$type_id=str_replace("'","",$cbo_type_id);
	//echo $type_id;die;
	
	if($cbo_buyer!=0) $buyerCond=" and a.buyer_id='$cbo_buyer'"; else $cbo_buyer="";

	if($db_type==0)
	{
		if( $date_from=="" && $date_to=="" ) $date_cond=""; else $date_cond=" and a.costing_date between '".$date_from."' and '".$date_to."'";
	}
	else if($db_type==2)
	{
		if( $date_from=="" && $date_to=="" ) $date_cond=""; else $date_cond=" and a.costing_date between '".change_date_format($date_from,'dd-mm-yyyy','-',1)."' and '".change_date_format($date_to,'dd-mm-yyyy','-',1)."'";
	}

	$buyerArr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$buyerLocation_arr=return_library_array("select tuid, agent_location from lib_agent_location","tuid","agent_location");
	
	$lib_temp_arr=return_library_array("select id, item_name from lib_qc_template","id","item_name");
	if($db_type==0) $concat_cond="group_concat(lib_item_id)";
	else if($db_type==2) $concat_cond="listagg(cast(lib_item_id as varchar2(4000)),',') within group (order by lib_item_id)";
	else $concat_cond="";
	   $sql_tmp="select tuid, temp_id, $concat_cond as lib_item_id from qc_template where status_active=1 and is_deleted=0 group by tuid, temp_id order by temp_id ASC"; 
	$sql_tmp_res=sql_select($sql_tmp);
	 
	//print_r($sql_tmp_res);die;
	$template_name_arr=array();
	foreach($sql_tmp_res as $row)
	{
		$lib_temp_id='';
		
		$ex_temp_id=explode(',',$row[csf('lib_item_id')]);
		foreach($ex_temp_id as $lib_id)
		{
			if($lib_temp_id=="") $lib_temp_id=$lib_temp_arr[$lib_id]; else $lib_temp_id.=','.$lib_temp_arr[$lib_id];
		}
		$template_name_arr[$row[csf('tuid')]]=$lib_temp_id;
	}
	unset($sql_tmp_res);
	
 	  $sql_cons_rate="select b.id, b.mst_id, b.item_id, b.type, b.particular_type_id, b.consumption, b.ex_percent, b.tot_cons, b.unit, b.is_calculation, b.rate, b.rate_data, b.value from qc_cons_rate_dtls b,qc_mst a  where a.id=b.mst_id and b.status_active=1 and b.is_deleted=0 $date_cond order by b.id asc"; 
	$sql_result_cons_rate=sql_select($sql_cons_rate); $consAmt_arr=array();
	foreach ($sql_result_cons_rate as $rowConsRate)
	{
		if($rowConsRate[csf("type")]==1)
		{
			$consAmt_arr[$rowConsRate[csf("mst_id")]]['fabcons']+=$rowConsRate[csf("tot_cons")];
			$consAmt_arr[$rowConsRate[csf("mst_id")]]['fabamt']+=$rowConsRate[csf("value")];
		}
		if($rowConsRate[csf("type")]=2)
		{
			if($rowConsRate[csf("particular_type_id")]==1) $consAmt_arr[$rowConsRate[csf("mst_id")]]['printamt']+=$rowConsRate[csf("value")];
			if($rowConsRate[csf("particular_type_id")]==2) $consAmt_arr[$rowConsRate[csf("mst_id")]]['embodamt']+=$rowConsRate[csf("value")];
			if($rowConsRate[csf("particular_type_id")]==3) $consAmt_arr[$rowConsRate[csf("mst_id")]]['washamt']+=$rowConsRate[csf("value")];
		}
		if($rowConsRate[csf("type")]==3)
		{
			$consAmt_arr[$rowConsRate[csf("mst_id")]]['trimamt']+=$rowConsRate[csf("value")];
		}
	}
	
	  $sql_item_summ="select mst_id, tot_fob_cost, tot_fab_cost, tot_accessories_cost, tot_cm_cost, tot_other_cost, tot_cost, tot_commission_cost from qc_tot_cost_summary where status_active=1 and is_deleted=0" ;
	$sql_result_item_summ=sql_select($sql_item_summ); $summary_cost_arr=array();
	foreach($sql_result_item_summ as $rowItemSumm)
	{
		$summary_cost_arr[$rowItemSumm[csf("mst_id")]]['fobamt']=$rowItemSumm[csf("tot_fob_cost")];
		$summary_cost_arr[$rowItemSumm[csf("mst_id")]]['commamt']=$rowItemSumm[csf("tot_commission_cost")];
		$summary_cost_arr[$rowItemSumm[csf("mst_id")]]['cmamt']=$rowItemSumm[csf("tot_cm_cost")];
		$summary_cost_arr[$rowItemSumm[csf("mst_id")]]['otheramt']=$rowItemSumm[csf("tot_other_cost")];
		$summary_cost_arr[$rowItemSumm[csf("mst_id")]]['tot_cost']=$rowItemSumm[csf("tot_cost")];
	}
	unset($sql_result_item_summ);
	
	$rowData_arr=array();
	// echo $type_id.'DSSSSSSSSSS';die;
	if($type_id==0 || $type_id==1)
	{
		$sql_mst="select a.id, a.qc_no, a.cost_sheet_no, a.costing_date, a.buyer_id, a.style_ref, a.style_des, a.temp_id, a.lib_item_id, a.offer_qty, a.delivery_date, a.season_id, a.department_id, a.uom, a.inserted_by, a.approved, a.approved_by, a.approved_date, a.revise_no, a.option_id, c.buyer_agent_id from qc_mst a, qc_confirm_mst b, qc_tot_cost_summary c where a.qc_no=b.cost_sheet_id and a.qc_no=c.mst_id and b.cost_sheet_id=c.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $buyerCond $date_cond order by a.id asc";
		//echo $sql_mst;die;
		$sql_result=sql_select($sql_mst);
		foreach($sql_result as $row)
		{
			$rowData_arr[$row[csf('qc_no')]][$row[csf('revise_no')]][$row[csf('option_id')]]['all']=$row[csf('cost_sheet_no')].'***'.$row[csf('costing_date')].'***'.$row[csf('buyer_id')].'***'.$row[csf('style_ref')].'***'.$row[csf('style_des')].'***'.$row[csf('temp_id')].'***'.$row[csf('offer_qty')].'***'.$row[csf('delivery_date')].'***'.$row[csf('department_id')].'***'.$row[csf('uom')].'***'.$row[csf('approved')].'***'.$row[csf('buyer_agent_id')];
		}
		unset($sql_result);
	}
	if($type_id==0 || $type_id==2)
	{
		$sql_confirm="select a.qc_no from qc_mst a, qc_confirm_mst b where a.qc_no=b.cost_sheet_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyerCond $dateCond order by a.id asc";
		$sqlResConfirm=sql_select($sql_confirm);
		foreach($sqlResConfirm as $row)
		{
			$confirmid_arr[$row[csf('qc_no')]]=$row[csf('qc_no')];	
		}
		unset($sqlResConfirm);
		
		if(count($confirmid_arr)>0)
		{
			$confirmid=array_chunk($confirmid_arr,999, true);
			$confirmidCond="";
			$ji=0;
			foreach($confirmid as $key=>$value)
			{
				if($ji==0)
				{
					$confirmidCond=" and a.qc_no not in(".implode(",",$value).")"; 
				}
				else
				{
					$confirmidCond.=" or a.qc_no not in(".implode(",",$value).")";
				}
				$ji++;
			}
		}
		$sql_mst="select a.id, a.qc_no,a.entry_form, a.cost_sheet_no, a.costing_date, a.buyer_id, a.style_ref, a.style_des, a.temp_id, a.lib_item_id, a.offer_qty, a.delivery_date, a.season_id, a.department_id, a.uom, a.inserted_by, a.approved, a.approved_by, a.approved_date, a.revise_no, a.option_id, c.buyer_agent_id from qc_mst a, qc_tot_cost_summary c where a.qc_no=c.mst_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $buyerCond $date_cond $confirmidCond order by a.id asc";
		// echo $sql_mst;
		$sql_result=sql_select($sql_mst);
		foreach($sql_result as $row)
		{
			$rowData_arr[$row[csf('qc_no')]][$row[csf('revise_no')]][$row[csf('option_id')]]['all']=$row[csf('cost_sheet_no')].'***'.$row[csf('costing_date')].'***'.$row[csf('buyer_id')].'***'.$row[csf('style_ref')].'***'.$row[csf('style_des')].'***'.$row[csf('temp_id')].'***'.$row[csf('offer_qty')].'***'.$row[csf('delivery_date')].'***'.$row[csf('department_id')].'***'.$row[csf('uom')].'***'.$row[csf('approved')].'***'.$row[csf('buyer_agent_id')].'***'.$row[csf('entry_form')];
		}
		unset($sql_result);
	}
	//die;
	
	ob_start();
	?>
    <fieldset>
        <table width="1840" cellspacing="0" >
            <tr style="border:none;">
                <td align="center" style="border:none;font-size:16px; font-weight:bold" colspan="21"> <? echo $report_title; ?></td>
            </tr>
            <tr style="border:none;">
                <td align="center" style="border:none;font-size:12px; font-weight:bold" colspan="21"> <? if( $date_from!="" && $date_to!="" ) echo "From  ".change_date_format($date_from)."  To  ".change_date_format($date_to);?></td>
            </tr>
        </table>
        <table width="1840" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all">
            <thead>
                <tr>
                    <th width="30">SL</th>   
                    <th width="100">Cost Sheet No</th>
                    <th width="80">Costing Date</th>
                    <th width="120">Buyer</th>
                    <th width="120">Agent</th>
                    <th width="120">Style Ref.</th>
                    <th width="120">Gmts Item</th>
                    <th width="90">Offer Qty</th>
                    <th width="80">Fabric Cons</th>
                    <th width="80">Fabric Cost /Dzn</th>
                    <th width="80">Trims Cost /Dzn</th>
                    <th width="80">Print Cost /Dzn</th>
                    <th width="80">Emb. Cost /Dzn</th>
                    <th width="80">Gmts Wash. Cost /Dzn</th>
                    <th width="80">CM Cost /Dzn</th>
                    <th width="80">Other Cost /Dzn</th>
                    <th width="80">Factory Unit Price</th>
                    <th width="80">Commis sion Cost /Dzn</th>
                    <th width="80">Final Offer Price</th>
                    <th width="80">Delivery Date</th>
                    <th>Conf. Price /Pcs</th>
                </tr>
            </thead>
        </table>
        <div style="max-height:350px; overflow-y:scroll; width:1840px" id="scroll_body" >
            <table width="1820" cellspacing="0" cellpadding="0" border="1" class="rpt_table" rules="all" id="table_body">
            <?
            $i=1;
            foreach($rowData_arr as $qc_no=>$qcdata)
            {
				foreach($qcdata as $revise_no=>$revisedata)
				{
					foreach($revisedata as $option_id=>$optiondata)
					{
						if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$exData=explode("***",$optiondata['all']);
						
						$cost_sheet_no=$costing_date=$buyer_id=$style_ref=$style_des=$lib_item_id=$offer_qty=$delivery_date=$department_id=$uom=$approved=$buyer_agent_id='';
						$cost_sheet_no=$exData[0];
						$costing_date=$exData[1];
						$buyer_id=$exData[2];
						$style_ref=$exData[3];
						$style_des=$exData[4];
						$lib_item_id=$exData[5];
						$offer_qty=$exData[6];
						$delivery_date=$exData[7];
						$department_id=$exData[8];
						$uom=$exData[9];
						$approved=$exData[10];
						$buyer_agent_id=$exData[11];
						$entry_formId=$exData[12];
						
						$fabcons=$fabamt=$trimamt=$printamt=$embodamt=$washamt=$cmamt=$otheramt=$fobamt=$commamt=$finalamt=$fobpcs=0;
						$fabcons=$consAmt_arr[$qc_no]['fabcons'];
						$fabamt=$consAmt_arr[$qc_no]['fabamt'];
						$trimamt=$consAmt_arr[$qc_no]['trimamt'];
						$printamt=$consAmt_arr[$qc_no]['printamt'];
						$embodamt=$consAmt_arr[$qc_no]['embodamt'];
						$washamt=$consAmt_arr[$qc_no]['washamt'];
						$cmamt=$summary_cost_arr[$qc_no]['cmamt'];
						$otheramt=$summary_cost_arr[$qc_no]['otheramt'];
						$fobamt=$summary_cost_arr[$qc_no]['fobamt'];
						$commamt=$summary_cost_arr[$qc_no]['commamt'];
						$finalamt=$summary_cost_arr[$qc_no]['cmamt'];
						$fobpcs=$summary_cost_arr[$qc_no]['fobamt']/12;
						
						if($entry_formId==430)
						{
							$quick_costing_button="quick_costing_print";
						}
						else
						{
							$quick_costing_button="quick_costing_print2";	
						}
					
						?>
						<tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor; ?>')" id="tr_<?=$i; ?>">
							<td width="30"><?=$i;?></td>
							<td width="100" style="word-break:break-all"><a href='#report_details' onClick="generate_qc_report('<? echo $qc_no; ?>','<? echo $cost_sheet_no; ?>','<? echo $quick_costing_button; ?>','<? echo $entry_formId; ?>');"><?=$cost_sheet_no; ?></a></td>	
							<td width="80" align="center"><?=change_date_format($costing_date); ?></td>
							<td width="120" style="word-break:break-all"><?=$buyerArr[$buyer_id]; ?></td>
							<td width="120" style="word-break:break-all"><?=$buyerLocation_arr[$buyer_agent_id]; ?></td>
							<td width="120" style="word-break:break-all"><?=$style_ref; ?></td>
							<td width="120" style="word-break:break-all"><?=$template_name_arr[$lib_item_id]; ?></td>
							<td width="90" align="right"><?=number_format($offer_qty); ?></td>
							<td width="80" align="right"><?=number_format($fabcons,4,".",""); ?></td>
							<td width="80" align="right"><?=number_format($fabamt,4,".",""); ?></td>
							<td width="80" align="right"><?=number_format($trimamt,4,".",""); ?></td>
							<td width="80" align="right"><?=number_format($printamt,4,".",""); ?></td>
							<td width="80" align="right"><?=number_format($embodamt,4,".",""); ?></td>
							<td width="80" align="right"><?=number_format($washamt,4,".",""); ?></td>
							<td width="80" align="right"><?=number_format($cmamt,4,".",""); ?></td>
							<td width="80" align="right"><?=number_format($otheramt,4,".",""); ?></td>
							<td width="80" align="right"><? //number_format($row[csf("fact_u_price")],4,".",""); ?></td>
							<td width="80" align="right"><?=number_format($commamt,4,".",""); ?></td>
							<td width="80" align="right"><? //number_format($row[csf("final_offer_price")],4,".",""); ?></td>
							<td width="80" align="center"><?=change_date_format($delivery_date); ?></td>
							<td align="right"><?=number_format($fobpcs,4,".",""); ?></td>
						</tr>
						<?
						$i++;
						$tofferQty+= $offer_qty;
					}
				}
            }
            ?>
            </table>
        </div>
        <table cellspacing="0" cellpadding="0" border="1" class="rpt_table" width="1840" rules="all">
            <tfoot>
                <tr>
                    <th width="30">&nbsp;</th>   
                    <th width="100">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="120">&nbsp;</th>
                    <th width="120">&nbsp;</th>
                    <th width="120">&nbsp;</th>
                    <th width="120">&nbsp;</th>
                    <th width="90" id="value_total_wo_qnty"><? echo number_format($tofferQty,2,".",""); ?></th>
                    <th width="80">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th>&nbsp;</th>
                </tr>
            </tfoot>
        </table>
    </fieldset>
		<?
	$html = ob_get_contents();
    ob_clean();
    //$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
    foreach (glob("*.xls") as $filename) 
    {
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
