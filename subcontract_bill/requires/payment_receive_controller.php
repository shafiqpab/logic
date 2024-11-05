<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../includes/common.php');

$permission=$_SESSION['page_permission'];
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if ($action=="load_drop_down_party_name")
{
	echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $selected, "show_list_view(document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_bill_type').value+'_'+ this.value+'_'+document.getElementById('update_id').value+'_'+document.getElementById('cbo_payment_type').value,'payment_receive_list_view','payment_receive_list_view','requires/payment_receive_controller',''); disable_flds(this.value)","","","","","",3); //setFilterGrid(\'list_view_payment\',-1)
	exit();
}

if ($action=="load_drop_down_party_name_popup")
{
	echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$data' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $selected, "","","","","","",3);
	exit();
}

if ($action=="payment_receive_list_view")
{
	echo load_html_head_contents("Payment Receive Info","../", 1, 1, $unicode,1,'');
	$data = explode("_",$data);
	//print_r($data);
	if($data[4]!=2)
	{
	?>
	<script>
	</script>
	</head>
	<body>
        <div style="width:100%;">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="957px" class="rpt_table">
                <thead>
                    <th width="30">SL</th>
                    <th width="100">Bill Type</th>
                    <th width="110">Bill No</th>
                    <th width="70">Bill Issue Year</th>
                    <th width="70">Bill Date</th>
                    <th width="70">Bill Currency</th>                   
                    <th width="100">Balance Bill Amount</th>
                    <th width="100">Current Payment</th>
                    <th width="100">Latest balance</th>
                    <th width="70" class="must_entry_caption">Exchenge Rate</th>
                    <th>Total Amount</th>
                </thead>
            </table>
        </div>
        <div style="width:960px;max-height:180px; overflow-y:scroll" id="sewing_production_list_view">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="940px" class="rpt_table" id="list_view_payment">
                <?  
                    $i=1;
                    if($data[3]=="")
                    {
                        $pre_bill_array=array();
                        $result= sql_select("select bill_id, sum(total_adjusted) as total_adjust from subcon_payment_receive_dtls where status_active=1 and is_deleted=0 group by bill_id ");
                        foreach($result as $inf)
                        {
                            $pre_bill_array[$inf[csf('bill_id')]]=$inf[csf('total_adjust')];
                        }
                        //var_dump($pre_bill_array);

                        if($db_type==0)
                        {
                            $group_cond= " group by a.bill_no";
                            $year_cond= "year(a.insert_date)as year";
                        }
                        else if($db_type==2)
                        {
                            $group_cond= " group by a.id, a.bill_no, a.prefix_no_num, a.insert_date, a.bill_date, a.process_id, b.currency_id";
                            $year_cond= "TO_CHAR(a.insert_date,'YYYY') as year";
                        }
                        
                        if($data[1]!=0) $bill_type=" and a.process_id='$data[1]'"; else $bill_type="";
                        //echo "select a.id as bill_id, a.bill_no, a.prefix_no_num, $year_cond, a.bill_date, b.process_id, b.currency_id, sum(b.amount) as bill_amount from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  a.party_id='$data[2]' $bill_type $group_cond order by a.bill_date";
                        $sql_result= sql_select("select a.id as bill_id, a.bill_no, a.prefix_no_num, $year_cond, a.bill_date, a.process_id, b.currency_id, sum(b.amount) as bill_amount from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and a.company_id='$data[0]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.party_id='$data[2]' $bill_type $group_cond order by a.bill_date");//,sum(b.amount) as bill_amount
						//echo "select a.id as bill_id, a.bill_no, a.prefix_no_num, $year_cond, a.bill_date, b.process_id, b.currency_id, sum(b.amount) as bill_amount from subcon_inbound_bill_mst a, subcon_inbound_bill_dtls b where a.id=b.mst_id and a.company_id='$data[0]' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.party_id='$data[2]' $bill_type $group_cond order by a.bill_date";
                        
                        foreach($sql_result as $row)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";

                            $bill_amount=$row[csf('bill_amount')];
                            $bill_no_id=$row[csf('bill_id')];
                            if($db_type==0)
                            {
                                $group_cond_ad= "";
                            }
                            else if($db_type==2)
                            {
                                $group_cond_ad= " group by id, bill_no";
                            }
                            //echo $bill_amount.'-'.$pre_bill_array[$row[csf('bill_id')]].', ';
                            if(($bill_amount-$pre_bill_array[$row[csf('bill_id')]])>0)
                            {
                                ?>
                                <tr id="tr_<?  echo $row[csf('id')]; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" > 
                                    <input type="hidden" name="updateiddtls_<? echo $i; ?>" id="updateiddtls_<? echo $i; ?>" value="" />
                                    <td width="30" align="center"><? echo $i; ?></td>
                                    <td width="100" align="left"><p><? echo $production_process[$row[csf('process_id')]]; ?></p>
                                        <input type="hidden" name="processid_<? echo $i; ?>" id="processid_<? echo $i; ?>" value="<? echo $row[csf('process_id')]; ?>" />
                                    </td>
                                    <td width="110" align="center"><? echo $row[csf('bill_no')]; ?>
                                        <input type="hidden" name="billno_<? echo $i; ?>" id="billno_<? echo $i; ?>" value="<? echo $row[csf('bill_no')]; ?>" />
                                    </td>
                                    <td width="70" align="center"><? echo $row[csf('year')]; ?><input type="hidden" name="billid_<? echo $i; ?>" id="billid_<? echo $i; ?>" value="<? echo $row[csf('bill_id')]; ?>" /></td>
                                    <td width="70" align="center"><? echo change_date_format($row[csf('bill_date')]); ?>
                                        <input type="hidden" name="billdate_<? echo $i; ?>" id="billdate_<? echo $i; ?>" value="<? echo $row[csf('bill_date')]; ?>" />
                                    </td>
                                    <td width="70" align="center"><input type="hidden" name="currencyid_<? echo $i; ?>" id="currencyid_<? echo $i; ?>" value="<? echo $row[csf('currency_id')]; ?>" /><? echo $currency[$row[csf('currency_id')]]; ?></td>
                                    <td width="100" align="right"><? $bill_amt=$bill_amount-$pre_bill_array[$row[csf('bill_id')]];  echo number_format($bill_amt,2,'.',''); ?>
                                        <input type="hidden" name="billamount_<? echo $i; ?>" id="billamount_<? echo $i; ?>" value="<? echo $bill_amt; ?>" />
                                        <?  $total_sum +=$bill_amt; ?>
                                    </td>
                                    <td width="100" align="right"><input type="text" name="currentpayment_<? echo $i; ?>" id="currentpayment_<? echo $i; ?>" value=""  class="text_boxes_numeric" style="width:80px" readonly /></td>
                                    <td width="100" align="right"><input type="text" name="latestbalance_<? echo $i; ?>" id="latestbalance_<? echo $i; ?>" value=""  class="text_boxes_numeric" style="width:80px" readonly /></td>
                                    <td width="70" align="right"><input type="text" name="exchengerate_<? echo $i; ?>" id="exchengerate_<? echo $i; ?>" value=""  class="text_boxes_numeric" style="width:60px" onBlur="exchenge_rate_val(this.value)" /></td>
                                    <td width="" align="right"><input type="text" name="exchengamount_<? echo $i; ?>" id="exchengamount_<? echo $i; ?>" value=""  class="text_boxes_numeric" style="width:80px" readonly /></td>
                                </tr>
                                <?
                                $i++;
                            }
                        }
                    }
                    else
                    {
                        if($db_type==0)
                        {
                            $year_id_cond= "year(insert_date)as year";
                        }
                        else if($db_type==2)
                        {
                            $year_id_cond= "TO_CHAR(insert_date,'YYYY') as year";
                        }
                        //echo "select id, bill_type, bill_no, $year_id_cond, bill_date, bill_amount, total_adjusted, payment_balance, exchenge_rate, exchenge_amount from subcon_payment_receive_dtls where master_id='$data[1]'";
                        $sql_result= sql_select("select id, bill_type, bill_id, bill_no, $year_id_cond, bill_date, currency_id, bill_amount, total_adjusted, payment_balance, exchenge_rate, exchenge_amount from subcon_payment_receive_dtls where master_id='$data[3]' and status_active=1 order by bill_date");
                        foreach($sql_result as $row)
                        {
                            if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                            ?>
                            <tr id="tr_<?  echo $row[csf('id')]; ?>" bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" > 
                                <td width="30" align="center"><? echo $i; ?></td>
                                <td width="100" align="left"><p><? echo $production_process[$row[csf('bill_type')]]; ?></p>
                                    <input type="hidden" name="updateiddtls_<? echo $i; ?>" id="updateiddtls_<? echo $i; ?>" value="<?  echo $row[csf('id')]; ?>" />
                                    <input type="hidden" name="processid_<? echo $i; ?>" id="processid_<? echo $i; ?>" value="<? echo $row[csf('bill_type')]; ?>" />
                                </td>
                                <td width="110" align="center"><? echo $row[csf('bill_no')]; ?>
                                    <input type="hidden" name="billno_<? echo $i; ?>" id="billno_<? echo $i; ?>" value="<? echo $row[csf('bill_no')]; ?>" />
                                </td>
                                <td width="70" align="center"><? echo $row[csf('year')]; ?><input type="hidden" name="billid_<? echo $i; ?>" id="billid_<? echo $i; ?>" value="<? echo $row[csf('bill_id')]; ?>" /></td>
                                <td width="70" align="center"><? echo change_date_format($row[csf('bill_date')]); ?>
                                    <input type="hidden" name="billdate_<? echo $i; ?>" id="billdate_<? echo $i; ?>" value="<? echo $row[csf('bill_date')]; ?>" />
                                </td>
                                <td width="70" align="center"><input type="hidden" name="currencyid_<? echo $i; ?>" id="currencyid_<? echo $i; ?>" value="<? echo $row[csf('currency_id')]; ?>" /><? echo $currency[$row[csf('currency_id')]]; ?></td>
                                <td width="100" align="right"><? echo number_format($row[csf('bill_amount')],2,'.',''); ?>&nbsp;
                                    <input type="hidden" name="billamount_<? echo $i; ?>" id="billamount_<? echo $i; ?>" value="<? echo $row[csf('bill_amount')]; ?>" />
                                    <?  $total_sum += $row[csf('bill_amount')];?>
                                </td>
                                <td width="100" align="right"><input type="text" name="currentpayment_<? echo $i; ?>" id="currentpayment_<? echo $i; ?>" value="<? echo $row[csf('total_adjusted')]; ?>" class="text_boxes_numeric" style="width:80px;" readonly /></td>
                                <? $total_pay += $row[csf('total_adjusted')];?>
                                <td width="100" align="right"><?  ?><input type="text" name="latestbalance_<? echo $i; ?>" id="latestbalance_<? echo $i; ?>" value="<? echo $row[csf('payment_balance')]; ?>" class="text_boxes_numeric" style="width:80px;" readonly /></td>
                                <td width="70" align="right"><input type="text" name="exchengerate_<? echo $i; ?>" id="exchengerate_<? echo $i; ?>" value="<? echo $row[csf('exchenge_rate')]; ?>" class="text_boxes_numeric" style="width:60px" onBlur="exchenge_rate_val(this.value)" /></td>
                                <td width="" align="right"><input type="text" name="exchengamount_<? echo $i; ?>" id="exchengamount_<? echo $i; ?>" value="<? echo $row[csf('exchenge_amount')]; ?>" class="text_boxes_numeric" style="width:80px" readonly /></td>
                            </tr>
                            <?
                            $i++;
                        }	
                    }
                    ?>
                    <tr>
                        <td colspan="6"></td>
                        <td width="100" align="right">
                            <input type="text" style="text-align:right;width:80px;" name="total_bill" id="total_bill" class="text_boxes_numeric" value="<? echo number_format($total_sum,2,'.',''); ?>" readonly />
                        </td>
                        <td width="100" align="right">
                            <input type="text" style="width:80px;" name="total_payment" id="total_payment" class="text_boxes_numeric" value="<? echo $total_pay; ?>" readonly />
                        </td>
                        <td colspan="3"></td>
                    </tr>
                </table>
                </div>
            </div>
        </body>           
		<script src="../includes/functions_bottom.js" type="text/javascript"></script>
        </html>
	<?
	}
	exit();
}

