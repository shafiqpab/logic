<?
/*-------------------------------------------- Comments -----------------------
Purpose         :   This Form Will Create Quick Costing Margin Entry . // coppied from Quick Costing Statement Report
Functionality   :   
JS Functions    :
Created by      :   K.M Nazim Uddin 
Creation date   :   15-09-2020
Updated by      :       
Update date     :          
QC Performed BY :       
QC Date         :   
Comments        :
*/
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
    ?>
    <script>
        
    function fnc_costing_details(qc_no,buyer,costing_date,ex_rate,offer_qty,action)
    {
        //alert(buyer)
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/quick_costing_margin_entry_controller.php?qc_no='+qc_no+'&buyer='+buyer+'&costing_date='+costing_date+'&ex_rate='+ex_rate+'&offer_qty='+offer_qty+'&action='+action,'Costing Popup', 'width=958px,height=500px,center=1,resize=0','../');
        emailwindow.onclose=function()
        {
            
        }
    }
    </script>
    <?
	$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
	$cbo_season_id=str_replace("'","",$cbo_season_id);
	$cbo_subDept_id=str_replace("'","",$cbo_subDept_id);
	$txt_styleRef=str_replace("'","",$txt_styleRef);
	$txt_costSheetNo=str_replace("'","",$txt_costSheetNo);
	$cbo_status_id=str_replace("'","",$cbo_status_id);
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
    <fieldset style="width:1050px; margin: 0 auto" >
        
        
        <? if($type==1)
    	{ ?>
            <table width="1050" cellspacing="0" border="1" class="rpt_table" rules="all" align="center">
                <thead>
                    <tr style="font-size:13px">
                        <th width="30">SL.</th> 
                        <th width="80">Cost Sheet No</th>
                        <th width="50">Option</th>
                        <th width="50">Revise No</th>
                        <th width="100">Buyer</th>
                        <th width="100">Style Desc.</th>
                        <th width="100">Style Ref.</th>
                        <th width="80">Offer Qty</th>
                        <th width="60">UOM</th>
                        <th width="70">FOB Price</th>
                        <th width="70">Delivery Date</th>
                        <th width="70">Costing Date</th>
                        <th>Insert By</th>
                     </tr>
                </thead>
            </table>
            <div style="width:1050px; max-height:350px; overflow-y:scroll" id="scroll_body">
            <table width="1030" cellspacing="0" border="1" class="rpt_table" rules="all" align="center" id="table_body">
                <tbody>
                <?
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
				if($cbo_status_id==1)//Pending
				{
                	$sql_mst="select a.id, a.qc_no, a.cost_sheet_no, a.lib_item_id, a.buyer_id, a.style_des, a.style_ref, a.season_id, a.department_id, a.offer_qty, a.uom, a.delivery_date, a.costing_date, a.inserted_by, a.approved, a.approved_by, a.approved_date, a.revise_no, a.option_id, a.exchange_rate from qc_mst a, qc_confirm_mst b where a.qc_no=b.cost_sheet_id and a.qc_no not in (select qc_no from qc_margin_mst where status_active=1 and is_deleted=0) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=444 $buyerCond $seasonCond $deptCond $styleCond $costSheetNoCond $dateCond  group by a.id, a.qc_no, a.cost_sheet_no, a.lib_item_id, a.buyer_id, a.style_des, a.style_ref, a.season_id, a.department_id, a.offer_qty, a.uom, a.delivery_date, a.costing_date, a.inserted_by, a.approved, a.approved_by, a.approved_date, a.revise_no, a.option_id, a.exchange_rate order by a.cost_sheet_no asc";// and a.revise_no=0 and a.option_id=0  and (b.job_id is null or b.job_id =0)  and a.approved not in(1,3)
				}
				else if($cbo_status_id==2)//Done
				{
					$sql_mst="select a.id, a.qc_no, a.cost_sheet_no, a.lib_item_id, a.buyer_id, a.style_des, a.style_ref, a.season_id, a.department_id, a.offer_qty, a.uom, a.delivery_date, a.costing_date, a.inserted_by, a.approved, a.approved_by, a.approved_date, a.revise_no, a.option_id, a.exchange_rate from qc_mst a, qc_confirm_mst b, qc_margin_mst c where a.qc_no=b.cost_sheet_id and a.qc_no=c.qc_no and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form=444 $buyerCond $seasonCond $deptCond $styleCond $costSheetNoCond $dateCond  group by a.id, a.qc_no, a.cost_sheet_no, a.lib_item_id, a.buyer_id, a.style_des, a.style_ref, a.season_id, a.department_id, a.offer_qty, a.uom, a.delivery_date, a.costing_date, a.inserted_by, a.approved, a.approved_by, a.approved_date, a.revise_no, a.option_id, a.exchange_rate order by a.cost_sheet_no asc";// and a.revise_no=0 and a.option_id=0 and (b.job_id is null or b.job_id =0)  and a.approved not in(1,3)
				}
				// app status not check issue id ISD-21-02209
                $sql_result=sql_select($sql_mst);
                //echo $sql_mst;
                foreach($sql_result as $row)
                {
                    $rowData_arr[$row[csf('cost_sheet_no')]][$row[csf('qc_no')]][$row[csf('revise_no')]][$row[csf('option_id')]]['all']=$row[csf('cost_sheet_no')].'***'.$row[csf('costing_date')].'***'.$row[csf('buyer_id')].'***'.$row[csf('style_ref')].'***'.$row[csf('style_des')].'***'.$row[csf('lib_item_id')].'***'.$row[csf('offer_qty')].'***'.$row[csf('delivery_date')].'***'.$row[csf('department_id')].'***'.$row[csf('uom')].'***'.$row[csf('approved')].'***'.$row[csf('inserted_by')].'***'.$row[csf('season_id')].'***'.$row[csf('department_id')].'***1'.'***'.$row[csf('approved_by')].'***'.$row[csf('approved_date')].'***'.$row[csf('exchange_rate')];
                }
                unset($sql_result);
                
                //echo $sql;
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
    							$ex_rate=$exData[17];
    							
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
    							<tr bgcolor="<?=$bgcolor;?>" onClick="change_color('tr_<?=$i; ?>','<?=$bgcolor;?>')" id="tr_<?=$i; ?>">
    								<td width="30"><?=$i; ?></td>
                                    <td width="80"  title="<?=$qc_no; ?>"><a href="##" onClick="fnc_costing_details('<? echo $qc_no;?>','<? echo $buyerArr[$buyer_id]."_".$buyer_id;?>','<? echo $costing_date;?>','<? echo $ex_rate;?>','<? echo $offer_qty; ?>','costing_popup')"><p><?=$cost_sheet_no; ?></p></a></td> 
                                    <td width="50" style="word-break:break-all"><?=$option_id; ?></td>
                                    <td width="50" style="word-break:break-all"><a href="##" onClick="fnc_revise_details('<?=$cost_sheet_no;?>','revise_popup')"><?=$revise_no; ?></td>
    								<td width="100" style="word-break:break-all"><?=$buyerArr[$buyer_id]; ?></td>
    								<td width="100" style="word-break:break-all"><?=$style_des; ?></td>
    								<td width="100" style="word-break:break-all"><?=$style_ref; ?></td>
    								<td width="80" align="right"><?=number_format($offer_qty,0); ?></td>
    								<td width="60"><?=$itemUom;//$unit_of_measurement[$row[csf("uom")]]; ?></td>
    								<td width="70" align="right"><?=number_format($fobPrice,2); ?></td>
    								<td width="70"><?=change_date_format($delivery_date); ?></td>
    								<td width="70"><?=change_date_format($costing_date); ?></td>
    								<td style="word-break:break-all"><?=$user_arr[$inserted_by]; ?></td>
    							 </tr>   
    							<?
    							/*$grand_fabCost+=$fabCost;
    							$grand_trimCost+=$trimCost;
    							$grand_printEmb+=$printEmb;
    							$grand_wash+=$wash;
    							
    							$grand_cm+=$cm;
    							$grand_other+=$other;
    							$grand_totCost+=$totCost;
    							$grand_fobPcs+=$fobPcs;*/
    							$i++;
    						}
    					}
    				}
    			}
                ?>
                </tbody>
            </table>
            
        <? } ?>
        </div>
    </fieldset>
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
	echo "$total_data####$filename####$tot_rows";
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
<!--	<div style="width:780px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
-->	<fieldset style="width:550px; margin-left:3px">
		<div id="scroll_body" align="center">
			<table border="1" class="rpt_table" rules="all" width="530" cellpadding="0" cellspacing="0" align="center">
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

