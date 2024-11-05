<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if ($action=="load_drop_down_year")
{
	$sql=sql_select("select lc_year as lc_sc_year from com_export_lc where beneficiary_name='$data' and status_active=1 and is_deleted=0  union all select sc_year as lc_sc_year from com_sales_contract where beneficiary_name='$data' and status_active=1 and is_deleted=0");
	foreach($sql as $row)
	{
		$lc_sc_year[$row[csf("lc_sc_year")]]=$row[csf("lc_sc_year")];
	}
	echo create_drop_down( "hide_year", 100, $lc_sc_year,"", 1, "-- Select --", $selected, "",0 );
	exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_lein_bank=str_replace("'","",$cbo_lein_bank); 
	$hide_year=str_replace("'","",$hide_year);
	//echo $hide_year;die;
	$company_arr=return_library_array( "select company_name,id from  lib_company",'id','company_name');
	$bank_arr=return_library_array( "select bank_name,id from lib_bank",'id','bank_name');
	//$suplier_name_arr=return_library_array( "select id,supplier_name from  lib_supplier",'id','supplier_name');
	 //echo $cbo_company_name.'____'.$cbo_buyer_name.'____'.$cbo_lein_bank.'____'.$txt_file_no; die;lc_year sc_year
	$sql_cond="";
	if($cbo_lein_bank>0) $sql_cond=" and c.issuing_bank_id=$cbo_lein_bank";
	if(trim($txt_file_no)!="") $sql_cond.="  and a.internal_file_no=$txt_file_no";
	
	
	$lc_sc_sql="select id as lc_sc_id, lien_bank, convertible_to_lc, contract_value as lc_sc_value, converted_from, 1 as type from com_sales_contract where beneficiary_name=$cbo_company_name and lien_bank=$cbo_lein_bank and sc_year='$hide_year' 
	union all
	select id as lc_sc_id, lien_bank, replacement_lc as convertible_to_lc, lc_value as lc_sc_value, null as converted_from, 2 as type from com_export_lc where beneficiary_name=$cbo_company_name and lien_bank=$cbo_lein_bank and lc_year='$hide_year'";
	
	/*$lc_sc_sql="select id as lc_sc_id, lien_bank, convertible_to_lc, contract_value as lc_sc_value, converted_from, 1 as type from com_sales_contract where beneficiary_name=$cbo_company_name and lien_bank=$cbo_lein_bank and sc_year<='$hide_year' 
	union all
	select id as lc_sc_id, lien_bank, replacement_lc as convertible_to_lc, lc_value as lc_sc_value, null as converted_from, 2 as type from com_export_lc where beneficiary_name=$cbo_company_name and lien_bank=$cbo_lein_bank and lc_year<='$hide_year'";*/
	
	//echo $lc_sc_sql;die;
	
	
	$lc_sc_sql_result=sql_select($lc_sc_sql);
	$lc_id=$sc_id="";
	$file_value=$sc_value_1_3=$lc_value_1=$sc_value_2=$lc_value_2=0;
	foreach($lc_sc_sql_result as $row)
	{
		if($row[csf("type")]==1)
		{
			$sc_id.=$row[csf("lc_sc_id")].",";
			if($row[csf("convertible_to_lc")]!=2)
			{
				$sc_value_1_3+=$row[csf("lc_sc_value")];
			}
			else
			{
				if($row[csf("converted_from")]>0)
				{
					$lc_value_1+=$row[csf("lc_sc_value")];
				}
				else
				{
					$sc_value_2+=$row[csf("lc_sc_value")];
				}
				
			}
			//``
		}
		else
		{
			$lc_id.=$row[csf("lc_sc_id")].",";
			if($row[csf("convertible_to_lc")]==2)
			{
				$lc_value_2+=$row[csf("lc_sc_value")];
			}
			else
			{
				$lc_value_1+=$row[csf("lc_sc_value")];
			}
		}
	}
	//$file_value=(($sc_value_1_3-$lc_value_1)+$sc_value_2+$lc_value_2+$lc_value_1);
	$file_value=($sc_value_1_3+$sc_value_2+$lc_value_2);
	$sc_id=chop($sc_id,",");
	$lc_id=chop($lc_id,",");
	if($lc_id=="" && $sc_id=="") die;
	if($sc_id=="") $sc_id=0;
	if($lc_id=="") $lc_id=0;
	$sc_id_arr=array_unique(explode(",",$sc_id));
	if($db_type==0)
	{
		$rlz_sc_cond=" and a.lc_sc_id in(".implode(",",$sc_id_arr).")";
	}
	else
	{
		$sc_id_arr=array_chunk($sc_id_arr,999);
		$rlz_sc_cond=" and (";
		foreach($sc_id_arr as $id_arr)
		{
			$rlz_sc_cond.="a.lc_sc_id in(".implode(",",$id_arr).") or";
		}
		$rlz_sc_cond=chop($rlz_sc_cond,"or");
		$rlz_sc_cond.=")";
		
		//echo $rlz_sc_cond;die;
	}
	
	$lc_id_arr=array_unique(explode(",",$lc_id));
	if($db_type==0)
	{
		$rlz_lc_cond=" and a.lc_sc_id in(".implode(",",$lc_id_arr).")";
	}
	else
	{
		$lc_id_arr=array_chunk($lc_id_arr,999);
		$rlz_lc_cond=" and (";
		foreach($lc_id_arr as $id_arr)
		{
			$rlz_lc_cond.="a.lc_sc_id in(".implode(",",$id_arr).") or";
		}
		$rlz_lc_cond=chop($rlz_lc_cond,"or");
		$rlz_lc_cond.=")";
	}
	
	
	
	$bill_sql="select b.id as bill_id, b.possible_reali_date, a.net_invo_value as bill_value, 1 as type from com_export_doc_submission_invo a, com_export_doc_submission_mst b where a.doc_submission_mst_id=b.id and a.is_lc=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $rlz_sc_cond
	union all
	select b.id as bill_id, b.possible_reali_date, a.net_invo_value as bill_value, 1 as type from com_export_doc_submission_invo a, com_export_doc_submission_mst b where a.doc_submission_mst_id=b.id and a.is_lc=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 $rlz_lc_cond";
	//$bill_data=array();
	$bill_sql_result=sql_select($bill_sql);
	foreach($bill_sql_result as $row)
	{
		if($row[csf("possible_reali_date")]!="" && $row[csf("possible_reali_date")]!="0000-00-00")
		{
			$month_wise_bill_data[date('Y',strtotime($row[csf("possible_reali_date")]))][date('n',strtotime($row[csf("possible_reali_date")]))]["bill_value"]+=$row[csf("bill_value")];
			$month_wise_bill_data[date('Y',strtotime($row[csf("possible_reali_date")]))][date('n',strtotime($row[csf("possible_reali_date")]))]["bill_id"].=$row[csf("bill_id")].",";
			$bill_id.=$row[csf("bill_id")].",";
			$possiable_value+=$row[csf("bill_value")];
		}
	}
	
	//echo $bill_sql;die;
	
	
	$bill_id=chop($bill_id,",");
	if($bill_id!="")
	{
		$bill_id_arr=array_unique(explode(",",$bill_id));
		if($db_type==0)
		{
			$rlz_cond=" and b.invoice_bill_id in(".implode(",",$bill_id_arr).")";
		}
		else
		{
			$bill_id_arr=array_chunk($bill_id_arr,999);
			$rlz_cond=" and (";
			foreach($bill_id_arr as $id_arr)
			{
				$rlz_cond.="b.invoice_bill_id in(".implode(",",$id_arr).") or";
			}
			$rlz_cond=chop($rlz_cond,"or");
			$rlz_cond.=")";
		}
	}
	
	
	
	
	$proceed_rlz_sql="select b.invoice_bill_id, c.document_currency as document_currency from  com_export_proceed_realization b, com_export_proceed_rlzn_dtls c where b.id=c.mst_id and b.is_invoice_bill=1 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 $rlz_cond";
	//echo $proceed_rlz_sql;die;
	$realize_data_arr=array();
	$proceed_rlz_sql_result=sql_select($proceed_rlz_sql);
	$rlz_value=0;
	foreach($proceed_rlz_sql_result as $row)
	{
		$realize_data_arr[$row[csf("invoice_bill_id")]]+=$row[csf("document_currency")];
		$rlz_value+=$row[csf("document_currency")];
	}
	
	//var_dump($realize_data_arr);die;
	
	$payment_data_array=return_library_array("select invoice_id, sum(accepted_ammount) as paid_amt from  com_import_payment where status_active=1 and is_deleted=0 group by invoice_id","invoice_id","paid_amt");
	$lc_wise_btb_paid=return_library_array("select lc_id, sum(accepted_ammount) as paid_amt from  com_import_payment where status_active=1 and is_deleted=0 group by invoice_id","lc_id","paid_amt");
	
	$btb_id_sql="select b.id as btb_id, b.lc_date, b.tenor, b.payterm_id, b.lc_category, b.lc_value, 1 as type from com_btb_export_lc_attachment a,  com_btb_lc_master_details b where a.import_mst_id=b.id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.is_lc_sc=1 $rlz_sc_cond
	group by b.id, b.lc_date, b.tenor, b.payterm_id, b.lc_category, b.lc_value
	union all 
	select b.id as btb_id, b.lc_date, b.tenor, b.payterm_id, b.lc_category, b.lc_value, 2 as type from com_btb_export_lc_attachment a,  com_btb_lc_master_details b where a.import_mst_id=b.id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.is_lc_sc=0 $rlz_lc_cond
	group by b.id, b.lc_date, b.tenor, b.payterm_id, b.lc_category, b.lc_value
	";
	//echo $btb_id_sql;die;
	$btb_id_result=sql_select($btb_id_sql);
	$btb_id_lc=$btb_id_sc="";
	foreach($btb_id_result as $row)
	{
		$btb_ids.=$row[csf("btb_id")].",";
	}
	$btb_ids=chop($btb_ids,",");
	
	

	
	//echo $test_btb_id;die;
	
	if($btb_ids!="")
	{
		if($db_type==0)
		{
			$btb_sql="select a.id as btb_lc_id, a.lc_date, a.tenor, b.id as import_inv_id, a.payterm_id, a.lc_category, a.lc_value, b.maturity_date, b.edf_paid_date, sum(c.current_acceptance_value) as edf_loan_value, 1 as type 
			from 
					com_btb_lc_master_details a, com_import_invoice_mst b, com_import_invoice_dtls c
			where 
					a.id=c.btb_lc_id and c.import_invoice_id=b.id  and a.lc_category>0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.id in($btb_ids) group by a.id, a.lc_date, a.tenor, b.id, a.payterm_id, a.lc_category, a.lc_value, b.maturity_date, b.edf_paid_date";
		}
		else
		{
			$btb_ids_arr=array_chunk(array_unique(explode(",",$btb_ids)),999);
			$btb_sql="select a.id as btb_lc_id, a.lc_date, a.tenor, b.id as import_inv_id, a.payterm_id, a.lc_category, a.lc_value, b.maturity_date, b.edf_paid_date, sum(c.current_acceptance_value) as edf_loan_value, 1 as type 
			from 
					com_btb_lc_master_details a, com_import_invoice_mst b, com_import_invoice_dtls c
			where 
					a.id=c.btb_lc_id and c.import_invoice_id=b.id  and a.lc_category>0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1";
			$p=1;
			foreach($btb_ids_arr as $btb_id)
			{
				if($p==1) $btb_sql.=" and ( a.id in(".implode(",",$btb_id).")"; else $btb_sql.=" or a.id in(".implode(",",$btb_id).")";
				$p++;
			}
			$btb_sql.=") group by a.id, a.lc_date, a.tenor, b.id, a.payterm_id, a.lc_category, a.lc_value, b.maturity_date, b.edf_paid_date";
			
		}
		
		
		if($db_type==0)
		{
			$btb_dails_sql="select a.id as btb_lc_id, a.payterm_id, a.lc_category, a.lc_date, a.lc_value from com_btb_lc_master_details a where a.lc_category>0 and a.is_deleted=0 and a.status_active=1 and a.id in($btb_ids)";
		}
		else
		{
			$btb_ids_arr=array_chunk(array_unique(explode(",",$btb_ids)),999);
			$btb_dails_sql="select a.id as btb_lc_id, a.payterm_id, a.lc_category, a.lc_date, a.lc_value from com_btb_lc_master_details a where a.lc_category>0 and a.is_deleted=0 and a.status_active=1";
			$p=1;
			foreach($btb_ids_arr as $btb_id)
			{
				if($p==1) $btb_dails_sql.=" and ( a.id in(".implode(",",$btb_id).")"; else $btb_dails_sql.=" or a.id in(".implode(",",$btb_id).")";
				$p++;
			}
			$btb_dails_sql.=")";
			
		}
		
	}
	
	//echo $btb_sql;//die;
	 
	/*$btb_sql="select b.id as btb_lc_id, c.id as import_inv_id, d.pi_id, b.payterm_id, b.lc_category, c.maturity_date, c.edf_paid_date, d.current_acceptance_value as edf_loan_value, 1 as type 
			from 
					com_btb_lc_master_details a, com_import_invoice_mst b, com_import_invoice_dtls c
			where 
					a.id=c.btb_lc_id and c.import_invoice_id=b.id  and a.is_lc_sc=1 and b.lc_category>0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 $rlz_sc_cond
					
			union all 
			
			select b.id as btb_lc_id, c.id as import_inv_id, d.pi_id, b.payterm_id, b.lc_category, c.maturity_date, c.edf_paid_date, d.current_acceptance_value as edf_loan_value, 1 as type 
			from 
					com_btb_export_lc_attachment a,  com_btb_lc_master_details b, com_import_invoice_mst c, com_import_invoice_dtls d
			where 
					a.import_mst_id=b.id and b.id=d.btb_lc_id and d.import_invoice_id=c.id  and a.is_lc_sc=0 and b.lc_category>0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1 $rlz_lc_cond";*/
	
	//echo $btb_sql;die;
	
	
	$btb_sql_result=sql_select($btb_sql);
	$edf_libality_data=$btb_libality_data=array();
	$total_edf_data_arr=$total_btb_data_arr=array();
	$lc_total_edf_data_arr=array();
	$invoce_data_arr=array();
	foreach($btb_sql_result as $row)
	{
		if($row[csf("edf_paid_date")]!="" && $row[csf("edf_paid_date")]!="0000-00-00" && (abs($row[csf("lc_category")])==3 || abs($row[csf("lc_category")])==5 || abs($row[csf("lc_category")])==11))
		{
			$lc_wise_edf_paid[$row[csf("btb_lc_id")]]+=$row[csf("edf_loan_value")];
		}
		
		// maturity daty wise summery
		if($row[csf("maturity_date")]!="" && $row[csf("maturity_date")]!="0000-00-00")
		{
			if($row[csf("payterm_id")]==1 && ($row[csf("edf_paid_date")]=="" || $row[csf("edf_paid_date")]=="0000-00-00") && abs($row[csf("lc_category")])==3)
			{
				$edf_local_loan+=$row[csf("edf_loan_value")];
				$total_edf_data_arr[date('Y',strtotime($row[csf("maturity_date")]))][date('n',strtotime($row[csf("maturity_date")]))]["local"]+=$row[csf("edf_loan_value")];
				$total_edf_data_arr[date('Y',strtotime($row[csf("maturity_date")]))][date('n',strtotime($row[csf("maturity_date")]))]["local_btb_lc_id"].=$row[csf("btb_lc_id")].",";
				$total_edf_loan+=$row[csf("edf_loan_value")];
			}
			if($row[csf("payterm_id")]==1 && ($row[csf("edf_paid_date")]=="" || $row[csf("edf_paid_date")]=="0000-00-00") && (abs($row[csf("lc_category")])==5 || abs($row[csf("lc_category")])==11))
			{
				$edf_foreign_loan+=$row[csf("edf_loan_value")];
				$total_edf_data_arr[date('Y',strtotime($row[csf("maturity_date")]))][date('n',strtotime($row[csf("maturity_date")]))]["foreign"]+=$row[csf("edf_loan_value")];
				$total_edf_data_arr[date('Y',strtotime($row[csf("maturity_date")]))][date('n',strtotime($row[csf("maturity_date")]))]["foreign_btb_lc_id"].=$row[csf("btb_lc_id")].",";
				$total_edf_loan+=$row[csf("edf_loan_value")];
			}
			
			//if($inv_check[$row[csf("import_inv_id")]]=="")
			
			if(abs($row[csf("lc_category")])==4)
			{
				$btb_local_loan+=$row[csf("edf_loan_value")]-$payment_data_array[$row[csf("import_inv_id")]];
				$total_btb_data_arr[date('Y',strtotime($row[csf("maturity_date")]))][date('n',strtotime($row[csf("maturity_date")]))]["local"]+=$row[csf("edf_loan_value")]-$payment_data_array[$row[csf("import_inv_id")]];
				$total_btb_data_arr[date('Y',strtotime($row[csf("maturity_date")]))][date('n',strtotime($row[csf("maturity_date")]))]["local_btb_lc_id"].=$row[csf("btb_lc_id")].",";
				$total_btb_loan+=$row[csf("edf_loan_value")]-$payment_data_array[$row[csf("import_inv_id")]];
			}
			
			if(abs($row[csf("lc_category")])!=3 && abs($row[csf("lc_category")])!=4 && abs($row[csf("lc_category")])!=5 && abs($row[csf("lc_category")])!=11)
			{
				$btb_foreign_loan+=$row[csf("edf_loan_value")]-$payment_data_array[$row[csf("import_inv_id")]];
				$total_btb_data_arr[date('Y',strtotime($row[csf("maturity_date")]))][date('n',strtotime($row[csf("maturity_date")]))]["foreign"]+=$row[csf("edf_loan_value")]-$payment_data_array[$row[csf("import_inv_id")]];
				$total_btb_data_arr[date('Y',strtotime($row[csf("maturity_date")]))][date('n',strtotime($row[csf("maturity_date")]))]["foreign_btb_lc_id"].=$row[csf("btb_lc_id")].",";
				$total_btb_loan+=$row[csf("edf_loan_value")]-$payment_data_array[$row[csf("import_inv_id")]];
			}
		}
		
		
		$invoce_data_arr[$row[csf("btb_lc_id")]][$row[csf("import_inv_id")]]["lc_date"]=$row[csf("lc_date")];
		$invoce_data_arr[$row[csf("btb_lc_id")]][$row[csf("import_inv_id")]]["maturity_date"]=$row[csf("maturity_date")];
		$invoce_data_arr[$row[csf("btb_lc_id")]][$row[csf("import_inv_id")]]["edf_paid_date"]=$row[csf("edf_paid_date")];
		$invoce_data_arr[$row[csf("btb_lc_id")]][$row[csf("import_inv_id")]]["edf_loan_value"]=$row[csf("edf_loan_value")];
		
		
	}
	
	
	//var_dump($lc_wise_btb_paid[1129]);die;
	//LC OPEN DATE WISY SUMMERY Data
	
	foreach($btb_id_result as $row)
	{
		if($btb_check[$row[csf("btb_id")]]=="")
		{
			$btb_check[$row[csf("btb_id")]]=$row[csf("btb_id")];
			$lc_date=add_date($row[csf("lc_date")],$row[csf("tenor")]);
			if($row[csf("payterm_id")]==1 && abs($row[csf("lc_category")])==3)
			{
				$lc_total_edf_data_arr[date('Y',strtotime($lc_date))][date('n',strtotime($lc_date))]["local"]+=$row[csf("lc_value")]-$lc_wise_edf_paid[$row[csf("btb_id")]];
				$lc_total_edf_data_arr[date('Y',strtotime($lc_date))][date('n',strtotime($lc_date))]["local_btb_lc_id"].=$row[csf("btb_id")].",";
			}
			
			if($row[csf("payterm_id")]==1  && (abs($row[csf("lc_category")])==5 || abs($row[csf("lc_category")])==11))
			{
				$lc_total_edf_data_arr[date('Y',strtotime($lc_date))][date('n',strtotime($lc_date))]["foreign"]+=$row[csf("lc_value")]-$lc_wise_edf_paid[$row[csf("btb_id")]];
				$lc_total_edf_data_arr[date('Y',strtotime($lc_date))][date('n',strtotime($lc_date))]["foreign_btb_lc_id"].=$row[csf("btb_id")].",";
				
			}
			
			if(abs($row[csf("lc_category")])==4)
			{
				//$lc_total_btb_data_arr[date('Y',strtotime($lc_date))][date('n',strtotime($lc_date))]["local"]+=$row[csf("lc_value")]-$lc_wise_btb_paid[$row[csf("btb_id")]];
				//$cu_paid=0;
				//$cu_paid=$row[csf("lc_value")]-$lc_wise_btb_paid[$row[csf("btb_id")]];
				/*if($cu_paid>0)
				{
					$lc_total_btb_data_arr[date('Y',strtotime($lc_date))][date('n',strtotime($lc_date))]["local"]+=$row[csf("lc_value")]-$lc_wise_btb_paid[$row[csf("btb_id")]];
					$lc_total_btb_data_arr[date('Y',strtotime($lc_date))][date('n',strtotime($lc_date))]["local_btb_lc_id"].=$row[csf("btb_id")].",";
				}*/
				
				foreach($invoce_data_arr[$row[csf("btb_id")]] as $inv_id=>$value)
				{
					$lc_total_btb_data_arr[date('Y',strtotime($lc_date))][date('n',strtotime($lc_date))]["local_paid"]+=$payment_data_array[$inv_id];
					$lc_local_paid+=$payment_data_array[$inv_id];
				}
				$lc_total_btb_data_arr[date('Y',strtotime($lc_date))][date('n',strtotime($lc_date))]["local"]+=$row[csf("lc_value")]-$lc_local_paid;
				
				$lc_total_btb_data_arr[date('Y',strtotime($lc_date))][date('n',strtotime($lc_date))]["local_btb_lc_id"].=$row[csf("btb_id")].",";
				$lc_local_paid=0;
				
				
			}
			
			if(abs($row[csf("lc_category")])!=3 && abs($row[csf("lc_category")])!=4 && abs($row[csf("lc_category")])!=5 && abs($row[csf("lc_category")])!=11)
			{
				//$lc_total_btb_data_arr[date('Y',strtotime($lc_date))][date('n',strtotime($lc_date))]["foreign"]+=$row[csf("lc_value")]-$lc_wise_btb_paid[$row[csf("btb_id")]];
				//$cu_paid=0;
				//$cu_paid=$row[csf("lc_value")]-$lc_wise_btb_paid[$row[csf("btb_id")]];
				/*if($cu_paid>0)
				{
					$lc_total_btb_data_arr[date('Y',strtotime($lc_date))][date('n',strtotime($lc_date))]["foreign"]+=$row[csf("lc_value")]-$lc_wise_btb_paid[$row[csf("btb_id")]];
					$lc_total_btb_data_arr[date('Y',strtotime($lc_date))][date('n',strtotime($lc_date))]["foreign_btb_lc_id"].=$row[csf("btb_id")].",";
				}*/
				
				foreach($invoce_data_arr[$row[csf("btb_id")]] as $inv_id=>$value)
				{
					$lc_total_btb_data_arr[date('Y',strtotime($lc_date))][date('n',strtotime($lc_date))]["foreign_paid"]+=$payment_data_array[$inv_id];
					$lc_foreign_value+=$payment_data_array[$inv_id];
				}
				
				$lc_total_btb_data_arr[date('Y',strtotime($lc_date))][date('n',strtotime($lc_date))]["foreign"]+=$row[csf("lc_value")]-$lc_foreign_value;
				
				$lc_total_btb_data_arr[date('Y',strtotime($lc_date))][date('n',strtotime($lc_date))]["foreign_btb_lc_id"].=$row[csf("btb_id")].",";
				$lc_foreign_value=0;
				
			}
		}
	}
	
	// details data create hare
	
	$btb_details_result=sql_select($btb_dails_sql);
	$btb_details_data_arr=array();
	foreach($btb_details_result as $row)
	{
		$btb_details_data_arr[date('Y',strtotime($row[csf("lc_date")]))][date('n',strtotime($row[csf("lc_date")]))]["lc_open"]+=$row[csf("lc_value")];
		$btb_details_data_arr[date('Y',strtotime($row[csf("lc_date")]))][date('n',strtotime($row[csf("lc_date")]))]["btb_lc_id"].=$row[csf("btb_lc_id")].",";
		if($row[csf("payterm_id")]==1 && abs($row[csf("lc_category")])==3)
		{
			$btb_details_data_arr[date('Y',strtotime($row[csf("lc_date")]))][date('n',strtotime($row[csf("lc_date")]))]["edf_local_open"]+=$row[csf("lc_value")];
			$btb_details_data_arr[date('Y',strtotime($row[csf("lc_date")]))][date('n',strtotime($row[csf("lc_date")]))]["edf_local_open_lc_id"].=$row[csf("btb_lc_id")].",";
			
			foreach($invoce_data_arr[$row[csf("btb_lc_id")]] as $inv_id=>$value)
			{
				if($value["edf_paid_date"]!="" && $value["edf_paid_date"]!="0000-00-00")
				{
					$btb_details_data_arr[date('Y',strtotime($row[csf("lc_date")]))][date('n',strtotime($row[csf("lc_date")]))]["edf_local_paid"]+=$value["edf_loan_value"];
					$btb_details_data_arr[date('Y',strtotime($row[csf("lc_date")]))][date('n',strtotime($row[csf("lc_date")]))]["edf_local_inv_id"].=$inv_id.",";
					$btb_details_data_arr[date('Y',strtotime($row[csf("lc_date")]))][date('n',strtotime($row[csf("lc_date")]))]["edf_local_lc_id"].=$row[csf("btb_lc_id")].",";
				}
			}
		}
		if($row[csf("payterm_id")]==1 && (abs($row[csf("lc_category")])==5 || abs($row[csf("lc_category")])==11))
		{
			$btb_details_data_arr[date('Y',strtotime($row[csf("lc_date")]))][date('n',strtotime($row[csf("lc_date")]))]["edf_foreign_open"]+=$row[csf("lc_value")];
			$btb_details_data_arr[date('Y',strtotime($row[csf("lc_date")]))][date('n',strtotime($row[csf("lc_date")]))]["edf_foreign_open_lc_id"].=$row[csf("btb_lc_id")].",";
			foreach($invoce_data_arr[$row[csf("btb_lc_id")]] as $inv_id=>$value)
			{
				if($value["edf_paid_date"]!="" && $value["edf_paid_date"]!="0000-00-00")
				{
					$btb_details_data_arr[date('Y',strtotime($row[csf("lc_date")]))][date('n',strtotime($row[csf("lc_date")]))]["edf_foreign_paid"]+=$value["edf_loan_value"];
					$btb_details_data_arr[date('Y',strtotime($row[csf("lc_date")]))][date('n',strtotime($row[csf("lc_date")]))]["edf_foreign_inv_id"].=$inv_id.",";
					$btb_details_data_arr[date('Y',strtotime($row[csf("lc_date")]))][date('n',strtotime($row[csf("lc_date")]))]["edf_foreign_lc_id"].=$row[csf("btb_lc_id")].",";
				}
			}
		}
		
		if(abs($row[csf("lc_category")])==4)
		{
			$btb_details_data_arr[date('Y',strtotime($row[csf("lc_date")]))][date('n',strtotime($row[csf("lc_date")]))]["btb_local_open"]+=$row[csf("lc_value")];
			$btb_details_data_arr[date('Y',strtotime($row[csf("lc_date")]))][date('n',strtotime($row[csf("lc_date")]))]["btb_local_open_lc_id"].=$row[csf("btb_lc_id")].",";
			
			foreach($invoce_data_arr[$row[csf("btb_lc_id")]] as $inv_id=>$value)
			{
				$btb_details_data_arr[date('Y',strtotime($row[csf("lc_date")]))][date('n',strtotime($row[csf("lc_date")]))]["btb_local_paid"]+=$payment_data_array[$inv_id];
				$btb_details_data_arr[date('Y',strtotime($row[csf("lc_date")]))][date('n',strtotime($row[csf("lc_date")]))]["btb_local_inv_id"].=$inv_id.",";
			}
		}
		
		if(abs($row[csf("lc_category")])!=3 && abs($row[csf("lc_category")])!=4 && abs($row[csf("lc_category")])!=5 && abs($row[csf("lc_category")])!=11)
		{
			$btb_details_data_arr[date('Y',strtotime($row[csf("lc_date")]))][date('n',strtotime($row[csf("lc_date")]))]["btb_foreign_open"]+=$row[csf("lc_value")];
			$btb_details_data_arr[date('Y',strtotime($row[csf("lc_date")]))][date('n',strtotime($row[csf("lc_date")]))]["btb_foreign_open_lc_id"].=$row[csf("btb_lc_id")].",";
			
			foreach($invoce_data_arr[$row[csf("btb_lc_id")]] as $inv_id=>$value)
			{
				$btb_details_data_arr[date('Y',strtotime($row[csf("lc_date")]))][date('n',strtotime($row[csf("lc_date")]))]["btb_foreign_paid"]+=$payment_data_array[$inv_id];
				$btb_details_data_arr[date('Y',strtotime($row[csf("lc_date")]))][date('n',strtotime($row[csf("lc_date")]))]["btb_foreign_inv_id"].=$inv_id.",";
			}
		}
		
	}
	
	//echo $edf_foreign_loan.jahid;die;
	//echo "<pre>";print_r($total_btb_data_arr);die;
	//echo "<pre>";print_r($total_edf_data_arr);die;
	//echo $btb_sql;die;
	
	//echo $sc_value_1_3."##".$lc_value_1."##".$sc_value_2."##".$lc_value_2."##".$file_value;die;
	
	//echo $lc_sc_sql;die; .$bank_arr
	
	
	ob_start();
?>
<div style="width:1610px;" id="scroll_body">
<fieldset style="width:100%">
	<p style="font-size:16px; font-weight:bold">Summary</p>
	<p style="font-size:16px; font-weight:bold"><? echo "Bank name: ". $bank_arr[$cbo_lein_bank];?></p>
    <table width="1600" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" id="" align="left">
        <thead>
            <tr>
                <th width="100">SC Value(LC/SC, Finance)</th>
                <th width="100">Rep. LC/SC</th>
                <th width="100">Balance</th>
                <th width="100">SC Value(Direct)</th>
                <th width="100">LC Value(Direct)</th>
                <th width="100">File Value</th>
                <th width="100">Possible Realized Value</th>
                <th width="100">Precced Realized</th>
                <th width="100">File Against Procced Balance</th>
                <th width="100">LC Against Procced Balance</th>
                <th width="100">EDF Liability Local</th>
                <th width="100">EDF Liability Foreign</th>
                <th width="100">Total EDF Liability</th>
                <th width="100">BTB Liability Local</th>
                <th width="100">BTB Liability Foreign</th>
                <th>Total BTB Liability</th>
            </tr>
        </thead>
        <tbody>
        	<tr bgcolor="#FFFFFF">
            	<td align="right"><? echo number_format($sc_value_1_3,2); ?></td>
                <td align="right"><? echo number_format($lc_value_1,2); ?></td>
                <td align="right"><? echo number_format(($sc_value_1_3-$lc_value_1),2); ?></td>
                <td align="right"><? echo number_format($sc_value_2,2); ?></td>
                <td align="right"><? echo number_format($lc_value_2,2); ?></td>
                <td align="right"><? echo number_format($file_value,2); ?></td>
                <td align="right"><? echo number_format($possiable_value,2); ?></td>
                <td align="right"><? echo number_format($rlz_value,2); ?></td>
                <td align="right"><? $proceed_ag_rlz=$file_value-$rlz_value; echo number_format($proceed_ag_rlz,2); ?></td>
                <td align="right"><? $lc_ag_rlz=(($lc_value_1+$sc_value_2+$lc_value_2)-$rlz_value); echo number_format($lc_ag_rlz,2); ?></td>
                <td align="right"><? echo number_format($edf_local_loan,2); ?></td>
                <td align="right"><? echo number_format($edf_foreign_loan,2); ?></td>
                <td align="right"><? echo number_format($total_edf_loan,2); ?></td>
                <td align="right"><? echo number_format($btb_local_loan,2); ?></td>
                <td align="right"><? echo number_format($btb_foreign_loan,2); ?></td>
                <td align="right"><? echo number_format($total_btb_loan,2); ?></td>
            </tr>
        </tbody>
    </table>
    <table width="1500" cellpadding="0" cellspacing="0" align="left">
    	<tr><td>&nbsp;</td></tr>
    </table>
    
    <table width="1500" cellpadding="0" cellspacing="0" align="left">
    	<tr>
        	<td width="350" valign="top">
            	<p style="font-size:16px; font-weight:bold">EDF Liability Month Wise</p>
                <table width="350" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" align="left">
                	
                    	<?
						$i=$k=1; 
						foreach($total_edf_data_arr as $year=>$month_data)
						{
							ksort($month_data);
							?>
                            <thead>
                                <tr>
                                    <th width="120">Year-<? echo $year; ?></th>
                                    <th width="120">Local</th>
                                    <th>Foreign</th>
                                </tr>
                            </thead>
                            <?
							foreach($month_data as $month_id=>$val)
							{
								$bgcolor=($k%2==0)?"#E9F3FF":"#FFFFFF";	 
								?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i?>','<? echo $bgcolor?>')" id="tr_<? echo $i; ?>" style="cursor:pointer">
                                	<td><? echo $months[abs($month_id)]; ?></td>
                                    <td align="right" title="<? echo $val["local_btb_lc_id"]; ?>"><a href='#report_detals'  onclick= "openmypage('<? echo chop($val["local_btb_lc_id"],","); ?>','<? echo $year; ?>','<? echo $month_id; ?>','1','EDF Info')"><? echo number_format($val["local"],2); ?></a></td>
                                    <td align="right" title="<? echo $val["foreign_btb_lc_id"]; ?>"><a href='#report_detals'  onclick= "openmypage('<? echo chop($val["foreign_btb_lc_id"],","); ?>','<? echo $year; ?>','<? echo $month_id; ?>','1','EDF Info')"><? echo number_format($val["foreign"],2); ?></a></td>
                                </tr>
                                <?
								$i++;$k++;
								$year_total_local+=$val["local"];
								$year_total_foreign+=$val["foreign"];
								$grand_local+=$val["local"];
								$grand_foreign+=$val["foreign"];
							}
							?>
                            <tr bgcolor="#CCCCCC">
                                <td>Year Total:</td>
                                <td align="right"><? echo number_format($year_total_local,2); ?></td>
                                <td align="right"><? echo number_format($year_total_foreign,2); ?></td>
                            </tr>
                            <?
							$year_total_local=$year_total_foreign=0;
						}
						?>
                        <tfoot>
                        	<tr>
                                <th align="right">Grand Total:</th>
                                <th align="right"><? echo number_format($grand_local,2); ?></th>
                                <th align="right"><? echo number_format($grand_foreign,2); ?></th>
                            </tr>
                        </tfoot>
                </table>
            </td>
            <td width="50"></td>
            <td  valign="top" width="350">
            	<p style="font-size:16px; font-weight:bold">BTB Liability Month Wise</p>
            	<table width="350" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" align="left">
                	
                    	<?
						$k=1; 
						foreach($total_btb_data_arr as $year=>$month_data)
						{
							?>
                            <thead>
                                <tr>
                                    <th width="120">Year-<? echo $year; ?></th>
                                    <th width="120">Local</th>
                                    <th>Foreign</th>
                                </tr>
                            </thead>
                            <?
							ksort($month_data);
							foreach($month_data as $month_id=>$val)
							{
								$bgcolor=($k%2==0)?"#E9F3FF":"#FFFFFF";
								?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i?>','<? echo $bgcolor?>')" id="tr_<? echo $i; ?>" style="cursor:pointer">
                                	<td><? echo $months[abs($month_id)]; ?></td>
                                    <td align="right" title="<? echo $val["local_btb_lc_id"]; ?>"><a href='#report_detals'  onclick= "openmypage('<? echo chop($val["local_btb_lc_id"],","); ?>','<? echo $year; ?>','<? echo $month_id; ?>','2','BTB Info')"><? echo number_format($val["local"],2); ?></a></td>
                                    <td align="right" title="<? echo $val["foreign_btb_lc_id"]; ?>"><a href='#report_detals'  onclick= "openmypage('<? echo chop($val["foreign_btb_lc_id"],","); ?>','<? echo $year; ?>','<? echo $month_id; ?>','2','BTB Info')"><? echo number_format($val["foreign"],2); ?></a></td>
                                </tr>
                                <?
								$i++;$k++;
								$year_total_btb_local+=$val["local"];
								$year_total_btb_foreign+=$val["foreign"];
								$grand_btb_local+=$val["local"];
								$grand_btb_foreign+=$val["foreign"];
							}
							?>
                            <tr bgcolor="#CCCCCC">
                                <td align="right">Year Total:</td>
                                <td align="right"><? echo number_format($year_total_btb_local,2); ?></td>
                                <td align="right"><? echo number_format($year_total_btb_foreign,2); ?></td>
                            </tr>
                            <?
							$year_total_btb_local=$year_total_btb_foreign=0;
						}
						?>
                        
                        <tfoot>
                        	<tr>
                                <th align="right">Grand Total:</th>
                                <th align="right"><? echo number_format($grand_btb_local,2); ?></th>
                                <th align="right"><? echo number_format($grand_btb_foreign,2); ?></th>
                            </tr>
                        </tfoot>
                    
                </table>
            </td>
            <td width="50"></td>
            <td valign="top">
            	<p style="font-size:16px; font-weight:bold">Possible Realized Value Month Wise</p>
                <table width="450" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" align="left">
                	
                    	<?
						$k=1; 
						foreach($month_wise_bill_data as $year=>$month_data)
						{
							ksort($month_data);
							$bill_id="";
							?>
                            <thead>
                                <tr>
                                    <th width="120">Year-<? echo $year; ?></th>
                                    <th width="110">Possible Realized Amount</th>
                                    <th width="110">Realized Amount</th>
                                    <th>Balance Value</th>
                                </tr>
                            </thead>
                            <!--<a href='#report_detals'  onclick= "openmypage('<?// echo $bill_id; ?>','<?// echo $year; ?>','<?// echo $month_id; ?>','1','Bill Info')"></a>-->
                            <?
							foreach($month_data as $month_id=>$val)
							{
								$bgcolor=($k%2==0)?"#E9F3FF":"#FFFFFF";
								$bill_id=chop($val["bill_id"],",");
								$realize_value=$balance=0;
								$bill_id_arr=array_unique(explode(",",$bill_id));
								foreach($bill_id_arr as $b_id)
								{
									$realize_value+=$realize_data_arr[$b_id];
								}
								$balance=$val["bill_value"]-$realize_value;
								 
								?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i?>','<? echo $bgcolor?>')" id="tr_<? echo $i; ?>" style="cursor:pointer">
                                	<td><? echo $months[abs($month_id)]; ?></td>
                                    <td align="right" title="<? echo $bill_id; ?>"><? echo number_format($val["bill_value"],2); ?></td>
                                    <td align="right"><? echo number_format($realize_value,2); ?></td>
                                    <td align="right"><? echo number_format($balance,2); ?></td>
                                </tr>
                                <?
								$i++;$k++;
								$year_total_bill_value+=$val["bill_value"];
								$year_total_realize_value+=$realize_value;
								$year_total_balance+=$balance;
								$grand_bill_value+=$val["bill_value"];
								$grand_realize_value+=$realize_value;
								$grand_balance+=$balance;
								
							}
							?>
                            <tr bgcolor="#CCCCCC">
                                <td>Year Total:</td>
                                <td align="right"><? echo number_format($year_total_bill_value,2); ?></td>
                                <td align="right"><? echo number_format($year_total_realize_value,2); ?></td>
                                <td align="right"><? echo number_format($year_total_balance,2); ?></td>
                            </tr>
                            <?
							$year_total_local=$year_total_foreign=0;
						}
						?>
                        <tfoot>
                        	<tr>
                                <th align="right">Grand Total:</th>
                                <th align="right"><? echo number_format($grand_bill_value,2); ?></th>
                                <th align="right"><? echo number_format($grand_realize_value,2); ?></th>
                                <th align="right"><? echo number_format($grand_balance,2); ?></th>
                            </tr>
                        </tfoot>
                </table>
            </td>
        </tr>
    </table>
    
     <table width="1500" cellpadding="0" cellspacing="0" align="left">
    	<tr><td>&nbsp;</td></tr>
    </table>
    <table width="1500" cellpadding="0" cellspacing="0" rules="all" align="left">
    <tr>
    	<td><p style="font-size:16px; font-weight:bold">TOTAL L/C Open Wise Breakdown</p></td>
    </tr>
    </table>
    <table width="1800" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" align="left">
		<?
       $k=1; 
        foreach($btb_details_data_arr as $year=>$month_data)
        {
            ksort($month_data);
            ?>
            <thead>
                <tr>
                    <th width="120" rowspan="2">Year-<? echo $year; ?></th>
                    <th width="120" rowspan="2">TOTAL L/C OPEN Value</th>
                    <th colspan="3">EDF FOREIGN L/C</th>
                    <th colspan="3">EDF LOCAL L/C</th>
                    <th width="100" rowspan="2">EDF TOTAL</th>
                    <th colspan="3">BTB FOREIGN L/C</th>
                    <th colspan="3">BTB LOCAL L/C</th>
                    <th width="100" rowspan="2">BTB TOTAL</th>
                    <th width="100" rowspan="2">(EDF+BTB)TOTAL</th>
                </tr>
                <tr>
                	<th width="100">Open Value</th>
                    <th width="100">Paid Value</th>
                    <th width="100">Paid Balance</th>
                    <th width="100">Open Value</th>
                    <th width="100">Paid Value</th>
                    <th width="100">Paid Balance</th>
                    <th width="100">Open Value</th>
                    <th width="100">Paid Value</th>
                    <th width="100">Paid Balance</th>
                    <th width="100">Open Value</th>
                    <th width="100">Paid Value</th>
                    <th width="100">Paid Balance</th>
                </tr>
            </thead>
            <?
            foreach($month_data as $month_id=>$val)
            {
				$edf_balance=$foreign_balance=$local_balance=$btb_balance=$btb_foreign_balance=$btb_local_balance=$edf_btb_balance=0;
                $bgcolor=($k%2==0)?"#E9F3FF":"#FFFFFF";	 
                ?>
                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i?>','<? echo $bgcolor?>')"  id="tr_<? echo $i; ?>" style="cursor:pointer">
                    <td><? echo $months[abs($month_id)]; ?></td>
                    <td align="right" title="<? echo chop($val["btb_lc_id"],","); ?>"><a href='#report_detals'  onclick= "openmypage_btb('<? echo chop($val["btb_lc_id"],","); ?>','<? echo $year; ?>','<? echo $month_id; ?>','1','L/C OPEN Info')"><? echo number_format($val["lc_open"],2); ?></a></td>
                    <td align="right" title="<? echo chop($val["edf_foreign_open_lc_id"],","); ?>"><a href='#report_detals'  onclick= "openmypage_btb('<? echo chop($val["edf_foreign_open_lc_id"],","); ?>','<? echo $year; ?>','<? echo $month_id; ?>','1','EDF Foreign Info')"><? echo number_format($val["edf_foreign_open"],2); ?></a></td>
                    <td align="right" title="<? echo $val["edf_foreign_lc_id"]; ?>"><a href='#report_detals'  onclick= "openmypage('<? echo chop($val["edf_foreign_lc_id"],","); ?>','<? echo $year; ?>','<? echo $month_id; ?>','3','EDF Info')"><? echo number_format($val["edf_foreign_paid"],2); ?></a></td>
                    <td align="right"><a href='#report_detals'  onclick= "openmypage_btb_paid('<? echo chop($val["edf_foreign_open_lc_id"],","); ?>','<? echo $year; ?>','<? echo $month_id; ?>','3','BTB Paid Info')"><? $foreign_balance=$val["edf_foreign_open"]-$val["edf_foreign_paid"]; echo number_format($foreign_balance,2); ?></a></td>
                    <td align="right" title="<? echo chop($val["edf_local_open_lc_id"],","); ?>"><a href='#report_detals'  onclick= "openmypage_btb('<? echo chop($val["edf_local_open_lc_id"],","); ?>','<? echo $year; ?>','<? echo $month_id; ?>','1','EDF Local Info')"><? echo number_format($val["edf_local_open"],2); ?></a></td>
                    <td align="right" title="<? echo $val["edf_local_lc_id"]; ?>"><a href='#report_detals'  onclick= "openmypage('<? echo chop($val["edf_local_lc_id"],","); ?>','<? echo $year; ?>','<? echo $month_id; ?>','3','EDF Info')"><? echo number_format($val["edf_local_paid"],2); ?></a></td>
                    <td align="right"><a href='#report_detals'  onclick= "openmypage_btb_paid('<? echo chop($val["edf_local_open_lc_id"],","); ?>','<? echo $year; ?>','<? echo $month_id; ?>','3','BTB Paid Info')"><? $local_balance=$val["edf_local_open"]-$val["edf_local_paid"]; echo number_format($local_balance,2); ?></a></td>
                    <td align="right"><? $edf_balance=$foreign_balance+$local_balance; echo number_format($edf_balance,2); ?></td>
                    <td align="right" title="<? echo chop($val["btb_foreign_open_lc_id"],","); ?>"><a href='#report_detals'  onclick= "openmypage_btb('<? echo chop($val["btb_foreign_open_lc_id"],","); ?>','<? echo $year; ?>','<? echo $month_id; ?>','1','BTB Foreign Info')"><? echo number_format($val["btb_foreign_open"],2); ?></a></td>
                    <td align="right" title="<? echo $val["btb_foreign_inv_id"]; ?>"><a href='#report_detals'  onclick= "openmypage_btb_paid('<? echo chop($val["btb_foreign_inv_id"],","); ?>','<? echo $year; ?>','<? echo $month_id; ?>','2','BTB Paid Info')"><? echo number_format($val["btb_foreign_paid"],2); ?></a></td>
                    <td align="right"><a href='#report_detals'  onclick= "openmypage_btb_paid('<? echo chop($val["btb_foreign_open_lc_id"],","); ?>','<? echo $year; ?>','<? echo $month_id; ?>','3','BTB Paid Info')"><? $btb_foreign_balance=$val["btb_foreign_open"]-$val["btb_foreign_paid"]; echo number_format($btb_foreign_balance,2); ?></a></td>
                    <td align="right" title="<? echo chop($val["btb_local_open_lc_id"],","); ?>"><a href='#report_detals'  onclick= "openmypage_btb('<? echo chop($val["btb_local_open_lc_id"],","); ?>','<? echo $year; ?>','<? echo $month_id; ?>','1','BTB Local Info')"><? echo number_format($val["btb_local_open"],2); ?></a></td>
                    <td align="right" title="<? echo $val["btb_local_inv_id"]; ?>"><a href='#report_detals'  onclick= "openmypage_btb_paid('<? echo chop($val["btb_local_inv_id"],","); ?>','<? echo $year; ?>','<? echo $month_id; ?>','2','BTB Paid Info')"><? echo number_format($val["btb_local_paid"],2); ?></a></td>
                    <td align="right"><a href='#report_detals'  onclick= "openmypage_btb_paid('<? echo chop($val["btb_local_open_lc_id"],","); ?>','<? echo $year; ?>','<? echo $month_id; ?>','3','BTB Paid Info')"><? $btb_local_balance=$val["btb_local_open"]-$val["btb_local_paid"]; echo number_format($btb_local_balance,2); ?></a></td>
                    <td align="right"><? $btb_balance=$btb_foreign_balance+$btb_local_balance; echo number_format($btb_balance,2); ?></td>
                    <td align="right"><? $edf_btb_balance=$edf_balance+$btb_balance; echo number_format($edf_btb_balance,2); ?></td>
                </tr>
                <?
                $i++;$k++;
                $year_total_lc_open+=$val["lc_open"];
                $year_total_edf_foreign_open+=$val["edf_foreign_open"];
                $year_total_edf_foreign_paid+=$val["edf_foreign_paid"];
				$year_total_foreign_balance+=$foreign_balance;
				$year_total_edf_local_open+=$val["edf_local_open"];
                $year_total_edf_local_paid+=$val["edf_local_paid"];
				$year_total_local_balance+=$local_balance;
				$year_total_btb_foreign_open+=$val["btb_foreign_open"];
                $year_total_btb_foreign_paid+=$val["btb_foreign_paid"];
				$year_total_btb_foreign_balance+=$btb_foreign_balance;
				$year_total_btb_local_open+=$val["btb_local_open"];
                $year_total_btb_local_paid+=$val["btb_local_paid"];
				$year_total_btb_local_balance+=$btb_local_balance;
				
				$year_edf_balance+=$edf_balance;
				$year_btb_balance+=$btb_balance;
				$year_edf_btb_balance+=$edf_btb_balance;
				
				
				$grand_lc_open+=$val["lc_open"];
                $grand_edf_foreign_open+=$val["edf_foreign_open"];
                $grand_edf_foreign_paid+=$val["edf_foreign_paid"];
				$grand_foreign_balance+=$foreign_balance;
				$grand_edf_local_open+=$val["edf_local_open"];
                $grand_edf_local_paid+=$val["edf_local_paid"];
				$grand_local_balance+=$local_balance;
				$grand_btb_foreign_open+=$val["btb_foreign_open"];
                $grand_btb_foreign_paid+=$val["btb_foreign_paid"];
				$grand_btb_foreign_balance+=$btb_foreign_balance;
				$grand_btb_local_open+=$val["btb_local_open"];
                $grand_btb_local_paid+=$val["btb_local_paid"];
				$grand_btb_local_balance+=$btb_local_balance;
				
				$grand_edf_balance+=$edf_balance;
				$grand_btb_balance+=$btb_balance;
				$grand_edf_btb_balance+=$edf_btb_balance;
            }
            ?>
            <tr bgcolor="#CCCCCC">
                <td align="right">Year Total:</td>
                <td align="right"><? echo number_format($year_total_lc_open,2); ?></td>
                <td align="right"><? echo number_format($year_total_edf_foreign_open,2); ?></td>
                <td align="right"><? echo number_format($year_total_edf_foreign_paid,2); ?></td>
                <td align="right"><? echo number_format($year_total_foreign_balance,2); ?></td>
                <td align="right"><? echo number_format($year_total_edf_local_open,2); ?></td>
                <td align="right"><? echo number_format($year_total_edf_local_paid,2); ?></td>
                <td align="right"><? echo number_format($year_total_local_balance,2); ?></td>
                
                <td align="right"><? echo number_format($year_edf_balance,2); ?></td>
                
                <td align="right"><? echo number_format($year_total_btb_foreign_open,2); ?></td>
                <td align="right"><? echo number_format($year_total_btb_foreign_paid,2); ?></td>
                <td align="right"><? echo number_format($year_total_btb_foreign_balance,2); ?></td>
                <td align="right"><? echo number_format($year_total_btb_local_open,2); ?></td>
                <td align="right"><? echo number_format($year_total_btb_local_paid,2); ?></td>
                <td align="right"><? echo number_format($year_total_btb_local_balance,2); ?></td>
                
                <td align="right"><? echo number_format($year_btb_balance,2); ?></td>
                <td align="right"><? echo number_format($year_edf_btb_balance,2); ?></td>
            </tr>
            <?
            $year_total_lc_open=$year_total_edf_foreign_open=$year_total_edf_foreign_paid=$year_total_foreign_balance=$year_total_edf_local_open=$year_total_edf_local_paid=$year_total_local_balance=$year_total_btb_foreign_open=$year_total_btb_foreign_paid=$year_total_btb_foreign_balance=$year_total_btb_local_open=$year_total_btb_local_paid=$year_total_btb_local_balance=0;
			$year_edf_balance=$year_btb_balance=$year_edf_btb_balance=0;
        }
        ?>
        <tfoot>
            <tr>
                <th align="right">Grand Total:</th>
                <th align="right"><? echo number_format($grand_lc_open,2); ?></th>
                <th align="right"><? echo number_format($grand_edf_foreign_open,2); ?></th>
                <th align="right"><? echo number_format($grand_edf_foreign_paid,2); ?></th>
                <th align="right"><? echo number_format($grand_foreign_balance,2); ?></th>
                <th align="right"><? echo number_format($grand_edf_local_open,2); ?></th>
                <th align="right"><? echo number_format($grand_edf_local_paid,2); ?></th>
                <th align="right"><? echo number_format($grand_local_balance,2); ?></th>
                
                <th align="right"><? echo number_format($grand_edf_balance,2); ?></th>
                
                <th align="right"><? echo number_format($grand_btb_foreign_open,2); ?></th>
                <th align="right"><? echo number_format($grand_btb_foreign_paid,2); ?></th>
                <th align="right"><? echo number_format($grand_btb_foreign_balance,2); ?></th>
                <th align="right"><? echo number_format($grand_btb_local_open,2); ?></th>
                <th align="right"><? echo number_format($grand_btb_local_paid,2); ?></th>
                <th align="right"><? echo number_format($grand_btb_local_balance,2); ?></th>
                
                <th align="right"><? echo number_format($grand_btb_balance,2); ?></th>
                <th align="right"><? echo number_format($grand_edf_btb_balance,2); ?></th>
                
            </tr>
        </tfoot>
    </table>
    
    
    <table width="800" cellpadding="0" cellspacing="0" align="left" style="margin-top:20px;">
    	<tr>
        	<td colspan="3"><p style="font-size:16px; font-weight:bold; margin-bottom:10px;">LC OPEN DATE WISY SUMMERY</p></td>
        </tr>
    	<tr>
        	<td width="350" valign="top">
            	<p style="font-size:16px; font-weight:bold">EDF Liability Month Wise</p>
                <table width="350" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" align="left">
                	
                    	<?
						$i=$k=1; 
						foreach($lc_total_edf_data_arr as $year=>$month_data)
						{
							ksort($month_data);
							?>
                            <thead>
                                <tr>
                                    <th width="120">Year-<? echo $year; ?></th>
                                    <th width="120">Local</th>
                                    <th>Foreign</th>
                                </tr>
                            </thead>
                            <?
							foreach($month_data as $month_id=>$val)
							{
								$bgcolor=($k%2==0)?"#E9F3FF":"#FFFFFF";	 
								?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i?>','<? echo $bgcolor?>')" id="tr_<? echo $i; ?>" style="cursor:pointer">
                                	<td><? echo $months[abs($month_id)]; ?></td>
                                    <td align="right" title="<? echo $val["local_btb_lc_id"]; ?>"><a href='#report_detals'  onclick= "openmypage('<? echo chop($val["local_btb_lc_id"],","); ?>','<? echo $year; ?>','<? echo $month_id; ?>','4','EDF Info')"><? echo number_format($val["local"],2); ?></a></td>
                                    <td align="right" title="<? echo $val["foreign_btb_lc_id"]; ?>"><a href='#report_detals'  onclick= "openmypage('<? echo chop($val["foreign_btb_lc_id"],","); ?>','<? echo $year; ?>','<? echo $month_id; ?>','4','EDF Info')"><? echo number_format($val["foreign"],2); ?></a></td>
                                </tr>
                                <?
								$i++;$k++;
								$lc_year_total_local+=$val["local"];
								$lc_year_total_foreign+=$val["foreign"];
								$lc_grand_local+=$val["local"];
								$lc_grand_foreign+=$val["foreign"];
							}
							?>
                            <tr bgcolor="#CCCCCC">
                                <td>Year Total:</td>
                                <td align="right"><? echo number_format($lc_year_total_local,2); ?></td>
                                <td align="right"><? echo number_format($lc_year_total_foreign,2); ?></td>
                            </tr>
                            <?
							//$lc_grand_local+=$lc_year_total_local;
							//$lc_grand_foreign+=$lc_year_total_foreign;
							$year_total_local=$year_total_foreign=0;
						}
						?>
                        <tfoot>
                        	<tr>
                                <th align="right">Grand Total:</th>
                                <th align="right"><? echo number_format($lc_grand_local,2); ?></th>
                                <th align="right"><? echo number_format($lc_grand_foreign,2); ?></th>
                            </tr>
                        </tfoot>
                </table>
            </td>
            <td width="50"></td>
            <td  valign="top" width="350">
            	<p style="font-size:16px; font-weight:bold">BTB Liability Month Wise</p>
            	<table width="350" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" align="left">
                	
                    	<?
						$k=1; 
						foreach($lc_total_btb_data_arr as $year=>$month_data)
						{
							?>
                            <thead>
                                <tr>
                                    <th width="120">Year-<? echo $year; ?></th>
                                    <th width="120">Local</th>
                                    <th>Foreign</th>
                                </tr>
                            </thead>
                            <?
							ksort($month_data);
							foreach($month_data as $month_id=>$val)
							{
								$bgcolor=($k%2==0)?"#E9F3FF":"#FFFFFF";
								?>
                                <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i?>','<? echo $bgcolor?>')" id="tr_<? echo $i; ?>" style="cursor:pointer">
                                	<td><? echo $months[abs($month_id)]; ?></td>
                                    <td align="right" title="<? echo $val["local_btb_lc_id"]; ?>"><a href='#report_detals'  onclick= "openmypage('<? echo chop($val["local_btb_lc_id"],","); ?>','<? echo $year; ?>','<? echo $month_id; ?>','5','BTB Info')"><? echo number_format($val["local"],2); ?></a></td>
                                    <td align="right" title="<? echo $val["foreign_btb_lc_id"]; ?>"><a href='#report_detals'  onclick= "openmypage('<? echo chop($val["foreign_btb_lc_id"],","); ?>','<? echo $year; ?>','<? echo $month_id; ?>','5','BTB Info')"><? echo number_format($val["foreign"],2); ?></a></td>
                                </tr>
                                <?
								$i++;$k++;
								$lc_year_total_btb_local+=$val["local"];
								$lc_year_total_btb_foreign+=$val["foreign"];
								$gt_local_paid+=$val["local_paid"];
								$gt_foreign_paid+=$val["foreign_paid"];
								$lc_grand_btb_local+=$val["local"];;
								$lc_grand_btb_foreign+=$val["foreign"];
								
							}
							?>
                            <tr bgcolor="#CCCCCC">
                                <td align="right">Year Total:</td>
                                <td align="right"><? echo number_format($lc_year_total_btb_local,2); ?></td>
                                <td align="right"><? echo number_format($lc_year_total_btb_foreign,2); ?></td>
                            </tr>
                            <?
							//$lc_grand_btb_local+=$lc_year_total_btb_local;
							//$lc_grand_btb_foreign+=$lc_year_total_btb_foreign;
							$year_total_btb_local=$year_total_btb_foreign=0;
						}
						?>
                        
                        <tfoot>
                        	<tr>
                                <th align="right">Grand Total:</th>
                                <th align="right" title="<? echo $gt_local_paid; ?>"><? echo number_format($lc_grand_btb_local,2); ?></th>
                                <th align="right" title="<? echo $gt_foreign_paid; ?>"><? echo number_format($lc_grand_btb_foreign,2); ?></th>
                            </tr>
                        </tfoot>
                    
                </table>
            </td>
        </tr>
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
	$create_new_doc = fopen($filename, 'w');
	$is_created = fwrite($create_new_doc,ob_get_contents());
	$filename=$user_id."_".$name.".xls";
	echo "$total_data####$filename";
	exit();
}


