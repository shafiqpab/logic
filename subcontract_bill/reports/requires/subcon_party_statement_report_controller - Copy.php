<? 
include('../../../includes/common.php');
session_start();
extract($_REQUEST);
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$date=date('Y-m-d');

$company_arr=return_library_array( "select id, company_name from lib_company", "id", "company_name");
$supplier_arr=return_library_array( "select id, supplier_name from lib_supplier", "id", "supplier_name");
$store_arr=return_library_array( "select id, store_name from lib_store_location", "id", "store_name");
$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
$order_arr=return_library_array( "select id, order_no from  subcon_ord_dtls", "id", "order_no");

if ($action=="load_drop_down_buyer")
{ 
	echo create_drop_down( "cbo_party_id", 140, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "--Select Party--", $selected, "" );
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
            <table width="927" border="1" cellpadding="0" cellspacing="0" rules="all" class="rpt_table">
                <thead>
                    <th width="30">SL</th>
                    <th width="110">Trans. Ref.</th>
                    <th width="110">Invoice No.</th>
                    <th width="70">Trans. Date</th>
                    <th width="80">Bill Type</th>                            
                    <th width="100">Narration</th>
                    <th width="100">Inv. Amount (Debit)</th>
                    <th width="100">Receive (Credit)</th>
                    <th width="100">Non-Cash /LC (Credit)</th>
                    <th>Balance</th>
                </thead>
            </table>
        <div style="max-height:300px; overflow-y:scroll; width:930px" id="scroll_body">
            <table width="910" border="1" class="rpt_table" rules="all" id="table_body">
            <?
                /*$sql_dtls="select max(a.receive_no) as receive_no, a.party_name, max(a.receipt_date) as receipt_date, max(a.instrument_id) as instrument_id, max(a.bank_name) as bank_name, max(a.instrument_date) as instrument_date, max(a.instrument_no) as instrument_no, max(b.bill_type) as bill_type, max(b.bill_no) as bill_no, 
                sum(case when a.receipt_date<'".str_replace("'","",$txt_date_from)."' then b.bill_amount else 0 end) as total_opening,
                sum(case when a.receipt_date between '".str_replace("'","",$txt_date_from)."' and '".str_replace("'","",$txt_date_to)."' then b.bill_amount else 0 end) as inv_amount,
                sum(case when a.receipt_date between '".str_replace("'","",$txt_date_from)."' and '".str_replace("'","",$txt_date_to)."' then b.total_adjusted else 0 end) as rec_amount,
                sum(case when a.receipt_date between '".str_replace("'","",$txt_date_from)."' and '".str_replace("'","",$txt_date_to)."' then b.payment_balance else 0 end) as balance
                from subcon_payment_receive_mst a, subcon_payment_receive_dtls b where a.id=b.master_id and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  group by b.master_id, a.party_name order by a.party_name";*/
                if(str_replace("'","",$cbo_party_id)==0) $party_cond=""; else  $party_cond=" and a.party_name=$cbo_party_id";
                if(str_replace("'","",$cbo_party_id)==0) $party_bill_cond=""; else  $party_bill_cond=" and a.party_id=$cbo_party_id";
    
                if(str_replace("'","",$cbo_bill_type)==0) $bill_type_cond=""; else  $bill_type_cond=" and b.bill_type=$cbo_bill_type";
                if(str_replace("'","",$cbo_bill_type)==0) $process_cond=""; else  $process_cond=" and a.process_id=$cbo_bill_type";
                
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
                
                if($date_from=="") $bill_date=""; else $bill_date= " and a.bill_date <".$txt_date_from."";
                if($date_from=="") $receipt_date=""; else $receipt_date= " and a.receipt_date <".$txt_date_from."";
                
                $opening_balance_arr=array();
                $ope_bal=sql_select("select a.party_id, sum(b.amount) as amount from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.company_id=$cbo_company_id and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $party_bill_cond $bill_date $process_cond group by a.party_id order by a.party_id");
                foreach ($ope_bal as $row)
                {
                    $opening_balance_arr[$row[csf("party_id")]]=$row[csf("amount")];
                }
                
                $pre_add_array=array();
                $sql_adj=sql_select("select a.party_name, sum(b.total_adjusted) as pre_adjusted from subcon_payment_receive_mst a,subcon_payment_receive_dtls b where a.id=b.master_id and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 $party_cond $receipt_date $bill_type_cond group by a.party_name order by a.party_name");
               //echo "select a.party_name, sum(b.total_adjusted) as pre_adjusted from subcon_payment_receive_mst a,subcon_payment_receive_dtls b where a.id=b.master_id and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 $party_cond $receipt_date $bill_type_cond group by a.party_name";
    			foreach ($sql_adj as $row)
                {
                    $pre_add_array[$row[csf("party_name")]]=$row[csf("pre_adjusted")];
                }
    			// $sql_adj[0][csf('pre_adjusted')];
               // var_dump($pre_add_array);
			   if($db_type==0)
			   {
				   $sql_bill="select a.bill_no, a.prefix_no_num, a.bill_date, a.party_id, a.bill_for, a.process_id, group_concat(b.order_id) as order_id, sum(b.amount) as amount, sum(b.delivery_qty) as delivery_qty from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.company_id=$cbo_company_id and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $party_bill_cond $bill_date_cond $process_cond group by a.bill_no, a.prefix_no_num, a.bill_date, a.party_id, a.bill_for, a.process_id order by a.party_id, a.bill_no";
			   }
			   else if($db_type==2)
			   {
				   $sql_bill="select a.bill_no, a.prefix_no_num, a.bill_date, a.party_id, a.bill_for, a.process_id, listagg(CAST(b.order_id as varchar2(4000)),',') within group (order by b.order_id) as order_id, sum(b.amount) as amount, sum(b.delivery_qty) as delivery_qty from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.company_id=$cbo_company_id and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $party_bill_cond $bill_date_cond $process_cond group by a.party_id, a.bill_no, a.prefix_no_num, a.bill_date, a.bill_for, a.process_id order by a.party_id,a.bill_no";
			   }
                
                $sql_bill_result=sql_select($sql_bill);
                $i=1; $k=1; $party_array=array();
                foreach ($sql_bill_result as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    $opening_bal=$opening_balance_arr[$row[csf("party_id")]]-$pre_add_array[$row[csf("party_id")]];
                    if (!in_array($row[csf("party_id")],$party_array) )
                    {
                        if($k!=1)
                        { 
                        ?>
                            <tr class="tbl_bottom">
                                <td colspan="6" align="right"><b>Party Total:</b></td>
                                <td align="right"><b><? echo number_format($inv_amount,2); ?></b></td>
                                <td align="right"><b><? echo number_format($tot_rec_amount,2); ?></b></td>
                                <td align="right"><b><? echo number_format($tot_lc_noncash,2); ?></b></td>
                                <td align="right"><b><? echo number_format($bal_row,2); ?></b></td>
                            </tr>
                        <?
                            unset($inv_amount);
                        }
                        ?>
                            <tr bgcolor="#dddddd">
                                <td colspan="10" align="left" ><b>Party Name: <? echo $buyer_arr[$row[csf("party_id")]]; ?></b></td>
                            </tr>
                            <tr bgcolor="#999999">
                                <td colspan="5" align="left" ><b>Opening Balance:</b></td><td colspan="5" align="right" ><b> <? echo number_format($opening_bal,2); ?></b></td>
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
                    $total_bill_am+=$row[csf('amount')];
                    $grand_inv_amount+=$row[csf('amount')];
                    
					$order_no='';
					$order_id=array_unique(explode(',',$row[csf('order_id')]));
					foreach ($order_id as $val)
					{
						if($order_no=="") $order_no= $order_arr[$val]; else $order_no.=', '.$order_arr[$val];
					}
                    
                    ?>
                    <tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                         <td width="30" ><? echo $i; ?></td>
                         <td width="110" ><? echo $row[csf('bill_no')]; ?></td>
                         <td width="110" align="center" ><? echo $row[csf('prefix_no_num')]; ?></td>
                         <td width="70" ><? echo change_date_format($row[csf('bill_date')]); ?></td>
                         <td width="80" ><? echo $production_process[$row[csf('process_id')]]; ?></td>
                         <td width="100" align="center" ><p><? echo $order_no; ?></p></td>
                         <td width="100" align="right" ><? echo number_format($row[csf('amount')],2,'.',''); ?></td>
                         <td width="100" align="center" ><? echo '-'; ?></td>
                         <td width="100" align="center" ><? echo '-'; ?></td>
                         <td align="right"><? echo number_format($bal_row,2,'.',''); ?></td>
                    </tr>
                    <?	
                    $i++;			
                }
                $bill_num_arr=return_library_array( "select bill_no, prefix_no_num from subcon_inbound_bill_mst", "bill_no", "prefix_no_num");
                
                $sql_dtls="select a.receive_no, a.party_name, a.receipt_date, a.instrument_id, a.bank_name, a.instrument_date, a.instrument_no, a.adjustment_type, b.bill_type, b.bill_no,  
                sum(b.total_adjusted) as rec_amount
                from subcon_payment_receive_mst a, subcon_payment_receive_dtls b where a.id=b.master_id and a.company_id=$cbo_company_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $party_cond $receipt_date_cond $bill_type_cond group by a.party_name, b.bill_no, a.receive_no, a.receipt_date, a.instrument_id, a.bank_name, a.instrument_date, a.instrument_no, a.adjustment_type, b.bill_type order by a.party_name, b.bill_no";
                
                //sum(case when b.payment_balance!=0 and b.bill_amount=b.payment_balance then b.total_adjusted else 0 end) as bill_amt
                
                $sql_dtls_result=sql_select($sql_dtls);
                foreach ($sql_dtls_result as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                    
                    /*if (!in_array($row[csf("party_name")],$party_array) )
                    {
                        if($k!=1)
                        {
                        ?>
                            <tr class="tbl_bottom">
                                <td colspan="6" align="right"><b>Party Total:</b></td>
                                <td align="right"><b><? echo number_format($inv_amount,2); ?></b></td>
                                <td align="right"><b><? echo number_format($tot_rec_amount,2); ?></b></td>
                                <td align="right"><b><? echo number_format($tot_lc_noncash,2); ?></b></td>
                                <td align="right"><b><? echo number_format($bal_row,2); ?></b></td>
                            </tr>
                        <?
                            unset($inv_amount);
                            unset($tot_rec_amount);
                            unset($tot_lc_noncash);
                        }
                        ?>
                            <tr bgcolor="#dddddd">
                                <td colspan="10" align="left" ><b>Party Name: <? echo $buyer_arr[$row[csf("party_name")]]; ?></b></td>
                            </tr>
                            <tr bgcolor="#999999">
                                <td colspan="5" align="left" ><b>Opening Balance:</b></td><td colspan="5" align="right" ><b> <? //echo number_format($opening_balance_arr[$row[csf("party_name")]],2); ?></b></td>
                            </tr>
                        <?
                        //$bal_row=$opening_bal;

                        $party_array[]=$row[csf('party_name')];            
                        $k++;
                    }*/
                    /*$inv_amount=0;
                    if ($row[csf('inv_amount')]!=$row[csf('rec_amount')])
                    {
                        $inv_amount=$row[csf('balance')];
                    }
                    else if($row[csf('balance')]!=0)
                    {
                        $inv_amount=$row[csf('balance')];
                    }
                    else
                    {
                        $inv_amount;
                    }
                    
                    $receive=0;
                    if($inv_amount==0)
                    {
                        $receive=$row[csf('rec_amount')];
                    }
                    elseif ($row[csf('inv_amount')]!=$row[csf('rec_amount')] && $inv_amount==0)
                    {
                        $receive=$row[csf('rec_amount')];
                    }*/
                    $bal_row=($bal_row-$row[csf('rec_amount')])-$row[csf('lc_nocash')];

                    $instrument_id=$row[csf('instrument_id')];
                    
                    ?>
                    <tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                         <td width="30" ><? echo $i; ?></td>
                         <td width="110" ><? echo $row[csf('receive_no')]; ?></td>
                         <td width="110" align="center" ><? echo $bill_num_arr[$row[csf('bill_no')]]; ?></td>
                         <td width="70" ><? echo change_date_format($row[csf('receipt_date')]); ?></td>
                         <td width="80" ><? echo $production_process[$row[csf('bill_type')]]; ?></td>
                         <?
							 if ($instrument_id==1 || $instrument_id==2 || $instrument_id==3)
							 {
								 $narration=$row[csf('instrument_no')].'<br>'.change_date_format($row[csf('instrument_date')]).'<br>'.$row[csf('bank_name')];
							 }
							 else  if ($instrument_id==4 || $instrument_id==5)
							 {
								$narration=$adjustment_type[$row[csf('adjustment_type')]];
							 }
						 ?>
                         <td width="100" align="center" ><p><? echo  $narration; ?></p></td>
                         <td width="100" align="center" ><? echo '-'; ?></td>
                         <?
							 if ($instrument_id==1 || $instrument_id==2 || $instrument_id==3) 
							 {
								  $rec_amount=$row[csf('rec_amount')];
								  $grand_rec_amount+=$row[csf('rec_amount')];
								  $align_rec="right";
								  $align_lc="center";
								  $lc_noncash='-';
								  $tot_rec_amount+=$row[csf('rec_amount')];
							  }
							  else  if ($instrument_id==4 || $instrument_id==5)
							  {
								  $rec_amount='-';
								  $align_rec="center";
								  $align_lc="right";
								  $lc_noncash=$row[csf('rec_amount')];
								  $grand_lc_noncash+=$row[csf('rec_amount')];
								  $tot_lc_noncash+=$row[csf('rec_amount')];
							  } 
                         ?>
                         <td width="100" align="<? echo  $align_rec; ?>" ><? echo number_format($rec_amount,2,'.',''); ?></td>
                         <td width="100" align="<? echo  $align_lc; ?>" ><? echo number_format($lc_noncash,2,'.',''); ?></td>
                         <td align="right"><? echo number_format($bal_row,2,'.',''); ?></td>
                    </tr>
                    <?
                    $i++;
                }
				
				$grand_balance+=(($opening_bal+$grand_inv_amount)-$grand_rec_amount)-$grand_lc_noncash;

                ?>
                <tr class="tbl_bottom">
                    <td colspan="6" align="right"><b>Party Total:</b></td>
                    <td align="right"><b><? echo number_format($inv_amount,2); ?></b></td>
                    <td align="right"><b><? echo number_format($tot_rec_amount,2); ?></b></td>
                    <td align="right"><b><? echo number_format($tot_lc_noncash,2); ?></b></td>
                    <td align="right"><b><? echo number_format($bal_row,2); ?></b></td>
                </tr>
                <tr class="tbl_bottom">
                    <td colspan="6" align="right"><b>Grand Total:</b></td>
                    <td align="right"><b><? echo number_format($grand_inv_amount,2); ?></b></td>
                    <td align="right"><b><? echo number_format($grand_rec_amount,2); ?></b></td>
                    <td align="right"><b><? echo number_format($grand_lc_noncash,2); ?></b></td>
                    <td align="right"><b><? echo number_format($grand_balance,2); ?></b></td>
                </tr>
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
                    <th width="130">Party</th>
                    <th width="130">Bill Type</th>
                    <th width="120">Opening Balance</th>
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
    
                if(str_replace("'","",$cbo_bill_type)==0) $bill_type_cond=""; else  $bill_type_cond=" and b.bill_type=$cbo_bill_type";
                if(str_replace("'","",$cbo_bill_type)==0) $process_cond=""; else  $process_cond=" and a.process_id=$cbo_bill_type";
                
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
                $sql_adj=sql_select("select a.party_name, sum(b.total_adjusted) as pre_adjusted from subcon_payment_receive_mst a,subcon_payment_receive_dtls b where a.id=b.master_id and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 $party_cond $receipt_date $bill_type_cond group by a.party_name");
				//echo "select a.party_name, sum(b.total_adjusted) as pre_adjusted from subcon_payment_receive_mst a,subcon_payment_receive_dtls b where a.id=b.master_id and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 $party_cond $receipt_date $bill_type_cond group by a.party_name";
				foreach($sql_adj as $row)
				{
					$adjusted_amount_array[$row[csf('party_name')]]=$row[csf('pre_adjusted')];
				}
				
				$pay_rec_array=array();
				
                $sql_rec="select a.party_name,
				sum(case when a.instrument_id in (1,2,3) then b.total_adjusted else 0 end) as rec_amount,
				sum(case when a.instrument_id in (4,5) then b.total_adjusted else 0 end) as lc_amount
                from subcon_payment_receive_mst a, subcon_payment_receive_dtls b where a.id=b.master_id and a.company_id=$cbo_company_id  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $party_cond $receipt_date $bill_type_cond group by a.party_name order by a.party_name";
				
				$sql_rec_result=sql_select($sql_rec);
				foreach($sql_rec_result as $row)
				{
					$pay_rec_array[$row[csf('party_name')]]['rec_amount']=$row[csf('rec_amount')];
					$pay_rec_array[$row[csf('party_name')]]['lc_amount']=$row[csf('lc_amount')];
					//$pay_rec_array[$row[csf('party_name')]]['instrument_id']=$row[csf('instrument_id')];
				}
				//var_dump($pay_rec_array);
				
				$opening_balance_arr=array();
                $ope_bal=sql_select("select a.party_id, sum(b.amount) as amount from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.company_id=$cbo_company_id and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $party_bill_cond $bill_date $process_cond group by a.party_id order by a.party_id");
                foreach ($ope_bal as $row)
                {
                    $opening_balance_arr[$row[csf("party_id")]]=$row[csf("amount")];
                }	
				
				$invoice_amount_arr=array();
                $inv_amt=sql_select("select a.party_id, sum(b.amount) as amount from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.company_id=$cbo_company_id and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $party_bill_cond $bill_date $process_cond group by a.party_id order by a.party_id");
                foreach ($inv_amt as $row)
                {
                    $invoice_amount_arr[$row[csf("party_id")]]=$row[csf("amount")];
                }				
			
				if($db_type==0)
				{
					$bill_dtls="select a.party_id, group_concat(a.process_id) as process_id from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.company_id=$cbo_company_id and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $party_bill_cond $bill_date $process_cond group by a.party_id order by a.party_id";
				}
				else if($db_type==2)
				{
					$bill_dtls="select a.party_id, listagg(CAST(a.process_id as varchar2(4000)),',') within group (order by a.process_id) as process_id from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.company_id=$cbo_company_id and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $party_bill_cond $process_cond group by a.party_id order by a.party_id";
				}
				//echo $bill_dtls;
                $bill_dtls_result=sql_select($bill_dtls);
				$i=1;
                foreach ($bill_dtls_result as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					
					$opening_balance=$opening_balance_arr[$row[csf("party_id")]]-$adjusted_amount_array[$row[csf('party_id')]];
					
					$process_name='';
					$process_id=array_unique(explode(',',$row[csf('process_id')]));
					foreach ($process_id as $val)
					{
						if($process_name=="") $process_name= $production_process[$val]; else $process_name.=', '.$production_process[$val];
					}
					?>
                        <tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                             <td width="40" ><? echo $i; ?></td>
                             <td width="130" ><? echo $buyer_arr[$row[csf('party_id')]]; ?></td>
                             <td width="130" ><p><? echo $process_name; ?></p></td>
                             <td width="120" align="right" ><? echo number_format($opening_balance,2,'.',''); ?></td>
							                           
                             <td width="120" align="right" ><? echo number_format($invoice_amount_arr[$row[csf("party_id")]],2,'.',''); ?></td>
                             <td width="120" align="right" ><? echo number_format($pay_rec_array[$row[csf('party_id')]]['rec_amount'],2,'.',''); ?></td>
                             <td width="120" align="right" ><? echo number_format($pay_rec_array[$row[csf('party_id')]]['lc_amount'],2,'.',''); ?></td>
                             <td align="right"><? $balance=($opening_balance+$invoice_amount_arr[$row[csf("party_id")]])-($pay_rec_array[$row[csf('party_id')]]['rec_amount']-$pay_rec_array[$row[csf('party_id')]]['lc_amount']); echo number_format($balance,2,'.',''); ?></td>
                        </tr>
                    <?
					$tot_opening+=$opening_balance;
					$tot_inv_amount+=$invoice_amount_arr[$row[csf("party_id")]];
					$tot_receive+=$pay_rec_array[$row[csf('party_id')]]['rec_amount'];
					$tot_lc_noncash+=$pay_rec_array[$row[csf('party_id')]]['lc_amount'];
					$tot_balance+=$balance;
                    $i++;
				}
				?>
                <tr class="tbl_bottom">
                    <td colspan="3" align="right"><b>Total:</b></td>
                    <td align="right"><b><? echo number_format($tot_opening,2); ?></b></td>
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
    
                if(str_replace("'","",$cbo_bill_type)==0) $bill_type_cond=""; else  $bill_type_cond=" and b.bill_type=$cbo_bill_type";
                if(str_replace("'","",$cbo_bill_type)==0) $process_cond=""; else  $process_cond=" and a.process_id=$cbo_bill_type";
                
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
                $sql_adj=sql_select("select a.party_name, b.bill_no, max(b.bill_date) as bill_date,
				sum(case when a.instrument_id in (1,2,3) then b.total_adjusted else 0 end) as rec_amount,
				sum(case when a.instrument_id in (4,5) then b.total_adjusted else 0 end) as lc_amount
 from subcon_payment_receive_mst a,subcon_payment_receive_dtls b where a.id=b.master_id and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 $party_cond $receipt_date $bill_type_cond group by a.party_name, b.bill_no order by a.party_name, b.bill_no");
             //  echo "select a.party_name, b.bill_no, max(b.bill_date) as bill_date, sum(case when a.instrument_id in (1,2,3) then b.total_adjusted else 0 end) as rec_amount, sum(case when a.instrument_id in (4,5) then b.total_adjusted else 0 end) as lc_amount from subcon_payment_receive_mst a,subcon_payment_receive_dtls b where a.id=b.master_id and a.company_id=$cbo_company_id and a.status_active=1 and a.is_deleted=0 $party_cond $receipt_date $bill_type_cond group by a.party_name, b.bill_no order by a.party_name, b.bill_no";
    			foreach ($sql_adj as $row)
                {
                    $pre_add_array[$row[csf("party_name")]][$row[csf("bill_no")]]['rec_amount']=$row[csf("rec_amount")];
                    $pre_add_array[$row[csf("party_name")]][$row[csf("bill_no")]]['lc_amount']=$row[csf("lc_amount")];
                    $pre_add_array[$row[csf("party_name")]][$row[csf("bill_no")]]['bill_date']=$row[csf("bill_date")];
                }
    			// $sql_adj[0][csf('pre_adjusted')];
                //var_dump($pre_add_array);
               $sql_bill="select a.bill_no, a.prefix_no_num, a.party_id, a.bill_date, a.process_id, sum(b.amount) as amount from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.company_id=$cbo_company_id and a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $party_bill_cond $bill_date $process_cond group by a.bill_no, a.prefix_no_num, a.bill_date, a.party_id, a.process_id order by a.party_id, a.process_id";
                
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
                    
                    ?>
                    <tr bgcolor="<? echo $bgcolor;  ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                         <td width="40" ><? echo $i; ?></td>
                         <td width="140" ><? echo $row[csf('bill_no')]; ?></td>
                         <td width="130" align="center" ><? echo change_date_format($row[csf('bill_date')]); ?></td>
                         <td width="70" align="center" ><? echo $daysOnHand; ?></td>
                         <td width="120" align="right" ><? echo number_format($row[csf('amount')],2,'.',''); ?></td>
                         <td width="120" align="right" ><? $rec_amount=$pre_add_array[$row[csf("party_id")]][$row[csf("bill_no")]]['rec_amount']; echo number_format($rec_amount,2,'.',''); ?></td>
                         <td width="120" align="right" ><? $lc_amount=$pre_add_array[$row[csf("party_id")]][$row[csf("bill_no")]]['lc_amount']; echo number_format($lc_amount,2,'.',''); ?></td>
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
}