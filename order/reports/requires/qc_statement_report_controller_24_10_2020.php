<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_season")
{
	echo create_drop_down( "cbo_season_id", 100, "select id, season_name from lib_buyer_season where buyer_id='$data' and status_active =1 and is_deleted=0 group by id, season_name order by season_name ASC","id,season_name", 1, "-Select Season-", "", "" );
	exit();
}

if ($action=="load_drop_down_sub_dep")
{
	echo create_drop_down( "cbo_subDept_id", 100, "select id, sub_department_name from lib_pro_sub_deparatment where buyer_id=$data and status_active=1 and is_deleted=0 order by sub_department_name","id,sub_department_name", 1, "-Select Dept-", $selected, "" );
	exit();
}

if ($action=="report_generate")
{
	extract($_REQUEST);
	$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
	$cbo_season_id=str_replace("'","",$cbo_season_id);
	$cbo_subDept_id=str_replace("'","",$cbo_subDept_id);
	$txt_styleRef=str_replace("'","",$txt_styleRef);
	$txt_costSheetNo=str_replace("'","",$txt_costSheetNo);
	$cbo_type_id=str_replace("'","",$cbo_type_id);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	$costingstage_id=str_replace("'","",$cbo_costingstage_id);
	
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer ","id","buyer_name");
	$department_arr=return_library_array( "select id, sub_department_name from lib_pro_sub_deparatment",'id','sub_department_name');
	$season_arr=return_library_array( "select id, season_name from lib_buyer_season",'id','season_name');
	$user_arr=return_library_array( "select id, user_full_name from user_passwd",'id','user_full_name');
	
	$dateCond="";
	if($cbo_type_id==1)
	{
		$caption="Delivery Date : ";
		if($txt_date_from!="" && $txt_date_to!="") $dateCond=" and a.delivery_date between '$txt_date_from' and '$txt_date_to'";
	}
	else
	{
		$caption="Costing Date : ";
		if($txt_date_from!="" && $txt_date_to!="") $dateCond=" and a.costing_date between '$txt_date_from' and '$txt_date_to'";
	}
	ob_start();
	?>
    <div align="center">
    <table width="2010px" cellspacing="0">
        <tr class="form_caption" style="border:none;">
            <td colspan="27" align="center" style="border:none;font-size:14px; font-weight:bold" ><? echo $report_title; ?></td>
        </tr>
        <tr style="border:none;">
            <td colspan="27" align="center" style="border:none;font-size:12px; font-weight:bold">
                <? echo $caption.change_date_format($txt_date_from)." To ".change_date_format($txt_date_to); ?>
            </td>
        </tr>
    </table>
    <? if($type==1 || $type==3)
	{ 
        if($type==1 ) $width='2010px'; else $width='2130px';
        if($type==1 ) $inner_width='1992px'; else $inner_width='2112px';
        ?>
        <table width="<? echo $width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all">
            <thead>
                <tr style="font-size:13px">
                    <th width="30">SL.</th> 
                    <?
                    if($type==3){
                        ?>
                        <th width="60">Margin %</th>
                        <?
                    }
                    ?>
                    <th width="80">Cost Sheet No</th>
                    <th width="100">Buyer</th>   
                    <th width="100">Style Desc.</th>
                    <th width="100">Style Ref.</th>
                    <th width="80">Season</th>
                    <th width="80">Department</th>
                    <th width="80">Offer Qty</th>
                    <th width="60">UOM</th>
                    
                    <th width="70">FOB Price</th>
                    <?
                    if($type==3){
                        ?>
                        <th width="60">AVL Minit</th>
                        <?
                    }
                    ?>
                    <th width="80">Amount</th>   
                    <th width="70">Delivery Date</th>
                    <th width="70">Costing Date</th>
                    <th width="80">Insert By</th>
                    <th width="80">Status</th>
                    <th width="30">Rv.</th>
                    <th width="30">Op.</th>
                    <th width="130">Approved By</th>
                    <th width="70">Days to Confirm</th>
                    <th width="70">Fabric Cost /Dzn</th>
                    
                    <th width="70">Trims Cost /Dzn</th>
                    <th width="70">Print /Emb. Cost /Dzn</th>   
                    <th width="70">Wash. Cost /Dzn</th>
                    <th width="70">CM Cost /Dzn</th>
                    <th width="70">Other Cost /Dzn</th>
                    <th width="70">Total FOB Cost /Dzn</th>
                    <th>FOB Cost /Pcs</th>
                 </tr>
            </thead>
        </table>
        <div style="width:<? echo $width; ?>; max-height:300px; overflow-y:scroll" id="scroll_body"> 
            <table width="<? echo $inner_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
            <?
           // echo $type;
            /*if($type==3){
                $sql_qc_margin="select qc_no, margin_percent, avl_min from qc_margin_mst where status_active=1 and is_deleted=0";
                $sql_result_qc_margin=sql_select($sql_qc_margin); 
                foreach($sql_result_qc_margin as $row)
                {
                    
                }
            }*/

            $sql_item_summ="select mst_id, tot_fob_cost, tot_fab_cost, tot_accessories_cost, tot_cm_cost, tot_other_cost, tot_cost from qc_tot_cost_summary where status_active=1 and is_deleted=0";
            $sql_result_item_summ=sql_select($sql_item_summ); $summary_cost_arr=array();
            foreach($sql_result_item_summ as $rowItemSumm)
            {
                $summary_cost_arr[$rowItemSumm[csf("mst_id")]]['fob']=$rowItemSumm[csf("tot_fob_cost")];
                $summary_cost_arr[$rowItemSumm[csf("mst_id")]]['fab']=$rowItemSumm[csf("tot_fab_cost")];
                $summary_cost_arr[$rowItemSumm[csf("mst_id")]]['acc']=$rowItemSumm[csf("tot_accessories_cost")];
                $summary_cost_arr[$rowItemSumm[csf("mst_id")]]['cm']=$rowItemSumm[csf("tot_cm_cost")];
                $summary_cost_arr[$rowItemSumm[csf("mst_id")]]['other']=$rowItemSumm[csf("tot_other_cost")];
                $summary_cost_arr[$rowItemSumm[csf("mst_id")]]['tot_cost']=$rowItemSumm[csf("tot_cost")];
            }
            unset($sql_result_item_summ);
            
            $sp_sql="select mst_id, particular_type_id, tot_cons, rate, value from qc_cons_rate_dtls where type=2 and particular_type_id in (1,2,3) and status_active=1 and is_deleted=0";
            $sp_sql_res=sql_select($sp_sql); $special_arr=array();
            foreach($sp_sql_res as $rowSp)
            {
                if($rowSp[csf("particular_type_id")]==1 || $rowSp[csf("particular_type_id")]==2)
                {
                    $special_arr[$rowSp[csf("mst_id")]]['premb']+=$rowSp[csf("tot_cons")]*$rowSp[csf("rate")];
                }
                else if($rowSp[csf("particular_type_id")]==3)
                {
                    $special_arr[$rowSp[csf("mst_id")]]['wash']+=$rowSp[csf("tot_cons")]*$rowSp[csf("rate")];
                }
            }
            unset($sp_sql_res);
            
            //$isConfirm = return_library_array("select cost_sheet_id, id from qc_confirm_mst where status_active=1 and is_deleted=0","cost_sheet_id","id");
            
            if($cbo_buyer_id!=0) $buyerCond=" and a.buyer_id='$cbo_buyer_id'"; else $buyerCond="";
            if($cbo_season_id!=0) $seasonCond=" and a.season_id='$cbo_season_id'"; else $seasonCond="";
            if($cbo_subDept_id!=0) $deptCond=" and a.department_id='$cbo_subDept_id'"; else $deptCond="";
            if($txt_styleRef!='') $styleCond=" and a.style_ref='$txt_styleRef'"; else $styleCond="";
            if($txt_costSheetNo!='') $costSheetNoCond=" and a.cost_sheet_no='$txt_costSheetNo'"; else $costSheetNoCond="";
            
            //$sql_mst="select id, qc_no, cost_sheet_no, buyer_id, style_des, style_ref, season_id, department_id, offer_qty, uom, delivery_date, costing_date, inserted_by, approved, approved_by, approved_date from qc_mst where status_active=1 and is_deleted=0 $buyerCond $seasonCond $deptCond $styleCond $costSheetNoCond $dateCond order by id asc";
            if($costingstage_id==1)
            {
                if($type==1)
				{
					$sql_mst="select a.id, a.qc_no, a.cost_sheet_no, a.lib_item_id, a.buyer_id, a.style_des, a.style_ref, a.season_id, a.department_id, a.offer_qty, a.uom, a.delivery_date, a.costing_date, a.inserted_by, a.approved, a.approved_by, a.approved_date, a.revise_no, a.option_id from qc_mst a, qc_confirm_mst b where a.qc_no=b.cost_sheet_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyerCond $seasonCond $deptCond $styleCond $costSheetNoCond $dateCond order by a.cost_sheet_no asc";// and a.revise_no=0 and a.option_id=0 
					$sql_result=sql_select($sql_mst);
					//echo $sql_mst;
					foreach($sql_result as $row)
					{
						$rowData_arr[$row[csf('cost_sheet_no')]][$row[csf('qc_no')]][$row[csf('revise_no')]][$row[csf('option_id')]]['all']=$row[csf('cost_sheet_no')].'***'.$row[csf('costing_date')].'***'.$row[csf('buyer_id')].'***'.$row[csf('style_ref')].'***'.$row[csf('style_des')].'***'.$row[csf('lib_item_id')].'***'.$row[csf('offer_qty')].'***'.$row[csf('delivery_date')].'***'.$row[csf('department_id')].'***'.$row[csf('uom')].'***'.$row[csf('approved')].'***'.$row[csf('inserted_by')].'***'.$row[csf('season_id')].'***'.$row[csf('department_id')].'***1'.'***'.$row[csf('approved_by')].'***'.$row[csf('approved_date')];
					}
					unset($sql_result);
				}
				else if($type==3)
				{
					$sql_mst="select a.id, a.qc_no, a.cost_sheet_no, a.lib_item_id, a.buyer_id, a.style_des, a.style_ref, a.season_id, a.department_id, a.offer_qty, a.uom, a.delivery_date, a.costing_date, a.inserted_by, a.approved, a.approved_by, a.approved_date, a.revise_no, a.option_id, c.margin_percent, c.avl_min from qc_mst a, qc_confirm_mst b, qc_margin_mst c where a.qc_no=b.cost_sheet_id and a.qc_no=c.qc_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyerCond $seasonCond $deptCond $styleCond $costSheetNoCond $dateCond order by a.cost_sheet_no asc";// and a.revise_no=0 and a.option_id=0 
					$sql_result=sql_select($sql_mst);
					//echo $sql_mst;
					foreach($sql_result as $row)
					{
						$rowData_arr[$row[csf('cost_sheet_no')]][$row[csf('qc_no')]][$row[csf('revise_no')]][$row[csf('option_id')]]['all']=$row[csf('cost_sheet_no')].'***'.$row[csf('costing_date')].'***'.$row[csf('buyer_id')].'***'.$row[csf('style_ref')].'***'.$row[csf('style_des')].'***'.$row[csf('lib_item_id')].'***'.$row[csf('offer_qty')].'***'.$row[csf('delivery_date')].'***'.$row[csf('department_id')].'***'.$row[csf('uom')].'***'.$row[csf('approved')].'***'.$row[csf('inserted_by')].'***'.$row[csf('season_id')].'***'.$row[csf('department_id')].'***1'.'***'.$row[csf('approved_by')].'***'.$row[csf('approved_date')];
						$qc_margin_arr[$row[csf("qc_no")]]['margin_percent']=$row[csf("margin_percent")];
                    	$qc_margin_arr[$row[csf("qc_no")]]['avl_min']=$row[csf("avl_min")];
					}
					unset($sql_result);
				}
            }
            if($costingstage_id==0 || $costingstage_id==2)
            {
                $sql_confirm="select a.qc_no from qc_mst a, qc_confirm_mst b where a.qc_no=b.cost_sheet_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyerCond $seasonCond $deptCond $styleCond $costSheetNoCond $dateCond order by a.id asc";
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
                            $confirmidCond=" and a.qc_no in(".implode(",",$value).")"; 
                        }
                        else
                        {
                            $confirmidCond.=" or a.qc_no in(".implode(",",$value).")";
                        }
                        $ji++;
                    }
                }
				if($type==1)
				{
					$sql_mst="select a.id, a.qc_no, a.cost_sheet_no, a.costing_date, a.buyer_id, a.style_ref, a.style_des, a.lib_item_id, a.offer_qty, a.delivery_date, a.season_id, a.department_id, a.uom, a.inserted_by, a.season_id, a.department_id, a.approved, a.approved_by, a.approved_date, a.revise_no, a.option_id, c.buyer_agent_id from qc_mst a, qc_tot_cost_summary c where a.qc_no=c.mst_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $buyerCond $seasonCond $deptCond $styleCond $costSheetNoCond $date_cond $confirmidCond order by a.cost_sheet_no asc";
					//echo $sql_mst;
					$sql_result=sql_select($sql_mst);
					foreach($sql_result as $row)
					{
						$rowData_arr[$row[csf('cost_sheet_no')]][$row[csf('qc_no')]][$row[csf('revise_no')]][$row[csf('option_id')]]['all']=$row[csf('cost_sheet_no')].'***'.$row[csf('costing_date')].'***'.$row[csf('buyer_id')].'***'.$row[csf('style_ref')].'***'.$row[csf('style_des')].'***'.$row[csf('lib_item_id')].'***'.$row[csf('offer_qty')].'***'.$row[csf('delivery_date')].'***'.$row[csf('department_id')].'***'.$row[csf('uom')].'***'.$row[csf('approved')].'***'.$row[csf('inserted_by')].'***'.$row[csf('season_id')].'***'.$row[csf('department_id')].'***2';
					}
					unset($sql_result);
				}
				else if($type==3)
				{
					$sql_mst="select a.id, a.qc_no, a.cost_sheet_no, a.costing_date, a.buyer_id, a.style_ref, a.style_des, a.lib_item_id, a.offer_qty, a.delivery_date, a.season_id, a.department_id, a.uom, a.inserted_by, a.season_id, a.department_id, a.approved, a.approved_by, a.approved_date, a.revise_no, a.option_id, b.margin_percent, b.avl_min, c.buyer_agent_id from qc_mst a, qc_margin_mst b, qc_tot_cost_summary c where a.qc_no=c.mst_id and a.qc_no=b.qc_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $buyerCond $seasonCond $deptCond $styleCond $date_cond $costSheetNoCond $confirmidCond order by b.margin_percent DESC";// 
					//echo $sql_mst;
					$sql_result=sql_select($sql_mst);
					foreach($sql_result as $row)
					{
						$rowData_arr[$row[csf('cost_sheet_no')]][$row[csf('qc_no')]][$row[csf('revise_no')]][$row[csf('option_id')]]['all']=$row[csf('cost_sheet_no')].'***'.$row[csf('costing_date')].'***'.$row[csf('buyer_id')].'***'.$row[csf('style_ref')].'***'.$row[csf('style_des')].'***'.$row[csf('lib_item_id')].'***'.$row[csf('offer_qty')].'***'.$row[csf('delivery_date')].'***'.$row[csf('department_id')].'***'.$row[csf('uom')].'***'.$row[csf('approved')].'***'.$row[csf('inserted_by')].'***'.$row[csf('season_id')].'***'.$row[csf('department_id')].'***2';
						
						$qc_margin_arr[$row[csf("qc_no")]]['margin_percent']=$row[csf("margin_percent")];
                    	$qc_margin_arr[$row[csf("qc_no")]]['avl_min']=$row[csf("avl_min")];
					}
					unset($sql_result);
				}
            }
            
            //echo $sql;
            //$sql_data=sql_select($sql_mst);
            $i=1; $tot_rows=0;
			foreach($rowData_arr as $costSheetNo=>$costdata)
            {
				foreach($costdata as $qc_no=>$qcdata)
				{
					foreach($qcdata as $revise_no=>$revisedata)
					{
						foreach($revisedata as $option_id=>$optiondata)
						{
							if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							
							$exData=explode("***",$optiondata['all']);
								
							$cost_sheet_no=$costing_date=$buyer_id=$style_ref=$style_des=$lib_item_id=$offer_qty=$delivery_date=$department_id=$uom=$approved=$approved_by=$approved_date=$inserted_by=$season_id=$department_id='';
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
							$inserted_by=$exData[11];
							$season_id=$exData[12];
							$department_id=$exData[13];
							$isconfirm=$exData[14];
							
							$approved_by=$exData[15];
							$approved_date=$exData[16];
							
							$fobPrice=$amount=$fabCost=$trimCost=$printEmb=$wash=$cm=$other=$fobPcs=$totCost=0;
							$fobPrice=$summary_cost_arr[$qc_no]['fob'];
							$amount=$offer_qty*$fobPrice;
							
							$item_id=0; $itemUom='';
							$item_id=explode(',',$lib_item_id);
							if(count($item_id)>1) $itemUom='Set'; else $itemUom='Pcs';
							
							$fabCost=$summary_cost_arr[$qc_no]['fab'];
							$trimCost=$summary_cost_arr[$qc_no]['acc'];
							$printEmb=$special_arr[$qc_no]['premb'];
							$wash=$special_arr[$qc_no]['wash'];
							$cm=$summary_cost_arr[$qc_no]['cm'];
							$other=$summary_cost_arr[$qc_no]['other'];
							$totCost=$summary_cost_arr[$qc_no]['tot_cost'];
							$fobPcs=$totCost/12;
							
							//if($isConfirm[$row[csf("qc_no")]]!="") $status="Confirm"; else $status="";
							$cstatus="";
							if($isconfirm==1) $cstatus="Confirm"; else $cstatus="Pending";
							if($approved==1) $approvedby_time=$user_arr[$approved_by].'<br>'.$approved_date; else $approvedby_time="";
							
							if($delivery_date!="") $daysToHand=datediff("d",$costing_date,$delivery_date); else $daysToHand="";
							?>
							<tr bgcolor="<?=$bgcolor;?>" onclick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>">
								<td width="30"><?=$i; ?></td>
                                <?
                                if($type==3){
                                    ?>
                                    <td width="60" align="right"><?=$qc_margin_arr[$qc_no]['margin_percent']; ?></td>
                                    <?
                                }
                                ?>
								<td width="80" title="<?=$qc_no; ?>"><a href='#report_details' onClick="generate_qc_report('<?=$qc_no; ?>','<?=$cost_sheet_no; ?>','<?='quick_costing_print2'; ?>');"><?=$cost_sheet_no; ?></a></td>
								<td width="100" style="word-break:break-all"><?=$buyerArr[$buyer_id]; ?></td>
								<td width="100" style="word-break:break-all"><?=$style_des; ?></td>
								<td width="100" style="word-break:break-all"><?=$style_ref; ?></td>
								
								<td width="80" style="word-break:break-all"><?=$season_arr[$season_id]; ?></td>
								<td width="80" style="word-break:break-all"><?=$department_arr[$department_id]; ?></td>
								<td width="80" align="right"><?=number_format($offer_qty,0); ?></td>
								<td width="60"><?=$itemUom;//$unit_of_measurement[$row[csf("uom")]]; ?></td>
                                
								<td width="70" align="right"><?=number_format($fobPrice,2); ?></td>
                                <?
                                if($type==3){
                                    ?>
                                    <td width="60" align="right" ><?=$qc_margin_arr[$qc_no]['avl_min']; ?></td>
                                    <?
                                }
                                ?>
								<td width="80" align="right" style="word-break:break-all"><?=number_format($amount,2); ?></td>
								<td width="70"><?=change_date_format($delivery_date); ?></td>
								<td width="70"><?=change_date_format($costing_date); ?></td>
								<td width="80" style="word-break:break-all"><?=$user_arr[$inserted_by]; ?></td>
								
								<td width="80" style="word-break:break-all"><?=$cstatus; ?></td>
								<td width="30" style="word-break:break-all; text-align:center"><?=$revise_no; ?></td>
								<td width="30" style="word-break:break-all; text-align:center"><?=$option_id; ?></td>
								<td width="130" align="center" style="word-break:break-all"><?=$approvedby_time; ?></td>
								<td width="70" style="word-break:break-all" align="right"><?=$daysToHand; ?></td>
								
								<td width="70" align="right"><?=number_format($fabCost,2); ?></td>
								<td width="70" align="right"><?=number_format($trimCost,2); ?></td>
								
								<td width="70" align="right"><?=number_format($printEmb,2); ?></td>
								<td width="70" align="right"><?=number_format($wash,2); ?></td>
								<td width="70" align="right"><?=number_format($cm,2); ?></td>
								<td width="70" align="right"><?=number_format($other,2); ?></td>
								<td width="70" align="right"><?=number_format($totCost,2); ?></td>
								<td align="right"><?=number_format($fobPcs,2); ?></td>
							 </tr>   
							<?
							$grand_fabCost+=$fabCost;
							$grand_trimCost+=$trimCost;
							$grand_printEmb+=$printEmb;
							$grand_wash+=$wash;
							
							$grand_cm+=$cm;
							$grand_other+=$other;
							$grand_totCost+=$totCost;
							$grand_fobPcs+=$fobPcs;
							$i++;
						}
					}
				}
			}
            ?>
            </table>
        </div>
        <table width="<? echo $width; ?>" cellspacing="0" border="1" class="tbl_bottom" rules="all">
            <tr style="font-size:13px">
                <td width="30">&nbsp;</td> 
                <?
                if($type==3){
                    ?>
                    <td width="60">&nbsp;</td>
                    <?
                }
                ?>
                <td width="80">&nbsp;</td>
                <td width="100">&nbsp;</td>   
                <td width="100">&nbsp;</td>
                <td width="100">&nbsp;</td>
                
                <td width="80">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="60">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <?
                if($type==3){
                    ?>
                    <td width="60">&nbsp;</td>
                    <?
                }
                ?>
                <td width="80">&nbsp;</td>  
                 
                <td width="70">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="30">&nbsp;</td>
                <td width="30">&nbsp;</td>
                <td width="130">Total :</td>
                <td width="70">&nbsp;</td>
                
                <td width="70" align="right" id="value_td_fab"><?=number_format($grand_fabCost,2); ?></td>
                <td width="70" align="right" id="value_td_trim"><?=number_format($grand_trimCost,2); ?></td>
                <td width="70" align="right" id="value_td_print"><?=number_format($grand_printEmb,2); ?></td>
                <td width="70" align="right" id="value_td_wash"><?=number_format($grand_wash,2); ?></td>
                <td width="70" align="right" id="value_td_cm"><?=number_format($grand_cm,2); ?></td>
                <td width="70" align="right" id="value_td_other"><?=number_format($grand_other,2); ?></td>
                <td width="70" align="right" id="value_td_fob"><?=number_format($grand_totCost,2); ?></td>
                <td id="value_td_fobpcs"><?=number_format($grand_fobPcs,2); ?></td>
             </tr>
        </table>
    <? } ?>
    </div>
    <?
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	$tot_rows=$i-1;
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename####$tot_rows####$type";
	exit();
}
?>
