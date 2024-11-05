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

$paymentDataArray=sql_select("SELECT invoice_id, payment_date, sum(accepted_ammount) as accepted_ammount, payment_date FROM  where payment_head=40 and status_active=1 and is_deleted=0 group by invoice_id,payment_date");
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
                <?
                $exel_short="<table width='900'>
									 <tr>
										<th colspan='9' align='center'>".$company_library[$company_name]."</th>
									 </tr>
									 <tr>
										<th colspan='9' align='center'>Purchase Recap For Yarn</th>
									 </tr>
								</table>
								<table class='rpt_table' border='1' rules='all' width='900'>
									<thead>
										<th>LC Date</th>
										<th>Quantity</th>
										<th>UOM</th>
										<th>Unit Price</th>
										<th>Value</th>
										<th>Yarn Count</th>
										<th>Yarn Type</th>
										<th>LC No</th>
										<th>Supplier Name</th>
									</thead>
									";
				?>
                <div style="width:<? echo $table_width+20; ?>px; max-height:300px; overflow-y:scroll" id="scroll_body">
   		 			<table width="<? echo $table_width; ?>" class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all" id="table_body">
                    <? 
					$mat_str_cond=$pi_date_cond="";
					if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="") $pi_date_cond=" and a.pi_date between $txt_date_from and $txt_date_to";
					if(str_replace("'","",$txt_maturity_date_from)!="" && str_replace("'","",$txt_maturity_date_to)!="") $mat_str_cond=" and maturity_date between $txt_maturity_date_from and $txt_maturity_date_to";
						
					$invoice_data_arr=$pi_data_arr=array();	
					$invoice_sql =sql_select("select id as invoice_id, btb_lc_id, invoice_no, invoice_date, bill_no, bill_date, doc_to_cnf, feeder_vessel, mother_vessel, eta_date, port_of_loading, port_of_discharge, copy_doc_receive_date, bill_of_entry_no, pkg_quantity, container_no, maturity_date, inco_term, inco_term_place, company_acc_date, bank_acc_date 
					FROM com_import_invoice_mst 
					where status_active=1 and is_deleted=0 $mat_str_cond");
					$bill_of_entry_no_check=array();
					foreach($invoice_sql as $row)
					{
						$invoice_data_arr[$row[csf("btb_lc_id")]][$row[csf("invoice_id")]]["invoice_id"]=$row[csf("invoice_id")];
						$invoice_data_arr[$row[csf("btb_lc_id")]][$row[csf("invoice_id")]]["invoice_no"]=$row[csf("invoice_no")];
						$invoice_data_arr[$row[csf("btb_lc_id")]][$row[csf("invoice_id")]]["invoice_date"]=$row[csf("invoice_date")];
						$invoice_data_arr[$row[csf("btb_lc_id")]][$row[csf("invoice_id")]]["bill_no"]=$row[csf("bill_no")];
						$invoice_data_arr[$row[csf("btb_lc_id")]][$row[csf("invoice_id")]]["bill_date"]=$row[csf("bill_date")];
						$invoice_data_arr[$row[csf("btb_lc_id")]][$row[csf("invoice_id")]]["doc_to_cnf"]=$row[csf("doc_to_cnf")];
						$invoice_data_arr[$row[csf("btb_lc_id")]][$row[csf("invoice_id")]]["feeder_vessel"]=$row[csf("feeder_vessel")];
						$invoice_data_arr[$row[csf("btb_lc_id")]][$row[csf("invoice_id")]]["mother_vessel"]=$row[csf("mother_vessel")];
						$invoice_data_arr[$row[csf("btb_lc_id")]][$row[csf("invoice_id")]]["eta_date"]=$row[csf("eta_date")];
						$invoice_data_arr[$row[csf("btb_lc_id")]][$row[csf("invoice_id")]]["port_of_loading"]=$row[csf("port_of_loading")];
						$invoice_data_arr[$row[csf("btb_lc_id")]][$row[csf("invoice_id")]]["port_of_discharge"]=$row[csf("port_of_discharge")];
						$invoice_data_arr[$row[csf("btb_lc_id")]][$row[csf("invoice_id")]]["copy_doc_receive_date"]=$row[csf("copy_doc_receive_date")];
						$invoice_data_arr[$row[csf("btb_lc_id")]][$row[csf("invoice_id")]]["bill_of_entry_no"]=$row[csf("bill_of_entry_no")];
						$invoice_data_arr[$row[csf("btb_lc_id")]][$row[csf("invoice_id")]]["pkg_quantity"]=$row[csf("pkg_quantity")];
						$invoice_data_arr[$row[csf("btb_lc_id")]][$row[csf("invoice_id")]]["container_no"]=$row[csf("container_no")];
						$invoice_data_arr[$row[csf("btb_lc_id")]][$row[csf("invoice_id")]]["maturity_date"]=$row[csf("maturity_date")];
						$invoice_data_arr[$row[csf("btb_lc_id")]][$row[csf("invoice_id")]]["inco_term"]=$row[csf("inco_term")];
						$invoice_data_arr[$row[csf("btb_lc_id")]][$row[csf("invoice_id")]]["inco_term_place"]=$row[csf("inco_term_place")];
						$invoice_data_arr[$row[csf("btb_lc_id")]][$row[csf("invoice_id")]]["company_acc_date"]=$row[csf("company_acc_date")];
						$invoice_data_arr[$row[csf("btb_lc_id")]][$row[csf("invoice_id")]]["bank_acc_date"]=$row[csf("bank_acc_date")];
						$bill_of_entry_no_check[$row[csf("btb_lc_id")]].=$row[csf("bill_of_entry_no")].",";
					}
					
					//var_dump($invoice_data_arr);die;
					
					$sql_pi=sql_select("select p.com_btb_lc_master_details_id as btb_lc_id , a.id, a.item_category_id, a.supplier_id, a.pi_number, a.pi_date, a.last_shipment_date, a.currency_id, a.intendor_name, b.count_name, b.yarn_type, b.uom, b.quantity, b.rate, b.amount, b.net_pi_rate, b.net_pi_amount 
					from com_btb_lc_pi p, com_pi_master_details a, com_pi_item_details b 
					where p.pi_id=a.id and a.id=b.pi_id and b.is_deleted=0 and b.status_active=1  $pi_date_cond");
					foreach($sql_pi as $row)
					{
						$pi_data_arr[$row[csf("btb_lc_id")]][$row[csf("id")]]["id"]=$row[csf("id")];
						$pi_data_arr[$row[csf("btb_lc_id")]][$row[csf("id")]]["item_category_id"]=$row[csf("item_category_id")];
						$pi_data_arr[$row[csf("btb_lc_id")]][$row[csf("id")]]["supplier_id"]=$row[csf("supplier_id")];
						$pi_data_arr[$row[csf("btb_lc_id")]][$row[csf("id")]]["pi_number"]=$row[csf("pi_number")];
						$pi_data_arr[$row[csf("btb_lc_id")]][$row[csf("id")]]["pi_date"]=$row[csf("pi_date")];
						$pi_data_arr[$row[csf("btb_lc_id")]][$row[csf("id")]]["last_shipment_date"]=$row[csf("last_shipment_date")];
						$pi_data_arr[$row[csf("btb_lc_id")]][$row[csf("id")]]["currency_id"]=$row[csf("currency_id")];
						$pi_data_arr[$row[csf("btb_lc_id")]][$row[csf("id")]]["intendor_name"]=$row[csf("intendor_name")];
						$pi_data_arr[$row[csf("btb_lc_id")]][$row[csf("id")]]["count_name"]=$row[csf("count_name")];
						$pi_data_arr[$row[csf("btb_lc_id")]][$row[csf("id")]]["yarn_type"]=$row[csf("yarn_type")];
						$pi_data_arr[$row[csf("btb_lc_id")]][$row[csf("id")]]["uom"]=$row[csf("uom")];
						$pi_data_arr[$row[csf("btb_lc_id")]][$row[csf("id")]]["quantity"]=$row[csf("quantity")];
						$pi_data_arr[$row[csf("btb_lc_id")]][$row[csf("id")]]["rate"]=$row[csf("rate")];
						$pi_data_arr[$row[csf("btb_lc_id")]][$row[csf("id")]]["amount"]=$row[csf("amount")];
						$pi_data_arr[$row[csf("btb_lc_id")]][$row[csf("id")]]["net_pi_rate"]=$row[csf("net_pi_rate")];
						$pi_data_arr[$row[csf("btb_lc_id")]][$row[csf("id")]]["net_pi_amount"]=$row[csf("net_pi_amount")];
						
					}
					
					$btb_date_cond="";
					if(str_replace("'","",$txt_date_from_btb)!="" && str_replace("'","",$txt_date_to_btb)!="") $btb_date_cond=" and a.lc_date between $txt_date_from_btb and $txt_date_to_btb";
						
					if($db_type==0)
					{	
						$sql="select a.id, a.lc_value, a.lc_date, a.lc_number, a.tenor, a.last_shipment_date, a.lc_expiry_date, a.lc_type_id, a.issuing_bank_id, a.etd_date, a.payterm_id, group_concat(distinct(b.pi_id)) as pi_id 
						from com_btb_lc_master_details a left join com_btb_lc_pi b on a.id=b.com_btb_lc_master_details_id and b.is_deleted=0 and b.status_active=1 
						where a.importer_id=$cbo_company_name and a.item_category_id=$cbo_item_category_id and a.lc_type_id like '$lc_type_id' and a.issuing_bank_id like '$issuing_bank' and a.is_deleted=0 and a.status_active=1 $btb_date_cond  group by a.id";
					}
					else if($db_type==2)
					{
						 $sql="select a.id, a.lc_value, a.lc_date, a.lc_number, a.tenor, a.last_shipment_date, a.lc_expiry_date, a.lc_type_id, a.issuing_bank_id, a.etd_date, a.payterm_id, LISTAGG(CAST(b.pi_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.pi_id) as pi_id 
						from com_btb_lc_master_details a left join com_btb_lc_pi b on a.id=b.com_btb_lc_master_details_id and b.is_deleted=0 and b.status_active=1 
						where a.importer_id=$cbo_company_name and a.item_category_id=$cbo_item_category_id and a.lc_type_id like '$lc_type_id' and a.issuing_bank_id like '$issuing_bank' and a.is_deleted=0 and a.status_active=1 $btb_date_cond group by a.id, a.lc_value, a.lc_date, a.lc_number, a.tenor, a.last_shipment_date, a.lc_expiry_date, a.lc_type_id, a.etd_date, a.payterm_id, a.issuing_bank_id";
					}
					//echo $sql; die;
					$result=sql_select($sql); $i=1; $btb_pi_id=array();$total_pi_qty=0;$total_pi_val=0;
					foreach($result as $row)
					{
						/*$invoice_sql = "select id as invoice_id, invoice_no, invoice_date, bill_no, bill_date, doc_to_cnf, feeder_vessel, mother_vessel, eta_date, port_of_loading, port_of_discharge, copy_doc_receive_date, bill_of_entry_no, pkg_quantity, container_no, maturity_date, inco_term, inco_term_place FROM com_import_invoice_mst where find_in_set(".$row[csf('id')].",btb_lc_id) and is_lc=1 and status_active=1 and is_deleted=0 $mat_str_cond";*/
							
						$mrr_qnty=0; $mrr_value=0;
							
						$pi_id=array_unique(explode(",",$row[csf('pi_id')]));
						foreach($pi_id as $val)
						{
							$mrr_qnty+=$receive_arr[$val]['qnty']; 
							$mrr_value+=$receive_arr[$val]['amnt'];
						}
						
						$bill_entry_no_arr=array_unique(explode(",",chop($bill_of_entry_no_check[$row[csf('id')]]," , ")));
						foreach($bill_entry_no_arr as $bill_entry_no)
						{
							if($bill_entry_no=="") $bill_entry_check=1;
							
						}
						$lc_date_calculate=strtotime($row[csf('lc_date')])+(60*60*24*60);
						$current_date_calculate=strtotime(date('Y-m-d'));
						$lc_bgcolor='';$invoice_bgcolor='';
						if($row[csf('payterm_id')]==3)
						{
							if($current_date_calculate>$lc_date_calculate)
							{
								if($bill_entry_check==1)
								{
									$lc_bgcolor='bgcolor="#FF0000"';
								}
								else
								{
									$lc_bgcolor='';
								}
							}
							else
							{
								$lc_bgcolor='';
							}
						}
						else if($row[csf('payterm_id')]<3)
						{
							$lc_bgcolor='bgcolor="#FF0000"';
							foreach($invoice_data_arr[$row[csf('id')]] as $inv_id=>$row_invoice)
							{
								$accep_date_calculate=strtotime($row_invoice['company_acc_date'])+(60*60*24*60);
								if($current_date_calculate>$accep_date_calculate)
								{
									if($row_invoice["bill_of_entry_no"]=="")
									{
										$lc_bgcolor='bgcolor="#FF0000"';break;
									}
									else
									{
										$lc_bgcolor='';
									}
								}
								else
								{
									$lc_bgcolor='';
								}
							}
						}
						else
						{
							$lc_bgcolor='';
						}
						

						if($row[csf('pi_id')]=="")
						{
							
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							
							$exel_short.="<tr bgcolor='".$bgcolor."'>
										<td align='center'><p>".change_date_format($row[csf('lc_date')])."&nbsp;</p></td>
										<td align='right'></td>
										<td align='center'></td>
										<td align='center'></td>
										<td align='right'></td>
										<td align='left'></td>
										<td align='left'></td>
										<td align='left'><p>".$row[csf('lc_number')]."&nbsp;</p></td>
										<td align='left'></td>";	
						?>
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td width="40" align="center" <? echo $lc_bgcolor; ?> ><? echo $i; ?></td>
							<td width="120" <? echo $lc_bgcolor; ?>>&nbsp;</td>
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
							<td width="80" align="center" <? echo $lc_bgcolor; ?>><? echo change_date_format($row[csf('lc_date')]); ?>&nbsp;</td>
							<td width="120" <? echo $lc_bgcolor; ?>><p><? echo $row[csf('lc_number')]; ?></p></td>
							<td width="120"><p><? echo $bank_arr[$row[csf('issuing_bank_id')]]; ?></p></td>
							<td width="110"><p><? echo $pay_term[$row[csf('payterm_id')]]; ?>&nbsp;</p></td>
							<td width="60">&nbsp;<? echo $row[csf('tenor')]; ?></td>
							<td width="100" align="right" <? echo $lc_bgcolor; ?>><? echo number_format($row[csf('lc_value')],2,'.',''); ?>&nbsp;</td>
							<td width="80" align="center"><? echo change_date_format($row[csf('last_shipment_date')]); ?>&nbsp;</td>
							<td width="80" align="center"><? echo change_date_format($row[csf('lc_expiry_date')]); ?>&nbsp;</td>
							<td width="80" align="center"><? echo change_date_format($row[csf('etd_date')]); ?>&nbsp;</td>
							<?
							//$result_invoice=sql_select($invoice_sql);
							$tot_invoice=count($invoice_data_arr[$row[csf('id')]]);
							$inv_date_calculate="";
							if($tot_invoice>0)
							{ 
							?>
								<td valign="middle" align="left" rowspan="<? echo $rowspan; ?>" colspan="23">
									<table width="100%"  border="0" class="rpt_table" rules="all">
									<?
									foreach($invoice_data_arr[$row[csf('id')]] as $inv_id=>$row_invoice)
									{
										if($row[csf('payterm_id')]<4)
										{
											if($row_invoice["bill_of_entry_no"]=="")
											{
												$inv_date_calculate=strtotime($row_invoice['invoice_date'])+(60*60*24*7);
												if($current_date_calculate>$inv_date_calculate)
												{
													$invoice_bgcolor='bgcolor="#FF0000"';
												}
												else
												{
													$invoice_bgcolor='';
												}
											}
											else
											{
												$invoice_bgcolor='';
											}
										}
										else
										{
											$invoice_bgcolor='';
										}
									?>
										<tr>
											<td width="120" style="border-left:hidden;"><p><? echo $row_invoice[('port_of_loading')]; ?>&nbsp;</p></td>
											<td width="120"><p><? echo $row_invoice[('port_of_discharge')]; ?>&nbsp;</p></td>
											<td width="80" align="center"><? if($row_invoice[('eta_date')]!="" && $row_invoice[('eta_date')]!="0000-00-00") echo change_date_format($row_invoice[('eta_date')]); ?>&nbsp;</td>          
											<td width="120"><p><? echo $row_invoice[('invoice_no')]; ?>&nbsp;</p></td>
											<td width="80" align="center"><? if($row_invoice[('invoice_date')]!="" && $row_invoice[('invoice_date')]!="0000-00-00") echo change_date_format($row_invoice[('invoice_date')]); ?>&nbsp;</td>
											<td width="80"><p><? echo $incoterm[$row_invoice[('inco_term')]]; ?>&nbsp;</p></td>
											<td width="100"><p><? echo $row_invoice[('inco_term_place')]; ?>&nbsp;</p></td>
											<td width="120"><p><? echo $row_invoice[('bill_no')]; ?>&nbsp;</p></td>
											<td width="80" align="center"><? echo change_date_format($row_invoice[('bill_date')]); ?>&nbsp;</td>
											<td width="120"><p><? echo $row_invoice[('mother_vessel')]; ?>&nbsp;</p></td> 
											<td width="120"><p><? echo $row_invoice[('feeder_vessel')]; ?>&nbsp;</p></td> 
											<td width="80"><p><? echo $row_invoice[('container_no')]; ?>&nbsp;</p></td>
											<td width="80" align="right"><p><? echo $row_invoice[('pkg_quantity')]; ?>&nbsp;</p></td>            
											<td width="100" align="center"><p><? if($row_invoice[('doc_to_cnf')]!="" && $row_invoice[('doc_to_cnf')]!="0000-00-00")  echo change_date_format($row_invoice[('doc_to_cnf')]); ?>&nbsp;</p></td>
											<td width="100" align="center"><p><? if($row_invoice[('copy_doc_receive_date')]!="" && $row_invoice[('copy_doc_receive_date')]!="0000-00-00")  echo change_date_format($row_invoice[('copy_doc_receive_date')]); ?>&nbsp;</p></td>
											<td width="120" <? echo $invoice_bgcolor; ?>><p><? echo $row_invoice[('bill_of_entry_no')]; ?>&nbsp;</p></td>
											<td width="80" align="center"><p><? if($row_invoice[('maturity_date')]!="" && $row_invoice[('maturity_date')]!="0000-00-00") echo change_date_format($row_invoice[('maturity_date')]); ?>&nbsp;</p></td>
											<td width="80" align="center"><p><? echo date('F',strtotime($row_invoice[('maturity_date')])); ?></p></td>
											<td width="80" align="center"><p><? if($payment_arr[$row_invoice[('invoice_id')]]['date']!="" && $payment_arr[$row_invoice[('invoice_id')]]['date']!="0000-00-00") echo change_date_format($payment_arr[$row_invoice[('invoice_id')]]['date']);?>&nbsp;</p></td>
											<td width="90" align="right"><p><? echo number_format($payment_arr[$row_invoice[('invoice_id')]]['amnt'],2,'.',''); ?>;</p></td>
											<td width="80" align="right"><p><? $total_mrr_qnty+=$mrr_qnty; echo number_format($mrr_qnty,2,'.',''); ?></p></td>
											<td width="90" align="right"><p><? $total_mrr_val+=$mrr_value;$total_short_val+=$mrr_value-$row[('lc_value')];
											echo number_format($mrr_value,2,'.',''); ?></p></td>
											<td align="right"><p><? echo number_format($mrr_value-$row[('lc_value')],2,'.',''); ?></p></td>
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
						/*$sql_pi="select a.id, a.item_category_id, a.supplier_id, a.pi_number, a.pi_date, a.last_shipment_date, a.currency_id, a.intendor_name, b.count_name, b.yarn_type, b.uom, b.quantity, b.rate, b.amount, b.net_pi_rate, b.net_pi_amount from com_pi_master_details a, com_pi_item_details b where a.id=b.pi_id and b.is_deleted=0 and b.status_active=1 and a.id in(".$row[csf('pi_id')].") $pi_date_cond";
						$result_pi=sql_select($sql_pi);*/
						$rowspan=count($pi_data_arr[$row[csf('id')]]);
						if($rowspan>0)
						{ 
							foreach($pi_data_arr[$row[csf('id')]] as $pi_id=>$row_pi)
							{
								
								if(!in_array($row_pi[('id')],$btb_pi_id))
								{
									$btb_pi_id[]=$row_pi[('id')];
								}
								
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								$total_pi_qty_short+=$row_pi[('quantity')];
								$total_pi_val_short+=$row_pi[('amount')];
								$exel_short.="<tr bgcolor='".$bgcolor."'>
											<td align='center'><p>".change_date_format($row[csf('lc_date')])."&nbsp;</p></td>
											<td align='right'>".number_format($row_pi[('quantity')],2,'.','')."</td>
											<td align='center'>".$unit_of_measurement[$row_pi[('uom')]]."</td>
											<td align='right'>".number_format($row_pi[('rate')],2,'.','')."</td>
											<td align='right'>".number_format($row_pi[('amount')],2,'.','')."</td>
											<td align='left'>".$count_arr[$row_pi[('count_name')]]."</td>
											<td align='left'>".$yarn_type[$row_pi[('yarn_type')]]."</td>
											<td align='left'><p>".$row[csf('lc_number')]."&nbsp;</p></td>
											<td align='left'>".$supplier_arr[$row_pi[('supplier_id')]]."</td>";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="40" align="center" <? echo $lc_bgcolor; ?>><? echo $i; ?></td>
									<td width="120" <? echo $lc_bgcolor; ?>><p><? echo $row_pi[('pi_number')]; ?></p></td>
									<td width="80" align="center"><? if($row_pi[('pi_date')]!="" && $row_pi[('pi_date')]!="0000-00-00")   echo change_date_format($row_pi[('pi_date')]); ?></td>
									<td width="80" align="center"><? if($row_pi[('last_shipment_date')]!="" && $row_pi[('last_shipment_date')]) echo change_date_format($row_pi[('last_shipment_date')]); ?></td>
									<td width="140"><p><? echo $supplier_arr[$row_pi[('supplier_id')]]; ?></p></td>
									<td width="100"><p><? echo $item_category[$row_pi[('item_category_id')]]; ?></p></td>
									<? 
									if($item_cate == 1) 
									{ 
									?>            
										<td width="80"><p><? echo $count_arr[$row_pi[('count_name')]]; ?>&nbsp;</p></td>
										<td width="100"><p><? echo $yarn_type[$row_pi[('yarn_type')]]; ?>&nbsp;</p></td>
									<?
									}
									?>
									<td width="60" align="center"><p><? echo $unit_of_measurement[$row_pi[('uom')]]; ?></p></td>
									<td width="80" align="right"><p><? $total_pi_qty+=$row_pi[('quantity')];echo number_format($row_pi[('quantity')],2,'.',''); ?></p></td> 
									<td width="80" align="right"><p><? echo number_format($row_pi[('rate')],2,'.',''); ?></p></td>
									<td width="100" align="right"><p><? $total_pi_val+=$row_pi[('amount')];echo number_format($row_pi[('amount')],2,'.',''); ?></p></td>
									<td width="80"><P><? echo $currency[$row_pi[('currency_id')]]; ?>&nbsp;</P></td>
									<td width="120"><P><? echo $supplier_arr[$row_pi[('intendor_name')]]; ?>&nbsp;</P></td>
									<?
									if($s==0)
									{
									?>
										<td width="80" align="center" rowspan="<? echo $rowspan; ?>" <? echo $lc_bgcolor; ?>><p><? if($row[csf('lc_date')]!="" && $row[csf('lc_date')]!="0000-00-00") echo change_date_format($row[csf('lc_date')]); ?>&nbsp;</p></td>
										<td width="120" rowspan="<? echo $rowspan; ?>" <? echo $lc_bgcolor; ?>><p><? echo $row[csf('lc_number')]; ?>&nbsp;</p></td>
										<td width="120" rowspan="<? echo $rowspan; ?>"><p><? echo $bank_arr[$row[csf('issuing_bank_id')]]; ?>&nbsp;</p></td>
										<td width="110" rowspan="<? echo $rowspan; ?>"><p><? echo $pay_term[$row[csf('payterm_id')]]; ?>&nbsp;</p></td>
										<td width="60" rowspan="<? echo $rowspan; ?>"><p><? echo $row[csf('tenor')]; ?>&nbsp;</p></td>
										<td width="100" align="right" rowspan="<? echo $rowspan; ?>" <? echo $lc_bgcolor; ?>><p><? echo number_format($row[csf('lc_value')],2,'.',''); ?></p></td>
										<td width="80" align="center" rowspan="<? echo $rowspan; ?>"><p><? if($row[csf('last_shipment_date')]!="" && $row[csf('last_shipment_date')]!="0000-00-00") echo change_date_format($row[csf('last_shipment_date')]); ?>&nbsp;</p></td>
										<td width="80" align="center" rowspan="<? echo $rowspan; ?>"><p><? if($row[csf('lc_expiry_date')]!="" && $row[csf('lc_expiry_date')]!="0000-00-00") echo change_date_format($row[csf('lc_expiry_date')]); ?>&nbsp;</p></td>
										<td width="80" align="center" rowspan="<? echo $rowspan; ?>"><p><? if($row[csf('etd_date')]!="" && $row[csf('etd_date')]!="0000-00-00") echo change_date_format($row[csf('etd_date')]); ?>&nbsp;</p></td>  
										<?
										//$result_invoice=sql_select($invoice_sql);
										//$tot_invoice=count($result_invoice);
										//$result_invoice=sql_select($invoice_data_arr[$row[csf('id')]]);
										$tot_invoice=count($invoice_data_arr[$row[csf('id')]]);
										$inv_date_calculate="";
										if($tot_invoice>0)
										{
											
										?>
											<td valign="middle" align="left" rowspan="<? echo $rowspan; ?>" colspan="23">
												<table width="100%"  border="0" class="rpt_table" rules="all">
												<?
												$k=1;
												foreach($invoice_data_arr[$row[csf('id')]] as $invoice_in=>$row_invoice)
												{
													if($row[csf('payterm_id')]<4)
													{
														if($row_invoice["bill_of_entry_no"]=="")
														{
															$inv_date_calculate=strtotime($row_invoice['invoice_date'])+(60*60*24*7);
															if($current_date_calculate>$inv_date_calculate)
															{
																$invoice_bgcolor='bgcolor="#FF0000"';
															}
															else
															{
																$invoice_bgcolor='';
															}
														}
														else
														{
															$invoice_bgcolor='';
														}
													}
													else
													{
														$invoice_bgcolor='';
													}
												?>
													<tr>
														<td width="120" style="border-left:hidden;"><p><? echo $row_invoice[('port_of_loading')]; ?>&nbsp;</p></td>
														<td width="120"><p><? echo $row_invoice[('port_of_discharge')]; ?>&nbsp;</p></td>
														<td width="80" align="center"><? echo change_date_format($row_invoice[('eta_date')]); ?>&nbsp;</td>          
														<td width="120"><p><? echo $row_invoice[('invoice_no')]; ?>&nbsp;</p></td>
														<td width="80" align="center"><p><? if($row_invoice[('invoice_date')]!="" && $row_invoice[('invoice_date')]!="0000-00-00")  echo change_date_format($row_invoice[('invoice_date')]); ?>&nbsp;</p></td>
														<td width="80"><p><? echo $incoterm[$row_invoice[('inco_term')]]; ?>&nbsp;</p></td>
														<td width="100"><p><? echo $row_invoice[('inco_term_place')]; ?>&nbsp;</p></td>
														<td width="120"><p><? echo $row_invoice[('bill_no')]; ?>&nbsp;</p></td>
														<td width="80" align="center"><? if($row_invoice[('bill_date')]!="" && $row_invoice[('bill_date')]!="0000-00-00") echo change_date_format($row_invoice[('bill_date')]); ?>&nbsp;</td>
														<td width="120"><p><? echo $row_invoice[('mother_vessel')]; ?>&nbsp;</p></td> 
														<td width="120"><p><? echo $row_invoice[('feeder_vessel')]; ?>&nbsp;</p></td> 
														<td width="80"><p><? echo $row_invoice[('container_no')]; ?>&nbsp;</p></td>
														<td width="80" align="right"><? echo $row_invoice[('pkg_quantity')]; ?>&nbsp;</td>            
														<td width="100" align="center"><? if($row_invoice[('doc_to_cnf')]!="" && $row_invoice[('doc_to_cnf')]!="0000-00-00") echo change_date_format($row_invoice[('doc_to_cnf')]); ?>&nbsp;</td>
														<td width="100" align="center"><? if($row_invoice[('copy_doc_receive_date')]!="" && $row_invoice[('copy_doc_receive_date')]!="0000-00-00") echo change_date_format($row_invoice[('copy_doc_receive_date')]); ?>&nbsp;</td>
														<td width="120" <? echo $invoice_bgcolor; ?>><p><? echo $row_invoice[('bill_of_entry_no')]; ?>&nbsp;</p></td>
														<td width="80" align="center"><? if($row_invoice[('maturity_date')]!="" && $row_invoice[('maturity_date')]!="0000-00-00") echo change_date_format($row_invoice[('maturity_date')]); ?>&nbsp;</td>
														<td width="80" align="center"><? echo date('F',strtotime($row_invoice[('maturity_date')])); ?></td>
														<td width="80" align="center">
															<? echo change_date_format($payment_arr[$row_invoice[('invoice_id')]]['date']);?>&nbsp;
														</td>
														<td width="90" align="right">
															<? echo number_format($payment_arr[$row_invoice[('invoice_id')]]['amnt'],2,'.',''); ?>&nbsp;
														</td>
														<?
														if($k==1)
														{
															$total_mrr_qnty+=$mrr_qnty;
															$total_mrr_val+=$mrr_value;
															$total_short_val+=$mrr_value-$row[csf('lc_value')];
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
											<td width="80" align="right"><a href="javascript:void(0)" onclick="show_qty_details('<? echo $row[csf('pi_id')]; ?>',<? echo $item_cate; ?>)"><? $total_mrr_qnty+=$mrr_qnty;echo number_format($mrr_qnty,2,'.',''); ?></a>&nbsp;</td>
											<td width="90" align="right"><? $total_mrr_val+=$mrr_value;$total_short_val+=$mrr_value-$row[csf('lc_value')]; echo number_format($mrr_value,2,'.',''); ?>&nbsp;</td>
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
					if(str_replace("'","",$txt_date_from_btb)=="" && str_replace("'","",$txt_date_to_btb)=="")
					{
						if(count($btb_pi_id)>0) $pi_id_cond=" and a.id not in(".implode(",",$btb_pi_id).")";
						$query="select a.id, a.item_category_id, a.supplier_id, a.pi_number, a.pi_date, a.last_shipment_date, a.currency_id, a.intendor_name, b.count_name, b.yarn_type, b.uom, b.quantity, b.rate, b.amount, b.net_pi_rate, b.net_pi_amount 
						from com_pi_master_details a, com_pi_item_details b 
						where a.id=b.pi_id and b.is_deleted=0 and b.status_active=1 and a.item_category_id=$cbo_item_category_id and a.importer_id=$cbo_company_name $pi_id_cond $pi_date_cond";
						$result=sql_select($query);
						foreach($result as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							$mrr_qnty=$receive_arr[$row[csf('id')]]['qnty']; 
							$mrr_value=$receive_arr[$row[csf('id')]]['amnt'];
							$total_pi_qty_short+=$row[csf('quantity')];	
							$total_pi_val_short+=$row[csf('amount')];
							$exel_short.="<tr bgcolor='".$bgcolor."'>
										<td align='center'></td>
										<td align='right'>".number_format($row[csf('quantity')],2,'.','')."</td>
										<td align='center'>".$unit_of_measurement[$row[csf('uom')]]."</td>
										<td align='right'>".number_format($row[csf('rate')],2,'.','')."</td>
										<td align='right'>".number_format($row[csf('amount')],2,'.','')."</td>
										<td align='left'>".$count_arr[$row[csf('count_name')]]."</td>
										<td align='left'>".$yarn_type[$row[csf('yarn_type')]]."</td>
										<td align='left'></td>
										<td align='left'>".$supplier_arr[$row[csf('supplier_id')]]."</td>";
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
                                <td width="80" align="right"><? $total_pi_qty+=$row[csf('quantity')];
								$total_pi_val+=$row[csf('amount')];echo number_format($row[csf('quantity')],2,'.',''); ?>&nbsp;</td> 
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
                                <td width="80" align="right"><a href="javascript:void(0)" onclick="show_qty_details(<? echo $row[csf('id')]; ?>,<? echo $item_cate; ?>)"><? $total_mrr_qnty+=$mrr_qnty; echo number_format($mrr_qnty,2,'.',''); ?></a>&nbsp;</td>
                                <td width="90" align="right"><? $total_mrr_val+=$mrr_value;
															$total_short_val+=$mrr_value;
															echo number_format($mrr_value,2,'.',''); ?>&nbsp;</td>
                                <td align="right"><? echo number_format($mrr_value,2,'.',''); ?>&nbsp;</td>
							</tr>
							<?	
							$i++;
						}
						$exel_short.="<tr>
								<td align='center'><b>Total</b></td>
								<td align='center'><b>".number_format($total_pi_qty_short,2)."</b></td>
								<td align='center'><b></b></td>
								<td align='center'><b></b></td>
								<td align='center'><b>".number_format($total_pi_val_short,2)."</b></td>
								<td align='center'><b></b></td>
								<td align='center'><b></b></td>
								<td align='center'><b></b></td>
								<td align='center'><b></b></td>
								";
				}
				
				//}
				?>
                        <tfoot>
                        
                        <tr>    
                            <th width="40">&nbsp;</th>
                            <th width="120">&nbsp;</th>
                            <th width="80">&nbsp;</th>
                            <th width="80">&nbsp;</th>
                            <th width="140">&nbsp;</th>
                            <th width="100">Total</th>
                            <? 
							if($item_cate == 1) 
                            { 
                            ?>            
                                <th width="80">&nbsp;</th>
                                <th width="100">&nbsp;</th>
                            <?
                            }
                            ?>
                            <th width="60">&nbsp;</th>
                            <th width="80"><? echo number_format($total_pi_qty,2);?></th> 
                            <th width="80"><? //echo $total_pi_val;?></th>
                            <th width="100"><? echo number_format($total_pi_val,2);?></th>
                            <th width="80">&nbsp;</th>
                            <th width="120">&nbsp;</th>
                            <th width="80">&nbsp; </th>
                            <th width="120">&nbsp;</th>
                            <th width="120">&nbsp;</th>
                            <th width="110">&nbsp;</th>
                            <th width="60">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="80">&nbsp;</th>
                            <th width="80">&nbsp;</th>
                            <th width="80">&nbsp;</th>  
                            <th width="120">&nbsp;</th>
                            <th width="120">&nbsp;</th>
                            <th width="80">&nbsp;</th>          
                            <th width="120">&nbsp;</th>
                            <th width="80">&nbsp;</th>
                            <th width="80">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="120">&nbsp;</th>
                            <th width="80">&nbsp;</th>
                            <th width="120">&nbsp;</th> 
                            <th width="120">&nbsp;</th> 
                            <th width="80">&nbsp;</th>
                            <th width="80">&nbsp;</th>            
                            <th width="100">&nbsp;</th>
                            <th width="100">&nbsp;</th>
                            <th width="120">&nbsp;</th>
                            <th width="80">&nbsp;</th>
                            <th width="80">&nbsp; </th>
                            <th width="80">&nbsp;</th> 
                            <th width="90"><? //echo $total_mrr_qnty;?></th>
                            <th width="80"><? echo number_format($total_mrr_qnty,2);?></th>
                            <th width="90"><? echo number_format($total_mrr_val,2);?></th>
                            <th><? echo number_format($total_short_val,2);?></th>
                        </tr>
                             
                             
                        </tr>
                        </tfoot>
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
	$filename_short=$user_id."_".$name."short.xls";
	$create_new_doc = fopen($filename, 'w');
	$create_new_doc_short = fopen($filename_short, 'w');	
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$is_created_short = fwrite($create_new_doc_short,$exel_short);
	$filename=$user_id."_".$name.".xls";
	$filename_short=$user_id."_".$name."short.xls";
	echo "$total_data****$filename****$filename_short";
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
