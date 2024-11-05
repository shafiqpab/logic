<?
header('Content-type:text/html; charset=utf-8');
session_start();
if( $_SESSION['logic_erp']['user_id'] == "" ) header("location:login.php");

include('../../../includes/common.php'); 

$data=$_REQUEST['data'];
$action=$_REQUEST['action'];
$permission=$_SESSION['page_permission'];
if ($action=="save_update_delete")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	// $txt_sequence=str_replace("'", "", $txt_sequence);

	//echo $txt_contact_no;die;

	if ($operation==0)  // Insert Here
	{
		// ==============================hide by Rasel Sir=============14-11-2021=================================
		// if ($txt_sequence=='') $duplicate_cond =" and sequence_no is not null"; else $duplicate_cond =" and sequence_no='".$txt_sequence."'";
		// $duplicate_item = is_duplicate_field("buyer_name","lib_buyer","is_deleted=0 $duplicate_cond");
		// if($duplicate_item==1)
		// {
		// 	echo "11**Duplicate Sequence No. is Not Allow.";
		// 	disconnect($con);
		// 	die;
		// }
		if (is_duplicate_field( "buyer_name", "lib_buyer", "buyer_name=$txt_buyer_name and is_deleted=0" ) == 1)
		{
			echo "11**0"; die;
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}

			$id=return_next_id( "id", "lib_buyer", 1);
			$field_array="id, buyer_name, short_name, contact_person, exporters_reference, party_type, designation, tag_company, country_id, web_site, buyer_email, address_1, address_2, address_3, address_4,remark,supllier, credit_limit_days, credit_limit_amount,credit_limit_amount_currency, discount_method, securitye_deducted, vat_to_be_deducted, ait_to_be_deducted,sewing_effi_mkt_percent,sewing_effi_plaing_per,marketing_team_id,control_delivery,cut_off_used,delivery_buffer_days,deffd_lc_cost_percent,min_quoted_profit_parcent,min_budgeted_profit_parcent,commercial_invoice,inserted_by, insert_date, status_active,is_deleted,bank_id,is_partial_rlz,lc_sc_tol_level,sequence_no,contact_no";
			$data_array="(".$id.",".$txt_buyer_name.",".$txt_short_name.",".$txt_contact_person.",".$txt_exporter_ref.",".$cbo_party_type.",".$txt_desination.",".$cbo_buyer_company.",".$cbo_country.",".$txt_web_site.",".$txt_buyer_email.",".$txt_address_1st.",".$txt_address_2nd.",".$txt_address_3rd.",".$txt_address_4th.",".$txt_remark.",".$cbo_buyer_supplier.",".$txt_credit_limit_days.",".$txt_credit_limit_amount.",".$cbo_credit_limit_amount_curr.",".$cbo_discount_method.",".$cbo_security_deducted.",".$cbo_vat_to_be_deducted.",".$cbo_ait_to_be_deducted.",".$txt_sewing_effi_mkt.",".$txt_sewing_effi_planing.",".$cbo_marketing_team.",".$cbo_control_delivery.",".$cbo_cut_Off_used.",".$txt_del_buffer_days.",".$txt_deffd_lc_cost_percent.",".$txt_min_quoted_profit_parcent.",".$txt_min_budgeted_profit_parcent.",".$cbo_commercial_invoice.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',".$cbo_status.",'0',".$cbo_bank_name.",".$cbo_partial_rlz.",".$cbo_tol_level.",".$txt_sequence.",".$txt_contact_no.")";

			//  echo "10** insert into lib_buyer ($field_array) values $data_array";die;
			$rID=sql_insert("lib_buyer",$field_array,$data_array,0);
			
			//Insert Data in lib_buyer_party_type Table----------------------------------------
			$data_array="";
			$party_type=explode(',',str_replace("'","",$cbo_party_type));
			for($i=0; $i<count($party_type); $i++)
			{
				if($id_lib_buyer_party_type=="") $id_lib_buyer_party_type=return_next_id( "id", "lib_buyer_party_type", 1 ); else $id_lib_buyer_party_type=$id_lib_buyer_party_type+1;
				if($i==0) $add_comma=""; else $add_comma=",";
				$data_array.="$add_comma(".$id_lib_buyer_party_type.",".$id.",".$party_type[$i].")";
			}
			$field_array="id, buyer_id, party_type";
			$rID1=sql_insert("lib_buyer_party_type",$field_array,$data_array,0);
			
			//----------------------------------------------------------------------------------
			//Insert Data in  lib_buyer_tag_company Table----------------------------------------
			$data_array="";
			$tag_company=explode(',',str_replace("'","",$cbo_buyer_company));
			for($i=0; $i<count($tag_company); $i++)
			{
				if($id_lib_buyer_tag_company=="") $id_lib_buyer_tag_company=return_next_id( "id", "lib_buyer_tag_company", 1 ); else $id_lib_buyer_tag_company=$id_lib_buyer_tag_company+1;
				if($i==0) $add_comma=""; else $add_comma=",";
				$data_array.="$add_comma(".$id_lib_buyer_tag_company.",".$id.",".$tag_company[$i].")";
			}
			$field_array="id, buyer_id, tag_company";
			 //echo "10**insert into lib_buyer_tag_company (".$field_array.") values ".$data_array."";die;
			$rID2=sql_insert("lib_buyer_tag_company",$field_array,$data_array,1);
			$rID3=true;
			if(str_replace("'","",$cbo_bank_name)!="")
			{
				$data_array3="";
				$tag_bank=explode(',',str_replace("'","",$cbo_bank_name));
				for($i=0; $i<count($tag_bank); $i++)
				{
					if($id_lib_buyer_tag_bank=="") $id_lib_buyer_tag_bank=return_next_id( "id", "lib_buyer_tag_bank", 1 ); else $id_lib_buyer_tag_bank=$id_lib_buyer_tag_bank+1;
					if($i==0) $add_comma=""; else $add_comma=",";
					$data_array3.="$add_comma(".$id_lib_buyer_tag_bank.",".$id.",".$tag_bank[$i].")";
				}
				if($data_array3!='')
				{
					 // echo "10**insert into lib_buyer_tag_bank (".$field_array3.") values ".$data_array3."";die;
				$field_array3="id, buyer_id, tag_bank";
				$rID3=sql_insert("lib_buyer_tag_bank",$field_array3,$data_array3,1);
				}
			}
			//echo $rID3;die;
			//----------------------------------------------------------------------------------
			if($db_type==0)
			{
				if($rID  && $rID1  && $rID2 && $rID3)
				{
					mysql_query("COMMIT");
					echo "0**".$rID;
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".$rID;
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($rID  && $rID1  && $rID2 && $rID3)
				{
					oci_commit($con);
					echo "0**".$rID;
				}
				else
				{
					oci_rollback($con);
					echo "10**".$rID;
				}
			}
			disconnect($con);
			die;
		}
	}
	else if ($operation==1)   // Update Here
	{
		if ($txt_sequence=='') $duplicate_cond =" and sequence_no is not null"; else $duplicate_cond =" and sequence_no='".$txt_sequence."'";
		$duplicate_item = is_duplicate_field("buyer_name","lib_buyer","is_deleted=0 and id!=$update_id $duplicate_cond");
		if($duplicate_item==1)
		{
			echo "11**Duplicate Sequence No. is Not Allow.";
			disconnect($con);
			die;
		}

		if (is_duplicate_field( "buyer_name", "lib_buyer", "buyer_name=$txt_buyer_name  and id!=$update_id and is_deleted=0" ) == 1)
		{
			echo "11**0"; die;
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$id=return_next_id( "id", "lib_buyer", 1 ) ;
			if(str_replace("'", "", $is_posted_accounts)==1)
			{
				//########## previous tag company check start ###########//
				$sql_prev_data=sql_select("select tag_company from lib_buyer where id=$update_id");
				$prev_tag_company=explode(",",$sql_prev_data[0][csf("tag_company")]);
				//echo "10**=".count($diff_tag_com);print_r($prev_tag_company);print_r($current_tag_company);oci_rollback($con);disconnect($con);die;
				$current_tag_company=explode(",",str_replace("'","",$cbo_buyer_company));
				$diff_tag_com=array_diff($prev_tag_company,$current_tag_company);
				if(count($diff_tag_com)>0)
				{
					echo "50** Already Posted Company Change Not Allow";oci_rollback($con);disconnect($con);die;
				}
				//########## previous tag company check end start ###########//
				
				$field_array="contact_person*exporters_reference*party_type*designation*tag_company*country_id*web_site*buyer_email*address_1*address_2*address_3* address_4*remark*supllier*credit_limit_days*credit_limit_amount*credit_limit_amount_currency*discount_method*securitye_deducted*vat_to_be_deducted*ait_to_be_deducted*sewing_effi_mkt_percent*sewing_effi_plaing_per*marketing_team_id*control_delivery*cut_Off_used*delivery_buffer_days*deffd_lc_cost_percent*min_quoted_profit_parcent*min_budgeted_profit_parcent*commercial_invoice*updated_by*update_date*bank_id*is_partial_rlz*is_posted_sql*lc_sc_tol_level*sequence_no*contact_no";
				$data_array="".$txt_contact_person."*".$txt_exporter_ref."*".$cbo_party_type."*".$txt_desination."*".$cbo_buyer_company."*".$cbo_country."*".$txt_web_site."*".$txt_buyer_email."*".$txt_address_1st."*".$txt_address_2nd."*".$txt_address_3rd."*".$txt_address_4th."*".$txt_remark."*".$cbo_buyer_supplier."*".$txt_credit_limit_days."*".$txt_credit_limit_amount."*".$cbo_credit_limit_amount_curr."*".$cbo_discount_method."*".$cbo_security_deducted."*".$cbo_vat_to_be_deducted."*".$cbo_ait_to_be_deducted."*".$txt_sewing_effi_mkt."*".$txt_sewing_effi_planing."*".$cbo_marketing_team."*".$cbo_control_delivery."*".$cbo_cut_Off_used."*".$txt_del_buffer_days."*".$txt_deffd_lc_cost_percent."*".$txt_min_quoted_profit_parcent."*".$txt_min_budgeted_profit_parcent."*".$cbo_commercial_invoice."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_bank_name."*".$cbo_partial_rlz."*2"."*".$cbo_tol_level."*".$txt_sequence."*".$txt_contact_no."";
			}
			else
			{
				$field_array="buyer_name*short_name*contact_person*exporters_reference*party_type*designation*tag_company*country_id*web_site*buyer_email*address_1*address_2*address_3* address_4*remark*supllier*credit_limit_days*credit_limit_amount*credit_limit_amount_currency*discount_method*securitye_deducted*vat_to_be_deducted*ait_to_be_deducted*sewing_effi_mkt_percent*sewing_effi_plaing_per*marketing_team_id*control_delivery*cut_Off_used*delivery_buffer_days*deffd_lc_cost_percent*min_quoted_profit_parcent*min_budgeted_profit_parcent*commercial_invoice*updated_by*update_date *status_active*is_deleted*bank_id*is_partial_rlz*is_posted_sql*lc_sc_tol_level*sequence_no*contact_no";
				$data_array="".$txt_buyer_name."*".$txt_short_name."*".$txt_contact_person."*".$txt_exporter_ref."*".$cbo_party_type."*".$txt_desination."*".$cbo_buyer_company."*".$cbo_country."*".$txt_web_site."*".$txt_buyer_email."*".$txt_address_1st."*".$txt_address_2nd."*".$txt_address_3rd."*".$txt_address_4th."*".$txt_remark."*".$cbo_buyer_supplier."*".$txt_credit_limit_days."*".$txt_credit_limit_amount."*".$cbo_credit_limit_amount_curr."*".$cbo_discount_method."*".$cbo_security_deducted."*".$cbo_vat_to_be_deducted."*".$cbo_ait_to_be_deducted."*".$txt_sewing_effi_mkt."*".$txt_sewing_effi_planing."*".$cbo_marketing_team."*".$cbo_control_delivery."*".$cbo_cut_Off_used."*".$txt_del_buffer_days."*".$txt_deffd_lc_cost_percent."*".$txt_min_quoted_profit_parcent."*".$txt_min_budgeted_profit_parcent."*".$cbo_commercial_invoice."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*".$cbo_status."*'0'*".$cbo_bank_name."*".$cbo_partial_rlz."*2"."*".$cbo_tol_level."*".$txt_sequence."*".$txt_contact_no."";
			}
			
			$rID=sql_update("lib_buyer",$field_array,$data_array,"id","".$update_id."",0);
			//echo '10**'.$rID ;oci_rollback($con);disconnect($con);die;
			//Insert Data in lib_buyer_party_type Table----------------------------------------
			$rID1=execute_query( "delete from lib_buyer_party_type where  buyer_id = $update_id",0);
			$data_array="";
			$party_type=explode(',',str_replace("'","",$cbo_party_type));
			for($i=0; $i<count($party_type); $i++)
			{
				if($id_lib_buyer_party_type=="") $id_lib_buyer_party_type=return_next_id( "id", "lib_buyer_party_type", 1 ); else $id_lib_buyer_party_type=$id_lib_buyer_party_type+1;
				if($i==0) $add_comma=""; else $add_comma=",";
				$data_array.="$add_comma(".$id_lib_buyer_party_type.",".$update_id.",".$party_type[$i].")";
			}
			$field_array="id, buyer_id, party_type";
			$rID2=sql_insert("lib_buyer_party_type",$field_array,$data_array,0);
			//------------------------------------------------------------------------------------

			//Insert Data in  lib_buyer_tag_company Table----------------------------------------
			//if(str_replace("'", "", $is_posted_accounts)!=1)
			//{
				$data_array="";
				$tag_company=explode(',',str_replace("'","",$cbo_buyer_company));
				for($i=0; $i<count($tag_company); $i++)
				{
					if($id_lib_buyer_tag_company=="") $id_lib_buyer_tag_company=return_next_id( "id", "lib_buyer_tag_company", 1 ); else $id_lib_buyer_tag_company=$id_lib_buyer_tag_company+1;
					if($i==0) $add_comma=""; else $add_comma=",";
					$data_array.="$add_comma(".$id_lib_buyer_tag_company.",".$update_id.",".$tag_company[$i].")";
				}
			//}
			$rID3=$rID4=$rID5=$rID6=1;
			//if(str_replace("'", "", $is_posted_accounts)!=1)
			//{
				$rID3=execute_query( "delete from lib_buyer_tag_company where  buyer_id = $update_id",0);
				$field_array="id, buyer_id, tag_company";
				$rID4=sql_insert("lib_buyer_tag_company",$field_array,$data_array,1);
			//}
			
			if(str_replace("'","",$cbo_bank_name)!="")
			{
				$rID5=execute_query( "delete from lib_buyer_tag_bank where  buyer_id = $update_id",0);
				$data_array6="";
				$tag_bank=explode(',',str_replace("'","",$cbo_bank_name));
				for($i=0; $i<count($tag_bank); $i++)
				{
					if($id_lib_buyer_tag_bank=="") $id_lib_buyer_tag_bank=return_next_id( "id", "lib_buyer_tag_bank", 1 ); else $id_lib_buyer_tag_bank=$id_lib_buyer_tag_bank+1;
					if($i==0) $add_comma=""; else $add_comma=",";
					$data_array6.="$add_comma(".$id_lib_buyer_tag_bank.",".$update_id.",".$tag_bank[$i].")";
				}
				$field_array6="id, buyer_id, tag_bank";
				$rID6=sql_insert("lib_buyer_tag_bank",$field_array6,$data_array6,1);
			}
			//echo $rID.'='.$rID1.'='.$rID2.'='.$rID3.'='.$rID4.'='.$rID5.'='.$rID6;oci_rollback($con);disconnect($con);die;
			//----------------------------------------------------------------------------------

			if($db_type==0)
			{
				if($rID && $rID1 && $rID2 && $rID3 && $rID4 && $rID5 && $rID6)
				{
					mysql_query("COMMIT");
					echo "1**".$rID;
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".$rID;
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
			  if($rID && $rID1 && $rID2 && $rID3 && $rID4)
				{
					oci_commit($con);
					echo "1**".$rID;
				}
				else
				{
					oci_rollback($con);
					echo "10**".$rID;
				}
			}
			disconnect($con);
			die;
		}
	}
	else if ($operation==2)   // Delete Here
	{
		if(str_replace("'", "", $is_posted_accounts)==1)
			{ echo "50**Already Posted In Accounts. Buyer Deleting Not Allowed";disconnect($con);die; }
		die;
		//$s="select job_no from wo_po_details_master where buyer_name=$hidden_buyer_id  and status_active=1 and is_deleted=0";
		$marchandising_job=return_field_value("min(job_no) as job_no", "wo_po_details_master", "buyer_name=$hidden_buyer_id  and status_active=1 and is_deleted=0","job_no");
		$sub_contract_job=return_field_value("min(subcon_job) as job_no" , "subcon_ord_mst", "party_id=$hidden_buyer_id  and status_active=1 and is_deleted=0","job_no");
		$sample_develop=return_field_value("id as sam_dev_id", "sample_development_mst", "buyer_name=$hidden_buyer_id  and status_active=1 and is_deleted=0","sam_dev_id");
		$price_quotation=return_field_value("id as pq_id", "wo_price_quotation", "buyer_id=$hidden_buyer_id  and status_active=1 and is_deleted=0","pq_id");
		$quotation_inquiry=return_field_value("system_number as system_number", "wo_quotation_inquery", "buyer_id=$hidden_buyer_id  and status_active=1 and is_deleted=0","system_number");
		$buyer_quotation_final=return_field_value("id as quotation_final", "wo_pri_sim_mst", "buyer_id=$hidden_buyer_id  and status_active=1 and is_deleted=0","quotation_final");
		$sales_contract=return_field_value("contact_system_id as contact_system_id", "com_sales_contract", "buyer_name=$hidden_buyer_id  and status_active=1 and is_deleted=0","contact_system_id");
		$export_lc=return_field_value("export_lc_system_id 	 as export_lc_system_id", "com_export_lc", "buyer_name=$hidden_buyer_id  and status_active=1 and is_deleted=0","export_lc_system_id");

		if($marchandising_job!="" || $sub_contract_job!="" || $sample_develop!="" || $price_quotation!="" || $quotation_inquiry!="" || $buyer_quotation_final!="" || $sales_contract!="" || $export_lc!="")
		{
			echo "50**Some Entries Found For This Buyer, Deleting Not Allowed, \n Merchandising Job: ".$marchandising_job."\n Sub Contract Job: ".$sub_contract_job."\n Sample Develop ID: ".$sample_develop."\n Price Quotation ID: ".$price_quotation."\n Quotation Inquiry ID: ".$quotation_inquiry."\n Buyer Quotation: ".$buyer_quotation_final."\n Sales Contract: ".$sales_contract."\n Export LC: ".$export_lc;
		}

	/*	if (is_duplicate_field( "buyer", "lib_supplier", "buyer=$update_id and is_deleted=0" ) == 1)
		{
			echo "13**0"; die;
		}*/
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}
			$field_array="updated_by*update_date*status_active*is_deleted";
			$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
			$rID=sql_delete("lib_buyer",$field_array,$data_array,"id","".$update_id."",1);

			if($db_type==0)
			{
				if($rID )
				{
					mysql_query("COMMIT");
					echo "2**".$rID;
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".$rID;
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($rID )
					{
						oci_commit($con);
						echo "2**".$rID;
					}
				else
					{
						oci_rollback($con);
						echo "10**".$rID;
					}
			}
			disconnect($con);
			die;
		}
	}
}

