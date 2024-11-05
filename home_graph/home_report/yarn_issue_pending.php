<?
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This Graph Will Create Yarn Issue Pending for home page
Functionality	:	
JS Functions	:
Created by		:	Sohel 
Creation date 	: 	01.11.2015
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');
$user_id=$_SESSION['logic_erp']['user_id'];
extract($_REQUEST);
include('../includes/common.php');
require_once('../includes/class3/class.conditions.php');
require_once('../includes/class3/class.reports.php');
require_once('../includes/class3/class.yarns.php');

echo load_html_head_contents("Yarn Issue Pending", "../", "", $popup, $unicode, $multi_select, 1);

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	$company_name= $cbo_company_name;
	$location= $cbo_location_name;
	
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$loc_name_arr=return_library_array("select id,location_name from lib_location", "id","location_name");
	$buyer_short_name_library=return_library_array( "select id, short_name from lib_buyer", "id", "short_name"  );
	$yarn_count_details=return_library_array( "select id, yarn_count from lib_yarn_count", "id", "yarn_count");

	
	$dataArrayYarn=array(); $dataArrayYarnIssue=array(); $greyPurchaseQntyArray=array();
	$sql_yarn_iss="select a.po_breakdown_id, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type, 
			sum(CASE WHEN a.entry_form ='3' THEN a.quantity ELSE 0 END) AS issue_qnty,
			sum(CASE WHEN a.entry_form ='9' THEN a.quantity ELSE 0 END) AS return_qnty
			from order_wise_pro_details a, product_details_master b where a.prod_id=b.id and b.item_category_id=1 and a.status_active=1 and a.is_deleted=0 and b.is_deleted=0 and b.status_active=1 and a.issue_purpose!=2 group by a.po_breakdown_id, b.yarn_count_id, b.yarn_comp_type1st, b.yarn_comp_percent1st, b.yarn_comp_type2nd, b.yarn_comp_percent2nd, b.yarn_type";
	$dataArrayIssue=sql_select($sql_yarn_iss);
	foreach($dataArrayIssue as $row_yarn_iss)
	{
		$dataArrayYarnIssue[$row_yarn_iss[csf('po_breakdown_id')]].=$row_yarn_iss[csf('yarn_count_id')]."**".$row_yarn_iss[csf('yarn_comp_type1st')]."**".$row_yarn_iss[csf('yarn_comp_percent1st')]."**".$row_yarn_iss[csf('yarn_comp_type2nd')]."**".$row_yarn_iss[csf('yarn_comp_percent2nd')]."**".$row_yarn_iss[csf('yarn_type')]."**".$row_yarn_iss[csf('issue_qnty')]."**".$row_yarn_iss[csf('return_qnty')].",";
	}
		
	$trans_qnty_arr=array(); $grey_receive_qnty_arr=array(); $grey_issue_qnty_arr=array();$grey_receive_return_qnty_arr=array(); $grey_issue_return_qnty_arr=array();
	$dataArrayTrans=sql_select("select po_breakdown_id, 
							sum(CASE WHEN entry_form ='2' THEN quantity ELSE 0 END) AS grey_receive,
							sum(CASE WHEN entry_form ='45' and trans_type=3 THEN quantity ELSE 0 END) AS grey_receive_return,

							sum(CASE WHEN entry_form ='16' THEN quantity ELSE 0 END) AS grey_issue,
							sum(CASE WHEN entry_form ='61' THEN quantity ELSE 0 END) AS grey_issue_roll_wise,
							sum(CASE WHEN entry_form ='51' and trans_type=4 THEN quantity ELSE 0 END) AS grey_issue_return,
							
							sum(CASE WHEN entry_form ='11' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_yarn,
							sum(CASE WHEN entry_form ='11' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_yarn,
							sum(CASE WHEN entry_form ='13' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty_knit,
							sum(CASE WHEN entry_form ='13' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty_knit
							from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form in(2,11,13,16,45,51,61) group by po_breakdown_id");
	foreach($dataArrayTrans as $row)
	{
		$trans_qnty_arr[$row[csf('po_breakdown_id')]]['yarn_trans']=$row[csf('transfer_in_qnty_yarn')]-$row[csf('transfer_out_qnty_yarn')];
		$trans_qnty_arr[$row[csf('po_breakdown_id')]]['knit_trans']=$row[csf('transfer_in_qnty_knit')]-$row[csf('transfer_out_qnty_knit')];
		
		$grey_receive_qnty_arr[$row[csf('po_breakdown_id')]]=$row[csf('grey_receive')];
		$grey_issue_qnty_arr[$row[csf('po_breakdown_id')]]=$row[csf('grey_issue')]+$row[csf('grey_issue_roll_wise')];
		
		$grey_receive_return_qnty_arr[$row[csf('po_breakdown_id')]]=$row[csf('grey_receive_return')];//add by reza;
		$grey_issue_return_qnty_arr[$row[csf('po_breakdown_id')]]=$row[csf('grey_issue_return')];//add by reza;
	}
		
	$trans_qnty_fin_arr=array(); $finish_receive_qnty_arr=array(); $finish_purchase_qnty_arr=array(); $finish_issue_qnty_arr=array(); $finish_recv_rtn_qnty_arr=array(); $finish_issue_rtn_qnty_arr=array();
	$dataArrayTrans=sql_select("select po_breakdown_id, color_id, 
							sum(CASE WHEN entry_form ='7' THEN quantity ELSE 0 END) AS finish_receive,
							sum(CASE WHEN entry_form ='66' THEN quantity ELSE 0 END) AS finish_receive_roll_wise,
							sum(CASE WHEN entry_form ='37' THEN quantity ELSE 0 END) AS finish_purchase,
							sum(CASE WHEN entry_form ='18' THEN quantity ELSE 0 END) AS finish_issue,
							sum(CASE WHEN entry_form ='71' THEN quantity ELSE 0 END) AS finish_issue_roll_wise,
							sum(CASE WHEN entry_form ='46' and trans_type=3 THEN quantity ELSE 0 END) AS recv_rtn_qnty,
							sum(CASE WHEN entry_form ='52' and trans_type=4 THEN quantity ELSE 0 END) AS iss_retn_qnty,
							sum(CASE WHEN entry_form ='15' and trans_type=5 THEN quantity ELSE 0 END) AS transfer_in_qnty,
							sum(CASE WHEN entry_form ='15' and trans_type=6 THEN quantity ELSE 0 END) AS transfer_out_qnty
							from order_wise_pro_details where status_active=1 and is_deleted=0 and entry_form in(7,15,18,37,46,52,66,71) group by po_breakdown_id, color_id");
	foreach($dataArrayTrans as $row)
	{
		$trans_qnty_fin_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]['trans']=$row[csf('transfer_in_qnty')]-$row[csf('transfer_out_qnty')];
		$finish_receive_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]=$row[csf('finish_receive')]+$row[csf('finish_receive_roll_wise')];
		$finish_issue_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]=$row[csf('finish_issue')]+$row[csf('finish_issue_roll_wise')];
		
		$finish_recv_rtn_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]=$row[csf('recv_rtn_qnty')];
		$finish_issue_rtn_qnty_arr[$row[csf('po_breakdown_id')]][$row[csf('color_id')]]=$row[csf('iss_retn_qnty')];
	}
	
	$sql_tna=sql_select("select job_no, po_number_id, task_number, task_finish_date from tna_process_mst where status_active=1 and is_deleted=0 and task_number='50'");
	$task_finish_date_arr= array();
	foreach($sql_tna as $row)
	{
		$task_finish_date_arr[$row[csf('po_number_id')]]=date("Y-m-d", strtotime($row[csf('task_finish_date')]));
	}
	//print_r($task_finish_date_arr);
	
	$sql_tna_percent=sql_select("select completion_percent from lib_tna_task where status_active=1 and is_deleted=0 and task_name='50'");
	$completion_percent=0;
	foreach($sql_tna_percent as $row)
	{
		$completion_percent=$row[csf('completion_percent')];
	}
		
	$tot_order_qnty=0; $tot_mkt_required=0; $tot_yarn_issue_qnty=0;$tot_net_trans_yarn_qnty=0; $tot_balance=0; 
	
	$buyer_name_array= array(); $order_qty_array= array(); $grey_required_array= array(); $yarn_issue_array= array(); $grey_issue_array= array(); 
	$fin_fab_Requi_array= array(); $fin_fab_recei_array= array(); $issue_to_cut_array= array(); $yarn_balance_array= array(); 
	$grey_balance_array= array(); $fin_balance_array= array(); $knitted_array=array(); $dye_qnty_array=array(); $batch_qnty_array=array();

	if($location!="" and $location!=0) $location_cond= "and a.location_name=$location "; else $location_cond="";
	$sql="select a.company_name, a.buyer_name, a.job_no_prefix_num, a.job_no, a.style_ref_no, a.gmts_item_id, a.order_uom, a.total_set_qnty as ratio, b.id as po_id, b.po_number, b.po_quantity as po_qnty, b.pub_shipment_date, b.shiping_status, b.insert_date, b.po_received_date, b.plan_cut, b.file_no, b.grouping 
	from wo_po_details_master a, wo_po_break_down b 
	where a.job_no=b.job_no_mst and a.company_name='$company_name' and b.shiping_status!=3 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $location_cond order by b.pub_shipment_date, a.job_no_prefix_num, b.id";	
	$nameArray=sql_select($sql);
	?>
    <style>
	hr
	{
		color:#666;
	}
	</style>
   <div align="center"> <h3 style="top:1px;width:98%; text-align:center;" id="accordion_h1" class="accordion_h" align="center"> Yarn Issue Pending </h3></div>
    <div align="center">
		<? if($company_name!="") echo "<b>Company : </b> ".$company_library[$company_name]; ?> 
        <? if($location!="" && $location!=0) echo " , <b>Location :</b> ".$loc_name_arr[$location]; ?>
    </div>
	<div align="center" style="height:150px;">
		 <div id="summery_div_show" align="left" style="float:left; margin-left:200px; margin-top:20px;"></div>
		 <div style="width:32%; height:150px; float:right; position:relative; margin-right:120px; margin-top:5px; border:solid 1px">
			<table style="margin-left:60px; font-size:12px">
				<tr>
					<td colspan="4">Summary Graph</td>
				</tr>
			</table>
			<canvas id="canvas" height="150" width="500"></canvas>
		</div>
	</div>
	<br />
	<div id="month_summery_div_show" align="center"></div>
	<br />
    <div align="center" style="width:100%;">
    <fieldset style="width:1340px; ">
        <table align="left" class="rpt_table" border="1" rules="all" width="1320px" cellpadding="0" cellspacing="0" id="table_header_1">
            <thead>
                 <tr>
                    <td  colspan="17" align="center"><h2>Yarn Issue Pending</h2></td>
                </tr>
                <tr>
                    <th colspan="9">Order Details</th>
                    <th colspan="8">Yarn Status</th>
                </tr>
                <tr>
                    <th width="30">SL</th>
                    <th width="100">Job Number <br><hr style="width:100%; border:1px dotted;">Order Number<br><hr style="width:100%; border:1px dotted">Buyer Name</th>
                    <th width="120">Style Ref.</th>
                    <th width="60">File No</th>
                    <th width="100">Internal Ref.</th>
                    <th width="110">Item Name</th>
                    <th width="70">Order Qnty</th>
                    <th width="70">Shipment Date</th>
                    <th width="70">PO Received Date</th>
                    
                    <th width="70">Count</th>
                    <th width="100">Composition</th>
                    <th width="70">Type</th>
                    
                    <th width="70">Required<br/><font style="font-size:9px; font-weight:100">(As Per Pre-Cost)</font></th>
                    <th width="70">Issued</th>
                    <th width="70">Net Transfer</th>
                    <th width="70">Balance<br/><font style="font-size:9px; font-weight:100">(Yarn Req-(Yarn Issue+ Net Trans))</font></th>
                    <th width="70">TNA Finish Date</font></th>
                </tr>
            </thead>
        </table>
        <div style="width:1340px; overflow-y:scroll; max-height:400px" id="scroll_body">
            <table width="1320px" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
            <?
            $res_month=array(); 
            /*$JobArr=array();
            foreach($nameArray as $result_sql_row){
                $JobArr[]=$result_sql_row[csf('job_no')];
            }*/
			$condition= new condition();
			$condition->company_name("='$company_name'");
			$condition->init();
            $yarn= new yarn($condition);
            $yarn_des_data=$yarn->getOrderCountCompositionPercentAndTypeWiseYarnQtyArray();
			//print_r($yarn_des_data);
            //$yarn_des_data_job=$yarn->getJobCountCompositionPercentAndTypeWiseYarnQtyArray();
            
			$k=1;
			$i=1; 
			foreach($nameArray as $row)
			{
				$shipment_date=date("Y-m", strtotime($row[csf('pub_shipment_date')]));	
		
				$template_id=$template_id_arr[$row[csf('po_id')]];
				
				$order_qnty_in_pcs=$row[csf('po_qnty')]*$row[csf('ratio')];
				$plan_cut_qnty=$row[csf('plan_cut')]*$row[csf('ratio')];
				$order_qty_array[$row[csf('buyer_name')]]+=$order_qnty_in_pcs;
				
				$gmts_item='';
				$gmts_item_id=explode(",",$row[csf('gmts_item_id')]);
				foreach($gmts_item_id as $item_id)
				{
					if($gmts_item=="") $gmts_item=$garments_item[$item_id]; else $gmts_item.=",".$garments_item[$item_id];
				}
				
				$dzn_qnty=0; $balance=0; $job_mkt_required=0; $yarn_issued=0;
				if($costing_per_id_library[$row[csf('job_no')]]==1) $dzn_qnty=12;
				else if($costing_per_id_library[$row[csf('job_no')]]==3) $dzn_qnty=12*2;
				else if($costing_per_id_library[$row[csf('job_no')]]==4) $dzn_qnty=12*3;
				else if($costing_per_id_library[$row[csf('job_no')]]==5) $dzn_qnty=12*4;
				else $dzn_qnty=1;
				
				$dzn_qnty=$dzn_qnty*$row[csf('ratio')];

				$yarn_data_array=array(); $mkt_required_array=array(); $yarn_desc_array_for_popup=array(); $yarn_desc_array=array(); $yarn_iss_qnty_array=array();
				$s=1;
				$yarn_descrip_data=$yarn_des_data[$row[csf('po_id')]];
				$qnty=0;
				foreach($yarn_descrip_data as $count=>$count_value)
				{
					foreach($count_value as $Composition=>$composition_value)
					{
						foreach($composition_value as $percent=>$percent_value)
						{
							foreach($percent_value as $typee=>$type_value)
							{
								//$yarnRow=explode("**",$yarnRow);
								$count_id=$count;//$yarnRow[0];
								$copm_one_id=$Composition;//$yarnRow[1];
								$percent_one=$percent;//$yarnRow[2];
								$copm_two_id=0;
								$percent_two=0;
								$type_id=$typee;//$yarnRow[5];
								$qnty=$type_value;//$yarnRow[6];
								
								$mkt_required=$qnty;//$plan_cut_qnty*($qnty/$dzn_qnty);
								$mkt_required_array[$s]=$mkt_required;
								$job_mkt_required+=round($mkt_required,2);
								
								$yarn_data_array['count'][$s]=$yarn_count_details[$count_id];
								$yarn_data_array['type'][$s]=$yarn_type[$type_id];
								
								$compos=$composition[$copm_one_id]." ".$percent_one." %"." ".$composition[$copm_two_id];
	
								$yarn_data_array['comp'][]=$compos;
								
								$yarn_desc_array[$s]=$yarn_count_details[$count_id]." ".$compos." ".$yarn_type[$type_id];
								
								$yarn_desc_for_popup=$count_id."__".$copm_one_id."__".$percent_one."__".$copm_two_id."__".$percent_two."__".$type_id;
								$yarn_desc_array_for_popup[$s]=$yarn_desc_for_popup;
								
								$s++;
							}
						}
					 }
				}
                    
				$dataYarnIssue=explode(",",substr($dataArrayYarnIssue[$row[csf('po_id')]],0,-1));
				foreach($dataYarnIssue as $yarnIssueRow)
				{
					$yarnIssueRow=explode("**",$yarnIssueRow);
					$yarn_count_id=$yarnIssueRow[0];
					$yarn_comp_type1st=$yarnIssueRow[1];
					$yarn_comp_percent1st=$yarnIssueRow[2];
					$yarn_comp_type2nd=$yarnIssueRow[3];
					$yarn_comp_percent2nd=$yarnIssueRow[4];
					$yarn_type_id=$yarnIssueRow[5];
					$issue_qnty=$yarnIssueRow[6];
					$return_qnty=$yarnIssueRow[7];
					
					if($yarn_comp_percent2nd!=0)
					{
						$compostion_not_req=$composition[$yarn_comp_type1st]." ".$yarn_comp_percent1st." % ".$composition[$yarn_comp_type2nd]." ".$yarn_comp_percent2nd." %";
					}
					else
					{
						$compostion_not_req=$composition[$yarn_comp_type1st]." ".$yarn_comp_percent1st." % ".$composition[$yarn_comp_type2nd];
					}
			
					$desc=$yarn_count_details[$yarn_count_id]." ".$compostion_not_req." ".$yarn_type[$yarn_type_id];
					
					$net_issue_qnty=$issue_qnty-$return_qnty;
					$yarn_issued+=$net_issue_qnty;
					if(!in_array($desc,$yarn_desc_array))
					{
						$yarn_iss_qnty_array['not_req']+=$net_issue_qnty;
					}
					else
					{
						$yarn_iss_qnty_array[$desc]+=$net_issue_qnty;
					}
				}
				
				$net_trans_yarn=$trans_qnty_arr[$row[csf('po_id')]]['yarn_trans'];
				$balance=$job_mkt_required-($yarn_issued+$net_trans_yarn);
				
				$yarn_issued_trans_yarn=number_format($yarn_issued+$net_trans_yarn,2,'.','');
				
				//echo $row[csf('job_no')]."==".$job_mkt_required."==".$yarn_issued_trans_yarn."==".$completion_percent;
				$chk_balence=(round($job_mkt_required,2)*$completion_percent)/100;
				
				//echo "==".number_format($chk_balence,2,'.','')."==".$yarn_issued_trans_yarn;
				//if(number_format($balance,2)>0)
				if(number_format($chk_balence,2,'.','')>=$yarn_issued_trans_yarn and number_format($balance,2)>0)
				{
					$tot_order_qnty+=$order_qnty_in_pcs;
					$tot_mkt_required+=$job_mkt_required;
					$tot_yarn_issue_qnty+=$yarn_issued;
					$tot_net_trans_yarn_qnty+=$net_trans_yarn;
					$tot_balance+=$balance;
					
					$res_month[$shipment_date][$row[csf('buyer_name')]]['required']+=$job_mkt_required;
                    $res_month[$shipment_date][$row[csf('buyer_name')]]['issued']+=($yarn_issued+$net_trans_yarn);
                    $res_month[$shipment_date][$row[csf('buyer_name')]]['balance']+=$balance;
					if($task_finish_date_arr[$row[csf('po_id')]]==""){ $tnacolor=""; }
                    else if($task_finish_date_arr[$row[csf('po_id')]]<$date){ $tnacolor="#FF0000"; } else { $tnacolor=""; }
                        ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
                            <td width="30" bgcolor="<? echo $tnacolor; ?>"><? echo $display_font_color.$i.$font_end; ?></td>
                            <td width="100" align="center" valign="middle">
								<p><? echo $display_font_color.$row[csf('job_no')].$font_end; ?> </p>
                                <br>
                                <p><hr style="width:100%; border:1px dotted"></p>
                                <p><? echo $display_font_color.$row[csf('po_number')].$font_end;  ?></p>
                                <br>
                                <p><hr style="width:100%; border:1px dotted"></p>
                                <p><? echo $display_font_color.$buyer_short_name_library[$row[csf('buyer_name')]].$font_end; ?></p>
                            </td>
                            <td width="120"><p><? echo $display_font_color.$row[csf('style_ref_no')].$font_end; ?></p></td>
                            <td width="60"><p><? echo $display_font_color.$row[csf('file_no')].$font_end; ?></p></td>
                            <td width="100"><p><? echo $display_font_color.$row[csf('grouping')].$font_end; ?></p></td>
                            <td width="110"><p><? echo $display_font_color.$gmts_item.$font_end; ?></p></td>
                            <td width="70" align="right"><p><? echo number_format($order_qnty_in_pcs,0); ?></p></td>
                            <td width="70" align="center"><p><? echo $display_font_color.change_date_format($row[csf('pub_shipment_date')]).$font_end; ?></p></td>
                            <td width="70" align="center"><p><? echo $display_font_color.change_date_format($row[csf('po_received_date')]).$font_end; ?></p></td>
                            <td width="70" align="center">
                                <p>
                                 <? 
                                     $html.="<td>"; $d=1;
                                     foreach($yarn_data_array['count'] as $yarn_count_value)
                                     {
                                        if($d!=1)
                                        {
                                            echo $display_font_color."<hr/>".$font_end;
                                            if($z==1) $html.="<hr/>";
                                        }
                                        
                                        echo $display_font_color.$yarn_count_value.$font_end;
                                        if($z==1) $html.=$yarn_count_value;
                                        $d++;
                                     }
                                ?>
                                </p>
                            </td>
                            <td width="100" align="center" style="word-wrap:break-word">
                                <p>
                                <? 
                                     $d=1;
                                     foreach($yarn_data_array['comp'] as $yarn_composition_value)
                                     {
                                        if($d!=1)
                                        {
                                            echo "<p>".$display_font_color."</p><hr/><p>".$font_end."</p>";
                                        }
                                        echo "<p>".$display_font_color.$yarn_composition_value.$font_end."</p>";
                                     	$d++;
                                     }
                                ?>
                                </p> 
                            </td>
                            <td width="70" align="center">
                                <p>
                                <? 
                                 $d=1;
                                 foreach($yarn_data_array['type'] as $yarn_type_value)
                                 {
                                    if($d!=1)
                                    {
                                        echo $display_font_color."<hr/>".$font_end;
                                        if($z==1) $html.="<hr/>";
                                    }
                                    
                                    echo $display_font_color.$yarn_type_value.$font_end; 
                                    if($z==1) $html.=$yarn_type_value;
                                 $d++;
                                 }
                                ?>
                                </p>
                            </td>
                            <td width="70" align="right">
                                <? 
                                    echo "<font color='$bgcolor' style='display:none'>"."sss".number_format(array_sum($mkt_required_array),2,'.','')."</font>\n";
                                    $d=1; 
                                    foreach($mkt_required_array as $mkt_required_value)
                                    {
                                        if($d!=1)
                                        {
                                            echo "<hr/>";
                                        }
                                        
                                        $yarn_desc_for_popup_req=explode("__",$yarn_desc_array_for_popup[$d]);
                                        echo number_format($mkt_required_value,2);
                                        $d++;
                                    }
									//echo "==".number_format($chk_balence,2,'.','')."==".$yarn_issued_trans_yarn;
                                ?>
                            </td>
                            <td width="70" align="right" bgcolor="<? echo $discrepancy_td_color; ?>">
                                <? 
                                    echo "<font color='$bgcolor' style='display:none'>".number_format($yarn_issued,2,'.','')."</font>\n";
                                    $d=1; 
                                    foreach($yarn_desc_array as $yarn_desc)
                                    {
                                        if($d!=1)
                                        {
                                            echo "<hr/>";
                                        }
                                        
                                        $yarn_iss_qnty=$yarn_iss_qnty_array[$yarn_desc];
                                        $yarn_desc_for_popup=explode("__",$yarn_desc_array_for_popup[$d]);
                                        echo number_format($yarn_iss_qnty,2);
                                        $d++;
                                    }
                                    
                                    if($d!=1)
                                    {
                                        echo "<hr/>";
                                    }
                                    $yarn_desc=join(",",$yarn_desc_array);
                                    $iss_qnty_not_req=$yarn_iss_qnty_array['not_req'];
                                    
                                    echo number_format($iss_qnty_not_req,2);
                                    ?>
                            </td>
                            <td width="70" align="right">
                                <? 
                                echo number_format($net_trans_yarn,2); 
                                ?>
                            </td>
                            <td width="70" align="right">
                                <?
                                echo number_format($balance,2);
                                ?>
                            </td>
                             <td width="70" align="center"> <?  echo change_date_format($task_finish_date_arr[$row[csf('po_id')]]) ?></td>
                        </tr>
                    <?	
                    $z++;
                    $k++;
                    $i++;
				}//end if condition for balence chk
             }//end foreach
            ?>
    		</table>
		</div>
<table align="left" width="1320px" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
    <tfoot>
        <tr>
            <th width="30"></th>
            <th width="100"></th>
            <th width="120"></th>
            <th width="60"></th>
            <th width="100"></th>
            <th width="110">Total</th>
            <th width="70" id="tot_order_qnty"><p><? echo number_format($tot_order_qnty,0); ?></p></th>
            <th width="70"></th>
            <th width="70"></th>
            
            <th width="70"></th>
            <th width="100"></th>
            <th width="70"></th>
            
            <th width="70" id="value_tot_yarn_rec"><p><? echo number_format($tot_mkt_required,2); ?></p></th>
            <th width="70" id="value_tot_yarn_issue"><p><? echo number_format($tot_yarn_issue_qnty,2); ?></p></th>
            <th width="70" id="value_tot_net_trans_yarn"><p><? echo number_format($tot_net_trans_yarn_qnty,2); ?></p></th>
            <th width="70" id="value_tot_yarn_balance"><p><? echo number_format($tot_balance,2); ?></p></th>
            <th width="70"></th>
        </tr>
    </tfoot>
</table>
<br />
<br />
    </fieldset>
    
    </div>

 <div id="summery_div" style="display:none; visibility:hidden;">
  <fieldset style="width:500px;">
    <table width="500" class="rpt_table" border="1" rules="all" align="center">
        <thead>
            <tr>
                <th colspan="3">Summary</th>
            </tr>
            <tr>
                <th width="200">Particulars</th>
                <th width="170">Total Qnty</th>
                <th>% On Required</th>
            </tr>
        </thead>
        <tbody>
            <tr bgcolor="#FFFFFF" onclick="change_color('tr3_1','#FFFFFF')" id="tr3_1">
               <td>Total Yarn Required</td>
               <td align="right"><? echo number_format($tot_mkt_required,2); ?></td>
               <td>&nbsp;</td>
            </tr>
            <tr bgcolor="#FFFFFF" onclick="change_color('tr3_2','#FFFFFF')" id="tr3_2">
               <td>Total Yarn Issued To Knitting</td>
               <td align="right"><? echo number_format(($tot_yarn_issue_qnty+$tot_net_trans_yarn_qnty),2); ?></td>
               <td align="right"><? echo number_format(($tot_yarn_issue_qnty+$tot_net_trans_yarn_qnty)/$tot_mkt_required*100,2)."%"; ?></td>
            </tr>
            <tr bgcolor="#E0E0E0" style="font-weight:bold" onclick="change_color('tr3_3','#CFF')" id="tr3_3">
               <td>Total Yarn Balance</td>
               <td align="right"><? echo number_format($tot_mkt_required-($tot_yarn_issue_qnty+$tot_net_trans_yarn_qnty),2); ?></td>
               <td align="right"><? echo number_format(((($tot_mkt_required-($tot_yarn_issue_qnty+$tot_net_trans_yarn_qnty))/$tot_mkt_required)*100),2)."%"; ?></td>
            </tr>
        </tbody>
   </table>
   </fieldset>
   </div>
	<?
	$tot_mkt_required=round($tot_mkt_required,2);
	$issue_qnty=round($tot_yarn_issue_qnty+$tot_net_trans_yarn_qnty,2);
	$balence=round($tot_mkt_required-($tot_yarn_issue_qnty+$tot_net_trans_yarn_qnty),2);
   
	$item_arr=array(0=>"Required",1=>"Issued",2=>"Balance");
	$val_arr=array(0=>$tot_mkt_required,1=>$issue_qnty,2=>$balence);
	
	foreach($item_arr as $item=>$res)
	{
		$item_data[]=$res;
		$item_val[]=$val_arr[$item];
	}
    //print_r($item_data);
    $item_data= json_encode($item_data);
    $item_val= json_encode($item_val);
    ?>

<div id="month_summery_div" style="display:none; visibility:hidden;"> 
    <fieldset style="width:1200px;">
        <div align="center" style="width:100%; font-size:18px;"><b>Month Wise Yarn Issue Pending Summary</b></div> 
        <table width="1190" align="center">
            <tr>
				<?
				$s=0;	$sum_req="";$sum_iss="";$sum_bal="";
                foreach( $res_month as $month_id=>$buyer_arr)
                {
					if($s%3==0) $tr="</tr><tr>"; else $tr=""; echo $tr;
					?>
					<td valign="top">
                        <div style="width:380px">
                            <table width="380" class="rpt_table" border="1" rules="all" align="center">
                                <thead>
                                    <tr>
                                        <th colspan="5" align="center" style="font-size:16px;"> Total Summary  <? echo $month_name=date("F",strtotime($month_id)).", ".date("Y",strtotime($month_id)); ?> </th>
                                    </tr>
                                    <tr>
                                        <th width="30">SL</th>
                                        <th width="90">Buyer Name</th>
                                        <th width="90">Required</th>
                                        <th width="80">Issued</th>
                                        <th width="">Balance</th>
                                    </tr>
                            	</thead>
								<?
                                $d=1; 
                                foreach( $buyer_arr as $buyer_id=>$value)
                                {
									//$no_of_job=count($job_no);
									if ($d%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
									<tr bgcolor="<? echo $bgcolor; ?>">
										<td><? echo $d; ?></td>
										<td><? echo $buyer_short_name_library[$buyer_id]; ?></td>
										<td align="right"><?  echo number_format($value['required'],2); $month_wise_required +=$value['required'];?></td>
										<td align="right"><?  echo number_format($value['issued'],2); $month_wise_issued +=$value['issued'];?></td>
										<td align="right"><?  echo number_format($value['balance'],2); $month_wise_balance +=$value['balance'];?></td>
									</tr>
									<?
									$d++;
                                }
                                ?>
                                <tfoot>
                                    <tr>
                                        <th colspan="2" align="right">Total</th>
                                        <th align="right"><? echo number_format($month_wise_required,2); $sum_req +=$month_wise_required; //echo "==".$sum_req; ?></th>
                                        <th align="right"><? echo number_format($month_wise_issued,2); $sum_iss +=$month_wise_issued; //echo "==".$sum_iss; ?></th>
                                        <th align="right"><? echo number_format($month_wise_balance,2); $sum_bal +=$month_wise_balance; //echo "==".$sum_bal; ?></th>												
                                    </tr>
                                </tfoot>
                            </table>
                        </div>  
					</td> 
					<?
					$month_wise_required=""; $month_wise_issued=""; $month_wise_balance="";
					$s++;
                }
                ?>
            </tr>
        </table>
    </fieldset> 
    </div>
    <script src="../Chart.js-master/Chart.js"></script>
	<script>
		var barChartData = {
			labels : <? echo $item_data; ?>,
			datasets : [
				{
					fillColor : "green",
					strokeColor : "rgba(0,128,0)",
					highlightFill: "rgb(0,128,0)",
					highlightStroke: "rgba(0,128,0)",
					data : <? echo $item_val; ?>
				}
			]
		}
		
		var ctx = document.getElementById("canvas").getContext("2d");
		window.myBar = new Chart(ctx).Bar(barChartData, {
		responsive : true
		});
   
    document.getElementById('summery_div_show').innerHTML=document.getElementById('summery_div').innerHTML;
    document.getElementById('summery_div').innerHTML="";
	
	document.getElementById('month_summery_div_show').innerHTML=document.getElementById('month_summery_div').innerHTML;
    document.getElementById('month_summery_div').innerHTML="";
    
    function change_color(v_id,e_color)
    {
		if (document.getElementById(v_id).bgColor=="#33CC00")
		{
			document.getElementById(v_id).bgColor=e_color;
		}
		else
		{
			document.getElementById(v_id).bgColor="#33CC00";
		}
    }
	
    </script> 
	<?
	exit();
}


?>