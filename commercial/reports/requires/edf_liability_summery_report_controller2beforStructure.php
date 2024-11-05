<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

$seource_des_array=array(3=>"Non EPZ",4=>"Non EPZ",5=>"Abroad",6=>"Abroad",11=>"EPZ",12=>"EPZ");

if ($action=="load_drop_down_year")
{
	$sql=sql_select("select lc_year as lc_sc_year from com_export_lc where beneficiary_name='$data' and status_active=1 and is_deleted=0  union all select sc_year as lc_sc_year from com_sales_contract where beneficiary_name='$data' and status_active=1 and is_deleted=0");
	foreach($sql as $row)
	{
		$lc_sc_year[$row[csf("lc_sc_year")]]=$row[csf("lc_sc_year")];
	}
	//create_drop_down( $field_id, $field_width, $query, $field_list, $show_select, $select_text_msg, $selected_index, $onchange_func, $is_disabled, $array_index, $fixed_options, $fixed_values, $not_show_array_index, $tab_index, $new_conn, $field_name, $additionalClass, $additionalAttributes )
	//echo date('Y');die;
	echo create_drop_down( "hide_year", 100, $lc_sc_year,"", 1, "-- Select --", date('Y'), "",0, date('Y'));
	exit();
}

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_lein_bank=str_replace("'","",$cbo_lein_bank); 
	$txt_lc_sc_date=str_replace("'","",$txt_lc_sc_date);
	//echo $txt_lc_sc_date;die;
	$company_arr=return_library_array( "select company_name,id from  lib_company",'id','company_name');
	$bank_arr=return_library_array( "select bank_name,id from lib_bank",'id','bank_name');
	
	
	/*$lc_sc_sql="select id as lc_sc_id, lien_bank, convertible_to_lc, contract_value as lc_sc_value, converted_from, contract_date as lc_sc_date, 1 as type from com_sales_contract where beneficiary_name=$cbo_company_name and lien_bank=$cbo_lein_bank and contract_date<='$txt_lc_sc_date' and sc_year>'2015'
	union all
	select id as lc_sc_id, lien_bank, replacement_lc as convertible_to_lc, lc_value as lc_sc_value, null as converted_from, lc_date as lc_sc_date, 2 as type from com_export_lc where beneficiary_name=$cbo_company_name and lien_bank=$cbo_lein_bank and lc_date<='$txt_lc_sc_date' and lc_year>'2015'";
	
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
	if($sc_id!="")
	{
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
	}
	
	if($lc_id!="")
	{
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
	}
	if($lc_id=="" && $sc_id=="") die;
	$btb_id_sql="select b.id as btb_id, b.lc_date, b.tenor, b.payterm_id, b.lc_category, b.lc_value, 1 as type from com_btb_export_lc_attachment a, com_btb_lc_master_details b where a.import_mst_id=b.id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.is_lc_sc=1 and b.ref_closing_status<>1 $rlz_sc_cond
	group by b.id, b.lc_date, b.tenor, b.payterm_id, b.lc_category, b.lc_value
	union all 
	select b.id as btb_id, b.lc_date, b.tenor, b.payterm_id, b.lc_category, b.lc_value, 2 as type from com_btb_export_lc_attachment a, com_btb_lc_master_details b where a.import_mst_id=b.id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.is_lc_sc=0 and b.ref_closing_status<>1 $rlz_lc_cond
	group by b.id, b.lc_date, b.tenor, b.payterm_id, b.lc_category, b.lc_value";
	*/
	//var_dump($realize_data_arr);die;
	
	$payment_data_array=return_library_array("select invoice_id, sum(accepted_ammount) as paid_amt from  com_import_payment where status_active=1 and is_deleted=0 and payment_date<='$txt_lc_sc_date' group by invoice_id","invoice_id","paid_amt");
	//and b.invoice_date <='$txt_lc_sc_date'
	$btb_sql="select a.id as btb_lc_id, a.lc_date, a.tenor, a.payterm_id, a.lc_category, a.lc_value, a.maturity_from_id, b.id as import_inv_id, b.maturity_date, b.edf_paid_date, b.bank_acc_date, b.nagotiate_date, b.shipment_date, b.bill_date, a.maturity_from_id, sum(c.current_acceptance_value) as edf_loan_value, 1 as type 
	from com_btb_lc_master_details a, com_import_invoice_mst b, com_import_invoice_dtls c
	where a.id=c.btb_lc_id and c.import_invoice_id=b.id  and a.lc_category>0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.ref_closing_status<>1 and a.payterm_id <> 3 and a.importer_id=$cbo_company_name and a.issuing_bank_id=$cbo_lein_bank and a.lc_date <='$txt_lc_sc_date'
	group by a.id, a.lc_date, a.tenor, a.payterm_id, a.lc_category, a.lc_value, a.maturity_from_id, b.id, b.maturity_date, b.edf_paid_date, b.bank_acc_date, b.nagotiate_date, b.shipment_date, b.bill_date, a.maturity_from_id";
	//echo $btb_sql;//die;
	
	$btb_sql_result=sql_select($btb_sql);
	$edf_libality_data=$btb_libality_data=array();
	$total_edf_data_arr=$total_btb_data_arr=array();
	$lc_total_edf_data_arr=array();
	$invoce_data_arr=array();
	$pending_edf_local=$pending_edf_forein=$pending_btb_local=$pending_btb_forein=0;
	foreach($btb_sql_result as $row)
	{
		$lc_wise_aeecp[$row[csf("btb_lc_id")]]+=$row[csf("edf_loan_value")];
		if($row[csf("edf_paid_date")]!="" && $row[csf("edf_paid_date")]!="0000-00-00" && (abs($row[csf("lc_category")])==3 || abs($row[csf("lc_category")])==5 || abs($row[csf("lc_category")])==11))
		{
			$lc_wise_edf_paid[$row[csf("btb_lc_id")]]+=$row[csf("edf_loan_value")];
		}
		if($row[csf("maturity_from_id")]==1)
		$maturity_date=$row[csf("bank_acc_date")];
		else if($row[csf("maturity_from_id")]==2)
		$maturity_date=$row[csf("shipment_date")];
		else if($row[csf("maturity_from_id")]==3)
		$maturity_date=$row[csf("nagotiate_date")];
		else if($row[csf("maturity_from_id")]==4)
		$maturity_date=$row[csf("bill_date")];
		//echo strtotime($maturity_date)."=".$maturity_date."=".strtotime($txt_lc_sc_date)."=".$txt_lc_sc_date;die;
		/*if($row[csf("payterm_id")]==1 && $row[csf("bank_acc_date")]!="" && $row[csf("bank_acc_date")]!="0000-00-00")
		{
			$btb_lc_id_check[$row[csf("btb_lc_id")]]=$row[csf("btb_lc_id")];
			if(abs($row[csf("lc_category")])==3)
			{
				$paid_values=0;
				if($row[csf("edf_paid_date")]!="" && $row[csf("edf_paid_date")]!="0000-00-00")
				{
					$paid_values=$row[csf("edf_loan_value")];
				}
				$ac_edf_grand_total+=$row[csf("edf_loan_value")]-$paid_values;
			}
			
			if(abs($row[csf("lc_category")])==5 || abs($row[csf("lc_category")])==11)
			{
				$paid_values=0;
				if($row[csf("edf_paid_date")]!="" && $row[csf("edf_paid_date")]!="0000-00-00")
				{
					$paid_values=$row[csf("edf_loan_value")];
				}
				$ac_edf_grand_total+=$row[csf("edf_loan_value")]-$paid_values;
			}
		}*/
		//#########   check maturity date #########//
		if(strtotime($maturity_date)<=strtotime($txt_lc_sc_date))
		{
			if($row[csf("payterm_id")]==1 && $row[csf("maturity_date")]!="" && $row[csf("maturity_date")]!="0000-00-00")
			{
				$paid_values=0;
				if($row[csf("edf_paid_date")]!="" && $row[csf("edf_paid_date")]!="0000-00-00")
				{
					$paid_values=$row[csf("edf_loan_value")];
				}
				
				if(abs($row[csf("lc_category")])==3)
				{
					$actual_edf_data[date('Y',strtotime($row[csf("maturity_date")]))][date('n',strtotime($row[csf("maturity_date")]))]["local"]+=$row[csf("edf_loan_value")]-$paid_values;
					$ac_edf_grand_total+=$row[csf("edf_loan_value")]-$paid_values;
					if(($row[csf("edf_loan_value")]-$paid_values)>0)
					{
						$actual_edf_data[date('Y',strtotime($row[csf("maturity_date")]))][date('n',strtotime($row[csf("maturity_date")]))]["local_btb_lc_id"].=$row[csf("btb_lc_id")].",";
					}
				}
				if(abs($row[csf("lc_category")])==5 || abs($row[csf("lc_category")])==11)
				{
					if(($row[csf("edf_loan_value")]-$paid_values)>0)
					{
						$actual_edf_data[date('Y',strtotime($row[csf("maturity_date")]))][date('n',strtotime($row[csf("maturity_date")]))]["foreign_btb_lc_id"].=$row[csf("btb_lc_id")].",";
					}
					$actual_edf_data[date('Y',strtotime($row[csf("maturity_date")]))][date('n',strtotime($row[csf("maturity_date")]))]["foreign"]+=$row[csf("edf_loan_value")]-$paid_values;
					$ac_edf_grand_total+=$row[csf("edf_loan_value")]-$paid_values;
				}
				
			}
			
			if($row[csf("payterm_id")]!=1 && $row[csf("maturity_date")]!="" && $row[csf("maturity_date")]!="0000-00-00")
			{
				if(abs($row[csf("lc_category")])==4)
				{
					if(($row[csf("edf_loan_value")]-$payment_data_array[$row[csf("import_inv_id")]])>0)
					{
						$actual_btb_data[date('Y',strtotime($row[csf("maturity_date")]))][date('n',strtotime($row[csf("maturity_date")]))]["local_btb_lc_id"].=$row[csf("btb_lc_id")].",";
					}
					if(date('Y',strtotime($row[csf("maturity_date")]))==2019 && date('n',strtotime($row[csf("maturity_date")]))==5)
					{
						$test_data[$row[csf("btb_lc_id")]]+=$row[csf("edf_loan_value")]-$payment_data_array[$row[csf("import_inv_id")]];
					}
					
					$actual_btb_data[date('Y',strtotime($row[csf("maturity_date")]))][date('n',strtotime($row[csf("maturity_date")]))]["local"]+=$row[csf("edf_loan_value")]-$payment_data_array[$row[csf("import_inv_id")]];
				}
				if(abs($row[csf("lc_category")])!=3 && abs($row[csf("lc_category")])!=4 && abs($row[csf("lc_category")])!=5 && abs($row[csf("lc_category")])!=11)
				{
					if(($row[csf("edf_loan_value")]-$payment_data_array[$row[csf("import_inv_id")]])>0)
					{
						$actual_btb_data[date('Y',strtotime($row[csf("maturity_date")]))][date('n',strtotime($row[csf("maturity_date")]))]["foreign_btb_lc_id"].=$row[csf("btb_lc_id")].",";
					}
					$actual_btb_data[date('Y',strtotime($row[csf("maturity_date")]))][date('n',strtotime($row[csf("maturity_date")]))]["foreign"]+=$row[csf("edf_loan_value")]-$payment_data_array[$row[csf("import_inv_id")]];
				}
			}
			
			$invoce_data_arr[$row[csf("btb_lc_id")]][$row[csf("import_inv_id")]]["lc_date"]=$row[csf("lc_date")];
			$invoce_data_arr[$row[csf("btb_lc_id")]][$row[csf("import_inv_id")]]["maturity_date"]=$row[csf("maturity_date")];
			$invoce_data_arr[$row[csf("btb_lc_id")]][$row[csf("import_inv_id")]]["edf_paid_date"]=$row[csf("edf_paid_date")];
			$invoce_data_arr[$row[csf("btb_lc_id")]][$row[csf("import_inv_id")]]["edf_loan_value"]=$row[csf("edf_loan_value")];
		}
	}
	
	$tot_pending_local=$pending_edf_local+$pending_btb_local;
	$tot_pending_forein=$pending_edf_forein+$pending_btb_forein;
	
	
	$btb_id_sql="select b.id as btb_id, b.lc_date, b.tenor, b.payterm_id, b.lc_category, b.lc_value
	from com_btb_lc_master_details b 
	where b.is_deleted=0 and b.status_active=1 and b.ref_closing_status<>1 and b.payterm_id <> 3 and b.importer_id=$cbo_company_name and b.issuing_bank_id=$cbo_lein_bank and b.lc_date <='$txt_lc_sc_date' and (b.lc_category<>0 or b.lc_category is not null)";
	//echo $btb_id_sql;
	$test_count=array();
	$btb_id_result=sql_select($btb_id_sql);
	foreach($btb_id_result as $row)
	{
		if($btb_check[$row[csf("btb_id")]]=="")
		{
			$pending_btb_value=$row[csf("lc_value")]-$lc_wise_aeecp[$row[csf("btb_id")]];
			$btb_check[$row[csf("btb_id")]]=$row[csf("btb_id")];
			$lc_date=add_date($row[csf("lc_date")],$row[csf("tenor")]);
			if($row[csf("payterm_id")]==1 && abs($row[csf("lc_category")])==3)
			{
				$lc_total_edf_data_arr[date('Y',strtotime($lc_date))][date('n',strtotime($lc_date))]["local"]+=$row[csf("lc_value")]-$lc_wise_edf_paid[$row[csf("btb_id")]];
				if($pending_btb_value>0)
				{
					$all_pending_edf_local.=$row[csf("btb_id")].",";
					$lc_total_edf_data_arr[date('Y',strtotime($lc_date))][date('n',strtotime($lc_date))]["local_btb_lc_id"].=$row[csf("btb_id")].",";
				}
			}
			
			if($row[csf("payterm_id")]==1  && (abs($row[csf("lc_category")])==5 || abs($row[csf("lc_category")])==11))
			{
				$lc_total_edf_data_arr[date('Y',strtotime($lc_date))][date('n',strtotime($lc_date))]["foreign"]+=$row[csf("lc_value")]-$lc_wise_edf_paid[$row[csf("btb_id")]];
				
				if($pending_btb_value>0)
				{
					$all_pending_edf_foreign.=$row[csf("btb_id")].",";
					$lc_total_edf_data_arr[date('Y',strtotime($lc_date))][date('n',strtotime($lc_date))]["foreign_btb_lc_id"].=$row[csf("btb_id")].",";
				}
				
			}
			
			if(abs($row[csf("lc_category")])==4)
			{
				foreach($invoce_data_arr[$row[csf("btb_id")]] as $inv_id=>$value)
				{
					$lc_total_btb_data_arr[date('Y',strtotime($lc_date))][date('n',strtotime($lc_date))]["local_paid"]+=$payment_data_array[$inv_id];
					$lc_local_paid+=$payment_data_array[$inv_id];
				}
				$lc_local_pending=$row[csf("lc_value")]-$lc_local_paid;
				$lc_total_btb_data_arr[date('Y',strtotime($lc_date))][date('n',strtotime($lc_date))]["local"]+=$row[csf("lc_value")]-$lc_local_paid;
				$lc_local_paid=0;
				if($lc_local_pending>0)
				{
					$all_pending_btb_local.=$row[csf("btb_id")].",";
					$lc_total_btb_data_arr[date('Y',strtotime($lc_date))][date('n',strtotime($lc_date))]["local_btb_lc_id"].=$row[csf("btb_id")].",";
				}
				
			}
			
			if(abs($row[csf("lc_category")])!=3 && abs($row[csf("lc_category")])!=4 && abs($row[csf("lc_category")])!=5 && abs($row[csf("lc_category")])!=11)
			{
				foreach($invoce_data_arr[$row[csf("btb_id")]] as $inv_id=>$value)
				{
					$lc_total_btb_data_arr[date('Y',strtotime($lc_date))][date('n',strtotime($lc_date))]["foreign_paid"]+=$payment_data_array[$inv_id];
					$lc_foreign_value+=$payment_data_array[$inv_id];
				}
				$test_count[$row[csf("btb_id")]]=abs($row[csf("lc_category")]);
				$lc_total_btb_data_arr[date('Y',strtotime($lc_date))][date('n',strtotime($lc_date))]["foreign"]+=$row[csf("lc_value")]-$lc_foreign_value;
				$lc_foreign_pending=$row[csf("lc_value")]-$lc_foreign_value;
				$lc_foreign_value=0;
				if($lc_foreign_pending>0)
				{
					$all_pending_btb_foreign.=$row[csf("btb_id")].",";
					$lc_total_btb_data_arr[date('Y',strtotime($lc_date))][date('n',strtotime($lc_date))]["foreign_btb_lc_id"].=$row[csf("btb_id")].",";
				}
			}
		}
	}
	//echo count($test_count);echo "<pre>";print_r($test_count); die;
	//echo "<pre>";print_r($lc_total_edf_data_arr);die;
	ob_start();