if ($action=="show_buyer_list_view")
{
	$arr=array (3=>$party_type,8=>$currency,10=>$row_status);
	echo create_list_view ( "list_view", "ID,Contact Name,Short Name,Sewing Effi Mkt. %,Contact Person,Designation,Credit Limit(Days),Credit Limit (Amount),Currency, Del. Buffer Days, Status", "60,150,100,150,100,120,80,80,80,70","1110","220",0, "select buyer_name, short_name, sewing_effi_mkt_percent, contact_person, designation, credit_limit_days, credit_limit_amount, credit_limit_amount_currency,delivery_buffer_days, status_active, id from lib_buyer where is_deleted=0", "get_php_form_data", "id", "'load_php_data_to_form'", 1, "0,0,0,0,0,0,0,0,credit_limit_amount_currency,0,status_active", $arr , "id,buyer_name,short_name,sewing_effi_mkt_percent,contact_person,designation,credit_limit_days,credit_limit_amount,credit_limit_amount_currency,delivery_buffer_days,status_active", "requires/buyer_info_controller",'setFilterGrid("list_view",-1);','0,0,0,1,0,0,1,1,0,0,0,0');
	exit();
}

if ($action=="load_php_data_to_form")
{
	$nameArray=sql_select( "select id ,buyer_name, short_name, contact_person, exporters_reference, party_type, designation, tag_company, country_id, web_site, buyer_email, address_1, address_2, address_3, address_4, remark, supllier, credit_limit_days, credit_limit_amount, credit_limit_amount_currency, discount_method, securitye_deducted, vat_to_be_deducted, ait_to_be_deducted, sewing_effi_mkt_percent, sewing_effi_plaing_per, marketing_team_id, control_delivery,cut_off_used,delivery_buffer_days,deffd_lc_cost_percent,min_quoted_profit_parcent,min_budgeted_profit_parcent,commercial_invoice, status_active,is_posted_account,bank_id,is_partial_rlz, lc_sc_tol_level, sequence_no, contact_no from lib_buyer  where id='$data'");
	foreach ($nameArray as $inf)
	{
	
		echo "document.getElementById('txt_buyer_name').value = '".($inf[csf("buyer_name")])."';\n";
		echo "document.getElementById('hidden_buyer_id').value = '".($inf[csf("id")])."';\n";
		echo "document.getElementById('cbo_tol_level').value = '".($inf[csf("lc_sc_tol_level")])."';\n";
		echo "document.getElementById('txt_sequence').value = '".($inf[csf("sequence_no")])."';\n";
		
		//echo "document.getElementById('cbo_buyer_company').value  = '".($inf[csf("tag_company")])."';\n";  
		//echo "document.getElementById('cbo_party_type').value  = '".($inf[csf("party_type")])."';\n"; 

		echo "document.getElementById('txt_short_name').value = '".($inf[csf("short_name")])."';\n";
		echo "document.getElementById('txt_contact_person').value  = '".($inf[csf("contact_person")])."';\n";
		echo "document.getElementById('txt_exporter_ref').value  = '".($inf[csf("exporters_reference")])."';\n";
		//echo "document.getElementById('cbo_party_type').value  = '".($inf[csf("party_type")])."';\n";
		echo "document.getElementById('txt_desination').value = '".($inf[csf("designation")])."';\n";
		//echo "document.getElementById('cbo_buyer_company').value  = '".($inf[csf("tag_company")])."';\n";
		echo "document.getElementById('cbo_country').value = '".($inf[csf("country_id")])."';\n";
		echo "document.getElementById('txt_web_site').value  = '".($inf[csf("web_site")])."';\n";
		echo "document.getElementById('txt_buyer_email').value  = '".($inf[csf("buyer_email")])."';\n";
		/*echo "document.getElementById('txt_address_1st').value  = '".($inf[csf("address_1")])."';\n";
		echo "document.getElementById('txt_address_2nd').value  = '".($inf[csf("address_2")])."';\n";
		echo "document.getElementById('txt_address_3rd').value  = '".($inf[csf("address_3")])."';\n";
		echo "document.getElementById('txt_address_4th').value  = '".($inf[csf("address_4")])."';\n";*/
		echo "document.getElementById('txt_address_1st').value  = '".preg_replace("/[\r\n]*/","",$inf[csf("address_1")])."';\n";  
		echo "document.getElementById('txt_address_2nd').value  = '".preg_replace("/[\r\n]*/","",$inf[csf("address_2")])."';\n";  
		echo "document.getElementById('txt_address_3rd').value  = '".preg_replace("/[\r\n]*/","",$inf[csf("address_3")])."';\n";  
		echo "document.getElementById('txt_address_4th').value  = '".preg_replace("/[\r\n]*/","",$inf[csf("address_4")])."';\n";
		echo "document.getElementById('txt_remark').value  = '".($inf[csf("remark")])."';\n";
		echo "document.getElementById('cbo_buyer_supplier').value  = '".($inf[csf("supllier")])."';\n";
		echo "document.getElementById('cbo_marketing_team').value  = '".($inf[csf("marketing_team_id")])."';\n";
		echo "document.getElementById('txt_credit_limit_days').value  = '".($inf[csf("credit_limit_days")])."';\n";
		echo "document.getElementById('txt_credit_limit_amount').value  = '".($inf[csf("credit_limit_amount")])."';\n";
		echo "document.getElementById('cbo_credit_limit_amount_curr').value  = '".($inf[csf("credit_limit_amount_currency")])."';\n";
		echo "document.getElementById('cbo_discount_method').value  = '".($inf[csf("discount_method")])."';\n";
		echo "document.getElementById('cbo_security_deducted').value  = '".($inf[csf("securitye_deducted")])."';\n";
		echo "document.getElementById('cbo_vat_to_be_deducted').value  = '".($inf[csf("vat_to_be_deducted")])."';\n";
		echo "document.getElementById('cbo_ait_to_be_deducted').value  = '".($inf[csf("ait_to_be_deducted")])."';\n";
		echo "document.getElementById('txt_sewing_effi_mkt').value  = '".($inf[csf("sewing_effi_mkt_percent")])."';\n";
		echo "document.getElementById('txt_sewing_effi_planing').value  = '".($inf[csf("sewing_effi_plaing_per")])."';\n";
		echo "document.getElementById('cbo_control_delivery').value  = '".($inf[csf("control_delivery")])."';\n";
		echo "document.getElementById('cbo_status').value  = '".($inf[csf("status_active")])."';\n";
		echo "document.getElementById('cbo_cut_Off_used').value  = '".($inf[csf("cut_off_used")])."';\n";
	    echo "document.getElementById('txt_del_buffer_days').value  = '".($inf[csf("delivery_buffer_days")])."';\n";
		echo "document.getElementById('txt_deffd_lc_cost_percent').value  = '".($inf[csf("deffd_lc_cost_percent")])."';\n";
		//echo "document.getElementById('cbo_bank_name').value  = '".($inf[csf("bank_id")])."';\n"; update_id
		echo "document.getElementById('cbo_partial_rlz').value  = '".($inf[csf("is_partial_rlz")])."';\n";

		echo "document.getElementById('is_posted_accounts').value  = '".($inf[csf("is_posted_account")])."';\n"; 
		if($inf[csf("is_posted_account")]==1) $msg="Already Posted in Accounts. Buyer Name Tag Company and Status Update Not Allowed.";
		else $msg="";
		echo "$('#posted_account_td').text('".$msg."');\n";

		echo "document.getElementById('txt_min_quoted_profit_parcent').value  = '".($inf[csf("min_quoted_profit_parcent")])."';\n";
		echo "document.getElementById('txt_min_budgeted_profit_parcent').value  = '".($inf[csf("min_budgeted_profit_parcent")])."';\n";
		echo "document.getElementById('cbo_commercial_invoice').value  = '".($inf[csf("commercial_invoice")])."';\n";
		
		echo "set_multiselect('cbo_party_type*cbo_buyer_company*cbo_bank_name','0*0*0','1','".$inf[csf("party_type")]."*".$inf[csf("tag_company")]."*".$inf[csf("bank_id")]."','0*0*0');\n";

		echo "document.getElementById('update_id').value  = '".($inf[csf("id")])."';\n";
		echo "document.getElementById('txt_contact_no').value  = '".($inf[csf("contact_no")])."';\n";
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_buyer_info',1);\n";
		
	//	if($inf[csf("bank_id")]==0) $inf[csf("bank_id")]="";
		 //echo "set_multiselect('cbo_party_type','0','0','','0','0');\n";
		 // echo "set_multiselect('cbo_buyer_company','0','0','','0','0');\n";
		 //  echo "set_multiselect('cbo_bank_name','0','0','','0','0');\n";

		//echo "set_multiselect('cbo_party_type*cbo_buyer_company*cbo_bank_name','0*0*0','1','".$inf[csf("party_type")]."*".$inf[csf("tag_company")]."*".$inf[csf("bank_id")]."','__set_supplier_status__../contact_details/requires/buyer_info_controller*__set_supplier_status__../contact_details/requires/buyer_info_controller*__set_supplier_status__../contact_details/requires/buyer_info_controller');\n";
		
		

	}
	//exit();
}