if ($action=="receive_no_popup")
{
	echo load_html_head_contents("Payment Recive Info","../../", 1, 1, $unicode,'','');
	$ex_data=explode('_',$data);
	?>
	<script>
		function js_set_value(id)
		{ 
			document.getElementById('payment_id').value=id;
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
        <div align="center" style="width:100%;" >
        <form name="paymentreceive_2"  id="paymentreceive_2" autocomplete="off">
            <table width="690" cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center">
                <thead>                	 
                    <th width="150">Company Name</th>
                    <th width="150">Party Name</th>
                    <th width="80">System ID</th>
                    <th width="170">Date Range</th>
                    <th><input type="reset" name="reset" id="reset" class="formbutton" value="Reset" style="width:70px;" /></th>           
                </thead>
                <tbody>
                    <tr>
                        <td> <input type="hidden" id="payment_id">  
							<?   
								echo create_drop_down( "cbo_company_id", 150, "select id,company_name from lib_company comp where status_active =1 and is_deleted=0 $company_cond order by company_name","id,company_name",1, "-- Select Company --", $ex_data[0],"load_drop_down( 'payment_receive_controller', this.value, 'load_drop_down_party_name_popup', 'party_td' );",0 );
                            ?>
                        </td>
                        <td id="party_td">
							<?
								echo create_drop_down( "cbo_party_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id  and b.tag_company='$ex_data[0]' $buyer_cond  and buy.id in (select  buyer_id from  lib_buyer_party_type where party_type in (2,3)) order by buyer_name","id,buyer_name", 1, "-- Select Party --", $ex_data[1], "","","","","","",3);
                            ?> 
                        </td>
                            <td>
                                <input type="text" name="txt_search_common" id="txt_search_common" class="text_boxes_numeric" style="width:75px" />
                            </td>
                        <td align="center">
                            <input name="txt_date_from" id="txt_date_from" class="datepicker" style="width:60px">
                            <input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px">
                        </td> 
                        <td align="center">
                            <input type="button" name="button2" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_company_id').value+'_'+document.getElementById('cbo_party_name').value+'_'+document.getElementById('txt_date_from').value+'_'+document.getElementById('txt_date_to').value+'_'+document.getElementById('txt_search_common').value, 'payment_receive_list_view_popup', 'search_div', 'payment_receive_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" align="center" height="40" valign="middle">
							<? echo load_month_buttons(1);  ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" align="center" valign="top" id=""><div id="search_div"></div></td>
                    </tr>
                </tbody>
            </table>    
        </form>
        </div>
	</body>           
	<script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
	exit();
}

if($action=="payment_receive_list_view_popup")
{
	$data=explode('_',$data);
	if ($data[0]!=0) $company_name=" and company_id='$data[0]'"; else { echo "Please Select Company First."; die; }
	if ($data[1]!=0) $party_name=" and party_name='$data[1]'"; else $party_name="";
	//if ($data[2]!="" &&  $data[3]!="") $receipt_date = "and receipt_date between '".change_date_format($data[2], "mm-dd-yyyy", "/",1)."' and '".change_date_format($data[3], "mm-dd-yyyy", "/",1)."'"; else $receipt_date="";
	
	if ($data[4]!='') $rec_id_cond=" and prefix_no_num='$data[4]'"; else $rec_id_cond="";
	
	$company_id=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$party_arr=return_library_array( "select id,buyer_name from lib_buyer",'id','buyer_name');
	
	if($db_type==0)
	{
		$year_cond= "year(insert_date)as year";
		if ($data[2]!="" &&  $data[3]!="") $receipt_date = "and receipt_date between '".change_date_format($data[2], "yyyy-mm-dd")."' and '".change_date_format($data[3], "yyyy-mm-dd")."'"; else $receipt_date="";
	}
	else if($db_type==2)
	{
		$year_cond= "TO_CHAR(insert_date,'YYYY') as year";
		if ($data[2]!="" &&  $data[3]!="") $receipt_date = "and receipt_date between '".change_date_format($data[2], "mm-dd-yyyy", "/",1)."' and '".change_date_format($data[3], "mm-dd-yyyy", "/",1)."'"; else $receipt_date="";
	}
	$arr=array (2=>$party_arr,3=>$production_process,5=>$instrument_payment,6=>$bill_for);
	
	$sql= "select id, receive_no, prefix_no_num, $year_cond, company_id, party_name, bill_type, receipt_date, instrument_id, net_amount from subcon_payment_receive_mst where status_active=1 and is_deleted=0 $company_name $party_name $receipt_date $rec_id_cond order by id DESC";
	
	echo  create_list_view("list_view", "Receive No,Year,Party,Process,Receipt Data,Instrument Id,Net Amount", "70,70,100,100,100,100,150","690","250",0, $sql , "js_set_value", "id", "", 1, "0,0,party_name,bill_type,0,instrument_id,0", $arr , "prefix_no_num,year,party_name,bill_type,receipt_date,instrument_id,net_amount", "payment_receive_controller","",'0,0,0,0,3,0,2');
	exit();
}

if($action=="load_php_data_to_form_payment")
{
	$nameArray= sql_select("select id, receive_no, company_id, party_name, bill_type, payment_type, receipt_date, instrument_id, currency_id, net_amount, adjustment_type, adjusted_amount, bank_name, instrument_date, instrument_no, clearance_method, advance_amount, is_posted_account, remarks from subcon_payment_receive_mst where id='$data'");
	foreach ($nameArray as $row)
	{	
		echo "document.getElementById('txt_system_id').value 						= '".$row[csf("receive_no")]."';\n";
		echo "document.getElementById('cbo_company_id').value 						= '".$row[csf("company_id")]."';\n";
		echo "load_drop_down( 'requires/payment_receive_controller', document.getElementById('cbo_company_id').value, 'load_drop_down_party_name', 'party_td' );\n";
		echo "document.getElementById('cbo_party_name').value						= '".$row[csf("party_name")]."';\n";
		echo "document.getElementById('cbo_bill_type').value						= '".$row[csf("bill_type")]."';\n";
		echo "document.getElementById('cbo_payment_type').value						= '".$row[csf("payment_type")]."';\n";  
		echo "document.getElementById('txt_receipt_date').value 					= '".change_date_format($row[csf("receipt_date")])."';\n";   
		echo "document.getElementById('cbo_instrument').value						= '".$row[csf("instrument_id")]."';\n"; 
		echo "document.getElementById('cbo_currency').value							= '".$row[csf("currency_id")]."';\n";
		echo "document.getElementById('text_net_amount').value						= '".$row[csf("net_amount")]."';\n";
		echo "document.getElementById('cbo_adjustment_type').value					= '".$row[csf("adjustment_type")]."';\n";
		echo "document.getElementById('text_adjust_amount').value					= '".$row[csf("adjusted_amount")]."';\n";
		echo "document.getElementById('text_bank_name').value						= '".$row[csf("bank_name")]."';\n"; 
		echo "document.getElementById('text_instrument_date').value					= '".$row[csf("instrument_date")]."';\n"; 
		echo "document.getElementById('text_instrument_no').value					= '".$row[csf("instrument_no")]."';\n";
		echo "document.getElementById('cbo_clearance_method').value					= '".$row[csf("clearance_method")]."';\n"; 
		echo "document.getElementById('text_advance_amount').value					= '".$row[csf("advance_amount")]."';\n"; 
		echo "document.getElementById('text_remarks').value							= '".$row[csf("remarks")]."';\n"; 
		echo "document.getElementById('hidden_acc_integ').value						= '".$row[csf("is_posted_account")]."';\n";
		if($row[csf("is_posted_account")]==1)
		{
			echo "$('#accounting_integration_div').text('All Ready Internal Bill Raised');\n"; 
		}
		else
		{
			echo "$('#accounting_integration_div').text('');\n"; 
		}
	    echo "document.getElementById('update_id').value            				= '".$row[csf("id")]."';\n";
		echo "advance_disable(document.getElementById('cbo_payment_type').value);\n";
	}
	exit();
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	if ($operation==0)   // Insert Here========================================================================================delivery_id
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		if(str_replace("'",'',$cbo_payment_type)!=2)
		{
			if (is_duplicate_field( "master_id", "subcon_payment_receive_dtls", "master_id=$update_id" )==1)
			{
				echo "11**".str_replace("'",'',$update_id)."**".str_replace("'",'',$txt_system_id);
                disconnect($con); 
				die;			
			}
		}
		if($db_type==0) $year_cond=" and YEAR(insert_date)"; else if($db_type==2) $year_cond=" and TO_CHAR(insert_date,'YYYY')";	
		
		$new_system_id=explode("*",return_mrr_number( str_replace("'","",$cbo_company_id), '', '', date("Y",time()), 5, "select prefix_no, prefix_no_num from subcon_payment_receive_mst where company_id=$cbo_company_id $year_cond=".date('Y',time())." order by id desc ", "prefix_no", "prefix_no_num"));
		if(str_replace("'",'',$update_id)=="")
		{
			$id=return_next_id( "id", "subcon_payment_receive_mst", 1 ) ; 	
			$field_array="id, prefix_no, prefix_no_num, receive_no, company_id, party_name, bill_type, payment_type, receipt_date, instrument_id, currency_id, net_amount, adjustment_type, adjusted_amount, bank_name, instrument_date, instrument_no, clearance_method, advance_amount, remarks, inserted_by, insert_date";
			$data_array="(".$id.",'".$new_system_id[1]."','".$new_system_id[2]."','".$new_system_id[0]."',".$cbo_company_id.",".$cbo_party_name.",".$cbo_bill_type.",".$cbo_payment_type.",".$txt_receipt_date.",".$cbo_instrument.",".$cbo_currency.",".$text_net_amount.",".$cbo_adjustment_type.",".$text_adjust_amount.",".$text_bank_name.",".$text_instrument_date.",".$text_instrument_no.",".$cbo_clearance_method.",".$text_advance_amount.",".$text_remarks.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')"; 
			//echo "INSERT INTO subcon_payment_receive_mst(".$field_array.") VALUES ".$data_array; 
			$return_no=$new_system_id[0];
		}
		else
		{
			$id=str_replace("'",'',$update_id);
			$field_array="receive_no*company_id*party_name*bill_type*payment_type*receipt_date*instrument_id*currency_id*net_amount*adjustment_type*adjusted_amount*bank_name*instrument_date*instrument_no*clearance_method*advance_amount*remarks*updated_by*update_date";
			$data_array="".$txt_system_id."*".$cbo_company_id."*".$cbo_party_name."*".$cbo_bill_type."*".$cbo_payment_type."*".$txt_receipt_date."*".$cbo_instrument."*".$cbo_currency."*".$text_net_amount."*".$cbo_adjustment_type."*".$text_adjust_amount."*".$text_bank_name."*".$text_instrument_date."*".$text_instrument_no."*".$cbo_clearance_method."*".$text_advance_amount."*".$text_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'"; 
			
			$return_no=str_replace("'",'',$txt_system_id);
		}
		
		if(str_replace("'",'',$cbo_payment_type)!=2)
		{
			$id1=return_next_id( "id", "subcon_payment_receive_dtls",1);
			$field_array1 ="id, master_id, bill_type, bill_id, bill_no, bill_date, currency_id, bill_amount, total_adjusted, payment_balance, exchenge_rate, exchenge_amount, inserted_by, insert_date"; 
			$add_comma=0;
			for($i=1; $i<=$tot_row; $i++)
			{
				$bill_type="processid_".$i;
				$billid="billid_".$i;
				$bill_no="billno_".$i;
				$bill_date="billdate_".$i;
				$currencyid="currencyid_".$i;
				$bill_amount="billamount_".$i;
				$total_adjustment="currentpayment_".$i;
				$latest_balance="latestbalance_".$i;
				$ex_rate="exchengerate_".$i;
				$ex_amount="exchengamount_".$i;
				$updateid_dtls="updateiddtls_".$i;
				
				if(str_replace("'",'',$$updateid_dtls)=="")  
				{
					if(str_replace("'",'',$$total_adjustment)!=="")  
					{
						if ($add_comma!=0) $data_array1 .=",";
						$data_array1 .="(".$id1.",".$id.",".$$bill_type.",".$$billid.",".$$bill_no.",".$$bill_date.",".$$currencyid.",".$$bill_amount.",".$$total_adjustment.",".$$latest_balance.",".$$ex_rate.",".$$ex_amount.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
						
						$id1=$id1+1;
						$add_comma++;
					}
				}
			}
		}
		
		$flag=1;
		if(str_replace("'",'',$update_id)=="")
		{
			$rID=sql_insert("subcon_payment_receive_mst",$field_array,$data_array,0);
			if($rID==1 && $flag==1) $flag=1; else $flag=0;
		}
		else
		{
			$rID=sql_update("subcon_payment_receive_mst",$field_array,$data_array,"id",$update_id,0);
			if($rID==1 && $flag==1) $flag=1; else $flag=0;
		}
		
		if(str_replace("'",'',$cbo_payment_type)!=2)
		{
			if($data_array1!="")
			{
				//echo "insert into subcon_payment_receive_dtls (".$field_array1.") values ".$data_array1;
				$rID1=sql_insert("subcon_payment_receive_dtls",$field_array1,$data_array1,1);
				if($rID1==1 && $flag==1) $flag=1; else $flag=0;
			}
		}
		
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
		}
		else if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
		}	
		
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here=============================================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$id=str_replace("'",'',$update_id);
		$posted_account = return_field_value("is_posted_account","subcon_payment_receive_mst","id='$id'");
		
		if($posted_account==1)
		{
			echo "14**All Ready Posted in Accounting.";
            disconnect($con);
			exit();
		}
		$field_array="receive_no*company_id*party_name*bill_type*payment_type*receipt_date*instrument_id*currency_id*net_amount*adjustment_type*adjusted_amount*bank_name*instrument_date*instrument_no*clearance_method*advance_amount*remarks*updated_by*update_date";
		$data_array="".$txt_system_id."*".$cbo_company_id."*".$cbo_party_name."*".$cbo_bill_type."*".$cbo_payment_type."*".$txt_receipt_date."*".$cbo_instrument."*".$cbo_currency."*".$text_net_amount."*".$cbo_adjustment_type."*".$text_adjust_amount."*".$text_bank_name."*".$text_instrument_date."*".$text_instrument_no."*".$cbo_clearance_method."*".$text_advance_amount."*".$text_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$return_no=str_replace("'",'',$txt_system_id);
		
		if(str_replace("'",'',$cbo_payment_type)!=2)
		{
			$id1=return_next_id( "id", "subcon_payment_receive_dtls",1);
			$field_array1 ="id, master_id, bill_type, bill_id, bill_no, bill_date, currency_id, bill_amount, total_adjusted, payment_balance, exchenge_rate, exchenge_amount, inserted_by, insert_date"; 
			$field_array_up ="bill_type*bill_id*bill_no*bill_date*currency_id*bill_amount*total_adjusted*payment_balance*exchenge_rate*exchenge_amount*updated_by*update_date";
			$add_comma=0;
			for($i=1; $i<=$tot_row; $i++)
			{
				$bill_type="processid_".$i;
				$billid="billid_".$i;
				$bill_no="billno_".$i;
				$bill_date="billdate_".$i;
				$currencyid="currencyid_".$i;
				$bill_amount="billamount_".$i;
				$total_adjustment="currentpayment_".$i;
				$latest_balance="latestbalance_".$i;
				$ex_rate="exchengerate_".$i;
				$ex_amount="exchengamount_".$i;
				$updateid_dtls="updateiddtls_".$i;
					
				if(str_replace("'",'',$$updateid_dtls)=="")  
				{ 
					if ($add_comma!=0) $data_array1 .=",";
					$data_array1 .="(".$id1.",".$id.",".$$bill_type.",".$$billid.",".$$bill_no.",".$$bill_date.",".$$currencyid.",".$$bill_amount.",".$$total_adjustment.",".$$latest_balance.",".$$ex_rate.",".$$ex_amount.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
					$id1=$id1+1;
					$add_comma++;
				}
				else
				{
					$id_arr[]=str_replace("'",'',$$updateid_dtls);
					$data_array_up[str_replace("'",'',$$updateid_dtls)] =explode("*",("".$$bill_type."*".$$billid."*".$$bill_no."*".$$bill_date."*".$$currencyid."*".$$bill_amount."*".$$total_adjustment."*".$$latest_balance."*".$$ex_rate."*".$$ex_amount."*'".$_SESSION['logic_erp']['user_id']."'*'".$pc_date_time."'"));
				}
			}
		}
		
		$sql_dtls="Select id from subcon_payment_receive_dtls where master_id=$update_id and status_active=1 and is_deleted=0";
		$nameArray=sql_select( $sql_dtls );
		foreach($nameArray as $row)
		{
			$dtls_update_id_array[]=$row[csf('id')];
		}
		$flag=1;
		$rID=sql_update("subcon_payment_receive_mst",$field_array,$data_array,"id",$update_id,1);
		if($rID==1 && $flag==1) $flag=1; else $flag=0;
		
		if(str_replace("'",'',$cbo_payment_type)!=2)
		{
			if($data_array_up!="")
			{
				$rID1=execute_query(bulk_update_sql_statement("subcon_payment_receive_dtls", "id",$field_array_up,$data_array_up,$id_arr ));
				if($rID1==1 && $flag==1) $flag=1; else $flag=0;
			}
			if($data_array1!="")
			{
				//echo "10**insert into subcon_payment_receive_dtls (".$field_array1.") values ".$data_array1;die;
				$rID2=sql_insert("subcon_payment_receive_dtls",$field_array1,$data_array1,0);
				if($rID2==1 && $flag==1) $flag=1; else $flag=0;
			}
			
			if(implode(',',$id_arr)!="")
			{
				$distance_delete_id=implode(',',array_diff($dtls_update_id_array,$id_arr));
			}
			else
			{
				$distance_delete_id=implode(',',$dtls_update_id_array);
			}
			if(str_replace("'",'',$distance_delete_id)!="")
			{
				$rID3=execute_query( "delete from subcon_payment_receive_dtls where id in ($distance_delete_id)",0);
				if($rID3==1 && $flag==1) $flag=1; else $flag=0;
			}
		}
		//echo "10**".$rID.'--'.$rID1.'--'.$rID2.'--'.$rID3.'--'.$flag; die;
			
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "1**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
		}
		else if($db_type==2)
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
			else
			{
				oci_rollback($con);
				echo "10**".str_replace("'",'',$id)."**".str_replace("'",'',$return_no);
			}
		}		
		
		disconnect($con);
		die;
	}
	else if ($operation==2)  //Delete here======================================================================================
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
	}
}