if($action=="btb_details")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	if($btb_id=="") die;
	$day_of_month=cal_days_in_month(CAL_GREGORIAN, $month_val, $year_val);
	$maturity_start_date=$year_val."-".$month_val."-01";
	$maturity_end_date=$year_val."-".$month_val."-".$day_of_month;
	if($db_type==2)
	{
		$maturity_start_date=change_date_format($maturity_start_date,"","",1);
		$maturity_end_date=change_date_format($maturity_end_date,"","",1);
	}
	//echo $maturity_start_date."**".$maturity_end_date;die;
	//print_r($po_id);die;
?> 

<script>

	function print_window()
	{
		//document.getElementById('scroll_body').style.overflow="auto";
		//document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		//document.getElementById('scroll_body').style.overflowY="scroll";
		//document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>
<div style="width:660px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
<div style="width:660px" id="report_container">
<fieldset style="width:660px">
    <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="660">
        <thead>
        	<tr>
                <th width="40">SL NO</th>
                <th width="120">BTB LC NO</th>
                <th width="70">BTB LC Date</th>
                <th width="100">BTB LC Value</th>
                <th width="120">Invoice No</th>
                <th width="100">Invoice Value</th>
                <th>Maturity Date</th>
            </tr>
        </thead>
        <tbody>
		<?
		//for show file year
		/*
		$lc_year_sql=sql_select("select id as lc_sc_id, lc_year as lc_sc_year, 0 as type from com_export_lc union all select id as lc_sc_id, sc_year as lc_sc_year, 1 as type from com_sales_contract");
		$lc_sc_year=array();
		foreach($lc_year_sql as $row)
		{
			$lc_sc_year[$row[csf("lc_sc_id")]][$row[csf("type")]]=$row[csf("lc_sc_year")];
		}*/
		
		if($btb_id!="")
		{
			//previous query with file year and export lc/sc
			/*$btb_sql="select b.lc_sc_id, b.is_lc_sc, c.id as btb_lc_sc_id, c.lc_number as btb_lc_number, c.lc_date as btb_lc_date, c.lc_value, d.id as invoice_id, d.invoice_no, d.invoice_date, d.maturity_date, sum(e.current_acceptance_value) as inv_value
			from 
					com_btb_export_lc_attachment b,  com_btb_lc_master_details c, com_import_invoice_mst d, com_import_invoice_dtls e
			where 
					b.import_mst_id=c.id and c.id=e.btb_lc_id and e.import_invoice_id=d.id and c.id in($btb_id) and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1  and e.is_deleted=0 and e.status_active=1 and d.maturity_date between '$maturity_start_date' and '$maturity_end_date' 
			group by b.lc_sc_id, b.is_lc_sc, c.id, c.lc_number, c.lc_date, c.lc_value, d.id, d.invoice_no, d.invoice_date, d.maturity_date";*/
			
			if($type==3)
			{
				$btb_sql="select c.id as btb_lc_sc_id, c.lc_number as btb_lc_number, c.lc_date as btb_lc_date, c.lc_value, d.id as invoice_id, d.invoice_no, d.invoice_date, d.maturity_date, sum(e.current_acceptance_value) as inv_value
				from 
						com_btb_lc_master_details c, com_import_invoice_mst d, com_import_invoice_dtls e
				where 
						c.id=e.btb_lc_id and e.import_invoice_id=d.id and c.id in($btb_id) and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1  and e.is_deleted=0 and e.status_active=1 and c.lc_date between '$maturity_start_date' and '$maturity_end_date' 
				group by c.id, c.lc_number, c.lc_date, c.lc_value, d.id, d.invoice_no, d.invoice_date, d.maturity_date";
				
				
			}
			else
			{
				$btb_sql="select c.id as btb_lc_sc_id, c.lc_number as btb_lc_number, c.lc_date as btb_lc_date, c.lc_value, d.id as invoice_id, d.invoice_no, d.invoice_date, d.maturity_date, sum(e.current_acceptance_value) as inv_value
				from 
						com_btb_lc_master_details c, com_import_invoice_mst d, com_import_invoice_dtls e
				where 
						c.id=e.btb_lc_id and e.import_invoice_id=d.id and c.id in($btb_id) and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1  and e.is_deleted=0 and e.status_active=1 and d.maturity_date between '$maturity_start_date' and '$maturity_end_date' 
				group by c.id, c.lc_number, c.lc_date, c.lc_value, d.id, d.invoice_no, d.invoice_date, d.maturity_date";
			}
			
		}
		
		//echo $btb_sql;
		
		
		$payment_data_array=return_library_array("select invoice_id, sum(accepted_ammount) as paid_amt from com_import_payment where status_active=1 and is_deleted=0 group by invoice_id","invoice_id","paid_amt");
		
		
		$i=1;
        $sql_re=sql_select($btb_sql);
        $total_invoice_qty=0;  $total_order_qty=0;  $total_attach_qty=0;$result=0;
        
        foreach($sql_re as $row)
        {
			if($type==2)
			{
				$invoice_value=$row[csf("inv_value")]-$payment_data_array[$row[csf("invoice_id")]];
				if($invoice_value>0)
				{
					$lc_value=$payment_data_array[$row[csf('btb_lc_date')]];
					$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";  
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
						<td align="center"><? echo $i; ?></td>
						<td><p><?  echo $row[csf('btb_lc_number')]; ?>&nbsp;</p></td>
						<td align="center"><? if($row[csf('btb_lc_date')]!="" && $row[csf('btb_lc_date')]!="0000-00-00") echo change_date_format($row[csf('btb_lc_date')]); ?></td>
						<!--<td align="center"><?//  echo $lc_sc_year[$row[csf("lc_sc_id")]][$row[csf("is_lc_sc")]];?></td>-->
						<td align="right"><?  echo number_format($row[csf('lc_value')],2); $total_lc_value+=$row[csf('lc_value')]; ?></td>
						<td><p><?  echo $row[csf("invoice_no")]; ?>&nbsp;</p></td>
						<td align="right"><?  echo number_format($invoice_value,2); $total_invoice_value+=$invoice_value;  ?></td>
						<td align="center"><? if($row[csf('maturity_date')]!="" && $row[csf('maturity_date')]!="0000-00-00") echo change_date_format($row[csf('maturity_date')]); ?></td>
					</tr>
					<?
					$i++;
				}
			}
			else
			{
				$lc_value=$invoice_value=0;
				$invoice_value=$row[csf("inv_value")];
				$lc_value=$row[csf('lc_value')]-$payment_data_array[$row[csf('invoice_id')]];
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";  
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
					<td align="center"><? echo $i; ?></td>
					<td><p><?  echo $row[csf('btb_lc_number')]; ?>&nbsp;</p></td>
					<td align="center"><? if($row[csf('btb_lc_date')]!="" && $row[csf('btb_lc_date')]!="0000-00-00") echo change_date_format($row[csf('btb_lc_date')]); ?></td>
					<!--<td align="center"><?//  echo $lc_sc_year[$row[csf("lc_sc_id")]][$row[csf("is_lc_sc")]];?></td>-->
					<td align="right"><?  echo number_format($lc_value,2); $total_lc_value+=$lc_value; ?></td>
					<td><p><?  echo $row[csf("invoice_no")]; ?>&nbsp;</p></td>
					<td align="right"><?  echo number_format($invoice_value,2); $total_invoice_value+=$invoice_value;  ?></td>
					<td align="center"><? if($row[csf('maturity_date')]!="" && $row[csf('maturity_date')]!="0000-00-00") echo change_date_format($row[csf('maturity_date')]); ?></td>
				</tr>
				<?
				$i++;
			}
			
        }
        ?>
        </tbody>
        <tfoot>
            <tr >
                <th align="right">&nbsp;</th>
                <th align="right" >&nbsp;</th>
                <!--<th align="right">&nbsp;</th>-->
                <th align="right">Total:</th>
                <th align="right"><? echo number_format($total_lc_value,2); ?></th>
                <th align="right">&nbsp;</th>
                <th align="right"><? echo number_format($total_invoice_value,2); ?></th>
                <th align="right">&nbsp;</th>
            </tr>
        </tfoot>
    </table>
</fieldset>
</div>
<?
exit();
}


