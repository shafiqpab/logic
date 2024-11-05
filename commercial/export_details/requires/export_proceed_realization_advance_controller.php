<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

//--------------------------- Start-------------------------------------//

if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b,lib_buyer_party_type c where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and c.buyer_id=buy.id and c.party_type in (1,3,21,90) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
        //echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b,lib_buyer_party_type c where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond and c.buyer_id=buy.id and c.party_type='$data'  group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" );
	//echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' $buyer_cond group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $selected, "" ); // old
	exit();
}

if($action=="populate_acc_loan_no_data")
{
	$data=explode("**",$data);
	$acc_type=$data[0];
	$rowID=$data[1];
	$company_id=$data[2];
	$bank_id=$data[3];

	if($bank_id>0)
	{
		$sql="select account_no from lib_bank_account where account_type=$acc_type and company_id=$company_id and account_id=$bank_id and status_active=1 and is_deleted=0";
	}
	else
	{
		$sql="select account_no from lib_bank_account where account_type=$acc_type and company_id=$company_id and status_active=1 and is_deleted=0";
	}

	$nameArray=sql_select($sql);
	echo "$('#acLoanNo_".$rowID."').removeAttr('readonly');\n";
 	echo "$('#acLoanNo_".$rowID."').val('');\n";

	if (count($nameArray)>1)
	{
		echo "$('#acLoanNo_".$rowID."').attr('readonly','readonly');\n";
		echo "$('#acLoanNo_".$rowID."').val('');\n";
	}
	else
	{
		foreach($nameArray as $row)
		{
			echo "$('#acLoanNo_".$rowID."').attr('readonly','readonly');\n";
			echo "$('#acLoanNo_".$rowID."').val('".$row[csf("account_no")]."');\n";
		}
	}
	
	exit();
}

