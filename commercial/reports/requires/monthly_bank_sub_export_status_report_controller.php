<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];


//Company Details


if ($action=="load_drop_down_buyer")
{
	echo create_drop_down( "cbo_buyer_name", 150, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0); 
	die;
}

if($action=="report_generate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$con = connect();
    if($db_type==0)
    {
      mysql_query("BEGIN");
    } 
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$rpt_type=str_replace("'","",$rpt_type);
	$cbo_submission_type=str_replace("'","",$cbo_submission_type);
	
	$r_id3=execute_query("delete from tmp_poid where userid=$user_id");
	if($r_id3) oci_commit($con);
	
	
	$sql = "SELECT id, company_name, company_short_name, group_id FROM lib_company";
	$result = sql_select($sql);
	$company_arr = array();
	foreach($result as $row)
	{
		$company_arr[$row[csf('id')]]['full'] = $row[csf('company_name')];
		$company_arr[$row[csf('id')]]['short'] = $row[csf('company_short_name')];
	}
	$bank_arr=return_library_array( "select id, bank_name from lib_bank",'id','bank_name');
	$buyer_arr=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$com_cond="";
	if($cbo_company_name) $com_cond=" and beneficiary_name=$cbo_company_name";
	$lcDataArray=sql_select("SELECT id, export_lc_no, internal_file_no, lien_bank, lc_year FROM com_export_lc where status_active=1 $com_cond");
	$lc_arr = array();
	foreach($lcDataArray as $row)
	{
		$lc_arr[$row[csf('id')]]['lc'] = $row[csf('export_lc_no')];
		$lc_arr[$row[csf('id')]]['file'] = $row[csf('internal_file_no')];
		$lc_arr[$row[csf('id')]]['lien_bank'] = $row[csf('lien_bank')];
		$lc_arr[$row[csf('id')]]['file_year'] = $row[csf('lc_year')];
	}
	
	$scDataArray=sql_select("SELECT id, contract_no, internal_file_no, lien_bank, sc_year FROM com_sales_contract where status_active=1 $com_cond");
	$sc_arr = array();
	foreach($scDataArray as $row)
	{
		$sc_arr[$row[csf('id')]]['sc'] = $row[csf('contract_no')];
		$sc_arr[$row[csf('id')]]['file'] = $row[csf('internal_file_no')];
		$sc_arr[$row[csf('id')]]['lien_bank'] = $row[csf('lien_bank')];
		$sc_arr[$row[csf('id')]]['file_year'] = $row[csf('sc_year')];
	}
	$com_cond_inv="";
	if($cbo_company_name) $com_cond_inv=" and benificiary_id=$cbo_company_name";
	$invoiceDataArray=sql_select("SELECT id, invoice_no, net_invo_value, invoice_date, ex_factory_date, is_lc, lc_sc_id, bl_no, bl_date FROM com_export_invoice_ship_mst where status_active=1 $com_cond_inv");
	$invoice_arr = array();
	foreach($invoiceDataArray as $row)
	{
		$invoice_arr[$row[csf('id')]]['no'] = $row[csf('invoice_no')];
		$invoice_arr[$row[csf('id')]]['inv_date'] = $row[csf('invoice_date')];
		$invoice_arr[$row[csf('id')]]['exf_date'] = $row[csf('ex_factory_date')];
		$invoice_arr[$row[csf('id')]]['value'] = $row[csf('net_invo_value')];
		$invoice_arr[$row[csf('id')]]['is_lc'] = $row[csf('is_lc')];
		$invoice_arr[$row[csf('id')]]['lc_sc_id'] = $row[csf('lc_sc_id')];
		$invoice_arr[$row[csf('id')]]['bl_no'] = $row[csf('bl_no')];
		$invoice_arr[$row[csf('id')]]['bl_date'] = $row[csf('bl_date')];
	}
	
	$sql_realization="select d.invoice_bill_id as INVOICE_BILL_ID, d.received_date as RECEIVED_DATE, e.type as TYPE, e.document_currency as DOCUMENT_CURRENCY, e.account_head as ACCOUNT_HEAD
	from com_export_proceed_realization d, com_export_proceed_rlzn_dtls e 
	where d.id=e.mst_id and d.is_invoice_bill=1 and d.status_active=1 and d.is_deleted=0 and e.status_active=1 and e.is_deleted=0";
	$sql_realization_result=sql_select($sql_realization);
	foreach($sql_realization_result as $row)
	{
		$realize_arr[$row["INVOICE_BILL_ID"]]['date'] = $row[csf('received_date')];
		if($row["TYPE"]==0)
		{
			$realize_arr[$row["INVOICE_BILL_ID"]]['shortrealized'] += $row["DOCUMENT_CURRENCY"];
		}
		else
		{
			$realize_arr[$row["INVOICE_BILL_ID"]]['realized'] += $row["DOCUMENT_CURRENCY"];
			if($row["ACCOUNT_HEAD"]==5)
			{
				$realize_arr[$row["INVOICE_BILL_ID"]]['mergin_lc'] += $row["DOCUMENT_CURRENCY"];
			}
			elseif($row["ACCOUNT_HEAD"]==6)
			{
				$realize_arr[$row["INVOICE_BILL_ID"]]['erq'] += $row["DOCUMENT_CURRENCY"];
			}
			elseif($row["ACCOUNT_HEAD"]==49)
			{
				$realize_arr[$row["INVOICE_BILL_ID"]]['source_tax'] += $row["DOCUMENT_CURRENCY"];
			}
			elseif($row["ACCOUNT_HEAD"]==129)
			{
				$realize_arr[$row["INVOICE_BILL_ID"]]['rmg'] += $row["DOCUMENT_CURRENCY"];
			}
			else
			{
				$realize_arr[$row["INVOICE_BILL_ID"]]['others'] += $row["DOCUMENT_CURRENCY"];
			}
		}
	}
	unset($sql_realization_result);
	
	
	$purchase_amnt_arr=return_library_array( "select doc_submission_mst_id, sum(lc_sc_curr) as purchase_amnt from com_export_doc_sub_trans where status_active=1 and is_deleted=0 group by doc_submission_mst_id",'doc_submission_mst_id','purchase_amnt');
	
	if(str_replace("'","",$cbo_lien_bank)==0) $lien_bank_id="%%"; else $lien_bank_id=str_replace("'","",$cbo_lien_bank);
	
	if(str_replace("'","",$cbo_buyer_name)==0)
	{
		if ($_SESSION['logic_erp']["data_level_secured"]==1)
		{
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond=" and a.buyer_id in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
			if($_SESSION['logic_erp']["buyer_id"]!="") $buyer_id_cond_lc=" and buyer_name in (".$_SESSION['logic_erp']["buyer_id"].")"; else $buyer_id_cond="";
		}
		else
		{
			$buyer_id_cond="";
			$buyer_id_cond_lc="";
		}
	}
	else
	{
		$buyer_id_cond=" and a.buyer_id=$cbo_buyer_name";
		$buyer_id_cond_lc=" and buyer_name=$cbo_buyer_name";
	}
	
	
	
	$based_on=array(0=>"Invoice Date",1=>"Submission Date",2=>"Purchase Date",3=>"Realization Date",4=>"Ex-Factory Date");
	
	if($rpt_type==1)
	{
		ob_start();
		?>
        <div style="width:2540px;">
            <fieldset style="width:100%;">	 
                <table width="2490" cellpadding="0" cellspacing="0" id="caption">
                    <tr>
                       <td align="center" width="100%" colspan="20" class="form_caption" style="font-size:16px"><strong><? echo $company_arr[$cbo_company_name]['full']; ?></strong></td>
                    </tr> 
                    <tr>  
                       <td align="center" width="100%" colspan="20" class="form_caption" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
                    </tr>  
                    <tr> 
                       <td align="center" width="100%" colspan="20" class="form_caption" style="font-size:16px"><strong><? echo "Based On: ".$based_on[str_replace("'","",$cbo_based)]; ?></strong></td>
                    </tr>
                </table>
                <br />
                <?
				$i=1; 
				if($cbo_submission_type>0)
				{
					?>
                    <table width="2510" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                        <thead>
                            <tr>
                                <th width="50" rowspan="2">Sl</th>
                                <th width="80" rowspan="2">Company</th>
                                <th width="60" rowspan="2">File No.</th>
                                <th width="60" rowspan="2">File year.</th>
                                <th width="120" rowspan="2">LC/SC No.</th>
                                <th width="100" rowspan="2">LC Pay Term</th>
                                <th width="100" rowspan="2">LC Tenor</th>
                                <th width="100" rowspan="2">Invoice No.</th>
                                <th width="90" rowspan="2"><? if(str_replace("'","",$cbo_based)==4) echo $based_on[str_replace("'","",$cbo_based)]; else echo "Invoice Date"; ?></th>
                                <th width="110" rowspan="2">Invoice Value</th>
                                <th width="100" rowspan="2">BL/Cargo No</th>
                                <th width="90" rowspan="2">BL/Cargo Date</th>
                                <th width="90" rowspan="2">Doc Sub Date</th>
                                <th width="100" rowspan="2">Bill/FDBC  No.</th>
                                <th width="90" rowspan="2">Poss. Rlz. Date</th>
                                <th width="100">Collection</th>
                                <th width="390" colspan="4"> Sub. Under Purchase</th>
                                <th width="300" colspan="3">Realized</th>
                                <th width="100" rowspan="2">Balance</th>
                                <th width="110" rowspan="2">Buyer</th>
                                <th rowspan="2">Lien Bank</th>
                            </tr>
                            <tr>
                                <th width="100">Amount</th>
                                <th width="120">Bill Amount</th>
                                <th width="110">Purchase Amount</th>
                                <th width="80">(%)</th>
                                <th width="80">Purchase Date</th>
                                <th width="120">Amount</th>
                                <th width="80">Date</th>
                                <th width="100">Short Realization</th>
                            </tr>
                        </thead>
                    </table>
                    <div style="width:2530px; max-height:300px; overflow-y:scroll" id="scroll_body">
                        <table width="2510" class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all">
                        <?
                            if(str_replace("'","",$cbo_based)==0) $search_field="c.invoice_date";
                            else if(str_replace("'","",$cbo_based)==1) $search_field="a.submit_date";
                            else if(str_replace("'","",$cbo_based)==2) $search_field="a.negotiation_date";
                            else if(str_replace("'","",$cbo_based)==3) $search_field="d.received_date";
                            else if(str_replace("'","",$cbo_based)==4) $search_field="c.ex_factory_date";
                            else if(str_replace("'","",$cbo_based)==5) $search_field="a.possible_reali_date";
                            if($cbo_company_name>0) $company_cond="  and a.company_id=$cbo_company_name"; else  $company_cond="";
                            if($db_type==0)
                            {
                                if(str_replace("'","",$cbo_based)==3)
                                {
                                    $sql="select a.id, a.company_id, a.buyer_id, a.lien_bank, a.submit_date, a.negotiation_date, a.bank_ref_no, group_concat(distinct(b.invoice_id)) as invoice_id, c.is_lc, c.lc_sc_id,a.possible_reali_date, 
                                            sum(case when a.submit_type=1 then b.net_invo_value else 0 end) as doc_collection,
                                            sum(case when a.submit_type=2 then b.net_invo_value else 0 end) as bill_amt
                                        from com_export_doc_submission_mst a, com_export_doc_submission_invo b, com_export_invoice_ship_mst c, com_export_proceed_realization d 
                                        where a.id=b.doc_submission_mst_id and b.invoice_id=c.id and a.id=d.invoice_bill_id and a.entry_form=40 and a.lien_bank like '$lien_bank_id' and d.is_invoice_bill=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.status_active=1 and d.is_deleted=0 and $search_field between $txt_date_from and $txt_date_to $buyer_id_cond $company_cond 
                                        group by a.id";
                                }
                                else
                                {
                                    $sql="select a.id, a.submit_type, a.company_id, a.buyer_id, a.lien_bank, a.submit_date, a.negotiation_date, a.bank_ref_no, group_concat(distinct(b.invoice_id)) as invoice_id, c.is_lc, c.lc_sc_id,c.bl_no,c.bl_date,a.possible_reali_date, 
                                            sum(case when a.submit_type=1 then b.net_invo_value else 0 end) as doc_collection,
                                            sum(case when a.submit_type=2 then b.net_invo_value else 0 end) as bill_amt
                                        from com_export_doc_submission_mst a, com_export_doc_submission_invo b, com_export_invoice_ship_mst c 
                                        where a.id=b.doc_submission_mst_id and b.invoice_id=c.id and a.entry_form=40 and a.lien_bank like '$lien_bank_id' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and $search_field between $txt_date_from and $txt_date_to $buyer_id_cond $company_cond 
                                        group by a.id";
                                }
                            }
                            else if($db_type==2)
                            {
                                if(str_replace("'","",$cbo_based)==3)
                                {
                                    $sql="select a.id, a.company_id, a.buyer_id, a.lien_bank, a.submit_date, a.negotiation_date, a.bank_ref_no, LISTAGG(CAST(b.invoice_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.invoice_id) as invoice_id, c.is_lc, c.lc_sc_id,a.possible_reali_date,
                                    sum(case when a.submit_type=1 then b.net_invo_value else 0 end) as doc_collection,
                                    sum(case when a.submit_type=2 then b.net_invo_value else 0 end) as bill_amt
                                    from com_export_doc_submission_mst a, com_export_doc_submission_invo b, com_export_invoice_ship_mst c, com_export_proceed_realization d 
                                    where a.id=b.doc_submission_mst_id and b.invoice_id=c.id and a.entry_form=40 and a.lien_bank like '$lien_bank_id' and a.id=d.invoice_bill_id and d.is_invoice_bill=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.status_active=1 and d.is_deleted=0 and $search_field between $txt_date_from and $txt_date_to $buyer_id_cond  $company_cond
                                    group by a.id, a.company_id, a.buyer_id, a.lien_bank, a.submit_date, a.negotiation_date, a.bank_ref_no, a.possible_reali_date, c.is_lc,  c.lc_sc_id";
                                }
                                else
                                {
                                    $sql="SELECT a.id, a.submit_type, a.company_id, a.buyer_id, a.lien_bank, a.submit_date, a.negotiation_date, a.bank_ref_no, LISTAGG(CAST(b.invoice_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.invoice_id) as invoice_id, c.is_lc, c.lc_sc_id,a.possible_reali_date,
                                    sum(case when a.submit_type=1 then b.net_invo_value else 0 end) as doc_collection,
                                    sum(case when a.submit_type=2 then b.net_invo_value else 0 end) as bill_amt
                                    from com_export_doc_submission_mst a, com_export_doc_submission_invo b, com_export_invoice_ship_mst c 
                                    where a.id=b.doc_submission_mst_id and b.invoice_id=c.id and a.entry_form=40 and a.lien_bank like '$lien_bank_id' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and $search_field between $txt_date_from and $txt_date_to $buyer_id_cond $company_cond
                                    group by a.id, a.submit_type, a.company_id, a.buyer_id, a.lien_bank, a.submit_date, a.negotiation_date, a.bank_ref_no, a.possible_reali_date, c.is_lc, c.lc_sc_id";
                                }
                            }
                            //echo $sql;
                            $lc_sql="SELECT export_lc_no, lc_year, pay_term, tenor from com_export_lc where beneficiary_name='$cbo_company_name' $buyer_id_cond_lc";
                            $res=sql_select($lc_sql);
                            $pay_term_arr = array();
                            $tenor_arr = array();
                            foreach($res as $row)
                            {
                                $pay_term_arr[$row[csf('lc_year')]][$row[csf('export_lc_no')]]=$row[csf('pay_term')];
                                $tenor_arr[$row[csf('lc_year')]][$row[csf('export_lc_no')]]=$row[csf('tenor')];
                            } 
    
                            /*echo "<pre>";
                            print_r($pay_tenor_arr); */
    
                            $sc_sql="SELECT contract_no, sc_year, pay_term, tenor from com_sales_contract where beneficiary_name='$cbo_company_name' $buyer_id_cond_lc";
                            $sc_res=sql_select($sc_sql);
                            $sc_pay_term_arr = array();
                            $sc_tenor_arr = array();
                            foreach($sc_res as $row)
                            {
                                $sc_pay_term_arr[$row[csf('sc_year')]][$row[csf('contract_no')]]=$row[csf('pay_term')];
                                $sc_tenor_arr[$row[csf('sc_year')]][$row[csf('contract_no')]]=$row[csf('tenor')];
                            } 
    
                            /*echo "<pre>";
                            print_r($sc_tenor_arr);*/ 
    
                            $result=sql_select($sql);
                            $invoice_id_array=array();
                            $total_inv_value=0; $total_doc_collection=0; $total_bill_amt=0; $total_purchase_amt=0; $total_realized_amt=0; $total_short_ship=0; $total_balance=0;
                            foreach($result as $row)
                            {
                                if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
                                
                                $file_no=$file_year='';
                                if($row[csf('is_lc')]==1) $file_no=$lc_arr[$row[csf('lc_sc_id')]]['file']; 
                                else $file_no=$sc_arr[$row[csf('lc_sc_id')]]['file'];
                                if($row[csf('is_lc')]==1) $file_year=$lc_arr[$row[csf('lc_sc_id')]]['file_year']; 
                                else $file_year=$sc_arr[$row[csf('lc_sc_id')]]['file_year']; 
    
                                /*echo "<pre>";
                                print_r($sc_pay_term_arr); */
    
                                ?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                                    <td width="50"><? echo $i;?></td>
                                    <td width="80"><? echo $company_arr[$row[csf('company_id')]]['short']; ?></td>
                                    <td width="60" align="center"><p><? echo $file_no; ?></p></td>
                                    <td width="60" align="center"><p><? echo $file_year; ?></p></td>
                                    <td width="818">
                                        <table width="100%" cellspacing="0" cellpadding="0" border="0" rules="all">
                                        <?
                                            $all_invoice_id=explode(",",$row[csf('invoice_id')]);
                                            $all_invoice_id=array_unique($all_invoice_id);
                                            $s=1; $sub_inv_value=0;
                                            foreach($all_invoice_id as $invoice_id)
                                            {
                                                if($s==1) $top_style="border-top:none"; else $top_style="";
                                                
                                                $is_lc_sc=$invoice_arr[$invoice_id]['is_lc']; 
                                                
                                                $lc_sc_no='';
                                                
                                                if($is_lc_sc==1) 
                                                {
                                                    $lc_sc_no=$lc_arr[$invoice_arr[$invoice_id]['lc_sc_id']]['lc']; 
                                                    $lc_sc_pay_term=$pay_term[$pay_term_arr[$file_year][$lc_sc_no]];
                                                    $lc_sc_tenor=$tenor_arr[$file_year][$lc_sc_no]; 
                                                }
                                                else 
                                                {
                                                    $lc_sc_no=$sc_arr[$invoice_arr[$invoice_id]['lc_sc_id']]['sc']; 
                                                    $lc_sc_pay_term=$pay_term[$sc_pay_term_arr[$file_year][$lc_sc_no]];
                                                    $lc_sc_tenor=$sc_tenor_arr[$file_year][$lc_sc_no];
                                                }
    
                                                if(!in_array($invoice_id,$invoice_id_array))
                                                {
                                                    $invoice_id_array[]=$invoice_id;
                                                }
                                                ?>
                                                <tr>
                                                    <td width="120" style="border-left:none; border-bottom:none;<? echo $top_style; ?>"><p><? echo $lc_sc_no; ?></p></td>
                                                    <td width="100" style="border-left:none; border-bottom:none;<? echo $top_style; ?>"><p><? echo $lc_sc_pay_term; ?></p></td>
                                                    <td width="100" style="border-left:none; border-bottom:none;<? echo $top_style; ?>"><p> <? echo $lc_sc_tenor; ?></p></td>
                                                    <td width="100" style="border-bottom:none;<? echo $top_style; ?>"><p><? echo $invoice_arr[$invoice_id]['no']; ?></p></td>
                                                    <td width="90" align="center" style="border-bottom:none;<? echo $top_style; ?>">
                                                        <? 
                                                            if(str_replace("'","",$cbo_based)==4)
                                                                echo change_date_format($invoice_arr[$invoice_id]['exf_date']); 
                                                            else 
                                                                echo change_date_format($invoice_arr[$invoice_id]['inv_date']); 	
                                                        ?>
                                                    </td>
                                                    <td style="border-right:none; border-bottom:none;<? echo $top_style; ?>" align="right">
                                                        <?
                                                            echo number_format($invoice_arr[$invoice_id]['value'],2,'.','');
                                                        ?>
                                                    </td>
                                                    <? 
                                                        if(str_replace("'","",$cbo_based)==1)
                                                        {
                                                            ?>
                                                            <td width="100" style="word-wrap: break-word;word-break: break-all; border-right:none; border-bottom:none; border-top:none;" align="center"><? echo $invoice_arr[$invoice_id]['bl_no'];?></td>
                                                            <td width="90" style="word-wrap: break-word;word-break: break-all; border-right:none; border-bottom:none; border-top:none;" align="center"><? echo change_date_format($invoice_arr[$invoice_id]['bl_date']); ?></td>
                                                            <? 
                                                        }
                                                        else{
                                                            
                                                            ?>
                                                            <td width="100" style="word-wrap: break-word;word-break: break-all; border-right:none; border-bottom:none; border-top:none;" align="center"><? echo $invoice_arr[$invoice_id]['bl_no'];?></td>
                                                            <td width="90" style="word-wrap: break-word;word-break: break-all; border-right:none; border-bottom:none; border-top:none;" align="center"><? echo change_date_format($invoice_arr[$invoice_id]['bl_date']); ?></td>
                                                            <?
                                                        }
                                                    ?>
                                                </tr>
                                                <?
                                                $s++;
                                                $sub_inv_value+=$invoice_arr[$invoice_id]['value'];
                                            }
                                        ?>
                                        </table>
                                    </td> 
                                    <td width="90" align="center"><? echo change_date_format($row[csf('submit_date')]); ?></td>
                                    <td width="100"><p><? echo $row[csf('bank_ref_no')]; ?></p></td>
                                    <td width="90" align="center"><p><? if($row[csf('possible_reali_date')]!="" && $row[csf('possible_reali_date')]!="0000-00-00") echo change_date_format($row[csf('possible_reali_date')]); ?></p></td>
                                    <td width="100" align="right"><? echo number_format($row[csf('doc_collection')],2,'.',''); ?></td>
                                    <td width="120" align="right"><? echo number_format($row[csf('bill_amt')],2,'.',''); ?></td>
                                    <?
                                    if(!in_array($row[csf('bank_ref_no')],$temp_array))
                                    {
                                        $temp_array[]=$row[csf('bank_ref_no')];
                                        ?>
                                        <td width="110" align="right"><a href="##" onclick="openmypage2(<? echo "'".$row[csf('id')]."'"  ?>,'purchase_popup_qnty')"><? echo number_format($purchase_amnt_arr[$row[csf('id')]],2,'.',''); $total_purchase_amt += $purchase_amnt_arr[$row[csf('id')]];  ?></a></td>
                                        <td width="80" align="right">
                                            <?
                                                $purcase_perc = ($purchase_amnt_arr[$row[csf('id')]]/$row[csf('bill_amt')])*100;
                                                echo number_format($purcase_perc,2,'.','')."";
                                            ?>
                                        </td>
                                        <td width="80" align="center"><? if($row[csf('negotiation_date')]!="0000-00-00" && $row[csf('negotiation_date')]!="") echo change_date_format($row[csf('negotiation_date')]); ?></td>
                                        <td width="120" align="right"><? echo number_format($realize_arr[$row[csf('id')]]['realized'],2,'.',''); $total_realized_amt += $realize_arr[$row[csf('id')]]['realized']; ?></td>
                                        <td width="80" align="center"><? if($realize_arr[$row[csf('id')]]['date']!="0000-00-00" && $realize_arr[$row[csf('id')]]['date']!="") echo change_date_format($realize_arr[$row[csf('id')]]['date']); ?></td>
                                        <td width="100" align="right"><? echo number_format($realize_arr[$row[csf('id')]]['shortrealized'],2,'.',''); $total_short_ship += $realize_arr[$row[csf('id')]]['shortrealized']; ?></td>
                                        
                                    <?
                                    }
                                    else
                                    {
                                        ?>
                                        <td width="110" align="right"></td>
                                        <td width="80" align="right">
                                            <?
                                                $purcase_perc = ($purchase_amnt_arr[$row[csf('id')]]/$row[csf('bill_amt')])*100;
                                                echo number_format($purcase_perc,2,'.','')."";
                                            ?>
                                        </td>
                                        <td width="80" align="center"><? if($row[csf('negotiation_date')]!="0000-00-00" && $row[csf('negotiation_date')]!="") echo change_date_format($row[csf('negotiation_date')]); ?></td>
                                        <td width="120" align="right"></td>
                                        <td width="80" align="center"><? if($realize_arr[$row[csf('id')]]['date']!="0000-00-00" && $realize_arr[$row[csf('id')]]['date']!="") echo change_date_format($realize_arr[$row[csf('id')]]['date']); ?></td>
                                        <td width="100" align="right"></td>
                                        
                                        <?
                                    }
                                    ?>
                                    <td width="100" align="right">
                                        <?
                                            if($row[csf('doc_collection')]>0)
                                            {
                                                $balance = $row[csf('doc_collection')]-($realize_arr[$row[csf('id')]]['realized']+$realize_arr[$row[csf('id')]]['shortrealized']);
                                            }
                                            else
                                            {
                                                $balance = $row[csf('bill_amt')]-($realize_arr[$row[csf('id')]]['realized']+$realize_arr[$row[csf('id')]]['shortrealized']);
                                            }
                                            echo number_format($balance,2)."";
                                         ?>
                                    </td>
                                    <td width="110"><p><? echo $buyer_arr[$row[csf('buyer_id')]]; ?></p></td>
                                    <td><p><? echo $bank_arr[$row[csf('lien_bank')]]; ?></p></td>
                                </tr>
                            <?
                                $i++;	
                                
                                $total_inv_value += $sub_inv_value;
                                $total_doc_collection += $row[csf('doc_collection')];
                                $total_bill_amt += $row[csf('bill_amt')];
                                $total_balance += $balance;
                            }
                            ?>
                            <tfoot>
                                <th colspan="4" align="right"><b>Total</b></th>
                                <th width="613">
                                    <table width="100%" rules="all" class="rpt_table">
                                        <tr>
                                            <td width="423" style="border-top:none; " width="100%" colspan="4" align="right"><?php echo number_format($total_inv_value,2);?></td>
                                            <td width="100"></td>
                                            <td width="90"></td> 
                                        </tr>
                                    </table>
                                </th>
                                <th colspan="3"></th>
                                <th align="right"><?php echo number_format($total_doc_collection,2); ?></th>
                                <th align="right"><?php echo number_format($total_bill_amt,2); ?></th>
                                <th align="right"><?php echo number_format($total_purchase_amt,2); ?></th> 
                                <th colspan="2"></th> 				
                                <th align="right"><?php echo number_format($total_realized_amt,2); ?></th>
                                <th></th>
                                <th align="right"><?php echo number_format($total_short_ship,2); ?></th>
                                <th align="right"><?php echo number_format($total_balance,2); ?></th>
                                <th></th>
                                <th></th>	
                            </tfoot>	
                        </table>
                    </div>
                    <?
				}
				?>
                
                <br />
                <?
				if((str_replace("'","",$cbo_based)==0 || str_replace("'","",$cbo_based)==4) && ($cbo_submission_type!=1))
				{
					if(str_replace("'","",$cbo_based)==0) $search_field="a.invoice_date";
					else if(str_replace("'","",$cbo_based)==4) $search_field="a.ex_factory_date";
					?>
                	<b>Un-Submitted Invoice</b>
					<table width="1590" border="1" cellspacing="0" cellpadding="0" class="rpt_table" rules="all">
						<thead>
							<th width="70">Company</th>
                            <th width="120">Buyer</th>
							<th width="60">File No</th>
                            <th width="60">File year</th>
                            <th width="220">Style No</th>
							<th width="120">LC/SC No</th>
							<th width="100">LC Pay Term</th>
							<th width="100">LC Tenor</th>
							<th width="120">Invoice No</th>
							<th width="100">Invoice Value</th>
							<th width="80"><? echo $based_on[str_replace("'","",$cbo_based)]; ?></th>
                            <th width="80">BL Date</th>
                            <th width="80">Deviation</th>
							<th>Lien Bank</th>
						</thead>
					</table>
                    <div style="width:1570px; max-height:300px; overflow-y:scroll" id="scroll_body_bottom">
                    	<table width="1550" border="1" cellspacing="0" cellpadding="0" class="rpt_table" rules="all">
					<?
						if($db_type==0)
						{
							$style_ref_arr=return_library_array("select c.mst_id,group_concat(distinct a.style_ref_no) as style_ref_no from  wo_po_details_master a, wo_po_break_down b,  com_export_invoice_ship_dtls c where a.job_no=b.job_no_mst and b.id=c.po_breakdown_id and c.current_invoice_qnty>0  group by  c.mst_id","mst_id","style_ref_no");
						}
						else
						{
							$sql=sql_select("select c.mst_id,LISTAGG(CAST(a.style_ref_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.style_ref_no) as style_ref_no from  wo_po_details_master a, wo_po_break_down b,  com_export_invoice_ship_dtls c where a.job_no=b.job_no_mst and b.id=c.po_breakdown_id and c.current_invoice_qnty>0  group by  c.mst_id");
							foreach($sql as $row)
							{
								$style=implode(",",array_unique(explode(",",$row[csf('style_ref_no')])));
								$style_ref_arr[$row[csf('mst_id')]]=$style;
							}
						}
						if($cbo_company_name>0) $company_cond="  and  a.benificiary_id=$cbo_company_name"; else  $company_cond="";
						$data_cond="";
						if(str_replace("'","",$txt_date_from)!="" && str_replace("'","",$txt_date_to)!="") $data_cond=" and $search_field between $txt_date_from and $txt_date_to";
                        $query="SELECT a.id, a.benificiary_id, a.invoice_no, a.net_invo_value, a.invoice_date, a.ex_factory_date, a.is_lc, a.lc_sc_id, a.buyer_id, a.bl_date FROM com_export_invoice_ship_mst a where a.is_deleted=0 and a.status_active=1 and  a.id not in(select invoice_id from com_export_doc_submission_invo where status_active=1 and is_deleted=0) $buyer_id_cond $company_cond $data_cond";
						//echo $query;die;
						$cbo_lien_bank=str_replace("'","",$cbo_lien_bank); $tot_unsub_invoice_value=0;
					    $dataArray=sql_select($query);$k=1;
					    foreach($dataArray as $row_uns)
					    {
							$file_no=''; $lc_sc_no=''; $lien_bank='';
							if($row_uns[csf('is_lc')]==1)
							{
								$file_no=$lc_arr[$row_uns[csf('lc_sc_id')]]['file'];
								$lc_sc_no=$lc_arr[$row_uns[csf('lc_sc_id')]]['lc'];
								$lien_bank=$lc_arr[$row_uns[csf('lc_sc_id')]]['lien_bank']; 
								$file_year=$lc_arr[$row_uns[csf('lc_sc_id')]]['file_year'];
								$lc_sc_pay_term=$pay_term[$pay_term_arr[$file_year][$lc_sc_no]];
								$lc_sc_tenor=$tenor_arr[$file_year][$lc_sc_no];
							}
							else 
							{
								$file_no=$sc_arr[$row_uns[csf('lc_sc_id')]]['file'];
								$lc_sc_no=$sc_arr[$row_uns[csf('lc_sc_id')]]['sc']; 
								$lien_bank=$sc_arr[$row_uns[csf('lc_sc_id')]]['lien_bank'];
								$file_year=$sc_arr[$row_uns[csf('lc_sc_id')]]['file_year'];
								$lc_sc_pay_term=$pay_term[$sc_pay_term_arr[$file_year][$lc_sc_no]];
								$lc_sc_tenor=$sc_tenor_arr[$file_year][$lc_sc_no];
							}
							
							if($cbo_lien_bank==0 || $cbo_lien_bank==$lien_bank)
							{
								if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="70"><? echo $company_arr[$row_uns[csf('benificiary_id')]]['short']; ?></td>
                                    <td width="120"><p><? echo $buyer_arr[$row_uns[csf("buyer_id")]]; ?></p></td>
									<td width="60" align="center"><p><? echo $file_no; ?></p></td>
                                    <td width="60" align="center"><p><? echo $file_year; ?></p></td>
                                    <td width="220"><p><? echo $style_ref_arr[$row_uns[csf("id")]]; ?></p></td>
									<td width="120"><p><? echo $lc_sc_no; ?></p></td>
									<td width="100"><p> <? echo $lc_sc_pay_term; ?></p></td>
									<td width="100"><p> <? echo $lc_sc_tenor; ?></p></td>
									<td width="120"><p><? echo $row_uns[csf('invoice_no')]; ?></p></td>
									<td width="100" align="right"><? echo number_format($row_uns[csf('net_invo_value')],2,'.',''); ?></td>
									<td width="80" align="center">
										<?
											if(str_replace("'","",$cbo_based)==4)
												echo change_date_format($row_uns[csf('ex_factory_date')]); 
											else 
												echo change_date_format($row_uns[csf('invoice_date')]); 
										?>
									</td>
                                    <td width="80"><p><? if($row_uns[csf('bl_date')]!="" && $row_uns[csf('bl_date')]!="0000-00-00") echo change_date_format($row_uns[csf('bl_date')]); ?></p></td>
                                    <td width="80" align="center"><p>
									<?
									$diff_bl="";
									if(str_replace("'","",$cbo_based)==4)
									{
										if($row_uns[csf('bl_date')]!="" && $row_uns[csf('bl_date')]!="0000-00-00")
										{
											$diff_bl=datediff("d",$row_uns[csf('ex_factory_date')], $row_uns[csf('bl_date')]);
											$diff_bl=$diff_bl." Days";
										}
									}
									echo $diff_bl;
									 
									?></p></td>
									<td><p><? echo $bank_arr[$lien_bank]; ?></p></td>
								</tr>
							<?
								$i++;  $k++;
								$tot_unsub_invoice_value+=$row_uns[csf('net_invo_value')];
							}
						}
                    	?>
                        <tfoot>
                        	<tr>
                            	<th colspan="9" align="right">Total</th>
                                <th align="right"><?php echo number_format($tot_unsub_invoice_value,2); ?></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                            <tr>
                            	<th colspan="9" align="right">Grand Total</th>
                                <th align="right"><?php echo number_format($total_inv_value+$tot_unsub_invoice_value,2); ?></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                    </div>   
					<?
				}
				else
				{
					echo '<div style="display:none" id="scroll_body_bottom"></div>';
				}
				?>
            </fieldset>
        </div>
		<?
	}
	else
	{
		ob_start();
		?>
        <div style="width:1680px;">
            <fieldset style="width:100%;">	 
                <table width="1680" cellpadding="0" cellspacing="0" id="caption">
                    <tr>
                       <td align="center" width="100%" colspan="20" class="form_caption" style="font-size:16px"><strong><? echo $company_arr[$cbo_company_name]['full']; ?></strong></td>
                    </tr> 
                    <tr>  
                       <td align="center" width="100%" colspan="20" class="form_caption" style="font-size:16px"><strong><? echo $report_title; ?></strong></td>
                    </tr>  
                    <tr> 
                       <td align="center" width="100%" colspan="20" class="form_caption" style="font-size:16px"><strong><? echo "Based On: ".$based_on[str_replace("'","",$cbo_based)]; ?></strong></td>
                    </tr>
                </table>
                <br />
                <table width="1680" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all">
                    <thead>
                        <tr>
                            <th width="50" rowspan="2">Sl</th>
                            <th width="80" rowspan="2">Company</th>
                            <th width="60" rowspan="2">File No.</th>
                            <th width="60" rowspan="2">File year.</th>
                            <th width="90" rowspan="2">Doc Sub Date</th>
                            <th width="100" rowspan="2">Bill/FDBC  No.</th>
                            <th width="90" rowspan="2">Poss. Rlz. Date</th>
                            <th width="100">Collection</th>
                            <th width="300" colspan="3">Realized</th>
                            <th width="100" rowspan="2">Margin AC</th>
                            <th width="100" rowspan="2">ERQ AC</th>
                            <th width="100" rowspan="2">Source Tax</th>
                            <th width="100" rowspan="2">RMG</th>
                            <th width="100" rowspan="2">Balance</th>
                            <th width="110" rowspan="2">Buyer</th>
                            <th rowspan="2">Lien Bank</th>
                        </tr>
                        <tr>
                            <th width="100">Bill Amount</th>
                            <th width="80">Date</th>
                            <th width="100">Short Realization</th>
                            <th width="120">Amount</th>
                        </tr>
                    </thead>
                </table>
                <div style="width:1700px; max-height:300px; overflow-y:scroll" id="scroll_body">
   		 			<table width="1680" class="rpt_table" cellspacing="0" cellpadding="0" border="1" rules="all">
                    <?
						if(str_replace("'","",$cbo_based)==0) $search_field="c.invoice_date";
						else if(str_replace("'","",$cbo_based)==1) $search_field="a.submit_date";
						else if(str_replace("'","",$cbo_based)==2) $search_field="a.negotiation_date";
						else if(str_replace("'","",$cbo_based)==3) $search_field="d.received_date";
						else if(str_replace("'","",$cbo_based)==4) $search_field="c.ex_factory_date";
						else if(str_replace("'","",$cbo_based)==5) $search_field="a.possible_reali_date";
						if($cbo_company_name>0) $company_cond="  and a.company_id=$cbo_company_name"; else  $company_cond="";
						if($db_type==0)
						{
							if(str_replace("'","",$cbo_based)==3)
							{
								$sql="select a.id, a.company_id, a.buyer_id, a.lien_bank, a.submit_date, a.negotiation_date, a.bank_ref_no, group_concat(distinct(b.invoice_id)) as invoice_id, c.is_lc, c.lc_sc_id,a.possible_reali_date, 
										sum(case when a.submit_type=1 then b.net_invo_value else 0 end) as doc_collection,
										sum(case when a.submit_type=2 then b.net_invo_value else 0 end) as bill_amt
									from com_export_doc_submission_mst a, com_export_doc_submission_invo b, com_export_invoice_ship_mst c, com_export_proceed_realization d 
									where a.id=b.doc_submission_mst_id and b.invoice_id=c.id and a.id=d.invoice_bill_id and a.entry_form=40 and a.lien_bank like '$lien_bank_id' and d.is_invoice_bill=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.status_active=1 and d.is_deleted=0 and $search_field between $txt_date_from and $txt_date_to $buyer_id_cond $company_cond 
									group by a.id";
							}
							else
							{
								$sql="select a.id, a.submit_type, a.company_id, a.buyer_id, a.lien_bank, a.submit_date, a.negotiation_date, a.bank_ref_no, group_concat(distinct(b.invoice_id)) as invoice_id, c.is_lc, c.lc_sc_id,c.bl_no,c.bl_date,a.possible_reali_date, 
										sum(case when a.submit_type=1 then b.net_invo_value else 0 end) as doc_collection,
										sum(case when a.submit_type=2 then b.net_invo_value else 0 end) as bill_amt
									from com_export_doc_submission_mst a, com_export_doc_submission_invo b, com_export_invoice_ship_mst c 
									where a.id=b.doc_submission_mst_id and b.invoice_id=c.id and a.entry_form=40 and a.lien_bank like '$lien_bank_id' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and $search_field between $txt_date_from and $txt_date_to $buyer_id_cond $company_cond 
									group by a.id";
							}
						}
						else if($db_type==2)
						{
							if(str_replace("'","",$cbo_based)==3)
							{
								$sql="select A.ID, A.COMPANY_ID, A.BUYER_ID, A.LIEN_BANK, A.SUBMIT_DATE, A.NEGOTIATION_DATE, A.BANK_REF_NO, C.IS_LC, C.LC_SC_ID, A.POSSIBLE_REALI_DATE, A.SUBMIT_TYPE, B.NET_INVO_VALUE, B.INVOICE_ID, B.ID AS DTLS_ID
								from com_export_doc_submission_mst a, com_export_doc_submission_invo b, com_export_invoice_ship_mst c, com_export_proceed_realization d 
								where a.id=b.doc_submission_mst_id and b.invoice_id=c.id and a.entry_form=40 and a.lien_bank like '$lien_bank_id' and a.id=d.invoice_bill_id and d.is_invoice_bill=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.status_active=1 and d.is_deleted=0 and $search_field between $txt_date_from and $txt_date_to $buyer_id_cond  $company_cond";
							}
							else
							{
								$sql="SELECT A.ID, A.COMPANY_ID, A.BUYER_ID, A.LIEN_BANK, A.SUBMIT_DATE, A.NEGOTIATION_DATE, A.BANK_REF_NO, C.IS_LC, C.LC_SC_ID, A.POSSIBLE_REALI_DATE, A.SUBMIT_TYPE, B.NET_INVO_VALUE, B.INVOICE_ID, B.ID AS DTLS_ID
								from com_export_doc_submission_mst a, com_export_doc_submission_invo b, com_export_invoice_ship_mst c 
								where a.id=b.doc_submission_mst_id and b.invoice_id=c.id and a.entry_form=40 and a.lien_bank like '$lien_bank_id' and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and $search_field between $txt_date_from and $txt_date_to $buyer_id_cond $company_cond";
							}
						}
						//echo $sql;die;
						
						$result=sql_select($sql);
						$dtls_data=array();
						foreach($result as $val)
						{
							$dtls_data[$val["ID"]]["id"]=$val["ID"];
							$dtls_data[$val["ID"]]["company_id"]=$val["COMPANY_ID"];
							$dtls_data[$val["ID"]]["buyer_id"]=$val["BUYER_ID"];
							$dtls_data[$val["ID"]]["lien_bank"]=$val["LIEN_BANK"];
							
							$dtls_data[$val["ID"]]["submit_date"]=$val["SUBMIT_DATE"];
							$dtls_data[$val["ID"]]["negotiation_date"]=$val["NEGOTIATION_DATE"];
							$dtls_data[$val["ID"]]["bank_ref_no"]=$val["BANK_REF_NO"];
							$dtls_data[$val["ID"]]["is_lc"]=$val["IS_LC"];
							$dtls_data[$val["ID"]]["lc_sc_id"]=$val["LC_SC_ID"];
							
							$dtls_data[$val["ID"]]["possible_reali_date"]=$val["POSSIBLE_REALI_DATE"];
							$dtls_data[$val["ID"]]["submit_type"]=$val["SUBMIT_TYPE"];
							if($dls_id_check[$val["DTLS_ID"]]=="")
							{
								$dls_id_check[$val["DTLS_ID"]]=$val["DTLS_ID"];
								if($val["SUBMIT_TYPE"]==1) $dtls_data[$val["ID"]]["doc_collection"]+=$val["NET_INVO_VALUE"];
								else if($val["SUBMIT_TYPE"]==2) $dtls_data[$val["ID"]]["bill_amt"]+=$val["NET_INVO_VALUE"];
							}
							if($inv_id_check[$val["INVOICE_ID"]]=="")
							{
								$inv_id_check[$val["INVOICE_ID"]]=$val["INVOICE_ID"];
								$dtls_data[$val["ID"]]["invoice_id"].=$val["INVOICE_ID"].",";
							}
						}
						unset($result);
						$i=1; $invoice_id_array=array();
						$total_inv_value=0; $total_doc_collection=0; $total_bill_amt=0; $total_purchase_amt=0; $total_realized_amt=0; $total_short_ship=0; $total_balance=0;
						foreach($dtls_data as $row)
						{
							if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
							
							$file_no=$file_year='';
							if($row[('is_lc')]==1) $file_no=$lc_arr[$row[('lc_sc_id')]]['file']; 
							else $file_no=$sc_arr[$row[('lc_sc_id')]]['file'];
							if($row[('is_lc')]==1) $file_year=$lc_arr[$row[('lc_sc_id')]]['file_year']; 
							else $file_year=$sc_arr[$row[('lc_sc_id')]]['file_year']; 

							/*echo "<pre>";
							print_r($sc_pay_term_arr); */

							?>
                        	<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                            	<td width="50"><? echo $i;?></td>
                                <td width="80"><? echo $company_arr[$row[('company_id')]]['short']; ?></td>
                                <td width="60" align="center"><p><? echo $file_no; ?></p></td>
                                <td width="60" align="center"><p><? echo $file_year; ?></p></td>
                                <?
								$all_invoice_ids=implode(",",array_unique(explode(",",$row[('invoice_id')])));
								?>
                                <td width="90" align="center"><? echo change_date_format($row[('submit_date')]); ?></td>
                                <td width="100"><p><a href="#report_details" onclick="open_details('<? echo $all_invoice_ids;?>','invoice_details','Invoice Details','600');"><? echo $row[('bank_ref_no')]; ?></a></p></td>
                                <td width="90" align="center"><p><? if($row[('possible_reali_date')]!="" && $row[('possible_reali_date')]!="0000-00-00") echo change_date_format($row[('possible_reali_date')]); ?></p></td>
                                <td width="100" align="right"><? echo number_format($row[('doc_collection')],2,'.',''); ?></td>
                                <?
								if(!in_array($row[('bank_ref_no')],$temp_array))
								{
									$temp_array[]=$row[('bank_ref_no')];
									?>
                                    <td width="80" align="center"><? if($realize_arr[$row[('id')]]['date']!="0000-00-00" && $realize_arr[$row[('id')]]['date']!="") echo change_date_format($realize_arr[$row[('id')]]['date']); ?></td>
                                    <td width="100" align="right"><? echo number_format($realize_arr[$row[('id')]]['shortrealized'],2,'.',''); $total_short_ship += $realize_arr[$row[('id')]]['shortrealized']; ?></td>
                                    <td width="120" align="right"><? echo number_format($realize_arr[$row[('id')]]['realized'],2,'.',''); $total_realized_amt += $realize_arr[$row[('id')]]['realized']; ?></td>
                                    <td width="100" align="right"><? echo number_format($realize_arr[$row[('id')]]['mergin_lc'],2,'.',''); $total_realized_mergin_lc += $realize_arr[$row[('id')]]['mergin_lc']; ?></td>
                                    <td width="100" align="right"><? echo number_format($realize_arr[$row[('id')]]['erq'],2,'.',''); $total_realized_erq += $realize_arr[$row[('id')]]['erq']; ?></td>
                                    <td width="100" align="right"><? echo number_format($realize_arr[$row[('id')]]['source_tax'],2,'.',''); $total_realized_source_tax += $realize_arr[$row[('id')]]['source_tax']; ?></td>
                                    <td width="100" align="right"><? echo number_format($realize_arr[$row[('id')]]['rmg'],2,'.',''); $total_realized_rmg += $realize_arr[$row[('id')]]['rmg']; ?></td>
                                	<?
								}
								else
								{
									?>
                                    <td width="80" align="center"><? if($realize_arr[$row[('id')]]['date']!="0000-00-00" && $realize_arr[$row[('id')]]['date']!="") echo change_date_format($realize_arr[$row[('id')]]['date']); ?></td>
                                    <td width="100" align="right"></td>
                                    <td width="120" align="right"></td>
                                    <td width="100" align="right"></td>
                                    <td width="100" align="right"></td>
                                    <td width="100" align="right"></td>
                                    <td width="100" align="right"></td>
                                    <?
								}
								?>
                                <td width="100" align="right">
								<?
                                    if($row[('doc_collection')]>0)
                                    {
                                        $balance = $row[('doc_collection')]-($realize_arr[$row[('id')]]['realized']+$realize_arr[$row[('id')]]['shortrealized']);
                                    }
                                    else
                                    {
                                        $balance = $row[('bill_amt')]-($realize_arr[$row[('id')]]['realized']+$realize_arr[$row[('id')]]['shortrealized']);
                                    }
                                    echo number_format($balance,2)."";
                                 ?>
                                </td>
                                <td width="110"><p><? echo $buyer_arr[$row[('buyer_id')]]; ?></p></td>
                                <td><p><? echo $bank_arr[$row[('lien_bank')]]; ?></p></td>
                            </tr>
                        	<?
							$i++;	
							
							$total_inv_value += $sub_inv_value;
							$total_doc_collection += $row[('doc_collection')];
							$total_bill_amt += $row[('bill_amt')];
							$total_balance += $balance;
						}
						?>
                        <tfoot>
                            <th colspan="4" align="right"><b>Total</b></th>
                            <th colspan="3"></th>
                            <th align="right"><?php echo number_format($total_doc_collection,2); ?></th>
                            <th></th> 
                            <th align="right"><?php echo number_format($total_short_ship,2); ?></th>				
                            <th align="right"><?php echo number_format($total_realized_amt,2); ?></th>
                            <th align="right"><?php echo number_format($total_realized_mergin_lc,2); ?></th>				
                            <th align="right"><?php echo number_format($total_realized_erq,2); ?></th>
                            <th align="right"><?php echo number_format($total_realized_source_tax,2); ?></th>
                            <th align="right"><?php echo number_format($total_realized_rmg,2); ?></th>
                            <th align="right"><?php echo number_format($total_balance,2); ?></th>
                            <th></th>
                            <th></th>	
                        </tfoot>	
                	</table>
                </div>
                <br />
                <?
				if(str_replace("'","",$cbo_based)==0 || str_replace("'","",$cbo_based)==4)
				{
					if(str_replace("'","",$cbo_based)==0) $search_field="a.invoice_date";
					else if(str_replace("'","",$cbo_based)==4) $search_field="a.ex_factory_date";

					//if(count($invoice_id_array)>0) $invoice_id_cond=" and a.id not in (".implode(",",$invoice_id_array).")"; else $invoice_id_cond="";
					$inv_id="";
					foreach($invoice_id_array as $key=> $inv_id)
					{
						if($inv_id!=0)
						{
							$r_id2=execute_query("insert into tmp_poid (userid, poid, type) values ($user_id,$inv_id,'121')");
							if($r_id2==false)
							{
								oci_rollback($con);
								$rID4=execute_query("delete from tmp_poid where userid=$user_id");
								if($rID4) oci_commit($con);
								disconnect($con);die;
							}
							if($inv_id=="") $inv_id=$inv_id;else $inv_id.=",".$inv_id;
						}
					}
					
					if($r_id2){
						oci_commit($con);
					}else{
						$r_id3=execute_query("delete from tmp_poid where userid=$user_id");
						if($r_id3){oci_commit($con);disconnect($con);die;}
					}
					?>
                	<b>Un-Submitted Invoice</b>
					<table width="1590" border="1" cellspacing="0" cellpadding="0" class="rpt_table" rules="all">
						<thead>
							<th width="70">Company</th>
                            <th width="120">Buyer</th>
							<th width="60">File No</th>
                            <th width="60">File year</th>
                            <th width="220">Style No</th>
							<th width="120">LC/SC No</th>
							<th width="100">LC Pay Term</th>
							<th width="100">LC Tenor</th>
							<th width="120">Invoice No</th>
							<th width="100">Invoice Value</th>
							<th width="80"><? echo $based_on[str_replace("'","",$cbo_based)]; ?></th>
                            <th width="80">BL Date</th>
                            <th width="80">Deviation</th>
							<th>Lien Bank</th>
						</thead>
					</table>
                    <div style="width:1570px; max-height:300px; overflow-y:scroll" id="scroll_body_bottom">
                    	<table width="1550" border="1" cellspacing="0" cellpadding="0" class="rpt_table" rules="all">
					<?
						if($db_type==0)
						{
							$style_ref_arr=return_library_array("select c.mst_id,group_concat(distinct a.style_ref_no) as style_ref_no from  wo_po_details_master a, wo_po_break_down b,  com_export_invoice_ship_dtls c where a.job_no=b.job_no_mst and b.id=c.po_breakdown_id and c.current_invoice_qnty>0  group by  c.mst_id","mst_id","style_ref_no");
						}
						else
						{
							$sql=sql_select("select c.mst_id,LISTAGG(CAST(a.style_ref_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.style_ref_no) as style_ref_no from  wo_po_details_master a, wo_po_break_down b,  com_export_invoice_ship_dtls c where a.job_no=b.job_no_mst and b.id=c.po_breakdown_id and c.current_invoice_qnty>0  group by  c.mst_id");
							foreach($sql as $row)
							{
								$style=implode(",",array_unique(explode(",",$row[csf('style_ref_no')])));
								$style_ref_arr[$row[csf('mst_id')]]=$style;
							}
						}
						if($cbo_company_name>0) $company_cond="  and  a.benificiary_id=$cbo_company_name"; else  $company_cond="";
                        $query="SELECT a.id, a.benificiary_id, a.invoice_no, a.net_invo_value, a.invoice_date, a.ex_factory_date, a.is_lc, a.lc_sc_id, a.buyer_id, a.bl_date FROM com_export_invoice_ship_mst a where a.is_deleted=0 and a.status_active=1 and  a.id not in(select poid from tmp_poid where userid=$user_id and type='121')and $search_field between $txt_date_from and $txt_date_to $buyer_id_cond $company_cond";
						$cbo_lien_bank=str_replace("'","",$cbo_lien_bank); $tot_unsub_invoice_value=0;
					    $dataArray=sql_select($query);
					    foreach($dataArray as $row_uns)
					    {
							$file_no=''; $lc_sc_no=''; $lien_bank='';
							if($row_uns[csf('is_lc')]==1)
							{
								$file_no=$lc_arr[$row_uns[csf('lc_sc_id')]]['file'];
								$lc_sc_no=$lc_arr[$row_uns[csf('lc_sc_id')]]['lc'];
								$lien_bank=$lc_arr[$row_uns[csf('lc_sc_id')]]['lien_bank']; 
								$file_year=$lc_arr[$row_uns[csf('lc_sc_id')]]['file_year'];
								$lc_sc_pay_term=$pay_term[$pay_term_arr[$file_year][$lc_sc_no]];
								$lc_sc_tenor=$tenor_arr[$file_year][$lc_sc_no];
							}
							else 
							{
								$file_no=$sc_arr[$row_uns[csf('lc_sc_id')]]['file'];
								$lc_sc_no=$sc_arr[$row_uns[csf('lc_sc_id')]]['sc']; 
								$lien_bank=$sc_arr[$row_uns[csf('lc_sc_id')]]['lien_bank'];
								$file_year=$sc_arr[$row_uns[csf('lc_sc_id')]]['file_year'];
								$lc_sc_pay_term=$pay_term[$sc_pay_term_arr[$file_year][$lc_sc_no]];
								$lc_sc_tenor=$sc_tenor_arr[$file_year][$lc_sc_no];
							}
							
							if($cbo_lien_bank==0 || $cbo_lien_bank==$lien_bank)
							{
							?>
								<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
									<td width="70"><? echo $company_arr[$row_uns[csf('benificiary_id')]]['short']; ?></td>
                                    <td width="120"><p><? echo $buyer_arr[$row_uns[csf("buyer_id")]]; ?></p></td>
									<td width="60" align="center"><p><? echo $file_no; ?></p></td>
                                    <td width="60" align="center"><p><? echo $file_year; ?></p></td>
                                    <td width="220"><p><? echo $style_ref_arr[$row_uns[csf("id")]]; ?></p></td>
									<td width="120"><p><? echo $lc_sc_no; ?></p></td>
									<td width="100"><p> <? echo $lc_sc_pay_term; ?></p></td>
									<td width="100"><p> <? echo $lc_sc_tenor; ?></p></td>
									<td width="120"><p><? echo $row_uns[csf('invoice_no')]; ?></p></td>
									<td width="100" align="right"><? echo number_format($row_uns[csf('net_invo_value')],2,'.',''); ?></td>
									<td width="80" align="center">
										<?
											if(str_replace("'","",$cbo_based)==4)
												echo change_date_format($row_uns[csf('ex_factory_date')]); 
											else 
												echo change_date_format($row_uns[csf('invoice_date')]); 
										?>
									</td>
                                    <td width="80"><p><? if($row_uns[csf('bl_date')]!="" && $row_uns[csf('bl_date')]!="0000-00-00") echo change_date_format($row_uns[csf('bl_date')]); ?></p></td>
                                    <td width="80" align="center"><p>
									<?
									$diff_bl="";
									if(str_replace("'","",$cbo_based)==4)
									{
										if($row_uns[csf('bl_date')]!="" && $row_uns[csf('bl_date')]!="0000-00-00")
										{
											$diff_bl=datediff("d",$row_uns[csf('ex_factory_date')], $row_uns[csf('bl_date')]);
											$diff_bl=$diff_bl." Days";
										}
									}
									echo $diff_bl;
									 
									?></p></td>
									<td><p><? echo $bank_arr[$lien_bank]; ?></p></td>
								</tr>
							<?
								$i++;  
								$tot_unsub_invoice_value+=$row_uns[csf('net_invo_value')];
							}
						}
                    	?>
                        <tfoot>
                        	<tr>
                            	<th colspan="9" align="right">Total</th>
                                <th align="right"><?php echo number_format($tot_unsub_invoice_value,2); ?></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                            <tr>
                            	<th colspan="9" align="right">Grand Total</th>
                                <th align="right"><?php echo number_format($total_inv_value+$tot_unsub_invoice_value,2); ?></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                    </div>   
					<?
					$r_id3=execute_query("delete from tmp_poid where userid=$user_id ");
					if($r_id3)
					{
						oci_commit($con);  
					}
				}
				else
				{
					echo '<div style="display:none" id="scroll_body_bottom"></div>';
				}
				?>
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
	disconnect($con);
}


if($action=="invoice_details")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $ref_id."=".$popup_type;die;
	$sql="select a.id as lc_sc_id, a.export_lc_system_id as sys_num, a.lc_date as date_lc_sc, a.export_lc_no as lc_sc_no, a.lc_value as lc_sc_value, b.id as inv_id, b.invoice_no, b.invoice_date, b.invoice_value
	from com_export_lc a, com_export_invoice_ship_mst b 
	where a.id=b.lc_sc_id and b.is_lc=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id in($ref_id) 
	union all
	select a.id as lc_sc_id, a.contact_system_id as sys_num, a.contract_date as date_lc_sc, a.contract_no as lc_sc_no, a.contract_value as lc_sc_value, b.id as inv_id, b.invoice_no, b.invoice_date, b.invoice_value  
	from com_sales_contract a, com_export_invoice_ship_mst b 
	where a.id=b.lc_sc_id and b.is_lc=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and b.id in($ref_id)";
	//echo $sql;
	$result=sql_select($sql);
	ob_start();
	?>
	<div style="width:550px" align="center" id="scroll_body" >
	<fieldset style="width:100%;" >
		 <table cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="550" align="center" border="1">
            <thead>
                <th width="30">SL</th>
                <th width="150">SC/LC No.</th>
                <th width="150">Invoice No.</th>
                <th width="80">Invoice Date</th>
                <th>Invoice Value</th>
            </thead>
            <tbody>
            <?
			$i=1;
			foreach($result as $row)
			{
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                	<td align="center"><? echo $i; ?></td>
                    <td><p><? echo $row[csf("lc_sc_no")]; ?>&nbsp;</p></td>
                    <td><p><? echo $row[csf("invoice_no")]; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo change_date_format($row[csf("invoice_date")]); ?>&nbsp;</p></td>
                    <td align="right"><? echo number_format($row[csf("invoice_value")],2); ?></td>
				</tr>
				<?
				$i++;
				$total_value+=$row[csf("invoice_value")];
			}
            ?>
            </tbody>
            <tfoot>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th align="right">Total:</th>
                <th align="right"><? echo number_format($total_value,2); ?></th> 
            </tfoot>
        </table>
	</fieldset>
	</div>
	<?	
	die;
}


