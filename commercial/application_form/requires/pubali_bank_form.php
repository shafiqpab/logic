<?
/*-------------------------------------------- Comments
Purpose			: 	This form is created for Hameem group ---Pubali Bank CF7 Form
Functionality	:
JS Functions	:
Created by		:	REZA
Creation date 	: 	8-7-2021
Updated by 		: 	
Update date		: 	
QC Performed BY	:
QC Date			:
Comments		:
*/

header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="btb_application_form")
{

	 $sql = "SELECT	importer_id,issuing_bank_id,lc_value,lc_date,currency_id,tenor,supplier_id,item_category_id,pi_id,port_of_loading,port_of_discharge,delivery_mode_id,last_shipment_date,lc_expiry_date,inco_term_place,doc_presentation_days,origin,inco_term_id,insurance_company_name,cover_note_no,cover_note_date,payterm_id,tolerance,lca_no,lc_number,lc_date,btb_system_id,pi_id,partial_shipment,transhipment,remarks,ADVISING_BANK,ADVISING_BANK_ADDRESS from com_btb_lc_master_details where id='$data'";
	 // echo $sql;
	$data_array=sql_select($sql);

// $is_lc_sc_sql=sql_select("SELECT lc_sc_id, is_lc_sc from com_btb_export_lc_attachment where import_mst_id=$data and is_deleted=0 and status_active=1");
	// foreach($is_lc_sc_sql as $row)
	// {
	// 	if($row[csf('is_lc_sc')]==0){
	// 		$sql_lc=sql_select("SELECT id,export_lc_no FROM com_export_lc  where id=".$row[csf('lc_sc_id')]);
	// 		foreach($sql_lc as $lc_row)
	// 		{
	// 			$export_lc_sc_no_arr[$lc_row[csf("id")]]=$lc_row[csf("export_lc_no")];
	// 		}
	// 	}
	// 	else{
	// 		$sql_sc=sql_select("SELECT id,contract_no FROM com_sales_contract where id=".$row[csf('lc_sc_id')]);
	// 		foreach($sql_sc as $sc_row)
	// 		{
	// 			$export_lc_sc_no_arr[$sc_row[csf("id")]]=$sc_row[csf("contract_no")];
	// 		}
	// 	}

	// 	$lc_sc_id_arr[]=$row[csf('lc_sc_id')];

	// }


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

	$bank_address = return_field_value("ADDRESS","lib_bank","id=".$data_array[0][csf("issuing_bank_id")],"ADDRESS");
	$currency_sign = $currency_sign_arr[$data_array[0][csf("currency_id")]];


	$supplier_name = return_field_value("supplier_name","lib_supplier","id=".$data_array[0][csf("supplier_id")],"supplier_name");
	$supplier_add = return_field_value("address_1","lib_supplier","id=".$data_array[0][csf("supplier_id")],"address_1");
	$supplier_add_2 = return_field_value("address_2","lib_supplier","id=".$data_array[0][csf("supplier_id")],"address_2");

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
$via=$data_array[0][csf("delivery_mode_id")];
if($via!=""){
    if($via==1){
        $via_by="Sea";
    }elseif($via==2){
        $via_by="Air";
    }elseif($via==3){
        $via_by="Road";
    }elseif($via==4){
        $via_by="Train";
    }elseif($via==5){
        $via_by="Sea/Air";
    }elseif($via==6){
        $via_by="Road/Air";
    }elseif($via==7){
        $via_by="Courier";
    }
}


$is_lc_sc_sql=sql_select("select lc_sc_id, is_lc_sc from com_btb_export_lc_attachment where import_mst_id=$data and is_deleted=0 and status_active=1");



if(empty($is_lc_sc_sql)) {echo "May be attachment not complete yet";die;}
$export_lc_sc_no_arr;
$export_lc_sc_date_arr;
foreach($is_lc_sc_sql as $row)
{
    if($row[csf('is_lc_sc')]==0){
        $sql_lc=sql_select("SELECT id,export_lc_no, lc_date FROM com_export_lc  where id=".$row[csf('lc_sc_id')]);
        foreach($sql_lc as $lc_row)
        {
            $export_lc_sc_no_arr[$lc_row[csf("id")]].=$lc_row[csf("export_lc_no")];
            $export_lc_sc_date_arr[$lc_row[csf("id")]].=$lc_row[csf("lc_date")];
        }
    }
    else{
		
		$sql_sc=sql_select("SELECT id,contract_no,CONTRACT_DATE as contrct_date FROM com_sales_contract where id=".$row[csf('lc_sc_id')]);
        foreach($sql_sc as $sc_row)
        {
            $export_lc_sc_no_arr[$sc_row[csf("id")]].=$sc_row[csf("contract_no")];
            $export_lc_sc_date_arr[$sc_row[csf("id")]].=$sc_row[csf("contrct_date")];
        }
    }
    
    $lc_sc_id_arr[]=$row[csf('lc_sc_id')];

}


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
           /* background: url("../application_form/form_image/image/pubali_cf7.jpg");*/
            background-size:21.59cm 30.56cm;
            background-repeat: no-repeat;
        }

        #position1 {
            position: absolute;
            margin-top: 105px;
            margin-left: 50px; 
        }

        #position2 {
            position: absolute;
            margin-top: 200px;
            margin-left: 120px; 
        }

        #position3 {
            position: absolute;
            margin-top: 215px;
            margin-left: 130px;
        }

        #position4 {
            position: absolute;
            margin-top: 255px;
            margin-left: 100px;
        }

        #position5 {
            position: absolute;
            margin-top: 255px;
            margin-left: 280px;
        }

        #position6 {
            position: absolute;
            margin-top: 255px;
            margin-left: 550px;
        }

        #position7 {
            position: absolute;
            margin-top: 310px;
            margin-left: 100px;
        }

        #position8 {
            position: absolute;
            margin-top: 325px;
            margin-left: 130px; 
        }
        #position8a {
            position: absolute;
            margin-top: 325px;
            margin-left: 130px;
        }

        #position9 {
            position: absolute;
            margin-top: 420px;
            margin-left: 70px;
        }

        #position10 {
            position: absolute;
            margin-top: 450px;
            margin-left: 200px;
            width: 300px;
        }

        #position11 {
            position: absolute;
            margin-top: 450px;
            margin-left: 400px;
        }

        #position12 {
            position: absolute;
            margin-top: 450px;
            margin-left: 520px;
        }

        #position13 {
            position: absolute; 
            margin-top: 470px;
            margin-left: 250px;
        }

        #position14 {
            position: absolute;
            margin-top: 470px;
            margin-left: 500px;
        }

        #position15 {
            position: absolute; 
            margin-top: 530px;
            margin-left: 120px;
        }

        #position16 {
            position: absolute; 
            margin-top: 530px; 
            margin-left: 370px;
            width: 300px;
        }

        #position17 {
            position: absolute;
            margin-top: 630px;
            margin-left: 270px;
        }

        #position18 {
            position: absolute; 
            margin-top: 560px;
            margin-left: 130px;
        }

        #position19 {
            position: absolute;
            margin-top: 560px;
            margin-left: 330px;
        }

        #position20 {
            position: absolute;
            margin-top: 560px;
            margin-left: 650px;
        }

        #position21 {
            position: absolute;
            margin-top: 580px;
            margin-left: 130px;  
        }

        #position22 {
            position: absolute;
            margin-top: 580px;
            margin-left: 320px;
        }

        #position23 {
            position: absolute;
            margin-top: 580px;
            margin-left: 500px; 
        }

        #position24 {
            position: absolute;
            margin-top: 605px;
            margin-left: 230px;
        }

        #position25 {
            position: absolute;
            margin-top: 650px;
            margin-left: 220px;
            width: 150px;
        }

        #position26 {
            position: absolute; 
            margin-top: 650px;
            margin-left: 430px;
            /* width: 250px; */
        }

        #position27 {
            position: absolute;
            margin-top: 730px;
            margin-left: 170px; 
        }

        #position28 {
            position: absolute;
            margin-top: 730px;
            margin-left: 500px;
        }
        #position28a {
            position: absolute;
            margin-top: 820px;
            margin-left: 280px;
        }
        #position28b {
            position: absolute; 
            margin-top: 845px;
            margin-left: 170px;
        }
        #position28c {
            position: absolute;
            margin-top: 845px;
            margin-left: 400px; 
        }

        #position29 {
            position: absolute;
            margin-top: 925px;
            margin-left: 305px;
        }

        #position30 {
            position: absolute;
            margin-top: 950px;
            margin-left: 145px;
        }

        #position31 {
            position: absolute;
            margin-top: 1030px;
            margin-left: 280px;
        }

    </style>

