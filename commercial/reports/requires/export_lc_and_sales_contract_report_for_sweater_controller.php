<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');


$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 110, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0); 
	exit();
}

if ($action=="load_drop_down_applicant")
{
	$sql = "select a.id,a.buyer_name from lib_buyer a, lib_buyer_tag_company b where a.status_active =1 and a.is_deleted=0 and b.buyer_id=a.id and b.tag_company='$data' and a.id in (select  buyer_id from  lib_buyer_party_type where party_type in (22,23)) order by buyer_name";  
 	echo create_drop_down( "cbo_applicant", 110, $sql,"id,buyer_name", 1, "-- Select --", 0, "" );
	exit();
	
}


//Company Details
$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
$bank_arr=return_library_array("select id,bank_name from   lib_bank","id","bank_name");

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$txt_year=str_replace("'","",$txt_year);

	$cbo_date_type=str_replace("'","",$cbo_date_type);
	if($cbo_date_type == 0) $cbo_date_type="%%"; else $cbo_date_type = $cbo_date_type;
	if($txt_year!="")
	{
		$year_lc="and lc_year='$txt_year'";
		$year_sc="and sc_year='$txt_year'";
	}
	else
	{
		$year_lc="";
		$year_sc="";
	}
	ob_start();	
	?>
	<div style="width:3360px;" align="left">
		<fieldset>
            <table width="3320" cellpadding="0" cellspacing="0" id="caption">
                <tr>
                   <td align="center" width="100%" colspan="20" class="form_caption" ><strong style="font-size:18px">Company Name:<? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
                </tr> 
                <tr>  
                   <td align="center" width="100%" colspan="20" class="form_caption" ><strong style="font-size:18px"><? echo $report_title; ?></strong></td>
                </tr>  
            </table>
            <table width="3320" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="">
                <thead>
                    <tr>
                        <th width="50">Sl</th>
                        <th width="100">Internal File No.</th>
                        <th width="100">Bank File No.</th>
                        <th width="80">Year</th>
                        <th width="80">Beneficiary</th>
                        <th width="100">Buyer</th>
                        <th width="100">Applicant</th>
                        <th width="80">SC/LC</th>
                        <th width="110">SC/LC No.</th>
                        <th width="75">Convertible Type</th>
						<? 
							//echo $cbo_date_type;die;
						if ($cbo_date_type==1){ ?>
                        	<th width="100">SC/LC Date</th>
						<? }else{ ?>
							<th width="100">Lien Date</th>
						<? }?>
                        <th width="100">Insert Date</th>
                        <th width="100">Delayed Days</th>
                        <th width="100">Last Ship Date</th>
                        <th width="120">SC Value(LC/SC, Finance)</th>
                        <th width="110">Rep. LC/SC</th>
                        <th width="110">Balance</th>
                        <th width="110">SC Value(Direct)</th>
                        <th width="110">LC Value(Direct)</th>
                        <th width="100">Order Qty(As Attached)</th>
                        <th width="110">File Value</th>
                        <th width="100">Expiry Date</th>
                        <th width="110">Lien Bank</th>
                        <th width="110">Issuing Bank</th>
                        <th width="100">Pay Term</th>
                        <th width="100">Tenor</th>
                        <th width="100">Incoterm</th>
                        <th width="110">Transfering Bank</th>
                        <th width="110">Negotiating Bank</th>
                        <th width="110">Nominated Ship. Line</th>
                        <th width="78">Re- Imbursing Bank</th>
                        <th width="100">Gross Bill Amount</th>
                        <th width="">Net Bill Amount</th>
                    </tr>
                </thead>
            </table>            
			<div style="width:3340px; overflow-y: scroll; overflow-x:hidden; max-height:250px;font-size:12px;" id="scroll_body">
	            <table width="3320" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
	                <tbody>
	                <?
					$cbo_company_name=str_replace("'","",$cbo_company_name);
					if($cbo_company_name!=0) $cbo_company_name = $cbo_company_name; else $cbo_company_name="%%";
					
					$cbo_lien_bank=str_replace("'","",$cbo_lien_bank);
					if($cbo_lien_bank == 0) $cbo_lien_bank="%%"; else $cbo_lien_bank = $cbo_lien_bank;
					
					$cbo_currency_name=str_replace("'","",$cbo_currency_name);
					if($cbo_currency_name == 0) $cbo_currency_name="%%"; else $cbo_currency_name = $cbo_currency_name;
					
					$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
					if($cbo_buyer_name == 0) $cbo_buyer_name="%%"; else $cbo_buyer_name = $cbo_buyer_name;
					
					$cbo_applicant=str_replace("'","",$cbo_applicant);
					if($cbo_applicant == 0) $cbo_applicant="%%"; else $cbo_applicant = $cbo_applicant;					
					
					$txt_date_from=str_replace("'","",$txt_date_from);
					if(trim($txt_date_from)!= "") $txt_date_from  =$txt_date_from;
					
					$txt_date_to=str_replace("'","",$txt_date_to);
					if(trim($txt_date_to)!= "") $txt_date_to = $txt_date_to;
					//if(trim($data[7])!="") $cbo_year2=$data[7];,lc_date
					if ($cbo_date_type==1) 
					{
						if ($txt_date_from!="" && $txt_date_to!="")
						{
							$str_cond=" and lc_date between '$txt_date_from' and  '$txt_date_to'";
							
							$str_cond_con=" and contract_date between '$txt_date_from' and  '$txt_date_to'";
						}
						else
						{
							$str_cond="";
							$str_cond_con="";
						}
					} 
					else 
					{
						if ($txt_date_from!="" && $txt_date_to!="")
						{
							$str_cond=" and lien_date between '$txt_date_from' and  '$txt_date_to'";						
							$str_cond_con=" and contract_date between '$txt_date_from' and  '$txt_date_to'";
						}
						else
						{
							$str_cond="";
							$str_cond_con="";
						}
					}					
					
					$txt_file_no=str_replace("'","",trim($txt_file_no));
					if($txt_file_no == "") $txt_file_no_cond=""; else $txt_file_no_cond = " and internal_file_no like '%$txt_file_no%'";
					$txt_lc_sc_no=str_replace("'","",trim($txt_lc_sc_no));
					if($txt_lc_sc_no == "") 
					{
						$txt_lc_no_cond="";
						$txt_sc_no_cond="";
					} 
					else 
					{
						$txt_lc_no_cond = " and export_lc_no like '%$txt_lc_sc_no%'";
						$txt_sc_no_cond = " and contract_no like '%$txt_lc_sc_no%'";
					}

					$doc_inv_sc_res = sql_select("select b.lc_sc_id, sum(c.current_invoice_value) as current_invoice_value , 1 as lc_sc_type
					from com_export_invoice_ship_mst b, com_export_invoice_ship_dtls c 
					where b.is_lc=1 and b.id=c.mst_id 
					and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.benificiary_id = '$cbo_company_name'
					group by b.lc_sc_id
					union all
					select b.lc_sc_id, sum(c.current_invoice_value) as current_invoice_value , 2 as lc_sc_type
					from com_export_invoice_ship_mst b, com_export_invoice_ship_dtls c 
					where b.is_lc=2 and b.id=c.mst_id  and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.benificiary_id = '$cbo_company_name'
					group by b.lc_sc_id");
					foreach ($doc_inv_sc_res as $val) 
					{
						$doc_inv_sc_arr[$val[csf("lc_sc_id")]][$val[csf("lc_sc_type")]] += $val[csf("current_invoice_value")];
					}
					unset($doc_inv_sc_res);
					if($db_type == 0)
					{
						$upcharge_select = "IFNULL(upcharge, 0)";
					}else{
						$upcharge_select = "nvl(upcharge,0)";
					}
					
					$gross_sql =  sql_select("select id, is_lc, lc_sc_id, net_invo_value, (discount_ammount+bonus_ammount+claim_ammount+commission ) - $upcharge_select as deduct_amnt 
					from com_export_invoice_ship_mst where status_active=1 and is_deleted=0");

					foreach ($gross_sql as $val2) 
					{
						$net_amount_arr[$val2[csf("lc_sc_id")]][$val2[csf("is_lc")]]["net_invo_value"] += $val2[csf("net_invo_value")];
						$net_amount_arr[$val2[csf("lc_sc_id")]][$val2[csf("is_lc")]]["deduct_amnt"] += $val2[csf("deduct_amnt")];
					}
					unset($gross_sql);


					$sql="select id, internal_file_no,bank_file_no,lc_year as lc_sc_year,beneficiary_name,buyer_name,applicant_name,export_lc_no as lc_sc ,lc_date as lc_sc_date,insert_date,last_shipment_date,lc_value as lc_sc_value, currency_name,expiry_date,lien_bank,lien_date,pay_term,tenor,inco_term,nominated_shipp_line as ship_line, null as convertible_to_lc,issuing_bank_name,transfer_bank,negotiating_bank,re_imbursing_bank,replacement_lc, null as converted_from,1 as type
					from  com_export_lc
					where  beneficiary_name like '$cbo_company_name' and lien_bank like '$cbo_lien_bank' and currency_name  like '$cbo_currency_name'and buyer_name like '$cbo_buyer_name' and applicant_name like '$cbo_applicant' and status_active=1 and is_deleted=0 $str_cond $year_lc $txt_file_no_cond $txt_lc_no_cond
					
					UNION ALL
					
					select id,internal_file_no,bank_file_no,sc_year as lc_sc_year,beneficiary_name,buyer_name,applicant_name,contract_no as lc_sc,contract_date as lc_sc_date,insert_date,last_shipment_date,contract_value as lc_sc_value,currency_name,expiry_date,lien_bank,lien_date,pay_term,tenor,inco_term,shipping_line as ship_line,convertible_to_lc, null as issuing_bank_name, null as transfer_bank , null as negotiating_bank, null as re_imbursing_bank, null as replacement_lc, converted_from,2 as type 
					from com_sales_contract
					where  beneficiary_name like '$cbo_company_name' and lien_bank like '$cbo_lien_bank' and currency_name  like '$cbo_currency_name' and buyer_name like '$cbo_buyer_name' and applicant_name like '$cbo_applicant' and status_active=1 and is_deleted=0 $str_cond_con $year_sc $txt_file_no_cond $txt_sc_no_cond order by internal_file_no";
					
					//echo $sql;

					$i= 1; $k=1; $item_group_array=array();$year=array();					
					$sql_re=sql_select($sql);
					$com_ex_lc_id_arr=array();
					foreach ($sql_re as $value) 
					{
						$com_ex_lc_id_arr[$value[csf('id')]]=$value[csf('id')];
					}
					$com_ex_lc_ids_con = implode(",",$com_ex_lc_id_arr);

					$sql_attach = "select ci.com_export_lc_id as id, 1 as type, sum( wm.total_set_qnty*ci.attached_qnty) as attached_qnty 
					FROM wo_po_break_down wb, wo_po_details_master wm, com_export_lc_order_info ci 
					WHERE wb.job_no_mst = wm.job_no AND wb.id = ci.wo_po_break_down_id AND ci.com_export_lc_id in($com_ex_lc_ids_con) AND wb.status_active = 1 AND wb.is_deleted = 0 AND wm.status_active = 1 AND wm.is_deleted = 0 AND ci.status_active = 1 AND ci.is_deleted = 0 group by ci.com_export_lc_id
					
					UNION ALL
					
					select ci.com_sales_contract_id as id, 2 as type, sum( wm.total_set_qnty*ci.attached_qnty) as attached_qnty 
					FROM wo_po_break_down wb, wo_po_details_master wm, com_sales_contract_order_info ci 
					WHERE wb.job_no_mst = wm.job_no AND wb.id = ci.wo_po_break_down_id AND ci.com_sales_contract_id in($com_ex_lc_ids_con)  AND wb.status_active = 1 AND wb.is_deleted = 0 AND wm.status_active = 1 AND wm.is_deleted = 0 AND ci.status_active = 1 AND ci.is_deleted = 0 group by ci.com_sales_contract_id
					";
					
					$sql_attach_res = sql_select($sql_attach);
					$attach_qty_arr=array();
					foreach ($sql_attach_res as $value)
					{
						$lc_sc_ids=$value[csf("id")]."__".$value[csf("type")];	
						$attach_qty_arr[$lc_sc_ids] = $value[csf("attached_qnty")];
					}
					//echo '<pre>';print_r($attach_qty_arr);
					$grand_total_file_value=0;
					$grand_total_balance=0;
					$grand_sc_value_1_3=0;
					$grand_lc_value_1=0;
					$grand_sc_value_2=0;
					$grand_lc_value_0_1=0;
					$grand_gross_value=0;
					$grand_net_value=0;
					$tot_attach_qty=0;
					$grand_total_attach_qty=0;

					foreach($sql_re as $row_result)
					{
						if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
						$lsc_sc_ids=$row_result[csf("id")]."__".$row_result[csf("type")];
						$attach_qty=$attach_qty_arr[$lsc_sc_ids];

						$date1 = strtotime($row_result[csf('insert_date')]);  
						$date2 = strtotime($row_result[csf('lc_sc_date')]);  
						$delayed =floor(abs(($date2 - $date1)/60/60/24)); 
						 //07-MAR-19 03.37.53 PM
						?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
	                        <td width="50" ><? echo $i;?></td>
	                        <td width="100"><p><? echo  $row_result[csf('internal_file_no')];?></p></td>
	                        <td  width="100"><p><? echo $row_result[csf('bank_file_no')];?></p></td>
	                        <td  width="80"><p><? echo $row_result[csf('lc_sc_year')];?></p></td>
	                        <td width="80"><p><? echo $company_arr[$row_result[csf('beneficiary_name')]]; ?></p></td>
	                        <td width="100"><p><? echo $buyer_arr[$row_result[csf('buyer_name')]]; ?></p>
	                        </td>
	                        <td width="100"><p><? echo $buyer_arr[$row_result[csf('applicant_name')]];?><p></td>
	                        
	                        <td align="center" width="80"><p><? if($row_result[csf('type')] == 1) echo "LC"; else echo "SC"; ?></p></td>
	                        
	                        <td width="110"><p><a href="#" onClick="openmypage_file('show_file','<? echo $row_result[csf('id')];?>','<? echo $row_result[csf('type')];?>')"><? echo $row_result[csf('lc_sc')];?></a></p></td>
	                        <td align="center" width="75"><p><? echo $convertible_to_lc[$row_result[csf('convertible_to_lc')]];?></p></td>
	                        <td align="center" width="100"><p><?  if($row_result[csf('lc_sc_date')]!="0000-00-00") echo change_date_format( $row_result[csf('lc_sc_date')]);?></p></td>
	                        <td align="center" width="100"><p><? echo change_date_format( $row_result[csf('insert_date')]);?></p></td>
	                        <td align="center" width="100"><p><? echo ($delayed>1) ? $delayed." days" : $delayed." day";?></p></td>
	                        <td align="center" width="100"><p><? if($row_result[csf('last_shipment_date')]!="0000-00-00") echo change_date_format( $row_result[csf('last_shipment_date')]);?></p></td>
	                        
	                        <td align="right" width="120"><p><? if($row_result[csf('type')] == 2 && $row_result[csf('convertible_to_lc')] !=2 ) {$sc_1_3=$row_result[csf('lc_sc_value')]; echo number_format($sc_1_3 ,2);}?><p></td>
	                        
	                        <td align="right" width="110"><p><? if(($row_result[csf('replacement_lc')] == 1 && $row_result[csf('type')] == 1) || ($row_result[csf('type')] == 2 && $row_result[csf('convertible_to_lc')] ==2 && $row_result[csf('converted_from')]>0) ){ $lc_1=$row_result[csf('lc_sc_value')]; echo number_format($lc_1 ,2);} ?></p></td>
	                        <td align="right" width="110"><p></p></td>
	                        <td align="right" width="110"><p><? if($row_result[csf('type')] == 2 && $row_result[csf('convertible_to_lc')] ==2 && $row_result[csf('converted_from')]<1){$sc_2=$row_result[csf('lc_sc_value')]; echo number_format($sc_2 ,2); }?></p></td>
	                        <td align="right" width="110"><p><? if($row_result[csf('replacement_lc')] == 2 && $row_result[csf('type')] == 1 ){ $lc_0_1=$row_result[csf('lc_sc_value')] ; echo number_format($lc_0_1,2);} ?></p></td>

	                        <td align="right" width="100"><p><? echo number_format($attach_qty); ?></p></td>

	                        <td align="right" width="110"><p></p></td>
	                        <td align="center" width="100"><p><? if($row_result[csf('ex_factory_date')]!="0000-00-00") echo change_date_format( $row_result[csf('expiry_date')]);?></p></td>
	                        <td width="110"><p><? echo $bank_arr[$row_result[csf('lien_bank')]]; ?></p>
	                         </td>
	                        <td width="110"><p><? if($row_result[csf('type')]==1) echo $row_result[csf("issuing_bank_name")];?></p></td>
	                        <td width="100"><p><? echo $pay_term[$row_result[csf('pay_term')]];?></p></td>
	                        <td width="100"><p><? echo $row_result[csf('tenor')];?></p></td>
	                        <td width="100"><p><? echo $incoterm[$row_result[csf('inco_term')]];?></p></td>
	                        <td width="110"><p><? echo $row_result[csf('transfer_bank')];?></p></td>
	                        <td width="110"><p><? echo $row_result[csf('negotiating_bank')];?></p></td>
	                        <td width="110"><P><? echo $row_result[csf('ship_line')];?></P></td>
	                        <td width="78"><p><? echo $row_result[csf('re_imbursing_bank')];?></p></td>
	                        <td width="100"  align="right">
	                        	<a href="##" onclick='openmypage("<? echo $row_result[csf('beneficiary_name')];?>","<? echo $row_result[csf('internal_file_no')];?>","<? echo $row_result[csf('lien_bank')];?>","<? echo $row_result[csf('lc_sc_year')];?>","<? echo $row_result[csf("type")];?>","<? echo $row_result[csf("id")];?>","gross_amount_popup")'><p>
	                        		<? 
	                        		echo $gross_value = $doc_inv_sc_arr[$row_result[csf("id")]][$row_result[csf("type")]];
	                        		?>
	                        	</p></a>
	                        </td>
	                        <td align="right"><a href="##" onclick='openmypage("<? echo $row_result[csf('beneficiary_name')];?>","<? echo $row_result[csf('internal_file_no')];?>","<? echo $row_result[csf('lien_bank')];?>","<? echo $row_result[csf('lc_sc_year')];?>","<? echo $row_result[csf("type")];?>","<? echo $row_result[csf("id")];?>","net_amount_popup")'><p>
	                        	<? 
	                        	echo $net_value =$net_amount_arr[$row_result[csf("id")]][$row_result[csf("type")]]["net_invo_value"];
	                        	?>
	                        	</p></a>
	                        </td>
	                    </tr>
						<?
						if(!in_array($row_result[csf('lc_sc_year')],$item_group_array[$row_result[csf('internal_file_no')]]))
						{
							?>	
							<tr bgcolor="#CCCCCC" title="<? echo $k; ?>">
								<td>&nbsp;</td><td><font color="#CCCCCC"><?php //echo $last_file22;  ?></font></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
								<td align="right"><b>Total</b></td>
								<td align="right"><p> <? echo number_format($sc_value_1_3,2,'.',''); $grand_sc_value_1_3 +=$sc_value_1_3 ;?></p></td>
								<td align="right"><p> <? echo number_format($lc_value_1,2,'.',''); $grand_lc_value_1 +=$lc_value_1 ;?></p></td>
								<td align="right"><p><? echo number_format($balance_1_3_1,2,'.','');$grand_total_balance +=$balance_1_3_1 ; ?></p></td>
								<td align="right"><p><? echo number_format($sc_value_2,2,'.',''); $grand_sc_value_2 +=$sc_value_2 ; ?></p></td>
							   <td align="right"> <p><? echo number_format($lc_value_0_1,2,'.',''); $grand_lc_value_0_1 +=$lc_value_0_1 ; ?></p></td>

							   <td align="right"><p><? echo  number_format($attach_qty); $tot_attach_qty +=$attach_qty ; ?></p></td>

								<td align="right"><p><? echo number_format($file_value,2,'.',''); $grand_total_file_value +=$file_value ; ?></p></td>
								<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
								<td align="right"><? echo number_format($grand_gross_value,2,'.','');?></td>
								<td align="right"><? echo number_format($grand_net_value,2,'.',''); ?></td>
							</tr>
							<?
							$sc_value_1_3 = 0;
							$lc_value_1 = 0;
							$balance_1_3_1 = 0;
							$sc_value_2 = 0;
							$lc_value_0_1 = 0;
							$file_value = 0;
							$total_gross_value = 0;
							$total_net_value = 0;
							$attach_qty=0;
							$print_cond=0;
							$item_group_array[$row_result[csf('internal_file_no')]]['year']=$row_result[csf('lc_sc_year')];
						}
	                    $i++;
;
						if($row_result[csf('type')] == 2 && $row_result[csf('convertible_to_lc')] !=2) $sc_value_1_3 += $row_result[csf('lc_sc_value')];
	                    if(($row_result[csf('replacement_lc')] == 1 && $row_result[csf('type')] == 1 ) || ($row_result[csf('type')] == 2 && $row_result[csf('convertible_to_lc')] ==2 && $row_result[csf('converted_from')]>0) )$lc_value_1 += $row_result[csf('lc_sc_value')]; 
	                     
	                    $balance_1_3_1=$sc_value_1_3-$lc_value_1;
	                    //$grand_total_balance +=$balance_1_3_1;
	                    if($row_result[csf('type')] == 2 && $row_result[csf('convertible_to_lc')] ==2 && $row_result[csf('converted_from')]<1) $sc_value_2 += $row_result[csf('lc_sc_value')];
	                    if($row_result[csf('replacement_lc')] == 2 && $row_result[csf('type')] == 1 )$lc_value_0_1 += $row_result[csf('lc_sc_value')];
	                    $file_value=($lc_value_1+ $balance_1_3_1+$sc_value_2+$lc_value_0_1);

	                    $total_gross_value += $gross_value;
	                    $total_net_value += $net_value;
	                    $grand_gross_value += $gross_value;
	                    $grand_net_value += $net_value;
	                }
	                ?>
	                <tr bgcolor="#CCCCCC">
	                    <td>&nbsp;</td>
	                    <td><font color="#CCCCCC"><?php //echo $last_file22;  ?></font></td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td align="right"><b>Total</b></td>
	                    <td align="right"><p> <? echo number_format($sc_value_1_3,2,'.',''); $grand_sc_value_1_3 +=$sc_value_1_3 ;?></p></td>
	                    <td align="right"><p> <? echo number_format($lc_value_1,2,'.',''); $grand_lc_value_1 +=$lc_value_1 ;?></p></td>
	                    <td align="right"><p><? echo number_format($balance_1_3_1,2,'.','');$grand_total_balance +=$balance_1_3_1 ; ?></p></td>
	                    <td align="right"><p><? echo number_format($sc_value_2,2,'.',''); $grand_sc_value_2 +=$sc_value_2 ; ?></p></td>
	                    <td align="right"> <p><? echo number_format($lc_value_0_1,2,'.',''); $grand_lc_value_0_1 +=$lc_value_0_1 ; ?></p></td>

						<td align="right"> <p><? echo  number_format($tot_attach_qty); $grand_total_attach_qty +=$tot_attach_qty ; ?></p></td>

	                    <td align="right"><p><? echo number_format($file_value,2,'.',''); $grand_total_file_value +=$file_value ; ?></p></td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td>&nbsp;</td>
	                    <td align="right"><? echo  number_format($total_gross_value,2,'.',','); ?></td>
	                    <td align="right"><? echo  number_format($total_net_value,2,'.',','); ?></td>
	                </tr>
	                </tbody>
	            </table>
	        </div>
            <table width="3320" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="report_table_footer">
                <tfoot>
                    <tr>
                        <th width="50" ></th>
                        <th width="100"></th>
                        <th  width="100"></th>
                        <th  width="80"></th>
                        <th width="80"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th align="center" width="80"></th>
                        <th width="110"></th>
                        <th align="center" width="75"></th>
                        <th align="center" width="100"></th>
                        <th align="center" width="100"></th>
                        <th align="center" width="100"></th>
                        <th align="right" width="100"><b>Grand Total:</b></th>
                        <th align="right" width="120" id="grand_sc_value_1_3"><? echo number_format($grand_sc_value_1_3,2,'.',',');  ?></th>
                        <th align="right" width="110" id="grand_lc_value_1"><? echo number_format($grand_lc_value_1,2,'.',',');  ?></th>
                        <th align="right" width="110" id="grand_total_balance"><? echo number_format($grand_total_balance,2,'.',',');  ?></th>
                        <th align="right" width="110" id="grand_sc_value_2"><? echo number_format($grand_sc_value_2,2,'.',',');  ?></th>
                        <th align="right" width="110" id="grand_lc_value_0_1"><? echo number_format($grand_lc_value_0_1,2,'.',',');  ?></th>

						<th align="right" width="100" id="grand_total_attach_qty"><? echo  number_format($grand_total_attach_qty); ?></th>

                        <th align="right" width="110" id="grand_total_file_value"><? echo  number_format($grand_total_file_value,2,'.',','); ?></th>
                        <th align="center" width="100"></th>
                        <th width="110"> </th>
                        <th width="110"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="100"></th>
                        <th width="110"></th>
                        <th width="110"></th>
                        <th width="110"></th>
                        <th width="78"></th>
                        <th width="100"  align="right"> <? echo  number_format($grand_gross_value,2,'.',','); ?> </th>
                        <th align="right"><? echo  number_format($grand_net_value,2,'.',','); ?></th>
                    </tr>
                </tfoot>
            </table> 
        </fieldset>
    </div>
	<?
	foreach (glob("$user_id*.xls") as $filename) 
	{
		if( @filemtime($filename) < (time()-$seconds_old) )
		@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w') or die('can not open');
	$is_created = fwrite($create_new_doc,ob_get_contents()) or die('can not write');
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";

	exit();
}

if($action=="show_file") //shafiq-sumon
{
	echo load_html_head_contents("Booking File","../../../../", 1, 1, $unicode);
    extract($_REQUEST);

	if($type==2)
	{
	 $data_array=sql_select("select image_location  from common_photo_library  where master_tble_id='$lc_sc_no' and form_name='sales_contract_entry' and is_deleted=0 and file_type=2");
	?>
    <table>
        <tr>
        <?
        foreach ($data_array as $row)
        {
			//echo  $row[csf('image_location')].'azzz';
        ?>
        <td><a href="../../../<? echo $row[csf('image_location')] ?>" target="_new">
        <img src="../../../../file_upload/blank_file.png" width="80" height="60"> </a>
        </td>
        <?
        }
        ?>
        </tr>
    </table>
    <?
	}
	exit();
}

if($action=="gross_amount_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>

		function print_window()
		{
			document.getElementById('scroll_body').style.overflow="auto";
			document.getElementById('scroll_body').style.maxHeight="none";
			
			var w = window.open("Surprise", "#");
			var d = w.document.open();
			d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
		'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
		
			d.close();
			document.getElementById('scroll_body').style.overflowY="scroll";
			document.getElementById('scroll_body').style.maxHeight="230px";
		}	
		
	</script>	
	<div style="width:810px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<fieldset style="width:100%; margin-left:10px">
	        <div id="report_container" align="center" style="width:100%">
	            <div style="width:800px">
	                <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="500" align="center">
	                    <thead>
	                        <th width="200">Company Name</th>
	                        <th>File No</th>
	                    </thead>
	                    <tr bgcolor="#EFEFEF">
	                        <td><? echo $company_arr[$company_name]; ?></td>
	                        <td><? echo $file_no; ?></td>
	                    </tr>
	                </table>
	                <br />  
	                <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
	                    <thead>
	                        <th width="40">SL</th>
	                        <th width="150">Invoice No.</th>
	                        <th width="90">Invoice Date</th>
	                        <th width="150">Buyer Name</th>
	                        <th width="110">Invoice Qnty</th>
	                        <th width="80">Rate</th>
	                        <th>Invoice Value</th>
	                    </thead>
	                </table>
	           </div>
	           <div style="width:800px; overflow-y:scroll; max-height:280px" id="scroll_body" align="left" >
	                <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
	                <? 
	                    $i=1; $total_value=0;
	                    $sql_lc="select b.invoice_no, b.invoice_date, b.buyer_id, c.current_invoice_value, c.current_invoice_qnty, c.current_invoice_rate from com_export_lc a, com_export_invoice_ship_mst b, com_export_invoice_ship_dtls c where a.id=b.lc_sc_id and b.is_lc=1 and b.id=c.mst_id and a.beneficiary_name='$company_name' and a.internal_file_no='$file_no' and a.lien_bank like '$bank_id' and a.lc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.is_lc = $lc_sc_type and a.id = $lc_sc_id";
	                    
	                    $result=sql_select($sql_lc);
	                    foreach($result as $row)  
	                    {
	                        $total_value += $row[csf('current_invoice_value')];
	                        
	                        if ($i%2==0)  
	                            $bgcolor="#E9F3FF";
	                        else
	                            $bgcolor="#FFFFFF";
	                        
	                    ?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
	                        <td width="40"><? echo $i; ?></td>
	                        <td width="150"><p><? echo $row[csf('invoice_no')]; ?></p></td>
	                        <td width="90" align="center"><? echo change_date_format($row[csf('invoice_date')]); ?></td>
	                        <td width="150"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
	                        <td width="110" align="right"><? echo number_format($row[csf('current_invoice_qnty')],2); ?></td>
	                        <td width="80" align="right"><? echo number_format($row[csf('current_invoice_rate')],2); ?></td>
	                        <td align="right"><? echo number_format($row[csf('current_invoice_value')],2); ?></td>
	                    </tr>
	                    <?
	                    $i++;
	                    }
	                    
	                    $sql_sc="select b.invoice_no, b.invoice_date, b.buyer_id, c.current_invoice_value, c.current_invoice_qnty, c.current_invoice_rate from com_sales_contract a, com_export_invoice_ship_mst b, com_export_invoice_ship_dtls c where a.id=b.lc_sc_id and b.is_lc=2 and b.id=c.mst_id and a.beneficiary_name='$company_name' and a.internal_file_no='$file_no' and a.lien_bank like '$bank_id' and a.sc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.is_lc = $lc_sc_type and a.id = $lc_sc_id";
	                    //echo $sql_sc;
	                    $result_sc=sql_select($sql_sc);
	                    foreach($result_sc as $row_sc)  
	                    {
	                        $total_value += $row_sc[csf('current_invoice_value')];
	                        if ($i%2==0)  
	                            $bgcolor="#E9F3FF";
	                        else
	                            $bgcolor="#FFFFFF";
	                    
	                        ?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i;?>">
	                            <td width="40"><? echo $i; ?></td>
	                            <td width="150"><p><? echo $row_sc[csf('invoice_no')]; ?></p></td>
	                            <td width="90" align="center"><? echo change_date_format($row_sc[csf('invoice_date')]); ?></td>
	                            <td width="150"><p><? echo $buyer_arr[$row_sc[csf('buyer_id')]]; ?></p></td>
	                            <td width="110" align="right"><? echo number_format($row_sc[csf('current_invoice_qnty')],2); ?></td>
	                            <td width="80" align="right"><? echo number_format($row_sc[csf('current_invoice_rate')],2); ?></td>
	                            <td align="right"><? echo number_format($row_sc[csf('current_invoice_value')],2); ?></td>
	                        </tr>
	                        <?
	                    $i++;
	                    }
	                    ?>
	                     <tfoot>
	                        <th colspan="6" align="right">Total</th>
	                        <th align="right"><? echo number_format($total_value,2); ?></th>
	                    </tfoot>
	                </table>
	            </div>
	        </div>
	    </fieldset>    
	</div>
	<?
	exit();
}

if($action=="net_amount_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>

	function print_window()
	{
		document.getElementById('scroll_body').style.overflow="auto";
		document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css" media="print"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		document.getElementById('scroll_body').style.overflowY="scroll";
		document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
	</script>	
	<div style="width:1210px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
		<fieldset style="width:100%; margin-left:10px">
	        <div id="report_container" align="center" style="width:100%">
	            <div style="width:1200px">
	                <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="500" align="center">
	                    <thead>
	                        <th width="200">Company Name</th>
	                        <th>File No</th>
	                    </thead>
	                    <tr bgcolor="#EFEFEF">
	                        <td><? echo $company_arr[$company_name]; ?></td>
	                        <td><? echo $file_no; ?></td>
	                    </tr>
	                </table>
	                <br />  
	                <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
	                    <thead>
	                        <th width="40">SL</th>
	                        <th width="150">Invoice No.</th>
	                        <th width="90">Invoice Date</th>
	                        <th width="150">Buyer Name</th>
	                        <th width="110">Invoice Value</th>
	                        <th width="100">Discount Value</th>
	                        <th width="100">Bonus Value</th>
	                        <th width="100">Claim Value</th>
	                        <th width="100">Commision Value</th>
	                        <th width="100">Upcharge Value</th>
	                        <th>Net Invoice Value</th>
	                    </thead>
	                </table>
	           </div>
	           <div style="width:1200px; overflow-y:scroll; max-height:290px" id="scroll_body" align="left" >
	                <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
	                <? 
	                    $i=1; $total_value=0; $total_deduct_value=0; $total_net_value=0;
	                    if($db_type==0)
						{
							$sql_lc="select b.invoice_no, b.invoice_date, b.buyer_id, b.invoice_value, b.net_invo_value, IFNULL(b.discount_ammount,0) as discount_ammount, IFNULL(b.bonus_ammount,0) as bonus_ammount, IFNULL(b.claim_ammount,0) as claim_ammount, IFNULL(b.commission,0) as commission,IFNULL(b.upcharge,0) as upcharge from com_export_lc a, com_export_invoice_ship_mst b where a.id=b.lc_sc_id and b.is_lc=1 and a.beneficiary_name='$company_name' and a.internal_file_no='$file_no' and a.lien_bank like '$bank_id' and a.lc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id = '$lc_sc_id'";
						}
						else
						{
							$sql_lc="select b.invoice_no, b.invoice_date, b.buyer_id, b.invoice_value, b.net_invo_value,  nvl(b.discount_ammount,0) as discount_ammount, nvl(b.bonus_ammount,0) as bonus_ammount, nvl(b.claim_ammount,0) as claim_ammount, nvl(b.commission,0) as commission , nvl(b.upcharge,0) as upcharge from com_export_lc a, com_export_invoice_ship_mst b where a.id=b.lc_sc_id and b.is_lc=1 and a.beneficiary_name='$company_name' and a.internal_file_no='$file_no' and a.lien_bank like '$bank_id' and a.lc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id = '$lc_sc_id'";

						}
	                    $result=sql_select($sql_lc);
	                    foreach($result as $row)  
	                    {
	                        $total_value += $row[csf('invoice_value')];
							$total_discount_ammount += $row[csf('discount_ammount')];
							$total_bonus_ammount += $row[csf('bonus_ammount')];
							$total_claim_ammount += $row[csf('claim_ammount')];
							$total_commission += $row[csf('commission')];
							$total_upcharge += $row[csf('upcharge')];
							$total_net_value += $row[csf('net_invo_value')];
	                        
	                        if ($i%2==0)  
	                            $bgcolor="#E9F3FF";
	                        else
	                            $bgcolor="#FFFFFF";
	                        
	                    ?>
	                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
	                        <td width="40"><? echo $i; ?></td>
	                        <td width="150"><p><? echo $row[csf('invoice_no')]; ?></p></td>
	                        <td width="90" align="center"><? echo change_date_format($row[csf('invoice_date')]); ?></td>
	                        <td width="150"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
	                        <td width="110" align="right"><? echo number_format($row[csf('invoice_value')],2); ?></td>
	                        <td width="100" align="right"><? echo number_format($row[csf('discount_ammount')],2); ?></td>
	                        <td width="100" align="right"><? echo number_format($row[csf('bonus_ammount')],2); ?></td>
	                        <td width="100" align="right"><? echo number_format($row[csf('claim_ammount')],2); ?></td>
	                        <td width="100" align="right"><? echo number_format($row[csf('commission')],2); ?></td>
	                        <td width="100" align="right"><? echo number_format($row[csf('upcharge')],2); ?></td>
	                        <td align="right"><? echo number_format($row[csf('net_invo_value')],2); ?></td>
	                    </tr>
	                    <?
	                    $i++;
	                    }
	                    
	                    if($db_type==0)
						{
							$sql_sc="select b.invoice_no, b.invoice_date, b.buyer_id, b.invoice_value, b.net_invo_value, IFNULL(b.discount_ammount,0) as discount_ammount, IFNULL(b.bonus_ammount,0) as bonus_ammount, IFNULL(b.claim_ammount,0) as claim_ammount, IFNULL(b.commission,0) as commission,IFNULL(b.upcharge,0) as upcharge from com_sales_contract a, com_export_invoice_ship_mst b where a.id=b.lc_sc_id and b.is_lc=2 and a.beneficiary_name='$company_name' and a.internal_file_no='$file_no' and a.lien_bank like '$bank_id' and a.sc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id = '$lc_sc_id'";
						}
						else
						{
							$sql_sc="select b.invoice_no, b.invoice_date, b.buyer_id, b.invoice_value, b.net_invo_value, nvl(b.discount_ammount,0) as discount_ammount, nvl(b.bonus_ammount,0) as bonus_ammount, nvl(b.claim_ammount,0) as claim_ammount, nvl(b.commission,0) as commission , nvl(b.upcharge,0) as upcharge from com_sales_contract a, com_export_invoice_ship_mst b where a.id=b.lc_sc_id and b.is_lc=2 and a.beneficiary_name='$company_name' and a.internal_file_no='$file_no' and a.lien_bank like '$bank_id' and a.sc_year like '$text_year' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.id = '$lc_sc_id'";
						}
						//echo $sql_sc;
	                    $result_sc=sql_select($sql_sc);
	                    foreach($result_sc as $row_sc)  
	                    {
	                        $total_value += $row_sc[csf('invoice_value')];
							$total_discount_ammount += $row_sc[csf('discount_ammount')];
							$total_bonus_ammount += $row_sc[csf('bonus_ammount')];
							$total_claim_ammount += $row_sc[csf('claim_ammount')];
							$total_commission += $row_sc[csf('commission')];
							$total_upcharge += $row_sc[csf('upcharge')];
							$total_net_value += $row_sc[csf('net_invo_value')];
							
	                        if ($i%2==0)  
	                            $bgcolor="#E9F3FF";
	                        else
	                            $bgcolor="#FFFFFF";
	                        ?>
	                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i;?>">
	                            <td width="40"><? echo $i; ?></td>
	                            <td width="150"><p><? echo $row_sc[csf('invoice_no')]; ?></p></td>
	                            <td width="90" align="center"><? echo change_date_format($row_sc[csf('invoice_date')]); ?></td>
	                            <td width="150"><p><? echo $buyer_arr[$row_sc[csf('buyer_id')]]; ?></p></td>
	                            <td width="110" align="right"><? echo number_format($row_sc[csf('invoice_value')],2); ?></td>
	                            <td width="100" align="right"><? echo number_format($row_sc[csf('discount_ammount')],2); ?></td>
		                        <td width="100" align="right"><? echo number_format($row_sc[csf('bonus_ammount')],2); ?></td>
		                        <td width="100" align="right"><? echo number_format($row_sc[csf('claim_ammount')],2); ?></td>
		                        <td width="100" align="right"><? echo number_format($row_sc[csf('commission')],2); ?></td>
		                        <td width="100" align="right"><? echo number_format($row_sc[csf('upcharge')],2); ?></td>
	                            <td align="right"><? echo number_format($row_sc[csf('net_invo_value')],2); ?></td>
	                        </tr>
	                   	<?
	                    	$i++;
	                    }
	                    ?>
	                     <tfoot>
	                        <th colspan="4" align="right">Total</th>
	                        <th align="right"><? echo number_format($total_value,2); ?></th>
	                        <th align="right"><? echo number_format($total_discount_ammount,2); ?></th>
	                        <th align="right"><? echo number_format($total_bonus_ammount,2); ?></th>
	                        <th align="right"><? echo number_format($total_claim_ammount,2); ?></th>
	                        <th align="right"><? echo number_format($total_commission,2); ?></th>
	                        <th align="right"><? echo number_format($total_upcharge,2); ?></th>
	                        <th align="right"><? echo number_format($total_net_value,2); ?></th>
	                    </tfoot>
	                </table>
	            </div>
	        </div>
	    </fieldset>    
	</div>
	<?
	exit();
}
disconnect($con);
?>