<? 
include('../../../includes/common.php');
session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');
$user_id=$_SESSION['logic_erp']['user_id'];
$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
$store_arr=return_library_array( "select id, store_name from lib_store_location", "id", "store_name");
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
$order_arr=return_library_array( "select id, order_no from  subcon_ord_dtls", "id", "order_no");
$color_arr=return_library_array( "select id, color_name from lib_color", "id", "color_name");
$batch_arr=return_library_array( "select id, batch_no from pro_batch_create_mst", "id", "batch_no");

if ($action=="load_drop_down_location")
{
	echo create_drop_down( "cbo_location_name", 130, "select id, location_name from lib_location where company_id='$data' and status_active =1 and is_deleted=0 order by location_name","id,location_name", 1, "-Select Location-", $selected, "","","","","","",3 );
	exit();	 
}

if ($action=="load_drop_down_buyer")
{ 
	$ex_data=explode('_',$data);
	if($ex_data[1]==2)
	{
		echo create_drop_down( "cbo_party_id", 130, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$ex_data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "--Select Party--", $selected, "" );
	}
	else if($ex_data[1]==1)
	{
		echo create_drop_down( "cbo_party_id", 130, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name", 1, "--Select Party--", $ex_data[0], "" );
	}
	else
	{
		 echo create_drop_down( "cbo_party_id", 130, $blank_array,"", 1, "--Select Party--", $selected, "",1,"" );
	}
	exit();   	 
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$date_from=str_replace("'","",$txt_date_from);
	$date_to=str_replace("'","",$txt_date_to);
	
	ob_start();

	if($type==1)
	{
		?>
        <div align="center">
         <fieldset style="width:950px;">
            <table cellpadding="0" cellspacing="0" width="930">
                <tr  class="form_caption" style="border:none;">
                   <td align="center" width="100%" colspan="10" style="font-size:20px"><strong><? echo 'Party Ledger'; ?></strong></td>
                </tr>
                <tr class="form_caption" style="border:none;">
                   <td align="center" width="100%" colspan="10" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
                </tr>
                <tr class="form_caption" style="border:none;">
                    <td align="center" width="100%" colspan="10" style="font-size:12px">
                        <? if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="") echo "From ".change_date_format(str_replace("'","",$txt_date_from),'dd-mm-yyyy')." To ".change_date_format(str_replace("'","",$txt_date_to),'dd-mm-yyyy')."" ;?>
                    </td>
                </tr>
            </table>
            <table width="1100" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
                <thead>
                    <th width="30">SL</th>
                    <th width="110">Trans. Ref.</th>
                    <th width="110">Invoice No.</th>
                    <th width="70">Trans. Date</th>
                    <th width="80">Bill Type</th>                            
                    <th width="100">Narration</th>
                    <th width="100">Bill Qty.</th>
                    <th width="100">Inv. Amount (Debit)</th>
                    <th width="100">Receive (Credit)</th>
                    <th width="100">Non-Cash /LC (Credit)</th>
                    <th width="100">Balance</th>
                    <th>Currency</th>
                </thead>
            </table>
        <div style="max-height:300px; overflow-y:scroll; width:1100px" id="scroll_body">
            <table width="1100" border="1" class="rpt_table" rules="all" id="table_body">
            <?
				$po_num_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number");
				$bill_num_arr=return_library_array( "select bill_no, prefix_no_num from subcon_inbound_bill_mst", "bill_no", "prefix_no_num");
                if(str_replace("'","",$cbo_party_id)==0) $party_cond=""; else  $party_cond=" and a.party_name=$cbo_party_id";
                if(str_replace("'","",$cbo_party_id)==0) $party_bill_cond=""; else  $party_bill_cond=" and a.party_id=$cbo_party_id";
				if(str_replace("'","",$cbo_party_source)==0) $party_source_cond=""; else $party_source_cond=" and a.party_source=$cbo_party_source";
				if(str_replace("'","",$cbo_location_name)==0) $location_cond=""; else $location_cond=" and a.location_id=$cbo_location_name";
    
                if(str_replace("'","",$cbo_bill_type)==0) $bill_type_cond=""; else $bill_type_cond=" and b.bill_type=$cbo_bill_type";
                if(str_replace("'","",$cbo_bill_type)==0) $process_cond=""; else  $process_cond=" and a.process_id=$cbo_bill_type";
				if(str_replace("'","",$cbo_party_source)==0) $source_cond=""; else $source_cond=" and a.party_source=$cbo_party_source";
                
                if($db_type==0)
                {
                    if( $date_from==0 && $date_to==0 ) $receipt_date_cond=""; else $receipt_date_cond=" and a.receipt_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
                    if( $date_from==0 && $date_to==0 ) $bill_date_cond=""; else $bill_date_cond=" and a.bill_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
                }
                else if($db_type==2)
                {
                    if( $date_from==0 && $date_to==0 ) $receipt_date_cond=""; else $receipt_date_cond= " and a.receipt_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
                    if( $date_from=='' && $date_to=='' ) $bill_date_cond=""; else $bill_date_cond= " and a.bill_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
                }
                
                if($date_from=="") $bill_date=""; else $bill_date= " and a.bill_date <".$txt_date_from."";
                if($date_from=="") $receipt_date=""; else $receipt_date= " and a.receipt_date <".$txt_date_from."";
                
                $opening_balance_arr=array();
                $ope_bal=sql_select("select a.party_id, sum(b.amount) as amount from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.company_id=$cbo_company_id and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $location_cond $party_source_cond $party_bill_cond $bill_date $process_cond group by a.party_id order by a.party_id");
                foreach ($ope_bal as $row)
                {
                    $opening_balance_arr[$row[csf("party_id")]]=$row[csf("amount")];
                }
                
                $pre_add_array=array();
                $sql_adj=sql_select("select a.party_name, sum(b.total_adjusted) as pre_adjusted from subcon_payment_receive_mst a,subcon_payment_receive_dtls b where a.id=b.master_id and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 $party_cond $receipt_date $bill_type_cond group by a.party_name order by a.party_name");
              // echo "select a.party_name, sum(b.total_adjusted) as pre_adjusted from subcon_payment_receive_mst a,subcon_payment_receive_dtls b where a.id=b.master_id and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 $party_cond $receipt_date $bill_type_cond group by a.party_name order by a.party_name";
    			foreach ($sql_adj as $row)
                {
                    $pre_add_array[$row[csf("party_name")]]=$row[csf("pre_adjusted")];
                }
    			// $sql_adj[0][csf('pre_adjusted')];
				$pay_recv_array=array();	
				$sql_dtls="Select a.receive_no, a.party_name, a.receipt_date, a.instrument_id, a.bank_name, a.instrument_date, a.instrument_no, a.adjustment_type, b.bill_type, b.bill_no,  
                sum(b.total_adjusted) as rec_amount, a.remarks
                from subcon_payment_receive_mst a, subcon_payment_receive_dtls b where a.id=b.master_id and a.company_id=$cbo_company_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $party_cond $receipt_date_cond $bill_type_cond  group by a.party_name, b.bill_no, a.receive_no, a.receipt_date, a.instrument_id, a.bank_name, a.instrument_date, a.instrument_no, a.adjustment_type, b.bill_type, a.remarks order by a.party_name, b.bill_no";
                //echo $sql_dtls; die;
				$sql_dtls_result=sql_select($sql_dtls);
				foreach ($sql_dtls_result as $row)
				{
					$instrument_id=$row[csf('instrument_id')];
					 if ($instrument_id==1 || $instrument_id==2 || $instrument_id==3)
					 {
						 if ($row[csf('instrument_date')]=='' || $row[csf('instrument_date')]=="0000-00-00") $instrument_date=''; else $instrument_date=change_date_format($row[csf('instrument_date')]);
                          //$narration=$row[csf('instrument_no')].'<br>'.$instrument_date.'<br>'.$row[csf('bank_name')]; 
						 
						 $rec_amount=$row[csf('rec_amount')];
						 $lc_noncash='-';
					 }
					 else  if ($instrument_id==4 || $instrument_id==5)
					 {
						//$narration=$adjustment_type[$row[csf('adjustment_type')]];
						$rec_amount='-';
						$lc_noncash=$row[csf('rec_amount')];
					 }
                    $narration=$row[csf('remarks')];
					$pay_recv_array[$row[csf("party_name")]].=$row[csf('bill_no')]."_".$row[csf('receive_no')]."_".$row[csf('receipt_date')]."_".$row[csf('bill_type')]."_".$narration."_".$rec_amount."_".$lc_noncash.",";
					
				}
				//echo $trans_data;
             // var_dump($pay_recv_array);die;
			 
			   if($db_type==0) $order_id_cond="group_concat(b.order_id)";  else if($db_type==2) $order_id_cond="listagg(CAST(b.order_id as varchar2(4000)),',') within group (order by b.order_id)";
			   
				$sql_bill="select a.bill_no, a.prefix_no_num, a.bill_date, a.party_id, a.party_source, a.bill_for, a.process_id, $order_id_cond as order_id, sum(b.amount) as amount, sum(b.delivery_qty) as delivery_qty,b.currency_id from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.company_id=$cbo_company_id and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $location_cond $party_source_cond $party_bill_cond $bill_date_cond $process_cond $source_cond group by a.party_id, a.party_source, a.bill_no, a.prefix_no_num, a.bill_date, a.bill_for, a.process_id,b.currency_id order by a.party_id,a.bill_no";
               //echo $sql_bill;
                $sql_bill_result=sql_select($sql_bill);
                $i=1; $k=1; $party_array=array(); $pay_rec_val_array=array(); 
                foreach ($sql_bill_result as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    $opening_bal=$opening_balance_arr[$row[csf("party_id")]]-$pre_add_array[$row[csf("party_id")]];
					$party_arr="";
					//echo $row[csf("party_source")].'==';
					if($row[csf("party_source")]==2) $party_name=$buyer_arr;
					else if($row[csf("party_source")]==1) $party_name=$company_arr;
					
                    if (!in_array($row[csf("party_id")],$party_array) )
                    {
                        if($k!=1)
                        { 	
							$dataArray=array_filter(explode(",",substr($pay_recv_array[$party],0,-1)));

							foreach($dataArray as $key=>$val2)
							{ 
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								
								$value=explode("_",$val2);
								$bill_no=$value[0];
								$recv_no=$value[1];
								$receipt_date=$value[2];
								$bill_type=$value[3];
								$narration=$value[4];
								$rec_amount=$value[5];
								$lc_noncash=$value[6];
								?>
                                <tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                     <td width="30" ><? echo $i; ?></td>
                                     <td width="110" ><? echo $recv_no; ?></td>
                                     <td width="110" align="center" ><? echo $bill_num_arr[$bill_no]; ?></td>
                                     <td width="70" ><? echo '&nbsp;'.change_date_format($receipt_date); ?></td>
                                     <td width="80" ><? echo $production_process[$bill_type]; ?></td>
                                     <td width="100" align="center" ><p><? echo $narration; ?></p></td>
                                     <td width="100" align="right" ><? //echo ?></td>
                                     <td width="100" align="right" ><? //echo ?></td>
                                     <td width="100" align="right" ><? echo number_format($rec_amount,2,'.',''); ?></td>
                                     <td width="100" align="right" ><? echo number_format($lc_noncash,2,'.',''); ?></td>
                                     <td width="100" align="right"><? $bal_row=$bal_row-($rec_amount+$lc_noncash); echo number_format($bal_row,2,'.',''); ?></td>
                                     <td><? 
                                     $currency = array(1 => "Taka", 2 => "USD", 3 => "EURO", 4 => "CHF", 5 => "SGD", 6 => "Pound", 7 => "YEN");
                                     echo $currency[$row[csf("currency_id")]]; ?>
                                     </td>
                                </tr>
                                <?
								$tot_rec_amount+=$rec_amount;
								$tot_lc_noncash+=$lc_noncash;
								
								$grand_rec_amount+=$rec_amount;
								$grand_lc_noncash+=$lc_noncash;
								$grand_tot_balance=$bal_row;

                                $i++;
							}
                        ?>
                            <tr class="tbl_bottom">
                                <td colspan="6" align="right"><b>Party Total:</b></td>
                                <td align="right"><b><? echo number_format($bill_qty,2); ?></b></td>
                                <td align="right"><b><? echo number_format($inv_amount,2); ?></b></td>
                                <td align="right"><b><? echo number_format($tot_rec_amount,2); ?></b></td>
                                <td align="right"><b><? echo number_format($tot_lc_noncash,2); ?></b></td>
                                <td align="right"><b><? echo number_format($bal_row,2); $grand_balance1=$bal_row; ?></b></td>
                                <td colspan="1"></td>
                            </tr>
                        <?
							unset($bill_qty);
                            unset($inv_amount);
							unset($tot_rec_amount);
							unset($tot_lc_noncash);
                        }
                        ?>
                            <tr bgcolor="#dddddd">
                                <td colspan="12" align="left" ><b>Party Name: <? echo $party_name[$row[csf("party_id")]]; ?></b></td>
                            </tr>
                            <tr bgcolor="#999999">
                                <td colspan="5" align="left" ><b>Opening Balance:</b></td><td colspan="6" align="right" ><b> <? echo number_format($opening_bal,2); ?></b></td><td colspan="1"></td>
                            </tr>
                        <?
                        //unset($opening_balance_arr[$row[csf("party_id")]]);
                        //$balance_tot=$opening_balance_arr[$row[csf("party_id")]];
                        $bal_row=$opening_bal;


                        $party_array[]=$row[csf('party_id')];            
                        $k++;
                    }

                    $bal_row=$bal_row+$row[csf('amount')];
                    $inv_amount+=$row[csf('amount')];
                    $grand_inv_amount+=$row[csf('amount')];
                    $party=$row[csf('party_id')];
					$order_no='';
					$order_id=array_unique(explode(',',$row[csf('order_id')]));
					foreach ($order_id as $val)
					{
						if($row[csf("party_source")]==2)
							if($order_no=="") $order_no= $order_arr[$val]; else $order_no.=', '.$order_arr[$val];
						else if($row[csf("party_source")]==1)
							if($order_no=="") $order_no= $po_num_arr[$val]; else $order_no.=', '.$po_num_arr[$val];
					}
                    
                    ?>
                    <tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                         <td width="30" ><? echo $i; ?></td>
                         <td width="110" ><? echo $row[csf('bill_no')]; ?></td>
                         <td width="110" align="center" ><? echo $row[csf('prefix_no_num')]; ?></td>
                         <td width="70" ><? echo '&nbsp;'.change_date_format($row[csf('bill_date')]); ?></td>
                         <td width="80" ><? echo $production_process[$row[csf('process_id')]]; ?></td>
                         <td width="100" align="center" ><p><? echo $order_no; ?></p></td>
                         <td width="100" align="right" ><? echo number_format($row[csf('delivery_qty')],2,'.',''); ?></td>
                         <td width="100" align="right" ><? echo number_format($row[csf('amount')],2,'.',''); ?></td>
                         <td width="100" align="center" ><? echo '-'; ?></td>
                         <td width="100" align="center" ><? echo '-'; ?></td>
                         <td width="100" align="right"><? echo number_format($bal_row,2,'.',''); ?></td>
                         <td><? 
                            $currency = array(1 => "Taka", 2 => "USD", 3 => "EURO", 4 => "CHF", 5 => "SGD", 6 => "Pound", 7 => "YEN");
                            echo $currency[$row[csf("currency_id")]]; ?>
                         </td>
                         
                    </tr>
                    <?	
                    $i++;
					$bill_qty+=$row[csf('delivery_qty')];
					$grand_bill_qty+=$row[csf('delivery_qty')];
					$grand_tot_balance=$bal_row;
					$grand_balance2=$bal_row;
                }
                $bill_num_arr=return_library_array( "select bill_no, prefix_no_num from subcon_inbound_bill_mst", "bill_no", "prefix_no_num");
				
				$grand_balance+=(($opening_bal+$grand_inv_amount)-$grand_rec_amount)-$grand_lc_noncash;
				
				$dataArray=array_filter(explode(",",substr($pay_recv_array[$row[csf('party_id')]],0,-1)));
							
				foreach($dataArray as $key=>$val2)
				{
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					//echo $val2.'<br>'; die;
					$value=explode("_",$val2);
					$bill_no=$value[0];
					$recv_no=$value[1];
					$receipt_date=$value[2];
					$bill_type=$value[3];
					$narration=$value[4];
					$rec_amount=$value[5];
					$lc_noncash=$value[6];
					?>
					<tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						 <td width="30" ><? echo $i; ?></td>
						 <td width="110" ><? echo $recv_no; ?></td>
						 <td width="110" align="center" ><? echo $bill_num_arr[$bill_no]; ?></td>
						 <td width="70" ><? echo '&nbsp;'.change_date_format($receipt_date); ?></td>
						 <td width="80" ><? echo $production_process[$bill_type]; ?></td>
						 <td width="100" align="center" ><p><? echo $narration; ?></p></td>
						 <td width="100" align="right" >&nbsp;</td>
                         <td width="100" align="right" >&nbsp;</td>
						 <td width="100" align="right" ><? echo number_format($rec_amount,2,'.',''); ?></td>
						 <td width="100" align="right" ><? echo number_format($lc_noncash,2,'.',''); ?></td>
						 <td width="100" align="right"><? $bal_row=$bal_row-($rec_amount+$lc_noncash); echo number_format($bal_row,2,'.',''); ?></td>
                         <td><? 
                            $currency = array(1 => "Taka", 2 => "USD", 3 => "EURO", 4 => "CHF", 5 => "SGD", 6 => "Pound", 7 => "YEN");
                            echo $currency[$row[csf("currency_id")]]; ?>
                         </td>
					</tr>
					<?
					$tot_rec_amount+=$rec_amount;
					$tot_lc_noncash+=$lc_noncash;
					$grand_rec_amount+=$rec_amount;
					$grand_lc_noncash+=$lc_noncash;
					$i++;
					$grand_tot_balance=$bal_row;
					$grand_balance3=$bal_row;
				}
				//$grand_balance+=$grand_tot_balance;
				$grand_balance=$grand_balance1+$grand_balance3+$bal_row;
                ?>
                <tr class="tbl_bottom">
                    <td colspan="6" align="right"><b>Party Total:</b></td>
                    <td align="right"><b><? echo number_format($bill_qty,2); ?></b></td>
                    <td align="right"><b><? echo number_format($inv_amount,2); ?></b></td>
                    <td align="right"><b><? echo number_format($tot_rec_amount,2); ?></b></td>
                    <td align="right"><b><? echo number_format($tot_lc_noncash,2); ?></b></td>
                    <td align="right"><b><? echo number_format($bal_row,2); ?></b></td>
                    <td colspan="1"></td>
                </tr>
                <?
				if(str_replace("'","",$cbo_party_id)==0)
				{
				?>
                <tr class="tbl_bottom">
                    <td colspan="6" align="right"><b>Grand Total:</b></td>
                    <td align="right"><b><? echo number_format($grand_bill_qty,2); ?></b></td>
                    <td align="right"><b><? echo number_format($grand_inv_amount,2); ?></b></td>
                    <td align="right"><b><? echo number_format($grand_rec_amount,2); ?></b></td>
                    <td align="right"><b><? echo number_format($grand_lc_noncash,2); ?></b></td>
                    <td align="right"><b><? echo number_format($grand_balance,2); ?></b></td>
                    <td colspan="1"></td>
                </tr>
                <? } ?>
            </table>
            </div>
            </fieldset>
            </div>
        <?
	}
	else if($type==2)
	{
	?>
        <div align="center">
         <fieldset style="width:950px;">
            <table cellpadding="0" cellspacing="0" width="930">
                <tr  class="form_caption" style="border:none;">
                   <td align="center" width="100%" colspan="8" style="font-size:20px"><strong><? echo 'Receivable Statement'; ?></strong></td>
                </tr>
                <tr class="form_caption" style="border:none;">
                   <td align="center" width="100%" colspan="8" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
                </tr>
                <tr class="form_caption" style="border:none;">
                    <td align="center" width="100%" colspan="8" style="font-size:12px">
                        <? if(str_replace("'","",$txt_date_to)!="") echo " As On ".change_date_format(str_replace("'","",$txt_date_to),'dd-mm-yyyy')."" ;?>
                    </td>
                </tr>
            </table>
            <table width="927" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
                <thead>
                    <th width="40">SL</th>
                    <th width="160">Party</th>
                    <th width="150">Bill Type</th>
                    <th width="120">Inv. Amount (Debit)</th>
                    <th width="120">Receive (Credit)</th>
                    <th width="120">Non-Cash /LC (Credit)</th>
                    <th>Balance</th>
                </thead>
            </table>
        <div style="max-height:300px; overflow-y:scroll; width:930px" id="scroll_body">
            <table width="910" border="1" class="rpt_table" rules="all" id="table_body">
            <?	
				if(str_replace("'","",$cbo_party_id)==0) $party_cond=""; else  $party_cond=" and a.party_name=$cbo_party_id";
                if(str_replace("'","",$cbo_party_id)==0) $party_bill_cond=""; else  $party_bill_cond=" and a.party_id=$cbo_party_id";
				if(str_replace("'","",$cbo_location_name)==0) $location_cond=""; else $location_cond=" and a.location_id=$cbo_location_name";
    
                if(str_replace("'","",$cbo_bill_type)==0) $bill_type_cond=""; else  $bill_type_cond=" and b.bill_type=$cbo_bill_type";
                if(str_replace("'","",$cbo_bill_type)==0) $process_cond=""; else  $process_cond=" and a.process_id=$cbo_bill_type";
				if(str_replace("'","",$cbo_party_source)==0) $source_cond=""; else $source_cond=" and a.party_source=$cbo_party_source";
                
                if($db_type==0)
                {
                    if( $date_from==0 && $date_to==0 ) $receipt_date_cond=""; else $receipt_date_cond= " and a.receipt_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
                    if( $date_from==0 && $date_to==0 ) $bill_date_cond=""; else $bill_date_cond= " and a.bill_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
                }
                else if($db_type==2)
                {
                    if( $date_from==0 && $date_to==0 ) $receipt_date_cond=""; else $receipt_date_cond= " and a.receipt_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
                    if( $date_from=='' && $date_to=='' ) $bill_date_cond=""; else $bill_date_cond= " and a.bill_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
                }
                
                if($date_to=="") $bill_date=""; else $bill_date= " and a.bill_date <=".$txt_date_to."";
                if($date_to=="") $receipt_date=""; else $receipt_date= " and a.receipt_date <=".$txt_date_to."";			
				
				$adjusted_amount_array=array();
                $sql_adj=sql_select("select a.party_name, b.bill_type, sum(b.total_adjusted) as pre_adjusted from subcon_payment_receive_mst a,subcon_payment_receive_dtls b where a.id=b.master_id and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 $party_cond $receipt_date $bill_type_cond group by a.party_name, b.bill_type");
				//echo "select a.party_name, sum(b.total_adjusted) as pre_adjusted from subcon_payment_receive_mst a,subcon_payment_receive_dtls b where a.id=b.master_id and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 $party_cond $receipt_date $bill_type_cond group by a.party_name";
				foreach($sql_adj as $row)
				{
					$adjusted_amount_array[$row[csf('party_name')]][$row[csf('bill_type')]]=$row[csf('pre_adjusted')];
				}
				
				$pay_rec_array=array();
				
                $sql_rec="select a.party_name, b.bill_type,
				sum(case when a.instrument_id in (1,2,3) then b.total_adjusted else 0 end) as rec_amount,
				sum(case when a.instrument_id in (4,5) then b.total_adjusted else 0 end) as lc_amount
                from subcon_payment_receive_mst a, subcon_payment_receive_dtls b where a.id=b.master_id and a.company_id=$cbo_company_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $party_cond $receipt_date $bill_type_cond group by a.party_name, b.bill_type order by a.party_name";
				
				$sql_rec_result=sql_select($sql_rec);
				foreach($sql_rec_result as $row)
				{
					$pay_rec_array[$row[csf('party_name')]][$row[csf('bill_type')]]['rec_amount']=$row[csf('rec_amount')];
					$pay_rec_array[$row[csf('party_name')]][$row[csf('bill_type')]]['lc_amount']=$row[csf('lc_amount')];
					//$pay_rec_array[$row[csf('party_name')]]['instrument_id']=$row[csf('instrument_id')];
				}
				//var_dump($pay_rec_array);
				
				$opening_balance_arr=array();
                $ope_bal=sql_select("select a.party_id, a.process_id, sum(b.amount) as amount from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.company_id=$cbo_company_id and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $location_cond $party_bill_cond $bill_date $process_cond group by a.party_id, a.process_id order by a.party_id");
                foreach ($ope_bal as $row)
                {
                    $opening_balance_arr[$row[csf("party_id")]][$row[csf("process_id")]]=$row[csf("amount")];
                }	
				
				$invoice_amount_arr=array();
                $inv_amt=sql_select("select a.party_id, a.process_id, sum(b.amount) as amount from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.company_id=$cbo_company_id and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $location_cond $party_bill_cond $bill_date $process_cond group by a.party_id, a.process_id order by a.party_id");
                foreach ($inv_amt as $row)
                {
                    $invoice_amount_arr[$row[csf("party_id")]][$row[csf("process_id")]]=$row[csf("amount")];
                }
				//var_dump($invoice_amount_arr);			
			
				$bill_dtls="select a.party_id, a.process_id from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.company_id=$cbo_company_id and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $location_cond $party_bill_cond $process_cond $source_cond group by a.party_id, a.process_id order by a.party_id, a.process_id";
				//listagg(CAST(a.process_id as varchar2(4000)),',') within group (order by a.process_id) as process_id
				//echo $bill_dtls;
                $bill_dtls_result=sql_select($bill_dtls);
				$i=1; $z=0;
                foreach ($bill_dtls_result as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
					//$opening_balance=$opening_balance_arr[$row[csf("party_id")]][$row[csf('process_id')]]-$adjusted_amount_array[$row[csf('party_id')]][$row[csf('process_id')]];
					$invoice_amount=$invoice_amount_arr[$row[csf("party_id")]][$row[csf('process_id')]];
					$receive_amount=$pay_rec_array[$row[csf('party_id')]][$row[csf('process_id')]]['rec_amount'];
					$lc_amount=$pay_rec_array[$row[csf('party_id')]][$row[csf('process_id')]]['lc_amount'];
					$process_name='';
					$process_id=array_unique(explode(',',$row[csf('process_id')]));
					$col_pro=count($process_id); 
					foreach ($process_id as $val)
					{
						if($process_name=="") $process_name= $production_process[$val]; else $process_name.=', '.$production_process[$val];
					}
					?>
                    <tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <?
                      //  if($z==0)
                        //{
                        ?>
                         <td width="40" rowspan="<? //echo $col_pro; ?>" ><? echo $i; ?></td>
                         <td width="160" rowspan="<? //echo $col_pro; ?>" ><? echo $buyer_arr[$row[csf('party_id')]]; ?></td>
                         <?
                        //}
                         ?>
                         <td width="150" ><p><? echo $production_process[$row[csf('process_id')]]; ?></p></td>
                                                   
                         <td width="120" align="right" ><? echo number_format($invoice_amount,2,'.',''); ?></td>
                         <td width="120" align="right" ><? echo number_format($receive_amount,2,'.',''); ?></td>
                         <td width="120" align="right" ><? echo number_format($lc_amount,2,'.',''); ?></td>
                         <td align="right"><? $balance=$invoice_amount-($receive_amount+$lc_amount); echo number_format($balance,2,'.',''); ?></td>
                    </tr>
                    <?

					//$tot_opening+=$opening_balance;
					$tot_inv_amount+=$invoice_amount;
					$tot_receive+=$receive_amount;
					$tot_lc_noncash+=$lc_amount;
					$tot_balance+=$balance;
                    $i++; $z++;
				}
				?>
                <tr class="tbl_bottom">
                    <td colspan="3" align="right"><b>Total:</b></td>
                    <td align="right"><b><? echo number_format($tot_inv_amount,2); ?></b></td>
                    <td align="right"><b><? echo number_format($tot_receive,2); ?></b></td>
                    <td align="right"><b><? echo number_format($tot_lc_noncash,2); ?></b></td>
                    <td align="right"><b><? echo number_format($tot_balance,2); ?></b></td>
                </tr>
            </table>
            </div>
            </fieldset>
        </div>
		<?
	}
	else if($type==3)
	{
		?>
        <div align="center">
         <fieldset style="width:950px;">
            <table cellpadding="0" cellspacing="0" width="930">
                <tr  class="form_caption" style="border:none;">
                   <td align="center" width="100%" colspan="8" style="font-size:20px"><strong><? echo 'Invoice Wise A/R'; ?></strong></td>
                </tr>
                <tr class="form_caption" style="border:none;">
                   <td align="center" width="100%" colspan="8" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
                </tr>
                <tr class="form_caption" style="border:none;">
                    <td align="center" width="100%" colspan="8" style="font-size:12px">
                        <? if( str_replace("'","",$txt_date_to)!="") echo " As On :- ".change_date_format(str_replace("'","",$txt_date_to),'dd-mm-yyyy')."" ;?>
                    </td>
                </tr>
            </table>
            <table width="927" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
                <thead>
                    <th width="40">SL</th>
                    <th width="140">Invc. No.</th>
                    <th width="130">Invc. Date</th>
                    <th width="70">Age (Days)</th>
                    <th width="120">Inv. Amount (Debit)</th>
                    <th width="120">Receive (Credit)</th>
                    <th width="120">Non-Cash /LC (Credit)</th>
                    <th>Balance</th>
                </thead>
            </table>
        <div style="max-height:300px; overflow-y:scroll; width:930px" id="scroll_body">
            <table width="910" border="1" class="rpt_table" rules="all" id="table_body">
            <?
                if(str_replace("'","",$cbo_party_id)==0) $party_cond=""; else  $party_cond=" and a.party_name=$cbo_party_id";
                if(str_replace("'","",$cbo_party_id)==0) $party_bill_cond=""; else  $party_bill_cond=" and a.party_id=$cbo_party_id";
				if(str_replace("'","",$cbo_location_name)==0) $location_cond=""; else $location_cond=" and a.location_id=$cbo_location_name";
    
                if(str_replace("'","",$cbo_bill_type)==0) $bill_type_cond=""; else  $bill_type_cond=" and b.bill_type=$cbo_bill_type";
                if(str_replace("'","",$cbo_bill_type)==0) $process_cond=""; else  $process_cond=" and a.process_id=$cbo_bill_type";
				if(str_replace("'","",$cbo_party_source)==0) $source_cond=""; else $source_cond=" and a.party_source=$cbo_party_source";
                
                if($db_type==0)
                {
                    if( $date_from==0 && $date_to==0 ) $receipt_date_cond=""; else $receipt_date_cond= " and a.receipt_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
                    if( $date_from==0 && $date_to==0 ) $bill_date_cond=""; else $bill_date_cond= " and a.bill_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
                }
                else if($db_type==2)
                {
                    if( $date_from==0 && $date_to==0 ) $receipt_date_cond=""; else $receipt_date_cond= " and a.receipt_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
                    if( $date_from=='' && $date_to=='' ) $bill_date_cond=""; else $bill_date_cond= " and a.bill_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
                }
                
                if($date_to=="") $bill_date=""; else $bill_date= " and a.bill_date <=".$txt_date_to."";
                if($date_to=="") $receipt_date=""; else $receipt_date= " and a.receipt_date <=".$txt_date_to."";

                $pre_add_array=array();
                $sql_adj=sql_select("select a.party_name, b.bill_no, max(b.bill_date) as bill_date, b.bill_type,
				sum(case when a.instrument_id in (1,2,3) then b.total_adjusted else 0 end) as rec_amount,
				sum(case when a.instrument_id in (4,5) then b.total_adjusted else 0 end) as lc_amount
        from subcon_payment_receive_mst a,subcon_payment_receive_dtls b where a.id=b.master_id and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 $party_cond $receipt_date $bill_type_cond $source_cond group by a.party_name, b.bill_type, b.bill_no order by a.party_name, b.bill_type, b.bill_no");
              /* echo "select a.party_name, b.bill_no, max(b.bill_date) as bill_date, b.bill_type,
				sum(case when a.instrument_id in (1,2,3) then b.total_adjusted else 0 end) as rec_amount,
				sum(case when a.instrument_id in (4,5) then b.total_adjusted else 0 end) as lc_amount
        from subcon_payment_receive_mst a,subcon_payment_receive_dtls b where a.id=b.master_id and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 $party_cond $receipt_date $bill_type_cond group by a.party_name, b.bill_type, b.bill_no order by a.party_name, b.bill_type, b.bill_no";*/
    			foreach ($sql_adj as $row)
                {
                    $pre_add_array[$row[csf("party_name")]][$row[csf("bill_type")]][$row[csf("bill_no")]]['rec_amount']=$row[csf("rec_amount")];
                    $pre_add_array[$row[csf("party_name")]][$row[csf("bill_type")]][$row[csf("bill_no")]]['lc_amount']=$row[csf("lc_amount")];
                    $pre_add_array[$row[csf("party_name")]][$row[csf("bill_type")]][$row[csf("bill_no")]]['bill_date']=$row[csf("bill_date")];
                }
    			// $sql_adj[0][csf('pre_adjusted')];
               // print_r($pre_add_array);
               $sql_bill="select a.bill_no, a.prefix_no_num, a.party_id, max(a.bill_date) as bill_date, a.process_id, sum(b.amount) as amount from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.company_id=$cbo_company_id and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $location_cond $party_bill_cond $bill_date $process_cond group by  a.party_id, a.process_id, a.bill_no, a.prefix_no_num order by a.party_id, a.process_id";
                
                $sql_bill_result=sql_select($sql_bill);
                $i=1; $k=1; $j=1; $party_array=array(); $process_array=array();
                foreach ($sql_bill_result as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
                    if (!in_array( $row[csf("party_id")],$party_array) )
                    {
                        if($k!=1)
                        { 
                        ?>
							<tr class="tbl_bottom">
								<td colspan="4" align="right"><b>Process Total:</b></td>
								<td align="right"><b><? echo number_format($process_inv_amt,2); ?></b></td>
								<td align="right"><b><? echo number_format($process_receive,2); ?></b></td>
								<td align="right"><b><? echo number_format($process_lc,2); ?></b></td>
								<td align="right"><b><? echo number_format($process_balance,2); ?></b></td>
							</tr>
                            <tr class="tbl_bottom">
                                <td colspan="4" align="right"><b>Party Total:</b></td>
                                <td align="right"><b><? echo number_format($party_inv_amt,2); ?></b></td>
                                <td align="right"><b><? echo number_format($party_receive,2); ?></b></td>
                                <td align="right"><b><? echo number_format($party_lc,2); ?></b></td>
                                <td align="right"><b><? echo number_format($party_balance,2); ?></b></td>
                            </tr>
                        <?
                            unset($process_inv_amt);
							unset($process_receive);
							unset($process_lc);
							unset($process_balance);
							
							unset($party_inv_amt);
							unset($party_receive);
							unset($party_lc);
							unset($party_balance);
                        }
                        ?>
                            <tr bgcolor="#dddddd">
                                <td colspan="8" align="left" ><b>Party Name: <? echo $buyer_arr[$row[csf("party_id")]]; ?></b></td>
                            </tr>
                        <?
						$party_array[]=$row[csf("party_id")];
                        $k++;
                    }
					if (!in_array( $row[csf("process_id")],$process_array) )
					{
						if($j!=1)
						{ 
						?>
							<tr class="tbl_bottom">
								<td colspan="4" align="right"><b>Process Total:</b></td>
								<td align="right"><b><? echo number_format($process_inv_amt,2); ?></b></td>
								<td align="right"><b><? echo number_format($process_receive,2); ?></b></td>
								<td align="right"><b><? echo number_format($process_lc,2); ?></b></td>
								<td align="right"><b><? echo number_format($process_balance,2); ?></b></td>
							</tr>
						<?
                            unset($process_inv_amt);
							unset($process_receive);
							unset($process_lc);
							unset($process_balance);
						}
						?>
							<tr bgcolor="#dddddd">
								<td colspan="8" align="left" ><b>Bill Type: <? echo $production_process[$row[csf('process_id')]]; ?></b></td>
							</tr>
						<?
						$process_array[]=$row[csf("process_id")];
						$j++;
					}                    
					$daysOnHand = datediff("d",$row[csf('bill_date')],date("Y-m-d"));
                   // echo $row[csf("party_id")].'=='.$row[csf("process_id")].'=='.$row[csf("bill_no")];
                    ?>
                    <tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                         <td width="40" ><? echo $i; ?></td>
                         <td width="140" ><? echo $row[csf('bill_no')]; ?></td>
                         <td width="130" align="center" ><? echo '&nbsp;'.change_date_format($row[csf('bill_date')]); ?></td>
                         <td width="70" align="center" ><? echo $daysOnHand; ?></td>
                         <td width="120" align="right" ><? echo number_format($row[csf('amount')],2,'.',''); ?></td>
                         <td width="120" align="right" ><? $rec_amount=$pre_add_array[$row[csf("party_id")]][$row[csf("process_id")]][$row[csf("bill_no")]]['rec_amount']; echo number_format($rec_amount,2,'.',''); ?></td>
                         <td width="120" align="right" ><? $lc_amount=$pre_add_array[$row[csf("party_id")]][$row[csf("process_id")]][$row[csf("bill_no")]]['lc_amount']; echo number_format($lc_amount,2,'.',''); ?></td>
                         <td align="right"><? $balance=($row[csf('amount')]-($rec_amount+$lc_amount)); echo number_format($balance,2,'.',''); ?></td>
                    </tr>
                    <?
					$process_inv_amt+=$row[csf('amount')];
					$process_receive+=$rec_amount;
					$process_lc+=$lc_amount;
					$process_balance+=$balance;
					
					$party_inv_amt+=$row[csf('amount')];
					$party_receive+=$rec_amount;
					$party_lc+=$lc_amount;
					$party_balance+=$balance;
					
					$grand_inv_amt+=$row[csf('amount')];
					$grand_receive+=$rec_amount;
					$grand_lc+=$lc_amount;
					$grand_balance+=$balance;
                    $i++;			
                }
                ?>
                <tr class="tbl_bottom">
                    <td colspan="4" align="right"><b>Process Total:</b></td>
                    <td align="right"><b><? echo number_format($process_inv_amt,2); ?></b></td>
                    <td align="right"><b><? echo number_format($process_receive,2); ?></b></td>
                    <td align="right"><b><? echo number_format($process_lc,2); ?></b></td>
                    <td align="right"><b><? echo number_format($process_balance,2); ?></b></td>
                </tr>
                <tr class="tbl_bottom">
                    <td colspan="4" align="right"><b>Party Total:</b></td>
                    <td align="right"><b><? echo number_format($party_inv_amt,2); ?></b></td>
                    <td align="right"><b><? echo number_format($party_receive,2); ?></b></td>
                    <td align="right"><b><? echo number_format($party_lc,2); ?></b></td>
                    <td align="right"><b><? echo number_format($party_balance,2); ?></b></td>
                </tr>
                <tr class="tbl_bottom">
                    <td colspan="4" align="right"><b>Grand Total:</b></td>
                    <td align="right"><b><? echo number_format($grand_inv_amt,2); ?></b></td>
                    <td align="right"><b><? echo number_format($grand_receive,2); ?></b></td>
                    <td align="right"><b><? echo number_format($grand_lc,2); ?></b></td>
                    <td align="right"><b><? echo number_format($grand_balance,2); ?></b></td>
                </tr>
            </table>
            </div>
            </fieldset>
            </div>
        <?
	}
	else if($type==4)
	{
		?>
        <div align="center">
         <fieldset style="width:930px;">
            <table cellpadding="0" cellspacing="0" width="930">
                <tr  class="form_caption" style="border:none;">
                   <td align="center" width="100%" colspan="10" style="font-size:20px"><strong><? echo 'Bill Issue Statement'; ?></strong></td>
                </tr>
                <tr class="form_caption" style="border:none;">
                   <td align="center" width="100%" colspan="10" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
                </tr>
                <tr class="form_caption" style="border:none;">
                    <td align="center" width="100%" colspan="10" style="font-size:12px">
                        <? if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="") echo "From ".change_date_format(str_replace("'","",$txt_date_from),'dd-mm-yyyy')." To ".change_date_format(str_replace("'","",$txt_date_to),'dd-mm-yyyy')."" ;?>
                    </td>
                </tr>
            </table>
            <table width="930" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
                <thead>
                    <th width="30">SL</th>
                    <th width="110">Invc. No.</th>
                    <th width="70">Invc. Date</th>
                    <th width="120">Party</th>
                    <th width="70">Challan</th>
                    <th width="90">Batch No</th>
                    <th width="100">Order No</th>
                    <th width="80">Internal Ref.</th>
                    <th width="90">Color</th>
                    <th width="80">Bill Qty</th>
                    <th>Bill Amount</th>
                </thead>
            </table>
        <div style="max-height:300px; overflow-y:scroll; width:930px" id="scroll_body">
            <table width="910" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
            <?
				//$po_num_arr=return_library_array( "select id, po_number from wo_po_break_down", "id", "po_number");
				$sql_po=sql_select("select id, po_number, grouping from wo_po_break_down ");
				$po_num_arr=array();
				foreach($sql_po as $prow)
				{
					$po_num_arr[$prow[csf('id')]]['po']=$prow[csf('po_number')];
					$po_num_arr[$prow[csf('id')]]['ref']=$prow[csf('grouping')];
				}
				unset($sql_po);
                if(str_replace("'","",$cbo_party_id)==0) $party_bill_cond=""; else  $party_bill_cond=" and a.party_id=$cbo_party_id";
				if(str_replace("'","",$cbo_location_name)==0) $location_cond=""; else $location_cond=" and a.location_id=$cbo_location_name";
                if(str_replace("'","",$cbo_bill_type)==0) $process_cond=""; else  $process_cond=" and a.process_id=$cbo_bill_type";
				if(str_replace("'","",$cbo_party_source)==0) $source_cond=""; else $source_cond=" and a.party_source=$cbo_party_source";
                
                if($db_type==0)
                {
                    if( $date_from==0 && $date_to==0 ) $bill_date_cond=""; else $bill_date_cond= " and a.bill_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
                }
                else if($db_type==2)
                {
                    if( $date_from=='' && $date_to=='' ) $bill_date_cond=""; else $bill_date_cond= " and a.bill_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
                }
				if(str_replace("'","",$cbo_party_source)==0) $party_source_cond=""; else $party_source_cond=" and a.party_source=$cbo_party_source";
				if ($db_type==0)
				{
					$delivery_id_cond="group_concat(b.delivery_id) as delivery_id";
					$batch_id_cond="group_concat(c.batch_id) as batch_id";
				}
				else if ($db_type==2)
				{
					$delivery_id_cond="listagg(CAST(b.delivery_id as varchar2(4000)),',') within group (order by b.delivery_id) as delivery_id";
					$batch_id_cond="listagg(CAST(c.batch_id as varchar2(4000)),',') within group (order by c.batch_id) as batch_id";
				}
                
            	$sql_bill="select a.bill_no, a.prefix_no_num, a.party_source, a.party_id, a.bill_date, b.color_id, b.order_id, b.challan_no, sum(b.delivery_qty) as bill_qty, sum(b.amount) as amount, b.delivery_id from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.company_id=$cbo_company_id and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $location_cond $party_bill_cond $party_source_cond $bill_date_cond $process_cond $source_cond group by a.bill_no, a.prefix_no_num, a.party_source, a.party_id, a.bill_date, b.color_id, b.order_id, b.challan_no, b.delivery_id order by a.party_id, a.prefix_no_num";
               // echo $sql_bill;
                $sql_bill_result=sql_select($sql_bill);
                $i=1;
                foreach ($sql_bill_result as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$batch_id=array_unique(explode(',',$row[csf('batch_id')]));
					$batch_no="";
					foreach($batch_id as $key)
					{
						if($batch_no=="") $batch_no=$batch_arr[$key]; else $batch_no.=','.$batch_arr[$key];
					}
					$order_no= ''; $party_value=''; $internal_ref="";
					if($row[csf("party_source")]==2)
					{
						$order_no= $order_arr[$row[csf('order_id')]];
						$party_value=$buyer_arr[$row[csf('party_id')]];
						$internal_ref="";
					}
					else if($row[csf("party_source")]==1)
					{
						$order_no= $po_num_arr[$row[csf('order_id')]]['po'];//$po_num_arr[$row[csf('order_id')]];
						$party_value=$company_arr[$row[csf('party_id')]];
						$internal_ref=$po_num_arr[$row[csf('order_id')]]['ref'];
					}
                    ?>
                    <tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                         <td width="30"><? echo $i; ?></td>
                         <td width="110" style="word-break:break-all"><? echo $row[csf('bill_no')]; ?></td>
                         <td width="70"><? echo '&nbsp;'.change_date_format($row[csf('bill_date')]); ?></td>
                         <td width="120" style="word-break:break-all"><? echo $party_value; ?></td>
                         <td width="70" align="center" style="word-break:break-all"><? echo $row[csf('challan_no')]; ?></td>
                         <td width="90" style="word-break:break-all"><? echo $batch_no; ?>&nbsp;</td>
                         <td width="100" style="word-break:break-all"><? echo $order_no; ?></td>
                         <td width="80" style="word-break:break-all"><? echo $internal_ref; ?></td>
                         <td width="90" style="word-break:break-all"><? echo $color_arr[$row[csf('color_id')]]; ?>&nbsp;</td>
                         <td width="80" align="right"><?  echo number_format($row[csf('bill_qty')],2,'.',''); ?></td>
                         <td align="right"><? echo number_format($row[csf('amount')],2,'.',''); ?></td>
                    </tr>
                    <?
					$bill_qty+=$row[csf('bill_qty')];
					$bill_amount+=$row[csf('amount')];
                    $i++;			
                }
                ?>
            </table>
             <table width="910" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all">
                <tr>
                    <td width="30"></td>
                    <td width="110"></td>
                    <td width="70"></td>
                    <td width="120"></td>
                    <td width="70"></td>
                    <td width="90"></td>
                    <td width="100"></td>
                    <td width="80"></td>
                    <td width="90"></td>
                    <td width="80" align="right" id="tot_bill_qnty"><b><? //echo number_format($bill_qty,2); ?></b></td>
                    <td align="right" id="tot_bill_amt"><b><? //echo number_format($bill_amount,2); ?></b></td>
                </tr>
            </table>
            </div>
            </fieldset>
            </div>
        <?
	}
	else if($type==5)
	{
		?>
        <div>
         <fieldset style="width:1150px;">
            <table cellpadding="0" cellspacing="0" width="1160">
                <tr  class="form_caption" style="border:none;">
                   <td align="center" width="100%" colspan="12" style="font-size:20px"><strong><? echo 'Bill Issue Statement'; ?></strong></td>
                </tr>
                <tr class="form_caption" style="border:none;">
                   <td align="center" width="100%" colspan="12" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
                </tr>
                <tr class="form_caption" style="border:none;">
                    <td align="center" width="100%" colspan="12" style="font-size:12px">
                        <? if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="") echo "From ".change_date_format(str_replace("'","",$txt_date_from),'dd-mm-yyyy')." To ".change_date_format(str_replace("'","",$txt_date_to),'dd-mm-yyyy')."" ;?>
                    </td>
                </tr>
            </table>
            <table width="1160" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
                <thead>
                    <th width="30">SL</th>
                    <th width="110">Invc. No.</th>
                    <th width="75">Invc. Date</th>
                    <th width="125">Party</th>
                    <th width="70">Challan</th>
                    <th width="90">Batch No</th>
                    <th width="110">Order No</th>
                    <th width="110">Color</th>
                    <th width="140">Fab.Description</th>
                    <th width="100">Bill Qty</th>
                    <th width="70">Rate</th>
                    <th width="70">Bill Amount</th>
                    <th>Currency</th>
                </thead>
            </table>
        <div style="max-height:300px; overflow-y:scroll; width:1178px" id="scroll_body">
            <table width="1160" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
            <?
				$dyeing_prod_arr=return_library_array( "select id, item_description from  pro_batch_create_dtls", "id", "item_description");
				$item_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
                $prod_dtls_arr=return_library_array( "select id, product_name_details from  product_details_master",'id','product_name_details');
                $batch_array=return_library_array( "select id, item_description from  pro_batch_create_dtls",'id','item_description');
                $sql_po=sql_select("select id, po_number, grouping from wo_po_break_down ");
				$po_num_arr=array();
				foreach($sql_po as $prow)
				{
					$po_num_arr[$prow[csf('id')]]['po']=$prow[csf('po_number')];
					$po_num_arr[$prow[csf('id')]]['ref']=$prow[csf('grouping')];
				}
				unset($sql_po); 
				
                if(str_replace("'","",$cbo_party_id)==0) $party_bill_cond=""; else  $party_bill_cond=" and a.party_id=$cbo_party_id";
    			if(str_replace("'","",$cbo_location_name)==0) $location_cond=""; else $location_cond=" and a.location_id=$cbo_location_name";
                if(str_replace("'","",$cbo_bill_type)==0) $process_cond=""; else  $process_cond=" and a.process_id=$cbo_bill_type";
				if(str_replace("'","",$cbo_party_source)==0) $source_cond=""; else $source_cond=" and a.party_source=$cbo_party_source";
                
                if($db_type==0)
                {
                    if( $date_from==0 && $date_to==0 ) $bill_date_cond=""; else $bill_date_cond= " and a.bill_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
                }
                else if($db_type==2)
                {
                    if( $date_from=='' && $date_to=='' ) $bill_date_cond=""; else $bill_date_cond= " and a.bill_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
                }
				
				if ($db_type==0)
				{
					$delivery_id_cond="group_concat(b.delivery_id) as delivery_id";
					$batch_id_cond="group_concat(c.batch_id) as batch_id";
				}
				else if ($db_type==2)
				{
					$delivery_id_cond="listagg(CAST(b.delivery_id as varchar2(4000)),',') within group (order by b.delivery_id) as delivery_id";
					$batch_id_cond="listagg(CAST(c.batch_id as varchar2(4000)),',') within group (order by c.batch_id) as batch_id";
				}
				$delivery_batch_arr=return_library_array( "select id, batch_id from subcon_inbound_bill_dtls",'id','batch_id');
				
                
            	$sql_bill="select a.bill_no, a.prefix_no_num, a.party_id, a.bill_date, b.color_id, b.order_id, b.item_id, b.process_id, b.challan_no, b.id as delivery_id , sum(b.delivery_qty) as bill_qty, sum(b.rate) as rate, sum(b.amount) as amount,a.party_source,b.currency_id from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.company_id=$cbo_company_id and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  $location_cond $party_bill_cond $bill_date_cond $process_cond $source_cond group by a.bill_no, a.prefix_no_num, a.party_id, a.bill_date, b.color_id, b.order_id, b.item_id, b.process_id, b.challan_no, b.id,a.party_source,b.currency_id order by a.party_id, a.prefix_no_num"; // b.delivery_id
                //echo $sql_bill;
                $sql_bill_result=sql_select($sql_bill);
                $i=1;
                foreach ($sql_bill_result as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
					$delivery_id=array_unique(explode(',',$row[csf('delivery_id')]));
					$batch_no="";
					foreach($delivery_id as $did)
					{
						$batch_id=array_unique(explode(',',$delivery_batch_arr[$did]));
						foreach($batch_id as $key)
						{
							if($batch_no=="") $batch_no=$batch_arr[$key]; else $batch_no.=','.$batch_arr[$key];
						}
					}

                    if($row[csf('party_source')]==2)
                    {
                        $item_all= explode(',',$batch_array[$row[csf('item_id')]]);
                    }
                    else if($row[csf('party_source')]==1)
                    {
                        $item_all= explode(',',$row[csf('item_id')]);
                    }

                    $item_name="";
                    foreach($item_all as $inf)
                    {
                        if($row[csf('party_source')]==2)
                        {
                            if($item_name=="") $item_name=$inf; else $item_name.=", ".$inf;
                        }
                        else if($row[csf('party_source')]==1)
                        {
                            if($item_name=="") $item_name=$prod_dtls_arr[$inf]; else $item_name.=", ".$prod_dtls_arr[$inf];
                        }
                    }
					
					$fab_desc="";
					if($row[csf('process_id')]==4) $fab_desc=$dyeing_prod_arr[$row[csf('item_id')]];
					else if($row[csf('process_id')]==2) $fab_desc=$item_arr[$row[csf('item_id')]];
					else $fab_desc=$garments_item[$row[csf('item_id')]];

                    if(empty($fab_desc))
                    {
                        $fab_desc=$item_name;
                    }

                    $party_value='';  $order_no= '';
					if($row[csf("party_source")]==2)
					{			
                        $order_no= $order_arr[$row[csf('order_id')]];		
						$party_value=$buyer_arr[$row[csf('party_id')]];
					}
					else if($row[csf("party_source")]==1)
					{
                        $order_no= $po_num_arr[$row[csf('order_id')]]['po'];
						$party_value=$company_arr[$row[csf('party_id')]];
					}
					
                    ?>
                    <tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                         <td width="30"><? echo $i; ?></td>
                         <td width="110"><p><? echo $row[csf('bill_no')]; ?></p></td>
                         <td width="75"><? echo '&nbsp;'.change_date_format($row[csf('bill_date')]); ?></td>
                         <td width="125"><? echo $party_value; ?></td>
                         <td width="70" align="center" ><p><? echo $row[csf('challan_no')]; ?></p></td>
                         <td width="90" ><p><? echo $batch_no;//$batch_arr[$row[csf('batch_id')]]; ?></p></td>
                         <td width="110" ><p><? echo $order_no; ?></p></td>
                         <td width="110" ><p><? echo $color_arr[$row[csf('color_id')]]; ?></p></td>
                         <td width="140" ><p><? echo $fab_desc; ?></p></td>
                         <td width="100" align="right"><? echo number_format($row[csf('bill_qty')],2,'.',''); ?></td>
                         <td width="70" align="right"><? echo number_format($row[csf('rate')],2,'.',''); ?></td>
                         <td width="70"align="right"><? echo number_format($row[csf('amount')],2,'.',''); ?></td>
                         <td><? 
                            $currency = array(1 => "Taka", 2 => "USD", 3 => "EURO", 4 => "CHF", 5 => "SGD", 6 => "Pound", 7 => "YEN");
                            echo $currency[$row[csf("currency_id")]]; ?>
                         </td>
                    </tr>
                    <?
					$bill_qty+=$row[csf('bill_qty')];
					$bill_amount+=$row[csf('amount')];
                    $i++;			
                }
                ?>
                <tr class="tbl_bottom">
                    <td colspan="9" align="right"><b> Total:</b></td>
                    <td align="right"><b><? echo number_format($bill_qty,2); ?></b></td>
                    <td align="right">&nbsp;</td>
                    <td align="right"><b><? echo number_format($bill_amount,2); ?></b></td>
                </tr>
            </table>
            </div>
            </fieldset>
            </div>
        <?
	}
	else if($type==6)
	{
		?>
        <div>
         <fieldset style="width:1150px;">
            <table cellpadding="0" cellspacing="0" width="1130">
                <tr  class="form_caption" style="border:none;">
                   <td align="center" width="100%" colspan="12" style="font-size:20px"><strong><? echo 'Bill Issue Statement'; ?></strong></td>
                </tr>
                <tr class="form_caption" style="border:none;">
                   <td align="center" width="100%" colspan="12" style="font-size:16px"><strong><? echo $company_arr[str_replace("'","",$cbo_company_id)]; ?></strong></td>
                </tr>
                <tr class="form_caption" style="border:none;">
                    <td align="center" width="100%" colspan="12" style="font-size:12px">
                        <? if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="") echo "From ".change_date_format(str_replace("'","",$txt_date_from),'dd-mm-yyyy')." To ".change_date_format(str_replace("'","",$txt_date_to),'dd-mm-yyyy')."" ;?>
                    </td>
                </tr>
            </table>
            <table width="1055" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
                <thead>
                    <th width="30">SL</th>
                    <th width="80">Bill Date</th>
                    <th width="120">Bill No</th>
                    <th width="125">Party</th>
                    <th width="100">Process</th>
                    <th width="100">Gray Used Qty Kg</th>
                    <th width="100">Delivery Qty Kg</th>
                    <th width="100">Process Loss Kg</th>
                    <th width="100">Bill Qty</th>
                    <th width="100">AVG Rate</th>
                    <th width="">Bill Amount</th>
                   
                </thead>
            </table>
        <div style="max-height:300px; overflow-y:scroll; width:1075px" id="scroll_body">
            <table width="1055" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_body">
            <?
				$dyeing_prod_arr=return_library_array( "select id, item_description from  pro_batch_create_dtls", "id", "item_description");
				$item_arr=return_library_array( "select id,const_comp from lib_subcon_charge",'id','const_comp');
				
                if(str_replace("'","",$cbo_party_id)==0) $party_bill_cond=""; else  $party_bill_cond=" and a.party_id=$cbo_party_id";
                if(str_replace("'","",$cbo_bill_type)==0) $process_cond=""; else  $process_cond=" and a.process_id=$cbo_bill_type";
				if(str_replace("'","",$cbo_location_name)==0) $location_cond=""; else $location_cond=" and a.location_id=$cbo_location_name";
				if(str_replace("'","",$cbo_party_source)==0) $source_cond=""; else $source_cond=" and a.party_source=$cbo_party_source";
                
                if($db_type==0)
                {
                    if( $date_from==0 && $date_to==0 ) $bill_date_cond=""; else $bill_date_cond= " and a.bill_date between '".change_date_format($date_from,'yyyy-mm-dd')."' and '".change_date_format($date_to,'yyyy-mm-dd')."'";
                }
                else if($db_type==2)
                {
                    if( $date_from=='' && $date_to=='' ) $bill_date_cond=""; else $bill_date_cond= " and a.bill_date between '".change_date_format($date_from,'','',1)."' and '".change_date_format($date_to,'','',1)."'";
                }
				
				if ($db_type==0)
				{
					$delivery_id_cond="group_concat(b.delivery_id) as delivery_id";
					$batch_id_cond="group_concat(c.batch_id) as batch_id";
				}
				else if ($db_type==2)
				{
					$delivery_id_cond="listagg(CAST(b.delivery_id as varchar2(4000)),',') within group (order by b.delivery_id) as delivery_id";
					$batch_id_cond="listagg(CAST(c.batch_id as varchar2(4000)),',') within group (order by c.batch_id) as batch_id";
				}
				$sewing_del="select b.id as sewing_dtls_id,a.party_id,b.delivery_id,b.delivery_date,b.packing_qnty,b.delivery_qty from subcon_inbound_bill_mst a,subcon_inbound_bill_dtls  b where a.id=b.mst_id and b.process_id=5";
				$sewing_del_result=sql_select($sewing_del);
				 foreach ($sewing_del_result as $row)
				 {
					$sewing_issue_arr[$row[csf('sewing_dtls_id')]]['packing_qnty']+=$row[csf('packing_qnty')];
					$sewing_issue_arr[$row[csf('sewing_dtls_id')]]['delivery_qty']+=$row[csf('delivery_qty')];
				 }
				
				  $payment_recv="select party_name,exchenge_amount from subcon_payment_receive_mst a,subcon_payment_receive_dtls b where a.id=b.master_id and a.company_id=$cbo_company_id and b.status_active=1 and b.is_deleted=0";
				  $payment_recv_resultset=sql_select($payment_recv);
				  foreach ($payment_recv_resultset as $row)
				 {
					$payment_recv_resultset[$row[csf('party_name')]]['exchenge_amount']+=$row[csf('exchenge_amount')];
				 }
				// subcon_delivery_mst  
                $fin_delivery="select c.id as fin_del_dtls_id,c.gray_qty,c.delivery_qty, c.process_id, c.sub_process_id,d.party_id,d.process_id from subcon_delivery_dtls c, subcon_delivery_mst d where d.id=c.mst_id and d.process_id in(2,4,3) and  d.company_id=$cbo_company_id and d.status_active=1 and d.is_deleted=0";
				$fin_del_result=sql_select($fin_delivery);
            	foreach ($fin_del_result as $row)
                {
					$dye_fin_delivery_arr[$row[csf('fin_del_dtls_id')]]['process_id']=$row[csf('process_id')];
					$dye_fin_delivery_arr[$row[csf('fin_del_dtls_id')]]['party_id']=$row[csf('party_id')];
					$dye_fin_delivery_arr[$row[csf('fin_del_dtls_id')]]['gray_qty']+=$row[csf('gray_qty')];
					$dye_fin_delivery_arr[$row[csf('fin_del_dtls_id')]]['delivery_qty']+=$row[csf('delivery_qty')];
				}
				$sql_bill="select b.delivery_id,a.bill_no,a.party_source, a.prefix_no_num, a.party_id, a.bill_date, b.color_id, b.order_id, b.item_id, b.process_id, b.challan_no,(b.delivery_qty) as bill_qty, (b.rate) as rate,(b.amount) as amount from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.company_id=$cbo_company_id and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $location_cond $party_bill_cond $bill_date_cond $process_cond $source_cond order by a.bill_no ASC ";
                
                $sql_bill_result=sql_select($sql_bill);
				foreach ($sql_bill_result as $row)
                {
					$bill_no_arr[$row[csf('bill_no')]]['bill_date']=$row[csf('bill_date')];
					$bill_no_arr[$row[csf('bill_no')]]['amount']+=$row[csf('amount')];
					$bill_no_arr[$row[csf('bill_no')]]['bill_qty']+=$row[csf('bill_qty')];
					$bill_no_arr[$row[csf('bill_no')]]['gray_qty']+=$dye_fin_delivery_arr[$row[csf('delivery_id')]]['gray_qty'];
					$bill_no_arr[$row[csf('bill_no')]]['delivery_qty']+=$dye_fin_delivery_arr[$row[csf('delivery_id')]]['delivery_qty'];
					$bill_no_arr[$row[csf('bill_no')]]['process_id']=$row[csf('process_id')];//$dye_fin_delivery_arr[$row[csf('delivery_id')]]['process_id'];
					$bill_no_arr[$row[csf('bill_no')]]['party_id']=$dye_fin_delivery_arr[$row[csf('delivery_id')]]['party_id'];
					$bill_no_arr[$row[csf('bill_no')]]['party_source']=$row[csf('party_source')];
				}
                $i=1;$tot_bill_qty=$tot_bill_amount=$tot_payment_recv=$tot_bill_amount=0;
                foreach($bill_no_arr as $bill_no=>$row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
					
					$gray_used_qty=$row[('gray_qty')];$fin_delivery_qty=$row[('delivery_qty')];
					$process_loss_qty=$gray_used_qty-$fin_delivery_qty;
					$party_source=$row[('party_source')];
				//	echo $party_source.'='.$row[('party_id')].', ';
				if($party_source==2)
				{
					$payment_recv=$payment_recv_resultset[$row[('party_id')]]['exchenge_amount'];
				}
					
                    ?>
                    <tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                         <td width="30"><? echo $i; ?></td>
						 <td width="80"><? echo '&nbsp;'.change_date_format($row[('bill_date')]); ?></td>
                         <td width="120"><p><? echo $bill_no; ?></p></td>
                       
                         <td width="125"><? if($party_source==1) echo $company_arr[$row[('party_id')]];else echo  $buyer_arr[$row[('party_id')]];?></td>
                         <td width="100" align="center" title="Process=<? echo $row[('process_id')];?>" ><p><? echo $production_process[$row[('process_id')]]; ?></p></td>
                         <td width="100" align="right" ><p><? echo number_format($gray_used_qty,0); ?></p></td>
                         <td width="100" align="right" ><p><? echo number_format($fin_delivery_qty,0); ?></p></td>
                         <td width="100" align="right" ><p><? echo number_format($process_loss_qty,0); ?></p></td>
                        
                         <td width="100" align="right"><? echo number_format($row[('bill_qty')],0); ?></td>
                         <td width="100" align="right"><? $avg_rate=$row[('amount')]/$row[('bill_qty')];echo number_format($avg_rate,2,'.',''); ?></td>
						 <td width="" align="right"><? echo number_format($row[('amount')],2,'.',''); ?></td>
                       
                    </tr>
                    <?
					$tot_bill_qty+=$row[('bill_qty')];
					$tot_bill_amount+=$row[('amount')];
					$tot_payment_recv+=$payment_recv;
					//$tot_gray_used_qty+=$gray_used_qty;
					//$tot_bill_amount+=$row[('amount')];
                    $i++;			
                }
                ?>
                <tr class="tbl_bottom">
                    <td colspan="10" align="left"><b> Total Bill:</b></td>
                    <td align="right"><b><? echo number_format($tot_bill_amount,2); ?></b></td>
                </tr>
				<tr class="tbl_bottom">
                    <td colspan="10" align="left"><b> Payment receipt: </b></td>
                    <td align="right"><b><? echo number_format($tot_payment_recv,2); ?></b></td>
                </tr>
				<tr class="tbl_bottom">
                    <td colspan="10" align="left"><b> Due: </b></td>
                    <td align="right"><b><? echo number_format($tot_bill_amount-$tot_payment_recv,2); ?></b></td>
                </tr>
            </table>
            </div>
            </fieldset>
            </div>
        <?
	}
    $html = ob_get_contents();
    ob_clean();
    foreach (glob("*.xls") as $filename) {
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
?>