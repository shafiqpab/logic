<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
require_once('../../../includes/common.php');

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];

if($action=="report_generate")
{ 
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$cbo_lein_bank=str_replace("'","",$cbo_lein_bank); 
	
	$sql_condition="";
	if($cbo_company_name>0) $sql_condition =" and b.beneficiary_name='$cbo_company_name' ";
	if($cbo_lein_bank >0) $sql_condition .= " and b.lien_bank='$cbo_lein_bank'";
	
	
	
	$sql=sql_select("SELECT b.id as lc_id,  b.export_lc_no as export_lc_no, b.lc_value as lc_value,b.buyer_name, b.replacement_lc, 1 as type
				FROM  com_export_lc b
				WHERE b.status_active=1 and b.is_deleted=0  $sql_condition
				union all
				SELECT b.id as lc_id,  b.contract_no  as export_lc_no, b.contract_value  as lc_value,b.buyer_name, null as replacement_lc, 2 as type
				FROM com_sales_contract b 
				WHERE  b.status_active=1 and b.is_deleted=0 $sql_condition");

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
	
	$sql_rlz=" SELECT b.invoice_id, 1 as type
		from  com_export_doc_submission_invo b,  com_export_proceed_realization d
		where b.doc_submission_mst_id=d.invoice_bill_id and b.is_lc=1 and b.lc_sc_id in(".$lc_id_group.") and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0
		union all
		SELECT b.invoice_id, 2 as type
		from  com_export_doc_submission_invo b,  com_export_proceed_realization d
		where b.doc_submission_mst_id=d.invoice_bill_id and b.is_lc=2 and b.lc_sc_id in(".$sc_id_group.") and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0";
	$sql_rlz_result=sql_select($sql_rlz);
	foreach($sql_rlz_result as $row)
	{
		$unrealized_inv_id[$row[csf("invoice_id")]]=$row[csf("invoice_id")];
	}
	
	//var_dump($unrealized_inv_id);die;
	
	//$unrealized_inv_id[$row[csf("invoice_id")]]=$row[csf("invoice_id")];
		
	/*$sql_sub="SELECT b.invoice_id, b.net_invo_value as bill_value, a.possible_reali_date
		FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b
		WHERE b.doc_submission_mst_id=a.id and b.is_lc=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=40 and b.lc_sc_id in($lc_id_group) and a.id not in( SELECT b.doc_submission_mst_id
		from  com_export_doc_submission_invo b,  com_export_proceed_realization d
		where b.doc_submission_mst_id=d.invoice_bill_id and b.is_lc=1 and b.lc_sc_id in(".$lc_id_group.") and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0)
		union all
		SELECT b.invoice_id, b.net_invo_value as bill_value, a.possible_reali_date
		FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b
		WHERE b.doc_submission_mst_id=a.id and b.is_lc=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=40 and b.lc_sc_id in($lc_id_group) and a.id not in( SELECT b.doc_submission_mst_id
		from  com_export_doc_submission_invo b,  com_export_proceed_realization d
		where b.doc_submission_mst_id=d.invoice_bill_id and b.is_lc=2 and b.lc_sc_id in(".$sc_id_group.") and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0)";*/
		
	if($db_type==0)
	{
		$sql_sub="SELECT group_concat(b.invoice_id) as invoice_id, sum(b.net_invo_value) as bill_value, a.id, a.bank_ref_no, a.possible_reali_date
		FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b
		WHERE b.doc_submission_mst_id=a.id and b.is_lc=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=40 and b.lc_sc_id in($lc_id_group) and a.id not in( SELECT b.doc_submission_mst_id
		from  com_export_doc_submission_invo b,  com_export_proceed_realization d
		where b.doc_submission_mst_id=d.invoice_bill_id and b.is_lc=1 and b.lc_sc_id in(".$lc_id_group.") and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0) group by  a.id, a.bank_ref_no, a.possible_reali_date
		union all
		SELECT group_concat(b.invoice_id) as invoice_id, sum(b.net_invo_value) as bill_value, a.id, a.bank_ref_no, a.possible_reali_date
		FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b
		WHERE b.doc_submission_mst_id=a.id and b.is_lc=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=40 and b.lc_sc_id in($lc_id_group) and a.id not in( SELECT b.doc_submission_mst_id
		from  com_export_doc_submission_invo b,  com_export_proceed_realization d
		where b.doc_submission_mst_id=d.invoice_bill_id and b.is_lc=2 and b.lc_sc_id in(".$sc_id_group.") and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0) group by a.id, a.bank_ref_no, a.possible_reali_date";
	}
	else
	{
		$sql_sub="SELECT listagg(cast(b.invoice_id as varchar(4000)),',') within group(order by b.invoice_id) as invoice_id, sum(b.net_invo_value) as bill_value, a.id, a.bank_ref_no, a.possible_reali_date
		FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b
		WHERE b.doc_submission_mst_id=a.id and b.is_lc=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=40 and b.lc_sc_id in($lc_id_group) and a.id not in( SELECT b.doc_submission_mst_id
		from  com_export_doc_submission_invo b,  com_export_proceed_realization d
		where b.doc_submission_mst_id=d.invoice_bill_id and b.is_lc=1 and b.lc_sc_id in(".$lc_id_group.") and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0) group by  a.id, a.bank_ref_no, a.possible_reali_date
		union all
		SELECT listagg(cast(b.invoice_id as varchar(4000)),',') within group(order by b.invoice_id) as invoice_id, sum(b.net_invo_value) as bill_value, a.id, a.bank_ref_no, a.possible_reali_date
		FROM com_export_doc_submission_mst a, com_export_doc_submission_invo b
		WHERE b.doc_submission_mst_id=a.id and b.is_lc=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and a.entry_form=40 and b.lc_sc_id in($lc_id_group) and a.id not in( SELECT b.doc_submission_mst_id
		from  com_export_doc_submission_invo b,  com_export_proceed_realization d
		where b.doc_submission_mst_id=d.invoice_bill_id and b.is_lc=2 and b.lc_sc_id in(".$sc_id_group.") and b.status_active=1 and b.is_deleted=0 and d.status_active=1 and d.is_deleted=0) group by a.id, a.bank_ref_no, a.possible_reali_date";
	}
		
	
	//echo $sql_sub;//die;
	
	$sql_sub_result=sql_select($sql_sub);
	$unrealized_value="";$monthly_possiable_rlz_amt=array();
	foreach($sql_sub_result as $row)
	{
		$unrealized_inv_id[$row[csf("invoice_id")]]=$row[csf("invoice_id")];
		$unrealized_value+=$row[csf("bill_value")];
		if($row[csf("possible_reali_date")]!="" && $row[csf("possible_reali_date")]!="0000-00-00")
		{
			$monthly_possiable_rlz_amt[date('Y',strtotime($row[csf("possible_reali_date")]))][date('n',strtotime($row[csf("possible_reali_date")]))]+=$row[csf("bill_value")];
		}
		
		
	}
	
	//echo "<pre>";print_r($monthly_possiable_rlz_amt);die;
	
	$sql_unsubmit_lc= "SELECT a.id, a.net_invo_value, a.bl_date, 1 as type 
	from com_export_invoice_ship_mst a where a.is_lc=1 and a.status_active=1 and a.is_deleted=0 and a.lc_sc_id in($lc_id_group)";
	$p=1;
	if(!empty($unrealized_inv_id))
	{
		$invoice_id_lc_arr=array_chunk($unrealized_inv_id,999);
		foreach($invoice_id_lc_arr as $invoice_id_lc)
		{
			if($p==1) $sql_unsubmit_lc .="and (a.id not in(".implode(',',$invoice_id_lc).")"; else  $sql_unsubmit_lc .=" and a.id not in(".implode(',',$invoice_id_lc).")";
			
			$p++;
		}
		$sql_unsubmit_lc .=")";
		$p++;
	}
	
	//echo $sql_unsubmit_lc."<br>";//die;
	
	$sql_unsubmit_lc_result=sql_select($sql_unsubmit_lc);
	$un_submit_value=$on_bord_value=$on_bord_pending_value=0;
	foreach($sql_unsubmit_lc_result as $row)
	{
		$un_submit_value+=$row[csf("net_invo_value")];
		if($row[csf("bl_date")]!="" && $row[csf("bl_date")]!="0000-00-00")
		{
			$on_bord_value+=$row[csf("net_invo_value")];
		}
		else
		{
			$on_bord_pending_value+=$row[csf("net_invo_value")];
		}
	}
	
	//echo $un_submit_value."==".$on_bord_value."==".$on_bord_pending_value."<br>";
	
	$sql_unsubmit_sc= "SELECT a.id, a.net_invo_value, a.bl_date, 2 as type 
	from com_export_invoice_ship_mst a where a.is_lc=2 and a.status_active=1 and a.is_deleted=0 and a.lc_sc_id in($sc_id_group)";
	$p=1;
	if(!empty($unrealized_inv_id))
	{
		$invoice_id_lc_arr=array_chunk($unrealized_inv_id,999);
		foreach($invoice_id_lc_arr as $invoice_id_lc)
		{
			if($p==1) $sql_unsubmit_sc .="and (a.id not in(".implode(',',$invoice_id_lc).")"; else  $sql_unsubmit_sc .=" and a.id not in(".implode(',',$invoice_id_lc).")";
			
			$p++;
		}
		$sql_unsubmit_sc .=")";
		$p++;
	}
	
	//echo $sql_unsubmit_sc."<br>";//die;
	
	$sql_unsubmit_sc_result=sql_select($sql_unsubmit_sc);
	foreach($sql_unsubmit_sc_result as $row)
	{
		$un_submit_value+=$row[csf("net_invo_value")];
		if($row[csf("bl_date")]!="" && $row[csf("bl_date")]!="0000-00-00")
		{
			$on_bord_value+=$row[csf("net_invo_value")];
		}
		else
		{
			$on_bord_pending_value+=$row[csf("net_invo_value")];
		}
	}
	$export_amt=$unrealized_value+$un_submit_value;
	
	$bank_arr=return_library_array( "select bank_name,id from lib_bank",'id','bank_name');
	ob_start();
?>
<div style="width:710px;" id="scroll_body">
<fieldset style="width:100%">
	<p style="font-size:16px; font-weight:bold">Summary</p>
	<p style="font-size:16px; font-weight:bold"><? echo "Bank name: ". $bank_arr[$cbo_lein_bank];?></p>
    
    <?
	
	/*foreach($lc_total_edf_data_arr as $year=>$month_data)
	{
		if($year==$hide_year)
		{
			foreach($month_data as $month_id=>$val)
			{
				
				$edf_grand_total+=$val["local"]+$val["foreign"];
			}
		}
		
		if($year<$hide_year)
		{
			foreach($month_data as $month_id=>$val)
			{
				
				$edf_grand_total+=$val["local"]+$val["foreign"];
				$prev_edf_local_balance+=$val["local"];
				$prev_edf_foreign_balance+=$val["foreign"];
			}
		}
		
	}
	
	
	foreach($lc_total_btb_data_arr as $year=>$month_data)
	{
		if($year==$hide_year)
		{
			foreach($month_data as $month_id=>$val)
			{
				
				$btb_grand_total+=$val["local"]+$val["foreign"];
				
			}
		}
		
		if($year<$hide_year)
		{
			foreach($month_data as $month_id=>$val)
			{
				
				$btb_grand_total+=$val["local"]+$val["foreign"];
				$prev_btb_local_balance+=$val["local"];
				$prev_btb_foreign_balance+=$val["foreign"];
				
			}
		}
	}
	
	$grand_total=$edf_grand_total+$btb_grand_total;*/
	
	?>
    <table width="700" cellpadding="0" cellspacing="0" align="left" style="margin-top:20px;">
    	<tr>
        	<td width="300" valign="top">
            	<p style="font-size:16px; font-weight:bold">Export Document Summery</p>
                <table width="300" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" align="left">
                
                		<thead>
                            <tr>
                                <th width="150">As Of Today</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                        	<tr bgcolor="#E9F3FF">
                                <td>Export Amount</td>
                                <td align="right" title="<? echo "Un-realized value+Un-submit value"; ?>"><? echo number_format($export_amt,2); ?></td>
                            </tr>
                            <tr bgcolor="#FFFFFF">
                                <td>Un-Realization Amount</td>
                                <td align="right"><? echo number_format($unrealized_value,2); ?></td>
                            </tr>
                            <tr bgcolor="#E9F3FF">
                                <td>Un-Submitted Amount</td>
                                <td align="right"><? echo number_format($un_submit_value,2); ?></td>
                            </tr>
                            <tr bgcolor="#FFFFFF">
                                <td>On Board Amount</td>
                                <td align="right"  title="<? echo "With Bl Date of Bill Amount"; ?>"><? echo number_format($on_bord_value,2); ?></td>
                            </tr>
                            <tr bgcolor="#FFFFFF">
                                <td>On Board Pending Amount</td>
                                <td align="right" title="<? echo "Without Bl Date of Bill Amount"; ?>"><? echo number_format($on_bord_pending_value,2); ?></td>
                            </tr>
                        </tbody>
                </table>
            </td>
            <td width="100"></td>
            <td  valign="top" width="300">
            	<p style="font-size:16px; font-weight:bold">Monthly Possible Realization Amount</p>
            	<table width="300" cellpadding="0" cellspacing="0" class="rpt_table" border="1" rules="all" align="left">
                
                    	<?
						$i=1;$k=1;
						foreach($monthly_possiable_rlz_amt as $year=>$month_data)
						{
							?>
                            <thead>
                                <tr>
                                    <th width="150">Year-<? echo $year; ?></th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <?
							ksort($month_data);
							//print_r($month_data);echo "<br>";
							foreach($month_data as $month_id=>$val)
							{
								$bgcolor=($k%2==0)?"#E9F3FF":"#FFFFFF";
								?>
								<tr bgcolor="<? echo $bgcolor; ?>" onclick="change_color('tr_<? echo $i?>','<? echo $bgcolor?>')" id="tr_<? echo $i; ?>" style="cursor:pointer">
									<td><? echo $months[abs($month_id)]; ?></td>
									<td align="right" title="<? echo $val; ?>"><? echo number_format($val,2); ?></td>
								</tr>
								<?
								$i++;$k++;
								$gt_possiable_amt+=$val;
								
							}
						}
						?>
                        
                        <tfoot>
                        	<tr>
                                <th align="right">Grand Total:</th>
                                <th align="right" title="<? echo $gt_possiable_amt; ?>"><? echo number_format($gt_possiable_amt,2); ?></th>
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


if($action=="btb_paid_details")
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
