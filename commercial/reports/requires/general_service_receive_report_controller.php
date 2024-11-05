<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if ($action=="load_drop_down_supplier")
{ 
	echo create_drop_down( "cbo_supplier", 100, "select a.id,a.supplier_name from lib_supplier a, lib_supplier_party_type b, lib_supplier_tag_company c where a.id=b.supplier_id and a.id=c.supplier_id  and c.tag_company in($data) and a.status_active=1 and a.is_deleted=0 group by a.id,a.supplier_name order by a.supplier_name","id,supplier_name", 1, "-- Select --", 0, "load_drop_down( 'requires/general_service_receive_report_controller', this.value, 'load_drop_down_category', 'category_td' );",0 );
}

if($action=="load_drop_down_location")
{ 
	echo create_drop_down( "cbo_location", 140, "select id,location_name from lib_location where company_id=$data","id,location_name", 1, "-- Select Location --", $selected,"load_drop_down( 'requires/general_service_receive_report_controller',document.getElementById('cbo_company_name').value+'_'+this.value, 'load_drop_down_store', 'store_td' )",0,"" );
}

if ($action=="load_drop_down_department")
{
	echo create_drop_down( "cbo_department_name", 100,"select a.id,a.department_name from lib_department a, lib_division b where  b.company_id=$data and a.division_id=b.id and a.status_active=1 and b.status_active=1 group by a.id,a.department_name order by a.department_name ","id,department_name", 1, "-- All --", $selected, "load_drop_down( 'requires/general_service_receive_report_controller', this.value, 'load_drop_down_section','section_td');" );
}

if ($action=="load_drop_down_section")
{
	if ($data != ''){
		echo create_drop_down( "cbo_section", 100,"select id,section_name from lib_section where department_id=$data and is_deleted=0 and status_active=1","id,section_name", 1, "-- Select --", $selected, "" );
	} 
    else 
    {
		echo create_drop_down( "cbo_section", 100,$blank_array,"", 1, "-- Select --", $selected, "" );
	}
}


