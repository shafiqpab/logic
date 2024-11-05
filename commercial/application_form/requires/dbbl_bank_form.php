<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="btb_application_form")
{

	$sql = "select	importer_id,issuing_bank_id,lc_number,lc_value,currency_id,tenor,supplier_id,item_category_id,pi_id,port_of_loading,port_of_discharge,delivery_mode_id,last_shipment_date,lc_expiry_date,inco_term_place,doc_presentation_days,origin,inco_term_id,insurance_company_name,cover_note_no,cover_note_date,payterm_id,tolerance,lca_no,remarks from com_btb_lc_master_details where id='$data'";
	
	$data_array=sql_select($sql); 

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
	$country_array = return_library_array("select id,country_name from lib_country where is_deleted=0","id","country_name");
	//echo "select id,plot_no,road_no,block_no,country_id,city,zip_code,irc_no,tin_number,vat_number,bang_bank_reg_no from lib_company where id = ".$data_array[0][csf('importer_id')]."";
	$address = sql_select("select id,plot_no,road_no,block_no,country_id,city,zip_code,irc_no,tin_number,vat_number,bang_bank_reg_no from lib_company where id = ".$data_array[0][csf('importer_id')]."");
	foreach($address as $row){
		$company_add[$row[csf('id')]]['plot_no'] = $row[csf('plot_no')];
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
	
	$pi_number_arr=return_library_array( "select id, pi_number from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','pi_number');
	$pi_beneficiary_arr=return_library_array( "select id, beneficiary from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','beneficiary');
	//echo "select id, pi_date from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0";
	$pi_date_array = return_library_array("select id, pi_date from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0","id","pi_date");

	$hs_code_array = return_library_array("select id, hs_code from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0","id","hs_code");

	//print_r($hs_code_array);
	
	$total_pi=count($pi_number_arr);$pi_number;
	
	if(count($pi_number_arr)>1){
		foreach($pi_number_arr as $row){
			$pi_number .= $row.",";
		}		
	}
	else
	{
		$pi_number = $pi_number_arr[$data_array[0][csf("pi_id")]];
	}

	if(count($pi_date_array)>=1){
		foreach($pi_date_array as $row){
			//$pi_date .= $row.",";
			$pi_date .= date('d.m.Y',strtotime($row)).",";
		}		
	}
	else
	{
		$pi_date=date('d.m.Y',strtotime($pi_date_array[0]));
	}
	if(count($hs_code_array)>1){
		foreach($hs_code_array as $row){
			$hs_code .= $row.",";
		}		
	}
	else
	{
		$hs_code=$hs_code_array[$data_array[0][csf("pi_id")]];
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

	$lc_number = $data_array[0][csf("lc_number")];
	
	?>
	<html>
	<head>
	<style type="text/css">
		body
		{
			margin:0;
			padding:0;
		}
		#position1{ position: absolute; margin-top: 70px; margin-left: 400px; font-size:120%; }
		#position2{ position: absolute; margin-top: 210px; margin-left: 100px; font-size:120%; width:350px; }
		#position3{ position: absolute; margin-top: 280px; margin-left: 600px; font-size:120%; }
		#position4{ position: absolute; margin-top: 420px; margin-left: 600px; font-size:120%; width:350px;}
		#position5{ position: absolute; margin-top: 420px; margin-left: 220px; font-size:80%; }
		#position6{ position: absolute; margin-top: 540px; margin-left: 180px; font-size:80%; }
		#position7{ position: absolute; margin-top: 590px; margin-left: 300px; font-size:80%; }
		#position8{ position: absolute; margin-top: 590px; margin-left: 525px; font-size:80%; }
		#position9{ position: absolute; margin-top: 620px; margin-left: 220px; font-size:80%; }
		#position10{ position: absolute; margin-top: 620px; margin-left: 490px; font-size:80%; }
		#position11{ position: absolute; margin-top: 620px; margin-left: 810px; font-size:80%; }
		#position12{ position: absolute; margin-top: 645px; margin-left: 220px; font-size:80%; }
		#position13{ position: absolute; margin-top: 645px; margin-left: 550px; font-size:80%; }
		#position14{ position: absolute; margin-top: 645px; margin-left: 860px; font-size:80%; }
		#position15{ position: absolute; margin-top: 675px; margin-left: 250px; font-size:80%; }
		#position16{ position: absolute; margin-top: 790px; margin-left: 575px; font-size:80%; }
		#position17{ position: absolute; margin-top: 1077px; margin-left: 190px; font-size:80%; }
		#position18{ position: absolute; margin-top: 1105px; margin-left: 100px; font-size:80%; }
		#position19{ position: absolute; margin-top: 1105px; margin-left: 450px; font-size:80%; }
		#position20{ position: absolute; margin-top: 1275px; margin-left: 100px; font-size:80%; }
		#position21{ position: absolute; margin-top: 1275px; margin-left: 350px; font-size:80%; }
		#position22{ position: absolute; margin-top: 1300px; margin-left: 300px; font-size:80%; }
		#position23{ position: absolute; margin-top: 1300px; margin-left: 680px; font-size:80%; }
		#position24{ position: absolute; margin-top: 1355px; margin-left: 50px; font-size:80%; }
	</style>
	</head>
	<body>
		<div id="position1"><? echo $branch;?></div>
		<div id="position2">
			<? echo $company_name;?><br />
			<? echo "Plot# ".$company_add[$data_array[0][csf("importer_id")]]['plot_no'].", Road# ".$company_add[$data_array[0][csf("importer_id")]]['road_no'].",<br/> ".$company_add[$data_array[0][csf("importer_id")]]['city'].", ".$country_array[$company_add[$data_array[0][csf("importer_id")]]['country_id']]; ?>
		</div>
		<div id="position3"><? echo $currency_sign.' '.number_format($data_array[0][csf('lc_value')],2);?></div>
		<div id="position4">
			<? if ($pi_beneficiary_arr[$data_array[0][csf("pi_id")]]=="") echo $supplier_name; else echo $pi_beneficiary_arr[$data_array[0][csf("pi_id")]]; ?><br /><? if ($pi_beneficiary_arr[$data_array[0][csf("pi_id")]]=="") echo $supplier_add;?>
		</div>
		<div id="position5"><? echo $data_array[0][csf("tenor")];?> Day's</div>
		<div id="position6">
			<? echo strtoupper($item_category[$data_array[0][csf("item_category_id")]]).' '.strtoupper("for 100% exort orientd <br/>readymade garments industry");?>
		</div>
		<div id="position7"><? echo $pi_number;?></div>
		<div id="position8"><? echo $pi_date;?></div>
		<div id="position9"><? echo $data_array[0][csf("port_of_loading")];?></div>
		<div id="position10"><? echo $data_array[0][csf("port_of_discharge")];?></div>
		<div id="position11"><? echo $shipment_mode[$data_array[0][csf("delivery_mode_id")]];?></div>
		<div id="position12"><? echo $last_shipment_date;?></div>
		<div id="position13"><? echo $lc_expiry_date;?></div>
		<div id="position14"><? echo $data_array[0][csf("inco_term_place")];?></div>
		<div id="position15"><? echo $data_array[0][csf("doc_presentation_days")];?></div>
		<div id="position16"><? echo $origin;?></div>
		<div id="position17"><? echo $data_array[0][csf("insurance_company_name")];?></div>
		<div id="position18"><? echo $data_array[0][csf("cover_note_no")];?></div>
		<div id="position19"><? echo $cover_note_date;?></div>
		<div id="position20"><? echo $company_add[$data_array[0][csf("importer_id")]]['irc_no'];?></div>
		<div id="position21"><? echo $data_array[0][csf("lca_no")];?></div>
		<div id="position22"><? echo $company_add[$data_array[0][csf("importer_id")]]['bang_bank_reg_no'];?></div>
		<div id="position23"><? echo chop($hs_code, ",");?></div>
		<div id="position24"><? echo $data_array[0][csf("remarks")];?></div>
	</body>
	</html>
    <?
	exit();
}