if($action=="money_receipt_print")
{
	extract($_REQUEST);
	$exdata=explode('*',$data);
	$company=$exdata[0];
	$company_library=return_library_array( "select id, company_name from lib_company", "id", "company_name");
	$buyer_library=return_library_array( "select id, buyer_name from lib_buyer", "id", "buyer_name");
	$bill_prefix_no=return_library_array( "select bill_no, prefix_no_num from  subcon_inbound_bill_mst", "bill_no","prefix_no_num");
	$imge_arr=return_library_array( "select master_tble_id,image_location from common_photo_library",'master_tble_id','image_location');
	
	$sql_mst="Select receive_no, receipt_date, party_name, payment_type, instrument_id, net_amount, adjustment_type, adjusted_amount, advance_amount, currency_id, instrument_no from subcon_payment_receive_mst where company_id=$exdata[0] and id='$exdata[1]' and status_active=1 and is_deleted=0";
	
	$dataArray=sql_select($sql_mst);
	?>
	<style type="text/css">
    .bgimg {
		background:url(../<? echo $imge_arr[str_replace("'","",$company)]; ?>);
		z-index:-10;
		opacity:0.5;
    }
    </style> 
    <div style="width:1000px">  
        <div style="background:url(../images/header_logo.png);">
            <table width="100%" cellpadding="0" cellspacing="0" >
                <tr>
                    <td>
                        <table width="100%" cellpadding="0" cellspacing="0" >
                            <tr>
                                <td width="70" align="right" valign="top"> 
                                    <img src='../<? echo $imge_arr[str_replace("'","",$company)]; ?>' height='70%' width='70%' />
                                </td>
                                <td>
                                    <table width="380" cellspacing="0" align="left">
                                        <tr>
                                            <td align="left" style="font-size:18px"><strong ><? echo $company_library[$company]; ?></strong></td>
                                        </tr>
                                        <tr class="form_caption">
                                            <td align="left" style="font-size:10px">  
                                                <?
                                                $nameArray=sql_select( "select plot_no, level_no, road_no, block_no, contact_no, city, email, website from lib_company where id=$company and status_active=1 and is_deleted=0"); 
                                                foreach ($nameArray as $result)
                                                { 
                                                    ?>
                                                    <? echo $result[csf('plot_no')]; ?>
                                                    <? echo $result[csf('level_no')]; ?>
                                                    <? echo $result[csf('road_no')]; ?> &nbsp; 
                                                    <? echo $result[csf('block_no')];?> &nbsp; 
                                                    <? echo $result[csf('city')]; ?>&nbsp;  
                                                    <? echo $result[csf('contact_no')]; ?>  <br>
                                                    <? echo $result[csf('email')]; 
                                                }
                                                ?> 
                                            </td>  
                                        </tr>
                                        <tr>
                                            <td align="center" style="font-size:12px"><strong>MONEY RECEIPT</strong> <i>(Office Copy)</i></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                        <br>
                        <table width="450" cellspacing="0" align="left">
                            <thead style="font-size:12px">
                                <th width="30">No:</th>
                                <th width="150" align="left"><? echo $dataArray[0][csf('receive_no')]; ?></th>
                                <th width="40">Date:</th>
                                <th width="100" align="left"><? echo change_date_format($dataArray[0][csf('receipt_date')]); ?></th>
                            </thead>
                            <tbody style=" background-color:#EEEEEE;">
                            	<tr><td colspan="4"><strong><hr></hr></strong></td></tr>
                                <tr><td colspan="4" style="font-size:12px"><strong>Received with thanks from : </strong> <font style="font-family:'Comic Sans MS', cursive"> <i> <? echo $buyer_library[$dataArray[0][csf('party_name')]]; ?></i></font></td></tr>
                                <tr><td colspan="4" style="font-size:12px"><strong>In word: </strong> <font style="font-family:'Comic Sans MS', cursive"> <i> 
                                <?
								if($dataArray[0][csf('payment_type')]!=2)
								{
                                    if($dataArray[0][csf('instrument_id')]==1 || $dataArray[0][csf('instrument_id')]==2 || $dataArray[0][csf('instrument_id')]==3)
                                    {
										$amount=$dataArray[0][csf('net_amount')]+$dataArray[0][csf('advance_amount')];
										$total_amount=number_format($amount,2,'.',',');
                                        $format_total_amount=number_format($amount,2,'.','');
                                    }
                                    else if($dataArray[0][csf('instrument_id')]==4 || $dataArray[0][csf('instrument_id')]==5)
                                    {
										$total_amount=number_format($dataArray[0][csf('adjusted_amount')],2,'.',',');
                                        $format_total_amount=number_format($dataArray[0][csf('adjusted_amount')],2,'.','');
                                    }
								}
								else
								{
									$total_amount=number_format($dataArray[0][csf('advance_amount')],2,'.',',');
									$format_total_amount=number_format($dataArray[0][csf('advance_amount')],2,'.','');
								}
                                    $currency_type=$currency[$dataArray[0][csf('currency_id')]];
                                     
                                    if($dataArray[0][csf('currency_id')]==1)
                                    {
                                        $currency_unit="Paysha.";
                                    }
                                    else if($dataArray[0][csf('currency_id')]==2)
                                    {
                                        $currency_unit="Cent.";
                                    }
                                    else if($dataArray[0][csf('currency_id')]==3)
                                    {
                                        $currency_unit="Cent.";
                                    }
                                    echo number_to_words($format_total_amount,$currency_type,$currency_unit); 
                                ?></i></font></td></tr>
                                <tr><td colspan="4" style="font-size:12px"><strong><? echo 'Payment Type: '.$payment_type[$dataArray[0][csf('payment_type')]].'; '.$instrument_payment[$dataArray[0][csf('instrument_id')]]; ?> : </strong> <font style="font-family:'Comic Sans MS', cursive"> <i> 
                                <? 
                                    if ($dataArray[0][csf('instrument_id')]==2 || $dataArray[0][csf('instrument_id')]==3)
                                    {
                                        $instrument='No: '.$dataArray[0][csf('instrument_no')];
                                    }
                                    else if ($dataArray[0][csf('instrument_id')]==4 || $dataArray[0][csf('instrument_id')]==5)
                                    {
                                        $instrument=$adjustment_type[$dataArray[0][csf('adjustment_type')]];
                                    }
									else
									{
										$instrument='';
									}
                                    echo $instrument; 
                                ?></i></font></td></tr>
                                <tr><td colspan="4" style="font-size:12px"><strong>Adjusted Invoice No: </strong> <font style="font-family:'Comic Sans MS', cursive"> <i>
                                <?
                                    if($db_type==0)
                                    {
                                        $sql_inv="Select group_concat(bill_no) as bill_no from subcon_payment_receive_dtls where master_id='$exdata[1]' and status_active=1 and is_deleted=0";
                                    }
                                    else if($db_type==2)
                                    {
                                        $sql_inv="Select listagg((cast(bill_no as varchar2(4000))),',') within group (order by bill_no) as bill_no from subcon_payment_receive_dtls where master_id='$exdata[1]' and status_active=1 and is_deleted=0";
            
                                    }
                                    $dtlsArray=sql_select($sql_inv);
                                    $inv_no=array_unique(explode(",",$dtlsArray[0][csf('bill_no')]));
                                    $invoice_no='';
                                    foreach($inv_no as $val)
                                    {
                                        if($invoice_no=="") $invoice_no=$bill_prefix_no[$val]; else $invoice_no.=", ".$bill_prefix_no[$val];
                                    }
                                    echo $invoice_no; 
                                ?></i></font></td></tr>
                             </tbody>
                             <tfoot>
                                <tr><td colspan="4"><strong>&nbsp;</strong></td></tr>
                                <tr>
                                    <td colspan="2">
                                        <table cellspacing="0" width="200"  border="1" rules="all" class="rpt_table" >
                                            <tr>
                                                <td width="60"><strong>Amount: </strong></td>
                                                <td width="140"><font style="font-family:'Comic Sans MS', cursive"> <i> <? echo $total_amount; ?>/=</i></font></td>
                                            </tr>
                                        </table>
                                   </td>
                                   <td colspan="2" align="center" style="font-size:12px"><strong><hr>Received By</hr></strong></td>
                               </tr>
                            </tfoot>
                        </table>
                    </td>
                    <td width="20">&nbsp;</td>
                    <td>
                        <table width="100%" cellpadding="0" cellspacing="0" >
                            <tr>
                                <td width="70" align="right" valign="top"> 
                                    <img  src='../<? echo $imge_arr[str_replace("'","",$company)]; ?>' height='70%' width='70%' />
                                </td>
                                <td>
                                    <table width="380" cellspacing="0" align="left">
                                        <tr>
                                            <td align="left" style="font-size:18px"><strong ><? echo $company_library[$company]; ?></strong></td>
                                        </tr>
                                        <tr class="form_caption">
                                            <td align="left" style="font-size:10px">  
                                                <?
                                                $nameArray=sql_select( "select plot_no, level_no, road_no, block_no, contact_no, city, email, website, vat_number from lib_company where id=$company and status_active=1 and is_deleted=0"); 
                                                foreach ($nameArray as $result)
                                                { 
                                                    ?>
                                                    <? echo $result[csf('plot_no')]; ?>
                                                    <? echo $result[csf('level_no')]; ?>
                                                    <? echo $result[csf('road_no')]; ?> &nbsp; 
                                                    <? echo $result[csf('block_no')];?> &nbsp; 
                                                    <? echo $result[csf('city')]; ?>&nbsp; 
                                                    <? echo $result[csf('contact_no')]; ?>  <br> 
                                                    <? echo $result[csf('email')]; ?> <br>
                                                <b> Vat No : <? echo $result[csf('vat_number')]; ?></b> <?
                                                }
                                                ?> 
                                            </td>  
                                        </tr>
                                        <tr>
                                            <td align="center" style="font-size:12px"><strong>MONEY RECEIPT</strong> <i>(Customer Copy)</i></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                        <br>
                        <table width="450" cellspacing="0" align="left">
                            <thead style="font-size:12px" >
                                <th width="30">No:</th>
                                <th width="150" align="left"><? echo $dataArray[0][csf('receive_no')]; ?></th>
                                <th width="40">Date:</th>
                                <th width="100" align="left"><? echo change_date_format($dataArray[0][csf('receipt_date')]); ?></th>
                            </thead>
                            <tbody bgcolor="#EEEEEE">
                                <tr><td colspan="4"><strong><hr></hr></strong></td></tr>
                                <tr><td colspan="4" style="font-size:12px"><strong>Received with thanks from : </strong> <font style="font-family:'Comic Sans MS', cursive"> <i> <? echo $buyer_library[$dataArray[0][csf('party_name')]]; ?></i></font></td></tr>
                                <tr><td colspan="4" style="font-size:12px"><strong>In word: </strong> <font style="font-family:'Comic Sans MS', cursive"> <i> 
                                <?
								if($dataArray[0][csf('payment_type')]!=2)
								{
                                    if($dataArray[0][csf('instrument_id')]==1 || $dataArray[0][csf('instrument_id')]==2 || $dataArray[0][csf('instrument_id')]==3)
                                    {
										$amount=$dataArray[0][csf('net_amount')]+$dataArray[0][csf('advance_amount')];
										$total_amount=number_format($amount,2,'.',',');
                                        $format_total_amount=number_format($amount,2,'.','');
                                    }
                                    else if($dataArray[0][csf('instrument_id')]==4 || $dataArray[0][csf('instrument_id')]==5)
                                    {
										$total_amount=number_format($dataArray[0][csf('adjusted_amount')],2,'.',',');
                                        $format_total_amount=number_format($dataArray[0][csf('adjusted_amount')],2,'.','');
                                    }
								}
                                else
								{
									$total_amount=number_format($dataArray[0][csf('advance_amount')],2,'.',',');
									$format_total_amount=number_format($dataArray[0][csf('advance_amount')],2,'.','');
								}    
                                    $currency_type=$currency[$dataArray[0][csf('currency_id')]];
                                     
                                    if($dataArray[0][csf('currency_id')]==1)
                                    {
                                        $currency_unit="Paysha.";
                                    }
                                    else if($dataArray[0][csf('currency_id')]==2)
                                    {
                                        $currency_unit="Cent.";
                                    }
                                    else if($dataArray[0][csf('currency_id')]==3)
                                    {
                                        $currency_unit="Cent.";
                                    }
                                    echo number_to_words($format_total_amount,$currency_type,$currency_unit); 
                                ?></i></font></td></tr>
                                <tr><td colspan="4" style="font-size:12px"><strong><? echo 'Payment Type: '.$payment_type[$dataArray[0][csf('payment_type')]].'; '.$instrument_payment[$dataArray[0][csf('instrument_id')]]; ?> : </strong> <font style="font-family:'Comic Sans MS', cursive"> <i> 
                                <? 
                                    if ($dataArray[0][csf('instrument_id')]==2 || $dataArray[0][csf('instrument_id')]==3)
                                    {
                                        $instrument='No: '.$dataArray[0][csf('instrument_no')];
                                    }
                                    else if ($dataArray[0][csf('instrument_id')]==4 || $dataArray[0][csf('instrument_id')]==5)
                                    {
                                        $instrument=$adjustment_type[$dataArray[0][csf('adjustment_type')]];
                                    }
									else
									{
										$instrument='';
									}
                                    echo $instrument; 
                                ?></i></font></td></tr>
                                <tr><td colspan="4" style="font-size:12px"><strong>Adjusted Invoice No: </strong> <font style="font-family:'Comic Sans MS', cursive"> <i>
                                <?
                                    if($db_type==0)
                                    {
                                        $sql_inv="Select group_concat(bill_no) as bill_no from subcon_payment_receive_dtls where master_id='$exdata[1]' and status_active=1 and is_deleted=0";
                                    }
                                    else if($db_type==2)
                                    {
                                        $sql_inv="Select listagg((cast(bill_no as varchar2(4000))),',') within group (order by bill_no) as bill_no from subcon_payment_receive_dtls where master_id='$exdata[1]' and status_active=1 and is_deleted=0";
            
                                    }
                                    $dtlsArray=sql_select($sql_inv);
                                    $inv_no=array_unique(explode(",",$dtlsArray[0][csf('bill_no')]));
                                    $invoice_no='';
                                    foreach($inv_no as $val)
                                    {
                                        if($invoice_no=="") $invoice_no=$bill_prefix_no[$val]; else $invoice_no.=", ".$bill_prefix_no[$val];
                                    }
                                    echo $invoice_no; 
                                ?></i></font></td></tr>
                             </tbody>
                             <tfoot>
                                <tr><td colspan="4"><strong>&nbsp;</strong></td></tr>
                                <tr>
                                    <td colspan="2">
                                        <table cellspacing="0" width="200"  border="1" rules="all" class="rpt_table" >
                                            <tr>
                                                <td width="60"><strong>Amount: </strong></td>
                                                <td width="140"><font style="font-family:'Comic Sans MS', cursive"> <i> <? echo $total_amount; ?>/=</i></font></td>
                                            </tr>
                                        </table>
                                   </td>
                                   <td colspan="2" align="center" style="font-size:12px"><strong><hr>Authorized Signature</hr></strong></td>
                               </tr>
                            </tfoot>
                        </table>
                     </td>
                </tr>
            </table>
            <br>
       </div> 
   </div>
   <script src="../../includes/functions_bottom.js" type="text/javascript"></script>
	<?	
	exit();
}

if ($action=="load_unadjustmed_amount")
{
	//echo $data;
	$ex_data=explode('_',$data);
	$adjusted_sql=sql_select("select sum(adjusted_amount) as adjusted_amt from  subcon_payment_receive_mst where company_id='$ex_data[0]' and bill_type='$ex_data[1]' and party_name='$ex_data[2]' and payment_type='3' and instrument_id in (4,5) and adjustment_type=6 and status_active=1 and is_deleted=0");
	$sql=sql_select("select sum(advance_amount) as unadjusted_amt from  subcon_payment_receive_mst where company_id='$ex_data[0]' and bill_type='$ex_data[1]' and party_name='$ex_data[2]' and payment_type in (1,2) and status_active=1 and is_deleted=0");
	$unadjusted_balance=$sql[0][csf('unadjusted_amt')]-$adjusted_sql[0][csf('adjusted_amt')];
	echo "document.getElementById('text_unadj_advance').value			= '".$unadjusted_balance."';\n"; 
	exit();
}
?>