<body>
    <div id="position1">
    <? echo $branch ?><br>
    <? echo $bank_address;?>
    </div>
    <div id="position2">
    <? echo $company_name;?>
    </div>
    <div id="position3">
    <? echo $company_add[$data_array[0][csf("importer_id")]]['city']?>
    </div>
    <div id="position4">
    <? echo $irc_no?>
    </div>
    <div id="position5">
    <? echo $company_add[$data_array[0][csf("importer_id")]]['tin_number'];?> 
    </div>
    <div id="position6">
    <? echo $company_add[$data_array[0][csf("importer_id")]]['vat_number'];?>
    </div>
    <div id="position7">
    <? echo $supplier_name;?>
    </div>
    <div id="position8">
    <? echo $supplier_add ?>
    </div>
    <div id="position8a">
    <? echo $supplier_add_2 ?>
    </div>
 
    <div id="position9">
    <? echo $item_category[$data_array[0][csf("item_category_id")]]." FOR 100% EXPORT ORIENTED READYMADE ".$business_nature."GARMENTS INDUSTRY.";?>
    </div>
    <div id="position10">
    <? echo chop($pi_numbers,","); ?>
    </div>
    <div id="position11">
    <? echo chop($pi_date,",");?>
    </div>
    <div id="position12">
    <? echo $supplier_name;?>
    </div>
    <div id="position13">
    <? echo $origin;?> 
    </div>
    <div id="position14">
    <? echo $hs_code;?> 
    </div>
    <div id="position15">
    <? echo $data_array[0][csf("lc_value")];?>
    </div>
    <div id="position16">
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
    <? echo number_to_words(number_format($data_array[0][csf("lc_value")],2), $mcurrency, $dcurrency) ?>
    </div>
    
    <div id="position17">
    <? echo $data_array[0][csf("tenor")]; ?>
    </div>
    <div id="position18">
    <? echo $data_array[0][csf("port_of_loading")];?>
    </div>
    <div id="position19">
    <? echo $data_array[0][csf("port_of_discharge")];?>
    </div>
    <div id="position20">
    <? echo $via_by; ?>
    </div>
    <div id="position21">
    <? echo $last_shipment_date;?>
    </div>
    <div id="position22">
    <? echo $lc_expiry_date;?>
    </div>
    <div id="position23">
    (DRAFTS NOT REQUIRED)
    </div>
    <div id="position24">
   <? echo $doc_presentation_days; ?>
    </div>
    <div id="position25">
   <? echo implode(', ',$export_lc_sc_no_arr);?>
          
    </div>
    <div id="position26">
    DT: <? echo implode(', ',$export_lc_sc_date_arr);?>
    </div>

    <div id="position27">
    <? echo $data_array[0][csf("port_of_loading")];?>
    </div>
    <div id="position28">
    <? echo $data_array[0][csf("port_of_discharge")];?>
    </div>
    <div id="position28a">
    <? echo $data_array[0][csf("insurance_company_name")];?>
    </div>
    <div id="position28b">
    <? echo $data_array[0][csf("cover_note_no")];?>
    </div>
    <div id="position28c">
    <? 
    if($data_array[0][csf("cover_note_date")] !=''){
        $cover_numbers = $data_array[0][csf("cover_note_date")];
        echo chop(date('d.m.Y',strtotime($cover_numbers)),",");
    }

    ?>
    </div>
    <div id="position29">
    <? echo $branch ?>
    </div>
    <div id="position30">
    <? echo $branch ?>
    </div>
    <div id="position31">
    <?= "Advising / Party Bank: ".$data_array[0][csf("ADVISING_BANK")];?><br>
    <?= $data_array[0][csf("ADVISING_BANK_ADDRESS")];?>
    </div>
  
</body>
    <?
	exit();
}

?>