if($action=="costing_popup")
{
    echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
    extract($_REQUEST);
    ?>
    <script>
        //if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";
        var permission='<?=$permission; ?>';
        function fnc_cost_entry(operation)
        {
			freeze_window(operation);
			var is_delete=0;
			if(operation==2)
			{
				var rr=confirm("You are going to delete QC Margin Entry data.\n Are you sure?");
				if(rr==true)
				{
					 is_delete=1;
				}
				else
				{
					is_delete=0;
					release_freezing();	
					return;
				}
			}
            if (form_validation('txt_fabric','Fabric')==false)
            {
				release_freezing();
                return;
            }
            else
            {
                var numRowYarn = $('table#tbl_yarn_cost tbody tr').length;  
                var data_all="";
                for (var i=1; i<=numRowYarn; i++) 
                {
                    data_all+="&txt_Yarn_Yarn_Count_" + i + "='" + $('#txt_Yarn_Yarn_Count_'+i).val()+"'"+"&txt_Yarn_Yarn_Type_" + i + "='" + $('#txt_Yarn_Yarn_Type_'+i).val()+"'"+"&txt_Yarn_composition_" + i + "='" + $('#txt_Yarn_composition_'+i).val()+"'"+"&txt_Yarn_Rate_" + i + "='" + $('#txt_Yarn_Rate_'+i).val()+"'"+"&lib_yarn_rate_id_" + i + "='" + $('#lib_yarn_rate_id_'+i).val()+"'"+"&yarn_dtls_update_id_" + i + "='" + $('#yarn_dtls_update_id_'+i).val()+"'"+"&txt_qc_Yarn_Rate_" + i + "='" + $('#txt_qc_Yarn_Rate_'+i).val()+"'"+"&txt_Yarn_Yarn_Dtls_" + i + "='" + $('#txt_Yarn_Yarn_Dtls_'+i).val()+"'"+"&lib_rate_data_id_" + i + "='" + $('#lib_rate_data_id_'+i).val()+"'"+"&txt_yarn_tot_cons_" + i + "='" + $('#txt_yarn_tot_cons_'+i).val()+"'"+"&txt_Yarn_cost_" + i + "='" + $('#txt_Yarn_cost_'+i).val()+"'"+"&txt_ex_percent_yarn_" + i + "='" + $('#txt_ex_percent_yarn_'+i).val()+"'";
                }
               
                var numRowknit = $('table#tbl_kniting_cost tbody tr').length;
                for (var j=1; j<=numRowknit; j++)
                {
                    data_all+="&txt_knit_body_part_" + j + "='" + $('#txt_knit_body_part_'+j).val()+"'"+"&lib_knit_Yarn_id_" + j + "='" + $('#lib_knit_Yarn_id_'+j).val()+"'"+"&knit_dtls_update_id_" + j + "='" + $('#knit_dtls_update_id_'+j).val()+"'"+"&txt_knit_feb_desc_" + j + "='" + $('#txt_knit_feb_desc_'+j).val()+"'"+"&txt_knit_yarn_desc_" + j + "='" + $('#txt_knit_yarn_desc_'+j).val()+"'"+"&txt_qc_knit_Rate_" + j + "='" + $('#txt_qc_knit_Rate_'+j).val()+"'"+"&txt_knit_Rate_" + j + "='" + $('#txt_knit_Rate_'+j).val()+"'"+"&lib_knit_rate_data_id_" + j + "='" + $('#lib_knit_rate_data_id_'+j).val()+"'"+"&txt_knit_tot_cons_" + j + "='" + $('#txt_knit_tot_cons_'+j).val()+"'"+"&txt_knit_cost_" + j + "='" + $('#txt_knit_cost_'+j).val()+"'";
                }

                var numRowDF = $('table#tbl_df tbody tr').length;
                for (var k=1; k<=numRowDF; k++)
                {
                    data_all+="&txt_df_Color_Type_" + k + "='" + $('#txt_df_Color_Type_'+k).val()+"'"+"&lib_dyeing_finishing_id_" + k + "='" + $('#lib_dyeing_finishing_id_'+k).val()+"'"+"&df_dtls_update_id_" + k + "='" + $('#df_dtls_update_id_'+k).val()+"'"+"&txt_df_Color_" + k + "='" + $('#txt_df_Color_'+k).val()+"'"+"&txt_df_process_" + k + "='" + $('#txt_df_process_'+k).val()+"'"+"&txt_qc_df_Rate_" + k + "='" + $('#txt_qc_df_Rate_'+k).val()+"'"+"&txt_df_Rate_" + k + "='" + $('#txt_df_Rate_'+k).val()+"'"+"&lib_df_rate_data_id_" + k + "='" + $('#lib_df_rate_data_id_'+k).val()+"'"+"&txt_df_tot_cons_" + k + "='" + $('#txt_df_tot_cons_'+k).val()+"'"+"&txt_df_cost_" + k + "='" + $('#txt_df_cost_'+k).val()+"'";
                }

                var numRowAop = $('table#tbl_aop tbody tr').length; 
                for (var m=1; m<=numRowAop; m++) 
                {
                    data_all+="&txt_aop_Color_Type_" + m + "='" + $('#txt_aop_Color_Type_'+m).val()+"'"+"&lib_aop_id_" + m + "='" + $('#lib_aop_id_'+m).val()+"'"+"&aop_dtls_update_id_" + m + "='" + $('#aop_dtls_update_id_'+m).val()+"'"+"&txt_aop_Color_" + m + "='" + $('#txt_aop_Color_'+m).val()+"'"+"&txt_aop_process_" + m + "='" + $('#txt_aop_process_'+m).val()+"'"+"&txt_qc_aop_Rate_" + m + "='" + $('#txt_qc_aop_Rate_'+m).val()+"'"+"&txt_aop_Rate_" + m + "='" + $('#txt_aop_Rate_'+m).val()+"'"+"&lib_aop_rate_data_id_" + m + "='" + $('#lib_aop_rate_data_id_'+m).val()+"'"+"&txt_aop_tot_cons_" + m + "='" + $('#txt_aop_tot_cons_'+m).val()+"'"+"&txt_aop_cost_" + m + "='" + $('#txt_aop_cost_'+m).val()+"'";
                }
                var data="action=save_update_delete&operation="+operation+'&numRowYarn='+numRowYarn+'&numRowknit='+numRowknit+'&numRowDF='+numRowDF+'&numRowAop='+numRowAop+get_submitted_data_string('hid_qc_no*txt_fabric*txt_accessories*txt_avl_min*txt_cm_dzn*txt_frieght_dzn*txt_lab_dzn*txt_mis_offer_qty*txt_other_cost_dzn*txt_commercial_cost_dzn*txt_com_dzn*txt_fob_dzn*txt_fob_pcs*txt_margin_per_dzn*txt_margin*txt_total_yarn_cost*txt_yarn_dyeing_cost*txt_knitting_cost*txt_df_cost*txt_aop_cost*txt_total_cost*update_id*txt_cpm*txt_smv*txt_efficency*txt_cm*txt_available_min*txt_buyer*txt_special_operation*txtactualmainfabric*txtactualrib*txtactualhood*txtactualothers*txtactualtotcons*txtactualyds*txtfabricpurchasekg*txtfabricpurchaseyds',"../../../")+data_all;
               
                //alert(data); return;
                
                http.open("POST","quick_costing_margin_entry_controller.php",true);
                http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                http.send(data);
                http.onreadystatechange = fnc_cost_entry_reponse;
            }
        }

        function fnc_cost_entry_reponse()
        {
            if(http.readyState == 4)
            {
                var reponse=trim(http.responseText).split('**');
                show_msg(trim(reponse[0]));
                if((reponse[0]==0 || reponse[0]==1)){
                    parent.emailwindow.hide();
                }else if(reponse[0]==2){
                    reset_form('quick_cosing_entry','','','','','');
                }
                release_freezing();
            }
        }

        function frm_close()
        {
            parent.emailwindow.hide();
        }

        function reset_for_refresh()
        {
            reset_form('quick_cosing_entry','','','','','update_id*hid_qc_no');
        }

        function calculate_rate(rate_type,type,row,val)
        {
			var mainbodyid=$('#txtactualmainfabric').attr('consdtlsid');
			var ribbodyid=$('#txtactualrib').attr('consdtlsid');
			var hoodbodyid=$('#txtactualhood').attr('consdtlsid');
			var otherbodyid=$('#txtactualothers').attr('consdtlsid');
			if(type==1)
			{
				var numRow = $('table#tbl_yarn_cost tbody tr').length;
				var total_Yarn_Rate=0; var total_actual_Yarn_cost=0; var yarn_tot_cons=0; 
				//($('#txt_Yarn_Rate_'+i).val()*1)*(($('#txt_ex_percent_yarn_'+i).val()*1)/100)
				for( var i = 1; i <= numRow; i++ ){
					var bodyQty=0;
					var yarnbodyid=$('#lib_rate_data_id_'+i).val();
					var yper=($('#txt_ex_percent_yarn_'+i).val()*1)/100;
					var rate= ($('#txt_Yarn_Rate_'+i).val()*1);
					if(yarnbodyid==mainbodyid) 
					{
						bodyQty=$('#txtactualmainfabric').val();
						total_actual_Yarn_cost += (bodyQty*1)*rate*yper;
					}
					if(yarnbodyid==ribbodyid) 
					{
						bodyQty=$('#txtactualrib').val();
						total_actual_Yarn_cost += (bodyQty*1)*rate*yper;
					}
					if(yarnbodyid==hoodbodyid) 
					{
						bodyQty=$('#txtactualhood').val();
						total_actual_Yarn_cost += (bodyQty*1)*rate*yper;
					}
					if(yarnbodyid==otherbodyid) 
					{
						bodyQty=$('#txtactualothers').val();
						total_actual_Yarn_cost += (bodyQty*1)*rate*yper;
					}
				}
				$('#txt_total_yarn_cost').val(total_actual_Yarn_cost.toFixed(4));
			}
			if(type==3){
				var numRow = $('table#tbl_kniting_cost tbody tr').length;
				var total_knit_cost=0; var actual_knit_cost=0;
				for( var i = 1; i <= numRow; i++ ){
					var bodyQty=0;
					var knitbodyid=$('#lib_knit_rate_data_id_'+i).val();
					if(knitbodyid==mainbodyid) 
					{
						bodyQty=$('#txtactualmainfabric').val();
						actual_knit_cost += (bodyQty*1)*($('#txt_knit_Rate_'+i).val()*1);
					}
					if(knitbodyid==ribbodyid) 
					{
						bodyQty=$('#txtactualrib').val();
						actual_knit_cost += (bodyQty*1)*($('#txt_knit_Rate_'+i).val()*1);
					}
					if(knitbodyid==hoodbodyid) 
					{
						bodyQty=$('#txtactualhood').val();
						actual_knit_cost += (bodyQty*1)*($('#txt_knit_Rate_'+i).val()*1);
					}
					if(knitbodyid==otherbodyid) 
					{
						bodyQty=$('#txtactualothers').val();
						actual_knit_cost += (bodyQty*1)*($('#txt_knit_Rate_'+i).val()*1);
					}
				}
				$('#txt_knitting_cost').val(actual_knit_cost.toFixed(4));
			}
			if(type==4){
				var numRow = $('table#tbl_df tbody tr').length;
				var total_df_cost=0; var actual_df_cost=0;
				for( var i = 1; i <= numRow; i++ ){
					
					var dfbodyid=$('#lib_df_rate_data_id_'+i).val();
					if(dfbodyid==mainbodyid) 
					{
						bodyQty=$('#txtactualmainfabric').val();
						actual_df_cost += (bodyQty*1)*($('#txt_df_Rate_'+i).val()*1);
					}
					if(dfbodyid==ribbodyid) 
					{
						bodyQty=$('#txtactualrib').val();
						actual_df_cost += (bodyQty*1)*($('#txt_df_Rate_'+i).val()*1);
					}
					if(dfbodyid==hoodbodyid) 
					{
						bodyQty=$('#txtactualhood').val();
						actual_df_cost += (bodyQty*1)*($('#txt_df_Rate_'+i).val()*1);
					}
					if(dfbodyid==otherbodyid) 
					{
						bodyQty=$('#txtactualothers').val();
						actual_df_cost += (bodyQty*1)*($('#txt_df_Rate_'+i).val()*1);
					}
				}
				$('#txt_df_cost').val(actual_df_cost.toFixed(4));
			}
			if(type==5){
				var numRow = $('table#tbl_aop tbody tr').length;
				var total_aop_cost=0; var actual_aop_cost=0;
				for( var i = 1; i <= numRow; i++ ){
					
					var aopbodyid=$('#lib_aop_rate_data_id_'+i).val();
					if(aopbodyid==mainbodyid) 
					{
						bodyQty=$('#txtactualmainfabric').val();
						actual_aop_cost += (bodyQty*1)*($('#txt_aop_Rate_'+i).val()*1);
					}
					if(aopbodyid==ribbodyid) 
					{
						bodyQty=$('#txtactualrib').val();
						actual_aop_cost += (bodyQty*1)*($('#txt_aop_Rate_'+i).val()*1);
					}
					if(aopbodyid==hoodbodyid) 
					{
						bodyQty=$('#txtactualhood').val();
						actual_aop_cost += (bodyQty*1)*($('#txt_aop_Rate_'+i).val()*1);
					}
					if(aopbodyid==otherbodyid) 
					{
						bodyQty=$('#txtactualothers').val();
						actual_aop_cost += (bodyQty*1)*($('#txt_aop_Rate_'+i).val()*1);
					}
				}
				$('#txt_aop_cost').val(actual_aop_cost.toFixed(4));
			}
			calculate_total_rate();
        }

        function calculate_total_rate()
        {
            var total_yarn_cost=$('#txt_total_yarn_cost').val()*1;
            var yarn_dyeing_cost=$('#txt_yarn_dyeing_cost').val()*1;
            var knitting_cost=$('#txt_knitting_cost').val()*1;
            var df_cost=$('#txt_df_cost').val()*1;
            var aop_cost=$('#txt_aop_cost').val()*1;
            var fabricpurchasekg=$('#txtfabricpurchasekg').val()*1;
			var fabricpurchaseyds=$('#txtfabricpurchaseyds').val()*1;
            var total_cost=total_yarn_cost+yarn_dyeing_cost+knitting_cost+df_cost+aop_cost+fabricpurchasekg+fabricpurchaseyds;
            var total_fabric_cost =total_cost;
            $('#txt_total_cost').val( total_cost.toFixed(4) );
            $('#txt_fabric').val( total_fabric_cost.toFixed(4) );
            calculate_marketing_cost();
        }

        function calculate_marketing_cost() 
        {
			var costingper_id=$("#cbo_costingper_id").val();
			if(costingper_id==2) var costingqty="1"; else if(costingper_id==1) var costingqty="12"; else var costingqty="0";
            var fabric_cost=$('#txt_fabric').val()*1;
            var special_operation=$('#txt_special_operation').val()*1;
            var accessories_cost=$('#txt_accessories').val()*1;
            var cm_dzn=$('#txt_cm_dzn').val()*1;
            var frieght_dzn=$('#txt_frieght_dzn').val()*1;
            var lab_dzn=$('#txt_lab_dzn').val()*1;
            var mis_offer_qty=$('#txt_mis_offer_qty').val()*1;
            var other_cost_dzn=$('#txt_other_cost_dzn').val()*1;
			var commercial_cost_dzn=$('#txt_commercial_cost_dzn').val()*1;
            var com_dzn=$('#txt_com_dzn').val()*1;
            var fob_dzn=fabric_cost+special_operation+accessories_cost+cm_dzn+frieght_dzn+lab_dzn+mis_offer_qty+other_cost_dzn+commercial_cost_dzn+com_dzn;
			//alert(fabric_cost+'_'+special_operation+'_'+accessories_cost+'_'+cm_dzn+'_'+frieght_dzn+'_'+lab_dzn+'_'+mis_offer_qty+'_'+other_cost_dzn+'_'+commercial_cost_dzn+'_'+com_dzn)
            $('#txt_fob_dzn').val( fob_dzn.toFixed(4) );
            
            var fob_pcs =fob_dzn/costingqty;
            //var fob_pcs=$('#txt_fob_pcs').val()*1;
            $('#txt_fob_pcs').val( fob_pcs.toFixed(4) ); 

            var qc_fob_dzn=$('#txt_qc_fob_dzn').val()*1;
            //var margin_dzn=fob_pcs-fob_dzn;
            var margin_dzn=qc_fob_dzn-fob_dzn;
            $('#txt_margin_per_dzn').val( margin_dzn.toFixed(4) );
            
            //var margin_percent =margin_dzn*100;
            var margin_percent = (margin_dzn/fob_dzn)*100;
            $('#txt_margin').val( margin_percent.toFixed(4) );
        }
        
        function calculate_cm_cost()
        {
			var costingper_id=$("#cbo_costingper_id").val();
			if(costingper_id==2) var costingqty="1"; else if(costingper_id==1) var costingqty="12"; else var costingqty="0";
            var cpm=$('#txt_cpm').val()*1;
            var smv=$('#txt_smv').val()*1;
            var efficency=$('#txt_efficency').val()*1;
            var ex_rate=$('#txt_ex_rate').val()*1;
            var cm=((((cpm*100)/efficency)*smv)*costingqty)/ex_rate;
            $('#txt_cm').val( cm.toFixed(4) );

            //var cm_dzn=((cpm*smv*costingqty)+efficency)/ex_rate; Wrong calculation
            $('#txt_cm_dzn').val( cm.toFixed(4) );

            var offer_qty=$('#txt_offer_qty').val()*1;
            //alert(offer_qty);
            var available_min =(smv*offer_qty)/(efficency/100);
            $('#txt_available_min').val( available_min.toFixed(0) );
            $('#txt_avl_min').val( available_min.toFixed(0) );
            calculate_marketing_cost();
        }

        function fnc_details_popup(row,qc_no,exchange_rate,action)
        {
            //alert(row)
			var popup_width='';
			if(action=="dyeing_finishing_popup" || action=="aop_finishing_popup") popup_width='770px'; else popup_width='700px';
            emailwindow=dhtmlmodal.open('EmailBox', 'iframe','quick_costing_margin_entry_controller.php?qc_no='+qc_no+'&exchange_rate='+exchange_rate+'&action='+action,'Details Popup', 'width='+popup_width+',height=300px,center=1,resize=0','../../');
            emailwindow.onclose=function()
            {
                var popupData=this.contentDoc.getElementById("popupData").value;
                var popupData=popupData.split('_');
                var ex_rate=$('#txt_ex_rate').val()*1;
                //alert(popupData);
                //1> yarn 2> yd 3>knit 4>df 5>aop
                if(action=='yarn_count_popup'){
                    $('#lib_yarn_rate_id_'+row).val(popupData[0]);
                    $('#txt_Yarn_Yarn_Count_'+row).val(popupData[1]);
                    $('#txt_Yarn_Yarn_Type_'+row).val(popupData[2]);
                    //var rate_usd= popupData[3]/ex_rate;
                    //alert(popupData[3]);
                    var rate= popupData[3]*1;
                    var ex_percent_yarn=$('#txt_ex_percent_yarn_'+row).val()*1;
                    var yarn_tot_cons=$('#txt_yarn_tot_cons_'+row).val()*1;
                    var ex_percent_yarn_persent=ex_percent_yarn/100;
                    var actual_cost=rate*ex_percent_yarn_persent*yarn_tot_cons;
                    //var actual_cost=rate*ex_percent_yarn_persent;
                    $('#txt_Yarn_Rate_'+row).val(rate.toFixed(4));
                    $('#txt_Yarn_cost_'+row).val(actual_cost.toFixed(4));
                    //$('#txt_Yarn_Rate_'+row).val(popupData[3].toFixed(4));
                    //alert($('#txt_Yarn_Rate_'+row).val());
                    $('#txt_Yarn_composition_'+row).val(popupData[4]);
                    calculate_rate(1,1,1,rate)
                }else if(action=='kniting_details_popup'){
                    $('#lib_knit_Yarn_id_'+row).val(popupData[0]);
                    $('#txt_knit_body_part_'+row).val(popupData[1]);
                    $('#txt_knit_feb_desc_'+row).val(popupData[2]);
                    $('#txt_knit_yarn_desc_'+row).val(popupData[3]);
                    var rate_usd= popupData[4]/ex_rate;
                    var knit_tot_cons=$('#txt_knit_tot_cons_'+row).val()*1;
                    var actual_cost=rate_usd*knit_tot_cons;
                    $('#txt_knit_Rate_'+row).val(rate_usd.toFixed(4));
                    $('#txt_knit_cost_'+row).val(actual_cost.toFixed(4));
                    calculate_rate(1,3,1,rate_usd)
                }else if(action=='dyeing_finishing_popup'){
                    $('#lib_dyeing_finishing_id_'+row).val(popupData[0]);
                    $('#txt_df_Color_'+row).val(popupData[1]);
                    var rate_usd= popupData[2]/ex_rate;
                    var df_tot_cons=$('#txt_df_tot_cons_'+row).val()*1;
                    var actual_cost=rate_usd*df_tot_cons;
                    $('#txt_df_Rate_'+row).val(rate_usd.toFixed(4));
                    $('#txt_df_cost_'+row).val(actual_cost.toFixed(4));
                    $('#txt_df_process_'+row).val(popupData[3]);
                    $('#txt_df_Color_Type_'+row).val(popupData[4]);
                    calculate_rate(1,4,1,rate_usd)
                }else if(action=='aop_finishing_popup'){
                    $('#lib_aop_id_'+row).val(popupData[0]);
                    $('#txt_aop_Color_'+row).val(popupData[1]);
                    var rate_usd= popupData[2]/ex_rate;
                    var aop_tot_cons=$('#txt_aop_tot_cons_'+row).val()*1;
                    var actual_cost=rate_usd*aop_tot_cons;
                    $('#txt_aop_Rate_'+row).val(rate_usd.toFixed(4));
                    $('#txt_aop_cost_'+row).val(actual_cost.toFixed(4));
                    $('#txt_aop_process_'+row).val(popupData[3]);
                    $('#txt_aop_Color_Type_'+row).val(popupData[4]);
                    calculate_rate(1,5,1,rate_usd)
                }
            }
        }
		
		function fnc_bodypart_cal()
		{
			var actualBodyCons=($('#txtactualmainfabric').val()*1)+($('#txtactualrib').val()*1)+($('#txtactualhood').val()*1)+($('#txtactualothers').val()*1);
			
			$('#txtactualtotcons').val( number_format(actualBodyCons,2,'.','') );
			calculate_rate(2,1,0,0);
			calculate_rate(2,3,0,0);
			calculate_rate(2,4,0,0);
			calculate_rate(2,5,0,0);
		}
    </script>
    <body onLoad="set_hotkey();">
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
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_qc_fabric" id="txt_qc_fabric" value="<? echo $fabric_cost_qc; ?>" style="width:51px"  readonly="readonly" placeholder="Display" ></td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_fabric" id="txt_fabric" value="<? echo $fabric_cost; ?>" style="width:51px" readonly placeholder="Display" ></td>
                            </tr>
                            <tr>
                                <td width="120">Special Operation</td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_qc_special_operation" id="txt_qc_special_operation" value="<? echo $sp_operation_cost_qc; ?>" onKeyUp="calculate_marketing_cost()" style="width:51px" readonly placeholder="Display" ></td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_special_operation" id="txt_special_operation" value="<? echo $sp_operation_cost; ?>" onKeyUp="calculate_marketing_cost()" style="width:51px" ></td>
                            </tr>
                            <tr>
                                <td width="120">Accessories</td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_qc_accessories" id="txt_qc_accessories" value="<? echo $accessories_cost_qc; ?>" onKeyUp="calculate_marketing_cost()" style="width:51px" readonly placeholder="Display" ></td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_accessories" id="txt_accessories" value="<? echo $accessories_cost; ?>" onKeyUp="calculate_marketing_cost()" style="width:51px" ></td>
                            </tr>
                            <tr>
                                <td width="120">CM (<?=$costingcap; ?>)</td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_qc_cm_dzn" id="txt_qc_cm_dzn" value="<?=$cm_cost_qc; ?>" style="width:51px"  readonly="readonly" placeholder="Display"/></td>
                                <td width="65" title="(((CPM*100)/Efficiency)*SMV)*<?=$costingcap; ?>"><input type="text" class="text_boxes_numeric" name="txt_cm_dzn" id="txt_cm_dzn" value="<?=$cm_cost; ?>" readonly placeholder="Display"  style="width:51px" /></td>
                            </tr>
                            <tr>
                                <td width="120">Frieght Cost(<?=$costingcap; ?>)</td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_qc_frieght_dzn" id="txt_qc_frieght_dzn" value="<? echo $frieght_cost_qc; ?>" onKeyUp="calculate_marketing_cost()" style="width:51px" readonly placeholder="Display" ></td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_frieght_dzn" id="txt_frieght_dzn" value="<? echo $frieght_cost; ?>" onKeyUp="calculate_marketing_cost()" style="width:51px" ></td>
                            </tr>
                            <tr>
                                <td width="120">Lab - Test(<?=$costingcap; ?>)</td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_qc_lab_dzn" id="txt_qc_lab_dzn" value="<? echo $lab_test_cost_qc; ?>" onKeyUp="calculate_marketing_cost()" style="width:51px" readonly placeholder="Display"></td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_lab_dzn" id="txt_lab_dzn" value="<? echo $lab_test_cost; ?>" onKeyUp="calculate_marketing_cost()" style="width:51px" ></td>
                            </tr>
                            <tr>
                                <td width="120">Mis/Offer Qty.</td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_qc_mis_offer_qty" id="txt_qc_mis_offer_qty" value="<? echo $mis_offer_qty_qc; ?>" onKeyUp="calculate_marketing_cost()" style="width:51px" readonly placeholder="Display" ></td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_mis_offer_qty" id="txt_mis_offer_qty" value="<? echo $mis_offer_qty; ?>" onKeyUp="calculate_marketing_cost()" style="width:51px" ></td>
                            </tr>
                            <tr>
                                <td width="120">Other Cost(<?=$costingcap; ?>)</td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_qc_other_cost_dzn" id="txt_qc_other_cost_dzn" value="<? echo $other_cost_qc; ?>" onKeyUp="calculate_marketing_cost()" style="width:51px"  readonly="readonly" placeholder="Display"></td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_other_cost_dzn" id="txt_other_cost_dzn" value="<? echo $other_cost; ?>" onKeyUp="calculate_marketing_cost()" style="width:51px" ></td>
                            </tr>
                            <tr>
                                <td width="120">Commercial Cost</td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_qc_commercial_cost_dzn" id="txt_qc_commercial_cost_dzn" value="<?=$commercial_cost_qc; ?>" onKeyUp="calculate_marketing_cost()" style="width:51px"  readonly="readonly" placeholder="Display"></td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_commercial_cost_dzn" id="txt_commercial_cost_dzn" value="<?=$commercial_cost; ?>" onKeyUp="calculate_marketing_cost()" style="width:51px" ></td>
                            </tr>
                            <tr>
                                <td width="120">Com.(%)(<?=$costingcap; ?>)</td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_qc_com_dzn" id="txt_qc_com_dzn" value="<? echo $com_cost_qc; ?>" onKeyUp="calculate_marketing_cost()" style="width:51px"  readonly="readonly" placeholder="Display"></td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_com_dzn" id="txt_com_dzn" value="<? echo $com_cost; ?>" onKeyUp="calculate_marketing_cost()" style="width:51px" ></td>
                            </tr>
                            <tr>
                                <td width="120"><strong>F.O.B(<?=$costingcap; ?>)</strong></td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_qc_fob_dzn" id="txt_qc_fob_dzn" value="<?=$fob_qc; ?>" style="width:51px" readonly placeholder="Display" ></td>
                                <td width="65" title="fabric_cost+special_operation+accessories_cost+cm_dzn+frieght_dzn+lab_dzn+mis_offer_qty+other_cost_dzn+com_dzn"><input type="text" class="text_boxes_numeric" name="txt_fob_dzn" id="txt_fob_dzn" value="<? echo $fob; ?>"  readonly="readonly" placeholder="Display" style="width:51px" ></td>
                            </tr>
                            <tr>
                                <td width="120">F.O.B($/PCS)</td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_qc_fob_pcs" id="txt_qc_fob_pcs" value="<?=$fob_pcs_qc; ?>" style="width:51px" readonly placeholder="Display" ></td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_fob_pcs" id="txt_fob_pcs" value="<? echo $fob_pcs; ?>"  readonly="readonly"placeholder="Display"  style="width:51px" ></td>
                            </tr>
                            <tr>
                                <td width="120" >Margin Per/<?=$costingcap; ?></td>
                                <td colspan="2" title="F.O.B($/PCS)-Cost - F.O.B(<?=$costingcap; ?>)" ><input type="text" class="text_boxes_numeric" name="txt_margin_per_dzn" id="txt_margin_per_dzn" value="<? echo number_format($margin,4); ?>" style="width:117px"  readonly="readonly" placeholder="Display" >
                                <input type="hidden" class="text_boxes_numeric" name="txt_qc_margin_per_dzn" id="txt_qc_margin_per_dzn" value="<? echo $margin; ?>" style="width:51px" readonly placeholder="Display" >
                                </td>
                            </tr>
                            <tr>
                                <td width="120" >Margin %</td>
                                <td colspan="2" title="Margin Per/DZN * 100"><input type="text" class="text_boxes_numeric" name="txt_margin" id="txt_margin" value="<? echo number_format($margin_percent,4); ?>" style="width:117px" readonly placeholder="Display" >
                                <input type="hidden" class="text_boxes_numeric" name="txt_qc_margin" id="txt_qc_margin" value="<? //echo $margin_percent; ?>" style="width:51px" readonly placeholder="Display" ></td>
                            </tr>
                            <tr>
                                <td colspan="3">&nbsp;</td>
                            </tr>
                            <tr>
                                <td width="120">AVL Min.</td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_qc_avl_min" id="txt_qc_avl_min" value="<? echo $avl_min_qc; ?>" onKeyUp="calculate_marketing_cost()" style="width:51px"  readonly="readonly" placeholder="Display"></td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_avl_min" id="txt_avl_min" value="<? echo $avl_min; ?>" onKeyUp="calculate_marketing_cost()" style="width:51px" ></td>
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
                                    <input type="hidden" class="text_boxes_numeric" name="txt_buyer" id="txt_buyer" value="<? echo $buyer_id; ?>">
                                    <input type="hidden" class="text_boxes" name="txt_ex_rate" id="txt_ex_rate" value="<? echo $ex_rate; ?>">
                                    <input type="hidden" class="text_boxes" name="txt_offer_qty" id="txt_offer_qty" value="<? echo $offer_qty; ?>">
                                </td>
                            </tr>
                            <tr>
                                <td width="150">CPM</td>
                                <td width="100"><input type="text" class="text_boxes_numeric" name="txt_cpm" id="txt_cpm" value="<?=$cpm; ?>" placeholder="Display" onKeyUp="calculate_cm_cost();" readonly ></td>
                            </tr>
                            <tr>
                                <td width="150">SMV</td>
                                <td width="100"><input type="text" class="text_boxes_numeric" name="txt_smv" id="txt_smv" value="<?=$smv; ?>" placeholder="Write" onKeyUp="calculate_cm_cost();" ></td>
                            </tr>
                            <tr>
                                <td width="150">Efficency %</td>
                                <td width="100"><input type="text" class="text_boxes_numeric" name="txt_efficency" id="txt_efficency" value="<?=$efficency; ?>" placeholder="" onKeyUp="calculate_cm_cost();" ></td>
                            </tr>
                            <tr>
                                <td width="150">CM</td>
                                <td width="100" title="((((cpm*100)/efficency)*smv)*<?=$costingcap; ?>)/ex_rate"><input type="text" class="text_boxes_numeric" name="txt_cm" id="txt_cm" value="<?=$cm; ?>" placeholder="Write/Display" onKeyUp="calculate_cm_cost();"  ></td>
                            </tr>
                            <tr>
                                <td width="150">Available Minutes</td>
                                <td width="100" title="(smv*offer_qty)/(efficency/100)"><input type="text" class="text_boxes_numeric" name="txt_available_min" id="txt_available_min" value="<? echo $available_min; ?>" placeholder="Write/Display" onKeyUp="calculate_cm_cost();"  ></td>
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
                                    <td width="75"><input type="text" class="text_boxes_numeric" name="txtqcmainfabric" id="txtqcmainfabric" value="<?=$mainfabricBodyQty; ?>" consdtlsid="<?=$mainfabricBodyid; ?>" style="width:60px" placeholder="Display" readonly></td>
                                    <td width="75"><input type="text" class="text_boxes_numeric" name="txtqcrib" id="txtqcrib" value="<?=$ribBodyQty; ?>" consdtlsid="<?=$ribBodyid; ?>" style="width:60px" placeholder="Display" readonly></td>
                                    <td width="75"><input type="text" class="text_boxes_numeric" name="txtqchood" id="txtqchood" value="<?=$hoodBodyQty; ?>" consdtlsid="<?=$hoodBodyid; ?>" style="width:60px" placeholder="Display" readonly></td>
                                    <td width="70"><input type="text" class="text_boxes_numeric" name="txtqcothers" id="txtqcothers" value="<?=$othersBodyQty; ?>" consdtlsid="<?=$othersBodyid; ?>" style="width:55px" placeholder="Display" readonly></td>
                                    <td width="80"><input type="text" class="text_boxes_numeric" name="txtqctotcons" id="txtqctotcons" value="<?=$totBodyconsQty; ?>" style="width:65px" placeholder="Display" readonly></td>
                                    <td><input type="text" class="text_boxes_numeric" name="txtqcyds" id="txtqcyds" value="<?=$ydsBodyQty; ?>" style="width:55px" placeholder="Display" readonly></td>
                                </tr>
                                <tr>
                                    <td width="100"><strong>Actual Cons.</strong></td>
                                    <td width="75"><input type="text" class="text_boxes_numeric" name="txtactualmainfabric" id="txtactualmainfabric" value="<?=$actualMainfabricBodyQty; ?>" consdtlsid="<?=$mainfabricBodyid; ?>" onChange="fnc_bodypart_cal();" style="width:60px" placeholder="Write" <?=$fabconsdisabled; ?> ></td>
                                    <td width="75"><input type="text" class="text_boxes_numeric" name="txtactualrib" id="txtactualrib" value="<?=$actualRibBodyQty; ?>" consdtlsid="<?=$ribBodyid; ?>" onChange="fnc_bodypart_cal();" style="width:60px" placeholder="Write" <?=$fabconsdisabled; ?> ></td>
                                    <td width="75"><input type="text" class="text_boxes_numeric" name="txtactualhood" id="txtactualhood" value="<?=$actualHoodBodyQty; ?>" consdtlsid="<?=$hoodBodyid; ?>" onChange="fnc_bodypart_cal();" style="width:60px" placeholder="Write" <?=$fabconsdisabled; ?> ></td>
                                    <td width="70"><input type="text" class="text_boxes_numeric" name="txtactualothers" id="txtactualothers" value="<?=$actualOthersBodyQty; ?>" consdtlsid="<?=$othersBodyid; ?>" onChange="fnc_bodypart_cal();" style="width:55px" placeholder="Write" <?=$fabconsdisabled; ?> ></td>
                                    <td width="80"><input type="text" class="text_boxes_numeric" name="txtactualtotcons" id="txtactualtotcons" value="<?=$actualTotBodyconsQty; ?>" readonly style="width:65px" placeholder="Display"></td>
                                    <td><input type="text" class="text_boxes_numeric" name="txtactualyds" id="txtactualyds" value="<?=$actualYdsBodyQty; ?>" onChange="fnc_bodypart_cal();" style="width:55px" placeholder="Display" disabled></td>
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
                                            <td width="100"><input type="text" class="text_boxes" name="txt_Yarn_Yarn_Count_<?=$i; ?>" id="txt_Yarn_Yarn_Count_<?=$i; ?>" value="<?=$row[csf('yarn_count')]; ?>" style="width:86px" consdtlsid="<?=$rid; ?>" onClick="fnc_details_popup('<?=$i;?>','<?=$qc_no;?>','<?=$exchange_rate;?>','yarn_count_popup');" readonly placeholder="Browse" >
                                                <input type="hidden" class="text_boxes" name="lib_yarn_rate_id_<?=$i; ?>" id="lib_yarn_rate_id_<?=$i; ?>" value="<?=$row[csf('lib_table_id')]; ?>" >
                                                <input type="hidden" class="text_boxes" name="lib_rate_data_id_<?=$i; ?>" id="lib_rate_data_id_<?=$i; ?>" value="<?=$row[csf('rate_data_id')]; ?>" >
                                                <input type="hidden" class="text_boxes" name="yarn_dtls_update_id_<?=$i; ?>" id="yarn_dtls_update_id_<?=$i; ?>" value="<?=$row[csf('id')]; ?>" >
                                            </td>
                                            <td width="100"><input type="text" class="text_boxes" name="txt_Yarn_Yarn_Type_<?=$i; ?>" id="txt_Yarn_Yarn_Type_<?=$i; ?>" value="<?=$row[csf('yarn_type')]; ?>" style="width:86px" readonly placeholder="Display"  ></td>
                                            <td width="100"><input type="text" class="text_boxes" name="txt_Yarn_composition_<?=$i; ?>" id="txt_Yarn_composition_<?=$i; ?>" value="<?=$row[csf('composition')]; ?>" style="width:86px" readonly placeholder="Display"  ></td>
                                            <td width="100"><input type="text" class="text_boxes" name="txt_Yarn_Yarn_Dtls_<?=$i; ?>" id="txt_Yarn_Yarn_Dtls_<?=$i; ?>" value="<?=$row[csf('yarn_details')]; ?>" style="width:86px" readonly placeholder="Display" ></td>
                                            <td width="50"><input type="text" class="text_boxes_numeric" name="txt_ex_percent_yarn_<?=$i; ?>" id="txt_ex_percent_yarn_<?=$i; ?>" value="<?=$row[csf('ex_percent')]; ?>" style="width:35px" readonly placeholder="Display" ></td>
                                            <td width="50" titel="<?=$row[csf('tot_cons')]; ?>"><input type="text" class="text_boxes_numeric" name="txt_qc_Yarn_Rate_<?=$i; ?>" id="txt_qc_Yarn_Rate_<?=$i; ?>" value="<?=$row[csf('qc_rate')]; $total_qc_rate +=$row[csf('qc_rate')]*$row[csf('tot_cons')]; ?>" style="width:35px" readonly placeholder="Display"> </td>
                                            <td><input type="text" class="text_boxes_numeric" name="txt_Yarn_Rate_<?=$i; ?>" id="txt_Yarn_Rate_<?=$i; ?>" qcdata="<?=$row[csf('tot_cons')].'='.$row[csf('ex_percent')].'='.$row[csf('actual_rate')]; ?>" value="<?=$row[csf('actual_rate')]; ?>" onChange="calculate_rate(2,1,<?=$i; ?>,this.value)" style="width:35px" placeholder="Display/Write"  > 
                                            <input type="hidden" class="text_boxes_numeric" name="txt_yarn_tot_cons_<?=$i; ?>" id="txt_yarn_tot_cons_<?=$i; ?>" value="<?=$row[csf('tot_cons')]; ?>" style="width:30px" >
                                            <input type="hidden" class="text_boxes_numeric" name="txt_Yarn_cost_<?=$i; ?>" id="txt_Yarn_cost_<?=$i; ?>" value="<?=$row[csf('actual_cost')]; ?>" style="width:30px" > 
                                            </td>
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
														<input type="text" class="text_boxes" name="txt_Yarn_Yarn_Count_<?=$i; ?>" id="txt_Yarn_Yarn_Count_<?=$i; ?>" value="<?=$yCount; ?>" style="width:86px" consdtlsid="<?=$rid; ?>" onClick="fnc_details_popup('<?=$i;?>','<?=$qc_no;?>','<?=$exchange_rate;?>','yarn_count_popup');" readonly placeholder="Browse" >
														<input type="hidden" class="text_boxes" name="lib_yarn_rate_id_<?=$i; ?>" id="lib_yarn_rate_id_<?=$i; ?>"  >
														<input type="hidden" class="text_boxes" name="lib_rate_data_id_<?=$i; ?>" id="lib_rate_data_id_<?=$i; ?>" value="<?=$rate_data_id; ?>" >
														<input type="hidden" class="text_boxes" name="yarn_dtls_update_id_<?=$i; ?>" id="yarn_dtls_update_id_<?=$i; ?>" value="" >
													</td>
													<td width="100"><input type="text" class="text_boxes" name="txt_Yarn_Yarn_Type_<?=$i; ?>" id="txt_Yarn_Yarn_Type_<?=$i; ?>" value="<?=$yType; ?>" style="width:86px" readonly placeholder="Display" ></td>
													<td width="100"><input type="text" class="text_boxes" name="txt_Yarn_composition_<?=$i; ?>" id="txt_Yarn_composition_<?=$i; ?>" value="<?=$compo; ?>" style="width:86px" readonly placeholder="Display"  ></td>
													<td width="100"><input type="text" class="text_boxes" name="txt_Yarn_Yarn_Dtls_<?=$i; ?>" id="txt_Yarn_Yarn_Dtls_<?=$i; ?>" value="<?=$yarnDtls; ?>" style="width:86px" readonly placeholder="Display" ></td>
                                                    <td width="50"><input type="text" class="text_boxes_numeric" name="txt_ex_percent_yarn_<?=$i; ?>" id="txt_ex_percent_yarn_<?=$i; ?>" value="<?=$yPer; ?>" style="width:35px" readonly placeholder="Display"></td>
													<td width="50" titel="<?=$tot_cons_yarn; ?>"><input type="text" class="text_boxes_numeric" name="txt_qc_Yarn_Rate_<?=$i; ?>" id="txt_qc_Yarn_Rate_<?=$i; ?>" value="<?=$qcRate; $total_qc_rate+=$qcRate*$tot_cons_yarn; ?>" style="width:35px"  readonly="readonly" placeholder="Display"> </td>
													<td><input type="text" class="text_boxes_numeric" name="txt_Yarn_Rate_<?=$i; ?>" qcdata="<?=$tot_cons_yarn.'='.$yPer.'='.$yRate; ?>" id="txt_Yarn_Rate_<?=$i; ?>" value="<?=$yRate; ?>" onChange="calculate_rate(2,1,<?=$i; ?>,this.value);" style="width:35px" placeholder="Display/Write"  >
														<input type="hidden" class="text_boxes_numeric" name="txt_yarn_tot_cons_<?=$i; ?>" id="txt_yarn_tot_cons_<?=$i; ?>" value="<?=$tot_cons_yarn; ?>" style="width:35px" >
														<input type="hidden" class="text_boxes_numeric" name="txt_Yarn_cost_<?=$i; ?>" id="txt_Yarn_cost_<?=$i; ?>" value="" style="width:35px"> 
													</td>
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
												<input type="text" class="text_boxes" name="txt_Yarn_Yarn_Count_1" id="txt_Yarn_Yarn_Count_1" value="" style="width:86px" onClick="fnc_details_popup('1','<?=$qc_no;?>','<?=$exchange_rate;?>','yarn_count_popup');" readonly placeholder="Browse">
												<input type="hidden" class="text_boxes" name="lib_yarn_rate_id_1" id="lib_yarn_rate_id_1"  >
												<input type="hidden" class="text_boxes" name="lib_rate_data_id_1" id="lib_rate_data_id_1" value="" >
												<input type="hidden" class="text_boxes" name="yarn_dtls_update_id_1" id="yarn_dtls_update_id_1" value="" >
											</td>
											<td width="100"><input type="text" class="text_boxes" name="txt_Yarn_Yarn_Type_1" id="txt_Yarn_Yarn_Type_1" value="" style="width:86px" readonly placeholder="Display" ></td>
											<td width="100"><input type="text" class="text_boxes" name="txt_Yarn_composition_1" id="txt_Yarn_composition_1" value="" style="width:86px" readonly placeholder="Display"  ></td>
											<td width="100"><input type="text" class="text_boxes" name="txt_Yarn_Yarn_Dtls_1" id="txt_Yarn_Yarn_Dtls_1" value="" style="width:86px" readonly placeholder="Display" ></td>
                                            <td width="50"><input type="text" class="text_boxes_numeric" name="txt_ex_percent_yarn_1" id="txt_ex_percent_yarn_1" style="width:35px"></td>
											<td width="50"><input type="text" class="text_boxes_numeric" name="txt_qc_Yarn_Rate_1" id="txt_qc_Yarn_Rate_1" value="" style="width:35px"  readonly="readonly" placeholder="Display"> </td>
											<td><input type="text" class="text_boxes_numeric" name="txt_Yarn_Rate_1" id="txt_Yarn_Rate_1" value="" onKeyUp="calculate_rate(2,1,1,this.value)" style="width:35px" placeholder="Display/Write"  >
												<input type="hidden" class="text_boxes_numeric" name="txt_yarn_tot_cons_1" id="txt_yarn_tot_cons_1" style="width:35px" >
												<input type="hidden" class="text_boxes_numeric" name="txt_Yarn_cost_1" id="txt_Yarn_cost_1" style="width:35px" > 
											</td>
										</tr>
                                        <?
									}
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <table width="550" cellpadding="2" cellspacing="2" class="rpt_table" border="1" rules="all" align="left">
                        <tfoot>
                            <tr>
                                <td colspan="3" align="right"><strong>Total Yarn Cost</strong></td>
                                <td width="50"><input type="text" class="text_boxes_numeric" name="txt_qc_total_yarn_cost" id="txt_qc_total_yarn_cost" value="<?=$total_qc_rate; ?>" readonly  style="width:35px"  readonly="readonly" placeholder="Display"></td>
                                <td width="50" ><input type="text" class="text_boxes_numeric" name="txt_total_yarn_cost" id="txt_total_yarn_cost" value="<?=$total_yarn_cost; ?>" readonly placeholder="Display"  style="width:35px" ></td>
                            </tr>
                            <tr>
                                <td colspan="3" align="right">Yarn Dyeing Cost</td>
                                <td width="50"><input type="text" class="text_boxes_numeric" name="txt_qc_yarn_dyeing_cost" id="txt_qc_yarn_dyeing_cost" value="<? echo $total_qc_yd_cost; ?>" style="width:35px" readonly placeholder="Display"> </td>
                                <td width="50"><input type="text" class="text_boxes_numeric" name="txt_yarn_dyeing_cost" id="txt_yarn_dyeing_cost" value="<? echo $yarn_dyeing_cost; ?>" onKeyUp="calculate_rate(2,2,1,this.value)" style="width:35px" placeholder="Write" > </td>
                            </tr>
                        </tfoot>
                    </table>
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
                                            <td width="150"><input type="text" class="text_boxes" name="txt_knit_body_part_<? echo $j; ?>" id="txt_knit_body_part_<? echo $j; ?>"  value="<? echo $row[csf('body_part')]; ?>" style="width:136px" onClick="fnc_details_popup(<? echo $j; ?>,'<?=$qc_no;?>','<?=$exchange_rate;?>','kniting_details_popup')"  readonly="readonly" placeholder="Browse"  >
                                                <input type="hidden" class="text_boxes" name="lib_knit_Yarn_id_<? echo $j; ?>" id="lib_knit_Yarn_id_<? echo $j; ?>"  value="<? echo $row[csf('lib_table_id')]; ?>" >
                                                <input type="hidden" class="text_boxes" name="knit_dtls_update_id_<? echo $j; ?>" id="knit_dtls_update_id_<? echo $j; ?>"  value="<? echo $row[csf('id')]; ?>" >
                                            </td>
                                            <td width="150"><input type="text" class="text_boxes" name="txt_knit_feb_desc_<? echo $j; ?>" id="txt_knit_feb_desc_<? echo $j; ?>"  value="<? echo $row[csf('feb_desc')]; ?>" style="width:136px" readonly placeholder="Display" ></td>
                                            <td width="100"><input type="text" class="text_boxes" name="txt_knit_yarn_desc_<? echo $j; ?>" id="txt_knit_yarn_desc_<? echo $j; ?>"  value="<? echo $row[csf('yarn_desc')]; ?>" style="width:86px" readonly placeholder="Display" ></td>
                                            <td ><input type="text" class="text_boxes_numeric" name="txt_qc_knit_Rate_<? echo $j; ?>" id="txt_qc_knit_Rate_<? echo $j; ?>"  value="<? echo $row[csf('qc_rate')]; $total_qc_knit_cost+=$row[csf('tot_cons')]*$row[csf('qc_rate')]; ?>"  style="width:61px"  readonly="readonly" placeholder="Display" ></td>
                                            <td ><input type="text" class="text_boxes_numeric" name="txt_knit_Rate_<? echo $j; ?>" id="txt_knit_Rate_<? echo $j; ?>"  value="<? echo $row[csf('actual_rate')]; ?>" onKeyUp="calculate_rate(2,3,<? echo $j; ?>,this.value)" style="width:61px" placeholder="Display/Write"  >
                                                <input type="hidden" class="text_boxes" name="lib_knit_rate_data_id_<? echo $j; ?>" id="lib_knit_rate_data_id_<? echo $j; ?>" value="<? echo $row[csf('rate_data_id')]; ?>" >
                                                <input type="hidden" class="text_boxes_numeric" name="txt_knit_tot_cons_<? echo $j; ?>" id="txt_knit_tot_cons_<? echo $j; ?>" value="<? echo $row[csf('tot_cons')]; ?>" style="width:61px" >
                                                <input type="hidden" class="text_boxes_numeric" name="txt_knit_cost_<? echo $j; ?>" id="txt_knit_cost_<? echo $j; ?>" value="<? echo $row[csf('actual_cost')]; ?>"   style="width:61px" > 
                                            </td>
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
													<td width="150"><input type="text" class="text_boxes" name="txt_knit_body_part_<?=$j; ?>" id="txt_knit_body_part_<?=$j; ?>" value="<?=$bodyPart; ?>" style="width:136px" onClick="fnc_details_popup(<?=$j; ?>,'<?=$qc_no; ?>','<?=$exchange_rate;?>','kniting_details_popup')"  readonly="readonly" placeholder="Browse"  >
														<input type="hidden" class="text_boxes" name="lib_knit_Yarn_id_<?=$j; ?>" id="lib_knit_Yarn_id_<?=$j; ?>" value="<?=$rate_data_id; ?>" >
														<input type="hidden" class="text_boxes" name="knit_dtls_update_id_<?=$j; ?>" id="knit_dtls_update_id_<?=$j; ?>"  value="" >
													</td>
													<td width="150"><input type="text" class="text_boxes" name="txt_knit_feb_desc_<?=$j; ?>" id="txt_knit_feb_desc_<?=$j; ?>" value="<?=$const_comp; ?>" style="width:136px" readonly placeholder="Display" ></td>
													<td width="100"><input type="text" class="text_boxes" name="txt_knit_yarn_desc_<?=$j; ?>" id="txt_knit_yarn_desc_<?=$j; ?>" value="<?=$yarn_description; ?>" style="width:86px" readonly placeholder="Display" ></td>
													<td ><input type="text" class="text_boxes_numeric" name="txt_qc_knit_Rate_<?=$j; ?>" id="txt_qc_knit_Rate_<?=$j; ?>"  value="<?=$qcRate; $total_qc_knit_cost+=$tot_cons_knit*$qcRate; ?>" style="width:61px" tr="<?=$tot_cons_knit*$qcRate; ?>" readonly placeholder="Display" ></td>
													<td><input type="text" class="text_boxes_numeric" name="txt_knit_Rate_<?=$j; ?>" id="txt_knit_Rate_<?=$j; ?>"  value="<?=$in_house_rate; ?>" onKeyUp="calculate_rate(2,3,<?=$j; ?>,this.value)" style="width:61px" placeholder="Display/Write"  >
														<input type="hidden" class="text_boxes_numeric" name="txt_knit_tot_cons_<?=$j; ?>" id="txt_knit_tot_cons_<?=$j; ?>" value="<?=$tot_cons_knit; $knitting_cost+=$tot_cons_knit*$in_house_rate; ?>" style="width:61px" >
														<input type="hidden" class="text_boxes" name="lib_knit_rate_data_id_<?=$j; ?>" id="lib_knit_rate_data_id_<?=$j; ?>" value="<?=$kid; ?>" >
														<input type="hidden" class="text_boxes_numeric" name="txt_knit_cost_<?=$j; ?>" id="txt_knit_cost_<?=$j; ?>" value="" style="width:61px" >
													</td>
												</tr>
											<? $j++;
											}
                                        }
                                    }
									else
									{
										?>
                                        <tr> 
                                            <td width="150"><input type="text" class="text_boxes" name="txt_knit_body_part_1" id="txt_knit_body_part_1" value="" style="width:136px" onClick="fnc_details_popup(1,'<?=$qc_no;?>','<?=$exchange_rate;?>','kniting_details_popup')"  readonly="readonly" placeholder="Browse"  >
                                                <input type="hidden" class="text_boxes" name="lib_knit_Yarn_id_1" id="lib_knit_Yarn_id_1" value="" >
                                                <input type="hidden" class="text_boxes" name="knit_dtls_update_id_1" id="knit_dtls_update_id_1"  value="" >
                                            </td>
                                            <td width="150"><input type="text" class="text_boxes" name="txt_knit_feb_desc_1" id="txt_knit_feb_desc_1" value="" style="width:136px" readonly placeholder="Display" ></td>
                                            <td width="100"><input type="text" class="text_boxes" name="txt_knit_yarn_desc_1" id="txt_knit_yarn_desc_1" value="" style="width:86px" readonly placeholder="Display" ></td>
                                            <td ><input type="text" class="text_boxes_numeric" name="txt_qc_knit_Rate_1" id="txt_qc_knit_Rate_1"  value=""  style="width:61px"  readonly="readonly" placeholder="Display" ></td>
                                            <td><input type="text" class="text_boxes_numeric" name="txt_knit_Rate_1" id="txt_knit_Rate_1"  value="" onKeyUp="calculate_rate(2,3,1,this.value)" style="width:61px" placeholder="Display/Write"  >
                                                <input type="hidden" class="text_boxes_numeric" name="txt_knit_tot_cons_1" id="txt_knit_tot_cons_1" value="" style="width:61px" >
                                                <input type="hidden" class="text_boxes" name="lib_knit_rate_data_id_1" id="lib_knit_rate_data_id_1" value="" >
                                                <input type="hidden" class="text_boxes_numeric" name="txt_knit_cost_1" id="txt_knit_cost_1" value="" style="width:61px">
                                            </td>
                                        </tr>
                                        <?	
									}
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <table width="550" cellpadding="2" cellspacing="2" class="rpt_table" border="1" rules="all" align="left">
                        <tfoot>
                            <tr>
                                <td colspan="3" align="right"><strong>Knitting Cost</strong></td>
                                <td width="74"><input type="text" class="text_boxes_numeric" name="txt_qc_knitting_cost" id="txt_qc_knitting_cost" value="<? echo $total_qc_knit_cost; ?>" readonly style="width:61px"  readonly="readonly" placeholder="Display"></td>
                                <td width="74"><input type="text" class="text_boxes_numeric" name="txt_knitting_cost" id="txt_knitting_cost" value="<? echo $knitting_cost; ?>" readonly placeholder="Display" style="width:61px" ></td>
                            </tr>
                        </tfoot>
                    </table>
                    
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
                                            <td width="150"><input type="text" class="text_boxes" name="txt_df_Color_Type_<? echo $k; ?>" id="txt_df_Color_Type_<? echo $k; ?>" value="<? echo $row[csf('color_type')]; ?>" style="width:136px" onClick="fnc_details_popup(<? echo $k; ?>,'<?=$qc_no;?>','<?=$exchange_rate;?>','dyeing_finishing_popup')"  readonly="readonly" placeholder="Browse" >
                                                <input type="hidden" class="text_boxes" name="lib_dyeing_finishing_id_<? echo $k; ?>" id="lib_dyeing_finishing_id_<? echo $k; ?>"  value="<? echo $row[csf('lib_table_id')]; ?>" >
                                                <input type="hidden" class="text_boxes" name="df_dtls_update_id_<? echo $k; ?>" id="df_dtls_update_id_<? echo $k; ?>" value="<? echo $row[csf('id')]; ?>" >
                                            </td>
                                            <td width="150"><input type="text" class="text_boxes" name="txt_df_Color_<? echo $k; ?>" id="txt_df_Color_<? echo $k; ?>" value="<? echo $row[csf('color')]; ?>" style="width:136px" readonly placeholder="Display" ></td>
                                            <td width="100"><input type="text" class="text_boxes" name="txt_df_process_<? echo $k; ?>" id="txt_df_process_<? echo $k; ?>" value="<? echo $row[csf('process')]; ?>" style="width:86px" readonly placeholder="Display" ></td>
                                            <td width="75" ><input type="text" class="text_boxes_numeric" name="txt_qc_df_Rate_<? echo $k; ?>" id="txt_qc_df_Rate_<? echo $k; ?>" value="<? echo $row[csf('qc_rate')]; $total_qc_df_cost+=$row[csf('qc_rate')]*$row[csf('tot_cons')];  ?>"  style="width:61px" readonly placeholder="Display" ></td>
                                            <td ><input type="text" class="text_boxes_numeric" name="txt_df_Rate_<? echo $k; ?>" id="txt_df_Rate_<? echo $k; ?>" value="<? echo $row[csf('actual_rate')]; ?>"  onKeyUp="calculate_rate(2,4,<? echo $k; ?>,this.value)" style="width:61px" placeholder="Display/Write" >
                                                <input type="hidden" class="text_boxes" name="lib_df_rate_data_id_<? echo $k; ?>" id="lib_df_rate_data_id_<? echo $k; ?>" value="<? echo $row[csf('rate_data_id')]; ?>" >
                                                <input type="hidden" class="text_boxes_numeric" name="txt_df_tot_cons_<? echo $k; ?>" id="txt_df_tot_cons_<? echo $k; ?>" value="<? echo $row[csf('tot_cons')] ; ?>" style="width:61px" >
                                                <input type="hidden" class="text_boxes_numeric" name="txt_df_cost_<? echo $k; ?>" id="txt_df_cost_<? echo $k; ?>" value="<? echo $row[csf('actual_cost')]; ?>"   style="width:61px" > 
                                            </td>
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
                                                <td width="150"><input type="text" class="text_boxes" name="txt_df_Color_Type_<?=$k; ?>" id="txt_df_Color_Type_<?=$k; ?>" value="<?=$color_range_id; ?>" style="width:136px" onClick="fnc_details_popup(<?=$k; ?>,'<?=$qc_no;?>','<?=$exchange_rate;?>','dyeing_finishing_popup')" readonly placeholder="Browse" >
                                                    <input type="hidden" class="text_boxes" name="lib_dyeing_finishing_id_<? echo $k; ?>" id="lib_dyeing_finishing_id_<? echo $k; ?>"  value="<?=$rate_data_id; ?>" >
                                                    <input type="hidden" class="text_boxes" name="df_dtls_update_id_<? echo $k; ?>" id="df_dtls_update_id_<? echo $k; ?>" value="<? echo $row[csf('id')]; ?>" >
                                                </td>
                                                <td width="150"><input type="text" class="text_boxes" name="txt_df_Color_<? echo $k; ?>" id="txt_df_Color_<? echo $k; ?>" value="<?=$colorName; ?>" style="width:136px" readonly placeholder="Display" ></td>
                                                <td width="100"><input type="text" class="text_boxes" name="txt_df_process_<?=$k; ?>" id="txt_df_process_<?=$k; ?>" value="<?=$process_type_id; ?>" style="width:86px" readonly placeholder="Display" ></td>
                                                <td width="75"><input type="text" class="text_boxes_numeric" name="txt_qc_df_Rate_<? echo $k; ?>" id="txt_qc_df_Rate_<? echo $k; ?>" value="<?=$qcRate; $total_qc_df_cost+=$qcRate*$tot_cons_df; ?>" style="width:61px" readonly placeholder="Display" ></td>
                                                <td ><input type="text" class="text_boxes_numeric" name="txt_df_Rate_<? echo $k; ?>" id="txt_df_Rate_<? echo $k; ?>" value="<?=$in_house_rate; ?>"  onKeyUp="calculate_rate(2,4,<? echo $k; ?>,this.value)" style="width:61px" placeholder="Display/Write" >
                                                    <input type="hidden" class="text_boxes" name="lib_df_rate_data_id_<? echo $k; ?>" id="lib_df_rate_data_id_<? echo $k; ?>" value="<?=$drid; ?>" >
                                                    <input type="hidden" class="text_boxes_numeric" name="txt_df_cost_<? echo $k; ?>" id="txt_df_cost_<? echo $k; ?>" value=""   style="width:61px" >
                                                    <input type="hidden" class="text_boxes_numeric" name="txt_df_tot_cons_<? echo $k; ?>" id="txt_df_tot_cons_<? echo $k; ?>" value="<?=$tot_cons_df; $df_cost+=$in_house_rate*$tot_cons_df; ?>" style="width:61px" ></td>
                                            </tr>
                                            <? $k++;
											}
                                        }
                                    }
									else
									{
										?>
                                        <tr>
                                            <td width="150"><input type="text" class="text_boxes" name="txt_df_Color_Type_1" id="txt_df_Color_Type_1" value="" style="width:136px" onClick="fnc_details_popup(1,'<?=$qc_no;?>','<?=$exchange_rate;?>','dyeing_finishing_popup')" readonly placeholder="Browse" >
                                                <input type="hidden" class="text_boxes" name="lib_dyeing_finishing_id_1" id="lib_dyeing_finishing_id_1" value="" >
                                                <input type="hidden" class="text_boxes" name="df_dtls_update_id_1" id="df_dtls_update_id_1" value="" >
                                            </td>
                                            <td width="150"><input type="text" class="text_boxes" name="txt_df_Color_1" id="txt_df_Color_1" value="" style="width:136px" readonly placeholder="Display" ></td>
                                            <td width="100"><input type="text" class="text_boxes" name="txt_df_process_1" id="txt_df_process_1" value="" style="width:86px" readonly placeholder="Display" ></td>
                                            <td width="75" ><input type="text" class="text_boxes_numeric" name="txt_qc_df_Rate_1" id="txt_qc_df_Rate_1" value=""  style="width:61px" readonly placeholder="Display" ></td>
                                            <td ><input type="text" class="text_boxes_numeric" name="txt_df_Rate_1" id="txt_df_Rate_1" value=""  onKeyUp="calculate_rate(2,4,1,this.value)" style="width:61px" placeholder="Display/Write" >
                                                <input type="hidden" class="text_boxes" name="lib_df_rate_data_id_1" id="lib_df_rate_data_id_1" value="" >
                                                <input type="hidden" class="text_boxes_numeric" name="txt_df_cost_1" id="txt_df_cost_1" value=""   style="width:61px" >
                                                <input type="hidden" class="text_boxes_numeric" name="txt_df_tot_cons_1" id="txt_df_tot_cons_1" value="" style="width:61px" ></td>
                                        </tr>
                                        <?
									}
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <table width="550" cellpadding="2" cellspacing="2" class="rpt_table" border="1" rules="all" align="left">
                        <tfoot>
                            <tr>
                                <td colspan="3" align="right"><strong>Dyeing Finishing Cost</strong></td>
                                <td width="74" ><input type="text" class="text_boxes_numeric" name="txt_qc_df_cost" id="txt_qc_df_cost" value="<?=$total_qc_df_cost; ?>" readonly style="width:61px"  readonly="readonly" placeholder="Display" ></td>
                                <td width="74"  ><input type="text" class="text_boxes_numeric" name="txt_df_cost" id="txt_df_cost" value="<?=$df_cost; ?>" readonly placeholder="Display" style="width:61px" readonly="readonly" placeholder="Display"  ></td>
                            </tr>
                        </tfoot>
                    </table>
                    
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
                                            <td width="150"><input type="text" class="text_boxes" name="txt_aop_Color_Type_<? echo $m; ?>" id="txt_aop_Color_Type_<? echo $m; ?>" value="<? echo $row[csf('color_type')]; ?>" style="width:136px" onClick="fnc_details_popup(<? echo $m; ?>,'<?=$qc_no;?>','<?=$exchange_rate;?>','aop_finishing_popup')"  readonly="readonly" placeholder="Browse" >
                                                <input type="hidden" class="text_boxes" name="lib_aop_id_<? echo $m; ?>" id="lib_aop_id_<? echo $m; ?>"  value="<? echo $row[csf('lib_table_id')]; ?>" >
                                                <input type="hidden" class="text_boxes" name="aop_dtls_update_id_<? echo $m; ?>" id="aop_dtls_update_id_<? echo $m; ?>" value="<? echo $row[csf('id')]; ?>" >
                                            </td>
                                            <td width="150"><input type="text" class="text_boxes" name="txt_aop_Color_<? echo $m; ?>" id="txt_aop_Color_<? echo $m; ?>" value="<? echo $row[csf('color')]; ?>" style="width:136px" readonly placeholder="Display" ></td>
                                            <td width="100"><input type="text" class="text_boxes" name="txt_aop_process_<? echo $m; ?>" id="txt_aop_process_<? echo $m; ?>" value="<? echo $row[csf('process')]; ?>" style="width:86px" readonly placeholder="Display" ></td>
                                            <td width="75" ><input type="text" class="text_boxes_numeric" name="txt_qc_aop_Rate_<? echo $m; ?>" id="txt_qc_aop_Rate_<? echo $m; ?>" value="<? echo $row[csf('qc_rate')]; $total_qc_aop_cost+=$row[csf('qc_rate')]*$row[csf('tot_cons')]; ?>"   style="width:61px" readonly placeholder="Display" ></td>
                                            <td ><input type="text" class="text_boxes_numeric" name="txt_aop_Rate_<? echo $m; ?>" id="txt_aop_Rate_<? echo $m; ?>" value="<? echo $row[csf('actual_rate')]; ?>"  onKeyUp="calculate_rate(2,5,<? echo $m; ?>,this.value)" style="width:61px" placeholder="Display/Write" >
                                                <input type="hidden" class="text_boxes_numeric" name="txt_aop_tot_cons_<? echo $m; ?>" id="txt_aop_tot_cons_<? echo $m; ?>" value="<? echo $row[csf('tot_cons')]; ?>" style="width:61px" >
                                                <input type="hidden" class="text_boxes" name="lib_aop_rate_data_id_<? echo $m; ?>" id="lib_aop_rate_data_id_<? echo $m; ?>" value="<? echo $row[csf('rate_data_id')]; ?>" ></td>
                                                <input type="hidden" class="text_boxes_numeric" name="txt_aop_cost_<? echo $m; ?>" id="txt_aop_cost_<? echo $m; ?>" value="<? echo $row[csf('actual_cost')]; ?>"   style="width:61px" > 
                                        </tr>
                                        <? $m++;
                                    }
                                }
                                else
                                {
                                    if(count($aopDataArr)>0)
									{
                                        $m=1; $tot_cons_aop=''; $rate_data=$qcData=$qcRate=''; $rate_data_id=''; $qcData="";
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
                                                <td width="150"><input type="text" class="text_boxes" name="txt_aop_Color_Type_<? echo $m; ?>" id="txt_aop_Color_Type_<? echo $m; ?>" value="<?=$color_range_id; ?>" style="width:136px" onClick="fnc_details_popup(<? echo $m; ?>,'<?=$qc_no;?>','<?=$exchange_rate;?>','aop_finishing_popup')"  readonly="readonly" placeholder="Browse" >
                                                    <input type="hidden" class="text_boxes" name="lib_aop_id_<? echo $m; ?>" id="lib_aop_id_<? echo $m; ?>"  value="<? echo $row[csf('lib_table_id')]; ?>" >
                                                    <input type="hidden" class="text_boxes" name="aop_dtls_update_id_<? echo $m; ?>" id="aop_dtls_update_id_<? echo $m; ?>" value="<? echo $row[csf('id')]; ?>" >
                                                </td>
                                                <td width="150"><input type="text" class="text_boxes" name="txt_aop_Color_<? echo $m; ?>" id="txt_aop_Color_<? echo $m; ?>" value="<?=$colorName; ?>" style="width:136px" readonly placeholder="Display" ></td>
                                                <td width="100"><input type="text" class="text_boxes" name="txt_aop_process_<? echo $m; ?>" id="txt_aop_process_<? echo $m; ?>" value="<?=$process_type_id; ?>" style="width:86px" readonly placeholder="Display" ></td>
                                                <td width="75" ><input type="text" class="text_boxes_numeric" name="txt_qc_aop_Rate_<? echo $m; ?>" id="txt_qc_aop_Rate_<? echo $m; ?>" value="<?=$qcRate; $total_qc_aop_cost+=$qcRate*$tot_cons_aop; ?>"   style="width:61px" readonly placeholder="Display" ></td>
                                                <td ><input type="text" class="text_boxes_numeric" name="txt_aop_Rate_<? echo $m; ?>" id="txt_aop_Rate_<? echo $m; ?>" value="<?=$in_house_rate; ?>"  onKeyUp="calculate_rate(2,5,<? echo $m; ?>,this.value)" style="width:61px" placeholder="Display/Write" >
                                                <input type="hidden" class="text_boxes_numeric" name="txt_aop_tot_cons_<? echo $m; ?>" id="txt_aop_tot_cons_<? echo $m; ?>" value="<?=$tot_cons_aop; $aop_cost+=$in_house_rate*$tot_cons_aop; ?>" style="width:61px" >
                                                <input type="hidden" class="text_boxes" name="lib_aop_rate_data_id_<? echo $m; ?>" id="lib_aop_rate_data_id_<? echo $m; ?>" value="<?=$arid; ?>" >
                                                <input type="hidden" class="text_boxes_numeric" name="txt_aop_cost_<? echo $m; ?>" id="txt_aop_cost_<? echo $m; ?>" value=""   style="width:61px" > </td>
                                            </tr>
                                            <? $m++;
											}
                                        }
                                    }
									else
									{
										?>
                                        <tr>
                                            <td width="150"><input type="text" class="text_boxes" name="txt_aop_Color_Type_1" id="txt_aop_Color_Type_1" value="" style="width:136px" onClick="fnc_details_popup(1,'<?=$qc_no; ?>','<?=$exchange_rate; ?>','aop_finishing_popup')"  readonly="readonly" placeholder="Browse" >
                                                <input type="hidden" class="text_boxes" name="lib_aop_id_1" id="lib_aop_id_1"  value="" >
                                                <input type="hidden" class="text_boxes" name="aop_dtls_update_id_1" id="aop_dtls_update_id_1" value="" >
                                            </td>
                                            <td width="150"><input type="text" class="text_boxes" name="txt_aop_Color_1" id="txt_aop_Color_1" value="" style="width:136px" readonly placeholder="Display" ></td>
                                            <td width="100"><input type="text" class="text_boxes" name="txt_aop_process_1" id="txt_aop_process_1" value="" style="width:86px" readonly placeholder="Display" ></td>
                                            <td width="75" ><input type="text" class="text_boxes_numeric" name="txt_qc_aop_Rate_1" id="txt_qc_aop_Rate_1" value=""   style="width:61px" readonly placeholder="Display" ></td>
                                            <td ><input type="text" class="text_boxes_numeric" name="txt_aop_Rate_1" id="txt_aop_Rate_1" value=""  onKeyUp="calculate_rate(2,5,1,this.value)" style="width:61px" placeholder="Display/Write" >
                                            <input type="hidden" class="text_boxes_numeric" name="txt_aop_tot_cons_1" id="txt_aop_tot_cons_1" value="" style="width:61px" >
                                            <input type="hidden" class="text_boxes" name="lib_aop_rate_data_id_1" id="lib_aop_rate_data_id_1" value="" >
                                            <input type="hidden" class="text_boxes_numeric" name="txt_aop_cost_1" id="txt_aop_cost_1" value=""   style="width:61px" > </td>
                                        </tr>
                                        <?	
									}
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <table width="550" cellpadding="2" cellspacing="2" class="rpt_table" border="1" rules="all" align="left">
                        <tfoot>
                            <tr>
                                <td colspan="3" align="right"><strong>AOP Cost</strong></td>
                                <td width="74"><input type="text" class="text_boxes_numeric" name="txt_qc_aop_cost" id="txt_qc_aop_cost" value="<?=$total_qc_aop_cost; ?>" style="width:61px"  readonly="readonly" placeholder="Display" ></td>
                                <td width="74"><input type="text" class="text_boxes_numeric" name="txt_aop_cost" id="txt_aop_cost" value="<?=$aop_cost; ?>" placeholder="Display" style="width:61px"  ></td>
                            </tr>
                            <tr>
                                <td colspan="4" align="right"><strong>Fabric Purchase [Kg]</strong></td>
                                <td width="74"><input type="text" class="text_boxes_numeric" name="txtfabricpurchasekg" id="txtfabricpurchasekg" value="<?=$withOutConsRateCost; ?>" readonly style="width:61px"  readonly="readonly" placeholder="Display"></td>
                            </tr>
                            <tr>
                                <td colspan="4" align="right"><strong>Fabric Purchase Cost[Yds]</strong></td>
                                <td width="74"><input type="text" class="text_boxes_numeric" name="txtfabricpurchaseyds" id="txtfabricpurchaseyds" value="<?=$ydsAmount; ?>" readonly style="width:61px"  readonly="readonly" placeholder="Display"></td>
                            </tr>
                            <tr>
                                <?
                                    $qc_total_cost=$total_qc_aop_cost+$total_qc_df_cost+$total_qc_knit_cost+$total_qc_yd_cost+$total_qc_rate; 
                                    $total_cost=$total_yarn_cost+$yarn_dyeing_cost+$knitting_cost+$df_cost+$aop_cost+$withOutConsRateCost+$ydsAmount; 
                                    $total_fab_cost=$total_cost*$tot_cons;
									$fabric_cost_qc=$total_qc_rate+$total_qc_yd_cost+$total_qc_knit_cost+$total_qc_df_cost+$total_qc_aop_cost+$withOutConsRateCost+$ydsAmount;
                                ?>
                                <td colspan="3" align="right"><strong>Fabric Total Cost</strong></td>
                                <td width="74"><input type="text" class="text_boxes_numeric" name="txt_qc_total_cost" id="txt_qc_total_cost" value="<? echo $fabric_cost_qc; //$qc_total_cost; ?>" readonly style="width:61px"  readonly="readonly" placeholder="Display"></td>
                                <td width="74"><input type="text" class="text_boxes_numeric" name="txt_total_cost" id="txt_total_cost" value="<? echo $total_cost; ?>" readonly placeholder="Display" style="width:61px"  ></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div style="width: 100%">
                <table  style="width: 100%">
                    <tr>
                        <td height="50" valign="middle" align="center" class="button_container">
                            <input type="hidden" class="text_boxes" name="hid_qc_no" id="hid_qc_no" value="<?=$qc_no ?>"> 
                            <input type="hidden" class="text_boxes" name="update_id" id="update_id" value="<?=$update_id ?>">
                            <input type="hidden" class="text_boxes" name="hid_tot_cons" id="hid_tot_cons" value="<?=$tot_cons ?>">
                        <? echo load_submit_buttons( $permission, "fnc_cost_entry", $update_button_active ,0 ,"reset_for_refresh()",1); ?>
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
		calculate_marketing_cost();
    </script>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
    <?
	exit();
}

