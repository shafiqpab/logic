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
	$data_array=sql_select($sql); 

	$is_lc_sc_sql=sql_select("select lc_sc_id, is_lc_sc from com_btb_export_lc_attachment where import_mst_id=$data and is_deleted=0 and status_active=1");
	foreach($is_lc_sc_sql as $row)
	{
		if($row['IS_LC_SC']==0){
			$sql_lc=sql_select("SELECT id,export_lc_no, lc_date FROM com_export_lc  where id=".$row['LC_SC_ID']);
			foreach($sql_lc as $lc_row)
			{
				$export_lc_sc_no_arr[$lc_row["ID"]]=$lc_row["EXPORT_LC_NO"];
				$export_lc_sc_date_arr[$lc_row["ID"]]=$lc_row["LC_DATE"];
			}
		}
		else{
			$sql_sc=sql_select("SELECT id,contract_no,contract_date FROM com_sales_contract where id=".$row['LC_SC_ID']);
			foreach($sql_sc as $sc_row)
			{
				$export_lc_sc_no_arr[$sc_row["ID"]]=$sc_row["CONTRACT_NO"];
				$export_lc_sc_date_arr[$sc_row["ID"]]=$sc_row["CONTRACT_DATE"];
			}
		}
		
		$lc_sc_id_arr[]=$row['LC_SC_ID'];

	}


	$order_pcs_qty = return_field_value("sum(b.order_quantity) as order_quantity","com_export_lc_order_info a,wo_po_color_size_breakdown b","b.po_break_down_id=a.wo_po_break_down_id and a.com_export_lc_id in(".implode(',',$lc_sc_id_arr).")","order_quantity");


