<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];


$company_arr=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
$count_arr=return_library_array( "select id, yarn_count from lib_yarn_count",'id','yarn_count');
$bank_arr=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');

$paymentDataArray=sql_select("SELECT invoice_id, sum(accepted_ammount) as accepted_ammount, payment_date FROM  where payment_head=40 and status_active=1 and is_deleted=0 group by invoice_id");
$payment_arr = array();
foreach($paymentDataArray as $row)
{
	$payment_arr[$row[csf('invoice_id')]]['amnt'] = $row[csf('accepted_ammount')];
	$payment_arr[$row[csf('invoice_id')]]['date'] = $row[csf('payment_date')];
}

$tmplte=explode("**",$data);

if ($tmplte[0]=="viewtemplate") $template=$tmplte[1]; else $template=$lib_report_template_array[$_SESSION['menu_id']]['0'];
if ($template=="") $template=1;

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	
	$item_cate=str_replace("'","",$cbo_item_category_id);
	if(str_replace("'","",$cbo_issuing_bank)==0) $issuing_bank="%%"; else $issuing_bank=str_replace("'","",$cbo_issuing_bank);
	if(str_replace("'","",$cbo_lc_type_id)==0) $lc_type_id="%%"; else $lc_type_id=str_replace("'","",$cbo_lc_type_id);
	
	$recvDataArray=sql_select("select pi_wo_batch_no, sum(cons_quantity) as qnty, sum(cons_amount) as amnt from inv_transaction where item_category=$cbo_item_category_id and receive_basis=1 and transaction_type=1 and status_active=1 and is_deleted=0 group by pi_wo_batch_no");

	$receive_arr = array();
	foreach($recvDataArray as $row)
	{
		$receive_arr[$row[csf('pi_wo_batch_no')]]['qnty'] = $row[csf('qnty')];
		$receive_arr[$row[csf('pi_wo_batch_no')]]['amnt'] = $row[csf('amnt')];
	}

	if($template==1)
	{
		ob_start();
		
		if($item_cate == 1) $table_width=4370; else $table_width=4220;
	?>
        <div style="width:<? echo $table_width+30; ?>px; margin-left:10px">
            <fieldset style="width:100%;">	 
                <table width="<? echo $table_width; ?>" cellpadding="0" cellspacing="0" id="caption">
                    <tr>
                       <td align="center" width="100%" colspan="20" class="form_caption" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_name)]; ?></strong></td>
                    </tr> 
                    <tr>  
                       <td align="center" width="100%" colspan="20" class="form_caption" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
                    </tr>  
                </table>
                <br />
                <table width="<? echo $table_width; ?>" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr>
                            <th colspan="<? if($item_cate == 1) echo 14; else echo 12;?>" >PI Details</th>
                            <th colspan="9">BTB LC Details</th>
                            <th colspan="16">Invoice Details</th>
                            <th colspan="4">Payment Details</th>
                            <th colspan="3">Matarials Received Information</th>
                        </tr>
                        <tr>    
                            <th width="40">SL</th>
                            <th width="120">PI No</th>
                            <th width="80">PI Date</th>
                            <th width="80">Last Ship Date</th>
                            <th width="140">Supplier Name</th>
                            <th width="100">Item Category</th>
                            <? 
							if($item_cate == 1) 
                            { 
                            ?>            
                                <th width="80">Yarn Count</th>
                                <th width="100">Yarn Type</th>
                            <?
                            }
                            ?>
                            <th width="60">UOM</th>
                            <th width="80">PI Quantity</th> 
                            <th width="80">Unit Price</th>
                            <th width="100">PI Value</th>
                            <th width="80">Currency</th>
                            <th width="120">Indentor Name</th>
                            <th width="80"> LC Date</th>
                            <th width="120">LC No </th>
                            <th width="120">Issuing Bank</th>
                            <th width="110">Pay Term</th>
                            <th width="60">Tenor</th>
                            <th width="100">LC Amount</th>
                            <th width="80">Shipment Date</th>
                            <th width="80">Expiry Date</th>
                            <th width="80">ETD Date	</th>  
                            <th width="120">ETD Port</th>
                            <th width="120">ETA Port</th>
                            <th width="80">ETA Date</th>          
                            <th width="120">Invoice No</th>
                            <th width="80">Invoice Date</th>
                            <th width="80">Inco Term</th>
                            <th width="100">Inco Term Place</th>
                            <th width="120">B/L No</th>
                            <th width="80">BL Date</th>
                            <th width="120">Mother Vassel</th> 
                            <th width="120">Feedar Vassel</th> 
                            <th width="80">Continer No</th>
                            <th width="80">Pkg Qty</th>            
                            <th width="100">Doc Send to CNF</th>
                            <th width="100">NN Doc Received Date</th>
                            <th width="120">Bill Of Entry No</th>
                            <th width="80">Maturity Date</th>
                            <th width="80">Maturity Month </th>
                            <th width="80">Payment Date</th> 
                            <th width="90">Paid Amount</th>
                            <th width="80">MRR Qnty</th>
                            <th width="90">MRR Value</th>
                            <th>Short Value</th>
                        </tr>
                    </thead>
                </table>
                <div style="width:<? echo $table_width+20; ?>px; max-height:300px; overflow-y:scroll" id="scroll_body">
   		 			<table width="<? echo $table_width; ?>" class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all" id="table_body">
                    <? 
						if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="") $pi_date_cond=" and a.pi_date between $txt_date_from and $txt_date_to";
						if(str_replace("'","",$txt_maturity_date_from)!="" && str_replace("'","",$txt_maturity_date_to)!="") $mat_str_cond=" and maturity_date between $txt_maturity_date_from and $txt_maturity_date_to";
						if($db_type==0)
						{	
							$sql="select a.id, a.lc_value, a.lc_date, a.lc_number, a.tenor, a.last_shipment_date, a.lc_expiry_date, a.lc_type_id, a.issuing_bank_id, a.etd_date, a.payterm_id, group_concat(distinct(b.pi_id)) as pi_id from com_btb_lc_master_details a left join com_btb_lc_pi b on a.id=b.com_btb_lc_master_details_id and b.is_deleted=0 and b.status_active=1 where a.importer_id=$cbo_company_name and a.item_category_id=$cbo_item_category_id and a.lc_type_id like '$lc_type_id' and a.issuing_bank_id like '$issuing_bank' and a.is_deleted=0 and a.status_active=1 group by a.id";
						}
						else if($db_type==2)
						{
							$sql="select a.id, a.lc_value, a.lc_date, a.lc_number, a.tenor, a.last_shipment_date, a.lc_expiry_date, a.lc_type_id, a.issuing_bank_id, a.etd_date, a.payterm_id, LISTAGG(CAST(b.pi_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.pi_id) as pi_id from com_btb_lc_master_details a left join com_btb_lc_pi b on a.id=b.com_btb_lc_master_details_id and b.is_deleted=0 and b.status_active=1 where a.importer_id=$cbo_company_name and a.item_category_id=$cbo_item_category_id and a.lc_type_id like '$lc_type_id' and a.issuing_bank_id like '$issuing_bank' and a.is_deleted=0 and a.status_active=1 group by a.id, a.lc_value, a.lc_date, a.lc_number, a.tenor, a.last_shipment_date, a.lc_expiry_date, a.lc_type_id, a.etd_date, a.payterm_id, a.issuing_bank_id";
						}
						//echo $sql; die;
						$result=sql_select($sql); $i=1; $btb_pi_id=array();
						foreach($result as $row)
						{
							$invoice_sql = "select id as invoice_id, invoice_no, invoice_date, bill_no, bill_date, doc_to_cnf, feeder_vessel, mother_vessel, eta_date, port_of_loading, port_of_discharge, copy_doc_receive_date, bill_of_entry_no, pkg_quantity, container_no, maturity_date, inco_term, inco_term_place FROM com_import_invoice_mst where find_in_set(".$row[csf('id')].",btb_lc_id) and is_lc=1 and status_active=1 and is_deleted=0 $mat_str_cond";
							
							$mrr_qnty=0; $mrr_value=0;
								
							$pi_id=array_unique(explode(",",$row[csf('pi_id')]));
							foreach($pi_id as $val)
							{
								$mrr_qnty+=$receive_arr[$val]['qnty']; 
								$mrr_value+=$receive_arr[$val]['amnt'];
							}
								
							if($row[csf('pi_id')]=="")
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="40"><? echo $i; ?></td>
									<td width="120">&nbsp;</td>
									<td width="80">&nbsp;</td>
                                    <td width="80">&nbsp;</td>
									<td width="140">&nbsp;</td>
									<td width="100">&nbsp;</td>
									<? 
									if($item_cate == 1) 
									{ 
									?>            
										<td width="80">&nbsp;</td>
										<td width="100">&nbsp;</td>
									<?
									}
									?>
									<td width="60">&nbsp;</td>
									<td width="80">&nbsp;</td> 
									<td width="80">&nbsp;</td>
									<td width="100">&nbsp;</td>
									<td width="80">&nbsp;</td>
									<td width="120">&nbsp;</td>
                                    <td width="80" align="center"><? echo change_date_format($row[csf('lc_date')]); ?>&nbsp;</td>
                                    <td width="120"><p><? echo $row[csf('lc_number')]; ?></p></td>
                                    <td width="120"><p><? echo $bank_arr[$row[csf('issuing_bank_id')]]; ?></p></td>
                                    <td width="110"><p><? echo $pay_term[$row[csf('payterm_id')]]; ?>&nbsp;</p></td>
                                    <td width="60">&nbsp;<? echo $row[csf('tenor')]; ?></td>
                                    <td width="100" align="right"><? echo number_format($row[csf('lc_value')],2,'.',''); ?>&nbsp;</td>
                                    <td width="80" align="center"><? echo change_date_format($row[csf('last_shipment_date')]); ?>&nbsp;</td>
                                    <td width="80" align="center"><? echo change_date_format($row[csf('lc_expiry_date')]); ?>&nbsp;</td>
                                    <td width="80" align="center"><? echo change_date_format($row[csf('etd_date')]); ?>&nbsp;</td>
                                    <?
									$result_invoice=sql_select($invoice_sql);
									$tot_invoice=count($result_invoice);
									if($tot_invoice>0)
									{ 
									?>
										<td valign="middle" align="left" rowspan="<? echo $rowspan; ?>" colspan="23">
											<table width="100%"  border="0" class="rpt_table" rules="all">
											<?
											foreach($result_invoice as $row_invoice)
											{
											?>
												<tr>
													<td width="120" style="border-left:hidden;"><p><? echo $row_invoice[csf('port_of_loading')]; ?>&nbsp;</p></td>
													<td width="120"><p><? echo $row_invoice[csf('port_of_discharge')]; ?>&nbsp;</p></td>
													<td width="80" align="center"><? echo change_date_format($row_invoice[csf('eta_date')]); ?>&nbsp;</td>          
													<td width="120"><p><? echo $row_invoice[csf('invoice_no')]; ?>&nbsp;</p></td>
													<td width="80" align="center"><? echo change_date_format($row_invoice[csf('invoice_date')]); ?>&nbsp;</td>
													<td width="80"><p><? echo $incoterm[$row_invoice[csf('inco_term')]]; ?>&nbsp;</p></td>
													<td width="100"><p><? echo $row_invoice[csf('inco_term_place')]; ?>&nbsp;</p></td>
													<td width="120"><p><? echo $row_invoice[csf('bill_no')]; ?>&nbsp;</p></td>
													<td width="80" align="center"><? echo change_date_format($row_invoice[csf('bill_date')]); ?>&nbsp;</td>
													<td width="120"><p><? echo $row_invoice[csf('mother_vessel')]; ?>&nbsp;</p></td> 
													<td width="120"><p><? echo $row_invoice[csf('feeder_vessel')]; ?>&nbsp;</p></td> 
													<td width="80"><p><? echo $row_invoice[csf('container_no')]; ?>&nbsp;</p></td>
													<td width="80" align="right"><? echo $row_invoice[csf('pkg_quantity')]; ?>&nbsp;</td>            
													<td width="100" align="center"><? echo change_date_format($row_invoice[csf('doc_to_cnf')]); ?>&nbsp;</td>
													<td width="100" align="center"><? echo change_date_format($row_invoice[csf('copy_doc_receive_date')]); ?>&nbsp;</td>
													<td width="120"><p><? echo $row_invoice[csf('bill_of_entry_no')]; ?>&nbsp;</p></td>
													<td width="80" align="center"><? echo change_date_format($row_invoice[csf('maturity_date')]); ?>&nbsp;</td>
													<td width="80" align="center"><? echo date('F',strtotime($row_invoice[csf('maturity_date')])); ?></td>
                                                    <td width="80" align="center"><? echo change_date_format($payment_arr[$row_invoice[csf('invoice_id')]]['date']);?>&nbsp;</td>
                                                    <td width="90" align="right"><? echo number_format($payment_arr[$row_invoice[csf('invoice_id')]]['amnt'],2,'.',''); ?>&nbsp;</td>
                                                    <td width="80" align="right"><? echo number_format($mrr_qnty,2,'.',''); ?>&nbsp;</td>
                                                    <td width="90" align="right"><? echo number_format($mrr_value,2,'.',''); ?>&nbsp;</td>
                                                    <td align="right"><? echo number_format($mrr_value-$row[csf('lc_value')],2,'.',''); ?>&nbsp;</td>
												</tr>
											<?	
                                            }
                                            ?>
											</table>
										</td>
									 <?       
									}
									else
									{
									?>
										<td width="120">&nbsp;</td>
										<td width="120">&nbsp;</td>
										<td width="80" align="center">&nbsp;</td>          
										<td width="120">&nbsp;</td>
										<td width="80" align="center">&nbsp;</td>
										<td width="80">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="120">&nbsp;</td>
										<td width="80">&nbsp;</td>
										<td width="120">&nbsp;</td> 
										<td width="120">&nbsp;</td> 
										<td width="80">&nbsp;</td>
										<td width="80">&nbsp;</td>            
										<td width="100">&nbsp;</td>
										<td width="100">&nbsp;</td>
										<td width="120">&nbsp;</td>
										<td width="80" align="center">&nbsp;</td>
										<td width="80">&nbsp;</td>
										<td width="80" align="center">&nbsp;</td>
										<td width="90">&nbsp;</td>
										<td width="80" align="right">&nbsp;</td>
                                        <td width="90" align="right">&nbsp;</td>
                                        <td align="right">&nbsp;</td>
									<?	
									}
									?>
								</tr>
							<?
								$i++;	
							}
							else
							{
								$s=0; 
								$sql_pi="select a.id, a.item_category_id, a.supplier_id, a.pi_number, a.pi_date, a.last_shipment_date, a.currency_id, a.intendor_name, b.count_name, b.yarn_type, b.uom, b.quantity, b.rate, b.amount, b.net_pi_rate, b.net_pi_amount from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and b.is_deleted=0 and b.status_active=1 and a.id in(".$row[csf('pi_id')].") $pi_date_cond";
								$result_pi=sql_select($sql_pi);
								$rowspan=count($result_pi);
								if($rowspan>0)
								{ 
									foreach($result_pi as $row_pi)
									{
										
										if(!in_array($row_pi[csf('id')],$btb_pi_id))
										{
											$btb_pi_id[]=$row_pi[csf('id')];
										}
										
										if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
									?>
										<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
											<td width="40"><? echo $i; ?></td>
											<td width="120"><p><? echo $row_pi[csf('pi_number')]; ?></p></td>
											<td width="80" align="center"><? echo change_date_format($row_pi[csf('pi_date')]); ?></td>
											<td width="80" align="center"><? echo change_date_format($row_pi[csf('last_shipment_date')]); ?></td>
											<td width="140"><p><? echo $supplier_arr[$row_pi[csf('supplier_id')]]; ?></p></td>
											<td width="100"><p><? echo $item_category[$row_pi[csf('item_category_id')]]; ?></p></td>
											<? 
											if($item_cate == 1) 
											{ 
											?>            
												<td width="80"><p><? echo $count_arr[$row_pi[csf('count_name')]]; ?>&nbsp;</p></td>
												<td width="100"><p><? echo $yarn_type[$row_pi[csf('yarn_type')]]; ?>&nbsp;</p></td>
											<?
											}
											?>
											<td width="60" align="center"><p><? echo $unit_of_measurement[$row_pi[csf('uom')]]; ?></p></td>
											<td width="80" align="right"><? echo number_format($row_pi[csf('quantity')],2,'.',''); ?>&nbsp;</td> 
											<td width="80" align="right"><? echo number_format($row_pi[csf('rate')],2,'.',''); ?>&nbsp;</td>
											<td width="100" align="right"><? echo number_format($row_pi[csf('amount')],2,'.',''); ?>&nbsp;</td>
											<td width="80"><P><? echo $currency[$row_pi[csf('currency_id')]]; ?>&nbsp;</P></td>
											<td width="120"><P><? echo $supplier_arr[$row_pi[csf('intendor_name')]]; ?>&nbsp;</P></td>
											<?
											if($s==0)
											{
											?>
												<td width="80" align="center" rowspan="<? echo $rowspan; ?>"><? echo change_date_format($row[csf('lc_date')]); ?>&nbsp;</td>
												<td width="120" rowspan="<? echo $rowspan; ?>"><p><? echo $row[csf('lc_number')]; ?></p></td>
                                                <td width="120" rowspan="<? echo $rowspan; ?>"><p><? echo $bank_arr[$row[csf('issuing_bank_id')]]; ?></p></td>
												<td width="110" rowspan="<? echo $rowspan; ?>"><p><? echo $pay_term[$row[csf('payterm_id')]]; ?>&nbsp;</p></td>
												<td width="60" rowspan="<? echo $rowspan; ?>">&nbsp;<? echo $row[csf('tenor')]; ?></td>
												<td width="100" align="right" rowspan="<? echo $rowspan; ?>"><? echo number_format($row[csf('lc_value')],2,'.',''); ?>&nbsp;</td>
												<td width="80" align="center" rowspan="<? echo $rowspan; ?>"><? echo change_date_format($row[csf('last_shipment_date')]); ?>&nbsp;</td>
												<td width="80" align="center" rowspan="<? echo $rowspan; ?>"><? echo change_date_format($row[csf('lc_expiry_date')]); ?>&nbsp;</td>
												<td width="80" align="center" rowspan="<? echo $rowspan; ?>"><? echo change_date_format($row[csf('etd_date')]); ?>&nbsp;</td>  
												<?
												$result_invoice=sql_select($invoice_sql);
												$tot_invoice=count($result_invoice);
												if($tot_invoice>0)
												{
												?>
													<td valign="middle" align="left" rowspan="<? echo $rowspan; ?>" colspan="23">
														<table width="100%"  border="0" class="rpt_table" rules="all">
														<?
														$k=1;
														foreach($result_invoice as $row_invoice)
														{
														?>
															<tr>
																<td width="120" style="border-left:hidden;"><p><? echo $row_invoice[csf('port_of_loading')]; ?>&nbsp;</p></td>
																<td width="120"><p><? echo $row_invoice[csf('port_of_discharge')]; ?>&nbsp;</p></td>
																<td width="80" align="center"><? echo change_date_format($row_invoice[csf('eta_date')]); ?>&nbsp;</td>          
																<td width="120"><p><? echo $row_invoice[csf('invoice_no')]; ?>&nbsp;</p></td>
																<td width="80" align="center"><? echo change_date_format($row_invoice[csf('invoice_date')]); ?>&nbsp;</td>
																<td width="80"><p><? echo $incoterm[$row_invoice[csf('inco_term')]]; ?>&nbsp;</p></td>
																<td width="100"><p><? echo $row_invoice[csf('inco_term_place')]; ?>&nbsp;</p></td>
																<td width="120"><p><? echo $row_invoice[csf('bill_no')]; ?>&nbsp;</p></td>
																<td width="80" align="center"><? echo change_date_format($row_invoice[csf('bill_date')]); ?>&nbsp;</td>
																<td width="120"><p><? echo $row_invoice[csf('mother_vessel')]; ?>&nbsp;</p></td> 
																<td width="120"><p><? echo $row_invoice[csf('feeder_vessel')]; ?>&nbsp;</p></td> 
																<td width="80"><p><? echo $row_invoice[csf('container_no')]; ?>&nbsp;</p></td>
																<td width="80" align="right"><? echo $row_invoice[csf('pkg_quantity')]; ?>&nbsp;</td>            
																<td width="100" align="center"><? echo change_date_format($row_invoice[csf('doc_to_cnf')]); ?>&nbsp;</td>
																<td width="100" align="center"><? echo change_date_format($row_invoice[csf('copy_doc_receive_date')]); ?>&nbsp;</td>
																<td width="120"><p><? echo $row_invoice[csf('bill_of_entry_no')]; ?>&nbsp;</p></td>
																<td width="80" align="center"><? echo change_date_format($row_invoice[csf('maturity_date')]); ?>&nbsp;</td>
																<td width="80" align="center"><? echo date('F',strtotime($row_invoice[csf('maturity_date')])); ?></td>
																<td width="80" align="center">
																	<? echo change_date_format($payment_arr[$row_invoice[csf('invoice_id')]]['date']);?>&nbsp;
                                                                </td>
																<td width="90" align="right">
																	<? echo number_format($payment_arr[$row_invoice[csf('invoice_id')]]['amnt'],2,'.',''); ?>&nbsp;
                                                                </td>
                                                                <?
																if($k==1)
																{
																?>
                                                                    <td width="80" align="right" rowspan="<? echo $rowspan; ?>"><a href="javascript:void(0)" onclick="show_qty_details('<? echo $row[csf('pi_id')]; ?>',<? echo $item_cate; ?>)"><? echo number_format($mrr_qnty,2,'.',''); ?></a>&nbsp;</td>
                                                                    <td width="90" align="right" rowspan="<? echo $rowspan; ?>">
																		<? echo number_format($mrr_value,2,'.',''); ?>&nbsp;
                                                                    </td>
                                                                    <td align="right" rowspan="<? echo $rowspan; ?>">
																		<? echo number_format($mrr_value-$row[csf('lc_value')],2,'.',''); ?>&nbsp;
                                                                    </td>														
                                                                <?
																}
																?>
															</tr>
														<?	
															$k++;
														}
														?>
														</table>
													</td>
												 <?       
												}
												else
												{
												?>
													<td width="120">&nbsp;</td>
													<td width="120">&nbsp;</td>
													<td width="80" align="center">&nbsp;</td>          
													<td width="120">&nbsp;</td>
													<td width="80" align="center">&nbsp;</td>
													<td width="80">&nbsp;</td>
													<td width="100">&nbsp;</td>
													<td width="120">&nbsp;</td>
													<td width="80">&nbsp;</td>
													<td width="120">&nbsp;</td> 
													<td width="120">&nbsp;</td> 
													<td width="80">&nbsp;</td>
													<td width="80">&nbsp;</td>            
													<td width="100">&nbsp;</td>
													<td width="100">&nbsp;</td>
													<td width="120">&nbsp;</td>
													<td width="80" align="center">&nbsp;</td>
													<td width="80">&nbsp;</td>
													<td width="80" align="center">&nbsp;</td>
													<td width="90">&nbsp;</td>
													<td width="80" align="right"><a href="javascript:void(0)" onclick="show_qty_details('<? echo $row[csf('pi_id')]; ?>',<? echo $item_cate; ?>)"><? echo number_format($mrr_qnty,2,'.',''); ?></a>&nbsp;</td>
                                                    <td width="90" align="right"><? echo number_format($mrr_value,2,'.',''); ?>&nbsp;</td>
                                                    <td align="right"><? echo number_format($mrr_value-$row[csf('lc_value')],2,'.',''); ?>&nbsp;</td>
												<?	
												}
											}
											?>
										</tr>
										<?
										$i++;
										$s++;
									}
								}
							}
						}
						
						//if(str_replace("'","",$txt_date_from)=="" && str_replace("'","",$txt_date_to)=="") 
						//{
						if(count($btb_pi_id)>0) $pi_id_cond=" and a.id not in(".implode(",",$btb_pi_id).")";
						$query="select a.id, a.item_category_id, a.supplier_id, a.pi_number, a.pi_date, a.last_shipment_date, a.currency_id, a.intendor_name, b.count_name, b.yarn_type, b.uom, b.quantity, b.rate, b.amount, b.net_pi_rate, b.net_pi_amount from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and b.is_deleted=0 and b.status_active=1 and a.item_category_id=$cbo_item_category_id and a.importer_id=$cbo_company_name $pi_id_cond $pi_date_cond";
						$result=sql_select($query);
						foreach($result as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							
							$mrr_qnty=$receive_arr[$row[csf('id')]]['qnty']; 
							$mrr_value=$receive_arr[$row[csf('id')]]['amnt'];	
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td width="40"><? echo $i; ?></td>
								<td width="120"><p><? echo $row[csf('pi_number')]; ?></p></td>
								<td width="80" align="center"><? echo change_date_format($row[csf('pi_date')]); ?></td>
								<td width="80" align="center"><? echo change_date_format($row[csf('last_shipment_date')]); ?></td>
								<td width="140"><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?></p></td>
								<td width="100"><p><? echo $item_category[$row[csf('item_category_id')]]; ?></p></td>
								<? 
								if($item_cate == 1) 
								{ 
								?>            
									<td width="80"><p><? echo $count_arr[$row[csf('count_name')]]; ?>&nbsp;</p></td>
									<td width="100"><p><? echo $yarn_type[$row[csf('yarn_type')]]; ?>&nbsp;</p></td>
								<?
								}
								?>
								<td width="60" align="center"><p><? echo $unit_of_measurement[$row[csf('uom')]]; ?></p></td>
								<td width="80" align="right"><? echo number_format($row[csf('quantity')],2,'.',''); ?>&nbsp;</td> 
								<td width="80" align="right"><? echo number_format($row[csf('rate')],2,'.',''); ?>&nbsp;</td>
								<td width="100" align="right"><? echo number_format($row[csf('amount')],2,'.',''); ?>&nbsp;</td>
								<td width="80"><P><? echo $currency[$row[csf('currency_id')]]; ?>&nbsp;</P></td>
								<td width="120"><P><? echo $supplier_arr[$row[csf('intendor_name')]]; ?>&nbsp;</P></td>
								<td width="80">&nbsp;</td>
								<td width="120">&nbsp;</td>
                                <td width="120">&nbsp;</td>
								<td width="110">&nbsp;</td>
								<td width="60">&nbsp;</td>
								<td width="100">&nbsp;</td>
								<td width="80">&nbsp;</td>
								<td width="80">&nbsp;</td>
								<td width="80">&nbsp;</td>  
								<td width="120">&nbsp;</td>
								<td width="120">&nbsp;</td>
								<td width="80" align="center">&nbsp;</td>          
								<td width="120">&nbsp;</td>
								<td width="80" align="center">&nbsp;</td>
								<td width="80">&nbsp;</td>
								<td width="100">&nbsp;</td>
								<td width="120">&nbsp;</td>
								<td width="80">&nbsp;</td>
								<td width="120">&nbsp;</td> 
								<td width="120">&nbsp;</td> 
								<td width="80">&nbsp;</td>
								<td width="80">&nbsp;</td>            
								<td width="100">&nbsp;</td>
								<td width="100">&nbsp;</td>
								<td width="120">&nbsp;</td>
								<td width="80" align="center">&nbsp;</td>
								<td width="80">&nbsp;</td>
								<td width="80" align="center">&nbsp;</td>
								<td width="90">&nbsp;</td>
								<td width="80" align="right"><a href="javascript:void(0)" onclick="show_qty_details(<? echo $row[csf('id')]; ?>,<? echo $item_cate; ?>)"><? echo number_format($mrr_qnty,2,'.',''); ?></a>&nbsp;</td>
								<td width="90" align="right"><? echo number_format($mrr_value,2,'.',''); ?>&nbsp;</td>
								<td align="right"><? echo number_format($mrr_value,2,'.',''); ?>&nbsp;</td>
							</tr>
						 <?	
						$i++;
						}
						//}
						?>
                	</table>
                </div>
            </fieldset>
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
	echo "$total_data****$filename";
	exit();
}

