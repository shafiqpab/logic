<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];



if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
	$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$itemGroup_arr=return_library_array( "select id,item_name from lib_item_group where item_category in (5,6,7,22,23) and status_active=1 and is_deleted=0 order by item_name",'id','item_name');
	//$yarnCount_arr=return_library_array( "select id,yarn_count from  lib_yarn_count where is_deleted=0 and status_active=1",'id','yarn_count');
	//$color_arr=return_library_array( "select id, color_name from lib_color",'id','color_name');
	$bank_arr=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');
	//("cbo_company_name*cbo_year*cbo_based_on*txt_search_no*txt_date_from*txt_date_to","../../")
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_year=str_replace("'","",$cbo_year);
	$type=str_replace("'","",$type);
	//$cbo_based_on=str_replace("'","",$cbo_based_on);
	$txt_search_no=str_replace("'","",$txt_search_no);
	$txt_date_from=str_replace("'","",$txt_date_from);
	$txt_date_to=str_replace("'","",$txt_date_to);
	//$cbo_receive_status=str_replace("'","",$cbo_receive_status);
	
	$str_cond=$str_cond_independ="";
	
	//echo $cbo_based_on ; die;
	// req condition check here
	if($db_type==0)
	{
		$txt_date_from=change_date_format($txt_date_from,'yyyy-mm-dd');
		$txt_date_to=change_date_format($txt_date_to,'yyyy-mm-dd');
	}
	else if($db_type==2)
	{
		$txt_date_from=change_date_format($txt_date_from,'','',-1);
		$txt_date_to=change_date_format($txt_date_to,'','',-1);
	}

	if ($type==1)  // show
	{	
		$str_cond=$str_cond_independ=$pi_cond=$btb_cond="";
		if ($txt_search_no != "")
		{
			$pi_cond.=" and a.pi_number like '%$txt_search_no%'";
		}
		if($txt_date_from!="" && $txt_date_to!="") $pi_cond.=" and a.pi_date between '$txt_date_from' and '$txt_date_to'";
		$sql_pi="select a.id as pi_id, a.supplier_id as pi_suplier, a.pi_number, a.pi_date, a.last_shipment_date, a.currency_id, b.work_order_id, b.work_order_dtls_id,b.item_prod_id,b.item_group, b.item_description, b.uom, b.quantity, b.rate, b.amount
			,d.id as btb_id, d.lc_number, d.lc_date, d.issuing_bank_id, d.payterm_id, d.tenor, d.lc_value
		 from  com_pi_item_details b ,com_pi_master_details a
			left join com_btb_lc_pi c on c.pi_id=a.id and c.is_deleted=0 and c.status_active=1
			left join com_btb_lc_master_details d on d.id=c.com_btb_lc_master_details_id and d.is_deleted=0 and d.status_active=1
		where a.item_category_id in (5,6,7,22,23)  and a.id=b.pi_id and a.importer_id=$cbo_company_name and a.status_active=1 and b.status_active=1  $pi_cond order by a.pi_date";
		$sql_pi_res=sql_select($sql_pi);
		// and a.goods_rcv_status<>1
		
		$pi_data_arr=array();
		foreach($sql_pi_res as $row)
		{
			//if($row[csf("work_order_dtls_id")]) $pi_wo_dtls_id_all[]=$row[csf("work_order_dtls_id")];
			//$pi_id_arr[]=$row[csf("pi_id")];
			$pi_data_arr[$row[csf("uom")]][$row[csf("pi_suplier")]][$row[csf("pi_id")]][$row[csf("item_prod_id")]][$row[csf("btb_id")]]["pi_number"]=$row[csf("pi_number")];
			$pi_data_arr[$row[csf("uom")]][$row[csf("pi_suplier")]][$row[csf("pi_id")]][$row[csf("item_prod_id")]][$row[csf("btb_id")]]["pi_date"]=$row[csf("pi_date")];
			$pi_data_arr[$row[csf("uom")]][$row[csf("pi_suplier")]][$row[csf("pi_id")]][$row[csf("item_prod_id")]][$row[csf("btb_id")]]["last_shipment_date"]=$row[csf("last_shipment_date")];
			$pi_data_arr[$row[csf("uom")]][$row[csf("pi_suplier")]][$row[csf("pi_id")]][$row[csf("item_prod_id")]][$row[csf("btb_id")]]["currency_id"]=$row[csf("currency_id")];
			$pi_data_arr[$row[csf("uom")]][$row[csf("pi_suplier")]][$row[csf("pi_id")]][$row[csf("item_prod_id")]][$row[csf("btb_id")]]["item_description"]=$row[csf("item_description")];
			$pi_data_arr[$row[csf("uom")]][$row[csf("pi_suplier")]][$row[csf("pi_id")]][$row[csf("item_prod_id")]][$row[csf("btb_id")]]["quantity"]+=$row[csf("quantity")];
			$pi_data_arr[$row[csf("uom")]][$row[csf("pi_suplier")]][$row[csf("pi_id")]][$row[csf("item_prod_id")]][$row[csf("btb_id")]]["amount"]+=$row[csf("amount")];
			$pi_data_arr[$row[csf("uom")]][$row[csf("pi_suplier")]][$row[csf("pi_id")]][$row[csf("item_prod_id")]][$row[csf("btb_id")]]["item_group"]=$row[csf("item_group")];
			$pi_data_arr[$row[csf("uom")]][$row[csf("pi_suplier")]][$row[csf("pi_id")]][$row[csf("item_prod_id")]][$row[csf("btb_id")]]["lc_number"]=$row[csf("lc_number")];
			$pi_data_arr[$row[csf("uom")]][$row[csf("pi_suplier")]][$row[csf("pi_id")]][$row[csf("item_prod_id")]][$row[csf("btb_id")]]["lc_date"]=$row[csf("lc_date")];
			$pi_data_arr[$row[csf("uom")]][$row[csf("pi_suplier")]][$row[csf("pi_id")]][$row[csf("item_prod_id")]][$row[csf("btb_id")]]["issuing_bank_id"]=$row[csf("issuing_bank_id")];
			$pi_data_arr[$row[csf("uom")]][$row[csf("pi_suplier")]][$row[csf("pi_id")]][$row[csf("item_prod_id")]][$row[csf("btb_id")]]["payterm_id"]=$row[csf("payterm_id")];
			$pi_data_arr[$row[csf("uom")]][$row[csf("pi_suplier")]][$row[csf("pi_id")]][$row[csf("item_prod_id")]][$row[csf("btb_id")]]["tenor"]=$row[csf("tenor")];
			$pi_data_arr[$row[csf("uom")]][$row[csf("pi_suplier")]][$row[csf("pi_id")]][$row[csf("item_prod_id")]][$row[csf("btb_id")]]["lc_value"]=$row[csf("lc_value")];
			$pi_data_arr[$row[csf("uom")]][$row[csf("pi_suplier")]][$row[csf("pi_id")]][$row[csf("item_prod_id")]][$row[csf("btb_id")]]["work_order_id"].=$row[csf("work_order_id")].",";
		}
		/*echo "<pre>";
		print_r($pi_data_arr);*/
		
		$rcv_return_sql=sql_select("select b.prod_id, a.received_id, b.cons_quantity, b.cons_quantity, b.cons_amount from inv_issue_master a, inv_transaction b where a.id=b.mst_id and b.transaction_type=3 and b.item_category in (5,6,7,22,23) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0");
		$rcv_rtn_data=array();
		foreach($rcv_return_sql as $row)
		{
			$rcv_rtn_data[$row[csf("received_id")]][$row[csf("prod_id")]]["cons_quantity"]+=$row[csf("cons_quantity")];
			$rcv_rtn_data[$row[csf("received_id")]][$row[csf("prod_id")]]["cons_amount"]+=$row[csf("cons_amount")];
		}
		
		$req_wo_recv_sql=sql_select("select a.receive_basis, a.booking_id, c.color, c.yarn_type, c.yarn_count_id, c.yarn_comp_type1st, b.order_qnty as recv_qnty, b.order_amount as recv_amt, b.mst_id, b.prod_id, b.transaction_date, a.exchange_rate, a.receive_basis, c.item_description 
		from  inv_receive_master a, inv_transaction b, product_details_master c 
		where a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=1 and a.item_category in (5,6,7,22,23) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0
		order by a.booking_id, a.receive_basis, c.color, c.yarn_type, c.yarn_count_id, c.yarn_comp_type1st, b.transaction_date");
		$min_date=$max_date="";
		$b=0;
		foreach($req_wo_recv_sql as $row)
		{
			if($item_check[$row[csf("booking_id")]][$row[csf("prod_id")]]=="")
			{
				$item_check[$row[csf("booking_id")]][$row[csf("prod_id")]]=$row[csf("prod_id")];
				$min_date=$row[csf("transaction_date")];
				$max_date=$row[csf("transaction_date")];
				$b++;
			}
			else
			{
				if(strtotime($row[csf("transaction_date")])>strtotime($max_date)) $max_date=$row[csf("transaction_date")];
			}
			$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("receive_basis")]][trim($row[csf("item_description")])]['booking_id']=$row[csf("booking_id")];
			$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("receive_basis")]][trim($row[csf("item_description")])]['min_date']=$min_date;
			$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("receive_basis")]][trim($row[csf("item_description")])]['max_date']=$max_date;
			$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("receive_basis")]][trim($row[csf("item_description")])]['recv_qnty']+=$row[csf("recv_qnty")]-$rcv_rtn_data[$row[csf("mst_id")]][$row[csf("prod_id")]]["cons_quantity"];
			$req_wo_recv_arr[$row[csf("booking_id")]][$row[csf("receive_basis")]][trim($row[csf("item_description")])]['recv_amt']+=$row[csf("recv_amt")]-($rcv_rtn_data[$row[csf("mst_id")]][$row[csf("prod_id")]]["cons_amount"]/$row[csf("exchange_rate")]);
		}
		//echo "<pre>";print_r($req_wo_recv_arr[7700][2]);die;
		//echo $sql_req_wo;//die;
		$req_result=sql_select($sql_req_wo);
		//echo "jahid";die;
		ob_start();
		?>
	    <div style="width:2070px">
	        <table width="1800" cellpadding="0" cellspacing="0" id="caption"  align="left">
	        <tr>
	            <td align="center" width="100%"  class="form_caption" colspan="47"><strong style="font-size:18px">Company Name:<? echo " ". $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
	        </tr> 
	        <tr>  
	            <td align="center" width="100%" class="form_caption"  colspan="47"><strong style="font-size:18px"><? echo $report_title; ?></strong></td>
	        </tr>
	        <tr>  
	            <td align="center" width="100%"  class="form_caption"  colspan="47"><strong style="font-size:18px">From : <? echo $txt_date_from; ?> To : <? echo $txt_date_to; ?></strong></td>
	        </tr>
	        </table>
	    	<br />
	    <div style="width:2030px" >
			<table width="2050" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_3"  align="left">
				<thead>
					<tr>
						<th width="30" rowspan="2">Sl</th>
						<th colspan="11">PI Details</th>
						<th colspan="6">BTB LC Details</th>
						<th colspan="4">Matarials Received Information</th>
					</tr>
					<tr>
						<th width="150">Supplier</th>
						<th width="100">PI No.</th>
						<th width="70">PI Date</th>
						<th width="70">Last Ship Date</th>
						<th width="80">Group</th>
						<th width="230">Name of Items</th>
						<th width="50">UOM</th>
						<th width="80">PI Qnty</th>
						<th width="70">PI Rate</th>
						<th width="100">PI Amount</th>
						<th width="70">Currency</th>
						<th width="70">LC Date</th>
						<th width="100">LC No</th>
						<th width="100">Issuing Bank</th>
						<th width="70">Pay Term</th>
						<th width="80">Tenor</th>
						<th width="100">LC Amount</th>
						<th width="80">MRR Qnty</th>
						<th width="100">MRR Value</th>
						<th width="100">Short Value</th>
	                    <th >Pipe Line</th>
					</tr>
				</thead>
			</table>
			<div style="width:2050px; overflow-y:scroll; max-height:200px;font-size:12px; overflow-x:hidden;" id="scroll_body3" align="left">
			<table width="2032" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body3" align="left">
				<tbody>
				<?
				$i=1;
				//var_dump($tem_arr);die;
				$pi_array_check=array();
				foreach($pi_data_arr as $uom_id=> $uom_id_data)
				{
					$uom_pi_qntty=$uom_mrr_qty=$uom_short_qty=0;
					foreach($uom_id_data as $pi_suplier=> $pi_suplier_data)
					{
						foreach($pi_suplier_data as $pi_id=> $pi_id_data)
						{
							foreach($pi_id_data as $item_prod_id=> $item_prod_id_data)
							{
								foreach($item_prod_id_data as $btb_id=> $row)
								{
									if ($i%2==0)
									$bgcolor="#E9F3FF";
									else
									$bgcolor="#FFFFFF";
									$pipe_pi_qnty=$pipe_line="";
									$mrr_qnty=0;$mrr_value=0;$receive_basis=1;
									
									$mrr_qnty=$req_wo_recv_arr[$pi_id][1][trim($row["item_description"])]['recv_qnty'];
									$mrr_value=$req_wo_recv_arr[$pi_id][1][trim($row["item_description"])]['recv_amt'];
									$pi_id_ref=$req_wo_recv_arr[$pi_id][1][trim($row["item_description"])]['booking_id'];
									$book_pi_id=$pi_id;
									if($mrr_value<=0)
									{
										$all_wo_id_arr=array_unique(explode(",",chop($row["work_order_id"],",")));
										$book_pi_id="";
										foreach($all_wo_id_arr as $wo_id)
										{
											
											$receive_basis=2;
											$mrr_qnty+=$req_wo_recv_arr[$wo_id][2][trim($row["item_description"])]['recv_qnty'];
											$mrr_value+=$req_wo_recv_arr[$wo_id][2][trim($row["item_description"])]['recv_amt'];
											$pi_id_ref.=$req_wo_recv_arr[$wo_id][2][trim($row["item_description"])]['booking_id'].",";
											$book_pi_id.=$wo_id.",";
										}
									}
									$book_pi_id=chop($book_pi_id,",");
									if($pi_suplier==$prev_pi_suplier)
									{
										$supplier="&nbsp";
									}else{
										
										$supplier=$supplier_arr[$pi_suplier];
										$prev_pi_suplier=$pi_suplier;
									}

									if($pi_id==$prev_pi_id)
									{
										$pi_number="&nbsp";
										$pi_date="&nbsp";
										$last_shipment_date="&nbsp";
									}else{
										$prev_pi_id=$pi_id;
										$pi_number=$row["pi_number"];
										$pi_date=change_date_format($row["pi_date"]);
										$last_shipment_date=change_date_format($row["last_shipment_date"]);
									}

									if($row["item_group"]==$prev_item_group)
									{
										$item_group="&nbsp";
									}else{
										$prev_item_group=$row["item_group"];
										$item_group=$itemGroup_arr[$row["item_group"]];
									}

									if($btb_id==$prev_btb_id)
									{
										$lc_date="&nbsp";
										$lc_number="&nbsp";
										$issuing_bank_id="&nbsp";
										$payterm_id="&nbsp";
										$tenor="&nbsp";
										$lc_value="&nbsp";
									}else{
										$prev_btb_id=$btb_id;
										$lc_date=change_date_format($row["lc_date"]);
										$lc_number=$row["lc_number"];
										$issuing_bank_id=$bank_arr[$row["issuing_bank_id"]];
										$payterm_id=$bank_arr[$row["payterm_id"]];
										$tenor=$row["tenor"];
										$lc_value=$row["lc_value"];
									}
									//echo $pi_id.'=='.$prev_pi_id.'=='.$pi_number.'<br>';*/

									?>
				                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
				                        <td width="30"><? echo $i; ?></td>
				                        <td width="150"><p><? echo $supplier; ?></p></td>
				                        <td width="100"><p><? echo $pi_number; ?></p></td>
				                        <td width="70"><p>&nbsp;<? echo $pi_date; ?></p></td>
				                        <td width="70"><p>&nbsp;<? echo $last_shipment_date; ?></p></td>
				                        <td width="80"><p><? echo $item_group; ?></p></td>
				                        <td width="230"><p><? echo $row["item_description"]; ?> </p></td>
				                        <td width="50"><p><? echo $unit_of_measurement[$uom_id];?></p></td>
				                        <td width="80" align="right"><p><? echo  number_format($row["quantity"],0); ?> </p></td>
				                        <td width="70" align="right"><p><? echo  number_format($row["amount"]/$row["quantity"],2); ?> </p></td>
				                        <td width="100" align="right"><p><? echo number_format($row["amount"],2); ?> </p></td>
				                        <td width="70"><p><? echo $currency[$row["currency_id"]]; ?></p></td>
				                        <td width="70"><p><? echo $lc_date; ?> </p></td>
				                        <td width="100"><p><? echo $lc_number; ?></p></td>
				                        <td width="100"><p><? echo $issuing_bank_id; ?></p></td>
				                        <td width="70"><p><? echo $payterm_id; ?></p></td>
				                        <td width="80"><p><? echo $tenor; ?></p></td>
				                        <td width="100" align="right"><p><? echo $lc_value; ?></p></td>
				                        <td width="80" align="right"><p><a href="##" onclick="fn_mrr_details('<? echo $book_pi_id;?>','<? echo trim($row["item_description"]);?>','<? echo $receive_basis;?>','receive_details_popup')"><? echo number_format($mrr_qnty,2); ?></a></p></td>
				                        <td width="100" align="right"><p><? echo number_format($mrr_value,2);?></p></td>
				                        <td width="100" align="right"><p><? $short_amt=$row["amount"]-$mrr_value; echo number_format($short_amt,2); ?></p></td>
				                        <td align="right" ><p><? $short_qty=$row["quantity"]-$mrr_qnty; echo number_format($short_qty,2); ?></p></td>
				                    </tr>
				                    <?
				                    $k++;
				                    $i++;
				                    $uom_pi_qntty+=$row["quantity"];
				                    //$uom_pi_amount+=$row["amount"];
				                    $uom_mrr_qty+=$mrr_qnty;
				                    $uom_short_qty+=$short_qty;
				                    //$uom_mrr_val+=$mrr_value;
								}
							}
						}
					}
					?>
					<tr style="background: #dbdbdb;">
						<td colspan="8" align="right"><strong>UOM Total</strong></td>
						<td align="right"><b><? echo number_format($uom_pi_qntty,2);?></b></td>
						<td colspan="9">&nbsp;</td>
						<td align="right"><b><? echo number_format($uom_mrr_qty,2);?></b></td>
						<td colspan="2">&nbsp;</td>
						<td align="right"><b><? echo number_format($uom_short_qty,2);?></b></td>
					</tr>
					<?
				}
				?>
				</tbody>
			</table>
			</div>
			<?
		
	    ?>
		</div>
	    <?
	}
		
	
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename####$cbo_based_on";
	exit();
}

