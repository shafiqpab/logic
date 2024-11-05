<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="btb_application_form")
{

	 $sql = "SELECT	importer_id,issuing_bank_id,lc_value,lc_date,currency_id,tenor,supplier_id,item_category_id,pi_id,port_of_loading,port_of_discharge,delivery_mode_id,last_shipment_date,lc_expiry_date,inco_term_place,doc_presentation_days,origin,inco_term_id,insurance_company_name,cover_note_no,cover_note_date,payterm_id,tolerance,lca_no from com_btb_lc_master_details where id='$data'";
	 // echo $sql;
	$data_array=sql_select($sql);

$is_lc_sc_sql=sql_select("SELECT lc_sc_id, is_lc_sc from com_btb_export_lc_attachment where import_mst_id=$data and is_deleted=0 and status_active=1");
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

	$country_array = return_library_array("select id,country_name from lib_country where is_deleted=0","id","country_name");
	//echo "select id,plot_no,road_no,block_no,country_id,city,zip_code,irc_no,tin_number,vat_number,bang_bank_reg_no from lib_company where id = ".$data_array[0][csf('importer_id')]."";
	$address = sql_select("SELECT id,plot_no,level_no,road_no,block_no,country_id,city,zip_code,irc_no,tin_number,vat_number,bang_bank_reg_no from lib_company where id = ".$data_array[0][csf('importer_id')]."");
	foreach($address as $row){
		$company_add[$row[csf('id')]]['plot_no'] = $row[csf('plot_no')];
		$company_add[$row[csf('id')]]['level_no'] = $row[csf('level_no')];
		$company_add[$row[csf('id')]]['road_no'] = $row[csf('road_no')];
		$company_add[$row[csf('id')]]['block_no'] = $row[csf('block_no')];
		$company_add[$row[csf('id')]]['country_id'] = $row[csf('country_id')];
		$company_add[$row[csf('id')]]['city'] = $row[csf('city')];
		$company_add[$row[csf('id')]]['zip_code'] = $row[csf('zip_code')];
		$company_add[$row[csf('id')]]['irc_no'] = $row[csf('irc_no')];
		$company_add[$row[csf('id')]]['tin_number'] = $row[csf('tin_number')];
		$company_add[$row[csf('id')]]['vat_number'] = $row[csf('vat_number')];
		$company_add[$row[csf('id')]]['bang_bank_reg_no'] = $row[csf('bang_bank_reg_no')];
	}
	//print_r($company_add);

	$branch = return_field_value("branch_name","lib_bank","id=".$data_array[0][csf("issuing_bank_id")],"branch_name");
	$currency_sign = $currency_sign_arr[$data_array[0][csf("currency_id")]];


	$supplier_name = return_field_value("supplier_name","lib_supplier","id=".$data_array[0][csf("supplier_id")],"supplier_name");
	$supplier_add = return_field_value("address_1","lib_supplier","id=".$data_array[0][csf("supplier_id")],"address_1");

	//echo "select id, pi_number from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0";
	$pi_number_arr=return_library_array( "SELECT id, pi_number from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','pi_number');

	$pi_date = return_field_value("pi_date","com_pi_master_details"," status_active = 1 AND is_deleted = 0 AND id=".$data_array[0][csf("pi_id")],"pi_date");



	$total_pi=count($pi_number_arr);

	$pi_numbers;

	if($total_pi>1){
		foreach($pi_number_arr as $row){
			$pi_numbers .= $row.", ";
		}
		$pi_numbers=chop($pi_numbers,', ');
		$pi_date='';
	}
	else
	{
		$pi_numbers = $pi_number_arr[$data_array[0][csf("pi_id")]];
		$pi_date=date('d.m.Y',strtotime($pi_date));
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

	if($data_array[0][csf("payterm_id")]==1){
		$pay_term_cond = $pay_term[$data_array[0][csf("payterm_id")]];
	}else{
		$pay_term_cond = $data_array[0][csf("tenor")]. "Day's";
	}
	if($data_array[0][csf("doc_presentation_days")] == ""){
		$doc_presentation_days = "";
	}else{
		$doc_presentation_days = $data_array[0][csf("doc_presentation_days")]."Days";
	}

	$nature=return_field_value("business_nature","lib_company","id=".$data_array[0][csf("importer_id")],"business_nature");
	$business_nature_arr= explode(",", $nature);
	$business_nature;
	if(count($business_nature_arr)>0){
		foreach($business_nature_arr as $row)
		{
			if($row==2){$business_nature .= "Knit ";}
			elseif($row==3){$business_nature .= "Woven ";}
			elseif($row==4){ $business_nature .= "Trims "; }
			elseif($row==5){ $business_nature .= "Print "; }
			elseif($row==6){ $business_nature .= "Embroidery "; }
			elseif($row==7){ $business_nature .= "Wash "; }
			elseif($row==8){ $business_nature .= "Yarn Dyeing "; }
			elseif($row==9){ $business_nature .= "AOP "; }
			elseif($row==100){ $business_nature .= "Sweater "; }
		}
	}  
	?>

<style>
/*printer setup 0,0,0,0*/
/*21cm,33.5cm    21.5 34.5*/
body{margin:0;padding:0; line-height:17px;}
.height{ height:15px!important;}

#form_body{
	background:url(../application_form/form_image/jamuna.jpg);
	width:8.5in;
	height:14in;
	overflow:hidden;
	background-repeat: no-repeat;
	background-size:21.59cm 35.56cm;
	padding:0;
	margin:0;
	margin-top:10px;
}

#form{
	padding-left:0px;margin:0 1.35cm 0 25px; font-size:12px; height:37.5cm;overflow:hidden;
	font-weight:bold;
}
.clear_both{clear:both; overflow:hidden; }
@media print {#form{ border:none;}}

/* .branch{ margin:5px 0 0 0;text-align:center; font-size:18px; } */
.application_name_aaddress{margin-top: 150px; text-align:left;  width:49%;  height:75px;float: left;}
.application_name_aaddress2{margin-top: 128px; text-align:left;  width:49%;  height:75px;float: left;}
.application_name_aaddress2 .amount{text-align: left;padding-left: 200px;}
.application_name_aaddress2 .in_word{text-align: ce;padding-left: 40px;}
.com_address{margin-top: 8px;padding-top:3px;}

.credit_availabe{text-align: left;width: 49%;height: 50px;overflow: hidden;float: left;}
.beneficiary_name_address{text-align: left;width: 49%;height: 50px;overflow: hidden;float: left;}

.deferred_payment_beneficiary_draft_at{padding: 0px 0 0 0;text-align: left;width: 100%;height: 19px;float: left;}
/* .deferred_payment_beneficiary_draft_at .deferred_payment{margin:35px 0 0 0;width:60%; float:right;  } */
.deferred_payment_beneficiary_draft_at .beneficiary_draft_at{margin: 0;width:50%;  text-align:center; float:right;}

.letter_credit_amount{margin:0;text-align:center;  width:100%; height:50px; overflow:hidden; }
/* .letter_credit_amount .letter{margin:0 0 0 0;}
.letter_credit_amount .credit{margin:5px 0 0 0;} */
.letter_credit_amount .amount{margin-top: 5px;width: 55%;text-align: right;padding-right: 15px;overflow: hidden;height: 20px;}
.letter_credit_amount .hard_code_block{height: 22px;width: 95%;text-align: center;padding-top: 10px;float: right;clear: both;}
.goods_des{ text-align: left;margin-top: 170px; padding-left: 155px;}
.invoice_no_date_message{width: 100%;margin-top: 20px;}
.invoice_no_date_message .invoice_no{ width:53%; text-align:right; float:left;}
.invoice_no_date_message .date{ width:33%; text-align:left; float:left; padding-left: 50px;}
.pi_number_data{width: 100%;margin-top: 5px;}
.pi_number_data .invoice_no{ width:53%; text-align:right; float:left;}
.pi_date_data {width: 100%;margin-top: 15px;}
.pi_date_data .date{ width:33%; text-align:left; float:right; padding-left: 50px;}
/* .invoice_no_date_message .message{margin:0 0 0 45px; width:28%; text-align:center; float:left;} */
.toBeOf{ margin:39px 0 0 475px;}
.MS{margin: 149px 0 0 0px;width: 57%;overflow: hidden;float: right;}
.coverNotNo_date{margin:15px 0 0 0px; width: 100%;}
.coverNotNo_date .notNo{margin: 0 0 0 0;width: 75%;float: left;text-align: right;}
.coverNotNo_date .date{margin: 0 0 0 1px;width: 23%;float: left;text-align: center;}


.shipment_from_to_by{ margin: 0;width: 100%;}
.shipment_from_to_by .from{margin: 1px 0 0 0;width: 50%;text-align: center;float: left;padding-top: 13px;padding-left:5px;}
.shipment_from_to_by .to{ margin: 1px 0 0 15px;width: 25%;text-align: center;float: left;padding-top: 13px;}
.shipment_from_to_by .by{margin: 1px 0 0 15px;width: 20%;text-align: center;float: left;padding-top: 13px;}

.doc_presentation_days{ padding-left: 200px; padding-top: 7px;}

.notLeterThan_expeiryDate_expeiryPlace{ margin: 0;width: 100%; }
.notLeterThan_expeiryDate_expeiryPlace .notLeterThan{width: 30%;text-align: center;float: left;margin-top: 0px;}
.notLeterThan_expeiryDate_expeiryPlace .expeiryDate{width: 40%;text-align: center;float: left;margin-top: 0px;}
/* .notLeterThan_expeiryDate_expeiryPlace .expeiryPlace{margin:0 0 0 75px; width:23%; text-align:center; float:left;} */
.presentedWith{ margin: 0px 0 0 0;width: 55%;float: right;text-align: center;}
.lc_no_hard_code_block{width: 100%;text-align: center;margin: 13px 0 0 0;height:35px;}
.lc_sc_no_block{width:100%; text-align:center;margin: 1px 0 0 0;height:17px;}
.lra_no_block{width: 35%;text-align: center;margin-top: 104px;float: left;}
.lca_no_block{width: 35%;text-align: left;margin-top: 104px;float: left;padding-left:10px;}


#p1{
    position: absolute;
	margin-top: 400px;
    margin-left: 250px;
}



</style>

<div id="form_body">
<div id="form">
	<div id="p1">100% Export Oriented <? echo $business_nature; ?> Garments </div>
	<!-- <div class="branch">&nbsp;<? //echo $branch;?></div> -->
	<div>
		<div class="application_name_aaddress">
            <div style="padding-left:15px;">
                <? echo $company_name;?><br />
                <div class="com_address">
                    <? echo $company_add[$data_array[0][csf("importer_id")]]['plot_no'].",".$company_add[$data_array[0][csf("importer_id")]]['level_no'].",".$company_add[$data_array[0][csf("importer_id")]]['road_no'].",".$company_add[$data_array[0][csf("importer_id")]]['city'].",".$country_array[$company_add[$data_array[0][csf("importer_id")]]['country_id']]; ?>
                </div>
            </div>
		</div>
		<div class="application_name_aaddress2">
	    	<div class="amount"><? echo $currency_sign.' '.number_format($data_array[0][csf('lc_value')],2);?></div>
			<div class="in_word">
				<?
				 $in_word = number_to_words($data_array[0][csf('lc_value')]);
				 echo  $in_word;
				 ?>
			</div>
		</div>

	</div>
	<br clear="all">

	<div style="width: 100%;margin-top: 85px;">
		<div class="credit_availabe">

		</div>
		<div class="beneficiary_name_address">
			<div style="padding-left: 50px;">
				<div><? echo $supplier_name;?><br>
				<? echo $supplier_add;?></div>
				<br clear="all">
			</div>
		</div>
	</div>
	<!-- <div class="deferred_payment_beneficiary_draft_at">
        <div class="beneficiary_draft_at"><? echo $pay_term_cond; //$data_array[0][csf("tenor")];?> </div>
    </div> -->
	<!-- <div class="letter_credit_amount clear_both">
         <div class="letter">&nbsp;</div>
        <div class="credit">&nbsp;</div>
        <div class="amount"><? //echo $currency_sign.' '.number_format($data_array[0][csf('lc_value')],2);?></div>
		<div class="hard_code_block">AS PER PROFORMA INVOICE</div>
    </div> -->

	<div class="goods_des clear_both">&nbsp;<? echo $item_category[$data_array[0][csf("item_category_id")]];?></div>

	<? 
		if($total_pi>1)
		{ 
			?>
				<div class="pi_number_data clear_both">
					<div class="invoice_no"><? echo $pi_numbers;?></div>
				</div>
				<div class="pi_number_data clear_both">
					<div class="date"><? echo $pi_date;?></div>
				</div>
			<?
		}
		else
		{ 
			?>
				<div class="invoice_no_date_message clear_both">
					<div class="invoice_no"><? echo $pi_numbers;?></div>
					<div class="date"><? echo $pi_date;?></div>
				</div>
			<?
		}
	?>
	<!-- <div class="invoice_no_date_message clear_both">
        <div class="invoice_no"><? echo chop($pi_numbers, ",");?></div>
        <div class="date"><? echo $pi_date;?></div>
         <div class="message">&nbsp;</div> 
    </div> -->


	<div class="shipment_from_to_by clear_both">
        <div class="from">&nbsp;<? echo $data_array[0][csf("port_of_loading")];?></div>
        <div class="to">&nbsp;<? echo $data_array[0][csf("port_of_discharge")];?></div>
        <div class="by">&nbsp;<? echo $shipment_mode[$data_array[0][csf("delivery_mode_id")]];?></div>
    </div>

	<div class="notLeterThan_expeiryDate_expeiryPlace clear_both">
        <div class="notLeterThan">&nbsp;<? echo $last_shipment_date;?></div>
        <div class="expeiryDate">&nbsp;<? echo $lc_expiry_date;?></div>
        <!-- <div class="expeiryPlace">&nbsp;<? //echo $data_array[0][csf("inco_term_place")];?></div> -->
    </div>

    <div class="doc_presentation_days clear_both"><? echo $doc_presentation_days;?>.</div>

	<!-- <div class="toBeOf clear_both height">&nbsp;<? echo $origin;?></div>
	<div class="MS clear_both">&nbsp;<? echo $data_array[0][csf("insurance_company_name")];?></div>
	<div class="clear_both"></div>
	<div class="coverNotNo_date clear_both">
        <div class="notNo"><? echo $data_array[0][csf("cover_note_no")];?></div>
        <div class="date">Date: <? echo $cover_note_date;?></div>
    </div>
	<div class="lc_no_hard_code_block">
		<div class="additionla_docs_block" >THIS BTB LC WILL BE OPENED AGAINST PURCHASE CONTRACT NO: <span class="lc_no"><? echo implode(', ',$export_lc_sc_no_arr);?></span></div>
		<div class="lc_sc_no_block"><? echo implode(', ',$export_lc_sc_no_arr);?>  &nbsp;Dated: <? echo $data_array[0][csf("lc_date")]; ?></div>
	</div> -->
	<div class="clear_both" style="margin-top: 350px;"></div>
	<div class="irc_no_and_ica_no clear_both">
		<div class="lra_no_block"><? echo $company_add[$data_array[0][csf("importer_id")]]['irc_no'];?>.</div>
		<div class="lca_no_block"><? echo $data_array[0][csf("lca_no")];?>.</div>
	</div>

	<div style="margin-top: 45px;padding-left: 40px;">Export sales contract/LC No. </div>

	<!-- <div class="BankOfBangladeshLtd_1 height clear_both">&nbsp;<? //echo $branch;?></div>
	<div class="BankOfBangladeshLtd_2 height clear_both">&nbsp;<? //echo $branch;?></div>
	<div class="dateMarketFright height clear_both">&nbsp;<? //echo $inco_term_id;?></div>
	<div class="BankOfBangladeshLtd_3 height clear_both">&nbsp;<? //echo $branch;?></div>
	<div class="receiptMarketFright height clear_both"><? //echo $inco_term_id;?></div>
	<div class="underNo clear_both">&nbsp;</div>
    <div class="issuedBy clear_both">&nbsp;</div>


	<div class="packingList clear_both"> &nbsp;  </div>
	<div class="otherDocument clear_both">&nbsp; LC/SC: <? //echo implode(', ',$export_lc_sc_no_arr);?>  </div>
	<div class="additionalInstraction clear_both">&nbsp;</div>
	<div class="QTY clear_both">&nbsp;Gmts Qty: <? //echo number_format($order_pcs_qty);?> pcs </div> -->
</div>
</div>
    <?
	exit();
}

?>