if ($action=="load_information_data_to_form")
{
	$nameArray=sql_select( "select id ,pay_through,lc_sc,lc_sc_shpmnt,payment_term,tenor,tenor_shpmnt,trnsfr_lc,trnsfr_type,comm_avlbl,comm_prcnt_local,comm_prcnt_forgn,incoterm,incoterm_plc,tolerance,port_discrg,partial_shpmnt,transhipment,inspect_crt,insurance,insurance_other,ship_mode,inspected_by,paid_by,bill_neg,penalty_dsc,rmbrs_cls,bill_landing,com_cost_prcnt_imp_fabric,short_realization_prcnt from lib_buyer  where id='$data'");
	//echo "<pre>";
	//print_r($nameArray);
	foreach ($nameArray as $inf)
	{
		if($inf[csf("pay_through")]){
			echo "document.getElementById('cbo_pay_through').value = '".($inf[csf("pay_through")])."';\n";
			echo "document.getElementById('txt_lc_sc').value = '".($inf[csf("lc_sc")])."';\n";
			echo "document.getElementById('cbo_lc_sc_shpmnt').value = '".($inf[csf("lc_sc_shpmnt")])."';\n";
			echo "document.getElementById('cbo_payment_term').value = '".($inf[csf("payment_term")])."';\n";
			echo "document.getElementById('txt_tenor').value = '".($inf[csf("tenor")])."';\n";
			echo "document.getElementById('txt_tenor_shpmnt').value = '".($inf[csf("tenor_shpmnt")])."';\n";
			echo "document.getElementById('cbo_trnsfr_lc').value = '".($inf[csf("trnsfr_lc")])."';\n";
			echo "document.getElementById('txt_trnsfr_type').value = '".($inf[csf("trnsfr_type")])."';\n";
			echo "document.getElementById('cbo_comm_avlbl').value = '".($inf[csf("comm_avlbl")])."';\n";
			echo "document.getElementById('txt_comm_prcnt_local').value = '".($inf[csf("comm_prcnt_local")])."';\n";
			echo "document.getElementById('txt_comm_prcnt_forgn').value = '".($inf[csf("comm_prcnt_forgn")])."';\n";
			echo "document.getElementById('cbo_incoterm').value = '".($inf[csf("incoterm")])."';\n";
			echo "document.getElementById('txt_incoterm_plc').value = '".($inf[csf("incoterm_plc")])."';\n";
			echo "document.getElementById('txt_tolerance').value = '".($inf[csf("tolerance")])."';\n";
			echo "document.getElementById('txt_port_discrg').value = '".($inf[csf("port_discrg")])."';\n";
			echo "document.getElementById('cbo_partial_shpmnt').value = '".($inf[csf("partial_shpmnt")])."';\n";
			echo "document.getElementById('cbo_transhipment').value = '".($inf[csf("transhipment")])."';\n";
			echo "document.getElementById('cbo_inspect_crt').value = '".($inf[csf("inspect_crt")])."';\n";
			echo "document.getElementById('cbo_insurance').value = '".($inf[csf("insurance")])."';\n";
			echo "document.getElementById('txt_insurance_other').value = '".($inf[csf("insurance_other")])."';\n";
			echo "document.getElementById('cbo_ship_mode').value = '".($inf[csf("ship_mode")])."';\n";
			echo "document.getElementById('cbo_inspected_by').value = '".($inf[csf("inspected_by")])."';\n";
			echo "document.getElementById('cbo_paid_by').value = '".($inf[csf("paid_by")])."';\n";
			echo "document.getElementById('txt_bill_neg').value = '".($inf[csf("bill_neg")])."';\n";
			echo "document.getElementById('txt_penalty_dsc').value = '".($inf[csf("penalty_dsc")])."';\n";
			echo "document.getElementById('txt_rmbrs_cls').value = '".($inf[csf("rmbrs_cls")])."';\n";
			echo "document.getElementById('txt_bill_landing').value = '".($inf[csf("bill_landing")])."';\n";
			echo "document.getElementById('txt_com_cost_prcnt_imp_fabric').value = '".($inf[csf("com_cost_prcnt_imp_fabric")])."';\n";
			echo "document.getElementById('txt_short_realization_prcnt').value = '".($inf[csf("short_realization_prcnt")])."';\n";
			echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_info',1);\n";
		}
		else
		{
			echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_info',0);\n";
		}
	}
	exit();
}