if($action=="receive_details_popup")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	//echo $color_id."&&".$yarn_type."&&".$count_id."&&".$composition;die;
	?>
    	<div style="width:620px;">
    	<table width="600" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_pop">
        	<thead>
            	<tr>
                	<th width="30">SL</th>
                    <th width="120">Book/PI No</th>
                    <th width="130">MRR No.</th>
                    <th width="70">Receive Date</th>
                    <th width="50">UOM</th>
                    <th width="50">Receive Qty</th>
					<th width="100">Remarks</th>
                </tr>
            </thead>
        </table>
        <div style="width:620px; overflow-y:scroll; max-height:200px;font-size:12px; overflow-x:hidden;" id="scroll_body_pop" >
        <table width="600" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body_pop">
        	<tbody>
            <?
			$rcv_sql=sql_select("select a.id, a.recv_number, a.receive_basis, a.booking_id, a.booking_no, a.receive_date,max(b.cons_uom) as cons_uom, sum(b.cons_quantity) as mrr_qnty,b.remarks
			from inv_receive_master a, inv_transaction b, product_details_master c
			where a.id=b.mst_id and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.receive_basis=$receive_basis and a.booking_id in($pi_id) and trim(c.ITEM_DESCRIPTION)='$item_description'
			group by a.id, a.recv_number, a.receive_basis, a.booking_id, a.booking_no, a.receive_date, b.remarks");
			
			$k=1;$all_rcv_id=array();
			foreach($rcv_sql as $row)
			{
				if ($k%2==0)
				$bgcolor="#E9F3FF";
				else
				$bgcolor="#FFFFFF";
				$all_rcv_id[$row[csf("id")]]=$row[csf("id")];
				?>
            	<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
                	<td width="30" align="center"><? echo $k; ?></td>
                    <td align="center" width="120"><p><? echo $row[csf("booking_no")]; ?></p></td>
                    <td align="center" width="130"><p><? echo $row[csf("recv_number")]; ?></p></td>
                    <td align="center" width="70"><p>&nbsp;<? if($row[csf("receive_date")]!="" && $row[csf("receive_date")]!="0000-00-00") echo change_date_format($row[csf("receive_date")]); ?></p></td>
                    <td  align="center" width="50"><p><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
                    <td align="right" width="50"><p><? echo number_format($row[csf("mrr_qnty")],2); $total_mrr_qnty+=$row[csf("mrr_qnty")]; ?></p></td>
					<td align="center" width="100"><p><? echo substr($row[csf("remarks")],0,30) ?> </p></td>
                </tr>
                <?
				$k++;
			}
			unset($rcv_sql);
			
			//==============>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> for PI from WO
                     
			?>
            </tbody>
            <tfoot>
            	<tr>
                	<th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Total</th>
                    <th><? echo number_format($total_mrr_qnty,2); ?></th>
					<th></th>
                </tr>
            </tfoot>
        </table>
        </div>
        <br/>
        <?
			$all_rcv_ids=implode(",",$all_rcv_id);
			if($all_rcv_ids=="") $all_rcv_ids=0;
			$rcv_return_sql=sql_select("select a.issue_number, a.issue_date, b.cons_uom, sum(b.cons_quantity) as qnty, sum(b.cons_amount) as amt 
			from inv_issue_master a, inv_transaction b, product_details_master c 
			where a.id=b.mst_id and b.prod_id=c.id  and b.transaction_type=3 and b.item_category in (5,6,7,22,23) and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.received_id in($all_rcv_ids) and c.id=$prod_id
			group by a.issue_number, a.issue_date, b.cons_uom");
			?>
            <table width="600" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_pop">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="130">MRR No.</th>
                        <th width="130">Return Date</th>
                        <th width="80">UOM</th>
                        <th>Return Qty</th>
						<th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
					<?
					$i=1;
					foreach($rcv_return_sql as $row)
					{
						if ($k%2==0)
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";
						?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
                            <td align="center"><? echo $i;?></td>
                            <td><p><? echo $row[csf("issue_number")]; ?></p></td>
                            <td align="center"><p><? echo change_date_format($row[csf("issue_date")]); ?></p></td>
                            <td align="center"><p><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
                            <td align="right"><? echo number_format($row[csf("qnty")],2); $total_rtn+=$row[csf("qnty")]; ?></td>
                        </tr>
                        <?
						$i++;$k++;
					}
                    ?>
                	
                </tbody>
                <tfoot>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>Total Rtn</th>
                        <th><? echo number_format($total_rtn,2); ?></th>
						<th></th>
						
                    </tr>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>Balance</th>
                        <th><? $balance_qnty=($total_mrr_qnty-$total_rtn); echo number_format($balance_qnty,2); ?></th>
						<th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <script>setFilterGrid("table_body_pop",-1);</script>
    <?
	exit();
}

if($action=="receive_details_popup_analysis")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	//echo $color_id."&&".$yarn_type."&&".$count_id."&&".$composition;die;
	?>
    	<div style="width:620px;">
    	<table width="600" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_pop">
        	<thead>
            	<tr>
                	<th width="30">SL</th>
                    <th width="120">WO/PI No</th>
                    <th width="130">MRR No.</th>
                    <th width="70">Receive Date</th>
					<th width="50">Yarn Lot</th>
                    <th width="50">UOM</th>
                    <th width="50">Receive Qty</th>
					<th width="100">Remarks</th>
                </tr>
            </thead>
        </table>
        <div style="width:620px; overflow-y:scroll; max-height:200px;font-size:12px; overflow-x:hidden;" id="scroll_body_pop" >
        <table width="600" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body_pop">
        	<tbody>
            <?
			$wo_num_arr=return_library_array( "select id, wo_number from wo_non_order_info_mst",'id','wo_number');
			$pi_num_arr=return_library_array( "select id, pi_number from  com_pi_master_details",'id','pi_number');
			
			$rcv_sql=sql_select("select a.id, a.recv_number, a.receive_basis, a.booking_id, a.receive_date,max(b.cons_uom) as cons_uom, sum(b.cons_quantity) as mrr_qnty,b.remarks, c.lot 
			from inv_receive_master a, inv_transaction b, product_details_master c
			where a.id=b.mst_id and b.prod_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.receive_basis=$book_basis and b.pi_wo_req_dtls_id=$booking_id and c.color=$color_id and c.yarn_type=$yarn_type and c.yarn_count_id=$count_id and c.yarn_comp_type1st=$composition 
			group by a.id, a.recv_number, a.receive_basis, a.booking_id, a.receive_date, b.remarks, c.lot");
			
			$k=1;$all_rcv_id=array();
			foreach($rcv_sql as $row)
			{
				if ($k%2==0)
				$bgcolor="#E9F3FF";
				else
				$bgcolor="#FFFFFF";
				$all_rcv_id[$row[csf("id")]]=$row[csf("id")];
				?>
            	<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
                	<td width="30" align="center"><? echo $k; ?></td>
                    <?
					$wo_pi_no="";
					if($row[csf("receive_basis")]==1)
					{
						$wo_pi_no=$pi_num_arr[$row[csf("booking_id")]];
					}
					else
					{
						$wo_pi_no=$wo_num_arr[$row[csf("booking_id")]];
					}

					
					?>
                    <td align="center" width="120"><p><? echo $wo_pi_no; ?></p></td>
                    <td align="center" width="130"><p><? echo $row[csf("recv_number")]; ?></p></td>
                    <td align="center" width="70"><p>&nbsp;<? if($row[csf("receive_date")]!="" && $row[csf("receive_date")]!="0000-00-00") echo change_date_format($row[csf("receive_date")]); ?></p></td>
					<td align="center" width="50"><p><? echo $row[csf("lot")] ?></p></td>
                    <td  align="center" width="50"><p><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
                    <td align="right" width="50"><p><? echo number_format($row[csf("mrr_qnty")],2); $total_mrr_qnty+=$row[csf("mrr_qnty")]; ?></p></td>
					<td align="center" width="100"><p><? echo substr($row[csf("remarks")],0,30) ?> </p></td>
                </tr>
                <?
				$k++;
			}
			unset($rcv_sql);
			
			//==============>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> for PI from WO
                     
			?>
            </tbody>
            <tfoot>
            	<tr>
                	<th></th>
                    <th></th>
                    <th></th>
					<th></th>
                    <th></th>
                    <th>Total</th>
                    <th><? echo number_format($total_mrr_qnty,2); ?></th>
					<th></th>
                </tr>
            </tfoot>
        </table>
        </div>
        <br/>
        <?
			$all_rcv_ids=implode(",",$all_rcv_id);
			if($all_rcv_ids=="") $all_rcv_ids=0;
			$rcv_return_sql=sql_select("select a.issue_number, a.issue_date, b.cons_uom, sum(b.cons_quantity) as qnty, sum(b.cons_amount) as amt 
			from inv_issue_master a, inv_transaction b, product_details_master c 
			where a.id=b.mst_id and b.prod_id=c.id and a.entry_form=8 and b.transaction_type=3 and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.received_id in($all_rcv_ids) and c.color=$color_id and c.yarn_type=$yarn_type and c.yarn_count_id=$count_id and c.yarn_comp_type1st=$composition 
			group by a.issue_number, a.issue_date, b.cons_uom");
			?>
            <table width="600" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_pop">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="130">MRR No.</th>
                        <th width="130">Return Date</th>
                        <th width="80">UOM</th>
                        <th>Return Qty</th>
						<th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
					<?
					$i=1;
					foreach($rcv_return_sql as $row)
					{
						if ($k%2==0)
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";
						?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
                            <td align="center"><? echo $i;?></td>
                            <td><p><? echo $row[csf("issue_number")]; ?></p></td>
                            <td align="center"><p><? echo change_date_format($row[csf("issue_date")]); ?></p></td>
                            <td align="center"><p><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
                            <td align="right"><? echo number_format($row[csf("qnty")],2); $total_rtn+=$row[csf("qnty")]; ?></td>
                        </tr>
                        <?
						$i++;$k++;
					}
                    ?>
                	
                </tbody>
                <tfoot>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>Total Rtn</th>
                        <th><? echo number_format($total_rtn,2); ?></th>
						<th></th>
						
                    </tr>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>Balance</th>
                        <th><? $balance_qnty=($total_mrr_qnty-$total_rtn); echo number_format($balance_qnty,2); ?></th>
						<th></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <script>setFilterGrid("table_body_pop",-1);</script>
    <?
	exit();
}



if($action=="issue_details_popup")
{
	extract($_REQUEST);
 	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode,'','');
	//echo $color_id."&&".$yarn_type."&&".$count_id."&&".$composition;die;
	?>
    	<div style="width:620px;">
    	<table width="600" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_pop">
        	<thead>
            	<tr>
                	<th width="30">SL</th>
                    <th width="130">Issue No.</th>
                    <th width="70">Issue Date</th>
					<th width="50">Yarn Lot</th>
                    <th width="50">UOM</th>
                    <th width="50">Issue Qty</th>
                </tr>
            </thead>
        </table>
        <div style="width:620px; overflow-y:scroll; max-height:200px;font-size:12px; overflow-x:hidden;" id="scroll_body_pop" >
        <table width="600" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body_pop">
        	<tbody>
            <?
			$wo_num_arr=return_library_array( "select id, wo_number from wo_non_order_info_mst",'id','wo_number');
			$pi_num_arr=return_library_array( "select id, pi_number from  com_pi_master_details",'id','pi_number');
			
			$issue_sql="SELECT a.id, a.issue_number, a.issue_date,b.prod_id,c.lot, max(b.cons_uom) as cons_uom, sum(b.cons_quantity) as issue_qnty
		from  inv_issue_master a, inv_transaction b, product_details_master c 
		where  a.id=b.mst_id and b.prod_id=c.id and b.transaction_type=2 and a.item_category=1 and a.status_active=1 and a.is_deleted=0 and a.issue_basis=$book_basis and b.prod_id=$booking_id and c.color=$color_id  and c.yarn_type=$yarn_type and c.yarn_count_id=$count_id and c.yarn_comp_type1st=$composition and b.status_active=1 and b.is_deleted=0 and a.entry_form=3
		group by a.id, a.issue_number, a.issue_date, b.prod_id, c.lot";
		//echo $issue_sql;die;
		$issue_sql_arr=sql_select($issue_sql);

			
			$k=1;$all_issue_id=array();$all_prod_id=array();$all_lot_id=array();
			foreach($issue_sql_arr as $row)
			{
				if ($k%2==0)
				$bgcolor="#E9F3FF";
				else
				$bgcolor="#FFFFFF";
				$all_issue_id[$row[csf("id")]]=$row[csf("id")];
				$all_prod_id[$row[csf("prod_id")]]=$row[csf("prod_id")];
				$all_lot_id[$row[csf("lot")]]="'".$row[csf("lot")]."'";
				?>
            	<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
                	<td width="30" align="center"><? echo $k; ?></td>
                    <?
				
					?>
                    
                    <td align="center" width="130"><p><? echo $row[csf("issue_number")]; ?></p></td>
                    <td align="center" width="70"><p>&nbsp;<? if($row[csf("issue_date")]!="" && $row[csf("issue_date")]!="0000-00-00") echo change_date_format($row[csf("issue_date")]); ?></p></td>
					<td align="center" width="50"><p><? echo $row[csf("lot")] ?></p></td>
                    <td  align="center" width="50"><p><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
                    <td align="right" width="50"><p><? echo number_format($row[csf("issue_qnty")],2); $total_issue_qnty+=$row[csf("issue_qnty")]; ?></p></td>
                </tr>
                <?
				$k++;
			}
			unset($rcv_sql);
			
			//==============>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> for PI from WO
                     
			?>
            </tbody>
            <tfoot>
            	<tr>
                	
                    <th></th>
                    <th></th>
					<th></th>
                    <th></th>
                    <th>Total Issue  </th>
                    <th><? echo number_format($total_issue_qnty,2); ?></th>
				
                </tr>
            </tfoot>
        </table>
        </div>
        <br/>
        <?
			$all_issue_ids=implode(",",$all_issue_id);
			if($all_issue_ids=="") $all_issue_ids=0;

			$all_prod_ids=implode(",",$all_prod_id);
			if($all_prod_ids=="") $all_prod_ids=0;

			$all_lot_ids=implode(",",$all_lot_id);
			if($all_lot_ids=="") $all_lot_ids=0;

			$issue_return_sql="SELECT a.recv_number, a.issue_id,b.prod_id, b.cons_amount,a.receive_date, max(b.cons_uom) as cons_uom,sum(b.cons_quantity) as issue_rtn_qnty,c.lot from inv_receive_master a, inv_transaction b, product_details_master c where a.id=b.mst_id and a.entry_form=9 and b.transaction_type=4 and b.item_category=1 and a.status_active=1 and a.is_deleted=0 and a.issue_id in($all_issue_ids) and b.prod_id in($all_prod_ids) and c.lot in($all_lot_ids) and c.color=$color_id and c.yarn_type=$yarn_type and c.yarn_count_id=$count_id and c.yarn_comp_type1st=$composition and b.status_active=1 and b.is_deleted=0 group by a.recv_number, a.issue_id,b.prod_id, b.cons_quantity, b.cons_amount,a.receive_date, c.lot";
			//echo $issue_return_sql;die;
			$issue_return_sql_arr = sql_select($issue_return_sql);
		
			?>
            <table width="600" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_header_pop">
                <thead>
                    <tr>
                        <th width="30">SL</th>
                        <th width="130">Issue Return No.</th>
                        <th width="70">Return Date</th>
						<th width="50">Yarn Lot</th>
                        <th width="50">UOM</th>
                        <th width="50">Return Qty</th>
                    </tr>
                </thead>
                <tbody>
					<?
					$i=1;
					foreach($issue_return_sql_arr as $row)
					{
						if ($k%2==0)
						$bgcolor="#E9F3FF";
						else
						$bgcolor="#FFFFFF";

						?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
                            <td align="center"><? echo $i;?></td>
                            <td align="center"><p><? echo $row[csf("recv_number")]; ?></p></td>
                            <td align="center"><p><? echo change_date_format($row[csf("receive_date")]); ?></p></td>
							<td align="center"><p><? echo $row[csf("lot")]; ?></p></td>
                            <td align="center"><p><? echo $unit_of_measurement[$row[csf("cons_uom")]]; ?></p></td>
                            <td align="right"><? echo number_format($row[csf("issue_rtn_qnty")],2); $total_rtn+=$row[csf("issue_rtn_qnty")]; ?></td>
                        </tr>
                        <?
						$i++;$k++;
					}
                    ?>
                	
                </tbody>
                <tfoot>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>Total Rtn  </th>
                        <th><? echo number_format($total_rtn,2); ?></th>
						
						
                    </tr>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>Balance</th>
                        <th><? $balance_qnty=($total_issue_qnty-$total_rtn); echo number_format($balance_qnty,2); ?></th>
					
                    </tr>
                </tfoot>
            </table>
        </div>
        <script>setFilterGrid("table_body_pop",-1);</script>
    <?
	exit();
}




	disconnect($con);
?>


 