?>
<div style="width:810px;" id="scroll_body">
<fieldset style="width:100%">
	<p style="font-size:16px; font-weight:bold">Summary</p>
	<p style="font-size:16px; font-weight:bold"><? echo "Bank name: ". $bank_arr[$cbo_lein_bank];?></p>
    <?
	foreach($lc_total_edf_data_arr as $year=>$month_data)
	{
		foreach($month_data as $month_id=>$val)
		{
			$edf_grand_total+=$val["local"]+$val["foreign"];
			$prev_edf_local_balance+=$val["local"];
			$prev_edf_foreign_balance+=$val["foreign"];
		}
	}
	
	foreach($lc_total_btb_data_arr as $year=>$month_data)
	{
		
		foreach($month_data as $month_id=>$val)
		{
			$btb_grand_total+=$val["local"]+$val["foreign"];
			$prev_btb_local_balance+=$val["local"];
			$prev_btb_foreign_balance+=$val["foreign"];
		}
	}
	//echo "<pre>";print_r($actual_btb_data);die;
	
	$grand_total=$edf_grand_total+$btb_grand_total;
	
	foreach($actual_btb_data as $year=>$month_data)
	{
		foreach($month_data as $month_id=>$val)
		{
			if(number_format($val["local"],2,'.','')>0)
			{
				$ac_btb_grand_total+=$val["local"];
			}
			if(number_format($val["foreign"],2,'.','')>0)
			{
				$ac_btb_grand_total+=$val["foreign"];
			}
			//$ac_btb_grand_total+=$val["local"]+$val["foreign"];
		}
	}
	$ac_grand_total=$ac_edf_grand_total+$ac_btb_grand_total;
	?>
    <table width="780" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" align="left">
    	<thead>
        	<tr>
            	<th>EDF Liability</th>
                <th>BTB LC Liability</th>
                <th>Total Liability</th>
            </tr>
        </thead>
        <tbody>
        	<tr bgcolor="#FFFFFF">
            	<td align="right" style="font-size:18px; font-weight:bold;"><? echo number_format($edf_grand_total,2); ?></td>
                <td align="right" style="font-size:18px; font-weight:bold;"><? echo number_format($btb_grand_total,2); ?></td>
                <td align="right" style="font-size:18px; font-weight:bold;"><? echo number_format($grand_total,2); ?></td>
            </tr>
        </tbody>
    </table>
    <table width="800" cellpadding="0" cellspacing="0" align="left" style="margin-top:20px;">
    	<tr>
        	<td width="350" valign="top">
                <table width="350" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" align="left">
                	<thead>
                        <tr>
                            <th colspan="3" style="font-size:16px; font-weight:bold">EDF Liability Month Wise</th>
                        </tr>
                    </thead>
                		<thead>
                            <tr>
                                <th width="120">Month</th>
                                <th width="120">Local</th>
                                <th>Foreign</th>
                            </tr>
                        </thead>
                    	<?
						$i=$k=1; 
						//var_dump($lc_total_edf_data_arr);die;
						foreach($lc_total_edf_data_arr as $year=>$month_data)
						{
							ksort($month_data);
							foreach($month_data as $month_id=>$val)
							{
								if(number_format($val["local"],2) >=1 || number_format($val["foreign"],2) >=1)
								{
									$bgcolor=($k%2==0)?"#E9F3FF":"#FFFFFF";	 
									$i++;
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i?>','<? echo $bgcolor?>')" id="tr_<? echo $i; ?>" style="cursor:pointer">
										<td><? echo $months[abs($month_id)]."-". $year; ?></td>
										<td align="right"><? echo number_format($val["local"],2); ?></td>
										<td align="right"><? echo number_format($val["foreign"],2); ?></td>
									</tr>
									<?
									$k++;
								}
								$lc_grand_local+=$val["local"];
								$lc_grand_foreign+=$val["foreign"];
							}
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
            	<table width="350" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" align="left">
                	<thead>
                        <tr>
                            <th colspan="3" style="font-size:16px; font-weight:bold">BTB Liability Month Wise</th>
                        </tr>
                    </thead>
                		<thead>
                            <tr>
                                <th width="120">Month</th>
                                <th width="120">Local</th>
                                <th>Foreign</th>
                            </tr>
                        </thead>
                    	<?
						$k=1; 
						$i=$i+1;
						foreach($lc_total_btb_data_arr as $year=>$month_data)
						{
							ksort($month_data);
							foreach($month_data as $month_id=>$val)
							{
								if(number_format($val["local"],2)>=1 || number_format($val["foreign"],2)>=1)
								{
									$bgcolor=($k%2==0)?"#E9F3FF":"#FFFFFF";
									$i++;
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i?>','<? echo $bgcolor?>')" id="tr_<? echo $i; ?>" style="cursor:pointer">
										<td><? echo $months[abs($month_id)]."-". $year; ?></td>
										<td align="right" title="<? echo $val["local_btb_lc_id"]; ?>"><? echo number_format($val["local"],2); ?></td>
										<td align="right" title="<? echo $val["foreign_btb_lc_id"]; ?>"><? echo number_format($val["foreign"],2); ?></td>
									</tr>
									<?
									$k++;
									$gt_btb_local+=$val["local"];
									$gt_btb_foreign+=$val["foreign"];
								}
							}
						}
						?>
                        <tfoot>
                        	<tr>
                                <th align="right">Grand Total:</th>
                                <th align="right" title="<? echo $gt_local_paid; ?>"><? echo number_format($gt_btb_local,2); ?></th>
                                <th align="right" title="<? echo $gt_foreign_paid; ?>"><? echo number_format($gt_btb_foreign,2); ?></th>
                            </tr>
                        </tfoot>
                    
                </table>
            </td>
        </tr>
    </table>

    <!-- One Row Start -->
    <table width="800" cellpadding="0" cellspacing="0" align="left" style="margin-top:20px;">
    	<tr>
        	<td width="350" valign="top">
                <table width="350" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" align="left">
                	<thead>
                        <tr>
                            <th width="120">Actual EDF Liability</th>
                            <th width="120">Actual BTB LC Liability</th>
                            <th>Actual Total Liability</th>
                        </tr>
                    </thead>
                	
					<tbody>
						<tr bgcolor="#FFFFFF">
							<td align="right" style="font-size:18px; font-weight:bold;" title="<? echo "Bank Acc Value-EDF Paid Date Value";?>"><? if(number_format($ac_edf_grand_total,2,'.','')>0.00) echo number_format($ac_edf_grand_total,2); else echo "0.00"; ?></td>
							<td align="right" style="font-size:18px; font-weight:bold;" title="<? echo "Bank Acc Value-Payment Value";?>"><? if(number_format($ac_btb_grand_total,2,'.','')>0.00) echo number_format($ac_btb_grand_total,2); else echo "0.00"; ?></td>
							<td align="right" style="font-size:18px; font-weight:bold;"><? if(number_format($ac_grand_total,2,'.','')>0.00) echo number_format($ac_grand_total,2); else echo "0.00"; ?></td>
						</tr>
					</tbody>
                </table>
            </td>            
        </tr>
    </table>

    <table width="800" cellpadding="0" cellspacing="0" align="left" style="margin-top:20px;">
    	<tr>
        	<td width="350" valign="top">
                <table width="350" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" align="left">
                	<thead>
                        <tr>
                            <th colspan="3" style="font-size:16px; font-weight:bold">EDF Liability Month Wise Actual</th>
                        </tr>
                    </thead>
                		<thead>
                            <tr>
                                <th width="120">Month</th>
                                <th width="120">Local</th>
                                <th>Foreign</th>
                            </tr>
                        </thead>
                    	<?
						$k=1; 
						$i=$i+1;
						//echo "test<pre>";print_r($actual_edf_data);//die;
						foreach($actual_edf_data as $year=>$month_data)
						{
							ksort($month_data);
							foreach($month_data as $month_id=>$val)
							{
								if(number_format($val["local"],2)>=1 || number_format($val["foreign"],2) >=1)
								{
									$bgcolor=($k%2==0)?"#E9F3FF":"#FFFFFF";	 
									$i++;
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i?>','<? echo $bgcolor?>')" id="tr_<? echo $i; ?>" style="cursor:pointer">
										<td><? echo $months[abs($month_id)]."-".$year; ?></td>
										<td align="right" title="<? echo "Maturity Date Value-Paid Date Value"; ?>"><a href='#report_detals'  onclick= "openmypage_btb_ac_dtls('<? echo implode(",",array_unique(explode(",",chop($val["local_btb_lc_id"],",")))); ?>','<? echo $year; ?>','<? echo $month_id; ?>','btb_ac_popup','BTB Info','1')"><? echo number_format($val["local"],2); ?></a></td>
										<td align="right" title="<? echo "Maturity Date Value-Paid Date Value"; ?>"><a href='#report_detals'  onclick= "openmypage_btb_ac_dtls('<? echo implode(",",array_unique(explode(",",chop($val["foreign_btb_lc_id"],",")))); ?>','<? echo $year; ?>','<? echo $month_id; ?>','btb_ac_popup','BTB Info','2')"><? echo number_format($val["foreign"],2); ?></a></td>
									</tr>
									<?
									$k++;
									$actual_edf_local_tot+=$val["local"];
									$actual_edf_foreign_tot+=$val["foreign"];
								}
							}
						}
						?>
                        <tfoot>
                        	<tr>
                                <th align="right">Grand Total:</th>
                                <th align="right"><? if(number_format($actual_edf_local_tot,2,'.','')>0.00) echo number_format($actual_edf_local_tot,2); else echo "0.00";   ?></th>
                                <th align="right"><? if(number_format($actual_edf_foreign_tot,2,'.','')>0.00) echo number_format($actual_edf_foreign_tot,2); else echo "0.00"; ?></th>
                            </tr>
                        </tfoot>
                </table>
            </td>
            <td width="50"></td>
            <td  valign="top" width="350">
            	<table width="350" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" align="left">
                	<thead>
                        <tr>
                            <th colspan="3" style="font-size:16px; font-weight:bold">BTB Liability Month Wise Actual</th>
                        </tr>
                    </thead>
                		<thead>
                            <tr>
                                <th width="120">Month</th>
                                <th width="120">Local</th>
                                <th>Foreign</th>
                            </tr>
                        </thead>
                    	<?
						$k=1;
						$i=$i+1; 
						foreach($actual_btb_data as $year=>$month_data)
						{
							ksort($month_data);
							foreach($month_data as $month_id=>$val)
							{
								if(number_format($val["local"],2) >=1 || number_format($val["foreign"],2) >=1)
								{
									$bgcolor=($k%2==0)?"#E9F3FF":"#FFFFFF";
									$i++;
									?>
									<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i?>','<? echo $bgcolor?>')" id="tr_<? echo $i; ?>" style="cursor:pointer">
										<td><? echo $months[abs($month_id)]."-".$year; ?></td>
										<td align="right" title="<? echo "Maturity Date Value-Payment Value"; ?>"><a href='#report_detals'  onclick= "openmypage_btb_ac_dtls('<? echo implode(",",array_unique(explode(",",chop($val["local_btb_lc_id"],",")))); ?>','<? echo $year; ?>','<? echo $month_id; ?>','btb_ac_popup','BTB Info','3')"><? echo number_format($val["local"],2); ?></a></td>
										<td align="right" title="<? echo "Maturity Date Value-Payment Value"; ?>"><a href='#report_detals'  onclick= "openmypage_btb_ac_dtls('<? echo implode(",",array_unique(explode(",",chop($val["foreign_btb_lc_id"],",")))); ?>','<? echo $year; ?>','<? echo $month_id; ?>','btb_ac_popup','BTB Info','4')"><? echo number_format($val["foreign"],2); ?></a></td>
									</tr>
									<?
									$k++;
									$actual_btb_local_tot+=$val["local"];
									$actual_btb_foreign_tot+=$val["foreign"];
								}
							}
						}
						?>
                        <tfoot>
                        	<tr>
                                <th align="right">Grand Total:</th>
                                <th align="right" title="<? echo $gt_local_paid; ?>"><? if(number_format($actual_btb_local_tot,2,'.','')>0.00) echo number_format($actual_btb_local_tot,2); else echo "0.00"; ?></th>
                                <th align="right" title="<? echo $gt_foreign_paid; ?>"><? if(number_format($actual_btb_foreign_tot,2,'.','')>0.00) echo number_format($actual_btb_foreign_tot,2); else echo "0.00";  ?></th>
                            </tr>
                        </tfoot>
                    
                </table>
            </td>
        </tr>
    </table>
    <?
	$pending_edf_local=$lc_grand_local-$actual_edf_local_tot;
	$pending_edf_forein=$lc_grand_foreign-$actual_edf_foreign_tot;
	$pending_btb_local=$gt_btb_local-$actual_btb_local_tot;
	$pending_btb_forein=$gt_btb_foreign-$actual_btb_foreign_tot;
	$tot_pending_local=$pending_edf_local+$pending_btb_local;
	$tot_pending_forein=$pending_edf_forein+$pending_btb_forein;
	?>
    <table width="800" cellpadding="0" cellspacing="0" style="margin-top:20px;" class="rpt_table" border="1" rules="all" align="left">
    	<thead>
        	<tr>
            	<th colspan="2">Pending EDF Liability</th>
                <th colspan="2">Pending BTB LC Liability</th>
                <th colspan="2">Pending Total Liability</th>
                <th rowspan="2">Total</th>
            </tr>
            <tr>
                <th>Local</th>
                <th>Foreign</th>
                <th>Local</th>
                <th>Foreign</th>
                <th>Local</th>
                <th>Foreign</th>
            </tr>
        </thead>
        <tbody>
        	<tr bgcolor="#FFFFFF">
            	<td align="right" title="<? echo "BTB Value = $lc_grand_local - Bank Accep Value = $actual_edf_local_tot";?>"><a href='#report_detals' style="font-size:18px; font-weight:bold;" onclick= "openmypage_btb_dtls('<? echo $cbo_company_name; ?>','<? echo $cbo_lein_bank; ?>','<? echo $txt_lc_sc_date; ?>','btb_popup','BTB Info','5')"><? if(number_format($pending_edf_local,2,'.','')>0.00) echo number_format($pending_edf_local,2); else echo "0.00"; ?></a></td>
                <td align="right" title="<? echo "BTB Value-Bank Accep Value";?>"><a href='#report_detals' style="font-size:18px; font-weight:bold;" onclick= "openmypage_btb_dtls('<? echo $cbo_company_name; ?>','<? echo $cbo_lein_bank; ?>','<? echo $txt_lc_sc_date; ?>','btb_popup','BTB Info','6')"><? if(number_format($pending_edf_forein,2,'.','')>0.00) echo number_format($pending_edf_forein,2); else echo "0.00"; ?></a></td>
                <td align="right"  title="<? echo "BTB Value-Bank Accep Value";?>"><a href='#report_detals' style="font-size:18px; font-weight:bold;" onclick= "openmypage_btb_dtls('<? echo $cbo_company_name; ?>','<? echo $cbo_lein_bank; ?>','<? echo $txt_lc_sc_date; ?>','btb_popup','BTB Info','7')"><? if(number_format($pending_btb_local,2,'.','')>0.00) echo number_format($pending_btb_local,2); else echo "0.00"; ?></a></td>
                <td align="right" title="<? echo "BTB Value = ".$gt_btb_foreign."-Bank Accep Value = ".$actual_btb_foreign_tot ;?>"><a href='#report_detals' onclick= "openmypage_btb_dtls('<? echo $cbo_company_name; ?>','<? echo $cbo_lein_bank; ?>','<? echo $txt_lc_sc_date; ?>','btb_popup','BTB Info','8')" style="font-size:18px; font-weight:bold;"><? if(number_format($pending_btb_forein,2,'.','')>0.00) echo number_format($pending_btb_forein,2); else echo "0.00"; ?></a></td>
                <td align="right" style="font-size:18px; font-weight:bold;"><? if(number_format($tot_pending_local,2,'.','')>0.00) echo number_format($tot_pending_local,2); else echo "0.00"; ?></td>
                <td align="right" style="font-size:18px; font-weight:bold;"><? if(number_format($tot_pending_forein,2,'.','')>0.00) echo number_format($tot_pending_forein,2); else echo "0.00"; ?></td>
                <td align="right" style="font-size:18px; font-weight:bold;"><? $total_pending=$tot_pending_local+$tot_pending_forein; if(number_format($total_pending,2,'.','')>0.00) echo number_format($total_pending,2); else echo "0.00"; ?></td>
            </tr>
        </tbody>
    </table>

    <table width="800" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" align="left" style="margin-top:20px;">
        <thead>
            <tr>
                <th colspan="6" style="font-size:16px; font-weight:bold">Sales Contact</th>
            </tr>
        </thead>
        <thead>
            <tr>
                <th width="130">SC Value</th>
                <th width="130">SC Replace Value</th>
                <th width="130">Pending Value</th>
                <th width="130">SC Value(Direct)</th>
                <th width="130">LC Value(Direct)</th>
                <th>File Value</th>
            </tr>
        </thead>
        <tbody>
            <tr bgcolor="#FFFFFF">
                <td align="right" style="font-size:18px; font-weight:bold;"><? echo number_format($sc_value_1_3,2); ?></td>
                <td align="right" style="font-size:18px; font-weight:bold;"><? echo number_format($lc_value_1,2); ?></td>
                <td align="right" style="font-size:18px; font-weight:bold;"><? echo number_format(($sc_value_1_3-$lc_value_1),2); ?></td>
                <td align="right" style="font-size:18px; font-weight:bold;"><? echo number_format($sc_value_2,2); ?></td>
                <td align="right" style="font-size:18px; font-weight:bold;"><? echo number_format($lc_value_2,2); ?></td>
                <td align="right" style="font-size:18px; font-weight:bold;"><? echo number_format($file_value,2); ?></td>
            </tr>
        </tbody>
    </table>
<!-- End -->
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


if($action=="btb_ac_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');	
	extract($_REQUEST);
	//echo $btb_id."==".$type;die;
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$sql_payment="select invoice_id, payment_date, sum(accepted_ammount) as accepted_ammount from com_import_payment where status_active=1 and is_deleted=0 group by invoice_id, payment_date order by invoice_id";
	$sql_payment_result=sql_select($sql_payment);
	$lc_wise_payment=array();$invoice_wise_payment=array();
	foreach($sql_payment_result as $row)
	{
		$invoice_wise_payment[$row[csf("invoice_id")]]["payment_date"]=$row[csf("payment_date")];
		$invoice_wise_payment[$row[csf("invoice_id")]]["accepted_ammount"]+=$row[csf("accepted_ammount")];
	}
	
	if($db_type==0)
	{
		$year_month_cond=" and year(b.maturity_date)=$year_id and ABS(month(b.maturity_date))=$month_id";
	}
	else
	{
		$year_month_cond=" and to_char(b.maturity_date,'YYYY')=$year_id and ABS(to_char(b.maturity_date,'MM'))=$month_id";
	}
	$btb_sql="select a.id, a.importer_id, a.issuing_bank_id, a.supplier_id, a.lc_number, a.lc_category, a.lc_date, a.lc_expiry_date, a.lc_value, a.tenor, a.payterm_id, b.id as inv_id, b.invoice_no, b.bank_acc_date, b.maturity_date, sum(c.current_acceptance_value) as accep_value
	from com_btb_lc_master_details a, com_import_invoice_mst b, com_import_invoice_dtls c
	where a.id=c.btb_lc_id and c.import_invoice_id=b.id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.lc_category>0 and a.ref_closing_status<>1 and a.payterm_id <> 3 and a.id in($btb_id) $year_month_cond
	group by a.id, a.importer_id, a.issuing_bank_id, a.supplier_id, a.lc_number, a.lc_category, a.lc_date, a.lc_expiry_date, a.lc_value, a.tenor, a.payterm_id, b.id, b.invoice_no, b.bank_acc_date, b.maturity_date
	order by maturity_date";
	//echo $btb_sql;//die;
	$btb_sql_result=sql_select($btb_sql);
	?>
    <div style="width:870px; margin-left:30px" id="report_div">
         <div id="report_container"> </div>
    </div>
    <?    
	ob_start();
	?>
	<div id="" align="center" style="width:1230px">
	<fieldset style="width:1230px;">
		<table class="rpt_table" border="1" rules="all" width="1220" cellpadding="0" cellspacing="0">
			<thead>
				<th width="50">SL</th>
				<th width="110">BTB LC No</th>
				<th width="80">BTB LC Date</th>
				<th width="100">LC Value</th>
				<th width="120">Supplier Name</th>
				<th width="110">Invoice No</th>
				<th width="80">Bank Acceptance date</th>
				<th width="80">LC Tenor</th>
				<th width="80">Maturity Date</th>
				<th width="100">Invoice Value</th>
				<th width="80">Paid Date</th>
				<th width="100">Paid Value</th>
				<th>Balance</th>
			</thead>
			<tbody>
			<? 
			$i=1; $r=1;
			foreach($btb_sql_result as $row)  
			{
				$pending_value =$row[csf("accep_value")]-$invoice_wise_payment[$row[csf("inv_id")]]["accepted_ammount"];
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				?>
				<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
					<td align="center" title="<? echo $r; ?>"><? echo $i; ?></td>
					<td><p><? echo $row[csf('lc_number')]; ?>&nbsp;</p></td>
					<td align="center"><p><? if($row[csf('lc_date')]!="" && $row[csf('lc_date')]!="0000-00-00") echo change_date_format($row[csf('lc_date')]);?>&nbsp;</p></td>
					<td align="right"><? echo number_format($row[csf("lc_value")],2);  ?></td>
					<td><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?>&nbsp;</p></td>
					<td><p><? echo $row[csf('invoice_no')]; ?>&nbsp;</p></td>
					<td align="center"><p><? if($row[csf('bank_acc_date')]!="" && $row[csf('bank_acc_date')]!="0000-00-00") echo change_date_format($row[csf('bank_acc_date')]);?>&nbsp;</p></td>
					<td align="center"><p><? echo $row[csf('tenor')]; ?>&nbsp;</p></td>
					<td align="center"><p><? if($row[csf('maturity_date')]!="" && $row[csf('maturity_date')]!="0000-00-00") echo change_date_format($row[csf('maturity_date')]);?>&nbsp;</p></td>
					<td align="right"><? echo number_format($row[csf("accep_value")],2);  ?></td>
					<td align="center"><p><? if($invoice_wise_payment[$row[csf("inv_id")]]["payment_date"]!="" && $invoice_wise_payment[$row[csf("inv_id")]]["payment_date"]!="0000-00-00") echo change_date_format($invoice_wise_payment[$row[csf("inv_id")]]["payment_date"]);?>&nbsp;</p></td>
					<td align="right"><? echo number_format($invoice_wise_payment[$row[csf("inv_id")]]["accepted_ammount"],2);  ?></td>
					<td align="right"><p><? echo number_format($pending_value,2); ?>&nbsp;</p></td>
				</tr>
				<?
				$tot_inv_value+=$row[csf("accep_value")];
				$tot_paid_value+=$invoice_wise_payment[$row[csf("inv_id")]]["accepted_ammount"];
				$tot_bal_value+=$pending_value;
				$i++;
			}
			?>
			</tbody>
			<tfoot>
				<tr class="tbl_bottom">
					<td colspan="9" align="right">Total:</td>
					<td align="right" id="value_tot_pendin_value"><? echo number_format($tot_inv_value,2); ?></td>
					 <td>&nbsp;</td>
					<td align="right" id="value_tot_pendin_value"><? echo number_format($tot_paid_value,2); ?></td>
					<td align="right" id="value_tot_pendin_value"><? echo number_format($tot_bal_value,2); ?></td>
				</tr>
			</tfoot>
		</table>
	</fieldset>
	</div>
	<?
	$html=ob_get_contents();
	//ob_closed
	//ob_flush();
	foreach (glob("$user_id*.xls") as $filename) 
	{
		@unlink($filename);
	}
	//html to xls convert
	$name=time();
	$name=$user_id."_".$name.".xls";
	$create_new_excel = fopen(''.$name, 'w');	
	$is_created = fwrite($create_new_excel,$html);
	?>
    <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
    <script>
	$(document).ready(function(e) 
	{
		document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
	});	
	</script>
    <?
	die;
}


if($action=="btb_popup")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');	
	extract($_REQUEST);
	//echo $btb_id."==".$type;die;
	$supplier_arr = return_library_array("select id, supplier_name from lib_supplier","id","supplier_name");
	$sql_payment="select invoice_id, payment_date, sum(accepted_ammount) as accepted_ammount from com_import_payment where status_active=1 and is_deleted=0 and payment_date<='$txt_lc_sc_date' group by invoice_id, payment_date order by invoice_id";
	$sql_payment_result=sql_select($sql_payment);
	$lc_wise_payment=array();$invoice_wise_payment=array();
	foreach($sql_payment_result as $row)
	{
		$invoice_wise_payment[$row[csf("invoice_id")]]["payment_date"]=$row[csf("payment_date")];
		$invoice_wise_payment[$row[csf("invoice_id")]]["accepted_ammount"]+=$row[csf("accepted_ammount")];
	}
	
	//and a.id in($btb_id)
	$btb_inv_sql="select a.id, a.payterm_id, a.lc_category, b.id as inv_id, b.invoice_no, b.invoice_date, b.maturity_date, b.edf_paid_date, b.bank_acc_date, b.nagotiate_date, b.shipment_date, b.bill_date, a.maturity_from_id, sum(c.current_acceptance_value) as current_acceptance_value
	from com_btb_lc_master_details a, com_import_invoice_mst b, com_import_invoice_dtls c
	where a.id=c.btb_lc_id and c.import_invoice_id=b.id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and a.lc_category>0 and a.ref_closing_status<>1 and a.payterm_id <> 3 and a.lc_date <='$txt_lc_sc_date'
	group by a.id, a.payterm_id, a.lc_category, b.id, b.invoice_no, b.invoice_date, b.maturity_date, b.edf_paid_date, b.bank_acc_date, b.nagotiate_date, b.shipment_date, b.bill_date, a.maturity_from_id
	order by b.id";
	$btb_inv_result=sql_select($btb_inv_sql);
	$btb_inv_data=array();
	foreach($btb_inv_result as $row)
	{
		if($row[csf("maturity_from_id")]==1)
		$maturity_date=$row[csf("bank_acc_date")];
		else if($row[csf("maturity_from_id")]==2)
		$maturity_date=$row[csf("shipment_date")];
		else if($row[csf("maturity_from_id")]==3)
		$maturity_date=$row[csf("nagotiate_date")];
		else if($row[csf("maturity_from_id")]==4)
		$maturity_date=$row[csf("bill_date")];
		if(strtotime($maturity_date)<=strtotime($txt_lc_sc_date))
		{
			if($btb_inv_check[$row[csf("id")]][$row[csf("inv_id")]]=="")
			{
				$btb_inv_check[$row[csf("id")]][$row[csf("inv_id")]]=$row[csf("inv_id")];
				$btb_inv_data[$row[csf("id")]]["inv_id"].=$row[csf("inv_id")].",";
				$btb_inv_data[$row[csf("id")]]["invoice_no"].=$row[csf("invoice_no")].",";
				$btb_inv_data[$row[csf("id")]]["invoice_date"]=$row[csf("invoice_date")];
				$btb_inv_data[$row[csf("id")]]["bank_acc_date"]=$row[csf("bank_acc_date")];
				$btb_inv_data[$row[csf("id")]]["maturity_date"]=$row[csf("maturity_date")];
			}
			if(($row[csf("edf_paid_date")]!="" && $row[csf("edf_paid_date")]!="0000-00-00") || ($row[csf("maturity_date")]!="" && $row[csf("maturity_date")]!="0000-00-00"))
			{
				$btb_inv_data[$row[csf("id")]]["edf_paid_value"]+=$row[csf("current_acceptance_value")];
			}
			
			/*if($row[csf("payterm_id")]!=1 && $row[csf("maturity_date")]!="" && $row[csf("maturity_date")]!="0000-00-00")
			{
				if(abs($row[csf("lc_category")])==4)
				{
					$actual_btb_pop_data[$row[csf("id")]]["actual_btb_value"]+=$row[csf("current_acceptance_value")];
				}
				if(abs($row[csf("lc_category")])!=3 && abs($row[csf("lc_category")])!=4 && abs($row[csf("lc_category")])!=5 && abs($row[csf("lc_category")])!=11)
				{
					$actual_btb_pop_data[$row[csf("id")]]["actual_btb_value"]+=$row[csf("current_acceptance_value")];
				}
			}*/
			
			if(abs($row[csf("lc_category")])!=3 && abs($row[csf("lc_category")])!=5 && abs($row[csf("lc_category")])!=11 && $row[csf("maturity_date")]!="" && $row[csf("maturity_date")]!="0000-00-00")
			{
				$actual_btb_pop_data[$row[csf("id")]]["actual_btb_value"]+=$row[csf("current_acceptance_value")]-$invoice_wise_payment[$row[csf("inv_id")]]["accepted_ammount"];
			}
			
			$btb_inv_data[$row[csf("id")]]["current_acceptance_value"]+=$row[csf("current_acceptance_value")];
		}
	}
	//echo "<pre>";print_r($actual_btb_pop_data);die;
	$lc_cond="";
	if($type==5)
	{
		$lc_cond=" and b.payterm_id=1 and abs(b.lc_category)=3";
	}
	else if($type==6)
	{
		$lc_cond=" and b.payterm_id=1 and abs(b.lc_category) in(5,11)";
	}
	else if($type==7)
	{
		$lc_cond=" and abs(b.lc_category)=4";
	}
	else
	{
		$lc_cond=" and abs(b.lc_category) not in(3,4,5,11)";
	}
	
	/*$btb_sql="select b.id as btb_id, b.importer_id, b.issuing_bank_id, b.supplier_id, b.lc_number, b.lc_category, b.lc_date, b.lc_expiry_date, b.lc_value, b.tenor, b.payterm_id, 1 as type 
	from com_sales_contract p, com_btb_export_lc_attachment a, com_btb_lc_master_details b 
	where p.id=a.lc_sc_id and a.import_mst_id=b.id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.is_lc_sc=1 and b.ref_closing_status<>1 and p.beneficiary_name=$cbo_company_name and p.lien_bank=$cbo_lein_bank and b.lc_date<='$txt_lc_sc_date' and p.sc_year>'2015' $lc_cond
	group by b.id, b.importer_id, b.issuing_bank_id, b.supplier_id, b.lc_number, b.lc_category, b.lc_date, b.lc_expiry_date, b.lc_value, b.tenor, b.payterm_id
	union all 
	select b.id as btb_id, b.importer_id, b.issuing_bank_id, b.supplier_id, b.lc_number, b.lc_category, b.lc_date, b.lc_expiry_date, b.lc_value, b.tenor, b.payterm_id, 2 as type 
	from com_export_lc p, com_btb_export_lc_attachment a, com_btb_lc_master_details b 
	where p.id=a.lc_sc_id and a.import_mst_id=b.id and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and a.is_lc_sc=0 and b.ref_closing_status<>1  and p.beneficiary_name=$cbo_company_name and p.lien_bank=$cbo_lein_bank and b.lc_date<='$txt_lc_sc_date' and p.lc_year>'2015' $lc_cond
	group by  b.id, b.importer_id, b.issuing_bank_id, b.supplier_id, b.lc_number, b.lc_category, b.lc_date, b.lc_expiry_date, b.lc_value, b.tenor, b.payterm_id
	order by lc_date";*/
	
	$btb_sql="select b.id as btb_id, b.importer_id, b.issuing_bank_id, b.supplier_id, b.lc_number, b.lc_category, b.lc_date, b.lc_expiry_date, b.lc_value, b.tenor, b.payterm_id, 1 as type 
	from com_btb_lc_master_details b 
	where b.is_deleted=0 and b.status_active=1 and b.ref_closing_status<>1 and b.payterm_id <> 3 and b.importer_id=$cbo_company_name and b.issuing_bank_id=$cbo_lein_bank and b.lc_date<='$txt_lc_sc_date' $lc_cond and (b.lc_category<>0 or b.lc_category is not null)";
	
	//echo $btb_sql;die;
	
	/*foreach($btb_id_result as $row)
	{
		if($btb_check[$row[csf("btb_id")]]=="")
		{
			$pending_btb_value=$row[csf("lc_value")]-$lc_wise_aeecp[$row[csf("btb_id")]];
			$btb_check[$row[csf("btb_id")]]=$row[csf("btb_id")];
			$lc_date=add_date($row[csf("lc_date")],$row[csf("tenor")]);
			if($row[csf("payterm_id")]==1 && abs($row[csf("lc_category")])==3)
			{
				$lc_total_edf_data_arr[date('Y',strtotime($lc_date))][date('n',strtotime($lc_date))]["local"]+=$row[csf("lc_value")]-$lc_wise_edf_paid[$row[csf("btb_id")]];
				if($pending_btb_value>0)
				{
					$all_pending_edf_local.=$row[csf("btb_id")].",";
					$lc_total_edf_data_arr[date('Y',strtotime($lc_date))][date('n',strtotime($lc_date))]["local_btb_lc_id"].=$row[csf("btb_id")].",";
				}
			}
			
			if($row[csf("payterm_id")]==1  && (abs($row[csf("lc_category")])==5 || abs($row[csf("lc_category")])==11))
			{
				$lc_total_edf_data_arr[date('Y',strtotime($lc_date))][date('n',strtotime($lc_date))]["foreign"]+=$row[csf("lc_value")]-$lc_wise_edf_paid[$row[csf("btb_id")]];
				
				if($pending_btb_value>0)
				{
					$all_pending_edf_foreign.=$row[csf("btb_id")].",";
					$lc_total_edf_data_arr[date('Y',strtotime($lc_date))][date('n',strtotime($lc_date))]["foreign_btb_lc_id"].=$row[csf("btb_id")].",";
				}
				
			}
			
			if(abs($row[csf("lc_category")])==4)
			{
				foreach($invoce_data_arr[$row[csf("btb_id")]] as $inv_id=>$value)
				{
					$lc_total_btb_data_arr[date('Y',strtotime($lc_date))][date('n',strtotime($lc_date))]["local_paid"]+=$payment_data_array[$inv_id];
					$lc_local_paid+=$payment_data_array[$inv_id];
				}
				$lc_local_pending=$row[csf("lc_value")]-$lc_local_paid;
				$lc_total_btb_data_arr[date('Y',strtotime($lc_date))][date('n',strtotime($lc_date))]["local"]+=$row[csf("lc_value")]-$lc_local_paid;
				$lc_local_paid=0;
				if($lc_local_pending>0)
				{
					$all_pending_btb_local.=$row[csf("btb_id")].",";
					$lc_total_btb_data_arr[date('Y',strtotime($lc_date))][date('n',strtotime($lc_date))]["local_btb_lc_id"].=$row[csf("btb_id")].",";
				}
				
			}
			
			if(abs($row[csf("lc_category")])!=3 && abs($row[csf("lc_category")])!=4 && abs($row[csf("lc_category")])!=5 && abs($row[csf("lc_category")])!=11)
			{
				foreach($invoce_data_arr[$row[csf("btb_id")]] as $inv_id=>$value)
				{
					$lc_total_btb_data_arr[date('Y',strtotime($lc_date))][date('n',strtotime($lc_date))]["foreign_paid"]+=$payment_data_array[$inv_id];
					$lc_foreign_value+=$payment_data_array[$inv_id];
				}
				
				$lc_total_btb_data_arr[date('Y',strtotime($lc_date))][date('n',strtotime($lc_date))]["foreign"]+=$row[csf("lc_value")]-$lc_foreign_value;
				$lc_foreign_pending=$row[csf("lc_value")]-$lc_foreign_value;
				$lc_foreign_value=0;
				if($lc_foreign_pending>0)
				{
					$all_pending_btb_foreign.=$row[csf("btb_id")].",";
					$lc_total_btb_data_arr[date('Y',strtotime($lc_date))][date('n',strtotime($lc_date))]["foreign_btb_lc_id"].=$row[csf("btb_id")].",";
				}
			}
		}
	}*/
	
	/*$btb_sql="select a.id, a.importer_id, a.issuing_bank_id, a.supplier_id, a.lc_number, a.lc_category, a.lc_date, a.lc_expiry_date, a.lc_value, a.tenor, a.payterm_id  
	from com_btb_lc_master_details a 
	where a.status_active=1 and a.is_deleted=0 and a.lc_category>0  and a.ref_closing_status<>1 and a.id in($btb_id)";*/
	//echo $btb_sql;//die;
	$btb_sql_result=sql_select($btb_sql);
	?>
    <div style="width:870px; margin-left:30px" id="report_div">
         <div id="report_container"> </div>
    </div>
    <?
	ob_start();
	?>
	<div id="" align="center" style="width:1290px">
	<fieldset style="width:1290px;">
		<table class="rpt_table" border="1" rules="all" width="1290" cellpadding="0" cellspacing="0">
			<thead>
				<th width="30">SL</th>
				<th width="110">BTB LC No</th>
				<th width="80">BTB LC Date</th>
				<th width="100">LC Value</th>
				<th width="90">Pay Term</th>
				<th width="80">LC Tenor</th>
				<th width="120"> Supplier Name</th>
				<th width="80">Invoice Date</th>
				<th width="110">Invoice No</th>
				<th width="100">Invoice Value</th>
				<th width="80">Maturity Date</th>
				<th width="80">Paid Date</th>
				<th width="100">Paid Value</th>
				<th>Loan Liability</th>
			</thead>
			<tbody>
			<? 
			$i=1; $r=1;
			foreach($btb_sql_result as $row)  
			{
				//$pendin_value =$row[csf("lc_value")]-$accp_data[$row[csf("id")]];
				if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				$all_inv_id=explode(",",chop($btb_inv_data[$row[csf("btb_id")]]["inv_id"],","));
				$paid_date="";$paid_value=$loan_liability=0;
				foreach($all_inv_id as $inv_id)
				{
					$paid_date=$invoice_wise_payment[$inv_id]["payment_date"];
					$paid_value+=$invoice_wise_payment[$inv_id]["accepted_ammount"];
				}
				
				if($type==5 || $type==6)
				{
					$loan_liability=$row[csf("lc_value")]-$btb_inv_data[$row[csf("btb_id")]]["edf_paid_value"];
				}
				else
				{
					if($actual_btb_pop_data[$row[csf("btb_id")]]["actual_btb_value"]>0)
					{
						$loan_liability=$row[csf("lc_value")]-($actual_btb_pop_data[$row[csf("btb_id")]]["actual_btb_value"]+$paid_value);
						$tot_ac+=$actual_btb_pop_data[$row[csf("btb_id")]]["actual_btb_value"];
					}
					else
					{
						$loan_liability=$row[csf("lc_value")]-($paid_value);
					}
					$tot_btb+=$row[csf("lc_value")]-($paid_value);
					
					
				}
				if(number_format($loan_liability,2) != 0.00)
				{
					?>
                    <tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
                        <td align="center" title="<? echo $type."=".$row[csf("btb_id")]."=".$btb_inv_data[$row[csf("btb_id")]]["current_acceptance_value"]; ?>"><? echo $i; ?></td>
                        <td><p><? echo $row[csf('lc_number')]; ?>&nbsp;</p></td>
                        <td align="center"><p><? if($row[csf('lc_date')]!="" && $row[csf('lc_date')]!="0000-00-00") echo change_date_format($row[csf('lc_date')]);?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($row[csf("lc_value")],2);  ?></td>
                        <td align="center"><p><? echo $pay_term[$row[csf('payterm_id')]]; ?>&nbsp;</p></td>
                        <td align="center"><p><? echo $row[csf('tenor')]; ?>&nbsp;</p></td>
                        <td><p><? echo $supplier_arr[$row[csf('supplier_id')]]; ?>&nbsp;</p></td>
                        <td align="center"><p><?  if($btb_inv_data[$row[csf("btb_id")]]["invoice_date"]!="" && $btb_inv_data[$row[csf("btb_id")]]["invoice_date"]!="0000-00-00") echo change_date_format($btb_inv_data[$row[csf("btb_id")]]["invoice_date"]); ?>&nbsp;</p></td>
                        <td><p><? echo chop($btb_inv_data[$row[csf("btb_id")]]["invoice_no"],","); ?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($btb_inv_data[$row[csf("btb_id")]]["current_acceptance_value"],2);  ?></td>
                        <td align="center"><p><?  if($btb_inv_data[$row[csf("btb_id")]]["maturity_date"]!="" && $btb_inv_data[$row[csf("btb_id")]]["maturity_date"]!="0000-00-00") echo change_date_format($btb_inv_data[$row[csf("btb_id")]]["maturity_date"]); ?>&nbsp;</p></td>
                        <td align="center"><p><? if($paid_date!="" && $paid_date!="0000-00-00") echo change_date_format($paid_date);?>&nbsp;</p></td>
                        <td align="right"><? echo number_format($paid_value,2);  ?></td>
                        <td align="right"><? echo number_format($loan_liability,2);  ?></td>
                    </tr>
                    <?
                    $tot_lc_value+=$row[csf("lc_value")];
                    $tot_inv_value+=$btb_inv_data[$row[csf("btb_id")]]["current_acceptance_value"];
					$tot_liability+=$loan_liability;
                    $i++;
				}
			}
			?>
			</tbody>
			<tfoot>
				<tr class="tbl_bottom">
					<td colspan="3" align="right">Total:</td>
					<td align="right" id="value_tot_pendin_value"><? echo number_format($tot_lc_value,2); ?></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td align="right" id="value_tot_inv_value"><? echo number_format($tot_inv_value,2); ?></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
					<td align="right" id="value_tot_liability" title="<? echo $tot_btb."=".$tot_ac; ?>"><? echo number_format($tot_liability,2); ?></td>
				</tr>
			</tfoot>
		</table>
	</fieldset>
	</div>
	<?
	$html=ob_get_contents();
	//ob_closed
	//ob_flush();
	foreach (glob("$user_id*.xls") as $filename) 
	{
		@unlink($filename);
	}
	//html to xls convert
	$name=time();
	$name=$user_id."_".$name.".xls";
	$create_new_excel = fopen(''.$name, 'w');	
	$is_created = fwrite($create_new_excel,$html);
	?>
    <input type="hidden" id="txt_excl_link" value="<? echo 'requires/'.$name; ?>" />
    <script>
	$(document).ready(function(e) 
	{
		document.getElementById('report_container').innerHTML='<a href="<? echo $name?>" style="text-decoration:none"><input type="button" value="Excel Download" name="excel" id="excel" class="formbutton" style="width:90px;font-size:11px"/></a>&nbsp;&nbsp;';
	});	
	</script>
    <?
	die;
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
	//echo $type;die;
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
				$payment_data_array=return_library_array("select lc_id, sum(accepted_ammount) as paid_amt from  com_import_payment where status_active=1 and is_deleted=0 group by invoice_id","lc_id","paid_amt");
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
		
		//echo $btb_sql;
		
		
		//$payment_data_array=return_library_array("select invoice_id, sum(accepted_ammount) as paid_amt from com_import_payment where status_active=1 and is_deleted=0 group by invoice_id","invoice_id","paid_amt");
		
		
		
		
		$i=1;
        $sql_re=sql_select($btb_sql);
        $total_invoice_qty=0;  $total_order_qty=0;  $total_attach_qty=0;$result=0;
        
        foreach($sql_re as $row)
        {
			$lc_value=$row[csf('lc_value')]-$payment_data_array[$row[csf("btb_lc_sc_id")]];
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


/*if($action=="btb_paid_details")
{
	echo load_html_head_contents("Report Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	if($inv_id=="") die;
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
					com_btb_lc_master_details c, com_import_invoice_mst d, com_import_invoice_dtls e,  f
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
exit();
}
*/
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
