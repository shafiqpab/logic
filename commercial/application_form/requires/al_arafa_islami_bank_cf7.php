<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="btb_application_form")
{
	$currency_sign_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');
	$company_arr = return_library_array('SELECT id, company_name FROM lib_company','id','company_name');
	$supplier_arr = return_library_array('SELECT id, supplier_name FROM lib_supplier','id','supplier_name');
	$country_array = return_library_array("select id,country_name from lib_country where is_deleted=0","id","country_name");

	$sql_btb = "SELECT IMPORTER_ID,LC_VALUE,CURRENCY_ID,SUPPLIER_ID,TENOR,PI_ID,PORT_OF_LOADING,PORT_OF_DISCHARGE,ORIGIN,LAST_SHIPMENT_DATE,LC_EXPIRY_DATE,TRANSHIPMENT,PARTIAL_SHIPMENT,COVER_NOTE_NO,PAYTERM_ID,issuing_bank_id from com_btb_lc_master_details where id='$data'";
	
	$data_array=sql_select($sql_btb); 
	$address = sql_select("SELECT ID,PLOT_NO,LEVEL_NO,ROAD_NO,BLOCK_NO,COUNTRY_ID,CITY,ZIP_CODE from lib_company where id = ".$data_array[0]['IMPORTER_ID']."");
	foreach($address as $row){
		$company_add[$row['ID']]['plot_no'] = $row['PLOT_NO'];
		$company_add[$row['ID']]['level_no'] = $row['LEVEL_NO'];
		$company_add[$row['ID']]['road_no'] = $row['ROAD_NO'];
		$company_add[$row['ID']]['block_no'] = $row['BLOCK_NO'];
		$company_add[$row['ID']]['country_id'] = $row['COUNTRY_ID'];
		$company_add[$row['ID']]['city'] = $row['CITY'];
		$company_add[$row['ID']]['zip_code'] = $row['ZIP_CODE'];
	}

	$supplier_name = return_field_value("supplier_name","lib_supplier","id=".$data_array[0]["SUPPLIER_ID"],"supplier_name");
	$supplier_add = return_field_value("address_1","lib_supplier","id=".$data_array[0]["SUPPLIER_ID"],"address_1");

	$pi_cate_arr=return_library_array( "SELECT id, item_category_id from com_pi_master_details where id in(".$data_array[0]["PI_ID"].")  AND status_active = 1 AND is_deleted = 0",'id','item_category_id');
	$pi_number_arr=return_library_array( "SELECT id, pi_number from com_pi_master_details where id in(".$data_array[0]["PI_ID"].")  AND status_active = 1 AND is_deleted = 0",'id','pi_number');
	$pi_date_arr = return_field_value("pi_date","com_pi_master_details"," status_active = 1 AND is_deleted = 0 AND id=".$data_array[0][csf("pi_id")],"pi_date");

	if(count($pi_number_arr)>1){
		foreach($pi_number_arr as $row){
			$pi_number .= $row.", ";
		}
		foreach($pi_date_arr as $row){
			$pi_date .= $row.", ";
		}
		$pi_number=chop($pi_number,', ');
		$pi_date=chop($pi_date,', ');
		foreach($pi_cate_arr as $row){
			$pi_category .= $item_category[$row].",";
		}
		$pi_category=implode(", ",array_unique(explode(",",chop($pi_category,','))));
	}
	else
	{
		$pi_number = $pi_number_arr[$data_array[0]["PI_ID"]];
		$pi_category= $item_category[$pi_cate_arr[$data_array[0]["PI_ID"]]];
		$pi_date= $item_category[$pi_date_arr[$data_array[0]["PI_ID"]]];
	}

	$pi_qty_data=sql_select( "SELECT UOM, sum(quantity) as QTY from com_pi_item_details where pi_id in(".$data_array[0]["PI_ID"].")  AND status_active = 1 AND is_deleted = 0 group by uom");
	$pi_qty='';
	foreach($pi_qty_data as $row)
	{
		$pi_qty.=number_format($row["QTY"],2)." ".$unit_of_measurement[$row["UOM"]].", ";
	}

	if($data_array[0][csf("payterm_id")]==1){
		$pay_term_cond = $pay_term[$data_array[0][csf("payterm_id")]];
	}else{
		$pay_term_cond = $data_array[0][csf("tenor")]." Day's";
	}

	$pi_qty_data=sql_select( "SELECT UOM, sum(quantity) as QTY from com_pi_item_details where pi_id in(".$data_array[0]["PI_ID"].")  AND status_active = 1 AND is_deleted = 0 group by uom");
	$pi_qty='';
	foreach($pi_qty_data as $row)
	{
		$pi_qty.=number_format($row["QTY"],2)." ".$unit_of_measurement[$row["UOM"]].", ";
	}
	
	?>

	<style>

		body{
			margin:0;
			padding:0;  
			font-size:90%;
			background: url("../application_form/form_image/image/aibl_cf7.jpg");
			background-size:21.59cm 30.56cm;
			background-repeat: no-repeat;
		}
		

		#position1 {position: absolute;margin-top: 335px;margin-left: 270px;}
		#position2 {position: absolute;margin-top: 355px;margin-left: 100px;}
		#position3 {position: absolute;margin-top: 380px;margin-left: 300px;} 
		#position4 {position: absolute;margin-top: 400px;margin-left: 350px;}
		#position5 {position: absolute;margin-top: 425px;margin-left: 180px;}
		#position6 {position: absolute;margin-top: 470px;margin-left: 150px;}
		#position7 {position: absolute;margin-top: 485px;margin-left: 100px;}
		#position8 {position: absolute;margin-top: 520px;margin-left: 190px; width: 200px;}
		#position9 {position: absolute;margin-top: 520px;margin-left: 420px;}
		#position10 {position: absolute;margin-top: 570px;margin-left: 150px;}
		#position11 {position: absolute;margin-top: 720px;margin-left: 350px;}
		#position12 {position: absolute;margin-top: 740px;margin-left: 470px;}
		#position13 {position: absolute;margin-top: 820px;margin-left: 470px;}
		#position14 {position: absolute;margin-top: 840px;margin-left: 470px;}
	</style>

	<body>

		<div id="position1">
			<? echo $supplier_name;?>
		</div>
	    <div id="position2">
			<?echo $supplier_add;?>
		</div>
		<div id="position3">
			<? echo $currency_sign_arr[$data_array[0]["CURRENCY_ID"]].' '.number_format($data_array[0]['LC_VALUE'],2);?>
		</div>
		<div id="position4">
			<? echo $company_arr[$data_array[0]["IMPORTER_ID"]].", ".$company_add[$data_array[0]["IMPORTER_ID"]]['city'];?> 
	    </div>
		<div id="position5">
			<?echo $pay_term_cond ;?>
		</div>
		<div id="position6">
			<? echo $pi_category; ?> for 100% Export Oriented ready made Garments Ind.
		</div>
		<div id="position7">
			asper PI No. <? echo $pi_number."DT ".$pi_date; ?> 
		</div>
		<div id="position8">
			<?echo $data_array[0]['PORT_OF_LOADING'];?>
		</div>
		<div id="position9">
			<?echo $data_array[0]['PORT_OF_DISCHARGE'];?>
		</div>
		<div id="position10">
			<?echo $country_array[$data_array[0]['ORIGIN']];?>
		</div>
		<div id="position11">
			<?echo change_date_format($data_array[0]['LAST_SHIPMENT_DATE']);?>
		</div>
		<div id="position12">
			<?echo change_date_format($data_array[0]['LC_EXPIRY_DATE']);?>
		</div>
		<div id="position13">
			<?=($data_array[0]['TRANSHIPMENT']==1)?"Yes":"No";?>
		</div>
		<div id="position14">
			<?=($data_array[0]['PARTIAL_SHIPMENT']==1)?"Yes":"No";?>
		</div>
		<div id="position15">
			<? echo $pi_category; ?> QTY: <?echo rtrim($pi_qty,", ");?>
		</div>
	</body>
    <?
	exit();
}

?>