if ($action=="set_supplier_status")
{
	if($data=="") echo "";
	else
	{
		$data=explode(",",$data);
		if (in_array("90",$data))
			echo "$('#cbo_buyer_supplier').removeAttr('disabled');\n";
		else
			echo "$('#cbo_buyer_supplier').attr('disabled','true');\n";
	}
	exit();
}

if($action=="sample_name_popup")
{
	echo load_html_head_contents("Party Type Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
    $_SESSION['page_permission']=$permission;
?>
	<script>
	var permission='<? echo $permission; ?>';
	$(document).ready(function(e) {
		setFilterGrid('tbl_list_search',-1);
	});
	var selected_id = new Array();
	var selected_name = new Array();
	var buyer_id='';
	var seq_array= new Array();
	function check_all_data()
	 {
		var tbl_row_count = document.getElementById( 'tbl_list_search' ).rows.length;

		tbl_row_count = tbl_row_count-1;
		for( var i = 1; i <= tbl_row_count; i++ ) {
			js_set_value( i );
		}
	}
	
	function toggle( x, origColor ) {
		var newColor = 'yellow';
		if ( x.style ) {
			x.style.backgroundColor = ( newColor == x.style.backgroundColor )? origColor : newColor;
		}
	}
	
	function set_all()
	{
		var old=document.getElementById('txt_party_row_id').value;
		if(old!="")
		{
			old=old.split(",");
			for(var k=0; k<old.length; k++)
			{
				js_set_value( old[k] )
			}
		}
	}
	
	var secq=1;
	function js_set_value( str )
	{
		//alert(secq)

		toggle( document.getElementById( 'search' + str ), '#FFFFCC' );

		if( jQuery.inArray( $('#txt_individual_id' + str).val(), selected_id ) == -1 ) {
			selected_id.push( $('#txt_individual_id' + str).val() );
			selected_name.push( $('#txt_individual' + str).val() );
			seq_array.push(secq)
			$('#txtseq' + str).val(secq);
			secq++
		}
		else {
			for( var i = 0; i < selected_id.length; i++ ) {
				if( selected_id[i] == $('#txt_individual_id' + str).val() ) break;
			}
			selected_id.splice( i, 1 );
			selected_name.splice( i, 1 );
			seq_array.splice( i, 1 );
			$('#txtseq' + str).val('');
			--secq
		}
		var id = ''; var name = ''; var txtseq = '';
		for( var i = 0; i < selected_id.length; i++ ) {
			id += selected_id[i] + ',';
			name += selected_name[i] + ',';
			txtseq += seq_array[i] + ',';
		}
		id = id.substr( 0, id.length - 1 );
		name = name.substr( 0, name.length - 1 );
		txtseq = txtseq.substr( 0, txtseq.length - 1 );
		$('#hidden_sample_id').val(id);
		$('#hidden_sample_name').val(name);
		$('#hidden_txtseq').val(txtseq);
	}

	function fnc_sequ_info(operation)
	{
		var row_num = $('#tbl_list_search tr').length-1;
		var total_row=0;
		var data_arr=Array();
		for (var i=1; i<=row_num; i++)
		{

			data_arr.push('txt_sample_id_'+i+'*txtseq_'+i+'*cbo_business_nature_'+i);
			
			//data_all=data_all+get_submitted_data_string('hidden_buyer_id*txt_sample_id_'+i+'*txtseq_'+i+'*cbo_business_nature_'+i,"../../../",i);
			
		}
		
		data_arr_str=data_arr.join('*');
		
		data_all=get_submitted_data_string(data_arr_str,"../../../",1);
		
		var hidden_buyer_id=$('#hidden_buyer_id').val();

		
		
		var data="action=save_update_delete_sample_tag&operation="+operation+'&hidden_buyer_id='+hidden_buyer_id+'&total_row='+row_num+data_all;
		//freeze_window(operation);
		http.open("POST","buyer_info_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_sequ_info_reponse;
	}
	
	function fnc_sequ_info_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');
			if (reponse[0].length>2) reponse[0]=10;
			if(reponse[0]==0 || reponse[0]==1)
			{
				parent.emailwindow.hide();
			}
		}
	}
	function synchronize_sample()
	{
		data_all=get_submitted_data_string('hidden_buyer_id*hidden_company_id',"../../../");
		var data="action=synchronize_sample_tag&"+data_all;
		freeze_window();
		http.open("POST","buyer_info_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = synchronize_sample_reponse;
	}
	function synchronize_sample_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');
			if(reponse[0]==0)
			{
				release_freezing();
				alert("Successfully Synchronize Sample Tag");
			}
			else if(reponse[0]== 99)
			{
				release_freezing();
				alert("No Data Found To Synchronize");
			}
			else
			{
				release_freezing();
				alert("Error! Data Synchronized Failed");
			}
			
		}
	}
    </script>