if ($action=="save_update_delete")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));
    //echo "10**".$operation; die;
    if ($operation==0)  // Insert Here
    {
        $con = connect();
        if($db_type==0)
        {
            mysql_query("BEGIN");
        }


        $id=return_next_id( "id", "qc_margin_mst", 1 );
        $dtlsId=return_next_id( "id", "qc_margin_dtls", 1 );
        //$field_array="id,qc_no,fabric_cost,accessories_cost,avl_min,cm_cost,frieght_cost,lab_test_cost,mis_offer_qty,other_cost,com_cost,fob,fob_pcs,margin,margin_percent,Yarn_Yarn_Count_1,Yarn_Yarn_Type_1,Yarn_Rate_1,Yarn_Yarn_Count_2,Yarn_Yarn_Type_2,Yarn_Rate_2,Yarn_Yarn_Count_3,Yarn_Yarn_Type_3,Yarn_Rate_3,total_yarn_cost,yarn_dyeing_cost,knit_Yarn_Count_1,knit_Fabric_Type_1,knit_Rate_1,knit_Yarn_Count_2,knit_Fabric_Type_2,knit_Rate_2,knitting_cost,df_Color_Type_1,df_Color_1,df_Rate_1,df_Color_Type_2,df_Color_2,df_Rate_2,df_Color_Type_3,df_Color_3,df_Rate_3,df_cost,aop_cost,total_cost,inserted_by,insert_date";
        
        $field_array="id, qc_no, fabric_cost, accessories_cost, avl_min,cm_cost, frieght_cost, lab_test_cost, mis_offer_qty, other_cost, commercial_cost, com_cost, fob, fob_pcs, margin, margin_percent, total_yarn_cost, yarn_dyeing_cost, knitting_cost, df_cost, aop_cost, total_cost, buyer, cpm, smv, efficency, cm, available_min, special_operation, main_fabric_top, rib, hood, others, totbodycons, yds, fabricpurchasekg, fabricpurchaseyds, inserted_by, insert_date";
        
        $field_array_dtls=" id, qc_no, mst_id, type, lib_table_id, rate_data_id, yarn_count, yarn_type, yarn_details, qc_rate, actual_rate, febric_type, color_type, color, composition, body_part, feb_desc, yarn_desc, process, tot_cons, actual_cost, ex_percent, inserted_by, insert_date";

        $data_array="(".$id.",".$hid_qc_no.",".$txt_fabric.",".$txt_accessories.",".$txt_avl_min.",".$txt_cm_dzn.",".$txt_frieght_dzn.",".$txt_lab_dzn.",".$txt_mis_offer_qty.",".$txt_other_cost_dzn.",".$txt_commercial_cost_dzn.",".$txt_com_dzn.",".$txt_fob_dzn.",".$txt_fob_pcs.",".$txt_margin_per_dzn.",".$txt_margin.",".$txt_total_yarn_cost.",".$txt_yarn_dyeing_cost.",".$txt_knitting_cost.",".$txt_df_cost.",".$txt_aop_cost.",".$txt_total_cost.",".$txt_buyer.",".$txt_cpm.",".$txt_smv.",".$txt_efficency.",".$txt_cm.",".$txt_available_min.",".$txt_special_operation.",".$txtactualmainfabric.",".$txtactualrib.",".$txtactualhood.",".$txtactualothers.",".$txtactualtotcons.",".$txtactualyds.",".$txtfabricpurchasekg.",".$txtfabricpurchaseyds.",".$user_id.",'".$pc_date_time."')";

        $add_commaa=0; $data_array_dtls=''; 
        for($i=1;$i<=$numRowYarn;$i++) 
        {
            $lib_yarn_rate_id       ="lib_yarn_rate_id_".$i;
            $lib_rate_data_id       ="lib_rate_data_id_".$i;
            $txt_Yarn_Yarn_Count    ="txt_Yarn_Yarn_Count_".$i;
            $txt_Yarn_composition   ="txt_Yarn_composition_".$i;
            $txt_Yarn_Yarn_Type     ="txt_Yarn_Yarn_Type_".$i;
            $txt_Yarn_Yarn_Dtls     ="txt_Yarn_Yarn_Dtls_".$i;
            $txt_qc_Yarn_Rate       ="txt_qc_Yarn_Rate_".$i;
            $txt_Yarn_Rate          ="txt_Yarn_Rate_".$i;
            $txt_yarn_tot_cons      ="txt_yarn_tot_cons_".$i;
            $txt_Yarn_cost          ="txt_Yarn_cost_".$i;
            $txt_ex_percent_yarn    ="txt_ex_percent_yarn_".$i;

            if ($add_commaa!=0) $data_array_dtls .=","; $add_commaa=0;
            $data_array_dtls .="(".$dtlsId.",".$hid_qc_no.",".$id.",1,".$$lib_yarn_rate_id.",".$$lib_rate_data_id.",".$$txt_Yarn_Yarn_Count.",".$$txt_Yarn_Yarn_Type.",".$$txt_Yarn_Yarn_Dtls.",".$$txt_qc_Yarn_Rate.",".$$txt_Yarn_Rate.",'','','',".$$txt_Yarn_composition.",'','','','',".$$txt_yarn_tot_cons.",".$$txt_Yarn_cost.",".$$txt_ex_percent_yarn.",'".$user_id."','".$pc_date_time."')";
            $dtlsId++; $add_commaa++;
        }
        //echo "5**$add_commaa insert into qc_margin_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
        for($j=1;$j<=$numRowknit;$j++)
        {
            $lib_knit_Yarn_id       ="lib_knit_Yarn_id_".$j;
            $txt_knit_body_part     ="txt_knit_body_part_".$j;
            $txt_knit_feb_desc      ="txt_knit_feb_desc_".$j;
            $txt_knit_yarn_desc     ="txt_knit_yarn_desc_".$j;
            $txt_qc_knit_Rate       ="txt_qc_knit_Rate_".$j;
            $txt_knit_Rate          ="txt_knit_Rate_".$j;
            $lib_knit_rate_data_id  ="lib_knit_rate_data_id_".$j;
            $txt_knit_tot_cons      ="txt_knit_tot_cons_".$j;
            $txt_knit_cost          ="txt_knit_cost_".$j;
           
            if ($add_commaa!=0) $data_array_dtls .=","; $add_commaa=0;
            $data_array_dtls .="(".$dtlsId.",".$hid_qc_no.",".$id.",2,".$$lib_knit_Yarn_id.",".$$lib_knit_rate_data_id.",'','','',".$$txt_qc_knit_Rate.",".$$txt_knit_Rate.",'','','','',".$$txt_knit_body_part.",".$$txt_knit_feb_desc.",".$$txt_knit_yarn_desc.",'',".$$txt_knit_tot_cons.",".$$txt_knit_cost.",'','".$user_id."','".$pc_date_time."')";
            $dtlsId++; $add_commaa++;
        }
        
        for($k=1;$k<=$numRowDF;$k++)
        {
            $lib_dyeing_finishing_id    ="lib_dyeing_finishing_id_".$k;
            $txt_df_Color_Type          ="txt_df_Color_Type_".$k;
            $txt_df_Color               ="txt_df_Color_".$k;
            $txt_df_process             ="txt_df_process_".$k;
            $txt_qc_df_Rate             ="txt_qc_df_Rate_".$k;
            $txt_df_Rate                ="txt_df_Rate_".$k;
            $lib_df_rate_data_id        ="lib_df_rate_data_id_".$k;
            $txt_df_tot_cons            ="txt_df_tot_cons_".$k;
            $txt_df_cost                ="txt_df_cost_".$k;
            
            if ($add_commaa!=0) $data_array_dtls .=","; $add_commaa=0;
            $data_array_dtls .="(".$dtlsId.",".$hid_qc_no.",".$id.",3,".$$lib_dyeing_finishing_id.",".$$lib_df_rate_data_id.",'','','',".$$txt_qc_df_Rate.",".$$txt_df_Rate.",'',".$$txt_df_Color_Type.",".$$txt_df_Color.",'','','','',".$$txt_df_process.",".$$txt_df_tot_cons.",".$$txt_df_cost.",'','".$user_id."','".$pc_date_time."')";
            $dtlsId++; $add_commaa++;
        }
        //echo "5**insert into qc_margin_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
        for($m=1;$m<=$numRowAop;$m++)
        {
            $lib_aop_id                 ="lib_aop_id_".$m;
            $txt_aop_Color_Type         ="txt_aop_Color_Type_".$m;
            $txt_aop_Color              ="txt_aop_Color_".$m;
            $txt_aop_process            ="txt_aop_process_".$m;
            $txt_qc_aop_Rate            ="txt_qc_aop_Rate_".$m;
            $txt_aop_Rate               ="txt_aop_Rate_".$m;
            $lib_aop_rate_data_id       ="lib_aop_rate_data_id_".$m;
            $txt_aop_tot_cons           ="txt_aop_tot_cons_".$m;
            $txt_aop_cost               ="txt_aop_cost_".$m;

            if ($add_commaa!=0) $data_array_dtls .=","; $add_commaa=0;
            $data_array_dtls .="(".$dtlsId.",".$hid_qc_no.",".$id.",4,".$$lib_aop_id.",".$$lib_aop_rate_data_id.",'','','',".$$txt_qc_df_Rate.",".$$txt_aop_Rate.",'',".$$txt_aop_Color_Type.",".$$txt_aop_Color.",'','','','',".$$txt_aop_process.",".$$txt_aop_tot_cons.",".$$txt_aop_cost.",'','".$user_id."','".$pc_date_time."')";
            $dtlsId++; $add_commaa++;
        }

        //echo "5**insert into qc_margin_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
        if($data_array!=""){
            $rID1=sql_insert("qc_margin_mst",$field_array,$data_array,0);
        }
        if($data_array_dtls!=""){
            $rID2=sql_insert("qc_margin_dtls",$field_array_dtls,$data_array_dtls,0);
        }
        //echo "10**".$rID1 ."&&".  $rID2;die;

        if($db_type==0)
        {
            if($rID1)
            {
                mysql_query("COMMIT");
                echo "0**".str_replace("'", '', $id);
            }
            else
            {
                mysql_query("ROLLBACK");
                echo "6**".str_replace("'", '', $id);
            }
        }
        else if($db_type==2 || $db_type==1 )
        {
            if($rID1 )
            {
                oci_commit($con);
                echo "0**".str_replace("'", '', $id);
            }
            else
            {
                oci_rollback($con);
                echo "6**".str_replace("'", '', $id);
            }
        }
    }
    else if ($operation==1)   // Update Here
    {
        $con = connect();
        if($db_type==0)
        {
            mysql_query("BEGIN");
        }

        $field_array="fabric_cost*accessories_cost*avl_min*cm_cost*frieght_cost*lab_test_cost*mis_offer_qty*other_cost*commercial_cost*com_cost*fob*fob_pcs*margin*margin_percent*total_yarn_cost*yarn_dyeing_cost*knitting_cost*df_cost*aop_cost*total_cost*buyer*cpm*smv*efficency*cm*available_min*special_operation*main_fabric_top*rib*hood*others*totbodycons*yds*fabricpurchasekg*fabricpurchaseyds*updated_by*update_date";

        $data_array=$txt_fabric."*".$txt_accessories."*".$txt_avl_min."*".$txt_cm_dzn."*".$txt_frieght_dzn."*".$txt_lab_dzn."*".$txt_mis_offer_qty."*".$txt_other_cost_dzn."*".$txt_commercial_cost_dzn."*".$txt_com_dzn."*".$txt_fob_dzn."*".$txt_fob_pcs."*".$txt_margin_per_dzn."*".$txt_margin."*".$txt_total_yarn_cost."*".$txt_yarn_dyeing_cost."*".$txt_knitting_cost."*".$txt_df_cost."*".$txt_aop_cost."*".$txt_total_cost."*".$txt_buyer."*".$txt_cpm."*".$txt_smv."*".$txt_efficency."*".$txt_cm."*".$txt_available_min."*".$txt_special_operation."*".$txtactualmainfabric."*".$txtactualrib."*".$txtactualhood."*".$txtactualothers."*".$txtactualtotcons."*".$txtactualyds."*".$txtfabricpurchasekg."*".$txtfabricpurchaseyds."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

        $field_array_dtls="lib_table_id*rate_data_id*yarn_count*yarn_type*yarn_details*qc_rate*actual_rate*febric_type*color_type*color*composition*body_part*feb_desc*yarn_desc*process*tot_cons*actual_cost*ex_percent*updated_by*update_date";
        $add_commaa=0; $data_array_dtls='';
        for($i=1;$i<=$numRowYarn;$i++)
        {
            $lib_yarn_rate_id       ="lib_yarn_rate_id_".$i;
            $lib_rate_data_id       ="lib_rate_data_id_".$i;
            $txt_Yarn_Yarn_Count    ="txt_Yarn_Yarn_Count_".$i;
            $txt_Yarn_composition   ="txt_Yarn_composition_".$i;
            $txt_Yarn_Yarn_Type     ="txt_Yarn_Yarn_Type_".$i;
            $txt_Yarn_Yarn_Dtls     ="txt_Yarn_Yarn_Dtls_".$i;
            $txt_qc_Yarn_Rate       ="txt_qc_Yarn_Rate_".$i;
            $txt_Yarn_Rate          ="txt_Yarn_Rate_".$i;
            $txt_yarn_tot_cons      ="txt_yarn_tot_cons_".$i;
            $txt_Yarn_cost          ="txt_Yarn_cost_".$i;
            $txt_ex_percent_yarn    ="txt_ex_percent_yarn_".$i;
            $yarn_dtls_update_id    ="yarn_dtls_update_id_".$i;
            $dtls_id =str_replace("'",'',$$yarn_dtls_update_id);

            //echo "10**".$pc_date_time;
            $data_array_dtls[$dtls_id]=explode("*",("".$$lib_yarn_rate_id."*".$$lib_rate_data_id."*".$$txt_Yarn_Yarn_Count."*".$$txt_Yarn_Yarn_Type."*".$$txt_Yarn_Yarn_Dtls."*".$$txt_qc_Yarn_Rate."*".$$txt_Yarn_Rate."*''*''*''*".$$txt_Yarn_composition."*''*''*''*''*".$$txt_yarn_tot_cons."*".$$txt_Yarn_cost."*".$$txt_ex_percent_yarn."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
            $hdn_dtls_id_arr[]=$dtls_id;
        }
        // echo "10**".bulk_update_sql_statement( "qc_margin_dtls", "id",$field_array_dtls,$data_array_dtls,$hdn_dtls_id_arr); die;
        //echo "5**$add_commaa insert into qc_margin_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
        for($j=1;$j<=$numRowknit;$j++)
        {
            $lib_knit_Yarn_id       ="lib_knit_Yarn_id_".$j;
            $txt_knit_body_part     ="txt_knit_body_part_".$j;
            $txt_knit_feb_desc      ="txt_knit_feb_desc_".$j;
            $txt_knit_yarn_desc     ="txt_knit_yarn_desc_".$j;
            $txt_qc_knit_Rate       ="txt_qc_knit_Rate_".$j;
            $txt_knit_Rate          ="txt_knit_Rate_".$j;
            $lib_knit_rate_data_id  ="lib_knit_rate_data_id_".$j;
            $txt_knit_tot_cons      ="txt_knit_tot_cons_".$j;
            $txt_knit_cost          ="txt_knit_cost_".$j;
            $knit_dtls_update_id    ="knit_dtls_update_id_".$j;
            
            $dtls_id =str_replace("'",'',$$knit_dtls_update_id);
            $data_array_dtls[$dtls_id]=explode("*",("".$$lib_knit_Yarn_id."*".$$lib_knit_rate_data_id."*''*''*''*".$$txt_qc_knit_Rate."*".$$txt_knit_Rate."*''*''*''*''*".$$txt_knit_body_part."*".$$txt_knit_feb_desc."*".$$txt_knit_yarn_desc."*''*".$$txt_knit_tot_cons."*".$$txt_knit_cost."*''*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
            $hdn_dtls_id_arr[]=$dtls_id;
        }
        
        
        for($k=1;$k<=$numRowDF;$k++)
        {
            $lib_dyeing_finishing_id    ="lib_dyeing_finishing_id_".$k;
            $txt_df_Color_Type          ="txt_df_Color_Type_".$k;
            $txt_df_Color               ="txt_df_Color_".$k;
            $txt_df_process             ="txt_df_process_".$k;
            $txt_qc_df_Rate             ="txt_qc_df_Rate_".$k;
            $txt_df_Rate                ="txt_df_Rate_".$k;
            $lib_df_rate_data_id        ="lib_df_rate_data_id_".$k;
            $txt_df_tot_cons            ="txt_df_tot_cons_".$k;
            $txt_df_cost                ="txt_df_cost_".$k;
            $df_dtls_update_id          ="df_dtls_update_id_".$k;
            
            $dtls_id =str_replace("'",'',$$df_dtls_update_id);
            $data_array_dtls[$dtls_id]=explode("*",("".$$lib_dyeing_finishing_id."*".$$lib_df_rate_data_id."*''*''*''*".$$txt_qc_df_Rate."*".$$txt_df_Rate."*''*".$$txt_df_Color_Type."*".$$txt_df_Color."*''*''*''*''*".$$txt_df_process."*".$$txt_df_tot_cons."*".$$txt_df_cost."*''*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
            $hdn_dtls_id_arr[]=$dtls_id;
        }
        // echo "10**".bulk_update_sql_statement( "qc_margin_dtls", "id",$field_array_dtls,$data_array_dtls,$hdn_dtls_id_arr); die;
        //echo "5**insert into qc_margin_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
         for($m=1;$m<=$numRowAop;$m++)
        {
            $lib_aop_id                 ="lib_aop_id_".$m;
            $txt_aop_Color_Type         ="txt_aop_Color_Type_".$m;
            $txt_aop_Color              ="txt_aop_Color_".$m;
            $txt_aop_process            ="txt_aop_process_".$m;
            $txt_qc_aop_Rate            ="txt_qc_aop_Rate_".$m;
            $txt_aop_Rate               ="txt_aop_Rate_".$m;
            $lib_aop_rate_data_id       ="lib_aop_rate_data_id_".$m;
            $txt_aop_tot_cons           ="txt_aop_tot_cons_".$m;
            $txt_aop_cost               ="txt_aop_cost_".$m;
            $aop_dtls_update_id         ="aop_dtls_update_id_".$m;

            $dtls_id =str_replace("'",'',$$aop_dtls_update_id);
            $data_array_dtls[$dtls_id]=explode("*",("".$$lib_aop_id."*".$$lib_aop_rate_data_id."*''*''*''*".$$txt_qc_aop_Rate."*".$$txt_aop_Rate."*''*".$$txt_aop_Color_Type."*".$$txt_aop_Color."*''*''*''*''*".$$txt_aop_process."*".$$txt_aop_tot_cons."*".$$txt_aop_cost."*''*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
            $hdn_dtls_id_arr[]=$dtls_id;
        }
        // echo "10**".bulk_update_sql_statement( "qc_margin_dtls", "id",$field_array_dtls,$data_array_dtls,$hdn_dtls_id_arr); die;
        $rID=sql_update("qc_margin_mst",$field_array,$data_array,"id",$update_id,1); //die;
        if($data_array_dtls!="")
        {
            //echo "10**".bulk_update_sql_statement( "qc_margin_dtls", "id",$field_array_dtls,$data_array_dtls,$hdn_dtls_id_arr); die;
            $rID2=execute_query(bulk_update_sql_statement( "qc_margin_dtls", "id",$field_array_dtls,$data_array_dtls,$hdn_dtls_id_arr),1);
        }
        
        //echo "10**".$rID . $rID2;die;
        if($db_type==0)
        {
            if($rID && $rID2)
            {
                mysql_query("COMMIT");
                echo "1**".str_replace("'", '', $update_id);
            }
            else
            {
                mysql_query("ROLLBACK");
                echo "6**".str_replace("'", '', $update_id);
            }
        }
        else if($db_type==2 || $db_type==1 )
        {
            if($rID  && $rID2)
            {
                oci_commit($con);
                echo "1**".str_replace("'", '', $update_id);
            }
            else
            {
                oci_rollback($con);
                echo "6**".str_replace("'", '', $update_id);
            }
        }
        disconnect($con);
        die;
    }
    else if ($operation==2)   // Delete Here
    {
        $con = connect();
        if($db_type==0)
        {
            mysql_query("BEGIN");
        }

        $field_array="status_active*is_deleted*updated_by*update_date";
        $data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
        $rID=sql_update("qc_margin_mst",$field_array,$data_array,"id",$update_id,1);
        $rID2=sql_update("qc_margin_dtls",$field_array,$data_array,"mst_id",$update_id,1);

        // echo "10**".$rID . $update_id;die;
        if($db_type==0)
        {
            if($rID && $rID2)
            {
                mysql_query("COMMIT");
                echo "2**".str_replace("'", '', $update_id);
            }
            else
            {
                mysql_query("ROLLBACK");
                echo "6**".str_replace("'", '', $update_id);
            }
        }
        else if($db_type==2 || $db_type==1 )
        {
            if($rID && $rID2)
            {
                oci_commit($con);
                echo "2**".str_replace("'", '', $update_id);
            }
            else
            {
                oci_rollback($con);
                echo "6**".str_replace("'", '', $update_id);
            }
        }
        disconnect($con);
        die;
    }
}

if($action=="costing_popup1")
{
    echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
    extract($_REQUEST);
    ?>
    <script>
        //if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";
        var permission='<?=$permission; ?>';
        function fnc_cost_entry(operation)
        {
			freeze_window(operation);
            //var r=confirm("Press  \"OK\"  to open with zero value\nPress  \"Cancel\"  to open without zero value");
            //alert();
            if (form_validation('txt_fabric','Fabric')==false)
            {
                //alert();
                //,'Fabric*Accessories*AVL Min.*CM DZN*Frieght Cost DZN*Lab - Test DZN*Mis/Offer Qty*Other Cost DZN*Com. DZN*F.O.B DZN*F.O.B PCS-Cost*Margin Per/DZN*Margin %'
				release_freezing();
                return;
            }
            else
            {
                var numRowYarn = $('table#tbl_yarn_cost tbody tr').length;  
                var data_all="";
                for (var i=1; i<=numRowYarn; i++) 
                {
                    data_all+="&txt_Yarn_Yarn_Count_" + i + "='" + $('#txt_Yarn_Yarn_Count_'+i).val()+"'"+"&txt_Yarn_Yarn_Type_" + i + "='" + $('#txt_Yarn_Yarn_Type_'+i).val()+"'"+"&txt_Yarn_composition_" + i + "='" + $('#txt_Yarn_composition_'+i).val()+"'"+"&txt_Yarn_Rate_" + i + "='" + $('#txt_Yarn_Rate_'+i).val()+"'"+"&lib_yarn_rate_id_" + i + "='" + $('#lib_yarn_rate_id_'+i).val()+"'"+"&yarn_dtls_update_id_" + i + "='" + $('#yarn_dtls_update_id_'+i).val()+"'"+"&txt_qc_Yarn_Rate_" + i + "='" + $('#txt_qc_Yarn_Rate_'+i).val()+"'"+"&txt_Yarn_Yarn_Dtls_" + i + "='" + $('#txt_Yarn_Yarn_Dtls_'+i).val()+"'"+"&lib_rate_data_id_" + i + "='" + $('#lib_rate_data_id_'+i).val()+"'"+"&txt_yarn_tot_cons_" + i + "='" + $('#txt_yarn_tot_cons_'+i).val()+"'"+"&txt_Yarn_cost_" + i + "='" + $('#txt_Yarn_cost_'+i).val()+"'"+"&txt_ex_percent_yarn_" + i + "='" + $('#txt_ex_percent_yarn_'+i).val()+"'";
                }
               
                var numRowknit = $('table#tbl_kniting_cost tbody tr').length;
                for (var j=1; j<=numRowknit; j++)
                {
                    data_all+="&txt_knit_body_part_" + j + "='" + $('#txt_knit_body_part_'+j).val()+"'"+"&lib_knit_Yarn_id_" + j + "='" + $('#lib_knit_Yarn_id_'+j).val()+"'"+"&knit_dtls_update_id_" + j + "='" + $('#knit_dtls_update_id_'+j).val()+"'"+"&txt_knit_feb_desc_" + j + "='" + $('#txt_knit_feb_desc_'+j).val()+"'"+"&txt_knit_yarn_desc_" + j + "='" + $('#txt_knit_yarn_desc_'+j).val()+"'"+"&txt_qc_knit_Rate_" + j + "='" + $('#txt_qc_knit_Rate_'+j).val()+"'"+"&txt_knit_Rate_" + j + "='" + $('#txt_knit_Rate_'+j).val()+"'"+"&lib_knit_rate_data_id_" + j + "='" + $('#lib_knit_rate_data_id_'+j).val()+"'"+"&txt_knit_tot_cons_" + j + "='" + $('#txt_knit_tot_cons_'+j).val()+"'"+"&txt_knit_cost_" + j + "='" + $('#txt_knit_cost_'+j).val()+"'";
                }

                var numRowDF = $('table#tbl_df tbody tr').length;
                for (var k=1; k<=numRowDF; k++)
                {
                    data_all+="&txt_df_Color_Type_" + k + "='" + $('#txt_df_Color_Type_'+k).val()+"'"+"&lib_dyeing_finishing_id_" + k + "='" + $('#lib_dyeing_finishing_id_'+k).val()+"'"+"&df_dtls_update_id_" + k + "='" + $('#df_dtls_update_id_'+k).val()+"'"+"&txt_df_Color_" + k + "='" + $('#txt_df_Color_'+k).val()+"'"+"&txt_df_process_" + k + "='" + $('#txt_df_process_'+k).val()+"'"+"&txt_qc_df_Rate_" + k + "='" + $('#txt_qc_df_Rate_'+k).val()+"'"+"&txt_df_Rate_" + k + "='" + $('#txt_df_Rate_'+k).val()+"'"+"&lib_df_rate_data_id_" + k + "='" + $('#lib_df_rate_data_id_'+k).val()+"'"+"&txt_df_tot_cons_" + k + "='" + $('#txt_df_tot_cons_'+k).val()+"'"+"&txt_df_cost_" + k + "='" + $('#txt_df_cost_'+k).val()+"'";
                }

                var numRowAop = $('table#tbl_aop tbody tr').length; 
                for (var m=1; m<=numRowAop; m++) 
                {
                    data_all+="&txt_aop_Color_Type_" + m + "='" + $('#txt_aop_Color_Type_'+m).val()+"'"+"&lib_aop_id_" + m + "='" + $('#lib_aop_id_'+m).val()+"'"+"&aop_dtls_update_id_" + m + "='" + $('#aop_dtls_update_id_'+m).val()+"'"+"&txt_aop_Color_" + m + "='" + $('#txt_aop_Color_'+m).val()+"'"+"&txt_aop_process_" + m + "='" + $('#txt_aop_process_'+m).val()+"'"+"&txt_qc_aop_Rate_" + m + "='" + $('#txt_qc_aop_Rate_'+m).val()+"'"+"&txt_aop_Rate_" + m + "='" + $('#txt_aop_Rate_'+m).val()+"'"+"&lib_aop_rate_data_id_" + m + "='" + $('#lib_aop_rate_data_id_'+m).val()+"'"+"&txt_aop_tot_cons_" + m + "='" + $('#txt_aop_tot_cons_'+m).val()+"'"+"&txt_aop_cost_" + m + "='" + $('#txt_aop_cost_'+m).val()+"'";
                }
                var data="action=save_update_delete&operation="+operation+'&numRowYarn='+numRowYarn+'&numRowknit='+numRowknit+'&numRowDF='+numRowDF+'&numRowAop='+numRowAop+get_submitted_data_string('hid_qc_no*txt_fabric*txt_accessories*txt_avl_min*txt_cm_dzn*txt_frieght_dzn*txt_lab_dzn*txt_mis_offer_qty*txt_other_cost_dzn*txt_commercial_cost_dzn*txt_com_dzn*txt_fob_dzn*txt_fob_pcs*txt_margin_per_dzn*txt_margin*txt_total_yarn_cost*txt_yarn_dyeing_cost*txt_knitting_cost*txt_df_cost*txt_aop_cost*txt_total_cost*update_id*txt_cpm*txt_smv*txt_efficency*txt_cm*txt_available_min*txt_buyer*txt_special_operation',"../../../")+data_all;
               
                //alert(data); return;
                
                http.open("POST","quick_costing_margin_entry_controller.php",true);
                http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                http.send(data);
                http.onreadystatechange = fnc_cost_entry_reponse;
            }
        }

        function fnc_cost_entry_reponse()
        {
            if(http.readyState == 4)
            {
                var reponse=trim(http.responseText).split('**');
                show_msg(trim(reponse[0]));
                if((reponse[0]==0 || reponse[0]==1)){
                    parent.emailwindow.hide();
                    //document.getElementById('update_id').value = reponse[1];
                    //set_button_status(1, permission, 'fnc_cost_entry',1);

                   // $('#cbo_item_category_id').attr('disabled','true');
                }else if(reponse[0]==2){
                    reset_form('quick_cosing_entry','','','','','');
                }
                release_freezing();
            }
        }

        function frm_close()
        {
            parent.emailwindow.hide();
        }

        function reset_for_refresh()
        {
            reset_form('quick_cosing_entry','','','','','update_id*hid_qc_no');
        }

        function calculate_rate(rate_type,type,row,val)
        {
            //1> yarn 2> yd 3>knit 4>df 5>aop

            if(rate_type==1)
            {
                if(type==1)
                {
                    var numRow = $('table#tbl_yarn_cost tbody tr').length;

                    var total_Yarn_Rate=0; var total_actual_Yarn_cost=0; var yarn_tot_cons=0;  
                    //var array_index_chk=0; var rowChk=0; var yarn_cost=0;  var yarn_tot_cons_arr = new Array(); var lib_rate_data_id='';

                    for( var i = 1; i <= numRow; i++ ){

                        total_Yarn_Rate += $('#txt_Yarn_Rate_'+i).val()*1;
                        yarn_tot_cons= $('#txt_yarn_tot_cons_'+i).val()*1;
                        lib_rate_data_id  =$('#lib_rate_data_id_'+i).val()*1;
                         total_actual_Yarn_cost += ($('#txt_yarn_tot_cons_'+i).val()*1)*($('#txt_Yarn_Rate_'+i).val()*1);
                        //yarn_cost  = $('#txt_Yarn_cost_'+i).val()*1;
                        //yarn_tot_cons_arr.push({'id':lib_rate_data_id, 'cons':yarn_tot_cons, 'cost':yarn_cost});
                        /*if(jQuery.inArray(yarn_tot_cons, yarn_tot_cons_arr) !== -1)
                        {
                            yarn_tot_cons_arr[] =yarn_tot_cons;
                            array_index_chk++;
                        }*/
                        //if(array_index_chk)
                    }
                    $('#txt_total_yarn_cost').val(total_actual_Yarn_cost.toFixed(4));
                    /*var id_arr = new Array(); var tot_act_cost=0; var tottal_act_cost=0; var j=1;
                    for(var i = 0; i <= yarn_tot_cons_arr.length; i++) 
                    {
                        var cube = yarn_tot_cons_arr[i];
                        var present_id=cube.id;
                        var cost=cube.cost;
                        var act_cost=cost*1;
                        if(j<=yarn_tot_cons_arr.length)
                        {
                            if(j==yarn_tot_cons_arr.length)
                            {
                                tot_act_cost += act_cost;
                                tottal_act_cost += tot_act_cost;
                                alert(tot_act_cost+'='+i);
                                //tot_act_cost=0;
                            }
                            else
                            {
                                var cube_next = yarn_tot_cons_arr[j];
                                var next_id=cube_next.id;
                                if(present_id==next_id)
                                {
                                    if(jQuery.inArray(present_id, id_arr) !== -1)
                                    {
                                        alert('same id , array te ache'+tot_act_cost);
                                        //alert(tot_act_cost+'='+i);
                                    }
                                    else
                                    {
                                        id_arr.push(present_id);
                                        alert('same id , array te nai'+tot_act_cost);
                                        //alert(tot_act_cost+'='+i);
                                    }
                                    tot_act_cost += act_cost;
                                }
                                else
                                {
                                    if(jQuery.inArray(present_id, id_arr) !== -1)
                                    {
                                        alert('Diff id , array te ache'+tot_act_cost);
                                        //alert(tot_act_cost+'='+i);
                                        //tot_act_cost+=cube.cost;
                                    }
                                    else
                                    {
                                        id_arr.push(present_id);
                                        alert('Diff id , array te nai'+tot_act_cost);
                                        
                                    }
                                    tot_act_cost += act_cost;
                                    tottal_act_cost += tot_act_cost;
                                    tot_act_cost=0;
                                }
                                //alert(tot_act_cost+'='+i+'='+tottal_act_cost);
                                //alert(tot_act_cost+'='+i);
                                j++;
                                
                            }
                        }
                        alert(tottal_act_cost);
                    }*/
                    
                    /*var len= yarn_tot_cons_arr.length;
                    for (let f = 0; f < len; f++) {
                        const key = `key_${f}`;
                        obj = { [key] : chunks[f]};
                        //Parameters.push(obj);
                    }*/
                    //console.log(yarn_tot_cons_arr);
                    //$('#txt_total_yarn_cost').val(total_Yarn_Rate.toFixed(4));
                    
                }else if(type==3){
                    var numRow = $('table#tbl_kniting_cost tbody tr').length;
                    var total_knit_cost=0; var total_actual_knit_cost=0;
                    for( var i = 1; i <= numRow; i++ ){
                        total_knit_cost += $('#txt_knit_Rate_'+i).val()*1;
                        total_actual_knit_cost += $('#txt_knit_cost_'+i).val()*1;
                    }
                    //$('#txt_knitting_cost').val(total_knit_cost.toFixed(4));
                    $('#txt_knitting_cost').val(total_actual_knit_cost.toFixed(4));
                }else if(type==4){
                    var numRow = $('table#tbl_df tbody tr').length;
                    var total_df_cost=0; var total_actual_df_cost=0;
                    for( var i = 1; i <= numRow; i++ ){
                        total_df_cost += $('#txt_df_Rate_'+i).val()*1;
                        total_actual_df_cost += $('#txt_df_cost_'+i).val()*1;
                    }
                    //$('#txt_df_cost').val(total_df_cost.toFixed(4));
                    $('#txt_df_cost').val(total_actual_df_cost.toFixed(4));
                }else if(type==5){
                    var numRow = $('table#tbl_aop tbody tr').length;
                    var total_aop_cost=0; var total_actual_aop_cost=0;
                    for( var i = 1; i <= numRow; i++ ){
                        total_aop_cost += $('#txt_aop_Rate_'+i).val()*1;
                        total_actual_aop_cost += $('#txt_aop_cost_'+i).val()*1;
                    }
                    //$('#txt_aop_cost').val(total_aop_cost.toFixed(4));
                    $('#txt_aop_cost').val(total_actual_aop_cost.toFixed(4));
                }
                calculate_total_rate();
            }
            else
            {
                if(type==1){
					
					var numRow = $('table#tbl_yarn_cost tbody tr').length;

                    var total_Yarn_Rate=0; var total_actual_Yarn_cost=0; var yarn_tot_cons=0;  

                    for( var i = 1; i <= numRow; i++ ){
                        total_Yarn_Rate += $('#txt_Yarn_Rate_'+i).val()*1;
                        yarn_tot_cons= $('#txt_yarn_tot_cons_'+i).val()*1;
                        lib_rate_data_id  =$('#lib_rate_data_id_'+i).val()*1;
                        total_actual_Yarn_cost += ($('#txt_yarn_tot_cons_'+i).val()*1)*($('#txt_Yarn_Rate_'+i).val()*1);
                    }
                    $('#txt_total_yarn_cost').val(total_actual_Yarn_cost.toFixed(4));
                } /*else if(type==3){
                    var knit_Rate_1=$('#txt_knit_Rate_1').val()*1;
                    var knit_Rate_2=$('#txt_knit_Rate_2').val()*1;

                    var total_knit_Rate=knit_Rate_1+knit_Rate_2;
                    //alert(knit_Rate_1+'='+knit_Rate_2+'='+total_knit_Rate);
                    $('#txt_knitting_cost').val( total_knit_Rate );
                } else if(type==4){
                    var df_Rate_1=$('#txt_df_Rate_1').val()*1;
                    var df_Rate_2=$('#txt_df_Rate_2').val()*1;
                    var df_Rate_3=$('#txt_df_Rate_3').val()*1;
                    var df_cost=df_Rate_1+df_Rate_2+df_Rate_3;
                    $('#txt_df_cost').val( df_cost );
                }
                else if(type==5){
                    var aop_Rate_1=$('#txt_aop_Rate_1').val()*1;
                    var aop_Rate_2=$('#txt_aop_Rate_2').val()*1;
                    var aop_Rate_3=$('#txt_aop_Rate_3').val()*1;
                    var aop_cost=aop_Rate_1+aop_Rate_2+aop_Rate_3;
                    $('#txt_aop_cost').val( aop_cost );
                }
                */

                if(type==3){
                    var numRow = $('table#tbl_kniting_cost tbody tr').length;
                    var total_knit_cost=0; var total_actual_knit_cost=0;
                    for( var i = 1; i <= numRow; i++ ){
                        total_knit_cost += $('#txt_knit_Rate_'+i).val()*1;
                        total_actual_knit_cost += ($('#txt_knit_tot_cons_'+i).val()*1)*($('#txt_knit_Rate_'+i).val()*1);
                    }
                    //$('#txt_knitting_cost').val(total_knit_cost.toFixed(4));
                    $('#txt_knitting_cost').val(total_actual_knit_cost.toFixed(4));
                }else if(type==4){
                    var numRow = $('table#tbl_df tbody tr').length;
                    var total_df_cost=0; var total_actual_df_cost=0;
                    for( var i = 1; i <= numRow; i++ ){
                        total_df_cost += $('#txt_df_Rate_'+i).val()*1;
                        total_actual_df_cost += ($('#txt_df_tot_cons_'+i).val()*1)*($('#txt_df_Rate_'+i).val()*1);
                    }
                    //$('#txt_df_cost').val(total_df_cost.toFixed(4));
                    $('#txt_df_cost').val(total_actual_df_cost.toFixed(4));
                }else if(type==5){
                    var numRow = $('table#tbl_aop tbody tr').length;
                    var total_aop_cost=0; var total_actual_aop_cost=0;
                    for( var i = 1; i <= numRow; i++ ){
                        total_aop_cost += $('#txt_aop_Rate_'+i).val()*1;
                        total_actual_aop_cost += ($('#txt_aop_tot_cons_'+i).val()*1)*($('#txt_aop_Rate_'+i).val()*1);
                    }
                    //$('#txt_aop_cost').val(total_aop_cost.toFixed(4));
                    $('#txt_aop_cost').val(total_actual_aop_cost.toFixed(4));
                }
                calculate_total_rate();
            }
        }

        function calculate_total_rate()
        {
            var total_yarn_cost=$('#txt_total_yarn_cost').val()*1;
            var yarn_dyeing_cost=$('#txt_yarn_dyeing_cost').val()*1;
            var knitting_cost=$('#txt_knitting_cost').val()*1;
            var df_cost=$('#txt_df_cost').val()*1;
            var aop_cost=$('#txt_aop_cost').val()*1;
            //var tot_cons=$('#hid_tot_cons').val()*1;
            var total_cost=total_yarn_cost+yarn_dyeing_cost+knitting_cost+df_cost+aop_cost;
            var total_fabric_cost =total_cost;
            $('#txt_total_cost').val( total_cost.toFixed(4) );
            $('#txt_fabric').val( total_fabric_cost.toFixed(4) );
            calculate_marketing_cost();
        }

        function calculate_marketing_cost() 
        {
            var fabric_cost=$('#txt_fabric').val()*1;
            var special_operation=$('#txt_special_operation').val()*1;
            var accessories_cost=$('#txt_accessories').val()*1;
            var cm_dzn=$('#txt_cm_dzn').val()*1;
            var frieght_dzn=$('#txt_frieght_dzn').val()*1;
            var lab_dzn=$('#txt_lab_dzn').val()*1;
            var mis_offer_qty=$('#txt_mis_offer_qty').val()*1;
            var other_cost_dzn=$('#txt_other_cost_dzn').val()*1;
			var other_cost_dzn=$('#txt_commercial_cost_dzn').val()*1;
            var commercial_cost=$('#txt_com_dzn').val()*1;
            var fob_dzn=fabric_cost+special_operation+accessories_cost+cm_dzn+frieght_dzn+lab_dzn+mis_offer_qty+other_cost_dzn+commercial_cost+com_dzn;
            $('#txt_fob_dzn').val( fob_dzn.toFixed(4) );
            
            var fob_pcs =fob_dzn/12;
            //var fob_pcs=$('#txt_fob_pcs').val()*1;
            $('#txt_fob_pcs').val( fob_pcs.toFixed(4) ); 

            var qc_fob_dzn=$('#txt_qc_fob_dzn').val()*1;
            //var margin_dzn=fob_pcs-fob_dzn;
            var margin_dzn=qc_fob_dzn-fob_dzn;
            $('#txt_margin_per_dzn').val( margin_dzn.toFixed(4) );
            
            //var margin_percent =margin_dzn*100;
            var margin_percent = (margin_dzn/fob_dzn)*100;
            $('#txt_margin').val( margin_percent.toFixed(4) );
        }
        
        function calculate_cm_cost()
        {
            var cpm=$('#txt_cpm').val()*1;
            var smv=$('#txt_smv').val()*1;
            var efficency=$('#txt_efficency').val()*1;
            var ex_rate=$('#txt_ex_rate').val()*1;
            var cm=((((cpm*100)/efficency)*smv)*12)/ex_rate;
            $('#txt_cm').val( cm.toFixed(4) );

            //var cm_dzn=((cpm*smv*12)+efficency)/ex_rate; Wrong calculation
            $('#txt_cm_dzn').val( cm.toFixed(4) );

            var offer_qty=$('#txt_offer_qty').val()*1;
            //alert(offer_qty);
            var available_min =(smv*offer_qty)/(efficency/100);
            $('#txt_available_min').val( available_min.toFixed(0) );
            $('#txt_avl_min').val( available_min.toFixed(0) );
            calculate_marketing_cost();
        }

        function fnc_details_popup(row,qc_no,exchange_rate,action)
        {
            //alert(row)
			var popup_width='';
			if(action=="dyeing_finishing_popup" || action=="aop_finishing_popup") popup_width='770px'; else popup_width='700px';
            emailwindow=dhtmlmodal.open('EmailBox', 'iframe','quick_costing_margin_entry_controller.php?qc_no='+qc_no+'&exchange_rate='+exchange_rate+'&action='+action,'Details Popup', 'width='+popup_width+',height=300px,center=1,resize=0','../../');
            emailwindow.onclose=function()
            {
                var popupData=this.contentDoc.getElementById("popupData").value;
                var popupData=popupData.split('_');
                var ex_rate=$('#txt_ex_rate').val()*1;
                //alert(popupData);
                //1> yarn 2> yd 3>knit 4>df 5>aop
                if(action=='yarn_count_popup'){
                    $('#lib_yarn_rate_id_'+row).val(popupData[0]);
                    $('#txt_Yarn_Yarn_Count_'+row).val(popupData[1]);
                    $('#txt_Yarn_Yarn_Type_'+row).val(popupData[2]);
                    //var rate_usd= popupData[3]/ex_rate;
                    //alert(popupData[3]);
                    var rate= popupData[3]*1;
                    var ex_percent_yarn=$('#txt_ex_percent_yarn_'+row).val()*1;
                    var yarn_tot_cons=$('#txt_yarn_tot_cons_'+row).val()*1;
                    var ex_percent_yarn_persent=ex_percent_yarn/100;
                    var actual_cost=rate*ex_percent_yarn_persent*yarn_tot_cons;
                    //var actual_cost=rate*ex_percent_yarn_persent;
                    $('#txt_Yarn_Rate_'+row).val(rate.toFixed(4));
                    $('#txt_Yarn_cost_'+row).val(actual_cost.toFixed(4));
                    //$('#txt_Yarn_Rate_'+row).val(popupData[3].toFixed(4));
                    //alert($('#txt_Yarn_Rate_'+row).val());
                    $('#txt_Yarn_composition_'+row).val(popupData[4]);
                    calculate_rate(1,1,1,rate)
                }else if(action=='kniting_details_popup'){
                    $('#lib_knit_Yarn_id_'+row).val(popupData[0]);
                    $('#txt_knit_body_part_'+row).val(popupData[1]);
                    $('#txt_knit_feb_desc_'+row).val(popupData[2]);
                    $('#txt_knit_yarn_desc_'+row).val(popupData[3]);
                    var rate_usd= popupData[4]/ex_rate;
                    var knit_tot_cons=$('#txt_knit_tot_cons_'+row).val()*1;
                    var actual_cost=rate_usd*knit_tot_cons;
                    $('#txt_knit_Rate_'+row).val(rate_usd.toFixed(4));
                    $('#txt_knit_cost_'+row).val(actual_cost.toFixed(4));
                    calculate_rate(1,3,1,rate_usd)
                }else if(action=='dyeing_finishing_popup'){
                    $('#lib_dyeing_finishing_id_'+row).val(popupData[0]);
                    $('#txt_df_Color_'+row).val(popupData[1]);
                    var rate_usd= popupData[2]/ex_rate;
                    var df_tot_cons=$('#txt_df_tot_cons_'+row).val()*1;
                    var actual_cost=rate_usd*df_tot_cons;
                    $('#txt_df_Rate_'+row).val(rate_usd.toFixed(4));
                    $('#txt_df_cost_'+row).val(actual_cost.toFixed(4));
                    $('#txt_df_process_'+row).val(popupData[3]);
                    $('#txt_df_Color_Type_'+row).val(popupData[4]);
                    calculate_rate(1,4,1,rate_usd)
                }else if(action=='aop_finishing_popup'){
                    $('#lib_aop_id_'+row).val(popupData[0]);
                    $('#txt_aop_Color_'+row).val(popupData[1]);
                    var rate_usd= popupData[2]/ex_rate;
                    var aop_tot_cons=$('#txt_aop_tot_cons_'+row).val()*1;
                    var actual_cost=rate_usd*aop_tot_cons;
                    $('#txt_aop_Rate_'+row).val(rate_usd.toFixed(4));
                    $('#txt_aop_cost_'+row).val(actual_cost.toFixed(4));
                    $('#txt_aop_process_'+row).val(popupData[3]);
                    $('#txt_aop_Color_Type_'+row).val(popupData[4]);
                    calculate_rate(1,5,1,rate_usd)
                }
            }
        }
    </script>
    <body onLoad="set_hotkey();">
    <div align="center" style="width:100%;">
        <?=load_freeze_divs ("../../../",'',1); 

        $sql_cost_summary=sql_select("select  id, mst_id, buyer_agent_id, location_id, no_of_pack, is_confirm, is_cm_calculative, mis_lumsum_cost, commision_per, tot_fab_cost, tot_sp_operation_cost, tot_accessories_cost, tot_cm_cost, tot_fright_cost, tot_lab_test_cost, tot_miscellaneous_cost, tot_other_cost, tot_commission_cost, tot_cost, tot_fob_cost, tot_rmg_ratio from qc_tot_cost_summary where mst_id=$qc_no and status_active=1 and is_deleted=0");
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

                $fob_qc             =$row[csf('tot_cost')];
                $fob_pcs_qc         =$row[csf('tot_fob_cost')];
                //$fob_qc=$fabric_cost_qc+$accessories_cost_qc+$cm_cost_qc+$frieght_cost_qc+$lab_test_cost_qc+$mis_offer_qty_qc+$other_cost_qc+$com_cost_qc;
                //$fob_pcs_qc         =$fob_qc /12;
                //$margin_qc          =$row[csf('tot_fab_cost')];
                //$margin_percent_qc  =$row[csf('tot_fab_cost')];
                //$tot_fob_cost       =$row[csf('tot_fob_cost')];
            }
        }

        $rate_data=''; $tot_cons='';
        $total_qc_yd_cost=$total_qc_yd_cost=$total_qc_knit_cost=$total_qc_df_cost=$total_qc_aop_cost=0;
        
        $sql_cons_rate=sql_select("select  id, rate_data, tot_cons, ex_percent from qc_cons_rate_dtls where mst_id=$qc_no and type=1 and is_calculation = 1 and rate_data is not null and status_active=1 and is_deleted=0 order by id ");
       
	   $yarnIdArr=array(); $yarnQcRate=array(); $knittingIdArr=array(); $knitQcRate=array(); $dyeingIdArr=array(); $dyeingQcRate=array(); $aopIdArr=array(); $aopQcRate=array();
        foreach($sql_cons_rate as $row){
            $tot_cons =$row[csf('tot_cons')];
            $rate_data          =explode('~~',$row[csf('rate_data')]);
			
			if($rate_data[23]!="") 
			{
				$actualCons=0;
				$actualCons=$tot_cons*($rate_data[2]/100);
				$yarnIdArr[$row[csf('id')]][$rate_data[23]]=$rate_data[23];
				$yarnQcRate[$row[csf('id')]][$rate_data[23]]=$rate_data[3].'_'.$actualCons.'_'.$rate_data[2];
			}
			if($rate_data[24]!="") 
			{
				$actualCons=0;
				$actualCons=$tot_cons*($rate_data[6]/100);
				$yarnIdArr[$row[csf('id')]][$rate_data[24]]=$rate_data[24];
				$yarnQcRate[$row[csf('id')]][$rate_data[24]]=$rate_data[7].'_'.$actualCons.'_'.$rate_data[6];
			}
			if($rate_data[25]!="") 
			{
				$actualCons=0;
				$actualCons=$tot_cons*($rate_data[10]/100);
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
            $total_qc_yd_cost   += $rate_data[18]*$tot_cons;
        }
		
		//print_r($knittingIdArr);
		
		
		$companyArr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
		$buyerArr = return_library_array("select id,short_name from lib_buyer ","id","short_name");
		$color_library_arr=return_library_array( "select id,color_name from lib_color where status_active=1 and is_deleted=0", "id", "color_name");
		$lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
        //echo $tot_cons; die;
        $sql_mst="select a.id, a.qc_no, a.company_id, a.location_id, a.cost_sheet_no, a.lib_item_id, a.buyer_id, a.style_des, a.style_ref, a.season_id, a.department_id, a.offer_qty, a.uom, a.delivery_date, a.costing_date, a.inserted_by, a.approved, a.approved_by, a.approved_date, a.revise_no, a.option_id, a.exchange_rate from qc_mst a, qc_confirm_mst b where a.qc_no=$qc_no and a.qc_no=b.cost_sheet_id and (b.job_id is null or b.job_id =0) and a.approved not in(1,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0";
        $sql_mst_res=sql_select($sql_mst);
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
        }

        $sql_qc=sql_select("select id, qc_no, fabric_cost, accessories_cost, avl_min, cm_cost, frieght_cost, lab_test_cost, mis_offer_qty, other_cost, com_cost, fob, fob_pcs, margin, margin_percent, total_yarn_cost, yarn_dyeing_cost, knitting_cost, df_cost, aop_cost, total_cost, buyer, cpm, smv, efficency, cm, available_min, special_operation from qc_margin_mst where qc_no=$qc_no and status_active=1 and is_deleted=0");
       
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
                $update_button_active   =1;
            }
            //$buyer_name = return_field_value("short_name", "lib_buyer", "id='$buyer' and status_active = 1", "short_name");
        } 
        else 
        {
            $update_id=$fabric_cost=$accessories_cost=$avl_min=$cm_cost=$frieght_cost=$lab_test_cost=$mis_offer_qty=$other_cost=$com_cost=$fob=$margin=$margin_percent=$total_yarn_cost=$yarn_dyeing_cost=$knitting_cost=$df_cost=$aop_cost=$total_cost=$cpm=$smv=$efficency=$cm=$available_min='';
            $update_button_active   =0;

            $fabric_cost=$fabric_cost_qc;
            $accessories_cost=$accessories_cost_qc;
            $cm_cost=$cm_cost_qc;
            $frieght_cost=$frieght_cost_qc;
            $lab_test_cost=$lab_test_cost_qc;
            $mis_offer_qty=$mis_offer_qty_qc;
            $other_cost=$other_cost_qc;
            $com_cost=$com_cost_qc;
            $sp_operation_cost=$sp_operation_cost_qc;
            
            $fob=$fabric_cost+$sp_operation_cost+$accessories_cost+$cm_cost+$frieght_cost+$lab_test_cost+$mis_offer_qty+$other_cost+$com_cost;
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
        
        //echo $buyer; die;
        /*$buyer_info=explode('_', $buyer);
        $buyer_name=$buyer_info[0];
        $buyer_id=$buyer_info[1];*/
        
		
        ?>
        <fieldset style="width:835px ">
            <legend><?="Company:$company_name; Cost Sheet No : $cost_sheet_no;  Option: $option_id; Revise No: $revise_no; Style Desc.:$style_des; Style Ref.: $style_ref"; ?></legend>
        
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
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_qc_fabric" id="txt_qc_fabric" value="<? echo $fabric_cost_qc; ?>" style="width:51px"  readonly="readonly" placeholder="Display" ></td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_fabric" id="txt_fabric" value="<? echo $fabric_cost; ?>" style="width:51px" readonly placeholder="Display" ></td>
                            </tr>
                            <tr>
                                <td width="120">Special Operation</td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_qc_special_operation" id="txt_qc_special_operation" value="<? echo $sp_operation_cost_qc; ?>" onKeyUp="calculate_marketing_cost()" style="width:51px" readonly placeholder="Display" ></td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_special_operation" id="txt_special_operation" value="<? echo $sp_operation_cost; ?>" onKeyUp="calculate_marketing_cost()" style="width:51px" ></td>
                            </tr>
                            <tr>
                                <td width="120">Accessories</td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_qc_accessories" id="txt_qc_accessories" value="<? echo $accessories_cost_qc; ?>" onKeyUp="calculate_marketing_cost()" style="width:51px" readonly placeholder="Display" ></td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_accessories" id="txt_accessories" value="<? echo $accessories_cost; ?>" onKeyUp="calculate_marketing_cost()" style="width:51px" ></td>
                            </tr>
                            <tr>
                                <td width="120">CM ($/DZN)</td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_qc_cm_dzn" id="txt_qc_cm_dzn" value="<?=$cm_cost_qc; ?>" style="width:51px"  readonly="readonly" placeholder="Display"/></td>
                                <td width="65" title="(((CPM*100)/Efficiency)*SMV)*12"><input type="text" class="text_boxes_numeric" name="txt_cm_dzn" id="txt_cm_dzn" value="<?=$cm_cost; ?>" readonly placeholder="Display"  style="width:51px" /></td>
                            </tr>
                            <tr>
                                <td width="120">Frieght Cost($/DZN)</td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_qc_frieght_dzn" id="txt_qc_frieght_dzn" value="<? echo $frieght_cost_qc; ?>" onKeyUp="calculate_marketing_cost()" style="width:51px" readonly placeholder="Display" ></td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_frieght_dzn" id="txt_frieght_dzn" value="<? echo $frieght_cost; ?>" onKeyUp="calculate_marketing_cost()" style="width:51px" ></td>
                            </tr>
                            <tr>
                                <td width="120">Lab - Test($/DZN)</td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_qc_lab_dzn" id="txt_qc_lab_dzn" value="<? echo $lab_test_cost_qc; ?>" onKeyUp="calculate_marketing_cost()" style="width:51px" readonly placeholder="Display"></td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_lab_dzn" id="txt_lab_dzn" value="<? echo $lab_test_cost; ?>" onKeyUp="calculate_marketing_cost()" style="width:51px" ></td>
                            </tr>
                            <tr>
                                <td width="120">Mis/Offer Qty.</td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_qc_mis_offer_qty" id="txt_qc_mis_offer_qty" value="<? echo $mis_offer_qty_qc; ?>" onKeyUp="calculate_marketing_cost()" style="width:51px" readonly placeholder="Display" ></td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_mis_offer_qty" id="txt_mis_offer_qty" value="<? echo $mis_offer_qty; ?>" onKeyUp="calculate_marketing_cost()" style="width:51px" ></td>
                            </tr>
                            <tr>
                                <td width="120">Other Cost($/DZN)</td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_qc_other_cost_dzn" id="txt_qc_other_cost_dzn" value="<? echo $other_cost_qc; ?>" onKeyUp="calculate_marketing_cost()" style="width:51px"  readonly="readonly" placeholder="Display"></td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_other_cost_dzn" id="txt_other_cost_dzn" value="<? echo $other_cost; ?>" onKeyUp="calculate_marketing_cost()" style="width:51px" ></td>
                            </tr>
                            <tr>
                                <td width="120">Com.(%)($/DZN)</td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_qc_com_dzn" id="txt_qc_com_dzn" value="<? echo $com_cost_qc; ?>" onKeyUp="calculate_marketing_cost()" style="width:51px"  readonly="readonly" placeholder="Display"></td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_com_dzn" id="txt_com_dzn" value="<? echo $com_cost; ?>" onKeyUp="calculate_marketing_cost()" style="width:51px" ></td>
                            </tr>
                            <tr>
                                <td width="120"><strong>F.O.B($/DZN)</strong></td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_qc_fob_dzn" id="txt_qc_fob_dzn" value="<?=$fob_qc; ?>" style="width:51px" readonly placeholder="Display" ></td>
                                <td width="65" title="fabric_cost+special_operation+accessories_cost+cm_dzn+frieght_dzn+lab_dzn+mis_offer_qty+other_cost_dzn+com_dzn"><input type="text" class="text_boxes_numeric" name="txt_fob_dzn" id="txt_fob_dzn" value="<? echo $fob; ?>"  readonly="readonly" placeholder="Display" style="width:51px" ></td>
                            </tr>
                            <tr>
                                <td width="120">F.O.B($/PCS)</td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_qc_fob_pcs" id="txt_qc_fob_pcs" value="<?=$fob_pcs_qc; ?>" style="width:51px" readonly placeholder="Display" ></td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_fob_pcs" id="txt_fob_pcs" value="<? echo $fob_pcs; ?>"  readonly="readonly"placeholder="Display"  style="width:51px" ></td>
                            </tr>
                            <tr>
                                <td width="120" >Margin Per/DZN</td>
                                <td colspan="2" title="F.O.B($/PCS)-Cost - F.O.B($/DZN)" ><input type="text" class="text_boxes_numeric" name="txt_margin_per_dzn" id="txt_margin_per_dzn" value="<? echo number_format($margin,4); ?>" style="width:117px"  readonly="readonly" placeholder="Display" >
                                <input type="hidden" class="text_boxes_numeric" name="txt_qc_margin_per_dzn" id="txt_qc_margin_per_dzn" value="<? echo $margin; ?>" style="width:51px" readonly placeholder="Display" >
                                </td>
                            </tr>
                            <tr>
                                <td width="120" >Margin %</td>
                                <td colspan="2" title="Margin Per/DZN * 100"><input type="text" class="text_boxes_numeric" name="txt_margin" id="txt_margin" value="<? echo number_format($margin_percent,4); ?>" style="width:117px" readonly placeholder="Display" >
                                <input type="hidden" class="text_boxes_numeric" name="txt_qc_margin" id="txt_qc_margin" value="<? //echo $margin_percent; ?>" style="width:51px" readonly placeholder="Display" ></td>
                            </tr>
                            <tr>
                                <td colspan="3">&nbsp;</td>
                            </tr>
                            <tr>
                                <td width="120">AVL Min.</td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_qc_avl_min" id="txt_qc_avl_min" value="<? echo $avl_min_qc; ?>" onKeyUp="calculate_marketing_cost()" style="width:51px"  readonly="readonly" placeholder="Display"></td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_avl_min" id="txt_avl_min" value="<? echo $avl_min; ?>" onKeyUp="calculate_marketing_cost()" style="width:51px" ></td>
                            </tr>
                        </tbody>
                    </table>
                    <table width="250" cellpadding="2" cellspacing="2" class="rpt_table" border="1" rules="all" align="left">
                    <tr>
                        <thead>
                            <tr>
                                <th colspan="2">CM Cost</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td width="150">Buyer Name</td>
                                <td width="100" align="center"><strong><? echo $buyer_name; ?></strong>
                                    <input type="hidden" class="text_boxes_numeric" name="txt_buyer" id="txt_buyer" value="<? echo $buyer_id; ?>">
                                    <input type="hidden" class="text_boxes" name="txt_ex_rate" id="txt_ex_rate" value="<? echo $ex_rate; ?>">
                                    <input type="hidden" class="text_boxes" name="txt_offer_qty" id="txt_offer_qty" value="<? echo $offer_qty; ?>">
                                </td>
                            </tr>
                            <tr>
                                <td width="150">CPM</td>
                                <td width="100"><input type="text" class="text_boxes_numeric" name="txt_cpm" id="txt_cpm" value="<?=$cpm; ?>" placeholder="Display" onKeyUp="calculate_cm_cost();" readonly ></td>
                            </tr>
                            <tr>
                                <td width="150">SMV</td>
                                <td width="100"><input type="text" class="text_boxes_numeric" name="txt_smv" id="txt_smv" value="<?=$smv; ?>" placeholder="Write" onKeyUp="calculate_cm_cost();" ></td>
                            </tr>
                            <tr>
                                <td width="150">Efficency %</td>
                                <td width="100"><input type="text" class="text_boxes_numeric" name="txt_efficency" id="txt_efficency" value="<?=$efficency; ?>" placeholder="" onKeyUp="calculate_cm_cost();" ></td>
                            </tr>
                            <tr>
                                <td width="150">CM</td>
                                <td width="100" title="((((cpm*100)/efficency)*smv)*12)/ex_rate"><input type="text" class="text_boxes_numeric" name="txt_cm" id="txt_cm" value="<?=$cm; ?>" placeholder="Write/Display" onKeyUp="calculate_cm_cost();"  ></td>
                            </tr>
                            <tr>
                                <td width="150">Available Minutes</td>
                                <td width="100" title="(smv*offer_qty)/(efficency/100)"><input type="text" class="text_boxes_numeric" name="txt_available_min" id="txt_available_min" value="<? echo $available_min; ?>" placeholder="Write/Display" onKeyUp="calculate_cm_cost();"  ></td>
                            </tr>
                        </tbody>
                    </tr>
                </table>
                </div>

                <div style="width: 570px; float: right;">
                    <table width="550" cellpadding="2" cellspacing="2" class="rpt_table" border="1" rules="all" align="left">
                        <thead>
                            <tr>
                                <th colspan="6">Yarn details</th>
                            </tr>       
                            <tr>
                                <th width="100">Yarn Count</th>
                                <th width="100">Yarn Type</th>
                                <th width="100">Composition Name</th>
                                <th width="100">Yarn details</th>
                                <th width="75">QC Rate</th>
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
                                            <td width="100"><input type="text" class="text_boxes" name="txt_Yarn_Yarn_Count_<?=$i; ?>" id="txt_Yarn_Yarn_Count_<?=$i; ?>" value="<?=$row[csf('yarn_count')]; ?>" style="width:86px" onClick="fnc_details_popup('<?=$i;?>','<?=$qc_no;?>','<?=$exchange_rate;?>','yarn_count_popup');" readonly placeholder="Browse" >
                                                <input type="hidden" class="text_boxes" name="lib_yarn_rate_id_<?=$i; ?>" id="lib_yarn_rate_id_<?=$i; ?>" value="<?=$row[csf('lib_table_id')]; ?>" >
                                                <input type="hidden" class="text_boxes" name="lib_rate_data_id_<?=$i; ?>" id="lib_rate_data_id_<?=$i; ?>" value="<?=$row[csf('rate_data_id')]; ?>" >
                                                <input type="hidden" class="text_boxes" name="yarn_dtls_update_id_<?=$i; ?>" id="yarn_dtls_update_id_<?=$i; ?>" value="<?=$row[csf('id')]; ?>" >
                                            </td>
                                            <td width="100"><input type="text" class="text_boxes" name="txt_Yarn_Yarn_Type_<?=$i; ?>" id="txt_Yarn_Yarn_Type_<?=$i; ?>" value="<?=$row[csf('yarn_type')]; ?>" style="width:86px" readonly placeholder="Display"  ></td>
                                            <td width="100"><input type="text" class="text_boxes" name="txt_Yarn_composition_<?=$i; ?>" id="txt_Yarn_composition_<?=$i; ?>" value="<?=$row[csf('composition')]; ?>" style="width:86px" readonly placeholder="Display"  ></td>
                                            <td width="100"><input type="text" class="text_boxes" name="txt_Yarn_Yarn_Dtls_<?=$i; ?>" id="txt_Yarn_Yarn_Dtls_<?=$i; ?>" value="<?=$row[csf('yarn_details')]; ?>" style="width:86px" readonly placeholder="Display"  ></td>
                                            <td><input type="text" class="text_boxes_numeric" name="txt_qc_Yarn_Rate_<?=$i; ?>" id="txt_qc_Yarn_Rate_<?=$i; ?>" value="<?=$row[csf('qc_rate')]; $total_qc_rate +=$row[csf('qc_rate')]*$row[csf('tot_cons')]; ?>"  style="width:61px"  readonly="readonly" placeholder="Display"> </td>
                                            <td><input type="text" class="text_boxes_numeric" name="txt_Yarn_Rate_<?=$i; ?>" id="txt_Yarn_Rate_<?=$i; ?>" value="<?=$row[csf('actual_rate')]; ?>"  onKeyUp="calculate_rate(2,1,<?=$i; ?>,this.value)" style="width:61px"  placeholder="Display/Write"  > 
                                            <input type="hidden" class="text_boxes_numeric" name="txt_yarn_tot_cons_<?=$i; ?>" id="txt_yarn_tot_cons_<?=$i; ?>" value="<?=$row[csf('tot_cons')]; ?>" style="width:61px" >
                                            <input type="hidden" class="text_boxes_numeric" name="txt_ex_percent_yarn_<?=$i; ?>" id="txt_ex_percent_yarn_<?=$i; ?>" value="<?=$row[csf('ex_percent')] ; ?>" style="width:61px" >
                                            <input type="hidden" class="text_boxes_numeric" name="txt_Yarn_cost_<?=$i; ?>" id="txt_Yarn_cost_<?=$i; ?>" value="<?=$row[csf('actual_cost')]; ?>"   style="width:61px" > 
                                            </td>
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
												
												$rate_data_id=$yid; 
												$qcData=explode('_',$yarnQcRate[$rid][$yid]);
												$qcRate=$qcData[0];
												$tot_cons_yarn =$qcData[1];
												$yPer =$qcData[2];
												
												$yarnDtls="";
												
												if($yCount!="") $yarnDtls.=$yCount;
												if($yType!="") $yarnDtls.=', '.$yType;
												if($compo!="") $yarnDtls.=', '.$compo;
												
												?>
												<tr> 
													<td width="100">
														<input type="text" class="text_boxes" name="txt_Yarn_Yarn_Count_<?=$i; ?>" id="txt_Yarn_Yarn_Count_<?=$i; ?>" value="<?=$yCount; ?>" style="width:86px" onClick="fnc_details_popup('<?=$i;?>','<?=$qc_no;?>','<?=$exchange_rate;?>','yarn_count_popup');" readonly placeholder="Browse" >
														<input type="hidden" class="text_boxes" name="lib_yarn_rate_id_<?=$i; ?>" id="lib_yarn_rate_id_<?=$i; ?>"  >
														<input type="hidden" class="text_boxes" name="lib_rate_data_id_<?=$i; ?>" id="lib_rate_data_id_<?=$i; ?>" value="<?=$rate_data_id; ?>" >
														<input type="hidden" class="text_boxes" name="yarn_dtls_update_id_<?=$i; ?>" id="yarn_dtls_update_id_<?=$i; ?>" value="" >
													</td>
													<td width="100"><input type="text" class="text_boxes" name="txt_Yarn_Yarn_Type_<?=$i; ?>" id="txt_Yarn_Yarn_Type_<?=$i; ?>" value="<?=$yType; ?>" style="width:86px" readonly placeholder="Display" ></td>
													<td width="100"><input type="text" class="text_boxes" name="txt_Yarn_composition_<?=$i; ?>" id="txt_Yarn_composition_<?=$i; ?>" value="<?=$compo; ?>" style="width:86px" readonly placeholder="Display"  ></td>
													<td width="100"><input type="text" class="text_boxes" name="txt_Yarn_Yarn_Dtls_<?=$i; ?>" id="txt_Yarn_Yarn_Dtls_<?=$i; ?>" value="<?=$yarnDtls; ?>" style="width:86px" readonly placeholder="Display"  ></td>
													<td ><input type="text" class="text_boxes_numeric" name="txt_qc_Yarn_Rate_<?=$i; ?>" id="txt_qc_Yarn_Rate_<?=$i; ?>" value="<?=$qcRate; $total_qc_rate+=$qcRate*$tot_cons_yarn; ?>" style="width:61px"  readonly="readonly" placeholder="Display"> </td>
													<td ><input type="text" class="text_boxes_numeric" name="txt_Yarn_Rate_<?=$i; ?>" id="txt_Yarn_Rate_<?=$i; ?>" value="<?=$yRate;  ?>"  onKeyUp="calculate_rate(2,1,<?=$i; ?>,this.value)" style="width:61px" placeholder="Display/Write"  >
														<input type="hidden" class="text_boxes_numeric" name="txt_yarn_tot_cons_<?=$i; ?>" id="txt_yarn_tot_cons_<?=$i; ?>" value="<?=$tot_cons_yarn; $total_yarn_cost+=$yRate*$tot_cons_yarn; ?>" style="width:61px" >
														<input type="hidden" class="text_boxes_numeric" name="txt_ex_percent_yarn_<?=$i; ?>" id="txt_ex_percent_yarn_<?=$i; ?>" value="<?=$yPer; ?>" style="width:61px" > 
														<input type="hidden" class="text_boxes_numeric" name="txt_Yarn_cost_<?=$i; ?>" id="txt_Yarn_cost_<?=$i; ?>" value=""   style="width:61px" > 
													</td>
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
												<input type="text" class="text_boxes" name="txt_Yarn_Yarn_Count_1" id="txt_Yarn_Yarn_Count_1" value="" style="width:86px" onClick="fnc_details_popup('1','<?=$qc_no;?>','<?=$exchange_rate;?>','yarn_count_popup');" readonly placeholder="Browse">
												<input type="hidden" class="text_boxes" name="lib_yarn_rate_id_1" id="lib_yarn_rate_id_1"  >
												<input type="hidden" class="text_boxes" name="lib_rate_data_id_1" id="lib_rate_data_id_1" value="" >
												<input type="hidden" class="text_boxes" name="yarn_dtls_update_id_1" id="yarn_dtls_update_id_1" value="" >
											</td>
											<td width="100"><input type="text" class="text_boxes" name="txt_Yarn_Yarn_Type_1" id="txt_Yarn_Yarn_Type_1" value="" style="width:86px" readonly placeholder="Display" ></td>
											<td width="100"><input type="text" class="text_boxes" name="txt_Yarn_composition_1" id="txt_Yarn_composition_1" value="" style="width:86px" readonly placeholder="Display"  ></td>
											<td width="100"><input type="text" class="text_boxes" name="txt_Yarn_Yarn_Dtls_1" id="txt_Yarn_Yarn_Dtls_1" value="" style="width:86px" readonly placeholder="Display"  ></td>
											<td ><input type="text" class="text_boxes_numeric" name="txt_qc_Yarn_Rate_1" id="txt_qc_Yarn_Rate_1" value="" style="width:61px"  readonly="readonly" placeholder="Display"> </td>
											<td ><input type="text" class="text_boxes_numeric" name="txt_Yarn_Rate_1" id="txt_Yarn_Rate_1" value="" onKeyUp="calculate_rate(2,1,1,this.value)" style="width:61px" placeholder="Display/Write"  >
												<input type="hidden" class="text_boxes_numeric" name="txt_yarn_tot_cons_1" id="txt_yarn_tot_cons_1" value="" style="width:61px" >
												<input type="hidden" class="text_boxes_numeric" name="txt_ex_percent_yarn_1" id="txt_ex_percent_yarn_1" value="" style="width:61px" > 
												<input type="hidden" class="text_boxes_numeric" name="txt_Yarn_cost_1" id="txt_Yarn_cost_1" value=""   style="width:61px" > 
											</td>
										</tr>
                                        <?
									}
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <table width="550" cellpadding="2" cellspacing="2" class="rpt_table" border="1" rules="all" align="left">
                        <tfoot>
                            <tr>
                                <td colspan="3"><strong>Total Yarn Cost</strong></td>
                                <td width="74" ><input type="text" class="text_boxes_numeric" name="txt_qc_total_yarn_cost" id="txt_qc_total_yarn_cost" value="<?=$total_qc_rate; ?>" readonly  style="width:61px"  readonly="readonly" placeholder="Display"></td>
                                <td width="74" ><input type="text" class="text_boxes_numeric" name="txt_total_yarn_cost" id="txt_total_yarn_cost" value="<?=$total_yarn_cost; ?>" readonly placeholder="Display"  style="width:61px" ></td>
                                
                            </tr>
                            <tr>
                                <td colspan="3">Yarn Dyeing Cost</td>
                                <td width="74"  ><input type="text" class="text_boxes_numeric" name="txt_qc_yarn_dyeing_cost" id="txt_qc_yarn_dyeing_cost" value="<? echo $total_qc_yd_cost; ?>" style="width:61px" readonly placeholder="Display"> </td>
                                <td width="74"  ><input type="text" class="text_boxes_numeric" name="txt_yarn_dyeing_cost" id="txt_yarn_dyeing_cost" value="<? echo $yarn_dyeing_cost; ?>" onKeyUp="calculate_rate(2,2,1,this.value)" style="width:61px" placeholder="Write" > </td>
                            </tr>
                        </tfoot>
                    </table>
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
                                            <td width="150"><input type="text" class="text_boxes" name="txt_knit_body_part_<? echo $j; ?>" id="txt_knit_body_part_<? echo $j; ?>"  value="<? echo $row[csf('body_part')]; ?>" style="width:136px" onClick="fnc_details_popup(<? echo $j; ?>,'<?=$qc_no;?>','<?=$exchange_rate;?>','kniting_details_popup')"  readonly="readonly" placeholder="Browse"  >
                                                <input type="hidden" class="text_boxes" name="lib_knit_Yarn_id_<? echo $j; ?>" id="lib_knit_Yarn_id_<? echo $j; ?>"  value="<? echo $row[csf('lib_table_id')]; ?>" >
                                                <input type="hidden" class="text_boxes" name="knit_dtls_update_id_<? echo $j; ?>" id="knit_dtls_update_id_<? echo $j; ?>"  value="<? echo $row[csf('id')]; ?>" >
                                            </td>
                                            <td width="150"><input type="text" class="text_boxes" name="txt_knit_feb_desc_<? echo $j; ?>" id="txt_knit_feb_desc_<? echo $j; ?>"  value="<? echo $row[csf('feb_desc')]; ?>" style="width:136px" readonly placeholder="Display" ></td>
                                            <td width="100"><input type="text" class="text_boxes" name="txt_knit_yarn_desc_<? echo $j; ?>" id="txt_knit_yarn_desc_<? echo $j; ?>"  value="<? echo $row[csf('yarn_desc')]; ?>" style="width:86px" readonly placeholder="Display" ></td>
                                            <td ><input type="text" class="text_boxes_numeric" name="txt_qc_knit_Rate_<? echo $j; ?>" id="txt_qc_knit_Rate_<? echo $j; ?>"  value="<? echo $row[csf('qc_rate')]; $total_qc_knit_cost+=$row[csf('tot_cons')]*$row[csf('qc_rate')]; ?>"  style="width:61px"  readonly="readonly" placeholder="Display" ></td>
                                            <td ><input type="text" class="text_boxes_numeric" name="txt_knit_Rate_<? echo $j; ?>" id="txt_knit_Rate_<? echo $j; ?>"  value="<? echo $row[csf('actual_rate')]; ?>" onKeyUp="calculate_rate(2,3,<? echo $j; ?>,this.value)" style="width:61px" placeholder="Display/Write"  >
                                                <input type="hidden" class="text_boxes" name="lib_knit_rate_data_id_<? echo $j; ?>" id="lib_knit_rate_data_id_<? echo $j; ?>" value="<? echo $row[csf('rate_data_id')]; ?>" >
                                                <input type="hidden" class="text_boxes_numeric" name="txt_knit_tot_cons_<? echo $j; ?>" id="txt_knit_tot_cons_<? echo $j; ?>" value="<? echo $row[csf('tot_cons')]; ?>" style="width:61px" >
                                                <input type="hidden" class="text_boxes_numeric" name="txt_knit_cost_<? echo $j; ?>" id="txt_knit_cost_<? echo $j; ?>" value="<? echo $row[csf('actual_cost')]; ?>"   style="width:61px" > 
                                            </td>
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
													<td width="150"><input type="text" class="text_boxes" name="txt_knit_body_part_<?=$j; ?>" id="txt_knit_body_part_<?=$j; ?>" value="<?=$bodyPart; ?>" style="width:136px" onClick="fnc_details_popup(<?=$j; ?>,'<?=$qc_no; ?>','<?=$exchange_rate;?>','kniting_details_popup')"  readonly="readonly" placeholder="Browse"  >
														<input type="hidden" class="text_boxes" name="lib_knit_Yarn_id_<?=$j; ?>" id="lib_knit_Yarn_id_<?=$j; ?>" value="<?=$rate_data_id; ?>" >
														<input type="hidden" class="text_boxes" name="knit_dtls_update_id_<?=$j; ?>" id="knit_dtls_update_id_<?=$j; ?>"  value="<?=$row[csf('id')]; ?>" >
													</td>
													<td width="150"><input type="text" class="text_boxes" name="txt_knit_feb_desc_<?=$j; ?>" id="txt_knit_feb_desc_<?=$j; ?>" value="<?=$const_comp; ?>" style="width:136px" readonly placeholder="Display" ></td>
													<td width="100"><input type="text" class="text_boxes" name="txt_knit_yarn_desc_<?=$j; ?>" id="txt_knit_yarn_desc_<?=$j; ?>" value="<?=$yarn_description; ?>" style="width:86px" readonly placeholder="Display" ></td>
													<td ><input type="text" class="text_boxes_numeric" name="txt_qc_knit_Rate_<?=$j; ?>" id="txt_qc_knit_Rate_<?=$j; ?>"  value="<?=$qcRate; $total_qc_knit_cost+=$tot_cons_knit*$qcRate; ?>" style="width:61px" tr="<?=$tot_cons_knit*$qcRate; ?>" readonly placeholder="Display" ></td>
													<td><input type="text" class="text_boxes_numeric" name="txt_knit_Rate_<?=$j; ?>" id="txt_knit_Rate_<?=$j; ?>"  value="<?=$in_house_rate; ?>" onKeyUp="calculate_rate(2,3,<?=$j; ?>,this.value)" style="width:61px" placeholder="Display/Write"  >
														<input type="hidden" class="text_boxes_numeric" name="txt_knit_tot_cons_<?=$j; ?>" id="txt_knit_tot_cons_<?=$j; ?>" value="<?=$tot_cons_knit; $knitting_cost+=$tot_cons_knit*$in_house_rate; ?>" style="width:61px" >
														<input type="hidden" class="text_boxes" name="lib_knit_rate_data_id_<?=$j; ?>" id="lib_knit_rate_data_id_<?=$j; ?>" value="<?=$rate_data_id; ?>" >
														<input type="hidden" class="text_boxes_numeric" name="txt_knit_cost_<?=$j; ?>" id="txt_knit_cost_<?=$j; ?>" value="" style="width:61px" >
													</td>
												</tr>
											<? $j++;
											}
                                        }
                                    }
									else
									{
										?>
                                        <tr> 
                                            <td width="150"><input type="text" class="text_boxes" name="txt_knit_body_part_1" id="txt_knit_body_part_1" value="" style="width:136px" onClick="fnc_details_popup(1,'<?=$qc_no;?>','<?=$exchange_rate;?>','kniting_details_popup')"  readonly="readonly" placeholder="Browse"  >
                                                <input type="hidden" class="text_boxes" name="lib_knit_Yarn_id_1" id="lib_knit_Yarn_id_1" value="" >
                                                <input type="hidden" class="text_boxes" name="knit_dtls_update_id_1" id="knit_dtls_update_id_1"  value="" >
                                            </td>
                                            <td width="150"><input type="text" class="text_boxes" name="txt_knit_feb_desc_1" id="txt_knit_feb_desc_1" value="" style="width:136px" readonly placeholder="Display" ></td>
                                            <td width="100"><input type="text" class="text_boxes" name="txt_knit_yarn_desc_1" id="txt_knit_yarn_desc_1" value="" style="width:86px" readonly placeholder="Display" ></td>
                                            <td ><input type="text" class="text_boxes_numeric" name="txt_qc_knit_Rate_1" id="txt_qc_knit_Rate_1"  value=""  style="width:61px"  readonly="readonly" placeholder="Display" ></td>
                                            <td><input type="text" class="text_boxes_numeric" name="txt_knit_Rate_1" id="txt_knit_Rate_1"  value="" onKeyUp="calculate_rate(2,3,1,this.value)" style="width:61px" placeholder="Display/Write"  >
                                                <input type="hidden" class="text_boxes_numeric" name="txt_knit_tot_cons_1" id="txt_knit_tot_cons_1" value="" style="width:61px" >
                                                <input type="hidden" class="text_boxes" name="lib_knit_rate_data_id_1" id="lib_knit_rate_data_id_1" value="" >
                                                <input type="hidden" class="text_boxes_numeric" name="txt_knit_cost_1" id="txt_knit_cost_1" value="" style="width:61px">
                                            </td>
                                        </tr>
                                        <?	
									}
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <table width="550" cellpadding="2" cellspacing="2" class="rpt_table" border="1" rules="all" align="left">
                        <tfoot>
                            <tr>
                                <td colspan="3"><strong>Knitting Cost</strong></td>
                                <td width="74" ><input type="text" class="text_boxes_numeric" name="txt_qc_knitting_cost" id="txt_qc_knitting_cost" value="<? echo $total_qc_knit_cost; ?>" readonly style="width:61px"  readonly="readonly" placeholder="Display"></td>
                                <td width="74" ><input type="text" class="text_boxes_numeric" name="txt_knitting_cost" id="txt_knitting_cost" value="<? echo $knitting_cost; ?>" readonly placeholder="Display" style="width:61px" ></td>
                            </tr>
                        </tfoot>
                    </table>
                    
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
                                            <td width="150"><input type="text" class="text_boxes" name="txt_df_Color_Type_<? echo $k; ?>" id="txt_df_Color_Type_<? echo $k; ?>" value="<? echo $row[csf('color_type')]; ?>" style="width:136px" onClick="fnc_details_popup(<? echo $k; ?>,'<?=$qc_no;?>','<?=$exchange_rate;?>','dyeing_finishing_popup')"  readonly="readonly" placeholder="Browse" >
                                                <input type="hidden" class="text_boxes" name="lib_dyeing_finishing_id_<? echo $k; ?>" id="lib_dyeing_finishing_id_<? echo $k; ?>"  value="<? echo $row[csf('lib_table_id')]; ?>" >
                                                <input type="hidden" class="text_boxes" name="df_dtls_update_id_<? echo $k; ?>" id="df_dtls_update_id_<? echo $k; ?>" value="<? echo $row[csf('id')]; ?>" >
                                            </td>
                                            <td width="150"><input type="text" class="text_boxes" name="txt_df_Color_<? echo $k; ?>" id="txt_df_Color_<? echo $k; ?>" value="<? echo $row[csf('color')]; ?>" style="width:136px" readonly placeholder="Display" ></td>
                                            <td width="100"><input type="text" class="text_boxes" name="txt_df_process_<? echo $k; ?>" id="txt_df_process_<? echo $k; ?>" value="<? echo $row[csf('process')]; ?>" style="width:86px" readonly placeholder="Display" ></td>
                                            <td width="75" ><input type="text" class="text_boxes_numeric" name="txt_qc_df_Rate_<? echo $k; ?>" id="txt_qc_df_Rate_<? echo $k; ?>" value="<? echo $row[csf('qc_rate')]; $total_qc_df_cost+=$row[csf('qc_rate')]*$row[csf('tot_cons')];  ?>"  style="width:61px" readonly placeholder="Display" ></td>
                                            <td ><input type="text" class="text_boxes_numeric" name="txt_df_Rate_<? echo $k; ?>" id="txt_df_Rate_<? echo $k; ?>" value="<? echo $row[csf('actual_rate')]; ?>"  onKeyUp="calculate_rate(2,4,<? echo $k; ?>,this.value)" style="width:61px" placeholder="Display/Write" >
                                                <input type="hidden" class="text_boxes" name="lib_df_rate_data_id_<? echo $k; ?>" id="lib_df_rate_data_id_<? echo $k; ?>" value="<? echo $row[csf('rate_data_id')]; ?>" >
                                                <input type="hidden" class="text_boxes_numeric" name="txt_df_tot_cons_<? echo $k; ?>" id="txt_df_tot_cons_<? echo $k; ?>" value="<? echo $row[csf('tot_cons')] ; ?>" style="width:61px" >
                                                <input type="hidden" class="text_boxes_numeric" name="txt_df_cost_<? echo $k; ?>" id="txt_df_cost_<? echo $k; ?>" value="<? echo $row[csf('actual_cost')]; ?>"   style="width:61px" > 
                                            </td>
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
                                                <td width="150"><input type="text" class="text_boxes" name="txt_df_Color_Type_<?=$k; ?>" id="txt_df_Color_Type_<?=$k; ?>" value="<?=$color_range_id; ?>" style="width:136px" onClick="fnc_details_popup(<?=$k; ?>,'<?=$qc_no;?>','<?=$exchange_rate;?>','dyeing_finishing_popup')" readonly placeholder="Browse" >
                                                    <input type="hidden" class="text_boxes" name="lib_dyeing_finishing_id_<? echo $k; ?>" id="lib_dyeing_finishing_id_<? echo $k; ?>"  value="<?=$rate_data_id; ?>" >
                                                    <input type="hidden" class="text_boxes" name="df_dtls_update_id_<? echo $k; ?>" id="df_dtls_update_id_<? echo $k; ?>" value="<? echo $row[csf('id')]; ?>" >
                                                </td>
                                                <td width="150"><input type="text" class="text_boxes" name="txt_df_Color_<? echo $k; ?>" id="txt_df_Color_<? echo $k; ?>" value="<?=$colorName; ?>" style="width:136px" readonly placeholder="Display" ></td>
                                                <td width="100"><input type="text" class="text_boxes" name="txt_df_process_<?=$k; ?>" id="txt_df_process_<?=$k; ?>" value="<?=$process_type_id; ?>" style="width:86px" readonly placeholder="Display" ></td>
                                                <td width="75"><input type="text" class="text_boxes_numeric" name="txt_qc_df_Rate_<? echo $k; ?>" id="txt_qc_df_Rate_<? echo $k; ?>" value="<?=$qcRate; $total_qc_df_cost+=$qcRate*$tot_cons_df; ?>" style="width:61px" readonly placeholder="Display" ></td>
                                                <td ><input type="text" class="text_boxes_numeric" name="txt_df_Rate_<? echo $k; ?>" id="txt_df_Rate_<? echo $k; ?>" value="<?=$in_house_rate; ?>"  onKeyUp="calculate_rate(2,4,<? echo $k; ?>,this.value)" style="width:61px" placeholder="Display/Write" >
                                                    <input type="hidden" class="text_boxes" name="lib_df_rate_data_id_<? echo $k; ?>" id="lib_df_rate_data_id_<? echo $k; ?>" value="<? echo $rate_data_id; ?>" >
                                                    <input type="hidden" class="text_boxes_numeric" name="txt_df_cost_<? echo $k; ?>" id="txt_df_cost_<? echo $k; ?>" value=""   style="width:61px" >
                                                    <input type="hidden" class="text_boxes_numeric" name="txt_df_tot_cons_<? echo $k; ?>" id="txt_df_tot_cons_<? echo $k; ?>" value="<?=$tot_cons_df; $df_cost+=$in_house_rate*$tot_cons_df; ?>" style="width:61px" ></td>
                                            </tr>
                                            <? $k++;
											}
                                        }
                                    }
									else
									{
										?>
                                        <tr>
                                            <td width="150"><input type="text" class="text_boxes" name="txt_df_Color_Type_1" id="txt_df_Color_Type_1" value="" style="width:136px" onClick="fnc_details_popup(1,'<?=$qc_no;?>','<?=$exchange_rate;?>','dyeing_finishing_popup')" readonly placeholder="Browse" >
                                                <input type="hidden" class="text_boxes" name="lib_dyeing_finishing_id_1" id="lib_dyeing_finishing_id_1" value="" >
                                                <input type="hidden" class="text_boxes" name="df_dtls_update_id_1" id="df_dtls_update_id_1" value="" >
                                            </td>
                                            <td width="150"><input type="text" class="text_boxes" name="txt_df_Color_1" id="txt_df_Color_1" value="" style="width:136px" readonly placeholder="Display" ></td>
                                            <td width="100"><input type="text" class="text_boxes" name="txt_df_process_1" id="txt_df_process_1" value="" style="width:86px" readonly placeholder="Display" ></td>
                                            <td width="75" ><input type="text" class="text_boxes_numeric" name="txt_qc_df_Rate_1" id="txt_qc_df_Rate_1" value=""  style="width:61px" readonly placeholder="Display" ></td>
                                            <td ><input type="text" class="text_boxes_numeric" name="txt_df_Rate_1" id="txt_df_Rate_1" value=""  onKeyUp="calculate_rate(2,4,1,this.value)" style="width:61px" placeholder="Display/Write" >
                                                <input type="hidden" class="text_boxes" name="lib_df_rate_data_id_1" id="lib_df_rate_data_id_1" value="" >
                                                <input type="hidden" class="text_boxes_numeric" name="txt_df_cost_1" id="txt_df_cost_1" value=""   style="width:61px" >
                                                <input type="hidden" class="text_boxes_numeric" name="txt_df_tot_cons_1" id="txt_df_tot_cons_1" value="" style="width:61px" ></td>
                                        </tr>
                                        <?
									}
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <table width="550" cellpadding="2" cellspacing="2" class="rpt_table" border="1" rules="all" align="left">
                        <tfoot>
                            <tr>
                                <td colspan="3"><strong>Dyeing Finishing Cost</strong></td>
                                <td width="74" ><input type="text" class="text_boxes_numeric" name="txt_qc_df_cost" id="txt_qc_df_cost" value="<?=$total_qc_df_cost; ?>" readonly style="width:61px"  readonly="readonly" placeholder="Display" ></td>
                                <td width="74"  ><input type="text" class="text_boxes_numeric" name="txt_df_cost" id="txt_df_cost" value="<?=$df_cost; ?>" readonly placeholder="Display" style="width:61px" readonly="readonly" placeholder="Display"  ></td>
                            </tr>
                        </tfoot>
                    </table>
                    
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
                                    $m=1;
                                    foreach($aop_dtls_update as $row){ 
                                        ?>
                                        <tr>
                                            <td width="150"><input type="text" class="text_boxes" name="txt_aop_Color_Type_<? echo $m; ?>" id="txt_aop_Color_Type_<? echo $m; ?>" value="<? echo $row[csf('color_type')]; ?>" style="width:136px" onClick="fnc_details_popup(<? echo $m; ?>,'<?=$qc_no;?>','<?=$exchange_rate;?>','aop_finishing_popup')"  readonly="readonly" placeholder="Browse" >
                                                <input type="hidden" class="text_boxes" name="lib_aop_id_<? echo $m; ?>" id="lib_aop_id_<? echo $m; ?>"  value="<? echo $row[csf('lib_table_id')]; ?>" >
                                                <input type="hidden" class="text_boxes" name="aop_dtls_update_id_<? echo $m; ?>" id="aop_dtls_update_id_<? echo $m; ?>" value="<? echo $row[csf('id')]; ?>" >
                                            </td>
                                            <td width="150"><input type="text" class="text_boxes" name="txt_aop_Color_<? echo $m; ?>" id="txt_aop_Color_<? echo $m; ?>" value="<? echo $row[csf('color')]; ?>" style="width:136px" readonly placeholder="Display" ></td>
                                            <td width="100"><input type="text" class="text_boxes" name="txt_aop_process_<? echo $m; ?>" id="txt_aop_process_<? echo $m; ?>" value="<? echo $row[csf('process')]; ?>" style="width:86px" readonly placeholder="Display" ></td>
                                            <td width="75" ><input type="text" class="text_boxes_numeric" name="txt_qc_aop_Rate_<? echo $m; ?>" id="txt_qc_aop_Rate_<? echo $m; ?>" value="<? echo $row[csf('qc_rate')]; $total_qc_aop_cost+=$row[csf('qc_rate')]*$row[csf('tot_cons')]; ?>"   style="width:61px" readonly placeholder="Display" ></td>
                                            <td ><input type="text" class="text_boxes_numeric" name="txt_aop_Rate_<? echo $m; ?>" id="txt_aop_Rate_<? echo $m; ?>" value="<? echo $row[csf('actual_rate')]; ?>"  onKeyUp="calculate_rate(2,5,<? echo $m; ?>,this.value)" style="width:61px" placeholder="Display/Write" >
                                                <input type="hidden" class="text_boxes_numeric" name="txt_aop_tot_cons_<? echo $m; ?>" id="txt_aop_tot_cons_<? echo $m; ?>" value="<? echo $row[csf('tot_cons')]; ?>" style="width:61px" >
                                                <input type="hidden" class="text_boxes" name="lib_aop_rate_data_id_<? echo $m; ?>" id="lib_aop_rate_data_id_<? echo $m; ?>" value="<? echo $row[csf('rate_data_id')]; ?>" ></td>
                                                <input type="hidden" class="text_boxes_numeric" name="txt_aop_cost_<? echo $m; ?>" id="txt_aop_cost_<? echo $m; ?>" value="<? echo $row[csf('actual_cost')]; ?>"   style="width:61px" > 
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
                                                <td width="150"><input type="text" class="text_boxes" name="txt_aop_Color_Type_<? echo $m; ?>" id="txt_aop_Color_Type_<? echo $m; ?>" value="<?=$color_range_id; ?>" style="width:136px" onClick="fnc_details_popup(<? echo $m; ?>,'<?=$qc_no;?>','<?=$exchange_rate;?>','aop_finishing_popup')"  readonly="readonly" placeholder="Browse" >
                                                    <input type="hidden" class="text_boxes" name="lib_aop_id_<? echo $m; ?>" id="lib_aop_id_<? echo $m; ?>"  value="<? echo $row[csf('lib_table_id')]; ?>" >
                                                    <input type="hidden" class="text_boxes" name="aop_dtls_update_id_<? echo $m; ?>" id="aop_dtls_update_id_<? echo $m; ?>" value="<? echo $row[csf('id')]; ?>" >
                                                </td>
                                                <td width="150"><input type="text" class="text_boxes" name="txt_aop_Color_<? echo $m; ?>" id="txt_aop_Color_<? echo $m; ?>" value="<?=$colorName; ?>" style="width:136px" readonly placeholder="Display" ></td>
                                                <td width="100"><input type="text" class="text_boxes" name="txt_aop_process_<? echo $m; ?>" id="txt_aop_process_<? echo $m; ?>" value="<?=$process_type_id; ?>" style="width:86px" readonly placeholder="Display" ></td>
                                                <td width="75" ><input type="text" class="text_boxes_numeric" name="txt_qc_aop_Rate_<? echo $m; ?>" id="txt_qc_aop_Rate_<? echo $m; ?>" value="<?=$qcRate; $total_qc_aop_cost+=$qcRate*$tot_cons_aop; ?>"   style="width:61px" readonly placeholder="Display" ></td>
                                                <td ><input type="text" class="text_boxes_numeric" name="txt_aop_Rate_<? echo $m; ?>" id="txt_aop_Rate_<? echo $m; ?>" value="<?=$in_house_rate; ?>"  onKeyUp="calculate_rate(2,5,<? echo $m; ?>,this.value)" style="width:61px" placeholder="Display/Write" >
                                                <input type="hidden" class="text_boxes_numeric" name="txt_aop_tot_cons_<? echo $m; ?>" id="txt_aop_tot_cons_<? echo $m; ?>" value="<?=$tot_cons_aop; $aop_cost+=$in_house_rate*$tot_cons_aop; ?>" style="width:61px" >
                                                <input type="hidden" class="text_boxes" name="lib_aop_rate_data_id_<? echo $m; ?>" id="lib_aop_rate_data_id_<? echo $m; ?>" value="<? echo $rate_data_id; ?>" >
                                                <input type="hidden" class="text_boxes_numeric" name="txt_aop_cost_<? echo $m; ?>" id="txt_aop_cost_<? echo $m; ?>" value=""   style="width:61px" > </td>
                                            </tr>
                                            <? $m++;
											}
                                        }
                                    }
									else
									{
										?>
                                        <tr>
                                            <td width="150"><input type="text" class="text_boxes" name="txt_aop_Color_Type_1" id="txt_aop_Color_Type_1" value="" style="width:136px" onClick="fnc_details_popup(1,'<?=$qc_no; ?>','<?=$exchange_rate; ?>','aop_finishing_popup')"  readonly="readonly" placeholder="Browse" >
                                                <input type="hidden" class="text_boxes" name="lib_aop_id_1" id="lib_aop_id_1"  value="" >
                                                <input type="hidden" class="text_boxes" name="aop_dtls_update_id_1" id="aop_dtls_update_id_1" value="" >
                                            </td>
                                            <td width="150"><input type="text" class="text_boxes" name="txt_aop_Color_1" id="txt_aop_Color_1" value="" style="width:136px" readonly placeholder="Display" ></td>
                                            <td width="100"><input type="text" class="text_boxes" name="txt_aop_process_1" id="txt_aop_process_1" value="" style="width:86px" readonly placeholder="Display" ></td>
                                            <td width="75" ><input type="text" class="text_boxes_numeric" name="txt_qc_aop_Rate_1" id="txt_qc_aop_Rate_1" value=""   style="width:61px" readonly placeholder="Display" ></td>
                                            <td ><input type="text" class="text_boxes_numeric" name="txt_aop_Rate_1" id="txt_aop_Rate_1" value=""  onKeyUp="calculate_rate(2,5,1,this.value)" style="width:61px" placeholder="Display/Write" >
                                            <input type="hidden" class="text_boxes_numeric" name="txt_aop_tot_cons_1" id="txt_aop_tot_cons_1" value="" style="width:61px" >
                                            <input type="hidden" class="text_boxes" name="lib_aop_rate_data_id_1" id="lib_aop_rate_data_id_1" value="" >
                                            <input type="hidden" class="text_boxes_numeric" name="txt_aop_cost_1" id="txt_aop_cost_1" value=""   style="width:61px" > </td>
                                        </tr>
                                        <?	
									}
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <table width="550" cellpadding="2" cellspacing="2" class="rpt_table" border="1" rules="all" align="left">
                        <tfoot>
                            <tr>
                                <td colspan="3"><strong>AOP Cost</strong></td>
                                <td width="74"><input type="text" class="text_boxes_numeric" name="txt_qc_aop_cost" id="txt_qc_aop_cost" value="<?=$total_qc_aop_cost; ?>" style="width:61px"  readonly="readonly" placeholder="Display" ></td>
                                <td width="74"><input type="text" class="text_boxes_numeric" name="txt_aop_cost" id="txt_aop_cost" value="<?=$aop_cost; ?>" placeholder="Display" style="width:61px"  ></td>
                            </tr>
                            <tr>
                                <?
                                    $qc_total_cost=$total_qc_aop_cost+$total_qc_df_cost+$total_qc_knit_cost+$total_qc_yd_cost+$total_qc_rate; 
                                    $total_cost=$total_yarn_cost+$yarn_dyeing_cost+$knitting_cost+$df_cost+$aop_cost; 
                                    $total_fab_cost=$total_cost*$tot_cons;
									$fabric_cost_qc=$total_qc_rate+$total_qc_yd_cost+$total_qc_knit_cost+$total_qc_df_cost+$total_qc_aop_cost;
                                ?>
                                <td colspan="3"><strong>Fabric Total Cost</strong></td>
                                <td width="74"><input type="text" class="text_boxes_numeric" name="txt_qc_total_cost" id="txt_qc_total_cost" value="<? echo $fabric_cost_qc; //$qc_total_cost; ?>" readonly style="width:61px"  readonly="readonly" placeholder="Display"></td>
                                <td width="74"><input type="text" class="text_boxes_numeric" name="txt_total_cost" id="txt_total_cost" value="<? echo $total_cost; ?>" readonly placeholder="Display" style="width:61px"  ></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div style="width: 100%">
                <table  style="width: 100%">
                    <tr>
                        <td height="50" valign="middle" align="center" class="button_container">
                            <input type="hidden" class="text_boxes" name="hid_qc_no" id="hid_qc_no" value="<?=$qc_no ?>"> 
                            <input type="hidden" class="text_boxes" name="update_id" id="update_id" value="<?=$update_id ?>">
                            <input type="hidden" class="text_boxes" name="hid_tot_cons" id="hid_tot_cons" value="<?=$tot_cons ?>">
                        <? echo load_submit_buttons( $permission, "fnc_cost_entry", $update_button_active ,0 ,"reset_for_refresh()",1); ?>
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

