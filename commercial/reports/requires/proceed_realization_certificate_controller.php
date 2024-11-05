<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id = $_SESSION['logic_erp']["user_id"];


//--------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------


if ($action=="file_popup")
{

  	echo load_html_head_contents("Popup Info","../../../", 1, 1, $unicode);
	extract($_REQUEST);
	//echo $lien_bank;die;
?>
	<script>
		function js_set_value(str)
		{
			$("#hide_file_no").val(str);
			parent.emailwindow.hide();
		}
	</script>
	</head>
	<body>
	    <div style="width:530px">
	    <form name="search_order_frm"  id="search_order_frm">
	    <fieldset style="width:530px">
	        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" width="100%">
	            <thead>
	            	<th>Year</th>
	                <th>File No</th>
	                <th>
	                <input type="hidden" name="txt_company_id" id="txt_company_id" value="<?  echo $company_id; ?>"/>
	                <input type="hidden" name="hide_file_no" id="hide_file_no" value=""/>
	                </th>
	            </thead>
	            <tbody>

	                <tr class="general">
	                	<td>
	                    <?
						$sql=sql_select("select lc_year as lc_sc_year from com_export_lc where beneficiary_name='$company_id' and status_active=1 and is_deleted=0  union all select sc_year as lc_sc_year from com_sales_contract where beneficiary_name='$company_id' and status_active=1 and is_deleted=0");
						foreach($sql as $row)
						{
							$lc_sc_year[$row[csf("lc_sc_year")]]=$row[csf("lc_sc_year")];
						}
						echo create_drop_down( "cbo_year", 120,$lc_sc_year,"", 1, "-- Select --",$cbo_year);
						?>
	                    </td>
	                    <td align="center">
	                    <input type="text" name="txt_file_no" id="txt_file_no" class="text_boxes" style="width:160px" autocomplete=off />
	                    </td>
	                    <td>
	                    <input type="button" name="show" id="show" onClick="show_list_view(document.getElementById('cbo_year').value+'_'+document.getElementById('txt_file_no').value+'_'+<?  echo $company_id; ?>,'search_file_info','search_div_file','proceed_realization_certificate_controller','setFilterGrid(\'list_view\',-1)')" class="formbutton" style="width:100px;" value="Show" />
	                    </td>
	                </tr>
	            </tbody>
	        </table>
	        <table width="100%">


	            <tr>
	                <td>
	                <div style="width:560px; margin-top:5px" id="search_div_file" align="left"></div>
	                </td>
	            </tr>
	        </table>
	    </fieldset>
	    </form>
	    </div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
<?

	exit();

}