</head>
<body>
<div align="center">
<? echo load_freeze_divs ("../../../",$permission);  ?>
	<fieldset style="width:470px;margin-left:10px">
    <input type="hidden" name="hidden_buyer_id" id="hidden_buyer_id" class="text_boxes" value="<? echo $hidden_buyer_id; ?>">
    <input type="hidden" name="hidden_company_id" id="hidden_company_id" class="text_boxes" value="<? echo $company; ?>">
        <form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off">
            <table cellspacing="0" cellpadding="0" border="1" rules="all" width="450" class="rpt_table" >
                <thead>
                    <th width="30">SL</th>
                    <th width="150">Sample Name</th>
                    <th width="100">Sample Type</th>
                    <th width="90">Business Nature</th>
                    <th>Sequence</th>
                </thead>
            </table>
            <div style="width:450px; overflow-y:scroll; max-height:280px;" id="buyer_list_view" align="center">
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="432" class="rpt_table" id="tbl_list_search" >
                <?
				$is_in_approval=array();
				$sql=sql_select("select a.buyer_name ,a.job_no,b.sample_type_id from wo_po_details_master a, wo_po_sample_approval_info b,lib_buyer_tag_sample c  where a.job_no=b.job_no_mst and a.buyer_name=c.buyer_id and c.tag_sample=b.sample_type_id and a.buyer_name=$hidden_buyer_id  group by a.buyer_name ,a.job_no,b.sample_type_id");
				foreach($sql as $sql_row)
				{
					$is_in_approval[$sql_row[csf('sample_type_id')]]=1;
				}


				$up_data=array();$business_nature_data=array();
				//echo "select buyer_id,tag_sample,sequ from  lib_buyer_tag_sample where buyer_id=$hidden_buyer_id and sequ>0 ";
				$sql=sql_select("select buyer_id,tag_sample,sequ,business_nature from  lib_buyer_tag_sample where buyer_id=$hidden_buyer_id and sequ>0 ");
				foreach($sql as $row)
				{
					$up_data[$row[csf('tag_sample')]]=$row[csf('sequ')];
					$business_nature_data[$row[csf('tag_sample')]]=$row[csf('business_nature')];
				}

				$i=1; $party_row_id='';
				$hidden_party_id=explode(",",$txt_tag_buyer_id);
				$sql_buyer=sql_select("select  id,sample_name,sample_type,business_nature from lib_sample where is_deleted=0 and status_active=1 order by sample_name");
				foreach($sql_buyer as $row_buyer)
				{
					$disabled="";
					if($is_in_approval[$row_buyer[csf('id')]]==1 && $up_data[$row_buyer[csf('id')]]>0) $disabled="disabled"; else $disabled="";
					if ($i%2==0) $bgcolor="#E9F3FF"; else $bgcolor="#FFFFFF";
					if(in_array($row_buyer[csf('id')],$hidden_party_id))
					{
						if($party_row_id=="") $party_row_id=$i; else $party_row_id.=",".$i;
					}
					$ttl=$hidden_buyer_id.'='.$row_buyer[csf('id')].'='.$business_nature_data[$row_buyer[csf('id')]].'='.$row_buyer[csf('business_nature')]; 
					?>
					<tr bgcolor="<? echo $bgcolor; ?>" style="text-decoration:none; cursor:pointer" id="search<? echo $i;?>">
						<td width="30" align="center"><?php echo $i; ?></td>
						<td width="150" title="<? echo $ttl;?>"><p><? echo $row_buyer[csf('sample_name')]; ?></p><input type="hidden" name="txt_sample_id" id="txt_sample_id_<?php echo $i ?>" value="<? echo $row_buyer[csf('id')]; ?>"/>	</td>
						<td width="100"><p><? echo $sample_type[$row_buyer[csf('sample_type')]]; ?></p></td>
						<td width="90"><? echo create_drop_down( "cbo_business_nature_".$i, 90, $business_nature_arr, "", 0, "Select", $row_buyer[csf('business_nature')], ''); ?></td>
                        <td><input type="text" id="txtseq_<?php echo $i ?>" name="txtseq_<?php echo $i ?>" class="text_boxes" value="<? echo $up_data[$row_buyer[csf('id')]]  ?>" <? echo $disabled; ?> style="width:45px;" /></td>
					</tr>
					<?
					$i++;
				}
                ?>
                <input type="hidden" name="txt_party_row_id" id="txt_party_row_id" value="<?php echo $party_row_id; ?>"/>
                </table>
            </div>
             <table width="450" cellspacing="0" cellpadding="0" style="border:none" align="center">
                <tr>
                    <td align="center" height="30" valign="bottom">
                    <?
						echo load_submit_buttons( $permission, "fnc_sequ_info", 0,0 ,"reset_form('searchprocessfrm_1','','','','')",1) ;
					?>
					<input type="button" value="Synchronize" name="synchronize" onClick="synchronize_sample()" style="width:80px" id="synchronize" class="formbutton" title="Synchronize Sample Tag">
                    </td>
                </tr>
            </table>
        </form>
    </fieldset>
</div>
</body>
<script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
</html>
<?
exit();
}

if($action == "synchronize_sample_tag")
{
	$con = connect();
	if($db_type==0)
	{
		mysql_query("BEGIN");
	}
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	//echo $hidden_buyer_id.'--'.$hidden_company_id;
	$flag=0;
	$company = explode(',', str_replace("'",'',$hidden_company_id));
	$company_id = "'".implode("','", $company)."'";
	$id_sm=return_next_id( "id", "wo_po_sample_approval_info", 1 ) ;
	$sample_tag=sql_select("select tag_sample, buyer_id, sequ from lib_buyer_tag_sample where sequ!=0 and buyer_id=$hidden_buyer_id order by sequ");
	$field_array_sm="id,job_no_mst,po_break_down_id,color_number_id,sample_type_id,inserted_by,insert_date,status_active,is_deleted";
	$data_sample = "SELECT a.job_no, b.id as po_id, c.color_number_id, min(c.id) as color_size_table_id from wo_po_details_master a join wo_po_break_down b on a.job_no=b.job_no_mst join wo_po_color_size_breakdown c on b.job_no_mst=c.job_no_mst and b.id=c.po_break_down_id where a.is_deleted=0 and a.status_active=1 and b.is_deleted=0 and b.status_active=1 and c.is_deleted=0 and c.status_active=1 and b.status_active=1 and company_name in ($company_id) and a.buyer_name=$hidden_buyer_id and b.shiping_status in (0,1) group by a.job_no, b.id, c.color_number_id order by b.id";
	$data_array_sample = sql_select($data_sample); 
	$poId=array();
	foreach ($data_array_sample as $rowpo)
	{
		$poId[$rowpo[csf("po_id")]]=$rowpo[csf("po_id")];
	}

	/*$po_Id=array_filter(array_unique(explode(",",$poId)));
	$poIds=implode(",",$po_Id);*/

	$poidCount=count($poId);

	//echo $bidCount; die;
	$poidcond="";
	if($db_type==2 && $poidCount>700)
	{
		$poidcond=" and (";
		
		//$poidcondArr=array_chunk(explode(",",$poIds),699);
		$poidcondArr=array_chunk($poId,699);
		foreach($poidcondArr as $ids)
		{
			$ids=implode(",",$ids);
			$poidcond.=" po_break_down_id in($ids) or ";
		}
		$poidcond=chop($poidcond,'or ');
		$poidcond.=")";
	}
	else
	{
		$poIds=implode(",",$poId);
		$poidcond=" and po_break_down_id in ($poIds)";
	}

	$dup_data=sql_select("select id as id, job_no_mst, po_break_down_id, color_number_id, sample_type_id from wo_po_sample_approval_info where status_active=1 and is_deleted=0 $poidcond group by id, job_no_mst, po_break_down_id, color_number_id, sample_type_id");
	//echo "select count(id) as id, job_no_mst, po_break_down_id, color_number_id, sample_type_id from wo_po_sample_approval_info where status_active=1 and is_deleted=0 $poidcond group by job_no_mst, po_break_down_id, color_number_id, sample_type_id"; die;
	$dupdataArr=array();
	foreach($dup_data as $drow)
	{
		$dupdataArr[$drow[csf('job_no_mst')]][$drow[csf('po_break_down_id')]][$drow[csf('color_number_id')]][$drow[csf('sample_type_id')]]=$drow[csf('id')];
	}
	unset($dup_data);
	$data_array_sm='';
	$sam=1;
	foreach($sample_tag as $sample_tag_row)
	{
		foreach ( $data_array_sample as $row_sam1 )
		{
			$dup_data_count=0;
			$dup_data_count=$dupdataArr[$row_sam1[csf('job_no')]][$row_sam1[csf('po_id')]][$row_sam1[csf('color_size_table_id')]][$sample_tag_row[csf('tag_sample')]]*1;
			if($dup_data_count == 0)
			{
				$insert_arr[$id_sm]= "(".$id_sm.",'".$row_sam1[csf('job_no')]."',".$row_sam1[csf('po_id')].",".$row_sam1[csf('color_size_table_id')].",'".$sample_tag_row[csf('tag_sample')]."',".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				$id_sm=$id_sm+1;
			}
		}
	}
	if(count($insert_arr) == 0)
	{
		echo "99**".count($insert_arr);
		disconnect($con);
		die;
	}
	/*echo count($insert_arr);
	die;*/
	//$k=1;
	$data_summary=array_chunk($insert_arr,100);
	foreach ($data_summary as $data) {
		$sam =1;
		$data_array_sm ='';
		foreach ($data as $row) {
			if ($sam!=1) $data_array_sm .=",";
			$data_array_sm .=$row;
			$sam++;
		}
		if($data_array_sm !='')
		{
			$rID3=sql_insert("wo_po_sample_approval_info",$field_array_sm,$data_array_sm,1);
			if($rID3==1) $flag=1; else $flag=0;
			/*$insert_re[$k] = $flag;
			$k++;*/
		}
	}
	/*echo '<pre>';
	print_r($insert_re); die;*/
	if($db_type==0)
	{
		if($flag==1)
		{
			mysql_query("COMMIT");
			echo "0**".count($insert_arr);
		}
		else
		{
			mysql_query("ROLLBACK");
			echo "10**".count($insert_arr);
		}
	}
	else if($db_type==2 || $db_type==1 )
	{
		if($flag==1)
		{
			oci_commit($con);
			echo "0**".count($insert_arr);
		}
		else
		{
			oci_rollback($con);
			echo "10**".count($insert_arr);
		}
	}
	exit();

}

