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
        
    function fnc_costing_details(qc_no,buyer,costing_date,action)
    {
        
        emailwindow=dhtmlmodal.open('EmailBox', 'iframe','requires/quick_costing_margin_entry_controller.php?qc_no='+qc_no+'&buyer='+buyer+'&costing_date='+costing_date+'&action='+action,'Costing Popup', 'width=745px,height=600px,center=1,resize=0','../');
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
        <div style="width:1050px; max-height:300px; overflow-y:scroll" id="scroll_body">
        
        <? if($type==1)
    	{ ?>
            <table width="100%" cellspacing="0" border="1" class="rpt_table" rules="all" align="center">
                <thead>
                    <tr style="font-size:13px">
                        <th width="30">SL.</th> 
                        <th width="80">Cost Sheet No</th>
                        <th width="30">Option</th>
                        <th width="70">Revise No</th>
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

                $sql_mst="select a.id, a.qc_no, a.cost_sheet_no, a.lib_item_id, a.buyer_id, a.style_des, a.style_ref, a.season_id, a.department_id, a.offer_qty, a.uom, a.delivery_date, a.costing_date, a.inserted_by, a.approved, a.approved_by, a.approved_date, a.revise_no, a.option_id from qc_mst a, qc_confirm_mst b where a.qc_no=b.cost_sheet_id and (b.job_id is null or b.job_id =0) and a.approved not in(1,3) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $buyerCond $seasonCond $deptCond $styleCond $costSheetNoCond $dateCond  group by a.id, a.qc_no, a.cost_sheet_no, a.lib_item_id, a.buyer_id, a.style_des, a.style_ref, a.season_id, a.department_id, a.offer_qty, a.uom, a.delivery_date, a.costing_date, a.inserted_by, a.approved, a.approved_by, a.approved_date, a.revise_no, a.option_id order by a.cost_sheet_no asc";// and a.revise_no=0 and a.option_id=0 
                    $sql_result=sql_select($sql_mst);
                    //echo $sql_mst;
                foreach($sql_result as $row)
                {
                    $rowData_arr[$row[csf('cost_sheet_no')]][$row[csf('qc_no')]][$row[csf('revise_no')]][$row[csf('option_id')]]['all']=$row[csf('cost_sheet_no')].'***'.$row[csf('costing_date')].'***'.$row[csf('buyer_id')].'***'.$row[csf('style_ref')].'***'.$row[csf('style_des')].'***'.$row[csf('lib_item_id')].'***'.$row[csf('offer_qty')].'***'.$row[csf('delivery_date')].'***'.$row[csf('department_id')].'***'.$row[csf('uom')].'***'.$row[csf('approved')].'***'.$row[csf('inserted_by')].'***'.$row[csf('season_id')].'***'.$row[csf('department_id')].'***1'.'***'.$row[csf('approved_by')].'***'.$row[csf('approved_date')];
                }
                unset($sql_result);

                /*if($costingstage_id==0 || $costingstage_id==1)
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
                                $confirmidCond=" and a.qc_no not in(".implode(",",$value).")"; 
                            }
                            else
                            {
                                $confirmidCond.=" or a.qc_no not in(".implode(",",$value).")";
                            }
                            $ji++;
                        }
                    }
                    $sql_mst="select a.id, a.qc_no, a.cost_sheet_no, a.costing_date, a.buyer_id, a.style_ref, a.style_des, a.lib_item_id, a.offer_qty, a.delivery_date, a.season_id, a.department_id, a.uom, a.inserted_by, a.season_id, a.department_id, a.approved, a.approved_by, a.approved_date, a.revise_no, a.option_id, c.buyer_agent_id from qc_mst a, qc_tot_cost_summary c where a.qc_no=c.mst_id and a.status_active=1 and a.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $buyerCond $seasonCond $deptCond $styleCond $costSheetNoCond $date_cond $confirmidCond order by a.cost_sheet_no asc";
                    //echo $sql_mst;
                    $sql_result=sql_select($sql_mst);
                    foreach($sql_result as $row)
                    {
                        $rowData_arr[$row[csf('cost_sheet_no')]][$row[csf('qc_no')]][$row[csf('revise_no')]][$row[csf('option_id')]]['all']=$row[csf('cost_sheet_no')].'***'.$row[csf('costing_date')].'***'.$row[csf('buyer_id')].'***'.$row[csf('style_ref')].'***'.$row[csf('style_des')].'***'.$row[csf('lib_item_id')].'***'.$row[csf('offer_qty')].'***'.$row[csf('delivery_date')].'***'.$row[csf('department_id')].'***'.$row[csf('uom')].'***'.$row[csf('approved')].'***'.$row[csf('inserted_by')].'***'.$row[csf('season_id')].'***'.$row[csf('department_id')].'***2';
                    }
                    unset($sql_result);
                }*/
                
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
                                    <!--  SL. Cost Sheet No   Buyer   Style Desc. Style Ref.  Offer Qty   UOM FOB Price   Delivery Date   Costing Date    Insert By -->
    								<td width="30"><?=$i; ?></td>
                                    <td width="80"  title="<?=$qc_no; ?>"><a href="##" onclick="fnc_costing_details('<? echo $qc_no;?>','<? echo $buyerArr[$buyer_id]."_".$buyer_id;?>','<? echo $costing_date;?>','costing_popup')"><p><?=$cost_sheet_no; ?></p></a></td>
                                    <td width="30" style="word-break:break-all"><?=$option_id; ?></td>
                                    <td width="70" style="word-break:break-all"><?=$revise_no; ?></td>
    								<td width="100" style="word-break:break-all"><?=$buyerArr[$buyer_id]; ?></td>
    								<td width="100" style="word-break:break-all"><?=$style_des; ?></td>
    								<td width="100" style="word-break:break-all"><?=$style_ref; ?></td>
    								<td width="80" align="right"><?=number_format($offer_qty,0); ?></td>
    								<td width="60"><?=$itemUom;//$unit_of_measurement[$row[csf("uom")]]; ?></td>
    								<td width="70" align="right"><?=number_format($fobPrice,2); ?></td>
    								<td width="70"><?=change_date_format($delivery_date); ?></td>
    								<td width="70"><?=change_date_format($costing_date); ?></td>
    								<td width="80" style="word-break:break-all"><?=$user_arr[$inserted_by]; ?></td>
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



if($action=="costing_popup")
{
    echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
    extract($_REQUEST);
    ?>
    <script>
        //if( $('#index_page', window.parent.document).val()!=1) window.location.href = "../../../logout.php";
        var permission='<? echo $permission; ?>';
        function fnc_cost_entry(operation)
        {
            //alert();
            if (form_validation('txt_fabric','Fabric')==false)
            {
                //alert();
                //,'Fabric*Accessories*AVL Min.*CM DZN*Frieght Cost DZN*Lab - Test DZN*Mis/Offer Qty*Other Cost DZN*Com. DZN*F.O.B DZN*F.O.B PCS-Cost*Margin Per/DZN*Margin %'
                return;
            }
            else
            {
                var numRowYarn = $('table#tbl_yarn_cost tbody tr').length;
                var data_all="";
                for (var i=1; i<=numRowYarn; i++)
                {
                    data_all+="&txt_Yarn_Yarn_Count_" + i + "='" + $('#txt_Yarn_Yarn_Count_'+i).val()+"'"+"&txt_Yarn_Yarn_Type_" + i + "='" + $('#txt_Yarn_Yarn_Type_'+i).val()+"'"+"&txt_Yarn_Rate_" + i + "='" + $('#txt_Yarn_Rate_'+i).val()+"'"+"&lib_yarn_rate_id_" + i + "='" + $('#lib_yarn_rate_id_'+i).val()+"'"+"&yarn_dtls_update_id_" + i + "='" + $('#yarn_dtls_update_id_'+i).val()+"'"+"&txt_qc_Yarn_Rate_" + i + "='" + $('#txt_qc_Yarn_Rate_'+i).val()+"'"+"&txt_Yarn_Yarn_Dtls_" + i + "='" + $('#txt_Yarn_Yarn_Dtls_'+i).val()+"'"+"&lib_rate_data_id_" + i + "='" + $('#lib_rate_data_id_'+i).val()+"'";
                }
               
                var numRowknit = $('table#tbl_kniting_cost tbody tr').length;
                for (var j=1; j<=numRowknit; j++)
                {
                    data_all+="&txt_knit_Yarn_Count_" + j + "='" + $('#txt_knit_Yarn_Count_'+j).val()+"'"+"&lib_knit_Yarn_id_" + j + "='" + $('#lib_knit_Yarn_id_'+j).val()+"'"+"&knit_dtls_update_id_" + j + "='" + $('#knit_dtls_update_id_'+j).val()+"'"+"&txt_knit_Fabric_Type_" + j + "='" + $('#txt_knit_Fabric_Type_'+j).val()+"'"+"&txt_qc_knit_Rate_" + j + "='" + $('#txt_qc_knit_Rate_'+j).val()+"'"+"&txt_knit_Rate_" + j + "='" + $('#txt_knit_Rate_'+j).val()+"'";
                }

                var numRowDF = $('table#tbl_df tbody tr').length;
                for (var k=1; k<=numRowDF; k++)
                {
                    data_all+="&txt_df_Color_Type_" + k + "='" + $('#txt_df_Color_Type_'+k).val()+"'"+"&lib_dyeing_finishing_id_" + k + "='" + $('#lib_dyeing_finishing_id_'+k).val()+"'"+"&df_dtls_update_id_" + k + "='" + $('#df_dtls_update_id_'+k).val()+"'"+"&txt_df_Color_" + k + "='" + $('#txt_df_Color_'+k).val()+"'"+"&txt_qc_df_Rate_" + k + "='" + $('#txt_qc_df_Rate_'+k).val()+"'"+"&txt_df_Rate_" + k + "='" + $('#txt_df_Rate_'+k).val()+"'";
                }
                var data="action=save_update_delete&operation="+operation+'&numRowYarn='+numRowYarn+'&numRowknit='+numRowknit+'&numRowDF='+numRowDF+get_submitted_data_string('hid_qc_no*txt_fabric*txt_accessories*txt_avl_min*txt_cm_dzn*txt_frieght_dzn*txt_lab_dzn*txt_mis_offer_qty*txt_other_cost_dzn*txt_com_dzn*txt_fob_dzn*txt_fob_pcs*txt_margin_per_dzn*txt_margin*txt_total_yarn_cost*txt_yarn_dyeing_cost*txt_knitting_cost*txt_df_cost*txt_aop_cost*txt_total_cost*update_id',"../../../")+data_all;
               // var data="action=save_update_delete&operation="+operation+get_submitted_data_string('hid_qc_no*txt_fabric*txt_accessories*txt_avl_min*txt_cm_dzn*txt_frieght_dzn*txt_lab_dzn*txt_mis_offer_qty*txt_other_cost_dzn*txt_com_dzn*txt_fob_dzn*txt_fob_pcs*txt_margin_per_dzn*txt_margin*txt_Yarn_Yarn_Count_1*txt_Yarn_Yarn_Type_1*txt_Yarn_Rate_1*txt_Yarn_Yarn_Count_2*txt_Yarn_Yarn_Type_2*txt_Yarn_Rate_2*txt_Yarn_Yarn_Count_3*txt_Yarn_Yarn_Type_3*txt_Yarn_Rate_3*txt_total_yarn_cost*txt_yarn_dyeing_cost*txt_knit_Yarn_Count_1*txt_knit_Fabric_Type_1*txt_knit_Rate_1*txt_knit_Yarn_Count_2*txt_knit_Fabric_Type_2*txt_knit_Rate_2*txt_knitting_cost*txt_df_Color_Type_1*txt_df_Color_1*txt_df_Rate_1*txt_df_Color_Type_2*txt_df_Color_2*txt_df_Rate_2*txt_df_Color_Type_3*txt_df_Color_3*txt_df_Rate_3*txt_df_cost*txt_aop_cost*txt_total_cost',"../../../");
               
                alert(data);
                freeze_window(operation);
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
                    document.getElementById('update_id').value = reponse[1];
                    set_button_status(1, permission, 'fnc_cost_entry',1);
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
            if(rate_type==1)
            {
                if(type==1){
                    var numRow = $('table#tbl_yarn_cost tbody tr').length;
                    var total_Yarn_Rate=0;
                    for( var i = 1; i <= numRow; i++ ){
                        total_Yarn_Rate += $('#txt_Yarn_Rate_'+i).val()*1;
                    }
                    $('#txt_total_yarn_cost').val(total_Yarn_Rate.toFixed(4));
                    calculate_total_rate();
                }else if(type==4){
                    var numRow = $('table#tbl_df tbody tr').length;
                    var total_df_cost=0;
                    for( var i = 1; i <= numRow; i++ ){
                        total_df_cost += $('#txt_df_Rate_'+i).val()*1;
                    }
                    $('#txt_df_cost').val(total_df_cost.toFixed(4));
                    calculate_total_rate();
                }
            }
            else
            {
                if(type==1){
                    var yarn_Rate_1=$('#txt_Yarn_Rate_1').val()*1;
                    var yarn_Rate_2=$('#txt_Yarn_Rate_2').val()*1;
                    var yarn_Rate_3=$('#txt_Yarn_Rate_3').val()*1;
                    var total_yarn_cost=yarn_Rate_1+yarn_Rate_2+yarn_Rate_3;
                    $('#txt_total_yarn_cost').val( total_yarn_cost );
                } else if(type==3){
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
            var total_cost=total_yarn_cost+yarn_dyeing_cost+knitting_cost+df_cost+aop_cost;
            $('#txt_total_cost').val( total_cost );
            $('#txt_fabric').val( total_cost );
            calculate_marketing_cost()
        }

        function calculate_marketing_cost()
        {
            var fabric_cost=$('#txt_fabric').val()*1;
            var accessories_cost=$('#txt_accessories').val()*1;
            var avl_min=$('#txt_avl_min').val()*1;
            var cm_dzn=$('#txt_cm_dzn').val()*1;
            var frieght_dzn=$('#txt_frieght_dzn').val()*1;
            var lab_dzn=$('#txt_lab_dzn').val()*1;
            var mis_offer_qty=$('#txt_mis_offer_qty').val()*1;
            var other_cost_dzn=$('#txt_other_cost_dzn').val()*1;
            var com_dzn=$('#txt_com_dzn').val()*1;
            var fob_dzn=fabric_cost+accessories_cost+avl_min+cm_dzn+frieght_dzn+lab_dzn+mis_offer_qty+other_cost_dzn+com_dzn;
            //alert(fob_dzn);
            $('#txt_fob_dzn').val( fob_dzn );

            var fob_pcs=$('#txt_fob_pcs').val()*1;
            var margin_dzn=fob_pcs-fob_dzn;
            $('#txt_margin_per_dzn').val( margin_dzn );

            //F.O.B($/PCS)-Cost   CM ($/DZN) due
        }
        
        function fnc_details_popup(row,qc_no,action)
        {
            //alert(row)
            emailwindow=dhtmlmodal.open('EmailBox', 'iframe','quick_costing_margin_entry_controller.php?qc_no='+qc_no+'&action='+action,'Details Popup', 'width=700px,height=400px,center=1,resize=0','../../');
            emailwindow.onclose=function()
            {
                var popupData=this.contentDoc.getElementById("popupData").value;
                var popupData=popupData.split('_');
                //alert(popupData);
                if(action=='yarn_count_popup')
                {
                    $('#lib_yarn_rate_id_'+row).val(popupData[0]);
                    $('#txt_Yarn_Yarn_Count_'+row).val(popupData[1]);
                    $('#txt_Yarn_Yarn_Type_'+row).val(popupData[2]);
                    $('#txt_Yarn_Rate_'+row).val(popupData[3]);
                    calculate_rate(1,1,1,popupData[3])
                }
                else if(action=='dyeing_finishing_popup')
                {
                    $('#lib_dyeing_finishing_id_'+row).val(popupData[0]);
                    $('#txt_df_Color_'+row).val(popupData[1]);
                    $('#txt_df_Rate_'+row).val(popupData[2]);
                    calculate_rate(1,4,1,popupData[2])
                }
            }
        }
    </script>
    <body onLoad="set_hotkey()">
    <div align="center" style="width:100%;">
        <? 
        echo load_freeze_divs ("../../../",'',1); 
        $sql_qc=sql_select("select id,qc_no,fabric_cost,accessories_cost,avl_min,cm_cost,frieght_cost,lab_test_cost,mis_offer_qty,other_cost,com_cost,fob,fob_pcs,margin,margin_percent,Yarn_Yarn_Count_1,Yarn_Yarn_Type_1,Yarn_Rate_1,Yarn_Yarn_Count_2,Yarn_Yarn_Type_2,Yarn_Rate_2,Yarn_Yarn_Count_3,Yarn_Yarn_Type_3,Yarn_Rate_3,total_yarn_cost,yarn_dyeing_cost,knit_Yarn_Count_1,knit_Fabric_Type_1,knit_Rate_1,knit_Yarn_Count_2,knit_Fabric_Type_2,knit_Rate_2,knitting_cost,df_Color_Type_1,df_Color_1,df_Rate_1,df_Color_Type_2,df_Color_2,df_Rate_2,df_Color_Type_3,df_Color_3,df_Rate_3,df_cost,aop_cost,total_cost from qc_margin_mst where qc_no=$qc_no and status_active=1 and is_deleted=0");
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
                $Yarn_Yarn_Count_1      =$row[csf('Yarn_Yarn_Count_1')];
                $Yarn_Yarn_Type_1       =$row[csf('Yarn_Yarn_Type_1')];
                $Yarn_Rate_1            =$row[csf('Yarn_Rate_1')];
                $Yarn_Yarn_Count_2      =$row[csf('Yarn_Yarn_Count_2')];
                $Yarn_Yarn_Type_2       =$row[csf('Yarn_Yarn_Type_2')];
                $Yarn_Rate_2            =$row[csf('Yarn_Rate_2')];
                $Yarn_Yarn_Count_3      =$row[csf('Yarn_Yarn_Count_3')];
                $Yarn_Yarn_Type_3       =$row[csf('Yarn_Yarn_Type_3')];
                $Yarn_Rate_3            =$row[csf('Yarn_Rate_3 ')];
                $total_yarn_cost        =$row[csf('total_yarn_cost')];
                $yarn_dyeing_cost       =$row[csf('yarn_dyeing_cost')];
                $knit_Yarn_Count_1      =$row[csf('knit_Yarn_Count_1')];
                $knit_Fabric_Type_1     =$row[csf('knit_Fabric_Type_1')];
                $knit_Rate_1            =$row[csf('knit_Rate_1')];
                $knit_Yarn_Count_2      =$row[csf('knit_Yarn_Count_2')];
                $knit_Fabric_Type_2     =$row[csf('knit_Fabric_Type_2')];
                $knit_Rate_2            =$row[csf('knit_Rate_2')];
                $knitting_cost          =$row[csf('knitting_cost')];
                $df_Color_Type_1        =$row[csf('df_Color_Type_1')];
                $df_Color_1             =$row[csf('df_Color_1')];
                $df_Rate_1              =$row[csf('df_Rate_1')];
                $df_Color_Type_2        =$row[csf('df_Color_Type_2')];
                $df_Color_2             =$row[csf('df_Color_2')];
                $df_Rate_2              =$row[csf('df_Rate_2')];
                $df_Color_Type_3        =$row[csf('df_Color_Type_3')];
                $df_Color_3             =$row[csf('df_Color_3')];
                $df_Rate_3              =$row[csf('df_Rate_3')];
                $df_cost                =$row[csf('df_cost')];
                $aop_cost               =$row[csf('aop_cost')];
                $total_cost             =$row[csf('total_cost')];
                $update_button_active   =1;
            }
        } 
        else 
        {
            $update_id=$fabric_cost=$accessories_cost=$avl_min=$cm_cost=$frieght_cost=$lab_test_cost=$mis_offer_qty=$other_cost=$com_cost=$fob=$margin=$margin_percent=$Yarn_Yarn_Count_1=$Yarn_Yarn_Type_1=$Yarn_Rate_1=$Yarn_Yarn_Count_2=$Yarn_Yarn_Type_2=$Yarn_Rate_2=$Yarn_Yarn_Count_3=$Yarn_Yarn_Type_3=$Yarn_Rate_3=$total_yarn_cost=$yarn_dyeing_cost=$knit_Yarn_Count_1=$knit_Fabric_Type_1=$knit_Rate_1=$knit_Yarn_Count_2=$knit_Fabric_Type_2=$knit_Rate_2=$knitting_cost=$df_Color_Type_1=$df_Color_1=$df_Rate_1=$df_Color_Type_2=$df_Color_2=$df_Rate_2=$df_Color_Type_3=$df_Color_3=$df_Rate_3=$df_cost=$aop_cost=$total_cost='';
            $update_button_active   =0;
            $tot_fob_cost=return_field_value( "tot_fob_cost", "qc_tot_cost_summary"," mst_id=$qc_no and status_active=1 and is_deleted=0");
            $fob_pcs=$tot_fob_cost*12;
            $tot_fob_cost=return_field_value( "cost_per_minute", "lib_standard_cm_entry"," $costing_date' between applying_period_date and applying_period_to_date and status_active=1 and is_deleted=0");
            $cpm=sql_select("select cost_per_minute from lib_standard_cm_entry where '$costing_date' between applying_period_date and applying_period_to_date and status_active=1 and is_deleted=0"); //company_id=$cbo_company_name and
        }
        $sql_cost_summary=sql_select("select  id, mst_id, buyer_agent_id, location_id, no_of_pack, is_confirm, is_cm_calculative, mis_lumsum_cost, commision_per, tot_fab_cost, tot_sp_operation_cost, tot_accessories_cost, tot_cm_cost, tot_fright_cost, tot_lab_test_cost, tot_miscellaneous_cost, tot_other_cost, tot_commission_cost, tot_cost, tot_fob_cost, tot_rmg_ratio from qc_tot_cost_summary where mst_id=$qc_no and status_active=1 and is_deleted=0");
        if(count($sql_cost_summary)>0){
            foreach($sql_cost_summary as $row)
            {
                $fabric_cost_qc     =$row[csf('tot_fab_cost')];
                $accessories_cost_qc=$row[csf('tot_accessories_cost')];
                //$avl_min_qc=$row[csf('tot_fab_cost')];
                $cm_cost_qc         =$row[csf('tot_cm_cost')];
                $frieght_cost_qc    =$row[csf('tot_fright_cost')];
                $lab_test_cost_qc   =$row[csf('tot_lab_test_cost')];
                //$mis_offer_qty_qc   =$row[csf('tot_fab_cost')];
                //$other_cost_qc      =$row[csf('tot_fab_cost')];
                $com_cost_qc        =$row[csf('tot_commission_cost')];
                $fob_qc             =$row[csf('tot_fob_cost')];
                //$margin_qc          =$row[csf('tot_fab_cost')];
                //$margin_percent_qc  =$row[csf('tot_fab_cost')];
                //$tot_fob_cost       =$row[csf('tot_fob_cost')];
            }
        }
        $buyer_info=explode('_', $buyer);
        ?>
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
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_fabric" id="txt_fabric" value="<? echo $fabric_cost; ?>" style="width:51px" readonly="readonly" placeholder="Display" ></td>
                            </tr>
                            <tr>
                                <td width="120">Accessories</td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_qc_accessories" id="txt_qc_accessories" value="<? echo $accessories_cost_qc; ?>" onKeyUp="calculate_marketing_cost()" style="width:51px" readonly="readonly" placeholder="Display" ></td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_accessories" id="txt_accessories" value="<? echo $accessories_cost; ?>" onKeyUp="calculate_marketing_cost()" style="width:51px" ></td>
                            </tr>
                            <tr>
                                <td width="120">AVL Min.</td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_qc_avl_min" id="txt_qc_avl_min" value="<? echo $avl_min; ?>" onKeyUp="calculate_marketing_cost()" style="width:51px"  readonly="readonly" placeholder="Display"></td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_avl_min" id="txt_avl_min" value="<? echo $avl_min; ?>" onKeyUp="calculate_marketing_cost()" style="width:51px" ></td>
                            </tr>
                            <tr>
                                <td width="120">CM ($/DZN)</td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_qc_cm_dzn" id="txt_qc_cm_dzn" value="<? echo $cm_cost_qc; ?>"  readonly="readonly"  style="width:51px"  readonly="readonly" placeholder="Display"></td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_cm_dzn" id="txt_cm_dzn" value="<? echo $cm_cost; ?>"   readonly="readonly" placeholder="Display"  style="width:51px" ></td>
                            </tr>
                            <tr>
                                <td width="120">Frieght Cost($/DZN)</td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_qc_frieght_dzn" id="txt_qc_frieght_dzn" value="<? echo $frieght_cost_qc; ?>" onKeyUp="calculate_marketing_cost()" style="width:51px" readonly="readonly" placeholder="Display" ></td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_frieght_dzn" id="txt_frieght_dzn" value="<? echo $frieght_cost; ?>" onKeyUp="calculate_marketing_cost()" style="width:51px" ></td>
                            </tr>
                            <tr>
                                <td width="120">Lab - Test($/DZN)</td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_qc_lab_dzn" id="txt_qc_lab_dzn" value="<? echo $lab_test_cost_qc; ?>" onKeyUp="calculate_marketing_cost()" style="width:51px" readonly="readonly" placeholder="Display"></td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_lab_dzn" id="txt_lab_dzn" value="<? echo $lab_test_cost; ?>" onKeyUp="calculate_marketing_cost()" style="width:51px" ></td>
                            </tr>
                            <tr>
                                <td width="120">Mis/Offer Qty.</td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_qc_mis_offer_qty" id="txt_qc_mis_offer_qty" value="<? echo $mis_offer_qty; ?>" onKeyUp="calculate_marketing_cost()" style="width:51px" readonly="readonly" placeholder="Display" ></td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_mis_offer_qty" id="txt_mis_offer_qty" value="<? echo $mis_offer_qty; ?>" onKeyUp="calculate_marketing_cost()" style="width:51px" ></td>
                            </tr>
                            <tr>
                                <td width="120">Other Cost($/DZN)</td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_qc_other_cost_dzn" id="txt_qc_other_cost_dzn" value="<? echo $other_cost; ?>" onKeyUp="calculate_marketing_cost()" style="width:51px"  readonly="readonly" placeholder="Display"></td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_other_cost_dzn" id="txt_other_cost_dzn" value="<? echo $other_cost; ?>" onKeyUp="calculate_marketing_cost()" style="width:51px" ></td>
                            </tr>
                            <tr>
                                <td width="120">Com.(%)($/DZN)</td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_qc_com_dzn" id="txt_qc_com_dzn" value="<? echo $com_cost_qc; ?>" onKeyUp="calculate_marketing_cost()" style="width:51px"  readonly="readonly" placeholder="Display"></td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_com_dzn" id="txt_com_dzn" value="<? echo $com_cost; ?>" onKeyUp="calculate_marketing_cost()" style="width:51px" ></td>
                            </tr>
                            <tr>
                                <td width="120"><strong>F.O.B($/DZN)</stron style="width:51px"g></td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_qc_fob_dzn" id="txt_qc_fob_dzn" value="<? echo $fob_qc; ?>" readonly="readonly" style="width:51px" readonly="readonly" placeholder="Display" ></td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_fob_dzn" id="txt_fob_dzn" value="<? echo $fob; ?>"  readonly="readonly" placeholder="Display" style="width:51px" ></td>
                            </tr>
                            <tr>
                                <td width="120">F.O.B($/PCS)-Cost</td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_qc_fob_pcs" id="txt_qc_fob_pcs" value="<? echo $fob_pcs; ?>" readonly="readonly"  style="width:51px" readonly="readonly" placeholder="Display" ></td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_fob_pcs" id="txt_fob_pcs" value="<? echo $fob_pcs; ?>"  readonly="readonly"placeholder="Display"  style="width:51px" ></td>
                            </tr>
                            <tr>
                                <td width="120">Margin Per/DZN</td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_qc_margin_per_dzn" id="txt_qc_margin_per_dzn" value="<? echo $margin; ?>" style="width:51px" readonly="readonly" placeholder="Display" ></td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_margin_per_dzn" id="txt_margin_per_dzn" value="<? echo $margin; ?>" style="width:51px"  readonly="readonly" placeholder="Display" ></td>
                            </tr>
                            <tr>
                                <td width="120">Margin %</td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_qc_margin" id="txt_qc_margin" value="<? echo $margin_percent; ?>" style="width:51px" readonly="readonly"placeholder="Display" ></td>
                                <td width="65"><input type="text" class="text_boxes_numeric" name="txt_margin" id="txt_margin" value="<? echo $margin_percent; ?>" style="width:51px" readonly="readonly" placeholder="Display" ></td>
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
                                <td width="100"><? echo $buyer_info[0]; ?></td>
                            </tr>
                            <tr>
                                <td width="150">CPM</td>
                                <td width="100"><input type="text" class="text_boxes_numeric" name="txt_cpm" id="txt_cpm" value="" readonly="readonly" ></td>
                            </tr>
                            <tr>
                                <td width="150">SMV</td>
                                <td width="100"><input type="text" class="text_boxes_numeric" name="txt_smv" id="txt_smv" value="" ></td>
                            </tr>
                            <tr>
                                <td width="150">Efficency %</td>
                                <td width="100"><input type="text" class="text_boxes_numeric" name="txt_efficency" id="txt_efficency" value="" readonly="readonly" ></td>
                            </tr>
                            <tr>
                                <td width="150">CM</td>
                                <td width="100"><input type="text" class="text_boxes_numeric" name="txt_cm" id="txt_cm" value="" readonly="readonly" ></td>
                            </tr>
                            <tr>
                                <td width="150">Available Minutes</td>
                                <td width="100"><input type="text" class="text_boxes_numeric" name="txt_available_min" id="txt_available_min" value="" readonly="readonly" ></td>
                            </tr>
                        </tbody>
                    </tr>
                </table>
                </div>

                <div  style="width: 470px; float: right;">
                    <table width="450" cellpadding="2" cellspacing="2" class="rpt_table" border="1" rules="all" align="left">
                        <thead>
                            <tr>
                                <th colspan="5">Yarn details</th>
                            </tr>       
                            <tr>
                                <th width="100">Yarn Count</th>
                                <th width="100">Yarn Type</th>
                                <th width="100">Yarn details</th>
                                <th width="75">QC Rate</th>
                                <th>Actual Rate</th>
                            </tr>
                        </thead>
                    </table>
                    <div style="width:470px; max-height:85px; float: left; overflow-y:scroll" id="scroll_body">
                        <table width="450" cellpadding="2" cellspacing="2" class="rpt_table" border="1" rules="all" align="left" id="tbl_yarn_cost">
                            <tbody>
                                <?
                                //echo "select  id, rate_data from qc_cons_rate_dtls where mst_id=$qc_no and type=1 and is_calculation = 1 and rate_data is not null and status_active=1 and is_deleted=0";
                                $sql_cons_rate=sql_select("select  id, rate_data from qc_cons_rate_dtls where mst_id=$qc_no and type=1 and is_calculation = 1 and rate_data is not null and status_active=1 and is_deleted=0");
                                if(count($sql_cons_rate)>0){
                                    $i=1;
                                    foreach($sql_cons_rate as $row){
                                        $rate_data=explode('~~',$row[csf('rate_data')]);
                                        $rate_data_id=$row[csf('id')];
                                        $yrnDtlsArr = array(1 => $rate_data[0].'_'.$rate_data[3], 2 => $rate_data[4].'_'.$rate_data[7], 3 => $rate_data[8].'_'.$rate_data[11]);
                                        foreach($yrnDtlsArr as $row){ 
                                            $yarnDtlsData=explode('_',$row);
                                            ?>
                                            <tr> 
                                                <td width="100"><input type="text" class="text_boxes" name="txt_Yarn_Yarn_Count_<? echo $i; ?>" id="txt_Yarn_Yarn_Count_<? echo $i; ?>" value="<? echo $Yarn_Yarn_Count_1; ?>" style="width:86px" onclick="fnc_details_popup('<? echo $i;?>','<? echo $qc_no;?>','yarn_count_popup')" readonly="readonly" placeholder="Browse" >
                                                    <input type="hidden" class="text_boxes" name="lib_yarn_rate_id_<? echo $i; ?>" id="lib_yarn_rate_id_<? echo $i; ?>"  >
                                                    <input type="hidden" class="text_boxes" name="lib_rate_data_id_<? echo $i; ?>" id="lib_rate_data_id_<? echo $i; ?>" value="<? echo $rate_data_id; ?>" >
                                                    <input type="hidden" class="text_boxes" name="yarn_dtls_update_id_<? echo $i; ?>" id="yarn_dtls_update_id_<? echo $i; ?>" value="<? echo $rate_data_id; ?>" >
                                                </td>
                                                <td width="100"><input type="text" class="text_boxes" name="txt_Yarn_Yarn_Type_<? echo $i; ?>" id="txt_Yarn_Yarn_Type_<? echo $i; ?>" value="<? echo $Yarn_Yarn_Type_1; ?>" style="width:86px" readonly="readonly" placeholder="Display"  ></td>
                                                <td width="100"><input type="text" class="text_boxes" name="txt_Yarn_Yarn_Dtls_<? echo $i; ?>" id="txt_Yarn_Yarn_Dtls_<? echo $i; ?>" value="<? echo $yarnDtlsData[0]; ?>" style="width:86px" readonly="readonly" placeholder="Display"  ></td>
                                                <td ><input type="text" class="text_boxes_numeric" name="txt_qc_Yarn_Rate_<? echo $i; ?>" id="txt_qc_Yarn_Rate_<? echo $i; ?>" value="<? echo $yarnDtlsData[1]; $total_qc_rate +=$yarnDtlsData[1]; ?>"  style="width:61px"  readonly="readonly" placeholder="Display"> </td>
                                                <td ><input type="text" class="text_boxes_numeric" name="txt_Yarn_Rate_<? echo $i; ?>" id="txt_Yarn_Rate_<? echo $i; ?>" value="<? echo $Yarn_Rate_1; ?>"  onKeyUp="calculate_rate(1,<? echo $i; ?>,this.value)" style="width:61px" readonly="readonly" placeholder="Display"  > </td>
                                            </tr>
                                            <? $i++;
                                        }
                                    }
                                } ?>
                            </tbody>
                        </table>
                    </div>
                    <table width="450" cellpadding="2" cellspacing="2" class="rpt_table" border="1" rules="all" align="left">
                        <tfoot>
                            <tr>
                                <td colspan="3"><strong>Total Yarn Cost</strong></td>
                                <td width="74" ><input type="text" class="text_boxes_numeric" name="txt_qc_total_yarn_cost" id="txt_qc_total_yarn_cost" value="<? echo $total_qc_rate; ?>" readonly="readonly"  style="width:61px"  readonly="readonly" placeholder="Display"></td>
                                <td width="74" ><input type="text" class="text_boxes_numeric" name="txt_total_yarn_cost" id="txt_total_yarn_cost" value="<? echo $total_yarn_cost; ?>" readonly="readonly" placeholder="Display"  style="width:61px" ></td>
                                
                            </tr>
                            <tr>
                                <td colspan="3">Yarn Dyeing Cost</td>
                                <td width="74"  ><input type="text" class="text_boxes_numeric" name="txt_qc_yarn_dyeing_cost" id="txt_qc_yarn_dyeing_cost" value="<? echo $yarn_dyeing_cost; ?>" onKeyUp="calculate_rate(2,1,this.value)" style="width:61px" readonly="readonly" placeholder="Display"> </td>
                                <td width="74"  ><input type="text" class="text_boxes_numeric" name="txt_yarn_dyeing_cost" id="txt_yarn_dyeing_cost" value="<? echo $yarn_dyeing_cost; ?>" onKeyUp="calculate_rate(2,1,this.value)" style="width:61px" > </td>
                            </tr>
                        </tfoot>
                    </table>
                    <table width="450" cellpadding="2" cellspacing="2" class="rpt_table" border="1" rules="all" align="left">
                        <thead>
                            <tr>
                                <th colspan="4">Knitting  Details</th>
                            </tr>       
                            <tr>
                                <th width="150">Yarn Count</th>
                                <th width="150">Fabric Type</th>
                                <th width="75">QC Rate</th>
                                <th>Actual Rate</th>
                            </tr>
                        </thead>
                    </table>
                    <table width="450" cellpadding="2" cellspacing="2" class="rpt_table" border="1" rules="all" align="left" id="tbl_kniting_cost">
                        <tbody>
                            <tr> 
                                <td width="150"><input type="text" class="text_boxes_numeric" name="txt_knit_Yarn_Count_1" id="txt_knit_Yarn_Count_1" value="<? echo $knit_Yarn_Count_1; ?>" style="width:136px" onclick="fnc_details_popup('1','<? echo $qc_no;?>','kniting_details_popup')"  readonly="readonly" placeholder="Browse"  >
                                    <input type="hidden" class="text_boxes" name="lib_knit_Yarn_id_1" id="lib_knit_Yarn_id_1"  >
                                    <input type="hidden" class="text_boxes" name="knit_dtls_update_id_1" id="knit_dtls_update_id_1" value="" >
                                </td>
                                <td width="150"><input type="text" class="text_boxes_numeric" name="txt_knit_Fabric_Type_1" id="txt_knit_Fabric_Type_1" value="<? echo $knit_Fabric_Type_1; ?>" style="width:136px" readonly="readonly" placeholder="Display" ></td>
                                <td ><input type="text" class="text_boxes_numeric" name="txt_qc_knit_Rate_1" id="txt_qc_knit_Rate_1" value="<? echo $knit_Rate_1; ?>" onKeyUp="calculate_rate(3,1,this.value)" style="width:61px"  readonly="readonly" placeholder="Display" ></td>
                                <td ><input type="text" class="text_boxes_numeric" name="txt_knit_Rate_1" id="txt_knit_Rate_1" value="<? echo $knit_Rate_1; ?>" onKeyUp="calculate_rate(3,1,this.value)" style="width:61px" readonly="readonly" placeholder="Display"  ></td>
                            </tr>
                            <tr>
                                <td width="150"><input type="text" class="text_boxes_numeric" name="txt_knit_Yarn_Count_2" id="txt_knit_Yarn_Count_2" value="<? echo $knit_Yarn_Count_2; ?>" style="width:136px" onclick="fnc_details_popup('2','<? echo $qc_no;?>','kniting_details_popup')"  readonly="readonly" placeholder="Browse"  >
                                    <input type="hidden" class="text_boxes" name="lib_knit_Yarn_id_2" id="lib_knit_Yarn_id_2"  >
                                    <input type="hidden" class="text_boxes" name="knit_dtls_update_id_2" id="knit_dtls_update_id_2" value="" >
                                </td>
                                <td width="150"><input type="text" class="text_boxes_numeric" name="txt_knit_Fabric_Type_2" id="txt_knit_Fabric_Type_2" value="<? echo $knit_Fabric_Type_2; ?>" style="width:136px" readonly="readonly" placeholder="Display"  ></td>
                                <td ><input type="text" class="text_boxes_numeric" name="txt_qc_knit_Rate_2" id="txt_qc_knit_Rate_2" value="<? echo $knit_Rate_2; ?>" onKeyUp="calculate_rate(3,2,this.value)" style="width:61px"  readonly="readonly" placeholder="Display" ></td>
                                <td ><input type="text" class="text_boxes_numeric" name="txt_knit_Rate_2" id="txt_knit_Rate_2" value="<? echo $knit_Rate_2; ?>" onKeyUp="calculate_rate(3,2,this.value)" style="width:61px" readonly="readonly" placeholder="Display"  ></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2"><strong>Knitting Cost</strong></td>
                                <td width="150"><input type="text" class="text_boxes_numeric" name="txt_qc_knitting_cost" id="txt_qc_knitting_cost" value="<? echo $knitting_cost; ?>" readonly="readonly" style="width:61px"  readonly="readonly" placeholder="Display"></td>
                                <td><input type="text" class="text_boxes_numeric" name="txt_knitting_cost" id="txt_knitting_cost" value="<? echo $knitting_cost; ?>" readonly="readonly" placeholder="Display" style="width:61px" ></td>
                            </tr>
                        </tfoot>
                    </table>
                    <table width="450" cellpadding="2" cellspacing="2" class="rpt_table" border="1" rules="all" align="left" id="tbl_df">
                        <thead>
                            <tr>
                                <th colspan="4">Dyeing Finishing Details</th>
                            </tr>       
                            <tr>
                                <th width="150">Color Type</th>
                                <th width="150">Color</th>
                                <th width="75">QC Rate</th>
                                <th>Actual Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td width="150"><input type="text" class="text_boxes" name="txt_df_Color_Type_1" id="txt_df_Color_Type_1" value="<? echo $df_Color_Type_1; ?>"  style="width:136px" onclick="fnc_details_popup('1','<? echo $qc_no;?>','dyeing_finishing_popup')"  readonly="readonly" placeholder="Browse" >
                                    <input type="hidden" class="text_boxes" name="lib_dyeing_finishing_id_1" id="lib_dyeing_finishing_id_1"  value="" >
                                    <input type="hidden" class="text_boxes" name="df_dtls_update_id_1" id="df_dtls_update_id_1" value="" >
                                </td>
                                <td width="150"><input type="text" class="text_boxes" name="txt_df_Color_1" id="txt_df_Color_1" value="<? echo $df_Color_1; ?>" style="width:136px" readonly="readonly" placeholder="Display" ></td>
                                <td width="75" ><input type="text" class="text_boxes_numeric" name="txt_qc_df_Rate_1" id="txt_qc_df_Rate_1" value="<? echo $df_Rate_1; ?>"  onKeyUp="calculate_rate(4,1,this.value)" style="width:61px" readonly="readonly" placeholder="Display" ></td>
                                <td ><input type="text" class="text_boxes_numeric" name="txt_df_Rate_1" id="txt_df_Rate_1" value="<? echo $df_Rate_1; ?>"  onKeyUp="calculate_rate(4,1,this.value)" style="width:61px" readonly="readonly" placeholder="Display"></td>
                            </tr>
                            <tr>
                                <td width="150"><input type="text" class="text_boxes" name="txt_df_Color_Type_2" id="txt_df_Color_Type_2" value="<? echo $df_Color_Type_2; ?>"  style="width:136px" onclick="fnc_details_popup('2','<? echo $qc_no;?>','dyeing_finishing_popup')"  readonly="readonly" placeholder="Browse"  >
                                    <input type="hidden" class="text_boxes" name="lib_dyeing_finishing_id_2" id="lib_dyeing_finishing_id_2"  value="" >
                                    <input type="hidden" class="text_boxes" name="df_dtls_update_id_2" id="df_dtls_update_id_2" value="" >
                                </td>
                                <td width="150"><input type="text" class="text_boxes" name="txt_df_Color_2" id="txt_df_Color_2" value="<? echo $df_Color_2; ?>" style="width:136px" readonly="readonly" placeholder="Display" ></td>
                                <td width="75"><input type="text" class="text_boxes_numeric" name="txt_qc_df_Rate_2" id="txt_qc_df_Rate_2" value="<? echo $df_Rate_2; ?>" onKeyUp="calculate_rate(4,2,this.value)" style="width:61px" readonly="readonly" placeholder="Display" ></td>
                                <td ><input type="text" class="text_boxes_numeric" name="txt_df_Rate_2" id="txt_df_Rate_2" value="<? echo $df_Rate_2; ?>" onKeyUp="calculate_rate(4,2,this.value)" style="width:61px" readonly="readonly" placeholder="Display"></td>
                            </tr>
                            <tr>
                                <td width="150"><input type="text" class="text_boxes" name="txt_df_Color_Type_3" id="txt_df_Color_Type_3" value="<? echo $df_Color_Type_3; ?>"  style="width:136px" onclick="fnc_details_popup('3','<? echo $qc_no;?>','dyeing_finishing_popup')"  readonly="readonly" placeholder="Browse"  >
                                    <input type="hidden" class="text_boxes" name="lib_dyeing_finishing_id_3" id="lib_dyeing_finishing_id_3"  value="" >
                                    <input type="hidden" class="text_boxes" name="df_dtls_update_id_3" id="df_dtls_update_id_3" value="" >
                                </td>
                                <td width="150"><input type="text" class="text_boxes" name="txt_df_Color_3" id="txt_df_Color_3" value="<? echo $df_Color_3; ?>"  style="width:136px" readonly="readonly" placeholder="Display" ></td>
                                <td width="75"><input type="text" class="text_boxes_numeric" name="txt_qc_df_Rate_3" id="txt_qc_df_Rate_3" value="<? echo $df_Rate_3; ?>" onKeyUp="calculate_rate(4,3,this.value)" style="width:61px" readonly="readonly" placeholder="Display" ></td>
                                <td ><input type="text" class="text_boxes_numeric" name="txt_df_Rate_3" id="txt_df_Rate_3" value="<? echo $df_Rate_3; ?>" onKeyUp="calculate_rate(4,3,this.value)" style="width:61px" readonly="readonly" placeholder="Display"></td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2"><strong>Dyeing Finishing Cost</strong></td>
                                <td width="75"><input type="text" class="text_boxes_numeric" name="txt_qc_df_cost" id="txt_qc_df_cost" value="<? echo $df_cost; ?>" readonly="readonly" style="width:61px"  readonly="readonly" placeholder="Display" ></td>
                                <td ><input type="text" class="text_boxes_numeric" name="txt_df_cost" id="txt_df_cost" value="<? echo $df_cost; ?>" readonly="readonly" placeholder="Display" style="width:61px" readonly="readonly" placeholder="Display"  ></td>
                            </tr>
                            <tr>
                                <td colspan="2">AOP Cost </td>
                                <td width="75"><input type="text" class="text_boxes_numeric" name="txt_qc_aop_cost" id="txt_qc_aop_cost" value="<? echo $aop_cost; ?>" onKeyUp="calculate_rate(5,1,this.value)" style="width:61px"  readonly="readonly" placeholder="Display" ></td>
                                <td ><input type="text" class="text_boxes_numeric" name="txt_aop_cost" id="txt_aop_cost" value="<? echo $aop_cost; ?>" onKeyUp="calculate_rate(5,1,this.value)" style="width:61px"  ></td>
                            </tr>
                            <tr>
                                <td colspan="2"><strong>Fabric Total Cost</strong></td>
                                <td width="75"><input type="text" class="text_boxes_numeric" name="txt_qc_total_cost" id="txt_qc_total_cost" value="<? echo $total_cost; ?>" readonly="readonly" style="width:61px"  readonly="readonly" placeholder="Display"></td>
                                <td ><input type="text" class="text_boxes_numeric" name="txt_total_cost" id="txt_total_cost" value="<? echo $total_cost; ?>" readonly="readonly" placeholder="Display" style="width:61px"  ></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
            </div>
            
            <div style="width: 100%">
                <table  style="width: 100%">
                    <tr>
                        <td height="50" valign="middle" align="center" class="button_container">
                            <input type="hidden" class="text_boxes" name="hid_qc_no" id="hid_qc_no" value="<? echo $qc_no ?>">
                            <input type="hidden" class="text_boxes" name="update_id" id="update_id" value="<? echo $update_id ?>">
                        <? echo load_submit_buttons( $permission, "fnc_cost_entry", $update_button_active ,0 ,"reset_for_refresh()",1); ?>
                        </td>
                    </tr>
                </table>
            </div>
            <div style="width:100%"><p align="center"><input type="button" id="btn_close" class="formbutton" style="width:100px;" value="Close" onClick="frm_close();" ></p></div>
        </form>
    </div>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
    <?
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
        
        $field_array="id,qc_no,fabric_cost,accessories_cost,avl_min,cm_cost,frieght_cost,lab_test_cost,mis_offer_qty,other_cost,com_cost,fob,fob_pcs,margin,margin_percent,total_yarn_cost,yarn_dyeing_cost,knitting_cost,df_cost,aop_cost,total_cost,inserted_by,insert_date";
        
        $field_array_dtls=" id, qc_no, mst_id, type, lib_table_id, rate_data_id, yarn_count, yarn_type, yarn_details, qc_rate, actual_rate, febric_type, color_type, color, inserted_by, insert_date";
        $add_commaa=0;
        for($i=1;$i<=$numRowYarn;$i++)
        {
            $lib_yarn_rate_id   ="lib_yarn_rate_id_".$i;
            $lib_rate_data_id   ="lib_rate_data_id_".$i;
            $txt_Yarn_Yarn_Count="txt_Yarn_Yarn_Count_".$i;
            $txt_Yarn_Yarn_Type ="txt_Yarn_Yarn_Type_".$i;
            $txt_Yarn_Yarn_Dtls ="txt_Yarn_Yarn_Dtls_".$i;
            $txt_qc_Yarn_Rate   ="txt_qc_Yarn_Rate_".$i;
            $txt_Yarn_Rate      ="txt_Yarn_Rate_".$i;

            if ($add_commaa!=0) $data_array_dtls .=","; $add_comma=0;
            $data_array_dtls .="(".$dtlsId.",".$hid_qc_no.",".$id.","1",".$$lib_yarn_rate_id.",".$$lib_rate_data_id.",".$$txt_Yarn_Yarn_Count.",".$$txt_Yarn_Yarn_Type.",".$$txt_Yarn_Yarn_Dtls.",".$$txt_qc_Yarn_Rate.",".$$txt_Yarn_Rate.",'','','','".$user_id."','".$pc_date_time."')";
            $dtlsId++; $add_comma++;
        }

        for($j=1;$j<=$numRowknit;$j++)
        {
            $lib_knit_Yarn_id       ="lib_knit_Yarn_id_".$j;
            $txt_knit_Yarn_Count    ="txt_knit_Yarn_Count_".$j;
            $txt_knit_Fabric_Type   ="txt_knit_Fabric_Type_".$j;
            $txt_qc_knit_Rate       ="txt_qc_knit_Rate_".$j;
            $txt_knit_Rate          ="txt_knit_Rate_".$j;

            if ($add_commaa!=0) $data_array_dtls .=","; $add_comma=0;
            $data_array_dtls .="(".$dtlsId.",".$hid_qc_no.",".$id.","2",".$$lib_knit_Yarn_id.",'',".$$txt_knit_Yarn_Count.",'','',".$$txt_qc_knit_Rate.",".$$txt_knit_Rate.",".$$txt_knit_Fabric_Type.",'','','".$user_id."','".$pc_date_time."')";
            $dtlsId++; $add_comma++;
        }

        for($k=1;$k<=$numRowDF;$k++)
        {
            $lib_dyeing_finishing_id    ="lib_dyeing_finishing_id_".$k;
            $txt_df_Color_Type          ="txt_df_Color_Type_".$k;
            $txt_df_Color               ="txt_df_Color_".$k;
            $txt_qc_df_Rate             ="txt_qc_df_Rate_".$k;
            $txt_df_Rate                ="txt_df_Rate_".$k;

            if ($add_commaa!=0) $data_array_dtls .=","; $add_comma=0;
            $data_array_dtls .="(".$dtlsId.",".$hid_qc_no.",".$id.","3",".$$lib_dyeing_finishing_id.",'','','','',".$$txt_qc_df_Rate.",".$$txt_df_Rate.",'',".$$txt_df_Color_Type.",".$$txt_df_Color.",'".$user_id."','".$pc_date_time."')";
            $dtlsId++; $add_comma++;
        }
           
        $data_array="(".$id.",".$hid_qc_no.",".$txt_fabric.",".$txt_accessories.",".$txt_avl_min.",".$txt_cm_dzn.",".$txt_frieght_dzn.",".$txt_lab_dzn.",".$txt_mis_offer_qty.",".$txt_other_cost_dzn.",".$txt_com_dzn.",".$txt_fob_dzn.",".$txt_fob_pcs.",".$txt_margin_per_dzn.",".$txt_margin.",".$txt_total_yarn_cost.",".$txt_yarn_dyeing_cost.",".$txt_knitting_cost.",".$txt_df_cost.",".$txt_aop_cost.",".$txt_total_cost.",".$user_id.",'".$pc_date_time."')";
        //echo "5**insert into qc_margin_dtls (".$field_array_dtls.") values ".$data_array_dtls;die;
        if($data_array!=""){
            $rID1=sql_insert("qc_margin_mst",$field_array,$data_array,0);
        }
        if($data_array_dtls!=""){
            $rID2=sql_insert("qc_margin_dtls",$field_array_dtls,$data_array_dtls,0);
        }
         echo "10**".$rID ."&&".  $update_id;die;

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

        $field_array="fabric_cost*accessories_cost*avl_min*cm_cost*frieght_cost*lab_test_cost*mis_offer_qty*other_cost*com_cost*fob*fob_pcs*margin*margin_percent*Yarn_Yarn_Count_1*Yarn_Yarn_Type_1*Yarn_Rate_1*Yarn_Yarn_Count_2*Yarn_Yarn_Type_2*Yarn_Rate_2*Yarn_Yarn_Count_3*Yarn_Yarn_Type_3*Yarn_Rate_3*total_yarn_cost*yarn_dyeing_cost*knit_Yarn_Count_1*knit_Fabric_Type_1*knit_Rate_1*knit_Yarn_Count_2*knit_Fabric_Type_2*knit_Rate_2*knitting_cost*df_Color_Type_1*df_Color_1*df_Rate_1*df_Color_Type_2*df_Color_2*df_Rate_2*df_Color_Type_3*df_Color_3*df_Rate_3*df_cost*aop_cost*total_cost*updated_by*update_date";

        $data_array=$txt_fabric."*".$txt_accessories."*".$txt_avl_min."*".$txt_cm_dzn."*".$txt_frieght_dzn."*".$txt_lab_dzn."*".$txt_mis_offer_qty."*".$txt_other_cost_dzn."*".$txt_com_dzn."*".$txt_fob_dzn."*".$txt_fob_pcs."*".$txt_margin_per_dzn."*".$txt_margin."*".$txt_Yarn_Yarn_Count_1."*".$txt_Yarn_Yarn_Type_1."*".$txt_Yarn_Rate_1."*".$txt_Yarn_Yarn_Count_2."*".$txt_Yarn_Yarn_Type_2."*".$txt_Yarn_Rate_2."*".$txt_Yarn_Yarn_Count_3."*".$txt_Yarn_Yarn_Type_3."*".$txt_Yarn_Rate_3."*".$txt_total_yarn_cost."*".$txt_yarn_dyeing_cost."*".$txt_knit_Yarn_Count_1."*".$txt_knit_Fabric_Type_1."*".$txt_knit_Rate_1."*".$txt_knit_Yarn_Count_2."*".$txt_knit_Fabric_Type_2."*".$txt_knit_Rate_2."*".$txt_knitting_cost."*".$txt_df_Color_Type_1."*".$txt_df_Color_1."*".$txt_df_Rate_1."*".$txt_df_Color_Type_2."*".$txt_df_Color_2."*".$txt_df_Rate_2."*".$txt_df_Color_Type_3."*".$txt_df_Color_3."*".$txt_df_Rate_3."*".$txt_df_cost."*".$txt_aop_cost."*".$txt_total_cost."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
        $rID=sql_update("qc_margin_mst",$field_array,$data_array,"id",$update_id,1);

        // echo "10**".$rID . $update_id;die;
        if($db_type==0)
        {
            if($rID)
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
            if($rID )
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

        // echo "10**".$rID . $update_id;die;
        if($db_type==0)
        {
            if($rID)
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
            if($rID)
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

function sql_updates($strTable,$arrUpdateFields,$arrUpdateValues,$arrRefFields,$arrRefValues,$commit)
{

    $strQuery = "UPDATE ".$strTable." SET ";
    $arrUpdateFields=explode("*",$arrUpdateFields);
    $arrUpdateValues=explode("*",$arrUpdateValues);

    if(count($arrUpdateFields)!=count($arrUpdateValues)){
        return "0";
    }

    if(is_array($arrUpdateFields))
    {
        $arrayUpdate = array_combine($arrUpdateFields,$arrUpdateValues);
        $Arraysize = count($arrayUpdate);
        $i = 1;
        foreach($arrayUpdate as $key=>$value):
            $strQuery .= ($i != $Arraysize)? $key."=".$value.", ":$key."=".$value;
            $i++;
        endforeach;
    }
    else
    {
        $strQuery .= $arrUpdateFields."=".$arrUpdateValues;
    }
    $strQuery .=" WHERE ";

    $arrRefFields=explode("*",$arrRefFields);
    $arrRefValues=explode("*",$arrRefValues);
    if(is_array($arrRefFields))
    {
        $arrayRef = array_combine($arrRefFields,$arrRefValues);
        $Arraysize = count($arrayRef);
        $i = 1;
        foreach($arrayRef as $key=>$value):
            $strQuery .= ($i != $Arraysize)? $key."=".$value." AND ":$key."=".$value."";
            $i++;
        endforeach;
    }
    else
    {
        $strQuery .= $arrRefFields."=".$arrRefValues."";
    }
    echo "10**".$strQuery; die;
    global $con;
    if( strpos($strQuery, "WHERE")==false)  return "0";
    $stid =  oci_parse($con, $strQuery);
    $exestd=oci_execute($stid,OCI_NO_AUTO_COMMIT);
    if ($exestd)
        return "1";
    else
        return "0";

    die;
    if ( $commit==1 )
    {
        if (!oci_error($stid))
        {
            oci_commit($con);
            return "1";
        }
        else
        {
            oci_rollback($con);
            return "10";
        }
    }
    else
        return 1;
    die;
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
        <fieldset style="width:660px;margin-left:10px">
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
                <div style="width:660px; max-height:350px;overflow-y:scroll;" >  
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="640" class="rpt_table" id="list_view">
                <tbody>
                    <? 
                    $i=1;
                    foreach($data_array as $row)
                    {  
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$lib_yarn_count[$row[csf('yarn_count')]].'_'.$yarn_type[$row[csf('yarn_type')]].'_'.$row[csf('rate')]; ?>")' style="cursor:pointer" >
                            <td width="30"><? echo $i; ?></td>
                            <td width="140" style="word-break:break-all"><? echo $lib_sup[$row[csf('supplier_id')]]; ?></td>
                            <td width="80" style="word-break:break-all"><? echo $lib_yarn_count[$row[csf('yarn_count')]]; ?></td>
                            <td width="150" style="word-break:break-all"><? echo $composition[$row[csf('composition')]]; ?></td>
                            <td width="50" style="word-break:break-all"><? echo $row[csf('percent')]; ?></td>
                            <td width="60" style="word-break:break-all"><? echo $yarn_type[$row[csf('yarn_type')]]; ?></td>
                            <td width="50" style="word-break:break-all"><? echo $row[csf('rate')]; ?></td>
                            <td style="word-break:break-all"><? echo $row[csf('effective_date')]; ?></td>
                        </tr>
                        <? 
                        $i++; 
                    }
                    ?>
                </tbody>
            </table>
            ?>
            <input type="hidden" name="popupData" id="popupData" value="" style="width:50px">
        </fieldset>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>setFilterGrid('comp_tbl',-1);</script>
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
            $lib_sup=return_library_array("select supplier_name,id from lib_supplier", "id", "supplier_name");
            $lib_yarn_count=return_library_array( "select yarn_count,id from lib_yarn_count", "id", "yarn_count");
            $sql="select id,supplier_id,yarn_count,composition,percent,yarn_type,rate,effective_date from lib_yarn_rate where status_active=1 and is_deleted=0 order by id";
            $arr=array (0=>$lib_sup,1=>$lib_yarn_count,2=>$composition,4=>$yarn_type);
            echo  create_list_view ( "list_view", "Supplier Name,Yarn Count,Composition,Percent,Type,Rate/KG,Effective Date", "140,80,150,50,60,50","660","350",0, $sql, "js_set_value", "id,yarn_count,yarn_type,rate", "",1, "supplier_id,yarn_count,composition,0,yarn_type,0,0", $arr , "supplier_id,yarn_count,composition,percent,yarn_type,rate,effective_date", "quick_costing_margin_entry_controller",'setFilterGrid("list_view",-1);','0,0,0,1,0,2,3') ;
            ?>
            <input type="text" name="popupData" id="popupData" value="" style="width:50px">
        </fieldset>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>setFilterGrid('comp_tbl',-1);</script>
    </html>
    <?
    exit();
}

if($action=="dyeing_finishing_popup")
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
            $buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
            $company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
            $color_library_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"); 
            $sql="select id, comapny_id, const_comp, process_type_id, process_id, color_id, width_dia_id, in_house_rate, uom_id, rate_type_id ,customer_rate, buyer_id, status_active from lib_subcon_charge where status_active=1 and is_deleted=0 and rate_type_id in (3,4,7,6)";
            $data_array=sql_select($sql);
            ?>
            <table class="rpt_table" border="1" cellpadding="0" cellspacing="0" rules="all" width="660" >
                <thead>
                    <th width="30">SL</th>
                    <th width="60">Com.</th>
                    <th width="70">Const. Compo.</th>
                    <th width="70">Process Type</th>
                    <th width="70">Process Name</th>
                    <th width="50">Color</th>
                    <th width="40">Width / Dia type</th>
                    <th width="40">In House Rate</th>
                    <th width="60">UOM</th>
                    <th width="50">Rate type</th>
                    <th width="50">Cust. Rate</th>
                    <th>Buyer</th>
                </thead>
                </table>
                <div style="width:660px; max-height:350px;overflow-y:scroll;" >  
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="640" class="rpt_table" id="list_view">
                <tbody>
                    <? 
                    $i=1;
                    foreach($data_array as $row)
                    {  
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                        //comapny_id,const_comp,process_type_id,process_id,color_id,width_dia_id,in_house_rate,uom_id,rate_type_id,customer_rate,buyer_id
                        //$arr=array (0=>$company_arr,2=>$process_type,3=>$conversion_cost_head_array,4=>$color_library_arr,5=>$fabric_typee,7=>$unit_of_measurement,8=>$production_process,10=>$buyer_arr);
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick='js_set_value("<? echo $row[csf('id')].'_'.$color_library_arr[$row[csf('color_id')]].'_'.$row[csf('customer_rate')]; ?>")' style="cursor:pointer" >
                            <td width="30"><? echo $i; ?></td>
                            <td width="60" style="word-break:break-all"><? echo $company_arr[$row[csf('comapny_id')]]; ?></td>
                            <td width="70" style="word-break:break-all"><? echo $row[csf('const_comp')]; ?></td>
                            <td width="70" style="word-break:break-all"><? echo $process_type[$row[csf('process_type_id')]]; ?></td>
                            <td width="70" style="word-break:break-all"><? echo $conversion_cost_head_array[$row[csf('process_id')]]; ?></td>
                            <td width="50" style="word-break:break-all"><? echo $color_library_arr[$row[csf('color_id')]]; ?></td>
                            <td width="40" style="word-break:break-all"><? echo $fabric_typee[$row[csf('width_dia_id')]]; ?></td>
                            <td width="40" style="word-break:break-all"><? echo $row[csf('in_house_rate')]; ?></td>
                            <td width="60" style="word-break:break-all"><? echo $unit_of_measurement[$row[csf('uom_id')]]; ?></td>
                            <td width="50" style="word-break:break-all"><? echo $production_process[$row[csf('rate_type_id')]]; ?></td>
                            <td width="50" style="word-break:break-all"><? echo $row[csf('customer_rate')]; ?></td>
                            <td style="word-break:break-all"><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></td>
                        </tr>
                        <? 
                        $i++; 
                    }
                    ?>
                </tbody>
            </table>
            ?>
            <input type="hidden" name="popupData" id="popupData" value="" style="width:50px">
        </fieldset>
    </body>           
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>setFilterGrid('comp_tbl',-1);</script>
    </html>
    <?
    exit();
}
?>