if($action=="commercial_head_popup")
{
	echo load_html_head_contents("Export Proceeds Realization Form", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(id,fld_val)
		{
			//alert(id+"="+fld_val);
			$('#hdn_head_id').val(id);
			$('#hdn_head_val').val(fld_val);
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center" style="width:360px;">
        <table cellpadding="0" cellspacing="0" width="360" class="rpt_table">
            <thead>
                <th width="50">SL</th>
                <th>Account Head
                <input type="hidden" id="hdn_head_id" />
                <input type="hidden" id="hdn_head_val" />
                </th>
                
            </thead>
		</table>
        <div style="width:360px; max-height:350px; overflow-y:scroll">
     	<table width="340" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" id="list_view">
        	<tbody>
            <?
            $i=1;
			unset($commercial_head[177]);
			foreach($commercial_head as $key=>$val)
			{
				if ($i%2==0)
                    $bgcolor="#FFFFFF";
                else
                    $bgcolor="#E9F3FF";
				?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer;" id="search<? echo $i;?>" onClick="js_set_value(<? echo $key.",'".$val."'"; ?>);" >
                    <td width="50" align="center"><?= $i++; ?></td>
                    <td><? echo $val; ?></td>
                </tr>
                <?
			}
			?>
            </tbody>
        </table>
        </div>
    </div>
    </body>
    <!--<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>-->
    <script>setFilterGrid('list_view',-1)</script>
    </html>
    <?
	exit();
}

if($action=="AccountNo_popup")
{
	echo load_html_head_contents("Account No Form", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	?>
	<script>
		function js_set_value(account_no)
		{
			//alert(id+"="+fld_val);
			$('#hdn_account_no').val(account_no);
			parent.emailwindow.hide();
		}
    </script>
    </head>
    <body>
    <div align="center" style="width:360px;">
        <table cellpadding="0" cellspacing="0" width="360" class="rpt_table">
            <thead>
                <th width="50">SL</th>
                <th>Account No</th>
                <input type="hidden" id="hdn_account_no" />
            </thead>
		</table>
        <div style="width:360px; max-height:250px; overflow-y:scroll">
     	<table width="340" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" id="list_view" align="left">
        	<tbody>
            <?
            $i=1;
			$lien_bank_cond="";
			if ($lien_bank != "") $lien_bank_cond=" and account_id=$lien_bank";
			$sql="select account_no as ACCOUNT_NO from lib_bank_account where account_type=$acheadval and company_id=$beneficiary_name  $lien_bank_cond and status_active=1 and is_deleted=0";
			$sql_res=sql_select($sql);			
			foreach($sql_res as $key=>$val)
			{
				if ($i%2==0)  $bgcolor="#FFFFFF";                   
                else  $bgcolor="#E9F3FF";                   
				?>
                <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer;" id="search<? echo $i;?>" onClick="js_set_value(<? echo "'".$val['ACCOUNT_NO']."'"; ?>);" >
                    <td width="50" align="center"><?= $i++; ?></td>
                    <td><? echo $val['ACCOUNT_NO']; ?></td>
                </tr>
                <?
			}
			?>
            </tbody>
        </table>
        </div>
    </div>
    </body>
    <!--<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>-->
    <script>setFilterGrid('list_view',-1)</script>
    </html>
    <?
	exit();
}


if($action=="invoice_bill_popup_search")
{
	echo load_html_head_contents("Export Proceeds Realization Form", "../../../", 1, 1,'','1','');
	extract($_REQUEST);
	?>
	<script>

		function js_set_value(id,type,lienBank,lc_id,lc_type)
		{
			$('#hidden_invoice_bill_id').val(id);
			$('#hidden_is_invoiceBill').val(type);
			$('#hidden_lc_id').val(lc_id);
			$('#hidden_lc_type').val(lc_type);
			$('#hidden_is_invoiceLienBank').val(lienBank);
			parent.emailwindow.hide();
		}

    </script>

</head>

<body>
<div align="center" style="width:740px;">
	<form name="searchexportinformationfrm"  id="searchexportinformationfrm">
		<fieldset style="width:720px;">
		<legend>Enter search words</legend>
            <table cellpadding="0" cellspacing="0" width="630" class="rpt_table">
                <thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th width="180" id="search_by_td_up">Enter SC No</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:100px" class="formbutton" />
                        <input type="hidden" name="hidden_invoice_bill_id" id="hidden_invoice_bill_id" value="" />
                        <input type="hidden" name="hidden_is_invoiceBill" id="hidden_is_invoiceBill" value="" />
                        <input type="hidden" name="hidden_lc_id" id="hidden_lc_id" value="" />
                        <input type="hidden" name="hidden_lc_type" id="hidden_lc_type" value="" />
                        <input type="hidden" name="hidden_is_invoiceLienBank" id="hidden_is_invoiceLienBank" value="" />
                        <input type="hidden" name="import_btb" id="import_btb" value="<? echo $import_btb?>" />
                    </th>
                </thead>
                <tr class="general">
                    <td>
                        <?
						//beneficiary_name
						//echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy,  lib_buyer_tag_company c,lib_buyer_party_type b where buy.id=c.buyer_id and buy.status_active =1 and buy.is_deleted=0 and c.tag_company=$beneficiary_name  $buyer_cond and b.buyer_id=buy.id and b.party_type='$beneficiary_name' order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $buyerID, "",0 );
						if($import_btb == 1)
						{
							echo create_drop_down( "cbo_buyer_name", 152, "select comp.id, comp.company_name from lib_company comp where comp.status_active=1 and comp.is_deleted=0 $company_cond order by comp.company_name","id,company_name", 1, "-- Select Beneficiary --", $buyerID, "" );
						}else
						{
							echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b,lib_buyer_party_type c where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$beneficiary_name' $buyer_cond and c.buyer_id=buy.id and c.party_type in (1,3,21,90) group by buy.id, buy.buyer_name order by buy.buyer_name","id,buyer_name", 1, "-- Select Buyer --", $buyerID, "" );
						}

						//echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy,  lib_buyer_tag_company c where buy.id=c.buyer_id and buy.status_active =1 and buy.is_deleted=0 and c.tag_company=$beneficiary_name  $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $buyerID, "",0 ); // old

                        ?>
                    </td>
                    <td>
                        <?
                            $arr=array(1=>'SC No');
                        	//$arr=array(1=>'Bill No');
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../../') ";
							echo create_drop_down( "cbo_search_by", 150, $arr,"",0, "--Select--", "",$dd,0 );
                        ?>
                    </td>
                    <td id="search_by_td">
                        <input type="text" style="width:130px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                    </td>
                     <td>
                        <input type="button" id="search_button" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'**'+<? echo $beneficiary_name; ?>+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('import_btb').value, 'invoice_bill_search_list_view', 'search_div', 'export_proceed_realization_advance_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:100px;" />
                     </td>
                </tr>
           </table>
            <div style="width:100%; margin-top:10px" id="search_div" align="left"></div>
		</fieldset>
	</form>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
	exit();
}

if($action=="invoice_bill_search_list_view")
{
	$data=explode('**',$data);
	if($data[0]==0) $buyer_id="%%"; else $buyer_id=$data[0];
	$company_id=$data[1];
	$search_by=$data[2];
	$search_text=trim($data[3]);
	$import_btb=$data[4];
	$import_btb_cond = "";
	if($import_btb != "")
	{
		if($search_by == 1 || $search_by == 2)
		{
			$import_btb_cond .= " and a.import_btb in ($import_btb)";
		}else
		{
			$import_btb_cond .= "and c.import_btb in ($import_btb)";
		}
	}

	$search_cond= "";
	if($search_text != "")
	{
		$search_cond .= " and c.contract_no like '%".$search_text."%'";
	}
	
	$sql_inv = "select listagg(cast(b.id as varchar(4000)),',') within group (order by b.id) as INV_ID, b.lc_sc_id as LC_SC_ID
	from com_export_invoice_ship_mst b, com_sales_contract c 
	where b.lc_sc_id = c.id and b.is_lc=2 and c.pay_term = 3 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.benificiary_id=$company_id and b.buyer_id like '$buyer_id' $search_cond $import_btb_cond
	group by b.lc_sc_id";
	//echo $sql_inv;die;
	$sql_inv_result=sql_select($sql_inv);
	$sc_inv_data=array();
	foreach($sql_inv_result as $row)
	{
		$sc_inv_data[$row["LC_SC_ID"]]=$row["INV_ID"];
	}
	
	
	$prev_realized=sql_select("select a.invoice_bill_id, a.is_invoice_bill, sum(b.document_currency) as document_currency 
	from com_export_proceed_realization a, com_export_proceed_rlzn_dtls b 
	where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.is_invoice_bill in(2,3)
	group by a.invoice_bill_id, a.is_invoice_bill");
	$prev_rlz_data=array();
	foreach($prev_realized as $row)
	{
		$prev_rlz_data[$row[csf("is_invoice_bill")]][$row[csf("invoice_bill_id")]]["invoice_bill_id"]=$row[csf("invoice_bill_id")];
		$prev_rlz_data[$row[csf("is_invoice_bill")]][$row[csf("invoice_bill_id")]]["document_currency"]=$row[csf("document_currency")];
	}
	
	//and a.id not in (select invoice_bill_id from com_export_proceed_realization where is_invoice_bill=1 and status_active=1 and is_deleted=0)
	//and c.id not in(select invoice_bill_id from com_export_proceed_realization where is_invoice_bill=2 and status_active=1 and is_deleted=0)
	// and a.id not in (select invoice_bill_id from com_export_proceed_realization where is_invoice_bill=1 and status_active=1 and is_deleted=0) 
	//and c.id not in(select invoice_bill_id from com_export_proceed_realization where is_invoice_bill=2 and status_active=1 and is_deleted=0)
	
	

	//echo $sql;die;
	$buyer_sql="select id, buyer_name, is_partial_rlz from lib_buyer";
	$buyer_sql_result=sql_select($buyer_sql);
	foreach($buyer_sql_result as $row)
	{
		$buyer_arr[$row[csf("id")]]=$row[csf("buyer_name")];
		$buyer_partial_rlz[$row[csf("id")]]=$row[csf("is_partial_rlz")];
	}
	unset($buyer_sql_result);
	$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	
	$sql = "select c.id as lc_sc_id, c.beneficiary_name as company_id, c.buyer_name as buyer_id, c.contract_no as bank_ref_no, c.contract_date as bank_ref_date, 0 as import_btb, 0 as submit_type, 0 as invoice_id, 2 as is_lc, 3 as type, c.lien_bank, c.contract_value as bill_value 
	from com_sales_contract c 
	where c.pay_term = 3 and c.status_active=1 and c.is_deleted=0 and c.beneficiary_name=$company_id and c.buyer_name like '$buyer_id' $search_cond  
	order by c.id";
	?>
	<table width="700" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all">
        <thead>
            <th width="50">SL</th>
            <th width="150">SC No</th>
            <th width="80">SC Date</th>
            <th width="150">Beneficiary</th>
            <th width="150">Buyer</th>
            <th>Negotiated Amnt</th>
        </thead>
	</table>
    <div style="width:720px; max-height:270px; overflow-y:scroll">
     	<table width="700" cellpadding="0" cellspacing="0" border="1" class="rpt_table" rules="all" id="list_view">
		<?
			$data_array=sql_select($sql);
            $i = 1;
            foreach($data_array as $row)
            {
                if ($i%2==0)
                    $bgcolor="#FFFFFF";
                else
                    $bgcolor="#E9F3FF";
				
				$sc_inv_rlz_amt=0;
				$sc_inv_id_arr = array_unique(explode(",",$sc_inv_data[$row[csf('lc_sc_id')]]));
				foreach($sc_inv_id_arr as $inv_id)
				{
					$sc_inv_rlz_amt+=$prev_rlz_data[2][$inv_id]["document_currency"];
				}
				$pending_value=($row[csf("bill_value")]-($prev_rlz_data[3][$row[csf("id")]]["document_currency"]+$sc_inv_rlz_amt));
				//$buyer_partial_rlz_permision=$buyer_partial_rlz[$row[csf('buyer_id')]];
				//if($print_cond==1 && $prev_rlz_data[$row[csf('type')]][$row[csf("id")]]=="")
				if($pending_value>0)
				{
					$lc_sc_no=implode(",",array_unique(explode(",",$lc_sc_no)));
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer;" id="search<? echo $i;?>" onClick="js_set_value(<? echo $row[csf('lc_sc_id')].','.$row[csf('type')].','.$row[csf('lien_bank')].','.$row[csf('lc_sc_id')].','.$row[csf('is_lc')]; ?>);" >
                    	<td width="50" align="center"><? echo $i; ?></td>
                        <td width="150"><p><? echo $row[csf('bank_ref_no')]; ?>&nbsp;</p></td>
                        <td width="80" align="center"><? echo change_date_format($row[csf('bank_ref_date')]); ?>&nbsp;</td>
                        <td width="150"><? echo $comp[$row[csf('company_id')]]; ?></td>
                        <? if($row[csf('import_btb')] == 1) $buyer = $comp[$row[csf('buyer_id')]]; else $buyer =$buyer_arr[$row[csf('buyer_id')]];?>
                        <td width="150" title="<? echo $sc_inv_data[$row[csf('lc_sc_id')]]; ?>"><? echo $buyer ?></td>
                        <td align="right"><? echo number_format($pending_value,2); ?>&nbsp;</td>
                    </tr>
					<?
                    $i++;
				}
            }
			?>
		</table>
	</div>
	<?
	exit();
}

if ($action=="invoice_details_popup")
{
	extract($_REQUEST);
	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);

	$company_arr=return_library_array( "select id, company_name from lib_company", 'id', 'company_name'  );
	$sql="SELECT a.BANK_REF_NO, c.INVOICE_NO, c.INVOICE_DATE, c.INVOICE_QUANTITY, c.NET_INVO_VALUE from com_export_doc_submission_mst a, com_export_doc_submission_invo b, com_export_invoice_ship_mst c 
	where a.id=b.doc_submission_mst_id and b.invoice_id=c.id and a.company_id=$beneficiary_name and a.buyer_id=$buyerID and a.id=$invoice_bill_id and a.entry_form=40 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0";
	$sql_res=sql_select($sql);
	?>
	<table cellpadding="0" cellspacing="0" width="500">
		<tr>
		   <td align="center" width="100%"><strong style="font-size:16px"><?= $company_arr[$beneficiary_name]; ?></strong></td>
		</tr>
	</table>
	<table width="500" style="margin: 0px auto; font-weight: bold;" cellspacing="0" cellpadding="0" class="rpt_table" align="left" border="1" rules="all">			
		<thead>
			<tr>
				<th width="200" colspan="2">Bil Number</th>
				<th width="300" colspan="3"><?= $sql_res[0]['BANK_REF_NO']; ?></th>
			</tr>
			<tr>
				<th width="50">SL</th>
				<th width="150">Invoice No</th>
				<th width="100">Invoice Date</th>
				<th width="100">Invoice Qty</th>
				<th width="100">Invoice Value</th>
			</tr>
		</thead>
	</table>
	<div style="width:520px; max-height:280px;overflow-y:scroll;">
		<table cellspacing="0" cellpadding="0" border="0" rules="all" width="500" class="rpt_table" id="tbl_search_list2">
			<tbody>		
				<?
				$i=1;
				$tot_invoice_qty=0;
				$tot_invoice_value=0;
				foreach($sql_res as $row)
				{
					?>
					<tr>
						<td width="50" align="center"><?= $i; ?></td>
						<td width="150" align="center"><p><?= $row['INVOICE_NO']; ?></p></td>
						<td width="100" align="center"><p><?= change_date_format($row['INVOICE_DATE']); ?></td>
						<td width="100" align="right"><p><?= $row['INVOICE_QUANTITY']; ?></td>
						<td width="100" align="right"><p><?= number_format($row['NET_INVO_VALUE'],2); ?></td>
					</tr>
					<?
					$i++;
					$tot_invoice_qty += $row['INVOICE_QUANTITY'];
					$tot_invoice_value += $row['NET_INVO_VALUE'];
				}				
				?>
			</tbody>
			<tfoot>
				<tr>
					<th width="300" colspan="3" align="right">Grand Total</th>
					<th width="100" align="right"><?= $tot_invoice_qty; ?></th>
					<th width="100" align="right"><?= number_format($tot_invoice_value,2); ?></th>
				</tr>
			</tfoot>
		</table>
	</div>
	<script type="text/javascript">
		setFilterGrid("tbl_search_list2",-1);
	</script>

	<?
	exit();
}

if($action=="populate_data_from_invoice_bill")
{
	$data=explode("**",$data);
	$invoice_bill_id=$data[0];
	$is_invoiceBill=$data[1];
	$realization_id=$data[2];
	$lc_id=$data[3];
	$lc_type=$data[4];
	//echo $lc_id."__".$lc_type."__".$realization_id;die;
	$sql_inv = "select listagg(cast(b.id as varchar(4000)),',') within group (order by b.id) as INV_ID
	from com_export_invoice_ship_mst b, com_sales_contract c 
	where b.lc_sc_id = c.id and b.is_lc=2 and c.pay_term = 3 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and b.lc_sc_id=$invoice_bill_id";
	//echo $sql_inv;die;
	$sql_inv_result=sql_select($sql_inv);
	foreach($sql_inv_result as $row)
	{
		$sc_inv_id=$row["INV_ID"];
	}
	$rlz_cond="";
	if($realization_id) $rlz_cond=" and id <> $realization_id";
	$prev_realized=sql_select("select a.invoice_bill_id, b.id as dtls_id, b.document_currency 
	from com_export_proceed_realization a, com_export_proceed_rlzn_dtls b 
	where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.is_invoice_bill in(3) and a.invoice_bill_id=$invoice_bill_id $rlz_cond
	union all
	select a.invoice_bill_id, b.id as dtls_id, b.document_currency 
	from com_export_proceed_realization a, com_export_proceed_rlzn_dtls b 
	where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.is_invoice_bill in(2) and b.ACCOUNT_HEAD<>177 and a.invoice_bill_id in($sc_inv_id)");
	$prev_rlz_data=array();
	foreach($prev_realized as $row)
	{
		$prev_rlz_value+=$row[csf("document_currency")];
	}
	
	//$prev_realized=sql_select("select a.invoice_bill_id, sum(b.document_currency) as document_currency 
//	from com_export_proceed_realization a, com_export_proceed_rlzn_dtls b 
//	where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.invoice_bill_id=$invoice_bill_id and a.is_invoice_bill=1 and a.id <> $realization_id
//	group by a.invoice_bill_id");
//	$prev_rlz_data=array();
//	foreach($prev_realized as $row)
//	{
//		$prev_rlz_value+=$row[csf("document_currency")];
//	}
	
	//if($is_invoiceBill==1)
//	{
//		if($db_type==0)
//		{
//			$sql = "select a.bank_ref_no, a.bank_ref_date, a.buyer_id, a.submit_type, group_concat(distinct(b.invoice_id)) as invoice_id, sum(b.net_invo_value) as bill_amnt,a.import_btb,a.lien_bank,b.is_lc,b.lc_sc_id 
//			from com_export_doc_submission_mst a, com_export_doc_submission_invo b 
//			where a.id=b.doc_submission_mst_id and a.id=$invoice_bill_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.bank_ref_no, a.bank_ref_date, a.buyer_id, a.submit_type,a.import_btb,a.lien_bank,b.is_lc,b.lc_sc_id";
//		}
//		else
//		{
//			$sql = "select a.bank_ref_no, a.bank_ref_date, a.buyer_id, a.submit_type, rtrim(xmlagg(xmlelement(e,b.invoice_id,',').extract('//text()') order by b.invoice_id).GetClobVal(),',') as invoice_id, sum(b.net_invo_value) as bill_amnt,a.import_btb,a.lien_bank,b.is_lc,b.lc_sc_id 
//			from com_export_doc_submission_mst a, com_export_doc_submission_invo b 
//			where a.id=b.doc_submission_mst_id and a.id=$invoice_bill_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 group by a.id, a.bank_ref_no, a.bank_ref_date, a.buyer_id,a.lien_bank, a.submit_type,a.import_btb,b.is_lc,b.lc_sc_id";
//		}
//	}
//	else
//	{
//		$sql = "select invoice_no as bank_ref_no, buyer_id, invoice_date as bank_ref_date, is_lc, lc_sc_id, net_invo_value as bill_amnt,import_btb from com_export_invoice_ship_mst where id=$invoice_bill_id and status_active=1 and is_deleted=0";
//	}

	$sql = "select c.contract_no as bank_ref_no, c.buyer_name as buyer_id, c.contract_date as bank_ref_date, 2 as is_lc, c.id as lc_sc_id, c.contract_value as bill_amnt, 0 as import_btb, 3 as submit_type, 3 as type, c.lien_bank, c.currency_name 
	from com_sales_contract c 
	where c.pay_term = 3 and c.status_active=1 and c.is_deleted=0 and c.id=$invoice_bill_id";
	//echo $sql;//die;
	//$sql_loan = "select a.id,a.pre_export_dtls_id,a.export_type,a.lc_sc_id,a.currency_id,a.amount,a.conversion_rate, b.loan_type, b.loan_number from com_pre_export_lc_wise_dtls a, com_pre_export_finance_dtls b, com_pre_export_finance_mst c where a.pre_export_dtls_id=b.id and b.mst_id=c.id and a.lc_sc_id = $lc_id and a.export_type = $lc_type";
//	//echo $sql_loan;
//	$result = sql_select($sql_loan);
//	foreach ($result as $value) {
//		$pre_export_dtls_id .= $value[csf('pre_export_dtls_id')].",";
//		$loan_type = $value[csf('loan_type')];
//	}
//	$pre_export_dtls_id = chop($pre_export_dtls_id, ",");
	//echo $loan_type;die;
	$data_array=sql_select($sql);

 	foreach ($data_array as $row)
	{
		echo "document.getElementById('is_invoice_bill_lien_bank').value 		= '".$row[csf("lien_bank")]."';\n";
		echo "document.getElementById('txt_invoice_bill_no').value 		= '".$row[csf("bank_ref_no")]."';\n";
		echo "document.getElementById('invoice_bill_id').value 			= '".$invoice_bill_id."';\n";
		echo "document.getElementById('is_invoice_bill').value 			= '".$is_invoiceBill."';\n";
		echo "document.getElementById('txt_lc_sc_no').value 			= '".$row[csf("bank_ref_no")]."';\n";
		echo "document.getElementById('cbo_buyer_name').value 			= '".$row[csf("buyer_id")]."';\n";
		echo "document.getElementById('cbo_currency_name').value 		= '".$row[csf("currency_name")]."';\n";
		echo "document.getElementById('txt_bill_invoice_date').value 	= '".change_date_format($row[csf("bank_ref_date")])."';\n";
		$pending_rlz_value=$row[csf("bill_amnt")]-$prev_rlz_value;
		echo "document.getElementById('txt_bill_invoice_amnt').value 	= '".$pending_rlz_value."';\n";
		echo "document.getElementById('txt_negotiated_amount').value 	= '".$negotiated_amnt."';\n";
		echo "document.getElementById('submit_type').value 				= '".$row[csf('submit_type')]."';\n";
		echo "document.getElementById('txt_import_btb').value 				= '".$row[csf("import_btb")]."';\n";

		if($realization_id!="")
		{
			$sql_realization="select benificiary_id, buyer_id, received_date, remarks, lib_distribution_string from com_export_proceed_realization where id=$realization_id";
			$data_array_realization=sql_select($sql_realization);

			echo "document.getElementById('cbo_beneficiary_name').value 	= '".$data_array_realization[0][csf("benificiary_id")]."';\n";
			echo "document.getElementById('cbo_buyer_name').value 			= '".$data_array_realization[0][csf("buyer_id")]."';\n";
			echo "document.getElementById('txt_received_date').value 		= '".change_date_format($data_array_realization[0][csf("received_date")])."';\n";
			echo "document.getElementById('txt_remarks').value 				= '".$data_array_realization[0][csf("remarks")]."';\n";
			echo "document.getElementById('hdn_variable_distribution').value = '".$data_array_realization[0][csf("lib_distribution_string")]."';\n";
			echo "document.getElementById('update_id').value 				= '".$realization_id."';\n";
			echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_export_proceed_realization',1);\n";
		}
		else
		{
			echo "set_button_status(0, '".$_SESSION['page_permission']."', 'fnc_export_proceed_realization',1);\n";
		}

		exit();
	}
}

if($action=="proceed_realization_popup_search")
{
	echo load_html_head_contents("Export Proceeds Realization Form", "../../../", 1, 1,'','1','');
	extract($_REQUEST);
	?>

	<script>

		function js_set_value(data)
		{
			var data=data.split("_");
			$('#hidden_realization_id').val(data[0]);
			$('#hidden_invoice_bill_id').val(data[1]);
			$('#hidden_is_invoiceBill').val(data[2]);
			$('#hidden_is_posted_account').val(data[3]);
			parent.emailwindow.hide();
		}

    </script>

</head>

<body>
<div align="center" style="width:760px;">
	<form name="searchexportinformationfrm"  id="searchexportinformationfrm">
		<fieldset style="width:750px;">
		<legend>Enter search words</legend>
            <table cellpadding="0" cellspacing="0" width="750" class="rpt_table">
                <thead>
                    <th>Buyer</th>
                    <th>Search By</th>
                    <th width="160" id="search_by_td_up">Enter SC No</th>
                    <th>SC Date Range</th>
                    <th>
                        <input type="reset" name="reset" id="reset" value="Reset" style="width:70px" class="formbutton" />
                        <input type="hidden" name="hidden_realization_id" id="hidden_realization_id" value="" />
                        <input type="hidden" name="hidden_invoice_bill_id" id="hidden_invoice_bill_id" value="" />
                        <input type="hidden" name="hidden_is_invoiceBill" id="hidden_is_invoiceBill" value="" />
                        <input type="hidden" name="hidden_is_posted_account" id="hidden_is_posted_account" value="" />
                    </th>
                </thead>
                <tr class="general">
                    <td>
                        <?
						if($buyerID!=0)
						{
							echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company c where buy.id=c.buyer_id and buy.status_active =1 and buy.is_deleted=0 and c.tag_company=$beneficiary_name  $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $buyerID, "",0 );
						}
						else
						{
						 	echo create_drop_down( "cbo_buyer_name", 152, "select buy.id, buy.buyer_name from lib_buyer buy, lib_buyer_tag_company c where buy.id=c.buyer_id  and buy.status_active =1 and buy.is_deleted=0 and c.tag_company=$beneficiary_name $buyer_cond order by buy.buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "",0 );
						}
                        ?>
                    </td>
                    <td>
                        <?
                            $arr=array(1=>'SC No');
							$dd="change_search_event(this.value, '0*0*0', '0*0*0', '../../../') ";
							echo create_drop_down( "cbo_search_by", 150, $arr,"",0, "--Select--", "",$dd,0 );
                        ?>
                    </td>
                    <td id="search_by_td">
                        <input type="text" style="width:130px;" class="text_boxes"  name="txt_search_common" id="txt_search_common" />
                    </td>
                    <td>
                        <input name="txt_date_from" id="txt_date_from" class="datepicker"  style="width:60px" placeholder="From Date" />
	                	<input name="txt_date_to" id="txt_date_to" class="datepicker" style="width:60px"placeholder="To Date" />
                    </td>
                     <td>
                        <input type="button" id="search_button" class="formbutton" value="Show" onClick="show_list_view ( document.getElementById('cbo_buyer_name').value+'**'+<? echo $beneficiary_name; ?>+'**'+document.getElementById('cbo_search_by').value+'**'+document.getElementById('txt_search_common').value+'**'+document.getElementById('txt_date_from').value+'**'+document.getElementById('txt_date_to').value, 'proceed_realization_search_list_view', 'search_div', 'export_proceed_realization_advance_controller', 'setFilterGrid(\'list_view\',-1)')" style="width:70px;" />
                     </td>
                </tr>
                <tr>
                    <td colspan="5" align="center" valign="bottom"><? echo load_month_buttons(1);  ?></td>
                </tr>
           </table>
            <div style="width:100%; margin-top:10px" id="search_div" align="left"></div>
		</fieldset>
	</form>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
	exit();
}

if($action=="proceed_realization_search_list_view")
{
	$data=explode('**',$data);

	$buyer_id=$data[0];
	$company_id=$data[1];
	$search_by=$data[2];
	$search_text=trim($data[3]);
	$date_form=$data[4];
	$date_to=$data[5];
	//if($buyer_id!=0) $byer_cond="and a.buyer_id=$buyer_id"; else $byer_cond="";

	$search_cond="";
	$search_cond = " and e.contract_no like '%".$search_text."%'";

	if($buyer_id==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $byer_cond="and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $byer_cond="";
		}
		else
		{
			$byer_cond="";
		}
	}
	else
	{
		$byer_cond="and a.buyer_id= $buyer_id";
	}

	$date_cond="";
	if($date_form!="" && $date_to !="")
	{
		if($db_type==0)
		{
			$date_form=change_date_format($date_form,'yyyy-mm-dd');
			$date_to=change_date_format($date_to,'yyyy-mm-dd');
		}
		else
		{
			$date_form=change_date_format($date_form,'','',1);
			$date_to=change_date_format($date_to,'','',1);
		}
		$date_cond=" and a.received_date between '$date_form' and '$date_to'";
	}
	
	/*
	if($search_text=="")
	{
		$sql = "select a.id, a.benificiary_id, a.buyer_id, a.invoice_bill_id, a.is_invoice_bill, b.bank_ref_no, b.bank_ref_date, sum(c.net_invo_value) as bill_amnt, a.is_posted_account, a.received_date from com_export_proceed_realization a, com_export_doc_submission_mst b, com_export_doc_submission_invo c where a.invoice_bill_id=b.id and a.is_invoice_bill=1 and b.id=c.doc_submission_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.benificiary_id=$company_id $byer_cond $date_cond group by a.id, a.benificiary_id, a.buyer_id, a.invoice_bill_id, a.is_invoice_bill, a.received_date, b.bank_ref_no, b.bank_ref_date, a.is_posted_account
		union all
		select a.id, a.benificiary_id, a.buyer_id, a.invoice_bill_id, a.is_invoice_bill, e.invoice_no as bank_ref_no, e.invoice_date as bank_ref_date, sum(e.invoice_value) as bill_amnt, a.is_posted_account, a.received_date from com_export_proceed_realization a, com_export_invoice_ship_mst e where a.invoice_bill_id=e.id and a.is_invoice_bill=2 and a.status_active=1 and a.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.benificiary_id=$company_id $byer_cond $date_cond group by a.id, a.benificiary_id, a.buyer_id, a.invoice_bill_id, a.is_invoice_bill, a.received_date, e.invoice_no, e.invoice_date, a.is_posted_account
                order by bank_ref_no";
	}
	else
	{
		if($search_by==1)
		{
			$sql = "select a.id, a.benificiary_id, a.buyer_id, a.invoice_bill_id, a.is_invoice_bill, b.bank_ref_no, b.bank_ref_date, sum(c.net_invo_value) as bill_amnt, a.is_posted_account, a.received_date from com_export_proceed_realization a, com_export_doc_submission_mst b, com_export_doc_submission_invo c where a.invoice_bill_id=b.id and a.is_invoice_bill=1 and b.id=c.doc_submission_mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.benificiary_id=$company_id $byer_cond $date_cond and b.bank_ref_no like '%".$search_text."%' group by a.id, a.benificiary_id, a.buyer_id, a.invoice_bill_id, a.received_date, a.is_invoice_bill, b.bank_ref_no, b.bank_ref_date, a.is_posted_account order by b.bank_ref_no";
		}
		else
		{
			$sql = "select a.id, a.benificiary_id, a.buyer_id, a.invoice_bill_id, a.is_invoice_bill, e.invoice_no as bank_ref_no, e.invoice_date as bank_ref_date, sum(e.invoice_value) as bill_amnt, a.is_posted_account, a.received_date from com_export_proceed_realization a, com_export_invoice_ship_mst e where a.status_active=1 and a.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.benificiary_id=$company_id $byer_cond $date_cond and e.invoice_no like '%".$search_text."%' group by a.id, a.benificiary_id, a.buyer_id, a.invoice_bill_id, a.is_invoice_bill, a.received_date, e.invoice_no, e.invoice_date, a.is_posted_account order by e.invoice_no";
		}
	}
	

	if($search_by==1 || $search_by==2)
	{
		$sql = "select a.id, a.benificiary_id, a.buyer_id, a.invoice_bill_id, a.is_invoice_bill, b.bank_ref_no, b.bank_ref_date, sum(c.net_invo_value) as bill_amnt, a.is_posted_account, a.received_date,b.import_btb 
		from com_export_proceed_realization a, com_export_doc_submission_mst b, com_export_doc_submission_invo c , com_export_invoice_ship_mst e 
		where a.invoice_bill_id=b.id and a.is_invoice_bill=1 and b.id=c.doc_submission_mst_id and e.id = c.invoice_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.is_partial=1 and a.benificiary_id=$company_id $byer_cond $date_cond $search_cond 
		group by a.id, a.benificiary_id, a.buyer_id, a.invoice_bill_id, a.is_invoice_bill, a.received_date, b.bank_ref_no, b.bank_ref_date, a.is_posted_account,b.import_btb";
	}
	else
	{
		$sql = "select a.id, a.benificiary_id, a.buyer_id, a.invoice_bill_id, a.is_invoice_bill, e.invoice_no as bank_ref_no, e.invoice_date as bank_ref_date, sum(e.invoice_value) as bill_amnt, a.is_posted_account, a.received_date 
		from com_export_proceed_realization a, com_export_invoice_ship_mst e 
		where a.invoice_bill_id=e.id and a.status_active=1 and a.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.is_partial=1 and a.benificiary_id=$company_id $byer_cond $date_cond $search_cond
		group by a.id, a.benificiary_id, a.buyer_id, a.invoice_bill_id, a.is_invoice_bill, a.received_date, e.invoice_no, e.invoice_date, a.is_posted_account order by e.invoice_no";
	}*/
	
	$sql = "select a.id, a.benificiary_id, a.buyer_id, a.invoice_bill_id, a.is_invoice_bill, a.is_posted_account, a.received_date, e.contract_no as bank_ref_no, e.contract_date as bank_ref_date, sum(e.contract_value) as bill_amnt 
	from com_export_proceed_realization a, com_sales_contract e 
	where a.invoice_bill_id=e.id and a.is_invoice_bill=3 and a.status_active=1 and a.is_deleted=0 and e.status_active=1 and e.is_deleted=0 and a.is_partial=1 and a.benificiary_id=$company_id $byer_cond $date_cond $search_cond
	group by a.id, a.benificiary_id, a.buyer_id, a.invoice_bill_id, a.is_invoice_bill, a.received_date, a.is_posted_account , e.contract_no, e.contract_date
	order by e.contract_no";
	//echo $sql;
	$is_invoiceBill_arr=array(1=>"Bill",2=>"Invoice");
	$buyer_arr=return_library_array( "select id, short_name from lib_buyer",'id','short_name');
	$comp=return_library_array( "select id, company_short_name from lib_company",'id','company_short_name');

	$result = sql_select($sql);
	foreach ($result as $value) {
		if($value[csf("import_btb")] == 1)
		{
			$buyer_company[$value[csf("id")]] = $comp[$value[csf("buyer_id")]];
		}else{
			$buyer_company[$value[csf("id")]] = $buyer_arr[$value[csf("buyer_id")]];
		}
	}

	$arr=array (2=>$is_invoiceBill_arr,3=>$comp,4=>$buyer_company);

	echo create_list_view("list_view", "System Id,Bill/ Invoice No,Bill/ Invoice,Benificiary,Buyer,Bill/ Invoice Amnt,Received Date", "70,120,70,80,100,110","720","280",0, $sql, "js_set_value", "id,invoice_bill_id,is_invoice_bill,is_posted_account", "", 1, "0,0,is_invoice_bill,benificiary_id,id,0,0", $arr , "id,bank_ref_no,is_invoice_bill,benificiary_id,id,bill_amnt,received_date", "",'','0,0,0,0,0,2,3');
	exit();
}

if($action=="details_list_view")
{
	$data=explode("**",$data);
	$realization_id=$data[0];
	$type=$data[1];
	$submit_type=$data[2];
	$variable_distribution=$data[3];

	$nameArray=sql_select( "select id, account_head, ac_loan_no, document_currency, conversion_rate, domestic_currency, distribute_percent from com_export_proceed_rlzn_dtls where mst_id='$realization_id' and type='$type' and status_active=1 and is_deleted=0" );
	$num_row=count($nameArray);

	$i=1;

	if($type==0)
	{
		if($num_row>0)
		{
			foreach($nameArray as $row)
			{
			?>
				<tr class="general" id="deduction_<? echo $i; ?>">
					<td>
                    	<!--<select class="combo_boxes" id="cbodeductionHead_<?// echo $i; ?>" name="cbodeductionHead[]" style="width:172px">
                            <option value="0">-- Select Account Head --</option>
                                <?
                                    /*foreach($commercial_head as $key=>$value)
                                    {
                                    ?>
                                        <option value=<? echo $key; if($row[csf('account_head')]==$key) { ?> selected <? } ?>><? echo $value; ?></option>
                                    <?
                                    }*/
                                ?>
                        </select>-->
                        <input type="text" name="cbodeductionHead[]" id="cbodeductionHead_<?=$i;?>" class="text_boxes" style="width:170px;" onFocus="add_auto_complete( <?=$i;?> )"  onDblClick="fn_commercial_head_display(<?=$i;?>,1,'cbodeductionHead')" onBlur="fn_value_check(<?=$i;?>,1,this.value,'cbodeductionHead')" placeholder="Browse Or Write" acHeadVal="<?= $row[csf('account_head')];?>" value="<?= $commercial_head[$row[csf('account_head')]];?>" />
					</td>
					<td>
						<input type="text" name="deductionDocumentCurrency[]" id="deductionDocumentCurrency_<? echo $i; ?>" class="text_boxes_numeric" style="width:177px;" onKeyUp="calculate(<? echo $i; ?>,'DocumentCurrency_','tbl_deduction','deduction')" value="<? echo $row[csf('document_currency')]; ?>"/>
					</td>
					<td>
						<input type="text" name="deductionConversionRate[]" id="deductionConversionRate_<? echo $i; ?>" class="text_boxes_numeric" style="width:177px;" onKeyUp="calculate(<? echo $i; ?>,'ConversionRate_','tbl_deduction','deduction')" value="<? echo $row[csf('conversion_rate')]; ?>"/>
					</td>
					<td>
						<input type="text" name="deductionDomesticCurrency[]" id="deductionDomesticCurrency_<? echo $i; ?>" class="text_boxes_numeric" style="width:177px;" onKeyUp="calculate(<? echo $i; ?>,'DomesticCurrency_','tbl_deduction','deduction')" value="<? echo $row[csf('domestic_currency')]; ?>" />
					</td>
					<td width="65">
						<input type="button" id="deductionincrease_<? echo $i; ?>" name="deductionincrease[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fnc_addRow(<? echo $i; ?>,'tbl_deduction','deduction_')" />
						<input type="button" id="deductiondecrease_<? echo $i; ?>" name="deductiondecrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fnc_deleteRow(<? echo $i; ?>,'tbl_deduction','deduction_');" />
					</td>
				</tr>
			<?
			$i++;
			}
		}
		else
		{
		?>
        	<tr class="general" id='deduction_1' align="center">
                <td>
                	<!--<select class="combo_boxes" id="cbodeductionHead_1" name="cbodeductionHead[]" style="width:172px">
                        <option value="0">-- Select Account Head --</option>
                            <?
                                /*foreach($commercial_head as $key=>$value)
                                {
                                ?>
                                    <option value="<? echo $key; ?>"><? echo $value; ?></option>
                                <?
                                }*/
                            ?>
                    </select>-->
                    <input type="text" name="cbodeductionHead[]" id="cbodeductionHead_1" class="text_boxes" style="width:170px;" onFocus="add_auto_complete( 1 )"  onBlur="fn_value_check(1,1,this.value,'cbodeductionHead')" onDblClick="fn_commercial_head_display(1,1,'cbodeductionHead')" placeholder="Browse Or Write" />
                </td>
                <td>
                    <input type="text" name="deductionDocumentCurrency[]" id="deductionDocumentCurrency_1" class="text_boxes_numeric" style="width:177px;" onKeyUp="calculate(1,'DocumentCurrency_','tbl_deduction','deduction')"/>
                </td>
                <td>
                    <input type="text" name="deductionConversionRate[]" id="deductionConversionRate_1" class="text_boxes_numeric" style="width:177px;" onKeyUp="calculate(1,'ConversionRate_','tbl_deduction','deduction')"/>
                </td>
                <td>
                    <input type="text" name="deductionDomesticCurrency[]" id="deductionDomesticCurrency_1" class="text_boxes_numeric" style="width:177px;" onKeyUp="calculate(1,'DomesticCurrency_','tbl_deduction','deduction')" />
                </td>
                <td width="65">
                    <input type="button" id="deductionincrease_1" name="deductionincrease[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fnc_addRow(1,'tbl_deduction','deduction_')" />
                    <input type="button" id="deductiondecrease_1" name="deductiondecrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fnc_deleteRow(1,'tbl_deduction','deduction_');" />
                </td>
            </tr>
        <?
		}
	}
	else
	{
		if($num_row>0)
		{
			foreach($nameArray as $row)
			{
				// comment on 2-18-2017 by ashraful issue id-1001
				/*if($submit_type==2 && $row[csf('account_head')]==1)
				{
					$disable="disabled='disabled'";
				}
				else
				{
					$disable="";
				}*/
			?>
				<tr class="general" id="distribution_<? echo $i; ?>">
					<td>
                    	<!--<select class="combo_boxes" id="cbodistributionHead_<?// echo $i; ?>" name="cbodistributionHead[]" onChange="get_php_form_data( this.value+'**<?// echo $i; ?>**'+$('#cbo_beneficiary_name').val()+'**'+$('#is_invoice_bill_lien_bank').val(), 'populate_acc_loan_no_data', 'requires/export_proceed_realization_advance_controller' );check_duplication(<?// echo $i; ?>)" <?// echo $disable; ?>>
                            <option value="0">-- Select Account Head --</option>
                                <?
                                    /*foreach($commercial_head as $key=>$value)
                                    {
                                    ?>
                                        <option value=<? echo $key; if($row[csf('account_head')]==$key) { ?> selected <? } ?>><? echo $value; ?></option>
                                    <?
                                    }*/
									//echo create_drop_down( "cbodistributionHead_$i", 172, $commercial_head,"", 1, "-- Select Account Head --", $row[csf('account_head')], "" );
                                ?>
                        </select>-->
                        <input type="text" name="cbodistributionHead[]" id="cbodistributionHead_<?=$i;?>" class="text_boxes" style="width:170px;" onFocus="add_auto_complete( <?=$i;?> )"  onDblClick="fn_commercial_head_display(<?=$i;?>,2,'cbodistributionHead')"  onBlur="fn_value_check(<?=$i;?>,2,this.value,'cbodistributionHead')"  placeholder="Browse Or Write" acHeadVal="<?= $row[csf('account_head')];?>" value="<?= $commercial_head[$row[csf('account_head')]];?>" />
					</td>
					<td>
						<input type="text" name="acLoanNo[]" id="acLoanNo_<? echo $i; ?>" class="text_boxes" style="width:120px;" onDblClick="openmypage_AccountNo(this.id)" placeholder="Browse" readonly value="<? echo $row[csf('ac_loan_no')]; ?>" />
					</td>
                    <?
					if($variable_distribution!="")
					{
						?>
                        <td id="dis_per_td">
                        <input type="text" id="txtdispersent_<?=$i;?>" value="<?= $row[csf('distribute_percent')];?>" name="txtdispersent[]" class="text_boxes_numeric" style="width:50px"  readonly />
                        </td>
                        <?
					}
					else
					{
						?>
                        <td id="dis_per_td" style="display:none">
                        <input type="text" id="txtdispersent_1" name="txtdispersent[]" class="text_boxes_numeric" style="width:50px"  readonly />
                        </td>
                        <?
					}
					?>
					<td>
						<input type="text" name="distributionDocumentCurrency[]" id="distributionDocumentCurrency_<? echo $i; ?>" class="text_boxes_numeric" style="width:130px;" onKeyUp="calculate(<? echo $i; ?>,'DocumentCurrency_','tbl_distribution','distribution')" value="<? echo $row[csf('document_currency')]; ?>" <? echo $disable; ?>/>
					</td>
					<td>
						<input type="text" name="distributionConversionRate[]" id="distributionConversionRate_<? echo $i; ?>" class="text_boxes_numeric" style="width:90px;" onKeyUp="calculate(<? echo $i; ?>,'ConversionRate_','tbl_distribution','distribution')" value="<? echo $row[csf('conversion_rate')]; ?>" <? echo $disable; ?>/>
					</td>
					<td>
						<input type="text" name="distributionDomesticCurrency[]" id="distributionDomesticCurrency_<? echo $i; ?>" class="text_boxes_numeric" style="width:130px;" onKeyUp="calculate(<? echo $i; ?>,'DomesticCurrency_','tbl_distribution','distribution')" value="<? echo $row[csf('domestic_currency')]; ?>" <? echo $disable; ?>/>
					</td>
					<td width="65">
						<input type="button" id="distributionincrease_<? echo $i; ?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fnc_addRow(<? echo $i; ?>,'tbl_distribution','distribution_')"/>
						<input type="button" id="distributiondecrease_<? echo $i; ?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fnc_deleteRow(<? echo $i; ?>,'tbl_distribution','distribution_');"/>
					</td>
				</tr>
			<?
			$i++;
			}
		}
		else
		{
		?>
            <tr class="general" id='distribution_1' align="center">
                <td>
					<!--<select class="combo_boxes" id="cbodistributionHead_1" name="cbodistributionHead[]" onChange="get_php_form_data( this.value+'**1**'+$('#cbo_beneficiary_name').val()+'**'+$('#is_invoice_bill_lien_bank').val(), 'populate_acc_loan_no_data', 'requires/export_proceed_realization_advance_controller' );check_duplication(1)">
                        <option value="0">-- Select Account Head --</option>
                            <?
                                /*foreach($commercial_head as $key=>$value)
                                {
                                ?>
                                    <option value="<? echo $key; ?>"><? echo $value; ?></option>
                                <?
                                }*/
                            ?>
					</select>-->
                    <input type="text" name="cbodistributionHead[]" id="cbodistributionHead_1" class="text_boxes" style="width:170px;" onFocus="add_auto_complete( 1 )"  onBlur="fn_value_check(1,2,this.value,'cbodistributionHead')"  onDblClick="fn_commercial_head_display(1,2,'cbodistributionHead')"  placeholder="Browse Or Write" />
                </td>
                <td>
                    <input type="text" name="acLoanNo[]" id="acLoanNo_1" class="text_boxes" style="width:120px;" onDblClick="openmypage_AccountNo(this.id)" placeholder="Browse" readonly/>
                </td>
                <?
				if($variable_distribution!="")
				{
					?>
					<td id="dis_per_td">
					<input type="text" id="txtdispersent_1" name="txtdispersent[]" class="text_boxes_numeric" style="width:50px"  readonly />
					</td>
					<?
				}
				else
				{
					?>
					<td id="dis_per_td" style="display:none">
					<input type="text" id="txtdispersent_1" name="txtdispersent[]" class="text_boxes_numeric" style="width:50px"  readonly />
					</td>
					<?
				}
				?>
                <td>
                    <input type="text" name="distributionDocumentCurrency[]" id="distributionDocumentCurrency_1" class="text_boxes_numeric" style="width:130px;" onKeyUp="calculate(1,'DocumentCurrency_','tbl_distribution','distribution')"/>
                </td>
                <td>
                    <input type="text" name="distributionConversionRate[]" id="distributionConversionRate_1" class="text_boxes_numeric" style="width:90px;" onKeyUp="calculate(1,'ConversionRate_','tbl_distribution','distribution')"/>
                </td>
                <td>
                    <input type="text" name="distributionDomesticCurrency[]" id="distributionDomesticCurrency_1" class="text_boxes_numeric" style="width:130px;" onKeyUp="calculate(1,'DomesticCurrency_','tbl_distribution','distribution')" />
                </td>
                <td width="65">
                    <input type="button" id="distributionincrease_1" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fnc_addRow(1,'tbl_distribution','distribution_')" />
                    <input type="button" id="distributiondecrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fnc_deleteRow(1,'tbl_distribution','distribution_');" />
                </td>
            </tr>
        <?
		}
	}
}

if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$inv_bill_id=str_replace("'","",$invoice_bill_id);
	$rlz_id=str_replace("'","",$update_id);
	$tot_rlz_amt=str_replace("'","",$grand_total_document_currency);
	
	if($rlz_id>0)
	{
		$rlz_update_cond=" and a.id <> $rlz_id";
	}
	
	$sql_inv = "select listagg(cast(b.id as varchar(4000)),',') within group (order by b.id) as INV_ID, c.CONTRACT_VALUE
	from  com_sales_contract c left join com_export_invoice_ship_mst b on  c.id= b.lc_sc_id and b.is_lc=2 and b.status_active=1 and b.is_deleted=0
	where c.pay_term = 3  and c.status_active=1 and c.is_deleted=0 and c.id=$inv_bill_id
	group by c.CONTRACT_VALUE";
	//echo "10**".$sql_inv;die;
	$sql_inv_result=sql_select($sql_inv);
	foreach($sql_inv_result as $row)
	{
		$sc_inv_id=$row["INV_ID"];
		$inv_bill_value=$row["CONTRACT_VALUE"]*1;
	}
	
	$prev_realized=sql_select("select a.invoice_bill_id, b.id as dtls_id, b.document_currency, 1 as type 
	from com_export_proceed_realization a, com_export_proceed_rlzn_dtls b 
	where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.is_invoice_bill in(3) and a.invoice_bill_id=$inv_bill_id $rlz_update_cond
	union all
	select a.invoice_bill_id, b.id as dtls_id, b.document_currency, 2 as type 
	from com_export_proceed_realization a, com_export_proceed_rlzn_dtls b 
	where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.is_invoice_bill in(2) and b.ACCOUNT_HEAD<>177 and a.invoice_bill_id in($sc_inv_id)
	union all
	select a.invoice_bill_id, b.id as dtls_id, b.document_currency, 3 as type 
	from com_export_proceed_realization a, com_export_proceed_rlzn_dtls b 
	where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.is_invoice_bill in(2) and b.ACCOUNT_HEAD=177 and a.invoice_bill_id in($sc_inv_id)");
	$prev_rlz_data=array();
	$prev_inv_adv=$prev_rlz_value=0;
	foreach($prev_realized as $row)
	{
		if($row[csf("type")]==3)
		{
			$prev_inv_adv+=$row[csf("document_currency")];
		}
		else
		{
			$prev_rlz_value+=$row[csf("document_currency")];
		}
		
	}
	
	//$prev_realized=sql_select("select a.invoice_bill_id, sum(b.document_currency) as document_currency 
//	from com_export_proceed_realization a, com_export_proceed_rlzn_dtls b 
//	where a.id=b.mst_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.invoice_bill_id=$inv_bill_id and a.is_invoice_bill=1 $rlz_update_cond
//	group by a.invoice_bill_id");
//	$prev_rlz_data=array();
//	foreach($prev_realized as $row)
//	{
//		$prev_rlz_value+=$row[csf("document_currency")];
//	}

	$tot_bill_value=($prev_rlz_value+$tot_rlz_amt)*1;
	
	if($tot_bill_value>$inv_bill_value || $tot_rlz_amt*1 < $prev_inv_adv*1)
	{
		echo "20**Realized Amount Not Allow More Then Bill Amount. = $tot_bill_value = $inv_bill_value = $tot_rlz_amt = $prev_inv_adv";die;
	}
	
	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$flag=1;
		$id=return_next_id( "id", "com_export_proceed_realization", 1 ) ;

		$field_array="id, invoice_bill_id, is_invoice_bill, buyer_id, benificiary_id, received_date, remarks, lib_distribution_string, is_partial, buyer_partial_rlz, inserted_by, insert_date";

		$data_array="(".$id.",".$invoice_bill_id.",".$is_invoice_bill.",".$cbo_buyer_name.",".$cbo_beneficiary_name.",".$txt_received_date.",".$txt_remarks.",".$hdn_variable_distribution.",1,".$buyer_partial_rlz.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";

		/*$rID=sql_insert("com_export_proceed_realization",$field_array,$data_array,0);
		if($flag==1)
		{
			if($rID) $flag=1; else $flag=0;
		} */

		$field_array_dtls="id, mst_id, type, account_head, ac_loan_no, distribute_percent, document_currency, conversion_rate, domestic_currency, inserted_by, insert_date";
		$realization_id = return_next_id( "id", "com_export_proceed_rlzn_dtls", 1 );
		for($j=1;$j<=$deduction_tot_row;$j++)
		{
			$type="0";
			$account_head="cbodeductionHead_".$j;
			$ac_loan_no="";
			$distribute_percent="";
			$document_currency="deductionDocumentCurrency_".$j;
			$conversion_rate="deductionConversionRate_".$j;
			$domestic_currency="deductionDomesticCurrency_".$j;

			if($data_array_dtls!="") $data_array_dtls.=",";

			$data_array_dtls.="(".$realization_id.",".$id.",".$type.",'".$$account_head."','".$ac_loan_no."','".$distribute_percent."','".$$document_currency."','".$$conversion_rate."','".$$domestic_currency."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$realization_id = $realization_id+1;
		}

		for($j=1;$j<=$distribution_tot_row;$j++)
		{
			$type="1";
			$account_head="cbodistributionHead_".$j;
			$ac_loan_no="acLoanNo_".$j;
			$distribute_percent="txtdispersent_".$j;
			$document_currency="distributionDocumentCurrency_".$j;
			$conversion_rate="distributionConversionRate_".$j;
			$domestic_currency="distributionDomesticCurrency_".$j;

			if($data_array_dtls!="") $data_array_dtls.=",";

			$data_array_dtls.="(".$realization_id.",".$id.",".$type.",'".$$account_head."','".$$ac_loan_no."','".$$distribute_percent."','".$$document_currency."','".$$conversion_rate."','".$$domestic_currency."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$realization_id = $realization_id+1;
		}


		$rID=sql_insert("com_export_proceed_realization",$field_array,$data_array,0);
		if($flag==1)
		{
			if($rID) $flag=1; else $flag=0;
		}
		//echo "10** insert into com_export_proceed_rlzn_dtls ($field_array_dtls) values $data_array_dtls";oci_rollback($con);die;
		$rID2=sql_insert("com_export_proceed_rlzn_dtls",$field_array_dtls,$data_array_dtls,1);
		if($flag==1)
		{
			if($rID2) $flag=1; else $flag=0;
		}
		//echo "10**$rID=$rID2";oci_rollback($con);die;
		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "0**".$id."**1";
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "5**0**0";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "0**".$id."**1";
			}
			else
			{
				oci_rollback($con);
				echo "5**0**0";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$flag=1;

		$field_array="invoice_bill_id*is_invoice_bill*buyer_id*benificiary_id*received_date*remarks*updated_by*update_date";

		$data_array=$invoice_bill_id."*".$is_invoice_bill."*".$cbo_buyer_name."*".$cbo_beneficiary_name."*".$txt_received_date."*".$txt_remarks."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";

		/*$rID=sql_update("com_export_proceed_realization",$field_array,$data_array,"id",$update_id,0);
		if($flag==1)
		{
			if($rID) $flag=1; else $flag=0;
		}

		$delete_dtls=execute_query( "delete from com_export_proceed_rlzn_dtls where mst_id=$update_id",0);
		if($flag==1)
		{
			if($delete_dtls) $flag=1; else $flag=0;
		} */

		$field_array_dtls="id, mst_id, type, account_head, ac_loan_no, distribute_percent, document_currency, conversion_rate, domestic_currency, inserted_by, insert_date";
		$realization_id = return_next_id( "id", "com_export_proceed_rlzn_dtls", 1 );

		for($j=1;$j<=$deduction_tot_row;$j++)
		{
			$type="0";
			$account_head="cbodeductionHead_".$j;
			$ac_loan_no="";
			$distribute_percent="";
			$document_currency="deductionDocumentCurrency_".$j;
			$conversion_rate="deductionConversionRate_".$j;
			$domestic_currency="deductionDomesticCurrency_".$j;

			if($data_array_dtls!="") $data_array_dtls.=",";

			$data_array_dtls.="(".$realization_id.",".$update_id.",".$type.",'".$$account_head."','".$ac_loan_no."','".$distribute_percent."','".$$document_currency."','".$$conversion_rate."','".$$domestic_currency."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$realization_id = $realization_id+1;
		}

		for($j=1;$j<=$distribution_tot_row;$j++)
		{
			$type="1";
			$account_head="cbodistributionHead_".$j;
			$ac_loan_no="acLoanNo_".$j;
			$distribute_percent="txtdispersent_".$j;
			$document_currency="distributionDocumentCurrency_".$j;
			$conversion_rate="distributionConversionRate_".$j;
			$domestic_currency="distributionDomesticCurrency_".$j;

			if($data_array_dtls!="") $data_array_dtls.=",";

			$data_array_dtls.="(".$realization_id.",".$update_id.",".$type.",'".$$account_head."','".$$ac_loan_no."','".$$distribute_percent."','".$$document_currency."','".$$conversion_rate."','".$$domestic_currency."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."')";
			$realization_id = $realization_id+1;
		}
		//echo $data_array_dtls;die;
		$rID=sql_update("com_export_proceed_realization",$field_array,$data_array,"id",$update_id,0);
		if($flag==1)
		{
			if($rID) $flag=1; else $flag=0;
		}

		$delete_dtls=execute_query( "delete from com_export_proceed_rlzn_dtls where mst_id=$update_id",0);
		if($flag==1)
		{
			if($delete_dtls) $flag=1; else $flag=0;
		}
		//echo "6**insert into com_export_proceed_rlzn_dtls (".$field_array_dtls.") values ".$data_array_dtls;oci_rollback($con);die;
		$rID2=sql_insert("com_export_proceed_rlzn_dtls",$field_array_dtls,$data_array_dtls,1);
		//echo "6**$rID=$rID2";oci_rollback($con);die;
		if($flag==1)
		{
			if($rID2) $flag=1; else $flag=0;
		}

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo "1**".str_replace("'", '', $update_id)."**1";
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "6**".str_replace("'", '', $update_id)."**1";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo "1**".str_replace("'", '', $update_id)."**1";
			}
			else
			{
				oci_rollback($con);
				echo "6**".str_replace("'", '', $update_id)."**1";
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)   // Update Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}
		
		$sql_ac_post=sql_select("select is_posted_account from com_export_proceed_realization where id=$update_id");
		$is_posted_accounts=$sql_ac_post[0][csf("is_posted_account")];
		if($is_posted_accounts==1)
		{
			echo "20**Already Posted In Accounts. \n Delete Not Allow.";disconnect($con);die;
		}
		
		$field_array="status_active*is_deleted*updated_by*update_date";
		$data_array="0*1*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID=sql_update("com_export_proceed_realization",$field_array,$data_array,"id",$update_id,0);
		$rID2=sql_update("com_export_proceed_rlzn_dtls",$field_array,$data_array,"mst_id",$update_id,0);

		if($db_type==0)
		{
			if($rID && $rID2)
			{
				mysql_query("COMMIT");
				echo "2**".str_replace("'", '', $update_id)."**1";
			}
			else
			{
				mysql_query("ROLLBACK");
				echo "6**".str_replace("'", '', $update_id)."**1";
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID && $rID2)
			{
				oci_commit($con);
				echo "2**".str_replace("'", '', $update_id)."**1";
			}
			else
			{
				oci_rollback($con);
				echo "6**".str_replace("'", '', $update_id)."**1";
			}
		}
		disconnect($con);
		die;
	}

}


if($action=="check_loan_type")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	$sql = "select a.id,a.pre_export_dtls_id,a.export_type,a.lc_sc_id,a.currency_id,a.amount,a.conversion_rate, b.loan_type, b.loan_number from com_pre_export_lc_wise_dtls a, com_pre_export_finance_dtls b, com_pre_export_finance_mst c where a.pre_export_dtls_id=b.id and b.mst_id=c.id and a.lc_sc_id = $lc_id and a.export_type = $lc_type";
	//echo $sql;
	$result = sql_select($sql);
	foreach ($result as $value) {
		$pre_export_dtls_id .= $value[csf('pre_export_dtls_id')].",";
		$loan_type = $value[csf('loan_type')];
	}
	$pre_export_dtls_id = chop($pre_export_dtls_id, ",");
	//echo $loan_type;die;
	if($loan_type == 20 || $loan_type == 22){
		echo $pre_export_dtls_id;
	}else{
		echo "";
	}

	exit();

}
if($action=="loan_number_popup")
{
	echo load_html_head_contents("Loan Number Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	
	$sql = "select a.id,a.pre_export_dtls_id,a.export_type,a.lc_sc_id,a.currency_id,a.amount, a.equivalent_fc, a.conversion_rate, b.id as loan_id, b.loan_number, b.loan_type, c.loan_date 
	from com_pre_export_lc_wise_dtls a, com_pre_export_finance_dtls b, com_pre_export_finance_mst c where a.pre_export_dtls_id = b.id and b.mst_id = c.id and a.pre_export_dtls_id in( $pre_export_dtls_id) and a.lc_sc_id in($lc_id) and a.export_type=$lc_type and b.loan_type = $loan_type";
	
	//echo $sql;//die;
	?>
	<script>

		function js_set_value(loan_dtls_id,loan_number,equivalent_fc,conversion_rate)
		{
			$('#hidden_loan_dtls_id').val(loan_dtls_id);
			$('#hidden_loan_number').val(loan_number);
			$('#hidden_conversion_rate').val(conversion_rate);
			$('#hidden_amount').val(equivalent_fc);
			// $('#hidden_row_id').val(row_id);
			parent.emailwindow.hide();
		}

    </script>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table" >
            <thead>
                <th width="30">SL</th>
                <th width="130">LC/SC No</th>
                <th width="80"><? if($lc_type==1) echo "LC"; else echo "SC"; ?></th>
                <th width="120">Loan Number</th>
                <th width="90">Loan Date</th>
                <th width="90">Conversion Rate</th>
                <th width="80">Currency</th>
                <th width="">Loan Amount</th>
            </thead>
        </table>
        <div style="width:750px; overflow-y:scroll; max-height:240px;" id="buyer_list_view" align="left">
			<input type="hidden" name="hidden_loan_dtls_id" id="hidden_loan_dtls_id" />
			<input type="hidden" name="hidden_loan_number" id="hidden_loan_number" />
			<input type="hidden" name="hidden_conversion_rate" id="hidden_conversion_rate" />
			<input type="hidden" name="hidden_amount" id="hidden_amount" />
			<!-- <input type="hidden" name="hidden_row_id" id="hidden_row_id" /> -->
			<input type="hidden" name="import_btb" id="import_btb" value="<? echo $import_btb?>" />
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="720" class="rpt_table" id="tbl_list_search" >
            <?
				$buyer_arr = return_library_array("select id, buyer_name from lib_buyer",'id','buyer_name');
				if($lc_type == 1){
					$lc_sc_no_array = return_library_array("select id, export_lc_no from com_export_lc",'id','export_lc_no');
				}else{
					$lc_sc_no_array = return_library_array("select id, contract_no from com_sales_contract",'id','contract_no');
				}
				$i=1; $lcsc_row_id='';
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $selectResult)
				{
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";

					if(in_array($selectResult[csf('id')],$hidden_lcsc_id))
					{
						if($lcsc_row_id=="") $lcsc_row_id=$i; else $lcsc_row_id.=",".$i;
					}

					?>
                        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $selectResult[csf('loan_id')];?>,<? echo $selectResult[csf('loan_number')];?>,<?= $selectResult[csf('equivalent_fc')];?>,<?= $selectResult[csf('conversion_rate')];?>,<?php echo $i; ?>)">
                            <td width="30" align="center"><?php echo "$i"; ?>
                             <input type="hidden" name="hidden_loan_dtls_id" id="hidden_loan_dtls_id" value="<?= $selectResult[csf('loan_id')]; ?>"/>
                             <input type="hidden" name="hidden_loan_number" id="hidden_loan_number" value="<?= $selectResult[csf('loan_number')]; ?>"/>
                             <input type="hidden" name="hidden_lc_sc_id" id="hidden_lc_sc_id" value="<?= $selectResult[csf('lc_sc_id')]; ?>"/>
                            </td>
                            <td width="130" align="center"><p><? echo $lc_sc_no_array[$selectResult[csf('lc_sc_id')]]; ?></p></td>
                            <td width="80" align="center"><p><? if($lc_type==1) echo "LC"; else echo "SC"; ?></p></td>
                            <td width="120" align="center"><p><?= $selectResult[csf('loan_number')]; ?></p></td>
                            <td width="90" align="center"><p><?= $selectResult[csf('loan_date')]; ?></p></td>
                            <td width="90" align="right"><? echo $selectResult[csf('conversion_rate')]; ?></td>
                            <td width="80" align="center"><? echo $currency[$selectResult[csf('currency_id')]]; ?></td>
                            <td width="" align="right"><? echo $selectResult[csf('equivalent_fc')]; ?></td>
                         </tr>
                    <?
                    $i++;
				}
			?>
				<!-- <input type="hidden" name="txt_lcsc_row_id" id="txt_lcsc_row_id" value="<?php echo $lcsc_row_id; ?>"/> -->
            </table>
        </div>
	</div>
	<?
}

