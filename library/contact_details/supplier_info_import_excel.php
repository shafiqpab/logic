<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../includes/common.php');
//echo '<pre>';print_r($_SESSION);
$permission=$_SESSION['page_permission'];

function check_special_character($string)
{
	$special_charater='*\'£$&()#~|=_"`^\\';
	$specialCharactersArr = str_split($special_charater);
	$splitStringArr=str_split($string);
	$result=array_diff($specialCharactersArr,$splitStringArr);
	if (count($result)<count($specialCharactersArr)) return 1;
	else return 0;
}

echo load_html_head_contents("Supplier Import","../../", 1, 1, $unicode,1,'');

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

	$source = $_FILES['uploadfile']['tmp_name'];
	$targetzip ='files/'.$_FILES['uploadfile']['name'];
	$file_name=$_FILES['uploadfile']['name'];
    //echo $source.'**'.$targetzip.'**'.$file_name;die;
	unset($_SESSION['excel']);
	if (move_uploaded_file($source, $targetzip)) 
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
				$supplier_name=$short_name=$contact_person=$designation=$contact_no=$email=$http_www=$address1='';
				$address2=$address3=$address4=$country=$supplier_type=$tag_company=$link_to_buyer=$credit_limit_days='';
				$credit_limit_amount=$currancy=$discount_method=$security_deducted=$vat_to_be_deducted=$ait_to_be_deducted='';
				$remark=$individual=$supplier_nature=$status=$tag_buyer=$supplier_ref=$owner_info=$tin_number=$vat_number='';
				//echo '<pre>';print_r($excel->sheets[0]['cells'][$i]);die;
				
				$str_rep=array("*",  "=", "\r", "\n", "#");
				//$str_rep= preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $excel->sheets[0]['cells'][$i]); 
				if (isset($excel->sheets[0]['cells'][$i][1])) $supplier_name = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][1]);
				if (isset($excel->sheets[0]['cells'][$i][2])) $short_name = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][2]);
				if (isset($excel->sheets[0]['cells'][$i][3])) $contact_person = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][3]);
				if (isset($excel->sheets[0]['cells'][$i][4])) $designation = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][4]);
				if (isset($excel->sheets[0]['cells'][$i][5])) $contact_no = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][5]);
				if (isset($excel->sheets[0]['cells'][$i][6])) $email = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][6]);
				if (isset($excel->sheets[0]['cells'][$i][7])) $http_www  = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][7]);
				if (isset($excel->sheets[0]['cells'][$i][8])) $address1 = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][8]);
				if (isset($excel->sheets[0]['cells'][$i][9])) $address2= str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][9]);
				if (isset($excel->sheets[0]['cells'][$i][10])) $address3 = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][10]);
				if (isset($excel->sheets[0]['cells'][$i][11])) $address4 = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][11]);
				if (isset($excel->sheets[0]['cells'][$i][12])) $country = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][12]);
				if (isset($excel->sheets[0]['cells'][$i][13])) $supplier_type = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][13]);
				if (isset($excel->sheets[0]['cells'][$i][14])) $tag_company = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][14]);
				if (isset($excel->sheets[0]['cells'][$i][15])) $link_to_buyer = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][15]);
				if (isset($excel->sheets[0]['cells'][$i][16])) $credit_limit_days = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][16]);
				if (isset($excel->sheets[0]['cells'][$i][17])) $credit_limit_amount = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][17]);
				if (isset($excel->sheets[0]['cells'][$i][18])) $currancy = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][18]);
				if (isset($excel->sheets[0]['cells'][$i][19])) $discount_method = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][19]);
				if (isset($excel->sheets[0]['cells'][$i][20])) $security_deducted = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][20]);
				if (isset($excel->sheets[0]['cells'][$i][21])) $vat_to_be_deducted = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][21]);
				if (isset($excel->sheets[0]['cells'][$i][22])) $ait_to_be_deducted = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][22]);
				if (isset($excel->sheets[0]['cells'][$i][23])) $remark = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][23]);
				if (isset($excel->sheets[0]['cells'][$i][24])) $individual = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][24]);
				if (isset($excel->sheets[0]['cells'][$i][25])) $supplier_nature = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][25]);
				if (isset($excel->sheets[0]['cells'][$i][26])) $status = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][26]);
				if (isset($excel->sheets[0]['cells'][$i][27])) $tag_buyer = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][27]);			
				if (isset($excel->sheets[0]['cells'][$i][28])) $supplier_ref = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][28]);		
				if (isset($excel->sheets[0]['cells'][$i][29])) $owner_info = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][29]);		
				if (isset($excel->sheets[0]['cells'][$i][30])) $tin_number = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][30]);		
				if (isset($excel->sheets[0]['cells'][$i][31])) $vat_number = str_replace($str_rep,' ',$excel->sheets[0]['cells'][$i][31]);	

				$all_data_arr[$i][1]['supplier_name']=trim($supplier_name);
				$all_data_arr[$i][2]['short_name']=trim($short_name);
				$all_data_arr[$i][3]['contact_person']=trim($contact_person);
				$all_data_arr[$i][4]['designation']=trim($designation);
				$all_data_arr[$i][5]['contact_no']=trim($contact_no);
				$all_data_arr[$i][6]['email']=trim($email);
				$all_data_arr[$i][7]['http_www']=trim($http_www);
				$all_data_arr[$i][8]['address1']=trim($address1);
				$all_data_arr[$i][9]['address2']=trim($address2);
				$all_data_arr[$i][10]['address3']=trim($address3);
				$all_data_arr[$i][11]['address4']=trim($address4);
				$all_data_arr[$i][12]['country']=trim($country);
				$all_data_arr[$i][13]['supplier_type']=trim($supplier_type);
				$all_data_arr[$i][14]['tag_company']=trim($tag_company);
				$all_data_arr[$i][15]['link_to_buyer']=trim($link_to_buyer);
				$all_data_arr[$i][16]['credit_limit_days']=trim($credit_limit_days);
				$all_data_arr[$i][17]['credit_limit_amount']=trim($credit_limit_amount);
				$all_data_arr[$i][18]['currancy']=trim($currancy);
				$all_data_arr[$i][19]['discount_method']=trim($discount_method);
				$all_data_arr[$i][20]['security_deducted']=trim($security_deducted);
				$all_data_arr[$i][21]['vat_to_be_deducted']=trim($vat_to_be_deducted);
				$all_data_arr[$i][22]['ait_to_be_deducted']=trim($ait_to_be_deducted);
				$all_data_arr[$i][23]['remark']=trim($remark);
				$all_data_arr[$i][24]['individual']=trim($individual);
				$all_data_arr[$i][25]['supplier_nature']=trim($supplier_nature);
				$all_data_arr[$i][26]['status']=trim($status);
				$all_data_arr[$i][27]['tag_buyer']=trim($tag_buyer);
				$all_data_arr[$i][28]['supplier_ref']=trim($supplier_ref);
				$all_data_arr[$i][29]['owner_info']=trim($owner_info);
				$all_data_arr[$i][30]['tin_number']=trim($tin_number);
				$all_data_arr[$i][31]['vat_number']=trim($vat_number);
			
			}
		}
		//echo '<pre>';print_r($all_data_arr);die;

		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		$supplier_library=return_library_array("select supplier_name, id from lib_supplier where status_active=1 and is_deleted=0", "supplier_name", "id");
		$country_library=return_library_array("select country_name, id from lib_country where status_active=1 and is_deleted=0","country_name","id");
		$company_library=return_library_array("select company_name, id from lib_company where status_active=1 and is_deleted=0","company_name","id");
		$buyer_library=return_library_array("select buyer_name, id from lib_buyer where status_active=1 and is_deleted=0","buyer_name","id");			

    	$party_type_supplier_arr = array_flip($party_type_supplier);
		$currency_arr = array_flip($currency);
		$yes_no_arr = array_flip($yes_no);
		$supplier_nature_arr = array_flip($supplier_nature);
		$row_status_arr = array_flip($row_status);

		$id=return_next_id( "id", "lib_supplier", 1 );
		$id_lib_supplier_party_type=return_next_id( "id", "lib_supplier_party_type", 1 );
		$id_lib_supplier_tag_company=return_next_id( "id", "lib_supplier_tag_company", 1 );
		$id_lib_supplier_tag_buyer=return_next_id( "id", "lib_supplier_tag_buyer", 1 );
		$field_array="id, supplier_name, short_name, contact_person, designation, contact_no, email, web_site, address_1, address_2, address_3, address_4, country_id,  party_type, tag_company, buyer, credit_limit_days, credit_limit_amount, credit_limit_amount_currency, discount_method, securitye_deducted, vat_to_be_deducted, ait_to_be_deducted, remark, individual, supplier_nature, status_active, tag_buyer, supplier_ref, owner_name, owner_nid, owner_contact, owner_email, tin_number, vat_number, inserted_by, insert_date, is_deleted";
		$field_array1="id, supplier_id, party_type";
		$field_array2="id, supplier_id, tag_company";
		$field_array3="id, supplier_id, tag_buyer";	


		$sql_supplier_res=sql_select("select id as ID, supplier_name as SUPPLIER_NAME from lib_supplier where status_active=1 and is_deleted=0");
		$duplicate_supplier_data_arr=array();
		foreach ($sql_supplier_res as $val)
		{
			$duplicate_supplier_data_arr[$val['SUPPLIER_NAME']] = $val['ID'];
		}
		//echo '<pre>';print_r($duplicate_supplier_data_arr);
		
		$row_num_excel=1;
		foreach($all_data_arr as $column_val)
		{
			$row_num_excel++;			

			$supplier_name=$column_val[1]['supplier_name'];
			$check_supplier_name=check_special_character($supplier_name);
			if ($check_supplier_name==1 || $supplier_name=="") {
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Supplier Name ['.$column_val[1]["supplier_name"].'] and Excel row number ['.$row_num_excel.']</p>';
				oci_rollback($con); disconnect($con); die;
			}
			
			if (array_key_exists($supplier_name, $duplicate_supplier_data_arr))
			{
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Duplicate Supplier Name ['.$supplier_name.'] and Excel row number ['.$row_num_excel.']</p>';
				oci_rollback($con); disconnect($con); die;
			}
			$duplicate_supplier_data_arr[$supplier_name] = $id;

			$short_name=$column_val[2]['short_name'];
			$check_short_name=check_special_character($short_name);
			if ($check_short_name==1 || $short_name=="") {
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Supplier Short Name ['.$column_val[2]["short_name"].'] and Excel row number ['.$row_num_excel.']</p>';
				oci_rollback($con); disconnect($con); die;
			}			

			$contact_person=$column_val[3]['contact_person'];
			$designation=$column_val[4]['designation'];
			$contact_no=$column_val[5]['contact_no'];
			$email=$column_val[6]['email'];
			$http_www=$column_val[7]['http_www'];
			$address1=$column_val[8]['address1'];
			$address2=$column_val[9]['address2'];
			$address3=$column_val[10]['address3'];
			$address4=$column_val[11]['address4'];

			$country_id=$country_library[$column_val[12]['country']];
			if ($column_val[12]['country'] != "" && $country_id==""){
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Country Name ['.$column_val[12]['country'].'] and Excel row number ['.$row_num_excel.']</p>';
				oci_rollback($con); disconnect($con); die;
			}
             
			if ($column_val[13]['supplier_type']=="") {
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Supplier Type ['.$column_val[13]["supplier_type"].'] and Excel row number ['.$row_num_excel.']</p>';
				oci_rollback($con); disconnect($con); die;
			}
			$exp_supplier_type=explode(',', $column_val[13]['supplier_type']);
			$supplier_type_id=$supplier_type_ids='';
			foreach ($exp_supplier_type as $supp_type) {
				$supplier_type_id .= $party_type_supplier_arr[$supp_type].',';
			}
			$supplier_type_ids=rtrim($supplier_type_id,',');

			$tag_company_ids='';
			if ($column_val[14]['tag_company'] != '')
			{
				$tag_company_id='';
				$exp_tag_company=explode(',', $column_val[14]['tag_company']);
				foreach ($exp_tag_company as $tagCompany) {
					$tag_company_id .= $company_library[$tagCompany].',';
				}
				$tag_company_ids=rtrim($tag_company_id,',');
			}
			else
			{
				$tag_company_ids = implode(",",$company_library);
			}

			$link_to_buyer_id=$buyer_library[$column_val[15]['link_to_buyer']];
			if ($column_val[15]['link_to_buyer'] != "" && $link_to_buyer_id==""){
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Link to Buyer ['.$column_val[15]['link_to_buyer'].'] and Excel row number ['.$row_num_excel.']</p>';
				oci_rollback($con); disconnect($con); die;
			}
			
			$credit_limit_days=$column_val[16]['credit_limit_days'];
			$credit_limit_amount=$column_val[17]['credit_limit_amount'];

			if ($column_val[18]['currancy'] != "")
			{
				$currancy_id=$currency_arr[$currancy];
				if ($currancy_id==""){
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Currency ['.$column_val[18]['currancy'].'] and Excel row number ['.$row_num_excel.']</p>';
					oci_rollback($con); disconnect($con); die;
				}
			}
			else $currancy_id=1;

			$discount_method_id=$currency_arr[$column_val[19]['discount_method']];
			if ($column_val[19]['discount_method'] != "" && $discount_method_id==""){
				echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct Discount Method ['.$column_val[19]['discount_method'].'] and Excel row number ['.$row_num_excel.']</p>';
				oci_rollback($con); disconnect($con); die;
			}

			if ($column_val[20]['security_deducted'] != "")
			{
				$security_deducted_id=$yes_no_arr[$security_deducted];
				if ($security_deducted_id==""){
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct security deducted ['.$column_val[20]['security_deducted'].'] and Excel row number ['.$row_num_excel.']</p>';
					oci_rollback($con); disconnect($con); die;
				}
			}
			else $security_deducted_id=1;

			if ($column_val[21]['vat_to_be_deducted'] != "")
			{
				$vat_to_be_deducted_id=$yes_no_arr[$vat_to_be_deducted];
				if ($vat_to_be_deducted_id==""){
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct vat to be deducted ['.$column_val[21]['vat_to_be_deducted'].'] and Excel row number ['.$row_num_excel.']</p>';
					oci_rollback($con); disconnect($con); die;
				}
			}
			else $vat_to_be_deducted_id=1;

			if ($column_val[22]['ait_to_be_deducted'] != "")
			{
				$ait_to_be_deducted_id=$yes_no_arr[$ait_to_be_deducted];
				if ($ait_to_be_deducted_id==""){
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct ait to be deducted ['.$column_val[22]['ait_to_be_deducted'].'] and Excel row number ['.$row_num_excel.']</p>';
					oci_rollback($con); disconnect($con); die;
				}
			}
			else $ait_to_be_deducted_id=1;

			$remark=$column_val[23]['remark'];

			if ($column_val[24]['individual'] != "")
			{
				$individual_id=$yes_no_arr[$individual];
				if ($individual_id==""){
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct individual ['.$column_val[24]['individual'].'] and Excel row number ['.$row_num_excel.']</p>';
					oci_rollback($con); disconnect($con); die;
				}
			}
			else $individual_id=1;

			if ($column_val[25]['supplier_nature'] != "")
			{
				$supplier_nature_id=$supplier_nature_arr[$supplier_nature];
				if ($supplier_nature_id==""){
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct supplier nature ['.$column_val[25]['supplier_nature'].'] and Excel row number ['.$row_num_excel.']</p>';
					oci_rollback($con); disconnect($con); die;
				}
			}
			else $supplier_nature_id=1;

			if ($column_val[26]['status'] != "")
			{
				$status_id=$row_status_arr[$status];
				if ($status_id==""){
					echo '<p style="font-size: 18px; text-align:center; color: red; font-weight:bold;">Please Fill-Up Correct status ['.$column_val[26]['status'].'] and Excel row number ['.$row_num_excel.']</p>';
					oci_rollback($con); disconnect($con); die;
				}
			}
			else $status_id=1;

			$tag_buyer_id=$tag_buyer_ids='';
			if ($column_val[27]['tag_buyer'] != '')
			{
				$exp_tag_buyer=explode(',', $column_val[27]['tag_buyer']);
				foreach ($exp_tag_buyer as $tagBuyer) {
					$tag_buyer_id .= $buyer_library[$tagBuyer].',';
				}
				$tag_buyer_ids=rtrim($tag_buyer_id,',');
			}

			$supplier_ref=$column_val[28]['supplier_ref'];

			$owner_info=$column_val[29]['owner_info'];
			$owner_name=$owen_nid=$owen_contact=$owen_email='';
			$exp_owner_info=explode(',', $owner_info);
			$owner_name=$exp_owner_info[0];
			$owen_nid=$exp_owner_info[1];
			$owen_contact=$exp_owner_info[2];
			$owen_email=$exp_owner_info[3];

			$tin_number=$column_val[30]['tin_number'];
			$vat_number=$column_val[31]['vat_number'];			

			if ($data_array != '') $data_array .=",";
			$data_array.="(".$id.",'".$supplier_name."','".$short_name."','".$contact_person."','".$designation."','".$contact_no."','".$email."','".$http_www."','".$address1."','".$address2."','".$address3."','".$address4."','".$country_id."','".$supplier_type_ids."','".$tag_company_ids."','".$link_to_buyer_id."','".$credit_limit_days."','".$credit_limit_amount."','".$currancy_id."','".$discount_method_id."','".$security_deducted_id."','".$vat_to_be_deducted_id."','".$ait_to_be_deducted_id."','".$remark."','".$individual_id."','".$supplier_nature_id."',1,'".$tag_buyer_ids."','".$supplier_ref."','".$owner_name."','".$owen_nid."','".$owen_contact."','".$owen_email."','".$tin_number."','".$vat_number."','".$user_id."','".$pc_date_time."',0)";
			$id = $id+1;

			// Insert Data in  lib_supplier_party_type Table
			if ($supplier_type_ids != '')
			{
				$party_type=explode(',',$supplier_type_ids);
				for($sp=0; $sp<count($party_type); $sp++)
				{
					if ($data_array1 != '') $data_array1 .=",";
					$data_array1.="(".$id_lib_supplier_party_type.",".$id.",".$party_type[$sp].")";
					$id_lib_supplier_party_type=$id_lib_supplier_party_type+1;
				}
			}	
			

			//Insert Data in  lib_supplier_tag_company Table----------------------------------------
			if ($tag_company_ids != '')
			{
				$tagCompanyId=explode(',',$tag_company_ids);
				for($stc=0; $stc<count($tagCompanyId); $stc++)
				{
					if ($data_array2 != '') $data_array2 .=",";
					$data_array2.="(".$id_lib_supplier_tag_company.",".$id.",".$tagCompanyId[$stc].")";
					$id_lib_supplier_tag_company=$id_lib_supplier_tag_company+1;
				}
			}
			

			//Insert Data in  lib_supplier_buyer Table
			if ($tag_buyer_ids != '')
			{
				$tagBuyerId=explode(',',$tag_buyer_ids);
				for($stb=0; $stb<count($tagBuyerId); $stb++)
				{
					if ($data_array3 != '') $data_array3 .=",";
					$data_array3.="(".$id_lib_supplier_tag_buyer.",".$id.",".$tagBuyerId[$stb].")";
					$id_lib_supplier_tag_buyer=$id_lib_supplier_tag_buyer+1;
				}
			}
		}	

		$flag=1;
		//echo "10** insert into lib_supplier ($field_array) values $data_array";die;
		$rID=sql_insert("lib_supplier",$field_array,$data_array,0);
		if ($flag==1){
			if ($rID) $flag=1; else $flag=0;
		}

		if ($data_array1 != '')
		{
			$rID1=sql_insert("lib_supplier_party_type",$field_array1,$data_array1,0);
			if ($flag==1){
				if ($rID1) $flag=1; else $flag=0;
			}
		}

		if ($data_array2 != '')
		{
			$rID2=sql_insert("lib_supplier_tag_company",$field_array2,$data_array2,1);
			if ($flag==1){
				if ($rID2) $flag=1; else $flag=0;
			}
		}
		
		if ($data_array3 != '')
		{
			$rID3=sql_insert("lib_supplier_tag_buyer",$field_array3,$data_array3,1);
			if ($flag==1){
				if ($rID3) $flag=1; else $flag=0;
			}
		}

		//echo '<pre>';print_r($all_data_arr);die;
		//echo "10** insert into product_details_master ($field_array) values $data_array";die;

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
?>
