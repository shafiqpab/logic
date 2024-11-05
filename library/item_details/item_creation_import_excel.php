<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../includes/common.php');
$permission=$_SESSION['page_permission'];

function return_domestic_rate($rate,$ile_cost,$exchange_rate,$conversion_factor){
	$rate_ile=$rate+$ile_cost;
	$rate_ile_exchange=$rate_ile*$exchange_rate;
	$doemstic_rate=$rate_ile_exchange/$conversion_factor;
	return $doemstic_rate;	
}

//$specialCharactersArr = array(0=>'*', 1=>'\'', 2=>'£', 3=>'$', 4=>'&', 5=>'(', 6=>')', 7=>'#', 8=>'~', 9=>'|', 10=>'=', 11=>'_', 12=>'"', 13=>'`', 14=>'^', 15=>'\\');
		
function check_special_character($string)
{
	$special_charater='*\'£$&()#~|=_"`^\\';
	$specialCharactersArr = str_split($special_charater);
	$splitStringArr=str_split($string);
	$result=array_diff($specialCharactersArr,$splitStringArr);
	if (count($result)<count($specialCharactersArr)) return 1;
	else return 0;
}

$cdate=date("d-m-Y");
include('excel_reader.php');
$output = `uname -a`;
if( isset( $_POST["submit"] ) )
{	
	//error_reporting(E_ALL);
	//ini_set('display_errors', '1');	
	extract($_REQUEST);
	
	foreach (glob("files/"."*.xls") as $filename){			
		@unlink($filename);
	}
	foreach (glob("files/"."*.xlsx") as $filename){			
		@unlink($filename);
	}

	$source_excel = $_FILES['uploadfile']['tmp_name'];
	$targetzip ='files/'.$_FILES['uploadfile']['name'];
	$file_name=$_FILES['uploadfile']['name'];
    //echo $source_excel.'**'.$targetzip.'**'.$file_name;die;
	unset($_SESSION['excel']);
	if (move_uploaded_file($source_excel, $targetzip)) 
	{
		$excel = new Spreadsheet_Excel_Reader($targetzip); 
		$card_colum=0; $m=1; 
		$all_data_arr=array(); 

		for ($i = 1; $i <= $excel->sheets[0]['numRows']; $i++) 
		{
			if($m==1)
			{
				for ($j = 1; $j <= $excel->sheets[0]['numCols']; $j++) 
				{
				}
				$m++;
			}
			else
			{
				$company=$item_ategory_val=$item_group=$sub_group_code=$sub_group_name=$item_code=$item_description=$item_size='';
				$re_order_level=$min_level=$max_level=$order_uom=$cons_uom=$conversion_factor=$item_account=$brand='';
				$origin=$fixed_asset=$item_number=$model=$stock_qty=$rate=$supplier=$currency_val=$store_name=$pay_mode_val=$source_val='';
				$lot=$manuf_date=$exp_date='';
				//echo '<pre>';print_r($excel->sheets[0]['cells'][$i]);die;
				
				$str_rep=array("*",  "=", "\r", "\n", "#");
				//$str_rep= preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $excel->sheets[0]['cells'][$i]); 
				if (isset($excel->sheets[0]['cells'][$i][1])) $company = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][1]);
				if (isset($excel->sheets[0]['cells'][$i][2])) $item_ategory_val = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][2]);
				if (isset($excel->sheets[0]['cells'][$i][3])) $item_group = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][3]);
				if (isset($excel->sheets[0]['cells'][$i][4])) $sub_group_code = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][4]);
				if (isset($excel->sheets[0]['cells'][$i][5])) $sub_group_name = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][5]);
				if (isset($excel->sheets[0]['cells'][$i][6])) $item_code = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][6]);
				if (isset($excel->sheets[0]['cells'][$i][7])) $item_description  =  preg_replace('/\s\s+/', ' ', str_replace("\n", " ", $excel->sheets[0]['cells'][$i][7]));//trim(preg_replace('/\s/u', ' ', $strtyu));
				if (isset($excel->sheets[0]['cells'][$i][8])) $item_size = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][8]);
				if (isset($excel->sheets[0]['cells'][$i][9])) $re_order_level= $excel->sheets[0]['cells'][$i][9];
				if (isset($excel->sheets[0]['cells'][$i][10])) $min_level = $excel->sheets[0]['cells'][$i][10];
				if (isset($excel->sheets[0]['cells'][$i][11])) $max_level = $excel->sheets[0]['cells'][$i][11];
				if (isset($excel->sheets[0]['cells'][$i][12])) $order_uom = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][12]);
				if (isset($excel->sheets[0]['cells'][$i][13])) $cons_uom = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][13]);
				if (isset($excel->sheets[0]['cells'][$i][14])) $conversion_factor = trim($excel->sheets[0]['cells'][$i][14]);
				if (isset($excel->sheets[0]['cells'][$i][15])) $item_account = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][15]);
				if (isset($excel->sheets[0]['cells'][$i][16])) $brand = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][16]);
				if (isset($excel->sheets[0]['cells'][$i][17])) $origin = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][17]);
				if (isset($excel->sheets[0]['cells'][$i][18])) $fixed_asset = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][18]);
				if (isset($excel->sheets[0]['cells'][$i][19])) $item_number = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][19]);
				if (isset($excel->sheets[0]['cells'][$i][20])) $model = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][20]);
				if (isset($excel->sheets[0]['cells'][$i][21])) $stock_qty = $excel->sheets[0]['cells'][$i][21];
				if (isset($excel->sheets[0]['cells'][$i][22])) $rate = $excel->sheets[0]['cells'][$i][22];
				if (isset($excel->sheets[0]['cells'][$i][23])) $supplier = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][23]);
				if (isset($excel->sheets[0]['cells'][$i][24])) $currency_val = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][24]);
				if (isset($excel->sheets[0]['cells'][$i][25])) $store_name = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][25]);
				if (isset($excel->sheets[0]['cells'][$i][26])) $pay_mode_val = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][26]);
				if (isset($excel->sheets[0]['cells'][$i][27])) $source_val = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][27]);
				if (isset($excel->sheets[0]['cells'][$i][28])) $lot = trim(str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][28]));
				if (isset($excel->sheets[0]['cells'][$i][29])) $manuf_date = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][29]);
				if (isset($excel->sheets[0]['cells'][$i][30])) $exp_date = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][30]);

				$all_data_arr[$i][1]['company']=trim($company);
				$all_data_arr[$i][2]['item_ategory_val']=trim($item_ategory_val);
				$all_data_arr[$i][3]['item_group']=trim($item_group);
				$all_data_arr[$i][4]['sub_group_code']=trim($sub_group_code);
				$all_data_arr[$i][5]['sub_group_name']=trim($sub_group_name);
				$all_data_arr[$i][6]['item_code']=trim($item_code);
				$all_data_arr[$i][7]['item_description']=trim($item_description);
				$all_data_arr[$i][8]['item_size']=trim($item_size);
				$all_data_arr[$i][9]['re_order_level']=trim($re_order_level);
				$all_data_arr[$i][10]['min_level']=trim($min_level);
				$all_data_arr[$i][11]['max_level']=trim($max_level);
				$all_data_arr[$i][12]['order_uom']=trim($order_uom);
				$all_data_arr[$i][13]['cons_uom']=trim($cons_uom);
				$all_data_arr[$i][14]['conversion_factor']=trim($conversion_factor);
				$all_data_arr[$i][15]['item_account']=trim($item_account);
				$all_data_arr[$i][16]['brand']=trim($brand);
				$all_data_arr[$i][17]['origin']=trim($origin);
				$all_data_arr[$i][18]['fixed_asset']=trim($fixed_asset);
				$all_data_arr[$i][19]['item_number']=trim($item_number);
				$all_data_arr[$i][20]['model']=trim($model);
				$all_data_arr[$i][21]['stock_qty']=trim(str_replace(",","",$stock_qty));
				$all_data_arr[$i][22]['rate']=trim(str_replace(",","",$rate));
				$all_data_arr[$i][23]['supplier']=trim($supplier);
				$all_data_arr[$i][24]['currency_val']=trim($currency_val);
				$all_data_arr[$i][25]['store_name']=trim($store_name);
				$all_data_arr[$i][26]['pay_mode_val']=trim($pay_mode_val);
				$all_data_arr[$i][27]['source_val']=trim($source_val);
				$all_data_arr[$i][28]['lot']=trim($lot);
				if (trim($manuf_date) != "") $all_data_arr[$i][29]['manuf_date']=date("d-M-Y", strtotime(str_replace("'", "", trim($manuf_date))));
				else $all_data_arr[$i][29]['manuf_date']="";
				if (trim($exp_date) != "") $all_data_arr[$i][30]['exp_date']=date("d-M-Y", strtotime(str_replace("'", "", trim($exp_date))));
				else $all_data_arr[$i][30]['exp_date']="";
			}
		}
		//echo '<pre>';print_r($all_data_arr);die;

		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$company_library=return_library_array("select company_name, id from lib_company where status_active=1 and is_deleted=0","company_name","id");
		$item_category_library=return_library_array("select short_name, category_id from lib_item_category_list where status_active=1 and is_deleted=0", "short_name", "category_id");
		$supplier_library=return_library_array("select supplier_name, id from lib_supplier where status_active=1 and is_deleted=0", "supplier_name", "id");
		$country_library=return_library_array("select country_name, id from lib_country where status_active=1 and is_deleted=0","country_name","id");

		// variable check Dyes Chemical Lot Maintain
		$sql_variable=sql_select("select a.auto_transfer_rcv as AUTO_TRANSFER_RCV, a.company_name as COMPANY_NAME from variable_settings_inventory a where variable_list=29 and status_active=1 and is_deleted=0");
		$variable_lot_arr=array();
		foreach ($sql_variable as $val) {
			$variable_lot_arr[$val['COMPANY_NAME']] = $val['AUTO_TRANSFER_RCV'];
		}
		unset($sql_variable);
		//echo '<pre>';print_r($variable_lot_arr);die;

		$sql_store=sql_select("select a.id as ID, a.store_name as STORE_NAME, a.company_id as COMPANY_ID from lib_store_location a where a.status_active=1 and a.is_deleted=0");
		$store_arr=array();
		foreach ($sql_store as $val) {
			$store_arr[$val['COMPANY_ID']][$val['STORE_NAME']] = $val['ID'];
		}
		unset($sql_store);

		$sql_item_group=sql_select("select id as ID, item_name as ITEM_NAME, item_category as ITEM_CATEGORY from lib_item_group where status_active=1 and is_deleted=0");
		$item_group_arr=array();
		foreach ($sql_item_group as $val) {
			$item_group_arr[$val['ITEM_CATEGORY']][$val['ITEM_NAME']]=$val['ID'];
		}
		unset($sql_item_group);

		$sql_sub_group=sql_select("select id as ID, item_category_id as ITEM_CATEGORY_ID, item_group_id as ITEM_GROUP_ID, sub_group_code as SUB_GROUP_CODE, sub_group_name as SUB_GROUP_NAME from lib_item_sub_group where status_active=1 and is_deleted=0");
		$sub_group_arr=array();
		foreach ($sql_sub_group as $val) {
			$sub_group_arr[$val['ITEM_CATEGORY_ID']][$val['ITEM_GROUP_ID']][$val['SUB_GROUP_NAME']]['item_sub_group_id'] = $val['ID'];
			$sub_group_arr[$val['ITEM_CATEGORY_ID']][$val['ITEM_GROUP_ID']][$val['SUB_GROUP_NAME']]['sub_group_code'] = $val['SUB_GROUP_CODE'];
			$sub_group_arr[$val['ITEM_CATEGORY_ID']][$val['ITEM_GROUP_ID']][$val['SUB_GROUP_NAME']]['sub_group_name'] = $val['SUB_GROUP_NAME'];
		}
		unset($sql_sub_group);

		$unit_of_measurement_arr = array_flip($unit_of_measurement);
		$currency_arr = array_flip($currency);
		$pay_mode_arr = array_flip($pay_mode);
		$source_arr = array_flip($source);
		$fixed_asset_arr = array_flip($yes_no);
		
	
		$field_array_prod="id,entry_form,company_id,item_category_id,item_group_id,item_sub_group_id,sub_group_code,sub_group_name,item_code,item_description,product_name_details,item_size,re_order_label,minimum_label,maximum_label,unit_of_measure,order_uom,conversion_factor,item_account,item_number,brand_name,model,origin,fixed_asset,current_stock,avg_rate_per_unit,stock_value,supplier_id,store_id,status_active,is_deleted,inserted_by,insert_date,is_excel";
		//$field_array_prod="id,status_active,is_deleted,inserted_by,insert_date,is_excel";

		$field_array_receive="id,recv_number_prefix,recv_number_prefix_num,recv_number,entry_form,company_id,receive_basis,receive_date,store_id,exchange_rate,currency_id,supplier_id,pay_mode,source,inserted_by,insert_date";

		$field_array_trans= "id,mst_id,receive_basis,company_id,supplier_id,prod_id,item_category,transaction_type,transaction_date,store_id,order_uom,order_qnty,order_rate,order_ile,order_ile_cost,order_amount,cons_uom,cons_quantity,cons_rate,cons_ile,cons_ile_cost,cons_amount,balance_qnty,balance_amount,floor_id,room,rack,self,bin_box,manufacture_date,expire_date,inserted_by,insert_date,is_excel";

		$field_array_receive_with_lot="id,recv_number_prefix,recv_number_prefix_num,recv_number,entry_form,item_category,company_id,receive_basis,receive_date,store_id,supplier_id,currency_id,exchange_rate,source,pay_mode,inserted_by,insert_date";

		$field_array_prod_with_lot="id,entry_form,company_id,item_category_id,item_group_id,item_sub_group_id,sub_group_code,sub_group_name,item_code,item_description,product_name_details,item_size,re_order_label,minimum_label,maximum_label,unit_of_measure,order_uom,conversion_factor,item_account,item_number,brand_name,model,origin,fixed_asset,current_stock,avg_rate_per_unit,stock_value,supplier_id,store_id,lot,status_active,is_deleted,inserted_by,insert_date,is_excel";				

		$field_array_trans_with_lot= "id,mst_id,receive_basis,company_id,supplier_id,prod_id,item_category,transaction_type,transaction_date,store_id,order_uom,order_qnty,order_rate,order_ile,order_ile_cost,order_amount,cons_uom,cons_quantity,cons_rate,cons_ile,cons_ile_cost,cons_amount,balance_qnty,balance_amount,floor_id,room,rack,self,bin_box,batch_lot,manufacture_date,expire_date,inserted_by,insert_date,is_excel";

		$field_array_store_with_lot="id,company_id,store_id,category_id,prod_id,cons_qty,rate,amount,last_purchased_qnty,inserted_by,insert_date,lot,first_receive_date,last_receive_date";

		$field_array_prod_update="current_stock*avg_rate_per_unit*stock_value*supplier_id*is_excel*store_id*updated_by*update_date";

		//echo 'system';
		//echo '<pre>';print_r($item_category_library);
		$sql_prod_res=sql_select("select id as ID, company_id as COMPANY_ID, item_category_id as ITEM_CATEGORY_ID, item_group_id as ITEM_GROUP_ID, sub_group_name as SUB_GROUP_NAME, item_description as ITEM_DESCRIPTION, item_size as ITEM_SIZE, model as MODEL, item_number as ITEM_NUMBER, item_code as ITEM_CODE, current_stock as CURRENT_STOCK, lot as LOT, avg_rate_per_unit as AVG_RATE_PER_UNIT, stock_value as STOCK_VALUE, conversion_factor as CONVERSION_FACTOR from product_details_master where status_active=1 and is_deleted=0 and entry_form<>24");
		$product_data_arr=$duplicate_product_data_arr=$product_id_data_arr=array();
		$dyesproduct_data_arr=$dyesduplicate_product_data_arr=$dyseproduct_id_data_arr=array();

		foreach ($sql_prod_res as $val)
		{
			if ($val['ITEM_CATEGORY_ID']==5 || $val['ITEM_CATEGORY_ID']==6 || $val['ITEM_CATEGORY_ID']==7 || $val['ITEM_CATEGORY_ID']==23)
			{
				$dyeskey_withlot=trim($val['COMPANY_ID']).'**'.trim($val['ITEM_CATEGORY_ID']).'**'.trim($val['ITEM_GROUP_ID']).'**'.trim($val['SUB_GROUP_NAME']).'**'.trim($val['ITEM_DESCRIPTION']).'**'.trim($val['ITEM_SIZE']).'**'.trim($val['MODEL']).'**'.trim($val['ITEM_NUMBER']).'**'.trim($val['ITEM_CODE']).'**'.trim($val['LOT']);
				$dyeskey_withoutlot=trim($val['COMPANY_ID']).'**'.trim($val['ITEM_CATEGORY_ID']).'**'.trim($val['ITEM_GROUP_ID']).'**'.trim($val['SUB_GROUP_NAME']).'**'.trim($val['ITEM_DESCRIPTION']).'**'.trim($val['ITEM_SIZE']).'**'.trim($val['MODEL']).'**'.trim($val['ITEM_NUMBER']).'**'.trim($val['ITEM_CODE']);
				$dyesproduct_data_arr_withlot[$dyeskey_withlot] = $val['ID'];
				$dyesproduct_data_arr_withoutlot[$dyeskey_withoutlot] = $val['ID'];
				$dyesduplicate_product_data_arr[$dyeskey_withlot] = $val['ID'];
				$dyseproduct_id_data_arr[$val['ID']]['CURRENT_STOCK']=$val['CURRENT_STOCK'];
				$dyseproduct_id_data_arr[$val['ID']]['AVG_RATE_PER_UNIT']=$val['AVG_RATE_PER_UNIT'];
				$dyseproduct_id_data_arr[$val['ID']]['STOCK_VALUE']=$val['STOCK_VALUE'];
				$dyseproduct_id_data_arr[$val['ID']]['LOT']=$val['LOT'];
			}
			else
			{
				$key=trim($val['COMPANY_ID']).'**'.trim($val['ITEM_CATEGORY_ID']).'**'.trim($val['ITEM_GROUP_ID']).'**'.trim($val['SUB_GROUP_NAME']).'**'.trim($val['ITEM_DESCRIPTION']).'**'.trim($val['ITEM_SIZE']).'**'.trim($val['MODEL']).'**'.trim($val['ITEM_NUMBER']).'**'.trim($val['ITEM_CODE']);
				$product_data_arr[$key] = $val['ID'];
				$duplicate_product_data_arr[$key] = $val['ID'];
				$product_id_data_arr[$val['ID']]['CURRENT_STOCK']=$val['CURRENT_STOCK'];
				$product_id_data_arr[$val['ID']]['AVG_RATE_PER_UNIT']=$val['AVG_RATE_PER_UNIT'];
				$product_id_data_arr[$val['ID']]['STOCK_VALUE']=$val['STOCK_VALUE'];
				$product_id_data_arr[$val['ID']]['CONVERSION_FACTOR']=$val['CONVERSION_FACTOR'];
			}			
			//$product_company_arr[$val['ID']]=$val['COMPANY_ID'];
		}
		unset($sql_prod_res);
		//echo '<pre>';print_r($product_data_arr);die;
		$data_array_receive_with_stock=$data_array_receive_with_stock_existingProd='';
		$data_array_trans_with_stock=$data_array_trans_with_stock_existingProd='';
		$data_array_prod_with_stock=$data_array_prod_without_stock='';
		$data_array_prod_chemicaldyes_with_lot='';
		$data_array_trans_chemicaldyes_with_lot='';
		$data_array_receive_chemicaldyes_with_lot='';
		$data_array_store_with_lot='';
		
		$duplicate_product_arr=array();
		$duplicate_dyesproduct_arr=array();
		$row_num_excel=1;
		$flag=1;
		$is_excel_insert=1;
		$is_excel_update=2;

		// Validation Part
		foreach($all_data_arr as $column_val)
		{
			$row_num_excel++;
			$company_id=$company_library[$column_val[1]['company']];			
			//$item_category_val = $item_category_arr[$column_val[1]['item_category_val']];
			if ($column_val[1]['company']=="" || $company_id==""){
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Company Name ['.$column_val[1]["company"].'] and Excel row number ['.$row_num_excel.']</p>';
				oci_rollback($con); disconnect($con); die;
			}

			$item_ategory_id=$item_category_library[$column_val[2]['item_ategory_val']];			
			if ($column_val[2]['item_ategory_val']=="" || $item_ategory_id==""){
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Item Category ['.$column_val[2]["item_ategory_val"].'] and Excel row number ['.$row_num_excel.']</p>';
				oci_rollback($con); disconnect($con); die;
			}

			$item_group=$column_val[3]['item_group'];
			$item_group_id=$item_group_arr[$item_ategory_id][$column_val[3]['item_group']];
			if ($column_val[3]['item_group']=="" || $item_group_id==""){
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Item Group ['.$column_val[3]["item_group"].'] and Excel row number ['.$row_num_excel.']</p>';
				oci_rollback($con); disconnect($con); die;
			}

			$item_code=$column_val[6]['item_code'];
			$check_item_code=check_special_character($item_code);
			if ($check_item_code==1) {
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Check Special Characters ['.$item_code.'] and Excel row number ['.$row_num_excel.']</p>';
				oci_rollback($con); disconnect($con); die;
			}

			$item_description=$column_val[7]['item_description'];
			$check_item_description=check_special_character($item_description);
			//echo $check_item_description;die;
			if ($check_item_description==1 || $item_description=="") {
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Check Item Description and Special Characters ['.$item_description.'] and Excel row number ['.$row_num_excel.']</p>';
				oci_rollback($con); disconnect($con); die;
			}

			$item_size=$column_val[8]['item_size'];
			$check_item_size=check_special_character($item_size);
			if ($check_item_size==1) {
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Check Special Characters ['.$item_size.'] and Excel row number ['.$row_num_excel.']</p>';
				oci_rollback($con); disconnect($con); die;
			}

			$order_uom_id=$unit_of_measurement_arr[$column_val[12]['order_uom']];
			if ($column_val[12]['order_uom']=="" || $order_uom_id==""){
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Order UOM ['.$column_val[12]["order_uom"].'] and Excel row number ['.$row_num_excel.']</p>';
				oci_rollback($con); disconnect($con); die;
			}

			$cons_uom_id=$unit_of_measurement_arr[$column_val[13]['cons_uom']];
			if ($column_val[13]['cons_uom']=="" || $cons_uom_id==""){
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Cons UOM ['.$column_val[13]["cons_uom"].'] and Excel row number ['.$row_num_excel.']</p>';
				oci_rollback($con); disconnect($con); die;
			}

			$conversion_factor=$column_val[14]['conversion_factor'];
			if ($conversion_factor==""){
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Conversion Factor ['.$conversion_factor.'] and Excel row number ['.$row_num_excel.']</p>';
				oci_rollback($con); disconnect($con); die;
			}

			$item_account=$column_val[15]['item_account'];
			$check_item_account=check_special_character($item_account);
			if ($check_item_account==1) {
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Check Special Characters ['.$item_account.'] and Excel row number ['.$row_num_excel.']</p>';
				oci_rollback($con); disconnect($con); die;
			}

			$brand=$column_val[16]['brand'];
			$check_brand=check_special_character($brand);
			if ($check_brand==1) {
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Check Special Characters ['.$brand.'] and Excel row number ['.$row_num_excel.']</p>';
				oci_rollback($con); disconnect($con); die;
			}

			$origin_id=$country_library[$column_val[17]['origin']];
			if ($column_val[17]['origin'] !=""){
				if ($origin_id==""){
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Origin ['.$column_val[17]["origin"].'] and Excel row number ['.$row_num_excel.']</p>';
					oci_rollback($con); disconnect($con); die;
				}				
			}

			$fixed_asset_id=$fixed_asset_arr[$column_val[18]['fixed_asset']];
			if ($column_val[18]['fixed_asset'] !=""){
				if ($fixed_asset_id==""){
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Fixed Asset ['.$column_val[18]["fixed_asset"].'] and Excel row number ['.$row_num_excel.']</p>';
					oci_rollback($con); disconnect($con); die;
				}				
			}

			$item_number=$column_val[19]['item_number'];
			$check_item_number=check_special_character($item_number);
			if ($check_item_number==1) {
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Check Special Characters ['.$item_number.'] and Excel row number ['.$row_num_excel.']</p>';
				oci_rollback($con); disconnect($con); die;
			}

			$stock_qty=$column_val[21]['stock_qty'];
			$rate=$column_val[22]['rate'];			
			
			$lot=strtoupper($column_val[28]['lot']);
			$check_lot=check_special_character($lot);
			if ($check_lot==1) {
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Check Special Characters ['.$lot.'] and Excel row number ['.$row_num_excel.']</p>';
				oci_rollback($con); disconnect($con); die;
			}

			if ($item_ategory_id==5 || $item_ategory_id==6 || $item_ategory_id==7 || $item_ategory_id==23) //Dyes Chemical Category
			{
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Dyes Chemical Category not ready ['.$row_num_excel.']</p>';oci_rollback($con); disconnect($con); die;
			}
			else
			{
				if ($stock_qty != '' && $stock_qty != '0' )  //With Stock
				{
					if ($rate ==""){
						echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Rate ['.$rate.'] and Excel row number ['.$row_num_excel.']</p>';
						oci_rollback($con); disconnect($con); die;
					}
		
					$supplier_id=$supplier_library[$column_val[23]['supplier']];
					if ($column_val[23]['supplier'] !=""){
						if ($supplier_id==""){
							echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Supplier ['.$column_val[23]["supplier"].'] and Excel row number ['.$row_num_excel.']</p>';
							oci_rollback($con); disconnect($con); die;
						}				
					}
		
					$currency_id=$currency_arr[$column_val[24]['currency_val']];
					if ($column_val[24]['currency_val'] !=""){
						if ($currency_id==""){
							echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct currency ['.$column_val[24]["currency_val"].'] and Excel row number ['.$row_num_excel.']</p>';
							oci_rollback($con); disconnect($con); die;
						}				
					}
		
					$store_name_id=$store_arr[$company_id][$column_val[25]['store_name']];
					if ($column_val[25]['store_name'] !=""){
						if ($store_name_id==""){
							echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Store Name ['.$column_val[25]["store_name"].'] and Excel row number ['.$row_num_excel.']</p>';
							oci_rollback($con); disconnect($con); die;
						}				
					}
		
					$pay_mode_id=$pay_mode_arr[$column_val[26]['pay_mode_val']];
					if ($column_val[26]['pay_mode_val'] !=""){
						if ($pay_mode_id==""){
							echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Pay Mode ['.$column_val[26]["pay_mode_val"].'] and Excel row number ['.$row_num_excel.']</p>';
							oci_rollback($con); disconnect($con); die;
						}				
					}
		
					$source_id=$source_arr[$column_val[27]['source_val']];
					if ($column_val[27]['source_val'] !=""){
						if ($source_id==""){
							echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Source ['.$column_val[27]["source_val"].'] and Excel row number ['.$row_num_excel.']</p>';
							oci_rollback($con); disconnect($con); die;
						}				
					}					
				}
			}			
		}

		$row_num_excel=1;
		foreach($all_data_arr as $column_val)
		{
			$row_num_excel++;
			$company_id=$company_library[$column_val[1]['company']];
			$item_ategory_id=$item_category_library[$column_val[2]['item_ategory_val']];
			$item_group=$column_val[3]['item_group'];
			$item_group_id=$item_group_arr[$item_ategory_id][$column_val[3]['item_group']];
			$sub_group_code=$column_val[4]['sub_group_code'];
			$sub_group_name=$column_val[5]['sub_group_name'];
			$item_sub_group_id=$sub_group_arr[$item_ategory_id][$item_group_id][$sub_group_name]['item_sub_group_id'];
			$sub_group_code_val=$sub_group_arr[$item_ategory_id][$item_group_id][$sub_group_name]['sub_group_code'];			
			$sub_group_name_val=$sub_group_arr[$item_ategory_id][$item_group_id][$sub_group_name]['sub_group_name'];
			$item_code=$column_val[6]['item_code'];
			$item_description=$column_val[7]['item_description'];			
			$item_size=$column_val[8]['item_size'];
			$re_order_level=$column_val[9]['re_order_level'];	
			$min_level=$column_val[10]['min_level'];	
			$max_level=$column_val[11]['max_level'];
			$order_uom_id=$unit_of_measurement_arr[$column_val[12]['order_uom']];
			$cons_uom_id=$unit_of_measurement_arr[$column_val[13]['cons_uom']];
			$conversion_factor=$column_val[14]['conversion_factor'];
			$item_account=$column_val[15]['item_account'];
			$brand=$column_val[16]['brand'];
			$origin_id=$country_library[$column_val[17]['origin']];
			$fixed_asset_id=$fixed_asset_arr[$column_val[18]['fixed_asset']];
			$item_number=$column_val[19]['item_number'];
			$model=$column_val[20]['model'];
			$stock_qty=$column_val[21]['stock_qty'];
			$rate=$column_val[22]['rate'];
			$supplier_id=$supplier_library[$column_val[23]['supplier']];
			$currency_id=$currency_arr[$column_val[24]['currency_val']];
			$store_name_id=$store_arr[$company_id][$column_val[25]['store_name']];
			$pay_mode_id=$pay_mode_arr[$column_val[26]['pay_mode_val']];
			$source_id=$source_arr[$column_val[27]['source_val']];			
			$lot=strtoupper($column_val[28]['lot']);
			$manuf_date=$column_val[29]['manuf_date'];
			$exp_date=$column_val[30]['exp_date'];
			
			//echo $stock_qty.'system';			
			if ($stock_qty != '' && $stock_qty != '0')  //With Stock
			{
				if ($item_ategory_id==5 || $item_ategory_id==6 || $item_ategory_id==7 || $item_ategory_id==23) //Dyes Chemical Category
				{
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Dyes Chemical Category not ready ['.$row_num_excel.']</p>';oci_rollback($con); disconnect($con); die;
					$variable_lot=$variable_lot_arr[$company_id];
					if ($variable_lot==1) //With Lot
					{
						if ($rate=='' || $supplier_id=='' || $currency_id=='' || $store_name_id=='' || $pay_mode_id=='' || $source_id=='' || $lot==''){
							echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill Up Rate, Supplier, Currency, Store Name, Pay Mode, Source, Lot and Excel row number ['.$row_num_excel.']</p>';
							oci_rollback($con); disconnect($con); die;
						}

						$dyesproduct_data=trim($company_id).'**'.trim($item_ategory_id).'**'.trim($item_group_id).'**'.trim($item_sub_group_id).'**'.trim($item_description).'**'.trim($item_size).'**'.trim($model).'**'.trim($item_number).'**'.trim($item_code).'**'.trim($lot);
						$dyesproduct_id="";
						if (array_key_exists($dyesproduct_data,$dyesproduct_data_arr_withlot)){
							$dyesproduct_id= $dyesproduct_data_arr_withlot[$dyesproduct_data];
						}

						if ($dyesproduct_id != "")
						{
							//echo $dyesproduct_id.'**';
							if ($db_type==0) $receive_date = date("Y-m-d", strtotime(str_replace("'", "",  date("Y-m-d"))));
							else $receive_date = date("d-M-Y", strtotime(str_replace("'", "",  date("Y-m-d"))));
							
							if ($db_type==0) $conversion_date=change_date_format($receive_date, "Y-m-d", "-",1);
							else $conversion_date=change_date_format($receive_date, "d-M-y", "-",1);
							$exchange_rate=set_conversion_rate( $currency_id, $conversion_date );

							$txt_receive_qty=$stock_qty;
							$txt_rate=$rate;
							$txt_amount=$txt_receive_qty*$txt_rate;
							$ile=$ile_cost=0;
							//echo $txt_rate.'**'.$ile_cost.'**'.$exchange_rate.'**'.$conversion_factor;					
							$domestic_rate = return_domestic_rate($txt_rate,$ile_cost,$exchange_rate,$conversion_factor);
							//echo $domestic_rate.'system';
							$cons_rate = number_format($domestic_rate,$dec_place[3],".","");
							$con_quantity = $conversion_factor*$txt_receive_qty;
							$con_amount = $cons_rate*$con_quantity;
							
							$presentStock=$presentStockValue=$presentAvgRate=0;
							$prev_lot='';
							$presentStock		= $dyseproduct_id_data_arr[$dyesproduct_id]['CURRENT_STOCK'];
							$presentStockValue	= $dyseproduct_id_data_arr[$dyesproduct_id]['STOCK_VALUE'];
							$presentAvgRate		= $dyseproduct_id_data_arr[$dyesproduct_id]['AVG_RATE_PER_UNIT'];
							$prev_lot		    = $dyseproduct_id_data_arr[$dyesproduct_id]['LOT'];

							$stock_value 	= $domestic_rate*$stock_qty;
							$currentStock   = $presentStock+$stock_qty;
							//$available_qnty = $available_qnty+$con_quantity;
							$StockValue=0;		
							$avgRate=$presentAvgRate;
							if ($currentStock != 0) {
								$StockValue	 = number_format($presentStockValue+$stock_value,$dec_place[4],".","");
								$avgRate	 = number_format($StockValue/$currentStock,$dec_place[4],".","");
							}

							$updateID_array[] = $dyesproduct_id;
							$update_product_data[$dyesproduct_id]=explode("*",("".$currentStock."*".$avgRate."*".$StockValue."*".$supplier_id."*".$store_name_id."*".$user_id."*'".$pc_date_time."'"));

							$id = return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con);
							$dtlsid = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
							$new_chemical_dyes_recv_number = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master",$con,1,str_replace("'","",$company_id),'CDR',4,date("Y",time())));

							$receive_basis=4;					
							
							if ($data_array_receive_chemical_dyes_with_lot != '') $data_array_receive_chemical_dyes_with_lot .=",";				
							$data_array_receive_chemical_dyes_with_lot .= "(".$id.",'".$new_chemical_dyes_recv_number[1]."',".$new_chemical_dyes_recv_number[2].",'".$new_chemical_dyes_recv_number[0]."',4,".$item_ategory_id.",".$company_id.",".$receive_basis."','".$receive_date."','".$store_name_id."','".$supplier_id."','".$currency_id."','".$exchange_rate."','".$source_id."','".$pay_mode_id."','".$user_id."','".$pc_date_time."')";

							$con_ile=$con_ile_cost=0;
							$cbo_floor=$cbo_room=$txt_rack=$txt_shelf=$cbo_bin=0;

							if ($data_array_trans_chemical_dyes_with_lot != '') $data_array_trans_chemical_dyes_with_lot .=",";
							$data_array_trans_chemical_dyes_with_lot .= "(".$dtlsid.",".$id.",".$receive_basis.",".$company_id.",".$supplier_id.",".$dyesproduct_id.",".$item_ategory_id.",1,'".$receive_date."','".$store_name_id."','".$order_uom_id."','".$txt_receive_qty."','".$txt_rate."','".$ile."','".$ile_cost."','".$txt_amount."','".$cons_uom_id."','".$con_quantity."','".$cons_rate."','".$con_ile."','".$con_ile_cost."','".$con_amount."','".$con_quantity."','".$con_amount."','".$cbo_floor."','".$cbo_room."','".$txt_rack."','".$txt_shelf."','".$cbo_bin."','".$lot."','".$manuf_date."','".$exp_date."','".$user_id."','".$pc_date_time."')";
						}
						else //new lot insert
						{
							$dyesprod_keys="";
							$dyesprod_keys=trim($company_id).'**'.trim($item_ategory_id).'**'.trim($item_group_id).'**'.trim($sub_group_name_val).'**'.trim($item_description).'**'.trim($item_size).'**'.trim($model).'**'.trim($item_number).'**'.trim($item_code).'**'.trim($lot)."**".trim($order_uom_id);

							// Duplicate Product Check
							if (array_key_exists($dyesprod_keys, $dyesduplicate_product_data_arr))
							{
								$duplicate_dyesprod_keys=trim($column_val[1]['company'])."**".trim($column_val[2]['item_ategory_val'])."**".trim($column_val[3]['item_group'])."**".trim($sub_group_name_val)."**".trim($item_description)."**".trim($item_size)."**".trim($model)."**".trim($item_number)."**".trim($item_code)."**".trim($lot)."**".trim($order_uom_id);
								$duplicate_dyesproduct_arr[]=$duplicate_dyesprod_keys;
								continue;
							}

							$productname = $item_group." ".$item_description." ".$item_size;
							$entry_form_lib=0;
							// data array Product Details Master Table
							$prod_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
							$dtlsid = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
							$stock_value=$stock_qty*$rate;
						
							if ($data_array_prod_chemicaldyes_with_lot != '') $data_array_prod_chemicaldyes_with_lot .=",";	
							$data_array_prod_chemicaldyes_with_lot .= "(".$prod_id.",".$entry_form_lib.",".$company_id.",".$item_ategory_id.",".$item_group_id.",'".$item_sub_group_id."','".$sub_group_code_val."','".$sub_group_name_val."','".$item_code."','".$item_description."','".$productname."','".$item_size."','".$re_order_level."','".$min_level."','".$max_level."','".$cons_uom_id."','".$order_uom_id."','".$conversion_factor."','".$item_account."','".$item_number."','".$brand."','".$model."','".$origin_id."','".$fixed_asset_id."','".$stock_qty."','".$rate."','".$stock_value."','".$supplier_id."','".$store_name_id."','".$lot."',1,0,'".$user_id."','".$pc_date_time."')";							
							

							// data array inv receive master Table
							$id = return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con);
							$new_chemical_dyes_recv_number = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master",$con,1,str_replace("'","",$company_id),'CDR',4,date("Y",time())));

							$receive_basis=4;
							if ($db_type==0) $receive_date = date("Y-m-d", strtotime(str_replace("'", "",  date("Y-m-d"))));
							else $receive_date = date("d-M-Y", strtotime(str_replace("'", "",  date("Y-m-d"))));

							if ($db_type==0) $conversion_date=change_date_format($receive_date, "Y-m-d", "-",1);
							else $conversion_date=change_date_format($receive_date, "d-M-y", "-",1);
							$exchange_rate=set_conversion_rate( $currency_id, $conversion_date );

							$booking_without_order=1;

							if ($data_array_receive_chemicaldyes_with_lot != '') $data_array_receive_chemicaldyes_with_lot .=",";			
							$data_array_receive_chemicaldyes_with_lot="(".$id.",'".$new_chemical_dyes_recv_number[1]."',".$new_chemical_dyes_recv_number[2].",'".$new_chemical_dyes_recv_number[0]."',4,".$item_ategory_id.",".$company_id.",".$receive_basis."','".$receive_date."','".$booking_without_order."','".$store_name_id."','".$supplier_id."','".$currency_id."','".$exchange_rate."','".$source_id."','".$pay_mode_id."','".$user_id."','".$pc_date_time."')";		
							
							// data array Transaction Table
							$txt_receive_qty=$stock_qty;
							$txt_rate=$rate;
							$txt_amount=$txt_receive_qty*$txt_rate;
							$ile=$ile_cost=0;
							//echo $txt_rate.'**'.$ile_cost.'**'.$exchange_rate.'**'.$conversion_factor;
							$domestic_rate = return_domestic_rate($txt_rate,$ile_cost,$exchange_rate,$conversion_factor);
							//echo $domestic_rate.'system';
							$cons_rate = number_format($domestic_rate,$dec_place[3],".","");
							$con_quantity = $conversion_factor*$txt_receive_qty;
							$con_amount = $cons_rate*$con_quantity;

							$con_ile=$con_ile_cost=0;
							$cbo_floor=$cbo_room=$txt_rack=$txt_shelf=$cbo_bin=0;

							if ($data_array_trans_chemicaldyes_with_lot != '') $data_array_trans_chemicaldyes_with_lot .=",";
							$data_array_trans_chemicaldyes_with_lot .= "(".$dtlsid.",".$id.",".$receive_basis.",".$company_id.",".$supplier_id.",".$prod_id.",".$item_ategory_id.",1,'".$receive_date."','".$store_name_id."','".$order_uom_id."','".$txt_receive_qty."','".$txt_rate."','".$ile."','".$ile_cost."','".$txt_amount."','".$cons_uom_id."','".$con_quantity."','".$cons_rate."','".$con_ile."','".$con_ile_cost."','".$con_amount."','".$con_quantity."','".$con_amount."','".$cbo_floor."','".$cbo_room."','".$txt_rack."','".$txt_shelf."','".$cbo_bin."','".$lot."','".$manuf_date."','".$exp_date."','".$user_id."','".$pc_date_time."')";

							//--------------Store Wise Stock------------------

							$sdtlsid = return_next_id_by_sequence("inv_store_wise_qty_dtls_pk_seq", "inv_store_wise_qty_dtls", $con);
							if ($data_array_store_with_lot != '') $data_array_store_with_lot .=",";
							$data_array_store_with_lot .= "(".$sdtlsid.",".$company_id.",".$store_name_id.",".$item_ategory_id.",".$prod_id.",".$txt_receive_qty.",".$cons_rate.",".$con_amount.",".$txt_receive_qty.",'".$_SESSION['logic_erp']['user_id']."','".$pc_date_time."','".$lot."','".$receive_date."','".$receive_date."')";							
						}						
					}
					else //Without Lot
					{
						if ($rate=='' || $supplier_id=='' || $currency_id=='' || $store_name_id=='' || $pay_mode_id=='' || $source_id==''){
							echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill Up Rate, Supplier, Currency, Store Name, Pay Mode, Source and Excel row number ['.$row_num_excel.']</p>';
							oci_rollback($con); disconnect($con); die;
						}
					}					
				}
				else
				{
					$product_data=trim($company_id).'**'.trim($item_ategory_id).'**'.trim($item_group_id).'**'.trim($item_sub_group_id).'**'.trim($item_description).'**'.trim($item_size).'**'.trim($model).'**'.trim($item_number).'**'.trim($item_code);
					$product_id="";
					if (array_key_exists($product_data,$product_data_arr)){
						$product_id= $product_data_arr[$product_data];
					}
					//echo $product_id.'system';die;					
					if ($product_id != "") //update item with stock
					{
						if ($db_type==0) $conversion_date=change_date_format($receive_date, "Y-m-d", "-",1);
						else $conversion_date=change_date_format($receive_date, "d-M-y", "-",1);
						$exchange_rate=set_conversion_rate( $currency_id, $conversion_date );

						$presentStock=$presentStockValue=$presentAvgRate=0;
						$presentStock		= $product_id_data_arr[$product_id]['CURRENT_STOCK'];
						$presentStockValue	= $product_id_data_arr[$product_id]['STOCK_VALUE'];
						$presentAvgRate		= $product_id_data_arr[$product_id]['AVG_RATE_PER_UNIT'];
						$conversion_factor	= $product_id_data_arr[$product_id]['CONVERSION_FACTOR'];

						$txt_receive_qty=$stock_qty;
						$txt_rate=$rate;
						$txt_amount=$txt_receive_qty*$txt_rate;
						$ile=$ile_cost=0;
						//echo $txt_rate.'**'.$ile_cost.'**'.$exchange_rate.'**'.$conversion_factor;					
						$domestic_rate = return_domestic_rate($txt_rate,$ile_cost,$exchange_rate,$conversion_factor);
						//echo $domestic_rate.'system';
						$cons_rate = number_format($domestic_rate,$dec_place[3],".","");
						$con_quantity = $conversion_factor*$txt_receive_qty;
						$con_amount = $cons_rate*$con_quantity;						

						$stock_value 	= $cons_rate*$con_quantity;
						$currentStock   = $presentStock+$con_quantity;
						  //$available_qnty = $available_qnty+$con_quantity;
						$StockValue=$avgRate=0;						
						if ($currentStock != 0) {
							$StockValue	 = number_format($presentStockValue+$stock_value,$dec_place[4],".","");
							$avgRate	 = number_format($StockValue/$currentStock,$dec_place[4],".","");
						}

						$prodUpdate=execute_query("update product_details_master set current_stock='".$currentStock."', avg_rate_per_unit='".$avgRate."', stock_value='".$StockValue."', supplier_id='".$supplier_id."', is_excel='".$is_excel_update."', store_id='".$store_name_id."', updated_by='".$user_id."', update_date='".$pc_date_time."' where id=".$product_id."");

						if ($prodUpdate) { $flag=1; } else {echo "update product_details_master set current_stock='".$currentStock."', avg_rate_per_unit='".$avgRate."', stock_value='".$StockValue."', supplier_id='".$supplier_id."', is_excel='".$is_excel_update."', store_id='".$store_name_id."', updated_by='".$user_id."', update_date='".$pc_date_time."' where id=".$product_id.""; oci_rollback($con); disconnect($con); die;}

						$id = return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con);
						$dtlsid = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
						$new_recv_number = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master",$con,1,str_replace("'","",$company_id),'GIR',20,date("Y",time())));

						$receive_basis=4;
						if ($db_type==0) $receive_date = date("Y-m-d", strtotime(str_replace("'", "",  date("Y-m-d"))));
						else $receive_date = date("d-M-Y", strtotime(str_replace("'", "",  date("Y-m-d"))));
										
						$data_array_receive_with_stock_existingProd ="INSERT INTO inv_receive_master (".$field_array_receive.") VALUES (".$id.",'".$new_recv_number[1]."','".$new_recv_number[2]."','".$new_recv_number[0]."',20,".$company_id.",".$receive_basis.",'".$receive_date."','".$store_name_id."','".$exchange_rate."','".$currency_id."','".$supplier_id."','".$pay_mode_id."','".$source_id."','".$user_id."','".$pc_date_time."')";
						$rID5=execute_query($data_array_receive_with_stock_existingProd);
						if ($rID5) { $flag=1; }
						else { echo $data_array_receive_with_stock_existingProd;oci_rollback($con); disconnect($con); die; }

						$con_ile=$con_ile_cost=0;
						$cbo_floor=$cbo_room=$txt_rack=$txt_shelf=$cbo_bin=0;

						$data_array_trans_with_stock_existingProd ="INSERT INTO inv_transaction (".$field_array_trans.") VALUES (".$dtlsid.",".$id.",".$receive_basis.",".$company_id.",".$supplier_id.",".$product_id.",".$item_ategory_id.",1,'".$receive_date."','".$store_name_id."','".$order_uom_id."','".$txt_receive_qty."','".$txt_rate."','".$ile."','".$ile_cost."','".$txt_amount."','".$cons_uom_id."','".$con_quantity."','".$cons_rate."','".$con_ile."','".$con_ile_cost."','".$con_amount."','".$con_quantity."','".$con_amount."','".$cbo_floor."','".$cbo_room."','".$txt_rack."','".$txt_shelf."','".$cbo_bin."','".$manuf_date."','".$exp_date."','".$user_id."','".$pc_date_time."',".$is_excel_insert.")";
						$rID6=execute_query($data_array_trans_with_stock_existingProd);
						if ($rID6) { $flag=1; }
						else { echo $data_array_trans_with_stock_existingProd; oci_rollback($con); disconnect($con); die; }
					}
					else //new item insert with stock
					{
						$prod_keys="";
						$prod_keys=trim($company_id).'**'.trim($item_ategory_id).'**'.trim($item_group_id).'**'.trim($sub_group_name_val).'**'.trim($item_description).'**'.trim($item_size).'**'.trim($model).'**'.trim($item_number).'**'.trim($item_code)."**".trim($order_uom_id);
						//echo $prod_keys.'system';
				
						// Duplicate Product Check
						if (array_key_exists($prod_keys, $duplicate_product_data_arr))
						{
							$duplicate_prod_keys=trim($column_val[1]['company'])."**".trim($column_val[2]['item_ategory_val'])."**".trim($column_val[3]['item_group'])."**".trim($sub_group_name_val)."**".trim($item_description)."**".trim($item_size)."**".trim($model)."**".trim($item_number)."**".trim($item_code)."**".trim($order_uom_id);
							$duplicate_product_arr[]=$duplicate_prod_keys;
							continue;
						}

						$productname = $item_group." ".$item_description." ".$item_size;
						if ($item_ategory_id==4) $entry_form_lib=20; else $entry_form_lib=0;

						$prod_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
						$dtlsid = return_next_id_by_sequence("INV_TRANSACTION_PK_SEQ", "inv_transaction", $con);
						$id = return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master", $con);
						$duplicate_product_data_arr[$prod_keys] = $prod_id; // Push item from excel data in array

						$new_recv_number = explode("*", return_next_id_by_sequence("INV_RECEIVE_MASTER_PK_SEQ", "inv_receive_master",$con,1,str_replace("'","",$company_id),'GIR',20,date("Y",time())));

						$stock_value=$stock_qty*$rate;								

						$receive_basis=4;
						if ($db_type==0) $receive_date = date("Y-m-d", strtotime(str_replace("'", "",  date("Y-m-d"))));
						else $receive_date = date("d-M-Y", strtotime(str_replace("'", "",  date("Y-m-d"))));

						if ($db_type==0) $conversion_date=change_date_format($receive_date, "Y-m-d", "-",1);
						else $conversion_date=change_date_format($receive_date, "d-M-y", "-",1);
						$exchange_rate=set_conversion_rate( $currency_id, $conversion_date );

						$data_array_receive_with_stock ="INSERT INTO inv_receive_master (".$field_array_receive.") VALUES (".$id.",'".$new_recv_number[1]."','".$new_recv_number[2]."','".$new_recv_number[0]."',20,".$company_id.",".$receive_basis.",'".$receive_date."','".$store_name_id."','".$exchange_rate."','".$currency_id."','".$supplier_id."','".$pay_mode_id."','".$source_id."','".$user_id."','".$pc_date_time."')";
						$rID3=execute_query($data_array_receive_with_stock);
						if ($rID3) { $flag=1; }
						else { echo $data_array_receive_with_stock; oci_rollback($con); disconnect($con); die; }

						$txt_receive_qty=$stock_qty;
						$txt_rate=$rate;
						$txt_amount=$txt_receive_qty*$txt_rate;
						$ile=$ile_cost=0;
						//echo $txt_rate.'**'.$ile_cost.'**'.$exchange_rate.'**'.$conversion_factor;
						$domestic_rate = return_domestic_rate($txt_rate,$ile_cost,$exchange_rate,$conversion_factor);
						//echo $domestic_rate.'system';
						$cons_rate = number_format($domestic_rate,$dec_place[3],".","");
						$con_quantity = $conversion_factor*$txt_receive_qty;
						$con_amount = $cons_rate*$con_quantity;

						$con_ile=$con_ile_cost=0;
						$cbo_floor=$cbo_room=$txt_rack=$txt_shelf=$cbo_bin=0;

						$data_array_trans_with_stock ="INSERT INTO inv_transaction (".$field_array_trans.") VALUES (".$dtlsid.",".$id.",".$receive_basis.",".$company_id.",".$supplier_id.",".$prod_id.",".$item_ategory_id.",1,'".$receive_date."','".$store_name_id."','".$order_uom_id."','".$txt_receive_qty."','".$txt_rate."','".$ile."','".$ile_cost."','".$txt_amount."','".$cons_uom_id."','".$con_quantity."','".$cons_rate."','".$con_ile."','".$con_ile_cost."','".$con_amount."','".$con_quantity."','".$con_amount."','".$cbo_floor."','".$cbo_room."','".$txt_rack."','".$txt_shelf."','".$cbo_bin."','".$manuf_date."','".$exp_date."','".$user_id."','".$pc_date_time."',".$is_excel_insert.")";
						$rID4=execute_query($data_array_trans_with_stock);
						if ($rID4) { $flag=1; }
						else { echo $data_array_trans_with_stock; oci_rollback($con); disconnect($con); die; }


						$data_array_prod_with_stock="INSERT INTO product_details_master (".$field_array_prod.") VALUES (".$prod_id.",".$entry_form_lib.",".$company_id.",".$item_ategory_id.",".$item_group_id.",'".$item_sub_group_id."','".$sub_group_code_val."','".$sub_group_name_val."','".$item_code."','".$item_description."','".$productname."','".$item_size."','".$re_order_level."','".$min_level."','".$max_level."','".$cons_uom_id."','".$order_uom_id."','".$conversion_factor."','".$item_account."','".$item_number."','".$brand."','".$model."','".$origin_id."','".$fixed_asset_id."','".$con_quantity."','".$cons_rate."','".$con_amount."','".$supplier_id."','".$store_name_id."',1,0,'".$user_id."','".$pc_date_time."',".$is_excel_insert.")";
						$rID2=execute_query($data_array_prod_with_stock);
						if ($rID2) { $flag=1; $flg.="1*$prod_id*";}
						else { echo $data_array_prod_with_stock; oci_rollback($con); disconnect($con); die; }
					}
				}
			}
			else  //new item insert without stock
			{
				$prod_keys=trim($company_id).'**'.trim($item_ategory_id).'**'.trim($item_group_id).'**'.trim($sub_group_name_val).'**'.trim($item_description).'**'.trim($item_size).'**'.trim($model).'**'.trim($item_number).'**'.trim($item_code)."**".trim($order_uom_id);
				
				// Duplicate Product Check
				if (array_key_exists($prod_keys, $duplicate_product_data_arr))
				{
					$duplicate_prod_keys=trim($column_val[1]['company'])."**".trim($column_val[2]['item_ategory_val'])."**".trim($column_val[3]['item_group'])."**".trim($sub_group_name_val)."**".trim($item_description)."**".trim($item_size)."**".trim($model)."**".trim($item_number)."**".trim($item_code)."**".trim($order_uom_id);
					$duplicate_product_arr[]=$duplicate_prod_keys;
					continue;
				}

				$productname = $item_group." ".$item_description." ".$item_size;
				if ($item_ategory_id==4) $entry_form_lib=20; else $entry_form_lib=0;

				$stock_qty=$rate=$stock_value=0;
				$supplier_id=$store_name_id=0;

				$prod_id = return_next_id_by_sequence("PRODUCT_DETAILS_MASTER_PK_SEQ", "product_details_master", $con);
				$duplicate_product_data_arr[$prod_keys] = $prod_id; // Push item from excel data in array
				
				//if ($data_array_prod_without_stock != '') $data_array_prod_without_stock .=",";
				//$data_array_prod_without_stock="(".$prod_id.",".$entry_form_lib.",".$company_id.",".$item_ategory_id.",".$item_group_id.",'".$item_sub_group_id."','".$sub_group_code_val."','".$sub_group_name_val."','".$item_code."','".$item_description."','".$productname."','".$item_size."','".$re_order_level."','".$min_level."','".$max_level."','".$cons_uom_id."','".$order_uom_id."','".$conversion_factor."','".$item_account."','".$item_number."','".$brand."','".$model."','".$origin_id."','".$fixed_asset_id."','".$stock_qty."','".$rate."','".$stock_value."','".$supplier_id."','".$store_name_id."',1,0,'".$user_id."','".$pc_date_time."',1)";
				$data_array_prod_without_stock="INSERT INTO product_details_master (".$field_array_prod.") VALUES (".$prod_id.",".$entry_form_lib.",".$company_id.",".$item_ategory_id.",".$item_group_id.",'".$item_sub_group_id."','".$sub_group_code_val."','".$sub_group_name_val."','".$item_code."','".$item_description."','".$productname."','".$item_size."','".$re_order_level."','".$min_level."','".$max_level."','".$cons_uom_id."','".$order_uom_id."','".$conversion_factor."','".$item_account."','".$item_number."','".$brand."','".$model."','".$origin_id."','".$fixed_asset_id."','".$stock_qty."','".$rate."','".$stock_value."','".$supplier_id."','".$store_name_id."',1,0,'".$user_id."','".$pc_date_time."',".$is_excel_insert.")";
				$rID=execute_query($data_array_prod_without_stock);
				if ($rID) { $flag=1; }
				else { echo $data_array_prod_without_stock; oci_rollback($con); disconnect($con); die; }
												
			}	

		}
		//echo "10** insert into product_details_master ($field_array_prod) values $data_array_prod_without_stock";die;
		//echo '<pre>';print_r($duplicate_product_arr);die;		
		//echo "10**$count";oci_rollback($con);disconnect($con);die;
		$all_datas='';
		if (!empty($duplicate_product_arr))
		{
			$all_datas.='<div style="width:100%">';
			$all_datas.='<table border="0" class="rpt_table" rules="all" width="1110" cellpadding="0" cellspacing="0" style="font-size: 16px;">
					<tr>
						<td colspan="10"><strong>Duplicate Item List:</strong></td>
					</tr>
				</table>';
			$all_datas.='<table border="1" class="rpt_table" rules="all" width="1110" cellpadding="0" cellspacing="0" style="background-color:#bbb; font-size: 14px;">
					<tr>
						<th width="30">SL</th>
						<th width="130">Company</th>
						<th width="120">Item Category</th>
						<th width="120">Item Group</th>
						<th width="120">Sub Group Name</th>
						<th width="200">Item Description</th>
						<th width="100">Item Size</th>
						<th width="100">Model</th>
						<th width="100">Item Number</th>
						<th width="100">Item Code</th>
					</tr>
				</table>
				<table border="1" class="rpt_table" rules="all" width="1110" cellpadding="0" cellspacing="0" style="font-size: 14px;">';
					$i=1;					
					foreach ($duplicate_product_arr as $value)
					{
						$exp_val=explode('**', $value);
						$all_datas.='<tr>
							<td width="30">'.$i.'</td>
							<td width="130" style="word-break:break-all">'.$exp_val[0].'</td>
							<td width="120" style="word-break:break-all">'.$exp_val[1].'</td>
							<td width="120" style="word-break:break-all">'.$exp_val[2].'</td>
							<td width="120" style="word-break:break-all">'.$exp_val[3].'</td>
							<td width="200" style="word-break:break-all">'.$exp_val[4].'</td>
							<td width="100" style="word-break:break-all">'.$exp_val[5].'</td>
							<td width="100" style="word-break:break-all">'.$exp_val[6].'</td>
							<td width="100" style="word-break:break-all">'.$exp_val[7].'</td>
							<td width="100" style="word-break:break-all">'.$exp_val[8].'</td>
						</tr>';
						$i++;
					}
				$all_datas.='</table></div>';
		}
		//echo "10**$flag";oci_rollback($con);die;
		//echo "10** insert into inv_receive_master ($field_array1) values $data_array1";
		//echo "10**$flag*******$flg insert into inv_transaction ($field_array_trams) values $data_array_trans";oci_rollback($con);die;

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Excel File Upload Successfully...</p><br/>';
				echo $all_datas;
			}
			else
			{
				mysql_query("ROLLBACK");
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Excel File is not Uploaded...</p><br/>';
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Excel File Upload Successfully...</p><br/>';
				echo $all_datas;
			}
			else
			{
				oci_rollback($con);
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Excel File is not Uploaded...</p><br/>';		
			}
		}
		disconnect($con);
		die;
	}
	else
	{
		echo "Failed";	
	}
	die;
}