if($action=="purchase_popup_qnty")
{
	extract($_REQUEST);
	echo load_html_head_contents("Purchase Popup", "../../../", 1, 1,$unicode,'','');	
	//echo $id;die;//company_id
	
	if($db_type==0)
	{
		$sql="select a.id, a.buyer_id, a.bank_ref_no, a.bank_ref_date, sum(b.net_invo_value) as tot_inv_value, max(b.is_lc) as is_lc, group_concat(distinct b.lc_sc_id) as lc_sc_id from  com_export_doc_submission_mst a, com_export_doc_submission_invo b where a.id=b.doc_submission_mst_id and a.entry_form=40 and a.submit_type=2 and a.id='$id' and a.status_active=1 and a.is_deleted=0 group by a.id , a.buyer_id, a.bank_ref_no, a.bank_ref_date";
	}
	else if($db_type==2)
	{
		$sql="select a.id, a.buyer_id, a.bank_ref_no, a.bank_ref_date, sum(b.net_invo_value) as tot_inv_value, max(b.is_lc) as is_lc,LISTAGG(CAST(b.lc_sc_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.lc_sc_id) lc_sc_id from  com_export_doc_submission_mst a, com_export_doc_submission_invo b where a.id=b.doc_submission_mst_id and a.entry_form=40 and a.submit_type=2 and a.id='$id' and a.status_active=1 and a.is_deleted=0 group by a.id , a.buyer_id, a.bank_ref_no, a.bank_ref_date";
	}
	//echo $sql;
	
	$dataArray=sql_select($sql);//$dataArray[0][csf('bank_ref_no')]
	$buyer_library=return_library_array( "select id,buyer_name from  lib_buyer", "id", "buyer_name"  );
	$lc_library=return_library_array( "select id,export_lc_no from  com_export_lc", "id", "export_lc_no"  );
	$sc_library=return_library_array( "select id,contract_no from  com_sales_contract", "id", "contract_no"  );
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
		document.getElementById('scroll_body').style.maxHeight="none";
	}	
	
