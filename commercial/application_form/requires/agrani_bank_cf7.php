<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="btb_application_form")
{
	$sql = "SELECT	importer_id,issuing_bank_id,lc_value,lc_date,currency_id,tenor,supplier_id,item_category_id,pi_id,port_of_loading,port_of_discharge,delivery_mode_id,last_shipment_date,lc_expiry_date,inco_term_place,doc_presentation_days,origin,inco_term_id,insurance_company_name,lcaf_no,cover_note_no,cover_note_date,payterm_id,tolerance,lc_number,btb_system_id,pi_id,partial_shipment,transhipment,application_date from com_btb_lc_master_details where id='$data'";
	 // echo $sql;
	$data_array=sql_select($sql);

 	$lc_sc_sql=sql_select("SELECT b.export_lc_no as LC_SC_NO,b.lc_date as LC_SC_DATE
	from com_btb_export_lc_attachment a, com_export_lc b 
	where a.import_mst_id=$data and a.lc_sc_id=b.id and a.is_lc_sc=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1
	union all
	SELECT b.contract_no as LC_SC_NO, b.contract_date as LC_SC_DATE
	from com_btb_export_lc_attachment a, com_sales_contract b 
	where a.import_mst_id=$data and a.lc_sc_id=b.id and a.is_lc_sc=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 ");
	if(empty($lc_sc_sql)) {echo "May be attachment not complete yet";die;}
	$lc_sc_no='';
	foreach($lc_sc_sql as $row)
	{
		$lc_sc_no=$row["LC_SC_NO"].', ';
		$lc_sc_date=change_date_format($row["LC_SC_DATE"]).', ';
	}
	$lc_sc_no=rtrim($lc_sc_no,', ');
	$lc_sc_date=rtrim($lc_sc_date,', ');
    //--------------lib
	$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');
	$company_name = return_field_value("company_name","lib_company","id=".$data_array[0][csf("importer_id")],"company_name");
	$country_array = return_library_array("select id,country_name from lib_country where is_deleted=0","id","country_name");

	$address = sql_select("SELECT id,plot_no,level_no,road_no,block_no,country_id,city,zip_code,irc_no,bin_no from lib_company where id = ".$data_array[0][csf('importer_id')]."");
	foreach($address as $row){
		$company_add[$row[csf('id')]]['plot_no'] = $row[csf('plot_no')];
		$company_add[$row[csf('id')]]['level_no'] = $row[csf('level_no')];
		$company_add[$row[csf('id')]]['road_no'] = $row[csf('road_no')];
		$company_add[$row[csf('id')]]['block_no'] = $row[csf('block_no')];
		$company_add[$row[csf('id')]]['country_id'] = $row[csf('country_id')];
		$company_add[$row[csf('id')]]['city'] = $row[csf('city')];
		$company_add[$row[csf('id')]]['zip_code'] = $row[csf('zip_code')];
		$company_add[$row[csf('id')]]['irc_no'] = $row[csf('irc_no')];
		$company_add[$row[csf('id')]]['bin_no'] = $row[csf('bin_no')];
	}
	//print_r($company_add);

	$branch = return_field_value("branch_name","lib_bank","id=".$data_array[0][csf("issuing_bank_id")],"branch_name");

	// $bank_address = return_field_value("ADDRESS","lib_bank","id=".$data_array[0][csf("issuing_bank_id")],"ADDRESS");
	$currency_sign = $currency_sign_arr[$data_array[0][csf("currency_id")]];

	$supplier_name = return_field_value("supplier_name","lib_supplier","id=".$data_array[0][csf("supplier_id")],"supplier_name");
	$supplier_add = return_field_value("address_1","lib_supplier","id=".$data_array[0][csf("supplier_id")],"address_1");

	//echo "select id, pi_number from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0";
	$pi_number_arr=return_library_array( "SELECT id, pi_number from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','pi_number');
	$pi_cate_arr=return_library_array( "SELECT id, item_category_id from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','item_category_id');
	
	$hs_code_arr=return_library_array( "SELECT id, HS_CODE from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','HS_CODE');

	$pi_date = return_field_value("pi_date","com_pi_master_details"," status_active = 1 AND is_deleted = 0 AND id=".$data_array[0][csf("pi_id")],"pi_date");

	$total_pi=count($pi_number_arr);

	if($total_pi>1){
		foreach($pi_number_arr as $row){
			$pi_number .= $row.", ";
		}
		$pi_number=chop($pi_number,', ');
		foreach($pi_cate_arr as $row){
			$pi_category .= $item_category[$row].",";
		}
		$pi_category=implode(", ",array_unique(explode(",",chop($pi_category,','))));
		$pi_date='';
	}
	else
	{
		$pi_number = $pi_number_arr[$data_array[0][csf("pi_id")]];
		$pi_category= $item_category[$pi_cate_arr[$data_array[0][csf("pi_id")]]];
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
	
	$inco_term_id=$incoterm[$data_array[0][csf("inco_term_id")]];

	if($data_array[0][csf("cover_note_date")]!=''){
		$cover_note_date=date('d.m.Y',strtotime($data_array[0][csf("cover_note_date")]));
	}
	else
	{
		$cover_note_date='';
	}

	$nature=return_field_value("business_nature","lib_company","id=".$data_array[0][csf("importer_id")],"business_nature");
	$business_nature_arr= explode(",", $nature);
	$business_nature;
	if(count($business_nature_arr)>0){
		foreach($business_nature_arr as $row)
		{
			if($row==2){$business_nature .= "Knit ";}
			elseif($row==3){$business_nature .= "Woven ";}
			elseif($row==4){ $business_nature .= "Trims "; }
			elseif($row==5){ $business_nature .= "Print "; }
			elseif($row==6){ $business_nature .= "Embroidery "; }
			elseif($row==7){ $business_nature .= "Wash "; }
			elseif($row==8){ $business_nature .= "Yarn Dyeing "; }
			elseif($row==9){ $business_nature .= "AOP "; }
			elseif($row==100){ $business_nature .= "Sweater "; }
		}
	} 

	$btb_system_id=$data_array[0][csf("btb_system_id")];
	$terms_res=sql_select("SELECT id, TERMS from wo_booking_terms_condition where entry_form=105 and booking_no= '$btb_system_id' ");
	$terms_data="";
	$i=1;
	foreach($terms_res as $val)
	{
		$terms_data.=$i.". ".$val["TERMS"]."<br>";
		$i++;
	}
	?>

	<style>
        body{margin:0;padding:0; font-size: 90%;
            background: url("../application_form/form_image/image/agrani_cf7.jpg");
            background-size:21.59cm 30.56cm;
            background-repeat: no-repeat;
        }

        #position1{position: absolute;margin-top: 180px;margin-left: 100px; }
        #position2{position: absolute;margin-top: 260px;margin-left: 120px;}
        #position3{position: absolute;margin-top: 270px;margin-left: 120px;}
        #position4{position: absolute;margin-top: 370px;margin-left: 300px;}
        #position5{position: absolute;margin-top: 400px;margin-left: 150px;}
        #position6{position: absolute;margin-top: 450px;margin-left: 600px;}
        #position7{position: absolute;margin-top: 470px;margin-left: 60px;}
        #position8{position: absolute;margin-top: 520px;margin-left: 550px;}
        #position9{position: absolute;margin-top: 700px;margin-left: 70px;}
        #position10{position: absolute;margin-top: 700px;margin-left: 220px;}
        #position11{position: absolute;margin-top: 700px;margin-left: 520px;}
        #position12{position: absolute;margin-top: 720px;margin-left: 280px;}
        #position13{position: absolute;margin-top: 720px;margin-left: 420px;}
        #position14{position: absolute;margin-top: 735px;margin-left: 280px;}
        #position15{position: absolute;margin-top: 815px;margin-left: 100px;}
        #position16{position: absolute;margin-top: 830px;margin-left: 100px;}
        #position17{position: absolute;margin-top: 860px;margin-left: 370px;}

   </style>

    <body>
        <div class="template clear">
			<div id="position1">
				<? echo $company_name.", ".$company_add[$data_array[0][csf("importer_id")]]['city']; ?>
			</div>
			<div id="position2">
				<?
					if($data_array[0][csf("payterm_id")]==2){echo $data_array[0][csf("tenor")]." Days" ;}
				?>
			</div>
			<div id="position3">
				<? echo $supplier_name.", ".$supplier_add;?>
			</div>
			<div id="position4">
				<?
					$dcurrency_arr=array(1=>'Paisa',2=>'CENTS',3=>'CENTS',);
					$mcurrency = $currency[$data_array[0][csf("currency_id")]];
					$dcurrency = $dcurrency_arr[$data_array[0][csf("currency_id")]];
				?>
				<? echo $mcurrency.' '.$currency_sign.' '.number_format($data_array[0][csf('lc_value')],2);?>
			</div>
			<div id="position5">
				<? echo number_to_words(number_format($data_array[0][csf('lc_value')],2), $mcurrency, $dcurrency);?>
			</div>
			<div id="position6">
				<? echo $pi_category;?>
			</div>
			<div id="position7">
				FOR 100% EXPORT ORIENTED <? echo $business_nature; ?> READYMADE GARMENTS AS PROFORMA INVOICE <?=$pi_number.' DT.'.$pi_date;?>
			</div>
			<div id="position8">
				<? echo $country_array[$data_array[0][csf("origin")]];?>
			</div>
			<div id="position9">
				<? echo $data_array[0][csf("port_of_loading")];?>
			</div>
			<div id="position10">
				<? echo $data_array[0][csf("port_of_discharge")];?>
			</div>
			<div id="position11">
				<? echo $shipment_mode[$data_array[0][csf("delivery_mode_id")]];?>
			</div>
			<div id="position12">
				<? echo $last_shipment_date;?>
			</div>
			<div id="position13">
				<? echo $lc_expiry_date;?>
			</div>
			<div id="position14">
				<? if($data_array[0][csf("doc_presentation_days")]){echo $data_array[0][csf("doc_presentation_days")]." Days";}?>
			</div>
			<div id="position15">
				<? echo "Export LC/SC NO ".$lc_sc_no ." DATED ".$lc_sc_date;?>
			</div>
			<div id="position16">
				<? echo $terms_data;?>
			</div>
			<div id="position17">
				<? echo $company_add[$data_array[0][csf("importer_id")]]['irc_no'];?>
			</div>

        </div>
    </body>
    <?
	exit();
}

?>