if($action=="btb_open_details")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $btb_id;die;
	if($btb_id=="") die;
	$day_of_month=cal_days_in_month(CAL_GREGORIAN, $month_val, $year_val);
	$maturity_start_date=$year_val."-".$month_val."-01";
	$maturity_end_date=$year_val."-".$month_val."-".$day_of_month;
	if($db_type==2)
	{
		$maturity_start_date=change_date_format($maturity_start_date,"","",1);
		$maturity_end_date=change_date_format($maturity_end_date,"","",1);
	}
	//echo $maturity_start_date."**".$maturity_end_date;die;
	//print_r($po_id);die;
?> 

<script>

	function print_window()
	{
		//document.getElementById('scroll_body').style.overflow="auto";
		//document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		//document.getElementById('scroll_body').style.overflowY="scroll";
		//document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>
<div style="width:450px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
<div style="width:450px" id="report_container">
<fieldset style="width:450px">
    <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="450">
        <thead>
        	<tr>
                <th width="50">SL NO</th>
                <th width="150">BTB LC NO</th>
                <th width="100">BTB LC Date</th>
                <th>BTB LC Value</th>
            </tr>
        </thead>
        <tbody>
		<?
		
		if($btb_id!="")
		{
			
			$btb_sql="select c.id as btb_lc_sc_id, c.lc_number as btb_lc_number, c.lc_date as btb_lc_date, c.lc_value
			from 
					com_btb_lc_master_details c 
			where 
					 c.id in($btb_id) and c.is_deleted=0 and c.status_active=1";
			
			if($type==5)
			{
				//$payment_data_array=return_library_array("select lc_id, sum(accepted_ammount) as paid_amt from  com_import_payment where status_active=1 and is_deleted=0 group by invoice_id","lc_id","paid_amt");
				$payment_data_array=return_library_array("select invoice_id, sum(accepted_ammount) as paid_amt from  com_import_payment where status_active=1 and is_deleted=0 group by invoice_id","invoice_id","paid_amt");
				$btb_id_sql=sql_select("select c.import_invoice_id, c.btb_lc_id from com_import_invoice_dtls c where c.is_deleted=0 and c.status_active=1 and c.btb_lc_id in($btb_id)");
				$btb_inv_id_arr=array();
				foreach($btb_id_sql as $row)
				{
					$btb_inv_id_arr[$row[csf("btb_lc_id")]].=$row[csf("import_invoice_id")].",";
				}
				
				
			}
			else
			{
				
				if($db_type==0)
				{
					$payment_data_array=return_library_array("select c.btb_lc_id, sum(c.current_acceptance_value) as edf_loan_value 
				from com_import_invoice_mst b, com_import_invoice_dtls c 
				where c.btb_lc_id in($btb_id) and c.import_invoice_id=b.id and b.edf_paid_date!='0000-00-00' and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 group by c.btb_lc_id","btb_lc_id","edf_loan_value");
				}
				else
				{
					$payment_data_array=return_library_array("select c.btb_lc_id, sum(c.current_acceptance_value) as edf_loan_value 
				from com_import_invoice_mst b, com_import_invoice_dtls c 
				where c.btb_lc_id in($btb_id) and c.import_invoice_id=b.id and b.edf_paid_date is not null and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 group by c.btb_lc_id","btb_lc_id","edf_loan_value");
				}
			}
			
			
			
			
			
		}
		
		//echo $btb_sql;//die;
		
		
		//$payment_data_array=return_library_array("select invoice_id, sum(accepted_ammount) as paid_amt from com_import_payment where status_active=1 and is_deleted=0 group by invoice_id","invoice_id","paid_amt");
		
		
		
		
		$i=1;
        $sql_re=sql_select($btb_sql);
        $total_invoice_qty=0;  $total_order_qty=0;  $total_attach_qty=0;$result=0;
        
        foreach($sql_re as $row)
        {
			if($type==5)
			{
				$all_inv_id_arr=array_unique(explode(",",chop($btb_inv_id_arr[$row[csf("btb_lc_sc_id")]],",")));
				foreach($all_inv_id_arr as $invoice_id)
				{
					$lc_payment+=$payment_data_array[$invoice_id];
				}
				$lc_value=$row[csf('lc_value')]-$lc_payment;
				$lc_payment=0;
			}
			else
			{
				$lc_value=$row[csf('lc_value')]-$payment_data_array[$row[csf("btb_lc_sc_id")]];
			}
			
			
			if(number_format($lc_value,2)>0)
			{
				$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";  
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
					<td align="center"><? echo $i; ?></td>
					<td><p><?  echo $row[csf('btb_lc_number')]; ?>&nbsp;</p></td>
					<td align="center"><? if($row[csf('btb_lc_date')]!="" && $row[csf('btb_lc_date')]!="0000-00-00") echo change_date_format($row[csf('btb_lc_date')]); ?></td>
					<td align="right"><?  echo number_format($lc_value,2); $total_lc_value+=$lc_value; ?></td>
				</tr>
				<?
				$lc_value=0;
				$i++;
			}
			
        }
        ?>
        </tbody>
        <tfoot>
            <tr >
                <th align="right">&nbsp;</th>
                <th align="right" >&nbsp;</th>
                <th align="right">Total:</th>
                <th align="right"><? echo number_format($total_lc_value,2); ?></th>
            </tr>
        </tfoot>
    </table>
</fieldset>
</div>
<?
exit();
}


if($action=="btb_paid_details")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	if($inv_id=="") die;
	//echo $type;die;
	$day_of_month=cal_days_in_month(CAL_GREGORIAN, $month_val, $year_val);
	$btb_start_date=$year_val."-".$month_val."-01";
	$btb_end_date=$year_val."-".$month_val."-".$day_of_month;
	if($db_type==2)
	{
		$btb_start_date=change_date_format($btb_start_date,"","",1);
		$btb_end_date=change_date_format($btb_end_date,"","",1);
	}
	
	if($type==3)
	{
		?>
        <div style="width:760px" id="report_container">
        <fieldset style="width:760px">
            <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="760">
                <thead>
                    <tr>
                        <th width="50">SL NO</th>
                        <th width="140">BTB LC NO</th>
                        <th width="80">BTB LC Date</th>
                        <th width="100">BTB LC Value</th>
                        <th width="100">Paid Value</th>
                        <th width="100">Balance Value</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody>
                <?
				$payment_data_array=return_library_array("select invoice_id, sum(accepted_ammount) as paid_amt from  com_import_payment where status_active=1 and is_deleted=0 group by invoice_id","invoice_id","paid_amt");
                $btb_sql="select c.id as btb_lc_sc_id, c.lc_category, c.lc_number as btb_lc_number, c.lc_date as btb_lc_date, c.lc_value, d.id as inv_id, d.edf_paid_date, e.current_acceptance_value as inv_value
                    from 
                            com_btb_lc_master_details c left join com_import_invoice_dtls e on c.id=e.btb_lc_id and e.is_deleted=0 and e.status_active=1 left join  com_import_invoice_mst d on e.import_invoice_id=d.id and d.is_deleted=0 and d.status_active=1  
                    where 
                             c.id in($inv_id) and c.is_deleted=0 and c.status_active=1 and c.lc_date between '$btb_start_date' and '$btb_end_date'";
                //echo $btb_sql;
                
                $i=1;
                $sql_re=sql_select($btb_sql);
				$details_data=array();
				foreach($sql_re as $row)
                {
					$details_data[$row[csf('btb_lc_sc_id')]]["btb_lc_sc_id"]=$row[csf('btb_lc_sc_id')];
					$details_data[$row[csf('btb_lc_sc_id')]]["btb_lc_number"]=$row[csf('btb_lc_number')];
					$details_data[$row[csf('btb_lc_sc_id')]]["btb_lc_date"]=$row[csf('btb_lc_date')];
					$details_data[$row[csf('btb_lc_sc_id')]]["lc_value"]=$row[csf('lc_value')];
					if(($row[csf('lc_category')]*1)==3 || ($row[csf('lc_category')]*1)==5 || ($row[csf('lc_category')]*1)==11)
					{
						if($row[csf('edf_paid_date')] !="" && $row[csf('edf_paid_date')]!="0000-00-00")
						{
							$details_data[$row[csf('btb_lc_sc_id')]]["paid_value"]+=$row[csf('inv_value')];
						}
					}
					else
					{
						if($inv_check_arr[$row[csf('inv_id')]]=="")
						{
							$inv_check_arr[$row[csf('inv_id')]]=$row[csf('inv_id')];
							$details_data[$row[csf('btb_lc_sc_id')]]["paid_value"]+=$payment_data_array[$row[csf('inv_id')]];
						}
					}
				}
				
                foreach($details_data as $row)
                {
                    $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";
					$balance_value=  $row[('lc_value')]-$row[("paid_value")];
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <td><p><?  echo $row[('btb_lc_number')]; ?>&nbsp;</p></td>
                        <td align="center"><? if($row[('btb_lc_date')]!="" && $row[('btb_lc_date')]!="0000-00-00") echo change_date_format($row[('btb_lc_date')]); ?></td>
                        <td align="right"><?  echo number_format($row[('lc_value')],2); $total_lc_value+=$row[('lc_value')]; ?></td>
                        <td align="right"><?  echo number_format($row[("paid_value")],2); $total_paid_value+=$row[("paid_value")];  ?></td>
                        <td align="right"><?  echo number_format($balance_value,2); $total_balance_value+=$balance_value;  ?></td>
                        <td>&nbsp;</td>
                    </tr>
                    <?
                    $i++;
                }
                ?>
                </tbody>
                <tfoot>
                    <tr >
                        <th align="right">&nbsp;</th>
                        <th align="right" >&nbsp;</th>
                        <th align="right">Total:</th>
                        <th align="right"><? echo number_format($total_lc_value,2); ?></th>
                        <th align="right"><? echo number_format($total_paid_value,2); ?></th>
                        <th align="right"><? echo number_format($total_balance_value,2); ?></th>
                        <th align="right">&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
        </fieldset>
        </div>
        <?
	}
	else
	{
		?>
        <script>
        function print_window()
        {
            //document.getElementById('scroll_body').style.overflow="auto";
            //document.getElementById('scroll_body').style.maxHeight="none";
            
            var w = window.open("Surprise", "#");
            var d = w.document.open();
            d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
        '<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
        
            d.close();
            //document.getElementById('scroll_body').style.overflowY="scroll";
            //document.getElementById('scroll_body').style.maxHeight="230px";
        }	
        </script>
        <div style="width:760px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
        <div style="width:760px" id="report_container">
        <fieldset style="width:760px">
            <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="760">
                <thead>
                    <tr>
                        <th width="40">SL NO</th>
                        <th width="120">BTB LC NO</th>
                        <th width="70">BTB LC Date</th>
                        <th width="100">BTB LC Value</th>
                        <th width="120">Invoice No</th>
                        <th width="100">Invoice Value</th>
                        <th width="100">Paid Value</th>
                        <th>Paid Date</th>
                    </tr>
                </thead>
                <tbody>
                <?
                
                if($inv_id!="")
                {
                    
                    
                    $btb_sql="select c.id as btb_lc_sc_id, c.lc_number as btb_lc_number, c.lc_date as btb_lc_date, c.lc_value, d.id as invoice_id, d.invoice_no, d.invoice_date, d.maturity_date, sum(e.current_acceptance_value) as inv_value, f.accepted_ammount, f.payment_date
                    from 
                            com_btb_lc_master_details c, com_import_invoice_mst d, com_import_invoice_dtls e, com_import_payment f
                    where 
                            c.id=e.btb_lc_id and e.import_invoice_id=d.id and d.id=f.invoice_id  and d.id in($inv_id) and c.is_deleted=0 and c.status_active=1 and d.is_deleted=0 and d.status_active=1  and e.is_deleted=0 and e.status_active=1 and c.lc_date between '$btb_start_date' and '$btb_end_date' 
                    group by c.id, c.lc_number, c.lc_date, c.lc_value, d.id, d.invoice_no, d.invoice_date, d.maturity_date, f.accepted_ammount, f.payment_date";
                }
                
                //echo $btb_sql;
                
                $i=1;
                $sql_re=sql_select($btb_sql);
                $total_invoice_qty=0;  $total_order_qty=0;  $total_attach_qty=0;$result=0;
                
                foreach($sql_re as $row)
                {
                    $bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";  
                    ?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td align="center"><? echo $i; ?></td>
                        <td><p><?  echo $row[csf('btb_lc_number')]; ?>&nbsp;</p></td>
                        <td align="center"><? if($row[csf('btb_lc_date')]!="" && $row[csf('btb_lc_date')]!="0000-00-00") echo change_date_format($row[csf('btb_lc_date')]); ?></td>
                        <td align="right"><?  echo number_format($row[csf('lc_value')],2); $total_lc_value+=$row[csf('lc_value')]; ?></td>
                        <td><p><?  echo $row[csf("invoice_no")]; ?>&nbsp;</p></td>
                        <td align="right"><?  echo number_format($row[csf("inv_value")],2); $total_invoice_value+=$row[csf("inv_value")];  ?></td>
                        <td align="right"><?  echo number_format($row[csf("accepted_ammount")],2); $total_paid_value+=$row[csf("accepted_ammount")];  ?></td>
                        <td align="center"><? if($row[csf('payment_date')]!="" && $row[csf('payment_date')]!="0000-00-00") echo change_date_format($row[csf('payment_date')]); ?></td>
                    </tr>
                    <?
                    $i++;
                }
                ?>
                </tbody>
                <tfoot>
                    <tr >
                        <th align="right">&nbsp;</th>
                        <th align="right" >&nbsp;</th>
                        <th align="right">Total:</th>
                        <th align="right"><? echo number_format($total_lc_value,2); ?></th>
                        <th align="right">&nbsp;</th>
                        <th align="right"><? echo number_format($total_invoice_value,2); ?></th>
                        <th align="right"><? echo number_format($total_paid_value,2); ?></th>
                        <th align="right">&nbsp;</th>
                    </tr>
                </tfoot>
            </table>
        </fieldset>
        </div>
        <?
	}
	
	//echo $maturity_start_date."**".$maturity_end_date;die;
	//print_r($po_id);die;
?> 

		
<?
exit();
}

if($action=="btb_open_details")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	if($btb_id=="") die;
	$day_of_month=cal_days_in_month(CAL_GREGORIAN, $month_val, $year_val);
	$btb_start_date=$year_val."-".$month_val."-01";
	$btb_end_date=$year_val."-".$month_val."-".$day_of_month;
	if($db_type==2)
	{
		$btb_start_date=change_date_format($btb_start_date,"","",1);
		$btb_end_date=change_date_format($btb_end_date,"","",1);
	}
	//echo $maturity_start_date."**".$maturity_end_date;die;
	//print_r($po_id);die;
?> 

<script>

	function print_window()
	{
		//document.getElementById('scroll_body').style.overflow="auto";
		//document.getElementById('scroll_body').style.maxHeight="none";
		
		var w = window.open("Surprise", "#");
		var d = w.document.open();
		d.write ('<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN""http://www.w3.org/TR/html4/strict.dtd">'+
	'<html><head><link rel="stylesheet" href="../../../css/style_common.css" type="text/css"/><title></title></head><body>'+document.getElementById('report_container').innerHTML+'</body</html>');
	
		d.close();
		//document.getElementById('scroll_body').style.overflowY="scroll";
		//document.getElementById('scroll_body').style.maxHeight="230px";
	}	
	
</script>
<div style="width:500px" align="center"><input type="button" value="Print Preview" onClick="print_window()" style="width:100px"  class="formbutton"/></div>
<div style="width:500px" id="report_container">
<fieldset style="width:500px">
    <table border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" width="480">
        <thead>
        	<tr>
                <th width="50">SL NO</th>
                <th width="150">BTB LC NO</th>
                <th width="100">BTB LC Date</th>
                <th>BTB LC Value</th>
            </tr>
        </thead>
        <tbody>
		<?
		
		if($btb_id!="")
		{
			
			$btb_sql="select c.id as btb_lc_sc_id, c.lc_number as btb_lc_number, c.lc_date as btb_lc_date, c.lc_value
			from 
					com_btb_lc_master_details c
			where 
					c.id in($btb_id) and c.is_deleted=0 and c.status_active=1  and c.lc_date between '$btb_start_date' and '$btb_end_date'";
		}
		
		//echo $btb_sql;
		
		$i=1;
        $sql_re=sql_select($btb_sql);
        $total_invoice_qty=0;  $total_order_qty=0;  $total_attach_qty=0;$result=0;
        
        foreach($sql_re as $row)
        {
			$bgcolor=($i%2==0)?"#E9F3FF":"#FFFFFF";  
			?>
			<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
				<td align="center"><? echo $i; ?></td>
				<td><p><?  echo $row[csf('btb_lc_number')]; ?>&nbsp;</p></td>
				<td align="center"><? if($row[csf('btb_lc_date')]!="" && $row[csf('btb_lc_date')]!="0000-00-00") echo change_date_format($row[csf('btb_lc_date')]); ?></td>
				<td align="right"><?  echo number_format($row[csf('lc_value')],2); $total_lc_value+=$row[csf('lc_value')]; ?></td>
			</tr>
			<?
			$i++;
			
        }
        ?>
        </tbody>
        <tfoot>
            <tr >
                <th align="right">&nbsp;</th>
                <th align="right" >&nbsp;</th>
                <th align="right">Total:</th>
                <th align="right"><? echo number_format($total_lc_value,2); ?></th>
            </tr>
        </tfoot>
    </table>
</fieldset>
</div>
<?
exit();
}


disconnect($con);
?>