if($action == "buyer_profile_popup")
{
	echo load_html_head_contents("Party Type Info", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$_SESSION['page_permission']=$permission;

	?>
	<script>

	function fnc_info(operation)
	{
		var data="action=save_update_information&operation="+operation+get_submitted_data_string('hidden_buyer_id*cbo_pay_through*txt_lc_sc*cbo_lc_sc_shpmnt*cbo_payment_term*txt_tenor*txt_tenor_shpmnt*cbo_trnsfr_lc*txt_trnsfr_type*cbo_comm_avlbl*txt_comm_prcnt_local*txt_comm_prcnt_forgn*cbo_incoterm*txt_incoterm_plc*txt_tolerance*txt_port_discrg*cbo_partial_shpmnt*cbo_transhipment*cbo_inspect_crt*cbo_insurance*txt_insurance_other*cbo_ship_mode*cbo_inspected_by*cbo_paid_by*txt_bill_neg*txt_penalty_dsc*txt_rmbrs_cls*txt_bill_landing',"../../../");
		http.open("POST","buyer_info_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_info_reponse;
	}
	
	function fnc_info_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');
			if (reponse[0].length>2) reponse[0]=10;
			if(reponse[0]==0 || reponse[0]==1)
			{
				parent.emailwindow.hide();
			}
		}
	}

	function fnc_readonly()
	{
		ins = document.getElementById("cbo_insurance").value;

		if(ins == 0)
		{
			document.getElementById('txt_insurance_other').value = "";
			document.getElementById("txt_insurance_other").readOnly = true;
		}
		else
		{
			document.getElementById("txt_insurance_other").readOnly = false;
		}
	}

</script>

</head>
<body>
	<div align="center">
		<? echo load_freeze_divs ("../../../",$permission);  ?>
		<fieldset style="width:650px;">
			 <input type="hidden" name="hidden_buyer_id" id="hidden_buyer_id" class="text_boxes" value="<? echo $hidden_buyer_id; ?>">
			<form name="searchprocessfrm_1"  id="searchprocessfrm_1" autocomplete="off" method="post">
				<table cellspacing="0" cellpadding="0" border="1" width="650" id="tbl_list_search" class="rpt_table" >
					<thead><th colspan="5" >Terms & Condition</th></thead>
				</table>
                <table cellspacing="3" cellpadding="3" width="650" id="tbl_list_search" >
                    <tr>
                        <td  width="100" align="left">Payment through</td>
                        <td width="155" >
                        <?
							$beforeorafter = array(1=>'Before Shipment',2=>'After Shipment');
							$payment = array(1=>'Direct SC',2=>'Direct LC',3=>'SC to LC');
							$inspect_by = array(1=>"Buyer's Care",2=>"Other Party");
							$paid_by = array(1=>"Paid by Customer",2=>"Paid by Seller");

							echo create_drop_down( "cbo_pay_through", 150,  $payment, "", 0, "", "", "", "","");
						?>
                        </td>
                        <td width="95" >LC/SC to be opened</td>
                        <td width="100" ><input type="text" class="text_boxes" name="txt_lc_sc" id="txt_lc_sc"  style="width:100px" value="30" /></td>
                        <td width="100"><? echo create_drop_down( "cbo_lc_sc_shpmnt", 110,  $beforeorafter, "", 0, "", "", "", "",""); ?></td>
                    </tr>
                    <tr>
                        <td>Payment Term</td>
                        <td><? echo create_drop_down( "cbo_payment_term", 150,  $pay_term, "", 0, "", "", "", "",""); ?></td>
                        <td>Tenor</td>
                        <td><input type="text" name="txt_tenor" id="txt_tenor" class="text_boxes" style="width:100px" value="50" />	</td>
                        <td><? echo create_drop_down( "txt_tenor_shpmnt", 115,  $beforeorafter, "", 0, "", "", "", "",""); ?></td>
                    </tr>
                    <tr>
                        <td>Transferable LC</td>
                        <td><? echo create_drop_down( "cbo_trnsfr_lc", 150,  $yes_no, "", 0, "", "", "", "",""); ?></td>
                        <td> Transfer Type</td>
                        <td colspan="2">  <input type="text" class="text_boxes" name="txt_trnsfr_type" id="txt_trnsfr_type"  style="width:230px" value="Foreign Transfer" /></td>
                    </tr>
                    <tr>
                        <td>Commission available</td>
                        <td ><? echo create_drop_down( "cbo_comm_avlbl", 150,  $yes_no, "", 0, "", "", "", "",""); ?></td>
                        <td>Commission %</td>
                        <td>Local <input type="text" name="txt_comm_prcnt_local" id="txt_comm_prcnt_local" class="text_boxes_numeric" style="width:60px" value="" />	</td>
                        <td>Foreign <input type="text" name="txt_comm_prcnt_forgn" id="txt_comm_prcnt_forgn" class="text_boxes_numeric" style="width:60px" value="" /></td>
                    </tr>
                    <tr>
                        <td>Incoterm</td>
                        <td><? echo create_drop_down( "cbo_incoterm", 150,  $incoterm, "", 0, "", "1", "", "",""); ?></td>
                        <td> Incoterm Place</td>
                        <td colspan="2"><input type="text" name="txt_incoterm_plc" id="txt_incoterm_plc" class="text_boxes" style="width:230px" /></td>
                    </tr>
                    <tr>
                        <td>Tolerance %</td>
                        <td><input type="text" name="txt_tolerance" id="txt_tolerance"  style="width:140px" value="5" class="text_boxes_numeric" /></td>
                        <td> Port of discharge</td>
                        <td colspan="2"><input type="text" name="txt_port_discrg" id="txt_port_discrg"  style="width:230px" class="text_boxes" />	</td>
                    </tr>
                    <tr>
                        <td>Partial Shipment </td>
                        <td><? echo create_drop_down( "cbo_partial_shpmnt", 150,  $yes_no, "", 0, "", "", "", "",""); ?></td>
                        <td>Transhipment</td>
                        <td colspan="2"> <? echo create_drop_down( "cbo_transhipment", 240,  $yes_no, "", 0, "", "", "", "",""); ?>	</td>
                    </tr>
                    <tr>
                        <td>Inspection certificate</td>
                        <td><? echo create_drop_down( "cbo_inspect_crt", 150,  $commission_particulars, "", 0, "", "", "", "",""); ?></td>
                        <td>Insurance</td>
                        <td id= "cbo_ins"  onchange="fnc_readonly()"><? echo create_drop_down( "cbo_insurance", 115,  $inspect_by, "", 0, "", "", "", "",""); ?></td>
                        <td>  <input id="txt_insurance_other" type="text" name="txt_insurance_other" style="width:100px" class="text_boxes"   />	</td>
                   </tr>
                   <tr>
                        <td>Shipping Mode</td>
                        <td><? echo create_drop_down( "cbo_ship_mode", 150,  $shipment_mode, "", 0, "", "", "", "",""); ?></td>
                        <td>Inspection By</td>
                        <td id= "cbo_ins"> <? echo create_drop_down( "cbo_inspected_by", 115,  $inspect_by, "", 0, "", "", "", "",""); ?></td>
                        <td><? echo create_drop_down( "cbo_paid_by", 115,  $paid_by, "", 0, "", "", "", "",""); ?></td>
                    </tr>
				</table>
				<br>
                <table cellspacing="0" cellpadding="0" border="1" rules="all" width="650" class="rpt_table" id="tbl_list_search" >
                    <thead>
                    	<th colspan="5" >Clauses</th>
                    </thead>
                    <tr>
                        <td width="130" align="left">Bill Negotiation</td>
                        <td colspan="4" width="500"><input style="width:500px" type="text" class="text_boxes" name="txt_bill_neg" id="txt_bill_neg" value="Any bank in Bangladesh by Negotiation." /></td>
                    </tr>
                    <tr>
                        <td width="130" align="left">Penalty/ Discount Cls</td>
                        <td colspan="4" width="500"> <input style="width:500px" type="text"  class="text_boxes" name="txt_penalty_dsc" id="txt_penalty_dsc" value="" /></td>
                    </tr>
                    <tr>
                        <td width="130" align="left">Reimbursement Cls</td>
                        <td colspan="4" width="500"><input style="width:500px" type="text" class="text_boxes" name="txt_rmbrs_cls" id="txt_rmbrs_cls" value="Applicants bank will release payment as per negotiating banks instruction " /></td>
                    </tr>
                    <tr>
                        <td width="130" align="left">Bill of Lading </td>
                        <td colspan="4" width="500"><input style="width:500px"  type="text" class="text_boxes" name="txt_bill_landing" id="txt_bill_landing" value="Made out to the order of the negotiating bank and endorsed to the opening bank." /></td>
                    </tr>
                </table>
                <div style="width:50%; float:center" align="center">
                    <? echo load_submit_buttons($permission, "fnc_info", 0,0 ,"",1); ?>
                </div>
			</form>
		</fieldset>
	</div>
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>
        get_php_form_data('<? echo $hidden_buyer_id; ?>','load_information_data_to_form','buyer_info_controller')
    </script>
    </html>
	<?
    exit();
}

if($action=="save_update_information")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0 || $operation==1)  // Update information Here
	{
		if (is_duplicate_field( "buyer_name", "lib_buyer", "id=$update_id and is_deleted=0" ) == 1)
		{
			echo "11**0"; die;
		}
		else
		{
			$con = connect();
			if($db_type==0)
			{
				mysql_query("BEGIN");
			}

			$field_array="pay_through*lc_sc*lc_sc_shpmnt*payment_term*tenor*tenor_shpmnt*trnsfr_lc*trnsfr_type*comm_avlbl*comm_prcnt_local*comm_prcnt_forgn*incoterm*incoterm_plc*tolerance*port_discrg*partial_shpmnt*transhipment*inspect_crt*insurance*insurance_other*ship_mode*inspected_by*paid_by*bill_neg*penalty_dsc*rmbrs_cls*bill_landing*inserted_by*insert_date*is_deleted";

			$data_array="".$cbo_pay_through."*".$txt_lc_sc."*".$cbo_lc_sc_shpmnt."*".$cbo_payment_term."*".$txt_tenor."*".$txt_tenor_shpmnt."*".$cbo_trnsfr_lc."*".$txt_trnsfr_type."*".$cbo_comm_avlbl."*".$txt_comm_prcnt_local."*".$txt_comm_prcnt_forgn."*".$cbo_incoterm."*".$txt_incoterm_plc."*".$txt_tolerance."*".$txt_port_discrg."*".$cbo_partial_shpmnt."*".$cbo_transhipment."*".$cbo_inspect_crt."*".$cbo_insurance."*".$txt_insurance_other."*".$cbo_ship_mode."*".$cbo_inspected_by."*".$cbo_paid_by."*".$txt_bill_neg."*".$txt_penalty_dsc."*".$txt_rmbrs_cls."*".$txt_bill_landing."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'";
			
			//$rID=sql_insert("lib_buyer",$field_array,$data_array,0);
			$rID=sql_update("lib_buyer",$field_array,$data_array,"id","".$hidden_buyer_id."",0);

			//----------------------------------------------------------------------------------
			if($db_type==0)
			{
				if($rID  )
				{
					mysql_query("COMMIT");
					echo "0**".$rID;
				}
				else
				{
					mysql_query("ROLLBACK");
					echo "10**".$rID;
				}
			}
			else if($db_type==2 || $db_type==1 )
			{
				if($rID )
				{
					oci_commit($con);
					echo "0**".$rID;
				}
				else
				{
					oci_rollback($con);
					echo "10**".$rID;
				}
			}
			disconnect($con);
			die;
		}
	}
}

