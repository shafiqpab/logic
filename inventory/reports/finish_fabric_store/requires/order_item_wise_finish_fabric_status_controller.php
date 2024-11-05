<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../../includes/common.php');

if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if($action=="load_drop_down_buyer")
{
	$data=explode("_",$data);
	if($data[1]==1) $party="1,3,21,90"; else $party="80";
	echo create_drop_down( "cbo_buyer_id", 140, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data[0]' $buyer_cond and buy.id in (select buyer_id from lib_buyer_party_type where party_type in ($party)) order by buy.buyer_name","id,buyer_name", 1, "--Select Buyer--", $selected, "","" );
	exit();
}

if ($action=="load_drop_down_store")
{
	echo create_drop_down( "cbo_store_id", 100, "select a.id,a.store_name from lib_store_location a,lib_store_location_category b where a.id=b.store_location_id and a.status_active=1 and a.is_deleted=0 and a.company_id='$data' and  b.category_type in(2,3)  group by a.id,a.store_name","id,store_name", 1, "--Select Store--", 0, "",$disable );
	exit();
}



if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_buyer_id=str_replace("'","",$cbo_buyer_id);
	$cbo_report_type=str_replace("'","",$cbo_report_type);
	$cbo_year=str_replace("'","",$cbo_year);
	$cbo_search_by=str_replace("'","",$cbo_search_by);
	$txt_search_comm=str_replace("'","",$txt_search_comm);
	$txt_search_booking=str_replace("'","",$txt_search_booking);
	$cbo_store_id=str_replace("'","",$cbo_store_id);
	$cbo_shipment_status=str_replace("'","",$cbo_shipment_status);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);


	$sql_cond="";
	if($cbo_buyer_id > 0) $sql_cond=" and a.buyer_name=$cbo_buyer_id";
	if($cbo_store_id > 0) $sql_cond.=" and c.store_id=$cbo_store_id";
	if($cbo_shipment_status > 0) $sql_cond.=" and b.shiping_status=$cbo_shipment_status";

	if($cbo_year > 0) 
	{
		if($db_type==0) $sql_cond .=" and year(a.insert_date) = $cbo_year"; 
		else if($db_type==2) $sql_cond .=" and to_char(a.insert_date,'YYYY') = $cbo_year";
	}

	$search_cond='';
	if($cbo_search_by==1 && $txt_search_comm != "")
	{
		$sql_cond.=" and a.job_no_prefix_num in ($txt_search_comm) ";
	}
	else if($cbo_search_by==2 && $txt_search_comm != "")
	{
		$sql_cond.=" and a.style_ref_no LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==3 && $txt_search_comm != "")
	{
		$sql_cond.=" and b.po_number LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==4 && $txt_search_comm != "")
	{
		$sql_cond.=" and b.file_no LIKE '%$txt_search_comm%'";
	}
	else if($cbo_search_by==5 && $txt_search_comm != "")
	{
		$sql_cond.=" and b.grouping LIKE '%$txt_search_comm%'";
	}
	if( $txt_date_from !="" && $txt_date_to !="") $sql_cond.= " and c.transaction_date between '$txt_date_from' and '$txt_date_to'";
	
	$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name"  );
	$color_arr=return_library_array( "select id,color_name from lib_color", "id", "color_name"  );
	
	$batch_booking_cond="";
	if($txt_search_booking !="")
	{
		$sql_booking=sql_select("select id, batch_no, booking_no from pro_batch_create_mst where booking_no='$txt_search_booking'");
		foreach ($sql_booking as $row)
		{
			$batch_ids.=$row[csf("id")].",";
			$batch_arr[$row[csf("id")]]["batch_no"]=$row[csf("batch_no")];
			$batch_arr[$row[csf("id")]]["booking_no"]=$row[csf("booking_no")];
		}
		$batch_ids=chop($batch_ids,",");
		$batch_booking_cond=" and c.pi_wo_batch_no in($batch_ids)";
	}
	else
	{
		$sql_booking=sql_select("select id, batch_no, booking_no from pro_batch_create_mst");
		foreach ($sql_booking as $row)
		{
			$batch_arr[$row[csf("id")]]["batch_no"]=$row[csf("batch_no")];
			$batch_arr[$row[csf("id")]]["booking_no"]=$row[csf("booking_no")];
		}
	}

	ob_start();
	if($cbo_report_type==1) // Knit Finish Start
	{
		$sql_query="Select a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, b.id as po_id, b.po_number, b.grouping as ref_no, b.file_no, b.pub_shipment_date, c.prod_id, c.pi_wo_batch_no as batch_id, d.color_id, d.entry_form, d.trans_type, d.quantity 
		from  wo_po_details_master a, wo_po_break_down b, order_wise_pro_details d, inv_transaction c
		where a.job_no=b.job_no_mst and b.id=d.po_breakdown_id and d.trans_id=c.id and d.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and d.entry_form in (7,14,15,18,37,46,52,66,68,71,126,134) and c.item_category=2 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.company_name=$cbo_company_id $sql_cond $batch_booking_cond";
		
		//echo $sql_query;die;
		$nameArray=sql_select($sql_query);
		$dtls_data=array();
		foreach($nameArray as $row)
		{
			$all_prod_id[$row[csf("prod_id")]]=$row[csf("prod_id")];
			$dtls_data[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
			$dtls_data[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]["job_no"]=$row[csf("job_no")];
			$dtls_data[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]["buyer_name"]=$row[csf("buyer_name")];
			$dtls_data[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
			
			$dtls_data[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]["po_id"]=$row[csf("po_id")];
			$dtls_data[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]["po_number"]=$row[csf("po_number")];
			$dtls_data[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]["ref_no"]=$row[csf("ref_no")];
			$dtls_data[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]["file_no"]=$row[csf("file_no")];
			
			$dtls_data[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]["pub_shipment_date"]=$row[csf("pub_shipment_date")];
			$dtls_data[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]["prod_id"]=$row[csf("prod_id")];
			$dtls_data[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]["batch_id"]=$row[csf("batch_id")];
			$dtls_data[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]["color_id"]=$row[csf("color_id")];
			
			$dtls_data[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]["entry_form"]=$row[csf("entry_form")];
			$dtls_data[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]["trans_type"]=$row[csf("trans_type")];
			
			if($prod_check[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]=="")
			{
				$prod_check[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]=$row[csf("prod_id")];
				$dtls_data[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]["all_prod_id"].=$row[csf("prod_id")].",";
				if($row[csf("batch_id")])
				{
					$dtls_data[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]["all_batch_id"].=$row[csf("batch_id")].",";
				}
				$dtls_data[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]["all_po_id"].=$row[csf("po_id")].",";
			}
			
			if($row[csf("entry_form")]==7 || $row[csf("entry_form")]==37 || $row[csf("entry_form")]==66 || $row[csf("entry_form")]==68)
			{
				$dtls_data[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]["rcv_quantity"]+=$row[csf("quantity")];
			}
			else if($row[csf("entry_form")]==46)
			{
				$dtls_data[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]["rcv_rtn_quantity"]+=$row[csf("quantity")];
			}
			else if($row[csf("entry_form")]==18 || $row[csf("entry_form")]==71)
			{
				$dtls_data[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]["issue_quantity"]+=$row[csf("quantity")];
			}
			else if($row[csf("entry_form")]==52 || $row[csf("entry_form")]==126)
			{
				$dtls_data[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]["issue_rtn_quantity"]+=$row[csf("quantity")];
			}
			else if( $row[csf("trans_type")]==5 && ($row[csf("entry_form")]==14 || $row[csf("entry_form")]==15 || $row[csf("entry_form")]==134))
			{
				$dtls_data[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]["rcv_transfer_quantity"]+=$row[csf("quantity")];
			}
			else if( $row[csf("trans_type")]==6 && ($row[csf("entry_form")]==14 || $row[csf("entry_form")]==15 || $row[csf("entry_form")]==134))
			{
				$dtls_data[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]["issue_transfer_quantity"]+=$row[csf("quantity")];
			}
		}
		//echo "<pre>";print_r($dtls_data);die;
		if(count($all_prod_id)>0)
		{
			$product_array=array();
			$sql_product="select id, detarmination_id, gsm, dia_width, color from product_details_master where item_category_id=2 and company_id=$cbo_company_id and status_active=1 and is_deleted=0 ".where_con_using_array($all_prod_id,0,'id');//and id in(".implode(",",$all_prod_id)."
			$sql_product_result=sql_select($sql_product);
			foreach( $sql_product_result as $row )
			{
				$product_array[$row[csf('id')]]['color']=$row[csf('color')];
				$product_array[$row[csf('id')]]['detarmination_id']=$row[csf('detarmination_id')];
				$product_array[$row[csf('id')]]['gsm']=$row[csf('gsm')];
				$product_array[$row[csf('id')]]['dia_width']=$row[csf('dia_width')];
			}
			unset($sql_product_result);
		}
		
		
		$composition_arr=$construction_arr=array();
		$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
		$data_array=sql_select($sql_deter);
		if(count($data_array)>0)
		{
			foreach( $data_array as $row )
			{
				$construction_arr[$row[csf('id')]]=$row[csf('construction')];
				if(array_key_exists($row[csf('id')],$composition_arr))
				{
					$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				}
				else
				{
					$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				}
			}
		}
		
		?>
		<fieldset style="width:1720px;">
			<table cellpadding="0" cellspacing="0" width="1720">
				<tr class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="21" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="21" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="21" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
				</tr>
			</table>
			<table width="1720" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">            	
				<thead>
					<th width="30">SL</th>
					<th width="60">Prod. Id</th>
					<th width="100">Job No</th>
					<th width="100">Style No</th>
					<th width="120">Buyer</th>
					<th width="100">Order No</th> 
					<th width="100">Booking No</th>
					<th width="70">File No</th>
					<th width="70">Ref. No</th>
					<th width="120">Construction</th>
					<th width="170">Composition</th>
					<th width="80">Color</th>
					<th width="60">GSM</th>
					<th width="60">Dia</th>
					<th width="100">Batch No</th>
					<th width="80">Receive Qty</th>
					<th width="80">Receive Rtn Qty</th>
					<th width="80">Issue Qty</th>
					<th>Issue Return Qty</th>
				</thead>
			</table>
			<div style="width:1720px; max-height:350px; overflow-y:scroll;" id="scroll_body">
				<table width="1700" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" > 
					<?
					$i=1; $k=1;$total_rec_return_qnty=0;$total_issue_ret_qnty=0;$issue_trns_out_qnty=0;$rec_trns_in_qnty=0;
					//$fin_color_array=array(); $fin_color_data_arr=array();$checkbuyerArr=array(); $po_break_id_arr=array(); $booking_po_data_arr=array();
					//print_r($po_break_id_arr);
					
					
					//print_r($booking_po_data_arr);
					foreach ($dtls_data as $prod_id=>$prod_val)
					{
						foreach($prod_val as $batch_id=>$batch_val)
						{
							foreach($batch_val as $po_id=>$po_val)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$rcv_qnty=$po_val["rcv_quantity"]+$po_val["rcv_transfer_quantity"];
								$issue_qnty=$po_val["issue_quantity"]+$po_val["issue_transfer_quantity"];
								$prod_ids=implode(",",array_unique(explode(",",chop($po_val["all_prod_id"],","))));
								$batch_ids=implode(",",array_unique(explode(",",chop($po_val["all_batch_id"],","))));
								$po_ids=implode(",",array_unique(explode(",",chop($po_val["all_po_id"],","))));
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
									<td width="30" align="center"><? echo $i; ?></td>
									<td width="60" align="center"><? echo $prod_id; ?></td>
									<td width="100"><p><? echo $po_val["job_no"]; ?>&nbsp;</p></td>
									<td width="100"><p><? echo $po_val["style_ref_no"]; ?>&nbsp;</p></td>
									<td width="120" title="<? echo $po_val["buyer_name"]; ?>"><p><? echo $buyer_arr[$po_val["buyer_name"]]; ?>&nbsp;</p></td>
									<td width="100"><p><? echo $po_val["po_number"]; ?>&nbsp;</p></td> 
									<td width="100"><p><? echo $batch_arr[$batch_id]["booking_no"]; ?>&nbsp;</p></td>
									<td width="70"><p><? echo $po_val["file_no"]; ?>&nbsp;</p></td>
									<td width="70"><p><? echo $po_val["ref_no"]; ?>&nbsp;</p></td>
									<td width="120"><P><? echo $construction_arr[$product_array[$prod_id]['detarmination_id']]; ?>&nbsp;</P></td>
									<td width="170"><P><? echo $composition_arr[$product_array[$prod_id]['detarmination_id']]; ?>&nbsp;</P></td>
									<td width="80"><p><? echo $color_arr[$product_array[$prod_id]['color']]; ?>&nbsp;</p></td>
									<td width="60" align="center"><p><? echo $product_array[$prod_id]['gsm']; ?>&nbsp;</p></td>
									<td width="60" align="center"><p><? echo $product_array[$prod_id]['dia_width']; ?>&nbsp;</p></td>
									<td width="100"><p><? echo $batch_arr[$batch_id]["batch_no"]; ?>&nbsp;</p></td>
									<td width="80" align="right"><a href='#report_details' onClick="openmypage('<? echo $prod_ids; ?>','<? echo $po_ids; ?>','<? echo $batch_ids; ?>','<? echo $cbo_store_id; ?>','<? echo $txt_date_from; ?>','<? echo $txt_date_to; ?>','2','total_rec_popup');"><? echo number_format($rcv_qnty,2);?></a></td>
									<td width="80" align="right"><? echo number_format($po_val["rcv_rtn_quantity"],2);?></td>
									<td width="80" align="right"><a href='#report_details' onClick="openmypage('<? echo $prod_ids; ?>','<? echo $po_ids; ?>','<? echo $batch_ids; ?>','<? echo $cbo_store_id; ?>','<? echo $txt_date_from; ?>','<? echo $txt_date_to; ?>','2','total_issue_popup');"><? echo number_format($issue_qnty,2);?></a></td>
									<td align="right"><? echo number_format($po_val["issue_rtn_quantity"],2);?></td>
								</tr>
								<?
								$i++;	
								$total_rcv_qnty+=$rcv_qnty;
								$total_rcv_rtn_quantity+=$po_val["rcv_rtn_quantity"];
								$total_issue_qnty+=$issue_qnty;
								$total_issue_rtn_quantity+=$po_val["issue_rtn_quantity"];
							}
						}
					}
					?>
					</table>
					<table width="1700" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" > 
						<tfoot>
							<th width="30">&nbsp;</th>
                            <th width="60">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="120">&nbsp;</th>
                            <th width="100">&nbsp;</th> 
                            <th width="100">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="120">&nbsp;</th>
                            <th width="170">&nbsp;</th>
                            <th width="80">&nbsp;</th>
                            <th width="60">&nbsp;</th>
                            <th width="60">&nbsp;</th>
                            <th width="100" align="right">Total:</th>
                            <th width="80" id="value_total_rcv_qnty" align="right"><? echo number_format($total_rcv_qnty,2);?></th>
                            <th width="80" id="value_total_rcv_rtn_quantity" align="right"><? echo number_format($total_rcv_rtn_quantity,2);?></th>
                            <th width="80" id="value_total_issue_qnty" align="right"><? echo number_format($total_issue_qnty,2);?></th>
                            <th id="value_total_issue_rtn_quantity" align="right"><? echo number_format($total_issue_rtn_quantity,2);?></th>
						</tfoot>
					</table> 
				</div>  
		</fieldset>         
		<?
	}//Knit end
	else if($cbo_report_type==2) // Woven Finish Start
	{
		$sql_query="Select a.job_no_prefix_num, a.job_no, a.buyer_name, a.style_ref_no, b.id as po_id, b.po_number, b.grouping as ref_no, b.file_no, b.pub_shipment_date, c.prod_id, c.pi_wo_batch_no as batch_id, d.color_id, d.entry_form, d.trans_type, d.quantity 
		from  wo_po_details_master a, wo_po_break_down b, order_wise_pro_details d, inv_transaction c
		where a.job_no=b.job_no_mst and b.id=d.po_breakdown_id and d.trans_id=c.id and d.trans_id!=0 and a.status_active=1 and a.is_deleted=0 and d.entry_form in (17,19,195,202,196,209) and c.item_category=3 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and a.company_name=$cbo_company_id $sql_cond $batch_booking_cond";
		
		//echo $sql_query;die;
		/*
		receive=17
		receive rtn=202
		issue=19,195
		issue return=196,209
		*/
		
		$nameArray=sql_select($sql_query);
		$dtls_data=array();
		foreach($nameArray as $row)
		{
			$all_prod_id[$row[csf("prod_id")]]=$row[csf("prod_id")];
			$dtls_data[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]["job_no_prefix_num"]=$row[csf("job_no_prefix_num")];
			$dtls_data[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]["job_no"]=$row[csf("job_no")];
			$dtls_data[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]["buyer_name"]=$row[csf("buyer_name")];
			$dtls_data[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]["style_ref_no"]=$row[csf("style_ref_no")];
			
			$dtls_data[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]["po_id"]=$row[csf("po_id")];
			$dtls_data[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]["po_number"]=$row[csf("po_number")];
			$dtls_data[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]["ref_no"]=$row[csf("ref_no")];
			$dtls_data[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]["file_no"]=$row[csf("file_no")];
			
			$dtls_data[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]["pub_shipment_date"]=$row[csf("pub_shipment_date")];
			$dtls_data[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]["prod_id"]=$row[csf("prod_id")];
			$dtls_data[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]["batch_id"]=$row[csf("batch_id")];
			$dtls_data[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]["color_id"]=$row[csf("color_id")];
			
			$dtls_data[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]["entry_form"]=$row[csf("entry_form")];
			$dtls_data[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]["trans_type"]=$row[csf("trans_type")];
			
			
			if($prod_check[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]=="")
			{
				$prod_check[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]=$row[csf("prod_id")];
				$dtls_data[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]["all_prod_id"].=$row[csf("prod_id")].",";
				if($row[csf("batch_id")])
				{
					$dtls_data[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]["all_batch_id"].=$row[csf("batch_id")].",";
				}
				$dtls_data[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]["all_po_id"].=$row[csf("po_id")].",";
			}
			
			if($row[csf("entry_form")]==17)
			{
				$dtls_data[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]["rcv_quantity"]+=$row[csf("quantity")];
			}
			else if($row[csf("entry_form")]==202)
			{
				$dtls_data[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]["rcv_rtn_quantity"]+=$row[csf("quantity")];
			}
			else if($row[csf("entry_form")]==19 || $row[csf("entry_form")]==95)
			{
				$dtls_data[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]["issue_quantity"]+=$row[csf("quantity")];
			}
			else if($row[csf("entry_form")]==196 || $row[csf("entry_form")]==209)
			{
				$dtls_data[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]["issue_rtn_quantity"]+=$row[csf("quantity")];
			}
			
			/*else if( $row[csf("trans_type")]==5 && ($row[csf("entry_form")]==14 || $row[csf("entry_form")]==15 || $row[csf("entry_form")]==134))
			{
				$dtls_data[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]["rcv_transfer_quantity"]+=$row[csf("quantity")];
			}
			else if( $row[csf("trans_type")]==6 && ($row[csf("entry_form")]==14 || $row[csf("entry_form")]==15 || $row[csf("entry_form")]==134))
			{
				$dtls_data[$row[csf("prod_id")]][$row[csf("batch_id")]][$row[csf("po_id")]]["issue_transfer_quantity"]+=$row[csf("quantity")];
			}*/
		}
		//echo "<pre>";print_r($dtls_data);die;
		if(count($all_prod_id)>0)
		{
			$product_array=array();
			$sql_product="select id, detarmination_id, gsm, dia_width, color from product_details_master where item_category_id=3 and company_id=$cbo_company_id and status_active=1 and is_deleted=0 and id in(".implode(",",$all_prod_id).")";
			$sql_product_result=sql_select($sql_product);
			foreach( $sql_product_result as $row )
			{
				$product_array[$row[csf('id')]]['color']=$row[csf('color')];
				$product_array[$row[csf('id')]]['detarmination_id']=$row[csf('detarmination_id')];
				$product_array[$row[csf('id')]]['gsm']=$row[csf('gsm')];
				$product_array[$row[csf('id')]]['dia_width']=$row[csf('dia_width')];
			}
			unset($sql_product_result);
		}
		
		
		$composition_arr=$construction_arr=array();
		$sql_deter="select a.id, a.construction, b.copmposition_id, b.percent from lib_yarn_count_determina_mst a, lib_yarn_count_determina_dtls b where a.id=b.mst_id";
		$data_array=sql_select($sql_deter);
		if(count($data_array)>0)
		{
			foreach( $data_array as $row )
			{
				$construction_arr[$row[csf('id')]]=$row[csf('construction')];
				if(array_key_exists($row[csf('id')],$composition_arr))
				{
					$composition_arr[$row[csf('id')]]=$composition_arr[$row[csf('id')]]." ".$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				}
				else
				{
					$composition_arr[$row[csf('id')]]=$composition[$row[csf('copmposition_id')]]." ".$row[csf('percent')]."%";
				}
			}
		}
		?>
		<fieldset style="width:1720px;">
			<table cellpadding="0" cellspacing="0" width="1720">
				<tr class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="21" style="font-size:18px"><strong><? echo $report_title; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="21" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
				</tr>
				<tr  class="form_caption" style="border:none;">
					<td align="center" width="100%" colspan="21" style="font-size:14px"><strong> <? if($date_from!="") echo "Upto : ".change_date_format(str_replace("'","",$txt_date_from)) ;?></strong></td>
				</tr>
			</table>
			<table width="1720" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table">            	
				<thead>
					<th width="30">SL</th>
					<th width="60">Prod. Id</th>
					<th width="100">Job No</th>
					<th width="100">Style No</th>
					<th width="120">Buyer</th>
					<th width="100">Order No</th> 
					<th width="100">Booking No</th>
					<th width="70">File No</th>
					<th width="70">Ref. No</th>
					<th width="120">Construction</th>
					<th width="170">Composition</th>
					<th width="80">Color</th>
					<th width="60">GSM</th>
					<th width="60">Dia</th>
					<th width="100">Batch No</th>
					<th width="80">Receive Qty</th>
					<th width="80">Receive Rtn Qty</th>
					<th width="80">Issue Qty</th>
					<th>Issue Return Qty</th>
				</thead>
			</table>
			<div style="width:1720px; max-height:350px; overflow-y:scroll;" id="scroll_body">
				<table width="1700" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" id="table_body" > 
					<?
					$i=1; $k=1;$total_rec_return_qnty=0;$total_issue_ret_qnty=0;$issue_trns_out_qnty=0;$rec_trns_in_qnty=0;
					$fin_color_array=array(); $fin_color_data_arr=array();$checkbuyerArr=array(); $po_break_id_arr=array(); $booking_po_data_arr=array();
					//print_r($po_break_id_arr);
					if($txt_search_booking =='')
					{
						foreach ($nameArray as $row)
						{
							$po_break_id_arr[]=$row[csf('po_id')];
							$color_id_arr[]=$row[csf('color_id')];
						}
						$sql_booking_po_query=sql_select("select po_break_down_id, booking_no, fabric_color_id from wo_booking_dtls where po_break_down_id in(".implode(',',$po_break_id_arr).") and  fabric_color_id in(".implode(',',$color_id_arr).") and status_active=1 and is_deleted=0 and booking_type=1 ");

						foreach ($sql_booking_po_query as $row)
						{
							//$booking_po_data_arr[$row[csf('po_break_down_id')]] = $row[csf('booking_no')];
							$booking_po_data_arr[$row[csf("po_break_down_id")]][$row[csf("fabric_color_id")]]['booking_no']=$row[csf("booking_no")];
						}
					}
					
					//print_r($booking_po_data_arr);
					foreach ($dtls_data as $prod_id=>$prod_val)
					{
						foreach($prod_val as $batch_id=>$batch_val)
						{
							foreach($batch_val as $po_id=>$po_val)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$rcv_qnty=$po_val["rcv_quantity"]+$po_val["rcv_transfer_quantity"];
								$issue_qnty=$po_val["issue_quantity"]+$po_val["issue_transfer_quantity"];
								$prod_ids=implode(",",array_unique(explode(",",chop($po_val["all_prod_id"],","))));
								$batch_ids=implode(",",array_unique(explode(",",chop($po_val["all_batch_id"],","))));
								$po_ids=implode(",",array_unique(explode(",",chop($po_val["all_po_id"],","))));
								
								?>
								<tr bgcolor="<? echo $bgcolor;?>" onClick="change_color('tr<? echo $i;?>','<? echo $bgcolor;?>')" id="tr<? echo $i;?>">
									<td width="30" align="center"><? echo $i; ?></td>
									<td width="60" align="center"><? echo $prod_id; ?></td>
									<td width="100"><p><? echo $po_val["job_no"]; ?>&nbsp;</p></td>
									<td width="100"><p><? echo $po_val["style_ref_no"]; ?>&nbsp;</p></td>
									<td width="120" title="<? echo $po_val["buyer_name"]; ?>"><p><? echo $buyer_arr[$po_val["buyer_name"]]; ?>&nbsp;</p></td>
									<td width="100"><p><? echo $po_val["po_number"]; ?>&nbsp;</p></td> 
									<td width="100"><p><? echo $batch_arr[$batch_id]["booking_no"]; ?>&nbsp;</p></td>
									<td width="70"><p><? echo $po_val["file_no"]; ?>&nbsp;</p></td>
									<td width="70"><p><? echo $po_val["ref_no"]; ?>&nbsp;</p></td>
									<td width="120"><P><? echo $construction_arr[$product_array[$prod_id]['detarmination_id']]; ?>&nbsp;</P></td>
									<td width="170"><P><? echo $composition_arr[$product_array[$prod_id]['detarmination_id']]; ?>&nbsp;</P></td>
									<td width="80"><p><? echo $color_arr[$product_array[$prod_id]['color']]; ?>&nbsp;</p></td>
									<td width="60" align="center"><p><? echo $product_array[$prod_id]['gsm']; ?>&nbsp;</p></td>
									<td width="60" align="center"><p><? echo $product_array[$prod_id]['dia_width']; ?>&nbsp;</p></td>
									<td width="100"><p><? echo $batch_arr[$batch_id]["batch_no"]; ?>&nbsp;</p></td>
									<td width="80" align="right"><a href='#report_details' onClick="openmypage('<? echo $prod_ids; ?>','<? echo $po_ids; ?>','<? echo $batch_ids; ?>','<? echo $cbo_store_id; ?>','<? echo $txt_date_from; ?>','<? echo $txt_date_to; ?>','3','total_rec_popup');"><? echo number_format($rcv_qnty,2);?></a></td>
									<td width="80" align="right"><? echo number_format($po_val["rcv_rtn_quantity"],2);?></td>
									<td width="80" align="right"><a href='#report_details' onClick="openmypage('<? echo $prod_ids; ?>','<? echo $po_ids; ?>','<? echo $batch_ids; ?>','<? echo $cbo_store_id; ?>','<? echo $txt_date_from; ?>','<? echo $txt_date_to; ?>','3','total_issue_popup');"><? echo number_format($issue_qnty,2);?></a></td>
									<td align="right"><? echo number_format($po_val["issue_rtn_quantity"],2);?></td>
								</tr>
								<?
								$i++;	
								$total_rcv_qnty+=$rcv_qnty;
								$total_rcv_rtn_quantity+=$po_val["rcv_rtn_quantity"];
								$total_issue_qnty+=$issue_qnty;
								$total_issue_rtn_quantity+=$po_val["issue_rtn_quantity"];
							}
						}
					}
					?>
					</table>
					<table width="1700" cellpadding="0" cellspacing="0" border="1" rules="all" class="rpt_table" > 
						<tfoot>
							<th width="30">&nbsp;</th>
                            <th width="60">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="120">&nbsp;</th>
                            <th width="100">&nbsp;</th> 
                            <th width="100">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="70">&nbsp;</th>
                            <th width="120">&nbsp;</th>
                            <th width="170">&nbsp;</th>
                            <th width="80">&nbsp;</th>
                            <th width="60">&nbsp;</th>
                            <th width="60">&nbsp;</th>
                            <th width="100" align="right">Total:</th>
                            <th width="80" id="value_total_rcv_qnty" align="right"><? echo number_format($total_rcv_qnty,2);?></th>
                            <th width="80" id="value_total_rcv_rtn_quantity" align="right"><? echo number_format($total_rcv_rtn_quantity,2);?></th>
                            <th width="80" id="value_total_issue_qnty" align="right"><? echo number_format($total_issue_qnty,2);?></th>
                            <th id="value_total_issue_rtn_quantity" align="right"><? echo number_format($total_issue_rtn_quantity,2);?></th>
						</tfoot>
					</table> 
				</div>  
		</fieldset>         
		<?         
    }
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
    echo "$html**$filename"; 
    exit();
}




if($action=="total_rec_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo "test";die();

	?>
	<fieldset style="width:600px; margin-left:3px">
		<script>
			/*function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}*/

			$(document).ready(function(e) {
				setFilterGrid('tbl_list_search',-1);
			});
			
		</script>	
		<?
		ob_start();
		?>   
		<div id="scroll_body" align="center">
			<!--<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0"> 
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>-->
			<table border="1" class="rpt_table" rules="all" width="600" cellpadding="0" cellspacing="0" align="center" >
				<thead>
					<tr>
						<th colspan="6">Receive Details</th>
					</tr>
					<tr>
						<th width="50">Sl</th>
						<th width="110">Trans. Ref.</th>
						<th width="90">Trans. Date</th>
						<th width="100">Challan No</th>
						<th width="130">Party Name</th>
						<th>Recived Qty</th>
					</tr>
				</thead>
				<tbody id="tbl_list_search">
					<?
					$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
					$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");
					$store_id=str_replace("'","",$store_id);
					$store_cond="";
					if($store_id>0) $store_cond=" and b.store_id=$store_id";
					if($item_category_id==2)
					{
						$entry_form="7,37,66,68";
					}
					else
					{
						$entry_form="17";
					}
					$date_cond= "";
					if( $date_form !="" && $date_to !="") $date_cond= " and b.transaction_date between '$date_form' and '$date_to'";
					$mrr_sql="select a.recv_number, a.receive_date, a.knitting_source, a.knitting_company, a.challan_no, sum(c.quantity) as quantity 
					from  inv_receive_master a, inv_transaction b, order_wise_pro_details c 
					where a.id=b.mst_id and b.id=c.trans_id and a.entry_form in ($entry_form) and c.entry_form in ($entry_form) and a.item_category=$item_category_id and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and b.prod_id in($prod_id) and b.pi_wo_batch_no in($batch_id) and b.transaction_type=1 and c.trans_id >0 and c.trans_type=1 $store_cond $date_cond
					group by a.recv_number, a.receive_date, a.knitting_source, a.knitting_company, a.challan_no";
					
					$i=1;
					//echo $mrr_sql;//die;
					
					$dtlsArray=sql_select($mrr_sql);
					
					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
						
						$tot_reject=$row[csf('returnable_qnty')];
						if($row[csf('knitting_source')]==1)
						{
							$knitting_company=$company_arr[$row[csf('knitting_company')]];
						}
						else
						{
							$knitting_company=$supplier_name_arr[$row[csf('knitting_company')]];
						}
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td align="center"><p><? echo $i; ?></p></td>
							<td><p><? echo $row[csf('recv_number')]; ?></p></td> 
							<td align="center"><p><? echo change_date_format($row[csf('receive_date')]); ?>&nbsp;</p></td>
                            <td><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
							<td><p><? echo $knitting_company; ?>&nbsp;</p></td>
                            <td align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
						</tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$i++;
					}
					
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="5" align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty,2); ?> </td>
					</tr>
                   
				</tfoot>

			</table>
			<br>
			<table border="1" class="rpt_table" rules="all" width="600" cellpadding="0" cellspacing="0" align="left">
				<thead>
					<tr>
						<th colspan="6">Transfer In Details</th>
					</tr>
					<tr>
						<th width="50">Sl</th>
						<th width="110">Trans. Ref.</th>
						<th width="90">Trans. Date</th>
						<th width="100">Challan No</th>
						<th width="130">Party Name</th>
						<th>Recived Qty</th>
					</tr>
				</thead>
				<tbody>
					<?
					if($item_category_id==2)
					{
						$store_transf_cond="";
						if($store_id>0) $store_transf_cond=" and b.to_store=$store_id";
						if( $date_form !="" && $date_to !="") $date_cond= " and b.transaction_date between '$date_form' and '$date_to'";
						$sql_transfer_in="select a.transfer_system_id, a.transfer_date, a.challan_no, 0 as knitting_source, 0 as knitting_company, sum(c.quantity) as quantity 
						from  inv_item_transfer_mst a, inv_transaction b, order_wise_pro_details c 
						where a.id=b.mst_id and b.id=c.trans_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form in(14,15,134) and c.entry_form in(14,15,134) and a.company_id=$companyID and c.po_breakdown_id in($po_id) and b.prod_id in($prod_id) and b.pi_wo_batch_no in($batch_id) and b.transaction_type=5 and c.trans_id >0 and c.trans_type=5 and a.transfer_criteria in(1,2,4) $store_cond  $date_cond 
						group by a.transfer_system_id, a.transfer_date, a.challan_no";
						//echo $sql_transfer_in;die;
						$transfer_data=sql_select($sql_transfer_in);
						//echo "<pre>";print_r($transfer_data);die;
						$i=1;
						foreach($transfer_data as $row)
						{
							//echo "jahid";die;
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr3_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td align="center"><p><? echo $i; ?></p></td>
								<td><p><? echo $row[csf('transfer_system_id')]; ?></p></td> 
								<td align="center"><p><? echo change_date_format($row[csf('transfer_date')]); ?>&nbsp;</p></td>
								<td><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
								<td><p>&nbsp;</p></td>
								<td align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
							</tr>
							<?
							$tot_trans_qty+=$row[csf('quantity')];
							$i++;
						}
					}
					else
					{
						//##### difine leter  #####//
					}
					
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="5" align="right">Total</td>
						<td align="right"><? echo number_format($tot_trans_qty,2); ?> </td>
					</tr>
					<tr class="tbl_bottom">
						<td colspan="5" align="right">Total Receive Balance</td>
						<td align="right">&nbsp;<? $tot_balance=$tot_qty+$tot_trans_qty; echo number_format($tot_balance,2); ?>&nbsp;</td>
					</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<?
	exit();
}//Knit Finish end