if($action == "create_loan_number_search_list_view")
{	
	$data = explode("_",$data);

	$search_string=trim($data[0]);
	$search_by=$data[1];
	$company_id =$data[2];
	$all_lc_sc=$data[3];


 	$hidden_lcsc_id=explode(",",$all_lc_sc);

	if($search_by==1)
	{
		$sql = "select id,pre_export_dtls_id,export_type,lc_sc_id,currency_id,amount,conversion_rate from com_pre_export_lc_wise_dtls";
	}
	else
	{
		$sql = "select id,contract_no as lcsc,buyer_name,contract_date as lcsc_date,currency_name,contract_value as lcsc_value from com_sales_contract where status_active=1";
	}
	//echo $sql;die;
	?>
    <div>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" >
            <thead>
                <th width="30">SL</th>
                <th width="130"><? if($search_by==1) echo "LC"; else echo "SC"; ?></th>
                <th width="150">Buyer</th>
                <th width="90">LC Date</th>
                <th width="80">Currency</th>
                <th width="">LC Value</th>
            </thead>
        </table>
        <div style="width:618px; overflow-y:scroll; max-height:240px;" id="buyer_list_view" align="center">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="600" class="rpt_table" id="tbl_list_search" >
            <?
				$buyer_arr = return_library_array("select id, buyer_name from lib_buyer",'id','buyer_name');

				$i=1; $lcsc_row_id='';
				$nameArray=sql_select( $sql );
				foreach ($nameArray as $selectResult)
				{
					if ($i%2==0)
						$bgcolor="#E9F3FF";
					else
						$bgcolor="#FFFFFF";

					if(in_array($selectResult[csf('id')],$hidden_lcsc_id))
					{
						if($lcsc_row_id=="") $lcsc_row_id=$i; else $lcsc_row_id.=",".$i;
					}

					?>
                        <tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>" onClick="js_set_value(<? echo $i;?>,<? echo $selectResult[csf('currency_name')];?>)">
                            <td width="30" align="center"><?php echo "$i"; ?>
                             <input type="hidden" name="txt_individual_id" id="txt_individual_id<?php echo $i ?>" value="<? echo $selectResult[csf('id')]; ?>"/>
                            </td>
                            <td width="130"><p><? echo $selectResult[csf('lcsc')]; ?></p></td>
                            <td width="150"><p><? echo $buyer_arr[$selectResult[csf('buyer_name')]]; ?></p></td>
                            <td width="90"><? echo change_date_format($selectResult[csf('lcsc_date')]); ?></td>
                            <td width="80"><? echo $currency[$selectResult[csf('currency_name')]]; ?></td>
                            <td width=""><? echo $selectResult[csf('lcsc_value')]; ?></td>
                         </tr>
                    <?
                    $i++;
				}
			?>
				<input type="hidden" name="txt_lcsc_row_id" id="txt_lcsc_row_id" value="<?php echo $lcsc_row_id; ?>"/>
            </table>
        </div>
         <table width="620" cellspacing="0" cellpadding="0" style="border:none" align="center">
            <tr>
                <td align="center" height="30" valign="bottom">
                    <div style="width:100%">
                        <div style="width:50%; float:left" align="left">
                            <input type="checkbox" name="check_all" id="check_all" onClick="check_all_data()" /> Check / Uncheck All
                        </div>
                        <div style="width:50%; float:left" align="left">
                            <input type="button" name="close" onClick="show_lcsc_wise_entry();" class="formbutton" value="Close" style="width:100px" />
                        </div>
                    </div>
                </td>
            </tr>
        </table>
	</div>
	<?

	exit();
}