</script>	
	<div style="width:667px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
	<fieldset style="width:670px; margin-left:7px" >
	<div id="report_container" align="left">
    <table border="1" class="rpt_table" rules="all" width="650px" align="left">
        <thead>
            <tr>
                <th width="100">Buyer</th>
                <th width="100">Bill No</th>
                <th width="80">Bill Date</th>
                <th width="100">Bill Value</th>
                <th width="100">LC No.</th>
            </tr>
            <tr>
                <th width="100"><? echo $buyer_library[$dataArray[0][csf('buyer_id')]]; ?></th>
                <th width="100"><? echo $dataArray[0][csf('bank_ref_no')]; ?></th>
                <th width="80"><? echo change_date_format($dataArray[0][csf('bank_ref_date')]); ?></th>
                <th width="120"><? echo number_format($dataArray[0][csf('tot_inv_value')],2); ?></th>
                <th width="100"><p>
				<? 
					if ($dataArray[0][csf('is_lc')]==1)
					{
						$lc_no_arr=array_unique(explode(",",$dataArray[0][csf('lc_sc_id')]));
						$lc_all="";
						foreach($lc_no_arr as $lc_id)
						{
							if($lc_all=="") $lc_all=$lc_library[$lc_id]; else $lc_all=$lc_all.", ".$lc_library[$lc_id];
						}
					}
					else
					{
						$sc_no_arr=array_unique(explode(",",$dataArray[0][csf('lc_sc_id')]));
						$lc_all="";
						foreach($sc_no_arr as $sc_id)
						{
							if($lc_all=="") $lc_all=$sc_library[$sc_id]; else $lc_all=$lc_all.", ".$sc_library[$sc_id];
						}
					}
					echo $lc_all;
					//echo $lc_library[$dataArray[0][csf('lc_sc_id')]]; else echo $sc_library[$dataArray[0][csf('lc_sc_id')]]; 
				?></p>
                </th>
           </tr>
        </thead>
    </table>
    <br /> <br /> <br />
    <?
	$mst_id=$dataArray[0][csf('id')];
	//$dtls_sql="select a.id, a.acc_head, a.acc_loan, a.dom_curr, a.conver_rate, a.lc_sc_curr, b.net_invo_value from  com_export_doc_sub_trans a, com_export_doc_submission_invo b where a.doc_submission_mst_id='$mst_id' and b.doc_submission_mst_id='$mst_id' and a.status_active=1 and a.is_deleted=0 group by a.acc_head";
	$dtls_sql="select a.id, a.acc_head, a.acc_loan, sum(a.dom_curr) as dom_curr, a.conver_rate, sum(a.lc_sc_curr) as lc_sc_curr from com_export_doc_sub_trans a where a.doc_submission_mst_id='$id' and a.status_active=1 and a.is_deleted=0 group by a.id, a.acc_head, a.acc_loan, a.conver_rate";
	//echo $dtls_sql;
	$dtls_sql_result=sql_select($dtls_sql);//$dataArray[0][csf('bank_ref_no')]
    ?>
    <div style="width:667px; overflow-y:scroll" id="scroll_body" align="left">
    <table cellspacing="0" width="650"  border="1" rules="all" class="rpt_table" >
        <thead>
            <th width="30">SL</th>
            <th width="">Account Head</th>
            <th width="120" >Purchase Amount</th>
            <th width="100" >Pur.%(USD)</th>
            <th width="80">Con. Rate</th>
            <th width="120" >BDT Amount</th>
        </thead>
		<?
        $i=1;
		foreach($dtls_sql_result as $row)
		{
			if ($i%2==0)  
				$bgcolor="#E9F3FF";
			else
				$bgcolor="#FFFFFF";
			$pur_per=($row[csf('lc_sc_curr')]/$row[csf('net_invo_value')])*100;
			$bdt_amount=$row[csf('lc_sc_curr')]*$row[csf('conver_rate')];
			?>
			<tr bgcolor="<? echo $bgcolor; ?>">
				<td><? echo $i;  ?></td>
				<td><? echo $commercial_head[$row[csf('acc_head')]]; ?></td>
				<td align="right"><? echo number_format($row[csf('lc_sc_curr')],2); ?></td>
                <td align="right"><? $pur_per=($row[csf('lc_sc_curr')]/$dataArray[0][csf('tot_inv_value')])*100; echo number_format($pur_per,4); //if($pur_per>0) echo "%"; ?></td>
                <td align="right"><? echo number_format($row[csf('conver_rate')],3); ?></td>
                <td align="right"><? echo number_format($bdt_amount,2); ?></td>
			</tr>
			<?
			$tot_pur_qnty+=$row[csf('lc_sc_curr')];
			$tot_bdt_amount+=$bdt_amount;
			$i++;
		}
		?>
		<tfoot>
			<tr>
				<th colspan="2" align="right"><strong>Total :</strong></th>
				<th align="right"><? echo number_format($tot_pur_qnty,2); ?></th>
				<th align="right"><? $total_percent=($tot_pur_qnty/$dataArray[0][csf('tot_inv_value')])*100; echo number_format($total_percent,4); ?></th>
                <th align="right"></th>
				<th align="right"><? echo number_format($tot_bdt_amount,2); ?></th>
			</tr>
		</tfoot>                           
	</table>
	</div>
    </div>
    </fieldset>
	<?
    exit();	
}
?>