if($action=="save_update_delete_sample_tag")
{
	// /;parent.emailwindow.hide();
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0)
		{
			mysql_query("BEGIN");
		}

		//if  ( check_table_status( $_SESSION['menu_id'], 1 )==0 ) { echo "15**0"; die;}
		 $id=return_next_id( "id", " lib_buyer_tag_sample", 1 ) ;
		 $field_array="id,buyer_id,tag_sample,business_nature,sequ";
		 for ($i=1;$i<=$total_row;$i++)
		 {
			 $txt_sample_id="txt_sample_id_".$i;
			 $business_nature="cbo_business_nature_".$i;
			 $txtseq="txtseq_".$i;
			if ($i!=1) $data_array .=",";
			$data_array .="(".$id.",".$hidden_buyer_id.",".$$txt_sample_id.",".$$business_nature.",".$$txtseq.")";
			$id=$id+1;
		 }
		 //echo  $data_array;
		$rID_de3=execute_query( "delete from lib_buyer_tag_sample where  buyer_id =".$hidden_buyer_id."",0);

		 $rID=sql_insert("lib_buyer_tag_sample",$field_array,$data_array,1);

		 //echo "10**".$rID_de3.'=='.$rID; die;
		// check_table_status( $_SESSION['menu_id'],0);
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "0**".$hidden_buyer_id;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$hidden_buyer_id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0**".$hidden_buyer_id;
			}
			else{
				oci_rollback($con);
				echo "10**".$hidden_buyer_id;
			}
		}
		disconnect($con);
		die;
	}
}

if($action == "comm_importFabric")
{
	echo load_html_head_contents("Com. Cost for import fabric % and Short Realization %", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$_SESSION['page_permission']=$permission;
	$buyer_name=return_field_value("buyer_name", "lib_buyer", "id=$hidden_buyer_id");
	?>
	<script>
	var permission='<? echo $permission; ?>';
	function fnc_comm_info(operation)
	{
		var data="action=save_update_comm_importFabric&operation="+operation+get_submitted_data_string('hidden_buyer_id*txt_com_cost_importFabric*txt_short_realization*txt_effective_date*update_id',"../../../");
		http.open("POST","buyer_info_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_comm_info_reponse;
	}
	
	function fnc_comm_info_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');
			//if (reponse[0].length>2) reponse[0]=10;
			
			if(reponse[0]==0 || reponse[0]==1)
			{
				$('#update_id').val(reponse[1]);
				show_list_view( $('#hidden_buyer_id').val(),'show_comm_list_view','date_wise_list','buyer_info_controller','setFilterGrid("list_view",-1)');
				reset_form('commcostinfo_1','','');
				set_button_status(0, permission, 'fnc_comm_info');
				//parent.emailwindow.hide();
			}
		}
	}
</script>
</head>
<body>
	<div align="center">
		<? echo load_freeze_divs ("../../../",$permission);  ?>
		<fieldset style="width:450px;">
			<input type="hidden" name="hidden_buyer_id" id="hidden_buyer_id" class="text_boxes" value="<? echo $hidden_buyer_id; ?>">
			<form name="commcostinfo_1" id="commcostinfo_1" autocomplete="off" method="post">
                <table cellspacing="1" cellpadding="1" width="400" id="tbl_list_search" border="1" class="rpt_table" align="center" rules="all">
                	<thead>
                        <tr>
                            <th colspan="3"><? echo $buyer_name; ?></th>
                        </tr>
                        <tr>
                        	<th width="130">Com. Cost for import fabric %</th>
                            <th width="130">Short Realization %</th>
                            <th>Effective Date</th>
                        </tr>
                    </thead>
                    <tr>
                        <td><input type="text" class="text_boxes_numeric" name="txt_com_cost_importFabric" id="txt_com_cost_importFabric" style="width:120px" /></td>
                        <td><input type="text" class="text_boxes_numeric" name="txt_short_realization" id="txt_short_realization" style="width:120px" /></td>
                        <td><input type="text" class="datepicker" name="txt_effective_date" id="txt_effective_date" style="width:120px" /></td>
                    </tr>
                    <tr>
                        <td colspan="3" align="center" valign="middle" class="button_container">
                            <input type="hidden" name="update_id" id="update_id" /> 
                            <? echo load_submit_buttons($permission, "fnc_comm_info", 0,0 ,"reset_form('commcostinfo_1','','')",1); ?> 
                    	</td>	
                    </tr>
                </table>
			</form>
		</fieldset>
        <div id="date_wise_list"></div>
	</div>
    
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>
        show_list_view( $('#hidden_buyer_id').val(),'show_comm_list_view','date_wise_list','buyer_info_controller','setFilterGrid("list_view",-1)');
    </script>
    </html>
	<?
    exit();
}

if($action=="save_update_comm_importFabric")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));

	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0) mysql_query("BEGIN");
		//echo "10**select id from lib_comm_import_fabric where mst_id=$hidden_buyer_id and effective_date=$txt_effective_date and is_deleted=0";die;
		if (is_duplicate_field( "id", "lib_comm_import_fabric", "mst_id=$hidden_buyer_id and effective_date=$txt_effective_date and is_deleted=0" ) == 1)
		{
			echo "11**0";
			disconnect($con);
			die;
		}

		$id=return_next_id( "id", " lib_comm_import_fabric", 1 ) ;
		$field_array="id, mst_id, com_cost_imp_fabric, short_realization_per, effective_date, inserted_by, insert_date, status_active, is_deleted";
		 
		$data_array ="(".$id.",".$hidden_buyer_id.",".$txt_com_cost_importFabric.",".$txt_short_realization.",".$txt_effective_date.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";

		$rID=sql_insert("lib_comm_import_fabric",$field_array,$data_array,1);
		
		
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "0**".$id;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0**".$id;
			}
			else{
				oci_rollback($con);
				echo "10**".$id;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)  // Update Here
	{
		$con = connect();
		if($db_type==0) mysql_query("BEGIN");
		if (is_duplicate_field( "id", "lib_comm_import_fabric", "id!=$update_id and mst_id=$hidden_buyer_id and effective_date=$txt_effective_date and is_deleted=0") == 1)
		{
			echo "11**0";
			disconnect($con);
			die;
		}
		$id=str_replace("'","",$update_id);
		$field_array="com_cost_imp_fabric*short_realization_per*effective_date*updated_by*update_date";
		 
		$data_array="".$txt_com_cost_importFabric."*".$txt_short_realization."*".$txt_effective_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		$rID=sql_update("lib_comm_import_fabric",$field_array,$data_array,"id","".$update_id."",1);
		
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "1**".$id;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "1**".$id;
			}
			else{
				oci_rollback($con);
				echo "10**".$id;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)  // Delete Here
	{
		$con = connect();
		if($db_type==0) mysql_query("BEGIN");
		
		
		$id=str_replace("'","",$update_id);
		$field_array="updated_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*'0'*'1'";

		$rID=sql_delete("lib_comm_import_fabric",$field_array,$data_array,"id","".$update_id."",1);
		
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "2**".$id;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "2**".$id;
			}
			else{
				oci_rollback($con);
				echo "10**".$id;
			}
		}
		disconnect($con);
		die;
	}
}

if ($action=="show_comm_list_view")
{
	echo create_list_view ( "list_view", "Effective Date,Com. Cost for import fabric %,Short Realization %", "130,100,100","400","200",0, "select id, effective_date, com_cost_imp_fabric, short_realization_per from lib_comm_import_fabric where mst_id=$data", "get_php_form_data", "id", "'load_php_comm_data_to_form'", 1, "0,0,0", "" , "effective_date,com_cost_imp_fabric,short_realization_per", "buyer_info_controller",'setFilterGrid("list_view",-1);','3,0,0');
	exit();
}

if ($action=="load_php_comm_data_to_form")
{
	$nameArray=sql_select( "select id, effective_date, com_cost_imp_fabric, short_realization_per from lib_comm_import_fabric where id=$data");
	foreach ($nameArray as $inf)
	{
		echo "document.getElementById('update_id').value = '".($inf[csf("id")])."';\n";
		echo "document.getElementById('txt_com_cost_importFabric').value = '".($inf[csf("com_cost_imp_fabric")])."';\n";
		echo "document.getElementById('txt_short_realization').value = '".($inf[csf("short_realization_per")])."';\n";
		echo "document.getElementById('txt_effective_date').value  = '".change_date_format($inf[csf("effective_date")])."';\n";
		
		echo "set_button_status(1, '".$_SESSION['page_permission']."', 'fnc_comm_info',1);\n";
	}
	exit();
}