if($action=="populate_data_form_lib")
{
	$data_ref=explode("**",$data);
	$sql_variable=sql_select("select ID, HEAD_PERCENT_STRING from BANK_HEAD_DISTRIBUTE_DTLS where COMPANY_ID=$data_ref[0] and BANK_ID= $data_ref[1] order by DTIS_DATE desc");
	echo $sql_variable[0]["HEAD_PERCENT_STRING"];
}

if($action=="populate_data_form_lib_dis")
{
	$data_ref=explode("**",$data);
	if($data_ref[2]=="") $tot_row=0; else $tot_row=$data_ref[2];
	if($data_ref[0]!="")
	{
		$ac_head_arr=explode("__",$data_ref[0]);
		$i=$tot_row+1;
		foreach($ac_head_arr as $val)
		{
			$ref_val=explode("_",$val);
			$ac_head_data.=$commercial_head[$ref_val[0]].",";
			$doc_amt=(($data_ref[1]/100)*$ref_val[1]);
			?>
            <tr class="general" id='distribution_<?=$i?>' align="center">
                <td> 
                    <input type="text" name="cbodistributionHead[]" id="cbodistributionHead_<?=$i?>" class="text_boxes" style="width:170px;" onFocus="add_auto_complete( <?=$i?> )"  onBlur="fn_value_check(<?=$i?>,2,this.value,'cbodistributionHead')"  onDblClick="fn_commercial_head_display(<?=$i?>,2,'cbodistributionHead')"  placeholder="Browse Or Write" acHeadVal="<?= $ref_val[0];?>" value="<?= $commercial_head[$ref_val[0]];?>" />
                </td>
                <td>
                    <input type="text" name="acLoanNo[]" id="acLoanNo_<?=$i?>" class="text_boxes" style="width:120px;" />
                    <input type="hidden" name="pre_export_dtls_id" id="pre_export_dtls_id"  />
                </td>
                <td id="dis_per_td">
                <input type="text" id="txtdispersent_<?=$i?>" name="txtdispersent[]" class="text_boxes_numeric" style="width:50px" value="<?= $ref_val[1];?>"  readonly />
                </td>
                <td>
                    <input type="text" name="distributionDocumentCurrency[]" id="distributionDocumentCurrency_<?=$i?>" class="text_boxes_numeric" style="width:130px;" onKeyUp="calculate(<?=$i?>,'DocumentCurrency_','tbl_distribution','distribution')" value="<? echo number_format($doc_amt,4,".","");?>"/>
                </td>
                <td>
                    <input type="text" name="distributionConversionRate[]" id="distributionConversionRate_<?=$i?>" class="text_boxes_numeric" style="width:90px;" onKeyUp="calculate(<?=$i?>,'ConversionRate_','tbl_distribution','distribution')"/>
                </td>
                <td>
                    <input type="text" name="distributionDomesticCurrency[]" id="distributionDomesticCurrency_<?=$i?>" class="text_boxes_numeric" style="width:130px;" onKeyUp="calculate(<?=$i?>,'DomesticCurrency_','tbl_distribution','distribution')" />
                </td>
                <td width="65">
                    <input type="button" id="distributionincrease_<?=$i?>" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fnc_addRow(<?=$i?>,'tbl_distribution','distribution_')" />
                    <input type="button" id="distributiondecrease_<?=$i?>" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fnc_deleteRow(<?=$i?>,'tbl_distribution','distribution_');" />
                </td>
            </tr>
            <?
			$i++;
		}
	}
	else
	{
		?>
        <tr class="general" id='distribution_1' align="center">
            <td> 
                <input type="text" name="cbodistributionHead[]" id="cbodistributionHead_1" class="text_boxes" style="width:170px;" onFocus="add_auto_complete( 1 )"  onBlur="fn_value_check(1,2,this.value,'cbodistributionHead')"  onDblClick="fn_commercial_head_display(1,2,'cbodistributionHead')"  placeholder="Browse Or Write" />
            </td>
            <td>
                <input type="text" name="acLoanNo[]" id="acLoanNo_1" class="text_boxes" style="width:120px;" />
                <input type="hidden" name="pre_export_dtls_id" id="pre_export_dtls_id"  />
            </td>
            <td id="dis_per_td" style="display:none">
            <input type="text" id="txtdispersent_1" name="txtdispersent[]" class="text_boxes_numeric" style="width:50px"  readonly />
            </td>
            <td>
                <input type="text" name="distributionDocumentCurrency[]" id="distributionDocumentCurrency_1" class="text_boxes_numeric" style="width:130px;" onKeyUp="calculate(1,'DocumentCurrency_','tbl_distribution','distribution')"/>
            </td>
            <td>
                <input type="text" name="distributionConversionRate[]" id="distributionConversionRate_1" class="text_boxes_numeric" style="width:90px;" onKeyUp="calculate(1,'ConversionRate_','tbl_distribution','distribution')"/>
            </td>
            <td>
                <input type="text" name="distributionDomesticCurrency[]" id="distributionDomesticCurrency_1" class="text_boxes_numeric" style="width:130px;" onKeyUp="calculate(1,'DomesticCurrency_','tbl_distribution','distribution')" />
            </td>
            <td width="65">
                <input type="button" id="distributionincrease_1" name="increase[]" style="width:30px" class="formbuttonplasminus" value="+" onClick="fnc_addRow(1,'tbl_distribution','distribution_')" />
                <input type="button" id="distributiondecrease_1" name="decrease[]" style="width:30px" class="formbuttonplasminus" value="-" onClick="fnc_deleteRow(1,'tbl_distribution','distribution_');" />
            </td>
        </tr>
        <?
	}
}


