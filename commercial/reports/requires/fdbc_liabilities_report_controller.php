<?

session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$_SESSION['page_permission']=$permission;
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];


$buyer_arr=return_library_array("select id,buyer_name from  lib_buyer","id","buyer_name");
$company_library=return_library_array( "select id,company_name from lib_company where status_active=1 and is_deleted=0", "id", "company_name"  );


if ($action=="load_drop_down_buyer")
{
	//echo "yes";
	echo create_drop_down( "cbo_buyer_name", 155, "select buy.id,buy.buyer_name from lib_buyer buy, lib_buyer_tag_company b where buy.status_active =1 and buy.is_deleted=0 and b.buyer_id=buy.id and b.tag_company='$data' and buy.id in (select buyer_id from lib_buyer_party_type where party_type in (1,3,21,90))  $buyer_cond order by buyer_name","id,buyer_name", 1, "-- All Buyer --", $selected, "" ,0); 
	exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_buyer_name=str_replace("'","",$cbo_buyer_name);
	$cbo_lien_bank=str_replace("'","",$cbo_lien_bank);
	$cbo_search_by=str_replace("'","",$cbo_search_by);
	$txt_exchange_rate=str_replace("'","",$txt_exchange_rate);
	
	//echo $cbo_company_name."___".$cbo_buyer_name."___".$cbo_lien_bank."___".$cbo_search_by."___".$txt_exchange_rate;die;
	
    if($cbo_company_name ==0) $cbo_company_name =""; else $cbo_company="and b.beneficiary_name='$cbo_company_name'";
	if($cbo_buyer_name == 0) $cbo_buyer_name=""; else $cbo_buyer_name = "and b.buyer_name='$cbo_buyer_name'";
	if($cbo_lien_bank == 0) $cbo_lien_bank=""; else $cbo_lien_bank = "and b.lien_bank='$cbo_lien_bank'";
	
	

	$sc_id_group=0;$inv_id=0;$invoice_id=0;$sub_id=0;$lc_id_group=0;$total_inv_value=array();$total_realize_value=array(); $buyer_result=array();$unsubmit_inv=array();$no_inv_lc=array();
	


	/*echo "SELECT b.id as lc_id,  b.export_lc_no as export_lc_no, b.lc_value as lc_value,b.buyer_name, b.replacement_lc, 1 as type
				FROM  com_export_lc b
				WHERE b.status_active=1 and b.is_deleted=0 $cbo_company $cbo_buyer_name $cbo_lien_bank
				union all
				SELECT b.id as lc_id,  b.contract_no  as export_lc_no, b.contract_value  as lc_value,b.buyer_name, null as replacement_lc, 2 as type
				FROM com_sales_contract b 
				WHERE  b.status_active=1 and b.is_deleted=0 $cbo_company $cbo_buyer_name $cbo_lien_bank order by buyer_name asc";die;*/

	$sql=sql_select("SELECT b.id as lc_id,  b.export_lc_no as export_lc_no, b.lc_value as lc_value,b.buyer_name, b.replacement_lc, 1 as type
				FROM  com_export_lc b
				WHERE b.status_active=1 and b.is_deleted=0 $cbo_company $cbo_buyer_name $cbo_lien_bank
				union all
				SELECT b.id as lc_id,  b.contract_no  as export_lc_no, b.contract_value  as lc_value,b.buyer_name, null as replacement_lc, 2 as type
				FROM com_sales_contract b 
				WHERE  b.status_active=1 and b.is_deleted=0 $cbo_company $cbo_buyer_name $cbo_lien_bank order by buyer_name asc");

	foreach($sql as $row)
	{
		
		if($row[csf("type")]==1){ if($lc_id_group==0) $lc_id_group=$row[csf("lc_id")]; else $lc_id_group=$lc_id_group.",".$row[csf("lc_id")]; } 
		if($row[csf("type")]==2){ if($sc_id_group==0) $sc_id_group=$row[csf("lc_id")]; else $sc_id_group=$sc_id_group.",".$row[csf("lc_id")]; } 
		$total_inv_value[$row[csf("lc_id")]]['lc_value']+=$row[csf("lc_value")];
		if($row[csf('type')] == 2 && $row_result[csf('convertible_to_lc')] ==1 || $row_result[csf('convertible_to_lc')] ==3 ) $sc_value_1_3 += $row_result[csf('lc_sc_value')];
		
		if($row[csf("type")]==1)
		{
			$lc_result[$row[csf("lc_id")]]["type"]=$row[csf("type")];
			$lc_result[$row[csf("lc_id")]]["lc_no"]=$row[csf("export_lc_no")];
			$lc_result[$row[csf("lc_id")]]["lc_val"]+=$row[csf("lc_value")];
			if($row[csf('replacement_lc')] == 2) 
			{
				$buyer_lc_val[$row[csf("buyer_name")]] +=$row[csf("lc_value")];
			}
			
		}
		if($row[csf("type")]==2)
		{
			$sc_result[$row[csf("lc_id")]]["type"]=$row[csf("type")];
			$sc_result[$row[csf("lc_id")]]["lc_no"]=$row[csf("export_lc_no")];
			$sc_result[$row[csf("lc_id")]]["lc_val"]+=$row[csf("lc_value")];
			$buyer_lc_val[$row[csf("buyer_name")]] +=$row[csf("lc_value")];
		}
 		
	}
	//echo $sc_id_group;die;
	
	$all_invoice_lc=array();
	$all_invoice_sc=array();
	//for realization lc
	$rlz_details_sql=sql_select("select e.mst_id, sum(case when e.type=0 then e.document_currency else 0 end) as deducation, sum(case when e.type=1 then e.document_currency else 0 end) as distribution, sum(case when e.type=1 and e.account_head=1 then e.document_currency else 0 end) as nagotiate_distribution from com_export_proceed_rlzn_dtls e where e.status_active=1 and e.is_deleted=0 group by e.mst_id");
	foreach($rlz_details_sql as $row)
	{
		$rlz_dtls_arr[$row[csf("mst_id")]]["mst_id"]=$row[csf("mst_id")];
		$rlz_dtls_arr[$row[csf("mst_id")]]["deducation"]=$row[csf("deducation")];
		$rlz_dtls_arr[$row[csf("mst_id")]]["distribution"]=$row[csf("distribution")];
		$rlz_dtls_arr[$row[csf("mst_id")]]["nagotiate_distribution"]=$row[csf("nagotiate_distribution")];
	}
	
	if(empty($lc_id_group)) $lc_id_group=0;
	if(empty($sc_id_group)) $sc_id_group=0;
	
	if($db_type==0)
	{
		$sql_relz_lc="SELECT group_concat(a.id) as invoice_id, group_concat(a.invoice_no) as invoice_no, sum(a.invoice_quantity) as invoice_quantity, sum(a.net_invo_value) as invoice_value, sum(a.freight_amnt_by_supllier) as freight_amnt_by_supllier, sum(a.paid_amount) as paid_amount, sum(a.advice_amount) as advice_amount, max(b.lc_sc_id) as lc_id, max(b.is_lc) as is_lc, c.id as sub_id, c.bank_ref_no, c.submit_type, c.buyer_id, sum(case when c.submit_type=1 then b.net_invo_value end) as sub_collection, sum(case when c.submit_type=2 then b.net_invo_value end) as sub_purchase, d.id as rlz_id
		from com_export_invoice_ship_mst a, com_export_doc_submission_invo b, com_export_doc_submission_mst c,  com_export_proceed_realization d
		where a.id=b.invoice_id and b.doc_submission_mst_id=c.id and c.id=d.invoice_bill_id and b.is_lc=1 and b.lc_sc_id in(".$lc_id_group.") and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.entry_form=40
		group by c.id, c.bank_ref_no, c.submit_type, c.buyer_id, d.id";
		
	}
	else
	{
		$sql_relz_lc="SELECT LISTAGG(CAST(a.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.id)  as invoice_id, LISTAGG(CAST(  a.invoice_no  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY  a.invoice_no)  as invoice_no, sum(a.invoice_quantity) as invoice_quantity, sum(a.net_invo_value) as invoice_value, sum(a.freight_amnt_by_supllier) as freight_amnt_by_supllier, sum(a.paid_amount) as paid_amount, sum(a.advice_amount) as advice_amount, max(b.lc_sc_id) as lc_id, max(b.is_lc) as is_lc, c.id as sub_id, c.bank_ref_no, c.submit_type, c.buyer_id, sum(case when c.submit_type=1 then b.net_invo_value end) as sub_collection, sum(case when c.submit_type=2 then b.net_invo_value end) as sub_purchase, d.id as rlz_id
		from com_export_invoice_ship_mst a, com_export_doc_submission_invo b, com_export_doc_submission_mst c,  com_export_proceed_realization d
		where a.id=b.invoice_id and b.doc_submission_mst_id=c.id and c.id=d.invoice_bill_id and b.is_lc=1 and b.lc_sc_id in(".$lc_id_group.")  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.entry_form=40 
		group by c.id, c.bank_ref_no, c.submit_type, c.buyer_id , d.id";
	}
	//echo $sql_relz_lc;die;
	$rlz_lc_result=sql_select($sql_relz_lc);
	$rlz_sub_lc_id=array();
	$invoice_rlz_lc_id=0;
	foreach($rlz_lc_result as $row)
	{
		if($invoice_rlz_lc_id==0) $invoice_rlz_lc_id=$row[csf("invoice_id")]; else $invoice_rlz_lc_id=$invoice_rlz_lc_id.",".$row[csf("invoice_id")];
		$all_invoice_lc[$row[csf("lc_id")]]=$row[csf("lc_id")];
		
		$rlz_sub_lc_id[]=$row[csf("sub_id")];
		$total_realize_value[$row[csf("sub_id")]]['rlz_val']=$rlz_dtls_arr[$row[csf("rlz_id")]]["deducation"]+$rlz_dtls_arr[$row[csf("rlz_id")]]["distribution"];
		$total_realize_value[$row[csf("sub_id")]]['deducation']+=$rlz_dtls_arr[$row[csf("rlz_id")]]["deducation"];
		$total_realize_value[$row[csf("sub_id")]]['distribution']+=$rlz_dtls_arr[$row[csf("rlz_id")]]["distribution"];
		$total_realize_value[$row[csf("sub_id")]]['nagotiate_distribution']+=$rlz_dtls_arr[$row[csf("rlz_id")]]["nagotiate_distribution"];
		
		
		$total_realize_value[$row[csf("sub_id")]]["is_lc"]=$row[csf("is_lc")];
		$total_realize_value[$row[csf("sub_id")]]["lc_id"]=$row[csf("lc_id")];
		$total_realize_value[$row[csf("sub_id")]]["sub_id"]=$row[csf("sub_id")];
		$total_realize_value[$row[csf("sub_id")]]["buyer_id"]=$row[csf("buyer_id")];
		$total_realize_value[$row[csf("sub_id")]]["bank_ref_no"]=$row[csf("bank_ref_no")];
		$total_realize_value[$row[csf("sub_id")]]["invoice_no"]=$row[csf("invoice_no")];
		$total_realize_value[$row[csf("sub_id")]]["invoice_quantity"]+=$row[csf("invoice_quantity")];
		$total_realize_value[$row[csf("sub_id")]]["invoice_value"]+=$row[csf("invoice_value")];
		$total_realize_value[$row[csf("sub_id")]]["advice_amount"]+=$row[csf("advice_amount")];
		$total_realize_value[$row[csf("sub_id")]]["freight_amnt_by_supllier"]+=$row[csf("freight_amnt_by_supllier")];
		$total_realize_value[$row[csf("sub_id")]]["paid_amount"]+=$row[csf("paid_amount")];
		$total_realize_value[$row[csf("sub_id")]]["sub_collection"]+=$row[csf("sub_collection")];
		$total_realize_value[$row[csf("sub_id")]]["sub_purchase"]+=$row[csf("sub_purchase")];
		$total_realize_value[$row[csf("sub_id")]]["submit_type"]+=$row[csf("submit_type")];
	}
	
	//for realization sc
	if($db_type==0)
	{
		$sql_relz_sc="SELECT group_concat(a.id) as invoice_id, group_concat(a.invoice_no) as invoice_no, sum(a.invoice_quantity) as invoice_quantity, sum(a.net_invo_value) as invoice_value, sum(a.freight_amnt_by_supllier) as freight_amnt_by_supllier, sum(a.paid_amount) as paid_amount, sum(a.advice_amount) as advice_amount, max(b.lc_sc_id) as lc_id, max(b.is_lc) as is_lc, c.id as sub_id, c.bank_ref_no, c.submit_type, c.buyer_id, sum(case when c.submit_type=1 then b.net_invo_value end) as sub_collection, sum(case when c.submit_type=2 then b.net_invo_value end) as sub_purchase, d.id as rlz_id
		from com_export_invoice_ship_mst a, com_export_doc_submission_invo b, com_export_doc_submission_mst c,  com_export_proceed_realization d
		where a.id=b.invoice_id and b.doc_submission_mst_id=c.id and c.id=d.invoice_bill_id and b.is_lc=2 and b.lc_sc_id in(".$sc_id_group.")  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.entry_form=40 
		group by c.id, c.bank_ref_no, c.submit_type, c.buyer_id, d.id";
		
	}
	else
	{
		$sql_relz_sc="SELECT LISTAGG(CAST(a.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY a.id)  as invoice_id, LISTAGG(CAST(  a.invoice_no  AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY  a.invoice_no)  as invoice_no, sum(a.invoice_quantity) as invoice_quantity, sum(a.net_invo_value) as invoice_value, sum(a.freight_amnt_by_supllier) as freight_amnt_by_supllier, sum(a.paid_amount) as paid_amount, sum(a.advice_amount) as advice_amount, max(b.lc_sc_id) as lc_id, max(b.is_lc) as is_lc, c.id as sub_id, c.bank_ref_no, c.submit_type, c.buyer_id, sum(case when c.submit_type=1 then b.net_invo_value end) as sub_collection, sum(case when c.submit_type=2 then b.net_invo_value end) as sub_purchase, d.id as rlz_id
		from com_export_invoice_ship_mst a, com_export_doc_submission_invo b, com_export_doc_submission_mst c,  com_export_proceed_realization d
		where a.id=b.invoice_id and b.doc_submission_mst_id=c.id and c.id=d.invoice_bill_id and b.is_lc=2 and b.lc_sc_id in(".$sc_id_group.")  and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.entry_form=40
		group by c.id, c.bank_ref_no, c.submit_type, c.buyer_id, d.id";
	}
	

	$rlz_sc_result=sql_select($sql_relz_sc);
	
	$rlz_sub_sc_id=array();
	$invoice_rlz_sc_id=0;
	foreach($rlz_sc_result as $row)
	{
		
		if($invoice_rlz_sc_id==0) $invoice_rlz_sc_id=$row[csf("invoice_id")]; else $invoice_rlz_sc_id=$invoice_rlz_sc_id.",".$row[csf("invoice_id")];
		$all_invoice_sc[$row[csf("lc_id")]]=$row[csf("lc_id")];
		
		$rlz_sub_sc_id[]=$row[csf("sub_id")];
		$total_realize_value[$row[csf("sub_id")]]['rlz_val']=$rlz_dtls_arr[$row[csf("rlz_id")]]["deducation"]+$rlz_dtls_arr[$row[csf("rlz_id")]]["distribution"];
		$total_realize_value[$row[csf("sub_id")]]['deducation']+=$rlz_dtls_arr[$row[csf("rlz_id")]]["deducation"];
		$total_realize_value[$row[csf("sub_id")]]['distribution']+=$rlz_dtls_arr[$row[csf("rlz_id")]]["distribution"];
		$total_realize_value[$row[csf("sub_id")]]['nagotiate_distribution']+=$rlz_dtls_arr[$row[csf("rlz_id")]]["nagotiate_distribution"];
		
		
		$total_realize_value[$row[csf("sub_id")]]["is_lc"]=$row[csf("is_lc")];
		$total_realize_value[$row[csf("sub_id")]]["lc_id"]=$row[csf("lc_id")];
		$total_realize_value[$row[csf("sub_id")]]["sub_id"]=$row[csf("sub_id")];
		$total_realize_value[$row[csf("sub_id")]]["buyer_id"]=$row[csf("buyer_id")];
		$total_realize_value[$row[csf("sub_id")]]["bank_ref_no"]=$row[csf("bank_ref_no")];
		$total_realize_value[$row[csf("sub_id")]]["invoice_no"]=$row[csf("invoice_no")];
		$total_realize_value[$row[csf("sub_id")]]["invoice_quantity"]+=$row[csf("invoice_quantity")];
		$total_realize_value[$row[csf("sub_id")]]["invoice_value"]+=$row[csf("invoice_value")];
		$total_realize_value[$row[csf("sub_id")]]["advice_amount"]+=$row[csf("advice_amount")];
		$total_realize_value[$row[csf("sub_id")]]["freight_amnt_by_supllier"]+=$row[csf("freight_amnt_by_supllier")];
		$total_realize_value[$row[csf("sub_id")]]["paid_amount"]+=$row[csf("paid_amount")];
		$total_realize_value[$row[csf("sub_id")]]["sub_collection"]+=$row[csf("sub_collection")];
		$total_realize_value[$row[csf("sub_id")]]["sub_purchase"]+=$row[csf("sub_purchase")];
		$total_realize_value[$row[csf("sub_id")]]["submit_type"]+=$row[csf("submit_type")];
	}

	//var_dump($total_realize_value);die;
	
	
	//submissio to bank lc
	if(empty($rlz_sub_id)) $rlz_sub_id[]=0;
	
	if($db_type==0)
	{
		$sql_sub_lc="SELECT group_concat(distinct b.invoice_id) as inv_id,a.id as sub_id, group_concat(distinct c.invoice_no ) as invoice_no, sum(b.net_invo_value) as net_invo_value, a.bank_ref_no, a.bank_ref_date, sum(case when a.submit_type=1 then b.net_invo_value else 0 end) as sub_collection, sum(case when a.submit_type=2 then b.net_invo_value else 0 end) as purchase_amount, a.negotiation_date, a.total_lcsc_currency as total_negotiated_amount, a.buyer_id, max(c.lc_sc_id) as lc_sc_id, max(c.is_lc) as is_lc,sum(c.invoice_quantity) as invoice_quantity
		FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c
		WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id and b.is_lc=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form=40  and b.lc_sc_id in($lc_id_group) ";
		
		if(!empty($rlz_sub_lc_id))
		{
			$sql_sub_lc.="  and a.id not in(".implode(',',$rlz_sub_lc_id).")";
		}
		
		$sql_sub_lc.=" group by a.id, a.bank_ref_no, a.bank_ref_date, a.negotiation_date, a.buyer_id, a.total_lcsc_currency order by a.id ";
		
	}
	else if($db_type==2)
	{
		$rlz_sub_lc_id_arr=array_chunk(array_unique($rlz_sub_lc_id),999);
		$sql_sub_lc="SELECT LISTAGG(CAST(  b.invoice_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.invoice_id) as inv_id,a.id as sub_id, LISTAGG(CAST(   c.invoice_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.invoice_no) as invoice_no, sum(b.net_invo_value) as net_invo_value, a.bank_ref_no, a.bank_ref_date, sum(case when a.submit_type=1 then b.net_invo_value else 0 end) as sub_collection, sum(case when a.submit_type=2 then b.net_invo_value else 0 end) as purchase_amount, a.negotiation_date, a.total_lcsc_currency as total_negotiated_amount, a.buyer_id , max(c.lc_sc_id) as lc_sc_id, max(c.is_lc) as is_lc,sum(c.invoice_quantity) as invoice_quantity
		FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c
		WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id and b.is_lc=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form=40 and b.lc_sc_id in($lc_id_group) ";
		
		$p=1;
		if(!empty($rlz_sub_lc_id_arr))
		{
			foreach($rlz_sub_lc_id_arr as $rlz_sub_id)
			{
				if($p==1) $sql_sub_lc .=" and (a.id not in(".implode(',',$rlz_sub_id).")"; else $sql_sub_lc .=" and a.id not in(".implode(',',$rlz_sub_id).")";
				$p++;
			}
			$sql_sub_lc .=" ) group by a.id, a.bank_ref_no, a.bank_ref_date, a.negotiation_date,a.total_lcsc_currency, a.buyer_id order by a.id";
		}
		else
		{
			$sql_sub_lc .=" group by a.id, a.bank_ref_no, a.bank_ref_date, a.negotiation_date,a.total_lcsc_currency, a.buyer_id order by a.id";
		}
	}

	
	$sub_bank_lc_result=sql_select($sql_sub_lc);
	$bank_sub_id_lc=array();
	foreach($sub_bank_lc_result as $result)
	{
		if($invoice_rlz_lc_id==0) $invoice_rlz_lc_id=$result[csf("inv_id")]; else $invoice_rlz_lc_id=$invoice_rlz_lc_id.",".$result[csf("inv_id")];
		$bank_sub_id_lc[]=$result[csf("sub_id")];
		$all_invoice_lc[$result[csf("lc_sc_id")]]=$result[csf("lc_sc_id")];
		
		$sub_bank_arr[$result[csf("sub_id")]]['sub_id']=$result[csf("sub_id")];
		$sub_bank_arr[$result[csf("sub_id")]]['buyer_id']=$result[csf("buyer_id")];
		$sub_bank_arr[$result[csf("sub_id")]]['inv_id']=$result[csf("inv_id")];
		$sub_bank_arr[$result[csf("sub_id")]]['invoice_no']=$result[csf("invoice_no")];
		$sub_bank_arr[$result[csf("sub_id")]]['invoice_quantity']=$result[csf("invoice_quantity")];
		$sub_bank_arr[$result[csf("sub_id")]]['net_invo_value']=$result[csf("net_invo_value")];
		$sub_bank_arr[$result[csf("sub_id")]]['bank_ref_no']=$result[csf("bank_ref_no")];
		$sub_bank_arr[$result[csf("sub_id")]]['sub_collection']=$result[csf("sub_collection")];
		$sub_bank_arr[$result[csf("sub_id")]]['bank_ref_date']=$result[csf("bank_ref_date")];
		$sub_bank_arr[$result[csf("sub_id")]]['purchase_amount']=$result[csf("purchase_amount")];
		$sub_bank_arr[$result[csf("sub_id")]]['negotiation_date']=$result[csf("negotiation_date")];
		$sub_bank_arr[$result[csf("sub_id")]]['total_negotiated_amount']=$result[csf("total_negotiated_amount")];
		$sub_bank_arr[$result[csf("sub_id")]]['lc_sc_id']=$result[csf("lc_sc_id")];
		$sub_bank_arr[$result[csf("sub_id")]]['is_lc']=$result[csf("is_lc")];
	}
	
	//submissio to bank SC
	if($db_type==0)
	{
		$sql_sub_sc="SELECT group_concat(distinct b.invoice_id) as inv_id,a.id as sub_id, group_concat(distinct c.invoice_no) as invoice_no, sum(b.net_invo_value) as net_invo_value, a.bank_ref_no, a.bank_ref_date, sum(case when a.submit_type=1 then b.net_invo_value else 0 end) as sub_collection, sum(case when a.submit_type=2 then b.net_invo_value else 0 end) as purchase_amount, a.negotiation_date, a.total_lcsc_currency as total_negotiated_amount, a.buyer_id, max(c.lc_sc_id) as lc_sc_id, max(c.is_lc) as is_lc,sum(c.invoice_quantity) as invoice_quantity, group_concat(distinct c.id) as sub_invoice_id
		FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c
		WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id  and b.is_lc='2' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form=40 and b.lc_sc_id in(".$sc_id_group.")";
		
		if(!empty($rlz_sub_sc_id))
		{
			$sql_sub_sc.="  and a.id not in(".implode(',',$rlz_sub_sc_id).")";
		}
		
		$sql_sub_sc.=" group by a.id, a.bank_ref_no, a.bank_ref_date, a.negotiation_date,a.total_lcsc_currency, a.buyer_id order by a.id";
		
	}
	else if($db_type==2)
	{
		$rlz_sub_id_arr=array_chunk(array_unique($rlz_sub_sc_id),999);
		$sql_sub_sc="SELECT LISTAGG(CAST( b.invoice_id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY b.invoice_id) as inv_id,a.id as sub_id, LISTAGG(CAST(c.invoice_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.invoice_no)  as invoice_no, sum(b.net_invo_value) as net_invo_value, a.bank_ref_no, a.bank_ref_date, sum(case when a.submit_type=1 then b.net_invo_value else 0 end) as sub_collection, sum(case when a.submit_type=2 then b.net_invo_value else 0 end) as purchase_amount, a.negotiation_date, a.total_lcsc_currency as total_negotiated_amount, a.buyer_id, max(c.lc_sc_id) as lc_sc_id, max(c.is_lc) as is_lc,sum(c.invoice_quantity) as invoice_quantity, LISTAGG(CAST( c.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.id) as sub_invoice_id
		FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c
		WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id  and b.is_lc='2' and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form=40 and b.lc_sc_id in(".$sc_id_group.")";
		$p=1;
		if(!empty($rlz_sub_id_arr))
		{
			foreach($rlz_sub_id_arr as $rlz_sub_id)
			{
				if($p==1) $sql_sub_sc .=" and (a.id not in(".implode(',',$rlz_sub_id).")"; else $sql_sub_sc .=" and a.id not in(".implode(',',$rlz_sub_id).")";
				$p++;
			}
			$sql_sub_sc .=" ) group by  a.id, a.bank_ref_no, a.bank_ref_date, a.negotiation_date,a.total_lcsc_currency, a.buyer_id order by a.id";
		}
		else
		{
			$sql_sub_sc .=" group by  a.id, a.bank_ref_no, a.bank_ref_date, a.negotiation_date,a.total_lcsc_currency, a.buyer_id order by a.id";
		}
		
	}
	//echo $sql_sub_sc;die;
	
	$sub_bank_result=sql_select($sql_sub_sc);
	$bank_sub_id_sc=array();
	foreach($sub_bank_result as $result)
	{
		if($invoice_rlz_sc_id==0) $invoice_rlz_sc_id=$result[csf("inv_id")]; else $invoice_rlz_sc_id=$invoice_rlz_sc_id.",".$result[csf("inv_id")];
		$bank_sub_id_sc[]=$result[csf("sub_id")];
		$all_invoice_sc[$result[csf("lc_sc_id")]]=$result[csf("lc_sc_id")];
		
		$sub_bank_arr[$result[csf("sub_id")]]['sub_id']=$result[csf("sub_id")];
		$sub_bank_arr[$result[csf("sub_id")]]['buyer_id']=$result[csf("buyer_id")];
		$sub_bank_arr[$result[csf("sub_id")]]['inv_id']=$result[csf("inv_id")];
		$sub_bank_arr[$result[csf("sub_id")]]['invoice_no']=$result[csf("invoice_no")];
		$sub_bank_arr[$result[csf("sub_id")]]['invoice_quantity']=$result[csf("invoice_quantity")];
		$sub_bank_arr[$result[csf("sub_id")]]['net_invo_value']=$result[csf("net_invo_value")];
		$sub_bank_arr[$result[csf("sub_id")]]['bank_ref_no']=$result[csf("bank_ref_no")];
		$sub_bank_arr[$result[csf("sub_id")]]['sub_collection']=$result[csf("sub_collection")];
		$sub_bank_arr[$result[csf("sub_id")]]['bank_ref_date']=$result[csf("bank_ref_date")];
		$sub_bank_arr[$result[csf("sub_id")]]['purchase_amount']=$result[csf("purchase_amount")];
		$sub_bank_arr[$result[csf("sub_id")]]['negotiation_date']=$result[csf("negotiation_date")];
		$sub_bank_arr[$result[csf("sub_id")]]['total_negotiated_amount']=$result[csf("total_negotiated_amount")];
		$sub_bank_arr[$result[csf("sub_id")]]['lc_sc_id']=$result[csf("lc_sc_id")];
		$sub_bank_arr[$result[csf("sub_id")]]['is_lc']=$result[csf("is_lc")];
	}
	// submissio to buyer Lc
	
	if($db_type==0)
	{
		$sql_sub_lc_buy="SELECT group_concat(distinct c.id) as inv_id,  group_concat(distinct c.invoice_no) as invoice_no, sum(c.invoice_quantity) as invoice_quantity, a.id as sub_id, a.submit_date, a.buyer_id, sum(b.net_invo_value) as net_invo_value, max(b.lc_sc_id) as lc_sc_id, max(c.is_lc) as is_lc
		FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c
		WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id and b.is_converted=0  and b.is_lc='1' AND b.lc_sc_id in(".$lc_id_group.") and a.entry_form=39 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  
		group by a.id, a.submit_date, a.buyer_id order by a.id";
	}
	else if($db_type==2)
	{
		$sql_sub_lc_buy="SELECT LISTAGG(CAST( c.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.id) as inv_id, LISTAGG(CAST(c.invoice_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.invoice_no)  as invoice_no, sum(c.invoice_quantity) as invoice_quantity, a.id as sub_id, a.submit_date, a.buyer_id, sum(b.net_invo_value) as net_invo_value, max(b.lc_sc_id) as lc_sc_id, max(c.is_lc) as is_lc
		FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c
		WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id and b.is_converted=0  and b.is_lc='1' AND b.lc_sc_id in(".$lc_id_group.") and a.entry_form=39 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  
		group by a.id, a.submit_date, a.buyer_id order by a.id";
		
	}
	
	//echo $sql_sub_lc_buy;die;
	
	$sub_buyer_result=sql_select($sql_sub_lc_buy);
	foreach($sub_buyer_result as $result)
	{
		if($invoice_rlz_lc_id==0) $invoice_rlz_lc_id=$result[csf("inv_id")]; else $invoice_rlz_lc_id=$invoice_rlz_lc_id.",".$result[csf("inv_id")];
		$all_invoice_lc[$result[csf("lc_sc_id")]]=$result[csf("lc_sc_id")];
		
		$sub_buyer_arr[$result[csf("sub_id")]]['sub_id']=$result[csf("sub_id")];
		$sub_buyer_arr[$result[csf("sub_id")]]['inv_id']=$result[csf("inv_id")];
		$sub_buyer_arr[$result[csf("sub_id")]]['invoice_no']=$result[csf("invoice_no")];
		$sub_buyer_arr[$result[csf("sub_id")]]['invoice_quantity']=$result[csf("invoice_quantity")];
		$sub_buyer_arr[$result[csf("sub_id")]]['submit_date']=$result[csf("submit_date")];
		$sub_buyer_arr[$result[csf("sub_id")]]['buyer_id']=$result[csf("buyer_id")];
		$sub_buyer_arr[$result[csf("sub_id")]]['net_invo_value']=$result[csf("net_invo_value")];
		$sub_buyer_arr[$result[csf("sub_id")]]['lc_sc_id']=$result[csf("lc_sc_id")];
		$sub_buyer_arr[$result[csf("sub_id")]]['is_lc']=$result[csf("is_lc")];
	}
	//var_dump($sub_buyer_arr);die;
	
	// submissio to buyer SC
	if($db_type==0)
	{
		$sql_sub_sc_buy="SELECT group_concat(distinct c.id) as inv_id,  group_concat(distinct c.invoice_no) as invoice_no, sum(c.invoice_quantity) as invoice_quantity, a.id as sub_id, a.submit_date, a.buyer_id, sum(b.net_invo_value) as net_invo_value, max(b.lc_sc_id) as lc_sc_id, max(c.is_lc) as is_lc
		FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c
		WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id and b.is_converted=0  and b.is_lc='2' AND b.lc_sc_id in(".$sc_id_group.") and a.entry_form=39 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  
		group by a.id, a.submit_date, a.buyer_id order by a.id";
	}
	else if($db_type==2)
	{
		$sql_sub_sc_buy="SELECT LISTAGG(CAST( c.id AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.id) as inv_id, LISTAGG(CAST(c.invoice_no AS VARCHAR(4000)), ',') WITHIN GROUP (ORDER BY c.invoice_no)  as invoice_no, sum(c.invoice_quantity) as invoice_quantity, a.id as sub_id, a.submit_date, a.buyer_id, sum(b.net_invo_value) as net_invo_value, max(b.lc_sc_id) as lc_sc_id, max(c.is_lc) as is_lc
		FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b,  com_export_invoice_ship_mst c
		WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id and b.is_converted=0  and b.is_lc='2' AND b.lc_sc_id in(".$sc_id_group.") and a.entry_form=39 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0  
		group by a.id, a.submit_date, a.buyer_id order by a.id";
		
	}
	//echo $sql_sub_sc_buy;die;
	
	$sub_buyer_result=sql_select($sql_sub_sc_buy);
	foreach($sub_buyer_result as $result)
	{
		if($invoice_rlz_sc_id==0) $invoice_rlz_sc_id=$result[csf("inv_id")]; else $invoice_rlz_sc_id=$invoice_rlz_sc_id.",".$result[csf("inv_id")];
		$all_invoice_sc[$result[csf("lc_sc_id")]]=$result[csf("lc_sc_id")];
		
		$sub_buyer_arr[$result[csf("sub_id")]]['sub_id']=$result[csf("sub_id")];
		$sub_buyer_arr[$result[csf("sub_id")]]['inv_id']=$result[csf("inv_id")];
		$sub_buyer_arr[$result[csf("sub_id")]]['invoice_no']=$result[csf("invoice_no")];
		$sub_buyer_arr[$result[csf("sub_id")]]['invoice_quantity']=$result[csf("invoice_quantity")];
		$sub_buyer_arr[$result[csf("sub_id")]]['submit_date']=$result[csf("submit_date")];
		$sub_buyer_arr[$result[csf("sub_id")]]['buyer_id']=$result[csf("buyer_id")];
		$sub_buyer_arr[$result[csf("sub_id")]]['net_invo_value']=$result[csf("net_invo_value")];
		$sub_buyer_arr[$result[csf("sub_id")]]['lc_sc_id']=$result[csf("lc_sc_id")];
		$sub_buyer_arr[$result[csf("sub_id")]]['is_lc']=$result[csf("is_lc")];
	}
	

	
	$sql_unsubmit_lc= "SELECT b.id as lc_sc_id, a.id, a.buyer_id, a.invoice_no, a.invoice_quantity, a.net_invo_value as invoice_value, b.export_lc_no as export_lc_no, b.lc_value as lc_sc_val, a.ex_factory_date, a.remarks, 1 as type from com_export_invoice_ship_mst a, com_export_lc b where a.lc_sc_id=b.id and a.is_lc=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0  and a.lc_sc_id in($lc_id_group)";
	$p=1;
	if(!empty($invoice_rlz_lc_id))
	{
		$invoice_id_lc_arr=array_chunk(array_unique(explode(",",$invoice_rlz_lc_id)),999);
		foreach($invoice_id_lc_arr as $invoice_id_lc)
		{
			if($p==1) $sql_unsubmit_lc .="and (a.id not in(".implode(',',$invoice_id_lc).")"; else  $sql_unsubmit_lc .=" and a.id not in(".implode(',',$invoice_id_lc).")";
			
			$p++;
		}
		$sql_unsubmit_lc .=")";
	}
	//echo $sql_unsubmit_lc;die;
	
	$sql_unsubmit_result_lc=sql_select($sql_unsubmit_lc);
	foreach($sql_unsubmit_result_lc as $row)
	{
		$unsubmit_inv[$row[csf('id')]]['id']=$row[csf('id')];
		$unsubmit_inv[$row[csf('id')]]['type']=$row[csf('type')];
		$unsubmit_inv[$row[csf('id')]]['buyer_id']=$row[csf('buyer_id')];
		$unsubmit_inv[$row[csf('id')]]['invoice_no']=$row[csf('invoice_no')];
		$unsubmit_inv[$row[csf('id')]]['invoice_quantity']=$row[csf('invoice_quantity')];
		$unsubmit_inv[$row[csf('id')]]['invoice_value']=$row[csf('invoice_value')];
		$unsubmit_inv[$row[csf('id')]]['export_lc_no']=$row[csf('export_lc_no')];
		$unsubmit_inv[$row[csf('id')]]['lc_sc_val']=$row[csf('lc_sc_val')];
		$unsubmit_inv[$row[csf('id')]]['lc_sc_id']=$row[csf('lc_sc_id')];
		$unsubmit_inv[$row[csf('id')]]['remarks']=$row[csf('remarks')];
		$unsubmit_inv[$row[csf('id')]]['ex_factory_date']=$row[csf('ex_factory_date')];
	
		$buyer_inhand_total[$row[csf('buyer_id')]]+=$row[csf('invoice_value')];
		
		$lc_contac_id[]=$row[csf("lc_sc_id")];
		$all_invoice_lc[$row[csf("lc_sc_id")]]=$row[csf("lc_sc_id")];
	}
	
	//print_r($invoice_rlz_sc_id);die;
	
	$sql_unsubmit_sc =" SELECT b.id as lc_sc_id, a.id, a.buyer_id, a.invoice_no, a.invoice_quantity, a.net_invo_value as invoice_value, b.contract_no as export_lc_no, b.contract_value as lc_sc_val, a.remarks, a.ex_factory_date, 2 as type from com_export_invoice_ship_mst a,  com_sales_contract b where a.lc_sc_id=b.id and a.is_lc=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.lc_sc_id in( $sc_id_group )  ";
	
	
	//var_dump($invoice_rlz_sc_id);die;
	if(!empty($invoice_rlz_sc_id))
	{
		$invoice_id_sc_arr=array_chunk(array_unique(explode(",",$invoice_rlz_sc_id)),999);
		$q=1;
		foreach($invoice_id_sc_arr as $invoice_id_sc)
		{
			//print_r($invoice_id_sc); die;
			if($q==1) $sql_unsubmit_sc .="and ( a.id not in(".implode(',',$invoice_id_sc).")"; else $sql_unsubmit_sc .=" and a.id not in(".implode(',',$invoice_id_sc).")";
			$q++;
		}
		$sql_unsubmit_sc .=" )";
	}
	
	//echo $sql_unsubmit_sc."<br>";
	$sql_unsubmit_result_sc=sql_select($sql_unsubmit_sc);
	foreach($sql_unsubmit_result_sc as $row)
	{
		$unsubmit_inv[$row[csf('id')]]['id']=$row[csf('id')];
		$unsubmit_inv[$row[csf('id')]]['type']=$row[csf('type')];
		$unsubmit_inv[$row[csf('id')]]['buyer_id']=$row[csf('buyer_id')];
		$unsubmit_inv[$row[csf('id')]]['invoice_no']=$row[csf('invoice_no')];
		$unsubmit_inv[$row[csf('id')]]['invoice_quantity']=$row[csf('invoice_quantity')];
		$unsubmit_inv[$row[csf('id')]]['invoice_value']=$row[csf('invoice_value')];
		$unsubmit_inv[$row[csf('id')]]['export_lc_no']=$row[csf('export_lc_no')];
		$unsubmit_inv[$row[csf('id')]]['lc_sc_val']=$row[csf('lc_sc_val')];
		$unsubmit_inv[$row[csf('id')]]['lc_sc_id']=$row[csf('lc_sc_id')];
		$unsubmit_inv[$row[csf('id')]]['remarks']=$row[csf('remarks')];
		$unsubmit_inv[$row[csf('id')]]['ex_factory_date']=$row[csf('ex_factory_date')];
	
		$buyer_inhand_total[$row[csf('buyer_id')]]+=$row[csf('invoice_value')];
		$sales_con_id[]=$row[csf("lc_sc_id")];
		$all_invoice_sc[$row[csf("lc_sc_id")]]=$row[csf("lc_sc_id")];
	}
	
	$lc_id_arr_unique=array_unique($all_invoice_lc);
	$sc_id_arr_unique=array_unique($all_invoice_sc);
	//var_dump($all_invoice_sc);die;
	if(empty($lc_id_arr_unique)) $lc_id_arr_unique[]=0;
	if(empty($sc_id_arr_unique)) $sc_id_arr_unique[]=0;
		
	/*echo "select b.id as lc_sc_id, b.buyer_name as buyer_id, b.export_lc_no as lc_sc_no, b.lc_value as lc_sc_val, 1 as type from  com_export_lc b where  b.status_active=1 and b.is_deleted=0 and b.id not in(".implode(",",$lc_id_arr_unique).") $cbo_company $cbo_buyer_name $cbo_lien_bank
	union all
	select b.id as lc_sc_id, b.buyer_name as buyer_id, b.contract_no as lc_sc_no, b.contract_value as lc_sc_val, 2 as type  from  com_sales_contract b where  b.status_active=1 and b.is_deleted=0 and b.convertible_to_lc=2 and  b.id not in(".implode(",",$sc_id_arr_unique).") $cbo_company $cbo_buyer_name $cbo_lien_bank";*/
	$sql_no_inv_lc=sql_select("select b.id as lc_sc_id, b.buyer_name as buyer_id, b.export_lc_no as lc_sc_no, b.lc_value as lc_sc_val, 1 as type from  com_export_lc b where  b.status_active=1 and b.is_deleted=0 and b.id not in(".implode(",",$lc_id_arr_unique).") $cbo_company $cbo_buyer_name $cbo_lien_bank
	union all
	select b.id as lc_sc_id, b.buyer_name as buyer_id, b.contract_no as lc_sc_no, b.contract_value as lc_sc_val, 2 as type  from  com_sales_contract b where  b.status_active=1 and b.is_deleted=0 and b.convertible_to_lc=2 and  b.id not in(".implode(",",$sc_id_arr_unique).") $cbo_company $cbo_buyer_name $cbo_lien_bank
	");
	
	
	foreach($sql_no_inv_lc as $row)
	{
		$no_inv_lc[$row[csf('lc_sc_id')]]['id'] = $row[csf('lc_sc_id')];
		$no_inv_lc[$row[csf('lc_sc_id')]]['type']=$row[csf('type')];
		$no_inv_lc[$row[csf('lc_sc_id')]]['buyer_id']=$row[csf('buyer_id')];
		$no_inv_lc[$row[csf('lc_sc_id')]]['lc_sc_no']=$row[csf('lc_sc_no')];
		$no_inv_lc[$row[csf('lc_sc_id')]]['lc_sc_val']=$row[csf('lc_sc_val')];
	}
	//print_r($no_inv_lc);
	
	
	//var_dump($total_realize_value);die;
	//$company_library=return_library_array( "select id,company_name from lib_company", "id", "company_name"  );
	$purchase_val_arr=return_library_array("select doc_submission_mst_id, sum(lc_sc_curr) as dom_curr from com_export_doc_sub_trans where status_active =1 and	is_deleted=0 group by doc_submission_mst_id","doc_submission_mst_id","dom_curr");
	//var_dump($purchase_val_arr);die;
	/*$lc_gross_val_arr=return_library_array("select lc_sc_id, sum(invoice_value) as invoice_value from com_export_invoice_ship_mst where is_lc=1 and status_active =1 and	is_deleted=0 group by lc_sc_id","lc_sc_id","invoice_value");
	$sc_gross_val_arr=return_library_array("select lc_sc_id, sum(invoice_value) as invoice_value from com_export_invoice_ship_mst where is_lc=2 and status_active =1 and	is_deleted=0 group by lc_sc_id","lc_sc_id","invoice_value");*/
	
	$submission_value_array=return_library_array("select a.id, sum(b.net_invo_value) as invoice_value from com_export_doc_submission_mst a, com_export_doc_submission_invo b where a.id=b.doc_submission_mst_id and a.entry_form=40 and b.status_active=1 and b.is_deleted=0 group by a.id","id","invoice_value");
	
	

		//lc wise table create here
		//exclude full realize buyer/only full realize wise data create here 
			
		$k=1;$i=1;$purchase=0;$gt_total_val=0;$total_inv_qty=0;$total_invoice_val=0;$total_sub_collectin=0;$total_sub_pur=0;$all_purchase=0;$total_in_hand=0;$total_freight=0;$total_paid=0;$total_panding=0;$total_parcentage=0;$html_data="";$total_advice_amt=0;$lc_id_arr=array();$tr_color=array();
		
		foreach($total_realize_value as $rlz_sub_id=>$row_result)
		{
			
			//echo $row_result[('distribution')].jahid;die;
			if ($k%2==0)
			$bgcolor="#E9F3FF";
			else
			$bgcolor="#FFFFFF";
			$sub_collection=0;
			if($cbo_search_by==1)
			{
				$purchase=$purchase_val_arr[$row_result['sub_id']];
				$sub_extra=$row_result['sub_purchase']- $purchase;
				if($row_result['submit_type']==1)
				{
					$sub_collection= $row_result[('sub_collection')];
					$sub_purchase=0;
					$purchase=0;
					$advice_amt=$row_result[('advice_amount')];
				}
				else
				{
					$sub_collection= $sub_extra;
					$sub_purchase=$row_result[('sub_purchase')];
					$purchase=$purchase;
					$advice_amt=0;
				}
					
				$onclick=" change_color('tr_".$k."','".$bgcolor."')";
				$html_data.='<tr bgcolor="'.$bgcolor.'" onclick="'.$onclick.'" id="tr_'.$k.'">';
				$html_data.='<td  width="50"><p>'.$k.'</p></td>
							<td  width="120"><p>'.$buyer_arr[$row_result["buyer_id"]].'</p></td>';
							if($row_result["is_lc"]==1)
							{
					$html_data.='<td  width="50" align="center"><p>Lc</p></td>
								<td  width="130"><p>'.$lc_result[$row_result[('lc_id')]]["lc_no"].'</p></td>';
							}
							else
							{
					$html_data.='<td  width="50" align="center"><p>Sc</p></td>
								<td  width="130"><p>'.$sc_result[$row_result[('lc_id')]]["lc_no"].'</p></td>';
							}
							if($row_result["is_lc"]==1)
							{
								if(!in_array($row_result['lc_id'],$lc_id_arr))
								{
					$html_data.='<td align="right" width="110"><p>'. number_format($lc_result[$row_result[('lc_id')]]["lc_val"],2,".","").'</p></td>';
								$gt_total_val+=$lc_result[$row_result[('lc_id')]]["lc_val"];
								//$buyer_result[$row_result["buyer_id"]]['lc_val']+=$lc_result[$row_result[('lc_id')]]["lc_val"];
								$lc_id_arr[$row_result['lc_id']]=$row_result['lc_id'];
								}
								else
								{
					$html_data.='<td align="right" width="110"><p></p></td>';
								}
							
							}
							else
							{
								if(!in_array($row_result['lc_id'],$sc_check_id_arr))
								{
					$html_data.='<td align="right" width="110"><p>'. number_format($sc_result[$row_result[('lc_id')]]["lc_val"],2,".","").'</p></td>';
								$gt_total_val+=$sc_result[$row_result[('lc_id')]]["lc_val"];
								//$buyer_result[$row_result["buyer_id"]]['lc_val']+=$sc_result[$row_result[('lc_id')]]["lc_val"];
								$sc_check_id_arr[$row_result['lc_id']]=$row_result['lc_id'];
								}
								else
								{
					$html_data.='<td align="right" width="110"><p></p></td>';
								}
							
							}
				$html_data.='<td align="center" width="110"><p>'.$row_result[('bank_ref_no')].'</p></td>
							<td align="center" width="130"><p>'.$row_result[('invoice_no')].'</p></td> 
							<td align="right" width="100"><p>'. $row_result[('invoice_quantity')].'</p></td>
							<td align="right" width="110"><p>'.number_format($row_result[('invoice_value')],2,".","").'</p></td>
							<td width="110" align="right"><p>'.number_format($sub_collection,2,".","").'</p></td>
							<td width="110" align="right"><p>'. number_format($sub_purchase,2,".","").'</p></td>
							<td width="110" align="right"><p>'. number_format($purchase,2,".","").'</p></td>
							<td width="110" align="right"><p>'.number_format($row_result[('distribution')],2,".","").'</p>
	</td>
							<td width="110" align="right"><p>'.number_format($row_result[('deducation')],2,".","").'</p>
	</td>
							<td align="right" width="110" ><p>'.number_format($advice_amt,2,".","").'</p></td>
							<td align="right" width="110"><p>'.number_format($row_result[('freight_amnt_by_supllier')],2,".","").'</p></td>
							<td align="right" width="110"><p>'. number_format($row_result[('paid_amount')],2,".","").'</p></td>
							<td width="110" align="right"><p>'. number_format($pandin_charse=$row_result[('freight_amnt_by_supllier')]- $row_result[('paid_amount')],2,".","").'</p></td>
							<td align="right" width="110"><p>'. number_format($freight_in_usd=$row_result[('freight_amnt_by_supllier')]/$txt_exchange_rate,2,".","").'</p></td>
							<td align="center"><p>'.number_format($parcentage=(($freight_in_usd/$row_result[('invoice_value')])*100),2,".","").'%												                                </p></td>
							</tr>';
								
				
				$total_inv_qty += $row_result[('invoice_quantity')];
				$total_invoice_val +$row_result[('invoice_value')];
				$total_sub_collectin +=$sub_collection;
				$total_sub_pur +=$sub_purchase;
				$all_purchase+=$purchase;
				$total_realize+=$row_result[('distribution')];
				$total_deduction+=$row_result[('deducation')];
				$total_freight +=$row_result[('freight_amnt_by_supllier')];
				$total_paid += $row_result[('paid_amount')];
				$total_panding+= $pandin_charse;
				$total_parcentage+=$parcentage;
				$total_advice_amt+=$row_result[('advice_amount')];
				
				/*if(in_array($lc_key,$rlz_sub_id))
				{
					$sub_collection=$sub_purchase=$purchase=0;
				}*/
				
				$buyer_result[$row_result["buyer_id"]]['buyer_name']=$row_result["buyer_id"];
				$buyer_result[$row_result["buyer_id"]]['invoice_quantity']+=$row_result[('invoice_quantity')];
				$buyer_result[$row_result["buyer_id"]]['invoice_value']+=$row_result[('invoice_value')];
				/*$buyer_result[$row_result["buyer_id"]]['sub_collection']+=$sub_collection;
				$buyer_result[$row_result["buyer_id"]]['sub_purchase']+=$sub_purchase;
				$buyer_result[$row_result["buyer_id"]]['purchase']+=$purchase;*/
				$buyer_result[$row_result["buyer_id"]]['distribution']+=$row_result[('distribution')];
				$buyer_result[$row_result["buyer_id"]]['deducation']+=$row_result[('deducation')];
				$buyer_result[$row_result["buyer_id"]]['advice_amount']+=$advice_amt;
				$buyer_result[$row_result["buyer_id"]]['freight_amnt_by_supllier']+=$row_result[('freight_amnt_by_supllier')];
				$buyer_result[$row_result["buyer_id"]]['paid_amount']+=$row_result[('paid_amount')];
				$buyer_result[$row_result["buyer_id"]]['pandin_charse']+=$pandin_charse;
				$buyer_result[$row_result["buyer_id"]]['freight_in_usd']+=$freight_in_usd;
			}
			
			if($cbo_search_by==2)
			{
				/*if($row_result[('is_lc')]==1)
				{
					$sub_inv_gros_val=$lc_gross_val_arr[$row_result[('lc_id')]];
				}
				else
				{
					$sub_inv_gros_val=$sc_gross_val_arr[$row_result[('lc_id')]];
				}
				
				if($sub_inv_gros_val!=$total_realize_value[$row_result[('sub_id')]]['rlz_val']) $total_realize_value[$row_result[('sub_id')]]['rlz_val']=$sub_inv_gros_val;*/
				//Excluded full realize
				if($submission_value_array[$row_result[('sub_id')]]>$total_realize_value[$row_result[('sub_id')]]['rlz_val'])
				{
					$purchase=$purchase_val_arr[$row_result['sub_id']];
					$sub_extra=$row_result['sub_purchase']- $purchase;
					if($row_result['submit_type']==1)
					{
						$sub_collection= $row_result[('sub_collection')];
						$sub_purchase=0;
						$purchase=0;
						$advice_amt=$row_result[('advice_amount')];
					}
					else
					{
						$sub_collection= $sub_extra;
						$sub_purchase=$row_result[('sub_purchase')];
						$purchase=$purchase;
						$advice_amt=0;
					}
					
					$onclick=" change_color('tr_".$k."','".$bgcolor."')";
					$html_data.='<tr bgcolor="'.$bgcolor.'" onclick="'.$onclick.'" id="tr_'.$k.'">';
					$html_data.='<td  width="50"><p>'.$k.'</p></td>
								<td  width="120"><p>'.$buyer_arr[$row_result["buyer_id"]].'</p></td>';
								if($row_result["is_lc"]==1)
								{
						$html_data.='<td  width="50" align="center"><p>Lc</p></td>
									<td  width="130"><p>'.$lc_result[$row_result[('lc_id')]]["lc_no"].'</p></td>';
								}
								else
								{
						$html_data.='<td  width="50" align="center"><p>Sc</p></td>
									<td  width="130"><p>'.$sc_result[$row_result[('lc_id')]]["lc_no"].'</p></td>';
								}
								if($row_result["is_lc"]==1)
								{
									if(!in_array($row_result['lc_id'],$lc_id_arr))
									{
						$html_data.='<td align="right" width="110"><p>'. number_format($lc_result[$row_result[('lc_id')]]["lc_val"],2,".","").'</p></td>';
									$gt_total_val+=$lc_result[$row_result[('lc_id')]]["lc_val"];
									//$buyer_result[$row_result["buyer_id"]]['lc_val']+=$lc_result[$row_result[('lc_id')]]["lc_val"];
									$lc_id_arr[$row_result['lc_id']]=$row_result['lc_id'];
									}
									else
									{
						$html_data.='<td align="right" width="110"><p></p></td>';
									}
								
								}
								else
								{
									if(!in_array($row_result['lc_id'],$sc_check_id_arr))
									{
						$html_data.='<td align="right" width="110"><p>'. number_format($sc_result[$row_result[('lc_id')]]["lc_val"],2,".","").'</p></td>';
									$gt_total_val+=$sc_result[$row_result[('lc_id')]]["lc_val"];
									//$buyer_result[$row_result["buyer_id"]]['lc_val']+=$sc_result[$row_result[('lc_id')]]["lc_val"];
									$sc_check_id_arr[$row_result['lc_id']]=$row_result['lc_id'];
									}
									else
									{
						$html_data.='<td align="right" width="110"><p></p></td>';
									}
								
								}
					$html_data.='<td align="center" width="110"><p>'.$row_result[('bank_ref_no')].'</p></td>
								<td align="center" width="130"><p>'.$row_result[('invoice_no')].'</p></td> 
								<td align="right" width="100"><p>'. $row_result[('invoice_quantity')].'</p></td>
								<td align="right" width="110"><p>'.number_format($row_result[('invoice_value')],2,".","").'</p></td>
								<td width="110" align="right"><p>'.number_format($sub_collection,2,".","").'</p></td>
								<td width="110" align="right"><p>'. number_format($sub_purchase,2,".","").'</p></td>
								<td width="110" align="right"><p>'. number_format($purchase,2,".","").'</p></td>
								<td width="110" align="right"><p>'.number_format($row_result[('distribution')],2,".","").'</p>
		</td>
								<td width="110" align="right"><p>'.number_format($row_result[('deducation')],2,".","").'</p>
		</td>
								<td align="right" width="110" ><p>'.number_format($advice_amt,2,".","").'</p></td>
								<td align="right" width="110"><p>'.number_format($row_result[('freight_amnt_by_supllier')],2,".","").'</p></td>
								<td align="right" width="110"><p>'. number_format($row_result[('paid_amount')],2,".","").'</p></td>
								<td width="110" align="right"><p>'. number_format($pandin_charse=$row_result[('freight_amnt_by_supllier')]- $row_result[('paid_amount')],2,".","").'</p></td>
								<td align="right" width="110"><p>'. number_format($freight_in_usd=$row_result[('freight_amnt_by_supllier')]/$txt_exchange_rate,2,".","").'</p></td>
								<td align="center"><p>'.number_format($parcentage=(($freight_in_usd/$row_result[('invoice_value')])*100),2,".","").'%												                                </p></td>
								</tr>';
									
					
					$total_inv_qty += $row_result[('invoice_quantity')];
					$total_invoice_val +$row_result[('invoice_value')];
					$total_sub_collectin +=$sub_collection;
					$total_sub_pur +=$sub_purchase;
					$all_purchase+=$purchase;
					$total_realize+=$row_result[('distribution')];
					$total_deduction+=$row_result[('deducation')];
					$total_freight +=$row_result[('freight_amnt_by_supllier')];
					$total_paid += $row_result[('paid_amount')];
					$total_panding+= $pandin_charse;
					$total_parcentage+=$parcentage;
					$total_advice_amt+=$row_result[('advice_amount')];
					
					
					/*if(in_array($lc_key,$rlz_sub_id))
					{
						$sub_collection=$sub_purchase=$purchase=0;
					}*/
					
					
					$buyer_result[$row_result["buyer_id"]]['buyer_name']=$row_result["buyer_id"];
					$buyer_result[$row_result["buyer_id"]]['invoice_quantity']+=$row_result[('invoice_quantity')];
					$buyer_result[$row_result["buyer_id"]]['invoice_value']+=$row_result[('invoice_value')];
					$buyer_result[$row_result["buyer_id"]]['sub_collection']+=$sub_collection;
					$buyer_result[$row_result["buyer_id"]]['sub_purchase']+=$sub_purchase;
					$buyer_result[$row_result["buyer_id"]]['purchase']+=$purchase;
					$buyer_result[$row_result["buyer_id"]]['distribution']+=$row_result[('distribution')];
					$buyer_result[$row_result["buyer_id"]]['deducation']+=$row_result[('deducation')];
					$buyer_result[$row_result["buyer_id"]]['advice_amount']+=$advice_amt;
					$buyer_result[$row_result["buyer_id"]]['freight_amnt_by_supllier']+=$row_result[('freight_amnt_by_supllier')];
					$buyer_result[$row_result["buyer_id"]]['paid_amount']+=$row_result[('paid_amount')];
					$buyer_result[$row_result["buyer_id"]]['pandin_charse']+=$pandin_charse;
					$buyer_result[$row_result["buyer_id"]]['freight_in_usd']+=$freight_in_usd;
				}
			
			}
		
			if($cbo_search_by==3)
			{
				
				/*if($row_result[('is_lc')]==1)
				{
					$sub_inv_gros_val=$lc_gross_val_arr[$row_result[('lc_id')]];
				}
				else
				{
					$sub_inv_gros_val=$sc_gross_val_arr[$row_result[('lc_id')]];
				}
				//$sub_inv_gros_val=return_field_value("sum(invoice_value) as invoice_value","com_export_invoice_ship_mst","id in($inv_id)","invoice_value");
				//var_dump($sub_inv_gros_val);var_dump($total_realize_value[$row_result[csf('sub_id')]]['rlz_val']);echo $lc_result[$row_result[csf('lc_id')]]["lc_val"];
				if($sub_inv_gros_val!=$total_realize_value[$row_result[('sub_id')]]['rlz_val']) $total_realize_value[$row_result[('sub_id')]]['rlz_val']=$sub_inv_gros_val;*/
				
				//only full realize
				
				if($total_realize_value[$row_result[('sub_id')]]['rlz_val']>=$submission_value_array[$row_result[('sub_id')]])
				{
					$purchase=$purchase_val_arr[$row_result['sub_id']];
					$sub_extra=$row_result['sub_purchase']- $purchase;
					if($row_result['submit_type']==1)
					{
						$sub_collection= $row_result[('sub_collection')];
						$sub_purchase=0;
						$purchase=0;
						$advice_amt=$row_result[('advice_amount')];
					}
					else
					{
						$sub_collection= $sub_extra;
						$sub_purchase=$row_result[('sub_purchase')];
						$purchase=$purchase;
						$advice_amt=0;
					}
						
					$onclick=" change_color('tr_".$k."','".$bgcolor."')";
					$html_data.='<tr bgcolor="'.$bgcolor.'" onclick="'.$onclick.'" id="tr_'.$k.'">';
					$html_data.='<td  width="50"><p>'.$k.'</p></td>
								<td  width="120"><p>'.$buyer_arr[$row_result["buyer_id"]].'</p></td>';
								if($row_result["is_lc"]==1)
								{
						$html_data.='<td  width="50" align="center"><p>Lc</p></td>
									<td  width="130"><p>'.$lc_result[$row_result[('lc_id')]]["lc_no"].'</p></td>';
								}
								else
								{
						$html_data.='<td  width="50" align="center"><p>Sc</p></td>
									<td  width="130"><p>'.$sc_result[$row_result[('lc_id')]]["lc_no"].'</p></td>';
								}
								if($row_result["is_lc"]==1)
								{
									if(!in_array($row_result['lc_id'],$lc_id_arr))
									{
						$html_data.='<td align="right" width="110"><p>'. number_format($lc_result[$row_result[('lc_id')]]["lc_val"],2,".","").'</p></td>';
									$gt_total_val+=$lc_result[$row_result[('lc_id')]]["lc_val"];
									//$buyer_result[$row_result["buyer_id"]]['lc_val']+=$lc_result[$row_result[('lc_id')]]["lc_val"];
									$lc_id_arr[$row_result['lc_id']]=$row_result['lc_id'];
									}
									else
									{
						$html_data.='<td align="right" width="110"><p></p></td>';
									}
								
								}
								else
								{
									if(!in_array($row_result['lc_id'],$sc_check_id_arr))
									{
						$html_data.='<td align="right" width="110"><p>'. number_format($sc_result[$row_result[('lc_id')]]["lc_val"],2,".","").'</p></td>';
									$gt_total_val+=$sc_result[$row_result[('lc_id')]]["lc_val"];
									//$buyer_result[$row_result["buyer_id"]]['lc_val']+=$sc_result[$row_result[('lc_id')]]["lc_val"];
									$sc_check_id_arr[$row_result['lc_id']]=$row_result['lc_id'];
									}
									else
									{
						$html_data.='<td align="right" width="110"><p></p></td>';
									}
								
								}
					$html_data.='<td align="center" width="110"><p>'.$row_result[('bank_ref_no')].'</p></td>
								<td align="center" width="130"><p>'.$row_result[('invoice_no')].'</p></td> 
								<td align="right" width="100"><p>'. $row_result[('invoice_quantity')].'</p></td>
								<td align="right" width="110"><p>'.number_format($row_result[('invoice_value')],2,".","").'</p></td>
								<td width="110" align="right"><p>'.number_format($sub_collection,2,".","").'</p></td>
								<td width="110" align="right"><p>'. number_format($sub_purchase,2,".","").'</p></td>
								<td width="110" align="right"><p>'. number_format($purchase,2,".","").'</p></td>
								<td width="110" align="right"><p>'.number_format($row_result[('distribution')],2,".","").'</p>
		</td>
								<td width="110" align="right"><p>'.number_format($row_result[('deducation')],2,".","").'</p>
		</td>
								<td align="right" width="110" ><p>'.number_format($advice_amt,2,".","").'</p></td>
								<td align="right" width="110"><p>'.number_format($row_result[('freight_amnt_by_supllier')],2,".","").'</p></td>
								<td align="right" width="110"><p>'. number_format($row_result[('paid_amount')],2,".","").'</p></td>
								<td width="110" align="right"><p>'. number_format($pandin_charse=$row_result[('freight_amnt_by_supllier')]- $row_result[('paid_amount')],2,".","").'</p></td>
								<td align="right" width="110"><p>'. number_format($freight_in_usd=$row_result[('freight_amnt_by_supllier')]/$txt_exchange_rate,2,".","").'</p></td>
								<td align="center"><p>'.number_format($parcentage=(($freight_in_usd/$row_result[('invoice_value')])*100),2,".","").'%												                                </p></td>
								</tr>';
									
					
					$total_inv_qty += $row_result[('invoice_quantity')];
					$total_invoice_val +$row_result[('invoice_value')];
					$total_sub_collectin +=$sub_collection;
					$total_sub_pur +=$sub_purchase;
					$all_purchase+=$purchase;
					$total_realize+=$row_result[('distribution')];
					$total_deduction+=$row_result[('deducation')];
					$total_freight +=$row_result[('freight_amnt_by_supllier')];
					$total_paid += $row_result[('paid_amount')];
					$total_panding+= $pandin_charse;
					$total_parcentage+=$parcentage;
					$total_advice_amt+=$row_result[('advice_amount')];
					
					/*
					if(in_array($lc_key,$rlz_sub_id))
					{
						$sub_collection=$sub_purchase=$purchase=0;
					}*/
					
					
					$buyer_result[$row_result["buyer_id"]]['buyer_name']=$row_result["buyer_id"];
					$buyer_result[$row_result["buyer_id"]]['invoice_quantity']+=$row_result[('invoice_quantity')];
					$buyer_result[$row_result["buyer_id"]]['invoice_value']+=$row_result[('invoice_value')];
					$buyer_result[$row_result["buyer_id"]]['sub_collection']+=$sub_collection;
					$buyer_result[$row_result["buyer_id"]]['sub_purchase']+=$sub_purchase;
					$buyer_result[$row_result["buyer_id"]]['purchase']+=$purchase;
					$buyer_result[$row_result["buyer_id"]]['distribution']+=$row_result[('distribution')];
					$buyer_result[$row_result["buyer_id"]]['deducation']+=$row_result[('deducation')];
					$buyer_result[$row_result["buyer_id"]]['advice_amount']+=$advice_amt;
					$buyer_result[$row_result["buyer_id"]]['freight_amnt_by_supllier']+=$row_result[('freight_amnt_by_supllier')];
					$buyer_result[$row_result["buyer_id"]]['paid_amount']+=$row_result[('paid_amount')];
					$buyer_result[$row_result["buyer_id"]]['pandin_charse']+=$pandin_charse;
					$buyer_result[$row_result["buyer_id"]]['freight_in_usd']+=$freight_in_usd;
				
				}
			}
		$purchase=0;$inv_id=0;$sub_inv_gros_val=0;$sub_id_con=0;
		$k++;
		}
		$m=1;$html_sub_bank_data="";
		foreach($sub_bank_arr as $sub_id=>$row_result)
		{
			if ($k%2==0)
			$bgcolor="#E9F3FF";
			else
			$bgcolor="#FFFFFF";
			$sub_collection=0;
			$bill_percent=($row_result[('total_negotiated_amount')]/$row_result[('purchase_amount')])*100;
			$sub_collection_extra=$row_result[('purchase_amount')]-$row_result[('total_negotiated_amount')];
			$sub_collection=$row_result[('sub_collection')]+$sub_collection_extra;
			$onclick=" change_color('tr_".$k."','".$bgcolor."')";
			$html_sub_bank_data.='<tr bgcolor="'.$bgcolor.'" onclick="'.$onclick.'" id="tr_'.$k.'">';
			$html_sub_bank_data.='<td  width="30"><p>'.$m.'</p></td>
								<td  width="120"><p>'.$row_result[('bank_ref_no')].'</p></td> 
								<td  width="260"><p>'.$row_result[("invoice_no")].'</p></td>
								<td align="center" width="70"><p>'. change_date_format($row_result[('bank_ref_date')]).'</p></td>
								<td align="right" width="80"><p>'. number_format($row_result[('invoice_quantity')],0,".","").'</p></td>
								<td align="right" width="100"><p>'. number_format($row_result[('net_invo_value')],2,".","").'</p></td>
								<td align="right" width="100"><p>'. number_format($sub_collection,2,".","").'</p></td>
								<td align="right" width="100"><p>'. number_format($row_result[('purchase_amount')],2,".","").'</p></td>
								<td align="right" width="100"><p>'. number_format($row_result[('total_negotiated_amount')],2,".","").'</p></td>
								<td  align="right" width="60"><p>'.number_format($bill_percent,2,".","").'</p></td>';
			if($row_result[('negotiation_date')]!="" && $row_result[('negotiation_date')]!='0000-00-00') $negotiation_date=change_date_format($row_result[('negotiation_date')]);
			$html_sub_bank_data.='<td  align="center"><p>'.$negotiation_date.'</p></td>
								</tr>';
							
					$buyer_result[$row_result["buyer_id"]]['invoice_quantity']+=$row_result[('invoice_quantity')];
					$buyer_result[$row_result["buyer_id"]]['invoice_value']+=$row_result[('net_invo_value')];
					$buyer_result[$row_result["buyer_id"]]['sub_collection']+=$sub_collection;
					$buyer_result[$row_result["buyer_id"]]['sub_purchase']+=$row_result[('purchase_amount')];
					$buyer_result[$row_result["buyer_id"]]['purchase']+=$row_result[('total_negotiated_amount')];
			
			/*if($row_result['type']==1)//for lc check $lc_id_arr
			{
				if(!in_array($row_result[('lc_sc_id')],$lc_id_arr))
				{
					$buyer_result[$row_result["buyer_id"]]['lc_val']+=$row_result["lc_sc_val"];
					$lc_id_arr[$row_result['lc_sc_id']]=$row_result['lc_sc_id'];
				}
			}
			else if($row_result['type']==2) // for sc check $sc_check_id_arr
			{
				if(!in_array($row_result[('lc_sc_id')],$sc_check_id_arr))
				{
					$buyer_result[$row_result["buyer_id"]]['lc_val']+=$row_result["lc_sc_val"];
					$sc_check_id_arr[$row_result['lc_sc_id']]=$row_result['lc_sc_id'];
				}
			}*/
			$k++;$m++;
		}
		//var_dump($buyer_result);die;
		$m=1;$html_sub_buyer_data="";
		foreach($sub_buyer_arr as $sub_id=>$row_result)
		{
			if ($k%2==0)
			$bgcolor="#E9F3FF";
			else
			$bgcolor="#FFFFFF";
			$onclick=" change_color('tr_".$k."','".$bgcolor."')";
			$html_sub_buyer_data.='<tr bgcolor="'.$bgcolor.'" onclick="'.$onclick.'" id="tr_'.$k.'">';
			$html_sub_buyer_data.='<td  width="50"><p>'.$m.'</p></td>
								<td  width="150"><p>'.$buyer_arr[$row_result[("buyer_id")]].'</p></td>
								<td  width="500"><p>'.$row_result[("invoice_no")].'</p></td>
								<td  width="80" align="center"><p>'.$sub_id.'</p></td> 
								<td align="center" width="80"><p>'. change_date_format($row_result[('submit_date')]).'</p></td>
								<td align="right" width="100"><p>'. number_format($row_result[('invoice_quantity')],0,".","").'</p></td>
								<td align="right" ><p>'. number_format($row_result[('net_invo_value')],2,".","").'</p></td>
								</tr>';
							
			
					$buyer_result[$row_result["buyer_id"]]['invoice_quantity']+=$row_result[('invoice_quantity')];
					$buyer_result[$row_result["buyer_id"]]['invoice_value']+=$row_result[('net_invo_value')];
					$buyer_result[$row_result["buyer_id"]]['sub_collection']+=$row_result[('net_invo_value')];
					$buyer_result[$row_result["buyer_id"]]['buyer_name']=$row_result["buyer_id"];
					/**/
			
			/*if($row_result['type']==1)//for lc check $lc_id_arr
			{
				if(!in_array($row_result[('lc_sc_id')],$lc_id_arr))
				{
					$buyer_result[$row_result["buyer_id"]]['lc_val']+=$row_result["lc_sc_val"];
					$lc_id_arr[$row_result['lc_sc_id']]=$row_result['lc_sc_id'];
				}
			}
			else if($row_result['type']==2) // for sc check $sc_check_id_arr
			{
				if(!in_array($row_result[('lc_sc_id')],$sc_check_id_arr))
				{
					$buyer_result[$row_result["buyer_id"]]['lc_val']+=$row_result["lc_sc_val"];
					$sc_check_id_arr[$row_result['lc_sc_id']]=$row_result['lc_sc_id'];
				}
			}*/
			
			$k++;$m++;
		}
		
		
		
		
		$m=1;$buyer_inhand=array();
		foreach($unsubmit_inv as $inv_id=>$row_result)
		{
			if ($k%2==0)
			$bgcolor="#E9F3FF";
			else
			$bgcolor="#FFFFFF";
			$onclick=" change_color('tr_".$k."','".$bgcolor."')";
			$html_unsubmit_data.='<tr bgcolor="'.$bgcolor.'" onclick="'.$onclick.'" id="tr_'.$k.'">';
			$html_unsubmit_data.='<td  width="50"><p>'.$m.'</p></td>
								<td  width="120"><p>'.$buyer_arr[$row_result[("buyer_id")]].'</p></td>
								<td align="center" width="130"><p>'.$row_result[('invoice_no')].'</p></td> 
								<td align="right" width="120"><p>'. $row_result[('invoice_quantity')].'</p></td>
								<td align="right" width="120"><p>'. number_format($row_result[('invoice_value')],2,".","").'</p></td>';
								if($row_result[('type')]==1)
								{
			$html_unsubmit_data.='<td align="center" width="100"><p>Lc</p></td>';
								}
								else
								{
			$html_unsubmit_data.='<td align="center" width="100"><p>Sc</p></td>';
								}
			$html_unsubmit_data.='<td   align="center" width="120"><p>'.$row_result[('export_lc_no')].'</p></td>
								<td   align="right" width="100"><p>'.number_format($row_result[('lc_sc_val')],2,".","").'</p></td>';
			if($row_result[('ex_factory_date')]!="" && $row_result[('ex_factory_date')]!='0000-00-00') $ex_fact_date=change_date_format($row_result[('ex_factory_date')]);
			$html_unsubmit_data.='<td  width="80" align="center"><p>'.$ex_fact_date.'</p></td>
								<td  ><p>'.$row_result[('remarks')].'</p></td>
			</tr>';
							
			$unsbmit_inv_qty += $row_result[('invoice_quantity')];
			$unsbmit_invoice_val +=$row_result[('invoice_value')];
			$unsubmit_lc_val +=$row_result[('lc_sc_val')];
			
			$buyer_result[$row_result["buyer_id"]]['buyer_name']=$row_result["buyer_id"];
			$buyer_result[$row_result["buyer_id"]]['invoice_quantity']+=$row_result[('invoice_quantity')];
			$buyer_result[$row_result["buyer_id"]]['invoice_value']+=$row_result[('invoice_value')];
			$buyer_inhand[$row_result["buyer_id"]]+=$row_result[('invoice_value')];
			
			$ex_fact_date="";
			
			/*if($row_result['type']==1)//for lc check $lc_id_arr
			{
				if(!in_array($row_result[('lc_sc_id')],$lc_id_arr))
				{
					$buyer_result[$row_result["buyer_id"]]['lc_val']+=$row_result["lc_sc_val"];
					$lc_id_arr[$row_result['lc_sc_id']]=$row_result['lc_sc_id'];
				}
			}
			else if($row_result['type']==2) // for sc check $sc_check_id_arr
			{
				if(!in_array($row_result[('lc_sc_id')],$sc_check_id_arr))
				{
					$buyer_result[$row_result["buyer_id"]]['lc_val']+=$row_result["lc_sc_val"];
					$sc_check_id_arr[$row_result['lc_sc_id']]=$row_result['lc_sc_id'];
				}
			}*/
			
			$k++;$m++;
		}
		//print_r($buyer_result);
		//var_dump($buyer_result);die;
		
		$n=1;$html_no_invoice_data="";
		foreach($no_inv_lc as $lc_key=>$row_result)
		{
			if ($k%2==0)
			$bgcolor="#E9F3FF";
			else
			$bgcolor="#FFFFFF";
			$onclick=" change_color('tr_".$k."','".$bgcolor."')";
			$html_no_invoice_data.='<tr bgcolor="'.$bgcolor.'" onclick="'.$onclick.'" id="tr_'.$k.'">';
			$html_no_invoice_data.='<td  width="50"><p>'.$n.'</p></td>
								<td  width="120"><p>'.$buyer_arr[$row_result[("buyer_id")]].'</p></td>';
								if($row_result[('type')]==1)
								{
			$html_no_invoice_data.='<td align="center" width="100"><p>Lc</p></td>';
								}
								else
								{
			$html_no_invoice_data.='<td align="center" width="100"><p>Sc</p></td>';
								}
			$html_no_invoice_data.='<td   align="center" width="130"><p>'.$row_result[('lc_sc_no')].'</p></td>
								<td   align="right"><p>'.number_format($row_result[('lc_sc_val')],2,".","").'</p></td>
			</tr>';
							
			
			$total_no_invoice_val +=$row_result[('lc_sc_val')];
			
			$buyer_result[$row_result["buyer_id"]]['buyer_name']=$row_result["buyer_id"];
			$buyer_result[$row_result["buyer_id"]]['invoice_quantity']+=$row_result[('invoice_quantity')];
			$buyer_result[$row_result["buyer_id"]]['invoice_value']+=$row_result[('invoice_value')];
			
			/*if($row_result["type"]==1) // for lc check
			{
				if(!in_array($row_result[('id')],$lc_id_arr))
				{
					$buyer_result[$row_result["buyer_id"]]['lc_val']+=$row_result["lc_sc_val"];
					$lc_id_arr[$row_result['lc_sc_id']]=$row_result['id'];
				}
			}
			else if($row_result["type"]==2) // for sc check
			{
				if(!in_array($row_result[('id')],$sc_check_id_arr))
				{
					$buyer_result[$row_result["buyer_id"]]['lc_val']+=$row_result["lc_sc_val"];
					$sc_check_id_arr[$row_result['lc_sc_id']]=$row_result['id'];
				}
			}*/
			$k++;$n++;
		}
		//var_dump($buyer_result);die;
			
	ob_start();
?>
<div style="width:2150px">
    <div align="left">
        <table width="1500" cellpadding="0" cellspacing="0" id="caption" align="center">
        <tr>
        <td align="center" width="100%" colspan="15" class="form_caption" ><strong style="font-size:18px">Company Name:<? echo " ".$company_library[$cbo_company_name]; ?></strong></td>
        </tr>
        <tr>  
        	<td align="center" width="100%" colspan="15" class="form_caption" ><strong style="font-size:18px"><? echo $report_title; ?></strong></td>
        </tr> 
          
  
        </table>
        <br />
        <strong style="font-size:18px;">&nbsp; Buyer Wise:</strong>
    	<br />
       <table width="1950" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="">
        <thead>
            <tr>
                <th width="50" rowspan="2">Sl</th>
                <th width="120" rowspan="2">Buyer</th>
                <th width="110" rowspan="2">LC Value USD</th>
                <th colspan="2">Invoice</th>
                <th width="110" rowspan="2">Online/ Sub. as Collection</th>
                <th width="110" rowspan="2">Online/ Sub. as Purchase</th>
                <th width="110" rowspan="2">Purchase</th>
                <th width="100" rowspan="2">%</th>
                <th width="110" rowspan="2">Realized</th>
                <th width="110" rowspan="2">Deducation</th>
                <th width="110" rowspan="2">In Hand</th>
                <th width="110" rowspan="2">Advice Received</th>
                <th width="110" rowspan="2">Freight Charges(Tk)</th>
                <th width="110" rowspan="2">Freight Paid (Tk)</th>
                <th width="110" rowspan="2">Freight Pending (Tk)</th>
                <th width="110" rowspan="2">Freight In USD</th>
                <th rowspan="2" >Freight % on Inv. Value</th>
            </tr>
            <tr>
                <th width="110">Invoice  Qty.</th>
                <th width="110" >Invoice Value</th>
            </tr>
        </thead>
        </table>
        <div style="width:1968px; overflow-y:scroll; max-height:300px;font-size:12px; overflow-x:hidden;" id="scroll_body2" align="left">
        <table width="1950" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body2" align="left">
        <tbody>
			<?
			//buyer wise table print here 
			$i=1;
			//print_r($buyer_result);
            foreach($buyer_result as $buy_key=>$row_result)
            {
				if ($k%2==0)
				$bgcolor="#E9F3FF";
				else
				$bgcolor="#FFFFFF";
				$sub_extra=$row_result['sub_purchase']- $row_result['purchase'];
            ?>
            <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $k; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $k; ?>">
                <td  width="50"><p><? echo $i; ?>&nbsp;</p></td>
                <td  width="120"><p><? echo  $buyer_arr[$buy_key]; ?>&nbsp;</p></td>
                <td align="right" width="110"><p><? echo number_format($buyer_lc_val[$buy_key],2,".","");$buyer_total_lc_val+=$buyer_lc_val[$buy_key];?>&nbsp;</p></td>
                <td align="right" width="110"><p><? echo  $row_result['invoice_quantity']; $buyer_total_inv_qty += $row_result['invoice_quantity']; ?>&nbsp;</p></td>
                <td align="right" width="110"><p><? echo number_format($row_result['invoice_value'],2,".",""); $buy_total_invoice_val +=$row_result['invoice_value']; ?>&nbsp;</p></td>
                <td width="110" align="right" title="<? echo "Online/ Sub. as Collection=Buyer Submit(collection)+Bank Submit(collection)+Lc/Sc Wise(Realized):Sub. as Collection "; ?>"><p><? echo number_format(($row_result['sub_collection']),2,".",""); $buy_total_sub_collectin +=$row_result['sub_collection'];?>&nbsp;</p></td>
                <td width="110" align="right" title="<? echo "Online/ Sub. as Purchase=Bank Submit(Bill Amount)+Lc/Sc Wise(Realized):Online/ Sub. as Purchase "; ?>"><p><? echo number_format($row_result['sub_purchase'],2,".",""); $buy_total_sub_pur +=$row_result['sub_purchase'];?>&nbsp;</p></td>
                <td width="110" align="right" title="<? echo "Purchase=Bank Submit(Purchase Amount)+Purchase"; ?>"><p><? echo number_format($row_result['purchase'],2); $buy_all_purchase+=$row_result['purchase'];?></p></td>
                <td width="100" align="right"><p><? $parcentage=($row_result['purchase']/$row_result['sub_purchase'])*100; echo number_format($parcentage,2)."%"; ?></p></td>
                <td width="110" align="right">
                <p><? echo number_format($row_result['distribution'],2,".",""); $buy_total_realize+=$row_result['distribution'];?>&nbsp;</p>
                </td>
                <td width="110" align="right">
                <p><? echo number_format($row_result['deducation'],2,".",""); $buy_total_deduction+=$row_result['deducation']; ?>&nbsp;</p>
                </td>
                <td align="right" width="110">
                <p><?  
				//echo number_format($buyer_inhand[$buy_key],2,".",""); $buy_total_in_hand +=$buyer_inhand[$buy_key];
				echo number_format($buyer_inhand_total[$buy_key],2,".",""); $buy_total_in_hand +=$buyer_inhand_total[$buy_key];  
				?>&nbsp;</p></td>
                <td align="right" width="110" ><p><?  echo number_format($row_result['advice_amount'],2,".",""); $buy_total_advice_amt +=$row_result['advice_amount'];?>&nbsp;</p></td>
                <td align="right" width="110"><p><? echo  number_format($row_result['freight_amnt_by_supllier'],2,".",""); $buy_total_freight +=$row_result['freight_amnt_by_supllier']; ?>&nbsp;</p></td>
                <td align="right" width="110"><p><? echo number_format($row_result['paid_amount'],2,".",""); $buy_total_paid += $row_result['paid_amount']; ?>&nbsp;</p></td>
                <td width="110" align="right"><p><? echo number_format($row_result['pandin_charse'],2,".","");$buy_total_panding+= $row_result['pandin_charse'];?>&nbsp;</p></td>
                <td align="right" width="110"><p><? echo number_format($row_result['freight_in_usd'],2,".","")?>&nbsp;</p></td>
                <td align="center"><p><?  $buy_parcentage=($row_result['freight_in_usd']/$row_result['invoice_value'])*100; echo number_format($buy_parcentage,2,".","");$buy_total_parcentage+=$buy_parcentage;?>%&nbsp;</p></td>
            </tr>
			<?
			$purchase=0;$inv_id=0;$sub_inv_gros_val=0;
			$k++;$i++;
			}
            ?>
        </tbody>
        </table>
        <table width="1950" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="" align="left">
        <tfoot>
            <tr>
            <th width="50" align="right"></th>
            <th width="120" align="right">Grand Total:</th>
            <th  id="value_buyer_total_lc_val" width="110"><?  echo number_format($buyer_total_lc_val,2); ?></th>
            <th  id="buyer_total_inv_qty" width="110"><? echo number_format($buyer_total_inv_qty,0); ?></th>
            <th id="value_buy_total_invoice_val" width="110"><? echo number_format($buy_total_invoice_val,2); ?></th>
            <th id="value_buy_total_sub_collectin" width="110"><? echo number_format($buy_total_sub_collectin,2); ?></th> 
            <th id="value_buy_total_sub_pur" width="110"><? echo number_format($buy_total_sub_pur,2); ?></th>
            <th id="value_buy_all_purchase" width="110"><? echo number_format($buy_all_purchase,2); ?></th>
            <th width="100">&nbsp;</th>
            <th id="value_buy_total_realize" width="110"><? echo number_format($buy_total_realize,2); ?></th>
            <th id="value_buy_total_deduction" width="110"><? echo number_format($buy_total_deduction,2); ?></th>
            <th id="value_buy_total_in_hand" width="110"><? echo number_format($buy_total_in_hand,2); ?></th>
            <th  width="110"></th>
            <th id="value_buy_total_freight" width="110"><? echo number_format($buy_total_freight,2); ?></th>
            <th id="value_buy_total_paid" width="110"><? echo number_format($buy_total_paid,2); ?></th>
            <th id="value_buy_total_panding" width="110"><? echo number_format($buy_total_panding,2); ?></th>
            <th width="110">&nbsp;</th>
            <th id="buy_total_parcentage" ><? echo number_format($buy_total_parcentage,2); ?></th>
            </tr>
        </tfoot>
        </table>
        </div>
        </div>
        
    <div align="left">	
    	<br />
        <strong style="font-size:18px;">&nbsp; Lc/Sc Wise(Realized) :</strong>
        <br />
        <table width="2130" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="">
        <thead>
            <tr>
                <th width="50">Sl</th>
                <th width="120">Buyer</th>
                <th width="50">LC/SC</th>
                <th width="130">LC/SC No</th>
                <th width="110">LC Value USD</th>
                <th width="110">Bill Number</th>
                <th width="130">Invoice No</th>
                <th width="100">Invoice Qty.</th>
                <th width="110">Invoice Value</th>
                <th width="110">Online/ Sub. as Collection</th>
                <th width="110">Online/ Sub. as Purchase</th>
                <th width="110">Purchase</th>
                <th width="110">Realized</th>
                <th width="110">Deducation</th>
                <th width="110">Advice Received</th>
                <th width="110">Freight Charges(Tk)</th>
                <th width="110">Freight Paid (Tk)</th>
                <th width="110">Freight Pending (Tk)</th>
                <th width="110">Freight In USD</th>
                <th >Freight % on Inv. Value</th>
            </tr>
        </thead>
        </table>
    
        <div style="width:2150px; overflow-y:scroll; overflow-x:hidden; max-height:300px;font-size:12px;" id="scroll_body">
        <table width="2130" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body">
        <tbody>
		<?
		//lc wise table print here
		
		echo $html_data;
		?>	
        </tbody>
        </table>
        </div>
        <table width="2130" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="">
        <tfoot>
            <tr>
            <th  align="right" width="50">&nbsp;</th>
            <th  align="right" width="120">&nbsp;</th>
            <th  align="right" width="50">&nbsp;</th>
            <th  align="right" width="130">Grand Total:</th>
            <th id="value_gt_total_val" width="110"><?  echo number_format($gt_total_val,2); ?></th>
            <th width="110" ></th>
            <th  width="130"></th>
            <th align="right" id="total_inv_qty" width="100"><? echo number_format($total_inv_qty,0); ?></th>
            <th id="value_total_invoice_val" width="110"><? echo number_format($total_invoice_val,2); ?></th>
            <th  id="value_total_sub_collectin" width="110"><? echo number_format($total_sub_collectin,2); ?></th> 
            <th id="value_total_sub_pur" width="110"><? echo number_format($total_sub_pur,2); ?></th>
            <th  id="value_all_purchase" width="110"><? echo number_format($all_purchase,2); ?></th>
            <th id="value_total_realize" width="110"><? echo number_format($total_realize,2); ?></th>
            <th id="value_total_deduction" width="110"><? echo number_format($total_deduction,2); ?></th>
            <th id="value_total_advice_amt" width="110"><? echo number_format($total_advice_amt,2); ?></th>
            <th  id="value_total_freight" width="110"><? echo number_format($total_freight,2); ?></th>
            <th id="value_total_paid" width="110"><? echo number_format($total_paid,2); ?></th>
            <th id="value_total_panding" width="110"><? echo number_format($total_panding,2); ?></th>
            <th width="110">&nbsp;</th>
            <th align="right" id="total_parcentage"><? echo number_format($total_parcentage,2); ?></th>
            </tr>
        </tfoot>
        </table>
        </div>
    	<br />
        
        <div align="left">
        <strong style="font-size:18px;">&nbsp; Submitted To Bank :</strong>
        <br />
        <table width="1100" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="" >
            <thead>
                <tr>
                    <th width="30" rowspan="2">Sl</th>
                    <th width="120" rowspan="2">Bill No</th>
                    <th width="260" rowspan="2">Invoice No</th>
                    <th width="70" rowspan="2">Bill Date</th>
                    <th width="80" rowspan="2">Bill Qty.</th>
                    <th width="100" rowspan="2">Bill Value </th>
                    <th width="100" rowspan="2">Sub Under Collection</th>
                    <th colspan="4">Sub Under Purchase</th>
                </tr>
                <tr>
                    <th width="100">Bill Amount</th>
                    <th width="100">Purchase Amount</th>
                    <th width="60">%</th>
                    <th>Purchase Date</th>
                </tr>
            </thead>
        </table>
        <div style="width:1120px; overflow-y:scroll; overflow-x:hidden; max-height:300px; font-size:12px;" id="scroll_body5" >
        <table width="1100" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body5" >
            <tbody>
            <?
				//bank submission data show here
            	echo $html_sub_bank_data;
            ?>	
            </tbody>
        </table>
        </div>
        <table width="1100" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="" >
            <tfoot>
                <tr>
                    <th width="30"></th>
                    <th width="120"></th>
                    <th width="260"></th>
                    <th width="70">Grand Total:</th>
                    <th width="80" id="total_bank_sub_qnty"><? //echo number_format($unsbmit_invoice_val,2); ?></th>
                    <th width="100" id="value_total_bank_sub_value">&nbsp; </th>
                    <th width="100" id="value_total_bank_sub_collect_value">&nbsp; </th>
                    <th width="100" id="value_total_bank_sub_nago_value">&nbsp; </th>
                    <th width="100" id="value_total_bank_sub_purchase_value">&nbsp; </th>
                    <th width="60">&nbsp; </th>
                    <th  >&nbsp; </th>
                </tr>
            </tfoot>
        </table>
        </div>
        
        <br />
        
        <div align="left">
        <strong style="font-size:18px;">&nbsp; Submitted To Buyer :</strong>
        <br />
        <table width="1100" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="" >
            <thead>
                <tr>
                    <th width="50">Sl</th>
                	<th width="150">Buyer</th>
                    <th width="500" >Invoice No</th>
                    <th width="80" >System Id</th>
                    <th width="80" >Submit Date</th>
                    <th width="100" >Submit Qty.</th>
                    <th >Submit Value </th>
                </tr>
            </thead>
        </table>
        <div style="width:1120px; overflow-y:scroll; overflow-x:hidden; max-height:300px; font-size:12px;" id="scroll_body6" >
        <table width="1100" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body6" >
            <tbody>
            <?
            	//buyer submission data show here
            	echo $html_sub_buyer_data;
            ?>	
            </tbody>
        </table>
        </div>
        <table width="1100" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="" >
            <tfoot>
                <tr>
                    <th width="50">&nbsp;</th>
                    <th width="150">&nbsp; </th>
                    <th width="500">&nbsp;</th>
                    <th width="80">&nbsp;</th>
                    <th width="80">Grand Total:</th>
                    <th width="100" id="total_buyer_sub_qnty"><? //echo number_format($unsbmit_invoice_val,2); ?></th>
                    <th id="value_total_buyer_sub_value"><? //echo number_format($unsbmit_invoice_val,2); ?> </th>
                </tr>
            </tfoot>
        </table>
        </div>
        
    	<br />
        <div align="left">
        <strong style="font-size:18px;">&nbsp; Un-Submitted Invoice :</strong>
        <br />

        <table width="1250" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="" >
        <thead>
            <tr>
                <th width="50">Sl</th>
                <th width="120">Buyer</th>
                <th width="130">Invoice No</th>
                <th width="120">Invoice Qty.</th>
                <th width="120">Invoice Value</th>
                <th width="100">LC/SC </th>
                <th width="120">Lc/SC No</th>
                <th width="100">Lc/SC Value</th>
                <th width="80">Ex-factory Date </th>
                <th >Remarks</th>
            </tr>
        </thead>
        </table>
    
        <div style="width:1270px; overflow-y:scroll; overflow-x:hidden; max-height:300px; font-size:12px;" id="scroll_body3" >
        <table width="1250" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body3" >
        <tbody>
		<?
		//Unsubmitted Invoice data  print here
		echo $html_unsubmit_data;
		?>	
        </tbody>
        </table>
        </div>
        <table width="1250" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="" >
        <tfoot>
            <tr>
                <th  align="right" width="50"></th>
                <th  align="right"  width="120"></th>
                <th  align="right"  width="130">Grand Total:</th>
                <th id="unsbmit_inv_qty"  width="120"><?  echo number_format($unsbmit_inv_qty,0); ?></th>
                <th align="right" id="value_unsbmit_invoice_val"  width="120"><? echo number_format($unsbmit_invoice_val,2); ?></th>
                <th align="right"  width="100">&nbsp; </th>
                <th align="right"  width="120">&nbsp; </th>
                <th align="right" width="100">&nbsp; </th>
                <th align="right" width="80">&nbsp; </th>
                <th  >&nbsp; </th>
            </tr>
        </tfoot>
        </table>
        </div>
        
        <div align="left">
        
    	<br />
        <strong style="font-size:18px;">&nbsp; No Invoice Lc/Sc :</strong>
        <br />

        <table width="550" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="" >
        <thead>
            <tr>
                <th width="50">Sl</th>
                <th width="120">Buyer</th>
                <th width="100">Lc/Sc</th>
                <th width="130">Lc No</th>
                <th >Lc Value</th>
            </tr>
        </thead>
        </table>
    
        <div style="width:570px; overflow-y:scroll; overflow-x:hidden; max-height:300px; font-size:12px;" id="scroll_body4" >
        <table width="550" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="table_body4" >
        <tbody>
		<?
		//No Invoice data  print here
		echo $html_no_invoice_data;
		?>	
        </tbody>
        </table>
        </div>
        <table width="550" class="rpt_table" cellpadding="0" cellspacing="0" border="1" rules="all" id="" >
        <tfoot>
            <tr>
            <th width="50"></th>
            <th width="120" ></th>
            <th  width="100"></th>
            <th width="130">Grand Total:</th>
            <th id="value_total_no_invoice_val"><span style="font-size:11px; font-weight:bold;"><? echo number_format($total_no_invoice_val,2); ?></span></th>
            </tr>
        </tfoot>
        </table>
        </div>
        
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
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}
disconnect($con);


?>
