<?
/*-------------------------------------------- Comments
Purpose			: 	This form will create for Pubali Bank LCA Form
Functionality	:
JS Functions	:
Created by		:	Nayem
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

	$sql = "SELECT	importer_id,issuing_bank_id,lc_value,lc_date,currency_id,tenor,supplier_id,item_category_id,pi_id,port_of_loading,port_of_discharge,delivery_mode_id,last_shipment_date,lc_expiry_date,inco_term_place,doc_presentation_days,origin,inco_term_id,insurance_company_name,cover_note_no,cover_note_date,payterm_id,tolerance,lca_no,hs_code,lc_type_id from com_btb_lc_master_details where id='$data'";
	 //echo $sql;
	$data_array=sql_select($sql);

	/*$is_lc_sc_sql=sql_select("SELECT lc_sc_id, is_lc_sc from com_btb_export_lc_attachment where import_mst_id=$data and is_deleted=0 and status_active=1");
	foreach($is_lc_sc_sql as $row)
	{
		if($row[csf('is_lc_sc')]==0){
			$sql_lc=sql_select("SELECT id,export_lc_no FROM com_export_lc  where id=".$row[csf('lc_sc_id')]);
			foreach($sql_lc as $lc_row)
			{$export_lc_sc_no_arr[$lc_row[csf("id")]]=$lc_row[csf("export_lc_no")];}
		}
		else{
			$sql_sc=sql_select("SELECT id,contract_no FROM com_sales_contract where id=".$row[csf('lc_sc_id')]);
			foreach($sql_sc as $sc_row)
			{$export_lc_sc_no_arr[$sc_row[csf("id")]]=$sc_row[csf("contract_no")];}
		}
		$lc_sc_id_arr[]=$row[csf('lc_sc_id')];
	}
	$order_pcs_qty = return_field_value("sum(b.order_quantity) as order_quantity","com_export_lc_order_info a,wo_po_color_size_breakdown b","b.po_break_down_id=a.wo_po_break_down_id and a.com_export_lc_id in(".implode(',',$lc_sc_id_arr).")","order_quantity");*/
	$lc_sc_item_description_sql=sql_select("SELECT b.export_item_category as EXPORT_ITEM_CATEGORY
	from com_btb_export_lc_attachment a, com_export_lc b 
	where a.import_mst_id=$data and a.lc_sc_id=b.id and a.is_lc_sc=0 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1
	union all
	SELECT b.export_item_category as EXPORT_ITEM_CATEGORY
	from com_btb_export_lc_attachment a, com_sales_contract b 
	where a.import_mst_id=$data and a.lc_sc_id=b.id and a.is_lc_sc=1 and a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 group by export_item_category");
	$item_description='';
	foreach($lc_sc_item_description_sql as $row)
	{
		$item_description.=$export_item_category[$row['EXPORT_ITEM_CATEGORY']].', ';
	}

	//--------------lib
	$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');
	$company_name = return_field_value("company_name","lib_company","id=".$data_array[0][csf("importer_id")],"company_name");
	$country_array = return_library_array("select id,country_name from lib_country where is_deleted=0","id","country_name");
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
    
	$importer_id= $data_array[0][csf("importer_id")];
    $nature=return_field_value("business_nature","lib_company","id=".$data_array[0][csf("importer_id")],"business_nature");
    $business_nature_ar= explode(",", $nature);
    $business_nature='';
    if(count($business_nature_ar)>0){
     	foreach($business_nature_ar as $row){
			$business_nature.=$business_nature_arr[$row].', ';
		}
	}
	$business_nature=chop($business_nature,', ');
	// $supplier_name = return_field_value("supplier_name","lib_supplier","id=".$data_array[0][csf("supplier_id")],"supplier_name");
	// $supplier_add = return_field_value("address_1","lib_supplier","id=".$data_array[0][csf("supplier_id")],"address_1");

	//echo "select id, pi_number from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0";
	// $pi_number_arr=return_library_array( "SELECT id, pi_number from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','pi_number');
	
	$hs_code_arr=return_library_array( "SELECT id, HS_CODE from com_pi_master_details where id in(".$data_array[0][csf("pi_id")].")  AND status_active = 1 AND is_deleted = 0",'id','HS_CODE');

	// $pi_date = return_field_value("pi_date","com_pi_master_details"," status_active = 1 AND is_deleted = 0 AND id=".$data_array[0][csf("pi_id")],"pi_date");

	//echo $total_pi=count($pi_number_arr);
	if($data_array[0][csf("last_shipment_date")]!=''){
		$last_shipment_date=date('d.m.Y',strtotime($data_array[0][csf("last_shipment_date")]));
	}
	else
	{
		$last_shipment_date='';
	}

	if(count($hs_code_arr)>0){
		foreach($hs_code_arr as $row){
			$hs_code .= $row.", ";
		}
	}

	$origin = return_field_value("country_name"," lib_country","id=".$data_array[0][csf("origin")],"country_name");
	
	?>

 <style>

	body{
		margin:0;
		padding:0;  
		font-size:90%;
		background: url("../application_form/form_image/image/pubali_lca.jpg");
		background-size:21.59cm 30.56cm;
		background-repeat: no-repeat;
	}
	#position1{
        position: absolute;
        margin-top: 40px;
        margin-left: 340px;
    }
	#position2{
        position: absolute; 
        margin-top: 105px;
        margin-left: 210px;
    }
	#position3{
        position: absolute;
        margin-top: 105px;
        margin-left: 460px;
    }
    #position4{
        position: absolute;
        margin-top: 140px;
        margin-left: 310px;
    }
    #position5{
        position: absolute;
        margin-top: 162px;
        margin-left: 170px;
    }
    #position6{
        position: absolute;
        margin-top: 165px;
        margin-left: 500px;
    }
	#position7{
        position: absolute;
		margin-top: 190px;
        margin-left: 180px; 
    }
	#position8{
        position: absolute;
		margin-top: 330px;
        margin-left: 400px;
    }
    #position9{
        position: absolute;
        margin-top: 350px;
        margin-left: 400px;
    }
	#position10{
        position: absolute;
		margin-top: 365px;
        margin-left: 200px;
    }
    #position11{
        position: absolute;
        margin-top: 530px;
        margin-left: 40px;
		width:300px;
    }
    #position12{
        position: absolute;
        margin-top: 530px;
        margin-left: 400px;
		
    }
    </style>

	<body>
		<div id="position1">
			<!-- For Branch -->
			<? echo $branch;?>
		</div>
		<div id="position2">
			<!-- For LCA no -->
			<? echo $data_array[0][csf("lca_no")];?>
		</div>
		<div id="position3">
			<!-- For Shipping date -->
			<?
			$last_shipment_date_year=date('Y',strtotime($last_shipment_date));
			echo $last_shipment_date_year;?>
		</div>
		<div id="position4">
			<? echo $company_name;?>
			<? //echo " Plot# ".$company_add[$data_array[0][csf("importer_id")]]['plot_no'].", Road# ".$company_add[$data_array[0][csf("importer_id")]]['road_no'].", ".$company_add[$data_array[0][csf("importer_id")]]['city'].", ".$country_array[$company_add[$data_array[0][csf("importer_id")]]['country_id']]; ?> , <? //echo $origin;?>
			<? echo $company_add[$data_array[0][csf("importer_id")]]['city']; ?>
		</div>
		<div id="position5">
			<?= $irc_no;?>
		</div>
		<div id="position6">
			<!-- 2019-20-->  <? echo $renewal_year;?>
		</div>
		<div id="position7">
			<!-- For Sector of Industry-->  100% Export Oriented Readymade <? echo chop($item_description,', ');//$business_nature;?>  Industry
		</div>
		<div id="position8">
			<!-- For LC Type -->
			<?
			echo $lc_type[$data_array[0][csf("lc_type_id")]] ;
			?>
    	</div>
		<div id="position9">
			<?
				$dcurrency_arr=array(1=>'Paisa',2=>'CENTS',3=>'CENTS',);
				$mcurrency = $currency[$data_array[0][csf("currency_id")]];
				$dcurrency = $dcurrency_arr[$data_array[0][csf("currency_id")]];
			?>
			<b > <? echo $mcurrency.' '.$currency_sign.' '.number_format($data_array[0][csf('lc_value')],2);?></b>
		</div>
		<div id="position10">
			<b ><? echo number_to_words(number_format($data_array[0][csf('lc_value')],2), $mcurrency, $dcurrency);?></b>
		</div>
		<div id="position11">
			<? echo $item_category[$data_array[0][csf("item_category_id")]];?> 100% Export Oriented Readymade <? echo chop($item_description,', ');//$business_nature;?>  Industry
		</div>
		<div id="position12">
			H.S CODE: <? echo chop($hs_code,', ');?>
		</div>
	</body>
    <?
	exit();
}

?>