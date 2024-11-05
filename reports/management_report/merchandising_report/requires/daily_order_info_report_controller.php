<?php
/*-------------------------------------------- Comments -----------------------
Purpose			: 	This file for daily order entry report info.
Functionality	:	
JS Functions	:
Created by		:	Md. Saidul Islam Reza 
Creation date 	: 	24-04-2016
Updated by 		: 		
Update date		: 		   
QC Performed BY	:		
QC Date			:	
Comments		:
*/

session_start();
include('../../../../includes/common.php');

extract($_REQUEST);
 
$pc_time= add_time(date("H:i:s",time()),360);  
$pc_date = date("Y-m-d",strtotime(add_time(date("H:i:s",time()),360)));

$permission=$_SESSION['page_permission'];

//---------------------------------------------------- Start

if ($action=="load_drop_down_buyer")
{
	

	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by  buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );     	 
	exit();
}


if ($action=="report_generate")
{	
	
	
	if(str_replace("'","",$cbo_company_name)==0 || str_replace("'","",$cbo_company_name)=="") $company_name="";else $company_name = "and a.company_name=$cbo_company_name";
	
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_cond="";
		}
		else
		{
			$buyer_id_cond="";$buyer_id_cond2="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_name=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
		$buyer_id_cond2=" and a.buyer_id=$cbo_buyer_name";//.str_replace("'","",$cbo_buyer_name)
	}
	
	$search_text='';
	if(str_replace("'","",$txt_style_ref)!=""){$search_text = " and a.style_ref_no like '%".str_replace("'","",$txt_style_ref)."%'";}
	if(str_replace("'","",$txt_job_no)!=""){$search_text .= " and a.job_no like '%".str_replace("'","",$txt_job_no)."'";}
	if(str_replace("'","",$txt_order_no)!=""){$search_text .= " and b.po_number like '%".str_replace("'","",$txt_order_no)."%'";}
	if(str_replace("'","",$cbo_order_status)!=0){$search_text .= " and b.is_confirmed = '".str_replace("'","",$cbo_order_status)."'";}
	
	
	
	$txt_date_from=str_replace("'","",trim($txt_date_from));
	$txt_date_to=str_replace("'","",trim($txt_date_to));
	if($txt_date_from!="" || $txt_date_to!=""){
		if($db_type==0){
			$start_date = date('Y-m-d H:i:s',strtotime($txt_date_from)); 
			$end_date = date("Y-m-d",strtotime($txt_date_to));
			if(str_replace("'","",$cbo_date_type)==2){
				$search_text .=" and b.insert_date between '".$start_date."' and '".$end_date." 23:59:59'";
			}
			else
			{
				$search_text .=" and b.po_received_date between '".$start_date."' and '".$end_date."'";
			}
		}
		else
		{
			
			$start_date = change_date_format(date("Y-m-d H:i:s",strtotime($txt_date_from)),'','',1);
			$end_date = change_date_format(date("Y-m-d H:i:s",strtotime($txt_date_to)),'','',1);
			if(str_replace("'","",$cbo_date_type)==2){
			$search_text.=" and b.insert_date between '".$start_date."' and '".$end_date." 11:59:59 PM'";
			}
			else
			{
				$search_text .=" and b.po_received_date between '".$start_date."' and '".$end_date."'";
			}
		
		}
	}
	
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0", "id", "buyer_name"  );
	$supplier_library = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	$user_arr = return_library_array("select id,user_name from user_passwd where valid=1","id","user_name");
	$dealing_merchant_arr = return_library_array("select id,team_member_name from lib_mkt_team_member_info where status_active =1 and is_deleted=0 order by team_member_name","id","team_member_name");
		
			
