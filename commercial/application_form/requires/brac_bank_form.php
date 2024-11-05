<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="btb_application_form")
{

	$sql = "SELECT btb_system_id,importer_id,application_date,issuing_bank_id,lc_value,currency_id,tenor,supplier_id,item_category_id,pi_id,port_of_loading,port_of_discharge,delivery_mode_id,last_shipment_date,lc_expiry_date,inco_term_place,doc_presentation_days,origin,inco_term_id,insurance_company_name,cover_note_no,cover_note_date,payterm_id,tolerance,lca_no,maturity_from_id from com_btb_lc_master_details where id='$data'";
	
	$data_array=sql_select($sql); 
	$btb_system_id=$data_array[0][csf('btb_system_id')];

	$is_lc_sc_sql=sql_select("select lc_sc_id, is_lc_sc from com_btb_export_lc_attachment where import_mst_id=$data and is_deleted=0 and status_active=1");
	if(empty($is_lc_sc_sql)) {echo "May be attachment not complete yet";die;}
	foreach($is_lc_sc_sql as $row)
	{
		if($row[csf('is_lc_sc')]==0){
			$sql_lc=sql_select("SELECT id,export_lc_no,lc_date,lc_value FROM com_export_lc  where id=".$row[csf('lc_sc_id')]);
			foreach($sql_lc as $lc_row)
			{
				$export_lc_sc_no_arr[$lc_row[csf("id")]]=$lc_row[csf("export_lc_no")]." DT :".$lc_row[csf("lc_date")];
				$lc_sc_value+=$lc_row[csf("lc_value")];
			}
		}
		else{
			$sql_sc=sql_select("SELECT id,contract_no,contract_date,contract_value FROM com_sales_contract where id=".$row[csf('lc_sc_id')]);
			foreach($sql_sc as $sc_row)
			{
				$export_lc_sc_no_arr[$sc_row[csf("id")]]=$sc_row[csf("contract_no")]." DT :".$sc_row[csf("contract_date")];
				$lc_sc_value+=$sc_row[csf("contract_value")];
			}
		}
		
		$lc_sc_id_arr[]=$row[csf('lc_sc_id')];

	}


	$order_pcs_qty = return_field_value("sum(b.order_quantity) as order_quantity","com_export_lc_order_info a,wo_po_color_size_breakdown b","b.po_break_down_id=a.wo_po_break_down_id and a.com_export_lc_id in(".implode(',',$lc_sc_id_arr).")","order_quantity");


	//--------------lib
	$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');
	$company_name = return_field_value("company_name","lib_company","id=".$data_array[0][csf("importer_id")],"company_name");
	$country_array = return_library_array("select id,country_name from lib_country where is_deleted=0","id","country_name");
	//echo "select id,plot_no,level_no,road_no,block_no,country_id,city,zip_code,irc_no,tin_number,vat_number,bang_bank_reg_no from lib_company where id = ".$data_array[0][csf('importer_id')]."";
	$address = sql_select("select id,plot_no,level_no,road_no,block_no,country_id,city,zip_code,irc_no,tin_number,vat_number,bang_bank_reg_no,business_nature from lib_company where id = ".$data_array[0][csf('importer_id')]."");
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
	$business_nature=explode(',',$address[0][csf('business_nature')]);
	foreach($business_nature as $row)
	{
		$business_nature_info=$business_nature_arr[$row].',';
	}

	$branch = return_field_value("branch_name","lib_bank","id=".$data_array[0][csf("issuing_bank_id")],"branch_name");
	$currency_sign = $currency_sign_arr[$data_array[0][csf("currency_id")]];
	
	
	$supplier_name = return_field_value("supplier_name","lib_supplier","id=".$data_array[0][csf("supplier_id")],"supplier_name");
	$supplier_add = return_field_value("address_1","lib_supplier","id=".$data_array[0][csf("supplier_id")],"address_1");
	
	
	//$pi_number_arr=return_library_array( "select id, pi_number from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','pi_number');

	$pi_number_date='';
  	if ($data_array[0][csf("pi_id")] != ""){
		$sql_pi=sql_select("SELECT id, pi_number, pi_date from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0");
		foreach($sql_pi as $row){
			$pi_number_date.='PI: '.$row[csf('pi_number')].'&nbsp;DT: '.change_date_format($row[csf('pi_date')]).', ';
		}
  	}
	
	//echo "select id, pi_date from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0";
	//$pi_date_array = return_library_array("select id, pi_date from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0","id","pi_date");

	$hs_code_array = return_library_array("select id, hs_code from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0","id","hs_code");

	$pi_cate_arr=return_library_array( "SELECT id, item_category_id from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','item_category_id');
	//print_r($pi_date_array);
	
	$total_pi=count($sql_pi);

	if(count($hs_code_array)>1){
		foreach($hs_code_array as $row){
			$hs_code .= $row.",";
		}		
	}
	else
	{
		$hs_code=$hs_code_array[$data_array[0][csf("pi_id")]];
	}

	if(count($pi_cate_arr)>1){
		foreach($pi_cate_arr as $row){
			$pi_category .= $item_category[$row].",";
		}
		$pi_category=implode(", ",array_unique(explode(",",chop($pi_category,','))));		
	}
	else
	{
		$pi_category= $item_category[$pi_cate_arr[$data_array[0][csf("pi_id")]]];
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
	
	?>
    
<style>
	/*printer setup 0,0,0,0*/
	/*21cm,33.5cm    21.5 34.5*/
	body {
		margin: 0;padding: 0;font-size: 90%;
	}
	#position1 {position: absolute;margin-top: 125px;margin-left: 160px;}
	#position2 {position: absolute;margin-top: 200px;margin-left: 50px;width:350px;}
	#position3 {position: absolute;margin-top: 200px;margin-left: 400px;width:350px;}
	#position4 {position: absolute;margin-top: 290px;margin-left: 250px;}
	#position5 {position: absolute;margin-top: 305px;margin-left: 50px;width:350px;}
	#position6 {position: absolute;margin-top: 330px;margin-left: 410px;}
	#position7 {position: absolute;margin-top: 330px;margin-left: 580px;}
	#position8 {position: absolute;margin-top: 400px;margin-left: 220px;}
	#position9 {position: absolute;margin-top: 400px;margin-left: 650px;}
	#position10 {position: absolute;margin-top: 420px;margin-left: 100px;}
	#position11 {position: absolute;margin-top: 420px;margin-left: 320px;}
	#position12 {position: absolute;margin-top: 450px;margin-left: 200px;}
	#position13 {position: absolute;margin-top: 450px;margin-left: 470px;}
	#position14 {position: absolute;margin-top: 500px;margin-left: 50px;}
	#position15 {position: absolute;margin-top: 520px;margin-left: 50px;}
	#position16 {position: absolute;margin-top: 540px;margin-left: 50px;}
	#position17 {position: absolute;margin-top: 560px;margin-left: 50px;}
	#position18 {position: absolute;margin-top: 650px;margin-left: 140px;}
	#position19 {position: absolute;margin-top: 790px;margin-left: 200px;}
	#position20 {position: absolute;margin-top: 790px;margin-left: 450px;}
	#position21 {position: absolute;margin-top: 810px;margin-left: 150px;}
	#position22 {position: absolute;margin-top: 970px;margin-left: 120px;}
	#position23 {position: absolute;margin-top: 970px;margin-left: 250px;}
	#position24 {position: absolute;margin-top: 970px;margin-left: 650px;}
	#position25 {position: absolute;margin-top: 1000px;margin-left: 120px;}
	#position26 {position: absolute;margin-top: 1000px;margin-left: 250px;}
	#position27 {position: absolute;margin-top: 1000px;margin-left: 600px;}
</style>
<body>
	<div id="position1"><? echo change_date_format($data_array[0][csf("application_date")]);?></div>
	<div id="position2">
		<? echo $company_name;?><br />
		<? echo $company_add[$data_array[0][csf("importer_id")]]['city']; ?>
	</div>
	<div id="position3"><? echo $supplier_name;?><br /><? echo $supplier_add;?></div>
	
	<div id="position4"><? echo $currency_sign.' '.number_format($data_array[0][csf('lc_value')],2);?></div>
	<div id="position5">
		<?
			$currency_id = $data_array[0][csf("currency_id")];
			$dcurrency="";
			if($currency_id==1)
			{
			$mcurrency='Taka';
			$dcurrency='Paisa';
			}
			if($currency_id==2)
			{
			$mcurrency='USD';
			$dcurrency='CENTS';
			}
			if($currency_id==3)
			{
			$mcurrency='EURO';
			$dcurrency='CENTS';
			}
			echo number_to_words(number_format($data_array[0][csf('lc_value')],2), $mcurrency, $dcurrency);
		?>
	</div>
	<div id="position6"><? echo $pay_term_cond;?></div>
	<div id="position7"><?echo $maturity_from[$data_array[0][csf("maturity_from_id")]];?></div>
	<div id="position8"><? echo $data_array[0][csf("port_of_loading")];?></div>
	<div id="position9"><? echo $lc_expiry_date;?></div>
	<div id="position10"><? echo $data_array[0][csf("port_of_discharge")];?></div>
	<div id="position11"><? echo $shipment_mode[$data_array[0][csf("delivery_mode_id")]];?></div>
	<div id="position12"><? echo $last_shipment_date;?></div>
	<div id="position13"><? echo $data_array[0][csf("doc_presentation_days")];?></div>
	<div id="position14">1. <? echo $pi_category;?> for 100% Export Oriented <?echo chop($business_nature_info,',');?> Readymade Garments Industries</div>
	<div id="position15">2. Beneficiary's <? echo rtrim($pi_number_date,', '); ?></div>
	<div id="position16">3. BTB LC open against SC/LC No. <? echo implode(', ',$export_lc_sc_no_arr);?> Value: <?echo $currency_sign.' '.number_format($lc_sc_value,2);?></div>
	<div id="position17">
		<? 
			/*if($data_array[0][csf("tolerance")]>0){ ?>
					# <? echo $data_array[0][csf("tolerance")];?>% MORE OR LESS OF QUANTITY & AMOUNT ARE ACCEPTABLE.
			<? }*/ 
			$sql_term= sql_select("SELECT terms from wo_booking_terms_condition where booking_no='$btb_system_id' ");
			$i=4;
			foreach ($sql_term as $value) {
				echo $i.". ".$value[csf('terms')]."</br>";
				$i++;
			}
			
			?>
	</div>
	<div id="position18"><? echo $origin;?></div>
	<div id="position19"><? echo $data_array[0][csf("cover_note_no")];?></div>
	<div id="position20">DATED: <? echo $cover_note_date;?></div>
	<div id="position21"><? echo $data_array[0][csf("insurance_company_name")];?></div>
	<div id="position22"><? echo $data_array[0][csf("lca_no")];?>  </div>
	<div id="position23"><? echo $company_add[$data_array[0][csf("importer_id")]]['irc_no'];?>  </div>
	<div id="position24"><? echo $company_add[$data_array[0][csf("importer_id")]]['bang_bank_reg_no'];?>  </div>
	<div id="position25"><? echo $company_add[$data_array[0][csf("importer_id")]]['tin_number'];?>  </div>
	<div id="position26"><? echo $company_add[$data_array[0][csf("importer_id")]]['vat_number'];?>  </div>
	<div id="position27"><? echo chop($hs_code, ",");?>  </div>
</body>
    <?
	exit();
}

?>