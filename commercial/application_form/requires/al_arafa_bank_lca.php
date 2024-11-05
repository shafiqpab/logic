<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="btb_application_form")
{

	 $sql = "SELECT	importer_id,issuing_bank_id,lc_value,lc_date,currency_id,tenor,supplier_id,item_category_id,pi_id,port_of_loading,port_of_discharge,delivery_mode_id,last_shipment_date,lc_expiry_date,inco_term_place,doc_presentation_days,origin,inco_term_id,insurance_company_name,cover_note_no,cover_note_date,payterm_id,tolerance,lca_no,lc_number,lc_date,btb_system_id,pi_id,partial_shipment,lc_type_id,transhipment from com_btb_lc_master_details where id='$data'";
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
    $irc_no = return_field_value("irc_no","lib_company","id=".$data_array[0][csf("importer_id")],"irc_no");
    $company_irc_expiry_date = return_field_value("irc_expiry_date","lib_company","id=".$data_array[0][csf("importer_id")],"irc_expiry_date");
	if($company_irc_expiry_date!="")
	{
		if(date("m",strtotime($company_irc_expiry_date))>6)
		{
			$irc_year_increment=date("Y",strtotime($company_irc_expiry_date))+1;
			$irc_year=date("Y",strtotime($company_irc_expiry_date))."-".$irc_year_increment;
		}
		else
		{
			$irc_year_decrement=date("Y",strtotime($company_irc_expiry_date))-1;
			$irc_year=$irc_year_decrement."-".date("Y",strtotime($company_irc_expiry_date));
		}
	}

	$country_array = return_library_array("select id,country_name from lib_country where is_deleted=0","id","country_name");
	$com_address = sql_select("SELECT id,plot_no,level_no,road_no,block_no,country_id,city,zip_code,irc_no,tin_number,vat_number,bang_bank_reg_no,business_nature from lib_company where id = ".$data_array[0][csf('importer_id')]."");
	foreach($com_address as $row){
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
    $business_nature=explode(',',$com_address[0][csf('business_nature')]);
	foreach($business_nature as $row)
	{
		$business_nature_info=$business_nature_arr[$row].',';
	}

	$branch = return_field_value("branch_name","lib_bank","id=".$data_array[0][csf("issuing_bank_id")],"branch_name");

	$bank_address = return_field_value("ADDRESS","lib_bank","id=".$data_array[0][csf("issuing_bank_id")],"ADDRESS");
	$currency_sign = $currency_sign_arr[$data_array[0][csf("currency_id")]];
	$supplier_name = return_field_value("supplier_name","lib_supplier","id=".$data_array[0][csf("supplier_id")],"supplier_name");
	$supplier_add = return_field_value("address_1","lib_supplier","id=".$data_array[0][csf("supplier_id")],"address_1");

	$pi_number_arr=return_library_array( "SELECT id, pi_number from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','pi_number');
	
	$hs_code_arr=return_library_array( "SELECT id, HS_CODE from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','HS_CODE');
   
	$pi_date = return_field_value("pi_date","com_pi_master_details"," status_active = 1 AND is_deleted = 0 AND id=".$data_array[0][csf("pi_id")],"pi_date");
	$pi_cate_arr=return_library_array( "SELECT id, item_category_id from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','item_category_id');

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
	
	if(count($hs_code_arr)>1){
		foreach($hs_code_arr as $row){
			$hs_code .= $row.",";
		}
	}
	else
	{
		$hs_code = $hs_code_arr[$data_array[0][csf("pi_id")]];
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

	$lc_type_id = $data_array[0][csf("lc_type_id")];
	$lc_type ="";
	if($lc_type_id==1){$lc_type='BTB LC';}
	if($lc_type_id==2){$lc_type='Margin LC';}
	if($lc_type_id==3){$lc_type='Fund Building';}
	if($lc_type_id==4){$lc_type='TT';}
	if($lc_type_id==5){$lc_type='FTT';}
	if($lc_type_id==5){$lc_type='FDD';}

	$currency_id = $data_array[0][csf("currency_id")];
	$dcurrency=$mcurrency="";
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
	?>

   	<style>
        body{margin:0;padding:0; font-size: 90%;
            background: url("../application_form/form_image/image/al_arafa_lca.jpg") ;
            background-size:21.59cm 30.56cm;
            background-repeat: no-repeat;
        }
        #position1{position: absolute;margin-top: 75px;margin-left: 240px;}
        #position2{position: absolute;margin-top: 125px;margin-left: 570px;}
        #position3{position: absolute;margin-top: 190px;margin-left: 340px; font-weight: bold;}
        #position4{position: absolute;margin-top: 210px;margin-left: 150px;}
        #position5{position: absolute;margin-top: 230px;margin-left: 200px;}
        #position6{position: absolute;margin-top: 235px;margin-left: 510px;}
        #position7{position: absolute;margin-top: 250px;margin-left: 200px;}
        #position8{position: absolute;margin-top: 370px;margin-left: 450px;}
        #position9{position: absolute;margin-top: 385px;margin-left: 350px;}
        #position10{position: absolute;margin-top: 400px;margin-left: 250px;}
        #position11{position: absolute;margin-top: 520px;margin-left: 65px; width:280px;}
        #position12{position: absolute;margin-top: 520px;margin-left: 370px;}    
    </style>

    <body>
        <div id="position1"><?echo $branch;?></div>
        <div id="position2">
			<?
			$last_shipment_date_year=date('Y',strtotime($last_shipment_date));
			echo $last_shipment_date_year;
			?>
		</div>
		<div id="position3"><? echo $company_name;?></div>
		<div id="position4"><? echo $company_add[$data_array[0][csf("importer_id")]]['city'];?></div>
		<div id="position5"> <? echo $irc_no;?></div>
        <div id="position6"><? echo $irc_year; ?></div>
        <div id="position7"><? echo chop($business_nature_info,',');?> Garments</div>
        <div id="position8"><? echo $lc_type;?></div>
		<div id="position9"><? echo $currency_sign.' '.number_format($data_array[0][csf('lc_value')],2);?> </div>
		<div id="position10"><? echo number_to_words(number_format($data_array[0][csf('lc_value')],2), $mcurrency, $dcurrency);?> </div>
        <div id="position11"><? echo $pi_category;?> For 100% Export Oriented Knit Readymade <? echo  chop($business_nature_info,',');?> Industry</div>
        <div id="position12"> <? echo "H.S CODE : ". $hs_code;?> </div>
    </body>
    <?
	exit();
}

?>