<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="btb_application_form")
{

	$sql = "SELECT	BTB_SYSTEM_ID,importer_id,issuing_bank_id,lc_value,currency_id,tenor,supplier_id,item_category_id,pi_id,port_of_loading,port_of_discharge,delivery_mode_id,last_shipment_date,lc_expiry_date,inco_term_place,doc_presentation_days,origin,inco_term_id,insurance_company_name,cover_note_no,cover_note_date,payterm_id,tolerance,lca_no,remarks,lcaf_no 
	from com_btb_lc_master_details where id='$data'";
	
	$data_array=sql_select($sql); 
	$btb_system_id=$data_array[0]["BTB_SYSTEM_ID"];

	$is_lc_sc_sql=sql_select("select lc_sc_id, is_lc_sc from com_btb_export_lc_attachment where import_mst_id=$data and is_deleted=0 and status_active=1");
	if(empty($is_lc_sc_sql)) {echo "May be attachment not complete yet";die;}
    foreach($is_lc_sc_sql as $row)
    {
        if($row[csf('is_lc_sc')]==0){
            $sql_lc=sql_select("SELECT id,export_lc_no, lc_date FROM com_export_lc  where id=".$row[csf('lc_sc_id')]);
            foreach($sql_lc as $lc_row)
            {
                $export_lc_sc_no_arr[$lc_row[csf("id")]]=$lc_row[csf("export_lc_no")]." DT :".$lc_row[csf("lc_date")];
            }
        }
        else{
            $sql_sc=sql_select("SELECT id,contract_no,contract_date FROM com_sales_contract where id=".$row[csf('lc_sc_id')]);
            foreach($sql_sc as $sc_row)
            {
                $export_lc_sc_no_arr[$sc_row[csf("id")]]=$sc_row[csf("contract_no")]." DT :".$sc_row[csf("contract_date")];
            }
        }               
        $lc_sc_id_arr[]=$row[csf('lc_sc_id')];
    }

	// $order_pcs_qty = return_field_value("sum(b.order_quantity) as order_quantity","com_export_lc_order_info a,wo_po_color_size_breakdown b","b.po_break_down_id=a.wo_po_break_down_id and a.com_export_lc_id in(".implode(',',$lc_sc_id_arr).")","order_quantity");


//--------------lib
	$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');
	$company_name = return_field_value("company_name","lib_company","id=".$data_array[0]["IMPORTER_ID"],"company_name");
	$country_array = return_library_array("select id,country_name from lib_country where is_deleted=0","id","country_name");
	//echo "select id,plot_no,road_no,block_no,country_id,city,zip_code,irc_no,tin_number,vat_number,bang_bank_reg_no from lib_company where id = ".$data_array[0]['IMPORTER_ID']."";
	$address = sql_select("select id,plot_no,road_no,block_no,country_id,city,zip_code,irc_no,tin_number,vat_number,bang_bank_reg_no from lib_company where id = ".$data_array[0]['IMPORTER_ID']."");
	foreach($address as $row){
		$company_add[$row['ID']]['plot_no'] = $row['PLOT_NO'];
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
	
	
	$pi_number_arr=return_library_array( "select id, pi_number from com_pi_master_details where id in(".$data_array[0]["PI_ID"].")  AND status_active = 1 AND is_deleted = 0",'id','pi_number');
	$pi_beneficiary_arr=return_library_array( "select id, beneficiary from com_pi_master_details where id in(".$data_array[0]["PI_ID"].")  AND status_active = 1 AND is_deleted = 0",'id','beneficiary');
	
	//echo "select id, pi_date from com_pi_master_details where id in(".$data_array[0]["PI_ID"].")  AND status_active = 1 AND is_deleted = 0";
	$pi_date_array = return_library_array("select id, pi_date from com_pi_master_details where id in(".$data_array[0]["PI_ID"].")  AND status_active = 1 AND is_deleted = 0","id","pi_date");

	$hs_code_array = return_library_array("select id, hs_code from com_pi_master_details where id in(".$data_array[0]["PI_ID"].")  AND status_active = 1 AND is_deleted = 0","id","hs_code");
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

	//print_r($hs_code_array);
	
	
	
	
	$total_pi=count($pi_number_arr);$pi_number;
	
	if(count($pi_number_arr)>1){
		foreach($pi_number_arr as $row){
			$pi_number .= $row.",";
		}		
	}
	else
	{
		$pi_number = $pi_number_arr[$data_array[0]["PI_ID"]];
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
		$hs_code=$hs_code_array[$data_array[0]["PI_ID"]];
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
		#position1 { position: absolute; margin-top: 125px; margin-left: 120px; }
		#position2 { position: absolute; margin-top: 300px; margin-left: 200px; }
		#position3 { position: absolute; margin-top: 315px; margin-left: 200px; }
		#position4 { position: absolute; margin-top: 370px; margin-left: 230px; }
		#position5 { position: absolute; margin-top: 385px; margin-left: 230px; }
		#position6 { position: absolute; margin-top: 445px; margin-left: 80px; }
		#position7 { position: absolute; margin-top: 420px; margin-left: 550px; }
		#position8 { position: absolute; margin-top: 495px; margin-left: 250px; }
		#position9 { position: absolute; margin-top: 510px; margin-left: 360px; width:250px;}
		#position10 { position: absolute; margin-top: 510px; margin-left: 655px; }
		#position11 { position: absolute; margin-top: 620px; margin-left: 150px; }
		#position12 { position: absolute; margin-top: 620px; margin-left: 600px; }
		#position13 { position: absolute; margin-top: 670px; margin-left: 150px; }
		#position14 { position: absolute; margin-top: 670px; margin-left: 350px; }
		#position15 { position: absolute; margin-top: 680px; margin-left: 390px; }
		#position16 { position: absolute; margin-top: 670px; margin-left: 620px; }
		#position17 { position: absolute; margin-top: 720px; margin-left: 150px; }
		#position18 { position: absolute; margin-top: 720px; margin-left: 450px; }
		#position19 { position: absolute; margin-top: 905px; margin-left: 220px; }
		#position20 { position: absolute; margin-top: 905px; margin-left: 600px; }
		#position21 { position: absolute; margin-top: 970px; margin-left: 100px; }
		#position22 { position: absolute; margin-top: 970px; margin-left: 500px; }
		#position23 { position: absolute; margin-top: 1090px; margin-left: 80px; }

	</style>
	<body>
		<div id="position1"><?=$branch;?></div>
		<div id="position2"><?=$company_name;?></div>
		<div id="position3"><?="Plot# ".$company_add[$data_array[0]["IMPORTER_ID"]]['plot_no'].", Road# ".$company_add[$data_array[0]["IMPORTER_ID"]]['road_no'].",<br/> ".$company_add[$data_array[0]["IMPORTER_ID"]]['city'].", ".$country_array[$company_add[$data_array[0]["IMPORTER_ID"]]['country_id']]; ?></div>
		<div id="position4"><?=$supplier_name;?></div>
		<div id="position5"><?=$supplier_add;?></div>
		<div id="position6"><?=$currency_sign.' '.number_format($data_array[0]['LC_VALUE'],2);?></div>
		<!-- <div id="position7"><?=$pay_term_cond;?></div> -->
		<div id="position8"><?=$pi_category;?> 100% EXPORT ORIENTED GARMENTS INDUSTRY</div>
		<div id="position9"><?=chop($pi_number,",");?></div>
		<div id="position10"><?=chop($pi_date,",");?></div>
		<div id="position11"><?=chop($hs_code,",");?></div>
		<div id="position12"><?=$company_add[$data_array[0]["IMPORTER_ID"]]['tin_number'];?></div>
		<div id="position13"><?=$origin;?></div>
		<div id="position14"><?=$data_array[0]["LCAF_NO"];?></div>
		<div id="position15"><?=$company_add[$data_array[0]["IMPORTER_ID"]]['irc_no'];?></div>
		<div id="position16"><?=$company_add[$data_array[0]["IMPORTER_ID"]]['vat_number'];?></div>
		<div id="position17"><?=$data_array[0]["PORT_OF_LOADING"];?></div>
		<div id="position18"><?=$data_array[0]["PORT_OF_DISCHARGE"];?></div>
		<div id="position19"><?=$last_shipment_date;?></div>
		<div id="position20"><?=$lc_expiry_date;?></div>
		<div id="position21"><?=$data_array[0]["COVER_NOTE_NO"];?></div>
		<div id="position22"><?=$data_array[0]["INSURANCE_COMPANY_NAME"];?></div>
		<div id="position23">
			<?
                $term_cond=sql_select("SELECT terms from wo_booking_terms_condition where entry_form=105 and booking_no='$btb_system_id'");
                foreach($term_cond as $row)
                {
                    echo $row["TERMS"]."<br>";
                }
            ?> 
			<?=implode(', ',$export_lc_sc_no_arr);?>
		</div>
	</body>

    <?
	exit();
}

?>