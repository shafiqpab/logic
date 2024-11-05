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
	$con = connect();
	
	//echo $cbo_company_name."___".$cbo_buyer_name."___".$cbo_lien_bank."___".$cbo_search_by."___".$txt_exchange_rate;die;
	
    if($cbo_company_name ==0) $cbo_company_name =""; else $cbo_company="and b.beneficiary_name='$cbo_company_name'";
	if($cbo_buyer_name == 0) $cbo_buyer_name=""; else $cbo_buyer_name = "and b.buyer_name='$cbo_buyer_name'";
	if($cbo_lien_bank == 0) $cbo_lien_bank=""; else $cbo_lien_bank = "and b.lien_bank='$cbo_lien_bank'";
	
	

	$sc_id_group=0;$inv_id=0;$invoice_id=0;$sub_id=0;$lc_id_group=0;
	$total_inv_value=array();$total_realize_value=array();$buyer_result=array();$unsubmit_inv=array();$no_inv_lc=array();
    //B.ID AS LC_ID,  B.EXPORT_LC_NO AS EXPORT_LC_NO, B.LC_VALUE AS LC_VALUE,B.BUYER_NAME, B.REPLACEMENT_LC, 1 AS TYPE
	$sql="SELECT b.id as LC_ID, b.export_lc_no as EXPORT_LC_NO, b.lc_value as LC_VALUE, b.buyer_name as BUYER_NAME, b.replacement_lc as REPLACEMENT_LC, 1 as TYPE
	FROM  com_export_lc b
	WHERE b.status_active=1 and b.is_deleted=0 $cbo_company $cbo_buyer_name $cbo_lien_bank
	union all
	SELECT b.id as LC_ID,  b.contract_no as EXPORT_LC_NO, b.contract_value as LC_VALUE, b.buyer_name as BUYER_NAME, null as REPLACEMENT_LC, 2 as TYPE
	FROM com_sales_contract b 
	WHERE  b.status_active=1 and b.is_deleted=0 $cbo_company $cbo_buyer_name $cbo_lien_bank order by buyer_name asc";
	//echo $sql;die;
	$sql_result=sql_select($sql);
	$temp_table_id=return_field_value("max(id) as id","gbl_temp_report_id","1=1","id");
	if($temp_table_id=="") $temp_table_id=1;
	$refrID1=$refrID2=true;
	foreach($sql_result as $row)
	{
		if($row["TYPE"]==1)
		{ 
			if($lc_id_group==0) $lc_id_group=$row["LC_ID"]; 
			else $lc_id_group=$lc_id_group.",".$row["LC_ID"];
			$refrID1=execute_query("insert into GBL_TEMP_REPORT_ID (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row["LC_ID"].",1,".$user_id.")");
			if(!$refrID1)
			{
				echo "insert into GBL_TEMP_REPORT_ID (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row["LC_ID"].",1,".$user_id.")";oci_rollback($con);disconnect($con);die;
			}
			$temp_table_id++;
		} 
		if($row["TYPE"]==2)
		{ 
			if($sc_id_group==0) $sc_id_group=$row["LC_ID"]; 
			else $sc_id_group=$sc_id_group.",".$row["LC_ID"];
			$refrID2=execute_query("insert into GBL_TEMP_REPORT_ID (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row["LC_ID"].",2,".$user_id.")");
			if(!$refrID2)
			{
				echo "insert into GBL_TEMP_REPORT_ID (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row["LC_ID"].",1,".$user_id.")";oci_rollback($con);disconnect($con);die;
			}
			$temp_table_id++;
		} 
		
		$total_inv_value[$row["LC_ID"]]['lc_value']+=$row["LC_VALUE"];
		//if($row["TYPE"] == 2 && $row_result[csf('convertible_to_lc')] ==1 || $row_result[csf('convertible_to_lc')] ==3 ) $sc_value_1_3 += $row_result[csf('lc_sc_value')];
		
		if($row["TYPE"]==1)
		{
			$lc_result[$row["LC_ID"]]["type"]=$row["TYPE"];
			$lc_result[$row["LC_ID"]]["lc_no"]=$row["EXPORT_LC_NO"];
			$lc_result[$row["LC_ID"]]["lc_val"]+=$row["LC_VALUE"];
			if($row["REPLACEMENT_LC"] == 2) 
			{
				$buyer_lc_val[$row["BUYER_NAME"]] +=$row["LC_VALUE"];
			}
		}
		if($row["TYPE"]==2)
		{
			$sc_result[$row["LC_ID"]]["type"]=$row["TYPE"];
			$sc_result[$row["LC_ID"]]["lc_no"]=$row["EXPORT_LC_NO"];
			$sc_result[$row["LC_ID"]]["lc_val"]+=$row["LC_VALUE"];
			$buyer_lc_val[$row["BUYER_NAME"]] +=$row["LC_VALUE"];
		}
	}
	
	if($refrID1 && $refrID2)
	{
		oci_commit($con);
	}
	//echo "this report is under construction ";die;
	unset($sql_result);
	
	$all_invoice_lc=array();
	$all_invoice_sc=array();
	//for realization lc
	$rlz_details_sql="select e.mst_id as MST_ID, sum(case when e.type=0 then e.document_currency else 0 end) as DEDUCATION, sum(case when e.type=1 then e.document_currency else 0 end) as DISTRIBUTION, sum(case when e.type=1 and e.account_head=1 then e.document_currency else 0 end) as NAGOTIATE_DISTRIBUTION 
	from com_export_proceed_rlzn_dtls e where e.status_active=1 and e.is_deleted=0 group by e.mst_id";
	//echo $rlz_details_sql;die;
	$rlz_details_sql_result=sql_select($rlz_details_sql);
	foreach($rlz_details_sql_result as $row)
	{
		$rlz_dtls_arr[$row["MST_ID"]]["mst_id"]=$row["MST_ID"];
		$rlz_dtls_arr[$row["MST_ID"]]["deducation"]=$row["DEDUCATION"];
		$rlz_dtls_arr[$row["MST_ID"]]["distribution"]=$row["DISTRIBUTION"];
		$rlz_dtls_arr[$row["MST_ID"]]["nagotiate_distribution"]=$row["NAGOTIATE_DISTRIBUTION"];
	}
	unset($rlz_details_sql_result);
	
	if(empty($lc_id_group)) $lc_id_group=0;
	if(empty($sc_id_group)) $sc_id_group=0;
	
	$sql_relz_lc="SELECT a.id as INVOICE_ID, a.invoice_no as INVOICE_NO, a.invoice_quantity as INVOICE_QUANTITY, a.net_invo_value as INVOICE_VALUE, a.freight_amnt_by_supllier as FREIGHT_AMNT_BY_SUPLLIER, a.paid_amount as PAID_AMOUNT, a.advice_amount as ADVICE_AMOUNT, (b.lc_sc_id) as LC_ID, (b.is_lc) as IS_LC, c.id as SUB_ID, c.bank_ref_no as BANK_REF_NO, c.submit_type as SUBMIT_TYPE, c.buyer_id as BUYER_ID, (case when c.submit_type=1 then b.net_invo_value end) as SUB_COLLECTION, (case when c.submit_type=2 then b.net_invo_value end) as SUB_PURCHASE, d.id as RLZ_ID
	from gbl_temp_report_id p, com_export_invoice_ship_mst a, com_export_doc_submission_invo b, com_export_doc_submission_mst c, com_export_proceed_realization d
	where a.id=b.invoice_id and b.doc_submission_mst_id=c.id and c.id=d.invoice_bill_id and a.is_lc=1 and a.lc_sc_id=p.ref_val and p.ref_from=1 and p.user_id=$user_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.entry_form=40";
	//echo $sql_relz_lc."<br>";die;
	$rlz_lc_result=sql_select($sql_relz_lc);
	$rlz_sub_lc_id=array();
	$invoice_rlz_lc_id=0;
	foreach($rlz_lc_result as $row)
	{
		if($invoice_rlz_lc_id==0) $invoice_rlz_lc_id=$row["INVOICE_ID"]; else $invoice_rlz_lc_id=$invoice_rlz_lc_id.",".$row["INVOICE_ID"];
		$all_invoice_lc[$row["LC_ID"]]=$row["LC_ID"];
		
		$rlz_sub_lc_id[]=$row["SUB_ID"];
		if($rlz_sub_lc_id_check[$row["SUB_ID"]]=="")
		{
			$rlz_sub_lc_id_check[$row["SUB_ID"]]=$row["SUB_ID"];
			$refrID2=execute_query("insert into GBL_TEMP_REPORT_ID (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row["SUB_ID"].",3,".$user_id.")");
			if(!$refrID2)
			{
				echo "insert into GBL_TEMP_REPORT_ID (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row["SUB_ID"].",3,".$user_id.")";oci_rollback($con);disconnect($con);die;
			}
			$temp_table_id++;
			
			$total_realize_value[$row["SUB_ID"]]["is_lc"]=$row["IS_LC"];

			$total_realize_value[$row["SUB_ID"]]["lc_id"]=$row["LC_ID"];
			$total_realize_value[$row["SUB_ID"]]["sub_id"]=$row["SUB_ID"];
			$total_realize_value[$row["SUB_ID"]]["buyer_id"]=$row["BUYER_ID"];
			$total_realize_value[$row["SUB_ID"]]["bank_ref_no"]=$row["BANK_REF_NO"];
			$total_realize_value[$row["SUB_ID"]]["sub_collection"]+=$row["SUB_COLLECTION"];
			$total_realize_value[$row["SUB_ID"]]["sub_purchase"]+=$row["SUB_PURCHASE"];
			$total_realize_value[$row["SUB_ID"]]["submit_type"]+=$row["SUBMIT_TYPE"];
			
			if($rlz_lc_check[$row["RLZ_ID"]]=="")
			{
				$rlz_lc_check[$row["RLZ_ID"]]=$row["RLZ_ID"];
				$total_realize_value[$row["SUB_ID"]]['rlz_val']=$rlz_dtls_arr[$row["RLZ_ID"]]["deducation"]+$rlz_dtls_arr[$row["RLZ_ID"]]["distribution"];
				$total_realize_value[$row["SUB_ID"]]['deducation']+=$rlz_dtls_arr[$row["RLZ_ID"]]["deducation"];
				$total_realize_value[$row["SUB_ID"]]['distribution']+=$rlz_dtls_arr[$row["RLZ_ID"]]["distribution"];
				$total_realize_value[$row["SUB_ID"]]['nagotiate_distribution']+=$rlz_dtls_arr[$row["RLZ_ID"]]["nagotiate_distribution"];
			}
		}
		
		if($all_invoice_lc_check[$row["INVOICE_ID"]]=="")
		{
			$all_invoice_lc_check[$row["INVOICE_ID"]]=$row["INVOICE_ID"];
			$invs_id_arr=array_unique(explode(",",$row["INVOICE_ID"]));
			foreach($invs_id_arr as $val)
			{
				if($val)
				{
					$refrID2=execute_query("insert into GBL_TEMP_REPORT_ID (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$val.",5,".$user_id.")");
					if(!$refrID2)
					{
						echo "insert into GBL_TEMP_REPORT_ID (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$val.",5,".$user_id.")";oci_rollback($con);disconnect($con);die;
					}
					$temp_table_id++;
				}
			}
			$total_realize_value[$row["SUB_ID"]]["invoice_no"].=$row["INVOICE_NO"].",";
			$total_realize_value[$row["SUB_ID"]]["invoice_quantity"]+=$row["INVOICE_QUANTITY"];
			$total_realize_value[$row["SUB_ID"]]["invoice_value"]+=$row["INVOICE_VALUE"];
			$total_realize_value[$row["SUB_ID"]]["advice_amount"]+=$row["ADVICE_AMOUNT"];
			$total_realize_value[$row["SUB_ID"]]["freight_amnt_by_supllier"]+=$row["FREIGHT_AMNT_BY_SUPLLIER"];
			$total_realize_value[$row["SUB_ID"]]["paid_amount"]+=$row["PAID_AMOUNT"];
		}
	}
	
	//for realization sc
	$sql_relz_sc="SELECT a.id as INVOICE_ID, a.invoice_no as INVOICE_NO, a.invoice_quantity as INVOICE_QUANTITY, a.net_invo_value as INVOICE_VALUE, a.freight_amnt_by_supllier as FREIGHT_AMNT_BY_SUPLLIER, a.paid_amount as PAID_AMOUNT, a.advice_amount as ADVICE_AMOUNT, b.lc_sc_id as LC_ID, b.is_lc as IS_LC, c.id as SUB_ID, c.bank_ref_no as BANK_REF_NO, c.submit_type as SUBMIT_TYPE, c.buyer_id as BUYER_ID, (case when c.submit_type=1 then b.net_invo_value end) as SUB_COLLECTION, (case when c.submit_type=2 then b.net_invo_value end) as SUB_PURCHASE, d.id as RLZ_ID
	from gbl_temp_report_id p, com_export_invoice_ship_mst a, com_export_doc_submission_invo b, com_export_doc_submission_mst c, com_export_proceed_realization d
	where a.id=b.invoice_id and b.doc_submission_mst_id=c.id and c.id=d.invoice_bill_id and a.is_lc=2 and a.lc_sc_id=p.ref_val and p.ref_from=2 and p.user_id=$user_id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and d.status_active=1 and d.is_deleted=0 and c.entry_form=40";
	
	//echo $sql_relz_sc."<br>";die;

	$rlz_sc_result=sql_select($sql_relz_sc);
	
	$rlz_sub_sc_id=array();
	$invoice_rlz_sc_id=0;
	foreach($rlz_sc_result as $row)
	{
		
		if($invoice_rlz_sc_id==0) $invoice_rlz_sc_id=$row["INVOICE_ID"]; else $invoice_rlz_sc_id=$invoice_rlz_sc_id.",".$row["INVOICE_ID"];
		$all_invoice_sc[$row["LC_ID"]]=$row["LC_ID"];
		
		
		$rlz_sub_sc_id[]=$row["SUB_ID"];
		if($rlz_sub_sc_id_check[$row["SUB_ID"]]=="")
		{
			$rlz_sub_sc_id_check[$row["SUB_ID"]]=$row["SUB_ID"];
			$refrID2=execute_query("insert into GBL_TEMP_REPORT_ID (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row["SUB_ID"].",4,".$user_id.")");
			if(!$refrID2)
			{
				echo "insert into GBL_TEMP_REPORT_ID (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row["SUB_ID"].",4,".$user_id.")";oci_rollback($con);die;
			}
			$temp_table_id++;
			$total_realize_value[$row["SUB_ID"]]["is_lc"]=$row["IS_LC"];
			$total_realize_value[$row["SUB_ID"]]["lc_id"]=$row["LC_ID"];
			$total_realize_value[$row["SUB_ID"]]["sub_id"]=$row["SUB_ID"];
			$total_realize_value[$row["SUB_ID"]]["buyer_id"]=$row["BUYER_ID"];
			$total_realize_value[$row["SUB_ID"]]["bank_ref_no"]=$row["BANK_REF_NO"];
			
			$total_realize_value[$row["SUB_ID"]]["sub_collection"]+=$row["SUB_COLLECTION"];
			$total_realize_value[$row["SUB_ID"]]["sub_purchase"]+=$row["SUB_PURCHASE"];
			$total_realize_value[$row["SUB_ID"]]["submit_type"]+=$row["SUBMIT_TYPE"];
		
			if($rlz_sc_check[$row["RLZ_ID"]]=="")
			{
				$rlz_sc_check[$row["RLZ_ID"]]=$row["RLZ_ID"];
				$total_realize_value[$row["SUB_ID"]]['rlz_val']=$rlz_dtls_arr[$row["RLZ_ID"]]["deducation"]+$rlz_dtls_arr[$row["RLZ_ID"]]["distribution"];
				$total_realize_value[$row["SUB_ID"]]['deducation']+=$rlz_dtls_arr[$row["RLZ_ID"]]["deducation"];
				$total_realize_value[$row["SUB_ID"]]['distribution']+=$rlz_dtls_arr[$row["RLZ_ID"]]["distribution"];
				$total_realize_value[$row["SUB_ID"]]['nagotiate_distribution']+=$rlz_dtls_arr[$row["RLZ_ID"]]["nagotiate_distribution"];
			}
		}
		
		if($all_invoice_sc_check[$row["INVOICE_ID"]]=="")
		{
			$all_invoice_sc_check[$row["INVOICE_ID"]]=$row["INVOICE_ID"];
			$invs_id_arr=array_unique(explode(",",$row["INVOICE_ID"]));
			foreach($invs_id_arr as $val)
			{
				if($val)
				{
					$refrID2=execute_query("insert into GBL_TEMP_REPORT_ID (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$val.",6,".$user_id.")");
					if(!$refrID2)
					{
						echo "insert into GBL_TEMP_REPORT_ID (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$val.",6,".$user_id.")";oci_rollback($con);disconnect($con);die;
					}
					$temp_table_id++;
				}
			}
			
			$total_realize_value[$row["SUB_ID"]]["invoice_no"].=$row["INVOICE_NO"].",";
			$total_realize_value[$row["SUB_ID"]]["invoice_quantity"]+=$row["INVOICE_QUANTITY"];
			$total_realize_value[$row["SUB_ID"]]["invoice_value"]+=$row["INVOICE_VALUE"];
			$total_realize_value[$row["SUB_ID"]]["advice_amount"]+=$row["ADVICE_AMOUNT"];
			$total_realize_value[$row["SUB_ID"]]["freight_amnt_by_supllier"]+=$row["FREIGHT_AMNT_BY_SUPLLIER"];
			$total_realize_value[$row["SUB_ID"]]["paid_amount"]+=$row["PAID_AMOUNT"];
		}
	}

	//var_dump($total_realize_value);die;
	
	
	//submissio to bank lc
	if(empty($rlz_sub_id)) $rlz_sub_id[]=0;
	$sql_sub_lc="SELECT b.invoice_id as INV_ID, a.id as SUB_ID, b.id as SUB_DTLS_ID, c.invoice_no as INVOICE_NO, (b.net_invo_value) as NET_INVO_VALUE, a.bank_ref_no as BANK_REF_NO, a.bank_ref_date as BANK_REF_DATE, (case when a.submit_type=1 then b.net_invo_value else 0 end) as SUB_COLLECTION, (case when a.submit_type=2 then b.net_invo_value else 0 end) as PURCHASE_AMOUNT, a.negotiation_date as NEGOTIATION_DATE, a.total_lcsc_currency as TOTAL_NEGOTIATED_AMOUNT, a.buyer_id as BUYER_ID, (c.lc_sc_id) as LC_SC_ID, (c.is_lc) as IS_LC, (c.invoice_quantity) as INVOICE_QUANTITY
	FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b, com_export_invoice_ship_mst c, gbl_temp_report_id p
	WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form=40 and c.is_lc=1 and c.lc_sc_id=p.REF_VAL and p.REF_FROM=1 and p.USER_ID=$user_id";
	
	if(!empty($rlz_sub_lc_id))
	{
		$sql_sub_lc.=" and a.id not in(select REF_VAL from GBL_TEMP_REPORT_ID where REF_FROM=3 and USER_ID=$user_id)";
	}
	//echo $sql_sub_lc."<br>";die;
	
	$sub_bank_lc_result=sql_select($sql_sub_lc);
	$bank_sub_id_lc=array();
	foreach($sub_bank_lc_result as $result)
	{
		if($invoice_rlz_lc_id==0) $invoice_rlz_lc_id=$result["INV_ID"]; else $invoice_rlz_lc_id=$invoice_rlz_lc_id.",".$result["INV_ID"];
		$bank_sub_id_lc[]=$result["SUB_ID"];
		$all_invoice_lc[$result["LC_SC_ID"]]=$result["LC_SC_ID"];
		if($all_invoice_lc_check[$result["INV_ID"]]=="")
		{
			$all_invoice_lc_check[$result["INV_ID"]]=$result["INV_ID"];
			$invs_id_arr=array_unique(explode(",",$result["INV_ID"]));
			foreach($invs_id_arr as $val)
			{
				if($val)
				{
					$refrID2=execute_query("insert into GBL_TEMP_REPORT_ID (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$val.",5,".$user_id.")");
					if(!$refrID2)
					{
						echo "insert into GBL_TEMP_REPORT_ID (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$val.",5,".$user_id.")";oci_rollback($con);die;
					}
					$temp_table_id++;
				}
			}
			
			$sub_bank_arr[$result["SUB_ID"]]['inv_id'].=$result["INV_ID"].",";
			$sub_bank_arr[$result["SUB_ID"]]['invoice_no'].=$result["INVOICE_NO"].",";
			$sub_bank_arr[$result["SUB_ID"]]['invoice_quantity']+=$result["INVOICE_QUANTITY"];
			$sub_bank_arr[$result["SUB_ID"]]['net_invo_value']+=$result["NET_INVO_VALUE"];
			$sub_bank_arr[$result["SUB_ID"]]['total_negotiated_amount']+=$result["TOTAL_NEGOTIATED_AMOUNT"];
		}
		
		
		
		$sub_bank_arr[$result["SUB_ID"]]['lc_sc_id']=$result["LC_SC_ID"];
		$sub_bank_arr[$result["SUB_ID"]]['is_lc']=$result["IS_LC"];
		$sub_bank_arr[$result["SUB_ID"]]['sub_id']=$result["SUB_ID"];
		$sub_bank_arr[$result["SUB_ID"]]['buyer_id']=$result["BUYER_ID"];
		$sub_bank_arr[$result["SUB_ID"]]['bank_ref_no']=$result["BANK_REF_NO"];
		$sub_bank_arr[$result["SUB_ID"]]['bank_ref_date']=$result["BANK_REF_DATE"];
		$sub_bank_arr[$result["SUB_ID"]]['negotiation_date']=$result["NEGOTIATION_DATE"];
		
		if($sub_dtls_id_check["SUB_DTLS_ID"]=="")
		{
			$sub_bank_arr[$result["SUB_ID"]]['sub_collection']+=$result["SUB_COLLECTION"];
			$sub_bank_arr[$result["SUB_ID"]]['purchase_amount']+=$result["PURCHASE_AMOUNT"];
		}
	}
	
	//submissio to bank SC
	$sql_sub_sc="SELECT b.invoice_id as INV_ID, a.id as SUB_ID, b.id as SUB_DTLS_ID, c.invoice_no as INVOICE_NO, b.net_invo_value as NET_INVO_VALUE, a.bank_ref_no as BANK_REF_NO, a.bank_ref_date as BANK_REF_DATE, (case when a.submit_type=1 then b.net_invo_value else 0 end) as SUB_COLLECTION, (case when a.submit_type=2 then b.net_invo_value else 0 end) as PURCHASE_AMOUNT, a.negotiation_date as NEGOTIATION_DATE, a.total_lcsc_currency as TOTAL_NEGOTIATED_AMOUNT, a.buyer_id as BUYER_ID, c.lc_sc_id as LC_SC_ID, c.is_lc as IS_LC, c.invoice_quantity as INVOICE_QUANTITY, c.id as SUB_INVOICE_ID
	FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b, com_export_invoice_ship_mst c, gbl_temp_report_id p
	WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.entry_form=40 and c.is_lc=2 and c.lc_sc_id=p.REF_VAL and p.REF_FROM=2 and p.USER_ID=$user_id";
	
	if(!empty($rlz_sub_sc_id))
	{
		$sql_sub_sc.=" and a.id not in(select REF_VAL from GBL_TEMP_REPORT_ID where REF_FROM=4 and USER_ID=$user_id)";
	}
	//$sql_sub_sc .=" group by  a.id, a.bank_ref_no, a.bank_ref_date, a.negotiation_date,a.total_lcsc_currency, a.buyer_id order by a.id";
	
	//echo $sql_sub_sc."<br>test";die;
	
	$sub_bank_result=sql_select($sql_sub_sc);
	$bank_sub_id_sc=array();
	foreach($sub_bank_result as $result)
	{
		if($invoice_rlz_sc_id==0) $invoice_rlz_sc_id=$result["INV_ID"]; else $invoice_rlz_sc_id=$invoice_rlz_sc_id.",".$result["INV_ID"];
		$bank_sub_id_sc[]=$result["SUB_ID"];
		$all_invoice_sc[$result["LC_SC_ID"]]=$result["LC_SC_ID"];
		if($all_invoice_sc_check[$result["INV_ID"]]=="")
		{
			$all_invoice_sc_check[$result["INV_ID"]]=$result["INV_ID"];
			$invs_id_arr=array_unique(explode(",",$result["INV_ID"]));
			foreach($invs_id_arr as $val)
			{
				if($val)
				{
					$refrID2=execute_query("insert into GBL_TEMP_REPORT_ID (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$val.",6,".$user_id.")");
					if(!$refrID2)
					{
						echo "insert into GBL_TEMP_REPORT_ID (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$val.",6,".$user_id.")";oci_rollback($con);die;
					}
					$temp_table_id++;
				}
			}
			
			$sub_bank_arr[$result["SUB_ID"]]['inv_id'].=$result["INV_ID"].",";
			$sub_bank_arr[$result["SUB_ID"]]['invoice_no'].=$result["INVOICE_NO"].",";
			$sub_bank_arr[$result["SUB_ID"]]['invoice_quantity']+=$result["INVOICE_QUANTITY"];
			$sub_bank_arr[$result["SUB_ID"]]['total_negotiated_amount']=$result["TOTAL_NEGOTIATED_AMOUNT"];
		}
		
		$sub_bank_arr[$result["SUB_ID"]]['lc_sc_id']=$result["LC_SC_ID"];
		$sub_bank_arr[$result["SUB_ID"]]['is_lc']=$result["IS_LC"];
		$sub_bank_arr[$result["SUB_ID"]]['sub_id']=$result["SUB_ID"];
		$sub_bank_arr[$result["SUB_ID"]]['buyer_id']=$result["BUYER_ID"];
		$sub_bank_arr[$result["SUB_ID"]]['bank_ref_no']=$result["BANK_REF_NO"];
		$sub_bank_arr[$result["SUB_ID"]]['bank_ref_date']=$result["BANK_REF_DATE"];
		$sub_bank_arr[$result["SUB_ID"]]['negotiation_date']=$result["NEGOTIATION_DATE"];
		
		if($all_submission_sc_check[$result["SUB_DTLS_ID"]]=="")
		{
			$all_submission_sc_check[$result["SUB_DTLS_ID"]]=$result["SUB_DTLS_ID"];
			$sub_bank_arr[$result["SUB_ID"]]['net_invo_value']+=$result["NET_INVO_VALUE"];
			$sub_bank_arr[$result["SUB_ID"]]['sub_collection']+=$result["SUB_COLLECTION"];
			$sub_bank_arr[$result["SUB_ID"]]['purchase_amount']+=$result["PURCHASE_AMOUNT"];
		}
	}
	// submissio to buyer Lc
	
	$sql_sub_lc_buy="SELECT c.id as inv_id, c.invoice_no as invoice_no, c.invoice_quantity as invoice_quantity, a.id as sub_id, a.submit_date, a.buyer_id, b.net_invo_value as net_invo_value, b.lc_sc_id as lc_sc_id, c.is_lc as is_lc
	FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b, com_export_invoice_ship_mst c, gbl_temp_report_id p
	WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id and b.is_converted=0 and a.entry_form=39 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.is_lc=1 AND c.lc_sc_id=p.REF_VAL and p.REF_FROM=1 and p.USER_ID=$user_id and c.id not in(select REF_VAL from GBL_TEMP_REPORT_ID where REF_FROM=5 and USER_ID=$user_id)";
	
	//echo $sql_sub_lc_buy."<br>";//die;
	
	$sub_buyer_result=sql_select($sql_sub_lc_buy);
	foreach($sub_buyer_result as $result)
	{
		if($invoice_rlz_lc_id==0) $invoice_rlz_lc_id=$result[csf("inv_id")]; else $invoice_rlz_lc_id=$invoice_rlz_lc_id.",".$result[csf("inv_id")];
		$all_invoice_lc[$result[csf("lc_sc_id")]]=$result[csf("lc_sc_id")];
		/*if($all_invoice_lc_check[$result[csf("lc_sc_id")]]=="")
		{
			$all_invoice_lc_check[$result[csf("lc_sc_id")]]=$result[csf("lc_sc_id")];
			$refrID2=execute_query("insert into GBL_TEMP_REPORT_ID (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$result[csf("lc_sc_id")].",3,".$user_id.")");
			if(!$refrID2)
			{
				echo "insert into GBL_TEMP_REPORT_ID (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$result[csf("lc_sc_id")].",3,".$user_id.")";oci_rollback($con);die;
			}
			$temp_table_id++;
		}*/
		
		$sub_buyer_arr[$result[csf("sub_id")]]['sub_id']=$result[csf("sub_id")];
		$sub_buyer_arr[$result[csf("sub_id")]]['lc_sc_id']=$result[csf("lc_sc_id")];
		$sub_buyer_arr[$result[csf("sub_id")]]['is_lc']=$result[csf("is_lc")];
		$sub_buyer_arr[$result[csf("sub_id")]]['submit_date']=$result[csf("submit_date")];
		$sub_buyer_arr[$result[csf("sub_id")]]['buyer_id']=$result[csf("buyer_id")];
		$sub_buyer_arr[$result[csf("sub_id")]]['inv_id'].=$result[csf("inv_id")].",";
		$sub_buyer_arr[$result[csf("sub_id")]]['invoice_no'].=$result[csf("invoice_no")].",";
		$sub_buyer_arr[$result[csf("sub_id")]]['invoice_quantity']+=$result[csf("invoice_quantity")];
		$sub_buyer_arr[$result[csf("sub_id")]]['net_invo_value']+=$result[csf("net_invo_value")];
		
	}
	//var_dump($sub_buyer_arr);die;
	
	// submissio to buyer SC
	$sql_sub_sc_buy="SELECT c.id as inv_id, c.invoice_no as invoice_no, c.invoice_quantity as invoice_quantity, a.id as sub_id, a.submit_date, a.buyer_id, b.net_invo_value as net_invo_value, b.lc_sc_id as lc_sc_id, c.is_lc as is_lc
	FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b, com_export_invoice_ship_mst c, gbl_temp_report_id p
	WHERE b.doc_submission_mst_id=a.id and b.invoice_id=c.id and b.is_converted=0 and a.entry_form=39 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and c.is_lc=2 AND c.lc_sc_id=p.REF_VAL and p.REF_FROM=2 and p.USER_ID=$user_id and c.id not in(select REF_VAL from GBL_TEMP_REPORT_ID where REF_FROM=6 and USER_ID=$user_id)";
	
	//echo $sql_sub_sc_buy."<br>";die;
	
	$sub_buyer_result=sql_select($sql_sub_sc_buy);
	foreach($sub_buyer_result as $result)
	{
		if($invoice_rlz_sc_id==0) $invoice_rlz_sc_id=$result[csf("inv_id")]; else $invoice_rlz_sc_id=$invoice_rlz_sc_id.",".$result[csf("inv_id")];
		$all_invoice_sc[$result[csf("lc_sc_id")]]=$result[csf("lc_sc_id")];
		/*if($all_invoice_sc_check[$result[csf("lc_sc_id")]]=="")
		{
			$all_invoice_sc_check[$result[csf("lc_sc_id")]]=$result[csf("lc_sc_id")];
			$refrID2=execute_query("insert into GBL_TEMP_REPORT_ID (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$result[csf("lc_sc_id")].",4,".$user_id.")");
			if(!$refrID2)
			{
				echo "insert into GBL_TEMP_REPORT_ID (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$result[csf("lc_sc_id")].",4,".$user_id.")";oci_rollback($con);die;
			}
			$temp_table_id++;
		}*/
		
		$sub_buyer_arr[$result[csf("sub_id")]]['sub_id']=$result[csf("sub_id")];		
		$sub_buyer_arr[$result[csf("sub_id")]]['lc_sc_id']=$result[csf("lc_sc_id")];
		$sub_buyer_arr[$result[csf("sub_id")]]['is_lc']=$result[csf("is_lc")];		
		$sub_buyer_arr[$result[csf("sub_id")]]['submit_date']=$result[csf("submit_date")];
		$sub_buyer_arr[$result[csf("sub_id")]]['buyer_id']=$result[csf("buyer_id")];
		
		$sub_buyer_arr[$result[csf("sub_id")]]['inv_id'].=$result[csf("inv_id")].",";
		$sub_buyer_arr[$result[csf("sub_id")]]['invoice_no'].=$result[csf("invoice_no")].",";
		$sub_buyer_arr[$result[csf("sub_id")]]['invoice_quantity']+=$result[csf("invoice_quantity")];
		$sub_buyer_arr[$result[csf("sub_id")]]['net_invo_value']+=$result[csf("net_invo_value")];
	}
	
	$sql_unsubmit_lc= "SELECT b.id as lc_sc_id, a.id, a.buyer_id, a.invoice_no, a.invoice_quantity, a.net_invo_value as invoice_value, b.export_lc_no as export_lc_no, b.lc_value as lc_sc_val, a.ex_factory_date, a.remarks, 1 as type 
	from com_export_invoice_ship_mst a, com_export_lc b, gbl_temp_report_id p 
	where a.lc_sc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.is_lc=1 and a.lc_sc_id=p.REF_VAL and p.REF_FROM=1 and p.USER_ID=$user_id $cbo_company $cbo_buyer_name $cbo_lien_bank
	and a.id not in(select invoice_id from com_export_doc_submission_invo)";
	
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
		/*if($all_invoice_lc_check[$row[csf("lc_sc_id")]]=="")
		{
			$all_invoice_lc_check[$row[csf("lc_sc_id")]]=$row[csf("lc_sc_id")];
			$refrID2=execute_query("insert into GBL_TEMP_REPORT_ID (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row[csf("lc_sc_id")].",3,".$user_id.")");
			if(!$refrID2)
			{
				echo "insert into GBL_TEMP_REPORT_ID (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row[csf("lc_sc_id")].",3,".$user_id.")";oci_rollback($con);die;
			}
			$temp_table_id++;
		}*/
	}
	
	
	$sql_unsubmit_sc =" SELECT b.id as lc_sc_id, a.id, a.buyer_id, a.invoice_no, a.invoice_quantity, a.net_invo_value as invoice_value, b.contract_no as export_lc_no, b.contract_value as lc_sc_val, a.remarks, a.ex_factory_date, 2 as type 
	from com_export_invoice_ship_mst a, com_sales_contract b, gbl_temp_report_id p 
	where a.lc_sc_id=b.id and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.is_lc=2 and a.lc_sc_id=p.REF_VAL and p.REF_FROM=2 and p.USER_ID=$user_id $cbo_company $cbo_buyer_name $cbo_lien_bank 
	and a.id not in(select invoice_id from com_export_doc_submission_invo) ";
	//echo $sql_unsubmit_sc."<br>";die;
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
		
		/*if($all_invoice_sc_check[$row[csf("lc_sc_id")]]=="")
		{
			$all_invoice_sc_check[$row[csf("lc_sc_id")]]=$row[csf("lc_sc_id")];
			$refrID2=execute_query("insert into GBL_TEMP_REPORT_ID (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row[csf("lc_sc_id")].",4,".$user_id.")");
			if(!$refrID2)
			{
				echo "insert into GBL_TEMP_REPORT_ID (ID, REF_VAL, REF_FROM, USER_ID) values (".$temp_table_id.",".$row[csf("lc_sc_id")].",4,".$user_id.")";oci_rollback($con);die;
			}
			$temp_table_id++;
		}*/
	}
	
	$lc_id_arr_unique=array_unique($all_invoice_lc);
	$sc_id_arr_unique=array_unique($all_invoice_sc);
	//var_dump($all_invoice_sc);die;
	if(empty($lc_id_arr_unique)) $lc_id_arr_unique[]=0;
	if(empty($sc_id_arr_unique)) $sc_id_arr_unique[]=0;
		
	
	$sql_no_inv_lc=sql_select("select b.id as lc_sc_id, b.buyer_name as buyer_id, b.export_lc_no as lc_sc_no, b.lc_value as lc_sc_val, 1 as type from  com_export_lc b where  b.status_active=1 and b.is_deleted=0 and b.id not in(select REF_VAL from GBL_TEMP_REPORT_ID where REF_FROM=3 and USER_ID=$user_id) $cbo_company $cbo_buyer_name $cbo_lien_bank
	union all
	select b.id as lc_sc_id, b.buyer_name as buyer_id, b.contract_no as lc_sc_no, b.contract_value as lc_sc_val, 2 as type  from  com_sales_contract b where  b.status_active=1 and b.is_deleted=0 and b.convertible_to_lc=2 and  b.id not in(select REF_VAL from GBL_TEMP_REPORT_ID where REF_FROM=4 and USER_ID=$user_id) $cbo_company $cbo_buyer_name $cbo_lien_bank
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
	$submission_value_array=return_library_array("select a.id, sum(b.net_invo_value) as invoice_value from com_export_doc_submission_mst a, com_export_doc_submission_invo b where a.id=b.doc_submission_mst_id and a.entry_form=40 and b.status_active=1 and b.is_deleted=0 group by a.id","id","invoice_value");

	$gblDel=execute_query("delete from GBL_TEMP_REPORT_ID where USER_ID=$user_id");
	if($gblDel)
	{
		oci_commit($con);disconnect($con);
	}
		
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
							<td width="110" align="right"><p>'.number_format($sub_purchase,2,".","").'</p></td>
							<td width="110" align="right"><p>'.number_format($purchase,2,".","").'</p></td>
							<td width="110" align="right"><p>'.number_format($row_result[('distribution')],2,".","").'</p></td>
							<td width="110" align="right"><p>'.number_format($row_result[('deducation')],2,".","").'</p></td>
							<td align="right" width="110" ><p>'.number_format($advice_amt,2,".","").'</p></td>
							<td align="right" width="110"><p>'.number_format($row_result[('freight_amnt_by_supllier')],2,".","").'</p></td>
							<td align="right" width="110"><p>'.number_format($row_result[('paid_amount')],2,".","").'</p></td>
							<td width="110" align="right"><p>'.number_format($pandin_charse=$row_result[('freight_amnt_by_supllier')]- $row_result[('paid_amount')],2,".","").'</p></td>
							<td align="right" width="110"><p>'. number_format($freight_in_usd=$row_result[('freight_amnt_by_supllier')]/$txt_exchange_rate,2,".","").'</p></td>
							<td align="center"><p>'.number_format($parcentage=(($freight_in_usd/$row_result[('invoice_value')])*100),2,".","").'%</p></td>
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
	//echo "test<pre>";print_r($sub_bank_arr);die;
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
							<td  width="260"><p>'.chop($row_result[("invoice_no")],",").'</p></td>
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
		$k++;$m++;
	}
	//var_dump($buyer_result);die;
	//echo "<pre>";print_r($buyer_result);die;
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
							<td  width="500"><p>'.chop($row_result[("invoice_no")],",").'</p></td>
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
	if($rptType==2)
	{
		ob_clean();
		$filename=$user_id."_".$name.".xls";
		echo "$total_data####$filename";
	}
	else
	{
		$filename=$user_id."_".$name.".xls";
		echo "$total_data####$filename";
	}
	exit();
}



?>
