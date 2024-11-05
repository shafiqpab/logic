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
	echo create_drop_down( "cbo_buyer_name", 120, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (1,3,21,90)) group by buy.id,buy.buyer_name order by buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );     	 
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
			$buyer_id_cond=""; $buyer_id_cond2="";
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
	if($txt_date_from!="" || $txt_date_to!="")
	{
		if($db_type==0)
		{
			$start_date = date('Y-m-d H:i:s',strtotime($txt_date_from)); 
			$end_date = date("Y-m-d",strtotime($txt_date_to));
			if(str_replace("'","",$cbo_date_type)==2)
			{
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
	
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer where  status_active=1 and is_deleted=0", "id", "buyer_name");
	$company_library=return_library_array( "select id, company_name from lib_company where  status_active=1 and is_deleted=0", "id", "company_name");
	$location_library=return_library_array( "select id, location_name from lib_location where  status_active=1 and is_deleted=0", "id", "location_name");

	$supplier_library = return_library_array("select id,supplier_name from lib_supplier where status_active=1 and is_deleted=0","id","supplier_name");
	$user_arr = return_library_array("select id,user_name from user_passwd where valid=1","id","user_name");
	$team_name_arr=return_library_array( "select id, team_name from lib_marketing_team where status_active=1 and is_deleted=0", "id", "team_name");
	$team_member_arr=return_library_array( "select id, team_member_name from lib_mkt_team_member_info where status_active=1 and is_deleted=0", "id", "team_member_name");
    $price_quotation = return_library_array( "select id, insert_date from wo_price_quotation where status_active=1 and is_deleted=0", "id", "insert_date");
    $pre_cost = return_library_array( "select job_no, insert_date from wo_pre_cost_mst where status_active=1 and is_deleted=0", "job_no", "insert_date");
    $pre_cost_efecency = return_library_array( "select job_no, SEW_EFFI_PERCENT from wo_pre_cost_mst where status_active=1 and is_deleted=0", "job_no", "SEW_EFFI_PERCENT");
    $quotation_app = sql_select("Select a.mst_id, max(a.approved_date) as max_pproved_date, min(a.approved_date) as min_pproved_date from approval_history a join wo_price_quotation b on a.mst_id =b.id where a.entry_form=10 and a.un_approved_date is null and b.company_id = $cbo_company_name group by mst_id");
    $quotation_app_info = array();
    foreach ($quotation_app as $key => $row) {
        $quotation_app_info[$row[csf('mst_id')]]['min_approved'] = $row[csf('min_pproved_date')];
    }

    $precost_app = sql_select("Select a.mst_id,b.job_no, max(a.approved_date) as max_pproved_date, min(a.approved_date) as min_pproved_date from approval_history a join wo_pre_cost_mst b on a.mst_id =b.id where a.entry_form=15 and a.un_approved_date is null group by a.mst_id,b.job_no");
    $precost_app_info = array();
    foreach ($precost_app as $key => $row) {
        $precost_app_info[$row[csf('job_no')]]['min_approved'] = $row[csf('min_pproved_date')];
    }




		$sql_qk="select a.insert_date,min(c.approved_date) as min_approved_date,d.job_id from qc_mst a, qc_tot_cost_summary b,approval_history c,qc_confirm_mst d where c.mst_id=a.id and c.entry_form=36 and  a.qc_no=b.mst_id and b.mst_id=d.cost_sheet_id and a.status_active=1 and a.is_deleted=0 and a.approved in (1,3) and d.ready_to_approve=1 and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.current_approval_status=1  group by a.insert_date,d.job_id";
		$sql_qk_data = sql_select($sql_qk);// or die(mysql_error() //and a.temp_id=$cbo_company_name
		foreach($sql_qk_data as $row)
		{
			$quick_costingArr[$row[csf('job_id')]]['min_approved_date']=$row[csf('min_approved_date')];
			$quick_costingArr[$row[csf('job_id')]]['insert_date']=$row[csf('insert_date')];
		}
		
		
			
		
	$sql_book="select a.booking_no, a.company_id, a.job_no, c.po_break_down_id, a.item_category, a.fabric_source, a.is_short, a.is_approved, max(b.approved_date) as max_pproved_date, min(b.approved_date) as min_pproved_date, count(b.approved_date) as revised_no
	from 
		wo_booking_mst a, approval_history b, wo_booking_dtls c
	where
		a.id=b.mst_id and a.booking_no=c.booking_no and a.company_id = $cbo_company_name and a.booking_type=1 and a.item_category in(2,3,13) and b.entry_form=7 $buyer_id_cond2
	group by c.po_break_down_id, a.booking_no, a.company_id, a.job_no, a.item_category, a.fabric_source, a.is_short, a.is_approved ";	
	//echo $sql_book;
	$sql_result_data = sql_select($sql_book);// or die(mysql_error()
	foreach($sql_result_data as $rows)
	{
		$apprval_date[$rows[csf('po_break_down_id')]]['min']=$rows[csf('min_pproved_date')];
		$apprval_date[$rows[csf('po_break_down_id')]]['max']=$rows[csf('max_pproved_date')];
		$apprval_data[$rows[csf('po_break_down_id')]]="'".$rows[csf('is_short')]."','".$rows[csf('booking_no')]."','".$rows[csf('company_id')]."','".$rows[csf('po_break_down_id')]."','".$rows[csf('item_category')]."','".$rows[csf('fabric_source')]."','".$rows[csf('job_no')]."','".$rows[csf('is_approved')]."'";
		$apprval_date[$rows[csf('po_break_down_id')]]['revised_no']=$rows[csf('revised_no')]-1;
	}	
	unset($sql_result_data);

	if($db_type==0) $date_diff="DATEDIFF(b.shipment_date,b.po_received_date)"; else if($db_type==2) $date_diff="(b.shipment_date - b.po_received_date)";
	
	$sql="select $date_diff as date_diff, a.id as job_id,a.company_name,a.location_name,a.product_category as prod_cate,a.job_no, a.design_source_id, a.set_smv, a.set_break_down, a.order_uom, a.team_leader, a.dealing_marchant, a.quotation_id, b.id, b.po_number, a.buyer_name, a.style_ref_no, b.po_quantity, b.grouping as ref_no,b.unit_price, b.shipment_date, b.po_received_date, b.insert_date, b.is_confirmed, b.inserted_by from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst $company_name and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 $search_text $buyer_id_cond ";
		
		// echo $sql;			
	$sql_result = sql_select($sql) or die(mysql_error());
	foreach($sql_result as $row)
	{
		$quot_id_arr[$row[csf('quotation_id')]]=$row[csf('quotation_id')];
	}
	
	 $sql_spot_mrg="select a.qc_no,b.margin_percent,b.margin from  qc_mst a,qc_margin_mst b where a.qc_no=b.qc_no and a.status_active=1 and a.is_deleted=0  and b.status_active=1 and b.is_deleted=0 ".where_con_using_array($quot_id_arr,0,'a.qc_no')."";
		$sql_spot_data = sql_select($sql_spot_mrg);// or die(mysql_error() //and a.temp_id=$cbo_company_name
		foreach($sql_spot_data as $row)
		{
			$quick_mergin_costingArr[$row[csf('qc_no')]]['margin']=$row[csf('margin')];
			$quick_mergin_costingArr[$row[csf('qc_no')]]['margin_per']=$row[csf('margin_percent')];
		}
		unset($sql_spot_data);
		


        $dbData=sql_select("select id,item_name,commercial_name,product_category_id,product_type_id,status_active,standard_smv,efficiency,is_default from lib_garment_item  ");
            foreach ($dbData as $inf) 
            {
                
                    $prod_type_arr[$inf[csf("item_name")]]['prod_type']=$product_types[$inf[csf("product_type_id")]];
                    $prod_type_arr[$inf[csf("item_name")]][$inf[csf("product_category_id")]]['effcency']=$inf[csf("efficiency")];
            }
            unset($dbData);
	ob_start();	
	?>
	<fieldset>
	<div style="width:3520px" align="left">	
       <span style="background:red; padding:07px;">&nbsp;</span> Red meaning 2 days delay.
        <table width="3510" border="1" rules="all" class="rpt_table" > 
            <thead>
                <tr style="font-size:12px"> 
                    <th width="30">SL</th>
                    <th width="120">Company Name </th>
                    <th width="100">Location </th>
                    <th width="100">Team Name</th>
                      
                    <th width="100">Team Member</th>
                    <th width="100">Buyer</th> c  
                    <th width="100">Style</th>
                    <th width="100">Order No</th>
                    <th width="100">Job No</th>
                    <th width="100">Design Source</th>
                    <th width="70">Inter. Ref.</th>
                    <th width="130">Item</th>
                    <th width="100">Prod.Category</th>
                    <th width="100">Prod.Type</th>
                    <th width="70">Insert Date</th>
                    <th width="70" title="Po Received Date">OPD Date</th>
                    <th width="70">Ship Date</th>                    
                    <th width="70" title="First Fabric Booking Approval">First Booking  Date</th>
                    <th width="70" title="Last Fabric Booking Approval">Last Booking  Date</th>
                    <th width="70" style="word-break:break-all">OPD to First Booking (Number of Days)</th>
                    <th width="70" style="word-break:break-all">OPD to Last Booking (Number of Days)</th>    
                    <th width="70">Price Quotation No</th>
                    
                    <th width="70">QC Margin</th>
                    <th width="70">QC Margin %</th>
                      
                    <th width="70" title="Price Quotation Insert Date">Quotation Insert Date</th>
                    <th width="70">Quotation 1st Approved Date</th>
                    <th width="70">Pre-Costing Insert Date</th>
                    <th width="70">Pre-Costing 1st Approved Date</th>
                    <th width="50">Revised NO</th>
                    <th width="70" style="word-break:break-all">Manufacturing  Lead Time (days): OPD to Ship date</th>
                    <th width="70" style="word-break:break-all">Production Lead Time (days): First App to Ship</th>
                    <th width="50">SMV</th>
                    <th width="70">Sewing Effi%</th>
                    <th width="100">Order Qty.</th>
                    <th width="50">UOM</th>
                    <th width="100">Order Qty (Pcs)</th>
                    <th width="100">Total SMV</th>
                    <th width="70">Unit Price</th>
                    <th width="120">Value</th>
                    <th width="80">Order Status</th>
                    <th>Insert By</th>
                </tr>                            	
            </thead>
        </table>
        <div style="width:3530px; max-height:300px; overflow-y:scroll" id="report_div" > 
            <table id="report_tbl" width="3510" border="1" class="rpt_table" rules="all">
              <tbody>
				<?php




                $i=1; //$total_po_qty=0; $total_value=0; $total_po_qtypcs=0; $grund_tot_smv=0;
                foreach($sql_result as $row)
                {
                    $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
                        
                    $set_arr=explode('__',$row[csf('set_break_down')]);
                    $item_sting=''; $smv_sting=''; $set_sting=''; $smv_sum=0; $set_sum=0;
                    foreach($set_arr as $set_data)
					{
                        list($item,$set,$smv)=explode('_',$set_data);
                        if($item_sting=='')$item_sting.=$garments_item[$item];else $item_sting.=','.$garments_item[$item];
                        if($smv_sting=='')$smv_sting.=$smv;else $smv_sting.='+'.$smv;
                        if($set_sting=='')$set_sting.=$set;else $set_sting.=':'.$set;
                        $smv_sum+=$smv;
                        $set_sum+=$set;
                    }
					$opd_lead=datediff("d",$row[csf('po_received_date')],date('d-M-Y',time()));
					$min_lead=datediff("d",$row[csf('po_received_date')],$apprval_date[$row[csf('id')]]['min']);
					$max_lead=datediff("d",$row[csf('po_received_date')],$apprval_date[$row[csf('id')]]['max']);
					$tdColorMin=$min_lead > 2 ? '#FF000' : '';
					//$tdColorMax=$max_lead > 4 ? '#FF000' : '';
					$tdColorOpd=$opd_lead > 2 && $apprval_date[$row[csf('id')]]['min']=='' ? '#FF000' : '';
					$min_approved_date=$quick_costingArr[$row[csf('job_id')]]['min_approved_date'];
					$quick_insert_date=$quick_costingArr[$row[csf('job_id')]]['insert_date'];
					$margin=$quick_mergin_costingArr[$row[csf('quotation_id')]]['margin'];
					$margin_per=$quick_mergin_costingArr[$row[csf('quotation_id')]]['margin_per'];
					//echo $min_approved_date.'='.$quick_insert_date;
				?>	
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" style="font-size:12px">
                    <td width="30" align="center"><? echo $i;?></td>
                    <td width="120" style="word-break:break-all"><? echo $company_library[$row[csf('company_name')]]; ?></td>
                   <td width="100" style="word-break:break-all"><? echo $location_library[$row[csf('location_name')]]; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $team_name_arr[$row[csf('team_leader')]]; ?></td>
                          
                    <td width="100" style="word-break:break-all"><? echo $team_member_arr[$row[csf('dealing_marchant')]]; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $buyer_library[$row[csf('buyer_name')]]; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $row[csf('style_ref_no')]; ?></td>
                    <td width="100" style="word-break:break-all"><? if($apprval_date[$row[csf('id')]]['min']){?><a href="javascript:generate_worder_report(<? echo $apprval_data[$row[csf('id')]];?>);"><? echo $row[csf('po_number')]; ?></a><? }else {echo $row[csf('po_number')]; }?></td>
                    <td width="100" style="word-break:break-all"><? echo $row[csf('job_no')]; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $design_source_arr[$row[csf('design_source_id')]]; ?></td>
                    <td width="70" style="word-break:break-all"><? echo $row[csf('ref_no')]; ?></td>
                    <td width="130" style="word-break:break-all"><? echo $item_sting; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $product_category[$row[csf('prod_cate')]]; ?></td>
                    <td width="100" style="word-break:break-all"><? echo $prod_type_arr[$item_sting]['prod_type']; ?></td>
                    <td width="70" style="word-break:break-all"><? echo change_date_format($row[csf('insert_date')]); ?></td>
                    <td width="70" style="word-break:break-all" bgcolor="<? echo $tdColorOpd; ?>"><? echo change_date_format($row[csf('po_received_date')]); ?></td>
                    <td width="70" style="word-break:break-all"><? echo change_date_format($row[csf('shipment_date')]); ?></td>
                    <td width="70" style="word-break:break-all"><p><? echo change_date_format($apprval_date[$row[csf('id')]]['min']); ?></p></td>
                    <td width="70" align="center">
                            <? 
                            echo (change_date_format($apprval_date[$row[csf('id')]]['max']) == change_date_format($apprval_date[$row[csf('id')]]['min']) && $apprval_date[$row[csf('id')]]['revised_no'] == 0) ? '' : change_date_format($apprval_date[$row[csf('id')]]['max']); 
							
							$min_approved_date=explode(" ",$min_approved_date);
							$fisrt_approved_date=$min_approved_date[0];
							$quick_insert_date=explode(" ",$quick_insert_date);
							$quick_costing_insert_date=$quick_insert_date[0];
							if($fisrt_approved_date!="") $fisrt_approved_date=$fisrt_approved_date;
							else if($quotation_app_info[$row[csf('quotation_id')]]['min_approved']!="") $fisrt_approved_date=$quotation_app_info[$row[csf('quotation_id')]]['min_approved'];
							else $fisrt_approved_date="";
							
							if($quick_costing_insert_date!="") $quickCosting_insert_date=$quick_costing_insert_date;
							else if($price_quotation[$row[csf('quotation_id')]]!="") $quickCosting_insert_date=$price_quotation[$row[csf('quotation_id')]];
							else $quickCosting_insert_date="";
                            ?>
                    </td>                    
                    <td width="70" align="center" bgcolor="<? echo $tdColorMin; ?>"><? echo $min_lead;?></td>                    
                    <td width="70" align="center"><? echo ($max_lead == $min_lead && $apprval_date[$row[csf('id')]]['revised_no'] == 0 ) ? '' : $max_lead ;?></td>
                    <td width="70" style="word-break:break-all" align="center"><? echo $row[csf('quotation_id')]; ?></td>
                    
                      <td width="70" style="word-break:break-all" align="center"><? echo number_format($margin,2); ?></td>
                      <td width="70" style="word-break:break-all" align="center"><? echo number_format($margin_per,2); ?></td>

                    <td width="70" style="word-break:break-all" align="center"><? echo change_date_format($quickCosting_insert_date); ?></td>
                    <td width="70" style="word-break:break-all" title="<?  if($quick_costing_insert_date!="") echo "QuickCost Found"; ?>" align="center"><?  echo change_date_format($fisrt_approved_date);;//change_date_format($quotation_app_info[$row[csf('quotation_id')]]['min_approved']); ?></td>
                    <td width="70" style="word-break:break-all" align="center"><? echo change_date_format($pre_cost[$row[csf('job_no')]]); ?></td>
                    <td width="70" style="word-break:break-all" align="center"><? echo change_date_format($precost_app_info[$row[csf('job_no')]]['min_approved']); ?></td>

                    <td width="50" align="center"><? echo ($apprval_date[$row[csf('id')]]['revised_no'] == 0) ? '' : $apprval_date[$row[csf('id')]]['revised_no'] ;?></td>
                    
                    <td width="70" align="center"><? echo $row[csf('date_diff')]; ?></td>
                    <td width="70" align="center"><? echo datediff("d",$apprval_date[$row[csf('id')]]['min'],$row[csf('shipment_date')]); ?></td>
                    <td width="50" align="right"  style="word-break:break-all"><? echo number_format($smv_sting,4);; if($row[csf('order_uom')]!=1) echo '='.number_format($smv_sum,4); ?></td>
                    <td width="70" align="right"  style="word-break:break-all"><? echo $pre_cost_efecency[$row[csf('job_no')]]; ?></td>
                    <td width="100" style="word-break:break-all" align="right"><? echo number_format($row[csf('po_quantity')],2); ?></td>
                    <td width="50" align="left"><div style="word-wrap:break-word; width:50px"><? echo $unit_of_measurement[$row[csf('order_uom')]]; if($row[csf('order_uom')]!=1)echo '<br>('.$set_sting.')'; ?></div></td>
                    <td width="100" align="right"><? echo $tot_pic_qty=$set_sum*$row[csf('po_quantity')]; ?></td>
                    <td width="100" align="right"><? $tot_smv=($row[csf('set_smv')]*$row[csf('po_quantity')]); echo number_format($tot_smv,2); ?></td>
                    <td width="70" align="right"><?php echo number_format($row[csf('unit_price')],2); ?></td>
                    <td width="120" align="right">
                        <?php 
                            $value=$row[csf('po_quantity')]*$row[csf('unit_price')]; 
                            echo number_format($value,2);
                        ?>
                    </td>
                    <td width="80" align="center"><? echo $order_status[$row[csf('is_confirmed')]]; ?></td>
                    <td><? echo $user_arr[$row[csf('inserted_by')]]; ?></td>
                </tr>
            	<?
				$total_po_qty += $row[csf('po_quantity')];
				$total_po_qtypcs += $tot_pic_qty;
				$grund_tot_smv += $tot_smv;
				$total_value += $value;
                //echo $total_po_qty.'<br>';
				$i++;
            } 
            //echo $total_po_qty.'<br>';
            ?> 
         <!-- </tbody>
		</table>
        </div>
        <table width="2300" border="1" cellpadding="0" cellspacing="0" class="tbl_bottom" rules="all"> -->
        	<tfoot>
                <td width="30">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="130">&nbsp;</td>
                <td width="100">&nbsp;</td>
                <td width="80">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="70">&nbsp;</td>
                <td width="50">&nbsp;</td>
                
                <td width="70">&nbsp;</td>
                <td width="70">Total:</td>
                <td width="50">&nbsp;</td>
                <td width="70">&nbsp;</td>
                
                <td width="100" align="right" id="tot_po_qty22"><? echo number_format($total_po_qty,2); ?></td>
                <td width="50">&nbsp;</td>
                <td width="100" align="right" id="tot_po_qty_pcs"><? echo number_format($total_po_qtypcs,2);?></td>
                <td width="100" align="right" id="tot_smv"><? echo number_format($grund_tot_smv,2); ?></td>
                <td width="70">&nbsp;</td>
                <td width="120" align="right" id="tot_val"><? echo number_format($total_value,2); ?></td>
                <td width="80">&nbsp;</td>
                <td>&nbsp;</td>
            </tfoot>
        </table>
        </div>   
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