if ($action=="save_update_delete1")
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));
    //echo "10**".$operation; die;
    if ($operation==0)  // Insert Here
    {
        $con = connect();
        if($db_type==0)
        {
            mysql_query("BEGIN");
        }


        $id=return_next_id( "id", "qc_margin_mst", 1 );
        $dtlsId=return_next_id( "id", "qc_margin_dtls", 1 );
        //$field_array="id,qc_no,fabric_cost,accessories_cost,avl_min,cm_cost,frieght_cost,lab_test_cost,mis_offer_qty,other_cost,com_cost,fob,fob_pcs,margin,margin_percent,Yarn_Yarn_Count_1,Yarn_Yarn_Type_1,Yarn_Rate_1,Yarn_Yarn_Count_2,Yarn_Yarn_Type_2,Yarn_Rate_2,Yarn_Yarn_Count_3,Yarn_Yarn_Type_3,Yarn_Rate_3,total_yarn_cost,yarn_dyeing_cost,knit_Yarn_Count_1,knit_Fabric_Type_1,knit_Rate_1,knit_Yarn_Count_2,knit_Fabric_Type_2,knit_Rate_2,knitting_cost,df_Color_Type_1,df_Color_1,df_Rate_1,df_Color_Type_2,df_Color_2,df_Rate_2,df_Color_Type_3,df_Color_3,df_Rate_3,df_cost,aop_cost,total_cost,inserted_by,insert_date";
        
        $field_array="id,qc_no,fabric_cost,accessories_cost,avl_min,cm_cost,frieght_cost,lab_test_cost,mis_offer_qty,other_cost,com_cost,fob,fob_pcs,margin,margin_percent,total_yarn_cost,yarn_dyeing_cost,knitting_cost,df_cost,aop_cost,total_cost,buyer,cpm,smv,efficency,cm,available_min,special_operation,inserted_by,insert_date";
        
        $field_array_dtls=" id, qc_no, mst_id, type, lib_table_id, rate_data_id, yarn_count, yarn_type, yarn_details, qc_rate, actual_rate, febric_type, color_type, color, composition, body_part, feb_desc, yarn_desc, process, tot_cons, actual_cost, ex_percent, inserted_by, insert_date";

        $data_array="(".$id.",".$hid_qc_no.",".$txt_fabric.",".$txt_accessories.",".$txt_avl_min.",".$txt_cm_dzn.",".$txt_frieght_dzn.",".$txt_lab_dzn.",".$txt_mis_offer_qty.",".$txt_other_cost_dzn.",".$txt_com_dzn.",".$txt_fob_dzn.",".$txt_fob_pcs.",".$txt_margin_per_dzn.",".$txt_margin.",".$txt_total_yarn_cost.",".$txt_yarn_dyeing_cost.",".$txt_knitting_cost.",".$txt_df_cost.",".$txt_aop_cost.",".$txt_total_cost.",".$txt_buyer.",".$txt_cpm.",".$txt_smv.",".$txt_efficency.",".$txt_cm.",".$txt_available_min.",".$txt_special_operation.",".$user_id.",'".$pc_date_time."')";

        $add_commaa=0; $data_array_dtls=''; 
        for($i=1;$i<=$numRowYarn;$i++) 
        {
            $lib_yarn_rate_id       ="lib_yarn_rate_id_".$i;
            $lib_rate_data_id       ="lib_rate_data_id_".$i;
            $txt_Yarn_Yarn_Count    ="txt_Yarn_Yarn_Count_".$i;
            $txt_Yarn_composition   ="txt_Yarn_composition_".$i;
            $txt_Yarn_Yarn_Type     ="txt_Yarn_Yarn_Type_".$i;
            $txt_Yarn_Yarn_Dtls     ="txt_Yarn_Yarn_Dtls_".$i;
            $txt_qc_Yarn_Rate       ="txt_qc_Yarn_Rate_".$i;
            $txt_Yarn_Rate          ="txt_Yarn_Rate_".$i;
            $txt_yarn_tot_cons      ="txt_yarn_tot_cons_".$i;
            $txt_Yarn_cost          ="txt_Yarn_cost_".$i;
            $txt_ex_percent_yarn    ="txt_ex_percent_yarn_".$i;

            if ($add_commaa!=0) $data_array_dtls .=","; $add_commaa=0;
            $data_array_dtls .="(".$dtlsId.",".$hid_qc_no.",".$id.",1,".$$lib_yarn_rate_id.",".$$lib_rate_data_id.",".$$txt_Yarn_Yarn_Count.",".$$txt_Yarn_Yarn_Type.",".$$txt_Yarn_Yarn_Dtls.",".$$txt_qc_Yarn_Rate.",".$$txt_Yarn_Rate.",'','','',".$$txt_Yarn_composition.",'','','','',".$$txt_yarn_tot_cons.",".$$txt_Yarn_cost.",".$$txt_ex_percent_yarn.",'".$user_id."','".$pc_date_time."')";
            $dtlsId++; $add_commaa++;
        }
        //echo "5**$add_commaa insert into qc_margin_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
        for($j=1;$j<=$numRowknit;$j++)
        {
            $lib_knit_Yarn_id       ="lib_knit_Yarn_id_".$j;
            $txt_knit_body_part     ="txt_knit_body_part_".$j;
            $txt_knit_feb_desc      ="txt_knit_feb_desc_".$j;
            $txt_knit_yarn_desc     ="txt_knit_yarn_desc_".$j;
            $txt_qc_knit_Rate       ="txt_qc_knit_Rate_".$j;
            $txt_knit_Rate          ="txt_knit_Rate_".$j;
            $lib_knit_rate_data_id  ="lib_knit_rate_data_id_".$j;
            $txt_knit_tot_cons      ="txt_knit_tot_cons_".$j;
            $txt_knit_cost          ="txt_knit_cost_".$j;
           
            if ($add_commaa!=0) $data_array_dtls .=","; $add_commaa=0;
            $data_array_dtls .="(".$dtlsId.",".$hid_qc_no.",".$id.",2,".$$lib_knit_Yarn_id.",".$$lib_knit_rate_data_id.",'','','',".$$txt_qc_knit_Rate.",".$$txt_knit_Rate.",'','','','',".$$txt_knit_body_part.",".$$txt_knit_feb_desc.",".$$txt_knit_yarn_desc.",'',".$$txt_knit_tot_cons.",".$$txt_knit_cost.",'','".$user_id."','".$pc_date_time."')";
            $dtlsId++; $add_commaa++;
        }
        
        for($k=1;$k<=$numRowDF;$k++)
        {
            $lib_dyeing_finishing_id    ="lib_dyeing_finishing_id_".$k;
            $txt_df_Color_Type          ="txt_df_Color_Type_".$k;
            $txt_df_Color               ="txt_df_Color_".$k;
            $txt_df_process             ="txt_df_process_".$k;
            $txt_qc_df_Rate             ="txt_qc_df_Rate_".$k;
            $txt_df_Rate                ="txt_df_Rate_".$k;
            $lib_df_rate_data_id        ="lib_df_rate_data_id_".$k;
            $txt_df_tot_cons            ="txt_df_tot_cons_".$k;
            $txt_df_cost                ="txt_df_cost_".$k;
            
            if ($add_commaa!=0) $data_array_dtls .=","; $add_commaa=0;
            $data_array_dtls .="(".$dtlsId.",".$hid_qc_no.",".$id.",3,".$$lib_dyeing_finishing_id.",".$$lib_df_rate_data_id.",'','','',".$$txt_qc_df_Rate.",".$$txt_df_Rate.",'',".$$txt_df_Color_Type.",".$$txt_df_Color.",'','','','',".$$txt_df_process.",".$$txt_df_tot_cons.",".$$txt_df_cost.",'','".$user_id."','".$pc_date_time."')";
            $dtlsId++; $add_commaa++;
        }
        //echo "5**insert into qc_margin_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
        for($m=1;$m<=$numRowAop;$m++)
        {
            $lib_aop_id                 ="lib_aop_id_".$m;
            $txt_aop_Color_Type         ="txt_aop_Color_Type_".$m;
            $txt_aop_Color              ="txt_aop_Color_".$m;
            $txt_aop_process            ="txt_aop_process_".$m;
            $txt_qc_aop_Rate            ="txt_qc_aop_Rate_".$m;
            $txt_aop_Rate               ="txt_aop_Rate_".$m;
            $lib_aop_rate_data_id       ="lib_aop_rate_data_id_".$m;
            $txt_aop_tot_cons           ="txt_aop_tot_cons_".$m;
            $txt_aop_cost               ="txt_aop_cost_".$m;

            if ($add_commaa!=0) $data_array_dtls .=","; $add_commaa=0;
            $data_array_dtls .="(".$dtlsId.",".$hid_qc_no.",".$id.",4,".$$lib_aop_id.",".$$lib_aop_rate_data_id.",'','','',".$$txt_qc_df_Rate.",".$$txt_aop_Rate.",'',".$$txt_aop_Color_Type.",".$$txt_aop_Color.",'','','','',".$$txt_aop_process.",".$$txt_aop_tot_cons.",".$$txt_aop_cost.",'','".$user_id."','".$pc_date_time."')";
            $dtlsId++; $add_commaa++;
        }

        //echo "5**insert into qc_margin_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
        if($data_array!=""){
            $rID1=sql_insert("qc_margin_mst",$field_array,$data_array,0);
        }
        if($data_array_dtls!=""){
            $rID2=sql_insert("qc_margin_dtls",$field_array_dtls,$data_array_dtls,0);
        }
        //echo "10**".$rID1 ."&&".  $rID2;die;

        if($db_type==0)
        {
            if($rID1)
            {
                mysql_query("COMMIT");
                echo "0**".str_replace("'", '', $id);
            }
            else
            {
                mysql_query("ROLLBACK");
                echo "6**".str_replace("'", '', $id);
            }
        }
        else if($db_type==2 || $db_type==1 )
        {
            if($rID1 )
            {
                oci_commit($con);
                echo "0**".str_replace("'", '', $id);
            }
            else
            {
                oci_rollback($con);
                echo "6**".str_replace("'", '', $id);
            }
        }
    }
    else if ($operation==1)   // Update Here
    {
        $con = connect();
        if($db_type==0)
        {
            mysql_query("BEGIN");
        }

        $field_array="fabric_cost*accessories_cost*avl_min*cm_cost*frieght_cost*lab_test_cost*mis_offer_qty*other_cost*com_cost*fob*fob_pcs*margin*margin_percent*total_yarn_cost*yarn_dyeing_cost*knitting_cost*df_cost*aop_cost*total_cost*buyer*cpm*smv*efficency*cm*available_min*special_operation*updated_by*update_date";

        $data_array=$txt_fabric."*".$txt_accessories."*".$txt_avl_min."*".$txt_cm_dzn."*".$txt_frieght_dzn."*".$txt_lab_dzn."*".$txt_mis_offer_qty."*".$txt_other_cost_dzn."*".$txt_com_dzn."*".$txt_fob_dzn."*".$txt_fob_pcs."*".$txt_margin_per_dzn."*".$txt_margin."*".$txt_total_yarn_cost."*".$txt_yarn_dyeing_cost."*".$txt_knitting_cost."*".$txt_df_cost."*".$txt_aop_cost."*".$txt_total_cost."*".$txt_buyer."*".$txt_cpm."*".$txt_smv."*".$txt_efficency."*".$txt_cm."*".$txt_available_min."*".$txt_special_operation."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

        $field_array_dtls="lib_table_id*rate_data_id*yarn_count*yarn_type*yarn_details*qc_rate*actual_rate*febric_type*color_type*color*composition*body_part*feb_desc*yarn_desc*process*tot_cons*actual_cost*ex_percent*updated_by*update_date";
        $add_commaa=0; $data_array_dtls='';
        for($i=1;$i<=$numRowYarn;$i++)
        {
            $lib_yarn_rate_id       ="lib_yarn_rate_id_".$i;
            $lib_rate_data_id       ="lib_rate_data_id_".$i;
            $txt_Yarn_Yarn_Count    ="txt_Yarn_Yarn_Count_".$i;
            $txt_Yarn_composition   ="txt_Yarn_composition_".$i;
            $txt_Yarn_Yarn_Type     ="txt_Yarn_Yarn_Type_".$i;
            $txt_Yarn_Yarn_Dtls     ="txt_Yarn_Yarn_Dtls_".$i;
            $txt_qc_Yarn_Rate       ="txt_qc_Yarn_Rate_".$i;
            $txt_Yarn_Rate          ="txt_Yarn_Rate_".$i;
            $txt_yarn_tot_cons      ="txt_yarn_tot_cons_".$i;
            $txt_Yarn_cost          ="txt_Yarn_cost_".$i;
            $txt_ex_percent_yarn    ="txt_ex_percent_yarn_".$i;
            $yarn_dtls_update_id    ="yarn_dtls_update_id_".$i;
            $dtls_id =str_replace("'",'',$$yarn_dtls_update_id);

            //echo "10**".$pc_date_time;
            $data_array_dtls[$dtls_id]=explode("*",("".$$lib_yarn_rate_id."*".$$lib_rate_data_id."*".$$txt_Yarn_Yarn_Count."*".$$txt_Yarn_Yarn_Type."*".$$txt_Yarn_Yarn_Dtls."*".$$txt_qc_Yarn_Rate."*".$$txt_Yarn_Rate."*''*''*''*".$$txt_Yarn_composition."*''*''*''*''*".$$txt_yarn_tot_cons."*".$$txt_Yarn_cost."*".$$txt_ex_percent_yarn."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
            $hdn_dtls_id_arr[]=$dtls_id;
        }
        // echo "10**".bulk_update_sql_statement( "qc_margin_dtls", "id",$field_array_dtls,$data_array_dtls,$hdn_dtls_id_arr); die;
        //echo "5**$add_commaa insert into qc_margin_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
        for($j=1;$j<=$numRowknit;$j++)
        {
            $lib_knit_Yarn_id       ="lib_knit_Yarn_id_".$j;
            $txt_knit_body_part     ="txt_knit_body_part_".$j;
            $txt_knit_feb_desc      ="txt_knit_feb_desc_".$j;
            $txt_knit_yarn_desc     ="txt_knit_yarn_desc_".$j;
            $txt_qc_knit_Rate       ="txt_qc_knit_Rate_".$j;
            $txt_knit_Rate          ="txt_knit_Rate_".$j;
            $lib_knit_rate_data_id  ="lib_knit_rate_data_id_".$j;
            $txt_knit_tot_cons      ="txt_knit_tot_cons_".$j;
            $txt_knit_cost          ="txt_knit_cost_".$j;
            $knit_dtls_update_id    ="knit_dtls_update_id_".$j;
            
            $dtls_id =str_replace("'",'',$$knit_dtls_update_id);
            $data_array_dtls[$dtls_id]=explode("*",("".$$lib_knit_Yarn_id."*".$$lib_knit_rate_data_id."*''*''*''*".$$txt_qc_knit_Rate."*".$$txt_knit_Rate."*''*''*''*''*".$$txt_knit_body_part."*".$$txt_knit_feb_desc."*".$$txt_knit_yarn_desc."*''*".$$txt_knit_tot_cons."*".$$txt_knit_cost."*''*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
                    $hdn_dtls_id_arr[]=$dtls_id;
        }
        
        
        for($k=1;$k<=$numRowDF;$k++)
        {
            $lib_dyeing_finishing_id    ="lib_dyeing_finishing_id_".$k;
            $txt_df_Color_Type          ="txt_df_Color_Type_".$k;
            $txt_df_Color               ="txt_df_Color_".$k;
            $txt_df_process             ="txt_df_process_".$k;
            $txt_qc_df_Rate             ="txt_qc_df_Rate_".$k;
            $txt_df_Rate                ="txt_df_Rate_".$k;
            $lib_df_rate_data_id        ="lib_df_rate_data_id_".$k;
            $txt_df_tot_cons            ="txt_df_tot_cons_".$k;
            $txt_df_cost                ="txt_df_cost_".$k;
            $df_dtls_update_id          ="df_dtls_update_id_".$k;
            
            $dtls_id =str_replace("'",'',$$df_dtls_update_id);
            $data_array_dtls[$dtls_id]=explode("*",("".$$lib_dyeing_finishing_id."*".$$lib_df_rate_data_id."*''*''*''*".$$txt_qc_df_Rate."*".$$txt_df_Rate."*''*".$$txt_df_Color_Type."*".$$txt_df_Color."*''*''*''*''*".$$txt_df_process."*".$$txt_df_tot_cons."*".$$txt_df_cost."*''*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
            $hdn_dtls_id_arr[]=$dtls_id;
        }
        // echo "10**".bulk_update_sql_statement( "qc_margin_dtls", "id",$field_array_dtls,$data_array_dtls,$hdn_dtls_id_arr); die;
        //echo "5**insert into qc_margin_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
         for($m=1;$m<=$numRowAop;$m++)
        {
            $lib_aop_id                 ="lib_aop_id_".$m;
            $txt_aop_Color_Type         ="txt_aop_Color_Type_".$m;
            $txt_aop_Color              ="txt_aop_Color_".$m;
            $txt_aop_process            ="txt_aop_process_".$m;
            $txt_qc_aop_Rate            ="txt_qc_aop_Rate_".$m;
            $txt_aop_Rate               ="txt_aop_Rate_".$m;
            $lib_aop_rate_data_id       ="lib_aop_rate_data_id_".$m;
            $txt_aop_tot_cons           ="txt_aop_tot_cons_".$m;
            $txt_aop_cost               ="txt_aop_cost_".$m;
            $aop_dtls_update_id         ="aop_dtls_update_id_".$m;

            $dtls_id =str_replace("'",'',$$aop_dtls_update_id);
            $data_array_dtls[$dtls_id]=explode("*",("".$$lib_aop_id."*".$$lib_aop_rate_data_id."*''*''*''*".$$txt_qc_aop_Rate."*".$$txt_aop_Rate."*''*".$$txt_aop_Color_Type."*".$$txt_aop_Color."*''*''*''*''*".$$txt_aop_process."*".$$txt_aop_tot_cons."*".$$txt_aop_cost."*''*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"));
                    $hdn_dtls_id_arr[]=$dtls_id;
        }
        // echo "10**".bulk_update_sql_statement( "qc_margin_dtls", "id",$field_array_dtls,$data_array_dtls,$hdn_dtls_id_arr); die;
        $rID=sql_update("qc_margin_mst",$field_array,$data_array,"id",$update_id,1); //die;
        if($data_array_dtls!="")
        {
            //echo "10**".bulk_update_sql_statement( "qc_margin_dtls", "id",$field_array_dtls,$data_array_dtls,$hdn_dtls_id_arr); die;
            $rID2=execute_query(bulk_update_sql_statement( "qc_margin_dtls", "id",$field_array_dtls,$data_array_dtls,$hdn_dtls_id_arr),1);
        }
        
        //echo "10**".$rID . $rID2;die;
        if($db_type==0)
        {
            if($rID && $rID2)
            {
                mysql_query("COMMIT");
                echo "1**".str_replace("'", '', $update_id);
            }
            else
            {
                mysql_query("ROLLBACK");
                echo "6**".str_replace("'", '', $update_id);
            }
        }
        else if($db_type==2 || $db_type==1 )
        {
            if($rID  && $rID2)
            {
                oci_commit($con);
                echo "1**".str_replace("'", '', $update_id);
            }
            else
            {
                oci_rollback($con);
                echo "6**".str_replace("'", '', $update_id);
            }
        }
        disconnect($con);
        die;
    }
    else if ($operation==2)   // Delete Here
    {
        $con = connect();
        if($db_type==0)
        {
            mysql_query("BEGIN");
        }

        $field_array="status_active*is_deleted*updated_by*update_date";
        $data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
        $rID=sql_update("qc_margin_mst",$field_array,$data_array,"id",$update_id,1);
        $rID2=sql_update("qc_margin_dtls",$field_array,$data_array,"mst_id",$update_id,1);

        // echo "10**".$rID . $update_id;die;
        if($db_type==0)
        {
            if($rID && $rID2)
            {
                mysql_query("COMMIT");
                echo "2**".str_replace("'", '', $update_id);
            }
            else
            {
                mysql_query("ROLLBACK");
                echo "6**".str_replace("'", '', $update_id);
            }
        }
        else if($db_type==2 || $db_type==1 )
        {
            if($rID && $rID2)
            {
                oci_commit($con);
                echo "2**".str_replace("'", '', $update_id);
            }
            else
            {
                oci_rollback($con);
                echo "6**".str_replace("'", '', $update_id);
            }
        }
        disconnect($con);
        die;
    }
}

if($action=="yarn_count_popup")
{
    echo load_html_head_contents("Yarn Count Info","../../../", 1, 1, '','1','');
    extract($_REQUEST); 
    ?>
    <script>
        function js_set_value(data)
        {
            //alert(data)
            document.getElementById('popupData').value=data;
            parent.emailwindow.hide();
        }
    </script>
    </head>
    <body>
        <fieldset style="width:660px; margin-left:10px">
            <?
            $lib_sup=return_library_array("select supplier_name,id from lib_supplier", "id", "supplier_name");
            $lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
            $sql="select id,supplier_id,yarn_count,composition,percent,yarn_type,rate,effective_date from lib_yarn_rate where status_active=1 and is_deleted=0 order by id";
            //$arr=array (0=>$lib_sup,1=>$lib_yarn_count,2=>$composition,4=>$yarn_type);
            //echo  create_list_view ( "list_view", "Supplier Name,Yarn Count,Composition,Percent,Type,Rate/KG,Effective Date", "140,80,150,50,60,50","660","350",0, $sql, "js_set_value", "id,yarn_count,yarn_type,rate", "",1, "supplier_id,yarn_count,composition,0,yarn_type,0,0", $arr , "supplier_id,yarn_count,composition,percent,yarn_type,rate,effective_date", "quick_costing_margin_entry_controller",'setFilterGrid("list_view",-1);','0,0,0,1,0,2,3') ;
            $data_array=sql_select($sql);
            ?>
            <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="660" >
                <thead>
                    <th width="30">SL</th>
                    <th width="140">Supplier Name</th>
                    <th width="80">Yarn Count</th>
                    <th width="150">Composition</th>
                    <th width="50">Percent</th>
                    <th width="60">Type</th>
                    <th width="50">Rate/KG</th>
                    <th>Effective Date</th>
                </thead>
            </table>
                <div style="width:660px; max-height:250px; overflow-y:scroll;" >  
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="640" class="rpt_table" id="list_view">
                    <tbody>
                        <? 
                        $i=1;
                        foreach($data_array as $row)
                        {  
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                            ?>
                            <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$lib_yarn_count[$row[csf('yarn_count')]].'_'.$yarn_type[$row[csf('yarn_type')]].'_'.$row[csf('rate')].'_'.$composition[$row[csf('composition')]]; ?>")' style="cursor:pointer" >
                                <td width="30"><? echo $i; ?></td>
                                <td width="140" style="word-break:break-all"><? echo $lib_sup[$row[csf('supplier_id')]]; ?></td>
                                <td width="80" style="word-break:break-all"><? echo $lib_yarn_count[$row[csf('yarn_count')]]; ?></td>
                                <td width="150" style="word-break:break-all"><? echo $composition[$row[csf('composition')]]; ?></td>
                                <td width="50" style="word-break:break-all" align="right"><? echo $row[csf('percent')]; ?></td>
                                <td width="60" style="word-break:break-all"><? echo $yarn_type[$row[csf('yarn_type')]]; ?></td>
                                <td width="50" style="word-break:break-all" align="right"><? echo $row[csf('rate')]; ?></td>
                                <td style="word-break:break-all"><? echo $row[csf('effective_date')]; ?></td>
                            </tr>
                            <? 
                            $i++; 
                        }
                        ?>
                    </tbody>
                </table>
            <input type="hidden" name="popupData" id="popupData" value="" style="width:50px">
        </fieldset>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>setFilterGrid('list_view',-1);</script>
    </html>
    <?
    exit();
}

if($action=="kniting_details_popup")
{
    echo load_html_head_contents("Kniting details Info","../../../", 1, 1, '','1','');
    extract($_REQUEST); 
    ?>
    <script>
        function js_set_value(data)
        {
            //alert(data)
            document.getElementById('popupData').value=data;
            parent.emailwindow.hide();
        }
    </script>
    </head>
    <body>
        <fieldset style="width:660px;margin-left:10px">
            <?
            $buyer_arr=return_library_array("select id, buyer_name from lib_buyer",'id','buyer_name');
            $sql="select id, body_part, const_comp, gsm,gauge, yarn_description, uom_id, status_active, customer_rate, buyer_id, in_house_rate from lib_subcon_charge where is_deleted=0 and rate_type_id=2 and status_active=1 order by id desc";
            $data_array=sql_select($sql);
            ?>
            <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="660" >
                <thead>
                    <th width="30">SL</th>
                    <th width="100">Buyer Name</th>
                    <th width="70">Body Part</th>
                    <th width="100">Construction & Composition</th>
                    <th width="40">GSM</th>
                    <th width="40">Gauge</th>
                    <th width="100">Yarn Description</th>
                    <th width="60">In House Rate</th>
                    <th width="50">UOM</th>
                    <th>Cust. Rate</th>
                </thead>
                </table>
                <div style="width:660px; max-height:250px;overflow-y:scroll;" >  
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="640" class="rpt_table" id="list_view">
                <tbody>
                    <? 
                    $i=1;
                    foreach($data_array as $row)
                    {  
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$body_part[$row[csf('body_part')]].'_'.$row[csf('const_comp')].'_'.$row[csf('yarn_description')].'_'.number_format($row[csf('in_house_rate')]/$exchange_rate,4); ?>")' style="cursor:pointer" >
                            <td width="30"><? echo $i; ?></td>
                            <td width="100" style="word-break:break-all"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
                            <td width="70" style="word-break:break-all"><? echo $body_part[$row[csf('body_part')]]; ?></td>
                            <td width="100" style="word-break:break-all"><? echo $row[csf('const_comp')]; ?></td>
                            <td width="40" style="word-break:break-all"><? echo $row[csf('gsm')]; ?></td>
                            <td width="40" style="word-break:break-all"><? echo $row[csf('gauge')]; ?></td>
                            <td width="100" style="word-break:break-all"><? echo $row[csf('yarn_description')]; ?></td>
                            <td align="right" width="60" style="word-break:break-all"><? echo number_format($row[csf('in_house_rate')]/$exchange_rate,4); ?></td>
                            <td width="50" style="word-break:break-all" align="center"><? echo $unit_of_measurement[$row[csf('uom_id')]]; ?></td>
                            <td align="right" style="word-break:break-all"><? echo $row[csf('customer_rate')]; ?></td>
                            
                        </tr>
                        <? 
                        $i++; 
                    }
                    ?>
                </tbody>
            </table>
            <input type="hidden" name="popupData" id="popupData" value="" style="width:50px">
        </fieldset>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>setFilterGrid('list_view',-1);</script>
    </html>
    <?
    exit();
}

if($action=="dyeing_finishing_popup")
{
    echo load_html_head_contents("Dyeing Finishing Details Info","../../../", 1, 1, '','1','');
    extract($_REQUEST); 
    ?>
    <script>
        function js_set_value(data)
        {
            //alert(data)
            document.getElementById('popupData').value=data;
            parent.emailwindow.hide();
        }
    </script>
    </head>
    <body>
        <fieldset style="width:730px;margin-left:10px">
            <?
            $buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
            $company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
            $color_library_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"); 
            $sql="select id, comapny_id, const_comp, process_type_id, process_id, color_id, width_dia_id, in_house_rate, uom_id, rate_type_id ,customer_rate, buyer_id, status_active, color_range_id from lib_subcon_charge where status_active=1 and is_deleted=0 and rate_type_id in (3,4,7,6)";
            $data_array=sql_select($sql);
            ?>
            <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="730" >
                <thead>
                    <th width="30">SL</th>
                    <th width="40">Com.</th>
                    <th width="100">Const. Compo.</th>
                    <th width="70">Color Range</th>
                    <th width="70">Process Type</th>
                    <th width="70">Process Name</th>
                    <th width="50">Color</th>
                    <th width="40">Width / Dia type</th>
                    <th width="40">In House Rate</th>
                    <th width="40">UOM</th>
                    <th width="50">Rate type</th>
                    <th width="40">Cust. Rate</th>
                    <th>Buyer</th>
                </thead>
                </table>
                <div style="width:730px; max-height:250px;overflow-y:scroll;" >  
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="710" class="rpt_table" id="list_view">
                <tbody>
                    <? 
                    $i=1;
                    foreach($data_array as $row)
                    {  
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$color_library_arr[$row[csf('color_id')]].'_'.number_format($row[csf('in_house_rate')]/$exchange_rate,4).'_'.$process_type[$row[csf('process_type_id')]].'_'.$color_range[$row[csf('color_range_id')]]; ?>")' style="cursor:pointer" >
                            <td width="30"><? echo $i; ?></td>
                            <td width="40" style="word-break:break-all"><? echo $company_arr[$row[csf('comapny_id')]]; ?></td> 
                            <td width="100" style="word-break:break-all"><? echo $row[csf('const_comp')]; ?></td>
                            <td width="70" style="word-break:break-all"><?=$color_range[$row[csf('color_range_id')]]; ?></td>
                            <td width="70" style="word-break:break-all"><? echo $process_type[$row[csf('process_type_id')]]; ?></td>
                            <td width="70" style="word-break:break-all"><? echo $conversion_cost_head_array[$row[csf('process_id')]]; ?></td>
                            <td width="50" style="word-break:break-all"><? echo $color_library_arr[$row[csf('color_id')]]; ?></td>
                            <td width="40" style="word-break:break-all"><? echo $fabric_typee[$row[csf('width_dia_id')]]; ?></td>
                            <td width="40" style="word-break:break-all" align="right"><? echo number_format($row[csf('in_house_rate')]/$exchange_rate,4); ?></td>
                            <td width="40" style="word-break:break-all" align="center"><? echo $unit_of_measurement[$row[csf('uom_id')]]; ?></td>
                            <td width="50" style="word-break:break-all"><? echo $production_process[$row[csf('rate_type_id')]]; ?></td>
                            <td width="40" style="word-break:break-all" align="right"><? echo $row[csf('customer_rate')]; ?></td>
                            <td style="word-break:break-all"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
                        </tr>
                        <? 
                        $i++; 
                    }
                    ?>
                </tbody>
            </table>
            <input type="hidden" name="popupData" id="popupData" value="" style="width:50px">
        </fieldset>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>setFilterGrid('list_view',-1);</script>
    </html>
    <?
    exit();
}

if($action=="aop_finishing_popup")
{
    echo load_html_head_contents("Dyeing Finishing Details Info","../../../", 1, 1, '','1','');
    extract($_REQUEST); 
    ?>
    <script>
        function js_set_value(data)
        {
            //alert(data)
            document.getElementById('popupData').value=data;
            parent.emailwindow.hide();
        }
    </script>
    </head>
    <body>
        <fieldset style="width:660px;margin-left:10px">
            <?
            $buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
            $company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
            $color_library_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"); 
            $sql="select id, comapny_id, const_comp, process_type_id, process_id, color_id, width_dia_id, in_house_rate, uom_id, rate_type_id ,customer_rate, buyer_id, status_active,color_range_id from lib_subcon_charge where status_active=1 and is_deleted=0 and rate_type_id in (3,4,7,6) and process_id=35";
            $data_array=sql_select($sql);
            ?>
            <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="730" >
                <thead>
                    <th width="30">SL</th>
                    <th width="40">Com.</th>
                    <th width="100">Const. Compo.</th>
                    <th width="70">Color Range</th>
                    <th width="70">Process Type</th>
                    <th width="70">Process Name</th>
                    <th width="50">Color</th>
                    <th width="40">Width / Dia type</th>
                    <th width="40">In House Rate</th>
                    <th width="40">UOM</th>
                    <th width="50">Rate type</th>
                    <th width="40">Cust. Rate</th>
                    <th>Buyer</th>
                </thead>
                </table>
                <div style="width:730px; max-height:250px;overflow-y:scroll;" >  
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="710" class="rpt_table" id="list_view">
                <tbody>
                    <? 
                    $i=1;
                    foreach($data_array as $row)
                    {  
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$color_library_arr[$row[csf('color_id')]].'_'.number_format($row[csf('in_house_rate')]/$exchange_rate,4).'_'.$process_type[$row[csf('process_type_id')]].'_'.$color_range[$row[csf('color_range_id')]]; ?>")' style="cursor:pointer" >
                            <td width="30"><? echo $i; ?></td>
                            <td width="40" style="word-break:break-all"><? echo $company_arr[$row[csf('comapny_id')]]; ?></td>
                            <td width="100" style="word-break:break-all"><? echo $row[csf('const_comp')]; ?></td>
                            <td width="70" style="word-break:break-all"><?=$color_range[$row[csf('color_range_id')]]; ?></td>
                            <td width="70" style="word-break:break-all"><? echo $process_type[$row[csf('process_type_id')]]; ?></td>
                            <td width="70" style="word-break:break-all"><? echo $conversion_cost_head_array[$row[csf('process_id')]]; ?></td>
                            <td width="50" style="word-break:break-all"><? echo $color_library_arr[$row[csf('color_id')]]; ?></td>
                            <td width="40" style="word-break:break-all"><? echo $fabric_typee[$row[csf('width_dia_id')]]; ?></td>
                            <td width="40" style="word-break:break-all" align="right"><? echo number_format($row[csf('in_house_rate')]/$exchange_rate,4); ?></td>
                            <td width="40" style="word-break:break-all" align="center"><? echo $unit_of_measurement[$row[csf('uom_id')]]; ?></td>
                            <td width="50" style="word-break:break-all"><? echo $production_process[$row[csf('rate_type_id')]]; ?></td>
                            <td width="40" style="word-break:break-all" align="right"><? echo $row[csf('customer_rate')]; ?></td>
                            <td style="word-break:break-all"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
                        </tr>
                        <? 
                        $i++; 
                    }
                    ?>
                </tbody>
            </table>
            <input type="hidden" name="popupData" id="popupData" value="" style="width:50px">
        </fieldset>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>setFilterGrid('list_view',-1);</script>
    </html>
    <?
    exit();
}
?>