if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$store_arr=return_library_array( "select id, store_name from lib_store_location",'id','store_name');
	$department_arr=return_library_array( "select id, department_name from lib_department",'id','department_name');
	$divishion_arr=return_library_array( "select id, division_name from lib_division",'id','division_name');
	$item_group_arr=return_library_array( "select id, item_name from lib_item_group",'id','item_name');
	$user_arr=return_library_array( "select id, user_name from user_passwd",'id','user_name');
	$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$section_arr=return_library_array( "select id, section_name from lib_section",'id','section_name');
	$user_library = return_library_array("select id, user_name from user_passwd", "id", "user_name");

	$cbo_company_id=str_replace("'","",$cbo_company_name);
	$cbo_supplier=str_replace("'","",$cbo_supplier);
	$cbo_location=str_replace("'","", $cbo_location);
	$cbo_department_name=str_replace("'","", $cbo_department_name);
	$txt_search_string=str_replace("'","", $txt_search_string);
	$cbo_section=str_replace("'","", $cbo_section);
	$cbo_type=str_replace("'","", $cbo_type);
	$date_from=str_replace("'","", $txt_date_from);
	$date_to=str_replace("'","", $txt_date_to);
     	
	$department_cond=$cbo_suppayler=$cbo_loca=$cbo_section=$company_cond=$cond='';
	if ($cbo_company_id >0) $company_cond=" and a.company_id=$cbo_company_id";
	if ($cbo_supplier >0) $cbo_suppayler=" and d.supplier_id=$cbo_supplier";
	if ($cbo_location >0) $cbo_loca=" and a.location_id=$cbo_location";
	if ($cbo_department_name >0) $department_cond=" and a.department_id=$cbo_department_name";
	if ($cbo_section >0) $cbo_section=" and a.section_id=$cbo_section";

	$date_cond='';
    if($cbo_type==1){
	   if ($txt_search_string >0) $cond=" and a.requ_prefix_num=$txt_search_string";
		if($date_from != '' && $date_to != '')
		{
			$date_cond=" and a.requisition_date between '$date_from' and '$date_to'";
		}
    }elseif($cbo_type==2){
	   if ($txt_search_string >0) $cond=" and d.WO_NUMBER_PREFIX_NUM=$txt_search_string";
		if($date_from != '' && $date_to != '')
		{
			$date_cond=" and d.wo_date between '$date_from' and '$date_to'";
		}
    }else{
	   if ($txt_search_string >0) $cond=" and e.system_prefix_num=$txt_search_string";
	   if($date_from != '' && $date_to != '')
	   {
		   $date_cond=" and e.ackn_date between '$date_from' and '$date_to'";
	   }	   
    }



	$sql_requ="SELECT a.id as REQU_ID, a.requ_no as REQU_NO,a.division_id as DIVISION_ID,a.section_id as SECTION_ID,a.INSERTED_BY, d.id as WO_ID, a.requ_prefix_num as REQU_PREFIX_NUM, a.manual_req as MANUAL_REQ, a.requisition_date as REQUISITION_DATE, a.delivery_date as DELIVERY_DATE, a.store_name as STORE_NAME, a.pay_mode as PAY_MODE, a.department_id as DEPARTMENT_ID, b.item_category as ITEM_CATEGORY, b.id as REQ_DTSL_ID, b.product_id as PROD_ID, b.quantity as QUANTITY, b.rate as RATE, b.cons_uom as CONS_UOM, b.amount as AMOUNT, a.is_approved as IS_APPROVED, a.company_id as COMPANY_ID, a.location_id as LOCATION_ID, a.remarks as REMARKS, b.remarks as DTLS_REMARKS, a.source as SOURCE, d.WO_DATE as WO_DEL_DATE, d.wo_number as WO_NUMBER, c.rate as WO_RATE, c.supplier_order_quantity as WO_QTY, c.amount as WO_AMOUNT ,c.service_details as SERVICE_DETAILS, c.service_for as SERVICE_FOR, d.WO_BASIS_ID,d.supplier_id as SUPPLIER_ID,d.currency_id as CURRENCY_ID, e.SYSTEM_NO, e.ACKN_DATE, f.ACKN_QTY, f.amount as ACK_AMMOUNT
	from inv_purchase_requisition_mst a, inv_purchase_requisition_dtls b 
	left join wo_non_order_info_dtls c on b.id=c.requisition_dtls_id and c.status_active=1 and c.is_deleted=0  
	left join wo_non_order_info_mst d on d.id=c.mst_id and d.status_active=1 and d.is_deleted=0
    LEFT JOIN wo_service_acknowledgement_mst e ON d.id = e.wo_booking_id AND e.status_active = 1 AND e.is_deleted = 0
    LEFT JOIN wo_service_acknowledgement_dtls f ON e.id = f.mst_id AND f.status_active = 1 AND f.is_deleted = 0
	where a.id=b.mst_id and a.status_active=1 and a.entry_form=526 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $company_cond $department_cond  $date_cond $cbo_suppayler $cbo_loca $cbo_section $cond order by a.id desc";
	// echo $sql_requ;
	$sql_requ_res=sql_select($sql_requ);   
	$tot_rows=0;
	$requ_ids=$wo_ids=$pi_ids=$prod_ids="";
	$requisition_arr=array();
	$check_requisition_arr=array();
	foreach ($sql_requ_res as $row) 
	{
		if ($check_requisition_arr[$row['REQU_ID']][$row['PROD_ID']]=='')
		{
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['SYSTEM_NO']=$row['SYSTEM_NO'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['ACKN_DATE']=$row['ACKN_DATE'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['ACKN_QTY']=$row['ACKN_QTY'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['ACK_AMMOUNT']=$row['ACK_AMMOUNT'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['SUPPLIER_ID']=$row['SUPPLIER_ID'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['CURRENCY_ID']=$row['CURRENCY_ID'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['REQU_ID']=$row['REQU_ID'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['WO_BASIS_ID']=$row['WO_BASIS_ID'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['REQU_NO']=$row['REQU_NO'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['REQU_PREFIX_NUM']=$row['REQU_PREFIX_NUM'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['SERVICE_DETAILS']=$row['SERVICE_DETAILS'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['SERVICE_FOR']=$row['SERVICE_FOR'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['INSERTED_BY']=$row['INSERTED_BY'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['MANUAL_REQ']=$row['MANUAL_REQ'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['REQUISITION_DATE']=$row['REQUISITION_DATE'];		
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['DELIVERY_DATE']=$row['DELIVERY_DATE'];		
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['STORE_NAME']=$row['STORE_NAME'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['PAY_MODE']=$row['PAY_MODE'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['IS_APPROVED']=$row['IS_APPROVED'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['DEPARTMENT_ID']=$row['DEPARTMENT_ID'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['SECTION_ID']=$row['SECTION_ID'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['DIVISION_ID']=$row['DIVISION_ID'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['ITEM_CATEGORY']=$row['ITEM_CATEGORY'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['REQ_DTSL_ID'].=$row['REQ_DTSL_ID'].',';
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['PROD_ID'].=$row['PROD_ID'].',';
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['QUANTITY']+=$row['QUANTITY'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['RATE']=$row['RATE'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['AMOUNT']+=$row['AMOUNT'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['CONS_UOM']=$row['CONS_UOM'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['COMPANY_ID']=$row['COMPANY_ID'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['LOCATION_ID']=$row['LOCATION_ID'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['REMARKS']=$row['REMARKS'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['DTLS_REMARKS']=$row['DTLS_REMARKS'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['SOURCE']=$row['SOURCE'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['WO_RATE']=$row['WO_RATE'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['WO_DEL_DATE']=$row['WO_DEL_DATE'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['WO_NUMBER']=$row['WO_NUMBER'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['WO_QTY']=$row['WO_QTY'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['WO_AMOUNT']+=$row['WO_AMOUNT'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['WO_RATE']=$row['WO_RATE'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['WO_ID']=$row['WO_ID'];
			$requisition_arr[$row['REQU_ID']][$row['PROD_ID']]['PI_IDS'].=$row['PI_ID'].",";
			$check_requisition_arr[$row['REQU_ID']][$row['PROD_ID']]=$row['REQU_ID'].'**'.$row['PROD_ID'];
		}
	}
	// echo '<pre>';print_r($requisition_arr);die;

	$table_width=2950;
	ob_start();
	?>
    <div style="width:<? echo $table_width+30; ?>px; margin-left:5px">
        <table width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" align="left">
            <tr>
                <td align="left" width="100%" colspan="25" style="font-size:18px"><strong></strong></td>
            </tr>
        </table>
        <table width="<? echo $table_width; ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                <thead>
                    <tr>
                        <th colspan="14">Service Requisiton Details</th>
                        <th colspan="7">WO Details</th>
                        <th colspan="4">Service Akn. Details</th>
                    </tr>
                    <tr>
                    	<!--Requisiton info tot collum 14 width 1350--> 
                        <th width="30">SL</th>
                        <th width="120">Divition</th>
                        <th width="120">Department</th>
                        <th width="100">Section</th>
                        <th width="150">Req. No</th>                      
                        <th width="120">Req. Date</th>
                        <th width="100">Req. By</th>                      
                        <th width="80">Service For</th>
                        <th width="80">Service Details</th>
                        <th width="120">Item Description</th>
                        <th width="60">UOM</th>
                        <th width="80">Req. Quty</th>
                        <th width="60">Req Rate</th>
                        <th width="100">Req. Amount</th>                                            

						  <!--WO Details --> 
						<th width="120">Basis </th>
						<th width="120">WO Number</th>
                        <th width="80">WO Date</th>
                        <th width="80">WO qty</th>
                        <th width="80">Supplier </th>
                        <th width="80">Currency</th>
                        <th width="80">WO Net Value</th>

                          <!--ACK Details --> 
						<th width="120">Service Akn. No</th>
						<th width="120">Ackn Date</th>
                        <th width="80">Ackn. Qty</th>
                        <th width="80">Amount</th>
                    </tr>
                </thead>
                <tbody>
		 		    <?
		 		    $i=1;
		 		    $tot_requ_qnty=$tot_requ_value=0;
					$tot_mrr_qnty=$tot_mrr_value=0;
                    $wo_ammount="";$wo_net_value="";
                    $ack_ammount="";
					$wo_id_arr=$pi_id_arr=array();
		 		    foreach ($requisition_arr as $key => $prod_data)
		 		    {
		 		    	foreach ($prod_data as $prod_id => $row)
		 		    	{	
		 		    		if ($row['IS_APPROVED']==0) $is_approved="Un-Approved";
		 		    		else $is_approved="Approved";
		 		    		$approval_person=$approval_arr[$row['REQU_ID']]['APPROVED_BY'];		
		 		    		$approval_date=$approval_arr[$row['REQU_ID']]['APPROVED_DATE'];
		 		    		$row_span=$row_span_arr[$row['REQU_ID']][$prod_id];
							
							// echo $key."__".$prod_id."__".$mrr_arr[$key][$prod_id]['RECV_NUMBER']."<br>";
							 //echo $mrr_arr[$key][$row['PROD_ID']]['RECV_NUMBER']."dd";
							 
							 
		 		    		$tot_requ_qnty+=$row['QUANTITY'];
							$tot_requ_value+=$row['AMOUNT'];
							$wo_value=$row['WO_RATE']*$row['SUPPLIER_ORDER_QUANTITY'];
							$wo_rate=0;
							if ($row['WO_AMOUNT']!=0 && $row['WO_QTY'] != 0){
								$wo_rate=$row['WO_AMOUNT']/$row['WO_QTY'];
							}
							
		 		    		?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
		                    	<td width="30" align="center" ><? echo $i; ?></td>
		                        <td width="120" ><p><? echo $divishion_arr[$row['DIVISION_ID']]; ?>&nbsp;</p></td>
		                        <td width="120" ><p><? echo $department_arr[$row['DEPARTMENT_ID']]; ?>&nbsp;</p></td>
		                        <td width="100" ><p><? echo $section_arr[$row['SECTION_ID']]; ?>&nbsp;</p></td>
		                        <td width="150" ><p><? echo  $row['REQU_NO']; ?>&nbsp;</p></td>
		                        <td width="120" align="center"><p><? echo  change_date_format($row['REQUISITION_DATE']); ?>&nbsp;</p></td>
		                        <td width="100" align="center" ><p><? echo $user_library[$row['INSERTED_BY']]; ?>&nbsp;</p></td>
		                        <td width="80" align="center" ><p><? echo $service_for_arr[$row['SERVICE_FOR']]; ?>&nbsp;</p></td>
		                        <td width="80" align="center" ><p><? echo $row['SERVICE_DETAILS']; ?>&nbsp;</p></td>
		                        <td width="120"align="center" ><p><? echo $product_arr[$prod_id]['ITEM_DESCRIPTION']; ?>&nbsp;</p></td>
		                        <td width="60" align="center" ><p><? echo $unit_of_measurement[$row['CONS_UOM']]; ?>&nbsp;</p></td>
		                        <td width="80" align="right" ><p><? echo number_format($row['QUANTITY'],2); ?></p></td>
		                        <td width="60" align="right" ><p><? echo number_format($row['RATE'],2); ?></p></td>
		                        <td width="100" align="right" ><p><? echo number_format($row['AMOUNT'],2); ?></p></td>

		                        <td width="120" align="center"  ><p>&nbsp;<? echo  $wo_basis[$row['WO_BASIS_ID']];?></p></td>
		                        <td width="120" align="center"  ><p>&nbsp;<? echo  $row['WO_NUMBER'];?></p></td>
		                        <td width="80" align="center"  ><p>&nbsp;<? echo change_date_format($row['WO_DEL_DATE']);?></p></td>
		                        <td width="80"align="right" ><p>&nbsp;<? echo number_format($row['WO_QTY'],2);?></p></td>
		                        <td width="80"  align="right" ><p>&nbsp;<? echo $supplier_arr[$row['SUPPLIER_ID']]; ?></p></td>
		                        <td width="80"  align="right"><p>&nbsp;<? echo $currency[$row['CURRENCY_ID']];?></p></td>
		                        <td width="80"  align="right"><p>&nbsp;<? echo number_format($row['WO_AMOUNT'],2); ?></p></td>

                                <td width="120" align="center"  ><p>&nbsp;<? echo  $row['SYSTEM_NO'];?></p></td>
		                        <td width="120" align="center"  ><p>&nbsp;<? echo  change_date_format($row['ACKN_DATE']);?></p></td>
		                        <td width="80" align="center"  ><p>&nbsp;<? echo $row['ACKN_QTY'];;?></p></td>
		                        <td width="80"align="right" ><p>&nbsp;<? echo $row['ACK_AMMOUNT'];?></p></td>		                      
                            </tr>
                            <?
                            $tot_mrr_qnty+=$mrr_qnty;
                            $tot_mrr_value+=$mrr_amt;
                            $wo_ammount+=$row['AMOUNT'];
                            $wo_net_value+=$row['WO_AMOUNT'];
                            $ack_ammount+=$row['ACK_AMMOUNT'];
							$i++;						
						}												
					}		
					?>
                 </tbody>
                 <tfoot>
                        <tr>                            
                            <th colspan="13" style="word-break: break-all;" width="180">Total</th>
                            <th ><?=$wo_ammount?></th>
                            <th ></th>
                            <th ></th>
                            <th ></th>
                            <th colspan="3"></th>
                            <th><?=$wo_net_value?></th>
                            <th colspan="3"> </th>                      
                            <th ><?=$ack_ammount?></th>
                        </tr>
                 </tfoot>
        </table>
       
    </div>
	<?
	foreach (glob("$user_id*.xls") as $filename) 
	{
		@unlink($filename);
	}
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	echo "$total_data****$filename";
	exit();
}
disconnect($con);
?>