if($action=="buyer_currency_conversion_rate")
{
	echo load_html_head_contents("Buyer Wise Currency Conversion Rate", "../../../", 1, 1,'','','');
	extract($_REQUEST);
	$_SESSION['page_permission']=$permission;
	//$buyer_name=return_field_value("buyer_name", "lib_buyer", "id=$hidden_buyer_id");
	?>
	<script>
	var permission='<?=$permission; ?>';
	function fnc_buyer_currency_rate_info(operation)
	{
		var data="action=save_update_buyer_currency_rate&operation="+operation+get_submitted_data_string('hidden_buyer_id*cbo_currency*txt_conversion_rate*txt_marketing_rate*txt_date*update_id',"../../../");
		http.open("POST","buyer_info_controller.php",true);
		http.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		http.send(data);
		http.onreadystatechange = fnc_buyer_currency_rate_info_reponse;
	}
	
	function fnc_buyer_currency_rate_info_reponse()
	{
		if(http.readyState == 4)
		{
			var reponse=trim(http.responseText).split('**');
			//if (reponse[0].length>2) reponse[0]=10;
			
			if(reponse[0]==0 || reponse[0]==1)
			{
				$('#update_id').val(reponse[1]);
				show_list_view( $('#hidden_buyer_id').val(),'show_rate_list_view','buyer_currency_rate_list','buyer_info_controller','setFilterGrid("list_view",-1)');
				reset_form('buyercurrencyrateinfo_1','','');
				set_button_status(0, permission, 'fnc_buyer_currency_rate_info');
				//parent.emailwindow.hide();
			}
		}
	}
</script>
</head>
<body>
	<div align="center">
		<div style="display:none"><?=load_freeze_divs ("../../../",$permission); ?></div>
		<fieldset style="width:450px;">
			<input type="hidden" name="hidden_buyer_id" id="hidden_buyer_id" class="text_boxes" value="<?=$update_id; ?>">
			<form name="buyercurrencyrateinfo_1" id="buyercurrencyrateinfo_1" autocomplete="off" method="post">
                <table cellspacing="1" cellpadding="1" width="400" align="center">
                	<tr>
                        <td width="90" class="must_entry_caption">Currency</td>
                        <td colspan="2"><?=create_drop_down( "cbo_currency", 160, $currency,"", 1, "--- Select Currency ---", 0, "fn_load_list_view(this.value);", "", "", "" , "" , "1" ); ?></td>                
                        <td colspan="1">&nbsp;</td>                
                    </tr>
                    <tr>
                        <td width="90" class="must_entry_caption">Conversion Rate</td>
                        <td id="location_td_rn" width="90"><input type="text" id="txt_conversion_rate" name="txt_conversion_rate" class="text_boxes_numeric" style="width:70px;"/></td>                
                          <td class="must_entry_caption" width="90">Marketing Rate</td>
                        <td id="location_td_rn_mr"><input type="text" id="txt_marketing_rate" name="txt_marketing_rate" class="text_boxes_numeric" style="width:70px;"/></td>                
                    </tr>
                    <tr>
                        <td width="90" class="must_entry_caption">Effective Date</td>
                        <td colspan="2"><input type="text" id="txt_date" name="txt_date" class="datepicker" readonly style="width:153px;"/></td> 
                        <td colspan="1">&nbsp;</td> 
                    </tr>
                    
                    <tr>
                        <td align="center" colspan="4" class="button_container">
                            <input type="hidden" id="update_id">
                            <?=load_submit_buttons( $permission, "fnc_buyer_currency_rate_info", 0,0 ,"reset_form('buyercurrencyrateinfo_1','','')",1); ?>                   
                        </td>
                    </tr>
                </table>
			</form>
		</fieldset>
        <div id="buyer_currency_rate_list"></div>
	</div>
    
    </body>
    <script src="../../../includes/functions_bottom.js" type="text/javascript"></script>
    <script>
        show_list_view( $('#hidden_buyer_id').val(),'show_rate_list_view','buyer_currency_rate_list','buyer_info_controller','setFilterGrid("list_view",-1)');
    </script>
    </html>
	<?
    exit();
}

if($action=="save_update_buyer_currency_rate")
{
	$process = array( &$_POST );
	extract(check_magic_quote_gpc( $process ));
	
	$txtDate=str_replace("'","",$txt_date);
	$txtDate=change_date_format($txtDate, "d-M-y", "-",1);

	if ($operation==0)  // Insert Here
	{
		$con = connect();
		if($db_type==0) mysql_query("BEGIN");
		if(str_replace("'","",$update_id)=="")
		{
			$id=return_field_value("id","currency_conversion_rate_buyer","buyer_id=$hidden_buyer_id and currency=$cbo_currency and effective_date='$txtDate' and status_active=1 and is_deleted=0");			

			if($id==''){
				$id= return_next_id("id","currency_conversion_rate_buyer",1);
				$field_array_mst="id, buyer_id, currency, conversion_rate, marketing_rate, effective_date, inserted_by, insert_date, status_active, is_deleted";
				$data_array_mst="(".$id.",".$hidden_buyer_id.",".$cbo_currency.",".$txt_conversion_rate.",".$txt_marketing_rate.",".$txt_date.",".$_SESSION['logic_erp']['user_id'].",'".$pc_date_time."',1,0)";
				//echo "10**insert into currency_conversion_rate_buyer($field_array_mst)values".$data_array_mst;die;
				$rID=sql_insert("currency_conversion_rate_buyer",$field_array_mst,$data_array_mst);
			}
			else
			{
				$field_array_up="currency*conversion_rate*marketing_rate*effective_date*update_by*update_date";
				$data_array_up="".$cbo_currency."*".$txt_conversion_rate."*".$txt_marketing_rate."*".$txt_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
			
				$rID=sql_update("currency_conversion_rate_buyer",$field_array_up,$data_array_up,"id",$id,1);
			}
		}
		
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "0**".$id;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "0**".$id;
			}
			else{
				oci_rollback($con);
				echo "10**".$id;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==1)  // Update Here
	{
		$con = connect();
		if($db_type==0) mysql_query("BEGIN");
		$update_id=str_replace("'",'',$update_id);
		if($update_id!="")
		{
			$field_array_up="currency*conversion_rate*marketing_rate*effective_date*update_by*update_date";
			$data_array_up="".$cbo_currency."*".$txt_conversion_rate."*".$txt_marketing_rate."*".$txt_date."*".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'";
		}
		
		$rID=sql_update("currency_conversion_rate_buyer",$field_array_up,$data_array_up,"id",$update_id,1);
		
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "1**".$update_id;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$update_id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "1**".$update_id;
			}
			else{
				oci_rollback($con);
				echo "10**".$update_id;
			}
		}
		disconnect($con);
		die;
	}
	else if ($operation==2)  // Delete Here
	{
		$con = connect();
		if($db_type==0) mysql_query("BEGIN");
		
		$field_array="update_by*update_date*status_active*is_deleted";
		$data_array="".$_SESSION['logic_erp']['user_id']."*'".$pc_date_time."'*0*1";
		$rID=sql_update("currency_conversion_rate_buyer",$field_array,$data_array,"id","".$update_id."",1);
		
		if($db_type==0)
		{
			if($rID ){
				mysql_query("COMMIT");
				echo "2**".$id;
			}
			else{
				mysql_query("ROLLBACK");
				echo "10**".$id;
			}
		}
		else if($db_type==2 || $db_type==1 )
		{
			if($rID ){
				oci_commit($con);
				echo "2**".$id;
			}
			else{
				oci_rollback($con);
				echo "10**".$id;
			}
		}
		disconnect($con);
		die;
	}
}

if($action=="show_rate_list_view")
{
	$buyer_id=$data;
	$con="buyer_id='$buyer_id' and";
	//$comp=return_library_array( "select id, company_name from lib_company",'id','company_name');
	$buyer_name=return_field_value("buyer_name", "lib_buyer", "id=$buyer_id");
	?>
    <fieldset style="width:450px;">
        <div style="width:450px;">
            <table class="rpt_table" width="99%" border="1" cellspacing="0" cellpadding="0" rules="all">
                <thead>
                    <th width="30">SL No</th>
                    <th width="120">Buyer</th>
                    <th width="70">Currency</th>
                    <th width="70">Conversion Rate</th>
                    <th width="70">Marketing Rate</th>
                    <th>Effective Date</th>
                </thead>
            </table>
        </div>
        <div style="width:450px; overflow-y: scroll; max-height:200px;">
            <table class="rpt_table" width="99%" border="1" id="mail_setup" cellspacing="0" cellpadding="0" rules="all">
                <tbody>
					<? 
                    $result=sql_select("select id, buyer_id, currency, conversion_rate, marketing_rate, effective_date from currency_conversion_rate_buyer where $con  status_active=1 and is_deleted=0 order by id DESC");
                    $sl=1;
                    foreach($result as $list_rows)
					{
						$bgcolor=($sl%2==0)?"#E9F3FF":"#FFFFFF";
						?>    
						<tr bgcolor="<? echo $bgcolor; ?>" onClick="get_php_form_data(<?=$list_rows[csf('id')]; ?>,'conversion_rate_entry_from_data', 'buyer_info_controller')" style="cursor:pointer;">
                            <td width="30" align="center"><?=$sl; ?> </td>
                            <td width="120" style="word-break:break-all"><?=$buyer_name; ?> </td>
                            <td width="70"><?=$currency[$list_rows[csf('currency')]]; ?> </td>
                            <td width="70" align="right"><?=$list_rows[csf('conversion_rate')]; ?></td>
                            <td width="70" align="right"><?=$list_rows[csf('marketing_rate')]; ?></td>
                            <td align="center"><?=change_date_format($list_rows[csf('effective_date')]); ?> </td>
						</tr>
						<? $sl++; 
					} ?>    
                </tbody>  
            </table>
        </div>    
    </fieldset>	
    <?
    exit(); 
}


if($action=="conversion_rate_entry_from_data")
{
	$sql="select id, buyer_id, currency, conversion_rate, marketing_rate, effective_date from currency_conversion_rate_buyer where status_active=1 and is_deleted=0 and id=$data";
	$res = sql_select($sql);
	foreach($res as $row)
	{	
		echo "$('#update_id').val('".$row[csf("id")]."');\n";
		echo "$('#hidden_buyer_id').val('".$row[csf("buyer_id")]."');\n";
		echo "$('#cbo_currency').val('".$row[csf("currency")]."');\n";
		echo "$('#txt_conversion_rate').val('".$row[csf("conversion_rate")]."');\n";	
        echo "$('#txt_marketing_rate').val('".$row[csf("marketing_rate")]."');\n";
		echo "$('#txt_date').val('".change_date_format($row[csf("effective_date")])."');\n";
		echo "set_button_status(1, permission, 'fnc_buyer_currency_rate_info',1,1);";
	}
	exit();	
}

?>