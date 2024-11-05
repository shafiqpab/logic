<?
header('Content-type:text/html; charset=utf-8');
session_start();
if ($_SESSION['logic_erp']['user_id'] == "") header("location:login.php");

include('../../../includes/common.php');
$data = $_REQUEST['data'];
$action = $_REQUEST['action'];

if ($action == "btb_application_form") {
    $sql = "SELECT	importer_id, issuing_bank_id, lc_value, lc_date, currency_id, tenor, supplier_id, item_category_id, pi_id, port_of_loading, port_of_discharge, delivery_mode_id, last_shipment_date, lc_expiry_date, inco_term_place, doc_presentation_days, origin,inco_term_id, insurance_company_name, cover_note_no, cover_note_date, MATURITY_FROM_ID, payterm_id, tolerance, lca_no,lcaf_no, lc_number, lc_date, btb_system_id, pi_id, partial_shipment, transhipment, garments_qty, uom_id, advising_bank, advising_bank_address, remarks, lc_type_id
	from com_btb_lc_master_details
	where id='$data' ";
    //echo $sql;
    $data_array = sql_select($sql);
    $all_pi_ids = $data_array[0][csf("pi_id")];
	
	//echo $data_array[0][csf("lc_type_id")];

    $is_lc_sc_sql = sql_select("select lc_sc_id, is_lc_sc from com_btb_export_lc_attachment where import_mst_id=$data and is_deleted=0 and status_active=1");
    if (empty($is_lc_sc_sql) && $data_array[0][csf("lc_type_id")] !=2) {
        echo "May be attachment not complete yet";
        die;
    }
    foreach ($is_lc_sc_sql as $row) {
        if ($row[csf('is_lc_sc')] == 0) {
            $sql_lc = sql_select("SELECT id,export_lc_no, lc_date FROM com_export_lc  where id=" . $row[csf('lc_sc_id')]);
            foreach ($sql_lc as $lc_row) {
                $export_lc_sc_no_arr[$lc_row[csf("id")]] = $lc_row[csf("export_lc_no")] . " Dated: " . $lc_row[csf("lc_date")];
            }
        } else {
            $sql_sc = sql_select("SELECT id,contract_no,contract_date FROM com_sales_contract where id=" . $row[csf('lc_sc_id')]);
            foreach ($sql_sc as $sc_row) {
                $export_lc_sc_no_arr[$sc_row[csf("id")]] = $sc_row[csf("contract_no")] . " Dated: " . $sc_row[csf("contract_date")];
            }
        }
        $lc_sc_id_arr[] = $row[csf('lc_sc_id')];
    }

    $order_pcs_qty = return_field_value("sum(b.order_quantity) as order_quantity", "com_export_lc_order_info a,wo_po_color_size_breakdown b", "b.po_break_down_id=a.wo_po_break_down_id and a.com_export_lc_id in(" . implode(',', $lc_sc_id_arr) . ")", "order_quantity");

    //--------------lib
    $currency_sign_arr = array(1 => '৳', 2 => '$', 3 => '€', 4 => '€', 5 => '$', 6 => '£', 7 => '¥');
    $company_name = return_field_value("company_name", "lib_company", "id=" . $data_array[0][csf("importer_id")], "company_name");
    $bang_bank_reg_no = return_field_value("bang_bank_reg_no", "lib_company", "id=" . $data_array[0][csf("importer_id")], "bang_bank_reg_no");

    $country_array = return_library_array("select id,country_name from lib_country where is_deleted=0", "id", "country_name");
    $address = sql_select("SELECT id,plot_no,level_no,road_no,block_no,country_id,city,zip_code,irc_no,tin_number,vat_number,bang_bank_reg_no,bin_no from lib_company where id = " . $data_array[0][csf('importer_id')] . "");
    foreach ($address as $row) {
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
        $company_add[$row[csf('id')]]['bin_no'] = $row[csf('bin_no')];
    }
    //print_r($company_add);

    $branch = return_field_value("branch_name", "lib_bank", "id=" . $data_array[0][csf("issuing_bank_id")], "branch_name");
    $bank_name = return_field_value("bank_name", "lib_bank", "id=" . $data_array[0][csf("issuing_bank_id")], "bank_name");
	$bank_address = return_field_value("ADDRESS","lib_bank","id=".$data_array[0][csf("issuing_bank_id")],"ADDRESS");
    $currency_sign = $currency_sign_arr[$data_array[0][csf("currency_id")]];
    $supplier_name = return_field_value("supplier_name", "lib_supplier", "id=" . $data_array[0][csf("supplier_id")], "supplier_name");
    $supplier_add = return_field_value("address_1", "lib_supplier", "id=" . $data_array[0][csf("supplier_id")], "address_1");

	$pi_number_arr=return_library_array( "SELECT id, pi_number from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','pi_number');
	$hs_code_arr=return_library_array( "SELECT id, HS_CODE from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','HS_CODE');
	$pi_date = return_field_value("pi_date","com_pi_master_details"," status_active = 1 AND is_deleted = 0 AND id=".$data_array[0][csf("pi_id")],"pi_date");
	$pi_cate_arr=return_library_array( "SELECT id, item_category_id from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','item_category_id');

    $pi_numbers;
	if(count($pi_number_arr)>1){
		foreach($pi_number_arr as $row){
			$pi_numbers .= $row.",";
		}
        // $pi_numbers .= " Pls Check Attached LC Forwarding Letter.";
        $pi_date='';
	}
	else
	{
		$pi_numbers = $pi_number_arr[$data_array[0][csf("pi_id")]];
		$pi_date='&nbsp;DT: '.date('d.m.Y',strtotime($pi_date));
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

    if ($data_array[0][csf("last_shipment_date")] != '') {
        $last_shipment_date = date('d.m.Y', strtotime($data_array[0][csf("last_shipment_date")]));
    } else {
        $last_shipment_date = '';
    }

    if ($data_array[0][csf("lc_expiry_date")] != '') {
        $lc_expiry_date = date('d.m.Y', strtotime($data_array[0][csf("lc_expiry_date")]));
    } else {
        $lc_expiry_date = '';
    }
    $origin = return_field_value("country_name", " lib_country", "id=" . $data_array[0][csf("origin")], "country_name");
    $inco_term_id = $incoterm[$data_array[0][csf("inco_term_id")]];


    if ($data_array[0][csf("cover_note_date")] != '') {
        $cover_note_date = date('d.m.Y', strtotime($data_array[0][csf("cover_note_date")]));
    } else {
        $cover_note_date = '';
    }

    if ($data_array[0][csf("payterm_id")] == 1) {
        $pay_term_cond = $pay_term[$data_array[0][csf("payterm_id")]];
    } else {
        $pay_term_cond = $data_array[0][csf("tenor")] . "Day's";
    }
    if ($data_array[0][csf("doc_presentation_days")] == "") {
        $doc_presentation_days = "";
    } else {
        $doc_presentation_days = $data_array[0][csf("doc_presentation_days")] . "Days";
    }

    $nature = return_field_value("business_nature", "lib_company", "id=" . $data_array[0][csf("importer_id")], "business_nature");
    $business_nature_arr = explode(",", $nature);
    $business_nature;
    if (count($business_nature_arr) > 0) {
        foreach ($business_nature_arr as $row) {
            if ($row == 2) {
                $business_nature .= "Knit ";
            } elseif ($row == 3) {
                $business_nature .= "Woven ";
            } elseif ($row == 4) {
                $business_nature .= "Trims ";
            } elseif ($row == 5) {
                $business_nature .= "Print ";
            } elseif ($row == 6) {
                $business_nature .= "Embroidery ";
            } elseif ($row == 7) {
                $business_nature .= "Wash ";
            } elseif ($row == 8) {
                $business_nature .= "Yarn Dyeing ";
            } elseif ($row == 9) {
                $business_nature .= "AOP ";
            } elseif ($row == 100) {
                $business_nature .= "Sweater ";
            }
        }
    }
    $currency_id = $data_array[0][csf("currency_id")];
    //$mcurrency, $dcurrency;
    $dcurrency = "";
    if ($currency_id == 1) {
        $mcurrency = 'Taka';
        $dcurrency = 'Paisa';
    }
    if ($currency_id == 2) {
        $mcurrency = 'USD';
        $dcurrency = 'CENTS';
    }
    if ($currency_id == 3) {
        $mcurrency = 'EURO';
        $dcurrency = 'CENTS';
    }
	?>

    <style>
        body{
            margin:0;padding:0; font-size: 90%;
            /* background: url("../application_form/form_image/image/sibl_cf7.jpg") ; */
            background-size:21.59cm 30.56cm;
            background-repeat: no-repeat;
        }
        *{font-weight: bold;}
        #position1{position: absolute;margin-top: 80px;margin-left: 30px;}
        #position2{position: absolute;margin-top: 100px;margin-left: 30px;}
        #position3{position: absolute;margin-top: 335px;margin-left: 250px;}
        #position4{position: absolute;margin-top: 350px;margin-left: 70px;}
        #position5{position: absolute;margin-top: 400px;margin-left: 250px;}
        #position6{position: absolute;margin-top: 420px;margin-left: 70px;}
        #position7{position: absolute;margin-top: 470px;margin-left: 50px;}
        #position8{position: absolute;margin-top: 480px;margin-left: 120px; width:250px;}
        #position9{position: absolute;margin-top: 530px;margin-left: 600px;} 
        #position10{position: absolute;margin-top: 550px;margin-left: 50px;}
        #position11{position: absolute;margin-top: 570px;margin-left: 50px;}
        #position12{position: absolute;margin-top: 720px;margin-left: 580px;}  
        #position13{position: absolute;margin-top: 750px;margin-left: 410px;}  
        #position14{position: absolute;margin-top: 750px;margin-left: 580px;}  
        #position15{position: absolute;margin-top: 865px;margin-left: 110px;}  
        #position16{position: absolute;margin-top: 865px;margin-left: 350px;}  
        #position17{position: absolute;margin-top: 1040px;margin-left: 180px;}  
        #position18{position: absolute;margin-top: 1040px;margin-left: 530px;}    
        #position19{position: absolute;margin-top: 1110px;margin-left: 100px;}   
    </style>
    <body>
        <div id="position1"><?=$branch;?></div>
        <div id="position2"><?=$bank_address;?></div>
        <div id="position3"><?=$supplier_name;?></div>
        <div id="position4"><? echo $supplier_add; ?></div>
        <div id="position5"><?=$company_name;?></div>
        <div id="position6">
            <? echo $company_add[$data_array[0][csf("importer_id")]]['plot_no'] . "," . $company_add[$data_array[0][csf("importer_id")]]['level_no'] . ", " . $company_add[$data_array[0][csf("importer_id")]]['road_no'] . ",<br/> " . $company_add[$data_array[0][csf("importer_id")]]['city'] . ", " . $country_array[$company_add[$data_array[0][csf("importer_id")]]['country_id']]; ?>
        </div>
        <div id="position7"><? echo $currency_sign.' '.number_format($data_array[0][csf('lc_value')], 2); ?></div>
        <div id="position8"><? echo $mcurrency.' '.number_to_words(number_format($data_array[0][csf('lc_value')], 2),'', $dcurrency); ?></div>
        <div id="position9"><? echo $origin; ?></div>
        <div id="position10"><? echo $pi_category;?> For 100% Export Oriented Knit Readymade <? echo  chop($business_nature_info,',');?> Industry</div>
        <div id="position11"><? echo 'As per PI no: '.$pi_numbers.' '.$pi_date;?></div>
        <div id="position12"><? echo $data_array[0][csf("lcaf_no")] ; ?></div>
        <div id="position13"><?= $hs_code;?></div>
        <div id="position14"><?= $company_add[$data_array[0][csf("importer_id")]]['irc_no']; ?></div>
        <div id="position15"><? echo $data_array[0][csf("port_of_loading")]; ?></div>
        <div id="position16"><? echo $data_array[0][csf("port_of_discharge")]; ?></div>
        <div id="position17"><? echo $last_shipment_date; ?></div>
        <div id="position18"><? echo $lc_expiry_date; ?></div>
        <div id="position19">
           <u> Export Contract No: under the Export Contract No:  <? echo implode(', ', $export_lc_sc_no_arr); ?></u></br></br> TIN No: <?= $company_add[$data_array[0][csf("importer_id")]]['tin_number']; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; BIN No: <?= $company_add[$data_array[0][csf("importer_id")]]['bin_no']; ?> 
        </div>
    </body>
	<?
    exit();
}

?>