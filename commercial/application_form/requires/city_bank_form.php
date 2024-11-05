<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="btb_application_form")
{

     $sql = "SELECT importer_id,issuing_bank_id,lc_value,lc_date,currency_id,tenor,supplier_id,item_category_id,pi_id,port_of_loading,port_of_discharge,delivery_mode_id,last_shipment_date,lc_expiry_date,inco_term_place,doc_presentation_days,origin,inco_term_id,insurance_company_name,cover_note_no,cover_note_date,payterm_id,tolerance,lca_no,lc_number,lc_date,btb_system_id,pi_id,partial_shipment,transhipment,lcaf_no,remarks from com_btb_lc_master_details where id='$data'";
     // echo $sql;
    $data_array=sql_select($sql);

$is_lc_sc_sql=sql_select("SELECT lc_sc_id, is_lc_sc from com_btb_export_lc_attachment where import_mst_id=$data and is_deleted=0 and status_active=1");
    foreach($is_lc_sc_sql as $row)
    {
        if ($row[csf('is_lc_sc')] == 0) {
            $sql_lc = sql_select("SELECT id,export_lc_no, lc_date FROM com_export_lc  where id=" . $row[csf('lc_sc_id')]);
            foreach ($sql_lc as $lc_row) {
                $export_lc_sc_no_arr[$lc_row[csf("id")]] = "Export Contact No: ".$lc_row[csf("export_lc_no")];
                $export_lc_sc_date_arr[$lc_row[csf("id")]] = $lc_row[csf("lc_date")];
            }
        } else {
            $sql_sc = sql_select("SELECT id,contract_no,contract_date FROM com_sales_contract where id=" . $row[csf('lc_sc_id')]);
            foreach ($sql_sc as $sc_row) {
                $export_lc_sc_no_arr[$sc_row[csf("id")]] = "Export Contact No: ".$sc_row[csf("contract_no")];
                $export_lc_sc_date_arr[$sc_row[csf("id")]] = $sc_row[csf("contract_date")];
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
	$address = sql_select("SELECT id,plot_no,level_no,road_no,block_no,country_id,city,zip_code,irc_no,tin_number,vat_number,bang_bank_reg_no,bin_no,business_nature from lib_company where id = ".$data_array[0][csf('importer_id')]."");
    $company_address='';
	foreach($address as $row){
		$company_address.= $row[csf('plot_no')];
        if( $row[csf('level_no')]!=''){$company_address.= ', '.$row[csf('level_no')];}
        if( $row[csf('road_no')]!=''){$company_address.= ', '.$row[csf('road_no')];}
        if( $row[csf('block_no')]!=''){$company_address.= ', '.$row[csf('block_no')];}
        if( $row[csf('city')]!=''){$company_address.= ', '.$row[csf('city')];}
        if( $row[csf('zip_code')]!=''){$company_address.= ', '.$row[csf('zip_code')];}
        if( $row[csf('country_id')]!=''){$company_address.= ', '.$country_array[$row[csf('country_id')]];}
		$company_add[$row[csf('id')]]['irc_no'] = $row[csf('irc_no')];
		$company_add[$row[csf('id')]]['tin_number'] = $row[csf('tin_number')];
		$company_add[$row[csf('id')]]['vat_number'] = $row[csf('vat_number')];
		$company_add[$row[csf('id')]]['bin_number'] = $row[csf('bin_no')];
		$company_add[$row[csf('id')]]['bang_bank_reg_no'] = $row[csf('bang_bank_reg_no')];
	}
	$business_nature=explode(',',$address[0][csf('business_nature')]);
	foreach($business_nature as $row)
	{
		$business_nature_info=$business_nature_arr[$row].',';
	}
    $branch = return_field_value("branch_name","lib_bank","id=".$data_array[0][csf("issuing_bank_id")],"branch_name");

    $bank_address = return_field_value("ADDRESS","lib_bank","id=".$data_array[0][csf("issuing_bank_id")],"ADDRESS");
    $currency_sign = $currency_sign_arr[$data_array[0][csf("currency_id")]];

    $supplier_name = return_field_value("supplier_name","lib_supplier","id=".$data_array[0][csf("supplier_id")],"supplier_name");
    $supplier_add = return_field_value("address_1","lib_supplier","id=".$data_array[0][csf("supplier_id")],"address_1");

    //echo "select id, pi_number from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0";
    $pi_number_arr=return_library_array( "SELECT id, pi_number from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','pi_number');

    $pi_date_arr=return_library_array( "SELECT id, pi_date from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','pi_date');
    
    $hs_code_arr=return_library_array( "SELECT id, HS_CODE from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','HS_CODE');

    // $pi_date = return_field_value("pi_date","com_pi_master_details"," status_active = 1 AND is_deleted = 0 AND id=".$data_array[0][csf("pi_id")],"pi_date");
    $pi_cate_arr=return_library_array( "SELECT id, item_category_id from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','item_category_id');



    //echo $total_pi=count($pi_number_arr);

    $pi_numbers;

    if(count($pi_number_arr)>1){
        foreach($pi_number_arr as $key=>$row){
            $pi_number .= $row." date: ".$pi_date_arr[$key].",";
        }
        $pi_numbers .= "Pls Check down the below <br>others terms and condition.";
        $pi_date='';
    }
    else
    {
        $pi_numbers = $pi_number_arr[$data_array[0][csf("pi_id")]];
        $pi_date=date('d.m.Y',strtotime($pi_date_arr[$data_array[0][csf("pi_id")]]));
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
            margin:0;
            padding:0;
            background: url("../application_form/form_image/image/city.jpg");
            background-size:21.59cm 30.56cm;
            background-repeat: no-repeat;
        }
        div{ margin:0;padding:0;font-size:80%;}
        #position0{ position: absolute; margin-top: 110px; margin-left: 170px; }
        #position1{ position: absolute; margin-top: 135px; margin-left: 470px; }
        #position2{ position: absolute; margin-top: 210px; margin-left: 190px; }
        #position3{ position: absolute; margin-top: 230px; margin-left: 120px; }
        #position4{ position: absolute; margin-top: 250px; margin-left: 160px; }
        #position5{ position: absolute; margin-top: 250px; margin-left: 330px; }
        #position6{ position: absolute; margin-top: 250px; margin-left: 510px; }
        #position7{ position: absolute;margin-top: 300px; margin-left: 190px; }
        #position8{position: absolute;margin-top: 315px;margin-left: 120px;}
        #position9{position: absolute;margin-top: 395px;margin-left: 100px;}
        #position10{position: absolute;margin-top: 415px;margin-left: 240px;}
        #position11{position: absolute;margin-top: 415px;margin-left: 520px; }
        #position12{position: absolute;margin-top: 430px; margin-left: 220px;}
        #position13{position: absolute;margin-top: 430px; margin-left: 550px;}
        #position14{position: absolute;margin-top: 483px; margin-left: 130px;}
        #position15{position: absolute;margin-top: 480px; margin-left: 300px;}
        #position16{ position: absolute;margin-top: 500px;margin-left: 355px;  }
        #position17{position: absolute;margin-top: 525px;margin-left: 130px;}
        #position18{position: absolute;margin-top: 525px;margin-left: 340px;}
        #position19{position: absolute; margin-top: 600px; margin-left: 250px;  }
        #position20{ position: absolute;margin-top: 600px;margin-left: 545px; }
        #position21{position: absolute; margin-top: 645px;margin-left: 220px;}
        #position22{position: absolute;margin-top: 645px;margin-left: 508px; }
        #position23{position: absolute;margin-top: 1000px;margin-left:80px; }
        #position24{position: absolute;margin-top: 1030px;margin-left:80px; }
    </style>

    <body>
        <div id="position0"><?echo $branch;?></div>
        <div id="position1"><? echo $data_array[0][csf("lcaf_no")];?></div>
        <div id="position2"><? echo $company_name; ?></div>
        <div id="position3"><? echo $company_address; ?></div>
        <div id="position4"><? echo $company_add[$data_array[0][csf('importer_id')]]['irc_no']; ?></div>
        <div id="position5"><? echo $company_add[$data_array[0][csf('importer_id')]]['tin_number'] ?></div>
        <div id="position6"><? echo $company_add[$data_array[0][csf('importer_id')]]['bin_number'] ?></div>
        <div id="position7"><? echo $supplier_name;?> </div>
        <div id="position8"> <? echo $supplier_add;?></div>
        <div id="position9"> <? echo $pi_category;?> For 100% Export Oriented Knit Readymade <? echo  chop($business_nature_info,',');?> Garments Industry</div>
        <div id="position10"><? echo $origin;?></div>
        <div id="position11"><? echo $hs_code;?></div>
        <div id="position12"><? echo chop($pi_numbers,",");?></div>
        <div id="position13"><? echo chop($pi_date,",");?></div>
        <div id="position14"><? echo $currency_sign.' '.number_format($data_array[0][csf('lc_value')],2);?> </div>
        <div id="position15"><? echo $mcurrency.' '.number_to_words(number_format($data_array[0][csf('lc_value')], 2),'', $dcurrency); ?></div>
        <div id="position16"><? echo $data_array[0][csf("tenor")];?></div>
        <div id="position17"><? echo $lc_expiry_date;?></div>
        <div id="position18"><? echo $last_shipment_date;?> </div>
        <div id="position19"> <? echo implode(', ', $export_lc_sc_no_arr); ?> </div>
        <div id="position20"> <? echo implode(', ', $export_lc_sc_date_arr); ?> </div>
        <div id="position21"><? echo $data_array[0][csf("port_of_loading")];?></div>
        <div id="position22"><? echo $data_array[0][csf("port_of_discharge")];?> </div>
        <div id="position23"><? echo $pi_number;?> </div>
        <div id="position24"><? echo $data_array[0][csf("remarks")];?> </div>
    </body>
    <?
    exit();
}

?>