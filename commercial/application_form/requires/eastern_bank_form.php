<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="btb_application_form")
{

	$sql = "SELECT importer_id,issuing_bank_id,lc_value,lc_date,currency_id,tenor,supplier_id,item_category_id,pi_id,port_of_loading,port_of_discharge,delivery_mode_id,last_shipment_date,lc_expiry_date,inco_term_place,doc_presentation_days,origin,inco_term_id,insurance_company_name,lcaf_no,cover_note_no,cover_note_date,payterm_id,tolerance,lca_no,lc_number,lc_date,btb_system_id,pi_id,partial_shipment,transhipment,remarks from com_btb_lc_master_details where id='$data'";
	// echo $sql;
	$data_array=sql_select($sql);

    //echo "select lc_sc_id, is_lc_sc from com_btb_export_lc_attachment where import_mst_id=$data and is_deleted=0 and status_active=1";
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
	$address = sql_select("SELECT id,plot_no,level_no,road_no,block_no,country_id,city,zip_code,irc_no,tin_number,vat_number from lib_company where id = ".$data_array[0][csf('importer_id')]."");
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
	}
	//print_r($company_add);

	// $branch = return_field_value("branch_name","lib_bank","id=".$data_array[0][csf("issuing_bank_id")],"branch_name");
	// $bank_address = return_field_value("ADDRESS","lib_bank","id=".$data_array[0][csf("issuing_bank_id")],"ADDRESS");
	$currency_sign = $currency_sign_arr[$data_array[0][csf("currency_id")]];

	$supplier_name = return_field_value("supplier_name","lib_supplier","id=".$data_array[0][csf("supplier_id")],"supplier_name");
	$supplier_add = return_field_value("address_1","lib_supplier","id=".$data_array[0][csf("supplier_id")],"address_1");

	/*$pi_number_arr=return_library_array( "SELECT id, pi_number from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','pi_number');
	$pi_date = return_field_value("pi_date","com_pi_master_details"," status_active = 1 AND is_deleted = 0 AND id=".$data_array[0][csf("pi_id")],"pi_date");

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
	}*/
	$hs_code_arr=return_library_array( "SELECT id, HS_CODE from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','HS_CODE');
	if(count($hs_code_arr)>1){
		foreach($hs_code_arr as $row){
			$hs_code .= $row.",";
		}
	}
	else
	{
		$hs_code = $hs_code_arr[$data_array[0][csf("pi_id")]];
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

	if($data_array[0][csf("doc_presentation_days")] == ""){
		$doc_presentation_days = "";
	}else{
		$doc_presentation_days = $data_array[0][csf("doc_presentation_days")];
	}

	?>

    <style>
        body{margin:0;padding:0; font-size: 90%;
            background: url("../application_form/form_image/image/ebl_cf7.jpg");
            background-size:21.59cm 30.56cm;
            background-repeat: no-repeat;
        }
        .template{ width: 960px;}
        .clear{ overflow: hidden; }
        p{margin:0;padding:0;}
        #position1{
            position: absolute;
            margin-top: 50px;
            margin-left: 50px;
			width:300px;
        }
        #position2{
            position: absolute;
            margin-top: 180px;
            margin-left: 540px;
			width:300px;
        }
        #position3{
            position: absolute;
            margin-top: 295px;
            margin-left: 550px;
        }
        #position4{
            position: absolute;
            margin-top: 350px;
            margin-left: 130px;
        }
        #position5{
            position: absolute;
            margin-top: 380px;
            margin-left: 150px;
        }
        #position6{
            position: absolute;
            margin-top: 390px;
            margin-left: 650px;
        }
        #position7{
            position: absolute;
            margin-top: 420px;
            margin-left: 200px;
        }
         #position8{
            position: absolute;
            margin-top: 445px;
            margin-left: 200px;
        }
        #position9{
            position: absolute;
            margin-top: 520px;
            margin-left: 100px;
        }
        #position10{
            position: absolute;
            margin-top: 580px;
            margin-left: 200px;
        }
        #position11{
            position: absolute;
            margin-top: 680px;
            margin-left: 110px;
        }
        #position12{
            position: absolute;
            margin-top: 680px;
            margin-left: 260px;
        }
        #position13{
            position: absolute;
            margin-top: 680px;
            margin-left: 470px;
        }
        #position14{
            position: absolute;
            margin-top: 710px;
            margin-left: 110px;
        }
        #position15{
            position: absolute;
            margin-top: 710px;
            margin-left: 330px;
        }
        #position16{
            position: absolute;
            margin-top: 765px;
            margin-left: 300px;
        }
        #position17{
            position: absolute;
            margin-top: 785px;
            margin-left: 170px;
        }
        #position18{
            position: absolute;
            margin-top: 830px;
            margin-left: 50px;
        }
        #position19{
            position: absolute;
            margin-top: 975px;
            margin-left: 350px;
        }
        #position20{
            position: absolute;
            margin-top: 1025px;
            margin-left: 440px;
        }
    </style>

	<body>
		<div class="template clear">
			<div id="position1">
				<p><? echo $company_name;?>,
					<? echo $company_add[$data_array[0][csf("importer_id")]]['plot_no'].",".$company_add[$data_array[0][csf("importer_id")]]['level_no'].", ".$company_add[$data_array[0][csf("importer_id")]]['road_no'].",<br/> ".$company_add[$data_array[0][csf("importer_id")]]['city'].", ".$country_array[$company_add[$data_array[0][csf("importer_id")]]['country_id']]; ?>
				</p>
			</div>
			<div id="position2">
				<p><? echo $supplier_name;?>,
					<? echo $supplier_add;?>
				</p>
			</div>
			<div id="position3">
				<p>
					<? echo $currency[$data_array[0][csf("currency_id")]].' '.$currency_sign.' '.number_format($data_array[0][csf('lc_value')],2);?> 
				</p>
			</div>
			<div id="position4">
				<p><? echo $data_array[0][csf("port_of_loading")];?> </p>
			</div>
			<div id="position5">
				<p><? echo $data_array[0][csf("port_of_discharge")];?></p>
			</div>
			<div id="position6">
				<p><? echo $data_array[0][csf("tenor")];?></p>
			</div>
			<div id="position7">
				<p><? echo $last_shipment_date;?></p>
			</div>
			<div id="position8">
				<p><? echo $lc_expiry_date?></p>
			</div>
			<div id="position9">
				<p>For 100% Export Oriented Readymade Garments Industry.</p>
			</div>
			<div id="position10">
				<p><? echo $doc_presentation_days;?> </p>
			</div>
			<div id="position11">
				<p><? echo $irc_no;?></p>
			</div>
			<div id="position12">
				<p><? echo $company_add[$data_array[0][csf("importer_id")]]['tin_number'];?> </p>
			</div>
			<div id="position13">
				<p><? echo $company_add[$data_array[0][csf("importer_id")]]['vat_number'];?></p>
			</div>
			<div id="position14">
				<p><? echo $data_array[0][csf("lca_no")];?></p>
			</div>
			<div id="position15">
				<p><? echo $hs_code;?></p>
			</div>
			<div id="position16">
				<p><? echo implode(', ',$export_lc_sc_no_arr);?></p>
			</div>
			<div id="position17">
				<p><? echo $origin;?></p>
			</div>
			<div id="position18">
				<p>Bond License no: 315/CUS/SBW/93</p>
			</div>
			<div id="position19">
				<p>
				<? echo $data_array[0][csf("cover_note_no")]." Date: ". $data_array[0][csf("cover_note_date")]?>
				</p>
			</div>
			<div id="position20">
				<p><? echo $data_array[0][csf("remarks")];?></p>
			</div>
		</div>
	</body>
    <?
	exit();
}

?>