if ($action=="search_file_info")
{
	$ex_data = explode("_",$data);
	$buyer_name_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
	$lein_bank_arr=return_library_array( "select bank_name,id from lib_bank where is_deleted=0  and status_active=1 and lien_bank=1 order by bank_name",'id','bank_name');
	//print_r($ex_data);die;
	$cbo_year = trim(str_replace("'","",$ex_data[0]));
	$txt_file_no = trim(str_replace("'","",$ex_data[1]));
	$company_id = trim(str_replace("'","",$ex_data[2]));
	$sql_cond=$year_cond_sc=$year_cond_lc="";
	if($cbo_year>0)
	{
		$year_cond_sc="and sc_year='$cbo_year'";
		$year_cond_lc="and lc_year='$cbo_year'";
	}
	if($txt_file_no!="") $sql_cond .= " and internal_file_no ='$txt_file_no'";
    if($db_type == 0)
    {
        $sql = "select a.id, a.beneficiary_name, a.internal_file_no, a.lien_bank, a.buyer_name, a.lc_sc_year, group_concat(a.export_lc_no) as export_lc_no
        from (
             select id, beneficiary_name, internal_file_no, lien_bank, buyer_name, lc_year as lc_sc_year, group_concat(export_lc_no) as export_lc_no, 'export' as type
             from com_export_lc
             where beneficiary_name='$company_id' and status_active=1 and is_deleted=0 $year_cond_lc $sql_cond
             group by id, internal_file_no, lc_year, beneficiary_name, buyer_name, lien_bank
             union all
             select id, beneficiary_name, internal_file_no, lien_bank, buyer_name, sc_year as lc_sc_year, group_concat(contract_no) as export_lc_no, 'import' as type
             from com_sales_contract
             where beneficiary_name='$company_id' and status_active=1 and is_deleted=0 $year_cond_sc $sql_cond
             group by id,internal_file_no, sc_year, beneficiary_name, buyer_name , lien_bank
         ) a
         group by a.id, a.beneficiary_name, a.internal_file_no, a.lien_bank, a.buyer_name, a.lc_sc_year";
    }
    else
    {
        $sql = "select a.id, a.beneficiary_name, a.internal_file_no, a.lien_bank, a.buyer_name, a.lc_sc_year, listagg(cast(a.export_lc_no as varchar(4000)),',') within group(order by a.export_lc_no) as export_lc_no
        from (
             select id, beneficiary_name, internal_file_no, lien_bank, buyer_name, lc_year as lc_sc_year, listagg(cast(export_lc_no as varchar(4000)),',') within group(order by export_lc_no) as export_lc_no, 'export' as type
             from com_export_lc
             where beneficiary_name='$company_id' and status_active=1 and is_deleted=0 $year_cond_lc $sql_cond
             group by id, internal_file_no, lc_year, beneficiary_name, buyer_name, lien_bank
             union all
             select id, beneficiary_name, internal_file_no, lien_bank, buyer_name, sc_year as lc_sc_year, listagg(cast(contract_no as varchar(4000)),',') within group(order by contract_no) as export_lc_no, 'import' as type
             from com_sales_contract
             where beneficiary_name='$company_id' and status_active=1 and is_deleted=0 $year_cond_sc $sql_cond
             group by id, internal_file_no, sc_year, beneficiary_name, buyer_name, lien_bank
         ) a
         group by a.id, a.beneficiary_name, a.internal_file_no, a.lien_bank, a.buyer_name, a.lc_sc_year";
    }
	//echo $sql;
	?>
   <div style="width:560px">
    <form name="display_file"  id="display_file">
        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" width="100%">
            <thead>
                <th width="60">Sl NO.</td>
                <th width="80">File NO</td>
                <th width="80">Year</td>
                <th width="130"> Buyer</td>
                <th width="100"> Lien Bank</td>
                <th >SC/LC No.</td>
            </thead>
            </table>
        <table cellspacing="0" cellpadding="0" border="1" rules="all" class="rpt_table" align="center" width="100%" id="list_view">
            <tbody>
            <?
			$sll_result=sql_select($sql);
			$i=1;
			foreach($sll_result as $row)
			{
				if ($i%2==0)$bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
				//echo $row[csf("internal_file_no")];die;
				?>
                <tr bgcolor="<? echo $bgcolor; ?>"  onclick="js_set_value('<? echo $row[csf("internal_file_no")];?>,<? echo $row[csf("lc_sc_year")];?>,<? echo $row[csf("id")];?>')" id="search<? echo $row[csf("id")]; ?>" style="cursor:pointer">
                    <td align="center" width="60"> <? echo $i;?></td>
                    <td align="center" width="80"><p><? echo $row[csf("internal_file_no")]; ?>&nbsp;</p></td>
                    <td align="center" width="80"><p><? echo $row[csf("lc_sc_year")];  ?>&nbsp;</p></td>
                    <td width="130"><p><? echo $buyer_name_arr[$row[csf("buyer_name")]];  ?>&nbsp;</p></td>
                    <td width="100"><p><? echo $lein_bank_arr[$row[csf("lien_bank")]];  ?>&nbsp;</p></td>
                    <td><p><? echo $row[csf("export_lc_no")];  ?>&nbsp;</p></td>
                </tr>
                <?
				$i++;
			}
			?>
            </tbody>
            <input type="hidden" id="hide_file_no" name="hide_file_no"  />
        </table>
    </form>
    <script>setFilterGrid('list_view',-1)</script>
    </div>

        <?
}

/*$company_arr=return_library_array( "select id, company_name from lib_company",'id','company_name');
$buyer_name_arr=return_library_array( "select id, buyer_name from  lib_buyer",'id','buyer_name');
$lc_arr=return_library_array( "select id, export_lc_no from com_export_lc",'id','export_lc_no');
$sc_arr=return_library_array( "select id, contract_no from com_sales_contract",'id','contract_no');*/

