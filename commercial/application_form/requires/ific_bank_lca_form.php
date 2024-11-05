<? 
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];

if($action=="btb_application_form")
{

	$sql = "SELECT IMPORTER_ID, ISSUING_BANK_ID, LC_VALUE, CURRENCY_ID, ITEM_CATEGORY_ID, PI_ID, PAYTERM_ID, TOLERANCE, LCA_NO from com_btb_lc_master_details where id='$data' and status_active=1 and is_deleted=0";
	
	$data_array=sql_select($sql); 

	$currency_arr=array(1=>'৳',2=>'$',3=>'€',4=>'€',5=>'$',6=>'£',7=>'¥');
	//$importer_id=$data_array[0]['ITEM_CATEGORY_ID'];
	
	$company_name = return_field_value("company_name","lib_company","id=".$data_array[0]['IMPORTER_ID'],"company_name");
	$country_array = return_library_array("select id,country_name from lib_country where is_deleted=0","id","country_name");

	$com_address = sql_select("SELECT ID, PLOT_NO, LEVEL_NO, ROAD_NO, BLOCK_NO, COUNTRY_ID, CITY, ZIP_CODE, IRC_NO, TIN_NUMBER, VAT_NUMBER, BANG_BANK_REG_NO, IRC_EXPIRY_DATE from lib_company where id=".$data_array[0]['IMPORTER_ID']."");
	foreach($com_address as $row){
		$company_add[$row['ID']]['PLOT_NO']    = $row['PLOT_NO'];
		$company_add[$row['ID']]['LEVEL_NO']   = $row['LEVEL_NO'];
		$company_add[$row['ID']]['ROAD_NO']    = $row['ROAD_NO'];
		$company_add[$row['ID']]['BLOCK_NO']   = $row['BLOCK_NO'];
		$company_add[$row['ID']]['COUNTRY_ID'] = $row['COUNTRY_ID'];
		$company_add[$row['ID']]['CITY']       = $row['CITY'];
		$company_add[$row['ID']]['ZIP_CODE']   = $row['ZIP_CODE'];
		$company_add[$row['ID']]['IRC_NO']     = $row['IRC_NO'];
		$company_add[$row['ID']]['TIN_NUMBER'] = $row['TIN_NUMBER'];
		$company_add[$row['ID']]['VAT_NUMBER'] = $row['VAT_NUMBER'];
		$company_add[$row['ID']]['BANG_BANK_REG_NO'] = $row['BANG_BANK_REG_NO'];
		$company_add[$row['ID']]['IRC_EXPIRY_DATE']  = $row['IRC_EXPIRY_DATE'];
	}
	//print_r($company_add);
	/*function calculateFiscalYearForDate($inputDate, $fyStart, $fyEnd){
	    $date = strtotime($inputDate);
	    $inputyear = strftime('%Y',$date);
	       
	    $fyStartdate = strtotime($fyStart.$inputyear);
	    $fyEnddate = strtotime($fyEnd.$inputyear);

	    if($date < $fyEnddate) $fy = intval($inputyear);
	    else $fy = intval(intval($inputyear) + 1);
	    return $fy;
	}

	$irc_expiry_date=$company_add[$data_array[0]['IMPORTER_ID']]['IRC_EXPIRY_DATE'];
	$fiscal_year = calculateFiscalYearForDate("$irc_expiry_date","7/1","6/30");
	$shipping_period=$fiscal_year.'-'.($fiscal_year+1);*/
	$shipping_period="2020-2021";

	$currency_sign = $currency_arr[$data_array[0]['CURRENCY_ID']];
	$currency_id = $data_array[0]['CURRENCY_ID'];
	//$mcurrency, $dcurrency;
	$dcurrency="";
	if($currency_id==1) {
		$mcurrency='Taka';
		$dcurrency='Paisa';
	} else if($currency_id==2) {
		$mcurrency='USD';
		$dcurrency='Cents';
	} else if($currency_id==3){
		$mcurrency='Euro';
		$dcurrency='Cents';
	}

	$lc_value=$data_array[0]['LC_VALUE'];
	$tolerance=$data_array[0]['TOLERANCE']/100;
	$amount = $lc_value+($lc_value*$tolerance);

	$hs_code_array = return_library_array("select ID, HS_CODE from com_pi_master_details where id in(".$data_array[0]['PI_ID'].") and status_active=1 and is_deleted=0 order by id","ID","HS_CODE");
	//echo '<pre>';print_r($hs_code_array);
	$i=1;
	if(count($hs_code_array)>0){
		foreach($hs_code_array as $val){
			if ($val !='') $hs_codes .= $val.",";
			if ($i==1) $hs_code = $val;
			$i++;			
		}			
	}
	$hs_codes = $poIds = implode(',',array_unique(explode(',',chop($hs_codes,','))));

    $item_cat = $item_category[$data_array[0]['ITEM_CATEGORY_ID']];
	?>
    
<style>
/*printer setup 0,0,0,0*/
/*21cm,33.5cm    21.5 34.5*/
.height{ height:15px!important;}
body{margin:0;padding:0; line-height:20px;}
#form_body{
	background:url(../application_form/form_image/ific_bank_lca_form.jpg);
	width:8.5in; height:14in; overflow:hidden; background-repeat: no-repeat; background-size:21.59cm 35.56cm; padding:0; margin:0;
}

