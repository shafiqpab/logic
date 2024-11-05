<?php
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");
include('../../../includes/common.php');
$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$user_id=$_SESSION['logic_erp']['user_id'];
$permission=$_SESSION['page_permission'];


if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process )); 
	extract($_REQUEST);

	if ($operation==0)  // Insert Here
	{	
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

		for($i=1; $i<=$total_row; $i++)
		{
			$supplier_name="supplier_name_".$i;
			$short_name="short_name_".$i;
			$contact_person="contact_person_".$i;
			$designation="designation_".$i;
			$contact_no="contact_no_".$i;
			$email="email_".$i;			
			$http_www="http_www_".$i;

			$address1="address1_".$i;
			$address2="address2_".$i;
			$address3="address3_".$i;
			$address4="address4_".$i;
			$country="country_".$i;
			$country_id=$country_library[$$country];

			$supplier_type_ids="supplier_type_".$i;
			$tag_company_ids="tag_company_".$i;

			$link_to_buyer="link_to_buyer_".$i;
			$link_to_buyer_id=$buyer_library[$$link_to_buyer];

			$credit_limit_days="credit_limit_days_".$i;
			$credit_limit_amount="credit_limit_amount_".$i;

			$currancy="currancy_".$i;
			if ($$currancy != "") $currancy_id=$currency_arr[$$currancy];
			else $currancy_id=1;

			$discount_method="discount_method_".$i;
			$discount_method_id=$currency_arr[$$currancy];

			$security_deducted="security_deducted_".$i;
			if ($$security_deducted != "") $security_deducted_id=$yes_no_arr[$$security_deducted];
			else $security_deducted_id=1;			

			$vat_to_be_deducted="vat_to_be_deducted_".$i;
			if ($$vat_to_be_deducted != "") $vat_to_be_deducted_id=$yes_no_arr[$$vat_to_be_deducted];
			else $vat_to_be_deducted_id=1;			

			$ait_to_be_deducted="ait_to_be_deducted_".$i;
			if ($$ait_to_be_deducted != "") $ait_to_be_deducted_id=$yes_no_arr[$$ait_to_be_deducted];
			else $ait_to_be_deducted_id=1;			

			$remark="remark_".$i;

			$individual="individual_".$i;
			if ($$individual != "") $individual_id=$yes_no_arr[$$individual];
			else $individual_id=1;

			$supplier_nature="supplier_nature_".$i;
			if ($$supplier_nature != "") $supplier_nature_id=$supplier_nature_arr[$$supplier_nature];
			else $supplier_nature_id=1;			

			$status="status_".$i;
			if ($$status != "") $status_id=$row_status_arr[$$status];
			else $status_id=1;			

			$tag_buyer_ids="tag_buyer_".$i;
			$supplier_ref="supplier_ref_".$i;

			$owner_info="owner_info_".$i;
			$owner_name=$owen_nid=$owen_contact=$owen_email='';
			$exp_owner_info=explode(',', $$owner_info);
			$owner_name=$exp_owner_info[0];
			$owen_nid=$exp_owner_info[1];
			$owen_contact=$exp_owner_info[2];
			$owen_email=$exp_owner_info[3];

			$tin_number="tin_number_".$i;
			$vat_number="vat_number_".$i;

			$duplicate = is_duplicate_field("supplier_name","lib_supplier","supplier_name='".$$supplier_name."' and is_deleted=0");
			if($duplicate==1)
			{
				echo "11**Duplicate Supplier Name is Not Allow.";
				disconnect($con);
				die;
			}

			if ($data_array != '') $data_array .=",";
			$data_array.="(".$id.",'".$$supplier_name."','".$$short_name."','".$$contact_person."','".$$designation."','".$$contact_no."','".$$email."','".$$http_www."','".$$address1."','".$$address2."','".$$address3."','".$$address4."','".$country_id."','".$$supplier_type_ids."','".$$tag_company_ids."','".$link_to_buyer_id."','".$$credit_limit_days."','".$$credit_limit_amount."','".$currancy_id."','".$discount_method_id."','".$security_deducted_id."','".$vat_to_be_deducted_id."','".$ait_to_be_deducted_id."','".$$remark."','".$individual_id."','".$supplier_nature_id."',1,'".$$tag_buyer_ids."','".$$supplier_ref."','".$owner_name."','".$owen_nid."','".$owen_contact."','".$owen_email."','".$$tin_number."','".$$vat_number."','".$user_id."','".$pc_date_time."',0)";
			$id = $id+1;

			// Insert Data in  lib_supplier_party_type Table
			if ($$supplier_type_ids != '')
			{
				$party_type=explode(',',$$supplier_type_ids);
				for($sp=0; $sp<count($party_type); $sp++)
				{
					if ($data_array1 != '') $data_array1 .=",";
					$data_array1.="(".$id_lib_supplier_party_type.",".$id.",".$party_type[$sp].")";
					$id_lib_supplier_party_type=$id_lib_supplier_party_type+1;
				}
			}	
			

			//Insert Data in  lib_supplier_tag_company Table----------------------------------------
			if ($$tag_company_ids != '')
			{
				$tagCompanyId=explode(',',$$tag_company_ids);
				for($stc=0; $stc<count($tagCompanyId); $stc++)
				{
					if ($data_array2 != '') $data_array2 .=",";
					$data_array2.="(".$id_lib_supplier_tag_company.",".$id.",".$tagCompanyId[$stc].")";
					$id_lib_supplier_tag_company=$id_lib_supplier_tag_company+1;
				}
			}
			

			//Insert Data in  lib_supplier_buyer Table
			if ($$tag_buyer_ids != '')
			{
				$tagBuyerId=explode(',',$$tag_buyer_ids);
				for($stb=0; $stb<count($tagBuyerId); $stb++)
				{
					if ($data_array3 != '') $data_array3 .=",";
					$data_array3.="(".$id_lib_supplier_tag_buyer.",".$id.",".$tagBuyerId[$stb].")";
					$id_lib_supplier_tag_buyer=$id_lib_supplier_tag_buyer+1;
				}
			}		

		}

		$flag=1;

		//echo "10** insert into lib_supplier_tag_buyer ($field_array3) values $data_array3";die;
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
		
		//echo "10**".$rID.'&&'.$rID1.'&&'.$rID2.'&&'.$rID3;die;
		$commit_msg="Data is Saveed!!";
		$roll_back_msg="Data is not saved!!";

		if($db_type==0)
		{
			if($flag==1)
			{
				mysql_query("COMMIT");  
				echo "0**".$commit_msg;
			}
			else
			{
				mysql_query("ROLLBACK"); 
				echo "10**".$roll_back_msg;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($flag==1)
			{
				oci_commit($con);  
				echo "0**".$commit_msg;
			}
			else
			{
				oci_rollback($con); 
				echo "10**".$roll_back_msg;
			}
		}
		disconnect($con);
		die;	
		
	}
}

?>