<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];



if ($action=="load_drop_down_buyer")
{ 
	list($company,$type)=explode("_",$data);
	if($type==1)
	{
		echo create_drop_down( "cbo_customer_name", 100, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Company --", $company, "$load_function");
	}
	else
	{
		echo create_drop_down( "cbo_customer_name", 100, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", "", "" );
	}	
	exit();	 
} 







if($action=="generate_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$cbo_company_id=str_replace("'","", $cbo_company_id);
	$cbo_customer_source=str_replace("'","", $cbo_customer_source);
	$cbo_customer_name=str_replace("'","", $cbo_customer_name);
	$txt_order_no=str_replace("'","", $txt_order_no);
	$cbo_section_id=str_replace("'","", $cbo_section_id);
	$cbo_sub_section_id=str_replace("'","", $cbo_sub_section_id);
	$txt_item_description=str_replace("'","", $txt_item_description);
	$cbo_date_category=str_replace("'","", $cbo_date_category);
	$txt_date_from=str_replace("'","", $txt_date_from);
	$txt_date_to=str_replace("'","", $txt_date_to);
	$cbo_delivery_status=str_replace("'","", $cbo_delivery_status);


	if($cbo_delivery_status== 0){$selivery_status_con="";}
	else{$selivery_status_con=" and c.delivery_status=$cbo_delivery_status";}
	
	
	
	if($db_type==0) 
	{
		$txt_date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
		$txt_date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
	}
	else if($db_type==2) 
	{
		$txt_date_from=change_date_format($txt_date_from,'','',1);
		$txt_date_to=change_date_format($txt_date_to,'','',1);
	}
	
	
	$companyArr = return_library_array("select id,company_name from lib_company where status_active=1 and is_deleted=0","id","company_name");
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 and is_deleted=0 ","id","buyer_name");
	$trimsGroupArr = return_library_array("select id, item_name from lib_item_group where item_category=4 and status_active=1","id","item_name");
	$colorNameArr = return_library_array("select id, color_name from lib_color where status_active=1 and is_deleted=0","id","color_name");
	
	
	
 	if($txt_date_from!="" and $txt_date_to!=""){	
		if($cbo_date_category==1){$where_con.=" and a.receive_date between '$txt_date_from' and '$txt_date_to'";}	
		else if($cbo_date_category==2){$where_con.=" and a.delivery_date between '$txt_date_from' and '$txt_date_to'";}
	}
	if($cbo_section_id){$where_con.=" and b.section='$cbo_section_id'";} 
	if($cbo_sub_section_id){$where_con.=" and b.sub_section='$cbo_sub_section_id'";} 
	if($cbo_customer_source){$where_con.=" and a.within_group='$cbo_customer_source'";} 
	
	
	
	
	$trims_order_sql="select a.id,a.subcon_job,a.receive_date,a.delivery_date,a.order_no as cust_order_no,a.party_id,a.within_group,a.subcon_job,b.item_group,b.buyer_buyer,b.section,b.sub_section,b.job_no_mst,b.order_no,b.booked_uom,b.order_uom,b.buyer_po_no,c.description,c.order_id,c.item_id,c.color_id,c.size_id ,c.qnty,c.rate ,c.amount,c.booked_qty,b.delivery_status from subcon_ord_mst a, subcon_ord_dtls b, subcon_ord_breakdown c where a.subcon_job=b.job_no_mst and b.job_no_mst=c.job_no_mst and b.id=c.mst_id and a.entry_form=255 and a.company_id =$cbo_company_id and b.order_no like('%$txt_order_no%') $where_con $selivery_status_con
 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 ";
	   //echo $trims_order_sql;die;
	$trims_receive_id_arr=array();$trims_data_arr=array();	
	$result_trims_order_sql = sql_select($trims_order_sql);
	foreach($result_trims_order_sql as $row)
	{
		$key=$row[csf("id")].'*'.$row[csf("order_id")].'*'.$row[csf("section")].'*'.$row[csf("sub_section")].'*'.$row[csf("item_group")].'*'.$row[csf("item_id")].'*'.$row[csf("description")].'*'.$row[csf("color_id")].'*'.$row[csf("size_id")].'*'.$row[csf("booked_uom")].'*'.$row[csf("rate")];
		
		$trims_data_arr[$key][qnty]+=$row[csf("qnty")];
		$trims_data_arr[$key][amount]+=$row[csf("amount")];
		$trims_data_arr[$key][booked_qty]+=$row[csf("booked_qty")];
		
		$date_array[$key]=array(
		subcon_job=>$row[csf("subcon_job")],
		item_group=>$row[csf("item_group")],
		receive_date=>$row[csf("receive_date")],
		delivery_date=>$row[csf("delivery_date")],
		order_qty=>$trims_data_arr[$key][qnty],
		order_rate=>$row[csf("rate")],
		order_amount=>$trims_data_arr[$key][amount],
		booked_qty=>$trims_data_arr[$key][booked_qty],
		cust_order_no=>$row[csf("cust_order_no")],
		party_id=>$row[csf("party_id")],
		within_group=>$row[csf("within_group")],
		buyer_buyer=>$row[csf("buyer_buyer")],
		section=>$row[csf("section")],
		sub_section=>$row[csf("sub_section")],
		booked_uom=>$row[csf("booked_uom")],
		order_uom=>$row[csf("order_uom")],
		description=>$row[csf("description")],
		delivery_status=>$row[csf("delivery_status")],
		order_no=>$row[csf("order_no")],
		buyer_po_no=>$row[csf("buyer_po_no")],
		
		);
		$trims_receive_id_arr[$row[csf("id")]]=$row[csf("id")];
	}
		
	//Job-------------------------------	
	$trims_job_sql="select id,trims_job,received_no from trims_job_card_mst where received_id in(".implode(',',$trims_receive_id_arr).")";
	$trims_job_no_arr=array();	
	$result_trims_job_sql = sql_select($trims_job_sql);
	foreach($result_trims_job_sql as $row)
	{
		$trims_job_no_arr[$row[csf("received_no")]]=$row[csf("trims_job")];
		$trims_job_id_arr[$row[csf("id")]]=$row[csf("id")];
	}
		
		
	//production.................................
	$trims_production_sql="select a.section_id,c.sub_section,a.received_id,a.job_id,b.job_dtls_id,c.id as order_receive_dtls_id,c.item_description,c.color_id,c.size_id,c.uom,c.job_quantity,b.production_qty,b.qc_qty  from trims_production_mst a,trims_production_dtls b,trims_job_card_dtls c where a.id=b.mst_id and c.id=b.job_dtls_id and  a.job_id in(".implode(',',$trims_job_id_arr).") and a.received_id in(".implode(',',$trims_receive_id_arr).") and a.entry_form=269
and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	$trims_production_data_arr=array();	
	$result_trims_production_sql = sql_select($trims_production_sql);
	foreach($result_trims_production_sql as $row)
	{
		$key=$row[csf("received_id")].'*'.$row[csf("section_id")].'*'.$row[csf("sub_section")].'*'.$row[csf("item_description")].'*'.$row[csf("color_id")].'*'.$row[csf("size_id")].'*'.$row[csf("uom")];
		
		$trims_production_data_arr[$key][qc_qty]+=$row[csf("qc_qty")];
		$trims_production_data_arr[$key][job_quantity]+=$row[csf("job_quantity")];
	}
	
	
	
	//Delivery.................................
	$trims_delivery_sql="select a.delivery_date,a.received_id,b.delevery_qty,b.order_receive_rate,c.uom,b.section,c.sub_section,b.item_group,b.color_id,b.size_id,b.description,b.delevery_status from trims_delivery_mst a, trims_delivery_dtls b, trims_job_card_dtls c  where a.id=b.mst_id and c.id=b.job_dtls_id and a.received_id  in(".implode(',',$trims_receive_id_arr).")  and a.entry_form=208 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 order by a.delivery_date";
	$trims_delivery_data_arr=array();	
	$result_trims_delivery_sql = sql_select($trims_delivery_sql);
	foreach($result_trims_delivery_sql as $row)
	{
		$key=$row[csf("item_group")].'*'.$row[csf("received_id")].'*'.$row[csf("section")].'*'.$row[csf("sub_section")].'*'.$row[csf("description")].'*'.$row[csf("color_id")].'*'.$row[csf("size_id")].'*'.$row[csf("uom")];
		
		$trims_delivery_data_arr[$key][delevery_qty]+=$row[csf("delevery_qty")];
		$trims_delivery_data_arr[$key][delevery_val]+=$row[csf("delevery_qty")]*$row[csf("order_receive_rate")];
		$trims_delivery_data_arr[$key][delevery_last_date]=$row[csf("delivery_date")];
		$trims_delivery_data_arr[$key][delevery_status]=$row[csf("delevery_status")];
	}
	
	
	//print_r($trims_delivery_data_arr);
	
	//Bill.................................
	//select SECTION,ITEM_DESCRIPTION,COLOR_ID,SIZE_ID,ORDER_UOM,TOTAL_DELV_QTY,b.QUANTITY     from TRIMS_BILL_MST a, TRIMS_BILL_dtls b where a.id=b.mst_id and a.ENTRY_FORM=276	
		
		
	//bill.................................
	$trims_bill_sql="select d.received_id,b.section,c.sub_section,b.item_description,b.color_id,b.size_id,c.uom,total_delv_qty,b.quantity,b.bill_amount from trims_bill_mst a, trims_bill_dtls b,trims_job_card_dtls c, trims_job_card_mst d  where a.id=b.mst_id and c.id=b.job_dtls_id and c.job_no_mst=d.trims_job and a.entry_form=276 and d.received_id in(".implode(',',$trims_receive_id_arr).") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	$trims_bill_data_arr=array();	
	$result_trims_bill_sql = sql_select($trims_bill_sql);
	foreach($result_trims_bill_sql as $row)
	{
		$key=$row[csf("received_id")].'*'.$row[csf("section")].'*'.$row[csf("sub_section")].'*'.$row[csf("item_description")].'*'.$row[csf("color_id")].'*'.$row[csf("size_id")].'*'.$row[csf("uom")];
		
		$trims_bill_data_arr[$key][bill_qty]+=$row[csf("quantity")];
		$trims_bill_data_arr[$key][bill_val]+=$row[csf("bill_amount")];
	}
	
		
		
		
		$width=3400;
		ob_start();
		?>
		<div align="center" style="height:auto; width:<? echo $width+20;?>px; margin:0 auto; padding:0;">
			<table width="<? echo $width;?>" cellpadding="0" cellspacing="0" id="caption" align="center">
				<thead class="form_caption" >
					<tr>
						<td colspan="35" align="center" style="font-size:20px;"><? echo $companyArr[$cbo_company_id]; ?></td>
					</tr>
					<tr>
						<td colspan="35" align="center" style="font-size:14px; font-weight:bold" ><? echo $report_title; ?></td>
					</tr>
					<tr>
						<td colspan="35" align="center" style="font-size:14px; font-weight:bold">
							<? echo " From : ".change_date_format($txt_date_from) ." To : ". change_date_format($txt_date_to) ;?>
						</td>
					</tr>
				</thead>
			</table>
            <table align="left">
                <tr>
                    <td bgcolor="#FFF" width="15">&nbsp;</td><td>Full Pending</td>
                    <td bgcolor="#FFCC66" width="15">&nbsp;</td><td>Partial Deliverd</td>
                    <td bgcolor="#8CD59C" width="15">&nbsp;</td><td>Full Deliverd</td>
              </tr>
            </table>
            
            
            <table  border="1" cellpadding="2" cellspacing="0" class="rpt_table" width="<? echo $width;?>" rules="all" id="rpt_table_header" align="left">
				<thead>
                    <th width="35">SL</th>
                    <th width="120">Order Rcv. No</th>
                    <th width="100">Job Card No</th>
                    <th width="100">Cust. WO No</th>
                    <th width="100">Customer Name</th>
                    <th width="100">Cust. Buyer</th>
                    <th width="100">Cust. Order No</th>
                    <th width="60">Section</th>
                    <th width="100">Sub-Section</th>
                    <th width="100">Trims Group</th>
                    <th width="100">Item Description</th>
                    <th width="100">Color</th>
                    <th width="60">Order UOM</th>
                    <th width="100">Order Qty</th>
                    <th width="60">Booked UOM</th>
                    <th width="100">Booked Qty</th>
                    <th width="100">Order Rate ($)</th>
                    <th width="100">Order Amount ($)</th>
                    <th width="80">Order Rcv.Date</th>
                    <th width="80">Target Delv. Date</th>
                    <th width="80">Last Delv. Date</th>
                    <th width="100">Delv.Status</th>
                    <th width="100">Production Qty</th>
                    <th width="100">Prod Bal Qty</th>
                    <th width="100">Delv.Qty</th>
                    <th width="100">Delv.Amount</th>
                    <th width="100">Delv. Balance Qty</th>
                    <th width="100">Delv. Balance Amount</th>
                    <th width="100">Short Delv Qty</th>
                    <th width="100">Short Delv Value</th>
                    <th width="100">Bill Qty</th>
                    <th width="100">Bill Amount</th>
                    <th width="100">Bill Bal. Qty</th>
                    <th width="100">Bill Bal. Amnt</th>
                    <th>Remarks</th>
				</thead>
			</table>
			<div style="width:<? echo $width+18;?>px; max-height:250px; float:left; overflow-y:scroll;" id="scroll_body">
			<table border="1" cellpadding="2" cellspacing="0" class="rpt_table" id="table_body_id" width="<? echo $width;?>" rules="all" align="left">
                <? 
				$i=1;
				$total_order_qty=0;$total_order_val=0;$total_booked_qty=0;$total_production_qty=0;
				
				foreach($date_array as $key=>$row){
					
					list($id,$order_id,$section,$sub_section,$item_group,$item_id,$description,$color_id,$size_id,$booked_uom,$rate)=explode('*',$key);
					$key=$id.'*'.$section.'*'.$sub_section.'*'.$description.'*'.$color_id.'*'.$size_id.'*'.$booked_uom;
					//$production_qty_on_order_parcent=($trims_production_data_arr[$key][qc_qty]/$trims_production_data_arr[$key][job_quantity])*$row[order_qty];
					$production_qty_on_order_parcent=$trims_production_data_arr[$key][qc_qty];
					
					//WORK ORDER NO : 161
					
					
					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					$party=($row[within_group]==1)?$companyArr[$row[party_id]]:$buyerArr[$row[party_id]];
				
					//$row[delivery_status]=$trims_delivery_data_arr[$item_group.'*'.$key][delevery_status];
					
					if($row[delivery_status]==2){$bgcolor="#FFCC66";}
					elseif($row[delivery_status]==3){$bgcolor="#8CD59C";}
					else{$row[delivery_status]=1;}
				
				//---------------------------------------
				$total_order_qty+=$row[order_qty];
				$total_order_val+=$row[order_amount];
				$total_booked_qty+=$row[booked_qty];
				$total_production_qty+=$production_qty_on_order_parcent;
				?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                    <td width="35" align="center"><? echo $i;?></td>
                    <td width="120" align="center"><p><? echo $row[subcon_job];?></p></td>
                    <td width="100" align="center"><p><? echo $trims_job_no_arr[$row[subcon_job]];?></p></td>
                    <td width="100" align="center"><p><? echo $row[cust_order_no];?></p></td>
                    <td width="100"><p><? echo $party;?></p></td>
                    <td width="100"><p><? echo $buyerArr[$row[buyer_buyer]];?></p></td>
                    <td width="100"><p><? echo $row[buyer_po_no];?></p></td>
                    <td width="60"><p><? echo $trims_section[$row[section]];?></p></td>
                    <td width="100"><p><? echo $trims_sub_section[$row[sub_section]];?></p></td>
                    <td width="100"><p><? echo $trimsGroupArr[$row[item_group]];?></p></td>
                    <td width="100"><p><? echo $row[description];?></p></td>
                    <td width="100"><p><? echo $colorNameArr[$color_id];?></p></td>
                    <td width="60" align="center"><? echo $unit_of_measurement[$row[order_uom]];?></td>
                    <td width="100" align="right"><? echo number_format($row[order_qty],0);?></td>
                    <td width="60" align="center"><? echo $unit_of_measurement[$row[booked_uom]];?></td>
                    <td width="100" align="right"><? echo number_format($row[booked_qty],0);?></td>
                    <td width="100" align="right"><? echo number_format($row[order_rate],4);?></td>
                    <td width="100" align="right"><? echo number_format($row[order_amount],2);?></td>
                    <td width="80" align="center"><? echo change_date_format($row[receive_date]);?></td>
                    <td width="80" align="center"><? echo change_date_format($row[delivery_date]);?></td>
                    <td width="80" align="center"><? echo change_date_format($trims_delivery_data_arr[$item_group.'*'.$key][delevery_last_date]);?></td>
                    <td width="100" align="center"><? echo $delivery_status[$row[delivery_status]];?></td>
                    <td width="100" align="right"><? echo number_format($production_qty_on_order_parcent,0);?></td>
                    <td width="100" align="right"><? 
					//echo number_format($row[order_qty]-$production_qty_on_order_parcent,0);
					echo number_format($row[booked_qty]-$production_qty_on_order_parcent,0);
					?></td>
                    <td width="100" align="right"><? echo number_format($trims_delivery_data_arr[$item_group.'*'.$key][delevery_qty],0);?></td>
                    <td width="100" align="right"><? echo number_format($trims_delivery_data_arr[$item_group.'*'.$key][delevery_val],2);?></td>
                    <td width="100" align="right"><? 
					echo number_format($row[order_qty]-$trims_delivery_data_arr[$item_group.'*'.$key][delevery_qty],0);
					//echo number_format($row[booked_qty]-$trims_delivery_data_arr[$item_group.'*'.$key][delevery_qty],0);?></td>
                    <td width="100" align="right"><? echo number_format(($row[order_amount]-$trims_delivery_data_arr[$item_group.'*'.$key][delevery_val]),2);?></td>
                    
                    
                    <td width="100" align="right"><? //echo number_format($row[order_qty]-$trims_delivery_data_arr[$item_group.'*'.$key][delevery_qty],2);?></td>
                    <td width="100" align="right"><? //echo number_format($row[order_amount]-$trims_delivery_data_arr[$item_group.'*'.$key][delevery_val]);?></td>
                    
                    
                    
                    
                    <td width="100" align="right"><? echo $trims_bill_data_arr[$key][bill_qty];?></td>
                    <td width="100" align="right"><? echo $trims_bill_data_arr[$key][bill_val];?></td>
                    <td width="100" align="right"><? echo number_format($trims_delivery_data_arr[$item_group.'*'.$key][delevery_qty]-$trims_bill_data_arr[$key][bill_qty]);?></td>
                    <td width="100" align="right"><? echo number_format($trims_delivery_data_arr[$item_group.'*'.$key][delevery_val]-$trims_bill_data_arr[$key][bill_val],2);?></td>
                    <td></td>
                </tr>
                <? 
				$i++;
				} ?>
			</table>
			</div>
			<table width="<? echo $width;?>" border="1" cellpadding="2" cellspacing="0" class="rpt_table" rules="all" id="table_body_footer" align="left">
				<tfoot>
                    <th width="35"></th>
                    <th width="120"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"> </th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="60"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100"></th>
                    <th width="100" align="right"><? //echo number_format($total_order_qty);?></th>
                    <th width="60"></th>
                    <th width="100"></th>
                    <th width="60" align="right"><? //echo number_format($total_booked_qty);?></th>
                    <th width="100"></th>
                    <th width="100" align="right"><? //echo number_format($total_order_val,2);?></th>
                    <th width="100" align="right"><? //echo number_format($total_order_val,2);?></th>
                    <th width="80"></th>
                    <th width="80"></th>
                    <th width="80"></th>
                    <th width="100"></th>
                    <th width="100" align="right"><? //echo number_format($total_production_qty,2);?></th>
                    <th width="100"><!--Prod Bal Qty--></th>
                    <th width="100" align="right"><? //echo $total_delivery_qty;?></th>
                    <th width="100"><!--Delv.Amount--></th>
                    <th width="100"><!--Delv. Balance Qty--></th>
                    <th width="100"><!--Delv. Balance Amount--></th>
                    <th width="100"><!--Short Delv Qty--></th>
                    <th width="100"><!--Short Delv Value--></th>
                    <th width="100"><!--Bill Qty--></th>
                    <th width="100"><!--Bill Amount--></th>
                    <th width="100"><!--Bill Bal. Qty--></th>
                    <th width="100"><!--Bill Bal. Amnt--></th>
                    <th>&nbsp;</th>
				</tfoot>
			</table>
        </div>
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
    echo "$html**$filename**$report_type";
    exit();
}



?>