//--------------lib
	$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');
	$company_name = return_field_value("company_name","lib_company","id=".$data_array[0]["IMPORTER_ID"],"company_name");
	
	$country_array = return_library_array("select id,country_name from lib_country where is_deleted=0","id","country_name");
	//echo "select id,plot_no,road_no,block_no,country_id,city,zip_code,irc_no,tin_number,vat_number,bang_bank_reg_no from lib_company where id = ".$data_array[0]['IMPORTER_ID']."";
	$address = sql_select("select id,plot_no,level_no,road_no,block_no,country_id,city,zip_code,irc_no,tin_number,vat_number,bang_bank_reg_no from lib_company where id = ".$data_array[0]['IMPORTER_ID']."");
	foreach($address as $row){
		$company_add[$row['ID']]['plot_no'] = $row['PLOT_NO'];
		$company_add[$row['ID']]['level_no'] = $row['LEVEL_NO'];
		$company_add[$row['ID']]['road_no'] = $row['ROAD_NO'];
		$company_add[$row['ID']]['block_no'] = $row['BLOCK_NO'];
		$company_add[$row['ID']]['country_id'] = $row['COUNTRY_ID'];
		$company_add[$row['ID']]['city'] = $row['CITY'];
		$company_add[$row['ID']]['zip_code'] = $row['ZIP_CODE'];
		$company_add[$row['ID']]['irc_no'] = $row['IRC_NO'];
		$company_add[$row['ID']]['tin_number'] = $row['TIN_NUMBER'];
		$company_add[$row['ID']]['vat_number'] = $row['VAT_NUMBER'];
		$company_add[$row['ID']]['bang_bank_reg_no'] = $row['BANG_BANK_REG_NO'];
	}
	//print_r($company_add);

	$branch = return_field_value("branch_name","lib_bank","id=".$data_array[0]["ISSUING_BANK_ID"],"branch_name");
	$currency_sign = $currency_sign_arr[$data_array[0]["CURRENCY_ID"]];
	
	
	$supplier_name = return_field_value("supplier_name","lib_supplier","id=".$data_array[0]["SUPPLIER_ID"],"supplier_name");
	$supplier_add = return_field_value("address_1","lib_supplier","id=".$data_array[0]["SUPPLIER_ID"],"address_1");
	
	//echo "select id, pi_number from com_pi_master_details where id in(".$data_array[0]["PI_ID"].")  AND status_active = 1 AND is_deleted = 0";
	$pi_number_arr=return_library_array( "select id, pi_number from com_pi_master_details where id in(".$data_array[0]["PI_ID"].")  AND status_active = 1 AND is_deleted = 0",'id','pi_number');
	
	$pi_date = return_field_value("pi_date","com_pi_master_details"," status_active = 1 AND is_deleted = 0 AND id=".$data_array[0]["PI_ID"],"pi_date");
	
	$pi_cate_arr=return_library_array( "SELECT id, item_category_id from com_pi_master_details where id in(".$data_array[0]["PI_ID"].")  AND status_active = 1 AND is_deleted = 0",'id','item_category_id');
	if(count($pi_cate_arr)>1){
		foreach($pi_cate_arr as $row){
			$pi_category .= $item_category[$row].",";
		}
		$pi_category=implode(", ",array_unique(explode(",",chop($pi_category,','))));		
	}
	else
	{
		$pi_category= $item_category[$pi_cate_arr[$data_array[0]["PI_ID"]]];
	}
	
	//echo $total_pi=count($pi_number_arr);
	
	$pi_numbers;
	
	if(count($pi_number_arr)>1){
		foreach($pi_number_arr as $row){
			$pi_numbers .= $row.",";
		}	
		$pi_date='';	
	}
	else
	{
		$pi_numbers = $pi_number_arr[$data_array[0]["PI_ID"]];
		$pi_date=date('d.m.Y',strtotime($pi_date));
	}

	if($data_array[0]["LAST_SHIPMENT_DATE"]!=''){
		$last_shipment_date=date('d.m.Y',strtotime($data_array[0]["LAST_SHIPMENT_DATE"]));
	}
	else
	{
		$last_shipment_date='';
	}
	
	
	if($data_array[0]["LC_EXPIRY_DATE"]!=''){
		$lc_expiry_date=date('d.m.Y',strtotime($data_array[0]["LC_EXPIRY_DATE"]));
	}
	else
	{
		$lc_expiry_date='';
	}
	$origin = return_field_value("country_name"," lib_country","id=".$data_array[0]["ORIGIN"],"country_name");
	
	$inco_term_id=$incoterm[$data_array[0]["INCO_TERM_ID"]];
	
	
	
	if($data_array[0]["COVER_NOTE_DATE"]!=''){
		$cover_note_date=date('d.m.Y',strtotime($data_array[0]["COVER_NOTE_DATE"]));
	}
	else
	{
		$cover_note_date='';
	}
	
	if($data_array[0]["PAYTERM_ID"]==1){ 
		$pay_term_cond = $pay_term[$data_array[0]["PAYTERM_ID"]];
	}else{
		$pay_term_cond = $data_array[0]["TENOR"]. "Day's";
	}
	if($data_array[0]["DOC_PRESENTATION_DAYS"] == ""){
		$doc_presentation_days = "";
	}else{
		$doc_presentation_days = $data_array[0]["DOC_PRESENTATION_DAYS"]."Days";
	}
	
	?>
    
	<style>
		body{
			background:url(../application_form/form_image/IF.jpg);
			width:8.5in;
			height:11in;
			overflow:hidden;
			background-repeat: no-repeat;
			background-size:21.59cm 35.56cm;
			padding:0;
			margin:0;
		}

		#position1 { position: absolute; margin-top: 220px; margin-left: 90px; }
		#position2 { position: absolute; margin-top: 245px; margin-left: 90px; }
		#position3 { position: absolute; margin-top: 310px; margin-left: 90px; }
		#position4 { position: absolute; margin-top: 335px; margin-left: 90px; }
		#position5 { position: absolute; margin-top: 365px; margin-left: 500px; }
		#position6 { position: absolute; margin-top: 395px; margin-left: 260px; }
		#position7 { position: absolute; margin-top: 440px; margin-left: 350px; }
		#position8 { position: absolute; margin-top: 440px; margin-left: 250px; }
		#position9 { position: absolute; margin-top: 520px; margin-left: 300px; }
		#position10 { position: absolute; margin-top: 520px; margin-left: 650px; }
		#position11 { position: absolute; margin-top: 585px; margin-left: 520px; }
		#position12 { position: absolute; margin-top: 800px; margin-left: 150px; }
		#position13 { position: absolute; margin-top: 800px; margin-left: 450px; }
		#position14 { position: absolute; margin-top: 815px; margin-left: 170px; }
		#position15 { position: absolute; margin-top: 850px; margin-left: 520px; }
		#position16 { position: absolute; margin-top: 870px; margin-left: 60px; }
		#position17 { position: absolute; margin-top: 870px; margin-left: 520px; }
		#position18 { position: absolute; margin-top: 910px; margin-left: 380px; }
		#position19 { position: absolute; margin-top: 945px; margin-left: 350px; }
		#position20 { position: absolute; margin-top: 950px; margin-left: 450px; }
		#position21 { position: absolute; margin-top: 970px; margin-left: 50px; }
		#position22 { position: absolute; margin-top: 985px; margin-left: 50px; }
		#position23 { position: absolute; margin-top: 1050px; margin-left: 390px; }
		#position24 { position: absolute; margin-top: 1070px; margin-left: 320px; }

	</style>
	<body>
		<div id="position1"><?=$company_name;?></div>
		<div id="position2"><?=$company_add[$data_array[0]["IMPORTER_ID"]]['plot_no'].",".$company_add[$data_array[0]["IMPORTER_ID"]]['level_no'].",".$company_add[$data_array[0]["IMPORTER_ID"]]['road_no'].",".$company_add[$data_array[0]["IMPORTER_ID"]]['city'].",".$country_array[$company_add[$data_array[0]["IMPORTER_ID"]]['country_id']]; ?></div>
		<div id="position3"><?=$supplier_name;?></div>
		<div id="position4"><?=$supplier_add;?></div>
		<div id="position5"><?=$pay_term_cond;?></div>
		<div id="position6"><?=$currency_sign.' '.number_format($data_array[0]['LC_VALUE'],2);?></div>
		<div id="position7"><?=$pi_category;?> 100% EXPORT ORIENTED GARMENTS INDUSTRY</div>
		<!-- <div id="position8"><?=$item_category[$data_array[0]["ITEM_CATEGORY_ID"]];?></div> -->
		<div id="position9"><?=chop($pi_numbers, ",");?></div>
		<div id="position10"><?=$pi_date;?></div>
		<div id="position11"><?=$origin;?></div>
		<div id="position12"><?=$data_array[0]["INSURANCE_COMPANY_NAME"];?></div>
		<div id="position13"><?=$data_array[0]["COVER_NOTE_NO"];?></div>
		<div id="position14">Date: <?=$cover_note_date;?></div>
		<div id="position15"><?=$data_array[0]["PORT_OF_LOADING"];?></div>
		<div id="position16"><?=$data_array[0]["PORT_OF_DISCHARGE"];?></div>
		<div id="position17"><?=$shipment_mode[$data_array[0]["DELIVERY_MODE_ID"]];?></div>
		<div id="position18"><?=$last_shipment_date;?></div>
		<div id="position19"><?=$lc_expiry_date;?></div>
		<!-- <div id="position20"><?=$doc_presentation_days;?></div> -->
		<div id="position21">THIS BTB LC WILL BE OPENED AGAINST PURCHASE CONTRACT NO: <span class="lc_no"><?=implode(', ',$export_lc_sc_no_arr);?></div>
		<div id="position22">Dated: <?=implode(', ',$export_lc_sc_date_arr);?></div>
		<div id="position23"><?=$company_add[$data_array[0]["IMPORTER_ID"]]['irc_no'];?></div>
		<div id="position24"><?=$data_array[0]["LCA_NO"];?></div>
	</body>

    <?
	exit();
}

?>