//report generated here--------------------//
if($action=="generate_report")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	$con = connect();
	$cbo_company_name=str_replace("'","",$cbo_company_name);
	$txt_file_no=str_replace("'","",$txt_file_no);
	$txt_hide_year=str_replace("'","",$txt_hide_year);
	
	$po_sql="select a.total_set_qnty, b.id as po_id from wo_po_details_master a, wo_po_break_down b where a.job_no=b.job_no_mst and a.company_name=$cbo_company_name";
	$po_result=sql_select($po_sql);
	$po_data=array();
	foreach($po_result as $row)
	{
		$po_data[$row[csf("po_id")]]=$row[csf("total_set_qnty")];
	}
	unset($po_result);
	
	$sql_inv="select a.id as lc_sc_id, a.lien_bank, b.id as inv_id, b.exp_form_no, b.exp_form_date, b.bl_no, b.bl_date, b.invoice_no, b.invoice_date, c.po_breakdown_id, c.current_invoice_qnty, c.current_invoice_value
	from com_export_lc a, com_export_invoice_ship_mst b, com_export_invoice_ship_dtls c 
	where a.id=b.lc_sc_id and b.id=c.mst_id and b.is_lc=1 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.beneficiary_name=$cbo_company_name and a.lc_year='$txt_hide_year' and a.internal_file_no='$txt_file_no'
	union all
	select a.id as lc_sc_id, a.lien_bank, b.id as inv_id, b.exp_form_no, b.exp_form_date, b.bl_no, b.bl_date, b.invoice_no, b.invoice_date, c.po_breakdown_id, c.current_invoice_qnty, c.current_invoice_value
	from com_sales_contract a, com_export_invoice_ship_mst b, com_export_invoice_ship_dtls c 
	where a.id=b.lc_sc_id and b.id=c.mst_id and b.is_lc=2 and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.beneficiary_name=$cbo_company_name and a.sc_year='$txt_hide_year' and a.internal_file_no='$txt_file_no'";
	//echo $sql_inv;die;
	$inv_result=sql_select($sql_inv);
	$inv_data=array();
	$temp_table_id=return_field_value("max(id) as id","gbl_temp_report_id","1=1","id");
	if($temp_table_id=="") $temp_table_id=1;
	foreach($inv_result as $row)
	{
		$inv_data[$row[csf("inv_id")]]["exp_form_no"]=$row[csf("exp_form_no")];
		$inv_data[$row[csf("inv_id")]]["exp_form_date"]=$row[csf("exp_form_date")];
		$inv_data[$row[csf("inv_id")]]["bl_no"]=$row[csf("bl_no")];
		$inv_data[$row[csf("inv_id")]]["bl_date"]=$row[csf("bl_date")];
		$inv_data[$row[csf("inv_id")]]["invoice_no"]=$row[csf("invoice_no")];
		$inv_data[$row[csf("inv_id")]]["invoice_date"]=$row[csf("invoice_date")];
		$inv_data[$row[csf("inv_id")]]["current_invoice_qnty"]+=$row[csf("current_invoice_qnty")]*$po_data[$row[csf("po_breakdown_id")]];
		$inv_data[$row[csf("inv_id")]]["current_invoice_value"]+=$row[csf("current_invoice_value")];
		$bank_id=$row[csf("lien_bank")];
		if($inv_data_check[$row[csf("inv_id")]]=="")
		{
			$inv_data_check[$row[csf("inv_id")]]=$row[csf("inv_id")];
			$r_id=execute_query("insert into gbl_temp_report_id (id, ref_val, ref_from, user_id, ref_string) values ($temp_table_id,".$row[csf("inv_id")].",40,$user_id,'".$row[csf("invoice_no")]."')");
			if($r_id) $r_id=1; else {echo "insert into gbl_temp_report_id (id, ref_val, ref_from, user_id, ref_string) values ($temp_table_id,".$row[csf("inv_id")].",40,$user_id,'".$row[csf("invoice_no")]."')";oci_rollback($con);die;}
			$temp_table_id++;
		}
	}
	if($r_id) oci_commit($con);
	unset($inv_result);
	$realization_sql="select a.id as bill_dtls_id, a.invoice_id, a.net_invo_value, b.received_date, b.id as rlz_id, b.invoice_bill_id, b.is_invoice_bill, c.id as rlz_dtls_id, c.type as rlz_type, c.document_currency
	from com_export_doc_submission_invo a, com_export_proceed_realization b, com_export_proceed_rlzn_dtls c
	where a.doc_submission_mst_id=b.invoice_bill_id and b.id=c.mst_id and b.is_invoice_bill=1 and b.benificiary_id=$cbo_company_name and a.status_active=1 and a.is_deleted=0 and b.status_active=1 and b.is_deleted=0 and c.status_active=1 and c.is_deleted=0 and a.invoice_id in(select ref_val from gbl_temp_report_id)";
	//echo $realization_sql;die;
	$realization_result=sql_select($realization_sql);
	$realization_data=array();$rlz_bill_data=array();
	foreach($realization_result as $row)
	{
		$realization_data[$row[csf("invoice_id")]]["received_date"]=$row[csf("received_date")];
		if($rlz_dtls_check[$row[csf("rlz_dtls_id")]]=="" && $row[csf("rlz_type")]==1)
		{
			$rlz_dtls_check[$row[csf("rlz_dtls_id")]]=$row[csf("rlz_dtls_id")];
			$rlz_bill_data[$row[csf("invoice_bill_id")]]["document_currency"]+=$row[csf("document_currency")];
		}
		
		if($row[csf("is_invoice_bill")]==1)
		{
			if($rlz_inv_check[$row[csf("invoice_bill_id")]][$row[csf("invoice_id")]]=="")
			{
				$rlz_inv_check[$row[csf("invoice_bill_id")]][$row[csf("invoice_id")]]=$row[csf("invoice_id")];
				$rlz_bill_data[$row[csf("invoice_bill_id")]]["inv_id"].=$row[csf("invoice_id")].",";
				$inv_wise_amt[$row[csf("invoice_id")]]+=$row[csf("net_invo_value")];
			}
		}
		else
		{
			$rlz_bill_data[$row[csf("invoice_bill_id")]]["inv_id"]=$row[csf("invoice_bill_id")];
			$inv_wise_amt[$row[csf("invoice_id")]]+=$row[csf("net_invo_value")];
		}
		
		if($bill_dtls_check[$row[csf("bill_dtls_id")]]=="" && $row[csf("is_invoice_bill")]==1)
		{
			$bill_dtls_check[$row[csf("bill_dtls_id")]]=$row[csf("bill_dtls_id")];
			$bill_wise_amt[$row[csf("invoice_bill_id")]]+=$row[csf("net_invo_value")];
		}
	}
	unset($realization_result);
	//echo "<pre>";print_r($rlz_bill_data); "<pre>";print_r($inv_wise_amt);die;
	$inv_wise_rlz_amt=array();
	foreach($rlz_bill_data as $bill_id=>$bill_value)
	{
		$inv_ids=explode(",",chop($bill_value["inv_id"],","));
		if(count($inv_ids)>1)
		{
			$rlz_amount=$rlz_bill_data[$bill_id]["document_currency"];
			$bill_amount=$bill_wise_amt[$bill_id];
			//echo $rlz_amount."=".$bill_amount;die;
			$number_of_inv=count($inv_ids);
			foreach($inv_ids as $invoice_id)
			{
				$inv_wise_rlz_amt[$invoice_id]=number_format((($rlz_amount*$inv_wise_amt[$invoice_id])/$bill_amount),2,'.','');
			}
		}
		else
		{
			$inv_wise_rlz_amt[chop($bill_value["inv_id"],",")]=$rlz_bill_data[$bill_id]["document_currency"];
		}
	}
	//echo "<pre>";print_r($inv_wise_rlz_amt);die;
	$tr_str=execute_query("delete from gbl_temp_report_id where user_id=$user_id");
	if($tr_str) oci_commit($con);
	$bank_sql=sql_select("select id, bank_name, address from lib_bank where id=$bank_id");
	$company_sql=sql_select("select id, company_name, city from lib_company where id=$cbo_company_name");
	ob_start();	
	if(count($inv_data)<1) {echo '<p style="font-size:14px; font-weight:bold; color:red;">NO DATA FOUND </p>'; die;}
	?>
    <style type='text/css' media="print">
	thead { display:table-header-group; }
	tfoot { display:table-footer-group; }
	tr.page-break  { page-break-after: always;} 
	</style>
	<div style="width:1050px;"> 
        <table width="1050" border="1" cellpadding="0" cellspacing="0" class="rpt_table" rules="all" id="table_header" align="left">
            <thead>
                <tr style="border:none;"> 
                    <td colspan="13" align="center" style="border:none; font-size:14px;"> BANK NAME : <? echo strtoupper($bank_sql[0][csf("bank_name")]); ?></td>
                </tr>
                <tr style="border:none;">
                    <td colspan="13" align="center" style="border:none; font-size:14px;"> BANK ADDERESS : <? echo strtoupper($bank_sql[0][csf("address")]); ?></td>
                </tr>
                <tr class="form_caption" style="border:none;">
                    <td colspan="13" align="center" style="border:none;font-size:16px; font-weight:bold" ><? echo strtoupper($report_title); ?> </td> 
                </tr>
                <tr style="border:none;">
                    <td colspan="13" align="center" style="border:none; font-size:14px;"> NAME AND ADDRESS OF THE EXPORTER : <? echo strtoupper($company_sql[0][csf("company_name")]); ?></td>
                </tr>
                <tr style="border:none;">
                    <td colspan="13" align="center" style="border:none; font-size:14px;"><? echo strtoupper($company_sql[0][csf("city")]); ?></td>
                </tr>
                <tr style="border:none;">
                    <td colspan="13" align="center" style="border:none; font-size:14px;">DATE: &nbsp;&nbsp; FROM &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; TO </td>
                </tr>
                <tr>
                    <td colspan="13" align="center" style="border:none; font-size:14px;">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="2">CHAPTER 8</td>
                    <td align="center">PARA 22</td>
                    <td style="border:none;" colspan="7">&nbsp;</td>
                    <td>APP.5</td>
                    <td align="right">23</td>
                    <td style="border:none;">&nbsp;</td>
                </tr>
                <tr style="border:none;">
                	<td style="border:none;" colspan="13">&nbsp;</td>
                </tr>
                <tr>
                    <td style="border:none;" colspan="2">PRC NO.</td>
                    <td style="border:none;" colspan="2">&nbsp;</td>
                    <td style="border:none;">FBC NO.</td>
                    <td style="border:none;" colspan="8">&nbsp;</td>
                </tr>
                <tr style="border:none;">
                	<td style="border:none;" colspan="13">&nbsp;</td>
                </tr>
                <tr bgcolor="#CCCCCC">
                    <td width="20" rowspan="2" align="center">SL</td>
                    <td width="80" rowspan="2" align="center">EXPORT FROM NO & DATE</td>
                    <td width="80" rowspan="2" align="center">DESCRIPTION OF COMMODITY EXPORTED</td>
                    <td width="80" rowspan="2" align="center">B/L NO & DATE AND CONTRY OF DESTINATION</td>
                    <td width="80" align="center">INVOICE NO.</td>
                    <td width="160" colspan="2" align="center">AMOUNT REALIZED</td>
                    <td width="70" rowspan="2" align="center">DATE OF REALIZATION</td>
                    <td width="60" align="center">1. FRIGHT PREPAID 2. COMMISSION</td>
                    <td width="160" colspan="2" align="center">NET F.O.B</td>
                    <td width="100" rowspan="2" align="center">DATE OF SUBMISSION OF TRIPLICATE / COPIES TO THE CONTROL</td>
                    <td rowspan="2" align="center">REFERENCE OF THE SCHEDULE STATEMENT IN WHICH TRANSACTION HAS BEEN OR WILL BE REPORTER TO THE CONTROL</td>
                </tr>
                <tr bgcolor="#CCCCCC">
                	<td width="80" align="center">INVOICE DATE & VALUE USD</td>
                    <td width="80" align="center">FOREIGN CORRENCY USD</td>
                    <td width="80" align="center">BANGLADESHI TAKA</td>
                    <td width="60" align="center">3. INSURANCE</td>
                    <td width="80" align="center">FOREIGN CORRENCY USD</td>
                    <td width="80" align="center">BANGLADESHI TAKA</td>
                </tr>
            </thead>
            <tbody>
            <?
			$i=1;
			foreach($inv_data as $inv_id=>$val)
			{
				if ($i%2==0)
				$bgcolor="#E9F3FF";
				else
					$bgcolor="#FFFFFF";
				if($i%8==0)
				{
					$tr_class='class="page-break"';
				}
				else
				{
					$tr_class='';
				}
				?>
            	<tr bgcolor="<? echo $bgcolor; ?>" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>" <? echo $tr_class;?>>
                	<td align="center"><? echo $i; ?></td>
                    <td align="center"><p><? echo $val["exp_form_no"]."<br>"; if($val["exp_form_date"]!="" && $val["exp_form_date"]!="0000-00-00") echo change_date_format($val["exp_form_date"]); ?>&nbsp;</p></td>
                    <td align="center" title="<? echo $inv_id ."==". $val["current_invoice_qnty"]; ?>"><p><? echo "T-SHIRT <br>"; echo number_format($val["current_invoice_qnty"],0)." Pcs"; ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $val["bl_no"]."<br>"; if($val["bl_date"]!="" && $val["bl_date"]!="0000-00-00") echo change_date_format($val["bl_date"]); ?>&nbsp;</p></td>
                    <td align="center"><p><? echo $val["invoice_no"]."<br>"; if($val["invoice_date"]!="" && $val["invoice_date"]!="0000-00-00") echo change_date_format($val["invoice_date"])."<br>"; echo number_format($val["current_invoice_value"],2); ?>&nbsp;</p></td>
                    <td align="right">
					<? 
					if($realization_data[$inv_id]["received_date"]!="" && $realization_data[$inv_id]["received_date"]!="0000-00-00")
					{
						echo number_format($inv_wise_rlz_amt[$inv_id],2);
					}
					//echo number_format($val["current_invoice_value"],2); 
					?></td>
                    <td></td>
                    <td align="center"><? if($realization_data[$inv_id]["received_date"]!="" && $realization_data[$inv_id]["received_date"]!="0000-00-00") echo change_date_format($realization_data[$inv_id]["received_date"]); ?></td>
                    <td align="center">-DO-</td>
                    <td align="right"><? echo number_format($val["current_invoice_value"],2); ?></td>
                    <td></td>
                    <td align="center">
					<? 
					if($realization_data[$inv_id]["received_date"]!="" && $realization_data[$inv_id]["received_date"]!="0000-00-00")
					{
						echo date('M',strtotime($realization_data[$inv_id]["received_date"]))."-".date('Y',strtotime($realization_data[$inv_id]["received_date"]));
					}
					?></td>
                    <td>Schedule A-1/01 Stat-S-1</td>
                </tr>
                <?
				$i++;
			}
			?>	
            </tbody>
            <tfoot>
            	<tr style="border:none;">
                	<td colspan="13" style="border:none;">&nbsp;</td>
                </tr>
                <tr style="border:none;">
                	<td colspan="13" style="border:none;">&nbsp;</td>
                </tr>
            	<tr style="border:none;">
                	<td colspan="13" style="border:none;">WE HERE BY CERTIFY THAT THE PARTICULARS MENTIONED ABOVE ARE CORRECT. WE ALSO UNDERTAKE THAT IN CASE ANY DISCREPANCY IN REGARDS TO THE TRANSACTIONS MENTIONED IS THIS CERTIFICATE IN DETECTED. WE SHALL REMAIN RESPONSIBLE FOR THE SAME AND SHALL ABIDE BY ANY DECISION TAKEN BY THE BANGLADESH BANK IN THIS REGARD. WE FURTHER UNDERTAKE IF ANY INSURANCE AND OTHER EXPORT CLAIMS RELATING TO THE EXPORT INDICATED IN THE PROCEEDS REALIZATION CERTIFICATE ARISE WE SHALL ABIDE ANY DECISION OF BANGLADESH BANK FOR DEDUCTION OF APPROPRIATE AMOUNT FROM ANY FUTURE PROCEEDS REALIZATION CERTIFICATE. WE WILL SEND THE VERIFICATION EXP OF THE PRC TO BANGLADESH BANK FOR POST FACTO CHECKING IN THE MONTH OF.........................</td>
                </tr>
                <tr style="border:none;">
                	<td colspan="13" style="border:none;">&nbsp;</td>
                </tr>
                <tr style="border:none;">
                	<td colspan="13" style="border:none;">&nbsp;</td>
                </tr>
                <tr style="border:none;">
                	<td colspan="10" align="left" style="text-align:left !important; border:none;">
                    SIGNATURE OF THE HEAD OF THE BRANCH <br>
                    FULL NAMDESIGNATION <br>
                    PA NO <br>
                    PHONE:
                    </td>
                    <td colspan="3" align="left" style="text-align:left !important; border:none;">
                    SIGNATURE OF ISSUING OFFICER <br>
                    FULL NAME <br>
                    DESIGNATION <br>
                    PA NOPHONE:
                    </td>
                </tr>
            </tfoot>
        </table> 
   </div>    
	<?	 
	$html = ob_get_contents();
	ob_clean();
	$new_link=create_delete_report_file( $html, 2, $delete, "../../../" );
	foreach (glob("$user_id*.xls") as $filename) {
	if( @filemtime($filename) < (time()-$seconds_old) )
	@unlink($filename);
	}
	//---------end------------//
	$name=time();
	$filename=$user_id."_".$name.".xls";
	$create_new_doc = fopen($filename, 'w');	
	$is_created = fwrite($create_new_doc, $html);
	echo "$html**$filename"; 
	exit();
	disconnect($con);
}
?>