function fnc_store_wise_qty_operation($company_id,$store_id,$category,$prod_id,$trans_type,$dyes_lot)
{
	$trans_type=str_replace("'","",$trans_type);
	$prod_id=str_replace("'","",$prod_id);
	$store_id=str_replace("'","",$store_id);
	$category=str_replace("'","",$category);
	$company_id=str_replace("'","",$company_id);
	$dyes_lot=str_replace("'","",$dyes_lot);
	
	if($trans_type==2)
	{
		$prod_ids=rtrim($prod_id,",");
		$prod_ids=array_chunk(array_unique(explode(",",$prod_ids)),1000, true);
		$prod_cond="";
		$ji=0;
		foreach($prod_ids as $key=> $value)
		{
			if($ji==0)
			{
				$prod_cond=" and prod_id  in(".implode(",",$value).")";
			}
			else
			{
				$prod_cond.=" or prod_id  in(".implode(",",$value).")";
			}
			$ji++;
		}
		$category_ids=rtrim($category,",");
		$cat_ids=array_chunk(array_unique(explode(",",$category_ids)),1000, true);
		$cat_cond="";
		$k=0;
		foreach($cat_ids as $key=> $value)
		{
			if($k==0)
			{
				$cat_cond=" and category_id  in(".implode(",",$value).")";
			}
			else
			{
				$cat_cond.=" or category_id  in(".implode(",",$value).")";
			}
			$k++;
		}
	}

	if($trans_type==2) //Issue
	{
		$sql_data=sql_select("select id, company_id, category_id, prod_id, cons_qty, rate, amount from inv_store_wise_qty_dtls where company_id=$company_id  and status_active=1 and is_deleted=0 $prod_cond $cat_cond");
	}
	else if($trans_type==1 || $trans_type==4) //Recv && Issue Return;
	{
		$lot_cond="";
		if($dyes_lot!="")  $lot_cond=" and lot='$dyes_lot'";
		$sql_data=sql_select("select id, company_id, category_id, store_id, prod_id, cons_qty, rate, amount, lot
		from inv_store_wise_qty_dtls where company_id=$company_id and store_id=$store_id and category_id in($category) and status_active=1 and is_deleted=0 and prod_id=$prod_id $lot_cond");
	}
	$stock_prod_arr=array();
	if($trans_type==2) //Issue
	{
		$updated_store_ids=''; $updated_ids='';$prod_arr=array();
		foreach($sql_data as $row)
		{
		if($updated_store_ids=='') $updated_store_ids=$row[csf("id")];else $updated_store_ids.=",".$row[csf("id")];
		}
		$stock_prod_arr=$updated_store_ids;//.'**'.$stock_prod_arr;
	}
	else if($trans_type==1 || $trans_type==4) //recv && Issue Return;
	{
		if(count($sql_data)>0)//value Empty
		{
			foreach($sql_data as $row)
			{
				$stock_prod_arr[$row[csf('company_id')]][$row[csf('prod_id')]][$row[csf('store_id')]][$row[csf('category_id')]][$row[csf('lot')]]=$row[csf('id')];
			}
		}
	}

	 return $stock_prod_arr;

} //Function End

?>	