$sql="		
		select
			a.booking_no,a.company_id,a.job_no,a.po_break_down_id,a.item_category,a.fabric_source,a.is_short,a.is_approved,
			max(b.approved_date) as max_pproved_date,min(b.approved_date) as min_pproved_date,count(b.approved_date) as revised_no
		from 
			wo_booking_mst a,
			approval_history b
		where
			a.id=b.mst_id and
			a.company_id=$cbo_company_name and 
			a.booking_type=1 and 
			a.item_category in(2,3,13) and 
			b.entry_form=7 
			$buyer_id_cond2
		group by a.po_break_down_id,a.booking_no,a.company_id,a.job_no,a.item_category,a.fabric_source,a.is_short,a.is_approved
			";
			//echo $sql;	
		$sql_result = sql_select($sql);
		foreach($sql_result as $rows){
			$apprval_date[$rows[csf('po_break_down_id')]]['min']=$rows[csf('min_pproved_date')];
			$apprval_date[$rows[csf('po_break_down_id')]]['max']=$rows[csf('max_pproved_date')];
			$apprval_data[$rows[csf('po_break_down_id')]]="'".$rows[csf('is_short')]."','".$rows[csf('booking_no')]."','".$rows[csf('company_id')]."','".$rows[csf('po_break_down_id')]."','".$rows[csf('item_category')]."','".$rows[csf('fabric_source')]."','".$rows[csf('job_no')]."','".$rows[csf('is_approved')]."'";
			$apprval_date[$rows[csf('po_break_down_id')]]['revised_no']=$rows[csf('revised_no')]-1;
		}	
		unset($sql_result);
	
	
	 $sql="		
		select a.booking_no,a.booking_date,a.job_no,a.is_short ,a.booking_type,a.fabric_source,a.is_approved,a.item_category from wo_booking_mst a
		where
			a.company_id=$cbo_company_name and 
			a.booking_type in(1,4) and 
			a.item_category in(2,3,13) 
			$buyer_id_cond2
			";			
		
		$sql_result = sql_select($sql);
		foreach($sql_result as $rows){
			if($rows[csf('is_short')]==1 && $rows[csf('booking_type')]==1){
				$fab_booking_data_arr[$rows[csf('job_no')]]['short_fab_date']=$rows[csf('booking_date')];
			}
			elseif($rows[csf('is_short')]==2 && $rows[csf('booking_type')]==4){
				$fab_booking_data_arr[$rows[csf('job_no')]]['smpal_fab_date']=$rows[csf('booking_date')];
			}
			$fab_booking_data_arr[$rows[csf('job_no')]]['booking_no']=$rows[csf('booking_no')];
			$fab_booking_data_arr[$rows[csf('job_no')]]['fabric_source']=$rows[csf('fabric_source')];
			$fab_booking_data_arr[$rows[csf('job_no')]]['is_approved']=$rows[csf('is_approved')];
			$fab_booking_data_arr[$rows[csf('job_no')]]['item_category']=$rows[csf('item_category')];
		}	
		unset($sql_result);
	//echo $fab_booking_data_arr['FAL-13-00223']['smpal_fab_date'];
	
	//.................................	
	$sqlQuotation="SELECT  a.id as quotation_id,a.quot_date,b.inquery_date FROM wo_price_quotation a LEFT JOIN wo_quotation_inquery b ON a.inquery_id = b.id where a.company_id=$cbo_company_name and  b.company_id=$cbo_company_name";
	$sqlQuotationResult= sql_select($sqlQuotation);
	foreach($sqlQuotationResult as $row){
		$quotation_arr[$row[csf('quotation_id')]]['quot_date']=$row[csf('quot_date')];
	}
	unset($sqlQuotationResult);
	//.........................GSD
	$sqlGSD="select id,po_job_no,insert_date,update_date,total_smv from ppl_gsd_entry_mst where company_id=$cbo_company_name and is_deleted=0 and status_active=1";
	$sqlGSDResult= sql_select($sqlGSD);
	foreach($sqlGSDResult as $row){
		$gsd_arr[$row[csf('po_job_no')]]['insert'][$row[csf('id')]]=$row[csf('insert_date')];
		$gsd_arr[$row[csf('po_job_no')]]['update']=$row[csf('update_date')];
		$gsd_arr[$row[csf('po_job_no')]]['smv']=$row[csf('total_smv')];
	}
	unset($sqlGSDResult);	
	//.........................
	$sqlCosting="select job_no,costing_date from wo_pre_cost_mst where is_deleted=0 and status_active=1";
	$sqlCostingResult= sql_select($sqlCosting);
	foreach($sqlCostingResult as $row){
		$costing_date_arr[$row[csf('job_no')]]=$row[csf('costing_date')];
	}
	unset($sqlCostingResult);
	//..............................Yarn Delivery Start Date
	$sqlYearnIssue="select c.po_breakdown_id,min(a.issue_date) as issue_date from inv_issue_master  a, inv_transaction b, order_wise_pro_details c where a.id=b.mst_id and b.id=c.trans_id and a.company_id=$cbo_company_name and a.entry_form=3 and a.item_category=1 and b.transaction_type=2 and b.receive_basis in(1,2,3) and c.trans_type=2 and c.entry_form=3 group by c.po_breakdown_id";	
	$sqlYearnIssueResult= sql_select($sqlYearnIssue);
	foreach($sqlYearnIssueResult as $row){
		$yarn_delivery_start_date_arr[$row[csf('po_breakdown_id')]]=$row[csf('issue_date')];
	}
	unset($sqlYearnIssueResult);

	//......................................		
		
		if($db_type==0){$date_diff="DATEDIFF(b.shipment_date,b.po_received_date) as  date_diff,";}
		else{$date_diff="(b.shipment_date - b.po_received_date) as  date_diff,";}
		
		$sql="SELECT $date_diff a.job_no,a.set_smv,a.set_break_down,a.order_uom,b.id,b.po_number,a.buyer_name,a.style_ref_no,a.booking_meeting_date,a.dealing_marchant,a.quotation_id,b.po_quantity,b.unit_price,b.shipment_date,b.po_received_date,b.insert_date,b.is_confirmed,b.inserted_by,b.sc_lc from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $search_text $buyer_id_cond order by a.id ";
		
		  //echo $sql;			
	$sql_result = sql_select($sql) or die(mysql_error());	
	ob_start();	
	?>
	<fieldset>
    
    
	<div style="width:2950px" align="left">	
      <!-- <span style="background:red; padding:0 7px;">&nbsp;</span> Red meaning 3 days delay.-->
        <table width="2915" border="1" rules="all"  class="rpt_table" > 
            <thead>
                <tr style="font-size:12px"> 
                <th width="35">Sl</th>
                <th width="100">Job No</th>
                <th width="100">Buyer</th>
                <th width="100">Order No</th>
                <th width="100">Style</th>
				<th width="100">SC/LC:No</th>
                <th width="100">Item</th>
                <th width="50">SMV</th>
                <th width="100">Total SMV</th>
                <th width="50">UOM</th>
                <th width="80">Unit Price</th>
                <th width="100">Value</th>
                <th width="80">Order Qty (Pcs)</th>
                <th width="80">Delivery Lead Time</th>
                <th width="80">Production Lead Time</th>
                <th width="80">Dealing Merchandiser</th>
                <th width="80">Insert By</th>
               
                <th width="80">Inquiry</th>
                <th width="80">Quotation</th>
                <th width="80">Sample Fabric Booking date</th>
                <th width="80">GSD Entry Date</th>
                <th width="80">Last Modified Date</th>
                <th width="80">GSD SMV</th>
                <th width="80">Pre-Cost</th>
                <th width="80">OPD Date</th>
                <th width="80">Booking Meeting Date</th>
                <th width="80">First Fabric Booking Approval Date</th>
                <th width="80">OPD to First Booking (Number of Days)</th>
                <th width="80">Revised NO</th>
                <th width="80">Last Fabric Booking Approval Date</th>
                <th width="80">OPD to Last Booking (Number of Days)</th>
                
                <th width="80">Yarn Delivery Start Date</th>
                <th width="80">Booking Approval to Yarn Delivery (Number of Days)</th>
                <th width="80">OPD to Yarn Delivery (Number of Days)</th>
                
                <th width="80">CPA (No, Date and KG)</th>
                <th width="80">Order Status</th>
                <th width="80">Ship Date</th>
                </tr>                            	
            </thead>
        </table>
        <div style="width:2950px; max-height:350px; overflow-y:scroll" id="report_div" > 
            <table id="report_tbl" width="2915" border="1"  class="rpt_table" rules="all">
              <tbody>
				<?php
                $i=0;
                $total_po_qty=0;
                $total_value=05;
                foreach($sql_result as $row)
                {
                    $i++;
                    $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
                        
                    $set_arr=explode('__',$row[csf('set_break_down')]);
                    $item_sting='';
                    $smv_sting='';
                    $set_sting='';
                    $smv_sum=0;
                    $set_sum=0;
                    foreach($set_arr as $set_data){
                        list($item,$set,$smv)=explode('_',$set_data);
                        if($item_sting=='')$item_sting.=$garments_item[$item];else $item_sting.=','.$garments_item[$item];
                        if($smv_sting=='')$smv_sting.=$smv;else $smv_sting.='+'.$smv;
                        if($set_sting=='')$set_sting.=$set;else $set_sting.=':'.$set;
                        $smv_sum+=$smv;
                        $set_sum+=$set;
                        
                    }
				$production_lead=datediff("d",$apprval_date[$row[csf('id')]]['min'],$row[csf('shipment_date')]);
				$opd_lead=datediff("d",$row[csf('po_received_date')],date('d-M-Y',time()));
				$min_lead=datediff("d",$row[csf('po_received_date')],$apprval_date[$row[csf('id')]]['min']);
				$max_lead=datediff("d",$row[csf('po_received_date')],$apprval_date[$row[csf('id')]]['max']);
            	
				$BookingApprovaltoYarnDelivery =datediff("d",$apprval_date[$row[csf('id')]]['min'],$yarn_delivery_start_date_arr[$row[csf('id')]]);
				$OPDtoYarnDelivery=datediff("d",$row[csf('po_received_date')],$yarn_delivery_start_date_arr[$row[csf('id')]]);
				
				$tdColorMin=$min_lead > 3 ? '#FF000' : '';
            	$tdProduction_lead=$production_lead > 4 ? '#FF000' : '';
            	$tdColorOpd=$opd_lead > 3 && $apprval_date[$row[csf('id')]]['min']=='' ? '#FF000' : '';
				
            ?>	
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="font-size:12px">
                    <td width="35" align="center"><? echo $i;?></td>
                    <td width="100"><p><? echo $row[csf('job_no')]; ?></p></td>
                    <td width="100"><p><? echo $buyer_library[$row[csf('buyer_name')]]; ?></p></td>
                    <td width="100"><p><? if($apprval_date[$row[csf('id')]]['min']){?><a href="javascript:generate_worder_report(<? echo $apprval_data[$row[csf('id')]];?>);"><? echo $row[csf('po_number')]; ?></a><? }else {echo $row[csf('po_number')]; }?></p></td>
                    <td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo $row[csf('style_ref_no')]; ?></p></td>
					<td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo $row[csf('sc_lc')]; ?></p></td>
                    <td width="100" style="word-wrap: break-word;word-break: break-all;"><p><? echo $item_sting; ?></p></td>
					<td width="50" align="right">
					<div style="word-wrap: break-word;word-break: break-all; width:50px">
						<p>
                    <? echo number_format($smv_sting,4);
                        if($row[csf('order_uom')]!=1)echo '='.number_format($smv_sum,4);
                     ?></p>
					 	</div>
                    </td>
                    <td width="100" align="right" style="word-wrap: break-word;word-break: break-all;"><p>
                        <? 
                            $tot_smv=($row[csf('set_smv')]*$row[csf('po_quantity')]); 
                            echo number_format($tot_smv,2); 
                            $grund_tot_smv+=$tot_smv; 
                        ?></p>
                    </td>
                    <td width="50" align="center" style="word-wrap: break-word;word-break: break-all;">
                        <? 
                            echo $unit_of_measurement[$row[csf('order_uom')]]; 
                            if($row[csf('order_uom')]!=1)echo '<br>('.$set_sting.')';
                        ?>
                    </td>
                    
                    <td width="80" align="right" style="word-wrap: break-word;word-break: break-all;" ><?php echo number_format($row[csf('unit_price')],2); ?></td>
                    <td width="100" align="right"><p>
                        <?php 
                            $value=$row[csf('po_quantity')]*$row[csf('unit_price')]; 
                            echo number_format($value,2);
                            $total_value+= $value;
                        ?></p>
                    </td>
                    <td width="80" align="right" style="word-wrap: break-word;word-break: break-all;"><? echo $tot_pic_qty=$set_sum*$row[csf('po_quantity')]; $total_po_qty+=$tot_pic_qty ; ?></td>
                    
                    <td width="80" align="center" style="word-wrap: break-word;word-break: break-all;"><? echo $row[csf('date_diff')]; ?></td>
                    <td width="80" align="center" bgcolor="<? echo $tdProduction_lead; ?>">
						<? echo $production_lead;?>
                    </td>
					<td width="80" style="word-wrap: break-word;word-break: break-all;"><? echo $dealing_merchant_arr[$row[csf('dealing_marchant')]]; ?></td>
                    <td width="80" style="word-wrap: break-word;word-break: break-all;"><? echo $user_arr[$row[csf('inserted_by')]]; ?></td>
					<td width="80" align="center"><? $quotation_arr[$row[csf('quotation_id')]]['quot_date'];?></td>
					<td width="80" align="center"><? echo $user_arr[$row[csf('quotation_id')]]; ?></td>
					<td width="80" align="center">
                        <a href="javascript:generate_worder_report(4,'<? echo $fab_booking_data_arr[$row[csf('job_no')]]['booking_no'];?>',<? echo $cbo_company_name;?>,<? echo $row[csf('id')];?>,<? echo $fab_booking_data_arr[$row[csf('job_no')]]['item_category'];?>,<? echo $fab_booking_data_arr[$row[csf('job_no')]]['fabric_source'];?>,'<? echo $row[csf('job_no')];?>',<? echo $fab_booking_data_arr[$row[csf('job_no')]]['is_approved'];?>)">
							<? echo change_date_format($fab_booking_data_arr[$row[csf('job_no')]]['smpal_fab_date']); ?>
                        </a>
                    </td>
					
                    <td width="80" align="center">
						<? 
							foreach($gsd_arr[$row[csf('job_no')]]['insert'] as $gsd_id=>$insert_date){
								$data_string="'".str_replace("'","",$cbo_company_name).'*'.$row[csf('job_no')].'*'.$gsd_id."'";
								echo '<a href="javascript:print_gsd_report('.$data_string.')">'.change_date_format($insert_date).'</a><br>'; 
                        }
                        ?>
                    </td>
					<td width="80" align="center"><? echo change_date_format($gsd_arr[$row[csf('job_no')]]['update']); ?></td>
					<td width="80" align="center"><? echo $gsd_arr[$row[csf('job_no')]]['smv']; ?></td>




					<td width="80" align="center"><? echo change_date_format($costing_date_arr[$row[csf('job_no')]]); ?></td>
                    <td width="80" align="center" bgcolor="<? echo $tdColorOpd; ?>"><? echo change_date_format($row[csf('po_received_date')]); ?></td>
					<td width="80" align="center"><? echo change_date_format($row[csf('booking_meeting_date')]); ?></td>
                    <td width="80" align="center"><p><? echo change_date_format($apprval_date[$row[csf('id')]]['min']); ?></p></td>
                    
                   <td width="80" align="center" bgcolor="<? echo $tdColorMin; ?>"><? echo $min_lead;?></td>
                   
                    <td width="80" align="center"><? echo ($apprval_date[$row[csf('id')]]['revised_no'] == 0) ? '' : $apprval_date[$row[csf('id')]]['revised_no'] ;?></td>
                    <td width="80" align="center"><p><? echo (change_date_format($apprval_date[$row[csf('id')]]['max']) == change_date_format($apprval_date[$row[csf('id')]]['min']) && $apprval_date[$row[csf('id')]]['revised_no'] == 0) ? '' : change_date_format($apprval_date[$row[csf('id')]]['max']); ?></p></td>
                   
                    <td width="80" align="center"><? echo ($max_lead == $min_lead && $apprval_date[$row[csf('id')]]['revised_no'] == 0 ) ? '' : $max_lead ;?></td>
                    
                    <td width="80" align="center"><? echo change_date_format($yarn_delivery_start_date_arr[$row[csf('id')]]);?></td>
                    <td width="80" align="center"><? echo $BookingApprovaltoYarnDelivery; ?></td>
                    <td width="80" align="center"><? echo $OPDtoYarnDelivery; ?></td>
                    
                    
                    <td width="80" align="center">
                        <a href="javascript:generate_worder_report(3,'<? echo $fab_booking_data_arr[$row[csf('job_no')]]['booking_no'];?>',<? echo $cbo_company_name;?>,<? echo $row[csf('id')];?>,<? echo $fab_booking_data_arr[$row[csf('job_no')]]['item_category'];?>,<? echo $fab_booking_data_arr[$row[csf('job_no')]]['fabric_source'];?>,'<? echo $row[csf('job_no')];?>',<? echo $fab_booking_data_arr[$row[csf('job_no')]]['is_approved'];?>)">
							<? echo change_date_format($fab_booking_data_arr[$row[csf('job_no')]]['short_fab_date']); ?>
                        </a>
                    </td>
                    <td width="80" align="center"><? echo $order_status[$row[csf('is_confirmed')]]; ?></td>
                    <td width="80" align="center"><? echo change_date_format($row[csf('shipment_date')]); ?></td>
                </tr>
            <?
            } 
            ?> 
         </tbody>
		</table>
        </div>
        <table width="2915" border="1" cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all">
		<tfoot>
                <tr > 
                <td width="35">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100">&nbsp;</td>
				<td width="100">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="50">Total:</td>
				<td width="100" align="right" id="tot_smv"><? echo number_format($grund_tot_smv,2); $grund_tot_smv=0;?></td>
                <td width="50">&nbsp;</td>
                <td width="80">&nbsp;</td>
				<td width="100" align="right" id="tot_val"><? echo number_format($total_value,2); $total_value=0;?></td>
				<td align="right" width="80" id="tot_po_qty"><? echo number_format($total_po_qty,2);$total_po_qty=0; ?></td>
                <td width="80">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="80">&nbsp;</td>
               
                <td width="80">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="80">&nbsp;</td>
                
                <td width="80">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="80">&nbsp;</td>
                
                <td width="80">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="80">&nbsp;</td>
                </tr>                            	
            </tfoot>
        </table>   
	</div>
	</fieldset>	
	<?
	$html = ob_get_contents();
	ob_clean();
	 
	foreach (glob($_SESSION['logic_erp']['user_id']."*.xls") as $filename) {
	@unlink($filename);
	}
	
	$name=time();
	$filename=$_SESSION['logic_erp']['user_id']."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc,$html);
	echo "$html****$filename";
	exit();	
}
?>