if($action=="receive_qnty")
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
<div style="width:490px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/>
	<fieldset style="width:100%; margin-left:10px">
        <div id="report_container" align="center" style="width:480px">
            <div style="width:480px">
                <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="100%" align="center">
                    <thead>
                        <th align="center">Category Name : <? echo $item_category[$category_id]; ?></th>
                    </thead>
                </table>
                <br />    
                <table class="rpt_table" border="1" rules="all" width="100%" cellpadding="0" cellspacing="0">
                    <thead>
                        <th width="40">SL</th>
                        <th width="140">MRR No.</th>
                        <th width="90">Receive Date</th>
                        <th width="70">UOM</th>
                        <th>Receive Qty</th>
                    </thead>
                </table>
            </div>
            <div style="width:100%; overflow-y:scroll; max-height:230px" id="scroll_body" align="left" >
                <table class="rpt_table" border="1" rules="all" width="462" cellpadding="0" cellspacing="0">
                <? 
                    $i=1; $total_qty=0; 
                    $sql="select a.recv_number, a.receive_date, b.cons_uom, sum(b.cons_quantity) as qnty, sum(b.cons_amount) as amnt from inv_receive_master a, inv_transaction b where a.id=b.mst_id and a.item_category=$category_id and b.item_category=$category_id and b.receive_basis=1 and b.transaction_type=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.pi_wo_batch_no in($pi_id) group by a.id,a.recv_number, a.receive_date, b.cons_uom";
                    $result=sql_select($sql);
                    foreach($result as $row)  
                    {
                        $total_qty += $row[csf('qnty')];
                        
                        if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    ?>
                        <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            <td width="40"><? echo $i; ?></td>
                            <td width="140"><p><? echo $row[csf('recv_number')]; ?></p></td>
                            <td width="90" align="center"><? echo change_date_format($row[csf('receive_date')]); ?></td>
                            <td width="70" align="center"><? echo $unit_of_measurement[$row[csf('cons_uom')]]; ?></td>
                            <td align="right"><? echo number_format($row[csf('qnty')],2); ?></td>
                        </tr>
                    <?
                    $i++;
                    }
                    ?>
                     <tfoot>
                        <tr class="tbl_bottom">
                            <td colspan="4" align="right">Total</td>
                            <td align="right"><? echo number_format($total_qty,2); ?></td>
                        </tr>
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
