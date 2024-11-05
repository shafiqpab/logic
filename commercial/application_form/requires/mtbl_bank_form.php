<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="btb_application_form")
{

	 $sql = "SELECT	importer_id,btb_system_id,application_date,issuing_bank_id,lc_value,lc_date,currency_id,tenor,supplier_id,item_category_id,pi_id,port_of_loading,port_of_discharge,delivery_mode_id,last_shipment_date,lc_expiry_date,inco_term_place,doc_presentation_days,origin,inco_term_id,insurance_company_name,cover_note_no,cover_note_date,payterm_id,tolerance,lca_no,lc_number,lc_date,btb_system_id,pi_id,partial_shipment,transhipment,add_confirmation_of_credit,lcaf_no from com_btb_lc_master_details where id='$data'";
	 // echo $sql;
	$data_array=sql_select($sql);
    $btb_system_id=$data_array[0]["BTB_SYSTEM_ID"];
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

	$bank_address = return_field_value("ADDRESS","lib_bank","id=".$data_array[0][csf("issuing_bank_id")],"ADDRESS");
	$irc_no = return_field_value("irc_no","lib_company","id=".$data_array[0][csf("importer_id")],"irc_no");
	$currency_sign = $currency_sign_arr[$data_array[0][csf("currency_id")]];
    $is_lc_sc_sql=sql_select("select lc_sc_id, is_lc_sc from com_btb_export_lc_attachment where import_mst_id=$data and is_deleted=0 and status_active=1");
    if(empty($is_lc_sc_sql)) {echo "May be attachment not complete yet";die;}
    foreach($is_lc_sc_sql as $row)
    {
        if($row[csf('is_lc_sc')]==0){
            $sql_lc=sql_select("SELECT id,export_lc_no, lc_date FROM com_export_lc  where id=".$row[csf('lc_sc_id')]);
            foreach($sql_lc as $lc_row)
            {
                $export_lc_sc_no_arr[$lc_row[csf("id")]]=$lc_row[csf("export_lc_no")];
                $export_lc_sc_date_arr[$lc_row[csf("id")]]=$lc_row[csf("lc_date")];
            }
        }
        else{
            $sql_sc=sql_select("SELECT id,contract_no,contract_date FROM com_sales_contract where id=".$row[csf('lc_sc_id')]);
            foreach($sql_sc as $sc_row)
            {
                $export_lc_sc_no_arr[$sc_row[csf("id")]]=$sc_row[csf("contract_no")];
                // $export_lc_sc_date_arr[$lc_row[csf("id")]]=$lc_row[csf("contrct_date")];
                $export_lc_sc_date_arr[$sc_row[csf("id")]]=$sc_row[csf("contract_date")];
            }
        }
        
        $lc_sc_id_arr[]=$row[csf('lc_sc_id')];
    
    }

	$supplier_name = return_field_value("supplier_name","lib_supplier","id=".$data_array[0][csf("supplier_id")],"supplier_name");
	$supplier_add = return_field_value("address_1","lib_supplier","id=".$data_array[0][csf("supplier_id")],"address_1");
    $country_id_supplier = return_field_value("country_id","lib_supplier","id=".$data_array[0][csf("supplier_id")],"country_id");
    $supplier_country_name = $country_array[$country_id_supplier];
	$pi_number_arr=sql_select( "SELECT id, pi_number, pi_date,item_category_id from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].") AND status_active = 1 ");
	
	$hs_code_arr=return_library_array( "SELECT id, HS_CODE from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','HS_CODE');

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

    if(count($pi_number_arr)>1){
		foreach($pi_number_arr as $row){
			$pi_numbers .= $row["PI_NUMBER"].", ";
			$pi_date .= change_date_format($row["PI_DATE"]).", ";
		}
		$pi_numbers=chop($pi_numbers,', ');
		$pi_date=chop($pi_date,', ');
	}
	else
	{
		$pi_numbers = $pi_number_arr[0]["PI_NUMBER"];
		$pi_date = change_date_format($pi_number_arr[0]["PI_DATE"]);
	}
	$pi_category= $item_category[$pi_number_arr[0]["ITEM_CATEGORY_ID"]];

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
		$doc_presentation_days = $data_array[0][csf("doc_presentation_days")];
	}

	?>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-size: 90%;
            /* width: 8.4in; */
        }
        #position1 { position: absolute; margin-top: 90px; margin-left: 70px; }
        #position2 { position: absolute; margin-top: 90px; margin-left: 670px; }
        #position3 { position: absolute; margin-top: 192px; margin-left: 50px; }
        #position4 { position: absolute; margin-top: 192px; margin-left: 390px; width: 300px; }
        #position5 { position: absolute; margin-top: 283px; margin-left: 554px; }
        #position6 { position: absolute; margin-top: 293px; margin-left: 30px; }
        #position7 { position: absolute; margin-top: 293px; margin-left: 180px; }
        #position8 { position: absolute; margin-top: 303px; margin-left: 454px; }
        #position9 { position: absolute; margin-top: 328px; margin-left: 514px; }
        #position10 { position: absolute; margin-top: 380px; margin-left: 15px; }
        #position11 { position: absolute; margin-top: 394px; margin-left: 45px; }
        #position11a { position: absolute; margin-top: 435px; margin-left: 400px; }
        #position12 { position: absolute; margin-top: 376px; margin-left: 554px; }
        #position13 { position: absolute; margin-top: 435px; margin-left: 30px; }
        #position14 { position: absolute; margin-top: 435px; margin-left: 65px; }
        #position15 { position: absolute; margin-top: 435px; margin-left: 140px; }
        #position16 { position: absolute; margin-top: 435px; margin-left: 180px; }
        #position17 { position: absolute; margin-top: 435px; margin-left: 265px; }
        #position18 { position: absolute; margin-top: 435px; margin-left: 564px; }
        #position19 { position: absolute; margin-top: 470px; margin-left: 380px; }
        #position20 { position: absolute; margin-top: 470px; margin-left: 460px; }
        #position21 { position: absolute; margin-top: 470px; margin-left: 535px; }
        #position22 { position: absolute; margin-top: 470px; margin-left: 600px; }
        #position23 { position: absolute; margin-top: 470px; margin-left: 680px; }
        #position24 { position: absolute; margin-top: 475px; margin-left: 15px; }
        #position25 { position: absolute; margin-top: 475px; margin-left: 200px; }
        #position26 { position: absolute; margin-top: 495px; margin-left: 15px; }
        #position27 { position: absolute; margin-top: 490px; margin-left: 216px; width: 200px; }
        #position28 { position: absolute; margin-top: 549px; margin-left: 42px; }
        #position29 { position: absolute; margin-top: 559px; margin-left: 500px; }
        #position30 { position: absolute; margin-top: 570px; margin-left: 42px; }
        #position31 { position: absolute; margin-top: 580px; margin-left: 442px; }
        #position32 { position: absolute; margin-top: 590px; margin-left: 96px; }
        #position33 { position: absolute; margin-top: 580px; margin-left: 626px; }
        #position34 { position: absolute; margin-top: 613px; margin-left: 96px; }
        #position35 { position: absolute; margin-top: 631px; margin-left: 160px; }
        #position36 { position: absolute; margin-top: 655px; margin-left: 98px; }
        #position37 { position: absolute; margin-top: 670px; margin-left: 140px; }
        #position38 { position: absolute; margin-top: 670px; margin-left: 240px; }
        #position39 { position: absolute; margin-top: 672px; margin-left: 500px; }
        #position40 { position: absolute; margin-top: 697px; margin-left: 452px; }
        #position41 { position: absolute; margin-top: 875px; margin-left: 50px; }
    </style>

    <body>
        <div id="position1"> <? echo $branch;?></div>
        <div id="position2"> <? echo $data_array[0][csf("application_date")];?></div>
        <div id="position3">
            <? echo $company_name;?><br>
            <? echo $company_add[$data_array[0][csf("importer_id")]]['city']; ?>
        </div>
        <div id="position4">
            <? echo $supplier_name;?><br>
            <? echo $supplier_add;?>
        </div>
        <div id="position5"><? echo $currency_sign.' '.number_format($data_array[0][csf('lc_value')],2);?></div>
        <div id="position6"> <? echo $irc_no;?></div>
        <div id="position7"><? echo$data_array[0][csf('lcaf_no')];?></div>
        <div id="position8">
            <?
            $dcurrency_arr=array(1=>'Paisa',2=>'CENTS',3=>'CENTS',);
            $mcurrency = $currency[$data_array[0][csf("currency_id")]];
            $dcurrency = $dcurrency_arr[$data_array[0][csf("currency_id")]];
            echo number_to_words(number_format($data_array[0][csf('lc_value')],2), $mcurrency, $dcurrency);?>
        </div>
        <div id="position9">
            <? 
            if($data_array[0][csf('tolerance')]>0){
                echo '&plusmn '.$data_array[0][csf('tolerance')].'%';
            }
            ?>
        </div>
        <?php if($data_array[0][csf("payterm_id")]==1){ echo "<div id='position10'> &#10004; </div>" ; } ?>
        <div id="position11"> <? if($data_array[0][csf("tenor")]){ echo $data_array[0][csf("tenor")];}?> </div>
        <div id="position11a"> <? echo $supplier_country_name;?> </div>
        <div id="position12"> <? echo $doc_presentation_days; ?> </div>
        <?  
            $partial_shipment = $data_array[0][csf("partial_shipment")];
            if($partial_shipment==1){ echo "<div id='position13'> &#10004; </div>" ; }
            elseif($partial_shipment==2) { echo "<div id='position14'> &#10004; </div>" ; }

            $transhipment = $data_array[0][csf("transhipment")];
            if($transhipment==1){ echo "<div id='position15'> &#10004; </div>" ; }
            elseif($transhipment==2) { echo "<div id='position16'> &#10004; </div>" ; }

        ?>
        <div id="position17"> <? echo $lc_expiry_date;?> </div>
        <div id="position18"> <? echo $last_shipment_date;?>  </div>        
        <?
            $inco_term_id = $data_array[0][csf("inco_term_id")];
            if($inco_term_id==1) { echo "<div id='position19'> &#10004; </div>" ; }
            elseif($inco_term_id==3){echo "<div id='position20'> &#10004; </div>" ;	}
            elseif($inco_term_id==2){ echo "<div id='position21'> &#10004; </div>" ; }
            elseif($inco_term_id==5){ echo "<div id='position22'> &#10004; </div>" ; }
            elseif($inco_term_id==8){ echo "<div id='position23'> &#10004; </div>" ; }
            elseif($inco_term_id !=1 || $inco_term_id !=3 || $inco_term_id !=2 || $inco_term_id !=5 || $inco_term_id !=8) { echo ''; }

            if($data_array[0][csf("port_of_loading")]!=""){
                echo "<div id='position24'> &#10004; </div><div id='position25'>".$data_array[0][csf("port_of_loading")]." </div>";
            }

            if($data_array[0][csf("port_of_discharge")]!=""){
                echo "<div id='position26'> &#10004; </div><div id='position27'>".$data_array[0][csf("port_of_discharge")]." </div>";
            }
        ?> 
        <div id="position28"> <? echo $pi_category." FOR 100% ORIENTED READYMADE";?>  </div>
        <div id="position29"> <? echo $data_array[0][csf("insurance_company_name")];?> </div>
        <div id="position30"> <? echo $business_nature." Industry as per";?> </div>
        <div id="position31"> <? echo $data_array[0][csf("cover_note_no")];?> </div>
        <div id="position32"> <? echo $origin;?> </div>
        <div id="position33"> <? echo $data_array[0][csf("cover_note_date")];?> </div>
        <div id="position34"> <? echo $hs_code;?> </div>
        <div id="position35"> <? echo chop($pi_numbers,","); ?> </div>
        <div id="position36"> <? echo chop($pi_date,",");?> </div>
        <?
            if($data_array[0][csf("add_confirmation_of_credit")]==1){ echo "<div id='position37'> &#10004; </div>"; }
            else{echo "<div id='position38'> &#10004; </div>";}
        ?>
        <div id="position39"><? echo implode(', ',$export_lc_sc_no_arr);?> </div>
        <div id="position40"><? echo implode(', ',$export_lc_sc_date_arr);?> </div>
        <div id="position41">
            <?
                $term_cond=sql_select("SELECT terms from wo_booking_terms_condition where entry_form=105 and booking_no='$btb_system_id'");
                foreach($term_cond as $row)
                {
                    echo $row["TERMS"]."<br>";
                }
            ?> 
         </div>

    </body>

    <?
	exit();
}

?>