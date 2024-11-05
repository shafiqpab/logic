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
	if($type==1) 
	{
		$hdColspan="27"; $width='2010px'; $inner_width='1992px';
	}else if($type==2)
	{
		$hdColspan="17"; $width='1300px'; $inner_width='1282px';
	}else if($type==3) 
	{
		$hdColspan="29"; $width='2130px'; $inner_width='2112px';
	}
	?>
    <div align="center">
    <table width="<?=$width; ?>px" cellspacing="0">
        <tr class="form_caption" style="border:none;">
            <td colspan="<?=$hdColspan; ?>" align="center" style="border:none;font-size:14px; font-weight:bold" ><?=$report_title; ?></td>
        </tr>
        <tr style="border:none;">
            <td colspan="<?=$hdColspan; ?>" align="center" style="border:none;font-size:12px; font-weight:bold">
                <?=$caption.change_date_format($txt_date_from)." To ".change_date_format($txt_date_to); ?>
            </td>
        </tr>
    </table>
    <? if($type==1 || $type==3)
	{ 
        ?>
        <table width="<?=$width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all">
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
        <div style="width:<?=$width; ?>; max-height:300px; overflow-y:scroll" id="scroll_body"> 
            <table width="<?=$inner_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
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
					$sql_mst="select a.id, a.qc_no, a.cost_sheet_no, a.lib_item_id, a.buyer_id, a.style_des, a.style_ref, a.season_id, a.department_id, a.offer_qty, a.uom, a.delivery_date, a.costing_date, a.inserted_by, a.approved, a.approved_by, a.approved_date, a.revise_no, a.option_id, a.exchange_rate, a.entry_form from qc_mst a, qc_confirm_mst b where a.qc_no=b.cost_sheet_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyerCond $seasonCond $deptCond $styleCond $costSheetNoCond $dateCond order by a.cost_sheet_no asc";// and a.revise_no=0 and a.option_id=0 
					$sql_result=sql_select($sql_mst);
					//echo $sql_mst;
					foreach($sql_result as $row)
					{
						$rowData_arr[$row[csf('cost_sheet_no')]][$row[csf('qc_no')]][$row[csf('revise_no')]][$row[csf('option_id')]]['all']=$row[csf('cost_sheet_no')].'***'.$row[csf('costing_date')].'***'.$row[csf('buyer_id')].'***'.$row[csf('style_ref')].'***'.$row[csf('style_des')].'***'.$row[csf('lib_item_id')].'***'.$row[csf('offer_qty')].'***'.$row[csf('delivery_date')].'***'.$row[csf('department_id')].'***'.$row[csf('uom')].'***'.$row[csf('approved')].'***'.$row[csf('inserted_by')].'***'.$row[csf('season_id')].'***'.$row[csf('department_id')].'***1'.'***'.$row[csf('approved_by')].'***'.$row[csf('approved_date')].'***'.$row[csf('exchange_rate')].'***'.$row[csf('entry_form')];
					}
					unset($sql_result);
				}
				else if($type==3)
				{
					$sql_mst="select a.id, a.qc_no, a.cost_sheet_no, a.lib_item_id, a.buyer_id, a.style_des, a.style_ref, a.season_id, a.department_id, a.offer_qty, a.uom, a.delivery_date, a.costing_date, a.inserted_by, a.approved, a.approved_by, a.approved_date, a.revise_no, a.option_id, a.entry_form, c.margin_percent, c.avl_min,a.exchange_rate from qc_mst a, qc_confirm_mst b, qc_margin_mst c where a.qc_no=b.cost_sheet_id and a.qc_no=c.qc_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyerCond $seasonCond $deptCond $styleCond $costSheetNoCond $dateCond order by a.cost_sheet_no asc";// and a.revise_no=0 and a.option_id=0 
					$sql_result=sql_select($sql_mst);
					//echo $sql_mst;
					foreach($sql_result as $row)
					{
						$rowData_arr[$row[csf('cost_sheet_no')]][$row[csf('qc_no')]][$row[csf('revise_no')]][$row[csf('option_id')]]['all']=$row[csf('cost_sheet_no')].'***'.$row[csf('costing_date')].'***'.$row[csf('buyer_id')].'***'.$row[csf('style_ref')].'***'.$row[csf('style_des')].'***'.$row[csf('lib_item_id')].'***'.$row[csf('offer_qty')].'***'.$row[csf('delivery_date')].'***'.$row[csf('department_id')].'***'.$row[csf('uom')].'***'.$row[csf('approved')].'***'.$row[csf('inserted_by')].'***'.$row[csf('season_id')].'***'.$row[csf('department_id')].'***1'.'***'.$row[csf('approved_by')].'***'.$row[csf('approved_date')].'***'.$row[csf('exchange_rate')].'***'.$row[csf('entry_form')];
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
					$sql_mst="select a.id, a.qc_no, a.cost_sheet_no, a.costing_date, a.buyer_id, a.style_ref, a.style_des, a.lib_item_id, a.offer_qty, a.delivery_date, a.season_id, a.department_id, a.uom, a.inserted_by, a.season_id, a.department_id, a.approved, a.approved_by, a.approved_date, a.revise_no, a.option_id, a.entry_form, c.buyer_agent_id, a.exchange_rate from qc_mst a, qc_tot_cost_summary c where a.qc_no=c.mst_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $buyerCond $seasonCond $deptCond $styleCond $costSheetNoCond $date_cond $confirmidCond order by a.cost_sheet_no asc";
					//echo $sql_mst;
					$sql_result=sql_select($sql_mst);
					foreach($sql_result as $row)
					{
						//$rowData_arr[$row[csf('cost_sheet_no')]][$row[csf('qc_no')]][$row[csf('revise_no')]][$row[csf('option_id')]]['all']=$row[csf('cost_sheet_no')].'***'.$row[csf('costing_date')].'***'.$row[csf('buyer_id')].'***'.$row[csf('style_ref')].'***'.$row[csf('style_des')].'***'.$row[csf('lib_item_id')].'***'.$row[csf('offer_qty')].'***'.$row[csf('delivery_date')].'***'.$row[csf('department_id')].'***'.$row[csf('uom')].'***'.$row[csf('approved')].'***'.$row[csf('inserted_by')].'***'.$row[csf('season_id')].'***'.$row[csf('department_id')].'***2'.'***'.$row[csf('exchange_rate')];
                        $rowData_arr[$row[csf('cost_sheet_no')]][$row[csf('qc_no')]][$row[csf('revise_no')]][$row[csf('option_id')]]['all']=$row[csf('cost_sheet_no')].'***'.$row[csf('costing_date')].'***'.$row[csf('buyer_id')].'***'.$row[csf('style_ref')].'***'.$row[csf('style_des')].'***'.$row[csf('lib_item_id')].'***'.$row[csf('offer_qty')].'***'.$row[csf('delivery_date')].'***'.$row[csf('department_id')].'***'.$row[csf('uom')].'***'.$row[csf('approved')].'***'.$row[csf('inserted_by')].'***'.$row[csf('season_id')].'***'.$row[csf('department_id')].'***2'.'***'.$row[csf('approved_by')].'***'.$row[csf('approved_date')].'***'.$row[csf('exchange_rate')].'***'.$row[csf('entry_form')];
					}
					unset($sql_result);
				}
				else if($type==3)
				{
					$sql_mst="select a.id, a.qc_no, a.cost_sheet_no, a.costing_date, a.buyer_id, a.style_ref, a.style_des, a.lib_item_id, a.offer_qty, a.delivery_date, a.season_id, a.department_id, a.uom, a.inserted_by, a.season_id, a.department_id, a.approved, a.approved_by, a.approved_date, a.revise_no, a.option_id, a.entry_form, b.margin_percent, b.avl_min, c.buyer_agent_id,a.exchange_rate from qc_mst a, qc_margin_mst b, qc_tot_cost_summary c where a.qc_no=c.mst_id and a.qc_no=b.qc_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $buyerCond $seasonCond $deptCond $styleCond $date_cond $costSheetNoCond $confirmidCond order by b.margin_percent DESC";// 
					//echo $sql_mst;
					$sql_result=sql_select($sql_mst);
					foreach($sql_result as $row)
					{
						//$rowData_arr[$row[csf('cost_sheet_no')]][$row[csf('qc_no')]][$row[csf('revise_no')]][$row[csf('option_id')]]['all']=$row[csf('cost_sheet_no')].'***'.$row[csf('costing_date')].'***'.$row[csf('buyer_id')].'***'.$row[csf('style_ref')].'***'.$row[csf('style_des')].'***'.$row[csf('lib_item_id')].'***'.$row[csf('offer_qty')].'***'.$row[csf('delivery_date')].'***'.$row[csf('department_id')].'***'.$row[csf('uom')].'***'.$row[csf('approved')].'***'.$row[csf('inserted_by')].'***'.$row[csf('season_id')].'***'.$row[csf('department_id')].'***2'.'***'.$row[csf('exchange_rate')];
                        $rowData_arr[$row[csf('cost_sheet_no')]][$row[csf('qc_no')]][$row[csf('revise_no')]][$row[csf('option_id')]]['all']=$row[csf('cost_sheet_no')].'***'.$row[csf('costing_date')].'***'.$row[csf('buyer_id')].'***'.$row[csf('style_ref')].'***'.$row[csf('style_des')].'***'.$row[csf('lib_item_id')].'***'.$row[csf('offer_qty')].'***'.$row[csf('delivery_date')].'***'.$row[csf('department_id')].'***'.$row[csf('uom')].'***'.$row[csf('approved')].'***'.$row[csf('inserted_by')].'***'.$row[csf('season_id')].'***'.$row[csf('department_id')].'***2'.'***'.$row[csf('approved_by')].'***'.$row[csf('approved_date')].'***'.$row[csf('exchange_rate')].'***'.$row[csf('entry_form')];
						
						$qc_margin_arr[$row[csf("qc_no")]]['margin_percent']=$row[csf("margin_percent")];
                    	$qc_margin_arr[$row[csf("qc_no")]]['avl_min']=$row[csf("avl_min")];
					}
					unset($sql_result);
				}
            }

            $data_binding=array();

            $key=0;

            foreach($rowData_arr as $costSheetNo=>$costdata)
            {
                foreach($costdata as $qc_no=>$qcdata)
                {
                    foreach($qcdata as $revise_no=>$revisedata)
                    {
                        foreach($revisedata as $option_id=>$optiondata)
                        {
                            
                            
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
                            $exchange_rate=83;
                            if(count($exData)>17)
                            {
                                $exchange_rate=$exData[17];
                            }
							$entryForm=$exData[18];
                            
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

                            $data_binding[$key]['marging']=$qc_margin_arr[$qc_no]['margin_percent'];
                            $data_binding[$key]['cost_sheet_no']=$cost_sheet_no;
                            $data_binding[$key]['qc_no']=$qc_no;
                            $data_binding[$key]['buyer']=$buyerArr[$buyer_id];
                            $data_binding[$key]['buyer_id']=$buyer_id;
                            $data_binding[$key]['style_des']=$style_des;
                            $data_binding[$key]['style_ref']=$style_ref;
                            $data_binding[$key]['season']=$season_arr[$season_id];
                            $data_binding[$key]['department']=$department_arr[$department_id];
                            $data_binding[$key]['offer_qty']=$offer_qty;
                            $data_binding[$key]['itemUom']=$itemUom;
                            $data_binding[$key]['fobPrice']=$fobPrice;
                            $data_binding[$key]['avl_min']=$qc_margin_arr[$qc_no]['avl_min'];
                            $data_binding[$key]['amount']=$amount;
                            $data_binding[$key]['delivery_date']=$delivery_date;
                            $data_binding[$key]['costing_date']=$costing_date;
                            $data_binding[$key]['inserted_by']=$user_arr[$inserted_by];
                            $data_binding[$key]['cstatus']=$cstatus;
                            $data_binding[$key]['revise_no']=$revise_no;
                            $data_binding[$key]['option_id']=$option_id;
                            $data_binding[$key]['approvedby_time']=$approvedby_time;
                            $data_binding[$key]['daysToHand']=$daysToHand;
                            $data_binding[$key]['fabCost']=$fabCost;
                            $data_binding[$key]['trimCost']=$trimCost;
                            $data_binding[$key]['printEmb']=$printEmb;
                            $data_binding[$key]['wash']=$wash;
                            $data_binding[$key]['cm']=$cm;
                            $data_binding[$key]['other']=$other;
                            $data_binding[$key]['totCost']=$totCost;
                            $data_binding[$key]['fobPcs']=$fobPcs;
                            $data_binding[$key]['exchange_rate']=$exchange_rate;
							$data_binding[$key]['entryForm']=$entryForm;
                            $key++;

                           
                        }
                    }
                }
            }

            if($type==3)
            {
                function sortByMargin($a, $b) {
                    return $a['marging'] < $b['marging'];
                }

                usort($data_binding, 'sortByMargin');
            }
            
            //echo $sql;
            //$sql_data=sql_select($sql_mst);
            $i=1; $tot_rows=0;
			foreach($data_binding as $key=>$optiondata)
            {
				
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$cost_sheet_no=$optiondata['cost_sheet_no'];
                $qc_no=$optiondata['qc_no'];
				
				?>
				<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>">
					<td width="30"><?=$i; ?></td>
                    <?
                    if($type==3){

                        $costing_date=$optiondata['costing_date'];
                        $buyer_id=$optiondata['buyer_id'];
                        $offer_qty=$optiondata['offer_qty'];
                        $ex_rate=$optiondata['exchange_rate'];
                        $buyer=$optiondata['buyer'];
                        ?>
                        <td width="60" align="right"> <a href="##" onClick="fnc_costing_details('<?=$qc_no; ?>','<?=$buyer."_".$buyer_id; ?>','<?=$costing_date; ?>','<?=$ex_rate;?>','<?=$offer_qty; ?>','costing_popup')"><p><?=$optiondata['marging']; ?></p></a></td>
                        <?
                    }
					if($optiondata['entryForm']==430)
					{
						$quick_costing_button="quick_costing_print";
					}
					else
					{
						$quick_costing_button="quick_costing_print2";	
					}
                    ?>
					<td width="80" title="<?=$qc_no; ?>"><a href='#report_details' onClick="generate_qc_report('<?=$qc_no; ?>','<?=$cost_sheet_no; ?>','<?=$quick_costing_button; ?>','<?=$optiondata['entryForm']; ?>');"><?=$optiondata['cost_sheet_no']; ?></a></td>
					<td width="100" style="word-break:break-all"><?=$optiondata['buyer']; ?></td>
					<td width="100" style="word-break:break-all"><?=$optiondata['style_des']; ?></td>
					<td width="100" style="word-break:break-all"><?=$optiondata['style_ref']; ?></td>
					
					<td width="80" style="word-break:break-all"><?=$optiondata['season']; ?></td>
					<td width="80" style="word-break:break-all"><?=$optiondata['department']; ?></td>
					<td width="80" align="right"><?=number_format($optiondata['offer_qty'],0); ?></td>
					<td width="60"><?=$optiondata['itemUom'];//$unit_of_measurement[$row[csf("uom")]]; ?></td>
                    
					<td width="70" align="right"><?=number_format($optiondata['fobPrice'],2); ?></td>
                    <?
                    if($type==3){
                        ?>
                        <td width="60" align="right" ><?=$optiondata['avl_min']; ?></td>
                        <?
                    }
                    ?>
					<td width="80" align="right" style="word-break:break-all"><?=number_format($optiondata['amount'],2); ?></td>
					<td width="70"><?=change_date_format($optiondata['delivery_date']); ?></td>
					<td width="70"><?=change_date_format($optiondata['costing_date']); ?></td>
					<td width="80" style="word-break:break-all"><?=$optiondata['inserted_by']; ?></td>
					
					<td width="80" style="word-break:break-all"><?=$optiondata['cstatus']; ?></td>
					<td width="30" style="word-break:break-all; text-align:center">
                        <?php if($optiondata['revise_no']>0){?>
                        <a href="##" onClick="fnc_revise_details('<?=$cost_sheet_no;?>','revise_popup')"><?=$optiondata['revise_no']; ?></a>
                    <?php }
                    else{
                       echo $optiondata['revise_no'];
                     }?>
                        </td>
					<td width="30" style="word-break:break-all; text-align:center"><?=$optiondata['option_id']; ?></td>
					<td width="130" align="center" style="word-break:break-all"><?=$optiondata['approvedby_time']; ?></td>
					<td width="70" style="word-break:break-all" align="right"><?=$optiondata['daysToHand']; ?></td>
					
					<td width="70" align="right"><?=number_format($optiondata['fabCost'],2); ?></td>
					<td width="70" align="right"><?=number_format($optiondata['trimCost'],2); ?></td>
					
					<td width="70" align="right"><?=number_format($optiondata['printEmb'],2); ?></td>
					<td width="70" align="right"><?=number_format($optiondata['wash'],2); ?></td>
					<td width="70" align="right"><?=number_format($optiondata['cm'],2); ?></td>
					<td width="70" align="right"><?=number_format($optiondata['other'],2); ?></td>
					<td width="70" align="right"><?=number_format($optiondata['totCost'],2); ?></td>
					<td align="right"><?=number_format($optiondata['fobPcs'],2); ?></td>
				 </tr>   
				<?
				$grand_fabCost+=$optiondata['fabCost'];
				$grand_trimCost+=$optiondata['trimCost'];
				$grand_printEmb+=$optiondata['printEmb'];
				$grand_wash+=$optiondata['wash'];
				
				$grand_cm+=$optiondata['cm'];
				$grand_other+=$optiondata['other'];
				$grand_totCost+=$optiondata['totCost'];
				$grand_fobPcs+=$optiondata['fobPcs'];
				$i++;
			}
            ?>
            </table>
        </div>
        <table width="<?=$width; ?>" cellspacing="0" border="1" class="tbl_bottom" rules="all">
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
    <? } 
	else if($type==2){
		?>
		 <table width="<?=$width; ?>" cellspacing="0" border="1" class="rpt_table" rules="all">
            <thead>
                <tr style="font-size:13px">
                    <th width="30">SL.</th> 
                    <th width="80">Cost Sheet No</th>
                    <th width="100">Buyer</th>   
                    <th width="100">Style Desc.</th>
                    <th width="100">Style Ref.</th>
                    <th width="80">Season</th>
                    <th width="80">Department</th>
                    <th width="80">Offer Qty</th>
                    <th width="60">UOM</th>
                    <th width="70">FOB Price</th>
                    
                    <th width="80">Amount</th>   
                    <th width="70">Delivery Date</th>
                    <th width="70">Costing Date</th>
                    <th width="80">Insert By</th>
                    <th width="30">Rv.</th>
                    <th width="30">Op.</th>
                    <th>Approved By</th>
                 </tr>
            </thead>
        </table>
        <div style="width:<?=$width; ?>; max-height:300px; overflow-y:scroll" id="scroll_body"> 
            <table width="<?=$inner_width; ?>" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
            <?
            /*$sql_item_summ="select mst_id, tot_fob_cost, tot_fab_cost, tot_accessories_cost, tot_cm_cost, tot_other_cost, tot_cost from qc_tot_cost_summary where status_active=1 and is_deleted=0";
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
            unset($sql_result_item_summ);*/
            
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
            
            if($costingstage_id==1)
            {
				$sql_mst="select a.id, a.qc_no, a.cost_sheet_no, a.lib_item_id, a.buyer_id, a.style_des, a.style_ref, a.season_id, a.department_id, a.offer_qty, a.uom, a.delivery_date, a.costing_date, a.inserted_by, a.approved, a.approved_by, a.approved_date, a.revise_no, a.option_id, a.entry_form, a.exchange_rate from qc_mst a, qc_confirm_mst b where a.qc_no=b.cost_sheet_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyerCond $seasonCond $deptCond $styleCond $costSheetNoCond $dateCond order by a.cost_sheet_no asc";// and a.revise_no=0 and a.option_id=0 
				$sql_result=sql_select($sql_mst);
				//echo $sql_mst;
				foreach($sql_result as $row)
				{
					$rowData_arr[$row[csf('cost_sheet_no')]][$row[csf('qc_no')]][$row[csf('revise_no')]][$row[csf('option_id')]]['all']=$row[csf('cost_sheet_no')].'***'.$row[csf('costing_date')].'***'.$row[csf('buyer_id')].'***'.$row[csf('style_ref')].'***'.$row[csf('style_des')].'***'.$row[csf('lib_item_id')].'***'.$row[csf('offer_qty')].'***'.$row[csf('delivery_date')].'***'.$row[csf('department_id')].'***'.$row[csf('uom')].'***'.$row[csf('approved')].'***'.$row[csf('inserted_by')].'***'.$row[csf('season_id')].'***'.$row[csf('department_id')].'***1'.'***'.$row[csf('approved_by')].'***'.$row[csf('approved_date')].'***'.$row[csf('exchange_rate')].'***'.$row[csf('entry_form')];
				}
				unset($sql_result);
				
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
				$sql_mst="select a.id, a.qc_no, a.cost_sheet_no, a.costing_date, a.buyer_id, a.style_ref, a.style_des, a.lib_item_id, a.offer_qty, a.delivery_date, a.season_id, a.department_id, a.uom, a.inserted_by, a.season_id, a.department_id, a.approved, a.approved_by, a.approved_date, a.revise_no, a.option_id, a.entry_form, c.buyer_agent_id,a.exchange_rate from qc_mst a, qc_tot_cost_summary c where a.qc_no=c.mst_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $buyerCond $seasonCond $deptCond $styleCond $costSheetNoCond $date_cond $confirmidCond order by a.cost_sheet_no asc";
				//echo $sql_mst;
				$sql_result=sql_select($sql_mst);
				foreach($sql_result as $row)
				{
					$rowData_arr[$row[csf('cost_sheet_no')]][$row[csf('qc_no')]][$row[csf('revise_no')]][$row[csf('option_id')]]['all']=$row[csf('cost_sheet_no')].'***'.$row[csf('costing_date')].'***'.$row[csf('buyer_id')].'***'.$row[csf('style_ref')].'***'.$row[csf('style_des')].'***'.$row[csf('lib_item_id')].'***'.$row[csf('offer_qty')].'***'.$row[csf('delivery_date')].'***'.$row[csf('department_id')].'***'.$row[csf('uom')].'***'.$row[csf('approved')].'***'.$row[csf('inserted_by')].'***'.$row[csf('season_id')].'***'.$row[csf('department_id')].'***2'.'***'.$row[csf('approved_by')].'***'.$row[csf('approved_date')].'***'.$row[csf('exchange_rate')].'***'.$row[csf('entry_form')];
				}
				unset($sql_result);
            }

            $data_binding=array();

            $key=0;

            foreach($rowData_arr as $costSheetNo=>$costdata)
            {
                foreach($costdata as $qc_no=>$qcdata)
                {
                    foreach($qcdata as $revise_no=>$revisedata)
                    {
                        foreach($revisedata as $option_id=>$optiondata)
                        {
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
                            $exchange_rate=83;
                            if(count($exData)>17)
                            {
                                $exchange_rate=$exData[17];
                            }
							$entryForm=$exData[18];
                            
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

                            $cstatus="";
                            if($isconfirm==1) $cstatus="Confirm"; else $cstatus="Pending";
                            if($approved==1 || $approved==3) $approvedby_time=$user_arr[$approved_by].'<br>'.$approved_date; else $approvedby_time="";
                            
                            if($delivery_date!="") $daysToHand=datediff("d",$costing_date,$delivery_date); else $daysToHand="";

                            $data_binding[$key]['marging']=$qc_margin_arr[$qc_no]['margin_percent'];
                            $data_binding[$key]['cost_sheet_no']=$cost_sheet_no;
                            $data_binding[$key]['qc_no']=$qc_no;
                            $data_binding[$key]['buyer']=$buyerArr[$buyer_id];
                            $data_binding[$key]['buyer_id']=$buyer_id;
                            $data_binding[$key]['style_des']=$style_des;
                            $data_binding[$key]['style_ref']=$style_ref;
                            $data_binding[$key]['season']=$season_arr[$season_id];
                            $data_binding[$key]['department']=$department_arr[$department_id];
                            $data_binding[$key]['offer_qty']=$offer_qty;
                            $data_binding[$key]['itemUom']=$itemUom;
                            $data_binding[$key]['fobPrice']=$fobPrice;
                            $data_binding[$key]['avl_min']=$qc_margin_arr[$qc_no]['avl_min'];
                            $data_binding[$key]['amount']=$amount;
                            $data_binding[$key]['delivery_date']=$delivery_date;
                            $data_binding[$key]['costing_date']=$costing_date;
                            $data_binding[$key]['inserted_by']=$user_arr[$inserted_by];
                            $data_binding[$key]['cstatus']=$cstatus;
                            $data_binding[$key]['revise_no']=$revise_no;
                            $data_binding[$key]['option_id']=$option_id;
                            $data_binding[$key]['approvedby_time']=$approvedby_time;
                            $data_binding[$key]['daysToHand']=$daysToHand;
                            $data_binding[$key]['fabCost']=$fabCost;
                            $data_binding[$key]['trimCost']=$trimCost;
                            $data_binding[$key]['printEmb']=$printEmb;
                            $data_binding[$key]['wash']=$wash;
                            $data_binding[$key]['cm']=$cm;
                            $data_binding[$key]['other']=$other;
                            $data_binding[$key]['totCost']=$totCost;
                            $data_binding[$key]['fobPcs']=$fobPcs;
                            $data_binding[$key]['exchange_rate']=$exchange_rate;
							$data_binding[$key]['entryForm']=$entryForm;
                            $key++;
                        }
                    }
                }
            }
            //echo $sql;
            //$sql_data=sql_select($sql_mst);
            $i=1; $tot_rows=0;
			foreach($data_binding as $key=>$optiondata)
            {
				if($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$cost_sheet_no=$optiondata['cost_sheet_no'];
                $qc_no=$optiondata['qc_no'];
				?>
				<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>">
					<td width="30"><?=$i; ?></td>
					<td width="80" title="<?=$qc_no; ?>"><a href='#report_details' onClick="generate_qc_report('<?=$qc_no; ?>','<?=$cost_sheet_no; ?>','<?='quick_costing_print'; ?>','<?=$optiondata['entryForm']; ?>');"><?=$optiondata['cost_sheet_no']; ?></a></td>
					<td width="100" style="word-break:break-all"><?=$optiondata['buyer']; ?></td>
					<td width="100" style="word-break:break-all"><?=$optiondata['style_des']; ?></td>
					<td width="100" style="word-break:break-all"><?=$optiondata['style_ref']; ?></td>
					
					<td width="80" style="word-break:break-all"><?=$optiondata['season']; ?></td>
					<td width="80" style="word-break:break-all"><?=$optiondata['department']; ?></td>
					<td width="80" align="right"><?=number_format($optiondata['offer_qty'],0); ?></td>
					<td width="60"><?=$optiondata['itemUom'];//$unit_of_measurement[$row[csf("uom")]]; ?></td>
                    
					<td width="70" align="right"><?=number_format($optiondata['fobPrice'],2); ?></td>
					<td width="80" align="right" style="word-break:break-all"><?=number_format($optiondata['amount'],2); ?></td>
					<td width="70"><?=change_date_format($optiondata['delivery_date']); ?></td>
					<td width="70"><?=change_date_format($optiondata['costing_date']); ?></td>
					<td width="80" style="word-break:break-all"><?=$optiondata['inserted_by']; ?></td>
					
					<td width="30" style="word-break:break-all; text-align:center">
                        <?php if($optiondata['revise_no']>0){?>
                        <a href="##" onClick="fnc_revise_details('<?=$cost_sheet_no;?>','revise_popup')"><?=$optiondata['revise_no']; ?></a>
                    <?php }
                    else{
                       echo $optiondata['revise_no'];
                     }?>
                        </td>
					<td width="30" style="word-break:break-all; text-align:center"><?=$optiondata['option_id']; ?></td>
					<td align="center" style="word-break:break-all"><?=$optiondata['approvedby_time']; ?></td>
				 </tr>   
				<?
				$grand_fabCost+=$optiondata['fabCost'];
				$grand_trimCost+=$optiondata['trimCost'];
				$grand_printEmb+=$optiondata['printEmb'];
				$grand_wash+=$optiondata['wash'];
				
				$grand_cm+=$optiondata['cm'];
				$grand_other+=$optiondata['other'];
				$grand_totCost+=$optiondata['totCost'];
				$grand_fobPcs+=$optiondata['fobPcs'];
				$i++;
			}
            ?>
            </table>
        </div>
		<?
	}
	?>
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

if($action=="costing_popup")
{
    echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
    extract($_REQUEST);
    ?>
    <script>
        //if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";
        var permission='<?=$permission; ?>';
      
        
        function frm_close()
        {
            parent.emailwindow.hide();
        }

       
    </script>
    <body >
    <div align="center" style="width:100%;">
        <?=load_freeze_divs ("../../../",'',1); 

        $sql_cost_summary=sql_select("select  id, mst_id, buyer_agent_id, location_id, no_of_pack, is_confirm, is_cm_calculative, mis_lumsum_cost, commision_per, tot_fab_cost, tot_sp_operation_cost, tot_accessories_cost, tot_cm_cost, tot_fright_cost, tot_lab_test_cost, tot_miscellaneous_cost, tot_other_cost, tot_commission_cost, tot_cost, tot_fob_cost, tot_rmg_ratio, commercial_cost from qc_tot_cost_summary where mst_id=$qc_no and status_active=1 and is_deleted=0");
        if(count($sql_cost_summary)>0){
            foreach($sql_cost_summary as $row)
            {
                $fabric_cost_qc     =$row[csf('tot_fab_cost')];
                $sp_operation_cost_qc =$row[csf('tot_sp_operation_cost')];
                $accessories_cost_qc=$row[csf('tot_accessories_cost')];
                //$avl_min_qc       =$row[csf('tot_fab_cost')];
                $cm_cost_qc         =$row[csf('tot_cm_cost')];
                $frieght_cost_qc    =$row[csf('tot_fright_cost')];
                $lab_test_cost_qc   =$row[csf('tot_lab_test_cost')];
                $mis_offer_qty_qc   =$row[csf('tot_miscellaneous_cost')];
                $other_cost_qc      =$row[csf('tot_other_cost')];
                $com_cost_qc        =$row[csf('tot_commission_cost')];
                $commercial_cost_qc =$row[csf('commercial_cost')];

                $fob_qc             =$row[csf('tot_cost')];
                $fob_pcs_qc         =$row[csf('tot_fob_cost')];
            }
        }

        $rate_data=''; $tot_cons='';
        $total_qc_yd_cost=$total_qc_yd_cost=$total_qc_knit_cost=$total_qc_df_cost=$total_qc_aop_cost=0;
        
        $sql_cons_rate=sql_select("select id, item_id, particular_type_id, is_calculation, rate_data, tot_cons, rate, ex_percent, value from qc_cons_rate_dtls where mst_id=$qc_no and type=1 and status_active=1 and is_deleted=0 order by id ");
        //echo "select id, particular_type_id, is_calculation, rate_data, tot_cons, ex_percent, value from qc_cons_rate_dtls where mst_id=$qc_no and type=1 and status_active=1 and is_deleted=0 order by id";
       
       $yarnIdArr=array(); $yarnQcRate=array(); $knittingIdArr=array(); $knitQcRate=array(); $dyeingIdArr=array(); $dyeingQcRate=array(); $aopIdArr=array(); $aopQcRate=array(); $mainfabricBodyQty=$ribBodyQty=$hoodBodyQty=$othersBodyQty=$totBodyconsQty=$ydsBodyQty=0; $withOutConsRateCost=0; $ydsAmount=0; $itemCountArr=array();
       $mainfabricBodyid=$ribBodyid=$hoodBodyid=$othersBodyid=0;
        foreach($sql_cons_rate as $row){
            if($row[csf('is_calculation')]==1 && $row[csf('rate_data')]!="")
            {
                $tot_cons =$row[csf('tot_cons')];
                $rate_data          =explode('~~',$row[csf('rate_data')]);
                
                if($rate_data[23]!="") 
                {
                    $actualCons=0;
                    $actualCons=$tot_cons;//$tot_cons*($rate_data[2]/100);
                    $yarnIdArr[$row[csf('id')]][$rate_data[23]]=$rate_data[23];
                    $yarnQcRate[$row[csf('id')]][$rate_data[23]]=$rate_data[3].'_'.$actualCons.'_'.$rate_data[2];
                }
                if($rate_data[24]!="") 
                {
                    $actualCons=0;
                    $actualCons=$tot_cons;//$tot_cons*($rate_data[6]/100);
                    $yarnIdArr[$row[csf('id')]][$rate_data[24]]=$rate_data[24];
                    $yarnQcRate[$row[csf('id')]][$rate_data[24]]=$rate_data[7].'_'.$actualCons.'_'.$rate_data[6];
                }
                if($rate_data[25]!="") 
                {
                    $actualCons=0;
                    $actualCons=$tot_cons;//$tot_cons*($rate_data[10]/100);
                    $yarnIdArr[$row[csf('id')]][$rate_data[25]]=$rate_data[25];
                    $yarnQcRate[$row[csf('id')]][$rate_data[25]]=$rate_data[11].'_'.$actualCons.'_'.$rate_data[10];
                }
                
                if($rate_data[27]!="") 
                {
                    $knittingIdArr[$row[csf('id')]][$rate_data[27]]=$rate_data[27];
                    $knitQcRate[$row[csf('id')]][$rate_data[27]]=$rate_data[28].'_'.$tot_cons;
                }
                if($rate_data[30]!="") 
                {
                    $knittingIdArr[$row[csf('id')]][$rate_data[30]]=$rate_data[30];
                    $knitQcRate[$row[csf('id')]][$rate_data[30]]=$rate_data[31].'_'.$tot_cons;
                }
                if($rate_data[33]!="") 
                {
                    $knittingIdArr[$row[csf('id')]][$rate_data[33]]=$rate_data[33];
                    $knitQcRate[$row[csf('id')]][$rate_data[33]]=$rate_data[34].'_'.$tot_cons;
                }
                
                if($rate_data[36]!="") 
                {
                    $dyeingIdArr[$row[csf('id')]][$rate_data[36]]=$rate_data[36];
                    $dyeingQcRate[$row[csf('id')]][$rate_data[36]]=$rate_data[37].'_'.$tot_cons;
                }
                if($rate_data[39]!="") 
                {
                    $dyeingIdArr[$row[csf('id')]][$rate_data[39]]=$rate_data[39];
                    $dyeingQcRate[$row[csf('id')]][$rate_data[39]]=$rate_data[40].'_'.$tot_cons;
                }
                if($rate_data[42]!="") 
                {
                    $dyeingIdArr[$row[csf('id')]][$rate_data[42]]=$rate_data[42];
                    $dyeingQcRate[$row[csf('id')]][$rate_data[42]]=$rate_data[43].'_'.$tot_cons;
                }
                
                if($rate_data[45]!="") 
                {
                    $aopIdArr[$row[csf('id')]][$rate_data[45]]=$rate_data[45];
                    $aopQcRate[$row[csf('id')]][$rate_data[45]]=$rate_data[46].'_'.$tot_cons;
                }
                if($rate_data[48]!="") 
                {
                    $aopIdArr[$row[csf('id')]][$rate_data[48]]=$rate_data[48];
                    $aopQcRate[$row[csf('id')]][$rate_data[48]]=$rate_data[49].'_'.$tot_cons;
                }
                if($rate_data[51]!="") 
                {
                    $aopIdArr[$row[csf('id')]][$rate_data[51]]=$rate_data[51];
                    $aopQcRate[$row[csf('id')]][$rate_data[51]]=$rate_data[52].'_'.$tot_cons;
                }
                
                //echo $rate_data[15].'='.$rate_data[17].'='.$tot_cons.'+++++++++'; 
                $total_qc_yd_cost+= $rate_data[18]*$tot_cons;
                
                if($row[csf('particular_type_id')]==1 || $row[csf('particular_type_id')]==20) 
                {
                    $mainfabricBodyQty+=$row[csf('tot_cons')];
                    $mainfabricBodyid=$row[csf('id')];
                }
                if($row[csf('particular_type_id')]==4)
                {
                    $ribBodyQty+=$row[csf('tot_cons')];
                    $ribBodyid=$row[csf('id')];
                }
                if($row[csf('particular_type_id')]==6 || $row[csf('particular_type_id')]==7) 
                {
                    $hoodBodyQty+=$row[csf('tot_cons')];
                    $hoodBodyid=$row[csf('id')];
                }
                if($row[csf('particular_type_id')]==998) 
                {
                    $othersBodyQty+=$row[csf('tot_cons')];
                    $othersBodyid=$row[csf('id')];
                }
                $totBodyconsQty+=$row[csf('tot_cons')];
                //if($row[csf('particular_type_id')]==999) $ydsBodyQty+=$row[csf('tot_cons')];
            }
            
            if($row[csf('is_calculation')]!=1 && $row[csf('particular_type_id')]!=999 ) $withOutConsRateCost+=$row[csf('tot_cons')]*$row[csf('rate')];
            if($row[csf('particular_type_id')]==999) { $ydsAmount+=$row[csf('tot_cons')]*$row[csf('rate')]; $ydsBodyQty+=$row[csf('tot_cons')]; }
            $itemCountArr[$row[csf('item_id')]]=$row[csf('item_id')];
        }
        $countItemId=count($itemCountArr);
        if($countItemId==1) $fabconsdisabled=""; else $fabconsdisabled="disabled";
        //echo $mainfabricBodyid.'='.$ribBodyid.'='.$hoodBodyid.'='.$othersBodyid;
        //print_r($knittingIdArr);
        $companyArr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
        $buyerArr = return_library_array("select id,short_name from lib_buyer ","id","short_name");
        $color_library_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
        $lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
        //echo $tot_cons; die;
        $sql_mst="select a.id, a.qc_no, a.company_id, a.location_id, a.cost_sheet_no, a.lib_item_id, a.buyer_id, a.style_des, a.style_ref, a.season_id, a.department_id, a.offer_qty, a.uom, a.delivery_date, a.costing_date, a.inserted_by, a.approved, a.approved_by, a.approved_date, a.revise_no, a.option_id, a.exchange_rate, a.costing_per from qc_mst a, qc_confirm_mst b where a.qc_no=$qc_no and a.qc_no=b.cost_sheet_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";// and (b.job_id is null or b.job_id =0) and a.approved not in(1,3)
        $sql_mst_res=sql_select($sql_mst); $costingper=0;
        foreach($sql_mst_res as $row){ 
            $cost_sheet_no = $row[csf('cost_sheet_no')];
            $style_des  = $row[csf('style_des')];
            $offer_qty  = $row[csf('offer_qty')];
            $style_ref  = $row[csf('style_ref')];
            $revise_no  = $row[csf('revise_no')];
            $option_id  = $row[csf('option_id')];
            $buyer_id   = $row[csf('buyer_id')];
            $buyer_name = $buyerArr[$row[csf('buyer_id')]];
            $company_name =  $companyArr[$row[csf('company_id')]];
            $company_id = $row[csf('company_id')];
            $location_id = $row[csf('location_id')];
            $costing_date = $row[csf('costing_date')];
            $exchange_rate = $row[csf('exchange_rate')];
            $costingper= $row[csf('costing_per')];
        }

        $sql_qc=sql_select("select id, qc_no, fabric_cost, accessories_cost, avl_min, cm_cost, frieght_cost, lab_test_cost, mis_offer_qty, other_cost, commercial_cost, com_cost, fob, fob_pcs, margin, margin_percent, total_yarn_cost, yarn_dyeing_cost, knitting_cost, df_cost, aop_cost, total_cost, buyer, cpm, smv, efficency, cm, available_min, special_operation, main_fabric_top, rib, hood, others, totbodycons, yds, fabricpurchasekg, fabricpurchaseyds from qc_margin_mst where qc_no=$qc_no and status_active=1 and is_deleted=0");
        $actualMainfabricBodyQty=$actualRibBodyQty=$actualHoodBodyQty=$actualOthersBodyQty=$actualTotBodyconsQty=$actualYdsBodyQty=0;
        if(count($sql_qc)>0){
            foreach($sql_qc as $row)
            {
                $update_id              =$row[csf('id')];
                $fabric_cost            =$row[csf('fabric_cost')];
                $accessories_cost       =$row[csf('accessories_cost')];
                $avl_min                =$row[csf('avl_min')];
                $cm_cost                =$row[csf('cm_cost')];
                $frieght_cost           =$row[csf('frieght_cost')];
                $lab_test_cost          =$row[csf('lab_test_cost')];
                $mis_offer_qty          =$row[csf('mis_offer_qty')];
                $other_cost             =$row[csf('other_cost')];
                $commercial_cost        =$row[csf('commercial_cost')];
                $com_cost               =$row[csf('com_cost')];
                $fob                    =$row[csf('fob')];
                $fob_pcs                =$row[csf('fob_pcs')];
                $margin                 =$row[csf('margin')];
                $margin_percent         =$row[csf('margin_percent')];
                $total_yarn_cost        =$row[csf('total_yarn_cost')];
                $yarn_dyeing_cost       =$row[csf('yarn_dyeing_cost')];
                $knitting_cost          =$row[csf('knitting_cost')];
                $df_cost                =$row[csf('df_cost')];
                $aop_cost               =$row[csf('aop_cost')];
                $buyer                  =$row[csf('buyer')];
                $cpm                    =$row[csf('cpm')];
                $smv                    =$row[csf('smv')];
                $efficency              =$row[csf('efficency')];
                $cm                     =$row[csf('cm')];
                $available_min          =$row[csf('available_min')];
                $sp_operation_cost      =$row[csf('special_operation')];
                
                $actualMainfabricBodyQty=$row[csf('main_fabric_top')];
                $actualRibBodyQty=$row[csf('rib')];
                $actualHoodBodyQty=$row[csf('hood')];
                $actualOthersBodyQty=$row[csf('others')];
                $actualTotBodyconsQty=$row[csf('totbodycons')];
                $actualYdsBodyQty=$row[csf('yds')];
                
                $withOutConsRateCost=$row[csf('fabricpurchasekg')];
                $ydsAmount=$row[csf('fabricpurchaseyds')];
                
                $update_button_active   =1;
            }
            //$buyer_name = return_field_value("short_name", "lib_buyer", "id='$buyer' and status_active = 1", "short_name");
        } 
        else 
        {
            if($actualMainfabricBodyQty==0) $actualMainfabricBodyQty=$mainfabricBodyQty;
            if($actualRibBodyQty==0) $actualRibBodyQty=$ribBodyQty;
            if($actualHoodBodyQty==0) $actualHoodBodyQty=$hoodBodyQty;
            if($actualOthersBodyQty==0) $actualOthersBodyQty=$othersBodyQty;
            if($actualTotBodyconsQty==0) $actualTotBodyconsQty=$totBodyconsQty;
            if($actualYdsBodyQty==0) $actualYdsBodyQty=$ydsBodyQty;
            
            $update_id=$fabric_cost=$accessories_cost=$avl_min=$cm_cost=$frieght_cost=$lab_test_cost=$mis_offer_qty=$other_cost=$com_cost=$fob=$margin=$margin_percent=$total_yarn_cost=$yarn_dyeing_cost=$knitting_cost=$df_cost=$aop_cost=$total_cost=$cpm=$smv=$efficency=$cm=$available_min='';
            $update_button_active   =0;

            $fabric_cost=$fabric_cost_qc;
            $accessories_cost=$accessories_cost_qc;
            $cm_cost=$cm_cost_qc;
            $frieght_cost=$frieght_cost_qc;
            $lab_test_cost=$lab_test_cost_qc;
            $mis_offer_qty=$mis_offer_qty_qc;
            $other_cost=$other_cost_qc;
            $commercial_cost=$commercial_cost_qc;
            $com_cost=$com_cost_qc;
            $sp_operation_cost=$sp_operation_cost_qc;
            
            $fob=$fabric_cost+$sp_operation_cost+$accessories_cost+$cm_cost+$frieght_cost+$lab_test_cost+$mis_offer_qty+$other_cost+$commercial_cost+$com_cost;
            $fob_pcs=$fob_pcs_qc;
            //$fob_pcs=$fob/12;
            $margin=$fob_qc-$fob;
            $margin_percent=($margin/$fob)*100;

            $applyingDate=date('d-M-Y');
            $cost_per_minute_variable=return_field_value("yarn_iss_with_serv_app as cost_per_minute","variable_order_tracking","company_name ='$company_id' and variable_list=67 and is_deleted=0 and status_active=1","cost_per_minute");
            if($cost_per_minute_variable=="" || $cost_per_minute_variable==0) $cost_per_minute_variable=0; else $cost_per_minute_variable=$cost_per_minute_variable;
            //echo $cost_per_minute_variable;
            // class="form_caption" style="font-size:large"
            if($db_type==0) $limit_cond="LIMIT 1"; else if($db_type==2) $limit_cond="";
            if($location_id>0 && $cost_per_minute_variable==1)
            {
                $sql="select b.cost_per_minute from lib_standard_cm_entry a, lib_standard_cm_entry_dtls b where a.id=b.mst_id and a.company_id='$company_id' and b.location_id='$location_id' and '$applyingDate' between b.applying_period_date and b.applying_period_to_date and b.status_active=1 and b.is_deleted=0 $limit_cond";
            }
            else 
            {
                $sql="select cost_per_minute from lib_standard_cm_entry where company_id='$company_id' and '$applyingDate' between applying_period_date and applying_period_to_date and status_active=1 and is_deleted=0";
            }
            //echo $sql;
            $dataSql=sql_select($sql); $cpm=0;
            foreach ($dataSql as $row)
            {
                if($row[csf("cost_per_minute")]!="") $cpm=$row[csf("cost_per_minute")];
            }
            unset($dataSql);
            
            $effPerSql="select sum(available_min) as available_min, sum(produce_min) as produce_min from production_logicsoft where buyer = '$buyer_name' and production_date<'$costing_date'";
            $effPerSqlData=sql_select($effPerSql); $efficency=0;
            foreach ($effPerSqlData as $row)
            {
                $efficency=($row[csf("produce_min")]/$row[csf("available_min")])*100;
            }
            unset($effPerSqlData);
            if(($efficency*1)!=0) $efficency=number_format($efficency,4,'.','');
            
            $yarnLibIdArr=array(); $knitLibIdArr=array(); $dyeLibIdArr=array(); $aopLibIdArr=array();
            foreach($yarnIdArr as $yrid=>$yiddata)
            {
                foreach($yiddata as $ylid)
                {
                    $yarnLibIdArr[$ylid]=$ylid;
                }
            }
            //print_r($yarnLibIdArr);
            
            foreach($knittingIdArr as $krid=>$kiddata)
            {
                foreach($kiddata as $klid)
                {
                    $knitLibIdArr[$klid]=$klid;
                }
            }
            //print_r($knitLibIdArr);
            
            foreach($dyeingIdArr as $drid=>$diddata)
            {
                foreach($diddata as $dlid)
                {
                    $dyeLibIdArr[$dlid]=$dlid;
                }
            }
            //print_r($dyeLibIdArr);
            
            foreach($aopIdArr as $arid=>$aiddata)
            {
                foreach($aiddata as $alid)
                {
                    $aopLibIdArr[$alid]=$alid;
                }
            }
            //print_r($aopLibIdArr);
            
            $yarnDataArr=array();
            if(implode(",",$yarnIdArr)!="")
            {
                $sql="select id, supplier_id, yarn_count, composition, percent, yarn_type, rate from lib_yarn_rate where status_active=1 and is_deleted=0 and id in (".implode(",",$yarnLibIdArr).") order by id desc";
                $data_array=sql_select($sql);
                foreach($data_array as $row)
                {
                    $yarnDataArr[$row[csf('id')]]=$lib_yarn_count[$row[csf('yarn_count')]].'_'.$yarn_type[$row[csf('yarn_type')]].'_'.$composition[$row[csf('composition')]].'_'.$row[csf('rate')];
                }
                unset($data_array);
            }
            
            $knittingDataArr=array();
            if(implode(",",$knittingIdArr)!="")
            {
                $sql="select id, body_part, const_comp, gsm, gauge, yarn_description, uom_id, in_house_rate, buyer_id from lib_subcon_charge where is_deleted=0 and rate_type_id=2 and status_active=1 and id in (".implode(",",$knitLibIdArr).") order by id desc";
                $data_array=sql_select($sql);
                foreach($data_array as $row)
                {
                    $knittingDataArr[$row[csf('id')]]=$body_part[$row[csf('body_part')]].'_'.$row[csf('const_comp')].'_'.$row[csf('yarn_description')].'_'.($row[csf('in_house_rate')]/$exchange_rate);
                }
                unset($data_array);
            }
            
            $dyeingDataArr=array();
            if(implode(",",$dyeingIdArr)!="")
            {
                $sql="select id, comapny_id, const_comp, process_type_id, process_id, color_id, width_dia_id, uom_id, buyer_id, in_house_rate, color_range_id from lib_subcon_charge where status_active=1 and is_deleted=0 and rate_type_id in (3,4,7,6) and id in (".implode(",",$dyeLibIdArr).")";
                $data_array=sql_select($sql);
                foreach($data_array as $row)
                {
                    $dyeingDataArr[$row[csf('id')]]=$color_library_arr[$row[csf('color_id')]].'_'.($row[csf('in_house_rate')]/$exchange_rate).'_'.$process_type[$row[csf('process_type_id')]].'_'.$color_range[$row[csf('color_range_id')]];
                }
                unset($data_array);
            }
            
            $aopDataArr=array();
            if(implode(",",$aopIdArr)!="")
            {
                $sql="select id, comapny_id, const_comp, process_type_id, process_id, color_id, width_dia_id, in_house_rate, uom_id, rate_type_id ,customer_rate, buyer_id, status_active,color_range_id from lib_subcon_charge where status_active=1 and is_deleted=0 and rate_type_id in (3,4,7,6) and process_id=35 and id in (".implode(",",$aopLibIdArr).")";
                $data_array=sql_select($sql);
                foreach($data_array as $row)
                {
                    $aopDataArr[$row[csf('id')]]=$color_library_arr[$row[csf('color_id')]].'_'.($row[csf('in_house_rate')]/$exchange_rate).'_'.$process_type[$row[csf('process_type_id')]].'_'.$color_range[$row[csf('color_range_id')]];
                }
                unset($data_array);
            }
        }
        if($costingper==2) $costingcap="$/PCS"; else if($costingper==1) $costingcap="$/DZN"; else $costingcap="";
        ?>
        <fieldset style="width:835px ">
            <legend><?="Company:$company_name; Cost Sheet No : $cost_sheet_no;  Option: $option_id; Revise No: $revise_no; Style Desc.:$style_des; Style Ref.: $style_ref; Costing Per: $qccosting_per[$costingper]"; ?><input type="hidden" class="text_boxes" name="cbo_costingper_id" id="cbo_costingper_id" value="<?=$costingper; ?>" ></legend>
        
        <form name="quick_cosing_entry" id="quick_cosing_entry" enctype="multipart/form-data" method="post">
            <div style="float: left">
                <div style="width: 250px; float: left;">
                    <table width="250" cellpadding="2" cellspacing="2" class="rpt_table" border="1" rules="all" align="left">
                        <thead>
                            <tr>
                                <th colspan="3">Marketing Actual Cost</th>
                            </tr>       
                            <tr>
                                <th width="120">Description</th>
                                <th width="65">QC Cost</th>
                                <th width="65">Actual Cost</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td width="120">Fabric</td> 
                                <td width="65"><p><? echo $fabric_cost_qc; ?></p></td>
                                <td width="65"><p><? echo $fabric_cost; ?></p></td>
                            </tr>
                            <tr>
                                <td width="120">Special Operation</td>
                                <td width="65"><p><? echo $sp_operation_cost_qc; ?></p></td>
                                <td width="65"><p><? echo $sp_operation_cost; ?></p></td>
                            </tr>
                            <tr>
                                <td width="120">Accessories</td>
                                <td width="65"><p><? echo $accessories_cost_qc; ?></p></td>
                                <td width="65"><p><? echo $accessories_cost; ?></p></td>
                            </tr>
                            <tr>
                                <td width="120">CM (<?=$costingcap; ?>)</td>
                                <td width="65"><p><?=$cm_cost_qc; ?></p></td>
                                <td width="65" title="(((CPM*100)/Efficiency)*SMV)*<?=$costingcap; ?>"><p><?=$cm_cost; ?></p></td>
                            </tr>
                            <tr>
                                <td width="120">Frieght Cost(<?=$costingcap; ?>)</td>
                                <td width="65"><p><? echo $frieght_cost_qc; ?></p></td>
                                <td width="65"><p><? echo $frieght_cost; ?></p></td>
                            </tr>
                            <tr>
                                <td width="120">Lab - Test(<?=$costingcap; ?>)</td>
                                <td width="65"><p><? echo $lab_test_cost_qc; ?></p></td>
                                <td width="65"><p><? echo $lab_test_cost; ?></p></td>
                            </tr>
                            <tr>
                                <td width="120">Mis/Offer Qty.</td>
                                <td width="65"><p><? echo $mis_offer_qty_qc; ?></p></td>
                                <td width="65"><p><? echo $mis_offer_qty; ?></p></td>
                            </tr>
                            <tr>
                                <td width="120">Other Cost(<?=$costingcap; ?>)</td>
                                <td width="65"><p><? echo $other_cost_qc; ?></p></td>
                                <td width="65"><p><? echo $other_cost; ?></p></td>
                            </tr>
                            <tr>
                                <td width="120">Commercial Cost</td>
                                <td width="65"><p><?=$commercial_cost_qc; ?></p></td>
                                <td width="65"><p><?=$commercial_cost; ?></p></td>
                            </tr>
                            <tr>
                                <td width="120">Com.(%)(<?=$costingcap; ?>)</td>
                                <td width="65"><p><? echo $com_cost_qc; ?></p></td>
                                <td width="65"><p><? echo $com_cost; ?></p></td>
                            </tr>
                            <tr>
                                <td width="120"><strong>F.O.B(<?=$costingcap; ?>)</strong></td>
                                <td width="65"><p><?=$fob_qc; ?></p></td>
                                <td width="65" title="fabric_cost+special_operation+accessories_cost+cm_dzn+frieght_dzn+lab_dzn+mis_offer_qty+other_cost_dzn+com_dzn"><p><? echo $fob; ?></p></td>
                            </tr>
                            <tr>
                                <td width="120">F.O.B($/PCS)</td>
                                <td width="65"><p><?=$fob_pcs_qc; ?></p></td>
                                <td width="65"><p><? echo $fob_pcs; ?></p></td>
                            </tr>
                            <tr>
                                <td width="120" >Margin Per/<?=$costingcap; ?></td>
                                <td colspan="2" title="F.O.B($/PCS)-Cost - F.O.B(<?=$costingcap; ?>)" ><p><? echo number_format($margin,4); ?></p>
                               
                                </td>
                            </tr>
                            <tr>
                                <td width="120" >Margin %</td>
                                <td colspan="2" title="Margin Per/DZN * 100"><p><?=number_format($margin_percent,4); ?></p></td>
                            </tr>
                            <tr>
                                <td colspan="3">&nbsp;</td>
                            </tr>
                            <tr>
                                <td width="120">AVL Min.</td>
                                <td width="65"><p><? echo $avl_min_qc; ?></p></td>
                                <td width="65"><p><? echo $avl_min; ?></p></td>
                            </tr>
                        </tbody>
                    </table>
                    <table width="250" cellpadding="2" cellspacing="2" class="rpt_table" border="1" rules="all" align="left">
                        <thead>
                            <tr>
                                <th colspan="2">CM Cost</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td width="150">Buyer Name</td>
                                <td width="100" align="center"><strong><? echo $buyer_name; ?></strong>
                                    
                                </td>
                            </tr>
                            <tr>
                                <td width="150">CPM</td>
                                <td width="100"><p><?=$cpm; ?></p></td>
                            </tr>
                            <tr>
                                <td width="150">SMV</td>
                                <td width="100"><p><?=$smv; ?></p></td>
                            </tr>
                            <tr>
                                <td width="150">Efficency %</td>
                                <td width="100"><p><?=$efficency; ?></p></td>
                            </tr>
                            <tr>
                                <td width="150">CM</td>
                                <td width="100" title="((((cpm*100)/efficency)*smv)*<?=$costingcap; ?>)/ex_rate"><p><?=$cm; ?></p></td>
                            </tr>
                            <tr>
                                <td width="150">Available Minutes</td>
                                <td width="100" title="(smv*offer_qty)/(efficency/100)"><p><? echo $available_min; ?></p></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2">
                                <?
                                $data_array2=sql_select("select image_location from common_photo_library where master_tble_id='$qc_no' and form_name='qcv2img' and is_deleted=0 and file_type=1");
                                foreach($data_array2 as $img_row)
                                {
                                    ?>
                                    <img src='../../../<? echo $img_row[csf('image_location')]; ?>' height='70' width='50' align="middle" />   
                                    <? 
                                }
                                ?>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div style="width: 570px; float: right;">
                    <table width="550" cellpadding="2" cellspacing="2" class="rpt_table" border="1" rules="all" align="left">
                        <thead>
                            <tr>
                                <th colspan="7">Fabric Consuption </th>
                            </tr>       
                            <tr>
                                <th width="100">Details</th>
                                <th width="75">Main Fabric Top</th>
                                <th width="75">Rib</th>
                                <th width="75">Hood</th>
                                <th width="70">Other</th>
                                <th width="80">Total Cons.</th>
                                <th>Yds Cons.</th>
                            </tr>
                        </thead>
                    </table>
                    <div style="width:570px; max-height:85px; float: left; overflow-y:scroll" id="scroll_body">
                        <table width="550" cellpadding="2" cellspacing="2" class="rpt_table" border="1" rules="all" align="left" id="tbl_bodyPart">
                            <tbody>
                                <tr>
                                    <td width="100"><strong>QC Cons.</strong></td>
                                    <td width="75"><p><?=$mainfabricBodyQty; ?></p></td>
                                    <td width="75"><p><?=$ribBodyQty; ?></p></td>
                                    <td width="75"><p><?=$hoodBodyQty; ?></p></td>
                                    <td width="70"><p><?=$othersBodyQty; ?></p></td>
                                    <td width="80"><p><?=$totBodyconsQty; ?></p></td>
                                    <td><p><?=$ydsBodyQty; ?></p></td>
                                </tr>
                                <tr>
                                    <td width="100"><strong>Actual Cons.</strong></td>
                                    <td width="75"><p><?=$actualMainfabricBodyQty; ?></p></td>
                                    <td width="75"><p><?=$actualRibBodyQty; ?></p></td>
                                    <td width="75"><p><?=$actualHoodBodyQty; ?></p></td>
                                    <td width="70"><p><?=$actualOthersBodyQty; ?></p></td>
                                    <td width="80"><p><?=$actualTotBodyconsQty; ?></p></td>
                                    <td><p><?=$actualYdsBodyQty; ?></p></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <table width="550" cellpadding="2" cellspacing="2" class="rpt_table" border="1" rules="all" align="left">
                        <thead>
                            <tr>
                                <th colspan="7">Yarn details</th>
                            </tr>       
                            <tr>
                                <th width="100">Yarn Count</th>
                                <th width="100">Yarn Type</th>
                                <th width="100">Composition Name</th>
                                <th width="100">Yarn details</th>
                                <th width="50">%</th>
                                <th width="50">QC Rate</th>
                                <th>Actual Rate</th>
                            </tr>
                        </thead>
                    </table>
                    <div style="width:570px; max-height:85px; float: left; overflow-y:scroll" id="scroll_body">
                        <table width="550" cellpadding="2" cellspacing="2" class="rpt_table" border="1" rules="all" align="left" id="tbl_yarn_cost">
                            <tbody>
                                <?
                                if($update_id!='')
                                {
                                    $yarn_dtls_update=sql_select("select  id, qc_no, mst_id, type, lib_table_id, rate_data_id, yarn_count, yarn_type, yarn_details, qc_rate, actual_rate, febric_type, color_type, color, composition, body_part, feb_desc, yarn_desc, process, tot_cons, actual_cost, ex_percent from qc_margin_dtls where mst_id=$update_id and qc_no=$qc_no and type=1 and status_active=1 and is_deleted=0");
                                    $i=1;
                                    foreach($yarn_dtls_update as $row)
                                    {
                                        ?>
                                        <tr> 
                                            <td width="100"><p><?=$row[csf('yarn_count')]; ?></p>
                                            </td>
                                            <td width="100"><p><?=$row[csf('yarn_type')]; ?></p></td>
                                            <td width="100"><p><?=$row[csf('composition')]; ?></p></td>
                                            <td width="100"><p><?=$row[csf('yarn_details')]; ?></p></td>
                                            <td width="50"><p><?=$row[csf('ex_percent')]; ?></p></td>
                                            <td width="50" titel="<?=$row[csf('tot_cons')]; ?>"><p><?=$row[csf('qc_rate')]; $total_qc_rate +=$row[csf('qc_rate')]*$row[csf('tot_cons')]; ?></p></td>
                                            <td><p><?=$row[csf('actual_rate')]; ?></p></td>
                                        </tr>
                                        <? $i++;
                                    }
                                }
                                else
                                {
                                    if(count($yarnDataArr)>0)
                                    {
                                        $i=1; $tot_cons_yarn=''; $rate_data=''; $rate_data_id=''; $ex_percent_yarn=''; $qcData="";
                                        foreach($yarnIdArr as $rid=>$iddata)
                                        //foreach($yarnDataArr as $yid=>$ydata)
                                        {
                                            foreach($iddata as $yid=>$ydata)
                                            {
                                                $rate_data=explode('_',$yarnDataArr[$yid]);
                                                $yCount=$yType=$compo=$yRate=$yPer="";
                                                
                                                $yCount=$rate_data[0];
                                                $yType=$rate_data[1];
                                                $compo=$rate_data[2];
                                                $yRate=number_format($rate_data[3],4);
                                                
                                                $rate_data_id=$rid; 
                                                $qcData=explode('_',$yarnQcRate[$rid][$yid]);
                                                $qcRate=$qcData[0];
                                                $tot_cons_yarn =$qcData[1];
                                                $yPer =$qcData[2];
                                                $actualcostyarn=($tot_cons_yarn*$yRate*($yPer/100));
                                                $total_yarn_cost+=$actualcostyarn;
                                                //echo $tot_cons_yarn*(($yPer*1)/100)*$yRate.'<br>';
                                                //$actual_cons_yarn=$tot_cons_yarn*($yPer/100);
                                                $yarnDtls="";
                                                
                                                if($yCount!="") $yarnDtls.=$yCount;
                                                if($yType!="") $yarnDtls.=', '.$yType;
                                                if($compo!="") $yarnDtls.=', '.$compo;
                                                
                                                ?>
                                                <tr> 
                                                    <td width="100" title="<?=$tot_cons_yarn.'='.$yPer.'='.$yRate; ?>">
                                                        <p><?=$yCount; ?></p>
                                                    </td>
                                                    <td width="100"><p><?=$yType; ?></p></td>
                                                    <td width="100"><p><?=$compo; ?></p></td>
                                                    <td width="100"><p><?=$yarnDtls; ?></p></td>
                                                    <td width="50"><p><?=$yPer; ?></p></td>
                                                    <td width="50" titel="<?=$tot_cons_yarn; ?>"><p><?=$qcRate; $total_qc_rate+=$qcRate*$tot_cons_yarn; ?></p> </td>
                                                    <td><p><?=$yRate; ?></p></td>
                                                </tr>
                                                <? 
                                                $i++;
                                            }
                                        }
                                    }
                                    else
                                    {
                                        ?>
                                        <tr> 
                                            <td width="100">
                                                
                                            </td>
                                            <td width="100"></td>
                                            <td width="100"></td>
                                            <td width="100"></td>
                                            <td width="50"></td>
                                            <td width="50"> </td>
                                            <td></td>
                                        </tr>
                                        <?
                                    }
                                }
                                ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="5" align="right"><strong>Total Yarn Cost</strong></td>
                                <td width="50"><p><?=$total_qc_rate; ?></p></td>
                                <td width="50" ><p><?=$total_yarn_cost; ?></p></td>
                            </tr>
                            <tr>
                                <td colspan="5" align="right">Yarn Dyeing Cost</td>
                                <td width="50"><p><? echo $total_qc_yd_cost; ?></p> </td>
                                <td width="50"><p><? echo $yarn_dyeing_cost; ?></p> </td>
                            </tr>
                        </tfoot>
                        </table>
                    </div>
                    
                    <table width="550" cellpadding="2" cellspacing="2" class="rpt_table" border="1" rules="all" align="left">
                        <thead>
                            <tr>    

                                <th colspan="5">Knitting Details</th>
                            </tr>       
                            <tr>
                                <th width="150">Body Part</th>
                                <th width="150">Fabric Construction</th>
                                <th width="100">Yarn Description</th>
                                <th width="75">QC Rate</th>
                                <th>Actual Rate</th>
                            </tr>
                        </thead>
                    </table>
                    <div style="width:570px; max-height:65px; float: left; overflow-y:scroll" id="scroll_body">
                        <table width="550" cellpadding="2" cellspacing="2" class="rpt_table" border="1" rules="all" align="left" id="tbl_kniting_cost">
                            <tbody>
                                <? $total_qc_knit_cost=0;
                                if($update_id!='')
                                {
                                    $knit_dtls_update=sql_select("select  id, qc_no, mst_id, type, lib_table_id, rate_data_id, yarn_count, yarn_type, yarn_details, qc_rate, actual_rate, febric_type, color_type, color, composition, body_part, feb_desc, yarn_desc, process, tot_cons from qc_margin_dtls where mst_id=$update_id and qc_no=$qc_no and type=2 and status_active=1 and is_deleted=0");
                                    $j=1;
                                    foreach($knit_dtls_update as $row){ 
                                        ?>
                                        <tr> 
                                            <td width="150"><p><? echo $row[csf('body_part')]; ?></p></td>
                                            <td width="150"><p><? echo $row[csf('feb_desc')]; ?></p></td>
                                            <td width="100"><p><? echo $row[csf('yarn_desc')]; ?></p></td>
                                            <td ><p><? echo $row[csf('qc_rate')]; $total_qc_knit_cost+=$row[csf('tot_cons')]*$row[csf('qc_rate')]; ?></p></td>
                                            <td ><p><? echo $row[csf('actual_rate')]; ?></p></td>
                                        </tr>
                                        <? $j++;
                                    }
                                }
                                else
                                {
                                    if(count($knittingDataArr)>0)
                                    {
                                        $j=1; $tot_cons_knit=''; $rate_data=''; $rate_data_id=''; $qcData="";
                                        foreach($knittingIdArr as $kid=>$kiddata)
                                        //foreach($knittingIdArr as $krid=>$kiddata)
                                        {
                                            foreach($kiddata as $krid=>$knitdata)
                                            {
                                            $rate_data=explode('_',$knittingDataArr[$krid]);
                                            $rate_data_id=$krid;
                                            
                                            $qcData=explode('_',$knitQcRate[$kid][$krid]);
                                            $qcRate =$qcData[0];
                                            $tot_cons_knit =$qcData[1];
                                            $bodyPart=$const_comp=$yarn_description=""; $in_house_rate=0;
                                            
                                            $bodyPart=$rate_data[0];
                                            $const_comp=$rate_data[1];
                                            $yarn_description=$rate_data[2]; 
                                            $in_house_rate=number_format($rate_data[3],4);
                                            
                                            ?>
                                                <tr> 
                                                    <td width="150"><p><?=$bodyPart; ?></p></td>
                                                    <td width="150"><p><?=$const_comp; ?></p></td>
                                                    <td width="100"><p><?=$yarn_description; ?></p></td>
                                                    <td ><p><?=$qcRate; $total_qc_knit_cost+=$tot_cons_knit*$qcRate; ?></p></td>
                                                    <td><p><?=$in_house_rate; ?></p></td>
                                                </tr>
                                            <? $j++;
                                            }
                                        }
                                    }
                                    else
                                    {
                                        ?>
                                        <tr> 
                                            <td width="150"></td>
                                            <td width="150"></td>
                                            <td width="100"></td>
                                            <td ></td>
                                            <td></td>
                                        </tr>
                                        <?  
                                    }
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" align="right"><strong>Knitting Cost</strong></td>
                                    <td width="74"><p><? echo $total_qc_knit_cost; ?></p></td>
                                    <td width="74"><p><? echo $knitting_cost; ?></p></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                   
                    
                    <table width="550" cellpadding="2" cellspacing="2" class="rpt_table" border="1" rules="all" align="left">
                        <thead>
                            <tr>
                                <th colspan="5">Dyeing Finishing Details</th>
                            </tr>       
                            <tr>
                                <th width="150">Color Range</th>
                                <th width="150">Color Name</th>
                                <th width="100">Process Name</th>
                                <th width="75">QC Rate</th>
                                <th>Actual Rate</th>
                            </tr>
                        </thead>
                    </table>
                    <div style="width:570px; max-height:55px; float: left; overflow-y:scroll" id="scroll_body">
                        <table width="550" cellpadding="2" cellspacing="2" class="rpt_table" border="1" rules="all" align="left" id="tbl_df">
                            <tbody>
                                <?
                                if($update_id!='')
                                {
                                    $df_dtls_update=sql_select("select  id, qc_no, mst_id, type, lib_table_id, rate_data_id, yarn_count, yarn_type, yarn_details, qc_rate, actual_rate, febric_type, color_type, color, composition, body_part, feb_desc, yarn_desc, process, tot_cons from qc_margin_dtls where mst_id=$update_id and qc_no=$qc_no and type=3 and status_active=1 and is_deleted=0");
                                    $k=1;
                                    foreach($df_dtls_update as $row){ 
                                        ?>
                                        <tr>
                                            <td width="150"><p><? echo $row[csf('color_type')]; ?></p></td>
                                            <td width="150"><p><? echo $row[csf('color')]; ?></p></td>
                                            <td width="100"><p><? echo $row[csf('process')]; ?></p></td>
                                            <td width="75" ><p><? echo $row[csf('qc_rate')]; $total_qc_df_cost+=$row[csf('qc_rate')]*$row[csf('tot_cons')];  ?></p></td>
                                            <td ><p><? echo $row[csf('actual_rate')]; ?></p></td>
                                        </tr>
                                        <? $k++;
                                    }
                                }
                                else
                                {
                                    if(count($dyeingDataArr)>0)
                                    {
                                        $k=1; $tot_cons_df=$qcData=''; $rate_data=''; $rate_data_id=''; $qcData="";
                                        foreach($dyeingIdArr as $drid=>$diddata)
                                       //foreach($dyeingDataArr as $did=>$ddata)
                                        {
                                            foreach($diddata as $did=>$ddata)
                                            {
                                            //echo $ddata;
                                            $rate_data=explode('_',$dyeingDataArr[$did]);
                                            $rate_data_id=$did;
                                            $qcData=explode('_',$dyeingQcRate[$drid][$did]);
                                            $qcRate=$qcData[0];
                                            $tot_cons_df =$qcData[1];
                                            
                                            $colorName=$in_house_rate=$process_type_id=$color_range_id="";
                                            
                                            $colorName=$rate_data[0];
                                            $in_house_rate=number_format($rate_data[1],4);
                                            $process_type_id=$rate_data[2];
                                            $color_range_id=$rate_data[3];
                                            
                                            ?>
                                            <tr>
                                                <td width="150"><p><?=$color_range_id; ?></p></td>
                                                <td width="150"><p><?=$colorName; ?></p></td>
                                                <td width="100"><p><?=$process_type_id; ?></p></td>
                                                <td width="75"><p><?=$qcRate; $total_qc_df_cost+=$qcRate*$tot_cons_df; ?></p></td>
                                                <td ><p><?=$in_house_rate; ?></p></td>
                                            </tr>
                                            <? $k++;
                                            }
                                        }
                                    }
                                    else
                                    {
                                        ?>
                                        <tr>
                                            <td width="150"></td>
                                            <td width="150"></td>
                                            <td width="100"></td>
                                            <td width="75" ></td>
                                            <td ></td>
                                        </tr>
                                        <?
                                    }
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" align="right"><strong>Dyeing Finishing Cost</strong></td>
                                    <td width="74" ><p><?=$total_qc_df_cost; ?></p></td>
                                    <td width="74"  ><p><?=$df_cost; ?></p></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                   
                    
                    <table width="550" cellpadding="2" cellspacing="2" class="rpt_table" border="1" rules="all" align="left">
                        <thead>
                            <tr>
                                <th colspan="5">AOP Cost Details</th>
                            </tr>       
                            <tr>
                                <th width="150">Color Range</th>
                                <th width="150">Color Name</th>
                                <th width="100">Process Name</th>
                                <th width="75">QC Rate</th>
                                <th>Actual Rate</th>
                            </tr>
                        </thead>
                    </table>
                    <div style="width:570px; max-height:55px; float: left; overflow-y:scroll" id="scroll_body">
                        <table width="550" cellpadding="2" cellspacing="2" class="rpt_table" border="1" rules="all" align="left" id="tbl_aop">
                            <tbody>
                                <?
                                if($update_id!='')
                                {
                                    $aop_dtls_update=sql_select("select  id, qc_no, mst_id, type, lib_table_id, rate_data_id, yarn_count, yarn_type, yarn_details, qc_rate, actual_rate, febric_type, color_type, color, composition, body_part, feb_desc, yarn_desc, process, tot_cons,actual_cost from qc_margin_dtls where mst_id=$update_id and qc_no=$qc_no and type=4 and status_active=1 and is_deleted=0");
                                    //echo $update_id;
                                    $m=1;
                                    foreach($aop_dtls_update as $row){ 
                                        ?>
                                        <tr>
                                            <td width="150"><p><? echo $row[csf('color_type')]; ?></p></td>
                                            <td width="150"><p><? echo $row[csf('color')]; ?></p></td>
                                            <td width="100"><p><? echo $row[csf('process')]; ?></p></td>
                                            <td width="75" ><p><? echo $row[csf('qc_rate')]; $total_qc_aop_cost+=$row[csf('qc_rate')]*$row[csf('tot_cons')]; ?></p></td>
                                            <td ><p><? echo $row[csf('actual_rate')]; ?></p></td>
                                               
                                        </tr>
                                        <? $m++;
                                    }
                                }
                                else
                                {
                                    if(count($aopDataArr)>0)
                                    {
                                        $m=1; $tot_cons_aop=''; $rate_data=$qcData=''; $rate_data_id=''; $qcData="";
                                        foreach($aopIdArr as $arid=>$aiddata)
                                        //foreach($aopDataArr as $aid=>$aopData)
                                        {
                                            foreach($aiddata as $aid=>$aopData)
                                            {
                                            $rate_data=explode('_',$aopDataArr[$aid]);
                                            
                                            $qcData=explode('_',$aopQcRate[$arid][$aid]);
                                            $qcRate=$qcData[0];
                                            $tot_cons_aop =$qcData[1];
                                            
                                            $colorName=$in_house_rate=$process_type_id=$color_range_id="";
                                            
                                            $colorName=$rate_data[0];
                                            $in_house_rate=number_format($rate_data[1],4);
                                            $process_type_id=$rate_data[2];
                                            $color_range_id=$rate_data[3];
                                            
                                            ?>
                                            <tr>
                                                <td width="150"><?=$color_range_id; ?></td>
                                                <td width="150"><?=$colorName; ?></td>
                                                <td width="100"><p><?=$process_type_id; ?></p></td>
                                                <td width="75" ><p><?=$qcRate; $total_qc_aop_cost+=$qcRate*$tot_cons_aop; ?></p></td>
                                                <td ><p><?=$in_house_rate; ?></p></td>
                                            </tr>
                                            <? $m++;
                                            }
                                        }
                                    }
                                    else
                                    {
                                        ?>
                                        <tr>
                                            <td width="150"></td>
                                            <td width="150"></td>
                                            <td width="100"></td>
                                            <td width="75" ></td>
                                            <td > </td>
                                        </tr>
                                        <?  
                                    }
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" align="right"><strong>AOP Cost</strong></td>
                                    <td width="74"><p><?=$total_qc_aop_cost; ?></p></td>
                                    <td width="74"><p><?=$aop_cost; ?></p></td>
                                </tr>
                                <tr>
                                    <td colspan="4" align="right"><strong>Fabric Purchase [Kg]</strong></td>
                                    <td width="74"><p><?=$withOutConsRateCost; ?></p></td>
                                </tr>
                                <tr>
                                    <td colspan="4" align="right"><strong>Fabric Purchase Cost[Yds]</strong></td>
                                    <td width="74"><p><?=$ydsAmount; ?></p></td>
                                </tr>
                                <tr>
                                    <?
                                        $qc_total_cost=$total_qc_aop_cost+$total_qc_df_cost+$total_qc_knit_cost+$total_qc_yd_cost+$total_qc_rate; 
                                        $total_cost=$total_yarn_cost+$yarn_dyeing_cost+$knitting_cost+$df_cost+$aop_cost+$withOutConsRateCost+$ydsAmount; 
                                        $total_fab_cost=$total_cost*$tot_cons;
                                        $fabric_cost_qc=$total_qc_rate+$total_qc_yd_cost+$total_qc_knit_cost+$total_qc_df_cost+$total_qc_aop_cost+$withOutConsRateCost+$ydsAmount;
                                    ?>
                                    <td colspan="3" align="right"><strong>Fabric Total Cost</strong></td>
                                    <td width="74"><p><? echo $fabric_cost_qc; //$qc_total_cost; ?></p></td>
                                    <td width="74"><p><? echo $total_cost; ?></p></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                </div>
            </div>
            <div style="width: 100%">
                <table  style="width: 100%">
                    <tr>
                        <td height="50" valign="middle" align="center" class="button_container">
                           
                      

                        </td>
                    </tr>
                </table>
            </div>
            <div style="width:100%"><p align="center"><input type="button" id="btn_close" class="formbutton" style="width:100px;" value="Close" onClick="frm_close();" ></p></div>
        </form>
    </div>
    </fieldset>
    <script type="text/javascript">
        $('#txt_fabric').val(<?=$total_cost; ?>);
    </script>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    </html>
    <?
    exit();
}
if($action=="revise_popup")
{
    echo load_html_head_contents("Revise Info", "../../../", 1, 1,'','','');
    extract($_REQUEST);
    $buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name");
    $country_name_library=return_library_array( "select id, country_name from lib_country", "id", "country_name");
    $insertUArr=return_library_array( "select id, user_full_name from user_passwd",'id','user_full_name');
    //echo $po_id; die;
    $sqlMst=sql_select("Select cost_sheet_no, style_ref, buyer_id, costing_date, inserted_by from qc_mst where cost_sheet_no='$cost_sheet_no' and status_active=1 and is_deleted=0");
    ?>
    <!--    <div style="width:780px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
    --> <fieldset style="width:580px; margin-left:3px">
        <div id="scroll_body" align="center">
            <table border="1" class="rpt_table" rules="all" width="560" cellpadding="0" cellspacing="0" align="center">
                <thead>
                    <tr>
                        <th colspan="9" style="word-break:break-all">Buyer Name:<?=$buyer_short_name_library[$sqlMst[0][csf('buyer_id')]]; ?> Cost Sheet No:<?=$cost_sheet_no; ?> Style Ref:<?=$sqlMst[0][csf('style_ref')]; ?> Costing Date:<?=change_date_format($sqlMst[0][csf('costing_date')]); ?> InsertBy:<?=$insertUArr[$sqlMst[0][csf('inserted_by')]]; ?></th>
                    </tr>
                    <tr>
                        <th width="30" rowspan="2">SL</th>
                        <th width="60" rowspan="2">Revise No</th>
                        <th width="60" rowspan="2">Option</th>
                        <th colspan="2">QC</th>
                        <th width="60" rowspan="2">FOB</th>
                        <th width="60" rowspan="2">1st Margin[Dzn]</th>
                        <th width="60" rowspan="2">Deferrence</th>
                        <th rowspan="2">Revise and Option Date</th>
                    </tr>
                    <tr>
                        <th width="60">Margin $[Dzn]</th>
                        <th width="60">Margin %</th>
                    </tr>
                </thead>
                <tbody>
                <?
                $sql="Select a.cost_sheet_no, a.revise_no, a.option_id, a.insert_date, b.margin, b.margin_percent, c.tot_fob_cost from qc_mst a, qc_margin_mst b, qc_tot_cost_summary c where a.qc_no=b.qc_no and a.qc_no=c.mst_id and b.qc_no=c.mst_id and a.cost_sheet_no='$cost_sheet_no' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.revise_no, a.option_id ASC";
                $sqlMargin=sql_select($sql); $i=1;
                foreach($sqlMargin as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    
                    
                    //echo $row[csf('revise_no')].'='.$row[csf('option_id')];
                    $revOptionDate="";
                    if($row[csf('revise_no')]==0 && $row[csf('option_id')]==0)
                    {
                        $actualMargin=$row[csf('margin')];
                        $revOptionDate="";
                    }
                    else
                    {
                        $revOptionDate=$row[csf('insert_date')];
                    }
                    //echo $actualMargin;
                    
                    $diffMargin=0;
                    $diffMargin=$row[csf('margin')]-$actualMargin;
                    ?>
                    <tr bgcolor="<?=$bgcolor; ?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>');" id="tr_<?=$i;?>">
                        <td width="30" align="center"><?=$i; ?></td>
                        <td width="60" align="center"><?=$row[csf('revise_no')]; ?></td>
                        <td width="60" align="center"><?=$row[csf('option_id')]; ?></td>
                        <td align="right"><?=number_format($row[csf('margin')],4); ?></td>
                        <td align="right"><?=number_format($row[csf('margin_percent')],4); ?></td>
                        <td align="right"><?=number_format($row[csf('tot_fob_cost')],4); ?></td>
                        <td align="right"><?=number_format($actualMargin,4); ?></td>
                        <td align="right"><?=number_format($diffMargin,4); ?></td>
                        <td><?=$revOptionDate; ?></td>
                    </tr>
                    <?
                    $tot_margin+=$row[csf('margin')];
                    $tot_fob+=$row[csf('tot_fob_cost')];
                    $tot_diff+=$diffMargin;
                    //$tot_actualMargin+=$actualMargin;
                    $i++;
                }
                ?>
                </tbody>
                <tfoot style="display:none">
                    <tr class="tbl_bottom">
                        <td colspan="3" align="right">Total</td>
                        <td><? echo number_format($tot_margin,4); ?></td>
                        <td>&nbsp;</td>
                        <td><? echo number_format($tot_fob,4); ?></td>
                        <td><? //echo number_format($tot_actualMargin,4); ?></td>
                        <td><? echo number_format($tot_diff,4); ?></td>
                        <td>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </fieldset>
    <?
    exit();
}
?>