if ($action=="realization_report_print")
{
	//echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');	
	extract($_REQUEST);
	$mst_sql="select a.id as rlz_id, a.received_date, b.bank_ref_no, b.submit_date, b.bank_ref_date, b.buyer_id, c.is_lc, C.lc_sc_id, sum(c.net_invo_value) as bill_value
	from com_export_proceed_realization a, com_export_doc_submission_mst b , com_export_doc_submission_invo c
	where a.invoice_bill_id=b.id and b.id=c.doc_submission_mst_id and b.entry_form=40 and a.status_active=1 and b.status_active=1 and c.status_active=1 and a.id=$data
	group by a.id, a.received_date, b.bank_ref_no, b.submit_date, b.bank_ref_date, b.buyer_id, c.is_lc, c.lc_sc_id";
	//echo $mst_sql;die;
	$mst_sql_result = sql_select($mst_sql);
	$rlz_id=$mst_sql_result[0][csf("rlz_id")];
	$received_date=$mst_sql_result[0][csf("received_date")];
	$bank_ref_no=$mst_sql_result[0][csf("bank_ref_no")];
	$submit_date=$mst_sql_result[0][csf("submit_date")];
	$bank_ref_date=$mst_sql_result[0][csf("bank_ref_date")];
	$buyer_id=$mst_sql_result[0][csf("buyer_id")];
	$is_lc=$mst_sql_result[0][csf("is_lc")];
	$lc_sc_id=$mst_sql_result[0][csf("lc_sc_id")];
	$bill_value=$mst_sql_result[0][csf("bill_value")];
	
	//$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','short_name');
	$buyer_names=return_field_value("buyer_name","lib_buyer","id=$buyer_id","buyer_name");
	if($is_lc==1)
	{
		$lc_sc_no=return_field_value("export_lc_no","com_export_lc","id=$lc_sc_id","export_lc_no");
	}
	else
	{
		$lc_sc_no=return_field_value("contract_no","com_sales_contract","id=$lc_sc_id","contract_no");
	}
	
	$dtls_sql_deduct="select id, type, account_head, ac_loan_no, document_currency, conversion_rate, domestic_currency, distribute_percent from com_export_proceed_rlzn_dtls where status_active=1 and mst_id=$data and type=0 order by type";
	//echo $dtls_sql;die;
	$dtls_sql_deduct_result = sql_select($dtls_sql_deduct);
	
	$dtls_sql_distribute="select id, type, account_head, ac_loan_no, document_currency, conversion_rate, domestic_currency, distribute_percent from com_export_proceed_rlzn_dtls where status_active=1 and mst_id=$data and type=1 order by type";
	//echo $dtls_sql;die;
	$dtls_sql_distribute_result = sql_select($dtls_sql_distribute);
	
	?>
	<table id="" cellspacing="0" cellpadding="0" width="720" rules="all" border="1" style="font-size:12px;">
		<tr>
			<td width="120" style="font-weight:bold">Bill/Invoice No</td>
            <td width="120"><?= $bank_ref_no;?></td>
            <td width="120" style="font-weight:bold">Buyer</td>
            <td width="120"><?= $buyer_names;?></td>
            <td width="120" style="font-weight:bold">Received Date</td>
            <td><?= change_date_format($received_date);?></td>
		</tr>
        <tr>
        	<td style="font-weight:bold">LC/SC No</td>
            <td><?= $lc_sc_no;?></td>
            <td style="font-weight:bold"> Bill/Invoice Amount </td>
            <td align="right"><?= number_format($bill_value,2);?></td>
            <td style="font-weight:bold">Bill/Invoice Date</td>
            <td><?= change_date_format($submit_date);?></td>
        </tr>
	</table>
    <?
	if(count($dtls_sql_deduct_result)>0)
	{
		?>
        <p style="font-size:16px; font-weight:bold;">Deductions at Source</p>
		<table cellspacing="0" cellpadding="0" class="rpt_table" width="720" rules="all" border="1" style="font-size:12px;">
            <thead>
                <tr bgcolor="#99CCFF">
                    <th width="50">SL</th>
                    <th width="200">Account Head</th>
                    <th width="120">Document Currency</th>
                    <th width="120">Conversion Rate</th>
                    <th width="120">Domestic Currency</th>
                    <th>%</th>
                </tr>
            </thead>
            <tbody>
                <?
                $i=1;
                foreach($dtls_sql_deduct_result as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$doc_percent=$row[csf("domestic_currency")]/($bill_value*$row[csf("conversion_rate")])*100;
                    ?>
                    <tr bgcolor="<?=$bgcolor;?>">
                        <td align="center"><?= $i;?></td>
                        <td><p><? echo $commercial_head[$row[csf("account_head")]];?>&nbsp;</p></td>
                        <td align="right"><? echo  number_format($row[csf("document_currency")],2);?></td>
                        <td align="right"><? echo  number_format($row[csf("conversion_rate")],2);?></td>
                        <td align="right"><? echo  number_format($row[csf("domestic_currency")],2);?></td>
                        <td align="right" title="<?= "Domestic Currency/(Bill/Invoice Amount *Conversion Rate)*100";?>"><? echo  number_format($doc_percent,4);?></td>
                    </tr>
                    <?
                    $i++;
                    $total_document_currency+=$row[csf("document_currency")];
                    $total_domestic_currency+=$row[csf("domestic_currency")];
                    $total_distribute_percent+=$row[csf("distribute_percent")];
                }
                ?>
                <tr bgcolor="#FFFF99">
                        <td colspan="2" align="right">Deductions at Source Total</td>
                        <td align="right"><? echo  number_format($total_document_currency,2);?></td>
                        <td align="right">&nbsp;</td>
                        <td align="right"><? echo  number_format($total_domestic_currency,2);?></td>
                        <td align="right"><? echo  number_format($total_distribute_percent,2);?></td>
                    </tr>
            </tbody>
        </table>
        <?
	}
	if(count($dtls_sql_distribute_result)>0)
	{
		?>
        <p style="font-size:16px; font-weight:bold;">Distributions</p>
		<table class="rpt_table" cellspacing="0" cellpadding="0" width="720" rules="all" border="1" style="font-size:12px;">
            <thead>
                <tr bgcolor="#99CCFF">
                    <th width="50">SL</th>
                    <th width="200">Account Head</th>
                    <th width="120">Document Currency</th>
                    <th width="120">Conversion Rate</th>
                    <th width="120">Domestic Currency</th>
                    <th>%</th>
                </tr>
            </thead>
            <tbody>
                <?
                $i=1;
                $total_document_currency=$total_domestic_currency=$total_distribute_percent=0;
                foreach($dtls_sql_distribute_result as $row)
                {
                    if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					$doc_percent=$row[csf("domestic_currency")]/($bill_value*$row[csf("conversion_rate")])*100;
                    ?>
                    <tr bgcolor="<?=$bgcolor;?>">
                        <td align="center"><?= $i;?></td>
                        <td><p><? echo $commercial_head[$row[csf("account_head")]];?>&nbsp;</p></td>
                        <td align="right"><? echo  number_format($row[csf("document_currency")],2);?></td>
                        <td align="right"><? echo  number_format($row[csf("conversion_rate")],2);?></td>
                        <td align="right"><? echo  number_format($row[csf("domestic_currency")],2);?></td>
                        <td align="right" title="<?= "Domestic Currency/(Bill/Invoice Amount *Conversion Rate)*100";?>"><? echo  number_format($doc_percent,4);?></td>
                    </tr>
                    <?
                    $i++;
                    $total_document_currency+=$row[csf("document_currency")];
                    $total_domestic_currency+=$row[csf("domestic_currency")];
                    $total_distribute_percent+=$row[csf("distribute_percent")];
                }
                ?>
                <tr bgcolor="#FFFF99">
                        <td colspan="2" align="right">Distributions Total</td>
                        <td align="right"><? echo  number_format($total_document_currency,2);?></td>
                        <td align="right">&nbsp;</td>
                        <td align="right"><? echo  number_format($total_domestic_currency,2);?></td>
                        <td align="right"><? echo  number_format($total_distribute_percent,2);?></td>
                    </tr>
            </tbody>
        </table>
        <table id="" cellspacing="0" cellpadding="0" width="300" rules="all" border="1" style="font-size:12px; margin-top:20px;">
            <tr>
            	<td width="150" style="font-weight:bold">Invoice Value</td>
                <td align="right"><?= number_format($bill_value,2);?></td>
            </tr>
            <tr>
            	<td style="font-weight:bold">Document Currency</td>
                <td align="right"><?= number_format($total_document_currency,2);?></td>
            </tr>
            <tr>
            	<td style="font-weight:bold">Domestic Currency</td>
                <td align="right"><?= number_format($total_domestic_currency,2);?></td>
            </tr>
        </table>
        <?
	}
	exit();
}


?>