#form{padding-left:0px;	margin:0px; padding-top: 158px; font-size:12px; height:30cm; overflow:hidden; font-weight:bold;}
.clear_both{clear:both; overflow:hidden; }
@media print {#form{ border:none;}}


.branch{ width: 100%; margin:0 0 0 290px; font-size:18px; height: 1.3cm;}
.shipping_period{ margin:3px 160px 0 0; text-align:right; font-size:18px;}
.applicant_beneficiary_block{
	position: relative;	width: 100%; overflow: hidden;	margin: 10px 0 0 0;	height: 1.6cm;
}

.beneficiary_name{ width: 100%;  margin:18px 0 0 400px; padding: 0; font-size: 18px; }
.beneficiary_name_address{ width: 100%; float: left; margin:0 0 0 127px; padding: 0; font-size: 17px; text-align: left;}

.irc_number { position: relative; width: 100%; overflow: hidden; margin: 0; height: .5cm; }
.irc_no_left{float: left; margin: 0 0 0 235px; font-size: 16px;}
.irc_no_right{float: left; margin: 0 0 0 250px; font-size: 16px;}


.sector_of_ind{width: 100%; height: .5cm; font-size: 17px; margin: 0 0 0 265px;} /*margin: 0 0 0 270px;*/
.reguler{width: 100%; height: 1.6cm; font-size: 17px; margin: 0 0 0 385px;}
.source_of_finance{width: 100%; height: 2.3cm; font-size: 17px; margin: 0 0 0 450px;}
.amount_block{width: 100%; height: 0.5cm; margin: 2px 0 0 376px; font-size: 17px;}
.number_to_words{width: 100%; height: 1.8cm; font-size: 11px; margin: 1px 0 0 228px;}

.hs_code{width: 100%; height: 2cm; font-size: 17px; margin: 0 0 0 450px;}
.category_hscode{width: 100%; height: 2cm; font-size: 17px; margin: 2px 0 0 0;}
.category{width: 42.3%; height: 1.5cm; font-size: 17px; float: left; text-align: center;}
.hscode{width: 14%; height: 1.5cm; font-size: 17px; float: left; word-break: break-all;}
.others{width: 43.7%; height: 1.5cm; font-size: 17px; float: left;}

</style>

<div id="form_body">
	<div id="form">
		<div class="branch">&nbsp;Gulshan</div>
		<div class="shipping_period">&nbsp;<? echo $shipping_period; ?></div>
		<div class="applicant_beneficiary_block">
			<div class="beneficiary_name">
				<? echo $company_name."&nbsp;".$company_add[$data_array[0]['IMPORTER_ID']]['PLOT_NO']; ?>			
			</div>
			<div class="beneficiary_name_address">
				<? echo $company_add[$data_array[0]['IMPORTER_ID']]['LEVEL_NO'].", ".$company_add[$data_array[0]['IMPORTER_ID']]['ROAD_NO'].", ".$company_add[$data_array[0]['IMPORTER_ID']]['CITY'].", ".$country_array[$company_add[$data_array[0]['IMPORTER_ID']]['COUNTRY_ID']]; ?>
			</div>
		</div>
		<div class="irc_number">
			<div class="irc_no_left"><? echo $company_add[$data_array[0]['IMPORTER_ID']]['IRC_NO']; ?></div>
			<div class="irc_no_right"><? echo $shipping_period; ?></div>
		</div>

		<div class="sector_of_ind">RMG(SWEATER)</div>
		<div class="reguler">REGULER</div>	
		<div class="source_of_finance">BTB LC&nbsp;
			<? 
			if ($data_array[0]['PAYTERM_ID'] == 3) 
				echo "/&nbsp".$pay_term[$data_array[0]['PAYTERM_ID']];
			else '';
			?></div>
		<div class="amount_block"><? echo $currency_sign.' '.number_format($amount,2); ?></div>
		<div class="number_to_words"><? echo number_to_words(number_format($amount,2), $mcurrency, $dcurrency); ?></div>
		<div class="hs_code"><? echo $hs_code; ?></div>
		<div class="category_hscode">
			<div class="category"><? echo $item_cat; ?></div>
			<div class="hscode"><? echo $hs_codes; ?></div>
			<div class="others"></div>
		</div>	
    </div>
</div>
<!-- <span class="amount"><? //echo $currency_sign.' '.number_format($amount,2); ?></span><span class="number_to_words">(<? //echo number_to_words(number_format($amount,2), $mcurrency, $dcurrency); ?>)</span> -->
    <?
	exit();
}

?>


 