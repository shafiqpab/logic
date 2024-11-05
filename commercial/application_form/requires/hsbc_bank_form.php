<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="btb_application_form")
{

	 $sql = "SELECT	importer_id,issuing_bank_id,lc_value,lc_date,currency_id,tenor,supplier_id,item_category_id,pi_id,port_of_loading,port_of_discharge,delivery_mode_id,last_shipment_date,lc_expiry_date,inco_term_place,doc_presentation_days,origin,inco_term_id,insurance_company_name,cover_note_no,cover_note_date,payterm_id,tolerance,lca_no,lc_number,lc_date,btb_system_id,pi_id,partial_shipment,transhipment,hs_code,lc_type_id from com_btb_lc_master_details where id='$data'";
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
    $irc_no = return_field_value("irc_no","lib_company","id=".$data_array[0][csf("importer_id")],"irc_no");
	
    $nature=return_field_value("business_nature","lib_company","id=".$data_array[0][csf("importer_id")],"business_nature");
    $business_nature_arr= explode(",", $nature);
    $business_nature;
    if(count($business_nature_arr)>0){
     foreach($business_nature_arr as $row){
    if($row==2){
    $business_nature .= "Knit ";
    }elseif($row==3){
    $business_nature .= "Woven ";
    }elseif($row==4){
     $business_nature .= "Trims ";
     }elseif($row==5){
     $business_nature .= "Print ";
     }elseif($row==6){
     $business_nature .= "Embroidery ";
     }elseif($row==7){
     $business_nature .= "Wash ";
     }elseif($row==8){
     $business_nature .= "Yarn Dyeing ";
     }elseif($row==9){
      $business_nature .= "AOP ";
     }elseif($row==100){
      $business_nature .= "Sweater ";
}
}
}
	
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

	$bank_address = return_field_value("ADDRESS","lib_bank","id=".$data_array[0][csf("issuing_bank_id")],"ADDRESS");
	$currency_sign = $currency_sign_arr[$data_array[0][csf("currency_id")]];


	$supplier_name = return_field_value("supplier_name","lib_supplier","id=".$data_array[0][csf("supplier_id")],"supplier_name");
	$supplier_add = return_field_value("address_1","lib_supplier","id=".$data_array[0][csf("supplier_id")],"address_1");

	//echo "select id, pi_number from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0";
	$pi_number_arr=return_library_array( "SELECT id, pi_number from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','pi_number');
	
	$hs_code_arr=return_library_array( "SELECT id, HS_CODE from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','HS_CODE');

	$pi_date = return_field_value("pi_date","com_pi_master_details"," status_active = 1 AND is_deleted = 0 AND id=".$data_array[0][csf("pi_id")],"pi_date");



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

	?>

   <style>
        body {
            margin: 0;
            padding: 0;
            font-size: 90%;
            background: url("../application_form/form_image/image/HSBC.jpg");
            background-size:21.59cm 30.56cm;
            background-repeat: no-repeat;
        }
		#position0 {
            position: absolute;
            margin-top: 137px;
            margin-left: 550px;
        }

        #position1 {
            position: absolute;
            margin-top: 172px;
            margin-left: 220px;
        }

        #position2 {
            position: absolute;
            margin-top: 212px;
            margin-left: 150px;
        }
		#position3 {
            position: absolute;
            margin-top: 212px;
            margin-left: 450px;
        }
		#position2a {
            position: absolute;
            margin-top: 229px;
            margin-left: 150px;
        }

       

        #position4 {
            position: absolute;
            margin-top: 340px;
            margin-left: 300px;
        }

        #position5 {
            position: absolute;
            margin-top: 352px;
            margin-left: 300px;
        }
		 #position5a {
            position: absolute;
            margin-top: 369px;
            margin-left: 300px;
        }

        #position6 {
            position: absolute;
            margin-top: 540px;
            margin-left: 50px;
        }

        #position7 {
            position: absolute;
            margin-top: 540px;
            margin-left: 400px;
        }
    </style>

<body>
     <div id="position0">
	 <!-- For Shipping date -->
	 <?
        $last_shipment_date_year=date('Y',strtotime($last_shipment_date));
        echo $last_shipment_date_year;?>
	 </div>
    <div id="position1">
    <? echo $company_name;?> </br>
    <? echo "Plot# ".$company_add[$data_array[0][csf("importer_id")]]['plot_no'].", Road# ".$company_add[$data_array[0][csf("importer_id")]]['road_no'].", ".$company_add[$data_array[0][csf("importer_id")]]['city'].", ".$country_array[$company_add[$data_array[0][csf("importer_id")]]['country_id']]; ?> , <? echo $origin;?>  
    </div>
    <div id="position2">
    <? echo $irc_no ?>
	</div>
	<div id="position3">
        <!-- 2019-20 --> <? echo $irc_year ;?>
    </div>
	<div id="position2a">
	100% Export Oriented Knit Readymade <? echo $business_nature; ?> Industry  
	</div>
    
    <div id="position4">
        <!-- For LC Type -->
		 <?
	   $lc_type = $data_array[0][csf("lc_type_id")];
	   if($lc_type==1){
		   echo "BTB LC";
	   }elseif($lc_type==2){
		echo "Margin LC";
	   }elseif($lc_type==3){
		echo "Fund Building";
	}elseif($lc_type==4){
		echo "TT";
	}elseif($lc_type==5){
		echo "FTT";
	}elseif($lc_type==6){
		echo "FDD";
	}
	   ?>
    </div>
    <div id="position5">
     
        <?
			$currency_id = $data_array[0][csf("currency_id")];
			//$mcurrency, $dcurrency;
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
		?>

        <b id="p7a"> US <? echo $currency_sign.' '.number_format($data_array[0][csf('lc_value')],2);?></b>
      
    </div>
	<div id="position5a">
    <b id="p7b"><? echo number_to_words(number_format($data_array[0][csf('lc_value')],2), $mcurrency, $dcurrency);?></b>
    </div>
    <div id="position6">
    <? echo $item_category[$data_array[0][csf("item_category_id")]];?>
    </div>
    <div id="position7">
   HS CODE:   <? echo $hs_code;?>
    </div>

    <?
	exit();
}

?>


