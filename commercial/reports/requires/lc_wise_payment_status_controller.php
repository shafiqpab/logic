<?
header('Content-type:text/html; charset=utf-8');
session_start();
include('../../../includes/common.php');

$user_id = $_SESSION['logic_erp']["user_id"];
if( $_SESSION['logic_erp']['user_id'] == "" ) { header("location:login.php"); die; }
$permission=$_SESSION['page_permission'];

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="lc_sc_popup")
{
	echo load_html_head_contents("LC/SC Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	//echo $txt_hidden_pi_id;die;
	?>

	<script>
		function js_set_value(id)
		{
			//alert(id);
			document.getElementById('selected_id').value=id;
			parent.emailwindow.hide();
		}

		function fn_show_lc_sc()
		{
			if(form_validation('cbo_buyer_name','Buyer Name')==false )
			{
				return;
			}
			else
			{
				show_list_view (<? echo $company_id; ?>+'**'+<? echo $cbo_search_by; ?>+'**'+document.getElementById('cbo_buyer_name').value+'**'+document.getElementById('txt_search_text').value, 'create_lc_search_list_view', 'search_div', 'lc_wise_payment_status_controller', 'setFilterGrid(\'tbl_list_search\',-1)');
			}
		}
    </script>

	</head>
	<body>
	<div align="center" style="width:100%;" >
		<form name="searInfoForm"  id="searInfoForm">
			<fieldset style="width:820px">
				<table style="margin-top:5px;" width="680" cellspacing="0" border="1" rules="all" cellpadding="0" class="rpt_table">
					<thead>
						<tr>
							<th class="must_entry_caption">Buyer Name</th>
							<th><?if($cbo_search_by==1){echo "LC No";}else{echo "SC No";}?></th>
							<th><input type="reset" name="res" id="res" value="Reset" style="width:60px" class="formbutton" onClick="reset_form('searInfoForm','','','','')" /></th>
						</tr>
					</thead>
					<tbody>
						<tr class="general">
							<td align="center">
								<?
									echo create_drop_down( "cbo_buyer_name", 150, "select id, buyer_name from lib_buyer where status_active=1 and is_deleted=0 order by buyer_name","id,buyer_name",1, "-- Select--",0,"",0 );
								?>
							</td>
							<td align="center" id="searchText">
								<input type="text" name="txt_search_text" id="txt_search_text" class="text_boxes" />
							</td>
							<td align="center">
								<input type="button" name="button" class="formbutton" value="Show" onClick="fn_show_lc_sc()" style="width:100px;" />
								<input type="hidden" name="selected_id" id="selected_id" class="text_boxes" readonly />
							</td>
						</tr>
					</tbody>
				</table>
				<div id="search_div"></div>
			</fieldset>
		</form>
	</div>
	</body>
	<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
	</html>
	<?
  exit();
}


if($action=="create_lc_search_list_view")
{
	list($company_id,$cbo_search_by,$cbo_buyer_name,$txt_search_text)=explode('**',$data);

	$buyer_details=return_library_array( "select id, buyer_name from lib_buyer",'id','buyer_name');
	$company_details = return_library_array('SELECT id, company_name FROM lib_company','id','company_name');

	if($cbo_search_by==1)
	{
		$sql="SELECT a.id as sc_lc_id, a.export_lc_no as sc_lc_no, a.lc_date as lc_sc_date, a.beneficiary_name as company_name, a.buyer_name as buyer_name, a.lc_value as sc_lc_value, '0' as type from com_export_lc a where a.beneficiary_name='$company_id' and a.buyer_name like '$cbo_buyer_name' and a.export_lc_no like '%$txt_search_text%' and a.status_active=1 and a.is_deleted=0 group by a.id, a.export_lc_no, a.lc_date, a.beneficiary_name, a.buyer_name, a.lc_value";
	}
	else if($cbo_search_by==2)
	{
		$sql="SELECT b.id as sc_lc_id, b.contract_no as sc_lc_no, b.contract_date as lc_sc_date, b.beneficiary_name as company_name, b.buyer_name as buyer_name, b.contract_value as sc_lc_value, '1' as type from com_sales_contract b where b.beneficiary_name='$company_id' and b.buyer_name like '$cbo_buyer_name' and b.contract_no like '%$txt_search_text%' and b.status_active=1 and b.is_deleted=0 group by b.id, b.contract_no, b.contract_date, b.beneficiary_name, b.buyer_name, b.contract_value";
	}
	
	// echo $sql;
	$lc_sc_type_array=array (0=>"LC",1=>"SC");
	$arr=array (2=>$lc_sc_type_array,3=>$company_details,4=>$buyer_details);

	echo create_list_view("tbl_list_search", "LC/SC No,LC/SC Date,Type,Beneficiary,Buyer,LC/SC Value", "150,80,80,150,150,150","820","250",0, $sql , "js_set_value", "sc_lc_id,sc_lc_no", "", 1, "0,0,type,company_name,buyer_name,0", $arr , "sc_lc_no,lc_sc_date,type,company_name,buyer_name,sc_lc_value", "","",'0,3,0,0,0,2','') ;

   exit();
}

if ($action=="report_generate")  //show
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$cbo_search_by=str_replace("'","",$cbo_search_by);
	
	if($report_type==1) //Show
	{
		$companyArr = return_library_array("select id,company_name from lib_company ","id","company_name"); 
		$buyerArr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 ","id","buyer_name");
		$lc_attch_sc=$sc_id='';
		$converte_sc_data_arr=$replaced_sc_amount_arr=array();
		if($cbo_search_by==2)
		{
			$lcAttchSc_sql="SELECT a.COM_EXPORT_LC_ID,a.REPLACED_AMOUNT, b.id as LC_SC_ID, b.BUYER_NAME, b.INTERNAL_FILE_NO, b.contract_no as LC_SC_NO, b.contract_date as LC_SC_DATE, b.contract_value as LC_SC_VALUE from com_export_lc_atch_sc_info a, com_sales_contract b where a.com_sales_contract_id=b.id and b.id=$txt_lc_sc_id and b.beneficiary_name=$cbo_company_id and a.status_active=1 and b.status_active=1";
			// echo $lcAttchSc_sql;die;
			$lcAttchSc_result=sql_select($lcAttchSc_sql);
			foreach($lcAttchSc_result as $row)
			{
				$lc_attch_sc.=$row['COM_EXPORT_LC_ID'].',';
				$sc_id.=$row['LC_SC_ID'].',';
				$converte_sc_data_arr[$row['LC_SC_ID']]['lc_sc_id']=$row['LC_SC_ID'];
				$converte_sc_data_arr[$row['LC_SC_ID']]['buyer_name']=$row['BUYER_NAME'];
				$converte_sc_data_arr[$row['LC_SC_ID']]['internal_file_no']=$row['INTERNAL_FILE_NO'];
				$converte_sc_data_arr[$row['LC_SC_ID']]['lc_sc_no']=$row['LC_SC_NO'];
				$converte_sc_data_arr[$row['LC_SC_ID']]['lc_sc_date']=$row['LC_SC_DATE'];
				$converte_sc_data_arr[$row['LC_SC_ID']]['lc_sc_value']=$row['LC_SC_VALUE'];
				$replaced_sc_amount_arr[$row['COM_EXPORT_LC_ID']]['replaced_amount']=$row['REPLACED_AMOUNT'];
			}
			$lc_attch_sc=rtrim($lc_attch_sc,',');
		}
		else
		{
			$lcAttchSc_sql="SELECT a.COM_EXPORT_LC_ID,a.REPLACED_AMOUNT, b.id as LC_SC_ID, b.BUYER_NAME, b.INTERNAL_FILE_NO, b.contract_no as LC_SC_NO, b.contract_date as LC_SC_DATE, b.contract_value as LC_SC_VALUE from com_export_lc_atch_sc_info a, com_sales_contract b where a.com_sales_contract_id=b.id and a.com_export_lc_id=$txt_lc_sc_id and b.beneficiary_name=$cbo_company_id and a.status_active=1 and b.status_active=1";
			// echo $lcAttchSc_sql;die;
			$lcAttchSc_result=sql_select($lcAttchSc_sql);
			foreach($lcAttchSc_result as $row)
			{
				$sc_id.=$row['LC_SC_ID'].',';
				$converte_sc_data_arr[$row['LC_SC_ID']]['lc_sc_id']=$row['LC_SC_ID'];
				$converte_sc_data_arr[$row['LC_SC_ID']]['buyer_name']=$row['BUYER_NAME'];
				$converte_sc_data_arr[$row['LC_SC_ID']]['internal_file_no']=$row['INTERNAL_FILE_NO'];
				$converte_sc_data_arr[$row['LC_SC_ID']]['lc_sc_no']=$row['LC_SC_NO'];
				$converte_sc_data_arr[$row['LC_SC_ID']]['lc_sc_date']=$row['LC_SC_DATE'];
				$converte_sc_data_arr[$row['LC_SC_ID']]['lc_sc_value']=$row['LC_SC_VALUE'];
				$replaced_sc_amount_arr[$row['COM_EXPORT_LC_ID']]['replaced_amount']=$row['REPLACED_AMOUNT'];
			}
		}
		if($lc_attch_sc=='')
		{
			if($cbo_search_by==1)
			{
				if($db_type==0)
				{
					$main_sql="SELECT a.id as LC_SC_ID, a.BUYER_NAME, a.INTERNAL_FILE_NO, a.export_lc_no as LC_SC_NO, a.lc_date as LC_SC_DATE, a.lc_value as LC_SC_VALUE, b.is_lc_sc as IS_LC_SC, c.id as BTB_ID, c.lc_number as BTB_NO, c.PAYTERM_ID, c.lc_value as BTB_VALUE, c.LAST_SHIPMENT_DATE, sum(d.current_acceptance_value) as INVOICE_VALUE, group_concat(distinct d.pi_id) as PI_ID, e.id as INVOICE_ID, e.INVOICE_NO, e.BANK_ACC_DATE, e.MATURITY_DATE, e.BANK_REF, f.id as PAYMENT_ID, f.PAYMENT_DATE, g.accepted_ammount as PAYMENT_VALUE
					from com_export_lc a
					left join com_btb_export_lc_attachment b on b.lc_sc_id=a.id and b.is_lc_sc=0 and b.status_active=1
					left join com_btb_lc_master_details c on b.import_mst_id=c.id and c.payterm_id<>1 and c.status_active=1
					left join com_import_invoice_dtls d on d.btb_lc_id=c.id and d.status_active=1 
					left join com_import_invoice_mst e on d.import_invoice_id=e.id and e.is_lc=1 and e.status_active=1
					left join com_import_payment_mst f on f.invoice_id=e.id and f.lc_id=c.id and f.status_active=1
					left join com_import_payment g on g.mst_id=f.id and g.status_active=1
					where a.id=$txt_lc_sc_id and a.beneficiary_name=$cbo_company_id and a.status_active=1 
					group by a.id, a.buyer_name, a.internal_file_no, a.export_lc_no, a.lc_date, a.lc_value, b.is_lc_sc, c.id, c.lc_number, c.payterm_id, c.lc_value, c.last_shipment_date, e.id, e.invoice_no, e.bank_acc_date, e.maturity_date, e.bank_ref, f.id, f.payment_date, g.accepted_ammount
					union all
					SELECT a.id as LC_SC_ID, a.BUYER_NAME, a.INTERNAL_FILE_NO, a.export_lc_no as LC_SC_NO, a.lc_date as LC_SC_DATE, a.lc_value as LC_SC_VALUE, b.is_lc_sc as IS_LC_SC, c.id as BTB_ID, c.lc_number as BTB_NO, c.PAYTERM_ID, c.lc_value as BTB_VALUE, c.LAST_SHIPMENT_DATE, sum(d.current_acceptance_value) as INVOICE_VALUE, group_concat(distinct d.pi_id) as PI_ID, e.id as INVOICE_ID, e.INVOICE_NO, e.BANK_ACC_DATE, e.MATURITY_DATE, e.BANK_REF, f.PAYMENT_DATE, f.id as PAYMENT_ID, g.accepted_ammount as PAYMENT_VALUE
					from com_export_lc a
					left join com_btb_export_lc_attachment b on b.lc_sc_id=a.id and b.is_lc_sc=0 and b.status_active=1
					left join com_btb_lc_master_details c on b.import_mst_id=c.id and c.payterm_id=1 and c.status_active=1
					left join com_import_invoice_dtls d on d.btb_lc_id=c.id and d.status_active=1 
					left join com_import_invoice_mst e on d.import_invoice_id=e.id and e.is_lc=1 and e.status_active=1
					left join com_import_payment_com_mst f on f.invoice_id=e.id and f.lc_id=c.id and f.status_active=1
					left join com_import_payment_com g on g.mst_id=f.id and g.status_active=1
					where a.id=$txt_lc_sc_id and a.beneficiary_name=$cbo_company_id and a.status_active=1
					group by a.id, a.buyer_name, a.internal_file_no, a.export_lc_no, a.lc_date, a.lc_value, b.is_lc_sc, c.id, c.lc_number, c.payterm_id, c.lc_value, c.last_shipment_date, e.id, e.invoice_no, e.bank_acc_date, e.maturity_date, e.bank_ref, f.id, f.payment_date, g.accepted_ammount
					order by LC_SC_ID, BTB_ID";
				}
				else
				{
					$main_sql="SELECT a.id as LC_SC_ID, a.BUYER_NAME, a.INTERNAL_FILE_NO, a.export_lc_no as LC_SC_NO, a.lc_date as LC_SC_DATE, a.lc_value as LC_SC_VALUE, b.is_lc_sc as IS_LC_SC, c.id as BTB_ID, c.lc_number as BTB_NO, c.PAYTERM_ID, c.lc_value as BTB_VALUE, c.LAST_SHIPMENT_DATE, sum(d.current_acceptance_value) as INVOICE_VALUE, listagg(d.pi_id ,',') within group (order by d.id) as PI_ID, e.id as INVOICE_ID, e.INVOICE_NO, e.BANK_ACC_DATE, e.MATURITY_DATE, e.BANK_REF, f.id as PAYMENT_ID, f.PAYMENT_DATE, g.accepted_ammount as PAYMENT_VALUE
					from com_export_lc a
					left join com_btb_export_lc_attachment b on b.lc_sc_id=a.id and b.is_lc_sc=0 and b.status_active=1
					left join com_btb_lc_master_details c on b.import_mst_id=c.id and c.payterm_id<>1 and c.status_active=1
					left join com_import_invoice_dtls d on d.btb_lc_id=c.id and d.status_active=1 
					left join com_import_invoice_mst e on d.import_invoice_id=e.id and e.is_lc=1 and e.status_active=1
					left join com_import_payment_mst f on f.invoice_id=e.id and f.lc_id=c.id and f.status_active=1
					left join com_import_payment g on g.mst_id=f.id and g.status_active=1
					where a.id=$txt_lc_sc_id and a.beneficiary_name=$cbo_company_id and a.status_active=1 
					group by a.id, a.buyer_name, a.internal_file_no, a.export_lc_no, a.lc_date, a.lc_value, b.is_lc_sc, c.id, c.lc_number, c.payterm_id, c.lc_value, c.last_shipment_date, e.id, e.invoice_no, e.bank_acc_date, e.maturity_date, e.bank_ref, f.id, f.payment_date, g.accepted_ammount
					union all
					SELECT a.id as LC_SC_ID, a.BUYER_NAME, a.INTERNAL_FILE_NO, a.export_lc_no as LC_SC_NO, a.lc_date as LC_SC_DATE, a.lc_value as LC_SC_VALUE, b.is_lc_sc as IS_LC_SC, c.id as BTB_ID, c.lc_number as BTB_NO, c.PAYTERM_ID, c.lc_value as BTB_VALUE, c.LAST_SHIPMENT_DATE, sum(d.current_acceptance_value) as INVOICE_VALUE, listagg(d.pi_id ,',') within group (order by d.id) as PI_ID, e.id as INVOICE_ID, e.INVOICE_NO, e.BANK_ACC_DATE, e.MATURITY_DATE, e.BANK_REF, f.id as PAYMENT_ID, f.PAYMENT_DATE, g.accepted_ammount as PAYMENT_VALUE
					from com_export_lc a
					left join com_btb_export_lc_attachment b on b.lc_sc_id=a.id and b.is_lc_sc=0 and b.status_active=1
					left join com_btb_lc_master_details c on b.import_mst_id=c.id and c.payterm_id=1 and c.status_active=1
					left join com_import_invoice_dtls d on d.btb_lc_id=c.id and d.status_active=1 
					left join com_import_invoice_mst e on d.import_invoice_id=e.id and e.is_lc=1 and e.status_active=1
					left join com_import_payment_com_mst f on f.invoice_id=e.id and f.lc_id=c.id and f.status_active=1
					left join com_import_payment_com g on g.mst_id=f.id and g.status_active=1
					where a.id=$txt_lc_sc_id and a.beneficiary_name=$cbo_company_id and a.status_active=1
					group by a.id, a.buyer_name, a.internal_file_no, a.export_lc_no, a.lc_date, a.lc_value, b.is_lc_sc, c.id, c.lc_number, c.payterm_id, c.lc_value, c.last_shipment_date, e.id, e.invoice_no, e.bank_acc_date, e.maturity_date, e.bank_ref, f.id, f.payment_date, g.accepted_ammount
					order by LC_SC_ID, BTB_ID";
				}
				
			}
			else
			{
				if($db_type==0)
				{
					$main_sql="SELECT a.id as LC_SC_ID, a.BUYER_NAME, a.INTERNAL_FILE_NO, a.contract_no as LC_SC_NO, a.contract_date as LC_SC_DATE, a.contract_value as LC_SC_VALUE, b.is_lc_sc as IS_LC_SC, c.id as BTB_ID, c.lc_number as BTB_NO, c.PAYTERM_ID, c.lc_value as BTB_VALUE, c.LAST_SHIPMENT_DATE, sum(d.current_acceptance_value) as INVOICE_VALUE, group_concat(distinct d.pi_id) as PI_ID, e.id as INVOICE_ID, e.INVOICE_NO, e.BANK_ACC_DATE, e.MATURITY_DATE, e.BANK_REF, f.id as PAYMENT_ID, f.PAYMENT_DATE, g.accepted_ammount as PAYMENT_VALUE
					from com_sales_contract a
					left join com_btb_export_lc_attachment b on b.lc_sc_id=a.id and b.is_lc_sc=1 and b.status_active=1
					left join com_btb_lc_master_details c on b.import_mst_id=c.id and c.payterm_id<>1 and c.status_active=1
					left join com_import_invoice_dtls d on d.btb_lc_id=c.id and d.status_active=1
					left join com_import_invoice_mst e on d.import_invoice_id=e.id and e.is_lc=1 and e.status_active=1
					left join com_import_payment_mst f on f.invoice_id=e.id and f.lc_id=c.id and f.status_active=1
					left join com_import_payment g on g.mst_id=f.id and g.status_active=1
					where a.id=$txt_lc_sc_id and a.beneficiary_name=$cbo_company_id and a.status_active=1 
					group by a.id, a.buyer_name, a.internal_file_no, a.contract_no, a.contract_date, a.contract_value, b.is_lc_sc, c.id, c.lc_number, c.payterm_id, c.lc_value, c.last_shipment_date, e.id, e.invoice_no, e.bank_acc_date, e.maturity_date, e.bank_ref, f.id, f.payment_date, g.accepted_ammount
					union all
					SELECT a.id as LC_SC_ID, a.BUYER_NAME, a.INTERNAL_FILE_NO, a.contract_no as LC_SC_NO, a.contract_date as LC_SC_DATE, a.contract_value as LC_SC_VALUE, b.is_lc_sc as IS_LC_SC, c.id as BTB_ID, c.lc_number as BTB_NO, c.PAYTERM_ID, c.lc_value as BTB_VALUE, c.LAST_SHIPMENT_DATE, sum(d.current_acceptance_value) as INVOICE_VALUE, group_concat(distinct d.pi_id) as PI_ID, e.id as INVOICE_ID, e.INVOICE_NO, e.BANK_ACC_DATE, e.MATURITY_DATE, e.BANK_REF, f.id as PAYMENT_ID, f.PAYMENT_DATE, g.accepted_ammount as PAYMENT_VALUE
					from com_sales_contract a
					left join com_btb_export_lc_attachment b on b.lc_sc_id=a.id and b.is_lc_sc=1 and b.status_active=1
					left join com_btb_lc_master_details c on b.import_mst_id=c.id and c.payterm_id=1 and c.status_active=1
					left join com_import_invoice_dtls d on d.btb_lc_id=c.id and d.status_active=1
					left join com_import_invoice_mst e on d.import_invoice_id=e.id and e.is_lc=1 and e.status_active=1
					left join com_import_payment_com_mst f on f.invoice_id=e.id and f.lc_id=c.id and f.status_active=1
					left join com_import_payment_com g on g.mst_id=f.id and g.status_active=1
					where a.id=$txt_lc_sc_id and a.beneficiary_name=$cbo_company_id and a.status_active=1 
					group by a.id, a.buyer_name, a.internal_file_no, a.contract_no, a.contract_date, a.contract_value, b.is_lc_sc, c.id, c.lc_number, c.payterm_id, c.lc_value, c.last_shipment_date, e.id, e.invoice_no, e.bank_acc_date, e.maturity_date, e.bank_ref, f.id, f.payment_date, g.accepted_ammount
					order by LC_SC_ID, BTB_ID";
				}
				else
				{
					$main_sql="SELECT a.id as LC_SC_ID, a.BUYER_NAME, a.INTERNAL_FILE_NO, a.contract_no as LC_SC_NO, a.contract_date as LC_SC_DATE, a.contract_value as LC_SC_VALUE, b.is_lc_sc as IS_LC_SC, c.id as BTB_ID, c.lc_number as BTB_NO, c.PAYTERM_ID, c.lc_value as BTB_VALUE, c.LAST_SHIPMENT_DATE, sum(d.current_acceptance_value) as INVOICE_VALUE, listagg(d.pi_id ,',') within group (order by d.id) as PI_ID, e.id as INVOICE_ID, e.INVOICE_NO, e.BANK_ACC_DATE, e.MATURITY_DATE, e.BANK_REF, f.id as PAYMENT_ID, f.PAYMENT_DATE, g.accepted_ammount as PAYMENT_VALUE
					from com_sales_contract a
					left join com_btb_export_lc_attachment b on b.lc_sc_id=a.id and b.is_lc_sc=1 and b.status_active=1
					left join com_btb_lc_master_details c on b.import_mst_id=c.id and c.payterm_id<>1 and c.status_active=1
					left join com_import_invoice_dtls d on d.btb_lc_id=c.id and d.status_active=1
					left join com_import_invoice_mst e on d.import_invoice_id=e.id and e.is_lc=1 and e.status_active=1
					left join com_import_payment_mst f on f.invoice_id=e.id and f.lc_id=c.id and f.status_active=1
					left join com_import_payment g on g.mst_id=f.id and g.status_active=1
					where a.id=$txt_lc_sc_id and a.beneficiary_name=$cbo_company_id and a.status_active=1 
					group by a.id, a.buyer_name, a.internal_file_no, a.contract_no, a.contract_date, a.contract_value, b.is_lc_sc, c.id, c.lc_number, c.payterm_id, c.lc_value, c.last_shipment_date, e.id, e.invoice_no, e.bank_acc_date, e.maturity_date, e.bank_ref, f.id, f.payment_date, g.accepted_ammount
					union all
					SELECT a.id as LC_SC_ID, a.BUYER_NAME, a.INTERNAL_FILE_NO, a.contract_no as LC_SC_NO, a.contract_date as LC_SC_DATE, a.contract_value as LC_SC_VALUE, b.is_lc_sc as IS_LC_SC, c.id as BTB_ID, c.lc_number as BTB_NO, c.PAYTERM_ID, c.lc_value as BTB_VALUE, c.LAST_SHIPMENT_DATE, sum(d.current_acceptance_value) as INVOICE_VALUE, listagg(d.pi_id ,',') within group (order by d.id) as PI_ID, e.id as INVOICE_ID, e.INVOICE_NO, e.BANK_ACC_DATE, e.MATURITY_DATE, e.BANK_REF, f.id as PAYMENT_ID, f.PAYMENT_DATE, g.accepted_ammount as PAYMENT_VALUE
					from com_sales_contract a
					left join com_btb_export_lc_attachment b on b.lc_sc_id=a.id and b.is_lc_sc=1 and b.status_active=1
					left join com_btb_lc_master_details c on b.import_mst_id=c.id and c.payterm_id=1 and c.status_active=1
					left join com_import_invoice_dtls d on d.btb_lc_id=c.id and d.status_active=1
					left join com_import_invoice_mst e on d.import_invoice_id=e.id and e.is_lc=1 and e.status_active=1
					left join com_import_payment_com_mst f on f.invoice_id=e.id and f.lc_id=c.id and f.status_active=1
					left join com_import_payment_com g on g.mst_id=f.id and g.status_active=1
					where a.id=$txt_lc_sc_id and a.beneficiary_name=$cbo_company_id and a.status_active=1 
					group by a.id, a.buyer_name, a.internal_file_no, a.contract_no, a.contract_date, a.contract_value, b.is_lc_sc, c.id, c.lc_number, c.payterm_id, c.lc_value, c.last_shipment_date, e.id, e.invoice_no, e.bank_acc_date, e.maturity_date, e.bank_ref, f.id, f.payment_date, g.accepted_ammount
					order by LC_SC_ID, BTB_ID";
				}
				
			}
		}
		else
		{
			if($db_type==0)
			{
				$main_sql="SELECT a.id as LC_SC_ID, a.BUYER_NAME, a.INTERNAL_FILE_NO, a.export_lc_no as LC_SC_NO, a.lc_date as LC_SC_DATE, a.lc_value as LC_SC_VALUE, b.is_lc_sc as IS_LC_SC, c.id as BTB_ID, c.lc_number as BTB_NO, c.PAYTERM_ID, c.lc_value as BTB_VALUE, c.LAST_SHIPMENT_DATE, sum(d.current_acceptance_value) as INVOICE_VALUE, group_concat(distinct d.pi_id) as PI_ID, e.id as INVOICE_ID, e.INVOICE_NO, e.BANK_ACC_DATE, e.MATURITY_DATE, e.BANK_REF, f.id as PAYMENT_ID, f.PAYMENT_DATE, g.accepted_ammount as PAYMENT_VALUE
				from com_export_lc a
				left join com_btb_export_lc_attachment b on b.lc_sc_id=a.id and b.is_lc_sc=0 and b.status_active=1
				left join com_btb_lc_master_details c on b.import_mst_id=c.id and c.payterm_id<>1 and c.status_active=1
				left join com_import_invoice_dtls d on d.btb_lc_id=c.id and d.status_active=1 
				left join com_import_invoice_mst e on d.import_invoice_id=e.id and e.is_lc=1 and e.status_active=1
				left join com_import_payment_mst f on f.invoice_id=e.id and f.lc_id=c.id and f.status_active=1
				left join com_import_payment g on g.mst_id=f.id and g.status_active=1
				where a.id in($lc_attch_sc) and a.beneficiary_name=$cbo_company_id and a.status_active=1 
				group by a.id, a.buyer_name, a.internal_file_no, a.export_lc_no, a.lc_date, a.lc_value, b.is_lc_sc, c.id, c.lc_number, c.payterm_id, c.lc_value, c.last_shipment_date, e.id, e.invoice_no, e.bank_acc_date, e.maturity_date, e.bank_ref, f.id, f.payment_date
				union all
				SELECT a.id as LC_SC_ID, a.BUYER_NAME, a.INTERNAL_FILE_NO, a.export_lc_no as LC_SC_NO, a.lc_date as LC_SC_DATE, a.lc_value as LC_SC_VALUE, b.is_lc_sc as IS_LC_SC, c.id as BTB_ID, c.lc_number as BTB_NO, c.PAYTERM_ID, c.lc_value as BTB_VALUE, c.LAST_SHIPMENT_DATE, sum(d.current_acceptance_value) as INVOICE_VALUE, group_concat(distinct d.pi_id) as PI_ID, e.id as INVOICE_ID, e.INVOICE_NO, e.BANK_ACC_DATE, e.MATURITY_DATE, e.BANK_REF, f.id as PAYMENT_ID, f.PAYMENT_DATE, g.accepted_ammount as PAYMENT_VALUE
				from com_export_lc a
				left join com_btb_export_lc_attachment b on b.lc_sc_id=a.id and b.is_lc_sc=0 and b.status_active=1
				left join com_btb_lc_master_details c on b.import_mst_id=c.id and c.payterm_id=1 and c.status_active=1
				left join com_import_invoice_dtls d on d.btb_lc_id=c.id and d.status_active=1 
				left join com_import_invoice_mst e on d.import_invoice_id=e.id and e.is_lc=1 and e.status_active=1
				left join com_import_payment_com_mst f on f.invoice_id=e.id and f.lc_id=c.id and f.status_active=1
				left join com_import_payment_com g on g.mst_id=f.id and g.status_active=1
				where a.id in($lc_attch_sc) and a.beneficiary_name=$cbo_company_id and a.status_active=1
				group by a.id, a.buyer_name, a.internal_file_no, a.export_lc_no, a.lc_date, a.lc_value, b.is_lc_sc, c.id, c.lc_number, c.payterm_id, c.lc_value, c.last_shipment_date, e.id, e.invoice_no, e.bank_acc_date, e.maturity_date, e.bank_ref, f.id, f.payment_date, g.accepted_ammount
				order by LC_SC_ID, BTB_ID";
			}
			else
			{
				$main_sql="SELECT a.id as LC_SC_ID, a.BUYER_NAME, a.INTERNAL_FILE_NO, a.export_lc_no as LC_SC_NO, a.lc_date as LC_SC_DATE, a.lc_value as LC_SC_VALUE, b.is_lc_sc as IS_LC_SC, c.id as BTB_ID, c.lc_number as BTB_NO, c.PAYTERM_ID, c.lc_value as BTB_VALUE, c.LAST_SHIPMENT_DATE, sum(d.current_acceptance_value) as INVOICE_VALUE, listagg(d.pi_id ,',') within group (order by d.id) as PI_ID, e.id as INVOICE_ID, e.INVOICE_NO, e.BANK_ACC_DATE, e.MATURITY_DATE, e.BANK_REF, f.id as PAYMENT_ID, f.PAYMENT_DATE, g.accepted_ammount as PAYMENT_VALUE
				from com_export_lc a
				left join com_btb_export_lc_attachment b on b.lc_sc_id=a.id and b.is_lc_sc=0 and b.status_active=1
				left join com_btb_lc_master_details c on b.import_mst_id=c.id and c.payterm_id<>1 and c.status_active=1
				left join com_import_invoice_dtls d on d.btb_lc_id=c.id and d.status_active=1 
				left join com_import_invoice_mst e on d.import_invoice_id=e.id and e.is_lc=1 and e.status_active=1
				left join com_import_payment_mst f on f.invoice_id=e.id and f.lc_id=c.id and f.status_active=1
				left join com_import_payment g on g.mst_id=f.id and g.status_active=1
				where a.id in($lc_attch_sc) and a.beneficiary_name=$cbo_company_id and a.status_active=1 
				group by a.id, a.buyer_name, a.internal_file_no, a.export_lc_no, a.lc_date, a.lc_value, b.is_lc_sc, c.id, c.lc_number, c.payterm_id, c.lc_value, c.last_shipment_date, e.id, e.invoice_no, e.bank_acc_date, e.maturity_date, e.bank_ref, f.id, f.payment_date, g.accepted_ammount
				union all
				SELECT a.id as LC_SC_ID, a.BUYER_NAME, a.INTERNAL_FILE_NO, a.export_lc_no as LC_SC_NO, a.lc_date as LC_SC_DATE, a.lc_value as LC_SC_VALUE, b.is_lc_sc as IS_LC_SC, c.id as BTB_ID, c.lc_number as BTB_NO, c.PAYTERM_ID, c.lc_value as BTB_VALUE, c.LAST_SHIPMENT_DATE, sum(d.current_acceptance_value) as INVOICE_VALUE, listagg(d.pi_id ,',') within group (order by d.id) as PI_ID, e.id as INVOICE_ID, e.INVOICE_NO, e.BANK_ACC_DATE, e.MATURITY_DATE, e.BANK_REF, f.id as PAYMENT_ID, f.PAYMENT_DATE, g.accepted_ammount as PAYMENT_VALUE
				from com_export_lc a
				left join com_btb_export_lc_attachment b on b.lc_sc_id=a.id and b.is_lc_sc=0 and b.status_active=1
				left join com_btb_lc_master_details c on b.import_mst_id=c.id and c.payterm_id=1 and c.status_active=1
				left join com_import_invoice_dtls d on d.btb_lc_id=c.id and d.status_active=1 
				left join com_import_invoice_mst e on d.import_invoice_id=e.id and e.is_lc=1 and e.status_active=1
				left join com_import_payment_com_mst f on f.invoice_id=e.id and f.lc_id=c.id and f.status_active=1
				left join com_import_payment_com g on g.mst_id=f.id and g.status_active=1
				where a.id in($lc_attch_sc) and a.beneficiary_name=$cbo_company_id and a.status_active=1
				group by a.id, a.buyer_name, a.internal_file_no, a.export_lc_no, a.lc_date, a.lc_value, b.is_lc_sc, c.id, c.lc_number, c.payterm_id, c.lc_value, c.last_shipment_date, e.id, e.invoice_no, e.bank_acc_date, e.maturity_date, e.bank_ref, f.id, f.payment_date, g.accepted_ammount
				order by LC_SC_ID, BTB_ID";
			}

		}
		//echo $main_sql;die;
		$main_result=sql_select($main_sql);
		$all_data_arr=$invoice_value_arr=array();$payment_value_arr=array();
		
		foreach($main_result as $row)
		{
			if($export_lc_check[$row['LC_SC_ID']]=="")
			{
				$export_lc_check[$row['LC_SC_ID']]=$row['LC_SC_ID'];
				$j=1;
			}
			
			if($lc_sc_chk[$row['LC_SC_ID']][$row['BTB_ID']][$row['INVOICE_ID']]==""){
				$lc_sc_chk[$row['LC_SC_ID']][$row['BTB_ID']][$row['INVOICE_ID']]=$row['LC_SC_ID'];
				if($row['BTB_ID']!="")
				{
					//  ==============LC/SC================ 
					$all_data_arr[$row['LC_SC_ID']][$row['BTB_ID']][$row['INVOICE_ID']]['LC_SC_ID']=$row['LC_SC_ID'];
					$all_data_arr[$row['LC_SC_ID']][$row['BTB_ID']][$row['INVOICE_ID']]['BUYER_NAME']=$row['BUYER_NAME'];
					$all_data_arr[$row['LC_SC_ID']][$row['BTB_ID']][$row['INVOICE_ID']]['INTERNAL_FILE_NO']=$row['INTERNAL_FILE_NO'];
					$all_data_arr[$row['LC_SC_ID']][$row['BTB_ID']][$row['INVOICE_ID']]['LC_SC_NO']=$row['LC_SC_NO'];
					$all_data_arr[$row['LC_SC_ID']][$row['BTB_ID']][$row['INVOICE_ID']]['LC_SC_DATE']=$row['LC_SC_DATE'];
					$all_data_arr[$row['LC_SC_ID']][$row['BTB_ID']][$row['INVOICE_ID']]['LC_SC_VALUE']=$row['LC_SC_VALUE'];
					// ==============BTB LC Details================
					$all_data_arr[$row['LC_SC_ID']][$row['BTB_ID']][$row['INVOICE_ID']]['BTB_ID']=$row['BTB_ID'];
					$all_data_arr[$row['LC_SC_ID']][$row['BTB_ID']][$row['INVOICE_ID']]['BTB_NO']=$row['BTB_NO'];
					$all_data_arr[$row['LC_SC_ID']][$row['BTB_ID']][$row['INVOICE_ID']]['PAYTERM_ID']=$row['PAYTERM_ID'];
					$all_data_arr[$row['LC_SC_ID']][$row['BTB_ID']][$row['INVOICE_ID']]['BTB_VALUE']=$row['BTB_VALUE'];
					$all_data_arr[$row['LC_SC_ID']][$row['BTB_ID']][$row['INVOICE_ID']]['LAST_SHIPMENT_DATE']=$row['LAST_SHIPMENT_DATE'];
					//  ==============Acceptance================
					$all_data_arr[$row['LC_SC_ID']][$row['BTB_ID']][$row['INVOICE_ID']]['INVOICE_VALUE']=$row['INVOICE_VALUE'];
					$all_data_arr[$row['LC_SC_ID']][$row['BTB_ID']][$row['INVOICE_ID']]['PI_ID']=$row['PI_ID'];
					$all_data_arr[$row['LC_SC_ID']][$row['BTB_ID']][$row['INVOICE_ID']]['INVOICE_ID']=$row['INVOICE_ID'];
					$all_data_arr[$row['LC_SC_ID']][$row['BTB_ID']][$row['INVOICE_ID']]['INVOICE_NO']=$row['INVOICE_NO'];
					$all_data_arr[$row['LC_SC_ID']][$row['BTB_ID']][$row['INVOICE_ID']]['BANK_ACC_DATE']=$row['BANK_ACC_DATE'];
					$all_data_arr[$row['LC_SC_ID']][$row['BTB_ID']][$row['INVOICE_ID']]['MATURITY_DATE']=$row['MATURITY_DATE'];
					$all_data_arr[$row['LC_SC_ID']][$row['BTB_ID']][$row['INVOICE_ID']]['BANK_REF']=$row['BANK_REF'];
					//  ==============Payment================ 
					$all_data_arr[$row['LC_SC_ID']][$row['BTB_ID']][$row['INVOICE_ID']]['PAYMENT_ID']=$row['PAYMENT_ID'];
					$all_data_arr[$row['LC_SC_ID']][$row['BTB_ID']][$row['INVOICE_ID']]['PAYMENT_DATE']=$row['PAYMENT_DATE'];
					$all_data_arr[$row['LC_SC_ID']][$row['BTB_ID']][$row['INVOICE_ID']]['PAYMENT_VALUE']=$row['PAYMENT_VALUE'];
					
					$invoice_value_arr[$row['LC_SC_ID']][$row['BTB_ID']]['invoice_value']+=$row['INVOICE_VALUE'];
					$payment_value_arr[$row['LC_SC_ID']][$row['BTB_ID']][$row['INVOICE_ID']]['tot_payment_value']+=$row['PAYMENT_VALUE'];
					if($row['IS_LC_SC']==0)
					{
						$lc_id.=$row['LC_SC_ID'].',';
					}
					else
					{
						$sc_id.=$row['LC_SC_ID'].',';
					}
					$btb_id.=$row['BTB_ID'].',';
					$pi_id.=$row['PI_ID'].',';
					$buyer_count[$row['BUYER_NAME']]++;
					$file_count[$row['INTERNAL_FILE_NO']]++;
					$lc_sc_count[$row['LC_SC_ID']]++;
					$btb_count[$row['BTB_ID']]++;
					$invoice_count[$row['BTB_ID']][$row['INVOICE_ID']]++;
					
					$j++;
				}
				else
				{
					if($j==1)
					{
						$all_data_arr[$row['LC_SC_ID']][$row['BTB_ID']][$row['INVOICE_ID']]['LC_SC_ID']=$row['LC_SC_ID'];
						$all_data_arr[$row['LC_SC_ID']][$row['BTB_ID']][$row['INVOICE_ID']]['BUYER_NAME']=$row['BUYER_NAME'];
						$all_data_arr[$row['LC_SC_ID']][$row['BTB_ID']][$row['INVOICE_ID']]['INTERNAL_FILE_NO']=$row['INTERNAL_FILE_NO'];
						$all_data_arr[$row['LC_SC_ID']][$row['BTB_ID']][$row['INVOICE_ID']]['LC_SC_NO']=$row['LC_SC_NO'];
						$all_data_arr[$row['LC_SC_ID']][$row['BTB_ID']][$row['INVOICE_ID']]['LC_SC_DATE']=$row['LC_SC_DATE'];
						$all_data_arr[$row['LC_SC_ID']][$row['BTB_ID']][$row['INVOICE_ID']]['LC_SC_VALUE']=$row['LC_SC_VALUE'];
						
						if($row['IS_LC_SC']==0)
						{
							$lc_id.=$row['LC_SC_ID'].',';
						}
						else
						{
							$sc_id.=$row['LC_SC_ID'].',';
						}
						$btb_id.=$row['BTB_ID'].',';
						$pi_id.=$row['PI_ID'].',';
						$buyer_count[$row['BUYER_NAME']]++;
						$file_count[$row['INTERNAL_FILE_NO']]++;
						$lc_sc_count[$row['LC_SC_ID']]++;
						$btb_count[$row['BTB_ID']]++;
						$invoice_count[$row['BTB_ID']][$row['INVOICE_ID']]++;
						
					}
					$j++;
				}
			}
		}
		
		// echo "<pre>";print_r($all_data_arr);echo "<pre>";print_r($lc_sc_count);die;
		
		$sc_id=implode(",",array_unique(explode(",",chop($sc_id,','))));
		$lc_id=implode(",",array_unique(explode(",",chop($lc_id,','))));
		$btb_id=implode(",",array_unique(explode(",",chop($btb_id,','))));
		$pi_id=implode(",",array_unique(explode(",",chop($pi_id,','))));

		$sc_ama_sql="SELECT CONTRACT_ID, AMENDMENT_NO, AMENDMENT_DATE, AMENDMENT_VALUE, VALUE_CHANGE_BY from com_sales_contract_amendment where contract_id in($sc_id) and status_active=1 order by id";
		// echo $sc_ama_sql;die;
		$sc_ama_result=sql_select($sc_ama_sql);
		$sc_ama_arr=array();
		foreach($sc_ama_result as $row)
		{
			$sc_ama_arr[$row['CONTRACT_ID']][$row['AMENDMENT_NO']]['amendment_no']=$row['AMENDMENT_NO'];
			$sc_ama_arr[$row['CONTRACT_ID']][$row['AMENDMENT_NO']]['amendment_date']=$row['AMENDMENT_DATE'];
			$sc_ama_arr[$row['CONTRACT_ID']][$row['AMENDMENT_NO']]['amendment_value']=$row['AMENDMENT_VALUE'];
			$sc_ama_arr[$row['CONTRACT_ID']][$row['AMENDMENT_NO']]['value_change_by']=$row['VALUE_CHANGE_BY'];
		}

		$lc_ama_sql="SELECT EXPORT_LC_ID, AMENDMENT_NO, AMENDMENT_DATE, AMENDMENT_VALUE, VALUE_CHANGE_BY from com_export_lc_amendment where export_lc_id in($lc_id) and status_active=1 order by id";
		// echo $lc_ama_sql;die;
		$lc_ama_result=sql_select($lc_ama_sql);
		$lc_ama_arr=array();
		foreach($lc_ama_result as $row)
		{
			$lc_ama_arr[$row['EXPORT_LC_ID']][$row['AMENDMENT_NO']]['export_lc_id']=$row['EXPORT_LC_ID'];
			$lc_ama_arr[$row['EXPORT_LC_ID']][$row['AMENDMENT_NO']]['amendment_no']=$row['AMENDMENT_NO'];
			$lc_ama_arr[$row['EXPORT_LC_ID']][$row['AMENDMENT_NO']]['amendment_date']=$row['AMENDMENT_DATE'];
			$lc_ama_arr[$row['EXPORT_LC_ID']][$row['AMENDMENT_NO']]['amendment_value']=$row['AMENDMENT_VALUE'];
			$lc_ama_arr[$row['EXPORT_LC_ID']][$row['AMENDMENT_NO']]['value_change_by']=$row['VALUE_CHANGE_BY'];
		}

		$btb_ama_sql="SELECT BTB_ID, AMENDMENT_NO, AMENDMENT_DATE, AMENDMENT_VALUE, VALUE_CHANGE_BY from com_btb_lc_amendment where btb_id in($btb_id) and status_active=1 order by id";
		// echo $btb_ama_sql;die;
		$btb_ama_result=sql_select($btb_ama_sql);
		$btb_ama_arr=array();
		foreach($btb_ama_result as $row)
		{
			$btb_ama_arr[$row['BTB_ID']][$row['AMENDMENT_NO']]['amendment_no']=$row['AMENDMENT_NO'];
			$btb_ama_arr[$row['BTB_ID']][$row['AMENDMENT_NO']]['amendment_date']=$row['AMENDMENT_DATE'];
			$btb_ama_arr[$row['BTB_ID']][$row['AMENDMENT_NO']]['amendment_value']=$row['AMENDMENT_VALUE'];
			$btb_ama_arr[$row['BTB_ID']][$row['AMENDMENT_NO']]['value_change_by']=$row['VALUE_CHANGE_BY'];
		}

		$piCategoryArr = return_library_array("SELECT id, item_category_id from com_pi_master_details where id in($pi_id) and status_active=1 ","id","item_category_id");
		ob_start();		
		?>
		<style>
			.wrd_brk{word-break: break-all;}
			.left{text-align: left;}
			.center{text-align: center;}
			.right{text-align: right;}
		</style>

		<div id="scroll_body" align="center" style="height:auto; width:2350px; margin:0 auto; padding:0;">
			<table width="2350px" >
				<tr>
					<td colspan="27" class="center" style="font-size:18px"><strong><u><? echo $report_title; ?></u></strong></td>
				</tr>
			</table>
			<table cellspacing="0" width="2350"  border="1" rules="all" class="rpt_table">
				<thead>
					<tr>
						<th width="50" rowspan="2">SL No</th>
						<th colspan="8">LC/SC</th>
						<th colspan="8">BTB LC Details</th>
						<th colspan="6">Acceptance</th>
						<th colspan="4">Payment</th>
					</tr>
					<tr>
						<!-- ==============LC/SC================ -->
						<th width="100" >BUYER</th>
						<th width="100" >File No</th>
						<th width="100" >SC/LC NO</th>
						<th width="80" >DATE</th>
						<th width="80" >Value Changed By</th>
						<th width="100" >SC Value (LC/SC, Finance)</th>
						<th width="80" >REPLACE AMOUNT</th>
						<th width="80" >Balance</th>
						<!-- ==============BTB LC Details================ -->
						<th width="100" >BTB LC NO.</th>
						<th width="100" >LC/Amd Date</th>
						<th width="100" >Amend No</th>
						<th width="80" >Value Changed By (US$)</th>
						<th width="80" >DEF L/C Amount (US$)</th>
						<th width="80" >At Sight L/C Amount (US$)</th>
						<th width="80" >TT/FDD (US$)</th>
						<th width="80" >LC Last SHIP. DATE</th>
						<!-- ==============Acceptance================ -->
						<th width="100" >ITEM Category</th>
						<th width="100" >Invoice Number</th>
						<th width="80" >DOCUMENT RECEIVED.</th>
						<th width="80" >Bank Acceptance date</th>
						<th width="80" >DUE/ Maturity DATE</th>
						<th width="80" >BALANCE</th>
						<!-- ==============Payment================ -->
						<th width="100" >Bank Ref. No</th>
						<th width="80" >Amount</th>
						<th width="80" >DATE</th>
						<th  >BALANCE</th>
					</tr>
				</thead>
				<tbody>
					<?
					$i=1;
					$array_check_arr=$array_check_arr2=$array_check_arr3=$array_check_arr4=$array_check_arr5=$array_check_arr6=array();
					foreach($converte_sc_data_arr as $row)
					{
						if (fmod($i,2)==0) $bgcolor='#E9F3FF';
		                else $bgcolor='#FFFFFF';
						?>
							<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
								<td class="center"><?=$i;?></td>
								<td class="wrd_brk"><?echo $buyerArr[$row['buyer_name']];?></td>
								<td><?echo $row['internal_file_no'];?></td>
								<td>
									<?
										echo $row['lc_sc_no'];
										foreach($sc_ama_arr[$row['lc_sc_id']] as $val)
										{
											if($val['amendment_no']!=0)
											{
												echo "<hr style='border: 1px solid #8DAFDA;'>SC-AMD: ".$val['amendment_no'];
											}
										}
									?>
								</td>
								<td class="center">
									<?
										echo change_date_format($row['lc_sc_date']);
										foreach($sc_ama_arr[$row['lc_sc_id']] as $val)
										{
											if($val['amendment_no']!=0)
											{
												echo "<hr style='border: 1px solid #8DAFDA;'>".change_date_format($val['amendment_date']);
											}
										}
									?>
								</td>
								<td class="right">
									<?
										foreach($sc_ama_arr[$row['lc_sc_id']] as $val)
										{
											if($val['amendment_no']==0)
											{
												echo "<br>";
											}
											else
											{
												if($val['value_change_by']==1)
												{
													echo "<hr style='border: 1px solid #8DAFDA;'>".number_format($val['amendment_value'],2);
												}
												if($val['value_change_by']==2)
												{
													echo "<hr style='border: 1px solid #8DAFDA;'>(".number_format($val['amendment_value'],2).")";
												}
											}
										}
									?>
								</td>
								<td class="right"><?echo number_format($row['lc_sc_value'],2);$tot_lc_sc_value+=$row['lc_sc_value'];?></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
								<td></td>
							</tr>
						<?
						$i++;
					}
					
					foreach($all_data_arr as $lcScId=>$lcScval)
					{
						foreach($lcScval as $btbID=>$btbData)
						{
							foreach($btbData as $invoiceId=>$row)
							{
								if (fmod($i,2)==0) $bgcolor='#E9F3FF';
								else $bgcolor='#FFFFFF';
								$buyer_row=$buyer_count[$row['BUYER_NAME']];
								$file_row=$file_count[$row['INTERNAL_FILE_NO']];
								$lc_sc_row=$lc_sc_count[$row['LC_SC_ID']];
								$btb_row=$btb_count[$row['BTB_ID']];
								$invoice_row=$invoice_count[$row['BTB_ID']][$row['INVOICE_ID']];
								?>
									<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
										<td class="center"><?=$i;?></td>
										<!-- ===============LC/SC================ -->
										<?php
											if(!in_array($row['BUYER_NAME'],$array_check_arr))
											{
												$array_check_arr[]=$row['BUYER_NAME'];
												?>
													<td class="wrd_brk" valign="top" rowspan="<?=$buyer_row;?>" ><?echo $buyerArr[$row['BUYER_NAME']];?></td>
												<?
											}
										?>
										<?php
											if(!in_array($row['INTERNAL_FILE_NO'],$array_check_arr2))
											{
												$array_check_arr2[]=$row['INTERNAL_FILE_NO'];
												?>
													<td class="wrd_brk" valign="top" rowspan="<?=$buyer_row;?>" ><?echo $row['INTERNAL_FILE_NO'];?></td>
												<?
											}
										?>
										<?php
											if(!in_array($row['LC_SC_ID'],$array_check_arr3))
											{
												$array_check_arr3[]=$row['LC_SC_ID'];
												?>
													<td valign="top" rowspan="<?=$lc_sc_row;?>" >
														<?
															echo $row['LC_SC_NO'];
															echo $lc_ama_arr[$row['LC_SC_ID']]['2']['amendment_no'];
															if($row['is_lc_sc']==0)
															{
																foreach($lc_ama_arr[$row['LC_SC_ID']] as $val)
																{
																	if($val['amendment_no']!=0)
																	{
																		echo "<hr style='border: 1px solid #8DAFDA;'>LC-AMD: ".$val['amendment_no'];
																	}
																}
															}
															else
															{
																foreach($sc_ama_arr[$row['LC_SC_ID']] as $val)
																{
																	if($val['amendment_no']!=0)
																	{
																		echo "<hr style='border: 1px solid #8DAFDA;'>SC-AMD: ".$val['amendment_no'];
																	}
																}
															}
		
														?>
													</td>
													<td class="center" valign="top" rowspan="<?=$lc_sc_row;?>" >
														<?
															echo change_date_format($row['LC_SC_DATE']);
															if($row['IS_LC_SC']==0)
															{
																foreach($lc_ama_arr[$row['LC_SC_ID']] as $val)
																{
																	if($val['amendment_no']!=0)
																	{
																		echo "<hr style='border: 1px solid #8DAFDA;'>".change_date_format($val['amendment_date']);
																	}
																}
															}
															else
															{
																foreach($sc_ama_arr[$row['LC_SC_ID']] as $val)
																{
																	if($val['amendment_no']!=0)
																	{
																		echo "<hr style='border: 1px solid #8DAFDA;'>".change_date_format($val['amendment_date']);
																	}
																}
															}
		
														?>
													</td>
													<td class="right" valign="top" rowspan="<?=$lc_sc_row;?>" >
														<?
															if($row['is_lc_sc']==0)
															{
																foreach($lc_ama_arr[$row['LC_SC_ID']] as $val)
																{
																	if($val['amendment_no']==0)
																	{
																		echo "<br>";
																	}
																	else
																	{
																		if($val['value_change_by']==1)
																		{
																			echo "<hr style='border: 1px solid #8DAFDA;'>".number_format($val['amendment_value'],2);
																		}
																		if($val['value_change_by']==2)
																		{
																			echo "<hr style='border: 1px solid #8DAFDA;'>(".number_format($val['amendment_value'],2).")";
																		}
																	}
																}
															}
															else
															{
																foreach($sc_ama_arr[$row['LC_SC_ID']] as $val)
																{
																	if($val['amendment_no']==0)
																	{
																		echo "<br>";
																	}
																	else
																	{
																		echo "<hr style='border: 1px solid #8DAFDA;'>".number_format($val['amendment_value'],2);
																	}
																}
															}
		
														?>
													</td>
													<td class="right" valign="top" rowspan="<?=$lc_sc_row;?>" ><?
														if(empty($replaced_sc_amount_arr[$row['LC_SC_ID']]['replaced_amount']))
														{
															echo number_format($row['LC_SC_VALUE'],2);$tot_lc_sc_value+=$row['LC_SC_VALUE'];
														}?>
													</td>
													<td class="right" valign="top" rowspan="<?=$lc_sc_row;?>" ><?echo number_format($replaced_sc_amount_arr[$row['LC_SC_ID']]['replaced_amount'],2);$tot_replaced_value+=$replaced_sc_amount_arr[$row['LC_SC_ID']]['replaced_amount'];?></td>
													<td valign="top" rowspan="<?=$lc_sc_row;?>" ></td>
												<?
											}
										?>
										<!-- ===============BTB LC Details================ -->
										<?php
											if(!in_array($row['BTB_ID'],$array_check_arr4))
											{
												$array_check_arr4[]=$row['BTB_ID'];
												?>
													<td class="wrd_brk" valign="top" rowspan="<?=$btb_row;?>"><?echo $row['BTB_NO'];?></td>
													<td class="center" valign="top" rowspan="<?=$btb_row;?>">
														<?
															foreach($btb_ama_arr[$row['BTB_ID']] as $val)
															{
																if($val['amendment_no']==0)
																{
																	echo "<br>";
																}
																else
																{
																	echo "<hr style='border: 1px solid #8DAFDA;'>".change_date_format($val['amendment_date']);
																}
															}
														?>
													</td>
													<td class="wrd_brk" valign="top" rowspan="<?=$btb_row;?>">
														<?
															foreach($btb_ama_arr[$row['BTB_ID']] as $val)
															{
																if($val['amendment_no']==0)
																{
																	echo "<br>";
																}
																else
																{
																	echo "<hr style='border: 1px solid #8DAFDA;'>".$val['amendment_no'];
																}
															}
														?>
													</td>
													<td class="wrd_brk right" valign="top" rowspan="<?=$btb_row;?>">
														<?
															$bal_btb_bal=$row['BTB_VALUE'];
															foreach($btb_ama_arr[$row['BTB_ID']] as $val)
															{
																if($val['amendment_no']==0)
																{
																	echo "<br>";
																}
																else
																{
																	if($val['value_change_by']==2)
																	{
																		echo "<hr style='border: 1px solid #8DAFDA;'>(".number_format($val['amendment_value'],2).")";
																		$bal_btb_bal-=$val['amendment_value'];
																	}
																	else
																	{
																		echo "<hr style='border: 1px solid #8DAFDA;'>".number_format($val['amendment_value'],2);
																		$bal_btb_bal+=$val['amendment_value'];
																	}
																}
															}
															$tot_bal_btb_bal+=$bal_btb_bal;
															$tot_btb_value+=$row['BTB_VALUE'];
														?>
													</td>
													<td class="wrd_brk" valign="top" rowspan="<?=$btb_row;?>">
														<?if($row['PAYTERM_ID']==2){echo number_format($bal_btb_bal,2);}?>
													</td>
													<td class="wrd_brk" valign="top" rowspan="<?=$btb_row;?>">
														<?if($row['PAYTERM_ID']==1){echo number_format($bal_btb_bal,2);}?>
													</td>
													<td class="wrd_brk" valign="top" rowspan="<?=$btb_row;?>">
														<?if($row['PAYTERM_ID']==3){echo number_format($bal_btb_bal,2);}?>
													</td>
													<td class="center" valign="top" rowspan="<?=$btb_row;?>"><?echo change_date_format($row['LAST_SHIPMENT_DATE']);?></td>
												<?
											}
										?>
										<!-- ===============Acceptance================ -->
										<?php
											if(!in_array($row['BTB_ID'].'**'.$row['INVOICE_ID'],$array_check_arr5))
											{
												$array_check_arr5[]=$row['BTB_ID'].'**'.$row['INVOICE_ID'];
												?>
													<td valign="top" rowspan="<?=$invoice_row;?>">
													<?
														$import_pi_id=explode(",",$row['PI_ID']);
														$import_category_id='';
														foreach($import_pi_id as $val)
														{
															$import_category_id.=$piCategoryArr[$val].',';
														}
														$import_category_id=array_unique(explode(",",chop($import_category_id,',')));
														$import_category='';
														foreach($import_category_id as $val){$import_category.=$item_category[$val].', ';}
														echo rtrim($import_category,', ');
													?>
													</td>
													<td valign="top" rowspan="<?=$invoice_row;?>"><?echo $row['INVOICE_NO'];?></td>
													<td class="right" valign="top" rowspan="<?=$invoice_row;?>"><?echo number_format($row['INVOICE_VALUE'],2);$tot_document_rcv+=$row['INVOICE_VALUE'];?></td>
													<td class="center" valign="top" rowspan="<?=$invoice_row;?>"><?echo change_date_format($row['BANK_ACC_DATE']);?></td>
													<td class="center" valign="top" rowspan="<?=$invoice_row;?>"><?echo change_date_format($row['MATURITY_DATE']);?></td>
												<?
											}
											if(!in_array($row['BTB_ID'],$array_check_arr7))
											{
												$array_check_arr7[]=$row['BTB_ID'];
												?>
												<td class="wrd_brk right" valign="top" rowspan="<?=$btb_row;?>" ><?
														$net_btb_bal=$bal_btb_bal-$invoice_value_arr[$lcScId][$btbID]['invoice_value'];
														echo $net_btb_bal;$tot_net_btb_bal+=$net_btb_bal;
														?>
													</td>
												<?

											}
										?>
										<!-- ===============Payment================ -->
										<td><?echo $row['BANK_REF'];?></td>
										<td class="right"><?echo $row['PAYMENT_VALUE'];$tot_payment+=$row['PAYMENT_VALUE'];?></td>
										<td class="center"><?echo change_date_format($row['PAYMENT_DATE']);?></td>
										<?php
											if(!in_array($row['BTB_ID'].'**'.$row['INVOICE_ID'],$array_check_arr6))
											{
												$array_check_arr6[]=$row['BTB_ID'].'**'.$row['INVOICE_ID'];
												$net_payment_bal=$payment_value_arr[$row['LC_SC_ID']][$row['BTB_ID']][$row['INVOICE_ID']]['tot_payment_value'];
												?>
													<td class="right" rowspan="<?=$invoice_row;?>"><?echo number_format($row['INVOICE_VALUE']-$net_payment_bal,2);?></td>
												<?
											}
										?>
									</tr>
								<?
								$i++;
							}
						}
					}
					?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="6">Amount of Export L/C. </th>
						<th><?echo number_format($tot_lc_sc_value,2);?></th>
						<th><?echo number_format($tot_replaced_value,2);?></th>
						<th><?echo number_format($tot_lc_sc_value-$tot_replaced_value,2);?></th>
						<th colspan="4">BTB Total</th>
						<th colspan="3"><?echo number_format($tot_bal_btb_bal,2);?></th>
						<th></th>
						<th colspan="2">Acceptance Total </th>
						<th><?echo number_format($tot_document_rcv,2);?></th>
						<th colspan="2">Acceptance Bal</th>
						<th><?echo number_format($tot_net_btb_bal,2);?></th>
						<th>Total Payment</th>
						<th><?echo number_format($tot_payment,2);?></th>
						<th>Balance</th>
						<th><?echo number_format($tot_document_rcv-$tot_payment,2);?></th>
					</tr>
					<tr>
						<th colspan="7">Back to Back L/C Open</th>
						<th colspan="2"><?echo number_format($tot_btb_value,2);?></th>
					</tr>
					<tr>
						<th colspan="7">% against Export L/C Value</th>
						<th colspan="2"><?echo number_format($tot_btb_value/$tot_replaced_value,2);?></th>
					</tr>
				</tfoot>
			</table>			
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
    $create_new_doc = fopen($filename, 'w') or die('canot open');
    $is_created = fwrite($create_new_doc,ob_get_contents()) or die('canot write');
    echo "$html####$filename####$report_type";
    exit();
}

if ($action=="report_generate2")  //show 2
{
    $process = array( &$_POST );
    extract(check_magic_quote_gpc( $process ));
	$cbo_company_id=str_replace("'","",$cbo_company_id);
	$lc_sc_no=str_replace("'","",$txt_lc_sc_no);
	$cbo_search_by=str_replace("'","",$cbo_search_by);
	
	$companyArr = return_library_array("select id,company_name from lib_company ","id","company_name"); 
	$buyerArr = return_library_array("select id,buyer_name from lib_buyer where status_active=1 ","id","buyer_name");
	$supplier_lib=return_library_array( "select id, supplier_name from lib_supplier",'id','supplier_name');
	$lc_attch_sc=$sc_id='';
	$converte_sc_data_arr=$replaced_sc_amount_arr=array();
	if($cbo_search_by==2)
	{
		$lcAttchSc_sql="SELECT b.id as LC_SC_ID, b.BUYER_NAME, b.CONTRACT_NO as LC_SC_NO, b.CONTRACT_DATE as LC_SC_DATE, b.CONTRACT_VALUE as LC_SC_VALUE 
		from COM_SALES_CONTRACT b
		where  b.id=$txt_lc_sc_id and b.beneficiary_name=$cbo_company_id  and b.status_active=1 and b.is_deleted=0";
		// echo $lcAttchSc_sql;//die;
		$lcAttchSc_result=sql_select($lcAttchSc_sql);
		foreach($lcAttchSc_result as $row)
		{
			$lc_attch_sc.=$row['COM_EXPORT_LC_ID'].',';
			$sc_id.=$row['LC_SC_ID'].',';

		}
		$lc_attch_sc=rtrim($lc_attch_sc,',');
	}
	else
	{
		$lcAttchSc_sql="SELECT  b.id as LC_SC_ID,b.BUYER_NAME, b.EXPORT_LC_NO as LC_SC_NO, b.LC_DATE as LC_SC_DATE, b.LC_VALUE as LC_SC_VALUE 
		from  COM_EXPORT_LC b 
		where b.id=$txt_lc_sc_id and b.beneficiary_name=$cbo_company_id and b.status_active=1 and b.is_deleted=0";
		// echo $lcAttchSc_sql;//die;
		$lcAttchSc_result=sql_select($lcAttchSc_sql);
		foreach($lcAttchSc_result as $row)
		{
			$sc_id.=$row['LC_SC_ID'].',';
		}
	}
	if($lc_attch_sc=='')
	{
		if($cbo_search_by==1)
		{
			// $main_sql="SELECT A.ID AS EXPORT_LC_ID , C.ID AS BTB_ID , C.LC_NUMBER,C.LC_VALUE ,c.SUPPLIER_ID ,c.PAYTERM_ID,SUM (D.CURRENT_ACCEPTANCE_VALUE) AS CURRENT_ACCEPTANCE_VALUE
			// FROM COM_EXPORT_LC A ,COM_BTB_EXPORT_LC_ATTACHMENT B ,COM_BTB_LC_MASTER_DETAILS C,COM_IMPORT_INVOICE_DTLS D 
			// WHERE A.ID = B.LC_SC_ID AND B.IMPORT_MST_ID=C.ID AND C.ID = D.BTB_LC_ID  AND A.ID = $txt_lc_sc_id and
			// c.IMPORTER_ID =$cbo_company_id and b.IS_LC_SC =0
			// AND A.STATUS_ACTIVE=1 AND A.IS_DELETED =0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED =0 AND C.STATUS_ACTIVE=1 AND C.IS_DELETED =0 AND D.STATUS_ACTIVE=1 AND D.IS_DELETED =0 GROUP BY A.ID  , C.ID  , C.LC_NUMBER,C.LC_VALUE,c.SUPPLIER_ID,c.PAYTERM_ID";

			$main_sql = "SELECT A.ID AS EXPORT_LC_ID ,a.BUYER_NAME, C.ID AS BTB_ID , C.LC_NUMBER,C.LC_VALUE ,c.SUPPLIER_ID ,
			SUM (D.CURRENT_ACCEPTANCE_VALUE) AS CURRENT_ACCEPTANCE_VALUE ,e.ACCEPTED_AMMOUNT
			FROM COM_EXPORT_LC A 
			left join COM_BTB_EXPORT_LC_ATTACHMENT b on b.LC_SC_ID=a.id and b.is_lc_sc=0 and b.status_active=1
			left join COM_BTB_LC_MASTER_DETAILS c on c.id = b.import_mst_id and c.payterm_id<>1 and c.status_active=1
			left join COM_IMPORT_INVOICE_DTLS D on d.BTB_LC_ID=c.id and d.status_active=1
			left join COM_IMPORT_PAYMENT E on e.lc_id=d.BTB_LC_ID and e.status_active=1
			WHERE  A.ID = $txt_lc_sc_id and c.IMPORTER_ID =$cbo_company_id
			GROUP BY A.ID ,a.BUYER_NAME, C.ID , C.LC_NUMBER,C.LC_VALUE,c.SUPPLIER_ID,e.ACCEPTED_AMMOUNT
			union all
			SELECT A.ID AS EXPORT_LC_ID ,a.BUYER_NAME, C.ID AS BTB_ID , C.LC_NUMBER,C.LC_VALUE ,c.SUPPLIER_ID ,
			SUM (D.CURRENT_ACCEPTANCE_VALUE) AS CURRENT_ACCEPTANCE_VALUE ,e.ACCEPTED_AMMOUNT
			FROM COM_EXPORT_LC A 
			left join COM_BTB_EXPORT_LC_ATTACHMENT b on b.LC_SC_ID=a.id and b.is_lc_sc=0 and b.status_active=1
			left join COM_BTB_LC_MASTER_DETAILS c on c.id = b.import_mst_id and c.payterm_id=1 and c.status_active=1
			left join COM_IMPORT_INVOICE_DTLS D on d.BTB_LC_ID=c.id and d.status_active=1
			left join COM_IMPORT_PAYMENT_COM E on e.lc_id=d.BTB_LC_ID and e.status_active=1
			WHERE  A.ID = $txt_lc_sc_id and c.IMPORTER_ID =$cbo_company_id
			GROUP BY A.ID ,a.BUYER_NAME, C.ID , C.LC_NUMBER,C.LC_VALUE,c.SUPPLIER_ID,e.ACCEPTED_AMMOUNT ";
		}
		else
		{
			$main_sql = "SELECT A.ID AS EXPORT_LC_ID , a.BUYER_NAME, C.ID AS BTB_ID , C.LC_NUMBER,C.LC_VALUE ,c.SUPPLIER_ID ,
			SUM (D.CURRENT_ACCEPTANCE_VALUE) AS CURRENT_ACCEPTANCE_VALUE ,e.ACCEPTED_AMMOUNT
			FROM com_sales_contract A 
			left join COM_BTB_EXPORT_LC_ATTACHMENT b on b.LC_SC_ID=a.id and b.is_lc_sc=1 and b.status_active=1
			left join COM_BTB_LC_MASTER_DETAILS c on c.id = b.import_mst_id and c.payterm_id<>1 and c.status_active=1
			left join COM_IMPORT_INVOICE_DTLS D on d.BTB_LC_ID=c.id and d.status_active=1
			left join COM_IMPORT_PAYMENT E on e.lc_id=d.BTB_LC_ID and e.status_active=1
			WHERE  A.ID = $txt_lc_sc_id and c.IMPORTER_ID =$cbo_company_id
			GROUP BY A.ID ,a.BUYER_NAME, C.ID , C.LC_NUMBER,C.LC_VALUE,c.SUPPLIER_ID,e.ACCEPTED_AMMOUNT
			union all
			SELECT A.ID AS EXPORT_LC_ID , a.BUYER_NAME, C.ID AS BTB_ID , C.LC_NUMBER,C.LC_VALUE ,c.SUPPLIER_ID ,
			SUM (D.CURRENT_ACCEPTANCE_VALUE) AS CURRENT_ACCEPTANCE_VALUE ,e.ACCEPTED_AMMOUNT
			FROM com_sales_contract A 
			left join COM_BTB_EXPORT_LC_ATTACHMENT b on b.LC_SC_ID=a.id and b.is_lc_sc=1 and b.status_active=1
			left join COM_BTB_LC_MASTER_DETAILS c on c.id = b.import_mst_id and c.payterm_id=1 and c.status_active=1
			left join COM_IMPORT_INVOICE_DTLS D on d.BTB_LC_ID=c.id and d.status_active=1
			left join COM_IMPORT_PAYMENT_COM E on e.lc_id=d.BTB_LC_ID and e.status_active=1
			WHERE  A.ID = $txt_lc_sc_id and c.IMPORTER_ID =$cbo_company_id
			GROUP BY A.ID ,a.BUYER_NAME, C.ID , C.LC_NUMBER,C.LC_VALUE,c.SUPPLIER_ID,e.ACCEPTED_AMMOUNT ";

		}
	}
	else
	{
		// $main_sql="SELECT A.ID AS EXPORT_LC_ID , C.ID AS BTB_ID , C.LC_NUMBER,C.LC_VALUE ,c.SUPPLIER_ID,c.PAYTERM_ID ,SUM (D.CURRENT_ACCEPTANCE_VALUE) AS CURRENT_ACCEPTANCE_VALUE
		// FROM COM_EXPORT_LC A ,COM_BTB_EXPORT_LC_ATTACHMENT B ,COM_BTB_LC_MASTER_DETAILS C,COM_IMPORT_INVOICE_DTLS D 
		// WHERE A.ID = B.LC_SC_ID AND B.IMPORT_MST_ID=C.ID AND C.ID = D.BTB_LC_ID  AND A.ID = $txt_lc_sc_id and
		// c.IMPORTER_ID =$cbo_company_id AND  b.IS_LC_SC =0
		// and a.status_active=1 and a.is_deleted =0 and b.status_active=1 and b.is_deleted =0 and c.status_active=1 and c.is_deleted =0 GROUP BY A.ID  , C.ID  , C.LC_NUMBER,C.LC_VALUE,c.SUPPLIER_ID,c.PAYTERM_ID";
		$main_sql = "SELECT A.ID AS EXPORT_LC_ID ,a.BUYER_NAME, C.ID AS BTB_ID , C.LC_NUMBER,C.LC_VALUE ,c.SUPPLIER_ID ,
		SUM (D.CURRENT_ACCEPTANCE_VALUE) AS CURRENT_ACCEPTANCE_VALUE ,SUM (e.ACCEPTED_AMMOUNT) AS ACCEPTED_AMMOUNT 
		FROM COM_EXPORT_LC A 
		left join COM_BTB_EXPORT_LC_ATTACHMENT b on b.LC_SC_ID=a.id and b.is_lc_sc=0 and b.status_active=1
		left join COM_BTB_LC_MASTER_DETAILS c on c.id = b.import_mst_id and c.payterm_id<>1 and c.status_active=1
		left join COM_IMPORT_INVOICE_DTLS D on d.BTB_LC_ID=c.id and d.status_active=1
		left join COM_IMPORT_PAYMENT E on e.lc_id=d.BTB_LC_ID and e.status_active=1
		WHERE  A.ID = $txt_lc_sc_id and c.IMPORTER_ID =$cbo_company_id
		GROUP BY A.ID , a.BUYER_NAME,C.ID , C.LC_NUMBER,C.LC_VALUE,c.SUPPLIER_ID
		union all
		SELECT A.ID AS EXPORT_LC_ID ,a.BUYER_NAME, C.ID AS BTB_ID , C.LC_NUMBER,C.LC_VALUE ,c.SUPPLIER_ID ,
		SUM (D.CURRENT_ACCEPTANCE_VALUE) AS CURRENT_ACCEPTANCE_VALUE ,SUM (e.ACCEPTED_AMMOUNT) AS ACCEPTED_AMMOUNT 
		FROM COM_EXPORT_LC A 
		left join COM_BTB_EXPORT_LC_ATTACHMENT b on b.LC_SC_ID=a.id and b.is_lc_sc=0 and b.status_active=1
		left join COM_BTB_LC_MASTER_DETAILS c on c.id = b.import_mst_id and c.payterm_id=1 and c.status_active=1
		left join COM_IMPORT_INVOICE_DTLS D on d.BTB_LC_ID=c.id and d.status_active=1
		left join COM_IMPORT_PAYMENT_COM E on e.lc_id=d.BTB_LC_ID and e.status_active=1
		WHERE  A.ID = $txt_lc_sc_id and c.IMPORTER_ID =$cbo_company_id
		GROUP BY A.ID ,a.BUYER_NAME, C.ID , C.LC_NUMBER,C.LC_VALUE,c.SUPPLIER_ID ";
	}
	//echo $main_sql;//die;
	$main_result=sql_select($main_sql);

	foreach($main_result as $row_order)
	{
		$btb_ids .= $row_order['BTB_ID'].',';
		$buyer_id = $row_order['BUYER_NAME'];
	}
	$all_btb_ids = ltrim(implode(",", array_unique(explode(",", chop($btb_ids, ",")))), ',');

	$accepted_amnt_sql = "SELECT E.LC_ID, sum(e.ACCEPTED_AMMOUNT) as accpeted_Amnt 
	FROM COM_IMPORT_PAYMENT_com e 
	WHERE e.LC_ID in($all_btb_ids) and e.status_active=1 and e.is_deleted =0 GROUP BY E.LC_ID
	";

	//echo $accepted_amnt_sql;


	$all_data_arr=$invoice_value_arr=array();$payment_value_arr=array();
	if($cbo_search_by==2){
		$order_sc_lc_sql = "SELECT sum(a.ATTACHED_QNTY) as TOTAL_QTY FROM COM_SALES_CONTRACT_ORDER_INFO a WHERE a.COM_SALES_CONTRACT_ID in ($txt_lc_sc_id) and a.status_active = 1 and a.is_deleted =0 "; 
	}
	else
	{
		$order_sc_lc_sql = "SELECT sum(a.ATTACHED_QNTY) as TOTAL_QTY FROM COM_EXPORT_LC_ORDER_INFO a WHERE a.COM_EXPORT_LC_ID in ($txt_lc_sc_id) and a.status_active = 1 and a.is_deleted =0 "; 
	}
	
	//echo $order_sc_lc_sql;
	$order_sc_lc_result=sql_select($order_sc_lc_sql);

	
	$btb_lc_sc = "SELECT sum(A.LC_VALUE) as TOTAL_BTB_VAL 
	FROM COM_BTB_LC_MASTER_DETAILS A, COM_BTB_EXPORT_LC_ATTACHMENT B
	WHERE A.ID=B.IMPORT_MST_ID AND B.LC_SC_ID IN ($txt_lc_sc_id) AND IMPORTER_ID = $cbo_company_id  and a.status_active = 1 and a.is_deleted =0 and  b.status_active = 1 and b.is_deleted =0 AND b.is_lc_sc = 0";
	// echo $btb_lc_sc;
	$btb_lc_sc_result=sql_select($btb_lc_sc);


	$total_real_amnt = " SELECT a.BANK_REF_NO, b.INVOICE_ID, SUM(B.NET_INVO_VALUE) AS TOTAL_REAL_AMNT
	FROM COM_EXPORT_DOC_SUBMISSION_MST A, COM_EXPORT_DOC_SUBMISSION_INVO B 
	WHERE A.ID=B.DOC_SUBMISSION_MST_ID AND B.LC_SC_ID = $txt_lc_sc_id AND a.COMPANY_ID = $cbo_company_id and 
	A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 AND B.STATUS_ACTIVE =1 AND B.IS_DELETED =0 group by a.BANK_REF_NO, b.INVOICE_ID";
	// echo $total_real_amnt ;
	$total_real_result=sql_select($total_real_amnt);
	$Fdc_Bill_Arr=array();
	foreach($total_real_result as $row){
		$Fdc_Bill_Arr[$row["INVOICE_ID"]]["BANK_REF_NO"]=$row["BANK_REF_NO"];
	}

	$doc_cur_sql = " SELECT  SUM(D.DOCUMENT_CURRENCY) AS DOCUMENT_CURRENCY
	FROM COM_EXPORT_PROCEED_REALIZATION A, COM_EXPORT_DOC_SUBMISSION_MST B, COM_EXPORT_DOC_SUBMISSION_INVO C ,COM_EXPORT_PROCEED_RLZN_DTLS D
	WHERE A.INVOICE_BILL_ID=B.ID AND A.ID=D.MST_ID AND A.IS_INVOICE_BILL=1 AND B.ID=C.DOC_SUBMISSION_MST_ID  AND A.STATUS_ACTIVE=1 AND A.IS_DELETED=0  and c.IS_LC=1
	AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND A.IS_PARTIAL=0 AND A.BENIFICIARY_ID=$cbo_company_id   AND C.LC_SC_ID = $txt_lc_sc_id";
	$doc_cur_result=sql_select($doc_cur_sql);
	// echo $doc_cur_sql;

	$inv_sql = "SELECT A.ID,A.INVOICE_NO,A.BENIFICIARY_ID,sum(a.INVOICE_QUANTITY) AS INV_QTY , sum(a.NET_INVO_VALUE) as INV_VAL  FROM com_export_invoice_ship_mst a WHERE A.LC_SC_ID = $txt_lc_sc_id AND A.BENIFICIARY_ID = $cbo_company_id  and a.BUYER_ID = $buyer_id  AND 
	A.STATUS_ACTIVE=1 AND A.IS_DELETED=0 group by  A.ID,A.INVOICE_NO,A.BENIFICIARY_ID ";
	// echo $inv_sql;
	$inv_result=sql_select($inv_sql);

	foreach($inv_result as $row){
		$inv_qty += $row['INV_QTY'];
		$inv_val += $row['INV_VAL'];
	}

	$real_status_sql = "SELECT A.ID, A.BENIFICIARY_ID,e.id, e.INVOICE_NO ,sum(d.DOCUMENT_CURRENCY) as DOCUMENT_CURRENCY
	FROM COM_EXPORT_PROCEED_REALIZATION A, COM_EXPORT_DOC_SUBMISSION_MST B, COM_EXPORT_DOC_SUBMISSION_INVO C ,COM_EXPORT_PROCEED_RLZN_DTLS D,
	com_export_invoice_ship_mst e 
	WHERE A.INVOICE_BILL_ID=B.ID AND A.ID=D.MST_ID AND c.INVOICE_ID = e.id and A.IS_INVOICE_BILL=1 AND B.ID=C.DOC_SUBMISSION_MST_ID AND A.STATUS_ACTIVE=1 
	AND A.IS_DELETED=0 AND B.STATUS_ACTIVE=1 AND B.IS_DELETED=0 AND C.STATUS_ACTIVE=1 AND C.IS_DELETED=0 AND A.IS_PARTIAL=0 
	AND A.BENIFICIARY_ID=$cbo_company_id AND C.LC_SC_ID = $txt_lc_sc_id GROUP BY A.ID, A.BENIFICIARY_ID,e.id, e.INVOICE_NO  ORDER BY A.ID DESC  ";
	//echo $real_status_sql;
	$real_status_sql_result=sql_select($real_status_sql);
	foreach($real_status_sql_result as $row){
		$realization_amnt_arr[$row['ID']]['DOCUMENT_CURRENCY'] = $row['DOCUMENT_CURRENCY'];
	}
	ob_start();		

	?>
	<style>
		.wrd_brk{word-break: break-all;}
		.left{text-align: left;}
		.center{text-align: center;}
		.right{text-align: right;}
		.bold{font-weight:bold;}
	</style>

	<div id="scroll_body" align="center" style="height:auto; width:1200px; margin:0 auto; padding:0;">
		<table width="1200px" >
			<tr>
				<td colspan="12" class="center" style="font-size:Large; font-weight:bold;"><strong><u><? echo $companyArr[$cbo_company_id]; ?></u></strong></td>
			</tr>
			<tr>
				<td colspan="12" class="center"  style="font-size:18px"><strong><u>SUMMURY OF LC/SC</u></strong></td>
			</tr>

		</table>
		<table cellspacing="0" width="1200"  border="1" rules="all" class="rpt_table" >
			<tr bgcolor="#E9F3FF">
				<td colspan="2" width="200"><strong>EXPORT LC/SC NUMBER:</strong></td>
				<td colspan="2" align="left" width="200" class=" bold"><?echo $lcAttchSc_result[0]['LC_SC_NO'];?></td>  
				<td width="200" align="left" class="bold">BUYER NAME:</td>  
				<td colspan="2" align="left" width="200" class=" bold"><?echo $buyerArr[$lcAttchSc_result[0]['BUYER_NAME']];?></td>  
				<td colspan="2" width="200" align="left" ><strong>Export LC/SC Date:</strong></td>
				<td colspan="2" width="200" align="left" class=" bold"><? echo change_date_format($lcAttchSc_result[0]['LC_SC_DATE']) ;?></td>
			
			</tr>

		</table>
		<table cellspacing="0" width="1200"  rules="all" class="rpt_table" style="border-collapse: collapse; border: none;" border="1">
			<tbody>
			<tr>
				<td colspan="2" width="200">EXPORT LC/SC VALUE:</td>   
				<td colspan="2" width="200" class=" bold"><?echo "$ ".number_format($lcAttchSc_result[0]['LC_SC_VALUE'],2); ?>&nbsp;</td>
				<td colspan="2" width="200">E_LC/SC QUANTITY:</td>
				<td colspan="2" width="200" class=" bold"><?echo $order_sc_lc_result[0]['TOTAL_QTY']." PCS"; ?>&nbsp;</td>
				<td colspan="2" width="200" style="border: none;"></td>
				<td colspan="2" width="200" style="border: none;"></td>
			</tr>
			<tr>
				<td colspan="2" >TOTAL BTB LC/SC VALUE:</td>
				<td colspan="2" class=" bold"><?echo "$ ".number_format($btb_lc_sc_result[0]['TOTAL_BTB_VAL'],2); ?>&nbsp;</td>  
				<td colspan="2" >TOTAL  INV. VALUE:</td>
				<td colspan="2" class=" bold"><?echo "$ ".number_format($inv_val,2); ?>&nbsp;</td>
				<td colspan="2">DELIVERED QUQNTITY:</td>
				<td colspan="2"class=" bold" title="Export Inv Qty"><?echo $inv_qty." PCS"; ?>&nbsp;</td>
			</tr>
			
			<tr>
				<td colspan="2">SHORT/EXCESS QUANTITY</td>
				<td colspan="2" class=" bold" title="Delevered Qty - Export LC Qty" ><? $short_qty = $inv_qty- $order_sc_lc_result[0]['TOTAL_QTY'] ; 
				echo $short_qty." PCS";?>&nbsp;</td>
				<td colspan="2">SHORT/EXCESS VALUE:</td>
				<td colspan="2" class=" bold" title="Total Inv. Value - Export Lc Value"><? $short_qty = $inv_val- $lcAttchSc_result[0]['LC_SC_VALUE'] ; 
				echo $short_qty." PCS";?>&nbsp;</td>
				<td colspan="2">IMPORT PERSANTAGE:</td>
				<td colspan="2"class=" bold" title="(Total BTB LC Val/Export LC Val)*100"><? $import_perc = ($btb_lc_sc_result[0]['TOTAL_BTB_VAL'] * 100) / $lcAttchSc_result[0]['LC_SC_VALUE']; 
				echo number_format($import_perc,2)." %";
				?>&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2">REALIZED AMOUNT:</td>
				<td colspan="2" class=" bold"><? echo number_format($doc_cur_result[0]['DOCUMENT_CURRENCY'],2);?>&nbsp;</td>
				<td colspan="2">UNREALIZED AMOUNT:</td>
				<td colspan="2" class=" bold"><?
				// $un_real_val = $total_real_result[0]['TOTAL_REAL_AMNT'] -$doc_cur_result[0]['DOCUMENT_CURRENCY']; echo number_format($un_real_val,2);
				 $un_real_val = $inv_val -$doc_cur_result[0]['DOCUMENT_CURRENCY']; echo number_format($un_real_val,2);
				 
				 ?>&nbsp;</td>
				<td colspan="2">SHORT REALIZED:</td>
				<td colspan="2" class=" bold" title="Realized Value - Total Inv Value"><?
				$shrt_ralized = $doc_cur_result[0]['DOCUMENT_CURRENCY'] - $inv_val;
				echo number_format($shrt_ralized,2);?>&nbsp;</td>
			</tr>
			</tbody>
		</table>	
		<br>
				
	</div>

	<div style=" float: left; width: 50%;">	
		<table cellspacing="0" width="600"  border="1" rules="all" class="rpt_table">
			<thead>
				<tr>
					<th colspan="6">BTB/IMPORT</th>
				</tr>
				<tr>
					<th width="100" >LC/SC NUMBER</th>
					<th width="100" >TOTAL LC/SC VALUE</th>
					<th width="100" >TOTAL ACCEPTANCE VALUE</th>
					<th width="100" >PAYMENT</th>
					<th width="100" >SUPPLIER NAME</th>
	
				</tr>
			</thead>
			<tbody>
				<?
				$i=1;
		
				foreach($main_result as $row)
				{
					if (fmod($i,2)==0) $bgcolor='#E9F3FF';
					else $bgcolor='#FFFFFF';
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="change_color('tr_<? echo $i; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $i; ?>">
							<td><?echo $row['LC_NUMBER']?></td>
							<td class="right"><?echo number_format($row['LC_VALUE'],2);?>&nbsp;</td>
							<td class="right"><? echo number_format($row['CURRENT_ACCEPTANCE_VALUE'],2);?>&nbsp;</td>
							<td class="right"><?echo number_format($row['CURRENT_ACCEPTANCE_VALUE'],2);//number_format($row['ACCEPTED_AMMOUNT'],2); khalid ?>&nbsp;</td>
							<td class="center"><? echo $supplier_lib[$row['SUPPLIER_ID']]?></td>

						</tr>

					<?
					$i++;
				}?>		
						
			</tbody>
		</table>	
	</div>

	<div  style=" float: left; width: 50%;">
		<table cellspacing="0" width="600"  border="1" rules="all" class="rpt_table">
			<thead>
				<tr>
					<th colspan="5">EXPORT</th>
				</tr>
				<tr>
					<th width="100" >INVOICE NUMBER</th>
					<th width="100" >INVOICE VALUE</th>
					<th width="100" >INVOICED QUANTITY</th>
					<th width="100" >REALIZATION STATUS</th>
					<th width="100" >FDBC BILL NO</th>
				</tr>
				
			</thead>
			<tbody>
			<?
				$j=1;
		
				foreach($inv_result as $row)
				{
					if (fmod($j,2)==0) $bgcolor='#E9F3FF';
					else $bgcolor='#FFFFFF';
					?>
						<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" onClick="change_color('tr_<? echo $j; ?>','<? echo $bgcolor; ?>')" id="tr_<? echo $j; ?>">
							<td class="center"><?echo $row['INVOICE_NO'];?></td>
							<td class="right"><?echo number_format($row['INV_VAL'],2);?>&nbsp;</td>
							<td class="right"><?echo number_format($row['INV_QTY'],2)." PCS";?>&nbsp;</td>
							<td class="center"><?
							$realized_amnt = $realization_amnt_arr[$row['ID']]['DOCUMENT_CURRENCY'];
							if($realized_amnt>0) echo "REALIZED"; else  echo "UNREALIZED";?></td>
							<td class="center"><?echo $Fdc_Bill_Arr[$row["ID"]]["BANK_REF_NO"];?>&nbsp;</td>
						</tr>
					<?
					$j++;
				}?>		
			</tbody>
		</table>	
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
    $create_new_doc = fopen($filename, 'w') or die('canot open');
    $is_created = fwrite($create_new_doc,ob_get_contents()) or die('canot write');
    echo "$html####$filename####$report_type";
    exit();
}

?>