if($action=="total_issue_popup")
{
	echo load_html_head_contents("Receive Info", "../../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo "test";die();

	?>
	<fieldset style="width:600px; margin-left:3px">
		<script>
			/*function print_window()
			{
				var w = window.open("Surprise", "#");
				var d = w.document.open();
				d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTsD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
					'<html><head><link rel="stylesheet" href="../../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('scroll_body').innerHTML+'</body</html>');
				d.close();
			}*/

			$(document).ready(function(e) {
				setFilterGrid('tbl_list_search',-1);
			});
			
		</script>	
		<?
		ob_start();
		?>   
		<div id="scroll_body" align="center">
			<!--<table border="0" class="rpt_table" rules="all" cellpadding="0" cellspacing="0"> 
				<tr>
					<td><input  type="button" value="Print Preview" onClick="print_window()" style="width:90px"  class="formbutton"/> &nbsp; </td>
					<td> <div id="report_container"> </div> </td>
				</tr>
			</table>-->
			<table border="1" class="rpt_table" rules="all" width="600" cellpadding="0" cellspacing="0" align="center" >
				<thead>
					<tr>
						<th colspan="6">Receive Details</th>
					</tr>
					<tr>
						<th width="50">Sl</th>
						<th width="110">Trans. Ref.</th>
						<th width="90">Trans. Date</th>
						<th width="100">Challan No</th>
						<th width="130">Party Name</th>
						<th>Recived Qty</th>
					</tr>
				</thead>
				<tbody id="tbl_list_search">
					<?
					$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name"  );
					$supplier_name_arr=return_library_array( "select id, supplier_name from  lib_supplier", "id", "supplier_name");
					$store_id=str_replace("'","",$store_id);
					$store_cond="";
					if($store_id>0) $store_cond=" and b.store_id=$store_id";
					if($item_category_id==2)
					{
						$entry_form="18,71";
					}
					else
					{
						$entry_form="19,195";
					}
					if( $date_form !="" && $date_to !="") $date_cond= " and b.transaction_date between '$date_form' and '$date_to'";
					$mrr_sql="select a.issue_number, a.issue_date, a.knit_dye_source, a.knit_dye_company, a.challan_no, sum(c.quantity) as quantity 
					from  inv_issue_master a, inv_transaction b, order_wise_pro_details c 
					where a.id=b.mst_id and b.id=c.trans_id and a.entry_form in ($entry_form) and c.entry_form in ($entry_form) and a.item_category=$item_category_id and a.is_deleted=0 and a.status_active=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.po_breakdown_id in($po_id) and a.company_id='$companyID' and b.prod_id in($prod_id) and b.pi_wo_batch_no in($batch_id) and b.transaction_type=2 and c.trans_id >0 and c.trans_type=2 $store_cond $date_cond
					group by a.issue_number, a.issue_date, a.knit_dye_source, a.knit_dye_company, a.challan_no";
					
					$i=1;
					//echo $mrr_sql;//die;
					
					$dtlsArray=sql_select($mrr_sql);
					
					foreach($dtlsArray as $row)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
						
						$tot_reject=$row[csf('returnable_qnty')];
						if($row[csf('knit_dye_source')]==1)
						{
							$knitting_company=$company_arr[$row[csf('knit_dye_company')]];
						}
						else
						{
							$knitting_company=$supplier_name_arr[$row[csf('knit_dye_company')]];
						}
						?>
						<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
							<td align="center"><p><? echo $i; ?></p></td>
							<td><p><? echo $row[csf('issue_number')]; ?></p></td> 
							<td align="center"><p><? echo change_date_format($row[csf('issue_date')]); ?>&nbsp;</p></td>
                            <td><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
							<td><p><? echo $knitting_company; ?>&nbsp;</p></td>
                            <td align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
						</tr>
						<?
						$tot_qty+=$row[csf('quantity')];
						$i++;
					}
					
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="5" align="right">Total</td>
						<td align="right"><? echo number_format($tot_qty,2); ?> </td>
					</tr>
                   
				</tfoot>

			</table>
			<br>
			<table border="1" class="rpt_table" rules="all" width="600" cellpadding="0" cellspacing="0" align="left">
				<thead>
					<tr>
						<th colspan="6">Transfer In Details</th>
					</tr>
					<tr>
						<th width="50">Sl</th>
						<th width="110">Trans. Ref.</th>
						<th width="90">Trans. Date</th>
						<th width="100">Challan No</th>
						<th width="130">Party Name</th>
						<th>Recived Qty</th>
					</tr>
				</thead>
				<tbody>
					<?
					if($item_category_id==2)
					{
						$store_transf_cond="";
						if($store_id>0) $store_transf_cond=" and b.to_store=$store_id";
						if( $date_form !="" && $date_to !="") $date_cond= " and b.transaction_date between '$date_form' and '$date_to'";
						$sql_transfer_in="select a.transfer_system_id, a.transfer_date, a.challan_no, 0 as knitting_source, 0 as knitting_company, sum(c.quantity) as quantity 
						from  inv_item_transfer_mst a, inv_transaction b, order_wise_pro_details c 
						where a.id=b.mst_id and b.id=c.trans_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form in(14,15,134) and c.entry_form in(14,15,134) and a.company_id=$companyID and c.po_breakdown_id in($po_id) and b.prod_id in($prod_id) and b.pi_wo_batch_no in($batch_id) and b.transaction_type=6 and c.trans_id >0 and c.trans_type=6 and a.transfer_criteria in(1,2,4) $store_cond $date_cond  
						group by a.transfer_system_id, a.transfer_date, a.challan_no";
						//echo $sql_transfer_in;die;
						$transfer_data=sql_select($sql_transfer_in);
						//echo "<pre>";print_r($transfer_data);die;
						$i=1;
						foreach($transfer_data as $row)
						{
							//echo "jahid";die;
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";	
							?>
							<tr bgcolor="<? echo  $bgcolor; ?>" onClick="change_color('tr3_<? echo $i; ?>','<? echo $bgcolor;?>')" id="tr_<? echo $i;?>">
								<td align="center"><p><? echo $i; ?></p></td>
								<td><p><? echo $row[csf('transfer_system_id')]; ?></p></td> 
								<td align="center"><p><? echo change_date_format($row[csf('transfer_date')]); ?>&nbsp;</p></td>
								<td><p><? echo $row[csf('challan_no')]; ?>&nbsp;</p></td>
								<td><p>&nbsp;</p></td>
								<td align="right"><p><? echo number_format($row[csf('quantity')],2); ?></p></td>
							</tr>
							<?
							$tot_trans_qty+=$row[csf('quantity')];
							$i++;
						}
					}
					else
					{
						//##### difine leter  #####//
					}
					
					?>
				</tbody>
				<tfoot>
					<tr class="tbl_bottom">
						<td colspan="5" align="right">Total</td>
						<td align="right"><? echo number_format($tot_trans_qty,2); ?> </td>
					</tr>
					<tr class="tbl_bottom">
						<td colspan="5" align="right">Total Receive Balance</td>
						<td align="right">&nbsp;<? $tot_balance=$tot_qty+$tot_trans_qty; echo number_format($tot_balance,2); ?>&nbsp;</td>
					</tr>
				</tfoot>
			</table>
		</div>
	</fieldset>
	<?
	exit();
}


?>