<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="btb_application_form")
{

	$sql = "SELECT	importer_id,issuing_bank_id,lc_value,lc_date,currency_id,tenor,supplier_id,item_category_id,pi_id,port_of_loading,port_of_discharge,delivery_mode_id,last_shipment_date,lc_expiry_date,lc_type_id,inco_term_place,doc_presentation_days,origin,inco_term_id,insurance_company_name,cover_note_no,cover_note_date,payterm_id,tolerance,lca_no,lc_number,lc_date,btb_system_id,pi_id,partial_shipment,transhipment,remarks,advising_bank from com_btb_lc_master_details where id='$data'";
	 // echo $sql;
	$data_array=sql_select($sql);

	$is_lc_sc_sql=sql_select("SELECT lc_sc_id, is_lc_sc from com_btb_export_lc_attachment where import_mst_id=$data and is_deleted=0 and status_active=1");
	foreach($is_lc_sc_sql as $row)
	{
		if($row[csf('is_lc_sc')]==0){
			$sql_lc=sql_select("SELECT id,export_lc_no, lc_date, lc_value FROM com_export_lc  where id=".$row[csf('lc_sc_id')]);
			foreach($sql_lc as $lc_row)
			{
				$export_lc_sc_no_arr[$lc_row[csf("id")]]=$lc_row[csf("export_lc_no")];
				$export_lc_sc_date_arr[$lc_row[csf("id")]]=$lc_row[csf("lc_date")];
				$export_lc_sc_value_arr[$lc_row[csf("id")]]=$lc_row[csf("lc_value")];
			}
		}
		else{
			$sql_sc=sql_select("SELECT id,contract_no,contrct_date,contract_value FROM com_sales_contract where id=".$row[csf('lc_sc_id')]);
			foreach($sql_sc as $sc_row)
			{
				$export_lc_sc_no_arr[$sc_row[csf("id")]]=$sc_row[csf("contract_no")];
				$export_lc_sc_date_arr[$sc_row[csf("id")]]=$sc_row[csf("contract_date")];
				$export_lc_sc_value_arr[$sc_row[csf("id")]]=$sc_row[csf("contract_value")];
			}
		}
		
		$lc_sc_id_arr[]=$row[csf('lc_sc_id')];

	}


	$order_pcs_qty = return_field_value("sum(b.order_quantity) as order_quantity","com_export_lc_order_info a,wo_po_color_size_breakdown b","b.po_break_down_id=a.wo_po_break_down_id and a.com_export_lc_id in(".implode(',',$lc_sc_id_arr).")","order_quantity");


	//--------------lib
	$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');
	$company_name = return_field_value("company_name","lib_company","id=".$data_array[0][csf("importer_id")],"company_name");
	$irc_no = return_field_value("irc_no","lib_company","id=".$data_array[0][csf("importer_id")],"irc_no");
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
	$advising_branch = return_field_value("bank_name","lib_bank","id=".$data_array[0][csf("advising_bank")],"bank_name");

	$bank_address = return_field_value("ADDRESS","lib_bank","id=".$data_array[0][csf("issuing_bank_id")],"ADDRESS");
	$currency_sign = $currency_sign_arr[$data_array[0][csf("currency_id")]];
	$swift_code = return_field_value("swift_code","lib_bank","id=".$data_array[0][csf("issuing_bank_id")],"swift_code");

	$supplier_name = return_field_value("supplier_name","lib_supplier","id=".$data_array[0][csf("supplier_id")],"supplier_name");
	$supplier_add = return_field_value("address_1","lib_supplier","id=".$data_array[0][csf("supplier_id")],"address_1");

	//echo "select id, pi_number from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0";
	$pi_number_arr=return_library_array( "SELECT id, pi_number from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','pi_number');

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
	$hs_code_arr=return_library_array( "SELECT id, HS_CODE from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','HS_CODE');

	$pi_date = return_field_value("pi_date","com_pi_master_details"," status_active = 1 AND is_deleted = 0 AND id=".$data_array[0][csf("pi_id")],"pi_date");

	$company_irc_expiry_date = return_field_value("irc_expiry_date","lib_company","id=".$data_array[0][csf("importer_id")],"irc_expiry_date");
	if($company_irc_expiry_date!="")
	{
		$month_irc=date('m',strtotime($company_irc_expiry_date)); 
		$year_irc=date('Y',strtotime($company_irc_expiry_date)); 
		$irc_date = strtotime($company_irc_expiry_date);
		if($year_irc>6)
		{
			$renewal_irc=date('Y', mktime(0,0,0,1,1,date('Y',$irc_date)+1));
		}
		else
		{
			$renewal_irc=date('Y', mktime(0,0,0,1,1,date('Y',$irc_date)-1));
		}
		if($year_irc>$renewal_irc){$renewal_year=$renewal_irc.'-'.$year_irc;}else{$renewal_year=$year_irc.'-'.$renewal_irc;}
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
			background: url("../application_form/form_image/image/ncc_cf7.jpg");
            background-size:21.59cm 30.56cm;
            background-repeat: no-repeat;
        }

        #position1 {
			position: absolute;
            margin-top: 82px;
            margin-left: 45px;
        }

        #position2 {
            position: absolute;
            margin-top: 216px;
            margin-left: 380px;
        }

        /* #position3 {
            position: absolute;
            margin-top: 155px;
            margin-left: 270px;
        } */
        #position4 {
            position: absolute;
            margin-top: 242px;
            margin-left: 310px;
        }
        #position5 {
			position: absolute;
            margin-top: 242px;
            margin-left: 480px;
        }
        #position6 {
            position: absolute;
            margin-top: 266px;
            margin-left: 200px;
        }

        #position7 {
            position: absolute;
            margin-top: 350px;
            margin-left: 200px;
        }

        #position8 {
            position: absolute;
            margin-top: 425px;
            margin-left: 280px;
        }

        #position9 {
            position: absolute;
			margin-top: 425px;
            margin-left: 440px;
			font-size: 90%;
			width: 150px;
        }

        #position10 {
            position: absolute;
            margin-top: 600px;
            margin-left: 200px;
			
        }

        #position11 {
            position: absolute;
            margin-top: 653px;
            margin-left: 360px;
			
        }
        #position12 {
            position: absolute;
            margin-top: 653px;
            margin-left: 610px;
			
        }
        #position13 {
            position: absolute;
            margin-top: 900px;
            margin-left: 320px;
			
        }
        #position14 {
            position: absolute;
            margin-top: 900px;
            margin-left: 600px;
			
        }
        #position15 {
            position: absolute;
            margin-top: 915px;
            margin-left: 290px;
			
        }
        #position16 {
            position: absolute;
            margin-top: 1070px;
            margin-left: 200px;
			
        }

     
    </style>

	<body>
		<div id="position1">
		<? echo $branch ?>
		</div>
		<div id="position2">
		<?   echo $advising_branch  ;?>
		</div>
		<div id="position3">
		<!-- <? echo $swift_code;?> -->

		</div>
		<div id="position4">
		<?  
		// $last_shipment_date_year=date('Y',strtotime($last_shipment_date));
			echo $last_shipment_date;?>
		</div>
		<div id="position5">
		<? echo $renewal_year; ?>
		</div>
		<div id="position6">
		<? echo $supplier_name;?></br>
		<? echo $supplier_add;?> 
			</div>
		<div id="position7">
		<? echo $company_name;?></br>
		<? echo "Plot# ".$company_add[$data_array[0][csf("importer_id")]]['plot_no']." ". $company_add[$data_array[0][csf("importer_id")]]['road_no']." ".  $company_add[$data_array[0][csf("importer_id")]]['city'] ." ". $country_array[$company_add[$data_array[0][csf("importer_id")]]['country_id']] ?> 
		
		</div>
		<div id="position8">
		<? echo $currency_sign.' '.number_format($data_array[0][csf('lc_value')],2);?>
			</div>
		<div id="position9">
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
			<? echo number_to_words(number_format($data_array[0][csf('lc_value')],2));?>
			<!-- <? echo number_to_words(number_format($data_array[0][csf('lc_value')],2), $mcurrency, $dcurrency);?> -->

		</div>
		<div id="position10">
		<? echo $item_category[$data_array[0][csf("item_category_id")]];?> 
		</div>
		<div id="position11">
		<? echo chop($pi_numbers,",");?>
		</div>
		<div id="position12">
		<? echo chop($pi_date,",");?>
		</div>
		<div id="position13">
		<? echo implode(', ',$export_lc_sc_no_arr);?>
		</div>
		<div id="position14">
		<? echo implode(', ',$export_lc_sc_date_arr);?>
		</div>
		<div id="position15">
		<? echo implode(', ',$export_lc_sc_value_arr);?>
		</div>
		<div id="position16">
		<? if($data_array[0][csf("remarks")] !=''){
			$remark = explode('.',$data_array[0][csf("remarks")]);
			foreach($remark as $key=> $value){
				echo $value."</br>";
			}
			
		} ?>
		</div>
	

	</body>
    <?
	exit();
}

?>


