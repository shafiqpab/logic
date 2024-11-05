<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="btb_application_form")
{

	$sql = "select	importer_id,issuing_bank_id,lc_value,currency_id,tenor,supplier_id,item_category_id,pi_id,port_of_loading,port_of_discharge,delivery_mode_id,last_shipment_date,lc_expiry_date,inco_term_place,doc_presentation_days,origin,inco_term_id,insurance_company_name,cover_note_no,cover_note_date from com_btb_lc_master_details where id='$data'";
	$data_array=sql_select($sql); 

	//echo "select lc_sc_id, is_lc_sc from com_btb_export_lc_attachment where import_mst_id=$data and is_deleted=0 and status_active=1";
	$is_lc_sc_sql=sql_select("select lc_sc_id, is_lc_sc from com_btb_export_lc_attachment where import_mst_id=$data and is_deleted=0 and status_active=1");
	if(empty($is_lc_sc_sql)) {echo "May be attachment not complete yet";die;}
	foreach($is_lc_sc_sql as $row)
	{
		if($row[csf('is_lc_sc')]==0){
			$sql_lc=sql_select("SELECT id,export_lc_no FROM com_export_lc  where id=".$row[csf('lc_sc_id')]);
			foreach($sql_lc as $lc_row)
			{
				$export_lc_sc_no_arr[$lc_row[csf("id")]]=$lc_row[csf("export_lc_no")];
			}
		}
		else{
			$sql_sc=sql_select("SELECT id,contract_no FROM com_sales_contract where id=".$row[csf('lc_sc_id')]);
			foreach($sql_sc as $sc_row)
			{
				$export_lc_sc_no_arr[$sc_row[csf("id")]]=$sc_row[csf("contract_no")];
			}
		}
		
		$lc_sc_id_arr[]=$row[csf('lc_sc_id')];

	}


	$order_pcs_qty = return_field_value("sum(b.order_quantity) as order_quantity","com_export_lc_order_info a,wo_po_color_size_breakdown b","b.po_break_down_id=a.wo_po_break_down_id and a.com_export_lc_id in(".implode(',',$lc_sc_id_arr).")","order_quantity");


//--------------lib
	$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');
	$company_name = return_field_value("company_name","lib_company","id=".$data_array[0][csf("importer_id")],"company_name");
	$company_add = return_field_value("city","lib_company","id=".$data_array[0][csf("importer_id")],"city");

	$branch = return_field_value("branch_name","lib_bank","id=".$data_array[0][csf("issuing_bank_id")],"branch_name");
	$currency_sign = $currency_sign_arr[$data_array[0][csf("currency_id")]];
	
	
	$supplier_name = return_field_value("supplier_name","lib_supplier","id=".$data_array[0][csf("supplier_id")],"supplier_name");
	$supplier_add = return_field_value("address_1","lib_supplier","id=".$data_array[0][csf("supplier_id")],"address_1");
	
	
	$pi_number_arr=return_library_array( "select id, pi_number from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','pi_number');
	
	$pi_date = return_field_value("pi_date","com_pi_master_details"," status_active = 1 AND is_deleted = 0 AND id=".$data_array[0][csf("pi_id")],"pi_date");
	
	
	
	$total_pi=count($pi_number_arr);
	if($total_pi==1){
		$pi_number = $pi_number_arr[$data_array[0][csf("pi_id")]];
		$pi_date=date('d.m.Y',strtotime($pi_date));
	}
	else
	{
		$pi_number = $total_pi .' PI Attached';
		$pi_date='';
	}
	if($data_array[0][csf("last_shipment_date")]!=''){
		$last_shipment_date=date('d.m.Y',strtotime($data_array[0][csf("last_shipment_date")]));
	}
	else
	{
		$last_shipment_date='';
	}
	
	
	if($data_array[0][csf("lc_expiry_date")]!=''){
		$lc_expiry_date=date('d.m.Y',strtotime($data_array[0][csf("lc_expiry_date")]));
	}
	else
	{
		$lc_expiry_date='';
	}
	$origin = return_field_value("country_name"," lib_country","id=".$data_array[0][csf("origin")],"country_name");
	
	$inco_term_id=$incoterm[$data_array[0][csf("inco_term_id")]];
	
	
	
	if($data_array[0][csf("cover_note_date")]!=''){
		$cover_note_date=date('d.m.Y',strtotime($data_array[0][csf("cover_note_date")]));
	}
	else
	{
		$cover_note_date='';
	}
	
	
	
	?>
    
<style>
/*printer setup 0,0,0,0*/
/*21cm,33.5cm    21.5 34.5*/
.height{ height:15px!important;}

#form_body{
	background:url(../application_form/form_image/topCut.gif);
	width:8.5in;
	height:11in;
	overflow:hidden;
	background-repeat: no-repeat;
	background-size:21cm 35cm;
	padding:0;
	margin:0;
}

#form{
	padding-left:0px;margin:2.5cm 1.35cm 0 25px; font-size:12px; height:27.5cm;overflow:hidden;
	font-weight:bold;
}
.clear_both{clear:both; overflow:hidden; }
@media print {#form{ border:none;}}

.branch{ margin:5px 0 0 0;text-align:center; font-size:18px; }
.application_name_aaddress{margin:80px 0 0 0;text-align:center;  width:48%; float:left; height:90px; }
.letter_credit_amount{margin:80px 0 0 0;text-align:center;  width:46%; float:right; height:90px; }
.letter_credit_amount .letter{margin:0 0 0 0;}
.letter_credit_amount .credit{margin:5px 0 0 0;}
.letter_credit_amount .amount{margin:18px 0 0 0;}

.deferred_payment_beneficiary_draft_at{margin:40px 0 0 0;text-align:center;  width:45%; float:left; height:110px;}
.deferred_payment_beneficiary_draft_at .deferred_payment{margin:35px 0 0 0;width:60%; float:right;  }
.deferred_payment_beneficiary_draft_at .beneficiary_draft_at{margin:25px 0 0 0;width:65%; float:right;}
.beneficiary_name_address{margin:40px 0 0 0;text-align:center;  width:50%; float:right; height:110px;  }

.goods_des{ margin:10px 0 0 160px;}

.invoice_no_date_message{ margin:20px 0 0 245px;}
.invoice_no_date_message .invoice_no{ width:25%; text-align:center; float:left;}
.invoice_no_date_message .date{ margin:0 0 0 15px; width:26%; text-align:center; float:left;}
.invoice_no_date_message .message{margin:0 0 0 45px; width:28%; text-align:center; float:left;}

.shipment_from_to_by{ margin:8px 0 0 130px;}
.shipment_from_to_by .from{margin:1px 0 0 0;width:37%; text-align:center; float:left;}
.shipment_from_to_by .to{ margin:1px 0 0 15px; width:37%; text-align:center; float:left;}
.shipment_from_to_by .by{margin:1px 0 0 15px;width:17%; text-align:center; float:left;}

.notLeterThan_expeiryDate_expeiryPlace{ margin:2px 0 0 70px; }
.notLeterThan_expeiryDate_expeiryPlace .notLeterThan{width:27%; text-align:center; float:left;}
.notLeterThan_expeiryDate_expeiryPlace .expeiryDate{margin:0 0 0 70px; width:22%; text-align:center; float:left;}
.notLeterThan_expeiryDate_expeiryPlace .expeiryPlace{margin:0 0 0 75px; width:23%; text-align:center; float:left;}
.presentedWith{ margin:2px 0 0 220px;}

.toBeOf{ margin:55px 0 0 430px;}

.BankOfBangladeshLtd_1{ margin:15px 0 0 640px;}
.BankOfBangladeshLtd_2{ margin:0 0 0 585px;}
.dateMarketFright{ margin:0 0 0 495px;}
.BankOfBangladeshLtd_3{ margin:0 0 0 290px;}
.receiptMarketFright{ margin:0 0 0 190px;}

.underNo{margin:15px 0 0 555px; }

.issuedBy{margin:5px 0 0 100px;}
.MS{margin:17px 0 0 80px;}

.coverNotNo_date{margin:0 0 0 85px;}
.coverNotNo_date .notNo{margin:0 0 0 0; width:22%; float:left; text-align:center;}
.coverNotNo_date .date{margin:0 0 0 15px;width:15%; float:left; text-align:center;}

.packingList{margin:0 0 0 100px;}

.otherDocument{margin:10px 0 0 0; height:45px; text-align:center;}
.additionalInstraction{margin:0 0 0 0; text-align:center;}
.QTY{margin:100px 0 0 35px;}




</style>

<div id="form_body">
<div id="form">
	<div class="branch">&nbsp;<? echo $branch;?></div>
	<div class="application_name_aaddress">
    	<br /><br />&nbsp;<? echo $company_name;?><br /><? echo $company_add;?></div>
	<div class="letter_credit_amount">
        <div class="letter">&nbsp;</div>
        <div class="credit">&nbsp;</div>
        <div class="amount"><br />&nbsp;<? echo $currency_sign.' '.number_format($data_array[0][csf('lc_value')],2);?></div>
    </div>
	<div class="deferred_payment_beneficiary_draft_at">
        <div class="deferred_payment">&nbsp;</div>
        <div class="beneficiary_draft_at"><? echo $data_array[0][csf("tenor")];?> Day's</div>
    </div>
	<div class="beneficiary_name_address"><br /><br /><? echo $supplier_name;?><br /><? echo $supplier_add;?></div>
    <div class="clear_both"></div>
	<div class="goods_des clear_both">&nbsp;<? echo $item_category[$data_array[0][csf("item_category_id")]];?></div>
    <div class="clear_both"></div>
	<div class="invoice_no_date_message clear_both"> 
        <div class="invoice_no"><? echo $pi_number;?></div>
        <div class="date">&nbsp;<? echo $pi_date;?></div>
        <div class="message">&nbsp;</div>
    </div>
	<div class="shipment_from_to_by clear_both"> 
        <div class="from">&nbsp;<? echo $data_array[0][csf("port_of_loading")];?></div>
        <div class="to">&nbsp;<? echo $data_array[0][csf("port_of_discharge")];?></div>
        <div class="by">&nbsp;<? echo $shipment_mode[$data_array[0][csf("delivery_mode_id")]];?></div>
    </div>
	<div class="notLeterThan_expeiryDate_expeiryPlace clear_both"> 
        <div class="notLeterThan">&nbsp;<? echo $last_shipment_date;?></div>
        <div class="expeiryDate">&nbsp;<? echo $lc_expiry_date;?></div>
        <div class="expeiryPlace">&nbsp;<? echo $data_array[0][csf("inco_term_place")];?></div>
    </div>
	<div class="presentedWith clear_both">&nbsp;<? echo $data_array[0][csf("doc_presentation_days")];?></div>
	<div class="toBeOf clear_both height">&nbsp;<? echo $origin;?></div>
	<div class="BankOfBangladeshLtd_1 height clear_both">&nbsp;<? echo $branch;?></div>
	<div class="BankOfBangladeshLtd_2 height clear_both">&nbsp;<? echo $branch;?></div>
	<div class="dateMarketFright height clear_both">&nbsp;<? echo $inco_term_id;?></div>
	<div class="BankOfBangladeshLtd_3 height clear_both">&nbsp;<? echo $branch;?></div>
	<div class="receiptMarketFright height clear_both"><? echo $inco_term_id;?></div>
	<div class="underNo clear_both">&nbsp;</div>
    <div class="issuedBy clear_both">&nbsp;</div>
	<div class="MS clear_both">&nbsp;<? echo $data_array[0][csf("insurance_company_name")];?></div>
	<div class="coverNotNo_date clear_both">
        <div class="notNo">&nbsp;<? echo $data_array[0][csf("cover_note_no")];?></div>
        <div class="date">&nbsp;<? echo $cover_note_date;?></div>
    </div>
	<div class="packingList clear_both"> &nbsp;  </div>
	<div class="otherDocument clear_both">&nbsp; LC/SC: <? echo implode(', ',$export_lc_sc_no_arr);?>  </div>
	<div class="additionalInstraction clear_both">&nbsp;</div>
	<div class="QTY clear_both">&nbsp;Gmts Qty: <? echo number_format($order_pcs_qty);?> pcs </div>
</div>
</div>
    <?
	exit();
}

?>


 